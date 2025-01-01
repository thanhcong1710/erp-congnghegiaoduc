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
                'name' => str_replace('Trung tâm','',data_get($row, 'branch_name')),
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
}
