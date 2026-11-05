<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsTest extends Model
{
    protected $table = 'lms_tests';

    protected $fillable = [
        'test_type',
        'test_cd',
        'level_cd',
        'test_seq',
        'name',
        'pdf_url',
        'local_pdf_path',
        'status',
    ];
}

