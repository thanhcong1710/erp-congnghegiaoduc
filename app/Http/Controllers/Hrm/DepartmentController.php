<?php

namespace App\Http\Controllers\Hrm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrmDepartment;
use App\Models\HrmEmployeeProfile;
use App\Models\HrmJobTitle;
use App\Services\Hrm\HrmScopeService;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    public function list(Request $request)
    {
        $currentUser = Auth::user();
        $isHrmAdmin = $currentUser ? HrmScopeService::isHrmAdmin($currentUser) : false;
        $scoped = $request->get('scoped');
        $companyId = $request->get('company_id');

        // Nếu request có flag scoped và user là Trưởng phòng (không phải HRM Admin), chỉ trả về phòng ban quản lý
        if ($scoped && !$isHrmAdmin && $currentUser) {
            $accessibleDeptIds = HrmScopeService::getAccessibleDepartmentIds($currentUser);
            $query = HrmDepartment::whereIn('id', $accessibleDeptIds);
            if ($companyId && $companyId !== 'all') {
                $query->where('company_id', $companyId);
            }
            $scopedDepts = $query->orderBy('id', 'asc')->get();
            return response()->json($scopedDepts);
        }

        // Get root departments and load nested children with counts
        $deptQuery = HrmDepartment::with([
            'company',
            'manager',
            'children' => function ($q) {
                $q->withCount(['employees', 'children'])->with('company');
            },
            'children.children' => function ($q) {
                $q->withCount(['employees', 'children'])->with('company');
            },
            'children.children.children' => function ($q) {
                $q->withCount(['employees', 'children'])->with('company');
            }
        ])
        ->withCount(['employees', 'children'])
        ->whereNull('parent_id');

        if ($companyId && $companyId !== 'all') {
            $deptQuery->where('company_id', $companyId);
        }

        $departments = $deptQuery->orderBy('id', 'asc')->get();

        return response()->json($departments);
    }
    
    public function add(Request $request) {
        if (!HrmScopeService::isHrmAdmin(Auth::user())) {
            return response()->json(['status' => 0, 'message' => 'Chỉ Quản trị viên HRM mới có quyền thêm phòng ban!'], 403);
        }
        $data = $request->all();
        $data['company_id'] = !empty($data['company_id']) ? (int)$data['company_id'] : 1;
        $department = HrmDepartment::create($data);
        return response()->json(['status' => 1, 'message' => 'Thêm phòng ban thành công!', 'data' => $department]);
    }
    
    public function show($id) {
        $department = HrmDepartment::with(['manager', 'parent', 'children', 'company'])->find($id);
        return response()->json($department);
    }
    
    public function update(Request $request, $id) {
        if (!HrmScopeService::isHrmAdmin(Auth::user())) {
            return response()->json(['status' => 0, 'message' => 'Chỉ Quản trị viên HRM mới có quyền cập nhật phòng ban!'], 403);
        }
        $department = HrmDepartment::find($id);
        if ($department) {
            $department->update($request->all());
            return response()->json(['status' => 1, 'message' => 'Cập nhật phòng ban thành công!']);
        }
        return response()->json(['status' => 0, 'message' => 'Không tìm thấy phòng ban!'], 404);
    }

    public function delete(Request $request, $id = null) {
        if (!HrmScopeService::isHrmAdmin(Auth::user())) {
            return response()->json(['status' => 0, 'message' => 'Chỉ Quản trị viên HRM mới có quyền xóa phòng ban!'], 403);
        }
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
