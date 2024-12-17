<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use App\Models\LogStudents;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CouponsController extends Controller
{
    public function list(Request $request)
    {
        $keyword = isset($request->keyword) ? $request->keyword : '';
        $status = isset($request->status) ? $request->status : [];

        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        $cond = " 1 ";
        
        if ($keyword !== '') {
            $cond .= " AND (b.code LIKE '%$keyword%')";
        }
        if (!empty($status)) {
            $cond .= " AND b.status IN (".implode(",",$status).")";
        }
        
        $order_by = " ORDER BY b.id DESC ";

        $total = u::first("SELECT count(b.id) AS total 
            FROM coupons AS b WHERE $cond");
        
        $list = u::query("SELECT b.*, IF(b.type=0, (SELECT title FROM campaigns WHERE id=b.campaign_id), '') AS title
            FROM coupons AS b 
            WHERE $cond $order_by $limitation");
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function update(Request $request)
    {
        $data = u::updateSimpleRow(array(
            'status' => data_get($request, 'status'),
            'updated_at'=>date('Y-m-d H:i:s'),
            'updator_id'=>Auth::user()->id,
        ),array('id'=> data_get($request, 'id')), 'coupons');
        return response()->json($data);
    }
}
