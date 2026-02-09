<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Log;

/**
 * Hybrid AI Service: Ưu tiên Function Calling → Fallback sang Text-to-SQL
 */
class HybridAiService
{
    protected $geminiService;
    protected $functionExecutor;
    protected $textToSqlService;
    protected $availableFunctions;

    public function __construct(
        GeminiService $geminiService,
        FunctionExecutor $functionExecutor,
        TextToSqlService $textToSqlService
    ) {
        $this->geminiService = $geminiService;
        $this->functionExecutor = $functionExecutor;
        $this->textToSqlService = $textToSqlService;
        $this->availableFunctions = config('ai.functions', []);
    }

    /**
     * Main method: Chat với AI (Hybrid approach)
     * 
     * @param array $messages Lịch sử chat
     * @return array Response
     */
    public function chat(array $messages)
    {
        try {
            // BƯỚC 1: Thử Function Calling trước (ưu tiên)
            $functionResult = $this->tryFunctionCalling($messages);

            if ($functionResult['success']) {
                Log::info('✅ Used Function Calling');
                return $functionResult;
            }

            // BƯỚC 2: Nếu không match function → Kiểm tra có phải data query không
            $lastMessage = end($messages);
            $userMessage = $lastMessage['content'] ?? '';

            if ($this->isDataQuery($userMessage)) {
                Log::info('🔍 Detected data query, trying Text-to-SQL');

                $sqlResult = $this->textToSqlService->ask($userMessage);

                if ($sqlResult['success']) {
                    Log::info('✅ Used Text-to-SQL');

                    // Format kết quả đẹp
                    $formattedResponse = $this->formatSqlResult($sqlResult);

                    return [
                        'success' => true,
                        'type' => 'text_to_sql',
                        'content' => $formattedResponse,
                        'raw_data' => $sqlResult,
                    ];
                }
            }

            // BƯỚC 3: Fallback sang normal chat (không có function, không phải data query)
            Log::info('💬 Fallback to normal chat');
            return $this->normalChat($messages);

        } catch (\Exception $e) {
            Log::error('Hybrid AI Error: ' . $e->getMessage());

            return [
                'success' => false,
                'type' => 'error',
                'content' => '❌ Xin lỗi, đã có lỗi xảy ra: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Thử gọi function calling
     */
    protected function tryFunctionCalling(array $messages)
    {
        try {
            // Format functions cho Gemini
            $tools = GeminiService::formatFunctions($this->availableFunctions);

            // Gọi Gemini với tools
            $response = $this->geminiService->chat($messages, $tools);

            // Nếu AI quyết định gọi function
            if ($response['type'] === 'function_call') {
                $functionName = $response['function_name'];
                $arguments = $response['arguments'];

                Log::info("🔧 Function called: {$functionName}", $arguments);

                // Execute function
                $result = $this->functionExecutor->execute($functionName, $arguments);

                // Format response
                $formattedResponse = $this->formatFunctionResult($functionName, $result);

                return [
                    'success' => true,
                    'type' => 'function_call',
                    'function_name' => $functionName,
                    'content' => $formattedResponse,
                    'raw_data' => $result,
                ];
            }

            // AI không gọi function → return text response
            return [
                'success' => false,
                'type' => 'text',
                'content' => $response['content'] ?? '',
            ];

        } catch (\Exception $e) {
            Log::error('Function Calling Error: ' . $e->getMessage());
            return ['success' => false];
        }
    }

    /**
     * Normal chat (không function, không SQL)
     */
    protected function normalChat(array $messages)
    {
        try {
            $response = $this->geminiService->chat($messages);

            return [
                'success' => true,
                'type' => 'text',
                'content' => $response['content'] ?? '',
            ];

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Kiểm tra có phải data query không
     */
    protected function isDataQuery($message)
    {
        $dataKeywords = [
            // Tiếng Việt
            'có bao nhiêu',
            'tìm',
            'danh sách',
            'thống kê',
            'báo cáo',
            'top',
            'cao nhất',
            'thấp nhất',
            'tổng',
            'trung bình',
            'lớp nào',
            'học sinh nào',
            'ai là',
            'những ai',
            'doanh thu',
            'công nợ',
            'số lượng',

            // Tiếng Anh
            'how many',
            'find',
            'list',
            'search',
            'show me',
            'statistics',
            'report',
            'top',
            'total',
            'average',
            'count',
            'sum',
            'max',
            'min',
        ];

        $messageLower = mb_strtolower($message);

        foreach ($dataKeywords as $keyword) {
            if (mb_strpos($messageLower, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Format kết quả từ Function Calling
     */
    protected function formatFunctionResult($functionName, $result)
    {
        if (!$result['success']) {
            return $result['message'] ?? '❌ Function execution failed';
        }

        // Đơn giản: Trả về JSON đẹp
        return "✅ **Kết quả từ {$functionName}:**\n\n```json\n"
            . json_encode($result['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            . "\n```";
    }

    /**
     * Format kết quả từ Text-to-SQL
     */
    protected function formatSqlResult($sqlResult)
    {
        $output = "📊 **Kết quả tìm kiếm:**\n\n";

        // SQL Query (optional - có thể ẩn)
        if (config('ai.show_sql_in_response', false)) {
            $output .= "```sql\n{$sqlResult['sql']}\n```\n\n";
        }

        // Data dạng bảng markdown
        if (!empty($sqlResult['data'])) {
            $output .= $this->arrayToMarkdownTable($sqlResult['data']);
        } else {
            $output .= "*Không có dữ liệu*\n";
        }

        $output .= "\n\n*Tìm thấy {$sqlResult['count']} kết quả*";

        return $output;
    }

    /**
     * Convert array sang markdown table
     */
    protected function arrayToMarkdownTable($data)
    {
        if (empty($data))
            return '';

        $first = (array) $data[0];
        $headers = array_keys($first);

        // Header
        $table = '| ' . implode(' | ', $headers) . ' |' . "\n";
        $table .= '|' . str_repeat(' --- |', count($headers)) . "\n";

        // Rows (limit 50 để tránh quá dài)
        $limit = min(50, count($data));
        for ($i = 0; $i < $limit; $i++) {
            $row = (array) $data[$i];
            $table .= '| ' . implode(' | ', array_values($row)) . ' |' . "\n";
        }

        if (count($data) > 50) {
            $table .= "\n*... và " . (count($data) - 50) . " kết quả khác*\n";
        }

        return $table;
    }
}
