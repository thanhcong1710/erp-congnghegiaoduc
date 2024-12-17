<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use App\Models\LogStudents;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class B2BSourcesController extends Controller
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
            FROM b2b_sources AS b WHERE $cond");
        
        $list = u::query("SELECT b.*
            FROM b2b_sources AS b 
            WHERE $cond $order_by $limitation");
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function add(Request $request)
    {
        $b2b_source_id = u::insertSimpleRow(array(
            'title' => data_get($request, 'title'),
            'note' => data_get($request, 'note'), 
            'created_at'=>date('Y-m-d H:i:s'),
            'creator_id'=>Auth::user()->id,
            'status' =>  data_get($request, 'status'),
        ), 'b2b_sources');
        if($b2b_source_id) {
            u::insertSimpleRow(array(
                'name' => data_get($request, 'title'),
                'status' => data_get($request, 'status'),
                'campaign_id' => $b2b_source_id,
                'source_id' => 2,
                'created_at'=>date('Y-m-d H:i:s'),
                'creator_id'=>Auth::user()->id,
            ), 'source_detail');
        }

        $result = array(
            'status' => 1,
            'message' => 'Thêm mới đối tác thành công'
        );
        return response()->json($result);
    }

    public function delete(Request $request){
        $b2b_source_id = data_get($request, 'b2b_source_id');
        u::query("DELETE FROM b2b_sources WHERE id=$b2b_source_id");
        u::query("DELETE FROM source_detail WHERE source_id=2 AND campaign_id=$b2b_source_id");
        $result = array(
            'status' => 1,
            'message' => 'Xóa đối tác thành công.'
        );
        return response()->json($result);
    } 

    public function show(Request $request,$id)
    {
        $data = u::first("SELECT * FROM b2b_sources WHERE id = $id");
        return response()->json($data);
    }

    public function update(Request $request)
    {
        u::updateSimpleRow(array(
            'title' => data_get($request, 'title'),
            'note' => data_get($request, 'note'), 
            'status' =>  data_get($request, 'status'),
            'updated_at'=>date('Y-m-d H:i:s'),
            'updator_id'=>Auth::user()->id,
        ),array('id'=>data_get($request, 'id')), 'b2b_sources');
        u::updateSimpleRow(array(
            'name' => data_get($request, 'title'),
            'status' => data_get($request, 'status'),
            'updated_at'=>date('Y-m-d H:i:s'),
            'updator_id'=>Auth::user()->id,
        ), array('campaign_id' => data_get($request, 'id'), 'source_id'=> 2), 'source_detail');
        $result = array(
            'status' => 1,
            'message' => 'Cập nhật thông tin đối tác thành công'
        );
        return response()->json($result);
    }
}
