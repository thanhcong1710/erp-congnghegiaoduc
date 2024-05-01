<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;

class ProcessDataScheduleHasStudent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scheduleHasStudent:process {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'input format: Y-m-d';

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
        $class_date = $this->option('date') ? $this->option('date') : date('Y-m-d', time()-24*3600);
        self::processReserve($class_date);
        self::updateContractDoneSessions();

        u::query("INSERT INTO log_jobs (`action`, created_at, `data`) VALUES ('ProcessDataScheduleHasStudent','".date('Y-m-d H:i:s')."', '$class_date')");
        return "ok";
    }
    
    private function processReserve($class_date){
        u::query("UPDATE schedule_has_student AS s SET s.status=2 WHERE s.class_date = '$class_date' 
            AND (SELECT count(id) FROM reserves WHERE start_date <= '$class_date' AND end_date>='$class_date' AND status=2 AND student_id=s.student_id AND contract_id=s.contract_id AND is_reserved=1)>0");
        return "ok";
    }

    private function updateContractDoneSessions(){
        u::query("UPDATE contracts AS c SET c.done_sessions = (SELECT count(id) FROM schedule_has_student WHERE contract_id=c.id AND status=1) WHERE c.status!=7");
        u::query("UPDATE contracts AS c SET c.left_sessions = c.summary_sessions - c.done_sessions WHERE c.status!=7");
        return "ok";
    }
}
