<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

$apiKey = config('ai.gemini.api_key');
$apiUrl = config('ai.gemini.api_url', 'https://generativelanguage.googleapis.com/v1beta');
$model = config('ai.gemini.model', 'gemini-1.5-flash');

echo "Testing Gemini API...\n";
echo "API URL: {$apiUrl}\n";
echo "Model: {$model}\n";
echo "API Key (first 5 chars): " . substr($apiKey, 0, 5) . "...\n";

$url = "{$apiUrl}/models/{$model}:generateContent?key={$apiKey}";

$payload = [
    'contents' => [
        [
            'role' => 'user',
            'parts' => [['text' => 'Hi']]
        ]
    ]
];

try {
    $response = Http::withHeaders([
        'x-goog-api-key' => $apiKey
    ])->post($url, $payload);

    echo "Status Code: " . $response->status() . "\n";
    echo "Body: " . $response->body() . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
