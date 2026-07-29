<?php

namespace App\Console\Commands;

use App\Http\Controllers\ClassesController;
use App\Http\Controllers\ClassTransfersController;
use App\Http\Controllers\JobsController;
use App\Http\Controllers\LMSController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\TuitionTransfersController;
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
        // $list = u::query("SELECT * FROM students WHERE id >1801 AND id < 1809");
        // foreach ($list as $row) {
        //     $lms = new LMSController();
        //     // $lms->addOrUpdateStudent(data_get($row, 'id'));
        //     $lms->addStudentToClass(data_get($row, 'id'));
        //     echo data_get($row, 'id') . "/";
        // }
        // $lms->studentWithdrawContract(2040);
        $lms = new LMSController();
        // $lms->studentWithdrawContract(2336);
        // $lms->addOrUpdateStudent(1997);
        $lms->addStudentToClass(709);

        // $list= u::query("SELECT * FROM contracts where class_id=622");
        // foreach ($list AS $contract){
        //     u::updateScheduleHasStudent($contract->id, '2026-07-21');
        //     echo $contract->id."/";
        // }
        // $classTransfer =  new ClassTransfersController();
        // $classTransfer->processClassTransfer(1226);
        return "ok";
    }
}