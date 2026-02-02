# Hướng dẫn Build và Test trên Môi trường thật (Production)

Để chạy ứng dụng trên môi trường thật (không phải Localhost, không qua Tunnel), bạn cần thực hiện các bước sau:

## 1. Cấu hình API Production
Mở file `src/constants/config.ts` và cập nhật `BASE_URL` trỏ về server thật của bạn (có SSL/HTTPS càng tốt).

```typescript
export const API_CONFIG = {
  // Ví dụ: https://erp.congnghegiaoduc.com/api
  BASE_URL: 'https://DIA_CHI_SERVER_THAT_CUA_BAN/api', 
  TIMEOUT: 30000,
};
```

## 2. Cấu hình App (app.json) (BẮT BUỘC)
Bạn cần thêm `bundleIdentifier` (iOS) và `package` (Android) vào file `app.json`. Đây là định danh duy nhất cho ứng dụng của bạn trên Store.

```json
{
  "expo": {
    "ios": {
      "supportsTablet": true,
      "bundleIdentifier": "com.congnghegiaoduc.erp" 
    },
    "android": {
      "package": "com.congnghegiaoduc.erp"
    }
  }
}
```

## 3. Cài đặt và Cấu hình EAS (Expo Application Services)
EAS là dịch vụ của Expo giúp bạn build file cài đặt `.apk` (Android) hoặc `.ipa` (iOS).

**Bước 3.1: Đăng nhập Expo**
Chạy lệnh sau và đăng nhập bằng tài khoản Expo của bạn:
```bash
eas login
```

**Bước 3.2: Tạo cấu hình Build**
Chạy lệnh sau để tạo file `eas.json`:
```bash
eas build:configure
```
*(Chọn All hoặc Android/iOS tùy nhu cầu)*

## 4. Tạo bản Build

**Cho Android (APK để cài thử):**
Để lấy file APK cài trực tiếp cho máy Android (không cần đẩy lên Store):
1. Mở `eas.json`, sửa `build.preview.android.buildType` thành `"apk"`.
2. Chạy lệnh:
```bash
eas build -p android --profile preview
```

**Cho iOS (TestFlight):**
Với iOS, bạn cần tài khoản Apple Developer (99$/năm).
```bash
eas build -p ios --profile production
```

## 5. Cập nhật không cần Build lại (OTA Update)
Khi bạn chỉ sửa code JS/Giao diện mà không cài thêm thư viện Native mới, bạn có thể đẩy cập nhật ngay lập tức tới máy người dùng:

```bash
eas update --branch production --message "Fix login bug"
```

---
**Lưu ý:**
Nếu bạn chỉ muốn test trên điện thoại thật nhưng vẫn dùng code local (Dev Mode), hãy sử dụng phương pháp **Tunnel** như đã làm ở bước trước, nhưng đảm bảo mạng ổn định.
