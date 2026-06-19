# Hướng dẫn tích hợp Admin Panel vào Menu

## 📋 Tổng quan

Admin Panel đã được tạo sẵn tại: `resources/js/components/AiAdminPanel.vue`

Để thêm vào menu, bạn cần:
1. Tạo route mới
2. Thêm menu item vào sidebar

---

## 🔧 Bước 1: Tạo Route

Mở file `resources/js/src/router.js` và thêm route mới:

```javascript
{
  path: '/ai-admin',
  name: 'ai-admin',
  component: () => import('@/components/AiAdminPanel.vue'),
  meta: {
    pageTitle: 'Quản lý AI',
    breadcrumb: [
      { title: 'Home', url: '/' },
      { title: 'Quản lý AI', active: true }
    ],
    requiresAuth: true,
    // Chỉ admin mới được truy cập
    rule: 'admin'
  }
}
```

---

## 🎨 Bước 2: Thêm vào Menu

### Cách 1: Thêm vào file menu config

Tìm file config menu (thường là `resources/js/src/layouts/components/vertical-nav-menu/navMenuItems.js` hoặc tương tự) và thêm:

```javascript
{
  url: '/ai-admin',
  name: 'Quản lý AI',
  slug: 'ai-admin',
  icon: 'BotIcon', // hoặc 'CpuIcon'
  i18n: 'AI Management',
  // Chỉ hiển thị cho admin
  acl: 'admin'
}
```

### Cách 2: Thêm trực tiếp vào database (nếu menu lưu trong DB)

Chạy SQL sau:

```sql
INSERT INTO menus (name, url, icon, parent_id, sort_order, is_active, created_at, updated_at)
VALUES ('Quản lý AI', '/ai-admin', 'cpu', NULL, 100, 1, NOW(), NOW());
```

---

## 🔐 Phân quyền

Nếu bạn muốn chỉ admin mới thấy menu này, thêm permission:

```sql
-- Tạo permission
INSERT INTO permissions (name, display_name, description, created_at, updated_at)
VALUES ('ai-admin', 'Quản lý AI', 'Quản lý AI Chatbot và quyền user', NOW(), NOW());

-- Gán cho role admin (giả sử role_id của admin là 1)
INSERT INTO permission_has_role (permission_id, role_id)
SELECT id, 1 FROM permissions WHERE name = 'ai-admin';
```

---

## ✅ Kiểm tra

1. **Đăng nhập** với tài khoản admin
2. **Vào menu** → Bạn sẽ thấy "Quản lý AI"
3. **Click vào** → Trang admin panel sẽ hiển thị

---

## 🎯 Tính năng Admin Panel

### Tab 1: Quản lý User
- ✅ Xem danh sách users
- ✅ Bật/tắt quyền AI cho từng user
- ✅ Cấu hình hạn mức token (ngày/tháng)
- ✅ Cấu hình quyền chi tiết (tra cứu, tạo báo cáo, truy cập tài liệu)

### Tab 2: Thống kê
- ✅ Tổng users đang dùng AI
- ✅ Tổng tokens đã dùng
- ✅ Tổng chi phí (USD)
- ✅ Tổng requests
- ✅ Top 10 users dùng nhiều nhất
- ✅ Biểu đồ sử dụng theo ngày

### Tab 3: Quản lý Tài liệu
- ✅ Xem danh sách knowledge base
- ✅ Thêm/sửa/xóa tài liệu
- ✅ Phân loại theo category

---

## 🚀 Nâng cao (Tùy chọn)

### Thêm biểu đồ (Chart.js)

Nếu muốn hiển thị biểu đồ đẹp hơn trong tab Thống kê:

```bash
npm install chart.js vue-chartjs
```

Sau đó import vào `AiAdminPanel.vue`:

```javascript
import { Line } from 'vue-chartjs'
```

---

## 📞 Hỗ trợ

Nếu gặp vấn đề khi tích hợp, kiểm tra:
1. Route đã được thêm chưa?
2. Component đã được import đúng chưa?
3. User có quyền truy cập không?
4. Console có báo lỗi gì không?
