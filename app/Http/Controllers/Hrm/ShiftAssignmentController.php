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
     * Lấy danh sách nhân viên kèm ca làm việc trong ngày
     */
    public function list(Request $request)
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

        if ($request->filled('keyword')) {
            $kw = trim($request->keyword);
            $query->where(function ($q) use ($kw) {
                $q->where('full_name', 'like', "%{$kw}%")
                  ->orWhere('employee_code', 'like', "%{$kw}%")
                  ->orWhere('phone', 'like', "%{$kw}%")
                  ->orWhereHas('user', function ($uq) use ($kw) {
                      $uq->where('name', 'like', "%{$kw}%");
                  });
            });
        }

        $employees = $query->orderBy('full_name', 'asc')->get();

        $empIds = $employees->pluck('id')->filter()->all();
        $userIds = $employees->pluck('user_id')->filter()->all();

        // Lấy danh sách xếp ca trong ngày
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

        $result = [];
        foreach ($employees as $emp) {
            $asn = $assignByEmp['emp_' . $emp->id] ?? ($emp->user_id ? ($assignByEmp['user_' . $emp->user_id] ?? null) : null);

            $result[] = [
                'employee_id' => $emp->id,
                'user_id' => $emp->user_id,
                'employee_code' => $emp->employee_code,
                'name' => $emp->full_name ?: ($emp->user ? $emp->user->name : 'N/A'),
                'department_id' => $emp->department_id,
                'team_name' => $emp->department ? $emp->department->name : 'N/A',
                'assignment_id' => $asn ? $asn->id : null,
                'shift_id' => $asn ? $asn->shift_id : null,
                'shift_name' => ($asn && $asn->shift) ? $asn->shift->name : null,
                'shift_start_time' => ($asn && $asn->shift) ? $asn->shift->start_time : null,
                'shift_end_time' => ($asn && $asn->shift) ? $asn->shift->end_time : null,
                'is_overtime' => $asn ? (int)$asn->is_overtime : 0,
                'date' => $date,
            ];
        }

        return response()->json([
            'status' => 1,
            'date' => $date,
            'data' => $result
        ]);
    }

    /**
     * Cập nhật ca làm việc hàng loạt hoặc 1 nhân sự
     */
    public function batchAssign(Request $request)
    {
        $request->validate([
            'employee_ids' => 'required|array|min:1',
            'shift_id' => 'required|integer',
            'date' => 'required|date',
        ]);

        $employeeIds = $request->employee_ids;
        $shiftId = (int)$request->shift_id;
        $date = $request->date;
        $isOvertime = $request->boolean('is_overtime') || $request->input('is_overtime') == 1 ? 1 : 0;

        $employees = HrmEmployeeProfile::whereIn('id', $employeeIds)->get()->keyBy('id');

        $count = 0;
        foreach ($employeeIds as $empId) {
            $emp = $employees->get($empId);
            $userId = $emp ? $emp->user_id : null;

            HrmShiftAssignment::updateOrCreate(
                [
                    'employee_id' => $empId,
                    'date' => $date,
                ],
                [
                    'user_id' => $userId,
                    'shift_id' => $shiftId,
                    'is_overtime' => $isOvertime,
                ]
            );

            // Đồng bộ sang bảng điểm danh nếu đã tồn tại bản ghi điểm danh trong ngày
            HrmAttendance::where('date', $date)
                ->where(function ($q) use ($empId, $userId) {
                    $q->where('employee_id', $empId);
                    if ($userId) {
                        $q->orWhere('user_id', $userId);
                    }
                })
                ->update(['shift_id' => $shiftId]);

            $count++;
        }

        $shift = HrmShift::find($shiftId);
        $shiftName = $shift ? $shift->name : "ID #$shiftId";

        return response()->json([
            'status' => 1,
            'message' => "Đã xếp ca '{$shiftName}' thành công cho {$count} nhân sự ngày {$date}."
        ]);
    }

    /**
     * Thêm đơn lẻ (tương thích ngược)
     */
    public function add(Request $request)
    {
        $request->validate([
            'shift_id' => 'required',
            'date' => 'required|date',
        ]);

        $empId = $request->employee_id;
        $userId = $request->user_id;

        if (!$empId && $userId) {
            $emp = HrmEmployeeProfile::where('user_id', $userId)->first();
            if ($emp) {
                $empId = $emp->id;
            }
        } elseif ($empId && !$userId) {
            $emp = HrmEmployeeProfile::find($empId);
            if ($emp) {
                $userId = $emp->user_id;
            }
        }

        if (!$empId && !$userId) {
            return response()->json(['status' => 0, 'message' => 'Vui lòng cung cấp nhân viên'], 422);
        }

        $condition = ['date' => $request->date];
        if ($empId) {
            $condition['employee_id'] = $empId;
        } else {
            $condition['user_id'] = $userId;
        }

        $assignment = HrmShiftAssignment::updateOrCreate(
            $condition,
            [
                'employee_id' => $empId,
                'user_id' => $userId,
                'shift_id' => $request->shift_id,
                'is_overtime' => $request->boolean('is_overtime') || $request->input('is_overtime') == 1 ? 1 : 0,
            ]
        );

        // Đồng bộ điểm danh nếu có
        HrmAttendance::where('date', $request->date)
            ->where(function ($q) use ($empId, $userId) {
                if ($empId) $q->where('employee_id', $empId);
                if ($userId) $q->orWhere('user_id', $userId);
            })
            ->update(['shift_id' => $request->shift_id]);

        return response()->json(['status' => 1, 'message' => 'Xếp ca thành công', 'data' => $assignment]);
    }
}
