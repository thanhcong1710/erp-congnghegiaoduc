<?php

namespace App\Console\Commands;

use App\Http\Controllers\ChargesController;
use App\Http\Controllers\JobsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ToolsController;
use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;
use Dom\CharacterData;
use Illuminate\Http\Request;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Test';

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
        $ChargesController = new ChargesController();
        $ChargesController->processContractsByAgreement(7);
        echo "ok";
        return "ok";
    }
    
}
