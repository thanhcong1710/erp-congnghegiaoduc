<?php

namespace App\Http\Controllers;

use App\Models\LogParents;
use App\Providers\UtilityServiceProvider as u;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ParentCareController extends Controller
{
    public function list(Request $request)
    {
        $keyword = isset($request->keyword) ? addslashes(trim($request->keyword)) : '';
        $creator_id = isset($request->creator_id) ? $request->creator_id : [];
        $call_status = isset($request->call_status) ? $request->call_status : [];
        $start_date = isset($request->start_date) ? addslashes(trim($request->start_date)) : '';
        $end_date = isset($request->end_date) ? addslashes(trim($request->end_date)) : '';

        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int)$pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int)$pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);
        $limitation = $limit > 0 ? " LIMIT $offset, $limit" : "";

        $cond = " 1 ";
        $need_parent_join = false;
        if (!Auth::user()->checkPermission('canViewAllParents')) {
            $staffIds = Auth::user()->getStaffHasUser();
            $cond .= " AND (c.creator_id IN ($staffIds) OR p.owner_id IN ($staffIds))";
            $need_parent_join = true;
        }

        if ($keyword !== '') {
            $cond .= " AND (p.name LIKE '%$keyword%' OR p.mobile_1 LIKE '%$keyword%' OR p.mobile_2 LIKE '%$keyword%' OR c.phone LIKE '%$keyword%')";
            $need_parent_join = true;
        }
        if (!empty($creator_id)) {
            $ids = is_array($creator_id)
                ? implode(",", array_map('intval', $creator_id))
                : (int)$creator_id;
            $cond .= " AND c.creator_id IN ($ids)";
        }
        if (!empty($call_status)) {
            $statuses = is_array($call_status)
                ? implode(",", array_map('intval', $call_status))
                : (int)$call_status;
            $cond .= " AND c.call_status IN ($statuses)";
        }
        if ($start_date !== '') {
            $cond .= " AND c.care_date >= '$start_date 00:00:00'";
        }
        if ($end_date !== '') {
            $cond .= " AND c.care_date <= '$end_date 23:59:59'";
        }

        $order_by = " ORDER BY c.care_date DESC ";
        $count_join = $need_parent_join ? " LEFT JOIN crm_parents AS p ON p.id = c.parent_id " : "";

        $total = u::first("SELECT count(c.id) AS total
            FROM crm_customer_care AS c
            $count_join
            WHERE $cond");

        $list = u::query("SELECT c.id, c.parent_id, c.care_date, c.next_care_date, c.note, c.attached_file,
                c.call_status, c.call_status_sub, c.method_id, c.creator_id,
                p.name AS parent_name,
                COALESCE(NULLIF(p.mobile_1, ''), c.phone) AS parent_phone,
                (SELECT name FROM users WHERE id = c.creator_id) AS creator_name,
                (SELECT name FROM crm_contact_methods WHERE id = c.method_id) AS method_name
            FROM crm_customer_care AS c
            LEFT JOIN crm_parents AS p ON p.id = c.parent_id
            WHERE $cond $order_by $limitation");

        foreach ($list as $k => $row) {
            $list[$k]->call_status_label = u::getTitleCallStatus($row->call_status, $row->call_status_sub);
        }

        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function getCareUsers(Request $request)
    {
        $cond = " u.status = 1 ";
        if (!Auth::user()->checkPermission('canViewAllParents')) {
            $cond .= " AND u.id IN (" . Auth::user()->getStaffHasUser() . ")";
        }
        $data = u::query("SELECT u.id, CONCAT(u.hrm_id, ' - ', u.name) AS label_name, u.id AS `value`
            FROM users AS u
            WHERE $cond
            ORDER BY u.id ASC");
        return response()->json($data);
    }

    public function getAllDataByParent(Request $request, $parent_id){
        $cond = "";
        $data = u::query("SELECT c.*, (SELECT CONCAT(name,' - ',hrm_id) FROM users WHERE id=c.creator_id) AS creator_name,
                (SELECT name FROM crm_contact_methods WHERE id=c.method_id) AS method_name ,
                (SELECT name FROM branches WHERE id=c.branch_id) AS branch_name
            FROM crm_customer_care AS c WHERE parent_id=$parent_id $cond ORDER BY c.care_date DESC");
        foreach($data AS $k=> $row){
            $data[$k]->call_status_label = u::getTitleCallStatus($row->call_status,$row->call_status_sub);
        }
        return response()->json($data);
    }

    public function add(Request $request)
    {
        $dataRequest = $request->all();
        $attachedFile = $dataRequest['attached_file'];
        
        if ($attachedFile) {
            // SAVE FILES TO SERVER
            $explod = explode(',', $attachedFile);
            $decod = base64_decode($explod[1]);
            if ( in_array(strtolower($explod[0]),['data:image/png;base64','data:image/jpg;base64','data:image/jpeg;base64'])) {
                $extend = 'jpg';
            } else {
                $data = (object)[
                    'status' => 0 ,
                    'message' => "File upload không hợp lệ. chỉ hỗ trợ định dạng JPG, PNG",
                ];
                return response()->json($data);
            }
            $fileAttached = 'customer_care_'.md5($request->attached_file.date('YmdHis')).'.'.$extend;
            $p = __DIR__.'/../../../public/static/upload/'.$fileAttached;
            file_put_contents($p, $decod);
        }
        $nextCareDate = data_get($dataRequest, 'next_care_date')
            ? date('Y-m-d H:i:s', strtotime(data_get($dataRequest, 'next_care_date')))
            : null;
        $callStatus = (int)data_get($dataRequest, 'method_id') === 1
            && data_get($dataRequest, 'call_status') !== ''
            && data_get($dataRequest, 'call_status') !== null
            ? (int)data_get($dataRequest, 'call_status')
            : null;

        $id = u::insertSimpleRow(array(
            'parent_id'=>$request->parent_id,
            'note'=>$request->note,
            'method_id' => $request->method_id,
            'care_date' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'creator_id' => Auth::user()->id,
            'branch_id' => Auth::user()->branch_id,
            'attached_file'=>$attachedFile ? '/static/upload/'.$fileAttached : '',
            'next_care_date' => $nextCareDate,
            'call_status' => $callStatus,
            'call_status_sub' => data_get($dataRequest, 'call_status_sub' ),
        ), 'crm_customer_care');

        $parentUpdate = array(
            'care_date' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => Auth::user()->id,
        );
        if ($nextCareDate) {
            $parentUpdate['next_care_date'] = $nextCareDate;
        }
        u::updateSimpleRow($parentUpdate, array('id' => $request->parent_id), 'crm_parents');
        if ($nextCareDate) {
            LogParents::logAdd($request->parent_id, 'Cập nhật lịch chăm sóc tiếp theo: ' . $nextCareDate, Auth::user()->id);
        }

        $data = (object)[
            'status' => 1 ,
            'message' => "Thêm mới chăm sóc thành công",
            'data'=>$id
        ];

        return response()->json($data);
    }
    
    public function getInfoCall($care_id){
        $data = u::first("SELECT c.id AS care_id,c.data_state,v.* 
            FROM crm_customer_care AS c 
                LEFT JOIN voip24h_data AS v ON v.id=c.data_id
            WHERE c.id=$care_id");
        return response()->json($data);
    }
    public function updateNoteCare(Request $request){
        u::updateSimpleRow(array('note'=>$request->note,'status'=>1),array('id'=>$request->care_id),'crm_customer_care');
        $data_info=u::first("SELECT parent_id FROM crm_customer_care WHERE id=".$request->care_id);
        Log::info("updateNoteCare $request->care_id",['parent_id'=>$data_info->parent_id]);
        if($data_info){
            ParentsController::processParentLockById($data_info->parent_id);
            Log::info("updateNoteCare processParentLockById $request->care_id",['parent_id'=>$data_info->parent_id]);
        }
        return "ok";
    }
    public function deleteFileAttached(){
        $list = u::query("SELECT id,attached_file FROM crm_customer_care WHERE attached_file IS NOT NULL AND created_at<'".date('Y-m-d 23:59:59',time()-24*3600*60)."'");
        foreach($list AS $row){
            $p = __DIR__.'/../../../public/'.$row->attached_file;
            if (file_exists($p)) {
                unlink($p);
            }
            u::query("UPDATE crm_customer_care SET attached_file=NULL WHERE id=".$row->id);
        }
        return "ok";
    }
}
