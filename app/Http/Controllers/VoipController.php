<?php

namespace App\Http\Controllers;

use App\Models\SocketIO;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Providers\CurlServiceProvider as curl;
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
        $this->apiKey = "f2966f069e0c637f438a1e87b8b6a928";
        // $this->baseUriSocket = "103.226.250.52";
        $this->baseUriCall = "https://crm.pavietnam.vn";
    }
    public function makeToCall($phone,$sip=0)
    {

        $header=[];
        $method = "POST";
        $url = sprintf('%s/api/callNow.php',$this->baseUriCall);
        $data_request = [
            'api_key' => $this->apiKey,
            'extension' => $sip,
            'phone'   => $phone,
        ];
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://crm.pavietnam.vn/api/callNow.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data_request));
        $result = curl_exec($ch);
        u::logRequest($url,$method,$header,$data_request,$result,'log_request_outbound');

        $res =json_decode($result);
        if(data_get($res, 'code') == 1000){
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
    public function getCDRReport($from_date)
    {
        $data_request = [
            'api_key' => $this->apiKey,
            'to_date' => '',
            'duration_max' => '',
            'from_date' => $from_date,
            'limit'   => '',
            'destination' => '',
            'duration_min' => '',
            'source'  => '',
            'status'  => '',
            'page'    => '',
            'from'    => '',
        ];
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://crm.pavietnam.vn/api/getCDRReport.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data_request));
        $result = curl_exec($ch);
        u::logRequest('https://crm.pavietnam.vn/api/getCDRReport.php','POST',[],$data_request,$result,'log_request_outbound');
        $res =json_decode($result);
        return $res;
    }

    public function test(){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://cms.logiclab.vn/api/leads-create-checkin',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
            "student_name": "Nh\\u1eadt Huy",
            "student_gender": "M",
            "student_note": null,
            "student_date_of_birth": "2020-09-25",
            "gud_name": "Di\\u1ec7u Linh",
            "gud_email": null,
            "gud_mobile_1": "0399739128",
            "gud_mobile_2": null,
            "gud_gender": "F",
            "gud_birthday": null,
            "gud_job": null,
            "source": 24,
            "source_detail": null,
            "address": null,
            "province_id": null,
            "district_id": null,
            "branch_id": 1,
            "checkin_at": "2024-11-09 00:00",
            "type_product": "1",
            "ec_hrm": "CM0114",
            "creator_hrm": "C00001",
            "sibling_id": 0
        }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            var_dump($response,$err);die();
        } 
        var_dump($response);die();


    }
}
