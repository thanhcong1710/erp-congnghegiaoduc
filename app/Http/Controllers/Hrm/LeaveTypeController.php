<?php

namespace App\Http\Controllers\Hrm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrmLeaveType;

class LeaveTypeController extends Controller
{
    public function list(Request $request)
    {
        $types = HrmLeaveType::orderBy('id', 'desc')->get();
        return response()->json($types);
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'days_per_year' => 'required|integer',
        ]);

        $type = HrmLeaveType::create($request->all());
        return response()->json(['status' => 1, 'message' => 'Tạo loại nghỉ phép thành công', 'data' => $type]);
    }

    public function update(Request $request, $id)
    {
        $type = HrmLeaveType::findOrFail($id);
        $type->update($request->all());
        return response()->json(['status' => 1, 'message' => 'Cập nhật loại nghỉ phép thành công', 'data' => $type]);
    }
}
