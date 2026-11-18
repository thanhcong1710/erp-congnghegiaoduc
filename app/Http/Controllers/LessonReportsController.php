<?php

namespace App\Http\Controllers;

use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Nhận xét buổi học (theo lớp - buổi) → báo cáo gửi group phụ huynh.
 * Điểm danh KHÔNG lưu riêng: đọc/ghi trực tiếp schedule_has_student (qua AttendancesController@save).
 */
class LessonReportsController extends Controller
{
    const STATUS_DRAFT = 0;
    const STATUS_DONE = 1;
    const STATUS_SENT = 2;

    /** Role chỉ được thấy buổi mình dạy (giáo viên, trợ giảng) */
    const TEACHER_ONLY_ROLES = [36, 54];

    const ALLOWED_TAGS = '<p><br><strong><b><em><i><u><s><ol><ul><li><h1><h2><h3><h4><blockquote><span><a><sub><sup>';

    private function tableReady()
    {
        return Schema::hasTable('lesson_reports') && Schema::hasTable('lesson_report_students');
    }

    private function notReady()
    {
        return response()->json((object)['status' => 0, 'message' => 'Chưa khởi tạo bảng dữ liệu nhận xét buổi học (cần chạy migration).', 'data' => null]);
    }

    /** Phạm vi dữ liệu của user hiện tại */
    private function scope()
    {
        $userId = (int)Auth::user()->id;
        $roles = array_map(function ($r) {
            return (int)$r->role_id;
        }, DB::select("SELECT role_id FROM role_has_user WHERE user_id = ?", [$userId]));
        $teacherOnly = !empty($roles) && count(array_diff($roles, self::TEACHER_ONLY_ROLES)) === 0;
        $branchIds = implode(',', array_filter(array_map('intval', explode(',', (string)Auth::user()->getBranchesHasUser())))) ?: '0';
        return (object)['user_id' => $userId, 'teacher_only' => $teacherOnly, 'branch_ids' => $branchIds];
    }

    private function scopeCond($scope, $s = 's', $cl = 'cl')
    {
        $cond = " $cl.branch_id IN ({$scope->branch_ids}) ";
        if ($scope->teacher_only) {
            $id = $scope->user_id;
            $cond .= " AND ($s.teacher_id = $id OR $cl.teacher_id = $id OR $cl.ta_id = $id) ";
        }
        return $cond;
    }

    private function validDate($v)
    {
        return is_string($v) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $v) && strtotime($v);
    }

    /** Lấy buổi học (có kiểm tra quyền) */
    private function getSchedule($scheduleId)
    {
        $scheduleId = (int)$scheduleId;
        if (!$scheduleId) return null;
        $scope = $this->scope();
        return u::first("SELECT s.id AS schedule_id, s.class_id, s.class_date, s.subject_stt, s.teacher_id AS schedule_teacher_id,
                cl.cls_name, cl.branch_id, cl.product_id, cl.teacher_id AS class_teacher_id, cl.ta_id, cl.cm_id, cl.max_students
            FROM schedules AS s JOIN classes AS cl ON cl.id = s.class_id
            WHERE s.id = $scheduleId AND s.status = 1 AND " . $this->scopeCond($scope));
    }

    private function sanitizeHtml($html)
    {
        $html = strip_tags((string)$html, self::ALLOWED_TAGS);
        $html = preg_replace('/\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
        $html = preg_replace('/(href|src)\s*=\s*("|\')?\s*(javascript|data|vbscript):[^"\'>\s]*("|\')?/i', '$1="#"', $html);
        return $html;
    }

    private function emptyHtml($html)
    {
        return trim(str_replace('&nbsp;', ' ', strip_tags((string)$html))) === '';
    }

    /* ======================= Bộ lọc ======================= */
    public function filters(Request $request)
    {
        $scope = $this->scope();
        $branches = u::query("SELECT id, name FROM branches WHERE status = 1 AND id IN ({$scope->branch_ids}) ORDER BY name");
        $classCond = " cl.status = 1 AND cl.branch_id IN ({$scope->branch_ids}) ";
        if ($scope->teacher_only) {
            $id = $scope->user_id;
            $classCond .= " AND (cl.teacher_id = $id OR cl.ta_id = $id OR EXISTS (SELECT 1 FROM schedules AS s WHERE s.class_id = cl.id AND s.teacher_id = $id))";
        }
        $classes = u::query("SELECT cl.id, cl.cls_name, cl.branch_id FROM classes AS cl WHERE $classCond ORDER BY cl.cls_name");
        $teachers = [];
        if (!$scope->teacher_only) {
            $teachers = u::query("SELECT DISTINCT u.id, u.name FROM users AS u
                JOIN role_has_user AS ru ON ru.user_id = u.id
                JOIN branch_has_user AS bu ON bu.user_id = u.id
                WHERE u.status = 1 AND ru.role_id IN (36, 37, 54) AND bu.branch_id IN ({$scope->branch_ids}) ORDER BY u.name");
        }
        return response()->json((object)['status' => 1, 'message' => '', 'data' => [
            'branches' => $branches,
            'classes' => $classes,
            'teachers' => $teachers,
            'teacher_only' => $scope->teacher_only,
            'ready' => $this->tableReady(),
        ]]);
    }

    /* ======================= Danh sách buổi ======================= */
    public function sessions(Request $request)
    {
        $from = (string)$request->input('from_date');
        $to = (string)$request->input('to_date');
        if (!$this->validDate($from) || !$this->validDate($to) || $from > $to) {
            return response()->json((object)['status' => 0, 'message' => 'Khoảng ngày không hợp lệ', 'data' => []]);
        }
        if ((strtotime($to) - strtotime($from)) / 86400 > 62) {
            return response()->json((object)['status' => 0, 'message' => 'Chỉ xem tối đa 2 tháng mỗi lần', 'data' => []]);
        }
        $scope = $this->scope();
        $cond = " s.status = 1 AND s.class_date BETWEEN '$from' AND '$to' AND " . $this->scopeCond($scope);
        if ($branchId = (int)$request->input('branch_id')) $cond .= " AND cl.branch_id = $branchId ";
        if ($classId = (int)$request->input('class_id')) $cond .= " AND s.class_id = $classId ";
        if (!$scope->teacher_only && ($teacherId = (int)$request->input('teacher_id'))) {
            $cond .= " AND (s.teacher_id = $teacherId OR cl.teacher_id = $teacherId OR cl.ta_id = $teacherId) ";
        }

        $ready = $this->tableReady();
        $reportCols = $ready
            ? "lr.id AS report_id, lr.status AS report_status, lr.lesson_title, lr.start_time, lr.end_time,
               (SELECT count(*) FROM lesson_report_students AS rs WHERE rs.lesson_report_id = lr.id AND rs.comment IS NOT NULL AND rs.comment <> '') AS commented"
            : "NULL AS report_id, NULL AS report_status, NULL AS lesson_title, NULL AS start_time, NULL AS end_time, 0 AS commented";
        $reportJoin = $ready ? " LEFT JOIN lesson_reports AS lr ON lr.schedule_id = s.id " : "";

        $rows = u::query("SELECT s.id AS schedule_id, s.class_id, s.class_date, s.subject_stt, cl.cls_name, cl.branch_id,
                (SELECT name FROM branches WHERE id = cl.branch_id) AS branch_name,
                (SELECT name FROM users WHERE id = COALESCE(s.teacher_id, cl.teacher_id)) AS teacher_name,
                (SELECT count(DISTINCT ss.student_id) FROM schedule_has_student AS ss WHERE ss.class_id = s.class_id AND ss.class_date = s.class_date) AS total_students,
                (SELECT count(DISTINCT ss.student_id) FROM schedule_has_student AS ss WHERE ss.class_id = s.class_id AND ss.class_date = s.class_date AND ss.attendance_status IN (1, 3)) AS present,
                (SELECT count(DISTINCT ss.student_id) FROM schedule_has_student AS ss WHERE ss.class_id = s.class_id AND ss.class_date = s.class_date AND ss.attendance_status = 0) AS pending_attendance,
                (SELECT count(*) FROM make_up_sessions AS m WHERE m.to_class_id = s.class_id AND m.to_class_date = s.class_date AND m.status IN (1, 2)) AS makeup_count,
                $reportCols
            FROM schedules AS s JOIN classes AS cl ON cl.id = s.class_id $reportJoin
            WHERE $cond ORDER BY s.class_date, cl.cls_name");

        foreach ($rows as $r) {
            $r->total_students = (int)$r->total_students + (int)$r->makeup_count;
            $r->present = (int)$r->present;
            $r->commented = (int)$r->commented;
            // ui_status: none = chưa nhận xét, draft = đang làm, done = hoàn thành, sent = đã gửi
            if ($r->report_id === null) $r->ui_status = 'none';
            elseif ((int)$r->report_status === self::STATUS_SENT) $r->ui_status = 'sent';
            elseif ((int)$r->report_status === self::STATUS_DONE) $r->ui_status = 'done';
            else $r->ui_status = 'draft';
        }
        return response()->json((object)['status' => 1, 'message' => '', 'data' => $rows, 'ready' => $ready]);
    }

    /* ======================= Chi tiết 1 buổi ======================= */
    private function buildDetail($sch, $forPublic = false)
    {
        $classId = (int)$sch->class_id;
        $classDate = $sch->class_date;
        $teacherId = (int)($sch->schedule_teacher_id ?: $sch->class_teacher_id);

        $users = [];
        $ids = array_filter([$teacherId, (int)$sch->ta_id, (int)$sch->cm_id]);
        if ($ids) {
            foreach (u::query("SELECT id, name FROM users WHERE id IN (" . implode(',', $ids) . ")") as $usr) $users[$usr->id] = $usr->name;
        }
        $branch = u::first("SELECT name, address, hotline FROM branches WHERE id = " . (int)$sch->branch_id);
        $totalSessions = u::first("SELECT count(*) AS total FROM schedules WHERE class_id = $classId AND status = 1");

        $report = DB::selectOne("SELECT * FROM lesson_reports WHERE schedule_id = ?", [(int)$sch->schedule_id]);
        if (!$report) {
            // Mặc định giờ học lấy từ báo cáo gần nhất của lớp
            $last = DB::selectOne("SELECT start_time, end_time FROM lesson_reports WHERE class_id = ? AND class_date < ? ORDER BY class_date DESC LIMIT 1", [$classId, $classDate]);
            $report = (object)[
                'id' => null, 'lesson_title' => '', 'general_comment' => '', 'homework_deadline' => '',
                'start_time' => $last->start_time ?? '', 'end_time' => $last->end_time ?? '',
                'status' => null, 'share_token' => null, 'sent_at' => null,
            ];
        }

        $saved = [];
        if ($report->id) {
            foreach (DB::select("SELECT student_id, homework_done, homework_score, comment FROM lesson_report_students WHERE lesson_report_id = ?", [$report->id]) as $row) {
                $saved[$row->student_id] = $row;
            }
        }

        $students = [];
        $own = DB::select("SELECT ss.id AS shs_id, ss.student_id, ss.attendance_status, ss.note, st.name, st.lms_code
            FROM schedule_has_student AS ss JOIN students AS st ON st.id = ss.student_id
            WHERE ss.class_id = ? AND ss.class_date = ? ORDER BY ss.id", [$classId, $classDate]);
        foreach ($own as $o) {
            if (isset($students[$o->student_id])) continue;
            $att = (int)$o->attendance_status;
            $students[$o->student_id] = (object)[
                'student_id' => (int)$o->student_id, 'name' => $o->name, 'lms_code' => $o->lms_code,
                'shs_id' => (int)$o->shs_id, 'note' => $o->note, 'is_makeup' => 0, 'makeup_from' => null,
                'attendance_status' => $att === 3 ? 1 : $att, // 3 (đã học bù) coi như đi học
            ];
        }
        $makeups = DB::select("SELECT m.id, m.student_id, m.status, st.name, st.lms_code, cl.cls_name AS from_class
            FROM make_up_sessions AS m JOIN students AS st ON st.id = m.student_id LEFT JOIN classes AS cl ON cl.id = m.from_class_id
            WHERE m.to_class_id = ? AND m.to_class_date = ? AND m.status IN (1, 2) ORDER BY m.id", [$classId, $classDate]);
        foreach ($makeups as $m) {
            if (isset($students[$m->student_id])) continue;
            $students[$m->student_id] = (object)[
                'student_id' => (int)$m->student_id, 'name' => $m->name, 'lms_code' => $m->lms_code,
                'shs_id' => null, 'note' => null, 'is_makeup' => 1, 'makeup_from' => $m->from_class,
                'attendance_status' => (int)$m->status === 2 ? 1 : 0,
            ];
        }
        foreach ($students as $sid => $st) {
            $sv = $saved[$sid] ?? null;
            $st->homework_done = $sv && $sv->homework_done !== null ? (int)$sv->homework_done : null;
            $st->homework_score = $sv && $sv->homework_score !== null ? (float)$sv->homework_score : null;
            $st->comment = $sv->comment ?? '';
            if ($forPublic) {
                unset($st->lms_code, $st->shs_id, $st->note);
            }
        }
        $students = array_values($students);
        usort($students, function ($a, $b) {
            if ($a->is_makeup !== $b->is_makeup) return $a->is_makeup - $b->is_makeup;
            return strcmp((string)$a->name, (string)$b->name);
        });

        $present = 0; $homework = 0;
        foreach ($students as $st) {
            if ($st->attendance_status === 1) $present++;
            if ($st->homework_done === 1) $homework++;
        }

        $session = (object)[
            'schedule_id' => (int)$sch->schedule_id,
            'class_id' => $classId,
            'cls_name' => $sch->cls_name,
            'class_date' => $classDate,
            'subject_stt' => $sch->subject_stt,
            'total_sessions' => $totalSessions ? (int)$totalSessions->total : 0,
            'teacher_name' => $users[$teacherId] ?? '',
            'ta_name' => $users[(int)$sch->ta_id] ?? '',
            'cm_name' => $users[(int)$sch->cm_id] ?? '',
            'branch_name' => $branch->name ?? '',
            'branch_address' => $branch->address ?? '',
            'branch_hotline' => $branch->hotline ?? '',
            'total_students' => count($students),
            'present' => $present,
            'homework_done' => $homework,
        ];

        $reportOut = (object)[
            'id' => $report->id,
            'lesson_title' => $report->lesson_title,
            'start_time' => $report->start_time,
            'end_time' => $report->end_time,
            'general_comment' => $report->general_comment,
            'homework_deadline' => $report->homework_deadline,
            'status' => $report->status === null ? null : (int)$report->status,
        ];
        if (!$forPublic) {
            $reportOut->share_token = $report->share_token;
            $reportOut->sent_at = $report->sent_at;
        }

        return (object)['session' => $session, 'report' => $reportOut, 'students' => $students];
    }

    public function show(Request $request, $schedule_id)
    {
        if (!$this->tableReady()) return $this->notReady();
        $sch = $this->getSchedule($schedule_id);
        if (!$sch) {
            return response()->json((object)['status' => 0, 'message' => 'Không tìm thấy buổi học hoặc bạn không có quyền', 'data' => null]);
        }
        $data = $this->buildDetail($sch);
        $scope = $this->scope();
        $classId = (int)$sch->class_id;
        $date = $sch->class_date;
        $teacherCond = $scope->teacher_only
            ? " AND (s.teacher_id = {$scope->user_id} OR cl.teacher_id = {$scope->user_id} OR cl.ta_id = {$scope->user_id})" : "";
        $prev = u::first("SELECT s.id FROM schedules AS s JOIN classes AS cl ON cl.id = s.class_id WHERE s.class_id = $classId AND s.status = 1 AND s.class_date < '$date' $teacherCond ORDER BY s.class_date DESC LIMIT 1");
        $next = u::first("SELECT s.id FROM schedules AS s JOIN classes AS cl ON cl.id = s.class_id WHERE s.class_id = $classId AND s.status = 1 AND s.class_date > '$date' $teacherCond ORDER BY s.class_date LIMIT 1");
        $data->prev_schedule_id = $prev ? (int)$prev->id : null;
        $data->next_schedule_id = $next ? (int)$next->id : null;
        return response()->json((object)['status' => 1, 'message' => '', 'data' => $data]);
    }

    /** Tạo (nếu chưa có) và trả về id báo cáo của buổi */
    private function ensureReport($sch)
    {
        $existing = DB::selectOne("SELECT id, status FROM lesson_reports WHERE schedule_id = ?", [(int)$sch->schedule_id]);
        if ($existing) return $existing;
        $now = date('Y-m-d H:i:s');
        $last = DB::selectOne("SELECT start_time, end_time FROM lesson_reports WHERE class_id = ? AND class_date < ? ORDER BY class_date DESC LIMIT 1", [(int)$sch->class_id, $sch->class_date]);
        DB::insert("INSERT INTO lesson_reports (schedule_id, class_id, branch_id, class_date, subject_stt, start_time, end_time, teacher_id, ta_id, cm_id, status, share_token, creator_id, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE id = id", [
            (int)$sch->schedule_id, (int)$sch->class_id, (int)$sch->branch_id, $sch->class_date, $sch->subject_stt,
            $last->start_time ?? null, $last->end_time ?? null,
            (int)($sch->schedule_teacher_id ?: $sch->class_teacher_id) ?: null, $sch->ta_id ?: null, $sch->cm_id ?: null,
            Str::random(32), Auth::user()->id, $now, $now,
        ]);
        return DB::selectOne("SELECT id, status FROM lesson_reports WHERE schedule_id = ?", [(int)$sch->schedule_id]);
    }

    /* ======================= Lưu (autosave từng phần) ======================= */
    public function save(Request $request)
    {
        if (!$this->tableReady()) return $this->notReady();
        $sch = $this->getSchedule($request->input('schedule_id'));
        if (!$sch) {
            return response()->json((object)['status' => 0, 'message' => 'Không tìm thấy buổi học hoặc bạn không có quyền', 'data' => null]);
        }
        $now = date('Y-m-d H:i:s');
        $userId = Auth::user()->id;

        // Validate học sinh trước khi ghi
        $studentsIn = $request->input('students');
        $rowsToSave = [];
        if (is_array($studentsIn) && count($studentsIn)) {
            $valid = [];
            foreach (DB::select("SELECT ss.id, ss.student_id FROM schedule_has_student AS ss WHERE ss.class_id = ? AND ss.class_date = ? ORDER BY ss.id", [(int)$sch->class_id, $sch->class_date]) as $r) {
                if (!isset($valid[$r->student_id])) $valid[$r->student_id] = ['shs' => (int)$r->id, 'mk' => null];
            }
            foreach (DB::select("SELECT id, student_id FROM make_up_sessions WHERE to_class_id = ? AND to_class_date = ? AND status IN (1, 2)", [(int)$sch->class_id, $sch->class_date]) as $r) {
                if (!isset($valid[$r->student_id])) $valid[$r->student_id] = ['shs' => null, 'mk' => (int)$r->id];
            }
            foreach ($studentsIn as $st) {
                $sid = (int)data_get($st, 'student_id');
                if (!isset($valid[$sid])) {
                    return response()->json((object)['status' => 0, 'message' => 'Học sinh không thuộc buổi học này', 'data' => null]);
                }
                $score = data_get($st, 'homework_score');
                if ($score === '' || $score === null) {
                    $score = null;
                } else {
                    $score = str_replace(',', '.', (string)$score);
                    if (!is_numeric($score) || $score < 0 || $score > 10) {
                        return response()->json((object)['status' => 0, 'message' => 'Điểm BTVN phải trong khoảng 0 - 10', 'data' => null]);
                    }
                    $score = round((float)$score, 1);
                }
                $hw = data_get($st, 'homework_done');
                $hw = ($hw === null || $hw === '') ? null : ((int)$hw ? 1 : 0);
                $rowsToSave[] = [
                    'student_id' => $sid, 'shs' => $valid[$sid]['shs'], 'mk' => $valid[$sid]['mk'],
                    'hw' => $hw, 'score' => $score, 'comment' => mb_substr(trim((string)data_get($st, 'comment', '')), 0, 5000),
                ];
            }
        }

        $report = $this->ensureReport($sch);
        $reportIn = $request->input('report');
        if (is_array($reportIn)) {
            $update = [];
            if (array_key_exists('lesson_title', $reportIn)) $update['lesson_title'] = mb_substr(trim((string)$reportIn['lesson_title']), 0, 255);
            foreach (['start_time', 'end_time'] as $k) {
                if (array_key_exists($k, $reportIn)) {
                    $v = (string)$reportIn[$k];
                    $update[$k] = preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $v) ? $v : null;
                }
            }
            if (array_key_exists('general_comment', $reportIn)) {
                $html = $this->sanitizeHtml($reportIn['general_comment']);
                $update['general_comment'] = $this->emptyHtml($html) ? null : $html;
            }
            if (array_key_exists('homework_deadline', $reportIn)) $update['homework_deadline'] = mb_substr(trim((string)$reportIn['homework_deadline']), 0, 255);
            if ($update) {
                $update['updator_id'] = $userId;
                $update['updated_at'] = $now;
                DB::table('lesson_reports')->where('id', $report->id)->update($update);
            }
        }

        foreach ($rowsToSave as $r) {
            DB::insert("INSERT INTO lesson_report_students (lesson_report_id, student_id, schedule_has_student_id, make_up_session_id, is_makeup, homework_done, homework_score, comment, updator_id, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE homework_done = VALUES(homework_done), homework_score = VALUES(homework_score), comment = VALUES(comment),
                    updator_id = VALUES(updator_id), updated_at = VALUES(updated_at)", [
                $report->id, $r['student_id'], $r['shs'], $r['mk'], $r['mk'] ? 1 : 0, $r['hw'], $r['score'], $r['comment'] === '' ? null : $r['comment'],
                $userId, $now, $now,
            ]);
        }
        if ($rowsToSave) {
            DB::update("UPDATE lesson_reports SET updator_id = ?, updated_at = ? WHERE id = ?", [$userId, $now, $report->id]);
        }

        return response()->json((object)['status' => 1, 'message' => 'Đã lưu', 'data' => ['report_id' => (int)$report->id, 'status' => (int)$report->status]]);
    }

    /* ======================= Trạng thái ======================= */
    public function updateStatus(Request $request)
    {
        if (!$this->tableReady()) return $this->notReady();
        $sch = $this->getSchedule($request->input('schedule_id'));
        if (!$sch) {
            return response()->json((object)['status' => 0, 'message' => 'Không tìm thấy buổi học hoặc bạn không có quyền', 'data' => null]);
        }
        $status = (int)$request->input('status');
        if (!in_array($status, [self::STATUS_DRAFT, self::STATUS_DONE, self::STATUS_SENT], true)) {
            return response()->json((object)['status' => 0, 'message' => 'Trạng thái không hợp lệ', 'data' => null]);
        }
        $report = $this->ensureReport($sch);
        $full = DB::selectOne("SELECT * FROM lesson_reports WHERE id = ?", [$report->id]);
        if ($status >= self::STATUS_DONE && $this->emptyHtml($full->general_comment) && trim((string)$full->lesson_title) === '') {
            return response()->json((object)['status' => 0, 'message' => 'Vui lòng nhập tên bài học hoặc nhận xét chung trước khi hoàn thành', 'data' => null]);
        }
        $update = ['status' => $status, 'updator_id' => Auth::user()->id, 'updated_at' => date('Y-m-d H:i:s')];
        if (!$full->share_token) $update['share_token'] = Str::random(32);
        if ($status === self::STATUS_SENT) {
            $update['sent_at'] = date('Y-m-d H:i:s');
            $update['sent_by'] = Auth::user()->id;
        }
        DB::table('lesson_reports')->where('id', $report->id)->update($update);
        $token = $update['share_token'] ?? $full->share_token;
        $messages = [self::STATUS_DRAFT => 'Đã mở lại để chỉnh sửa', self::STATUS_DONE => 'Đã đánh dấu hoàn thành', self::STATUS_SENT => 'Đã đánh dấu gửi phụ huynh'];
        return response()->json((object)['status' => 1, 'message' => $messages[$status], 'data' => ['status' => $status, 'share_token' => $token]]);
    }

    /* ======================= Sao chép nhận xét chung ======================= */
    public function copySources(Request $request, $schedule_id)
    {
        if (!$this->tableReady()) return $this->notReady();
        $sch = $this->getSchedule($schedule_id);
        if (!$sch) {
            return response()->json((object)['status' => 0, 'message' => 'Không tìm thấy buổi học hoặc bạn không có quyền', 'data' => []]);
        }
        $scope = $this->scope();
        $stt = (int)$sch->subject_stt;
        $rows = u::query("SELECT lr.schedule_id, lr.class_date, lr.subject_stt, lr.lesson_title, lr.general_comment, lr.homework_deadline, cl.cls_name,
                (lr.class_id = " . (int)$sch->class_id . ") AS same_class
            FROM lesson_reports AS lr JOIN classes AS cl ON cl.id = lr.class_id
            WHERE lr.schedule_id <> " . (int)$sch->schedule_id . " AND lr.general_comment IS NOT NULL
              AND cl.product_id " . ($sch->product_id ? "= " . (int)$sch->product_id : "IS NULL") . "
              AND cl.branch_id IN ({$scope->branch_ids})
              AND lr.class_date >= DATE_SUB(CURRENT_DATE, INTERVAL 180 DAY)
            ORDER BY (lr.subject_stt = $stt) DESC, lr.class_date DESC LIMIT 30");
        return response()->json((object)['status' => 1, 'message' => '', 'data' => $rows]);
    }

    /* ======================= Bản public cho phụ huynh ======================= */
    public function publicReport(Request $request, $token)
    {
        if (!$this->tableReady() || !preg_match('/^[A-Za-z0-9]{32}$/', (string)$token)) {
            return response()->json((object)['status' => 0, 'message' => 'Không tìm thấy báo cáo', 'data' => null]);
        }
        $lr = DB::selectOne("SELECT schedule_id, status FROM lesson_reports WHERE share_token = ?", [$token]);
        if (!$lr) {
            return response()->json((object)['status' => 0, 'message' => 'Không tìm thấy báo cáo', 'data' => null]);
        }
        if ((int)$lr->status < self::STATUS_DONE) {
            return response()->json((object)['status' => 0, 'message' => 'Báo cáo buổi học đang được giáo viên hoàn thiện, vui lòng quay lại sau.', 'data' => null]);
        }
        $sch = u::first("SELECT s.id AS schedule_id, s.class_id, s.class_date, s.subject_stt, s.teacher_id AS schedule_teacher_id,
                cl.cls_name, cl.branch_id, cl.product_id, cl.teacher_id AS class_teacher_id, cl.ta_id, cl.cm_id, cl.max_students
            FROM schedules AS s JOIN classes AS cl ON cl.id = s.class_id WHERE s.id = " . (int)$lr->schedule_id);
        if (!$sch) {
            return response()->json((object)['status' => 0, 'message' => 'Không tìm thấy báo cáo', 'data' => null]);
        }
        return response()->json((object)['status' => 1, 'message' => '', 'data' => $this->buildDetail($sch, true)]);
    }
}
