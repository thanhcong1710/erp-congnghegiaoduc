<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $apiKey;
    protected $apiUrl;
    protected $model;

    public function __construct()
    {
        $this->apiKey = config('ai.gemini.api_key');
        $this->apiUrl = config('ai.gemini.api_url');
        $this->model = config('ai.gemini.model');
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

            $response = Http::timeout(config('ai.limits.request_timeout'))
                ->post($url, $payload);

            if (!$response->successful()) {
                throw new \Exception("Gemini API Error: " . $response->body());
            }

            $result = $response->json();

            // Parse response
            return $this->parseResponse($result);

        } catch (\Exception $e) {
            Log::error('Gemini API Error: ' . $e->getMessage());
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
        $parts = $content['parts'][0] ?? null;

        // Kiểm tra có function call không
        if (isset($parts['functionCall'])) {
            return [
                'type' => 'function_call',
                'function_name' => $parts['functionCall']['name'],
                'arguments' => $parts['functionCall']['args'] ?? [],
                'usage' => $this->extractUsage($response),
            ];
        }

        // Response thường
        return [
            'type' => 'text',
            'content' => $parts['text'] ?? '',
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
