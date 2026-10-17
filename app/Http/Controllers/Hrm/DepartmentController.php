<?php

namespace App\Http\Controllers\Hrm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrmDepartment;
use App\Models\HrmEmployeeProfile;
use App\Models\HrmJobTitle;

class DepartmentController extends Controller
{
    public function list(Request $request)
    {
        // Get root departments and load nested children with counts
        $departments = HrmDepartment::with([
            'manager',
            'children' => function ($q) {
                $q->withCount(['employees', 'children']);
            },
            'children.children' => function ($q) {
                $q->withCount(['employees', 'children']);
            },
            'children.children.children' => function ($q) {
                $q->withCount(['employees', 'children']);
            }
        ])
        ->withCount(['employees', 'children'])
        ->whereNull('parent_id')
        ->orderBy('id', 'asc')
        ->get();

        return response()->json($departments);
    }
    
    public function add(Request $request) {
        $department = HrmDepartment::create($request->all());
        return response()->json(['status' => 1, 'message' => 'Thêm phòng ban thành công!', 'data' => $department]);
    }
    
    public function show($id) {
        $department = HrmDepartment::with(['manager', 'parent', 'children'])->find($id);
        return response()->json($department);
    }
    
    public function update(Request $request, $id) {
        $department = HrmDepartment::find($id);
        if ($department) {
            $department->update($request->all());
            return response()->json(['status' => 1, 'message' => 'Cập nhật phòng ban thành công!']);
        }
        return response()->json(['status' => 0, 'message' => 'Không tìm thấy phòng ban!'], 404);
    }

    public function delete(Request $request, $id = null) {
        $id = $id ?: $request->input('id');
        $department = HrmDepartment::find($id);
        if (!$department) {
            return response()->json(['status' => 0, 'message' => 'Phòng ban không tồn tại!'], 404);
        }

        // Check if there are sub-departments
        $childCount = HrmDepartment::where('parent_id', $id)->count();
        if ($childCount > 0) {
            return response()->json([
                'status' => 0,
                'message' => "Không thể xóa: Phòng ban đang có {$childCount} phòng ban/bộ phận trực thuộc. Vui lòng chuyển hoặc xóa các bộ phận con trước!"
            ]);
        }

        // Check if there are employees assigned to this department
        $employeeCount = HrmEmployeeProfile::where('department_id', $id)->count();
        if ($employeeCount > 0) {
            return response()->json([
                'status' => 0,
                'message' => "Không thể xóa: Phòng ban đang có {$employeeCount} nhân sự trực thuộc. Vui lòng điều chuyển nhân sự sang phòng ban khác trước!"
            ]);
        }

        // Unlink job titles linked to this department
        HrmJobTitle::where('department_id', $id)->update(['department_id' => null]);

        $department->delete();
        return response()->json(['status' => 1, 'message' => 'Đã xóa phòng ban thành công!']);
    }
}
