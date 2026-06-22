---
name: demo-skill
description: Một skill mẫu (demo) để hiển thị cấu trúc thư mục tiêu chuẩn mà Antigravity (AI Agent) sử dụng.
version: 1.0.0
---

# Demo Skill Instructions

Đây là file cốt lõi (Trái tim) của một skill. Agent sẽ đọc file này đầu tiên để hiểu mục đích và cách thức hoạt động của skill.

## 1. Mục đích
Skill này dùng để làm mẫu cấu trúc thư mục Hub-and-Spoke cho các skill khác.

## 2. Quy trình (Workflow)
Khi kích hoạt skill này, Agent cần làm các bước sau:
1. Đọc cấu hình từ `config.json` nếu có.
2. Sử dụng các kịch bản trong thư mục `scripts/` nếu cần thực thi logic phức tạp.
3. Tham khảo các tài liệu trong `references/` nếu gặp lỗi hoặc cần tra cứu API.
4. Điền dữ liệu vào các biểu mẫu trong `assets/` để trả về cho người dùng.

## 3. Hướng dẫn chi tiết
- **Không bao giờ** tự ý thay đổi cấu trúc của `config.json` trừ khi được yêu cầu.
- Nếu cần xuất báo cáo, hãy dùng `assets/report_template.md`.
