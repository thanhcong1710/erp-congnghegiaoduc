<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $apiKey;
    protected $apiUrl;
    protected $model;
    protected $httpProxy;
    protected $proxyKey;

    // Danh sách model dự phòng (theo thứ tự ưu tiên) khi model chính bị 404
    protected array $fallbackModels = [
        'gemini-2.5-flash',
        'gemini-3.5-flash',
        'gemini-flash-latest',
    ];

    public function __construct()
    {
        $this->apiKey = config('ai.gemini.api_key');
        $this->apiUrl = config('ai.gemini.api_url');
        $this->model = config('ai.gemini.model');
        $this->httpProxy = config('ai.gemini.http_proxy');
        $this->proxyKey = config('ai.gemini.proxy_key');
    }

    /**
     * Gửi request generate content đơn giản tới Gemini
     *
     * @param string $prompt Nội dung câu hỏi
     * @return string
     */
    public function generateContent(string $prompt)
    {
        $response = $this->chat([
            ['role' => 'user', 'content' => $prompt]
        ]);
        return $response['content'] ?? '';
    }

    /**
     * Gửi request chat tới Gemini
     * 
     * @param array $messages Lịch sử chat [{'role': 'user', 'content': '...'}]
     * @param array $tools Danh sách function AI có thể gọi
     * @return array
     */
    public function chat(array $messages, array $tools = [])
    {
        set_time_limit(120);

        // Convert messages sang format Gemini
        $contents = $this->formatMessages($messages);

        // Chuẩn bị payload
        $payload = [
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => config('ai.gemini.temperature'),
                'maxOutputTokens' => config('ai.gemini.max_tokens'),
            ],
        ];

        if (!empty($tools)) {
            $payload['tools'] = [
                ['functionDeclarations' => $tools]
            ];
        }

        // Chuẩn bị danh sách model sẽ thử (model chính + fallback)
        $modelsToTry = array_unique(array_merge(
            [$this->model],
            $this->fallbackModels
        ));

        $maxRetries = 3;
        $retryDelays = [2, 4, 8]; // giây cho retry 503

        foreach ($modelsToTry as $modelName) {
            $this->model = $modelName; // Tạm thời đổi model cho lần gọi này

            for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                try {
                    $result = $this->callApi($payload, $attempt);
                    return $this->parseResponse($result);

                } catch (\Exception $e) {
                    $is503 = str_contains($e->getMessage(), '503')
                          || str_contains($e->getMessage(), 'high demand')
                          || str_contains($e->getMessage(), 'UNAVAILABLE');

                    $is404 = str_contains($e->getMessage(), '404')
                          || str_contains($e->getMessage(), 'no longer available')
                          || str_contains($e->getMessage(), 'NOT_FOUND');

                    if ($is503 && $attempt < $maxRetries) {
                        // 503: Thử lại cùng model sau vài giây
                        $delay = $retryDelays[$attempt - 1];
                        Log::warning("Gemini 503 [{$modelName}] - Thử lại lần {$attempt}/{$maxRetries} sau {$delay}s...");
                        sleep($delay);
                        continue;
                    }

                    if ($is404) {
                        // 404: Model bị khai tử → thử model tiếp theo
                        Log::warning("Gemini 404 [{$modelName}] - Model không còn khả dụng, thử model tiếp theo...");
                        break; // Thoát vòng retry, chuyển sang model tiếp
                    }

                    // Lỗi khác (400, 401...) → throw ngay
                    Log::error('Gemini API Error (final): ' . $e->getMessage());
                    throw $e;
                }
            }
        }

        // Đã thử hết tất cả model mà vẫn thất bại
        throw new \Exception('Tất cả Gemini models đều không khả dụng. Vui lòng thử lại sau.');
    }

    /**
     * Thực hiện 1 lần gọi HTTP đến Gemini API
     */
    protected function callApi(array $payload, int $attempt = 1): array
    {
        // Khi dùng proxy: KHÔNG đưa key vào URL query string để tránh bị Vercel logs ghi lại
        // Key chỉ truyền qua HTTPS header (an toàn hơn)
        $isUsingProxy = $this->apiUrl !== 'https://generativelanguage.googleapis.com/v1beta';

        if ($isUsingProxy) {
            // Proxy URL: key chỉ trong header, không trong URL
            $url = "{$this->apiUrl}/models/{$this->model}:generateContent";
        } else {
            // Direct Google API: key trong URL (theo chuẩn của Google)
            $url = "{$this->apiUrl}/models/{$this->model}:generateContent?key={$this->apiKey}";
        }

        $headers = ['x-goog-api-key' => $this->apiKey];

        if (!empty($this->proxyKey)) {
            $headers['Authorization'] = "Bearer {$this->proxyKey}";
        }

        $http = Http::timeout(config('ai.limits.request_timeout'))
            ->withHeaders($headers);

        if (!empty($this->httpProxy)) {
            $http = $http->withOptions(['proxy' => $this->httpProxy]);
        }

        $response = $http->post($url, $payload);

        if (!$response->successful()) {
            Log::error('Gemini API Error', [
                'attempt' => $attempt,
                'status'  => $response->status(),
                'body'    => $response->body(),
                'url'     => str_replace($this->apiKey, '***', $url),
            ]);
            throw new \Exception("Gemini API Error [{$response->status()}]: " . $response->body());
        }

        $result = $response->json();

        Log::info('Gemini Response', [
            'attempt'          => $attempt,
            'candidates_count' => count($result['candidates'] ?? []),
        ]);

        return $result;
    }

    /**
     * Convert messages từ format chuẩn sang format Gemini
     */
    protected function formatMessages(array $messages)
    {
        $contents = [];

        foreach ($messages as $message) {
            $role = $message['role'] === 'assistant' ? 'model' : 'user';

            // Gemini không có role 'system', merge vào user message đầu tiên
            if ($message['role'] === 'system') {
                $role = 'user';
            }

            $contents[] = [
                'role' => $role,
                'parts' => [
                    ['text' => $message['content']]
                ]
            ];
        }

        return $contents;
    }

    /**
     * Parse response từ Gemini
     */
    protected function parseResponse(array $response)
    {
        $candidate = $response['candidates'][0] ?? null;

        if (!$candidate) {
            throw new \Exception('No response from Gemini');
        }

        $content = $candidate['content'] ?? null;
        $parts = $content['parts'] ?? [];

        // Kiểm tra có function call không (thường ở part đầu tiên)
        if (isset($parts[0]['functionCall'])) {
            return [
                'type' => 'function_call',
                'function_name' => $parts[0]['functionCall']['name'],
                'arguments' => $parts[0]['functionCall']['args'] ?? [],
                'usage' => $this->extractUsage($response),
            ];
        }

        // Response thường: Ghép tất cả các parts lại
        $fullText = '';
        foreach ($parts as $part) {
            if (isset($part['text'])) {
                $fullText .= $part['text'];
            }
        }

        return [
            'type' => 'text',
            'content' => $fullText,
            'usage' => $this->extractUsage($response),
        ];
    }

    /**
     * Trích xuất token usage từ response
     */
    protected function extractUsage(array $response)
    {
        $metadata = $response['usageMetadata'] ?? [];

        return [
            'input_tokens' => $metadata['promptTokenCount'] ?? 0,
            'output_tokens' => $metadata['candidatesTokenCount'] ?? 0,
            'total_tokens' => $metadata['totalTokenCount'] ?? 0,
        ];
    }

    /**
     * Format function declarations cho Gemini
     */
    public static function formatFunctions(array $functions)
    {
        $formatted = [];

        foreach ($functions as $name => $config) {
            $properties = [];
            $required = [];

            foreach ($config['parameters'] as $paramName => $paramType) {
                // Parse type (VD: "string|required" -> type: string, required: true)
                $parts = explode('|', $paramType);
                $type = $parts[0];
                $isRequired = in_array('required', $parts);

                $propertySchema = [
                    'type' => $type === 'integer' ? 'number' : $type,
                    'description' => "Parameter: {$paramName}",
                ];

                // Nếu là array, phải có items schema
                if ($type === 'array') {
                    $propertySchema['items'] = [
                        'type' => 'object',
                        'description' => 'Filter item',
                    ];
                }

                $properties[$paramName] = $propertySchema;

                if ($isRequired) {
                    $required[] = $paramName;
                }
            }

            $formatted[] = [
                'name' => $name,
                'description' => $config['description'],
                'parameters' => [
                    'type' => 'object',
                    'properties' => $properties,
                    'required' => $required,
                ],
            ];
        }

        return $formatted;
    }
}
