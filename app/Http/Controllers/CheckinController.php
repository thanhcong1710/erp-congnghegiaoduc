<?php

namespace App\Http\Controllers;

use App\Enums\SystemCode;
use App\Providers\UtilityServiceProvider as u;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LogStudents;
class CheckinController extends Controller
{
    public function list(Request $request)
    {
        $checkin_branch_id = isset($request->checkin_branch_id) ? $request->checkin_branch_id : [];
        $status = isset($request->status) ? $request->status : [];
        $keyword = isset($request->keyword) ? $request->keyword : '';
        $checkin_owner_id = isset($request->checkin_owner_id) ? $request->checkin_owner_id :  [];
        $source_id = isset($request->source_id) ? $request->source_id : [];
        $end_date = isset($request->end_date) ? $request->end_date : '';
        $start_date = isset($request->start_date) ? $request->start_date : '';

        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        $cond = " s.status > 0 ";
        $cond .= " AND s.checkin_branch_id IN (" . Auth::user()->getBranchesHasUser().")";
        
        if (!empty($status)) {
            $cond .= " AND s.status IN (".implode(",",$status).")";
        }
        if (!empty($checkin_branch_id)) {
            $cond .= " AND s.checkin_branch_id IN (".implode(",",$checkin_branch_id).")";
        }
        if (!empty($checkin_owner_id)) {
            $cond .= " AND s.checkin_owner_id IN (".implode(",",$checkin_owner_id).")" ;
        }
        if (!empty($source_id)) {
            $cond .= " AND p.source_id IN (".implode(",",$source_id).")";
        }
        
        if ($keyword !== '') {
            $cond .= " AND (s.name LIKE '%$keyword%' OR p.name LIKE '%$keyword%' OR p.mobile_1 LIKE '%$keyword%' OR p.mobile_2 LIKE '%$keyword%') ";
        }
        if ($end_date !== '') {
            $cond .= " AND s.checkin_at < '$end_date 23:59:59'";
        }
        if ($start_date !== '') {
            $cond .= " AND s.checkin_at > '$start_date 00:00:00'";
        }
        
        $order_by = " ORDER BY s.id DESC ";

        $total = u::first("SELECT count(s.id) AS total FROM crm_students AS s  LEFT JOIN crm_parents AS p ON p.id =s.parent_id WHERE $cond");
        
        $list = u::query("SELECT s.id, s.name, s.gender, s.birthday, p.name AS parent_name, p.mobile_1, p.address, 
                (SELECT name FROM sources WHERE id = p.source_id) AS source_name,
                (SELECT name FROM branches WHERE id = s.checkin_branch_id) AS checkin_branch_name,
                (SELECT CONCAT(hrm_id, '-', name) FROM users WHERE id= s.checkin_owner_id) AS checkin_owner_name,
                s.checkin_at, s.status
            FROM crm_students AS s 
            LEFT JOIN crm_parents AS p ON p.id =s.parent_id
            WHERE $cond $order_by $limitation");
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function studentChecked(Request $request)
    {
        u::updateSimpleRow(array(
            'checkined_at' => date('Y-m-d H:i:s', strtotime($request->checkined_at)),
            'checkined_by' =>  Auth::user()->id,
            'checkined_note' => $request->note,
            'status' => 2
        ), array('id'=>$request->student_id), 'crm_students');

        $result =(object)array(
            'status'=>1,
            'message'=>'Xác nhận checkin thành công'
        );
        return response()->json($result);
    }

    public function studentUpgrade(Request $request){
        $crm_student_info = u::first("SELECT * FROM crm_students WHERE id = $request->crm_student_id");
        $crm_parent_info = u::first("SELECT * FROM crm_parents WHERE id = ".(int)data_get($crm_student_info, 'parent_id'));
        if($crm_student_info && $crm_parent_info){
            $arr_name = u::explodeName(data_get($crm_student_info, 'name'));
            $lms_student_id = u::insertSimpleRow(array(
                'lms_code' => '',
                'name' => data_get($crm_student_info, 'name'),
                'firstname' => data_get($arr_name, 'firstname'),
                'midname' => data_get($arr_name, 'midname'),
                'lastname' => data_get($arr_name, 'lastname'),
                'gender' => data_get($crm_student_info, 'gender'),
                'date_of_birth' => data_get($crm_student_info, 'birthday'),
                'gud_mobile1' => data_get($crm_parent_info, 'mobile_1'),
                'gud_name1' => data_get($crm_parent_info, 'name'),
                'gud_email1' => data_get($crm_parent_info, 'email'),
                'address' =>  data_get($crm_parent_info, 'address'),
                'province_id' => data_get($crm_parent_info, 'province_id'),
                'district_id' => data_get($crm_parent_info, 'district_id'),
                'created_at' => date('Y-m-d H:i:s'),
                'creator_id' => Auth::user()->id,
                'branch_id' => data_get($crm_student_info, 'checkin_branch_id'),
                'gud_birth_day1' => data_get($crm_parent_info, 'birthday'),
                'gud_gender1' => data_get($crm_parent_info, 'gender'),
                'gud_job1' => data_get($crm_parent_info, 'job_id'),
                'status' => 1,
                'source_detail_id' => data_get($crm_parent_info, 'source_detail_id'),
                'source_id' => data_get($crm_parent_info, 'source_id', ),
            ), 'students');

            $ceo_info = u::first("SELECT u.id FROM role_has_user AS ru 
                LEFT JOIN roles AS r ON r.id = ru.role_id
                LEFT JOIN users AS u ON u.id = ru.user_id
                WHERE u.status=1 AND r.code ='".SystemCode::ROLE_CEO_BRANCH."'");
            $ec_info = u::first("SELECT u.id, u.manager_id FROM users AS u WHERE u.status=1 AND u.id = ".(int)data_get($crm_student_info, 'checkin_owner_id'));
            u::insertSimpleRow(array(
                'student_id' => $lms_student_id,
                'ec_id' => data_get($ec_info, 'id'),
                'branch_id' => data_get($crm_student_info, 'checkin_branch_id'),
                'ceo_branch_id' => data_get($ceo_info, 'id'),
                'ec_leader_id' => data_get($ec_info, 'manager_id'),
                'created_at' => date('Y-m-d H:i:s'),
                'creator_id' => Auth::user()->id,
                'status' => 1
            ), 'term_student_user');

            u::updateSimpleRow(array('status'=>3), array('id'=> data_get($crm_student_info, 'id')), 'crm_students');

            $last_lms_code = str_pad((string)$lms_student_id, 6, '0', STR_PAD_LEFT);
            $lms_code = config('app.prefix_student_code').$last_lms_code;
            u::updateSimpleRow(array('lms_code'=>$lms_code), array('id'=>$lms_student_id), 'students');
            LogStudents::logAdd($lms_student_id, 'Chuyển lên danh sách học sinh chính thức', Auth::user()->id);
        }
        $result =(object)array(
            'status'=>1,
            'message'=>'Chuyển học sinh từ checkin lên danh sách chính thức thành công',
            'data' => [
                'lms_student_id' => $lms_student_id ?? 0
            ]
        );
        return response()->json($result);
    }
}
