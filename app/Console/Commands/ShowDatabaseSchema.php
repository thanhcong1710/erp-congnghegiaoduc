<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AI\TextToSqlService;

class ShowDatabaseSchema extends Command
{
    protected $signature = 'ai:show-schema';
    protected $description = 'Show auto-discovered database schema';

    public function handle()
    {
        $this->info("🔍 Auto-discovering database schema...\n");

        $textToSql = app(TextToSqlService::class);

        // Access protected property via reflection
        $reflection = new \ReflectionClass($textToSql);
        $property = $reflection->getProperty('databaseSchema');
        $property->setAccessible(true);
        $schema = $property->getValue($textToSql);

        $this->line($schema);

        $this->info("\n✅ Schema auto-discovered successfully!");
        $this->info("💡 This schema is cached for 1 hour");
        $this->info("🔄 To refresh: php artisan cache:clear");
    }
}
