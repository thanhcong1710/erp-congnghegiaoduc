<?php

namespace App\Console\Commands;

use App\Models\LogClassStudent;
use App\Models\LogStudents;
use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;

class FixIncorrectlyDroppedStudents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lms:fix-dropped-students {student_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix students incorrectly dropped by AutoWithdrawEnrollmentDeposit (restore class_id, status, etc)';

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
        $studentId = $this->argument('student_id');

        // Query tìm các học sinh bị drop lúc 23:55 (bị xóa nhầm do debt_amount = 0)
        $query = "SELECT l.*, c.status as contract_current_status, c.class_id as contract_current_class_id
            FROM
                log_class_students AS l
                LEFT JOIN contracts AS c ON c.id = l.contract_id
                LEFT JOIN agreements AS a ON a.id = c.agreement_id
            WHERE DATE_FORMAT(l.created_at, '%H:%i')>='23:55' AND c.class_id IS NULL AND a.debt_amount<600000  AND l.action=0";
            
        if ($studentId) {
            $query .= " AND l.student_id = " . (int)$studentId;
        }

        $logs = u::query($query);
        $total = count($logs);
        
        if ($total == 0) {
            $this->info("Không tìm thấy bản ghi nào cần fix.");
            return 0;
        }

        $this->info("Tìm thấy $total bản ghi cần fix. Đang xử lý...");
        
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($logs as $log) {
            $contract_id = $log->contract_id;
            
            $class_id = $log->class_id;
            if (!$class_id) {
                $bar->advance();
                continue;
            }

            $clsInfo = u::first("SELECT program_id, cls_startdate, cls_enddate FROM classes WHERE id = $class_id");
            if (!$clsInfo) {
                $bar->advance();
                continue;
            }

            // Restore dữ liệu từ bảng classes
            $program_id = $clsInfo->program_id;
            $start_date = $clsInfo->cls_startdate;
            $last_date = $clsInfo->cls_enddate;
            $status = 6; 
            
            // Cập nhật lại contracts
            u::updateSimpleRow([
                'class_id' => $class_id,
                'program_id' => $program_id,
                'enrolment_start_date' => $start_date,
                'enrolment_last_date' => $last_date,
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ], ['id' => $contract_id], 'contracts');

            // Ghi log khôi phục
            // u::addLogContracts($contract_id);
            // LogClassStudent::logAction($class_id, $log->student_id, $contract_id, 1, 0); // Action 1 = add back
            // LogStudents::logAdd($log->student_id, 'Khôi phục học sinh vào lớp (rollback AutoWithdrawEnrollmentDeposit)', 0);
            // u::updateScheduleHasStudent($contract_id, $start_date);
            $bar->advance();
        }
        
        $bar->finish();
        $this->info("\nHoàn tất quá trình fix dữ liệu.");
        return 0;
    }
}
