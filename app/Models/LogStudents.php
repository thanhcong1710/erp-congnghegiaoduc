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
}
