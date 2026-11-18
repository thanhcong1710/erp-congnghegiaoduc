<?php

namespace App\Http\Controllers;

use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Dashboard theo vai trò (dữ liệu thật).
 * - Admin: xem tất cả tab; mỗi tab chọn được tài khoản bất kỳ thuộc role đó (tab "Toàn hệ thống" chọn cơ sở).
 * - Role khác: chỉ thấy tab của role mình; trưởng nhóm chọn được thành viên trong team, còn lại cố định là chính mình.
 *
 * Quy ước dữ liệu:
 * - Thực thu = payments (phiếu thu đã duyệt) theo charge_date; người hưởng doanh số = contracts.ec_id.
 * - HS đang học = contracts.type = 1 AND status = 6; công nợ = contracts.debt_amount của HĐ chưa kết thúc.
 * - Lead = crm_parents; trạng thái theo u::getStatusParent (8 hẹn checkin, 9 đã checkin, 10 đã mua gói, 12 DS đen).
 */
class RoleDashboardController extends Controller
{
    const ROLE_ADMIN = 999999;

    /** key => [label, icon, roles, leader roles] */
    const TABS = [
        'system' => ['label' => 'Toàn hệ thống', 'icon' => 'public', 'roles' => [999999], 'leaders' => []],
        'ceo' => ['label' => 'Giám đốc trung tâm', 'icon' => 'store', 'roles' => [686868], 'leaders' => []],
        'sale' => ['label' => 'Sale', 'icon' => 'shopping_cart', 'roles' => [68, 69], 'leaders' => [69]],
        'cm' => ['label' => 'Chăm sóc khách hàng', 'icon' => 'favorite', 'roles' => [55, 56], 'leaders' => [56]],
        'teacher' => ['label' => 'Giáo viên', 'icon' => 'school', 'roles' => [36, 37, 54], 'leaders' => [37]],
        'accountant' => ['label' => 'Kế toán', 'icon' => 'account_balance_wallet', 'roles' => [1000002], 'leaders' => []],
    ];

    const ACTIVE_CONTRACT_STATUSES = '1,2,3,5,6'; // chưa kết thúc (trừ bảo lưu 4, hết phí 7)

    /* ======================= Phân quyền tab / tài khoản ======================= */

    private function rolesOf($userId)
    {
        return array_map(function ($r) {
            return (int)$r->role_id;
        }, DB::select("SELECT role_id FROM role_has_user WHERE user_id = ?", [(int)$userId]));
    }

    private function isAdmin()
    {
        return in_array(self::ROLE_ADMIN, $this->rolesOf(Auth::user()->id), true);
    }

    private function usersWithRoles(array $roles, $onlyIds = null, $withTeaching = false)
    {
        $in = implode(',', array_map('intval', $roles));
        $cond = $onlyIds !== null ? " AND u.id IN (" . (implode(',', array_map('intval', $onlyIds)) ?: '0') . ")" : '';
        // Giáo viên: gồm cả người đang được gán dạy / trợ giảng ở lớp đang hoạt động (dù chưa gán role)
        $roleCond = "EXISTS (SELECT 1 FROM role_has_user AS ru WHERE ru.user_id = u.id AND ru.role_id IN ($in))";
        if ($withTeaching) {
            $roleCond = "($roleCond OR EXISTS (SELECT 1 FROM classes AS cl WHERE cl.status = 1 AND (cl.teacher_id = u.id OR cl.ta_id = u.id)))";
        }
        return u::query("SELECT u.id, u.name, u.hrm_id,
                (SELECT GROUP_CONCAT(DISTINCT b.name SEPARATOR ', ') FROM branch_has_user AS bu JOIN branches AS b ON b.id = bu.branch_id WHERE bu.user_id = u.id) AS branch_name
            FROM users AS u WHERE u.status = 1 AND $roleCond $cond ORDER BY u.name");
    }

    /** Danh sách tab được xem + tài khoản được chọn trong từng tab */
    private function tabsFor()
    {
        $me = Auth::user();
        $myId = (int)$me->id;
        $myRoles = $this->rolesOf($myId);
        $admin = in_array(self::ROLE_ADMIN, $myRoles, true);
        $tabs = [];
        foreach (self::TABS as $key => $t) {
            $hasRole = (bool)array_intersect($myRoles, $t['roles']);
            if ($key === 'system' && !$admin) continue;
            if (!$admin && !$hasRole) continue;

            $accounts = [];
            if ($key !== 'system') {
                $teaching = $key === 'teacher';
                if ($admin) {
                    $accounts = $this->usersWithRoles($t['roles'], null, $teaching);
                } elseif (array_intersect($myRoles, $t['leaders'])) {
                    $team = array_map('intval', explode(',', $me->getStaffHasUser($myId)));
                    $accounts = $this->usersWithRoles($t['roles'], $team, $teaching);
                } else {
                    $accounts = $this->usersWithRoles($t['roles'], [$myId], $teaching);
                }
            }
            $ids = array_map(function ($a) {
                return (int)$a->id;
            }, $accounts);
            $tabs[] = [
                'key' => $key, 'label' => $t['label'], 'icon' => $t['icon'],
                'accounts' => $accounts,
                'default_id' => in_array($myId, $ids, true) ? $myId : ($ids[0] ?? null),
                'can_switch' => count($accounts) > 1,
            ];
        }
        return $tabs;
    }

    public function init(Request $request)
    {
        $tabs = $this->tabsFor();
        $branches = u::query("SELECT id, name FROM branches WHERE status = 1 AND id IN (" . $this->myBranchIds() . ") ORDER BY name");
        return response()->json((object)['status' => 1, 'message' => '', 'data' => [
            'is_admin' => $this->isAdmin(),
            'tabs' => $tabs,
            'branches' => $branches,
            'default_tab' => $tabs ? $tabs[0]['key'] : null,
        ]]);
    }

    /* ======================= Helpers ======================= */

    private function myBranchIds()
    {
        return implode(',', array_filter(array_map('intval', explode(',', (string)Auth::user()->getBranchesHasUser())))) ?: '0';
    }

    private function branchIdsOf($userId)
    {
        $ids = array_map(function ($r) {
            return (int)$r->branch_id;
        }, DB::select("SELECT branch_id FROM branch_has_user WHERE user_id = ?", [(int)$userId]));
        return implode(',', array_filter($ids)) ?: '0';
    }

    private function teamIds($userId, array $leaderRoles)
    {
        if (array_intersect($this->rolesOf($userId), $leaderRoles)) {
            $ids = array_filter(array_map('intval', explode(',', Auth::user()->getStaffHasUser((int)$userId))));
            return implode(',', $ids) ?: (string)(int)$userId;
        }
        return (string)(int)$userId;
    }

    private function monthRange($month)
    {
        if (!is_string($month) || !preg_match('/^\d{4}-\d{2}$/', $month)) $month = date('Y-m');
        $start = $month . '-01';
        $end = date('Y-m-t', strtotime($start));
        $prevStart = date('Y-m-01', strtotime($start . ' -1 month'));
        $prevEnd = date('Y-m-t', strtotime($prevStart));
        return [$start, $end, $prevStart, $prevEnd, (int)date('Y', strtotime($start)), (int)date('n', strtotime($start))];
    }

    private function num($v)
    {
        return (float)($v ?? 0);
    }

    private function pct($a, $b)
    {
        return $b > 0 ? round($a * 100 / $b, 1) : null;
    }

    private function parentStatusLabels()
    {
        $out = [];
        for ($i = 1; $i <= 12; $i++) $out[$i] = u::getStatusParent($i);
        return $out;
    }

    /** Thực thu theo tháng trong N tháng gần nhất ($cond áp lên payments p / contracts c) */
    private function revenueTrend($end, $cond, $months = 6)
    {
        $from = date('Y-m-01', strtotime($end . ' -' . ($months - 1) . ' months'));
        $rows = u::query("SELECT DATE_FORMAT(p.charge_date, '%Y-%m') AS m, SUM(p.amount) AS total
            FROM payments AS p LEFT JOIN contracts AS c ON c.id = p.contract_id
            WHERE p.charge_date BETWEEN '$from' AND '$end' AND $cond GROUP BY m");
        $map = [];
        foreach ($rows as $r) $map[$r->m] = $this->num($r->total);
        $labels = []; $values = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $m = date('Y-m', strtotime($end . ' -' . $i . ' months'));
            $labels[] = date('m/Y', strtotime($m . '-01'));
            $values[] = $map[$m] ?? 0;
        }
        return ['labels' => $labels, 'values' => $values];
    }

    /** Thực thu theo ngày trong tháng */
    private function revenueDaily($start, $end, $cond)
    {
        $rows = u::query("SELECT DATE(p.charge_date) AS d, SUM(p.amount) AS total
            FROM payments AS p LEFT JOIN contracts AS c ON c.id = p.contract_id
            WHERE p.charge_date BETWEEN '$start' AND '$end' AND $cond GROUP BY d");
        $map = [];
        foreach ($rows as $r) $map[$r->d] = $this->num($r->total);
        $labels = []; $values = []; $acc = 0;
        for ($d = strtotime($start); $d <= strtotime($end); $d += 86400) {
            $k = date('Y-m-d', $d);
            $acc += $map[$k] ?? 0;
            $labels[] = date('d/m', $d);
            $values[] = $acc; // luỹ kế
        }
        return ['labels' => $labels, 'values' => $values];
    }

    /** Phễu chuyển đổi của tập lead (theo trạng thái hiện tại) */
    private function funnel($parentCond)
    {
        $r = u::first("SELECT COUNT(*) AS total,
                SUM(p.status NOT IN (1)) AS contacted,
                SUM(p.status IN (7, 8, 9, 10)) AS interested,
                SUM(p.status IN (8, 9, 10)) AS booked,
                SUM(p.status IN (9, 10)) AS checked_in,
                SUM(p.status = 10) AS bought
            FROM crm_parents AS p WHERE $parentCond");
        return [
            'labels' => ['Data mới', 'Đã liên hệ', 'Quan tâm', 'Hẹn checkin', 'Đã checkin', 'Mua gói phí'],
            'values' => [(int)$r->total, (int)$r->contacted, (int)$r->interested, (int)$r->booked, (int)$r->checked_in, (int)$r->bought],
        ];
    }

    private function leadsBySource($parentCond)
    {
        $rows = u::query("SELECT COALESCE(s.name, 'Chưa có nguồn') AS name, COUNT(*) AS total
            FROM crm_parents AS p LEFT JOIN sources AS s ON s.id = p.source_id WHERE $parentCond GROUP BY s.name ORDER BY total DESC");
        return ['labels' => array_column($rows, 'name'), 'values' => array_map('intval', array_column($rows, 'total'))];
    }

    /** HS sắp hết phí (còn ≤ 4 buổi) */
    private function nearEndStudents($contractCond, $limit = 15)
    {
        return u::query("SELECT c.id AS contract_id, c.student_id, s.name, s.lms_code, c.left_sessions, c.debt_amount,
                (SELECT cls_name FROM classes WHERE id = c.class_id) AS cls_name,
                (SELECT name FROM users WHERE id = c.cm_id) AS cm_name, s.gud_mobile1
            FROM contracts AS c JOIN students AS s ON s.id = c.student_id
            WHERE c.type = 1 AND c.status = 6 AND c.left_sessions BETWEEN 0 AND 4 AND $contractCond
            ORDER BY c.left_sessions ASC, s.name LIMIT " . (int)$limit);
    }

    /** HS vắng 2 buổi gần nhất liên tiếp (trong 45 ngày) */
    private function absentStreak($shsCond, $limit = 15)
    {
        $rows = u::query("SELECT ss.student_id, ss.class_id, ss.class_date, ss.attendance_status
            FROM schedule_has_student AS ss JOIN classes AS cl ON cl.id = ss.class_id
            WHERE ss.class_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 45 DAY) AND CURDATE() AND ss.attendance_status IN (1, 2, 3) AND $shsCond
            ORDER BY ss.student_id, ss.class_date DESC");
        $by = [];
        foreach ($rows as $r) $by[$r->student_id][] = $r;
        $hit = [];
        foreach ($by as $sid => $list) {
            $streak = 0;
            foreach ($list as $x) {
                if ((int)$x->attendance_status === 2) $streak++; else break;
            }
            if ($streak >= 2) $hit[$sid] = ['streak' => $streak, 'class_id' => (int)$list[0]->class_id, 'last_date' => $list[0]->class_date];
        }
        if (!$hit) return [];
        uasort($hit, function ($a, $b) { return $b['streak'] - $a['streak']; });
        $hit = array_slice($hit, 0, $limit, true);
        $info = [];
        foreach (u::query("SELECT id, name, lms_code, gud_mobile1 FROM students WHERE id IN (" . implode(',', array_map('intval', array_keys($hit))) . ")") as $s) $info[$s->id] = $s;
        $cls = [];
        foreach (u::query("SELECT id, cls_name FROM classes WHERE id IN (" . implode(',', array_unique(array_column($hit, 'class_id'))) . ")") as $c) $cls[$c->id] = $c->cls_name;
        $out = [];
        foreach ($hit as $sid => $h) {
            $s = $info[$sid] ?? null;
            if (!$s) continue;
            $out[] = ['student_id' => (int)$sid, 'name' => $s->name, 'lms_code' => $s->lms_code, 'gud_mobile1' => $s->gud_mobile1,
                'streak' => $h['streak'], 'last_date' => $h['last_date'], 'cls_name' => $cls[$h['class_id']] ?? ''];
        }
        return $out;
    }

    /** Sinh nhật học sinh trong 7 ngày tới (bỏ qua HS chỉ biết năm sinh) */
    private function birthdays($studentCond, $limit = 15)
    {
        return u::query("SELECT s.id AS student_id, s.name, s.date_of_birth,
                (SELECT cls_name FROM classes WHERE id = c.class_id) AS cls_name
            FROM students AS s JOIN contracts AS c ON c.student_id = s.id AND c.type = 1 AND c.status = 6
            WHERE s.date_of_birth IS NOT NULL AND IFNULL(s.birthday_year_only, 0) = 0 AND $studentCond
              AND DATEDIFF(DATE_ADD(s.date_of_birth, INTERVAL (YEAR(CURDATE()) - YEAR(s.date_of_birth) + IF(DATE_FORMAT(CURDATE(), '%m%d') > DATE_FORMAT(s.date_of_birth, '%m%d'), 1, 0)) YEAR), CURDATE()) BETWEEN 0 AND 7
            GROUP BY s.id, s.name, s.date_of_birth, c.class_id
            ORDER BY DATE_FORMAT(s.date_of_birth, '%m%d') LIMIT " . (int)$limit);
    }

    private function debtContracts($contractCond, $limit = 10)
    {
        return u::query("SELECT c.id AS contract_id, c.code, c.student_id, s.name, c.must_charge, c.total_charged, c.debt_amount,
                (SELECT name FROM users WHERE id = c.ec_id) AS ec_name, (SELECT name FROM branches WHERE id = c.branch_id) AS branch_name
            FROM contracts AS c JOIN students AS s ON s.id = c.student_id
            WHERE c.type = 1 AND c.status IN (" . self::ACTIVE_CONTRACT_STATUSES . ") AND c.debt_amount > 0 AND $contractCond
            ORDER BY c.debt_amount DESC LIMIT " . (int)$limit);
    }

    /* ======================= Data endpoint ======================= */

    public function data(Request $request)
    {
        $tabKey = (string)$request->input('tab');
        $tabs = $this->tabsFor();
        $tab = null;
        foreach ($tabs as $t) if ($t['key'] === $tabKey) $tab = $t;
        if (!$tab) {
            return response()->json((object)['status' => 0, 'message' => 'Bạn không có quyền xem dashboard này', 'data' => null]);
        }
        $userId = (int)$request->input('user_id') ?: (int)$tab['default_id'];
        // Admin xem tab Kế toán / GĐ trung tâm khi chưa có tài khoản thuộc role đó → theo cơ sở admin quản lý
        $noAccount = !$tab['accounts'] && $this->isAdmin() && in_array($tabKey, ['accountant', 'ceo'], true);
        if ($tabKey !== 'system' && !$tab['accounts'] && !$noAccount) {
            return response()->json((object)['status' => 0, 'message' => 'Chưa có tài khoản nào thuộc vai trò này', 'data' => null]);
        }
        if ($tabKey !== 'system' && !$noAccount) {
            $allowed = array_map(function ($a) { return (int)$a->id; }, $tab['accounts']);
            if (!in_array($userId, $allowed, true)) {
                return response()->json((object)['status' => 0, 'message' => 'Không có quyền xem dashboard của tài khoản này', 'data' => null]);
            }
        }
        $range = $this->monthRange((string)$request->input('month'));

        switch ($tabKey) {
            case 'system':
                $branchId = (int)$request->input('branch_id');
                $branchIds = $branchId && in_array((string)$branchId, explode(',', $this->myBranchIds()), true) ? (string)$branchId : $this->myBranchIds();
                $data = $this->overview($branchIds, $range, true);
                break;
            case 'ceo':
                $data = $this->overview($noAccount ? $this->myBranchIds() : $this->branchIdsOf($userId), $range, false);
                break;
            case 'sale':
                $data = $this->sale($userId, $range);
                break;
            case 'cm':
                $data = $this->cm($userId, $range);
                break;
            case 'accountant':
                $data = $this->accountant($noAccount ? $this->myBranchIds() : $this->branchIdsOf($userId), $range);
                break;
            default:
                $data = []; // tab Giáo viên dùng LessonReportsController@teacherTimetable
        }
        $data['month'] = date('Y-m', strtotime($range[0]));
        return response()->json((object)['status' => 1, 'message' => '', 'data' => $data]);
    }

    /* ======================= BGĐ / GĐ trung tâm ======================= */

    private function overview($branchIds, $range, $byBranch)
    {
        list($start, $end, $pStart, $pEnd, $year, $month) = $range;
        $pCond = " p.branch_id IN ($branchIds) ";
        $cCond = " c.branch_id IN ($branchIds) ";

        $rev = u::first("SELECT SUM(amount) AS total, COUNT(*) AS n FROM payments AS p WHERE p.charge_date BETWEEN '$start' AND '$end' AND $pCond");
        $revPrev = u::first("SELECT SUM(amount) AS total FROM payments AS p WHERE p.charge_date BETWEEN '$pStart' AND '$pEnd' AND $pCond");
        $target = u::first("SELECT SUM(target_amount) AS total FROM sale_targets WHERE year = $year AND month = $month AND branch_id IN ($branchIds)");
        $newC = u::first("SELECT COUNT(*) AS n, SUM(must_charge) AS total FROM contracts AS c WHERE c.type = 1 AND c.status > 0 AND c.created_at BETWEEN '$start 00:00:00' AND '$end 23:59:59' AND $cCond");
        $active = u::first("SELECT COUNT(DISTINCT c.student_id) AS n FROM contracts AS c WHERE c.type = 1 AND c.status = 6 AND $cCond");
        $waiting = u::first("SELECT COUNT(DISTINCT c.student_id) AS n FROM contracts AS c WHERE c.type = 1 AND c.status IN (2, 3, 5) AND $cCond");
        $reserved = u::first("SELECT COUNT(DISTINCT c.student_id) AS n FROM contracts AS c WHERE c.type = 1 AND c.status = 4 AND $cCond");
        $debt = u::first("SELECT SUM(c.debt_amount) AS total, COUNT(*) AS n FROM contracts AS c WHERE c.type = 1 AND c.status IN (" . self::ACTIVE_CONTRACT_STATUSES . ") AND c.debt_amount > 0 AND $cCond");
        $leadCond = " p.branch_id IN ($branchIds) AND p.created_at BETWEEN '$start 00:00:00' AND '$end 23:59:59' ";
        $leads = u::first("SELECT COUNT(*) AS n, SUM(p.status = 10) AS bought FROM crm_parents AS p WHERE $leadCond");
        $pending = u::first("SELECT COUNT(*) AS n, SUM(tp.charge_amount) AS total FROM tmp_payments AS tp JOIN contracts AS c ON c.id = tp.contract_id WHERE tp.status = 0 AND $cCond");
        $warehouse = u::first("SELECT COUNT(*) AS n FROM crm_parents AS p WHERE p.branch_id IN ($branchIds) AND " . ParentsController::warehouseCond('p'));
        $attendance = u::first("SELECT SUM(ss.attendance_status IN (1, 3)) AS present, SUM(ss.attendance_status = 2) AS absent
            FROM schedule_has_student AS ss JOIN classes AS cl ON cl.id = ss.class_id
            WHERE ss.class_date BETWEEN '$start' AND LEAST('$end', CURDATE()) AND cl.branch_id IN ($branchIds)");

        $revenue = $this->num($rev->total);
        $kpis = [
            'revenue' => $revenue, 'revenue_prev' => $this->num($revPrev->total), 'revenue_count' => (int)$rev->n,
            'target' => $this->num($target->total), 'target_pct' => $this->pct($revenue, $this->num($target->total)),
            'new_contracts' => (int)$newC->n, 'new_contract_amount' => $this->num($newC->total),
            'active_students' => (int)$active->n, 'waiting_students' => (int)$waiting->n, 'reserved_students' => (int)$reserved->n,
            'debt' => $this->num($debt->total), 'debt_count' => (int)$debt->n,
            'leads' => (int)$leads->n, 'leads_bought' => (int)$leads->bought, 'conversion' => $this->pct((int)$leads->bought, (int)$leads->n),
            'pending_receipts' => (int)$pending->n, 'pending_amount' => $this->num($pending->total),
            'warehouse' => (int)$warehouse->n,
            'attendance_rate' => $this->pct((int)$attendance->present, (int)$attendance->present + (int)$attendance->absent),
        ];

        // Xếp hạng Sale: chỉ tiêu vs thực thu (theo contracts.ec_id)
        $saleRows = u::query("SELECT u.id, u.name,
                (SELECT SUM(st.target_amount) FROM sale_targets AS st WHERE st.user_id = u.id AND st.year = $year AND st.month = $month) AS target,
                (SELECT SUM(p.amount) FROM payments AS p JOIN contracts AS c ON c.id = p.contract_id WHERE c.ec_id = u.id AND p.charge_date BETWEEN '$start' AND '$end' AND $cCond) AS actual,
                (SELECT COUNT(*) FROM contracts AS c WHERE c.ec_id = u.id AND c.type = 1 AND c.status > 0 AND c.created_at BETWEEN '$start 00:00:00' AND '$end 23:59:59' AND $cCond) AS contracts,
                (SELECT COUNT(*) FROM crm_parents AS p WHERE p.owner_id = u.id AND p.created_at BETWEEN '$start 00:00:00' AND '$end 23:59:59') AS leads
            FROM users AS u JOIN role_has_user AS ru ON ru.user_id = u.id AND ru.role_id IN (68, 69)
            JOIN branch_has_user AS bu ON bu.user_id = u.id AND bu.branch_id IN ($branchIds)
            WHERE u.status = 1 GROUP BY u.id, u.name");
        foreach ($saleRows as $s) {
            $s->target = $this->num($s->target); $s->actual = $this->num($s->actual);
            $s->pct = $this->pct($s->actual, $s->target);
        }
        usort($saleRows, function ($a, $b) { return $b->actual <=> $a->actual ?: $b->contracts <=> $a->contracts; });

        // Lấp đầy lớp
        $classes = u::query("SELECT cl.id, cl.cls_name, cl.max_students, (SELECT name FROM branches WHERE id = cl.branch_id) AS branch_name,
                (SELECT name FROM users WHERE id = cl.teacher_id) AS teacher_name,
                (SELECT COUNT(DISTINCT c.student_id) FROM contracts AS c WHERE c.class_id = cl.id AND c.type = 1 AND c.status = 6) AS students
            FROM classes AS cl WHERE cl.status = 1 AND cl.branch_id IN ($branchIds) AND (cl.cls_enddate IS NULL OR cl.cls_enddate >= CURDATE())");
        $seat = 0; $filled = 0;
        foreach ($classes as $c) {
            $c->students = (int)$c->students; $c->max_students = (int)$c->max_students;
            $c->fill = $this->pct($c->students, $c->max_students);
            if ($c->max_students > 0) { $seat += $c->max_students; $filled += min($c->students, $c->max_students); }
        }
        usort($classes, function ($a, $b) { return ($a->fill ?? 999) <=> ($b->fill ?? 999); });
        $kpis['class_count'] = count($classes);
        $kpis['occupancy'] = $this->pct($filled, $seat);

        $byProduct = u::query("SELECT COALESCE(pr.name, 'Khác') AS name, COUNT(*) AS n, SUM(c.must_charge) AS total
            FROM contracts AS c LEFT JOIN products AS pr ON pr.id = c.product_id
            WHERE c.type = 1 AND c.status > 0 AND c.created_at BETWEEN '$start 00:00:00' AND '$end 23:59:59' AND $cCond GROUP BY pr.name ORDER BY total DESC");

        $out = [
            'kpis' => $kpis,
            'revenue_trend' => $this->revenueTrend($end, $pCond),
            'funnel' => $this->funnel($leadCond),
            'leads_by_source' => $this->leadsBySource($leadCond),
            'contracts_by_product' => ['labels' => array_column($byProduct, 'name'), 'values' => array_map('floatval', array_column($byProduct, 'total')), 'counts' => array_map('intval', array_column($byProduct, 'n'))],
            'sale_ranking' => array_slice($saleRows, 0, 15),
            'classes_low' => array_slice($classes, 0, 10),
            'near_end' => $this->nearEndStudents($cCond),
            'absent' => $this->absentStreak(" cl.branch_id IN ($branchIds) "),
            'debts' => $this->debtContracts($cCond),
        ];
        if ($byBranch) {
            $out['by_branch'] = u::query("SELECT b.id, b.name,
                    (SELECT SUM(p.amount) FROM payments AS p WHERE p.branch_id = b.id AND p.charge_date BETWEEN '$start' AND '$end') AS actual,
                    (SELECT SUM(st.target_amount) FROM sale_targets AS st WHERE st.branch_id = b.id AND st.year = $year AND st.month = $month) AS target,
                    (SELECT COUNT(DISTINCT c.student_id) FROM contracts AS c WHERE c.branch_id = b.id AND c.type = 1 AND c.status = 6) AS students,
                    (SELECT COUNT(*) FROM crm_parents AS p WHERE p.branch_id = b.id AND p.created_at BETWEEN '$start 00:00:00' AND '$end 23:59:59') AS leads
                FROM branches AS b WHERE b.status = 1 AND b.id IN ($branchIds) ORDER BY b.name");
            foreach ($out['by_branch'] as $b) {
                $b->actual = $this->num($b->actual); $b->target = $this->num($b->target);
                $b->pct = $this->pct($b->actual, $b->target);
                $b->students = (int)$b->students; $b->leads = (int)$b->leads;
            }
        }
        return $out;
    }

    /* ======================= Sale ======================= */

    private function sale($userId, $range)
    {
        list($start, $end, $pStart, $pEnd, $year, $month) = $range;
        $staff = $this->teamIds($userId, [69]);
        $isTeam = strpos($staff, ',') !== false;
        $today = date('Y-m-d');
        $ecCond = " c.ec_id IN ($staff) ";
        $ownCond = " p.owner_id IN ($staff) ";
        $leadCond = "$ownCond AND COALESCE(p.last_assign_date, p.created_at) BETWEEN '$start 00:00:00' AND '$end 23:59:59'";
        $openCond = "$ownCond AND IFNULL(p.status, 0) NOT IN (10, 12)";

        $target = u::first("SELECT SUM(target_amount) AS total FROM sale_targets WHERE year = $year AND month = $month AND user_id IN ($staff)");
        $rev = u::first("SELECT SUM(p.amount) AS total FROM payments AS p JOIN contracts AS c ON c.id = p.contract_id WHERE $ecCond AND p.charge_date BETWEEN '$start' AND '$end'");
        $revPrev = u::first("SELECT SUM(p.amount) AS total FROM payments AS p JOIN contracts AS c ON c.id = p.contract_id WHERE $ecCond AND p.charge_date BETWEEN '$pStart' AND '$pEnd'");
        $newC = u::first("SELECT COUNT(*) AS n, SUM(c.must_charge) AS total FROM contracts AS c WHERE c.type = 1 AND c.status > 0 AND $ecCond AND c.created_at BETWEEN '$start 00:00:00' AND '$end 23:59:59'");
        $pendingRc = u::first("SELECT COUNT(*) AS n, SUM(tp.charge_amount) AS total FROM tmp_payments AS tp JOIN contracts AS c ON c.id = tp.contract_id WHERE tp.status = 0 AND $ecCond");
        $debt = u::first("SELECT SUM(c.debt_amount) AS total, COUNT(*) AS n FROM contracts AS c WHERE c.type = 1 AND c.status IN (" . self::ACTIVE_CONTRACT_STATUSES . ") AND c.debt_amount > 0 AND $ecCond");
        $owned = u::first("SELECT COUNT(*) AS n FROM crm_parents AS p WHERE $openCond");
        $leads = u::first("SELECT COUNT(*) AS n, SUM(p.status = 10) AS bought, SUM(p.status IN (8, 9)) AS checkin FROM crm_parents AS p WHERE $leadCond");
        $careToday = u::first("SELECT COUNT(*) AS n FROM crm_customer_care WHERE creator_id IN ($staff) AND created_at >= '$today 00:00:00'");
        $careMonth = u::first("SELECT COUNT(*) AS n FROM crm_customer_care WHERE creator_id IN ($staff) AND created_at BETWEEN '$start 00:00:00' AND '$end 23:59:59'");
        $due = u::first("SELECT SUM(DATE(p.next_care_date) = CURDATE()) AS today, SUM(DATE(p.next_care_date) < CURDATE()) AS overdue
            FROM crm_parents AS p WHERE $openCond AND p.next_care_date IS NOT NULL AND DATE(p.next_care_date) <= CURDATE()");
        $days = (int)ParentsController::WAREHOUSE_DAYS;
        $soonCond = "$ownCond AND IFNULL(p.is_lock, 0) = 1 AND IFNULL(p.status, 0) NOT IN (10, 12)
            AND DATEDIFF(CURDATE(), COALESCE(p.last_assign_date, p.created_at)) BETWEEN " . ($days - 3) . " AND $days
            AND NOT EXISTS (SELECT 1 FROM crm_students AS wcs JOIN contracts AS wct ON wct.student_id = wcs.lms_id WHERE wcs.parent_id = p.id AND wct.type > 0)";
        $soon = u::first("SELECT COUNT(*) AS n FROM crm_parents AS p WHERE $soonCond");

        $revenue = $this->num($rev->total);
        $kpis = [
            'target' => $this->num($target->total), 'revenue' => $revenue, 'revenue_prev' => $this->num($revPrev->total),
            'target_pct' => $this->pct($revenue, $this->num($target->total)),
            'remaining' => max(0, $this->num($target->total) - $revenue),
            'days_left' => max(0, (int)((strtotime($end) - strtotime(max($today, $start))) / 86400) + ($today <= $end ? 1 : 0)),
            'new_contracts' => (int)$newC->n, 'new_contract_amount' => $this->num($newC->total),
            'pending_receipts' => (int)$pendingRc->n, 'pending_amount' => $this->num($pendingRc->total),
            'debt' => $this->num($debt->total), 'debt_count' => (int)$debt->n,
            'owned' => (int)$owned->n, 'leads' => (int)$leads->n, 'leads_checkin' => (int)$leads->checkin, 'leads_bought' => (int)$leads->bought,
            'conversion' => $this->pct((int)$leads->bought, (int)$leads->n),
            'care_today' => (int)$careToday->n, 'care_month' => (int)$careMonth->n,
            'due_today' => (int)$due->today, 'overdue' => (int)$due->overdue, 'warehouse_soon' => (int)$soon->n,
        ];

        $labels = $this->parentStatusLabels();
        $todo = u::query("SELECT p.id, p.name, p.mobile_1, p.status, p.next_care_date, p.care_date,
                (SELECT name FROM users WHERE id = p.owner_id) AS owner_name,
                (SELECT note FROM crm_customer_care WHERE parent_id = p.id ORDER BY id DESC LIMIT 1) AS last_note
            FROM crm_parents AS p WHERE $openCond AND p.next_care_date IS NOT NULL AND DATE(p.next_care_date) <= CURDATE()
            ORDER BY p.next_care_date ASC LIMIT 20");
        $soonList = u::query("SELECT p.id, p.name, p.mobile_1, p.status, COALESCE(p.last_assign_date, p.created_at) AS assign_date,
                ($days + 1 - DATEDIFF(CURDATE(), COALESCE(p.last_assign_date, p.created_at))) AS days_left,
                (SELECT name FROM users WHERE id = p.owner_id) AS owner_name
            FROM crm_parents AS p WHERE $soonCond ORDER BY days_left ASC LIMIT 20");
        foreach (array_merge($todo, $soonList) as $r) $r->status_label = $labels[(int)$r->status] ?? '';

        $callRows = u::query("SELECT call_status, COUNT(*) AS n FROM crm_customer_care WHERE creator_id IN ($staff) AND created_at BETWEEN '$start 00:00:00' AND '$end 23:59:59' GROUP BY call_status ORDER BY n DESC");
        $calls = ['labels' => [], 'values' => []];
        foreach ($callRows as $c) {
            $calls['labels'][] = u::getTitleCallStatus((int)$c->call_status, 0) ?: 'Chưa phân loại';
            $calls['values'][] = (int)$c->n;
        }

        $out = [
            'is_team' => $isTeam,
            'kpis' => $kpis,
            'revenue_daily' => $this->revenueDaily($start, $end, $ecCond),
            'funnel' => $this->funnel($leadCond),
            'leads_by_source' => $this->leadsBySource($leadCond),
            'calls' => $calls,
            'todo' => $todo,
            'warehouse_soon' => $soonList,
            'debts' => $this->debtContracts($ecCond),
        ];
        if ($isTeam) {
            $members = u::query("SELECT u.id, u.name, u.hrm_id,
                    (SELECT SUM(target_amount) FROM sale_targets WHERE user_id = u.id AND year = $year AND month = $month) AS target,
                    (SELECT SUM(p.amount) FROM payments AS p JOIN contracts AS c ON c.id = p.contract_id WHERE c.ec_id = u.id AND p.charge_date BETWEEN '$start' AND '$end') AS actual,
                    (SELECT COUNT(*) FROM contracts AS c WHERE c.ec_id = u.id AND c.type = 1 AND c.status > 0 AND c.created_at BETWEEN '$start 00:00:00' AND '$end 23:59:59') AS contracts,
                    (SELECT COUNT(*) FROM crm_parents AS p WHERE p.owner_id = u.id AND COALESCE(p.last_assign_date, p.created_at) BETWEEN '$start 00:00:00' AND '$end 23:59:59') AS leads,
                    (SELECT COUNT(*) FROM crm_customer_care WHERE creator_id = u.id AND created_at BETWEEN '$start 00:00:00' AND '$end 23:59:59') AS cares,
                    (SELECT COUNT(*) FROM crm_parents AS p WHERE p.owner_id = u.id AND IFNULL(p.status, 0) NOT IN (10, 12) AND p.next_care_date IS NOT NULL AND DATE(p.next_care_date) < CURDATE()) AS overdue
                FROM users AS u WHERE u.id IN ($staff) AND u.status = 1 ORDER BY u.name");
            foreach ($members as $m) {
                $m->target = $this->num($m->target); $m->actual = $this->num($m->actual);
                $m->pct = $this->pct($m->actual, $m->target);
            }
            usort($members, function ($a, $b) { return $b->actual <=> $a->actual; });
            $out['members'] = $members;
        }
        return $out;
    }

    /* ======================= CSKH ======================= */

    private function cm($userId, $range)
    {
        list($start, $end) = $range;
        $staff = $this->teamIds($userId, [56]);
        // HS phụ trách: CM trên hợp đồng, hoặc CM của học sinh, hoặc CM của lớp
        $cCond = " (c.cm_id IN ($staff) OR EXISTS (SELECT 1 FROM term_student_user AS t WHERE t.student_id = c.student_id AND t.status = 1 AND t.cm_id IN ($staff))
            OR EXISTS (SELECT 1 FROM classes AS xc WHERE xc.id = c.class_id AND xc.cm_id IN ($staff))) ";
        $students = u::first("SELECT COUNT(DISTINCT c.student_id) AS n FROM contracts AS c WHERE c.type = 1 AND c.status = 6 AND $cCond");
        $classes = u::first("SELECT COUNT(*) AS n FROM classes AS cl WHERE cl.status = 1 AND cl.cm_id IN ($staff) AND (cl.cls_enddate IS NULL OR cl.cls_enddate >= CURDATE())");
        $nearEnd = u::first("SELECT COUNT(*) AS n FROM contracts AS c WHERE c.type = 1 AND c.status = 6 AND c.left_sessions BETWEEN 0 AND 4 AND $cCond");
        $reserved = u::first("SELECT COUNT(DISTINCT c.student_id) AS n FROM contracts AS c WHERE c.type = 1 AND c.status = 4 AND $cCond");
        $ended = u::first("SELECT COUNT(DISTINCT c.student_id) AS n FROM contracts AS c WHERE c.type = 1 AND c.status = 7 AND c.updated_at BETWEEN '$start 00:00:00' AND '$end 23:59:59' AND $cCond");
        $debt = u::first("SELECT SUM(c.debt_amount) AS total, COUNT(*) AS n FROM contracts AS c WHERE c.type = 1 AND c.status IN (" . self::ACTIVE_CONTRACT_STATUSES . ") AND c.debt_amount > 0 AND $cCond");
        $shsCond = " (cl.cm_id IN ($staff) OR EXISTS (SELECT 1 FROM term_student_user AS t WHERE t.student_id = ss.student_id AND t.status = 1 AND t.cm_id IN ($staff))) ";
        $att = u::first("SELECT SUM(ss.attendance_status IN (1, 3)) AS present, SUM(ss.attendance_status = 2) AS absent, SUM(ss.attendance_status = 0) AS pending
            FROM schedule_has_student AS ss JOIN classes AS cl ON cl.id = ss.class_id
            WHERE ss.class_date BETWEEN '$start' AND LEAST('$end', CURDATE()) AND $shsCond");
        $absent = $this->absentStreak($shsCond);
        $kpis = [
            'students' => (int)$students->n, 'classes' => (int)$classes->n, 'near_end' => (int)$nearEnd->n,
            'reserved' => (int)$reserved->n, 'ended_month' => (int)$ended->n,
            'debt' => $this->num($debt->total), 'debt_count' => (int)$debt->n,
            'attendance_rate' => $this->pct((int)$att->present, (int)$att->present + (int)$att->absent),
            'pending_attendance' => (int)$att->pending, 'absent_streak' => count($absent),
        ];
        $classList = u::query("SELECT cl.id, cl.cls_name, cl.max_students, (SELECT name FROM users WHERE id = cl.teacher_id) AS teacher_name,
                (SELECT COUNT(DISTINCT c.student_id) FROM contracts AS c WHERE c.class_id = cl.id AND c.type = 1 AND c.status = 6) AS students,
                (SELECT ROUND(100 * SUM(ss.attendance_status IN (1, 3)) / NULLIF(SUM(ss.attendance_status IN (1, 2, 3)), 0), 1)
                    FROM schedule_has_student AS ss WHERE ss.class_id = cl.id AND ss.class_date BETWEEN '$start' AND LEAST('$end', CURDATE())) AS attendance_rate
            FROM classes AS cl WHERE cl.status = 1 AND cl.cm_id IN ($staff) AND (cl.cls_enddate IS NULL OR cl.cls_enddate >= CURDATE()) ORDER BY cl.cls_name");
        return [
            'kpis' => $kpis,
            'near_end' => $this->nearEndStudents($cCond, 20),
            'absent' => $absent,
            'birthdays' => $this->birthdays($cCond),
            'debts' => $this->debtContracts($cCond),
            'classes' => $classList,
        ];
    }

    /* ======================= Kế toán ======================= */

    private function accountant($branchIds, $range)
    {
        list($start, $end, $pStart, $pEnd) = $range;
        $today = date('Y-m-d');
        $pCond = " p.branch_id IN ($branchIds) ";
        $cCond = " c.branch_id IN ($branchIds) ";
        $todayRev = u::first("SELECT SUM(amount) AS total, COUNT(*) AS n FROM payments AS p WHERE p.charge_date = '$today' AND $pCond");
        $rev = u::first("SELECT SUM(amount) AS total, COUNT(*) AS n FROM payments AS p WHERE p.charge_date BETWEEN '$start' AND '$end' AND $pCond");
        $revPrev = u::first("SELECT SUM(amount) AS total FROM payments AS p WHERE p.charge_date BETWEEN '$pStart' AND '$pEnd' AND $pCond");
        $pending = u::first("SELECT COUNT(*) AS n, SUM(tp.charge_amount) AS total FROM tmp_payments AS tp JOIN contracts AS c ON c.id = tp.contract_id WHERE tp.status = 0 AND $cCond");
        $debt = u::first("SELECT SUM(c.debt_amount) AS total, COUNT(*) AS n FROM contracts AS c WHERE c.type = 1 AND c.status IN (" . self::ACTIVE_CONTRACT_STATUSES . ") AND c.debt_amount > 0 AND $cCond");
        $deposit = u::first("SELECT COUNT(*) AS n FROM contracts AS c WHERE c.type = 1 AND c.status = 2 AND $cCond");
        $methods = u::query("SELECT p.method, SUM(p.amount) AS total FROM payments AS p WHERE p.charge_date BETWEEN '$start' AND '$end' AND $pCond GROUP BY p.method");
        $methodName = [0 => 'Tiền mặt', 1 => 'Chuyển khoản', 2 => 'Thẻ tín dụng'];
        $pendingList = u::query("SELECT tp.id, tp.code, tp.contract_id, tp.charge_amount, tp.charge_date, tp.method, tp.created_at, s.name AS student_name, c.code AS contract_code,
                (SELECT name FROM users WHERE id = tp.creator_id) AS creator_name, (SELECT name FROM branches WHERE id = c.branch_id) AS branch_name
            FROM tmp_payments AS tp JOIN contracts AS c ON c.id = tp.contract_id LEFT JOIN students AS s ON s.id = c.student_id
            WHERE tp.status = 0 AND $cCond ORDER BY tp.created_at ASC LIMIT 20");
        foreach ($pendingList as $r) $r->method_name = $methodName[(int)$r->method] ?? 'Khác';
        return [
            'kpis' => [
                'today' => $this->num($todayRev->total), 'today_count' => (int)$todayRev->n,
                'revenue' => $this->num($rev->total), 'revenue_count' => (int)$rev->n, 'revenue_prev' => $this->num($revPrev->total),
                'pending_receipts' => (int)$pending->n, 'pending_amount' => $this->num($pending->total),
                'debt' => $this->num($debt->total), 'debt_count' => (int)$debt->n, 'deposit_count' => (int)$deposit->n,
            ],
            'revenue_daily' => $this->revenueDaily($start, $end, $pCond),
            'revenue_trend' => $this->revenueTrend($end, $pCond),
            'by_method' => ['labels' => array_map(function ($m) use ($methodName) { return $methodName[(int)$m->method] ?? 'Khác'; }, $methods),
                'values' => array_map(function ($m) { return (float)$m->total; }, $methods)],
            'pending_list' => $pendingList,
            'debts' => $this->debtContracts($cCond, 20),
        ];
    }
}
