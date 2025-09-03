<?php

namespace App\Console\Commands;

use App\Http\Controllers\ClassesController;
use App\Http\Controllers\ReportsController;
use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;
use App\User;
use Illuminate\Http\Request;

class TestCallLms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'testCallLms:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Call Lms';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Request $request)
    {
        $lmsController = new \App\Http\Controllers\LmsController();
        // $lmsController->addOrUpdateStudent(721);
        // $lmsController->addStudentToClass(845, true);
        $lmsController->studentWithdrawByIdLMS(1328);
        // $list =u::query("SELECT id FROM contracts WHERE status=6");
        // foreach($list AS $row){
        //     u::updateScheduleHasStudent($row->id);
        //     echo $row->id."/";
        // }
        // $user = new User();
        // $tmp=$user->getStaffHasUser(46);
        // var_dump($tmp);die();
        // u::updateEnrolmentLastDate(899);
        //u::updateDoneSessions(564);
        // $arr = [10];
        // $reservesController = new \App\Http\Controllers\ReservesController();
        // foreach($arr AS $row){
        //     $reservesController->processReserve($row);
        // }
        // u::updateScheduleHasStudent(890);

        // Convert dữ liệu sang bảng crm_parent_branch
        // $list_parent = u::query("SELECT id,owner_id FROM crm_parents");
        // foreach($list_parent AS $parent){
        //     $owner_id = data_get($parent, 'owner_id');
        //     $parent_id = data_get($parent, 'id');
        //     $arrBranchOwner = u::getBranchIdByUserID($owner_id);
        //     foreach ($arrBranchOwner AS $row){
        //         $exit = u::first("SELECT parent_id,branch_id, owner_id, last_assign_date FROM crm_parent_branch WHERE parent_id = $parent_id AND branch_id = $row->branch_id");
        //         if($exit){
        //             if($owner_id != $exit->owner_id){
        //                 u::updateSimpleRow(array(
        //                     'updated_at' => date('Y-m-d H:i:s'),
        //                     'updator_id' => 0,
        //                     'owner_id' => $owner_id,
        //                     'last_assign_date' => date('Y-m-d H:i:s'),
        //                     'is_lock' => 1,
        //                 ), array('parent_id' => $exit->parent_id,'branch_id' => $exit->branch_id), 'crm_parent_branch');
        //             }
        //         }else{
        //             u::insertSimpleRow(array(
        //                 'branch_id' => $row->branch_id,
        //                 'parent_id' => $parent_id,
        //                 'owner_id' => $owner_id,
        //                 'created_at' => date('Y-m-d H:i:s'),
        //                 'creator_id' => 0,
        //                 'last_assign_date'=>date('Y-m-d H:i:s'),
        //                 'is_lock' => 1,
        //             ),'crm_parent_branch');
        //         }
        //     }
        //     echo $parent_id."/";
        // }

        // Convert dữ liệu sang bảng crm_student_checkin
        // $list_students = u::query("SELECT * FROM crm_students WHERe checkin_branch_id IS NOT NULL");
        // foreach($list_students AS $student){
        //     u::insertSimpleRow(array(
        //         'crm_student_id' => $student->id,
        //         'checkin_at' => $student->checkin_at,
        //         'checkined_at' => $student->checkined_at,
        //         'checkin_owner_id' => $student->checkin_owner_id,
        //         'checkin_branch_id' => $student->checkin_branch_id,
        //         'type_product' => $student->type_product,
        //         'status'=>$student->status,
        //         'checkined_note' => $student->checkined_note,
        //     ),'crm_student_checkin');
        //     echo $student->id."/";
        // }
        // u::updateEnrolmentLastDate(606);
        

        // $listContracts = u::query("SELECT DISTINCT student_id FROM contracts ");
        // foreach ($listContracts as $contract) {
        //     $studentId = $contract->student_id;
        //     $contracts = u::query("SELECT id, type FROM contracts WHERE student_id = $studentId ORDER BY id ASC");
        //     $tmp_count_recharge = 0;
        //     foreach($contracts as $contract) {
        //         if ($contract->type == 0) { // Assuming type 1 is for active contracts
        //             u::updateSimpleRow(['count_recharge'=>-1], ['id' => $contract->id], 'contracts');
        //         } else{
        //             u::updateSimpleRow(['count_recharge'=>$tmp_count_recharge], ['id' => $contract->id], 'contracts');
        //             $tmp_count_recharge++;
        //         }
        //     }
        //     echo  $studentId . "/";
        // }
        // $list = u::query("SELECT c.* FROM crm_students AS c LEFT JOIN crm_student_checkin AS cc ON cc.crm_student_id=c.id WHERE cc.id is NULL AND c.status=3");
        // foreach($list AS $student){
        //     if(data_get($student, 'checkin_branch_id')){
        //         u::insertSimpleRow(array(
        //             'crm_student_id' => $student->id,
        //             'checkin_at'=> data_get($student, 'checkin_at'),
        //             'checkin_owner_id' => data_get($student, 'checkin_owner_id') ,
        //             'checkin_branch_id'=> data_get($student, 'checkin_branch_id'),
        //             'created_at' =>  data_get($student, 'created_at') ,
        //             'creator_id' =>  data_get($student, 'creator_id') ,
        //             'type_product'=> data_get($student, 'type_product'),
        //             'status' => 1, // 
        //         ), 'crm_student_checkin');
        //     }
        // }
        return "ok";
    }
}
