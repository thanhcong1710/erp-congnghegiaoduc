<?php

namespace App\Http\Controllers;

use App\Enums\SystemCode;
use App\User;
use App\Http\Controllers\Controller;
use App\Models\LogStudents;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrolmentsController extends Controller
{
    public function loadClasses(Request $request)
    {
        $data = [];
        $branch_id = (int)$request->branch_id;
        $product_id = (int)$request->product_id;
        $query = "SELECT id, 
            id AS item_id, 
            'program' AS item_type, 
            `name` AS `text`, 
            parent_id, 
            'fa fa-folder' AS icon, 
            0 AS status 
        FROM programs 
        WHERE id > 0 AND status = 1 AND product_id = $product_id
        UNION ALL
        SELECT CONCAT(999, c.id) AS id, 
            c.id AS item_id, 
            'class' AS item_type, 
            c.cls_name AS `text`, 
            c.program_id AS parent_id, 
            IF(c.cm_id > 0, 
                IF(c.status = 0, 
                    'fa-regular fa-rectangle-xmark fa-fw', 
                    IF((SELECT COUNT(u.id) FROM users u LEFT JOIN sessions s ON u.id = s.teacher_id WHERE u.status > 0 AND s.class_id = c.id) > 0, 'fa-solid fa-file-lines fa-fw', 'fa-solid fa-triangle-exclamation fa-fw')), 'fa-solid fa-user-xmark fa-fw') AS icon, 
            c.status 
        FROM classes AS c INNER JOIN programs AS p ON c.program_id = p.id
        WHERE c.status = 1 AND p.status = 1 AND c.branch_id =$branch_id AND p.product_id = $product_id AND DATE(c.cls_enddate) >= CURDATE()";
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
                    if ($cls && !empty(data_get($cls, 'children')) ) {
                        $data[] = $cls;
                    }
                }
            }
        }
        return response()->json($data);
    }

    public function getClassInfo(Request $request, $class_id){
        $class_info = u::first("SELECT cl.id AS class_id, cl.cls_name, cl.cls_startdate, cl.cls_enddate,
                (SELECT CONCAT(`name`, ' - ', hrm_id) FROM users WHERE id = cl.teacher_id) AS teacher_name,
                (SELECT CONCAT(`name`, ' - ', hrm_id) FROM users WHERE id = cl.cm_id) AS cm_name,
                cl.max_students, cl.class_day,'' AS room_text, '' AS shift_text, '' AS class_day_text
            FROM classes AS cl WHERE id = $class_id");
        $rooms = u::query("SELECT DISTINCT r.name FROM `sessions` AS s LEFT JOIN rooms AS r ON r.id=s.room_id WHERE s.status=1 AND s.class_id =".$class_id);
        $shifts = u::query("SELECT DISTINCT sh.name FROM `sessions` AS s LEFT JOIN shifts AS sh ON sh.id=s.shift_id WHERE s.status=1 AND s.class_id =".$class_id);

        $room_text = "";
        foreach($rooms AS $room){
            $room_text.= $room_text ? ", ".$room->name : $room->name;
        }
        $class_info->room_text = $room_text;
        $shift_text = "";
        foreach($shifts AS $shift){
            $shift_text.= $shift_text ? ", ".$shift->name : $shift->name;
        }
        $class_info->shift_text = $shift_text;
        $class_info->class_day_text = u::getClassDayText($class_info->class_day);
        $students = u::query("SELECT c.code AS contract_code, c.id AS contract_id, s.name, s.lms_code,
                c.enrolment_start_date, c.enrolment_last_date, c.summary_sessions, c.real_sessions, c.bonus_sessions,
                c.must_charge, c.total_charged, 0 AS done_session,
                (SELECT name FROM tuition_fee WHERE id= c.tuition_fee_id) AS tuition_fee_name
            FROM contracts AS c LEFT JOIN students AS s ON c.student_id=s.id
            WHERE c.status!=7 AND c.class_id =$class_id");
        $class_info->num_students = count($students);
        $class_dates = u::query("SELECT class_date FROM schedules WHERE class_id = $class_id AND status=1 AND class_date >= CURRENT_DATE ORDER BY class_date");

        $data = [
            'class_info' =>$class_info,
            'students' => $students,
            'class_dates' => $class_dates
        ];
        return response()->json($data);
    }

    public function getStudentsAdd(Request $request){
        $class_info =u::first("SELECT id, branch_id, product_id FROM classes WHERE id=$request->class_id");
        $keyword = isset($request->keyword) ? $request->keyword : '';

        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";

        $product_id = data_get($class_info, 'product_id');
        $cond = " c.status IN (3, 4, 5)";
        $cond.=" AND (SELECT count(id) FROM contracts WHERE student_id =c.student_id AND status=6 AND product_id = $product_id)= 0
            AND c.count_recharge = (SELECT min(count_recharge) FROM contracts WHERE student_id =c.student_id AND product_id = $product_id AND status IN (3,4,5))";

        if ($keyword !== '') {
            $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%' OR c.code LIKE '%$keyword%') ";
        }
        
        $order_by = " ORDER BY s.id DESC ";

        $total = u::first("SELECT count(s.id) AS total  FROM contracts AS c 
                LEFT JOIN students AS s ON s.id=c.student_id WHERE $cond");
        
        $list = u::query("SELECT c.id AS contract_id, c.code, s.name, s.lms_code, c.start_date, c.student_id AS student_id,
                (SELECT name FROM tuition_fee WHERE id =c.tuition_fee_id) AS tuition_fee_name,
                (SELECT CONCAT('name',' - ',hrm_id) FROM users WHERE id =c.ec_id) AS ec_name, '' AS class_date
            FROM contracts AS c 
                LEFT JOIN students AS s ON s.id=c.student_id
            WHERE $cond $order_by $limitation");
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function addStudent(Request $request){
        $contracts = $request->contracts;
        $class_id = $request->class_id;
        $class_info = u::getObject(array('id'=>$class_id), 'classes');
        $cm_id =data_get($class_info,'cm_id', null);
        $teacher_id =data_get($class_info,'teacher_id', null);
        $cm_leader = u::first("SELECT ul.id 
            FROM users AS u 
                LEFT JOIN users AS ul ON ul.id=u.manager_id
                LEFT JOIN role_has_user AS ru ON ru.user_id= ul.id 
                LEFT JOIN roles AS r ON r.id=ru.role_id 
            WHERE r.code = '".SystemCode::ROLE_CM_LEADER."' AND ul.status=1 AND u.id = ".data_get($class_info, 'cm_id', 0)." LIMIT 1");
        $cm_leader_id = data_get($cm_leader,'id') ? data_get($cm_leader,'id') : $cm_id;
        $holidays = u::getPublicHolidays(data_get($request,'branch_id'), data_get($request,'product_id'));
        $arr_day = explode(",",data_get($class_info, 'class_day'));
        
        foreach($contracts AS $contract){
            $contract_id = data_get($contract,'contract_id');
            $student_id = data_get($contract,'student_id');
            $start_date = data_get($contract,'class_date', null);
            $data_sessions = u::calculatorSessionsByNumberOfSessions($start_date, data_get($request,'session'), $holidays, $arr_day);
            u::updateSimpleRow(array(
                'cm_id' => $cm_id,
                'cm_leader_id' => $cm_leader_id,
                'program_id' => data_get($class_info,'program_id', null),
                'class_id' => data_get($class_info,'id', null),
                'class_id' => data_get($class_info,'id', null),
                'enrolment_start_date' => $start_date,
                'enrolment_last_date' => data_get($data_sessions, 'end_date'),
                'status' => 6,
                'updated_at' => date('Y-m-d H:i:s'),
                'updator_id' => Auth::user()->id,
            ),array('id'=>$contract_id),'contracts');
            u::addLogContracts($contract_id);
            u::updateSimpleRow(array(
                'cm_id'=> $cm_id,
                'teacher_id'=>$teacher_id,
                'cm_leader_id' => $cm_leader_id,
                'updated_at' => date('Y-m-d H:i:s'),
                'updator_id' => Auth::user()->id
            ), array('student_id'=>$student_id), 'term_student_user');
            LogStudents::logAdd($student_id, 'Xếp vào lớp '.data_get($class_info,'cls_name'), Auth::user()->id);
        }

        $result = array(
            'status' => 1,
            'message' => 'Thêm học sinh vào lớp thành công'
        );
        return response()->json($result);
    }
}
