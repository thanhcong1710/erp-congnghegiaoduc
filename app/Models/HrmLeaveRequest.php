<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmLeaveRequest extends Model
{
    protected $table = 'hrm_leave_requests';

    protected $fillable = [
        'user_id',
        'type',
        'leave_type_id',
        'start_date',
        'end_date',
        'reason',
        'status',
        'approved_by',
        'hr_approved_by',
        'rejection_reason',
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }

    public function leaveType()
    {
        return $this->belongsTo(HrmLeaveType::class, 'leave_type_id');
    }

    public function managerApprover()
    {
        return $this->belongsTo(\App\User::class, 'approved_by');
    }

    public function hrApprover()
    {
        return $this->belongsTo(\App\User::class, 'hr_approved_by');
    }
}
