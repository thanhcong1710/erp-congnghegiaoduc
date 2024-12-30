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
    public function index(Request $request)
    {
        if(data_get($request, 'branch_id')) {
            $cond= " AND c.branch_id IN (" . implode(",", data_get($request, 'branch_id')) .")";
        } else {
            $cond= " AND c.branch_id IN (" . Auth::user()->getBranchesHasUser().")";
        }
        $numDashStudent = u::first("SELECT count(DISTINCT c.student_id) AS total FROM contracts AS c WHERE c.status=6 AND c.type>0 $cond");
        $numDashClass = u::first("SELECT count(DISTINCT c.id) AS total FROM classes AS c WHERE c.status=1 AND c.type=1 
            AND c.cls_startdate <= CURRENT_DATE AND c.cls_enddate >= CURRENT_DATE $cond");
        $numDashTeacher = u::first("SELECT count(DISTINCT u.id) AS total 
            FROM users AS u 
                LEFT JOIN role_has_user AS ru ON ru.user_id =u.id
                LEFT JOIN branch_has_user AS c ON c.user_id =u.id
            WHERE ru.role_id IN (".SystemCode::ROLE_TEACHER."," .SystemCode::ROLE_TEACHER_LEADER.") 
                AND u.status=1 $cond");
        $numDashRooms = u::first("SELECT count(DISTINCT c.id) AS total FROM rooms AS c WHERE c.status=1 $cond");

        $studentActive = u::first("SELECT count(DISTINCT c.student_id) AS total FROM contracts AS c WHERE c.status=6 AND c.type>0 AND c.debt_amount=0 $cond");
        $studentTrial = u::first("SELECT count(DISTINCT c.student_id) AS total FROM contracts AS c WHERE c.status=6 AND c.type=0 $cond");
        $studentWithdraw = u::first("SELECT count(DISTINCT c.student_id) AS total FROM contracts AS c WHERE c.status=7 AND c.type>0 
            AND (SELECT count(id) FROM contracts WHERE student_id=c.student_id AND type>0 AND status!=7)=0 $cond");
        $studentPending = u::first("SELECT count(DISTINCT c.student_id) AS total FROM contracts AS c WHERE c.status!=6 AND c.status!=7 AND c.type>0 AND c.debt_amount=0
            AND (SELECT count(id) FROM contracts WHERE student_id=c.student_id AND c.status=6)=0 $cond");
        
        $pieChartProduct = u::query("SELECT count( c.id ) AS total, c.product_id, (SELECT name FROM products WHERE id= c.product_id ) AS product_name 
            FROM contracts AS c  WHERE c.type > 0  AND c.STATUS != 7  AND c.debt_amount = 0 $cond GROUP BY c.product_id");
        $labelsChartProduct =[];
        $seriesChartProduct =[];
        foreach($pieChartProduct AS $product){
            $labelsChartProduct[] = data_get($product, 'product_name');
            $seriesChartProduct[] = data_get($product, 'total');
        }
        $data = [
            'numDashStudent' => [
                'series' => [ 0, 0, 0, 0, 0, data_get($numDashStudent, 'total')],
                'data' => data_get($numDashStudent, 'total')
            ],
            'numDashClass' => [
                'series' => [ 0, 0, 0, 0, 0, data_get($numDashClass, 'total')],
                'data' => data_get($numDashClass, 'total')
            ],
            'numDashTeacher' => [
                'series' => [ 0, 0, 0, 0, 0, data_get($numDashTeacher, 'total')],
                'data' => data_get($numDashTeacher, 'total')
            ],
            'numDashRooms' => [
                'series' => [ 0, 0, 0, 0, 0, data_get($numDashRooms, 'total')],
                'data' => data_get($numDashRooms, 'total')
            ],
            'pieChartStudent' => [
                'studentActive' => data_get($studentActive, 'total', 0),
                'studentTrial' => data_get($studentTrial, 'total', 0),
                'studentWithdraw' => data_get($studentWithdraw, 'total', 0),
                'studentPending' => data_get($studentPending, 'total', 0),
            ],
            'pieChartProduct' => [
                'labelsChartProduct' => $labelsChartProduct,
                'seriesChartProduct' => $seriesChartProduct
            ]

        ];
        return response()->json($data);
    }
}
