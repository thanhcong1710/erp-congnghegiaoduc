<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmAttendance extends Model
{
    protected $table = 'hrm_attendances';

    protected $fillable = [
        'employee_id',
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
        'attendance_status',
        'penalty_amount',
        'note',
    ];

    public function employee()
    {
        return $this->belongsTo(HrmEmployeeProfile::class, 'employee_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
