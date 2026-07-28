<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Providers\UtilityServiceProvider as u;

/**
 * Xếp học sinh vào lớp từ file not_imported.json (kết quả của erp:check-not-imported).
 *
 * Command CHỈ UPDATE bảng contracts — không tạo mới bất kỳ bản ghi nào.
 *
 * Logic tìm contract phù hợp để update:
 *   1. Lấy student_id từ students.gud_mobile1 (theo phone trong JSON)
 *   2. Lấy class_id từ classes.cls_name (theo class_name trong JSON)
 *   3. Tìm contract: agreement_id thuộc student_id đó, product_id khớp với product_id của lớp,
 *      status chưa phải đang học (status != 6), chưa có class_id
 *   4. Update: class_id, enrolment_start_date, status, real_sessions, summary_sessions, left_sessions
 *
 * CÁCH DÙNG (chỉ xem preview, KHÔNG ghi DB):
 *   php artisan erp:enrol-from-json
 *   php artisan erp:enrol-from-json --class=PT426
 *   php artisan erp:enrol-from-json --class=PT426 --dry-run
 *   php artisan erp:enrol-from-json --dry-run
 *
 * ĐỂ GHI VÀO DB (phải thêm --commit):
 *   php artisan erp:enrol-from-json --commit
 *   php artisan erp:enrol-from-json --class=PT426 --commit
 */
class EnrolStudentsFromJson extends Command
{
    protected $signature = 'erp:enrol-from-json
        {--class=         : Tên lớp cần xếp (ví dụ: PT426). Để trống = xử lý tất cả lớp}
        {--input=         : Đường dẫn file input (mặc định: not_imported.json)}
        {--commit         : THỰC SỰ ghi vào DB. Mặc định là dry-run (chỉ preview)}
        {--limit=0        : Giới hạn số bản ghi xử lý (0 = tất cả)}';

    protected $description = 'Xếp học sinh vào lớp bằng cách UPDATE contracts (không insert mới). Mặc định là dry-run.';

    // ── In-memory lookup maps ──────────────────────────────────────────────────
    /** normalizedPhone → [student_id, ...] */
    private array $studentsByPhone = [];
    /** cls_name_upper → {id, product_id, cm_id, cm_leader_id, program_id, cls_startdate} */
    private array $classesByName = [];
    /** student_id → [{contract_id, product_id, agreement_id, status, class_id, real_sessions, summary_sessions, done_sessions}] */
    private array $contractsByStudent = [];

    // ── Counters ──────────────────────────────────────────────────────────────
    private int $cntUpdated       = 0;
    private int $cntSkipNoStudent = 0;
    private int $cntSkipNoClass   = 0;
    private int $cntSkipNoContract= 0;
    private int $cntSkipAlready   = 0;
    private int $cntTotal         = 0;

    /** @var array Danh sách các update sẽ thực hiện [{contract_id, data, _debug}] */
    private array $pendingUpdates = [];

    // ─────────────────────────────────────────────────────────────────────────
    public function handle(): int
    {
        ini_set('memory_limit', '-1');

        $dataDir   = app_path('Console/Data/backup');
        $inputPath = $this->option('input') ?: $dataDir . '/not_imported.json';
        $classFilter = strtoupper(trim($this->option('class') ?? ''));
        $commit    = (bool) $this->option('commit');
        $limit     = (int) $this->option('limit');

        if (!$commit) {
            $this->warn('⚠️  Chạy ở chế độ DRY-RUN — không có gì được ghi vào DB.');
            $this->warn('   Thêm --commit để thực sự cập nhật.');
            $this->line('');
        } else {
            $this->error('🔴 Chế độ COMMIT — Dữ liệu SẼ được ghi vào DB!');
            $this->line('');
        }

        if (!file_exists($inputPath)) {
            $this->error("File không tồn tại: $inputPath");
            $this->info('Hãy chạy: php artisan erp:check-not-imported trước.');
            return 1;
        }

        $all = json_decode(file_get_contents($inputPath), true);
        if (!is_array($all)) {
            $this->error('File JSON không hợp lệ.');
            return 1;
        }

        // Lọc theo class nếu có
        if ($classFilter) {
            $all = array_filter($all, fn($r) => strtoupper(trim($r['class_name'] ?? '')) === $classFilter);
            $all = array_values($all);
            $this->info("🔍 Lọc theo lớp: $classFilter → " . count($all) . " bản ghi");
        }

        // Chỉ xử lý bản ghi có class_name + start_date + chưa có contract (result = CONTRACT_NOT_FOUND | STUDENT_NOT_FOUND)
        $records = array_filter($all, function ($r) {
            $className = trim($r['class_name'] ?? '');
            $startDate = trim($r['start_date'] ?? '');
            if ($className === '' || $startDate === '') return false;
            // Bỏ qua bản ghi không có SĐT
            $phone = trim((string)($r['phone'] ?? ''));
            if ($phone === '') return false;
            return true;
        });
        $records = array_values($records);

        if ($limit > 0) {
            $records = array_slice($records, 0, $limit);
        }

        $this->info(sprintf('📦 Tổng bản ghi sẽ xử lý: %d', count($records)));
        $this->line('');

        $this->info('🔄 Pre-loading dữ liệu từ DB...');
        $this->preload();
        $this->line('');

        $this->info('⚙️  Đang phân tích từng bản ghi...');
        foreach ($records as $row) {
            $this->processRow($row);
        }

        $this->line('');
        $this->info("📋 Tổng kết phân tích:");
        $this->table(
            ['Mục', 'Số lượng'],
            [
                ['Tổng bản ghi xử lý',               $this->cntTotal],
                ['Không tìm thấy HS trong DB',        $this->cntSkipNoStudent],
                ['Không tìm thấy lớp trong DB',       $this->cntSkipNoClass],
                ['Đã xếp lớp rồi (bỏ qua)',           $this->cntSkipAlready],
                ['Không tìm được contract phù hợp',   $this->cntSkipNoContract],
                ['✅ Sẽ update contracts',             count($this->pendingUpdates)],
            ]
        );

        if (empty($this->pendingUpdates)) {
            $this->info('Không có bản ghi nào cần update.');
            return 0;
        }

        // ── Preview 10 bản ghi đầu ────────────────────────────────────────────
        $this->line('');
        $this->info('📝 Preview (10 bản ghi đầu sẽ được update):');
        $previewRows = [];
        foreach (array_slice($this->pendingUpdates, 0, 10) as $u) {
            $previewRows[] = [
                $u['_debug']['phone'],
                $u['_debug']['name'],
                $u['_debug']['class_name'],
                $u['_debug']['start_date'],
                'contract #' . $u['contract_id'],
                $u['data']['status'],
            ];
        }
        $this->table(['Phone', 'Tên', 'Lớp', 'Start Date', 'Contract', 'Status mới'], $previewRows);

        if (!$commit) {
            $this->line('');
            $this->warn('DRY-RUN: Không có gì được ghi. Thêm --commit để thực sự cập nhật.');
            return 0;
        }

        // ── Ghi DB ────────────────────────────────────────────────────────────
        $this->line('');
        $this->info('💾 Đang cập nhật contracts vào DB...');
        $bar = $this->output->createProgressBar(count($this->pendingUpdates));
        $bar->start();

        DB::beginTransaction();
        try {
            foreach ($this->pendingUpdates as $upd) {
                DB::table('contracts')
                    ->where('id', $upd['contract_id'])
                    ->update($upd['data']);
                $this->cntUpdated++;
                $bar->advance();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $bar->finish();
            $this->line('');
            $this->error('Lỗi khi ghi DB: ' . $e->getMessage());
            return 1;
        }

        $bar->finish();
        $this->line('');
        $this->line('');
        $this->info("✅ Đã cập nhật {$this->cntUpdated} contracts thành công!");
        return 0;
    }

    // ── Pre-load ──────────────────────────────────────────────────────────────
    private function preload(): void
    {
        // 1. Students: normalizedPhone → [student_id, ...]
        $this->info('  → Đang load students...');
        DB::table('students')
            ->whereNotNull('gud_mobile1')
            ->select('id', 'gud_mobile1')
            ->orderBy('id')
            ->chunk(5000, function ($rows) {
                foreach ($rows as $r) {
                    $n = $this->normalizePhone((string)$r->gud_mobile1);
                    if ($n) {
                        $this->studentsByPhone[$n][] = (int)$r->id;
                    }
                }
            });
        $this->info('    ✓ ' . count($this->studentsByPhone) . ' SĐT unique');

        // 2. Classes: cls_name (upper) → {id, product_id, cm_id, cm_leader_id, program_id, cls_startdate}
        $this->info('  → Đang load classes...');
        DB::table('classes')
            ->select('id', 'cls_name', 'product_id', 'cm_id', 'program_id', 'cls_startdate')
            ->orderBy('id')
            ->chunk(5000, function ($rows) {
                foreach ($rows as $c) {
                    if ($c->cls_name) {
                        $key = strtoupper(trim($c->cls_name));
                        if (!isset($this->classesByName[$key])) {
                            $this->classesByName[$key] = [
                                'id'            => (int)$c->id,
                                'product_id'    => (int)$c->product_id,
                                'cm_id'         => $c->cm_id,
                                'program_id'    => $c->program_id,
                                'cls_startdate' => $c->cls_startdate,
                            ];
                        }
                    }
                }
            });
        $this->info('    ✓ ' . count($this->classesByName) . ' lớp');

        // 3. Contracts: student_id → [{contract_id, product_id, agreement_id, status, class_id, ...}]
        //    Chỉ lấy contracts chưa xếp lớp (class_id IS NULL) hoặc đang chờ (status IN 1,2,3,4,5)
        $this->info('  → Đang load contracts (chưa xếp lớp)...');
        DB::table('contracts')
            ->whereNull('class_id')
            ->whereIn('status', [1, 2, 3, 4, 5])
            ->whereNotNull('student_id')
            ->select('id', 'student_id', 'product_id', 'agreement_id', 'status',
                     'class_id', 'real_sessions', 'summary_sessions', 'done_sessions',
                     'must_charge', 'total_charged')
            ->orderBy('id')
            ->chunk(5000, function ($rows) {
                foreach ($rows as $c) {
                    $sid = (int)$c->student_id;
                    $this->contractsByStudent[$sid][] = [
                        'id'               => (int)$c->id,
                        'product_id'       => (int)$c->product_id,
                        'agreement_id'     => (int)$c->agreement_id,
                        'status'           => (int)$c->status,
                        'class_id'         => $c->class_id,
                        'real_sessions'    => (int)$c->real_sessions,
                        'summary_sessions' => (int)$c->summary_sessions,
                        'done_sessions'    => (int)$c->done_sessions,
                    ];
                }
            });
        $this->info('    ✓ ' . count($this->contractsByStudent) . ' HS có contracts chưa xếp lớp');
    }

    // ── Process single row ────────────────────────────────────────────────────
    private function processRow(array $row): void
    {
        $this->cntTotal++;

        $phone     = $this->normalizePhone(trim((string)($row['phone'] ?? '')));
        $className = strtoupper(trim($row['class_name'] ?? ''));
        $startDate = trim($row['start_date'] ?? '');
        $name      = trim($row['name'] ?? '');

        // ── Bước 1: Tìm student_id ────────────────────────────────────────────
        $studentIds = $phone ? ($this->studentsByPhone[$phone] ?? []) : [];
        if (empty($studentIds)) {
            $this->cntSkipNoStudent++;
            return;
        }

        // ── Bước 2: Tìm class ─────────────────────────────────────────────────
        $classInfo = $this->classesByName[$className] ?? null;
        if (!$classInfo) {
            // Thử tìm gần đúng
            foreach ($this->classesByName as $key => $info) {
                if ($key === $className || str_contains($key, $className) || str_contains($className, $key)) {
                    $classInfo = $info;
                    break;
                }
            }
        }
        if (!$classInfo) {
            $this->cntSkipNoClass++;
            return;
        }

        $classId        = $classInfo['id'];
        $classProductId = $classInfo['product_id'];
        $enrolStart     = $classInfo['cls_startdate'] ?: $startDate;

        // ── Bước 3: Tìm contract phù hợp ─────────────────────────────────────
        // Ưu tiên: student khớp phone → contract có product_id khớp lớp → chưa có class_id
        $contractToUpdate = null;

        foreach ($studentIds as $studentId) {
            $contracts = $this->contractsByStudent[$studentId] ?? [];
            foreach ($contracts as $contract) {
                // Contract phải khớp product_id của lớp
                if ($contract['product_id'] != $classProductId) {
                    continue;
                }
                // Contract chưa được xếp lớp nào
                if (!empty($contract['class_id'])) {
                    continue;
                }
                $contractToUpdate = array_merge($contract, ['student_id' => $studentId]);
                break 2;
            }
        }

        if (!$contractToUpdate) {
            // Kiểm tra xem đã xếp lớp này rồi chưa (trong DB có contract với class_id = classId)
            $alreadyEnrolled = false;
            foreach ($studentIds as $studentId) {
                $found = DB::table('contracts')
                    ->where('student_id', $studentId)
                    ->where('class_id', $classId)
                    ->exists();
                if ($found) {
                    $alreadyEnrolled = true;
                    break;
                }
            }
            if ($alreadyEnrolled) {
                $this->cntSkipAlready++;
            } else {
                $this->cntSkipNoContract++;
            }
            return;
        }

        // ── Bước 4: Xác định status mới ──────────────────────────────────────
        $nowDate = date('Y-m-d');
        if ($enrolStart && $enrolStart > $nowDate) {
            $newStatus = 6; // Đang học (chờ khai giảng)
        } else {
            $newStatus = 7; // Đã hoàn thành / đang học
        }

        $sessions = $contractToUpdate['real_sessions'] ?: $contractToUpdate['summary_sessions'];

        // ── Bước 5: Chuẩn bị data update ─────────────────────────────────────
        $updateData = [
            'class_id'             => $classId,
            'program_id'           => $classInfo['program_id'],
            'enrolment_start_date' => $enrolStart,
            'status'               => $newStatus,
            'real_sessions'        => $sessions,
            'summary_sessions'     => $sessions,
            'left_sessions'        => max(0, $sessions - $contractToUpdate['done_sessions']),
            'updated_at'           => now()->toDateTimeString(),
        ];

        $this->pendingUpdates[] = [
            'contract_id' => $contractToUpdate['id'],
            'data'        => $updateData,
            '_debug'      => [
                'phone'      => $row['phone'] ?? '',
                'name'       => $name,
                'class_name' => $row['class_name'] ?? '',
                'start_date' => $startDate,
                'student_id' => $contractToUpdate['student_id'],
            ],
        ];
    }

    // ── Helpers ───────────────────────────────────────────────────────────────
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
}
