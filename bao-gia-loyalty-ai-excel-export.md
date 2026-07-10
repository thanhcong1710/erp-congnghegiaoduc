# BẢNG KÊ CHI CHI PHÍ VÀ MAN-DAY HỆ THỐNG LOYALTY & AI
*(Bạn có thể bôi đen từng bảng dưới đây (hoặc dùng tổ hợp phím Cmd+C / Ctrl+C) và Paste trực tiếp vào phần mềm Microsoft Excel hoặc Google Sheets. Các cột sẽ tự động phân tách chuẩn xác).*

---

## SHEET 1: BẢNG TỔNG HỢP CHI PHÍ

| TT | Nhóm / Module | Hạng mục / Chức năng | Mô tả chức năng | Báo giá (VNĐ) |
|---|---|---|---|---|
| **I** | **MODULE LOYALTY (CORE)**| | | **42.000.000** |
| 1 | Loyalty | Cơ chế Tích điểm tự động | Tự động cộng điểm khi thanh toán, giới thiệu học viên, chuyên cần | 7.000.000 |
| 2 | Loyalty | Phân hạng thẻ (Tiering) | Quản lý hạng thành viên (Vàng, Bạc, Đồng) kèm đặc quyền, tự nâng/hạ hạng | 7.000.000 |
| 3 | Loyalty | Kho quà tặng (Redeem) | Quản lý kho quà vật lý/voucher, phụ huynh đổi điểm, trừ điểm tự động | 14.000.000 |
| 4 | Loyalty | Hệ thống Đánh giá 5 sao | Cổng Rate 5 sao đánh giá chất lượng buổi học, thu thập ý kiến | 14.000.000 |
| **II** | **MODULE TRÍ TUỆ NHÂN TẠO**| | | **56.000.000** |
| 5 | AI CSKH | AI Chatbot Tư vấn Loyalty | Chatbot hỗ trợ tra cứu điểm số, gợi ý quà tặng, giải đáp chính sách | 21.000.000 |
| 6 | AI CSKH | AI Phân tích Cảm xúc | Đọc nhận xét đánh giá để phân tích thái độ, cảnh báo rủi ro cho CSKH | 14.000.000 |
| 7 | AI CSKH | Tạo nội dung tự động | Tự động soạn tin nhắn cá nhân hóa (chúc mừng sinh nhật, thăng hạng) | 21.000.000 |
| **III** | **GIAO DIỆN NGƯỜI DÙNG** | | | **28.000.000** |
| 8 | Giao diện | Trang hiển thị & Đổi quà | Giao diện Web/App cho Phụ huynh xem điểm và bấm đổi quà | 14.000.000 |
| 9 | Giao diện | Bảng xếp hạng thi đua | Bảng vinh danh học sinh chăm chỉ, điểm cao hàng tháng (Leaderboard) | 14.000.000 |
| **IV** | **HẠ TẦNG KẾT NỐI API** | | | **20.000.000** |
| 10 | Tích hợp | API Đồng bộ ERP | Kết nối để hệ thống ERP tự đẩy dữ liệu đóng tiền sang tính điểm Loyalty | 14.000.000 |
| 11 | Tích hợp | Hệ thống LLM API | Kết nối API với các mô hình ngôn ngữ lớn (OpenAI) để chạy AI | 6.000.000 |
| | | | | |
| **TỔNG** | | | | **146.000.000** |

<br><br>

---

## SHEET 2: BẢNG TÍNH TOÁN THEO MAN-DAY (MD)

*Ghi chú: Đơn giá ước tính là **2.000.000 VNĐ / Man-day**. BA: Business Analyst, DEV: Developer, QC: Quality Control.*

| Module | Chức năng bổ sung | Mô tả yêu cầu ngắn gọn | BA | DEV | QC | Total (MD) |
|---|---|---|---|---|---|---|
| **I. Loyalty Core** | Cơ chế Tích điểm tự động | Lập trình rule cộng điểm tự động | 1 | 2 | 1 | **4** |
| I. Loyalty Core | Phân hạng thẻ (Tiering) | Rule xét hạng định kỳ, ưu đãi hạng | 1 | 2 | 1 | **4** |
| I. Loyalty Core | Kho quà tặng (Redeem) | Quản lý kho, xử lý trừ điểm, tồn kho | 1.5 | 4 | 1.5 | **7** |
| I. Loyalty Core | Hệ thống Đánh giá 5 sao | Luồng thu thập form đánh giá từ App | 1 | 4 | 1 | **6** |
| **II. AI CSKH** | AI Chatbot Tư vấn Loyalty | Lập trình Prompt, tích hợp hội thoại | 2 | 6 | 2 | **10** |
| II. AI CSKH | AI Phân tích Cảm xúc | Sentiment Analysis, trigger cảnh báo | 1 | 5 | 1 | **7** |
| II. AI CSKH | Tạo nội dung tự động | Sinh nội dung chúc mừng cá nhân hóa | 2 | 7 | 2 | **11** |
| **III. Giao diện** | Trang hiển thị & Đổi quà | Vẽ & Lập trình UI màn hình đổi quà | 1 | 5 | 1 | **7** |
| III. Giao diện | Bảng xếp hạng thi đua | Code UI/UX Leaderboard động | 1 | 5 | 1 | **7** |
| **IV. Tích hợp** | API Đồng bộ ERP | Webhook nhận webhook từ phần mềm kế toán | 1 | 5 | 1 | **7** |
| IV. Tích hợp | Hệ thống LLM API | Proxy server xử lý API Call tới OpenAI | 0.5 | 2 | 0.5 | **3** |
| | | | | | | |
| **TỔNG CỘNG** | | | | | | **73** |
| **NGÂN SÁCH (MD x 2.0M)**| | | | | | **146.000.000** |
