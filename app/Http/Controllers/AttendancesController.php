<?php

namespace App\Http\Controllers;

use App\Enums\SystemCode;
use App\User;
use App\Http\Controllers\Controller;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendancesController extends Controller
{
    public function loadClasses(Request $request){
        $program_id = data_get($request, 'program_id');
        $branch_id = data_get($request, 'branch_id');
        $data= u::query("SELECT id, cls_name FROM classes WHERE status=1 AND program_id=$program_id AND branch_id = $branch_id ");
        return response()->json($data);
    }

    public function loadStudents(Request $request){
        $class_id = data_get($request, 'class_id');
        $class_info = u::first("SELECT cl.id AS class_id, cl.cls_name, cl.cls_startdate, cl.cls_enddate,
                (SELECT CONCAT(`name`, ' - ', hrm_id) FROM users WHERE id = cl.teacher_id) AS teacher_name,
                (SELECT CONCAT(`name`, ' - ', hrm_id) FROM users WHERE id = cl.cm_id) AS cm_name,
                cl.max_students, cl.class_day,'' AS room_text, '' AS shift_text, '' AS class_day_text
            FROM classes AS cl WHERE id = $class_id");
        $rooms = u::query("SELECT DISTINCT r.room_name FROM `sessions` AS s LEFT JOIN rooms AS r ON r.id=s.room_id WHERE s.status=1 AND s.class_id =".$class_id);
        $shifts = u::query("SELECT DISTINCT sh.name FROM `sessions` AS s LEFT JOIN shifts AS sh ON sh.id=s.shift_id WHERE s.status=1 AND s.class_id =".$class_id);

        $room_text = "";
        foreach($rooms AS $room){
            $room_text.= $room_text ? ", ".$room->room_name : $room->room_name;
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
        
        $data = [
            'class_info' =>$class_info,
            'students' => $students
        ];
        return response()->json($data);
    }
}
