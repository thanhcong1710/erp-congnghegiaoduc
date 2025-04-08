<?php

namespace App\Http\Controllers;

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
        $list = u::query("SELECT * FROM student_waitting_process WHERE status=1 AND DATE_FORMAT(processed_at,'%Y-%m-%d') = '".date('Y-m-d')."'");
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
        $list = u::query("SELECT id FROM contracts WHERE status!=7 AND left_sessions =0 AND summary_sessions > 0");
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
}
