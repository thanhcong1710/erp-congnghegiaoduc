<?php

namespace App\Http\Controllers\Hrm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrmAttendance;
use App\Models\HrmEmployeeProfile;
use App\Models\HrmLeaveRequest;
use App\Models\HrmShift;
use App\Models\HrmShiftAssignment;
use App\Models\HrmOnlineRegistration;
use App\Services\Hrm\HrmScopeService;
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

        $query = HrmEmployeeProfile::with(['user', 'department', 'shift'])
            ->where('work_status', 'working');

        // Phân quyền dữ liệu theo phạm vi (Admin xem ALL, Trưởng phòng/Lead xem cấp dưới & phòng ban, Nhân viên xem chính mình)
        $query = HrmScopeService::applyProfileScope($query, $currentUser);

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $employees = $query->orderBy('full_name', 'asc')->get();

        // Lấy các bản ghi điểm danh hiện có trong ngày (theo employee_id hoặc user_id)
        $empIds = $employees->pluck('id')->filter()->all();
        $userIds = $employees->pluck('user_id')->filter()->all();

        $attendances = HrmAttendance::with('shift')
            ->where('date', $date)
            ->where(function ($q) use ($empIds, $userIds) {
                if (!empty($empIds)) {
                    $q->whereIn('employee_id', $empIds);
                }
                if (!empty($userIds)) {
                    $q->orWhereIn('user_id', $userIds);
                }
            })
            ->get();

        $attByEmp = [];
        foreach ($attendances as $att) {
            if ($att->employee_id) {
                $attByEmp['emp_' . $att->employee_id] = $att;
            }
            if ($att->user_id) {
                $attByEmp['user_' . $att->user_id] = $att;
            }
        }

        // Lấy các bản ghi xếp ca trong ngày từ bảng hrm_shift_assignments
        $assignments = HrmShiftAssignment::with('shift')
            ->where('date', $date)
            ->where(function ($q) use ($empIds, $userIds) {
                if (!empty($empIds)) {
                    $q->whereIn('employee_id', $empIds);
                }
                if (!empty($userIds)) {
                    $q->orWhereIn('user_id', $userIds);
                }
            })
            ->get();

        $assignByEmp = [];
        foreach ($assignments as $asn) {
            if ($asn->employee_id) {
                $assignByEmp['emp_' . $asn->employee_id] = $asn;
            }
            if ($asn->user_id) {
                $assignByEmp['user_' . $asn->user_id] = $asn;
            }
        }

        // Lấy danh sách đăng ký làm online ĐÃ ĐƯỢC DUYỆT (status = 1) vào ngày này
        $onlineRegistrations = HrmOnlineRegistration::where('date', $date)
            ->where('status', 1)
            ->where(function ($q) use ($empIds, $userIds) {
                if (!empty($empIds)) {
                    $q->whereIn('employee_id', $empIds);
                }
                if (!empty($userIds)) {
                    $q->orWhereIn('user_id', $userIds);
                }
            })
            ->get();

        $onlineRegByEmp = [];
        foreach ($onlineRegistrations as $reg) {
            if ($reg->employee_id) {
                $onlineRegByEmp['emp_' . $reg->employee_id] = $reg;
            }
            if ($reg->user_id) {
                $onlineRegByEmp['user_' . $reg->user_id] = $reg;
            }
        }

        // Lấy danh sách đơn nghỉ phép ĐÃ ĐƯỢC DUYỆT (status = 1) rơi vào ngày này
        $approvedLeaves = HrmLeaveRequest::where('status', 1)
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->where(function ($q) use ($empIds, $userIds) {
                if (!empty($empIds)) {
                    $q->whereIn('employee_id', $empIds);
                }
                if (!empty($userIds)) {
                    $q->orWhereIn('user_id', $userIds);
                }
            })
            ->get();

        $leaveByEmp = [];
        foreach ($approvedLeaves as $lv) {
            if ($lv->employee_id) {
                $leaveByEmp['emp_' . $lv->employee_id] = $lv;
            }
            if ($lv->user_id) {
                $leaveByEmp['user_' . $lv->user_id] = $lv;
            }
        }

        $allShifts = HrmShift::where('status', 1)->get()->keyBy('id');

        $result = [];
        foreach ($employees as $emp) {
            $att = $attByEmp['emp_' . $emp->id] ?? ($emp->user_id ? ($attByEmp['user_' . $emp->user_id] ?? null) : null);
            $asn = $assignByEmp['emp_' . $emp->id] ?? ($emp->user_id ? ($assignByEmp['user_' . $emp->user_id] ?? null) : null);

            // Ca làm việc: ưu tiên ca đã lưu trong bản ghi điểm danh, nếu chưa lưu thì lấy ca xếp trong ngày hoặc ca áp dụng của nhân sự
            $shiftId = null;
            $shiftName = null;
            if ($att && $att->shift_id) {
                $shiftId = $att->shift_id;
                $shiftName = $att->shift ? $att->shift->name : ($allShifts[$shiftId]->name ?? null);
            } elseif ($asn && $asn->shift_id) {
                $shiftId = $asn->shift_id;
                $shiftName = $asn->shift ? $asn->shift->name : ($allShifts[$shiftId]->name ?? null);
            } elseif ($emp->shift_id) {
                $shiftId = $emp->shift_id;
                $shiftName = $emp->shift ? $emp->shift->name : ($allShifts[$shiftId]->name ?? null);
            }

            // Kiểm tra làm Online:
            // 1. Ca làm việc có tên chứa 'Online' hoặc 'online'
            // 2. Hoặc nhân viên có đơn đăng ký làm online ĐÃ ĐƯỢC DUYỆT cho ngày hôm đó
            $isShiftOnline = ($shiftName && mb_stripos($shiftName, 'online') !== false);
            $onlineReg = $onlineRegByEmp['emp_' . $emp->id] ?? ($emp->user_id ? ($onlineRegByEmp['user_' . $emp->user_id] ?? null) : null);
            $isOnline = $isShiftOnline || !empty($onlineReg);
            $onlineReason = $isShiftOnline ? 'Ca làm việc Online' : ($onlineReg ? 'Đã duyệt đăng ký làm Online' : null);

            // Lọc theo ca làm việc nếu request có truyền shift_id
            if ($request->filled('shift_id')) {
                if ($request->shift_id == 'unassigned') {
                    if (!empty($shiftId))
                        continue;
                } else {
                    if ($shiftId != $request->shift_id)
                        continue;
                }
            }

            // Kiểm tra nghỉ phép ĐÃ ĐƯỢC DUYỆT cho ngày hôm đó
            $leaveReq = $leaveByEmp['emp_' . $emp->id] ?? ($emp->user_id ? ($leaveByEmp['user_' . $emp->user_id] ?? null) : null);
            $isLeaveApproved = !empty($leaveReq);
            $leaveReason = $leaveReq ? $leaveReq->reason : null;
            $leaveApprovalType = $leaveReq ? $leaveReq->approval_type : null;
            $leaveDurationDays = $leaveReq ? (float)$leaveReq->duration_days : 1.0;

            // Trạng thái điểm danh:
            // 1. Ưu tiên Nghỉ phép đã duyệt: tự động mang trạng thái 'leave_approved' / 'leave_approved_half' (hoặc 'late_submitted')
            // 2. Tiếp theo: Online -> tự động mang trạng thái 'online'
            // 3. Mặc định: lấy từ bản ghi attendance hoặc 'not_checked'
            $attendanceStatus = $att ? $att->attendance_status : 'not_checked';
            $overrideEdit = false;
            if ($isLeaveApproved) {
                if ($leaveApprovalType === 'late_unpaid') {
                    $expectedLeaveStatus = 'late_submitted';
                } elseif ($leaveDurationDays > 0 && $leaveDurationDays < 1) {
                    $expectedLeaveStatus = 'leave_approved_half';
                } else {
                    $expectedLeaveStatus = 'leave_approved';
                }

                if ($att && $att->attendance_status && $att->attendance_status !== $expectedLeaveStatus) {
                    $overrideEdit = true;
                } else {
                    $attendanceStatus = $expectedLeaveStatus;
                }
            } elseif ($isOnline) {
                if ($att && $att->attendance_status && $att->attendance_status !== 'online') {
                    $overrideEdit = true;
                } else {
                    $attendanceStatus = 'online';
                }
            }

            $result[] = [
                'employee_profile_id' => $emp->id,
                'user_id' => $emp->user_id,
                'employee_code' => $emp->employee_code,
                'name' => $emp->full_name ?: ($emp->user ? $emp->user->name : 'N/A'),
                'team_name' => $emp->department ? $emp->department->name : 'N/A',
                'department_id' => $emp->department_id,
                'attendance_id' => $att ? $att->id : null,
                'date' => $date,
                'shift_id' => $shiftId,
                'shift_name' => $shiftName,
                'is_online' => $isOnline,
                'online_reason' => $onlineReason,
                'is_leave_approved' => $isLeaveApproved,
                'leave_reason' => $leaveReason,
                'leave_approval_type' => $leaveApprovalType,
                'leave_duration_days' => $leaveDurationDays,
                'override_edit' => $overrideEdit,
                // not_checked, online, present, leave_approved, leave_approved_half, unexcused_absent, unexcused_absent_half, late_fined, late_submitted
                'attendance_status' => $attendanceStatus,
                'check_in' => $att ? $att->check_in : null,
                'check_out' => $att ? $att->check_out : null,
                'note' => $att ? $att->note : '',
            ];
        }

        return response()->json([
            'date' => $date,
            'is_hrm_admin' => HrmScopeService::isHrmAdmin($currentUser),
            'data' => $result
        ]);
    }

    /**
     * Cập nhật nhanh trạng thái điểm danh cho 1 hoặc nhiều nhân sự (lưu cả ca làm việc)
     */
    public function updateAttendanceStatus(Request $request)
    {
        if (!HrmScopeService::isHrmAdmin(Auth::user())) {
            return response()->json([
                'status' => 0,
                'message' => 'Bạn chỉ có quyền xem bảng chấm công, không có quyền chỉnh sửa điểm danh!'
            ], 403);
        }

        $request->validate([
            'date' => 'required|date',
            'records' => 'required|array',
        ]);

        $date = $request->date;
        foreach ($request->records as $item) {
            $empId = $item['employee_profile_id'] ?? null;
            $userId = $item['user_id'] ?? null;
            if (!$empId && !$userId)
                continue;

            $condition = ['date' => $date];
            if ($empId) {
                $condition['employee_id'] = $empId;
            } else {
                $condition['user_id'] = $userId;
            }

            // Lấy ca làm việc từ record gửi lên
            $shiftId = null;
            if (array_key_exists('shift_id', $item)) {
                $shiftId = !empty($item['shift_id']) ? $item['shift_id'] : null;
            } else {
                // Nếu record chưa có shift_id, tìm từ bảng xếp ca hoặc ca áp dụng của nhân sự
                $asn = HrmShiftAssignment::where('date', $date)
                    ->where(function ($q) use ($empId, $userId) {
                        if ($empId)
                            $q->where('employee_id', $empId);
                        if ($userId)
                            $q->orWhere('user_id', $userId);
                    })
                    ->first();
                if ($asn) {
                    $shiftId = $asn->shift_id;
                } elseif ($empId) {
                    $empProfile = HrmEmployeeProfile::find($empId);
                    if ($empProfile) {
                        $shiftId = $empProfile->shift_id;
                    }
                }
            }

            $statusToSave = $item['attendance_status'] ?? 'not_checked';
            if (empty($item['override_edit'])) {
                if (!empty($item['is_leave_approved'])) {
                    if (($item['leave_approval_type'] ?? '') === 'late_unpaid') {
                        $statusToSave = 'late_submitted';
                    } else {
                        $leaveDays = isset($item['leave_duration_days']) ? (float)$item['leave_duration_days'] : 1.0;
                        $statusToSave = ($leaveDays > 0 && $leaveDays < 1) ? 'leave_approved_half' : 'leave_approved';
                    }
                } elseif (!empty($item['is_online'])) {
                    $statusToSave = 'online';
                }
            }

            HrmAttendance::updateOrCreate(
                $condition,
                [
                    'employee_id' => $empId,
                    'user_id' => $userId,
                    'shift_id' => $shiftId,
                    'attendance_status' => $statusToSave,
                    'note' => $item['note'] ?? null,
                ]
            );

            if ($shiftId) {
                HrmShiftAssignment::updateOrCreate(
                    [
                        'date' => $date,
                        'employee_id' => $empId,
                    ],
                    [
                        'user_id' => $userId,
                        'shift_id' => $shiftId,
                        'is_overtime' => 0
                    ]
                );
            }
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

        $currentUser = Auth::user();
        $query = HrmEmployeeProfile::with(['user', 'department', 'shift'])
            ->where('work_status', 'working');

        // Phân quyền dữ liệu theo phạm vi (Admin xem ALL, Trưởng phòng/Lead xem cấp dưới & phòng ban, Nhân viên xem chính mình)
        $query = HrmScopeService::applyProfileScope($query, $currentUser);

        $employees = $query->get();

        $empIds = $employees->pluck('id')->filter()->all();
        $userIds = $employees->pluck('user_id')->filter()->all();

        $attendances = HrmAttendance::whereBetween('date', [$startDate, $endDate])
            ->where(function ($q) use ($empIds, $userIds) {
                if (!empty($empIds)) {
                    $q->whereIn('employee_id', $empIds);
                }
                if (!empty($userIds)) {
                    $q->orWhereIn('user_id', $userIds);
                }
            })
            ->get();

        // Lấy danh sách đăng ký làm online ĐÃ DUYỆT trong tháng
        $approvedOnlineRegs = HrmOnlineRegistration::whereBetween('date', [$startDate, $endDate])
            ->where('status', 1)
            ->where(function ($q) use ($empIds, $userIds) {
                if (!empty($empIds)) {
                    $q->whereIn('employee_id', $empIds);
                }
                if (!empty($userIds)) {
                    $q->orWhereIn('user_id', $userIds);
                }
            })
            ->get();

        $summary = [];
        foreach ($employees as $emp) {
            $userAtts = $attendances->filter(function ($att) use ($emp) {
                return ($att->employee_id && $att->employee_id == $emp->id) || ($emp->user_id && $att->user_id == $emp->user_id);
            });

            $workedDays = $userAtts->where('attendance_status', 'present')->count()
                + ($userAtts->where('attendance_status', 'leave_approved_half')->count() * 0.5)
                + ($userAtts->where('attendance_status', 'unexcused_absent_half')->count() * 0.5);
            $lateFined = $userAtts->where('attendance_status', 'late_fined')->count();
            $leaveApproved = $userAtts->where('attendance_status', 'leave_approved')->count()
                + ($userAtts->where('attendance_status', 'leave_approved_half')->count() * 0.5);
            $unexcusedAbsent = $userAtts->where('attendance_status', 'unexcused_absent')->count()
                + ($userAtts->where('attendance_status', 'unexcused_absent_half')->count() * 0.5);
            $lateSubmitted = $userAtts->where('attendance_status', 'late_submitted')->count();

            // Tính số ngày làm online:
            $isPermanentOnline = ($emp->shift && mb_stripos($emp->shift->name, 'online') !== false);
            if ($isPermanentOnline) {
                $onlineDays = $standardWorkDays;
            } else {
                $userOnlineRegs = $approvedOnlineRegs->filter(function ($r) use ($emp) {
                    return ($r->employee_id && $r->employee_id == $emp->id) || ($emp->user_id && $r->user_id == $emp->user_id);
                })->map(function ($r) {
                    return Carbon::parse($r->date)->format('Y-m-d');
                });
                $attOnlineDates = $userAtts->where('attendance_status', 'online')->map(function ($a) {
                    return Carbon::parse($a->date)->format('Y-m-d');
                });
                $onlineDays = $userOnlineRegs->merge($attOnlineDates)->unique()->count();
            }

            $summary[] = [
                'employee_profile_id' => $emp->id,
                'user_id' => $emp->user_id,
                'employee_code' => $emp->employee_code,
                'name' => $emp->full_name ?: ($emp->user ? $emp->user->name : 'N/A'),
                'team_name' => $emp->department ? $emp->department->name : 'N/A',
                'is_permanent_online' => $isPermanentOnline,
                'standard_days' => $standardWorkDays,
                'worked_days' => $workedDays,
                'online_days' => $onlineDays,
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
     */
    public function quickSearchSummary(Request $request)
    {
        $keyword = trim($request->keyword);
        $asciiKw = \Illuminate\Support\Str::ascii($keyword);
        if (!$keyword) {
            return response()->json(['status' => 0, 'message' => 'Vui lòng nhập từ khóa tìm kiếm']);
        }

        $month = $request->month ?: date('m');
        $year = $request->year ?: date('Y');

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->format('Y-m-d');

        // Tìm nhân sự
        $profile = HrmEmployeeProfile::with(['user', 'department', 'shift'])
            ->where(function ($q) use ($keyword, $asciiKw) {
                $q->where('employee_code', $keyword)
                    ->orWhere('phone', $keyword)
                    ->orWhere('full_name', 'like', "%{$keyword}%")
                    ->orWhere('full_name', 'like', "%{$asciiKw}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhereHas('user', function ($uq) use ($keyword, $asciiKw) {
                        $uq->where('name', 'like', "%{$keyword}%")
                            ->orWhere('name', 'like', "%{$asciiKw}%")
                            ->orWhere('email', 'like', "%{$keyword}%");
                    });
            })
            ->first();

        if (!$profile) {
            return response()->json(['status' => 0, 'message' => 'Không tìm thấy nhân sự phù hợp']);
        }

        if (!HrmScopeService::canAccessProfile(Auth::user(), $profile)) {
            return response()->json(['status' => 0, 'message' => 'Bạn không có quyền truy cập dữ liệu chấm công của nhân sự này'], 403);
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

        $userAtts = HrmAttendance::where(function ($q) use ($profile) {
            $q->where('employee_id', $profile->id);
            if ($profile->user_id) {
                $q->orWhere('user_id', $profile->user_id);
            }
        })
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $workedDays = $userAtts->where('attendance_status', 'present')->count()
            + ($userAtts->where('attendance_status', 'leave_approved_half')->count() * 0.5)
            + ($userAtts->where('attendance_status', 'unexcused_absent_half')->count() * 0.5);
        $lateFined = $userAtts->where('attendance_status', 'late_fined')->count();
        $leaveApproved = $userAtts->where('attendance_status', 'leave_approved')->count()
            + ($userAtts->where('attendance_status', 'leave_approved_half')->count() * 0.5);
        $unexcusedAbsent = $userAtts->where('attendance_status', 'unexcused_absent')->count()
            + ($userAtts->where('attendance_status', 'unexcused_absent_half')->count() * 0.5);
        $lateSubmitted = $userAtts->where('attendance_status', 'late_submitted')->count();

        // Tính số ngày làm online:
        $isPermanentOnline = ($profile->shift && mb_stripos($profile->shift->name, 'online') !== false);
        if ($isPermanentOnline) {
            $onlineDays = $standardWorkDays;
        } else {
            $userOnlineRegs = HrmOnlineRegistration::where(function ($q) use ($profile) {
                $q->where('employee_id', $profile->id);
                if ($profile->user_id) $q->orWhere('user_id', $profile->user_id);
            })
                ->whereBetween('date', [$startDate, $endDate])
                ->where('status', 1)
                ->get()
                ->map(function ($r) { return Carbon::parse($r->date)->format('Y-m-d'); });

            $attOnlineDates = $userAtts->where('attendance_status', 'online')->map(function ($a) {
                return Carbon::parse($a->date)->format('Y-m-d');
            });
            $onlineDays = $userOnlineRegs->merge($attOnlineDates)->unique()->count();
        }

        return response()->json([
            'status' => 1,
            'data' => [
                'name' => $profile->full_name ?: ($profile->user ? $profile->user->name : 'N/A'),
                'employee_code' => $profile->employee_code,
                'phone' => $profile->phone,
                'team_name' => $profile->department ? $profile->department->name : 'N/A',
                'month' => (int) $month,
                'year' => (int) $year,
                'standard_days' => $standardWorkDays,
                'worked_days' => $workedDays,
                'online_days' => $onlineDays,
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
            'date' => 'required|date',
            'check_in' => 'required',
        ]);

        $data = $request->all();
        $data['check_in_ip'] = $request->ip();

        // Tự động gán shift_id nếu chưa có
        if (empty($data['shift_id'])) {
            $empId = $data['employee_id'] ?? null;
            $userId = $data['user_id'] ?? null;
            $asn = HrmShiftAssignment::where('date', $data['date'])
                ->where(function ($q) use ($empId, $userId) {
                    if ($empId)
                        $q->where('employee_id', $empId);
                    if ($userId)
                        $q->orWhere('user_id', $userId);
                })
                ->first();
            if ($asn) {
                $data['shift_id'] = $asn->shift_id;
            }
        }

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
