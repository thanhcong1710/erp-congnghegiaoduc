<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmLeaveRequest extends Model
{
    protected $table = 'hrm_leave_requests';

    protected $fillable = [
        'user_id',
        'type',
        'start_date',
        'end_date',
        'reason',
        'status',
        'approver_id',
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function approver()
    {
        return $this->belongsTo('App\User', 'approver_id');
    }
}
