<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use App\Models\LogStudents;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportsController extends Controller
{

    public function collectFullFeeActive(Request $request, $month, $branch)
    {
        if($month && $month!="_"){
            $report_month = (int)$month > 9 ? date("Y").'-'.(int)$month : date("Y").'-0'.(int)$month;
        }else{
            $report_month = date('Y-m',time()-7*3600);
        }
        $start_date = date('Y-m-01',strtotime($report_month.'-01'));
        if(date('Y-m',strtotime($report_month.'-01')) == date('Y-m')){
            $end_date = date('Y-m-d');
        }else{
            $end_date = date('Y-m-t',strtotime($report_month.'-01'));
        }
        $where = "";
        $where_del = "";
        if (count(explode(',', $branch)) > 0 && $branch != '_') {
            $where = " AND c.branch_id IN ($branch)";
            $where_del = " AND branch_id IN ($branch)";
        }
        u::query("DELETE FROM report_full_fee_active WHERE report_month = '$report_month' $where_del ");
        $list = u::query("SELECT DISTINCT s.id student_id,
            c.id contract_id,c.tuition_fee_id, c.int_tuition_fee_id,
            s.branch_id,
            IF((c.class_id !=0 AND c.class_id IS NOT NULL),c.class_id,(SELECT class_id FROM contracts WHERE student_id=s.id AND `status` !=7 AND `class_id` IS NOT NULL LIMIT 1)) AS class_id,
            t.cm_id,
            c.product_id,
            '$report_month' report_month,
            c.enrolment_last_date,
            c.summary_sessions, c.done_sessions,c.enrolment_start_date,
            IF(c.count_recharge =0 , 0, 1) AS type_fee
        FROM
            contracts c
            LEFT JOIN students s ON c.student_id = s.id
            LEFT JOIN classes cls ON c.class_id = cls.id
            LEFT JOIN term_student_user AS t ON t.student_id = c.student_id
            LEFT JOIN users u ON t.cm_id = u.id
        WHERE
            c.type > 0
            AND c.`status` < 7
            AND (
                c.class_id IS NOT NULL
                AND c.enrolment_start_date <= ( SELECT class_date FROM schedules WHERE class_id = c.class_id AND class_date >= '$end_date' AND `status`=1 ORDER BY class_date ASC LIMIT 1 )
                AND c.enrolment_last_date >= ( SELECT class_date FROM schedules WHERE class_id = c.class_id AND class_date <= '$end_date' AND `status`=1 ORDER BY class_date ASC LIMIT 1 )
            )
            AND (SELECT count(id) FROM reserves WHERE contract_id=c.id AND is_reserved=1 AND `start_date` <= '$end_date' AND `end_date`>='$end_date' AND `status`=2) =0
            AND s.status > 0
        $where ");
        if (count($list)) {
            self::addItems($list);
        }
        return response()->json('ok');
    }

    public function addItems($list) {
        if ($list) {
            $created_at = date('Y-m-d H:i:s');
            $query = "INSERT INTO report_full_fee_active (student_id,contract_id, class_id, product_id, cm_id, report_month, branch_id, created_at, creator_id,end_date,`start_date`,done_sessions,summary_sessions,last_done_sessions, `type`, tuition_fee_id, init_tuition_fee_id) VALUES ";
            if (count($list) > 5000) {
                for($i = 0; $i < 5000; $i++) {
                    $item = $list[$i];
                    $query.= "('$item->student_id', '$item->contract_id', '$item->class_id', '$item->product_id', '$item->cm_id', '$item->report_month', '$item->branch_id', '$created_at', 99999,'$item->enrolment_last_date','$item->enrolment_start_date','$item->done_sessions','$item->summary_sessions','$item->last_done_sessions','$item->type_fee', '$item->tuition_fee_id', '$item->init_tuition_fee_id'),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
                self::addItems(array_slice($list, 5000));
            } else {
                foreach($list as $item) {
                    $query.= "('$item->student_id', '$item->contract_id', '$item->class_id', '$item->product_id', '$item->cm_id', '$item->report_month', '$item->branch_id', '$created_at', 99999,'$item->enrolment_last_date','$item->enrolment_start_date','$item->done_sessions','$item->summary_sessions','$item->last_done_sessions','$item->type_fee', '$item->tuition_fee_id', '$item->init_tuition_fee_id'),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
            }
        }
    }

    public function report01(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $keyword = isset($request->keyword) ? $request->keyword : '';
        $start_date = isset($request->start_date) ? $request->start_date : date('Y-m');

        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        $cond = " r.branch_id IN (" . Auth::user()->getBranchesHasUser().")";

        if (!empty($branch_id)) {
            $cond .= " AND r.branch_id IN (".implode(",",$branch_id).")";
        }
        
        if ($keyword !== '') {
            $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%') ";
        }

        if ($start_date !== '') {
            $cond .= " AND r.report_month = '$start_date'";
        }
        
        $order_by = " ORDER BY r.id DESC ";

        $total = u::first("SELECT count(r.id) AS total 
            FROM report_full_fee_active AS r LEFT JOIN students AS s ON s.id=r.student_id WHERE $cond");
        
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
            WHERE $cond $order_by $limitation");

        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }
}
