<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RolesController extends Controller
{
    public function list(Request $request)
    {
        $keyword = isset($request->keyword) ? $request->keyword : '';
        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit" : "";
        $cond = " 1=1 ";
        if ($keyword !== '') {
            $cond .= " AND (r.description LIKE '%$keyword%' OR r.name LIKE '%$keyword%')";
        }
        $total = u::first("SELECT count(r.id) AS total FROM roles AS r WHERE $cond ");
        $list = u::query("SELECT r.*
            FROM roles AS r 
            WHERE $cond ORDER BY r.id DESC $limitation");
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function create(Request $request)
    {
        if ($request->id) {
            u::updateSimpleRow(array(
                'name' => data_get($request, 'name'),
                'description' => data_get($request, 'description'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updator_id' => Auth::user()->id,
            ), array('id' => $request->id), 'roles');
            $result = [
                'status' => 1,
                'message' => 'Cập nhật thành công'
            ];
        } else {
            u::insertSimpleRow(array(
                'name' => data_get($request, 'name'),
                'description' => data_get($request, 'description'),
                'created_at' => date('Y-m-d H:i:s'),
                'creator_id' => Auth::user()->id,
            ), 'roles');
            $result = [
                'status' => 1,
                'message' => 'Thêm mới thành công'
            ];
        }
        return response()->json($result);
    }

    public function info(Request $request)
    {
        $role_info = u::first("SELECT * FROM roles WHERE id = $request->id");
        $groups = u::query("SELECT g.*, 0 AS active FROM permission_groups AS g WHERE g.status=1 ORDER BY g.display_order ");

        foreach ($groups as $k => $row) {
            $permissions = u::query("SELECT p.*, IF(r.role_id IS NOT NULL, 1, 0) AS active 
                FROM permissions AS p 
                LEFT JOIN permission_has_role AS r ON r.permission_id = p.id AND r.role_id = " .$role_info->id . " 
                WHERE p.status=1 AND p.group_id=$row->id  ORDER BY p.display_order");
            $groups[$k]->permissions = $permissions;
            $check_group_active = 1;
            foreach ($permissions as $p) {
                if ($p->active == 0) {
                    $check_group_active = 0;
                }
            }
            $groups[$k]->active = $check_group_active;
        }

        return response()->json(array(
            'role_info' => $role_info,
            'group_permissions' => $groups
        ));
    }

    public function permissions(Request $request)
    {
        u::query("DELETE FROM permission_has_role WHERE role_id=$request->role_id");
        $group_permissions = $request->group_permissions;
        foreach ($group_permissions as $k => $row) {
            $permissions = data_get($row, 'permissions');
            if (!empty($permissions)) {
                foreach ($permissions as $per) {
                    if (data_get($per, 'active')) {
                        u::insertSimpleRow(array(
                            'role_id' => $request->role_id,
                            'permission_id' => data_get($per, 'id')
                        ), 'permission_has_role');
                    }
                }
            }
        }
        return response()->json(array(
            'status' => 1,
            'message' => 'Cập nhật thành công'
        ));
    }
}
