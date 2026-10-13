<?php

namespace App\Http\Controllers\Hrm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrmAttendance;
use App\Models\HrmEmployeeProfile;
use App\Models\HrmLeaveRequest;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Lấy danh sách điểm danh hàng ngày
     * Nếu ngày đó chưa có bản ghi cho nhân viên, tự tạo bản ghi mặc định để tiện tích chọn
     */
    public function getDailyAttendanceSheet(Request $request)
    {
        $date = $request->date ?: date('Y-m-d');
        $currentUser = Auth::user();

        $query = HrmEmployeeProfile::with(['user', 'department'])
            ->where('work_status', 'working');

        // Phân quyền nếu là Leader chỉ thấy nhân sự team mình
        if ($currentUser) {
            $isHrOrAdmin = $currentUser->isHrmAdmin();
            if (!$isHrOrAdmin && $currentUser->isLeader()) {
                $subordinateIds = explode(',', $currentUser->getStaffHasUser());
                $query->whereIn('user_id', $subordinateIds);
            }
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $employees = $query->get();

        // Lấy các bản ghi điểm danh hiện có trong ngày
        $attendances = HrmAttendance::where('date', $date)
            ->whereIn('user_id', $employees->pluck('user_id'))
            ->get()
            ->keyBy('user_id');

        $result = [];
        foreach ($employees as $emp) {
            $att = $attendances->get($emp->user_id);
            $result[] = [
                'employee_profile_id' => $emp->id,
                'user_id' => $emp->user_id,
                'employee_code' => $emp->employee_code,
                'name' => $emp->user ? $emp->user->name : 'N/A',
                'team_name' => $emp->department ? $emp->department->name : 'N/A',
                'attendance_id' => $att ? $att->id : null,
                'date' => $date,
                // present, leave_approved, unexcused_absent, late_fined, late_submitted
                'attendance_status' => $att ? $att->attendance_status : 'present', 
                'check_in' => $att ? $att->check_in : null,
                'check_out' => $att ? $att->check_out : null,
                'note' => $att ? $att->note : '',
            ];
        }

        return response()->json([
            'date' => $date,
            'data' => $result
        ]);
    }

    /**
     * Cập nhật nhanh trạng thái điểm danh cho 1 hoặc nhiều nhân sự
     */
    public function updateAttendanceStatus(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'records' => 'required|array',
        ]);

        $date = $request->date;
        foreach ($request->records as $item) {
            if (empty($item['user_id'])) continue;

            HrmAttendance::updateOrCreate(
                [
                    'user_id' => $item['user_id'],
                    'date' => $date
                ],
                [
                    'attendance_status' => $item['attendance_status'] ?? 'present',
                    'note' => $item['note'] ?? null,
                ]
            );
        }

        return response()->json([
            'status' => 1,
            'message' => 'Lưu kết quả điểm danh thành công'
        ]);
    }

    /**
     * Tổng hợp công tháng:
     * - Công chuẩn: Giả định 26 hoặc tính theo số ngày trong tháng trừ Chủ nhật
     * - Đã làm: số ngày có status = 'present'
     * - Đi muộn: số ngày có status = 'late_fined'
     * - Nghỉ phép: số ngày có status = 'leave_approved'
     * - Nghỉ không phép: số ngày có status = 'unexcused_absent'
     * - Gửi đơn muộn: số ngày có status = 'late_submitted'
     */
    public function getMonthlySummary(Request $request)
    {
        $month = $request->month ?: date('m');
        $year = $request->year ?: date('Y');

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->format('Y-m-d');

        // Tính số ngày làm việc chuẩn (loại trừ Chủ nhật)
        $standardWorkDays = 0;
        $current = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        while ($current->lte($end)) {
            if ($current->dayOfWeek !== Carbon::SUNDAY) {
                $standardWorkDays++;
            }
            $current->addDay();
        }

        $query = HrmEmployeeProfile::with(['user', 'department'])
            ->where('work_status', 'working');

        // Phân quyền Leader
        $currentUser = Auth::user();
        if ($currentUser) {
            $isHrOrAdmin = $currentUser->isHrmAdmin();
            if (!$isHrOrAdmin && $currentUser->isLeader()) {
                $subordinateIds = explode(',', $currentUser->getStaffHasUser());
                $query->whereIn('user_id', $subordinateIds);
            }
        }

        $employees = $query->get();

        $attendances = HrmAttendance::whereBetween('date', [$startDate, $endDate])
            ->whereIn('user_id', $employees->pluck('user_id'))
            ->get();

        $summary = [];
        foreach ($employees as $emp) {
            $userAtts = $attendances->where('user_id', $emp->user_id);

            $workedDays = $userAtts->where('attendance_status', 'present')->count();
            $lateFined = $userAtts->where('attendance_status', 'late_fined')->count();
            $leaveApproved = $userAtts->where('attendance_status', 'leave_approved')->count();
            $unexcusedAbsent = $userAtts->where('attendance_status', 'unexcused_absent')->count();
            $lateSubmitted = $userAtts->where('attendance_status', 'late_submitted')->count();

            $summary[] = [
                'user_id' => $emp->user_id,
                'employee_code' => $emp->employee_code,
                'name' => $emp->user ? $emp->user->name : 'N/A',
                'team_name' => $emp->department ? $emp->department->name : 'N/A',
                'standard_days' => $standardWorkDays,
                'worked_days' => $workedDays,
                'late_fined_count' => $lateFined,
                'leave_approved_days' => $leaveApproved,
                'unexcused_absent_days' => $unexcusedAbsent,
                'late_submitted_count' => $lateSubmitted,
            ];
        }

        return response()->json([
            'month' => $month,
            'year' => $year,
            'standard_days' => $standardWorkDays,
            'data' => $summary
        ]);
    }

    /**
     * Tra cứu nhanh theo Tên, SĐT, hoặc Mã nhân sự:
     * Nguyễn Văn A
     * Công chuẩn: 20
     * Đã làm: 16
     * Đi muộn: 2 lần
     * Nghỉ phép: 1 ngày
     * Nghỉ không phép: 1
     * Gửi đơn muộn: 0
     */
    public function quickSearchSummary(Request $request)
    {
        $keyword = trim($request->keyword);
        if (!$keyword) {
            return response()->json(['status' => 0, 'message' => 'Vui lòng nhập từ khóa tìm kiếm']);
        }

        $month = $request->month ?: date('m');
        $year = $request->year ?: date('Y');

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->format('Y-m-d');

        // Tìm nhân sự
        $profile = HrmEmployeeProfile::with(['user', 'department'])
            ->where('employee_code', $keyword)
            ->orWhere('phone', $keyword)
            ->orWhereHas('user', function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%");
            })
            ->first();

        if (!$profile) {
            return response()->json(['status' => 0, 'message' => 'Không tìm thấy nhân sự phù hợp']);
        }

        // Tính công chuẩn tháng
        $standardWorkDays = 0;
        $current = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        while ($current->lte($end)) {
            if ($current->dayOfWeek !== Carbon::SUNDAY) {
                $standardWorkDays++;
            }
            $current->addDay();
        }

        $userAtts = HrmAttendance::where('user_id', $profile->user_id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $workedDays = $userAtts->where('attendance_status', 'present')->count();
        $lateFined = $userAtts->where('attendance_status', 'late_fined')->count();
        $leaveApproved = $userAtts->where('attendance_status', 'leave_approved')->count();
        $unexcusedAbsent = $userAtts->where('attendance_status', 'unexcused_absent')->count();
        $lateSubmitted = $userAtts->where('attendance_status', 'late_submitted')->count();

        return response()->json([
            'status' => 1,
            'data' => [
                'name' => $profile->user ? $profile->user->name : 'N/A',
                'employee_code' => $profile->employee_code,
                'phone' => $profile->phone,
                'team_name' => $profile->department ? $profile->department->name : 'N/A',
                'month' => (int)$month,
                'year' => (int)$year,
                'standard_days' => $standardWorkDays,
                'worked_days' => $workedDays,
                'late_fined_count' => $lateFined,
                'leave_approved_days' => $leaveApproved,
                'unexcused_absent_days' => $unexcusedAbsent,
                'late_submitted_count' => $lateSubmitted,
            ]
        ]);
    }

    public function checkIn(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required',
            'date' => 'required|date',
            'check_in' => 'required',
        ]);

        $data = $request->all();
        $data['check_in_ip'] = $request->ip();
        
        $attendance = HrmAttendance::create($data);
        return response()->json(['status' => 1, 'message' => 'Check-in thành công', 'data' => $attendance]);
    }

    public function checkOut(Request $request, $id)
    {
        $attendance = HrmAttendance::findOrFail($id);
        
        $data = $request->only(['check_out', 'check_out_location', 'device_id', 'note']);
        $data['check_out_ip'] = $request->ip();

        $attendance->update($data);
        return response()->json(['status' => 1, 'message' => 'Check-out thành công', 'data' => $attendance]);
    }
}
