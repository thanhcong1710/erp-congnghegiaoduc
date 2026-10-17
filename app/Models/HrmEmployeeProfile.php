<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmEmployeeProfile extends Model
{
    protected $table = 'hrm_employee_profiles';

    protected $fillable = [
        'user_id',
        'employee_code',
        'employee_type',
        'probation_start_date',
        'probation_end_date',
        'work_status',
        'resigned_date',
        'resigned_reason',
        'approval_status',
        'created_by_user_id',
        'profile_received_date',
        'profile_status',
        'missing_documents',
        'profile_note',
        'department_id',
        'position_id',
        'job_level_id',
        'job_title_id',
        'id_card_number',
        'id_card_date',
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

    protected $casts = [
        'missing_documents' => 'array',
    ];

    /**
     * Generate next sequential code like CHAM01, CHAM02...
     */
    public static function generateNextEmployeeCode()
    {
        $lastProfile = self::where('employee_code', 'LIKE', 'CHAM%')
            ->orderByRaw('CAST(SUBSTRING(employee_code, 5) AS UNSIGNED) DESC')
            ->first();

        if (!$lastProfile || !$lastProfile->employee_code) {
            return 'CHAM01';
        }

        $num = (int) substr($lastProfile->employee_code, 4);
        $nextNum = $num + 1;
        return 'CHAM' . str_pad($nextNum, 2, '0', STR_PAD_LEFT);
    }

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
