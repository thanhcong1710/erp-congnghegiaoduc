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
            WHERE DATE_FORMAT(ss.class_date, '%Y-%m')='$date_select' AND class_id=$class_id ORDER BY s.lastname");
        $shedules = u::query("SELECT s.class_date, DATE_FORMAT(s.class_date, '%Y_%m_%d') AS date_key, sj.code, s.subject_stt
            FROM schedules AS s LEFT JOIN subjects AS sj ON sj.id=s.subject_id  WHERE s.class_id=$class_id AND DATE_FORMAT(s.class_date, '%Y-%m') ='$date_select' ORDER BY class_date ");
        foreach($students AS $k=>$stu){
            $attendances =[];
            foreach($shedules AS $sche){
                $schedule_has_student_info = u::first("SELECT s.attendance_status, s.note, s.id, s.status, sj.code AS subject_code, sj.name AS subject_name, s.subject_stt, s.class_date,
                        (SELECT cl.cls_name  FROM make_up_sessions AS m LEFT JOIN classes AS cl ON cl.id=m.to_class_id WHERE m.schedule_student_id =s.id ORDER BY m.id DESC LIMIT 1) AS to_class_name,
                        (SELECT m.to_class_date  FROM make_up_sessions AS m WHERE m.schedule_student_id =s.id ORDER BY m.id DESC LIMIT 1) AS to_class_date, 
                        (SELECT m.status FROM make_up_sessions AS m WHERE m.schedule_student_id =s.id ORDER BY m.id DESC LIMIT 1) AS to_class_status 
                    FROM schedule_has_student AS s LEFT JOIN subjects AS sj ON sj.id=s.subject_id WHERE s.student_id=$stu->student_id AND s.class_id = $class_id AND s.class_date='$sche->class_date'");
                $attendances[$sche->date_key] = $schedule_has_student_info;
            }
            $students[$k]->attendances = $attendances;
        }
        $make_up_sessions = u::query("SELECT m.id, m.student_id, s.name, s.lms_code,m.to_class_date, m.status 
            FROM make_up_sessions AS m LEFT JOIN students AS s ON s.id=m.student_id WHERE m.to_class_id = $class_id AND DATE_FORMAT(m.to_class_date, '%Y-%m')='$date_select' ORDER BY m.to_class_date");
        $data = [
            'class_info' =>$class_info,
            'students' => $students,
            'shedules' => $shedules,
            'make_up_sessions' => $make_up_sessions
        ];
        return response()->json($data);
    }

    public function save(Request $request){
        $schedule_has_student_info = u::getObject(array('id'=>data_get($request, 'id')), 'schedule_has_student');
        if($schedule_has_student_info){
            u::updateSimpleRow(array(
                'attendance_status'=>data_get($request, 'attendance_status'),
                // 'status' => data_get($request, 'attendance_status') == 1 || data_get($request, 'attendance_status') == 3 ? 1 :0,
                'note'=>data_get($request, 'note'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updator_id' => Auth::user()->id,
            ),array('id'=>data_get($request, 'id')), 'schedule_has_student');
            // u::updateDoneSessions(data_get($schedule_has_student_info, 'contract_id'));
        }
        $result = array(
            'status' => 1,
            'message' => 'Cập nhật thành công'
        );
        return response()->json($result);
    }

    public function loadReSessions(Request $request, $class_id){
        $data = u::query("SELECT sj.code AS subject_code, sj.name AS subject_name , s.subject_stt, s.id, s.class_date, '' AS session_label
            FROM schedules AS s LEFT JOIN subjects AS sj ON sj.id=s.subject_id 
            WHERE s.status=1 AND s.class_id=$class_id AND s.class_date >= CURRENT_DATE ORDER BY s.class_date LIMIT 8 ");
        foreach ($data AS $k=> $row){
            $data[$k]->session_label = $row->subject_code ." - buổi ". $row->subject_stt ." (".u::formatDateView($row->class_date).")";
        }
        return response()->json($data);
    }

    public function addReSession(Request $request){
        $schedule_has_student_info = u::getObject(array('id'=>data_get($request, 'schedule_student_id')), 'schedule_has_student');
        u::insertSimpleRow(array(
            'student_id' => data_get($request, 'student_id'),
            'schedule_student_id' => data_get($request, 'schedule_student_id'),
            'from_class_id' => data_get($schedule_has_student_info, 'class_id'),
            'from_class_date' => data_get($schedule_has_student_info, 'class_date'),
            'to_class_id' => data_get($request, 'to_class_id'),
            'to_class_date' => data_get($request, 'to_class_date'),
            'status' =>1,
            'created_at' => date('Y-m-d H:i:s'),
            'creator_id' => Auth::user()->id,
        ), 'make_up_sessions');
        u::updateSimpleRow(array('attendance_status'=>2),array('id'=>data_get($request, 'schedule_student_id')), 'schedule_has_student');

        $result = array(
            'status' => 1,
            'message' => 'Đăng ký học bù thành công'
        );
        return response()->json($result);
    }

    public function updateReSession(Request $request){
        $make_up_session = u::first("SELECT schedule_student_id FROM make_up_sessions WHERE id =".data_get($request, 'id'));
        u::updateSimpleRow(array(
            'status'=>data_get($request, 'status'),
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => Auth::user()->id,
        ),array('id'=>data_get($request, 'id')), 'make_up_sessions');
        u::updateSimpleRow(array(
            'attendance_status'=>data_get($request, 'status') == 2 ? 3 : 0,
            //'status'=> data_get($request, 'status') == 2 ? 1 : 0,
            'note'=>data_get($request, 'note'),
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => Auth::user()->id,
        ),array('id'=>data_get($make_up_session, 'schedule_student_id')), 'schedule_has_student');
        // $schedule_has_student = u::first("SELECT contract_id FROM schedule_has_student WHERE id =".data_get($make_up_session, 'schedule_student_id'));
        // u::updateDoneSessions(data_get($schedule_has_student, 'contract_id'));
        $result = array(
            'status' => 1,
            'message' => 'Cập nhật thành công'
        );
        return response()->json($result);
    }
}