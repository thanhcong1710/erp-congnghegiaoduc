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
            $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.lms_id LIKE '%$keyword%' OR s.name LIKE '%$keyword%') ";
        }
        
        $order_by = " ORDER BY r.id DESC ";

        $total = u::first("SELECT count(r.id) AS total 
            FROM reserves AS r LEFT JOIN students AS s ON s.id=r.student_id WHERE $cond");
        
        $list = u::query("SELECT r.id, s.name, s.lms_code, s.lms_id,
                (SELECT name FROM branches WHERE id=r.branch_id) AS branch_name,
                (SELECT cls_name FROM classes WHERE id=r.class_id) AS class_name,
                r.session, r.start_date, r.end_date, r.is_reserved, r.status, r.type, r.reserve_multi_id
            FROM reserves AS r 
                LEFT JOIN students AS s ON s.id=r.student_id
            WHERE $cond $order_by $limitation");
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function searchStudent(Request $request){
        $keyword = $request->keyword;
        $branch_id = $request->branch_id;
        $cond = "";
        if(!Auth::user()->checkRole('999999')){
            $cond = " AND c.enrolment_last_date >= CURRENT_DATE";
        }
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
                AND c.status=6 AND c.type>0 $cond ");
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
        $reserved_dates = u::getReservedDates_transfer($contract_id);
        if (!empty($reserved_dates)) {
            $holidays = array_merge($holidays, $reserved_dates);
        }
        $arr_day = explode(",",data_get($class_info, 'class_day'));
        $data_sessions = u::calculatorSessionsByNumberOfSessions($start_date, $reserve_session, $holidays, $arr_day);
        $end_date = data_get($data_sessions,'end_date');

        $reverse_exit = u::first("SELECT id FROM reserves WHERE contract_id=$contract_id AND status IN (1,2,4) AND
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
                'status' => 1,
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
                'status' => 4,
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
            if($start_date < date('Y-m-d')){
                u::updateScheduleHasStudent(data_get($contract_info,'id'),$start_date);
            }
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
                if(data_get($reserve_info,'is_reserved')){
                    u::updateSimpleRow(array(
                        'status' => 4,
                        'approver_id' => Auth::user()->id,
                        'approved_at' => date('Y-m-d H:i:s'),
                        'comment' => data_get($request,'comment'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updator_id' => Auth::user()->id,
                    ),array('id'=>$reserve_id),'reserves');
                    $contract_info = u::first("SELECT id, reserved_sessions FROM contracts WHERE id = $reserve_info->contract_id");
                    $reserve_session = data_get($reserve_info,'session');
                    u::updateSimpleRow(array(
                        'reserved_sessions' => data_get($contract_info, 'reserved_sessions') + $reserve_session,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updator_id' => Auth::user()->id,
                    ),array('id'=>$contract_info->id),'contracts');
                    u::updateDoneSessions(data_get($contract_info,'id'));
                }else{
                    u::updateSimpleRow(array(
                        'status' => 2,
                        'approver_id' => Auth::user()->id,
                        'approved_at' => date('Y-m-d H:i:s'),
                        'comment' => data_get($request,'comment'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updator_id' => Auth::user()->id,
                    ),array('id'=>$reserve_id),'reserves');
                    if(data_get($reserve_info,'start_date') > date('Y-m-d')){
                        u::insertSimpleRow(array(
                            'student_id'=>data_get($reserve_info, 'student_id'),
                            'data_id'=>$reserve_id,
                            'type' => 1,
                            'status' => 1,
                            'created_at' => date('Y-m-d H:i:s'),
                            'processed_at' => data_get($reserve_info, 'start_date')
                        ),'student_waitting_process');
                    } else {
                        self::processReserve($reserve_id);
                    }
                }
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

    public static function processReserve($reserve_id){
        $reserve_info = u::getObject(array('id'=>$reserve_id), 'reserves');
        $contract_info = u::first("SELECT id, reserved_sessions, student_id FROM contracts WHERE id = $reserve_info->contract_id");
        $reserve_session = data_get($reserve_info,'session');
        $start_date = data_get($reserve_info,'start_date'); 
        $end_date = data_get($reserve_info,'end_date');
        $lmsController = new LMSController();
        $lmsController->studentWithdraw(data_get($contract_info, 'student_id'));
        u::updateSimpleRow(array(
            'status' => 4,
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => data_get($reserve_info, 'approver_id'),
        ),array('id'=>$reserve_id),'reserves');
        u::updateSimpleRow(array(
            'class_id' => null,
            'status' => 4,
            'reserved_sessions' => data_get($contract_info, 'reserved_sessions') + $reserve_session,
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => data_get($reserve_info, 'approver_id'),
        ),array('id'=>$contract_info->id),'contracts');
        u::addLogContracts(data_get($contract_info,'id'));
        LogStudents::logAdd(data_get($contract_info,'student_id'), "Bảo lưu $reserve_session buổi từ ngày $start_date đến ngày $end_date", data_get($reserve_info, 'approver_id'));
        if($start_date < date('Y-m-d')){
            u::updateScheduleHasStudent(data_get($contract_info,'id'),$start_date);
        }
        return true;
    }

    public function print(Request $request, $id){
        $reserveInfo = u::first("SELECT s.gud_name1, s.name AS student_name, s.lms_id, 
                (SELECT name FROM products WHERE id=r.product_id) AS product_name,
                (SELECT charge_date FROM payments WHERE contract_id=r.contract_id ORDER BY charge_date DESC LIMIT 1) AS charge_date,
                c.must_charge, c.init_tuition_fee_session, c.done_sessions , c.last_done_sessions, c.left_sessions, c.total_charged,
                r.start_date, r.end_date, r.note
            FROM reserves AS r 
            LEFT JOIN students AS s ON s.id=r.student_id
            LEFT JOIN contracts AS c ON c.id=r.contract_id
            WHERE r.id = $id");
        $data = [
            'gud_name1' => data_get($reserveInfo, 'gud_name1'),
            'student_name' => data_get($reserveInfo, 'student_name'),
            'lms_id' => data_get($reserveInfo, 'lms_id'),
            'product_name' => data_get($reserveInfo, 'product_name'),
            'charge_date' => data_get($reserveInfo, 'charge_date') ? date('d/m/Y',strtotime(data_get($reserveInfo, 'charge_date'))): '',
            'must_charge' => number_format((int)data_get($reserveInfo, 'must_charge')),
            'init_tuition_fee_session' => data_get($reserveInfo,'init_tuition_fee_session'),
            'total_done_sessions' => (int)data_get($reserveInfo,'last_done_sessions') + (int)data_get($reserveInfo,'done_sessions'),
            'left_sessions' => data_get($reserveInfo, 'left_sessions'),
            'left_amount' => $reserveInfo->left_sessions ? round($reserveInfo->total_charged * ($reserveInfo->left_sessions -  $reserveInfo->done_sessions) / $reserveInfo->left_sessions) : 0,
            'start_date' => data_get($reserveInfo, 'start_date') ? date('d/m/Y',strtotime(data_get($reserveInfo, 'start_date'))): '',
            'end_date' => data_get($reserveInfo, 'end_date') ? date('d/m/Y',strtotime(data_get($reserveInfo, 'end_date'))): '',
            'note' => data_get($reserveInfo, 'note'),
        ]; 
        $data['left_amount'] = number_format($data['left_amount']);
        return response()->json($data);
    }

    public function listMulti(Request $request)
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
            $cond .= " AND (cl.cls_name LIKE '%$keyword%') ";
        }
        
        $order_by = " ORDER BY r.id DESC ";

        $total = u::first("SELECT count(r.id) AS total 
            FROM reserve_multis AS r LEFT JOIN classes AS cl ON cl.id=r.class_id WHERE $cond");
        
        $list = u::query("SELECT r.*,cl.cls_name AS class_name,
                (SELECT name FROM users WHERE r.creator_id=id) AS creator_name,
                (SELECT name FROM users WHERE r.approver_id=id) AS approver_name
            FROM reserve_multis AS r 
                LEFT JOIN classes AS cl ON cl.id=r.class_id
            WHERE $cond $order_by $limitation");
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function addMulti(Request $request){
        $start_date =  data_get($request,'reserve.start_date');
        $end_date =  data_get($request,'reserve.end_date');
        $class_id =  data_get($request,'reserve.class_id');
        $reverse_exit = u::first("SELECT id FROM reserve_multis WHERE class_id=$class_id AND status IN (1,2,4) AND
            ((start_date >='$start_date' AND start_date<='$end_date') OR (end_date >='$start_date' AND end_date<='$end_date')
            OR (start_date <= '$start_date' AND end_date>= '$end_date'))");
        if($reverse_exit){
            $result = array(
                'status' => 0,
                'message' => 'Đã tồn tại bản ghi bảo lưu cả lớp trong khoảng thời gian đã chọn'
            );
            return response()->json($result);
        }else {
            u::insertSimpleRow(array(
                'branch_id' => data_get($request,'reserve.branch_id'),
                'class_id'=> data_get($request,'reserve.class_id'),
                'start_date' => data_get($request,'reserve.start_date'),
                'end_date' => data_get($request,'reserve.end_date'),
                'session'=> data_get($request,'reserve.session'),
                'note'=> data_get($request,'reserve.note'),
                'list_student'=>implode(",", data_get($request, 'checked_list')),
                'status' => 1,
                'creator_id' => Auth::user()->id,
                'created_at' => date('Y-m-d H:i:s'),
                'meta_data' => json_encode($request->input())
            ), 'reserve_multis');
            $result = array(
                'status' => 1,
                'message' => 'Thêm mới bảo lưu cả lớp thành công'
            );
            return response()->json($result);
        }
    }  
    public function deleteMulti(Request $request){
        u::updateSimpleRow(array('status' => 0), array('id'=>$request->id), 'reserve_multis');
        $result = array(
            'status' => 1,
            'message' => 'Hủy bản ghi bảo lưu thành công.'
        );
        return response()->json($result);
    }

    public function showMulti(Request $request,$reserve_id)
    {
        $data = u::first("SELECT r.* , (SELECT name FROM branches WHERE id=r.branch_id) AS branch_name ,(SELECT cls_name FROM classes WHERE id=r.class_id) AS class_name 
            FROM reserve_multis AS r WHERE r.id=$reserve_id");
        $data->meta_data = $data->meta_data ? json_decode($data->meta_data,true) : '';
        if(!empty($data->meta_data['students'])){
            foreach ($data->meta_data['students'] AS $k=> $row){
                if(in_array(data_get($row, 'student_id'), $data->meta_data['checked_list'])){
                    $data->meta_data['students'][$k]['checked'] =true;
                } else {
                    $data->meta_data['students'][$k]['checked'] =false;
                }
            }
        }
        return response()->json($data);
    }

    public function approveMulti(Request $request){
        $reserve_id = data_get($request,'reserve_id');
        $status = data_get($request,'status');
        $reserve_info = u::getObject(array('id'=>$reserve_id), 'reserve_multis');
        $result=[];
        if($reserve_info){
            if($status == 3){
                u::updateSimpleRow(array(
                    'status' => 3,
                    'approver_id' => Auth::user()->id,
                    'approved_at' => date('Y-m-d H:i:s'),
                    'comment' => data_get($request,'comment'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updator_id' => Auth::user()->id,
                ),array('id'=>$reserve_id),'reserve_multis');
                $result = array(
                    'status' => 1,
                    'message' => 'Từ chối phê duyệt bảo lưu thành công.'
                );
            }else{ 
                $meta_data = $reserve_info->meta_data ? json_decode($reserve_info->meta_data,true) : '';
                $students = data_get($meta_data, 'students');
                foreach($students AS $row) {
                    if(in_array(data_get($row, 'student_id'), data_get($meta_data, 'checked_list'))){
                        $contract_info = u::first("SELECT * FROM contracts WHERE id = ".data_get($row, 'contract_id'));
                        u::insertSimpleRow(array(
                            'student_id' => data_get($row, 'student_id'),
                            'type'=> 0 ,
                            'start_date' => data_get($reserve_info,'start_date'),
                            'session'=> data_get($reserve_info,'session'),
                            'end_date' => data_get($reserve_info,'end_date'),
                            'status' => 4,
                            'creator_id' => Auth::user()->id,
                            'created_at' => date('Y-m-d H:i:s'),
                            'is_reserved' => 1,
                            'contract_id' => data_get($contract_info,'id'),
                            'branch_id' => data_get($contract_info,'branch_id'),
                            'product_id' => data_get($contract_info,'product_id'),
                            'program_id' => data_get($contract_info,'program_id'),
                            'class_id' => data_get($contract_info,'class_id'),
                            'note' => data_get($reserve_info,'note'),
                            'meta_data' => json_encode($request->input()),
                            'reserve_multi_id'=>$reserve_id
                        ), 'reserves');
                        
                        if(data_get($reserve_info,'start_date') < date('Y-m-d')){
                            u::updateScheduleHasStudent(data_get($contract_info,'id'),data_get($reserve_info,'start_date'));
                        } else {
                            u::updateDoneSessions(data_get($contract_info,'id'));
                        }
                    }
                }
                u::updateSimpleRow(array(
                    'status' => 4,
                    'approver_id' => Auth::user()->id,
                    'approved_at' => date('Y-m-d H:i:s'),
                    'comment' => data_get($request,'comment'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updator_id' => Auth::user()->id,
                ),array('id'=>$reserve_id),'reserve_multis');
                $result = array(
                    'status' => 1,
                    'message' => 'Phê duyệt bảo lưu thành công.'
                );
            }
        }
        return response()->json($result);
    }
}
