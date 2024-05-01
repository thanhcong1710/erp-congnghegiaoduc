<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;

class AddScheduleHasStudent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scheduleHasStudent:add {--date=}';

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
        $class_date = $this->option('date') ? $this->option('date') : date('Y-m-d');
        u::addLogContracts(44068);
        // self::addNewScheduleHasStudent($class_date);
        // self::processReserve($class_date);

        u::query("INSERT INTO log_jobs (`action`, created_at, `data`) VALUES ('AddScheduleHasStudent','".date('Y-m-d H:i:s')."', '$class_date')");
        return "ok";
    }
    
    private function addNewScheduleHasStudent($class_date){
        u::query("DELETE FROM schedule_has_student WHERE class_date = '$class_date'");
        $list_student = u::query("SELECT c.student_id, c.branch_id, c.class_id, c.id AS contract_id, c.product_id, cl.program_id
        FROM contracts AS c 
            LEFT JOIN classes AS cl ON cl.id = c.class_id
            LEFT JOIN schedules AS s ON s.class_id=cl.id
        WHERE c.status=6 AND s.status=1 AND s.class_date = '$class_date'");
        self::addItem($list_student, $class_date);
        return "ok";
    }

    private function processReserve($class_date){
        u::query("UPDATE schedule_has_student AS s SET s.status=2 WHERE s.class_date = '$class_date' 
            AND (SELECT count(id) FROM reserves WHERE start_date <= '$class_date' AND end_date>='$class_date' AND status=2 AND student_id=s.student_id AND contract_id=s.contract_id AND is_reserved=1)>0");
        return "ok";
    }

    public function addItem($list, $class_date) {
        if ($list) {
            $created_at = date('Y-m-d H:i:s');
            $status = 1; 
            $query = "INSERT INTO schedule_has_student (student_id, branch_id, class_id, contract_id, product_id, program_id, class_date, created_at, `status`) VALUES ";
           
            if (count($list) > 10000) {
                for($i = 0; $i < 10000; $i++) {
                    $item = (object)$list[$i];
                    $query.= "('$item->student_id', '$item->branch_id', '$item->class_id', '$item->contract_id', '$item->product_id', '$item->program_id', '$class_date', '$created_at', '$status'),";
                }
                $query = substr($query, 0, -1);
                $this->addItem(array_slice($list, 10000), $class_date);
            } else {
                foreach($list as $i=>$item) {
                    $item = (object)$item;
                    $query.= "('$item->student_id', '$item->branch_id', '$item->class_id', '$item->contract_id', '$item->product_id', '$item->program_id', '$class_date', '$created_at', '$status'),";
                }
                $query = substr($query, 0, -1);
                u::query($query);
            }
        }
    }
}
