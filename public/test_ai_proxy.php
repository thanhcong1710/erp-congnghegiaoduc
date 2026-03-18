<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

$apiKey = config('ai.gemini.api_key');
$apiUrl = config('ai.gemini.api_url');
$proxyKey = config('ai.gemini.proxy_key');

$url = "{$apiUrl}/models/" . config('ai.gemini.model') . ":generateContent?key={$apiKey}";

echo "<h1>--- DIAGNOSTIC GEMINI PROXY ---</h1>";
echo "<p><b>URL:</b> $url</p>";
echo "<p><b>Proxy Key (from .env):</b> " . ($proxyKey ?: '<i>Empty</i>') . "</p>";

$tests = [
    "Test 1: NO Header (URL key only)" => [],
    "Test 2: Authorization: Bearer {key}" => ['Authorization' => "Bearer $proxyKey"],
    "Test 3: x-proxy-key: {key}" => ['x-proxy-key' => $proxyKey],
    "Test 4: Authorization: {key} (No Bearer)" => ['Authorization' => $proxyKey],
    "Test 5: x-goog-api-key header (standard)" => ['x-goog-api-key' => $apiKey],
];

// Thêm các test với URL params
$url_code = $url . "&code=" . urlencode($proxyKey);
$url_proxy = $url . "&proxy_key=" . urlencode($proxyKey);

echo "<h3>Tests with URL Parameters:</h3>";
echo "Testing Test 6: URL param ?code=...<br>";
run_test("Test 6", $url_code, []);

echo "Testing Test 7: URL param ?proxy_key=...<br>";
run_test("Test 7", $url_proxy, []);

foreach ($tests as $name => $headers) {
    echo "<h3>Trying $name...</h3>";
    run_test($name, $url, $headers);
}

function run_test($name, $targetUrl, $headers) {
    try {
        $response = Http::withHeaders($headers)->timeout(30)->post($targetUrl, [
            'contents' => [['parts' => [['text' => 'Hi']]]]
        ]);
        $status = $response->status();
        $body = $response->body();
        
        echo "<div style='padding:10px; border:1px solid #ccc; background:#f9f9f9;'>";
        echo "<b>Status:</b> $status<br>";
        echo "<b>Body:</b> <pre>" . htmlspecialchars(substr($body, 0, 500)) . "</pre>";
        echo "</div>";
        
        if ($status == 200) {
            echo "<p style='color:green;'><b>✅ SUCCESS WITH $name!</b></p>";
        } else {
            echo "<p style='color:red;'><b>❌ FAILED WITH $name!</b></p>";
        }
    } catch (\Exception $e) {
        echo "<p style='color:orange;'><b>Exception:</b> " . $e->getMessage() . "</p>";
    }
    echo "<hr>";
}
echo "<h3>--- END OF TESTS ---</h3>";
