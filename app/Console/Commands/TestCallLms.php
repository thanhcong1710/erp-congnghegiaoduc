<?php

namespace App\Console\Commands;

use App\Http\Controllers\ClassesController;
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
        $list= u:: query("SELECT * FROM contracts WHERE branch_id = 28");
        $lms = new LMSController();
        foreach($list AS $row){
            $lms->addOrUpdateStudent(data_get($row, 'student_id'));
            $lms->addStudentToClass(data_get($row, 'student_id'));
            echo data_get($row, 'student_id')."/";
        }
        return "ok";
    }
}
