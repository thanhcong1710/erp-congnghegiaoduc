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
        }

        $ordered_by = " ORDER BY c.id DESC ";

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
                    (SELECT count(ct.id) FROM contracts ct LEFT JOIN students s ON ct.student_id = s.id WHERE ct.class_id = c.id AND ct.status != 7 AND s.status > 0) AS total_students
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
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Trung tâm');
        $sheet->setCellValue('C1', 'Mã lớp');
        $sheet->setCellValue('D1', 'Team/Sản phẩm');
        $sheet->setCellValue('E1', 'Sĩ số');
        $sheet->setCellValue('F1', 'Max');
        $sheet->setCellValue('G1', 'Trạng thái');
        $sheet->setCellValue('H1', 'Lịch học');
        $sheet->setCellValue('I1', 'Khai giảng');
        $sheet->setCellValue('J1', 'Giáo viên');
        $sheet->setCellValue('K1', 'Trợ giảng');
        $sheet->setCellValue('L1', 'Phòng học');
        $sheet->setCellValue('M1', 'Loại lớp học');

        $sheet->getColumnDimension("A")->setWidth(10);
        $sheet->getColumnDimension("B")->setWidth(30);
        $sheet->getColumnDimension("C")->setWidth(20);
        $sheet->getColumnDimension("D")->setWidth(20);
        $sheet->getColumnDimension("E")->setWidth(10);
        $sheet->getColumnDimension("F")->setWidth(10);
        $sheet->getColumnDimension("G")->setWidth(15);
        $sheet->getColumnDimension("H")->setWidth(25);
        $sheet->getColumnDimension("I")->setWidth(15);
        $sheet->getColumnDimension("J")->setWidth(25);
        $sheet->getColumnDimension("K")->setWidth(25);
        $sheet->getColumnDimension("L")->setWidth(20);
        $sheet->getColumnDimension("M")->setWidth(20);

        for ($i = 0; $i < count($list); $i++) {
            $x = $i + 2;
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
                $days_arr = explode(',', $item->class_day);
                foreach ($days_arr as $d) {
                    $days[] = "T$d";
                }
            }
            $schedule_text = implode('+', $days);
            if ($item->start_time && $item->end_time) {
                $schedule_text .= " (" . substr($item->start_time, 0, 5) . "-" . substr($item->end_time, 0, 5) . ")";
            }
            $start_date = date('d/m/Y', strtotime($item->cls_startdate));

            $sheet->setCellValue('A' . $x, $i + 1);
            $sheet->setCellValue('B' . $x, $item->branch_name);
            $sheet->setCellValue('C' . $x, $item->cls_name);
            $sheet->setCellValue('D' . $x, $item->product_name);
            $sheet->setCellValue('E' . $x, $item->total_students);
            $sheet->setCellValue('F' . $x, $item->max_students);
            $sheet->setCellValue('G' . $x, $status_text);
            $sheet->setCellValue('H' . $x, $schedule_text);
            $sheet->setCellValue('I' . $x, $start_date);
            $sheet->setCellValue('J' . $x, $item->teacher_name);
            $sheet->setCellValue('K' . $x, $item->ta_name);
            $sheet->setCellValue('L' . $x, $item->room_name);
            $sheet->setCellValue('M' . $x, $item->is_online == 1 ? 'Online' : 'Offline');

            $sheet->getRowDimension($x)->setRowHeight(23);
        }
        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Báo cáo lớp học.xlsx"');
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
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Trung tâm');
        $sheet->setCellValue('C1', 'Mã HS');
        $sheet->setCellValue('D1', 'Tên HS');
        $sheet->setCellValue('E1', 'Lớp học');
        $sheet->setCellValue('F1', 'Ngày học');
        $sheet->setCellValue('G1', 'Doanh số');

        $sheet->getColumnDimension("A")->setWidth(10);
        $sheet->getColumnDimension("B")->setWidth(30);
        $sheet->getColumnDimension("C")->setWidth(20);
        $sheet->getColumnDimension("D")->setWidth(30);
        $sheet->getColumnDimension("E")->setWidth(30);
        $sheet->getColumnDimension("F")->setWidth(20);
        $sheet->getColumnDimension("G")->setWidth(20);

        for ($i = 0; $i < count($list); $i++) {
            $x = $i + 2;
            $item = $list[$i];

            $status_text = '';
            $revenue = 0;
            $sheet->setCellValue('A' . $x, $i + 1);
            $sheet->setCellValue('B' . $x, $item->branch_name);
            $sheet->setCellValue('C' . $x, $item->lms_code);
            $sheet->setCellValue('D' . $x, $item->student_name);
            $sheet->setCellValue('E' . $x, $item->class_name);
            $sheet->setCellValue('F' . $x, $item->class_date);
            $sheet->setCellValue('G' . $x, $revenue);

            $sheet->getRowDimension($x)->setRowHeight(23);
        }
        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Báo cáo doanh số chi tiết.xlsx"');
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

        // Set headers
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Trung tâm');
        $sheet->setCellValue('C1', 'Tổng số HS');
        $sheet->setCellValue('D1', 'Số buổi học');
        $sheet->setCellValue('E1', 'Doanh thu (VNĐ)');

        // Set column widths
        $sheet->getColumnDimension("A")->setWidth(10);
        $sheet->getColumnDimension("B")->setWidth(40);
        $sheet->getColumnDimension("C")->setWidth(20);
        $sheet->getColumnDimension("D")->setWidth(20);
        $sheet->getColumnDimension("E")->setWidth(25);

        // Style header row
        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);

        // Fill data
        $totalRevenue = 0;
        $totalStudents = 0;
        $totalSessions = 0;

        for ($i = 0; $i < count($list); $i++) {
            $x = $i + 2;
            $item = $list[$i];

            $sheet->setCellValue('A' . $x, $i + 1);
            $sheet->setCellValue('B' . $x, $item->branch_name);
            $sheet->setCellValue('C' . $x, $item->total_students);
            $sheet->setCellValue('D' . $x, $item->total_sessions);
            $sheet->setCellValue('E' . $x, $item->total_revenue);

            $totalRevenue += $item->total_revenue;
            $totalStudents += $item->total_students;
            $totalSessions += $item->total_sessions;

            $sheet->getRowDimension($x)->setRowHeight(23);
        }

        // Add total row
        $totalRow = count($list) + 2;
        $sheet->setCellValue('A' . $totalRow, '');
        $sheet->setCellValue('B' . $totalRow, 'TỔNG CỘNG:');
        $sheet->setCellValue('C' . $totalRow, $totalStudents);
        $sheet->setCellValue('D' . $totalRow, $totalSessions);
        $sheet->setCellValue('E' . $totalRow, $totalRevenue);

        $totalStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT],
        ];
        $sheet->getStyle('B' . $totalRow . ':E' . $totalRow)->applyFromArray($totalStyle);

        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Báo cáo tổng quan doanh thu theo trung tâm.xlsx"');
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

        if ($keyword !== '') {
            $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%' OR c.code LIKE '%$keyword%') ";
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
                LEFT JOIN users AS u_cm ON u_cm.id = c.cm_id
            WHERE $cond
            $order_by";

        $list = u::query($query);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Mã HS');
        $sheet->setCellValue('C1', 'Tên HS');
        $sheet->setCellValue('D1', 'SĐT');
        $sheet->setCellValue('E1', 'Mã Contract');
        $sheet->setCellValue('F1', 'Trạng thái');
        $sheet->setCellValue('G1', 'Trung tâm');
        $sheet->setCellValue('H1', 'Sản phẩm');
        $sheet->setCellValue('I1', 'Lớp học');
        $sheet->setCellValue('J1', 'Gói phí');
        $sheet->setCellValue('K1', 'EC');
        $sheet->setCellValue('L1', 'CM');
        $sheet->setCellValue('M1', 'Tổng buổi');
        $sheet->setCellValue('N1', 'Đã học');
        $sheet->setCellValue('O1', 'Còn lại');
        $sheet->setCellValue('P1', 'Phải đóng');
        $sheet->setCellValue('Q1', 'Đã đóng');
        $sheet->setCellValue('R1', 'Nợ');
        $sheet->setCellValue('S1', 'Số tiền còn lại');

        // Set column widths
        $sheet->getColumnDimension("A")->setWidth(8);
        $sheet->getColumnDimension("B")->setWidth(15);
        $sheet->getColumnDimension("C")->setWidth(25);
        $sheet->getColumnDimension("D")->setWidth(15);
        $sheet->getColumnDimension("E")->setWidth(15);
        $sheet->getColumnDimension("F")->setWidth(15);
        $sheet->getColumnDimension("G")->setWidth(25);
        $sheet->getColumnDimension("H")->setWidth(20);
        $sheet->getColumnDimension("I")->setWidth(25);
        $sheet->getColumnDimension("J")->setWidth(25);
        $sheet->getColumnDimension("K")->setWidth(25);
        $sheet->getColumnDimension("L")->setWidth(25);
        $sheet->getColumnDimension("M")->setWidth(12);
        $sheet->getColumnDimension("N")->setWidth(12);
        $sheet->getColumnDimension("O")->setWidth(12);
        $sheet->getColumnDimension("P")->setWidth(18);
        $sheet->getColumnDimension("Q")->setWidth(18);
        $sheet->getColumnDimension("R")->setWidth(18);
        $sheet->getColumnDimension("S")->setWidth(20);

        // Style header row
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ]
        ];
        $sheet->getStyle('A1:S1')->applyFromArray($headerStyle);

        // Fill data and calculate totals
        $totalLeftAmount = 0;
        $totalCharged = 0;
        $totalMustCharge = 0;
        $totalDebt = 0;
        $totalLeftSessions = 0;
        $totalSummarySessions = 0;

        for ($i = 0; $i < count($list); $i++) {
            $x = $i + 2;
            $item = $list[$i];

            // Get status label
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
            $sheet->setCellValue('L' . $x, $item->cm_name);
            $sheet->setCellValue('M' . $x, $item->summary_sessions);
            $sheet->setCellValue('N' . $x, $item->done_sessions);
            $sheet->setCellValue('O' . $x, $item->left_sessions);
            $sheet->setCellValue('P' . $x, $item->must_charge);
            $sheet->setCellValue('Q' . $x, $item->total_charged);
            $sheet->setCellValue('R' . $x, $item->debt_amount);
            $sheet->setCellValue('S' . $x, $item->left_amount);

            // Accumulate totals
            $totalLeftAmount += $item->left_amount;
            $totalCharged += $item->total_charged;
            $totalMustCharge += $item->must_charge;
            $totalDebt += $item->debt_amount;
            $totalLeftSessions += $item->left_sessions;
            $totalSummarySessions += $item->summary_sessions;

            $sheet->getRowDimension($x)->setRowHeight(23);
        }

        // Add total row
        $totalRow = count($list) + 2;
        $sheet->setCellValue('A' . $totalRow, '');
        $sheet->setCellValue('B' . $totalRow, '');
        $sheet->setCellValue('C' . $totalRow, '');
        $sheet->setCellValue('D' . $totalRow, '');
        $sheet->setCellValue('E' . $totalRow, '');
        $sheet->setCellValue('F' . $totalRow, '');
        $sheet->setCellValue('G' . $totalRow, '');
        $sheet->setCellValue('H' . $totalRow, '');
        $sheet->setCellValue('I' . $totalRow, '');
        $sheet->setCellValue('J' . $totalRow, '');
        $sheet->setCellValue('K' . $totalRow, '');
        $sheet->setCellValue('L' . $totalRow, 'TỔNG CỘNG:');
        $sheet->setCellValue('M' . $totalRow, $totalSummarySessions);
        $sheet->setCellValue('N' . $totalRow, '');
        $sheet->setCellValue('O' . $totalRow, $totalLeftSessions);
        $sheet->setCellValue('P' . $totalRow, $totalMustCharge);
        $sheet->setCellValue('Q' . $totalRow, $totalCharged);
        $sheet->setCellValue('R' . $totalRow, $totalDebt);
        $sheet->setCellValue('S' . $totalRow, $totalLeftAmount);

        $totalStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E8F5E9']
            ]
        ];
        $sheet->getStyle('L' . $totalRow . ':S' . $totalRow)->applyFromArray($totalStyle);

        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Báo cáo số tiền còn lại theo contracts.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}

