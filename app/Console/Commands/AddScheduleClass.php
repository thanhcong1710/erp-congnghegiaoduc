<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;

class AddScheduleClass extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'addScheduleClass:add';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'AddScheduleClass';

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
        u::query("UPDATE schedules s
                INNER JOIN public_holiday ph 
                    ON s.class_date BETWEEN ph.start_date AND ph.end_date
                    AND FIND_IN_SET(s.branch_id, ph.branch_id) > 0
                SET s.status = 0
                WHERE ph.status = 1
                AND s.status = 1");
        $listClass =u::query("SELECT
                cl.id ,p.num_sessions,cl.class_day,cl.branch_id,cl.product_id,cl.cls_startdate,cl.teacher_id,cl.cm_id
            FROM
                classes AS cl
                LEFT JOIN products AS p ON p.id = cl.product_id 
            WHERE
                p.num_sessions > (SELECT count(id) FROM schedules WHERE class_id = cl.id AND `status` = 1)");
        foreach ($listClass AS $class) {
            $class_id = data_get($class, 'id');
            u::query("DELETE FROM schedules WHERE class_id= $class_id");
            $arr_day = explode(",", data_get($class, 'class_day'));
            $start_date = data_get($class,'cls_startdate');
            $holidays = u::getPublicHolidays(data_get($class,'branch_id'), data_get($class,'product_id'));
            $data_sessions = u::calculatorSessionsByNumberOfSessions($start_date, data_get($class,'num_sessions'), $holidays, $arr_day);
    
            $i=0;
            foreach(data_get($data_sessions, 'dates') AS $row){
                $i++;
                u::insertSimpleRow(array(
                    'class_date'=> $row,
                    'class_id'=> $class_id,
                    'status'=> 1,
                    'created_at'=> date('Y-m-d H:i:s'),
                    'teacher_id'=> data_get($class,'teacher_id'),
                    'branch_id'=> data_get($class,'branch_id'),
                    'cm_id'=> data_get($class,'cm_id'),
                    'subject_stt' =>$i
                ),'schedules');
            }
            $arr_schedule = u::query("SELECT * FROM schedules WHERE status= 1 AND class_id= $class_id ORDER BY class_date");
            
            $arr_subject = u::query("SELECT * FROM subject_has_class WHERE class_id=$class_id ORDER BY stt");
            if(count($arr_subject) > 1){
                for($i=1;$i<= data_get($class,'num_sessions');$i++){
                    foreach($arr_subject AS $subject){
                        u::query("UPDATE schedules set subject_id = $subject->subject_id WHERE subject_id IS NULL AND status= 1 AND class_id= $class_id ORDER BY class_date LIMIT $subject->session");
    
                    }
                    $tmp_subject = 0;
                    $subject_stt = 0;
                    $class_stt = 0;
                    foreach($arr_schedule AS $row){
                        if($tmp_subject != $row->subject_id){
                            $tmp_subject = $row->subject_id;
                            $subject_stt = 0;
                        }
                        $subject_stt++;
                        $class_stt++;
                        u::updateSimpleRow(array('subject_stt'=>$subject_stt, 'class_stt'=>$class_stt), array('id'=>$row->id),'schedules');
                    }
                }
            }
            echo $class_id."/";
        }
    }
}
