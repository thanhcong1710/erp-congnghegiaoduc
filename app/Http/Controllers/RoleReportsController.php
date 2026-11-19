<?php

namespace App\Http\Controllers;

use App\Providers\UtilityServiceProvider as u;
use App\Services\ReportAccess as RA;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Báo cáo theo vai trò (05 → 12) trên trang /lms/reports.
 * Danh mục + phân quyền: App\Services\ReportAccess (admin xem tất cả).
 *
 * Phạm vi dữ liệu:
 * - Admin: toàn bộ cơ sở.
 * - GĐ trung tâm / Marketing / Kế toán: các cơ sở được phân quyền (branch_has_user).
 * - Leader CSKH (56) / Leader GV (37): cơ sở được phân quyền + nhân viên trong team (users.manager_id).
 * - CSKH / GV / Trợ giảng: chỉ dữ liệu của chính mình.
 *
 * Response chung: columns [{key,label,type,sub?}], rows, summary, cards, meta.
 */
class RoleReportsController extends Controller
{
    const ACTIVE_CONTRACT_STATUSES = '1,2,3,5,6';
    const BRANCH_LEVEL_ROLES = [RA::ROLE_CEO, RA::ROLE_MARKETING, RA::ROLE_ACCOUNTANT];
    const LEADER_ROLES = [RA::ROLE_CM_LEADER, RA::ROLE_TEACHER_LEADER, RA::ROLE_EC_LEADER];

    /** Role nhân viên hiển thị trong bộ lọc "Nhân viên" của từng báo cáo */
    const STAFF_ROLES = [
        'cm_summary' => [55, 56],
        'cm_renew' => [55, 56],
        'attendance_class' => [55, 56, 36, 37, 54],
        'teacher_workload' => [36, 37, 54],
    ];

    const METHODS = [0 => 'Tiền mặt', 1 => 'Chuyển khoản', 2 => 'Thẻ tín dụng'];

    /* ======================= Init ======================= */

    public function init(Request $request)
    {
        $uid = (int)Auth::user()->id;
        $catalog = RA::catalogFor($uid);
        $staffByReport = [];
        foreach ($catalog as $g) {
            foreach ($g['reports'] as $r) {
                if (in_array('staff', $r['filters'], true)) {
                    $scope = $this->scope($r['code']);
                    $staffByReport[$r['code']] = [
                        'level' => $scope['level'],
                        'staff' => $this->staffOptions($r['code'], $scope),
                    ];
                }
            }
        }
        $allBranches = $this->allowedBranchIds(RA::isAdmin($uid));
        $roleNames = u::query("SELECT r.name FROM role_has_user AS ru JOIN roles AS r ON r.id = ru.role_id WHERE ru.user_id = $uid");

        return response()->json((object)['status' => 1, 'message' => '', 'data' => [
            'is_admin' => RA::isAdmin($uid),
            'roles' => array_column($roleNames, 'name'),
            'catalog' => $catalog,
            'branches' => u::query("SELECT id, name FROM branches WHERE status = 1 AND id IN ($allBranches) ORDER BY name"),
            'sources' => u::query("SELECT id, name FROM sources ORDER BY id"),
            'products' => u::query("SELECT id, name FROM products WHERE status = 1 ORDER BY name"),
            'methods' => array_map(function ($k, $v) { return ['id' => $k, 'name' => $v]; }, array_keys(self::METHODS), self::METHODS),
            'staff_by_report' => $staffByReport,
        ]]);
    }

    /* ======================= Data ======================= */

    public function data(Request $request)
    {
        $code = (string)$request->input('code');
        $uid = (int)Auth::user()->id;
        if (!isset(RA::CATALOG[$code]) || in_array($code, ['overview', 'team', 'business_source', 'business_product'], true)) {
            return response()->json((object)['status' => 0, 'message' => 'Báo cáo không tồn tại', 'data' => null]);
        }
        if (!RA::can($uid, $code)) {
            return response()->json((object)['status' => 0, 'message' => 'Bạn không có quyền xem báo cáo này', 'data' => null], 403);
        }

        $scope = $this->scope($code);
        // Lọc theo cơ sở (chỉ trong phạm vi được phép)
        $branchId = (int)$request->input('branch_id');
        $branchIds = $scope['branch_ids'];
        if ($branchId) {
            if (!in_array($branchId, array_map('intval', explode(',', $branchIds)), true)) {
                return response()->json((object)['status' => 0, 'message' => 'Bạn không có quyền xem dữ liệu cơ sở này', 'data' => null], 403);
            }
            $branchIds = (string)$branchId;
        }
        // Lọc theo nhân viên (chỉ trong phạm vi được phép)
        $staffIds = $scope['staff_ids']; // null = không giới hạn
        $staffId = (int)$request->input('staff_id');
        if ($staffId) {
            if ($staffIds !== null && !in_array($staffId, array_map('intval', explode(',', $staffIds)), true)) {
                return response()->json((object)['status' => 0, 'message' => 'Bạn không có quyền xem dữ liệu nhân viên này', 'data' => null], 403);
            }
            $staffIds = (string)$staffId;
        }
        list($start, $end) = $this->dateRange($request);

        $ctx = (object)[
            'branch_ids' => $branchIds,
            'staff_ids' => $staffIds,
            'level' => $scope['level'],
            'start' => $start,
            'end' => $end,
            'today' => date('Y-m-d'),
            'source_id' => (int)$request->input('source_id'),
            'product_id' => (int)$request->input('product_id'),
            'method' => $request->input('method', '') === '' || $request->input('method') === null ? null : (int)$request->input('method'),
            'group_by' => (string)$request->input('group_by', ''),
            'renew_type' => (string)$request->input('renew_type', 'near_end'),
            'threshold' => max(0, min(30, (int)$request->input('threshold', 4))),
        ];

        switch ($code) {
            case 'mkt_source': $data = $this->mktSource($ctx); break;
            case 'mkt_trend': $data = $this->mktTrend($ctx); break;
            case 'cm_summary': $data = $this->cmSummary($ctx); break;
            case 'cm_renew': $data = $this->cmRenew($ctx); break;
            case 'attendance_class': $data = $this->attendanceClass($ctx); break;
            case 'teacher_workload': $data = $this->teacherWorkload($ctx); break;
            case 'finance_receipts': $data = $this->financeReceipts($ctx); break;
            case 'finance_debt': $data = $this->financeDebt($ctx); break;
            default: $data = ['columns' => [], 'rows' => []];
        }
        $data['meta'] = ['level' => $scope['level'], 'start_date' => $start, 'end_date' => $end];
        $data += ['summary' => null, 'cards' => [], 'note' => ''];
        return response()->json((object)['status' => 1, 'message' => '', 'data' => $data]);
    }

    /* ======================= Phạm vi ======================= */

    private function allowedBranchIds($admin)
    {
        if ($admin) {
            $ids = array_map(function ($r) { return (int)$r->id; }, u::query("SELECT id FROM branches WHERE status = 1"));
        } else {
            $ids = array_map('intval', explode(',', (string)Auth::user()->getBranchesHasUser()));
        }
        return implode(',', array_filter($ids)) ?: '0';
    }

    /** level: all | branch | team | self */
    private function scope($code)
    {
        $user = Auth::user();
        $uid = (int)$user->id;
        $roles = RA::rolesOf($uid);
        if (in_array(RA::ROLE_ADMIN, $roles, true)) {
            return ['level' => 'all', 'branch_ids' => $this->allowedBranchIds(true), 'staff_ids' => null];
        }
        $usable = array_values(array_intersect($roles, RA::CATALOG[$code]['roles']));
        $branchIds = $this->allowedBranchIds(false);
        if (array_intersect($usable, self::BRANCH_LEVEL_ROLES)) {
            return ['level' => 'branch', 'branch_ids' => $branchIds, 'staff_ids' => null];
        }
        if (array_intersect($usable, self::LEADER_ROLES)) {
            $team = implode(',', array_filter(array_map('intval', explode(',', (string)$user->getStaffHasUser($uid))))) ?: (string)$uid;
            return ['level' => 'team', 'branch_ids' => $branchIds, 'staff_ids' => $team];
        }
        return ['level' => 'self', 'branch_ids' => $branchIds, 'staff_ids' => (string)$uid];
    }

    private function staffOptions($code, $scope)
    {
        $roles = implode(',', self::STAFF_ROLES[$code] ?? [0]);
        $teaching = in_array($code, ['attendance_class', 'teacher_workload'], true);
        $roleCond = "EXISTS (SELECT 1 FROM role_has_user AS ru WHERE ru.user_id = u.id AND ru.role_id IN ($roles))";
        if ($teaching) {
            $roleCond = "($roleCond OR EXISTS (SELECT 1 FROM classes AS cl WHERE cl.status = 1 AND (cl.teacher_id = u.id OR cl.ta_id = u.id)))";
        }
        if ($scope['staff_ids'] !== null) {
            $cond = " AND u.id IN ({$scope['staff_ids']})";
        } else {
            $cond = " AND (EXISTS (SELECT 1 FROM branch_has_user AS bu WHERE bu.user_id = u.id AND bu.branch_id IN ({$scope['branch_ids']}))"
                . ($teaching ? " OR EXISTS (SELECT 1 FROM classes AS cl WHERE cl.branch_id IN ({$scope['branch_ids']}) AND (cl.teacher_id = u.id OR cl.ta_id = u.id))" : '') . ")";
        }
        return u::query("SELECT u.id, u.name, u.hrm_id FROM users AS u WHERE u.status = 1 AND $roleCond $cond ORDER BY u.name");
    }

    private function dateRange(Request $request)
    {
        $s = (string)$request->input('start_date');
        $e = (string)$request->input('end_date');
        $ok = function ($d) { return preg_match('/^\d{4}-\d{2}-\d{2}$/', $d) && strtotime($d); };
        if (!$ok($s)) $s = date('Y-m-01');
        if (!$ok($e)) $e = date('Y-m-d');
        if ($s > $e) list($s, $e) = [$e, $s];
        return [$s, $e];
    }

    /* ======================= Helpers ======================= */

    private function num($v) { return (float)($v ?? 0); }

    private function pct($a, $b) { return $b > 0 ? round($a * 100 / $b, 2) : 0; }

    private function namesOf($table, array $ids, $col = 'name')
    {
        $ids = array_filter(array_map('intval', $ids));
        if (!$ids) return [];
        $out = [];
        foreach (u::query("SELECT id, $col AS name FROM $table WHERE id IN (" . implode(',', array_unique($ids)) . ")") as $r) $out[(int)$r->id] = $r->name;
        return $out;
    }

    private function sumRows(array $rows, array $keys)
    {
        $s = [];
        foreach ($keys as $k) {
            $s[$k] = 0;
            foreach ($rows as $r) $s[$k] += (float)($r[$k] ?? 0);
        }
        return $s;
    }

    private function rateTone($pct)
    {
        if ($pct >= 90) return 'success';
        if ($pct >= 75) return 'warning';
        return 'danger';
    }

    /** CSKH phụ trách hợp đồng: CM trên HĐ → CM của học sinh → CM của lớp */
    private function cmKeyExpr()
    {
        return "COALESCE(NULLIF(c.cm_id, 0),
            (SELECT t.cm_id FROM term_student_user AS t WHERE t.student_id = c.student_id AND t.status = 1 ORDER BY t.id DESC LIMIT 1),
            (SELECT xc.cm_id FROM classes AS xc WHERE xc.id = c.class_id), 0)";
    }

    /* ======================= 05. Marketing - Hiệu quả nguồn ======================= */

    private function mktSource($x)
    {
        $groupBy = in_array($x->group_by, ['source', 'source_detail', 'branch', 'creator'], true) ? $x->group_by : 'source';
        $leadKey = ['source' => 'IFNULL(p.source_id, 0)', 'source_detail' => 'IFNULL(p.source_detail_id, 0)',
            'branch' => 'IFNULL(p.branch_id, 0)', 'creator' => 'IFNULL(p.creator_id, 0)'][$groupBy];
        $contractKey = ['source' => 'COALESCE(NULLIF(cp.source_id, 0), NULLIF(s.source_id, 0), 0)',
            'source_detail' => 'COALESCE(NULLIF(cp.source_detail_id, 0), NULLIF(s.source_detail_id, 0), 0)',
            'branch' => 'IFNULL(c.branch_id, 0)', 'creator' => 'IFNULL(cp.creator_id, 0)'][$groupBy];

        $leadCond = "p.branch_id IN ({$x->branch_ids}) AND p.created_at BETWEEN '{$x->start} 00:00:00' AND '{$x->end} 23:59:59'"
            . ($x->source_id ? " AND p.source_id = {$x->source_id}" : '');
        $leads = u::query("SELECT $leadKey AS k, COUNT(*) AS leads,
                SUM(IFNULL(p.status, 1) <> 1) AS contacted, SUM(p.status IN (8, 9, 10)) AS booked,
                SUM(p.status IN (9, 10)) AS checkin, SUM(p.status = 10) AS bought, SUM(p.status IN (3, 4, 6, 12)) AS bad
            FROM crm_parents AS p WHERE $leadCond GROUP BY k");

        $contracts = u::query("SELECT x.k, COUNT(*) AS n, SUM(x.must_charge) AS amount, SUM(x.total_charged) AS paid FROM (
                SELECT c.id, MAX($contractKey) AS k, MAX(COALESCE(NULLIF(cp.source_id, 0), NULLIF(s.source_id, 0), 0)) AS src,
                    MAX(c.must_charge) AS must_charge, MAX(c.total_charged) AS total_charged
                FROM contracts AS c LEFT JOIN students AS s ON s.id = c.student_id
                    LEFT JOIN crm_students AS cs ON cs.lms_id = c.student_id LEFT JOIN crm_parents AS cp ON cp.id = cs.parent_id
                WHERE c.type = 1 AND c.status > 0 AND c.branch_id IN ({$x->branch_ids})
                    AND c.created_at BETWEEN '{$x->start} 00:00:00' AND '{$x->end} 23:59:59'
                GROUP BY c.id) AS x" . ($x->source_id ? " WHERE x.src = {$x->source_id}" : '') . " GROUP BY x.k");

        $map = [];
        $blank = ['leads' => 0, 'contacted' => 0, 'booked' => 0, 'checkin' => 0, 'bought' => 0, 'bad' => 0, 'contracts' => 0, 'revenue' => 0, 'paid' => 0];
        foreach ($leads as $r) {
            $map[(int)$r->k] = array_merge($blank, ['leads' => (int)$r->leads, 'contacted' => (int)$r->contacted, 'booked' => (int)$r->booked,
                'checkin' => (int)$r->checkin, 'bought' => (int)$r->bought, 'bad' => (int)$r->bad]);
        }
        foreach ($contracts as $r) {
            $k = (int)$r->k;
            if (!isset($map[$k])) $map[$k] = $blank;
            $map[$k]['contracts'] = (int)$r->n;
            $map[$k]['revenue'] = $this->num($r->amount);
            $map[$k]['paid'] = $this->num($r->paid);
        }

        $ids = array_keys($map);
        $subs = [];
        if ($groupBy === 'source') {
            $names = $this->namesOf('sources', $ids);
            $empty = 'Chưa có nguồn';
        } elseif ($groupBy === 'source_detail') {
            $names = $this->namesOf('source_detail', $ids);
            $empty = 'Chưa có kênh ngách';
            $ids2 = array_filter(array_map('intval', $ids));
            if ($ids2) {
                foreach (u::query("SELECT sd.id, s.name FROM source_detail AS sd LEFT JOIN sources AS s ON s.id = sd.source_id WHERE sd.id IN (" . implode(',', $ids2) . ")") as $r) $subs[(int)$r->id] = $r->name;
            }
        } elseif ($groupBy === 'branch') {
            $names = $this->namesOf('branches', $ids);
            $empty = 'Chưa có cơ sở';
        } else {
            $names = $this->namesOf('users', $ids);
            $empty = 'Không rõ người tạo';
        }

        $rows = [];
        foreach ($map as $k => $m) {
            $m['name'] = $k ? ($names[$k] ?? ('#' . $k)) : $empty;
            $m['name_sub'] = $subs[$k] ?? '';
            $m['checkin_rate'] = $this->pct($m['checkin'], $m['leads']);
            $m['conversion'] = $this->pct($m['bought'], $m['leads']);
            $m['bad_rate'] = $this->pct($m['bad'], $m['leads']);
            $rows[] = $m;
        }
        usort($rows, function ($a, $b) { return $b['leads'] <=> $a['leads'] ?: $b['revenue'] <=> $a['revenue']; });

        $sum = $this->sumRows($rows, array_keys($blank));
        $sum['name'] = 'Tổng cộng';
        $sum['checkin_rate'] = $this->pct($sum['checkin'], $sum['leads']);
        $sum['conversion'] = $this->pct($sum['bought'], $sum['leads']);
        $sum['bad_rate'] = $this->pct($sum['bad'], $sum['leads']);

        $label = ['source' => 'Nguồn khách hàng', 'source_detail' => 'Kênh ngách', 'branch' => 'Cơ sở', 'creator' => 'Người tạo data'][$groupBy];
        return [
            'columns' => [
                ['key' => 'name', 'label' => $label, 'type' => 'text', 'sub' => 'name_sub', 'wide' => true],
                ['key' => 'leads', 'label' => 'Data mới', 'type' => 'number'],
                ['key' => 'contacted', 'label' => 'Đã liên hệ', 'type' => 'number'],
                ['key' => 'booked', 'label' => 'Hẹn checkin', 'type' => 'number'],
                ['key' => 'checkin', 'label' => 'Đã checkin', 'type' => 'number'],
                ['key' => 'checkin_rate', 'label' => '% Checkin', 'type' => 'percent'],
                ['key' => 'bought', 'label' => 'Mua gói', 'type' => 'number'],
                ['key' => 'conversion', 'label' => '% Chuyển đổi', 'type' => 'percent', 'highlight' => true],
                ['key' => 'bad', 'label' => 'Data xấu', 'type' => 'number'],
                ['key' => 'bad_rate', 'label' => '% Data xấu', 'type' => 'percent'],
                ['key' => 'contracts', 'label' => 'HĐ mới trong kỳ', 'type' => 'number'],
                ['key' => 'revenue', 'label' => 'Doanh số HĐ', 'type' => 'money'],
                ['key' => 'paid', 'label' => 'Đã thu', 'type' => 'money', 'tone' => 'success'],
            ],
            'rows' => $rows,
            'summary' => $sum,
            'cards' => [
                ['label' => 'Data mới', 'value' => $sum['leads'], 'type' => 'number', 'tone' => 'primary'],
                ['label' => 'Đã checkin', 'value' => $sum['checkin'], 'type' => 'number', 'sub' => $sum['checkin_rate'] . '% data'],
                ['label' => 'Mua gói', 'value' => $sum['bought'], 'type' => 'number', 'sub' => $sum['conversion'] . '% chuyển đổi', 'tone' => 'success'],
                ['label' => 'Doanh số HĐ mới', 'value' => $sum['revenue'], 'type' => 'money', 'sub' => $sum['contracts'] . ' hợp đồng'],
            ],
            'note' => 'Data mới tính theo ngày tạo data; trạng thái checkin/mua gói là trạng thái hiện tại của data. Doanh số = hợp đồng chính thức tạo trong kỳ, gắn nguồn theo phụ huynh của học sinh.',
        ];
    }

    /* ======================= 06. Marketing - Data theo thời gian ======================= */

    private function mktTrend($x)
    {
        $mode = in_array($x->group_by, ['day', 'week', 'month'], true) ? $x->group_by : 'day';
        $perExpr = [
            'day' => "DATE(p.created_at)",
            'week' => "DATE(DATE_SUB(p.created_at, INTERVAL WEEKDAY(p.created_at) DAY))",
            'month' => "DATE_FORMAT(p.created_at, '%Y-%m')",
        ][$mode];
        $cond = "p.branch_id IN ({$x->branch_ids}) AND p.created_at BETWEEN '{$x->start} 00:00:00' AND '{$x->end} 23:59:59'"
            . ($x->source_id ? " AND p.source_id = {$x->source_id}" : '');
        $data = u::query("SELECT $perExpr AS per, IFNULL(p.source_id, 0) AS src, COUNT(*) AS n,
                SUM(p.status IN (9, 10)) AS checkin, SUM(p.status = 10) AS bought
            FROM crm_parents AS p WHERE $cond GROUP BY per, src");

        // Danh sách kỳ đầy đủ (kể cả kỳ không có data)
        $periods = [];
        $t = strtotime($x->start);
        $endT = strtotime($x->end);
        if ($mode === 'week') $t = strtotime('-' . (date('N', $t) - 1) . ' days', $t);
        if ($mode === 'month') $t = strtotime(date('Y-m-01', $t));
        $guard = 0;
        while ($t <= $endT && $guard++ < 800) {
            if ($mode === 'day') {
                $k = date('Y-m-d', $t); $label = date('d/m/Y', $t) . ' (' . ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'][(int)date('w', $t)] . ')';
                $t = strtotime('+1 day', $t);
            } elseif ($mode === 'week') {
                $k = date('Y-m-d', $t); $label = 'Tuần ' . date('d/m', $t) . ' - ' . date('d/m/Y', strtotime('+6 days', $t));
                $t = strtotime('+7 days', $t);
            } else {
                $k = date('Y-m', $t); $label = 'Tháng ' . date('m/Y', $t);
                $t = strtotime('+1 month', $t);
            }
            $periods[$k] = $label;
        }

        $srcIds = [];
        $byPer = [];
        foreach ($data as $r) {
            $k = (string)$r->per;
            $s = (int)$r->src;
            $srcIds[$s] = true;
            if (!isset($byPer[$k])) $byPer[$k] = ['total' => 0, 'checkin' => 0, 'bought' => 0, 'src' => []];
            $byPer[$k]['total'] += (int)$r->n;
            $byPer[$k]['checkin'] += (int)$r->checkin;
            $byPer[$k]['bought'] += (int)$r->bought;
            $byPer[$k]['src'][$s] = ($byPer[$k]['src'][$s] ?? 0) + (int)$r->n;
        }
        $srcNames = $this->namesOf('sources', array_keys($srcIds));
        $srcCols = [];
        foreach (array_keys($srcIds) as $s) {
            $srcCols[] = ['key' => 'src_' . $s, 'label' => $s ? ($srcNames[$s] ?? '#' . $s) : 'Chưa có nguồn', 'type' => 'number', 'group' => 'Data mới theo nguồn'];
        }

        $rows = [];
        foreach ($periods as $k => $label) {
            $p = $byPer[$k] ?? ['total' => 0, 'checkin' => 0, 'bought' => 0, 'src' => []];
            $row = ['period' => $label, 'total' => $p['total'], 'checkin' => $p['checkin'], 'bought' => $p['bought'],
                'conversion' => $this->pct($p['bought'], $p['total'])];
            foreach (array_keys($srcIds) as $s) $row['src_' . $s] = $p['src'][$s] ?? 0;
            $rows[] = $row;
        }
        $sumKeys = array_merge(['total', 'checkin', 'bought'], array_map(function ($c) { return $c['key']; }, $srcCols));
        $sum = $this->sumRows($rows, $sumKeys);
        $sum['period'] = 'Tổng cộng';
        $sum['conversion'] = $this->pct($sum['bought'], $sum['total']);
        $nPer = max(1, count($rows));

        return [
            'columns' => array_merge(
                [['key' => 'period', 'label' => 'Kỳ', 'type' => 'text', 'wide' => true]],
                $srcCols,
                [
                    ['key' => 'total', 'label' => 'Tổng data mới', 'type' => 'number', 'highlight' => true],
                    ['key' => 'checkin', 'label' => 'Đã checkin', 'type' => 'number'],
                    ['key' => 'bought', 'label' => 'Mua gói', 'type' => 'number', 'tone' => 'success'],
                    ['key' => 'conversion', 'label' => '% Chuyển đổi', 'type' => 'percent'],
                ]
            ),
            'rows' => $rows,
            'summary' => $sum,
            'cards' => [
                ['label' => 'Tổng data mới', 'value' => $sum['total'], 'type' => 'number', 'tone' => 'primary'],
                ['label' => 'Trung bình / ' . ['day' => 'ngày', 'week' => 'tuần', 'month' => 'tháng'][$mode], 'value' => round($sum['total'] / $nPer, 1), 'type' => 'number'],
                ['label' => 'Đã checkin', 'value' => $sum['checkin'], 'type' => 'number'],
                ['label' => 'Mua gói', 'value' => $sum['bought'], 'type' => 'number', 'sub' => $sum['conversion'] . '% chuyển đổi', 'tone' => 'success'],
            ],
            'note' => 'Data tính theo ngày tạo; số checkin / mua gói là trạng thái hiện tại của các data phát sinh trong kỳ đó.',
        ];
    }

    /* ======================= 07. CSKH - Tổng hợp học sinh ======================= */

    private function cmSummary($x)
    {
        $cmKey = $this->cmKeyExpr();
        $contracts = u::query("SELECT * FROM (
                SELECT c.id, c.student_id, c.status, c.left_sessions, c.debt_amount, c.must_charge, c.created_at,
                    COALESCE(c.enrolment_last_date, DATE(c.updated_at)) AS last_date, $cmKey AS cm_key,
                    EXISTS (SELECT 1 FROM contracts AS c0 WHERE c0.student_id = c.student_id AND c0.id < c.id AND c0.type = 1 AND c0.status > 0) AS is_renew,
                    EXISTS (SELECT 1 FROM contracts AS c2 WHERE c2.student_id = c.student_id AND c2.id > c.id AND c2.type = 1 AND c2.status > 0) AS has_next
                FROM contracts AS c WHERE c.type = 1 AND c.status > 0 AND c.branch_id IN ({$x->branch_ids})
            ) AS t" . ($x->staff_ids !== null ? " WHERE t.cm_key IN ({$x->staff_ids})" : ''));

        $blank = ['classes' => 0, 'active' => 0, 'waiting' => 0, 'reserved' => 0, 'near_end' => 0, 'ended' => 0, 'ended_renewed' => 0,
            'renew_contracts' => 0, 'renew_amount' => 0, 'present' => 0, 'absent' => 0, 'pending' => 0, 'debt' => 0, 'debt_count' => 0];
        $map = [];
        $sets = [];
        foreach ($contracts as $c) {
            $k = (int)$c->cm_key;
            if (!isset($map[$k])) { $map[$k] = $blank; $sets[$k] = ['active' => [], 'waiting' => [], 'reserved' => []]; }
            $st = (int)$c->status;
            if ($st === 6) $sets[$k]['active'][$c->student_id] = 1;
            if (in_array($st, [2, 3, 5], true)) $sets[$k]['waiting'][$c->student_id] = 1;
            if ($st === 4) $sets[$k]['reserved'][$c->student_id] = 1;
            if ($st === 6 && (int)$c->left_sessions <= 4) $map[$k]['near_end']++;
            if ($st === 7 && $c->last_date >= $x->start && $c->last_date <= $x->end) {
                $map[$k]['ended']++;
                if ((int)$c->has_next) $map[$k]['ended_renewed']++;
            }
            if ((int)$c->is_renew && substr($c->created_at, 0, 10) >= $x->start && substr($c->created_at, 0, 10) <= $x->end) {
                $map[$k]['renew_contracts']++;
                $map[$k]['renew_amount'] += $this->num($c->must_charge);
            }
            if (in_array($st, [1, 2, 3, 5, 6], true) && $this->num($c->debt_amount) > 0) {
                $map[$k]['debt'] += $this->num($c->debt_amount);
                $map[$k]['debt_count']++;
            }
        }
        foreach ($sets as $k => $s) {
            $map[$k]['active'] = count($s['active']);
            $map[$k]['waiting'] = count($s['waiting']);
            $map[$k]['reserved'] = count($s['reserved']);
        }

        $clsCond = "cl.status = 1 AND cl.branch_id IN ({$x->branch_ids}) AND (cl.cls_enddate IS NULL OR cl.cls_enddate >= '{$x->start}')"
            . ($x->staff_ids !== null ? " AND cl.cm_id IN ({$x->staff_ids})" : '');
        foreach (u::query("SELECT IFNULL(cl.cm_id, 0) AS k, COUNT(*) AS n FROM classes AS cl WHERE $clsCond GROUP BY k") as $r) {
            $k = (int)$r->k;
            if (!isset($map[$k])) $map[$k] = $blank;
            $map[$k]['classes'] = (int)$r->n;
        }
        $attCond = "cl.branch_id IN ({$x->branch_ids}) AND ss.class_date BETWEEN '{$x->start}' AND LEAST('{$x->end}', CURDATE())"
            . ($x->staff_ids !== null ? " AND cl.cm_id IN ({$x->staff_ids})" : '');
        foreach (u::query("SELECT IFNULL(cl.cm_id, 0) AS k, SUM(ss.attendance_status IN (1, 3)) AS present, SUM(ss.attendance_status = 2) AS absent, SUM(ss.attendance_status = 0) AS pending
                FROM schedule_has_student AS ss JOIN classes AS cl ON cl.id = ss.class_id WHERE $attCond GROUP BY k") as $r) {
            $k = (int)$r->k;
            if (!isset($map[$k])) $map[$k] = $blank;
            $map[$k]['present'] = (int)$r->present;
            $map[$k]['absent'] = (int)$r->absent;
            $map[$k]['pending'] = (int)$r->pending;
        }

        // Bảo đảm hiển thị đủ nhân viên CSKH trong phạm vi (kể cả chưa có số liệu)
        $staffList = u::query("SELECT u.id FROM users AS u WHERE u.status = 1 AND EXISTS (SELECT 1 FROM role_has_user AS ru WHERE ru.user_id = u.id AND ru.role_id IN (55, 56))"
            . ($x->staff_ids !== null ? " AND u.id IN ({$x->staff_ids})" : " AND EXISTS (SELECT 1 FROM branch_has_user AS bu WHERE bu.user_id = u.id AND bu.branch_id IN ({$x->branch_ids}))"));
        foreach ($staffList as $s) if (!isset($map[(int)$s->id])) $map[(int)$s->id] = $blank;

        $names = $this->namesOf('users', array_keys($map));
        $hrm = [];
        $ids = array_filter(array_map('intval', array_keys($map)));
        if ($ids) foreach (u::query("SELECT id, hrm_id FROM users WHERE id IN (" . implode(',', $ids) . ")") as $r) $hrm[(int)$r->id] = $r->hrm_id;

        $rows = [];
        foreach ($map as $k => $m) {
            $m['name'] = $k ? ($names[$k] ?? '#' . $k) : 'Chưa gán CSKH';
            $m['name_sub'] = $hrm[$k] ?? '';
            $m['renew_rate'] = $this->pct($m['ended_renewed'], $m['ended']);
            $m['attendance_rate'] = $this->pct($m['present'], $m['present'] + $m['absent']);
            $m['attendance_rate_tone'] = ($m['present'] + $m['absent']) ? $this->rateTone($m['attendance_rate']) : 'neutral';
            $rows[] = $m;
        }
        usort($rows, function ($a, $b) { return $b['active'] <=> $a['active'] ?: strcmp($a['name'], $b['name']); });

        $sum = $this->sumRows($rows, array_keys($blank));
        $sum['name'] = 'Tổng cộng';
        $sum['renew_rate'] = $this->pct($sum['ended_renewed'], $sum['ended']);
        $sum['attendance_rate'] = $this->pct($sum['present'], $sum['present'] + $sum['absent']);
        $sum['attendance_rate_tone'] = $this->rateTone($sum['attendance_rate']);

        return [
            'columns' => [
                ['key' => 'name', 'label' => 'Nhân viên CSKH', 'type' => 'text', 'sub' => 'name_sub', 'wide' => true],
                ['key' => 'classes', 'label' => 'Lớp phụ trách', 'type' => 'number'],
                ['key' => 'active', 'label' => 'HS đang học', 'type' => 'number', 'highlight' => true],
                ['key' => 'waiting', 'label' => 'Chờ xếp lớp / Đặt cọc', 'type' => 'number'],
                ['key' => 'reserved', 'label' => 'Bảo lưu', 'type' => 'number'],
                ['key' => 'near_end', 'label' => 'Sắp hết phí (≤4 buổi)', 'type' => 'number', 'tone' => 'warning'],
                ['key' => 'ended', 'label' => 'Hết phí trong kỳ', 'type' => 'number'],
                ['key' => 'ended_renewed', 'label' => 'Trong đó đã tái phí', 'type' => 'number'],
                ['key' => 'renew_rate', 'label' => '% Tái phí', 'type' => 'percent'],
                ['key' => 'renew_contracts', 'label' => 'HĐ tái phí mới', 'type' => 'number'],
                ['key' => 'renew_amount', 'label' => 'Doanh số tái phí', 'type' => 'money', 'tone' => 'success'],
                ['key' => 'attendance_rate', 'label' => '% Chuyên cần', 'type' => 'badge_percent'],
                ['key' => 'pending', 'label' => 'Lượt chưa điểm danh', 'type' => 'number'],
                ['key' => 'debt', 'label' => 'Công nợ', 'type' => 'money', 'tone' => 'danger'],
            ],
            'rows' => $rows,
            'summary' => $sum,
            'cards' => [
                ['label' => 'HS đang học', 'value' => $sum['active'], 'type' => 'number', 'tone' => 'primary'],
                ['label' => 'Sắp hết phí', 'value' => $sum['near_end'], 'type' => 'number', 'tone' => 'warning'],
                ['label' => 'Tỷ lệ tái phí', 'value' => $sum['renew_rate'], 'type' => 'percent', 'sub' => $sum['ended_renewed'] . '/' . $sum['ended'] . ' HĐ hết phí'],
                ['label' => 'Chuyên cần', 'value' => $sum['attendance_rate'], 'type' => 'percent', 'tone' => 'success'],
                ['label' => 'Công nợ', 'value' => $sum['debt'], 'type' => 'money', 'tone' => 'danger', 'sub' => $sum['debt_count'] . ' hợp đồng'],
            ],
            'note' => 'Số HS (đang học, chờ xếp lớp, bảo lưu, sắp hết phí, công nợ) là số liệu hiện tại. Hết phí / tái phí / chuyên cần tính trong khoảng ngày đã chọn. CSKH phụ trách lấy theo HĐ → HS → lớp.',
        ];
    }

    /* ======================= 08. CSKH - Sắp hết phí & tái phí ======================= */

    private function cmRenew($x)
    {
        $type = in_array($x->renew_type, ['near_end', 'ended', 'all'], true) ? $x->renew_type : 'near_end';
        $nearCond = "(c.status = 6 AND c.left_sessions <= {$x->threshold})";
        $endCond = "(c.status = 7 AND COALESCE(c.enrolment_last_date, DATE(c.updated_at)) BETWEEN '{$x->start}' AND '{$x->end}')";
        $cond = $type === 'near_end' ? $nearCond : ($type === 'ended' ? $endCond : "($nearCond OR $endCond)");
        $cmKey = $this->cmKeyExpr();
        $rows = u::query("SELECT * FROM (
                SELECT c.id, c.code, c.status, c.left_sessions, c.debt_amount, c.class_id, c.product_id, c.branch_id,
                    COALESCE(c.enrolment_last_date, DATE(c.updated_at)) AS last_date,
                    s.name AS student_name, s.lms_code, s.gud_name1, s.gud_mobile1, $cmKey AS cm_key,
                    (SELECT c2.code FROM contracts AS c2 WHERE c2.student_id = c.student_id AND c2.id > c.id AND c2.type = 1 AND c2.status > 0 ORDER BY c2.id LIMIT 1) AS next_code
                FROM contracts AS c JOIN students AS s ON s.id = c.student_id
                WHERE c.type = 1 AND c.branch_id IN ({$x->branch_ids}) AND $cond
            ) AS t" . ($x->staff_ids !== null ? " WHERE t.cm_key IN ({$x->staff_ids})" : '') . " ORDER BY t.status ASC, t.left_sessions ASC, t.last_date ASC");

        $cls = $this->namesOf('classes', array_column($rows, 'class_id'), 'cls_name');
        $prd = $this->namesOf('products', array_column($rows, 'product_id'));
        $usr = $this->namesOf('users', array_column($rows, 'cm_key'));
        $br = $this->namesOf('branches', array_column($rows, 'branch_id'));
        $out = [];
        $renewed = 0;
        $debt = 0;
        foreach ($rows as $r) {
            $isRenew = !empty($r->next_code);
            if ($isRenew) $renewed++;
            $debt += $this->num($r->debt_amount);
            $out[] = [
                'student' => $r->student_name, 'student_sub' => $r->lms_code,
                'parent' => $r->gud_mobile1, 'parent_sub' => $r->gud_name1,
                'class' => $cls[(int)$r->class_id] ?? '—', 'class_sub' => $br[(int)$r->branch_id] ?? '',
                'product' => $prd[(int)$r->product_id] ?? '—',
                'cm' => (int)$r->cm_key ? ($usr[(int)$r->cm_key] ?? '—') : 'Chưa gán',
                'contract' => $r->code,
                'left_sessions' => (int)$r->left_sessions,
                'last_date' => $r->last_date,
                'status' => u::genStatusStudent((int)$r->status, 1),
                'status_tone' => (int)$r->status === 7 ? 'neutral' : 'warning',
                'renew' => $isRenew ? 'Đã tái phí' : 'Chưa tái phí',
                'renew_sub' => $isRenew ? $r->next_code : '',
                'renew_tone' => $isRenew ? 'success' : 'danger',
                'debt' => $this->num($r->debt_amount),
            ];
        }
        $total = count($out);
        return [
            'columns' => [
                ['key' => 'student', 'label' => 'Học sinh', 'type' => 'text', 'sub' => 'student_sub', 'wide' => true],
                ['key' => 'parent', 'label' => 'SĐT phụ huynh', 'type' => 'text', 'sub' => 'parent_sub'],
                ['key' => 'class', 'label' => 'Lớp', 'type' => 'text', 'sub' => 'class_sub'],
                ['key' => 'product', 'label' => 'Sản phẩm', 'type' => 'text'],
                ['key' => 'cm', 'label' => 'CSKH', 'type' => 'text'],
                ['key' => 'contract', 'label' => 'Mã HĐ', 'type' => 'text'],
                ['key' => 'left_sessions', 'label' => 'Buổi còn lại', 'type' => 'number', 'highlight' => true],
                ['key' => 'last_date', 'label' => 'Ngày học cuối', 'type' => 'date'],
                ['key' => 'status', 'label' => 'Trạng thái', 'type' => 'badge'],
                ['key' => 'renew', 'label' => 'Tái phí', 'type' => 'badge', 'sub' => 'renew_sub'],
                ['key' => 'debt', 'label' => 'Công nợ', 'type' => 'money', 'tone' => 'danger'],
            ],
            'rows' => $out,
            'summary' => null,
            'cards' => [
                ['label' => 'Tổng học sinh', 'value' => $total, 'type' => 'number', 'tone' => 'primary'],
                ['label' => 'Đã tái phí', 'value' => $renewed, 'type' => 'number', 'tone' => 'success'],
                ['label' => 'Chưa tái phí', 'value' => $total - $renewed, 'type' => 'number', 'tone' => 'danger'],
                ['label' => 'Tỷ lệ tái phí', 'value' => $this->pct($renewed, $total), 'type' => 'percent'],
                ['label' => 'Công nợ', 'value' => $debt, 'type' => 'money', 'tone' => 'danger'],
            ],
            'note' => 'Sắp hết phí: HĐ đang học còn ≤ ' . $x->threshold . ' buổi (số liệu hiện tại, không phụ thuộc ngày). Hết phí trong kỳ: HĐ hết phí có ngày học cuối nằm trong khoảng ngày đã chọn. "Đã tái phí" = học sinh đã có HĐ chính thức mới hơn.',
        ];
    }

    /* ======================= 09. Chuyên cần theo lớp ======================= */

    private function attendanceClass($x)
    {
        $staffCond = '';
        if ($x->staff_ids !== null) {
            $s = $x->staff_ids;
            $staffCond = " AND (cl.cm_id IN ($s) OR cl.teacher_id IN ($s) OR cl.ta_id IN ($s)
                OR EXISTS (SELECT 1 FROM schedules AS sc WHERE sc.class_id = cl.id AND sc.teacher_id IN ($s) AND sc.class_date BETWEEN '{$x->start}' AND '{$x->end}'))";
        }
        $rows = u::query("SELECT cl.id, cl.cls_name, cl.branch_id, cl.teacher_id, cl.cm_id, cl.max_students,
                COUNT(DISTINCT ss.class_date) AS sessions, COUNT(DISTINCT ss.student_id) AS students,
                SUM(ss.attendance_status IN (1, 3)) AS present, SUM(ss.attendance_status = 2) AS absent, SUM(ss.attendance_status = 0) AS pending,
                (SELECT COUNT(DISTINCT c.student_id) FROM contracts AS c WHERE c.class_id = cl.id AND c.type = 1 AND c.status = 6) AS current_size
            FROM schedule_has_student AS ss JOIN classes AS cl ON cl.id = ss.class_id
            WHERE cl.branch_id IN ({$x->branch_ids}) AND ss.class_date BETWEEN '{$x->start}' AND LEAST('{$x->end}', CURDATE()) $staffCond
            GROUP BY cl.id, cl.cls_name, cl.branch_id, cl.teacher_id, cl.cm_id, cl.max_students
            ORDER BY cl.cls_name");
        $br = $this->namesOf('branches', array_column($rows, 'branch_id'));
        $usr = $this->namesOf('users', array_merge(array_column($rows, 'teacher_id'), array_column($rows, 'cm_id')));
        $out = [];
        foreach ($rows as $r) {
            $present = (int)$r->present; $absent = (int)$r->absent;
            $rate = $this->pct($present, $present + $absent);
            $out[] = [
                'class' => $r->cls_name, 'class_sub' => $br[(int)$r->branch_id] ?? '',
                'teacher' => $usr[(int)$r->teacher_id] ?? '—',
                'cm' => $usr[(int)$r->cm_id] ?? '—',
                'current_size' => (int)$r->current_size, 'current_size_sub' => (int)$r->max_students ? 'Tối đa ' . (int)$r->max_students : '',
                'sessions' => (int)$r->sessions,
                'students' => (int)$r->students,
                'present' => $present, 'absent' => $absent, 'pending' => (int)$r->pending,
                'rate' => $rate, 'rate_tone' => ($present + $absent) ? $this->rateTone($rate) : 'neutral',
            ];
        }
        usort($out, function ($a, $b) { return $a['rate'] <=> $b['rate']; });
        $sum = $this->sumRows($out, ['current_size', 'sessions', 'students', 'present', 'absent', 'pending']);
        $sum['class'] = 'Tổng cộng (' . count($out) . ' lớp)';
        $sum['rate'] = $this->pct($sum['present'], $sum['present'] + $sum['absent']);
        $sum['rate_tone'] = $this->rateTone($sum['rate']);
        return [
            'columns' => [
                ['key' => 'class', 'label' => 'Lớp', 'type' => 'text', 'sub' => 'class_sub', 'wide' => true],
                ['key' => 'teacher', 'label' => 'Giáo viên', 'type' => 'text'],
                ['key' => 'cm', 'label' => 'CSKH', 'type' => 'text'],
                ['key' => 'current_size', 'label' => 'Sĩ số hiện tại', 'type' => 'number', 'sub' => 'current_size_sub'],
                ['key' => 'sessions', 'label' => 'Buổi đã học', 'type' => 'number'],
                ['key' => 'students', 'label' => 'HS có lịch', 'type' => 'number'],
                ['key' => 'present', 'label' => 'Lượt có mặt', 'type' => 'number', 'tone' => 'success'],
                ['key' => 'absent', 'label' => 'Lượt vắng', 'type' => 'number', 'tone' => 'danger'],
                ['key' => 'pending', 'label' => 'Chưa điểm danh', 'type' => 'number', 'tone' => 'warning'],
                ['key' => 'rate', 'label' => '% Chuyên cần', 'type' => 'badge_percent'],
            ],
            'rows' => $out,
            'summary' => $sum,
            'cards' => [
                ['label' => 'Số lớp', 'value' => count($out), 'type' => 'number', 'tone' => 'primary'],
                ['label' => 'Chuyên cần TB', 'value' => $sum['rate'], 'type' => 'percent', 'tone' => 'success'],
                ['label' => 'Lượt vắng', 'value' => $sum['absent'], 'type' => 'number', 'tone' => 'danger'],
                ['label' => 'Lượt chưa điểm danh', 'value' => $sum['pending'], 'type' => 'number', 'tone' => 'warning'],
            ],
            'note' => 'Chỉ tính các buổi đến hết hôm nay. % Chuyên cần = có mặt / (có mặt + vắng), không tính lượt chưa điểm danh. Lớp xếp theo chuyên cần tăng dần.',
        ];
    }

    /* ======================= 10. Giáo viên - Buổi dạy & nhận xét ======================= */

    private function teacherWorkload($x)
    {
        $sessions = u::query("SELECT s.id, s.class_id, s.class_date, COALESCE(NULLIF(s.teacher_id, 0), cl.teacher_id, 0) AS tid, IFNULL(cl.ta_id, 0) AS ta_id,
                EXISTS (SELECT 1 FROM schedule_has_student AS ss WHERE ss.class_id = s.class_id AND ss.class_date = s.class_date AND ss.attendance_status = 0) AS pending_att
            FROM schedules AS s JOIN classes AS cl ON cl.id = s.class_id
            WHERE cl.branch_id IN ({$x->branch_ids}) AND s.class_date BETWEEN '{$x->start}' AND LEAST('{$x->end}', CURDATE())");

        $monday = function ($d) { $t = strtotime($d); return date('Y-m-d', strtotime('-' . (date('N', $t) - 1) . ' days', $t)); };
        // Phiếu nhận xét tuần theo lớp
        $reports = [];
        $classIds = array_unique(array_map('intval', array_column($sessions, 'class_id')));
        if ($classIds) {
            $from = date('Y-m-d', strtotime($x->start . ' -7 days'));
            foreach (u::query("SELECT class_id, class_date, week_start, status FROM lesson_reports WHERE class_id IN (" . implode(',', $classIds) . ") AND class_date BETWEEN '$from' AND '{$x->end}'") as $r) {
                $wk = $r->week_start ? $monday($r->week_start) : $monday($r->class_date);
                $reports[(int)$r->class_id . '|' . $wk] = max($reports[(int)$r->class_id . '|' . $wk] ?? -1, (int)$r->status);
            }
        }

        $allow = $x->staff_ids !== null ? array_flip(array_map('intval', explode(',', $x->staff_ids))) : null;
        $blank = ['classes' => [], 'main' => 0, 'ta' => 0, 'pending_att' => 0, 'weeks' => [], 'weeks_due' => 0, 'done' => 0, 'sent' => 0];
        $map = [];
        $today = $x->today;
        foreach ($sessions as $s) {
            $tid = (int)$s->tid;
            $ta = (int)$s->ta_id;
            if ($tid && ($allow === null || isset($allow[$tid]))) {
                if (!isset($map[$tid])) $map[$tid] = $blank;
                $map[$tid]['classes'][(int)$s->class_id] = 1;
                $map[$tid]['main']++;
                if ((int)$s->pending_att) $map[$tid]['pending_att']++;
                $map[$tid]['weeks'][(int)$s->class_id . '|' . $monday($s->class_date)] = 1;
            }
            if ($ta && $ta !== $tid && ($allow === null || isset($allow[$ta]))) {
                if (!isset($map[$ta])) $map[$ta] = $blank;
                $map[$ta]['classes'][(int)$s->class_id] = 1;
                $map[$ta]['ta']++;
            }
        }
        foreach ($map as $k => $m) {
            foreach (array_keys($m['weeks']) as $wkKey) {
                list(, $wk) = explode('|', $wkKey);
                $sunday = date('Y-m-d', strtotime($wk . ' +6 days'));
                if ($sunday >= $today) continue; // tuần chưa kết thúc → chưa bắt buộc
                $map[$k]['weeks_due']++;
                $st = $reports[$wkKey] ?? -1;
                if ($st >= 1) $map[$k]['done']++;
                if ($st >= 2) $map[$k]['sent']++;
            }
        }

        $names = $this->namesOf('users', array_keys($map));
        $roleMap = [];
        $ids = array_filter(array_map('intval', array_keys($map)));
        if ($ids) {
            foreach (u::query("SELECT ru.user_id, GROUP_CONCAT(r.name SEPARATOR ', ') AS roles FROM role_has_user AS ru JOIN roles AS r ON r.id = ru.role_id
                    WHERE ru.user_id IN (" . implode(',', $ids) . ") AND ru.role_id IN (36, 37, 54) GROUP BY ru.user_id") as $r) $roleMap[(int)$r->user_id] = $r->roles;
        }
        $rows = [];
        foreach ($map as $k => $m) {
            $rate = $this->pct($m['done'], $m['weeks_due']);
            $rows[] = [
                'name' => $names[$k] ?? '#' . $k, 'name_sub' => $roleMap[$k] ?? '',
                'classes' => count($m['classes']), 'main' => $m['main'], 'ta' => $m['ta'],
                'total' => $m['main'] + $m['ta'], 'pending_att' => $m['pending_att'],
                'weeks_due' => $m['weeks_due'], 'done' => $m['done'], 'missing' => $m['weeks_due'] - $m['done'], 'sent' => $m['sent'],
                'rate' => $rate, 'rate_tone' => $m['weeks_due'] ? $this->rateTone($rate) : 'neutral',
            ];
        }
        usort($rows, function ($a, $b) { return $b['total'] <=> $a['total']; });
        $sum = $this->sumRows($rows, ['classes', 'main', 'ta', 'total', 'pending_att', 'weeks_due', 'done', 'missing', 'sent']);
        $sum['name'] = 'Tổng cộng';
        $sum['rate'] = $this->pct($sum['done'], $sum['weeks_due']);
        $sum['rate_tone'] = $this->rateTone($sum['rate']);
        return [
            'columns' => [
                ['key' => 'name', 'label' => 'Giáo viên / Trợ giảng', 'type' => 'text', 'sub' => 'name_sub', 'wide' => true],
                ['key' => 'classes', 'label' => 'Số lớp', 'type' => 'number'],
                ['key' => 'main', 'label' => 'Buổi dạy chính', 'type' => 'number', 'highlight' => true],
                ['key' => 'ta', 'label' => 'Buổi trợ giảng', 'type' => 'number'],
                ['key' => 'total', 'label' => 'Tổng buổi', 'type' => 'number'],
                ['key' => 'pending_att', 'label' => 'Buổi chưa điểm danh xong', 'type' => 'number', 'tone' => 'warning'],
                ['key' => 'weeks_due', 'label' => 'Tuần cần nhận xét', 'type' => 'number'],
                ['key' => 'done', 'label' => 'Đã hoàn thành', 'type' => 'number', 'tone' => 'success'],
                ['key' => 'missing', 'label' => 'Còn thiếu', 'type' => 'number', 'tone' => 'danger'],
                ['key' => 'sent', 'label' => 'Đã gửi PH', 'type' => 'number'],
                ['key' => 'rate', 'label' => '% Hoàn thành NX', 'type' => 'badge_percent'],
            ],
            'rows' => $rows,
            'summary' => $sum,
            'cards' => [
                ['label' => 'Tổng buổi dạy', 'value' => $sum['total'], 'type' => 'number', 'tone' => 'primary'],
                ['label' => 'Buổi chưa điểm danh xong', 'value' => $sum['pending_att'], 'type' => 'number', 'tone' => 'warning'],
                ['label' => 'Phiếu NX còn thiếu', 'value' => $sum['missing'], 'type' => 'number', 'tone' => 'danger'],
                ['label' => '% Hoàn thành nhận xét', 'value' => $sum['rate'], 'type' => 'percent', 'tone' => 'success'],
            ],
            'note' => 'Buổi dạy tính đến hết hôm nay; GV buổi lấy theo lịch học, nếu trống lấy GV của lớp. "Tuần cần nhận xét" chỉ tính các tuần (T2–CN) đã kết thúc mà GV có dạy; hoàn thành = phiếu tuần ở trạng thái Hoàn thành hoặc Đã gửi.',
        ];
    }

    /* ======================= 11. Kế toán - Sổ thu tiền ======================= */

    private function financeReceipts($x)
    {
        $cond = "p.charge_date BETWEEN '{$x->start}' AND '{$x->end}' AND p.branch_id IN ({$x->branch_ids})"
            . ($x->method !== null ? " AND p.method = {$x->method}" : '')
            . ($x->product_id ? " AND c.product_id = {$x->product_id}" : '');
        $rows = u::query("SELECT p.id, p.code, p.charge_date, p.amount, p.method, p.branch_id, p.creator_id, p.note,
                c.code AS contract_code, c.product_id, c.ec_id, c.must_charge, c.debt_amount, s.name AS student_name, s.lms_code
            FROM payments AS p LEFT JOIN contracts AS c ON c.id = p.contract_id LEFT JOIN students AS s ON s.id = COALESCE(p.student_id, c.student_id)
            WHERE $cond ORDER BY p.charge_date DESC, p.id DESC");
        $br = $this->namesOf('branches', array_column($rows, 'branch_id'));
        $prd = $this->namesOf('products', array_column($rows, 'product_id'));
        $usr = $this->namesOf('users', array_merge(array_column($rows, 'ec_id'), array_column($rows, 'creator_id')));
        $out = [];
        $byMethod = [];
        $total = 0;
        foreach ($rows as $r) {
            $amt = $this->num($r->amount);
            $total += $amt;
            $m = (int)$r->method;
            $byMethod[$m] = ($byMethod[$m] ?? 0) + $amt;
            $out[] = [
                'charge_date' => $r->charge_date,
                'code' => $r->code ?: ('#' . $r->id),
                'student' => $r->student_name ?: '—', 'student_sub' => $r->lms_code,
                'contract' => $r->contract_code ?: '—',
                'product' => $prd[(int)$r->product_id] ?? '—',
                'branch' => $br[(int)$r->branch_id] ?? '—',
                'method' => self::METHODS[$m] ?? 'Khác', 'method_tone' => $m === 0 ? 'warning' : 'info',
                'amount' => $amt,
                'debt' => $this->num($r->debt_amount),
                'ec' => $usr[(int)$r->ec_id] ?? '—',
                'creator' => $usr[(int)$r->creator_id] ?? '—',
                'note' => (string)$r->note,
            ];
        }
        $cards = [['label' => 'Tổng thực thu', 'value' => $total, 'type' => 'money', 'tone' => 'success', 'sub' => count($out) . ' phiếu thu']];
        foreach (self::METHODS as $k => $label) {
            if (isset($byMethod[$k])) $cards[] = ['label' => $label, 'value' => $byMethod[$k], 'type' => 'money', 'sub' => $this->pct($byMethod[$k], $total) . '%'];
        }
        return [
            'columns' => [
                ['key' => 'charge_date', 'label' => 'Ngày thu', 'type' => 'date'],
                ['key' => 'code', 'label' => 'Mã phiếu', 'type' => 'text'],
                ['key' => 'student', 'label' => 'Học sinh', 'type' => 'text', 'sub' => 'student_sub', 'wide' => true],
                ['key' => 'contract', 'label' => 'Mã HĐ', 'type' => 'text'],
                ['key' => 'product', 'label' => 'Sản phẩm', 'type' => 'text'],
                ['key' => 'branch', 'label' => 'Cơ sở', 'type' => 'text', 'branch' => true],
                ['key' => 'method', 'label' => 'Hình thức', 'type' => 'badge'],
                ['key' => 'amount', 'label' => 'Số tiền thu', 'type' => 'money', 'tone' => 'success', 'highlight' => true],
                ['key' => 'debt', 'label' => 'Còn nợ HĐ', 'type' => 'money', 'tone' => 'danger'],
                ['key' => 'ec', 'label' => 'Sale hưởng DS', 'type' => 'text'],
                ['key' => 'creator', 'label' => 'Người lập', 'type' => 'text'],
                ['key' => 'note', 'label' => 'Ghi chú', 'type' => 'text'],
            ],
            'rows' => $out,
            'summary' => ['charge_date' => 'Tổng cộng', 'amount' => $total],
            'cards' => $cards,
            'note' => 'Phiếu thu đã duyệt (bảng payments) theo ngày thu. "Còn nợ HĐ" là công nợ hiện tại của hợp đồng.',
        ];
    }

    /* ======================= 12. Kế toán - Công nợ ======================= */

    private function financeDebt($x)
    {
        $cond = "c.type = 1 AND c.status IN (" . self::ACTIVE_CONTRACT_STATUSES . ") AND c.debt_amount > 0 AND c.branch_id IN ({$x->branch_ids})"
            . ($x->product_id ? " AND c.product_id = {$x->product_id}" : '');
        $rows = u::query("SELECT c.id, c.code, c.status, c.created_at, c.must_charge, c.total_charged, c.debt_amount, c.branch_id, c.product_id, c.ec_id, c.cm_id,
                s.name AS student_name, s.lms_code, s.gud_mobile1, s.gud_name1, DATEDIFF(CURDATE(), DATE(c.created_at)) AS days,
                (SELECT MAX(p.charge_date) FROM payments AS p WHERE p.contract_id = c.id) AS last_paid
            FROM contracts AS c JOIN students AS s ON s.id = c.student_id WHERE $cond ORDER BY c.debt_amount DESC");
        $br = $this->namesOf('branches', array_column($rows, 'branch_id'));
        $prd = $this->namesOf('products', array_column($rows, 'product_id'));
        $usr = $this->namesOf('users', array_merge(array_column($rows, 'ec_id'), array_column($rows, 'cm_id')));
        $out = [];
        $buckets = ['b30' => 0, 'b60' => 0, 'bmore' => 0];
        $sum = ['must_charge' => 0, 'total_charged' => 0, 'debt' => 0];
        foreach ($rows as $r) {
            $debt = $this->num($r->debt_amount);
            $days = (int)$r->days;
            if ($days <= 30) $buckets['b30'] += $debt; elseif ($days <= 60) $buckets['b60'] += $debt; else $buckets['bmore'] += $debt;
            $sum['must_charge'] += $this->num($r->must_charge);
            $sum['total_charged'] += $this->num($r->total_charged);
            $sum['debt'] += $debt;
            $out[] = [
                'contract' => $r->code, 'contract_sub' => 'Tạo ' . date('d/m/Y', strtotime($r->created_at)),
                'student' => $r->student_name, 'student_sub' => $r->lms_code,
                'parent' => $r->gud_mobile1, 'parent_sub' => $r->gud_name1,
                'branch' => $br[(int)$r->branch_id] ?? '—',
                'product' => $prd[(int)$r->product_id] ?? '—',
                'ec' => $usr[(int)$r->ec_id] ?? '—',
                'cm' => $usr[(int)$r->cm_id] ?? '—',
                'must_charge' => $this->num($r->must_charge),
                'total_charged' => $this->num($r->total_charged),
                'debt' => $debt,
                'paid_rate' => $this->pct($this->num($r->total_charged), $this->num($r->must_charge)),
                'last_paid' => $r->last_paid,
                'days' => $days,
                'status' => u::genStatusStudent((int)$r->status, 1),
                'status_tone' => (int)$r->status === 6 ? 'success' : ((int)$r->status === 2 ? 'warning' : 'info'),
            ];
        }
        $sum['contract'] = 'Tổng cộng (' . count($out) . ' HĐ)';
        $sum['paid_rate'] = $this->pct($sum['total_charged'], $sum['must_charge']);
        return [
            'columns' => [
                ['key' => 'contract', 'label' => 'Mã HĐ', 'type' => 'text', 'sub' => 'contract_sub'],
                ['key' => 'student', 'label' => 'Học sinh', 'type' => 'text', 'sub' => 'student_sub', 'wide' => true],
                ['key' => 'parent', 'label' => 'SĐT phụ huynh', 'type' => 'text', 'sub' => 'parent_sub'],
                ['key' => 'branch', 'label' => 'Cơ sở', 'type' => 'text', 'branch' => true],
                ['key' => 'product', 'label' => 'Sản phẩm', 'type' => 'text'],
                ['key' => 'ec', 'label' => 'Sale', 'type' => 'text'],
                ['key' => 'cm', 'label' => 'CSKH', 'type' => 'text'],
                ['key' => 'must_charge', 'label' => 'Phải thu', 'type' => 'money'],
                ['key' => 'total_charged', 'label' => 'Đã thu', 'type' => 'money', 'tone' => 'success'],
                ['key' => 'debt', 'label' => 'Còn nợ', 'type' => 'money', 'tone' => 'danger', 'highlight' => true],
                ['key' => 'paid_rate', 'label' => '% Đã thu', 'type' => 'percent'],
                ['key' => 'last_paid', 'label' => 'Lần thu gần nhất', 'type' => 'date'],
                ['key' => 'days', 'label' => 'Số ngày từ khi tạo HĐ', 'type' => 'number'],
                ['key' => 'status', 'label' => 'Trạng thái', 'type' => 'badge'],
            ],
            'rows' => $out,
            'summary' => $sum,
            'cards' => [
                ['label' => 'Tổng công nợ', 'value' => $sum['debt'], 'type' => 'money', 'tone' => 'danger', 'sub' => count($out) . ' hợp đồng'],
                ['label' => 'Nợ ≤ 30 ngày', 'value' => $buckets['b30'], 'type' => 'money'],
                ['label' => 'Nợ 31 – 60 ngày', 'value' => $buckets['b60'], 'type' => 'money', 'tone' => 'warning'],
                ['label' => 'Nợ > 60 ngày', 'value' => $buckets['bmore'], 'type' => 'money', 'tone' => 'danger'],
            ],
            'note' => 'Số liệu công nợ hiện tại của các hợp đồng chính thức chưa kết thúc (không gồm bảo lưu / hết phí). Tuổi nợ tính từ ngày tạo hợp đồng.',
        ];
    }
}
