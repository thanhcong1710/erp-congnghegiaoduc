<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmAttendance extends Model
{
    protected $table = 'hrm_attendances';

    protected $fillable = [
        'user_id',
        'date',
        'check_in',
        'check_out',
        'note',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
