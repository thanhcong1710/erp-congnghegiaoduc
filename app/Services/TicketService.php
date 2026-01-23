<?php

namespace App\Services;

use App\Providers\UtilityServiceProvider as u;
use Illuminate\Support\Facades\Log;

class TicketService
{
    /**
     * Tạo ticket tự động khi học sinh được thêm vào lớp
     */
    public static function createTicketsForStudentEnrollment($studentId, $classId, $contractId, $actions = ['Phát sách', 'Thông báo lịch học'])
    {
        try {
            // Lấy thông tin học sinh và lớp học
            $student = u::first("SELECT s.name, s.lms_code FROM students AS s WHERE s.id = $studentId");
            $class = u::first("SELECT c.cls_name, c.cls_startdate, c.branch_id FROM classes AS c WHERE c.id = $classId");
            
            if (!$student || !$class) {
                Log::error("Không tìm thấy thông tin học sinh hoặc lớp học", [
                    'student_id' => $studentId,
                    'class_id' => $classId
                ]);
                return false;
            }

            $createdTickets = 0;
            
            // Tạo ticket cho mỗi hành động
            foreach ($actions as $action) {
                $description = "Học sinh {$student->name} (Mã: {$student->lms_code}) được thêm vào lớp {$class->cls_name}. Cần: {$action}.";
                
                u::insertSimpleRow([
                    'type' => 1, // Yêu cầu dịch vụ
                    'description' => $description,
                    'parent_id' => $studentId,
                    'creator_id' => auth()->id(),
                    'status' => 1, // Mới
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ], 'crm_tickets');
                
                $createdTickets++;
            }

            Log::info("Đã tạo {$createdTickets} ticket dịch vụ cho học sinh", [
                'student_id' => $studentId,
                'class_id' => $classId,
                'contract_id' => $contractId,
                'actions' => $actions
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Lỗi khi tạo ticket dịch vụ: " . $e->getMessage(), [
                'student_id' => $studentId,
                'class_id' => $classId,
                'contract_id' => $contractId
            ]);
            return false;
        }
    }
}
