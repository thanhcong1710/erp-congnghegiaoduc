<?php

namespace App\Http\Controllers\Hrm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrmOnlineRegistration;
use App\Models\HrmEmployeeProfile;
use App\Models\HrmAttendance;
use App\Models\HrmShift;
use App\Services\Hrm\HrmScopeService;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class OnlineRegistrationController extends Controller
{
    /**
     * Danh sách đăng ký làm online
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $canApprove = $currentUser ? $currentUser->canApproveHrmOnlineRegistration() : false;

        $query = HrmOnlineRegistration::with([
            'employee.department',
            'employee.shift',
            'user',
            'approver',
            'creator'
        ]);

        // Phân quyền dữ liệu theo phạm vi (Admin xem ALL, Trưởng phòng/Lead xem cấp dưới & phòng ban, Nhân viên xem chính mình)
        $query = HrmScopeService::applyOnlineRegistrationScope($query, $currentUser);

        // Filter theo Tháng / Năm
        if ($request->filled('month') && $request->filled('year')) {
            $month = (int)$request->month;
            $year = (int)$request->year;
            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->format('Y-m-d');
            $query->whereBetween('date', [$startDate, $endDate]);
        } elseif ($request->filled('year')) {
            $query->whereYear('date', $request->year);
        }

        // Filter theo Phòng ban
        if ($request->filled('department_id') && $request->department_id !== 'all') {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        // Filter theo Trạng thái (0: Chờ duyệt, 1: Đã duyệt, 2: Từ chối)
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', (int)$request->status);
        }

        // Tìm kiếm theo từ khóa
        if ($request->filled('keyword')) {
            $kw = trim($request->keyword);
            $asciiKw = \Illuminate\Support\Str::ascii($kw);
            $query->where(function ($q) use ($kw, $asciiKw) {
                $q->where('reason', 'like', "%{$kw}%")
                  ->orWhereHas('employee', function ($eq) use ($kw, $asciiKw) {
                      $eq->where('employee_code', 'like', "%{$kw}%")
                         ->orWhere('full_name', 'like', "%{$kw}%")
                         ->orWhere('full_name', 'like', "%{$asciiKw}%")
                         ->orWhere('phone', 'like', "%{$kw}%");
                  })
                  ->orWhereHas('user', function ($uq) use ($kw, $asciiKw) {
                      $uq->where('name', 'like', "%{$kw}%")
                         ->orWhere('name', 'like', "%{$asciiKw}%");
                  });
            });
        }

        $registrations = $query->orderBy('date', 'desc')
                               ->orderBy('id', 'desc')
                               ->get();

        $formatted = $registrations->map(function ($item) {
            $emp = $item->employee;
            $user = $item->user;
            $shift = $emp ? $emp->shift : null;

            return [
                'id' => $item->id,
                'employee_id' => $item->employee_id,
                'user_id' => $item->user_id,
                'employee_code' => $emp ? $emp->employee_code : ($user ? $user->hrm_id : '---'),
                'employee_name' => $emp ? $emp->full_name : ($user ? $user->name : 'N/A'),
                'team_name' => ($emp && $emp->department) ? $emp->department->name : '---',
                'current_shift_name' => $shift ? $shift->name : 'Chưa xếp ca',
                'date' => $item->date ? Carbon::parse($item->date)->format('Y-m-d') : '',
                'reason' => $item->reason,
                'attachment_path' => $item->attachment_path ? url($item->attachment_path) : null,
                'attachment_name' => $item->attachment_name,
                'status' => (int)$item->status,
                'approved_by_name' => $item->approver ? $item->approver->name : null,
                'approved_at' => $item->approved_at ? Carbon::parse($item->approved_at)->format('d/m/Y H:i') : null,
                'rejected_reason' => $item->rejected_reason,
                'created_by_name' => $item->creator ? $item->creator->name : null,
                'created_at' => $item->created_at ? Carbon::parse($item->created_at)->format('d/m/Y H:i') : null,
            ];
        });

        return response()->json([
            'status' => 1,
            'can_approve' => $canApprove,
            'data' => $formatted
        ]);
    }

    /**
     * Tạo đơn đăng ký làm online
     * Quy tắc:
     * - Chỉ được đăng ký ngày trong tương lai (date > today), không được chọn ngày trong quá khứ hoặc hôm nay.
     * - Nếu ca cố định đã là online thì báo không cần đăng ký.
     * - Cho phép đính kèm file.
     */
    public function store(Request $request)
    {
        $currentUser = Auth::user();

        // 1. Xác định nhân sự
        $employeeId = $request->employee_id;
        $employee = null;
        if ($employeeId) {
            $employee = HrmEmployeeProfile::with('shift')->find($employeeId);
        }

        if (!$employee && $currentUser) {
            $employee = HrmEmployeeProfile::with('shift')->where('user_id', $currentUser->id)->first();
        }

        if (!$employee) {
            return response()->json([
                'status' => 0,
                'message' => 'Không tìm thấy thông tin hồ sơ nhân viên!'
            ], 422);
        }

        // Kiểm tra phạm vi: Chỉ được đăng ký cho bản thân hoặc nhân sự cấp dưới
        if ($currentUser && !HrmScopeService::canAccessProfile($currentUser, $employee)) {
            return response()->json([
                'status' => 0,
                'message' => 'Bạn chỉ có quyền đăng ký làm online cho chính mình hoặc nhân viên cấp dưới thuộc quyền quản lý!'
            ], 403);
        }

        // Kiểm tra ca làm việc của nhân sự
        if ($employee->shift && mb_stripos($employee->shift->name, 'online') !== false) {
            return response()->json([
                'status' => 0,
                'message' => 'Nhân viên đã được xếp ca làm việc cố định là "Online", không cần đăng ký làm online!'
            ], 422);
        }

        // 2. Xử lý danh sách ngày đăng ký
        $datesInput = $request->dates;
        $dates = [];
        if (is_array($datesInput)) {
            $dates = $datesInput;
        } elseif (is_string($datesInput)) {
            // Có thể là JSON hoặc chuỗi cách nhau bằng dấu phẩy
            $decoded = json_decode($datesInput, true);
            if (is_array($decoded)) {
                $dates = $decoded;
            } else {
                $dates = array_map('trim', explode(',', $datesInput));
            }
        } elseif ($request->filled('date')) {
            $dates = [$request->date];
        }

        $dates = array_unique(array_filter($dates));
        if (empty($dates)) {
            return response()->json([
                'status' => 0,
                'message' => 'Vui lòng chọn ít nhất một ngày làm online!'
            ], 422);
        }

        // 3. VALIDATION QUAN TRỌNG: Không được đăng ký ngày trong quá khứ và cả ngày hiện tại (chỉ được trong tương lai)
        $todayStr = Carbon::today()->format('Y-m-d');
        foreach ($dates as $d) {
            $parsedDate = Carbon::parse($d)->format('Y-m-d');
            if ($parsedDate <= $todayStr) {
                return response()->json([
                    'status' => 0,
                    'message' => "Ngày " . Carbon::parse($d)->format('d/m/Y') . " không hợp lệ! Không được đăng ký ngày trong quá khứ hoặc ngày hiện tại. Bạn chỉ được phép đăng ký ngày trong tương lai (từ ngày mai trở đi)!"
                ], 422);
            }
        }

        // 4. Kiểm tra trùng lặp ngày đã đăng ký
        $existing = HrmOnlineRegistration::where('employee_id', $employee->id)
            ->whereIn('date', $dates)
            ->whereIn('status', [0, 1]) // Chờ duyệt hoặc Đã duyệt
            ->get();

        if ($existing->isNotEmpty()) {
            $dupDates = $existing->map(function ($item) {
                return Carbon::parse($item->date)->format('d/m/Y');
            })->join(', ');
            return response()->json([
                'status' => 0,
                'message' => "Các ngày sau đã được đăng ký trước đó: {$dupDates}. Vui lòng kiểm tra lại!"
            ], 422);
        }

        // 5. Xử lý file đính kèm (nếu có)
        $attachmentPath = null;
        $attachmentName = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $attachmentName = $file->getClientOriginalName();
            $ext = $file->getClientOriginalExtension();
            $fileName = 'online_reg_' . time() . '_' . uniqid() . '.' . $ext;
            
            $targetDir = public_path('uploads/hrm_online_registrations');
            if (!File::isDirectory($targetDir)) {
                File::makeDirectory($targetDir, 0755, true, true);
            }
            $file->move($targetDir, $fileName);
            $attachmentPath = 'uploads/hrm_online_registrations/' . $fileName;
        }

        // 6. Tạo các bản ghi đăng ký
        $createdRecords = [];
        foreach ($dates as $d) {
            $record = HrmOnlineRegistration::create([
                'employee_id' => $employee->id,
                'user_id' => $employee->user_id,
                'date' => Carbon::parse($d)->format('Y-m-d'),
                'reason' => $request->reason,
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
                'status' => 0, // Chờ duyệt
                'created_by' => $currentUser ? $currentUser->id : null,
            ]);
            $createdRecords[] = $record;
        }

        return response()->json([
            'status' => 1,
            'message' => 'Gửi đơn đăng ký làm online thành công (' . count($createdRecords) . ' ngày). Đang chờ Kế toán hoặc Admin phê duyệt!',
            'data' => $createdRecords
        ]);
    }

    /**
     * Phê duyệt đơn đăng ký làm online (Chỉ dành cho Kế toán hoặc Admin)
     */
    public function approve(Request $request, $id)
    {
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->canApproveHrmOnlineRegistration()) {
            return response()->json([
                'status' => 0,
                'message' => 'Bạn không có quyền phê duyệt! Chỉ Kế toán hoặc Admin mới có quyền duyệt đơn đăng ký làm online.'
            ], 403);
        }

        $reg = HrmOnlineRegistration::findOrFail($id);

        if ((int)$reg->status !== 0) {
            $statusText = ((int)$reg->status === 1) ? 'Đã duyệt' : 'Đã từ chối';
            return response()->json([
                'status' => 0,
                'message' => "Đơn này đã ở trạng thái cuối ({$statusText}), không thể cập nhật thêm!"
            ], 422);
        }

        $reg->status = 1; // Đã duyệt
        $reg->approved_by = $currentUser->id;
        $reg->approved_at = Carbon::now();
        $reg->rejected_reason = null;
        $reg->save();

        // Đồng bộ điểm danh ngày hôm đó nếu đã có bản ghi
        $att = HrmAttendance::where(function ($q) use ($reg) {
            if ($reg->employee_id) $q->where('employee_id', $reg->employee_id);
            if ($reg->user_id) $q->orWhere('user_id', $reg->user_id);
        })->where('date', $reg->date)->first();

        if ($att) {
            $att->attendance_status = 'online';
            $att->save();
        }

        return response()->json([
            'status' => 1,
            'message' => 'Phê duyệt đơn đăng ký làm online thành công!'
        ]);
    }

    /**
     * Từ chối đơn đăng ký làm online (Chỉ dành cho Kế toán hoặc Admin)
     */
    public function reject(Request $request, $id)
    {
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->canApproveHrmOnlineRegistration()) {
            return response()->json([
                'status' => 0,
                'message' => 'Bạn không có quyền thao tác! Chỉ Kế toán hoặc Admin mới có quyền từ chối đơn đăng ký làm online.'
            ], 403);
        }

        $reg = HrmOnlineRegistration::findOrFail($id);

        if ((int)$reg->status !== 0) {
            $statusText = ((int)$reg->status === 1) ? 'Đã duyệt' : 'Đã từ chối';
            return response()->json([
                'status' => 0,
                'message' => "Đơn này đã ở trạng thái cuối ({$statusText}), không thể cập nhật thêm!"
            ], 422);
        }

        $reg->status = 2; // Từ chối
        $reg->approved_by = $currentUser->id;
        $reg->approved_at = Carbon::now();
        $reg->rejected_reason = $request->rejected_reason ?: 'Kế toán/Admin từ chối';
        $reg->save();

        // Đồng bộ điểm danh: nếu trước đó đã được duyệt (status = online), revert về not_checked
        $att = HrmAttendance::where(function ($q) use ($reg) {
            if ($reg->employee_id) $q->where('employee_id', $reg->employee_id);
            if ($reg->user_id) $q->orWhere('user_id', $reg->user_id);
        })->where('date', $reg->date)->first();

        if ($att && $att->attendance_status === 'online') {
            $att->attendance_status = 'not_checked';
            $att->save();
        }

        return response()->json([
            'status' => 1,
            'message' => 'Đã từ chối đơn đăng ký làm online.'
        ]);
    }

    /**
     * Xóa đơn đăng ký (nếu là người tạo khi còn chờ duyệt, hoặc Admin/Kế toán)
     */
    public function destroy(Request $request, $id)
    {
        $currentUser = Auth::user();
        $reg = HrmOnlineRegistration::findOrFail($id);

        $canApprove = $currentUser ? $currentUser->canApproveHrmOnlineRegistration() : false;
        $isOwner = $currentUser && ($currentUser->id == $reg->created_by || $currentUser->id == $reg->user_id);

        if (!$canApprove && (!$isOwner || $reg->status != 0)) {
            return response()->json([
                'status' => 0,
                'message' => 'Bạn không có quyền xóa đơn này!'
            ], 403);
        }

        // Xóa file nếu có
        if ($reg->attachment_path && File::exists(public_path($reg->attachment_path))) {
            File::delete(public_path($reg->attachment_path));
        }

        $reg->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Đã xóa đơn đăng ký làm online thành công.'
        ]);
    }
}

