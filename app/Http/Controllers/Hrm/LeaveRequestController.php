<?php

namespace App\Http\Controllers\Hrm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrmLeaveRequest;
use App\Models\HrmLeaveBalance;
use App\Models\HrmLeaveType;
use App\Models\HrmAttendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LeaveRequestController extends Controller
{
    /**
     * Danh sách đơn nghỉ phép
     */
    public function list(Request $request)
    {
        $limit = $request->limit ? (int)$request->limit : 20;
        $currentUser = Auth::user();

        $query = HrmLeaveRequest::with(['user', 'leaveType', 'managerApprover', 'hrApprover']);

        // Phân quyền:
        // Leader hoặc Nhân viên bình thường chỉ thấy đơn của chính mình (hoặc nhân sự team mình nếu là Leader)
        if ($currentUser && method_exists($currentUser, 'hasRole')) {
            $isHrOrAdmin = $currentUser->hasRole('admin') || $currentUser->hasRole('hr') || $currentUser->hasRole('accountant') || $currentUser->id == 1;
            if (!$isHrOrAdmin) {
                if ($currentUser->hasRole('leader')) {
                    $subordinateIds = explode(',', $currentUser->getStaffHasUser());
                    $query->whereIn('user_id', $subordinateIds);
                } else {
                    $query->where('user_id', $currentUser->id);
                }
            }
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('keyword')) {
            $kw = trim($request->keyword);
            $query->whereHas('user', function ($q) use ($kw) {
                $q->where('name', 'like', "%{$kw}%")
                  ->orWhere('email', 'like', "%{$kw}%");
            });
        }

        $leaves = $query->orderBy('id', 'desc')->paginate($limit);
        return response()->json($leaves);
    }

    /**
     * Tạo đơn xin nghỉ / đổi ca / off sang onl
     */
    public function add(Request $request)
    {
        try {
            $currentUser = Auth::user();
            $userId = $request->user_id ?: ($currentUser ? $currentUser->id : 1);

            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            $durationDays = $request->duration_days ?: (max(1, $startDate->diffInDays($endDate) + 1));

            $leave = new HrmLeaveRequest();
            $leave->user_id = $userId;
            $leave->leave_type_id = $request->leave_type_id ?: null;
            $leave->type = $request->request_category ?: 'leave';
            $leave->request_category = $request->request_category ?: 'leave'; // leave, shift_change, off_to_onl
            $leave->start_date = $request->start_date;
            $leave->end_date = $request->end_date;
            $leave->duration_days = $durationDays;
            $leave->target_shift_info = $request->target_shift_info ?: null;
            $leave->reason = $request->reason;
            $leave->status = 0; // Chờ duyệt
            $leave->save();

            return response()->json([
                'status' => 1,
                'message' => 'Tạo đơn xin nghỉ / đổi ca thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Lỗi: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * HR Phê duyệt đơn xin phép:
     * - approved: Đã duyệt (hưởng nguyên lương / trừ phép năm)
     * - half_salary: 50% lương (off sang onl)
     * - late_unpaid: Gửi đơn muộn (không lương)
     * - rejected: Từ chối
     */
    public function approve(Request $request, $id)
    {
        try {
            $leave = HrmLeaveRequest::findOrFail($id);
            $approvalType = $request->approval_type ?: 'approved'; // approved, half_salary, late_unpaid, rejected

            if ($approvalType === 'rejected') {
                $leave->status = 2; // Từ chối
                $leave->approval_type = 'rejected';
                $leave->rejection_reason = $request->rejection_reason;
            } else {
                $leave->status = 1; // Đã duyệt
                $leave->approval_type = $approvalType;
                $leave->rejection_reason = null;

                // Tự động trừ số ngày phép vào hrm_leave_balances nếu là đơn nghỉ phép đã duyệt
                if ($leave->request_category === 'leave' && in_array($approvalType, ['approved'])) {
                    $year = Carbon::parse($leave->start_date)->year;
                    $balance = HrmLeaveBalance::firstOrCreate(
                        ['user_id' => $leave->user_id, 'year' => $year],
                        ['leave_type_id' => $leave->leave_type_id ?: 1, 'total_days' => 12, 'used_days' => 0]
                    );
                    $balance->used_days = min($balance->total_days, $balance->used_days + $leave->duration_days);
                    $balance->save();
                }

                // Tự động cập nhật bảng điểm danh trong khoảng ngày nghỉ
                $currentDay = Carbon::parse($leave->start_date);
                $endDay = Carbon::parse($leave->end_date);
                while ($currentDay->lte($endDay)) {
                    $dateStr = $currentDay->format('Y-m-d');
                    $attStatus = 'leave_approved';
                    if ($approvalType === 'late_unpaid') {
                        $attStatus = 'late_submitted';
                    }

                    HrmAttendance::updateOrCreate(
                        ['user_id' => $leave->user_id, 'date' => $dateStr],
                        ['attendance_status' => $attStatus, 'note' => 'Theo đơn xin phép #' . $leave->id]
                    );
                    $currentDay->addDay();
                }
            }

            $leave->hr_approved_by = Auth::id();
            $leave->save();

            return response()->json([
                'status' => 1,
                'message' => 'Phê duyệt đơn thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Lỗi: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Lấy thông tin số dư phép năm:
     * Phép năm được hưởng: 12 ngày
     * Đã sử dụng: X ngày
     * Còn lại: Y ngày
     */
    public function getLeaveBalance(Request $request)
    {
        $userId = $request->user_id ?: Auth::id();
        $year = $request->year ?: date('Y');

        $balance = HrmLeaveBalance::firstOrCreate(
            ['user_id' => $userId, 'year' => $year],
            ['leave_type_id' => 1, 'total_days' => 12, 'used_days' => 0]
        );

        $remainingDays = max(0, $balance->total_days - $balance->used_days);

        return response()->json([
            'status' => 1,
            'data' => [
                'user_id' => $userId,
                'year' => $year,
                'total_days' => (float)$balance->total_days,
                'used_days' => (float)$balance->used_days,
                'remaining_days' => (float)$remainingDays,
            ]
        ]);
    }
}
