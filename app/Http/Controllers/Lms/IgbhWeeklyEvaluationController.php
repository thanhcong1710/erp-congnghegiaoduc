<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Models\IgbhWeeklyEval;
use App\Models\IgbhWeeklyEvalDetail;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IgbhWeeklyEvaluationController extends Controller
{
    public function getResults(Request $request)
    {
        $search = $request->query('search', '');
        $testSeq = $request->query('test_seq');
        $classId = $request->query('class_id');
        $branchId = $request->query('branch_id');
        $perPage = (int) $request->query('per_page', 20);

        $query = DB::table('igbh_weekly_evals')
            ->leftJoin('igbh_tests', 'igbh_weekly_evals.test_seq', '=', 'igbh_tests.test_seq')
            ->leftJoin('classes', 'igbh_weekly_evals.class_id', '=', 'classes.id')
            ->select(
                'igbh_weekly_evals.*',
                'igbh_tests.test_nm',
                'classes.cls_name as erp_class_name'
            );

        $user = Auth::user();
        if ($user) {
            $userBranchIds = $user->getBranchesHasUser();
            if ($userBranchIds) {
                $branchesArr = explode(',', $userBranchIds);
                $query->where(function ($q) use ($branchesArr) {
                    $q->whereIn('igbh_weekly_evals.branch_id', $branchesArr)
                        ->orWhereIn('classes.branch_id', $branchesArr)
                        ->orWhereNull('igbh_weekly_evals.branch_id');
                });
            }
        }

        if (!empty($branchId)) {
            $query->where(function ($q) use ($branchId) {
                $q->where('igbh_weekly_evals.branch_id', $branchId)
                    ->orWhere('classes.branch_id', $branchId);
            });
        }

        if (!empty($testSeq)) {
            $query->where('igbh_weekly_evals.test_seq', $testSeq);
        }

        if (!empty($classId)) {
            $query->where('igbh_weekly_evals.class_id', $classId);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('igbh_weekly_evals.class_nm', 'like', "%{$search}%")
                    ->orWhere('classes.cls_name', 'like', "%{$search}%")
                    ->orWhere('igbh_weekly_evals.teacher_nm', 'like', "%{$search}%")
                    ->orWhere('igbh_tests.test_nm', 'like', "%{$search}%");
            });
        }

        $query->orderByDesc('igbh_weekly_evals.eval_ymd')
            ->orderByDesc('igbh_weekly_evals.id');

        $results = $query->paginate($perPage);

        return response()->json($results);
    }

    public function getInitData(Request $request)
    {
        $user = Auth::user();
        $userBranchIds = $user ? $user->getBranchesHasUser() : '';
        $branchCond = $userBranchIds ? "AND c.branch_id IN ({$userBranchIds})" : "";

        // Summative tests used for weekly evaluations
        $tests = DB::table('lms_tests')
            ->where('test_type', 'IG.BH')
            ->where('name', 'like', '%Summative%')
            ->select('test_seq', 'name as test_nm', 'level_cd')
            ->get();

        if ($tests->isEmpty()) {
            $tests = DB::table('igbh_tests')
                ->select('test_seq', 'test_nm', 'level_cd')
                ->get();
        }

        // Active classes from ERP
        $classes = u::query("
            SELECT c.id, c.cls_name, c.branch_id, c.teacher_id, u.name as teacher_name
            FROM classes c
            LEFT JOIN users u ON u.id = c.teacher_id
            WHERE c.status = 1 {$branchCond}
            ORDER BY c.cls_name
        ");

        $weeks = [];
        for ($i = 1; $i <= 12; $i++) {
            $cd = 'SE' . str_pad($i, 3, '0', STR_PAD_LEFT);
            $weeks[] = [
                'each_cd' => $cd,
                'each_cd_nm' => 'Tuần thứ ' . $i
            ];
        }

        return response()->json([
            'tests' => $tests,
            'classes' => $classes,
            'weeks' => $weeks
        ]);
    }

    public function createResult(Request $request)
    {
        $request->validate([
            'test_seq' => 'required',
            'each_cd' => 'required|string',
            'eval_ymd' => 'required|date'
        ]);

        $testSeq = $request->test_seq;
        $classId = $request->class_id;
        $eachCd = $request->each_cd;
        $evalYmd = $request->eval_ymd;

        $classObj = null;
        if ($classId) {
            $classObj = u::first("SELECT id, cls_name, branch_id, teacher_id FROM classes WHERE id = {$classId}");
        }

        $classSeq = $classId ?: (int)$request->class_seq;

        // Check if already exists
        $exists = DB::table('igbh_weekly_evals')
            ->where('test_seq', $testSeq)
            ->where(function($q) use ($classId, $classSeq) {
                if ($classId) $q->where('class_id', $classId);
                else $q->where('class_seq', $classSeq);
            })
            ->where('each_cd', $eachCd)
            ->first();

        if ($exists) {
            return response()->json(['id' => $exists->id], 200);
        }

        $weekNum = (int) str_replace('SE', '', $eachCd);

        $teacherNm = $request->teacher_nm;
        $teacherId = $classObj ? $classObj->teacher_id : null;
        $branchId = $classObj ? $classObj->branch_id : null;

        if ($teacherId && empty($teacherNm)) {
            $teacher = u::first("SELECT name FROM users WHERE id = {$teacherId}");
            if ($teacher) $teacherNm = $teacher->name;
        }

        if (empty($teacherNm) && Auth::user()) {
            $teacherNm = Auth::user()->name;
        }

        $id = DB::table('igbh_weekly_evals')->insertGetId([
            'test_seq' => $testSeq,
            'class_id' => $classId,
            'class_seq' => $classSeq,
            'class_nm' => $classObj ? $classObj->cls_name : $request->class_nm,
            'branch_id' => $branchId,
            'teacher_id' => $teacherId,
            'teacher_nm' => $teacherNm,
            'each_cd' => $eachCd,
            'each_cd_nm' => 'Tuần thứ ' . $weekNum,
            'eval_ymd' => $evalYmd,
            'status' => 'Draft',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['id' => $id], 201);
    }

    public function getExistingWeeks(Request $request)
    {
        $testSeq = $request->input('test_seq');
        $classId = $request->input('class_id');
        $classSeq = $request->input('class_seq');

        if (!$testSeq || (!$classId && !$classSeq)) {
            return response()->json([]);
        }

        $query = DB::table('igbh_weekly_evals')->where('test_seq', $testSeq);
        if ($classId) {
            $query->where('class_id', $classId);
        } else {
            $query->where('class_seq', $classSeq);
        }

        $existing = $query->pluck('each_cd')->toArray();

        return response()->json($existing);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:Draft,Completed']);

        DB::table('igbh_weekly_evals')->where('id', $id)->update([
            'status' => $request->status,
            'updated_at' => now()
        ]);

        return response()->json(['message' => 'Cập nhật trạng thái thành công']);
    }

    public function getResultDetail($id)
    {
        $eval = DB::table('igbh_weekly_evals')->where('id', $id)->first();
        if (!$eval) {
            return response()->json(['message' => 'Không tìm thấy đánh giá'], 404);
        }

        $testObj = DB::table('lms_tests')->where('test_seq', $eval->test_seq)->first();
        if (!$testObj) {
            $testObj = DB::table('igbh_tests')->where('test_seq', $eval->test_seq)->first();
        }
        $eval->test_nm = $testObj ? ($testObj->name ?? $testObj->test_nm) : null;

        $details = DB::table('igbh_weekly_eval_details')
            ->where('weekly_eval_id', $id)
            ->get();

        // Get students from ERP class
        $classId = $eval->class_id ?: $eval->class_seq;
        $allStudents = u::query("
            SELECT DISTINCT s.id as student_id, s.lms_code as stu_seq, s.name as stu_nm
            FROM contracts c
            JOIN students s ON c.student_id = s.id
            WHERE c.class_id = {$classId} AND c.status > 0
            ORDER BY s.name
        ");

        if ($details->isEmpty()) {
            $details = collect($allStudents)->map(function ($s) {
                return (object)[
                    'student_id' => $s->student_id,
                    'stu_seq' => $s->stu_seq ?: $s->student_id,
                    'stu_nm' => $s->stu_nm,
                    'workbook' => 0,
                    'attd_listen' => 5,
                    'attd_join' => 5,
                    'attd_express' => 5,
                    'attd_coop' => 5,
                    'detect_normal' => 5,
                    'detect_leadersh' => 5,
                    'detect_math' => 5,
                    'detect_creative' => 5,
                ];
            });
        }

        return response()->json([
            'evaluation' => $eval,
            'students' => $details,
            'all_students' => $allStudents
        ]);
    }

    public function saveGrade(Request $request, $id)
    {
        $eval = DB::table('igbh_weekly_evals')->where('id', $id)->first();
        if (!$eval) {
            return response()->json(['message' => 'Không tìm thấy đánh giá'], 404);
        }

        $request->validate([
            'students' => 'required|array',
            'eval_ymd' => 'nullable|date',
            'status' => 'nullable|in:Draft,Completed'
        ]);

        $updateData = [];
        if ($request->filled('eval_ymd')) $updateData['eval_ymd'] = $request->eval_ymd;
        if ($request->filled('status')) $updateData['status'] = $request->status;

        if (!empty($updateData)) {
            $updateData['updated_at'] = now();
            DB::table('igbh_weekly_evals')->where('id', $id)->update($updateData);
        }

        DB::beginTransaction();
        try {
            DB::table('igbh_weekly_eval_details')->where('weekly_eval_id', $id)->delete();

            $insertData = [];
            foreach ($request->students as $s) {
                $insertData[] = [
                    'weekly_eval_id' => $id,
                    'student_id' => $s['student_id'] ?? null,
                    'stu_seq' => $s['stu_seq'] ?? ($s['student_id'] ?? 0),
                    'stu_nm' => $s['stu_nm'] ?? '',
                    'workbook' => $s['workbook'] ?? 0,
                    'attd_listen' => $s['attd_listen'] ?? 5,
                    'attd_join' => $s['attd_join'] ?? 5,
                    'attd_express' => $s['attd_express'] ?? 5,
                    'attd_coop' => $s['attd_coop'] ?? 5,
                    'detect_normal' => $s['detect_normal'] ?? 5,
                    'detect_leadersh' => $s['detect_leadersh'] ?? 5,
                    'detect_math' => $s['detect_math'] ?? 5,
                    'detect_creative' => $s['detect_creative'] ?? 5,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($insertData)) {
                DB::table('igbh_weekly_eval_details')->insert($insertData);
            }

            DB::commit();
            return response()->json(['message' => 'Lưu đánh giá tuần thành công']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function deleteResult($id)
    {
        DB::table('igbh_weekly_eval_details')->where('weekly_eval_id', $id)->delete();
        DB::table('igbh_weekly_evals')->where('id', $id)->delete();

        return response()->json(['message' => 'Đã xóa đánh giá tuần']);
    }
}

