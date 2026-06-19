# ✅ Đã hoàn thành setup lại database cho AI Chatbot

## Database: dev-lapo

## 📋 Các bước đã thực hiện

### 1. ✅ Xóa các bảng AI cũ
```sql
DROP TABLE IF EXISTS ai_generated_reports;
DROP TABLE IF EXISTS ai_token_usage;
DROP TABLE IF EXISTS ai_knowledge_base;
DROP TABLE IF EXISTS ai_messages;
DROP TABLE IF EXISTS ai_conversations;
DROP TABLE IF EXISTS ai_user_permissions;
```

### 2. ✅ Chạy migration tạo 6 bảng mới
```bash
php artisan migrate --path=database/migrations/2026_02_05_095100_create_ai_chatbot_tables.php
```

**Các bảng đã tạo:**
- ✅ `ai_user_permissions` - Quản lý quyền AI của user
- ✅ `ai_conversations` - Lưu cuộc hội thoại
- ✅ `ai_messages` - Lưu tin nhắn
- ✅ `ai_token_usage` - Theo dõi token usage
- ✅ `ai_knowledge_base` - Kho tài liệu
- ✅ `ai_generated_reports` - Báo cáo đã tạo

### 3. ✅ Tạo permission AI cho user admin (ID=1)
```php
App\Models\AiUserPermission::create([
    'user_id' => 1,
    'is_enabled' => true,
    'daily_token_limit' => 50000,
    'monthly_token_limit' => 1500000,
    'can_query_data' => true,
    'can_generate_reports' => true,
    'can_access_knowledge' => true,
]);
```

### 4. ✅ Tạo 4 tài liệu mẫu trong Knowledge Base
- ✅ Cách tạo hợp đồng mới
- ✅ Tra cứu điểm học viên
- ✅ Chính sách hoàn tiền
- ✅ Cách tạo báo cáo bằng AI

### 5. ✅ Tạo permission menu `ai_admin`
```sql
INSERT INTO permissions (name, description, group_id, display_order, status, parent_id)
VALUES ('ai_admin', 'Quản lý AI Chatbot', 1, 99, 1, 0);

INSERT INTO permission_has_role (permission_id, role_id)
SELECT id, 1 FROM permissions WHERE name = 'ai_admin';
```

### 6. ✅ Sửa Models để tương thích Laravel 7
Đã xóa `HasFactory` trait khỏi tất cả models:
- ✅ AiConversation
- ✅ AiMessage
- ✅ AiUserPermission
- ✅ AiTokenUsage
- ✅ AiKnowledgeBase
- ✅ AiGeneratedReport

---

## 🎯 Kết quả

### Database hiện tại: `dev-lapo`

**Bảng AI:**
```
ai_user_permissions      (1 record - user_id=1)
ai_conversations         (0 records)
ai_messages              (0 records)
ai_token_usage           (0 records)
ai_knowledge_base        (4 records - tài liệu mẫu)
ai_generated_reports     (0 records)
```

**Permissions:**
```
ai_admin - Quản lý AI Chatbot (đã gán cho role admin)
```

---

## 🚀 Bước tiếp theo

### 1. Điền API Key vào `.env`
```bash
GEMINI_API_KEY=AIzaSyXXXXXXXXXXXXXXXXXXXXXXXXXXX
```
👉 Lấy tại: https://aistudio.google.com/app/apikey

### 2. Test Chatbot
1. Đăng nhập với user admin (ID=1)
2. Bạn sẽ thấy nút chat tròn màu tím ở góc dưới bên phải
3. Click vào và chat thử!

### 3. Vào Admin Panel
1. Vào menu **"Quản lý AI"** ở sidebar
2. Quản lý quyền user, xem thống kê, quản lý tài liệu

---

## 📊 Kiểm tra dữ liệu

### Kiểm tra permission của user admin:
```sql
SELECT * FROM ai_user_permissions WHERE user_id = 1;
```

### Kiểm tra knowledge base:
```sql
SELECT id, category, title FROM ai_knowledge_base;
```

### Kiểm tra permission menu:
```sql
SELECT p.name, p.description, r.name as role_name
FROM permissions p
JOIN permission_has_role pr ON p.id = pr.permission_id
JOIN roles r ON r.id = pr.role_id
WHERE p.name = 'ai_admin';
```

---

## ✅ Tất cả đã sẵn sàng!

Hệ thống AI Chatbot đã được setup hoàn chỉnh trên database `dev-lapo`.
Bạn chỉ cần điền API key Gemini là có thể sử dụng ngay!
