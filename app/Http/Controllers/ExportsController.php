<?php

namespace App\Http\Controllers;

use App\Providers\UtilityServiceProvider as u;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as x;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Http\Request;
use App\Models\ProcessExcel;
use Illuminate\Support\Facades\Auth;

class ExportsController extends Controller
{
    public function import(Request $request, $import_id)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Tên phụ huynh');
        $sheet->setCellValue('B1', 'Số điện thoại');
        $sheet->setCellValue('C1', 'Số điện thoại 2');
        $sheet->setCellValue('D1', 'Email');
        $sheet->setCellValue('E1', 'Địa chỉ');
        $sheet->setCellValue('F1', 'Ghi chú');
        $sheet->setCellValue('G1', 'Người phụ trách');
        $sheet->setCellValue('H1', 'Học sinh 1');
        $sheet->setCellValue('I1', 'Ngày sinh học sinh 1');
        $sheet->setCellValue('J1', 'Học sinh 2');
        $sheet->setCellValue('K1', 'Ngày sinh học sinh 2');
        $sheet->setCellValue('L1', 'Trạng thái');
        $sheet->setCellValue('M1', 'Thông tin lỗi');

        $parents = u::query("SELECT * FROM crm_import_parents WHERE import_id=$import_id");
        $arr_status = [
            '0' => 'Chưa xử lý',
            '1' => 'Đã kiểm tra dữ liệu đầu vào',
            '2' => 'Dữ liệu đầu vào không hợp lệ',
            '3' => 'Trùng lặp dữ liệu trong file import',
            '4' => 'Trùng lặp dữ liệu khách hàng đang chăm sóc',
            '6' => 'Đã import thành công'
        ];
        $sheet->getColumnDimension("A")->setWidth(30);
        $sheet->getColumnDimension("B")->setWidth(30);
        $sheet->getColumnDimension("C")->setWidth(30);
        $sheet->getColumnDimension("D")->setWidth(30);
        $sheet->getColumnDimension("E")->setWidth(30);
        $sheet->getColumnDimension("F")->setWidth(30);
        $sheet->getColumnDimension("G")->setWidth(30);
        $sheet->getColumnDimension("H")->setWidth(30);
        $sheet->getColumnDimension("I")->setWidth(30);
        $sheet->getColumnDimension("J")->setWidth(30);
        $sheet->getColumnDimension("K")->setWidth(30);
        $sheet->getColumnDimension("L")->setWidth(30);
        $sheet->getColumnDimension("M")->setWidth(30);
        for ($i = 0; $i < count($parents); $i++) {
            $x = $i + 2;
            $sheet->setCellValue('A' . $x, $parents[$i]->name);
            $sheet->setCellValue('B' . $x, $parents[$i]->gud_mobile1 ? "'" . $parents[$i]->gud_mobile1 : $parents[$i]->gud_mobile1);
            $sheet->setCellValue('C' . $x, $parents[$i]->gud_mobile2 ? "'" . $parents[$i]->gud_mobile2 : $parents[$i]->gud_mobile2);
            $sheet->setCellValue('D' . $x, $parents[$i]->email);
            $sheet->setCellValue('E' . $x, $parents[$i]->address);
            $sheet->setCellValue('F' . $x, $parents[$i]->note);
            $sheet->setCellValue('G' . $x, $parents[$i]->owner_hrm);
            $sheet->setCellValue('H' . $x, $parents[$i]->student_name_1);
            $sheet->setCellValue('I' . $x, $parents[$i]->student_birthday_1 ? "'" . $parents[$i]->student_birthday_1 : $parents[$i]->student_birthday_1);
            $sheet->setCellValue('J' . $x, $parents[$i]->student_name_2);
            $sheet->setCellValue('K' . $x, $parents[$i]->student_birthday_2 ? "'" . $parents[$i]->student_birthday_2 : $parents[$i]->student_birthday_2);
            $sheet->setCellValue('L' . $x, $arr_status[$parents[$i]->status]);
            $sheet->setCellValue('M' . $x, $parents[$i]->error_message);
            $sheet->getRowDimension($x)->setRowHeight(23);

        }
        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Kết quả import - ID ' . $import_id . '.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function report01(Request $request, $key, $value)
    {
        set_time_limit(300);
        ini_set('memory_limit', '-1');
        $cond = " r.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";
        $arr_key = explode(',', $key);
        $arr_value = explode(',', $value);
        foreach ($arr_key as $k => $key) {
            if ($key == 'keyword') {
                $keyword = $arr_value[$k];
                $cond .= " AND (p.name LIKE '%$keyword%' OR p.mobile_1 LIKE '%$keyword%' OR p.mobile_2 LIKE '%$keyword%')";
            }
            if ($key == 'start_date') {
                $cond .= " AND r.report_month = '$arr_value[$k]'";
            }
            if ($key == 'branch_id') {
                $cond .= " AND r.branch_id IN (" . str_replace("-", ",", $arr_value[$k]) . ")";
            }
        }

        $order_by = " ORDER BY r.id DESC ";
        $list = u::query("SELECT b.name AS branch_name, s.lms_code, s.name, s.gud_name1, cl.cls_name, p.name AS product_name,
                CONCAT (u.hrm_id, ' - ', u.name) AS cm_name, t.name AS tuition_fee_name,
                IF(r.type=0, 'NEW', 'RENEW') AS type_fee, r.last_done_sessions, r.done_sessions,r.summary_sessions,r.start_date, r.end_date
            FROM report_full_fee_active AS r 
                LEFT JOIN students AS s ON s.id=r.student_id
                LEFT JOIN branches AS b ON b.id = r.branch_id
                LEFT JOIN classes AS cl ON cl.id = r.class_id
                LEFT JOIN products AS p ON p.id = r.product_id
                LEFT JOIN tuition_fee AS t ON t.id = r.init_tuition_fee_id
                LEFT JOIN users AS u ON u.id=r.cm_id
            WHERE $cond $order_by");

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Trung tâm');
        $sheet->setCellValue('B1', 'Mã học sinh');
        $sheet->setCellValue('C1', 'Học sinh');
        $sheet->setCellValue('D1', 'Tên phụ huynh');
        $sheet->setCellValue('E1', 'Lớp');
        $sheet->setCellValue('F1', 'Sản phẩm');
        $sheet->setCellValue('G1', 'CM');
        $sheet->setCellValue('H1', 'Gói phí');
        $sheet->setCellValue('I1', 'Loại');
        $sheet->setCellValue('J1', 'Tổng số buổi');
        $sheet->setCellValue('K1', 'Số buổi còn lại');
        $sheet->setCellValue('L1', 'Ngày bắt đầu');
        $sheet->setCellValue('M1', 'Ngày kết thúc');

        $sheet->getColumnDimension("A")->setWidth(30);
        $sheet->getColumnDimension("B")->setWidth(20);
        $sheet->getColumnDimension("C")->setWidth(30);
        $sheet->getColumnDimension("D")->setWidth(30);
        $sheet->getColumnDimension("E")->setWidth(30);
        $sheet->getColumnDimension("F")->setWidth(20);
        $sheet->getColumnDimension("G")->setWidth(30);
        $sheet->getColumnDimension("H")->setWidth(30);
        $sheet->getColumnDimension("I")->setWidth(20);
        $sheet->getColumnDimension("J")->setWidth(20);
        $sheet->getColumnDimension("K")->setWidth(20);
        $sheet->getColumnDimension("L")->setWidth(20);
        $sheet->getColumnDimension("M")->setWidth(20);
        for ($i = 0; $i < count($list); $i++) {
            $x = $i + 2;
            $sheet->setCellValue('A' . $x, $list[$i]->branch_name);
            $sheet->setCellValue('B' . $x, $list[$i]->lms_code);
            $sheet->setCellValue('C' . $x, $list[$i]->name);
            $sheet->setCellValue('D' . $x, $list[$i]->gud_name1);
            $sheet->setCellValue('E' . $x, $list[$i]->cls_name);
            $sheet->setCellValue('F' . $x, $list[$i]->product_name);
            $sheet->setCellValue('G' . $x, $list[$i]->cm_name);
            $sheet->setCellValue('H' . $x, $list[$i]->tuition_fee_name);
            $sheet->setCellValue('I' . $x, $list[$i]->type_fee);
            $sheet->setCellValue('J' . $x, $list[$i]->summary_sessions + $list[$i]->last_done_sessions);
            $sheet->setCellValue('K' . $x, $list[$i]->summary_sessions - $list[$i]->done_sessions);
            $sheet->setCellValue('L' . $x, $list[$i]->start_date);
            $sheet->setCellValue('M' . $x, $list[$i]->end_date);

            $sheet->getRowDimension($x)->setRowHeight(23);

        }
        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Báo cáo full fee active.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function report02a(Request $request, $key, $value)
    {
        set_time_limit(300);
        ini_set('memory_limit', '-1');
        $cond = " r.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";
        $arr_key = explode(',', $key);
        $arr_value = explode(',', $value);
        foreach ($arr_key as $k => $key) {
            if ($key == 'keyword') {
                $keyword = $arr_value[$k];
                $cond .= " AND (p.name LIKE '%$keyword%' OR p.mobile_1 LIKE '%$keyword%' OR p.mobile_2 LIKE '%$keyword%')";
            }
            if ($key == 'branch_id') {
                $cond .= " AND r.branch_id IN (" . str_replace("-", ",", $arr_value[$k]) . ")";
            }
            if ($key == 'cm_id') {
                $cond .= " AND r.cm_id = " . $arr_value[$k];
            }
            if ($key == 'class_id') {
                $cond .= " AND r.class_id = " . $arr_value[$k];
            }
            if ($key == 'start_date') {
                $cond .= " AND r.last_date >= '" . $arr_value[$k] . "'";
            }
            if ($key == 'end_date') {
                $cond .= " AND r.last_date <= '" . $arr_value[$k] . "'";
            }
        }

        $order_by = " ORDER BY r.id DESC ";
        $list = u::query("SELECT s.name AS student_name, s.lms_code, s.gud_mobile1, r.last_date, r.status, r.renew_amount,
                b.name AS branch_name,
                p.name AS product_name,
                cl.cls_name AS class_name,
                IF(r.status=1,'Thành công','Thất bại') AS status_title,
                t.name AS tuition_fee_name,
                CONCAT(u.name,' - ', u.hrm_id) AS cm_name, r.renewed_month
            FROM report_renews AS r 
                LEFT JOIN students AS s ON s.id=r.student_id 
                LEFT JOIN users AS u ON u.id=r.cm_id 
                LEFT JOIN branches AS b ON b.id=r.branch_id
                LEFT JOIN products AS p ON p.id=r.product_id
                LEFT JOIN classes AS cl ON cl.id=r.class_id
                LEFT JOIN tuition_fee AS t ON t.id=r.init_renew_tuition_fee_id
            WHERE $cond $order_by");
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Trung tâm');
        $sheet->setCellValue('B1', 'Mã học sinh');
        $sheet->setCellValue('C1', 'Học sinh');
        $sheet->setCellValue('D1', 'Sản phẩm');
        $sheet->setCellValue('E1', 'Lớp học');
        $sheet->setCellValue('F1', 'Ngày đến hạn tái tục');
        $sheet->setCellValue('G1', 'Kết quả');
        $sheet->setCellValue('H1', 'Gói tái phí');
        $sheet->setCellValue('I1', 'Số tiền tái phí');
        $sheet->setCellValue('J1', 'AF');

        $sheet->getColumnDimension("A")->setWidth(30);
        $sheet->getColumnDimension("B")->setWidth(20);
        $sheet->getColumnDimension("C")->setWidth(30);
        $sheet->getColumnDimension("D")->setWidth(30);
        $sheet->getColumnDimension("E")->setWidth(30);
        $sheet->getColumnDimension("F")->setWidth(20);
        $sheet->getColumnDimension("G")->setWidth(30);
        $sheet->getColumnDimension("H")->setWidth(30);
        $sheet->getColumnDimension("I")->setWidth(20);
        $sheet->getColumnDimension("J")->setWidth(20);
        for ($i = 0; $i < count($list); $i++) {
            $x = $i + 2;
            $sheet->setCellValue('A' . $x, $list[$i]->branch_name);
            $sheet->setCellValue('B' . $x, $list[$i]->lms_code);
            $sheet->setCellValue('C' . $x, $list[$i]->student_name);
            $sheet->setCellValue('D' . $x, $list[$i]->product_name);
            $sheet->setCellValue('E' . $x, $list[$i]->class_name);
            $sheet->setCellValue('F' . $x, $list[$i]->last_date);
            $sheet->setCellValue('G' . $x, $list[$i]->status_title);
            $sheet->setCellValue('H' . $x, $list[$i]->status == 1 ? $list[$i]->tuition_fee_name : '');
            $sheet->setCellValue('I' . $x, $list[$i]->status == 1 ? $$list[$i]->renew_amount : '');
            $sheet->setCellValue('J' . $x, $list[$i]->cm_name);
            $sheet->getRowDimension($x)->setRowHeight(23);
        }
        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Báo cáo chi tiết học sinh tái phí.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function report02b(Request $request, $key, $value)
    {
        set_time_limit(300);
        ini_set('memory_limit', '-1');
        $cond = " b.id IN (" . Auth::user()->getBranchesHasUser() . ")";
        $arr_key = explode(',', $key);
        $arr_value = explode(',', $value);
        $start_date = date('Y-m');
        foreach ($arr_key as $k => $key) {
            if ($key == 'start_date') {
                $start_date = $arr_value[$k];
            }
            if ($key == 'branch_id') {
                $cond .= " AND b.id IN (" . str_replace("-", ",", $arr_value[$k]) . ")";
            }
        }

        $renewSql = "SELECT COUNT(r.id) FROM report_renews AS r LEFT JOIN students AS s ON s.id=r.student_id WHERE s.status>0 AND  r.`disabled` = 0 AND r.renewed_month = '$start_date' AND r.branch_id =b.id";
        $order_by = " ORDER BY b.id DESC ";
        $list = u::query("SELECT
                        ($renewSql AND r.status>0) total_item,
                        ($renewSql AND r.status=1) success_item,
                        b.name branch_name
                    FROM branches b
                    WHERE $cond $order_by ");
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Trung tâm');
        $sheet->setCellValue('B1', 'Số học sinh đến hạn tái tục');
        $sheet->setCellValue('C1', 'Học sinh đóng phí tái tục');
        $sheet->setCellValue('D1', 'Tỷ lệ tái tục (%');

        $sheet->getColumnDimension("A")->setWidth(30);
        $sheet->getColumnDimension("B")->setWidth(30);
        $sheet->getColumnDimension("C")->setWidth(30);
        $sheet->getColumnDimension("D")->setWidth(30);
        for ($i = 0; $i < count($list); $i++) {
            $x = $i + 2;
            $sheet->setCellValue('A' . $x, $list[$i]->branch_name);
            $sheet->setCellValue('B' . $x, $list[$i]->total_item);
            $sheet->setCellValue('C' . $x, $list[$i]->success_item);
            $sheet->setCellValue('D' . $x, $list[$i]->total_item ? floor($list[$i]->success_item * 100 / $list[$i]->total_item) : '--');
            $sheet->getRowDimension($x)->setRowHeight(23);
        }
        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Báo cáo tổng hợp học sinh tái phí.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function reportActiveClasses(Request $request, $key, $value)
    {
        set_time_limit(300);
        ini_set('memory_limit', '-1');
        $cond = " c.status = 1 AND c.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";
        $arr_key = explode(',', $key);
        $arr_value = explode(',', $value);
        $status_filter = '';
        foreach ($arr_key as $k => $key) {
            if ($key == 'keyword') {
                $keyword = $arr_value[$k];
                $cond .= " AND (c.cls_name LIKE '%$keyword%') ";
            }
            if ($key == 'branch_id' && $arr_value[$k]) {
                $cond .= " AND c.branch_id IN (" . str_replace("-", ",", $arr_value[$k]) . ")";
            }
            if ($key == 'product_id' && $arr_value[$k]) {
                $cond .= " AND c.product_id = '" . $arr_value[$k] . "'";
            }
            if ($key == 'status' && $arr_value[$k]) {
                $status_filter = $arr_value[$k];
            }
            if ($key == 'start_date' && $arr_value[$k]) {
                $cond .= " AND c.cls_startdate >= '" . $arr_value[$k] . "' ";
            }
            if ($key == 'end_date' && $arr_value[$k]) {
                $cond .= " AND c.cls_startdate <= '" . $arr_value[$k] . "' ";
            }
        }

        $order_by = " ORDER BY REGEXP_REPLACE(c.cls_name, '[0-9]+$', '') DESC, CAST(REGEXP_REPLACE(c.cls_name, '^[^0-9]+', '') AS UNSIGNED) DESC ";

        $having = "";
        if ($status_filter !== '') {
            if ($status_filter == 'THIEU') {
                $having = " AND (c.max_students - total_students) > 0 ";
            } elseif ($status_filter == 'THUA') {
                $having = " AND (c.max_students - total_students) < 0 ";
            } elseif ($status_filter == 'DU') {
                $having = " AND (c.max_students - total_students) = 0 ";
            }
        }

        $query = "SELECT c.id, c.cls_name, c.max_students, c.cls_startdate, c.class_day, c.status, c.is_online,
                    b.name AS branch_name,
                    p.name AS product_name,
                    u_teacher.name AS teacher_name,
                    u_ta.name AS ta_name,
                    (SELECT name FROM shifts WHERE id = (SELECT shift_id FROM sessions WHERE class_id = c.id LIMIT 1)) AS shift_name,
                    (SELECT start_time FROM shifts WHERE id = (SELECT shift_id FROM sessions WHERE class_id = c.id LIMIT 1)) AS start_time,
                    (SELECT end_time FROM shifts WHERE id = (SELECT shift_id FROM sessions WHERE class_id = c.id LIMIT 1)) AS end_time,
                    (SELECT name FROM rooms WHERE id = (SELECT room_id FROM sessions WHERE class_id = c.id LIMIT 1)) AS room_name,
                    (SELECT count(ct.id) FROM contracts ct LEFT JOIN students s ON ct.student_id = s.id WHERE ct.class_id = c.id AND (ct.status != 7 OR c.cls_enddate < CURDATE()) AND s.status > 0) AS total_students
                FROM classes AS c
                    LEFT JOIN branches AS b ON b.id = c.branch_id
                    LEFT JOIN products AS p ON p.id = c.product_id
                    LEFT JOIN users AS u_teacher ON u_teacher.id = c.teacher_id
                    LEFT JOIN users AS u_ta ON u_ta.id = c.ta_id
                WHERE $cond 
                HAVING 1=1 $having
                $ordered_by";

        $list = u::query($query);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        // ── Tiêu đề ──
        $sheet->setCellValue('A1', 'BÁO CÁO LỚP HỌC');
        $sheet->mergeCells('A1:M1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // ── Header row ──
        $hRow = 2;
        $hData = ['A' => 'STT', 'B' => 'Trung tâm', 'C' => 'Mã lớp', 'D' => 'Team/SP', 'E' => 'Sĩ số', 'F' => 'Max', 'G' => 'Trạng thái', 'H' => 'Lịch học', 'I' => 'Khai giảng', 'J' => 'Giáo viên', 'K' => 'Trợ giảng', 'L' => 'Phòng học', 'M' => 'Loại lớp'];
        foreach ($hData as $col => $label) {
            $sheet->setCellValue($col . $hRow, $label);
        }

        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(28);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(9);
        $sheet->getColumnDimension('F')->setWidth(9);
        $sheet->getColumnDimension('G')->setWidth(14);
        $sheet->getColumnDimension('H')->setWidth(24);
        $sheet->getColumnDimension('I')->setWidth(14);
        $sheet->getColumnDimension('J')->setWidth(24);
        $sheet->getColumnDimension('K')->setWidth(24);
        $sheet->getColumnDimension('L')->setWidth(18);
        $sheet->getColumnDimension('M')->setWidth(14);

        $hStyle = [
            'font' => ['bold' => true],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8E8E8']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'BBBBBB']]],
        ];
        $sheet->getStyle('A2:M2')->applyFromArray($hStyle);
        $sheet->getRowDimension(2)->setRowHeight(22);

        $borderOnly = ['borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]]];
        $centerAlign = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]];

        // ── Data rows ──
        for ($i = 0; $i < count($list); $i++) {
            $x = $i + 3;
            $item = $list[$i];

            $item->total_students = (int) $item->total_students;
            $item->max_students = (int) $item->max_students;
            $diff = $item->max_students - $item->total_students;
            $status_text = 'ĐỦ';
            if ($diff > 0) {
                $status_text = 'THIẾU';
            } elseif ($diff < 0) {
                $status_text = 'THỪA';
            }

            $days = [];
            if ($item->class_day) {
                foreach (explode(',', $item->class_day) as $d) {
                    $days[] = "T$d";
                }
            }
            $schedule_text = implode('+', $days);
            if ($item->start_time && $item->end_time) {
                $schedule_text .= ' (' . substr($item->start_time, 0, 5) . '-' . substr($item->end_time, 0, 5) . ')';
            }

            $sheet->setCellValue('A' . $x, $i + 1);
            $sheet->setCellValue('B' . $x, $item->branch_name);
            $sheet->setCellValue('C' . $x, $item->cls_name);
            $sheet->setCellValue('D' . $x, $item->product_name);
            $sheet->setCellValue('E' . $x, $item->total_students);
            $sheet->setCellValue('F' . $x, $item->max_students);
            $sheet->setCellValue('G' . $x, $status_text);
            $sheet->setCellValue('H' . $x, $schedule_text);
            $sheet->setCellValue('I' . $x, date('d/m/Y', strtotime($item->cls_startdate)));
            $sheet->setCellValue('J' . $x, $item->teacher_name);
            $sheet->setCellValue('K' . $x, $item->ta_name);
            $sheet->setCellValue('L' . $x, $item->room_name);
            $sheet->setCellValue('M' . $x, $item->is_online == 1 ? 'Online' : 'Offline');

            $sheet->getStyle("A$x:M$x")->applyFromArray($borderOnly);
            $sheet->getStyle("E$x:G$x")->applyFromArray($centerAlign);
            $sheet->getRowDimension($x)->setRowHeight(20);
        }
        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Bao cao lop hoc.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function report02c(Request $request, $key, $value)
    {
        set_time_limit(300);
        ini_set('memory_limit', '-1');
        $branch_query = Auth::user()->getBranchesHasUser();
        $arr_key = explode(',', $key);
        $arr_value = explode(',', $value);
        $start_date = date('Y-m');
        foreach ($arr_key as $k => $key) {
            if ($key == 'start_date') {
                $start_date = $arr_value[$k];
            }
            if ($key == 'branch_id' && $arr_value[$k]) {
                $branch_query = str_replace("-", ",", $arr_value[$k]);
            }
        }

        $renewSql = "SELECT COUNT(r.id) FROM report_renews AS r LEFT JOIN students AS s ON s.id=r.student_id WHERE s.status>0 AND r.cm_id = ru.user_id AND r.`disabled` = 0 AND r.renewed_month = '$start_date' AND r.branch_id IN ($branch_query)";
        $order_by = " ORDER BY b.id DESC ";
        $list = u::query("SELECT b.name AS branch_name, CONCAT(u.name, ' - ', u.hrm_id )AS cm_name, u.id AS cm_id, b.id AS branch_id,
            (SELECT ro.`name` FROM roles AS ro WHERE ru.role_id = ro.id LIMIT 1 ) role_name,
            ($renewSql AND r.status >0) total_item,
            ($renewSql AND r.status=1) success_item
            FROM users AS u 
                LEFT JOIN role_has_user AS ru ON u.id=ru.user_id
                LEFT JOIN branch_has_user AS bu ON bu.user_id=ru.user_id
                LEFT JOIN branches AS b ON b.id=bu.branch_id
            WHERE ru.role_id IN (55,56) AND (u.status =1 OR (u.status=0 AND (SELECT COUNT(id) FROM report_renews WHERE cm_id = ru.user_id AND `status` > 0 AND `disabled` = 0 AND renewed_month = '$start_date' AND branch_id IN ($branch_query))>0)) $order_by");
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Trung tâm');
        $sheet->setCellValue('B1', 'Nhân viên');
        $sheet->setCellValue('C1', 'Chức Danh');
        $sheet->setCellValue('D1', 'Số học sinh đến hạn tái tục');
        $sheet->setCellValue('E1', 'Học sinh đóng phí tái tục');
        $sheet->setCellValue('F1', 'Tỷ lệ tái tục (%');

        $sheet->getColumnDimension("A")->setWidth(30);
        $sheet->getColumnDimension("B")->setWidth(30);
        $sheet->getColumnDimension("C")->setWidth(30);
        $sheet->getColumnDimension("D")->setWidth(30);
        $sheet->getColumnDimension("E")->setWidth(30);
        $sheet->getColumnDimension("F")->setWidth(30);
        for ($i = 0; $i < count($list); $i++) {
            $x = $i + 2;
            $sheet->setCellValue('A' . $x, $list[$i]->branch_name);
            $sheet->setCellValue('B' . $x, $list[$i]->cm_name);
            $sheet->setCellValue('C' . $x, $list[$i]->role_name);
            $sheet->setCellValue('D' . $x, $list[$i]->total_item);
            $sheet->setCellValue('E' . $x, $list[$i]->success_item);
            $sheet->setCellValue('F' . $x, $list[$i]->total_item ? floor($list[$i]->success_item * 100 / $list[$i]->total_item) : '--');
            $sheet->getRowDimension($x)->setRowHeight(23);
        }
        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Báo cáo học sinh tái phí theo EC.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function report13(Request $request, $key, $value)
    {
        set_time_limit(300);
        ini_set('memory_limit', '-1');

        $arr_key = explode(',', $key);
        $arr_value = explode(',', $value);

        $branch_id = [];
        $keyword = '';
        $start_date = '';
        $end_date = '';

        foreach ($arr_key as $k => $key_name) {
            if ($key_name == 'branch_id' && $arr_value[$k] && $arr_value[$k] != 'v') {
                $branch_id = explode('-', $arr_value[$k]);
            }
            if ($key_name == 'keyword' && $arr_value[$k] && $arr_value[$k] != 'v') {
                $keyword = $arr_value[$k];
            }
            if ($key_name == 'start_date' && $arr_value[$k] && $arr_value[$k] != 'v') {
                $start_date = $arr_value[$k];
            }
            if ($key_name == 'end_date' && $arr_value[$k] && $arr_value[$k] != 'v') {
                $end_date = $arr_value[$k];
            }
        }

        $cond = " c.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";

        if (!empty($branch_id)) {
            $cond .= " AND c.branch_id IN (" . implode(",", $branch_id) . ")";
        }

        if ($keyword !== '') {
            $cond .= " AND (st.lms_code LIKE '%$keyword%' OR st.name LIKE '%$keyword%') ";
        }

        if ($start_date) {
            $cond .= " AND sks.class_date >= '$start_date'";
        }
        if ($end_date) {
            $cond .= " AND sks.class_date <= '$end_date'";
        }

        $order_by = " ORDER BY sks.class_date DESC, st.name ASC ";

        $query = "SELECT
                    b.name AS branch_name,
                    st.lms_code,
                    st.name AS student_name,
                    c.cls_name AS class_name,
                    sks.class_date,
                    sks.status,
                    (ct.must_charge / ct.summary_sessions) AS session_value
                   FROM schedule_has_student AS sks
                   JOIN classes AS c ON sks.class_id = c.id
                   JOIN branches AS b ON c.branch_id = b.id
                   JOIN students AS st ON sks.student_id = st.id
                   JOIN contracts AS ct ON sks.contract_id = ct.id
                   WHERE $cond
                   $order_by";

        $list = u::query($query);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet->setCellValue('A1', 'BÁO CÁO DOANH SỐ CHI TIẾT HỌC SINH ĐI HỌC');
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->applyFromArray(['font' => ['bold' => true, 'size' => 14], 'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        foreach (['A2' => 'STT', 'B2' => 'Trung tâm', 'C2' => 'Mã HS', 'D2' => 'Tên HS', 'E2' => 'Lớp học', 'F2' => 'Ngày học', 'G2' => 'Doanh số (VNĐ)'] as $c => $l) {
            $sheet->setCellValue($c, $l);
        }
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(28);
        $sheet->getColumnDimension('C')->setWidth(16);
        $sheet->getColumnDimension('D')->setWidth(28);
        $sheet->getColumnDimension('E')->setWidth(26);
        $sheet->getColumnDimension('F')->setWidth(14);
        $sheet->getColumnDimension('G')->setWidth(20);

        $hStyle = ['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8E8E8']], 'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER], 'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'BBBBBB']]]];
        $sheet->getStyle('A2:G2')->applyFromArray($hStyle);
        $sheet->getRowDimension(2)->setRowHeight(22);

        $borderOnly = ['borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]]];
        $centerAlign = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]];
        $rightAlign = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT]];
        $totalRevenue = 0;

        for ($i = 0; $i < count($list); $i++) {
            $x = $i + 3;
            $item = $list[$i];
            $revenue = $item->session_value ?? 0;
            $sheet->setCellValue('A' . $x, $i + 1);
            $sheet->setCellValue('B' . $x, $item->branch_name);
            $sheet->setCellValue('C' . $x, $item->lms_code);
            $sheet->setCellValue('D' . $x, $item->student_name);
            $sheet->setCellValue('E' . $x, $item->class_name);
            $sheet->setCellValue('F' . $x, $item->class_date);
            $sheet->setCellValue('G' . $x, $revenue);
            $totalRevenue += $revenue;
            $sheet->getStyle("A$x:G$x")->applyFromArray($borderOnly);
            $sheet->getStyle("A$x")->applyFromArray($centerAlign);
            $sheet->getStyle("F$x")->applyFromArray($centerAlign);
            $sheet->getStyle("G$x")->applyFromArray($rightAlign);
            $sheet->getStyle("G$x")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getRowDimension($x)->setRowHeight(20);
        }
        // Tổng cộng
        $tRow = count($list) + 3;
        $sheet->mergeCells("A$tRow:F$tRow");
        $sheet->setCellValue('A' . $tRow, 'TỔNG CỘNG');
        $sheet->setCellValue('G' . $tRow, $totalRevenue);
        $tStyle = ['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0F0F0']], 'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER], 'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'AAAAAA']]]];
        $sheet->getStyle("A$tRow:G$tRow")->applyFromArray($tStyle);
        $sheet->getStyle("G$tRow")->applyFromArray($rightAlign);
        $sheet->getStyle("G$tRow")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getRowDimension($tRow)->setRowHeight(22);

        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Bao cao doanh so chi tiet.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }


    public function report14(Request $request, $key, $value)
    {
        set_time_limit(300);
        ini_set('memory_limit', '-1');

        $keys = explode(',', $key);
        $values = explode(',', $value);
        $params = array_combine($keys, $values);

        $start_date = isset($params['start_date']) ? $params['start_date'] : '';
        $end_date = isset($params['end_date']) ? $params['end_date'] : '';

        // Get all branches accessible by user
        $branchIds = Auth::user()->getBranchesHasUser();

        // Build condition for date filtering
        $dateCond = " sks.status=1 ";
        if ($start_date) {
            $dateCond .= " AND sks.class_date >= '$start_date'";
        }
        if ($end_date) {
            $dateCond .= " AND sks.class_date <= '$end_date'";
        }

        // Query to get all branches with their revenue data (including branches with 0 revenue)
        $query = "SELECT
                    b.id AS branch_id,
                    b.name AS branch_name,
                    COALESCE(COUNT(DISTINCT sks.student_id), 0) AS total_students,
                    COALESCE(COUNT(sks.id), 0) AS total_sessions,
                    COALESCE(SUM(ct.must_charge / ct.summary_sessions), 0) AS total_revenue
                   FROM branches AS b
                   LEFT JOIN classes AS c ON b.id = c.branch_id
                   LEFT JOIN schedule_has_student AS sks ON c.id = sks.class_id AND $dateCond
                   LEFT JOIN contracts AS ct ON sks.contract_id = ct.id
                   WHERE b.id IN ($branchIds)
                   GROUP BY b.id, b.name
                   ORDER BY total_revenue DESC";

        $list = u::query($query);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet->setCellValue('A1', 'BÁO CÁO TỔNG QUAN DOANH THU HỌC SINH ĐI HỌC THEO TRUNG TÂM');
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->applyFromArray(['font' => ['bold' => true, 'size' => 14], 'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        foreach (['A2' => 'STT', 'B2' => 'Trung tâm', 'C2' => 'Tổng số HS', 'D2' => 'Số buổi học', 'E2' => 'Doanh thu (VNĐ)'] as $c => $l) {
            $sheet->setCellValue($c, $l);
        }
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(36);
        $sheet->getColumnDimension('C')->setWidth(16);
        $sheet->getColumnDimension('D')->setWidth(16);
        $sheet->getColumnDimension('E')->setWidth(22);

        $hStyle = ['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8E8E8']], 'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER], 'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'BBBBBB']]]];
        $sheet->getStyle('A2:E2')->applyFromArray($hStyle);
        $sheet->getRowDimension(2)->setRowHeight(22);

        $borderOnly = ['borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]]];
        $centerAlign = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]];
        $rightAlign = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT]];

        // Fill data
        $totalRevenue = 0;
        $totalStudents = 0;
        $totalSessions = 0;

        for ($i = 0; $i < count($list); $i++) {
            $x = $i + 3;
            $item = $list[$i];
            $sheet->setCellValue('A' . $x, $i + 1);
            $sheet->setCellValue('B' . $x, $item->branch_name);
            $sheet->setCellValue('C' . $x, $item->total_students);
            $sheet->setCellValue('D' . $x, $item->total_sessions);
            $sheet->setCellValue('E' . $x, $item->total_revenue);
            $totalRevenue += $item->total_revenue;
            $totalStudents += $item->total_students;
            $totalSessions += $item->total_sessions;
            $sheet->getStyle("A$x:E$x")->applyFromArray($borderOnly);
            $sheet->getStyle("A$x:D$x")->applyFromArray($centerAlign);
            $sheet->getStyle("E$x")->applyFromArray($rightAlign);
            $sheet->getStyle("E$x")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getRowDimension($x)->setRowHeight(20);
        }

        $tRow = count($list) + 3;
        $sheet->mergeCells("A$tRow:B$tRow");
        $sheet->setCellValue('A' . $tRow, 'TỔNG CỘNG');
        $sheet->setCellValue('C' . $tRow, $totalStudents);
        $sheet->setCellValue('D' . $tRow, $totalSessions);
        $sheet->setCellValue('E' . $tRow, $totalRevenue);
        $tStyle = ['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0F0F0']], 'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER], 'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'AAAAAA']]]];
        $sheet->getStyle("A$tRow:E$tRow")->applyFromArray($tStyle);
        $sheet->getStyle("E$tRow")->applyFromArray($rightAlign);
        $sheet->getStyle("E$tRow")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getRowDimension($tRow)->setRowHeight(22);

        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Bao cao doanh thu theo trung tam.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function report15(Request $request, $key, $value)
    {
        set_time_limit(300);
        ini_set('memory_limit', '-1');

        $keys = explode(',', $key);
        $values = explode(',', $value);
        $params = array_combine($keys, $values);

        $branch_id = [];
        $product_id = [];
        $status = [];
        $keyword = '';
        $start_date = '';
        $end_date = '';

        foreach ($keys as $k => $key_name) {
            if ($key_name == 'branch_id' && isset($values[$k]) && $values[$k] != 'v') {
                $branch_id = explode('-', $values[$k]);
            }
            if ($key_name == 'product_id' && isset($values[$k]) && $values[$k] != 'v') {
                $product_id = explode('-', $values[$k]);
            }
            if ($key_name == 'status' && isset($values[$k]) && $values[$k] != 'v') {
                $status = explode('-', $values[$k]);
            }
            if ($key_name == 'keyword' && isset($values[$k]) && $values[$k] != 'v') {
                $keyword = $values[$k];
            }
            if ($key_name == 'start_date' && isset($values[$k]) && $values[$k] != 'v') {
                $start_date = $values[$k];
            }
            if ($key_name == 'end_date' && isset($values[$k]) && $values[$k] != 'v') {
                $end_date = $values[$k];
            }
        }

        // Build conditions
        $cond = " c.status NOT IN (0,1,7,8) AND c.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";

        if (!empty($branch_id)) {
            $cond .= " AND c.branch_id IN (" . implode(",", $branch_id) . ")";
        }

        if (!empty($product_id)) {
            $cond .= " AND c.product_id IN (" . implode(",", $product_id) . ")";
        }

        if (!empty($status)) {
            $cond .= " AND c.status IN (" . implode(",", $status) . ")";
        }

        if ($keyword !== '') {
            $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%' OR c.code LIKE '%$keyword%' OR s.gud_mobile1 LIKE '%$keyword%') ";
        }

        if ($start_date) {
            $cond .= " AND c.created_at >= '$start_date'";
        }
        if ($end_date) {
            $cond .= " AND c.created_at <= '$end_date 23:59:59'";
        }

        $order_by = " ORDER BY c.id DESC ";

        // Query contracts
        $query = "SELECT 
                c.id AS contract_id,
                c.code AS contract_code,
                c.status,
                c.summary_sessions,
                c.done_sessions,
                c.left_sessions,
                c.total_charged,
                c.must_charge,
                c.debt_amount,
                c.created_at,
                s.lms_code,
                s.name AS student_name,
                s.gud_mobile1,
                b.name AS branch_name,
                p.name AS product_name,
                cl.cls_name AS class_name,
                t.name AS tuition_fee_name,
                CONCAT(u_ec.name, ' - ', u_ec.hrm_id) AS ec_name,
                CONCAT(u_ecl.name, ' - ', u_ecl.hrm_id) AS ec_leader_name,
                CONCAT(u_cm.name, ' - ', u_cm.hrm_id) AS cm_name,
                CASE 
                    WHEN c.summary_sessions > 0 THEN 
                        ROUND((c.total_charged * c.left_sessions / c.summary_sessions), 0)
                    ELSE 0
                END AS left_amount
            FROM contracts AS c
                LEFT JOIN students AS s ON s.id = c.student_id
                LEFT JOIN branches AS b ON b.id = c.branch_id
                LEFT JOIN products AS p ON p.id = c.product_id
                LEFT JOIN classes AS cl ON cl.id = c.class_id
                LEFT JOIN tuition_fee AS t ON t.id = c.tuition_fee_id
                LEFT JOIN users AS u_ec ON u_ec.id = c.ec_id
                LEFT JOIN users AS u_ecl ON u_ecl.id = c.ec_leader_id
                LEFT JOIN users AS u_cm ON u_cm.id = c.cm_id
            WHERE $cond
            $order_by";

        $list = u::query($query);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet->setCellValue('A1', 'BÁO CÁO DOANH THU CHƪa PHÂN BỔ THEO HỌC SINH');
        $sheet->mergeCells('A1:T1');
        $sheet->getStyle('A1')->applyFromArray(['font' => ['bold' => true, 'size' => 14], 'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $hCols = ['A2' => 'STT', 'B2' => 'Mã HS', 'C2' => 'Tên HS', 'D2' => 'SĐT', 'E2' => 'Mã Contract', 'F2' => 'Trạng thái', 'G2' => 'Trung tâm', 'H2' => 'Sản phẩm', 'I2' => 'Lớp học', 'J2' => 'Gói phí', 'K2' => 'EC', 'L2' => 'EC Leader', 'M2' => 'CM', 'N2' => 'Tổng buổi', 'O2' => 'Đã học', 'P2' => 'Còn lại', 'Q2' => 'Phải đóng', 'R2' => 'Đã đóng', 'S2' => 'Nợ', 'T2' => 'Số tiền còn lại'];
        foreach ($hCols as $c => $l) {
            $sheet->setCellValue($c, $l);
        }

        $widths = ['A' => 8, 'B' => 14, 'C' => 24, 'D' => 14, 'E' => 14, 'F' => 14, 'G' => 22, 'H' => 18, 'I' => 22, 'J' => 22, 'K' => 22, 'L' => 22, 'M' => 22, 'N' => 11, 'O' => 11, 'P' => 11, 'Q' => 16, 'R' => 16, 'S' => 16, 'T' => 18];
        foreach ($widths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        $hStyle = ['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8E8E8']], 'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER], 'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'BBBBBB']]]];
        $sheet->getStyle('A2:S2')->applyFromArray($hStyle);
        $sheet->getRowDimension(2)->setRowHeight(22);

        $borderOnly = ['borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]]];
        $centerAlign = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]];
        $rightAlign = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT]];

        $totalLeftAmount = 0;
        $totalCharged = 0;
        $totalMustCharge = 0;
        $totalDebt = 0;
        $totalLeftSessions = 0;
        $totalSummarySessions = 0;

        for ($i = 0; $i < count($list); $i++) {
            $x = $i + 3;
            $item = $list[$i];
            $statusLabel = u::geLabelStatusContract($item->status);

            $sheet->setCellValue('A' . $x, $i + 1);
            $sheet->setCellValue('B' . $x, $item->lms_code);
            $sheet->setCellValue('C' . $x, $item->student_name);
            $sheet->setCellValue('D' . $x, $item->gud_mobile1 ? "'" . $item->gud_mobile1 : '');
            $sheet->setCellValue('E' . $x, $item->contract_code);
            $sheet->setCellValue('F' . $x, $statusLabel);
            $sheet->setCellValue('G' . $x, $item->branch_name);
            $sheet->setCellValue('H' . $x, $item->product_name);
            $sheet->setCellValue('I' . $x, $item->class_name ?: 'Chưa xếp lớp');
            $sheet->setCellValue('J' . $x, $item->tuition_fee_name);
            $sheet->setCellValue('K' . $x, $item->ec_name);
            $sheet->setCellValue('L' . $x, $item->ec_leader_name);
            $sheet->setCellValue('M' . $x, $item->cm_name);
            $sheet->setCellValue('N' . $x, $item->summary_sessions);
            $sheet->setCellValue('O' . $x, $item->done_sessions);
            $sheet->setCellValue('P' . $x, $item->left_sessions);
            $sheet->setCellValue('Q' . $x, $item->must_charge);
            $sheet->setCellValue('R' . $x, $item->total_charged);
            $sheet->setCellValue('S' . $x, $item->debt_amount);
            $sheet->setCellValue('T' . $x, $item->left_amount);

            $totalLeftAmount += $item->left_amount;
            $totalCharged += $item->total_charged;
            $totalMustCharge += $item->must_charge;
            $totalDebt += $item->debt_amount;
            $totalLeftSessions += $item->left_sessions;
            $totalSummarySessions += $item->summary_sessions;

            $sheet->getStyle("A$x:T$x")->applyFromArray($borderOnly);
            $sheet->getStyle("A$x,N$x:P$x")->applyFromArray($centerAlign);
            $sheet->getStyle("Q$x:T$x")->applyFromArray($rightAlign);
            foreach (['Q', 'R', 'S', 'T'] as $mc) {
                $sheet->getStyle("$mc$x")->getNumberFormat()->setFormatCode('#,##0');
            }
            $sheet->getRowDimension($x)->setRowHeight(20);
        }

        $tRow = count($list) + 3;
        $sheet->mergeCells("A$tRow:M$tRow");
        $sheet->setCellValue('A' . $tRow, 'TỔNG CỘNG');
        $sheet->setCellValue('N' . $tRow, $totalSummarySessions);
        $sheet->setCellValue('O' . $tRow, '');
        $sheet->setCellValue('P' . $tRow, $totalLeftSessions);
        $sheet->setCellValue('Q' . $tRow, $totalMustCharge);
        $sheet->setCellValue('R' . $tRow, $totalCharged);
        $sheet->setCellValue('S' . $tRow, $totalDebt);
        $sheet->setCellValue('T' . $tRow, $totalLeftAmount);
        $tStyle = ['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0F0F0']], 'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER], 'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'AAAAAA']]]];
        $sheet->getStyle("A$tRow:T$tRow")->applyFromArray($tStyle);
        $sheet->getStyle("Q$tRow:T$tRow")->applyFromArray($rightAlign);
        foreach (['Q', 'R', 'S', 'T'] as $mc) {
            $sheet->getStyle("$mc$tRow")->getNumberFormat()->setFormatCode('#,##0');
        }
        $sheet->getRowDimension($tRow)->setRowHeight(22);

        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Bao cao doanh thu chua phan bo theo HS.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function report17(Request $request, $key, $value)
    {
        set_time_limit(300);
        ini_set('memory_limit', '-1');

        $keys = explode(',', $key);
        $values = explode(',', $value);
        $params = array_combine($keys, $values);

        $branch_id = [];
        $school_year = '';
        $keyword = '';
        $start_date = '';
        $end_date = '';

        foreach ($keys as $k => $key_name) {
            if ($key_name == 'branch_id' && isset($values[$k]) && $values[$k] != 'v') {
                $branch_id = explode('-', $values[$k]);
            }
            if ($key_name == 'school_year' && isset($values[$k]) && $values[$k] != 'v') {
                $school_year = $values[$k];
            }
            if ($key_name == 'keyword' && isset($values[$k]) && $values[$k] != 'v') {
                $keyword = $values[$k];
            }
            if ($key_name == 'start_date' && isset($values[$k]) && $values[$k] != 'v') {
                $start_date = $values[$k];
            }
            if ($key_name == 'end_date' && isset($values[$k]) && $values[$k] != 'v') {
                $end_date = $values[$k];
            }
        }

        // Điều kiện cơ bản: Lấy agreements có debt_amount = 0 và theo branch map
        $cond = " a.debt_amount = 0 AND a.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";

        if (!empty($branch_id)) {
            $cond .= " AND a.branch_id IN (" . implode(",", $branch_id) . ")";
        }

        if ($keyword !== '') {
            $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%' OR s.gud_mobile1 LIKE '%$keyword%') ";
        }

        if ($start_date) {
            $cond .= " AND a.id IN (SELECT agreement_id FROM payments WHERE debt = 0 AND charge_date >= '$start_date')";
        }
        if ($end_date) {
            $cond .= " AND a.id IN (SELECT agreement_id FROM payments WHERE debt = 0 AND charge_date <= '$end_date 23:59:59')";
        }

        $order_by = " ORDER BY a.id DESC ";

        // Lọc session school year thông qua join để lấy giá trị động
        $schedule_cond = " c.id = shs.contract_id AND shs.status = 1 ";
        if ($school_year) {
            $schedule_cond .= " AND YEAR(shs.class_date) = '" . $school_year . "' ";
        }

        // Query danh sách agreements (combo packages) có debt = 0 và lấy full fee_date (max charge_date)
        $query = "SELECT 
                a.id AS agreement_id,
                a.must_charge AS combo_fee,
                s.lms_code AS student_code,
                s.name AS student_name,
                t.name AS combo_name,
                COUNT(DISTINCT c.id) AS total_courses,
                MIN(c.enrolment_start_date) AS first_course_start_date,
                (SELECT charge_date FROM payments WHERE agreement_id = a.id AND debt = 0 ORDER BY charge_date DESC LIMIT 1) AS full_fee_date,
                SUM(c.summary_sessions) AS total_sessions,
                (SELECT COUNT(shs.id) FROM schedule_has_student shs INNER JOIN contracts c_inner ON c_inner.id = shs.contract_id WHERE c_inner.agreement_id = a.id AND shs.status = 1 " . ($school_year ? " AND YEAR(shs.class_date) = '$school_year'" : "") . ") AS done_sessions
            FROM agreements AS a
                LEFT JOIN students AS s ON s.id = a.student_id
                LEFT JOIN tuition_fee AS t ON t.id = a.tuition_fee_id
                LEFT JOIN contracts AS c ON c.agreement_id = a.id AND c.status NOT IN (0,1,7,8)
            WHERE $cond
            GROUP BY a.id, a.must_charge, s.lms_code, s.name, t.name, a.id
            $order_by";

        $list = u::query($query);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet->setCellValue('A1', 'BÁO CÁO TỔNG HỢP TIẾN ĐỘ HỌC SAU 01 NĂM');
        $sheet->mergeCells('A1:N1');
        $sheet->getStyle('A1')->applyFromArray(['font' => ['bold' => true, 'size' => 14], 'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $hCols = ['A2' => 'STT', 'B2' => 'Mã HV', 'C2' => 'Họ tên', 'D2' => 'Combo đăng ký', 'E2' => 'Số khóa', 'F2' => 'Ngày bắt đầu', 'G2' => 'Ngày full fee', 'H2' => 'Tổng buổi', 'I2' => 'Học phí (VNĐ)', 'J2' => 'Đã học', 'K2' => '% HT', 'L2' => 'Còn lại', 'M2' => 'GTrij đã dùng', 'N2' => 'Giá trị còn lại'];
        foreach ($hCols as $c => $l) {
            $sheet->setCellValue($c, $l);
        }

        $widths = ['A' => 8, 'B' => 14, 'C' => 24, 'D' => 24, 'E' => 12, 'F' => 22, 'G' => 18, 'H' => 16, 'I' => 20, 'J' => 16, 'K' => 12, 'L' => 14, 'M' => 22, 'N' => 22];
        foreach ($widths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        $hStyle = ['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8E8E8']], 'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER], 'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'BBBBBB']]]];
        $sheet->getStyle('A2:N2')->applyFromArray($hStyle);
        $sheet->getRowDimension(2)->setRowHeight(22);

        $borderOnly = ['borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]]];
        $centerAlign = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]];
        $rightAlign = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT]];

        $total_combo_fee_all = 0;
        $total_sessions_all = 0;
        $total_done_sessions_all = 0;
        $total_used_value_all = 0;
        $total_left_value_all = 0;
        $total_left_sessions_all = 0;


        for ($i = 0; $i < count($list); $i++) {
            $x = $i + 3;
            $item = $list[$i];

            $item->total_sessions = (int) $item->total_sessions;
            $item->done_sessions = (int) $item->done_sessions;
            $item->left_sessions = max(0, $item->total_sessions - $item->done_sessions);
            $item->combo_fee = (float) $item->combo_fee;

            if ($item->total_sessions > 0) {
                $item->used_value = round(($item->combo_fee * $item->done_sessions) / $item->total_sessions, 0);
                $item->completion_rate = round(($item->done_sessions / $item->total_sessions) * 100, 2);
            } else {
                $item->used_value = 0;
                $item->completion_rate = 0;
            }

            $item->left_value = max(0, $item->combo_fee - $item->used_value);

            $sheet->setCellValue('A' . $x, $i + 1);
            $sheet->setCellValue('B' . $x, $item->student_code);
            $sheet->setCellValue('C' . $x, $item->student_name);
            $sheet->setCellValue('D' . $x, $item->combo_name);
            $sheet->setCellValue('E' . $x, $item->total_courses);
            $sheet->setCellValue('F' . $x, $item->first_course_start_date);
            $sheet->setCellValue('G' . $x, $item->full_fee_date);
            $sheet->setCellValue('H' . $x, $item->total_sessions);
            $sheet->setCellValue('I' . $x, $item->combo_fee);
            $sheet->setCellValue('J' . $x, $item->done_sessions);
            $sheet->setCellValue('K' . $x, $item->completion_rate . '%');
            $sheet->setCellValue('L' . $x, $item->left_sessions);
            $sheet->setCellValue('M' . $x, $item->used_value);
            $sheet->setCellValue('N' . $x, $item->left_value);

            $total_combo_fee_all += $item->combo_fee;
            $total_sessions_all += $item->total_sessions;
            $total_done_sessions_all += $item->done_sessions;
            $total_used_value_all += $item->used_value;
            $total_left_value_all += $item->left_value;
            $total_left_sessions_all += $item->left_sessions;

            $sheet->getStyle("A$x:N$x")->applyFromArray($borderOnly);
            $sheet->getStyle("A$x,E$x:L$x")->applyFromArray($centerAlign);
            $sheet->getStyle("I$x,M$x:N$x")->applyFromArray($rightAlign);
            foreach (['I', 'M', 'N'] as $mc) {
                $sheet->getStyle("$mc$x")->getNumberFormat()->setFormatCode('#,##0');
            }
            $sheet->getRowDimension($x)->setRowHeight(20);
        }

        $tRow = count($list) + 3;
        $sheet->mergeCells("A$tRow:G$tRow");
        $sheet->setCellValue('A' . $tRow, 'TỔNG CỘNG');
        $sheet->setCellValue('H' . $tRow, $total_sessions_all);
        $sheet->setCellValue('I' . $tRow, $total_combo_fee_all);
        $sheet->setCellValue('J' . $tRow, $total_done_sessions_all);
        $sheet->setCellValue('K' . $tRow, '');
        $sheet->setCellValue('L' . $tRow, $total_left_sessions_all);
        $sheet->setCellValue('M' . $tRow, $total_used_value_all);
        $sheet->setCellValue('N' . $tRow, $total_left_value_all);
        $tStyle = ['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0F0F0']], 'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER], 'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'AAAAAA']]]];
        $sheet->getStyle("A$tRow:N$tRow")->applyFromArray($tStyle);
        $sheet->getStyle("I$tRow,M$tRow:N$tRow")->applyFromArray($rightAlign);
        foreach (['I', 'M', 'N'] as $mc) {
            $sheet->getStyle("$mc$tRow")->getNumberFormat()->setFormatCode('#,##0');
        }
        $sheet->getRowDimension($tRow)->setRowHeight(22);

        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Bao cao tien do hoc sau 01 nam.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function report16(Request $request, $key, $value)
    {
        set_time_limit(300);
        ini_set('memory_limit', '-1');

        $keys = explode(',', $key);
        $values = explode(',', $value);
        $params = array_combine($keys, $values);

        $branch_id = [];
        $product_id = [];
        $status = [];
        $start_date = '';
        $end_date = '';

        foreach ($keys as $k => $key_name) {
            if ($key_name == 'branch_id' && isset($values[$k]) && $values[$k] != 'v') {
                $branch_id = explode('-', $values[$k]);
            }
            if ($key_name == 'product_id' && isset($values[$k]) && $values[$k] != 'v') {
                $product_id = explode('-', $values[$k]);
            }
            if ($key_name == 'status' && isset($values[$k]) && $values[$k] != 'v') {
                $status = explode('-', $values[$k]);
            }
            if ($key_name == 'start_date' && isset($values[$k]) && $values[$k] != 'v') {
                $start_date = $values[$k];
            }
            if ($key_name == 'end_date' && isset($values[$k]) && $values[$k] != 'v') {
                $end_date = $values[$k];
            }
        }

        // Get all branches accessible by user
        $branchIdsQuery = Auth::user()->getBranchesHasUser();

        // Build condition for JOIN
        $joinCond = " c.status NOT IN (0,1,7,8) ";
        if (!empty($product_id)) {
            $joinCond .= " AND c.product_id IN (" . implode(",", $product_id) . ")";
        }
        if (!empty($status)) {
            $joinCond .= " AND c.status IN (" . implode(",", $status) . ")";
        }
        if ($start_date) {
            $joinCond .= " AND c.created_at >= '$start_date'";
        }
        if ($end_date) {
            $joinCond .= " AND c.created_at <= '$end_date 23:59:59'";
        }

        // Branch filter
        $branchCond = " b.id IN ($branchIdsQuery) ";
        if (!empty($branch_id)) {
            $branchCond .= " AND b.id IN (" . implode(",", $branch_id) . ")";
        }

        $query = "SELECT 
                    b.id AS branch_id,
                    b.name AS branch_name,
                    COUNT(c.id) AS total_contracts,
                    SUM(COALESCE(c.must_charge, 0)) AS total_must_charge,
                    SUM(COALESCE(c.total_charged, 0)) AS total_charged,
                    SUM(COALESCE(c.debt_amount, 0)) AS total_debt_amount,
                    SUM(
                        CASE 
                            WHEN c.summary_sessions > 0 THEN 
                                ROUND((c.total_charged * c.left_sessions / c.summary_sessions), 0)
                            ELSE 0
                        END
                    ) AS total_left_amount
                   FROM branches AS b
                   LEFT JOIN contracts AS c ON b.id = c.branch_id AND $joinCond
                   WHERE $branchCond
                   GROUP BY b.id, b.name
                   ORDER BY total_left_amount DESC";

        $list = u::query($query);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet->setCellValue('A1', 'BÁO CÁO DOANH THU CHƪa PHÂN BỔ THEO TRUNG TÂM');
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->applyFromArray(['font' => ['bold' => true, 'size' => 14], 'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        foreach (['A2' => 'STT', 'B2' => 'Trung tâm', 'C2' => 'Tổng contracts', 'D2' => 'Tổng phải đóng', 'E2' => 'Tổng đã đóng', 'F2' => 'Tổng nợ', 'G2' => 'Tổng số tiền còn lại'] as $c => $l) {
            $sheet->setCellValue($c, $l);
        }
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(32);
        $sheet->getColumnDimension('C')->setWidth(16);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(16);
        $sheet->getColumnDimension('G')->setWidth(22);

        $hStyle = ['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8E8E8']], 'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER], 'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'BBBBBB']]]];
        $sheet->getStyle('A2:G2')->applyFromArray($hStyle);
        $sheet->getRowDimension(2)->setRowHeight(22);

        $borderOnly = ['borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]]];
        $centerAlign = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]];
        $rightAlign = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT]];

        $totalContracts = 0;
        $totalMustCharge = 0;
        $totalCharged = 0;
        $totalDebt = 0;
        $totalLeftAmount = 0;

        for ($i = 0; $i < count($list); $i++) {
            $x = $i + 3;
            $item = $list[$i];
            $sheet->setCellValue('A' . $x, $i + 1);
            $sheet->setCellValue('B' . $x, $item->branch_name);
            $sheet->setCellValue('C' . $x, $item->total_contracts);
            $sheet->setCellValue('D' . $x, $item->total_must_charge);
            $sheet->setCellValue('E' . $x, $item->total_charged);
            $sheet->setCellValue('F' . $x, $item->total_debt_amount);
            $sheet->setCellValue('G' . $x, $item->total_left_amount);
            $totalContracts += $item->total_contracts;
            $totalMustCharge += $item->total_must_charge;
            $totalCharged += $item->total_charged;
            $totalDebt += $item->total_debt_amount;
            $totalLeftAmount += $item->total_left_amount;
            $sheet->getStyle("A$x:G$x")->applyFromArray($borderOnly);
            $sheet->getStyle("A$x:C$x")->applyFromArray($centerAlign);
            $sheet->getStyle("D$x:G$x")->applyFromArray($rightAlign);
            foreach (['D', 'E', 'F', 'G'] as $mc) {
                $sheet->getStyle("$mc$x")->getNumberFormat()->setFormatCode('#,##0');
            }
            $sheet->getRowDimension($x)->setRowHeight(20);
        }

        $tRow = count($list) + 3;
        $sheet->mergeCells("A$tRow:B$tRow");
        $sheet->setCellValue('A' . $tRow, 'TỔNG CỘNG');
        $sheet->setCellValue('C' . $tRow, $totalContracts);
        $sheet->setCellValue('D' . $tRow, $totalMustCharge);
        $sheet->setCellValue('E' . $tRow, $totalCharged);
        $sheet->setCellValue('F' . $tRow, $totalDebt);
        $sheet->setCellValue('G' . $tRow, $totalLeftAmount);
        $tStyle = ['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0F0F0']], 'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER], 'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'AAAAAA']]]];
        $sheet->getStyle("A$tRow:G$tRow")->applyFromArray($tStyle);
        $sheet->getStyle("D$tRow:G$tRow")->applyFromArray($rightAlign);
        foreach (['D', 'E', 'F', 'G'] as $mc) {
            $sheet->getStyle("$mc$tRow")->getNumberFormat()->setFormatCode('#,##0');
        }
        $sheet->getRowDimension($tRow)->setRowHeight(22);

        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Bao cao doanh thu chua phan bo theo TT.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function report18(Request $request, $key, $value)
    {
        set_time_limit(300);
        ini_set('memory_limit', '-1');

        $keys = explode(',', $key);
        $values = explode(',', $value);
        $params = array_combine($keys, $values);

        $branch_id = [];
        $school_year = '';
        $keyword = '';
        $start_date = '';
        $end_date = '';

        foreach ($keys as $k => $key_name) {
            if ($key_name == 'branch_id' && isset($values[$k]) && $values[$k] != 'v') {
                $branch_id = explode('-', $values[$k]);
            }
            if ($key_name == 'school_year' && isset($values[$k]) && $values[$k] != 'v') {
                $school_year = $values[$k];
            }
            if ($key_name == 'keyword' && isset($values[$k]) && $values[$k] != 'v') {
                $keyword = $values[$k];
            }
            if ($key_name == 'start_date' && isset($values[$k]) && $values[$k] != 'v') {
                $start_date = $values[$k];
            }
            if ($key_name == 'end_date' && isset($values[$k]) && $values[$k] != 'v') {
                $end_date = $values[$k];
            }
        }

        // Điều kiện cơ bản
        $cond = " a.debt_amount = 0 AND a.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";

        if (!empty($branch_id)) {
            $cond .= " AND a.branch_id IN (" . implode(",", $branch_id) . ")";
        }

        if ($keyword !== '') {
            $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%' OR s.gud_mobile1 LIKE '%$keyword%') ";
        }

        if ($start_date) {
            $cond .= " AND a.id IN (SELECT agreement_id FROM payments WHERE debt = 0 AND charge_date >= '$start_date')";
        }
        if ($end_date) {
            $cond .= " AND a.id IN (SELECT agreement_id FROM payments WHERE debt = 0 AND charge_date <= '$end_date 23:59:59')";
        }

        $order_by = " ORDER BY a.id DESC ";

        $query = "SELECT 
                a.id AS agreement_id,
                a.must_charge AS combo_fee,
                s.lms_code AS student_code,
                s.name AS student_name,
                (SELECT SUM(c2.summary_sessions) FROM contracts c2 WHERE c2.agreement_id = a.id AND c2.status NOT IN (0,1,7,8)) AS total_combo_sessions
            FROM agreements AS a
                LEFT JOIN students AS s ON s.id = a.student_id
            WHERE $cond
            $order_by";

        $agreements = u::query($query);
        $flat_list = [];
        $grand_summary_sessions = 0;
        $grand_done_sessions = 0;
        $grand_left_sessions = 0;
        $grand_left_value = 0;

        if (!empty($agreements)) {
            $agreement_ids = [];
            foreach ($agreements as $agrm) {
                $agreement_ids[] = $agrm->agreement_id;
            }

            $str_ids = implode(',', $agreement_ids);
            $contractsQuery = "SELECT 
                    c.agreement_id, 
                    p.name AS course_name, 
                    c.summary_sessions, 
                    (SELECT COUNT(shs.id) FROM schedule_has_student shs WHERE shs.contract_id = c.id AND shs.status = 1 " . ($school_year ? " AND YEAR(shs.class_date) = '$school_year'" : "") . ") AS done_sessions
                FROM contracts c 
                LEFT JOIN products p ON p.id = c.product_id 
                WHERE c.agreement_id IN ($str_ids) AND c.status NOT IN (0,1,7,8)
                ORDER BY c.id ASC";

            $contracts = u::query($contractsQuery);
            $contracts_by_agrm = [];
            foreach ($contracts as $c) {
                $contracts_by_agrm[$c->agreement_id][] = $c;
            }

            foreach ($agreements as $agrm) {
                $agrm_contracts = isset($contracts_by_agrm[$agrm->agreement_id]) ? $contracts_by_agrm[$agrm->agreement_id] : [];
                $session_price = $agrm->total_combo_sessions > 0 ? (float) $agrm->combo_fee / $agrm->total_combo_sessions : 0;

                foreach ($agrm_contracts as $c) {
                    $c->summary_sessions = (int) $c->summary_sessions;
                    $c->done_sessions = (int) $c->done_sessions;
                    $c->left_sessions = max(0, $c->summary_sessions - $c->done_sessions);
                    $c->left_value = round($c->left_sessions * $session_price, 0);

                    $grand_summary_sessions += $c->summary_sessions;
                    $grand_done_sessions += $c->done_sessions;
                    $grand_left_sessions += $c->left_sessions;
                    $grand_left_value += $c->left_value;

                    $flat_list[] = [
                        'student_code' => $agrm->student_code,
                        'student_name' => $agrm->student_name,
                        'course_name' => $c->course_name,
                        'summary_sessions' => $c->summary_sessions,
                        'done_sessions' => $c->done_sessions,
                        'left_sessions' => $c->left_sessions,
                        'left_value' => $c->left_value,
                    ];
                }
            }
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        // ── Tiêu đề ──
        $sheet->setCellValue('A1', 'BÁO CÁO CHI TIẾT THEO TỪNG KHÓA TRONG COMBO');
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // ── Header row ──
        $headers = ['A2' => 'Mã HV', 'B2' => 'Họ tên', 'C2' => 'Tên khóa', 'D2' => 'Số buổi', 'E2' => 'Đã học', 'F2' => 'Còn lại', 'G2' => 'Giá trị còn lại'];
        foreach ($headers as $cell => $label) {
            $sheet->setCellValue($cell, $label);
        }

        $sheet->getColumnDimension('A')->setWidth(14);
        $sheet->getColumnDimension('B')->setWidth(28);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(13);
        $sheet->getColumnDimension('E')->setWidth(13);
        $sheet->getColumnDimension('F')->setWidth(13);
        $sheet->getColumnDimension('G')->setWidth(20);

        $hStyle = [
            'font' => ['bold' => true],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8E8E8']],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'BBBBBB']]],
        ];
        $sheet->getStyle('A2:G2')->applyFromArray($hStyle);
        $sheet->getRowDimension(2)->setRowHeight(22);

        $borderOnly = ['borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]]];
        $centerAlign = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]];
        $rightAlign = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT]];

        // ── Data rows ──
        $row_num = 3;
        foreach ($flat_list as $row) {
            $sheet->setCellValue('A' . $row_num, $row['student_code']);
            $sheet->setCellValue('B' . $row_num, $row['student_name']);
            $sheet->setCellValue('C' . $row_num, $row['course_name']);
            $sheet->setCellValue('D' . $row_num, $row['summary_sessions']);
            $sheet->setCellValue('E' . $row_num, $row['done_sessions']);
            $sheet->setCellValue('F' . $row_num, $row['left_sessions']);
            $sheet->setCellValue('G' . $row_num, $row['left_value']);

            $sheet->getStyle("A$row_num:G$row_num")->applyFromArray($borderOnly);
            $sheet->getStyle("D$row_num:F$row_num")->applyFromArray($centerAlign);
            $sheet->getStyle("G$row_num")->applyFromArray($rightAlign);
            $sheet->getStyle("G$row_num")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getRowDimension($row_num)->setRowHeight(20);
            $row_num++;
        }

        // ── Dòng TỔNG CỘNG ──
        $sheet->mergeCells("A$row_num:C$row_num");
        $sheet->setCellValue('A' . $row_num, 'TỔNG CỘNG');
        $sheet->setCellValue('D' . $row_num, $grand_summary_sessions);
        $sheet->setCellValue('E' . $row_num, $grand_done_sessions);
        $sheet->setCellValue('F' . $row_num, $grand_left_sessions);
        $sheet->setCellValue('G' . $row_num, $grand_left_value);

        $totalStyle = [
            'font' => ['bold' => true],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0F0F0']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'AAAAAA']]],
        ];
        $sheet->getStyle("A$row_num:G$row_num")->applyFromArray($totalStyle);
        $sheet->getStyle("G$row_num")->applyFromArray($rightAlign);
        $sheet->getStyle("G$row_num")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getRowDimension($row_num)->setRowHeight(22);

        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Bao cao chi tiet khoa hoc trong combo.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function report19(Request $request, $key, $value)
    {
        set_time_limit(300);
        ini_set('memory_limit', '-1');

        $keys = explode(',', $key);
        $values = explode(',', $value);

        $branch_id = [];
        $start_date = '';
        $end_date = '';

        foreach ($keys as $k => $key_name) {
            if ($key_name == 'branch_id' && isset($values[$k]) && $values[$k] != 'v') {
                $branch_id = explode('-', $values[$k]);
            }
            if ($key_name == 'start_date' && isset($values[$k]) && $values[$k] != 'v') {
                $start_date = $values[$k];
            }
            if ($key_name == 'end_date' && isset($values[$k]) && $values[$k] != 'v') {
                $end_date = $values[$k];
            }
        }

        $agrm_cond = "a.debt_amount = 0 AND a.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";
        if (!empty($branch_id)) {
            $agrm_cond .= " AND a.branch_id IN (" . implode(",", $branch_id) . ")";
        }
        if ($start_date) {
            $agrm_cond .= " AND a.id IN (SELECT agreement_id FROM payments WHERE debt = 0 AND charge_date >= '$start_date')";
        }
        if ($end_date) {
            $agrm_cond .= " AND a.id IN (SELECT agreement_id FROM payments WHERE debt = 0 AND charge_date <= '$end_date 23:59:59')";
        }

        $query = "SELECT
                tf.id                                   AS tuition_fee_id,
                tf.name                                 AS course_name,
                COUNT(DISTINCT a.id)                    AS student_count,
                IFNULL(SUM(a.must_charge), 0)           AS total_revenue,
                (
                    SELECT SUM(c2.summary_sessions)
                    FROM contracts c2
                    WHERE c2.agreement_id = (
                        SELECT a3.id FROM agreements a3
                        WHERE a3.tuition_fee_id = tf.id
                          AND a3.debt_amount = 0
                        LIMIT 1
                    ) AND c2.status NOT IN (0,1,7,8)
                ) AS sessions_per_student
            FROM tuition_fee tf
            LEFT JOIN agreements a ON a.tuition_fee_id = tf.id AND $agrm_cond
            WHERE tf.status = 1
            GROUP BY tf.id, tf.name
            ORDER BY tf.name ASC";

        $rows = u::query($query);

        $total_students = 0;
        $total_revenue = 0;
        foreach ($rows as $row) {
            $total_students += (int) $row->student_count;
            $total_revenue += (float) $row->total_revenue;
        }

        $result = [];
        foreach ($rows as $row) {
            $count = (int) $row->student_count;
            $sessions = (int) $row->sessions_per_student;
            $percentage = $total_students > 0 ? round($count / $total_students * 100, 2) : 0;
            $result[] = [
                'course_name' => $row->course_name,
                'student_count' => $count,
                'percentage' => $percentage,
                'sessions_total' => $count * $sessions,
                'total_revenue' => (float) $row->total_revenue,
            ];
        }

        // --- Excel ---
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // ── Tiêu đề ──
        $sheet->setCellValue('A1', 'SỐ LƯỢNG HỌC VIÊN THEO TỪNG KHÓA');
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // ── Header ──
        $hCols = ['A' => 'Khóa học', 'B' => 'Số học viên', 'C' => 'Tỷ trọng %', 'D' => 'Số buổi phải dạy', 'E' => 'Doanh thu tương ứng'];
        foreach ($hCols as $col => $label) {
            $sheet->setCellValue($col . '2', $label);
        }

        $sheet->getColumnDimension('A')->setWidth(38);
        $sheet->getColumnDimension('B')->setWidth(16);
        $sheet->getColumnDimension('C')->setWidth(14);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(22);

        $hStyle = [
            'font' => ['bold' => true],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8E8E8']],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'BBBBBB']]],
        ];
        $sheet->getStyle('A2:E2')->applyFromArray($hStyle);
        $sheet->getRowDimension(2)->setRowHeight(22);

        $borderOnly = ['borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]]];
        $centerAlign = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]];
        $rightAlign = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT]];

        // ── Data rows ──
        $row_num = 3;
        foreach ($result as $row) {
            $sheet->setCellValue('A' . $row_num, $row['course_name']);
            $sheet->setCellValue('B' . $row_num, $row['student_count']);
            $sheet->setCellValue('C' . $row_num, $row['percentage'] . '%');
            $sheet->setCellValue('D' . $row_num, $row['sessions_total']);
            $sheet->setCellValue('E' . $row_num, $row['total_revenue']);

            $sheet->getStyle("A$row_num:E$row_num")->applyFromArray($borderOnly);
            $sheet->getStyle("B$row_num:D$row_num")->applyFromArray($centerAlign);
            $sheet->getStyle("E$row_num")->applyFromArray($rightAlign);
            $sheet->getStyle("E$row_num")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getRowDimension($row_num)->setRowHeight(20);
            $row_num++;
        }

        // ── Dòng TỔNG CỘNG ──
        $sheet->mergeCells("A$row_num:D$row_num");
        $sheet->setCellValue('A' . $row_num, 'TỔNG CỘNG');
        $sheet->setCellValue('E' . $row_num, $total_revenue);
        $totalStyle = [
            'font' => ['bold' => true],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0F0F0']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'AAAAAA']]],
        ];
        $sheet->getStyle("A$row_num:E$row_num")->applyFromArray($totalStyle);
        $sheet->getStyle("E$row_num")->applyFromArray($rightAlign);
        $sheet->getStyle("E$row_num")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getRowDimension($row_num)->setRowHeight(22);

        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="So luong hoc vien theo tung khoa.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function report20(Request $request, $key, $value)
    {
        set_time_limit(300);
        ini_set('memory_limit', '-1');

        $keys = explode(',', $key);
        $values = explode(',', $value);

        $branch_id = [];
        $start_date = '';
        $end_date = '';

        foreach ($keys as $k => $key_name) {
            if ($key_name == 'branch_id' && isset($values[$k]) && $values[$k] != 'v') {
                $branch_id = explode('-', $values[$k]);
            }
            if ($key_name == 'start_date' && isset($values[$k]) && $values[$k] != 'v') {
                $start_date = $values[$k];
            }
            if ($key_name == 'end_date' && isset($values[$k]) && $values[$k] != 'v') {
                $end_date = $values[$k];
            }
        }

        $branchCond = " a.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";
        if (!empty($branch_id)) {
            $branchCond .= " AND a.branch_id IN (" . implode(",", $branch_id) . ")";
        }
        $dateCond = '';
        if ($start_date) {
            $dateCond .= " AND a.full_fee_date >= '$start_date'";
        }
        if ($end_date) {
            $dateCond .= " AND a.full_fee_date <= '$end_date'";
        }
        $baseCond = "$branchCond AND a.debt_amount = 0 $dateCond";

        $query = "
            SELECT
                tf.name                 AS tuition_fee_name,
                tf.number_of_months      AS so_don_sau_tach,
                tf.price                AS gia_khoa_hoc,
                COUNT(a.id)             AS total_agreements,
                SUM(
                    CASE
                        WHEN a.count_recharge = 0 THEN 1
                        WHEN a.count_recharge > 0 THEN
                            CASE
                                WHEN (SELECT prev_a.id FROM agreements prev_a WHERE prev_a.student_id = a.student_id AND prev_a.tuition_fee_id = a.tuition_fee_id AND prev_a.count_recharge < a.count_recharge ORDER BY prev_a.count_recharge DESC LIMIT 1) IS NOT NULL
                                AND a.full_fee_date < (SELECT shs2.class_date FROM schedule_has_student shs2 INNER JOIN contracts c2 ON c2.id = shs2.contract_id INNER JOIN agreements prev_a2 ON prev_a2.id = c2.agreement_id WHERE prev_a2.student_id = a.student_id AND prev_a2.tuition_fee_id = a.tuition_fee_id AND prev_a2.count_recharge < a.count_recharge AND shs2.student_id = a.student_id AND shs2.status IN (1, 2) ORDER BY prev_a2.count_recharge DESC, shs2.class_date ASC LIMIT 1 OFFSET 7)
                                THEN 1 ELSE 0
                            END
                        ELSE 0
                    END
                ) AS count_new,
                SUM(
                    CASE
                        WHEN a.count_recharge > 0 THEN
                            CASE
                                WHEN (SELECT prev_a.id FROM agreements prev_a WHERE prev_a.student_id = a.student_id AND prev_a.tuition_fee_id = a.tuition_fee_id AND prev_a.count_recharge < a.count_recharge ORDER BY prev_a.count_recharge DESC LIMIT 1) IS NOT NULL
                                AND (a.full_fee_date IS NULL OR a.full_fee_date >= (SELECT shs2.class_date FROM schedule_has_student shs2 INNER JOIN contracts c2 ON c2.id = shs2.contract_id INNER JOIN agreements prev_a2 ON prev_a2.id = c2.agreement_id WHERE prev_a2.student_id = a.student_id AND prev_a2.tuition_fee_id = a.tuition_fee_id AND prev_a2.count_recharge < a.count_recharge AND shs2.student_id = a.student_id AND shs2.status IN (1, 2) ORDER BY prev_a2.count_recharge DESC, shs2.class_date ASC LIMIT 1 OFFSET 7))
                                THEN 1 ELSE 0
                            END
                        ELSE 0
                    END
                ) AS count_uplevel
            FROM agreements AS a
            LEFT JOIN tuition_fee AS tf ON tf.id = a.tuition_fee_id
            WHERE $baseCond
            GROUP BY tf.id, tf.name, tf.number_of_months, tf.price
            ORDER BY total_agreements DESC, tf.name ASC
        ";

        $list = u::query($query);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        // Tiêu đề
        $sheet->setCellValue('A1', 'BÁO CÁO DOANH SỐ CHI TIẾT THEO TỪNG KHÓA HỌC');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->applyFromArray(['font' => ['bold' => true, 'size' => 14], 'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Header
        $hData = ['A2' => 'STT', 'B2' => 'Khóa học đăng ký', 'C2' => 'Số đơn sau tách', 'D2' => 'Giá khóa học (VNĐ)', 'E2' => 'Mới', 'F2' => 'Up Level'];
        foreach ($hData as $c => $l) {
            $sheet->setCellValue($c, $l);
        }

        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(42);
        $sheet->getColumnDimension('C')->setWidth(16);
        $sheet->getColumnDimension('D')->setWidth(22);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(12);

        $hStyle = ['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8E8E8']], 'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER], 'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'BBBBBB']]]];
        $sheet->getStyle('A2:F2')->applyFromArray($hStyle);
        $sheet->getRowDimension(2)->setRowHeight(22);

        $borderOnly = ['borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]]];
        $centerAlign = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]];
        $rightAlign = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT]];

        $totalNew = 0;
        $totalUplevel = 0;
        $totalAll = 0;

        for ($i = 0; $i < count($list); $i++) {
            $x = $i + 3;
            $item = $list[$i];
            $sheet->setCellValue('A' . $x, $i + 1);
            $sheet->setCellValue('B' . $x, $item->tuition_fee_name);
            $sheet->setCellValue('C' . $x, $item->so_don_sau_tach);
            $sheet->setCellValue('D' . $x, $item->gia_khoa_hoc);
            $sheet->setCellValue('E' . $x, (int) $item->count_new);
            $sheet->setCellValue('F' . $x, (int) $item->count_uplevel);

            $sheet->getStyle("A$x:F$x")->applyFromArray($borderOnly);
            $sheet->getStyle("A$x")->applyFromArray($centerAlign);
            $sheet->getStyle("C$x:F$x")->applyFromArray($centerAlign);
            $sheet->getStyle("D$x")->applyFromArray($rightAlign);
            $sheet->getStyle("D$x")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getRowDimension($x)->setRowHeight(20);

            $totalNew += (int) $item->count_new;
            $totalUplevel += (int) $item->count_uplevel;
            $totalAll += (int) $item->total_agreements;
        }

        // Tổng cộng
        $tRow = count($list) + 3;
        $sheet->mergeCells("A$tRow:C$tRow");
        $sheet->setCellValue('A' . $tRow, 'TỔNG CỘNG');
        $sheet->setCellValue('D' . $tRow, '');
        $sheet->setCellValue('E' . $tRow, $totalNew);
        $sheet->setCellValue('F' . $tRow, $totalUplevel);
        $tStyle = ['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0F0F0']], 'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER], 'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'AAAAAA']]]];
        $sheet->getStyle("A$tRow:F$tRow")->applyFromArray($tStyle);
        $sheet->getRowDimension($tRow)->setRowHeight(22);

        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Bao cao doanh so chi tiet theo khoa hoc.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function report21(Request $request, $key, $value)
    {
        set_time_limit(300);
        ini_set('memory_limit', '-1');

        $keys = explode(',', $key);
        $values = explode(',', $value);

        $branch_id = [];
        $school_year = '';

        foreach ($keys as $k => $key_name) {
            if ($key_name == 'branch_id' && isset($values[$k]) && $values[$k] != 'v') {
                $branch_id = explode('-', $values[$k]);
            }
            if ($key_name == 'school_year' && isset($values[$k]) && $values[$k] != 'v') {
                $school_year = $values[$k];
            }
        }

        $branchIds = Auth::user()->getBranchesHasUser();
        $agCond = " a.debt_amount = 0 AND a.branch_id IN ($branchIds)";
        if (!empty($branch_id)) {
            $agCond .= " AND a.branch_id IN (" . implode(",", $branch_id) . ")";
        }

        // Queries (same as ReportsController@report21)
        $agStats = u::first("SELECT COUNT(a.id) AS total_agreements, SUM(IF(a.type_fee=1,1,0)) AS total_le, SUM(IF(a.type_fee=2,1,0)) AS total_combo FROM agreements AS a WHERE $agCond");
        $ctStats = u::first("SELECT COUNT(c.id) AS total_contracts, SUM(IF(a.type_fee=1,1,0)) AS total_le_ct, SUM(IF(a.type_fee=2,1,0)) AS total_combo_ct FROM contracts AS c INNER JOIN agreements AS a ON a.id=c.agreement_id WHERE $agCond AND c.status NOT IN (0,1,7,8)");
        $finStats = u::first("
            SELECT SUM(c.total_charged) AS total_charged_ct, SUM(c.summary_sessions) AS total_summary_sessions, SUM(c.done_sessions) AS total_done_sessions, SUM(c.left_sessions) AS total_left_sessions,
                SUM(CASE WHEN c.summary_sessions>0 THEN ROUND(c.total_charged*c.done_sessions/c.summary_sessions,0) ELSE 0 END) AS revenue_done,
                SUM(CASE WHEN c.summary_sessions>0 THEN ROUND(c.total_charged*c.left_sessions/c.summary_sessions,0) ELSE 0 END) AS revenue_left
            FROM contracts AS c INNER JOIN agreements AS a ON a.id=c.agreement_id WHERE $agCond AND c.status NOT IN (0,1,7,8)");

        $totalSummary = (int) ($finStats->total_summary_sessions ?? 0);
        $totalDone = (int) ($finStats->total_done_sessions ?? 0);
        $pct_done = $totalSummary > 0 ? round($totalDone / $totalSummary * 100, 2) : 0;
        $totalCharged = (float) ($finStats->total_charged_ct ?? 0);
        $revenueDone = (float) ($finStats->revenue_done ?? 0);
        $revenueLeft = (float) ($finStats->revenue_left ?? 0);
        $leftSessions = (int) ($finStats->total_left_sessions ?? 0);

        $val_total = (int) ($agStats->total_agreements ?? 0);
        $val_le = (int) ($agStats->total_le ?? 0);
        $val_combo = (int) ($agStats->total_combo ?? 0);
        $sach_total = (int) ($ctStats->total_contracts ?? 0);
        $sach_le = (int) ($ctStats->total_le_ct ?? 0);
        $sach_combo = (int) ($ctStats->total_combo_ct ?? 0);

        // Rows: [label, value, value_split, is_bold, is_money]
        $rows = [
            ['Tổng số học viên', $val_total, $sach_total, true, false],
            ['Số học viên đăng ký khóa lẻ', $val_le, $sach_le, false, false],
            ['Số học viên đăng ký combo', $val_combo, $sach_combo, false, false],
            ['Tổng doanh thu đã thu', $totalCharged, $totalCharged, false, true],
            ['Doanh thu đã thực hiện', $revenueDone, $revenueDone, false, true],
            ['Doanh thu chưa thực hiện (còn lại)', $revenueLeft, $revenueLeft, false, true],
            ['Số buổi đã dạy', $totalDone, $totalDone, false, false],
            ['Số buổi còn lại phải dạy', $leftSessions, $leftSessions, false, false],
            ['% hoàn thành trung bình', $pct_done . '%', $pct_done . '%', false, false],
        ];

        $yearLabel = $school_year ?: date('Y');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        // Tiêu đề
        $sheet->setCellValue('A1', 'BẢNG TỔNG QUAN QUẢN LÝ HỌC SINH NĂM ' . $yearLabel);
        $sheet->mergeCells('A1:C1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(32);

        // Dòng trống
        $sheet->getRowDimension(2)->setRowHeight(10);

        // Header cột
        $hStyle = [
            'font' => ['bold' => true],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8E8E8']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'BBBBBB']]],
        ];
        $sheet->setCellValue('A3', 'Chỉ số');
        $sheet->setCellValue('B3', 'Giá trị');
        $sheet->setCellValue('C3', 'Giá trị khóa học sau tách');
        $sheet->getStyle('A3:C3')->applyFromArray($hStyle);
        $sheet->getRowDimension(3)->setRowHeight(28);

        $sheet->getColumnDimension('A')->setWidth(38);
        $sheet->getColumnDimension('B')->setWidth(24);
        $sheet->getColumnDimension('C')->setWidth(28);

        $borderStyle = ['borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]]];
        $moneyFmt = '#,##0';
        $leftAlign = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT]];
        $rightAlign = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT]];

        foreach ($rows as $i => $row) {
            $x = $i + 4;
            [$label, $val, $split, $bold, $money] = $row;

            $sheet->setCellValue('A' . $x, $label);
            $sheet->setCellValue('B' . $x, $val);
            $sheet->setCellValue('C' . $x, $split);

            // Áp style chung
            $sheet->getStyle("A$x:C$x")->applyFromArray($borderStyle);
            $sheet->getStyle("A$x")->applyFromArray($leftAlign);  // Chỉ số → căn trái
            $sheet->getRowDimension($x)->setRowHeight(22);

            if ($bold) {
                // Dòng tổng số học viên: in đậm, số căn phải
                $sheet->getStyle("A$x:C$x")->getFont()->setBold(true);
                $sheet->getStyle("B$x")->applyFromArray($rightAlign);
                $sheet->getStyle("C$x")->applyFromArray($rightAlign);
            } elseif ($money) {
                // Dòng tiền tệ: căn phải + format tiền
                $sheet->getStyle("B$x")->applyFromArray($rightAlign);
                $sheet->getStyle("C$x")->applyFromArray($rightAlign);
                $sheet->getStyle("B$x")->getNumberFormat()->setFormatCode($moneyFmt);
                $sheet->getStyle("C$x")->getNumberFormat()->setFormatCode($moneyFmt);
            } else {
                // Số thường (buổi học, %) → đều căn phải
                $sheet->getStyle("B$x")->applyFromArray($rightAlign);
                $sheet->getStyle("C$x")->applyFromArray($rightAlign);
            }
        }

        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Bang tong quan hoc sinh nam ' . $yearLabel . '.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function report22(Request $request, $key, $value)
    {
        set_time_limit(300);
        ini_set('memory_limit', '-1');

        $keys = explode(',', $key);
        $values = explode(',', $value);

        $branch_id = [];
        $team_id = 0;
        $ec_id = 0;
        $keyword = '';
        $due_start = '';
        $due_end = '';
        $pay_start = '';
        $pay_end = '';
        $completion_status = -1;

        foreach ($keys as $k => $key_name) {
            $v = $values[$k] ?? '';
            if ($v === 'v')
                $v = '';
            switch ($key_name) {
                case 'branch_id':
                    $branch_id = $v ? explode('-', $v) : [];
                    break;
                case 'team_id':
                    $team_id = (int) $v;
                    break;
                case 'ec_id':
                    $ec_id = (int) $v;
                    break;
                case 'keyword':
                    $keyword = urldecode($v);
                    break;
                case 'due_start':
                    $due_start = $v;
                    break;
                case 'due_end':
                    $due_end = $v;
                    break;
                case 'pay_start':
                    $pay_start = $v;
                    break;
                case 'pay_end':
                    $pay_end = $v;
                    break;
                case 'completion_status':
                    $completion_status = (int) $v;
                    break;
            }
        }

        // Phân quyền dữ liệu
        $user = Auth::user();
        $userRoles = u::query("SELECT role_id FROM role_has_user WHERE user_id = {$user->id}");
        $roleIds = [];
        foreach ($userRoles as $ur) {
            $roleIds[] = $ur->role_id;
        }

        if (in_array(69, $roleIds)) {
            $team_id = $user->id;
        } elseif (in_array(68, $roleIds)) {
            $ec_id = $user->id;
        }

        $cond = " a.total_charged > 0 AND a.status > 0 AND a.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";
        if (!empty($branch_id)) {
            $cond .= " AND a.branch_id IN (" . implode(",", $branch_id) . ")";
        }
        if ($team_id > 0) {
            $cond .= " AND (a.ec_leader_id = $team_id OR ((a.ec_leader_id IS NULL OR a.ec_leader_id =0) AND a.ec_id = $team_id))";
        }
        if ($ec_id > 0) {
            $cond .= " AND a.ec_id = $ec_id";
        }
        if ($keyword !== '') {
            $kw = addslashes($keyword);
            $cond .= " AND (s.lms_code LIKE '%$kw%' OR s.name LIKE '%$kw%' OR s.gud_mobile1 LIKE '%$kw%')";
        }
        if ($completion_status == 1) {
            $cond .= " AND a.debt_amount = 0";
        } elseif ($completion_status == 0) {
            $cond .= " AND a.debt_amount > 0";
        }
        if ($due_start) {
            $cond .= " AND (SELECT shs.class_date FROM schedule_has_student shs INNER JOIN contracts c2 ON c2.id=shs.contract_id WHERE c2.agreement_id=a.id AND c2.enrolment_start_date IS NOT NULL ORDER BY shs.class_date ASC LIMIT 7,1) >= '$due_start'";
        }
        if ($due_end) {
            $cond .= " AND (SELECT shs.class_date FROM schedule_has_student shs INNER JOIN contracts c2 ON c2.id=shs.contract_id WHERE c2.agreement_id=a.id AND c2.enrolment_start_date IS NOT NULL ORDER BY shs.class_date ASC LIMIT 7,1) <= '$due_end'";
        }
        if ($pay_start) {
            $cond .= " AND (SELECT MAX(p.charge_date) FROM payments p WHERE p.agreement_id=a.id) >= '$pay_start'";
        }
        if ($pay_end) {
            $cond .= " AND (SELECT MAX(p.charge_date) FROM payments p WHERE p.agreement_id=a.id) <= '$pay_end'";
        }

        $list = u::query("
            SELECT
                s.lms_code,
                s.name AS student_name,
                tf.name AS course_name,
                CASE WHEN a.ec_leader_id IS NOT NULL
                    THEN (SELECT u.name FROM users u WHERE u.id = a.ec_leader_id)
                    ELSE (SELECT u.name FROM users u WHERE u.id = a.ec_id)
                END AS team_name,
                (SELECT u.name FROM users u WHERE u.id = a.ec_id) AS ec_name,
                a.must_charge, a.total_charged, a.debt_amount,
                (SELECT MAX(p.charge_date) FROM payments p WHERE p.agreement_id = a.id) AS last_pay_date,
                (SELECT shs.class_date FROM schedule_has_student shs
                    INNER JOIN contracts c2 ON c2.id = shs.contract_id
                    WHERE c2.agreement_id = a.id AND c2.enrolment_start_date IS NOT NULL
                    ORDER BY shs.class_date ASC LIMIT 7, 1) AS due_date
            FROM agreements AS a
            INNER JOIN students AS s ON s.id = a.student_id
            LEFT JOIN tuition_fee AS tf ON tf.id = a.tuition_fee_id
            WHERE $cond
            ORDER BY a.id DESC
        ");

        // Build Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);

        // Title
        $title = 'BÁO CÁO CHI TIẾT CÔNG NỢ THEO KHÁCH HÀNG';
        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells('A1:J1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);
        $sheet->getRowDimension(2)->setRowHeight(6);

        // Headers
        $headers = ['Mã HV', 'Họ tên', 'Chương trình', 'Team KD', 'Thành viên sale', 'Tổng học phí', 'Đã thu', 'Còn phải thu', 'Trạng thái', 'Hạn thanh toán', 'Ngày thu gần nhất'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];
        $hStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']]],
        ];
        foreach ($headers as $i => $h) {
            $sheet->setCellValue($cols[$i] . '3', $h);
        }
        $sheet->getStyle('A3:J3')->applyFromArray($hStyle);
        $sheet->getRowDimension(3)->setRowHeight(24);

        // Column widths
        $widths = [12, 22, 22, 16, 16, 16, 14, 14, 16, 16, 16];
        foreach ($cols as $i => $c) {
            $sheet->getColumnDimension($c)->setWidth($widths[$i]);
        }

        $moneyFmt = '#,##0';
        $leftAlign = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT]];
        $rightAlign = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT]];
        $centerAlign = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]];
        $borderStyle = ['borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'E0E0E0']]]];

        $totalMust = 0;
        $totalCharged = 0;
        $totalDebt = 0;
        $row = 4;
        foreach ($list as $item) {
            $sheet->setCellValue('A' . $row, $item->lms_code);
            $sheet->setCellValue('B' . $row, $item->student_name);
            $sheet->setCellValue('C' . $row, $item->course_name ?? '');
            $sheet->setCellValue('D' . $row, $item->team_name ?? '');
            $sheet->setCellValue('E' . $row, $item->ec_name ?? '');
            $sheet->setCellValue('F' . $row, (float) $item->must_charge);
            $sheet->setCellValue('G' . $row, (float) $item->total_charged);
            $sheet->setCellValue('H' . $row, (float) $item->debt_amount);
            $sheet->setCellValue('I' . $row, ((float) $item->debt_amount > 0 ? 'Chưa hoàn thành' : 'Hoàn thành'));
            $sheet->setCellValue('J' . $row, $item->due_date ?? '');
            $sheet->setCellValue('K' . $row, $item->last_pay_date ?? '');

            $sheet->getStyle("A$row:K$row")->applyFromArray($borderStyle);
            $sheet->getStyle("A$row")->applyFromArray($centerAlign);
            $sheet->getStyle("B$row:E$row")->applyFromArray($leftAlign);
            $sheet->getStyle("F$row:H$row")->applyFromArray($rightAlign);
            $sheet->getStyle("F$row")->getNumberFormat()->setFormatCode($moneyFmt);
            $sheet->getStyle("G$row")->getNumberFormat()->setFormatCode($moneyFmt);
            $sheet->getStyle("H$row")->getNumberFormat()->setFormatCode($moneyFmt);
            $sheet->getStyle("I$row:K$row")->applyFromArray($centerAlign);
            $sheet->getRowDimension($row)->setRowHeight(18);

            // Tô màu dòng nợ cao
            if ((float) $item->debt_amount > 0) {
                $sheet->getStyle("H$row")->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFD72B28'));
            }

            $totalMust += (float) $item->must_charge;
            $totalCharged += (float) $item->total_charged;
            $totalDebt += (float) $item->debt_amount;
            $row++;
        }

        // Dòng tổng cộng
        $sheet->mergeCells("A$row:E$row");
        $sheet->setCellValue('A' . $row, 'TỔNG CỘNG');
        $sheet->setCellValue('F' . $row, $totalMust);
        $sheet->setCellValue('G' . $row, $totalCharged);
        $sheet->setCellValue('H' . $row, $totalDebt);

        $totalStyle = [
            'font' => ['bold' => true],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EEF2FF']],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'BBBBBB']]],
        ];
        $sheet->getStyle("A$row:K$row")->applyFromArray($totalStyle);
        $sheet->getStyle("A$row")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("F$row:H$row")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("F$row")->getNumberFormat()->setFormatCode($moneyFmt);
        $sheet->getStyle("G$row")->getNumberFormat()->setFormatCode($moneyFmt);
        $sheet->getStyle("H$row")->getNumberFormat()->setFormatCode($moneyFmt);
        $sheet->getRowDimension($row)->setRowHeight(22);

        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Bao cao cong no chi tiet.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function report23(Request $request, $key, $value)
    {
        set_time_limit(300);
        $keys = explode(',', $key);
        $values = explode(',', $value);

        $branch_id = [];
        $start_date = '';
        $end_date = '';
        foreach ($keys as $k => $key_name) {
            $v = $values[$k] ?? '';
            if ($v === 'v')
                $v = '';
            switch ($key_name) {
                case 'branch_id':
                    $branch_id = $v ? explode('-', $v) : [];
                    break;
                case 'start_date':
                    $start_date = $v;
                    break;
                case 'end_date':
                    $end_date = $v;
                    break;
            }
        }

        $cond = " a.status > 0 AND a.total_charged > 0 AND a.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";
        if (!empty($branch_id)) {
            $cond .= " AND a.branch_id IN (" . implode(",", $branch_id) . ")";
        }
        if ($start_date) {
            $cond .= " AND a.full_fee_date >= '$start_date'";
        }
        if ($end_date) {
            $cond .= " AND a.full_fee_date <= '$end_date'";
        }

        $rows = u::query("
            SELECT
                COALESCE(a.ec_leader_id, a.ec_id) AS team_user_id,
                ANY_VALUE(
                    CASE WHEN a.ec_leader_id IS NOT NULL
                        THEN (SELECT u.name FROM users u WHERE u.id = a.ec_leader_id)
                        ELSE (SELECT u.name FROM users u WHERE u.id = a.ec_id)
                    END
                ) AS team_name,
                COUNT(CASE WHEN a.count_recharge = 0 THEN 1 END)       AS new_count,
                COUNT(CASE WHEN a.count_recharge > 0 THEN 1 END)       AS uplv_count,
                SUM(CASE WHEN a.count_recharge = 0 THEN a.must_charge ELSE 0 END) AS new_revenue,
                SUM(CASE WHEN a.count_recharge > 0 THEN a.must_charge ELSE 0 END) AS uplv_revenue,
                SUM(a.must_charge) AS total_revenue
            FROM agreements AS a
            WHERE $cond
            GROUP BY COALESCE(a.ec_leader_id, a.ec_id)
            ORDER BY team_name ASC
        ");

        // ---- Build Excel ----
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);

        $period = ($start_date ? $start_date : '...') . ' – ' . ($end_date ? $end_date : '...');
        $title = 'THỐNG KÊ DOANH THU THEO TEAM SALE  |  Full fee: ' . $period;
        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(26);
        $sheet->getRowDimension(2)->setRowHeight(5);

        // Row 3: merged group header
        $sheet->setCellValue('C3', 'DOANH SỐ');
        $sheet->mergeCells('C3:D3');
        $sheet->getStyle('C3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '16A34A']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ]);

        // Row 4: column headers
        $headers = ['STT', 'TEAM', 'MỚI', 'UP LV', 'DOANH THU', 'LƯƠNG SALE'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F'];
        $hBgColor = ['A' => '4F46E5', 'B' => '4F46E5', 'C' => '16A34A', 'D' => '16A34A', 'E' => 'EA580C', 'F' => 'EA580C'];
        foreach ($headers as $i => $h) {
            $c = $cols[$i];
            $sheet->setCellValue($c . '4', $h);
            $sheet->getStyle($c . '4')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $hBgColor[$c]]],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']]],
            ]);
        }
        $sheet->getRowDimension(3)->setRowHeight(20);
        $sheet->getRowDimension(4)->setRowHeight(22);

        // Column widths
        $widths = ['A' => 8, 'B' => 28, 'C' => 12, 'D' => 12, 'E' => 18, 'F' => 18];
        foreach ($widths as $c => $w) {
            $sheet->getColumnDimension($c)->setWidth($w);
        }

        $moneyFmt = '#,##0';
        $borderStyle = ['borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'E0E0E0']]]];
        $center = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]];
        $right = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT]];
        $left = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT]];

        $sumNew = 0;
        $sumUplv = 0;
        $sumRevenue = 0.0;
        $sumSalary = 0.0;
        $row = 5;
        foreach ($rows as $idx => $item) {
            $new_rev = (float) $item->new_revenue;
            $uplv_rev = (float) $item->uplv_revenue;
            $salary = round($new_rev * 0.10 + $uplv_rev * 0.06);
            $total = (float) $item->total_revenue;

            $sheet->setCellValue('A' . $row, $idx + 1);
            $sheet->setCellValue('B' . $row, $item->team_name ?? '—');
            $sheet->setCellValue('C' . $row, (int) $item->new_count);
            $sheet->setCellValue('D' . $row, (int) $item->uplv_count);
            $sheet->setCellValue('E' . $row, $total);
            $sheet->setCellValue('F' . $row, $salary);

            $sheet->getStyle("A$row:F$row")->applyFromArray($borderStyle);
            $sheet->getStyle("A$row")->applyFromArray($center);
            $sheet->getStyle("B$row")->applyFromArray($left);
            $sheet->getStyle("C$row:D$row")->applyFromArray($center);
            $sheet->getStyle("E$row:F$row")->applyFromArray($right);
            $sheet->getStyle("E$row")->getNumberFormat()->setFormatCode($moneyFmt);
            $sheet->getStyle("F$row")->getNumberFormat()->setFormatCode($moneyFmt);
            $sheet->getRowDimension($row)->setRowHeight(18);

            $sumNew += (int) $item->new_count;
            $sumUplv += (int) $item->uplv_count;
            $sumRevenue += $total;
            $sumSalary += $salary;
            $row++;
        }

        // Dòng tổng
        $sheet->mergeCells("A$row:B$row");
        $sheet->setCellValue('A' . $row, 'TỔNG');
        $sheet->setCellValue('C' . $row, $sumNew);
        $sheet->setCellValue('D' . $row, $sumUplv);
        $sheet->setCellValue('E' . $row, $sumRevenue);
        $sheet->setCellValue('F' . $row, $sumSalary);
        $totalStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT],
        ];
        $sheet->getStyle("A$row:F$row")->applyFromArray($totalStyle);
        $sheet->getStyle("A$row")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("B$row")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("C$row:D$row")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("E$row")->getNumberFormat()->setFormatCode($moneyFmt);
        $sheet->getStyle("F$row")->getNumberFormat()->setFormatCode($moneyFmt);
        $sheet->getRowDimension($row)->setRowHeight(22);

        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Thong ke doanh thu team sale.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }
    public function report24(Request $request, $key, $value)
    {
        set_time_limit(300);
        ini_set('memory_limit', '-1');

        $keys = explode(',', $key);
        $values = explode(',', $value);

        $branch_id = [];
        $team_id = 0;
        $ec_id = 0;
        $keyword = '';
        $start_date = '';
        $end_date = '';
        $completion_status = -1;

        foreach ($keys as $k => $key_name) {
            $v = $values[$k] ?? '';
            if ($v === 'v') $v = '';
            switch ($key_name) {
                case 'branch_id':
                    $branch_id = $v ? explode('-', $v) : [];
                    break;
                case 'team_id':
                    $team_id = (int) $v;
                    break;
                case 'ec_id':
                    $ec_id = (int) $v;
                    break;
                case 'keyword':
                    $keyword = urldecode($v);
                    break;
                case 'start_date':
                    $start_date = $v;
                    break;
                case 'end_date':
                    $end_date = $v;
                    break;
                case 'completion_status':
                    $completion_status = (int) $v;
                    break;
            }
        }

        $user = Auth::user();
        $userRoles = u::query("SELECT role_id FROM role_has_user WHERE user_id = {$user->id}");
        $roleIds = [];
        foreach ($userRoles as $ur) {
            $roleIds[] = $ur->role_id;
        }

        if (in_array(69, $roleIds)) {
            $team_id = $user->id;
        } elseif (in_array(68, $roleIds)) {
            $ec_id = $user->id;
        }

        $cond = " a.status > 0 AND a.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";
        if (!empty($branch_id)) {
            $cond .= " AND a.branch_id IN (" . implode(",", $branch_id) . ")";
        }
        if ($team_id > 0) {
            $cond .= " AND (a.ec_leader_id = $team_id OR (a.ec_leader_id IS NULL AND a.ec_id = $team_id))";
        }
        if ($ec_id > 0) {
            $cond .= " AND a.ec_id = $ec_id";
        }
        if ($keyword !== '') {
            $kw = addslashes($keyword);
            $cond .= " AND (s.lms_code LIKE '%$kw%' OR s.name LIKE '%$kw%' OR s.gud_mobile1 LIKE '%$kw%')";
        }
        if ($completion_status == 1) {
            $cond .= " AND a.debt_amount = 0";
        } elseif ($completion_status == 0) {
            $cond .= " AND a.debt_amount > 0";
        }
        if ($start_date) {
            $cond .= " AND a.created_at >= '$start_date 00:00:00'";
        }
        if ($end_date) {
            $cond .= " AND a.created_at <= '$end_date 23:59:59'";
        }

        $query = "
            SELECT
                DATE(a.created_at) AS date_0,
                IF(a.count_recharge = 0, 'Mới', '') AS status_register,
                'Không' AS up_process,
                tf.name AS course_name,
                s.name AS student_name,
                s.gud_mobile1 AS phone,
                CASE
                    WHEN a.ec_leader_id IS NOT NULL THEN (SELECT u.name FROM users u WHERE u.id = a.ec_leader_id)
                    ELSE (SELECT u.name FROM users u WHERE u.id = a.ec_id)
                END AS team_name,
                s.address,
                a.must_charge,
                IF(a.group_type > 0, CONCAT('Nhóm ', a.group_type), 'Không') AS dk_chung,
                (SELECT amount FROM payments p WHERE p.agreement_id = a.id ORDER BY id ASC LIMIT 1) AS p1_amount,
                (SELECT charge_date FROM payments p WHERE p.agreement_id = a.id ORDER BY id ASC LIMIT 1) AS p1_date,
                (SELECT SUM(amount) FROM payments p WHERE p.agreement_id = a.id) AS total_paid,
                (SELECT MAX(charge_date) FROM payments p WHERE p.agreement_id = a.id) AS last_pay_date,
                a.discount_amount AS discount,
                a.debt_amount,
                a.id AS agreement_id
            FROM agreements AS a
            INNER JOIN students AS s ON s.id = a.student_id
            LEFT JOIN tuition_fee AS tf ON tf.id = a.tuition_fee_id
            WHERE $cond
            ORDER BY a.id DESC
        ";

        $list = u::query($query);
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);

        $title = 'BÁO CÁO PHỤC VỤ XUẤT HÓA ĐƠN';
        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells('A1:W1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $headers = ['STT', 'Ngày tạo', 'Trạng thái đăng ký', 'Up quá trình từ', 'Khoá học đăng kí', 'Họ và tên', 'Sđt', 'Team kinh doanh', 'ĐỊA CHỈ NHẬN SÁCH', 'Giá khoá học', 'DK chung', 'Học phí đợt 1 (Đã duyệt)', 'Ngày CK 1 (Đã duyệt)', 'Học phí đợt 2 (Đã duyệt)', 'Ngày CK 2 (Đã duyệt)', 'Ảnh Bill (Đã duyệt)', 'Học phí đợt 1 (Chưa duyệt)', 'Ngày CK 1 (Chưa duyệt)', 'Học phí đợt 2 (Chưa duyệt)', 'Ngày CK 2 (Chưa duyệt)', 'Ảnh Bill (Chưa duyệt)', 'Giảm trừ', 'Công nợ'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W'];
        
        $hStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']]],
        ];
        
        foreach ($headers as $i => $h) {
            $sheet->setCellValue($cols[$i] . '3', $h);
        }
        $sheet->getStyle('A3:W3')->applyFromArray($hStyle);
        $sheet->getRowDimension(3)->setRowHeight(24);

        $widths = [8, 14, 16, 16, 22, 22, 14, 16, 22, 16, 12, 16, 14, 16, 14, 30, 16, 14, 16, 14, 30, 14, 16];
        foreach ($cols as $i => $c) {
            $sheet->getColumnDimension($c)->setWidth($widths[$i]);
        }

        $moneyFmt = '#,##0';
        $borderStyle = ['borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'E0E0E0']]]];
        $centerAlign = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]];
        $leftAlign = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT]];
        $rightAlign = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT]];

        $rowIdx = 4;
        foreach ($list as $idx => $item) {
            $p1_amount = (float)$item->p1_amount;
            $total_paid = (float)$item->total_paid;
            $p2_amount = $total_paid - $p1_amount;
            if ($p2_amount < 0) $p2_amount = 0;
            $p2_date = ($p2_amount > 0) ? $item->last_pay_date : '';
            
            $agrmId = $item->agreement_id;
            $tmpPayments = u::query("SELECT attachments FROM tmp_payments WHERE agreement_id = $agrmId AND status = 1");
            $bills = [];
            foreach($tmpPayments as $tp) {
                if (!empty($tp->attachments)) {
                    $arr = json_decode($tp->attachments, true);
                    if (is_array($arr)) {
                        foreach($arr as $path) {
                            $fullUrl = rtrim(env('APP_URL'), '/') . '/' . ltrim($path, '/');
                            $bills[] = $fullUrl;
                        }
                    }
                }
            }
            $img_bill_str = implode("\n", $bills);

            // Fetch Chua Duyet
            $p1_amount_cd = 0;
            $p1_date_cd = '';
            $p2_amount_cd = 0;
            $p2_date_cd = '';
            $tmpPaymentsCd = u::query("SELECT charge_amount, charge_date, attachments FROM tmp_payments WHERE agreement_id = $agrmId AND status = 0 ORDER BY id ASC");
            $bills_cd = [];
            if (count($tmpPaymentsCd) > 0) {
                $p1_amount_cd = (float)$tmpPaymentsCd[0]->charge_amount;
                $p1_date_cd = substr($tmpPaymentsCd[0]->charge_date, 0, 10);
                $total_paid_cd = 0;
                $last_pay_date_cd = '';
                foreach($tmpPaymentsCd as $tp_cd) {
                    $total_paid_cd += (float)$tp_cd->charge_amount;
                    if ($tp_cd->charge_date) {
                        $last_pay_date_cd = substr($tp_cd->charge_date, 0, 10);
                    }
                    if (!empty($tp_cd->attachments)) {
                        $arr = json_decode($tp_cd->attachments, true);
                        if (is_array($arr)) {
                            foreach($arr as $path) {
                                $fullUrl = rtrim(env('APP_URL'), '/') . '/' . ltrim($path, '/');
                                $bills_cd[] = $fullUrl;
                            }
                        }
                    }
                }
                $p2_amount_cd = $total_paid_cd - $p1_amount_cd;
                if ($p2_amount_cd < 0) $p2_amount_cd = 0;
                $p2_date_cd = ($p2_amount_cd > 0) ? $last_pay_date_cd : '';
            }
            $img_bill_str_cd = implode("\n", $bills_cd);

            $sheet->setCellValue('A' . $rowIdx, $idx + 1);
            $sheet->setCellValue('B' . $rowIdx, $item->date_0);
            $sheet->setCellValue('C' . $rowIdx, $item->status_register);
            $sheet->setCellValue('D' . $rowIdx, $item->up_process);
            $sheet->setCellValue('E' . $rowIdx, $item->course_name ?? '—');
            $sheet->setCellValue('F' . $rowIdx, $item->student_name);
            $sheet->setCellValue('G' . $rowIdx, $item->phone);
            $sheet->setCellValue('H' . $rowIdx, $item->team_name ?? '—');
            $sheet->setCellValue('I' . $rowIdx, $item->address ?? '—');
            $sheet->setCellValue('J' . $rowIdx, (float)$item->must_charge);
            $sheet->setCellValue('K' . $rowIdx, $item->dk_chung);
            $sheet->setCellValue('L' . $rowIdx, $p1_amount);
            $sheet->setCellValue('M' . $rowIdx, $item->p1_date);
            $sheet->setCellValue('N' . $rowIdx, $p2_amount);
            $sheet->setCellValue('O' . $rowIdx, $p2_date);
            $sheet->setCellValue('P' . $rowIdx, $img_bill_str);
            $sheet->setCellValue('Q' . $rowIdx, $p1_amount_cd);
            $sheet->setCellValue('R' . $rowIdx, $p1_date_cd);
            $sheet->setCellValue('S' . $rowIdx, $p2_amount_cd);
            $sheet->setCellValue('T' . $rowIdx, $p2_date_cd);
            $sheet->setCellValue('U' . $rowIdx, $img_bill_str_cd);
            $sheet->setCellValue('V' . $rowIdx, (float)$item->discount);
            $sheet->setCellValue('W' . $rowIdx, (float)$item->debt_amount);

            $sheet->getStyle("A$rowIdx:W$rowIdx")->applyFromArray($borderStyle);
            $sheet->getStyle("A$rowIdx:D$rowIdx")->applyFromArray($centerAlign);
            $sheet->getStyle("E$rowIdx:I$rowIdx")->applyFromArray($leftAlign);
            $sheet->getStyle("J$rowIdx")->applyFromArray($rightAlign);
            $sheet->getStyle("J$rowIdx")->getNumberFormat()->setFormatCode($moneyFmt);
            $sheet->getStyle("K$rowIdx")->applyFromArray($centerAlign);
            $sheet->getStyle("L$rowIdx")->applyFromArray($rightAlign);
            $sheet->getStyle("L$rowIdx")->getNumberFormat()->setFormatCode($moneyFmt);
            $sheet->getStyle("M$rowIdx")->applyFromArray($centerAlign);
            $sheet->getStyle("N$rowIdx")->applyFromArray($rightAlign);
            $sheet->getStyle("N$rowIdx")->getNumberFormat()->setFormatCode($moneyFmt);
            $sheet->getStyle("O$rowIdx")->applyFromArray($centerAlign);
            $sheet->getStyle("P$rowIdx")->applyFromArray($leftAlign);
            $sheet->getStyle("P$rowIdx")->getAlignment()->setWrapText(true);
            
            $sheet->getStyle("Q$rowIdx")->applyFromArray($rightAlign);
            $sheet->getStyle("Q$rowIdx")->getNumberFormat()->setFormatCode($moneyFmt);
            $sheet->getStyle("R$rowIdx")->applyFromArray($centerAlign);
            $sheet->getStyle("S$rowIdx")->applyFromArray($rightAlign);
            $sheet->getStyle("S$rowIdx")->getNumberFormat()->setFormatCode($moneyFmt);
            $sheet->getStyle("T$rowIdx")->applyFromArray($centerAlign);
            $sheet->getStyle("U$rowIdx")->applyFromArray($leftAlign);
            $sheet->getStyle("U$rowIdx")->getAlignment()->setWrapText(true);
            
            if (!empty($bills)) {
                $sheet->getCell('P' . $rowIdx)->getHyperlink()->setUrl($bills[0]);
                $sheet->getStyle('P' . $rowIdx)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLUE);
                $sheet->getStyle('P' . $rowIdx)->getFont()->setUnderline(true);
            }
            if (!empty($bills_cd)) {
                $sheet->getCell('U' . $rowIdx)->getHyperlink()->setUrl($bills_cd[0]);
                $sheet->getStyle('U' . $rowIdx)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLUE);
                $sheet->getStyle('U' . $rowIdx)->getFont()->setUnderline(true);
            }
            
            $sheet->getStyle("V$rowIdx:W$rowIdx")->applyFromArray($rightAlign);
            $sheet->getStyle("V$rowIdx")->getNumberFormat()->setFormatCode($moneyFmt);
            $sheet->getStyle("W$rowIdx")->getNumberFormat()->setFormatCode($moneyFmt);
            
            if ((float)$item->debt_amount > 0) {
                $sheet->getStyle("W$rowIdx")->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFD72B28'));
            }

            $rowIdx++;
        }

        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Bao cao phuc vu xuat hoa don.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function report25(Request $request, $key, $value)
    {
        set_time_limit(300);
        ini_set('memory_limit', '-1');

        $keys = explode(',', $key);
        $values = explode(',', $value);

        $branch_id = [];
        $team_id = 0;
        $ec_id = 0;
        $keyword = '';
        $start_date = '';
        $end_date = '';
        $completion_status = -1;

        foreach ($keys as $k => $key_name) {
            $v = $values[$k] ?? '';
            if ($v === 'v') $v = '';
            switch ($key_name) {
                case 'branch_id':
                    $branch_id = $v ? explode('-', $v) : [];
                    break;
                case 'team_id':
                    $team_id = (int) $v;
                    break;
                case 'ec_id':
                    $ec_id = (int) $v;
                    break;
                case 'keyword':
                    $keyword = urldecode($v);
                    break;
                case 'start_date':
                    $start_date = $v;
                    break;
                case 'end_date':
                    $end_date = $v;
                    break;
                case 'completion_status':
                    $completion_status = (int) $v;
                    break;
            }
        }

        $user = Auth::user();
        $userRoles = u::query("SELECT role_id FROM role_has_user WHERE user_id = {$user->id}");
        $roleIds = [];
        foreach ($userRoles as $ur) {
            $roleIds[] = $ur->role_id;
        }

        if (in_array(69, $roleIds)) {
            $team_id = $user->id;
        } elseif (in_array(68, $roleIds)) {
            $ec_id = $user->id;
        }

        $cond = " a.status > 0 AND a.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";
        if (!empty($branch_id)) {
            $cond .= " AND a.branch_id IN (" . implode(",", $branch_id) . ")";
        }
        if ($team_id > 0) {
            $cond .= " AND (a.ec_leader_id = $team_id OR (a.ec_leader_id IS NULL AND a.ec_id = $team_id))";
        }
        if ($ec_id > 0) {
            $cond .= " AND a.ec_id = $ec_id";
        }
        if ($keyword !== '') {
            $kw = addslashes($keyword);
            $cond .= " AND (s.lms_code LIKE '%$kw%' OR s.name LIKE '%$kw%' OR s.gud_mobile1 LIKE '%$kw%')";
        }
        if ($completion_status == 1) {
            $cond .= " AND a.debt_amount = 0";
        } elseif ($completion_status == 0) {
            $cond .= " AND a.debt_amount > 0";
        }
        if ($start_date) {
            $cond .= " AND a.created_at >= '$start_date 00:00:00'";
        }
        if ($end_date) {
            $cond .= " AND a.created_at <= '$end_date 23:59:59'";
        }

        $query = "
            SELECT
                DATE(a.created_at) AS date_0,
                IF(a.count_recharge = 0, 'Mới', '') AS status_register,
                'Không' AS up_process,
                tf.name AS course_name,
                s.name AS student_name,
                s.gud_mobile1 AS phone,
                CASE
                    WHEN a.ec_leader_id IS NOT NULL THEN (SELECT u.name FROM users u WHERE u.id = a.ec_leader_id)
                    ELSE (SELECT u.name FROM users u WHERE u.id = a.ec_id)
                END AS team_name,
                s.address,
                a.must_charge,
                IF(a.group_type > 0, CONCAT('Nhóm ', a.group_type), 'Không') AS dk_chung,
                (SELECT amount FROM payments p WHERE p.agreement_id = a.id ORDER BY id ASC LIMIT 1) AS p1_amount,
                (SELECT charge_date FROM payments p WHERE p.agreement_id = a.id ORDER BY id ASC LIMIT 1) AS p1_date,
                (SELECT SUM(amount) FROM payments p WHERE p.agreement_id = a.id) AS total_paid,
                (SELECT MAX(charge_date) FROM payments p WHERE p.agreement_id = a.id) AS last_pay_date,
                a.discount_amount AS discount,
                a.debt_amount,
                a.id AS agreement_id
            FROM agreements AS a
            INNER JOIN students AS s ON s.id = a.student_id
            LEFT JOIN tuition_fee AS tf ON tf.id = a.tuition_fee_id
            WHERE $cond
            ORDER BY a.id DESC
        ";

        $list = u::query($query);
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);

        $title = 'BÁO CÁO DOANH SỐ CHI TIẾT THEO TEAM';
        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells('A1:S1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $headers = ['STT', 'Ngày tạo', 'Trạng thái đăng ký', 'Up quá trình từ', 'Khoá học đăng kí', 'Họ và tên', 'Sđt', 'Team kinh doanh', 'ĐỊA CHỈ NHẬN SÁCH', 'Giá khoá học', 'DK chung', 'Học phí đợt 1', 'Ngày CK 1', 'Học phí đợt 2', 'Ngày CK 2', 'Giảm trừ', 'Công nợ', 'XN Kế toán', 'Lương sale'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S'];
        
        $hStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']]],
        ];
        
        foreach ($headers as $i => $h) {
            $sheet->setCellValue($cols[$i] . '3', $h);
        }
        $sheet->getStyle('A3:S3')->applyFromArray($hStyle);
        $sheet->getRowDimension(3)->setRowHeight(24);

        $widths = [8, 14, 16, 16, 22, 22, 14, 16, 22, 16, 12, 16, 14, 16, 14, 14, 16, 14, 16];
        foreach ($cols as $i => $c) {
            $sheet->getColumnDimension($c)->setWidth($widths[$i]);
        }

        $moneyFmt = '#,##0';
        $borderStyle = ['borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'E0E0E0']]]];
        $centerAlign = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]];
        $leftAlign = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT]];
        $rightAlign = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT]];

        $rowIdx = 4;
        foreach ($list as $idx => $item) {
            $p1_amount = (float)$item->p1_amount;
            $total_paid = (float)$item->total_paid;
            $p2_amount = $total_paid - $p1_amount;
            if ($p2_amount < 0) $p2_amount = 0;
            $p2_date = ($p2_amount > 0) ? $item->last_pay_date : '';
            
            $xn_ketoan = ((float)$item->debt_amount > 0) ? 'R thiếu' : 'R';
            $luong_sale = 0;
            if ((float)$item->debt_amount == 0) {
                $rate = ($item->status_register == 'Mới') ? 0.10 : 0.06;
                $luong_sale = ((float)$item->must_charge) * $rate;
            }

            $sheet->setCellValue('A' . $rowIdx, $idx + 1);
            $sheet->setCellValue('B' . $rowIdx, $item->date_0);
            $sheet->setCellValue('C' . $rowIdx, $item->status_register);
            $sheet->setCellValue('D' . $rowIdx, $item->up_process);
            $sheet->setCellValue('E' . $rowIdx, $item->course_name ?? '—');
            $sheet->setCellValue('F' . $rowIdx, $item->student_name);
            $sheet->setCellValue('G' . $rowIdx, $item->phone);
            $sheet->setCellValue('H' . $rowIdx, $item->team_name ?? '—');
            $sheet->setCellValue('I' . $rowIdx, $item->address ?? '—');
            $sheet->setCellValue('J' . $rowIdx, (float)$item->must_charge);
            $sheet->setCellValue('K' . $rowIdx, $item->dk_chung);
            $sheet->setCellValue('L' . $rowIdx, $p1_amount);
            $sheet->setCellValue('M' . $rowIdx, $item->p1_date);
            $sheet->setCellValue('N' . $rowIdx, $p2_amount);
            $sheet->setCellValue('O' . $rowIdx, $p2_date);
            $sheet->setCellValue('P' . $rowIdx, (float)$item->discount);
            $sheet->setCellValue('Q' . $rowIdx, (float)$item->debt_amount);
            $sheet->setCellValue('R' . $rowIdx, $xn_ketoan);
            $sheet->setCellValue('S' . $rowIdx, (float)$luong_sale);

            $sheet->getStyle("A$rowIdx:S$rowIdx")->applyFromArray($borderStyle);
            $sheet->getStyle("A$rowIdx:D$rowIdx")->applyFromArray($centerAlign);
            $sheet->getStyle("E$rowIdx:I$rowIdx")->applyFromArray($leftAlign);
            $sheet->getStyle("J$rowIdx")->applyFromArray($rightAlign);
            $sheet->getStyle("J$rowIdx")->getNumberFormat()->setFormatCode($moneyFmt);
            $sheet->getStyle("K$rowIdx")->applyFromArray($centerAlign);
            $sheet->getStyle("L$rowIdx")->applyFromArray($rightAlign);
            $sheet->getStyle("L$rowIdx")->getNumberFormat()->setFormatCode($moneyFmt);
            $sheet->getStyle("M$rowIdx")->applyFromArray($centerAlign);
            $sheet->getStyle("N$rowIdx")->applyFromArray($rightAlign);
            $sheet->getStyle("N$rowIdx")->getNumberFormat()->setFormatCode($moneyFmt);
            $sheet->getStyle("O$rowIdx")->applyFromArray($centerAlign);
            
            $sheet->getStyle("P$rowIdx:Q$rowIdx")->applyFromArray($rightAlign);
            $sheet->getStyle("P$rowIdx")->getNumberFormat()->setFormatCode($moneyFmt);
            $sheet->getStyle("Q$rowIdx")->getNumberFormat()->setFormatCode($moneyFmt);
            
            if ((float)$item->debt_amount > 0) {
                $sheet->getStyle("Q$rowIdx")->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFD72B28'));
            }
            
            $sheet->getStyle("R$rowIdx")->applyFromArray($centerAlign);
            if ($xn_ketoan === 'R') {
                $sheet->getStyle("R$rowIdx")->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKGREEN);
            } else {
                $sheet->getStyle("R$rowIdx")->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
            }
            
            $sheet->getStyle("S$rowIdx")->applyFromArray($rightAlign);
            $sheet->getStyle("S$rowIdx")->getNumberFormat()->setFormatCode($moneyFmt);
            $sheet->getStyle("S$rowIdx")->getFont()->setBold(true);

            $rowIdx++;
        }

        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Bao cao doanh so chi tiet theo team.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }
    public function report26(Request $request, $key, $value)
    {
        set_time_limit(300);
        ini_set('memory_limit', '-1');

        $keys = explode(',', $key);
        $values = explode(',', $value);

        $branch_id = [];
        $team_id = 0;
        $ec_id = 0;
        $product_id = 0;
        $class_status = -1;
        $keyword = '';
        $start_date = '';
        $end_date = '';
        $cls_start_date = '';
        $cls_end_date = '';
        $class_keyword = '';

        foreach ($keys as $k => $key_name) {
            $v = $values[$k] ?? '';
            if ($v === 'v') $v = '';
            switch ($key_name) {
                case 'branch_id':
                    $branch_id = $v ? explode('-', $v) : [];
                    break;
                case 'team_id':
                    $team_id = (int) $v;
                    break;
                case 'ec_id':
                    $ec_id = (int) $v;
                    break;
                case 'product_id':
                    $product_id = (int) $v;
                    break;
                case 'class_status':
                    $class_status = (int) $v;
                    break;
                case 'keyword':
                    $keyword = urldecode($v);
                    break;
                case 'class_keyword':
                    $class_keyword = urldecode($v);
                    break;
                case 'start_date':
                    $start_date = $v;
                    break;
                case 'end_date':
                    $end_date = $v;
                    break;
                case 'cls_start_date':
                    $cls_start_date = $v;
                    break;
                case 'cls_end_date':
                    $cls_end_date = $v;
                    break;
            }
        }

        $user = Auth::user();
        $userRoles = u::query("SELECT role_id FROM role_has_user WHERE user_id = {$user->id}");
        $roleIds = [];
        foreach ($userRoles as $ur) {
            $roleIds[] = $ur->role_id;
        }

        if (in_array(69, $roleIds)) {
            $team_id = $user->id; 
        } elseif (in_array(68, $roleIds)) {
            $ec_id = $user->id; 
        }

        $cond = " c.status > 0 AND c.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";

        if (!empty($branch_id)) {
            $cond .= " AND c.branch_id IN (" . implode(",", $branch_id) . ")";
        }
        if ($team_id > 0) {
            $cond .= " AND (c.ec_leader_id = $team_id OR (c.ec_leader_id IS NULL AND c.ec_id = $team_id))";
        }
        if ($ec_id > 0) {
            $cond .= " AND c.ec_id = $ec_id";
        }
        if ($product_id > 0) {
            $cond .= " AND c.product_id = $product_id";
        }
        if ($keyword !== '') {
            $kw = addslashes($keyword);
            $cond .= " AND (s.lms_code LIKE '%$kw%' OR s.name LIKE '%$kw%' OR s.gud_mobile1 LIKE '%$kw%')";
        }
        if ($class_keyword !== '') {
            $kw_cls = addslashes($class_keyword);
            $cond .= " AND (cls.cls_name LIKE '%$kw_cls%' OR cls.id = '$kw_cls')";
        }
        if ($class_status == 1) {
            $cond .= " AND c.class_id > 0";
        } elseif ($class_status == 0) {
            $cond .= " AND (c.class_id = 0 OR c.class_id IS NULL)";
        }
        if ($start_date) {
            $cond .= " AND c.created_at >= '$start_date 00:00:00'";
        }
        if ($end_date) {
            $cond .= " AND c.created_at <= '$end_date 23:59:59'";
        }
        if ($cls_start_date) {
            $cond .= " AND cls.cls_startdate >= '$cls_start_date 00:00:00'";
        }
        if ($cls_end_date) {
            $cond .= " AND cls.cls_startdate <= '$cls_end_date 23:59:59'";
        }

        $query = "
            SELECT
                c.created_at,
                s.name AS student_name,
                s.lms_code,
                s.gud_mobile1 AS phone,
                tf.name AS course_name,
                p.name AS product_name,
                cls.cls_name AS class_name,
                cls.cls_startdate AS start_date,
                u_team.name AS team_name,
                u_ec.name AS ec_name
            FROM contracts AS c
            INNER JOIN students AS s ON s.id = c.student_id
            LEFT JOIN agreements AS a ON a.id = c.agreement_id
            LEFT JOIN tuition_fee AS tf ON tf.id = a.tuition_fee_id
            LEFT JOIN products AS p ON p.id = c.product_id
            LEFT JOIN classes AS cls ON cls.id = c.class_id
            LEFT JOIN users AS u_team ON u_team.id = c.ec_leader_id
            LEFT JOIN users AS u_ec ON u_ec.id = c.ec_id
            WHERE $cond
            ORDER BY c.id DESC
        ";

        $list = u::query($query);
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);

        $title = 'BÁO CÁO CHI TIẾT XẾP LỚP';
        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells('A1:K1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $headers = ['STT', 'Ngày tạo', 'Họ và tên HS', 'Mã HS', 'SĐT', 'Khoá học đăng kí', 'Khóa lẻ', 'Lớp đăng ký', 'Ngày khai giảng', 'Team kinh doanh', 'Thành viên sale'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];
        
        $hStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']]],
        ];
        
        foreach ($headers as $i => $h) {
            $sheet->setCellValue($cols[$i] . '3', $h);
        }
        $sheet->getStyle('A3:K3')->applyFromArray($hStyle);
        $sheet->getRowDimension(3)->setRowHeight(24);

        $widths = [8, 14, 22, 14, 14, 22, 22, 22, 14, 16, 16];
        foreach ($cols as $i => $c) {
            $sheet->getColumnDimension($c)->setWidth($widths[$i]);
        }

        $borderStyle = ['borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'E0E0E0']]]];
        $centerAlign = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]];
        $leftAlign = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT]];

        $rowIdx = 4;
        foreach ($list as $idx => $item) {
            $sheet->setCellValue('A' . $rowIdx, $idx + 1);
            $sheet->setCellValue('B' . $rowIdx, $item->created_at);
            $sheet->setCellValue('C' . $rowIdx, $item->student_name);
            $sheet->setCellValue('D' . $rowIdx, $item->lms_code);
            $sheet->setCellValue('E' . $rowIdx, $item->phone);
            $sheet->setCellValue('F' . $rowIdx, $item->course_name ?? '—');
            $sheet->setCellValue('G' . $rowIdx, $item->product_name ?? '—');
            $sheet->setCellValue('H' . $rowIdx, $item->class_name ?? '—');
            $sheet->setCellValue('I' . $rowIdx, $item->start_date ?? '—');
            $sheet->setCellValue('J' . $rowIdx, $item->team_name ?? '—');
            $sheet->setCellValue('K' . $rowIdx, $item->ec_name ?? '—');

            $sheet->getStyle("A$rowIdx:K$rowIdx")->applyFromArray($borderStyle);
            $sheet->getStyle("A$rowIdx")->applyFromArray($centerAlign);
            $sheet->getStyle("B$rowIdx")->applyFromArray($centerAlign);
            $sheet->getStyle("C$rowIdx")->applyFromArray($leftAlign);
            $sheet->getStyle("D$rowIdx")->applyFromArray($centerAlign);
            $sheet->getStyle("E$rowIdx")->applyFromArray($centerAlign);
            $sheet->getStyle("F$rowIdx:K$rowIdx")->applyFromArray($leftAlign);
            $sheet->getStyle("I$rowIdx")->applyFromArray($centerAlign);

            $rowIdx++;
        }

        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Bao cao chi tiet xep lop.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function reportBookDelivered($keys, $values)
    {
        $arr_key = explode(',', $keys);
        $arr_value = explode(',', $values);
        $req = new \stdClass();
        foreach ($arr_key as $k => $v) {
            $req->$v = $arr_value[$k];
        }

        $cond = " c.class_id IS NOT NULL AND c.class_id > 0 AND c.total_charged > 0 AND s.status > 0 AND s.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")"; 
        if (isset($req->team_id) && $req->team_id > 0) {
            $team_id = (int) $req->team_id;
            $cond .= " AND (c.ec_leader_id = $team_id OR (c.ec_leader_id IS NULL AND c.ec_id = $team_id)) ";
        }
        if (isset($req->keyword) && $req->keyword) {
            $cond .= " AND (s.lms_code LIKE '%" . $req->keyword . "%' OR s.name LIKE '%" . $req->keyword . "%' OR cls.cls_name LIKE '%" . $req->keyword . "%') ";
        }
        if (isset($req->product_id) && $req->product_id) {
            $cond .= " AND c.product_id = '" . $req->product_id . "' ";
        }
        if (isset($req->status) && $req->status !== '') {
            if ($req->status == '1') {
                $cond .= " AND c.book_delivered_date IS NOT NULL ";
            } elseif ($req->status == '0') {
                $cond .= " AND c.book_delivered_date IS NULL ";
            }
        }
        if (isset($req->start_date) && $req->start_date) {
            $cond .= " AND c.book_delivered_date >= '" . $req->start_date . "' ";
        }
        if (isset($req->end_date) && $req->end_date) {
            $cond .= " AND c.book_delivered_date <= '" . $req->end_date . "' ";
        }
        if (isset($req->cls_start_start) && $req->cls_start_start) {
            $cond .= " AND cls.cls_startdate >= '" . $req->cls_start_start . "' ";
        }
        if (isset($req->cls_start_end) && $req->cls_start_end) {
            $cond .= " AND cls.cls_startdate <= '" . $req->cls_start_end . "' ";
        }

        $order_by = " ORDER BY c.id DESC ";

        $query = "SELECT c.id AS contract_id, c.book_delivered_date,
                    s.lms_code, s.name AS student_name, s.address, s.gud_mobile1 AS phone,
                    cls.cls_name, cls.cls_startdate,
                    p.name AS product_name,
                    cp.link_facebook,
                    CASE
                        WHEN c.ec_leader_id IS NOT NULL THEN (SELECT u.name FROM users u WHERE u.id = c.ec_leader_id)
                        ELSE (SELECT u.name FROM users u WHERE u.id = c.ec_id)
                    END AS team_name
                FROM contracts AS c
                    LEFT JOIN students AS s ON c.student_id = s.id
                    LEFT JOIN classes AS cls ON c.class_id = cls.id
                    LEFT JOIN products AS p ON c.product_id = p.id
                    LEFT JOIN crm_parents AS cp ON cp.student_id = s.id
                WHERE $cond 
                $order_by";

        $list = u::query($query);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'BÁO CÁO PHÁT SÁCH');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->mergeCells('A1:G1');

        $headers = ['STT', 'Team kinh doanh', 'Mã HV', 'Họ tên', 'Số điện thoại', 'Lớp học', 'Ngày khai giảng', 'Sản phẩm', 'Địa chỉ nhận sách', 'Link Facebook', 'Ngày phát sách'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '3', $header);
            $sheet->getStyle($col . '3')->getFont()->setBold(true);
            $col++;
        }

        $rowIdx = 4;
        foreach ($list as $index => $item) {
            $sheet->setCellValue('A' . $rowIdx, $index + 1);
            $sheet->setCellValue('B' . $rowIdx, $item->team_name);
            $sheet->setCellValue('C' . $rowIdx, $item->lms_code);
            $sheet->setCellValue('D' . $rowIdx, $item->student_name);
            $sheet->setCellValue('E' . $rowIdx, $item->phone);
            $sheet->setCellValue('F' . $rowIdx, $item->cls_name);
            $sheet->setCellValue('G' . $rowIdx, $item->cls_startdate);
            $sheet->setCellValue('H' . $rowIdx, $item->product_name);
            $sheet->setCellValue('I' . $rowIdx, $item->address);
            $sheet->setCellValue('J' . $rowIdx, $item->link_facebook);
            $sheet->setCellValue('K' . $rowIdx, $item->book_delivered_date);
            $rowIdx++;
        }

        foreach (range('A', 'K') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Bao_cao_phat_sach.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function report28(Request $request, $key, $value)
    {
        $arr_key = explode(",", $key);
        $arr_value = explode(",", $value);
        $params = array_combine($arr_key, $arr_value);

        $branch_id = (isset($params["branch_id"]) && $params["branch_id"] != "") ? explode("-", $params["branch_id"]) : [];
        $team_id = isset($params["team_id"]) ? (int) $params["team_id"] : 0;
        $ec_id = isset($params["ec_id"]) ? (int) $params["ec_id"] : 0;
        $keyword = isset($params["keyword"]) ? urldecode($params["keyword"]) : "";
        $start_date = isset($params["start_date"]) ? $params["start_date"] : "";
        $end_date = isset($params["end_date"]) ? $params["end_date"] : "";
        $completion_status = isset($params["completion_status"]) ? (int) $params["completion_status"] : -1;

        $user = Auth::user();
        $userRoles = u::query("SELECT role_id FROM role_has_user WHERE user_id = {$user->id}");
        $roleIds = [];
        foreach ($userRoles as $ur) {
            $roleIds[] = $ur->role_id;
        }

        if (in_array(69, $roleIds)) {
            $team_id = $user->id; 
        } elseif (in_array(68, $roleIds)) {
            $ec_id = $user->id; 
        }

        $cond = " a.status > 0 AND a.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";

        if (!empty($branch_id)) {
            $cond .= " AND a.branch_id IN (" . implode(",", $branch_id) . ")";
        }
        if ($team_id > 0) {
            $cond .= " AND (a.ec_leader_id = $team_id OR (a.ec_leader_id IS NULL AND a.ec_id = $team_id))";
        }
        if ($ec_id > 0) {
            $cond .= " AND a.ec_id = $ec_id";
        }
        if ($keyword !== "") {
            $kw = addslashes($keyword);
            $cond .= " AND (s.lms_code LIKE '%$kw%' OR s.name LIKE '%$kw%' OR s.gud_mobile1 LIKE '%$kw%')";
        }

        if ($completion_status == 1) {
            $cond .= " AND a.debt_amount = 0";
        } elseif ($completion_status == 0) {
            $cond .= " AND a.debt_amount > 0";
        }
        
        if ($start_date || $end_date) {
            if ($start_date) {
                $cond .= " AND tp.created_at >= '$start_date 00:00:00'";
            }
            if ($end_date) {
                $cond .= " AND tp.created_at <= '$end_date 23:59:59'";
            }
        }

        $query = "
            SELECT
                DATE(tp.created_at) AS date_0,
                IF(a.count_recharge = 0, 'Mới', '') AS status_register,
                tf.name AS course_name,
                s.name AS student_name,
                s.gud_mobile1 AS phone,
                CASE
                    WHEN a.ec_leader_id IS NOT NULL THEN (SELECT u.name FROM users u WHERE u.id = a.ec_leader_id)
                    ELSE (SELECT u.name FROM users u WHERE u.id = a.ec_id)
                END AS team_name,
                s.address,
                a.must_charge,
                tp.charge_amount,
                tp.charge_date,
                tp.status,
                tp.attachments
            FROM tmp_payments AS tp
            INNER JOIN agreements AS a ON a.id = tp.agreement_id
            INNER JOIN students AS s ON s.id = a.student_id
            LEFT JOIN tuition_fee AS tf ON tf.id = a.tuition_fee_id
            WHERE $cond
            ORDER BY tp.id DESC
        ";
        
        $list = u::query($query);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getParent()->getDefaultStyle()->getFont()->setName("Calibri")->setSize(11);

        $sheet->setCellValue("A1", "BÁO CÁO CHI TIẾT PHỤC VỤ XUẤT HÓA ĐƠN");
        $sheet->mergeCells("A1:M1");
        $sheet->getStyle("A1")->applyFromArray(["font" => ["bold" => true, "size" => 14], "alignment" => ["horizontal" => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, "vertical" => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $headers = [
            "A2" => "STT",
            "B2" => "Ngày tạo phiếu",
            "C2" => "Trạng thái đăng ký",
            "D2" => "Khoá học đăng kí",
            "E2" => "Họ và tên",
            "F2" => "Sđt",
            "G2" => "Team kinh doanh",
            "H2" => "ĐỊA CHỈ NHẬN SÁCH",
            "I2" => "Giá khoá học",
            "J2" => "Học phí",
            "K2" => "Ngày chuyển khoản",
            "L2" => "Link Bill",
            "M2" => "Trạng thái duyệt"
        ];
        
        foreach ($headers as $c => $l) {
            $sheet->setCellValue($c, $l);
        }

        $hStyle = ["font" => ["bold" => true], "fill" => ["fillType" => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, "startColor" => ["rgb" => "E8E8E8"]], "alignment" => ["horizontal" => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, "vertical" => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER], "borders" => ["allBorders" => ["borderStyle" => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, "color" => ["rgb" => "BBBBBB"]]]];
        $sheet->getStyle("A2:M2")->applyFromArray($hStyle);
        $sheet->getRowDimension(2)->setRowHeight(22);

        $borderOnly = ["borders" => ["allBorders" => ["borderStyle" => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, "color" => ["rgb" => "DDDDDD"]]]];

        for ($i = 0; $i < count($list); $i++) {
            $x = $i + 3;
            $item = $list[$i];
            
            $status_str = "";
            if ($item->status == 1) $status_str = "Đã duyệt";
            elseif ($item->status == 2) $status_str = "Từ chối";
            else $status_str = "Chờ duyệt";
            
            $bills_links = [];
            if (!empty($item->attachments)) {
                $arr = json_decode($item->attachments, true);
                if (is_array($arr)) {
                    foreach($arr as $path) {
                        $fullUrl = rtrim(env('APP_URL'), '/') . '/' . ltrim($path, '/');
                        $bills_links[] = $fullUrl;
                    }
                }
            }

            $sheet->setCellValue("A" . $x, $i + 1);
            $sheet->setCellValue("B" . $x, $item->date_0);
            $sheet->setCellValue("C" . $x, $item->status_register);
            $sheet->setCellValue("D" . $x, $item->course_name);
            $sheet->setCellValue("E" . $x, $item->student_name);
            $sheet->setCellValueExplicit("F" . $x, $item->phone, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue("G" . $x, $item->team_name);
            $sheet->setCellValue("H" . $x, $item->address);
            $sheet->setCellValue("I" . $x, $item->must_charge);
            $sheet->setCellValue("J" . $x, $item->charge_amount);
            $sheet->setCellValue("K" . $x, $item->charge_date);
            $sheet->setCellValue("L" . $x, implode("\n", $bills_links));
            $sheet->setCellValue("M" . $x, $status_str);

            $sheet->getStyle("A$x:M$x")->applyFromArray($borderOnly);
        }

        foreach (range("A", "M") as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        try {
            header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
            header("Content-Disposition: attachment;filename=\"Bao_cao_chi_tiet_phuc_vu_xuat_hoa_don.xlsx\"");
            header("Cache-Control: max-age=0");
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }

}