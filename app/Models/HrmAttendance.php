<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmAttendance extends Model
{
    protected $table = 'hrm_attendances';

    protected $fillable = [
        'user_id',
        'date',
        'check_in',
        'check_out',
        'check_in_ip',
        'check_out_ip',
        'check_in_location',
        'check_out_location',
        'device_id',
        'status',
        'note',
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
