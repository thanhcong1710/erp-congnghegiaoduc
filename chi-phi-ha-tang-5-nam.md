# BẢNG DỰ TOÁN CHI PHÍ HẠ TẦNG MÁY CHỦ (SERVER INFRASTRUCTURE)
**Tầm nhìn 5 năm: Phục vụ hệ sinh thái ERP, Mobile App & AI**

**CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM**  
**Độc lập - Tự do - Hạnh phúc**  
***

**Kính gửi:** Quý khách hàng / Quý đối tác  
**Dự án:** Triển khai hạ tầng Cloud Server cho hệ thống Giáo dục (EdTech)  
**Mục tiêu:** Mở rộng quy mô linh hoạt (Auto-scaling) từ 25.000 đến 300.000 học sinh trong 5 năm.  

Để đáp ứng được khối lượng dữ liệu khổng lồ từ hệ thống quản trị ERP, hoạt động điểm danh/thanh toán trên Mobile App và các tác vụ xử lý trí tuệ nhân tạo (AI), hệ thống đòi hỏi một kiến trúc hạ tầng (Cloud Infrastructure) mạnh mẽ, bảo mật và có khả năng mở rộng không giới hạn (Scalability).

Dưới đây là bảng dự toán cấu hình và chi phí hạ tầng máy chủ (AWS / Google Cloud / VNPT / Viettel IDC) tương ứng với sự tăng trưởng quy mô học sinh qua từng năm:

---

## I. BẢNG TỔNG HỢP LỘ TRÌNH VÀ CHI PHÍ (5 NĂM)

*Lưu ý: Chi phí mang tính chất ước tính dựa trên mức giá Cloud Server trung bình hiện tại. Đơn vị tính: VNĐ.*

<table border="1" style="border-collapse: collapse; width: 100%;">
  <tr style="background-color: #d9e1f2;">
    <th style="padding: 8px; border: 1px solid black; text-align: center;">Năm</th>
    <th style="padding: 8px; border: 1px solid black; text-align: center;">Quy mô Học sinh</th>
    <th style="padding: 8px; border: 1px solid black; text-align: center;">CCU Ước tính*</th>
    <th style="padding: 8px; border: 1px solid black; text-align: left;">Kiến trúc Hạ tầng (Infrastructure Architecture)</th>
    <th style="padding: 8px; border: 1px solid black; text-align: right;">Chi phí / Tháng</th>
    <th style="padding: 8px; border: 1px solid black; text-align: right;">Chi phí / Năm</th>
  </tr>
  
  <tr>
    <td style="padding: 8px; border: 1px solid black; text-align: center;">**Năm 1**</td>
    <td style="padding: 8px; border: 1px solid black; text-align: center; color: #d9534f;">**25.000**</td>
    <td style="padding: 8px; border: 1px solid black; text-align: center;">~ 1.000</td>
    <td style="padding: 8px; border: 1px solid black;">- 01 Web Server (8 Core, 16GB RAM)<br>- 01 Database Server (4 Core, 8GB RAM)<br>- S3 Object Storage (Lưu trữ ảnh/tài liệu: 500GB)</td>
    <td style="padding: 8px; border: 1px solid black; text-align: right;">~ 5.000.000</td>
    <td style="padding: 8px; border: 1px solid black; text-align: right;">**60.000.000**</td>
  </tr>
  
  <tr>
    <td style="padding: 8px; border: 1px solid black; text-align: center;">**Năm 2**</td>
    <td style="padding: 8px; border: 1px solid black; text-align: center; color: #d9534f;">**40.000**</td>
    <td style="padding: 8px; border: 1px solid black; text-align: center;">~ 2.000</td>
    <td style="padding: 8px; border: 1px solid black;">- Load Balancer (Bộ cân bằng tải)<br>- 02 Web Servers (Chạy song song)<br>- Caching (Redis/Memcached) giảm tải Database<br>- S3 Object Storage (1TB)</td>
    <td style="padding: 8px; border: 1px solid black; text-align: right;">~ 10.000.000</td>
    <td style="padding: 8px; border: 1px solid black; text-align: right;">**120.000.000**</td>
  </tr>
  
  <tr style="background-color: #fff3cd;">
    <td style="padding: 8px; border: 1px solid black; text-align: center;">**Năm 3**</td>
    <td style="padding: 8px; border: 1px solid black; text-align: center; color: #d9534f;">**80.000**</td>
    <td style="padding: 8px; border: 1px solid black; text-align: center;">~ 4.000</td>
    <td style="padding: 8px; border: 1px solid black;">- 03 Web Servers (Auto-scaling tự động tăng giảm)<br>- Master-Slave Database (Tách luồng Đọc/Ghi riêng biệt)<br>- Vector Database (Phục vụ AI xử lý dữ liệu RAG)<br>- CDN (Mạng phân phối nội dung tăng tốc tải file)</td>
    <td style="padding: 8px; border: 1px solid black; text-align: right;">~ 20.000.000</td>
    <td style="padding: 8px; border: 1px solid black; text-align: right;">**240.000.000**</td>
  </tr>
  
  <tr>
    <td style="padding: 8px; border: 1px solid black; text-align: center;">**Năm 4**</td>
    <td style="padding: 8px; border: 1px solid black; text-align: center; color: #d9534f;">**140.000**</td>
    <td style="padding: 8px; border: 1px solid black; text-align: center;">~ 8.000</td>
    <td style="padding: 8px; border: 1px solid black;">- Cụm Microservices / K8s (Kubernetes)<br>- Database Cluster (Cụm DB đồng bộ tốc độ cao)<br>- Message Queue (RabbitMQ/Kafka) xử lý tác vụ nền<br>- ElasticSearch (Tìm kiếm siêu tốc báo cáo 5 năm)</td>
    <td style="padding: 8px; border: 1px solid black; text-align: right;">~ 40.000.000</td>
    <td style="padding: 8px; border: 1px solid black; text-align: right;">**480.000.000**</td>
  </tr>
  
  <tr style="background-color: #d1ecf1;">
    <td style="padding: 8px; border: 1px solid black; text-align: center;">**Năm 5**</td>
    <td style="padding: 8px; border: 1px solid black; text-align: center; color: #d9534f;">**300.000**</td>
    <td style="padding: 8px; border: 1px solid black; text-align: center;">~ 20.000</td>
    <td style="padding: 8px; border: 1px solid black;">- Chuyển đổi hoàn toàn Cloud Native (Multi-AZ bảo mật)<br>- Data Warehouse (Hồ dữ liệu cho BI & Phân tích AI)<br>- Hệ thống CDN Global phủ sóng tốc độ cao<br>- Khả năng chịu lỗi cao (High Availability 99.99%)</td>
    <td style="padding: 8px; border: 1px solid black; text-align: right;">~ 80.000.000</td>
    <td style="padding: 8px; border: 1px solid black; text-align: right;">**960.000.000**</td>
  </tr>
</table>

*\*CCU (Concurrent Users): Số lượng người dùng truy cập và thao tác trên hệ thống cùng một thời điểm (Ví dụ: Lúc 19h00 tối hàng ngàn phụ huynh cùng mở App để điểm danh, đóng học phí, xem điểm).*

---

## II. CHIẾN LƯỢC TỐI ƯU HOÁ CHI PHÍ (COST OPTIMIZATION)

Sự khác biệt khi làm việc với đội ngũ của chúng tôi là **"Không mua thừa tài nguyên"**. Chúng tôi thiết kế hệ thống theo chuẩn **Auto-scaling (Co giãn tự động)**:
1. **Ban ngày (Giờ hành chính):** Chỉ chạy 1-2 Server để phục vụ nhân viên nội bộ thao tác ERP.
2. **Buổi tối (Giờ cao điểm 18h - 21h):** Khi phụ huynh và học sinh truy cập App đồng loạt, hệ thống tự động "nhân bản" lên 4-5 Server để chịu tải.
3. **Đêm khuya:** Hệ thống tự động thu nhỏ lại còn 1 Server để tiết kiệm tiền điện và tài nguyên Cloud.

Bằng kiến trúc này, Quý khách có thể **tiết kiệm được 30% - 40%** chi phí Cloud Server hàng tháng so với việc thuê cố định các máy chủ vật lý truyền thống.

## III. CHI PHÍ QUẢN TRỊ HẠ TẦNG (SYSTEM ADMINISTRATION)

Để vận hành một hệ thống lớn (đặc biệt từ Năm 3 trở đi với hàng chục ngàn dữ liệu mỗi ngày), bên cạnh chi phí thuê phần cứng (trả cho AWS/Google), khách hàng cần có đội ngũ kỹ sư quản trị hệ thống (SysAdmin/DevOps).

**Khách hàng có 2 lựa chọn:**
- **Tự vận hành (In-house):** Khách hàng tự tuyển dụng 1 Kỹ sư System/DevOps (Mức lương thị trường: 25.000.000 - 35.000.000 VNĐ/tháng).
- **Thuê dịch vụ Managed Services (Ủy thác cho chúng tôi):** 
  - Giai đoạn Năm 1 - 2: Phí quản trị, giám sát 24/7 và Backup dữ liệu: **5.000.000 VNĐ/tháng**.
  - Giai đoạn Năm 3 - 5: Phí quản trị cụm máy chủ phức tạp (K8s, Cluster): **15.000.000 VNĐ/tháng** (Rẻ hơn 50% so với tự tuyển dụng nhân sự chuyên trách).

---

**ĐẠI DIỆN ĐƠN VỊ CUNG CẤP DỊCH VỤ**  
*(Ký, ghi rõ họ tên và đóng dấu)*  
