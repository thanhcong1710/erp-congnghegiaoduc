<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmEmployeeDocument extends Model
{
    protected $table = 'hrm_employee_documents';

    protected $fillable = [
        'user_id',
        'document_type',
        'title',
        'file_path',
        'expiry_date',
        'note',
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
