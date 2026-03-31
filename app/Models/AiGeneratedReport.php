<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiGeneratedReport extends Model
{

    protected $fillable = [
        'conversation_id',
        'user_id',
        'report_type',
        'file_path',
        'parameters',
    ];

    protected $casts = [
        'parameters' => 'array',
    ];

    /**
     * Relationships
     */
    public function conversation()
    {
        return $this->belongsTo(AiConversation::class, 'conversation_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Lấy URL download file
     */
    public function getDownloadUrlAttribute()
    {
        return url('storage/' . $this->file_path);
    }
}
