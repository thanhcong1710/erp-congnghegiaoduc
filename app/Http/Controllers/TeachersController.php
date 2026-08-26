<?php

namespace App\Http\Controllers;

use App\Enums\SystemCode;
use App\User;
use App\Http\Controllers\Controller;
use App\Models\LogStudents;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TeachersController extends Controller
{
    public function list(Request $request)
    {
        $branch_id = isset($request->branch_id) ? $request->branch_id : [];
        $keyword = isset($request->keyword) ? $request->keyword : '';
        $end_date = isset($request->end_date) ? $request->end_date : '';
        $start_date = isset($request->start_date) ? $request->start_date : '';

        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int) $pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int) $pagination->limit : 20;
        $offset = $page == 1 ? 0 : $limit * ($page-1);
        $limitation =  $limit > 0 ? " LIMIT $offset, $limit": "";
        $cond = " s.status = 1 ";
        $cond .= " AND s.branch_id IN (" . Auth::user()->getBranchesHasUser().") ";

        if (!empty($branch_id)) {
            $cond .= " AND s.branch_id IN (".implode(",",$branch_id).") ";
        }
        if ($keyword !== '') {
            $cond .= " AND (cl.cls_name LIKE '%$keyword%' OR ut.name LIKE '%$keyword%' OR uc.name LIKE '%$keyword%' 
                OR ut.hrm_id LIKE '%$keyword%' OR uc.hrm_id LIKE '%$keyword%') ";
        }
        if ($end_date !== '') {
            $cond .= " AND s.class_date <= '$end_date'";
        }
        if ($start_date !== '') {
            $cond .= " AND s.class_date >= '$start_date'";
        }
        
        $user_role = Auth::user()->role_id;
        $user_id = Auth::user()->id;
        if (in_array($user_role, [36, 54])) {
            $cond .= " AND (s.teacher_id = $user_id OR s.ta_id = $user_id OR s.cm_id = $user_id OR cl.teacher_id = $user_id OR cl.ta_id = $user_id OR cl.cm_id = $user_id) ";
        }

        $order_by = " ORDER BY s.class_date DESC ";

        $total = u::first("SELECT count(s.id) AS total 
            FROM schedules AS s 
                LEFT JOIN classes AS cl ON cl.id = s.class_id
                LEFT JOIN users AS ut ON ut.id=s.teacher_id
                LEFT JOIN users AS uc ON uc.id=s.cm_id
            WHERE $cond");
        
        $list = u::query("SELECT s.id, s.class_date, s.subject_stt, cl.cls_name AS class_name,
                CONCAT(ut.name, ' - ', ut.hrm_id) AS teacher_name,
                CONCAT(uc.name, ' - ', uc.hrm_id) AS cm_name,
                (SELECT name FROM branches WHERE id = s.branch_id) AS branch_name,
                (SELECT COUNT(id) FROM schedule_has_student WHERE class_id = s.class_id AND class_date = s.class_date AND status=1) AS total_count,
                (SELECT COUNT(id) FROM schedule_has_student WHERE class_id = s.class_id AND class_date = s.class_date AND attendance_status=1 AND status=1) AS present_count
            FROM schedules AS s 
                LEFT JOIN classes AS cl ON cl.id = s.class_id
                LEFT JOIN users AS ut ON ut.id=s.teacher_id
                LEFT JOIN users AS uc ON uc.id=s.cm_id
            WHERE $cond $order_by $limitation");
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    public function show(Request $request,$id)
    {
        $data = u::first("SELECT s.*, 
                (SELECT name FROM branches WHERE id=s.branch_id) AS branch_name,
                c.cls_name AS class_name,
                s.subject_stt,
                c.max_students,
                (SELECT COUNT(ct.id) FROM contracts ct WHERE ct.class_id = c.id AND ct.status IN (6,1)) AS num_students,
                (SELECT CONCAT(u.name, ' - ', u.hrm_id) FROM users u WHERE u.id = s.teacher_id) AS teacher_name,
                (SELECT CONCAT(u.name, ' - ', u.hrm_id) FROM users u WHERE u.id = s.ta_id) AS ta_name,
                (SELECT CONCAT(u.name, ' - ', u.hrm_id) FROM users u WHERE u.id = c.cm_id) AS cm_name,
                (SELECT r.name FROM rooms r LEFT JOIN sessions se ON se.room_id = r.id WHERE se.class_id = c.id AND se.status = 1 LIMIT 1) AS room_text,
                (SELECT sh.name FROM shifts sh LEFT JOIN sessions se ON se.shift_id = sh.id WHERE se.class_id = c.id AND se.status = 1 LIMIT 1) AS shift_text
            FROM schedules AS s 
            LEFT JOIN classes AS c ON c.id = s.class_id
            WHERE s.id=$id");
            
        // Fetch students for attendance
        $students = DB::table('schedule_has_student as shs')
            ->leftJoin('students as s', 's.id', '=', 'shs.student_id')
            ->where('shs.class_id', $data->class_id)
            ->where('shs.class_date', $data->class_date)
            ->select(
                'shs.id as schedule_student_id',
                's.id as student_id',
                's.name as student_name',
                's.lms_code',
                'shs.attendance_status'
            )
            ->orderBy('s.name', 'asc')
            ->get();
            
        $data->students = $students;

        return response()->json($data);
    }

    public function update(Request $request)
    {
        DB::beginTransaction();
        try {
            u::updateSimpleRow(array(
                'teacher_id' => data_get($request, 'teacher_id'),
                'ta_id' => data_get($request, 'ta_id'),
                'note' => data_get($request, 'note'),
                'link_record' => data_get($request, 'link_record'),
                'updated_at'=>date('Y-m-d H:i:s'),
                'updator_id'=>Auth::user()->id,
            ),array('id'=>data_get($request, 'id')), 'schedules');

            $students = data_get($request, 'students', []);
            foreach ($students as $student) {
                if (isset($student['schedule_student_id'])) {
                    DB::table('schedule_has_student')
                        ->where('id', $student['schedule_student_id'])
                        ->update([
                            'attendance_status' => isset($student['is_present']) && $student['is_present'] ? 1 : 0,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updator_id' => Auth::user()->id
                        ]);
                }
            }
            DB::commit();
            $result = array(
                'status' => 1,
                'message' => 'Cập nhật thông tin buổi dạy thành công'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            $result = array(
                'status' => 0,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            );
        }
        return response()->json($result);
    }
}
