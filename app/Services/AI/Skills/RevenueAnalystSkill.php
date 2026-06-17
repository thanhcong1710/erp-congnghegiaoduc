<?php

namespace App\Services\AI\Skills;

class RevenueAnalystSkill extends BaseSkill
{
    public function getName(): string
    {
        return 'revenue_analyst';
    }

    public function getKeywords(): array
    {
        return [
            // Doanh thu
            'doanh thu', 'doanh số', 'thu nhập', 'thu tiền',
            // Báo cáo
            'báo cáo', 'report', 'thống kê', 'tổng kết',
            // Thời gian
            'tháng này', 'tháng trước', 'tuần này', 'hôm nay',
            'quý', 'năm nay', 'so sánh',
            // Hành động
            'xuất excel', 'tải xuống', 'export',
            // Tài chính
            'dòng tiền', 'kpi', 'mục tiêu', 'chỉ tiêu',
            'thanh toán', 'thu học phí', 'số hợp đồng',
        ];
    }

    public function getAllowedTools(): array
    {
        return [
            'get_revenue_report',
            'create_excel_report',
            'get_student_statistics',
            'count_students_by_status',
            'search_branches',
            'search_knowledge',
        ];
    }

    public function getPriority(): int
    {
        return 20;
    }

    public function getInstruction(): string
    {
        return <<<INSTRUCTION
## KỸ NĂNG: PHÂN TÍCH DOANH THU

Bạn là **Chuyên viên Phân tích Tài chính** của hệ thống ERP. Khi user hỏi về doanh thu/báo cáo:

### Quy tắc xử lý:
1. **Nếu user không nói rõ kỳ báo cáo** → Hỏi lại: "Bạn muốn xem doanh thu tháng mấy, năm mấy? Và của chi nhánh nào hay toàn hệ thống?"
2. **Sau khi có dữ liệu**, PHẢI phân tích theo các góc độ:
   - So sánh với kỳ trước (nếu có thể lấy thêm dữ liệu).
   - Ngày/tuần có doanh thu cao nhất, thấp nhất.
   - Nhận xét xu hướng: đang tăng hay giảm.
3. **Đưa ra nhận xét có chiều sâu**, KHÔNG chỉ liệt kê số:
   - ✅ Tốt: "Doanh thu tuần 3 tăng 35% so với tuần 2, do có đợt ký hợp đồng..."
   - ❌ Tránh: "Doanh thu: 50,000,000 VNĐ"
4. **Nếu doanh thu thấp bất thường** → Gợi ý kiểm tra các nguyên nhân tiềm ẩn.
5. **Tự động đề xuất xuất Excel** khi báo cáo có nhiều dòng dữ liệu (> 10 ngày).

### Format báo cáo chuẩn:
```
📊 BÁO CÁO DOANH THU [Kỳ]
━━━━━━━━━━━━━━━━━━━━━━━━
💰 Tổng doanh thu: X VNĐ
📈 So với kỳ trước: +/-X% 
🏆 Ngày cao nhất: [ngày] - X VNĐ
📉 Ngày thấp nhất: [ngày] - X VNĐ

💡 Nhận xét: [phân tích ngắn gọn]
👉 Khuyến nghị: [hành động cụ thể]
```
INSTRUCTION;
    }
}
