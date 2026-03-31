<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AiUserPermission extends Model
{
    protected $fillable = [
        'user_id',
        'is_enabled',
        'daily_token_limit',
        'monthly_token_limit',
        'can_query_data',
        'can_generate_reports',
        'can_access_knowledge',
        'allowed_functions',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'can_query_data' => 'boolean',
        'can_generate_reports' => 'boolean',
        'can_access_knowledge' => 'boolean',
        'allowed_functions' => 'array',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Kiểm tra user có được phép dùng AI không
     */
    public function canUseAi()
    {
        return $this->is_enabled;
    }

    /**
     * Kiểm tra user có được phép gọi function này không
     */
    public function canCallFunction($functionName)
    {
        if (!$this->is_enabled) {
            return false;
        }

        // Nếu allowed_functions null = cho phép tất cả
        if (empty($this->allowed_functions)) {
            return true;
        }

        return in_array($functionName, $this->allowed_functions);
    }

    /**
     * Kiểm tra user còn trong hạn mức token không
     */
    public function hasTokenQuota($tokensNeeded = 0)
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        // Tính token đã dùng hôm nay
        $dailyUsed = AiTokenUsage::where('user_id', $this->user_id)
            ->where('usage_date', $today)
            ->sum('total_tokens');

        // Tính token đã dùng tháng này
        $monthlyUsed = AiTokenUsage::where('user_id', $this->user_id)
            ->where('usage_date', '>=', $thisMonth)
            ->sum('total_tokens');

        // Kiểm tra
        $dailyRemaining = $this->daily_token_limit - $dailyUsed;
        $monthlyRemaining = $this->monthly_token_limit - $monthlyUsed;

        return [
            'has_quota' => ($dailyRemaining >= $tokensNeeded && $monthlyRemaining >= $tokensNeeded),
            'daily_remaining' => max(0, $dailyRemaining),
            'monthly_remaining' => max(0, $monthlyRemaining),
            'daily_used' => $dailyUsed,
            'monthly_used' => $monthlyUsed,
        ];
    }

    /**
     * Tạo permission mặc định cho user mới
     */
    public static function createDefault($userId, $isEnabled = false)
    {
        return self::create([
            'user_id' => $userId,
            'is_enabled' => $isEnabled,
            'daily_token_limit' => 10000,
            'monthly_token_limit' => 300000,
            'can_query_data' => true,
            'can_generate_reports' => true,
            'can_access_knowledge' => true,
        ]);
    }
}
