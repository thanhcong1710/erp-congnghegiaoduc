<?php

namespace App\Http\Controllers;

use App\Enums\SystemCode;
use App\User;
use App\Http\Controllers\Controller;
use App\Models\LogStudents;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function overview(Request $request) {
        $user = Auth::user();
        $userRoles = u::query("SELECT role_id FROM role_has_user WHERE user_id = {$user->id}");
        $roles = [];
        foreach($userRoles as $ur) {
            $roles[] = $ur->role_id;
        }

        $is_leader = in_array(69, $roles);
        $is_sales = in_array(68, $roles);
        $is_admin = !($is_leader || $is_sales) || in_array(1, $roles); 

        // Base Condition for branch
        $req_branch_id = data_get($request, 'branch_id');
        if($req_branch_id) {
            if(!is_array($req_branch_id)) $req_branch_id = [$req_branch_id];
            $branch_cond = " AND branch_id IN (" . implode(",", $req_branch_id) . ")";
            $branch_cond_a = " AND a.branch_id IN (" . implode(",", $req_branch_id) . ")";
            $branch_cond_p = " AND p.branch_id IN (" . implode(",", $req_branch_id) . ")";
        } else {
            $branch_cond = " AND branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";
            $branch_cond_a = " AND a.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";
            $branch_cond_p = " AND p.branch_id IN (" . Auth::user()->getBranchesHasUser() . ")";
        }

        $current_month_start = date('Y-m-01 00:00:00');
        $current_year_start = date('Y-01-01 00:00:00');

        $data = [
            'roles' => $roles,
            'is_admin' => $is_admin,
            'is_leader' => $is_leader,
            'is_sales' => $is_sales,
        ];

        // Prepare last 6 months dates
        $last_6_months_labels = [];
        $last_6_months_starts = [];
        $last_6_months_ends = [];
        for ($i = 5; $i >= 0; $i--) {
            $last_6_months_labels[] = date('m/Y', strtotime("-$i month"));
            $last_6_months_starts[] = date('Y-m-01 00:00:00', strtotime("-$i month"));
            $last_6_months_ends[] = date('Y-m-t 23:59:59', strtotime("-$i month"));
        }

        // 1. SALES DATA
        if ($is_sales) {
            $ec_id = $user->id;
            
            // Current Month
            $new_cm = u::first("SELECT COUNT(id) as c, SUM(must_charge) as v FROM agreements WHERE ec_id=$ec_id AND count_recharge=0 AND created_at >= '$current_month_start' $branch_cond");
            $up_cm = u::first("SELECT COUNT(id) as c, SUM(must_charge) as v FROM agreements WHERE ec_id=$ec_id AND count_recharge>0 AND created_at >= '$current_month_start' $branch_cond");
            $rev_cm = u::first("SELECT SUM(amount) as v FROM payments WHERE ec_id=$ec_id AND charge_date >= '$current_month_start' $branch_cond");
            
            // Current Year
            $tot_cy = u::first("SELECT COUNT(id) as c, SUM(must_charge) as v, SUM(debt_amount) as d FROM agreements WHERE ec_id=$ec_id AND created_at >= '$current_year_start' $branch_cond");
            $rev_cy = u::first("SELECT SUM(amount) as v FROM payments WHERE ec_id=$ec_id AND charge_date >= '$current_year_start' $branch_cond");

            $data['sales'] = [
                'current_month' => [
                    'new_contracts' => (int)data_get($new_cm, 'c', 0),
                    'new_contracts_value' => (float)data_get($new_cm, 'v', 0),
                    'uplevel_contracts' => (int)data_get($up_cm, 'c', 0),
                    'uplevel_contracts_value' => (float)data_get($up_cm, 'v', 0),
                    'revenue' => (float)data_get($rev_cm, 'v', 0),
                ],
                'current_year' => [
                    'total_contracts' => (int)data_get($tot_cy, 'c', 0),
                    'total_contracts_value' => (float)data_get($tot_cy, 'v', 0),
                    'total_revenue' => (float)data_get($rev_cy, 'v', 0),
                    'total_debt' => (float)data_get($tot_cy, 'd', 0),
                ]
            ];
        }

        // 2. LEADER DATA
        if ($is_leader) {
            $team_id = $user->id;
            $team_cond = " AND (ec_leader_id = $team_id OR (ec_leader_id IS NULL AND ec_id = $team_id)) ";
            $team_cond_a = " AND (a.ec_leader_id = $team_id OR (a.ec_leader_id IS NULL AND a.ec_id = $team_id)) ";
            // Wait, payments doesn't have ec_leader_id, we check agreements
            $team_cond_p = " AND p.agreement_id IN (SELECT id FROM agreements WHERE (ec_leader_id = $team_id OR (ec_leader_id IS NULL AND ec_id = $team_id))) ";

            // Current Month
            $new_cm_l = u::first("SELECT COUNT(id) as c, SUM(must_charge) as v FROM agreements WHERE count_recharge=0 AND created_at >= '$current_month_start' $team_cond $branch_cond");
            $up_cm_l = u::first("SELECT COUNT(id) as c, SUM(must_charge) as v FROM agreements WHERE count_recharge>0 AND created_at >= '$current_month_start' $team_cond $branch_cond");
            $rev_cm_l = u::first("SELECT SUM(amount) as v FROM payments p WHERE charge_date >= '$current_month_start' $team_cond_p $branch_cond_p");
            
            // Current Year
            $tot_cy_l = u::first("SELECT COUNT(id) as c, SUM(must_charge) as v, SUM(debt_amount) as d FROM agreements WHERE created_at >= '$current_year_start' $team_cond $branch_cond");
            $rev_cy_l = u::first("SELECT SUM(amount) as v FROM payments p WHERE charge_date >= '$current_year_start' $team_cond_p $branch_cond_p");

            // Members Revenue (Current Month)
            $members = u::query("
                SELECT u.name, 
                       (SELECT COUNT(id) FROM agreements WHERE ec_id = u.id AND created_at >= '$current_month_start') as contracts,
                       (SELECT COALESCE(SUM(amount), 0) FROM payments WHERE ec_id = u.id AND charge_date >= '$current_month_start') as revenue
                FROM users u 
                WHERE u.id IN (SELECT DISTINCT ec_id FROM agreements WHERE (ec_leader_id = $team_id OR ec_id = $team_id))
                ORDER BY revenue DESC
                LIMIT 10
            ");

            // 6 Months Revenue for Leader
            $chart_6m = [];
            for ($i = 0; $i < 6; $i++) {
                $start = $last_6_months_starts[$i];
                $end = $last_6_months_ends[$i];
                $rev = u::first("SELECT SUM(amount) as v FROM payments p WHERE charge_date >= '$start' AND charge_date <= '$end' $team_cond_p $branch_cond_p");
                $chart_6m[] = (float)data_get($rev, 'v', 0);
            }

            $data['leader'] = [
                'current_month' => [
                    'new_contracts' => (int)data_get($new_cm_l, 'c', 0),
                    'new_contracts_value' => (float)data_get($new_cm_l, 'v', 0),
                    'uplevel_contracts' => (int)data_get($up_cm_l, 'c', 0),
                    'uplevel_contracts_value' => (float)data_get($up_cm_l, 'v', 0),
                    'revenue' => (float)data_get($rev_cm_l, 'v', 0),
                ],
                'current_year' => [
                    'total_contracts' => (int)data_get($tot_cy_l, 'c', 0),
                    'total_contracts_value' => (float)data_get($tot_cy_l, 'v', 0),
                    'total_revenue' => (float)data_get($rev_cy_l, 'v', 0),
                    'total_debt' => (float)data_get($tot_cy_l, 'd', 0),
                ],
                'members' => $members,
                'chart_6m' => [
                    'labels' => $last_6_months_labels,
                    'data' => $chart_6m
                ]
            ];
        }

        // 3. ADMIN DATA
        if ($is_admin) {
            // Current Month
            $new_cm_a = u::first("SELECT COUNT(id) as c, SUM(must_charge) as v FROM agreements WHERE count_recharge=0 AND created_at >= '$current_month_start' $branch_cond");
            $up_cm_a = u::first("SELECT COUNT(id) as c, SUM(must_charge) as v FROM agreements WHERE count_recharge>0 AND created_at >= '$current_month_start' $branch_cond");
            $rev_cm_a = u::first("SELECT SUM(amount) as v FROM payments p WHERE charge_date >= '$current_month_start' $branch_cond_p");
            
            // Current Year
            $tot_cy_a = u::first("SELECT COUNT(id) as c, SUM(must_charge) as v, SUM(debt_amount) as d FROM agreements WHERE created_at >= '$current_year_start' $branch_cond");
            $rev_cy_a = u::first("SELECT SUM(amount) as v FROM payments p WHERE charge_date >= '$current_year_start' $branch_cond_p");

            // Students
            $total_st = u::first("SELECT count(id) as c FROM students WHERE status > 0 AND branch_id IN (" . Auth::user()->getBranchesHasUser() . ")");
            $active_st = u::first("SELECT count(DISTINCT student_id) as c FROM contracts WHERE status=6 AND type>0 AND branch_id IN (" . Auth::user()->getBranchesHasUser() . ")");

            // Teams
            $teams = u::query("
                SELECT u.name, 
                       (SELECT COUNT(id) FROM agreements WHERE (ec_leader_id = u.id OR (ec_leader_id IS NULL AND ec_id = u.id)) AND created_at >= '$current_month_start') as contracts,
                       (SELECT COALESCE(SUM(amount), 0) FROM payments p WHERE p.agreement_id IN (SELECT id FROM agreements WHERE (ec_leader_id = u.id OR (ec_leader_id IS NULL AND ec_id = u.id))) AND charge_date >= '$current_month_start') as revenue
                FROM users u 
                WHERE u.id IN (SELECT DISTINCT ec_leader_id FROM agreements WHERE ec_leader_id IS NOT NULL)
                ORDER BY revenue DESC
                LIMIT 10
            ");

            // 6 Months Revenue for Admin
            $chart_6m_a = [];
            for ($i = 0; $i < 6; $i++) {
                $start = $last_6_months_starts[$i];
                $end = $last_6_months_ends[$i];
                $rev = u::first("SELECT SUM(amount) as v FROM payments p WHERE charge_date >= '$start' AND charge_date <= '$end' $branch_cond_p");
                $chart_6m_a[] = (float)data_get($rev, 'v', 0);
            }

            $data['admin'] = [
                'current_month' => [
                    'new_contracts' => (int)data_get($new_cm_a, 'c', 0),
                    'new_contracts_value' => (float)data_get($new_cm_a, 'v', 0),
                    'uplevel_contracts' => (int)data_get($up_cm_a, 'c', 0),
                    'uplevel_contracts_value' => (float)data_get($up_cm_a, 'v', 0),
                    'revenue' => (float)data_get($rev_cm_a, 'v', 0),
                ],
                'current_year' => [
                    'total_contracts' => (int)data_get($tot_cy_a, 'c', 0),
                    'total_contracts_value' => (float)data_get($tot_cy_a, 'v', 0),
                    'total_revenue' => (float)data_get($rev_cy_a, 'v', 0),
                    'total_debt' => (float)data_get($tot_cy_a, 'd', 0),
                ],
                'total_students' => (int)data_get($total_st, 'c', 0),
                'active_students' => (int)data_get($active_st, 'c', 0),
                'teams' => $teams,
                'chart_6m' => [
                    'labels' => $last_6_months_labels,
                    'data' => $chart_6m_a
                ]
            ];
        }

        return response()->json($data);
    }

    public function dashboard01(Request $request)
    {
        if(data_get($request, 'branch_id')) {
            $cond= " AND c.branch_id IN (" . implode(",", data_get($request, 'branch_id')) .")";
        } else {
            $cond= " AND c.branch_id IN (" . Auth::user()->getBranchesHasUser().")";
        }
        $numDashStudent = u::first("SELECT count(DISTINCT c.student_id) AS total FROM contracts AS c WHERE c.status=6 AND c.type>0 $cond");
       
        $data = [
            'numDashStudent' => [
                'series' => [ 0, 0, 0, 0, 0, data_get($numDashStudent, 'total')],
                'data' => data_get($numDashStudent, 'total')
            ]
        ];
        return response()->json($data);
    }

    public function dashboard02(Request $request)
    {
        if(data_get($request, 'branch_id')) {
            $cond= " AND c.branch_id IN (" . implode(",", data_get($request, 'branch_id')) .")";
        } else {
            $cond= " AND c.branch_id IN (" . Auth::user()->getBranchesHasUser().")";
        }
        $numDashClass = u::first("SELECT count(DISTINCT c.id) AS total FROM classes AS c WHERE c.status=1 AND c.type=1 
            AND c.cls_startdate <= CURRENT_DATE AND c.cls_enddate >= CURRENT_DATE $cond");
        $data = [
            'numDashClass' => [
                'series' => [ 0, 0, 0, 0, 0, data_get($numDashClass, 'total')],
                'data' => data_get($numDashClass, 'total')
            ],
        ];
        return response()->json($data);
    }

    public function dashboard03(Request $request)
    {
        if(data_get($request, 'branch_id')) {
            $cond= " AND c.branch_id IN (" . implode(",", data_get($request, 'branch_id')) .")";
        } else {
            $cond= " AND c.branch_id IN (" . Auth::user()->getBranchesHasUser().")";
        }
        $numDashTeacher = u::first("SELECT count(DISTINCT u.id) AS total 
            FROM users AS u 
                LEFT JOIN role_has_user AS ru ON ru.user_id =u.id
                LEFT JOIN branch_has_user AS c ON c.user_id =u.id
            WHERE ru.role_id IN (".SystemCode::ROLE_TEACHER."," .SystemCode::ROLE_TEACHER_LEADER.") 
                AND u.status=1 $cond");
        $data = [
            'numDashTeacher' => [
                'series' => [ 0, 0, 0, 0, 0, data_get($numDashTeacher, 'total')],
                'data' => data_get($numDashTeacher, 'total')
            ],
        ];
        return response()->json($data);
    }

    public function dashboard04(Request $request)
    {
        if(data_get($request, 'branch_id')) {
            $cond= " AND c.branch_id IN (" . implode(",", data_get($request, 'branch_id')) .")";
        } else {
            $cond= " AND c.branch_id IN (" . Auth::user()->getBranchesHasUser().")";
        }
        $numDashRooms = u::first("SELECT count(DISTINCT c.id) AS total FROM rooms AS c WHERE c.status=1 $cond");
        $data = [
            'numDashRooms' => [
                'series' => [ 0, 0, 0, 0, 0, data_get($numDashRooms, 'total')],
                'data' => data_get($numDashRooms, 'total')
            ],
        ];
        return response()->json($data);
    }

    public function dashboard05(Request $request)
    {
        if(data_get($request, 'branch_id')) {
            $cond= " AND c.branch_id IN (" . implode(",", data_get($request, 'branch_id')) .")";
        } else {
            $cond= " AND c.branch_id IN (" . Auth::user()->getBranchesHasUser().")";
        }
        $studentActive = u::first("SELECT count(DISTINCT c.student_id) AS total FROM contracts AS c WHERE c.status=6 AND c.type>0 AND c.debt_amount=0 $cond");
        $studentTrial = u::first("SELECT count(DISTINCT c.student_id) AS total FROM contracts AS c WHERE c.status=6 AND c.type=0 $cond");
        $studentWithdraw = u::first("SELECT count(DISTINCT c.student_id) AS total FROM contracts AS c WHERE c.status=7 AND c.type>0 
            AND (SELECT count(id) FROM contracts WHERE student_id=c.student_id AND type>0 AND status!=7)=0 $cond");
        $studentPending = u::first("SELECT count(DISTINCT c.student_id) AS total FROM contracts AS c WHERE c.status!=6 AND c.status!=7 AND c.type>0 AND c.debt_amount=0
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
        if(data_get($request, 'branch_id')) {
            $cond= " AND c.branch_id IN (" . implode(",", data_get($request, 'branch_id')) .")";
        } else {
            $cond= " AND c.branch_id IN (" . Auth::user()->getBranchesHasUser().")";
        }
        $pieChartProduct = u::query("SELECT count( c.id ) AS total, c.product_id, (SELECT name FROM products WHERE id= c.product_id ) AS product_name 
            FROM contracts AS c  WHERE c.type > 0  AND c.STATUS != 7  AND c.debt_amount = 0 $cond GROUP BY c.product_id");
        $labelsChartProduct =[];
        $seriesChartProduct =[];
        foreach($pieChartProduct AS $product){
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
        if(data_get($request, 'branch_id')) {
            $cond= " AND b.id IN (" . implode(",", data_get($request, 'branch_id')) .")";
            $cond1= " AND p.branch_id IN (" . implode(",", data_get($request, 'branch_id')) .")";
        } else {
            $cond= " AND b.id IN (" . Auth::user()->getBranchesHasUser().")";
            $cond1= " AND p.branch_id IN (" . Auth::user()->getBranchesHasUser() .")";
        }
        $branches = u::query("SELECT b.name AS branch_name, b.code, b.id
            FROM branches AS b WHERE b.status=1 $cond");
        $categories = [];
        $series = [];
        $dataRevenue = [];
        foreach ($branches AS $row){
            $series[data_get($row,'id')] = [
                'name' => str_replace('Trung tâm Scots English','SE',data_get($row, 'branch_name')),
                'data' => [0, 0, 0, 0, 0, 0]
            ];
        }
        for($i=0; $i<6; $i++){
            $m = $i - 5;
            $tmp_date = date('Y-m', strtotime ( "$m month"));
            $categories[] = date('m/Y', strtotime ( "$m month"));
            $dataRevenue = u::query("SELECT SUM(p.amount) AS total, p.branch_id FROM payments AS p 
                WHERE DATE_FORMAT(p.charge_date,'%Y-%m')='$tmp_date' $cond1 GROUP BY p.branch_id");
            foreach ($dataRevenue AS $row){
                $series[$row->branch_id]['data'][$i] = round(data_get($row, 'total')/1000000, 2) ;
            }
        }
        $resultSeries = [];
        foreach($series AS $s){
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

    public function dashboard08(Request $request)
    {
        if(data_get($request, 'branch_id')) {
            $cond= " AND b.branch_id IN (" . implode(",", data_get($request, 'branch_id')) .")";
        } else {
            $cond= " AND b.branch_id IN (" . Auth::user()->getBranchesHasUser().")";
        }
        $ECs = u::query("SELECT CONCAT(u.hrm_id,'-', u.name) AS ec_name , b.user_id
            FROM branch_has_user AS b LEFT JOIN users AS u ON u.id=b.user_id 
            LEFT JOIN role_has_user AS r ON r.user_id = u.id
            WHERE u.status=1 AND r.role_id IN(".SystemCode::ROLE_EC.",".SystemCode::ROLE_EC_LEADER.") $cond");
        $categories = [];
        $series = [];
        $dataRevenue = [];
        foreach ($ECs AS $row){
            $series[data_get($row,'user_id')] = [
                'name' => data_get($row, 'ec_name'),
                'data' => [0, 0, 0, 0, 0, 0]
            ];
        }
        for($i=0; $i<6; $i++){
            $m = $i - 5;
            $tmp_date = date('Y-m', strtotime ( "$m month"));
            $categories[] = date('m/Y', strtotime ( "$m month"));
            $dataRevenue = u::query("SELECT SUM(b.amount) AS total, b.ec_id FROM payments AS b 
                WHERE DATE_FORMAT(b.charge_date,'%Y-%m')='$tmp_date' $cond GROUP BY b.ec_id");
            foreach ($dataRevenue AS $row){
                if(isset($series[$row->ec_id])){
                    $series[$row->ec_id]['data'][$i] = round(data_get($row, 'total')/1000000, 2) ;
                }
            }
        }
        $resultSeries = [];
        foreach($series AS $s){
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
    
    public function dashboard09(Request $request)
    {
        if(data_get($request, 'branch_id')) {
            $cond= " AND b.branch_id IN (" . implode(",", data_get($request, 'branch_id')) .")";
        } else {
            $cond= " AND b.branch_id IN (" . Auth::user()->getBranchesHasUser().")";
        }
        $totalRevenueDay = u::first("SELECT SUM(b.amount) AS total FROM payments AS b 
            WHERE b.charge_date='".date('Y-m-d')."' $cond");
       
        $data = [
            'totalRevenueDay' => [
                'series' => [ 0, 0, 0, 0, 0, round(data_get($totalRevenueDay, 'total', 0)/1000000, 2)],
                'data' => round(data_get($totalRevenueDay, 'total', 0)/1000000, 2)
            ]
        ];
        return response()->json($data);
    }

    public function dashboard10(Request $request)
    {
        if(data_get($request, 'branch_id')) {
            $cond= " AND b.branch_id IN (" . implode(",", data_get($request, 'branch_id')) .")";
        } else {
            $cond= " AND b.branch_id IN (" . Auth::user()->getBranchesHasUser().")";
        }
        $totalRevenueMonth = u::first("SELECT SUM(b.amount) AS total FROM payments AS b 
            WHERE DATE_FORMAT(b.charge_date,'%Y-%m')='".date('Y-m')."' $cond");
       
        $data = [
            'totalRevenueMonth' => [
                'series' => [ 0, 0, 0, 0, 0, round(data_get($totalRevenueMonth, 'total', 0)/1000000, 2)],
                'data' => round(data_get($totalRevenueMonth, 'total', 0)/1000000, 2)
            ]
        ];
        return response()->json($data);
    }

    public function dashboard11(Request $request)
    {
        if(data_get($request, 'branch_id')) {
            $cond= " AND b.id IN (" . implode(",", data_get($request, 'branch_id')) .")";
        } else {
            $cond= " AND b.id IN (" . Auth::user()->getBranchesHasUser().")";
        }
        $branches = u::query("SELECT b.name AS branch_name, b.code, b.id
            FROM branches AS b WHERE b.status=1 $cond");
        $categories = [];
        $arr1 = [44, 55, 41, 37, 22, 43, 21];
        $arr2 = [53, 32, 33, 52, 13, 43, 32];
        $dataRenew = [];
        $dataFalseRenew = [];
        $i=0;
        foreach ($branches AS $row){
            $categories[] = str_replace('Trung tâm Scots English','SE',data_get($row, 'branch_name'));
            $dataRenew[] = $arr1[$i];
            $dataFalseRenew[] = $arr2[2];
            $i++;
        }
        $data = [
            'lineChartRenew' => [
                'dataRenew' => $dataRenew,
                'dataFalseRenew' => $dataFalseRenew,
                'categories' => $categories
            ]
        ];
        return response()->json($data);
    }
    public function dashboard12(Request $request)
    {
        if(data_get($request, 'branch_id')) {
            $cond= " AND b.branch_id IN (" . implode(",", data_get($request, 'branch_id')) .")";
        } else {
            $cond= " AND b.branch_id IN (" . Auth::user()->getBranchesHasUser().")";
        }
        $CMs = u::query("SELECT CONCAT(u.hrm_id,'-', u.name) AS cm_name , b.user_id
            FROM branch_has_user AS b LEFT JOIN users AS u ON u.id=b.user_id 
            LEFT JOIN role_has_user AS r ON r.user_id = u.id
            WHERE u.status=1 AND r.role_id IN(".SystemCode::ROLE_CM.",".SystemCode::ROLE_CM_LEADER.") $cond");
        
        $categories = [];
        $arr1 = [44, 55, 41, 37, 22, 43, 21];
        $arr2 = [53, 32, 33, 52, 13, 43, 32];
        $dataRenew = [];
        $dataFalseRenew = [];
        $i=0;
        foreach ($CMs AS $row){
            $categories[] = data_get($row, 'cm_name');
            $dataRenew[] = $arr1[$i];
            $dataFalseRenew[] = $arr2[2];
            $i++;
        }
        $data = [
            'lineChartRenewAF' => [
                'dataRenew' => $dataRenew,
                'dataFalseRenew' => $dataFalseRenew,
                'categories' => $categories
            ]
        ];
        return response()->json($data);
    }
}
