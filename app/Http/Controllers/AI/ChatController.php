<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Services\AI\AiAgentService;
use App\Models\AiConversation;
use App\Models\AiUserPermission;
use App\Models\AiTokenUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    protected $aiAgent;

    public function __construct()
    {
        $this->aiAgent = new AiAgentService();
    }

    /**
     * POST /api/ai/chat
     * Gửi tin nhắn tới AI
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'session_id' => 'nullable|string',
        ]);

        $userId = Auth::id();
        $message = $request->input('message');
        $sessionId = $request->input('session_id');

        $response = $this->aiAgent->chat($userId, $message, $sessionId);

        return response()->json($response);
    }

    /**
     * GET /api/ai/conversations
     * Lấy danh sách conversations của user
     */
    public function getConversations(Request $request)
    {
        $userId = Auth::id();

        $conversations = AiConversation::where('user_id', $userId)
            ->with('messages')
            ->orderBy('updated_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($conv) {
                return [
                    'session_id' => $conv->session_id,
                    'title' => $conv->title ?? 'Cuộc hội thoại mới',
                    'is_active' => $conv->is_active,
                    'created_at' => $conv->created_at->format('d/m/Y H:i'),
                    'message_count' => $conv->messages->count(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $conversations,
        ]);
    }

    /**
     * GET /api/ai/conversations/{sessionId}
     * Lấy chi tiết 1 conversation
     */
    public function getConversation($sessionId)
    {
        $userId = Auth::id();

        $conversation = AiConversation::where('session_id', $sessionId)
            ->where('user_id', $userId)
            ->with('messages')
            ->first();

        if (!$conversation) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy cuộc hội thoại',
            ], 404);
        }

        $messages = $conversation->messages->map(function ($msg) {
            return [
                'role' => $msg->role,
                'content' => $msg->content,
                'created_at' => $msg->created_at->format('H:i d/m/Y'),
                'metadata' => $msg->metadata,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'session_id' => $conversation->session_id,
                'title' => $conversation->title,
                'messages' => $messages,
            ],
        ]);
    }

    /**
     * DELETE /api/ai/conversations/{sessionId}
     * Xóa conversation
     */
    public function deleteConversation($sessionId)
    {
        $userId = Auth::id();

        $conversation = AiConversation::where('session_id', $sessionId)
            ->where('user_id', $userId)
            ->first();

        if (!$conversation) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy cuộc hội thoại',
            ], 404);
        }

        $conversation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa cuộc hội thoại',
        ]);
    }

    /**
     * GET /api/ai/quota
     * Lấy thông tin quota của user
     */
    public function getQuota()
    {
        $userId = Auth::id();

        $permission = AiUserPermission::where('user_id', $userId)->first();

        if (!$permission) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa được cấp quyền sử dụng AI',
            ], 403);
        }

        $quota = $permission->hasTokenQuota();
        $stats = AiTokenUsage::getUserStats($userId, 'today');

        return response()->json([
            'success' => true,
            'data' => [
                'is_enabled' => $permission->is_enabled,
                'daily_limit' => $permission->daily_token_limit,
                'monthly_limit' => $permission->monthly_token_limit,
                'daily_used' => $quota['daily_used'],
                'daily_remaining' => $quota['daily_remaining'],
                'monthly_used' => $quota['monthly_used'],
                'monthly_remaining' => $quota['monthly_remaining'],
                'today_stats' => $stats,
            ],
        ]);
    }

    /**
     * POST /api/ai/new-conversation
     * Tạo conversation mới
     */
    public function newConversation()
    {
        $userId = Auth::id();

        $conversation = AiConversation::create([
            'user_id' => $userId,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'session_id' => $conversation->session_id,
            ],
        ]);
    }
}
