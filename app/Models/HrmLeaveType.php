<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmLeaveType extends Model
{
    protected $table = 'hrm_leave_types';

    protected $fillable = [
        'name', 'days_per_year', 'is_paid', 'status'
    ];
}
