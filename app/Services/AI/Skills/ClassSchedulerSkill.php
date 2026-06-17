<?php

namespace App\Services\AI\Skills;

class ClassSchedulerSkill extends BaseSkill
{
    public function getName(): string
    {
        return 'class_scheduler';
    }

    public function getKeywords(): array
    {
        return [
            // Lớp học
            'lớp học', 'lớp', 'xếp lớp', 'vào lớp',
            // Tìm kiếm lớp
            'lớp còn chỗ', 'lớp thiếu', 'lớp đầy', 'sĩ số',
            'tìm lớp', 'lớp nào', 'còn slot',
            // Lịch học
            'lịch học', 'thứ', 'ca học', 'giờ học',
            'thứ 2', 'thứ 3', 'thứ 4', 'thứ 5', 'thứ 6', 'thứ 7', 'chủ nhật',
            // Phòng
            'phòng học', 'phòng', 'room',
            // Giáo viên
            'giáo viên', 'teacher', 'thầy', 'cô',
        ];
    }

    public function getAllowedTools(): array
    {
        return [
            'get_classes_list',
            'get_class_detail',
            'search_students',
            'get_student_info',
            'get_tuition_fees_list',
            'search_branches',
            'search_knowledge',
        ];
    }

    public function getPriority(): int
    {
        return 15;
    }

    public function getInstruction(): string
    {
        return <<<INSTRUCTION
## KỸ NĂNG: TƯ VẤN LỊCH HỌC & XẾP LỚP

Bạn là **Chuyên viên Điều phối Lớp học** của hệ thống ERP. Khi user hỏi về lớp học:

### Quy tắc xử lý:
1. **Khi tìm lớp cho học sinh đang chờ**:
   - Hỏi: sản phẩm, thứ mong muốn, ca học.
   - Tìm lớp cùng sản phẩm, còn chỗ (THIẾU học sinh).
   - Ưu tiên gợi ý lớp THIẾU nhiều nhất.
2. **Khi hiển thị danh sách lớp**:
   - Dùng bảng/danh sách có cấu trúc rõ ràng.
   - Highlight trạng thái sĩ số: 🔴 THỪA | 🟡 ĐỦ | 🟢 THIẾU (còn slot).
3. **Cảnh báo tự động**:
   - Lớp > 100% sĩ số → ⚠️ cần điều chỉnh ngay.
   - Nhiều học sinh chờ cùng sản phẩm → gợi ý mở lớp mới.
4. **Format lịch học**: Hiển thị dạng "T2-T4 (17:30-19:00)" cho dễ đọc.

### Format hiển thị lớp chuẩn:
```
🏫 [Tên lớp] | 📅 [Lịch] | 🕐 [Ca]
   👥 Sĩ số: X/Y | 🟢 Còn Z chỗ
   👨‍🏫 GV: [Tên] | 📍 Phòng: [Phòng]
```
INSTRUCTION;
    }
}
