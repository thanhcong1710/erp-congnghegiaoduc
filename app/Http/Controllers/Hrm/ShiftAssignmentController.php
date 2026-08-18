<?php

namespace App\Http\Controllers\Hrm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrmShiftAssignment;

class ShiftAssignmentController extends Controller
{
    public function list(Request $request)
    {
        $assignments = HrmShiftAssignment::with(['user', 'shift'])->orderBy('date', 'desc')->get();
        return response()->json($assignments);
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required',
            'shift_id' => 'required',
            'date' => 'required|date',
        ]);

        $assignment = HrmShiftAssignment::create($request->all());
        return response()->json(['status' => 1, 'message' => 'Xếp ca thành công', 'data' => $assignment]);
    }
}
