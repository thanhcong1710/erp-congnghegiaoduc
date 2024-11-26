<?php

namespace App\Http\Controllers;

use App\Enums\SystemCode;
use App\User;
use App\Http\Controllers\Controller;
use App\Models\LogStudents;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExchangesController extends Controller
{
    public function list(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $keyword = isset($request->keyword) ? $request->keyword : '';

        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        $cond = " e.branch_id IN (" . Auth::user()->getBranchesHasUser().") ";

        if (!empty($branch_id)) {
            $cond .= " AND e.branch_id IN (".implode(",",$branch_id).") ";
        }
        if ($keyword !== '') {
            $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%') ";
        }
        
        $order_by = " ORDER BY e.id DESC ";

        $total = u::first("SELECT count(e.id) AS total 
            FROM exchanges AS e 
                LEFT JOIN students AS s ON s.id=e.student_id 
                LEFT JOIN contracts AS c ON e.contract_id = c.id
            WHERE $cond");
        
        $list = u::query("SELECT s.name, s.lms_code, c.code AS contract_code,
                (SELECT name FROM branches WHERE id= e.branch_id) AS branch_name,
                (SELECT name FROM tuition_fee WHERE id= e.from_tuition_fee_id) AS from_tuition_fee_name,
                (SELECT name FROM tuition_fee WHERE id= e.to_tuition_fee_id) AS to_tuition_fee_name,
                (SELECT name FROM products WHERE id= e.from_product_id) AS from_product_name,
                (SELECT name FROM products WHERE id= e.to_product_id) AS to_product_name,
                e.from_left_sessions, e.to_left_sessions, e.amount
            FROM exchanges AS e 
                LEFT JOIN students AS s ON s.id=e.student_id 
                LEFT JOIN contracts AS c ON e.contract_id = c.id
            WHERE $cond $order_by $limitation");
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }
    public function searchStudent(Request $request){
        $keyword = $request->keyword;
        $branch_id = $request->branch_id;
        $data = u::query("SELECT s.name, s.lms_code, s.gud_name1, s.gud_mobile1, s.gud_email1, s.address,
                s.id AS student_id, CONCAT(s.name, ' - ', s.lms_code) AS label,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id=t.cm_id) AS cm_name,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id=t.ec_id) AS ec_name
            FROM students AS s 
                LEFT JOIN term_student_user AS t ON t.student_id=s.id 
                WHERE t.branch_id= $branch_id AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%')
                AND s.status>0 ");
        return response()->json($data);
    }  

    public function getDataFromContractActive (Request $request){
        $student_id = data_get($request,'student_id');
        $contracts = u::query("SELECT c.id AS contract_id, c.total_charged, c.summary_sessions,c.real_sessions, c.branch_id, c.product_id,
                c.bonus_sessions, c.done_sessions, c.left_sessions,c.class_id, c.code, c.enrolment_start_date, c.enrolment_last_date,
                (SELECT class_day FROM classes WHERE id=c.class_id) AS class_day,
                (SELECT cls_name FROM classes WHERE id=c.class_id) AS class_name,
                (SELECT name FROM products WHERE id=c.product_id) AS product_name,
                (SELECT name FROM tuition_fee WHERE id=c.tuition_fee_id) AS tuition_fee_name
            FROM contracts AS c WHERE c.student_id =$student_id AND c.status!=7 AND c.class_id IS NULL");
        foreach ($contracts AS $k=> $contract){
            $left_real_sessions = $contract->real_sessions > $contract->done_sessions ? $contract->real_sessions - $contract->done_sessions : 0;
            $left_real_amount = $contract->real_sessions ? ceil($left_real_sessions * ($contract->total_charged / $contract->real_sessions)) : 0; 
            $contracts[$k]->left_amount = $left_real_amount;
        }
        return response()->json($contracts);
    }  

    public function getDataContractExchange(Request $request){
        $to_product_id = data_get($request, 'to_product_id');
        $student_id = data_get($request,'student_id');
        $contracts = u::query("SELECT c.id AS contract_id, c.total_charged, c.summary_sessions,c.real_sessions, c.branch_id, c.product_id,
                c.bonus_sessions, c.done_sessions, c.left_sessions,c.class_id, c.code, c.enrolment_start_date, c.enrolment_last_date,c.tuition_fee_id,
                (SELECT class_day FROM classes WHERE id=c.class_id) AS class_day,
                (SELECT cls_name FROM classes WHERE id=c.class_id) AS class_name,
                (SELECT name FROM products WHERE id=c.product_id) AS product_name,
                (SELECT name FROM tuition_fee WHERE id=c.tuition_fee_id) AS tuition_fee_name
            FROM contracts AS c WHERE c.student_id =$student_id AND c.status!=7 AND c.class_id IS NULL");
        $to_contracts = [];
        $contract_in_class = 0;
        $contract_exchange = 0;
        foreach ($contracts as $k => $contract) {
            $left_real_sessions = $contract->real_sessions > $contract->done_sessions ? $contract->real_sessions - $contract->done_sessions : 0;
            $left_real_amount = $contract->real_sessions ? ceil($left_real_sessions * ($contract->total_charged / $contract->real_sessions)) : 0; 
            $contracts[$k]->left_amount = $left_real_amount;
            if(data_get($contract, 'class_id')){
                $to_contracts[$k] = $contracts[$k];
                $contract_in_class++;
            } else {
                if($to_product_id != $contracts[$k]->product_id){
                    $data_calc_transfer = u::calcTransferTuitionFeeForTuitionTransfer($contract->tuition_fee_id, $left_real_amount, $contract->branch_id, $to_product_id);
                    if(data_get($data_calc_transfer, 'receive_tuition_fee.id')){
                        $to_contracts[$k] = $contracts[$k];
                        $to_contracts[$k]->tuition_fee_id = data_get($data_calc_transfer, 'receive_tuition_fee.id');
                        $to_contracts[$k]->tuition_fee_name = data_get($data_calc_transfer, 'receive_tuition_fee.name');
                        $to_contracts[$k]->total_charged = data_get($data_calc_transfer, 'transfer_amount', 0);
                        $to_contracts[$k]->real_sessions = data_get($data_calc_transfer, 'sessions', 0);
                        $to_contracts[$k]->bonus_sessions = $contract->bonus_sessions > $contract->left_sessions ? $contract->left_sessions : $contract->bonus_sessions;
                        $to_contracts[$k]->summary_sessions = $to_contracts[$k]->real_sessions + $to_contracts[$k]->bonus_sessions;
                        $to_contracts[$k]->product_name = data_get($data_calc_transfer, 'receive_tuition_fee.product_name');
                        $contract_exchange++;
                    }
                }
            }
        }
        return response()->json([
            'contract_exchange' => $contract_exchange, 
            'to_contracts' => $to_contracts
        ]);
    }
    
    public function add(Request $request){
        $exchange = data_get($request,'exchange'); 
        $to_product_id = data_get($exchange, 'to_product_id');
        $student_id = data_get($exchange,'student_id');
        $contracts = u::query("SELECT c.id, c.id AS contract_id, c.total_charged, c.summary_sessions,c.real_sessions, c.branch_id, c.product_id,
                c.bonus_sessions, c.done_sessions, c.left_sessions,c.class_id, c.code, c.enrolment_start_date, c.enrolment_last_date,c.tuition_fee_id,
                (SELECT class_day FROM classes WHERE id=c.class_id) AS class_day,
                (SELECT cls_name FROM classes WHERE id=c.class_id) AS class_name,
                (SELECT name FROM products WHERE id=c.product_id) AS product_name,
                (SELECT name FROM tuition_fee WHERE id=c.tuition_fee_id) AS tuition_fee_name
            FROM contracts AS c WHERE c.student_id =$student_id AND c.status!=7 AND c.class_id IS NULL");
        foreach ($contracts AS $k => $contract){
            if($to_product_id != $contract->product_id){
                $left_real_sessions = $contract->real_sessions > $contract->done_sessions ? $contract->real_sessions - $contract->done_sessions : 0;
                $left_real_amount = $contract->real_sessions ? ceil($left_real_sessions * ($contract->total_charged / $contract->real_sessions)) : 0; 
                $data_calc_transfer = u::calcTransferTuitionFeeForTuitionTransfer($contract->tuition_fee_id, $left_real_amount, $contract->branch_id, $to_product_id);
                if(data_get($data_calc_transfer, 'receive_tuition_fee.id')){
                    $bonus_sessions = $contract->bonus_sessions > $contract->left_sessions ? $contract->left_sessions : $contract->bonus_sessions;
                    u::updateSimpleRow(array(
                        'product_id'=> $to_product_id,
                        'program_id' => NULL,
                        'tuition_fee_id' => data_get($data_calc_transfer, 'receive_tuition_fee.id'),
                        'total_charged' => data_get($data_calc_transfer, 'transfer_amount', 0),
                        'real_sessions' => data_get($data_calc_transfer, 'sessions', 0),
                        'bonus_sessions' => $bonus_sessions,
                        'summary_sessions' => data_get($data_calc_transfer, 'sessions', 0) + $bonus_sessions,
                        'done_sessions' => 0,
                        'left_sessions' => data_get($data_calc_transfer, 'sessions', 0) + $bonus_sessions,
                        'last_done_sessions' => $contract->done_sessions,
                        'updator_id' => Auth::user()->id,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'action' => 'contract_exchange_product_id'
                    ), array('id'=>$contract->id),'contracts');

                    u::addLogContracts($contract->id);
                    u::insertSimpleRow(array(
                        'contract_id' => $contract->id,
                        'branch_id' => $contract->branch_id,
                        'student_id' => $student_id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'creator_id' => Auth::user()->id,
                        'from_product_id' => $contract->product_id,
                        'to_product_id' => $to_product_id,
                        'from_tuition_fee_id' => $contract->tuition_fee_id,
                        'to_tuition_fee_id' => data_get($data_calc_transfer, 'receive_tuition_fee.id'),
                        'from_left_sessions' => $contract->left_sessions,
                        'to_left_sessions' => data_get($data_calc_transfer, 'sessions', 0) + $bonus_sessions,
                        'amount' => data_get($data_calc_transfer, 'transfer_amount', 0),
                    ), 'exchanges');
                }
            }
        }
        
        $result = array(
            'status' => 1,
            'message' => 'Thực hiện quy đổi thành công'
        );

        return response()->json($result);
    } 
}
