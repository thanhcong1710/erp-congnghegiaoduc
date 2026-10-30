<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmShiftAssignment extends Model
{
    protected $table = 'hrm_shift_assignments';

    protected $fillable = [
        'employee_id', 'user_id', 'shift_id', 'date', 'is_overtime'
    ];

    public function employee()
    {
        return $this->belongsTo(HrmEmployeeProfile::class, 'employee_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }

    public function shift()
    {
        return $this->belongsTo(HrmShift::class, 'shift_id');
    }
}
