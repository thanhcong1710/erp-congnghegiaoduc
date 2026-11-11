<?php

namespace App\Http\Controllers\Hrm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrmCompany;
use App\Services\Hrm\HrmScopeService;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    /**
     * Lấy toàn bộ danh sách công ty đang hoạt động (dùng cho dropdown/filter)
     */
    public function all()
    {
        $companies = HrmCompany::where('status', 1)
            ->withCount(['employees', 'departments'])
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'status' => 1,
            'data' => $companies
        ]);
    }

    /**
     * Danh sách công ty có phân trang / tìm kiếm
     */
    public function list(Request $request)
    {
        $query = HrmCompany::withCount(['employees', 'departments']);

        if ($request->filled('keyword')) {
            $kw = trim($request->keyword);
            $query->where(function ($q) use ($kw) {
                $q->where('name', 'like', "%{$kw}%")
                  ->orWhere('code', 'like', "%{$kw}%");
            });
        }

        $companies = $query->orderBy('id', 'asc')->get();

        return response()->json([
            'status' => 1,
            'data' => $companies
        ]);
    }

    /**
     * Thêm mới công ty
     */
    public function add(Request $request)
    {
        if (!HrmScopeService::isHrmAdmin(Auth::user())) {
            return response()->json(['status' => 0, 'message' => 'Chỉ Quản trị viên HRM mới có quyền thêm công ty!'], 403);
        }

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:hrm_companies,code',
            'name' => 'required|string|max:255',
            'employee_prefix' => 'nullable|string|max:20',
            'contract_prefix' => 'nullable|string|max:20',
        ]);

        $data = $request->all();
        $data['code'] = strtoupper(trim($data['code']));
        if (empty($data['employee_prefix'])) {
            $data['employee_prefix'] = $data['code'];
        }
        if (empty($data['contract_prefix'])) {
            $data['contract_prefix'] = $data['code'];
        }

        $company = HrmCompany::create($data);

        return response()->json([
            'status' => 1,
            'message' => 'Thêm công ty thành công!',
            'data' => $company
        ]);
    }

    /**
     * Cập nhật thông tin công ty
     */
    public function update(Request $request, $id)
    {
        if (!HrmScopeService::isHrmAdmin(Auth::user())) {
            return response()->json(['status' => 0, 'message' => 'Chỉ Quản trị viên HRM mới có quyền sửa công ty!'], 403);
        }

        $company = HrmCompany::find($id);
        if (!$company) {
            return response()->json(['status' => 0, 'message' => 'Không tìm thấy công ty!'], 404);
        }

        $company->update($request->all());

        return response()->json([
            'status' => 1,
            'message' => 'Cập nhật công ty thành công!',
            'data' => $company
        ]);
    }

    /**
     * Lấy gợi ý mã nhân sự tiếp theo cho công ty
     */
    public function nextEmployeeCode(Request $request)
    {
        $companyId = $request->get('company_id');
        $code = \App\Models\HrmEmployeeProfile::generateNextEmployeeCode($companyId);
        return response()->json([
            'status' => 1,
            'code' => $code
        ]);
    }
}

