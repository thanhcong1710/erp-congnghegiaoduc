<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmContract extends Model
{
    protected $table = 'hrm_contracts';

    protected $fillable = [
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

    public static function generateNextContractNumber()
    {
        $last = self::where('contract_number', 'LIKE', 'CHAM%')
            ->orderByRaw('CAST(SUBSTRING(contract_number, 5) AS UNSIGNED) DESC')
            ->first();

        if (!$last || !$last->contract_number) {
            return 'CHAM01';
        }

        $num = (int) substr($last->contract_number, 4);
        $nextNum = $num + 1;
        return 'CHAM' . str_pad($nextNum, 2, '0', STR_PAD_LEFT);
    }
}
