# BẢNG KÊ CHI PHÍ HẠ TẦNG & QUẢN TRỊ SERVER (5 NĂM)
*(Bạn có thể bôi đen từng bảng dưới đây (hoặc dùng tổ hợp phím Cmd+C / Ctrl+C) và Paste trực tiếp vào phần mềm Microsoft Excel hoặc Google Sheets. Các cột sẽ tự động phân tách chuẩn xác).*

---

## SHEET 1: BẢNG TỔNG HỢP CHI PHÍ CLOUD SERVER (AWS / GOOGLE CLOUD)

*Ghi chú: Đây là chi phí phần cứng (Hardware) thanh toán trực tiếp cho nhà cung cấp dịch vụ Cloud. Đã bao gồm tính năng tự động thu nhỏ hệ thống vào ban đêm (Auto-scaling) để tiết kiệm 30% ngân sách.*

| Năm | Quy mô Học sinh | CCU Ước tính | Kiến trúc Hạ tầng (Infrastructure Architecture) | Chi phí / Tháng (VNĐ) | Chi phí / Năm (VNĐ) |
|---|---|---|---|---|---|
| **Năm 1** | 25.000 | ~ 1.000 | 01 Web Server, 01 Database Server, S3 Storage 500GB | 5.000.000 | **60.000.000** |
| **Năm 2** | 40.000 | ~ 2.000 | Load Balancer, 02 Web Servers, Caching (Redis), S3 1TB | 10.000.000 | **120.000.000** |
| **Năm 3** | 80.000 | ~ 4.000 | 03 Web Servers, Master-Slave DB, Vector DB (AI), CDN | 20.000.000 | **240.000.000** |
| **Năm 4** | 140.000 | ~ 8.000 | Cụm Microservices K8s, DB Cluster, Message Queue | 40.000.000 | **480.000.000** |
| **Năm 5** | 300.000 | ~ 20.000 | Data Warehouse, Cloud Native Multi-AZ, Global CDN | 80.000.000 | **960.000.000** |

<br><br>

---

## SHEET 2: BẢNG TÍNH CHI PHÍ QUẢN TRỊ HỆ THỐNG (MANAGED SERVICES) THEO MAN-DAY

*Ghi chú: Để hệ thống Cloud vận hành trơn tru, thay vì tự tuyển dụng 1 Kỹ sư DevOps (Lương 25-35tr/tháng), Quý khách có thể sử dụng gói Managed Services (Uỷ thác quản trị) của chúng tôi. Đơn giá Kỹ sư DevOps: **1.600.000 VNĐ / Man-day (MD)**.*

| Giai đoạn | Mức độ phức tạp | Hạng mục công việc (DevOps / SysAdmin) | Số Man-day / Tháng | Đơn giá MD (VNĐ) | Phí Dịch vụ / Tháng (VNĐ) | Phí Dịch vụ / Năm (VNĐ) |
|---|---|---|---|---|---|---|
| **Năm 1 - Năm 2** | Cơ bản | Giám sát Uptime 24/7, Backup DB hàng ngày, Cập nhật bảo mật, Xử lý sự cố Cấp 1 | **3 MD** | 1.600.000 | **4.800.000** | **57.600.000** |
| **Năm 3 - Năm 5** | Phức tạp (Cluster) | Quản trị cụm Kubernetes (K8s), Cấu hình Auto-scaling, Tối ưu hóa Database, Chống DDoS | **9.5 MD** | 1.600.000 | **15.200.000** | **182.400.000** |
