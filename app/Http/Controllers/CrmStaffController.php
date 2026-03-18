<?php

namespace App\Http\Controllers;

use App\Enums\SystemCode;
use App\Http\Controllers\Controller;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CrmStaffController extends Controller
{
    /**
     * Lấy danh sách nhân viên cấp dưới (Trưởng nhóm kinh doanh quản lý)
     */
    public function list(Request $request)
    {
        $keyword = $request->keyword ?? '';
        // Chỉ lấy nhân viên do người đang đăng nhập quản lý
        $cond = " u.manager_id = " . Auth::user()->id . " AND u.deleted_at IS NULL ";
        if ($keyword !== '') {
            $cond .= " AND (u.name LIKE '%$keyword%' OR u.hrm_id LIKE '%$keyword%')";
        }
        
        $pagination = (object)$request->pagination;
        $page = isset($pagination->cpage) ? (int)$pagination->cpage : 1;
        $limit = isset($pagination->limit) ? (int)$pagination->limit : 20;
        $offset = ($page - 1) * $limit;
        
        $total = DB::selectOne("SELECT count(id) AS total FROM users AS u WHERE $cond");
        $list = DB::select("SELECT u.* FROM users AS u WHERE $cond ORDER BY u.id DESC LIMIT $offset, $limit");
        
        $data = u::makingPagination($list, $total->total, $page, $limit);
        return response()->json($data);
    }

    /**
     * Thêm mới nhân viên kinh doanh
     */
    public function add(Request $request)
    {
        // 1. Tự động sinh mã hrm_id CTxxx
        // Lấy hrm_id lớn nhất bắt đầu bằng CT
        $last_user = DB::table('users')->where('hrm_id', 'LIKE', 'CT%')->orderBy('hrm_id', 'desc')->first();
        if ($last_user) {
            $number = (int)str_replace('CT', '', $last_user->hrm_id);
            $hrm_id = 'CT' . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $hrm_id = 'CT020'; // Khởi tạo nếu chưa có
        }

        // 2. Kiểm tra nếu hrm_id này đã tồn tại (phòng hờ)
        if (DB::table('users')->where('hrm_id', $hrm_id)->exists()) {
            return response()->json([
                'status' => 0,
                'message' => 'Hệ thống đang bận sinh mã hrm_id, vui lòng thử lại.',
            ]);
        }

        // 3. Mặc định mật khẩu nếu không nhập (ví dụ: 12345678@)
        $password = Hash::make('12345678@');

        // 4. Lưu dữ liệu
        // - manager_hrm_id: lấy từ tài khoản Trưởng nhóm đang tạo (Auth::user())
        // - role_name, role_id: mặc định 'Nhân viên kinh doanh' (Role ID = 68 theo SystemCode)
        // - branch_id, branch_name: lấy từ Trưởng nhóm
        $user_id = u::insertSimpleRow([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email ?: null, // Bỏ validate email bắt buộc
            'hrm_id' => $hrm_id,
            'password' => $password,
            'status' => 1,
            'manager_hrm_id' => Auth::user()->hrm_id,
            'manager_id' => Auth::user()->id,
            'role_name' => 'Nhân viên kinh doanh',
            'branch_id' => Auth::user()->branch_id,
            'branch_name' => Auth::user()->branch_name,
            'created_at' => date('Y-m-d H:i:s'),
            'creator_id' => Auth::user()->id
        ], 'users');

        if ($user_id) {
            // Gán quyền mặc định (Role ID 68)
            u::insertSimpleRow([
                'role_id' => 68,
                'user_id' => $user_id,
            ], 'role_has_user');

            // Gán trung tâm mặc định (theo Trưởng nhóm)
            if (Auth::user()->branch_id) {
                u::insertSimpleRow([
                    'branch_id' => Auth::user()->branch_id,
                    'user_id' => $user_id,
                ], 'branch_has_user');
            }

            return response()->json([
                'status' => 1,
                'message' => 'Thêm mới nhân viên Sales thành công: ' . $hrm_id,
                'hrm_id' => $hrm_id
            ]);
        }

        return response()->json(['status' => 0, 'message' => 'Thao tác thêm mới thất bại.']);
    }

    /**
     * Chỉnh sửa thông tin nhân viên
     */
    public function update(Request $request, $id)
    {
        $data = [
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email ?: null,
            'updated_at' => date('Y-m-d H:i:s'),
            'updator_id' => Auth::user()->id
        ];
        
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        // Đảm bảo chỉ được cập nhật nhân viên do mình quản lý
        $check = DB::table('users')->where('id', $id)->where('manager_id', Auth::user()->id)->exists();
        if (!$check) {
            return response()->json(['status' => 0, 'message' => 'Bạn không có quyền chỉnh sửa nhân viên này.']);
        }

        u::updateSimpleRow($data, ['id' => $id], 'users');

        return response()->json(['status' => 1, 'message' => 'Cập nhật thông tin thành công']);
    }

    /**
     * Lấy thông tin chi tiết nhân viên
     */
    public function show($id)
    {
        $user = DB::table('users')->where('id', $id)->where('manager_id', Auth::user()->id)->first();
        if ($user) {
            // Ẩn mật khẩu khi gửi lên frontend
            unset($user->password);
            return response()->json([
                'status' => 1,
                'data' => $user
            ]);
        }
        return response()->json(['status' => 0, 'message' => 'Dữ liệu không tồn tại hoặc bạn không có quyền xem.']);
    }
}
