<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Kiểm tra các bản ghi trong students_normalized_*.json xem đã được import vào hệ thống chưa.
 * Chỉ kiểm tra bản ghi có cả class_name VÀ start_date.
 *
 * Logic kiểm tra (3 bước):
 *   1. Tìm trong students.gud_mobile1 theo số điện thoại (chuẩn hóa).
 *   2. Nếu có HS → kiểm tra contracts.student_id có contract nào có class_id
 *      tương ứng với classes.cls_name = class_name trong JSON không.
 *   3. Nếu KHÔNG tìm thấy → đây là bản ghi chưa import → ghi ra file output.
 *
 * CÁCH DÙNG:
 *   php artisan erp:check-not-imported
 *   php artisan erp:check-not-imported --output=/path/to/not_imported.json
 */
class CheckNotImportedStudents extends Command
{
    protected $signature = 'erp:check-not-imported
        {--output=        : Đường dẫn file output (mặc định: app/Console/Data/backup/not_imported.json)}
        {--verbose-check  : Hiển thị chi tiết từng bản ghi đang kiểm tra}';

    protected $description = 'Kiểm tra các bản ghi trong students_normalized JSON chưa được import và xuất ra file riêng.';

    /** @var array normalizedPhone → [student_id, ...] */
    private array $studentsByPhone = [];

    /** @var array cls_name_upper → class_id */
    private array $classesByName = [];

    /** @var array student_id → [class_id, ...] (contracts đã có class_id) */
    private array $contractClassesByStudent = [];

    // ─── Data files ───────────────────────────────────────────────────────────
    private array $dataFiles = [];

    // ─── Counters ─────────────────────────────────────────────────────────────
    private int $cntTotal          = 0; // tổng bản ghi có class_name + start_date
    private int $cntNoPhone        = 0; // không có SĐT hợp lệ
    private int $cntStudentFound   = 0; // tìm thấy HS trong DB
    private int $cntStudentNotFound= 0; // không tìm thấy HS
    private int $cntClassNotFound  = 0; // HS có nhưng class_name không khớp lớp nào trong DB
    private int $cntContractOk     = 0; // đã import đầy đủ (contract có class_id trùng)
    private int $cntNotImported    = 0; // chưa import / chưa xếp lớp

    // ─────────────────────────────────────────────────────────────────────────
    public function handle(): int
    {
        ini_set('memory_limit', '-1');

        $dataDir = app_path('Console/Data/backup');
        $this->dataFiles = [
            $dataDir . '/students_normalized_1.json',
            $dataDir . '/students_normalized_2.json',
            $dataDir . '/students_normalized_3.json',
            $dataDir . '/students_normalized_4.json',
        ];

        $outputPath = $this->option('output') ?: $dataDir . '/not_imported.json';

        // Kiểm tra file tồn tại
        foreach ($this->dataFiles as $f) {
            if (!file_exists($f)) {
                $this->warn("⚠️  File không tồn tại, bỏ qua: $f");
            }
        }

        $this->info('🔄 Pre-loading dữ liệu từ DB...');
        $this->preload();

        $this->info('⚙️  Đang kiểm tra từng bản ghi...');
        $notImported = [];

        foreach ($this->dataFiles as $filePath) {
            if (!file_exists($filePath)) {
                continue;
            }

            $fileName = basename($filePath);
            $all      = json_decode(file_get_contents($filePath), true);
            if (!is_array($all)) {
                $this->error("❌ File JSON không hợp lệ: $filePath");
                continue;
            }

            $this->info("📂 Kiểm tra file: $fileName (" . count($all) . " bản ghi)");

            foreach ($all as $row) {
                // Chỉ xử lý bản ghi có class_name và start_date
                $className = trim($row['class_name'] ?? '');
                $startDate = trim($row['start_date'] ?? '');
                if ($className === '' || $startDate === '') {
                    continue;
                }

                $this->cntTotal++;
                $phone = trim((string)($row['phone'] ?? ''));

                if ($this->option('verbose-check')) {
                    $this->line("  ▶ Phone={$phone}, class={$className}, date={$startDate}");
                }

                // ── Bước 1: Chuẩn hóa SĐT ────────────────────────────────────────
                if ($phone === '') {
                    $this->cntNoPhone++;
                    $row['_check_result'] = 'NO_PHONE';
                    $notImported[] = array_merge($row, ['_source_file' => $fileName]);
                    $this->cntNotImported++;
                    continue;
                }

                $normalizedPhone = $this->normalizePhone($phone);
                if (!$normalizedPhone) {
                    $this->cntNoPhone++;
                    $row['_check_result'] = 'INVALID_PHONE';
                    $notImported[] = array_merge($row, ['_source_file' => $fileName]);
                    $this->cntNotImported++;
                    continue;
                }

                // ── Bước 2: Tìm HS trong DB ───────────────────────────────────────
                $studentIds = $this->studentsByPhone[$normalizedPhone] ?? [];
                if (empty($studentIds)) {
                    $this->cntStudentNotFound++;
                    $row['_check_result'] = 'STUDENT_NOT_FOUND';
                    $notImported[] = array_merge($row, [
                        '_source_file'      => $fileName,
                        '_normalized_phone' => $normalizedPhone,
                    ]);
                    $this->cntNotImported++;
                    continue;
                }

                $this->cntStudentFound++;

                // ── Bước 3: Tìm class_id tương ứng với class_name ────────────────
                $classNameUpper = strtoupper($className);
                $classId = $this->classesByName[$classNameUpper] ?? null;

                if (!$classId) {
                    // Thử tìm gần đúng (class_name có thể có prefix/suffix)
                    foreach ($this->classesByName as $key => $id) {
                        if (str_contains($key, $classNameUpper) || str_contains($classNameUpper, $key)) {
                            $classId = $id;
                            break;
                        }
                    }
                }

                if (!$classId) {
                    $this->cntClassNotFound++;
                    $row['_check_result'] = 'CLASS_NOT_FOUND_IN_DB';
                    $notImported[] = array_merge($row, [
                        '_source_file'      => $fileName,
                        '_normalized_phone' => $normalizedPhone,
                        '_student_ids'      => $studentIds,
                    ]);
                    $this->cntNotImported++;
                    continue;
                }

                // ── Bước 4: Kiểm tra contract của HS có class_id này chưa ──────
                $contractFound = false;
                foreach ($studentIds as $studentId) {
                    $classIds = $this->contractClassesByStudent[$studentId] ?? [];
                    if (in_array($classId, $classIds, true)) {
                        $contractFound = true;
                        break;
                    }
                }

                if ($contractFound) {
                    $this->cntContractOk++;
                    // Đã import → bỏ qua
                } else {
                    $row['_check_result'] = 'CONTRACT_NOT_FOUND';
                    $notImported[] = array_merge($row, [
                        '_source_file'      => $fileName,
                        '_normalized_phone' => $normalizedPhone,
                        '_student_ids'      => $studentIds,
                        '_class_id'         => $classId,
                        '_class_name'       => $className,
                    ]);
                    $this->cntNotImported++;
                }
            }
        }

        // ── Ghi output ────────────────────────────────────────────────────────
        file_put_contents($outputPath, json_encode($notImported, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        $this->line('');
        $this->table(
            ['Mục', 'Số lượng'],
            [
                ['Tổng bản ghi có class_name + start_date', $this->cntTotal],
                ['Không có SĐT hợp lệ',                    $this->cntNoPhone],
                ['Tìm thấy HS trong DB',                    $this->cntStudentFound],
                ['Không tìm thấy HS trong DB',              $this->cntStudentNotFound],
                ['Không tìm thấy lớp trong DB (class_name)', $this->cntClassNotFound],
                ['Đã import đầy đủ (có contract + lớp)',   $this->cntContractOk],
                ['⚠️  Chưa import (ghi vào output)',        $this->cntNotImported],
            ]
        );

        $this->info("✅ Xong! Bản ghi chưa import đã được lưu vào: $outputPath");
        return 0;
    }

    // ── Pre-load ──────────────────────────────────────────────────────────────
    private function preload(): void
    {
        // 1. Students: normalizedPhone → [student_id, ...]
        //    Một SĐT có thể gắn với nhiều HS (trường hợp trùng SĐT) → lưu mảng
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

        // 2. Classes: cls_name (upper) → class_id
        $this->info('  → Đang load classes...');
        DB::table('classes')
            ->select('id', 'cls_name')
            ->orderBy('id')
            ->chunk(5000, function ($rows) {
                foreach ($rows as $c) {
                    if ($c->cls_name) {
                        $key = strtoupper(trim($c->cls_name));
                        // Giữ id nhỏ nhất nếu trùng tên
                        if (!isset($this->classesByName[$key])) {
                            $this->classesByName[$key] = (int)$c->id;
                        }
                    }
                }
            });
        $this->info('    ✓ ' . count($this->classesByName) . ' lớp');

        // 3. Contracts: student_id → [class_id, ...] (chỉ những contract có class_id)
        $this->info('  → Đang load contracts (có class_id)...');
        DB::table('contracts')
            ->whereNotNull('class_id')
            ->select('student_id', 'class_id')
            ->orderBy('id')
            ->chunk(5000, function ($rows) {
                foreach ($rows as $c) {
                    $sid = (int)$c->student_id;
                    $cid = (int)$c->class_id;
                    if (!in_array($cid, $this->contractClassesByStudent[$sid] ?? [], true)) {
                        $this->contractClassesByStudent[$sid][] = $cid;
                    }
                }
            });
        $this->info('    ✓ ' . count($this->contractClassesByStudent) . ' HS có contract với lớp');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────
    private function normalizePhone(string $raw): ?string
    {
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
