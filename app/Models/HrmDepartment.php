<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmDepartment extends Model
{
    protected $table = 'hrm_departments';

    protected $fillable = [
        'name',
        'description',
        'manager_id',
        'status',
    ];

    public function manager()
    {
        return $this->belongsTo('App\User', 'manager_id');
    }

    public function positions()
    {
        return $this->hasMany(HrmPosition::class, 'department_id');
    }
}
