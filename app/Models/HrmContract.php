<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmContract extends Model
{
    protected $table = 'hrm_contracts';

    protected $fillable = [
        'company_id',
        'employee_id',
        'user_id',
        'contract_number',
        'contract_type',
        'start_date',
        'end_date',
        'basic_salary',
        'file_path',
        'status',
    ];

    public function employee()
    {
        return $this->belongsTo(HrmEmployeeProfile::class, 'employee_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function company()
    {
        return $this->belongsTo(HrmCompany::class, 'company_id');
    }

    public static function generateNextContractNumber($companyIdOrPrefix = null)
    {
        $prefix = 'CHAM';
        if (!empty($companyIdOrPrefix)) {
            if (is_numeric($companyIdOrPrefix)) {
                $comp = HrmCompany::find($companyIdOrPrefix);
                if ($comp && !empty($comp->contract_prefix)) {
                    $prefix = strtoupper(trim($comp->contract_prefix));
                }
            } else {
                $prefix = strtoupper(trim($companyIdOrPrefix));
            }
        }

        $prefixLen = strlen($prefix);
        $last = self::where('contract_number', 'LIKE', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(contract_number, ' . ($prefixLen + 1) . ') AS UNSIGNED) DESC')
            ->first();

        if (!$last || !$last->contract_number) {
            return $prefix . '01';
        }

        $num = (int) substr($last->contract_number, $prefixLen);
        $nextNum = $num + 1;
        return $prefix . str_pad($nextNum, 2, '0', STR_PAD_LEFT);
    }
}
