<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use App\Models\LogStudents;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgramsController extends Controller
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
        $cond = " p.branch_id IN (" . Auth::user()->getBranchesHasUser().")";
        if (!empty($branch_id)) {
            $cond .= " AND p.branch_id IN (".implode(",",$branch_id).")";
        }
        
        if ($keyword !== '') {
            $cond .= " AND (p.name LIKE '%$keyword%' OR b.code LIKE '%$keyword%')";
        }
        if (!empty($status)) {
            $cond .= " AND p.status IN (".implode(",",$status).")";
        }
        
        $order_by = " ORDER BY p.id DESC ";

        $total = u::first("SELECT count(p.id) AS total 
            FROM programs AS p WHERE $cond");
        
        $list = u::query("SELECT p.*, (SELECT count(id) FROM contracts WHERE program_id=p.id) AS disabled_delete,
                (SELECT name FROM products WHERE id=p.product_id) AS product_name,
                (SELECT name FROM branches WHERE id=p.branch_id) AS branch_name,
                (SELECT name FROM program_subs WHERE id=p.program_sub_id) AS sub_program
            FROM programs AS p 
            WHERE $cond $order_by $limitation");
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function add(Request $request)
    {
        $program_id = u::insertSimpleRow(array(
            'product_id' => data_get($request, 'product_id'),
            'parent_id' => (int)data_get($request, 'parent_id'),
            'name' => data_get($request, 'name'),
            'code' => data_get($request, 'code'), 
            'description' => data_get($request, 'description'),
            'created_at'=>date('Y-m-d H:i:s'),
            'creator_id'=>Auth::user()->id,
            'status' =>  data_get($request, 'status'),
            'program_sub_id' => (int)data_get($request, 'program_sub_id'),
        ), 'programs');

        $result = array(
            'status' => 1,
            'message' => 'Thêm mới chương trình học thành công'
        );
        return response()->json($result);
    }

    

    public function delete(Request $request){
        $program_id = data_get($request, 'program_id');
        u::query("DELETE FROM programs WHERE id=$program_id");
        $result = array(
            'status' => 1,
            'message' => 'Xóa chương trình học thành công.'
        );
        return response()->json($result);
    } 

    public function show(Request $request,$id)
    {
        $data = u::first("SELECT * FROM programs WHERE id = $id");
        return response()->json($data);
    }

    public function update(Request $request)
    {
        u::updateSimpleRow(array(
            'product_id' => data_get($request, 'product_id'),
            'parent_id' => (int)data_get($request, 'parent_id', 0),
            'name' => data_get($request, 'name'),
            'code' => data_get($request, 'code'), 
            'description' => data_get($request, 'description'),
            'status' =>  data_get($request, 'status'),
            'program_sub_id' => (int)data_get($request, 'program_sub_id'),
            'updated_at'=>date('Y-m-d H:i:s'),
            'updator_id'=>Auth::user()->id,
        ),array('id'=>data_get($request, 'id')), 'programs');
        $result = array(
            'status' => 1,
            'message' => 'Cập nhật thông tin chương trình học thành công'
        );
        return response()->json($result);
    }

    public function sync(Request $request)
    {
        $list =u::query("SELECT * FROM program_subs WHERe status=1");
        foreach ($list as $program_sub) {
            u::query("UPDATE programs SET program_sub_id=".data_get($program_sub,'id')." 
                WHERE product_id=".data_get($program_sub,'product_id')." AND name LIKE '".data_get($program_sub,'name')."'");
        }
        u::query("UPDATE programs AS p LEFT JOIN programs AS parent ON p.parent_id=parent.id 
                SET p.program_sub_id=parent.program_sub_id
            WHERE parent.program_sub_id IS NOT NULL AND p.program_sub_id IS NULL");

        $result = array(
            'status' => 1,
            'message' => 'Đồng bộ chương trình học thành công'
        );
        return response()->json($result);
    }
}
