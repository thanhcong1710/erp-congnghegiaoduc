<?php

namespace App\Http\Controllers;

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
        $date_select = data_get($request, 'date_select');
        $class_info = u::first("SELECT cl.id AS class_id, cl.cls_name, cl.cls_startdate, cl.cls_enddate,
                (SELECT CONCAT(`name`, ' - ', hrm_id) FROM users WHERE id = cl.teacher_id) AS teacher_name,
                (SELECT CONCAT(`name`, ' - ', hrm_id) FROM users WHERE id = cl.cm_id) AS cm_name,
                cl.max_students, cl.class_day,'' AS room_text, '' AS shift_text, '' AS class_day_text,
                (SELECT count(id) FROM contracts WHERE class_id=cl.id AND status=6) AS num_students
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
        $students = u::query("SELECT DISTINCT s.lastname, s.name, s.lms_code, s.id AS student_id
            FROM schedule_has_student AS ss LEFT JOIN students AS s ON ss.student_id=s.id
            WHERE ss.status>0 AND DATE_FORMAT(ss.class_date, '%Y-%m')='$date_select' ORDER BY s.lastname");
        $shedules = u::query("SELECT class_date, DATE_FORMAT(class_date, '%Y_%m_%d') AS date_key, DATE_FORMAT(class_date, '%d/%m') AS date_label FROM schedules WHERE class_id=$class_id AND DATE_FORMAT(class_date, '%Y-%m') ='$date_select' ORDER BY class_date ");
        foreach($students AS $k=>$stu){
            $attendances =[];
            foreach($shedules AS $sche){
                $schedule_has_student_info = u::first("SELECT attendance_status, note, id, status FROM schedule_has_student WHERE student_id=$stu->student_id AND class_date='$sche->class_date'");
                $attendances[$sche->date_key] = $schedule_has_student_info;
            }
            $students[$k]->attendances = $attendances;
        }
        $data = [
            'class_info' =>$class_info,
            'students' => $students,
            'shedules' => $shedules
        ];
        return response()->json($data);
    }

    public function save(Request $request){
        u::updateSimpleRow(array(
            'attendance_status'=>data_get($request, 'attendance_status'),
            'note'=>data_get($request, 'note'),
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => Auth::user()->id,
        ),array('id'=>data_get($request, 'id')), 'schedule_has_student');
        $result = array(
            'status' => 1,
            'message' => 'Cập nhật thành công'
        );
        return response()->json($result);
    }
}