<?php

namespace App\Services\AI\Skills;

abstract class BaseSkill
{
    /**
     * Tên định danh của Skill (dùng để log, config)
     */
    abstract public function getName(): string;

    /**
     * Danh sách từ khóa/intent để SkillRouter nhận diện
     * @return string[]
     */
    abstract public function getKeywords(): array;

    /**
     * Nội dung System Instruction chuyên biệt của Skill này
     * Sẽ được APPEND vào system prompt gốc
     */
    abstract public function getInstruction(): string;

    /**
     * Danh sách tool names mà Skill này được phép dùng
     * Trả về [] = dùng tất cả tools
     * @return string[]
     */
    public function getAllowedTools(): array
    {
        return [];
    }

    /**
     * Điểm ưu tiên khi nhiều Skill cùng match (số càng cao càng ưu tiên)
     */
    public function getPriority(): int
    {
        return 10;
    }

    /**
     * Kiểm tra xem Skill có phù hợp với message này không
     */
    public function matches(string $message): bool
    {
        $message = mb_strtolower($message, 'UTF-8');

        foreach ($this->getKeywords() as $keyword) {
            if (str_contains($message, mb_strtolower($keyword, 'UTF-8'))) {
                return true;
            }
        }

        return false;
    }
}
