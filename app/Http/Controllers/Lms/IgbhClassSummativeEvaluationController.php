<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Models\IgbhClassSummativeEval;
use App\Models\IgbhSummativeResult;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IgbhClassSummativeEvaluationController extends Controller
{
    public function getResults(Request $request)
    {
        $search = $request->query('search', '');
        $testSeq = $request->query('test_seq');
        $classId = $request->query('class_id');
        $branchId = $request->query('branch_id');
        $perPage = (int) $request->query('per_page', 20);

        $query = DB::table('igbh_class_summative_evals')
            ->leftJoin('igbh_tests', 'igbh_class_summative_evals.test_seq', '=', 'igbh_tests.test_seq')
            ->leftJoin('classes', 'igbh_class_summative_evals.class_id', '=', 'classes.id')
            ->select(
                'igbh_class_summative_evals.*',
                'igbh_tests.test_nm',
                'classes.cls_name as erp_class_name'
            );

        $user = Auth::user();
        if ($user) {
            $userBranchIds = $user->getBranchesHasUser();
            if ($userBranchIds) {
                $branchesArr = explode(',', $userBranchIds);
                $query->where(function ($q) use ($branchesArr) {
                    $q->whereIn('igbh_class_summative_evals.branch_id', $branchesArr)
                        ->orWhereIn('classes.branch_id', $branchesArr)
                        ->orWhereNull('igbh_class_summative_evals.branch_id');
                });
            }
        }

        if (!empty($branchId)) {
            $query->where(function ($q) use ($branchId) {
                $q->where('igbh_class_summative_evals.branch_id', $branchId)
                    ->orWhere('classes.branch_id', $branchId);
            });
        }

        if (!empty($testSeq)) {
            $query->where('igbh_class_summative_evals.test_seq', $testSeq);
        }

        if (!empty($classId)) {
            $query->where('igbh_class_summative_evals.class_id', $classId);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('igbh_class_summative_evals.class_nm', 'like', "%{$search}%")
                    ->orWhere('classes.cls_name', 'like', "%{$search}%")
                    ->orWhere('igbh_class_summative_evals.teacher_nm', 'like', "%{$search}%")
                    ->orWhere('igbh_tests.test_nm', 'like', "%{$search}%");
            });
        }

        $query->orderByDesc('igbh_class_summative_evals.eval_ymd')
            ->orderByDesc('igbh_class_summative_evals.id');

        $results = $query->paginate($perPage);

        return response()->json($results);
    }

    public function getInitData(Request $request)
    {
        $user = Auth::user();
        $userBranchIds = $user ? $user->getBranchesHasUser() : '';
        $branchCond = $userBranchIds ? "AND c.branch_id IN ({$userBranchIds})" : "";

        // Summative tests
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

        // Classes from ERP
        $classes = u::query("
            SELECT c.id, c.cls_name, c.branch_id, c.teacher_id, u.name as teacher_name
            FROM classes c
            LEFT JOIN users u ON u.id = c.teacher_id
            WHERE c.status = 1 {$branchCond}
            ORDER BY c.cls_name
        ");

        return response()->json([
            'tests' => $tests,
            'classes' => $classes
        ]);
    }

    public function createResult(Request $request)
    {
        $request->validate([
            'test_seq' => 'required',
            'eval_ymd' => 'required|date'
        ]);

        $testSeq = $request->test_seq;
        $classId = $request->class_id;
        $classSeq = $classId ?: (int)$request->class_seq;
        $evalYmd = $request->eval_ymd;

        $classObj = null;
        if ($classId) {
            $classObj = u::first("SELECT id, cls_name, branch_id, teacher_id FROM classes WHERE id = {$classId}");
        }

        // Check if exists
        $exists = DB::table('igbh_class_summative_evals')
            ->where('test_seq', $testSeq)
            ->where(function($q) use ($classId, $classSeq) {
                if ($classId) $q->where('class_id', $classId);
                else $q->where('class_seq', $classSeq);
            })
            ->first();

        if ($exists) {
            return response()->json(['message' => 'Lớp này đã được khởi tạo đánh giá cho bài kiểm tra này'], 400);
        }

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

        DB::beginTransaction();
        try {
            $id = DB::table('igbh_class_summative_evals')->insertGetId([
                'test_seq' => $testSeq,
                'class_id' => $classId,
                'class_seq' => $classSeq,
                'class_nm' => $classObj ? $classObj->cls_name : $request->class_nm,
                'branch_id' => $branchId,
                'teacher_id' => $teacherId,
                'teacher_nm' => $teacherNm,
                'eval_ymd' => $evalYmd,
                'status' => 'draft',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Auto enroll all students of this class from contracts into igbh_summative_results
            if ($classId) {
                $students = u::query("
                    SELECT DISTINCT s.id as student_id, s.lms_code as stu_seq, s.name as stu_nm, c.id as contract_id
                    FROM contracts c
                    JOIN students s ON c.student_id = s.id
                    WHERE c.class_id = {$classId} AND c.status > 0
                ");

                foreach ($students as $stu) {
                    $stuSeq = $stu->stu_seq ?: $stu->student_id;
                    $stuExists = DB::table('igbh_summative_results')
                        ->where('test_seq', $testSeq)
                        ->where(function($q) use ($stu, $stuSeq) {
                            $q->where('student_id', $stu->student_id)
                                ->orWhere('stu_seq', $stuSeq);
                        })
                        ->exists();

                    if (!$stuExists) {
                        DB::table('igbh_summative_results')->insert([
                            'test_seq' => $testSeq,
                            'student_id' => $stu->student_id,
                            'class_id' => $classId,
                            'contract_id' => $stu->contract_id,
                            'branch_id' => $branchId,
                            'teacher_id' => $teacherId,
                            'stu_seq' => $stuSeq,
                            'stu_nm' => $stu->stu_nm,
                            'class_seq' => $classSeq,
                            'class_nm' => $classObj ? $classObj->cls_name : null,
                            'teacher_nm' => $teacherNm,
                            'eval_dt' => $evalYmd,
                            'status' => 'draft',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json(['id' => $id], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getClassData($id)
    {
        $eval = DB::table('igbh_class_summative_evals')->where('id', $id)->first();
        if (!$eval) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $testObj = DB::table('lms_tests')->where('test_seq', $eval->test_seq)->first();
        if (!$testObj) {
            $testObj = DB::table('igbh_tests')->where('test_seq', $eval->test_seq)->first();
        }
        $eval->test_nm = $testObj ? ($testObj->name ?? $testObj->test_nm) : null;

        $results = DB::table('igbh_summative_results')
            ->where('test_seq', $eval->test_seq)
            ->where(function($q) use ($eval) {
                if ($eval->class_id) $q->where('class_id', $eval->class_id);
                else $q->where('class_seq', $eval->class_seq);
            })
            ->select('id', 'student_id', 'stu_seq', 'stu_nm', 'class_nm', 'total_score', 'status', 'eval_dt')
            ->orderBy('stu_nm')
            ->get();

        return response()->json([
            'evaluation' => $eval,
            'results' => $results
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:draft,completed']);

        DB::beginTransaction();
        try {
            $eval = DB::table('igbh_class_summative_evals')->where('id', $id)->first();
            if (!$eval) {
                return response()->json(['message' => 'Not found'], 404);
            }

            DB::table('igbh_class_summative_evals')->where('id', $id)->update([
                'status' => $request->status,
                'updated_at' => now()
            ]);

            // Sync student status
            DB::table('igbh_summative_results')
                ->where('test_seq', $eval->test_seq)
                ->where(function($q) use ($eval) {
                    if ($eval->class_id) $q->where('class_id', $eval->class_id);
                    else $q->where('class_seq', $eval->class_seq);
                })
                ->update(['status' => $request->status, 'updated_at' => now()]);

            DB::commit();
            return response()->json(['message' => 'Cập nhật trạng thái thành công']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function deleteResult($id)
    {
        $eval = DB::table('igbh_class_summative_evals')->where('id', $id)->first();
        if ($eval) {
            DB::table('igbh_class_summative_evals')->where('id', $id)->delete();
        }

        return response()->json(['message' => 'Đã xóa đánh giá lớp']);
    }
}

