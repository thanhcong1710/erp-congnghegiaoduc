<?php

namespace App\Http\Controllers\Hrm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrmEmployeeProfile;
use App\Models\HrmEmploymentHistory;
use App\Models\HrmEmployeeDocument;
use App\Models\HrmContract;
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

        // Phân quyền dữ liệu nếu là Leader (không phải super-admin hoặc hr)
        $currentUser = Auth::user();
        if ($currentUser) {
            $isHrOrAdmin = $currentUser->isHrmAdmin();
            if (!$isHrOrAdmin && $currentUser->isLeader()) {
                // Leader chỉ xem thành viên team mình hoặc CTV do mình tạo
                $subordinateIds = explode(',', $currentUser->getStaffHasUser());
                $query->where(function ($q) use ($currentUser, $subordinateIds) {
                    $q->whereIn('user_id', $subordinateIds)
                      ->orWhere('created_by_user_id', $currentUser->id);
                });
            }
        }

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
            $query->where(function ($q) use ($kw) {
                $q->where('employee_code', 'like', "%{$kw}%")
                  ->orWhere('phone', 'like', "%{$kw}%")
                  ->orWhereHas('user', function ($sub) use ($kw) {
                      $sub->where('name', 'like', "%{$kw}%")
                          ->orWhere('email', 'like', "%{$kw}%");
                  });
            });
        }

        $employees = $query->orderBy('id', 'desc')->get();
        return response()->json($employees);
    }
    
    public function add(Request $request) 
    {
        $data = $request->all();
        $currentUser = Auth::user();

        // Tự sinh mã nhân sự nếu chưa có (format: CHAM01, CHAM02...)
        if (empty($data['employee_code'])) {
            $data['employee_code'] = HrmEmployeeProfile::generateNextEmployeeCode();
        }

        // Xử lý tạo mới tài khoản User nếu cần
        if (empty($data['user_id']) && !empty($data['user_name'])) {
            $email = !empty($data['email']) ? $data['email'] : strtolower($data['employee_code']) . '@erp.local';
            $newUser = User::create([
                'name' => $data['user_name'],
                'email' => $email,
                'password' => bcrypt('12345678'),
                'status' => 1,
                'manager_id' => $currentUser ? $currentUser->id : 0,
            ]);
            $data['user_id'] = $newUser->id;
        }

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
        $profile = HrmEmployeeProfile::with(['user', 'department', 'jobTitle', 'jobLevel'])->where('user_id', $id)->first();
        if (!$profile) {
            $profile = HrmEmployeeProfile::with(['user', 'department', 'jobTitle', 'jobLevel'])->find($id);
        }
        
        $userId = $profile ? $profile->user_id : $id;
        
        $history = HrmEmploymentHistory::where('user_id', $userId)->orderBy('effective_date', 'desc')->get();
        $documents = HrmEmployeeDocument::where('user_id', $userId)->get();
        $contracts = HrmContract::where('user_id', $userId)->orderBy('start_date', 'desc')->get();
        
        return response()->json([
            'profile' => $profile,
            'history' => $history,
            'documents' => $documents,
            'contracts' => $contracts
        ]);
    }
    
    public function update(Request $request, $id) 
    {
        $profile = HrmEmployeeProfile::find($id);
        if ($profile) {
            $data = $request->all();
            if (isset($data['missing_documents'])) {
                $missingDocs = is_array($data['missing_documents']) ? $data['missing_documents'] : json_decode($data['missing_documents'], true);
                $data['profile_status'] = empty($missingDocs) ? 'full' : 'missing';
            }
            $profile->update($data);
            return response()->json(['status' => 1, 'message' => 'Cập nhật thành công']);
        }
        return response()->json(['status' => 0, 'message' => 'Không tìm thấy hồ sơ'], 404);
    }

    /**
     * Phê duyệt CTV (chỉ HR / Admin)
     */
    public function approveCtv(Request $request, $id)
    {
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
     * Lead hoặc HR đều có thể cập nhật
     */
    public function updateWorkStatus(Request $request, $id)
    {
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
        if ($request->filled('keyword')) {
            $kw = trim($request->keyword);
            $query->where(function ($q) use ($kw) {
                $q->where('employee_code', 'like', "%{$kw}%")
                  ->orWhereHas('user', function ($sub) use ($kw) {
                      $sub->where('name', 'like', "%{$kw}%");
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
                'user_name' => $item->user ? $item->user->name : 'N/A',
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
        $query = HrmContract::with(['user']);
        if ($request->filled('keyword')) {
            $kw = trim($request->keyword);
            $query->where('contract_number', 'like', "%{$kw}%")
                  ->orWhereHas('user', function ($q) use ($kw) {
                      $q->where('name', 'like', "%{$kw}%");
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
        if (count($paths) > 0) {
            foreach ($paths as $index => $path) {
                $docTitle = count($paths) > 1 ? $request->title . ' - Phần ' . ($index + 1) : $request->title;
                $docs[] = HrmEmployeeDocument::create([
                    'user_id' => $request->user_id,
                    'title' => $docTitle,
                    'document_type' => $request->document_type,
                    'expiry_date' => $request->expiry_date,
                    'file_path' => url($path)
                ]);
            }
        } else {
            $docs[] = HrmEmployeeDocument::create($request->except('attachments'));
        }

        return response()->json(['status' => 1, 'message' => 'Đã tải lên tài liệu thành công', 'data' => $docs]);
    }
}
