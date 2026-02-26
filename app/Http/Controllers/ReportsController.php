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

        $order_by = " ORDER BY c.id DESC ";

        if ($having) {
            $countSql = "SELECT count(*) as total FROM (
                SELECT c.id, c.max_students, (SELECT count(ct.id) FROM contracts ct LEFT JOIN students s ON ct.student_id = s.id WHERE ct.class_id = c.id AND ct.status != 7 AND s.status > 0) AS total_students
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
                    (SELECT count(ct.id) FROM contracts ct LEFT JOIN students s ON ct.student_id = s.id WHERE ct.class_id = c.id AND ct.status != 7 AND s.status > 0) AS total_students
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
            $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%' OR c.code LIKE '%$keyword%') ";
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
            $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%') ";
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
            $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%') ";
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
}
