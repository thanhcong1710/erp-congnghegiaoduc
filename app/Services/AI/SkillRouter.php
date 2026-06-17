<?php

namespace App\Services\AI;

use App\Services\AI\Skills\BaseSkill;
use App\Services\AI\Skills\StudentAdvisorSkill;
use App\Services\AI\Skills\RevenueAnalystSkill;
use App\Services\AI\Skills\ClassSchedulerSkill;
use App\Services\AI\Skills\HRAdvisorSkill;
use App\Services\AI\Skills\SmartReportSkill;

class SkillRouter
{
    /**
     * Danh sách tất cả Skills được đăng ký trong hệ thống
     * @var BaseSkill[]
     */
    protected array $skills = [];

    public function __construct()
    {
        $this->registerSkills();
    }

    /**
     * Đăng ký tất cả Skills vào router
     * Muốn thêm Skill mới → thêm vào đây
     */
    protected function registerSkills(): void
    {
        $this->skills = [
            new SmartReportSkill(),    // priority 25 - check trước
            new StudentAdvisorSkill(), // priority 20
            new RevenueAnalystSkill(), // priority 20
            new ClassSchedulerSkill(), // priority 15
            new HRAdvisorSkill(),      // priority 15
        ];

        // Sắp xếp theo priority giảm dần
        usort($this->skills, fn($a, $b) => $b->getPriority() - $a->getPriority());
    }

    /**
     * Phát hiện Skill phù hợp nhất từ nội dung tin nhắn user
     * Trả về null nếu không có Skill nào match (dùng default behavior)
     */
    public function detect(string $message): ?BaseSkill
    {
        foreach ($this->skills as $skill) {
            if ($skill->matches($message)) {
                return $skill;
            }
        }

        return null;
    }

    /**
     * Lấy tất cả Skills đã đăng ký
     * @return BaseSkill[]
     */
    public function getAllSkills(): array
    {
        return $this->skills;
    }
}
