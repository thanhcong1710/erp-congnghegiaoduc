<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IgbhClassSummativeEval extends Model
{
    protected $table = 'igbh_class_summative_evals';

    protected $fillable = [
        'test_seq',
        'class_id',
        'branch_id',
        'teacher_id',
        'class_seq',
        'class_nm',
        'teacher_nm',
        'eval_ymd',
        'status',
    ];
}

