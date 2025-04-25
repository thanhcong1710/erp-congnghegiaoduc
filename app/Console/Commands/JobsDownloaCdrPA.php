<?php

namespace App\Console\Commands;

use App\Http\Controllers\VoipController;
use App\Models\Sms;
use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JobsDownloadCdrPA extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jobsDownloadCdrPA:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'jobsDownloadCdrPA';

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
        $voipControll = new VoipController();
        $date = Date('Y-m-d');
        $data_cdr = $voipControll->getCDRReport($date);
        if(data_get($data_cdr,'code') == 1000){
            $list_cdr = data_get($data_cdr, 'data');
            if ($list_cdr){
                $list_cdr = json_decode($list_cdr);
                rsort($list_cdr);
            }
            foreach ($list_cdr AS $cdr){
                $sip_id = data_get($cdr, 'cnum');
                $phone = data_get($cdr, 'dst');
                $pa_uniqueid = data_get($cdr, 'uniqueid');
                $pa_disposition = data_get($cdr, 'disposition');
                $pa_duration = data_get($cdr, 'duration');
                $pa_billsec = data_get($cdr, 'billsec');
                $pa_recordingfile = data_get($cdr, 'recordingfile');
                $pa_calldate = data_get($cdr, 'calldate');
                $check_exit = u::first("SELECT id FROM pa_cdr_data WHERE pa_uniqueid='$pa_uniqueid' LIMIT 1");
                $end_check = date('Y-m-d H:i:s', strtotime($pa_calldate)+30);
                if (!$check_exit) {
                    $data_crm = u::first("SELECT id FROM pa_cdr_data WHERE sip_id ='$sip_id' AND phone='$phone' AND created_at < '$end_check' AND created_at > '$pa_calldate' AND status=0");
                    if($data_crm) {
                        u::updateSimpleRow(array(
                            'pa_uniqueid' => $pa_uniqueid,
                            'pa_disposition' => $pa_disposition,
                            'pa_duration' => $pa_duration,
                            'pa_billsec' => $pa_billsec,
                            'pa_recordingfile' => $pa_recordingfile,
                            'status' => 1,
                        ), array('id'=>data_get($data_crm, 'id')), 'pa_cdr_data');
                    }
                }
            }
        }

        $last_time = date('Y-m-d H:i:s',time()- 60*60);
        $list_call = u::query("SELECT id, data_id FROM crm_customer_care WHERE data_id IS NOT NULL AND get_data_call=0 AND created_at>'$last_time' ORDER BY id DESC");
        foreach($list_call AS $row){
            $pa_cdr_data = u::first("SELECT * FROM pa_cdr_data WHERE id=".(int)$row->data_id);
            if($pa_cdr_data && data_get($pa_cdr_data, 'pa_recordingfile')){
                $data_request = [
                    'api_key' => 'f2966f069e0c637f438a1e87b8b6a928',
                    'recording_file' => data_get($pa_cdr_data, 'pa_recordingfile'),
                ];
            
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://crm.pavietnam.vn/api/playRecording.php');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_request);
                $result = curl_exec($ch);
        
                $dir = __DIR__.'/../../../public/static/voip/'. date('Y_m').'/';
                $dir_file = 'static/voip/'. date('Y_m').'/';
                if(!file_exists($dir)){
                    mkdir($dir);
                }
                $file_name_mp3 = 'pa_cdr_'.$row->data_id.".wav";

                $result = file_put_contents($dir.$file_name_mp3,$result);
                Log::info("message",['id'=>$row->data_id,'result'=>$result]);
                if($result){
                    u::updateSimpleRow(array('get_data_call'=>1,'attached_file'=>$dir_file.$file_name_mp3),array('id'=>$row->id),'crm_customer_care');
                }
            }
        }
        u::query("INSERT INTO log_jobs (`action`, created_at) VALUES ('jobsDownloadCdrPA','".date('Y-m-d H:i:s')."')");
        return "ok";
    }
    
}
