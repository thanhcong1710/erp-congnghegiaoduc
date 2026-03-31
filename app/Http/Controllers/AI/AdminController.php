<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Models\AiUserPermission;
use App\Models\AiTokenUsage;
use App\Models\AiKnowledgeBase;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * GET /api/ai/admin/users
     * Lấy danh sách users với quyền AI
     */
    public function getUsers(Request $request)
    {
        $perPage = $request->input('per_page', 20);
        $search = $request->input('search', '');

        $query = User::select('users.*')
            ->leftJoin('ai_user_permissions', 'users.id', '=', 'ai_user_permissions.user_id')
            ->with('aiPermission');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $users->map(function ($user) {
                $permission = $user->aiPermission;
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'has_permission' => $permission !== null,
                    'is_enabled' => $permission ? $permission->is_enabled : false,
                    'daily_limit' => $permission ? $permission->daily_token_limit : 0,
                    'monthly_limit' => $permission ? $permission->monthly_token_limit : 0,
                    'permissions' => $permission ? [
                        'can_query_data' => $permission->can_query_data,
                        'can_generate_reports' => $permission->can_generate_reports,
                        'can_access_knowledge' => $permission->can_access_knowledge,
                    ] : null,
                ];
            }),
            'pagination' => [
                'total' => $users->total(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
            ],
        ]);
    }

    /**
     * POST /api/ai/admin/users/{userId}/permission
     * Cập nhật quyền AI cho user
     */
    public function updateUserPermission(Request $request, $userId)
    {
        $request->validate([
            'is_enabled' => 'required|boolean',
            'daily_token_limit' => 'nullable|integer|min:0',
            'monthly_token_limit' => 'nullable|integer|min:0',
            'can_query_data' => 'nullable|boolean',
            'can_generate_reports' => 'nullable|boolean',
            'can_access_knowledge' => 'nullable|boolean',
            'allowed_functions' => 'nullable|array',
        ]);

        $user = User::findOrFail($userId);

        $permission = AiUserPermission::updateOrCreate(
            ['user_id' => $userId],
            [
                'is_enabled' => $request->input('is_enabled'),
                'daily_token_limit' => $request->input('daily_token_limit', 10000),
                'monthly_token_limit' => $request->input('monthly_token_limit', 300000),
                'can_query_data' => $request->input('can_query_data', true),
                'can_generate_reports' => $request->input('can_generate_reports', true),
                'can_access_knowledge' => $request->input('can_access_knowledge', true),
                'allowed_functions' => $request->input('allowed_functions'),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật quyền AI cho ' . $user->name,
            'data' => $permission,
        ]);
    }

    /**
     * GET /api/ai/admin/statistics
     * Thống kê tổng quan
     */
    public function getStatistics(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // Tổng số user có quyền AI
        $totalUsers = AiUserPermission::where('is_enabled', true)->count();

        // Tổng token đã dùng
        $totalTokens = AiTokenUsage::whereBetween('usage_date', [$startDate, $endDate])
            ->sum('total_tokens');

        // Tổng chi phí
        $totalCost = AiTokenUsage::whereBetween('usage_date', [$startDate, $endDate])
            ->sum('estimated_cost');

        // Tổng requests
        $totalRequests = AiTokenUsage::whereBetween('usage_date', [$startDate, $endDate])
            ->count();

        // Top users
        $topUsers = AiTokenUsage::select('user_id', DB::raw('SUM(total_tokens) as total'))
            ->whereBetween('usage_date', [$startDate, $endDate])
            ->groupBy('user_id')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->with('user:id,name,email')
            ->get()
            ->map(function ($item) {
                return [
                    'user_id' => $item->user_id,
                    'user_name' => $item->user->name ?? 'Unknown',
                    'user_email' => $item->user->email ?? '',
                    'total_tokens' => $item->total,
                ];
            });

        // Usage by date
        $dailyUsage = AiTokenUsage::select(
            'usage_date',
            DB::raw('SUM(total_tokens) as tokens'),
            DB::raw('SUM(estimated_cost) as cost'),
            DB::raw('COUNT(*) as requests')
        )
            ->whereBetween('usage_date', [$startDate, $endDate])
            ->groupBy('usage_date')
            ->orderBy('usage_date', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => [
                    'total_users' => $totalUsers,
                    'total_tokens' => $totalTokens,
                    'total_cost' => round($totalCost, 4),
                    'total_requests' => $totalRequests,
                ],
                'top_users' => $topUsers,
                'daily_usage' => $dailyUsage,
            ],
        ]);
    }

    /**
     * GET /api/ai/admin/knowledge
     * Lấy danh sách knowledge base
     */
    public function getKnowledge(Request $request)
    {
        $perPage = $request->input('per_page', 20);
        $category = $request->input('category');

        $query = AiKnowledgeBase::query();

        if ($category) {
            $query->where('category', $category);
        }

        $knowledge = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $knowledge,
        ]);
    }

    /**
     * POST /api/ai/admin/knowledge
     * Tạo knowledge base mới
     */
    public function createKnowledge(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'tags' => 'nullable|array',
        ]);

        $knowledge = AiKnowledgeBase::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Đã tạo tài liệu mới',
            'data' => $knowledge,
        ]);
    }

    /**
     * PUT /api/ai/admin/knowledge/{id}
     * Cập nhật knowledge base
     */
    public function updateKnowledge(Request $request, $id)
    {
        $request->validate([
            'category' => 'nullable|string',
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'tags' => 'nullable|array',
            'is_active' => 'nullable|boolean',
        ]);

        $knowledge = AiKnowledgeBase::findOrFail($id);
        $knowledge->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật tài liệu',
            'data' => $knowledge,
        ]);
    }

    /**
     * DELETE /api/ai/admin/knowledge/{id}
     * Xóa knowledge base
     */
    public function deleteKnowledge($id)
    {
        $knowledge = AiKnowledgeBase::findOrFail($id);
        $knowledge->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa tài liệu',
        ]);
    }
}
