<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UcreaStudentResult extends Model
{
    protected $table = 'ucrea_student_results';

    protected $fillable = [
        'test_cd',
        'level_cd',
        'test_seq',
        'result_seq',
        'student_id',
        'class_id',
        'contract_id',
        'branch_id',
        'teacher_id',
        'memb_nm',
        'stu_nm',
        'class_nm',
        'eval_dt',
        'result_cd',
        'result_cd_nm',
        'report_data',
        'skills_grade',
    ];

    protected $casts = [
        'report_data' => 'array',
        'skills_grade' => 'array',
    ];

    public function details()
    {
        return $this->hasMany(UcreaStudentResultDetail::class, 'ucrea_student_result_id');
    }
}

