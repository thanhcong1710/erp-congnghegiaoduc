<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use App\Models\LogStudents;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoomsController extends Controller
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
            $cond .= " AND (r.name LIKE '%$keyword%' OR r.code LIKE '%$keyword%')";
        }
        if (!empty($status)) {
            $cond .= " AND r.status IN (".implode(",",$status).")";
        }
        
        $order_by = " ORDER BY r.id DESC ";

        $total = u::first("SELECT count(r.id) AS total 
            FROM rooms AS r WHERE $cond");
        
        $list = u::query("SELECT r.*, (SELECT count(id) FROM sessions WHERE room_id=r.id) AS disabled_delete,
                (SELECT name FROM branches WHERE id=r.branch_id) AS branch_name
            FROM rooms AS r 
            WHERE $cond $order_by $limitation");
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function add(Request $request)
    {
        $room_id = u::insertSimpleRow(array(
            'branch_id' => data_get($request, 'branch_id'),
            'name' => data_get($request, 'name'),
            'code' => data_get($request, 'code'), 
            'created_at'=>date('Y-m-d H:i:s'),
            'creator_id'=>Auth::user()->id,
            'status' =>  data_get($request, 'status'),
        ), 'rooms');

        $result = array(
            'status' => 1,
            'message' => 'Thêm mới phòng học thành công'
        );
        return response()->json($result);
    }

    

    public function delete(Request $request){
        $room_id = data_get($request, 'room_id');
        u::query("DELETE FROM rooms WHERE id=$room_id");
        $result = array(
            'status' => 1,
            'message' => 'Xóa phòng học thành công.'
        );
        return response()->json($result);
    } 

    public function show(Request $request,$id)
    {
        $data = u::first("SELECT * FROM rooms WHERE id = $id");
        return response()->json($data);
    }

    public function update(Request $request)
    {
        u::updateSimpleRow(array(
            'branch_id' => data_get($request, 'branch_id'),
            'name' => data_get($request, 'name'),
            'code' => data_get($request, 'code'), 
            'status' =>  data_get($request, 'status'),
            'updated_at'=>date('Y-m-d H:i:s'),
            'updator_id'=>Auth::user()->id,
        ),array('id'=>data_get($request, 'id')), 'rooms');
        $result = array(
            'status' => 1,
            'message' => 'Cập nhật thông tin phòng học thành công'
        );
        return response()->json($result);
    }
}
