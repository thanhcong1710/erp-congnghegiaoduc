<?php

namespace App\Console\Commands;

use App\Http\Controllers\ParentsController;
use App\Http\Controllers\ParentCareController;
use App\Models\Sms;
use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;

class JobsProcessLockParent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jobsProcessLockParent:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'JobsProcessLockParent';

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
        $import = new ParentsController();
        $import->processParentLock();
        $import->processGetStatus();
        
        u::query("INSERT INTO log_jobs (`action`, created_at) VALUES ('jobsProcessLockParent','".date('Y-m-d H:i:s')."')");
        return "ok";
    }
    
}
