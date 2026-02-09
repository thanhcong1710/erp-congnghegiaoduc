<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AI\TextToSqlService;

class TestTextToSql extends Command
{
    protected $signature = 'ai:test-sql {question}';
    protected $description = 'Test Text-to-SQL functionality';

    public function handle()
    {
        $question = $this->argument('question');
        
        $this->info("🤖 Question: {$question}");
        $this->info("⏳ Generating SQL...\n");
        
        $textToSql = app(TextToSqlService::class);
        $result = $textToSql->ask($question);
        
        if ($result['success']) {
            $this->info("✅ Success!\n");
            
            $this->line("📝 SQL Query:");
            $this->line("```sql");
            $this->line($result['sql']);
            $this->line("```\n");
            
            $this->line("💡 Explanation:");
            $this->line($result['explanation'] . "\n");
            
            $this->line("📊 Results ({$result['count']} rows):");
            $this->table(
                array_keys((array)$result['data'][0] ?? []),
                array_map(fn($row) => (array)$row, $result['data'])
            );
        } else {
            $this->error("❌ Failed: " . $result['message']);
        }
    }
}
