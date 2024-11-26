<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use App\Models\LogStudents;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractsController extends Controller
{

    public function list(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $keyword = isset($request->keyword) ? $request->keyword : '';
        $end_date = isset($request->end_date) ? $request->end_date : '';
        $start_date = isset($request->start_date) ? $request->start_date : '';

        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        $cond = " c.status > 0 ";
        $cond .= " AND c.branch_id IN (" . Auth::user()->getBranchesHasUser().")";

        if (!empty($branch_id)) {
            $cond .= " AND c.branch_id IN (".implode(",",$branch_id).")";
        }
        
        if ($keyword !== '') {
            $cond .= " AND (s.lms_code LIKE '%$keyword%' OR s.name LIKE '%$keyword%' OR c.code LIKE '%$keyword%') ";
        }

        if ($end_date !== '') {
            $cond .= " AND c.created_at < '$end_date 23:59:59'";
        }
        if ($start_date !== '') {
            $cond .= " AND c.created_at > '$start_date 00:00:00'";
        }
        
        $order_by = " ORDER BY c.id DESC ";

        $total = u::first("SELECT count(s.id) AS total 
            FROM contracts AS c LEFT JOIN students AS s ON s.id=c.student_id WHERE $cond");
        
        $list = u::query("SELECT c.id AS contract_id, s.name, s.lms_code, 
                (SELECT name FROM branches WHERE id =c.branch_id) AS branch_name,
                (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.ec_id) AS ec_name,
                (SELECT CONCAT(name,'-',hrm_id) FROM users WHERE id= c.cm_id) AS cm_name,
                (SELECT name FROM products WHERE id =c.product_id) AS product_name,
                c.code, (SELECT name FROM tuition_fee WHERE id=c.tuition_fee_id) AS tuition_fee_name,
                c.total_sessions,c.bonus_sessions, c.real_sessions, c.tuition_fee_amount, c.must_charge, c.debt_amount, c.total_charged, c.status, c.type
            FROM contracts AS c 
                LEFT JOIN students AS s ON s.id=c.student_id
            WHERE $cond $order_by $limitation");
        foreach($list AS $k=> $row){
            $list[$k]->label_status = u::genStatusStudent($row->status, $row->type);
        }
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }
}
