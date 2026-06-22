# Demo Rule

File `rules/demo.md` này định nghĩa các "quy tắc toàn cục" (Global Rules). Các nội dung trong thư mục này sẽ luôn được tự động chèn vào Prompt hệ thống (system prompt) của tôi ở mọi cuộc trò chuyện, bắt buộc tôi phải **luôn luôn tuân thủ** dù bạn không nhắc lại.

**Ví dụ các quy tắc bạn có thể thiết lập ở đây:**
1. **Tiêu chuẩn Code:** "Bất cứ khi nào tạo một file code mới, luôn phải có dòng comment ở đầu file: `// Được tự động sinh bởi Antigravity`."
2. **Phong cách giao tiếp:** "Luôn trả lời ngắn gọn dưới 3 câu và dùng tiếng Việt thân thiện."
3. **Quy tắc an toàn (Quan trọng):** "Tuyệt đối không bao giờ được phép tự động chạy lệnh `rm -rf` hoặc xóa thư mục chứa mã nguồn."
4. **Quy tắc công nghệ:** "Nếu không được chỉ định, luôn sử dụng Laravel 11 và Vue 3."

Mỗi khi bạn lưu file này, tôi sẽ đọc được nó như một phần của bản ngã hệ thống.
