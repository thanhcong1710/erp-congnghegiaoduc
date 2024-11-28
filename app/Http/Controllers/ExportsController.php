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
                $cond .= " AND r.report_month >= '$arr_value[$k]'";
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
}
