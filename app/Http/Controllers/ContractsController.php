<?php

namespace App\Http\Controllers;

use App\Enums\SystemCode;
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
        $branch_id = $request->branch_id;
        $data = u::query("SELECT t.name, t.id, t.price, t.receivable,t.session, t.type_fee , '' AS label, '' AS tuition_fee_relation
            FROM tuition_fee AS t 
            WHERE t.status=1 AND t.available_date <= CURRENT_DATE AND expired_date >= CURRENT_DATE 
            AND ( t.branch_id LIKE '$branch_id,%' OR t.branch_id LIKE '%,$branch_id,%' OR t.branch_id LIKE '%,$branch_id' OR t.branch_id = '$branch_id' ) 
            ORDER BY t.type_fee, t.price  ");
        foreach ($data AS $k=> $row){
            $data[$k]->label = data_get($row,'name'). " (".number_format( data_get($row, 'price'))."đ)";
            $data[$k]->tuition_fee_relation = u::query("SELECT r.price_combo, t.price, t.name, t.session FROM tuition_fee_relation AS r 
                LEFT JOIN tuition_fee AS t ON r.exchange_tuition_fee_id=t.id WHERE r.tuition_fee_id = ".data_get($row, 'id'));
        }
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
        $parent_info = u::getObject(['id'=>data_get($request, 'parent_id')], 'crm_parents');
        if(!data_get($parent_info,'student_id')){
            $arr_name = u::explodeName(data_get($parent_info, 'name'));
            $student_id = u::insertSimpleRow(array(
                'lms_code' => '',
                'name' => data_get($parent_info, 'name'),
                'firstname' => data_get($arr_name, 'firstname'),
                'midname' => data_get($arr_name, 'midname'),
                'lastname' => data_get($arr_name, 'lastname'),
                'gender' => data_get($parent_info, 'gender'),
                'date_of_birth' => data_get($parent_info, 'birthday'),
                'gud_mobile1' => data_get($parent_info, 'mobile_1'),
                'gud_email1' => data_get($parent_info, 'email'),
                'address' =>  data_get($parent_info, 'address'),
                'province_id' => data_get($parent_info, 'province_id'),
                'district_id' => data_get($parent_info, 'district_id'),
                'created_at' => date('Y-m-d H:i:s'),
                'creator_id' => Auth::user()->id,
                'branch_id' => data_get($request, 'branch_id'),
                'status' => 1,
                'source_detail_id' => data_get($parent_info, 'source_detail_id'),
                'source_id' => data_get($parent_info, 'source_id'),
                'avatar_url' => data_get($parent_info, 'gender') =='F' ?'/images/common/avatar-girl.svg' : '/images/common/avatar-boy.svg'
            ), 'students');
            $last_lms_code = str_pad((string)$student_id, 6, '0', STR_PAD_LEFT);
            $lms_code = config('app.prefix_student_code').$last_lms_code;
            u::updateSimpleRow(array('lms_code'=>$lms_code), array('id'=>$student_id), 'students');

            $ceo_info = u::first("SELECT u.id FROM role_has_user AS ru 
                LEFT JOIN roles AS r ON r.id = ru.role_id
                LEFT JOIN users AS u ON u.id = ru.user_id
                WHERE u.status=1 AND r.code ='".SystemCode::ROLE_CEO_BRANCH."'");
            $ec_info = u::first("SELECT u.id, u.manager_id FROM users AS u WHERE u.status=1 AND u.id = ".(int)data_get($parent_info, 'owner_id'));
            $ec_id = data_get($ec_info, 'id');
            $ec_leader_id = data_get($ec_info, 'manager_id');
            u::insertSimpleRow(array(
                'student_id' => $student_id,
                'ec_id' => $ec_id,
                'branch_id' => data_get($request, 'branch_id'),
                'ceo_branch_id' => data_get($ceo_info, 'id'),
                'ec_leader_id' => $ec_leader_id,
                'created_at' => date('Y-m-d H:i:s'),
                'creator_id' => Auth::user()->id,
                'status' => 1
            ), 'term_student_user');
            u::updateSimpleRow(array('student_id' =>$student_id), array('id'=>data_get($request, 'parent_id')), 'crm_parents');
        }else{
            $student_id = data_get($parent_info,'student_id');
            $ec_info = u::first("SELECT u.id, u.manager_id FROM users AS u WHERE u.status=1 AND u.id = ".(int)data_get($parent_info, 'owner_id'));
            $ec_id = data_get($ec_info, 'id');
            $ec_leader_id = data_get($ec_info, 'manager_id');
        }
        $agreement_id = u::insertSimpleRow(array(
            'student_id' => $student_id,
            'branch_id' =>  data_get($request, 'branch_id'),
            'ec_id' => $ec_id,
            'ec_leader_id' => $ec_leader_id,
            'type_fee' => data_get($request, 'tuition_fee_type'),
            'tuition_fee_id' => data_get($request, 'tuition_fee_id'),
            'must_charge' => data_get($request, 'tuition_fee_amount'),
            'debt_amount' => data_get($request, 'tuition_fee_amount'),
            'start_date' => data_get($request, 'start_date'),
            'note' => data_get($request, 'note'),
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'creator_id' => Auth::user()->id,
        ), 'agreements');
        $agreement_code = str_pad((string)$agreement_id, 6, '0', STR_PAD_LEFT);
        $agreement_code = config('app.prefix_agreement_code').$agreement_code;
        u::updateSimpleRow(array('code'=>$agreement_code), array('id'=>$agreement_id), 'agreements');
        u::addLogAgreements($agreement_id);

        if (data_get($request, 'tuition_fee_type') == 1){
            $tuition_fee_info = u::getObject(['id'=>data_get($request, 'tuition_fee_id')],'tuition_fee');
            if ($tuition_fee_info){
                $contract_id = u::insertSimpleRow(array(
                    'type' => 1,
                    'student_id' => $student_id, 
                    'branch_id' =>  data_get($request, 'branch_id'),
                    'ec_id' => $ec_id,
                    'ec_leader_id' => $ec_leader_id,
                    'product_id' => data_get($tuition_fee_info, 'product_id'),
                    'tuition_fee_id' => data_get($tuition_fee_info, 'id'),
                    'init_tuition_fee_id' => data_get($tuition_fee_info, 'id'),
                    'init_tuition_fee_amount' => data_get($tuition_fee_info, 'price'),
                    'init_tuition_fee_session' => data_get($tuition_fee_info, 'session'),
                    'init_total_charged'=>0,
                    'must_charge' => data_get($tuition_fee_info, 'price'),
                    'total_charged'=>0,
                    'debt_amount' => data_get($tuition_fee_info, 'price'),
                    'total_sessions' => data_get($tuition_fee_info, 'session'),
                    'real_sessions' => data_get($tuition_fee_info, 'session'),
                    'bonus_sessions' => 0,
                    'summary_sessions' => 0, // chưa đóng phí
                    'reservable_sessions' => 0, // khi nào có buổi summary_sessions mới được bảo lưu,
                    'start_date'=> data_get($request, 'start_date'),
                    'note'=> data_get($request, 'note'),
                    'created_at'=>date('Y-m-d H:i:s'),
                    'creator_id'=>Auth::user()->id,
                    'status' => 1,
                    'count_recharge'=> 1
                ), 'contracts');
                $contract_code = str_pad((string)$contract_id, 6, '0', STR_PAD_LEFT);
                $contract_code = config('app.prefix_contract_code').$contract_code;
                u::updateSimpleRow(array('code'=>$contract_code), array('id'=>$contract_id), 'contracts');
                u::addLogContracts($contract_id);
            }
        } elseif (data_get($request, 'tuition_fee_type') == 2) {
            $relation_tuition_fee = u::query("SELECT t.*, r.price_combo, r.stt FROM tuition_fee_relation AS r 
                LEFT JOIN tuition_fee AS t ON r.exchange_tuition_fee_id=t.id 
                WHERE r.status=1 AND r.tuition_fee_id = ".data_get($request, 'tuition_fee_id'));
            foreach ($relation_tuition_fee AS $tuition_fee_info){
                $contract_id = u::insertSimpleRow(array(
                    'type' => 1,
                    'student_id' => $student_id, 
                    'branch_id' =>  data_get($request, 'branch_id'),
                    'ec_id' => $ec_id,
                    'ec_leader_id' => $ec_leader_id,
                    'product_id' => data_get($tuition_fee_info, 'product_id'),
                    'tuition_fee_id' => data_get($tuition_fee_info, 'id'),
                    'init_tuition_fee_id' => data_get($tuition_fee_info, 'id'),
                    'init_tuition_fee_amount' => data_get($tuition_fee_info, 'price_combo'),
                    'init_tuition_fee_session' => data_get($tuition_fee_info, 'session'),
                    'init_total_charged'=>0,
                    'must_charge' => data_get($tuition_fee_info, 'price_combo'),
                    'total_charged'=>0,
                    'debt_amount' => data_get($tuition_fee_info, 'price_combo'),
                    'total_sessions' => data_get($tuition_fee_info, 'session'),
                    'real_sessions' => data_get($tuition_fee_info, 'session'),
                    'bonus_sessions' => 0,
                    'summary_sessions' => 0, // chưa đóng phí
                    'reservable_sessions' => 0, // khi nào có buổi summary_sessions mới được bảo lưu,
                    'start_date'=> data_get($request, 'start_date'),
                    'note'=> data_get($request, 'note'),
                    'created_at'=>date('Y-m-d H:i:s'),
                    'creator_id'=>Auth::user()->id,
                    'status' => 1,
                    'count_recharge'=>data_get($tuition_fee_info, 'stt'),
                ), 'contracts');
                $contract_code = str_pad((string)$contract_id, 6, '0', STR_PAD_LEFT);
                $contract_code = config('app.prefix_contract_code').$contract_code;
                u::updateSimpleRow(array('code'=>$contract_code), array('id'=>$contract_id), 'contracts');
                u::addLogContracts($contract_id);
            }
        }
        
        LogStudents::logAdd($student_id, 'Thêm mới hợp đồng nhập học - '.$contract_code, Auth::user()->id);

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
            $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.gud_mobile1 LIKE '%$keyword%' OR s.name LIKE '%$keyword%' OR c.code LIKE '%$keyword%') ";
        }

        if ($end_date !== '') {
            $cond .= " AND c.created_at < '$end_date 23:59:59'";
        }
        if ($start_date !== '') {
            $cond .= " AND c.created_at > '$start_date 00:00:00'";
        }
        
        $order_by = " ORDER BY c.id DESC ";

        $total = u::first("SELECT count(s.id) AS total 
            FROM agreements AS c LEFT JOIN students AS s ON s.id=c.student_id WHERE $cond");
        
        $list = u::query("SELECT c.id AS agreement_id, s.name, s.lms_code, 
                (SELECT name FROM branches WHERE id =c.branch_id) AS branch_name,
                (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.ec_id) AS ec_name,
                (SELECT name FROM products WHERE id =c.product_id) AS product_name,
                c.code, (SELECT name FROM tuition_fee WHERE id=c.tuition_fee_id) AS tuition_fee_name,
                c.must_charge, c.debt_amount, c.total_charged, c.status
            FROM agreements AS c 
                LEFT JOIN students AS s ON s.id=c.student_id
            WHERE $cond $order_by $limitation");
        foreach($list AS $k=> $row){
            $list[$k]->label_status = u::genStatusStudent($row->status, 1);
        }
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function delete(Request $request){
        $cagreement_info = u::first("SELECT student_id, code FROM agreements WHERE id=$request->agreement_id");
        u::updateSimpleRow(array('status' => 0), array('id'=>$request->agreement_id), 'agreements');
        $listContractDelete = u::query("SELECT id FROM contracts WHERE agreement_id=$request->agreement_id");
        foreach($listContractDelete AS $contract){
            u::updateSimpleRow(array('status' => 0), array('id'=>$contract->id), 'contracts');
            u::addLogContracts($contract->id);
        }

        LogStudents::logAdd(data_get($cagreement_info, 'student_id'), 'Hủy hợp đồng nhập học - '.data_get($cagreement_info, 'code'), Auth::user()->id);
        $result = array(
            'status' => 1,
            'message' => 'Hủy hợp đồng nhập học '.data_get($cagreement_info, 'code').' thành công.'
        );
        return response()->json($result);
    } 

    public function show(Request $request,$contract_id)
    {
        $data = u::first("SELECT c.*,c.id AS contract_id, s.name, s.lms_code, s.gud_name1, s.gud_mobile1, s.address, s.gud_email1,
            (SELECT name FROM branches WHERE id =c.branch_id) AS branch_name,
            (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.ec_id) AS ec_name,
            (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.ec_leader_id) AS ec_leader_name,
            (SELECT name FROM products WHERE id =c.product_id) AS product_name,
            (SELECT name FROM tuition_fee WHERE id=c.tuition_fee_id) AS tuition_fee_name,
            (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.creator_id) AS creator_name,
            '' AS contracts
        FROM agreements AS c 
            LEFT JOIN students AS s ON s.id=c.student_id WHERE c.id=$contract_id");
        foreach($data AS $k => $row){
            $data[$k]->contracts = u:: query("SELECT * FROM contracts WHERE") ;
        }
        return response()->json($data);
    }

    public function update(Request $request)
    {
        $student_info = u::getObject(['student_id'=>data_get($request, 'student_id'), 'status' => 1], 'term_student_user');
        $pre_update_contract_info = u::getObject(['id'=>data_get($request, 'id')], 'contracts');
        $contract_id = data_get($request, 'id');
        $coupon_amount = data_get($request,'coupon_code_check') == 1 ? data_get($request, 'coupon_amount') : 0;
        $total_discount = (int)$coupon_amount + (int)data_get($request, 'discount_code_amount') + (int)data_get($request,'b2b_amount');
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
           'init_tuition_fee_id' => data_get($request, 'tuition_fee_id'),
           'init_tuition_fee_amount' => data_get($request, 'tuition_fee_amount'),
           'init_tuition_fee_receivable' => data_get($request, 'tuition_fee_receivable'),
           'init_tuition_fee_session' => data_get($request, 'tuition_fee_session'),
           'init_total_charged'=>0,
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
           'status' => 1,
           'b2b_campaign_id' => data_get($request,'b2b_campaign_id'),
           'b2b_amount' => data_get($request,'b2b_amount'),
           'b2b_bonus_session' => data_get($request,'b2b_bonus_session'),
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
