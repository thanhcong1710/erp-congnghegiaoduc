<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ImportAnKhanhCommand extends Command
{
    protected $signature = 'import:ankhanh 
                            {--file= : Path to Excel file}
                            {--reset-all-data : Wipe all test data (parents, students, care, contracts, classes) and reset IDs to 1}
                            {--clear-demo : Clear demo data created earlier}
                            {--dry-run : Parse file and show summary without saving to DB}
                            {--run : Execute import into DB with batch tracking}
                            {--rollback : Rollback all records created in the last import batch}';

    protected $description = 'Import students, parents, contracts and classes for CS1 — Thang Long Victory';

    const BATCH_CODE = 'BATCH_ANKHANH_202611';
    const BRANCH_ID = 1; // CS1 — Thăng Long Victory
    const BATCH_FILE = 'import_batches/ankhanh_cs1_batch.json';

    public function handle()
    {
        ini_set('memory_limit', '1024M');

        if ($this->option('reset-all-data')) {
            $res = $this->handleResetAllData();
            if ($res !== 0 || !$this->option('run')) {
                return $res;
            }
        }

        if ($this->option('clear-demo')) {
            return $this->handleClearDemo();
        }

        if ($this->option('rollback')) {
            return $this->handleRollback();
        }

        if (!$this->option('dry-run') && !$this->option('run')) {
            $this->warn("Vui lòng chọn một cờ thực thi:");
            $this->line("  --reset-all-data : Xóa sạch toàn bộ data test (khách hàng, học sinh, CSKH, hợp đồng, lớp học...) và reset ID về 1");
            $this->line("  --dry-run        : Kiểm tra và xem trước kết quả parse dữ liệu (không lưu DB)");
            $this->line("  --run            : Chạy import chính thức vào DB");
            $this->line("  --clear-demo     : Xóa toàn bộ dữ liệu import demo trước đó");
            $this->line("  --rollback       : Thu hồi (xóa) toàn bộ bản ghi đã import của batch này");
            return 0;
        }

        return $this->handleImport($this->option('run'));
    }

    /**
     * Wipe all test data and reset AUTO_INCREMENT to 1
     */
    protected function handleResetAllData()
    {
        $this->warn("=== BẮT ĐẦU XÓA TOÀN BỘ DỮ LIỆU TEST & RESET AUTO_INCREMENT VỀ 1 ===");

        $tablesToWipe = [
            // Khách hàng & CSKH
            "crm_parents", "crm_customer_care", "crm_parent_logs", "crm_parent_overwrite",
            "crm_student_checkin", "crm_tickets",
            // Học sinh
            "crm_students", "students", "term_student_user", "student_logs",
            "student_waitting_process", "file_has_student",
            // Hợp đồng & Thanh toán & Chuyển đổi
            "contracts", "log_contracts", "payments", "tuition_transfer",
            "reserves", "reserve_multis", "class_transfer", "branch_transfer",
            "exchanges", "coupon_logs",
            // Lớp học & Lịch học
            "classes", "sessions", "subject_has_class", "schedule_has_student", "schedules",
            // Đánh giá / Kết quả
            "igbh_student_results", "igbh_student_result_details",
            "igbh_summative_results", "igbh_summative_result_details",
            "igbh_weekly_evals", "igbh_weekly_eval_details",
            "ucrea_student_results", "ucrea_student_result_details",
            // Báo cáo tạm
            "report_classes", "report_full_fee_active", "report_pending",
            "report_renews", "report_reserve", "report_student_in_class", "report_users",
            "tmp_import"
        ];

        try {
            DB::statement("SET FOREIGN_KEY_CHECKS = 0;");

            $wipedCount = 0;
            foreach ($tablesToWipe as $table) {
                if (Schema::hasTable($table)) {
                    $cnt = DB::table($table)->count();
                    DB::statement("TRUNCATE TABLE `{$table}`;");
                    try {
                        DB::statement("ALTER TABLE `{$table}` AUTO_INCREMENT = 1;");
                    } catch (\Exception $e) {
                        // ignore if table doesn't support auto_increment
                    }
                    $this->line("- Đã làm sạch `{$table}` (xóa {$cnt} dòng) và reset AUTO_INCREMENT = 1");
                    $wipedCount++;
                }
            }

            DB::statement("SET FOREIGN_KEY_CHECKS = 1;");

            // Remove batch tracking file
            $batchFilePath = storage_path('app/' . self::BATCH_FILE);
            if (file_exists($batchFilePath)) {
                @unlink($batchFilePath);
            }

            $this->info("Đã xóa sạch toàn bộ {$wipedCount} bảng dữ liệu test!");
            $this->info("Toàn bộ các bảng đã sẵn sàng với ID bắt đầu từ 1.\n");
            return 0;
        } catch (\Exception $e) {
            DB::statement("SET FOREIGN_KEY_CHECKS = 1;");
            $this->error("Lỗi khi reset bảng: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Clear demo data
     */
    protected function handleClearDemo()
    {
        $this->info("=== BẮT ĐẦU XÓA DỮ LIỆU DEMO ĐÃ IMPORT HÔM TRƯỚC ===");

        $demoStudentIds = [1838, 1839, 1840];
        $demoParentIds = [8172, 8173, 8175];
        $demoCrmStudentIds = [6977, 6978, 6979, 6980, 6981];

        // Also find any additional students or parents created around that test if needed
        DB::beginTransaction();
        try {
            // 1. student_logs
            $cntLogs = 0;
            if (Schema::hasTable('student_logs')) {
                $cntLogs = DB::table('student_logs')->whereIn('student_id', $demoStudentIds)->delete();
            }

            // 2. term_student_user
            $cntTsu = DB::table('term_student_user')->whereIn('student_id', $demoStudentIds)->delete();

            // 3. students
            $cntStudents = DB::table('students')->whereIn('id', $demoStudentIds)->delete();

            // 4. crm_customer_care
            $cntCare = 0;
            if (Schema::hasTable('crm_customer_care')) {
                $cntCare = DB::table('crm_customer_care')->whereIn('parent_id', $demoParentIds)->delete();
            }

            // 5. crm_parent_logs
            $cntParentLogs = 0;
            if (Schema::hasTable('crm_parent_logs')) {
                $cntParentLogs = DB::table('crm_parent_logs')->whereIn('parent_id', $demoParentIds)->delete();
            }

            // 6. crm_students
            $cntCrmStudents = DB::table('crm_students')
                ->whereIn('id', $demoCrmStudentIds)
                ->orWhereIn('parent_id', $demoParentIds)
                ->delete();

            // 7. crm_parents
            $cntParents = DB::table('crm_parents')->whereIn('id', $demoParentIds)->delete();

            DB::commit();

            $this->info("Đã xóa sạch dữ liệu demo:");
            $this->line("- students: {$cntStudents} bản ghi (IDs: " . implode(', ', $demoStudentIds) . ")");
            $this->line("- term_student_user: {$cntTsu} bản ghi");
            $this->line("- student_logs: {$cntLogs} bản ghi");
            $this->line("- crm_students: {$cntCrmStudents} bản ghi");
            $this->line("- crm_parents: {$cntParents} bản ghi (IDs: " . implode(', ', $demoParentIds) . ")");
            $this->line("- crm_customer_care: {$cntCare} bản ghi");
            $this->line("- crm_parent_logs: {$cntParentLogs} bản ghi");
            $this->info("Hệ thống đã sẵn sàng cho đợt import dữ liệu thật!");
            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Lỗi khi xóa dữ liệu demo: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Handle Import (Dry-run or Run)
     */
    protected function handleImport($isRealRun = false)
    {
        $filePath = $this->resolveFilePath();
        if (!$filePath || !file_exists($filePath)) {
            $this->error("Không tìm thấy file Excel tại: " . ($filePath ?: 'Đường dẫn rỗng'));
            return 1;
        }

        $this->info("Đang đọc file Excel: " . $filePath);
        $reader = new Xlsx();
        $reader->setReadDataOnly(true);
        $sheetsToRead = ['DSHS TOÁN', 'TIỀN TIỂU HỌC', 'TIẾNG ANH', 'Bảo lưu'];
        $reader->setLoadSheetsOnly($sheetsToRead);

        $t0 = microtime(true);
        $spreadsheet = $reader->load($filePath);
        $this->info("Đã nạp file thành công trong " . round(microtime(true) - $t0, 2) . "s");

        // Parse all rows
        $parsedRows = [];
        $uniqueClasses = [];
        $uniqueFees = [];

        foreach ($sheetsToRead as $sname) {
            $sheet = $spreadsheet->getSheetByName($sname);
            if (!$sheet) continue;

            $maxRow = $sheet->getHighestRow();
            $startRow = ($sname === 'Bảo lưu') ? 2 : 8;

            for ($r = $startRow; $r <= $maxRow; $r++) {
                $row = $this->parseSheetRow($sheet, $sname, $r);
                if ($row) {
                    $parsedRows[] = $row;
                    if (!empty($row['class_name'])) {
                        $clsProduct = $this->resolveClassProduct($row['class_name'], $row['product_id'], $row['product_name']);
                        $uniqueClasses[$row['class_name']] = [
                            'name' => $row['class_name'],
                            'product_id' => $clsProduct['id'],
                            'product_name' => $clsProduct['name']
                        ];
                    }
                    $feeKey = $row['product_id'] . '_' . $row['duration_months'] . '_' . $row['duration_sessions'];
                    $uniqueFees[$feeKey] = [
                        'product_id' => $row['product_id'],
                        'product_name' => $row['product_name'],
                        'duration' => $row['duration_raw'],
                        'months' => $row['duration_months'],
                        'sessions' => $row['duration_sessions']
                    ];
                }
            }
        }

        $this->info("Tổng số dòng học sinh hợp lệ tìm thấy: " . count($parsedRows));
        $this->info("Số lớp học cần ánh xạ/khởi tạo: " . count($uniqueClasses));

        if (!$isRealRun) {
            $this->showDryRunSummary($parsedRows, $uniqueClasses, $uniqueFees);
            $this->info("\n=== DRY RUN HOÀN TẤT. Để chạy thật và lưu vào DB, hãy chạy lệnh:");
            $this->comment("php artisan import:ankhanh --run");
            return 0;
        }

        // REAL RUN
        $this->info("\n=== BẮT ĐẦU IMPORT THẬT VÀO DATABASE (CS1 — Thăng Long Victory) ===");
        return $this->executeRealImport($parsedRows, $uniqueClasses);
    }

    /**
     * Resolve Excel file path
     */
    protected function resolveFilePath()
    {
        if ($this->option('file')) {
            return $this->option('file');
        }

        $candidates = [
            '/Users/mac24h/Desktop/Stemkey/Final và theo dõi DS An Khánh.xlsx',
            '/Users/mac24h/Desktop/Stemkey/Final và theo dõi DS An Khánh.xlsx',
        ];

        foreach ($candidates as $c) {
            if (file_exists($c)) return $c;
        }

        return null;
    }

    /**
     * Parse single row from sheet
     */
    protected function parseSheetRow($sheet, $sheetName, $r)
    {
        $isBaoLuu = ($sheetName === 'Bảo lưu');

        $colCode     = $isBaoLuu ? 3 : 4;
        $colName     = $isBaoLuu ? 4 : 5;
        $colDob      = $isBaoLuu ? 5 : 6;
        $colYob      = $isBaoLuu ? 6 : 7;
        $colCourse   = $isBaoLuu ? 7 : 8;
        $colDuration = $isBaoLuu ? 8 : 9;
        $colGift     = $isBaoLuu ? 10 : 11; // Tháng tặng / Học bổng
        $colSchool   = $isBaoLuu ? 11 : 13;
        $colDadName  = $isBaoLuu ? 12 : 14;
        $colDadTel   = $isBaoLuu ? 13 : 15;
        $colMomName  = $isBaoLuu ? 14 : 16;
        $colMomTel   = $isBaoLuu ? 15 : 17;
        $colAddress  = $isBaoLuu ? 16 : 18;
        $colStatus   = $isBaoLuu ? 17 : 19;
        $colStartDate= $isBaoLuu ? 18 : 20;
        $colClass    = $isBaoLuu ? 20 : 22;
        $colPrice    = $isBaoLuu ? 25 : 23;
        $colCharged  = $isBaoLuu ? null : 24;
        $colDebt     = $isBaoLuu ? 25 : 25;
        $colFeeStatus= $isBaoLuu ? null : 26;
        $colRegDate  = $isBaoLuu ? 2 : 2;

        $name = trim((string)$sheet->getCellByColumnAndRow($colName, $r)->getValue());
        $code = trim((string)$sheet->getCellByColumnAndRow($colCode, $r)->getValue());

        if (empty($name) && empty($code)) {
            return null;
        }

        // Normalize Dad & Mom
        $dadName = trim((string)$sheet->getCellByColumnAndRow($colDadName, $r)->getValue());
        $dadTel = $this->normalizePhone($sheet->getCellByColumnAndRow($colDadTel, $r)->getValue());
        $momName = trim((string)$sheet->getCellByColumnAndRow($colMomName, $r)->getValue());
        $momTel = $this->normalizePhone($sheet->getCellByColumnAndRow($colMomTel, $r)->getValue());

        // Determine Primary Parent
        $parentName = '';
        $parentMobile = '';
        $parentName2 = '';
        $parentMobile2 = '';

        if (!empty($momTel)) {
            $parentName = !empty($momName) ? $momName : 'Mẹ ' . $name;
            $parentMobile = $momTel;
            if (!empty($dadTel) || !empty($dadName)) {
                $parentName2 = !empty($dadName) ? $dadName : 'Bố ' . $name;
                $parentMobile2 = $dadTel;
            }
        } elseif (!empty($dadTel)) {
            $parentName = !empty($dadName) ? $dadName : 'Bố ' . $name;
            $parentMobile = $dadTel;
            if (!empty($momName)) {
                $parentName2 = $momName;
            }
        } else {
            // Neither has phone
            $parentName = !empty($momName) ? $momName : (!empty($dadName) ? $dadName : 'PH ' . $name);
            $parentMobile = '';
        }

        // Dates
        $dobVal = $sheet->getCellByColumnAndRow($colDob, $r)->getValue();
        $dob = $this->parseDate($dobVal);
        $yob = trim((string)$sheet->getCellByColumnAndRow($colYob, $r)->getValue());
        if (!$dob && !empty($yob) && is_numeric($yob) && $yob > 1990 && $yob < 2030) {
            $dob = "{$yob}-01-01";
        }

        $startDateVal = $sheet->getCellByColumnAndRow($colStartDate, $r)->getValue();
        $startDate = $this->parseDate($startDateVal);
        $regDateVal = $sheet->getCellByColumnAndRow($colRegDate, $r)->getValue();
        $regDate = $this->parseDate($regDateVal);

        if (!$startDate) {
            $startDate = $regDate ?: date('Y-m-d');
        }

        // Product & Course
        $course = trim((string)$sheet->getCellByColumnAndRow($colCourse, $r)->getValue());
        $productInfo = $this->resolveProduct($sheetName, $course);

        // Class
        $className = trim((string)$sheet->getCellByColumnAndRow($colClass, $r)->getValue());

        // Duration & Sessions
        $durationRaw = trim((string)$sheet->getCellByColumnAndRow($colDuration, $r)->getValue());
        $durationInfo = $this->parseDuration($durationRaw, $productInfo['id']);

        // Gift / Bonus sessions
        $giftVal = $sheet->getCellByColumnAndRow($colGift, $r)->getValue();
        $bonusSessions = 0;
        if (is_numeric($giftVal) && (int)$giftVal > 0) {
            $bonusSessions = (int)$giftVal * 8;
        }

        // Status
        $statusRaw = trim((string)$sheet->getCellByColumnAndRow($colStatus, $r)->getValue());
        $contractStatus = $this->resolveContractStatus($statusRaw, $isBaoLuu);

        // Class
        $className = trim((string)$sheet->getCellByColumnAndRow($colClass, $r)->getValue());

        // Price & Payment
        $priceVal = $sheet->getCellByColumnAndRow($colPrice, $r)->getValue();
        $chargedVal = $colCharged ? $sheet->getCellByColumnAndRow($colCharged, $r)->getValue() : null;
        $debtVal = $colDebt ? $sheet->getCellByColumnAndRow($colDebt, $r)->getValue() : null;

        $mustCharge = is_numeric($priceVal) ? (int)$priceVal : 0;
        $totalCharged = is_numeric($chargedVal) ? (int)$chargedVal : 0;
        $debtAmount = is_numeric($debtVal) ? (int)$debtVal : 0;

        return [
            'sheet' => $sheetName,
            'row' => $r,
            'code' => $code,
            'name' => $name,
            'dob' => $dob,
            'school' => trim((string)$sheet->getCellByColumnAndRow($colSchool, $r)->getValue()),
            'address' => trim((string)$sheet->getCellByColumnAndRow($colAddress, $r)->getValue()),
            'parent_name' => $parentName,
            'parent_mobile' => $parentMobile,
            'parent_name2' => $parentName2,
            'parent_mobile2' => $parentMobile2,
            'product_id' => $productInfo['id'],
            'product_name' => $productInfo['name'],
            'course' => $course,
            'duration_raw' => $durationRaw,
            'duration_months' => $durationInfo['months'],
            'duration_sessions' => $durationInfo['sessions'],
            'bonus_sessions' => $bonusSessions,
            'start_date' => $startDate,
            'class_name' => $className,
            'status_raw' => $statusRaw,
            'contract_status' => $contractStatus,
            'must_charge' => $mustCharge,
            'total_charged' => $totalCharged,
            'debt_amount' => $debtAmount,
        ];
    }

    /**
     * Resolve Product
     */
    protected function resolveProduct($sheetName, $course)
    {
        $c = mb_strtoupper($course, 'UTF-8');
        if ($sheetName === 'TIỀN TIỂU HỌC' || strpos($c, 'TTH') !== false || strpos($c, 'LUYỆN ĐỌC') !== false) {
            return ['id' => 4, 'name' => 'Tiền Tiểu Học'];
        }
        if ($sheetName === 'TIẾNG ANH' || strpos($c, 'TIẾNG ANH') !== false || strpos($c, 'TA') !== false) {
            return ['id' => 3, 'name' => 'Tiếng Anh'];
        }
        if ($sheetName === 'ROBOTIC') {
            return ['id' => 1, 'name' => 'Robotic'];
        }
        // DSHS TOAN
        if (strpos($c, 'UCREA') !== false || $c === 'U') {
            return ['id' => 1, 'name' => 'Ucrea'];
        }
        if (strpos($c, 'I.G') !== false || strpos($c, 'IG') !== false || strpos($c, 'BH') !== false || strpos($c, 'BRIGHT') !== false) {
            return ['id' => 2, 'name' => 'Bright I.G'];
        }

        return ['id' => 1, 'name' => 'Ucrea'];
    }

    /**
     * Resolve Product for Class based on Class Name
     */
    protected function resolveClassProduct($className, $defaultId = 1, $defaultName = 'Ucrea')
    {
        $cn = mb_strtoupper($className, 'UTF-8');
        if (strpos($cn, 'BRIGHT') !== false || strpos($cn, '.IG.') !== false || strpos($cn, 'IG ') !== false || strpos($cn, '.BH.') !== false) {
            return ['id' => 2, 'name' => 'Bright I.G'];
        }
        if (strpos($cn, '.U.') !== false || strpos($cn, 'UCREA') !== false || strpos($cn, '.U21.') !== false) {
            return ['id' => 1, 'name' => 'Ucrea'];
        }
        if (strpos($cn, 'TTH') !== false || strpos($cn, 'LUYỆN ĐỌC') !== false) {
            return ['id' => 4, 'name' => 'Tiền Tiểu Học'];
        }
        if (strpos($cn, 'ENGLISH') !== false || strpos($cn, 'TA') !== false) {
            return ['id' => 3, 'name' => 'Tiếng Anh'];
        }

        return ['id' => $defaultId, 'name' => $defaultName];
    }

    /**
     * Parse duration into months and sessions
     */
    protected function parseDuration($durationRaw, $productId)
    {
        $d = mb_strtolower(trim($durationRaw), 'UTF-8');

        // Sessions directly
        if (strpos($d, '45 buổi') !== false) {
            return ['months' => 6, 'sessions' => 45];
        }

        // Combinations: học thử 1 tháng + 12 tháng or + 24 tháng
        if (strpos($d, '+') !== false) {
            if (strpos($d, '24') !== false) {
                return ['months' => 25, 'sessions' => 200];
            }
            if (strpos($d, '12') !== false) {
                return ['months' => 13, 'sessions' => 104];
            }
        }

        if (preg_match('/\b24\b/', $d) || strpos($d, '24 tháng') !== false || strpos($d, '2 năm') !== false) {
            return ['months' => 24, 'sessions' => 192];
        }
        if (preg_match('/\b14\b/', $d)) {
            return ['months' => 14, 'sessions' => 112];
        }
        if (preg_match('/\b12\b/', $d) || strpos($d, '12 tháng') !== false || strpos($d, '1 năm') !== false) {
            return ['months' => 12, 'sessions' => 96];
        }
        if (preg_match('/\b9\b/', $d)) {
            return ['months' => 9, 'sessions' => 72];
        }
        if (preg_match('/\b8\b/', $d)) {
            return ['months' => 8, 'sessions' => 64];
        }
        if (preg_match('/\b7\b/', $d)) {
            return ['months' => 7, 'sessions' => 56];
        }
        if (preg_match('/\b6\b/', $d)) {
            return ['months' => 6, 'sessions' => 48];
        }
        if (preg_match('/\b4\b/', $d)) {
            return ['months' => 4, 'sessions' => 32];
        }
        if (preg_match('/\b3\b/', $d)) {
            return ['months' => 3, 'sessions' => 24];
        }
        if (preg_match('/\b2\b/', $d)) {
            return ['months' => 2, 'sessions' => 16];
        }
        if (preg_match('/\b1\b/', $d) || strpos($d, 'thử') !== false) {
            return ['months' => 1, 'sessions' => 8];
        }

        // Defaults by product
        if ($productId == 4) return ['months' => 4, 'sessions' => 32];
        if ($productId == 3) return ['months' => 12, 'sessions' => 96];
        return ['months' => 12, 'sessions' => 96];
    }

    /**
     * Resolve Contract Status
     * 1: Pending, 2: Deposit, 3: Full fee waiting class, 4: Reserved, 6: In Class/Active, 7: Finished / Cancelled
     */
    protected function resolveContractStatus($statusRaw, $isBaoLuu = false)
    {
        if ($isBaoLuu) return 4;

        $s = mb_strtolower(trim($statusRaw), 'UTF-8');
        if (strpos($s, 'đang học') !== false) return 6;
        if (strpos($s, 'full phí đang chờ') !== false) return 3;
        if (strpos($s, 'đặt cọc') !== false || strpos($s, 'đóng tiền lần 1') !== false) return 2;
        if (strpos($s, 'bảo lưu') !== false) return 4;
        if (strpos($s, 'kết thúc') !== false || strpos($s, 'huỷ') !== false || strpos($s, 'hủy') !== false) return 7;

        return 6; // Default to active if learning
    }

    /**
     * Parse Class Days from class name
     * E.g. Bright IG 19. Thứ 3-cn -> [3, 8], STM.06.IG.18.Thứ 7-cn -> [7, 8], English kids 1 -> [4, 6]
     */
    protected function parseClassDays($name)
    {
        $nameClean = mb_strtolower(trim($name), 'UTF-8');
        
        if (strpos($nameClean, 'luyện đọc chủ nhật') !== false) {
            return [8];
        }
        if (strpos($nameClean, 'english kids') !== false) {
            return [4, 6];
        }

        // X-cn or X-chủ nhật (e.g. Thứ 7-cn, t7-chủ nhật, Thứ 3-cn)
        if (preg_match('/(?:thứ\s*|t)?([2-7])\s*[-–]\s*(?:cn|chủ nhật)/iu', $nameClean, $m)) {
            return [(int)$m[1], 8];
        }

        // X-Y (e.g. Thứ 3-5, 4-6, t4-6, Thứ3-6, 2-4, 6-7, 3-6)
        if (preg_match('/(?:thứ\s*|t)?([2-7])\s*[-–]\s*([2-7])/iu', $nameClean, $m)) {
            return [(int)$m[1], (int)$m[2]];
        }

        $days = [];
        if (strpos($nameClean, 'chủ nhật') !== false || strpos($nameClean, 'cn') !== false) {
            $days[] = 8;
        }
        for ($d = 2; $d <= 7; $d++) {
            if (strpos($nameClean, "thứ $d") !== false || strpos($nameClean, "thứ$d") !== false || strpos($nameClean, "t$d") !== false) {
                if (!in_array($d, $days)) {
                    $days[] = $d;
                }
            }
        }
        sort($days);
        return !empty($days) ? $days : [7, 8];
    }

    /**
     * Normalize Phone Number
     */
    protected function normalizePhone($val)
    {
        if (!$val) return '';
        $tel = preg_replace('/[^0-9]/', '', (string)$val);
        if (empty($tel)) return '';

        // If 9 digits starting with non-0, add 0
        if (strlen($tel) == 9) {
            $tel = '0' . $tel;
        }

        return $tel;
    }

    /**
     * Parse Date
     */
    protected function parseDate($val)
    {
        if (!$val) return null;
        if ($val instanceof \DateTimeInterface) {
            return $val->format('Y-m-d');
        }
        if (is_numeric($val)) {
            try {
                return ExcelDate::excelToDateTimeObject($val)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }
        $str = trim((string)$val);
        $time = strtotime($str);
        if ($time && $time > 0) {
            return date('Y-m-d', $time);
        }
        return null;
    }

    /**
     * Dry Run Summary Display
     */
    protected function showDryRunSummary($parsedRows, $uniqueClasses, $uniqueFees)
    {
        $this->info("\n================ BÁO CÁO DRY-RUN IMPORT ================");
        $this->table(
            ['Danh mục', 'Số lượng'],
            [
                ['Tổng số học sinh', count($parsedRows)],
                ['Số lớp học cần tạo/ánh xạ', count($uniqueClasses)],
                ['Học sinh có SĐT phụ huynh', count(array_filter($parsedRows, fn($r) => !empty($r['parent_mobile'])))],
                ['Học sinh thiếu SĐT phụ huynh (Cần lưu ý)', count(array_filter($parsedRows, fn($r) => empty($r['parent_mobile'])))],
            ]
        );

        $this->info("\n--- DANH SÁCH LỚP HỌC (CLASSES) ---");
        $classRows = [];
        foreach ($uniqueClasses as $c) {
            $existing = DB::table('classes')->where('branch_id', self::BRANCH_ID)->where('cls_name', $c['name'])->first();
            $classRows[] = [
                $c['name'],
                $c['product_name'] . " (ID: {$c['product_id']})",
                $existing ? "<info>Đã có (ID: {$existing->id})</info>" : "<comment>Tạo mới</comment>"
            ];
        }
        $this->table(['Tên lớp', 'Sản phẩm', 'Trạng thái'], $classRows);

        $this->info("\n--- 10 DÒNG HỌC SINH MẪU ĐẦU TIÊN ---");
        $sampleTable = [];
        foreach (array_slice($parsedRows, 0, 10) as $i => $row) {
            $sampleTable[] = [
                $i + 1,
                $row['code'],
                $row['name'],
                $row['parent_name'] . ' (' . ($row['parent_mobile'] ?: 'Chưa có SĐT') . ')',
                $row['product_name'],
                $row['duration_raw'] . " ({$row['duration_sessions']} b)",
                $row['class_name'] ?: '(Chưa xếp)',
                $row['status_raw']
            ];
        }
        $this->table(['STT', 'Mã HS', 'Tên HS', 'Phụ huynh (SĐT)', 'Sản phẩm', 'Gói học', 'Lớp học', 'Tình trạng'], $sampleTable);
    }

    /**
     * Real Import Execution
     */
    protected function executeRealImport($parsedRows, $uniqueClasses)
    {
        DB::beginTransaction();

        $batchData = [
            'batch_code' => self::BATCH_CODE,
            'branch_id' => self::BRANCH_ID,
            'imported_at' => date('Y-m-d H:i:s'),
            'class_ids' => [],
            'tuition_fee_ids' => [],
            'parent_ids' => [],
            'crm_student_ids' => [],
            'student_ids' => [],
            'contract_ids' => [],
        ];

        try {
            // 1. Ensure Tuition Fees exist
            $this->info("1. Khởi tạo/Kiểm tra các gói phí (tuition_fee)...");
            $feeMap = $this->ensureTuitionFees($batchData);

            // 2. Ensure Classes exist
            $this->info("2. Khởi tạo các lớp học (classes) và sinh 96 buổi học...");
            $classMap = $this->ensureClasses($uniqueClasses, $batchData, $parsedRows);

            // 3. Process Students, Parents, Contracts
            $this->info("3. Đang import Phụ huynh, Học sinh, Hợp đồng...");
            $bar = $this->output->createProgressBar(count($parsedRows));
            $bar->start();

            $studentProcessedMap = []; // code/name -> student_id

            foreach ($parsedRows as $row) {
                // If student is already processed in this batch (e.g. mentioned in Bảo lưu sheet), update status instead of duplicating
                $studentKey = !empty($row['code']) ? $row['code'] : ($row['name'] . '_' . $row['parent_mobile']);
                if (isset($studentProcessedMap[$studentKey])) {
                    $existingStudentId = $studentProcessedMap[$studentKey];
                    if ($row['contract_status'] == 4) { // Bảo lưu
                        DB::table('contracts')
                            ->where('student_id', $existingStudentId)
                            ->where('branch_id', self::BRANCH_ID)
                            ->update(['status' => 4, 'updated_at' => date('Y-m-d H:i:s')]);
                    }
                    $bar->advance();
                    continue;
                }

                // A. CRM Parent
                $crmParentId = null;
                if (!empty($row['parent_mobile'])) {
                    $existingParent = DB::table('crm_parents')
                        ->where('mobile_1', $row['parent_mobile'])
                        ->first();

                    if ($existingParent) {
                        $crmParentId = $existingParent->id;
                        DB::table('crm_parents')->where('id', $crmParentId)->update([
                            'status' => 10, // 10. KH Đã mua gói phí
                            'level' => 10,
                        ]);
                    } else {
                        $crmParentId = DB::table('crm_parents')->insertGetId([
                            'name' => $row['parent_name'],
                            'mobile_1' => $row['parent_mobile'],
                            'mobile_2' => $row['parent_mobile2'],
                            'address' => $row['address'],
                            'branch_id' => self::BRANCH_ID,
                            'status' => 10, // 10. KH Đã mua gói phí
                            'level' => 10,  // 10. KH Đã mua gói phí
                            'creator_id' => 1,
                            'owner_id' => 1,
                            'note' => "[Import: " . self::BATCH_CODE . "]",
                            'created_at' => date('Y-m-d H:i:s'),
                        ]);
                        $batchData['parent_ids'][] = $crmParentId;
                    }
                }

                // B. CRM Student
                $crmStudentId = null;
                if ($crmParentId) {
                    $existingCrmStudent = DB::table('crm_students')
                        ->where('parent_id', $crmParentId)
                        ->where('name', $row['name'])
                        ->first();

                    if ($existingCrmStudent) {
                        $crmStudentId = $existingCrmStudent->id;
                    } else {
                        $crmStudentId = DB::table('crm_students')->insertGetId([
                            'parent_id' => $crmParentId,
                            'name' => $row['name'],
                            'birthday' => $row['dob'],
                            'status' => 3,
                            'creator_id' => 1,
                            'created_at' => date('Y-m-d H:i:s'),
                        ]);
                        $batchData['crm_student_ids'][] = $crmStudentId;
                    }
                }

                // C. LMS Student
                $arrName = u::explodeName($row['name']);
                $studentId = DB::table('students')->insertGetId([
                    'lms_code' => '', // update later with prefix
                    'name' => $row['name'],
                    'firstname' => data_get($arrName, 'firstname', ''),
                    'midname' => data_get($arrName, 'midname', ''),
                    'lastname' => data_get($arrName, 'lastname', ''),
                    'gender' => 'M',
                    'date_of_birth' => $row['dob'],
                    'gud_name1' => $row['parent_name'],
                    'gud_mobile1' => $row['parent_mobile'],
                    'gud_name2' => $row['parent_name2'],
                    'gud_mobile2' => $row['parent_mobile2'],
                    'school' => $row['school'],
                    'address' => $row['address'],
                    'branch_id' => self::BRANCH_ID,
                    'status' => 1,
                    'avatar_url' => '/images/common/avatar-boy.svg',
                    'note' => "[Import: " . self::BATCH_CODE . "] Mã cũ: {$row['code']}",
                    'creator_id' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                $lastLmsCode = str_pad((string)$studentId, 6, '0', STR_PAD_LEFT);
                $lmsCode = config('app.prefix_student_code', 'STK') . $lastLmsCode;
                DB::table('students')->where('id', $studentId)->update(['lms_code' => $lmsCode]);

                $batchData['student_ids'][] = $studentId;
                $studentProcessedMap[$studentKey] = $studentId;

                // Term Student User
                DB::table('term_student_user')->insert([
                    'student_id' => $studentId,
                    'branch_id' => self::BRANCH_ID,
                    'status' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                // Update LMS ID in CRM
                if ($crmStudentId) {
                    DB::table('crm_students')->where('id', $crmStudentId)->update(['lms_id' => $studentId]);
                }

                // D. Contract (Gói phí)
                $classId = null;
                $classProgramId = null;
                if (!empty($row['class_name']) && isset($classMap[$row['class_name']])) {
                    $classId = $classMap[$row['class_name']]['id'];
                    $classProgramId = $classMap[$row['class_name']]['program_id'];
                }
                $programId = $classProgramId ?: $this->resolveProgramId($row['class_name'], $row['product_id']);

                $feeKey = $row['product_id'] . '_' . $row['duration_months'] . '_' . $row['duration_sessions'];
                $feeInfo = isset($feeMap[$feeKey]) ? $feeMap[$feeKey] : $this->getFallbackFee($row['product_id'], $feeMap);

                $mustCharge = $row['must_charge'] > 0 ? $row['must_charge'] : data_get($feeInfo, 'price', 0);
                $totalCharged = $row['total_charged'] > 0 ? $row['total_charged'] : ($row['contract_status'] == 6 || $row['contract_status'] == 3 ? $mustCharge : 0);
                $debtAmount = max(0, $mustCharge - $totalCharged);

                $contractStatus = $row['contract_status'];
                if ($contractStatus == 6 && !$classId) {
                    $contractStatus = 3; // Waiting class if no class is assigned
                }

                $studentStartDate = $row['start_date'] ?: ($classId && isset($classMap[$row['class_name']]['start_date']) ? $classMap[$row['class_name']]['start_date'] : date('Y-m-d'));

                $summarySessions = $row['duration_sessions'] + $row['bonus_sessions'];

                $contractId = DB::table('contracts')->insertGetId([
                    'type' => 1,
                    'code' => '',
                    'student_id' => $studentId,
                    'branch_id' => self::BRANCH_ID,
                    'product_id' => $row['product_id'],
                    'program_id' => $programId,
                    'tuition_fee_id' => data_get($feeInfo, 'id'),
                    'init_tuition_fee_id' => data_get($feeInfo, 'id'),
                    'init_tuition_fee_amount' => $mustCharge,
                    'init_tuition_fee_receivable' => $mustCharge,
                    'init_tuition_fee_session' => $row['duration_sessions'],
                    'init_total_charged' => $totalCharged,
                    'must_charge' => $mustCharge,
                    'total_charged' => $totalCharged,
                    'debt_amount' => $debtAmount,
                    'total_sessions' => $summarySessions,
                    'real_sessions' => $row['duration_sessions'],
                    'bonus_sessions' => $row['bonus_sessions'],
                    'summary_sessions' => $summarySessions,
                    'reservable_sessions' => 0,
                    'class_id' => $classId,
                    'start_date' => $studentStartDate,
                    'enrolment_start_date' => $studentStartDate,
                    'status' => $contractStatus,
                    'count_recharge' => 0,
                    'note' => "[Import: " . self::BATCH_CODE . "]",
                    'creator_id' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                $lastContractCode = str_pad((string)$contractId, 6, '0', STR_PAD_LEFT);
                $contractCode = config('app.prefix_contract_code', 'C') . $lastContractCode;
                DB::table('contracts')->where('id', $contractId)->update(['code' => $contractCode]);

                $batchData['contract_ids'][] = $contractId;

                // Generate schedule_has_student for sessions already studied (class_date <= '2026-11-08')
                if ($classId && isset($classMap[$row['class_name']]['dates'])) {
                    $classDates = $classMap[$row['class_name']]['dates'];
                    $studentDates = [];
                    foreach ($classDates as $cd) {
                        if ($cd >= $studentStartDate) {
                            $studentDates[] = $cd;
                        }
                    }
                    $studentContractDates = array_slice($studentDates, 0, $summarySessions);
                    $todayStr = date('Y-m-d');

                    $scheduleStudentInserts = [];
                    foreach ($studentContractDates as $sIdx => $sDate) {
                        if ($sDate <= $todayStr) {
                            $scheduleStudentInserts[] = [
                                'student_id' => $studentId,
                                'branch_id' => self::BRANCH_ID,
                                'class_id' => $classId,
                                'contract_id' => $contractId,
                                'product_id' => $row['product_id'],
                                'program_id' => $programId,
                                'subject_id' => 1,
                                'subject_stt' => $sIdx + 1,
                                'class_date' => $sDate,
                                'created_at' => date('Y-m-d H:i:s'),
                                'status' => ($contractStatus == 4 ? 2 : 1), // 1: Đã học (2: bảo lưu)
                                'attendance_status' => 1,
                            ];
                        }
                    }

                    if (!empty($scheduleStudentInserts)) {
                        DB::table('schedule_has_student')->insert($scheduleStudentInserts);
                    }
                }

                // Update done_sessions & left_sessions
                $doneCount = DB::table('schedule_has_student')
                    ->where('contract_id', $contractId)
                    ->where('status', 1)
                    ->count();

                $leftCount = ($contractStatus == 7) ? 0 : max(0, $summarySessions - $doneCount);

                DB::table('contracts')->where('id', $contractId)->update([
                    'done_sessions' => $doneCount,
                    'left_sessions' => $leftCount,
                ]);

                $bar->advance();
            }

            $bar->finish();
            $this->line("");

            // Save Batch JSON for rollback
            $batchFilePath = storage_path('app/' . self::BATCH_FILE);
            @mkdir(dirname($batchFilePath), 0777, true);
            file_put_contents($batchFilePath, json_encode($batchData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            DB::commit();

            $this->info("\n================ IMPORT THÀNH CÔNG RỰC RỠ ================");
            $this->line("- Cơ sở tiếp nhận: CS1 — Thăng Long Victory (ID: " . self::BRANCH_ID . ")");
            $this->line("- Lớp học đã tạo: " . count($batchData['class_ids']) . " lớp");
            $this->line("- Khách hàng (Phụ huynh) tạo mới: " . count($batchData['parent_ids']) . " người");
            $this->line("- Học sinh (LMS) tạo mới: " . count($batchData['student_ids']) . " học sinh");
            $this->line("- Hợp đồng gói phí tạo mới: " . count($batchData['contract_ids']) . " hợp đồng");
            $this->line("- File metadata batch lưu tại: storage/app/" . self::BATCH_FILE);
            $this->info("Toàn bộ dữ liệu đã được gán mã định danh [" . self::BATCH_CODE . "].");
            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("\nĐã xảy ra lỗi trong quá trình import: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }

    /**
     * Ensure Tuition Fees exist
     */
    protected function ensureTuitionFees(&$batchData)
    {
        $existing = DB::table('tuition_fee')->get();
        $feeMap = [];

        foreach ($existing as $f) {
            $key = $f->product_id . '_' . $f->session;
            $feeMap[$key] = $f;
        }

        // Standard custom packages needed for An Khanh
        $required = [
            // Ucrea 1 month
            ['name' => 'Gói STM 2026 - 1 tháng (Ucrea)', 'product_id' => 1, 'session' => 8, 'price' => 2100000, 'months' => 1],
            // Bright IG 1 month
            ['name' => 'Gói STM 2026 - 1 tháng (Bright IG)', 'product_id' => 2, 'session' => 8, 'price' => 2100000, 'months' => 1],
            // TTH 4 months
            ['name' => 'Gói TTH CLC 2026 - 4 tháng', 'product_id' => 4, 'session' => 32, 'price' => 4800000, 'months' => 4],
            // TTH Luyện đọc 45 buổi
            ['name' => 'Gói TTH CLC 2026 - Luyện đọc (45 buổi)', 'product_id' => 4, 'session' => 45, 'price' => 6750000, 'months' => 6],
            // Tiếng Anh 7 months
            ['name' => 'Gói Tiếng Anh 2026 - 7 tháng', 'product_id' => 3, 'session' => 56, 'price' => 17500000, 'months' => 7],
        ];

        foreach ($required as $req) {
            $check = DB::table('tuition_fee')
                ->where('product_id', $req['product_id'])
                ->where('session', $req['session'])
                ->first();

            if (!$check) {
                $newId = DB::table('tuition_fee')->insertGetId([
                    'name' => $req['name'],
                    'product_id' => $req['product_id'],
                    'session' => $req['session'],
                    'price' => $req['price'],
                    'receivable' => $req['price'],
                    'status' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                $batchData['tuition_fee_ids'][] = $newId;
                $check = (object)[
                    'id' => $newId,
                    'name' => $req['name'],
                    'product_id' => $req['product_id'],
                    'session' => $req['session'],
                    'price' => $req['price']
                ];
            }

            $key = $req['product_id'] . '_' . $req['months'] . '_' . $req['session'];
            $feeMap[$key] = $check;
        }

        // Map existing standard packages
        foreach ($existing as $f) {
            if ($f->product_id == 1 || $f->product_id == 2) {
                if ($f->session == 24) $feeMap[$f->product_id . '_3_24'] = $f;
                if ($f->session == 48) $feeMap[$f->product_id . '_6_48'] = $f;
                if ($f->session == 96) $feeMap[$f->product_id . '_12_96'] = $f;
                if ($f->session == 192) $feeMap[$f->product_id . '_24_192'] = $f;
            } elseif ($f->product_id == 4) {
                if ($f->session == 8) $feeMap['4_1_8'] = $f;
                if ($f->session == 48) $feeMap['4_6_48'] = $f;
                if ($f->session == 72) $feeMap['4_9_72'] = $f;
            } elseif ($f->product_id == 3) {
                if ($f->session == 32) $feeMap['3_4_32'] = $f;
                if ($f->session == 64) $feeMap['3_8_64'] = $f;
                if ($f->session == 96) $feeMap['3_12_96'] = $f;
                if ($f->session == 192) $feeMap['3_24_192'] = $f;
            }
        }

        return $feeMap;
    }

    /**
     * Get fallback fee
     */
    protected function getFallbackFee($productId, $feeMap)
    {
        foreach ($feeMap as $f) {
            if (data_get($f, 'product_id') == $productId) {
                return $f;
            }
        }
        return (object)['id' => 239, 'price' => 25200000, 'session' => 96];
    }

    /**
     * Ensure Classes exist and generate 96 sessions
     */
    protected function ensureClasses($uniqueClasses, &$batchData, $parsedRows = [])
    {
        $this->ensureBrightIgProgram();

        $classMap = [];

        foreach ($uniqueClasses as $clsName => $info) {
            $programId = $this->resolveProgramId($clsName, $info['product_id']);
            $classDays = $this->parseClassDays($clsName);

            // Find start dates of students belonging to this class
            $studentStarts = [];
            foreach ($parsedRows as $r) {
                if (!empty($r['class_name']) && $r['class_name'] == $clsName && !empty($r['start_date'])) {
                    $studentStarts[] = $r['start_date'];
                }
            }
            $minStartDate = !empty($studentStarts) ? min($studentStarts) : '2026-05-14';

            // Calculate 96 sessions
            $holidays = u::getPublicHolidays(self::BRANCH_ID, $info['product_id']);
            $sessionCalc = u::calculatorSessionsByNumberOfSessions($minStartDate, 96, $holidays, $classDays);
            $clsStartDate = data_get($sessionCalc, 'start_date', $minStartDate);
            $clsEndDate = data_get($sessionCalc, 'end_date', $clsStartDate);
            $sessionDates = data_get($sessionCalc, 'dates', []);

            $existing = DB::table('classes')
                ->where('branch_id', self::BRANCH_ID)
                ->where('cls_name', $clsName)
                ->first();

            $classData = [
                'cls_name' => $clsName,
                'product_id' => $info['product_id'],
                'program_id' => $programId,
                'branch_id' => self::BRANCH_ID,
                'teacher_id' => 1,
                'teacher_name' => 'Admin - admin',
                'cls_startdate' => $clsStartDate,
                'cls_enddate' => $clsEndDate,
                'class_day' => implode(',', $classDays),
                'max_students' => 16,
                'total_cycles' => 1,
                'type' => 1,
                'status' => 1,
                'creator_id' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            if ($existing) {
                unset($classData['creator_id'], $classData['created_at']);
                $classData['updated_at'] = date('Y-m-d H:i:s');
                DB::table('classes')->where('id', $existing->id)->update($classData);
                $classId = $existing->id;
            } else {
                $classId = DB::table('classes')->insertGetId($classData);
                $batchData['class_ids'][] = $classId;
            }

            // Sync sessions table (days in week)
            DB::table('sessions')->where('class_id', $classId)->delete();
            foreach ($classDays as $day) {
                DB::table('sessions')->insert([
                    'class_id' => $classId,
                    'shift_id' => null,
                    'room_id' => 18,
                    'teacher_id' => 1,
                    'class_day' => $day,
                    'status' => 1,
                    'branch_id' => self::BRANCH_ID,
                    'creator_id' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }

            // Sync subject_has_class table
            DB::table('subject_has_class')->where('class_id', $classId)->delete();
            DB::table('subject_has_class')->insert([
                'class_id' => $classId,
                'subject_id' => 1,
                'session' => 96,
                'stt' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            // Sync schedules table (96 sessions)
            DB::table('schedules')->where('class_id', $classId)->delete();
            $schedulesToInsert = [];
            foreach ($sessionDates as $idx => $sDate) {
                $schedulesToInsert[] = [
                    'class_date' => $sDate,
                    'class_id' => $classId,
                    'subject_id' => 1,
                    'class_stt' => $idx + 1,
                    'subject_stt' => $idx + 1,
                    'status' => 1,
                    'branch_id' => self::BRANCH_ID,
                    'teacher_id' => 1,
                    'cm_id' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                ];
            }
            if (!empty($schedulesToInsert)) {
                DB::table('schedules')->insert($schedulesToInsert);
            }

            $classMap[$clsName] = [
                'id' => $classId,
                'program_id' => $programId,
                'start_date' => $clsStartDate,
                'end_date' => $clsEndDate,
                'days' => $classDays,
                'dates' => $sessionDates,
            ];
        }

        return $classMap;
    }

    /**
     * Ensure Bright I.G program exists in programs table
     */
    protected function ensureBrightIgProgram()
    {
        $p2 = DB::table('programs')->where('product_id', 2)->first();
        if (!$p2) {
            DB::table('programs')->insert([
                'name' => 'Bright I.G',
                'code' => 'BRIGHT_IG',
                'product_id' => 2,
                'status' => 1,
                'branch_id' => self::BRANCH_ID,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    /**
     * Resolve Program ID for a class
     */
    protected function resolveProgramId($className, $productId)
    {
        $cn = mb_strtolower((string)$className, 'UTF-8');

        // Product 4: Tiền Tiểu Học
        if ($productId == 4) {
            if (strpos($cn, 'nâng cao') !== false) {
                $p = DB::table('programs')->where('product_id', 4)->where('name', 'LIKE', '%Nâng cao%')->first();
                return $p ? $p->id : 533;
            }
            $p = DB::table('programs')->where('product_id', 4)->where('name', 'LIKE', '%Cơ bản%')->first();
            return $p ? $p->id : 532;
        }

        // Product 3: Tiếng Anh
        if ($productId == 3) {
            if (strpos($cn, 'kids 1') !== false || strpos($cn, 'english kids') !== false) {
                $p = DB::table('programs')->where('product_id', 3)->where('name', 'Kids 1')->first();
                return $p ? $p->id : 526;
            }
            $p = DB::table('programs')->where('product_id', 3)->first();
            return $p ? $p->id : 526;
        }

        // Product 2: Bright I.G
        if ($productId == 2) {
            $p = DB::table('programs')->where('product_id', 2)->first();
            return $p ? $p->id : 534;
        }

        // Product 1: Ucrea
        $p = DB::table('programs')->where('product_id', 1)->first();
        return $p ? $p->id : 520;
    }

    /**
     * Handle Rollback
     */
    protected function handleRollback()
    {
        $batchFilePath = storage_path('app/' . self::BATCH_FILE);
        if (!file_exists($batchFilePath)) {
            $this->error("Không tìm thấy file batch import tại: {$batchFilePath}");
            return 1;
        }

        $batchData = json_decode(file_get_contents($batchFilePath), true);
        $this->warn("=== BẮT ĐẦU ROLLBACK BATCH: " . data_get($batchData, 'batch_code') . " ===");

        DB::beginTransaction();
        try {
            // Delete contracts
            $contractIds = data_get($batchData, 'contract_ids', []);
            if (!empty($contractIds)) {
                DB::table('contracts')->whereIn('id', $contractIds)->delete();
                $this->line("- Đã xóa " . count($contractIds) . " hợp đồng.");
            }

            // Delete term_student_user & students
            $studentIds = data_get($batchData, 'student_ids', []);
            if (!empty($studentIds)) {
                DB::table('term_student_user')->whereIn('student_id', $studentIds)->delete();
                DB::table('students')->whereIn('id', $studentIds)->delete();
                $this->line("- Đã xóa " . count($studentIds) . " học sinh.");
            }

            // Delete crm_students
            $crmStudentIds = data_get($batchData, 'crm_student_ids', []);
            if (!empty($crmStudentIds)) {
                DB::table('crm_students')->whereIn('id', $crmStudentIds)->delete();
                $this->line("- Đã xóa " . count($crmStudentIds) . " crm_students.");
            }

            // Delete crm_parents
            $parentIds = data_get($batchData, 'parent_ids', []);
            if (!empty($parentIds)) {
                DB::table('crm_parents')->whereIn('id', $parentIds)->delete();
                $this->line("- Đã xóa " . count($parentIds) . " crm_parents.");
            }

            // Delete classes
            $classIds = data_get($batchData, 'class_ids', []);
            if (!empty($classIds)) {
                DB::table('classes')->whereIn('id', $classIds)->delete();
                $this->line("- Đã xóa " . count($classIds) . " lớp học.");
            }

            // Delete custom tuition fees
            $feeIds = data_get($batchData, 'tuition_fee_ids', []);
            if (!empty($feeIds)) {
                DB::table('tuition_fee')->whereIn('id', $feeIds)->delete();
                $this->line("- Đã xóa " . count($feeIds) . " gói phí khởi tạo tạm.");
            }

            @unlink($batchFilePath);
            DB::commit();

            $this->info("Rollback thành công toàn bộ batch!");
            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Lỗi khi rollback: " . $e->getMessage());
            return 1;
        }
    }
}
