<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IgbhSummativeResult extends Model
{
    protected $table = 'igbh_summative_results';

    protected $fillable = [
        'test_seq',
        'student_id',
        'class_id',
        'contract_id',
        'branch_id',
        'teacher_id',
        'stu_seq',
        'stu_nm',
        'class_seq',
        'class_nm',
        'teacher_nm',
        'total_score',
        'eval_dt',
        'status',
        'subjective_analysis',
    ];

    protected $casts = [
        'subjective_analysis' => 'array',
    ];

    public function details()
    {
        return $this->hasMany(IgbhSummativeResultDetail::class, 'summative_result_id');
    }
}

