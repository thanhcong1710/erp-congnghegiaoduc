<?php

namespace App\Http\Controllers;

use App\Models\LogParents;
use App\Providers\UtilityServiceProvider as u;
use App\Providers\CurlServiceProvider as curl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentsController extends Controller
{
    public function add(Request $request)
    {
        if($request->id>0){
            $pre_student_info = u::first("SELECT * FROM crm_students WHERE id= $request->id");
            $data_update = array(
                'parent_id'=>$request->parent_id,
                'name'=>$request->name,
                'gender' => $request->gender,
                'birthday' => $request->birthday,
                'school_level' => $request->school_level,
                'school' => $request->school,
                'note' => $request->note,
                'updated_at' => date('Y-m-d H:i:s'),
                'updator_id' => Auth::user()->id,
            );
            u::updateSimpleRow($data_update,array('id'=>$request->id), 'crm_students');
            LogParents::logUpdateStudentInfo($pre_student_info,$data_update,Auth::user()->id);
            $result =(object)array(
                'status'=>1,
                'message'=>'Cập nhật học sinh thành công'
            );
            return response()->json($result);
        }else{
            $data = u::insertSimpleRow(array(
                'parent_id'=>$request->parent_id,
                'name'=>$request->name,
                'gender' => $request->gender,
                'birthday' => $request->birthday,
                'school_level' => $request->school_level,
                'school' => $request->school,
                'note' => $request->note,
                'created_at' => date('Y-m-d H:i:s'),
                'creator_id' => Auth::user()->id,
            ), 'crm_students');
            $content = "Thêm mới học sinh: $request->name (ID: $data)";
            LogParents::logAdd($request->parent_id,$content,Auth::user()->id);
            $result =(object)array(
                'status'=>1,
                'message'=>'Thêm mới học sinh thành công'
            );
            return response()->json($result);
        }
    }
    public function getAllDataByParent($parent_id){
        $data = u::query("SELECT s.*, (SELECT name FROM users WHERE id=s.creator_id) AS creator_name,
                (SELECT name FROM branches WHERE id=s.checkin_branch_id) AS checkin_branch_name
            FROM crm_students AS s WHERE s.parent_id=$parent_id ORDER BY s.id DESC");
        return response()->json($data);
    }
    public function checkin(Request $request){
        $data_update = array(
            'checkin_at'=>$request->checkin_at,
            'checkin_owner_id' => $request->owner_id,
            'checkin_branch_id'=>$request->branch_id,
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => Auth::user()->id,
            'type_product'=>$request->type_product,
            'status' => 1, // 
        );
        u::updateSimpleRow($data_update,array('id'=>$request->student_id), 'crm_students');
        $result =(object)array(
            'status'=>1,
            'message'=>'Lưu thông tin checkin thành công'
        );
        return response()->json($result);
    }
}
