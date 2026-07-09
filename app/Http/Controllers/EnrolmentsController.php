<?php

namespace App\Http\Controllers;

use App\Enums\SystemCode;
use App\User;
use App\Http\Controllers\Controller;
use App\Models\LogStudents;
use App\Models\LogClassStudent;
use App\Providers\UtilityServiceProvider as u;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrolmentsController extends Controller
{
    public function loadClasses(Request $request)
    {
        $data = [];
        $branch_id = (int) $request->branch_id;
        $product_id = (int) $request->product_id;
        $status_filter = isset($request->class_status) ? (int) $request->class_status : 0;
        
        $cond = " c.branch_id = $branch_id AND c.product_id = $product_id AND c.status = 1 ";
        if ($status_filter == 1) { 
            $cond .= " AND c.cls_startdate > CURDATE() ";
        } elseif ($status_filter == 2) { 
            $cond .= " AND c.cls_startdate <= CURDATE() AND c.cls_enddate >= CURDATE() ";
        } elseif ($status_filter == 3) { 
            $cond .= " AND c.cls_enddate < CURDATE() ";
        }
        
        $query = "SELECT CONCAT(999, c.id) AS id, 
            c.id AS item_id, 
            'class' AS item_type, 
            c.cls_name,
            CONCAT('<b style=\"font-size: 16px\">', c.cls_name, '</b> (', 
                IF((SELECT COUNT(id) FROM contracts WHERE class_id = c.id ) >= c.max_students, 
                    CONCAT('<b style=\"color:red\">', (SELECT COUNT(id) FROM contracts WHERE class_id = c.id ), '</b>/', c.max_students),
                    CONCAT('<b>', (SELECT COUNT(id) FROM contracts WHERE class_id = c.id ), '</b>/', c.max_students)
                ), 
                ' <i class=\"fa-regular fa-calendar mx-1 text-primary\"></i>', c.cls_startdate, 
            ')') AS `text`, 
            0 AS parent_id, 
            IF(c.cm_id > 0, 
                IF(c.status = 0, 
                    'fa-regular fa-rectangle-xmark fa-fw', 
                    IF((SELECT COUNT(u.id) FROM users u LEFT JOIN sessions s ON u.id = s.teacher_id WHERE u.status > 0 AND s.class_id = c.id) > 0, 'fa-solid fa-file-lines fa-fw', 'fa-solid fa-triangle-exclamation fa-fw')), 'fa-solid fa-user-xmark fa-fw') AS icon, 
            c.status 
        FROM classes AS c 
        WHERE $cond 
            ORDER BY REGEXP_REPLACE(c.cls_name, '[0-9]+$', '') DESC, CAST(REGEXP_REPLACE(c.cls_name, '^[^0-9]+', '') AS UNSIGNED) DESC ";
        $class = u::query($query);
        if (count($class)) {
            foreach ($class as $item) {
                $item->value = $item->id;
                $item->opened = true;
                $item->selected = false;
                $item->disabled = false;
                $item->loading = false;
                $item->children = [];
            }
            $classes = u::get_tree_data($class);
            if ($classes) {
                foreach ($classes as $cls) {
                    if ($cls) {
                        $data[] = $cls;
                    }
                }
            }
        }
        return response()->json($data);
    }

    public function getClassInfo(Request $request, $class_id)
    {
        $class_info = u::first("SELECT cl.id AS class_id, cl.cls_name, cl.cls_startdate, cl.cls_enddate,
                (SELECT CONCAT(`name`, ' - ', hrm_id) FROM users WHERE id = cl.teacher_id) AS teacher_name,
                (SELECT CONCAT(`name`, ' - ', hrm_id) FROM users WHERE id = cl.cm_id) AS cm_name,
                (SELECT CONCAT(`name`, ' - ', hrm_id) FROM users WHERE id = cl.ta_id) AS ta_name,
                cl.max_students, cl.class_day,'' AS room_text, '' AS shift_text, '' AS class_day_text
            FROM classes AS cl WHERE id = $class_id");
        $rooms = u::query("SELECT DISTINCT r.name FROM `sessions` AS s LEFT JOIN rooms AS r ON r.id=s.room_id WHERE s.status=1 AND s.class_id =" . $class_id);
        $shifts = u::query("SELECT DISTINCT sh.name FROM `sessions` AS s LEFT JOIN shifts AS sh ON sh.id=s.shift_id WHERE s.status=1 AND s.class_id =" . $class_id);

        $room_text = "";
        foreach ($rooms as $room) {
            $room_text .= $room_text ? ", " . $room->name : $room->name;
        }
        $class_info->room_text = $room_text;
        $shift_text = "";
        foreach ($shifts as $shift) {
            $shift_text .= $shift_text ? ", " . $shift->name : $shift->name;
        }
        $class_info->shift_text = $shift_text;
        $class_info->class_day_text = u::getClassDayText($class_info->class_day);
        $students = u::query("SELECT c.code AS contract_code, c.id AS contract_id, s.name, s.lms_code, s.gud_mobile1,
                c.enrolment_start_date, c.enrolment_last_date, c.summary_sessions, c.real_sessions, c.bonus_sessions,
                c.must_charge, c.total_charged, c.done_sessions, c.add_class_status, c.ec_id, c.ec_leader_id,
                (SELECT name FROM tuition_fee WHERE id= c.tuition_fee_id) AS tuition_fee_name,
                (SELECT u.name FROM users u WHERE u.id = c.ec_id) AS ec_name,
                (SELECT u.name FROM users u WHERE u.id = c.ec_leader_id) AS team_name,
                (SELECT created_at FROM log_class_students WHERE class_id=$class_id AND contract_id=c.id AND action=1 ORDER BY id DESC LIMIT 1) AS added_at,
                p.link_facebook
            FROM contracts AS c
                LEFT JOIN students AS s ON c.student_id=s.id
                LEFT JOIN crm_parents AS p ON p.student_id = s.id
            WHERE c.class_id =$class_id ORDER BY added_at ASC");
        $class_info->num_students = count($students);
        $class_dates = u::query("SELECT class_date FROM schedules WHERE class_id = $class_id AND status=1 AND class_date >= CURRENT_DATE ORDER BY class_date");

        $pre_schedules = u::query("SELECT s.class_date, s.subject_stt, sj.code FROM schedules AS s LEFT JOIN subjects AS sj ON sj.id=s.subject_id WHERE s.class_id = $class_id AND s.status=1 AND s.class_date < CURRENT_DATE ORDER BY s.class_date DESC LIMIT 3");
        $next_schedules = u::query("SELECT s.class_date, s.subject_stt, sj.code FROM schedules AS s LEFT JOIN subjects AS sj ON sj.id=s.subject_id WHERE s.class_id = $class_id AND s.status=1 AND s.class_date >= CURRENT_DATE ORDER BY s.class_date LIMIT 3");
        $reversed = array_reverse($pre_schedules);

        // Check current user's role for permission control
        $current_user_id = Auth::user()->id;
        $is_sale = u::first("SELECT 1 FROM role_has_user WHERE user_id = $current_user_id AND role_id = " . SystemCode::ROLE_EC);
        $is_sale_leader = u::first("SELECT 1 FROM role_has_user WHERE user_id = $current_user_id AND role_id = " . SystemCode::ROLE_EC_LEADER);

        $data = [
            'class_info' => $class_info,
            'students' => $students,
            'class_dates' => $class_dates,
            'pre_schedules' => $reversed,
            'next_schedules' => $next_schedules,
            'user_role' => [
                'user_id' => $current_user_id,
                'is_sale' => !empty($is_sale),
                'is_sale_leader' => !empty($is_sale_leader),
            ],
        ];
        return response()->json($data);
    }

    public function getClassLogs(Request $request, $class_id)
    {
        $logs = u::query("SELECT l.*, s.name as student_name, s.lms_code, c.code as contract_code, u.name as creator_name
            FROM log_class_students l
            LEFT JOIN students s ON s.id = l.student_id
            LEFT JOIN contracts c ON c.id = l.contract_id
            LEFT JOIN users u ON u.id = l.creator_id
            WHERE l.class_id = $class_id
            ORDER BY l.created_at DESC");
        return response()->json($logs);
    }

    public function getStudentsAdd(Request $request)
    {
        $class_info = u::first("SELECT id, branch_id, product_id,type, program_id, type_fee, cls_startdate FROM classes WHERE id=$request->class_id");
        $keyword = isset($request->keyword) ? $request->keyword : '';

        $pagination = (object) $request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);
        $limitation = $limit > 0 ? " LIMIT $offset, $limit" : "";

        $product_id = data_get($class_info, 'product_id');
        if (data_get($class_info, 'cls_startdate') < date('Y-m-d')) {
            $cond = " c.status IN (2, 3, 4, 5) AND c.left_sessions > 0";
        } else {
            $cond = " c.status IN (1, 2, 3, 4, 5)";
        }
        $cond .= " AND c.product_id = $product_id  AND (SELECT count(id) FROM contracts WHERE student_id =c.student_id AND status=6 AND product_id = $product_id)= 0";

        if ($keyword !== '') {
            $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%' OR c.code LIKE '%$keyword%' OR s.gud_mobile1 LIKE '%$keyword%') ";
        }

        // Filter by Team KD (sale leader id - role 69)
        $ec_id = isset($request->ec_id) && $request->ec_id ? (int) $request->ec_id : '';
        if ($ec_id) {
            $cond .= " AND (c.ec_leader_id = $ec_id OR c.ec_id = $ec_id)";
        }

        // Filter by ngày học dự kiến (start_date range)
        $start_date_from = isset($request->start_date_from) && $request->start_date_from ? $request->start_date_from : '';
        $start_date_to = isset($request->start_date_to) && $request->start_date_to ? $request->start_date_to : '';
        if ($start_date_from && $start_date_to) {
            $cond .= " AND c.start_date >= '$start_date_from' AND c.start_date <= '$start_date_to'";
        } elseif ($start_date_from) {
            $cond .= " AND c.start_date >= '$start_date_from'";
        } elseif ($start_date_to) {
            $cond .= " AND c.start_date <= '$start_date_to'";
        }

        // Role-based filtering: sale/sale_leader chỉ thấy HS mình quản lý
        $current_user_id = Auth::user()->id;
        $is_sale = u::first("SELECT 1 FROM role_has_user WHERE user_id = $current_user_id AND role_id = " . SystemCode::ROLE_EC);
        $is_sale_leader = u::first("SELECT 1 FROM role_has_user WHERE user_id = $current_user_id AND role_id = " . SystemCode::ROLE_EC_LEADER);

        if ($is_sale && !$is_sale_leader) {
            // Role 68 (sale): chỉ thấy HS có ec_id = user_id
            $cond .= " AND c.ec_id = $current_user_id";
        } elseif ($is_sale_leader) {
            // Role 69 (sale leader): thấy HS có ec_id hoặc ec_leader_id = user_id
            $cond .= " AND (c.ec_id = $current_user_id OR c.ec_leader_id = $current_user_id)";
        }

        if (data_get($class_info, 'is_online') == 1) {
            $cond .= " AND t.type IN(0,2) ";
        } else {
            $cond .= " AND t.type IN(0,1) ";
        }

        $order_by = " ORDER BY s.id DESC ";

        $total = u::first("SELECT count(s.id) AS total  FROM contracts AS c
                LEFT JOIN students AS s ON s.id=c.student_id 
                LEFT JOIN tuition_fee AS t ON t.id=c.tuition_fee_id WHERE $cond");

        $list = u::query("SELECT c.id AS contract_id, c.code, s.name, s.lms_code, c.start_date, c.student_id AS student_id, c.left_sessions,
                (SELECT name FROM tuition_fee WHERE id =c.tuition_fee_id) AS tuition_fee_name,
                (SELECT CONCAT('name',' - ',hrm_id) FROM users WHERE id =c.ec_id) AS ec_name, '' AS class_date
            FROM contracts AS c 
                LEFT JOIN tuition_fee AS t ON t.id=c.tuition_fee_id
                LEFT JOIN students AS s ON s.id=c.student_id
            WHERE $cond $order_by $limitation");
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function addStudent(Request $request)
    {
        $contracts = $request->contracts;
        $class_id = $request->class_id;
        $class_info = u::getObject(array('id' => $class_id), 'classes');
        $cm_id = data_get($class_info, 'cm_id', null);
        $teacher_id = data_get($class_info, 'teacher_id', null);
        $cm_leader = u::first("SELECT ul.id 
            FROM users AS u 
                LEFT JOIN users AS ul ON ul.id=u.manager_id
                LEFT JOIN role_has_user AS ru ON ru.user_id= ul.id 
                LEFT JOIN roles AS r ON r.id=ru.role_id 
            WHERE r.code = '" . SystemCode::ROLE_CM_LEADER . "' AND ul.status=1 AND u.id = " . data_get($class_info, 'cm_id', 0) . " LIMIT 1");
        $cm_leader_id = data_get($cm_leader, 'id') ? data_get($cm_leader, 'id') : $cm_id;
        $holidays = u::getPublicHolidays(data_get($request, 'branch_id'), data_get($request, 'product_id'));
        $arr_day = explode(",", data_get($class_info, 'class_day'));

        // Check if role sale/sale leader and class has already started
        $current_user_id = Auth::user()->id;
        $is_sale = u::first("SELECT 1 FROM role_has_user WHERE user_id = $current_user_id AND role_id = " . SystemCode::ROLE_EC);
        $is_sale_leader = u::first("SELECT 1 FROM role_has_user WHERE user_id = $current_user_id AND role_id = " . SystemCode::ROLE_EC_LEADER);
        
        if ($is_sale || $is_sale_leader) {
            $class_startdate = data_get($class_info, 'cls_startdate');
            if ($class_startdate && $class_startdate < date('Y-m-d')) {
                return response()->json(array(
                    'status' => 0,
                    'message' => 'Lớp đã khai giảng, Sale và Sale Leader không có quyền thêm học sinh'
                ), 403);
            }
        }

        foreach ($contracts as $contract) {
            $contract_id = data_get($contract, 'contract_id');
            $student_id = data_get($contract, 'student_id');
            $start_date = data_get($contract, 'class_date', null);
            $data_sessions = u::calculatorSessionsByNumberOfSessions($start_date, data_get($request, 'session'), $holidays, $arr_day);
            u::updateSimpleRow(array(
                'cm_id' => $cm_id,
                'cm_leader_id' => $cm_leader_id,
                'program_id' => data_get($class_info, 'program_id', null),
                'class_id' => data_get($class_info, 'id', null),
                'enrolment_start_date' => $start_date,
                'enrolment_last_date' => data_get($data_sessions, 'end_date'),
                'status' => 6,
                'updated_at' => date('Y-m-d H:i:s'),
                'updator_id' => Auth::user()->id,
            ), array('id' => $contract_id), 'contracts');
            u::addLogContracts($contract_id);
            u::updateSimpleRow(array(
                'cm_id' => $cm_id,
                'teacher_id' => $teacher_id,
                'cm_leader_id' => $cm_leader_id,
                'updated_at' => date('Y-m-d H:i:s'),
                'updator_id' => Auth::user()->id
            ), array('student_id' => $student_id), 'term_student_user');
            LogStudents::logAdd($student_id, 'Xếp vào lớp ' . data_get($class_info, 'cls_name'), Auth::user()->id);
            LogClassStudent::logAction($class_id, $student_id, $contract_id, 1, Auth::user()->id);

            // Tự động tạo tickets khi học sinh được thêm vào lớp
            $actions = ['Phát sách', 'Thông báo lịch học'];
            TicketService::createTicketsForStudentEnrollment($student_id, $class_id, $contract_id, $actions);
        }

        $result = array(
            'status' => 1,
            'message' => 'Thêm học sinh vào lớp thành công'
        );
        return response()->json($result);
    }

    public function removeStudent(Request $request)
    {
        $contract_id = (int) data_get($request, 'contract_id');
        $class_id = (int) data_get($request, 'class_id');

        // Lấy thông tin contract
        $contract = u::getObject(['id' => $contract_id], 'contracts');

        if (!$contract) {
            return response()->json([
                'status' => 0,
                'message' => 'Không tìm thấy hợp đồng'
            ], 404);
        }

        // Kiểm tra xem học sinh đã bắt đầu học chưa (done_sessions > 0)
        if ((int) data_get($contract, 'done_sessions', 0) > 0) {
            return response()->json([
                'status' => 0,
                'message' => 'Không thể xóa học sinh đã bắt đầu học khỏi lớp'
            ], 400);
        }

        // Kiểm tra quyền: role 68 (sale) chỉ xóa HS có ec_id = user_id, role 69 (sale leader) chỉ xóa HS có ec_id hoặc ec_leader_id = user_id
        $current_user_id = Auth::user()->id;
        $is_sale = u::first("SELECT 1 FROM role_has_user WHERE user_id = $current_user_id AND role_id = " . SystemCode::ROLE_EC);
        $is_sale_leader = u::first("SELECT 1 FROM role_has_user WHERE user_id = $current_user_id AND role_id = " . SystemCode::ROLE_EC_LEADER);

        if ($is_sale && !$is_sale_leader) {
            // Role 68 (sale): chỉ xóa HS mình quản lý (ec_id = user_id)
            if ((int) data_get($contract, 'ec_id') !== $current_user_id) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Bạn không có quyền xóa học sinh này. Chỉ được xóa học sinh mình quản lý.'
                ], 403);
            }
        } elseif ($is_sale_leader) {
            // Role 69 (sale leader): xóa HS có ec_id hoặc ec_leader_id = user_id
            if ((int) data_get($contract, 'ec_id') !== $current_user_id && (int) data_get($contract, 'ec_leader_id') !== $current_user_id) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Bạn không có quyền xóa học sinh này. Chỉ được xóa học sinh trong team mình quản lý.'
                ], 403);
            }
        }

        // Cập nhật contract: status=3, class_id=null, enrolment_start_date=null, enrolment_last_date=null
        u::updateSimpleRow([
            'status' => data_get($contract, 'debt_amount') == 0 ? 3 : (data_get($contract, 'total_charged') > 0 ? 2 : 1),
            'class_id' => null,
            'enrolment_start_date' => null,
            'enrolment_last_date' => null,
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => Auth::user()->id,
        ], ['id' => $contract_id], 'contracts');

        u::addLogContracts($contract_id);

        $student_id = data_get($contract, 'student_id');
        $class_info = u::getObject(['id' => $class_id], 'classes');

        LogStudents::logAdd(
            $student_id,
            'Xóa khỏi lớp ' . data_get($class_info, 'cls_name'),
            Auth::user()->id
        );
        LogClassStudent::logAction($class_id, $student_id, $contract_id, 0, Auth::user()->id);

        return response()->json([
            'status' => 1,
            'message' => 'Xóa học sinh khỏi lớp thành công'
        ]);
    }

    public function updateAddClassStatus(Request $request)
    {
        $contract_id = (int) data_get($request, 'contract_id');
        $status = (int) data_get($request, 'add_class_status');

        if (!$contract_id) {
            return response()->json(['status' => 0, 'message' => 'Thiếu contract_id'], 400);
        }

        u::updateSimpleRow(
            ['add_class_status' => $status, 'updated_at' => date('Y-m-d H:i:s')],
            ['id' => $contract_id],
            'contracts'
        );

        return response()->json(['status' => 1, 'message' => 'Cập nhật thành công']);
    }
}
