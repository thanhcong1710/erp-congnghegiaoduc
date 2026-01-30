# ERP Project Rules

1.  **Code Security**:
    -   Không bao giờ commit API Key hoặc mật khẩu vào code.
    -   Sử dụng biến môi trường `.env` cho thông tin nhạy cảm.

2.  **Linting & Formatting**:
    -   Sử dụng PSR-12 cho PHP (chuẩn hiện đại).
    -   JavaScript/Vue.js nên dùng ESLint (nếu có).

3.  **Clean Code**:
    -   Hàm không quá 30 dòng.
    -   Tên biến/phương thức phải rõ nghĩa bằng Tiếng Anh.
    -   Comment logic bằng Tiếng Việt.

4.  **Database**:
    -   Luôn đặt `index` cho các foreign key.
    -   Mỗi bảng phải có `created_at` và `updated_at`.
