<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Providers\UtilityServiceProvider as u;
use App\Models\LogStudents;

class ImportRefundExcessFromJson extends Command
{
    protected $signature = 'erp:import-refund-excess
                            {file=app/Console/Data/danh_sach_hoan_coc.json : Đường dẫn tới file JSON chứa dữ liệu hoàn tiền}
                            {--phone= : Truyền SĐT để xử lý duy nhất 1 bản ghi cụ thể}';

    protected $description = 'Import và xử lý hoàn tiền thừa (refundExcess) từ file JSON, hỗ trợ xử lý theo SĐT và tránh duplicate';

    public function handle(): int
    {
        ini_set('memory_limit', '-1');

        $file = $this->argument('file');
        $filterPhoneRaw = $this->option('phone');

        if (!file_exists($file)) {
            $this->error("Không tìm thấy file JSON tại: {$file}");
            return 1;
        }

        $jsonContent = file_get_contents($file);
        $data = json_decode($jsonContent, true);

        if (!is_array($data)) {
            $this->error("File JSON không đúng định dạng array!");
            return 1;
        }

        $totalRows = count($data);
        $this->info("Đã mở file JSON thành công. Tổng số bản ghi: {$totalRows}");

        $filterPhone = null;
        if ($filterPhoneRaw) {
            $filterPhone = $this->normalizePhone($filterPhoneRaw);
            $this->info("Đang lọc xử lý theo SĐT: {$filterPhoneRaw} (Chuẩn hóa: {$filterPhone})");
        }

        $successCount = 0;
        $skipCount = 0;
        $failCount = 0;

        foreach ($data as $idx => $row) {
            $rawPhone = trim((string)($row['phone'] ?? $row['raw_phone'] ?? ''));
            $studentName = trim((string)($row['name'] ?? ''));
            $amount = (float)($row['amount'] ?? 0);
            $refundDate = trim((string)($row['refund_date'] ?? date('Y-m-d')));

            $phone = $this->normalizePhone($rawPhone);

            // Nếu người dùng chọn lọc theo SĐT thì bỏ qua các dòng không khớp trước
            if ($filterPhone !== null) {
                if (!$phone || ($phone !== $filterPhone && ltrim($phone, '0') !== ltrim($filterPhone, '0'))) {
                    continue;
                }
            }

            if (!$phone) {
                $this->warn("[" . ($idx + 1) . "] Bỏ qua dòng thiếu SĐT hợp lệ. HS: '{$studentName}'");
                $skipCount++;
                continue;
            }

            if ($amount <= 0) {
                $this->warn("[" . ($idx + 1) . "] Số tiền hoàn không hợp lệ ({$amount}). SĐT: {$phone}, HS: {$studentName}");
                $failCount++;
                continue;
            }

            // 1. Tìm học sinh theo SĐT
            $student = $this->findStudentByPhone($phone);
            if (!$student) {
                $this->error("[" . ($idx + 1) . "] Không tìm thấy học sinh trong DB với SĐT: {$phone} (HS: {$studentName})");
                $failCount++;
                continue;
            }

            // 2. Tìm hợp đồng của học sinh
            $agreement = $this->findAgreementForStudent($student->id, $amount);
            if (!$agreement) {
                $this->error("[" . ($idx + 1) . "] Không tìm thấy hợp đồng phù hợp cho học sinh ID {$student->id} ({$student->name}), SĐT: {$phone}");
                $failCount++;
                continue;
            }

            // 3. Kiểm tra trùng lặp (Duplicate Check)
            if ($this->isDuplicatePayment($agreement->id, $student->id, $amount, $refundDate)) {
                $this->warn("[" . ($idx + 1) . "] [DUPLICATE] Bản ghi đã được xử lý hoàn tiền trước đó. SĐT: {$phone}, Số tiền: " . number_format($amount) . "đ, Ngày: {$refundDate}. Bỏ qua.");
                $skipCount++;
                continue;
            }

            // 4. Xử lý hoàn tiền thừa (refundExcess logic)
            try {
                DB::transaction(function () use ($agreement, $student, $amount, $refundDate, $studentName) {
                    $agreementId = $agreement->id;
                    $totalCharged = (float)data_get($agreement, 'total_charged', 0);
                    $received = (float)data_get($agreement, 'received_amount', 0);
                    $transferred = (float)data_get($agreement, 'transferred_amount', 0);
                    $mustCharge = (float)data_get($agreement, 'must_charge', 0);

                    $newTotalCharged = $totalCharged - $amount;
                    $newEffective = $newTotalCharged + $received - $transferred;
                    $newDebt = max(0, $mustCharge - $newEffective);

                    // Update agreements
                    u::updateSimpleRow([
                        'total_charged' => $newTotalCharged,
                        'debt_amount' => $newDebt,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updator_id' => 1,
                    ], ['id' => $agreementId], 'agreements');

                    // Insert payments with is_import = 1
                    u::insertSimpleRow([
                        'agreement_id' => $agreementId,
                        'student_id'   => $student->id,
                        'branch_id'    => $agreement->branch_id,
                        'ec_id'        => $agreement->ec_id,
                        'method'       => 1,
                        'must_charge'  => $mustCharge,
                        'amount'       => -$amount,
                        'total'        => $newTotalCharged,
                        'debt'         => $newDebt,
                        'charge_date'  => $refundDate ,
                        'note'         => 'Import hoàn tiền thừa cho học sinh',
                        'created_at'   => date('Y-m-d H:i:s'),
                        'creator_id'   => 1,
                        'type'         => 3, // 3: Hoàn tiền
                        'is_import'    => 1, // Đánh dấu phân biệt import
                    ], 'payments');

                    // Log agreements and student log
                    u::addLogAgreements($agreementId);
                    $code = data_get($agreement, 'code', '');
                    LogStudents::logAdd($student->id, "Hoàn tiền thừa hợp đồng (Import) - {$code} (" . number_format($amount) . 'đ)', 1);
                });

                $this->info("[" . ($idx + 1) . "] SUCCESS: Hoàn tiền thành công " . number_format($amount) . "đ cho SĐT: {$phone} (HS: {$student->name}, HĐ: {$agreement->code})");
                $successCount++;
            } catch (\Exception $e) {
                $this->error("[" . ($idx + 1) . "] FAIL: Lỗi khi xử lý SĐT: {$phone} - " . $e->getMessage());
                $failCount++;
            }
        }

        $this->info("==========================================");
        $this->info("TỔNG KẾT: Thành công: {$successCount} | Bỏ qua (Trùng/Thiếu tin): {$skipCount} | Thất bại: {$failCount}");
        return 0;
    }

    private function normalizePhone(string $raw): ?string
    {
        if (!$raw) return null;
        if (stripos($raw, 'e+') !== false) {
            $raw = number_format((float) $raw, 0, '.', '');
        }
        $digits = preg_replace('/\D/', '', $raw);
        if (!$digits) return null;

        if (str_starts_with($digits, '84') && strlen($digits) >= 11) {
            $digits = '0' . substr($digits, 2);
        }
        if (!str_starts_with($digits, '0')) {
            $digits = '0' . $digits;
        }
        $digits = substr($digits, 0, 10);
        return strlen($digits) >= 9 ? $digits : null;
    }

    private function findStudentByPhone(string $phone)
    {
        $phoneClean = ltrim($phone, '0');
        return DB::table('students')
            ->where(function ($q) use ($phone, $phoneClean) {
                $q->whereIn('gud_mobile1', [$phone, $phoneClean])
                  ->orWhereIn('gud_mobile2', [$phone, $phoneClean])
                  ->orWhereIn('c2c_mobile', [$phone, $phoneClean]);
            })
            ->where('status', '>', 0)
            ->orderBy('id', 'desc')
            ->first();
    }

    private function findAgreementForStudent(int $studentId, float $amount)
    {
        $agreements = DB::table('agreements')
            ->where('student_id', $studentId)
            ->where('status', '>', 0)
            ->orderBy('id', 'desc')
            ->get();

        if ($agreements->isEmpty()) {
            return null;
        }

        // Chọn hợp đồng có dư tiền thừa hoặc dư tiền có thể hoàn
        foreach ($agreements as $agr) {
            $totalCharged = (float)data_get($agr, 'total_charged', 0);
            $received = (float)data_get($agr, 'received_amount', 0);
            $transferred = (float)data_get($agr, 'transferred_amount', 0);
            $mustCharge = (float)data_get($agr, 'must_charge', 0);

            $effective = $totalCharged + $received - $transferred;
            $excess = $effective - $mustCharge;

            if ($excess >= $amount || $effective >= $amount) {
                return $agr;
            }
        }

        // Nếu không có hợp đồng nào đủ excess, trả về hợp đồng mới nhất để xử lý
        return $agreements->first();
    }

    private function isDuplicatePayment(int $agreementId, int $studentId, float $amount, string $refundDate): bool
    {
        $negativeAmount = -$amount;
        $dateOnly = substr($refundDate, 0, 10);

        return DB::table('payments')
            ->where('agreement_id', $agreementId)
            ->where('student_id', $studentId)
            ->where('amount', $negativeAmount)
            ->where('type', 3)
            ->whereDate('charge_date', $dateOnly)
            ->exists();
    }
}
