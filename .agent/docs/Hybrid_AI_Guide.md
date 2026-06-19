# 🎯 Hybrid AI: Function Calling + Text-to-SQL

## 📋 **Tổng Quan**

Hệ thống AI giờ sử dụng **Hybrid Approach** - kết hợp 2 phương pháp:

1. **Function Calling** (Ưu tiên) - An toàn, chính xác
2. **Text-to-SQL** (Fallback) - Linh hoạt, mở rộng

---

## 🔄 **Luồng Xử Lý:**

```
User hỏi câu hỏi
    ↓
┌───────────────────────────────┐
│ BƯỚC 1: Function Calling      │
│ (Ưu tiên)                     │
└───────────────────────────────┘
    ↓
AI có match function không?
    ├─ CÓ → Execute function → Trả kết quả ✅
    │
    └─ KHÔNG ↓
        ┌───────────────────────────────┐
        │ BƯỚC 2: Text-to-SQL           │
        │ (Fallback cho data queries)   │
        └───────────────────────────────┘
            ↓
        Có phải data query không?
            ├─ CÓ → Generate SQL → Execute → Trả kết quả ✅
            │
            └─ KHÔNG ↓
                ┌───────────────────────────────┐
                │ BƯỚC 3: Normal Chat           │
                │ (Fallback cuối cùng)          │
                └───────────────────────────────┘
                    ↓
                AI trả lời thông thường ✅
```

---

## 💡 **Ví Dụ Thực Tế:**

### **Case 1: Function Calling (Ưu tiên)**

```
👤 User: "Tìm học sinh tên Nguyễn Văn A"

🤖 AI:
  1. Detect: Có function "search_students" match
  2. Call: search_students(keyword="Nguyễn Văn A")
  3. Return: Kết quả từ function

✅ Dùng Function Calling (an toàn, chính xác)
```

### **Case 2: Text-to-SQL (Fallback)**

```
👤 User: "Học sinh nào có điểm IELTS trên 7.0?"

🤖 AI:
  1. Detect: Không có function match chính xác
  2. Detect: Là data query (có từ "học sinh nào", "điểm")
  3. Generate SQL: SELECT s.name FROM students s JOIN scores...
  4. Execute & Return

✅ Dùng Text-to-SQL (linh hoạt)
```

### **Case 3: Normal Chat (Fallback cuối)**

```
👤 User: "Giải thích SOLID principles"

🤖 AI:
  1. Detect: Không có function match
  2. Detect: Không phải data query
  3. Normal chat: Trả lời bằng kiến thức chung

✅ Dùng Normal Chat
```

---

## 🎯 **Ưu Điểm Hybrid Approach:**

| Tiêu Chí | Function Only | Text-to-SQL Only | **Hybrid** |
|----------|---------------|------------------|------------|
| **An toàn** | ⭐⭐⭐ | ⭐⭐ | ⭐⭐⭐ |
| **Linh hoạt** | ⭐ | ⭐⭐⭐ | ⭐⭐⭐ |
| **Chính xác** | ⭐⭐⭐ | ⭐⭐ | ⭐⭐⭐ |
| **Dễ maintain** | ⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐ |
| **Coverage** | ⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐ |

---

## 🚀 **Cách Sử Dụng:**

### **Option 1: Dùng HybridAiService (Recommended)**

```php
use App\Services\AI\HybridAiService;

$hybridAi = app(HybridAiService::class);

$messages = [
    ['role' => 'user', 'content' => 'Có bao nhiêu học sinh?']
];

$response = $hybridAi->chat($messages);

// Response:
// [
//     'success' => true,
//     'type' => 'text_to_sql', // hoặc 'function_call', 'text'
//     'content' => '📊 Kết quả...',
//     'raw_data' => [...]
// ]
```

### **Option 2: Tích hợp vào Controller**

```php
// app/Http/Controllers/AiController.php

use App\Services\AI\HybridAiService;

class AiController extends Controller
{
    public function chat(Request $request, HybridAiService $hybridAi)
    {
        $messages = $request->input('messages');
        
        $response = $hybridAi->chat($messages);
        
        return response()->json($response);
    }
}
```

---

## ⚙️ **Cấu Hình:**

### **1. Bật/Tắt hiển thị SQL trong response:**

```php
// config/ai.php

'show_sql_in_response' => env('AI_SHOW_SQL', false),
```

```env
# .env
AI_SHOW_SQL=true  # Hiển thị SQL query trong response
```

### **2. Thêm keywords cho data query detection:**

```php
// app/Services/AI/HybridAiService.php

protected function isDataQuery($message)
{
    $dataKeywords = [
        'có bao nhiêu', 'tìm', 'danh sách',
        // Thêm keywords của bạn...
    ];
    
    // ...
}
```

### **3. Customize format response:**

```php
// Override formatSqlResult() hoặc formatFunctionResult()
protected function formatSqlResult($sqlResult)
{
    // Custom format của bạn
}
```

---

## 📊 **Monitoring & Logging:**

Hệ thống tự động log để bạn biết AI đang dùng phương pháp nào:

```
[INFO] ✅ Used Function Calling
[INFO] 🔍 Detected data query, trying Text-to-SQL
[INFO] ✅ Used Text-to-SQL
[INFO] 💬 Fallback to normal chat
```

**Xem logs:**
```bash
tail -f storage/logs/laravel.log | grep "Hybrid AI"
```

---

## 🧪 **Testing:**

### **Test Function Calling:**

```bash
php artisan tinker

$hybrid = app(\App\Services\AI\HybridAiService::class);
$result = $hybrid->chat([
    ['role' => 'user', 'content' => 'Tìm học sinh tên An']
]);
dd($result);
```

### **Test Text-to-SQL:**

```bash
$result = $hybrid->chat([
    ['role' => 'user', 'content' => 'Học sinh nào có công nợ trên 1 triệu?']
]);
dd($result);
```

### **Test Normal Chat:**

```bash
$result = $hybrid->chat([
    ['role' => 'user', 'content' => 'Giải thích design patterns']
]);
dd($result);
```

---

## 🎯 **Best Practices:**

### **1. Khi nào nên tạo Function?**

✅ **NÊN tạo function khi:**
- Query phổ biến, dùng nhiều
- Cần control chặt chẽ (bảo mật, performance)
- Business logic phức tạp
- Cần validate input kỹ

❌ **KHÔNG CẦN function khi:**
- Query ad-hoc, ít dùng
- Chỉ là SELECT đơn giản
- Dữ liệu không nhạy cảm

### **2. Optimize Performance:**

```php
// Cache function list
protected $availableFunctions;

public function __construct()
{
    $this->availableFunctions = Cache::remember(
        'ai_functions', 
        3600, 
        fn() => config('ai.functions')
    );
}
```

### **3. Error Handling:**

```php
try {
    $response = $hybridAi->chat($messages);
} catch (\Exception $e) {
    Log::error('AI Chat Error: ' . $e->getMessage());
    return response()->json([
        'error' => 'Xin lỗi, AI đang gặp sự cố'
    ], 500);
}
```

---

## 🔒 **Security:**

### **1. SQL Injection Prevention:**

Text-to-SQL service đã có validation:
- ✅ Chỉ cho phép SELECT
- ✅ Không cho DROP, DELETE, UPDATE...
- ✅ Timeout 5s

### **2. Rate Limiting:**

```php
// Thêm vào HybridAiService
protected function checkRateLimit($userId)
{
    $key = "ai_chat_limit:{$userId}";
    $count = Cache::get($key, 0);
    
    if ($count >= 100) { // 100 requests/day
        throw new \Exception('Rate limit exceeded');
    }
    
    Cache::put($key, $count + 1, now()->endOfDay());
}
```

---

## 📈 **Metrics:**

Track usage để optimize:

```php
// Log metrics
Log::info('AI Usage', [
    'method' => $response['type'], // function_call, text_to_sql, text
    'user_id' => auth()->id(),
    'query' => $userMessage,
    'execution_time' => $executionTime,
]);
```

---

## 🎉 **Kết Luận:**

**Hybrid Approach = Best of Both Worlds!**

✅ **Function Calling:** An toàn, chính xác cho queries quan trọng  
✅ **Text-to-SQL:** Linh hoạt, mở rộng cho queries động  
✅ **Normal Chat:** Fallback cho mọi thứ khác  

**Kết quả:**
- 🎯 Coverage 100%
- 🔒 An toàn tối đa
- 🚀 Linh hoạt tối đa
- 💪 Best user experience

---

**Enjoy your Hybrid AI! 🤖✨**
