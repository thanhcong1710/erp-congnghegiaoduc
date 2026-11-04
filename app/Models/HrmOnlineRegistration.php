<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class HrmOnlineRegistration extends Model
{
    protected $table = 'hrm_online_registrations';

    protected $fillable = [
        'employee_id',
        'user_id',
        'date',
        'reason',
        'attachment_path',
        'attachment_name',
        'status',
        'approved_by',
        'approved_at',
        'rejected_reason',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'approved_at' => 'datetime',
        'status' => 'integer',
    ];

    public function employee()
    {
        return $this->belongsTo(HrmEmployeeProfile::class, 'employee_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

