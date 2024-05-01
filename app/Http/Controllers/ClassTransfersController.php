<?php

namespace App\Http\Controllers;

use App\Enums\SystemCode;
use App\User;
use App\Http\Controllers\Controller;
use App\Models\LogStudents;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassTransfersController extends Controller
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
        $cond = " t.status > 0 ";
        $cond .= " AND (t.from_branch_id IN (" . Auth::user()->getBranchesHasUser().") OR t.to_branch_id IN (" . Auth::user()->getBranchesHasUser()."))";

        if (!empty($branch_id)) {
            $cond .= " AND (t.from_branch_id IN (".implode(",",$branch_id).") OR t.to_branch_id IN (".implode(",",$branch_id)."))";
        }
        if ($keyword !== '') {
            $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%') ";
        }
        
        $order_by = " ORDER BY t.id DESC ";

        $total = u::first("SELECT count(t.id) AS total 
            FROM class_transfer AS t LEFT JOIN students AS s ON s.id=t.student_id WHERE $cond");
        
        $list = u::query("SELECT t.id, s.name, s.lms_code,
                (SELECT cls_name FROM classes WHERE id= t.from_class_id) AS from_class_name,
                (SELECT cls_name FROM classes WHERE id= t.to_class_id) AS to_class_name,
                (SELECT name FROM branches WHERE id= t.from_branch_id) AS from_branch_name,
                t.transfer_date
            FROM class_transfer AS t 
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
                (SELECT name FROM products WHERE id=c.product_id) AS product_name,
                (SELECT name FROM programs WHERE id=c.program_id) AS program_name,
                (SELECT cls_name FROM classes WHERE id=c.class_id) AS class_name,
                (SELECT name FROM tuition_fee WHERE id=c.tuition_fee_id) AS tuition_fee_name,
                c.total_charged, c.summary_sessions, c.bonus_sessions, c.real_sessions,
                c.product_id, c.class_id, c.id AS contract_id, c.enrolment_start_date, c.enrolment_last_date ,
                0 AS done_sessions, 0 AS left_sessions 
            FROM contracts AS c LEFT JOIN students AS s ON c.student_id=s.id 
                WHERE c.branch_id= $branch_id AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%')
                AND c.status=6 AND c.enrolment_last_date >= CURRENT_DATE ");
        return response()->json($data);
    }  

    public function getLeftSessions (Request $request){
        $transfer_date = data_get($request,'transfer_date');
        $contract_id = data_get($request,'contract_id');
        $contract_info = u::first("SELECT cl.class_day, c.branch_id, c.product_id, c.summary_sessions, 0 AS done_sessions
            FROM contracts AS c LEFT JOIN classes AS cl ON cl.id=c.class_id WHERE c.id= $contract_id");

        $holidays = u::getPublicHolidays(data_get($contract_info,'branch_id'), data_get($contract_info,'product_id'));
        $arr_day = explode(",",data_get($contract_info, 'class_day'));
        $data_sessions = u::calculatorSessions(date('Y-m-d'), date('Y-m-d',strtotime($transfer_date)-24*3600), $holidays, $arr_day);
        $left_sessions = (int)data_get($contract_info, 'summary_sessions') - (int)data_get($contract_info, 'done_sessions') - (int)data_get($data_sessions, 'total');
        $left_sessions = $left_sessions > 0 ? $left_sessions : 0;  
        return response()->json($left_sessions);
    }  

    public function add(Request $request){
        $transfer_date =  data_get($request,'class_transfer.transfer_date'); 
        $contract_id= data_get($request,'class_transfer.contract_id');
        $to_class_id = data_get($request,'class_transfer.class_id');
        $contract_info = u::first("SELECT id, student_id, product_id, program_id, class_id, branch_id   
            FROM contracts WHERE id=$contract_id");
        $to_class_info = u::first("SELECT id AS class_id, product_id, program_id, branch_id   
            FROM classes WHERE id=$to_class_id");
        
        $class_transfer_id = u::insertSimpleRow(array(
            'student_id' => data_get($contract_info,'student_id'),
            'contract_id' => data_get($contract_info,'id'),
            'from_class_id' => data_get($contract_info,'class_id'),
            'from_branch_id'=>data_get($contract_info,'branch_id'),
            'from_product_id'=>data_get($contract_info,'product_id'),
            'from_program_id'=>data_get($contract_info,'program_id'),
            'to_class_id' => data_get($to_class_info,'class_id'),
            'to_branch_id' => data_get($to_class_info,'branch_id'),
            'to_product_id' => data_get($to_class_info,'product_id'),
            'to_program_id' => data_get($to_class_info,'program_id'),
            'creator_id' => Auth::user()->id,
            'created_at' => date('Y-m-d H:i:s'),
            'status' => 1,  // 0:đã hủy, 1: đã duyệt (chuyển lớp ko cần duyệt), 2: đã xử lý
            'note' => data_get($request,'class_transfer.note'),
            'transfer_date' => $transfer_date,
            'meta_data' => json_encode($request->input())
        ), 'class_transfer');

        if($transfer_date == date('Y-m-d')){
            u::insertSimpleRow(array(
                'student_id'=>data_get($contract_info, 'student_id'),
                'data_id'=>$class_transfer_id,
                'type' => 2,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ),'student_waitting_process');
        }else{
            self::processClassTransfer($class_transfer_id);
        }
        $result = array(
            'status' => 1,
            'message' => 'Thêm mới chuyển lớp thành công'
        );

        return response()->json($result);
    } 
    public static function processClassTransfer($class_transfer_id){
        $class_transfer_info = u::first("SELECT ct.student_id, ct.contract_id, ct.from_class_id, ct.to_class_id, ct.creator_id,
                ct.to_product_id, ct.to_program_id,
                (SELECT cls_name FROM classes WHERE id = ct.from_class_id) AS from_class_name,
                cl.cls_name AS to_class_name, cl.cm_id, cl.teacher_id
            FROM class_transfer AS ct LEFT JOIN classes AS cl ON cl.id= ct.to_class_id WHERE ct.id = $class_transfer_id");
        $contract_id = data_get($class_transfer_info,'contract_id');
        $cm_id =data_get($class_transfer_info,'cm_id', null);
        $teacher_id =data_get($class_transfer_info,'teacher_id', null);
        $student_id = data_get($class_transfer_info,'student_id');
        $cm_leader = u::first("SELECT ul.id 
            FROM users AS u 
                LEFT JOIN users AS ul ON ul.id=u.manager_id
                LEFT JOIN role_has_user AS ru ON ru.user_id= ul.id 
                LEFT JOIN roles AS r ON r.id=ru.role_id 
            WHERE r.code = '".SystemCode::ROLE_CM_LEADER."' AND ul.status=1 AND u.id = ".(int)$cm_id." LIMIT 1");
        $cm_leader_id = data_get($cm_leader,'id') ? data_get($cm_leader,'id') : $cm_id;
        u::updateSimpleRow(array(
            'cm_id'=> $cm_id,
            'teacher_id'=>$teacher_id,
            'cm_leader_id' => $cm_leader_id,
            'updated_at' => date('Y-m-d H:i:s')
        ), array('student_id'=>$student_id), 'term_student_user');
        u::updateSimpleRow(array(
            'status'=> 2,
            'updated_at' => date('Y-m-d H:i:s')
        ), array('id'=>$class_transfer_id), 'class_transfer');
        u::updateSimpleRow(array(
            'cm_id'=>$cm_id,
            'cm_leader_id'=>$cm_leader_id,
            'program_id' => data_get($class_transfer_info, 'to_program_id'),
            'product_id' => data_get($class_transfer_info, 'to_product_id'),
            'class_id' => data_get($class_transfer_info, 'to_class_id'),
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => Auth::user()->id,
        ),array('id'=>$contract_id),'contracts');
        u::addLogContracts($contract_id);
        LogStudents::logAdd($student_id, "Chuyển từ lớp $class_transfer_info->from_class_name sang lớp $class_transfer_info->to_class_name", $class_transfer_info->creator_id);
        return true;
    }

    public function show(Request $request,$class_transfer_id)
    {
        $data = u::first("SELECT t.* , (SELECT name FROM branches WHERE id=t.from_branch_id) AS branch_name,
                (SELECT cls_name FROM classes WHERE id=t.to_class_id) AS to_class_name,
                (SELECT name FROM products WHERE id=t.to_product_id) AS to_product_name 
            FROM class_transfer AS t WHERE t.id=$class_transfer_id");
        $data->meta_data = $data->meta_data ? json_decode($data->meta_data) : '';
        return response()->json($data);
    }

    public function getLogsByStudent(Request $request, $student_id){
        $data = u::query("SELECT t.id,t.transfer_date, t.note, t.created_at,
                (SELECT cls_name FROM classes WHERE id=t.from_class_id) AS from_class_name,
                (SELECT cls_name FROM classes WHERE id=t.to_class_id) AS to_class_name,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id=t.creator_id) AS creator_name
            FROM class_transfer AS t 
            WHERE t.student_id = $student_id AND t.status>0 ORDER BY t.id DESC");
        return response()->json($data);
    }
}
