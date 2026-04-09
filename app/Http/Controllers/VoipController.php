<?php

namespace App\Http\Controllers;

use App\Models\SocketIO;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Providers\CurlServiceProvider as curl;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Log;

class VoipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $apiKey;
    protected $baseUriCall;
    protected $baseUriSocket;
    public function __construct()
    {
        $this->baseUriCall = "http://172.16.95.19";
        $this->apiKey = "0764eec00a8098dfd6d6fe428fffbd71";
    }

    public function makeToCall($phone,$sip=0)
    {
        $header = array(
            'app-key: '.$this->apiKey,
            'tenant: 1',
            'Content-Type: application/json',
            'Cookie: HttpOnly; HttpOnly; HttpOnly'
        );
        $method = "POST";
        $url = sprintf('%s/api/v2/core/click_to_call',$this->baseUriCall);
        $data_request = [
            'caller' => $sip,
            'callee'   => $phone,
        ];
    
        $res = curl::curl($url, $method,$header,$data_request);
        u::logRequest($url,$method,$header,$data_request,$res,'log_request_outbound');
        $res = json_decode($res);

        if(data_get($res, 'status') == 'success'){
            $id = u::insertSimpleRow(array(
                'phone' => $phone,
                'sip_id' => $sip,
                'created_at' => date('Y-m-d H:i:s')
            ), 'pa_cdr_data');
            return [
                'status'=>1,
                'call_id'=> $id,
            ];
        } else{
            return [
                'status'=>0,
                'message'=> 'Thực hiện cuộc gọi thất bại, vui lòng thử lại'
            ];
        }
    }
    public function getCDRReport($sip_id)
    {
        $header = array(
            'app-key: '.$this->apiKey,
            'tenant: 1'
        );
        $method = "GET";
        $params = [
            'extensions' => $sip_id,
            'calltype' => 3,
            'start_date' => time() - 3600,
            'end_date' => time(),
            'limit'=>'10000',
            'order_by'=>'calldate',
            'order_type'=>'desc',
            'disposition'=> 'ANSWER',
        ];
        
        $queryString = http_build_query($params);
        $url = sprintf('%s/api/v2/cdr?%s',$this->baseUriCall,$queryString);
        $result = curl::curl($url, $method, $header);
        $res =json_decode($result);
        return $res;
    }

    public function getCDRReportAll()
    {
        $header = array(
            'app-key: '.$this->apiKey,
            'tenant: 1'
        );
        $method = "GET";
        $params = [
            'start_date' => time() - 3600,
            'end_date' => time(),
            'order_by'=>'calldate',
            'order_type'=>'desc',
        ];
        
        $queryString = http_build_query($params);
        $url = sprintf('%s/api/v2/cdr?%s',$this->baseUriCall,$queryString);
        $result = curl::curl($url, $method, $header);
        $res =json_decode($result);
        $list_cdr = data_get($res, 'data.result',[]);
        if($list_cdr){
            self::addItem($list_cdr);  
        }
       return $res;
    }
    private function addItem($list){
        $sql_update = "INSERT INTO pbx_data (uniqueid,`source`,dst, calldate, duration, billsec, disposition, recording_url, meta_data, call_type) VALUES ";
        if (count($list) > 1000) {
            for($i = 0; $i < 1000; $i++) {
                $item = (object)$list[$i];
                $date = new DateTime(data_get($item, 'calldate'), new DateTimeZone('UTC'));
                $date->setTimezone(new DateTimeZone('Asia/Ho_Chi_Minh'));
                $pa_calldate = $date->format('Y-m-d H:i:s');
                $sql_update.="('".data_get($item, 'uniqueid')."', '".data_get($item, 'source')."', '".data_get($item, 'dst')."', '".$pa_calldate."', '".data_get($item, 'duration')."', '".data_get($item, 'billsec')."', '".data_get($item, 'disposition')."', '".data_get($item, 'recording_url')."', '".json_encode($item)."', '".data_get($item, 'calltype')."'),";
            }
            $sql_update = substr($sql_update, 0, -1);
            $sql_update.=" ON DUPLICATE KEY UPDATE `uniqueid` = VALUES(`uniqueid`), `source` = VALUES(`source`), `dst` = VALUES(`dst`) , `calldate` = VALUES(`calldate`) , `billsec` = VALUES(`billsec`) , `disposition` = VALUES(`disposition`) , `recording_url` = VALUES(`recording_url`) , `meta_data` = VALUES(`meta_data`), `call_type` = VALUES(`call_type`)";
            u::query($sql_update);
            $this->addItem(array_slice($list, 1000));
        } else{
            foreach($list as $i=>$item) {
                $item = (object)$list[$i];
                $date = new DateTime(data_get($item, 'calldate'), new DateTimeZone('UTC'));
                $date->setTimezone(new DateTimeZone('Asia/Ho_Chi_Minh'));
                $pa_calldate = $date->format('Y-m-d H:i:s');
                $sql_update.="('".data_get($item, 'uniqueid')."', '".data_get($item, 'source')."', '".data_get($item, 'dst')."', '".$pa_calldate."', '".data_get($item, 'duration')."', '".data_get($item, 'billsec')."', '".data_get($item, 'disposition')."', '".data_get($item, 'recording_url')."', '".json_encode($item)."', '".data_get($item, 'calltype')."'),";
            }
            $sql_update = substr($sql_update, 0, -1);
            $sql_update.=" ON DUPLICATE KEY UPDATE `uniqueid` = VALUES(`uniqueid`), `source` = VALUES(`source`), `dst` = VALUES(`dst`) , `calldate` = VALUES(`calldate`) , `billsec` = VALUES(`billsec`) , `disposition` = VALUES(`disposition`) , `recording_url` = VALUES(`recording_url`) , `meta_data` = VALUES(`meta_data`), `call_type` = VALUES(`call_type`)";
            u::query($sql_update);
        }
    }
}
