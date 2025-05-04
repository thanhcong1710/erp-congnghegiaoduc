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
            $sheet->getRowDimension($x)->setRowHeight(23);

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
        $cond = " r.branch_id IN (" . Auth::user()->getBranchesHasUser().")";
        $arr_key =explode(',',$key);
        $arr_value =explode(',',$value);
        foreach($arr_key AS $k=>$key){
            if($key=='keyword'){
                $keyword = $arr_value[$k];
                $cond .= " AND (p.name LIKE '%$keyword%' OR p.mobile_1 LIKE '%$keyword%' OR p.mobile_2 LIKE '%$keyword%')";
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
        for ($i = 0; $i < count($list) ; $i++) {
            $x = $i + 2;
            $sheet->setCellValue('A' . $x, $list[$i]->branch_name);
            $sheet->setCellValue('B' . $x, $list[$i]->cm_name);
            $sheet->setCellValue('C' . $x, $list[$i]->role_name);
            $sheet->setCellValue('D' . $x, $list[$i]->total_item) ;
            $sheet->setCellValue('E' . $x, $list[$i]->success_item );
            $sheet->setCellValue('F' . $x, $list[$i]->total_item ? floor($list[$i]->success_item*100 / $list[$i]->total_item) :'--');
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

    public function report05(Request $request , $key,$value) {
        set_time_limit(300);
        ini_set('memory_limit', '-1');
        $cond = " 1 ";
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
            if($key=='owner_id'){
                $cond.= " AND  u.id IN (".str_replace("-",",", $arr_value[$k]).")" ;
            }
            if($key=='source_id'){
                $cond .= " AND  p.source_id IN (".str_replace("-",",", $arr_value[$k]).")" ;
            }
            if($key=='source_detail_id'){
                $cond .= " AND  p.source_detail_id IN (".str_replace("-",",", $arr_value[$k]).")" ;
            }
            if ($key=='call_status') {
                $cond.=" AND c.call_status=".$arr_value[$k];
            }
            if($key=='start_date'){
                $cond .= " AND c.created_at >= '".date('Y-m-d 00:00:00',strtotime($arr_value[$k]))."'";
            }
            if($key=='end_date'){
                $cond .= " AND c.created_at <= '".date('Y-m-d 23:59:59',strtotime($arr_value[$k]))."'";
            }
        }
        
        $list = u::query("SELECT p.id AS parent_id, p.name, p.mobile_1, c.call_status, c.call_status_sub, c.next_care_date,
            u.branch_name, CONCAT(u.name, ' - ', u.hrm_id) AS sale_name, c.created_at,
                s.name AS source_name, sd.name AS source_detail_name, c.note    
            FROM crm_customer_care AS c
                LEFT JOIN crm_parents AS p ON c.parent_id=p.id
                LEFT JOIN users AS u ON u.id =c.creator_id 
                LEFT JOIN sources AS s ON s.id=p.source_id
                LEFT JOIN source_detail AS sd ON sd.id=p.source_detail_id 
            WHERE c.status=1 AND c.method_id = 1 AND $cond 
            ORDER BY c.id DESC");

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'STT');
        $sheet->setCellValue('B1', 'Tên khách hàng');
        $sheet->setCellValue('C1', 'SĐT');
        $sheet->setCellValue('D1', 'Trạng thái cuộc gọi');
        $sheet->setCellValue('E1', 'Trung tâm');
        $sheet->setCellValue('F1', 'Sale');
        $sheet->setCellValue('G1', 'Ngày cập nhật');
        $sheet->setCellValue('H1', 'Nguồn');
        $sheet->setCellValue('I1', 'Nguồn chi tiết');
        $sheet->setCellValue('J1', 'Ghi chú');

        $sheet->getColumnDimension("A")->setWidth(10);
        $sheet->getColumnDimension("B")->setWidth(30);
        $sheet->getColumnDimension("C")->setWidth(30);
        $sheet->getColumnDimension("D")->setWidth(30);
        $sheet->getColumnDimension("E")->setWidth(30);
        $sheet->getColumnDimension("F")->setWidth(30);
        $sheet->getColumnDimension("G")->setWidth(30);
        $sheet->getColumnDimension("H")->setWidth(30);
        $sheet->getColumnDimension("I")->setWidth(30);
        $sheet->getColumnDimension("J")->setWidth(30);
        for ($i = 0; $i < count($list) ; $i++) {
            $x = $i + 2;
            $sheet->setCellValue('A' . $x, $i+1);
            $sheet->setCellValue('B' . $x, $list[$i]->name);
            $sheet->setCellValue('C' . $x, $list[$i]->mobile_1) ;
            $sheet->setCellValue('D' . $x, u::getTitleCallStatus($list[$i]->call_status) );
            $sheet->setCellValue('E' . $x, $list[$i]->branch_name);
            $sheet->setCellValue('F' . $x, $list[$i]->sale_name);
            $sheet->setCellValue('G' . $x, $list[$i]->created_at);
            $sheet->setCellValue('H' . $x, $list[$i]->source_name);
            $sheet->setCellValue('I' . $x, $list[$i]->source_detail_name);
            $sheet->setCellValue('J' . $x, $list[$i]->note);
            
            $sheet->getRowDimension($x)->setRowHeight(23);

        }
        $writer = new Xlsx($spreadsheet);
        try {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Báo cáo cuộc gọi chi tiết.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
