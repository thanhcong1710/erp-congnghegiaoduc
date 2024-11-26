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
        $status = isset($request->status) ? $request->status : [];

        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        $cond = " t.status > 0 ";
        $cond .= " AND (t.from_branch_id IN (" . Auth::user()->getBranchesHasUser().") OR t.to_branch_id IN (" . Auth::user()->getBranchesHasUser()."))";

        if (!empty($branch_id)) {
            $cond .= " AND (t.from_branch_id IN (".implode(",",$branch_id).") OR t.to_branch_id IN (".implode(",",$branch_id)."))";
        }
        if ($keyword !== '') {
            $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%') ";
        }
        if (!empty($status)) {
            $cond .= " AND t.status IN (".implode(",",$status).")";
        }
        
        $order_by = " ORDER BY t.id DESC ";

        $total = u::first("SELECT count(t.id) AS total 
            FROM branch_transfer AS t LEFT JOIN students AS s ON s.id=t.student_id WHERE $cond");
        
        $list = u::query("SELECT t.id, s.name, s.lms_code, t.status, t.note,
                (SELECT name FROM branches WHERE id= t.from_branch_id) AS from_branch_name,
                (SELECT name FROM branches WHERE id= t.to_branch_id) AS to_branch_name,
                t.transfer_date
            FROM branch_transfer AS t 
                LEFT JOIN students AS s ON s.id=t.student_id
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
            FROM contracts AS c WHERE c.student_id =$student_id AND c.status!=7");
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
            FROM contracts AS c WHERE c.student_id =$student_id AND c.status!=7");;
        $to_contracts = [];
        foreach ($contracts as $k => $contract) {
            $left_real_sessions = $contract->real_sessions > $contract->done_sessions ? $contract->real_sessions - $contract->done_sessions : 0;
            $left_real_amount = $contract->real_sessions ? ceil($left_real_sessions * ($contract->total_charged / $contract->real_sessions)) : 0; 
            $contracts[$k]->left_amount = $left_real_amount;
            if(data_get($contract, 'class_id') && 1==2){
                $to_contracts[$k] = $contracts[$k];
            } else {
                $data_calc_transfer = u::calcTransferTuitionFeeForTuitionTransfer($contract->tuition_fee_id, $left_real_amount, $contract->branch_id, $to_product_id);
                $to_contracts[$k] = $contracts[$k];
                $to_contracts[$k]-> tuition_fee_id = data_get($data_calc_transfer, 'receive_tuition_fee.id');
                $to_contracts[$k]-> tuition_fee_name = data_get($data_calc_transfer, 'receive_tuition_fee.name');
                $to_contracts[$k]-> total_charged = data_get($data_calc_transfer, 'transfer_amount');
                $to_contracts[$k]-> real_sessions = data_get($data_calc_transfer, 'sessions');
                $to_contracts[$k]-> summary_sessions = $to_contracts[$k]-> real_sessions + $to_contracts[$k]-> bonus_sessions;
                $to_contracts[$k]-> product_name = data_get($data_calc_transfer, 'receive_tuition_fee.product_name');
                
            }
        }
        return response()->json($to_contracts);
    }
    
    public function add(Request $request){
        $branch_transfer = data_get($request,'branch_transfer'); 
        $transfer_date =  data_get($branch_transfer,'transfer_date'); 
        $from_contracts = data_get($request,'from_contracts');
        $list_contract_id = "";
        foreach($from_contracts AS $contract){
            $list_contract_id.= $list_contract_id ? ",".data_get($contract, 'contract_id') : data_get($contract, 'contract_id');
        }
        
        $branch_transfer_id = u::insertSimpleRow(array(
            'student_id' => data_get($branch_transfer,'student_id'),
            'list_contract_id' =>  $list_contract_id,
            'from_branch_id'=>data_get($branch_transfer,'branch_id'),
            'to_branch_id' => data_get($branch_transfer,'to_branch_id'),
            'creator_id' => Auth::user()->id,
            'created_at' => date('Y-m-d H:i:s'),
            'status' => 1,  
            'note' => data_get($branch_transfer,'note'),
            'transfer_date' => $transfer_date,
            'meta_data' => json_encode($request->input())
        ), 'branch_transfer');
        
        $result = array(
            'status' => 1,
            'message' => 'Thêm mới chuyển trung tâm thành công'
        );

        return response()->json($result);
    } 
}
