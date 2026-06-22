# Danh sách API Errors

Tài liệu này chứa các lỗi API phức tạp. File này khá nặng nên Agent chỉ được tải vào ngữ cảnh khi thực sự cần thiết (ví dụ: khi gặp lỗi API không xác định).

| Mã lỗi | Mô tả | Cách xử lý |
|---|---|---|
| `ERR_001` | Lỗi kết nối Database | Kiểm tra thông tin trong .env |
| `ERR_002` | Timeout khi gọi API ngoài | Thử lại sau 5 giây hoặc kiểm tra log server |
| `ERR_003` | Invalid Token | Yêu cầu người dùng đăng nhập lại |
