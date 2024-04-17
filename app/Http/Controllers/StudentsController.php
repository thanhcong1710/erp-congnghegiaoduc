<?php

namespace App\Http\Controllers;

use App\Models\LogParents;
use App\Models\LogStudents;
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

    //LMS
    public function list(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $status = isset($request->status) ? $request->status : [];
        $keyword = isset($request->keyword) ? $request->keyword : '';

        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        $cond = " s.status > 0 ";
        $cond .= " AND s.branch_id IN (" . Auth::user()->getBranchesHasUser().")";
        
        if (!empty($status)) {
            $cond .= " AND s.status IN (".implode(",",$status).")";
        }
        if (!empty($branch_id)) {
            $cond .= " AND s.branch_id IN (".implode(",",$branch_id).")";
        }
        
        if ($keyword !== '') {
            $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%' OR s.gud_name1 LIKE '%$keyword%' OR s.gud_mobile1 LIKE '%$keyword%' OR s.gud_mobile2 LIKE '%$keyword%') ";
        }
        
        $order_by = " ORDER BY s.id DESC ";

        $total = u::first("SELECT count(s.id) AS total FROM students AS s WHERE $cond");
        
        $list = u::query("SELECT s.*
            FROM students AS s
            WHERE $cond $order_by $limitation");
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function show(Request $request,$student_id)
    {
        $data = u::first("SELECT s.*
            FROM students AS s WHERE s.id=$student_id");
        return response()->json($data);
    }

    public function update(Request $request)
    {
        $pre_student_info = u::first("SELECT * FROM students WHERE id = $request->id");
        $arr_name = u::explodeName(data_get($request, 'name'));
        $data_update = array(
            'name'=>$request->name,
            'firstname' => data_get($arr_name, 'firstname'),
            'midname' => data_get($arr_name, 'midname'),
            'lastname' => data_get($arr_name, 'lastname'),
            'date_of_birth'=>$request->date_of_birth,
            'gender' => $request->gender,
            'province_id' => $request->province_id,
            'district_id' => $request->district_id,
            'address' => $request->address,
            'school' => $request->school,
            'gud_name1' => $request->gud_name1,
            'gud_email1' => $request->gud_email1,
            'gud_birth_day1' => $request->gud_birth_day1,
            'gud_job1' => $request->gud_job1,
            'gud_name2' => $request->gud_name2,
            'gud_email2' => $request->gud_email2,
            'gud_mobile2' => $request->gud_mobile2,
            'gud_birth_day2' => $request->gud_birth_day2,
            'gud_job2' => $request->gud_job2,
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => Auth::user()->id,
        );
        $data = u::updateSimpleRow($data_update, array('id' => $request->id), 'students');
        LogStudents::logUpdateInfo($pre_student_info,$data_update,Auth::user()->id);
        $result =(object)array(
            'status'=>1,
            'message'=>'Cập nhật học sinh thành công'
        );
        return response()->json($result);
    }

    public function searchContract(Request $request){
        $keyword = $request->keyword;
        $branch_id = $request->branch_id;
        $data = u::query("SELECT s.name, s.lms_code, s.gud_name1, s.gud_mobile1, s.gud_email1, s.address,
                (SELECT name FROM branches WHERE id =t.branch_id) AS student_branch_name,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id =t.ec_id) AS ec_name,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id =t.ec_leader_id) AS ec_leader_name,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id =t.ceo_branch_id) AS ceo_branch_name, 
                s.id AS student_id, CONCAT(s.name, ' - ', s.lms_code) AS label
            FROM students AS s LEFT JOIN term_student_user AS t ON t.student_id=s.id AND t.status=1 
                WHERE t.branch_id= $branch_id AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%')");
        return response()->json($data);
    } 
}
