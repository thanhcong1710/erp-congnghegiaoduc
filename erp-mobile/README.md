# ERP Education Mobile App

Ứng dụng di động cho hệ thống quản lý giáo dục ERP, hỗ trợ cả iOS và Android.

## 🚀 Tính năng

### Dành cho Giáo viên
- ✅ Xem lịch dạy theo ngày/tuần/tháng
- ✅ Điểm danh học sinh
- ✅ Nhập điểm đánh giá
- ✅ Xem thông tin học sinh
- ✅ Quản lý lớp học
- ✅ Thông báo và nhắc nhở

### Dành cho Học sinh/Phụ huynh
- ✅ Xem lịch học
- ✅ Xem điểm số và đánh giá
- ✅ Theo dõi học phí
- ✅ Nhận thông báo từ trường
- ✅ Xem thông tin hợp đồng
- ✅ Lịch sử thanh toán

## 📱 Công nghệ sử dụng

- **React Native** + **Expo** - Framework phát triển cross-platform
- **TypeScript** - Type safety
- **Redux Toolkit** - State management
- **React Navigation** - Điều hướng
- **Axios** - HTTP client
- **AsyncStorage** - Local storage
- **React Native Paper** - UI components
- **React Native Calendars** - Calendar component

## 🛠️ Cài đặt

### Yêu cầu
- Node.js >= 18.x
- npm hoặc yarn
- Expo CLI
- iOS Simulator (cho Mac) hoặc Android Emulator

### Các bước cài đặt

1. **Di chuyển vào thư mục dự án:**
```bash
cd erp-mobile
```

2. **Cài đặt dependencies:**
```bash
npm install
```

3. **Cấu hình API:**
Mở file `src/constants/config.ts` và cập nhật URL API:
```typescript
export const API_CONFIG = {
  BASE_URL: 'http://your-api-url.com/api', // Thay đổi URL này
  TIMEOUT: 30000,
};
```

4. **Chạy ứng dụng:**

**Trên iOS Simulator:**
```bash
npm run ios
```

**Trên Android Emulator:**
```bash
npm run android
```

**Trên Web (để test):**
```bash
npm run web
```

**Sử dụng Expo Go (trên thiết bị thật):**
```bash
npm start
```
Sau đó quét QR code bằng app Expo Go trên điện thoại.

## 📂 Cấu trúc dự án

```
erp-mobile/
├── src/
│   ├── api/              # API services
│   │   ├── client.ts     # Axios client với interceptors
│   │   ├── auth.ts       # Authentication APIs
│   │   ├── teacher.ts    # Teacher APIs
│   │   └── student.ts    # Student APIs
│   ├── components/       # Reusable components
│   │   ├── common/       # Common components
│   │   ├── teacher/      # Teacher-specific components
│   │   └── student/      # Student-specific components
│   ├── constants/        # Constants và config
│   │   ├── colors.ts     # Color palette
│   │   └── config.ts     # App configuration
│   ├── navigation/       # Navigation setup
│   │   └── AppNavigator.tsx
│   ├── screens/          # Screen components
│   │   ├── auth/         # Authentication screens
│   │   ├── teacher/      # Teacher screens
│   │   └── student/      # Student screens
│   ├── store/            # Redux store
│   │   ├── slices/       # Redux slices
│   │   ├── hooks.ts      # Typed hooks
│   │   └── index.ts      # Store configuration
│   ├── types/            # TypeScript types
│   │   └── index.ts
│   └── utils/            # Utility functions
├── App.tsx               # Root component
├── package.json
└── README.md
```

## 🔐 Authentication

Ứng dụng sử dụng JWT authentication. Token được lưu trong AsyncStorage và tự động gửi kèm mọi request thông qua Axios interceptors.

### Luồng đăng nhập:
1. User nhập username và password
2. App gọi API `/auth/login`
3. Nhận token và thông tin user
4. Lưu vào AsyncStorage
5. Chuyển hướng đến màn hình tương ứng (Teacher/Student)

### User Types:
- **Teacher**: role_id = 4
- **Student/Parent**: role_id = 68, 69

## 🎨 Theming

Ứng dụng có 2 theme riêng biệt:
- **Teacher Theme**: Màu xanh dương (#3F51B5)
- **Student Theme**: Màu xanh lá (#009688)

Tất cả màu sắc được định nghĩa trong `src/constants/colors.ts`

## 📡 API Integration

### Base URL
Cấu hình trong `src/constants/config.ts`:
```typescript
BASE_URL: 'http://localhost:8000/api'
```

### API Endpoints được sử dụng:

#### Authentication
- `POST /auth/login` - Đăng nhập
- `POST /auth/logout` - Đăng xuất
- `GET /menu` - Lấy menu

#### Teacher
- `GET /lms/teachers/show/{id}` - Thông tin giáo viên
- `POST /settings/classes/load-classes` - Danh sách lớp
- `POST /settings/classes/sessions` - Lịch dạy
- `POST /lms/attendances/save` - Lưu điểm danh
- `POST /lms/assessments/add` - Thêm đánh giá

#### Student
- `GET /lms/students/show/{id}` - Thông tin học sinh
- `POST /lms/students/contracts` - Hợp đồng
- `POST /lms/students/sessions` - Lịch học
- `POST /lms/students/assessments` - Điểm số
- `POST /lms/students/payments` - Thanh toán

## 🚀 Build Production

### Build cho iOS:
```bash
expo build:ios
```

### Build cho Android:
```bash
expo build:android
```

### Hoặc sử dụng EAS Build (khuyến nghị):
```bash
npm install -g eas-cli
eas build --platform ios
eas build --platform android
```

## 🐛 Debug

### Xem logs:
```bash
npx react-native log-ios    # iOS logs
npx react-native log-android # Android logs
```

### Debug với React Native Debugger:
1. Cài đặt React Native Debugger
2. Chạy app
3. Shake device và chọn "Debug"

## 📝 Lưu ý

1. **API URL**: Nhớ cập nhật BASE_URL trong config trước khi build production
2. **Icons**: Sử dụng Ionicons từ @expo/vector-icons
3. **AsyncStorage**: Dữ liệu được lưu local, cần xử lý khi logout
4. **Error Handling**: Tất cả API calls đều có try-catch
5. **Loading States**: Mỗi screen đều có loading và refresh states

## 🔄 Phát triển tiếp

### Các tính năng cần bổ sung:
- [ ] Push notifications
- [ ] Offline mode
- [ ] Chat/Messaging
- [ ] File uploads
- [ ] Camera integration cho điểm danh
- [ ] Biometric authentication
- [ ] Multi-language support
- [ ] Dark mode

## 📞 Hỗ trợ

Nếu gặp vấn đề, vui lòng tạo issue trên GitHub hoặc liên hệ team phát triển.

## 📄 License

Copyright © 2026 ERP Education System
