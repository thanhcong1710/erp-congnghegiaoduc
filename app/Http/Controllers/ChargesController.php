<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use App\Models\LogStudents;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChargesController extends Controller
{
    public function waitchargesList(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $keyword = isset($request->keyword) ? $request->keyword : '';

        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        $cond = " c.status > 0  AND c.must_charge > 0 AND c.debt_amount > 0 ";
        $cond .= " AND c.branch_id IN (" . Auth::user()->getBranchesHasUser().")";

        if (!empty($branch_id)) {
            $cond .= " AND c.branch_id IN (".implode(",",$branch_id).")";
        }
        
        if ($keyword !== '') {
            $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%' OR c.code LIKE '%$keyword%') ";
        }
        
        $order_by = " ORDER BY c.id DESC ";

        $total = u::first("SELECT count(c.id) AS total 
            FROM contracts AS c LEFT JOIN students AS s ON s.id=c.student_id WHERE $cond");
        
        $list = u::query("SELECT c.id AS contract_id, s.name, s.lms_code, 
                (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.ec_id) AS ec_name,
                (SELECT name FROM products WHERE id =c.product_id) AS product_name,
                c.code, (SELECT name FROM tuition_fee WHERE id=c.tuition_fee_id) AS tuition_fee_name,
                c.must_charge, c.debt_amount, c.status
            FROM contracts AS c 
                LEFT JOIN students AS s ON s.id=c.student_id
            WHERE $cond $order_by $limitation");
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function add(Request $request){
        $contract_info = u::getObject(array('id'=>$request->contract_id), 'contracts');
        u::insertSimpleRow(array(
            'contract_id' => data_get($contract_info, 'id'),
            'student_id' => data_get($contract_info, 'student_id'), 
            'branch_id' => data_get($contract_info, 'branch_id'), 
            'cm_id' => data_get($contract_info, 'cm_id'), 
            'ec_id' => data_get($contract_info, 'ec_id'), 
            'method' => data_get($request, 'method'),
            'must_charge' => data_get($contract_info, 'must_charge'),
            'amount' => data_get($request, 'amount'),
            'total' => (int)data_get($contract_info, 'total_charged') + (int)data_get($request, 'amount'),
            'debt' => (int)data_get($contract_info, 'must_charge') - (int)data_get($contract_info, 'total_charged') - (int)data_get($request, 'amount'),
            'charge_date' => data_get($request, 'charge_date'),
            'note' => data_get($request, 'note'),
            'created_at'=>date('Y-m-d H:i:s'),
            'creator_id'=>Auth::user()->id,
        ), 'payments');

        $debt_amount = (int)data_get($contract_info, 'must_charge') - (int)data_get($contract_info, 'total_charged') - (int)data_get($request, 'amount');
        if($debt_amount == 0){
            u::updateSimpleRow(array(
                'status' => 3,
                'reservable_sessions' => floor(data_get($contract_info, 'total_sessions')/config('app.num_session_of_reservable')),
                'summary_sessions' => data_get($contract_info, 'total_sessions'), 
                'left_sessions' => data_get($contract_info, 'total_sessions'), 
                'total_charged' => (int)data_get($contract_info, 'total_charged') + (int)data_get($request, 'amount'),
                'init_total_charged' => (int)data_get($contract_info, 'total_charged') + (int)data_get($request, 'amount'),
                'debt_amount' => 0,
                'updated_at'=>date('Y-m-d H:i:s'),
                'updator_id'=>Auth::user()->id,
            ), array('id'=>data_get($contract_info, 'id')), 'contracts');
            LogStudents::logAdd(data_get($contract_info, 'student_id'), 'Thu đủ phí cho hợp đồng - '.data_get($contract_info, 'code'), Auth::user()->id);
            self::processC2C(data_get($contract_info, 'student_id'), data_get($contract_info, 'init_tuition_fee_id'), data_get($contract_info, 'id'));
        }else{
            u::updateSimpleRow(array(
                'status' => 2,
                'total_charged' => (int)data_get($contract_info, 'total_charged') + (int)data_get($request, 'amount'),
                'init_total_charged' => (int)data_get($contract_info, 'total_charged') + (int)data_get($request, 'amount'),
                'debt_amount' => $debt_amount,
                'updated_at'=>date('Y-m-d H:i:s'),
                'updator_id'=>Auth::user()->id,
            ), array('id'=>data_get($contract_info, 'id')), 'contracts');
            LogStudents::logAdd(data_get($contract_info, 'student_id'), 'Đặt cọc '.u::formatCurrency(data_get($request, 'amount')).' cho hợp đồng - '.data_get($contract_info, 'code'), Auth::user()->id);
        }
        
        u::addLogContracts(data_get($contract_info, 'id'));
        $result = array(
            'status' => 1,
            'message' => 'Thêm mới phiếu thu thành công.'
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
        $cond = " c.branch_id IN (" . Auth::user()->getBranchesHasUser().")";

        if (!empty($branch_id)) {
            $cond .= " AND c.branch_id IN (".implode(",",$branch_id).")";
        }
        
        if ($keyword !== '') {
            $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%' OR c.code LIKE '%$keyword%') ";
        }
        if ($end_date !== '') {
            $cond .= " AND p.charge_date <= '$end_date'";
        }
        if ($start_date !== '') {
            $cond .= " AND p.charge_date >= '$start_date'";
        }
        
        $order_by = " ORDER BY p.id DESC ";

        $total = u::first("SELECT count(p.id) AS total 
                FROM payments AS p
                    LEFT JOIN contracts AS c ON c.id=p.contract_id 
                    LEFT JOIN students AS s ON s.id=c.student_id WHERE $cond");
        
        $list = u::query("SELECT c.id AS contract_id, s.name, s.lms_code, 
                (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= p.creator_id) AS creator_name,
                (SELECT name FROM products WHERE id =c.product_id) AS product_name,
                c.code, (SELECT name FROM tuition_fee WHERE id=c.tuition_fee_id) AS tuition_fee_name,
                p.amount, p.must_charge, p.total, p.debt,p.charge_date, p.created_at
            FROM payments AS p
                LEFT JOIN contracts AS c ON c.id=p.contract_id
                LEFT JOIN students AS s ON s.id=c.student_id
            WHERE $cond $order_by $limitation");
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public static function processC2C($stuent_id, $tuition_fee_id, $contract_id){
        $stuent_info = u::first("SELECT source_id, source_detail_id, c2c_mobile, gud_mobile1 FROM students WHERE id=$stuent_id");
        if (data_get($stuent_info,'source_id')==3 && data_get($stuent_info,'source_detail_id')){
            $source_detail = u::first("SELECT campaign_id FROM source_detail WHERE id =".data_get($stuent_info,'source_detail_id'));
            $c2c_campaign = u::first("SELECT * FROM c2c_campaigns WHERE id=".data_get($source_detail,'campaign_id'));
            if(data_get($c2c_campaign, 'status')==1 && in_array($tuition_fee_id, explode(',', data_get($c2c_campaign, 'list_tuition_fee')))
                && data_get($c2c_campaign, 'start_date') <= date('Y-m-d')&& data_get($c2c_campaign, 'end_date') >= date('Y-m-d')){
                $meta_data = json_decode(data_get($c2c_campaign,'meta_data'));
                $voucher_amount = 0;
                $voucher_bonus_session =0;
                $voucher_ref_amount = 0;
                $voucher_ref_bonus_session = 0;
                foreach ($meta_data AS $row){
                    if($tuition_fee_id == data_get($row, 'tuition_fee.id')){
                        $voucher_amount = data_get($row, 'amount');
                        $voucher_bonus_session = data_get($row, 'bonus_session');
                        $voucher_ref_amount = data_get($row, 'ref_amount');
                        $voucher_ref_bonus_session = data_get($row, 'ref_bonus_session');
                    }
                }
                if($voucher_amount || $voucher_bonus_session) {
                    $voucher_code = u::generateRandomAlphanumeric(6);
                    $check_exit = u::first("SELECT id FROM coupons WHERE code='$voucher_code'");
                    while(!empty($check_exit)){
                        $voucher_code = u::generateRandomAlphanumeric(6);
                        $check_exit = u::first("SELECT id FROM coupons WHERE code='$voucher_code'");
                    }
                    u::insertSimpleRow(array(
                        'code' => $voucher_code,
                        'coupon_amount'=> $voucher_amount,
                        'coupon_session'=> $voucher_bonus_session,
                        'start_date'=> date('Y-m-d'),
                        'end_date'=> date('Y-m-d', strtotime('+1 year')),
                        'status' => 1,
                        'created_at'=>date('Y-m-d H:i:s'),
                        'creator_id'=>Auth::user()->id,
                        'campaign_id'=> data_get($c2c_campaign, 'id'),
                        'source_id' => 3,
                        'c2c_mobile' => data_get($stuent_info, 'c2c_mobile'),
                        'c2c_contract_id' => $contract_id
                    ), 'coupons');
                }
                if($voucher_ref_amount || $voucher_ref_bonus_session) {
                    $voucher_code = u::generateRandomAlphanumeric(6);
                    $check_exit = u::first("SELECT id FROM coupons WHERE code='$voucher_code'");
                    while(!empty($check_exit)){
                        $voucher_code = u::generateRandomAlphanumeric(6);
                        $check_exit = u::first("SELECT id FROM coupons WHERE code='$voucher_code'");
                    }
                    u::insertSimpleRow(array(
                        'code' => $voucher_code,
                        'coupon_amount'=> $voucher_amount,
                        'coupon_session'=> $voucher_bonus_session,
                        'start_date'=> date('Y-m-d'),
                        'end_date'=> date('Y-m-d', strtotime('+1 year')),
                        'status' => 1,
                        'created_at'=>date('Y-m-d H:i:s'),
                        'creator_id'=>Auth::user()->id,
                        'campaign_id'=> data_get($c2c_campaign, 'id'),
                        'source_id' => 3,
                        'c2c_mobile' => data_get($stuent_info, 'gud_mobile1'),
                        'c2c_contract_id' => $contract_id
                    ), 'coupons');
                }
            }
        }
        return "ok";
    }
}
