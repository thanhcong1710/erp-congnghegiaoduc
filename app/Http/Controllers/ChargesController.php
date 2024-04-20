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
                'total_charged' => (int)data_get($contract_info, 'total_charged') + (int)data_get($request, 'amount'),
                'debt_amount' => 0,
                'updated_at'=>date('Y-m-d H:i:s'),
                'updator_id'=>Auth::user()->id,
            ), array('id'=>data_get($contract_info, 'id')), 'contracts');
            LogStudents::logAdd(data_get($contract_info, 'student_id'), 'Thu đủ phí cho hợp đồng - '.data_get($contract_info, 'code'), Auth::user()->id);
        }else{
            u::updateSimpleRow(array(
                'status' => 2,
                'total_charged' => (int)data_get($contract_info, 'total_charged') + (int)data_get($request, 'amount'),
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
}
