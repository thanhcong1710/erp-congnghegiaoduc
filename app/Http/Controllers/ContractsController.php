<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use App\Models\LogStudents;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractsController extends Controller
{
    public function loadTuitionFee(Request $request)
    {
        $product_id = $request->product_id;
        $branch_id = $request->branch_id;
        $type_contract = $request->type_contract;
        $data = u::query("SELECT t.name, t.id, t.price, t.receivable,t.session
            FROM tuition_fee AS t 
            WHERE t.status=1 AND t.available_date <= CURRENT_DATE AND expired_date >= CURRENT_DATE AND type_contract = $type_contract
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

    public function add(Request $request)
    {
        $student_info = u::getObject(['student_id'=>data_get($request, 'student_id'), 'status' => 1], 'term_student_user');
        $coupon_amount = data_get($request,'coupon_code_check') == 1 ? data_get($request, 'coupon_amount') : 0;
        $total_discount = (int)$coupon_amount + (int)data_get($request, 'total_amount');
        $total_discount = $total_discount < data_get($request, 'tuition_fee_amount') ? $total_discount : data_get($request, 'tuition_fee_amount');
        $last_contract = u::first("SELECT count_recharge FROM contracts WHERE student_id=".data_get($request, 'student_id')." ORDER BY count_recharge DESC LIMIT 1");
        $contract_id = u::insertSimpleRow(array(
            'type' => data_get($request, 'type'),
           'student_id' => data_get($request, 'student_id'), 
           'branch_id' => data_get($student_info, 'branch_id'),
           'ceo_branch_id' => data_get($student_info, 'ceo_branch_id'),
           'ec_id' => data_get($student_info, 'ec_id'),
           'ec_leader_id' => data_get($student_info, 'ec_leader_id'),
           'cm_id' => data_get($student_info, 'cm_id'),
           'cm_leader_id' => data_get($student_info, 'cm_leader_id'),
           'product_id' => data_get($request, 'product_id'),
           'tuition_fee_id' => data_get($request, 'tuition_fee_id'),
           'tuition_fee_amount' => data_get($request, 'tuition_fee_amount'),
           'tuition_fee_session' => data_get($request, 'tuition_fee_session'),
           'tuition_fee_receivable' => data_get($request, 'tuition_fee_receivable'),
           'must_charge' => data_get($request, 'total_amount'),
           'total_charged'=>0,
           'debt_amount' => data_get($request, 'total_amount'),
           'total_discount' => $total_discount,
           'discount_code_id' => data_get($request, 'discount_code_id'),
           'discount_code' => data_get($request, 'discount_code'),
           'discount_code_percent' => data_get($request, 'discount_code_percent'),
           'discount_code_amount' => data_get($request, 'discount_code_amount'),
           'coupon_code' => data_get($request,'coupon_code_check') == 1 ? data_get($request, 'coupon_code') : '',
           'coupon_amount' => data_get($request,'coupon_code_check') == 1 ? data_get($request, 'coupon_amount') : 0,
           'coupon_session' => data_get($request,'coupon_code_check') == 1 ? data_get($request, 'coupon_session') : 0,
           'total_sessions' => data_get($request, 'total_session'),
           'real_sessions' => data_get($request, 'tuition_fee_session'),
           'bonus_sessions' => data_get($request,'coupon_code_check') == 1 ? data_get($request, 'coupon_session') : 0,
           'summary_sessions' => 0, // chưa đóng phí
           'reservable_sessions' =>0, // khi nào có buổi summary_sessions mới được bảo lưu,
           'start_date'=> data_get($request, 'start_date'),
           'note'=> data_get($request, 'note'),
           'created_at'=>date('Y-m-d H:i:s'),
           'creator_id'=>Auth::user()->id,
           'status' => 1,
           'count_recharge' => $last_contract ? $last_contract->count_recharge + 1 : 0,
        ), 'contracts');

        if(data_get($request,'coupon_code_check') == 1){
            $coupon_info = u::getObject(['code'=>data_get($request, 'coupon_code')], 'coupons');
            if($coupon_info->quota == 1){
                u::updateSimpleRow(array(
                    'status'=> 2,
                    'checked_date'=>date('Y-m-d'),
                    'updated_at'=>date('Y-m-d H:i:s'),
                    'updator_id'=>Auth::user()->id,
                ), array('id'=>$coupon_info->id), 'coupons');
            }
            u::insertSimpleRow(array(
                'contract_id' => $contract_id,
                'coupon_id' => $coupon_info->id,
                'created_at'=>date('Y-m-d H:i:s'),
                'creator_id'=>Auth::user()->id,
            ), 'coupon_logs');
        }
        $contract_code = str_pad((string)$contract_id, 6, '0', STR_PAD_LEFT);
        $contract_code = config('app.prefix_contract_code').$contract_code;
        u::updateSimpleRow(array('code'=>$contract_code), array('id'=>$contract_id), 'contracts');
        u::addLogContracts($contract_id);
        LogStudents::logAdd(data_get($student_info, 'student_id'), 'Thêm mới hợp đồng nhập học - '.$contract_code, Auth::user()->id);

        $result = array(
            'status' => 1,
            'message' => 'Thêm mới nhập học thành công'
        );
        return response()->json($result);
    }

    public function list(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $keyword = isset($request->keyword) ? $request->keyword : '';
        $end_date = isset($request->end_date) ? $request->end_date : '';
        $start_date = isset($request->start_date) ? $request->start_date : '';

        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        $cond = " c.status > 0 ";
        $cond .= " AND c.branch_id IN (" . Auth::user()->getBranchesHasUser().")";

        if (!empty($branch_id)) {
            $cond .= " AND c.branch_id IN (".implode(",",$branch_id).")";
        }
        
        if ($keyword !== '') {
            $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%' OR c.code LIKE '%$keyword%') ";
        }

        if ($end_date !== '') {
            $cond .= " AND c.created_at < '$end_date 23:59:59'";
        }
        if ($start_date !== '') {
            $cond .= " AND c.created_at > '$start_date 00:00:00'";
        }
        
        $order_by = " ORDER BY c.id DESC ";

        $total = u::first("SELECT count(s.id) AS total 
            FROM contracts AS c LEFT JOIN students AS s ON s.id=c.student_id WHERE $cond");
        
        $list = u::query("SELECT c.id AS contract_id, s.name, s.lms_code, 
                (SELECT name FROM branches WHERE id =c.branch_id) AS branch_name,
                (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.ec_id) AS ec_name,
                (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.cm_id) AS cm_name,
                (SELECT name FROM products WHERE id =c.product_id) AS product_name,
                c.code, (SELECT name FROM tuition_fee WHERE id=c.tuition_fee_id) AS tuition_fee_name,
                c.total_sessions,c.bonus_sessions, c.real_sessions, c.tuition_fee_amount, c.must_charge, c.debt_amount, c.total_charged, c.status
            FROM contracts AS c 
                LEFT JOIN students AS s ON s.id=c.student_id
            WHERE $cond $order_by $limitation");
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function delete(Request $request){
        $contract_info = u::first("SELECT student_id, code FROM contracts WHERE id=$request->contract_id");
        u::updateSimpleRow(array('status' => 0), array('id'=>$request->contract_id), 'contracts');
        u::addLogContracts($request->contract_id);

        LogStudents::logAdd(data_get($contract_info, 'student_id'), 'Hủy hợp đồng nhập học - '.data_get($contract_info, 'code'), Auth::user()->id);
        $result = array(
            'status' => 1,
            'message' => 'Hủy hợp đồng nhập học '.data_get($contract_info, 'code').' thành công.'
        );
        return response()->json($result);
    } 

    public function show(Request $request,$contract_id)
    {
        $data = u::first("SELECT c.*,c.id AS contract_id, s.name, s.lms_code, s.gud_name1, s.gud_mobile1, s.address, s.gud_email1,
            (SELECT name FROM branches WHERE id =c.branch_id) AS branch_name,
            (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.ec_id) AS ec_name,
            (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.cm_id) AS cm_name,
            (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.ec_leader_id) AS ec_leader_name,
            (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.ceo_branch_id) AS ceo_branch_name,
            (SELECT name FROM products WHERE id =c.product_id) AS product_name,
            (SELECT name FROM tuition_fee WHERE id=c.tuition_fee_id) AS tuition_fee_name,
            (SELECT name FROM discount_codes WHERE id=c.discount_code_id) AS discount_code_name,
            (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.creator_id) AS creator_name
        FROM contracts AS c 
            LEFT JOIN students AS s ON s.id=c.student_id WHERE c.id=$contract_id");
        return response()->json($data);
    }

    public function update(Request $request)
    {
        $student_info = u::getObject(['student_id'=>data_get($request, 'student_id'), 'status' => 1], 'term_student_user');
        $pre_update_contract_info = u::getObject(['id'=>data_get($request, 'id')], 'contracts');
        $contract_id = data_get($request, 'id');
        $coupon_amount = data_get($request,'coupon_code_check') == 1 ? data_get($request, 'coupon_amount') : 0;
        $total_discount = (int)$coupon_amount + (int)data_get($request, 'total_amount');
        $total_discount = $total_discount < data_get($request, 'tuition_fee_amount') ? $total_discount : data_get($request, 'tuition_fee_amount');
        u::updateSimpleRow(array(
            'type' => data_get($request, 'type'),
           'student_id' => data_get($request, 'student_id'), 
           'branch_id' => data_get($student_info, 'branch_id'),
           'ceo_branch_id' => data_get($student_info, 'ceo_branch_id'),
           'ec_id' => data_get($student_info, 'ec_id'),
           'ec_leader_id' => data_get($student_info, 'ec_leader_id'),
           'cm_id' => data_get($student_info, 'cm_id'),
           'cm_leader_id' => data_get($student_info, 'cm_leader_id'),
           'product_id' => data_get($request, 'product_id'),
           'tuition_fee_id' => data_get($request, 'tuition_fee_id'),
           'tuition_fee_amount' => data_get($request, 'tuition_fee_amount'),
           'tuition_fee_session' => data_get($request, 'tuition_fee_session'),
           'tuition_fee_receivable' => data_get($request, 'tuition_fee_receivable'),
           'tuition_fee_session' => data_get($request, 'tuition_fee_session'),
           'must_charge' => data_get($request, 'total_amount'),
           'total_charged'=>0,
           'debt_amount' => data_get($request, 'total_amount'),
           'total_discount'=> $total_discount, 
           'discount_code_id' => data_get($request, 'discount_code_id'),
           'discount_code' => data_get($request, 'discount_code'),
           'discount_code_percent' => data_get($request, 'discount_code_percent'),
           'discount_code_amount' => data_get($request, 'discount_code_amount'),
           'coupon_code' => data_get($request,'coupon_code_check') == 1 ? data_get($request, 'coupon_code') : '',
           'coupon_amount' => data_get($request,'coupon_code_check') == 1 ? data_get($request, 'coupon_amount') : 0,
           'coupon_session' => data_get($request,'coupon_code_check') == 1 ? data_get($request, 'coupon_session') : 0,
           'total_sessions' => data_get($request, 'total_session'),
           'real_sessions' => data_get($request, 'tuition_fee_session'),
           'bonus_sessions' => data_get($request,'coupon_code_check') == 1 ? data_get($request, 'coupon_session') : 0,
           'summary_sessions' => 0, // chưa đóng phí
           'reservable_sessions' =>0, // khi nào có buổi summary_sessions mới được bảo lưu,
           'start_date'=> data_get($request, 'start_date'),
           'note'=> data_get($request, 'note'),
           'updated_at'=>date('Y-m-d H:i:s'),
           'updator_id'=>Auth::user()->id,
           'status' => 1
        ), ['id'=>$contract_id],'contracts');

        if(data_get($pre_update_contract_info, 'coupon_code') && (data_get($request,'coupon_code_check') != 1 || data_get($pre_update_contract_info, 'coupon_code') != data_get($request, 'coupon_code'))){
            $pre_coupon = u::getObject(['code'=>data_get($pre_update_contract_info, 'coupon_code')], 'coupons');
            u::query("DELETE FROM coupon_logs WHERE contract_id = $contract_id AND coupon_id = ".data_get($pre_coupon, 'id'));
            u::updateSimpleRow(array(
                'status'=> 1,
                'checked_date'=>null,
                'updated_at'=>date('Y-m-d H:i:s'),
                'updator_id'=>Auth::user()->id,
            ), array('id'=>$pre_coupon->id), 'coupons');
        }
        if((!data_get($pre_update_contract_info, 'coupon_code') || data_get($pre_update_contract_info, 'coupon_code') != data_get($request, 'coupon_code'))  
            && data_get($request,'coupon_code_check') == 1){
            $coupon_info = u::getObject(['code'=>data_get($request, 'coupon_code')], 'coupons');
            if($coupon_info->quota == 1){
                u::updateSimpleRow(array(
                    'status'=> 2,
                    'checked_date'=>date('Y-m-d'),
                    'updated_at'=>date('Y-m-d H:i:s'),
                    'updator_id'=>Auth::user()->id,
                ), array('id'=>$coupon_info->id), 'coupons');
            }
            u::insertSimpleRow(array(
                'contract_id' => $contract_id,
                'coupon_id' => $coupon_info->id,
                'created_at'=>date('Y-m-d H:i:s'),
                'creator_id'=>Auth::user()->id,
            ), 'coupon_logs');
        }
        $contract_code = str_pad((string)$contract_id, 6, '0', STR_PAD_LEFT);
        $contract_code = config('app.prefix_contract_code').$contract_code;
        u::updateSimpleRow(array('code'=>$contract_code), array('id'=>$contract_id), 'contracts');
        u::addLogContracts($contract_id);
        
        $result = array(
            'status' => 1,
            'message' => 'Cập nhật thông tin nhập học thành công'
        );
        return response()->json($result);
    }
}
