<?php

namespace App\Http\Controllers\Hrm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrmJobTitle;
use App\Models\HrmEmployeeProfile;

class JobTitleController extends Controller
{
    public function list(Request $request)
    {
        $titles = HrmJobTitle::with('department')->withCount('employees')->orderBy('id', 'asc')->get();
        return response()->json($titles);
    }
    
    public function add(Request $request) {
        $title = HrmJobTitle::create($request->all());
        return response()->json(['status' => 1, 'message' => 'Thêm chức danh thành công!', 'data' => $title]);
    }
    
    public function show($id) {
        $title = HrmJobTitle::with('department')->withCount('employees')->find($id);
        return response()->json($title);
    }
    
    public function update(Request $request, $id) {
        $title = HrmJobTitle::find($id);
        if ($title) {
            $title->update($request->all());
            return response()->json(['status' => 1, 'message' => 'Cập nhật chức danh thành công!']);
        }
        return response()->json(['status' => 0, 'message' => 'Không tìm thấy chức danh!'], 404);
    }

    public function delete(Request $request, $id = null)
    {
        $id = $id ?: $request->input('id');
        $title = HrmJobTitle::find($id);
        if (!$title) {
            return response()->json(['status' => 0, 'message' => 'Chức danh không tồn tại!'], 404);
        }

        // Check if employees are assigned to this job title
        $empCount = HrmEmployeeProfile::where('job_title_id', $id)->count();
        if ($empCount > 0) {
            return response()->json([
                'status' => 0,
                'message' => "Không thể xóa: Hiện đang có {$empCount} nhân sự giữ chức danh này. Vui lòng chuyển chức danh cho nhân sự trước!"
            ]);
        }

        $title->delete();
        return response()->json(['status' => 1, 'message' => 'Đã xóa chức danh thành công!']);
    }
}
