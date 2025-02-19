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
            'contract_id' => data_get($request, 'contract_id'),
            'charge_amount' => data_get($request, 'amount'),
            'debt_amount' =>(int)data_get($contract_info, 'must_charge') - (int)data_get($contract_info, 'total_charged') - (int)data_get($request, 'amount'),
            'total_charged' => (int)data_get($contract_info, 'total_charged') + (int)data_get($request, 'amount'),
            'charge_date' => data_get($request, 'charge_date'),
            'method' =>  data_get($request, 'method'),
            'note' => data_get($request, 'note'),
            'meta_data' => json_encode($request->input()),
            'created_at' => date('Y-m-d H:i:s'),
            'creator_id' => Auth::user()->id,
            'status' => 0), 'tmp_payments');
        $result = array(
            'status' => 1,
            'message' => 'Thêm mới phiếu thu thành công.'
        );
        return response()->json($result);
    }

    public function update(Request $request){
        $contract_info = u::getObject(array('id'=>$request->contract_id), 'contracts');
        u::updateSimpleRow(array(
            'contract_id' => data_get($request, 'contract_id'),
            'charge_amount' => data_get($request, 'amount'),
            'debt_amount' =>(int)data_get($contract_info, 'must_charge') - (int)data_get($contract_info, 'total_charged') - (int)data_get($request, 'amount'),
            'total_charged' => (int)data_get($contract_info, 'total_charged') + (int)data_get($request, 'amount'),
            'charge_date' => data_get($request, 'charge_date'),
            'method' =>  data_get($request, 'method'),
            'note' => data_get($request, 'note'),
            'meta_data' => json_encode($request->input()),
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => Auth::user()->id), array('id'=> data_get($request, 'id')), 'tmp_payments');
        $result = array(
            'status' => 1,
            'message' => 'Cập nhật phiếu thu thành công.'
        );
        return response()->json($result);
    }

    public function approve(Request $request){
        $tmp_payment = u::getObject(array('id'=>$request->id), 'tmp_payments');
        if (data_get($request, 'status') == 1){
            $contract_info = u::getObject(array('id'=>data_get($tmp_payment, 'contract_id')), 'contracts');
            u::insertSimpleRow(array(
                'contract_id' => data_get($contract_info, 'id'),
                'student_id' => data_get($contract_info, 'student_id'), 
                'branch_id' => data_get($contract_info, 'branch_id'), 
                'cm_id' => data_get($contract_info, 'cm_id'), 
                'ec_id' => data_get($contract_info, 'ec_id'), 
                'method' => data_get($tmp_payment, 'method'),
                'must_charge' => data_get($contract_info, 'must_charge'),
                'amount' => data_get($tmp_payment, 'charge_amount'),
                'total' => (int)data_get($contract_info, 'total_charged') + (int)data_get($tmp_payment, 'charge_amount'),
                'debt' => (int)data_get($contract_info, 'must_charge') - (int)data_get($contract_info, 'total_charged') - (int)data_get($tmp_payment, 'charge_amount'),
                'charge_date' => data_get($tmp_payment, 'charge_date'),
                'note' => data_get($tmp_payment, 'note'),
                'created_at'=>date('Y-m-d H:i:s'),
                'creator_id'=>Auth::user()->id,
            ), 'payments');

            $debt_amount = (int)data_get($contract_info, 'must_charge') - (int)data_get($contract_info, 'total_charged') - (int)data_get($tmp_payment, 'charge_amount');
            if($debt_amount == 0){
                u::updateSimpleRow(array(
                    'status' => 3,
                    'reservable_sessions' => floor(data_get($contract_info, 'total_sessions')/config('app.num_session_of_reservable')),
                    'summary_sessions' => data_get($contract_info, 'total_sessions'), 
                    'left_sessions' => data_get($contract_info, 'total_sessions'), 
                    'total_charged' => (int)data_get($contract_info, 'total_charged') + (int)data_get($tmp_payment, 'charge_amount'),
                    'init_total_charged' => (int)data_get($contract_info, 'total_charged') + (int)data_get($tmp_payment, 'charge_amount'),
                    'debt_amount' => 0,
                    'updated_at'=>date('Y-m-d H:i:s'),
                    'updator_id'=>Auth::user()->id,
                ), array('id'=>data_get($contract_info, 'id')), 'contracts');
                LogStudents::logAdd(data_get($contract_info, 'student_id'), 'Thu đủ phí cho hợp đồng - '.data_get($contract_info, 'code'), Auth::user()->id);
                self::processC2C(data_get($contract_info, 'student_id'), data_get($contract_info, 'init_tuition_fee_id'), data_get($contract_info, 'id'));
            }else{
                u::updateSimpleRow(array(
                    'status' => 2,
                    'total_charged' => (int)data_get($contract_info, 'total_charged') + (int)data_get($tmp_payment, 'charge_amount'),
                    'init_total_charged' => (int)data_get($contract_info, 'total_charged') + (int)data_get($tmp_payment, 'charge_amount'),
                    'debt_amount' => $debt_amount,
                    'updated_at'=>date('Y-m-d H:i:s'),
                    'updator_id'=>Auth::user()->id,
                ), array('id'=>data_get($contract_info, 'id')), 'contracts');
                LogStudents::logAdd(data_get($contract_info, 'student_id'), 'Đặt cọc '.u::formatCurrency(data_get($tmp_payment, 'charge_amount')).' cho hợp đồng - '.data_get($contract_info, 'code'), Auth::user()->id);
            }
            
            u::addLogContracts(data_get($contract_info, 'id'));
            u::updateSimpleRow(array(
                'status' => 1,
                'approver_id' => Auth::user()->id,
                'approved_at' => date('Y-m-d H:i:s'),
            ), array('id'=>data_get($tmp_payment, 'id')), 'tmp_payments');
            $result = array(
                'status' => 1,
                'message' => 'Duyệt phiếu thu thành công.'
            );
        }else {
            u::updateSimpleRow(array(
                'status' => data_get($request, 'status'),
                'approver_id' => Auth::user()->id,
                'approved_at' => date('Y-m-d H:i:s'),
            ), array('id'=>data_get($request, 'id')), 'tmp_payments');
            $result = array(
                'status' => 1,
                'message' => 'Từ chối duyệt phiếu thu thành công.'
            );
        }
        
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

    public function waitchargeApproveList(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $keyword = isset($request->keyword) ? $request->keyword : '';

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
        
        $order_by = " ORDER BY tp.id DESC ";

        $total = u::first("SELECT count(c.id) AS total 
            FROM tmp_payments AS tp 
                LEFT JOIN contracts AS c ON c.id = tp.contract_id
                LEFT JOIN students AS s ON s.id=c.student_id WHERE $cond");
        
        $list = u::query("SELECT c.id AS contract_id, s.name, s.lms_code, 
                (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= tp.creator_id) AS creator_name,
                (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= tp.approver_id) AS approver_name,
                (SELECT name FROM products WHERE id =c.product_id) AS product_name,
                c.code, (SELECT name FROM tuition_fee WHERE id=c.init_tuition_fee_id) AS tuition_fee_name,
                tp.*
            FROM tmp_payments AS tp 
                 LEFT JOIN contracts AS c ON c.id = tp.contract_id
                LEFT JOIN students AS s ON s.id=c.student_id
            WHERE $cond $order_by $limitation");
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function getWaitchargeApproveInfo(Request $request, $id){
        $paymentInfo = u::getObject(array('id'=>$id), 'tmp_payments');
        $contractInfo = u::first("SELECT c.*,c.id AS contract_id, s.name, s.lms_code, s.gud_name1, s.gud_mobile1, s.address, s.gud_email1,
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
            c.b2b_campaign_id,c.b2b_amount, c.b2b_bonus_session
        FROM contracts AS c 
            LEFT JOIN students AS s ON s.id=c.student_id WHERE c.id=".data_get($paymentInfo, 'contract_id', 0));
        return response()->json([
            'payment_info' => $paymentInfo,
            'contract_info' => $contractInfo
        ]);
    }

    public function printWaitcharge(Request $request, $id){
        $data = u::first("SELECT '' AS text_1,c.code AS contract_code, c.debt_amount, tp.charge_amount,tp.method,
            s.gud_name1,s.address, s.name,
            (SELECT number_of_months FROM tuition_fee WHERE id = c.tuition_fee_id) AS number_of_months,
            (SELECT name FROM products WHERe id=c.product_id) AS product_name, tp.note,
            '' AS text_2, '' AS text_amount, '' AS text_amount_words,'' AS text_3,'' AS text_debt_amount
          FROM tmp_payments AS tp
            LEFT JOIN contracts AS c ON tp.contract_id=c.id
            LEFT JOIN students AS s ON c.student_id = s.id
          WHERE tp.id = $id");
        $data->text_1 = "Ngày ".date('d').  " tháng ". date('m'). " năm ". date('Y');
        $data->text_2 = "Thanh toán học phí khóa học ".$data->product_name." ".$data->number_of_months." tháng cho học viên ".$data->name;
        $data->text_debt_amount = number_format($data->debt_amount, 0, '', '.');
        $data->text_amount = number_format($data->charge_amount, 0, '', '.');
        $data->text_amount_words = u::convert_number_to_words($data->charge_amount)." đồng";
        $data->text_3 = $data->method == 0 ? "Tiền mặt" : ( $data->method == 2 ? "Quẹt thẻ tín dụng" : "Chuyển khoản");
        return response()->json([
            'payment_info' => $data,
        ]);
    }
}
