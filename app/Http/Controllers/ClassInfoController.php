<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;

class ClassInfoController extends Controller
{
    /**
     * Lấy thông tin chi tiết của lớp học để hiển thị khi chọn lớp
     */
    public function getClassInfo(Request $request)
    {
        $class_id = (int) $request->class_id;
        
        if (!$class_id) {
            return response()->json(['status' => 0, 'message' => 'Thiếu class_id']);
        }

        $class_info = u::first("SELECT c.id, c.cls_name, c.cls_startdate, c.cls_enddate, c.max_students,
                (SELECT COUNT(s.id) FROM contracts s WHERE s.class_id = c.id AND s.status = 6) AS enrolled_students,
                (SELECT CONCAT(u.name, ' - ', u.hrm_id) FROM users u WHERE u.id = c.teacher_id) AS teacher_name,
                (SELECT CONCAT(u.name, ' - ', u.hrm_id) FROM users u WHERE u.id = c.cm_id) AS cm_name,
                (SELECT CONCAT(u.name, ' - ', u.hrm_id) FROM users u WHERE u.id = c.ta_id) AS ta_name,
                c.class_day,
                c.product_id,
                (SELECT p.name FROM products p WHERE p.id = c.product_id) AS product_name,
                (SELECT r.name FROM rooms r 
                 LEFT JOIN sessions s ON s.room_id = r.id 
                 WHERE s.class_id = c.id AND s.status = 1 LIMIT 1) AS room_name,
                (SELECT sh.name FROM shifts sh 
                 LEFT JOIN sessions s ON s.shift_id = sh.id 
                 WHERE s.class_id = c.id AND s.status = 1 LIMIT 1) AS shift_name,
                CASE 
                    WHEN c.cls_startdate > CURRENT_DATE THEN 'Sắp khai giảng'
                    WHEN c.cls_enddate < CURRENT_DATE THEN 'Đã kết thúc'
                    ELSE 'Đang diễn ra'
                END AS status_text,
                CASE 
                    WHEN (SELECT COUNT(s.id) FROM contracts s WHERE s.class_id = c.id AND s.status = 6) >= c.max_students THEN 'Đã đầy'
                    WHEN (SELECT COUNT(s.id) FROM contracts s WHERE s.class_id = c.id AND s.status = 6) >= c.max_students * 0.8 THEN 'Sắp đầy'
                    ELSE 'Còn chỗ'
                END AS availability_text,
                c.max_students - (SELECT COUNT(s.id) FROM contracts s WHERE s.class_id = c.id AND s.status = 6) AS available_slots
            FROM classes c
            WHERE c.id = $class_id AND c.status = 1");

        if (!$class_info) {
            return response()->json(['status' => 0, 'message' => 'Không tìm thấy thông tin lớp học']);
        }

        // Lấy lịch học trong tuần
        $class_days = explode(',', $class_info->class_day);
        $day_names = [
            2 => 'Thứ 2', 3 => 'Thứ 3', 4 => 'Thứ 4', 5 => 'Thứ 5', 
            6 => 'Thứ 6', 7 => 'Thứ 7', 8 => 'Chủ nhật'
        ];
        
        $schedule_text = [];
        foreach ($class_days as $day) {
            if (isset($day_names[$day])) {
                $schedule_text[] = $day_names[$day];
            }
        }
        $class_info->schedule_text = implode(', ', $schedule_text);

        return response()->json(['status' => 1, 'data' => $class_info]);
    }
}
