<?php

namespace App\Console\Commands;

use App\Http\Controllers\LMSController;
use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;

class SyncLMS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'syncLMS:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync LMS Data';

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
        $lms = new LMSController();
        // $lms->syncDataBranch();
        // $lms->syncDataSemester();

        // $listSemester = u::query("SELECT * FROM lms_semesters");
        // foreach ($listSemester as $item) {
        //     $lms->syncDataPrograms($item->bsem_id);
        //     echo $item->bsem_id."/";
        // }

        // $listClass = u::query("SELECT id FROM lms_programs WHERE id LIKE '%:B'");
        // foreach ($listClass as $item) {
        //     $cls_id = str_replace(':B', '', $item->id);
        //     $lms->syncDataClassInfo($cls_id);
        //     echo $cls_id."/";
        // }

        // $listClass = u::query("SELECT id FROM lms_programs WHERE id LIKE '%:B'");
        // foreach ($listClass as $item) {
        //     $cls_id = str_replace(':B', '', $item->id);
        //     $lms->syncDataSchedules($cls_id);
        //     echo $cls_id."/";
        // }

        $listClass = u::query("SELECT id FROM lms_programs WHERE id LIKE '%:B'");
        foreach ($listClass as $item) {
            $cls_id = str_replace(':B', '', $item->id);
            $lms->syncDataClassStudents($cls_id);
            echo $cls_id."/";
        }
        return "ok";
    }
    
}
