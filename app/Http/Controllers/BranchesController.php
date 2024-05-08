<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use App\Models\LogStudents;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BranchesController extends Controller
{
    public function list(Request $request)
    {
        $keyword = isset($request->keyword) ? $request->keyword : '';
        $status = isset($request->status) ? $request->status : [];

        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        $cond = " 1 ";
        
        if ($keyword !== '') {
            $cond .= " AND (b.name LIKE '%$keyword%' OR b.code LIKE '%$keyword%')";
        }
        if (!empty($status)) {
            $cond .= " AND b.status IN (".implode(",",$status).")";
        }
        
        $order_by = " ORDER BY b.id DESC ";

        $total = u::first("SELECT count(b.id) AS total 
            FROM branches AS b WHERE $cond");
        
        $list = u::query("SELECT b.*, (SELECT count(id) FROM contracts WHERE branch_id=b.id) AS disabled_delete
            FROM branches AS b 
            WHERE $cond $order_by $limitation");
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function add(Request $request)
    {
        $branch_id = u::insertSimpleRow(array(
            'name' => data_get($request, 'name'),
            'code' => data_get($request, 'code'), 
            'opened_date' => data_get($request, 'opened_date') ? data_get($request, 'opened_date') : null,
            'hotline' => data_get($request, 'hotline', null),
            'email' => data_get($request, 'email', null),
            'created_at'=>date('Y-m-d H:i:s'),
            'creator_id'=>Auth::user()->id,
            'status' =>  data_get($request, 'status'),
        ), 'branches');

        $result = array(
            'status' => 1,
            'message' => 'Thêm mới trung tâm thành công'
        );
        return response()->json($result);
    }

    

    public function delete(Request $request){
        $branch_id = data_get($request, 'branch_id');
        u::query("DELETE FROM branches WHERE id=$branch_id");
        $result = array(
            'status' => 1,
            'message' => 'Xóa trung tâm thành công.'
        );
        return response()->json($result);
    } 

    public function show(Request $request,$id)
    {
        $data = u::first("SELECT * FROM branches WHERE id = $id");
        return response()->json($data);
    }

    public function update(Request $request)
    {
        u::updateSimpleRow(array(
            'name' => data_get($request, 'name'),
            'code' => data_get($request, 'code'), 
            'opened_date' => data_get($request, 'opened_date') ? data_get($request, 'opened_date') : null,
            'hotline' => data_get($request, 'hotline', null),
            'email' => data_get($request, 'email', null),
            'status' =>  data_get($request, 'status'),
            'updated_at'=>date('Y-m-d H:i:s'),
            'updator_id'=>Auth::user()->id,
        ),array('id'=>data_get($request, 'id')), 'branches');
        $result = array(
            'status' => 1,
            'message' => 'Cập nhật thông tin trung tâm thành công'
        );
        return response()->json($result);
    }
}
