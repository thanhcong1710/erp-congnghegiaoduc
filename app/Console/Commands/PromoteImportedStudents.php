<?php

namespace App\Console\Commands;

use App\Http\Controllers\ImportsController;
use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;

class PromoteImportedStudents extends Command
{
    protected $signature = 'crm:promoteImportedStudents {--import_id=0 : Chỉ xử lý 1 đợt import} {--run : Thực thi (mặc định chỉ xem trước)}';

    protected $description = 'Đưa học sinh CRM tạo từ import (chưa có lms_id) lên học sinh chính thức LMS';

    public function handle()
    {
        $import_id = (int)$this->option('import_id');
        $cond = $import_id > 0 ? "p.import_id = $import_id" : "p.import_id > 0";
        $rows = u::query("SELECT p.import_id, COUNT(cs.id) AS total FROM crm_students AS cs
                INNER JOIN crm_parents AS p ON p.id = cs.parent_id
            WHERE $cond AND IFNULL(cs.lms_id, 0) = 0
            GROUP BY p.import_id");
        $total = 0;
        foreach ($rows as $r) {
            $this->line("Import #{$r->import_id}: {$r->total} học sinh chưa lên chính thức");
            $total += $r->total;
        }
        $this->info("Tổng: $total");

        if (!$this->option('run')) {
            $this->comment('Chế độ xem trước. Thêm --run để thực thi.');
            return 0;
        }
        $done = ImportsController::promoteImportedStudents($import_id);
        $this->info("Đã tạo học sinh chính thức: $done");
        return 0;
    }
}
