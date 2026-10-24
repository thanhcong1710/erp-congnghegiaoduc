# HƯỚNG DẪN CHUYỂN SERVER GCP GIỮA 2 TÀI KHOẢN (ZERO DATA LOSS RUNBOOK)

> **Mục tiêu:** Chuyển đổi định kỳ (2 - 3 tháng) giữa 2 tài khoản Google Cloud Platform (GCP Trial rotation) cho máy chủ chạy **Nginx, PHP, MySQL, Node.js (với 10 domain)**.  
> **Cam kết:** Giữ nguyên 100% dữ liệu (Code, Uploads, Database, SSL, Nginx Config) ở cả 2 server, hạn chế tối đa downtime và không bị rủi ro mất mát dữ liệu.

---

## I. TỔNG QUAN CƠ CHẾ HOẠT ĐỘNG

Thay vì cài đặt lại môi trường và cấu hình 10 domain thủ công, ta sử dụng tính năng **Custom Image (hoặc Machine Image) của GCP**:
- Nhân bản nguyên khối ổ cứng (Byte-by-Byte Block Clone).
- Khi Server B sinh ra từ Image: **Code, Uploads trong `/var/www`, Database MySQL, cấu hình Nginx của 10 domain và chứng chỉ SSL** đã có sẵn và hoạt động ngay tức thì.

---

## II. QUY TRÌNH THỰC HIỆN CHI TIẾT (STEP-BY-STEP)

```
[TÀI KHOẢN 1 (Server A)]                                     [TÀI KHOẢN 2 (Server B)]
         │                                                            │
         ├── 1. Tạo Custom Image từ Disk của A                        │
         ├── 2. Phân quyền IAM (Compute Image User) cho TK 2 ───────> │
         │                                                            ├── 3. Tạo VM mới từ Image của TK 1
         │                                                            │   (Có sẵn 10 domain & môi trường)
         ├── 4. Bắn Database mới nhất sang B qua SSH ───────────────> ├── 4. Nhận DB mới nhất
         │                                                            │
         ├── 5. (Tùy chọn) ProxyPass traffic tạm thời ──────────────> ├── 5. Nhận toàn bộ người dùng
         │                                                            │
         └── 6. Xong ────────────────────────────────────────────────>└── 7. Tạo Image độc lập trên TK 2
```

---

### BƯỚC 1: Tạo Image & Phân quyền trên Tài khoản 1 (Server A)

1. **Lấy Project ID của Tài khoản 1:**
   - Nhìn lên góc trên bên trái màn hình GCP hoặc trên thanh URL.
   - Ví dụ: `project-0744ad46-ddc4-4cda-a89` *(Ghi nhớ lại mã này)*.

2. **Tạo Custom Image từ ổ đĩa:**
   - Vào menu bên trái: **Compute Engine** ➔ cuộn xuống mục **Storage** ➔ chọn **Images**.
   - Bấm **CREATE IMAGE**:
     - **Name:** `server-image-v1`
     - **Source:** Chọn **Disk** ➔ Chọn ổ đĩa của Server A (ví dụ: `conglt`).
     - Bấm **Create** (chờ 2 - 3 phút để trạng thái chuyển sang dấu tích xanh `Ready`).

3. **Chia sẻ quyền cho Tài khoản 2:**
   - Vào **IAM & Admin** ➔ **IAM**.
   - Bấm **GRANT ACCESS** (hoặc nút **ADD**):
     - **New principals:** Nhập chính xác địa chỉ **Gmail của Tài khoản 2**.
     - **Role:** Tìm và gán quyền **`Compute Image User`** (hoặc `Compute Admin`).
     - Bấm **Save**.

---

### BƯỚC 2: Khởi tạo Server B trên Tài khoản 2

Đăng nhập vào **Tài khoản 2**, bạn có thể chọn 1 trong 2 cách:

#### 👉 Cách A: Dùng 1 lệnh Cloud Shell (Nhanh nhất - 10 giây)
Bấm biểu tượng Cloud Shell (`>_` ở góc trên bên phải màn hình GCP) và dán lệnh:
```bash
gcloud compute instances create server-b \
    --image=server-image-v1 \
    --image-project=project-0744ad46-ddc4-4cda-a89 \
    --zone=asia-southeast1-a \
    --machine-type=e2-medium
```
*(Thay `project-0744ad46-ddc4-4cda-a89` bằng Project ID của TK 1 và `asia-southeast1-a` bằng vị trí bạn muốn).*

#### 👉 Cách B: Chọn trên giao diện GCP Console
1. Vào **Compute Engine** ➔ **VM instances** ➔ Bấm **CREATE INSTANCE**.
2. Ở thanh menu bên trái, chọn mục **"OS and storage"** (chỗ đang hiển thị Debian...) ➔ Bấm nút **Change**.
3. Chọn tab **Custom images**:
   - Ở dòng **Source project for images \*** ➔ Bấm nút **Change** bên cạnh ➔ Chọn/nhập **Project ID của Tài khoản 1**.
   - Ô **Image \*** bên dưới sẽ xuất hiện bản `server-image-v1` ➔ Bấm **Select**.
4. Bấm **Create** để khởi động máy.

---

### BƯỚC 2.5: Cấu hình SSH Key (Để Server A kết nối tự động sang Server B)

Để chạy được lệnh đồng bộ Database (`mysqldump`) và file (`rsync`) mà không bị hỏi mật khẩu hay bị ngắt kết nối, bạn cần thiết lập SSH Key từ A sang B:

#### 1. Trên Server A: Tạo SSH Key (nếu chưa có)
```bash
# Kiểm tra nếu chưa có file id_rsa thì chạy lệnh tạo key:
ssh-keygen -t rsa -b 4096 -f ~/.ssh/id_rsa -N ""

# In khóa công khai (Public Key) ra màn hình:
cat ~/.ssh/id_rsa.pub
```
*(Copy toàn bộ dòng hiển thị ra, bắt đầu bằng `ssh-rsa AAAA...`)*

#### 2. Thêm khóa vào Server B (Chọn 1 trong 2 cách):

* **Cách 1 (Nhanh nhất - Thêm trực tiếp trên giao diện GCP):**
  1. Vào GCP **Tài khoản 2** ➔ **VM instances** ➔ Bấm vào **Server B** ➔ Bấm **EDIT**.
  2. Cuộn xuống mục **SSH Keys** ➔ Bấm **ADD ITEM**.
  3. Dán toàn bộ nội dung public key vừa copy ở trên vào ➔ Bấm **Save**.

* **Cách 2 (Thêm trực tiếp trong terminal Server B):**
  Mở terminal Server B (bấm nút SSH trên GCP Console) và chạy:
  ```bash
  mkdir -p ~/.ssh && chmod 700 ~/.ssh
  echo "DÁN_TOÀN_BỘ_PUBLIC_KEY_CỦA_SERVER_A_VÀO_ĐÂY" >> ~/.ssh/authorized_keys
  chmod 600 ~/.ssh/authorized_keys
  ```

#### 3. Kiểm tra kết nối từ Server A:
Đứng trên Server A, gõ lệnh thử kết nối:
```bash
ssh -o StrictHostKeyChecking=no root@IP_EXTERNAL_SERVER_B "echo 'Kết nối SSH thành công!'"
```
*(Nếu màn hình in ra `Kết nối SSH thành công!` là bạn đã sẵn sàng cho Bước 3).*

---

### BƯỚC 3: Đồng bộ dữ liệu mới nhất (Cutover - Nếu Image tạo trước đó)

*Nếu bạn tạo Image ngay lúc chuyển giao thì Server B đã có sẵn 100% dữ liệu mới nhất, bỏ qua bước này.*  
*Nếu Image được tạo cách đó vài ngày/vài tuần, chạy 2 lệnh sau từ Server A sang Server B (downtime ~ 2-5 phút):*

1. **Khóa ghi nhẹ:** Tạm tắt Nginx trên Server A:
   ```bash
   systemctl stop nginx
   ```

2. **Bắn Database mới nhất từ A sang B trực tiếp qua SSH:**
   ```bash
   mysqldump -u root -p'PASS_A' --single-transaction --quick --routines --triggers --all-databases \
     | gzip -1 \
     | ssh root@IP_SERVER_B "gunzip | mysql -u root -p'PASS_B'"
   ```

3. **Sync các file upload mới phát sinh trong `/var/www`:**
   *(Không dùng cờ `--delete` để tuyệt đối không xóa dữ liệu ở cả 2 bên)*
   ```bash
   rsync -avzP --exclude '.env' /var/www/ root@IP_SERVER_B:/var/www/
   ```

4. **Khởi động dịch vụ trên Server B:**
   ```bash
   ssh root@IP_SERVER_B "systemctl restart php8.2-fpm && pm2 restart all && systemctl restart nginx"
   ```

---

### BƯỚC 4: Chuyển Traffic của 10 Domain

Vì 2 tài khoản GCP không chuyển trực tiếp External IP tĩnh được, bạn áp dụng 2 kỹ thuật sau:

1. **Cập nhật DNS:**
   - Trỏ IP của các domain sang **External IP của Server B**.
   - *(Khuyên dùng Cloudflare bật đám mây cam ☁️ để chuyển hướng toàn cầu chỉ trong 30 giây).*
   - **Mẹo nhàn:** Cấu hình 9 domain phụ dạng **CNAME** trỏ về 1 domain chính. Mỗi lần đổi tài khoản chỉ cần sửa **1 bản ghi A duy nhất** của domain chính.

2. **Dùng Server A làm Proxy chuyển tiếp tạm thời (Zero Downtime & Chống trễ DNS):**
   Trong khi chờ DNS toàn cầu cập nhật, cấu hình Nginx trên Server A đẩy mọi request sang Server B:
   ```nginx
   # Cấu hình trên Server A:
   location / {
       proxy_pass http://IP_SERVER_B;
       proxy_set_header Host $host;
       proxy_set_header X-Real-IP $remote_addr;
       proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
   }
   ```
   Sau đó gõ `nginx -s reload`. Khách vào IP nào cũng được phục vụ từ Server B, không bao giờ lo mất dữ liệu.

---

### BƯỚC 5: Lưu Image độc lập trên Tài khoản 2 (Bắt buộc)

Tài khoản 1 sẽ hết hạn hoặc bị hủy sau vài tuần. Để độc lập 100%:
1. Sau khi Server B đã chạy ổn định trên Tài khoản 2.
2. Vào **Compute Engine** ➔ **Storage** ➔ **Images** trên **Tài khoản 2**.
3. Bấm **CREATE IMAGE** từ chính ổ đĩa của Server B.
4. Từ nay bản Image này hoàn toàn thuộc sở hữu của Tài khoản 2, độc lập vĩnh viễn với Tài khoản 1.

---

## III. BẢNG CHECKLIST TRA CỨU NHANH

| Bước | Nơi thực hiện | Hành động chính |
| :--- | :--- | :--- |
| 1 | TK 1 | Tạo Image (`server-image-v1`) từ Disk của Server A. |
| 2 | TK 1 | Vào IAM gán quyền `Compute Image User` cho email TK 2. |
| 3 | TK 2 | Tạo VM Server B từ Image (qua Cloud Shell hoặc Custom Images). |
| 4 | TK 1 ➔ TK 2 | Cấu hình SSH Key để Server A kết nối tự động sang Server B. |
| 5 | TK 1 ➔ TK 2 | Pipe Database nén siêu tốc qua SSH (`mysqldump \| gzip \| ssh ...`). |
| 6 | TK 1 ➔ TK 2 | `rsync -avzP` thư mục uploads phát sinh (không `--delete`). |
| 7 | DNS | Đổi IP sang Server B. |
| 8 | TK 2 | Tạo Image dự phòng lưu riêng tại TK 2. |
