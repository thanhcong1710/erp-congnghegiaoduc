<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IgbhStudentResult extends Model
{
    protected $table = 'igbh_student_results';

    protected $fillable = [
        'result_seq',
        'test_seq',
        'stu_seq',
        'student_id',
        'class_id',
        'contract_id',
        'branch_id',
        'teacher_id',
        'stu_nm',
        'stu_birth_dt',
        'reg_name',
        'eval_dt',
        'reg_date',
        'total_score',
        'subject_total',
        'thinking_total',
        'assigned_level',
        'quarter_cd',
        'quarter_cd_nm',
        'class_type_cd',
    ];

    public function details()
    {
        return $this->hasMany(IgbhStudentResultDetail::class, 'igbh_student_result_id');
    }
}

