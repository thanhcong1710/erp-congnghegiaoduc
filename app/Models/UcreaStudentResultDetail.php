<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UcreaStudentResultDetail extends Model
{
    protected $table = 'ucrea_student_result_details';

    protected $fillable = [
        'ucrea_student_result_id',
        'question_no',
        'main_category',
        'sub_category',
        'rubric_name',
        'assigned_score',
    ];

    public function result()
    {
        return $this->belongsTo(UcreaStudentResult::class, 'ucrea_student_result_id');
    }
}

