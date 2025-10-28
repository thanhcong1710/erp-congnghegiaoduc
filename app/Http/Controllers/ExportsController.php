<?php

namespace App\Http\Controllers;

use App\Providers\UtilityServiceProvider as u;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as x;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Illuminate\Http\Request;
use App\Models\ProcessExcel;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ExportsController extends Controller
{
    public function import(Request $request , $import_id) {
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
            '0'=>'Chưa xử lý',
            '1'=>'Đã kiểm tra dữ liệu đầu vào',
            '2'=>'Dữ liệu đầu vào không hợp lệ',
            '3'=>'Trùng lặp dữ liệu trong file import',
            '4'=>'Trùng lặp dữ liệu khách hàng đang chăm sóc',
            '6'=> 'Đã import thành công'
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
        for ($i = 0; $i < count($parents) ; $i++) {
            $x = $i + 2;
            $sheet->setCellValue('A' . $x, $parents[$i]->name);
            $sheet->setCellValue('B' . $x, $parents[$i]->gud_mobile1 ? "'".$parents[$i]->gud_mobile1 : $parents[$i]->gud_mobile1);
            $sheet->setCellValue('C' . $x, $parents[$i]->gud_mobile2 ? "'".$parents[$i]->gud_mobile2 : $parents[$i]->gud_mobile2);
            $sheet->setCellValue('D' . $x, $parents[$i]->email);
            $sheet->setCellValue('E' . $x, $parents[$i]->address);
            $sheet->setCellValue('F' . $x, $parents[$i]->note);
            $sheet->setCellValue('G' . $x, $parents[$i]->owner_hrm);
            $sheet->setCellValue('H' . $x, $parents[$i]->student_name_1);
            $sheet->setCellValue('I' . $x, $parents[$i]->student_birthday_1? "'".$parents[$i]->student_birthday_1 : $parents[$i]->student_birthday_1);
            $sheet->setCellValue('J' . $x, $parents[$i]->student_name_2);
            $sheet->setCellValue('K' . $x, $parents[$i]->student_birthday_2 ? "'".$parents[$i]->student_birthday_2 : $parents[$i]->student_birthday_2);
            $sheet->setCellValue('L' . $x, $arr_status[$parents[$i]->status]);
            $sheet->setCellValue('M' . $x, $parents[$i]->error_message);

        }
        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Kết quả import - ID '.$import_id.'.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function report01(Request $request , $key,$value) {
        set_time_limit(300);
        ini_set('memory_limit', '-1');
        $arr_key =explode(',',$key);
        $arr_value =explode(',',$value);
        $report_month = date('Y-m');
        foreach($arr_key AS $k=>$key){
            if($key=='start_date'){
                $report_month = $arr_value[$k];
            }
        }
        if ($report_month == date('Y-m')){
            $cond = " AND c.branch_id IN (" . Auth::user()->getBranchesHasUser().")";
            foreach($arr_key AS $k=>$key){
                if($key=='keyword'){
                    $keyword = $arr_value[$k];
                    $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%')";
                }
                if($key=='branch_id'){
                    $cond .=  " AND c.branch_id IN (".str_replace("-",",", $arr_value[$k]).")";
                }
            }
            $end_date = date('Y-m-d');
            $order_by = " ORDER BY c.id DESC ";
            $list = u::query("SELECT DISTINCT c.id, b.name AS branch_name, s.lms_code, s.name, s.gud_name1, cls.cls_name, p.name AS product_name,
                CONCAT (u.hrm_id, ' - ', u.name) AS cm_name, t.name AS tuition_fee_name,
                IF(c.count_recharge=0, 'NEW', 'RENEW') AS type_fee, c.last_done_sessions, c.done_sessions,c.summary_sessions,c.enrolment_last_date AS end_date,
                (SELECT class_date FROM schedule_has_student WHERE contract_id=c.id ORDER BY class_date ASC LIMIT 1) AS start_date
            FROM
                contracts c
                LEFT JOIN students s ON c.student_id = s.id
                LEFT JOIN branches AS b ON b.id = c.branch_id
                LEFT JOIN classes cls ON c.class_id = cls.id
                LEFT JOIN products AS p ON p.id = c.product_id
                LEFT JOIN tuition_fee AS t ON t.id = c.init_tuition_fee_id
                LEFT JOIN term_student_user AS tsu ON tsu.student_id = c.student_id
                LEFT JOIN users u ON tsu.cm_id = u.id
            WHERE
                c.type > 0
                AND c.`status` < 7
                AND (
                    c.class_id IS NOT NULL
                    AND ( ( c.enrolment_start_date <= ( SELECT class_date FROM schedules WHERE class_id = c.class_id AND class_date >= '$end_date' AND `status`=1 ORDER BY class_date ASC LIMIT 1 )
                        AND c.enrolment_last_date >= ( SELECT class_date FROM schedules WHERE class_id = c.class_id AND class_date <= '$end_date' AND `status`=1 ORDER BY class_date ASC LIMIT 1 )
                        ) OR (SELECT count(id) FROM class_transfer WHERE student_id=s.id AND `status` = 2 AND transfer_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)) > 0
                    )
                )
                AND (SELECT count(id) FROM reserves WHERE contract_id=c.id AND is_reserved=1 AND `start_date` <= '$end_date' AND `end_date`>='$end_date' AND `status`=4) =0
                AND s.status > 0
            $cond $order_by");
        }else {
            $cond = " r.branch_id IN (" . Auth::user()->getBranchesHasUser().")";
            foreach($arr_key AS $k=>$key){
                if($key=='keyword'){
                    $keyword = $arr_value[$k];
                    $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%')";
                }
                if($key=='start_date'){
                    $cond .= " AND r.report_month = '$arr_value[$k]'";
                }
                if($key=='branch_id'){
                    $cond .=  " AND r.branch_id IN (".str_replace("-",",", $arr_value[$k]).")";
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
        }

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
        for ($i = 0; $i < count($list) ; $i++) {
            $x = $i + 2;
            $sheet->setCellValue('A' . $x, $list[$i]->branch_name);
            $sheet->setCellValue('B' . $x, $list[$i]->lms_code) ;
            $sheet->setCellValue('C' . $x, $list[$i]->name );
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

    public function report02a(Request $request , $key,$value) {
        set_time_limit(300);
        ini_set('memory_limit', '-1');
        $cond = " r.branch_id IN (" . Auth::user()->getBranchesHasUser().")";
        $arr_key =explode(',',$key);
        $arr_value =explode(',',$value);
        foreach($arr_key AS $k=>$key){
            if($key=='keyword'){
                $keyword = $arr_value[$k];
                $cond .= " AND (p.name LIKE '%$keyword%' OR p.mobile_1 LIKE '%$keyword%' OR p.mobile_2 LIKE '%$keyword%')";
            }
            if($key=='branch_id'){
                $cond .=  " AND r.branch_id IN (".str_replace("-",",", $arr_value[$k]).")";
            }
            if($key=='cm_id'){
                $cond .=  " AND r.cm_id = ".$arr_value[$k];
            }
            if($key=='class_id'){
                $cond .=  " AND r.class_id = ".$arr_value[$k];
            }
            if($key=='start_date'){
                $cond .=  " AND r.last_date >= '".$arr_value[$k]."'";
            }
            if($key=='end_date'){
                $cond .=  " AND r.last_date <= '".$arr_value[$k]."'";
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
        for ($i = 0; $i < count($list) ; $i++) {
            $x = $i + 2;
            $sheet->setCellValue('A' . $x, $list[$i]->branch_name);
            $sheet->setCellValue('B' . $x, $list[$i]->lms_code) ;
            $sheet->setCellValue('C' . $x, $list[$i]->student_name );
            $sheet->setCellValue('D' . $x, $list[$i]->product_name);
            $sheet->setCellValue('E' . $x, $list[$i]->class_name);
            $sheet->setCellValue('F' . $x, $list[$i]->last_date);
            $sheet->setCellValue('G' . $x, $list[$i]->status_title);
            $sheet->setCellValue('H' . $x, $list[$i]->status==1 ? $list[$i]->tuition_fee_name : '');
            $sheet->setCellValue('I' . $x, $list[$i]->status==1 ? $$list[$i]->renew_amount : '');
            $sheet->setCellValue('J' . $x, $list[$i]->cm_name);
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

    public function report02b(Request $request , $key,$value) {
        set_time_limit(300);
        ini_set('memory_limit', '-1');
        $cond = " b.id IN (" . Auth::user()->getBranchesHasUser().")";
        $arr_key =explode(',',$key);
        $arr_value =explode(',',$value);
        $start_date = date('Y-m');
        foreach($arr_key AS $k=>$key){
            if($key=='start_date'){
                $start_date = $arr_value[$k];
            }
            if($key=='branch_id'){
                $cond .=  " AND b.id IN (".str_replace("-",",", $arr_value[$k]).")";
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
        for ($i = 0; $i < count($list) ; $i++) {
            $x = $i + 2;
            $sheet->setCellValue('A' . $x, $list[$i]->branch_name);
            $sheet->setCellValue('B' . $x, $list[$i]->total_item) ;
            $sheet->setCellValue('C' . $x, $list[$i]->success_item );
            $sheet->setCellValue('D' . $x, $list[$i]->total_item ? floor($list[$i]->success_item*100 / $list[$i]->total_item) :'--');
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

    public function report02c(Request $request , $key,$value) {
        set_time_limit(300);
        ini_set('memory_limit', '-1');
        $branch_query =  Auth::user()->getBranchesHasUser();
        $arr_key =explode(',',$key);
        $arr_value =explode(',',$value);
        $start_date = date('Y-m');
        foreach($arr_key AS $k=>$key){
            if($key=='start_date'){
                $start_date = $arr_value[$k];
            }
            if($key=='branch_id' && $arr_value[$k]){
                $branch_query=  str_replace("-",",", $arr_value[$k]);
            }
        }

        $renewSql = "SELECT COUNT(r.id) FROM report_renews AS r LEFT JOIN students AS s ON s.id=r.student_id WHERE s.status>0 AND r.cm_id = ru.user_id AND r.`disabled` = 0 AND r.renewed_month = '$start_date' AND r.branch_id IN ($branch_query)";
        $renewSqlAmount = "SELECT SUM(r.renew_amount) FROM report_renews AS r LEFT JOIN students AS s ON s.id=r.student_id WHERE s.status>0 AND r.cm_id = ru.user_id AND r.`disabled` = 0 AND r.renewed_month = '$start_date' AND r.branch_id IN ($branch_query)";
        $order_by = " ORDER BY b.id DESC ";
        $list = u::query("SELECT b.name AS branch_name, CONCAT(u.name, ' - ', u.hrm_id )AS cm_name, u.id AS cm_id, b.id AS branch_id,
            (SELECT ro.`name` FROM roles AS ro WHERE ru.role_id = ro.id LIMIT 1 ) role_name,
            ($renewSql AND r.status >0) total_item,
            ($renewSql AND r.status=1) success_item,
            ($renewSqlAmount AND r.status=1) renew_amount
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
        $sheet->setCellValue('F1', 'Tỷ lệ tái tục (%)');
        $sheet->setCellValue('G1', 'Doanh thu Renew');

        $sheet->getColumnDimension("A")->setWidth(30);
        $sheet->getColumnDimension("B")->setWidth(30);
        $sheet->getColumnDimension("C")->setWidth(30);
        $sheet->getColumnDimension("D")->setWidth(30);
        $sheet->getColumnDimension("E")->setWidth(30);
        $sheet->getColumnDimension("F")->setWidth(30);
        $sheet->getColumnDimension("G")->setWidth(30);
        for ($i = 0; $i < count($list) ; $i++) {
            $x = $i + 2;
            $sheet->setCellValue('A' . $x, $list[$i]->branch_name);
            $sheet->setCellValue('B' . $x, $list[$i]->cm_name);
            $sheet->setCellValue('C' . $x, $list[$i]->role_name);
            $sheet->setCellValue('D' . $x, $list[$i]->total_item) ;
            $sheet->setCellValue('E' . $x, $list[$i]->success_item );
            $sheet->setCellValue('F' . $x, $list[$i]->total_item ? floor($list[$i]->success_item*100 / $list[$i]->total_item) :'--');
            $sheet->setCellValue('G' . $x, $list[$i]->renew_amount );
        }
        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Báo cáo học sinh tái phí theo CM.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function report05(Request $request , $key,$value) {
        set_time_limit(300);
        ini_set('memory_limit', '-1');
        $branch_query =  Auth::user()->getBranchesHasUser();
        $arr_key =explode(',',$key);
        $arr_value =explode(',',$value);
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-d');
        foreach($arr_key AS $k=>$key){
            if($key=='start_date'){
                $start_date = $arr_value[$k];
            }
            if($key=='end_date'){
                $end_date = $arr_value[$k];
            }
            if($key=='branch_id' && $arr_value[$k]){
                $branch_query=  str_replace("-",",", $arr_value[$k]);
            }
        }

        $order_by = " ORDER BY b.id DESC ";
        $list = u::query("SELECT b.name AS branch_name,
                (SELECT count(id) FROM contracts WHERE type=0 AND class_id IS NOT NULL AND enrolment_start_date >='$start_date' AND enrolment_start_date <= '$end_date' AND branch_id=b.id) AS num_trial,
                (SELECT count(DISTINCT p.contract_id) FROM payments AS p WHERE p.debt=0 AND p.charge_date >= '$start_date' AND p.charge_date <= '$end_date' AND p.branch_id=b.id ) AS num_full_fee,
                (SELECT count(DISTINCT p.contract_id) FROM payments AS p WHERE p.debt>0 AND (SELECT count(id) FROM payments WHERE contract_id=p.contract_id AND debt=0)=0 AND
                    p.charge_date >= '$start_date' AND p.charge_date <= '$end_date' AND p.branch_id=b.id ) AS num_deposit,
                (SELECT SUM(amount) FROM payments AS p WHERE p.charge_date >= '$start_date' AND p.charge_date <= '$end_date' AND p.branch_id=b.id ) AS total_amount
            FROM branches AS b 
            WHERE b.id IN ($branch_query) AND b.status=1 $order_by");
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Trung tâm');
        $sheet->setCellValue('B1', 'Số học sinh trial');
        $sheet->setCellValue('C1', 'Số học sinh full fee mới');
        $sheet->setCellValue('D1', 'Số học sinh cọc mới');
        $sheet->setCellValue('E1', 'Doanh thu');
        $sheet->setCellValue('F1', 'Doanh thu USD');
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => Color::COLOR_BLACK],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ]
        ];
        
        // Áp dụng cho dòng 1 từ A1 đến F1
        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

        $sheet->getColumnDimension("A")->setWidth(30);
        $sheet->getColumnDimension("B")->setWidth(30);
        $sheet->getColumnDimension("C")->setWidth(30);
        $sheet->getColumnDimension("D")->setWidth(30);
        $sheet->getColumnDimension("E")->setWidth(30);
        $sheet->getColumnDimension("F")->setWidth(30);
        for ($i = 0; $i < count($list) ; $i++) {
            $x = $i + 2;
            $sheet->setCellValue('A' . $x, $list[$i]->branch_name);
            $sheet->setCellValue('B' . $x, $list[$i]->num_trial);
            $sheet->setCellValue('C' . $x, $list[$i]->num_full_fee);
            $sheet->setCellValue('D' . $x, $list[$i]->num_deposit) ;
            $sheet->setCellValue('E' . $x, $list[$i]->total_amount );
            $sheet->setCellValue('F' . $x, floor($list[$i]->total_amount / 25000));
        }
        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Báo cáo tổng theo thời gian.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function report103(Request $request , $key,$value) {
        set_time_limit(300);
        ini_set('memory_limit', '-1');
        $branch_query =  Auth::user()->getBranchesHasUser();
        $arr_key =explode(',',$key);
        $arr_value =explode(',',$value);
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-d');
        foreach($arr_key AS $k=>$key){
            if($key=='start_date'){
                $start_date = $arr_value[$k];
            }
            if($key=='end_date'){
                $end_date = $arr_value[$k];
            }
            if($key=='branch_id' && $arr_value[$k]){
                $branch_query=  str_replace("-",",", $arr_value[$k]);
            }
        }
        $cond ="";
        if($start_date){
            $cond.= " AND csc.checkin_at >= '$start_date 00:00:00'";
        }
        if($end_date){
            $cond.= " AND csc.checkin_at <= '$end_date 23:59:59'";
        }

        $order_by = " ORDER BY s.id DESC ";
        $list = u::query("SELECT s.name, ss.lms_id, csc.checkin_at AS checkined_at, CONCAT(u.hrm_id, '-', u.name) AS ec_name, 
                b.name AS branch_name, p.name AS parent_name, p.mobile_1 AS parent_mobile,
                IF(ss.id IS NOT NULL, ss.date_of_birth, s.birthday) AS birthday
            FROM crm_student_checkin AS csc 
                LEFT JOIN crm_students AS s ON s.id=csc.crm_student_id   
                LEFT JOIN crm_parents AS p ON p.id = s.parent_id  
                LEFT JOIN students AS ss ON ss.id=s.lms_id
                LEFT JOIN users AS u ON u.id =csc.checkin_owner_id
                LEFT JOIN branches AS b ON b.id =csc.checkin_branch_id
            WHERE csc.checkin_branch_id IN ($branch_query) AND csc.status >= 2 $cond $order_by");
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Ngày checkin');
        $sheet->setCellValue('C1', 'Họ và tên');
        $sheet->setCellValue('D1', 'Mã LMS');
        $sheet->setCellValue('E1', 'Ngày sinh');
        $sheet->setCellValue('F1', 'Phụ huynh');
        $sheet->setCellValue('G1', 'SĐT');
        $sheet->setCellValue('H1', 'Sale');
        $sheet->setCellValue('I1', 'Trung tâm');
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => Color::COLOR_BLACK],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ]
        ];
        
        // Áp dụng cho dòng 1 từ A1 đến F1
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);

        $sheet->getColumnDimension("A")->setWidth(5);
        $sheet->getColumnDimension("B")->setWidth(20);
        $sheet->getColumnDimension("C")->setWidth(30);
        $sheet->getColumnDimension("D")->setWidth(20);
        $sheet->getColumnDimension("E")->setWidth(20);
        $sheet->getColumnDimension("F")->setWidth(30);
        $sheet->getColumnDimension("G")->setWidth(20);
        $sheet->getColumnDimension("H")->setWidth(30);
        $sheet->getColumnDimension("I")->setWidth(30);
        for ($i = 0; $i < count($list) ; $i++) {
            $x = $i + 2;
            $sheet->setCellValue('A' . $x, $i+1);
            $sheet->setCellValue('B' . $x, $list[$i]->checkined_at);
            $sheet->setCellValue('C' . $x, $list[$i]->name);
            $sheet->setCellValue('D' . $x, $list[$i]->lms_id) ;
            $sheet->setCellValue('E' . $x, $list[$i]->birthday);
            $sheet->setCellValue('F' . $x, $list[$i]->parent_name);
            $sheet->setCellValue('G' . $x, $list[$i]->parent_mobile);
            $sheet->setCellValue('H' . $x, $list[$i]->ec_name );
            $sheet->setCellValue('I' . $x, $list[$i]->branch_name);
        }
        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Báo cáo checkin.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function report104(Request $request , $key,$value) {
        set_time_limit(300);
        ini_set('memory_limit', '-1');
        $branch_query =  Auth::user()->getBranchesHasUser();
        $arr_key =explode(',',$key);
        $arr_value =explode(',',$value);
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-d');
        foreach($arr_key AS $k=>$key){
            if($key=='start_date'){
                $start_date = $arr_value[$k];
            }
            if($key=='end_date'){
                $end_date = $arr_value[$k];
            }
            if($key=='branch_id' && $arr_value[$k]){
                $branch_query=  str_replace("-",",", $arr_value[$k]);
            }
        }
        $cond ="";
        if($start_date){
            $cond.= " AND c.enrolment_start_date >= '$start_date'";
        }
        if($end_date){
            $cond.= " AND c.enrolment_start_date <= '$end_date'";
        }
        $order_by = " ORDER BY c.enrolment_start_date DESC ";
        $list = u::query("SELECT s.name, s.lms_id, CONCAT(u.hrm_id, '-', u.name) AS ec_name, 
                b.name AS branch_name, c.enrolment_start_date, c.enrolment_last_date,
                (SELECT `name` FROM programs WHERE id=p.parent_id) AS `level`,
                (SELECT count(id) FROM contracts WHERE student_id=c.student_id AND `type`>0 AND `status`>0) AS num_contract
            FROM contracts AS c 
                LEFT JOIN students AS s ON s.id = c.student_id
                LEFT JOIN users AS u ON u.id=c.ec_id
                LEFT JOIN branches AS b ON b.id=c.branch_id
                LEFT JOIN programs As p ON p.id=c.program_id
            WHERE c.branch_id IN ($branch_query) AND c.type=0  AND c.class_id IS NOT NULL $cond $order_by");
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Họ và tên');
        $sheet->setCellValue('C1', 'Mã LMS');
        $sheet->setCellValue('D1', 'Sale');
        $sheet->setCellValue('E1', 'Trung tâm');
        $sheet->setCellValue('F1', 'Ngày bắt đầu trial');
        $sheet->setCellValue('G1', 'Ngày kết thúc trial');
        $sheet->setCellValue('H1', 'Trình độ');
        $sheet->setCellValue('I1', 'Ghi chú');
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => Color::COLOR_BLACK],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ]
        ];
        
        // Áp dụng cho dòng 1 từ A1 đến F1
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);

        $sheet->getColumnDimension("A")->setWidth(5);
        $sheet->getColumnDimension("B")->setWidth(30);
        $sheet->getColumnDimension("C")->setWidth(30);
        $sheet->getColumnDimension("D")->setWidth(30);
        $sheet->getColumnDimension("E")->setWidth(30);
        $sheet->getColumnDimension("F")->setWidth(30);
        $sheet->getColumnDimension("G")->setWidth(30);
        $sheet->getColumnDimension("H")->setWidth(30);
        $sheet->getColumnDimension("I")->setWidth(30);
        for ($i = 0; $i < count($list) ; $i++) {
            $x = $i + 2;
            $sheet->setCellValue('A' . $x, $i+1);
            $sheet->setCellValue('B' . $x, $list[$i]->name);
            $sheet->setCellValue('C' . $x, $list[$i]->lms_id);
            $sheet->setCellValue('D' . $x, $list[$i]->ec_name) ;
            $sheet->setCellValue('E' . $x, $list[$i]->branch_name );
            $sheet->setCellValue('F' . $x, $list[$i]->enrolment_start_date);
            $sheet->setCellValue('G' . $x, $list[$i]->enrolment_last_date);
            $sheet->setCellValue('H' . $x, $list[$i]->level);
            $sheet->setCellValue('I' . $x, $list[$i]->num_contract>0 ?'Đã có gói chính thức' : '');

        }
        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Báo cáo học thử.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function report202(Request $request , $key,$value) {
        set_time_limit(300);
        ini_set('memory_limit', '-1');
        $branch_query =  Auth::user()->getBranchesHasUser();
        $arr_key =explode(',',$key);
        $arr_value =explode(',',$value);
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-d');
        foreach($arr_key AS $k=>$key){
            if($key=='start_date'){
                $start_date = $arr_value[$k];
            }
            if($key=='end_date'){
                $end_date = $arr_value[$k];
            }
            if($key=='branch_id' && $arr_value[$k]){
                $branch_query=  str_replace("-",",", $arr_value[$k]);
            }
        }
        $cond ="";
        if($start_date){
            $cond.= " AND p.charge_date >= '$start_date'";
        }
        if($end_date){
            $cond.= " AND p.charge_date <= '$end_date'";
        }
        $order_by = " ORDER BY p.charge_date DESC ";
        $list = u::query("SELECT p.charge_date AS payment_date, s.name AS stu_name, s.lms_id AS std_id, c.branch_id, c.product_id, c.program_id,
                p.must_charge AS total_fee, p.amount AS payment_amount, p.debt AS remaining_amount, 
                p.total, t.number_of_months, t.session, c.start_date, p.contract_id,
                (SELECT min(charge_date) FROM payments WHERE contract_id = p.contract_id) AS period_from,
                (SELECT max(charge_date) FROM payments WHERE contract_id = p.contract_id) AS period_to, 
                c.note, p.method, b.name AS branch_name, pd.name AS product_name,
                (SELECT `name` FROM programs WHERE id =pg.parent_id) AS `level`,
                DATE_FORMAT(p.charge_date, '%Y-%m') AS report_month
            FROM payments AS p 
                LEFT JOIN contracts AS c ON c.id=p.contract_id 
                LEFT JOIN students AS s ON s.id=p.student_id
                LEFT JOIN branches AS b ON b.id=p.branch_id
                LEFT JOIN products AS pd ON pd.id=c.product_id
                LEFT JOIN tuition_fee AS t ON t.id =c.init_tuition_fee_id
                LEFT JOIN programs AS pg ON pg.id= c.program_id
            WHERE p.branch_id IN ($branch_query) AND s.lms_id IS NOT NULL $cond $order_by");
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Ngày thanh toán');
        $sheet->setCellValue('C1', 'Tên học sinh');
        $sheet->setCellValue('D1', 'Mã LMS');
        $sheet->setCellValue('E1', 'Trung tâm');
        $sheet->setCellValue('F1', 'Khoá học');
        $sheet->setCellValue('G1', 'Trình độ');
        $sheet->setCellValue('H1', 'Tổng gói phí');
        $sheet->setCellValue('I1', 'Đã trả');
        $sheet->setCellValue('J1', 'Loại thanh toán');
        $sheet->setCellValue('K1', 'Học phí còn lại');
        $sheet->setCellValue('L1', 'Gói phí');
        $sheet->setCellValue('M1', 'Thời điểm tính doanh số');
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => Color::COLOR_BLACK],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ]
        ];
        
        // Áp dụng cho dòng 1 từ A1 đến F1
        $sheet->getStyle('A1:M1')->applyFromArray($headerStyle);

        $sheet->getColumnDimension("A")->setWidth(5);
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
        for ($i = 0; $i < count($list) ; $i++) {
            $x = $i + 2;
            $sheet->setCellValue('A' . $x, $i+1);
            $sheet->setCellValue('B' . $x, $list[$i]->payment_date);
            $sheet->setCellValue('C' . $x, $list[$i]->stu_name);
            $sheet->setCellValue('D' . $x, $list[$i]->std_id) ;
            $sheet->setCellValue('E' . $x, $list[$i]->branch_name );
            $sheet->setCellValue('F' . $x, $list[$i]->product_name);
            $sheet->setCellValue('G' . $x, $list[$i]->level);
            $sheet->setCellValue('H' . $x, $list[$i]->total_fee);
            $sheet->setCellValue('I' . $x, $list[$i]->payment_amount);
            $sheet->setCellValue('J' . $x, $list[$i]->remaining_amount > 0 ? 'Deposit' : ($list[$i]->payment_amount== $list[$i]->total_fee ? 'Full Fee' : 'Final Payment'));
            $sheet->setCellValue('K' . $x, $list[$i]->remaining_amount);
            $sheet->setCellValue('L' . $x, $list[$i]->number_of_months." tháng");
            $sheet->setCellValue('M' . $x, $list[$i]->report_month);

        }
        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Báo cáo đối soát Creverse.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function report203(Request $request , $key,$value) {
        set_time_limit(300);
        ini_set('memory_limit', '-1');
        $branch_query =  Auth::user()->getBranchesHasUser();
        $arr_key =explode(',',$key);
        $arr_value =explode(',',$value);
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-d');
        foreach($arr_key AS $k=>$key){
            if($key=='start_date'){
                $start_date = $arr_value[$k];
            }
            if($key=='end_date'){
                $end_date = $arr_value[$k];
            }
            if($key=='branch_id' && $arr_value[$k]){
                $branch_query=  str_replace("-",",", $arr_value[$k]);
            }
        }
        $cond ="";
        if($start_date){
            $cond.= " AND p.charge_date >= '$start_date'";
        }
        if($end_date){
            $cond.= " AND p.charge_date <= '$end_date'";
        }
        $order_by = " ORDER BY p.charge_date DESC ";
        $list = u::query("SELECT p.charge_date AS payment_date, s.name AS stu_name, s.lms_id AS std_id, c.branch_id, c.product_id, c.program_id,
                p.must_charge AS total_fee, p.amount AS payment_amount, p.debt AS remaining_amount, 
                p.total, t.number_of_months, t.session, c.start_date, p.contract_id,
                (SELECT min(charge_date) FROM payments WHERE contract_id = p.contract_id) AS period_from,
                (SELECT max(charge_date) FROM payments WHERE contract_id = p.contract_id) AS period_to, 
                c.note, p.method, b.name AS branch_name, pd.name AS product_name,
                (SELECT `name` FROM programs WHERE id =pg.parent_id) AS `level`,
                DATE_FORMAT(p.charge_date, '%Y-%m') AS report_month, s.nick, s.gud_mobile1, s.gud_name1,
                c.count_recharge, (SELECT CONCAT(hrm_id, '-', name) FROM users WHERE id=p.ec_id) AS ec_name 
            FROM payments AS p 
                LEFT JOIN contracts AS c ON c.id=p.contract_id 
                LEFT JOIN students AS s ON s.id=p.student_id
                LEFT JOIN branches AS b ON b.id=p.branch_id
                LEFT JOIN products AS pd ON pd.id=c.product_id
                LEFT JOIN tuition_fee AS t ON t.id =c.init_tuition_fee_id
                LEFT JOIN programs AS pg ON pg.id= c.program_id
            WHERE p.branch_id IN ($branch_query) AND s.lms_id IS NOT NULL $cond $order_by");
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Trung tâm');
        $sheet->setCellValue('B1', 'STT');
        $sheet->setCellValue('C1', 'Student ID (LMS)');
        $sheet->setCellValue('D1', 'STUDENT NAME');
        $sheet->setCellValue('E1', 'NickName');
        $sheet->setCellValue('F1', 'Brand Khóa học');
        $sheet->setCellValue('G1', 'Level trình độ');
        $sheet->setCellValue('H1', 'Guardian Name Phụ huynh');
        $sheet->setCellValue('I1', 'Home phone');
        $sheet->setCellValue('J1', 'Sale in charge');
        $sheet->setCellValue('K1', 'Status');
        $sheet->setCellValue('L1', 'Register?');
        $sheet->setCellValue('M1', 'Package Gói phí');
        $sheet->setCellValue('N1', 'Ngày đóng phí');
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => Color::COLOR_BLACK],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ]
        ];
        
        // Áp dụng cho dòng 1 từ A1 đến F1
        $sheet->getStyle('A1:N1')->applyFromArray($headerStyle);

        $sheet->getColumnDimension("A")->setWidth(30);
        $sheet->getColumnDimension("B")->setWidth(5);
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
        $sheet->getColumnDimension("N")->setWidth(30);
        for ($i = 0; $i < count($list) ; $i++) {
            $x = $i + 2;
            $sheet->setCellValue('A' . $x, $list[$i]->branch_name);
            $sheet->setCellValue('B' . $x, $i+1);
            $sheet->setCellValue('C' . $x, $list[$i]->std_id);
            $sheet->setCellValue('D' . $x, $list[$i]->stu_name) ;
            $sheet->setCellValue('E' . $x, $list[$i]->nick );
            $sheet->setCellValue('F' . $x, $list[$i]->product_name);
            $sheet->setCellValue('G' . $x, $list[$i]->level);
            $sheet->setCellValue('H' . $x, $list[$i]->gud_name1);
            $sheet->setCellValue('I' . $x, $list[$i]->gud_mobile1);
            $sheet->setCellValue('J' . $x, $list[$i]->ec_name );
            $sheet->setCellValue('K' . $x, $list[$i]->count_recharge ==0 ? 'New' :'Renew');
            $sheet->setCellValue('L' . $x, $list[$i]->remaining_amount > 0 ? 'Deposit' : ($list[$i]->payment_amount== $list[$i]->total_fee ? 'Full Fee' : 'Final Payment'));
            $sheet->setCellValue('M' . $x, $list[$i]->number_of_months . ' tháng');
            $sheet->setCellValue('N' . $x, $list[$i]->payment_date );
        }
        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Báo cáo doanh số.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function report06(Request $request , $key,$value) {
        set_time_limit(300);
        ini_set('memory_limit', '-1');
        $arr_key =explode(',',$key);
        $arr_value =explode(',',$value);
        foreach($arr_key AS $k=>$key){
            if($key=='start_date'){
                $report_month = $arr_value[$k];
            }
        }
        if ($report_month == date('Y-m')){
            $date = date('Y-m-d',time()-7*3600);
            $cond = " AND c.branch_id IN (" . Auth::user()->getBranchesHasUser().")";
            foreach($arr_key AS $k=>$key){
                if($key=='keyword'){
                    $keyword = $arr_value[$k];
                    $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%')";
                }
                if($key=='branch_id'){
                    $cond .=  " AND c.branch_id IN (".str_replace("-",",", $arr_value[$k]).")";
                }
            }
            
            $order_by = " ORDER BY c.id DESC ";
            $list = u::query("SELECT b.name AS branch_name, s.lms_code, s.name, s.gud_name1, cl.cls_name, p.name AS product_name,
                    CONCAT (u.hrm_id, ' - ', u.name) AS cm_name, t.name AS tuition_fee_name,
                    c.last_done_sessions, c.done_sessions, c.summary_sessions,
                    (SELECT is_reserved FROM reserves WHERE contract_id=c.id AND student_id=s.id AND end_date>= '$date' AND `start_date`<='$date' AND `status`=4 LIMIT 1) AS is_reserved,
                    (SELECT start_date FROM reserves WHERE contract_id=c.id AND student_id=s.id AND end_date>= '$date' AND `start_date`<='$date' AND `status`=4 LIMIT 1) AS start_date,
                    (SELECT end_date FROM reserves WHERE contract_id=c.id AND student_id=s.id AND end_date>= '$date' AND `start_date`<='$date' AND `status`=4 LIMIT 1) AS end_date
                FROM contracts AS c 
                    LEFT JOIN students AS s ON s.id=c.student_id 
                    LEFT JOIN branches AS b ON b.id = c.branch_id
                    LEFT JOIN classes AS cl ON cl.id = c.class_id
                    LEFT JOIN products AS p ON p.id = c.product_id
                    LEFT JOIN tuition_fee AS t ON t.id = c.tuition_fee_id
                    LEFT JOIN term_student_user AS tsu ON tsu.student_id=s.id AND tsu.status=1
                    LEFT JOIN users AS u ON u.id= tsu.cm_id
                WHERE s.status>0 AND c.status=4 AND c.class_id IS NULL AND c.type>0 AND c.summary_sessions>0
                    AND c.id = (SELECT id FROM contracts WHERE student_id=s.id AND `status`!=7 ORDER BY count_recharge LIMIT 1)
                $cond $order_by");

        } else {
            $cond = " r.branch_id IN (" . Auth::user()->getBranchesHasUser().")";
            foreach($arr_key AS $k=>$key){
                if($key=='keyword'){
                    $keyword = $arr_value[$k];
                    $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%')";
                }
                if($key=='start_date'){
                    $cond .= " AND r.report_month = '$arr_value[$k]'";
                }
                if($key=='branch_id'){
                    $cond .=  " AND r.branch_id IN (".str_replace("-",",", $arr_value[$k]).")";
                }
            }
            
            $order_by = " ORDER BY r.id DESC ";
            $list = u::query("SELECT b.name AS branch_name, s.lms_code, s.name, s.gud_name1, cl.cls_name, p.name AS product_name,
                    CONCAT (u.hrm_id, ' - ', u.name) AS cm_name, t.name AS tuition_fee_name,
                    c.last_done_sessions, c.done_sessions, c.summary_sessions,r.start_date, r.end_date , r.is_reserved
                FROM report_reserve AS r 
                    LEFT JOIN contracts AS c ON c.id = r.contract_id
                    LEFT JOIN students AS s ON s.id=r.student_id
                    LEFT JOIN branches AS b ON b.id = r.branch_id
                    LEFT JOIN classes AS cl ON cl.id = c.class_id
                    LEFT JOIN products AS p ON p.id = r.product_id
                    LEFT JOIN tuition_fee AS t ON t.id = r.tuition_fee_id
                    LEFT JOIN users AS u ON u.id=r.cm_id
                WHERE $cond $order_by");
        }  

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
        $sheet->setCellValue('I1', 'Tổng số buổi');
        $sheet->setCellValue('J1', 'Số buổi còn lại');
        $sheet->setCellValue('K1', 'Loại bảo lưu');
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
        for ($i = 0; $i < count($list) ; $i++) {
            $x = $i + 2;
            $sheet->setCellValue('A' . $x, $list[$i]->branch_name);
            $sheet->setCellValue('B' . $x, $list[$i]->lms_code) ;
            $sheet->setCellValue('C' . $x, $list[$i]->name );
            $sheet->setCellValue('D' . $x, $list[$i]->gud_name1);
            $sheet->setCellValue('E' . $x, $list[$i]->cls_name);
            $sheet->setCellValue('F' . $x, $list[$i]->product_name);
            $sheet->setCellValue('G' . $x, $list[$i]->cm_name);
            $sheet->setCellValue('H' . $x, $list[$i]->tuition_fee_name);
            $sheet->setCellValue('I' . $x, $list[$i]->summary_sessions + $list[$i]->last_done_sessions);
            $sheet->setCellValue('J' . $x, $list[$i]->summary_sessions - $list[$i]->done_sessions);
            $sheet->setCellValue('K' . $x, $list[$i]->is_reserved == 1 ? "Giữ chỗ" : "Không giữ chỗ");
            $sheet->setCellValue('L' . $x, $list[$i]->start_date);
            $sheet->setCellValue('M' . $x, $list[$i]->end_date);
        }
        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Báo cáo bảo lưu.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function report07(Request $request , $key,$value) {
        set_time_limit(300);
        ini_set('memory_limit', '-1');
        $arr_key =explode(',',$key);
        $arr_value =explode(',',$value);

        foreach($arr_key AS $k=>$key){
            if($key=='start_date'){
                $report_month = $arr_value[$k];
            }
        }
        if ($report_month == date('Y-m')){
            $cond = " AND c.branch_id IN (" . Auth::user()->getBranchesHasUser().")";
            foreach($arr_key AS $k=>$key){
                if($key=='keyword'){
                    $keyword = $arr_value[$k];
                    $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%')";
                }
                if($key=='branch_id'){
                    $cond .=  " AND c.branch_id IN (".str_replace("-",",", $arr_value[$k]).")";
                }
            }
            
            $order_by = " ORDER BY r.id DESC ";
            $list = u::query("SELECT b.name AS branch_name, s.lms_code, s.name, s.gud_name1, p.name AS product_name,
                    t.name AS tuition_fee_name, c.summary_sessions,c.start_date, c.id
                FROM contracts AS c 
                    LEFT JOIN students AS s ON s.id=c.student_id 
                    LEFT JOIN branches AS b ON b.id = c.branch_id
                    LEFT JOIN products AS p ON p.id = c.product_id
                    LEFT JOIN tuition_fee AS t ON t.id = c.tuition_fee_id
                WHERE s.status>0 AND c.status=3 AND c.class_id IS NULL AND c.type>0 AND c.summary_sessions>0
                    AND c.id = (SELECT id FROM contracts WHERE student_id=s.id AND `status`!=7 ORDER BY count_recharge LIMIT 1) 
                    $cond $order_by");
        } else {
            $cond = " r.branch_id IN (" . Auth::user()->getBranchesHasUser().")";
            foreach($arr_key AS $k=>$key){
                if($key=='keyword'){
                    $keyword = $arr_value[$k];
                    $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%')";
                }
                if($key=='start_date'){
                    $cond .= " AND r.report_month = '$arr_value[$k]'";
                }
                if($key=='branch_id'){
                    $cond .=  " AND r.branch_id IN (".str_replace("-",",", $arr_value[$k]).")";
                }
            }
            
            $order_by = " ORDER BY r.id DESC ";
            $list = u::query("SELECT b.name AS branch_name, s.lms_code, s.name, s.gud_name1, p.name AS product_name,
                    t.name AS tuition_fee_name, c.summary_sessions,r.start_date
                FROM report_pending AS r 
                    LEFT JOIN contracts AS c ON c.id = r.contract_id
                    LEFT JOIN students AS s ON s.id=r.student_id
                    LEFT JOIN branches AS b ON b.id = r.branch_id
                    LEFT JOIN products AS p ON p.id = r.product_id
                    LEFT JOIN tuition_fee AS t ON t.id = r.tuition_fee_id
                WHERE $cond $order_by");
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Trung tâm');
        $sheet->setCellValue('B1', 'Mã học sinh');
        $sheet->setCellValue('C1', 'Học sinh');
        $sheet->setCellValue('D1', 'Tên phụ huynh');
        $sheet->setCellValue('E1', 'Sản phẩm');
        $sheet->setCellValue('F1', 'Gói phí');
        $sheet->setCellValue('G1', 'Tổng số buổi');
        $sheet->setCellValue('H1', 'Ngày dự kiến đi học');

        $sheet->getColumnDimension("A")->setWidth(30);
        $sheet->getColumnDimension("B")->setWidth(20);
        $sheet->getColumnDimension("C")->setWidth(30);
        $sheet->getColumnDimension("D")->setWidth(30);
        $sheet->getColumnDimension("E")->setWidth(30);
        $sheet->getColumnDimension("F")->setWidth(20);
        $sheet->getColumnDimension("G")->setWidth(30);
        $sheet->getColumnDimension("H")->setWidth(30);
        for ($i = 0; $i < count($list) ; $i++) {
            $x = $i + 2;
            $sheet->setCellValue('A' . $x, $list[$i]->branch_name);
            $sheet->setCellValue('B' . $x, $list[$i]->lms_code) ;
            $sheet->setCellValue('C' . $x, $list[$i]->name );
            $sheet->setCellValue('D' . $x, $list[$i]->gud_name1);
            $sheet->setCellValue('E' . $x, $list[$i]->product_name);
            $sheet->setCellValue('F' . $x, $list[$i]->tuition_fee_name);
            $sheet->setCellValue('G' . $x, $list[$i]->summary_sessions);
            $sheet->setCellValue('H' . $x, $list[$i]->start_date);
        }
        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Báo cáo pending.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function report102(Request $request , $key,$value) {
        set_time_limit(300);
        ini_set('memory_limit', '-1');
        $arr_key =explode(',',$key);
        $arr_value =explode(',',$value);
        $start_date = date('Y-m');
        $cond = " AND bu.branch_id IN (" . Auth::user()->getBranchesHasUser().")";
        foreach($arr_key AS $k=>$key){
            if($key=='keyword'){
                $keyword = $arr_value[$k];
                $cond .= " AND (u.hrm_id LIKE '%$keyword%' OR u.name LIKE '$keyword')";
            }
            if($key=='branch_id'){
                $cond .=  " AND bu.branch_id IN (".str_replace("-",",", $arr_value[$k]).")";
            }
            if($key=='start_date'){
                $start_date = $arr_value[$k];
            }
        }
        
        $order_by = " ORDER BY u.id DESC ";
        $list = u::query("SELECT u.name, u.hrm_id, b.name AS branch_name,
                (SELECT COUNT(DISTINCT p.contract_id) FROM payments AS p WHERE p.debt=0 AND DATE_FORMAT(p.charge_date, '%Y-%m')= '$start_date' AND p.ec_id=u.id) AS count_full_fee,
                (SELECT SUM(p.amount) FROM payments AS p LEFT JOIN contracts AS c ON c.id=p.contract_id WHERE DATE_FORMAT(p.charge_date, '%Y-%m')= '$start_date' AND p.ec_id=u.id) AS total_amount
            FROM users AS u 
                LEFT JOIN role_has_user AS ru ON u.id=ru.user_id
                LEFT JOIN branch_has_user AS bu ON bu.user_id=ru.user_id
                LEFT JOIN branches AS b ON b.id=bu.branch_id
            WHERE ru.role_id IN (68,69) AND u.status =1 $cond $order_by");

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Trung tâm');
        $sheet->setCellValue('B1', 'Tên HOEC/EC');
        $sheet->setCellValue('C1', 'Số case thu phí');
        $sheet->setCellValue('D1', 'Doanh thu');

        $sheet->getColumnDimension("A")->setWidth(30);
        $sheet->getColumnDimension("B")->setWidth(30);
        $sheet->getColumnDimension("C")->setWidth(20);
        $sheet->getColumnDimension("D")->setWidth(20);
        for ($i = 0; $i < count($list) ; $i++) {
            $x = $i + 2;
            $sheet->setCellValue('A' . $x, $list[$i]->branch_name);
            $sheet->setCellValue('B' . $x, $list[$i]->name." - ".$list[$i]->hrm_id) ;
            $sheet->setCellValue('C' . $x, $list[$i]->count_full_fee );
            $sheet->setCellValue('D' . $x, $list[$i]->total_amount);
        }
        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Kết quả kinh doanh theo sale.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function report101(Request $request , $key,$value) {
        set_time_limit(300);
        ini_set('memory_limit', '-1');
        $arr_key =explode(',',$key);
        $arr_value =explode(',',$value);
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-d');
        $cond = " AND b.id IN (" . Auth::user()->getBranchesHasUser().")";
        foreach($arr_key AS $k=>$key){
            if($key=='branch_id'){
                $cond .=  " AND b.id IN (".str_replace("-",",", $arr_value[$k]).")";
            }
            if($key=='start_date'){
                $start_date = $arr_value[$k];
            }
            if($key=='end_date'){
                $end_date = $arr_value[$k];
            }
        }
        
        $order_by = " ORDER BY b.id DESC ";
        $list = u::query("SELECT b.name AS branch_name,
                (SELECT COUNT(id) FROM crm_student_checkin WHERE checkin_branch_id = b.id AND status >= 2 AND checkin_at >= '$start_date 00:00:00' AND checkin_at <= '$end_date 23:59:59') AS count_checkin,
                (SELECT COUNT(id) FROM contracts WHERE branch_id = b.id AND type=0 AND enrolment_start_date >= '$start_date' AND enrolment_start_date <= '$end_date') AS count_trial,
                (SELECT COUNT(DISTINCT p.contract_id) FROM payments AS p LEFT JOIN contracts AS c ON c.id=p.contract_id WHERE c.debt_amount>0 AND p.charge_date>= '$start_date' AND p.charge_date <= '$end_date'  AND p.branch_id=b.id) AS count_deposit,
                (SELECT COUNT(DISTINCT p.contract_id) FROM payments AS p WHERE p.debt=0 AND p.charge_date >= '$start_date' AND p.charge_date <= '$end_date' AND p.branch_id=b.id) AS count_full_fee,
                (SELECT COUNT(DISTINCT p.contract_id) FROM payments AS p LEFT JOIN contracts AS c ON c.id=p.contract_id WHERE c.count_recharge=0 AND p.charge_date >= '$start_date' AND p.charge_date <= '$end_date' AND p.branch_id=b.id) AS count_new,
                (SELECT COUNT(DISTINCT p.contract_id) FROM payments AS p LEFT JOIN contracts AS c ON c.id=p.contract_id WHERE c.count_recharge>0 AND p.charge_date >= '$start_date' AND p.charge_date <= '$end_date' AND p.branch_id=b.id) AS count_renew,
                (SELECT SUM(p.amount) FROM payments AS p LEFT JOIN contracts AS c ON c.id=p.contract_id WHERE c.count_recharge=0 AND p.charge_date >= '$start_date' AND p.charge_date <= '$end_date' AND p.branch_id=b.id) AS sales_new,
                (SELECT SUM(p.amount) FROM payments AS p LEFT JOIN contracts AS c ON c.id=p.contract_id WHERE c.count_recharge>0 AND p.charge_date >= '$start_date' AND p.charge_date <= '$end_date' AND p.branch_id=b.id) AS sales_renew,
                (SELECT SUM(c.debt_amount) FROM contracts AS c WHERE c.debt_amount > 0 AND c.created_at >= '$start_date 00:00:00' AND c.created_at <= '$end_date 00:00:00' AND c.branch_id=b.id) AS total_deposit
            FROM branches AS b 
            WHERE b.status =1 $cond AND b.id > 10 $order_by");

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Trung tâm');
        $sheet->setCellValue('B1', 'Số checkin');
        $sheet->setCellValue('C1', 'Số học thử');
        $sheet->setCellValue('D1', 'Số học sinh cọc');
        $sheet->setCellValue('E1', 'Số học sinh fullfee');
        $sheet->setCellValue('F1', 'Số học sinh new');
        $sheet->setCellValue('G1', 'Số học sinh renew');
        $sheet->setCellValue('H1', 'DS New');
        $sheet->setCellValue('I1', 'DS Renew');
        $sheet->setCellValue('J1', 'Tổng DS');
        $sheet->setCellValue('K1', 'Công nợ');

        $sheet->getColumnDimension("A")->setWidth(30);
        $sheet->getColumnDimension("B")->setWidth(20);
        $sheet->getColumnDimension("C")->setWidth(20);
        $sheet->getColumnDimension("D")->setWidth(20);
        $sheet->getColumnDimension("E")->setWidth(20);
        $sheet->getColumnDimension("F")->setWidth(20);
        $sheet->getColumnDimension("G")->setWidth(20);
        $sheet->getColumnDimension("H")->setWidth(20);
        $sheet->getColumnDimension("I")->setWidth(20);
        $sheet->getColumnDimension("J")->setWidth(20);
        $sheet->getColumnDimension("K")->setWidth(20);
        for ($i = 0; $i < count($list) ; $i++) {
            $x = $i + 2;
            $sheet->setCellValue('A' . $x, $list[$i]->branch_name);
            $sheet->setCellValue('B' . $x, $list[$i]->count_checkin );
            $sheet->setCellValue('C' . $x, $list[$i]->count_trial);
            $sheet->setCellValue('D' . $x, $list[$i]->count_deposit);
            $sheet->setCellValue('E' . $x, $list[$i]->count_full_fee);
            $sheet->setCellValue('F' . $x, $list[$i]->count_new);
            $sheet->setCellValue('G' . $x, $list[$i]->count_renew);
            $sheet->setCellValue('H' . $x, (int)$list[$i]->sales_new);
            $sheet->setCellValue('I' . $x, (int)$list[$i]->sales_renew);
            $sheet->setCellValue('J' . $x, (int)$list[$i]->sales_new + (int)$list[$i]->sales_renew);
            $sheet->setCellValue('K' . $x, (int)$list[$i]->total_deposit);
        }
        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Kết quả kinh doanh theo trung tâm.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function report08(Request $request , $key,$value) {
        set_time_limit(300);
        ini_set('memory_limit', '-1');
        $arr_key =explode(',',$key);
        $arr_value =explode(',',$value);
        $report_month = date('Y-m');
        $class_id = "";
        $cm_id = "";
        $keyword = "";
        $cond = " AND b.id IN (" . Auth::user()->getBranchesHasUser().")";
        foreach($arr_key AS $k=>$key){
            if($key=='branch_id'){
                $cond .=  " AND b.id IN (".str_replace("-",",", $arr_value[$k]).")";
            }
            if($key=='start_date'){
                $report_month = $arr_value[$k];
            }
            if($key=='keyword'){
                $keyword = $arr_value[$k];
            }
            if($key=='cm_id'){
                $cm_id = $arr_value[$k];
            }
            if($key=='class_id'){
                $class_id = $arr_value[$k];
            }
        }
        
        if ($report_month == date('Y-m')){
            if ($keyword !== '') {
                $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%') ";
            }
            if ($class_id) {
                $cond .= " AND c.class_id = '$class_id'";
            } elseif ($cm_id) {
                $cond .= " AND c.cm_id = '$cm_id'";
            }
            $order_by = " ORDER BY c.id DESC ";
            $list = u::query("SELECT c.id, sem.name AS semester_name, b.name AS branch_name, cls.cls_name AS class_name, 
                s.date_of_birth, s.lms_code, s.name, s.gud_name1, s.gud_mobile1, CONCAT (u.hrm_id, ' - ', u.name) AS cm_name
            FROM
                contracts c
                LEFT JOIN students s ON c.student_id = s.id
                LEFT JOIN branches AS b ON b.id = c.branch_id
                LEFT JOIN classes AS cls ON c.class_id = cls.id
                LEFT JOIN programs AS p ON p.id = cls.program_id
                LEFT JOIN semesters AS sem ON sem.id = p.semester_id
                LEFT JOIN users AS u ON u.id= c.cm_id
            WHERE
                c.`status` = 6 $cond $order_by ");
        }else {
            if ($keyword !== '') {
                $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%') ";
            }
    
            if ($report_month !== '') {
                $cond .= " AND r.report_month = '$report_month'";
            }
            if ($class_id) {
                $cond .= " AND r.class_id = '$class_id'";
            }
            if ($cm_id) {
                $cond .= " AND r.cm_id = '$cm_id'";
            }
            
            $order_by = " ORDER BY r.id DESC ";
            
            $list = u::query("SELECT r.id, sem.name AS semester_name, b.name AS branch_name, cls.cls_name AS class_name, 
                    s.date_of_birth, s.lms_code, s.name, s.gud_name1, s.gud_mobile1, CONCAT (u.hrm_id, ' - ', u.name) AS cm_name
                FROM report_student_in_class AS r 
                    LEFT JOIN students AS s ON s.id=r.student_id
                    LEFT JOIN branches AS b ON b.id = r.branch_id
                    LEFT JOIN classes AS cls ON cls.id = r.class_id
                    LEFT JOIN programs AS p ON p.id = cls.program_id
                    LEFT JOIN semesters AS sem ON sem.id = p.semester_id
                    LEFT JOIN users AS u ON u.id= r.cm_id
                WHERE 1 $cond $order_by");
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Kỳ học');
        $sheet->setCellValue('B1', 'Trung tâm');
        $sheet->setCellValue('C1', 'Lớp');
        $sheet->setCellValue('D1', 'Tên học sinh');
        $sheet->setCellValue('E1', 'Mã LMS');
        $sheet->setCellValue('F1', 'Ngày sinh');
        $sheet->setCellValue('G1', 'Tên phụ huynh');
        $sheet->setCellValue('H1', 'SDT');
        $sheet->setCellValue('I1', 'CM');

        $sheet->getColumnDimension("A")->setWidth(20);
        $sheet->getColumnDimension("B")->setWidth(30);
        $sheet->getColumnDimension("C")->setWidth(30);
        $sheet->getColumnDimension("D")->setWidth(30);
        $sheet->getColumnDimension("E")->setWidth(20);
        $sheet->getColumnDimension("F")->setWidth(20);
        $sheet->getColumnDimension("G")->setWidth(30);
        $sheet->getColumnDimension("H")->setWidth(20);
        $sheet->getColumnDimension("I")->setWidth(30);
        for ($i = 0; $i < count($list) ; $i++) {
            $x = $i + 2;
            $sheet->setCellValue('A' . $x, $list[$i]->semester_name);
            $sheet->setCellValue('B' . $x, $list[$i]->branch_name );
            $sheet->setCellValue('C' . $x, $list[$i]->class_name);
            $sheet->setCellValue('D' . $x, $list[$i]->name);
            $sheet->setCellValue('E' . $x, $list[$i]->lms_code);
            $sheet->setCellValue('F' . $x, $list[$i]->date_of_birth);
            $sheet->setCellValue('G' . $x, $list[$i]->gud_name1);
            $sheet->setCellValue('H' . $x, $list[$i]->gud_mobile1);
            $sheet->setCellValue('I' . $x, $list[$i]->cm_name);

        }
        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Báo cáo chi tiết học sinh theo lớp.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function report09(Request $request , $key,$value) {
        set_time_limit(300);
        ini_set('memory_limit', '-1');
        $arr_key =explode(',',$key);
        $arr_value =explode(',',$value);
        $report_month = date('Y-m');
        $cond = " AND b.id IN (" . Auth::user()->getBranchesHasUser().")";
        foreach($arr_key AS $k=>$key){
            if($key=='branch_id'){
                $cond .=  " AND b.id IN (".str_replace("-",",", $arr_value[$k]).")";
            }
            if($key=='start_date'){
                $report_month = $arr_value[$k];
            }
        }
        
        $order_by = " ORDER BY b.id DESC ";
        $list = u::query("SELECT b.name AS branch_name,
                (SELECT COUNT(id) FROM report_pending WHERE branch_id = b.id AND report_month = '$report_month') AS count_pending,
                (SELECT COUNT(id) FROM report_reserve WHERE branch_id = b.id AND report_month = '$report_month') AS count_reserve,
                (SELECT COUNT(id) FROM report_full_fee_active WHERE branch_id = b.id AND report_month = '$report_month') AS count_full_fee_active,
                (SELECT COUNT(id) FROM report_classes WHERE branch_id = b.id AND status=1 AND report_month = '$report_month') AS count_class,
                (SELECT COUNT(id) FROM report_full_fee_active WHERE branch_id = b.id AND report_month = '$report_month' AND product_id=1) AS count_full_fee_active_april,
                (SELECT COUNT(id) FROM report_classes WHERE branch_id = b.id AND status=1 AND report_month = '$report_month' AND product_id=1) AS count_class_april,
                (SELECT COUNT(id) FROM report_full_fee_active WHERE branch_id = b.id AND report_month = '$report_month' AND product_id=2) AS count_full_fee_active_igarten,
                (SELECT COUNT(id) FROM report_classes WHERE branch_id = b.id AND status=1 AND report_month = '$report_month' AND product_id=2) AS count_class_igarten,
                (SELECT COUNT(id) FROM report_users WHERE branch_id = b.id AND role_id IN (55,56) AND report_month = '$report_month') AS count_cm
            FROM
                branches AS b 
            WHERE
                b.status=1 $cond $order_by ");
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Trung tâm');
        $sheet->setCellValue('B1', 'Tổng số học sinh');
        $sheet->setCellValue('C1', 'Tổng học sinh bảo lưu');
        $sheet->setCellValue('D1', 'Tổng học sinh pending');
        $sheet->setCellValue('E1', 'Tổng số học sinh Active');
        $sheet->setCellValue('F1', 'Tổng số lớp');
        $sheet->setCellValue('G1', 'Tỷ lệ ACS');
        $sheet->setCellValue('H1', 'Tổng học sinh Sunny');
        $sheet->setCellValue('I1', 'Tổng số lớp Sunny');
        $sheet->setCellValue('J1', 'Tỷ lệ ACS Sunny');
        $sheet->setCellValue('K1', 'Tổng học sinh I-Kinder');
        $sheet->setCellValue('L1', 'Tổng số lớp I-Kinder');
        $sheet->setCellValue('M1', 'Tỷ lệ ACS I-Kinder');
        $sheet->setCellValue('N1', 'Số lượng OM+CM');
        $sheet->setCellValue('O1', 'Hiệu suất OM+CM');

        $sheet->getColumnDimension("A")->setWidth(30);
        $sheet->getColumnDimension("B")->setWidth(20);
        $sheet->getColumnDimension("C")->setWidth(20);
        $sheet->getColumnDimension("D")->setWidth(20);
        $sheet->getColumnDimension("E")->setWidth(20);
        $sheet->getColumnDimension("F")->setWidth(20);
        $sheet->getColumnDimension("G")->setWidth(20);
        $sheet->getColumnDimension("H")->setWidth(20);
        $sheet->getColumnDimension("I")->setWidth(20);
        $sheet->getColumnDimension("J")->setWidth(20);
        $sheet->getColumnDimension("K")->setWidth(20);
        $sheet->getColumnDimension("L")->setWidth(20);
        $sheet->getColumnDimension("M")->setWidth(20);
        $sheet->getColumnDimension("N")->setWidth(20);
        $sheet->getColumnDimension("O")->setWidth(20);
        for ($i = 0; $i < count($list) ; $i++) {
            $x = $i + 2;
            $sheet->setCellValue('A' . $x, $list[$i]->branch_name);
            $sheet->setCellValue('B' . $x, $list[$i]->count_pending + $list[$i]->count_reserve + $list[$i]->count_full_fee_active);
            $sheet->setCellValue('C' . $x, $list[$i]->count_reserve);
            $sheet->setCellValue('D' . $x, $list[$i]->count_pending);
            $sheet->setCellValue('E' . $x, $list[$i]->count_full_fee_active);
            $sheet->setCellValue('F' . $x, $list[$i]->count_class);
            $sheet->setCellValue('G' . $x, $list[$i]->count_class ? round($list[$i]->count_full_fee_active / $list[$i]->count_class, 2) : "--");
            $sheet->setCellValue('H' . $x, $list[$i]->count_full_fee_active_april);
            $sheet->setCellValue('I' . $x, $list[$i]->count_class_april);
            $sheet->setCellValue('J' . $x, $list[$i]->count_class_april ? round($list[$i]->count_full_fee_active_april / $list[$i]->count_class_april, 2) : "--");
            $sheet->setCellValue('K' . $x, $list[$i]->count_full_fee_active_igarten);
            $sheet->setCellValue('L' . $x, $list[$i]->count_class_igarten);
            $sheet->setCellValue('M' . $x, $list[$i]->count_class_igarten ? round($list[$i]->count_full_fee_active_igarten / $list[$i]->count_class_igarten, 2) : "--");
            $sheet->setCellValue('N' . $x, $list[$i]->count_cm);
            $sheet->setCellValue('O' . $x, $list[$i]->count_cm ? round($list[$i]->count_full_fee_active / $list[$i]->count_cm, 2) : "--");

        }
        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Báo cáo tổng hợp.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function report11(Request $request , $key,$value) {
        set_time_limit(300);
        ini_set('memory_limit', '-1');
        $arr_key =explode(',',$key);
        $arr_value =explode(',',$value);
        $report_month = date('Y-m');
        $cond = " AND b.id IN (" . Auth::user()->getBranchesHasUser().")";
        foreach($arr_key AS $k=>$key){
            if($key=='branch_id'){
                $cond .=  " AND b.id IN (".str_replace("-",",", $arr_value[$k]).")";
            }
            if($key=='start_date'){
                $report_month = $arr_value[$k];
            }
            if ($key=='keyword'){
                $cond .= " AND (u.name LIKE '%".$arr_value[$k]."%' OR u.hrm_id LIKE '".$arr_value[$k]."') ";
            }
        }
        
        $order_by = " ORDER BY ru.branch_id, u.id ";
        $list = u::query("SELECT DISTINCT ru.branch_id, u.id, b.name AS branch_name, CONCAT(u.name, ' - ', u.hrm_id ) AS cm_name,
                (SELECT count(id) FROM report_full_fee_active WHERE cm_id=ru.user_id AND report_month = '$report_month' AND product_id=1 AND branch_id =ru.branch_id) AS countStudentApril,
                (SELECT count(id) FROM report_full_fee_active WHERE cm_id=ru.user_id AND report_month = '$report_month' AND product_id=2 AND branch_id =ru.branch_id) AS countStudentIgarten,
                (SELECT count(id) FROM report_classes WHERE cm_id=ru.user_id AND report_month = '$report_month' AND product_id=1 AND status=1 AND branch_id =ru.branch_id) AS countClassApril,
                (SELECT count(id) FROM report_classes WHERE cm_id=ru.user_id AND report_month = '$report_month' AND product_id=2 AND status=1 AND branch_id =ru.branch_id) AS countClassIgarten
            FROM report_users AS ru 
            LEFT JOIN users AS u ON u.id =ru.user_id
            LEFT JOIN branches AS b ON b.id=ru.branch_id
            WHERE ru.role_id IN(55,56) $cond $order_by");
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Trung tâm');
        $sheet->setCellValue('B1', 'Tên CM');
        $sheet->setCellValue('C1', 'I-Kinder');
        $sheet->setCellValue('F1', 'Sunny');
        $sheet->setCellValue('I1', 'Tổng');
        
        $sheet->setCellValue('C2', 'HS Full fee active');
        $sheet->setCellValue('D2', 'Số lớp');
        $sheet->setCellValue('E2', 'ACS');
        $sheet->setCellValue('F2', 'HS Full fee active');
        $sheet->setCellValue('G2', 'Số lớp');
        $sheet->setCellValue('H2', 'ACS');
        $sheet->setCellValue('I2', 'HS Full fee active');
        $sheet->setCellValue('J2', 'Số lớp');
        $sheet->setCellValue('K2', 'ACS');
        $sheet->mergeCells('A1:A2');
        $sheet->mergeCells('B1:B2');
        $sheet->mergeCells('C1:E1');
        $sheet->mergeCells('F1:H1');
        $sheet->mergeCells('I1:K1');

        $sheet->getColumnDimension("A")->setWidth(30);
        $sheet->getColumnDimension("B")->setWidth(20);
        $sheet->getColumnDimension("C")->setWidth(20);
        $sheet->getColumnDimension("D")->setWidth(20);
        $sheet->getColumnDimension("E")->setWidth(20);
        $sheet->getColumnDimension("F")->setWidth(20);
        $sheet->getColumnDimension("G")->setWidth(20);
        $sheet->getColumnDimension("H")->setWidth(20);
        $sheet->getColumnDimension("I")->setWidth(20);
        $sheet->getColumnDimension("J")->setWidth(20);
        $sheet->getColumnDimension("K")->setWidth(20);
        for ($i = 0; $i < count($list) ; $i++) {
            $x = $i + 3;
            $sheet->setCellValue('A' . $x, $list[$i]->branch_name);
            $sheet->setCellValue('B' . $x, $list[$i]->cm_name);
            $sheet->setCellValue('C' . $x, $list[$i]->countStudentIgarten);
            $sheet->setCellValue('D' . $x, $list[$i]->countClassIgarten);
            $sheet->setCellValue('E' . $x, $list[$i]->countClassIgarten ? round($list[$i]->countStudentIgarten / $list[$i]->countClassIgarten, 2) : "--");
            $sheet->setCellValue('F' . $x, $list[$i]->countStudentApril);
            $sheet->setCellValue('G' . $x, $list[$i]->countClassApril);
            $sheet->setCellValue('H' . $x, $list[$i]->countClassApril ? round($list[$i]->countStudentApril / $list[$i]->countClassApril, 2) : "--");
            $sheet->setCellValue('I' . $x, $list[$i]->countStudentIgarten + $list[$i]->countStudentApril);
            $sheet->setCellValue('J' . $x, $list[$i]->countClassApril + $list[$i]->countClassIgarten);
            $sheet->setCellValue('K' . $x, $list[$i]->countClassApril + $list[$i]->countClassIgarten ? round(($list[$i]->countStudentIgarten + $list[$i]->countStudentApril) / ($list[$i]->countClassApril + $list[$i]->countClassIgarten), 2) : "--");
            
        }
        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Báo cáo tổng hợp.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function report10(Request $request , $key,$value) {
        set_time_limit(300);
        ini_set('memory_limit', '-1');
        $arr_key =explode(',',$key);
        $arr_value =explode(',',$value);
        $report_month = date('Y-m');
        $cond = " AND b.id IN (" . Auth::user()->getBranchesHasUser().")";
        $product_id = 0;
        foreach($arr_key AS $k=>$key){
            if($key=='branch_id'){
                $cond .=  " AND b.id IN (".str_replace("-",",", $arr_value[$k]).")";
            }
            if($key=='start_date'){
                $report_month = $arr_value[$k];
            }
            if($key=='product_id'){
                $product_id = $arr_value[$k];
            }
        }
        if ($product_id = 0) {
            $listProgramSubs = u::query("SELECT * FROM program_subs WHERE product_id IN (1,2) AND status=1");
        }else{
            $listProgramSubs = u::query("SELECT * FROM program_subs WHERE product_id=$product_id AND status=1");
        }
        $order_by = " ORDER BY b.id ";
        $list = u::query("SELECT b.name AS branch_name, b.id
            FROM branches AS b 
            WHERE b.status =1 $cond AND b.id > 10 $order_by");
        foreach ($list as $branch) {
            foreach ($listProgramSubs as $programSub) {
                $countStudent = u::first("SELECT COUNT(r.id) AS total FROM report_full_fee_active AS r
                        LEFT JOIN programs AS p ON p.id = r.program_id
                    WHERE r.branch_id = $branch->id AND r.report_month = '$report_month' AND r.product_id=$product_id AND p.program_sub_id = $programSub->id ");
                $countClass = u::first("SELECT COUNT(r.id) AS total FROM report_classes AS r
                        LEFT JOIN programs AS p ON p.id = r.program_id
                    WHERE r.branch_id = $branch->id AND r.report_month = '$report_month' AND r.product_id=$product_id AND r.status = 1 AND p.program_sub_id = $programSub->id ");
                $branch->{$programSub->id} = (object)[
                    'countStudent' => $countStudent->total,
                    'countClass' => $countClass->total
                ];
            }
        }
        $arrKey = ['B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U', 'V','W','X','Y','Z',
            'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ'];
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Trung tâm');
        foreach ($listProgramSubs as $index => $programSub) {
            $colIndex = $arrKey[$index * 3];
            $sheet->setCellValue($colIndex . '1', $programSub->name);
            $sheet->setCellValue($colIndex . '2', 'HS Full fee active');
            $sheet->setCellValue($arrKey[$index * 3 + 1] . '2', 'Số lớp');
            $sheet->setCellValue($arrKey[$index * 3 + 2] . '2', 'ACS');
            $sheet->mergeCells($colIndex . '1:' . $arrKey[$index * 3 + 2] . '1');
            $sheet->getColumnDimension($arrKey[$index * 3])->setWidth(15);
            $sheet->getColumnDimension($arrKey[$index * 3 + 1])->setWidth(15);
            $sheet->getColumnDimension($arrKey[$index * 3 + 2])->setWidth(15);
        }
        $sheet->mergeCells('A1:A2');
        $sheet->getColumnDimension('A')->setWidth(30);
        for ($i = 0; $i < count($list) ; $i++) {
            $x = $i + 3;
            $sheet->setCellValue('A' . $x, $list[$i]->branch_name);
            foreach ($listProgramSubs as $index => $programSub) {
                $colIndex = $arrKey[$index * 3];
                $countStudent = $list[$i]->{$programSub->id}->countStudent;
                $countClass = $list[$i]->{$programSub->id}->countClass;
                $sheet->setCellValue($colIndex . $x, $countStudent);
                $sheet->setCellValue($arrKey[$index * 3 + 1] . $x, $countClass);
                $sheet->setCellValue($arrKey[$index * 3 + 2] . $x, $countClass ? round($countStudent / $countClass, 2) : "--");
            }
        }
        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Báo cáo ACS chi tiết theo chương trình con.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
