<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use App\Models\LogStudents;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscountCodesController extends Controller
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
            FROM discount_codes AS b WHERE $cond");
        
        $list = u::query("SELECT b.*
            FROM discount_codes AS b 
            WHERE $cond $order_by $limitation");
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function add(Request $request)
    {
        $tuition_fees = data_get($request, 'tuition_fees');
        $tmp_tuition_fees = '';
        foreach($tuition_fees AS $tui){
            $tmp_tuition_fees.= $tmp_tuition_fees ? ','.data_get($tui, 'id') : data_get($tui, 'id');
        }
        $discount_code_id = u::insertSimpleRow(array(
            'code' => data_get($request, 'discount_code.code'),
            'name' => data_get($request, 'discount_code.name'), 
            'percent' => data_get($request, 'discount_code.percent'),
            'start_date' => data_get($request, 'discount_code.start_date'),
            'end_date' => data_get($request, 'discount_code.end_date'),
            'price' => (int)data_get($request, 'discount_code.price'),
            'discount' => (int)data_get($request, 'discount_code.discount'),
            'bonus_sessions' => (int)data_get($request, 'discount_code.bonus_sessions'),
            'created_at'=>date('Y-m-d H:i:s'),
            'creator_id'=>Auth::user()->id,
            'status' =>  data_get($request, 'discount_code.status'),
            'fee_ids' => $tmp_tuition_fees
        ), 'discount_codes');

        $result = array(
            'status' => 1,
            'message' => 'Thêm mới mã chiết khấu thành công'
        );
        return response()->json($result);
    }

    public function delete(Request $request){
        $discount_code_id = data_get($request, 'discount_code_id');
        u::query("DELETE FROM discount_codes WHERE id=$discount_code_id");
        $result = array(
            'status' => 1,
            'message' => 'Xóa mã chiết khấu thành công.'
        );
        return response()->json($result);
    } 

    public function show(Request $request,$id)
    {
        $data = u::first("SELECT * FROM discount_codes WHERE id = $id");
        if($data->fee_ids){
            $tuition_fees = u::query("SELECT t.name, t.id, t.available_date, t.expired_date,t.status,
                (SELECT name FROM products WHERE id=t.product_id) AS product_name   
            FROM tuition_fee AS t WHERE t.id IN ($data->fee_ids)");
        }else{
            $tuition_fees = [];
        }
        return response()->json([
            'tuition_fees' => $tuition_fees,
            'discount_code' =>$data
        ]);
    }

    public function update(Request $request)
    {
        $tuition_fees = data_get($request, 'tuition_fees');
        $tmp_tuition_fees = '';
        foreach($tuition_fees AS $tui){
            $tmp_tuition_fees.= $tmp_tuition_fees ? ','.data_get($tui, 'id') : data_get($tui, 'id');
        }
        u::updateSimpleRow(array(
            'code' => data_get($request, 'discount_code.code'),
            'name' => data_get($request, 'discount_code.name'), 
            'percent' => data_get($request, 'discount_code.percent'),
            'start_date' => data_get($request, 'discount_code.start_date'),
            'end_date' => data_get($request, 'discount_code.end_date'),
            'price' => (int)data_get($request, 'discount_code.price'),
            'discount' => (int)data_get($request, 'discount_code.discount'),
            'bonus_sessions' => (int)data_get($request, 'discount_code.bonus_sessions'),
            'updated_at'=>date('Y-m-d H:i:s'),
            'updator_id'=>Auth::user()->id,
            'status' =>  data_get($request, 'discount_code.status'),
            'fee_ids' => $tmp_tuition_fees
        ),array('id'=>data_get($request, 'discount_code.id')), 'discount_codes');
        $result = array(
            'status' => 1,
            'message' => 'Cập nhật thông tin mã chiết khấu thành công'
        );
        return response()->json($result);
    }
}
