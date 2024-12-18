<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use App\Models\LogStudents;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class B2BCampaignsController extends Controller
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
            FROM b2b_campaigns AS s WHERE $cond");
        
        $list = u::query("SELECT s.*
            FROM b2b_campaigns AS s 
            WHERE $cond $order_by $limitation");
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function add(Request $request)
    {
        $tuition_fees = data_get($request, 'tuition_fees');
        $list_tuition_fee = "";
        foreach($tuition_fees AS $row){
            $list_tuition_fee.= $list_tuition_fee ? ",".data_get($row,'tuition_fee.id',0) : data_get($row,'tuition_fee.id',0);
        }
        $campaign_id = u::insertSimpleRow(array(
            'title' => data_get($request, 'campaign.title'),
            'b2b_source_id' => data_get($request, 'campaign.b2b_source_id'),
            'start_date' => data_get($request, 'campaign.start_date'), 
            'end_date' => data_get($request, 'campaign.end_date'), 
            'note' => data_get($request, 'campaign.note'),
            'status' => data_get($request, 'campaign.status'),
            'created_at'=>date('Y-m-d H:i:s'),
            'creator_id'=>Auth::user()->id,
            'list_tuition_fee' => $list_tuition_fee,
            'meta_data' => json_encode($tuition_fees)
        ), 'b2b_campaigns');
        
        $result = array(
            'status' => 1,
            'message' => 'Thêm mới chính sách thành công'
        );
        return response()->json($result);
    }

    public function show(Request $request,$id)
    {
        $campaign = u::first("SELECT * FROM b2b_campaigns WHERE id = $id");
        $tuition_fees = data_get($campaign, 'meta_data')? json_decode(data_get($campaign, 'meta_data'),true) : [];
        
        return response()->json([
            'campaign'=>$campaign,
            'tuition_fees' => $tuition_fees
        ]);
    }

    public function update(Request $request)
    {
        $tuition_fees = data_get($request, 'tuition_fees');
        $list_tuition_fee = "";
        foreach($tuition_fees AS $row){
            $list_tuition_fee.= $list_tuition_fee ? ",".data_get($row,'tuition_fee.id',0) : data_get($row,'tuition_fee.id',0);
        }
        u::updateSimpleRow(array(
            'title' => data_get($request, 'campaign.title'),
            'b2b_source_id' => data_get($request, 'campaign.b2b_source_id'),
            'start_date' => data_get($request, 'campaign.start_date'), 
            'end_date' => data_get($request, 'campaign.end_date'), 
            'note' => data_get($request, 'campaign.note'),
            'status' => data_get($request, 'campaign.status'),
            'list_tuition_fee' => $list_tuition_fee,
            'meta_data' => json_encode($tuition_fees),
            'updated_at'=>date('Y-m-d H:i:s'),
            'updator_id'=>Auth::user()->id,
        ),array('id'=>data_get($request, 'campaign.id')), 'b2b_campaigns');

        $result = array(
            'status' => 1,
            'message' => 'Cập nhật chính sách thành công'
        );
        return response()->json($result);
    }
}
