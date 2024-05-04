<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use App\Models\LogStudents;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShiftsController extends Controller
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
            $cond .= " AND (s.name LIKE '%$keyword%' OR s.code LIKE '%$keyword%')";
        }
        if (!empty($status)) {
            $cond .= " AND s.status IN (".implode(",",$status).")";
        }
        
        $order_by = " ORDER BY s.id DESC ";

        $total = u::first("SELECT count(s.id) AS total 
            FROM shifts AS s WHERE $cond");
        
        $list = u::query("SELECT s.*, (SELECT count(id) FROM sessions WHERE shift_id=s.id) AS disabled_delete
            FROM shifts AS s 
            WHERE $cond $order_by $limitation");
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function add(Request $request)
    {
        $shift_id = u::insertSimpleRow(array(
            'name' => data_get($request, 'name'),
            'start_time' => data_get($request, 'start_time'),
            'end_time' => data_get($request, 'end_time'), 
            'created_at'=>date('Y-m-d H:i:s'),
            'creator_id'=>Auth::user()->id,
            'status' =>  data_get($request, 'status'),
        ), 'shifts');

        $result = array(
            'status' => 1,
            'message' => 'Thêm mới ca học thành công'
        );
        return response()->json($result);
    }

    

    public function delete(Request $request){
        $shift_id = data_get($request, 'shift_id');
        u::query("DELETE FROM shifts WHERE id=$shift_id");
        $result = array(
            'status' => 1,
            'message' => 'Xóa ca học thành công.'
        );
        return response()->json($result);
    } 

    public function show(Request $request,$id)
    {
        $data = u::first("SELECT * FROM shifts WHERE id = $id");
        return response()->json($data);
    }

    public function update(Request $request)
    {
        u::updateSimpleRow(array(
            'name' => data_get($request, 'name'),
            'start_time' => data_get($request, 'start_time'),
            'end_time' => data_get($request, 'end_time'), 
            'status' =>  data_get($request, 'status'),
            'updated_at'=>date('Y-m-d H:i:s'),
            'updator_id'=>Auth::user()->id,
        ),array('id'=>data_get($request, 'id')), 'shifts');
        $result = array(
            'status' => 1,
            'message' => 'Cập nhật thông tin ca học thành công'
        );
        return response()->json($result);
    }
}
