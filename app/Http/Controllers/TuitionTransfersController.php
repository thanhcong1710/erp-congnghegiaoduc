<?php

namespace App\Http\Controllers;

use App\Enums\SystemCode;
use App\User;
use App\Http\Controllers\Controller;
use App\Models\LogStudents;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TuitionTransfersController extends Controller
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
            $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%' OR s1.lms_code LIKE '%$keyword%' OR s1.name LIKE '%$keyword%') ";
        }
        if (!empty($status)) {
            $cond .= " AND t.status IN (".implode(",",$status).")";
        }
        
        $order_by = " ORDER BY t.id DESC ";

        $total = u::first("SELECT count(t.id) AS total 
            FROM tuition_transfer AS t 
                LEFT JOIN students AS s ON s.id=t.from_student_id 
                LEFT JOIN students AS s1 ON s1.id=t.to_student_id 
            WHERE $cond");
        
        $list = u::query("SELECT t.id, s.name AS from_student_name, s.lms_code AS from_student_lms_code, 
                s1.name AS to_student_name, s1.lms_code AS to_student_lms_code, t.status, t.note, t.transferred_amount,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id= t.creator_id) AS creator_name,
                t.transfer_date
            FROM tuition_transfer AS t 
                LEFT JOIN students AS s ON s.id=t.from_student_id 
                LEFT JOIN students AS s1 ON s1.id=t.to_student_id 
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

    public function getDataContractActive (Request $request){
        $student_id = data_get($request,'student_id');
        $contracts = u::query("SELECT c.id AS contract_id, c.total_charged, c.summary_sessions,c.real_sessions, c.branch_id, c.product_id,
                c.bonus_sessions, c.done_sessions, c.left_sessions,c.class_id, c.code, c.enrolment_start_date, c.enrolment_last_date, c.tuition_fee_id,
                (SELECT class_day FROM classes WHERE id=c.class_id) AS class_day,
                (SELECT cls_name FROM classes WHERE id=c.class_id) AS class_name,
                (SELECT name FROM products WHERE id=c.product_id) AS product_name,
                (SELECT name FROM tuition_fee WHERE id=c.tuition_fee_id) AS tuition_fee_name
            FROM contracts AS c WHERE c.student_id =$student_id AND c.status!=7");
        return response()->json($contracts);
    }  

    public function prepareTransferData(Request $request){
        $to_product_id = data_get($request, 'to_product_id');
        $to_branch_id = data_get($request, 'to_branch_id');
        $transfer_date = data_get($request, 'transfer_date');
        $from_contracts = data_get($request, 'from_contracts');
        $transferred_contracts = [];
        $received_contracts = [];
        $total_amount_transfer = 0;
        foreach($from_contracts AS $k=> $contract){
            $contract = (object)$contract;
            if(data_get($contract, 'class_id')){
                $holidays = u::getPublicHolidays(data_get($contract,'branch_id'), data_get($contract,'product_id'));
                $arr_day = explode(",",data_get($contract, 'class_day'));
                $data_sessions = u::calculatorSessions(date('Y-m-d'), date('Y-m-d',strtotime($transfer_date)-24*3600), $holidays, $arr_day);
                $contract->done_sessions = $contract->done_sessions + (int)data_get($data_sessions, 'total', 0);
                $contract->left_sessions = $contract->left_sessions - (int)data_get($data_sessions, 'total', 0);
                $contract->left_sessions = $contract->left_sessions > 0 ? $contract->left_sessions : 0;
                $left_real_sessions = $contract->real_sessions > $contract->done_sessions ? $contract->real_sessions - $contract->done_sessions : 0;
                $left_real_amount = $contract->real_sessions ? ceil($left_real_sessions * ($contract->total_charged / $contract->real_sessions)) : 0;
            }else{
                $left_real_sessions = $contract->real_sessions > $contract->done_sessions ? $contract->real_sessions - $contract->done_sessions : 0;
                $left_real_amount = $contract->real_sessions ? ceil($left_real_sessions * ($contract->total_charged / $contract->real_sessions)) : 0; 
            }
            $contract->left_real_sessions = $left_real_sessions;
            $contract->left_amount = $left_real_amount;
            $transferred_contracts[$k] = $contract;
            $data_calc_transfer = self::calcTransferTuitionFeeForTuitionTransfer($contract->tuition_fee_id, $left_real_amount, $to_branch_id, $to_product_id);
            if(!data_get($data_calc_transfer, 'receive_tuition_fee.id')){
                $result = array(
                    'status' => 0,
                    'message' => 'Chưa có gói phí quy đổi tương ứng'
                );
                return response()->json($result);
            }
            $received_contracts[$k] = array(
                'tuition_fee_id' => data_get($data_calc_transfer, 'receive_tuition_fee.id'),
                'tuition_fee_name' => data_get($data_calc_transfer, 'receive_tuition_fee.name'),
                'total_charged' => data_get($data_calc_transfer, 'transfer_amount'),
                'real_sessions' => data_get($data_calc_transfer, 'sessions'),
                'bonus_sessions' => 0,
                'product_name' => data_get($data_calc_transfer, 'receive_tuition_fee.product_name'),
            );
            $total_amount_transfer = $total_amount_transfer + $left_real_amount;
        }
        $data = [
            'status' => 1,
            'message' => 'ok',
            'transferred_contracts' => $transferred_contracts,
            'received_contracts' => $received_contracts,
            'total_amount_transfer' => $total_amount_transfer
        ];
        return response()->json($data);
    }

    public static function calcTransferTuitionFeeForTuitionTransfer($from_tuition_fee_id, $transfer_amount, $to_branch_id, $to_product_id)
    {
        $resp = (object)[];
        if ($from_tuition_fee_id && (int)$transfer_amount >= 0) {
            $available_tuiotion_fee_ids = u::query("SELECT exchange_tuition_fee_id FROM tuition_fee_relation WHERE tuition_fee_id = $from_tuition_fee_id AND status = 1");
            if (count($available_tuiotion_fee_ids)) {
                $available_ids = [];
                foreach ($available_tuiotion_fee_ids as $id) {
                    $available_ids[] = (int)$id->exchange_tuition_fee_id;
                }
                $available_ids = implode(',', $available_ids);
                $to_tuition_fee = u::first("SELECT t.*, p.name AS product_name 
                    FROM tuition_fee AS t LEFT JOIN products AS p ON t.product_id = p.id 
                    WHERE t.product_id = $to_product_id AND (t.branch_id LIKE '%,$to_branch_id' OR t.branch_id LIKE '%,$to_branch_id,%' OR t.branch_id LIKE '$to_branch_id,%' OR t.branch_id = '$to_branch_id') 
                        AND t.id IN ($available_ids)");
                if($to_tuition_fee){
                    $resp->sessions = ceil($transfer_amount / ( $to_tuition_fee->price / $to_tuition_fee->session));
                    $resp->receive_tuition_fee = $to_tuition_fee;
                    $resp->transfer_amount = $transfer_amount;
                }
            }
        }
        return $resp;
    }

    public function add(Request $request){
        $tuition_transfer = data_get($request,'tuition_transfer'); 

        $tuition_transfer_id = u::insertSimpleRow(array(
            'from_student_id' => data_get($tuition_transfer,'from_student_id'),
            'to_student_id' =>  data_get($tuition_transfer,'to_student_id'),
            'note'=>data_get($tuition_transfer,'note'),
            'transfer_date' => data_get($tuition_transfer,'transfer_date'),
            'creator_id' => Auth::user()->id,
            'created_at' => date('Y-m-d H:i:s'),
            'status' => 1,  
            'transferred_amount' => data_get($tuition_transfer,'transferred_amount'),
            'received_amount' => data_get($tuition_transfer,'received_amount'),
            'meta_data' => json_encode($request->input()),
            'from_branch_id' => data_get($tuition_transfer,'from_branch_id'),
            'to_branch_id' => data_get($tuition_transfer,'to_branch_id'),
            'to_product_id' => data_get($tuition_transfer,'to_product_id'),
        ), 'tuition_transfer');
        
        $result = array(
            'status' => 1,
            'message' => 'Thêm mới chuyển phí thành công'
        );

        return response()->json($result);
    } 

    public function show(Request $request,$tuition_transfer_id)
    {
        $data = u::first("SELECT t.* , 
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id=t.creator_id) AS creator_name,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id=t.ceo_approver_id) AS ceo_approver_name,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id=t.accounting_approver_id) AS accounting_approver_name,
                (SELECT name FROM products WHERE id=t.to_product_id) AS to_product_name,
                (SELECT name FROM branches WHERE id=t.from_branch_id) AS from_branch_name,
                (SELECT name FROM branches WHERE id=t.to_branch_id) AS to_branch_name 
            FROM tuition_transfer AS t WHERE t.id=$tuition_transfer_id");
        $data->meta_data = $data->meta_data ? json_decode($data->meta_data) : '';
        return response()->json($data);
    }

    public function approve(Request $request){
        $tuition_transfer_id = data_get($request,'tuition_transfer_id');
        $approve = data_get($request,'approve');
        $comment = data_get($request,'comment');
        $tuition_transfer_info = u::getObject(array('id'=>$tuition_transfer_id), 'tuition_transfer');
        $result=[];
        if($tuition_transfer_info){
            if(!$approve){
                if(data_get($tuition_transfer_info, 'status') ==1){
                    u::updateSimpleRow(array(
                        'status' => 2,
                        'ceo_approver_id' => Auth::user()->id,
                        'ceo_approved_at' => date('Y-m-d H:i:s'),
                        'ceo_comment' => $comment,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updator_id' => Auth::user()->id,
                    ),array('id'=>$tuition_transfer_id),'tuition_transfer');
                }else{
                    u::updateSimpleRow(array(
                        'status' => 4,
                        'accounting_approver_id' => Auth::user()->id,
                        'accounting_approved_at' => date('Y-m-d H:i:s'),
                        'accounting_comment' => $comment,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updator_id' => Auth::user()->id,
                    ),array('id'=>$tuition_transfer_id),'tuition_transfer');
                }
                
                $result = array(
                    'status' => 1,
                    'message' => 'Từ chối phê duyệt chuyển phí thành công.'
                );
            }else{
                if(data_get($tuition_transfer_info, 'status') == 1){
                    u::updateSimpleRow(array(
                        'status' => 3,
                        'ceo_approver_id' => Auth::user()->id,
                        'ceo_approved_at' => date('Y-m-d H:i:s'),
                        'ceo_comment' => $comment,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updator_id' => Auth::user()->id,
                    ),array('id'=>$tuition_transfer_id),'tuition_transfer');
                }else{
                    u::updateSimpleRow(array(
                        'status' => 5,
                        'accounting_approver_id' => Auth::user()->id,
                        'accounting_approved_at' => date('Y-m-d H:i:s'),
                        'accounting_comment' => $comment,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updator_id' => Auth::user()->id,
                    ),array('id'=>$tuition_transfer_id),'tuition_transfer');

                    if(data_get($tuition_transfer_info, 'transfer_date') <= date('Y-m-d')){
                        u::insertSimpleRow(array(
                            'student_id'=>data_get($tuition_transfer_info, 'from_student_id'),
                            'data_id'=>$tuition_transfer_id,
                            'type' => 4,
                            'status' => 1,
                            'created_at' => date('Y-m-d H:i:s')
                        ),'student_waitting_process');
                    }else{
                        self::processTuitionTransfer($tuition_transfer_id);
                    }
                }
            }
        }
        return response()->json($result);
    }

    public static function processTuitionTransfer($tuition_transfer_id){
        $tuition_transfer_info = u::first("SELECT t.from_student_id, t.from_branch_id, t.to_branch_id, t.creator_id,
                t.to_student_id, t.to_branch_id, t.to_product_id, 
                (SELECT CONCAT(name, ' - ', lms_code) FROM students WHERE id= t.from_student_id) AS from_student_name,
                (SELECT CONCAT(name, ' - ', lms_code) FROM students WHERE id= t.to_student_id) AS to_student_name
            FROM tuition_transfer AS t  WHERE t.id = $tuition_transfer_id");
        $from_student_id = data_get($tuition_transfer_info, 'from_student_id');
        $to_student_id = data_get($tuition_transfer_info, 'to_student_id');
        $list_contract_active = u::query("SELECT * FROM contracts WHERE student_id = $from_student_id AND status!=7");
        foreach($list_contract_active AS $contract){
            $left_real_sessions = $contract->real_sessions > $contract->done_sessions ? $contract->real_sessions - $contract->done_sessions : 0;
            $left_real_amount = $contract->real_sessions ? ceil($left_real_sessions * ($contract->total_charged / $contract->real_sessions)) : 0; 
            $contract_id = 0;
            if($left_real_amount > 0){
                $last_contract_to_student = u::first("SELECT count_recharge FROM contracts WHERE student_id=$to_student_id ORDER BY count_recharge DESC LIMIT 1");
                $data_calc_transfer = self::calcTransferTuitionFeeForTuitionTransfer($contract->tuition_fee_id, $left_real_amount, data_get($tuition_transfer_info, 'to_branch_id'), data_get($tuition_transfer_info, 'to_product_id'));
                $term_student_user = u::first("SELECT * FROM term_student_user WHERE student_id = $to_student_id");
                $contract_id = u::insertSimpleRow(array(
                    'type' => data_get($contract, 'type'),
                    'reference_contract_id' => data_get($contract, 'id'),
                    'student_id' => $to_student_id, 
                    'branch_id' => data_get($tuition_transfer_info, 'to_branch_id'),
                    'ceo_branch_id' => data_get($term_student_user, 'ceo_branch_id'),
                    'ec_id' => data_get($term_student_user, 'ec_id'),
                    'ec_leader_id' => data_get($term_student_user, 'ec_leader_id'),
                    'cm_id' => data_get($term_student_user, 'cm_id'),
                    'cm_leader_id' => data_get($term_student_user, 'cm_leader_id'),
                    'product_id' => data_get($data_calc_transfer, 'receive_tuition_fee.product_id'),
                    'tuition_fee_id' => data_get($data_calc_transfer, 'receive_tuition_fee.id'),
                    'tuition_fee_amount' => data_get($data_calc_transfer, 'receive_tuition_fee.price'),
                    'tuition_fee_session' => data_get($data_calc_transfer, 'receive_tuition_fee.session'),
                    'tuition_fee_receivable' => data_get($data_calc_transfer, 'receive_tuition_fee.receivable'),
                    'must_charge' => $left_real_amount,
                    'total_charged'=> $left_real_amount,
                    'debt_amount' => 0,
                    'total_sessions' => data_get($data_calc_transfer, 'sessions'),
                    'real_sessions' => data_get($data_calc_transfer, 'sessions'),
                    'bonus_sessions' => 0,
                    'summary_sessions' => data_get($data_calc_transfer, 'sessions'),
                    'reservable_sessions' =>floor(data_get($data_calc_transfer, 'sessions')/config('app.num_session_of_reservable')),
                    'note'=> 'Nhận chuyển phí',
                    'created_at'=>date('Y-m-d H:i:s'),
                    'creator_id'=>Auth::user()->id,
                    'status' => 5,
                    'count_recharge' => $last_contract_to_student ? $last_contract_to_student->count_recharge + 1 : 0,
                ), 'contracts');
                $contract_code = str_pad((string)$contract_id, 6, '0', STR_PAD_LEFT);
                $contract_code = config('app.prefix_contract_code').$contract_code;
                u::updateSimpleRow(array('code'=>$contract_code), array('id'=>$contract_id), 'contracts');
                u::addLogContracts($contract_id);
            }

            u::updateSimpleRow(array(
                'transfer_to_contract_id' => $contract_id,
                'status' => 7,
                'type_withdraw' => 2,
            ), array('id'=>$contract->id),'contracts');
            u::addLogContracts($contract->id);
        }
        LogStudents::logAdd($from_student_id, "Chuyển phí cho học sinh '".data_get($tuition_transfer_info,'to_student_name')."'", $tuition_transfer_info->creator_id);
        LogStudents::logAdd($from_student_id, "Nhận chuyển phí từ học sinh '".data_get($tuition_transfer_info,'from_student_name')."'", $tuition_transfer_info->creator_id);
        return true;
    }
}
