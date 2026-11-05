<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IgbhSummativeResultDetail extends Model
{
    protected $table = 'igbh_summative_result_details';

    protected $fillable = [
        'summative_result_id',
        'sort_no',
        'max_score',
        'score',
        'concept',
        'strategy',
        'calculation',
        'expression',
    ];

    public function summativeResult()
    {
        return $this->belongsTo(IgbhSummativeResult::class, 'summative_result_id');
    }
}

