<?php

namespace App\Services\AI\Skills;

class HRAdvisorSkill extends BaseSkill
{
    public function getName(): string
    {
        return 'hr_advisor';
    }

    public function getKeywords(): array
    {
        return [
            // Nhân viên
            'nhân viên', 'nhân sự', 'staff', 'ec', 'cm',
            'giáo viên', 'teacher', 'thầy', 'cô giáo',
            // Tìm kiếm
            'tìm nhân viên', 'thông tin nhân viên', 'ai phụ trách',
            'quản lý', 'phụ trách',
            // Phân công
            'phân công', 'phân công lại', 'load', 'tải công việc',
            'bao nhiêu học sinh', 'đang quản lý',
            // Vai trò
            'vai trò', 'role', 'quyền hạn', 'bộ phận',
            'chi nhánh nào', 'làm việc ở',
        ];
    }

    public function getAllowedTools(): array
    {
        return [
            'get_users_list',
            'get_user_detail',
            'search_branches',
            'get_student_statistics',
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
## KỸ NĂNG: TƯ VẤN NHÂN SỰ

Bạn là **Chuyên viên Nhân sự & Vận hành** của hệ thống ERP. Khi user hỏi về nhân viên/nhân sự:

### Quy tắc xử lý:
1. **Khi tra cứu nhân viên**: Hiển thị thông tin gọn gàng, bao gồm: vai trò, chi nhánh, số học sinh đang phụ trách.
2. **Phân tích load công việc**:
   - EC phụ trách > 50 học sinh → 🔴 Đang quá tải.
   - EC phụ trách < 10 học sinh → 🟡 Chưa được phân công đầy đủ.
   - Cân bằng: 20-40 học sinh/EC là lý tưởng.
3. **Đề xuất phân công lại** khi phát hiện mất cân bằng.
4. **Bảo mật**: Không hiển thị thông tin nhạy cảm như lương, mật khẩu, token.
5. **Ngôn ngữ**: Khách quan, chuyên nghiệp kiểu báo cáo quản lý.

### Format hiển thị nhân viên:
```
👤 [Tên] | 🆔 [Mã HRM]
   📍 Chi nhánh: [Tên chi nhánh]
   🎭 Vai trò: [Role]
   👥 Phụ trách: X học sinh (EC) | Y học sinh (CM)
   📊 Đánh giá: [🟢 Bình thường / 🟡 Nhẹ / 🔴 Quá tải]
```
INSTRUCTION;
    }
}
