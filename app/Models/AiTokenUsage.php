<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AiTokenUsage extends Model
{
    protected $table = 'ai_token_usage';

    protected $fillable = [
        'user_id',
        'conversation_id',
        'input_tokens',
        'output_tokens',
        'total_tokens',
        'model_used',
        'estimated_cost',
        'usage_date',
    ];

    protected $casts = [
        'usage_date' => 'date',
        'estimated_cost' => 'decimal:6',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function conversation()
    {
        return $this->belongsTo(AiConversation::class, 'conversation_id');
    }

    /**
     * Tự động set usage_date khi tạo
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($usage) {
            if (empty($usage->usage_date)) {
                $usage->usage_date = Carbon::today();
            }
        });
    }

    /**
     * Ghi nhận token usage và tính chi phí
     */
    public static function record($userId, $conversationId, $inputTokens, $outputTokens, $model = 'gemini-1.5-flash')
    {
        $totalTokens = $inputTokens + $outputTokens;
        $cost = self::calculateCost($inputTokens, $outputTokens, $model);

        return self::create([
            'user_id' => $userId,
            'conversation_id' => $conversationId,
            'input_tokens' => $inputTokens,
            'output_tokens' => $outputTokens,
            'total_tokens' => $totalTokens,
            'model_used' => $model,
            'estimated_cost' => $cost,
        ]);
    }

    /**
     * Tính chi phí dựa trên model
     */
    public static function calculateCost($inputTokens, $outputTokens, $model)
    {
        $pricing = [
            'gemini-1.5-flash' => ['input' => 0.075, 'output' => 0.30],
            'gemini-1.5-flash-latest' => ['input' => 0.075, 'output' => 0.30],
            'gemini-2.5-flash' => ['input' => 0.075, 'output' => 0.30],
            'gemini-1.5-pro' => ['input' => 1.25, 'output' => 5.00],
            'gpt-4o-mini' => ['input' => 0.15, 'output' => 0.60],
            'gpt-4o' => ['input' => 2.50, 'output' => 10.00],
        ];

        $price = $pricing[$model] ?? $pricing['gemini-1.5-flash'];

        $inputCost = ($inputTokens / 1000000) * $price['input'];
        $outputCost = ($outputTokens / 1000000) * $price['output'];

        return $inputCost + $outputCost;
    }

    /**
     * Lấy thống kê token usage của user
     */
    public static function getUserStats($userId, $period = 'today')
    {
        $query = self::where('user_id', $userId);

        switch ($period) {
            case 'today':
                $query->where('usage_date', Carbon::today());
                break;
            case 'this_month':
                $query->where('usage_date', '>=', Carbon::now()->startOfMonth());
                break;
            case 'last_30_days':
                $query->where('usage_date', '>=', Carbon::now()->subDays(30));
                break;
        }

        return [
            'total_tokens' => $query->sum('total_tokens'),
            'total_cost' => $query->sum('estimated_cost'),
            'total_requests' => $query->count(),
            'input_tokens' => $query->sum('input_tokens'),
            'output_tokens' => $query->sum('output_tokens'),
        ];
    }

    /**
     * Lấy báo cáo chi phí theo ngày
     */
    public static function getDailyCostReport($startDate, $endDate)
    {
        return self::selectRaw('
                usage_date,
                COUNT(*) as total_requests,
                SUM(total_tokens) as total_tokens,
                SUM(estimated_cost) as total_cost,
                COUNT(DISTINCT user_id) as unique_users
            ')
            ->whereBetween('usage_date', [$startDate, $endDate])
            ->groupBy('usage_date')
            ->orderBy('usage_date', 'desc')
            ->get();
    }
}
