<?php

namespace App\Http\Controllers\Hrm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrmDepartment;

class DepartmentController extends Controller
{
    public function list(Request $request)
    {
        // Get only root departments and load their nested children
        $departments = HrmDepartment::with(['manager', 'children.manager', 'children.children.manager'])
            ->whereNull('parent_id')
            ->get();
        return response()->json($departments);
    }
    
    public function add(Request $request) {
        $department = HrmDepartment::create($request->all());
        return response()->json(['status' => 1, 'message' => 'Created successfully', 'data' => $department]);
    }
    
    public function show($id) {
        $department = HrmDepartment::with(['manager', 'parent', 'children'])->find($id);
        return response()->json($department);
    }
    
    public function update(Request $request, $id) {
        $department = HrmDepartment::find($id);
        if ($department) {
            $department->update($request->all());
            return response()->json(['status' => 1, 'message' => 'Updated successfully']);
        }
        return response()->json(['status' => 0, 'message' => 'Not found'], 404);
    }
}
