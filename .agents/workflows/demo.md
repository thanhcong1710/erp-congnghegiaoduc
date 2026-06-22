# Demo Workflow (Quy trình làm việc mẫu)

Khác với Rule (áp dụng mọi lúc), **Workflow** (`workflows/demo.md`) là một bản hướng dẫn từng bước (Step-by-step guide) cho một tác vụ cụ thể. Tôi chỉ dùng file này khi bạn yêu cầu, ví dụ: "Hãy chạy workflow demo cho tôi". 

Dưới đây là một ví dụ về một quy trình tự động.

// turbo-all

*(Chú thích: Thẻ `// turbo-all` ở trên nói cho tôi biết rằng bạn cho phép tôi TỰ ĐỘNG CHẠY mọi lệnh terminal trong file này mà không cần dừng lại xin phép bạn ở từng bước).*

## Bước 1: Khảo sát môi trường
Sử dụng công cụ terminal để kiểm tra xem chúng ta đang đứng ở đâu.
```bash
pwd
```

## Bước 2: Khởi tạo file tạm
Hãy tạo ra một file tên là `hello_workflow.txt` ở thư mục hiện tại với nội dung "Xin chào từ Workflow". Bạn không cần dùng bash, hãy dùng tool viết file của bạn.

## Bước 3: Đọc và báo cáo
Kiểm tra lại nội dung file vừa tạo, hiển thị kết quả cho người dùng để xác nhận hoàn thành, sau đó dùng lệnh terminal để xóa file đó đi:
```bash
rm hello_workflow.txt
```

---
*Khi bạn gõ lệnh: "Chạy file workflow demo", tôi sẽ lấy file này ra và làm chính xác theo thứ tự từ Bước 1 đến Bước 3.*
