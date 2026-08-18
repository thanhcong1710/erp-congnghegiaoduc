<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmEmployeeProfile extends Model
{
    protected $table = 'hrm_employee_profiles';

    protected $fillable = [
        'user_id',
        'employee_code',
        'department_id',
        'position_id',
        'job_level_id',
        'job_title_id',
        'id_card_number',
        'address',
        'phone',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'contract_type',
        'dob',
        'gender',
        'start_date',
        'end_date',
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

    public function jobLevel()
    {
        return $this->belongsTo(HrmJobLevel::class, 'job_level_id');
    }

    public function jobTitle()
    {
        return $this->belongsTo(HrmJobTitle::class, 'job_title_id');
    }
}
