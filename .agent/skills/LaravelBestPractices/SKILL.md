---
name: Laravel Best Practices
description: Hướng dẫn viết code Laravel chuẩn, tối ưu và bảo mật cho dự án ERP.
---

# Laravel Best Practices for ERP

Khi viết code cho dự án ERP này, hãy tuân thủ các nguyên tắc sau:

## 1. Controller & Route
- Luôn sử dụng **Resource Controllers** khi có thể.
- Đặt tên route có prefix `admin/` hoặc `user/` rõ ràng.
- Validate request bằng **FormRequest**, không validate trực tiếp trong Controller.
- Response nên sử dụng chuẩn JSON API.

## 2. Eloquent & Database
- Tránh N+1 Query: Sử dụng `with()` khi load quan hệ.
- Sử dụng **Mass Assignment** cẩn thận, khai báo `$fillable`.
- Logic phức tạp nên đưa vào **Service Class** hoặc **Repository Pattern**.
- Sử dụng Scope cho các query thường dùng.

## 3. Frontend (Vue.js)
- Component phải đặt tên PascalCase (Ví dụ: `UserProfile.vue`).
- Không hardcode API URL, hãy dùng file config hoặc biến môi trường `process.env.MIX_APP_URL`.
- Sử dụng Store (Vuex) cho trạng thái dùng chung.

## 4. Helper & Utils
- Chỉ sử dụng Helper cho các hàm tiện ích nhỏ, không chứa logic nghiệp vụ.
- Đặt tên hàm Helper rõ nghĩa, ví dụ: `format_currency()`.
