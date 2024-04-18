<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractsController extends Controller
{
    public function loadTuitionFee(Request $request)
    {
        $product_id = $request->product_id;
        $branch_id = $request->branch_id;
        $data = u::query("SELECT t.name, t.id, t.price, t.receivable,t.session
            FROM tuition_fee AS t 
            WHERE t.status=1 AND t.available_date <= CURRENT_DATE AND expired_date >= CURRENT_DATE 
            AND t.product_id = $product_id AND ( t.branch_id LIKE '$branch_id,%' OR t.branch_id LIKE '%,$branch_id,%' OR t.branch_id LIKE '%,$branch_id' OR t.branch_id = '$branch_id' ) 
            ORDER BY t.name DESC");
        return response()->json($data);
    }
    
    public function loadDiscountCode(Request $request)
    {
        $tuition_fee_id = $request->tuition_fee_id;
        $data = u::query("SELECT d.name, d.id, d.percent, d.discount
            FROM discount_codes AS d 
            WHERE d.status=1 AND d.start_date <= CURRENT_DATE AND d.end_date >= CURRENT_DATE 
            AND ( d.fee_ids LIKE '$tuition_fee_id,%' OR d.fee_ids LIKE '%,$tuition_fee_id,%' OR d.fee_ids LIKE '%,$tuition_fee_id' OR d.fee_ids = '$tuition_fee_id' ) 
            ORDER BY d.name DESC");
        return response()->json($data);
    }

    public function checkCoupon(Request $request){
        $coupon_code = $request->coupon_code;
        $data = u::first("SELECT c.id, c.status, c.end_date, c.start_date, c.coupon_amount,c.coupon_session
            FROM coupons AS c 
            WHERE c.code='$coupon_code' AND c.status!=0 ");
        if($data){
            $message = "";
            if($data->status == 2){
                $message = "Mã voucher đã được sử dụng";
            }elseif($data->end_date < date('Y-m-d')){
                $message = "Mã voucher đã hết hạn";
            }elseif($data->start_date > date('Y-m-d')){
                $message = "Mã voucher chỉ được kích hoạt từ ngày ".$data->start_date;
            }
            if($message){
                $result = array(
                    'status' => 0,
                    'message' => $message
                );
            }else{
                $result = array(
                    'status' => 1,
                    'message' => 'ok',
                    'data'=>$data
                );
            }
        }else{
            $result = array(
                'status' => 0,
                'message' => 'Mã voucher không hợp lệ'
            );
        }
        return response()->json($result);
    }
}
