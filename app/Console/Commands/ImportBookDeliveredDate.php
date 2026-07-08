<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportBookDeliveredDate extends Command
{
    protected $signature = 'erp:import-book-delivered-date 
                            {--file= : Đường dẫn file Excel}
                            {--phone-col= : Chỉ số cột SDT (0-indexed, vd: 10 cho cột K). Nếu không điền sẽ tự tìm theo header.}
                            {--class-col= : Chỉ số cột Tên Lớp (0-indexed, vd: 1 cho cột B). Nếu không điền sẽ tự tìm theo header.}';
    protected $description = 'Update contracts.book_delivered_date from Excel (column AF) based on Phone and Class Name';

    public function handle()
    {
        ini_set('memory_limit', '-1');
        $file = $this->option('file');

        if (!$file || !file_exists($file)) {
            $this->error("Không tìm thấy file tại đường dẫn: {$file}");
            return 1;
        }

        $this->info("Đang đọc file Excel (Read Data Only)...");
        $reader = IOFactory::createReaderForFile($file);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file);

        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray(null, true, false, false);
        $highestRow = count($rows);
        $this->info("Tìm thấy {$highestRow} dòng. Đang chuẩn bị cache DB...");

        // 1. Cache Students by Phone
        $studentsByPhone = [];
        DB::table('students')->select('id', 'gud_mobile1', 'gud_mobile2', 'c2c_mobile')
            ->orderBy('id')
            ->chunk(5000, function ($stds) use (&$studentsByPhone) {
                foreach ($stds as $s) {
                    foreach (['gud_mobile1', 'gud_mobile2', 'c2c_mobile'] as $f) {
                        if ($s->$f) {
                            $n = $this->normalizePhone($s->$f);
                            if ($n) {
                                $studentsByPhone[$n] = $s->id;
                            }
                        }
                    }
                }
            });

        // 2. Cache Classes & Products
        $productByClass = [];
        DB::table('classes')->select('code', 'cls_name', 'product_id')->whereNotNull('product_id')->get()->each(function ($c) use (&$productByClass) {
            if ($c->code) $productByClass[mb_strtolower(trim($c->code), 'UTF-8')] = $c->product_id;
            if ($c->cls_name) $productByClass[mb_strtolower(trim($c->cls_name), 'UTF-8')] = $c->product_id;
        });

        DB::table('products')->select('id', 'name')->get()->each(function ($p) use (&$productByClass) {
            $productByClass[mb_strtolower(trim($p->name), 'UTF-8')] = $p->id;
        });

        // Xác định cột SDT, Tên Lớp, và Cột AF (Ngày gửi sách)
        $phoneCol = $this->option('phone-col') !== null ? (int) $this->option('phone-col') : -1;
        $classCol = $this->option('class-col') !== null ? (int) $this->option('class-col') : -1;
        $dateCol = 31; // Cột AF là index 31 (A=0, Z=25, AA=26, AF=31)

        if ($phoneCol === -1 || $classCol === -1) {
            foreach ($rows[0] as $colIdx => $colName) {
                $colNameLower = mb_strtolower(trim($colName), 'UTF-8');
                if (in_array($colNameLower, ['sđt', 'điện thoại', 'số điện thoại', 'phone', 'mobile'])) {
                    if ($phoneCol === -1) $phoneCol = $colIdx;
                }
                if (in_array($colNameLower, ['tên lớp', 'lớp', 'mã lớp', 'class', 'sản phẩm', 'khóa học'])) {
                    if ($classCol === -1) $classCol = $colIdx;
                }
            }
        }

        if ($phoneCol === -1 || $classCol === -1) {
            $this->error("Không thể tự động tìm cột SĐT hoặc Tên Lớp. Vui lòng chỉ định qua --phone-col và --class-col");
            return 1;
        }

        $this->info("Sử dụng Cột SĐT index: {$phoneCol}, Cột Lớp index: {$classCol}, Cột AF (Ngày gửi sách) index: {$dateCol}");

        $processedCount = 0;
        $updatedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                if ($index === 0) continue; // Skip header

                $rawPhone = trim($row[$phoneCol] ?? '');
                $rawClass = trim($row[$classCol] ?? '');
                $rawDate  = trim($row[$dateCol] ?? '');

                if (empty($rawPhone) || empty($rawClass) || empty($rawDate)) {
                    continue; // Bỏ qua nếu thiếu dữ liệu cơ bản
                }

                $phone = $this->normalizePhone($rawPhone);
                $classLower = mb_strtolower($rawClass, 'UTF-8');

                $studentId = $studentsByPhone[$phone] ?? null;
                $productId = $productByClass[$classLower] ?? null;

                if (!$studentId) {
                    $this->warn("Dòng {$index}: Không tìm thấy học sinh với SĐT {$rawPhone}");
                    continue;
                }
                if (!$productId) {
                    $this->warn("Dòng {$index}: Không tìm thấy sản phẩm/khóa học cho lớp {$rawClass}");
                    continue;
                }

                // Xử lý ngày tháng từ Excel (AF)
                $bookDate = null;
                if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $rawDate, $m)) {
                    $bookDate = "{$m[3]}-{$m[2]}-{$m[1]}";
                } else {
                    try {
                        $bookDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rawDate)->format('Y-m-d');
                    } catch (\Exception $e) {
                        $bookDate = date('Y-m-d', strtotime($rawDate));
                    }
                }

                if ($bookDate) {
                    $affected = DB::table('contracts')
                        ->where('student_id', $studentId)
                        ->where('product_id', $productId)
                        ->update([
                            'book_delivered_date' => $bookDate,
                            'updated_at' => now()
                        ]);

                    if ($affected > 0) {
                        $updatedCount++;
                        $this->info("Dòng {$index}: Đã cập nhật contracts.book_delivered_date = {$bookDate} cho Student: {$studentId}, Product: {$productId}");
                    } else {
                        $this->warn("Dòng {$index}: Không có bản ghi contracts nào khớp (Student: {$studentId}, Product: {$productId})");
                    }
                }

                $processedCount++;
                if ($processedCount % 100 == 0) {
                    DB::commit();
                    DB::beginTransaction();
                }
            }
            DB::commit();
            $this->info("\nHoàn tất! Đã xử lý {$processedCount} dòng hợp lệ, cập nhật thành công {$updatedCount} hợp đồng.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Lỗi tại dòng {$index}: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function normalizePhone($raw)
    {
        if (empty($raw)) return null;
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
