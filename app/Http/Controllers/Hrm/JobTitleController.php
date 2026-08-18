<?php

namespace App\Http\Controllers\Hrm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrmJobTitle;

class JobTitleController extends Controller
{
    public function list(Request $request)
    {
        $titles = HrmJobTitle::with('department')->get();
        return response()->json($titles);
    }
    
    public function add(Request $request) {
        $title = HrmJobTitle::create($request->all());
        return response()->json(['status' => 1, 'message' => 'Created successfully', 'data' => $title]);
    }
    
    public function show($id) {
        $title = HrmJobTitle::find($id);
        return response()->json($title);
    }
    
    public function update(Request $request, $id) {
        $title = HrmJobTitle::find($id);
        if ($title) {
            $title->update($request->all());
            return response()->json(['status' => 1, 'message' => 'Updated successfully']);
        }
        return response()->json(['status' => 0, 'message' => 'Not found'], 404);
    }
}
