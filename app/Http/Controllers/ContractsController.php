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
        $is_renew = $request->is_renew;
        if ($is_renew) {
            $cond = " AND t.type IN(0,2)";
        } else {
            $cond = " AND t.type IN(0,1)";
        }
        $data = u::query("SELECT t.name, t.id, t.price, t.receivable,t.session,t.number_of_months
            FROM tuition_fee AS t 
            WHERE t.status=1 AND t.available_date <= CURRENT_DATE AND expired_date >= CURRENT_DATE AND type_contract = $type_contract
            AND t.product_id = $product_id AND ( t.branch_id LIKE '$branch_id,%' OR t.branch_id LIKE '%,$branch_id,%' OR t.branch_id LIKE '%,$branch_id' OR t.branch_id = '$branch_id' )
            $cond 
            ORDER BY t.number_of_months DESC");
        return response()->json($data);
    }
    
    public function loadDiscountCode(Request $request)
    {
        $tuition_fee_id = $request->tuition_fee_id;
        $data = u::query("SELECT d.name, d.id, d.percent, d.discount,d.bonus_sessions
            FROM discount_codes AS d 
            WHERE d.status=1 AND d.start_date <= CURRENT_DATE AND d.end_date >= CURRENT_DATE 
            AND ( d.fee_ids LIKE '$tuition_fee_id,%' OR d.fee_ids LIKE '%,$tuition_fee_id,%' OR d.fee_ids LIKE '%,$tuition_fee_id' OR d.fee_ids = '$tuition_fee_id' ) 
            ORDER BY d.name DESC");
        return response()->json($data);
    }

    public function checkCoupon(Request $request){
        $coupon_code = $request->coupon_code;
        $data = u::first("SELECT c.id, c.status, c.end_date, c.start_date, c.coupon_amount,c.coupon_session, c.type, c.quota, c.branch_id, c.coupon_percent, c.limit
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
                if($data->type == 1){
                    $result = array(
                        'status' => 1,
                        'message' => 'ok',
                        'data'=>$data
                    );
                }elseif($data->type == 2) {
                    $student_info = u::first("SELECT branch_id FROM term_student_user WHERE student_id=".data_get($request, 'student_id')." AND status = 1");
                    if ($data->branch_id){
                        $branch_ids = explode(',', $data->branch_id);
                        if(!in_array($student_info->branch_id, $branch_ids)){
                            $result = array(
                                'status' => 0,
                                'message' => 'Mã voucher không áp dụng cho trung tâm này'
                            );
                            return response()->json($result);
                        }
                    }
                    $count_used = u::first("SELECT count(id) AS total FROM contracts WHERE student_id=".data_get($request, 'student_id')." AND coupon_code='$coupon_code'");
                    if($data->limit <= $count_used->total){
                        $result = array(
                            'status' => 0,
                            'message' => "Mỗi học sinh chỉ được sử dụng ".$data->limit." lần. Học sinh này đã sử dụng hết vui lòng kiểm tra lại."
                        );
                        return response()->json($result);
                    }
                    $count_total_used = u::first("SELECT count(id) AS total FROM contracts WHERE coupon_code='$coupon_code'");
                    if($data->quota && $data->quota <= $count_total_used->total){
                        $result = array(
                            'status' => 0,
                            'message' => "Mã voucher chỉ được sử dụng tối đa ".$data->quota." lần. Mã voucher này đã được sử dụng hết."
                        );
                        return response()->json($result);
                    }
                    $result = array(
                        'status' => 1,
                        'message' => 'ok',
                        'data'=>$data
                    );
                }
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
        $sibling_discount = data_get($request, 'sibling_discount') ? data_get($request, 'sibling_discount') : 0;
        $total_discount = (int)$coupon_amount + (int)data_get($request, 'discount_code_amount') + (int)data_get($request,'b2b_amount') + (int)$sibling_discount;
        $total_discount = $total_discount < data_get($request, 'tuition_fee_receivable') ? $total_discount : data_get($request, 'tuition_fee_receivable');
        $last_contract = u::first("SELECT count_recharge FROM contracts WHERE student_id=".data_get($request, 'student_id')." AND status > 0 ORDER BY count_recharge DESC LIMIT 1");
        $count_recharge = data_get($request, 'type') ==0 ? -1 : ($last_contract ? $last_contract->count_recharge + 1 : 0);
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
           'init_tuition_fee_id' => data_get($request, 'tuition_fee_id'),
           'init_tuition_fee_amount' => data_get($request, 'tuition_fee_amount'),
           'init_tuition_fee_receivable' => data_get($request, 'tuition_fee_receivable'),
           'init_tuition_fee_session' => data_get($request, 'tuition_fee_session'),
           'init_total_charged'=>0,
           'must_charge' => data_get($request, 'total_amount'),
           'total_charged'=>0,
           'debt_amount' => data_get($request, 'total_amount'),
           'total_discount' => $total_discount,
           'discount_code_id' => data_get($request, 'discount_code_id'),
           'discount_code' => data_get($request, 'discount_code'),
           'discount_code_percent' => data_get($request, 'discount_code_percent'),
           'discount_code_amount' => data_get($request, 'discount_code_amount'),
           'discount_code_session' => data_get($request, 'discount_code_session'),
           'coupon_code' => data_get($request,'coupon_code_check') == 1 ? data_get($request, 'coupon_code') : '',
           'coupon_amount' => data_get($request,'coupon_code_check') == 1 ? data_get($request, 'coupon_amount') : 0,
           'coupon_session' => data_get($request,'coupon_code_check') == 1 ? data_get($request, 'coupon_session') : 0,
           'total_sessions' => data_get($request, 'total_session'),
           'real_sessions' => data_get($request, 'type') ==0  || data_get($request, 'total_amount') == 0? 0 : data_get($request, 'tuition_fee_session'),
           'bonus_sessions' => data_get($request, 'type') ==0 || data_get($request, 'total_amount') == 0? data_get($request, 'total_session') : ((int)data_get($request, 'total_session') - (int)data_get($request, 'tuition_fee_session')),
           'summary_sessions' => data_get($request, 'type') ==0 || data_get($request, 'total_amount') == 0 ? data_get($request, 'total_session') : 0, // chưa đóng phí
           'reservable_sessions' =>0, // khi nào có buổi summary_sessions mới được bảo lưu,
           'start_date'=> data_get($request, 'start_date'),
           'note'=> data_get($request, 'note'),
           'created_at'=>date('Y-m-d H:i:s'),
           'creator_id'=>Auth::user()->id,
           'status' => data_get($request, 'type') ==0 || data_get($request, 'total_amount') == 0 ? 3 : 1,
           'count_recharge' => $count_recharge,
           'b2b_campaign_id' => data_get($request,'b2b_campaign_id'),
           'b2b_amount' => data_get($request,'b2b_amount'),
           'b2b_bonus_session' => data_get($request,'b2b_bonus_session'),
           'sibling_discount' => $sibling_discount,
        ), 'contracts');

        if(data_get($request,'coupon_code_check') == 1){
            $coupon_info = u::getObject(['code'=>data_get($request, 'coupon_code')], 'coupons');
            if($coupon_info->quota == 1){
                u::updateSimpleRow(array(
                    'status'=> 2,
                    'checked_date'=>date('Y-m-d'),
                    'updated_at'=>date('Y-m-d H:i:s'),
                    'updator_id'=>Auth::user()->id,
                ), array('id'=>$coupon_info->id,'type'=>1), 'coupons');
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
        $text_mess = $count_recharge > 0 ? 'tái phí' : 'nhập học';
        LogStudents::logAdd(data_get($student_info, 'student_id'), 'Thêm mới hợp đồng '.$text_mess.' - '.$contract_code, Auth::user()->id);
        $lmsController = new LMSController();
        $lmsController->addOrUpdateStudent(data_get($student_info, 'student_id'));

        $result = array(
            'status' => 1,
            'message' => 'Thêm mới '.$text_mess.' thành công'
        );
        return response()->json($result);
    }

    public function list(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $keyword = isset($request->keyword) ? $request->keyword : '';
        $end_date = isset($request->end_date) ? $request->end_date : '';
        $start_date = isset($request->start_date) ? $request->start_date : '';
        $type_contract = isset($request->type_contract) ? $request->type_contract : 0;

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
            $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.lms_id LIKE '%$keyword%' OR s.name LIKE '%$keyword%' OR c.code LIKE '%$keyword%') ";
        }

        if ($end_date !== '') {
            $cond .= " AND c.created_at < '$end_date 23:59:59'";
        }
        if ($start_date !== '') {
            $cond .= " AND c.created_at > '$start_date 00:00:00'";
        }
        if ($type_contract == 1) {
            $cond .= " AND c.count_recharge <= 0 ";
        } elseif ($type_contract == 2) {
            $cond .= " AND c.count_recharge > 0 ";
        }
        
        $order_by = " ORDER BY c.id DESC ";

        $total = u::first("SELECT count(s.id) AS total 
            FROM contracts AS c LEFT JOIN students AS s ON s.id=c.student_id WHERE $cond");
        
        $list = u::query("SELECT c.id AS contract_id, s.name, s.lms_code,  s.lms_id, 
                (SELECT name FROM branches WHERE id =c.branch_id) AS branch_name,
                (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.ec_id) AS ec_name,
                (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.cm_id) AS cm_name,
                (SELECT name FROM products WHERE id =c.product_id) AS product_name,
                c.code, (SELECT name FROM tuition_fee WHERE id=c.tuition_fee_id) AS tuition_fee_name,
                c.total_sessions,c.bonus_sessions, c.real_sessions, c.init_tuition_fee_amount, c.must_charge, c.debt_amount, c.total_charged, c.status, c.type, c.summary_sessions
            FROM contracts AS c 
                LEFT JOIN students AS s ON s.id=c.student_id
            WHERE $cond $order_by $limitation");
        foreach($list AS $k=> $row){
            $list[$k]->label_status = u::genStatusStudent($row->status, $row->type);
        }
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function delete(Request $request){
        $contract_info = u::first("SELECT student_id, code FROM contracts WHERE id=$request->contract_id");
        u::query("DELETE FROM contracts WHERE id =".$request->contract_id);

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
            (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.creator_id) AS creator_name,
            (SELECT title FROM b2b_campaigns WHERE id= c.b2b_campaign_id) AS b2b_campaign_title,
            c.b2b_campaign_id,c.b2b_amount, c.b2b_bonus_session,
            (SELECT count(id) FROM students WHERE gud_mobile1 = s.gud_mobile1 AND s.id != id) AS count_sibling
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
        $sibling_discount = data_get($request, 'sibling_discount') ? data_get($request, 'sibling_discount') : 0;
        $total_discount = (int)$coupon_amount + (int)data_get($request, 'discount_code_amount') + (int)data_get($request,'b2b_amount') + (int)$sibling_discount;
        $total_discount = $total_discount < data_get($request, 'tuition_fee_amount') ? $total_discount : data_get($request, 'tuition_fee_amount');
        $payment = u::first("SELECT SUM(amount) AS total FROM payments WHERE contract_id=$contract_id");
        $total_amount = data_get($payment, 'total', 0);
        $availableSession = (int) data_get($request, 'tuition_fee_session') && (int)data_get($request, 'total_amount') ? 
            round($total_amount / ((int)data_get($request, 'total_amount')/(int)data_get($request, 'tuition_fee_session'))) : 0; 
        $availableSession = $availableSession - (int)data_get($pre_update_contract_info, 'last_done_sessions') > 0 ? $availableSession - (int)data_get($pre_update_contract_info, 'last_done_sessions') : 0;
            
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
           'init_tuition_fee_id' => data_get($request, 'tuition_fee_id'),
           'init_tuition_fee_amount' => data_get($request, 'tuition_fee_amount'),
           'init_tuition_fee_receivable' => data_get($request, 'tuition_fee_receivable'),
           'init_tuition_fee_session' => data_get($request, 'tuition_fee_session'),
           'init_total_charged'=>$total_amount,
           'must_charge' => data_get($request, 'total_amount'),
           'total_charged'=> $total_amount,
           'debt_amount' => data_get($request, 'total_amount') - $total_amount > 0 ? data_get($request, 'total_amount') - $total_amount : 0,
           'total_discount'=> $total_discount, 
           'discount_code_id' => data_get($request, 'discount_code_id'),
           'discount_code' => data_get($request, 'discount_code'),
           'discount_code_percent' => data_get($request, 'discount_code_percent'),
           'discount_code_amount' => data_get($request, 'discount_code_amount'),
           'discount_code_session' => data_get($request, 'discount_code_session'),
           'coupon_code' => data_get($request,'coupon_code_check') == 1 ? data_get($request, 'coupon_code') : '',
           'coupon_amount' => data_get($request,'coupon_code_check') == 1 ? data_get($request, 'coupon_amount') : 0,
           'coupon_session' => data_get($request,'coupon_code_check') == 1 ? data_get($request, 'coupon_session') : 0,
           'total_sessions' => data_get($request, 'total_session'),
           'real_sessions' => data_get($request, 'tuition_fee_session'),
           'bonus_sessions' => data_get($request, 'type') ==0 ? data_get($request, 'total_session') : ((int)data_get($request, 'total_session') - (int)data_get($request, 'tuition_fee_session')),
           'summary_sessions' => $availableSession, // chưa đóng phí
           'reservable_sessions' =>0, // khi nào có buổi summary_sessions mới được bảo lưu,
           'start_date'=> data_get($request, 'start_date'),
           'note'=> data_get($request, 'note'),
           'updated_at'=>date('Y-m-d H:i:s'),
           'updator_id'=>Auth::user()->id,
           'left_sessions' => $availableSession - (int)data_get($pre_update_contract_info, 'done_sessions'), 
           'status' => 1, 
           'b2b_campaign_id' => data_get($request,'b2b_campaign_id'),
           'b2b_amount' => data_get($request,'b2b_amount'),
           'b2b_bonus_session' => data_get($request,'b2b_bonus_session'),
           'sibling_discount' => $sibling_discount,
        ), ['id'=>$contract_id],'contracts');

        if(data_get($pre_update_contract_info, 'coupon_code') && (data_get($request,'coupon_code_check') != 1 || data_get($pre_update_contract_info, 'coupon_code') != data_get($request, 'coupon_code'))){
            $pre_coupon = u::getObject(['code'=>data_get($pre_update_contract_info, 'coupon_code')], 'coupons');
            u::query("DELETE FROM coupon_logs WHERE contract_id = $contract_id AND coupon_id = ".data_get($pre_coupon, 'id'));
            u::updateSimpleRow(array(
                'status'=> 1,
                'checked_date'=>null, 
                'updated_at'=>date('Y-m-d H:i:s'),
                'updator_id'=>Auth::user()->id,
            ), array('id'=>$pre_coupon->id, 'type'=>1), 'coupons');
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
                ), array('id'=>$coupon_info->id, 'type'=>1), 'coupons');
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
        $text_mess = data_get($pre_update_contract_info, 'count_recharge') > 0 ? 'tái phí' : 'nhập học';
        
        $result = array(
            'status' => 1,
            'message' => 'Cập nhật thông tin '.$text_mess.' thành công'
        );
        return response()->json($result);
    }

    public function print(Request $request,$contract_id)
    {
        $contract_info = u::first("SELECT s.name AS student_name, s.date_of_birth, s.gender, s.address, s.school,
            s.gud_name1, s.gud_mobile1, s.gud_email1, c.note,c.total_charged, c.debt_amount, c.must_charge,
            (SELECT name FROM users WHERE id=c.ec_id) AS ec_name,
            (SELECT name FROM products WHERE id=c.product_id) AS product_name,
            (SELECT number_of_months FROM tuition_fee WHERE id=c.tuition_fee_id) AS number_of_months

        FROM contracts AS c 
            LEFT JOIN students AS s ON s.id=c.student_id WHERE c.id=$contract_id");
        $data = [
            'student_name' => data_get($contract_info, 'student_name') ?? '',
            'date_of_birth' => data_get($contract_info, 'date_of_birth') ? date('d/m/Y',strtotime(data_get($contract_info, 'date_of_birth'))): '',
            'gender' => data_get($contract_info, 'gender') ? (data_get($contract_info, 'gender')=='M'? 'Nam' : 'Nữ') :'',
            'address' => data_get($contract_info, 'address') ?? '',
            'school' => data_get($contract_info, 'school') ?? '',
            'gud_name1' => data_get($contract_info, 'gud_name1') ?? '',
            'gud_mobile1' => data_get($contract_info, 'gud_mobile1') ?? '',
            'gud_email1' => data_get($contract_info, 'gud_email1') ?? '',
            'note' => data_get($contract_info, 'note') ?? '',
            'ec_name' => data_get($contract_info, 'ec_name') ?? '',
            'debt_amount' => number_format(data_get($contract_info, 'debt_amount') ?? 0),
            'total_charged_text' => u::convert_number_to_words(data_get($contract_info, 'total_charged')),
            'total_charged' => number_format(data_get($contract_info, 'total_charged') ?? 0),
            'must_charge' => number_format(data_get($contract_info, 'must_charge') ?? 0),
            'product_name' => data_get($contract_info, 'product_name') ?? '',
            'number_of_months' => data_get($contract_info, 'number_of_months') ?? '',
        ];
        return response()->json($data);
    }

    public static function exitDepost (Request $request){
        $contract_info = u::getObject(array('id'=>data_get($request, 'contract_id')), 'contracts');
        u::updateSimpleRow(array(
            'count_recharge' => -1,
            'status' => 3,
            'debt_amount' => 0,
            'updated_at'=>date('Y-m-d H:i:s'),
            'updator_id'=>Auth::user()->id,
        ), array('id'=>data_get($request, 'contract_id')), 'contracts');
        LogStudents::logAdd(data_get($contract_info, 'student_id'), 'Quy đổi cọc cho hợp đồng - '.data_get($contract_info, 'code'), Auth::user()->id);
        u::addLogContracts(data_get($contract_info, 'id'));
        return response()->json('ok');
    }
}
