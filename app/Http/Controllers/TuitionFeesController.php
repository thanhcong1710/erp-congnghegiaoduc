<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use App\Models\LogStudents;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TuitionFeesController extends Controller
{
    public function list(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $keyword = isset($request->keyword) ? $request->keyword : '';
        $status = isset($request->status) ? $request->status : [];

        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        $cond = " 1 ";
        if (!empty($branch_id)) {
            $tmp = "";
            foreach($branch_id AS $b){
                $tmp.= $tmp ? " OR ( t.branch_id LIKE '$b,%' OR t.branch_id LIKE '%,$b,%' OR t.branch_id LIKE '%,$b' OR t.branch_id = '$b' ) " : " ( t.branch_id LIKE '$b,%' OR t.branch_id LIKE '%,$b,%' OR t.branch_id LIKE '%,$b' OR t.branch_id = '$b' ) ";
            }
            if($tmp){
                $cond .= " AND ($tmp) ";
            }
        }
        
        if ($keyword !== '') {
            $cond .= " AND t.name LIKE '%$keyword%' ";
        }
        if (!empty($status)) {
            $cond .= " AND t.status IN (".implode(",",$status).")";
        }
        
        $order_by = " ORDER BY t.id DESC ";

        $total = u::first("SELECT count(t.id) AS total 
            FROM tuition_fee AS t WHERE $cond");
        
        $list = u::query("SELECT t.*, (SELECT name FROM products WHERE id= t.product_id) AS product_name,
                (SELECT count(id) FROM contracts WHERE tuition_fee_id=t.id) AS disabled_delete
            FROM tuition_fee AS t 
            WHERE $cond $order_by $limitation");
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function add(Request $request)
    {
        $tuition_fee = data_get($request, 'tuition_fee');
        $branches = data_get($request, 'branches');
        $branch_id = "";
        foreach($branches AS $row){
            if(data_get($row, 'selected') == true){
                $branch_id.= $branch_id ? ",".data_get($row,'id') : data_get($row,'id');
            }
        }
        $tuition_fee_id = u::insertSimpleRow(array(
            'name' => data_get($tuition_fee, 'name'),
            'product_id' => data_get($tuition_fee, 'product_id'), 
            'session' => data_get($tuition_fee, 'session'),
            'price' => data_get($tuition_fee, 'price'),
            'receivable' => data_get($tuition_fee, 'receivable'),
            'number_of_months' => data_get($tuition_fee, 'number_of_months'),
            'type_contract' => data_get($tuition_fee, 'type_contract'),
            'available_date' => data_get($tuition_fee, 'available_date'),
            'expired_date' => data_get($tuition_fee, 'expired_date'),
            'branch_id' => $branch_id,
            'created_at'=>date('Y-m-d H:i:s'),
            'creator_id'=>Auth::user()->id,
            'status' =>  data_get($tuition_fee, 'status'),
        ), 'tuition_fee');

        $tuition_fees_relation = data_get($request, 'tuition_fees_relation');
        foreach($tuition_fees_relation AS $row){
            u::insertSimpleRow(array(
                'tuition_fee_id' => $tuition_fee_id,
                'exchange_tuition_fee_id' => data_get($row, 'id'), 
                'tuition_fee_name' => data_get($tuition_fee, 'name'),
                'exchange_tuition_fee_name' => data_get($row, 'name'),
                'status' =>  1,
            ), 'tuition_fee_relation');
        }

        $result = array(
            'status' => 1,
            'message' => 'Thêm mới gói phí thành công'
        );
        return response()->json($result);
    }

    

    public function delete(Request $request){
        $tuition_fee_id = data_get($request, 'tuition_fee_id');
        u::query("DELETE FROM tuition_fee WHERE id=$tuition_fee_id");
        $result = array(
            'status' => 1,
            'message' => 'Xóa gói phí thành công.'
        );
        return response()->json($result);
    } 

    public function show(Request $request,$id)
    {
        $tuition_fee = u::first("SELECT * FROM tuition_fee WHERE id = $id");
        $tuition_fees_relation = u::query("SELECT t.*, (SELECT name FROM products WHERE id=t.product_id) AS product_name
            FROM tuition_fee_relation AS r LEFT JOIN tuition_fee AS t ON t.id=r.exchange_tuition_fee_id 
            WHERE r.tuition_fee_id=$id AND r.status=1");
        $branches = u::query("SELECT b.* FROM branches AS b WHERE b.status=1");
        foreach($branches  AS $k => $row){
            if(in_array($row->id, explode(',', data_get($tuition_fee, 'branch_id')))){
                $branches[$k]->selected = true;
            }else{
                $branches[$k]->selected = false;
            }
        }
        $data = array(
            'branches' => $branches,
            'tuition_fee' => $tuition_fee,
            'tuition_fees_relation' => $tuition_fees_relation
        );
        return response()->json($data);
    }

    public function update(Request $request)
    {
        $tuition_fee = data_get($request, 'tuition_fee');
        $branches = data_get($request, 'branches');
        $branch_id = "";
        foreach($branches AS $row){
            if(data_get($row, 'selected') == true){
                $branch_id.= $branch_id ? ",".data_get($row,'id') : data_get($row,'id');
            }
        }
        u::updateSimpleRow(array(
            'name' => data_get($tuition_fee, 'name'),
            'product_id' => data_get($tuition_fee, 'product_id'), 
            'session' => data_get($tuition_fee, 'session'),
            'price' => data_get($tuition_fee, 'price'),
            'receivable' => data_get($tuition_fee, 'receivable'),
            'number_of_months' => data_get($tuition_fee, 'number_of_months'),
            'type_contract' => data_get($tuition_fee, 'type_contract'),
            'available_date' => data_get($tuition_fee, 'available_date'),
            'expired_date' => data_get($tuition_fee, 'expired_date'),
            'branch_id' => $branch_id,
            'updated_at'=>date('Y-m-d H:i:s'),
            'updator_id'=>Auth::user()->id,
            'status' =>  data_get($tuition_fee, 'status'),
        ),array('id'=>data_get($tuition_fee, 'id')), 'tuition_fee');

        u::updateSimpleRow(array('status'=>0),array('tuition_fee_id'=>data_get($tuition_fee, 'id')), 'tuition_fee_relation');
        $tuition_fees_relation = data_get($request, 'tuition_fees_relation');
        foreach($tuition_fees_relation AS $row){
            $ttr_info = u::first("SELECT id FROM tuition_fee_relation WHERE tuition_fee_id= ".data_get($tuition_fee, 'id')." 
                AND exchange_tuition_fee_id=".data_get($row, 'id'));
            if($ttr_info){
                u::updateSimpleRow(array(
                    'status' =>  1
                ), array('id'=>data_get($ttr_info, 'id')), 'tuition_fee_relation');
            }else{
                u::insertSimpleRow(array(
                    'tuition_fee_id' => data_get($tuition_fee, 'id'),
                    'exchange_tuition_fee_id' => data_get($row, 'id'), 
                    'tuition_fee_name' => data_get($tuition_fee, 'name'),
                    'exchange_tuition_fee_name' => data_get($row, 'name'),
                    'status' =>  1,
                ), 'tuition_fee_relation');
            }
        }
        
        $result = array(
            'status' => 1,
            'message' => 'Cập nhật thông tin gói phí thành công'
        );
        return response()->json($result);
    }
}
