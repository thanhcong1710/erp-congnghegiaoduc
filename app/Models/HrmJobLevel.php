<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmJobLevel extends Model
{
    protected $table = 'hrm_job_levels';

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    public function employees()
    {
        return $this->hasMany(HrmEmployeeProfile::class, 'job_level_id');
    }
}
