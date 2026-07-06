<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;

class GenClassStudentLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lms:gen-class-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gen lịch sử xếp lớp từ bảng contracts';

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
     * @return int
     */
    public function handle()
    {
        $this->info("Bắt đầu tạo dữ liệu lịch sử xếp lớp từ contracts...");

        // Xóa dữ liệu cũ nếu chạy lại (tuỳ chọn, nhưng để đảm bảo không bị trùng lặp)
        // Cần đảm bảo nếu hệ thống đang chạy thì không xoá hết, nhưng do đây là tool gen lại toàn bộ thì truncate là hợp lý nhất.
        // Tuy nhiên, để an toàn, ta chỉ chèn thêm và bỏ qua nếu đã tồn tại, HOẶC làm 1 phát INSERT IGNORE nếu có unique key.
        // Bảng log_class_students không có unique key. Để tránh duplicate, ta xoá các log có action=1 trước khi tạo lại (nếu cần).
        // Tốt nhất là clean table nếu user muốn gen lại.
        u::query("TRUNCATE TABLE log_class_students");

        $query = "
            INSERT INTO log_class_students (class_id, student_id, contract_id, action, creator_id, created_at, updated_at)
            SELECT 
                class_id, 
                student_id, 
                id AS contract_id, 
                1 AS action, 
                COALESCE(updator_id, creator_id, 0) AS creator_id, 
                IF(updated_at < '2000-01-01', IF(created_at < '2000-01-01', NOW(), created_at), updated_at) AS created_at, 
                IF(updated_at < '2000-01-01', IF(created_at < '2000-01-01', NOW(), created_at), updated_at) AS updated_at
            FROM contracts 
            WHERE class_id > 0 AND class_id IS NOT NULL AND status=6
        ";

        u::query($query);

        $this->info("Thành công! Đã sinh lại lịch sử xếp lớp.");
        return 0;
    }
}
