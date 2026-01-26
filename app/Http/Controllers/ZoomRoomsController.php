<?php

namespace App\Http\Controllers;

use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ZoomRoomsController extends Controller
{
    /**
     * Lấy danh sách phòng Zoom theo lớp học
     */
    public function list(Request $request)
    {
        $class_id = isset($request->class_id) ? $request->class_id : null;
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $keyword = isset($request->keyword) ? $request->keyword : '';

        $pagination = (object) $request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page - 1);

        $cond = " z.status > 0 ";

        if ($class_id) {
            $cond .= " AND z.class_id = $class_id";
        }

        if (!empty($branch_id)) {
            $cond .= " AND c.branch_id IN (" . implode(",", $branch_id) . ")";
        }

        if ($keyword !== '') {
            $cond .= " AND (c.cls_name LIKE '%$keyword%' OR z.topic LIKE '%$keyword%' OR z.meeting_id LIKE '%$keyword%')";
        }

        $limitation = $limit > 0 ? " LIMIT $offset, $limit" : "";
        $order_by = " ORDER BY z.id DESC ";

        $total = u::first("SELECT count(z.id) AS total 
            FROM zoom_rooms AS z
            LEFT JOIN classes AS c ON c.id = z.class_id
            WHERE $cond");

        $list = u::query("SELECT z.*, c.cls_name, c.is_online,
                (SELECT name FROM branches WHERE id = c.branch_id) AS branch_name,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id = c.teacher_id) AS teacher_name,
                (SELECT name FROM products WHERE id = c.product_id) AS product_name
            FROM zoom_rooms AS z
            LEFT JOIN classes AS c ON c.id = z.class_id
            WHERE $cond $order_by $limitation");

        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    /**
     * Lấy thông tin chi tiết phòng Zoom
     */
    public function show(Request $request, $id)
    {
        $data = u::first("SELECT z.*, c.cls_name, c.is_online,
                (SELECT name FROM branches WHERE id = c.branch_id) AS branch_name,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id = c.teacher_id) AS teacher_name,
                (SELECT name FROM products WHERE id = c.product_id) AS product_name
            FROM zoom_rooms AS z
            LEFT JOIN classes AS c ON c.id = z.class_id
            WHERE z.id = $id");

        return response()->json($data);
    }

    /**
     * Lấy thông tin phòng Zoom theo class_id
     */
    public function getByClass(Request $request, $class_id)
    {
        $data = u::first("SELECT z.*, c.cls_name, c.is_online
            FROM zoom_rooms AS z
            LEFT JOIN classes AS c ON c.id = z.class_id
            WHERE z.class_id = $class_id AND z.status = 1");

        return response()->json($data);
    }

    /**
     * Tạo mới hoặc cập nhật phòng Zoom
     */
    public function save(Request $request)
    {
        $id = isset($request->id) ? $request->id : 0;
        $class_id = $request->class_id;

        // Kiểm tra lớp học có phải loại online không
        $class_info = u::first("SELECT id, is_online, cls_name FROM classes WHERE id = $class_id");
        if (!$class_info) {
            return response()->json([
                'status' => 0,
                'message' => 'Lớp học không tồn tại'
            ]);
        }

        // Kiểm tra xem lớp đã có phòng Zoom chưa (nếu tạo mới)
        if ($id == 0) {
            $existing = u::first("SELECT id FROM zoom_rooms WHERE class_id = $class_id AND status = 1");
            if ($existing) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Lớp học đã có phòng Zoom. Vui lòng cập nhật thay vì tạo mới.'
                ]);
            }
        }

        $data = [
            'class_id' => $class_id,
            'meeting_id' => $request->meeting_id,
            'meeting_password' => $request->meeting_password,
            'join_url' => $request->join_url,
            'start_url' => $request->start_url,
            'host_email' => $request->host_email,
            'topic' => $request->topic ?? $class_info->cls_name,
            'duration' => $request->duration ?? 60,
            'timezone' => $request->timezone ?? 'Asia/Ho_Chi_Minh',
            'settings' => $request->settings ? json_encode($request->settings) : null,
        ];

        if ($id > 0) {
            // Cập nhật
            $data['updated_at'] = date('Y-m-d H:i:s');
            $data['updator_id'] = Auth::user()->id;
            u::updateSimpleRow($data, ['id' => $id], 'zoom_rooms');

            return response()->json([
                'status' => 1,
                'message' => 'Cập nhật phòng Zoom thành công',
                'data' => ['id' => $id]
            ]);
        } else {
            // Tạo mới
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['creator_id'] = Auth::user()->id;
            $data['status'] = 1;
            $new_id = u::insertSimpleRow($data, 'zoom_rooms');

            return response()->json([
                'status' => 1,
                'message' => 'Tạo phòng Zoom thành công',
                'data' => ['id' => $new_id]
            ]);
        }
    }

    /**
     * Xóa phòng Zoom
     */
    public function delete(Request $request, $id)
    {
        u::updateSimpleRow([
            'status' => 0,
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => Auth::user()->id
        ], ['id' => $id], 'zoom_rooms');

        return response()->json([
            'status' => 1,
            'message' => 'Xóa phòng Zoom thành công'
        ]);
    }

    /**
     * Lấy danh sách lớp online chưa có phòng Zoom
     */
    public function getAvailableClasses(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];

        $cond = " c.status = 1 AND c.is_online = 1"; // is_online = 1 là lớp online

        if (!empty($branch_id)) {
            $cond .= " AND c.branch_id IN (" . implode(",", $branch_id) . ")";
        }

        $list = u::query("SELECT c.id, c.cls_name, c.is_online,
                (SELECT name FROM branches WHERE id = c.branch_id) AS branch_name,
                (SELECT CONCAT(name, ' - ', hrm_id) FROM users WHERE id = c.teacher_id) AS teacher_name,
                (SELECT name FROM products WHERE id = c.product_id) AS product_name
            FROM classes AS c
            WHERE $cond 
            AND NOT EXISTS (SELECT 1 FROM zoom_rooms WHERE class_id = c.id AND status = 1)
            ORDER BY c.id DESC");

        return response()->json($list);
    }
}
