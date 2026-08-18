<?php

namespace App\Http\Controllers\Hrm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    public function list(Request $request)
    {
        $limit = $request->limit ? (int)$request->limit : 20;
        $query = \App\Models\HrmLeaveRequest::with(['user', 'leaveType']);

        if ($request->keyword) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->keyword . '%');
            });
        }

        $leaves = $query->orderBy('id', 'desc')->paginate($limit);
        return response()->json($leaves);
    }

    public function add(Request $request)
    {
        try {
            $leave = new \App\Models\HrmLeaveRequest();
            $leave->user_id = $request->user_id ?: \Auth::id(); // Use provided user_id or currently logged in user
            $leave->leave_type_id = $request->leave_type_id;
            $leave->type = 'other'; // Legacy column, dummy value
            $leave->start_date = $request->start_date;
            $leave->end_date = $request->end_date;
            $leave->reason = $request->reason;
            $leave->status = 0; // Chờ duyệt
            $leave->save();

            return response()->json([
                'status' => 1,
                'message' => 'Tạo đơn nghỉ phép thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Lỗi: ' . $e->getMessage()
            ]);
        }
    }

    public function approve(Request $request, $id)
    {
        try {
            $leave = \App\Models\HrmLeaveRequest::findOrFail($id);
            // $request->status is 1 (Approve) or 2 (Reject)
            $leave->status = $request->status;
            if ($leave->status == 2) {
                $leave->rejection_reason = $request->rejection_reason;
            }
            $leave->approved_by = \Auth::id();
            $leave->save();

            return response()->json([
                'status' => 1,
                'message' => 'Đã cập nhật trạng thái đơn nghỉ phép!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Lỗi: ' . $e->getMessage()
            ]);
        }
    }
}
