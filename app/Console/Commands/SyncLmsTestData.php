<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PDO;

class SyncLmsTestData extends Command
{
    protected $signature = 'lms:sync-test-data {--only-catalog : Only sync master test catalogs}';
    protected $description = 'Migrate LMS master test data and results into stemkey DB with ERP student/class mappings';

    public function handle()
    {
        $this->info("Connecting to source local LMS DB (127.0.0.1:33066/lms)...");
        try {
            $srcPdo = new PDO('mysql:host=127.0.0.1;port=33066;dbname=lms;charset=utf8mb4', 'root', 'secret', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (\Exception $e) {
            $this->error("Cannot connect to local LMS: " . $e->getMessage());
            return 1;
        }

        $this->info("Connected! Starting migration to DB stemkey...");

        // 1. Sync Master Catalogs
        $this->syncMasterCatalog($srcPdo, 'lms_tests', ['test_type', 'test_cd', 'level_cd', 'test_seq', 'name', 'pdf_url', 'local_pdf_path', 'status', 'created_at', 'updated_at']);
        $this->syncMasterCatalog($srcPdo, 'ucrea_tests', ['test_cd', 'test_nm', 'level_cd', 'level_cd_nm', 'test_seq', 'created_at', 'updated_at']);
        $this->syncMasterCatalog($srcPdo, 'igbh_tests', ['test_nm', 'test_seq', 'level_cd', 'created_at', 'updated_at']);
        $this->syncMasterCatalog($srcPdo, 'igbh_test_configs', ['test_seq', 'sectors', 'created_at', 'updated_at']);
        $this->syncMasterCatalog($srcPdo, 'igbh_test_questions', ['test_seq', 'question_type', 'sort_no', 'sector', 'type_cd', 'answer', 'areas', 'difficulty', 'standard_point', 'created_at', 'updated_at']);
        $this->syncMasterCatalog($srcPdo, 'igbh_test_comments', ['test_seq', 'comment_type', 'condition_value', 'good_comment', 'weak_comment', 'created_at', 'updated_at']);
        $this->syncMasterCatalog($srcPdo, 'igbh_summative_themes', ['test_seq', 'sort_no', 'theme_seq', 'theme_desc', 'theme_point', 'created_at', 'updated_at']);

        if ($this->option('only-catalog')) {
            $this->info("Finished syncing master test catalogs.");
            return 0;
        }

        // 2. Pre-fetch ERP Students and Classes lookup maps
        $this->info("Indexing ERP students and classes for relational mapping...");
        $students = DB::table('students')->select('id', 'name', 'lms_code', 'branch_id')->get();
        $studentByLmsCode = [];
        $studentByName = [];
        foreach ($students as $s) {
            if ($s->lms_code) $studentByLmsCode[$s->lms_code] = $s;
            $studentByName[mb_strtolower(trim($s->name))] = $s;
        }

        $classes = DB::table('classes')->select('id', 'cls_name', 'branch_id', 'teacher_id')->get();
        $classByName = [];
        foreach ($classes as $c) {
            $classByName[mb_strtolower(trim($c->cls_name))] = $c;
        }

        // 3. Sync UCREA Results
        $this->syncUcreaResults($srcPdo, $studentByLmsCode, $studentByName, $classByName);

        // 4. Sync IG.BH Diagnostic Results
        $this->syncIgbhDiagnosticResults($srcPdo, $studentByLmsCode, $studentByName, $classByName);

        // 5. Sync IG.BH Weekly Evaluations
        $this->syncIgbhWeeklyEvals($srcPdo, $studentByLmsCode, $studentByName, $classByName);

        // 6. Sync IG.BH Class Summative & Summative Results
        $this->syncIgbhSummativeResults($srcPdo, $studentByLmsCode, $studentByName, $classByName);

        $this->info("All data migration completed successfully on DB stemkey!");
        return 0;
    }

    protected function syncMasterCatalog($srcPdo, $table, $columns)
    {
        $this->info("Syncing master catalog table: {$table}...");
        $stmt = $srcPdo->query("SELECT * FROM {$table}");
        $rows = $stmt->fetchAll();
        $count = count($rows);
        $this->line("  Found {$count} rows in source {$table}");

        if ($count === 0) return;

        // Truncate destination before full re-population of master catalogs
        DB::table($table)->delete();

        $chunks = array_chunk($rows, 200);
        foreach ($chunks as $chunk) {
            $insertData = [];
            foreach ($chunk as $row) {
                $item = [];
                foreach ($columns as $col) {
                    $item[$col] = $row[$col] ?? null;
                }
                $insertData[] = $item;
            }
            DB::table($table)->insert($insertData);
        }
        $this->info("  -> Successfully synced {$count} rows into {$table} on stemkey.");
    }

    protected function syncUcreaResults($srcPdo, $studentByLmsCode, $studentByName, $classByName)
    {
        $this->info("Syncing ucrea_student_results...");
        $rows = $srcPdo->query("SELECT * FROM ucrea_student_results")->fetchAll();

        foreach ($rows as $row) {
            $stuNameNorm = mb_strtolower(trim($row['stu_nm']));
            $classNmNorm = mb_strtolower(trim($row['class_nm'] ?? ''));

            $matchedStudent = $studentByName[$stuNameNorm] ?? null;
            $matchedClass = $classByName[$classNmNorm] ?? null;

            $studentId = $matchedStudent ? $matchedStudent->id : null;
            $classId = $matchedClass ? $matchedClass->id : null;
            $branchId = $matchedStudent ? $matchedStudent->branch_id : ($matchedClass ? $matchedClass->branch_id : null);
            $teacherId = $matchedClass ? $matchedClass->teacher_id : null;

            $contractId = null;
            if ($studentId && $classId) {
                $cnt = DB::table('contracts')->where('student_id', $studentId)->where('class_id', $classId)->value('id');
                if ($cnt) $contractId = $cnt;
            }

            $destId = DB::table('ucrea_student_results')->updateOrInsert(
                ['result_seq' => $row['result_seq']],
                [
                    'test_cd' => $row['test_cd'],
                    'level_cd' => $row['level_cd'],
                    'test_seq' => $row['test_seq'],
                    'student_id' => $studentId,
                    'class_id' => $classId,
                    'contract_id' => $contractId,
                    'branch_id' => $branchId,
                    'teacher_id' => $teacherId,
                    'memb_nm' => $row['memb_nm'],
                    'stu_nm' => $row['stu_nm'],
                    'class_nm' => $row['class_nm'],
                    'eval_dt' => $row['eval_dt'],
                    'result_cd' => $row['result_cd'],
                    'result_cd_nm' => $row['result_cd_nm'],
                    'report_data' => $row['report_data'],
                    'skills_grade' => $row['skills_grade'],
                    'created_at' => $row['created_at'],
                    'updated_at' => $row['updated_at'],
                ]
            );

            // Sync details for this result
            $currentResultId = DB::table('ucrea_student_results')->where('result_seq', $row['result_seq'])->value('id');
            if ($currentResultId) {
                DB::table('ucrea_student_result_details')->where('ucrea_student_result_id', $currentResultId)->delete();
                $details = $srcPdo->prepare("SELECT * FROM ucrea_student_result_details WHERE ucrea_student_result_id = ?");
                $details->execute([$row['id']]);
                $detailRows = $details->fetchAll();

                $insertDetails = [];
                foreach ($detailRows as $d) {
                    $insertDetails[] = [
                        'ucrea_student_result_id' => $currentResultId,
                        'question_no' => $d['question_no'],
                        'main_category' => $d['main_category'],
                        'sub_category' => $d['sub_category'],
                        'rubric_name' => $d['rubric_name'],
                        'assigned_score' => $d['assigned_score'],
                        'created_at' => $d['created_at'],
                        'updated_at' => $d['updated_at'],
                    ];
                }
                if (!empty($insertDetails)) {
                    DB::table('ucrea_student_result_details')->insert($insertDetails);
                }
            }
        }
        $this->info("  -> Synced " . count($rows) . " UCREA results with ERP mapping.");
    }

    protected function syncIgbhDiagnosticResults($srcPdo, $studentByLmsCode, $studentByName, $classByName)
    {
        $this->info("Syncing igbh_student_results...");
        $rows = $srcPdo->query("SELECT * FROM igbh_student_results")->fetchAll();

        foreach ($rows as $row) {
            $stuNameNorm = mb_strtolower(trim($row['stu_nm']));
            $matchedStudent = ($row['stu_seq'] && isset($studentByLmsCode[$row['stu_seq']]))
                ? $studentByLmsCode[$row['stu_seq']]
                : ($studentByName[$stuNameNorm] ?? null);

            $studentId = $matchedStudent ? $matchedStudent->id : null;
            $branchId = $matchedStudent ? $matchedStudent->branch_id : null;

            DB::table('igbh_student_results')->updateOrInsert(
                ['result_seq' => $row['result_seq']],
                [
                    'test_seq' => $row['test_seq'],
                    'stu_seq' => $row['stu_seq'],
                    'student_id' => $studentId,
                    'branch_id' => $branchId,
                    'stu_nm' => $row['stu_nm'],
                    'stu_birth_dt' => $row['stu_birth_dt'],
                    'reg_name' => $row['reg_name'],
                    'eval_dt' => $row['eval_dt'],
                    'reg_date' => $row['reg_date'],
                    'total_score' => $row['total_score'],
                    'subject_total' => $row['subject_total'],
                    'thinking_total' => $row['thinking_total'],
                    'assigned_level' => $row['assigned_level'],
                    'quarter_cd' => $row['quarter_cd'],
                    'quarter_cd_nm' => $row['quarter_cd_nm'],
                    'class_type_cd' => $row['class_type_cd'],
                    'created_at' => $row['created_at'],
                    'updated_at' => $row['updated_at'],
                ]
            );

            $currentResultId = DB::table('igbh_student_results')->where('result_seq', $row['result_seq'])->value('id');
            if ($currentResultId) {
                DB::table('igbh_student_result_details')->where('igbh_student_result_id', $currentResultId)->delete();
                $details = $srcPdo->prepare("SELECT * FROM igbh_student_result_details WHERE igbh_student_result_id = ?");
                $details->execute([$row['id']]);
                $detailRows = $details->fetchAll();

                $insertDetails = [];
                foreach ($detailRows as $d) {
                    $insertDetails[] = [
                        'igbh_student_result_id' => $currentResultId,
                        'question_no' => $d['question_no'],
                        'question_type' => $d['question_type'],
                        'seq_id' => $d['seq_id'],
                        'assigned_score' => $d['assigned_score'],
                        'unit' => $d['unit'],
                        'is_correct' => $d['is_correct'],
                        'max_score' => $d['max_score'],
                        'created_at' => $d['created_at'],
                        'updated_at' => $d['updated_at'],
                    ];
                }
                if (!empty($insertDetails)) {
                    DB::table('igbh_student_result_details')->insert($insertDetails);
                }
            }
        }
        $this->info("  -> Synced " . count($rows) . " IG.BH Diagnostic results with ERP mapping.");
    }

    protected function syncIgbhWeeklyEvals($srcPdo, $studentByLmsCode, $studentByName, $classByName)
    {
        $this->info("Syncing igbh_weekly_evals...");
        $rows = $srcPdo->query("SELECT * FROM igbh_weekly_evals")->fetchAll();

        foreach ($rows as $row) {
            $classNmNorm = mb_strtolower(trim($row['class_nm'] ?? ''));
            $matchedClass = $classByName[$classNmNorm] ?? null;

            $classId = $matchedClass ? $matchedClass->id : null;
            $branchId = $matchedClass ? $matchedClass->branch_id : null;
            $teacherId = $matchedClass ? $matchedClass->teacher_id : null;

            DB::table('igbh_weekly_evals')->updateOrInsert(
                [
                    'test_seq' => $row['test_seq'],
                    'class_seq' => $row['class_seq'],
                    'each_cd' => $row['each_cd'],
                ],
                [
                    'class_id' => $classId,
                    'class_nm' => $row['class_nm'],
                    'branch_id' => $branchId,
                    'teacher_id' => $teacherId,
                    'each_cd_nm' => $row['each_cd_nm'],
                    'teacher_nm' => $row['teacher_nm'],
                    'eval_ymd' => $row['eval_ymd'],
                    'status' => $row['status'] ?? 'Draft',
                    'created_at' => $row['created_at'],
                    'updated_at' => $row['updated_at'],
                ]
            );

            $currentEvalId = DB::table('igbh_weekly_evals')
                ->where('test_seq', $row['test_seq'])
                ->where('class_seq', $row['class_seq'])
                ->where('each_cd', $row['each_cd'])
                ->value('id');

            if ($currentEvalId) {
                DB::table('igbh_weekly_eval_details')->where('weekly_eval_id', $currentEvalId)->delete();
                $details = $srcPdo->prepare("SELECT * FROM igbh_weekly_eval_details WHERE weekly_eval_id = ?");
                $details->execute([$row['id']]);
                $detailRows = $details->fetchAll();

                $insertDetails = [];
                foreach ($detailRows as $d) {
                    $stuNameNorm = mb_strtolower(trim($d['stu_nm'] ?? ''));
                    $matchedStudent = ($d['stu_seq'] && isset($studentByLmsCode[$d['stu_seq']]))
                        ? $studentByLmsCode[$d['stu_seq']]
                        : ($studentByName[$stuNameNorm] ?? null);

                    $insertDetails[] = [
                        'weekly_eval_id' => $currentEvalId,
                        'student_id' => $matchedStudent ? $matchedStudent->id : null,
                        'stu_seq' => $d['stu_seq'],
                        'stu_nm' => $d['stu_nm'],
                        'workbook' => $d['workbook'],
                        'attd_listen' => $d['attd_listen'],
                        'attd_join' => $d['attd_join'],
                        'attd_express' => $d['attd_express'],
                        'attd_coop' => $d['attd_coop'],
                        'detect_normal' => $d['detect_normal'],
                        'detect_leadersh' => $d['detect_leadersh'],
                        'detect_math' => $d['detect_math'],
                        'detect_creative' => $d['detect_creative'],
                        'created_at' => $d['created_at'],
                        'updated_at' => $d['updated_at'],
                    ];
                }
                if (!empty($insertDetails)) {
                    DB::table('igbh_weekly_eval_details')->insert($insertDetails);
                }
            }
        }
        $this->info("  -> Synced " . count($rows) . " IG.BH weekly evals with ERP mapping.");
    }

    protected function syncIgbhSummativeResults($srcPdo, $studentByLmsCode, $studentByName, $classByName)
    {
        $this->info("Syncing igbh_class_summative_evals & igbh_summative_results...");
        try {
            $classSummative = $srcPdo->query("SELECT * FROM igbh_class_summative_evals")->fetchAll();
            foreach ($classSummative as $row) {
                $classNmNorm = mb_strtolower(trim($row['class_nm'] ?? ''));
                $matchedClass = $classByName[$classNmNorm] ?? null;

                DB::table('igbh_class_summative_evals')->updateOrInsert(
                    [
                        'test_seq' => $row['test_seq'],
                        'class_seq' => $row['class_seq'],
                    ],
                    [
                        'class_id' => $matchedClass ? $matchedClass->id : null,
                        'class_nm' => $row['class_nm'],
                        'branch_id' => $matchedClass ? $matchedClass->branch_id : null,
                        'teacher_id' => $matchedClass ? $matchedClass->teacher_id : null,
                        'teacher_nm' => $row['teacher_nm'],
                        'eval_ymd' => $row['eval_ymd'],
                        'status' => $row['status'] ?? 'draft',
                        'created_at' => $row['created_at'],
                        'updated_at' => $row['updated_at'],
                    ]
                );
            }
        } catch (\Exception $e) {}

        $rows = $srcPdo->query("SELECT * FROM igbh_summative_results")->fetchAll();
        foreach ($rows as $row) {
            $stuNameNorm = mb_strtolower(trim($row['stu_nm'] ?? ''));
            $classNmNorm = mb_strtolower(trim($row['class_nm'] ?? ''));

            $matchedStudent = ($row['stu_seq'] && isset($studentByLmsCode[$row['stu_seq']]))
                ? $studentByLmsCode[$row['stu_seq']]
                : ($studentByName[$stuNameNorm] ?? null);

            $matchedClass = $classByName[$classNmNorm] ?? null;

            $studentId = $matchedStudent ? $matchedStudent->id : null;
            $classId = $matchedClass ? $matchedClass->id : null;
            $branchId = $matchedStudent ? $matchedStudent->branch_id : ($matchedClass ? $matchedClass->branch_id : null);
            $teacherId = $matchedClass ? $matchedClass->teacher_id : null;

            $contractId = null;
            if ($studentId && $classId) {
                $cnt = DB::table('contracts')->where('student_id', $studentId)->where('class_id', $classId)->value('id');
                if ($cnt) $contractId = $cnt;
            }

            DB::table('igbh_summative_results')->updateOrInsert(
                [
                    'test_seq' => $row['test_seq'],
                    'stu_seq' => $row['stu_seq'],
                ],
                [
                    'student_id' => $studentId,
                    'class_id' => $classId,
                    'contract_id' => $contractId,
                    'branch_id' => $branchId,
                    'teacher_id' => $teacherId,
                    'stu_nm' => $row['stu_nm'],
                    'class_seq' => $row['class_seq'],
                    'class_nm' => $row['class_nm'],
                    'teacher_nm' => $row['teacher_nm'],
                    'total_score' => $row['total_score'] ?? 0,
                    'eval_dt' => $row['eval_dt'],
                    'status' => $row['status'],
                    'subjective_analysis' => $row['subjective_analysis'] ?? null,
                    'created_at' => $row['created_at'],
                    'updated_at' => $row['updated_at'],
                ]
            );

            $currentResultId = DB::table('igbh_summative_results')
                ->where('test_seq', $row['test_seq'])
                ->where('stu_seq', $row['stu_seq'])
                ->value('id');

            if ($currentResultId) {
                DB::table('igbh_summative_result_details')->where('summative_result_id', $currentResultId)->delete();
                $details = $srcPdo->prepare("SELECT * FROM igbh_summative_result_details WHERE summative_result_id = ?");
                $details->execute([$row['id']]);
                $detailRows = $details->fetchAll();

                $insertDetails = [];
                foreach ($detailRows as $d) {
                    $insertDetails[] = [
                        'summative_result_id' => $currentResultId,
                        'sort_no' => $d['sort_no'],
                        'max_score' => $d['max_score'] ?? null,
                        'score' => $d['score'] ?? 0,
                        'concept' => $d['concept'] ?? null,
                        'strategy' => $d['strategy'] ?? null,
                        'calculation' => $d['calculation'] ?? null,
                        'expression' => $d['expression'] ?? null,
                        'created_at' => $d['created_at'],
                        'updated_at' => $d['updated_at'],
                    ];
                }
                if (!empty($insertDetails)) {
                    DB::table('igbh_summative_result_details')->insert($insertDetails);
                }
            }
        }
        $this->info("  -> Synced " . count($rows) . " IG.BH Summative results with ERP mapping.");
    }
}

