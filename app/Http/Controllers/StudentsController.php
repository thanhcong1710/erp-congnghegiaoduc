<?php

namespace App\Http\Controllers;

use App\Models\LogParents;
use App\Models\LogStudents;
use App\Providers\UtilityServiceProvider as u;
use App\Providers\CurlServiceProvider as curl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentsController extends Controller
{
    public function add(Request $request)
    {
        if ($request->id > 0) {
            $pre_student_info = u::first("SELECT * FROM crm_students WHERE id= $request->id");
            $data_update = array(
                'parent_id' => $request->parent_id,
                'name' => $request->name,
                'gender' => $request->gender,
                'birthday' => $request->birthday,
                'school_level' => $request->school_level,
                'school' => $request->school,
                'note' => $request->note,
                'updated_at' => date('Y-m-d H:i:s'),
                'updator_id' => Auth::user()->id,
            );
            u::updateSimpleRow($data_update, array('id' => $request->id), 'crm_students');
            LogParents::logUpdateStudentInfo($pre_student_info, $data_update, Auth::user()->id);
            $result = (object) array(
                'status' => 1,
                'message' => 'Cập nhật học sinh thành công'
            );
            return response()->json($result);
        } else {
            $data = u::insertSimpleRow(array(
                'parent_id' => $request->parent_id,
                'name' => $request->name,
                'gender' => $request->gender,
                'birthday' => $request->birthday,
                'school_level' => $request->school_level,
                'school' => $request->school,
                'note' => $request->note,
                'created_at' => date('Y-m-d H:i:s'),
                'creator_id' => Auth::user()->id,
            ), 'crm_students');
            $content = "Thêm mới học sinh: $request->name (ID: $data)";
            LogParents::logAdd($request->parent_id, $content, Auth::user()->id);
            $result = (object) array(
                'status' => 1,
                'message' => 'Thêm mới học sinh thành công'
            );
            return response()->json($result);
        }
    }
    public function getAllDataByParent($parent_id)
    {
        $data = u::query("SELECT s.*, (SELECT name FROM users WHERE id=s.creator_id) AS creator_name,
                (SELECT name FROM branches WHERE id=s.checkin_branch_id) AS checkin_branch_name,
                (SELECT name FROM products WHERE id=s.type_product) AS checkin_product_name
            FROM crm_students AS s WHERE s.parent_id=$parent_id ORDER BY s.id DESC");
        return response()->json($data);
    }
    public function checkin(Request $request)
    {
        $data_update = array(
            'checkin_at' => $request->checkin_at,
            'checkin_owner_id' => $request->owner_id,
            'checkin_branch_id' => $request->branch_id,
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => Auth::user()->id,
            'type_product' => $request->type_product,
            'status' => 1, // 
        );
        u::updateSimpleRow($data_update, array('id' => $request->student_id), 'crm_students');
        $result = (object) array(
            'status' => 1,
            'message' => 'Lưu thông tin checkin thành công'
        );
        return response()->json($result);
    }

    //LMS
    public function list(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $status_filter = isset($request->status) ? $request->status : [];
        $keyword = isset($request->keyword) ? $request->keyword : '';

        $pagination = (object) $request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);

        $cond = " s.status > 0 ";
        $cond .= " AND s.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";

        if (!empty($branch_id)) {
            $cond .= " AND s.branch_id IN (" . implode(",", $branch_id) . ")";
        }
        $role_ids = u::query("SELECT role_id FROM role_has_user WHERE user_id = " . Auth::user()->id);
        $roles = array_map(function($r) { return $r->role_id; }, $role_ids);
        
        $is_exact_phone = false;
        if ($keyword !== '' && preg_match('/^[0-9]{10,11}$/', $keyword)) {
            $is_exact_phone = true;
        }

        if (in_array(68, $roles) || in_array(69, $roles)) {
            if ($is_exact_phone) {
                $cond .= " AND (s.gud_mobile1 = '$keyword' OR s.gud_mobile2 = '$keyword') ";
            } else {
                $cond .= " AND t.ec_id IN (" . Auth::user()->getStaffHasUser() . ")";
                if ($keyword !== '') {
                    $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%' OR s.gud_name1 LIKE '%$keyword%' OR s.gud_mobile1 LIKE '%$keyword%' OR s.gud_mobile2 LIKE '%$keyword%') ";
                }
            }
        } else {
            if ($keyword !== '') {
                $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%' OR s.gud_name1 LIKE '%$keyword%' OR s.gud_mobile1 LIKE '%$keyword%' OR s.gud_mobile2 LIKE '%$keyword%') ";
            }
        }

        // Nếu có filter theo status, thêm điều kiện dựa trên logic ưu tiên
        if (!empty($status_filter)) {
            $status_conditions = [];

            foreach ($status_filter as $sf) {
                if ($sf == 1) { // Chưa đóng phí
                    $status_conditions[] = "
                        (NOT EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status = 6 AND status NOT IN (0, 8))
                        AND NOT EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status IN (3,4,5) AND status NOT IN (0, 8))
                        AND NOT EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status = 2 AND status NOT IN (0, 8))
                        AND EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status = 1 AND status NOT IN (0, 8)))
                    ";
                } elseif ($sf == 2) { // Đang học (status = 6)
                    $status_conditions[] = "
                        EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status = 6 AND status NOT IN (0, 8))
                    ";
                } elseif ($sf == 3) { // Hết phí (status = 7)
                    $status_conditions[] = "
                        (NOT EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status = 6 AND status NOT IN (0, 8))
                        AND NOT EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status IN (3,4,5) AND status NOT IN (0, 8))
                        AND NOT EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status = 2 AND status NOT IN (0, 8))
                        AND NOT EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status = 1 AND status NOT IN (0, 8))
                        AND EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status = 7 AND status NOT IN (0, 8)))
                    ";
                } elseif ($sf == 4) { // Đặt cọc (status = 2)
                    $status_conditions[] = "
                        (NOT EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status = 6 AND status NOT IN (0, 8))
                        AND NOT EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status IN (3,4,5) AND status NOT IN (0, 8))
                        AND EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status = 2 AND status NOT IN (0, 8)))
                    ";
                } elseif ($sf == 5) { // Chờ xếp lớp (status = 3,4,5)
                    $status_conditions[] = "
                        (NOT EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status = 6 AND status NOT IN (0, 8))
                        AND EXISTS (SELECT 1 FROM contracts WHERE student_id = s.id AND status IN (3,4,5) AND status NOT IN (0, 8)))
                    ";
                }
            }

            if (!empty($status_conditions)) {
                $cond .= " AND (" . implode(" OR ", $status_conditions) . ")";
            }
        }

        $limitation = $limit > 0 ? " LIMIT $offset, $limit" : "";
        $order_by = " ORDER BY s.id DESC ";

        // Đếm tổng số học sinh
        $total = u::first("SELECT count(s.id) AS total FROM students AS s LEFT JOIN term_student_user AS t ON t.student_id=s.id WHERE $cond");
        $total_count = $total->total;

        // Query danh sách học sinh với pagination
        $list = u::query("SELECT s.name, s.id, s.lms_code, s.gender, s.date_of_birth, s.gud_name1, s.gud_mobile1, s.avatar_url,
                (SELECT name FROM sources WHERE id = s.source_id) AS source_name,
                (SELECT name FROM branches WHERE id = s.branch_id) AS branch_name
            FROM students AS s
                LEFT JOIN term_student_user AS t ON t.student_id=s.id 
            WHERE $cond $order_by $limitation");

        // Thêm thông tin contract và status cho mỗi học sinh
        foreach ($list as $k => $row) {
            // Lấy contract đại diện (ưu tiên contract đang học hoặc mới nhất)
            $contract = u::first("SELECT c.id, c.class_id, c.ec_id, c.cm_id, c.done_sessions, c.summary_sessions, c.type, c.status
                FROM contracts AS c
                WHERE c.student_id = {$row->id}
                AND c.status NOT IN (0, 8)
                ORDER BY 
                    CASE 
                        WHEN c.status = 6 THEN 1
                        WHEN c.status IN (3,4,5) THEN 2
                        WHEN c.status = 2 THEN 3
                        WHEN c.status = 1 THEN 4
                        WHEN c.status = 7 THEN 5
                        ELSE 6
                    END,
                    c.id DESC
                LIMIT 1");

            if ($contract) {
                $list[$k]->class_name = u::first("SELECT cls_name FROM classes WHERE id = " . (int) $contract->class_id)->cls_name ?? '';
                $list[$k]->ec_name = u::first("SELECT CONCAT(name, ' - ', hrm_id) AS ec_name FROM users WHERE id = " . (int) $contract->ec_id)->ec_name ?? '';
                $list[$k]->cm_name = u::first("SELECT CONCAT(name, ' - ', hrm_id) AS cm_name FROM users WHERE id = " . (int) $contract->cm_id)->cm_name ?? '';
                $list[$k]->done_sessions = $contract->done_sessions;
                $list[$k]->summary_sessions = $contract->summary_sessions;
                $list[$k]->type = $contract->type;
                $list[$k]->status = $contract->status;
            } else {
                $list[$k]->class_name = '';
                $list[$k]->ec_name = '';
                $list[$k]->cm_name = '';
                $list[$k]->done_sessions = 0;
                $list[$k]->summary_sessions = 0;
                $list[$k]->type = 0;
                $list[$k]->status = 0;
            }

            // Sử dụng hàm mới để gen status label
            $list[$k]->label_status = u::genLearningStatusByContracts($row->id);
        }

        $data = u::makingPagination($list, $total_count, $page, $limit);
        return response()->json($data);
    }

    public function show(Request $request, $student_id)
    {
        $data = u::first("SELECT s.*, c.init_total_charged, c.type AS contract_type, c.status AS contract_status,
                c.summary_sessions,c.done_sessions, c.left_sessions, c.total_charged,c.real_sessions,
                (SELECT name FROM branches WHERE id=t.branch_id) AS branch_name,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id =t.ec_id) AS ec_name,
                t.ec_id,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id =t.cm_id) AS cm_name, '' AS satus_label,
                (SELECT id FROM crm_parents WHERE student_id = s.id LIMIT 1) AS parent_id,
                '' AS left_amount
            FROM students AS s 
                LEFT JOIN contracts AS c ON c.student_id=s.id 
                LEFT JOIN term_student_user AS t ON t.student_id=s.id
            WHERE (c.count_recharge = 
                    IF((SELECT count(id) FROM contracts WHERe student_id=s.id AND status!=7)>0,
                        (SELECT min(count_recharge) FROM contracts WHERE status !=7 AND student_id =s.id),
                        (SELECT max(count_recharge) FROM contracts WHERE student_id =s.id)) 
                OR c.id IS NULL) AND s.id=$student_id");
        $data->status_label = u::genLearningStatusByContracts($data->id);
        if ($data->real_sessions > $data->done_sessions && $data->left_sessions) {
            $data->left_amount = round($data->total_charged * ($data->left_sessions - $data->done_sessions) / $data->left_sessions);
        } else {
            $data->left_amount = 0;
        }

        $data->read_only = 0;
        $role_ids = u::query("SELECT role_id FROM role_has_user WHERE user_id = " . Auth::user()->id);
        $roles = array_map(function($r) { return $r->role_id; }, $role_ids);
        if (in_array(68, $roles) || in_array(69, $roles)) {
            $staff_ids = explode(',', Auth::user()->getStaffHasUser());
            if (!in_array($data->ec_id, $staff_ids)) {
                $data->read_only = 1;
            }
        }

        return response()->json($data);
    }

    public function update(Request $request)
    {
        $role_ids = u::query("SELECT role_id FROM role_has_user WHERE user_id = " . Auth::user()->id);
        $roles = array_map(function($r) { return $r->role_id; }, $role_ids);
        if (in_array(68, $roles) || in_array(69, $roles)) {
            $ec_id = u::first("SELECT ec_id FROM term_student_user WHERE student_id = " . (int)$request->id)->ec_id ?? 0;
            $staff_ids = explode(',', Auth::user()->getStaffHasUser());
            if (!in_array($ec_id, $staff_ids)) {
                return response()->json((object) [
                    'status' => 0,
                    'message' => 'Bạn không có quyền cập nhật thông tin học sinh này!'
                ]);
            }
        }

        $pre_student_info = u::first("SELECT * FROM students WHERE id = $request->id");
        $arr_name = u::explodeName(data_get($request, 'name'));
        $data_update = array(
            'name' => $request->name,
            'firstname' => data_get($arr_name, 'firstname'),
            'midname' => data_get($arr_name, 'midname'),
            'lastname' => data_get($arr_name, 'lastname'),
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'province_id' => $request->province_id,
            'district_id' => $request->district_id,
            'address' => $request->address,
            'school' => $request->school,
            'gud_name1' => $request->gud_name1,
            'gud_email1' => $request->gud_email1,
            'gud_birth_day1' => $request->gud_birth_day1,
            'gud_job1' => $request->gud_job1,
            'gud_name2' => $request->gud_name2,
            'gud_email2' => $request->gud_email2,
            'gud_mobile2' => $request->gud_mobile2,
            'gud_birth_day2' => $request->gud_birth_day2,
            'note' => $request->note,
            'aspiration' => $request->aspiration,
            'gud_job2' => $request->gud_job2,
            'gud_mobile1' => $request->gud_mobile1,
            'point_toeic' => $request->point_toeic,
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => Auth::user()->id,
        );
        $data = u::updateSimpleRow($data_update, array('id' => $request->id), 'students');
        u::query("UPDATE crm_parents SET 
            name = '".addslashes($request->gud_name1)."',
            mobile_1 = '$request->gud_mobile1',
            email = '$request->gud_email1',
            address = '".addslashes($request->address)."',
            province_id = '".(int)$request->province_id."',
            district_id = '".(int)$request->district_id."',
            point_toeic = '".(int)$request->point_toeic."'
            WHERE student_id = $request->id");
        LogStudents::logUpdateInfo($pre_student_info, $data_update, Auth::user()->id);
        $result = (object) array(
            'status' => 1,
            'message' => 'Cập nhật học sinh thành công'
        );
        return response()->json($result);
    }

    public function searchContract(Request $request)
    {
        $keyword = $request->keyword;
        $data = u::query("SELECT p.name, p.mobile_1 AS gud_mobile1, p.email AS gud_email1, p.address, 
                p.id AS parent_id, CONCAT(p.name, ' - ', p.mobile_1) AS label, p.point_toeic,
                CONCAT(u.name,' - ', u.hrm_id) AS ec_name,
                (SELECT CONCAT(name,' - ', hrm_id) FROM users WHERE id = u.manager_id ) AS ec_leader_name,
                IF(t.branch_id IS NULL ,0, t.branch_id) AS branch_id, s.lms_code
            FROM crm_parents AS p 
                LEFT JOIN users AS u ON u.id = p.owner_id
                LEFT JOIN term_student_user AS t ON t.student_id = p.student_id
                LEFT JOIN students As s ON s.id = p.student_id
                WHERE p.name LIKE '%$keyword%' OR p.mobile_1 LIKE '%$keyword%'");
        return response()->json($data);
    }

    public function logs(Request $request)
    {
        $student_id = isset($request->student_id) ? $request->student_id : 0;
        $pagination = (object) $request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);
        $limitation = $limit > 0 ? " LIMIT $offset, $limit" : "";
        $cond = " l.status = 1 AND l.student_id = $student_id";

        $order_by = " ORDER BY l.id DESC ";

        $total = u::first("SELECT count(l.id) AS total FROM student_logs AS l WHERE $cond");

        $list = u::query("SELECT l.created_at, l.content, CONCAT(u.name, ' - ', u.hrm_id) AS creator_name 
            FROM student_logs AS l
            LEFT  JOIN users AS u ON u.id=l.creator_id
            WHERE $cond $order_by $limitation");
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function contracts(Request $request)
    {
        $student_id = isset($request->student_id) ? $request->student_id : 0;
        $list = u::query("SELECT c.created_at, c.code, c.total_sessions, c.bonus_sessions, c.debt_amount, 
                c.must_charge, c.init_tuition_fee_amount, '' AS label_status, c.status, c.type,
                c.enrolment_start_date, c.enrolment_last_date,
                (SELECT name FROM products WHERE id=c.product_id) AS product_name,
                (SELECT name FROM tuition_fee WHERE id=c.tuition_fee_id) AS tuition_fee_name,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id =c.creator_id) AS creator_name,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id =c.ec_id) AS ec_name,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id =c.cm_id) AS cm_name,
                (SELECT name FROM branches WHERE id =c.branch_id) AS branch_name, c.id,
                cl.cls_name 
            FROM contracts AS c
                LEFT JOIN classes AS cl ON cl.id=c.class_id
            WHERE c.status>0 AND c.student_id= $student_id ORDER BY c.count_recharge DESC");
        foreach ($list as $k => $row) {
            $list[$k]->label_status = u::genLearningStatusByContract($row->status);
        }
        return response()->json($list);
    }

    public function sessions(Request $request)
    {
        $student_id = isset($request->student_id) ? $request->student_id : 0;
        $contract_id = isset($request->contract_id) ? $request->contract_id : 0;
        if ($contract_id) {
            $contract_active = u::first("SELECT c.id, c.class_id, c.status, c.done_sessions, c.summary_sessions
            FROM contracts AS c WHERE id=$contract_id");
        } else {
            $contract_active = u::first("SELECT c.id, c.class_id, c.status, c.done_sessions, c.summary_sessions
                FROM contracts AS c 
                WHERE c.count_recharge = 
                    IF((SELECT count(id) FROM contracts WHERe student_id=$student_id AND status!=7)>0,
                        (SELECT min(count_recharge) FROM contracts WHERE status !=7 AND student_id =$student_id),
                        (SELECT max(count_recharge) FROM contracts WHERE student_id =$student_id))  AND c.student_id=$student_id");
        }

        if ($contract_active) {
            $done_sessions = u::query("SELECT s.class_date,  s.subject_stt, s.attendance_status, (SELECT cls_name FROM classes WHERE id=s.class_id) AS cls_name FROM schedule_has_student AS s 
                WHERE s.contract_id = $contract_active->id");
            $done_sessions_active = u::query("SELECT s.class_date,  s.subject_stt, s.attendance_status, (SELECT cls_name FROM classes WHERE id=s.class_id) AS cls_name FROM schedule_has_student AS s 
                WHERE s.contract_id = $contract_active->id AND s.status=1");
            $limit = $contract_active->summary_sessions - count($done_sessions_active);
            $limit = $limit > 0 ? $limit : 0;
            if ($contract_active->class_id) {
                $next_sessions = u::query("SELECT s.class_date,  s.subject_stt, (SELECT cls_name FROM classes WHERE id=s.class_id) AS cls_name FROM schedules AS s 
                    WHERE s.class_id = $contract_active->class_id AND s.class_date > CURRENT_DATE ORDER BY s.class_date LIMIT $limit");
            }
        }
        $result = [
            'next_sessions' => $next_sessions ?? [],
            'done_sessions' => $done_sessions ?? [],
            'contract_info' => $contract_active
        ];
        return response()->json($result);
    }

    public function assessments(Request $request)
    {
        $student_id = isset($request->student_id) ? $request->student_id : 0;
        $pagination = (object) $request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);
        $limitation = $limit > 0 ? " LIMIT $offset, $limit" : "";
        $cond = " a.status = 1 AND a.student_id = $student_id";

        $order_by = " ORDER BY a.id DESC ";

        $total = u::first("SELECT count(a.id) AS total FROM assessments AS a WHERE $cond");

        $list = u::query("SELECT a.created_at, a.description, a.title, CONCAT(u.name, ' - ', u.hrm_id) AS creator_name 
            FROM assessments AS a
            LEFT  JOIN users AS u ON u.id=a.creator_id
            WHERE $cond $order_by $limitation");
        foreach ($list as $k => $row) {
            $list[$k]->description = nl2br($row->description);
        }
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function operating(Request $request)
    {
        $student_id = isset($request->student_id) ? $request->student_id : 0;
        $class_transfer = u::query("SELECT s.lms_code , s.name, c.transfer_date ,
                (SELECT cls_name FROM classes WHERE id=c.from_class_id) AS from_class_name,
                (SELECT cls_name FROM classes WHERE id=c.to_class_id) AS to_class_name,
                (SELECT name FROM branches WHERE id=c.from_branch_id) AS from_branch_name, c.status
            FROM class_transfer AS c LEFT JOIN students AS s ON s.id=c.student_id
            WHERE c.status>0 AND c.student_id= $student_id ORDER BY c.id DESC");
        $branch_transfer = u::query("SELECT s.lms_code , s.name, b.transfer_date ,b.status,
                (SELECT name FROM branches WHERE id=b.from_branch_id) AS from_branch_name,
                (SELECT name FROM branches WHERE id=b.to_branch_id) AS to_branch_name
            FROM branch_transfer AS b LEFT JOIN students AS s ON s.id=b.student_id
            WHERE b.status>0 AND b.student_id= $student_id ORDER BY b.id DESC");
        $tuition_transfer = u::query("SELECT t.transferred_amount ,t.transfer_date,t.status,
            (SELECT name FROM students WHERE id=t.from_student_id) AS from_student_name,
            (SELECT lms_code FROM students WHERE id=t.from_student_id) AS from_student_lms_code,
            (SELECT name FROM students WHERE id=t.to_student_id) AS to_student_name,
            (SELECT lms_code FROM students WHERE id=t.to_student_id) AS to_student_lms_code
        FROM tuition_transfer AS t 
        WHERE t.status>0 AND (t.from_student_id= $student_id OR t.to_student_id= $student_id) ORDER BY t.id DESC");

        return response()->json([
            'class_transfer' => $class_transfer,
            'branch_transfer' => $branch_transfer,
            'tuition_transfer' => $tuition_transfer
        ]);
    }

    public function uploadAvatar(Request $request)
    {
        $student_id = isset($_POST['student_id']) ? $_POST['student_id'] : '';
        $total = count($_FILES['files']['name']);
        if ($total > 0) {
            $tmpFilePath = $_FILES['files']['tmp_name'][0];
            if ($tmpFilePath != "") {
                $dir = __DIR__ . '/../../../public/static/upload/avatar_students/' . date('Y_m') . '/';
                if (!file_exists($dir)) {
                    mkdir($dir, 0755, true);
                }
                $newFilePath = $dir . $_FILES['files']['name'][0];
                $newFilePath = u::update_file_name($newFilePath);
                $dir_file_insert = str_replace(__DIR__ . '/../../../public', '', $newFilePath);
                move_uploaded_file($tmpFilePath, $newFilePath);
                u::updateSimpleRow(array(
                    'avatar_url' => $dir_file_insert
                ), array('id' => $student_id), 'students');
                return response()->json([
                    'status' => 1,
                    'message' => 'Upload avatar thành công.',
                ]);
            }
        }

        return response()->json([
            'status' => 0,
            'message' => 'Upload avatar thất bại vui lòng kiểm tra dung lượng và định dạng file.',
        ]);
    }

    public function payments(Request $request)
    {
        $student_id = data_get($request, 'student_id');
        $payments = u::query("SELECT p.*, (SELECT name FROM tuition_fee WHERE id=a.tuition_fee_id) AS tuition_fee_name
            FROM payments AS p 
                LEFT JOIN agreements AS a ON a.id=p.agreement_id
            WHERE p.student_id=$student_id ORDER BY p.id DESC");
        $agreements = u::query("SELECT c.id AS agreement_id,
                    (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.ec_id) AS ec_name,
                    c.code, (SELECT name FROM tuition_fee WHERE id=c.tuition_fee_id) AS tuition_fee_name,
                    c.must_charge, c.debt_amount, c.status, c.created_at, c.total_charged
                FROM agreements AS c 
                WHERE c.student_id =$student_id AND c.status > 0  AND c.must_charge > 0 AND c.debt_amount > 0 ");
        $reserves = u::query("SELECT c.id AS reserve_id,
                    (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.creator_id) AS creator_name,
                    c.must_charge, c.debt_amount, c.status, c.created_at
                FROM reserves AS c 
                WHERE c.student_id =$student_id AND c.status > 0  AND c.must_charge > 0 AND c.debt_amount > 0");

        return response()->json([
            'payments' => $payments,
            'agreements' => $agreements,
            'reserves' => $reserves
        ]);
    }
}
