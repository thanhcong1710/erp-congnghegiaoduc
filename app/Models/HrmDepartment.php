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
        'parent_id',
        'branch_id',
        'status',
    ];

    public function manager()
    {
        return $this->belongsTo('App\User', 'manager_id');
    }

    public function parent()
    {
        return $this->belongsTo(HrmDepartment::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(HrmDepartment::class, 'parent_id');
    }

    public function branch()
    {
        return $this->belongsTo('App\Models\Branch', 'branch_id'); // Assuming Branch model exists
    }

    public function positions()
    {
        return $this->hasMany(HrmPosition::class, 'department_id');
    }
}
