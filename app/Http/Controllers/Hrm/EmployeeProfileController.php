<?php

namespace App\Http\Controllers\Hrm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrmEmployeeProfile;
use App\Models\HrmEmploymentHistory;
use App\Models\HrmEmployeeDocument;
use App\Models\HrmContract;
use App\Models\HrmDepartment;
use App\Services\Hrm\HrmScopeService;
use App\User;
use Illuminate\Support\Facades\Auth;

class EmployeeProfileController extends Controller
{
    /**
     * Tự động kiểm tra và chuyển các nhân sự hết hạn thử việc sang fulltime
     */
    private function autoPromoteProbationEmployees()
    {
        $today = date('Y-m-d');
        HrmEmployeeProfile::where('employee_type', 'probation')
            ->whereNotNull('probation_end_date')
            ->where('probation_end_date', '<=', $today)
            ->where('work_status', 'working')
            ->update([
                'employee_type' => 'fulltime'
            ]);
    }

    public function list(Request $request)
    {
        // Tự động kiểm tra chuyển thử việc -> fulltime nếu hết hạn
        $this->autoPromoteProbationEmployees();

        $query = HrmEmployeeProfile::with(['user', 'department', 'jobTitle', 'jobLevel']);

        // Phân quyền dữ liệu theo phạm vi (Admin xem ALL, Trưởng phòng/Lead xem cấp dưới & phòng ban, Nhân viên xem chính mình)
        $currentUser = Auth::user();
        $query = HrmScopeService::applyProfileScope($query, $currentUser);

        // Lọc theo loại nhân sự
        if ($request->filled('employee_type') && $request->employee_type !== 'all') {
            $query->where('employee_type', $request->employee_type);
        }

        // Lọc theo trạng thái làm việc
        if ($request->filled('work_status') && $request->work_status !== 'all') {
            $query->where('work_status', $request->work_status);
        }

        // Lọc theo phòng ban
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Tìm kiếm từ khóa
        if ($request->filled('keyword')) {
            $kw = trim($request->keyword);
            $asciiKw = \Illuminate\Support\Str::ascii($kw);
            $query->where(function ($q) use ($kw, $asciiKw) {
                $q->where('employee_code', 'like', "%{$kw}%")
                    ->orWhere('full_name', 'like', "%{$kw}%")
                    ->orWhere('full_name', 'like', "%{$asciiKw}%")
                    ->orWhere('email', 'like', "%{$kw}%")
                    ->orWhere('phone', 'like', "%{$kw}%")
                    ->orWhereHas('user', function ($sub) use ($kw, $asciiKw) {
                        $sub->where('name', 'like', "%{$kw}%")
                            ->orWhere('name', 'like', "%{$asciiKw}%")
                            ->orWhere('email', 'like', "%{$kw}%")
                            ->orWhere('hrm_id', 'like', "%{$kw}%");
                    });
            });
        }

        $employees = $query->orderBy('id', 'desc')->get();
        $canDelete = $currentUser ? $currentUser->canDeleteHrmEmployee() : false;
        $isHrmAdmin = $currentUser ? HrmScopeService::isHrmAdmin($currentUser) : false;
        return response()->json([
            'status' => 1,
            'can_delete' => $canDelete,
            'is_hrm_admin' => $isHrmAdmin,
            'data' => $employees
        ]);
    }

    public function add(Request $request)
    {
        $currentUser = Auth::user();
        if (!HrmScopeService::isHrmAdmin($currentUser)) {
            return response()->json([
                'status' => 0,
                'message' => 'Bạn chỉ có quyền xem danh sách nhân sự, không có quyền thêm mới!'
            ], 403);
        }

        $data = $request->all();

        // Tự sinh mã nhân sự nếu chưa có (format: CHAM01, CHAM02...)
        if (empty($data['employee_code'])) {
            $data['employee_code'] = HrmEmployeeProfile::generateNextEmployeeCode();
        }

        // Lưu trực tiếp họ tên và email vào profile độc lập
        if ($request->filled('user_name')) {
            $data['full_name'] = $request->user_name;
        }
        if ($request->filled('email')) {
            $data['email'] = trim($request->email);
        }

        // Tạm thời comment phần tự động tạo tài khoản User theo yêu cầu
        /*
        if (empty($data['user_id']) && !empty($data['user_name'])) {
            $email = !empty($data['email']) ? trim($data['email']) : (strtolower($data['employee_code']) . '@erp.local');
            if (empty($data['email'])) {
                $count = User::where('email', $email)->count();
                if ($count > 0) {
                    $email = strtolower($data['employee_code']) . '_' . time() . '@erp.local';
                }
            }
            $newUser = User::create([
                'name' => $data['user_name'],
                'email' => $email,
                'password' => bcrypt('12345678'),
                'status' => 1,
            ]);
            $data['user_id'] = $newUser->id;
        }
        */

        // Nếu là CTV được tạo bởi Leader
        $data['created_by_user_id'] = $currentUser ? $currentUser->id : null;
        if (!empty($data['employee_type']) && $data['employee_type'] === 'ctv') {
            $isHrOrAdmin = $currentUser && $currentUser->isHrmAdmin();
            $data['approval_status'] = $isHrOrAdmin ? 1 : 0; // Nếu Lead tạo thì chờ HR duyệt
        } else {
            $data['approval_status'] = 1;
        }

        // Tự động kiểm tra tính đầy đủ của hồ sơ nếu có danh sách giấy tờ thiếu
        if (isset($data['missing_documents'])) {
            $missingDocs = is_array($data['missing_documents']) ? $data['missing_documents'] : json_decode($data['missing_documents'], true);
            $data['profile_status'] = empty($missingDocs) ? 'full' : 'missing';
        }

        $profile = HrmEmployeeProfile::create($data);
        return response()->json([
            'status' => 1,
            'message' => 'Thêm thông tin nhân viên thành công',
            'data' => $profile
        ]);
    }

    public function show($id)
    {
        $profile = HrmEmployeeProfile::with(['user', 'department', 'jobTitle', 'jobLevel'])->find($id);
        if (!$profile) {
            $profile = HrmEmployeeProfile::with(['user', 'department', 'jobTitle', 'jobLevel'])->where('user_id', $id)->first();
        }

        $currentUser = Auth::user();
        if ($profile && !HrmScopeService::canAccessProfile($currentUser, $profile)) {
            return response()->json(['status' => 0, 'message' => 'Bạn không có quyền truy cập hồ sơ nhân sự này!'], 403);
        }

        $empId = $profile ? $profile->id : null;
        $userId = $profile ? $profile->user_id : null;

        $history = HrmEmploymentHistory::where(function ($q) use ($empId, $userId) {
            if ($empId)
                $q->where('employee_id', $empId);
            if ($userId)
                $q->orWhere('user_id', $userId);
            if (!$empId && !$userId)
                $q->whereRaw('1=0');
        })->orderBy('effective_date', 'desc')->get();

        $documents = HrmEmployeeDocument::where(function ($q) use ($empId, $userId) {
            if ($empId)
                $q->where('employee_id', $empId);
            if ($userId)
                $q->orWhere('user_id', $userId);
            if (!$empId && !$userId)
                $q->whereRaw('1=0');
        })->get();

        $contracts = HrmContract::where(function ($q) use ($empId, $userId) {
            if ($empId)
                $q->where('employee_id', $empId);
            if ($userId)
                $q->orWhere('user_id', $userId);
            if (!$empId && !$userId)
                $q->whereRaw('1=0');
        })->orderBy('start_date', 'desc')->get();

        return response()->json([
            'profile' => $profile,
            'history' => $history,
            'documents' => $documents,
            'contracts' => $contracts
        ]);
    }

    public function update(Request $request, $id)
    {
        $currentUser = Auth::user();
        if (!HrmScopeService::isHrmAdmin($currentUser)) {
            return response()->json(['status' => 0, 'message' => 'Bạn chỉ có quyền xem thông tin nhân sự, không có quyền chỉnh sửa!'], 403);
        }

        $profile = HrmEmployeeProfile::find($id);
        if (!$profile) {
            return response()->json(['status' => 0, 'message' => 'Không tìm thấy hồ sơ'], 404);
        }

        $data = $request->all();
        if (isset($data['missing_documents'])) {
            $missingDocs = is_array($data['missing_documents']) ? $data['missing_documents'] : json_decode($data['missing_documents'], true);
            $data['profile_status'] = empty($missingDocs) ? 'full' : 'missing';
        }

        if ($request->filled('user_name')) {
            $data['full_name'] = $request->user_name;
        }
        if ($request->filled('email')) {
            $data['email'] = trim($request->email);
        }

        // Đồng bộ tên và email sang bảng User nếu có
        if ($profile->user_id) {
            $userUpdates = [];
            if ($request->filled('user_name')) {
                $userUpdates['name'] = $request->user_name;
            }
            if ($request->filled('email')) {
                $userUpdates['email'] = trim($request->email);
            }
            if (!empty($userUpdates)) {
                User::where('id', $profile->user_id)->update($userUpdates);
            }
        }

        $profile->update($data);
        return response()->json(['status' => 1, 'message' => 'Cập nhật thành công']);
    }

    /**
     * Phê duyệt CTV (chỉ HR / Admin)
     */
    public function approveCtv(Request $request, $id)
    {
        $currentUser = Auth::user();
        if (!HrmScopeService::isHrmAdmin($currentUser)) {
            return response()->json(['status' => 0, 'message' => 'Chỉ Quản trị viên hoặc Quản lý HRM mới có quyền phê duyệt CTV!'], 403);
        }

        $profile = HrmEmployeeProfile::findOrFail($id);
        $profile->approval_status = 1;
        $profile->save();

        return response()->json([
            'status' => 1,
            'message' => 'Đã phê duyệt CTV thành công'
        ]);
    }

    /**
     * Chuyển nhân sự thử việc sang Fulltime (1-click)
     */
    public function convertToFulltime(Request $request, $id)
    {
        $currentUser = Auth::user();
        if (!HrmScopeService::isHrmAdmin($currentUser)) {
            return response()->json(['status' => 0, 'message' => 'Chỉ Quản trị viên hoặc Quản lý HRM mới có quyền chuyển nhân sự Fulltime!'], 403);
        }

        $profile = HrmEmployeeProfile::findOrFail($id);
        $profile->employee_type = 'fulltime';
        $profile->save();

        return response()->json([
            'status' => 1,
            'message' => 'Đã chuyển sang nhân sự Fulltime thành công'
        ]);
    }

    /**
     * Cập nhật trạng thái làm việc (Đang làm việc / Đã nghỉ)
     * Chỉ Quản lý HRM hoặc Admin
     */
    public function updateWorkStatus(Request $request, $id)
    {
        $currentUser = Auth::user();
        if (!HrmScopeService::isHrmAdmin($currentUser)) {
            return response()->json(['status' => 0, 'message' => 'Chỉ Quản trị viên hoặc Quản lý HRM mới có quyền cập nhật trạng thái hoạt động!'], 403);
        }

        $profile = HrmEmployeeProfile::findOrFail($id);
        $profile->work_status = $request->work_status; // 'working' or 'resigned'
        if ($request->work_status === 'resigned') {
            $profile->resigned_reason = $request->resigned_reason;
            $profile->resigned_date = $request->resigned_date ?: date('Y-m-d');
        } else {
            $profile->resigned_reason = null;
            $profile->resigned_date = null;
        }
        $profile->save();

        return response()->json([
            'status' => 1,
            'message' => 'Đã cập nhật trạng thái hoạt động'
        ]);
    }

    /**
     * Cập nhật thông tin checklist Hồ sơ
     */
    public function updateProfileDocuments(Request $request, $id)
    {
        $currentUser = Auth::user();
        if (!HrmScopeService::isHrmAdmin($currentUser)) {
            return response()->json(['status' => 0, 'message' => 'Chỉ Quản trị viên hoặc Quản lý HRM mới có quyền cập nhật checklist hồ sơ!'], 403);
        }

        $profile = HrmEmployeeProfile::findOrFail($id);

        $missing = $request->missing_documents ?: [];
        $profile->profile_received_date = $request->profile_received_date;
        $profile->missing_documents = $missing;
        $profile->profile_status = empty($missing) ? 'full' : 'missing';
        $profile->profile_note = $request->profile_note;
        $profile->save();

        return response()->json([
            'status' => 1,
            'message' => 'Cập nhật tình trạng hồ sơ thành công'
        ]);
    }

    /**
     * Danh sách tổng quan hồ sơ & checklist (Theo ảnh 1)
     */
    public function listDocumentsOverview(Request $request)
    {
        $query = HrmEmployeeProfile::with(['user', 'department']);
        $currentUser = Auth::user();
        $query = HrmScopeService::applyProfileScope($query, $currentUser);
        if ($request->filled('keyword')) {
            $kw = trim($request->keyword);
            $asciiKw = \Illuminate\Support\Str::ascii($kw);
            $query->where(function ($q) use ($kw, $asciiKw) {
                $q->where('employee_code', 'like', "%{$kw}%")
                    ->orWhere('full_name', 'like', "%{$kw}%")
                    ->orWhere('full_name', 'like', "%{$asciiKw}%")
                    ->orWhere('email', 'like', "%{$kw}%")
                    ->orWhere('phone', 'like', "%{$kw}%")
                    ->orWhereHas('user', function ($sub) use ($kw, $asciiKw) {
                        $sub->where('name', 'like', "%{$kw}%")
                            ->orWhere('name', 'like', "%{$asciiKw}%");
                    });
            });
        }
        if ($request->filled('profile_status') && $request->profile_status !== 'all') {
            $query->where('profile_status', $request->profile_status);
        }

        $list = $query->orderBy('id', 'desc')->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'employee_code' => $item->employee_code,
                'user_name' => $item->full_name ?: ($item->user ? $item->user->name : 'N/A'),
                'team_name' => $item->department ? $item->department->name : 'Chưa phân team',
                'profile_received_date' => $item->profile_received_date,
                'profile_status' => $item->profile_status, // full: Đã đủ, missing: Thiếu
                'missing_documents' => $item->missing_documents ?: [],
                'profile_note' => $item->profile_note,
            ];
        });

        return response()->json($list);
    }

    /**
     * Thêm Hợp đồng (tự sinh mã CHAM01, CHAM02... nếu trống)
     */
    public function addContract(Request $request)
    {
        $data = $request->all();
        if (empty($data['contract_number'])) {
            $data['contract_number'] = HrmContract::generateNextContractNumber();
        }
        if (!empty($data['employee_profile_id']) && empty($data['employee_id'])) {
            $data['employee_id'] = $data['employee_profile_id'];
        }
        $contract = HrmContract::create($data);
        return response()->json([
            'status' => 1,
            'message' => 'Đã thêm hợp đồng thành công',
            'data' => $contract
        ]);
    }

    /**
     * Danh sách hợp đồng
     */
    public function listContracts(Request $request)
    {
        $query = HrmContract::with(['user', 'employee.department']);
        $currentUser = Auth::user();
        if ($currentUser && !HrmScopeService::isHrmAdmin($currentUser)) {
            $subordinateUserIds = HrmScopeService::getSubordinateUserIds($currentUser);
            $accessibleDeptIds = HrmScopeService::getAccessibleDepartmentIds($currentUser);
            $query->where(function ($q) use ($currentUser, $subordinateUserIds, $accessibleDeptIds) {
                $q->whereIn('user_id', $subordinateUserIds)
                  ->orWhere('user_id', $currentUser->id);
                if (!empty($accessibleDeptIds)) {
                    $q->orWhereHas('employee', function ($eq) use ($accessibleDeptIds) {
                        $eq->whereIn('department_id', $accessibleDeptIds);
                    });
                }
            });
        }
        if ($request->filled('keyword')) {
            $kw = trim($request->keyword);
            $asciiKw = \Illuminate\Support\Str::ascii($kw);
            $query->where(function ($q) use ($kw, $asciiKw) {
                $q->where('contract_number', 'like', "%{$kw}%")
                    ->orWhereHas('user', function ($sub) use ($kw, $asciiKw) {
                        $sub->where('name', 'like', "%{$kw}%")
                            ->orWhere('name', 'like', "%{$asciiKw}%");
                    })
                    ->orWhereHas('employee', function ($sub) use ($kw, $asciiKw) {
                        $sub->where('full_name', 'like', "%{$kw}%")
                            ->orWhere('full_name', 'like', "%{$asciiKw}%")
                            ->orWhere('employee_code', 'like', "%{$kw}%");
                    });
            });
        }
        $contracts = $query->orderBy('id', 'desc')->paginate($request->limit ?: 20);
        return response()->json($contracts);
    }

    public function addDocument(Request $request)
    {
        $paths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = time() . '_' . rand(1000, 9999) . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '', $file->getClientOriginalName());
                $file->move(public_path('uploads/documents'), $filename);
                $paths[] = '/uploads/documents/' . $filename;
            }
        }

        $docs = [];
        $empId = $request->employee_id ?: ($request->employee_profile_id ?: null);
        if (count($paths) > 0) {
            foreach ($paths as $index => $path) {
                $docTitle = count($paths) > 1 ? $request->title . ' - Phần ' . ($index + 1) : $request->title;
                $docs[] = HrmEmployeeDocument::create([
                    'employee_id' => $empId,
                    'user_id' => $request->user_id,
                    'title' => $docTitle,
                    'document_type' => $request->document_type,
                    'expiry_date' => $request->expiry_date,
                    'file_path' => url($path)
                ]);
            }
        } else {
            $docData = $request->except('attachments');
            if ($empId) {
                $docData['employee_id'] = $empId;
            }
            $docs[] = HrmEmployeeDocument::create($docData);
        }

        return response()->json(['status' => 1, 'message' => 'Đã tải lên tài liệu thành công', 'data' => $docs]);
    }

    /**
     * Xóa nhân viên (chỉ áp dụng cho Admin và Kế toán để xóa bản ghi import nhầm)
     */
    public function delete(Request $request, $id = null)
    {
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->canDeleteHrmEmployee()) {
            return response()->json([
                'status' => 0,
                'message' => 'Bạn không có quyền xóa nhân sự. Chức năng này chỉ áp dụng cho Quản trị viên (Admin) và Kế toán.'
            ], 403);
        }

        $empId = $id ?: $request->id;
        $profile = HrmEmployeeProfile::find($empId);
        if (!$profile) {
            return response()->json([
                'status' => 0,
                'message' => 'Không tìm thấy hồ sơ nhân sự cần xóa'
            ], 404);
        }

        $empName = $profile->full_name ?: ($profile->user ? $profile->user->name : $profile->employee_code);

        \DB::beginTransaction();
        try {
            $userId = $profile->user_id;

            // Xóa dữ liệu liên quan do import nhầm để dọn dẹp sạch sẽ
            \App\Models\HrmAttendance::where('employee_id', $empId)
                ->when($userId, function ($q) use ($userId) {
                    $q->orWhere('user_id', $userId);
                })->delete();

            \App\Models\HrmShiftAssignment::where('employee_id', $empId)
                ->when($userId, function ($q) use ($userId) {
                    $q->orWhere('user_id', $userId);
                })->delete();

            \App\Models\HrmContract::where('employee_id', $empId)
                ->when($userId, function ($q) use ($userId) {
                    $q->orWhere('user_id', $userId);
                })->delete();

            \App\Models\HrmEmployeeDocument::where('employee_id', $empId)
                ->when($userId, function ($q) use ($userId) {
                    $q->orWhere('user_id', $userId);
                })->delete();

            \App\Models\HrmEmploymentHistory::where('employee_id', $empId)
                ->when($userId, function ($q) use ($userId) {
                    $q->orWhere('user_id', $userId);
                })->delete();

            \App\Models\HrmLeaveRequest::where('employee_id', $empId)
                ->when($userId, function ($q) use ($userId) {
                    $q->orWhere('user_id', $userId);
                })->delete();

            // Xóa profile nhân viên
            $profile->delete();

            \DB::commit();

            return response()->json([
                'status' => 1,
                'message' => "Đã xóa nhân viên '{$empName}' thành công."
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'status' => 0,
                'message' => 'Lỗi khi xóa nhân sự: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy danh sách tài khoản User khả dụng để liên kết với nhân sự HRM
     * Đảm bảo nguyên tắc 1-1: 1 nhân sự ứng với 1 tài khoản và ngược lại
     */
    public function getAvailableUsers(Request $request)
    {
        $employeeId = $request->employee_id;
        $profile = null;
        if ($employeeId) {
            $profile = HrmEmployeeProfile::find($employeeId);
        }

        // Lấy danh sách ID của các user đã được map với profile KHÁC profile hiện tại
        $takenUserIds = HrmEmployeeProfile::whereNotNull('user_id')
            ->when($employeeId, function ($q) use ($employeeId) {
                $q->where('id', '!=', $employeeId);
            })
            ->pluck('user_id')
            ->toArray();

        // Lấy danh sách users khả dụng (chưa bị ai map, hoặc đang map đúng profile này)
        $query = User::whereNull('deleted_at')
            ->whereNotIn('id', $takenUserIds);

        if ($request->filled('keyword')) {
            $kw = trim($request->keyword);
            $query->where(function ($q) use ($kw) {
                $q->where('name', 'like', "%{$kw}%")
                    ->orWhere('hrm_id', 'like', "%{$kw}%")
                    ->orWhere('email', 'like', "%{$kw}%")
                    ->orWhere('phone', 'like', "%{$kw}%");
            });
        }

        $users = $query->orderBy('name', 'asc')->get()->map(function ($u) use ($profile) {
            $isCurrent = $profile && $profile->user_id == $u->id;
            $isEmailMatch = $profile && !empty($profile->email) && strtolower(trim($profile->email)) === strtolower(trim($u->email));
            $isPhoneMatch = $profile && !empty($profile->phone) && !empty($u->phone) && preg_replace('/[^0-9]/', '', $profile->phone) === preg_replace('/[^0-9]/', '', $u->phone);

            $tag = '';
            if ($isCurrent) {
                $tag = ' [ĐANG LIÊN KẾT]';
            } elseif ($isEmailMatch && $isPhoneMatch) {
                $tag = ' (Gợi ý: Trùng Email & SĐT)';
            } elseif ($isEmailMatch) {
                $tag = ' (Gợi ý: Trùng Email)';
            } elseif ($isPhoneMatch) {
                $tag = ' (Gợi ý: Trùng SĐT)';
            }

            return [
                'id' => $u->id,
                'name' => $u->name,
                'hrm_id' => $u->hrm_id,
                'email' => $u->email,
                'phone' => $u->phone,
                'role_name' => $u->role_name,
                'is_current' => $isCurrent,
                'is_suggested' => ($isEmailMatch || $isPhoneMatch),
                'displayName' => "[{$u->hrm_id}] {$u->name} - {$u->email}" . ($u->phone ? " ({$u->phone})" : "") . $tag
            ];
        });

        // Sắp xếp: Đang liên kết hoặc Gợi ý lên đầu
        $sortedUsers = $users->sortByDesc(function ($u) {
            if ($u['is_current']) return 2;
            if ($u['is_suggested']) return 1;
            return 0;
        })->values();

        return response()->json([
            'status' => 1,
            'data' => $sortedUsers
        ]);
    }

    /**
     * Thực hiện liên kết hồ sơ nhân sự với tài khoản User
     * Ràng buộc nghiêm ngặt: 1 nhân sự <-> 1 tài khoản
     */
    public function linkUser(Request $request, $id)
    {
        if (!HrmScopeService::isHrmAdmin(Auth::user())) {
            return response()->json(['status' => 0, 'message' => 'Chỉ Quản trị viên hoặc Quản lý HRM mới có quyền liên kết tài khoản!'], 403);
        }

        $request->validate([
            'user_id' => 'required|integer|exists:users,id'
        ]);

        $profile = HrmEmployeeProfile::findOrFail($id);
        $userId = (int) $request->user_id;

        // KIỂM TRA QUAN HỆ 1-1 CHẶT CHẼ
        $existingProfile = HrmEmployeeProfile::where('user_id', $userId)
            ->where('id', '!=', $id)
            ->first();

        if ($existingProfile) {
            return response()->json([
                'status' => 0,
                'message' => "Tài khoản này đã được liên kết với nhân sự '{$existingProfile->full_name}' (Mã NS: {$existingProfile->employee_code}). Mỗi tài khoản chỉ được liên kết với 1 nhân sự duy nhất."
            ]);
        }

        $user = User::findOrFail($userId);

        $profile->user_id = $userId;
        $profile->save();

        $profile->load('user');

        return response()->json([
            'status' => 1,
            'message' => "Đã liên kết nhân sự '{$profile->full_name}' với tài khoản '{$user->name}' (Mã ĐN: {$user->hrm_id}) thành công.",
            'data' => $profile
        ]);
    }

    /**
     * Hủy liên kết tài khoản User khỏi hồ sơ nhân sự
     */
    public function unlinkUser(Request $request, $id)
    {
        if (!HrmScopeService::isHrmAdmin(Auth::user())) {
            return response()->json(['status' => 0, 'message' => 'Chỉ Quản trị viên hoặc Quản lý HRM mới có quyền hủy liên kết tài khoản!'], 403);
        }

        $profile = HrmEmployeeProfile::findOrFail($id);
        $oldUserId = $profile->user_id;
        $profile->user_id = null;
        $profile->save();

        return response()->json([
            'status' => 1,
            'message' => "Đã hủy liên kết tài khoản khỏi nhân sự '{$profile->full_name}' thành công.",
            'data' => $profile
        ]);
    }
}
