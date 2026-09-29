<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;

class AddMissingScheduleClass extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'addMissingScheduleClass:add {class_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add missing schedules to a class after the last session';

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
    public function handle()
    {
        $class_id_param = $this->argument('class_id');
        $where_schedule_class = "";
        $where_class = "";
        if ($class_id_param) {
            $where_schedule_class = " AND s.class_id = " . (int)$class_id_param;
            $where_class = " AND cl.id = " . (int)$class_id_param;
        }

        // Cancel schedules that fall on holidays
        u::query("UPDATE schedules s
                INNER JOIN public_holiday ph 
                    ON s.class_date BETWEEN ph.start_date AND ph.end_date
                    AND FIND_IN_SET(s.branch_id, ph.branch_id) > 0
                SET s.status = 0
                WHERE ph.status = 1
                AND s.status = 1
                $where_schedule_class");

        $listClass = u::query("SELECT
                cl.id, p.num_sessions, cl.class_day, cl.branch_id, cl.product_id, cl.cls_startdate, cl.teacher_id, cl.cm_id
            FROM
                classes AS cl
                LEFT JOIN products AS p ON p.id = cl.product_id 
            WHERE
                p.num_sessions > (SELECT count(id) FROM schedules WHERE class_id = cl.id AND `status` = 1) AND cl.cls_enddate>'2026-08-29'
                $where_class");

        foreach ($listClass as $class) {
            $class_id = data_get($class, 'id');
            $num_sessions = data_get($class, 'num_sessions');

            // Get current active schedules count
            $current_count_obj = u::first("SELECT count(id) as total FROM schedules WHERE class_id = $class_id AND `status` = 1");
            $current_count = $current_count_obj ? (int)$current_count_obj->total : 0;
            
            $missing_sessions = $num_sessions - $current_count;
            if ($missing_sessions <= 0) {
                continue;
            }

            // Get the last schedule date to know where to start appending
            $last_schedule = u::first("SELECT class_date FROM schedules WHERE class_id = $class_id AND `status` = 1 ORDER BY class_date DESC LIMIT 1");
            
            $start_date = data_get($class, 'cls_startdate');
            if ($last_schedule && $last_schedule->class_date) {
                $start_date = date('Y-m-d', strtotime($last_schedule->class_date . ' +1 day'));
            }

            $arr_day = explode(",", data_get($class, 'class_day'));
            $holidays = u::getPublicHolidays(data_get($class, 'branch_id'), data_get($class, 'product_id'));
            
            $data_sessions = u::calculatorSessionsByNumberOfSessions($start_date, $missing_sessions, $holidays, $arr_day);

            if (!$data_sessions || !data_get($data_sessions, 'dates')) {
                continue;
            }

            $i = $current_count;
            foreach(data_get($data_sessions, 'dates') as $row){
                $i++;
                u::insertSimpleRow(array(
                    'class_date'=> $row,
                    'class_id'=> $class_id,
                    'status'=> 1,
                    'created_at'=> date('Y-m-d H:i:s'),
                    'teacher_id'=> data_get($class, 'teacher_id'),
                    'branch_id'=> data_get($class, 'branch_id'),
                    'cm_id'=> data_get($class, 'cm_id'),
                    'subject_stt' => $i
                ), 'schedules');
            }

            $arr_subject = u::query("SELECT * FROM subject_has_class WHERE class_id = $class_id ORDER BY stt");
            
            if(count($arr_subject) > 1){
                for($i = 1; $i <= $num_sessions; $i++){
                    foreach($arr_subject as $subject){
                        u::query("UPDATE schedules set subject_id = $subject->subject_id WHERE subject_id IS NULL AND status = 1 AND class_id = $class_id ORDER BY class_date LIMIT $subject->session");
                    }
                    $tmp_subject = 0;
                    $subject_stt = 0;
                    $class_stt = 0;
                    
                    $arr_schedule_updated = u::query("SELECT * FROM schedules WHERE status = 1 AND class_id = $class_id ORDER BY class_date");
                    
                    foreach($arr_schedule_updated as $row){
                        if($tmp_subject != $row->subject_id){
                            $tmp_subject = $row->subject_id;
                            $subject_stt = 0;
                        }
                        $subject_stt++;
                        $class_stt++;
                        u::updateSimpleRow(array('subject_stt' => $subject_stt, 'class_stt' => $class_stt), array('id' => $row->id), 'schedules');
                    }
                }
            }
            echo $class_id . "/";
        }
    }
}
