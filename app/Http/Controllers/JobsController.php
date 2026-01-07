<?php

namespace App\Http\Controllers;

use App\Enums\SystemCode;
use App\User;
use App\Http\Controllers\Controller;
use App\Models\LogStudents;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobsController extends Controller
{
    public function processWaittingStudent()
    {
        $date = date('Y-m-d');
        $list = u::query("SELECT * FROM student_waitting_process WHERE status=1 AND DATE_FORMAT(processed_at,'%Y-%m-%d') = '".$date."'");
        foreach ($list AS $row) {
            if ($row->type == 2) {
                $classTransfer = new ClassTransfersController();
                $classTransfer->processClassTransfer(data_get($row, 'data_id'));
            } else if ($row->type == 3) {
                $branchTransfer = new BranchTransfersController();
                $branchTransfer->processBranchTransfer(data_get($row, 'data_id'));
            } else if ($row->type == 1) {
                $reserve = new ReservesController();
                $reserve->processReserve(data_get($row, 'data_id'));
            }else if ($row->type == 4) {
                $reserve = new TuitionTransfersController();
                $reserve->processTuitionTransfer(data_get($row, 'data_id'));
            }
        }
        return "ok";
    }

    public function processAutoWithdrawStudent()
    {
        $tmpTimeWithdraw = date('Y-m-d',time()-3*24*3600);

        $list = u::query("SELECT id FROM contracts WHERE status!=7 AND left_sessions =0 AND summary_sessions > 0 AND enrolment_last_date < ' $tmpTimeWithdraw' ");
        foreach ($list AS $row) {
            u::updateSimpleRow(array(
                'status' => 7,
                'type_withdraw' =>1,
                'action' => 'Withdraw học sinh do quá hạn số buổi học',
                'updated_at' => date('Y-m-d H:i:s'),
            ), array('id'=>$row->id),'contracts');
            u::addLogContracts(data_get($row, 'id'));
        }
        return "ok";
    }

    public function testAuto($student_id){
        $dateWithdraw = date('Y-m-d', strtotime('-2 days'));
        $listContracts = u::query("SELECT c.* FROM contracts AS c WHERE c.status=6 AND c.class_id IS NOT NULL AND c.left_sessions <= 0 AND c.enrolment_last_date <= '$dateWithdraw'
            AND (SELECT count(id) FROM contracts WHERE student_id=c.student_id AND status!=7 AND count_recharge > c.count_recharge AND (debt_amount=0 OR total_charged>0))=0 AND c.student_id=$student_id");
        foreach ($listContracts AS $row){
            $lmsController = new LMSController();
            $lmsController->studentWithdraw(data_get($row, 'student_id'));
            u::updateSimpleRow(array(
                'status' => 7,
                'type_withdraw' =>1,
                'action' => 'Auto Withdraw học sinh do quá hạn số buổi học',
                'updated_at' => date('Y-m-d H:i:s'),
            ), array('id'=>data_get($row, 'id')),'contracts');
            u::addLogContracts(data_get($row, 'id'));
        }
        u::query("INSERT INTO log_jobs (`action`, created_at) VALUES ('AutoWitdraw','".date('Y-m-d H:i:s')."')");

        $dateJoin = date('Y-m-d', strtotime('-2 days'));
        $listContracts = u::query("SELECT c.* FROM contracts AS c WHERE c.status=6 AND c.class_id IS NOT NULL AND c.left_sessions <= 0 AND c.enrolment_last_date <= '$dateJoin'
            AND (SELECT count(id) FROM contracts WHERE student_id=c.student_id AND status!=7 AND status!=6 AND count_recharge > c.count_recharge AND debt_amount=0 )>0 AND c.student_id=$student_id");
        foreach ($listContracts AS $contract_withdraw){
            if($contract_withdraw){
                $class_id = data_get($contract_withdraw, 'class_id');
                $student_id = data_get($contract_withdraw, 'class_id');
                $contractJoin = u::first("SELECT id FROM contracts WHERE student_id=$student_id AND status!=7 AND status!=6 AND count_recharge > ".data_get($contract_withdraw, 'count_recharge')." AND debt_amount=0 LIMIT 1");
                if ($contractJoin){
                    $contract_id = data_get($contractJoin,'id');
                    u::updateSimpleRow(array(
                        'status' => 7,
                        'type_withdraw' =>1,
                        'action' => 'Withdraw học sinh do quá hạn số buổi học',
                        'updated_at' => date('Y-m-d H:i:s'),
                    ), array('id'=>data_get($contract_withdraw, 'id')),'contracts');
                    u::addLogContracts(data_get($contract_withdraw, 'id'));
    
                    $contractJoinInfo = u::getObject(array('id'=>$contract_id), 'contracts');
                    $class_info = u::getObject(array('id'=>$class_id), 'classes');
                    $cm_id =data_get($class_info,'cm_id', null);
                    $cm_leader = u::first("SELECT ul.id 
                        FROM users AS u 
                            LEFT JOIN users AS ul ON ul.id=u.manager_id
                            LEFT JOIN role_has_user AS ru ON ru.user_id= ul.id 
                            LEFT JOIN roles AS r ON r.id=ru.role_id 
                        WHERE r.code = '".SystemCode::ROLE_CM_LEADER."' AND ul.status=1 AND u.id = ".data_get($class_info, 'cm_id', 0)." LIMIT 1");
                    $cm_leader_id = data_get($cm_leader,'id') ? data_get($cm_leader,'id') : $cm_id;
                    $holidays = u::getPublicHolidays(data_get($class_info,'branch_id'), data_get($class_info,'product_id'));
                    $reserved_dates = u::getReservedDates_transfer($contract_id);
                    if (!empty($reserved_dates)) {
                        $holidays = array_merge($holidays, $reserved_dates);
                    }
                    $arr_day = explode(",",data_get($class_info, 'class_day'));
                    $join_date = date('Y-m-d', strtotime(data_get($contract_withdraw,'enrolment_last_date'))+24*3600);
                    $data_sessions = u::calculatorSessionsByNumberOfSessions($join_date, data_get($contractJoinInfo,'summary_sessions'), $holidays, $arr_day);
                    u::updateSimpleRow(array(
                        'cm_id' => $cm_id,
                        'cm_leader_id' => $cm_leader_id,
                        'program_id' => data_get($class_info,'program_id', null),
                        'class_id' => data_get($class_info,'id', null),
                        'class_id' => data_get($class_info,'id', null),
                        'enrolment_start_date' => data_get($data_sessions, 'start_date'),
                        'enrolment_last_date' => data_get($data_sessions, 'end_date'),
                        'status' => 6,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updator_id' => 0,
                    ),array('id'=>$contract_id),'contracts');
                    if(data_get($data_sessions,'start_date') < date('Y-m-d')){
                        u::processDataContractsPast($contract_id, data_get($data_sessions,'start_date'));
                    }
                    u::addLogContracts($contract_id);
                    LogStudents::logAdd($student_id, 'Nối phí cho học sinh trong lớp '.data_get($class_info,'cls_name').' - ngày bắt đầu gói mới: '.data_get($data_sessions, 'start_date'), 0);
                }
                
            }
        }
        u::query("INSERT INTO log_jobs (`action`, created_at) VALUES ('AutoJoin','".date('Y-m-d H:i:s')."')");

        return "ok";
    }
}
