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
            c.enrolment_last_date, cls.program_id,
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
                AND ( ( c.enrolment_start_date <= ( SELECT class_date FROM schedules WHERE class_id = c.class_id AND class_date >= '$end_date' AND `status`=1 ORDER BY class_date ASC LIMIT 1 )
                    AND c.enrolment_last_date >= ( SELECT class_date FROM schedules WHERE class_id = c.class_id AND class_date <= '$end_date' AND `status`=1 ORDER BY class_date ASC LIMIT 1 )
                    ) OR (SELECT count(id) FROM class_transfer WHERE student_id=s.id AND `status` = 2 AND transfer_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)) > 0
                )
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
            $query = "INSERT INTO report_full_fee_active (student_id,contract_id, class_id, product_id, cm_id, report_month, branch_id, created_at, creator_id,end_date,`start_date`,done_sessions,summary_sessions,last_done_sessions, `type`, tuition_fee_id, init_tuition_fee_id, program_id) VALUES ";
            if (count($list) > 5000) {
                for($i = 0; $i < 5000; $i++) {
                    $item = $list[$i];
                    $enrolment_last_date = $item->enrolment_last_date ? $item->enrolment_last_date : '2000-01-01';
                    $enrolment_start_date = $item->enrolment_start_date ? $item->enrolment_start_date : '2000-01-01';
                    $query.= "('$item->student_id', '$item->contract_id', '$item->class_id', '$item->product_id', '".(int)$item->cm_id."', '$item->report_month', '$item->branch_id', '$created_at', 99999,'$enrolment_last_date','$enrolment_start_date','$item->done_sessions','$item->summary_sessions','$item->last_done_sessions','$item->type_fee', '$item->tuition_fee_id', '$item->init_tuition_fee_id', '$item->program_id'),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
                self::addItems(array_slice($list, 5000));
            } else {
                foreach($list as $item) {
                    $enrolment_last_date = $item->enrolment_last_date ? $item->enrolment_last_date : '2000-01-01';
                    $enrolment_start_date = $item->enrolment_start_date ? $item->enrolment_start_date : '2000-01-01';
                    $query.= "('$item->student_id', '$item->contract_id', '$item->class_id', '$item->product_id', '".(int)$item->cm_id."', '$item->report_month', '$item->branch_id', '$created_at', 99999,'$enrolment_last_date','$enrolment_start_date','$item->done_sessions','$item->summary_sessions','$item->last_done_sessions','$item->type_fee', '$item->tuition_fee_id', '$item->init_tuition_fee_id', '$item->program_id'),";
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
                    AND ( ( c.enrolment_start_date <= ( SELECT class_date FROM schedules WHERE class_id = c.class_id AND class_date >= '$end_date' AND `status`=1 ORDER BY class_date ASC LIMIT 1 )
                        AND c.enrolment_last_date >= ( SELECT class_date FROM schedules WHERE class_id = c.class_id AND class_date <= '$end_date' AND `status`=1 ORDER BY class_date ASC LIMIT 1 )
                        ) OR (SELECT count(id) FROM class_transfer WHERE student_id=s.id AND `status` = 2 AND transfer_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)) > 0
                    )
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
                    AND ( ( c.enrolment_start_date <= ( SELECT class_date FROM schedules WHERE class_id = c.class_id AND class_date >= '$end_date' AND `status`=1 ORDER BY class_date ASC LIMIT 1 )
                        AND c.enrolment_last_date >= ( SELECT class_date FROM schedules WHERE class_id = c.class_id AND class_date <= '$end_date' AND `status`=1 ORDER BY class_date ASC LIMIT 1 )
                        ) OR (SELECT count(id) FROM class_transfer WHERE student_id=s.id AND `status` = 2 AND transfer_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)) > 0
                    )
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
        $renewSqlAmount = "SELECT SUM(r.renew_amount) FROM report_renews AS r LEFT JOIN students AS s ON s.id=r.student_id WHERE s.status>0 AND r.cm_id = ru.user_id AND r.`disabled` = 0 AND r.renewed_month = '$start_date' AND r.branch_id IN ($branch_query)";
        $list = u::query("SELECT b.name AS branch_name, CONCAT(u.name, ' - ', u.hrm_id )AS cm_name, u.id AS cm_id, b.id AS branch_id,
            (SELECT ro.`name` FROM roles AS ro WHERE ru.role_id = ro.id LIMIT 1 ) role_name,
            ($renewSql AND r.status >0) total_item,
            ($renewSql AND r.status=1) success_item,
            ($renewSqlAmount AND r.status=1) renew_amount
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
            $cond.= " AND csc.checkin_at >= '$start_date 00:00:00'";
        }
        if($end_date){
            $cond.= " AND csc.checkin_at <= '$end_date 23:59:59'";
        }
        
        $order_by = " ORDER BY s.id DESC";
        $total = u::first("SELECT COUNT(csc.id) total
            FROM crm_student_checkin AS csc 
                LEFT JOIN crm_students AS s ON s.id=csc.crm_student_id 
            WHERE csc.checkin_branch_id IN ($branch_query) AND csc.status >= 1 $cond");
        $list = u::query("SELECT s.name, ss.lms_code, ss.lms_id, csc.checkin_at AS checkined_at, CONCAT(u.hrm_id, '-', u.name) AS ec_name, 
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
        $list = u::query("SELECT s.name, s.lms_code, s.lms_id, CONCAT(u.hrm_id, '-', u.name) AS ec_name, 
                b.name AS branch_name, c.enrolment_start_date, c.enrolment_last_date,
                COALESCE((SELECT `name` FROM programs WHERE id=p.parent_id), p.name) AS `level`,
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
        $list = u::query("SELECT p.charge_date AS payment_date, s.name AS stu_name, COALESCE(s.lms_code, s.lms_id) AS std_id, c.branch_id, c.product_id, c.program_id,
                p.must_charge AS total_fee, p.amount AS payment_amount, p.debt AS remaining_amount, 
                p.total, t.number_of_months, t.session, c.start_date, p.contract_id,
                (SELECT min(charge_date) FROM payments WHERE contract_id = p.contract_id) AS period_from,
                (SELECT max(charge_date) FROM payments WHERE contract_id = p.contract_id) AS period_to, 
                c.note, p.method, b.name AS branch_name, pd.name AS product_name,
                COALESCE((SELECT `name` FROM programs WHERE id =pg.parent_id), pg.name) AS `level`,
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
        $list = u::query("SELECT p.charge_date AS payment_date, s.name AS stu_name, COALESCE(s.lms_code, s.lms_id) AS std_id, c.branch_id, c.product_id, c.program_id,
                p.must_charge AS total_fee, p.amount AS payment_amount, p.debt AS remaining_amount, 
                p.total, t.number_of_months, t.session, c.start_date, p.contract_id,
                (SELECT min(charge_date) FROM payments WHERE contract_id = p.contract_id) AS period_from,
                (SELECT max(charge_date) FROM payments WHERE contract_id = p.contract_id) AS period_to, 
                c.note, p.method, b.name AS branch_name, pd.name AS product_name,
                COALESCE((SELECT `name` FROM programs WHERE id =pg.parent_id), pg.name) AS `level`,
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
        $list = u::query("SELECT u.name, u.hrm_id, b.name AS branch_name,
                (SELECT COUNT(DISTINCT p.contract_id) FROM payments AS p WHERE p.debt=0 AND DATE_FORMAT(p.charge_date, '%Y-%m')= '$start_date' AND p.ec_id=u.id) AS count_full_fee,
                (SELECT SUM(p.amount) FROM payments AS p LEFT JOIN contracts AS c ON c.id=p.contract_id WHERE DATE_FORMAT(p.charge_date, '%Y-%m')= '$start_date' AND p.ec_id=u.id) AS total_amount
            FROM users AS u 
                LEFT JOIN role_has_user AS ru ON u.id=ru.user_id
                LEFT JOIN branch_has_user AS bu ON bu.user_id=ru.user_id
                LEFT JOIN branches AS b ON b.id=bu.branch_id
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

    public function report09(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $report_month = isset($request->start_date) ? $request->start_date : date('Y-m');

        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";

        $cond = " AND b.id IN (" . Auth::user()->getBranchesHasUser().")";
        if (!empty($branch_id)) {
            $cond .= " AND b.id IN (".implode(",",$branch_id).")";
        }
        $order_by = " ORDER BY b.id DESC ";
        $total = u::first("SELECT count(b.id) AS total 
            FROM branches AS b 
        WHERE
            b.status=1 $cond");

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
            b.status=1 $cond $order_by $limitation");
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function collectReportUser(Request $request, $month, $branch)
    {
        if($month && $month!="_"){
            $report_month = (int)$month > 9 ? date("Y").'-'.(int)$month : date("Y").'-0'.(int)$month;
        }else{
            $report_month = date('Y-m',time()-7*3600);
        }
        $where = "";
        $where_del = "";
        if (count(explode(',', $branch)) > 0 && $branch != '_') {
            $where = " AND bhu.branch_id IN ($branch)";
            $where_del = " AND branch_id IN ($branch)";
        }
        u::query("DELETE FROM report_users WHERE report_month = '$report_month' $where_del ");
        $list = u::query("SELECT DISTINCT bhu.user_id, bhu.branch_id, rhu.role_id, '$report_month' AS report_month
        FROM
            users AS u
            LEFT JOIN branch_has_user bhu ON bhu.user_id = u.id
            LEFT JOIN role_has_user rhu ON rhu.user_id = u.id
        WHERE
            u.status= 1 AND rhu.role_id IN (55,56,68,69,686868) $where ");
        if (count($list)) {
            self::addItemsReportUser($list);
        }
        return true;
    }

    public function addItemsReportUser($list) {
        if ($list) {
            $created_at = date('Y-m-d H:i:s');
            $query = "INSERT INTO report_users (user_id,branch_id, role_id, report_month, created_at) VALUES ";
            if (count($list) > 5000) {
                for($i = 0; $i < 5000; $i++) {
                    $item = $list[$i];
                    $query.= "('$item->user_id', '$item->branch_id', '$item->role_id', '$item->report_month', '$created_at'),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
                self::addItemsReportUser(array_slice($list, 5000));
            } else {
                foreach($list as $item) {
                    $query.= "('$item->user_id', '$item->branch_id', '$item->role_id', '$item->report_month', '$created_at'),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
            }
        }
    }

    public function collectReportClasses(Request $request, $month, $branch)
    {
        if($month && $month!="_"){
            $report_month = (int)$month > 9 ? date("Y").'-'.(int)$month : date("Y").'-0'.(int)$month;
        }else{
            $report_month = date('Y-m',time()-7*3600);
        }
        $where = "";
        $where_del = "";
        if (count(explode(',', $branch)) > 0 && $branch != '_') {
            $where = " AND cl.branch_id IN ($branch)";
            $where_del = " AND branch_id IN ($branch)";
        }
        u::query("DELETE FROM report_classes WHERE report_month = '$report_month' $where_del ");
        $list = u::query("SELECT cl.* , '$report_month' AS report_month
        FROM
            classes AS cl
        WHERE
            cl.status= 1 AND cl.cls_enddate >= CURRENT_DATE AND cl.cls_startdate <= CURRENT_DATE $where ");
        if (count($list)) {
            self::addItemsReportClasses($list);
        }
        return true;
    }

    public function addItemsReportClasses($list) {
        if ($list) {
            $created_at = date('Y-m-d H:i:s');
            $query = "INSERT INTO report_classes (branch_id, class_id, product_id, cm_id, cls_startdate, cls_enddate, program_id, `status`, report_month, created_at) VALUES ";
            if (count($list) > 5000) {
                for($i = 0; $i < 5000; $i++) {
                    $item = $list[$i];
                    $query.= "('$item->branch_id', '$item->id', '$item->product_id', '".(int)$item->cm_id."', '$item->cls_startdate', '$item->cls_enddate', '$item->program_id', '$item->status', '$item->report_month', '$created_at'),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
                self::addItemsReportClasses(array_slice($list, 5000));
            } else {
                foreach($list as $item) {
                    $query.= "('$item->branch_id', '$item->id', '$item->product_id', '".(int)$item->cm_id."', '$item->cls_startdate', '$item->cls_enddate', '$item->program_id', '$item->status', '$item->report_month', '$created_at'),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
            }
        }
    }

    public function report10(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $start_date = isset($request->start_date) ? $request->start_date : date('Y-m');
        $product_id = isset($request->product_id) ? $request->product_id : 1;
        if ($product_id == 0) {
            $listProgramSubs = u::query("SELECT * FROM program_subs WHERE product_id IN (1,2) AND status=1");
        } else {
            $listProgramSubs = u::query("SELECT * FROM program_subs WHERE product_id=$product_id AND status=1");
        }
        
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
        $list = u::query("SELECT b.name AS branch_name, b.id
            FROM branches AS b 
            WHERE b.status =1 $cond AND b.id > 10 $order_by $limitation");
        foreach ($list as $branch) {
            foreach ($listProgramSubs as $programSub) {
                $countStudent = u::first("SELECT COUNT(r.id) AS total FROM report_full_fee_active AS r
                        LEFT JOIN programs AS p ON p.id = r.program_id
                    WHERE r.branch_id = $branch->id AND r.report_month = '$start_date' AND r.product_id=$product_id AND p.program_sub_id = $programSub->id ");
                $countClass = u::first("SELECT COUNT(r.id) AS total FROM report_classes AS r
                        LEFT JOIN programs AS p ON p.id = r.program_id
                    WHERE r.branch_id = $branch->id AND r.report_month = '$start_date' AND r.product_id=$product_id AND r.status = 1 AND p.program_sub_id = $programSub->id ");
                $branch->{$programSub->id} = (object)[
                    'countStudent' => $countStudent->total,
                    'countClass' => $countClass->total
                ];
            }
        }

        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json([   
                'program_subs' => $listProgramSubs,
                'data' => $data]);
    }

    public function report11(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $report_month = isset($request->start_date) ? $request->start_date : date('Y-m');
        $keyword = isset($request->keyword) ? $request->keyword : 1;
        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";

        $cond = " AND ru.branch_id IN (" . Auth::user()->getBranchesHasUser().")";
        if (!empty($branch_id)) {
            $cond .= " AND ru.branch_id IN (".implode(",",$branch_id).")";
        }
        if (!$keyword){
            $cond .= " AND (u.name LIKE '%$keyword%' OR u.hrm_id LIKE '%$keyword%') ";
        }
        
        $order_by = " ORDER BY ru.branch_id, u.id ";
        $total = u::first("SELECT COUNT( DISTINCT u.id, ru.branch_id) total
                FROM report_users AS ru 
                LEFT JOIN users AS u ON u.id =ru.user_id
            WHERE ru.role_id IN(55,56) $cond ");
        $list = u::query("SELECT DISTINCT ru.branch_id, u.id, b.name AS branch_name, CONCAT(u.name, ' - ', u.hrm_id ) AS cm_name,
                (SELECT count(id) FROM report_full_fee_active WHERE cm_id=ru.user_id AND report_month = '$report_month' AND product_id=1 AND branch_id =ru.branch_id) AS countStudentApril,
                (SELECT count(id) FROM report_full_fee_active WHERE cm_id=ru.user_id AND report_month = '$report_month' AND product_id=2 AND branch_id =ru.branch_id) AS countStudentIgarten,
                (SELECT count(id) FROM report_classes WHERE cm_id=ru.user_id AND report_month = '$report_month' AND product_id=1 AND status=1 AND branch_id =ru.branch_id) AS countClassApril,
                (SELECT count(id) FROM report_classes WHERE cm_id=ru.user_id AND report_month = '$report_month' AND product_id=2 AND status=1 AND branch_id =ru.branch_id) AS countClassIgarten
            FROM report_users AS ru 
            LEFT JOIN users AS u ON u.id =ru.user_id
            LEFT JOIN branches AS b ON b.id=ru.branch_id
            WHERE ru.role_id IN(55,56) $cond $order_by $limitation");

        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    /**
     * Khởi tạo dữ liệu bộ lọc cho trang Báo cáo tập trung (/lms/reports)
     */
    public function initFilters(Request $request)
    {
        $user = Auth::user();
        $scope = $user->getReportScope(); // 'all' | 'team' | 'self'
        $branchIdsStr = $user->getBranchesHasUser();
        $branchCond = $branchIdsStr ? " AND b.id IN ($branchIdsStr) " : "";

        $branches = u::query("SELECT b.id, b.name FROM branches AS b WHERE b.status = 1 $branchCond ORDER BY b.id ASC");

        // Danh sách đội nhóm (Team Leader role = 69 hoặc là manager của nhân viên khác)
        $teamCond = "";
        if ($scope === 'team') {
            $teamCond = " AND u.id = " . (int)$user->id;
        } elseif ($scope === 'self') {
            $leaderId = (int)$user->manager_id;
            $teamCond = $leaderId > 0 ? " AND u.id = $leaderId " : " AND 1=0 ";
        }

        $leaders = u::query("SELECT DISTINCT u.id, u.name AS leader_name, u.hrm_id,
                (SELECT bhu.branch_id FROM branch_has_user bhu WHERE bhu.user_id = u.id LIMIT 1) AS branch_id
            FROM users AS u
            LEFT JOIN role_has_user AS ru ON ru.user_id = u.id
            WHERE u.status = 1
              AND (ru.role_id = 69 OR EXISTS (SELECT 1 FROM users sub WHERE sub.manager_id = u.id AND sub.status = 1))
              $teamCond
            ORDER BY u.id ASC");

        $teams = [];
        foreach ($leaders as $ld) {
            $members = u::query("SELECT id FROM users WHERE status = 1 AND (id = {$ld->id} OR manager_id = {$ld->id})");
            $memberIds = array_map(function ($m) {
                return (int)$m->id;
            }, $members);
            $teams[] = [
                'id' => (int)$ld->id,
                'leader_name' => $ld->leader_name,
                'hrm_id' => $ld->hrm_id,
                'label' => 'Team ' . $ld->leader_name,
                'branch_id' => (int)$ld->branch_id,
                'member_ids' => $memberIds,
            ];
        }

        // Danh sách nhân viên Sale / Quản lý trong phạm vi được phép xem
        $staffCond = "";
        if ($scope === 'self') {
            $staffCond = " AND u.id = " . (int)$user->id;
        } elseif ($scope === 'team') {
            $allowedIds = $user->getStaffHasUser();
            $staffCond = " AND u.id IN ($allowedIds) ";
        } elseif ($branchIdsStr) {
            $staffCond = " AND (EXISTS (SELECT 1 FROM branch_has_user bhu WHERE bhu.user_id = u.id AND bhu.branch_id IN ($branchIdsStr))
                OR EXISTS (SELECT 1 FROM crm_parents cp WHERE cp.owner_id = u.id)
                OR EXISTS (SELECT 1 FROM contracts ct WHERE ct.ec_id = u.id OR ct.creator_id = u.id)) ";
        }

        $sales = u::query("SELECT DISTINCT u.id, u.name AS full_name, u.name, u.hrm_id,
                (SELECT rhu.role_id FROM role_has_user rhu WHERE rhu.user_id = u.id LIMIT 1) AS role_id,
                u.manager_id,
                m.name AS manager_name,
                (SELECT bhu.branch_id FROM branch_has_user bhu WHERE bhu.user_id = u.id LIMIT 1) AS branch_id,
                (SELECT br.name FROM branch_has_user bhu JOIN branches br ON br.id = bhu.branch_id WHERE bhu.user_id = u.id LIMIT 1) AS branch_name
            FROM users AS u
            LEFT JOIN users AS m ON m.id = u.manager_id
            LEFT JOIN role_has_user AS ru ON ru.user_id = u.id
            WHERE u.status = 1
              AND (ru.role_id IN (68, 69, 686868, 999999)
                   OR EXISTS (SELECT 1 FROM crm_parents cp WHERE cp.owner_id = u.id)
                   OR EXISTS (SELECT 1 FROM contracts ct WHERE ct.ec_id = u.id))
              $staffCond
            ORDER BY u.id ASC");

        foreach ($sales as $s) {
            if (!empty($s->manager_name)) {
                $s->team_label = 'Team ' . $s->manager_name;
                $s->display_name = $s->full_name . ' / Team ' . $s->manager_name;
            } elseif ((int)$s->role_id === 69) {
                $s->team_label = 'Team ' . $s->full_name;
                $s->display_name = $s->full_name . ' / Team ' . $s->full_name;
            } else {
                $s->team_label = '';
                $s->display_name = $s->full_name;
            }
            $s->label = $s->full_name . ($s->hrm_id ? ' - ' . $s->hrm_id : '');
        }

        $sources = u::query("SELECT id, name FROM sources WHERE status = 1 ORDER BY id ASC");
        $sourceDetails = u::query("SELECT id, name, source_id FROM source_detail WHERE status = 1 ORDER BY id ASC");
        $products = u::query("SELECT id, name FROM products WHERE status = 1 ORDER BY id ASC");
        $creators = u::query("SELECT id, name AS full_name, hrm_id FROM users WHERE status = 1 ORDER BY id ASC");

        $statuses = [
            ['id' => 1, 'name' => '1. Chưa liên hệ'],
            ['id' => 2, 'name' => '2. Không liên hệ được (Thuê bao / Tắt máy)'],
            ['id' => 3, 'name' => '3. Liên hệ không thành công'],
            ['id' => 4, 'name' => '4. Đang chăm sóc'],
            ['id' => 5, 'name' => '5. Tiềm năng'],
            ['id' => 6, 'name' => '6. Không tiềm năng'],
            ['id' => 7, 'name' => '7. Đặt lịch hẹn Check-in'],
            ['id' => 8, 'name' => '8. Đã Check-in / Học thử'],
            ['id' => 9, 'name' => '9. Đã đóng phí (Nhập học)'],
            ['id' => 10, 'name' => '10. Tái tục'],
        ];

        return response()->json([
            'status' => 1,
            'data' => [
                'scope' => $scope,
                'can_manage_kpi' => in_array($scope, ['all', 'team']),
                'current_user' => [
                    'id' => (int)$user->id,
                    'full_name' => $user->full_name ?: $user->name,
                    'hrm_id' => $user->hrm_id,
                    'role_id' => (int)$user->role_id,
                    'manager_id' => (int)$user->manager_id,
                ],
                'branches' => $branches,
                'teams' => $teams,
                'sales' => $sales,
                'sources' => $sources,
                'source_details' => $sourceDetails,
                'products' => $products,
                'creators' => $creators,
                'statuses' => $statuses,
            ]
        ]);
    }

    /**
     * Báo cáo Tổng quan (Tab 1) & Báo cáo Đội nhóm (Tab 2)
     */
    public function overviewReport(Request $request)
    {
        $user = Auth::user();
        $scope = $user->getReportScope();
        $branchIdsStr = $user->getBranchesHasUser();

        $startDate = trim((string)$request->input('start_date', ''));
        $endDate = trim((string)$request->input('end_date', ''));
        $dateType = trim((string)$request->input('date_type', 'created_at')); // created_at | last_assign_date | care_date
        if (!in_array($dateType, ['created_at', 'last_assign_date', 'care_date'])) {
            $dateType = 'created_at';
        }

        $branchId = $request->input('branch_id');
        $teamLeaderId = (int)$request->input('team_leader_id', 0);
        $ownerId = (int)$request->input('owner_id', 0);
        $firstOwnerId = (int)$request->input('first_owner_id', 0);
        $contractType = trim((string)$request->input('contract_type', '')); // '' | 'new' | 'renew' | 'trial'
        $invoiceStartDate = trim((string)$request->input('invoice_start_date', ''));
        $invoiceEndDate = trim((string)$request->input('invoice_end_date', ''));
        $pacing = (string)$request->input('pacing', ''); // '' | '1' | '0'
        $trialStatus = (string)$request->input('trial_status', ''); // '' | '1' | '0'
        $crmStatus = (string)$request->input('status', '');
        $sourceIds = $request->input('source_ids', []);
        if (!is_array($sourceIds)) {
            $sourceIds = $sourceIds !== '' && $sourceIds !== null ? [(int)$sourceIds] : [];
        }
        $sourceIdSingle = (int)$request->input('source_id', 0);
        if ($sourceIdSingle > 0 && !in_array($sourceIdSingle, $sourceIds)) {
            $sourceIds[] = $sourceIdSingle;
        }
        $sourceIds = array_values(array_filter(array_map('intval', $sourceIds)));
        $sourceDetailId = (int)$request->input('source_detail_id', 0);
        $creatorId = (int)$request->input('creator_id', 0);

        // Xác định tháng/năm để lấy Chỉ tiêu KPI (sale_targets)
        $targetDateRef = $startDate ?: ($endDate ?: date('Y-m-d'));
        $targetYear = (int)date('Y', strtotime($targetDateRef));
        $targetMonth = (int)date('n', strtotime($targetDateRef));

        // 1. Xác định danh sách Nhân viên được hiển thị trên bảng báo cáo
        $userWhere = " u.status = 1 ";
        if ($scope === 'self') {
            $userWhere .= " AND u.id = " . (int)$user->id;
        } elseif ($scope === 'team') {
            $allowedStaff = $user->getStaffHasUser();
            $userWhere .= " AND u.id IN ($allowedStaff) ";
        }

        if ($teamLeaderId > 0) {
            $teamMembers = $user->getStaffHasUser($teamLeaderId);
            $userWhere .= " AND u.id IN ($teamMembers) ";
        }
        if ($ownerId > 0) {
            $userWhere .= " AND u.id = $ownerId ";
        }
        if (!empty($branchId)) {
            $bIds = is_array($branchId) ? array_map('intval', $branchId) : [(int)$branchId];
            $bIds = array_filter($bIds);
            if (!empty($bIds)) {
                $bStr = implode(',', $bIds);
                $userWhere .= " AND EXISTS (SELECT 1 FROM branch_has_user bhu WHERE bhu.user_id = u.id AND bhu.branch_id IN ($bStr)) ";
            }
        } elseif ($branchIdsStr && $scope !== 'self') {
            $userWhere .= " AND (EXISTS (SELECT 1 FROM branch_has_user bhu WHERE bhu.user_id = u.id AND bhu.branch_id IN ($branchIdsStr))
                OR EXISTS (SELECT 1 FROM crm_parents cp WHERE cp.owner_id = u.id)
                OR EXISTS (SELECT 1 FROM contracts ct WHERE ct.ec_id = u.id OR ct.creator_id = u.id)) ";
        }

        // Chỉ lấy các nhân viên Sale / Trưởng nhóm hoặc những người có phát sinh data / hợp đồng
        $staffList = u::query("SELECT DISTINCT u.id, u.name AS full_name, u.name, u.hrm_id,
                (SELECT rhu.role_id FROM role_has_user rhu WHERE rhu.user_id = u.id LIMIT 1) AS role_id,
                u.manager_id,
                m.name AS leader_name,
                (SELECT br.name FROM branch_has_user bhu JOIN branches br ON br.id = bhu.branch_id WHERE bhu.user_id = u.id LIMIT 1) AS branch_name,
                (SELECT bhu.branch_id FROM branch_has_user bhu WHERE bhu.user_id = u.id LIMIT 1) AS branch_id
            FROM users AS u
            LEFT JOIN users AS m ON m.id = u.manager_id
            LEFT JOIN role_has_user AS ru ON ru.user_id = u.id
            WHERE $userWhere
              AND (ru.role_id IN (68, 69)
                   OR EXISTS (SELECT 1 FROM crm_parents cp WHERE cp.owner_id = u.id AND cp.status > 0)
                   OR EXISTS (SELECT 1 FROM contracts ct WHERE (ct.ec_id = u.id OR ct.creator_id = u.id) AND ct.status > 0))
            ORDER BY u.id ASC");

        if (empty($staffList)) {
            return response()->json([
                'status' => 1,
                'data' => [
                    'rows' => [],
                    'summary' => [
                        'data_count' => 0,
                        'contract_count' => 0,
                        'conversion_rate' => 0,
                        'provisional_revenue' => 0,
                        'actual_revenue' => 0,
                        'revenue_per_data' => 0,
                        'debt_amount' => 0,
                        'target_amount' => 0,
                        'actual_achieved' => 0,
                        'completion_percent' => 0,
                    ],
                    'period' => ['year' => $targetYear, 'month' => $targetMonth]
                ]
            ]);
        }

        $staffIds = array_map(function ($s) {
            return (int)$s->id;
        }, $staffList);
        $staffIdsStr = implode(',', $staffIds);

        // 2. Tính Lượng Data (crm_parents) theo từng nhân viên
        $parentCond = " p.status > 0 AND p.owner_id IN ($staffIdsStr) ";
        if ($startDate !== '') {
            $parentCond .= " AND p.`$dateType` >= '" . addslashes($startDate) . " 00:00:00' ";
        }
        if ($endDate !== '') {
            $parentCond .= " AND p.`$dateType` <= '" . addslashes($endDate) . " 23:59:59' ";
        }
        if (!empty($branchId)) {
            $bIds = is_array($branchId) ? array_filter(array_map('intval', $branchId)) : [(int)$branchId];
            if (!empty($bIds)) {
                $bStr = implode(',', $bIds);
                $parentCond .= " AND (p.branch_id IN ($bStr) OR EXISTS (SELECT 1 FROM branch_has_user bhu WHERE bhu.user_id = p.owner_id AND bhu.branch_id IN ($bStr))) ";
            }
        }
        if ($pacing !== '') {
            $parentCond .= " AND p.is_lock = " . (int)$pacing . " ";
        }
        if ($crmStatus !== '') {
            $parentCond .= " AND p.status = " . (int)$crmStatus . " ";
        }
        if (!empty($sourceIds)) {
            $parentCond .= " AND p.source_id IN (" . implode(',', $sourceIds) . ") ";
        }
        if ($sourceDetailId > 0) {
            $parentCond .= " AND p.source_detail_id = $sourceDetailId ";
        }
        if ($creatorId > 0) {
            $parentCond .= " AND p.creator_id = $creatorId ";
        }
        if ($firstOwnerId > 0) {
            $parentCond .= " AND (COALESCE((SELECT cpl.creator_id FROM crm_parent_logs cpl WHERE cpl.parent_id = p.id ORDER BY cpl.id ASC LIMIT 1), p.creator_id, p.owner_id) = $firstOwnerId) ";
        }
        if ($trialStatus === '1') {
            $parentCond .= " AND (p.status = 8 OR EXISTS (SELECT 1 FROM crm_students cs LEFT JOIN contracts ct ON ct.student_id = cs.lms_id WHERE cs.parent_id = p.id AND (cs.checkin_at IS NOT NULL OR ct.type = 0))) ";
        } elseif ($trialStatus === '0') {
            $parentCond .= " AND p.status != 8 AND NOT EXISTS (SELECT 1 FROM crm_students cs LEFT JOIN contracts ct ON ct.student_id = cs.lms_id WHERE cs.parent_id = p.id AND (cs.checkin_at IS NOT NULL OR ct.type = 0)) ";
        }

        $parentRows = u::query("SELECT p.owner_id, COUNT(DISTINCT p.id) AS data_count
            FROM crm_parents AS p
            WHERE $parentCond
            GROUP BY p.owner_id");
        $dataByStaff = [];
        foreach ($parentRows as $pr) {
            $dataByStaff[(int)$pr->owner_id] = (int)$pr->data_count;
        }

        // 3. Tính Hợp đồng & Doanh số (contracts) theo từng nhân viên
        // Liên kết contract -> owner: COALESCE(NULLIF(c.ec_id, 0), NULLIF(tsu.ec_id, 0), NULLIF(cp.owner_id, 0), NULLIF(c.creator_id, 0))
        $ownerExpr = "COALESCE(NULLIF(c.ec_id, 0), NULLIF(tsu.ec_id, 0), NULLIF(cp.owner_id, 0), NULLIF(c.creator_id, 0))";
        $sourceExpr = "COALESCE(NULLIF(cp.source_id, 0), NULLIF(s.source_id, 0), 0)";
        $sourceDetailExpr = "COALESCE(NULLIF(cp.source_detail_id, 0), NULLIF(s.source_detail_id, 0), 0)";

        $contractCond = " c.status > 0 AND c.status != 7 AND ($ownerExpr) IN ($staffIdsStr) ";
        if ($startDate !== '') {
            $contractCond .= " AND c.created_at >= '" . addslashes($startDate) . " 00:00:00' ";
        }
        if ($endDate !== '') {
            $contractCond .= " AND c.created_at <= '" . addslashes($endDate) . " 23:59:59' ";
        }
        if (!empty($branchId)) {
            $bIds = is_array($branchId) ? array_filter(array_map('intval', $branchId)) : [(int)$branchId];
            if (!empty($bIds)) {
                $contractCond .= " AND c.branch_id IN (" . implode(',', $bIds) . ") ";
            }
        }
        if ($contractType === 'new') {
            $contractCond .= " AND c.type > 0 AND (c.count_recharge IS NULL OR c.count_recharge <= 0) ";
        } elseif ($contractType === 'renew') {
            $contractCond .= " AND c.type > 0 AND c.count_recharge > 0 ";
        } elseif ($contractType === 'trial') {
            $contractCond .= " AND c.type = 0 ";
        }
        if (!empty($sourceIds)) {
            $contractCond .= " AND ($sourceExpr) IN (" . implode(',', $sourceIds) . ") ";
        }
        if ($sourceDetailId > 0) {
            $contractCond .= " AND ($sourceDetailExpr) = $sourceDetailId ";
        }
        if ($creatorId > 0) {
            $contractCond .= " AND (c.creator_id = $creatorId OR cp.creator_id = $creatorId) ";
        }
        if ($pacing !== '') {
            $contractCond .= " AND cp.is_lock = " . (int)$pacing . " ";
        }
        if ($crmStatus !== '') {
            $contractCond .= " AND cp.status = " . (int)$crmStatus . " ";
        }
        if ($trialStatus === '1') {
            $contractCond .= " AND (c.type = 0 OR cs.checkin_at IS NOT NULL) ";
        } elseif ($trialStatus === '0') {
            $contractCond .= " AND c.type > 0 AND cs.checkin_at IS NULL ";
        }

        // Lọc theo ngày hóa đơn (charge_date trên bảng payments) nếu người dùng nhập Từ ngày hóa đơn / Đến ngày hóa đơn
        $actualRevenueExpr = "c.total_charged";
        if ($invoiceStartDate !== '' || $invoiceEndDate !== '') {
            $payCond = " p_inv.contract_id = c.id ";
            if ($invoiceStartDate !== '') {
                $payCond .= " AND p_inv.charge_date >= '" . addslashes($invoiceStartDate) . "' ";
            }
            if ($invoiceEndDate !== '') {
                $payCond .= " AND p_inv.charge_date <= '" . addslashes($invoiceEndDate) . "' ";
            }
            $contractCond .= " AND EXISTS (SELECT 1 FROM payments p_inv WHERE $payCond) ";
            $actualRevenueExpr = "COALESCE((SELECT SUM(p_inv.amount) FROM payments p_inv WHERE $payCond), 0)";
        }

        $contractRows = u::query("SELECT
                staff_id,
                COUNT(contract_id) AS contract_count,
                SUM(must_charge) AS provisional_revenue,
                SUM(actual_charged) AS actual_revenue,
                SUM(debt_amount) AS debt_amount
            FROM (
                SELECT
                    c.id AS contract_id,
                    ($ownerExpr) AS staff_id,
                    MAX(COALESCE(c.must_charge, 0)) AS must_charge,
                    MAX(COALESCE($actualRevenueExpr, 0)) AS actual_charged,
                    MAX(COALESCE(c.debt_amount, 0)) AS debt_amount
                FROM contracts AS c
                LEFT JOIN students AS s ON s.id = c.student_id
                LEFT JOIN term_student_user AS tsu ON tsu.student_id = c.student_id
                LEFT JOIN crm_students AS cs ON cs.lms_id = c.student_id
                LEFT JOIN crm_parents AS cp ON cp.id = cs.parent_id
                WHERE $contractCond
                GROUP BY c.id, staff_id
            ) AS sub
            GROUP BY staff_id");

        $contractsByStaff = [];
        foreach ($contractRows as $cr) {
            $contractsByStaff[(int)$cr->staff_id] = $cr;
        }

        // 4. Lấy Chỉ tiêu KPI (sale_targets) của tháng đang xem
        $targetRows = u::query("SELECT user_id, SUM(target_amount) AS target_amount
            FROM sale_targets
            WHERE year = $targetYear AND month = $targetMonth AND user_id IN ($staffIdsStr)
            GROUP BY user_id");
        $targetsByStaff = [];
        foreach ($targetRows as $tr) {
            $targetsByStaff[(int)$tr->user_id] = (int)$tr->target_amount;
        }

        // 5. Tổng hợp dữ liệu từng dòng & dòng Tổng
        $rows = [];
        $sumData = 0;
        $sumContracts = 0;
        $sumProvisional = 0;
        $sumActual = 0;
        $sumDebt = 0;
        $sumTarget = 0;

        foreach ($staffList as $idx => $st) {
            $uid = (int)$st->id;
            $dCount = isset($dataByStaff[$uid]) ? (int)$dataByStaff[$uid] : 0;
            $cObj = isset($contractsByStaff[$uid]) ? $contractsByStaff[$uid] : null;
            $cCount = $cObj ? (int)$cObj->contract_count : 0;
            $provRev = $cObj ? (float)$cObj->provisional_revenue : 0;
            $actRev = $cObj ? (float)$cObj->actual_revenue : 0;
            $debtAmt = $cObj ? (float)$cObj->debt_amount : 0;
            $targetAmt = isset($targetsByStaff[$uid]) ? (float)$targetsByStaff[$uid] : 0;

            $convRate = $dCount > 0 ? round(($cCount / $dCount) * 100, 2) : ($cCount > 0 ? 100.0 : 0.0);
            $revPerData = $dCount > 0 ? round($actRev / $dCount) : 0;
            $completionPct = $targetAmt > 0 ? round(($actRev / $targetAmt) * 100, 2) : 0.0;

            $teamName = '';
            if (!empty($st->leader_name)) {
                $teamName = 'Team ' . $st->leader_name;
            } elseif ((int)$st->role_id === 69) {
                $teamName = 'Team ' . $st->full_name;
            }

            $rows[] = [
                'stt' => $idx + 1,
                'user_id' => $uid,
                'full_name' => $st->full_name,
                'hrm_id' => $st->hrm_id,
                'role_id' => (int)$st->role_id,
                'team_name' => $teamName,
                'branch_id' => (int)$st->branch_id,
                'branch_name' => $st->branch_name ?: '-',
                'data_count' => $dCount,
                'contract_count' => $cCount,
                'conversion_rate' => $convRate,
                'provisional_revenue' => $provRev,
                'actual_revenue' => $actRev,
                'revenue_per_data' => $revPerData,
                'debt_amount' => $debtAmt,
                'target_amount' => $targetAmt,
                'actual_achieved' => $actRev,
                'completion_percent' => $completionPct,
            ];

            $sumData += $dCount;
            $sumContracts += $cCount;
            $sumProvisional += $provRev;
            $sumActual += $actRev;
            $sumDebt += $debtAmt;
            $sumTarget += $targetAmt;
        }

        // Sắp xếp ưu tiên người có doanh số hoặc lượng data lên đầu giống giao diện mẫu
        usort($rows, function ($a, $b) {
            if ($b['actual_revenue'] != $a['actual_revenue']) {
                return $b['actual_revenue'] <=> $a['actual_revenue'];
            }
            if ($b['provisional_revenue'] != $a['provisional_revenue']) {
                return $b['provisional_revenue'] <=> $a['provisional_revenue'];
            }
            if ($b['data_count'] != $a['data_count']) {
                return $b['data_count'] <=> $a['data_count'];
            }
            return strcmp($a['full_name'], $b['full_name']);
        });
        foreach ($rows as $i => &$r) {
            $r['stt'] = $i + 1;
        }
        unset($r);

        $totalConvRate = $sumData > 0 ? round(($sumContracts / $sumData) * 100, 2) : ($sumContracts > 0 ? 100.0 : 0.0);
        $totalRevPerData = $sumData > 0 ? round($sumActual / $sumData) : 0;
        $totalCompletionPct = $sumTarget > 0 ? round(($sumActual / $sumTarget) * 100, 2) : 0.0;

        return response()->json([
            'status' => 1,
            'data' => [
                'rows' => $rows,
                'summary' => [
                    'data_count' => $sumData,
                    'contract_count' => $sumContracts,
                    'conversion_rate' => $totalConvRate,
                    'provisional_revenue' => $sumProvisional,
                    'actual_revenue' => $sumActual,
                    'revenue_per_data' => $totalRevPerData,
                    'debt_amount' => $sumDebt,
                    'target_amount' => $sumTarget,
                    'actual_achieved' => $sumActual,
                    'completion_percent' => $totalCompletionPct,
                ],
                'period' => [
                    'year' => $targetYear,
                    'month' => $targetMonth,
                ]
            ]
        ]);
    }

    /**
     * Báo cáo Kinh doanh (Tab 3 - Row 9):
     * - Bộ lọc theo từng cá nhân Sale, từng Team, từng Cơ sở và Tổng của Trung tâm
     * - Tách Nguồn (sources) và Kênh nguồn ngách (source_detail)
     * - Các cột doanh thu đến từ từng Nguồn khách hàng (MKT, B2B, KH giới thiệu...),
     *   Thực thu, Tạm tính, Công nợ, KPI tháng và Doanh thu thực đạt tại thời điểm check,
     *   % chuyển đổi từ Data sang Hợp đồng (hiển thị cả % và số cụ thể)
     * - Bảng Doanh thu theo từng Sản phẩm (Ucrea, Bright I.G, Tiếng Anh, Tiền Tiểu Học...)
     */
    public function businessReport(Request $request)
    {
        $user = Auth::user();
        $scope = $user->getReportScope();
        $branchIdsStr = $user->getBranchesHasUser();

        $groupBy = trim((string)$request->input('group_by', 'sale')); // sale | team | branch | center
        if (!in_array($groupBy, ['sale', 'team', 'branch', 'center'])) {
            $groupBy = 'sale';
        }
        // Nếu là Sale cá nhân thì luôn hiển thị theo cá nhân sale đó
        if ($scope === 'self') {
            $groupBy = 'sale';
        }

        $startDate = trim((string)$request->input('start_date', ''));
        $endDate = trim((string)$request->input('end_date', ''));
        $branchId = (int)$request->input('branch_id', 0);
        $teamLeaderId = (int)$request->input('team_leader_id', 0);
        $ownerId = (int)$request->input('owner_id', 0);
        $sourceId = (int)$request->input('source_id', 0);
        $sourceDetailId = (int)$request->input('source_detail_id', 0);
        $productId = (int)$request->input('product_id', 0);

        $targetDateRef = $startDate ?: ($endDate ?: date('Y-m-d'));
        $targetYear = (int)date('Y', strtotime($targetDateRef));
        $targetMonth = (int)date('n', strtotime($targetDateRef));

        $sources = u::query("SELECT id, name FROM sources WHERE status = 1 ORDER BY id ASC");
        $products = u::query("SELECT id, name FROM products WHERE status = 1 ORDER BY id ASC");

        // Phạm vi nhân sự cho phép
        $allowedStaffIds = null;
        if ($scope === 'self') {
            $allowedStaffIds = (string)(int)$user->id;
        } elseif ($scope === 'team') {
            $allowedStaffIds = $user->getStaffHasUser();
        }
        if ($teamLeaderId > 0) {
            $teamIds = $user->getStaffHasUser($teamLeaderId);
            if ($allowedStaffIds !== null) {
                $inter = array_intersect(explode(',', $allowedStaffIds), explode(',', $teamIds));
                $allowedStaffIds = !empty($inter) ? implode(',', $inter) : '0';
            } else {
                $allowedStaffIds = $teamIds;
            }
        }
        if ($ownerId > 0) {
            if ($allowedStaffIds !== null) {
                $inter = array_intersect(explode(',', $allowedStaffIds), [(string)$ownerId]);
                $allowedStaffIds = !empty($inter) ? implode(',', $inter) : '0';
            } else {
                $allowedStaffIds = (string)$ownerId;
            }
        }

        // Lấy danh sách user và thông tin Team / Cơ sở của từng user
        $usersRaw = u::query("SELECT u.id, u.name AS full_name, u.hrm_id,
                (SELECT rhu.role_id FROM role_has_user rhu WHERE rhu.user_id = u.id LIMIT 1) AS role_id,
                u.manager_id,
                m.name AS manager_name,
                (SELECT bhu.branch_id FROM branch_has_user bhu WHERE bhu.user_id = u.id LIMIT 1) AS branch_id,
                (SELECT br.name FROM branch_has_user bhu JOIN branches br ON br.id = bhu.branch_id WHERE bhu.user_id = u.id LIMIT 1) AS branch_name
            FROM users AS u
            LEFT JOIN users AS m ON m.id = u.manager_id
            WHERE u.status = 1");

        $userMap = [];
        foreach ($usersRaw as $ur) {
            $uid = (int)$ur->id;
            $leaderId = (int)$ur->role_id === 69 ? $uid : (int)$ur->manager_id;
            $teamName = '';
            if (!empty($ur->manager_name)) {
                $teamName = 'Team ' . $ur->manager_name;
            } elseif ((int)$ur->role_id === 69) {
                $teamName = 'Team ' . $ur->full_name;
            } else {
                $teamName = 'Chưa phân Team';
            }
            $userMap[$uid] = [
                'id' => $uid,
                'full_name' => $ur->full_name,
                'hrm_id' => $ur->hrm_id,
                'role_id' => (int)$ur->role_id,
                'leader_id' => $leaderId,
                'team_name' => $teamName,
                'branch_id' => (int)$ur->branch_id ?: 1,
                'branch_name' => $ur->branch_name ?: 'CS1 — Thăng Long Victory',
            ];
        }

        $branchesRaw = u::query("SELECT id, name FROM branches WHERE status = 1 ORDER BY id ASC");
        $branchMap = [];
        foreach ($branchesRaw as $br) {
            $branchMap[(int)$br->id] = $br->name;
        }

        // Điều kiện lọc crm_parents (Lượng Data)
        $pCond = " p.status > 0 ";
        if ($allowedStaffIds !== null) {
            $pCond .= " AND p.owner_id IN ($allowedStaffIds) ";
        }
        if ($startDate !== '') {
            $pCond .= " AND p.created_at >= '" . addslashes($startDate) . " 00:00:00' ";
        }
        if ($endDate !== '') {
            $pCond .= " AND p.created_at <= '" . addslashes($endDate) . " 23:59:59' ";
        }
        if ($branchId > 0) {
            $pCond .= " AND (p.branch_id = $branchId OR EXISTS (SELECT 1 FROM branch_has_user bhu WHERE bhu.user_id = p.owner_id AND bhu.branch_id = $branchId)) ";
        } elseif ($branchIdsStr && $scope !== 'self') {
            $pCond .= " AND (p.branch_id IN ($branchIdsStr) OR p.branch_id IS NULL OR EXISTS (SELECT 1 FROM branch_has_user bhu WHERE bhu.user_id = p.owner_id AND bhu.branch_id IN ($branchIdsStr))) ";
        }
        if ($sourceId > 0) {
            $pCond .= " AND p.source_id = $sourceId ";
        }
        if ($sourceDetailId > 0) {
            $pCond .= " AND p.source_detail_id = $sourceDetailId ";
        }
        if ($productId > 0) {
            $pCond .= " AND EXISTS (SELECT 1 FROM crm_students cs LEFT JOIN contracts ct ON ct.student_id = cs.lms_id WHERE cs.parent_id = p.id AND (cs.type_product = $productId OR ct.product_id = $productId)) ";
        }

        $parentsData = u::query("SELECT p.id, p.owner_id, p.branch_id, p.source_id, p.source_detail_id
            FROM crm_parents AS p
            WHERE $pCond");

        // Điều kiện lọc Hợp đồng (contracts)
        $ownerExpr = "COALESCE(NULLIF(c.ec_id, 0), NULLIF(tsu.ec_id, 0), NULLIF(cp.owner_id, 0), NULLIF(c.creator_id, 0))";
        $sourceExpr = "COALESCE(NULLIF(cp.source_id, 0), NULLIF(s.source_id, 0), 0)";
        $sourceDetailExpr = "COALESCE(NULLIF(cp.source_detail_id, 0), NULLIF(s.source_detail_id, 0), 0)";

        $cCond = " c.status > 0 AND c.status != 7 ";
        if ($allowedStaffIds !== null) {
            $cCond .= " AND ($ownerExpr) IN ($allowedStaffIds) ";
        }
        if ($startDate !== '') {
            $cCond .= " AND c.created_at >= '" . addslashes($startDate) . " 00:00:00' ";
        }
        if ($endDate !== '') {
            $cCond .= " AND c.created_at <= '" . addslashes($endDate) . " 23:59:59' ";
        }
        if ($branchId > 0) {
            $cCond .= " AND c.branch_id = $branchId ";
        } elseif ($branchIdsStr && $scope !== 'self') {
            $cCond .= " AND c.branch_id IN ($branchIdsStr) ";
        }
        if ($sourceId > 0) {
            $cCond .= " AND ($sourceExpr) = $sourceId ";
        }
        if ($sourceDetailId > 0) {
            $cCond .= " AND ($sourceDetailExpr) = $sourceDetailId ";
        }
        if ($productId > 0) {
            $cCond .= " AND c.product_id = $productId ";
        }

        $contractsData = u::query("SELECT
                c.id AS contract_id,
                MAX($ownerExpr) AS staff_id,
                MAX(c.branch_id) AS branch_id,
                MAX(c.product_id) AS product_id,
                MAX($sourceExpr) AS source_id,
                MAX($sourceDetailExpr) AS source_detail_id,
                MAX(COALESCE(c.must_charge, 0)) AS must_charge,
                MAX(COALESCE(c.total_charged, 0)) AS total_charged,
                MAX(COALESCE(c.debt_amount, 0)) AS debt_amount
            FROM contracts AS c
            LEFT JOIN students AS s ON s.id = c.student_id
            LEFT JOIN term_student_user AS tsu ON tsu.student_id = c.student_id
            LEFT JOIN crm_students AS cs ON cs.lms_id = c.student_id
            LEFT JOIN crm_parents AS cp ON cp.id = cs.parent_id
            WHERE $cCond
            GROUP BY c.id");

        // Lấy KPI tháng (sale_targets)
        $targetCond = " year = $targetYear AND month = $targetMonth ";
        if ($allowedStaffIds !== null) {
            $targetCond .= " AND user_id IN ($allowedStaffIds) ";
        }
        $targetsRaw = u::query("SELECT user_id, branch_id, target_amount FROM sale_targets WHERE $targetCond");

        // Hàm ánh xạ 1 bản ghi (staff_id, branch_id) sang group_key & group_info theo $groupBy
        $resolveGroup = function ($staffId, $recBranchId) use ($groupBy, $userMap, $branchMap) {
            $uInfo = isset($userMap[$staffId]) ? $userMap[$staffId] : null;
            $bId = $recBranchId ?: ($uInfo ? $uInfo['branch_id'] : 1);
            $bName = isset($branchMap[$bId]) ? $branchMap[$bId] : ($uInfo ? $uInfo['branch_name'] : 'Chưa xác định');

            if ($groupBy === 'sale') {
                $key = 'sale_' . $staffId;
                $name = $uInfo ? $uInfo['full_name'] : ('NV #' . $staffId);
                $sub = $uInfo ? ($uInfo['team_name'] . ' • ' . $uInfo['branch_name']) : $bName;
                return ['key' => $key, 'id' => $staffId, 'name' => $name, 'sub_label' => $sub];
            } elseif ($groupBy === 'team') {
                $leaderId = $uInfo ? $uInfo['leader_id'] : 0;
                $teamName = $uInfo ? $uInfo['team_name'] : 'Chưa phân Team';
                $key = 'team_' . $leaderId;
                return ['key' => $key, 'id' => $leaderId, 'name' => $teamName, 'sub_label' => $bName];
            } elseif ($groupBy === 'branch') {
                $key = 'branch_' . $bId;
                return ['key' => $key, 'id' => $bId, 'name' => $bName, 'sub_label' => 'Cơ sở đào tạo'];
            } else {
                return ['key' => 'center_all', 'id' => 0, 'name' => 'Tổng toàn Trung tâm', 'sub_label' => 'Tất cả cơ sở'];
            }
        };

        $groups = [];
        $ensureGroup = function ($gInfo) use (&$groups, $sources) {
            $k = $gInfo['key'];
            if (!isset($groups[$k])) {
                $srcActual = [];
                $srcProv = [];
                $srcContracts = [];
                foreach ($sources as $src) {
                    $srcActual[(int)$src->id] = 0;
                    $srcProv[(int)$src->id] = 0;
                    $srcContracts[(int)$src->id] = 0;
                }
                $groups[$k] = [
                    'group_key' => $k,
                    'group_id' => $gInfo['id'],
                    'group_name' => $gInfo['name'],
                    'sub_label' => $gInfo['sub_label'],
                    'data_count' => 0,
                    'contract_count' => 0,
                    'conversion_rate' => 0,
                    'provisional_revenue' => 0,
                    'actual_revenue' => 0,
                    'debt_amount' => 0,
                    'target_amount' => 0,
                    'completion_percent' => 0,
                    'source_revenues' => $srcActual,
                    'source_provisional' => $srcProv,
                    'source_contracts' => $srcContracts,
                ];
            }
        };

        // Khởi tạo sẵn các nhóm mặc định để bảng không bị trống khi lọc
        if ($groupBy === 'branch') {
            foreach ($branchMap as $bId => $bName) {
                if ($branchId > 0 && $branchId !== $bId) continue;
                $ensureGroup(['key' => 'branch_' . $bId, 'id' => $bId, 'name' => $bName, 'sub_label' => 'Cơ sở đào tạo']);
            }
        } elseif ($groupBy === 'center') {
            $ensureGroup(['key' => 'center_all', 'id' => 0, 'name' => 'Tổng toàn Trung tâm', 'sub_label' => 'Tất cả cơ sở']);
        } elseif ($groupBy === 'team') {
            foreach ($userMap as $uid => $uInfo) {
                if ($uInfo['role_id'] === 69) {
                    if ($teamLeaderId > 0 && $teamLeaderId !== $uid) continue;
                    if ($branchId > 0 && $uInfo['branch_id'] !== $branchId) continue;
                    $ensureGroup(['key' => 'team_' . $uid, 'id' => $uid, 'name' => 'Team ' . $uInfo['full_name'], 'sub_label' => $uInfo['branch_name']]);
                }
            }
        } elseif ($groupBy === 'sale') {
            foreach ($userMap as $uid => $uInfo) {
                if (!in_array($uInfo['role_id'], [68, 69])) continue;
                if ($allowedStaffIds !== null && !in_array((string)$uid, explode(',', $allowedStaffIds))) continue;
                if ($branchId > 0 && $uInfo['branch_id'] !== $branchId) continue;
                $ensureGroup($resolveGroup($uid, $uInfo['branch_id']));
            }
        }

        // Cộng dồn Data (crm_parents)
        foreach ($parentsData as $p) {
            $staffId = (int)$p->owner_id;
            $gInfo = $resolveGroup($staffId, (int)$p->branch_id);
            $ensureGroup($gInfo);
            $groups[$gInfo['key']]['data_count']++;
        }

        // Cộng dồn Hợp đồng & Doanh thu theo Nguồn + Sản phẩm
        $productStats = [];
        foreach ($products as $prd) {
            $srcRev = [];
            foreach ($sources as $src) {
                $srcRev[(int)$src->id] = 0;
            }
            $productStats[(int)$prd->id] = [
                'product_id' => (int)$prd->id,
                'product_name' => $prd->name,
                'contract_count' => 0,
                'provisional_revenue' => 0,
                'actual_revenue' => 0,
                'debt_amount' => 0,
                'contract_share_percent' => 0,
                'revenue_share_percent' => 0,
                'source_revenues' => $srcRev,
            ];
        }

        foreach ($contractsData as $c) {
            $staffId = (int)$c->staff_id;
            $gInfo = $resolveGroup($staffId, (int)$c->branch_id);
            $ensureGroup($gInfo);
            $k = $gInfo['key'];

            $prov = (float)$c->must_charge;
            $act = (float)$c->total_charged;
            $debt = (float)$c->debt_amount;
            $srcId = (int)$c->source_id;
            $prdId = (int)$c->product_id;

            $groups[$k]['contract_count']++;
            $groups[$k]['provisional_revenue'] += $prov;
            $groups[$k]['actual_revenue'] += $act;
            $groups[$k]['debt_amount'] += $debt;

            if (isset($groups[$k]['source_revenues'][$srcId])) {
                $groups[$k]['source_revenues'][$srcId] += $act;
                $groups[$k]['source_provisional'][$srcId] += $prov;
                $groups[$k]['source_contracts'][$srcId]++;
            }

            if (isset($productStats[$prdId])) {
                $productStats[$prdId]['contract_count']++;
                $productStats[$prdId]['provisional_revenue'] += $prov;
                $productStats[$prdId]['actual_revenue'] += $act;
                $productStats[$prdId]['debt_amount'] += $debt;
                if (isset($productStats[$prdId]['source_revenues'][$srcId])) {
                    $productStats[$prdId]['source_revenues'][$srcId] += $act;
                }
            }
        }

        // Cộng dồn KPI tháng (sale_targets)
        foreach ($targetsRaw as $t) {
            $staffId = (int)$t->user_id;
            if ($branchId > 0) {
                $uBranch = isset($userMap[$staffId]) ? $userMap[$staffId]['branch_id'] : (int)$t->branch_id;
                if ($uBranch !== $branchId) continue;
            }
            $gInfo = $resolveGroup($staffId, (int)$t->branch_id);
            if (isset($groups[$gInfo['key']])) {
                $groups[$gInfo['key']]['target_amount'] += (float)$t->target_amount;
            }
        }

        // Tính tỷ lệ chuyển đổi & hoàn thành KPI cho từng nhóm + tổng hợp dòng Summary
        $rows = array_values($groups);
        $sumData = 0;
        $sumContracts = 0;
        $sumProv = 0;
        $sumAct = 0;
        $sumDebt = 0;
        $sumTarget = 0;
        $sumSourceRev = [];
        $sumSourceProv = [];
        $sumSourceContracts = [];
        foreach ($sources as $src) {
            $sumSourceRev[(int)$src->id] = 0;
            $sumSourceProv[(int)$src->id] = 0;
            $sumSourceContracts[(int)$src->id] = 0;
        }

        foreach ($rows as &$r) {
            $r['conversion_rate'] = $r['data_count'] > 0
                ? round(($r['contract_count'] / $r['data_count']) * 100, 2)
                : ($r['contract_count'] > 0 ? 100.0 : 0.0);
            $r['completion_percent'] = $r['target_amount'] > 0
                ? round(($r['actual_revenue'] / $r['target_amount']) * 100, 2)
                : 0.0;

            $sumData += $r['data_count'];
            $sumContracts += $r['contract_count'];
            $sumProv += $r['provisional_revenue'];
            $sumAct += $r['actual_revenue'];
            $sumDebt += $r['debt_amount'];
            $sumTarget += $r['target_amount'];

            foreach ($sources as $src) {
                $sid = (int)$src->id;
                $sumSourceRev[$sid] += $r['source_revenues'][$sid] ?? 0;
                $sumSourceProv[$sid] += $r['source_provisional'][$sid] ?? 0;
                $sumSourceContracts[$sid] += $r['source_contracts'][$sid] ?? 0;
            }
        }
        unset($r);

        usort($rows, function ($a, $b) {
            if ($b['actual_revenue'] != $a['actual_revenue']) {
                return $b['actual_revenue'] <=> $a['actual_revenue'];
            }
            if ($b['provisional_revenue'] != $a['provisional_revenue']) {
                return $b['provisional_revenue'] <=> $a['provisional_revenue'];
            }
            return $b['data_count'] <=> $a['data_count'];
        });
        foreach ($rows as $idx => &$r) {
            $r['stt'] = $idx + 1;
        }
        unset($r);

        // Tính tỷ trọng sản phẩm
        $productRows = array_values($productStats);
        foreach ($productRows as &$pr) {
            $pr['contract_share_percent'] = $sumContracts > 0 ? round(($pr['contract_count'] / $sumContracts) * 100, 2) : 0.0;
            $pr['revenue_share_percent'] = $sumProv > 0 ? round(($pr['provisional_revenue'] / $sumProv) * 100, 2) : 0.0;
        }
        unset($pr);

        return response()->json([
            'status' => 1,
            'data' => [
                'group_by' => $groupBy,
                'sources' => $sources,
                'rows' => $rows,
                'summary' => [
                    'data_count' => $sumData,
                    'contract_count' => $sumContracts,
                    'conversion_rate' => $sumData > 0 ? round(($sumContracts / $sumData) * 100, 2) : ($sumContracts > 0 ? 100.0 : 0.0),
                    'provisional_revenue' => $sumProv,
                    'actual_revenue' => $sumAct,
                    'debt_amount' => $sumDebt,
                    'target_amount' => $sumTarget,
                    'completion_percent' => $sumTarget > 0 ? round(($sumAct / $sumTarget) * 100, 2) : 0.0,
                    'source_revenues' => $sumSourceRev,
                    'source_provisional' => $sumSourceProv,
                    'source_contracts' => $sumSourceContracts,
                ],
                'product_breakdown' => $productRows,
                'period' => [
                    'year' => $targetYear,
                    'month' => $targetMonth,
                ],
            ]
        ]);
    }

    /**
     * Lưu chỉ tiêu KPI tháng (sale_targets) cho nhân viên Sale / Đội nhóm
     */
    public function saveTargets(Request $request)
    {
        $user = Auth::user();
        $scope = $user->getReportScope();
        if (!in_array($scope, ['all', 'team'])) {
            return response()->json([
                'status' => 0,
                'message' => 'Bạn không có quyền thiết lập chỉ tiêu KPI.'
            ], 403);
        }

        $year = (int)$request->input('year', date('Y'));
        $month = (int)$request->input('month', date('n'));
        $targets = $request->input('targets', []);
        if (!is_array($targets)) {
            return response()->json(['status' => 0, 'message' => 'Dữ liệu không hợp lệ.']);
        }

        $allowedStaff = $scope === 'team' ? explode(',', $user->getStaffHasUser()) : null;
        $now = date('Y-m-d H:i:s');

        foreach ($targets as $item) {
            $targetUserId = (int)data_get($item, 'user_id', 0);
            $targetAmount = (int)data_get($item, 'target_amount', 0);
            if ($targetUserId <= 0) continue;
            if ($allowedStaff !== null && !in_array((string)$targetUserId, $allowedStaff)) {
                continue;
            }

            $uBranch = u::first("SELECT branch_id FROM branch_has_user WHERE user_id = $targetUserId LIMIT 1");
            $branchId = $uBranch ? (int)$uBranch->branch_id : null;

            $existing = u::first("SELECT id FROM sale_targets WHERE user_id = $targetUserId AND year = $year AND month = $month LIMIT 1");
            if ($existing) {
                u::updateSimpleRow([
                    'target_amount' => $targetAmount,
                    'branch_id' => $branchId,
                    'updated_by' => $user->id,
                    'updated_at' => $now,
                ], ['id' => $existing->id], 'sale_targets');
            } else {
                u::insertSimpleRow([
                    'user_id' => $targetUserId,
                    'branch_id' => $branchId,
                    'year' => $year,
                    'month' => $month,
                    'target_amount' => $targetAmount,
                    'updated_by' => $user->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ], 'sale_targets');
            }
        }

        return response()->json([
            'status' => 1,
            'message' => "Đã lưu chỉ tiêu KPI tháng $month/$year thành công."
        ]);
    }
}
