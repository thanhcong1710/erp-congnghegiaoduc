<?php

namespace App\Http\Controllers;

use App\Providers\UtilityServiceProvider as u;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as x;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LogParents;

class ParentsController extends Controller
{
    public function list(Request $request)
    {
        $status = isset($request->status) ? $request->status : [];
        $keyword = isset($request->keyword) ? $request->keyword : '';
        $owner_id = isset($request->owner_id) ? $request->owner_id :  [];
        $source_id = isset($request->source_id) ? $request->source_id : [];
        $source_detail_id = isset($request->source_detail_id) ? $request->source_detail_id : [];
        $end_date = isset($request->end_date) ? $request->end_date : '';
        $start_date = isset($request->start_date) ? $request->start_date : '';
        $type_seach = isset($request->type_seach) ? $request->type_seach : 0;

        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        $cond = " 1 ";

        if(!Auth::user()->checkPermission('canViewAllParents')){
            $cond .= " AND p.owner_id IN (".Auth::user()->getStaffHasUser().")";
        }
        
        if (!empty($status)) {
            $cond .= " AND p.status IN (".implode(",",$status).")";
        }
        if (!empty($owner_id)) {
            $cond .= " AND p.owner_id IN (".implode(",",$owner_id).")" ;
        }
        if (!empty($source_id)) {
            $cond .= " AND p.source_id IN (".implode(",",$source_id).")";
        }
        if (!empty($source_detail_id)) {
            $cond .= " AND p.source_detail_id IN (".implode(",",$source_detail_id).")";
        }
        
        if ($keyword !== '') {
            $cond .= " AND (p.name LIKE '%$keyword%' OR p.mobile_1 LIKE '%$keyword%' OR p.mobile_2 LIKE '%$keyword%') ";
        }
        if ($end_date !== '') {
            $cond .= " AND p.next_care_date < '$end_date 23:59:59' AND p.next_care_date IS NOT NULL";
        }
        if ($start_date !== '') {
            $cond .= " AND p.next_care_date > '$start_date 00:00:00'";
        }
        //type_search=1
        $cond_1 = " AND p.care_date IS NULL AND p.status <80 ";
        //type_search=2
        $cond_2 = " AND DATE_FORMAT(next_care_date,'%Y-%m-%d') = '".date('Y-m-d')."'";
        $cond_3 = " AND next_care_date < '".date('Y-m-d')."' 
            AND (p.care_date < p.next_care_date OR p.care_date IS NULL) AND p.status NOT IN (8,9,10,12)";
        $order_by = " ORDER BY p.id DESC ";
        $tmp_cond="";
        if($type_seach==1){
            $tmp_cond = $cond_1;
        }elseif($type_seach==2){
            $tmp_cond = $cond_2;
            $order_by = " ORDER BY next_care_date ASC ";
        }elseif($type_seach==3){
            $tmp_cond = $cond_3;
            $order_by = " ORDER BY next_care_date ASC ";
        }

        $total = u::first("SELECT count(id) AS total FROM crm_parents AS p WHERE $cond $tmp_cond");
        
        $list = u::query("SELECT p.name,p.id,p.mobile_1,p.status,p.next_care_date, (SELECT name FROM sources WHERE id=p.source_id) AS source_name,
                (SELECT name FROM source_detail WHERE id=p.source_detail_id) AS source_detail_name,
                (SELECT note FROM crm_customer_care WHERE parent_id=p.id AND status=1 ORDER BY care_date DESC LIMIT 1) AS last_care,
                p.care_date AS last_time_care,
                (SELECT name FROM users WHERE id=p.owner_id) AS owner_name ,
                (SELECT name FROM crm_students WHERE parent_id=p.id LIMIT 0,1) AS hs1_name,
                (SELECT name FROM crm_students WHERE parent_id=p.id LIMIT 1,1) AS hs2_name
            FROM crm_parents AS p WHERE $cond $tmp_cond $order_by $limitation");
        $data = u::makingPagination($list, $total->total, $page, $limit);

        $total_0 = u::first("SELECT count(id) AS total FROM crm_parents AS p WHERE $cond ");
        $total_1 = u::first("SELECT count(id) AS total FROM crm_parents AS p WHERE $cond $cond_1 ");
        $total_2 = u::first("SELECT count(id) AS total FROM crm_parents AS p WHERE $cond $cond_2 ");
        $total_3 = u::first("SELECT count(id) AS total FROM crm_parents AS p WHERE $cond $cond_3 ");
        $data->detail_total = (object)array(
            'total_0' => $total_0->total,
            'total_1' => $total_1->total,
            'total_2' => $total_2->total,
            'total_3' => $total_3->total,
        );
        return response()->json($data);
    }

    public function validatePhone(Request $request){
        $parent_id = isset($request->parent_id) ? $request->parent_id : '';
        $phone = isset($request->phone) ? $request->phone : '';
        $result =(object)array(
            'status'=>1,
            'message'=>'',
            'dup_parent_id'=>'',
        );
        if($parent_id){
            $duplicate_info = u::first("SELECT p.is_lock,u.name,u.hrm_id, u.branch_name,p.id AS parent_id,
                    (SELECT care_date FROM crm_customer_care WHERE parent_id=p.id AND status=1 ORDER BY care_date DESC LIMIT 1) AS care_date,
                    (SELECT count(id) FROM crm_customer_care WHERE parent_id=p.id AND status=1 ) AS total_care, p.last_assign_date
                FROM crm_parents AS p LEFT JOIN users AS u ON u.id=p.owner_id  
                WHERE (p.mobile_1='$phone' OR p.mobile_2='$phone') AND p.id!='$parent_id'");
            if($duplicate_info){
                $result->status = 0;
                $result->message = "Khách hàng có SĐT: $phone đang thuộc quyền quản lý của nhân viên $duplicate_info->name - $duplicate_info->hrm_id $duplicate_info->branch_name";
                $result->dup_parent_id = $duplicate_info->parent_id;
            }
        }else{
            $duplicate_info = u::first("SELECT p.is_lock,u.name,u.hrm_id, u.branch_name,p.status,p.id AS parent_id,p.owner_id,
                    (SELECT care_date FROM crm_customer_care WHERE parent_id=p.id AND status=1 AND creator_id=p.owner_id ORDER BY care_date DESC LIMIT 1) AS care_date,
                    (SELECT count(id) FROM crm_customer_care WHERE parent_id=p.id AND status=1  AND creator_id=p.owner_id) AS total_care, p.last_assign_date
                FROM crm_parents AS p LEFT JOIN users AS u ON u.id=p.owner_id  WHERE (p.mobile_1='$phone' OR p.mobile_2='$phone') ");
            if($duplicate_info){
                if($duplicate_info->is_lock==0){
                    $result->status = 2;
                    $result->message = "Khách hàng có SĐT: $phone đã tồn tại trên hệ thống, bạn có muốn chăm sóc?";
                    $result->dup_parent_id = $duplicate_info->parent_id;
                }else{
                    $result->status = 0;
                    $result->dup_parent_id = $duplicate_info->parent_id;
                    $text = "";
                    if($duplicate_info->total_care>0 && (61 - floor((time() - strtotime($duplicate_info->care_date))/(3600*24)))>0){
                        $thoi_gian_con = 61 - floor((time() - strtotime($duplicate_info->care_date))/(3600*24));
                        $text.="<br> Thời gian chăm sóc gần nhất: $duplicate_info->care_date <br> Thời gian còn lại sẽ được ghi đè sau $thoi_gian_con ngày";
                    }else{
                        $thoi_gian_con = 16 - floor((time() - strtotime($duplicate_info->last_assign_date))/(3600*24));
                        $text.="<br> Thời gian còn lại sẽ được ghi đè sau $thoi_gian_con ngày";
                    }
                    if($duplicate_info->status > 80 || $duplicate_info->status==73){
                        $text="<br> Khách hàng thuộc các trường hợp không được phép ghi đè - ".u::getStatusParent($duplicate_info->status);
                    }
                    $result->message = "Khách hàng có SĐT: $phone đang thuộc quyền quản lý của nhân viên $duplicate_info->name - $duplicate_info->hrm_id $duplicate_info->branch_name .".$text;
                }
            }
        }
        return response()->json($result);
    }

    public function overwrite(Request $request){
        $phone = isset($request->phone) ? $request->phone : '';
        $parent_info = u::first("SELECT * FROM crm_parents WHERE mobile_1='$phone'");
        if($parent_info){
            u::updateSimpleRow(array(
                'updated_at' => date('Y-m-d H:i:s'),
                'updator_id' => Auth::user()->id,
                'owner_id'=>Auth::user()->id,
                'last_assign_date'=> date('Y-m-d H:i:s'),
                'is_lock'=>1,
            ), array('id' => $parent_info->id), 'crm_parents');
            u::insertSimpleRow(array(
                'parent_id'=>$parent_info->id,
                'last_owner_id'=>$parent_info->owner_id,
                'owner_id'=>Auth::user()->id,
                'created_at'=>date('Y-m-d H:i:s'),
                'creator_id'=>Auth::user()->id,
            ), 'crm_parent_overwrite');
            LogParents::logAssign($parent_info->id,$parent_info->owner_id,Auth::user()->id,Auth::user()->id,true);
        }
        $result =(object)array(
            'status'=>1,
            'message'=>'Ghi đè quyền chăm sóc thành công'
        );
        return response()->json($result);
    }

    public function validateC2CPhone(Request $request){
        $phone = isset($request->phone) ? $request->phone : '';
        
        $parent_info=u::first("SELECT name,mobile_1,mobile_2 FROM crm_parents WHERE mobile_1 = '$phone' OR mobile_2 ='$phone'");
        if($parent_info){
            $result =(object)array(
                'status'=>1,
                'message'=> "Khách hàng: ".$parent_info->name." - ".$parent_info->mobile_1.($parent_info->mobile_2?" - ".$parent_info->mobile_2:'')
            );
        }else{
            $result =(object)array(
                'status'=>0,
                'message'=>'Không tồn tại số điện thoại của khách hàng giới thiệu trên hệ thống'
            );
        }
        return response()->json($result);
    }

    public function add(Request $request)
    {
        $id = u::insertSimpleRow(array(
            'name'=>$request->name,
            'email'=>$request->email,
            'mobile_1' => $request->mobile_1,
            'mobile_2' => $request->mobile_2,
            'address' => $request->address,
            'province_id' => $request->province_id,
            'district_id' => $request->district_id,
            'gender' => $request->gender,
            'birthday' => $request->birthday,
            'job_id' => $request->job_id,
            'source_id' => $request->source_id,
            'source_detail_id' => $request->source_detail_id,
            'note' => $request->note,
            'created_at' => date('Y-m-d H:i:s'),
            'creator_id' => Auth::user()->id,
            'last_assign_date' => date('Y-m-d H:i:s'),
            'owner_id'=>$request->owner_id,
            'status'=>$request->status,
            'c2c_mobile'=>$request->c2c_mobile,
        ), 'crm_parents');
        LogParents::logAdd($id,'Khởi tạo khách hàng thủ công',Auth::user()->id);
        $result =(object)array(
            'status'=>1,
            'message'=>'Thêm mới khách hàng thành công'
        );
        return response()->json($result);
    }

    public function show(Request $request,$parent_id)
    {
        $cond="";
        if(!Auth::user()->checkPermission('canViewAllParents')){
            $cond .= " AND p.owner_id IN (".Auth::user()->getStaffHasUser().")";
        }
        $data = u::first("SELECT p.*,(SELECT name FROM users WHERE id=p.creator_id) AS creator_name,
                (SELECT name FROM districts WHERE id=p.district_id) AS district_name,
                (SELECT name FROM provinces WHERE id=p.province_id) AS province_name,
                (SELECT name FROM sources WHERE id=p.source_id) AS source_name,
                (SELECT name FROM source_detail WHERE id=p.source_detail_id) AS source_detail_name,
                (SELECT title FROM jobs WHERE id=p.job_id) AS job_name,
                (SELECT count(id) FROM crm_customer_care WHERE parent_id=p.id AND status=1) AS num_care,
                (SELECT care_date FROM crm_customer_care WHERE parent_id=p.id  AND status=1 ORDER BY care_date DESC LIMIT 1) AS last_care
            FROM crm_parents AS p WHERE id=$parent_id  $cond");
        return response()->json($data);
    }

    public function update(Request $request)
    {
        $pre_parent_info = u::first("SELECT * FROM crm_parents WHERE id = $request->id");
        $data_update = array(
            'name'=>$request->name,
            'email'=>$request->email,
            'mobile_1' => $request->mobile_1,
            'mobile_2' => $request->mobile_2,
            'address' => $request->address,
            'province_id' => $request->province_id,
            'district_id' => $request->district_id,
            'gender' => $request->gender,
            'birthday' => $request->birthday,
            'job_id' => $request->job_id,
            'source_id' => $request->source_id,
            'source_detail_id' => $request->source_detail_id,
            'note' => $request->note,
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => Auth::user()->id,
            'c2c_mobile'=>$request->c2c_mobile,
        );
        $data = u::updateSimpleRow($data_update, array('id' => $request->id), 'crm_parents');
        LogParents::logUpdateInfo($pre_parent_info,$data_update,Auth::user()->id);
        $result =(object)array(
            'status'=>1,
            'message'=>'Cập nhật khách hàng thành công'
        );
        return response()->json($result);
    }

    public function changeStatus(Request $request)
    {
        $pre_parent_info = u::first("SELECT status FROM crm_parents WHERE id=$request->parent_id");
        $data = u::updateSimpleRow(array(
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => Auth::user()->id,
            'status'=>$request->status,
        ), array('id' => $request->parent_id), 'crm_parents');
        LogParents::logStatus($request->parent_id,$pre_parent_info->status,$request->status,Auth::user()->id);
        $result =(object)array(
            'status'=>1,
            'message'=>'Cập nhật trạng thái khách hàng thành công'
        );
        return response()->json($result);
    }

    public function assign(Request $request)
    {
        $pre_parent_info = u::first("SELECT owner_id,last_assign_date FROM crm_parents WHERE id=$request->parent_id");
        $data = u::updateSimpleRow(array(
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => Auth::user()->id,
            'owner_id'=>$request->owner_id,
            'last_assign_date'=>$request->owner_id != $pre_parent_info->owner_id ? date('Y-m-d H:i:s') : $pre_parent_info->last_assign_date,
        ), array('id' => $request->parent_id), 'crm_parents');
        LogParents::logAssign($request->parent_id,$pre_parent_info->owner_id,$request->owner_id,Auth::user()->id);
        $result =(object)array(
            'status'=>1,
            'message'=>'Cập nhật người phụ trách thành công'
        );
        return response()->json($result);
    }

    public function assignList(Request $request)
    {
        $cond = implode(",",$request->parents);
        $arr_owner = $request->owners;
        $list_parent_info = u::query("SELECT p.id AS parent_id,p.owner_id,(SELECT CONCAT(name,' (',hrm_id,')') FROM users WHERE id= p.owner_id) AS pre_owner,p.last_assign_date FROM crm_parents AS p WHERE p.id IN ($cond)");
        foreach($list_parent_info AS $k=>$row){
            $owner_id =  $arr_owner[$k%count($arr_owner)];
            $last_assign_date = $owner_id != $row->owner_id ? date('Y-m-d H:i:s') : $row->last_assign_date;
            u::query("UPDATE crm_parents SET owner_id= $owner_id,last_assign_date='$last_assign_date' WHERE id =$row->parent_id");
            LogParents::logAssign($row->parent_id,$row->owner_id,$owner_id,Auth::user()->id);
        }
        $result =(object)array(
            'status'=>1,
            'message'=>'Bàn giao thành công'
        );
        return response()->json($result);
    }

    public function updateNextCareDate(Request $request){
        $data=u::updateSimpleRow(array(
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => $request->user()->id,
            'next_care_date'=>date('Y-m-d H:i:s',strtotime($request->next_care_date)),
        ), array('id' => $request->parent_id), 'crm_parents');
        LogParents::logAdd($request->parent_id,'Cập nhật lịch chăm sóc tiếp theo: '.date('Y-m-d H:i:s',strtotime($request->next_care_date)),$request->user()->id);
        $result =(object)array(
            'status'=>1,
            'message'=>'Cập nhật lịch chăm sóc thành công'
        );
        return response()->json($result);
    }

    public function getLogs(Request $request, $parent_id){
        $data = u::query("SELECT l.*,(SELECT name FROM users WHERE id=l.creator_id) AS creator_name
            FROM crm_parent_logs AS l WHERE l.parent_id=$parent_id AND l.status=1 
            ORDER BY l.id DESC");
        return response()->json($data);
    }

    public function sendSms(Request $request){
        $parent_info = u::first("SELECT id,mobile_1,mobile_2 FROM crm_parents WHERE id='$request->parent_id'");
        $phone = $request->phone ? $request->phone :$parent_info->mobile_1;
        if($parent_info){
            u::insertSimpleRow( array(
                'parent_id'=>$parent_info->id,
                'note'=>$request->content,
                'created_at'=>date('Y-m-d H:i:s'),
                'creator_id'=>$request->user()->id,
                'method_id'=>4,
                'care_date'=>date('Y-m-d H:i:s'),
                'phone'=>$phone, 
                'branch_id' => Auth::user()->branch_id,
            ),'crm_customer_care');
        }
        $result =(object)array(
            'status'=>1,
            'message'=>'Gửi tin nhắn sms thành công'
        );
        return response()->json($result);
    }
}
