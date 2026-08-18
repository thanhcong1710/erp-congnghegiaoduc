<?php

namespace App\Http\Controllers\Hrm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrmJobLevel;

class JobLevelController extends Controller
{
    public function list(Request $request)
    {
        $levels = HrmJobLevel::all();
        return response()->json($levels);
    }
    
    public function add(Request $request) {
        $level = HrmJobLevel::create($request->all());
        return response()->json(['status' => 1, 'message' => 'Created successfully', 'data' => $level]);
    }
    
    public function show($id) {
        $level = HrmJobLevel::find($id);
        return response()->json($level);
    }
    
    public function update(Request $request, $id) {
        $level = HrmJobLevel::find($id);
        if ($level) {
            $level->update($request->all());
            return response()->json(['status' => 1, 'message' => 'Updated successfully']);
        }
        return response()->json(['status' => 0, 'message' => 'Not found'], 404);
    }
}
