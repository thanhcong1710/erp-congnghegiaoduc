<?php

namespace App\Console\Commands;

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
        // $lmsController = new \App\Http\Controllers\LmsController();
        // $lmsController->addOrUpdateStudent(721);
        // $lmsController->addStudentToClass(528);
        // $lmsController->studentWithdrawByIdLMS(1340);
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
        $list_parent = u::query("SELECT id,owner_id FROM crm_parents");
        foreach($list_parent AS $parent){
            $owner_id = data_get($parent, 'owner_id');
            $parent_id = data_get($parent, 'id');
            $arrBranchOwner = u::getBranchIdByUserID($owner_id);
            foreach ($arrBranchOwner AS $row){
                $exit = u::first("SELECT id, owner_id, last_assign_date FROM crm_parent_branch WHERE parent_id = $parent_id AND branch_id = $row->branch_id");
                if($exit){
                    if($owner_id != $exit->owner_id){
                        u::updateSimpleRow(array(
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updator_id' => 0,
                            'owner_id' => $owner_id,
                            'last_assign_date' => date('Y-m-d H:i:s'),
                        ), array('id' => $exit->id), 'crm_parent_branch');
                    }
                }else{
                    u::insertSimpleRow(array(
                        'branch_id' => $row->branch_id,
                        'parent_id' => $parent_id,
                        'owner_id' => $owner_id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'creator_id' => 0,
                        'last_assign_date'=>date('Y-m-d H:i:s'),
                    ),'crm_parent_branch');
                }
            }
            echo $parent_id."/";
        }
        
        return "ok";
    }
}
