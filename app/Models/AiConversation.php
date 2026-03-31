<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AiConversation extends Model
{

    protected $fillable = [
        'user_id',
        'session_id',
        'title',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Tự động tạo session_id khi tạo conversation mới
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($conversation) {
            if (empty($conversation->session_id)) {
                $conversation->session_id = Str::uuid();
            }
        });
    }

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(AiMessage::class, 'conversation_id');
    }

    public function reports()
    {
        return $this->hasMany(AiGeneratedReport::class, 'conversation_id');
    }

    /**
     * Lấy lịch sử chat (context) để gửi cho AI
     * @param int $limit Số tin nhắn gần nhất
     */
    public function getContext($limit = 10)
    {
        return $this->messages()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->map(function ($message) {
                return [
                    'role' => $message->role,
                    'content' => $message->content,
                ];
            })
            ->toArray();
    }

    /**
     * Tự động tạo title từ tin nhắn đầu tiên
     */
    public function generateTitle()
    {
        $firstMessage = $this->messages()->where('role', 'user')->first();
        if ($firstMessage) {
            $this->title = Str::limit($firstMessage->content, 50);
            $this->save();
        }
    }
}
