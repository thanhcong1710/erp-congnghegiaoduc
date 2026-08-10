<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;

class UpdateStudentsGender extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'students:update-gender
                            {--dry-run : Chỉ hiển thị kết quả, không cập nhật dữ liệu}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cập nhật giới tính học sinh dựa trên tên: chứa "thị" → Nữ (F), chứa "văn" → Nam (M), còn lại → Nữ (F). Đồng thời cập nhật avatar_url.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('🔍 Chế độ dry-run: chỉ hiển thị, không cập nhật dữ liệu.');
        }

        $students = u::query("SELECT id, name, gender, avatar_url FROM students WHERE status > 0");

        $totalStudents = count($students);
        $updatedCount = 0;
        $skippedCount = 0;

        $this->info("📋 Tổng số học sinh: {$totalStudents}");
        $this->newLine();

        $bar = $this->output->createProgressBar($totalStudents);
        $bar->start();

        foreach ($students as $student) {
            // Tách tên thành các từ riêng biệt và so sánh chính xác từng từ
            $words = preg_split('/\s+/', mb_strtolower(trim($student->name), 'UTF-8'));

            $hasThi = in_array('thị', $words);
            $hasVan = in_array('văn', $words);

            // Chỉ xử lý học sinh có tên chứa đúng từ "thị" hoặc "văn"
            if (!$hasThi && !$hasVan) {
                $skippedCount++;
                $bar->advance();
                continue;
            }

            // "thị" → Nữ, "văn" → Nam (ưu tiên "thị" nếu có cả hai)
            $newGender = $hasThi ? 'F' : 'M';

            // Xác định avatar_url theo giới tính mới
            $newAvatar = $newGender == 'F'
                ? '/images/common/avatar-girl.svg'
                : '/images/common/avatar-boy.svg';

            // Kiểm tra có cần cập nhật không
            if ($student->gender === $newGender && $student->avatar_url === $newAvatar) {
                $skippedCount++;
                $bar->advance();
                continue;
            }

            if ($dryRun) {
                $genderLabel = $newGender == 'F' ? 'Nữ' : 'Nam';
                $oldGenderLabel = $student->gender == 'F' ? 'Nữ' : ($student->gender == 'M' ? 'Nam' : $student->gender);
                $this->newLine();
                $this->line("  ID: {$student->id} | Tên: {$student->name} | {$oldGenderLabel} → {$genderLabel}");
            } else {
                u::updateSimpleRow([
                    'gender' => $newGender,
                    'avatar_url' => $newAvatar,
                    'updated_at' => date('Y-m-d H:i:s'),
                ], ['id' => $student->id], 'students');
            }

            $updatedCount++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Hoàn thành!");
        $this->info("   - Đã cập nhật: {$updatedCount}");
        $this->info("   - Bỏ qua: {$skippedCount}");

        if ($dryRun) {
            $this->newLine();
            $this->warn('⚠️  Đây là chế độ dry-run. Chạy lại không có --dry-run để cập nhật thực tế.');
        }

        return 0;
    }
}
