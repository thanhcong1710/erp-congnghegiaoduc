<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmJobTitle extends Model
{
    protected $table = 'hrm_job_titles';

    protected $fillable = [
        'name',
        'department_id',
        'description',
        'status',
    ];

    public function department()
    {
        return $this->belongsTo(HrmDepartment::class, 'department_id');
    }
}
