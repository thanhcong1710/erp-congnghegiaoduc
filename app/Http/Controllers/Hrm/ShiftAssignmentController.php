<?php

namespace App\Http\Controllers\Hrm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrmShiftAssignment;
use App\Models\HrmEmployeeProfile;
use App\Models\HrmAttendance;
use App\Models\HrmShift;
use Illuminate\Support\Facades\Auth;

class ShiftAssignmentController extends Controller
{
    /**
     * Lấy danh sách nhân viên kèm ca làm việc áp dụng hiện tại (áp dụng đến khi thay đổi)
     */
    public function list(Request $request)
    {
        $currentUser = Auth::user();

        $query = HrmEmployeeProfile::with(['user', 'department', 'shift'])
            ->where('work_status', 'working');

        // Phân quyền nếu là Leader chỉ thấy nhân sự team mình
        if ($currentUser) {
            $isHrOrAdmin = $currentUser->isHrmAdmin();
            if (!$isHrOrAdmin && $currentUser->isLeader()) {
                $subordinateIds = explode(',', $currentUser->getStaffHasUser());
                $query->whereIn('user_id', $subordinateIds);
            }
        }

        // Lọc theo phòng ban
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Lọc theo ca làm việc
        if ($request->filled('shift_id')) {
            if ($request->shift_id === 'unassigned') {
                $query->whereNull('shift_id');
            } else {
                $query->where('shift_id', $request->shift_id);
            }
        }

        // Tìm kiếm theo từ khóa (tên, mã NV, SĐT)
        if ($request->filled('keyword')) {
            $kw = trim($request->keyword);
            $asciiKw = \Illuminate\Support\Str::ascii($kw);
            $query->where(function ($q) use ($kw, $asciiKw) {
                $q->where('full_name', 'like', "%{$kw}%")
                  ->orWhere('full_name', 'like', "%{$asciiKw}%")
                  ->orWhere('employee_code', 'like', "%{$kw}%")
                  ->orWhere('phone', 'like', "%{$kw}%")
                  ->orWhereHas('user', function ($uq) use ($kw, $asciiKw) {
                      $uq->where('name', 'like', "%{$kw}%")
                         ->orWhere('name', 'like', "%{$asciiKw}%");
                  });
            });
        }

        $employees = $query->orderBy('full_name', 'asc')->get();

        $result = [];
        foreach ($employees as $emp) {
            $result[] = [
                'employee_id' => $emp->id,
                'user_id' => $emp->user_id,
                'employee_code' => $emp->employee_code,
                'name' => $emp->full_name ?: ($emp->user ? $emp->user->name : 'N/A'),
                'department_id' => $emp->department_id,
                'team_name' => $emp->department ? $emp->department->name : 'N/A',
                'shift_id' => $emp->shift_id,
                'shift_name' => $emp->shift ? $emp->shift->name : null,
                'shift_start_time' => $emp->shift ? $emp->shift->start_time : null,
                'shift_end_time' => $emp->shift ? $emp->shift->end_time : null,
                'updated_at' => $emp->updated_at ? $emp->updated_at->format('d/m/Y H:i') : null,
            ];
        }

        return response()->json([
            'status' => 1,
            'data' => $result
        ]);
    }

    /**
     * Cập nhật ca làm việc áp dụng cho 1 hoặc nhiều nhân sự (áp dụng đến khi có thay đổi)
     */
    public function batchAssign(Request $request)
    {
        $request->validate([
            'employee_ids' => 'required|array|min:1',
            'shift_id' => 'required|integer|exists:hrm_shifts,id',
        ]);

        $employeeIds = $request->employee_ids;
        $shiftId = (int)$request->shift_id;
        $today = date('Y-m-d');

        // Cập nhật trực tiếp ca làm việc vào profile nhân viên
        HrmEmployeeProfile::whereIn('id', $employeeIds)->update([
            'shift_id' => $shiftId,
        ]);

        // Lưu lịch sử xếp ca vào hrm_shift_assignments
        $employees = HrmEmployeeProfile::whereIn('id', $employeeIds)->get();
        foreach ($employees as $emp) {
            HrmShiftAssignment::create([
                'employee_id' => $emp->id,
                'user_id' => $emp->user_id,
                'shift_id' => $shiftId,
                'date' => $today,
                'is_overtime' => 0
            ]);
        }

        // Cập nhật điểm danh hôm nay nếu đã có bản ghi
        HrmAttendance::where('date', $today)
            ->whereIn('employee_id', $employeeIds)
            ->update(['shift_id' => $shiftId]);

        $shift = HrmShift::find($shiftId);
        $shiftName = $shift ? $shift->name : "ID #$shiftId";
        $count = count($employeeIds);

        return response()->json([
            'status' => 1,
            'message' => "Đã xếp ca '{$shiftName}' thành công cho {$count} nhân sự (áp dụng đến khi thay đổi)."
        ]);
    }

    /**
     * Thêm / cập nhật đơn lẻ (tương thích ngược)
     */
    public function add(Request $request)
    {
        $request->validate([
            'shift_id' => 'required|integer|exists:hrm_shifts,id',
        ]);

        $empId = $request->employee_id;
        $userId = $request->user_id;

        if (!$empId && $userId) {
            $emp = HrmEmployeeProfile::where('user_id', $userId)->first();
            if ($emp) {
                $empId = $emp->id;
            }
        }

        if (!$empId) {
            return response()->json(['status' => 0, 'message' => 'Vui lòng cung cấp nhân viên'], 422);
        }

        HrmEmployeeProfile::where('id', $empId)->update([
            'shift_id' => $request->shift_id,
        ]);

        $assignment = HrmShiftAssignment::create([
            'employee_id' => $empId,
            'user_id' => $userId,
            'shift_id' => $request->shift_id,
            'date' => date('Y-m-d'),
            'is_overtime' => 0,
        ]);

        HrmAttendance::where('date', date('Y-m-d'))
            ->where('employee_id', $empId)
            ->update(['shift_id' => $request->shift_id]);

        return response()->json(['status' => 1, 'message' => 'Cập nhật ca làm việc thành công', 'data' => $assignment]);
    }
}
