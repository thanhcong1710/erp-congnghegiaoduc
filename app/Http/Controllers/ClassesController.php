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
        
        if($request->is_edit){

        }else{
            $start_date = data_get($request,'start_date');
            $data_sessions = u::calculatorSessionsByNumberOfSessions($start_date, data_get($request,'session'), $holidays, $arr_day);
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
}
