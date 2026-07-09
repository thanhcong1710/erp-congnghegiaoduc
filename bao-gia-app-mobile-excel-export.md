# BẢNG KÊ CHI PHÍ VÀ MAN-DAY ỨNG DỤNG MOBILE (APP)
*(Bạn có thể bôi đen từng bảng dưới đây (hoặc dùng tổ hợp phím Cmd+C / Ctrl+C) và Paste trực tiếp vào phần mềm Microsoft Excel hoặc Google Sheets. Các cột sẽ tự động phân tách chuẩn xác).*

---

## SHEET 1: BẢNG TỔNG HỢP CHI PHÍ

| TT | Nhóm / Module | Hạng mục / Chức năng | Mô tả chức năng | Báo giá (VNĐ) |
|---|---|---|---|---|
| **I** | **APP PHỤ HUYNH - CORE** | | | **40.000.000** |
| 1 | App Phụ huynh | Đăng nhập & Xác thực | Đăng nhập SĐT, OTP, liên kết hồ sơ nhiều học sinh | 10.000.000 |
| 2 | App Phụ huynh | Lịch học & Điểm danh | Xem lịch học trực quan, lịch sử điểm danh | 10.000.000 |
| 3 | App Phụ huynh | Xin nghỉ & Học bạ | Gửi đơn xin nghỉ phép, xem nhật ký buổi học, bảng điểm | 10.000.000 |
| 4 | App Phụ huynh | Thanh toán Online | Xem học phí, thanh toán qua VNPay, mã QR | 10.000.000 |
| **II** | **APP PHỤ HUYNH - LOYALTY** | | | **35.000.000** |
| 5 | App Phụ huynh | Tích điểm & Hạng thẻ | Hiển thị điểm, hạng thành viên (Vàng/Bạc/Đồng) | 10.000.000 |
| 6 | App Phụ huynh | Đổi thưởng & Quà tặng | Danh mục quà tặng, đổi điểm, sinh mã QR nhận quà | 10.000.000 |
| 7 | App Phụ huynh | Bảng xếp hạng | Hiển thị Leaderboard học sinh điểm cao/chăm chỉ | 5.000.000 |
| 8 | App Phụ huynh | Đánh giá 5 sao | Gửi rating 5 sao và nhận xét buổi học/giáo viên | 10.000.000 |
| **III** | **APP NỘI BỘ (TEACHER/CS)** | | | **45.000.000** |
| 9 | App Giáo viên | Quản lý Lớp học & Điểm danh| Xem lịch dạy, điểm danh, nhập nhận xét bài tập | 15.000.000 |
| 10 | App Giáo viên | Tương tác & Chat | Inbox trực tiếp Phụ huynh, quản lý Tickets hỗ trợ | 15.000.000 |
| 11 | App Giáo viên | Nghiệp vụ Chăm sóc (SLA) | Theo dõi hồ sơ, cảnh báo học sinh vắng, đo lường SLA | 15.000.000 |
| **IV** | **TÍCH HỢP TRÍ TUỆ NHÂN TẠO**| | | **40.000.000** |
| 12 | AI Integration| AI Chatbot Phụ huynh | Trợ lý ảo hỗ trợ giải đáp điểm số, lịch học 24/7 | 15.000.000 |
| 13 | AI Integration| AI Nhận xét tự động | Tự động sinh văn bản nhận xét học sinh dựa trên điểm | 15.000.000 |
| 14 | AI Integration| AI Phân tích Đánh giá | Đọc Sentiment Analysis cảnh báo CSKH kịp thời | 10.000.000 |
| **V** | **HẠ TẦNG & API BACKEND** | | | **65.000.000** |
| 15 | Hạ tầng | Xây dựng API Backend App | Thiết kế và code hệ thống API đồng bộ Web ERP - Mobile App | 45.000.000 |
| 16 | Hạ tầng | Push Notification | Gửi thông báo Firebase đa luồng (học phí, khuyến mãi) | 10.000.000 |
| 17 | Hạ tầng | Phát hành ứng dụng | Nộp ứng dụng lên Apple App Store (iOS) và Google Play | 10.000.000 |
| | | | | |
| **TỔNG** | | | | **225.000.000** |

<br><br>

---

## SHEET 2: BẢNG TÍNH TOÁN THEO MAN-DAY (MD)

*Ghi chú: Đơn giá ước tính là **1.600.000 VNĐ / Man-day**. BA: Business Analyst, DEV: Developer, QC: Quality Control.*

| Module | Chức năng bổ sung | Mô tả yêu cầu ngắn gọn | BA | DEV | QC | Total (MD) |
|---|---|---|---|---|---|---|
| **I. Phụ huynh (Core)** | Đăng nhập & Xác thực | Code UI & logic OTP, login | 1 | 3 | 2 | **6** |
| I. Phụ huynh (Core) | Lịch học & Điểm danh | Calendar view, check-in history | 1 | 3 | 2 | **6** |
| I. Phụ huynh (Core) | Xin nghỉ & Học bạ | Form nghỉ phép, view báo cáo học tập | 1 | 3 | 2 | **6** |
| I. Phụ huynh (Core) | Thanh toán Online | Tích hợp SDK VNPay/MoMo | 1 | 3 | 2 | **6** |
| **II. Phụ huynh (Loyalty)**| Tích điểm & Hạng thẻ | Màn hình hiển thị thẻ thành viên | 1 | 3 | 2 | **6** |
| II. Phụ huynh (Loyalty)| Đổi thưởng & Quà tặng | E-commerce view đổi quà, sinh QR | 1 | 3 | 2 | **6** |
| II. Phụ huynh (Loyalty)| Bảng xếp hạng | UI Leaderboard động | 1 | 2 | 1 | **4** |
| II. Phụ huynh (Loyalty)| Đánh giá 5 sao | Form rating, comment | 1 | 3 | 2 | **6** |
| **III. Giáo viên (CSKH)** | Quản lý Lớp học | Danh sách lớp, tick điểm danh | 2 | 5 | 2 | **9** |
| III. Giáo viên (CSKH) | Tương tác & Chat | Realtime Chat WebSocket | 2 | 5 | 2 | **9** |
| III. Giáo viên (CSKH) | Nghiệp vụ Chăm sóc | SLA tracking, cảnh báo vắng mặt | 2 | 5 | 2 | **9** |
| **IV. Tích hợp AI** | AI Chatbot Phụ huynh | UI Chatbot, kết nối API LLM | 2 | 5 | 2 | **9** |
| IV. Tích hợp AI | AI Nhận xét tự động | Prompt engineering, sinh nhận xét | 2 | 5 | 2 | **9** |
| IV. Tích hợp AI | AI Phân tích Đánh giá | Sentiment mapping, trigger alert | 1 | 4 | 2 | **7** |
| **V. API & Hạ tầng** | Xây dựng API Backend | Core API Server, JWT auth, endpoints | 4 | 18 | 6 | **28** |
| V. API & Hạ tầng | Push Notification | Firebase Cloud Messaging config | 1 | 4 | 2 | **7** |
| V. API & Hạ tầng | Phát hành ứng dụng | Store listing, review processing | 1 | 5 | 2 | **8** |
| | | | | | | |
| **TỔNG CỘNG** | | | | | | **141** |
| **NGÂN SÁCH (MD x 1.6M)**| | | | | | **225.600.000** |
