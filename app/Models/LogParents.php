<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Providers\UtilityServiceProvider as u;

class LogParents extends Model
{
    protected $table = 'crm_parent_logs';
    public static function logAssign($parent_id,$pre_owner_id,$owner_id,$creator_id,$overwrite = false){
        $pre_owner_info = u::first("SELECT name,hrm_id FROM users WHERE id = $pre_owner_id");
        $owner_info = u::first("SELECT name,hrm_id FROM users WHERE id = $owner_id");
        if($overwrite){
            $content = "Ghi đè người phụ trách: từ `$pre_owner_info->name ($pre_owner_info->hrm_id)` thành `$owner_info->name ($owner_info->hrm_id)`";
        }else{
            $content = "Thay đổi người phụ trách: từ `$pre_owner_info->name ($pre_owner_info->hrm_id)` thành `$owner_info->name ($owner_info->hrm_id)`";
        }
        u::insertSimpleRow(array(
            'parent_id'=>$parent_id,
            'content'=>$content,
            'creator_id'=>$creator_id,
            'created_at'=>date('Y-m-d H:i:s'),
            'status'=> 1
        ),'crm_parent_logs');
    }
    public static function logAssignList($list_parent_info,$owner_id,$creator_id){
        $created_at = date('Y-m-d H:i:s');
        $owner_info = u::first("SELECT name,hrm_id FROM users WHERE id = $owner_id");
        $query = "INSERT INTO crm_parent_logs (`parent_id`,`content`,creator_id,created_at) VALUES ";
        foreach($list_parent_info AS $item){
            $content = "Thay đổi người phụ trách: từ `$item->pre_owner` thành `$owner_info->name ($owner_info->hrm_id)`";
            $query.= "('$item->parent_id','$content','$creator_id','$created_at'),";
        }
        $query = substr($query, 0, -1);
        u::query($query);
    }
    public static function logStatus($parent_id,$pre_status,$status,$creator_id){
        $arr_status = array(
            '0'=>'KH mới',
            '10'=>'KH không liên lạc được',
            '20'=>'KH ở vùng CMS không có cơ sở',
            '30'=>'KH không nghe máy',
            '40'=>'KH hẹn gọi lại sau',
            '50'=>'KH không quan tâm',
            '60'=>'KH không tiềm năng',
            '71'=>'KH quan tâm, cần follow up date',
            '72'=>' KH tiềm năng nhưng không muốn làm phiền',
            '73'=>'KH đồng ý đặt lịch Checkin',
            '81'=>'KH đã đến checkin',
            '82'=>'KH đã mua gói phí',
            '83'=>'KH đến hạn tái tục',
            '90'=>'Danh sách đen',
        );
        $content = "Thay đổi trạng thái: từ `".$arr_status[$pre_status]."` thành `".$arr_status[$status]."`";
        u::insertSimpleRow(array(
            'parent_id'=>$parent_id,
            'content'=>$content,
            'creator_id'=>$creator_id,
            'created_at'=>date('Y-m-d H:i:s'),
            'status'=> 1
        ),'crm_parent_logs');
    }
    public static function logAdd($parent_id,$content,$creator_id){
        u::insertSimpleRow(array(
            'parent_id'=>$parent_id,
            'content'=>$content,
            'creator_id'=>$creator_id,
            'created_at'=>date('Y-m-d H:i:s'),
            'status'=> 1
        ),'crm_parent_logs');
    }
    public static function logUpdateInfo($pre_parent_info,$data_update,$creator_id){
        $content="Cập nhật thông tin khách hàng: ";
        foreach($data_update AS $key=>$row){
            if($row!=$pre_parent_info->$key){
                if($key=='name'){
                    $content.= "Tên từ `".$pre_parent_info->$key."` thành `".$row."`, ";
                }elseif($key=='email'){
                    $content.= "Emal từ `".$pre_parent_info->$key."` thành `".$row."`, ";
                }elseif($key=='mobile_1'){
                    $content.= "SĐT 1 từ `".$pre_parent_info->$key."` thành `".$row."`, ";
                }elseif($key=='mobile_2'){
                    $content.= "SĐT 2 từ `".$pre_parent_info->$key."` thành `".$row."`, ";
                }elseif($key=='address'){
                    $content.= "Địa chỉ từ `".$pre_parent_info->$key."` thành `".$row."`, ";
                }elseif($key=='province_id'){
                    $pre_province_info = u::first("SELECT name FROM provinces WHERE id =".(int)$pre_parent_info->$key);
                    $province_info = u::first("SELECT name FROM provinces WHERE id =".(int)$row);
                    $text_pre = isset($pre_province_info->name) ? $pre_province_info->name :'';
                    $text_curr = isset($province_info->name) ? $province_info->name :'';
                    $content.= "Tỉnh thành phố từ `".$text_pre."` thành `".$text_curr."`, ";
                }elseif($key=='district_id'){
                    $pre_district_info = u::first("SELECT name FROM districts WHERE id =".(int)$pre_parent_info->$key);
                    $district_info = u::first("SELECT name FROM districts WHERE id =".(int)$row);
                    $text_pre = isset($pre_district_info->name) ? $pre_district_info->name :'';
                    $text_curr = isset($district_info->name) ? $district_info->name :'';
                    $content.= "Quận huyện từ `".$text_pre."` thành `".$text_curr."`, ";
                }elseif($key=='gender'){
                    $content.= "Giới tính từ `".$pre_parent_info->$key."` thành `".$row."`, ";
                }elseif($key=='birthday'){
                    $content.= "Ngày sinh từ `".$pre_parent_info->$key."` thành `".$row."`, ";
                }elseif($key=='job_id'){
                    $pre_job_info = u::first("SELECT title FROM jobs WHERE id =".(int)$pre_parent_info->$key);
                    $job_info = u::first("SELECT title FROM jobs WHERE id =".(int)$row);
                    $text_pre = isset($pre_job_info->title) ? $pre_job_info->title :'';
                    $text_curr = isset($job_info->title) ? $job_info->title :'';
                    $content.= "Nghề nghiệp từ `".$text_pre."` thành `".$text_curr."`, ";
                }elseif($key=='source_id'){
                    $pre_source_info = u::first("SELECT name FROM sources WHERE id =".(int)$pre_parent_info->$key);
                    $source_info = u::first("SELECT name FROM sources WHERE id =".(int)$row);
                    $content.= "Nguồn từ `".$pre_source_info->name."` thành `".$source_info->name."`, ";
                }elseif($key=='source_detail_id'){
                    $pre_source_info = u::first("SELECT name FROM source_detail WHERE id =".(int)$pre_parent_info->$key);
                    $source_info = u::first("SELECT name FROM source_detail WHERE id =".(int)$row);
                    $content.= "Nguồn chi tiết từ `".$pre_source_info->name."` thành `".$source_info->name."`, ";
                }elseif($key=='note'){
                    $content.= "Ghi chú từ `".$pre_parent_info->$key."` thành `".$row."`, ";
                }elseif($key=='c2c_mobile'){
                    $content.= "SĐT khách hàng giới thiệu từ `".$pre_parent_info->$key."` thành `".$row."`, ";
                }elseif($key=='owner_id'){
                    $pre_owner_info = u::first("SELECT name,hrm_id FROM users WHERE id = ".(int)$pre_parent_info->$key);
                    $owner_info = u::first("SELECT name,hrm_id FROM users WHERE id = ".(int)$row);
                    $text_pre = isset($pre_owner_info->name) ? "$pre_owner_info->name ($pre_owner_info->hrm_id)" :'';
                    $text_curr = isset($owner_info->name) ? "$owner_info->name ($owner_info->hrm_id)" :'';
                    $content.= "Người phụ trách: từ `$pre_owner_info->name ($pre_owner_info->hrm_id)` thành `$owner_info->name ($owner_info->hrm_id)`, ";
                }
               
            }
        }
        u::insertSimpleRow(array(
            'parent_id'=>$pre_parent_info->id,
            'content'=>$content,
            'creator_id'=>$creator_id,
            'created_at'=>date('Y-m-d H:i:s'),
            'status'=> 1
        ),'crm_parent_logs');
    }
    public static function logUpdateStudentInfo($pre_student_info,$data_update,$creator_id){
        $content="Cập nhật thông tin học sinh (ID - $pre_student_info->id): ";
        foreach($data_update AS $key=>$row){
            if($row!=$pre_student_info->$key){
                if($key=='name'){
                    $content.= "Tên từ `".$pre_student_info->$key."` thành `".$row."`, ";
                }elseif($key=='gender'){
                    $content.= "Giới tính từ `".$pre_student_info->$key."` thành `".$row."`, ";
                }elseif($key=='birthday'){
                    $content.= "Ngày sinh từ `".$pre_student_info->$key."` thành `".$row."`, ";
                }elseif($key=='school_level'){
                    $content.= "Cấp trường từ `".$pre_student_info->$key."` thành `".$row."`, ";
                }elseif($key=='school'){
                    $content.= "Trường học từ `".$pre_student_info->$key."` thành `".$row."`, ";
                }elseif($key=='note'){
                    $content.= "Ghi chú từ `".$pre_student_info->$key."` thành `".$row."`, ";
                }
            }
        }
        u::insertSimpleRow(array(
            'parent_id'=>$pre_student_info->parent_id,
            'content'=>$content,
            'creator_id'=>$creator_id,
            'created_at'=>date('Y-m-d H:i:s'),
            'status'=> 1
        ),'crm_parent_logs');
    }
}
