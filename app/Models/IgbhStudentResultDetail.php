<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IgbhStudentResultDetail extends Model
{
    protected $table = 'igbh_student_result_details';

    protected $fillable = [
        'igbh_student_result_id',
        'question_no',
        'question_type',
        'seq_id',
        'assigned_score',
        'unit',
        'is_correct',
        'max_score',
    ];

    public function result()
    {
        return $this->belongsTo(IgbhStudentResult::class, 'igbh_student_result_id');
    }
}

