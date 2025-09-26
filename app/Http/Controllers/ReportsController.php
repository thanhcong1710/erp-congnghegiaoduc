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
            c.id contract_id,c.tuition_fee_id, c.init_tuition_fee_id,
            s.branch_id,
            IF((c.class_id !=0 AND c.class_id IS NOT NULL),c.class_id,(SELECT class_id FROM contracts WHERE student_id=s.id AND `status` !=7 AND `class_id` IS NOT NULL LIMIT 1)) AS class_id,
            t.cm_id,
            c.product_id,
            '$report_month' report_month,
            c.enrolment_last_date,
            c.summary_sessions, c.done_sessions, c.last_done_sessions,
            IF(c.count_recharge =0 , 0, 1) AS type_fee,
            (SELECT class_date FROM schedule_has_student WHERE contract_id=c.id ORDER BY class_date ASC LIMIT 1) AS enrolment_start_date
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
            AND (SELECT count(id) FROM reserves WHERE contract_id=c.id AND is_reserved=1 AND `start_date` <= '$end_date' AND `end_date`>='$end_date' AND `status`=4) =0
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
                    $query.= "('$item->student_id', '$item->contract_id', '$item->class_id', '$item->product_id', '".(int)$item->cm_id."', '$item->report_month', '$item->branch_id', '$created_at', 99999,'$item->enrolment_last_date','$item->enrolment_start_date','$item->done_sessions','$item->summary_sessions','$item->last_done_sessions','$item->type_fee', '$item->tuition_fee_id', '$item->init_tuition_fee_id'),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
                self::addItems(array_slice($list, 5000));
            } else {
                foreach($list as $item) {
                    $query.= "('$item->student_id', '$item->contract_id', '$item->class_id', '$item->product_id', '".(int)$item->cm_id."', '$item->report_month', '$item->branch_id', '$created_at', 99999,'$item->enrolment_last_date','$item->enrolment_start_date','$item->done_sessions','$item->summary_sessions','$item->last_done_sessions','$item->type_fee', '$item->tuition_fee_id', '$item->init_tuition_fee_id'),";
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

        if ($start_date == date('Y-m')) {
            $end_date = date('Y-m-d');
            $cond = " AND c.branch_id IN (" . Auth::user()->getBranchesHasUser().")";
            if (!empty($branch_id)) {
                $cond .= " AND c.branch_id IN (".implode(",",$branch_id).")";
            }
            
            if ($keyword !== '') {
                $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%') ";
            }
            $order_by = " ORDER BY c.id DESC ";
            $total = u::first("SELECT count(c.id) AS total 
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
                    AND c.enrolment_start_date <= ( SELECT class_date FROM schedules WHERE class_id = c.class_id AND class_date >= '$end_date' AND `status`=1 ORDER BY class_date ASC LIMIT 1 )
                    AND c.enrolment_last_date >= ( SELECT class_date FROM schedules WHERE class_id = c.class_id AND class_date <= '$end_date' AND `status`=1 ORDER BY class_date ASC LIMIT 1 )
                )
                AND (SELECT count(id) FROM reserves WHERE contract_id=c.id AND is_reserved=1 AND `start_date` <= '$end_date' AND `end_date`>='$end_date' AND `status`=4) =0
                AND s.status > 0
            $cond");

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
                    AND c.enrolment_start_date <= ( SELECT class_date FROM schedules WHERE class_id = c.class_id AND class_date >= '$end_date' AND `status`=1 ORDER BY class_date ASC LIMIT 1 )
                    AND c.enrolment_last_date >= ( SELECT class_date FROM schedules WHERE class_id = c.class_id AND class_date <= '$end_date' AND `status`=1 ORDER BY class_date ASC LIMIT 1 )
                )
                AND (SELECT count(id) FROM reserves WHERE contract_id=c.id AND is_reserved=1 AND `start_date` <= '$end_date' AND `end_date`>='$end_date' AND `status`=4) =0
                AND s.status > 0
            $cond $order_by $limitation");
        } else {
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
        }

        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function updateCompletedDate(){
        u::query("UPDATE contracts AS c LEFT JOIN payments AS  p ON p.contract_id=c.id AND p.debt=0 SET c.completed_date=p.charge_date WHERE c.debt_amount=0 AND c.must_charge>0 AND p.id IS NOT NULL");
        return true;
    }

    public function updateRenewedDates($student_id = 0)
    {
        $cond = $student_id ? " c.student_id=$student_id" : " 1 ";
        $query = "SELECT c.id, c.type, c.class_id, c.enrolment_last_date, c.status,c1.id AS next_id, c1.type AS next_type, 
                    c1.completed_date AS next_completed_date, c1.status AS next_status, 
                    (SELECT id FROM log_contracts WHERE contract_id = c.id ORDER BY id DESC LIMIT 1) AS last_log_id
                FROM contracts c 
                    LEFT JOIN contracts c1 ON c1.student_id=c.student_id 
                        AND c1.id = (SELECT id FROM contracts WHERE student_id=c.student_id AND count_recharge>c.count_recharge ORDER BY count_recharge LIMIT 1) 
                WHERE $cond AND c.product_id !=4";
        $contracts = u::query($query);
        if ($contracts) {
            $sql_update_contract = "INSERT INTO contracts (id,renewed_date,success_renewed_date,renew_runtime) VALUES";
            $sql_update_log_contract = "INSERT INTO log_contracts (id,renewed_date,success_renewed_date,renew_runtime) VALUES";
            $renew_runtime = date('Y-m-d H:i:s');
            foreach ($contracts as $contract) {
                $resp = (object)[
                    'renewed_date' => null,
                    'success_renewed_date' => null
                ];
                //process renewed_date
                if ($contract->type = 0 || !$contract->class_id) {
                    $resp->renewed_date = null;
                } else {
                    $resp->renewed_date = $contract->enrolment_last_date;
                }
                //process success_renewed_date
                if (!$resp->renewed_date || !$contract->next_completed_date) {
                    $resp->success_renewed_date = null;
                } else {
                    $resp->success_renewed_date = $contract->next_completed_date;
                }
                $resp->renewed_date = $resp->renewed_date ? "'$resp->renewed_date'" : "NULL";
                $resp->success_renewed_date = $resp->success_renewed_date ? "'$resp->success_renewed_date'" : "NULL";
                $sql_update_contract .= $contract->id ? " ($contract->id,$resp->renewed_date,$resp->success_renewed_date,'$renew_runtime')," : '';
                $sql_update_log_contract .= $contract->last_log_id ? " ($contract->last_log_id,$resp->renewed_date,$resp->success_renewed_date,'$renew_runtime')," : '';
            }
            $sql_update_contract = substr($sql_update_contract, 0, -1);
            $sql_update_contract .= " ON DUPLICATE KEY UPDATE renewed_date=VALUES(renewed_date),success_renewed_date=VALUES(success_renewed_date),renew_runtime=VALUES(renew_runtime)";
            $sql_update_log_contract = substr($sql_update_log_contract, 0, -1);
            $sql_update_log_contract .= " ON DUPLICATE KEY UPDATE renewed_date=VALUES(renewed_date),success_renewed_date=VALUES(success_renewed_date),renew_runtime=VALUES(renew_runtime)";
            u::query($sql_update_contract);
            u::query($sql_update_log_contract);
        }
        return true;
    }

    public function updateRenewReport($renewed_month = NULL, $student_id = NULL)
    {
        $cond = $student_id ? " AND s.id = $student_id" : '';
        $renewed_month = $renewed_month ? $renewed_month : date('Y-m', time() - 7 * 3600);
        $query = "SELECT c.student_id, c.id AS contract_id, t.branch_id, c.product_id, c.class_id, t.ec_id, t.cm_id, t.ec_leader_id,
                    t.cm_leader_id, t.ceo_branch_id, c.renewed_date, c.success_renewed_date,
                    IF (c.success_renewed_date IS NOT NULL AND DATE_FORMAT(c.success_renewed_date, '%Y-%m') <= '$renewed_month', 1, 2) renewed_status,
                    IF ( DATE_FORMAT(c.renewed_date, '%Y-%m')< '$renewed_month' , '$renewed_month', DATE_FORMAT(c.renewed_date, '%Y-%m') ) renewed_month,
                    c1.init_tuition_fee_id AS init_renew_tuition_fee_id, c.init_tuition_fee_id AS init_tuition_fee_id,
                    c1.must_charge AS renew_amount 
                FROM contracts c 
                    LEFT JOIN students s ON c.student_id = s.id 
                    LEFT JOIN term_student_user t ON t.student_id = c.student_id
                    LEFT JOIN contracts c1 ON c1.student_id=c.student_id 
                        AND c1.id = (SELECT id FROM contracts WHERE student_id=c.student_id AND count_recharge>c.count_recharge AND product_id !=4 AND completed_date IS NOT NULL ORDER BY count_recharge LIMIT 1) 
                WHERE s.status>0 AND ((DATE_FORMAT(c.renewed_date, '%Y-%m') >= '$renewed_month' OR DATE_FORMAT(c.success_renewed_date, '%Y-%m') >= '$renewed_month')) AND c.type>0
                $cond";
        $data = u::query($query);
        if ($data) {
            $cond_del = $student_id ? " AND student_id = $student_id" : '';
            u::query("DELETE FROM report_renews WHERE renewed_month >= '$renewed_month' $cond_del");
            self::addItemsRenew($data);
        }
        return "ok";
    }
    public function addItemsRenew($list)
    {
        if ($list) {
            $created_at = date('Y-m-d H:i:s');
            $query = "INSERT INTO report_renews (student_id, contract_id, branch_id, product_id, class_id, init_renew_tuition_fee_id, init_tuition_fee_id, ec_id, cm_id, renewed_cm_id, ec_leader_id, cm_leader_id, ceo_id, renew_amount, `status`, renewed_month, last_date, created_at) VALUES ";
            if (count($list) > 5000) {
                for ($i = 0; $i < 5000; $i++) {
                $item = $list[$i];
                $renewed_cm_id = $item->cm_id;
                $renew_amount = (int)$item->renew_amount;
                $query .= "('$item->student_id', '$item->contract_id', '$item->branch_id', '$item->product_id', '$item->class_id', '".(int)$item->init_renew_tuition_fee_id."', '".(int)$item->init_tuition_fee_id."', '".(int)$item->ec_id."', '".(int)$item->cm_id."', '".(int)$renewed_cm_id."','".(int)$item->ec_leader_id."','".(int)$item->cm_leader_id."',
                                '".(int)$item->ceo_branch_id."', '$renew_amount', '$item->renewed_status', '$item->renewed_month', '$item->renewed_date', '$created_at' ),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
                self::addItems(array_slice($list, 5000));
            } else {
                foreach ($list as $item) {
                $renewed_cm_id = $item->cm_id;
                $renew_amount = (int)$item->renew_amount;
                $query .= "('$item->student_id', '$item->contract_id', '$item->branch_id', '$item->product_id', '$item->class_id','".(int)$item->init_renew_tuition_fee_id."', '".(int)$item->init_tuition_fee_id."', '".(int)$item->ec_id."', '".(int)$item->cm_id."', '".(int)$renewed_cm_id."','".(int)$item->ec_leader_id."','".(int)$item->cm_leader_id."',
                                '".(int)$item->ceo_branch_id."', '$renew_amount', '$item->renewed_status', '$item->renewed_month', '$item->renewed_date', '$created_at' ),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
            }
        }
    }

    public function report02a(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $keyword = isset($request->keyword) ? $request->keyword : '';
        $start_date = data_get($request, 'start_date');
        $end_date = data_get($request, 'end_date');
        $class_id = data_get($request, 'class_id');
        $cm_id = data_get($request, 'cm_id');

        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        $cond = " r.`disabled` = 0 AND s.status>0 AND r.branch_id IN (" . Auth::user()->getBranchesHasUser().")";

        if (!empty($branch_id)) {
            $cond .= " AND r.branch_id IN (".implode(",",$branch_id).")";
        }
        
        if ($keyword !== '') {
            $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%') ";
        }

        if ($start_date) {
            $cond .= " AND r.last_date >= '$start_date'";
        }
        if ($end_date) {
            $cond .= " AND r.last_date <= '$end_date'";
        }

        if ($class_id) {
            $cond .= " AND r.class_id = '$class_id'";
        }
        if ($cm_id) {
            $cond .= " AND r.cm_id = '$cm_id'";
        }
        
        $order_by = " ORDER BY r.id DESC ";

        $total = u::first("SELECT count(r.id) AS total 
            FROM report_renews AS r LEFT JOIN students AS s ON s.id=r.student_id WHERE $cond");
        
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
                    WHERE $cond $order_by $limitation ");

        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function report02b(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $start_date = isset($request->start_date) ? $request->start_date : date('Y-m');

        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        $cond = " b.status = 1 AND b.id IN (" . Auth::user()->getBranchesHasUser().")";

        if (!empty($branch_id)) {
            $cond .= " AND b.id IN (".implode(",",$branch_id).")";
        }
        
        $order_by = " ORDER BY b.id ";
        $total = u::first("SELECT COUNT(b.id) total FROM branches b WHERE $cond");
        $renewSql = "SELECT COUNT(r.id) FROM report_renews AS r LEFT JOIN students AS s ON s.id=r.student_id WHERE s.status>0 AND  r.`disabled` = 0 AND r.renewed_month = '$start_date' AND r.branch_id =b.id";
        $list = u::query("SELECT
                        ($renewSql AND r.status>0) total_item,
                        ($renewSql AND r.status=1) success_item,
                        b.name branch_name
                    FROM branches b
                    WHERE $cond $order_by $limitation ");

        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function report02c(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $start_date = isset($request->start_date) ? $request->start_date : date('Y-m');

        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        $branch_query =  Auth::user()->getBranchesHasUser();
        if (!empty($branch_id)) {
            $branch_query=implode(",",$branch_id);
        }
        
        $order_by = " ORDER BY u.id ";
        $total = u::first("SELECT COUNT(u.id) total
                FROM users AS u 
                LEFT JOIN role_has_user AS ru ON u.id=ru.user_id
                LEFT JOIN branch_has_user AS bu ON bu.user_id=ru.user_id
            WHERE ru.role_id IN (55,56) AND (u.status =1 OR (u.status=0 AND (SELECT COUNT(id) FROM report_renews WHERE cm_id = ru.user_id AND `status` > 0 AND `disabled` = 0 AND renewed_month = '$start_date' AND branch_id IN ($branch_query))>0))");
        $renewSql = "SELECT COUNT(r.id) FROM report_renews AS r LEFT JOIN students AS s ON s.id=r.student_id WHERE s.status>0 AND r.cm_id = ru.user_id AND r.`disabled` = 0 AND r.renewed_month = '$start_date' AND r.branch_id IN ($branch_query)";
        $list = u::query("SELECT b.name AS branch_name, CONCAT(u.name, ' - ', u.hrm_id )AS cm_name, u.id AS cm_id, b.id AS branch_id,
            (SELECT ro.`name` FROM roles AS ro WHERE ru.role_id = ro.id LIMIT 1 ) role_name,
            ($renewSql AND r.status >0) total_item,
            ($renewSql AND r.status=1) success_item
            FROM users AS u 
                LEFT JOIN role_has_user AS ru ON u.id=ru.user_id
                LEFT JOIN branch_has_user AS bu ON bu.user_id=ru.user_id
                LEFT JOIN branches AS b ON b.id=bu.branch_id
            WHERE ru.role_id IN (55,56) AND (u.status =1 OR (u.status=0 AND (SELECT COUNT(id) FROM report_renews WHERE cm_id = ru.user_id AND `status` > 0 AND `disabled` = 0 AND renewed_month = '$start_date' AND branch_id IN ($branch_query))>0)) $order_by $limitation ");

        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function report05(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $start_date = isset($request->start_date) ? $request->start_date : date('Y-m-01');
        $end_date = isset($request->end_date) ? $request->end_date : date('Y-m-d');

        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 200;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        $branch_query =  Auth::user()->getBranchesHasUser();
        if (!empty($branch_id)) {
            $branch_query=implode(",",$branch_id);
        }
        
        $order_by = " ORDER BY b.id ";
        $total = u::first("SELECT COUNT(b.id) total
                FROM branches AS b 
            WHERE b.id IN ($branch_query) AND b.status=1");
        $list = u::query("SELECT b.name AS branch_name,
                (SELECT count(id) FROM contracts WHERE type=0 AND class_id IS NOT NULL AND enrolment_start_date >='$start_date' AND enrolment_start_date <= '$end_date' AND branch_id=b.id) AS num_trial,
                (SELECT count(DISTINCT p.contract_id) FROM payments AS p WHERE p.debt=0 AND p.charge_date >= '$start_date' AND p.charge_date <= '$end_date' AND p.branch_id=b.id ) AS num_full_fee,
                (SELECT count(DISTINCT p.contract_id) FROM payments AS p WHERE p.debt>0 AND (SELECT count(id) FROM payments WHERE contract_id=p.contract_id AND debt=0)=0 AND
                    p.charge_date >= '$start_date' AND p.charge_date <= '$end_date' AND p.branch_id=b.id ) AS num_deposit,
                (SELECT SUM(amount) FROM payments AS p WHERE p.charge_date >= '$start_date' AND p.charge_date <= '$end_date' AND p.branch_id=b.id ) AS total_amount
            FROM branches AS b 
            WHERE b.id IN ($branch_query) AND b.status=1 $order_by $limitation ");

        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function report103(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $start_date = isset($request->start_date) ? $request->start_date : date('Y-m-01');
        $end_date = isset($request->end_date) ? $request->end_date : date('Y-m-d');

        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        $branch_query =  Auth::user()->getBranchesHasUser();
        if (!empty($branch_id)) {
            $branch_query=implode(",",$branch_id);
        }
        $cond ="";
        if($start_date){
            $cond.= " AND csc.checkined_at >= '$start_date 00:00:00'";
        }
        if($end_date){
            $cond.= " AND csc.checkined_at <= '$end_date 23:59:59'";
        }
        
        $order_by = " ORDER BY s.id DESC";
        $total = u::first("SELECT COUNT(csc.id) total
            FROM crm_student_checkin AS csc 
                LEFT JOIN crm_students AS s ON s.id=csc.crm_student_id 
            WHERE csc.checkin_branch_id IN ($branch_query) AND csc.status >= 1 $cond");
        $list = u::query("SELECT s.name, ss.lms_id, csc.checkined_at, CONCAT(u.hrm_id, '-', u.name) AS ec_name, 
                b.name AS branch_name, p.name AS parent_name, p.mobile_1 AS parent_mobile,
                IF(ss.id IS NOT NULL, ss.date_of_birth, s.birthday) AS birthday
            FROM crm_student_checkin AS csc 
                LEFT JOIN crm_students AS s ON s.id=csc.crm_student_id   
                LEFT JOIN crm_parents AS p ON p.id = s.parent_id  
                LEFT JOIN students AS ss ON ss.id=s.lms_id
                LEFT JOIN users AS u ON u.id =csc.checkin_owner_id
                LEFT JOIN branches AS b ON b.id =csc.checkin_branch_id
            WHERE csc.checkin_branch_id IN ($branch_query) AND csc.status >= 1 $cond $order_by $limitation ");

        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function report104(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $start_date = isset($request->start_date) ? $request->start_date : date('Y-m-01');
        $end_date = isset($request->end_date) ? $request->end_date : date('Y-m-d');

        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        $branch_query =  Auth::user()->getBranchesHasUser();
        if (!empty($branch_id)) {
            $branch_query=implode(",",$branch_id);
        }
        $cond ="";
        if($start_date){
            $cond.= " AND c.enrolment_start_date >= '$start_date'";
        }
        if($end_date){
            $cond.= " AND c.enrolment_start_date <= '$end_date'";
        }
        
        $order_by = " ORDER BY c.enrolment_start_date DESC";
        $total = u::first("SELECT COUNT(c.id) total
                FROM contracts AS c 
                LEFT JOIN students AS s ON s.id = c.student_id
            WHERE c.branch_id IN ($branch_query) AND c.type=0  AND c.class_id IS NOT NULL $cond");
        $list = u::query("SELECT s.name, s.lms_id, CONCAT(u.hrm_id, '-', u.name) AS ec_name, 
                b.name AS branch_name, c.enrolment_start_date, c.enrolment_last_date,
                (SELECT `name` FROM programs WHERE id=p.parent_id) AS `level`,
                (SELECT count(id) FROM contracts WHERE student_id=c.student_id AND `type`>0 AND `status`>0) AS num_contract
            FROM contracts AS c 
                LEFT JOIN students AS s ON s.id = c.student_id
                LEFT JOIN users AS u ON u.id=c.ec_id
                LEFT JOIN branches AS b ON b.id=c.branch_id
                LEFT JOIN programs As p ON p.id=c.program_id
            WHERE c.branch_id IN ($branch_query) AND c.type=0  AND c.class_id IS NOT NULL $cond $order_by $limitation ");

        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function report202(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $start_date = isset($request->start_date) ? $request->start_date : date('Y-m-01');
        $end_date = isset($request->end_date) ? $request->end_date : date('Y-m-d');

        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        $branch_query =  Auth::user()->getBranchesHasUser();
        if (!empty($branch_id)) {
            $branch_query=implode(",",$branch_id);
        }
        $cond ="";
        if($start_date){
            $cond.= " AND p.charge_date >= '$start_date'";
        }
        if($end_date){
            $cond.= " AND p.charge_date <= '$end_date'";
        }
        
        $order_by = " ORDER BY p.charge_date DESC";
        $total = u::first("SELECT COUNT(s.id) total
            FROM payments AS p 
                LEFT JOIN contracts AS c ON c.id=p.contract_id 
                LEFT JOIN students AS s ON s.id=p.student_id
            WHERE  p.branch_id IN ($branch_query) AND s.lms_id IS NOT NULL $cond");
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
            WHERE p.branch_id IN ($branch_query) AND s.lms_id IS NOT NULL $cond $order_by $limitation ");

        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function report203(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $start_date = isset($request->start_date) ? $request->start_date : date('Y-m-01');
        $end_date = isset($request->end_date) ? $request->end_date : date('Y-m-d');

        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        $branch_query =  Auth::user()->getBranchesHasUser();
        if (!empty($branch_id)) {
            $branch_query=implode(",",$branch_id);
        }
        $cond ="";
        if($start_date){
            $cond.= " AND p.charge_date >= '$start_date'";
        }
        if($end_date){
            $cond.= " AND p.charge_date <= '$end_date'";
        }
        
        $order_by = " ORDER BY p.charge_date DESC";
        $total = u::first("SELECT COUNT(s.id) total
            FROM payments AS p 
                LEFT JOIN contracts AS c ON c.id=p.contract_id 
                LEFT JOIN students AS s ON s.id=p.student_id
            WHERE  p.branch_id IN ($branch_query) AND s.lms_id IS NOT NULL $cond");
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
            WHERE p.branch_id IN ($branch_query) AND s.lms_id IS NOT NULL $cond $order_by $limitation ");

        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function addItemPending($list) {
        if ($list) {
            $created_at = date('Y-m-d H:i:s');
            $query = "INSERT INTO report_pending (student_id, branch_id, product_id, cm_id, ec_id, contract_id,report_month,`sessions`,created_at ,tuition_fee_id,`start_date`) VALUES ";
            if (count($list) > 5000) {
                for($i = 0; $i < 5000; $i++) {
                    $item = $list[$i];
                    $start_date = $item->start_date ? $item->start_date : '2000-01-01';
                    $query.= "('$item->student_id', '$item->branch_id', '$item->product_id', '".(int)$item->cm_id."', '".(int)$item->ec_id."', '$item->contract_id', '$item->report_month', '$item->sessions', '$created_at' , '$item->tuition_fee_id', '$start_date'),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
                u::query("UPDATE report_pending SET start_date = null WHERE start_date = '2000-01-01'"); // Reset start_date to null if it was set to '0000-00-00'
                self::addItemPending(array_slice($list, 5000));
            } else {
                foreach($list as $item) {
                    $start_date = $item->start_date ? $item->start_date : '2000-01-01';
                    $query.= "('$item->student_id', '$item->branch_id', '$item->product_id', '".(int)$item->cm_id."', '".(int)$item->ec_id."', '$item->contract_id', '$item->report_month', '$item->sessions', '$created_at', '$item->tuition_fee_id', '$start_date'),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
                u::query("UPDATE report_pending SET start_date = null WHERE start_date = '2000-01-01'"); // Reset start_date to null if it was set to '0000-00-00'
            }
        }
    }

    public function collectReportPending(Request $request, $month)
    {
        if($month && $month!="_"){
            $report_month = (int)$month > 9 ? date("Y").'-'.(int)$month : date("Y").'-0'.(int)$month;
        }else{
            $report_month = date('Y-m',time()-7*3600);
        }
        $date = date('Y-m-d',time()-7*3600);
        $list = u::query("SELECT DISTINCT s.id AS student_id,c.branch_id,c.product_id,t.cm_id,t.ec_id,
                c.tuition_fee_id,c.summary_sessions AS `sessions`,c.start_date, c.end_date,c.id AS contract_id,c.tuition_fee_id,
                '$report_month' report_month
            FROM contracts AS c 
                LEFT JOIN students AS s ON s.id=c.student_id 
                LEFT JOIN term_student_user AS t ON t.student_id=s.id AND t.status=1
            WHERE s.status>0 AND c.status=3 AND c.class_id IS NULL AND c.type>0 AND c.summary_sessions>0
                AND c.id = (SELECT id FROM contracts WHERE student_id=s.id AND `status`!=7 ORDER BY count_recharge LIMIT 1) ");
        u::query("DELETE FROM report_pending WHERE report_month = '$report_month'");
        if (count($list)) {
            self::addItemPending($list);
        }
        return response()->json('ok');
    }

    public function addItemReserve($list) {
        if ($list) {
            $created_at = date('Y-m-d H:i:s');
            $query = "INSERT INTO report_reserve (student_id, branch_id, product_id, cm_id, ec_id, contract_id,report_month,`sessions`,created_at, is_reserved,tuition_fee_id, start_date, end_date) VALUES ";
            if (count($list) > 5000) {
                for($i = 0; $i < 5000; $i++) {
                    $item = $list[$i];
                    $start_date = $item->start_date ? $item->start_date : '2000-01-01';
                    $end_date = $item->end_date ? $item->end_date : '2000-01-01';
                    $query.= "('$item->student_id', '$item->branch_id', '$item->product_id', '".(int)$item->cm_id."', '".(int)$item->ec_id."', '$item->contract_id', '$item->report_month', '$item->sessions', '$created_at','".(int)$item->is_reserved."','$item->tuition_fee_id', '$start_date', '$end_date'),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
                u::query("UPDATE report_reserve SET start_date = null WHERE start_date = '2000-01-01'"); // Reset start_date to null if it was set to '0000-00-00'
                u::query("UPDATE report_reserve SET end_date = null WHERE end_date = '2000-01-01'"); // Reset start_date to null if it was set to '0000-00-00'
                self::addItemReserve(array_slice($list, 5000));
            } else {
                foreach($list as $item) {
                    $start_date = $item->start_date ? $item->start_date : '2000-01-01';
                    $end_date = $item->end_date ? $item->end_date : '2000-01-01';
                    $query.= "('$item->student_id', '$item->branch_id', '$item->product_id', '".(int)$item->cm_id."', '".(int)$item->ec_id."', '$item->contract_id', '$item->report_month', '$item->sessions', '$created_at','".(int)$item->is_reserved."','$item->tuition_fee_id', '$start_date', '$end_date'),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
                u::query("UPDATE report_reserve SET start_date = null WHERE start_date = '2000-01-01'"); // Reset start_date to null if it was set to '0000-00-00'
                u::query("UPDATE report_reserve SET end_date = null WHERE end_date = '2000-01-01'"); // Reset start_date to null if it was set to '0000-00-00'
            }
        }
    }

    public function collectReportReserve(Request $request, $month)
    {
        if($month && $month!="_"){
            $report_month = (int)$month > 9 ? date("Y").'-'.(int)$month : date("Y").'-0'.(int)$month;
        }else{
            $report_month = date('Y-m',time()-7*3600);
        }
        $date = date('Y-m-d',time()-7*3600);
        $list = u::query("SELECT DISTINCT s.id AS student_id,c.branch_id,c.product_id,t.cm_id,t.ec_id,
                c.tuition_fee_id,c.summary_sessions AS `sessions`,c.start_date, c.end_date,c.id AS contract_id,c.tuition_fee_id,
                (SELECT is_reserved FROM reserves WHERE contract_id=c.id AND student_id=s.id AND end_date>= '$date' AND `start_date`<='$date' AND `status`=4 LIMIT 1) AS is_reserved,
                (SELECT start_date FROM reserves WHERE contract_id=c.id AND student_id=s.id AND end_date>= '$date' AND `start_date`<='$date' AND `status`=4 LIMIT 1) AS start_date,
                (SELECT end_date FROM reserves WHERE contract_id=c.id AND student_id=s.id AND end_date>= '$date' AND `start_date`<='$date' AND `status`=4 LIMIT 1) AS end_date,
                '$report_month' report_month
            FROM contracts AS c 
                LEFT JOIN students AS s ON s.id=c.student_id 
                LEFT JOIN term_student_user AS t ON t.student_id=s.id AND t.status=1
            WHERE s.status>0 AND c.status=4 AND c.class_id IS NULL AND c.type>0 AND c.summary_sessions>0
                AND c.id = (SELECT id FROM contracts WHERE student_id=s.id AND `status`!=7 ORDER BY count_recharge LIMIT 1)");
        u::query("DELETE FROM report_reserve WHERE report_month = '$report_month'");
        if (count($list)) {
            self::addItemReserve($list);
        }
        return response()->json('ok');
    }

    public function report06(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $keyword = isset($request->keyword) ? $request->keyword : '';
        $start_date = isset($request->start_date) ? $request->start_date : date('Y-m');

        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";

        if ($start_date == date('Y-m')){
            $date = date('Y-m-d',time()-7*3600);
            $cond = " AND c.branch_id IN (" . Auth::user()->getBranchesHasUser().")";
            if (!empty($branch_id)) {
                $cond .= " AND c.branch_id IN (".implode(",",$branch_id).")";
            }
            
            if ($keyword !== '') {
                $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%') ";
            }
            
            $order_by = " ORDER BY c.id DESC ";
            $total = u::first("SELECT count(c.id) AS total 
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
                $cond");
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
                $cond $order_by $limitation");
        } else {
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
                FROM report_reserve AS r LEFT JOIN students AS s ON s.id=r.student_id WHERE $cond");
            
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
                WHERE $cond $order_by $limitation");
        }

        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function report07(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $keyword = isset($request->keyword) ? $request->keyword : '';
        $start_date = isset($request->start_date) ? $request->start_date : date('Y-m');

        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        if ($start_date == date('Y-m')) {
            $cond = " AND c.branch_id IN (" . Auth::user()->getBranchesHasUser().")";

            if (!empty($branch_id)) {
                $cond .= " AND c.branch_id IN (".implode(",",$branch_id).")";
            }
            
            if ($keyword !== '') {
                $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%') ";
            }
            $order_by = " ORDER BY c.id DESC ";
            $total = u::first("SELECT count(c.id) AS total 
               FROM contracts AS c 
                LEFT JOIN students AS s ON s.id=c.student_id 
                LEFT JOIN branches AS b ON b.id = c.branch_id
                LEFT JOIN products AS p ON p.id = c.product_id
                LEFT JOIN tuition_fee AS t ON t.id = c.tuition_fee_id
            WHERE s.status>0 AND c.status=3 AND c.class_id IS NULL AND c.type>0 AND c.summary_sessions>0
                AND c.id = (SELECT id FROM contracts WHERE student_id=s.id AND `status`!=7 ORDER BY count_recharge LIMIT 1) 
                $cond");

            $list = u::query("SELECT b.name AS branch_name, s.lms_code, s.name, s.gud_name1, p.name AS product_name,
                    t.name AS tuition_fee_name, c.summary_sessions,c.start_date, c.id
            FROM contracts AS c 
                LEFT JOIN students AS s ON s.id=c.student_id 
                LEFT JOIN branches AS b ON b.id = c.branch_id
                LEFT JOIN products AS p ON p.id = c.product_id
                LEFT JOIN tuition_fee AS t ON t.id = c.tuition_fee_id
            WHERE s.status>0 AND c.status=3 AND c.class_id IS NULL AND c.type>0 AND c.summary_sessions>0
                AND c.id = (SELECT id FROM contracts WHERE student_id=s.id AND `status`!=7 ORDER BY count_recharge LIMIT 1) 
                $cond $order_by $limitation");
        } else{
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
                FROM report_pending AS r LEFT JOIN students AS s ON s.id=r.student_id WHERE $cond");
            
            $list = u::query("SELECT b.name AS branch_name, s.lms_code, s.name, s.gud_name1, p.name AS product_name,
                    t.name AS tuition_fee_name, c.summary_sessions,r.start_date
                FROM report_pending AS r 
                    LEFT JOIN contracts AS c ON c.id = r.contract_id
                    LEFT JOIN students AS s ON s.id=r.student_id
                    LEFT JOIN branches AS b ON b.id = r.branch_id
                    LEFT JOIN products AS p ON p.id = r.product_id
                    LEFT JOIN tuition_fee AS t ON t.id = r.tuition_fee_id
                WHERE $cond $order_by $limitation");
        }

        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function report102(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $start_date = isset($request->start_date) ? $request->start_date : date('Y-m');
        $keyword = isset($request->keyword) ? $request->keyword : '';

        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";

        $cond = " AND bu.branch_id IN (" . Auth::user()->getBranchesHasUser().")";
        if (!empty($branch_id)) {
            $cond .= " AND bu.branch_id IN (".implode(",",$branch_id).")";
        }
        if ($keyword){
            $cond .= " AND (u.hrm_id LIKE '%$keyword%' OR u.name LIKE '$keyword')";
        }
        
        $order_by = " ORDER BY u.id ";
        $total = u::first("SELECT COUNT(u.id) total
                FROM users AS u 
                LEFT JOIN role_has_user AS ru ON u.id=ru.user_id
                LEFT JOIN branch_has_user AS bu ON bu.user_id=ru.user_id
            WHERE ru.role_id IN (68,69) AND u.status =1 $cond ");
        $list = u::query("SELECT u.name, u.hrm_id,
                (SELECT COUNT(id) FROM crm_student_checkin WHERE checkin_owner_id = u.id AND status >= 2 AND DATE_FORMAT(checkined_at, '%Y-%m')= '$start_date') AS count_checkin,
                (SELECT COUNT(id) FROM contracts WHERE ec_id = u.id AND type=0 AND DATE_FORMAT(enrolment_start_date, '%Y-%m')= '$start_date') AS count_trial,
                (SELECT COUNT(DISTINCT p.contract_id) FROM payments AS p LEFT JOIN contracts AS c ON c.id=p.contract_id WHERE c.debt_amount>0 AND DATE_FORMAT(p.charge_date, '%Y-%m')= '$start_date' AND p.ec_id=u.id) AS count_deposit,
                (SELECT COUNT(DISTINCT p.contract_id) FROM payments AS p WHERE p.debt=0 AND DATE_FORMAT(p.charge_date, '%Y-%m')= '$start_date' AND p.ec_id=u.id) AS count_full_fee,
                (SELECT COUNT(DISTINCT p.contract_id) FROM payments AS p LEFT JOIN contracts AS c ON c.id=p.contract_id WHERE c.count_recharge>0 AND DATE_FORMAT(p.charge_date, '%Y-%m')= '$start_date' AND p.ec_id=u.id) AS count_renew,
                (SELECT SUM(p.amount) FROM payments AS p LEFT JOIN contracts AS c ON c.id=p.contract_id WHERE c.count_recharge=0 AND DATE_FORMAT(p.charge_date, '%Y-%m')= '$start_date' AND p.ec_id=u.id) AS sales_new,
                (SELECT SUM(p.amount) FROM payments AS p LEFT JOIN contracts AS c ON c.id=p.contract_id WHERE c.count_recharge>0 AND DATE_FORMAT(p.charge_date, '%Y-%m')= '$start_date' AND p.ec_id=u.id) AS sales_renew,
                (SELECT SUM(c.debt_amount) FROM contracts AS c WHERE c.debt_amount > 0 AND DATE_FORMAT(c.created_at, '%Y-%m')= '$start_date' AND c.ec_id=u.id) AS total_deposit
            FROM users AS u 
                LEFT JOIN role_has_user AS ru ON u.id=ru.user_id
                LEFT JOIN branch_has_user AS bu ON bu.user_id=ru.user_id
            WHERE ru.role_id IN (68,69) AND u.status =1 $cond $order_by $limitation");

        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }
    public function report101(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $start_date = isset($request->start_date) ? $request->start_date : date('Y-m-01');
        $end_date = isset($request->end_date) ? $request->end_date : date('Y-m-d');

        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";

        $cond = " AND b.id IN (" . Auth::user()->getBranchesHasUser().")";
        if (!empty($branch_id)) {
            $cond .= " AND b.id IN (".implode(",",$branch_id).")";
        }
        
        $order_by = " ORDER BY b.id ";
        $total = u::first("SELECT COUNT(b.id) total
                FROM branches AS b 
            WHERE b.status =1 $cond AND b.id > 10");
        $list = u::query("SELECT b.name AS branch_name,
                (SELECT COUNT(id) FROM crm_student_checkin WHERE checkin_branch_id = b.id AND status >= 2 AND checkined_at >= '$start_date 00:00:00' AND checkined_at <= '$end_date 23:59:59') AS count_checkin,
                (SELECT COUNT(id) FROM contracts WHERE branch_id = b.id AND type=0 AND enrolment_start_date >= '$start_date' AND enrolment_start_date <= '$end_date') AS count_trial,
                (SELECT COUNT(DISTINCT p.contract_id) FROM payments AS p LEFT JOIN contracts AS c ON c.id=p.contract_id WHERE c.debt_amount>0 AND p.charge_date>= '$start_date' AND p.charge_date <= '$end_date'  AND p.branch_id=b.id) AS count_deposit,
                (SELECT COUNT(DISTINCT p.contract_id) FROM payments AS p WHERE p.debt=0 AND p.charge_date >= '$start_date' AND p.charge_date <= '$end_date' AND p.branch_id=b.id) AS count_full_fee,
                (SELECT COUNT(DISTINCT p.contract_id) FROM payments AS p LEFT JOIN contracts AS c ON c.id=p.contract_id WHERE c.count_recharge=0 AND p.charge_date >= '$start_date' AND p.charge_date <= '$end_date' AND p.branch_id=b.id) AS count_new,
                (SELECT COUNT(DISTINCT p.contract_id) FROM payments AS p LEFT JOIN contracts AS c ON c.id=p.contract_id WHERE c.count_recharge>0 AND p.charge_date >= '$start_date' AND p.charge_date <= '$end_date' AND p.branch_id=b.id) AS count_renew,
                (SELECT SUM(p.amount) FROM payments AS p LEFT JOIN contracts AS c ON c.id=p.contract_id WHERE c.count_recharge=0 AND p.charge_date >= '$start_date' AND p.charge_date <= '$end_date' AND p.branch_id=b.id) AS sales_new,
                (SELECT SUM(p.amount) FROM payments AS p LEFT JOIN contracts AS c ON c.id=p.contract_id WHERE c.count_recharge>0 AND p.charge_date >= '$start_date' AND p.charge_date <= '$end_date' AND p.branch_id=b.id) AS sales_renew,
                (SELECT SUM(c.debt_amount) FROM contracts AS c WHERE c.debt_amount > 0 AND c.created_at >= '$start_date 00:00:00' AND c.created_at <= '$end_date 00:00:00' AND c.branch_id=b.id) AS total_deposit
            FROM branches AS b 
            WHERE b.status =1 $cond AND b.id > 10 $order_by $limitation");

        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function report08(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $start_date = isset($request->start_date) ? $request->start_date : date('Y-m');
        $keyword = isset($request->keyword) ? $request->keyword : '';
        $class_id = isset($request->class_id) ? $request->class_id : '';
        $cm_id = isset($request->cm_id) ? $request->cm_id : '';

        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";

        if ($start_date == date('Y-m')) {
            $cond = " AND c.branch_id IN (" . Auth::user()->getBranchesHasUser().")";
            if (!empty($branch_id)) {
                $cond .= " AND c.branch_id IN (".implode(",",$branch_id).")";
            }
            
            if ($keyword !== '') {
                $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%') ";
            }
            if ($class_id) {
                $cond .= " AND c.class_id = '$class_id'";
            }elseif ($cm_id) {
                $cond .= " AND c.cm_id = '$cm_id'";
            }
            $order_by = " ORDER BY c.id DESC ";
            $total = u::first("SELECT count(c.id) AS total 
                FROM
                    contracts c
                    LEFT JOIN students s ON c.student_id = s.id
                    LEFT JOIN branches AS b ON b.id = c.branch_id
                    LEFT JOIN classes cls ON c.class_id = cls.id
            WHERE
                c.`status` = 6 $cond");
               
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
                c.`status` = 6 $cond $order_by $limitation");
        } else {
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
            if ($class_id) {
                $cond .= " AND r.class_id = '$class_id'";
            }
            if ($cm_id) {
                $cond .= " AND r.cm_id = '$cm_id'";
            }
            
            $order_by = " ORDER BY r.id DESC ";
    
            $total = u::first("SELECT count(r.id) AS total 
                FROM report_student_in_class AS r LEFT JOIN students AS s ON s.id=r.student_id WHERE $cond");
            
            $list = u::query("SELECT r.id, sem.name AS semester_name, b.name AS branch_name, cls.cls_name AS class_name, 
                    s.date_of_birth, s.lms_code, s.name, s.gud_name1, s.gud_mobile1, CONCAT (u.hrm_id, ' - ', u.name) AS cm_name
                FROM report_student_in_class AS r 
                    LEFT JOIN students AS s ON s.id=r.student_id
                    LEFT JOIN branches AS b ON b.id = r.branch_id
                    LEFT JOIN classes AS cls ON cls.id = r.class_id
                    LEFT JOIN programs AS p ON p.id = cls.program_id
                    LEFT JOIN semesters AS sem ON sem.id = p.semester_id
                    LEFT JOIN users AS u ON u.id= r.cm_id
                WHERE $cond $order_by $limitation");
        }

        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function updateReportStudentInClass()
    {
        $report_month =  date('Y-m', time() - 7 * 3600);
        $data = u::query("SELECT c.student_id, c.class_id, c.branch_id, c.product_id, c.id AS contract_id, 
            cls.teacher_id, c.type AS contract_type, c.cm_id, '$report_month' AS report_month
        FROM
            contracts c
            LEFT JOIN classes AS cls ON c.class_id = cls.id
        WHERE
            c.`status` = 6");
        if ($data) {
            u::query("DELETE FROM report_student_in_class WHERE report_month >= '$report_month' ");
            self::addItemsReportStudentInClass($data);
        }
        return "ok";
    }
    public function addItemsReportStudentInClass($list)
    {
        if ($list) {
            $created_at = date('Y-m-d H:i:s');
            $query = "INSERT INTO report_student_in_class (student_id, class_id, branch_id, product_id, teacher_id, cm_id, report_month, contract_id, contract_type, created_at) VALUES ";
            if (count($list) > 5000) {
                for ($i = 0; $i < 5000; $i++) {
                    $item = $list[$i];
                    $query .= "('".(int)$item->student_id."', '".(int)$item->class_id."', '".(int)$item->branch_id."', '".(int)$item->product_id."', '".(int)$item->teacher_id."', '".(int)$item->cm_id."', '".$item->report_month."', '".(int)$item->contract_id."','".(int)$item->contract_type."', '$created_at' ),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
                self::addItems(array_slice($list, 5000));
            } else {
                foreach ($list as $item) {
                    $query .= "('".(int)$item->student_id."', '".(int)$item->class_id."', '".(int)$item->branch_id."', '".(int)$item->product_id."', '".(int)$item->teacher_id."', '".(int)$item->cm_id."', '".$item->report_month."', '".(int)$item->contract_id."','".(int)$item->contract_type."', '$created_at' ),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
            }
        }
    }
}
