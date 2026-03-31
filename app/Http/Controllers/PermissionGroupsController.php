<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionGroupsController extends Controller
{
    /**
     * Lấy danh sách permission groups
     */
    public function list(Request $request)
    {
        $keyword = isset($request->keyword) ? $request->keyword : '';

        $pagination = (object) $request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);
        $limitation = $limit > 0 ? " LIMIT $offset, $limit" : "";

        $cond = " status = 1";

        if ($keyword !== '') {
            $cond .= " AND (name LIKE '%$keyword%' OR description LIKE '%$keyword%')";
        }

        $order_by = " ORDER BY display_order, id";

        $total = u::first("SELECT COUNT(id) AS total FROM permission_groups WHERE $cond");
        $list = u::query("SELECT pg.*,
                (SELECT COUNT(id) FROM permissions WHERE group_id = pg.id AND status = 1) AS permission_count
            FROM permission_groups AS pg 
            WHERE $cond $order_by $limitation");

        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    /**
     * Lấy thông tin chi tiết permission group
     */
    public function show(Request $request, $id)
    {
        $group = u::first("SELECT * FROM permission_groups WHERE id = $id");

        if (!$group) {
            return response()->json(['message' => 'Permission group không tồn tại'], 404);
        }

        return response()->json([
            'group' => $group
        ]);
    }

    /**
     * Thêm mới permission group
     */
    public function add(Request $request)
    {
        $name = trim(data_get($request, 'name'));
        $description = trim(data_get($request, 'description'));
        $display_order = (int) data_get($request, 'display_order', 0);

        // Validate
        if (empty($name)) {
            return response()->json(['message' => 'Tên nhóm không được để trống'], 400);
        }

        if (empty($description)) {
            return response()->json(['message' => 'Mô tả không được để trống'], 400);
        }

        // Kiểm tra trùng name
        $exists = u::first("SELECT id FROM permission_groups WHERE name = '$name' AND status = 1");
        if ($exists) {
            return response()->json(['message' => 'Tên nhóm đã tồn tại'], 400);
        }

        // Thêm mới
        $group_id = u::insertSimpleRow([
            'name' => $name,
            'description' => $description,
            'display_order' => $display_order,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'creator_id' => Auth::user()->id,
        ], 'permission_groups');

        return response()->json([
            'status' => 1,
            'message' => 'Thêm nhóm permission thành công',
            'group_id' => $group_id
        ]);
    }

    /**
     * Cập nhật permission group
     */
    public function update(Request $request)
    {
        $id = (int) data_get($request, 'id');
        $name = trim(data_get($request, 'name'));
        $description = trim(data_get($request, 'description'));
        $display_order = (int) data_get($request, 'display_order', 0);

        // Validate
        if ($id <= 0) {
            return response()->json(['message' => 'ID không hợp lệ'], 400);
        }

        if (empty($name)) {
            return response()->json(['message' => 'Tên nhóm không được để trống'], 400);
        }

        if (empty($description)) {
            return response()->json(['message' => 'Mô tả không được để trống'], 400);
        }

        // Kiểm tra group tồn tại
        $group = u::first("SELECT * FROM permission_groups WHERE id = $id");
        if (!$group) {
            return response()->json(['message' => 'Permission group không tồn tại'], 404);
        }

        // Kiểm tra trùng name (trừ chính nó)
        $exists = u::first("SELECT id FROM permission_groups WHERE name = '$name' AND id != $id AND status = 1");
        if ($exists) {
            return response()->json(['message' => 'Tên nhóm đã tồn tại'], 400);
        }

        // Cập nhật
        u::updateSimpleRow([
            'name' => $name,
            'description' => $description,
            'display_order' => $display_order,
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => Auth::user()->id,
        ], ['id' => $id], 'permission_groups');

        return response()->json([
            'status' => 1,
            'message' => 'Cập nhật nhóm permission thành công'
        ]);
    }

    /**
     * Xóa permission group (soft delete)
     */
    public function delete(Request $request)
    {
        $id = (int) data_get($request, 'id');

        if ($id <= 0) {
            return response()->json(['message' => 'ID không hợp lệ'], 400);
        }

        // Kiểm tra group tồn tại
        $group = u::first("SELECT * FROM permission_groups WHERE id = $id");
        if (!$group) {
            return response()->json(['message' => 'Permission group không tồn tại'], 404);
        }

        // Kiểm tra xem có permission nào đang sử dụng không
        $permission_count = u::first("SELECT COUNT(id) AS total FROM permissions WHERE group_id = $id AND status = 1");
        if ($permission_count->total > 0) {
            return response()->json(['message' => 'Không thể xóa nhóm đang chứa ' . $permission_count->total . ' permission(s)'], 400);
        }

        // Soft delete
        u::updateSimpleRow([
            'status' => 0,
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => Auth::user()->id,
        ], ['id' => $id], 'permission_groups');

        return response()->json([
            'status' => 1,
            'message' => 'Xóa nhóm permission thành công'
        ]);
    }
}
