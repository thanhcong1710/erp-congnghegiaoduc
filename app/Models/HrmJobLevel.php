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
}
