<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiKnowledgeBase extends Model
{

    protected $table = 'ai_knowledge_base';

    protected $fillable = [
        'category',
        'title',
        'content',
        'tags',
        'is_active',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Tìm kiếm Full-Text trong knowledge base
     * @param string $query
     * @param int $limit
     */
    public static function search($query, $limit = 5)
    {
        return self::whereRaw(
            "MATCH(title, content) AGAINST(? IN NATURAL LANGUAGE MODE)",
            [$query]
        )
            ->where('is_active', true)
            ->limit($limit)
            ->get();
    }

    /**
     * Tìm theo category
     */
    public static function findByCategory($category)
    {
        return self::where('category', $category)
            ->where('is_active', true)
            ->get();
    }
}
