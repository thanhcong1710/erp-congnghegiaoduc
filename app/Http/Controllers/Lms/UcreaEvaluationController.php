<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Models\UcreaStudentResult;
use App\Models\UcreaStudentResultDetail;
use App\Models\UcreaTest;
use App\Providers\UtilityServiceProvider as u;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UcreaEvaluationController extends Controller
{
    /**
     * Get list of UCREA test results.
     */
    public function getResults(Request $request)
    {
        $status = $request->query('status', 'completed'); // 'pending' or 'completed'
        $search = $request->query('search', '');
        $branchId = $request->query('branch_id');
        $classId = $request->query('class_id');
        $studentId = $request->query('student_id');
        $perPage = (int) $request->query('per_page', 20);

        $query = DB::table('ucrea_student_results')
            ->leftJoin('ucrea_tests', function ($join) {
                $join->on('ucrea_student_results.test_cd', '=', 'ucrea_tests.test_cd')
                    ->on('ucrea_student_results.level_cd', '=', 'ucrea_tests.level_cd')
                    ->on('ucrea_student_results.test_seq', '=', 'ucrea_tests.test_seq');
            })
            ->leftJoin('students', function ($join) {
                $join->on('ucrea_student_results.student_id', '=', 'students.id');
            })
            ->leftJoin('classes', function ($join) {
                $join->on('ucrea_student_results.class_id', '=', 'classes.id');
            })
            ->select(
                'ucrea_student_results.*',
                'ucrea_tests.test_nm',
                'students.name as erp_student_name',
                'students.lms_code as erp_student_code',
                'classes.cls_name as erp_class_name'
            );

        // Filter by status
        if ($status === 'completed') {
            $query->whereIn('ucrea_student_results.result_cd', ['IS002', 'IS003', 'IS004']);
        } else {
            $query->whereNotIn('ucrea_student_results.result_cd', ['IS002', 'IS003', 'IS004']);
        }

        // Branch filter
        $user = Auth::user();
        if ($user) {
            $userBranchIds = $user->getBranchesHasUser();
            if ($userBranchIds) {
                $branchesArr = explode(',', $userBranchIds);
                $query->where(function ($q) use ($branchesArr) {
                    $q->whereIn('ucrea_student_results.branch_id', $branchesArr)
                        ->orWhereIn('students.branch_id', $branchesArr)
                        ->orWhereNull('ucrea_student_results.branch_id');
                });
            }
        }

        if (!empty($branchId)) {
            $query->where(function ($q) use ($branchId) {
                $q->where('ucrea_student_results.branch_id', $branchId)
                    ->orWhere('students.branch_id', $branchId);
            });
        }

        if (!empty($classId)) {
            $query->where('ucrea_student_results.class_id', $classId);
        }

        if (!empty($studentId)) {
            $query->where('ucrea_student_results.student_id', $studentId);
        }

        // Search logic
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('ucrea_student_results.stu_nm', 'like', "%{$search}%")
                    ->orWhere('students.name', 'like', "%{$search}%")
                    ->orWhere('students.lms_code', 'like', "%{$search}%")
                    ->orWhere('ucrea_student_results.memb_nm', 'like', "%{$search}%")
                    ->orWhere('ucrea_tests.test_nm', 'like', "%{$search}%");
            });
        }

        $query->orderByDesc('ucrea_student_results.eval_dt')
            ->orderByDesc('ucrea_student_results.id');

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
        $result = DB::table('ucrea_student_results')
            ->leftJoin('ucrea_tests', function ($join) {
                $join->on('ucrea_student_results.test_cd', '=', 'ucrea_tests.test_cd')
                    ->on('ucrea_student_results.level_cd', '=', 'ucrea_tests.level_cd')
                    ->on('ucrea_student_results.test_seq', '=', 'ucrea_tests.test_seq');
            })
            ->leftJoin('students', function ($join) {
                $join->on('ucrea_student_results.student_id', '=', 'students.id');
            })
            ->where('ucrea_student_results.id', $id)
            ->select(
                'ucrea_student_results.*',
                'ucrea_tests.test_nm',
                'ucrea_tests.level_cd_nm',
                'students.date_of_birth',
                'students.gender as stu_gender',
                'students.lms_code as erp_student_code'
            )
            ->first();

        if (!$result) {
            return response()->json(['status' => 'error', 'message' => 'Không tìm thấy kết quả'], 404);
        }

        if (!empty($result->date_of_birth)) {
            try {
                $evalDate = $result->eval_dt ? Carbon::parse($result->eval_dt) : now();
                $birthDate = Carbon::parse($result->date_of_birth);
                $result->age_nm = $birthDate->diffInYears($evalDate) . ' Tuổi';

                if ($result->stu_gender === 'M' || $result->stu_gender == 1) {
                    $result->gender_nm = 'Nam';
                } elseif ($result->stu_gender === 'F' || $result->stu_gender == 0) {
                    $result->gender_nm = 'Nữ';
                }
            } catch (\Exception $e) {}
        }

        $details = DB::table('ucrea_student_result_details')
            ->where('ucrea_student_result_id', $id)
            ->get();

        $relatedResults = DB::table('ucrea_student_results')
            ->leftJoin('ucrea_tests', function ($join) {
                $join->on('ucrea_student_results.test_cd', '=', 'ucrea_tests.test_cd')
                    ->on('ucrea_student_results.level_cd', '=', 'ucrea_tests.level_cd')
                    ->on('ucrea_student_results.test_seq', '=', 'ucrea_tests.test_seq');
            })
            ->where(function ($q) use ($result) {
                if ($result->student_id) {
                    $q->where('ucrea_student_results.student_id', $result->student_id)
                        ->orWhere('ucrea_student_results.stu_nm', $result->stu_nm);
                } else {
                    $q->where('ucrea_student_results.stu_nm', $result->stu_nm);
                }
            })
            ->where('ucrea_student_results.level_cd', $result->level_cd)
            ->whereIn('ucrea_student_results.result_cd', ['IS002', 'IS003', 'IS004'])
            ->select('ucrea_student_results.id', 'ucrea_student_results.test_cd', 'ucrea_student_results.report_data', 'ucrea_tests.test_nm')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'general' => $result,
                'rubrics' => $details,
                'related_results' => $relatedResults
            ]
        ]);
    }

    /**
     * Save newly graded test from frontend
     */
    public function saveGrade(Request $request, $id)
    {
        $result = DB::table('ucrea_student_results')->where('id', $id)->first();
        if (!$result) {
            return response()->json(['status' => 'error', 'message' => 'Không tìm thấy kết quả'], 404);
        }

        $rubrics = $request->input('rubrics', []);

        DB::beginTransaction();
        try {
            DB::table('ucrea_student_result_details')->where('ucrea_student_result_id', $id)->delete();

            $insertData = [];
            $scoresByKey = [];
            $skillsGradeList = [];

            $scoresByOption = [100, 85, 55, 30, 10];
            $gradesByOption = ['S', 'A', 'B', 'C', 'C'];

            foreach ($rubrics as $index => $r) {
                $scoreIndex = array_search($r['score'], $r['options']);
                if ($scoreIndex === false) {
                    $grade = 'B';
                    $points = 70;
                } else {
                    $grade = $gradesByOption[$scoreIndex] ?? 'B';
                    $points = $scoresByOption[$scoreIndex] ?? 70;
                }

                $scoresByKey[$r['key']] = $points;

                $insertData[] = [
                    'ucrea_student_result_id' => $id,
                    'question_no' => strval($index + 1),
                    'main_category' => $r['main'],
                    'sub_category' => $r['sub'],
                    'rubric_name' => $r['name'],
                    'assigned_score' => $r['score'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $skillsGradeList[] = [
                    'skill' => $r['skillName'] ?? $r['name'],
                    'grade' => $grade
                ];
            }

            if (!empty($insertData)) {
                DB::table('ucrea_student_result_details')->insert($insertData);
            }

            $nameMap = ['TT001' => 'Đầu kỳ', 'TT002' => 'Giữa kỳ', 'TT003' => 'Cuối kỳ'];
            $reportDataName = $nameMap[$result->test_cd] ?? 'Đầu kỳ';

            $isL1L2 = in_array($result->level_cd, ['L1', 'L2']);

            $avgCoBan = (($scoresByKey['sk11'] ?? 70) + ($scoresByKey['sk12'] ?? 70) + ($scoresByKey['sk13'] ?? 70)) / 3;

            if ($isL1L2) {
                $avgToanHoc = (($scoresByKey['kn11'] ?? 70) + ($scoresByKey['kn12'] ?? 70) + ($scoresByKey['kn13'] ?? 70) + ($scoresByKey['kn14'] ?? 70)) / 4;
            } else {
                $avgToanHoc = (($scoresByKey['kn11'] ?? 70) + ($scoresByKey['kn12'] ?? 70) + ($scoresByKey['kn13'] ?? 70) + ($scoresByKey['kn14'] ?? 70) + ($scoresByKey['kn15'] ?? 70)) / 5;
            }

            if ($isL1L2) {
                $avgLogic = (($scoresByKey['sk21'] ?? 70) + ($scoresByKey['sk22'] ?? 70) + ($scoresByKey['sk23'] ?? 70)) / 3;
            } else {
                $avgLogic = (($scoresByKey['sk21'] ?? 70) + ($scoresByKey['sk22'] ?? 70) + ($scoresByKey['sk23'] ?? 70) + ($scoresByKey['sk24'] ?? 70)) / 4;
            }

            $avgSangTao = (($scoresByKey['sk31'] ?? 70) + ($scoresByKey['sk32'] ?? 70) + ($scoresByKey['sk33'] ?? 70) + ($scoresByKey['sk34'] ?? 70)) / 4;

            $getGradeFromScore = function ($avgScore) {
                if ($avgScore >= 91) return 'S';
                if ($avgScore >= 71) return 'A';
                if ($avgScore >= 41) return 'B';
                return 'C';
            };

            $gradeCoBan = $getGradeFromScore($avgCoBan);
            $gradeToanHoc = $getGradeFromScore($avgToanHoc);
            $gradeLogic = $getGradeFromScore($avgLogic);
            $gradeSangTao = $getGradeFromScore($avgSangTao);

            $skillsGradeList[] = ['skill' => 'Tư duy cơ bản', 'grade' => $gradeCoBan];
            $skillsGradeList[] = ['skill' => 'Tư duy toán học', 'grade' => $gradeToanHoc];
            $skillsGradeList[] = ['skill' => 'Tư duy logic', 'grade' => $gradeLogic];
            $skillsGradeList[] = ['skill' => 'Tư duy sáng tạo', 'grade' => $gradeSangTao];

            $reportDataObj = [
                'name' => $reportDataName,
                'kn11' => strval($scoresByKey['kn11'] ?? 70),
                'kn12' => strval($scoresByKey['kn12'] ?? 70),
                'kn13' => strval($scoresByKey['kn13'] ?? 70),
                'kn14' => strval($scoresByKey['kn14'] ?? 70),
                'kn15' => strval($scoresByKey['kn15'] ?? 70),
                'kn21' => '0',
                'kn22' => '0',
                'kn23' => '0',
                'kn24' => '0',
                'sk11' => strval($scoresByKey['sk11'] ?? 70),
                'sk12' => strval($scoresByKey['sk12'] ?? 70),
                'sk13' => strval($scoresByKey['sk13'] ?? 70),
                'sk21' => strval($scoresByKey['sk21'] ?? 70),
                'sk22' => strval($scoresByKey['sk22'] ?? 70),
                'sk23' => strval($scoresByKey['sk23'] ?? 70),
                'sk24' => strval($scoresByKey['sk24'] ?? 70),
                'sk31' => strval($scoresByKey['sk31'] ?? 70),
                'sk32' => strval($scoresByKey['sk32'] ?? 70),
                'sk33' => strval($scoresByKey['sk33'] ?? 70),
                'sk34' => strval($scoresByKey['sk34'] ?? 70),
            ];

            DB::table('ucrea_student_results')->where('id', $id)->update([
                'result_cd' => 'IS002',
                'result_cd_nm' => 'Nhập nhận xét',
                'eval_dt' => now()->toDateString(),
                'report_data' => json_encode($reportDataObj, JSON_UNESCAPED_UNICODE),
                'skills_grade' => json_encode($skillsGradeList, JSON_UNESCAPED_UNICODE),
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
     * Get initial data (branches, classes, teachers, tests, students) using ERP Core tables
     */
    public function getInitData(Request $request)
    {
        $user = Auth::user();
        $userBranchIds = $user ? $user->getBranchesHasUser() : '';
        $branchCond = $userBranchIds ? "WHERE id IN ({$userBranchIds})" : "";

        // 1. Branches from ERP
        $branches = u::query("SELECT id, name FROM branches {$branchCond} ORDER BY name");

        // 2. Classes from ERP with Teacher
        $classBranchCond = $userBranchIds ? "AND c.branch_id IN ({$userBranchIds})" : "";
        $classes = u::query("
            SELECT c.id, c.cls_name, c.branch_id, c.teacher_id, u.name as teacher_name
            FROM classes c
            LEFT JOIN users u ON u.id = c.teacher_id
            WHERE c.status = 1 {$classBranchCond}
            ORDER BY c.cls_name
        ");

        // 3. Teachers from ERP
        $teachers = u::query("
            SELECT id, name FROM users 
            WHERE status = 1 
            ORDER BY name
        ");

        // 4. Tests
        $tests = DB::table('ucrea_tests')
            ->select('id', 'test_cd', 'test_nm', 'level_cd', 'level_cd_nm', 'test_seq')
            ->orderBy('test_nm')
            ->get();

        // 5. Existing results
        $existingResults = DB::table('ucrea_student_results')
            ->select('student_id', 'stu_nm', 'class_id', 'class_nm', 'test_cd', 'level_cd', 'test_seq')
            ->get();

        // 6. Contracts with students enrolled in classes
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
                'existing_results' => $existingResults,
                'contracts' => $contracts,
            ]
        ]);
    }

    /**
     * Create a new pending evaluation record
     */
    public function createResult(Request $request)
    {
        $test = DB::table('ucrea_tests')->where('id', $request->input('test_id'))->first();
        if (!$test) {
            return response()->json(['status' => 'error', 'message' => 'Không tìm thấy bài test được chọn'], 404);
        }

        $studentId = $request->input('student_id');
        $classId = $request->input('class_id');
        $contractId = $request->input('contract_id');
        $branchId = $request->input('branch_id');
        $teacherId = $request->input('teacher_id');

        $stuName = $request->input('stu_nm');
        $classNm = $request->input('class_nm');
        $membNm = $request->input('memb_nm');

        // Auto resolve missing info from ERP tables
        if ($studentId && empty($stuName)) {
            $student = u::first("SELECT name, branch_id FROM students WHERE id = {$studentId}");
            if ($student) {
                $stuName = $student->name;
                if (empty($branchId)) $branchId = $student->branch_id;
            }
        }

        if ($classId && empty($classNm)) {
            $class = u::first("SELECT cls_name, branch_id, teacher_id FROM classes WHERE id = {$classId}");
            if ($class) {
                $classNm = $class->cls_name;
                if (empty($branchId)) $branchId = $class->branch_id;
                if (empty($teacherId)) $teacherId = $class->teacher_id;
            }
        }

        if ($teacherId && empty($membNm)) {
            $teacher = u::first("SELECT name FROM users WHERE id = {$teacherId}");
            if ($teacher) $membNm = $teacher->name;
        }

        $maxSeq = DB::table('ucrea_student_results')->max('result_seq');
        $resultSeq = $maxSeq ? ($maxSeq + 1) : 1001;

        $newId = DB::table('ucrea_student_results')->insertGetId([
            'test_cd' => $test->test_cd,
            'level_cd' => $test->level_cd,
            'test_seq' => $test->test_seq,
            'result_seq' => $resultSeq,
            'student_id' => $studentId,
            'class_id' => $classId,
            'contract_id' => $contractId,
            'branch_id' => $branchId,
            'teacher_id' => $teacherId,
            'memb_nm' => $membNm,
            'stu_nm' => $stuName ?: 'Học sinh',
            'class_nm' => $classNm,
            'eval_dt' => $request->input('eval_dt') ?: now()->toDateString(),
            'result_cd' => 'IS001',
            'result_cd_nm' => 'Chưa nhập',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $newId
            ]
        ]);
    }

    /**
     * Delete an evaluation record
     */
    public function deleteResult($id)
    {
        DB::table('ucrea_student_result_details')->where('ucrea_student_result_id', $id)->delete();
        DB::table('ucrea_student_results')->where('id', $id)->delete();

        return response()->json(['status' => 'success', 'message' => 'Đã xóa bài đánh giá']);
    }
}

