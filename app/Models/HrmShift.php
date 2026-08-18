<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmShift extends Model
{
    protected $table = 'hrm_shifts';
    
    protected $fillable = [
        'name', 'start_time', 'end_time', 'late_grace_period', 'early_leave_grace_period', 'status'
    ];
}
