# Hệ thống AI Chatbot cho ERP - Database Schema

## 📊 Tổng quan Database

Hệ thống gồm **6 bảng chính**:

### 1. `ai_user_permissions` - Quản lý quyền truy cập
Kiểm soát user nào được dùng AI và hạn mức token.

**Các trường quan trọng:**
- `is_enabled`: Bật/tắt quyền dùng AI
- `daily_token_limit`: Giới hạn token/ngày (mặc định: 10,000)
- `monthly_token_limit`: Giới hạn token/tháng (mặc định: 300,000)
- `can_query_data`: Cho phép tra cứu dữ liệu
- `can_generate_reports`: Cho phép tạo báo cáo Excel
- `can_access_knowledge`: Cho phép đọc tài liệu
- `allowed_functions`: Danh sách function được phép gọi (JSON)

**Ví dụ sử dụng:**
```php
// Kiểm tra user có được dùng AI không
$permission = AiUserPermission::where('user_id', $userId)->first();
if ($permission && $permission->canUseAi()) {
    // Cho phép chat
}

// Kiểm tra còn quota không
$quota = $permission->hasTokenQuota(2000);
if ($quota['has_quota']) {
    // Xử lý request
}
```

---

### 2. `ai_token_usage` - Theo dõi token đã sử dụng
Ghi nhận mỗi lần gọi AI để tính chi phí và kiểm soát quota.

**Các trường quan trọng:**
- `input_tokens`: Token input (prompt + context)
- `output_tokens`: Token output (response)
- `total_tokens`: Tổng token
- `model_used`: Model đã dùng (gemini-1.5-flash, gpt-4o...)
- `estimated_cost`: Chi phí ước tính (USD)
- `usage_date`: Ngày sử dụng

**Ví dụ sử dụng:**
```php
// Ghi nhận token usage
AiTokenUsage::record(
    userId: 1,
    conversationId: 123,
    inputTokens: 1500,
    outputTokens: 500,
    model: 'gemini-1.5-flash'
);

// Lấy thống kê
$stats = AiTokenUsage::getUserStats(1, 'today');
// => ['total_tokens' => 5000, 'total_cost' => 0.0015, ...]
```

---

### 3. `ai_conversations` - Phiên chat
Lưu từng phiên hội thoại của user.

**Các trường quan trọng:**
- `session_id`: UUID duy nhất cho mỗi phiên
- `title`: Tiêu đề tự động (từ tin nhắn đầu tiên)
- `is_active`: Phiên đang active hay đã đóng

---

### 4. `ai_messages` - Tin nhắn
Lưu từng tin nhắn trong conversation.

**Các trường quan trọng:**
- `role`: 'user' | 'assistant' | 'system'
- `content`: Nội dung tin nhắn
- `metadata`: Thông tin kỹ thuật (function called, tokens...)

---

### 5. `ai_knowledge_base` - Tài liệu hướng dẫn
Lưu các tài liệu để AI tham khảo khi trả lời.

**Các trường quan trọng:**
- `category`: 'user_guide' | 'faq' | 'policy'
- `title`: Tiêu đề tài liệu
- `content`: Nội dung (hỗ trợ Full-Text Search)
- `tags`: Tags để phân loại (JSON)

**Ví dụ sử dụng:**
```php
// Tìm kiếm tài liệu
$docs = AiKnowledgeBase::search('cách tạo hợp đồng', limit: 3);
```

---

### 6. `ai_generated_reports` - File Excel đã tạo
Lưu lịch sử các file báo cáo AI đã tạo.

**Các trường quan trọng:**
- `report_type`: Loại báo cáo
- `file_path`: Đường dẫn file trong storage
- `parameters`: Tham số đã dùng (JSON)

---

## 🚀 Hướng dẫn Migration

### Bước 1: Chạy migration
```bash
cd /Users/mac24h/Documents/docker-work/src/erp
php artisan migrate
```

### Bước 2: Chạy seeder (tạo dữ liệu mẫu)
```bash
php artisan db:seed --class=AiChatbotSeeder
```

Seeder sẽ tạo:
- ✅ Permission cho admin (user_id = 1)
- ✅ 4 tài liệu mẫu trong knowledge base

---

## 📈 Quản lý quyền user

### Cấp quyền cho user mới
```php
AiUserPermission::createDefault(
    userId: 5,
    isEnabled: true // Bật luôn
);
```

### Tùy chỉnh hạn mức cho user cụ thể
```php
$permission = AiUserPermission::where('user_id', 5)->first();
$permission->update([
    'daily_token_limit' => 20000, // Tăng hạn mức
    'can_generate_reports' => false, // Tắt tính năng tạo Excel
    'allowed_functions' => ['get_student_info', 'search_knowledge'], // Chỉ cho phép 2 function
]);
```

### Tắt quyền AI cho user
```php
$permission->update(['is_enabled' => false]);
```

---

## 💰 Theo dõi chi phí

### Xem chi phí hôm nay
```php
$stats = AiTokenUsage::getUserStats(1, 'today');
echo "Chi phí hôm nay: $" . $stats['total_cost'];
```

### Báo cáo chi phí theo khoảng thời gian
```php
$report = AiTokenUsage::getDailyCostReport('2026-02-01', '2026-02-28');
foreach ($report as $day) {
    echo "{$day->usage_date}: {$day->total_requests} requests, \${$day->total_cost}\n";
}
```

---

## 🔐 Bảo mật

1. **Kiểm tra quyền trước khi xử lý:**
```php
$permission = AiUserPermission::where('user_id', $userId)->first();

if (!$permission || !$permission->canUseAi()) {
    return response()->json(['error' => 'Bạn không có quyền sử dụng AI'], 403);
}

if (!$permission->canCallFunction('create_excel')) {
    return response()->json(['error' => 'Bạn không có quyền tạo báo cáo'], 403);
}

$quota = $permission->hasTokenQuota(2000);
if (!$quota['has_quota']) {
    return response()->json([
        'error' => 'Bạn đã hết hạn mức token',
        'daily_remaining' => $quota['daily_remaining'],
    ], 429);
}
```

2. **Ghi nhận token sau mỗi request:**
```php
AiTokenUsage::record($userId, $conversationId, $inputTokens, $outputTokens);
```

---

## 📝 Các bước tiếp theo

1. ✅ Tạo Config file (`config/ai.php`) - Lưu API key
2. ✅ Tạo Service (`GeminiService.php`) - Kết nối Gemini API
3. ✅ Tạo Controller (`ChatController.php`) - Xử lý request
4. ✅ Tạo Vue Component - Giao diện chat

Bạn muốn tôi tiếp tục phần nào?
