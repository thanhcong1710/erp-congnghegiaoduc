<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmContract extends Model
{
    protected $table = 'hrm_contracts';

    protected $fillable = [
        'user_id',
        'contract_number',
        'contract_type',
        'start_date',
        'end_date',
        'basic_salary',
        'file_path',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
