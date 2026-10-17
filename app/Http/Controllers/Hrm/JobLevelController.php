<?php

namespace App\Http\Controllers\Hrm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrmJobLevel;
use App\Models\HrmEmployeeProfile;

class JobLevelController extends Controller
{
    public function list(Request $request)
    {
        $levels = HrmJobLevel::withCount('employees')->orderBy('id', 'asc')->get();
        return response()->json($levels);
    }
    
    public function add(Request $request) {
        $level = HrmJobLevel::create($request->all());
        return response()->json(['status' => 1, 'message' => 'Thêm cấp bậc thành công!', 'data' => $level]);
    }
    
    public function show($id) {
        $level = HrmJobLevel::withCount('employees')->find($id);
        return response()->json($level);
    }
    
    public function update(Request $request, $id) {
        $level = HrmJobLevel::find($id);
        if ($level) {
            $level->update($request->all());
            return response()->json(['status' => 1, 'message' => 'Cập nhật cấp bậc thành công!']);
        }
        return response()->json(['status' => 0, 'message' => 'Không tìm thấy cấp bậc!'], 404);
    }

    public function delete(Request $request, $id = null)
    {
        $id = $id ?: $request->input('id');
        $level = HrmJobLevel::find($id);
        if (!$level) {
            return response()->json(['status' => 0, 'message' => 'Cấp bậc không tồn tại!'], 404);
        }

        // Check if employees are assigned to this job level
        $empCount = HrmEmployeeProfile::where('job_level_id', $id)->count();
        if ($empCount > 0) {
            return response()->json([
                'status' => 0,
                'message' => "Không thể xóa: Hiện đang có {$empCount} nhân sự được gắn cấp bậc này. Vui lòng chuyển cấp bậc cho nhân sự trước!"
            ]);
        }

        $level->delete();
        return response()->json(['status' => 1, 'message' => 'Đã xóa cấp bậc thành công!']);
    }
}
