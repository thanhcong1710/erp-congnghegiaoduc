<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionsController extends Controller
{
    /**
     * Lấy danh sách permissions với phân trang và tìm kiếm
     */
    public function list(Request $request)
    {
        $keyword = isset($request->keyword) ? $request->keyword : '';
        $group_id = isset($request->group_id) ? (int) $request->group_id : 0;

        $pagination = (object) $request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);
        $limitation = $limit > 0 ? " LIMIT $offset, $limit" : "";

        $cond = " p.status = 1";

        if ($keyword !== '') {
            $cond .= " AND (p.name LIKE '%$keyword%' OR p.description LIKE '%$keyword%')";
        }

        if ($group_id > 0) {
            $cond .= " AND p.group_id = $group_id";
        }

        $order_by = " ORDER BY p.group_id, p.display_order, p.id";

        $total = u::first("SELECT COUNT(p.id) AS total FROM permissions AS p WHERE $cond");
        $list = u::query("SELECT p.*, 
                (SELECT name FROM permission_groups WHERE id = p.group_id) AS group_name
            FROM permissions AS p 
            WHERE $cond $order_by $limitation");

        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    /**
     * Lấy thông tin chi tiết permission
     */
    public function show(Request $request, $id)
    {
        $permission = u::first("SELECT p.*, 
                (SELECT name FROM permission_groups WHERE id = p.group_id) AS group_name
            FROM permissions AS p WHERE p.id = $id");

        if (!$permission) {
            return response()->json(['message' => 'Permission không tồn tại'], 404);
        }

        return response()->json([
            'permission' => $permission
        ]);
    }

    /**
     * Thêm mới permission
     */
    public function add(Request $request)
    {
        $name = trim(data_get($request, 'name'));
        $description = trim(data_get($request, 'description'));
        $group_id = (int) data_get($request, 'group_id');
        $display_order = (int) data_get($request, 'display_order', 0);

        // Validate
        if (empty($name)) {
            return response()->json(['message' => 'Tên permission không được để trống'], 400);
        }

        if (empty($description)) {
            return response()->json(['message' => 'Mô tả không được để trống'], 400);
        }

        if ($group_id <= 0) {
            return response()->json(['message' => 'Vui lòng chọn nhóm permission'], 400);
        }

        // Kiểm tra trùng name
        $exists = u::first("SELECT id FROM permissions WHERE name = '$name' AND status = 1");
        if ($exists) {
            return response()->json(['message' => 'Tên permission đã tồn tại'], 400);
        }

        // Thêm mới
        $permission_id = u::insertSimpleRow([
            'name' => $name,
            'description' => $description,
            'group_id' => $group_id,
            'display_order' => $display_order,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'creator_id' => Auth::user()->id,
        ], 'permissions');

        return response()->json([
            'status' => 1,
            'message' => 'Thêm permission thành công',
            'permission_id' => $permission_id
        ]);
    }

    /**
     * Cập nhật permission
     */
    public function update(Request $request)
    {
        $id = (int) data_get($request, 'id');
        $name = trim(data_get($request, 'name'));
        $description = trim(data_get($request, 'description'));
        $group_id = (int) data_get($request, 'group_id');
        $display_order = (int) data_get($request, 'display_order', 0);

        // Validate
        if ($id <= 0) {
            return response()->json(['message' => 'ID không hợp lệ'], 400);
        }

        if (empty($name)) {
            return response()->json(['message' => 'Tên permission không được để trống'], 400);
        }

        if (empty($description)) {
            return response()->json(['message' => 'Mô tả không được để trống'], 400);
        }

        if ($group_id <= 0) {
            return response()->json(['message' => 'Vui lòng chọn nhóm permission'], 400);
        }

        // Kiểm tra permission tồn tại
        $permission = u::first("SELECT * FROM permissions WHERE id = $id");
        if (!$permission) {
            return response()->json(['message' => 'Permission không tồn tại'], 404);
        }

        // Kiểm tra trùng name (trừ chính nó)
        $exists = u::first("SELECT id FROM permissions WHERE name = '$name' AND id != $id AND status = 1");
        if ($exists) {
            return response()->json(['message' => 'Tên permission đã tồn tại'], 400);
        }

        // Cập nhật
        u::updateSimpleRow([
            'name' => $name,
            'description' => $description,
            'group_id' => $group_id,
            'display_order' => $display_order,
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => Auth::user()->id,
        ], ['id' => $id], 'permissions');

        return response()->json([
            'status' => 1,
            'message' => 'Cập nhật permission thành công'
        ]);
    }

    /**
     * Xóa permission (soft delete)
     */
    public function delete(Request $request)
    {
        $id = (int) data_get($request, 'id');

        if ($id <= 0) {
            return response()->json(['message' => 'ID không hợp lệ'], 400);
        }

        // Kiểm tra permission tồn tại
        $permission = u::first("SELECT * FROM permissions WHERE id = $id");
        if (!$permission) {
            return response()->json(['message' => 'Permission không tồn tại'], 404);
        }

        // Kiểm tra xem có role nào đang sử dụng không
        $role_count = u::first("SELECT COUNT(role_id) AS total FROM permission_has_role WHERE permission_id = $id");
        if ($role_count->total > 0) {
            return response()->json(['message' => 'Không thể xóa permission đang được sử dụng bởi ' . $role_count->total . ' role(s)'], 400);
        }

        // Soft delete
        u::updateSimpleRow([
            'status' => 0,
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => Auth::user()->id,
        ], ['id' => $id], 'permissions');

        return response()->json([
            'status' => 1,
            'message' => 'Xóa permission thành công'
        ]);
    }

    /**
     * Lấy danh sách permission groups
     */
    public function getGroups()
    {
        $groups = u::query("SELECT * FROM permission_groups WHERE status = 1 ORDER BY display_order, id");
        return response()->json($groups);
    }
}
