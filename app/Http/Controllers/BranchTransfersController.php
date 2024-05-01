<?php

namespace App\Http\Controllers;

use App\Enums\SystemCode;
use App\User;
use App\Http\Controllers\Controller;
use App\Models\LogStudents;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BranchTransfersController extends Controller
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
        return response()->json($contracts);
    }  

    public function getDataContractActiveByTransferDate(Request $request){
        $transfer_date = data_get($request, 'transfer_date');
        $from_contracts = data_get($request, 'from_contracts');
        $to_contracts = [];
        foreach($from_contracts AS $k=> $contract){
            $contract = (object)$contract;
            if(data_get($contract, 'class_id')){
                $holidays = u::getPublicHolidays(data_get($contract,'branch_id'), data_get($contract,'product_id'));
                $arr_day = explode(",",data_get($contract, 'class_day'));
                $data_sessions = u::calculatorSessions(date('Y-m-d'), date('Y-m-d',strtotime($transfer_date)-24*3600), $holidays, $arr_day);
                $contract->done_sessions = $contract->done_sessions + (int)data_get($data_sessions, 'total', 0);
                $contract->left_sessions = $contract->left_sessions - (int)data_get($data_sessions, 'total', 0);
                $contract->left_sessions = $contract->left_sessions > 0 ? $contract->left_sessions : 0;
            }
            $to_contracts[$k] = $contract;
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

    public function show(Request $request,$branch_transfer_id)
    {
        $data = u::first("SELECT t.* , 
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id=t.creator_id) AS creator_name,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id=t.to_approver_id) AS to_approver_name,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id=t.from_approver_id) AS from_approver_name,
                (SELECT name FROM branches WHERE id=t.from_branch_id) AS from_branch_name,
                (SELECT name FROM branches WHERE id=t.to_branch_id) AS to_branch_name 
            FROM branch_transfer AS t WHERE t.id=$branch_transfer_id");
        $data->meta_data = $data->meta_data ? json_decode($data->meta_data) : '';
        return response()->json($data);
    }

    public function approve(Request $request){
        $branch_transfer_id = data_get($request,'branch_transfer_id');
        $approve = data_get($request,'approve');
        $comment = data_get($request,'comment');
        $branch_transfer_info = u::getObject(array('id'=>$branch_transfer_id), 'branch_transfer');
        $result=[];
        if($branch_transfer_info){
            if(!$approve){
                if(data_get($branch_transfer_info, 'status') ==1){
                    u::updateSimpleRow(array(
                        'status' => 2,
                        'from_approver_id' => Auth::user()->id,
                        'from_approved_at' => date('Y-m-d H:i:s'),
                        'from_approve_comment' => $comment,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updator_id' => Auth::user()->id,
                    ),array('id'=>$branch_transfer_id),'branch_transfer');
                }else{
                    u::updateSimpleRow(array(
                        'status' => 4,
                        'to_approver_id' => Auth::user()->id,
                        'to_approved_at' => date('Y-m-d H:i:s'),
                        'to_approve_comment' => $comment,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updator_id' => Auth::user()->id,
                    ),array('id'=>$branch_transfer_id),'branch_transfer');
                }
                
                $result = array(
                    'status' => 1,
                    'message' => 'Từ chối phê duyệt chuyển trung tâm thành công.'
                );
            }else{
                if(data_get($branch_transfer_info, 'status') == 1){
                    u::updateSimpleRow(array(
                        'status' => 3,
                        'from_approver_id' => Auth::user()->id,
                        'from_approved_at' => date('Y-m-d H:i:s'),
                        'from_approve_comment' => $comment,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updator_id' => Auth::user()->id,
                    ),array('id'=>$branch_transfer_id),'branch_transfer');
                }else{
                    u::updateSimpleRow(array(
                        'status' => 5,
                        'to_approver_id' => Auth::user()->id,
                        'to_approved_at' => date('Y-m-d H:i:s'),
                        'to_approve_comment' => $comment,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updator_id' => Auth::user()->id,
                    ),array('id'=>$branch_transfer_id),'branch_transfer');

                    if(data_get($branch_transfer_info, 'transfer_date') <= date('Y-m-d')){
                        u::insertSimpleRow(array(
                            'student_id'=>data_get($branch_transfer_info, 'student_id'),
                            'data_id'=>$branch_transfer_id,
                            'type' => 3,
                            'status' => 1,
                            'created_at' => date('Y-m-d H:i:s')
                        ),'student_waitting_process');
                    }else{
                        self::processBranchTransfer($branch_transfer_id);
                    }
                }
            }
        }
        return response()->json($result);
    }

    public static function processBranchTransfer($branch_transfer_id){
        $branch_transfer_info = u::first("SELECT t.student_id, t.from_branch_id, t.to_branch_id,
                (SELECT name FROM branches WHERE id=t.from_branch_id) AS from_branch_name,
                (SELECT name FROM branches WHERE id=t.to_branch_id) AS to_branch_name, creator_id
            FROM branch_transfer AS t  WHERE t.id = $branch_transfer_id");
        $student_id = data_get($branch_transfer_info, 'student_id');
        $list_contract_active = u::query("SELECT id, `status` FROM contracts WHERE student_id = $student_id AND status!=7");
        foreach($list_contract_active AS $contract){
            if($contract->status == 6){
                u::updateSimpleRow(array(
                    'status'=> 4,
                    'class_id'=>null,
                    'branch_id'=> data_get($branch_transfer_info, 'to_branch_id'),
                    'cm_id'=> null,
                    'cm_leader_id'=>null,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updator_id' => Auth::user()->id,
                ), array('id'=>$contract->id),'contracts');
            }else{
                u::updateSimpleRow(array(
                    'branch_id'=> data_get($branch_transfer_info, 'to_branch_id'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updator_id' => Auth::user()->id,
                ), array('id'=>$contract->id),'contracts');
            }
            u::addLogContracts($contract->id);
        }

        u::updateSimpleRow(array(
            'cm_id'=> null,
            'teacher_id'=>null,
            'cm_leader_id' => null,
            'branch_id' => data_get($branch_transfer_info, 'to_branch_id'),
            'updated_at' => date('Y-m-d H:i:s'),
        ), array('student_id'=>$student_id), 'term_student_user');
        u::updateSimpleRow(array(
            'status'=> 6,
            'updated_at' => date('Y-m-d H:i:s')
        ), array('id'=>$branch_transfer_id), 'branch_transfer');
        LogStudents::logAdd($student_id, "Chuyển từ $branch_transfer_info->from_branch_name sang $branch_transfer_info->to_branch_name", $branch_transfer_info->creator_id);
        return true;
    }

    public function getLogsByStudent(Request $request, $student_id){
        $data = u::query("SELECT t.id, t.status, t.transfer_date,
                (SELECT name FROM branches WHERE id=t.from_branch_id) AS from_branch_name,
                (SELECT name FROM branches WHERE id=t.to_branch_id) AS to_branch_name
            FROM branch_transfer AS t 
            WHERE t.student_id = $student_id AND t.status ORDER BY t.id DESC");
        return response()->json($data);
    }
}
