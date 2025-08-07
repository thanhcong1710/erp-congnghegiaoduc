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
        if(data_get($request, 'branch_id')) {
            $cond= " AND b.branch_id IN (" . implode(",", data_get($request, 'branch_id')) .")";
        } else {
            $cond= " AND b.branch_id IN (" . Auth::user()->getBranchesHasUser().")";
        }
        $totalRevenueWeek = u::first("SELECT SUM(b.amount) AS total FROM payments AS b 
            WHERE b.charge_date >= '".$monday->format('Y-m-d')."' $cond");
       
        $data = [
            'totalRevenueWeek' => [
                'data' => round(data_get($totalRevenueWeek, 'total', 0)/1000000, 2)
            ]
        ];
        return response()->json($data);
    }
    public function dashboard02(Request $request)
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
                'data' => round(data_get($totalRevenueMonth, 'total', 0)/1000000, 2)
            ]
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
        if(data_get($request, 'branch_id')) {
            $cond= " AND c.branch_id IN (" . implode(",", data_get($request, 'branch_id')) .")";
        } else {
            $cond= " AND c.branch_id IN (" . Auth::user()->getBranchesHasUser().")";
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
        if(data_get($request, 'branch_id')) {
            $cond= " AND c.branch_id IN (" . implode(",", data_get($request, 'branch_id')) .")";
        } else {
            $cond= " AND c.branch_id IN (" . Auth::user()->getBranchesHasUser().")";
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
        if(data_get($request, 'branch_id')) {
            $cond= " AND c.branch_id IN (" . implode(",", data_get($request, 'branch_id')) .")";
        } else {
            $cond= " AND c.branch_id IN (" . Auth::user()->getBranchesHasUser().")";
        }
        $pieChartProduct = u::query("SELECT count( c.id ) AS total, c.product_id, (SELECT name FROM products WHERE id= c.product_id ) AS product_name 
            FROM contracts AS c  WHERE c.type > 0  AND c.STATUS != 7  AND c.init_total_charged > 0 $cond GROUP BY c.product_id");
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
            $cond= " AND c.branch_id IN (" . implode(",", data_get($request, 'branch_id')) .")";
        } else {
            $cond= " AND c.branch_id IN (" . Auth::user()->getBranchesHasUser().")";
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
        if(data_get($request, 'branch_id')) {
            $cond= " AND b.id IN (" . implode(",", data_get($request, 'branch_id')) .")";
            $cond1= " AND p.branch_id IN (" . implode(",", data_get($request, 'branch_id')) .")";
        } else {
            $cond= " AND b.id IN (" . Auth::user()->getBranchesHasUser().") AND b.id > 10";
            $cond1= " AND p.branch_id IN (" . Auth::user()->getBranchesHasUser() .") AND p.branch_id > 10";
        }
        $branches = u::query("SELECT b.name AS branch_name, b.code, b.id
            FROM branches AS b WHERE b.status=1 $cond");
        $categories = [];
        $series = [];
        $dataRevenue = [];
        foreach ($branches AS $row){
            $series[data_get($row,'id')] = [
                'name' => data_get($row, 'branch_name'),
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
                if(isset($series[$row->branch_id])){
                    $series[$row->branch_id]['data'][$i] = round(data_get($row, 'total')/1000000, 2) ;
                }
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

    public function dashboard09(Request $request)
    {
        if(data_get($request, 'branch_id')) {
            $cond= " AND b.id IN (" . implode(",", data_get($request, 'branch_id')) .")";
            $cond1= " AND p.branch_id IN (" . implode(",", data_get($request, 'branch_id')) .")";
        } else {
            $cond= " AND b.id IN (" . Auth::user()->getBranchesHasUser().") AND b.id > 10";
            $cond1= " AND p.branch_id IN (" . Auth::user()->getBranchesHasUser() .") AND p.branch_id > 10";
        }
        $branches = u::query("SELECT b.name AS branch_name, b.code, b.id
            FROM branches AS b WHERE b.status=1 $cond");
        $categories = [];
        $series = [];
        $dataRevenue = [];
        foreach ($branches AS $row){
            $series[data_get($row,'id')] = [
                'name' => data_get($row, 'branch_name'),
                'data' => [0, 0, 0, 0, 0, 0]
            ];
        }
        for($i=0; $i<6; $i++){
            $m = $i - 5;
            $tmp_date = date('Y-m', strtotime ( "$m month"));
            $categories[] = date('m/Y', strtotime ( "$m month"));
            $dataRevenue = u::query("SELECT SUM(p.amount) AS total, p.branch_id FROM payments AS p 
                LEFT JOIN contracts AS c ON c.id=p.contract_id
                WHERE DATE_FORMAT(p.charge_date,'%Y-%m')='$tmp_date' AND c.count_recharge > 0 $cond1 GROUP BY p.branch_id");
            foreach ($dataRevenue AS $row){
                if(isset($series[$row->branch_id])){
                    $series[$row->branch_id]['data'][$i] = round(data_get($row, 'total')/1000000, 2) ;
                }
            }
        }
        $resultSeries = [];
        foreach($series AS $s){
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
        if(data_get($request, 'branch_id')) {
            $cond= " AND b.id IN (" . implode(",", data_get($request, 'branch_id')) .")";
            $cond1= " AND branch_id IN (" . implode(",", data_get($request, 'branch_id')) .")";
        } else {
            $cond= " AND b.id IN (" . Auth::user()->getBranchesHasUser().") AND b.id > 10";
            $cond1= " AND branch_id IN (" . Auth::user()->getBranchesHasUser() .") AND branch_id > 10";
        }
        $branches = u::query("SELECT b.name AS branch_name, b.code, b.id
            FROM branches AS b WHERE b.status=1 $cond");
        $categories = [];
        $series = [];
        $dataRevenue = [];
        foreach ($branches AS $row){
            $series[data_get($row,'id')] = [
                'name' => data_get($row, 'branch_name'),
                'data' => [0, 0, 0, 0, 0, 0]
            ];
        }
        for($i=0; $i<6; $i++){
            $m = $i - 5;
            $tmp_date = date('Y-m', strtotime ( "$m month"));
            $categories[] = date('m/Y', strtotime ( "$m month"));
            $dataRevenue = u::query("SELECT count(id) AS total, branch_id,
                    SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS total_renew
                FROM report_renews WHERE renewed_month='$tmp_date' $cond1 GROUP BY branch_id");
            foreach ($dataRevenue AS $row){
                if(isset($series[$row->branch_id])){
                    $series[$row->branch_id]['data'][$i] = data_get($row, 'total') ? round((data_get($row, 'total_renew') /data_get($row, 'total'))*100, 2) : 0 ;
                }
            }
        }
        $resultSeries = [];
        foreach($series AS $s){
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
        $series = [
            '0' => [
                'name' => date('Y/m').'(thành công)',
                'group' => date('Y-m'),
                'data' => []
            ],
            '1' => [
                'name' => date('Y/m').'(thất bại)',
                'group' => date('Y-m'),
                'data' => []
            ],
            '2' => [
                'name' => date('Y/m', strtotime('+1 month')).'(thành công)',
                'group' => date('Y-m', strtotime('+1 month')),
                'data' => []
            ],
            '3' => [
                'name' => date('Y/m', strtotime('+1 month')).'(thất bại)',
                'group' => date('Y-m', strtotime('+1 month')),
                'data' => []
            ],
            '4' => [
                'name' => date('Y/m', strtotime('+2 month')).'(thành công)',
                'group' => date('Y-m', strtotime('+2 month')),
                'data' => []
            ],
            '5' => [
                'name' => date('Y/m', strtotime('+2 month')).'(thất bại)',
                'group' => date('Y-m', strtotime('+2 month')),
                'data' => []
            ],
        ];
        foreach ($CMs AS $cm){
            $dataRenew = u::first("SELECT count(id) AS total, SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS total_success FROM report_renews WHERE renewed_month = '".date('Y-m')."' AND cm_id=".data_get($cm, 'user_id', 0));
            $dataRenewT1 = u::first("SELECT count(id) AS total, SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS total_success FROM report_renews WHERE renewed_month = '".date('Y-m', strtotime('+1 month'))."' AND cm_id=".data_get($cm, 'user_id', 0));
            $dataRenewT2 = u::first("SELECT count(id) AS total, SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS total_success FROM report_renews WHERE renewed_month = '".date('Y-m', strtotime('+2 month'))."' AND cm_id=".data_get($cm, 'user_id', 0));
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
        if(data_get($request, 'branch_id')) {
            $cond= " AND c.branch_id IN (" . implode(",", data_get($request, 'branch_id')) .")";
        } else {
            $cond= " AND c.branch_id IN (" . Auth::user()->getBranchesHasUser().")";
        }
        if(Auth::user()->checkRole('68')){
            $cond.= " AND c.ec_id = " . Auth::user()->id;
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
        if(data_get($request, 'branch_id')) {
            $cond= " AND c.branch_id IN (" . implode(",", data_get($request, 'branch_id')) .")";
        } else {
            $cond= " AND c.branch_id IN (" . Auth::user()->getBranchesHasUser().")";
        }
        if(Auth::user()->checkRole('68')){
            $cond.= " AND c.ec_id = " . Auth::user()->id;
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
        if(data_get($request, 'branch_id')) {
            $cond= " AND c.branch_id IN (" . implode(",", data_get($request, 'branch_id')) .")";
        } else {
            $cond= " AND c.branch_id IN (" . Auth::user()->getBranchesHasUser().")";
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
        if(data_get($request, 'branch_id')) {
            $cond= " AND c.branch_id IN (" . implode(",", data_get($request, 'branch_id')) .")";
        } else {
            $cond= " AND c.branch_id IN (" . Auth::user()->getBranchesHasUser().")";
        }
        $data = u::query("SELECT s.id AS student_id, s.name, s.lms_id, c.enrolment_last_date, cl.cls_name
            FROM contracts AS c 
                LEFT JOIN students AS s ON s.id = c.student_id
                LEFT JOIN classes AS cl ON cl.id = c.class_id 
            WHERE c.status=6 AND c.class_id IS NOT NULL AND c.left_sessions <= 0 $cond ORDER BY c.enrolment_last_date");
        return response()->json($data);
    }
}
