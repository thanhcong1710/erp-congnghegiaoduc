<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use App\Models\LogStudents;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartnerController extends Controller
{
    public function getDataCreverse(Request $request)
    {
        $apiKey = $request->header('X-API-KEY');
        if($apiKey != 'ybhKQGBEuHWIo7RK66VrcrKSnEE1naTVtA03zzJlGRs='){
            $data = [
                'data' => null,
                'succeeded' => false,
                'errors'=> [
                    "key invalid"
                ]
            ];
        } else {
            $command = $request->command;
            $start_time = $request->start_time;
            $end_time = $request->end_time;
            if(strtotime($end_time) - strtotime($start_time) > 90*24*3600 ){
                $data = [
                    'data' => "Date range cannot exceed 90 days and end date must be after start date",
                    'succeeded' => false,
                    'errors'=> [
                        "Date range cannot exceed 90 days and end date must be after start date"
                    ]
                ];
            } else {
                $students = u::query("SELECT p.charge_date AS payment_date, s.name AS stu_name, s.lms_id AS std_id, c.branch_id, c.product_id, c.program_id,
                        p.must_charge AS total_fee, p.amount AS payment_amount, p.debt AS remaining_amount, 
                        p.total, t.number_of_months, t.session, c.start_date, p.contract_id,
                        (SELECT min(charge_date) FROM payments WHERE contract_id = p.contract_id) AS period_from,
                        (SELECT max(charge_date) FROM payments WHERE contract_id = p.contract_id) AS period_to, 
                        c.note, p.method, p.id AS payment_id,
                        (SELECT `name` FROM programs WHERE id =pg.parent_id) AS `level`
                    FROM payments AS p 
                        LEFT JOIN contracts AS c ON c.id=p.contract_id 
                        LEFT JOIN students AS s ON s.id=p.student_id
                        LEFT JOIN tuition_fee AS t ON t.id =c.init_tuition_fee_id
                        LEFT JOIN programs AS pg ON pg.id= c.program_id
                    WHERE p.charge_date >='$start_time' AND p.charge_date <= '$end_time' AND s.lms_id IS NOT NULL ORDER BY p.charge_date");
                $list = [];
                foreach($students AS $row){
                    $number_of_months = data_get($row, 'number_of_months');
                    $ug_type = $this->getCodeByProductId(data_get($row,'product_id'));
                    $branch_id = data_get($row, 'branch_id');
                    $branch_lms_info = u::first("SELECT * FROM lms_branches WHERE branch_id = $branch_id AND ug_type = '$ug_type' ");
                    $list[] = [
                        'payment_date'=> data_get($row,'payment_date'),
                        'stu_name'=> data_get($row,'stu_name'),
                        'stu_id'=> data_get($row,'std_id'),
                        'brch_id'=> data_get($branch_lms_info,'brch_id'),
                        'branch_name'=> data_get($branch_lms_info,'brch_name'),
                        'branch_location'=> data_get($branch_lms_info,'branch_location'),
                        'band'=> data_get($branch_lms_info,'ug_type'),
                        'level'=> data_get($row,'level'),
                        'total_fee'=> data_get($row,'total_fee'),
                        'payment_id'=> data_get($row,'payment_id'),
                        'payment_amount'=> data_get($row,'payment_amount'),
                        'payment_method'=> data_get($row,'method') ==1 ? 'BankTransfer' : 'Cash',
                        'payment_type'=> data_get($row,'remaining_amount') > 0 ? 'Deposit' : (data_get($row,'payment_amount') == data_get($row,'total_fee') ? 'Full Fee' : 'Final Payment'),
                        'remaining_amount'=> data_get($row,'remaining_amount'),
                        'period_from'=> data_get($row,'period_from'),
                        'period_to'=> data_get($row,'period_to'),
                        'note'=> data_get($row,'note'),
                        'sp_start_time'=> data_get($row,'start_date'),
                        'sp_end_time'=> date('Y-m-d', strtotime("+$number_of_months months", strtotime(data_get($row,'start_date')))),
                        'num_of_sessions'=> data_get($row,'session'),
                        'course_type'=> $number_of_months."M"
                    ];
                }
                $data = [
                    'data' => [
                        'command' => 'stu_payment_info',
                        'info' => $list
                    ],
                    'succeeded' => true,
                    'errors'=> []
                ];
            }
        }
        
        return response()->json($data);
    }

    private function getCodeByProductId($product_id)
    {
        $type = null;
        switch ($product_id) {
            case 1:
                $type = 'APRIL';
                break;
            case 2:
                $type = 'i-GARTEN';
                break;
            case 3:
                $type = 'CDI';
                break;
            default:
                break;
        }
        return $type;
    }
}
