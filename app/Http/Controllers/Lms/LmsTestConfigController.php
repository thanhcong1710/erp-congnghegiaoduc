<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LmsTestConfigController extends Controller
{
    /**
     * List all tests (UCREA, IG.BH)
     */
    public function index(Request $request)
    {
        $type = $request->query('type'); // UCREA or IG.BH
        $search = $request->query('search');

        $query = DB::table('lms_tests');

        if ($type) {
            $query->where('test_type', $type);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('level_cd', 'like', "%{$search}%")
                    ->orWhere('test_cd', 'like', "%{$search}%");
            });
        }

        $limit = (int) $request->query('per_page', 20);
        if (!in_array($limit, [20, 50, 100])) {
            $limit = 20;
        }

        $tests = $query->orderBy('id', 'desc')->paginate($limit);

        return response()->json([
            'status' => 'success',
            'data' => $tests->items(),
            'pagination' => [
                'total' => $tests->total(),
                'per_page' => $tests->perPage(),
                'current_page' => $tests->currentPage(),
                'last_page' => $tests->lastPage(),
                'from' => $tests->firstItem(),
                'to' => $tests->lastItem()
            ]
        ]);
    }

    /**
     * Get test questions and configuration for IG.BH
     */
    public function getConfig(Request $request, $testSeq)
    {
        $test = DB::table('lms_tests')->where('test_seq', $testSeq)->where('test_type', 'IG.BH')->first();
        if (!$test) {
            $test = DB::table('igbh_tests')->where('test_seq', $testSeq)->first();
            if ($test) {
                $test->name = $test->test_nm;
            }
        }

        if (!$test) {
            return response()->json(['message' => 'Không tìm thấy bài test'], 404);
        }

        $type = (stripos($test->name ?? '', 'summative') !== false) ? 'summative' : 'diagnostic';

        if ($type === 'summative') {
            $themes = DB::table('igbh_summative_themes')->where('test_seq', $testSeq)->orderBy('sort_no')->get();
            return response()->json([
                'type' => 'summative',
                'test' => $test,
                'themes' => $themes
            ]);
        } else {
            $configRow = DB::table('igbh_test_configs')->where('test_seq', $testSeq)->first();
            $questions = DB::table('igbh_test_questions')->where('test_seq', $testSeq)->orderBy('sort_no')->get();
            foreach ($questions as $q) {
                if ($q->areas) {
                    $decoded = json_decode($q->areas, true);
                    $q->areas = is_array($decoded) ? $decoded : [];
                } else {
                    $q->areas = [];
                }
            }
            $comments = DB::table('igbh_test_comments')->where('test_seq', $testSeq)->get();

            $config = $configRow ? json_decode($configRow->sectors, true) : [
                'A' => '', 'B' => '', 'C' => '', 'D' => '', 'E' => ''
            ];

            return response()->json([
                'type' => 'diagnostic',
                'test' => $test,
                'config' => $config,
                'questions' => $questions,
                'comments' => $comments
            ]);
        }
    }

    /**
     * Update configuration for IG.BH
     */
    public function updateConfig(Request $request, $testSeq)
    {
        $type = $request->input('type');

        if ($type === 'summative') {
            $themes = $request->input('themes', []);
            DB::beginTransaction();
            try {
                DB::table('igbh_summative_themes')->where('test_seq', $testSeq)->delete();
                $insertData = [];
                foreach ($themes as $index => $theme) {
                    $insertData[] = [
                        'test_seq' => $testSeq,
                        'sort_no' => $index + 1,
                        'theme_desc' => $theme['theme_desc'] ?? '',
                        'theme_point' => $theme['theme_point'] ?? 0,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                if (count($insertData) > 0) {
                    DB::table('igbh_summative_themes')->insert($insertData);
                }
                DB::commit();
                return response()->json(['message' => 'Cập nhật cấu hình thành công']);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['message' => $e->getMessage()], 500);
            }
        } else {
            $config = $request->input('config', []);
            $questions = $request->input('questions', []);
            $comments = $request->input('comments', []);

            DB::beginTransaction();
            try {
                $existingConfig = DB::table('igbh_test_configs')->where('test_seq', $testSeq)->first();
                if ($existingConfig) {
                    DB::table('igbh_test_configs')->where('test_seq', $testSeq)->update([
                        'sectors' => json_encode($config, JSON_UNESCAPED_UNICODE),
                        'updated_at' => now()
                    ]);
                } else {
                    DB::table('igbh_test_configs')->insert([
                        'test_seq' => $testSeq,
                        'sectors' => json_encode($config, JSON_UNESCAPED_UNICODE),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }

                DB::table('igbh_test_questions')->where('test_seq', $testSeq)->delete();
                $insertQuestions = [];
                foreach ($questions as $index => $q) {
                    $insertQuestions[] = [
                        'test_seq' => $testSeq,
                        'question_type' => $q['question_type'] ?? 'curriculum',
                        'sort_no' => $index + 1,
                        'sector' => $q['sector'] ?? null,
                        'type_cd' => $q['type_cd'] ?? null,
                        'answer' => $q['answer'] ?? null,
                        'areas' => is_array($q['areas'] ?? null) ? json_encode($q['areas']) : $q['areas'],
                        'difficulty' => $q['difficulty'] ?? null,
                        'standard_point' => $q['standard_point'] ?? 0,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                if (count($insertQuestions) > 0) {
                    DB::table('igbh_test_questions')->insert($insertQuestions);
                }

                DB::table('igbh_test_comments')->where('test_seq', $testSeq)->delete();
                $insertComments = [];
                foreach ($comments as $c) {
                    $insertComments[] = [
                        'test_seq' => $testSeq,
                        'comment_type' => $c['comment_type'] ?? 'total',
                        'condition_value' => $c['condition_value'] ?? '',
                        'good_comment' => $c['good_comment'] ?? '',
                        'weak_comment' => $c['weak_comment'] ?? '',
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                if (count($insertComments) > 0) {
                    DB::table('igbh_test_comments')->insert($insertComments);
                }

                DB::commit();
                return response()->json(['message' => 'Cập nhật cấu hình thành công']);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['message' => $e->getMessage()], 500);
            }
        }
    }
}

