<?php

namespace App\Console\Commands;

use App\Http\Controllers\ClassesController;
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
        // $classController = new ClassesController();
        // $classController->syncLMS();
        return "ok";
    }
}
