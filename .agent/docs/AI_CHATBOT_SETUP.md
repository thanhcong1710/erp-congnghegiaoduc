# Hướng dẫn Setup & Sử dụng AI Chatbot

## 📋 Tổng quan

Hệ thống AI Chatbot tích hợp vào ERP, sử dụng Google Gemini API để:
- Trả lời câu hỏi về hệ thống
- Tra cứu dữ liệu (học viên, doanh thu...)
- Tạo báo cáo Excel tự động
- Tìm kiếm tài liệu hướng dẫn

**Chi phí:** ~$0-5/tháng (hoặc miễn phí nếu dùng dưới 1,500 requests/ngày)

---

## 🚀 Bước 1: Lấy API Key từ Google

### 1.1. Truy cập Google AI Studio
Mở trình duyệt và vào: **https://aistudio.google.com/app/apikey**

### 1.2. Đăng nhập
Đăng nhập bằng tài khoản Google (Gmail) của bạn.

### 1.3. Tạo API Key
1. Bấm nút **"Create API Key"**
2. Chọn project (hoặc tạo mới nếu chưa có)
3. Copy API Key (dạng: `AIzaSy...`)

⚠️ **LƯU Ý:** Giữ API key bí mật, không commit lên Git!

---

## 🔧 Bước 2: Cấu hình hệ thống

### 2.1. Cập nhật file `.env`
Mở file `/Users/mac24h/Documents/docker-work/src/erp/.env` và điền API key:

```bash
GEMINI_API_KEY=AIzaSyXXXXXXXXXXXXXXXXXXXXXXXXXXX
```

### 2.2. Chạy Migration (Tạo bảng database)
```bash
cd /Users/mac24h/Documents/docker-work/src/erp
php artisan migrate
```

### 2.3. Chạy Seeder (Tạo dữ liệu mẫu)
```bash
php artisan db:seed --class=AiChatbotSeeder
```

Seeder sẽ tạo:
- ✅ Quyền AI cho admin (user_id = 1)
- ✅ 4 tài liệu hướng dẫn mẫu

### 2.4. Tạo symlink cho storage (Để download file Excel)
```bash
php artisan storage:link
```

---

## 👥 Bước 3: Cấp quyền cho user

### 3.1. Cấp quyền qua code (Artisan Tinker)
```bash
php artisan tinker
```

Trong tinker, chạy:
```php
// Cấp quyền cho user ID = 2
App\Models\AiUserPermission::createDefault(2, true);

// Hoặc tùy chỉnh chi tiết
App\Models\AiUserPermission::create([
    'user_id' => 3,
    'is_enabled' => true,
    'daily_token_limit' => 20000,
    'monthly_token_limit' => 500000,
    'can_query_data' => true,
    'can_generate_reports' => true,
    'can_access_knowledge' => true,
]);
```

### 3.2. Hoặc insert trực tiếp vào DB
```sql
INSERT INTO ai_user_permissions (user_id, is_enabled, daily_token_limit, monthly_token_limit, created_at, updated_at)
VALUES (2, 1, 10000, 300000, NOW(), NOW());
```

---

## 🧪 Bước 4: Test API

### 4.1. Test bằng Postman/cURL

**Endpoint:** `POST /api/ai/chat`

**Headers:**
```
Authorization: Bearer {JWT_TOKEN}
Content-Type: application/json
```

**Body:**
```json
{
  "message": "Xin chào, bạn là ai?"
}
```

**Response mẫu:**
```json
{
  "success": true,
  "message": "Xin chào! Tôi là trợ lý AI của hệ thống ERP giáo dục. Tôi có thể giúp bạn:\n✅ Tra cứu thông tin học viên\n✅ Xem báo cáo doanh thu\n✅ Tạo file Excel\n✅ Tìm tài liệu hướng dẫn\n\nBạn cần tôi giúp gì?",
  "session_id": "9d8f7e6c-5b4a-3c2d-1e0f-9a8b7c6d5e4f",
  "usage": {
    "input_tokens": 150,
    "output_tokens": 80,
    "total_tokens": 230
  }
}
```

### 4.2. Test function calling
```json
{
  "message": "Cho tôi xem thông tin học viên Nguyễn Văn A",
  "session_id": "9d8f7e6c-5b4a-3c2d-1e0f-9a8b7c6d5e4f"
}
```

AI sẽ tự động gọi function `get_student_info` và trả về kết quả.

---

## 📊 Bước 5: Kiểm tra quota & chi phí

### 5.1. API kiểm tra quota
```
GET /api/ai/quota
```

Response:
```json
{
  "success": true,
  "data": {
    "is_enabled": true,
    "daily_limit": 10000,
    "daily_used": 2300,
    "daily_remaining": 7700,
    "monthly_limit": 300000,
    "monthly_used": 15000,
    "monthly_remaining": 285000,
    "today_stats": {
      "total_tokens": 2300,
      "total_cost": 0.00069,
      "total_requests": 12
    }
  }
}
```

### 5.2. Xem chi phí trong database
```sql
SELECT 
    usage_date,
    COUNT(*) as requests,
    SUM(total_tokens) as tokens,
    SUM(estimated_cost) as cost_usd
FROM ai_token_usage
WHERE user_id = 1
GROUP BY usage_date
ORDER BY usage_date DESC
LIMIT 30;
```

---

## 🎨 Bước 6: Tích hợp giao diện Vue (Tùy chọn)

Tôi sẽ tạo component Vue trong bước tiếp theo nếu bạn cần.

---

## 🔍 Troubleshooting

### Lỗi: "Gemini API Error: 400"
**Nguyên nhân:** API key không hợp lệ hoặc chưa điền.
**Giải pháp:** Kiểm tra lại `GEMINI_API_KEY` trong file `.env`.

### Lỗi: "Bạn không có quyền sử dụng AI"
**Nguyên nhân:** User chưa được cấp quyền.
**Giải pháp:** Chạy lại Bước 3 để cấp quyền.

### Lỗi: "Bạn đã hết hạn mức token"
**Nguyên nhân:** User đã dùng hết quota ngày/tháng.
**Giải pháp:** 
- Đợi sang ngày mới (reset daily quota)
- Hoặc tăng hạn mức trong bảng `ai_user_permissions`

### AI trả lời sai hoặc không gọi function
**Nguyên nhân:** System prompt chưa rõ ràng hoặc function description chưa đủ chi tiết.
**Giải pháp:** Chỉnh sửa `config/ai.php` -> `system_prompt` và `functions`.

---

## 📚 Tài liệu API đầy đủ

### 1. POST /api/ai/chat
Gửi tin nhắn tới AI.

**Request:**
```json
{
  "message": "string (required, max 2000)",
  "session_id": "string (optional)"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Câu trả lời từ AI",
  "session_id": "UUID",
  "usage": {
    "input_tokens": 100,
    "output_tokens": 50,
    "total_tokens": 150
  }
}
```

### 2. GET /api/ai/conversations
Lấy danh sách conversations.

### 3. GET /api/ai/conversations/{sessionId}
Xem chi tiết 1 conversation.

### 4. DELETE /api/ai/conversations/{sessionId}
Xóa conversation.

### 5. POST /api/ai/new-conversation
Tạo conversation mới.

### 6. GET /api/ai/quota
Kiểm tra quota còn lại.

---

## 🎯 Các bước tiếp theo

1. ✅ **Tạo giao diện Vue** - Component chat đẹp
2. ✅ **Thêm functions** - Mở rộng khả năng AI
3. ✅ **Upload tài liệu PDF** - Cho AI học thêm
4. ✅ **Tối ưu token** - Giảm chi phí

Bạn muốn tôi làm phần nào tiếp theo?
