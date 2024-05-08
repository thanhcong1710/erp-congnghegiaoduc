<?php
/**
 * Created by PhpStorm.
 * User: PMTB
 * Date: 3/13/2018
 * Time: 9:28 AM
 */

namespace App\Models;
use Illuminate\Support\Facades\DB;
use App\Providers\UtilityServiceProvider as u;
use Exception;
use Illuminate\Support\Facades\Mail;

use function GuzzleHttp\json_decode;

class ProcessMail
{
    public function  processMail(){
        $list_email = u::query("SELECT * FROM email_queues WHERE `status`=0 AND `lock`=0 LIMIT 20");
        u::query("UPDATE email_queues SET `lock`=1 , add_queue_at ='".date('Y-m-d H:i:s')."' WHERE `status`=0 AND `lock`=0 LIMIT 20");
        foreach($list_email AS $email){
            $code = 'SUCCESS';
            if (env('APP_ENV') === "product"){
                if(empty(json_decode($email->email_to,true))){
                    $code = 'INVALID_MAIL_INFO';
                }else{
                    try{
                        Mail::send('mail.default',['content'=>$email->email_body], function ($message) use ($email){
                            $arr_to=json_decode($email->email_to,true);
                            $arr_cc = json_decode($email->email_cc,true);
                            $arr_att = json_decode($email->email_attack,true);
                            $arr_from=json_decode($email->email_from,true);
                            $message->subject($email->email_subject);
                            $message->priority(3);
                            foreach($arr_to AS $to){
                                $message->to($to);
                            }
                            foreach($arr_cc AS $cc){
                                $message->cc($cc);
                            }
                            foreach($arr_from AS $from){
                                $message->from($from);
                            }
                            foreach($arr_att AS $attachFile){
                                $message->attach($attachFile);
                            }
                        });
                    }catch (Exception $exception){
                        $code = 'FAILURE_SENDING_MAIL';
                    }
                }
            }
            u::query("UPDATE email_queues SET 
                    `lock`=0,
                    `status`=1,
                    send_at = '".date('Y-m-d H:i:s')."',
                    send_code = '$code'
                WHERE id=$email->id");
        }

        return true;
    }
}