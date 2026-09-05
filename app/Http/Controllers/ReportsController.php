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
        if ($month && $month != "_") {
            $report_month = (int) $month > 9 ? date("Y") . '-' . (int) $month : date("Y") . '-0' . (int) $month;
        } else {
            $report_month = date('Y-m', time() - 7 * 3600);
        }
        $start_date = date('Y-m-01', strtotime($report_month . '-01'));
        if (date('Y-m', strtotime($report_month . '-01')) == date('Y-m')) {
            $end_date = date('Y-m-d');
        } else {
            $end_date = date('Y-m-t', strtotime($report_month . '-01'));
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
            c.summary_sessions, c.done_sessions,c.enrolment_start_date, c.last_done_sessions,
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

    public function addItems($list)
    {
        if ($list) {
            $created_at = date('Y-m-d H:i:s');
            $query = "INSERT INTO report_full_fee_active (student_id,contract_id, class_id, product_id, cm_id, report_month, branch_id, created_at, creator_id,end_date,`start_date`,done_sessions,summary_sessions,last_done_sessions, `type`, tuition_fee_id, init_tuition_fee_id) VALUES ";
            if (count($list) > 5000) {
                for ($i = 0; $i < 5000; $i++) {
                    $item = $list[$i];
                    $query .= "('$item->student_id', '$item->contract_id', '$item->class_id', '$item->product_id', '$item->cm_id', '$item->report_month', '$item->branch_id', '$created_at', 99999,'$item->enrolment_last_date','$item->enrolment_start_date','$item->done_sessions','$item->summary_sessions','$item->last_done_sessions','$item->type_fee', '$item->tuition_fee_id', '$item->init_tuition_fee_id'),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
                self::addItems(array_slice($list, 5000));
            } else {
                foreach ($list as $item) {
                    $query .= "('$item->student_id', '$item->contract_id', '$item->class_id', '$item->product_id', '$item->cm_id', '$item->report_month', '$item->branch_id', '$created_at', 99999,'$item->enrolment_last_date','$item->enrolment_start_date','$item->done_sessions','$item->summary_sessions','$item->last_done_sessions','$item->type_fee', '$item->tuition_fee_id', '$item->init_tuition_fee_id'),";
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

        $pagination = (object) $request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);
        $limitation = $limit > 0 ? " LIMIT $offset, $limit" : "";
        $cond = " r.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";

        if (!empty($branch_id)) {
            $cond .= " AND r.branch_id IN (" . implode(",", $branch_id) . ")";
        }

        if ($keyword !== '') {
            $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%' OR s.gud_mobile1 LIKE '%$keyword%') ";
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

    public function updateCompletedDate()
    {
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
                $resp = (object) [
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
                WHERE s.status>0 AND ((DATE_FORMAT(c.renewed_date, '%Y-%m') >= '$renewed_month' OR DATE_FORMAT(c.success_renewed_date, '%Y-%m') >= '$renewed_month'))
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
                    $renew_amount = (int) $item->renew_amount;
                    $query .= "('$item->student_id', '$item->contract_id', '$item->branch_id', '$item->product_id', '$item->class_id', '" . (int) $item->init_renew_tuition_fee_id . "', '" . (int) $item->init_tuition_fee_id . "', '" . (int) $item->ec_id . "', '" . (int) $item->cm_id . "', '" . (int) $renewed_cm_id . "','" . (int) $item->ec_leader_id . "','" . (int) $item->cm_leader_id . "',
                                '" . (int) $item->ceo_branch_id . "', '$renew_amount', '$item->renewed_status', '$item->renewed_month', '$item->renewed_date', '$created_at' ),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
                self::addItems(array_slice($list, 5000));
            } else {
                foreach ($list as $item) {
                    $renewed_cm_id = $item->cm_id;
                    $renew_amount = (int) $item->renew_amount;
                    $query .= "('$item->student_id', '$item->contract_id', '$item->branch_id', '$item->product_id', '$item->class_id','" . (int) $item->init_renew_tuition_fee_id . "', '" . (int) $item->init_tuition_fee_id . "', '" . (int) $item->ec_id . "', '" . (int) $item->cm_id . "', '" . (int) $renewed_cm_id . "','" . (int) $item->ec_leader_id . "','" . (int) $item->cm_leader_id . "',
                                '" . (int) $item->ceo_branch_id . "', '$renew_amount', '$item->renewed_status', '$item->renewed_month', '$item->renewed_date', '$created_at' ),";
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

        $pagination = (object) $request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);
        $limitation = $limit > 0 ? " LIMIT $offset, $limit" : "";
        $cond = " r.`disabled` = 0 AND s.status>0 AND r.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";

        if (!empty($branch_id)) {
            $cond .= " AND r.branch_id IN (" . implode(",", $branch_id) . ")";
        }

        if ($keyword !== '') {
            $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%' OR s.gud_mobile1 LIKE '%$keyword%') ";
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

        $pagination = (object) $request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);
        $limitation = $limit > 0 ? " LIMIT $offset, $limit" : "";
        $cond = " b.status = 1 AND b.id IN (" . Auth::user()->getBranchesHasUser() . ")";

        if (!empty($branch_id)) {
            $cond .= " AND b.id IN (" . implode(",", $branch_id) . ")";
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

        $pagination = (object) $request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);
        $limitation = $limit > 0 ? " LIMIT $offset, $limit" : "";
        $branch_query = Auth::user()->getBranchesHasUser();
        if (!empty($branch_id)) {
            $branch_query = implode(",", $branch_id);
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

    public function reportActiveClasses(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $keyword = isset($request->keyword) ? $request->keyword : '';
        $product_id = isset($request->product_id) ? $request->product_id : '';
        $status = isset($request->status) ? $request->status : '';
        $start_date = isset($request->start_date) ? $request->start_date : '';
        $end_date = isset($request->end_date) ? $request->end_date : '';

        $pagination = (object) $request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);
        $limitation = $limit > 0 ? " LIMIT $offset, $limit" : "";

        $cond = " c.status = 1 AND c.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")"; // Active classes

        if (!empty($branch_id)) {
            $cond .= " AND c.branch_id IN (" . implode(",", $branch_id) . ")";
        }

        if ($keyword !== '') {
            $cond .= " AND (c.cls_name LIKE '%$keyword%') ";
        }

        if ($start_date !== '') {
            $cond .= " AND c.cls_startdate >= '$start_date' ";
        }

        if ($end_date !== '') {
            $cond .= " AND c.cls_startdate <= '$end_date' ";
        }

        if ($product_id !== '') {
            $cond .= " AND c.product_id = '$product_id' ";
        }

        $having = "";
        if ($status !== '') {
            if ($status == 'THIEU') {
                $having = " AND (c.max_students - total_students) > 0 ";
            } elseif ($status == 'THUA') {
                $having = " AND (c.max_students - total_students) < 0 ";
            } elseif ($status == 'DU') {
                $having = " AND (c.max_students - total_students) = 0 ";
            }
        }

        $order_by = " ORDER BY c.cls_startdate DESC ";

        if ($having) {
            $countSql = "SELECT count(*) as total FROM (
                SELECT c.id, c.max_students, (SELECT count(ct.id) FROM contracts ct LEFT JOIN students s ON ct.student_id = s.id WHERE ct.class_id = c.id AND (ct.status != 7 OR c.cls_enddate < CURDATE()) AND s.status > 0) AS total_students
                FROM classes AS c
                WHERE $cond
                HAVING 1=1 $having
             ) as tmp";
            $total = u::first($countSql);
        } else {
            $total = u::first("SELECT count(c.id) AS total FROM classes AS c WHERE $cond");
        }

        $query = "SELECT c.id, c.cls_name, c.max_students, c.cls_startdate, c.class_day, c.is_online,
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
                $order_by $limitation";

        $list = u::query($query);

        if ($list) {
            foreach ($list as $item) {
                $item->total_students = (int) $item->total_students;
                $item->max_students = (int) $item->max_students;
                $diff = $item->max_students - $item->total_students;
                if ($diff > 0) {
                    $item->status_text = 'THIẾU';
                    $item->status_class = 'text-warning';
                } elseif ($diff < 0) {
                    $item->status_text = 'THỪA';
                    $item->status_class = 'text-danger';
                } else {
                    $item->status_text = 'ĐỦ';
                    $item->status_class = 'text-success';
                }

                $days = [];
                if ($item->class_day) {
                    $days_arr = explode(',', $item->class_day);
                    foreach ($days_arr as $d) {
                        $days[] = "T$d";
                    }
                }
                $item->schedule_text = implode('+', $days);
                if ($item->start_time && $item->end_time) {
                    $item->schedule_text .= " (" . substr($item->start_time, 0, 5) . "-" . substr($item->end_time, 0, 5) . ")";
                }

                $item->cls_startdate = date('d/m/Y', strtotime($item->cls_startdate));
                $item->is_online_text = $item->is_online == 1 ? 'Online' : 'Offline';
            }
        }

        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function report13(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $keyword = isset($request->keyword) ? $request->keyword : '';
        $start_date = isset($request->start_date) ? $request->start_date : '';
        $end_date = isset($request->end_date) ? $request->end_date : '';

        $pagination = (object) $request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);
        $limitation = $limit > 0 ? " LIMIT $offset, $limit" : "";

        $cond = " sks.status=1 AND c.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";

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

        $total = u::first("SELECT count(sks.id) AS total 
                           FROM schedule_has_student AS sks
                           JOIN classes AS c ON sks.class_id = c.id
                           JOIN students AS st ON sks.student_id = st.id
                           WHERE $cond");

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
                   $order_by $limitation";

        $list = u::query($query);

        // Calculate total revenue for all filtered records
        $totalRevenueQuery = "SELECT SUM(ct.must_charge / ct.summary_sessions) AS total_revenue
                               FROM schedule_has_student AS sks
                               JOIN classes AS c ON sks.class_id = c.id
                               JOIN students AS st ON sks.student_id = st.id
                               JOIN contracts AS ct ON sks.contract_id = ct.id
                               WHERE $cond";
        $totalRevenueResult = u::first($totalRevenueQuery);
        $totalRevenue = $totalRevenueResult->total_revenue ?? 0;

        $data = u::makingPagination($list, $total->total, $page, $limit);
        $data->total_revenue = $totalRevenue;
        return response()->json($data);
    }

    public function report14(Request $request)
    {
        $start_date = isset($request->start_date) ? $request->start_date : '';
        $end_date = isset($request->end_date) ? $request->end_date : '';

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

        // Calculate totals across all branches
        $totalRevenue = 0;
        $totalStudents = 0;
        $totalSessions = 0;

        if ($list) {
            foreach ($list as $item) {
                $totalRevenue += $item->total_revenue;
                $totalStudents += $item->total_students;
                $totalSessions += $item->total_sessions;
            }
        }

        $data = (object) [
            'list' => $list,
            'total_revenue' => $totalRevenue,
            'total_students' => $totalStudents,
            'total_sessions' => $totalSessions
        ];

        return response()->json($data);
    }

    /**
     * Báo cáo số tiền còn lại theo contracts
     * Chỉ lấy contracts có status NOT IN (0,1,7,8) - tức là status 2,3,4,5,6
     * Số tiền còn lại = (total_charged * left_sessions / summary_sessions)
     */
    public function report15(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $product_id = isset($request->product_id) ? $request->product_id : [];
        $status = isset($request->status) ? $request->status : [];
        $keyword = isset($request->keyword) ? $request->keyword : '';
        $start_date = isset($request->start_date) ? $request->start_date : '';
        $end_date = isset($request->end_date) ? $request->end_date : '';

        $pagination = (object) $request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);
        $limitation = $limit > 0 ? " LIMIT $offset, $limit" : "";

        // Điều kiện cơ bản: chỉ lấy contracts có status NOT IN (0,1,7,8)
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

        // Đếm tổng số contracts
        $total = u::first("SELECT count(c.id) AS total 
            FROM contracts AS c 
            LEFT JOIN students AS s ON s.id = c.student_id 
            WHERE $cond");

        // Query danh sách contracts với thông tin chi tiết
        $query = "SELECT 
                c.id AS contract_id,
                c.code AS contract_code,
                c.status,
                c.type,
                c.summary_sessions,
                c.done_sessions,
                c.left_sessions,
                c.total_charged,
                c.must_charge,
                c.debt_amount,
                c.created_at,
                c.enrolment_start_date,
                c.enrolment_last_date,
                s.id AS student_id,
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
                -- Tính số tiền còn lại
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
            $order_by $limitation";

        $list = u::query($query);

        // Thêm label status cho mỗi contract
        foreach ($list as $k => $row) {
            $list[$k]->label_status = u::geLabelStatusContract($row->status);
            $list[$k]->left_amount = (float) $row->left_amount;
            $list[$k]->total_charged = (float) $row->total_charged;
            $list[$k]->must_charge = (float) $row->must_charge;
            $list[$k]->debt_amount = (float) $row->debt_amount;
        }

        // Tính tổng số tiền còn lại của tất cả contracts (không phân trang)
        $totalAmountQuery = "SELECT 
                SUM(
                    CASE 
                        WHEN c.summary_sessions > 0 THEN 
                            ROUND((c.total_charged * c.left_sessions / c.summary_sessions), 0)
                        ELSE 0
                    END
                ) AS total_left_amount,
                SUM(c.total_charged) AS total_charged_sum,
                SUM(c.must_charge) AS total_must_charge,
                SUM(c.debt_amount) AS total_debt_amount,
                SUM(c.left_sessions) AS total_left_sessions,
                SUM(c.summary_sessions) AS total_summary_sessions
            FROM contracts AS c
                LEFT JOIN students AS s ON s.id = c.student_id
            WHERE $cond";

        $totalAmount = u::first($totalAmountQuery);

        $data = u::makingPagination($list, $total->total, $page, $limit);
        $data->summary = [
            'total_left_amount' => (float) ($totalAmount->total_left_amount ?? 0),
            'total_charged_sum' => (float) ($totalAmount->total_charged_sum ?? 0),
            'total_must_charge' => (float) ($totalAmount->total_must_charge ?? 0),
            'total_debt_amount' => (float) ($totalAmount->total_debt_amount ?? 0),
            'total_left_sessions' => (int) ($totalAmount->total_left_sessions ?? 0),
            'total_summary_sessions' => (int) ($totalAmount->total_summary_sessions ?? 0),
        ];

        return response()->json($data);
    }

    /**
     * Báo cáo 16: Tổng hợp số tiền còn lại theo trung tâm
     */
    public function report16(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $product_id = isset($request->product_id) ? $request->product_id : [];
        $status = isset($request->status) ? $request->status : [];
        $start_date = isset($request->start_date) ? $request->start_date : '';
        $end_date = isset($request->end_date) ? $request->end_date : '';

        // Get all branches accessible by user
        $branchIds = Auth::user()->getBranchesHasUser();

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

        // Branch filter on the main query if provided
        $branchCond = " b.id IN ($branchIds) ";
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

        $total_summary = [
            'total_contracts' => 0,
            'total_must_charge' => 0,
            'total_charged' => 0,
            'total_debt_amount' => 0,
            'total_left_amount' => 0,
        ];

        foreach ($list as $row) {
            $total_summary['total_contracts'] += (int) $row->total_contracts;
            $total_summary['total_must_charge'] += (float) $row->total_must_charge;
            $total_summary['total_charged'] += (float) $row->total_charged;
            $total_summary['total_debt_amount'] += (float) $row->total_debt_amount;
            $total_summary['total_left_amount'] += (float) $row->total_left_amount;
        }

        return response()->json([
            'list' => $list,
            'summary' => $total_summary
        ]);
    }

    public function report17(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $school_year = isset($request->school_year) ? $request->school_year : '';
        $keyword = isset($request->keyword) ? $request->keyword : '';
        $start_date = isset($request->start_date) ? $request->start_date : '';
        $end_date = isset($request->end_date) ? $request->end_date : '';

        $pagination = (object) $request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);
        $limitation = $limit > 0 ? " LIMIT $offset, $limit" : "";

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
            // Lọc theo năm học từ format date, ví dụ lấy trong khoảng năm
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
            GROUP BY a.id
            $order_by $limitation";

        $list = u::query($query);

        $total_combo_fee = 0;
        $total_used_value = 0;
        $total_left_value = 0;
        $total_sessions = 0;
        $total_done_sessions = 0;
        $total_left_sessions = 0;

        foreach ($list as $k => $row) {
            $row->total_sessions = (int) $row->total_sessions;
            $row->done_sessions = (int) $row->done_sessions;
            $row->left_sessions = max(0, $row->total_sessions - $row->done_sessions);
            $row->combo_fee = (float) $row->combo_fee;

            if ($row->total_sessions > 0) {
                $row->used_value = round(($row->combo_fee * $row->done_sessions) / $row->total_sessions, 0);
                $row->completion_rate = round(($row->done_sessions / $row->total_sessions) * 100, 2);
            } else {
                $row->used_value = 0;
                $row->completion_rate = 0;
            }

            $row->left_value = max(0, $row->combo_fee - $row->used_value);

            // Tích luỹ cho summary (chỉ tính trong data paginated, có thể cần query riêng cho tổng hợp số liệu filter)
            $total_combo_fee += $row->combo_fee;
            $total_used_value += $row->used_value;
            $total_left_value += $row->left_value;
            $total_sessions += $row->total_sessions;
            $total_done_sessions += $row->done_sessions;
            $total_left_sessions += $row->left_sessions;
        }

        // Tính tổng kết quả query (bỏ LIMIT)
        $totalQuery = "SELECT COUNT(DISTINCT a.id) as total_count FROM agreements AS a LEFT JOIN students AS s ON s.id = a.student_id WHERE $cond";
        $totalCount = u::first($totalQuery)->total_count;

        // --- Tính Summary tổng hợp không phân trang ---
        $summaryQuery = "SELECT 
                a.id as agreement_id,
                a.must_charge as total_combo_fee_all,
                SUM(c.summary_sessions) as total_sessions_all,
                (SELECT COUNT(shs.id) FROM schedule_has_student shs INNER JOIN contracts c_inner ON c_inner.id = shs.contract_id WHERE c_inner.agreement_id = a.id AND shs.status = 1 " . ($school_year ? " AND YEAR(shs.class_date) = '$school_year'" : "") . ") AS total_done_sessions_all
            FROM agreements AS a
            LEFT JOIN students AS s ON s.id = a.student_id
            LEFT JOIN contracts AS c ON c.agreement_id = a.id AND c.status NOT IN (0,1,7,8)
            WHERE $cond
            GROUP BY a.id";

        $summaryDataSql = u::query($summaryQuery);
        $total_combo_fee_all = 0;
        $total_sessions_all = 0;
        $total_done_sessions_all = 0;
        $total_used_value_all = 0;

        foreach ($summaryDataSql as $sumRow) {
            $total_combo_fee_all += (float) $sumRow->total_combo_fee_all;
            $total_sessions_all += (int) $sumRow->total_sessions_all;
            $done_sessions = (int) $sumRow->total_done_sessions_all;
            $total_done_sessions_all += $done_sessions;

            if ($sumRow->total_sessions_all > 0) {
                $total_used_value_all += round(((float) $sumRow->total_combo_fee_all * $done_sessions) / (int) $sumRow->total_sessions_all, 0);
            }
        }

        $total_left_value_all = max(0, $total_combo_fee_all - $total_used_value_all);
        $total_left_sessions_all = max(0, $total_sessions_all - $total_done_sessions_all);

        $summary = [
            'total_combo_fee' => (float) $total_combo_fee_all,
            'total_used_value' => (float) $total_used_value_all,
            'total_left_value' => (float) $total_left_value_all,
            'total_sessions' => (int) $total_sessions_all,
            'total_done_sessions' => (int) $total_done_sessions_all,
            'total_left_sessions' => (int) $total_left_sessions_all
        ];

        $data = u::makingPagination($list, $totalCount, $page, $limit);
        $data->summary = $summary;

        return response()->json($data);
    }

    public function report18(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $school_year = isset($request->school_year) ? $request->school_year : '';
        $keyword = isset($request->keyword) ? $request->keyword : '';
        $start_date = isset($request->start_date) ? $request->start_date : '';
        $end_date = isset($request->end_date) ? $request->end_date : '';

        $pagination = (object) $request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);
        $limitation = $limit > 0 ? " LIMIT $offset, $limit" : "";

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

        // Lấy danh sách agreements phân trang
        $query = "SELECT 
                a.id AS agreement_id,
                a.must_charge AS combo_fee,
                s.lms_code AS student_code,
                s.name AS student_name,
                (SELECT SUM(c2.summary_sessions) FROM contracts c2 WHERE c2.agreement_id = a.id AND c2.status NOT IN (0,1,7,8)) AS total_combo_sessions
            FROM agreements AS a
                LEFT JOIN students AS s ON s.id = a.student_id
            WHERE $cond
            $order_by $limitation";

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

            // Lấy toàn bộ contracts của các agreements hiện tại
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
                        'is_summary' => false,
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

        $totalQuery = "SELECT COUNT(DISTINCT a.id) as total_count FROM agreements AS a LEFT JOIN students AS s ON s.id = a.student_id WHERE $cond";
        $totalCount = u::first($totalQuery)->total_count;

        $data = u::makingPagination($flat_list, $totalCount, $page, $limit);
        $data->summary = [
            'total_summary_sessions' => $grand_summary_sessions,
            'total_done_sessions' => $grand_done_sessions,
            'total_left_sessions' => $grand_left_sessions,
            'total_left_value' => $grand_left_value,
        ];

        return response()->json($data);
    }

    public function report19(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $start_date = isset($request->start_date) ? $request->start_date : '';
        $end_date = isset($request->end_date) ? $request->end_date : '';

        // Điều kiện lọc agreements
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

        // Tính tổng học viên để tính tỷ trọng %
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
                'sessions_per_student' => $sessions,
                'sessions_total' => $count * $sessions,
                'total_revenue' => (float) $row->total_revenue,
            ];
        }

        return response()->json([
            'list' => $result,
            'total_students' => $total_students,
            'total_revenue' => $total_revenue,
        ]);
    }

    /**
     * Report 20: Báo cáo doanh số chi tiết theo từng khóa học
     * Phân loại Mới / Up Level dựa trên full_fee_date vs buổi học thứ 8
     *
     * Định nghĩa:
     *  - Mới:       agreement có count_recharge = 0
     *               HOẶC (count_recharge > 0 VÀ full_fee_date < ngày buổi học thứ 8 của HS đó trong agreement trước)
     *  - Up Level:  agreement có count_recharge > 0 VÀ full_fee_date >= ngày buổi học thứ 8
     */
    public function report20(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $start_date = isset($request->start_date) ? $request->start_date : '';
        $end_date = isset($request->end_date) ? $request->end_date : '';

        $branchCond = " a.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";
        if (!empty($branch_id)) {
            $branchCond .= " AND a.branch_id IN (" . implode(",", $branch_id) . ")";
        }

        // Lọc theo thời gian full fee
        $dateCond = '';
        if ($start_date) {
            $dateCond .= " AND a.full_fee_date >= '$start_date'";
        }
        if ($end_date) {
            $dateCond .= " AND a.full_fee_date <= '$end_date'";
        }

        // Điều kiện cơ bản: agreement đã thu đủ phí (debt_amount = 0)
        $baseCond = "$branchCond AND a.debt_amount = 0 $dateCond";

        // Subquery lấy ngày buổi học thứ 8 của student trong một agreement cụ thể
        // (tính theo tất cả contracts thuộc agreement đó, sắp xếp theo class_date ASC, lấy row thứ 8)
        $session8Subquery = "
            SELECT shs.class_date
            FROM schedule_has_student shs
            INNER JOIN contracts c_inner ON c_inner.id = shs.contract_id
            WHERE c_inner.agreement_id = prev_a.id
              AND shs.student_id = a.student_id
              AND shs.status IN (1, 2)
            ORDER BY shs.class_date ASC
            LIMIT 1 OFFSET 7
        ";

        // Query chính: group by tuition_fee để đếm Mới và Up Level
        $query = "
            SELECT
                tf.id                   AS tuition_fee_id,
                tf.name                 AS tuition_fee_name,
                tf.number_of_months      AS so_don_sau_tach,
                tf.price                AS gia_khoa_hoc,
                COUNT(a.id)             AS total_agreements,

                -- Mới: count_recharge=0 HOẶC full_fee_date < buổi học thứ 8 (với agreement có count_recharge>0)
                SUM(
                    CASE
                        WHEN a.count_recharge = 0 THEN 1
                        WHEN a.count_recharge > 0 THEN
                            -- Tìm agreement trước đó của student (count_recharge nhỏ hơn)
                            CASE
                                WHEN (
                                    SELECT prev_a.id FROM agreements prev_a
                                    WHERE prev_a.student_id = a.student_id
                                      AND prev_a.tuition_fee_id = a.tuition_fee_id
                                      AND prev_a.count_recharge < a.count_recharge
                                    ORDER BY prev_a.count_recharge DESC LIMIT 1
                                ) IS NOT NULL
                                AND a.full_fee_date < (
                                    SELECT shs2.class_date
                                    FROM schedule_has_student shs2
                                    INNER JOIN contracts c2 ON c2.id = shs2.contract_id
                                    INNER JOIN agreements prev_a2 ON prev_a2.id = c2.agreement_id
                                    WHERE prev_a2.student_id = a.student_id
                                      AND prev_a2.tuition_fee_id = a.tuition_fee_id
                                      AND prev_a2.count_recharge < a.count_recharge
                                      AND shs2.student_id = a.student_id
                                      AND shs2.status IN (1, 2)
                                    ORDER BY prev_a2.count_recharge DESC, shs2.class_date ASC
                                    LIMIT 1 OFFSET 7
                                )
                                THEN 1
                                ELSE 0
                            END
                        ELSE 0
                    END
                ) AS count_new,

                -- Up Level: count_recharge>0 VÀ full_fee_date >= buổi học thứ 8
                SUM(
                    CASE
                        WHEN a.count_recharge > 0 THEN
                            CASE
                                WHEN (
                                    SELECT prev_a.id FROM agreements prev_a
                                    WHERE prev_a.student_id = a.student_id
                                      AND prev_a.tuition_fee_id = a.tuition_fee_id
                                      AND prev_a.count_recharge < a.count_recharge
                                    ORDER BY prev_a.count_recharge DESC LIMIT 1
                                ) IS NOT NULL
                                AND (
                                    a.full_fee_date IS NULL
                                    OR a.full_fee_date >= (
                                        SELECT shs2.class_date
                                        FROM schedule_has_student shs2
                                        INNER JOIN contracts c2 ON c2.id = shs2.contract_id
                                        INNER JOIN agreements prev_a2 ON prev_a2.id = c2.agreement_id
                                        WHERE prev_a2.student_id = a.student_id
                                          AND prev_a2.tuition_fee_id = a.tuition_fee_id
                                          AND prev_a2.count_recharge < a.count_recharge
                                          AND shs2.student_id = a.student_id
                                          AND shs2.status IN (1, 2)
                                        ORDER BY prev_a2.count_recharge DESC, shs2.class_date ASC
                                        LIMIT 1 OFFSET 7
                                    )
                                )
                                THEN 1
                                ELSE 0
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

        // Tính tổng
        $totalAgreements = 0;
        $totalNew = 0;
        $totalUplevel = 0;

        foreach ($list as $row) {
            $totalAgreements += (int) $row->total_agreements;
            $totalNew += (int) $row->count_new;
            $totalUplevel += (int) $row->count_uplevel;
        }

        return response()->json([
            'list' => $list,
            'total_agreements' => $totalAgreements,
            'total_new' => $totalNew,
            'total_uplevel' => $totalUplevel,
        ]);
    }

    /**
     * Report 21: Bảng tổng quan quản lý học sinh năm
     * KPI dạng pivot: Chỉ số | Giá trị (agreements) | Giá trị sau tách (contracts)
     * Chỉ lấy agreements đã thu đủ phí (debt_amount = 0)
     */
    public function report21(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $school_year = isset($request->school_year) ? $request->school_year : '';

        $branchIds = Auth::user()->getBranchesHasUser();

        // Điều kiện cho agreements
        $agCond = " a.debt_amount = 0 AND a.branch_id IN ($branchIds)";
        if (!empty($branch_id)) {
            $agCond .= " AND a.branch_id IN (" . implode(",", $branch_id) . ")";
        }

        // Điều kiện năm học cho schedule_has_student
        $yearCond = $school_year ? " AND YEAR(shs.class_date) = '$school_year'" : "";

        // --- 1. Số lượng agreements (cột Giá trị) ---
        $agStats = u::first("
            SELECT
                COUNT(a.id)                             AS total_agreements,
                SUM(IF(a.type_fee = 1, 1, 0))           AS total_le,
                SUM(IF(a.type_fee = 2, 1, 0))           AS total_combo,
                SUM(a.total_charged)                    AS total_charged
            FROM agreements AS a
            WHERE $agCond
        ");

        // --- 2. Số lượng contracts (cột Sau tách) ---
        $ctStats = u::first("
            SELECT
                COUNT(c.id)                             AS total_contracts,
                SUM(IF(a.type_fee = 1, 1, 0))           AS total_le_ct,
                SUM(IF(a.type_fee = 2, 1, 0))           AS total_combo_ct
            FROM contracts AS c
            INNER JOIN agreements AS a ON a.id = c.agreement_id
            WHERE $agCond AND c.status NOT IN (0, 1, 7, 8)
        ");

        // --- 3. Doanh thu & Số buổi (tính từ contracts, lọc theo năm học nếu có) ---
        // Số buổi đã dạy trong năm học (lọc theo schedule_has_student)
        if ($school_year) {
            $sessionStats = u::first("
                SELECT
                    COUNT(DISTINCT shs.id)          AS done_sessions_year
                FROM schedule_has_student AS shs
                INNER JOIN contracts AS c ON c.id = shs.contract_id
                INNER JOIN agreements AS a ON a.id = c.agreement_id
                WHERE $agCond
                  AND c.status NOT IN (0, 1, 7, 8)
                  AND shs.status IN (1, 2)
                  AND YEAR(shs.class_date) = '$school_year'
            ");
            $done_sessions = (int) ($sessionStats->done_sessions_year ?? 0);
        } else {
            $sessionStats = u::first("
                SELECT SUM(c.done_sessions) AS done_sessions_all
                FROM contracts AS c
                INNER JOIN agreements AS a ON a.id = c.agreement_id
                WHERE $agCond AND c.status NOT IN (0, 1, 7, 8)
            ");
            $done_sessions = (int) ($sessionStats->done_sessions_all ?? 0);
        }

        // Doanh thu và số buổi tổng hợp từ contracts
        $finStats = u::first("
            SELECT
                SUM(c.total_charged)        AS total_charged_ct,
                SUM(c.summary_sessions)     AS total_summary_sessions,
                SUM(c.done_sessions)        AS total_done_sessions,
                SUM(c.left_sessions)        AS total_left_sessions,
                SUM(
                    CASE WHEN c.summary_sessions > 0
                        THEN ROUND(c.total_charged * c.done_sessions / c.summary_sessions, 0)
                        ELSE 0
                    END
                ) AS revenue_done,
                SUM(
                    CASE WHEN c.summary_sessions > 0
                        THEN ROUND(c.total_charged * c.left_sessions / c.summary_sessions, 0)
                        ELSE 0
                    END
                ) AS revenue_left
            FROM contracts AS c
            INNER JOIN agreements AS a ON a.id = c.agreement_id
            WHERE $agCond AND c.status NOT IN (0, 1, 7, 8)
        ");

        // Tổng số học sinh duy nhất
        $studentCount = u::first("
            SELECT COUNT(DISTINCT a.student_id) AS total_students
            FROM agreements AS a
            WHERE $agCond
        ");

        // % hoàn thành trung bình
        $totalSummary = (int) ($finStats->total_summary_sessions ?? 0);
        $totalDone = (int) ($finStats->total_done_sessions ?? 0);
        $pct_done = $totalSummary > 0 ? round($totalDone / $totalSummary * 100, 2) : 0;

        $totalCharged = (float) ($finStats->total_charged_ct ?? 0);
        $revenueDone = (float) ($finStats->revenue_done ?? 0);
        $revenueLeft = (float) ($finStats->revenue_left ?? 0);
        $leftSessions = (int) ($finStats->total_left_sessions ?? 0);

        // Giá trị cột (agreements)
        $val_total = (int) ($agStats->total_agreements ?? 0);
        $val_le = (int) ($agStats->total_le ?? 0);
        $val_combo = (int) ($agStats->total_combo ?? 0);

        // Giá trị sau tách (contracts)
        $sach_total = (int) ($ctStats->total_contracts ?? 0);
        $sach_le = (int) ($ctStats->total_le_ct ?? 0);
        $sach_combo = (int) ($ctStats->total_combo_ct ?? 0);

        $rows = [
            ['label' => 'Tổng số học viên', 'value' => $val_total, 'value_split' => $sach_total, 'is_bold' => true],
            ['label' => 'Số học viên đăng ký khóa lẻ', 'value' => $val_le, 'value_split' => $sach_le, 'is_bold' => false],
            ['label' => 'Số học viên đăng ký combo', 'value' => $val_combo, 'value_split' => $sach_combo, 'is_bold' => false],
            ['label' => 'Tổng doanh thu đã thu', 'value' => $totalCharged, 'value_split' => $totalCharged, 'is_bold' => false, 'is_money' => true],
            ['label' => 'Doanh thu đã thực hiện', 'value' => $revenueDone, 'value_split' => $revenueDone, 'is_bold' => false, 'is_money' => true],
            ['label' => 'Doanh thu chưa thực hiện (còn lại)', 'value' => $revenueLeft, 'value_split' => $revenueLeft, 'is_bold' => false, 'is_money' => true],
            ['label' => 'Số buổi đã dạy', 'value' => $totalDone, 'value_split' => $totalDone, 'is_bold' => false],
            ['label' => 'Số buổi còn lại phải dạy', 'value' => $leftSessions, 'value_split' => $leftSessions, 'is_bold' => false],
            ['label' => '% hoàn thành trung bình', 'value' => $pct_done . '%', 'value_split' => $pct_done . '%', 'is_bold' => false],
        ];

        return response()->json([
            'rows' => $rows,
            'school_year' => $school_year ?: date('Y'),
        ]);
    }

    /**
     * Report 22: Báo cáo chi tiết công nợ theo khách hàng
     * Columns: Mã HV, Họ tên, Chương trình, Team KD, Thành viên sale,
     *          Tổng học phí, Đã thu, Còn phải thu, Hạn thanh toán, Ngày thu gần nhất
     *
     * Hạn thanh toán = ngày buổi học thứ 8 (tính từ enrolment_start_date của contract đầu tiên)
     * Chỉ lấy agreements đã đặt cọc: total_charged > 0 (và thường debt_amount > 0 = còn nợ)
     */
    public function report22(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $team_id = isset($request->team_id) ? (int) $request->team_id : 0;   // ec_leader_id (role 69)
        $ec_id = isset($request->ec_id) ? (int) $request->ec_id : 0;   // ec_id (role 68)
        $keyword = isset($request->keyword) ? trim($request->keyword) : '';
        $due_start = isset($request->due_start) ? $request->due_start : '';  // khoảng hạn TT
        $due_end = isset($request->due_end) ? $request->due_end : '';
        $pay_start = isset($request->pay_start) ? $request->pay_start : '';  // khoảng ngày thu gần nhất
        $pay_end = isset($request->pay_end) ? $request->pay_end : '';

        $completion_status = isset($request->completion_status) ? (int) $request->completion_status : -1; // 1: hoàn thành (debt_amount=0), 0: chưa hoàn thành (debt_amount>0)

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

        $pagination = (object) $request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);
        $limitation = $limit > 0 ? " LIMIT $offset, $limit" : "";

        // ---- Điều kiện cơ bản ----
        $cond = " a.total_charged > 0
                  AND a.status > 0
                  AND a.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";

        if (!empty($branch_id)) {
            $cond .= " AND a.branch_id IN (" . implode(",", $branch_id) . ")";
        }
        if ($team_id > 0) {
            // Team KD = ec_leader_id (nếu có), hoặc ec_id chính là leader (ec_leader_id IS NULL)
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
        // Filter theo hạn thanh toán (buổi 8)
        if ($due_start) {
            $cond .= " AND a.first_8th_session_date >= '$due_start'";
        }
        if ($due_end) {
            $cond .= " AND a.first_8th_session_date <= '$due_end'";
        }
        // Filter theo ngày thu gần nhất
        if ($pay_start) {
            $cond .= " AND (SELECT MAX(p.charge_date) FROM payments p WHERE p.agreement_id = a.id) >= '$pay_start'";
        }
        if ($pay_end) {
            $cond .= " AND (SELECT MAX(p.charge_date) FROM payments p WHERE p.agreement_id = a.id) <= '$pay_end'";
        }

        // ---- Count ----
        $totalRow = u::first("
            SELECT COUNT(a.id) AS total
            FROM agreements AS a
            INNER JOIN students AS s ON s.id = a.student_id
            WHERE $cond
        ");
        $totalCount = (int) ($totalRow->total ?? 0);

        // ---- Main query ----
        $query = "
            SELECT
                s.lms_code,
                s.name                          AS student_name,
                s.gud_mobile1                   AS phone,
                tf.name                         AS course_name,
                -- Team KD: ec_leader_id nếu có, ngược lại chính ec_id là leader
                CASE
                    WHEN a.ec_leader_id IS NOT NULL
                        THEN (SELECT u.name FROM users u WHERE u.id = a.ec_leader_id)
                    ELSE
                        (SELECT u.name FROM users u WHERE u.id = a.ec_id)
                END                             AS team_name,
                (SELECT u.name FROM users u WHERE u.id = a.ec_id) AS ec_name,
                a.must_charge,
                a.total_charged,
                a.debt_amount,
                -- Ngày thu gần nhất
                (SELECT MAX(p.charge_date) FROM payments p WHERE p.agreement_id = a.id) AS last_pay_date,
                a.first_8th_session_date                        AS due_date,
                a.branch_id,
                (SELECT name FROM branches WHERE id = a.branch_id) AS branch_name,
                a.id                            AS agreement_id,
                a.created_at
            FROM agreements AS a
            INNER JOIN students AS s ON s.id = a.student_id
            LEFT JOIN tuition_fee AS tf ON tf.id = a.tuition_fee_id
            WHERE $cond
            ORDER BY a.id DESC
            $limitation
        ";

        $list = u::query($query);

        // ---- Summary (không phân trang) ----
        $sumRow = u::first("
            SELECT
                SUM(a.must_charge)   AS total_must,
                SUM(a.total_charged) AS total_charged,
                SUM(a.debt_amount)   AS total_debt
            FROM agreements AS a
            INNER JOIN students AS s ON s.id = a.student_id
            WHERE $cond
        ");

        $summary = [
            'total_must' => (float) ($sumRow->total_must ?? 0),
            'total_charged' => (float) ($sumRow->total_charged ?? 0),
            'total_debt' => (float) ($sumRow->total_debt ?? 0),
        ];

        $data = u::makingPagination($list, $totalCount, $page, $limit);
        $data->summary = $summary;

        return response()->json($data);
    }

    /**
     * Report 23: Thống kê doanh thu theo Team sale
     *
     * Columns: Team, Mới (count_recharge=0), Up LV (count_recharge>0),
     *          Doanh thu, Lương sale = Mới*10% + UpLV*6%
     *
     * Filter: branch_id, khoảng thời gian full_fee_date
     */
    public function report23(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $raw_team_id = isset($request->team_id) ? $request->team_id : 0;
        $completion_status = isset($request->completion_status) ? (int) $request->completion_status : -1;
        $register_status = isset($request->register_status) ? (int) $request->register_status : -1;
        $start_date = isset($request->start_date) ? $request->start_date : '';
        $end_date = isset($request->end_date) ? $request->end_date : '';
        $pay_start_date = isset($request->pay_start_date) ? $request->pay_start_date : '';
        $pay_end_date = isset($request->pay_end_date) ? $request->pay_end_date : '';
        $salary_month = isset($request->salary_month) ? $request->salary_month : '';

        // Phân quyền dữ liệu
        $user = Auth::user();
        $userRoles = u::query("SELECT role_id FROM role_has_user WHERE user_id = {$user->id}");
        $roleIds = [];
        foreach ($userRoles as $ur) {
            $roleIds[] = $ur->role_id;
        }

        $is_sale_leader = in_array(69, $roleIds);

        // ---- Điều kiện cơ bản ----
        $cond = " a.status > 0
                  AND a.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";

        if (!empty($branch_id)) {
            $cond .= " AND a.branch_id IN (" . implode(",", $branch_id) . ")";
        }

        if ($is_sale_leader && (empty($raw_team_id) || $raw_team_id === 0 || $raw_team_id === '0')) {
            $cond .= " AND (a.ec_leader_id = {$user->id} OR ((a.ec_leader_id IS NULL OR a.ec_leader_id = 0) AND a.ec_id = {$user->id}))";
        } elseif (!empty($raw_team_id) && $raw_team_id !== 0 && $raw_team_id !== '0') {
            if (is_string($raw_team_id) && strpos($raw_team_id, 'p_') === 0) {
                $leader_id = (int) substr($raw_team_id, 2);
                $cond .= " AND s.source_id = 6";
                if (in_array($leader_id, [38, 49, 58])) {
                    $cond .= " AND (a.ec_leader_id = $leader_id OR ((a.ec_leader_id IS NULL OR a.ec_leader_id = 0) AND a.ec_id = $leader_id))";
                } elseif ($leader_id == -1) {
                    $cond .= " AND (a.ec_leader_id NOT IN (38,49,58) OR a.ec_leader_id IS NULL OR a.ec_leader_id = 0) AND (a.ec_id NOT IN (38,49,58) OR a.ec_id IS NULL OR a.ec_id = 0)";
                }
            } else {
                $leader_id = (int) $raw_team_id;
                $cond .= " AND (s.source_id IS NULL OR s.source_id != 6)";
                if ($leader_id > 0) {
                    $cond .= " AND (a.ec_leader_id = $leader_id OR ((a.ec_leader_id IS NULL OR a.ec_leader_id = 0) AND a.ec_id = $leader_id))";
                } elseif ($leader_id == -1) {
                    $cond .= " AND (a.ec_leader_id IS NULL OR a.ec_leader_id = 0) AND (a.ec_id IS NULL OR a.ec_id = 0)";
                }
            }
        }
        if ($completion_status == 1) {
            $cond .= " AND a.debt_amount = 0";
        } elseif ($completion_status == 2) {
            $cond .= " AND a.debt_amount > 0 AND a.total_charged > 0";
        } elseif ($completion_status == 3) {
            $cond .= " AND a.debt_amount > 0 AND a.total_charged = 0";
        }

        if ($register_status == 1) {
            $cond .= " AND a.count_recharge = 0";
        } elseif ($register_status == 2) {
            $cond .= " AND a.count_recharge > 0";
        }

        if ($start_date) {
            $cond .= " AND a.created_at >= '$start_date 00:00:00'";
        }
        if ($end_date) {
            $cond .= " AND a.created_at <= '$end_date 23:59:59'";
        }

        if ($pay_start_date) {
            $cond .= " AND (SELECT MAX(charge_date) FROM payments p WHERE p.agreement_id = a.id) >= '$pay_start_date 00:00:00'";
        }
        if ($pay_end_date) {
            $cond .= " AND (SELECT MAX(charge_date) FROM payments p WHERE p.agreement_id = a.id) <= '$pay_end_date 23:59:59'";
        }

        $join_history = "";
        $expr_must_charge = "a.must_charge";
        $expr_discount = "a.discount_amount";
        $expr_truy_thu = "COALESCE((SELECT SUM(arh_prev.revenue_amount) 
                 FROM agreements_revenue_histories arh_prev 
                 WHERE arh_prev.agreement_id = a.id 
                 AND arh_prev.salary_month < a.salary_month), 0)";

        if ($salary_month === 'none') {
            $cond .= " AND (a.salary_month IS NULL OR a.salary_month = '') AND NOT EXISTS (SELECT 1 FROM agreements_revenue_histories arh WHERE arh.agreement_id = a.id)";
        } elseif ($salary_month !== '') {
            $sm = addslashes($salary_month);
            $current_month = (date('d') <= 5) ? date('Y-m', strtotime('-1 month')) : date('Y-m');
            
            if ($sm <= $current_month) {
                $cond .= " AND a.salary_month = '$sm'";
                $expr_must_charge = "a.must_charge";
                $expr_discount = "a.discount_amount";
                $expr_truy_thu = "COALESCE((SELECT SUM(arh_prev.revenue_amount) 
                     FROM agreements_revenue_histories arh_prev 
                     WHERE arh_prev.agreement_id = a.id 
                     AND arh_prev.salary_month < '$sm'), 0)";
            } else {
                $cond .= " AND (a.salary_month = '$sm' OR EXISTS (SELECT 1 FROM agreements_revenue_histories arh WHERE arh.agreement_id = a.id AND arh.salary_month = '$sm'))";
                
                $join_history = "LEFT JOIN agreements_revenue_histories arh_current ON arh_current.agreement_id = a.id AND arh_current.salary_month = '$sm'";
                
                $expr_must_charge = "COALESCE(arh_current.must_charge, a.must_charge)";
                $expr_discount = "COALESCE(arh_current.discount_amount, a.discount_amount)";
                $expr_truy_thu = "COALESCE((SELECT SUM(arh_prev.revenue_amount) 
                     FROM agreements_revenue_histories arh_prev 
                     WHERE arh_prev.agreement_id = a.id 
                     AND arh_prev.salary_month < '$sm'), 0)";
            }
        }

        // ---- Main query: group theo team ----
        $query = "
            SELECT
                ANY_VALUE(
                    CONCAT(
                        IF(s.source_id = 6, 'p_', ''),
                        CASE
                            WHEN s.source_id = 6 THEN
                                CASE
                                    WHEN a.ec_leader_id IN (38, 49, 58) THEN a.ec_leader_id
                                    WHEN a.ec_id IN (38, 49, 58) THEN a.ec_id
                                    ELSE -1
                                END
                            ELSE
                                COALESCE(NULLIF(a.ec_leader_id, 0), a.ec_id, -1)
                        END
                    )
                ) AS team_user_id,
                ANY_VALUE(
                    CASE
                        WHEN s.source_id = 6 THEN
                            CASE
                                WHEN a.ec_leader_id IN (38, 49, 58) THEN CONCAT('PAGE - ', (SELECT u.name FROM users u WHERE u.id = a.ec_leader_id))
                                WHEN a.ec_id IN (38, 49, 58) THEN CONCAT('PAGE - ', (SELECT u.name FROM users u WHERE u.id = a.ec_id))
                                ELSE 'PAGE - Khác (Không có team KD)'
                            END
                        ELSE
                            CASE
                                WHEN a.ec_leader_id IS NOT NULL AND a.ec_leader_id > 0 THEN
                                    (SELECT u.name FROM users u WHERE u.id = a.ec_leader_id)
                                WHEN a.ec_id IS NOT NULL AND a.ec_id > 0 THEN
                                    (SELECT u.name FROM users u WHERE u.id = a.ec_id)
                                ELSE
                                    'Khác (Không có team KD)'
                            END
                    END
                )                                                       AS team_name,
                IF(s.source_id = 6, 1, 0)                               AS is_page,
                COUNT(CASE WHEN a.count_recharge = 0 THEN 1 END)       AS new_count,
                COUNT(CASE WHEN a.count_recharge > 0 THEN 1 END)       AS uplv_count,
                COUNT(a.id)                                            AS unseparated_sales,
                SUM(tf.number_of_months)                               AS separated_sales,
                SUM(CASE WHEN a.count_recharge = 0 THEN ($expr_must_charge - COALESCE($expr_discount, 0) - $expr_truy_thu) ELSE 0 END) AS new_revenue,
                SUM(CASE WHEN a.count_recharge > 0 THEN ($expr_must_charge - COALESCE($expr_discount, 0) - $expr_truy_thu) ELSE 0 END) AS uplv_revenue,
                SUM($expr_must_charge - COALESCE($expr_discount, 0) - $expr_truy_thu) AS total_revenue
            FROM agreements AS a
            INNER JOIN students AS s ON s.id = a.student_id
            LEFT JOIN tuition_fee AS tf ON tf.id = a.tuition_fee_id
            $join_history
            WHERE $cond
            GROUP BY
                IF(s.source_id = 6, 1, 0),
                CASE
                    WHEN s.source_id = 6 THEN
                        CASE
                            WHEN a.ec_leader_id IN (38, 49, 58) THEN a.ec_leader_id
                            WHEN a.ec_id IN (38, 49, 58) THEN a.ec_id
                            ELSE -1
                        END
                    ELSE
                        COALESCE(NULLIF(a.ec_leader_id, 0), a.ec_id, -1)
                END
            ORDER BY team_name ASC
        ";

        $rows = u::query($query);

        $result = [];
        $sum = ['new_count' => 0, 'uplv_count' => 0, 'unseparated_sales' => 0, 'separated_sales' => 0, 'new_revenue' => 0.0, 'uplv_revenue' => 0.0, 'total_revenue' => 0.0, 'salary' => 0.0];

        foreach ($rows as $row) {
            $new_rev = (float) $row->new_revenue;
            $uplv_rev = (float) $row->uplv_revenue;
            $is_page = (int) ($row->is_page ?? 0);
            $new_rate = $is_page ? 0.05 : 0.10;
            $uplv_rate = $is_page ? 0.03 : 0.06;
            $salary = round($new_rev * $new_rate + $uplv_rev * $uplv_rate);

            $result[] = [
                'team_user_id' => $row->team_user_id,
                'team_name' => $row->team_name ?: '—',
                'new_count' => (int) $row->new_count,
                'uplv_count' => (int) $row->uplv_count,
                'unseparated_sales' => (int) $row->unseparated_sales,
                'separated_sales' => (int) $row->separated_sales,
                'new_revenue' => $new_rev,
                'uplv_revenue' => $uplv_rev,
                'total_revenue' => (float) $row->total_revenue,
                'salary' => $salary,
            ];
            $sum['new_count'] += (int) $row->new_count;
            $sum['uplv_count'] += (int) $row->uplv_count;
            $sum['unseparated_sales'] += (int) $row->unseparated_sales;
            $sum['separated_sales'] += (int) $row->separated_sales;
            $sum['new_revenue'] += $new_rev;
            $sum['uplv_revenue'] += $uplv_rev;
            $sum['total_revenue'] += (float) $row->total_revenue;
            $sum['salary'] += $salary;
        }

        return response()->json([
            'rows' => $result,
            'summary' => $sum,
        ]);
    }

    public function report24(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $team_id = isset($request->team_id) ? (int) $request->team_id : 0;
        $ec_id = isset($request->ec_id) ? (int) $request->ec_id : 0;
        $keyword = isset($request->keyword) ? trim($request->keyword) : '';
        $start_date = isset($request->start_date) ? $request->start_date : '';
        $end_date = isset($request->end_date) ? $request->end_date : '';
        $completion_status = isset($request->completion_status) ? (int) $request->completion_status : -1;

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

        $pagination = (object) $request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);
        $limitation = $limit > 0 ? " LIMIT $offset, $limit" : "";

        // ---- Điều kiện cơ bản ----
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

        $totalRow = u::first("
            SELECT COUNT(a.id) AS total
            FROM agreements AS a
            INNER JOIN students AS s ON s.id = a.student_id
            WHERE $cond
        ");
        $totalCount = (int) ($totalRow->total ?? 0);

        $query = "
            SELECT
                DATE(a.created_at) AS date_0,
                IF(a.count_recharge = 0, 'Mới', 'Up level') AS status_register,
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
                '' AS img_bill,
                a.discount_amount AS discount,
                a.debt_amount,
                a.id AS agreement_id
            FROM agreements AS a
            INNER JOIN students AS s ON s.id = a.student_id
            LEFT JOIN tuition_fee AS tf ON tf.id = a.tuition_fee_id
            WHERE $cond
            ORDER BY a.id DESC
            $limitation
        ";

        $list = u::query($query);

        foreach ($list as &$row) {
            $row->p1_amount = (float) $row->p1_amount;
            $total_paid = (float) $row->total_paid;
            $row->p2_amount = $total_paid - $row->p1_amount;
            if ($row->p2_amount < 0)
                $row->p2_amount = 0;

            $row->p2_date = ($row->p2_amount > 0) ? $row->last_pay_date : '';

            $agrmId = $row->agreement_id;
            $tmpPayments = u::query("SELECT attachments FROM tmp_payments WHERE agreement_id = $agrmId AND status = 1");
            $bills = [];
            foreach ($tmpPayments as $tp) {
                if (!empty($tp->attachments)) {
                    $arr = json_decode($tp->attachments, true);
                    if (is_array($arr)) {
                        $billIndex = 1;
                        foreach ($arr as $path) {
                            $fullUrl = rtrim(env('APP_URL'), '/') . '/' . ltrim($path, '/');
                            $bills[] = '<a href="' . $fullUrl . '" target="_blank" style="color:blue; text-decoration:underline; white-space:nowrap;">Xem bill ' . $billIndex . '</a>';
                            $billIndex++;
                        }
                    }
                }
            }
            $row->img_bill = implode('<br>', $bills);

            $row->p1_amount_cd = 0;
            $row->p1_date_cd = '';
            $row->p2_amount_cd = 0;
            $row->p2_date_cd = '';
            $row->img_bill_cd = '';

            $tmpPaymentsCd = u::query("SELECT charge_amount, charge_date, attachments FROM tmp_payments WHERE agreement_id = $agrmId AND status = 0 ORDER BY id ASC");
            if (count($tmpPaymentsCd) > 0) {
                $row->p1_amount_cd = (float) $tmpPaymentsCd[0]->charge_amount;
                $row->p1_date_cd = substr($tmpPaymentsCd[0]->charge_date, 0, 10);
                $total_paid_cd = 0;
                $last_pay_date_cd = '';
                $bills_cd = [];
                $billIndexCd = 1;
                foreach ($tmpPaymentsCd as $tp_cd) {
                    $total_paid_cd += (float) $tp_cd->charge_amount;
                    if ($tp_cd->charge_date) {
                        $last_pay_date_cd = substr($tp_cd->charge_date, 0, 10);
                    }
                    if (!empty($tp_cd->attachments)) {
                        $arr = json_decode($tp_cd->attachments, true);
                        if (is_array($arr)) {
                            foreach ($arr as $path) {
                                $fullUrl = rtrim(env('APP_URL'), '/') . '/' . ltrim($path, '/');
                                $bills_cd[] = '<a href="' . $fullUrl . '" target="_blank" style="color:red; text-decoration:underline; white-space:nowrap;">Xem bill ' . $billIndexCd . '</a>';
                                $billIndexCd++;
                            }
                        }
                    }
                }
                $row->p2_amount_cd = $total_paid_cd - $row->p1_amount_cd;
                if ($row->p2_amount_cd < 0)
                    $row->p2_amount_cd = 0;
                $row->p2_date_cd = ($row->p2_amount_cd > 0) ? $last_pay_date_cd : '';
                $row->img_bill_cd = implode('<br>', $bills_cd);
            }
        }

        $data = u::makingPagination($list, $totalCount, $page, $limit);
        return response()->json($data);
    }


    public function report28(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $team_id = isset($request->team_id) ? (int) $request->team_id : 0;
        $ec_id = isset($request->ec_id) ? (int) $request->ec_id : 0;
        $keyword = isset($request->keyword) ? trim($request->keyword) : '';
        $start_date = isset($request->start_date) ? $request->start_date : '';
        $end_date = isset($request->end_date) ? $request->end_date : '';
        $completion_status = isset($request->completion_status) ? (int) $request->completion_status : -1;

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

        $pagination = (object) $request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);
        $limitation = $limit > 0 ? " LIMIT $offset, $limit" : "";

        $cond = " a.status > 0 AND a.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";

        if (!empty($branch_id)) {
            $cond .= " AND a.branch_id IN (" . implode(",", $branch_id) . ")";
        }
        if ($team_id > 0) {
            $cond .= " AND (a.ec_leader_id = $team_id OR ((a.ec_leader_id IS NULL OR a.ec_leader_id = 0) AND a.ec_id = $team_id))";
        } elseif ($team_id == -1) {
            $cond .= " AND (a.ec_leader_id IS NULL OR a.ec_leader_id = 0) AND (a.ec_id IS NULL OR a.ec_id = 0)";
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

        if ($start_date || $end_date) {
            if ($start_date) {
                $cond .= " AND tp.created_at >= '$start_date 00:00:00'";
            }
            if ($end_date) {
                $cond .= " AND tp.created_at <= '$end_date 23:59:59'";
            }
        }

        $totalRow = u::first("
            SELECT COUNT(tp.id) AS total
            FROM tmp_payments AS tp
            INNER JOIN agreements AS a ON a.id = tp.agreement_id
            INNER JOIN students AS s ON s.id = a.student_id
            WHERE $cond
        ");
        $totalCount = (int) ($totalRow->total ?? 0);

        $query = "
            SELECT
                DATE(tp.created_at) AS date_0,
                IF(a.count_recharge = 0, 'Mới', 'Up level') AS status_register,
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
                tp.attachments AS img_bill_raw,
                tp.status,
                (
                    SELECT COUNT(tp2.id)
                    FROM tmp_payments tp2
                    INNER JOIN agreements a2 ON a2.id = tp2.agreement_id
                    WHERE a2.student_id = a.student_id 
                      AND (tp2.charge_date < tp.charge_date 
                           OR (tp2.charge_date = tp.charge_date AND tp2.id <= tp.id))
                ) AS transfer_count
            FROM tmp_payments AS tp
            INNER JOIN agreements AS a ON a.id = tp.agreement_id
            INNER JOIN students AS s ON s.id = a.student_id
            LEFT JOIN tuition_fee AS tf ON tf.id = a.tuition_fee_id
            WHERE $cond
            ORDER BY tp.id DESC
            $limitation
        ";

        $list = u::query($query);
        $finalList = [];
        foreach ($list as $row) {
            $bills = [];
            if (!empty($row->img_bill_raw)) {
                $arr = json_decode($row->img_bill_raw, true);
                if (is_array($arr)) {
                    $billIndex = 1;
                    foreach ($arr as $path) {
                        $fullUrl = rtrim(env('APP_URL'), '/') . '/' . ltrim($path, '/');
                        $bills[] = '<a href="' . $fullUrl . '" target="_blank" style="color:blue; text-decoration:underline; white-space:nowrap;">Xem bill ' . $billIndex . '</a>';
                        $billIndex++;
                    }
                }
            }
            $row->img_bill = implode('<br>', $bills);
            $finalList[] = $row;
        }

        return response()->json([
            'list' => $finalList,
            'paging' => [
                'cpage' => $page,
                'limit' => $limit,
                'total' => $totalCount,
                'init' => 1
            ]
        ]);
    }


    public function report25(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $raw_team_id = isset($request->team_id) ? $request->team_id : 0;
        $ec_id = isset($request->ec_id) ? (int) $request->ec_id : 0;
        $keyword = isset($request->keyword) ? trim($request->keyword) : '';
        $start_date = isset($request->start_date) ? $request->start_date : '';
        $end_date = isset($request->end_date) ? $request->end_date : '';
        $pay_start_date = isset($request->pay_start_date) ? $request->pay_start_date : '';
        $pay_end_date = isset($request->pay_end_date) ? $request->pay_end_date : '';
        $completion_status = isset($request->completion_status) ? (int) $request->completion_status : -1;
        $register_status = isset($request->register_status) ? (int) $request->register_status : -1;
        $salary_month = isset($request->salary_month) ? $request->salary_month : '';

        $user = Auth::user();
        $userRoles = u::query("SELECT role_id FROM role_has_user WHERE user_id = {$user->id}");
        $roleIds = [];
        foreach ($userRoles as $ur) {
            $roleIds[] = $ur->role_id;
        }

        $is_sale_leader = in_array(69, $roleIds);
        if (in_array(68, $roleIds) && !$is_sale_leader) {
            $ec_id = $user->id;
        }

        $pagination = (object) $request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);
        $limitation = $limit > 0 ? " LIMIT $offset, $limit" : "";

        $cond = " a.status > 0 AND a.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";

        if (!empty($branch_id)) {
            $cond .= " AND a.branch_id IN (" . implode(",", $branch_id) . ")";
        }

        if ($is_sale_leader && (empty($raw_team_id) || $raw_team_id === 0 || $raw_team_id === '0')) {
            $cond .= " AND (a.ec_leader_id = {$user->id} OR ((a.ec_leader_id IS NULL OR a.ec_leader_id = 0) AND a.ec_id = {$user->id}))";
        } elseif (!empty($raw_team_id) && $raw_team_id !== 0 && $raw_team_id !== '0') {
            if (is_string($raw_team_id) && strpos($raw_team_id, 'p_') === 0) {
                $leader_id = (int) substr($raw_team_id, 2);
                $cond .= " AND s.source_id = 6";
                if (in_array($leader_id, [38, 49, 58])) {
                    $cond .= " AND (a.ec_leader_id = $leader_id OR ((a.ec_leader_id IS NULL OR a.ec_leader_id = 0) AND a.ec_id = $leader_id))";
                } elseif ($leader_id == -1) {
                    $cond .= " AND (a.ec_leader_id NOT IN (38,49,58) OR a.ec_leader_id IS NULL OR a.ec_leader_id = 0) AND (a.ec_id NOT IN (38,49,58) OR a.ec_id IS NULL OR a.ec_id = 0)";
                }
            } else {
                $leader_id = (int) $raw_team_id;
                $cond .= " AND (s.source_id IS NULL OR s.source_id != 6)";
                if ($leader_id > 0) {
                    $cond .= " AND (a.ec_leader_id = $leader_id OR ((a.ec_leader_id IS NULL OR a.ec_leader_id = 0) AND a.ec_id = $leader_id))";
                } elseif ($leader_id == -1) {
                    $cond .= " AND (a.ec_leader_id IS NULL OR a.ec_leader_id = 0) AND (a.ec_id IS NULL OR a.ec_id = 0)";
                }
            }
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
        } elseif ($completion_status == 2) {
            $cond .= " AND a.debt_amount > 0 AND a.total_charged > 0";
        } elseif ($completion_status == 3) {
            $cond .= " AND a.debt_amount > 0 AND a.total_charged = 0";
        }
        if ($register_status == 1) {
            $cond .= " AND a.count_recharge = 0";
        } elseif ($register_status == 2) {
            $cond .= " AND a.count_recharge > 0";
        }
        if ($start_date) {
            $cond .= " AND a.created_at >= '$start_date 00:00:00'";
        }
        if ($end_date) {
            $cond .= " AND a.created_at <= '$end_date 23:59:59'";
        }
        if ($pay_start_date) {
            $cond .= " AND (SELECT MAX(charge_date) FROM payments p WHERE p.agreement_id = a.id) >= '$pay_start_date 00:00:00'";
        }
        if ($pay_end_date) {
            $cond .= " AND (SELECT MAX(charge_date) FROM payments p WHERE p.agreement_id = a.id) <= '$pay_end_date 23:59:59'";
        }
        $join_history = "";
        $select_must_charge = "a.must_charge";
        $select_discount = "a.discount_amount AS discount";
        $select_salary_month = "a.salary_month";
        $select_truy_thu = "COALESCE((SELECT SUM(arh_prev.revenue_amount) 
                 FROM agreements_revenue_histories arh_prev 
                 WHERE arh_prev.agreement_id = a.id 
                 AND arh_prev.salary_month < a.salary_month), 0) AS truy_thu_doanh_so";

        if ($salary_month === 'none') {
            $cond .= " AND (a.salary_month IS NULL OR a.salary_month = '') AND NOT EXISTS (SELECT 1 FROM agreements_revenue_histories arh WHERE arh.agreement_id = a.id)";
        } elseif ($salary_month !== '') {
            $sm = addslashes($salary_month);
            $current_month = (date('d') <= 5) ? date('Y-m', strtotime('-1 month')) : date('Y-m');
            
            if ($sm <= $current_month) {
                // If filtered month is the actual current calendar month, use live data
                $cond .= " AND a.salary_month = '$sm'";
                
                $select_must_charge = "a.must_charge";
                $select_discount = "a.discount_amount AS discount";
                $select_salary_month = "a.salary_month";
                $select_truy_thu = "COALESCE((SELECT SUM(arh_prev.revenue_amount) 
                     FROM agreements_revenue_histories arh_prev 
                     WHERE arh_prev.agreement_id = a.id 
                     AND arh_prev.salary_month < '$sm'), 0) AS truy_thu_doanh_so";
            } else {
                // If filtered month is a past month, use history snapshot
                $cond .= " AND (a.salary_month = '$sm' OR EXISTS (SELECT 1 FROM agreements_revenue_histories arh WHERE arh.agreement_id = a.id AND arh.salary_month = '$sm'))";
                
                $join_history = "LEFT JOIN agreements_revenue_histories arh_current ON arh_current.agreement_id = a.id AND arh_current.salary_month = '$sm'";
                
                $select_must_charge = "COALESCE(arh_current.must_charge, a.must_charge) AS must_charge";
                $select_discount = "COALESCE(arh_current.discount_amount, a.discount_amount) AS discount";
                $select_salary_month = "IF(arh_current.id IS NOT NULL, arh_current.salary_month, a.salary_month) AS salary_month";
                $select_truy_thu = "COALESCE((SELECT SUM(arh_prev.revenue_amount) 
                     FROM agreements_revenue_histories arh_prev 
                     WHERE arh_prev.agreement_id = a.id 
                     AND arh_prev.salary_month < '$sm'), 0) AS truy_thu_doanh_so";
            }
        }

        $totalRow = u::first("
            SELECT COUNT(a.id) AS total
            FROM agreements AS a
            INNER JOIN students AS s ON s.id = a.student_id
            WHERE $cond
        ");
        $totalCount = (int) ($totalRow->total ?? 0);

        $query = "
            SELECT
                DATE(a.created_at) AS date_0,
                IF(a.count_recharge = 0, 'Mới', 'Up level') AS status_register,
                tf.name AS course_name,
                s.name AS student_name,
                (SELECT p.link_facebook FROM crm_parents p WHERE p.student_id = s.id ORDER BY p.id DESC LIMIT 1) AS link_facebook,
                s.gud_mobile1 AS phone,
                CASE
                    WHEN s.source_id = 6 THEN
                        CASE
                            WHEN a.ec_leader_id IN (38, 49, 58) THEN CONCAT('PAGE - ', (SELECT u.name FROM users u WHERE u.id = a.ec_leader_id))
                            WHEN a.ec_id IN (38, 49, 58) THEN CONCAT('PAGE - ', (SELECT u.name FROM users u WHERE u.id = a.ec_id))
                            ELSE 'PAGE - Khác (Không có team KD)'
                        END
                    ELSE
                        CASE
                            WHEN a.ec_leader_id IS NOT NULL AND a.ec_leader_id > 0 THEN
                                (SELECT u.name FROM users u WHERE u.id = a.ec_leader_id)
                            WHEN a.ec_id IS NOT NULL AND a.ec_id > 0 THEN
                                (SELECT u.name FROM users u WHERE u.id = a.ec_id)
                            ELSE
                                'Khác (Không có team KD)'
                        END
                END AS team_name,
                CASE
                    WHEN s.source_id = 6 AND (a.ec_id IN (38, 49, 58) OR a.ec_leader_id IN (38, 49, 58)) THEN
                        CONCAT('PAGE - ', (SELECT u.name FROM users u WHERE u.id = a.ec_id))
                    ELSE
                        (SELECT u.name FROM users u WHERE u.id = a.ec_id)
                END AS ec_name,
                (SELECT CONCAT(cls.cls_name, IF(ct.enrolment_start_date IS NOT NULL AND ct.enrolment_start_date > '2000-01-01', CONCAT(' (', SUBSTRING(ct.enrolment_start_date, 1, 10), ')'), ''))
                 FROM contracts ct
                 LEFT JOIN classes cls ON cls.id = ct.class_id
                 WHERE ct.agreement_id = a.id AND ct.class_id > 0 AND ct.status > 0
                 ORDER BY ct.id ASC LIMIT 1) AS class_info,
                s.address,
                s.source_id,
                $select_must_charge,
                a.total_charged,
                IF(a.group_type > 0, CONCAT('Nhóm ', a.group_type), 'Không') AS dk_chung,
                (SELECT amount FROM payments p WHERE p.agreement_id = a.id ORDER BY id ASC LIMIT 1) AS p1_amount,
                (SELECT charge_date FROM payments p WHERE p.agreement_id = a.id ORDER BY id ASC LIMIT 1) AS p1_date,
                (SELECT SUM(amount) FROM payments p WHERE p.agreement_id = a.id) AS total_paid,
                (SELECT MAX(charge_date) FROM payments p WHERE p.agreement_id = a.id) AS last_pay_date,
                $select_discount,
                a.first_8th_session_date AS due_date,
                a.debt_amount,
                a.id AS agreement_id,
                $select_salary_month,
                $select_truy_thu
            FROM agreements AS a
            INNER JOIN students AS s ON s.id = a.student_id
            LEFT JOIN tuition_fee AS tf ON tf.id = a.tuition_fee_id
            $join_history
            WHERE $cond
            ORDER BY a.id DESC
            $limitation
        ";

        $list = u::query($query);

        foreach ($list as &$row) {
            $row->p1_amount = (float) $row->p1_amount;
            $total_paid = (float) $row->total_paid;
            $row->p2_amount = $total_paid - $row->p1_amount;
            if ($row->p2_amount < 0)
                $row->p2_amount = 0;

            $row->p2_date = ($row->p2_amount > 0) ? $row->last_pay_date : '';

            $row->xn_ketoan = ((float) $row->debt_amount > 0) ? 'R thiếu' : 'R';

            $row->truy_thu_doanh_so = (float) $row->truy_thu_doanh_so;
            $row->luong_sale = 0;
            if ((float) $row->debt_amount == 0) {
                if ((int) $row->source_id == 6) {
                    $rate = ($row->status_register == 'Mới') ? 0.05 : 0.03;
                } else {
                    $rate = ($row->status_register == 'Mới') ? 0.10 : 0.06;
                }
                
                $doanh_so = ((float) $row->must_charge - (float) $row->discount) - $row->truy_thu_doanh_so;
                $row->luong_sale = $doanh_so * $rate;
            }
        }

        $data = u::makingPagination($list, $totalCount, $page, $limit);
        return response()->json($data);
    }

    public function report30(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $raw_team_id = isset($request->team_id) ? $request->team_id : 0;
        $ec_id = isset($request->ec_id) ? (int) $request->ec_id : 0;
        $keyword = isset($request->keyword) ? trim($request->keyword) : '';
        $start_date = isset($request->start_date) ? $request->start_date : '';
        $end_date = isset($request->end_date) ? $request->end_date : '';
        $pay_start_date = isset($request->pay_start_date) ? $request->pay_start_date : '';
        $pay_end_date = isset($request->pay_end_date) ? $request->pay_end_date : '';
        $completion_status = isset($request->completion_status) ? (int) $request->completion_status : -1;
        $register_status = isset($request->register_status) ? (int) $request->register_status : -1;
        $salary_month = isset($request->salary_month) ? $request->salary_month : '';

        $user = Auth::user();
        $userRoles = u::query("SELECT role_id FROM role_has_user WHERE user_id = {$user->id}");
        $roleIds = [];
        foreach ($userRoles as $ur) {
            $roleIds[] = $ur->role_id;
        }

        $is_sale_leader = in_array(69, $roleIds);
        if (in_array(68, $roleIds) && !$is_sale_leader) {
            $ec_id = $user->id;
        }

        $cond = " a.status > 0 AND a.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";

        if (!empty($branch_id)) {
            $cond .= " AND a.branch_id IN (" . implode(",", $branch_id) . ")";
        }

        if ($is_sale_leader && (empty($raw_team_id) || $raw_team_id === 0 || $raw_team_id === '0')) {
            $cond .= " AND (a.ec_leader_id = {$user->id} OR ((a.ec_leader_id IS NULL OR a.ec_leader_id = 0) AND a.ec_id = {$user->id}))";
        } elseif (!empty($raw_team_id) && $raw_team_id !== 0 && $raw_team_id !== '0') {
            if (is_string($raw_team_id) && strpos($raw_team_id, 'p_') === 0) {
                $leader_id = (int) substr($raw_team_id, 2);
                $cond .= " AND s.source_id = 6";
                if (in_array($leader_id, [38, 49, 58])) {
                    $cond .= " AND (a.ec_leader_id = $leader_id OR ((a.ec_leader_id IS NULL OR a.ec_leader_id = 0) AND a.ec_id = $leader_id))";
                } elseif ($leader_id == -1) {
                    $cond .= " AND (a.ec_leader_id NOT IN (38,49,58) OR a.ec_leader_id IS NULL OR a.ec_leader_id = 0) AND (a.ec_id NOT IN (38,49,58) OR a.ec_id IS NULL OR a.ec_id = 0)";
                }
            } else {
                $leader_id = (int) $raw_team_id;
                $cond .= " AND (s.source_id IS NULL OR s.source_id != 6)";
                if ($leader_id > 0) {
                    $cond .= " AND (a.ec_leader_id = $leader_id OR ((a.ec_leader_id IS NULL OR a.ec_leader_id = 0) AND a.ec_id = $leader_id))";
                } elseif ($leader_id == -1) {
                    $cond .= " AND (a.ec_leader_id IS NULL OR a.ec_leader_id = 0) AND (a.ec_id IS NULL OR a.ec_id = 0)";
                }
            }
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
        } elseif ($completion_status == 2) {
            $cond .= " AND a.debt_amount > 0 AND a.total_charged > 0";
        } elseif ($completion_status == 3) {
            $cond .= " AND a.debt_amount > 0 AND a.total_charged = 0";
        }
        if ($register_status == 1) {
            $cond .= " AND a.count_recharge = 0";
        } elseif ($register_status == 2) {
            $cond .= " AND a.count_recharge > 0";
        }
        if ($start_date) {
            $cond .= " AND a.created_at >= '$start_date 00:00:00'";
        }
        if ($end_date) {
            $cond .= " AND a.created_at <= '$end_date 23:59:59'";
        }
        if ($pay_start_date) {
            $cond .= " AND (SELECT MAX(charge_date) FROM payments p WHERE p.agreement_id = a.id) >= '$pay_start_date 00:00:00'";
        }
        if ($pay_end_date) {
            $cond .= " AND (SELECT MAX(charge_date) FROM payments p WHERE p.agreement_id = a.id) <= '$pay_end_date 23:59:59'";
        }
        $join_history = "";
        $select_must_charge = "a.must_charge";
        $select_discount = "a.discount_amount AS discount";
        $select_truy_thu = "COALESCE((SELECT SUM(arh_prev.revenue_amount) 
                 FROM agreements_revenue_histories arh_prev 
                 WHERE arh_prev.agreement_id = a.id 
                 AND arh_prev.salary_month < a.salary_month), 0) AS truy_thu_doanh_so";

        if ($salary_month === 'none') {
            $cond .= " AND (a.salary_month IS NULL OR a.salary_month = '') AND NOT EXISTS (SELECT 1 FROM agreements_revenue_histories arh WHERE arh.agreement_id = a.id)";
        } elseif ($salary_month !== '') {
            $sm = addslashes($salary_month);
            $current_month = (date('d') <= 5) ? date('Y-m', strtotime('-1 month')) : date('Y-m');
            
            if ($sm <= $current_month) {
                $cond .= " AND a.salary_month = '$sm'";
                $select_must_charge = "a.must_charge";
                $select_discount = "a.discount_amount AS discount";
                $select_truy_thu = "COALESCE((SELECT SUM(arh_prev.revenue_amount) 
                     FROM agreements_revenue_histories arh_prev 
                     WHERE arh_prev.agreement_id = a.id 
                     AND arh_prev.salary_month < '$sm'), 0) AS truy_thu_doanh_so";
            } else {
                $cond .= " AND (a.salary_month = '$sm' OR EXISTS (SELECT 1 FROM agreements_revenue_histories arh WHERE arh.agreement_id = a.id AND arh.salary_month = '$sm'))";
                
                $join_history = "LEFT JOIN agreements_revenue_histories arh_current ON arh_current.agreement_id = a.id AND arh_current.salary_month = '$sm'";
                
                $select_must_charge = "COALESCE(arh_current.must_charge, a.must_charge) AS must_charge";
                $select_discount = "COALESCE(arh_current.discount_amount, a.discount_amount) AS discount";
                $select_truy_thu = "COALESCE((SELECT SUM(arh_prev.revenue_amount) 
                     FROM agreements_revenue_histories arh_prev 
                     WHERE arh_prev.agreement_id = a.id 
                     AND arh_prev.salary_month < '$sm'), 0) AS truy_thu_doanh_so";
            }
        }

        $query = "
            SELECT
                IF(a.count_recharge = 0, 'Mới', 'Up level') AS status_register,
                CASE
                    WHEN s.source_id = 6 AND (a.ec_id IN (38, 49, 58) OR a.ec_leader_id IN (38, 49, 58)) THEN
                        CONCAT('PAGE - ', (SELECT u.name FROM users u WHERE u.id = a.ec_id))
                    ELSE
                        (SELECT u.name FROM users u WHERE u.id = a.ec_id)
                END AS ec_name,
                $select_must_charge,
                $select_discount,
                a.debt_amount,
                a.ec_id,
                s.source_id,
                $select_truy_thu,
                a.id AS agreement_id
            FROM agreements AS a
            INNER JOIN students AS s ON s.id = a.student_id
            $join_history
            WHERE $cond
        ";

        $list = u::query($query);
        $grouped = [];
        $total_luong_sale = 0;

        foreach ($list as &$row) {
            $ec_name = $row->ec_name ? $row->ec_name : 'Khác';
            $luong_sale = 0;
            if ((float) $row->debt_amount == 0) {
                if ((int) $row->source_id == 6) {
                    $rate = ($row->status_register == 'Mới') ? 0.05 : 0.03;
                } else {
                    $rate = ($row->status_register == 'Mới') ? 0.10 : 0.06;
                }
                $doanh_so = ((float) $row->must_charge - (float) $row->discount) - (float) $row->truy_thu_doanh_so;
                $luong_sale = $doanh_so * $rate;
            }

            if (!isset($grouped[$ec_name])) {
                $grouped[$ec_name] = [
                    'ec_name' => $ec_name,
                    'luong_sale' => 0,
                    'luong_cung' => 0,
                    'thuong_lead' => 0,
                    'thuong_team' => 0,
                    'tong_luong' => 0,
                ];
            }

            $grouped[$ec_name]['luong_sale'] += $luong_sale;
            $total_luong_sale += $luong_sale;
        }

        $final_list = [];
        foreach ($grouped as $g) {
            $g['tong_luong'] = $g['luong_sale'] + $g['luong_cung'] + $g['thuong_lead'] + $g['thuong_team'];
            $final_list[] = $g;
        }

        // Sắp xếp danh sách theo tên giảm dần (hoặc lương giảm dần)
        usort($final_list, function ($a, $b) {
            return $b['tong_luong'] <=> $a['tong_luong'];
        });

        $summary = [
            'total_luong_sale' => $total_luong_sale,
            'total_luong_cung' => 0,
            'total_thuong_lead' => 0,
            'total_thuong_team' => 0,
            'total_tong_luong' => $total_luong_sale,
        ];

        return response()->json([
            'list' => $final_list,
            'summary' => $summary,
            'paging' => [
                'cpage' => 1,
                'limit' => 1000,
                'total' => count($final_list),
                'init' => 1
            ]
        ]);
    }

    public function report26(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $team_id = isset($request->team_id) ? (int) $request->team_id : 0;
        $ec_id = isset($request->ec_id) ? (int) $request->ec_id : 0;
        $product_id = isset($request->product_id) ? (int) $request->product_id : 0;
        $keyword = isset($request->keyword) ? trim($request->keyword) : '';
        $class_keyword = isset($request->class_keyword) ? trim($request->class_keyword) : '';
        $start_date = isset($request->start_date) ? $request->start_date : '';
        $end_date = isset($request->end_date) ? $request->end_date : '';
        $cls_start_date = isset($request->cls_start_date) ? $request->cls_start_date : '';
        $cls_end_date = isset($request->cls_end_date) ? $request->cls_end_date : '';

        $user = Auth::user();

        $pagination = (object) $request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);
        $limitation = $limit > 0 ? " LIMIT $offset, $limit" : "";

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
        $cond .= " AND c.class_id > 0";
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

        $totalRow = u::first("
            SELECT COUNT(c.id) AS total
            FROM contracts AS c
            INNER JOIN students AS s ON s.id = c.student_id
            LEFT JOIN classes AS cls ON cls.id = c.class_id
            WHERE $cond
        ");
        $totalCount = (int) ($totalRow->total ?? 0);

        $query = "
            SELECT
                c.created_at,
                s.name AS student_name,
                s.lms_code,
                s.gud_mobile1 AS phone,
                s.gud_mobile2 AS parent_phone,
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
            $limitation
        ";

        $list = u::query($query);

        $data = u::makingPagination($list, $totalCount, $page, $limit);
        return response()->json($data);
    }

    public function report29(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $team_id = isset($request->team_id) ? (int) $request->team_id : 0;
        $ec_id = isset($request->ec_id) ? (int) $request->ec_id : 0;
        $product_id = isset($request->product_id) ? (int) $request->product_id : 0;
        $keyword = isset($request->keyword) ? trim($request->keyword) : '';
        $start_date = isset($request->start_date) ? $request->start_date : '';
        $end_date = isset($request->end_date) ? $request->end_date : '';

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

        $pagination = (object) $request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);
        $limitation = $limit > 0 ? " LIMIT $offset, $limit" : "";

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

        $cond .= " AND (c.class_id = 0 OR c.class_id IS NULL)";

        if ($start_date) {
            $cond .= " AND c.created_at >= '$start_date 00:00:00'";
        }
        if ($end_date) {
            $cond .= " AND c.created_at <= '$end_date 23:59:59'";
        }

        $totalRow = u::first("
            SELECT COUNT(c.id) AS total
            FROM contracts AS c
            INNER JOIN students AS s ON s.id = c.student_id
            WHERE $cond
        ");
        $totalCount = (int) ($totalRow->total ?? 0);

        $query = "
            SELECT
                c.created_at,
                s.name AS student_name,
                s.lms_code,
                s.gud_mobile1 AS phone,
                tf.name AS course_name,
                p.name AS product_name,
                u_team.name AS team_name,
                u_ec.name AS ec_name
            FROM contracts AS c
            INNER JOIN students AS s ON s.id = c.student_id
            LEFT JOIN agreements AS a ON a.id = c.agreement_id
            LEFT JOIN tuition_fee AS tf ON tf.id = a.tuition_fee_id
            LEFT JOIN products AS p ON p.id = c.product_id
            LEFT JOIN users AS u_team ON u_team.id = c.ec_leader_id
            LEFT JOIN users AS u_ec ON u_ec.id = c.ec_id
            WHERE $cond
            ORDER BY c.id DESC
            $limitation
        ";

        $list = u::query($query);

        $data = u::makingPagination($list, $totalCount, $page, $limit);
        return response()->json($data);
    }

    public function reportBookDelivered(Request $request)
    {
        $keyword = isset($request->keyword) ? $request->keyword : '';
        $product_id = isset($request->product_id) ? $request->product_id : '';
        $status = isset($request->status) ? $request->status : '';
        $start_date = isset($request->start_date) ? $request->start_date : '';
        $end_date = isset($request->end_date) ? $request->end_date : '';
        $cls_start_start = isset($request->cls_start_start) ? $request->cls_start_start : '';
        $cls_start_end = isset($request->cls_start_end) ? $request->cls_start_end : '';
        $team_id = isset($request->team_id) ? (int) $request->team_id : 0;
        $is_online = isset($request->is_online) ? $request->is_online : '';
        $book_receive = isset($request->book_receive) ? $request->book_receive : '';

        $pagination = (object) $request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);
        $limitation = $limit > 0 ? " LIMIT $offset, $limit" : "";

        $cond = " ((c.class_id IS NOT NULL AND c.class_id > 0) OR c.book_delivered_date IS NOT NULL) AND ((SELECT SUM(charge_amount) FROM tmp_payments WHERE agreement_id = c.agreement_id AND status IN (0, 1)) >= 2000000 OR (SELECT SUM(amount) FROM payments WHERE agreement_id = c.agreement_id) >= 2000000) AND s.status > 0 AND s.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";

        if ($team_id > 0) {
            $cond .= " AND (c.ec_leader_id = $team_id OR (c.ec_leader_id IS NULL AND c.ec_id = $team_id)) ";
        }

        if ($keyword !== '') {
            $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%' OR s.gud_mobile1 LIKE '%$keyword%' OR cls.cls_name LIKE '%$keyword%') ";
        }

        if ($start_date !== '') {
            $cond .= " AND c.book_delivered_date >= '$start_date' ";
        }

        if ($end_date !== '') {
            $cond .= " AND c.book_delivered_date <= '$end_date' ";
        }

        if ($cls_start_start !== '') {
            $cond .= " AND cls.cls_startdate >= '$cls_start_start' ";
        }

        if ($cls_start_end !== '') {
            $cond .= " AND cls.cls_startdate <= '$cls_start_end' ";
        }

        if ($product_id !== '') {
            $cond .= " AND c.product_id = '$product_id' ";
        }

        if ($status !== '') {
            if ($status == '1') {
                $cond .= " AND c.book_delivered_date IS NOT NULL ";
            } elseif ($status == '0') {
                $cond .= " AND c.book_delivered_date IS NULL ";
            }
        }

        if ($is_online !== '') {
            $cond .= " AND cls.is_online = " . (int) $is_online . " ";
        }

        if ($book_receive !== '') {
            $cond .= " AND a.book_receive = " . (int) $book_receive . " ";
        }

        $order_by = " ORDER BY c.id DESC ";

        $countSql = "SELECT count(c.id) AS total 
                     FROM contracts AS c
                     LEFT JOIN agreements AS a ON a.id = c.agreement_id
                     LEFT JOIN students AS s ON c.student_id = s.id
                     LEFT JOIN classes AS cls ON c.class_id = cls.id
                     WHERE $cond";
        $total = u::first($countSql);

        $query = "SELECT c.id AS contract_id, c.book_delivered_date, c.book_note,
                    s.lms_code, s.name AS student_name, s.address, s.gud_mobile1 AS phone,
                    cls.cls_name, cls.cls_startdate,
                    b_cls.cls_name AS book_class_name,
                    p.name AS product_name,
                    cp.link_facebook,
                    a.book_receive,
                    CASE
                        WHEN c.ec_leader_id IS NOT NULL THEN (SELECT u.name FROM users u WHERE u.id = c.ec_leader_id)
                        ELSE (SELECT u.name FROM users u WHERE u.id = c.ec_id)
                    END AS team_name
                FROM contracts AS c
                    LEFT JOIN agreements AS a ON a.id = c.agreement_id
                    LEFT JOIN students AS s ON c.student_id = s.id
                    LEFT JOIN classes AS cls ON c.class_id = cls.id
                    LEFT JOIN classes AS b_cls ON c.book_class_id = b_cls.id
                    LEFT JOIN products AS p ON c.product_id = p.id
                    LEFT JOIN crm_parents AS cp ON cp.student_id = s.id
                WHERE $cond 
                $order_by $limitation";

        $list = u::query($query);

        $data = u::makingPagination($list, $total ? $total->total : 0, $page, $limit);
        return response()->json($data);
    }

    public function updateBookDeliveredDate(Request $request)
    {
        $role_ids = u::query("SELECT role_id FROM role_has_user WHERE user_id = " . Auth::user()->id);
        $roles = array_map(function ($r) {
            return $r->role_id;
        }, $role_ids);
        if (in_array(68, $roles) || in_array(69, $roles)) { // 68: Sale, 69: Sale Leader
            return response()->json(['status' => 0, 'message' => 'Bạn không có quyền cập nhật trường này']);
        }

        $contract_ids = $request->contract_ids;
        $date = $request->book_delivered_date;
        $note = $request->book_note;
        $has_note = $request->has('book_note');

        if (!empty($contract_ids) && is_array($contract_ids)) {
            $ids = implode(',', array_map('intval', $contract_ids));

            $contracts = u::query("SELECT id, class_id, book_delivered_date FROM contracts WHERE id IN ($ids)");

            foreach ($contracts as $c) {
                $upd_contracts = [];
                $upd_log = [];

                if (!empty($date)) {
                    $safe_date = date('Y-m-d', strtotime($date));
                    $upd_contracts[] = "book_delivered_date = '$safe_date'";
                    $upd_log[] = "book_delivered_date = '$safe_date'";

                    if (empty($c->book_delivered_date)) {
                        $cls_id = $c->class_id ? $c->class_id : 'NULL';
                        $upd_contracts[] = "book_class_id = $cls_id";
                        $upd_log[] = "book_class_id = $cls_id";
                    }
                } else if ($request->has('book_delivered_date')) {
                    $upd_contracts[] = "book_delivered_date = NULL";
                    $upd_log[] = "book_delivered_date = NULL";
                    $upd_contracts[] = "book_class_id = NULL";
                    $upd_log[] = "book_class_id = NULL";
                }

                if ($has_note) {
                    $safe_note = addslashes($note);
                    $upd_contracts[] = "book_note = '$safe_note'";
                    $upd_log[] = "book_note = '$safe_note'";
                }

                if (!empty($upd_contracts)) {
                    u::query("UPDATE contracts SET " . implode(', ', $upd_contracts) . " WHERE id = " . $c->id);
                    u::query("UPDATE log_contracts SET " . implode(', ', $upd_log) . " WHERE contract_id = " . $c->id);
                }
            }
            return response()->json(['status' => 1, 'message' => 'Cập nhật thành công']);
        }
        return response()->json(['status' => 0, 'message' => 'Dữ liệu không hợp lệ']);
    }

    public function updateSalaryMonth(Request $request)
    {
        $agreement_ids = $request->agreement_ids;
        $salary_month = $request->salary_month;

        if (!empty($agreement_ids) && is_array($agreement_ids)) {
            $ids = implode(',', array_map('intval', $agreement_ids));
            if (!empty($salary_month)) {
                $safe_month = addslashes($salary_month);
                u::query("UPDATE agreements SET salary_month = '$safe_month' WHERE id IN ($ids)");
            } else {
                u::query("UPDATE agreements SET salary_month = NULL WHERE id IN ($ids)");
            }
            return response()->json(['status' => 1, 'message' => 'Cập nhật thành công']);
        }
        return response()->json(['status' => 0, 'message' => 'Dữ liệu không hợp lệ']);
    }

    public function updateSalaryMonthAll($agreement_id = null)
    {
        if ((int) date('j') <= 5) {
            $salaryMonth = date('Y-m', strtotime('first day of previous month'));
        } else {
            $salaryMonth = date('Y-m');
        }
        if ($agreement_id) {
            $cond = " AND id = $agreement_id";
        } else {
            $cond = " AND id IN (
                SELECT agreement_id
                FROM payments
                WHERE agreement_id IS NOT NULL
                GROUP BY agreement_id
                HAVING DATE_FORMAT(MAX(charge_date), '%Y-%m') = '$salaryMonth'
            )";
        }

        $query = "
            UPDATE agreements
            SET salary_month = '$salaryMonth'
            WHERE debt_amount = 0
            AND (salary_month IS NULL OR salary_month='') $cond
        ";
        u::query($query);

        $query = "
            UPDATE agreements
            SET salary_month = NULL
            WHERE debt_amount > 0 $cond
            AND salary_month = '$salaryMonth'
        ";
        u::query($query);

        return response()->json(['status' => 1, 'message' => 'Cập nhật thành công']);
    }

    public function refundDepositReport(Request $request)
    {
        $keyword = isset($request->keyword) ? trim($request->keyword) : '';
        $month = isset($request->month) ? $request->month : ''; // Format 'YYYY-MM'

        $pagination = (object) $request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);
        $limitation = $limit > 0 ? " LIMIT $offset, $limit" : "";

        // Base condition
        $cond = "p.type = 3 ";

        if ($month) {
            $cond .= " AND DATE_FORMAT(p.charge_date, '%Y-%m') = '$month'";
        }

        if ($keyword !== '') {
            $cleanKeyword = ltrim($keyword, '0');
            $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%' OR s.gud_mobile1 LIKE '%$keyword%' OR s.gud_mobile1 LIKE '%$cleanKeyword%' OR s.gud_mobile2 LIKE '%$keyword%' OR s.c2c_mobile LIKE '%$keyword%')";
        }

        // Leader Sale / Sales filter logic (Role 68, 69)
        $role_ids = u::query("SELECT role_id FROM role_has_user WHERE user_id = " . Auth::user()->id);
        $roles = array_map(function ($r) {
            return $r->role_id;
        }, $role_ids);

        if (in_array(68, $roles) || in_array(69, $roles)) {
            $staff_ids = Auth::user()->getStaffHasUser();
            if ($staff_ids) {
                $cond .= " AND (c.ec_id IN ($staff_ids) OR c.ec_leader_id = " . Auth::user()->id . ")";
            } else {
                $cond .= " AND (c.ec_id = " . Auth::user()->id . " OR c.ec_leader_id = " . Auth::user()->id . ")";
            }
        }

        $sql_select = "SELECT p.id, s.lms_code, s.name as student_name, s.gud_mobile1 as student_phone, 
                    (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.ec_id) AS ec_name,
                    (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.ec_leader_id) AS ec_leader_name,
                    p.amount, p.charge_date as refund_date, p.note";

        $sql_from = "FROM payments p
                    LEFT JOIN agreements c ON p.agreement_id = c.id
                    LEFT JOIN students s ON p.student_id = s.id
                    WHERE $cond";

        $total_query = u::first("SELECT COUNT(p.id) as total $sql_from");
        $total = $total_query ? $total_query->total : 0;

        $list = u::query("$sql_select $sql_from ORDER BY p.id DESC $limitation");

        $data = u::makingPagination($list, $total, $page, $limit);
        return response()->json($data);
    }

    public function teacherPayroll(Request $request)
    {
        $keyword = isset($request->keyword) ? $request->keyword : '';
        $end_date = isset($request->end_date) ? $request->end_date : '';
        $start_date = isset($request->start_date) ? $request->start_date : '';

        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 50;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit" : "";

        $cond = " s.status = 1 ";
        
        $user_role = \Illuminate\Support\Facades\Auth::user()->role_id;
        $user_id = \Illuminate\Support\Facades\Auth::user()->id;
        // Role 36: Giáo viên -> chỉ xem bản ghi của mình
        if ($user_role == 36) {
            $cond .= " AND s.teacher_id = $user_id ";
        }

        if ($keyword !== '') {
            $cond .= " AND (cl.cls_name LIKE '%$keyword%' OR ut.name LIKE '%$keyword%' OR ut.hrm_id LIKE '%$keyword%') ";
        }
        if ($end_date !== '') {
            $cond .= " AND s.class_date <= '$end_date'";
        }
        if ($start_date !== '') {
            $cond .= " AND s.class_date >= '$start_date'";
        }

        $query = "SELECT s.teacher_id, s.class_id, 
                    ut.name AS teacher_name,
                    ut.hrm_id AS teacher_code,
                    cl.cls_name AS class_name,
                    p.name AS product_name,
                    COUNT(s.id) AS total_sessions
            FROM schedules AS s 
                INNER JOIN classes AS cl ON cl.id = s.class_id
                LEFT JOIN products AS p ON p.id = cl.product_id
                INNER JOIN users AS ut ON ut.id = s.teacher_id
            WHERE $cond
            GROUP BY s.teacher_id, s.class_id
            ORDER BY ut.hrm_id ASC, cl.cls_name ASC";

        $count_query = "SELECT COUNT(*) AS total FROM ($query) AS t";
        $total = u::first($count_query);
        
        $list = u::query($query . $limitation);

        $all_records = u::query($query);
        $teacher_totals = [];
        foreach ($all_records as $item) {
            $salary = self::calculateSalary($item->class_name, $item->product_name, $item->total_sessions);
            if (!isset($teacher_totals[$item->teacher_id])) {
                $teacher_totals[$item->teacher_id] = 0;
            }
            $teacher_totals[$item->teacher_id] += $salary;
        }

        foreach ($list as $k => $item) {
            $list[$k]->salary = self::calculateSalary($item->class_name, $item->product_name, $item->total_sessions);
            $list[$k]->teacher_total_salary = isset($teacher_totals[$item->teacher_id]) ? $teacher_totals[$item->teacher_id] : 0;
        }

        return response()->json([
            'list' => $list,
            'paging' => [
                'total' => $total ? (int)$total->total : 0,
                'cpage' => $page,
                'limit' => $limit
            ]
        ]);
    }

    public static function calculateSalary($class_name, $product_name, $total_sessions)
    {
        $is_offline = \Illuminate\Support\Str::startsWith(strtoupper($class_name), 'O');
        $unit_price = 0;

        if ($is_offline) {
            switch (trim($product_name)) {
                case 'Pre-Toeic':
                    $unit_price = 300000;
                    break;
                case 'Toeic level 1':
                    $unit_price = 350000;
                    break;
                case 'Toeic level 2':
                    $unit_price = 400000;
                    break;
                case 'Toeic Writing':
                case 'Toeic Speaking':
                    $unit_price = 600000;
                    break;
            }
        } else {
            switch (trim($product_name)) {
                case 'Pre-Toeic':
                    $unit_price = 200000;
                    break;
                case 'Toeic level 1':
                    $unit_price = 250000;
                    break;
                case 'Toeic level 2':
                    $unit_price = 300000;
                    break;
                case 'Toeic Writing':
                case 'Toeic Speaking':
                    $unit_price = 450000;
                    break;
            }
        }

        return $unit_price * $total_sessions;
    }
}