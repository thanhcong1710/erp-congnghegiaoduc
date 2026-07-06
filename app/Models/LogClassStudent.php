<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Providers\UtilityServiceProvider as u;

class LogClassStudent extends Model
{
    protected $table = 'log_class_students';

    public static function logAction($class_id, $student_id, $contract_id, $action, $creator_id)
    {
        u::insertSimpleRow([
            'class_id' => $class_id,
            'student_id' => $student_id,
            'contract_id' => $contract_id,
            'action' => $action,
            'creator_id' => $creator_id,
            'created_at' => date('Y-m-d H:i:s'),
        ], 'log_class_students');
    }
}
