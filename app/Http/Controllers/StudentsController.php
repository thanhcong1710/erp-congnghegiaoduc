<?php

namespace App\Http\Controllers;

use App\Models\LogParents;
use App\Models\LogStudents;
use App\Providers\UtilityServiceProvider as u;
use App\Providers\CurlServiceProvider as curl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentsController extends Controller
{
    public function add(Request $request)
    {
        if($request->id>0){
            $pre_student_info = u::first("SELECT * FROM crm_students WHERE id= $request->id");
            $data_update = array(
                'parent_id'=>$request->parent_id,
                'name'=>$request->name,
                'gender' => $request->gender,
                'birthday' => $request->birthday,
                'school_level' => $request->school_level,
                'school' => $request->school,
                'note' => $request->note,
                'updated_at' => date('Y-m-d H:i:s'),
                'updator_id' => Auth::user()->id,
            );
            u::updateSimpleRow($data_update,array('id'=>$request->id), 'crm_students');
            LogParents::logUpdateStudentInfo($pre_student_info,$data_update,Auth::user()->id);
            $result =(object)array(
                'status'=>1,
                'message'=>'Cập nhật học sinh thành công'
            );
            return response()->json($result);
        }else{
            $data = u::insertSimpleRow(array(
                'parent_id'=>$request->parent_id,
                'name'=>$request->name,
                'gender' => $request->gender,
                'birthday' => $request->birthday,
                'school_level' => $request->school_level,
                'school' => $request->school,
                'note' => $request->note,
                'created_at' => date('Y-m-d H:i:s'),
                'creator_id' => Auth::user()->id,
            ), 'crm_students');
            $content = "Thêm mới học sinh: $request->name (ID: $data)";
            LogParents::logAdd($request->parent_id,$content,Auth::user()->id);
            $result =(object)array(
                'status'=>1,
                'message'=>'Thêm mới học sinh thành công'
            );
            return response()->json($result);
        }
    }
    public function getAllDataByParent($parent_id){
        $data = u::query("SELECT s.*, (SELECT name FROM users WHERE id=s.creator_id) AS creator_name,
                (SELECT name FROM branches WHERE id=s.checkin_branch_id) AS checkin_branch_name,
                (SELECT name FROM products WHERE id=s.type_product) AS checkin_product_name
            FROM crm_students AS s WHERE s.parent_id=$parent_id ORDER BY s.id DESC");
        return response()->json($data);
    }
    public function checkin(Request $request){
        $data_update = array(
            'checkin_at'=>$request->checkin_at,
            'checkin_owner_id' => $request->owner_id,
            'checkin_branch_id'=>$request->branch_id,
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => Auth::user()->id,
            'type_product'=>$request->type_product,
            'status' => 1, // 
        );
        u::updateSimpleRow($data_update,array('id'=>$request->student_id), 'crm_students');
        $result =(object)array(
            'status'=>1,
            'message'=>'Lưu thông tin checkin thành công'
        );
        return response()->json($result);
    }

    //LMS
    public function list(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $status = isset($request->status) ? $request->status : [];
        $keyword = isset($request->keyword) ? $request->keyword : '';

        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        $cond = " s.status > 0 ";
        $cond .= " AND s.branch_id IN (" . Auth::user()->getBranchesHasUser().")";
        
        if (!empty($status)) {
            $cond_status = "";
            foreach($status AS $s){
                if($s == 1){
                    $qu = " (c.type=0 AND c.status NOT IN(6,7)) ";
                }elseif($s == 2){
                    $qu = " (c.type=0 AND c.status =6) "; 
                }elseif($s == 3){
                    $qu = " (c.type=0 AND c.status =7) ";
                }elseif($s == 4){
                    $qu = " (c.type=1 AND c.status =1) ";
                }elseif($s == 5){
                    $qu = " (c.type=1 AND c.status =2) ";
                }elseif($s == 6){
                    $qu = " (c.type=1 AND c.status IN (3,4,5)) ";
                }elseif($s == 7){
                    $qu = " (c.type=1 AND c.status =6) ";
                }elseif($s == 8){
                    $qu = " (c.type=1 AND c.status =8) ";
                }
                $cond_status.= $cond_status ? " OR ".$qu : $qu; 
            }
            $cond .= " AND ($cond_status)";
        }
        if (!empty($branch_id)) {
            $cond .= " AND s.branch_id IN (".implode(",",$branch_id).")";
        }
        
        if ($keyword !== '') {
            $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%' OR s.gud_name1 LIKE '%$keyword%' OR s.gud_mobile1 LIKE '%$keyword%' OR s.gud_mobile2 LIKE '%$keyword%') ";
        }
        
        $order_by = " ORDER BY s.id DESC ";

        $total = u::first("SELECT count(s.id) AS total FROM students AS s 
            LEFT JOIN contracts AS c ON c.student_id=s.id AND c.count_recharge = (SELECT min(count_recharge) FROM contracts WHERE status !=7 AND student_id =s.id) WHERE $cond");
        
        $list = u::query("SELECT s.name, s.id, s.lms_code, s.gender, s.date_of_birth, s.gud_name1, s.gud_mobile1, 
                (SELECT name FROM sources WHERE id =s.source_id) AS source_name,
                (SELECT cls_name FROM classes WHERE id = c.class_id) AS class_name,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id =c.ec_id) AS ec_name,
                (SELECT name FROM branches WHERE id =s.branch_id) AS branch_name, c.done_sessions, c.summary_sessions, c.type, c.status, '' AS label_status
            FROM students AS s
            LEFT JOIN contracts AS c ON c.student_id=s.id
            WHERE (c.count_recharge = 
                    IF((SELECT count(id) FROM contracts WHERe student_id=s.id AND status!=7)>0,
                        (SELECT min(count_recharge) FROM contracts WHERE status !=7 AND student_id =s.id),
                        (SELECT max(count_recharge) FROM contracts WHERE student_id =s.id)) 
                OR c.id IS NULL) 
                AND $cond $order_by $limitation");
        foreach($list AS $k=> $row){
            $list[$k]->label_status = u::genStatusStudent($row->status, $row->type);
        }
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function show(Request $request,$student_id)
    {
        $data = u::first("SELECT s.*, c.total_charged, c.type AS contract_type, c.status AS contract_status,
                c.summary_sessions,c.done_sessions, c.left_sessions, 
                (SELECT name FROM branches WHERE id=t.branch_id) AS branch_name,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id =t.ec_id) AS ec_name,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id =t.cm_id) AS cm_name, '' AS satus_label
            FROM students AS s 
                LEFT JOIN contracts AS c ON c.student_id=s.id 
                LEFT JOIN term_student_user AS t ON t.student_id=s.id
            WHERE (c.count_recharge = 
                    IF((SELECT count(id) FROM contracts WHERe student_id=s.id AND status!=7)>0,
                        (SELECT min(count_recharge) FROM contracts WHERE status !=7 AND student_id =s.id),
                        (SELECT max(count_recharge) FROM contracts WHERE student_id =s.id)) 
                OR c.id IS NULL) AND s.id=$student_id");
        $data->status_label = u::genStatusStudent($data->contract_status, $data->contract_type);
        return response()->json($data);
    }

    public function update(Request $request)
    {
        $pre_student_info = u::first("SELECT * FROM students WHERE id = $request->id");
        $arr_name = u::explodeName(data_get($request, 'name'));
        $data_update = array(
            'name'=>$request->name,
            'firstname' => data_get($arr_name, 'firstname'),
            'midname' => data_get($arr_name, 'midname'),
            'lastname' => data_get($arr_name, 'lastname'),
            'date_of_birth'=>$request->date_of_birth,
            'gender' => $request->gender,
            'province_id' => $request->province_id,
            'district_id' => $request->district_id,
            'address' => $request->address,
            'school' => $request->school,
            'gud_name1' => $request->gud_name1,
            'gud_email1' => $request->gud_email1,
            'gud_birth_day1' => $request->gud_birth_day1,
            'gud_job1' => $request->gud_job1,
            'gud_name2' => $request->gud_name2,
            'gud_email2' => $request->gud_email2,
            'gud_mobile2' => $request->gud_mobile2,
            'gud_birth_day2' => $request->gud_birth_day2,
            'gud_job2' => $request->gud_job2,
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => Auth::user()->id,
        );
        $data = u::updateSimpleRow($data_update, array('id' => $request->id), 'students');
        LogStudents::logUpdateInfo($pre_student_info,$data_update,Auth::user()->id);
        $result =(object)array(
            'status'=>1,
            'message'=>'Cập nhật học sinh thành công'
        );
        return response()->json($result);
    }

    public function searchContract(Request $request){
        $keyword = $request->keyword;
        $branch_id = $request->branch_id;
        $data = u::query("SELECT s.name, s.lms_code, s.gud_name1, s.gud_mobile1, s.gud_email1, s.address,
                (SELECT name FROM branches WHERE id =t.branch_id) AS student_branch_name,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id =t.ec_id) AS ec_name,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id =t.ec_leader_id) AS ec_leader_name,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id =t.ceo_branch_id) AS ceo_branch_name, 
                s.id AS student_id, CONCAT(s.name, ' - ', s.lms_code) AS label
            FROM students AS s LEFT JOIN term_student_user AS t ON t.student_id=s.id AND t.status=1 
                WHERE t.branch_id= $branch_id AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%')");
        return response()->json($data);
    } 

    public function logs(Request $request)
    {
        $student_id = isset($request->student_id) ? $request->student_id : 0;
        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        $cond = " l.status = 1 AND l.student_id = $student_id";
        
        $order_by = " ORDER BY l.id DESC ";

        $total = u::first("SELECT count(l.id) AS total FROM student_logs AS l WHERE $cond");
        
        $list = u::query("SELECT l.created_at, l.content, CONCAT(u.name, ' - ', u.hrm_id) AS creator_name 
            FROM student_logs AS l
            LEFT  JOIN users AS u ON u.id=l.creator_id
            WHERE $cond $order_by $limitation");
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function contracts(Request $request)
    {
        $student_id = isset($request->student_id) ? $request->student_id : 0;
        $list = u::query("SELECT c.created_at, c.code, c.total_sessions, c.bonus_sessions, c.debt_amount, 
                c.must_charge, c.tuition_fee_amount, '' AS label_status, c.status, c.type,
                (SELECT name FROM products WHERE id=c.product_id) AS product_name,
                (SELECT name FROM tuition_fee WHERE id=c.tuition_fee_id) AS tuition_fee_name,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id =c.creator_id) AS creator_name,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id =c.ec_id) AS ec_name,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id =c.cm_id) AS cm_name,
                (SELECT name FROM branches WHERE id =c.branch_id) AS branch_name
            FROM contracts AS c
            WHERE c.status>0 AND c.student_id= $student_id ORDER BY c.count_recharge DESC");
        foreach($list AS $k=> $row){
            $list[$k]->label_status = u::genStatusStudent($row->status, $row->type);
        }
        return response()->json($list);
    }

    public function sessions(Request $request)
    {
        $student_id = isset($request->student_id) ? $request->student_id : 0;
        $contract_active = u::first("SELECT c.id, c.class_id, c.status, c.done_sessions, c.summary_sessions
            FROM contracts AS c 
            WHERE c.count_recharge = 
                IF((SELECT count(id) FROM contracts WHERe student_id=$student_id AND status!=7)>0,
                    (SELECT min(count_recharge) FROM contracts WHERE status !=7 AND student_id =$student_id),
                    (SELECT max(count_recharge) FROM contracts WHERE student_id =$student_id))  AND c.student_id=$student_id");
        if($contract_active){
            $done_sessions = u::query("SELECT s.class_date, sj.code, s.subject_stt, s.attendance_status FROM schedule_has_student AS s LEFT JOIN subjects AS sj ON s.subject_id = sj.id 
                WHERE s.contract_id = $contract_active->id");
            $limit = $contract_active->summary_sessions - count($done_sessions);
            $limit = $limit > 0 ? $limit : 0;
            if($contract_active->class_id){
                $next_sessions = u::query("SELECT s.class_date, sj.code, s.subject_stt FROM schedules AS s LEFT JOIN subjects AS sj ON s.subject_id = sj.id
                    WHERE s.class_id = $contract_active->class_id AND s.class_date > CURRENT_DATE ORDER BY s.class_date LIMIT $limit");
            }
        }
        $result = [
            'next_sessions' => $next_sessions ?? [],
            'done_sessions' => $done_sessions ?? [],
            'contract_info' => $contract_active
        ];
        return response()->json($result);
    }

    public function assessments(Request $request)
    {
        $student_id = isset($request->student_id) ? $request->student_id : 0;
        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        $cond = " a.status = 1 AND a.student_id = $student_id";
        
        $order_by = " ORDER BY a.id DESC ";

        $total = u::first("SELECT count(a.id) AS total FROM assessments AS a WHERE $cond");
        
        $list = u::query("SELECT a.created_at, a.description, a.title, CONCAT(u.name, ' - ', u.hrm_id) AS creator_name 
            FROM assessments AS a
            LEFT  JOIN users AS u ON u.id=a.creator_id
            WHERE $cond $order_by $limitation");
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function operating(Request $request)
    {
        $student_id = isset($request->student_id) ? $request->student_id : 0;
        $class_transfer = u::query("SELECT s.lms_code , s.name, c.transfer_date ,
                (SELECT cls_name FROM classes WHERE id=c.from_class_id) AS from_class_name,
                (SELECT cls_name FROM classes WHERE id=c.to_class_id) AS to_class_name,
                (SELECT name FROM branches WHERE id=c.from_branch_id) AS from_branch_name, c.status
            FROM class_transfer AS c LEFT JOIN students AS s ON s.id=c.student_id
            WHERE c.status>0 AND c.student_id= $student_id ORDER BY c.id DESC");
        $branch_transfer = u::query("SELECT s.lms_code , s.name, b.transfer_date ,b.status,
                (SELECT name FROM branches WHERE id=b.from_branch_id) AS from_branch_name,
                (SELECT name FROM branches WHERE id=b.to_branch_id) AS to_branch_name
            FROM branch_transfer AS b LEFT JOIN students AS s ON s.id=b.student_id
            WHERE b.status>0 AND b.student_id= $student_id ORDER BY b.id DESC");
        $tuition_transfer = u::query("SELECT t.transferred_amount ,t.transfer_date,t.status,
            (SELECT name FROM students WHERE id=t.from_student_id) AS from_student_name,
            (SELECT lms_code FROM students WHERE id=t.from_student_id) AS from_student_lms_code,
            (SELECT name FROM students WHERE id=t.to_student_id) AS to_student_name,
            (SELECT lms_code FROM students WHERE id=t.to_student_id) AS to_student_lms_code
        FROM tuition_transfer AS t 
        WHERE t.status>0 AND (t.from_student_id= $student_id OR t.to_student_id= $student_id) ORDER BY t.id DESC");

        return response()->json([
            'class_transfer' => $class_transfer,
            'branch_transfer' => $branch_transfer,
            'tuition_transfer' => $tuition_transfer
        ]);
    }
}
