<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use App\Models\LogStudents;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservesController extends Controller
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
        $cond = " r.status > 0 ";
        $cond .= " AND r.branch_id IN (" . Auth::user()->getBranchesHasUser().")";

        if (!empty($branch_id)) {
            $cond .= " AND r.branch_id IN (".implode(",",$branch_id).")";
        }
        if (!empty($status)) {
            $cond .= " AND r.status IN (".implode(",",$status).")";
        }
        if ($keyword !== '') {
            $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%') ";
        }
        
        $order_by = " ORDER BY r.id DESC ";

        $total = u::first("SELECT count(r.id) AS total 
            FROM reserves AS r LEFT JOIN students AS s ON s.id=r.student_id WHERE $cond");
        
        $list = u::query("SELECT r.id, s.name, s.lms_code,
                (SELECT name FROM branches WHERE id=r.branch_id) AS branch_name,
                (SELECT cls_name FROM classes WHERE id=r.class_id) AS class_name,
                r.session, r.start_date, r.end_date, r.is_reserved, r.status
            FROM reserves AS r 
                LEFT JOIN students AS s ON s.id=r.student_id
            WHERE $cond $order_by $limitation");
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function searchStudent(Request $request){
        $keyword = $request->keyword;
        $branch_id = $request->branch_id;
        $data = u::query("SELECT s.name, s.lms_code, s.gud_name1, s.gud_mobile1, s.gud_email1, s.address,
                s.id AS student_id, CONCAT(s.name, ' - ', s.lms_code) AS label,
                (SELECT name FROM products WHERE id=c.product_id) AS product_name,
                (SELECT name FROM programs WHERE id=c.program_id) AS program_name,
                (SELECT cls_name FROM classes WHERE id=c.class_id) AS class_name,
                (SELECT name FROM tuition_fee WHERE id=c.tuition_fee_id) AS tuition_fee_name,
                c.total_charged, c.summary_sessions, c.bonus_sessions, c.real_sessions, c.reservable_sessions, 
                c.reserved_sessions , c.product_id, c.class_id, c.id AS contract_id, c.enrolment_start_date, c.enrolment_last_date  
            FROM contracts AS c LEFT JOIN students AS s ON c.student_id=s.id 
                WHERE c.branch_id= $branch_id AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%')
                AND c.status=6 AND c.enrolment_last_date >= CURRENT_DATE ");
        return response()->json($data);
    }  

    public function add(Request $request){
        $is_reserved = data_get($request,'reserve.is_reserved');
        $contract_id= data_get($request,'reserve.contract_id');
        $reserve_session = data_get($request,'reserve.session'); 
        $start_date =  data_get($request,'reserve.start_date'); 
        $contract_info = u::first("SELECT id, reserved_sessions, reservable_sessions,student_id, 
                product_id, program_id, class_id, branch_id   
            FROM contracts WHERE id=$contract_id");
        $class_info = u::first("SELECT class_day FROM classes WHERE id = ".(int)data_get($contract_info,'class_id'));
        $left_reserve_session = (int)data_get($contract_info,'reservable_sessions') - (int)data_get($contract_info,'reserved_sessions');
        $required_approve = 0;
        if($is_reserved == 0 || $reserve_session > $left_reserve_session){
            $required_approve = 1;
        }

        $holidays = u::getPublicHolidays(data_get($contract_info,'branch_id'), data_get($contract_info,'product_id'));
        $arr_day = explode(",",data_get($class_info, 'class_day'));
        $data_sessions = u::calculatorSessionsByNumberOfSessions($start_date, $reserve_session, $holidays, $arr_day);
        $end_date = data_get($data_sessions,'end_date');

        $reverse_exit = u::first("SELECT id FROM reserves WHERE contract_id=$contract_id AND status IN (1,2) AND
            ((start_date >='$start_date' AND start_date<='$end_date') OR (end_date >='$start_date' AND end_date<='$end_date'))");
        if($reverse_exit){
            $result = array(
                'status' => 0,
                'message' => 'Đã tồn tại bản ghi bảo lưu trong khoảng thời gian đã chọn'
            );
            return response()->json($result);
        }

        if($required_approve){
            u::insertSimpleRow(array(
                'student_id' => data_get($contract_info,'student_id'),
                'type'=> 1 ,
                'start_date' => $start_date,
                'session'=> $reserve_session,
                'end_date' => $end_date,
                'status' => $required_approve ? 1 : 2,
                'creator_id' => Auth::user()->id,
                'created_at' => date('Y-m-d H:i:s'),
                'is_reserved' => $is_reserved,
                'contract_id' => data_get($contract_info,'id'),
                'branch_id' => data_get($contract_info,'branch_id'),
                'product_id' => data_get($contract_info,'product_id'),
                'program_id' => data_get($contract_info,'program_id'),
                'class_id' => data_get($contract_info,'class_id'),
                'note' => data_get($request,'reserve.note'),
                'meta_data' => json_encode($request->input())
            ), 'reserves');
            $result = array(
                'status' => 1,
                'message' => 'Loại bảo lưu đặc biệt cần GĐTT phê duyệt'
            );
        }else{
            u::insertSimpleRow(array(
                'student_id' => data_get($contract_info,'student_id'),
                'type'=> 0 ,
                'start_date' => $start_date,
                'session'=> $reserve_session,
                'end_date' => $end_date,
                'status' => $required_approve ? 1 : 2,
                'creator_id' => Auth::user()->id,
                'created_at' => date('Y-m-d H:i:s'),
                'is_reserved' => $is_reserved,
                'contract_id' => data_get($contract_info,'id'),
                'branch_id' => data_get($contract_info,'branch_id'),
                'product_id' => data_get($contract_info,'product_id'),
                'program_id' => data_get($contract_info,'program_id'),
                'class_id' => data_get($contract_info,'class_id'),
                'note' => data_get($request,'reserve.note'),
                'meta_data' => json_encode($request->input())
            ), 'reserves');
            u::updateSimpleRow(array(
                'reserved_sessions' => data_get($contract_info, 'reserved_sessions') + $reserve_session,
                'updated_at' => date('Y-m-d H:i:s'),
                'updator_id' => Auth::user()->id,
            ),array('id'=>$contract_id),'contracts');
            u::addLogContracts(data_get($contract_info,'id'));
            LogStudents::logAdd(data_get($contract_info,'student_id'), "Bảo lưu $reserve_session buổi từ ngày $start_date đến ngày $end_date", Auth::user()->id);

            $result = array(
                'status' => 1,
                'message' => 'Thêm mới bảo lưu thành công'
            );
            
        }

        return response()->json($result);
    }  

    public function delete(Request $request){
        u::updateSimpleRow(array('status' => 0), array('id'=>$request->id), 'reserves');
        $result = array(
            'status' => 1,
            'message' => 'Hủy bản ghi bảo lưu thành công.'
        );
        return response()->json($result);
    }

    public function show(Request $request,$reserve_id)
    {
        $data = u::first("SELECT r.* , (SELECT name FROM branches WHERE id=r.branch_id) AS branch_name 
            FROM reserves AS r WHERE r.id=$reserve_id");
        $data->meta_data = $data->meta_data ? json_decode($data->meta_data) : '';
        return response()->json($data);
    }

    public function approve(Request $request){
        $reserve_id = data_get($request,'reserve_id');
        $status = data_get($request,'status');
        $reserve_info = u::getObject(array('id'=>$reserve_id), 'reserves');
        $result=[];
        if($reserve_info){
            $contract_info = u::first("SELECT id, reserved_sessions FROM contracts WHERE id = $reserve_info->contract_id");
            if($status == 3){
                u::updateSimpleRow(array(
                    'status' => 3,
                    'approver_id' => Auth::user()->id,
                    'approved_at' => date('Y-m-d H:i:s'),
                    'comment' => data_get($request,'comment'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updator_id' => Auth::user()->id,
                ),array('id'=>$reserve_id),'reserves');
                $result = array(
                    'status' => 1,
                    'message' => 'Từ chối phê duyệt bảo lưu thành công.'
                );
            }else{
                $reserve_session = data_get($reserve_info,'session');
                $start_date = data_get($reserve_info,'start_date'); 
                $end_date = data_get($reserve_info,'end_date'); 
                u::updateSimpleRow(array(
                    'status' => 2,
                    'approver_id' => Auth::user()->id,
                    'approved_at' => date('Y-m-d H:i:s'),
                    'comment' => data_get($request,'comment'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updator_id' => Auth::user()->id,
                ),array('id'=>$reserve_id),'reserves');
                
                if(data_get($reserve_info,'is_reserved')){
                    u::updateSimpleRow(array(
                        'reserved_sessions' => data_get($contract_info, 'reserved_sessions') + $reserve_session,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updator_id' => Auth::user()->id,
                    ),array('id'=>$contract_info->id),'contracts');
                }else{
                    u::updateSimpleRow(array(
                        'class_id' => null,
                        'status' => 4,
                        'reserved_sessions' => data_get($contract_info, 'reserved_sessions') + $reserve_session,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updator_id' => Auth::user()->id,
                    ),array('id'=>$contract_info->id),'contracts');
                }
                u::addLogContracts(data_get($contract_info,'id'));
                LogStudents::logAdd(data_get($contract_info,'student_id'), "Bảo lưu $reserve_session buổi từ ngày $start_date đến ngày $end_date", Auth::user()->id);
                $result = array(
                    'status' => 1,
                    'message' => 'Phê duyệt bảo lưu thành công.'
                );
            }
        }
        return response()->json($result);
    }

    public function getLogsByStudent(Request $request, $student_id){
        $data = u::query("SELECT r.id, r.created_at, r.type, r.session, r.start_date, r.is_reserved,r.approved_at, r.status,
                (SELECT name FROM branches WHERE id=r.branch_id) AS branch_name,
                (SELECT cls_name FROM classes WHERE id=r.class_id) AS class_name,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id=r.creator_id) AS creator_name,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id=r.approver_id) AS approver_name
            FROM reserves AS r 
            WHERE r.student_id = $student_id AND r.status > 0 ORDER BY r.id DESC");
        return response()->json($data);
    }
}
