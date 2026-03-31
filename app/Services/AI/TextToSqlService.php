<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TextToSqlService
{
    protected $geminiService;
    protected $databaseSchema;
    protected $databaseName;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
        $this->databaseName = env('DB_DATABASE', 'erp');

        // Cache schema 1 giờ để tránh query nhiều lần
        $this->databaseSchema = Cache::remember('database_schema', 3600, function () {
            return $this->autoDiscoverSchema();
        });
    }

    /**
     * TỰ ĐỘNG KHÁM PHÁ DATABASE SCHEMA
     * Không cần định nghĩa thủ công!
     */
    protected function autoDiscoverSchema()
    {
        try {
            $schema = "# DATABASE SCHEMA (Auto-discovered)\n\n";

            // Lấy danh sách tables
            $tables = $this->getTables();

            foreach ($tables as $tableName) {
                $schema .= $this->getTableSchema($tableName);
            }

            return $schema;

        } catch (\Exception $e) {
            Log::error('Auto-discover schema error: ' . $e->getMessage());
            return $this->getFallbackSchema();
        }
    }

    /**
     * Lấy danh sách tables
     */
    protected function getTables()
    {
        $tables = DB::select("
            SELECT TABLE_NAME 
            FROM INFORMATION_SCHEMA.TABLES 
            WHERE TABLE_SCHEMA = ? 
            AND TABLE_TYPE = 'BASE TABLE'
            ORDER BY TABLE_NAME
        ", [$this->databaseName]);

        return array_map(function ($t) {
            return $t->TABLE_NAME;
        }, $tables);
    }

    /**
     * Lấy schema của 1 table
     */
    protected function getTableSchema($tableName)
    {
        $schema = "## Table: {$tableName}\n";

        // Lấy columns
        $columns = DB::select("
            SELECT 
                COLUMN_NAME,
                DATA_TYPE,
                COLUMN_TYPE,
                IS_NULLABLE,
                COLUMN_KEY,
                COLUMN_COMMENT
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = ?
            AND TABLE_NAME = ?
            ORDER BY ORDINAL_POSITION
        ", [$this->databaseName, $tableName]);

        foreach ($columns as $column) {
            $schema .= $this->formatColumn($column);
        }

        // Lấy foreign keys
        $foreignKeys = $this->getForeignKeys($tableName);
        if (!empty($foreignKeys)) {
            $schema .= "\n### Foreign Keys:\n";
            foreach ($foreignKeys as $fk) {
                $schema .= "- {$fk->COLUMN_NAME} → {$fk->REFERENCED_TABLE_NAME}.{$fk->REFERENCED_COLUMN_NAME}\n";
            }
        }

        $schema .= "\n";
        return $schema;
    }

    /**
     * Format column info
     */
    protected function formatColumn($column)
    {
        $line = "- {$column->COLUMN_NAME} ({$column->DATA_TYPE}";

        // Primary key
        if ($column->COLUMN_KEY === 'PRI') {
            $line .= ", PRIMARY KEY";
        }

        // Nullable
        if ($column->IS_NULLABLE === 'NO') {
            $line .= ", NOT NULL";
        }

        $line .= ")";

        // Comment (nếu có)
        if (!empty($column->COLUMN_COMMENT)) {
            $line .= " - {$column->COLUMN_COMMENT}";
        }

        $line .= "\n";
        return $line;
    }

    /**
     * Lấy foreign keys
     */
    protected function getForeignKeys($tableName)
    {
        return DB::select("
            SELECT 
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = ?
            AND TABLE_NAME = ?
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ", [$this->databaseName, $tableName]);
    }

    /**
     * Fallback schema nếu auto-discover fail
     */
    protected function getFallbackSchema()
    {
        return <<<SCHEMA
# DATABASE SCHEMA (Fallback - Basic Tables)

## Table: students
- id (INT, PRIMARY KEY)
- name (VARCHAR)
- email (VARCHAR)
- phone (VARCHAR)
- status (INT)

## Table: classes
- id (INT, PRIMARY KEY)
- cls_name (VARCHAR)
- status (INT)

## Table: contracts
- id (INT, PRIMARY KEY)
- student_id (INT)
- class_id (INT)
- status (INT)

SCHEMA;
    }

    /**
     * Chuyển câu hỏi tự nhiên thành SQL
     */
    public function questionToSql($question)
    {
        $prompt = <<<PROMPT
Bạn là SQL expert. Nhiệm vụ: Chuyển câu hỏi tự nhiên thành SQL query.

DATABASE SCHEMA:
{$this->databaseSchema}

RULES:
1. Chỉ trả về SQL query, KHÔNG giải thích
2. Sử dụng SELECT, không dùng INSERT/UPDATE/DELETE
3. Limit kết quả tối đa 100 rows
4. Format đẹp, dễ đọc
5. Sử dụng JOIN khi cần
6. Trả về JSON format: {"sql": "...", "explanation": "..."}

QUESTION: {$question}

Hãy generate SQL query:
PROMPT;

        try {
            $response = $this->geminiService->generateContent($prompt);

            // Parse JSON response
            $jsonMatch = [];
            if (preg_match('/\{[\s\S]*"sql"[\s\S]*\}/', $response, $jsonMatch)) {
                $data = json_decode($jsonMatch[0], true);
                return [
                    'sql' => $data['sql'] ?? null,
                    'explanation' => $data['explanation'] ?? '',
                ];
            }

            // Fallback: extract SQL từ code block
            if (preg_match('/```sql\n(.*?)\n```/s', $response, $matches)) {
                return [
                    'sql' => trim($matches[1]),
                    'explanation' => 'SQL query generated from natural language',
                ];
            }

            return [
                'sql' => null,
                'explanation' => 'Could not parse SQL from AI response',
            ];

        } catch (\Exception $e) {
            Log::error('Text-to-SQL Error: ' . $e->getMessage());
            return [
                'sql' => null,
                'explanation' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Execute SQL query an toàn
     */
    public function executeSafely($sql)
    {
        // Validate SQL (chỉ cho phép SELECT)
        if (!$this->isSafeQuery($sql)) {
            return [
                'success' => false,
                'message' => '❌ Chỉ cho phép SELECT query',
            ];
        }

        try {
            $results = DB::select($sql);

            return [
                'success' => true,
                'data' => $results,
                'count' => count($results),
            ];

        } catch (\Exception $e) {
            Log::error('SQL Execution Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => '❌ SQL Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Kiểm tra SQL có an toàn không
     */
    protected function isSafeQuery($sql)
    {
        $sql = strtoupper(trim($sql));

        // Chỉ cho phép SELECT
        if (!str_starts_with($sql, 'SELECT')) {
            return false;
        }

        // Không cho phép các từ khóa nguy hiểm
        $dangerousKeywords = [
            'DROP',
            'DELETE',
            'UPDATE',
            'INSERT',
            'ALTER',
            'CREATE',
            'TRUNCATE',
            'EXEC',
            'EXECUTE',
            'GRANT',
            'REVOKE'
        ];

        foreach ($dangerousKeywords as $keyword) {
            if (str_contains($sql, $keyword)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Main method: Hỏi và nhận kết quả
     */
    public function ask($question)
    {
        // Step 1: Generate SQL
        $sqlData = $this->questionToSql($question);

        if (!$sqlData['sql']) {
            return [
                'success' => false,
                'message' => '❌ Không thể tạo SQL query từ câu hỏi',
            ];
        }

        // Step 2: Execute SQL
        $result = $this->executeSafely($sqlData['sql']);

        // Step 3: Format response
        if ($result['success']) {
            return [
                'success' => true,
                'sql' => $sqlData['sql'],
                'explanation' => $sqlData['explanation'],
                'data' => $result['data'],
                'count' => $result['count'],
            ];
        }

        return $result;
    }
}
