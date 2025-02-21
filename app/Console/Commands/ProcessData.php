<?php

namespace App\Console\Commands;

use App\Http\Controllers\JobsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ToolsController;
use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;

class ProcessData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'processdata:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Data';

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
        $jobs = new JobsController();
        $jobs->processWaittingStudent();
        $report = new ReportsController();
        // report_full_fee_active
        $report->collectFullFeeActive( $request,'_','_');
        //report_renews
        $report->updateCompletedDate();
        $report->updateRenewedDates();
        $report->updateRenewReport();

        u::query("INSERT INTO log_jobs (`action`, created_at) VALUES ('ProcessData','".date('Y-m-d H:i:s')."')");
        return "ok";
    }
    
}
