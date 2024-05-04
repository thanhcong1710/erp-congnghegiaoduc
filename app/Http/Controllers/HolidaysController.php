<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use App\Models\LogStudents;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HolidaysController extends Controller
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
            FROM public_holiday AS s WHERE $cond");
        
        $list = u::query("SELECT s.*, IF(s.start_date > CURRENT_DATE , 0, 1) AS disabled_delete
            FROM public_holiday AS s 
            WHERE $cond $order_by $limitation");
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function add(Request $request)
    {
        $branch_id = '';
        foreach($request->branches AS $branch){
            if(data_get($branch,'selected')){
                $branch_id.= $branch_id ? ','. data_get($branch,'id') : data_get($branch,'id');
            }
        }
        $products = '';
        foreach($request->products AS $product){
            if(data_get($product,'selected')){
                $products.= $products ? ','. data_get($product,'id') : data_get($product,'id');
            }
        }
        $holiday_id = u::insertSimpleRow(array(
            'name' => data_get($request, 'holiday.name'),
            'start_date' => data_get($request, 'holiday.start_date'),
            'end_date' => data_get($request, 'holiday.end_date'), 
            'products' => $products, 
            'branch_id' => $branch_id, 
            'created_at'=>date('Y-m-d H:i:s'),
            'creator_id'=>Auth::user()->id,
            'status' =>  data_get($request, 'holiday.status'),
        ), 'public_holiday');

        $result = array(
            'status' => 1,
            'message' => 'Thêm mới ngày nghỉ lễ thành công'
        );
        return response()->json($result);
    }

    public function delete(Request $request){
        $holiday_id = data_get($request, 'holiday_id');
        u::query("DELETE FROM public_holiday WHERE id=$holiday_id");
        $result = array(
            'status' => 1,
            'message' => 'Xóa ngày nghỉ lễ thành công.'
        );
        return response()->json($result);
    } 

    public function show(Request $request,$id)
    {
        $holiday = u::first("SELECT * FROM public_holiday WHERE id = $id");
        $branches = u::query("SELECT b.*, 0 AS selected 
            FROM branches AS b WHERE b.status=1");
        foreach($branches AS $k=> $branch){
            if(in_array($branch->id, explode(',',$holiday->branch_id))){
                $branches[$k]->selected = true;
            }
        }
        $products = u::query("SELECT p.*, 0 AS selected 
            FROM products AS p WHERE p.status=1");
        foreach($products AS $k=> $product){
            if(in_array($product->id, explode(',',$holiday->products))){
                $products[$k]->selected = true;
            }
        }
        return response()->json([
            'holiday'=>$holiday,
            'branches'=>$branches,
            'products'=>$products,
        ]);
    }

    public function update(Request $request)
    {
        $branch_id = '';
        foreach($request->branches AS $branch){
            if(data_get($branch,'selected')){
                $branch_id.= $branch_id ? ','. data_get($branch,'id') : data_get($branch,'id');
            }
        }
        $products = '';
        foreach($request->products AS $product){
            if(data_get($product,'selected')){
                $products.= $products ? ','. data_get($product,'id') : data_get($product,'id');
            }
        }
        u::updateSimpleRow(array(
            'name' => data_get($request, 'holiday.name'),
            'start_date' => data_get($request, 'holiday.start_date'),
            'end_date' => data_get($request, 'holiday.end_date'), 
            'products' => $products, 
            'branch_id' => $branch_id,  
            'status' =>  data_get($request, 'holiday.status'),
            'updated_at'=>date('Y-m-d H:i:s'),
            'updator_id'=>Auth::user()->id,
        ),array('id'=>data_get($request, 'holiday.id')), 'public_holiday');
        $result = array(
            'status' => 1,
            'message' => 'Cập nhật thông tin ngày nghỉ lễ thành công'
        );
        return response()->json($result);
    }
}
