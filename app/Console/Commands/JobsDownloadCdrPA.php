<?php

namespace App\Console\Commands;

use App\Http\Controllers\VoipController;
use App\Models\Sms;
use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;
use DateTime;
use DateTimeZone;
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
        $voipControll->getCDRReportAll();
        $sips = u::query("SELECT DISTINCT sip_id FROM users");
        foreach ($sips as $sip) {
            $sip_id = data_get($sip, 'sip_id');
            if ($sip_id) {
                $data_cdr = $voipControll->getCDRReport($sip_id);
                $list_cdr = data_get($data_cdr, 'data.result', []);
                foreach ($list_cdr as $cdr) {
                    $sip_id = data_get($cdr, 'source');
                    $phone = data_get($cdr, 'dst');
                    $pa_uniqueid = data_get($cdr, 'cdr_id');
                    $pa_disposition = data_get($cdr, 'disposition');
                    $pa_duration = data_get($cdr, 'duration');
                    $pa_billsec = data_get($cdr, 'billsec');
                    $pa_recordingfile = data_get($cdr, 'recording_url');
                    $date = new DateTime(data_get($cdr, 'calldate'), new DateTimeZone('UTC'));
                    $date->setTimezone(new DateTimeZone('Asia/Ho_Chi_Minh'));
                    $pa_calldate = $date->format('Y-m-d H:i:s');
                    $check_exit = u::first("SELECT id FROM pa_cdr_data WHERE pa_uniqueid='$pa_uniqueid' LIMIT 1");
                    $end_check = date('Y-m-d H:i:s', strtotime($pa_calldate) + 30);
                    if (!$check_exit) {
                        $data_crm = u::first("SELECT id FROM pa_cdr_data WHERE sip_id ='$sip_id' AND phone='$phone' AND created_at < '$end_check' AND created_at > '$pa_calldate' AND status=0");
                        if ($data_crm) {
                            u::updateSimpleRow(array(
                                'pa_uniqueid' => $pa_uniqueid,
                                'pa_disposition' => $pa_disposition,
                                'pa_duration' => $pa_duration,
                                'pa_billsec' => $pa_billsec,
                                'pa_recordingfile' => $pa_recordingfile,
                                'status' => 1,
                            ), array('id' => data_get($data_crm, 'id')), 'pa_cdr_data');
                        }
                    }
                }
            }
        }
        $last_time = date('Y-m-d H:i:s', time() - 60 * 60);
        $list_call = u::query("SELECT id, data_id FROM crm_customer_care WHERE data_id IS NOT NULL AND get_data_call=0 AND created_at>'$last_time' ORDER BY id DESC");
        foreach ($list_call as $row) {
            $pa_cdr_data = u::first("SELECT * FROM pa_cdr_data WHERE id=" . (int) $row->data_id);
            if ($pa_cdr_data && data_get($pa_cdr_data, 'pa_recordingfile')) {
                u::updateSimpleRow(array('get_data_call' => 1, 'attached_file' => data_get($pa_cdr_data, 'pa_recordingfile')), array('id' => $row->id), 'crm_customer_care');
            }
        }
        u::query("INSERT INTO log_jobs (`action`, created_at) VALUES ('jobsDownloadCdrPA','" . date('Y-m-d H:i:s') . "')");
        return "ok";
    }

}
