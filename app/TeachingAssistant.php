<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TeachingAssistant extends Model
{
    protected $table = 'teaching_assistants';
    
    protected $fillable = [
        'user_id',
        'full_name',
        'facebook_link',
        'dob',
        'phone',
        'address',
        'bank_account',
        'email',
        'lr_link',
        'sw_link',
        'profile_link',
        'start_date',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
