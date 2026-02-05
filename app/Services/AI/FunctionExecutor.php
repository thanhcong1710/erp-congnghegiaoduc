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

        // Tìm học viên (giả sử bảng students)
        $student = DB::table('students')
            ->where('name', 'like', "%{$studentName}%")
            ->first();

        if (!$student) {
            return [
                'success' => false,
                'message' => "Không tìm thấy học viên có tên '{$studentName}'",
            ];
        }

        // Lấy thêm thông tin điểm, chuyên cần (tùy cấu trúc DB của bạn)
        $scores = DB::table('scores')
            ->where('student_id', $student->id)
            ->get();

        $attendance = DB::table('attendance')
            ->where('student_id', $student->id)
            ->where('status', 'absent')
            ->count();

        return [
            'success' => true,
            'data' => [
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email ?? '',
                    'phone' => $student->phone ?? '',
                ],
                'scores' => $scores,
                'absent_count' => $attendance,
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

        // Query doanh thu (giả sử bảng contracts/receipts)
        $query = DB::table('receipts')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('status', 'approved');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $totalRevenue = $query->sum('amount');
        $totalContracts = $query->distinct('contract_id')->count();

        // Doanh thu theo ngày
        $dailyRevenue = $query
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->get();

        return [
            'success' => true,
            'data' => [
                'period' => "Tháng {$month}/{$year}",
                'total_revenue' => $totalRevenue,
                'total_contracts' => $totalContracts,
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
}
