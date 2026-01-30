---
description: Kiểm tra chất lượng code trước khi deploy (Lint, Format, Test)
---

# 🚀 Quy trình kiểm tra chất lượng code

1.  Kiểm tra phiên bản PHP và Composer để đảm bảo môi trường:
    // turbo
    php artisan -V && composer -V

2.  Chạy kiểm tra cú pháp (Linting) cơ bản:
    // Nếu có công cụ linting như phpcs, hãy chạy nó. Ở đây ví dụ skip.
    echo "Running lint check..."

3.  Chạy Unit Test (nếu có):
    // turbo
    php artisan test --filter Unit

4.  Kiểm tra frontend build:
    // turbo
    npm run dev

5.  **Review thủ công**:
    - Kiểm tra file `.env.example` nếu có biến môi trường mới.
    - Đảm bảo migration có thể rollback được.
