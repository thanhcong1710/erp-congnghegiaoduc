<?php

namespace App\Services\AI\Skills;

class StudentAdvisorSkill extends BaseSkill
{
    public function getName(): string
    {
        return 'student_advisor';
    }

    public function getKeywords(): array
    {
        return [
            // Tra cứu học sinh
            'học sinh', 'học viên', 'em học', 'con em',
            // Tình trạng
            'hết phí', 'hết buổi', 'còn bao nhiêu', 'bảo lưu', 'chờ xếp lớp',
            'đang học', 'ngừng học', 'nghỉ học',
            // Hành động
            'tra cứu', 'tìm học sinh', 'tìm học viên', 'kiểm tra học sinh',
            'thông tin học viên', 'thông tin học sinh',
            // Tư vấn
            'gia hạn', 'nạp thêm', 'đóng học phí', 'học phí',
            'hợp đồng', 'số buổi',
        ];
    }

    public function getAllowedTools(): array
    {
        return [
            'get_student_info',
            'search_students',
            'count_students_by_status',
            'get_student_statistics',
            'get_tuition_fees_list',
            'get_tuition_fee_detail',
            'get_classes_list',
            'search_knowledge',
            'create_excel_report',
        ];
    }

    public function getPriority(): int
    {
        return 20;
    }

    public function getInstruction(): string
    {
        return <<<INSTRUCTION
## KỸ NĂNG: TƯ VẤN HỌC VIÊN

Bạn đang đóng vai trò **Chuyên viên Tư vấn Học vụ** của hệ thống ERP. Khi user hỏi về học sinh/học viên:

### Quy tắc xử lý:
1. **Nếu thiếu tên/mã học sinh** → Hỏi lại user trước khi gọi tool. Ví dụ: "Bạn có thể cho mình biết tên hoặc mã học sinh cần tra không?"
2. **Sau khi lấy được dữ liệu**, hãy phân tích toàn diện:
   - Hợp đồng: còn hiệu lực không? status là gì?
   - Số buổi còn lại: nếu < 5 → **cảnh báo ngay**.
   - Học phí: đã đóng chưa? còn nợ bao nhiêu?
3. **Luôn kết thúc bằng 1 khuyến nghị hành động cụ thể**, ví dụ:
   - "Học sinh này còn 3 buổi → Nên liên hệ phụ huynh để ký gia hạn sớm."
   - "Học sinh đang chờ xếp lớp → Có thể xem lớp [X] đang thiếu học sinh."
4. **Ngôn ngữ**: Lịch sự, chuyên nghiệp, thân thiện. KHÔNG trả lời khô khan kiểu "done" hay chỉ liệt kê dữ liệu thô.
5. **Format**: Dùng bullet points và emoji để dễ đọc. Highlight thông tin quan trọng bằng **bold**.

### Ví dụ phân tích tốt:
> 📋 **Thông tin học viên Nguyễn Văn A:**
> - 🏫 Lớp: Math-10A | Chi nhánh: Quận 1
> - 📅 Buổi còn lại: **3 buổi** ⚠️ Sắp hết!
> - 💰 Học phí: Đã đóng đủ
> 
> 👉 **Khuyến nghị:** Liên hệ phụ huynh để tư vấn gia hạn. Gói phí phù hợp: [gói X].
INSTRUCTION;
    }
}
