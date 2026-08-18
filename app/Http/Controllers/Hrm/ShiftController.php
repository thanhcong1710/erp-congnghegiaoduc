<?php

namespace App\Http\Controllers\Hrm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrmShift;

class ShiftController extends Controller
{
    public function list(Request $request)
    {
        $shifts = HrmShift::orderBy('id', 'desc')->get();
        return response()->json($shifts);
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        $shift = HrmShift::create($request->all());
        return response()->json(['status' => 1, 'message' => 'Tạo ca làm việc thành công', 'data' => $shift]);
    }

    public function show($id)
    {
        $shift = HrmShift::findOrFail($id);
        return response()->json($shift);
    }

    public function update(Request $request, $id)
    {
        $shift = HrmShift::findOrFail($id);
        $shift->update($request->all());
        return response()->json(['status' => 1, 'message' => 'Cập nhật ca làm việc thành công', 'data' => $shift]);
    }
}
