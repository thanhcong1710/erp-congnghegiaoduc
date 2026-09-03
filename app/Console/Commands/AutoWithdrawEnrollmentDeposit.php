<?php

namespace App\Console\Commands;

use App\Http\Controllers\ReportsController;
use App\Models\LogClassStudent;
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
        $report = new ReportsController();
        $report->updateSalaryMonthAll();

        // $date = date('Y-m-d', time() - 24*7*3600);
        // $listContracts = u::query("SELECT c.* , a.debt_amount AS agreement_debt_amount, s.created_at AS student_created_at
        //         FROM contracts AS c 
		// 		    LEFT JOIN students AS s On s.id=c.student_id
        //             LEFT JOIN agreements AS a ON a.id = c.agreement_id
        //         WHERE c.debt_amount> 200000 
        //             AND c.enrolment_start_date <= '$date' 
        //             AND c.status!=7  AND c.status!=8 AND c.class_id IS NOT NULL");
        // foreach ($listContracts AS $contract){
        //     if (data_get($contract,'agreement_debt_amount') > 1500000 || (data_get($contract,'student_created_at') > '2026-07-15 00:00:00' && data_get($contract,'agreement_debt_amount') > 200000)) {
        //         u::updateSimpleRow([
        //             'program_id' => null,
        //             'class_id' => null,
        //             'enrolment_start_date' => null,
        //             'enrolment_last_date' => null,
        //             'status'=> 2
        //         ], ['id'=> data_get($contract, 'id')], 'contracts');
        //         u::addLogContracts(data_get($contract, 'id'));
        //         LogStudents::logAdd(data_get($contract, 'student_id'), 'Xóa học sinh cọc khi lớp bắt đầu khai giảng', 0);
        //         echo data_get($contract, 'id')."/";
        //         LogClassStudent::logAction($contract->class_id, $contract->student_id, $contract->id, 0, 0);
        //     }
        // }

        
        // u::query("INSERT INTO log_jobs (`action`, created_at, `data`) VALUES ('AutoWithdrawEnrollmentDeposit','".date('Y-m-d H:i:s')."', '')");
        return "ok";
    }
}
