<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use App\Models\LogStudents;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassesController extends Controller
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
        WHERE p.status = 1 AND c.branch_id =$branch_id AND p.product_id = $product_id AND DATE(c.cls_enddate) >= CURDATE()";
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

    public function save(Request $request)
    {
        $class_day = $request->class_day;
        $arr_day = [];
        if(!empty($class_day)){
            foreach($class_day AS $k=>$row){
                if($row){
                    $arr_day[] = (int)(str_replace('day_','',$k));
                }
            }
        }
        $holidays = u::getPublicHolidays(data_get($request,'branch_id'), data_get($request,'product_id'));
        $start_date = data_get($request,'start_date');
        $data_sessions = u::calculatorSessionsByNumberOfSessions($start_date, data_get($request,'session'), $holidays, $arr_day);
        $subjects = data_get($request,'subjects');
            
        if($request->is_edit){
            $class_id = data_get($request,'class_id');
            u::query("DELETE FROM sessions WHERE class_id= $class_id");
            u::query("DELETE FROM subject_has_class WHERE class_id= $class_id");
            u::query("DELETE FROM schedules WHERE class_id= $class_id");
            foreach($arr_day AS $session){
                u::insertSimpleRow(array(
                    'class_id'=> $class_id,
                    'shift_id'=> data_get($request,'shift_id'),
                    'room_id'=> data_get($request,'room_id'),
                    'teacher_id'=> data_get($request,'teacher_id'),
                    'class_day'=> $session,
                    'status'=> 1,
                    'branch_id'=> data_get($request,'branch_id'),
                    'created_at'=> date('Y-m-d H:i:s'),
                    'creator_id'=> Auth::user()->id
                ),'sessions');
            }
            foreach($subjects AS $subject){
                u::insertSimpleRow(array(
                    'class_id'=> $class_id,
                    'subject_id'=> data_get($subject,'id'),
                    'session'=> data_get($subject,'session'),
                    'stt'=> data_get($subject,'stt'),
                    'created_at'=> date('Y-m-d H:i:s')
                ),'subject_has_class');
            }
            foreach(data_get($data_sessions, 'dates') AS $row){
                u::insertSimpleRow(array(
                    'class_date'=> $row,
                    'class_id'=> $class_id,
                    'status'=> 1,
                    'created_at'=> date('Y-m-d H:i:s'),
                ),'schedules');
            }

            $arr_subject = u::query("SELECT * FROM subject_has_class WHERE class_id=$class_id ORDER BY stt");
            foreach($arr_subject AS $subject){
                u::query("UPDATE schedules set subject_id = $subject->subject_id WHERE subject_id IS NULL AND status= 1 AND class_id= $class_id ORDER BY class_date LIMIT $subject->session");

            }
            $arr_schedule = u::query("SELECT * FROM schedules WHERE status= 1 AND class_id= $class_id ORDER BY class_date");
            $tmp_subject = 0;
            $subject_stt = 0;
            $class_stt = 0;
            foreach($arr_schedule AS $row){
                if($tmp_subject != $row->subject_id){
                    $tmp_subject = $row->subject_id;
                    $subject_stt = 0;
                }
                $subject_stt++;
                $class_stt++;
                u::updateSimpleRow(array('subject_stt'=>$subject_stt, 'class_stt'=>$class_stt), array('id'=>$row->id),'schedules');
            }
            
            u::updateSimpleRow(array(
                'branch_id'=> data_get($request,'branch_id'),
                'teacher_id'=> data_get($request,'teacher_id'),
                'cls_name'=> data_get($request,'title'),
                'product_id'=> data_get($request,'product_id'),
                'program_id'=> data_get($request,'program_id'),
                'cm_id'=> data_get($request,'cm_id'),
                'cls_startdate'=> data_get($request,'start_date'),
                'cls_enddate'=> data_get($data_sessions,'end_date'),
                'class_day'=> implode(",", $arr_day),
                'max_students'=> data_get($request,'max_students'),
                'updated_at'=> date('Y-m-d H:i:s'),
                'updator_id'=> Auth::user()->id,
                'status'=> data_get($request,'status'),
                'type'=> data_get($request,'type'),
            ), array('id'=>data_get($request,'class_id')), 'classes');

        }else{
            $class_id = u::insertSimpleRow(array(
                'branch_id'=> data_get($request,'branch_id'),
                'teacher_id'=> data_get($request,'teacher_id'),
                'cls_name'=> data_get($request,'title'),
                'product_id'=> data_get($request,'product_id'),
                'program_id'=> data_get($request,'program_id'),
                'cm_id'=> data_get($request,'cm_id'),
                'cls_startdate'=> data_get($request,'start_date'),
                'cls_enddate'=> data_get($data_sessions,'end_date'),
                'class_day'=> implode(",", $arr_day),
                'max_students'=> data_get($request,'max_students'),
                'created_at'=> date('Y-m-d H:i:s'),
                'creator_id'=> Auth::user()->id,
                'status'=> data_get($request,'status'),
                'type'=> data_get($request,'type'),
            ),'classes');
            foreach($arr_day AS $session){
                u::insertSimpleRow(array(
                    'class_id'=> $class_id,
                    'shift_id'=> data_get($request,'shift_id'),
                    'room_id'=> data_get($request,'room_id'),
                    'teacher_id'=> data_get($request,'teacher_id'),
                    'class_day'=> $session,
                    'status'=> 1,
                    'branch_id'=> data_get($request,'branch_id'),
                    'created_at'=> date('Y-m-d H:i:s'),
                    'creator_id'=> Auth::user()->id
                ),'sessions');
            }
            foreach($subjects AS $subject){
                u::insertSimpleRow(array(
                    'class_id'=> $class_id,
                    'subject_id'=> data_get($subject,'id'),
                    'session'=> data_get($subject,'session'),
                    'stt'=> data_get($subject,'stt'),
                    'created_at'=> date('Y-m-d H:i:s')
                ),'subject_has_class');
            }
            foreach(data_get($data_sessions, 'dates') AS $row){
                u::insertSimpleRow(array(
                    'class_date'=> $row,
                    'class_id'=> $class_id,
                    'status'=> 1,
                    'created_at'=> date('Y-m-d H:i:s'),
                ),'schedules');
            }

            $arr_subject = u::query("SELECT * FROM subject_has_class WHERE class_id=$class_id ORDER BY stt");
            foreach($arr_subject AS $subject){
                u::query("UPDATE schedules set subject_id = $subject->subject_id WHERE subject_id IS NULL AND status= 1 AND class_id= $class_id ORDER BY class_date LIMIT $subject->session");
            }

            $arr_schedule = u::query("SELECT * FROM schedules WHERE status= 1 AND class_id= $class_id ORDER BY class_date");
            $tmp_subject = 0;
            $subject_stt = 0;
            $class_stt = 0;
            foreach($arr_schedule AS $row){
                if($tmp_subject != $row->subject_id){
                    $tmp_subject = $row->subject_id;
                    $subject_stt = 0;
                }
                $subject_stt++;
                $class_stt++;
                u::updateSimpleRow(array('subject_stt'=>$subject_stt, 'class_stt'=>$class_stt), array('id'=>$row->id),'schedules');
            }
        }
        $result = array(
            'status' => 1,
            'message' => $request->is_edit ? 'Cập nhật thông tin lớp học thành công' : 'Thêm mới lớp học thành công'
        );
        return response()->json($result);
    }

    public function infoConfig(Request $request, $class_id){
        $class_info = u::first("SELECT cl.* FROM classes AS cl WHERE cl.id = $class_id");
        $session_info = u::first("SELECT s.shift_id, s.room_id FROM `sessions` AS s WHERE s.class_id = $class_id AND s.status=1 LIMIT 1");
        $schedule_num = u::first("SELECT count(id) AS total FROM schedules WHERE class_id = $class_id");
        $subjects = u::query("SELECT s.*, sc.session, sc.stt FROM subject_has_class AS sc LEFT JOIN subjects AS s ON s.id=sc.subject_id WHERE sc.class_id = $class_id ORDER BY sc.stt");
        $arr_day = explode(',',data_get($class_info, 'class_day'));
        $data = [
            'is_edit' => 1,
            'title'=> data_get($class_info, 'cls_name'),
            'class_id' =>$class_id,
            'branch_id' => data_get($class_info, 'branch_id'),
            'product_id' => data_get($class_info, 'product_id'),
            'program_id'=> data_get($class_info, 'program_id'),
            'max_students' => data_get($class_info, 'max_students'),
            'type' => data_get($class_info, 'type'),
            'status' => data_get($class_info, 'status'),
            'start_date' => data_get($class_info, 'cls_startdate'),
            'cm_id' => data_get($class_info, 'cm_id'),
            'teacher_id' => data_get($class_info, 'teacher_id'),
            'shift_id' => data_get($session_info, 'shift_id'),
            'room_id' => data_get($session_info, 'room_id'),
            'class_day' => [
                'day_2' => in_array(2,$arr_day) ? true : false,
                'day_3' => in_array(3,$arr_day) ? true : false,
                'day_4' => in_array(4,$arr_day) ? true : false,
                'day_5' => in_array(5,$arr_day) ? true : false,
                'day_6' => in_array(6,$arr_day) ? true : false,
                'day_7' => in_array(7,$arr_day) ? true : false,
                'day_8' => in_array(8,$arr_day) ? true : false,
            ],
            'session' => $schedule_num->total,
            'subjects' => $subjects
        ];
        return response()->json($data);
    }

    public function listSessions(Request $request)
    {
        $class_id = isset($request->class_id) ? $request->class_id : 0;

        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        $cond = " s.status = 1 AND s.class_id= $class_id";
        
        $order_by = " ORDER BY class_date ";

        $total = u::first("SELECT count(s.id) AS total FROM schedules AS s WHERE $cond");
        
        $list = u::query("SELECT s.class_date, sj.code AS subject_code, sj.name AS subject_name,
                IF(class_date < CURRENT_DATE, 'Đã học', 'Sắp học') AS status_label
            FROM schedules AS s 
                LEFT JOIN subjects AS sj ON s.subject_id=sj.id
            WHERE $cond $order_by $limitation");
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }
}
