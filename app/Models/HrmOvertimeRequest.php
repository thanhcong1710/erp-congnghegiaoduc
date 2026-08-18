<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmOvertimeRequest extends Model
{
    protected $table = 'hrm_overtime_requests';

    protected $fillable = [
        'user_id', 'date', 'start_time', 'end_time', 'reason', 'status', 'approved_by'
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }

    public function approver()
    {
        return $this->belongsTo(\App\User::class, 'approved_by');
    }
}
