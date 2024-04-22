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
            
        if($request->is_edit){
            $class_id = data_get($request,'class_id');
            $class_info = u::getObject(array('id'=>$class_id), 'classes');
            $session_num = u::first("SELECT count(id) AS total FROM schedules WHERE class_id = ".(int)$class_id);
            if($start_date !== data_get($class_info,'start_date') ||
                implode(",", $arr_day) !== data_get($class_info,'class_day') || 
                $session_num < data_get($request,'session')){
                u::updateSimpleRow(array(
                    'status'=>0,
                    'updated_at'=> date('Y-m-d H:i:s'),
                    'updator_id'=> Auth::user()->id,
                ),array('class_id'=>data_get($request,'class_id')), 'sessions');
                foreach($arr_day AS $session){
                    $session_info = u::first("SELECT id FROM `sessions` WHERE class_id=$class_id AND class_day = $session");
                    if($session_info){
                        u::updateSimpleRow(array('status'=>1),array('id'=>data_get($session_info,'id')), 'sessions');
                    }else{
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
                }
                u::query("DELETE FROM schedules WHERE class_id=$class_id"); 
                foreach(data_get($data_sessions, 'dates') AS $row){
                    u::insertSimpleRow(array(
                        'class_date'=> $row,
                        'class_id'=> $class_id,
                        'status'=> 1,
                        'created_at'=> date('Y-m-d H:i:s'),
                    ),'schedules');
                }  
            }elseif($session_num > data_get($request,'session')){
                foreach(data_get($data_sessions, 'dates') AS $row){
                    $schedule_info = u::first("SELECT id FROM schedules WHERE class_date='$row' AND class_id = $class_id");
                    if(!$schedule_info){
                        u::insertSimpleRow(array(
                            'class_date'=> $row,
                            'class_id'=> $class_id,
                            'status'=> 1,
                            'created_at'=> date('Y-m-d H:i:s'),
                        ),'schedules');
                    }
                }
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
            foreach(data_get($data_sessions, 'dates') AS $row){
                u::insertSimpleRow(array(
                    'class_date'=> $row,
                    'class_id'=> $class_id,
                    'status'=> 1,
                    'created_at'=> date('Y-m-d H:i:s'),
                ),'schedules');
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
            'session' => $schedule_num->total
        ];
        return response()->json($data);
    }
}
