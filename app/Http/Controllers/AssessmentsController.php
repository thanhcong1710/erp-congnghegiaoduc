<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use App\Models\LogStudents;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssessmentsController extends Controller
{
    public function list(Request $request)
    {
        $keyword = isset($request->keyword) ? $request->keyword : '';
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];

        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        $cond = " a.status = 1 ";
        
        if ($keyword !== '') {
            $cond .= " AND (s.name LIKE '%$keyword%' OR s.lms_code LIKE '%$keyword%')";
        }
        $cond .= " AND s.branch_id IN (" . Auth::user()->getBranchesHasUser().")";

        if (!empty($branch_id)) {
            $cond .= " AND s.branch_id IN (".implode(",",$branch_id).")";
        }
        
        $order_by = " ORDER BY a.id DESC ";

        $total = u::first("SELECT count(a.id) AS total 
            FROM assessments AS a LEFT JOIN students AS s On s.id= a.student_id  WHERE $cond");
        
        $list = u::query("SELECT a.* ,s.name, s.lms_code
            FROM assessments AS a LEFT JOIN students AS s On s.id= a.student_id 
            WHERE $cond $order_by $limitation");
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function searchStudent(Request $request){
        $keyword = $request->keyword;
        $branch_id = $request->branch_id;
        $data = u::query("SELECT s.name, s.lms_code, s.gud_name1, s.gud_mobile1, s.gud_email1, s.address,
                s.id AS student_id, CONCAT(s.name, ' - ', s.lms_code) AS label,
                (SELECT name FROM products WHERE id=c.product_id) AS product_name,
                (SELECT name FROM programs WHERE id=c.program_id) AS program_name,
                (SELECT cls_name FROM classes WHERE id=c.class_id) AS class_name,
                (SELECT name FROM tuition_fee WHERE id=c.tuition_fee_id) AS tuition_fee_name,
                c.product_id, c.class_id, c.id AS contract_id, c.enrolment_start_date, c.enrolment_last_date 
            FROM contracts AS c LEFT JOIN students AS s ON c.student_id=s.id 
                WHERE c.branch_id= $branch_id AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%')
                AND c.status=6 AND c.enrolment_last_date >= CURRENT_DATE ");
        return response()->json($data);
    }

    public function add(Request $request)
    {
        $assessment_id = u::insertSimpleRow(array(
            'student_id' => data_get($request, 'assessment.student_id'),
            'title' => data_get($request, 'assessment.title'), 
            'description' => data_get($request, 'assessment.description'),
            'created_at'=>date('Y-m-d H:i:s'),
            'creator_id'=>Auth::user()->id,
            'status' =>  1,
            'meta_data' => json_encode($request->input())
        ), 'assessments');

        $result = array(
            'status' => 1,
            'message' => 'Thêm mới nhận xét & đánh giá thành công'
        );
        return response()->json($result);
    }

    public function delete(Request $request){
        $assessment_id = data_get($request, 'assessment_id');
        u::updateSimpleRow(array(
            'status' => 0,
            'updated_at'=>date('Y-m-d H:i:s'),
            'updator_id'=>Auth::user()->id,
        ),array('id'=>$assessment_id), 'assessments');
        $result = array(
            'status' => 1,
            'message' => 'Xóa nhận xét & đánh giá thành công.'
        );
        return response()->json($result);
    } 

    public function show(Request $request,$id)
    {
        $data = u::first("SELECT a.*, (SELECT name FROM branches WHERE id=a.branch_id) AS branch_name FROM assessments AS a WHERE id = $id");
        $data->meta_data = $data->meta_data ? json_decode($data->meta_data) : '';
        return response()->json($data);
    }

    public function update(Request $request)
    {
        u::updateSimpleRow(array(
            'title' => data_get($request, 'title'), 
            'description' => data_get($request, 'description'),
            'updated_at'=>date('Y-m-d H:i:s'),
            'updator_id'=>Auth::user()->id,
        ),array('id'=>data_get($request, 'id')), 'assessments');
        $result = array(
            'status' => 1,
            'message' => 'Cập nhật thông tin nhận xét & đánh giá thành công'
        );
        return response()->json($result);
    }
}
