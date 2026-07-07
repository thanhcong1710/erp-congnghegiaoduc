<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Support\Facades\DB;

class InsertMissingTermStudentUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:missing_term_student_user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert missing term_student_user records for students using their latest agreement data';

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
        $this->info("Bắt đầu lấy danh sách học sinh chưa có trong term_student_user...");

        // Tìm các student_id chưa có trong term_student_user nhưng có trong agreements
        $missingStudents = u::query("
            SELECT DISTINCT a.student_id 
            FROM agreements a
            WHERE a.student_id NOT IN (SELECT student_id FROM term_student_user)
        ");

        $total = count($missingStudents);
        $this->info("Tìm thấy $total học sinh cần bổ sung dữ liệu.");

        $count = 0;
        foreach ($missingStudents as $student) {
            $student_id = $student->student_id;
            
            // Lấy thông tin từ agreement mới nhất (id lớn nhất)
            $lastAgreement = u::first("
                SELECT branch_id, ec_id, ec_leader_id 
                FROM agreements 
                WHERE student_id = {$student_id} 
                ORDER BY id DESC 
                LIMIT 1
            ");

            if ($lastAgreement) {
                // Insert vào term_student_user
                DB::table('term_student_user')->insert([
                    'student_id' => $student_id,
                    'branch_id' => $lastAgreement->branch_id ?: 0,
                    'ec_id' => $lastAgreement->ec_id ?: 0,
                    'ec_leader_id' => $lastAgreement->ec_leader_id ?: 0,
                    'cm_id' => 0,
                    'cm_leader_id' => 0,
                    'ceo_branch_id' => 0,
                    'teacher_id' => 0,
                    'status' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'creator_id' => 0,
                    'updator_id' => 0
                ]);
                $count++;
            }
        }

        $this->info("Hoàn tất! Đã insert thành công {$count} bản ghi vào bảng term_student_user.");
    }
}
