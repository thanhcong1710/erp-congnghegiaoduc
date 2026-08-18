<?php

namespace App\Http\Controllers\Hrm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrmOvertimeRequest;

class OvertimeRequestController extends Controller
{
    public function list(Request $request)
    {
        $requests = HrmOvertimeRequest::with(['user', 'approver'])->orderBy('id', 'desc')->get();
        return response()->json($requests);
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        $otRequest = HrmOvertimeRequest::create($request->all());
        return response()->json(['status' => 1, 'message' => 'Tạo đơn OT thành công', 'data' => $otRequest]);
    }

    public function updateStatus(Request $request, $id)
    {
        $otRequest = HrmOvertimeRequest::findOrFail($id);
        $otRequest->status = $request->status;
        $otRequest->approved_by = $request->user()->id ?? 1; // Default to 1 if no user in context for testing
        $otRequest->save();

        return response()->json(['status' => 1, 'message' => 'Cập nhật trạng thái đơn OT thành công']);
    }
}
