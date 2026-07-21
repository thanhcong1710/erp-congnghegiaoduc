<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdatePaymentsChargeDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:update-charge-date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cập nhật payments.charge_date từ các file json ứng với payment import đợt 1 và đợt 2 có charge_date là 2026-07-05';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->line('Đang lấy danh sách payments cần cập nhật...');

        $payments = DB::table('payments as p')
            ->leftJoin('agreements as a', 'a.id', '=', 'p.agreement_id')
            ->leftJoin('students as s', 's.id', '=', 'a.student_id')
            ->where('p.charge_date', '2026-07-05')
            ->where(function($q) {
                $q->where('p.note', 'Import đợt 1')
                  ->orWhere('p.note', 'Import đợt 2');
            })
            ->select('p.id as payment_id', 'p.amount', 's.gud_mobile1 as phone', 's.name')
            ->get();

        if ($payments->isEmpty()) {
            $this->info('Không tìm thấy payments nào thỏa mãn điều kiện.');
            return 0;
        }

        $this->info('Tìm thấy ' . $payments->count() . ' bản ghi payments cần xử lý.');

        // Đọc các file JSON
        $this->line('Đang tải dữ liệu từ các file JSON...');
        $jsonFiles = [
            app_path('Console/Data/students_normalized_1.json'),
            app_path('Console/Data/students_normalized_2.json'),
            app_path('Console/Data/students_normalized_3.json'),
            app_path('Console/Data/students_normalized_4.json'),
        ];

        $studentData = [];
        foreach ($jsonFiles as $file) {
            if (file_exists($file)) {
                $content = file_get_contents($file);
                $data = json_decode($content, true);
                if (is_array($data)) {
                    $studentData = array_merge($studentData, $data);
                }
            } else {
                $this->warn("Không tìm thấy file: $file");
            }
        }

        $this->info('Đã tải ' . count($studentData) . ' bản ghi học sinh từ JSON.');
        $this->line('Đang đánh chỉ mục (index) dữ liệu JSON (Phone & Name)...');

        $mapByPhone = [];
        $mapByName = [];
        foreach ($studentData as $item) {
            $jsonPhone = ltrim((string) ($item['phone'] ?? ''), '0');
            $jsonName = mb_strtolower(trim((string) ($item['name'] ?? '')), 'UTF-8');
            if ($jsonPhone !== '') {
                $mapByPhone[$jsonPhone][] = $item;
            }
            if ($jsonName !== '') {
                $mapByName[$jsonName][] = $item;
            }
        }
        $this->info('Đã đánh chỉ mục (index) xong.');

        $this->line('Bắt đầu đối chiếu và cập nhật DB (' . count($payments) . ' bản ghi)...');

        $updatedCount = 0;
        $notFoundCount = 0;

        $bar = $this->output->createProgressBar(count($payments));
        $bar->start();

        foreach ($payments as $payment) {
            $amount = (float) $payment->amount;
            $dbPhone = ltrim((string) $payment->phone, '0');
            $dbName = mb_strtolower(trim((string) $payment->name), 'UTF-8');

            $matchedDate = null;

            // Lấy danh sách các học sinh có khả năng khớp từ Map
            $possibleMatches = [];
            if ($dbPhone !== '' && isset($mapByPhone[$dbPhone])) {
                $possibleMatches = array_merge($possibleMatches, $mapByPhone[$dbPhone]);
            }
            if ($dbName !== '' && isset($mapByName[$dbName])) {
                $possibleMatches = array_merge($possibleMatches, $mapByName[$dbName]);
            }

            foreach ($possibleMatches as $item) {
                $p1Amount = isset($item['payment_1_amount']) ? ((float) $item['payment_1_amount']) * 1000 : 0;
                $p2Amount = isset($item['payment_2_amount']) ? ((float) $item['payment_2_amount']) * 1000 : 0;

                // Kiểm tra khớp số tiền
                if ($p1Amount > 0 && abs($p1Amount - $amount) < 1) {
                    $matchedDate = $item['payment_1_date'] ?? null;
                } elseif ($p2Amount > 0 && abs($p2Amount - $amount) < 1) {
                    $matchedDate = $item['payment_2_date'] ?? null;
                }

                if ($matchedDate) {
                    break;
                }
            }

            if ($matchedDate && preg_match('/^\d{4}-\d{2}-\d{2}$/', $matchedDate)) {
                DB::table('payments')
                    ->where('id', $payment->payment_id)
                    ->update([
                        'charge_date' => $matchedDate
                    ]);
                $updatedCount++;
            } else {
                $notFoundCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->line(''); // new line after progress bar

        $this->info("Xử lý hoàn tất!");
        $this->info("- Số bản ghi đã update thành công: $updatedCount");
        $this->warn("- Số bản ghi không tìm thấy thông tin khớp (hoặc ngày bị rỗng/sai định dạng): $notFoundCount");

        return 0;
    }
}
