<?php

use Illuminate\Database\Seeder;
use App\Models\AiUserPermission;
use App\Models\AiKnowledgeBase;
use App\User;


class AiChatbotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tạo permission cho admin (user_id = 1)
        $adminUser = User::find(1);
        if ($adminUser) {
            AiUserPermission::updateOrCreate(
                ['user_id' => 1],
                [
                    'is_enabled' => true,
                    'daily_token_limit' => 50000, // Admin có hạn mức cao hơn
                    'monthly_token_limit' => 1500000,
                    'can_query_data' => true,
                    'can_generate_reports' => true,
                    'can_access_knowledge' => true,
                    'allowed_functions' => null, // null = cho phép tất cả
                ]
            );
        }

        // 2. Tạo một số tài liệu mẫu trong Knowledge Base
        $knowledgeItems = [
            [
                'category' => 'user_guide',
                'title' => 'Cách tạo hợp đồng mới trong hệ thống',
                'content' => 'Để tạo hợp đồng mới, bạn cần: 
1. Vào menu Hợp đồng > Tạo mới
2. Nhập thông tin học viên (Họ tên, SĐT, Email)
3. Chọn khóa học và gói học phí
4. Nhập thông tin thanh toán
5. Bấm Lưu để hoàn tất

Lưu ý: Hợp đồng chỉ có hiệu lực sau khi được phê duyệt bởi quản lý.',
                'tags' => ['contract', 'guide', 'enrollment'],
            ],
            [
                'category' => 'faq',
                'title' => 'Làm sao để tra cứu điểm của học viên?',
                'content' => 'Có 2 cách tra cứu điểm:
1. Vào menu Học viên > Tìm kiếm học viên > Chọn tab "Điểm số"
2. Hoặc hỏi AI Chatbot: "Cho tôi xem điểm của học viên [Tên]"

AI sẽ tự động lấy dữ liệu từ hệ thống và hiển thị kết quả.',
                'tags' => ['student', 'score', 'faq'],
            ],
            [
                'category' => 'policy',
                'title' => 'Chính sách hoàn tiền học phí',
                'content' => 'Chính sách hoàn tiền:
- Hoàn 100% nếu hủy trước khi khai giảng 7 ngày
- Hoàn 70% nếu hủy trong vòng 7 ngày đầu học
- Hoàn 50% nếu hủy sau 7 ngày nhưng trước 1 tháng
- Không hoàn tiền sau 1 tháng học

Điều kiện: Học viên phải có lý do chính đáng (ốm đau, chuyển công tác...) và có giấy tờ chứng minh.',
                'tags' => ['policy', 'refund', 'tuition'],
            ],
            [
                'category' => 'faq',
                'title' => 'Cách tạo báo cáo doanh thu bằng AI',
                'content' => 'Bạn có thể yêu cầu AI tạo báo cáo bằng cách hỏi:
- "Tạo báo cáo doanh thu tháng 1 năm 2026"
- "Xuất Excel danh sách học viên đăng ký mới tuần này"
- "Thống kê số hợp đồng theo chi nhánh"

AI sẽ tự động truy vấn dữ liệu, tạo file Excel và gửi link download cho bạn.',
                'tags' => ['report', 'ai', 'excel'],
            ],
        ];

        foreach ($knowledgeItems as $item) {
            AiKnowledgeBase::create($item);
        }

        $this->command->info('✅ Đã tạo AI permissions và knowledge base mẫu!');
    }
}
