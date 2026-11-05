<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IgbhWeeklyEvalDetail extends Model
{
    protected $table = 'igbh_weekly_eval_details';

    protected $fillable = [
        'weekly_eval_id',
        'student_id',
        'stu_seq',
        'stu_nm',
        'workbook',
        'attd_listen',
        'attd_join',
        'attd_express',
        'attd_coop',
        'detect_normal',
        'detect_leadersh',
        'detect_math',
        'detect_creative',
    ];

    public function weeklyEval()
    {
        return $this->belongsTo(IgbhWeeklyEval::class, 'weekly_eval_id');
    }
}

