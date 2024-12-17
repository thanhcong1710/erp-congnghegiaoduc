<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use App\Models\LogStudents;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CampaignsController extends Controller
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
            FROM campaigns AS s WHERE $cond");
        
        $list = u::query("SELECT s.*
            FROM campaigns AS s 
            WHERE $cond $order_by $limitation");
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function add(Request $request)
    {
        $campaign_id = u::insertSimpleRow(array(
            'title' => data_get($request, 'campaign.title'),
            'type' => data_get($request, 'campaign.type'),
            'start_date' => data_get($request, 'campaign.start_date'), 
            'end_date' => data_get($request, 'campaign.end_date'),  
            'amount' => data_get($request, 'campaign.amount'),
            'note' => data_get($request, 'campaign.note'),
            'status' => data_get($request, 'campaign.status'),
            'voucher_amount' => data_get($request, 'campaign.voucher_amount'),  
            'voucher_num' => data_get($request, 'campaign.voucher_num'),  
            'voucher_bonus_sessions' => data_get($request, 'campaign.voucher_bonus_sessions'), 
            'note' => data_get($request, 'campaign.note'),  
            'created_at'=>date('Y-m-d H:i:s'),
            'creator_id'=>Auth::user()->id,
        ), 'campaigns');
        u::insertSimpleRow(array(
            'name' => data_get($request, 'campaign.title'),
            'status' => data_get($request, 'campaign.status'),
            'campaign_id' => $campaign_id,
            'source_id' => 1,
            'created_at'=>date('Y-m-d H:i:s'),
            'creator_id'=>Auth::user()->id,
        ), 'source_detail');
        if (data_get($request, 'campaign.type') == 4 && data_get($request, 'campaign.voucher_num')){
            for($i=0;$i<data_get($request, 'campaign.voucher_num');$i++){
                $voucher_code = u::generateRandomAlphanumeric(6);
                $check_exit = u::first("SELECT id FROM coupons WHERE code='$voucher_code'");
                while(!empty($check_exit)){
                    $voucher_code = u::generateRandomAlphanumeric(6);
                    $check_exit = u::first("SELECT id FROM coupons WHERE code='$voucher_code'");
                }
                u::insertSimpleRow(array(
                    'code' => $voucher_code,
                    'type' => 0,
                    'coupon_amount'=> data_get($request, 'campaign.voucher_amount'),
                    'coupon_session'=> data_get($request, 'campaign.voucher_bonus_sessions'),
                    'start_date'=> data_get($request, 'campaign.start_date'),
                    'end_date'=> data_get($request, 'campaign.end_date'),
                    'status' => 1,
                    'created_at'=>date('Y-m-d H:i:s'),
                    'creator_id'=>Auth::user()->id,
                    'campaign_id'=> $campaign_id
                ), 'coupons');
            }
        }

        $result = array(
            'status' => 1,
            'message' => 'Thêm mới chiến dịch thành công'
        );
        return response()->json($result);
    }

    public function show(Request $request,$id)
    {
        $campaign = u::first("SELECT * FROM campaigns WHERE id = $id");
        
        return response()->json([
            'campaign'=>$campaign,
        ]);
    }

    public function update(Request $request)
    {
        u::updateSimpleRow(array(
            'title' => data_get($request, 'campaign.title'),
            'type' => data_get($request, 'campaign.type'),
            'start_date' => data_get($request, 'campaign.start_date'), 
            'end_date' => data_get($request, 'campaign.end_date'),  
            'amount' => data_get($request, 'campaign.amount'),
            'note' => data_get($request, 'campaign.note'),
            'status' => data_get($request, 'campaign.status'),
            'voucher_amount' => data_get($request, 'campaign.voucher_amount'),  
            'voucher_num' => data_get($request, 'campaign.voucher_num'),  
            'voucher_bonus_sessions' => data_get($request, 'campaign.voucher_bonus_sessions'), 
            'note' => data_get($request, 'campaign.note'),  
            'updated_at'=>date('Y-m-d H:i:s'),
            'updator_id'=>Auth::user()->id,
        ),array('id'=>data_get($request, 'campaign.id')), 'campaigns');
        u::updateSimpleRow(array(
            'name' => data_get($request, 'campaign.title'),
            'status' => data_get($request, 'campaign.status'),
            'updated_at'=>date('Y-m-d H:i:s'),
            'updator_id'=>Auth::user()->id,
        ), array('campaign_id' => data_get($request, 'campaign.id'), 'source_id'=> 1), 'source_detail');

        if (data_get($request, 'campaign.type') == 4 && data_get($request, 'campaign.voucher_num')){
            $curr_count = u::first("SELECT count(id) As total FROM coupons WHERE campaign_id = ".data_get($request, 'campaign.id'));
            if($curr_count->total > data_get($request, 'campaign.voucher_num')){
                $tmp = $curr_count->total - data_get($request, 'campaign.voucher_num');
                u::query("DELETE FROM coupons WHERE campaign_id =  ".data_get($request, 'campaign.id')." ORDER BY id DESC LIMIT $tmp");
                u:: updateSimpleRow( array(
                    'coupon_amount'=> data_get($request, 'campaign.voucher_amount'),
                    'coupon_session'=> data_get($request, 'campaign.voucher_bonus_sessions'),
                    'start_date'=> data_get($request, 'campaign.start_date'),
                    'end_date'=> data_get($request, 'campaign.end_date'),
                    'updated_at'=>date('Y-m-d H:i:s'),
                    'updator_id'=>Auth::user()->id,
                ) ,array('campaign_id' => data_get($request, 'campaign.id'), 'type' => 0), 'coupons');
            }else {
                if($curr_count->total > 0){
                    u:: updateSimpleRow( array(
                        'coupon_amount'=> data_get($request, 'campaign.voucher_amount'),
                        'coupon_session'=> data_get($request, 'campaign.voucher_bonus_sessions'),
                        'start_date'=> data_get($request, 'campaign.start_date'),
                        'end_date'=> data_get($request, 'campaign.end_date'),
                        'updated_at'=>date('Y-m-d H:i:s'),
                        'updator_id'=>Auth::user()->id,
                    ) ,array('campaign_id' => data_get($request, 'campaign.id'), 'type'=> 0), 'coupons');
                }
                for($i=0;$i < (data_get($request, 'campaign.voucher_num') - $curr_count->total);$i++){
                    $voucher_code = u::generateRandomAlphanumeric(6);
                    $check_exit = u::first("SELECT id FROM coupons WHERE code='$voucher_code'");
                    while(!empty($check_exit)){
                        $voucher_code = u::generateRandomAlphanumeric(6);
                        $check_exit = u::first("SELECT id FROM coupons WHERE code='$voucher_code'");
                    }
                    u::insertSimpleRow(array(
                        'code' => $voucher_code,
                        'type' => 0,
                        'coupon_amount'=> data_get($request, 'campaign.voucher_amount'),
                        'coupon_session'=> data_get($request, 'campaign.voucher_bonus_sessions'),
                        'start_date'=> data_get($request, 'campaign.start_date'),
                        'end_date'=> data_get($request, 'campaign.end_date'),
                        'status' => 1,
                        'created_at'=>date('Y-m-d H:i:s'),
                        'creator_id'=>Auth::user()->id,
                        'campaign_id'=> data_get($request, 'campaign.id')
                    ), 'coupons');
                }   
            }
        }

        $result = array(
            'status' => 1,
            'message' => 'Cập nhật chiến dịch thành công'
        );
        return response()->json($result);
    }
}
