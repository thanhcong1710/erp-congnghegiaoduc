# 🔧 Sửa lỗi Gemini API - Hướng dẫn chi tiết

## ❌ Các lỗi đã gặp và cách sửa

### Lỗi 1: Missing field `items` trong array parameter
```json
{
  "error": {
    "message": "GenerateContentRequest.tools[0].function_declarations[3].parameters.properties[filters].items: missing field."
  }
}
```

**Nguyên nhân:** Gemini API yêu cầu khi parameter có type `array`, phải định nghĩa `items` schema.

**Giải pháp:** Đã sửa `GeminiService::formatFunctions()`:
```php
// Nếu là array, phải có items schema
if ($type === 'array') {
    $propertySchema['items'] = [
        'type' => 'object',
        'description' => 'Filter item',
    ];
}
```

---

### Lỗi 2: Model not found với v1beta
```json
{
  "error": {
    "code": 404,
    "message": "models/gemini-1.5-flash is not found for API version v1beta"
  }
}
```

**Nguyên nhân:** Model name cũ `gemini-1.5-flash` đã deprecated, cần dùng `gemini-1.5-flash-latest`.

**Giải pháp:** Đã cập nhật:
- `config/ai.php`: `'model' => 'gemini-1.5-flash-latest'`
- `.env`: `GEMINI_MODEL=gemini-1.5-flash-latest`

---

### Lỗi 3: Unknown field "tools" với v1
```json
{
  "error": {
    "code": 400,
    "message": "Invalid JSON payload received. Unknown name \"tools\": Cannot find field."
  }
}
```

**Nguyên nhân:** API v1 KHÔNG hỗ trợ function calling (tools). Chỉ có v1beta mới hỗ trợ.

**Giải pháp:** Phải dùng v1beta cho function calling:
```php
'api_url' => 'https://generativelanguage.googleapis.com/v1beta'
```

---

## ✅ Cấu hình cuối cùng (ĐÚNG)

### File: `config/ai.php`
```php
'gemini' => [
    'api_key' => env('GEMINI_API_KEY', ''),
    'model' => env('GEMINI_MODEL', 'gemini-1.5-flash-latest'),
    'api_url' => 'https://generativelanguage.googleapis.com/v1beta', // v1beta cho function calling
    'max_tokens' => 8192,
    'temperature' => 0.7,
],
```

### File: `.env`
```bash
GEMINI_API_KEY=AIzaSyXXXXXXXXXXXXXXXXXXXXXXXXXXX
GEMINI_MODEL=gemini-1.5-flash-latest  # Thêm -latest
```

---

## 📚 Tổng kết

### Gemini API có 2 phiên bản:

#### 1. **v1** (Stable)
- URL: `https://generativelanguage.googleapis.com/v1`
- ✅ Chat cơ bản
- ❌ KHÔNG hỗ trợ function calling
- Models: `gemini-pro`, `gemini-pro-vision`

#### 2. **v1beta** (Beta - có function calling)
- URL: `https://generativelanguage.googleapis.com/v1beta`
- ✅ Chat cơ bản
- ✅ Function calling (tools)
- Models: `gemini-1.5-flash-latest`, `gemini-1.5-pro-latest`

### Vì chatbot cần function calling → Phải dùng v1beta

---

## 🎯 Models hiện tại (2026)

| Model Name | API Version | Function Calling | Speed | Cost |
|------------|-------------|------------------|-------|------|
| `gemini-1.5-flash-latest` | v1beta | ✅ | Nhanh | Rẻ |
| `gemini-1.5-pro-latest` | v1beta | ✅ | Chậm hơn | Đắt hơn |
| `gemini-pro` | v1 | ❌ | Trung bình | Trung bình |

**Khuyến nghị:** Dùng `gemini-1.5-flash-latest` cho chatbot (nhanh + rẻ + có function calling)

---

## 🔄 Nếu gặp lỗi model not found

### Cách 1: Kiểm tra model có tồn tại
```bash
curl "https://generativelanguage.googleapis.com/v1beta/models?key=YOUR_API_KEY"
```

### Cách 2: Thử các model khác
Trong `.env`:
```bash
# Thử model này
GEMINI_MODEL=gemini-1.5-flash-latest

# Hoặc model này
GEMINI_MODEL=gemini-1.5-pro-latest

# Hoặc model cũ (không có function calling)
GEMINI_MODEL=gemini-pro
```

### Cách 3: Clear cache
```bash
php artisan config:clear
php artisan cache:clear
```

---

## 🚀 Test chatbot

Sau khi sửa xong, test bằng cách:

1. **Refresh trang**
2. **Mở chatbot** (nút tròn tím góc dưới phải)
3. **Chat thử:**
   - "Xin chào!"
   - "Hướng dẫn tôi cách tạo hợp đồng"
   - "Tìm tài liệu về chính sách hoàn tiền"

---

## 📝 Ghi chú quan trọng

1. **API Key phải active:** Vào https://aistudio.google.com/app/apikey kiểm tra
2. **Không dùng v1 cho function calling:** Sẽ lỗi "Unknown field tools"
3. **Model name phải có `-latest`:** Các model cũ đã deprecated
4. **Clear cache sau khi sửa config:** `php artisan config:clear`

---

## ✅ Checklist cuối cùng

- [x] API URL: `v1beta` (không phải v1)
- [x] Model: `gemini-1.5-flash-latest` (có -latest)
- [x] API Key: Đã điền vào `.env`
- [x] Array parameters: Có `items` schema
- [x] Cache: Đã clear
- [x] Build: Thành công

🎉 **Chatbot đã sẵn sàng!**
