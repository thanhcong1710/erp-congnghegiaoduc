# Quick Start Guide - ERP Mobile App

## 🚀 Bắt đầu nhanh trong 5 phút

### Bước 1: Kiểm tra yêu cầu hệ thống

```bash
# Kiểm tra Node.js version (cần >= 18.x)
node --version

# Nếu < 18, cài đặt Node mới hơn:
# Mac: brew install node@20
# Windows: Download từ nodejs.org
```

### Bước 2: Cài đặt dependencies

```bash
cd erp-mobile
npm install
```

### Bước 3: Cấu hình API

Mở file `src/constants/config.ts` và sửa URL API:

```typescript
export const API_CONFIG = {
  BASE_URL: 'http://YOUR_API_URL/api',  // ← Sửa đây
  TIMEOUT: 30000,
};
```

**Lưu ý quan trọng về API URL:**

- **Localhost trên máy tính:** `http://localhost:8000/api`
- **iOS Simulator:** `http://localhost:8000/api` hoặc `http://YOUR_IP:8000/api`
- **Android Emulator:** `http://10.0.2.2:8000/api` hoặc `http://YOUR_IP:8000/api`
- **Thiết bị thật:** `http://YOUR_IP:8000/api` (ví dụ: `http://192.168.1.100:8000/api`)

Để tìm IP của máy:
```bash
# Mac/Linux
ifconfig | grep "inet "

# Windows
ipconfig
```

### Bước 4: Chạy ứng dụng

#### Option A: Sử dụng Expo Go (Dễ nhất - Khuyến nghị)

1. **Cài đặt Expo Go trên điện thoại:**
   - iOS: [App Store](https://apps.apple.com/app/expo-go/id982107779)
   - Android: [Play Store](https://play.google.com/store/apps/details?id=host.exp.exponent)

2. **Khởi động development server:**
```bash
npm start
```

3. **Quét QR code:**
   - iOS: Mở Camera app và quét QR code
   - Android: Mở Expo Go app và quét QR code

#### Option B: iOS Simulator (Chỉ Mac)

```bash
npm run ios
```

#### Option C: Android Emulator

```bash
npm run android
```

### Bước 5: Đăng nhập

Sử dụng tài khoản từ hệ thống backend:

**Giáo viên:**
- Username: (tài khoản giáo viên từ hệ thống)
- Password: (mật khẩu)

**Học sinh/Phụ huynh:**
- Username: (tài khoản học sinh/phụ huynh)
- Password: (mật khẩu)

## 📱 Giao diện ứng dụng

### Giáo viên
- **Trang chủ:** Thống kê và lịch dạy hôm nay
- **Lịch dạy:** Xem lịch theo ngày
- **Lớp học:** Quản lý lớp học
- **Cá nhân:** Thông tin và cài đặt

### Học sinh/Phụ huynh
- **Trang chủ:** Thống kê và lịch học sắp tới
- **Lịch học:** Xem lịch học
- **Điểm số:** Xem điểm và đánh giá
- **Học phí:** Theo dõi thanh toán
- **Cá nhân:** Thông tin và cài đặt

## 🔧 Xử lý sự cố nhanh

### Lỗi: "Cannot connect to development server"

**Giải pháp:**
1. Đảm bảo điện thoại và máy tính cùng WiFi
2. Tắt VPN nếu đang bật
3. Thử chế độ tunnel:
```bash
npm start -- --tunnel
```

### Lỗi: "Network request failed"

**Giải pháp:**
1. Kiểm tra API URL trong `src/constants/config.ts`
2. Đảm bảo backend đang chạy
3. Thử ping API:
```bash
curl http://YOUR_API_URL/api/auth/login
```

### Lỗi: "Node version too old"

**Giải pháp:**
```bash
# Cài Node 20
nvm install 20
nvm use 20

# Hoặc
brew install node@20
```

### App bị crash khi mở

**Giải pháp:**
```bash
# Clear cache và restart
npm start -- --clear
```

## 📚 Tài liệu chi tiết

- **README.md** - Hướng dẫn đầy đủ
- **API_GUIDE.md** - Hướng dẫn tích hợp API
- **PROJECT_STATUS.md** - Tình trạng dự án
- **TROUBLESHOOTING.md** - Xử lý sự cố chi tiết

## 🎯 Checklist trước khi bắt đầu

- [ ] Node.js >= 18.x đã cài đặt
- [ ] npm dependencies đã cài (`npm install`)
- [ ] API URL đã cấu hình đúng
- [ ] Backend API đang chạy
- [ ] Expo Go đã cài trên điện thoại (nếu dùng thiết bị thật)
- [ ] Cùng mạng WiFi (nếu dùng thiết bị thật)

## 💡 Tips

### 1. Reload app
- **iOS:** Cmd + R
- **Android:** Double tap R
- **Physical device:** Shake device → Reload

### 2. Debug menu
- **iOS:** Cmd + D
- **Android:** Cmd + M (Mac) / Ctrl + M (Windows)
- **Physical device:** Shake device

### 3. View logs
Logs hiển thị trong terminal nơi bạn chạy `npm start`

### 4. Clear cache
```bash
npm start -- --clear
```

## 🆘 Cần trợ giúp?

1. Xem **TROUBLESHOOTING.md** cho các lỗi thường gặp
2. Kiểm tra logs trong terminal
3. Thử clear cache và restart
4. Liên hệ team phát triển

## 🎉 Chúc mừng!

Bạn đã sẵn sàng phát triển với ERP Mobile App! 

**Next steps:**
- Khám phá các màn hình
- Test các tính năng
- Đọc code để hiểu cấu trúc
- Bắt đầu phát triển tính năng mới

---

**Happy Coding! 🚀**
