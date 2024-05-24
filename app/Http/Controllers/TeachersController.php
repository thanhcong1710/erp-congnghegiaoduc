<?php

namespace App\Http\Controllers;

use App\Enums\SystemCode;
use App\User;
use App\Http\Controllers\Controller;
use App\Models\LogStudents;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeachersController extends Controller
{
    public function list(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $keyword = isset($request->keyword) ? $request->keyword : '';
        $end_date = isset($request->end_date) ? $request->end_date : '';
        $start_date = isset($request->start_date) ? $request->start_date : '';

        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        $cond = " s.status = 1 ";
        $cond .= " AND s.branch_id IN (" . Auth::user()->getBranchesHasUser().") ";

        if (!empty($branch_id)) {
            $cond .= " AND (s.branch_id IN (".implode(",",$branch_id).") ";
        }
        if ($keyword !== '') {
            $cond .= " AND (cl.cls_name LIKE '%$keyword%' OR ut.name LIKE '%$keyword%' OR uc.name LIKE '%$keyword%' 
                OR ut.hrm_id LIKE '%$keyword%' OR uc.hrm_id LIKE '%$keyword%') ";
        }
        if ($end_date !== '') {
            $cond .= " AND s.class_date <= '$end_date'";
        }
        if ($start_date !== '') {
            $cond .= " AND s.class_date >= '$start_date'";
        }
        
        $order_by = " ORDER BY s.class_date DESC ";

        $total = u::first("SELECT count(s.id) AS total 
            FROM schedules AS s 
                LEFT JOIN classes AS cl ON cl.id = s.class_id
                LEFT JOIN users AS ut ON ut.id=s.teacher_id
                LEFT JOIN users AS uc ON uc.id=s.cm_id
            WHERE $cond");
        
        $list = u::query("SELECT s.id, s.class_date, cl.cls_name AS class_name,
                CONCAT(ut.name, ' - ', ut.hrm_id) AS teacher_name,
                CONCAT(uc.name, ' - ', uc.hrm_id) AS cm_name,
                (SELECT name FROM branches WHERE id = s.branch_id) AS branch_name
            FROM schedules AS s 
                LEFT JOIN classes AS cl ON cl.id = s.class_id
                LEFT JOIN users AS ut ON ut.id=s.teacher_id
                LEFT JOIN users AS uc ON uc.id=s.cm_id
            WHERE $cond $order_by $limitation");
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function show(Request $request,$id)
    {
        $data = u::first("SELECT s.*, (SELECT name FROM branches WHERE id=s.branch_id) AS branch_name,
                (SELECT cls_name FROM classes WHERE id=s.class_id) AS class_name
            FROM schedules AS s WHERE s.id=$id");
        return response()->json($data);
    }

    public function update(Request $request)
    {
        u::updateSimpleRow(array(
            'teacher_id' => data_get($request, 'teacher_id'),
            'cm_id' => data_get($request, 'cm_id'),
            'updated_at'=>date('Y-m-d H:i:s'),
            'updator_id'=>Auth::user()->id,
        ),array('id'=>data_get($request, 'id')), 'schedules');
        $result = array(
            'status' => 1,
            'message' => 'Cập nhật thông tin buổi dạy thành công'
        );
        return response()->json($result);
    }
}
