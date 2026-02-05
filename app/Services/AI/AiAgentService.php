<?php

namespace App\Services\AI;

use App\Models\AiConversation;
use App\Models\AiMessage;
use App\Models\AiUserPermission;
use App\Models\AiTokenUsage;
use App\Models\AiGeneratedReport;
use Illuminate\Support\Facades\Log;

class AiAgentService
{
    protected $gemini;
    protected $executor;

    public function __construct()
    {
        $this->gemini = new GeminiService();
        $this->executor = new FunctionExecutor();
    }

    /**
     * Xử lý tin nhắn từ user
     * 
     * @param int $userId
     * @param string $message
     * @param string|null $sessionId
     * @return array
     */
    public function chat($userId, $message, $sessionId = null)
    {
        try {
            // 1. Kiểm tra quyền
            $permission = AiUserPermission::where('user_id', $userId)->first();

            if (!$permission || !$permission->canUseAi()) {
                return $this->errorResponse('Bạn không có quyền sử dụng AI chatbot. Vui lòng liên hệ quản trị viên.');
            }

            // 2. Kiểm tra quota token
            $quota = $permission->hasTokenQuota(2000); // Ước tính 2000 tokens
            if (!$quota['has_quota']) {
                return $this->errorResponse(
                    "Bạn đã hết hạn mức token hôm nay. Còn lại: {$quota['daily_remaining']} tokens."
                );
            }

            // 3. Lấy hoặc tạo conversation
            $conversation = $this->getOrCreateConversation($userId, $sessionId);

            // 4. Lưu tin nhắn user
            $this->saveMessage($conversation->id, 'user', $message);

            // 5. Chuẩn bị context (lịch sử chat + system prompt)
            $messages = $this->prepareMessages($conversation);

            // 6. Chuẩn bị tools (functions)
            $tools = $this->prepareTools($permission);

            // 7. Gọi AI
            $response = $this->gemini->chat($messages, $tools);

            // 8. Xử lý response
            $result = $this->handleResponse($response, $conversation, $permission);

            // 9. Ghi nhận token usage
            $this->recordTokenUsage(
                $userId,
                $conversation->id,
                $response['usage']['input_tokens'],
                $response['usage']['output_tokens']
            );

            return $result;

        } catch (\Exception $e) {
            Log::error('AI Chat Error: ' . $e->getMessage());
            return $this->errorResponse('Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Lấy hoặc tạo conversation
     */
    protected function getOrCreateConversation($userId, $sessionId)
    {
        if ($sessionId) {
            $conversation = AiConversation::where('session_id', $sessionId)
                ->where('user_id', $userId)
                ->first();

            if ($conversation) {
                return $conversation;
            }
        }

        // Tạo mới
        return AiConversation::create([
            'user_id' => $userId,
            'is_active' => true,
        ]);
    }

    /**
     * Lưu tin nhắn
     */
    protected function saveMessage($conversationId, $role, $content, $metadata = [])
    {
        return AiMessage::create([
            'conversation_id' => $conversationId,
            'role' => $role,
            'content' => $content,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Chuẩn bị messages (system prompt + lịch sử)
     */
    protected function prepareMessages($conversation)
    {
        $messages = [];

        // System prompt
        $messages[] = [
            'role' => 'system',
            'content' => config('ai.system_prompt'),
        ];

        // Lịch sử chat (10 tin nhắn gần nhất)
        $history = $conversation->getContext(config('ai.limits.max_context_messages'));
        $messages = array_merge($messages, $history);

        return $messages;
    }

    /**
     * Chuẩn bị tools (functions)
     */
    protected function prepareTools($permission)
    {
        $allFunctions = config('ai.functions');
        $allowedFunctions = $permission->allowed_functions;

        // Nếu allowed_functions null = cho phép tất cả
        if (empty($allowedFunctions)) {
            return GeminiService::formatFunctions($allFunctions);
        }

        // Lọc chỉ lấy functions được phép
        $filtered = array_filter($allFunctions, function ($key) use ($allowedFunctions) {
            return in_array($key, $allowedFunctions);
        }, ARRAY_FILTER_USE_KEY);

        return GeminiService::formatFunctions($filtered);
    }

    /**
     * Xử lý response từ AI
     */
    protected function handleResponse($response, $conversation, $permission)
    {
        // Nếu AI muốn gọi function
        if ($response['type'] === 'function_call') {
            return $this->handleFunctionCall($response, $conversation, $permission);
        }

        // Response thường (text)
        $assistantMessage = $response['content'];

        // Lưu tin nhắn AI
        $this->saveMessage($conversation->id, 'assistant', $assistantMessage);

        // Tự động tạo title cho conversation nếu chưa có
        if (!$conversation->title) {
            $conversation->generateTitle();
        }

        return [
            'success' => true,
            'message' => $assistantMessage,
            'session_id' => $conversation->session_id,
            'usage' => $response['usage'],
        ];
    }

    /**
     * Xử lý khi AI gọi function
     */
    protected function handleFunctionCall($response, $conversation, $permission)
    {
        $functionName = $response['function_name'];
        $arguments = $response['arguments'];

        // Kiểm tra quyền gọi function này
        if (!$permission->canCallFunction($functionName)) {
            return $this->errorResponse("Bạn không có quyền sử dụng chức năng '{$functionName}'");
        }

        // Thực thi function
        $functionResult = $this->executor->execute($functionName, $arguments);

        // Lưu metadata
        $this->saveMessage($conversation->id, 'assistant', '', [
            'function_called' => $functionName,
            'arguments' => $arguments,
            'result' => $functionResult,
        ]);

        // Nếu là tạo Excel, lưu vào bảng reports
        if ($functionName === 'create_excel_report' && $functionResult['success']) {
            AiGeneratedReport::create([
                'conversation_id' => $conversation->id,
                'user_id' => $conversation->user_id,
                'report_type' => $arguments['report_type'],
                'file_path' => $functionResult['data']['file_path'],
                'parameters' => $arguments,
            ]);
        }

        // Gửi kết quả function lại cho AI để nó viết câu trả lời
        $messages = $this->prepareMessages($conversation);
        $messages[] = [
            'role' => 'user',
            'content' => "Kết quả function '{$functionName}': " . json_encode($functionResult, JSON_UNESCAPED_UNICODE),
        ];

        $finalResponse = $this->gemini->chat($messages, []);

        // Lưu câu trả lời cuối
        $this->saveMessage($conversation->id, 'assistant', $finalResponse['content']);

        return [
            'success' => true,
            'message' => $finalResponse['content'],
            'session_id' => $conversation->session_id,
            'function_called' => $functionName,
            'function_result' => $functionResult,
            'usage' => $response['usage'],
        ];
    }

    /**
     * Ghi nhận token usage
     */
    protected function recordTokenUsage($userId, $conversationId, $inputTokens, $outputTokens)
    {
        AiTokenUsage::record(
            $userId,
            $conversationId,
            $inputTokens,
            $outputTokens,
            config('ai.gemini.model')
        );
    }

    /**
     * Error response
     */
    protected function errorResponse($message)
    {
        return [
            'success' => false,
            'message' => $message,
        ];
    }
}
