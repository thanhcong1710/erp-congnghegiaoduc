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
        // $list = u::query("SELECT c.* FROM contracts As c LEFT JOIN classes AS cl ON cl.id=c.class_id WHERe c.status=6 AND cl.cls_enddate > CURRENT_DATE AND c.debt_amount=0 AND c.id >9912");
        // $list = u::query("SELECT id,enrolment_start_date FROM contracts WHERE enrolment_start_date < '2026-05-07'  AND `status`=6 AND debt_amount=0");
        // foreach($list AS $row){
        //     u::processDataContractsPast(data_get($row, 'id'),data_get($row, 'enrolment_start_date'));

        //     echo data_get($row, 'id')."/";
        // }
        $charge = new ChargesController();
        // $list = u::query("SELECT DISTINCT agreement_id FROM payments WHERe is_import=2");
        // foreach($list AS $row){
        //     $charge->reProcessAgreement($row->agreement_id);
        //     echo  $row->agreement_id."/";
        // }
        foreach([207,
        597,
        4153,
        6317,
        7053,
        9132,
        9635,
        9638,
        10007] as $row){

        $charge->reProcessAgreement($row);
         echo  $row."/";
        }
        
        return "ok";
    }
    
}
