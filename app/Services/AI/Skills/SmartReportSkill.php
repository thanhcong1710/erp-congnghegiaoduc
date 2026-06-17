<?php

namespace App\Services\AI\Skills;

class SmartReportSkill extends BaseSkill
{
    public function getName(): string
    {
        return 'smart_report';
    }

    public function getKeywords(): array
    {
        return [
            // Tổng quan
            'tổng quan', 'overview', 'dashboard', 'tổng hợp',
            // Báo cáo tổng
            'báo cáo tháng', 'báo cáo tuần', 'báo cáo ngày',
            'tổng kết tháng', 'tổng kết tuần', 'kết quả tháng',
            // KPI
            'kpi', 'chỉ tiêu', 'mục tiêu', 'đạt được',
            'hiệu suất', 'performance',
            // Xuất
            'xuất báo cáo', 'tải báo cáo', 'export báo cáo',
            'file excel', 'file báo cáo',
        ];
    }

    public function getAllowedTools(): array
    {
        // SmartReport được dùng mọi tool
        return [];
    }

    public function getPriority(): int
    {
        return 25; // Ưu tiên cao nhất vì là tổng hợp
    }

    public function getInstruction(): string
    {
        return <<<INSTRUCTION
## KỸ NĂNG: BÁO CÁO TỔNG HỢP THÔNG MINH

Bạn là **Trợ lý Báo cáo & Phân tích Chiến lược** của hệ thống ERP. Khi user yêu cầu báo cáo tổng quan:

### Quy tắc xử lý:
1. **Thu thập đa chiều**: Gọi NHIỀU tools liên tiếp để có bức tranh toàn diện:
   - Doanh thu (`get_revenue_report`)
   - Học sinh mới, đang học, hết phí (`get_student_statistics`)
   - Lớp học thiếu/thừa (`get_classes_list`)
2. **Phân tích 3 góc độ bắt buộc**:
   - 📈 Tài chính: Doanh thu, so sánh kỳ.
   - 👥 Học viên: Số mới, đang học, cần chăm sóc.
   - 🏫 Vận hành: Tình trạng lớp, sĩ số.
3. **Đưa ra Top 3 ưu tiên hành động** dựa trên phân tích.
4. **Tự động đề xuất xuất Excel** khi báo cáo phức tạp.
5. **Ngôn ngữ**: Ngắn gọn, súc tích kiểu executive summary.

### Format báo cáo tổng hợp:
```
📊 BÁO CÁO TỔNG QUAN [Kỳ] — [Chi nhánh/Toàn hệ thống]
══════════════════════════════════════════

💰 TÀI CHÍNH
  Doanh thu: X VNĐ | So kỳ trước: +/-X%

👥 HỌC VIÊN  
  Tổng: X | Mới: X | Đang học: X | Cần chăm sóc: X

🏫 VẬN HÀNH
  Lớp thiếu HS: X | Lớp thừa HS: X | HS chờ xếp lớp: X

🎯 TOP 3 ƯU TIÊN HÀNH ĐỘNG:
  1. [Hành động cụ thể + lý do]
  2. [Hành động cụ thể + lý do]
  3. [Hành động cụ thể + lý do]
```
INSTRUCTION;
    }
}
