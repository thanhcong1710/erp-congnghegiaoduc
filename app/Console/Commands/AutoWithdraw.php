<?php

namespace App\Console\Commands;

use App\Models\LogStudents;
use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;
class AutoWithdraw extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autoWithdraw:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'AutoWithdraw';

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
        $date = date('Y-m-d', strtotime('-7 days'));
        $listContracts = u::query("SELECT c.* , cl.cls_name, cl.cls_enddate
                FROM contracts AS c 
				    LEFT JOIN classes AS cl On cl.id=c.class_id
                WHERE cl.cls_enddate <= '$date' AND c.class_id IS NOT NULL
                AND (c.status=6 OR c.status=3) AND c.enrolment_start_date IS NOT NULL");
        foreach ($listContracts AS $contract){
            u::updateSimpleRow([
                'updated_at'=> date('Y:m:d H:i:s'),
                'status'=> 7
            ], ['id'=> data_get($contract, 'id')], 'contracts');
            u::addLogContracts(data_get($contract, 'id'));
            LogStudents::logAdd(data_get($contract, 'student_id'), 'Kết thúc khóa học lớp: '.data_get($contract,'cls_name').' ('.data_get($contract,'cls_enddate').')', 0);
        }

        
        u::query("INSERT INTO log_jobs (`action`, created_at, `data`) VALUES ('AutoWithdraw','".date('Y-m-d H:i:s')."', '')");
        return "ok";
    }
}
