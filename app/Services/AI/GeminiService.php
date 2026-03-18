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

    public function __construct()
    {
        $this->apiKey = config('ai.gemini.api_key');
        $this->apiUrl = config('ai.gemini.api_url');
        $this->model = config('ai.gemini.model');
        $this->httpProxy = config('ai.gemini.http_proxy');
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
        set_time_limit(120); // Tăng thời gian thực thi PHP lên 120s

        try {
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

            // Thêm tools nếu có
            if (!empty($tools)) {
                $payload['tools'] = [
                    ['functionDeclarations' => $tools]
                ];
            }

            // Gọi API
            $url = "{$this->apiUrl}/models/{$this->model}:generateContent?key={$this->apiKey}";

            $http = Http::timeout(config('ai.limits.request_timeout'))
                ->withHeaders([
                    'x-goog-api-key' => $this->apiKey
                ]);

            if (!empty($this->httpProxy)) {
                $http = $http->withOptions(['proxy' => $this->httpProxy]);
            }

            $response = $http->post($url, $payload);

            if (!$response->successful()) {
                Log::error('Gemini API Error Detailed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'headers' => $response->headers(),
                    'url' => str_replace($this->apiKey, '***', $url),
                ]);
                throw new \Exception("Gemini API Error: " . $response->body());
            }

            $result = $response->json();

            // Debug: Log response để kiểm tra
            Log::info('Gemini Response', [
                'status' => $response->status(),
                'candidates_count' => count($result['candidates'] ?? []),
                'first_candidate' => $result['candidates'][0] ?? null,
            ]);

            // Parse response
            return $this->parseResponse($result);

        } catch (\Exception $e) {
            Log::error('Gemini API Exception: ' . $e->getMessage());
            throw $e;
        }
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
