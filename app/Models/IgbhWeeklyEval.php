<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IgbhWeeklyEval extends Model
{
    protected $table = 'igbh_weekly_evals';

    protected $fillable = [
        'test_seq',
        'class_id',
        'branch_id',
        'teacher_id',
        'class_seq',
        'class_nm',
        'each_cd',
        'each_cd_nm',
        'teacher_nm',
        'eval_ymd',
        'status',
    ];

    public function details()
    {
        return $this->hasMany(IgbhWeeklyEvalDetail::class, 'weekly_eval_id');
    }
}

