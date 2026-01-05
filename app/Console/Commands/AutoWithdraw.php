<?php

namespace App\Console\Commands;

use App\Http\Controllers\LMSController;
use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;

class AutoWithdraw extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autoWithdraw:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto withdraw';

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
        $dateWithdraw = date('Y-m-d', strtotime('-2 days'));
        $listContracts = u::query("SELECT c.* FROM contracts AS c WHERE c.status=6 AND c.class_id IS NOT NULL AND c.left_sessions <= 0 AND c.enrolment_last_date <= '$dateWithdraw'
            AND (SELECT count(id) FROM contracts WHERE student_id=c.id AND c.status!=7 AND count_recharge > c.count_recharge AND (debt_amount=0 OR total_charged>0))=0 ");
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
        return "ok";
    }
}
