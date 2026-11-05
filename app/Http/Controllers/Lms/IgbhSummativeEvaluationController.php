<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Models\IgbhSummativeResult;
use App\Models\IgbhSummativeResultDetail;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IgbhSummativeEvaluationController extends Controller
{
    public function getResults(Request $request)
    {
        $search = $request->query('search', '');
        $testSeq = $request->query('test_seq');
        $classId = $request->query('class_id');
        $studentId = $request->query('student_id');
        $branchId = $request->query('branch_id');
        $status = $request->query('status');
        $perPage = (int) $request->query('per_page', 20);

        $query = DB::table('igbh_summative_results')
            ->leftJoin('igbh_tests', 'igbh_summative_results.test_seq', '=', 'igbh_tests.test_seq')
            ->leftJoin('students', 'igbh_summative_results.student_id', '=', 'students.id')
            ->leftJoin('classes', 'igbh_summative_results.class_id', '=', 'classes.id')
            ->select(
                'igbh_summative_results.*',
                'igbh_tests.test_nm',
                'students.name as erp_student_name',
                'students.lms_code as erp_student_code',
                'classes.cls_name as erp_class_name'
            );

        $user = Auth::user();
        if ($user) {
            $userBranchIds = $user->getBranchesHasUser();
            if ($userBranchIds) {
                $branchesArr = explode(',', $userBranchIds);
                $query->where(function ($q) use ($branchesArr) {
                    $q->whereIn('igbh_summative_results.branch_id', $branchesArr)
                        ->orWhereIn('students.branch_id', $branchesArr)
                        ->orWhereNull('igbh_summative_results.branch_id');
                });
            }
        }

        if (!empty($branchId)) {
            $query->where(function ($q) use ($branchId) {
                $q->where('igbh_summative_results.branch_id', $branchId)
                    ->orWhere('students.branch_id', $branchId);
            });
        }

        if (!empty($testSeq)) {
            $query->where('igbh_summative_results.test_seq', $testSeq);
        }

        if (!empty($classId)) {
            $query->where('igbh_summative_results.class_id', $classId);
        }

        if (!empty($studentId)) {
            $query->where('igbh_summative_results.student_id', $studentId);
        }

        if (!empty($status)) {
            $query->where('igbh_summative_results.status', $status);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('igbh_summative_results.stu_nm', 'like', "%{$search}%")
                    ->orWhere('students.name', 'like', "%{$search}%")
                    ->orWhere('students.lms_code', 'like', "%{$search}%")
                    ->orWhere('igbh_summative_results.class_nm', 'like', "%{$search}%")
                    ->orWhere('igbh_tests.test_nm', 'like', "%{$search}%");
            });
        }

        $query->orderByDesc('igbh_summative_results.eval_dt')
            ->orderByDesc('igbh_summative_results.id');

        $results = $query->paginate($perPage);

        return response()->json($results);
    }

    public function getReport($id)
    {
        $result = DB::table('igbh_summative_results')->where('id', $id)->first();
        if (!$result) {
            return response()->json(['message' => 'Không tìm thấy kết quả'], 404);
        }

        $student = null;
        if ($result->student_id) {
            $student = u::first("SELECT date_of_birth, gender, lms_code FROM students WHERE id = {$result->student_id}");
        } elseif ($result->stu_seq) {
            $student = u::first("SELECT date_of_birth, gender, lms_code FROM students WHERE lms_code = '{$result->stu_seq}'");
        }

        $result->date_of_birth = $student ? $student->date_of_birth : null;
        $result->gender = $student ? $student->gender : null;

        $testObj = DB::table('lms_tests')->where('test_seq', $result->test_seq)->first();
        if (!$testObj) {
            $testObj = DB::table('igbh_tests')->where('test_seq', $result->test_seq)->first();
        }
        $result->test_nm = $testObj ? ($testObj->name ?? $testObj->test_nm) : null;
        $result->level_cd = $testObj ? $testObj->level_cd : null;

        // Fetch themes (configs)
        $themes = DB::table('igbh_summative_themes')
            ->where('test_seq', $result->test_seq)
            ->orderBy('sort_no')
            ->get();

        // Fetch student's weekly details
        $detailsQuery = DB::table('igbh_weekly_eval_details as d')
            ->join('igbh_weekly_evals as e', 'd.weekly_eval_id', '=', 'e.id')
            ->select('d.*', 'e.each_cd')
            ->where(function ($q) use ($result) {
                if ($result->student_id) {
                    $q->where('d.student_id', $result->student_id)
                        ->orWhere('d.stu_seq', $result->stu_seq);
                } else {
                    $q->where('d.stu_seq', $result->stu_seq);
                }
            })
            ->get();

        $details = $detailsQuery->keyBy('each_cd');

        $reportData = [];
        $totalWorkbook = 0;
        $maxWorkbook = 0;

        $sumAttitude = ['listen' => 0, 'join' => 0, 'express' => 0, 'coop' => 0];
        $sumDetection = ['normal' => 0, 'leadersh' => 0, 'math' => 0, 'creative' => 0];
        $weeksWithData = 0;

        for ($weekNum = 1; $weekNum <= 12; $weekNum++) {
            $theme = $themes->firstWhere('sort_no', $weekNum);
            $eachCd = 'SE' . str_pad($weekNum, 3, '0', STR_PAD_LEFT);
            $detail = $details->get($eachCd);

            $maxWorkbook += $theme ? $theme->theme_point : 3;

            if ($detail) {
                $isAllZero = $detail->workbook == 0 &&
                    $detail->attd_listen == 0 &&
                    $detail->attd_join == 0 &&
                    $detail->attd_express == 0 &&
                    $detail->attd_coop == 0 &&
                    $detail->detect_normal == 0 &&
                    $detail->detect_leadersh == 0 &&
                    $detail->detect_math == 0 &&
                    $detail->detect_creative == 0;

                if ($isAllZero) $detail = null;
            }

            $workbookScore = 0;
            $foundDetail = DB::table('igbh_summative_result_details')
                ->where('summative_result_id', $result->id)
                ->where('sort_no', $weekNum + 5)
                ->first();
            if ($foundDetail && $foundDetail->score !== null && $foundDetail->score !== '') {
                $workbookScore = $foundDetail->score;
            } else {
                $workbookScore = null;
            }

            if ($workbookScore !== null) {
                $totalWorkbook += $workbookScore;
            }

            if ($detail) {
                $sumAttitude['listen'] += $detail->attd_listen;
                $sumAttitude['join'] += $detail->attd_join;
                $sumAttitude['express'] += $detail->attd_express;
                $sumAttitude['coop'] += $detail->attd_coop;

                $sumDetection['normal'] += $detail->detect_normal;
                $sumDetection['leadersh'] += $detail->detect_leadersh;
                $sumDetection['math'] += $detail->detect_math;
                $sumDetection['creative'] += $detail->detect_creative;

                $weeksWithData++;
            }

            $reportData[] = [
                'week' => $weekNum,
                'theme_desc' => $theme ? $theme->theme_desc : '',
                'max_score' => $theme ? $theme->theme_point : 3,
                'score' => $workbookScore,
                'weekly_workbook' => $detail ? $detail->workbook : null,
                'attitude' => $detail ? [
                    'listen' => $detail->attd_listen,
                    'join' => $detail->attd_join,
                    'express' => $detail->attd_express,
                    'coop' => $detail->attd_coop
                ] : null,
                'detection' => $detail ? [
                    'normal' => $detail->detect_normal,
                    'leadersh' => $detail->detect_leadersh,
                    'math' => $detail->detect_math,
                    'creative' => $detail->detect_creative
                ] : null
            ];
        }

        // Radar chart averages
        $avgAttitude = [
            'listen' => $weeksWithData > 0 ? round($sumAttitude['listen'] / $weeksWithData, 1) : 0,
            'join' => $weeksWithData > 0 ? round($sumAttitude['join'] / $weeksWithData, 1) : 0,
            'express' => $weeksWithData > 0 ? round($sumAttitude['express'] / $weeksWithData, 1) : 0,
            'coop' => $weeksWithData > 0 ? round($sumAttitude['coop'] / $weeksWithData, 1) : 0,
        ];

        $avgDetection = [
            'normal' => $weeksWithData > 0 ? round($sumDetection['normal'] / $weeksWithData, 1) : 0,
            'leadersh' => $weeksWithData > 0 ? round($sumDetection['leadersh'] / $weeksWithData, 1) : 0,
            'math' => $weeksWithData > 0 ? round($sumDetection['math'] / $weeksWithData, 1) : 0,
            'creative' => $weeksWithData > 0 ? round($sumDetection['creative'] / $weeksWithData, 1) : 0,
        ];

        $subjectiveDetails = DB::table('igbh_summative_result_details')
            ->where('summative_result_id', $result->id)
            ->where('sort_no', '<=', 5)
            ->orderBy('sort_no')
            ->get();

        $subjectiveAnalysis = $result->subjective_analysis ? json_decode($result->subjective_analysis, true) : null;

        return response()->json([
            'result' => $result,
            'report_data' => $reportData,
            'avg_attitude' => $avgAttitude,
            'avg_detection' => $avgDetection,
            'total_workbook' => $totalWorkbook,
            'max_workbook' => $maxWorkbook,
            'subjective_details' => $subjectiveDetails,
            'subjective_analysis' => $subjectiveAnalysis
        ]);
    }

    public function getFormData($id)
    {
        $result = DB::table('igbh_summative_results')->where('id', $id)->first();
        if (!$result) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $details = DB::table('igbh_summative_result_details')
            ->where('summative_result_id', $id)
            ->get();

        $themes = DB::table('igbh_summative_themes')
            ->where('test_seq', $result->test_seq)
            ->orderBy('sort_no')
            ->get();

        $weeklyEvals = DB::table('igbh_weekly_eval_details as d')
            ->join('igbh_weekly_evals as e', 'd.weekly_eval_id', '=', 'e.id')
            ->where(function ($q) use ($result) {
                if ($result->student_id) {
                    $q->where('d.student_id', $result->student_id)
                        ->orWhere('d.stu_seq', $result->stu_seq);
                } else {
                    $q->where('d.stu_seq', $result->stu_seq);
                }
            })
            ->select('d.*', 'e.each_cd')
            ->get()
            ->keyBy('each_cd');

        return response()->json([
            'result' => $result,
            'details' => $details,
            'themes' => $themes,
            'weekly_evals' => $weeklyEvals
        ]);
    }

    public function saveFormData(Request $request, $id)
    {
        $result = DB::table('igbh_summative_results')->where('id', $id)->first();
        if (!$result) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $request->validate([
            'subjective_questions' => 'nullable|array',
            'workbook_scores' => 'nullable|array',
            'subjective_analysis' => 'nullable|array',
            'eval_dt' => 'nullable|date',
            'status' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            DB::table('igbh_summative_result_details')->where('summative_result_id', $id)->delete();

            $totalScore = 0;
            $insertData = [];

            // 1. Save Subjective Questions (1-5)
            if ($request->has('subjective_questions')) {
                foreach ($request->subjective_questions as $q) {
                    $score = floatval($q['score'] ?? 0);
                    $totalScore += $score;
                    $insertData[] = [
                        'summative_result_id' => $id,
                        'sort_no' => $q['sort_no'],
                        'max_score' => $q['max_score'] ?? 5,
                        'score' => $score,
                        'concept' => floatval($q['concept'] ?? 0),
                        'strategy' => floatval($q['strategy'] ?? 0),
                        'calculation' => floatval($q['calculation'] ?? 0),
                        'expression' => floatval($q['expression'] ?? 0),
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            }

            // 2. Save Workbook Scores (sort_no 6-17)
            if ($request->has('workbook_scores')) {
                foreach ($request->workbook_scores as $wb) {
                    $score = floatval($wb['score'] ?? 0);
                    $totalScore += $score;
                    $insertData[] = [
                        'summative_result_id' => $id,
                        'sort_no' => $wb['sort_no'],
                        'max_score' => $wb['max_score'] ?? 3,
                        'score' => $score,
                        'concept' => null,
                        'strategy' => null,
                        'calculation' => null,
                        'expression' => null,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            }

            if (!empty($insertData)) {
                DB::table('igbh_summative_result_details')->insert($insertData);
            }

            $updateData = [
                'total_score' => round($totalScore, 1),
                'updated_at' => now()
            ];

            if ($request->filled('eval_dt')) $updateData['eval_dt'] = $request->eval_dt;
            if ($request->filled('status')) $updateData['status'] = $request->status;
            if ($request->has('subjective_analysis')) {
                $updateData['subjective_analysis'] = json_encode($request->subjective_analysis, JSON_UNESCAPED_UNICODE);
            }

            DB::table('igbh_summative_results')->where('id', $id)->update($updateData);

            DB::commit();
            return response()->json(['message' => 'Lưu đánh giá tổng kết thành công']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function deleteResult($id)
    {
        DB::table('igbh_summative_result_details')->where('summative_result_id', $id)->delete();
        DB::table('igbh_summative_results')->where('id', $id)->delete();

        return response()->json(['message' => 'Đã xóa đánh giá học sinh']);
    }
}

