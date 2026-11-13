<?php

namespace App\Http\Controllers;

use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Cấu hình hệ thống → Nguồn khách hàng (sources) & Nguồn chi tiết (source_detail).
 *
 * Lưu ý: nguồn chi tiết của Marketing (1), B2B (2), C2C (3) được tự sinh từ module
 * Chiến dịch (source_detail.campaign_id) → màn này chỉ cho xem, không thêm/sửa/xóa tay.
 */
class SourcesController extends Controller
{
    const CAMPAIGN_SOURCE_IDS = [1, 2, 3];

    private function usageCount($column, $id)
    {
        $id = (int) $id;
        $row = u::first("SELECT
                (SELECT COUNT(*) FROM crm_parents WHERE $column = $id) +
                (SELECT COUNT(*) FROM students WHERE $column = $id) AS total");
        return (int) data_get($row, 'total', 0);
    }

    /* ======================== NGUỒN ======================== */

    public function listSources(Request $request)
    {
        $list = DB::select("SELECT s.*,
                (SELECT COUNT(*) FROM source_detail d WHERE d.source_id = s.id) AS detail_total,
                (SELECT COUNT(*) FROM source_detail d WHERE d.source_id = s.id AND d.status = 1) AS detail_active,
                (SELECT COUNT(*) FROM crm_parents p WHERE p.source_id = s.id) AS parent_total
            FROM sources s ORDER BY s.id ASC");
        foreach ($list as $s) {
            $s->is_campaign_source = in_array((int) $s->id, self::CAMPAIGN_SOURCE_IDS) ? 1 : 0;
        }
        return response()->json((object) ['status' => 1, 'message' => 'OK', 'data' => $list]);
    }

    public function saveSource(Request $request)
    {
        $name = trim((string) data_get($request, 'name', ''));
        if ($name === '') {
            return response()->json((object) ['status' => 0, 'message' => 'Vui lòng nhập tên nguồn']);
        }
        $id = (int) data_get($request, 'id', 0);
        $dup = DB::selectOne("SELECT id FROM sources WHERE name = ? AND id <> ? LIMIT 1", [$name, $id]);
        if ($dup) {
            return response()->json((object) ['status' => 0, 'message' => 'Tên nguồn đã tồn tại']);
        }
        $data = [
            'name' => $name,
            'description' => trim((string) data_get($request, 'description', '')) ?: null,
            'status' => (int) data_get($request, 'status', 1) ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        if ($id > 0) {
            u::updateSimpleRow($data, ['id' => $id], 'sources');
            $msg = 'Cập nhật nguồn thành công';
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $id = u::insertSimpleRow($data, 'sources');
            $msg = 'Thêm mới nguồn thành công';
        }
        return response()->json((object) ['status' => 1, 'message' => $msg, 'data' => ['id' => $id]]);
    }

    public function deleteSource(Request $request)
    {
        $id = (int) data_get($request, 'id');
        if (in_array($id, self::CAMPAIGN_SOURCE_IDS)) {
            return response()->json((object) ['status' => 0, 'message' => 'Nguồn gắn với chiến dịch Marketing/B2B/C2C, không thể xóa']);
        }
        $details = DB::selectOne("SELECT COUNT(*) AS total FROM source_detail WHERE source_id = ?", [$id]);
        if ((int) $details->total > 0) {
            return response()->json((object) ['status' => 0, 'message' => 'Nguồn đang có nguồn chi tiết, vui lòng xóa/chuyển nguồn chi tiết trước hoặc chuyển sang Ngừng sử dụng']);
        }
        $used = $this->usageCount('source_id', $id);
        if ($used > 0) {
            return response()->json((object) ['status' => 0, 'message' => "Nguồn đang được dùng cho $used khách hàng/học sinh, chỉ có thể chuyển sang Ngừng sử dụng"]);
        }
        DB::delete("DELETE FROM sources WHERE id = ?", [$id]);
        return response()->json((object) ['status' => 1, 'message' => 'Xóa nguồn thành công']);
    }

    /* ===================== NGUỒN CHI TIẾT ===================== */

    public function listDetails(Request $request)
    {
        $where = ['1 = 1'];
        $bind = [];
        if ($sourceId = (int) data_get($request, 'source_id')) {
            $where[] = 'd.source_id = ?';
            $bind[] = $sourceId;
        }
        $keyword = trim((string) data_get($request, 'keyword', ''));
        if ($keyword !== '') {
            $where[] = 'd.name LIKE ?';
            $bind[] = '%' . $keyword . '%';
        }
        $status = data_get($request, 'status');
        if ($status !== null && $status !== '') {
            $where[] = 'd.status = ?';
            $bind[] = (int) $status;
        }
        $branchId = data_get($request, 'branch_id');
        if ($branchId !== null && $branchId !== '') {
            if ((int) $branchId === 0) {
                $where[] = '(d.branch_id IS NULL OR d.branch_id = 0)';
            } else {
                $where[] = 'd.branch_id = ?';
                $bind[] = (int) $branchId;
            }
        }
        $cond = implode(' AND ', $where);

        $page = max(1, (int) data_get($request, 'page', 1));
        $limit = min(200, max(1, (int) data_get($request, 'limit', 20)));
        $offset = ($page - 1) * $limit;

        $total = DB::selectOne("SELECT COUNT(*) AS total FROM source_detail d WHERE $cond", $bind);
        $list = DB::select("SELECT d.*, s.name AS source_name, b.name AS branch_name,
                u1.name AS creator_name,
                (SELECT COUNT(*) FROM crm_parents p WHERE p.source_detail_id = d.id) AS parent_total,
                (SELECT COUNT(*) FROM students st WHERE st.source_detail_id = d.id) AS student_total
            FROM source_detail d
            LEFT JOIN sources s ON s.id = d.source_id
            LEFT JOIN branches b ON b.id = d.branch_id
            LEFT JOIN users u1 ON u1.id = d.creator_id
            WHERE $cond
            ORDER BY d.status DESC, d.id DESC
            LIMIT $offset, $limit", $bind);
        foreach ($list as $d) {
            $d->is_campaign = (!empty($d->campaign_id) || in_array((int) $d->source_id, self::CAMPAIGN_SOURCE_IDS)) ? 1 : 0;
        }
        return response()->json((object) [
            'status' => 1,
            'message' => 'OK',
            'data' => u::makingPagination($list, (int) $total->total, $page, $limit),
        ]);
    }

    public function saveDetail(Request $request)
    {
        $id = (int) data_get($request, 'id', 0);
        $sourceId = (int) data_get($request, 'source_id');
        $name = trim((string) data_get($request, 'name', ''));
        if (!$sourceId || $name === '') {
            return response()->json((object) ['status' => 0, 'message' => 'Vui lòng chọn nguồn và nhập tên nguồn chi tiết']);
        }
        if (in_array($sourceId, self::CAMPAIGN_SOURCE_IDS)) {
            return response()->json((object) ['status' => 0, 'message' => 'Nguồn chi tiết của Marketing/B2B/C2C được tạo tự động từ module Chiến dịch']);
        }
        if (!DB::selectOne("SELECT id FROM sources WHERE id = ?", [$sourceId])) {
            return response()->json((object) ['status' => 0, 'message' => 'Nguồn không tồn tại']);
        }
        if ($id > 0) {
            $cur = DB::selectOne("SELECT * FROM source_detail WHERE id = ?", [$id]);
            if (!$cur) {
                return response()->json((object) ['status' => 0, 'message' => 'Không tìm thấy nguồn chi tiết']);
            }
            if (!empty($cur->campaign_id) || in_array((int) $cur->source_id, self::CAMPAIGN_SOURCE_IDS)) {
                return response()->json((object) ['status' => 0, 'message' => 'Nguồn chi tiết thuộc chiến dịch, vui lòng sửa trong module Marketing']);
            }
        }
        $branchId = (int) data_get($request, 'branch_id', 0) ?: null;
        $dup = DB::selectOne("SELECT id FROM source_detail WHERE source_id = ? AND name = ? AND id <> ?
                AND (branch_id <=> ?) LIMIT 1", [$sourceId, $name, $id, $branchId]);
        if ($dup) {
            return response()->json((object) ['status' => 0, 'message' => 'Nguồn chi tiết này đã tồn tại trong nguồn đã chọn']);
        }
        $data = [
            'name' => $name,
            'source_id' => $sourceId,
            'branch_id' => $branchId,
            'status' => (int) data_get($request, 'status', 1) ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => Auth::user()->id,
        ];
        if ($id > 0) {
            u::updateSimpleRow($data, ['id' => $id], 'source_detail');
            $msg = 'Cập nhật nguồn chi tiết thành công';
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['creator_id'] = Auth::user()->id;
            $id = u::insertSimpleRow($data, 'source_detail');
            $msg = 'Thêm mới nguồn chi tiết thành công';
        }
        return response()->json((object) ['status' => 1, 'message' => $msg, 'data' => ['id' => $id]]);
    }

    public function toggleDetail(Request $request)
    {
        $id = (int) data_get($request, 'id');
        $cur = DB::selectOne("SELECT * FROM source_detail WHERE id = ?", [$id]);
        if (!$cur) {
            return response()->json((object) ['status' => 0, 'message' => 'Không tìm thấy nguồn chi tiết']);
        }
        if (!empty($cur->campaign_id) || in_array((int) $cur->source_id, self::CAMPAIGN_SOURCE_IDS)) {
            return response()->json((object) ['status' => 0, 'message' => 'Nguồn chi tiết thuộc chiến dịch, vui lòng cập nhật trong module Marketing']);
        }
        $new = (int) $cur->status === 1 ? 0 : 1;
        DB::update("UPDATE source_detail SET status = ?, updated_at = ?, updator_id = ? WHERE id = ?", [$new, date('Y-m-d H:i:s'), Auth::user()->id, $id]);
        return response()->json((object) ['status' => 1, 'message' => $new ? 'Đã kích hoạt nguồn chi tiết' : 'Đã ngừng sử dụng nguồn chi tiết', 'data' => ['status' => $new]]);
    }

    public function deleteDetail(Request $request)
    {
        $id = (int) data_get($request, 'id');
        $cur = DB::selectOne("SELECT * FROM source_detail WHERE id = ?", [$id]);
        if (!$cur) {
            return response()->json((object) ['status' => 0, 'message' => 'Không tìm thấy nguồn chi tiết']);
        }
        if (!empty($cur->campaign_id) || in_array((int) $cur->source_id, self::CAMPAIGN_SOURCE_IDS)) {
            return response()->json((object) ['status' => 0, 'message' => 'Nguồn chi tiết thuộc chiến dịch, không thể xóa tại đây']);
        }
        $used = $this->usageCount('source_detail_id', $id);
        if ($used > 0) {
            return response()->json((object) ['status' => 0, 'message' => "Nguồn chi tiết đang được dùng cho $used khách hàng/học sinh, chỉ có thể chuyển sang Ngừng sử dụng"]);
        }
        DB::delete("DELETE FROM source_detail WHERE id = ?", [$id]);
        return response()->json((object) ['status' => 1, 'message' => 'Xóa nguồn chi tiết thành công']);
    }
}
