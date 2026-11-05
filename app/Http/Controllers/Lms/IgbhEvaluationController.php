<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Models\IgbhStudentResult;
use App\Models\IgbhStudentResultDetail;
use App\Models\IgbhTest;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IgbhEvaluationController extends Controller
{
    /**
     * Get list of IG.BH test results.
     */
    public function getResults(Request $request)
    {
        $search = $request->query('search', '');
        $branchId = $request->query('branch_id');
        $classId = $request->query('class_id');
        $studentId = $request->query('student_id');
        $perPage = (int) $request->query('per_page', 20);

        $query = DB::table('igbh_student_results')
            ->leftJoin('igbh_tests', 'igbh_student_results.test_seq', '=', 'igbh_tests.test_seq')
            ->leftJoin('students', 'igbh_student_results.student_id', '=', 'students.id')
            ->leftJoin('classes', 'igbh_student_results.class_id', '=', 'classes.id')
            ->select(
                'igbh_student_results.*',
                'igbh_tests.test_nm',
                'students.name as erp_student_name',
                'students.lms_code as erp_student_code',
                'classes.cls_name as erp_class_name'
            );

        // Branch permission filter
        $user = Auth::user();
        if ($user) {
            $userBranchIds = $user->getBranchesHasUser();
            if ($userBranchIds) {
                $branchesArr = explode(',', $userBranchIds);
                $query->where(function ($q) use ($branchesArr) {
                    $q->whereIn('igbh_student_results.branch_id', $branchesArr)
                        ->orWhereIn('students.branch_id', $branchesArr)
                        ->orWhereNull('igbh_student_results.branch_id');
                });
            }
        }

        if (!empty($branchId)) {
            $query->where(function ($q) use ($branchId) {
                $q->where('igbh_student_results.branch_id', $branchId)
                    ->orWhere('students.branch_id', $branchId);
            });
        }

        if (!empty($classId)) {
            $query->where('igbh_student_results.class_id', $classId);
        }

        if (!empty($studentId)) {
            $query->where('igbh_student_results.student_id', $studentId);
        }

        // Search logic
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('igbh_student_results.stu_nm', 'like', "%{$search}%")
                    ->orWhere('students.name', 'like', "%{$search}%")
                    ->orWhere('students.lms_code', 'like', "%{$search}%")
                    ->orWhere('igbh_student_results.reg_name', 'like', "%{$search}%")
                    ->orWhere('igbh_tests.test_nm', 'like', "%{$search}%");
            });
        }

        $query->orderByDesc('igbh_student_results.reg_date')
            ->orderByDesc('igbh_student_results.id');

        $results = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $results->items(),
            'pagination' => [
                'current_page' => $results->currentPage(),
                'per_page' => $results->perPage(),
                'total' => $results->total(),
                'last_page' => $results->lastPage(),
                'from' => $results->firstItem(),
                'to' => $results->lastItem()
            ]
        ]);
    }

    /**
     * Get detail of a specific result.
     */
    public function getResultDetail($id)
    {
        $result = DB::table('igbh_student_results')
            ->leftJoin('igbh_tests', 'igbh_student_results.test_seq', '=', 'igbh_tests.test_seq')
            ->leftJoin('students', 'igbh_student_results.student_id', '=', 'students.id')
            ->where('igbh_student_results.id', $id)
            ->select(
                'igbh_student_results.*',
                'igbh_tests.test_nm',
                'students.date_of_birth',
                'students.gender as stu_gender',
                'students.lms_code as erp_student_code'
            )
            ->first();

        if (!$result) {
            return response()->json(['status' => 'error', 'message' => 'Không tìm thấy kết quả'], 404);
        }

        $details = DB::table('igbh_student_result_details')
            ->where('igbh_student_result_id', $id)
            ->get();

        $curriculum = $details->where('question_type', 'curriculum')->values();
        $thinking = $details->where('question_type', 'thinking')->values();

        $config = DB::table('igbh_test_configs')
            ->where('test_seq', $result->test_seq)
            ->first();

        $questions = DB::table('igbh_test_questions')
            ->where('test_seq', $result->test_seq)
            ->orderBy('sort_no')
            ->get();

        $comments = DB::table('igbh_test_comments')
            ->where('test_seq', $result->test_seq)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'general' => $result,
                'curriculum' => $curriculum,
                'thinking' => $thinking,
                'config' => $config ? json_decode($config->sectors, true) : null,
                'questions' => $questions,
                'comments' => $comments
            ]
        ]);
    }

    /**
     * Save/Update scores for a test result.
     */
    public function saveGrade(Request $request, $id)
    {
        $result = DB::table('igbh_student_results')->where('id', $id)->first();
        if (!$result) {
            return response()->json(['status' => 'error', 'message' => 'Không tìm thấy kết quả'], 404);
        }

        $evalDt = $request->input('eval_dt', now()->toDateString());
        $assignedLevel = $request->input('assigned_level');
        $curriculumInputs = $request->input('curriculum', []);
        $thinkingInputs = $request->input('thinking', []);

        DB::beginTransaction();
        try {
            DB::table('igbh_student_result_details')->where('igbh_student_result_id', $id)->delete();

            $insertData = [];
            $correctCount = 0;
            $subjectTotal = 0;
            $thinkingTotal = 0;

            foreach ($curriculumInputs as $item) {
                $qNo = $item['question_no'];
                $ansVal = $item['assigned_score'];
                $unit = $item['unit'] ?? null;
                $seqId = $item['seq_id'] ?? null;

                $correctAnswer = DB::table('igbh_test_questions')
                    ->where('test_seq', $result->test_seq)
                    ->where('question_type', 'curriculum')
                    ->where('sort_no', $qNo)
                    ->value('answer');

                $isCorrect = (!$correctAnswer) ? 'O' : (($ansVal == $correctAnswer) ? 'O' : 'X');

                if ($isCorrect === 'O') {
                    $point = DB::table('igbh_test_questions')
                        ->where('test_seq', $result->test_seq)
                        ->where('question_type', 'curriculum')
                        ->where('sort_no', $qNo)
                        ->value('standard_point') ?? 2;
                    $subjectTotal += $point;
                    $correctCount++;
                }

                $insertData[] = [
                    'igbh_student_result_id' => $id,
                    'question_no' => strval($qNo),
                    'question_type' => 'curriculum',
                    'seq_id' => $seqId,
                    'assigned_score' => $ansVal,
                    'unit' => $unit,
                    'is_correct' => $isCorrect,
                    'max_score' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            foreach ($thinkingInputs as $item) {
                $qNo = $item['question_no'];
                $scoreVal = (int) ($item['assigned_score'] ?? 0);
                $maxScore = $item['max_score'] ?? 5;
                $seqId = $item['seq_id'] ?? null;

                $thinkingTotal += $scoreVal;

                $insertData[] = [
                    'igbh_student_result_id' => $id,
                    'question_no' => strval($qNo),
                    'question_type' => 'thinking',
                    'seq_id' => $seqId,
                    'assigned_score' => strval($scoreVal),
                    'unit' => null,
                    'is_correct' => null,
                    'max_score' => $maxScore,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($insertData)) {
                DB::table('igbh_student_result_details')->insert($insertData);
            }

            $totalScore = $subjectTotal + $thinkingTotal;

            DB::table('igbh_student_results')->where('id', $id)->update([
                'eval_dt' => $evalDt,
                'assigned_level' => $assignedLevel,
                'subject_total' => $subjectTotal,
                'thinking_total' => $thinkingTotal,
                'total_score' => $totalScore,
                'updated_at' => now()
            ]);

            DB::commit();
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get initial data for dialog creation from ERP tables
     */
    public function getInitData(Request $request)
    {
        $user = Auth::user();
        $userBranchIds = $user ? $user->getBranchesHasUser() : '';
        $branchCond = $userBranchIds ? "WHERE id IN ({$userBranchIds})" : "";

        // 1. Branches from ERP
        $branches = u::query("SELECT id, name FROM branches {$branchCond} ORDER BY name");

        // 2. Classes from ERP
        $classBranchCond = $userBranchIds ? "AND c.branch_id IN ({$userBranchIds})" : "";
        $classes = u::query("
            SELECT c.id, c.cls_name, c.branch_id, c.teacher_id, u.name as teacher_name
            FROM classes c
            LEFT JOIN users u ON u.id = c.teacher_id
            WHERE c.status = 1 {$classBranchCond}
            ORDER BY c.cls_name
        ");

        // 3. Teachers from ERP
        $teachers = u::query("SELECT id, name FROM users WHERE status = 1 ORDER BY name");

        // 4. Diagnostic tests
        $tests = DB::table('lms_tests')
            ->where('test_type', 'IG.BH')
            ->where('name', 'not like', '%Summative%')
            ->select('id', 'name as test_nm', 'test_seq', 'level_cd')
            ->orderBy('name')
            ->get();

        if ($tests->isEmpty()) {
            $tests = DB::table('igbh_tests')
                ->select('id', 'test_nm', 'test_seq', 'level_cd')
                ->orderBy('test_nm')
                ->get();
        }

        // 5. Contracts with students
        $contractBranchCond = $userBranchIds ? "AND ct.branch_id IN ({$userBranchIds})" : "";
        $contracts = u::query("
            SELECT ct.id as contract_id, ct.student_id, ct.class_id, s.name as student_name, s.lms_code as student_lms_id
            FROM contracts ct
            INNER JOIN students s ON s.id = ct.student_id
            WHERE ct.status > 0 AND ct.class_id IS NOT NULL {$contractBranchCond}
        ");

        return response()->json([
            'status' => 'success',
            'data' => [
                'branches' => $branches,
                'classes' => $classes,
                'teachers' => $teachers,
                'tests' => $tests,
                'contracts' => $contracts,
            ]
        ]);
    }

    /**
     * Create a new pending IG.BH result stub.
     */
    public function createResult(Request $request)
    {
        $testSeq = $request->input('test_seq');
        $studentId = $request->input('student_id');
        $classId = $request->input('class_id');
        $contractId = $request->input('contract_id');
        $branchId = $request->input('branch_id');
        $teacherId = $request->input('teacher_id');

        $stuName = $request->input('stu_nm');
        $regName = $request->input('reg_name');

        if ($studentId && empty($stuName)) {
            $student = u::first("SELECT name, date_of_birth, branch_id FROM students WHERE id = {$studentId}");
            if ($student) {
                $stuName = $student->name;
                if (empty($branchId)) $branchId = $student->branch_id;
            }
        }

        if ($classId) {
            $class = u::first("SELECT branch_id, teacher_id FROM classes WHERE id = {$classId}");
            if ($class) {
                if (empty($branchId)) $branchId = $class->branch_id;
                if (empty($teacherId)) $teacherId = $class->teacher_id;
            }
        }

        if ($teacherId && empty($regName)) {
            $teacher = u::first("SELECT name FROM users WHERE id = {$teacherId}");
            if ($teacher) $regName = $teacher->name;
        }

        $maxSeq = DB::table('igbh_student_results')->max('result_seq');
        $resultSeq = $maxSeq ? ($maxSeq + 1) : 1001;

        $newId = DB::table('igbh_student_results')->insertGetId([
            'result_seq' => $resultSeq,
            'test_seq' => $testSeq,
            'student_id' => $studentId,
            'class_id' => $classId,
            'contract_id' => $contractId,
            'branch_id' => $branchId,
            'teacher_id' => $teacherId,
            'stu_nm' => $stuName ?: 'Học sinh',
            'stu_birth_dt' => $request->input('stu_birth_dt'),
            'reg_name' => $regName ?: (Auth::user() ? Auth::user()->name : 'Giáo viên'),
            'eval_dt' => $request->input('eval_dt') ?: now()->toDateString(),
            'reg_date' => now(),
            'total_score' => 0,
            'subject_total' => 0,
            'thinking_total' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $newId
            ]
        ]);
    }

    /**
     * Delete result
     */
    public function deleteResult($id)
    {
        DB::table('igbh_student_result_details')->where('igbh_student_result_id', $id)->delete();
        DB::table('igbh_student_results')->where('id', $id)->delete();

        return response()->json(['status' => 'success', 'message' => 'Đã xóa kết quả']);
    }
}

