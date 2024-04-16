<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Providers\UtilityServiceProvider as u;

class LogStudents extends Model
{
    protected $table = 'student_logs';
    
    public static function logAdd($parent_id,$content,$creator_id){
        u::insertSimpleRow(array(
            'student_id'=>$parent_id,
            'content'=>$content,
            'creator_id'=>$creator_id,
            'created_at'=>date('Y-m-d H:i:s'),
            'status'=> 1
        ),'student_logs');
    }

    public static function logUpdateInfo($pre_student_info,$data_update,$creator_id){
        $content="Cập nhật thông tin học sinh: ";
        foreach($data_update AS $key=>$row){
            if($row!=$pre_student_info->$key){
                if($key=='name'){
                    $content.= "Tên từ `".$pre_student_info->$key."` thành `".$row."`, ";
                }elseif($key=='date_of_birth'){
                    $content.= "Ngày sinh từ `".$pre_student_info->$key."` thành `".$row."`, ";
                }elseif($key=='gender'){
                    $content.= "Giới tính từ `".$pre_student_info->$key."` thành `".$row."`, ";
                }elseif($key=='province_id'){
                    $pre_province_info = u::first("SELECT name FROM provinces WHERE id =".(int)$pre_student_info->$key);
                    $province_info = u::first("SELECT name FROM provinces WHERE id =".(int)$row);
                    $text_pre = isset($pre_province_info->name) ? $pre_province_info->name :'';
                    $text_curr = isset($province_info->name) ? $province_info->name :'';
                    $content.= "Tỉnh thành phố từ `".$text_pre."` thành `".$text_curr."`, ";
                }elseif($key=='district_id'){
                    $pre_district_info = u::first("SELECT name FROM districts WHERE id =".(int)$pre_student_info->$key);
                    $district_info = u::first("SELECT name FROM districts WHERE id =".(int)$row);
                    $text_pre = isset($pre_district_info->name) ? $pre_district_info->name :'';
                    $text_curr = isset($district_info->name) ? $district_info->name :'';
                    $content.= "Quận huyện từ `".$text_pre."` thành `".$text_curr."`, ";
                }elseif($key=='address'){
                    $content.= "Địa chỉ từ `".$pre_student_info->$key."` thành `".$row."`, ";
                }elseif($key=='school'){
                    $content.= "Trường học từ `".$pre_student_info->$key."` thành `".$row."`, ";
                }elseif($key=='gud_name1'){
                    $content.= "Họ tên phụ huynh 1 từ `".$pre_student_info->$key."` thành `".$row."`, ";
                }elseif($key=='gud_email1'){
                    $content.= "Email phụ huynh 1 từ `".$pre_student_info->$key."` thành `".$row."`, ";
                }elseif($key=='gud_birth_day1'){
                    $content.= "Ngày sinh phụ huynh 1 từ `".$pre_student_info->$key."` thành `".$row."`, ";
                }elseif($key=='gud_job1'){
                    $pre_job_info = u::first("SELECT title FROM jobs WHERE id =".(int)$pre_student_info->$key);
                    $job_info = u::first("SELECT title FROM jobs WHERE id =".(int)$row);
                    $text_pre = isset($pre_job_info->title) ? $pre_job_info->title :'';
                    $text_curr = isset($job_info->title) ? $job_info->title :'';
                    $content.= "Nghề nghiệp phụ huynh 1 từ `".$text_pre."` thành `".$text_curr."`, ";
                }elseif($key=='gud_name2'){
                    $content.= "Họ tên phụ huynh 2 từ `".$pre_student_info->$key."` thành `".$row."`, ";
                }elseif($key=='gud_email2'){
                    $content.= "Email phụ huynh 2 từ `".$pre_student_info->$key."` thành `".$row."`, ";
                }elseif($key=='gud_mobile2'){
                    $content.= "SĐT phụ huynh 2 từ `".$pre_student_info->$key."` thành `".$row."`, ";
                }elseif($key=='gud_birth_day2'){
                    $content.= "Ngày sinh phụ huynh 2 từ `".$pre_student_info->$key."` thành `".$row."`, ";
                }elseif($key=='gud_job2'){
                    $pre_job_info = u::first("SELECT title FROM jobs WHERE id =".(int)$pre_student_info->$key);
                    $job_info = u::first("SELECT title FROM jobs WHERE id =".(int)$row);
                    $text_pre = isset($pre_job_info->title) ? $pre_job_info->title :'';
                    $text_curr = isset($job_info->title) ? $job_info->title :'';
                    $content.= "Nghề nghiệp phụ huynh 2 từ `".$text_pre."` thành `".$text_curr."`, ";
                }
            }
        }
        u::insertSimpleRow(array(
            'student_id'=>$pre_student_info->id,
            'content'=>$content,
            'creator_id'=>$creator_id,
            'created_at'=>date('Y-m-d H:i:s'),
            'status'=> 1
        ),'student_logs');
    }
}
