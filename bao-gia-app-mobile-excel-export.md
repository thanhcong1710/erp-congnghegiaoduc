# BẢNG KÊ CHI PHÍ VÀ MAN-DAY ỨNG DỤNG MOBILE (APP)
*(Bạn có thể bôi đen từng bảng dưới đây (hoặc dùng tổ hợp phím Cmd+C / Ctrl+C) và Paste trực tiếp vào phần mềm Microsoft Excel hoặc Google Sheets. Các cột sẽ tự động phân tách chuẩn xác).*

---

## SHEET 1: BẢNG TỔNG HỢP CHI PHÍ

| TT | Nhóm / Module | Hạng mục / Chức năng | Mô tả chức năng | Báo giá (VNĐ) |
|---|---|---|---|---|
| **I** | **APP PHỤ HUYNH - CORE** | | | **56.000.000** |
| 1 | App Phụ huynh | Đăng nhập & Xác thực | Đăng nhập SĐT, OTP, liên kết hồ sơ nhiều học sinh | 14.000.000 |
| 2 | App Phụ huynh | Lịch học & Điểm danh | Xem lịch học trực quan, lịch sử điểm danh | 14.000.000 |
| 3 | App Phụ huynh | Xin nghỉ & Học bạ | Gửi đơn xin nghỉ phép, xem nhật ký buổi học, bảng điểm | 14.000.000 |
| 4 | App Phụ huynh | Thanh toán Online | Xem học phí, thanh toán qua VNPay, mã QR | 14.000.000 |
| **II** | **APP PHỤ HUYNH - LOYALTY** | | | **49.000.000** |
| 5 | App Phụ huynh | Tích điểm & Hạng thẻ | Hiển thị điểm, hạng thành viên (Vàng/Bạc/Đồng) | 14.000.000 |
| 6 | App Phụ huynh | Đổi thưởng & Quà tặng | Danh mục quà tặng, đổi điểm, sinh mã QR nhận quà | 14.000.000 |
| 7 | App Phụ huynh | Bảng xếp hạng | Hiển thị Leaderboard học sinh điểm cao/chăm chỉ | 7.000.000 |
| 8 | App Phụ huynh | Đánh giá 5 sao | Gửi rating 5 sao và nhận xét buổi học/giáo viên | 14.000.000 |
| **III** | **APP NỘI BỘ (TEACHER/CS)** | | | **63.000.000** |
| 9 | App Giáo viên | Quản lý Lớp học & Điểm danh| Xem lịch dạy, điểm danh, nhập nhận xét bài tập | 21.000.000 |
| 10 | App Giáo viên | Tương tác & Chat | Inbox trực tiếp Phụ huynh, quản lý Tickets hỗ trợ | 21.000.000 |
| 11 | App Giáo viên | Nghiệp vụ Chăm sóc (SLA) | Theo dõi hồ sơ, cảnh báo học sinh vắng, đo lường SLA | 21.000.000 |
| **IV** | **TÍCH HỢP TRÍ TUỆ NHÂN TẠO**| | | **56.000.000** |
| 12 | AI Integration| AI Chatbot Phụ huynh | Trợ lý ảo hỗ trợ giải đáp điểm số, lịch học 24/7 | 21.000.000 |
| 13 | AI Integration| AI Nhận xét tự động | Tự động sinh văn bản nhận xét học sinh dựa trên điểm | 21.000.000 |
| 14 | AI Integration| AI Phân tích Đánh giá | Đọc Sentiment Analysis cảnh báo CSKH kịp thời | 14.000.000 |
| **V** | **HẠ TẦNG & API BACKEND** | | | **92.000.000** |
| 15 | Hạ tầng | Xây dựng API Backend App | Thiết kế và code hệ thống API đồng bộ Web ERP - Mobile App | 63.000.000 |
| 16 | Hạ tầng | Push Notification | Gửi thông báo Firebase đa luồng (học phí, khuyến mãi) | 15.000.000 |
| 17 | Hạ tầng | Phát hành ứng dụng | Nộp ứng dụng lên Apple App Store (iOS) và Google Play | 14.000.000 |
| | | | | |
| **TỔNG** | | | | **316.000.000** |

<br><br>

---

## SHEET 2: BẢNG TÍNH TOÁN THEO MAN-DAY (MD)

*Ghi chú: Đơn giá ước tính là **2.000.000 VNĐ / Man-day**. BA: Business Analyst, DEV: Developer, QC: Quality Control.*

| Module | Chức năng bổ sung | Mô tả yêu cầu ngắn gọn | BA | DEV | QC | Total (MD) |
|---|---|---|---|---|---|---|
| **I. Phụ huynh (Core)** | Đăng nhập & Xác thực | Code UI & logic OTP, login | 1 | 4 | 2 | **7** |
| I. Phụ huynh (Core) | Lịch học & Điểm danh | Calendar view, check-in history | 1 | 4 | 2 | **7** |
| I. Phụ huynh (Core) | Xin nghỉ & Học bạ | Form nghỉ phép, view báo cáo học tập | 1 | 4 | 2 | **7** |
| I. Phụ huynh (Core) | Thanh toán Online | Tích hợp SDK VNPay/MoMo | 1 | 4 | 2 | **7** |
| **II. Phụ huynh (Loyalty)**| Tích điểm & Hạng thẻ | Màn hình hiển thị thẻ thành viên | 1 | 4 | 2 | **7** |
| II. Phụ huynh (Loyalty)| Đổi thưởng & Quà tặng | E-commerce view đổi quà, sinh QR | 1 | 4 | 2 | **7** |
| II. Phụ huynh (Loyalty)| Bảng xếp hạng | UI Leaderboard động | 0.5 | 2 | 1 | **3.5** |
| II. Phụ huynh (Loyalty)| Đánh giá 5 sao | Form rating, comment | 1 | 4 | 2 | **7** |
| **III. Giáo viên (CSKH)** | Quản lý Lớp học | Danh sách lớp, tick điểm danh | 2 | 6.5 | 2 | **10.5** |
| III. Giáo viên (CSKH) | Tương tác & Chat | Realtime Chat WebSocket | 2 | 6.5 | 2 | **10.5** |
| III. Giáo viên (CSKH) | Nghiệp vụ Chăm sóc | SLA tracking, cảnh báo vắng mặt | 2 | 6.5 | 2 | **10.5** |
| **IV. Tích hợp AI** | AI Chatbot Phụ huynh | UI Chatbot, kết nối API LLM | 2 | 6.5 | 2 | **10.5** |
| IV. Tích hợp AI | AI Nhận xét tự động | Prompt engineering, sinh nhận xét | 2 | 6.5 | 2 | **10.5** |
| IV. Tích hợp AI | AI Phân tích Đánh giá | Sentiment mapping, trigger alert | 1 | 4 | 2 | **7** |
| **V. API & Hạ tầng** | Xây dựng API Backend | Core API Server, JWT auth, endpoints | 4.5 | 21 | 6 | **31.5** |
| V. API & Hạ tầng | Push Notification | Firebase Cloud Messaging config | 1 | 4.5 | 2 | **7.5** |
| V. API & Hạ tầng | Phát hành ứng dụng | Store listing, review processing | 1 | 4 | 2 | **7** |
| | | | | | | |
| **TỔNG CỘNG** | | | | | | **158** |
| **NGÂN SÁCH (MD x 2.0M)**| | | | | | **316.000.000** |
