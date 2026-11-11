<?php

namespace App\Http\Controllers\Hrm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrmLeaveRequest;
use App\Models\HrmLeaveBalance;
use App\Models\HrmLeaveType;
use App\Models\HrmAttendance;
use App\Models\HrmEmployeeProfile;
use App\Models\HrmDepartment;
use App\Services\Hrm\HrmScopeService;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeaveRequestController extends Controller
{
    /**
     * Danh sách đơn nghỉ phép
     */
    public function list(Request $request)
    {
        $limit = $request->limit ? (int)$request->limit : 20;
        $currentUser = Auth::user();

        $query = HrmLeaveRequest::with(['user', 'employee', 'leaveType', 'managerApprover', 'hrApprover']);

        // Phân quyền dữ liệu theo phạm vi (Admin xem ALL, Trưởng phòng/Lead xem cấp dưới & phòng ban, Nhân viên xem chính mình)
        $query = HrmScopeService::applyLeaveScope($query, $currentUser);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('keyword')) {
            $kw = trim($request->keyword);
            $asciiKw = \Illuminate\Support\Str::ascii($kw);
            $query->where(function ($q) use ($kw, $asciiKw) {
                $q->whereHas('user', function ($sub) use ($kw, $asciiKw) {
                    $sub->where('name', 'like', "%{$kw}%")
                        ->orWhere('name', 'like', "%{$asciiKw}%")
                        ->orWhere('email', 'like', "%{$kw}%");
                })->orWhereHas('employee', function ($sub) use ($kw, $asciiKw) {
                    $sub->where('full_name', 'like', "%{$kw}%")
                        ->orWhere('full_name', 'like', "%{$asciiKw}%")
                        ->orWhere('employee_code', 'like', "%{$kw}%");
                });
            });
        }

        $leaves = $query->orderBy('id', 'desc')->paginate($limit);
        $data = $leaves->toArray();
        $data['can_approve'] = $currentUser ? $currentUser->canApproveHrmLeave() : false;
        return response()->json($data);
    }

    /**
     * Tạo đơn xin nghỉ / đổi ca / off sang onl
     */
    public function add(Request $request)
    {
        try {
            $currentUser = Auth::user();
            $userId = $request->user_id ?: ($currentUser ? $currentUser->id : 1);

            // Kiểm tra phạm vi: Chỉ được tạo đơn cho bản thân hoặc cấp dưới
            if ($currentUser && (int)$userId !== (int)$currentUser->id) {
                $targetProfile = HrmEmployeeProfile::where('user_id', $userId)->first();
                if ($targetProfile && !HrmScopeService::canAccessProfile($currentUser, $targetProfile)) {
                    return response()->json([
                        'status' => 0,
                        'message' => 'Bạn chỉ có quyền tạo đơn cho chính mình hoặc nhân viên cấp dưới thuộc quyền quản lý!'
                    ], 403);
                }
            }

            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            $durationDays = $request->duration_days ?: (max(1, $startDate->diffInDays($endDate) + 1));

            $leave = new HrmLeaveRequest();
            $leave->user_id = $userId;
            $empProfile = HrmEmployeeProfile::where('user_id', $userId)->first();
            $leave->employee_id = $request->employee_id ?: ($empProfile ? $empProfile->id : null);
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
            $currentUser = Auth::user();
            if (!$currentUser) {
                return response()->json(['status' => 0, 'message' => 'Vui lòng đăng nhập'], 401);
            }

            $leave = HrmLeaveRequest::findOrFail($id);

            // Chỉ cho phép duyệt hoặc từ chối khi bản ghi ở trạng thái mới tạo (chờ duyệt: status == 0)
            // Nếu đã ở trạng thái cuối (1: đã duyệt, 2: đã từ chối) thì chặn không cho cập nhật
            if ((int)$leave->status !== 0) {
                $statusText = ((int)$leave->status === 1) ? 'Đã duyệt' : 'Đã từ chối';
                return response()->json([
                    'status' => 0,
                    'message' => "Đơn này đã ở trạng thái cuối ({$statusText}), không thể cập nhật thêm!"
                ], 422);
            }

            // Kiểm tra quyền phê duyệt đơn nghỉ phép: Admin, Kế toán hoặc Leader
            $canApprove = $currentUser->canApproveHrmLeave();
            if (!$canApprove) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Bạn không có quyền phê duyệt! Chỉ Kế toán, Admin hoặc Quản lý trực tiếp mới có quyền duyệt đơn nghỉ phép.'
                ], 403);
            }

            // Nếu không phải Admin, kiểm tra xem nhân viên có thuộc quyền quản lý hoặc phòng ban quản lý không
            if (!HrmScopeService::isHrmAdmin($currentUser)) {
                $subordinateIds = HrmScopeService::getSubordinateUserIds($currentUser);
                $accessibleDeptIds = HrmScopeService::getAccessibleDepartmentIds($currentUser);

                $empDeptId = $leave->employee ? $leave->employee->department_id : null;
                $canApprove = in_array((int)$leave->user_id, $subordinateIds, true)
                    || ($empDeptId && in_array((int)$empDeptId, $accessibleDeptIds, true));

                if (!$canApprove) {
                    return response()->json([
                        'status' => 0,
                        'message' => 'Bạn chỉ có quyền phê duyệt đơn nghỉ phép của nhân viên thuộc team hoặc phòng ban mình quản lý!'
                    ], 403);
                }
            }

            $approvalType = $request->approval_type ?: 'approved'; // approved, half_salary, late_unpaid, rejected

            if ($approvalType === 'rejected') {
                $wasApproved = ($leave->status == 1);
                $leave->status = 2; // Từ chối
                $leave->approval_type = 'rejected';
                $leave->rejection_reason = $request->rejection_reason ?: 'Quản lý từ chối';

                // Nếu đơn này trước đó đã được duyệt thì hoàn lại số ngày phép và hoàn lại trạng thái điểm danh
                if ($wasApproved) {
                    if ($leave->request_category === 'leave') {
                        $year = Carbon::parse($leave->start_date)->year;
                        $balance = HrmLeaveBalance::where('user_id', $leave->user_id)->where('year', $year)->first();
                        if ($balance) {
                            $balance->used_days = max(0, $balance->used_days - $leave->duration_days);
                            $balance->save();
                        }
                    }

                    $empProfile = HrmEmployeeProfile::where('user_id', $leave->user_id)->first();
                    $empId = $leave->employee_id ?: ($empProfile ? $empProfile->id : null);
                    HrmAttendance::where(function ($q) use ($empId, $leave) {
                        if ($empId) $q->where('employee_id', $empId);
                        if ($leave->user_id) $q->orWhere('user_id', $leave->user_id);
                    })
                    ->whereBetween('date', [$leave->start_date, $leave->end_date])
                    ->whereIn('attendance_status', ['leave_approved', 'leave_approved_half'])
                    ->update(['attendance_status' => 'not_checked']);
                }
            } else {
                $leave->status = 1; // Đã duyệt
                $leave->approval_type = $approvalType;
                $leave->rejection_reason = null;

                // Tự động trừ số ngày phép vào hrm_leave_balances nếu là đơn nghỉ phép đã duyệt
                if ($leave->request_category === 'leave' && in_array($approvalType, ['approved'])) {
                    $year = Carbon::parse($leave->start_date)->year;
                    $empProfile = HrmEmployeeProfile::where('user_id', $leave->user_id)
                        ->orWhere('id', $leave->employee_id)
                        ->first();
                    $defaultDays = $empProfile ? $empProfile->getDefaultAnnualLeaveDays() : 12.0;

                    $balance = HrmLeaveBalance::where(function ($q) use ($leave, $empProfile) {
                        if ($empProfile) {
                            $q->where('employee_id', $empProfile->id);
                        }
                        if ($leave->user_id) {
                            $q->orWhere('user_id', $leave->user_id);
                        }
                    })->where('year', $year)->first();

                    if (!$balance) {
                        $balance = HrmLeaveBalance::create([
                            'user_id' => $leave->user_id,
                            'employee_id' => $empProfile ? $empProfile->id : null,
                            'leave_type_id' => $leave->leave_type_id ?: 1,
                            'year' => $year,
                            'total_days' => $defaultDays,
                            'used_days' => 0
                        ]);
                    }

                    $balance->used_days = (float)$balance->used_days + (float)$leave->duration_days;
                    $balance->save();
                }

                // Tự động cập nhật bảng điểm danh trong khoảng ngày nghỉ cho cả employee_id và user_id
                $empProfile = HrmEmployeeProfile::where('user_id', $leave->user_id)->first();
                $empId = $leave->employee_id ?: ($empProfile ? $empProfile->id : null);

                $currentDay = Carbon::parse($leave->start_date);
                $endDay = Carbon::parse($leave->end_date);
                while ($currentDay->lte($endDay)) {
                    $dateStr = $currentDay->format('Y-m-d');
                    $attStatus = 'leave_approved';
                    if ($approvalType === 'late_unpaid') {
                        $attStatus = 'late_submitted';
                    } elseif ((float)$leave->duration_days > 0 && (float)$leave->duration_days < 1) {
                        $attStatus = 'leave_approved_half';
                    }

                    $condition = ['date' => $dateStr];
                    if ($empId) {
                        $condition['employee_id'] = $empId;
                    } else {
                        $condition['user_id'] = $leave->user_id;
                    }

                    HrmAttendance::updateOrCreate(
                        $condition,
                        [
                            'employee_id' => $empId,
                            'user_id' => $leave->user_id,
                            'attendance_status' => $attStatus,
                            'note' => 'Theo đơn xin phép #' . $leave->id
                        ]
                    );
                    $currentDay->addDay();
                }
            }

            $leave->hr_approved_by = $currentUser->id;
            $leave->approved_by = $currentUser->id;
            $leave->approved_at = Carbon::now();
            $leave->save();

            return response()->json([
                'status' => 1,
                'message' => ($approvalType === 'rejected') ? 'Đã từ chối đơn thành công!' : 'Phê duyệt đơn thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Lỗi: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Từ chối đơn nghỉ phép trực tiếp với lý do
     */
    public function reject(Request $request, $id)
    {
        $request->merge(['approval_type' => 'rejected']);
        return $this->approve($request, $id);
    }

    /**
     * Lấy thông tin số dư phép năm:
     * Phép năm được hưởng: mặc định Fulltime có 12 ngày, CTV hoặc khác là 0 ngày, hoặc số ngày theo cấu hình balance do Admin/HR điều chỉnh
     * Đã sử dụng: X ngày
     * Còn lại: Y ngày
     */
    public function getLeaveBalance(Request $request)
    {
        $userId = $request->user_id ?: Auth::id();
        $year = $request->year ?: date('Y');

        $empProfile = HrmEmployeeProfile::where('user_id', $userId)->first();
        $isFulltime = $empProfile ? $empProfile->isFulltime() : false;
        $defaultDays = $isFulltime ? 12.0 : 0.0;

        $balance = HrmLeaveBalance::where(function ($q) use ($userId, $empProfile) {
            if ($empProfile) {
                $q->where('employee_id', $empProfile->id);
            }
            if ($userId) {
                $q->orWhere('user_id', $userId);
            }
        })->where('year', $year)->first();

        if (!$balance) {
            $balance = HrmLeaveBalance::create([
                'user_id' => $userId,
                'employee_id' => $empProfile ? $empProfile->id : null,
                'leave_type_id' => 1,
                'year' => $year,
                'total_days' => $defaultDays,
                'used_days' => 0
            ]);
        }

        $totalDays = (float)$balance->total_days;
        $usedDays = (float)$balance->used_days;
        $remainingDays = max(0, $totalDays - $usedDays);

        return response()->json([
            'status' => 1,
            'data' => [
                'user_id' => $userId,
                'employee_id' => $empProfile ? $empProfile->id : null,
                'year' => $year,
                'is_fulltime' => $isFulltime,
                'employee_type' => $empProfile ? $empProfile->employee_type : null,
                'contract_type' => $empProfile ? $empProfile->contract_type : null,
                'start_date' => $empProfile ? $empProfile->start_date : null,
                'total_days' => $totalDays,
                'used_days' => $usedDays,
                'remaining_days' => $remainingDays,
                'note' => $balance->note,
            ]
        ]);
    }

    /**
     * Admin hoặc Quản lý nhân sự điều chỉnh số ngày phép năm của nhân viên
     */
    public function updateLeaveBalance(Request $request)
    {
        try {
            $currentUser = Auth::user();
            if (!$currentUser) {
                return response()->json(['status' => 0, 'message' => 'Vui lòng đăng nhập'], 401);
            }

            // Chỉ Admin hoặc Quản lý nhân sự (HR Manager) mới có quyền điều chỉnh
            if (!HrmScopeService::isHrmAdmin($currentUser) && !$currentUser->isHrmAdmin()) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Bạn không có quyền điều chỉnh số ngày phép! Chỉ Admin hoặc Quản lý nhân sự mới có quyền này.'
                ], 403);
            }

            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'employee_id' => 'required|exists:hrm_employee_profiles,id',
                'year' => 'required|integer',
                'total_days' => 'required|numeric|min:0|max:365',
                'used_days' => 'nullable|numeric|min:0|max:365',
                'note' => 'nullable|string|max:255'
            ], [
                'employee_id.required' => 'Chưa chọn nhân viên',
                'employee_id.exists' => 'Không tìm thấy hồ sơ nhân viên',
                'year.required' => 'Chưa chọn năm',
                'total_days.required' => 'Vui lòng nhập số ngày phép được hưởng',
                'total_days.numeric' => 'Số ngày phép phải là số hợp lệ',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $profile = HrmEmployeeProfile::findOrFail($request->employee_id);
            $year = (int)$request->year;
            $totalDays = (float)$request->total_days;

            // Tìm hoặc tạo mới balance theo employee_id hoặc user_id
            $balance = HrmLeaveBalance::where('employee_id', $profile->id)
                ->where('year', $year)
                ->first();

            if (!$balance && $profile->user_id) {
                $balance = HrmLeaveBalance::where('user_id', $profile->user_id)
                    ->where('year', $year)
                    ->first();
            }

            if (!$balance) {
                $balance = new HrmLeaveBalance();
                $balance->employee_id = $profile->id;
                $balance->user_id = $profile->user_id ?: null;
                $balance->leave_type_id = 1; // Mặc định phép năm
                $balance->year = $year;
                $balance->used_days = 0;
            } else {
                if (!$balance->employee_id) {
                    $balance->employee_id = $profile->id;
                }
                if (!$balance->user_id && $profile->user_id) {
                    $balance->user_id = $profile->user_id;
                }
            }

            $balance->total_days = $totalDays;
            if ($request->has('used_days') && $request->used_days !== null && $request->used_days !== '') {
                $balance->used_days = (float)$request->used_days;
            }
            if ($request->has('note')) {
                $balance->note = trim($request->note) ?: null;
            }
            $balance->updated_by = $currentUser->id;
            $balance->save();

            $remainingDays = max(0, $balance->total_days - (float)$balance->used_days);
            $overDays = max(0, (float)$balance->used_days - $balance->total_days);

            return response()->json([
                'status' => 1,
                'message' => "Đã điều chỉnh thành công quỹ phép năm {$year} cho nhân sự {$profile->full_name}: {$totalDays} ngày!",
                'data' => [
                    'employee_id' => $profile->id,
                    'year' => $year,
                    'total_days' => (float)$balance->total_days,
                    'used_days' => (float)$balance->used_days,
                    'remaining_days' => (float)$remainingDays,
                    'over_days' => (float)$overDays,
                    'note' => $balance->note,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Lỗi điều chỉnh: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bảng tổng hợp số dư và tình hình nghỉ phép của TẤT CẢ nhân sự trong công ty
     */
    public function allBalances(Request $request)
    {
        try {
            $year = $request->year ? (int)$request->year : 2026;
            $limit = $request->limit ? (int)$request->limit : 20;
            $departmentId = $request->department_id;
            $keyword = trim($request->keyword ?? '');
            $currentUser = Auth::user();
            $canAdjustBalance = $currentUser ? ($currentUser->isHrmAdmin() || HrmScopeService::isHrmAdmin($currentUser)) : false;

            // Query nhân sự có hồ sơ profile
            $query = HrmEmployeeProfile::with(['department', 'user']);
            $query = HrmScopeService::applyProfileScope($query, $currentUser);

            if ($departmentId && $departmentId !== 'all') {
                $query->where('department_id', $departmentId);
            }

            if ($keyword !== '') {
                $asciiKw = \Illuminate\Support\Str::ascii($keyword);
                $query->where(function ($q) use ($keyword, $asciiKw) {
                    $q->where('full_name', 'like', "%{$keyword}%")
                        ->orWhere('full_name', 'like', "%{$asciiKw}%")
                        ->orWhere('employee_code', 'like', "%{$keyword}%");
                });
            }

            // Lấy toàn bộ profiles trong phạm vi query để tính thống kê chuẩn xác theo danh sách hiển thị
            $allScopedProfiles = (clone $query)->get();
            $scopedEmpIds = $allScopedProfiles->pluck('id')->filter()->toArray();
            $scopedUserIds = $allScopedProfiles->pluck('user_id')->filter()->toArray();

            $allBalances = HrmLeaveBalance::where('year', $year)
                ->whereIn('employee_id', $scopedEmpIds)
                ->get()
                ->keyBy('employee_id');

            // Lấy leave requests đã duyệt trong năm cho các nhân sự này
            $scopedLeaveRequests = HrmLeaveRequest::where('status', 1)
                ->whereYear('start_date', $year)
                ->where(function ($q) use ($scopedEmpIds, $scopedUserIds) {
                    $q->whereIn('employee_id', $scopedEmpIds)
                      ->orWhereIn('user_id', $scopedUserIds);
                })
                ->get(['employee_id', 'user_id', 'duration_days']);

            $totalScopedUsedDays = 0.0;
            $overLimitCount = 0;
            $outOfLeaveCount = 0;

            foreach ($allScopedProfiles as $profile) {
                $isFulltime = $profile->isFulltime();
                $balance = $allBalances->get($profile->id);
                // Ưu tiên số ngày phép cấu hình trong balance (do Admin/HR gán hoặc điều chỉnh)
                $totalDays = $balance ? (float)$balance->total_days : ($isFulltime ? 12.0 : 0.0);
                if ($balance && $balance->used_days !== null) {
                    $used = (float)$balance->used_days;
                } else {
                    $used = (float)$scopedLeaveRequests->filter(function ($lr) use ($profile) {
                        return ($lr->employee_id && $lr->employee_id == $profile->id)
                            || ($profile->user_id && $lr->user_id == $profile->user_id);
                    })->sum('duration_days');
                }

                $totalScopedUsedDays += $used;
                if ($totalDays > 0 || $used > 0) {
                    if ($used > $totalDays) {
                        $overLimitCount++;
                    }
                    if ($used >= $totalDays && $used > 0) {
                        $outOfLeaveCount++;
                    }
                }
            }

            // Phân trang danh sách nhân sự
            $paginated = $query->orderBy('id', 'desc')->paginate($limit);

            $data = [];
            foreach ($paginated->items() as $index => $profile) {
                $isFulltime = $profile->isFulltime();
                $balance = $allBalances->get($profile->id);
                $totalDays = $balance ? (float)$balance->total_days : ($isFulltime ? 12.0 : 0.0);
                $usedDays = $balance ? (float)$balance->used_days : 0.0;

                // Nếu chưa có balance mà có leave requests đã duyệt thì tính
                if (!$balance) {
                    $usedDays = (float)HrmLeaveRequest::where(function ($q) use ($profile) {
                        $q->where('employee_id', $profile->id);
                        if ($profile->user_id) {
                            $q->orWhere('user_id', $profile->user_id);
                        }
                    })->whereYear('start_date', $year)
                      ->where('status', 1)
                      ->sum('duration_days');
                }

                $remainingDays = max(0, $totalDays - $usedDays);
                $overDays = max(0, $usedDays - $totalDays);

                // Lấy 3 đợt nghỉ gần nhất
                $recentLeaves = HrmLeaveRequest::where(function ($q) use ($profile) {
                    $q->where('employee_id', $profile->id);
                    if ($profile->user_id) {
                        $q->orWhere('user_id', $profile->user_id);
                    }
                })->whereYear('start_date', $year)
                  ->where('status', 1)
                  ->orderBy('start_date', 'desc')
                  ->limit(3)
                  ->get(['id', 'start_date', 'end_date', 'duration_days', 'reason', 'approval_type']);

                $data[] = [
                    'id' => $profile->id,
                    'user_id' => $profile->user_id,
                    'employee_code' => $profile->employee_code ?: 'CHAM' . str_pad($profile->id, 3, '0', STR_PAD_LEFT),
                    'full_name' => $profile->full_name,
                    'employee_type' => $profile->employee_type,
                    'contract_type' => $profile->contract_type,
                    'start_date' => $profile->start_date,
                    'probation_start_date' => $profile->probation_start_date,
                    'is_fulltime' => $isFulltime,
                    'department_id' => $profile->department_id,
                    'department_name' => $profile->department ? $profile->department->name : 'Chưa phân bổ',
                    'work_status' => $profile->work_status ?: 'working',
                    'total_days' => $totalDays,
                    'used_days' => $usedDays,
                    'remaining_days' => $remainingDays,
                    'over_days' => $overDays,
                    'balance_note' => $balance ? $balance->note : null,
                    'has_custom_balance' => (bool)$balance,
                    'is_over_limit' => $overDays > 0,
                    'is_out_of_leave' => $remainingDays <= 0 && $usedDays > 0,
                    'recent_leaves' => $recentLeaves,
                ];
            }

            // Lấy danh sách phòng ban cho bộ lọc
            $departments = HrmDepartment::select('id', 'name')->orderBy('name', 'asc')->get();

            return response()->json([
                'status' => 1,
                'year' => $year,
                'can_adjust_balance' => $canAdjustBalance,
                'stats' => [
                    'total_employees' => $paginated->total(),
                    'total_company_used_days' => (float)$totalScopedUsedDays,
                    'over_limit_count' => $overLimitCount,
                    'out_of_leave_count' => $outOfLeaveCount,
                ],
                'departments' => $departments,
                'pagination' => [
                    'total' => $paginated->total(),
                    'per_page' => $paginated->perPage(),
                    'current_page' => $paginated->currentPage(),
                    'last_page' => $paginated->lastPage(),
                ],
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Chi tiết toàn bộ lịch sử nghỉ phép của một nhân viên cụ thể
     */
    public function employeeLeaveHistory(Request $request, $employeeId)
    {
        try {
            $year = $request->year ? (int)$request->year : 2026;
            $profile = HrmEmployeeProfile::with(['department', 'user'])->findOrFail($employeeId);

            $leavesQuery = HrmLeaveRequest::with(['leaveType', 'hrApprover'])
                ->where(function ($q) use ($profile) {
                    $q->where('employee_id', $profile->id);
                    if ($profile->user_id) {
                        $q->orWhere('user_id', $profile->user_id);
                    }
                });

            if ($year) {
                $leavesQuery->whereYear('start_date', $year);
            }

            $leaves = $leavesQuery->orderBy('start_date', 'desc')->get();

            $balance = HrmLeaveBalance::where('employee_id', $profile->id)
                ->where('year', $year)
                ->first();

            if (!$balance && $profile->user_id) {
                $balance = HrmLeaveBalance::where('user_id', $profile->user_id)
                    ->where('year', $year)
                    ->first();
            }

            $isFulltime = $profile->isFulltime();
            $totalDays = $balance ? (float)$balance->total_days : ($isFulltime ? 12.0 : 0.0);
            $usedDays = (float)$leaves->where('status', 1)->sum('duration_days');
            $remainingDays = max(0, $totalDays - $usedDays);

            return response()->json([
                'status' => 1,
                'employee' => [
                    'id' => $profile->id,
                    'employee_code' => $profile->employee_code,
                    'full_name' => $profile->full_name,
                    'employee_type' => $profile->employee_type,
                    'contract_type' => $profile->contract_type,
                    'start_date' => $profile->start_date,
                    'is_fulltime' => $isFulltime,
                    'department_name' => $profile->department ? $profile->department->name : 'Chưa phân bổ',
                    'work_status' => $profile->work_status,
                ],
                'summary' => [
                    'year' => $year,
                    'is_fulltime' => $isFulltime,
                    'total_days' => $totalDays,
                    'used_days' => $usedDays,
                    'remaining_days' => $remainingDays,
                    'over_days' => max(0, $usedDays - $totalDays),
                    'total_requests' => $leaves->count(),
                    'balance_note' => $balance ? $balance->note : null,
                ],
                'leaves' => $leaves,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import danh sách nghỉ phép tự do từ bảng tính / raw text
     */
    public function importRawLeaves(Request $request)
    {
        try {
            $rows = $request->rows;
            if (!is_array($rows) || empty($rows)) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Dữ liệu import rỗng hoặc không đúng định dạng!'
                ]);
            }

            $deptMap = [
                'Sale' => 5,
                'Đào tạo' => 7,
                'Marketing' => 9,
                'Khối văn phòng' => 6,
            ];

            $importedCount = 0;
            $updatedProfiles = [];

            DB::beginTransaction();

            foreach ($rows as $row) {
                $rawName = trim($row['name'] ?? '');
                if (!$rawName) continue;

                $cleanName = \Normalizer::normalize($rawName, \Normalizer::FORM_C);
                $cleanName = mb_convert_case(preg_replace('/\s+/', ' ', $cleanName), MB_CASE_TITLE, "UTF-8");

                // Tìm hoặc tạo Profile
                $profile = HrmEmployeeProfile::where('full_name', $cleanName)
                    ->orWhere('full_name', 'like', '%' . $cleanName . '%')
                    ->first();

                if (!$profile) {
                    $profile = new HrmEmployeeProfile();
                    $profile->employee_code = HrmEmployeeProfile::generateNextEmployeeCode();
                    $profile->full_name = $cleanName;
                    $deptName = trim($row['dept'] ?? '');
                    $profile->department_id = $deptMap[$deptName] ?? 6;
                    $profile->work_status = (isset($row['note']) && strpos($row['note'], 'Đã nghỉ') !== false) ? 'resigned' : 'working';
                    $profile->save();
                }

                $days = (float)str_replace(',', '.', $row['days'] ?? 1);
                $dateStr = trim($row['date'] ?? '');
                $year = 2026;

                // Chuẩn hóa ngày
                if (strpos($dateStr, '-') !== false) {
                    $dParts = explode('-', $dateStr);
                    $start = Carbon::createFromFormat('d/m/Y', trim($dParts[0]))->format('Y-m-d');
                    $end = Carbon::createFromFormat('d/m/Y', trim($dParts[1]))->format('Y-m-d');
                    $year = Carbon::parse($start)->year;
                } else {
                    $cleanDate = preg_replace('/(\d{1,2}\/\d{1,2})$/', '$1/2026', $dateStr);
                    $start = Carbon::createFromFormat('d/m/Y', $cleanDate)->format('Y-m-d');
                    $end = $start;
                    $year = Carbon::parse($start)->year;
                }

                $note = trim($row['note'] ?? '');
                $approvalType = (strpos($note, 'Gửi đơn muộn') !== false) ? 'late_unpaid' : 'approved';

                $req = new HrmLeaveRequest();
                $req->employee_id = $profile->id;
                $req->user_id = $profile->user_id ?: 1;
                $req->leave_type_id = 1;
                $req->type = 'vacation';
                $req->request_category = 'leave';
                $req->start_date = $start;
                $req->end_date = $end;
                $req->duration_days = $days;
                $req->reason = $note ?: 'Nghỉ phép import theo kế hoạch';
                $req->status = 1;
                $req->approval_type = $approvalType;
                $req->approved_by = Auth::id() ?: 1;
                $req->hr_approved_by = Auth::id() ?: 1;
                $req->approved_at = Carbon::now();
                $req->save();

                $importedCount++;
                $updatedProfiles[$profile->id] = $year;
            }

            // Đồng bộ lại balance
            foreach ($updatedProfiles as $empId => $yr) {
                $prof = HrmEmployeeProfile::find($empId);
                $isFulltime = $prof ? $prof->isFulltime() : false;
                $totalUsed = (float)HrmLeaveRequest::where('employee_id', $empId)
                    ->whereYear('start_date', $yr)
                    ->where('status', 1)
                    ->sum('duration_days');

                $bal = HrmLeaveBalance::firstOrNew(['employee_id' => $empId, 'year' => $yr]);
                $bal->total_days = $isFulltime ? 12.0 : 0.0;
                $bal->used_days = $totalUsed;
                $bal->save();
            }

            DB::commit();

            return response()->json([
                'status' => 1,
                'message' => "Đã import thành công {$importedCount} dòng nghỉ phép!",
                'count' => $importedCount,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 0,
                'message' => 'Lỗi import: ' . $e->getMessage()
            ], 500);
        }
    }
}
