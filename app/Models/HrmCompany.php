<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmCompany extends Model
{
    protected $table = 'hrm_companies';

    protected $fillable = [
        'code',
        'name',
        'tax_code',
        'address',
        'phone',
        'email',
        'employee_prefix',
        'contract_prefix',
        'status',
    ];

    public function employees()
    {
        return $this->hasMany(HrmEmployeeProfile::class, 'company_id');
    }

    public function departments()
    {
        return $this->hasMany(HrmDepartment::class, 'company_id');
    }

    public function contracts()
    {
        return $this->hasMany(HrmContract::class, 'company_id');
    }

    public function shifts()
    {
        return $this->hasMany(HrmShift::class, 'company_id');
    }
}

