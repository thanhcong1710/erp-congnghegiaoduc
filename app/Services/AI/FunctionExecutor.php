<?php

namespace App\Services\AI;

use App\Models\AiKnowledgeBase;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Storage;

class FunctionExecutor
{
    /**
     * Thực thi function dựa vào tên
     */
    public function execute($functionName, $arguments)
    {
        $method = 'execute_' . $functionName;

        if (!method_exists($this, $method)) {
            throw new \Exception("Function {$functionName} not found");
        }

        return $this->$method($arguments);
    }

    /**
     * Function: Lấy thông tin học viên
     */
    protected function execute_get_student_info($args)
    {
        $studentName = $args['student_name'] ?? '';

        // Tìm học viên theo tên
        $student = DB::table('students')
            ->where('name', 'like', "%{$studentName}%")
            ->first();

        if (!$student) {
            return [
                'success' => false,
                'message' => "❌ Không tìm thấy học viên có tên '{$studentName}'",
            ];
        }

        // Lấy thông tin chi tiết học viên (giống StudentsController::show)
        $studentDetail = DB::selectOne("
            SELECT s.*, c.init_total_charged, c.type AS contract_type, c.status AS contract_status,
                c.summary_sessions, c.done_sessions, c.left_sessions, c.total_charged, c.real_sessions,
                (SELECT name FROM branches WHERE id=t.branch_id) AS branch_name,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id=t.ec_id) AS ec_name,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id=t.cm_id) AS cm_name,
                (SELECT cls_name FROM classes WHERE id=c.class_id) AS class_name
            FROM students AS s 
                LEFT JOIN contracts AS c ON c.student_id=s.id 
                LEFT JOIN term_student_user AS t ON t.student_id=s.id
            WHERE (c.count_recharge = 
                    IF((SELECT count(id) FROM contracts WHERE student_id=s.id AND status!=7)>0,
                        (SELECT min(count_recharge) FROM contracts WHERE status !=7 AND student_id =s.id),
                        (SELECT max(count_recharge) FROM contracts WHERE student_id =s.id)) 
                OR c.id IS NULL) AND s.id=?
        ", [$student->id]);

        // Tính số tiền còn lại
        $leftAmount = 0;
        if ($studentDetail && $studentDetail->real_sessions > $studentDetail->done_sessions && $studentDetail->left_sessions) {
            $leftAmount = round($studentDetail->total_charged * ($studentDetail->left_sessions - $studentDetail->done_sessions) / $studentDetail->left_sessions);
        }

        // Lấy danh sách hợp đồng
        $contracts = DB::select("
            SELECT c.created_at, c.code, c.total_sessions, c.bonus_sessions, c.debt_amount, 
                c.must_charge, c.init_tuition_fee_amount, c.status, c.type,
                c.enrolment_start_date, c.enrolment_last_date,
                (SELECT name FROM products WHERE id=c.product_id) AS product_name,
                (SELECT name FROM tuition_fee WHERE id=c.tuition_fee_id) AS tuition_fee_name,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id=c.ec_id) AS ec_name,
                (SELECT name FROM branches WHERE id=c.branch_id) AS branch_name,
                cl.cls_name 
            FROM contracts AS c
                LEFT JOIN classes AS cl ON cl.id=c.class_id
            WHERE c.status>0 AND c.student_id=? 
            ORDER BY c.count_recharge DESC
            LIMIT 5
        ", [$student->id]);

        return [
            'success' => true,
            'data' => [
                'student' => [
                    'id' => $studentDetail->id ?? $student->id,
                    'name' => $studentDetail->name ?? $student->name,
                    'email' => $studentDetail->email ?? '',
                    'phone' => $studentDetail->phone ?? '',
                    'date_of_birth' => $studentDetail->date_of_birth ?? '',
                    'status' => $studentDetail->status ?? '',
                    'branch' => $studentDetail->branch_name ?? '',
                    'ec' => $studentDetail->ec_name ?? '',
                    'cm' => $studentDetail->cm_name ?? '',
                    'class' => $studentDetail->class_name ?? '',
                ],
                'current_contract' => [
                    'type' => $studentDetail->contract_type ?? '',
                    'status' => $studentDetail->contract_status ?? '',
                    'total_sessions' => $studentDetail->summary_sessions ?? 0,
                    'done_sessions' => $studentDetail->done_sessions ?? 0,
                    'left_sessions' => $studentDetail->left_sessions ?? 0,
                    'total_charged' => number_format($studentDetail->total_charged ?? 0, 0, ',', '.') . ' VNĐ',
                    'left_amount' => number_format($leftAmount, 0, ',', '.') . ' VNĐ',
                ],
                'all_contracts' => $contracts,
            ],
        ];
    }

    /**
     * Function: Lấy báo cáo doanh thu
     */
    protected function execute_get_revenue_report($args)
    {
        $month = $args['month'] ?? date('n');
        $year = $args['year'] ?? date('Y');
        $branchId = $args['branch_id'] ?? null;

        // Query doanh thu từ bảng payments
        $query = DB::table('payments')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $totalRevenue = $query->sum('amount');
        $totalPayments = $query->count();

        // Doanh thu theo ngày
        $dailyRevenue = DB::table('payments')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'success' => true,
            'data' => [
                'period' => "Tháng {$month}/{$year}",
                'total_revenue' => number_format($totalRevenue, 0, ',', '.') . ' VNĐ',
                'total_payments' => $totalPayments,
                'daily_breakdown' => $dailyRevenue,
            ],
        ];
    }

    /**
     * Function: Tìm kiếm trong Knowledge Base
     */
    protected function execute_search_knowledge($args)
    {
        $query = $args['query'] ?? '';
        $limit = config('ai.limits.max_knowledge_results', 3);

        $results = AiKnowledgeBase::search($query, $limit);

        if ($results->isEmpty()) {
            return [
                'success' => false,
                'message' => "Không tìm thấy tài liệu liên quan đến '{$query}'",
            ];
        }

        return [
            'success' => true,
            'data' => $results->map(function ($doc) {
                return [
                    'title' => $doc->title,
                    'content' => $doc->content,
                    'category' => $doc->category,
                ];
            })->toArray(),
        ];
    }

    /**
     * Function: Tạo file Excel báo cáo
     */
    protected function execute_create_excel_report($args)
    {
        $reportType = $args['report_type'] ?? 'student_list';
        $filters = $args['filters'] ?? [];

        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Tùy theo report_type, tạo nội dung khác nhau
            switch ($reportType) {
                case 'student_list':
                    $this->generateStudentListExcel($sheet, $filters);
                    break;
                case 'revenue':
                    $this->generateRevenueExcel($sheet, $filters);
                    break;
                default:
                    throw new \Exception("Report type '{$reportType}' not supported");
            }

            // Lưu file
            $fileName = $reportType . '_' . date('YmdHis') . '.xlsx';
            $filePath = 'ai_reports/' . $fileName;

            $writer = new Xlsx($spreadsheet);
            $fullPath = storage_path('app/public/' . $filePath);

            // Tạo thư mục nếu chưa có
            if (!file_exists(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0755, true);
            }

            $writer->save($fullPath);

            return [
                'success' => true,
                'data' => [
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'download_url' => url('storage/' . $filePath),
                ],
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Lỗi khi tạo file Excel: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Helper: Tạo Excel danh sách học viên
     */
    protected function generateStudentListExcel($sheet, $filters)
    {
        // Header
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Họ tên');
        $sheet->setCellValue('C1', 'Email');
        $sheet->setCellValue('D1', 'Số điện thoại');
        $sheet->setCellValue('E1', 'Ngày đăng ký');

        // Data
        $students = DB::table('students')->limit(100)->get();
        $row = 2;

        foreach ($students as $student) {
            $sheet->setCellValue('A' . $row, $student->id);
            $sheet->setCellValue('B' . $row, $student->name);
            $sheet->setCellValue('C' . $row, $student->email ?? '');
            $sheet->setCellValue('D' . $row, $student->phone ?? '');
            $sheet->setCellValue('E' . $row, $student->created_at ?? '');
            $row++;
        }

        // Style header
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
    }

    /**
     * Helper: Tạo Excel báo cáo doanh thu
     */
    protected function generateRevenueExcel($sheet, $filters)
    {
        $month = $filters['month'] ?? date('n');
        $year = $filters['year'] ?? date('Y');

        // Header
        $sheet->setCellValue('A1', 'Ngày');
        $sheet->setCellValue('B1', 'Số hợp đồng');
        $sheet->setCellValue('C1', 'Doanh thu');

        // Data
        $data = DB::table('receipts')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('date')
            ->get();

        $row = 2;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item->date);
            $sheet->setCellValue('B' . $row, $item->count);
            $sheet->setCellValue('C' . $row, $item->total);
            $row++;
        }

        $sheet->getStyle('A1:C1')->getFont()->setBold(true);
    }

    /**
     * Function: Tìm kiếm học sinh (nhiều kết quả)
     */
    protected function execute_search_students($args)
    {
        $keyword = $args['keyword'] ?? '';
        $status = $args['status'] ?? null;
        $limit = $args['limit'] ?? 20;

        // Điều kiện cơ bản
        $cond = " s.status > 0 ";

        // Tìm kiếm theo keyword (giống StudentsController::list)
        if ($keyword !== '') {
            $cond .= " AND (s.lms_code LIKE '%{$keyword}%' 
                        OR s.name LIKE '%{$keyword}%' 
                        OR s.gud_name1 LIKE '%{$keyword}%' 
                        OR s.gud_mobile1 LIKE '%{$keyword}%' 
                        OR s.gud_mobile2 LIKE '%{$keyword}%') ";
        }

        // Filter theo status (logic ưu tiên contract)
        if ($status) {
            $statusMap = [
                'pending' => 1,      // Chưa đóng phí
                'active' => 2,       // Đang học
                'finished' => 3,     // Hết phí
                'deposit' => 4,      // Đặt cọc
                'waiting' => 5,      // Chờ xếp lớp
            ];

            $sf = $statusMap[$status] ?? null;

            if ($sf == 1) { // Chưa đóng phí
                $cond .= " AND (NOT EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status = 6 AND status NOT IN (0, 8))
                    AND NOT EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status IN (3,4,5) AND status NOT IN (0, 8))
                    AND NOT EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status = 2 AND status NOT IN (0, 8))
                    AND EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status = 1 AND status NOT IN (0, 8))) ";
            } elseif ($sf == 2) { // Đang học
                $cond .= " AND EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status = 6 AND status NOT IN (0, 8)) ";
            } elseif ($sf == 3) { // Hết phí
                $cond .= " AND (NOT EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status = 6 AND status NOT IN (0, 8))
                    AND NOT EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status IN (3,4,5) AND status NOT IN (0, 8))
                    AND NOT EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status = 2 AND status NOT IN (0, 8))
                    AND NOT EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status = 1 AND status NOT IN (0, 8))
                    AND EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status = 7 AND status NOT IN (0, 8))) ";
            } elseif ($sf == 4) { // Đặt cọc
                $cond .= " AND (NOT EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status = 6 AND status NOT IN (0, 8))
                    AND NOT EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status IN (3,4,5) AND status NOT IN (0, 8))
                    AND EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status = 2 AND status NOT IN (0, 8))) ";
            } elseif ($sf == 5) { // Chờ xếp lớp
                $cond .= " AND (NOT EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status = 6 AND status NOT IN (0, 8))
                    AND EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status IN (3,4,5) AND status NOT IN (0, 8))) ";
            }
        }

        // Query danh sách học sinh
        $students = DB::select("
            SELECT s.name, s.id, s.lms_code, s.gender, s.date_of_birth, 
                s.gud_name1, s.gud_mobile1, s.avatar_url,
                (SELECT name FROM sources WHERE id = s.source_id) AS source_name,
                (SELECT name FROM branches WHERE id = s.branch_id) AS branch_name
            FROM students AS s
            WHERE {$cond}
            ORDER BY s.id DESC
            LIMIT {$limit}
        ");

        if (empty($students)) {
            return [
                'success' => false,
                'message' => "❌ Không tìm thấy học sinh nào với từ khóa '{$keyword}'",
            ];
        }

        // Lấy thông tin contract cho mỗi học sinh
        foreach ($students as $student) {
            $contract = DB::selectOne("
                SELECT c.id, c.class_id, c.ec_id, c.cm_id, c.done_sessions, 
                    c.summary_sessions, c.type, c.status,
                    (SELECT cls_name FROM classes WHERE id = c.class_id) AS class_name,
                    (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id = c.ec_id) AS ec_name
                FROM contracts AS c
                WHERE c.student_id = ?
                AND c.status NOT IN (0, 8)
                ORDER BY 
                    CASE 
                        WHEN c.status = 6 THEN 1
                        WHEN c.status IN (3,4,5) THEN 2
                        WHEN c.status = 2 THEN 3
                        WHEN c.status = 1 THEN 4
                        ELSE 5
                    END, c.count_recharge DESC
                LIMIT 1
            ", [$student->id]);

            $student->contract = $contract;
        }

        return [
            'success' => true,
            'data' => [
                'total_found' => count($students),
                'keyword' => $keyword,
                'students' => $students,
            ],
        ];
    }

    /**
     * Function: Đếm học sinh theo trạng thái
     */
    protected function execute_count_students_by_status($args)
    {
        $status = $args['status'] ?? 'all';
        $branchId = $args['branch_id'] ?? null;

        $baseCond = " s.status > 0 ";

        if ($branchId) {
            $baseCond .= " AND s.branch_id = {$branchId} ";
        }

        // Đếm theo từng trạng thái (logic ưu tiên contract)
        $statusCounts = [
            'pending' => DB::selectOne("
                SELECT COUNT(DISTINCT s.id) as count
                FROM students AS s
                WHERE {$baseCond}
                AND (NOT EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status = 6 AND status NOT IN (0, 8))
                    AND NOT EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status IN (3,4,5) AND status NOT IN (0, 8))
                    AND NOT EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status = 2 AND status NOT IN (0, 8))
                    AND EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status = 1 AND status NOT IN (0, 8)))
            ")->count ?? 0,

            'active' => DB::selectOne("
                SELECT COUNT(DISTINCT s.id) as count
                FROM students AS s
                WHERE {$baseCond}
                AND EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status = 6 AND status NOT IN (0, 8))
            ")->count ?? 0,

            'finished' => DB::selectOne("
                SELECT COUNT(DISTINCT s.id) as count
                FROM students AS s
                WHERE {$baseCond}
                AND (NOT EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status = 6 AND status NOT IN (0, 8))
                    AND NOT EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status IN (3,4,5) AND status NOT IN (0, 8))
                    AND NOT EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status = 2 AND status NOT IN (0, 8))
                    AND NOT EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status = 1 AND status NOT IN (0, 8))
                    AND EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status = 7 AND status NOT IN (0, 8)))
            ")->count ?? 0,

            'deposit' => DB::selectOne("
                SELECT COUNT(DISTINCT s.id) as count
                FROM students AS s
                WHERE {$baseCond}
                AND (NOT EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status = 6 AND status NOT IN (0, 8))
                    AND NOT EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status IN (3,4,5) AND status NOT IN (0, 8))
                    AND EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status = 2 AND status NOT IN (0, 8)))
            ")->count ?? 0,

            'waiting' => DB::selectOne("
                SELECT COUNT(DISTINCT s.id) as count
                FROM students AS s
                WHERE {$baseCond}
                AND (NOT EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status = 6 AND status NOT IN (0, 8))
                    AND EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status IN (3,4,5) AND status NOT IN (0, 8)))
            ")->count ?? 0,
        ];

        $total = array_sum($statusCounts);

        return [
            'success' => true,
            'data' => [
                'total' => $total,
                'status_filter' => $status,
                'breakdown' => [
                    'pending' => $statusCounts['pending'] . ' (Chưa đóng phí)',
                    'active' => $statusCounts['active'] . ' (Đang học)',
                    'finished' => $statusCounts['finished'] . ' (Hết phí)',
                    'deposit' => $statusCounts['deposit'] . ' (Đặt cọc)',
                    'waiting' => $statusCounts['waiting'] . ' (Chờ xếp lớp)',
                ],
            ],
        ];
    }

    /**
     * Function: Thống kê tổng quan học sinh
     */
    protected function execute_get_student_statistics($args)
    {
        $branchId = $args['branch_id'] ?? null;
        $fromDate = $args['from_date'] ?? null;
        $toDate = $args['to_date'] ?? null;

        $baseCond = " s.status > 0 ";

        if ($branchId) {
            $baseCond .= " AND s.branch_id = {$branchId} ";
        }

        if ($fromDate && $toDate) {
            $baseCond .= " AND s.created_at BETWEEN '{$fromDate}' AND '{$toDate}' ";
        }

        // Tổng số học sinh
        $total = DB::selectOne("SELECT COUNT(*) as count FROM students AS s WHERE {$baseCond}")->count;

        // Học sinh mới trong tháng
        $newThisMonth = DB::selectOne("
            SELECT COUNT(*) as count 
            FROM students 
            WHERE YEAR(created_at) = YEAR(CURRENT_DATE) 
            AND MONTH(created_at) = MONTH(CURRENT_DATE)
            AND status > 0
        ")->count;

        // Học sinh đang học (contract status = 6)
        $activeStudents = DB::selectOne("
            SELECT COUNT(DISTINCT s.id) as count
            FROM students AS s
            WHERE {$baseCond}
            AND EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status = 6 AND status NOT IN (0, 8))
        ")->count;

        // Học sinh chờ xếp lớp
        $waitingStudents = DB::selectOne("
            SELECT COUNT(DISTINCT s.id) as count
            FROM students AS s
            WHERE {$baseCond}
            AND (NOT EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status = 6 AND status NOT IN (0, 8))
                AND EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status IN (3,4,5) AND status NOT IN (0, 8)))
        ")->count;

        // Thống kê theo chi nhánh (nếu không filter branch)
        $byBranch = [];
        if (!$branchId) {
            $byBranch = DB::select("
                SELECT b.name as branch_name, COUNT(s.id) as count
                FROM students AS s
                JOIN branches AS b ON b.id = s.branch_id
                WHERE s.status > 0
                GROUP BY s.branch_id, b.name
                ORDER BY count DESC
            ");
        }

        return [
            'success' => true,
            'data' => [
                'total_students' => $total,
                'new_this_month' => $newThisMonth,
                'active_students' => $activeStudents,
                'waiting_students' => $waitingStudents,
                'by_branch' => $byBranch,
                'period' => $fromDate && $toDate ? "Từ {$fromDate} đến {$toDate}" : "Tất cả",
            ],
        ];
    }

    /**
     * Function: Lấy danh sách gói phí
     */
    protected function execute_get_tuition_fees_list($args)
    {
        $keyword = $args['keyword'] ?? '';
        $productName = $args['product_name'] ?? '';
        $status = $args['status'] ?? null;
        $limit = $args['limit'] ?? 20;

        $cond = " 1 ";

        if ($keyword !== '') {
            $cond .= " AND t.name LIKE '%{$keyword}%' ";
        }

        if ($status) {
            $statusMap = [
                'active' => 1,
                'inactive' => 0,
            ];
            $statusValue = $statusMap[$status] ?? 1;
            $cond .= " AND t.status = {$statusValue} ";
        }

        // Nếu tìm theo sản phẩm: Tìm gói lẻ HOẶC gói combo chứa gói lẻ của sản phẩm đó
        if ($productName !== '') {
            $cond .= " AND (
                -- Gói lẻ có sản phẩm này
                (SELECT name FROM products WHERE id = t.product_id) LIKE '%{$productName}%'
                OR
                -- Gói combo chứa gói lẻ có sản phẩm này
                EXISTS (
                    SELECT 1 
                    FROM tuition_fee_relation AS r
                    JOIN tuition_fee AS tf ON tf.id = r.exchange_tuition_fee_id
                    JOIN products AS p ON p.id = tf.product_id
                    WHERE r.tuition_fee_id = t.id 
                    AND r.status = 1
                    AND p.name LIKE '%{$productName}%'
                )
            ) ";
        }

        $tuitionFees = DB::select("
            SELECT t.*, 
                (SELECT name FROM products WHERE id = t.product_id) AS product_name,
                (SELECT COUNT(id) FROM contracts WHERE tuition_fee_id = t.id) AS total_contracts,
                -- Kiểm tra xem có phải gói combo không
                (SELECT COUNT(id) FROM tuition_fee_relation WHERE tuition_fee_id = t.id AND status = 1) AS is_combo
            FROM tuition_fee AS t
            WHERE {$cond}
            ORDER BY t.id DESC
            LIMIT {$limit}
        ");

        if (empty($tuitionFees)) {
            return [
                'success' => false,
                'message' => $productName
                    ? "❌ Không tìm thấy gói phí nào cho sản phẩm '{$productName}'"
                    : "❌ Không tìm thấy gói phí nào",
            ];
        }

        // Lấy thông tin chi tiết cho từng gói phí
        foreach ($tuitionFees as $fee) {
            $fee->type_label = $fee->is_combo > 0 ? 'Gói Combo' : 'Gói Lẻ';
            $fee->price_formatted = number_format($fee->price ?? 0, 0, ',', '.') . ' VNĐ';

            // Nếu là gói combo, lấy danh sách gói lẻ bên trong
            if ($fee->is_combo > 0) {
                $fee->combo_items = DB::select("
                    SELECT tf.name, 
                        tf.price as original_price,
                        (SELECT name FROM products WHERE id = tf.product_id) AS product_name,
                        r.price_combo,
                        r.stt
                    FROM tuition_fee_relation AS r
                    JOIN tuition_fee AS tf ON tf.id = r.exchange_tuition_fee_id
                    WHERE r.tuition_fee_id = ? AND r.status = 1
                    ORDER BY r.stt
                ", [$fee->id]);

                // Format giá cho combo items
                foreach ($fee->combo_items as $item) {
                    $item->original_price_formatted = number_format($item->original_price ?? 0, 0, ',', '.') . ' VNĐ';
                    $item->price_combo_formatted = number_format($item->price_combo ?? 0, 0, ',', '.') . ' VNĐ';
                }
            }
        }

        return [
            'success' => true,
            'data' => [
                'total_found' => count($tuitionFees),
                'tuition_fees' => $tuitionFees,
            ],
        ];
    }

    /**
     * Function: Lấy chi tiết gói phí
     */
    protected function execute_get_tuition_fee_detail($args)
    {
        $tuitionFeeId = $args['tuition_fee_id'] ?? 0;

        // Lấy thông tin gói phí
        $tuitionFee = DB::selectOne("
            SELECT t.*,
                (SELECT name FROM products WHERE id = t.product_id) AS product_name,
                (SELECT COUNT(id) FROM contracts WHERE tuition_fee_id = t.id) AS total_contracts
            FROM tuition_fee AS t
            WHERE t.id = ?
        ", [$tuitionFeeId]);

        if (!$tuitionFee) {
            return [
                'success' => false,
                'message' => "❌ Không tìm thấy gói phí ID {$tuitionFeeId}",
            ];
        }

        // Lấy các gói phí liên quan (tuition_fee_relation)
        $relatedFees = DB::select("
            SELECT t.*, 
                (SELECT name FROM products WHERE id = t.product_id) AS product_name, 
                r.price_combo, r.stt
            FROM tuition_fee_relation AS r 
            LEFT JOIN tuition_fee AS t ON t.id = r.exchange_tuition_fee_id
            WHERE r.tuition_fee_id = ? AND r.status = 1
            ORDER BY r.stt
        ", [$tuitionFeeId]);

        // Lấy danh sách chi nhánh áp dụng
        $branchIds = explode(',', $tuitionFee->branch_id ?? '');
        $branches = [];
        if (!empty($branchIds[0])) {
            $branches = DB::select("
                SELECT id, name 
                FROM branches 
                WHERE id IN (" . implode(',', $branchIds) . ") AND status = 1
            ");
        }

        return [
            'success' => true,
            'data' => [
                'tuition_fee' => [
                    'id' => $tuitionFee->id,
                    'name' => $tuitionFee->name,
                    'product' => $tuitionFee->product_name,
                    'price' => number_format($tuitionFee->price ?? 0, 0, ',', '.') . ' VNĐ',
                    'session' => $tuitionFee->session ?? 0,
                    'type' => $tuitionFee->type ?? '',
                    'status' => $tuitionFee->status == 1 ? 'Đang hoạt động' : 'Ngừng hoạt động',
                    'total_contracts' => $tuitionFee->total_contracts ?? 0,
                ],
                'branches' => $branches,
                'related_fees' => $relatedFees,
            ],
        ];
    }

    /**
     * Function: Lấy danh sách lớp học
     */
    protected function execute_get_classes_list($args)
    {
        $keyword = $args['keyword'] ?? '';
        $productName = $args['product_name'] ?? '';
        $status = $args['status'] ?? '';
        $classDay = $args['class_day'] ?? '';
        $roomName = $args['room_name'] ?? '';
        $limit = $args['limit'] ?? 20;

        $cond = " c.status = 1 "; // Active classes only

        if ($keyword !== '') {
            $cond .= " AND c.cls_name LIKE '%{$keyword}%' ";
        }

        if ($productName !== '') {
            $cond .= " AND (SELECT name FROM products WHERE id = c.product_id) LIKE '%{$productName}%' ";
        }

        // Filter theo thứ học
        if ($classDay !== '') {
            // Chuyển "Thứ 2" -> "2", "Thứ 3" -> "3"...
            $dayNumber = preg_replace('/[^0-9]/', '', $classDay);
            if ($dayNumber) {
                $cond .= " AND (c.class_day LIKE '{$dayNumber},%' 
                            OR c.class_day LIKE '%,{$dayNumber},%' 
                            OR c.class_day LIKE '%,{$dayNumber}' 
                            OR c.class_day = '{$dayNumber}') ";
            }
        }

        // Filter theo phòng học
        if ($roomName !== '') {
            $cond .= " AND EXISTS (
                SELECT 1 FROM sessions s 
                JOIN rooms r ON r.id = s.room_id 
                WHERE s.class_id = c.id 
                AND r.name LIKE '%{$roomName}%'
            ) ";
        }

        $having = "";
        if ($status !== '') {
            $statusMap = [
                'THIEU' => " AND (c.max_students - total_students) > 0 ",
                'THUA' => " AND (c.max_students - total_students) < 0 ",
                'DU' => " AND (c.max_students - total_students) = 0 ",
            ];
            $having = $statusMap[$status] ?? "";
        }

        $classes = DB::select("
            SELECT c.id, c.cls_name, c.max_students, c.cls_startdate, c.class_day, c.is_online,
                b.name AS branch_name,
                p.name AS product_name,
                u_teacher.name AS teacher_name,
                u_ta.name AS ta_name,
                (SELECT name FROM shifts WHERE id = (SELECT shift_id FROM sessions WHERE class_id = c.id LIMIT 1)) AS shift_name,
                (SELECT start_time FROM shifts WHERE id = (SELECT shift_id FROM sessions WHERE class_id = c.id LIMIT 1)) AS start_time,
                (SELECT end_time FROM shifts WHERE id = (SELECT shift_id FROM sessions WHERE class_id = c.id LIMIT 1)) AS end_time,
                (SELECT name FROM rooms WHERE id = (SELECT room_id FROM sessions WHERE class_id = c.id LIMIT 1)) AS room_name,
                (SELECT count(ct.id) FROM contracts ct LEFT JOIN students s ON ct.student_id = s.id WHERE ct.class_id = c.id AND ct.status != 7 AND s.status > 0) AS total_students
            FROM classes AS c
                LEFT JOIN branches AS b ON b.id = c.branch_id
                LEFT JOIN products AS p ON p.id = c.product_id
                LEFT JOIN users AS u_teacher ON u_teacher.id = c.teacher_id
                LEFT JOIN users AS u_ta ON u_ta.id = c.ta_id
            WHERE {$cond}
            HAVING 1=1 {$having}
            ORDER BY c.id DESC
            LIMIT {$limit}
        ");

        if (empty($classes)) {
            return [
                'success' => false,
                'message' => "❌ Không tìm thấy lớp học nào",
            ];
        }

        // Format dữ liệu
        foreach ($classes as $class) {
            $class->total_students = (int) $class->total_students;
            $class->max_students = (int) $class->max_students;
            $diff = $class->max_students - $class->total_students;

            if ($diff > 0) {
                $class->status_text = "THIẾU {$diff} HS";
                $class->status_class = 'warning';
            } elseif ($diff < 0) {
                $class->status_text = "THỪA " . abs($diff) . " HS";
                $class->status_class = 'danger';
            } else {
                $class->status_text = "ĐỦ";
                $class->status_class = 'success';
            }

            // Format lịch học
            $days = [];
            if ($class->class_day) {
                $days_arr = explode(',', $class->class_day);
                foreach ($days_arr as $d) {
                    $days[] = "T{$d}";
                }
            }
            $class->schedule_text = implode('+', $days);
            if ($class->start_time && $class->end_time) {
                $class->schedule_text .= " (" . substr($class->start_time, 0, 5) . "-" . substr($class->end_time, 0, 5) . ")";
            }

            $class->cls_startdate = date('d/m/Y', strtotime($class->cls_startdate));
            $class->is_online_text = $class->is_online == 1 ? 'Online' : 'Offline';
        }

        return [
            'success' => true,
            'data' => [
                'total_found' => count($classes),
                'classes' => $classes,
            ],
        ];
    }

    /**
     * Function: Lấy chi tiết lớp học
     */
    protected function execute_get_class_detail($args)
    {
        $classId = $args['class_id'] ?? 0;

        // Lấy thông tin lớp
        $class = DB::selectOne("
            SELECT c.*, 
                b.name AS branch_name,
                p.name AS product_name,
                pr.name AS program_name,
                u_teacher.name AS teacher_name,
                u_cm.name AS cm_name,
                u_ta.name AS ta_name
            FROM classes AS c
                LEFT JOIN branches AS b ON b.id = c.branch_id
                LEFT JOIN products AS p ON p.id = c.product_id
                LEFT JOIN programs AS pr ON pr.id = c.program_id
                LEFT JOIN users AS u_teacher ON u_teacher.id = c.teacher_id
                LEFT JOIN users AS u_cm ON u_cm.id = c.cm_id
                LEFT JOIN users AS u_ta ON u_ta.id = c.ta_id
            WHERE c.id = ?
        ", [$classId]);

        if (!$class) {
            return [
                'success' => false,
                'message' => "❌ Không tìm thấy lớp học ID {$classId}",
            ];
        }

        // Lấy thông tin session (shift, room)
        $session = DB::selectOne("
            SELECT s.shift_id, s.room_id,
                (SELECT name FROM shifts WHERE id = s.shift_id) AS shift_name,
                (SELECT start_time FROM shifts WHERE id = s.shift_id) AS start_time,
                (SELECT end_time FROM shifts WHERE id = s.shift_id) AS end_time,
                (SELECT name FROM rooms WHERE id = s.room_id) AS room_name
            FROM sessions AS s
            WHERE s.class_id = ? AND s.status = 1
            LIMIT 1
        ", [$classId]);

        // Lấy danh sách học sinh
        $students = DB::select("
            SELECT s.id, s.name, s.email, s.phone, c.status as contract_status
            FROM contracts AS c
            JOIN students AS s ON s.id = c.student_id
            WHERE c.class_id = ? AND c.status != 7 AND s.status > 0
            ORDER BY s.name
        ", [$classId]);

        // Lấy danh sách môn học
        $subjects = DB::select("
            SELECT s.name, sc.session, sc.stt
            FROM subject_has_class AS sc
            LEFT JOIN subjects AS s ON s.id = sc.subject_id
            WHERE sc.class_id = ?
            ORDER BY sc.stt
        ", [$classId]);

        // Format lịch học
        $days = [];
        if ($class->class_day) {
            $days_arr = explode(',', $class->class_day);
            foreach ($days_arr as $d) {
                $days[] = "Thứ {$d}";
            }
        }
        $schedule_text = implode(', ', $days);
        if ($session && $session->start_time && $session->end_time) {
            $schedule_text .= " (" . substr($session->start_time, 0, 5) . "-" . substr($session->end_time, 0, 5) . ")";
        }

        return [
            'success' => true,
            'data' => [
                'class_info' => [
                    'id' => $class->id,
                    'name' => $class->cls_name,
                    'code' => $class->code ?? '',
                    'branch' => $class->branch_name,
                    'product' => $class->product_name,
                    'program' => $class->program_name,
                    'start_date' => date('d/m/Y', strtotime($class->cls_startdate)),
                    'max_students' => $class->max_students,
                    'total_students' => count($students),
                    'type' => $class->type,
                    'is_online' => $class->is_online == 1 ? 'Online' : 'Offline',
                    'status' => $class->status == 1 ? 'Đang hoạt động' : 'Ngừng hoạt động',
                    'schedule' => $schedule_text,
                    'room' => $session->room_name ?? '',
                    'teacher' => $class->teacher_name ?? '',
                    'ta' => $class->ta_name ?? '',
                    'cm' => $class->cm_name ?? '',
                ],
                'students' => $students,
                'subjects' => $subjects,
            ],
        ];
    }
}
