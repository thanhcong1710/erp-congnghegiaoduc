<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportSalaryMonth extends Command
{
    protected $signature = 'erp:import-salary-month {json : Đường dẫn file JSON}';
    protected $description = 'Update agreements.salary_month from normalized JSON based on Phone and Class Name';

    public function handle()
    {
        ini_set('memory_limit', '-1');
        $file = $this->argument('json');

        if (!$file || !file_exists($file)) {
            $this->error("Không tìm thấy file tại đường dẫn: {$file}");
            return 1;
        }

        $this->info("Đang đọc file JSON...");
        $data = json_decode(file_get_contents($file), true);
        
        if (!is_array($data)) {
            $this->error("File JSON không hợp lệ");
            return 1;
        }

        $totalRows = count($data);
        $this->info("Tìm thấy {$totalRows} record. Đang chuẩn bị cache DB...");

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

        // 3. Cache Agreements
        $this->info("Đang cache Agreements...");
        $agreementMap = [];
        DB::table('agreements')->select('id', 'student_id', 'product_id')
            ->orderBy('id')
            ->chunk(5000, function ($agrs) use (&$agreementMap) {
                foreach ($agrs as $a) {
                    if ($a->product_id) {
                        $agreementMap[$a->student_id][$a->product_id] = $a->id;
                    }
                }
            });

        DB::table('contracts')
            ->join('agreements', 'agreements.id', '=', 'contracts.agreement_id')
            ->select('agreements.id as agreement_id', 'agreements.student_id', 'contracts.product_id')
            ->whereNull('agreements.product_id')
            ->whereNotNull('contracts.product_id')
            ->orderBy('contracts.id')
            ->chunk(5000, function ($rows) use (&$agreementMap) {
                foreach ($rows as $r) {
                    $agreementMap[$r->student_id][$r->product_id] = $r->agreement_id;
                }
            });

        $processedCount = 0;
        $updates = [];

        try {
            foreach ($data as $index => $row) {
                $rawPhone = trim($row['phone'] ?? '');
                $rawClass = trim($row['class_name'] ?? '');
                $salaryMonth  = trim($row['salary_month'] ?? '');

                if (empty($rawPhone) || empty($rawClass) || empty($salaryMonth)) {
                    continue; // Bỏ qua nếu thiếu dữ liệu cơ bản
                }

                $phone = $this->normalizePhone($rawPhone);
                $classLower = mb_strtolower($rawClass, 'UTF-8');

                $studentId = $studentsByPhone[$phone] ?? null;
                $productId = $productByClass[$classLower] ?? null;

                if (!$studentId || !$productId) {
                    continue;
                }

                $agreementId = $agreementMap[$studentId][$productId] ?? null;
                if ($agreementId) {
                    $updates[$agreementId] = $salaryMonth;
                }

                $processedCount++;
            }

            $totalUpdates = count($updates);
            $this->info("Chuẩn bị update {$totalUpdates} agreements...");

            $count = 0;
            $chunks = array_chunk($updates, 500, true);
            foreach ($chunks as $chunk) {
                $ids = array_keys($chunk);
                $cases = [];
                $params = [];
                foreach ($chunk as $id => $month) {
                    $cases[] = "WHEN ? THEN ?";
                    $params[] = $id;
                    $params[] = $month;
                }
                
                $idsStr = implode(',', array_fill(0, count($ids), '?'));
                $sql = "UPDATE agreements SET salary_month = CASE id " . implode(' ', $cases) . " END, updated_at = NOW() WHERE id IN ($idsStr)";
                
                $finalParams = array_merge($params, $ids);
                DB::statement($sql, $finalParams);
                
                $count += count($chunk);
                $this->info("Đã update {$count}/{$totalUpdates} agreements...");
            }

            $this->info("\nHoàn tất file! Đã cập nhật thành công {$count} agreements.");
        } catch (\Exception $e) {
            $this->error("Lỗi: " . $e->getMessage());
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
