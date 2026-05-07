<?php

namespace App\Console\Commands;

use App\Models\LogStudents;
use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;

class AutoWithdrawEnrollmentDeposit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autoWithdrawEnrollmentDeposit:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'AutoWithdrawEnrollmentDeposit';

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
    public function handle()
    {
        $date = date('Y-m-d');
        $listContracts = u::query("SELECT * FROM contracts WHERE debt_amount>0 AND enrolment_start_date <= '$date' AND status!=7  AND status!=8 AND class_id IS NOT NULL");
        foreach ($listContracts AS $contract){
            u::updateSimpleRow([
                'program_id' => null,
                'class_id' => null,
                'enrolment_start_date' => null,
                'enrolment_last_date' => null,
                'status'=> 2
            ], ['id'=> data_get($contract, 'id')], 'contracts');
            u::addLogContracts(data_get($contract, 'id'));
            LogStudents::logAdd(data_get($contract, 'student_id'), 'Xóa học sinh cọc khi lớp bắt đầu khai giảng', 0);
            echo data_get($contract, 'id')."/";
        }

        
        u::query("INSERT INTO log_jobs (`action`, created_at, `data`) VALUES ('AutoWithdrawEnrollmentDeposit','".date('Y-m-d H:i:s')."', '')");
        return "ok";
    }
}
