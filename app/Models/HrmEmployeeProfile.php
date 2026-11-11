<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmEmployeeProfile extends Model
{
    protected $table = 'hrm_employee_profiles';

    protected $fillable = [
        'company_id',
        'user_id',
        'employee_code',
        'full_name',
        'email',
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
        'shift_id',
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
     * Kiểm tra nhân sự có phải fulltime không (để tính phép năm 12 ngày)
     */
    public function isFulltime()
    {
        $empType = strtolower($this->employee_type ?? '');
        $contractType = strtolower($this->contract_type ?? '');
        return $empType === 'fulltime' || $contractType === 'fulltime';
    }

    /**
     * Số ngày phép năm mặc định: chỉ Fulltime mới có 12 ngày, CTV hoặc loại khác là 0 ngày
     */
    public function getDefaultAnnualLeaveDays()
    {
        return $this->isFulltime() ? 12.0 : 0.0;
    }

    /**
     * Generate next sequential code like CHAM01, CHAM02... or MEGA01, MEGA02...
     */
    public static function generateNextEmployeeCode($companyIdOrCode = null)
    {
        $prefix = 'CHAM';
        if (!empty($companyIdOrCode)) {
            if (is_numeric($companyIdOrCode)) {
                $comp = HrmCompany::find($companyIdOrCode);
                if ($comp && !empty($comp->employee_prefix)) {
                    $prefix = strtoupper(trim($comp->employee_prefix));
                }
            } else {
                $prefix = strtoupper(trim($companyIdOrCode));
            }
        }

        $prefixLen = strlen($prefix);
        $lastProfile = self::where('employee_code', 'LIKE', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(employee_code, ' . ($prefixLen + 1) . ') AS UNSIGNED) DESC')
            ->first();

        if (!$lastProfile || !$lastProfile->employee_code) {
            return $prefix . '01';
        }

        $num = (int) substr($lastProfile->employee_code, $prefixLen);
        $nextNum = $num + 1;
        return $prefix . str_pad($nextNum, 2, '0', STR_PAD_LEFT);
    }

    public function company()
    {
        return $this->belongsTo(HrmCompany::class, 'company_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function department()
    {
        return $this->belongsTo(HrmDepartment::class, 'department_id');
    }

    public function shift()
    {
        return $this->belongsTo(HrmShift::class, 'shift_id');
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
