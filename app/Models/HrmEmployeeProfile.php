<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmEmployeeProfile extends Model
{
    protected $table = 'hrm_employee_profiles';

    protected $fillable = [
        'user_id',
        'department_id',
        'position_id',
        'id_card_number',
        'address',
        'dob',
        'gender',
        'start_date',
        'base_salary',
        'bank_account',
        'bank_name',
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function department()
    {
        return $this->belongsTo(HrmDepartment::class, 'department_id');
    }

    public function position()
    {
        return $this->belongsTo(HrmPosition::class, 'position_id');
    }
}
