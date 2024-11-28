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
        $report = new ReportsController();
        $report->collectFullFeeActive( $request,'_','_');

        u::query("INSERT INTO log_jobs (`action`, created_at) VALUES ('ProcessData','".date('Y-m-d H:i:s')."')");
        return "ok";
    }
    
}
