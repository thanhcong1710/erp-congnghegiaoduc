<?php

namespace App\Http\Controllers;

use App\Models\LogParents;
use App\Models\LogStudents;
use App\Enums\SystemCode;
use App\Providers\UtilityServiceProvider as u;
use App\Providers\CurlServiceProvider as curl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentsController extends Controller
{
    public function add(Request $request)
    {
        $bd = u::parseBirthday($request->birthday, $request->birthday_year_only);
        if ($request->id > 0) {
            $pre_student_info = u::first("SELECT * FROM crm_students WHERE id= " . (int)$request->id);
            $data_update = array(
                'parent_id' => $request->parent_id,
                'name' => $request->name,
                'gender' => $request->gender,
                'birthday' => $bd['date'],
                'birthday_year_only' => $bd['year_only'],
                'school_level' => $request->school_level,
                'school' => $request->school,
                'note' => $request->note,
                'updated_at' => date('Y-m-d H:i:s'),
                'updator_id' => Auth::user()->id,
            );
            if (!empty($request->branch_id)) {
                $data_update['checkin_branch_id'] = (int)$request->branch_id;
            }
            u::updateSimpleRow($data_update, array('id' => $request->id), 'crm_students');
            LogParents::logUpdateStudentInfo($pre_student_info, $data_update, Auth::user()->id);

            // Đồng bộ sang bảng students nếu đã có lms_id
            if ($pre_student_info && !empty($pre_student_info->lms_id)) {
                $arr_name = u::explodeName($request->name);
                $lms_update = array(
                    'name' => $request->name,
                    'firstname' => data_get($arr_name, 'firstname'),
                    'midname' => data_get($arr_name, 'midname'),
                    'lastname' => data_get($arr_name, 'lastname'),
                    'gender' => $request->gender,
                    'date_of_birth' => $bd['date'],
                    'birthday_year_only' => $bd['year_only'],
                    'note' => $request->note,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updator_id' => Auth::user()->id,
                );
                if (!empty($request->branch_id)) {
                    $lms_update['branch_id'] = (int)$request->branch_id;
                    u::updateSimpleRow(array('branch_id' => (int)$request->branch_id), array('student_id' => $pre_student_info->lms_id), 'term_student_user');
                }
                u::updateSimpleRow($lms_update, array('id' => $pre_student_info->lms_id), 'students');
            } elseif ($pre_student_info) {
                // Nếu bản ghi cũ chưa có lms_id thì tự động nâng cấp thành chính thức luôn
                self::promoteToOfficialStudent($request->id, $request->branch_id, $request->owner_id);
            }

            $result = (object) array(
                'status' => 1,
                'message' => 'Cập nhật học sinh thành công'
            );
            return response()->json($result);
        } else {
            $exit = u::first("SELECT * FROM crm_students WHERE parent_id= " . (int)$request->parent_id . " AND name LIKE '%" . addslashes($request->name) . "%'");
            if ($exit) {
                $result = (object) array(
                    'status' => 0,
                    'message' => 'Học sinh đã tồn tại'
                );
            } else {
                $branch_id = !empty($request->branch_id) ? (int)$request->branch_id : 0;
                $crm_student_id = u::insertSimpleRow(array(
                    'parent_id' => $request->parent_id,
                    'name' => $request->name,
                    'gender' => $request->gender,
                    'birthday' => $bd['date'],
                    'birthday_year_only' => $bd['year_only'],
                    'school_level' => $request->school_level,
                    'school' => $request->school,
                    'note' => $request->note,
                    'checkin_branch_id' => $branch_id,
                    'status' => 3, // Lên chính thức ngay
                    'created_at' => date('Y-m-d H:i:s'),
                    'creator_id' => Auth::user()->id,
                ), 'crm_students');

                // Tự động thăng cấp thành học sinh chính thức LMS (bỏ qua bước check-in)
                $upgradeInfo = self::promoteToOfficialStudent($crm_student_id, $branch_id, $request->owner_id);

                $content = "Thêm mới học sinh chính thức: $request->name (LMS: " . data_get($upgradeInfo, 'lms_code') . ")";
                LogParents::logAdd($request->parent_id, $content, Auth::user()->id);

                $result = (object) array(
                    'status' => 1,
                    'message' => 'Thêm mới học sinh chính thức thành công',
                    'data' => $upgradeInfo
                );
            }

            return response()->json($result);
        }
    }

    public static function promoteToOfficialStudent($crm_student_id, $branch_id = null, $owner_id = null)
    {
        $crm_student_id = (int)$crm_student_id;
        $crm_student_info = u::first("SELECT * FROM crm_students WHERE id = $crm_student_id");
        if (!$crm_student_info) {
            return null;
        }

        $parent_id = (int)data_get($crm_student_info, 'parent_id');
        $crm_parent_info = $parent_id > 0 ? u::first("SELECT * FROM crm_parents WHERE id = $parent_id") : null;

        // Xác định branch_id
        if (!$branch_id) {
            $branch_id = (int)data_get($crm_student_info, 'checkin_branch_id');
        }
        if (!$branch_id && $crm_parent_info) {
            $branch_id = (int)data_get($crm_parent_info, 'branch_id') ?: (int)data_get($crm_parent_info, 'tmp_branch_id');
        }
        if (!$branch_id && $owner_id) {
            $user_branch = u::first("SELECT branch_id FROM branch_has_user WHERE user_id = " . (int)$owner_id);
            $branch_id = (int)data_get($user_branch, 'branch_id');
        }
        if (!$branch_id && $crm_parent_info && data_get($crm_parent_info, 'owner_id')) {
            $user_branch = u::first("SELECT branch_id FROM branch_has_user WHERE user_id = " . (int)data_get($crm_parent_info, 'owner_id'));
            $branch_id = (int)data_get($user_branch, 'branch_id');
        }
        if (!$branch_id && Auth::check()) {
            $user_branch = u::first("SELECT branch_id FROM branch_has_user WHERE user_id = " . Auth::user()->id);
            $branch_id = (int)data_get($user_branch, 'branch_id');
        }

        // Xác định owner_id (EC phụ trách)
        if (!$owner_id) {
            $owner_id = (int)data_get($crm_student_info, 'checkin_owner_id');
        }
        if (!$owner_id && $crm_parent_info) {
            $owner_id = (int)data_get($crm_parent_info, 'owner_id');
        }
        if (!$owner_id && Auth::check()) {
            $owner_id = Auth::user()->id;
        }

        $creator_id = Auth::check() ? Auth::user()->id : (int)data_get($crm_student_info, 'creator_id');
        $lms_student_id = (int)data_get($crm_student_info, 'lms_id');
        $arr_name = u::explodeName(data_get($crm_student_info, 'name'));

        if (!$lms_student_id) {
            $student_data = array(
                'lms_code' => '',
                'name' => data_get($crm_student_info, 'name'),
                'firstname' => data_get($arr_name, 'firstname'),
                'midname' => data_get($arr_name, 'midname'),
                'lastname' => data_get($arr_name, 'lastname'),
                'gender' => data_get($crm_student_info, 'gender'),
                'date_of_birth' => data_get($crm_student_info, 'birthday'),
                'birthday_year_only' => (int)data_get($crm_student_info, 'birthday_year_only'),
                'gud_mobile1' => data_get($crm_parent_info, 'mobile_1'),
                'gud_name1' => data_get($crm_parent_info, 'name'),
                'gud_email1' => data_get($crm_parent_info, 'email'),
                'address' => data_get($crm_parent_info, 'address'),
                'province_id' => data_get($crm_parent_info, 'province_id'),
                'district_id' => data_get($crm_parent_info, 'district_id'),
                'created_at' => date('Y-m-d H:i:s'),
                'creator_id' => $creator_id,
                'note' => data_get($crm_student_info, 'note'),
                'branch_id' => $branch_id ?: 0,
                'gud_birth_day1' => data_get($crm_parent_info, 'birthday'),
                'gud_gender1' => data_get($crm_parent_info, 'gender'),
                'gud_job1' => data_get($crm_parent_info, 'job_id'),
                'status' => 1,
                'source_detail_id' => data_get($crm_parent_info, 'source_detail_id'),
                'source_id' => data_get($crm_parent_info, 'source_id'),
                'c2c_mobile' => data_get($crm_parent_info, 'c2c_mobile'),
                'avatar_url' => data_get($crm_student_info, 'gender') == 'F' ? '/images/common/avatar-girl.svg' : '/images/common/avatar-boy.svg',
            );
            $lms_student_id = u::insertSimpleRow($student_data, 'students');

            $last_lms_code = str_pad((string)$lms_student_id, 6, '0', STR_PAD_LEFT);
            $lms_code = config('app.prefix_student_code') . $last_lms_code;
            u::updateSimpleRow(array('lms_code' => $lms_code), array('id' => $lms_student_id), 'students');

            $ceo_info = $branch_id ? u::first("SELECT u.id FROM role_has_user AS ru 
                LEFT JOIN roles AS r ON r.id = ru.role_id
                LEFT JOIN users AS u ON u.id = ru.user_id
                LEFT JOIN branch_has_user AS b ON b.user_id = u.id
                WHERE u.status=1 AND r.code ='" . SystemCode::ROLE_CEO_BRANCH . "' AND b.branch_id=" . (int)$branch_id) : null;
            $ec_info = $owner_id ? u::first("SELECT u.id, u.manager_id FROM users AS u WHERE u.status=1 AND u.id = " . (int)$owner_id) : null;

            u::insertSimpleRow(array(
                'student_id' => $lms_student_id,
                'ec_id' => data_get($ec_info, 'id'),
                'branch_id' => $branch_id ?: 0,
                'ceo_branch_id' => data_get($ceo_info, 'id'),
                'ec_leader_id' => data_get($ec_info, 'manager_id'),
                'created_at' => date('Y-m-d H:i:s'),
                'creator_id' => $creator_id,
                'status' => 1
            ), 'term_student_user');

            LogStudents::logAdd($lms_student_id, 'Khởi tạo học sinh chính thức', $creator_id);
        } else {
            $last_lms = u::first("SELECT lms_code FROM students WHERE id = $lms_student_id");
            $lms_code = data_get($last_lms, 'lms_code');
        }

        // Cập nhật lại crm_students thành status = 3 (Chính thức)
        u::updateSimpleRow(array(
            'status' => 3,
            'lms_id' => $lms_student_id,
            'checkin_branch_id' => $branch_id ?: 0,
            'checkin_owner_id' => $owner_id ?: 0,
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => $creator_id,
        ), array('id' => $crm_student_id), 'crm_students');

        return [
            'crm_student_id' => $crm_student_id,
            'lms_student_id' => $lms_student_id,
            'lms_code' => $lms_code,
            'branch_id' => $branch_id,
        ];
    }
    public function getAllDataByParent($parent_id)
    {
        $parent_id = (int)$parent_id;
        $data = u::query("SELECT s.*, 
                (SELECT name FROM users WHERE id=s.creator_id) AS creator_name,
                (SELECT name FROM branches WHERE id=s.checkin_branch_id) AS checkin_branch_name,
                (SELECT name FROM products WHERE id=s.type_product) AS checkin_product_name,
                (SELECT lms_code FROM students WHERE id=s.lms_id) AS lms_code
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
        $is_update = isset($request->is_update) ? (int) $request->is_update : 0;
        $lastCheckin = u::first("SELECT id FROM crm_student_checkin WHERE crm_student_id = $request->student_id ORDER BY id DESC");
        if ($is_update && $lastCheckin) {
            u::updateSimpleRow(array(
                'checkin_at' => $request->checkin_at,
                'checkin_owner_id' => $request->owner_id,
                'checkin_branch_id' => $request->branch_id,
                'updated_at' => date('Y-m-d H:i:s'),
                'updator_id' => Auth::user()->id,
                'type_product' => $request->type_product,
            ), array('id' => data_get($lastCheckin, 'id')), 'crm_student_checkin');
        } else {
            u::insertSimpleRow(array(
                'crm_student_id' => $request->student_id,
                'checkin_at' => $request->checkin_at,
                'checkin_owner_id' => $request->owner_id,
                'checkin_branch_id' => $request->branch_id,
                'created_at' => date('Y-m-d H:i:s'),
                'creator_id' => Auth::user()->id,
                'type_product' => $request->type_product,
                'status' => 1, // 
            ), 'crm_student_checkin');
        }
        $result = (object) array(
            'status' => 1,
            'message' => 'Lưu thông tin checkin thành công'
        );
        return response()->json($result);
    }

    //LMS
    public function list(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : (isset($request->arr_branch) ? $request->arr_branch : []);
        $status = isset($request->status) ? $request->status : (isset($request->arr_status) ? $request->arr_status : []);
        $keyword = isset($request->keyword) ? $request->keyword : '';

        $pagination = (object) $request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);
        $limitation = $limit > 0 ? " LIMIT $offset, $limit" : "";
        $cond = " s.status > 0 ";
        $cond .= " AND s.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";
        $saleStaffIds = Auth::user()->getSaleAllowedStaffIds();
        if (!empty($saleStaffIds)) {
            $cond .= " AND (EXISTS (SELECT 1 FROM term_student_user tsu WHERE tsu.student_id = s.id AND (tsu.ec_id IN ($saleStaffIds) OR tsu.ec_leader_id IN ($saleStaffIds)))
                OR EXISTS (SELECT 1 FROM contracts ct WHERE ct.student_id = s.id AND (ct.ec_id IN ($saleStaffIds) OR ct.creator_id IN ($saleStaffIds)))
                OR EXISTS (SELECT 1 FROM crm_students cs JOIN crm_parents cp ON cp.id = cs.parent_id WHERE cs.lms_id = s.id AND cp.owner_id IN ($saleStaffIds))
                OR s.creator_id IN ($saleStaffIds)) ";
        }

        if (!empty($status)) {
            $cond_status = "";
            foreach ($status as $s) {
                if ($s == 1) {
                    $qu = " (c.type=0 AND c.status NOT IN(6,7)) ";
                } elseif ($s == 2) {
                    $qu = " (c.type=0 AND c.status =6) ";
                } elseif ($s == 3) {
                    $qu = " (c.type=0 AND c.status =7) ";
                } elseif ($s == 4) {
                    $qu = " (c.type=1 AND c.status =1) ";
                } elseif ($s == 5) {
                    $qu = " (c.type=1 AND c.status =2) ";
                } elseif ($s == 6) {
                    $qu = " (c.type=1 AND c.status IN (3,4,5)) ";
                } elseif ($s == 7) {
                    $qu = " (c.type=1 AND c.status =6) ";
                } elseif ($s == 8) {
                    $qu = " (c.type=1 AND c.status =8) ";
                }
                $cond_status .= $cond_status ? " OR " . $qu : $qu;
            }
            $cond .= " AND ($cond_status)";
        }
        if (!empty($branch_id)) {
            $cond .= " AND s.branch_id IN (" . implode(",", $branch_id) . ")";
        }

        if ($keyword !== '') {
            $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.lms_id LIKE '%$keyword%' OR s.name LIKE '%$keyword%' OR s.gud_name1 LIKE '%$keyword%' OR s.gud_mobile1 LIKE '%$keyword%' OR s.gud_mobile2 LIKE '%$keyword%') ";
        }

        $order_by = " ORDER BY s.id DESC ";

        $total = u::first("SELECT count(s.id) AS total FROM students AS s 
            LEFT JOIN contracts AS c ON c.student_id=s.id AND c.count_recharge = (SELECT min(count_recharge) FROM contracts WHERE status !=7 AND student_id =s.id) WHERE $cond");

        $list = u::query("SELECT s.name, s.id, s.lms_code, s.gender, s.date_of_birth, s.birthday_year_only, s.gud_name1, s.gud_mobile1, s.avatar_url,s.lms_id,
                (SELECT name FROM sources WHERE id =s.source_id) AS source_name,
                (SELECT cls_name FROM classes WHERE id = c.class_id) AS class_name,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id =c.ec_id) AS ec_name,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id =c.cm_id) AS cm_name,
                (SELECT name FROM branches WHERE id =s.branch_id) AS branch_name, c.done_sessions, c.summary_sessions, c.type, c.status, '' AS label_status
            FROM students AS s
            LEFT JOIN contracts AS c ON c.student_id=s.id AND c.status!=0
            WHERE (c.count_recharge = 
                    IF((SELECT count(id) FROM contracts WHERe student_id=s.id AND status!=7)>0,
                        (SELECT min(count_recharge) FROM contracts WHERE status !=7 AND student_id =s.id),
                        (SELECT max(count_recharge) FROM contracts WHERE student_id =s.id)) 
                OR c.id IS NULL) 
                AND $cond $order_by $limitation");
        foreach ($list as $k => $row) {
            $list[$k]->label_status = u::genStatusStudent($row->status, $row->type);
        }
        $total_count = $total ? (int)$total->total : 0;
        $data = u::makingPagination($list, $total_count, $page, $limit);
        $data->total = $total_count;
        return response()->json($data);
    }

    public function show(Request $request, $student_id)
    {
        $student_id = (int)$student_id;
        $saleCond = "";
        $saleStaffIds = Auth::user()->getSaleAllowedStaffIds();
        if (!empty($saleStaffIds)) {
            $saleCond = " AND (t.ec_id IN ($saleStaffIds) OR t.ec_leader_id IN ($saleStaffIds) OR c.ec_id IN ($saleStaffIds) OR s.creator_id IN ($saleStaffIds)
                OR EXISTS (SELECT 1 FROM crm_students cs JOIN crm_parents cp ON cp.id = cs.parent_id WHERE cs.lms_id = s.id AND cp.owner_id IN ($saleStaffIds))) ";
        }
        $data = u::first("SELECT s.*, c.init_total_charged, c.type AS contract_type, c.status AS contract_status,
                c.summary_sessions,c.done_sessions, c.left_sessions, c.total_charged,c.real_sessions,
                (SELECT name FROM branches WHERE id=t.branch_id) AS branch_name,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id =t.ec_id) AS ec_name, t.ec_id, t.cm_id,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id =t.cm_id) AS cm_name, '' AS satus_label,
                '' AS left_amount
            FROM students AS s 
                LEFT JOIN contracts AS c ON c.student_id=s.id 
                LEFT JOIN term_student_user AS t ON t.student_id=s.id
            WHERE (c.count_recharge = 
                    IF((SELECT count(id) FROM contracts WHERe student_id=s.id AND status!=7)>0,
                        (SELECT min(count_recharge) FROM contracts WHERE status !=7 AND student_id =s.id),
                        (SELECT max(count_recharge) FROM contracts WHERE student_id =s.id)) 
                OR c.id IS NULL) AND s.id=$student_id $saleCond");
        if ($data) {
            $data->status_label = u::genStatusStudent($data->contract_status, $data->contract_type);
            if ($data->real_sessions > $data->done_sessions) {
                $data->left_amount = $data->left_sessions ? round($data->total_charged * ($data->left_sessions - $data->done_sessions) / $data->left_sessions) : 0;
            } else {
                $data->left_amount = 0;
            }
        }

        return response()->json($data);
    }

    public function update(Request $request)
    {
        $pre_student_info = u::first("SELECT * FROM students WHERE id = $request->id");
        $arr_name = u::explodeName(data_get($request, 'name'));
        $bd = u::parseBirthday($request->date_of_birth, $request->birthday_year_only);
        $data_update = array(
            'name' => $request->name,
            'firstname' => data_get($arr_name, 'firstname'),
            'midname' => data_get($arr_name, 'midname'),
            'lastname' => data_get($arr_name, 'lastname'),
            'date_of_birth' => $bd['date'],
            'birthday_year_only' => $bd['year_only'],
            'gender' => $request->gender,
            'province_id' => $request->province_id,
            'district_id' => $request->district_id,
            'address' => $request->address,
            'school' => $request->school,
            'school_grade' => $request->school_grade,
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
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => Auth::user()->id,
        );
        if (data_get($request, 'lms_id')) {
            $data_update['lms_id'] = data_get($request, 'lms_id');
        }
        $data = u::updateSimpleRow($data_update, array('id' => $request->id), 'students');
        $ec_id = data_get($request, 'ec_id');
        $ec_info = u::first("SELECT u.id, u.manager_id FROM users AS u WHERE u.status=1 AND u.id = " . (int) $ec_id);
        $cm_id = data_get($request, 'cm_id');
        $cm_info = u::first("SELECT u.id, u.manager_id FROM users AS u WHERE u.status=1 AND u.id = " . (int) $cm_id);
        u::updateSimpleRow([
            'ec_id' => data_get($ec_info, 'id'),
            'ec_leader_id' => data_get($ec_info, 'manager_id'),
            'cm_id' => data_get($cm_info, 'id'),
            'cm_leader_id' => data_get($cm_info, 'manager_id'),
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => Auth::user()->id,
        ], array('student_id' => $request->id), 'term_student_user');
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
        $branch_id = (int)$request->branch_id;
        $student_id = isset($request->student_id) ? (int)$request->student_id : 0;
        $cond = "";
        if ($student_id > 0) {
            $where = " s.id = $student_id ";
            if ($branch_id > 0) {
                $where .= " AND t.branch_id = $branch_id ";
            }
        } else {
            $where = " t.branch_id = $branch_id AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%' OR s.gud_name1 LIKE '%$keyword%' OR s.gud_mobile1 LIKE '%$keyword%' OR s.gud_mobile2 LIKE '%$keyword%') ";
        }
        $saleStaffIds = Auth::user()->getSaleAllowedStaffIds();
        if (!empty($saleStaffIds)) {
            $cond .= " AND (t.ec_id IN ($saleStaffIds) OR t.ec_leader_id IN ($saleStaffIds) OR s.creator_id IN ($saleStaffIds)
                OR EXISTS (SELECT 1 FROM crm_students cs JOIN crm_parents cp ON cp.id = cs.parent_id WHERE cs.lms_id = s.id AND cp.owner_id IN ($saleStaffIds))) ";
        }
        $data = u::query("SELECT s.name, s.lms_code, s.gud_name1, s.gud_mobile1, s.gud_email1, s.address,
                (SELECT name FROM branches WHERE id =t.branch_id) AS student_branch_name,
                t.branch_id AS student_branch_id,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id =t.ec_id) AS ec_name,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id =t.ec_leader_id) AS ec_leader_name,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id =t.ceo_branch_id) AS ceo_branch_name, 
                s.id AS student_id, CONCAT(s.name, ' - ', s.lms_code, IF(s.gud_mobile1 IS NOT NULL AND s.gud_mobile1 != '', CONCAT(' - ', s.gud_mobile1), '')) AS label,
                (SELECT count(id) FROM students WHERE gud_mobile1 = s.gud_mobile1 AND s.id != id) AS count_sibling,
                (SELECT start_date FROM contracts WHERE student_id=s.id AND status!=0 ORDER BY id DESC LIMIT 1) AS last_start_date,
                (SELECT count(id) FROM contracts WHERE student_id=s.id AND  (type=0 AND status!=7)) AS disabled_trial,
                (SELECT enrolment_last_date FROM contracts WHERE student_id=s.id AND type=0 AND status=7 ORDER BY id DESC LIMIT 1) AS trial_enrolment_last_date,
                (SELECT enrolment_start_date FROM contracts WHERE student_id=s.id AND type=0 AND status=7 ORDER BY id DESC LIMIT 1) AS trial_enrolment_start_date
            FROM students AS s LEFT JOIN term_student_user AS t ON t.student_id=s.id AND t.status=1 
                WHERE $where $cond");
        foreach ($data as $k => $row) {
            if ($data[$k]->trial_enrolment_last_date && (strtotime($data[$k]->trial_enrolment_last_date) > strtotime('-3 month', strtotime(date('Y-m-d'))))) {
                $data[$k]->disabled_trial = 1;
            } elseif ($data[$k]->trial_enrolment_start_date && (strtotime($data[$k]->trial_enrolment_start_date) > strtotime('-3 month', strtotime(date('Y-m-d'))))) {
                $data[$k]->disabled_trial = 1;
            }
        }
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
        $list = u::query("SELECT c.created_at, c.code, c.total_sessions, c.bonus_sessions, c.debt_amount, c.id AS contract_id, 
                c.must_charge, c.init_tuition_fee_amount, '' AS label_status, c.status, c.type, c.total_charged, c.summary_sessions,
                (SELECT name FROM products WHERE id=c.product_id) AS product_name,
                (SELECT name FROM tuition_fee WHERE id=c.tuition_fee_id) AS tuition_fee_name,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id =c.creator_id) AS creator_name,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id =c.ec_id) AS ec_name,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id =c.cm_id) AS cm_name,
                (SELECT name FROM branches WHERE id =c.branch_id) AS branch_name,
                (SELECT cls_name FROM classes WHERE id =c.class_id) AS class_name,
                c.reserved_sessions, c.reservable_sessions,
                (SELECT class_date FROM schedule_has_student WHERE contract_id =c.id ORDER BY class_date LIMIT 1 ) AS enrolment_start_date, c.enrolment_last_date
            FROM contracts AS c
            WHERE c.status>0 AND c.student_id= $student_id ORDER BY c.count_recharge DESC");
        foreach ($list as $k => $row) {
            $list[$k]->label_status = u::genStatusStudent($row->status, $row->type);
        }
        return response()->json($list);
    }

    public function sessions(Request $request)
    {
        $student_id = (int)data_get($request, 'student_id', 0);
        $contract_active = u::first("SELECT c.id, c.class_id, c.status, c.done_sessions, c.summary_sessions
            FROM contracts AS c 
            WHERE c.count_recharge = 
                IF((SELECT count(id) FROM contracts WHERe student_id=$student_id AND status!=7)>0,
                    (SELECT min(count_recharge) FROM contracts WHERE status !=7 AND student_id =$student_id),
                    (SELECT max(count_recharge) FROM contracts WHERE student_id =$student_id))  AND c.student_id=$student_id");
        if ($contract_active) {
            $done_sessions = u::query("SELECT s.class_date, sj.code, s.subject_stt, s.status, s.attendance_status, (SELECT cls_name FROM classes WHERE id=s.class_id) AS cls_name FROM schedule_has_student AS s LEFT JOIN subjects AS sj ON s.subject_id = sj.id 
                WHERE s.contract_id = $contract_active->id ORDER BY s.class_date, s.id");
            $limit = $contract_active->summary_sessions - count($done_sessions);
            $limit = $limit > 0 ? $limit : 0;
            if ($contract_active->class_id) {
                $next_sessions = u::query("SELECT s.class_date, sj.code, s.subject_stt, (SELECT cls_name FROM classes WHERE id=s.class_id) AS cls_name FROM schedules AS s LEFT JOIN subjects AS sj ON s.subject_id = sj.id
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
        $student_id = isset($_POST['student_id']) ? $_POST['student_id'] : ($request->student_id ? $request->student_id : '');
        $files = isset($_FILES['files']) ? $_FILES['files'] : (isset($_FILES['file']) ? $_FILES['file'] : null);
        if ($files && isset($files['name'])) {
            $names = is_array($files['name']) ? $files['name'] : [$files['name']];
            $tmp_names = is_array($files['tmp_name']) ? $files['tmp_name'] : [$files['tmp_name']];
            if (count($names) > 0 && !empty($tmp_names[0])) {
                $dir = public_path('static/upload/avatar_students/' . date('Y_m') . '/');
                if (!file_exists($dir)) {
                    mkdir($dir, 0775, true);
                }
                $newFilePath = $dir . $names[0];
                $newFilePath = u::update_file_name($newFilePath);
                $dir_file_insert = str_replace(public_path(), '', $newFilePath);
                move_uploaded_file($tmp_names[0], $newFilePath);
                u::updateSimpleRow(array(
                    'avatar_url' => $dir_file_insert
                ), array('id' => $student_id), 'students');
                return response()->json([
                    'status' => 1,
                    'message' => 'Upload avatar thành công.',
                    'avatar_url' => $dir_file_insert
                ]);
            }
        }

        return response()->json([
            'status' => 0,
            'message' => 'Upload avatar thất bại vui lòng kiểm tra dung lượng và định dạng file.',
        ]);
    }
    public function getFile(Request $request)
    {
        $student_id = isset($request->student_id) ? $request->student_id : 0;
        $data = u::query("SELECT *
            FROM file_has_student
            WHERE status=1 AND student_id = $student_id ORDER BY id DESC");
        return response()->json($data);
    }
    public function uploadFile(Request $request)
    {
        // $request->validate([
        //     'file' => 'required|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx,xlsx,xls',
        //     'student_id' => 'required|integer',
        //     'note' => 'nullable|string',
        // ]);

        $file = $request->file('file');
        $student_id = $request->input('student_id');
        $note = $request->input('note');

        // Tạo thư mục lưu file
        $dir = public_path('static/upload/student_files/' . date('Y_m'));
        if (!file_exists($dir)) {
            mkdir($dir, 0775, true);
        }

        // Đặt tên file mới (tránh trùng tên)
        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $file->getClientOriginalName());
        $file->move($dir, $filename);

        // Lưu đường dẫn tương đối (bỏ phần public)
        $dir_file_insert = str_replace(public_path(), '', $dir . '/' . $filename);

        // Lưu vào DB
        u::insertSimpleRow([
            'student_id' => $student_id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $dir_file_insert,
            'note' => $note,
            'created_at' => now(),
            'creator_id' => Auth::id(),
            'status' => 1
        ], 'file_has_student');

        return response()->json([
            'status' => 1,
            'message' => 'Upload file thành công.',
        ]);
    }
    public function deleteFile(Request $request)
    {
        $file_id = $request->id ? $request->id : $request->file_id;
        u::updateSimpleRow(array(
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => Auth::user()->id,
            'status' => 0
        ), ['id' => $file_id], 'file_has_student');
        return response()->json([
            'status' => 1,
            'message' => 'Xoá file thành công.',
        ]);
    }
    public function validateLMS(Request $request)
    {
        $student_id = $request->student_id;
        $lms_id = $request->lms_id;
        if ($student_id && $lms_id) {
            $check = u::first("SELECT id FROM students WHERE  lms_id='$lms_id' && id != $student_id");
            if ($check) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Mã học sinh đã tồn tại trong hệ thống.'
                ]);
            }
        }
        return response()->json([
            'status' => 1,
            'message' => 'Mã học sinh hợp lệ.'
        ]);
    }
}
