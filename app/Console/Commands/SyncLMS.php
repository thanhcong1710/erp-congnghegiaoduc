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
        //lấy dữ liệu trung tâm từ LMS
        // $lms->syncDataBranch();
        //tạo trung tâm trên ERP 
        // $lms->erpCreateBranch();

        //lấy dữ liệu semester từ LMS
        // $lms->syncDataSemester();
        //tạo semester trên ERP 
        // $lms->erpCreateSemester();

        //lấy dữ liệu programs từ LMS
        // $listSemester = u::query("SELECT * FROM lms_semesters");
        // foreach ($listSemester as $item) {
        //     $lms->syncDataPrograms($item->bsem_id);
        //     echo $item->bsem_id."/";
        // }
        //tạo programs trên ERP 
        // $lms->erpCreateProgram();

        //lấy dữ liệu room từ LMS
        // $listBranch = u::query("SELECT * FROM lms_branches");
        // foreach ($listBranch as $item) {
        //     $lms->syncDataRoom($item->brch_id);
        //     echo $item->brch_id."/";
        // }
        //tạo room trên ERP 
        // $lms->erpCreateRoom();

        //lấy dữ liệu teachers từ LMS
        // $listBranch = u::query("SELECT * FROM lms_branches");
        // foreach ($listBranch as $item) {
        //     $lms->syncDataTeacher($item->brch_id, $item->ug_type, $item->branch_id);
        //     echo $item->brch_id."/";
        // }

        //lấy dữ liệu Class từ LMS
        $listClass = u::query("SELECT id, parent FROM lms_programs WHERE id LIKE '%:B'");
        foreach ($listClass as $item) {
            $cls_id = str_replace(':B', '', $item->id);
            $lms->syncDataClassInfo($cls_id, $item->parent);
            echo $cls_id."/";
        }
        //tạo Class trên ERP 
        $lms->erpCreateClass();
        //tạo session trên ERP
        $listClass = u::query("SELECT * FROM classes");
        foreach ($listClass as $item) {
            $lms->getDataRoomSessionInClass($item->id);
            echo $item->id."/";
        }

        // $listClass = u::query("SELECT id FROM lms_programs WHERE id LIKE '%:B'");
        foreach ($listClass as $item) {
            $cls_id = str_replace(':B', '', $item->id);
            $lms->syncDataSchedules($cls_id);
            echo $cls_id."/";
        }

        // $listClass = u::query("SELECT id FROM lms_programs WHERE id LIKE '%:B'");
        foreach ($listClass as $item) {
            $cls_id = str_replace(':B', '', $item->id);
            $lms->syncDataClassStudents($cls_id);
            echo $cls_id."/";
        }

        // $lms->syncDataClassStudents(267);
        return "ok";
    }
    
}
