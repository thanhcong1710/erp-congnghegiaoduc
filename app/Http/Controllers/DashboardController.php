<?php

namespace App\Http\Controllers;

use App\Enums\SystemCode;
use App\User;
use App\Http\Controllers\Controller;
use App\Models\LogStudents;
use App\Providers\UtilityServiceProvider as u;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function dashboard01(Request $request)
    {
        $today = new DateTime();
        $monday = $today->modify('monday this week');
        if (data_get($request, 'branch_id')) {
            $cond = " AND b.branch_id IN (" . implode(",", data_get($request, 'branch_id')) . ")";
        } else {
            $cond = " AND b.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";
        }
        $totalRevenueWeek = u::first("SELECT SUM(b.amount) AS total FROM payments AS b 
            WHERE b.charge_date >= '" . $monday->format('Y-m-d') . "' $cond");

        $data = [
            'totalRevenueWeek' => [
                'data' => round(data_get($totalRevenueWeek, 'total', 0) / 1000000, 2)
            ]
        ];
        return response()->json($data);
    }
    public function dashboard02(Request $request)
    {
        if (data_get($request, 'branch_id')) {
            $cond = " AND b.branch_id IN (" . implode(",", data_get($request, 'branch_id')) . ")";
        } else {
            $cond = " AND b.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";
        }
        $totalRevenueMonth = u::first("SELECT SUM(b.amount) AS total FROM payments AS b 
            WHERE DATE_FORMAT(b.charge_date,'%Y-%m')='" . date('Y-m') . "' $cond");

        $data = [
            'totalRevenueMonth' => [
                'data' => round(data_get($totalRevenueMonth, 'total', 0) / 1000000, 2)
            ]
        ];
        return response()->json($data);
    }

    public function dashboard03(Request $request)
    {
        if (data_get($request, 'branch_id')) {
            $cond = " AND c.branch_id IN (" . implode(",", data_get($request, 'branch_id')) . ")";
        } else {
            $cond = " AND c.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";
        }
        $numDashStudent = u::first("SELECT count(DISTINCT c.student_id) AS total FROM contracts AS c WHERE c.status=6 $cond");

        $data = [
            'numDashStudent' => [
                'data' => data_get($numDashStudent, 'total')
            ]
        ];
        return response()->json($data);
    }

    public function dashboard04(Request $request)
    {
        if (data_get($request, 'branch_id')) {
            $cond = " AND c.branch_id IN (" . implode(",", data_get($request, 'branch_id')) . ")";
        } else {
            $cond = " AND c.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";
        }
        $numDashClass = u::first("SELECT count(DISTINCT c.id) AS total FROM classes AS c WHERE c.status=1 
            AND c.cls_startdate <= CURRENT_DATE AND c.cls_enddate >= CURRENT_DATE $cond");
        $data = [
            'numDashClass' => [
                'data' => data_get($numDashClass, 'total')
            ],
        ];
        return response()->json($data);
    }
    public function dashboard05(Request $request)
    {
        if (data_get($request, 'branch_id')) {
            $cond = " AND c.branch_id IN (" . implode(",", data_get($request, 'branch_id')) . ")";
        } else {
            $cond = " AND c.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";
        }
        $studentActive = u::first("SELECT count(DISTINCT c.student_id) AS total FROM contracts AS c WHERE c.status=6 AND c.type>0 $cond");
        $studentTrial = u::first("SELECT count(DISTINCT c.student_id) AS total FROM contracts AS c WHERE c.status=6 AND c.type=0 $cond");
        $studentWithdraw = u::first("SELECT count(DISTINCT c.student_id) AS total FROM contracts AS c WHERE c.status=7 AND c.type>0 
            AND (SELECT count(id) FROM contracts WHERE student_id=c.student_id AND type>0 AND status!=7)=0 $cond");
        $studentPending = u::first("SELECT count(DISTINCT c.student_id) AS total FROM contracts AS c WHERE c.status!=6 AND c.status!=7 AND c.type>0 AND c.init_total_charged>0
            AND (SELECT count(id) FROM contracts WHERE student_id=c.student_id AND c.status=6)=0 $cond");
        $data = [
            'pieChartStudent' => [
                'studentActive' => data_get($studentActive, 'total', 0),
                'studentTrial' => data_get($studentTrial, 'total', 0),
                'studentWithdraw' => data_get($studentWithdraw, 'total', 0),
                'studentPending' => data_get($studentPending, 'total', 0),
            ],
        ];
        return response()->json($data);
    }
    public function dashboard06(Request $request)
    {
        if (data_get($request, 'branch_id')) {
            $cond = " AND c.branch_id IN (" . implode(",", data_get($request, 'branch_id')) . ")";
        } else {
            $cond = " AND c.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";
        }
        $pieChartProduct = u::query("SELECT count( c.id ) AS total, c.product_id, (SELECT name FROM products WHERE id= c.product_id ) AS product_name 
            FROM contracts AS c  WHERE c.type > 0  AND c.STATUS != 7  AND c.init_total_charged > 0 $cond GROUP BY c.product_id");
        $labelsChartProduct = [];
        $seriesChartProduct = [];
        foreach ($pieChartProduct as $product) {
            $labelsChartProduct[] = data_get($product, 'product_name');
            $seriesChartProduct[] = data_get($product, 'total');
        }
        $data = [
            'pieChartProduct' => [
                'labelsChartProduct' => $labelsChartProduct,
                'seriesChartProduct' => $seriesChartProduct
            ]
        ];
        return response()->json($data);
    }
    public function dashboard07(Request $request)
    {
        if (data_get($request, 'branch_id')) {
            $cond = " AND c.branch_id IN (" . implode(",", data_get($request, 'branch_id')) . ")";
        } else {
            $cond = " AND c.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";
        }
        $fullFee = u::first("SELECT count(DISTINCT c.student_id) AS total FROM contracts AS c WHERE c.status!=7 AND c.type>0 AND c.init_total_charged >0 AND c.debt_amount =0 $cond");
        $deposit = u::first("SELECT count(DISTINCT c.student_id) AS total FROM contracts AS c WHERE c.status!=7 AND c.type>0 AND c.init_total_charged >0 AND c.debt_amount >0 $cond");
        $init = u::first("SELECT count(DISTINCT c.student_id) AS total FROM contracts AS c WHERE c.status!=7 AND c.type>0 AND c.init_total_charged =0 AND c.debt_amount >0 $cond");
        $withdraw = u::first("SELECT count(DISTINCT c.student_id) AS total FROM contracts AS c WHERE c.status=7 AND c.type>0 
            AND (SELECT count(id) FROM contracts WHERE student_id=c.student_id AND type>0 AND status!=7)=0 $cond");
        $data = [
            'pieChartFee' => [
                'fullFee' => data_get($fullFee, 'total', 0),
                'deposit' => data_get($deposit, 'total', 0),
                'init' => data_get($init, 'total', 0),
                'withdraw' => data_get($withdraw, 'total', 0),
            ],
        ];
        return response()->json($data);
    }
    public function dashboard08(Request $request)
    {
        if (data_get($request, 'branch_id')) {
            $cond = " AND b.id IN (" . implode(",", data_get($request, 'branch_id')) . ")";
            $cond1 = " AND p.branch_id IN (" . implode(",", data_get($request, 'branch_id')) . ")";
        } else {
            $cond = " AND b.id IN (" . Auth::user()->getBranchesHasUser() . ") AND b.id > 10";
            $cond1 = " AND p.branch_id IN (" . Auth::user()->getBranchesHasUser() . ") AND p.branch_id > 10";
        }
        $branches = u::query("SELECT b.name AS branch_name, b.code, b.id
            FROM branches AS b WHERE b.status=1 $cond");
        $categories = [];
        $series = [];
        $dataRevenue = [];
        foreach ($branches as $row) {
            $series[data_get($row, 'id')] = [
                'name' => data_get($row, 'branch_name'),
                'data' => [0, 0, 0, 0, 0, 0]
            ];
        }
        for ($i = 0; $i < 6; $i++) {
            $m = $i - 5;
            $tmp_date = date('Y-m', strtotime("$m month"));
            $categories[] = date('m/Y', strtotime("$m month"));
            $dataRevenue = u::query("SELECT SUM(p.amount) AS total, p.branch_id FROM payments AS p 
                WHERE DATE_FORMAT(p.charge_date,'%Y-%m')='$tmp_date' $cond1 GROUP BY p.branch_id");
            foreach ($dataRevenue as $row) {
                if (isset($series[$row->branch_id])) {
                    $series[$row->branch_id]['data'][$i] = round(data_get($row, 'total') / 1000000, 2);
                }
            }
        }
        $resultSeries = [];
        foreach ($series as $s) {
            $resultSeries[] = $s;
        }
        $data = [
            'lineChartRevenue' => [
                'categories' => $categories,
                'series' => $resultSeries
            ]
        ];
        return response()->json($data);
    }

    public function dashboard09(Request $request)
    {
        if (data_get($request, 'branch_id')) {
            $cond = " AND b.id IN (" . implode(",", data_get($request, 'branch_id')) . ")";
            $cond1 = " AND p.branch_id IN (" . implode(",", data_get($request, 'branch_id')) . ")";
        } else {
            $cond = " AND b.id IN (" . Auth::user()->getBranchesHasUser() . ") AND b.id > 10";
            $cond1 = " AND p.branch_id IN (" . Auth::user()->getBranchesHasUser() . ") AND p.branch_id > 10";
        }
        $branches = u::query("SELECT b.name AS branch_name, b.code, b.id
            FROM branches AS b WHERE b.status=1 $cond");
        $categories = [];
        $series = [];
        $dataRevenue = [];
        foreach ($branches as $row) {
            $series[data_get($row, 'id')] = [
                'name' => data_get($row, 'branch_name'),
                'data' => [0, 0, 0, 0, 0, 0]
            ];
        }
        for ($i = 0; $i < 6; $i++) {
            $m = $i - 5;
            $tmp_date = date('Y-m', strtotime("$m month"));
            $categories[] = date('m/Y', strtotime("$m month"));
            $dataRevenue = u::query("SELECT SUM(p.amount) AS total, p.branch_id FROM payments AS p 
                LEFT JOIN contracts AS c ON c.id=p.contract_id
                WHERE DATE_FORMAT(p.charge_date,'%Y-%m')='$tmp_date' AND c.count_recharge > 0 $cond1 GROUP BY p.branch_id");
            foreach ($dataRevenue as $row) {
                if (isset($series[$row->branch_id])) {
                    $series[$row->branch_id]['data'][$i] = round(data_get($row, 'total') / 1000000, 2);
                }
            }
        }
        $resultSeries = [];
        foreach ($series as $s) {
            $resultSeries[] = $s;
        }
        $data = [
            'lineChartRevenueRenew' => [
                'categories' => $categories,
                'series' => $resultSeries
            ]
        ];
        return response()->json($data);
    }
    public function dashboard10(Request $request)
    {
        if (data_get($request, 'branch_id')) {
            $cond = " AND b.id IN (" . implode(",", data_get($request, 'branch_id')) . ")";
            $cond1 = " AND branch_id IN (" . implode(",", data_get($request, 'branch_id')) . ")";
        } else {
            $cond = " AND b.id IN (" . Auth::user()->getBranchesHasUser() . ") AND b.id > 10";
            $cond1 = " AND branch_id IN (" . Auth::user()->getBranchesHasUser() . ") AND branch_id > 10";
        }
        $branches = u::query("SELECT b.name AS branch_name, b.code, b.id
            FROM branches AS b WHERE b.status=1 $cond");
        $categories = [];
        $series = [];
        $dataRevenue = [];
        foreach ($branches as $row) {
            $series[data_get($row, 'id')] = [
                'name' => data_get($row, 'branch_name'),
                'data' => [0, 0, 0, 0, 0, 0]
            ];
        }
        for ($i = 0; $i < 6; $i++) {
            $m = $i - 5;
            $tmp_date = date('Y-m', strtotime("$m month"));
            $categories[] = date('m/Y', strtotime("$m month"));
            $dataRevenue = u::query("SELECT count(id) AS total, branch_id,
                    SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS total_renew
                FROM report_renews WHERE renewed_month='$tmp_date' $cond1 GROUP BY branch_id");
            foreach ($dataRevenue as $row) {
                if (isset($series[$row->branch_id])) {
                    $series[$row->branch_id]['data'][$i] = data_get($row, 'total') ? round((data_get($row, 'total_renew') / data_get($row, 'total')) * 100, 2) : 0;
                }
            }
        }
        $resultSeries = [];
        foreach ($series as $s) {
            $resultSeries[] = $s;
        }
        $data = [
            'lineChartRenew' => [
                'categories' => $categories,
                'series' => $resultSeries
            ]
        ];
        return response()->json($data);
    }
    public function dashboard11(Request $request)
    {
        if (data_get($request, 'branch_id')) {
            $cond = " AND b.branch_id IN (" . implode(",", data_get($request, 'branch_id')) . ")";
        } else {
            $cond = " AND b.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";
        }
        $ECs = u::query("SELECT CONCAT(u.hrm_id,'-', u.name) AS ec_name , b.user_id
            FROM branch_has_user AS b LEFT JOIN users AS u ON u.id=b.user_id 
            LEFT JOIN role_has_user AS r ON r.user_id = u.id
            WHERE u.status=1 AND r.role_id IN(" . SystemCode::ROLE_EC . "," . SystemCode::ROLE_EC_LEADER . ") $cond");
        $categories = [];
        $series = [];
        $dataRevenue = [];
        foreach ($ECs as $row) {
            $series[data_get($row, 'user_id')] = [
                'name' => data_get($row, 'ec_name'),
                'data' => [0, 0, 0, 0, 0, 0]
            ];
        }
        for ($i = 0; $i < 6; $i++) {
            $m = $i - 5;
            $tmp_date = date('Y-m', strtotime("$m month"));
            $categories[] = date('m/Y', strtotime("$m month"));
            $dataRevenue = u::query("SELECT SUM(b.amount) AS total, b.ec_id FROM payments AS b 
                WHERE DATE_FORMAT(b.charge_date,'%Y-%m')='$tmp_date' $cond GROUP BY b.ec_id");
            foreach ($dataRevenue as $row) {
                if (isset($series[$row->ec_id])) {
                    $series[$row->ec_id]['data'][$i] = round(data_get($row, 'total') / 1000000, 2);
                }
            }
        }
        $resultSeries = [];
        foreach ($series as $s) {
            $resultSeries[] = $s;
        }
        $data = [
            'lineChartRevenueEC' => [
                'categories' => $categories,
                'series' => $resultSeries
            ]
        ];
        return response()->json($data);
    }

    public function dashboard12(Request $request)
    {
        if (data_get($request, 'branch_id')) {
            $cond = " AND b.branch_id IN (" . implode(",", data_get($request, 'branch_id')) . ")";
        } else {
            $cond = " AND b.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";
        }
        $CMs = u::query("SELECT CONCAT(u.hrm_id,'-', u.name) AS cm_name , b.user_id
            FROM branch_has_user AS b LEFT JOIN users AS u ON u.id=b.user_id 
            LEFT JOIN role_has_user AS r ON r.user_id = u.id
            WHERE u.status=1 AND r.role_id IN(" . SystemCode::ROLE_CM . "," . SystemCode::ROLE_CM_LEADER . ") $cond");
        $categories = [];
        $series = [
            '0' => [
                'name' => date('Y/m') . '(thành công)',
                'group' => date('Y-m'),
                'data' => []
            ],
            '1' => [
                'name' => date('Y/m') . '(thất bại)',
                'group' => date('Y-m'),
                'data' => []
            ],
            '2' => [
                'name' => date('Y/m', strtotime('+1 month')) . '(thành công)',
                'group' => date('Y-m', strtotime('+1 month')),
                'data' => []
            ],
            '3' => [
                'name' => date('Y/m', strtotime('+1 month')) . '(thất bại)',
                'group' => date('Y-m', strtotime('+1 month')),
                'data' => []
            ],
            '4' => [
                'name' => date('Y/m', strtotime('+2 month')) . '(thành công)',
                'group' => date('Y-m', strtotime('+2 month')),
                'data' => []
            ],
            '5' => [
                'name' => date('Y/m', strtotime('+2 month')) . '(thất bại)',
                'group' => date('Y-m', strtotime('+2 month')),
                'data' => []
            ],
        ];
        foreach ($CMs as $cm) {
            $dataRenew = u::first("SELECT count(id) AS total, SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS total_success FROM report_renews WHERE renewed_month = '" . date('Y-m') . "' AND cm_id=" . data_get($cm, 'user_id', 0));
            $dataRenewT1 = u::first("SELECT count(id) AS total, SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS total_success FROM report_renews WHERE renewed_month = '" . date('Y-m', strtotime('+1 month')) . "' AND cm_id=" . data_get($cm, 'user_id', 0));
            $dataRenewT2 = u::first("SELECT count(id) AS total, SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS total_success FROM report_renews WHERE renewed_month = '" . date('Y-m', strtotime('+2 month')) . "' AND cm_id=" . data_get($cm, 'user_id', 0));
            $categories[] = data_get($cm, 'cm_name');
            $series[0]['data'][] = data_get($dataRenew, 'total_success', 0);
            $series[1]['data'][] = data_get($dataRenew, 'total', 0);
            $series[2]['data'][] = data_get($dataRenewT1, 'total_success', 0);
            $series[3]['data'][] = data_get($dataRenewT1, 'total', 0);
            $series[4]['data'][] = data_get($dataRenewT2, 'total_success', 0);
            $series[5]['data'][] = data_get($dataRenewT2, 'total', 0);
        }
        $data = [
            'lineChartRenewCM' => [
                'series' => $series,
                'categories' => $categories
            ]
        ];
        return response()->json($data);
    }

    public function dashboard13(Request $request)
    {
        if (data_get($request, 'branch_id')) {
            $cond = " AND c.branch_id IN (" . implode(",", data_get($request, 'branch_id')) . ")";
        } else {
            $cond = " AND c.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";
        }
        if (Auth::user()->checkRole('68')) {
            $cond .= " AND c.ec_id = " . Auth::user()->id;
        }
        $data = u::query("SELECT c.student_id, s.name, s.gud_mobile1, c.enrolment_start_date, c.left_sessions ,cl.cls_name
            FROM contracts AS c 
                LEFT JOIN students AS s ON s.id = c.student_id
                LEFT JOIN classes AS cl ON cl.id = c.class_id 
            WHERE c.status=6 AND c.type=0 AND c.class_id IS NOT NULL $cond ORDER BY c.enrolment_start_date");
        return response()->json($data);
    }

    public function dashboard14(Request $request)
    {
        if (data_get($request, 'branch_id')) {
            $cond = " AND c.branch_id IN (" . implode(",", data_get($request, 'branch_id')) . ")";
        } else {
            $cond = " AND c.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";
        }
        if (Auth::user()->checkRole('68')) {
            $cond .= " AND c.ec_id = " . Auth::user()->id;
        }
        $data = u::query("SELECT c.student_id, s.name, s.gud_mobile1, c.init_total_charged, c.debt_amount, c.must_charge, t.name AS tuition_fee_name 
            FROM contracts AS c 
                LEFT JOIN students AS s ON s.id = c.student_id
                LEFT JOIN tuition_fee AS t  ON t.id = c.init_tuition_fee_id
            WHERE c.type>0 AND c.debt_amount > 0 AND c.init_total_charged > 0  $cond ORDER BY c.id");
        return response()->json($data);
    }

    public function dashboard15(Request $request)
    {
        if (data_get($request, 'branch_id')) {
            $cond = " AND c.branch_id IN (" . implode(",", data_get($request, 'branch_id')) . ")";
        } else {
            $cond = " AND c.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";
        }
        $data = u::query("SELECT DISTINCT s.id AS student_id, s.name, s.gud_mobile1, s.lms_id, c.start_date, p.name AS product_name
            FROM contracts AS c 
                LEFT JOIN students AS s ON s.id = c.student_id
                LEFT JOIN products AS p ON p.id = c.product_id 
            WHERE s.status>0 AND c.status=3 AND c.class_id IS NULL AND c.type>0 AND c.summary_sessions>0
                AND c.id = (SELECT id FROM contracts WHERE student_id=s.id AND `status`!=7 ORDER BY count_recharge LIMIT 1) 
                $cond ORDER BY c.start_date");
        return response()->json($data);
    }

    public function dashboard16(Request $request)
    {
        if (data_get($request, 'branch_id')) {
            $cond = " AND c.branch_id IN (" . implode(",", data_get($request, 'branch_id')) . ")";
        } else {
            $cond = " AND c.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";
        }
        $data = u::query("SELECT s.id AS student_id, s.name, s.lms_id, c.enrolment_last_date, cl.cls_name
            FROM contracts AS c 
                LEFT JOIN students AS s ON s.id = c.student_id
                LEFT JOIN classes AS cl ON cl.id = c.class_id 
            WHERE c.status=6 AND c.class_id IS NOT NULL AND c.left_sessions <= 0 $cond ORDER BY c.enrolment_last_date");
        return response()->json($data);
    }

    public function dashboard17(Request $request)
    {
        if (data_get($request, 'branch_id')) {
            $cond = " AND b.id IN (" . implode(",", data_get($request, 'branch_id')) . ")";
        } else {
            $cond = " AND b.id IN (" . Auth::user()->getBranchesHasUser() . ") AND b.id > 10";
        }

        // Tính toán các ngày cần thiết
        $today = date('Y-m-d');
        $threeDaysAgo = date('Y-m-d', strtotime('-2 days')); // 3 ngày gần nhất (bao gồm hôm nay)
        $currentMonth = date('Y-m');
        $threeMonthsAgo = date('Y-m', strtotime('-2 months')); // 3 tháng gần nhất

        $branches = u::query("SELECT b.id, b.name AS branch_name
            FROM branches AS b 
            WHERE b.status=1 $cond 
            ORDER BY b.id");

        $result = [];
        foreach ($branches as $index => $branch) {
            $branchId = $branch->id;

            // Doanh số ngày (hôm nay)
            $revenueToday = u::first("SELECT COALESCE(SUM(amount), 0) AS total 
                FROM payments 
                WHERE branch_id = $branchId AND charge_date = '$today'");

            // Doanh số 3 ngày gần nhất
            $revenue3Days = u::first("SELECT COALESCE(SUM(amount), 0) AS total 
                FROM payments 
                WHERE branch_id = $branchId 
                AND charge_date >= '$threeDaysAgo' 
                AND charge_date <= '$today'");

            // Doanh số tháng hiện tại
            $revenueMonth = u::first("SELECT COALESCE(SUM(amount), 0) AS total 
                FROM payments 
                WHERE branch_id = $branchId 
                AND DATE_FORMAT(charge_date, '%Y-%m') = '$currentMonth'");

            // Doanh số 3 tháng gần nhất
            $revenue3Months = u::first("SELECT COALESCE(SUM(amount), 0) AS total 
                FROM payments 
                WHERE branch_id = $branchId 
                AND DATE_FORMAT(charge_date, '%Y-%m') >= '$threeMonthsAgo' 
                AND DATE_FORMAT(charge_date, '%Y-%m') <= '$currentMonth'");

            $result[] = [
                'stt' => $index + 1,
                'branch_id' => $branchId,
                'branch_name' => $branch->branch_name,
                'revenue_today' => (float) $revenueToday->total,
                'revenue_3days' => (float) $revenue3Days->total,
                'revenue_month' => (float) $revenueMonth->total,
                'revenue_3months' => (float) $revenue3Months->total,
                'note' => '' // Có thể thêm ghi chú nếu cần
            ];
        }

        return response()->json($result);
    }

    public function dashboard18(Request $request)
    {
        if (data_get($request, 'branch_id')) {
            $cond = " AND b.id IN (" . implode(",", data_get($request, 'branch_id')) . ")";
        } else {
            $cond = " AND b.id IN (" . Auth::user()->getBranchesHasUser() . ") AND b.id > 10";
        }

        $currentMonth = date('Y-m');
        $startOfMonth = date('Y-m-01');
        $today = date('Y-m-d');

        $branches = u::query("SELECT b.id, b.name AS branch_name
            FROM branches AS b 
            WHERE b.status=1 $cond 
            ORDER BY b.id");

        $result = [];
        foreach ($branches as $index => $branch) {
            $branchId = $branch->id;

            // Số học sinh Check in mới (trong tháng hiện tại)
            $checkinStudents = u::first("SELECT COUNT(DISTINCT crm_student_id) AS total 
                FROM crm_student_checkin 
                WHERE checkin_branch_id = $branchId 
                AND status >= 1
                AND DATE_FORMAT(checkin_at, '%Y-%m') = '$currentMonth'");

            // Số học sinh đăng ký mới (hoàn thành học phí trong tháng)
            $registeredStudents = u::first("SELECT COUNT(DISTINCT student_id) AS total 
                FROM contracts 
                WHERE branch_id = $branchId 
                AND type > 0
                AND count_recharge = 0
                AND debt_amount = 0
                AND DATE_FORMAT(created_at, '%Y-%m') = '$currentMonth'");

            // Số học sinh hết phí trong tháng (chưa được nối phí)
            $expiredStudents = u::first("SELECT COUNT(DISTINCT c.student_id) AS total 
                FROM contracts c
                WHERE c.branch_id = $branchId 
                AND c.status = 6
                AND c.enrolment_last_date >= '$startOfMonth'
                AND c.enrolment_last_date <= '$today'
                AND c.left_sessions <= 0
                AND (SELECT COUNT(id) FROM contracts 
                     WHERE student_id = c.student_id 
                     AND count_recharge > c.count_recharge 
                     AND status != 7) = 0");

            // Tổng số học sinh active (đang học)
            $activeStudents = u::first("SELECT COUNT(DISTINCT student_id) AS total 
                FROM contracts 
                WHERE branch_id = $branchId 
                AND status = 6 
                AND type > 0");

            // Tổng số lớp chính thức
            $totalClasses = u::first("SELECT COUNT(id) AS total 
                FROM classes 
                WHERE branch_id = $branchId 
                AND status = 1
                AND cls_startdate <= '$today'
                AND cls_enddate >= '$today'");

            // Tính tỉ lệ ACS
            $acsRatio = '0';
            if ($totalClasses->total > 0) {
                $acsRatio = number_format($activeStudents->total / $totalClasses->total, 2);
            }

            $result[] = [
                'stt' => $index + 1,
                'branch_id' => $branchId,
                'branch_name' => $branch->branch_name,
                'checkin_students' => (int) $checkinStudents->total,
                'registered_students' => (int) $registeredStudents->total,
                'expired_students' => (int) $expiredStudents->total,
                'active_students' => (int) $activeStudents->total,
                'total_classes' => (int) $totalClasses->total,
                'acs_ratio' => $acsRatio
            ];
        }

        return response()->json($result);
    }

    public function dashboard19(Request $request)
    {
        if (data_get($request, 'branch_id')) {
            $cond = " AND b.id IN (" . implode(",", data_get($request, 'branch_id')) . ")";
        } else {
            $cond = " AND b.id IN (" . Auth::user()->getBranchesHasUser() . ") AND b.id > 10";
        }

        $currentMonth = date('Y-m');
        $monthT1 = date('Y-m', strtotime('+1 month'));
        $monthT2 = date('Y-m', strtotime('+2 months'));
        $monthT3 = date('Y-m', strtotime('+3 months'));

        $branches = u::query("SELECT b.id, b.name AS branch_name
            FROM branches AS b 
            WHERE b.status=1 $cond 
            ORDER BY b.id");

        $result = [];
        foreach ($branches as $index => $branch) {
            $branchId = $branch->id;

            // Tháng hiện tại (T)
            $renewT = u::first("SELECT 
                    COUNT(id) AS total_expired,
                    SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS total_renewed
                FROM report_renews 
                WHERE branch_id = $branchId 
                AND renewed_month = '$currentMonth'");
            
            $ratioT = '0/0';
            if ($renewT->total_expired > 0) {
                $ratioT = $renewT->total_renewed . '/' . $renewT->total_expired;
            }

            // Tháng T+1
            $renewT1 = u::first("SELECT 
                    COUNT(id) AS total_expired,
                    SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS total_renewed
                FROM report_renews 
                WHERE branch_id = $branchId 
                AND renewed_month = '$monthT1'");
            
            $ratioT1 = '0/0';
            if ($renewT1->total_expired > 0) {
                $ratioT1 = $renewT1->total_renewed . '/' . $renewT1->total_expired;
            }

            // Tháng T+2
            $renewT2 = u::first("SELECT 
                    COUNT(id) AS total_expired,
                    SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS total_renewed
                FROM report_renews 
                WHERE branch_id = $branchId 
                AND renewed_month = '$monthT2'");
            
            $ratioT2 = '0/0';
            if ($renewT2->total_expired > 0) {
                $ratioT2 = $renewT2->total_renewed . '/' . $renewT2->total_expired;
            }

            // Tháng T+3
            $renewT3 = u::first("SELECT 
                    COUNT(id) AS total_expired,
                    SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS total_renewed
                FROM report_renews 
                WHERE branch_id = $branchId 
                AND renewed_month = '$monthT3'");
            
            $ratioT3 = '0/0';
            if ($renewT3->total_expired > 0) {
                $ratioT3 = $renewT3->total_renewed . '/' . $renewT3->total_expired;
            }

            $result[] = [
                'stt' => $index + 1,
                'branch_id' => $branchId,
                'branch_name' => $branch->branch_name,
                'ratio_t' => $ratioT,
                'ratio_t1' => $ratioT1,
                'ratio_t2' => $ratioT2,
                'ratio_t3' => $ratioT3,
                'total_expired_t' => (int) $renewT->total_expired,
                'total_renewed_t' => (int) $renewT->total_renewed,
                'total_expired_t1' => (int) $renewT1->total_expired,
                'total_renewed_t1' => (int) $renewT1->total_renewed,
                'total_expired_t2' => (int) $renewT2->total_expired,
                'total_renewed_t2' => (int) $renewT2->total_renewed,
                'total_expired_t3' => (int) $renewT3->total_expired,
                'total_renewed_t3' => (int) $renewT3->total_renewed,
            ];
        }

        return response()->json($result);
    }
}

