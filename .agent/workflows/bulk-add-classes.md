---
description: How to bulk add classes in ERP project
---

# Quy trình Import lớp học hàng loạt

Tài liệu này hướng dẫn cách sử dụng lệnh `php artisan bulk:add-classes` để nhập nhiều lớp học cùng lúc, tự động xử lý ca học, giáo viên và lịch học.

## Bước 1: Chuẩn bị dữ liệu JSON

Tạo một file JSON (ví dụ: `classes_data.json`) với cấu trúc như sau:

```json
[
  {
    "code": "OPT123",
    "product_name": "Pre-Toeic",
    "branch_id": 9,
    "days": [2, 5],
    "shift": "18H15-19H45",
    "start_time": "18:15",
    "end_time": "19:45",
    "start_date": "2026-04-02",
    "teacher": "GV Nguyễn Ngoan"
  }
]
```

## Bước 2: Chạy lệnh Import

Thực hiện lệnh sau trong terminal:

// turbo
```bash
php artisan bulk:add-classes --file=path/to/classes_data.json
```

## Các tính năng thông minh của lệnh:

1.  **Dộng theo khóa học**: Bạn có thể thay đổi `product_name` (ví dụ: "IELTS 6.5", "TOEIC Starter"). Hệ thống sẽ tự tìm `product_id` tương ứng và lấy số buổi học từ cấu hình của khóa đó.
2.  **Khử trùng lặp**: Tự động bỏ qua các lớp đã tồn tại dựa trên Mã lớp (`code`).
3.  **Tự động tạo Ca học**: Nếu tên ca học chưa có, hệ thống sẽ tự tạo mới.
4.  **Tự động tạo Giáo viên**: Nếu tên giáo viên chưa có, hệ thống tạo user mới với vai trò Giáo viên và mật khẩu mặc định `12345678@`.
5.  **Tự động sinh lịch học**: Nếu có `start_date`, hệ thống tự tính toán và sinh toàn bộ danh sách các ngày học chi tiết.
6.  **Tự động gán Subject/Program**: Tự động liên kết lớp học với môn học và chương trình học phù hợp.
