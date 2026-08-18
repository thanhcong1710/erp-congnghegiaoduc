<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmEmploymentHistory extends Model
{
    protected $table = 'hrm_employment_history';

    protected $fillable = [
        'user_id',
        'type',
        'from_department_id',
        'to_department_id',
        'from_position_id',
        'to_position_id',
        'from_salary',
        'to_salary',
        'effective_date',
        'note',
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
