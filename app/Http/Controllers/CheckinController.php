<?php

namespace App\Http\Controllers;

use App\Providers\UtilityServiceProvider as u;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function stuentChecked(Request $request)
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
}
