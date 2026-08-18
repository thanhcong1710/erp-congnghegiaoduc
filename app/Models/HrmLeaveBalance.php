<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmLeaveBalance extends Model
{
    protected $table = 'hrm_leave_balances';

    protected $fillable = [
        'user_id', 'leave_type_id', 'year', 'total_days', 'used_days'
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }

    public function leaveType()
    {
        return $this->belongsTo(HrmLeaveType::class, 'leave_type_id');
    }
}
