<?php

namespace App\Http\Controllers;

use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Nhận xét & đánh giá theo TUẦN (lớp × tuần thứ 2 → CN) → báo cáo gửi phụ huynh.
 * - 1 phiếu / lớp / tuần, gắn vào BUỔI CUỐI của tuần (lesson_reports.schedule_id). Các buổi khác trong tuần "gộp" vào phiếu đó.
 * - Điểm danh KHÔNG lưu riêng: đọc/ghi trực tiếp schedule_has_student (qua AttendancesController@save) cho từng buổi trong tuần.
 * - Nội dung bài cố định theo lộ trình: lesson_plans (khóa học × tuần thứ N) → tự điền khi phiếu còn trống.
 * - Mỗi học sinh 1 link riêng (lesson_report_students.share_token) → PH chỉ thấy nhận xét của con mình.
 *   Link chung của lớp (lesson_reports.share_token) chỉ hiện nội dung bài học, không có nhận xét từng em.
 */
class LessonReportsController extends Controller
{
    const STATUS_DRAFT = 0;
    const STATUS_DONE = 1;
    const STATUS_SENT = 2;

    /** Role chỉ được thấy buổi mình dạy (giáo viên, trợ giảng) */
    const TEACHER_ONLY_ROLES = [36, 54];

    const ALLOWED_TAGS = '<p><br><strong><b><em><i><u><s><ol><ul><li><h1><h2><h3><h4><blockquote><span><a><sub><sup>';

    private $readyCache = null;

    private function tableReady()
    {
        if ($this->readyCache === null) {
            $this->readyCache = Schema::hasTable('lesson_reports') && Schema::hasTable('lesson_report_students')
                && Schema::hasTable('lesson_plans') && Schema::hasColumn('lesson_reports', 'week_start')
                && Schema::hasColumn('lesson_report_students', 'share_token');
        }
        return $this->readyCache;
    }

    private function notReady()
    {
        return response()->json((object)['status' => 0, 'message' => 'Chưa khởi tạo bảng dữ liệu nhận xét (cần chạy migration lesson reports).', 'data' => null]);
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

    private function scheduleCols()
    {
        return "s.id AS schedule_id, s.class_id, s.class_date, s.subject_stt, s.teacher_id AS schedule_teacher_id,
                cl.cls_name, cl.branch_id, cl.product_id, cl.teacher_id AS class_teacher_id, cl.ta_id, cl.cm_id, cl.max_students";
    }

    /** Lấy buổi học (có kiểm tra quyền) */
    private function getSchedule($scheduleId)
    {
        $scheduleId = (int)$scheduleId;
        if (!$scheduleId) return null;
        $scope = $this->scope();
        return u::first("SELECT " . $this->scheduleCols() . "
            FROM schedules AS s JOIN classes AS cl ON cl.id = s.class_id
            WHERE s.id = $scheduleId AND s.status = 1 AND " . $this->scopeCond($scope));
    }

    /* ======================= Tuần học ======================= */

    /** [thứ 2, chủ nhật] của tuần chứa $date */
    private function weekRange($date)
    {
        $ts = strtotime($date);
        $monday = date('Y-m-d', strtotime('-' . ((int)date('N', $ts) - 1) . ' days', $ts));
        return [$monday, date('Y-m-d', strtotime($monday . ' +6 days'))];
    }

    /** Các buổi của lớp trong tuần chứa $date (tăng dần) */
    private function weekSessions($classId, $date)
    {
        list($from, $to) = $this->weekRange($date);
        return DB::select("SELECT id AS schedule_id, class_date, subject_stt FROM schedules
            WHERE class_id = ? AND status = 1 AND class_date BETWEEN ? AND ? ORDER BY class_date, id", [(int)$classId, $from, $to]);
    }

    /** Tuần thứ mấy của lớp (đếm số tuần có lịch học tính đến tuần này) */
    private function weekNo($classId, $date)
    {
        list(, $to) = $this->weekRange($date);
        $row = DB::selectOne("SELECT COUNT(DISTINCT YEARWEEK(class_date, 3)) AS n FROM schedules WHERE class_id = ? AND status = 1 AND class_date <= ?", [(int)$classId, $to]);
        return $row ? (int)$row->n : 0;
    }

    /**
     * Buổi gắn phiếu của tuần (= buổi cuối tuần). User đã có quyền với 1 buổi của lớp-tuần
     * thì được thao tác phiếu tuần đó (kể cả GV dạy thay buổi đầu tuần).
     */
    private function reportScheduleOf($sch)
    {
        $week = $this->weekSessions($sch->class_id, $sch->class_date);
        $last = end($week);
        if (!$last || (int)$last->schedule_id === (int)$sch->schedule_id) return $sch;
        return u::first("SELECT " . $this->scheduleCols() . " FROM schedules AS s JOIN classes AS cl ON cl.id = s.class_id WHERE s.id = " . (int)$last->schedule_id);
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

    /* ======================= Danh sách buổi (lịch tuần) ======================= */
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

        $rows = u::query("SELECT s.id AS schedule_id, s.class_id, s.class_date, s.subject_stt, cl.cls_name, cl.branch_id,
                (SELECT name FROM branches WHERE id = cl.branch_id) AS branch_name,
                (SELECT name FROM users WHERE id = COALESCE(s.teacher_id, cl.teacher_id)) AS teacher_name,
                (SELECT count(DISTINCT ss.student_id) FROM schedule_has_student AS ss WHERE ss.class_id = s.class_id AND ss.class_date = s.class_date) AS total_students,
                (SELECT count(DISTINCT ss.student_id) FROM schedule_has_student AS ss WHERE ss.class_id = s.class_id AND ss.class_date = s.class_date AND ss.attendance_status IN (1, 3)) AS present,
                (SELECT count(DISTINCT ss.student_id) FROM schedule_has_student AS ss WHERE ss.class_id = s.class_id AND ss.class_date = s.class_date AND ss.attendance_status = 0) AS pending_attendance,
                (SELECT count(*) FROM make_up_sessions AS m WHERE m.to_class_id = s.class_id AND m.to_class_date = s.class_date AND m.status IN (1, 2)) AS makeup_count,
                (SELECT s2.id FROM schedules AS s2 WHERE s2.class_id = s.class_id AND s2.status = 1 AND YEARWEEK(s2.class_date, 3) = YEARWEEK(s.class_date, 3)
                    ORDER BY s2.class_date DESC, s2.id DESC LIMIT 1) AS report_schedule_id,
                (SELECT count(*) FROM schedules AS s3 WHERE s3.class_id = s.class_id AND s3.status = 1 AND YEARWEEK(s3.class_date, 3) = YEARWEEK(s.class_date, 3)) AS week_session_count
            FROM schedules AS s JOIN classes AS cl ON cl.id = s.class_id
            WHERE $cond ORDER BY s.class_date, cl.cls_name");

        $ready = $this->tableReady();
        $reports = [];
        $reportIds = array_values(array_unique(array_filter(array_map(function ($r) {
            return (int)$r->report_schedule_id;
        }, $rows))));
        if ($ready && $reportIds) {
            foreach (u::query("SELECT lr.schedule_id, lr.id AS report_id, lr.status AS report_status, lr.lesson_title, lr.start_time, lr.end_time,
                    (SELECT count(*) FROM lesson_report_students AS rs WHERE rs.lesson_report_id = lr.id AND rs.comment IS NOT NULL AND rs.comment <> '') AS commented
                FROM lesson_reports AS lr WHERE lr.schedule_id IN (" . implode(',', $reportIds) . ")") as $lr) {
                $reports[(int)$lr->schedule_id] = $lr;
            }
        }
        // Tên các buổi cùng tuần (để hiển thị "gộp vào buổi ...")
        $reportDates = [];
        if ($reportIds) {
            foreach (u::query("SELECT id, class_date FROM schedules WHERE id IN (" . implode(',', $reportIds) . ")") as $x) $reportDates[(int)$x->id] = $x->class_date;
        }

        foreach ($rows as $r) {
            $r->total_students = (int)$r->total_students + (int)$r->makeup_count;
            $r->present = (int)$r->present;
            $r->report_schedule_id = (int)$r->report_schedule_id ?: (int)$r->schedule_id;
            $r->is_report_session = $r->report_schedule_id === (int)$r->schedule_id ? 1 : 0;
            $r->week_session_count = (int)$r->week_session_count;
            $r->report_date = $reportDates[$r->report_schedule_id] ?? $r->class_date;
            $lr = $reports[$r->report_schedule_id] ?? null;
            $r->report_id = $lr ? (int)$lr->report_id : null;
            $r->report_status = $lr ? (int)$lr->report_status : null;
            $r->lesson_title = $lr->lesson_title ?? null;
            $r->start_time = $lr->start_time ?? null;
            $r->end_time = $lr->end_time ?? null;
            $r->commented = $lr ? (int)$lr->commented : 0;
            // ui_status của PHIẾU TUẦN: none / draft / done / sent
            if (!$lr) $r->ui_status = 'none';
            elseif ($r->report_status === self::STATUS_SENT) $r->ui_status = 'sent';
            elseif ($r->report_status === self::STATUS_DONE) $r->ui_status = 'done';
            else $r->ui_status = 'draft';
        }
        return response()->json((object)['status' => 1, 'message' => '', 'data' => $rows, 'ready' => $ready]);
    }

    /* ======================= Chi tiết phiếu tuần ======================= */

    /** Học sinh của cả tuần + điểm danh từng buổi */
    private function weekStudents($classId, $weekSessions)
    {
        $dates = array_map(function ($w) {
            return $w->class_date;
        }, $weekSessions);
        $schByDate = [];
        foreach ($weekSessions as $w) $schByDate[$w->class_date] = (int)$w->schedule_id;
        if (!$dates) return [];
        $in = implode(',', array_fill(0, count($dates), '?'));

        $students = [];
        $own = DB::select("SELECT ss.id AS shs_id, ss.student_id, ss.class_date, ss.attendance_status, ss.note, st.name, st.lms_code
            FROM schedule_has_student AS ss JOIN students AS st ON st.id = ss.student_id
            WHERE ss.class_id = ? AND ss.class_date IN ($in) ORDER BY ss.id", array_merge([(int)$classId], $dates));
        foreach ($own as $o) {
            $sid = (int)$o->student_id;
            if (!isset($students[$sid])) {
                $students[$sid] = (object)['student_id' => $sid, 'name' => $o->name, 'lms_code' => $o->lms_code, 'is_makeup' => 1, 'makeup_from' => null, 'attendance' => []];
            }
            $schId = $schByDate[$o->class_date] ?? null;
            if (!$schId || isset($students[$sid]->attendance[$schId])) continue;
            $att = (int)$o->attendance_status;
            $students[$sid]->is_makeup = 0;
            $students[$sid]->attendance[$schId] = (object)[
                'shs_id' => (int)$o->shs_id, 'note' => $o->note, 'is_makeup' => 0,
                'status' => $att === 3 ? 1 : $att, // 3 (đã học bù) coi như đi học
            ];
        }
        $makeups = DB::select("SELECT m.id, m.student_id, m.to_class_date, m.status, st.name, st.lms_code, cl.cls_name AS from_class
            FROM make_up_sessions AS m JOIN students AS st ON st.id = m.student_id LEFT JOIN classes AS cl ON cl.id = m.from_class_id
            WHERE m.to_class_id = ? AND m.to_class_date IN ($in) AND m.status IN (1, 2) ORDER BY m.id", array_merge([(int)$classId], $dates));
        foreach ($makeups as $m) {
            $sid = (int)$m->student_id;
            if (!isset($students[$sid])) {
                $students[$sid] = (object)['student_id' => $sid, 'name' => $m->name, 'lms_code' => $m->lms_code, 'is_makeup' => 1, 'makeup_from' => $m->from_class, 'attendance' => []];
            }
            $schId = $schByDate[$m->to_class_date] ?? null;
            if (!$schId || isset($students[$sid]->attendance[$schId])) continue;
            $students[$sid]->attendance[$schId] = (object)[
                'shs_id' => null, 'note' => null, 'is_makeup' => 1, 'makeup_id' => (int)$m->id, 'from_class' => $m->from_class,
                'status' => (int)$m->status === 2 ? 1 : 0,
            ];
            if ($students[$sid]->is_makeup && !$students[$sid]->makeup_from) $students[$sid]->makeup_from = $m->from_class;
        }
        return $students;
    }

    /**
     * @param int|null $onlyStudentId null = cả lớp; >0 = chỉ 1 học sinh (link riêng PH); -1 = không kèm học sinh (link chung lớp)
     */
    private function buildDetail($sch, $forPublic = false, $onlyStudentId = null)
    {
        $classId = (int)$sch->class_id;
        $teacherId = (int)($sch->schedule_teacher_id ?: $sch->class_teacher_id);
        $week = $this->weekSessions($classId, $sch->class_date);
        list($weekStart, $weekEnd) = $this->weekRange($sch->class_date);
        $weekNo = $this->weekNo($classId, $sch->class_date);

        $users = [];
        $ids = array_filter([$teacherId, (int)$sch->ta_id, (int)$sch->cm_id]);
        if ($ids) {
            foreach (u::query("SELECT id, name FROM users WHERE id IN (" . implode(',', $ids) . ")") as $usr) $users[$usr->id] = $usr->name;
        }
        $branch = u::first("SELECT name, address, hotline FROM branches WHERE id = " . (int)$sch->branch_id);
        $totalWeeks = DB::selectOne("SELECT COUNT(DISTINCT YEARWEEK(class_date, 3)) AS n, COUNT(*) AS total FROM schedules WHERE class_id = ? AND status = 1", [$classId]);

        $report = DB::selectOne("SELECT * FROM lesson_reports WHERE schedule_id = ?", [(int)$sch->schedule_id]);
        if (!$report) {
            // Mặc định giờ học lấy từ báo cáo gần nhất của lớp
            $last = DB::selectOne("SELECT start_time, end_time FROM lesson_reports WHERE class_id = ? AND class_date < ? ORDER BY class_date DESC LIMIT 1", [$classId, $sch->class_date]);
            $report = (object)[
                'id' => null, 'lesson_title' => '', 'general_comment' => '', 'homework_deadline' => '',
                'start_time' => $last->start_time ?? '', 'end_time' => $last->end_time ?? '',
                'status' => null, 'share_token' => null, 'sent_at' => null,
            ];
        }

        $saved = [];
        if ($report->id) {
            foreach (DB::select("SELECT student_id, homework_done, homework_score, comment, share_token FROM lesson_report_students WHERE lesson_report_id = ?", [$report->id]) as $row) {
                $saved[$row->student_id] = $row;
            }
        }

        $students = $onlyStudentId === -1 ? [] : $this->weekStudents($classId, $week);
        if ($onlyStudentId > 0) {
            $students = isset($students[$onlyStudentId]) ? [$onlyStudentId => $students[$onlyStudentId]] : [];
        }
        foreach ($students as $sid => $st) {
            $sv = $saved[$sid] ?? null;
            $st->homework_done = $sv && $sv->homework_done !== null ? (int)$sv->homework_done : null;
            $st->homework_score = $sv && $sv->homework_score !== null ? (float)$sv->homework_score : null;
            $st->comment = $sv->comment ?? '';
            // Tổng hợp điểm danh của tuần: 1 có mặt ≥ 1 buổi, 2 vắng tất cả các buổi đã điểm danh, 0 chưa điểm danh
            $present = 0; $absent = 0; $pending = 0;
            foreach ($st->attendance as $a) {
                if ($a->status === 1) $present++; elseif ($a->status === 2) $absent++; else $pending++;
            }
            $st->present_count = $present;
            $st->attendance_status = $present ? 1 : ($absent && !$pending ? 2 : 0);
            if ($forPublic) {
                unset($st->lms_code);
                foreach ($st->attendance as $a) unset($a->shs_id, $a->note, $a->makeup_id);
            } else {
                $st->share_token = $sv->share_token ?? null;
            }
            $st->attendance = (object)$st->attendance; // giữ key schedule_id khi encode JSON
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
            'product_id' => (int)$sch->product_id,
            'cls_name' => $sch->cls_name,
            'class_date' => $sch->class_date,
            'subject_stt' => $sch->subject_stt,
            'week_start' => $weekStart,
            'week_end' => $weekEnd,
            'week_no' => $weekNo,
            'total_weeks' => $totalWeeks ? (int)$totalWeeks->n : 0,
            'total_sessions' => $totalWeeks ? (int)$totalWeeks->total : 0,
            'sessions' => array_map(function ($w) {
                return ['schedule_id' => (int)$w->schedule_id, 'class_date' => $w->class_date, 'subject_stt' => $w->subject_stt];
            }, $week),
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
        $reportSch = $this->reportScheduleOf($sch);
        if ((int)$reportSch->schedule_id !== (int)$sch->schedule_id) {
            // Buổi đầu tuần → chuyển sang phiếu tuần (gắn buổi cuối tuần)
            return response()->json((object)['status' => 1, 'message' => '', 'data' => ['redirect_schedule_id' => (int)$reportSch->schedule_id]]);
        }
        $scope = $this->scope();
        $data = $this->buildDetail($sch);
        $data->plan = $this->findPlan($sch->product_id, $data->session->week_no);
        $data->can_edit_plan = !$scope->teacher_only;

        // Điều hướng theo tuần: buổi cuối của tuần trước / tuần sau (trong phạm vi được xem)
        $classId = (int)$sch->class_id;
        list($ws, $we) = $this->weekRange($sch->class_date);
        $teacherCond = $scope->teacher_only
            ? " AND (s.teacher_id = {$scope->user_id} OR cl.teacher_id = {$scope->user_id} OR cl.ta_id = {$scope->user_id})" : "";
        $prev = u::first("SELECT s.id FROM schedules AS s JOIN classes AS cl ON cl.id = s.class_id WHERE s.class_id = $classId AND s.status = 1 AND s.class_date < '$ws' $teacherCond ORDER BY s.class_date DESC, s.id DESC LIMIT 1");
        $next = u::first("SELECT s.id FROM schedules AS s JOIN classes AS cl ON cl.id = s.class_id WHERE s.class_id = $classId AND s.status = 1 AND s.class_date > '$we' $teacherCond ORDER BY s.class_date, s.id LIMIT 1");
        $data->prev_schedule_id = $prev ? (int)$prev->id : null;
        $data->next_schedule_id = $next ? (int)$next->id : null; // buổi đầu tuần sau → show() tự chuyển sang buổi cuối tuần
        return response()->json((object)['status' => 1, 'message' => '', 'data' => $data]);
    }

    /** Tạo (nếu chưa có) và trả về id báo cáo của tuần ($sch = buổi cuối tuần) */
    private function ensureReport($sch)
    {
        $existing = DB::selectOne("SELECT id, status FROM lesson_reports WHERE schedule_id = ?", [(int)$sch->schedule_id]);
        if ($existing) return $existing;
        $now = date('Y-m-d H:i:s');
        list($weekStart) = $this->weekRange($sch->class_date);
        $last = DB::selectOne("SELECT start_time, end_time FROM lesson_reports WHERE class_id = ? AND class_date < ? ORDER BY class_date DESC LIMIT 1", [(int)$sch->class_id, $sch->class_date]);
        DB::insert("INSERT INTO lesson_reports (schedule_id, class_id, branch_id, class_date, week_start, week_no, subject_stt, start_time, end_time, teacher_id, ta_id, cm_id, status, share_token, creator_id, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE id = id", [
            (int)$sch->schedule_id, (int)$sch->class_id, (int)$sch->branch_id, $sch->class_date, $weekStart, $this->weekNo($sch->class_id, $sch->class_date), $sch->subject_stt,
            $last->start_time ?? null, $last->end_time ?? null,
            (int)($sch->schedule_teacher_id ?: $sch->class_teacher_id) ?: null, $sch->ta_id ?: null, $sch->cm_id ?: null,
            Str::random(32), Auth::user()->id, $now, $now,
        ]);
        return DB::selectOne("SELECT id, status FROM lesson_reports WHERE schedule_id = ?", [(int)$sch->schedule_id]);
    }

    /** Học sinh hợp lệ của tuần: student_id => [shs, mk] */
    private function validWeekStudents($sch)
    {
        $valid = [];
        foreach ($this->weekStudents($sch->class_id, $this->weekSessions($sch->class_id, $sch->class_date)) as $sid => $st) {
            $shs = null; $mk = null;
            foreach ($st->attendance as $a) {
                if (!$shs && !empty($a->shs_id)) $shs = (int)$a->shs_id;
                if (!$mk && !empty($a->makeup_id)) $mk = (int)$a->makeup_id;
            }
            $valid[$sid] = ['shs' => $shs, 'mk' => $shs ? null : $mk];
        }
        return $valid;
    }

    /* ======================= Lưu (autosave từng phần) ======================= */
    public function save(Request $request)
    {
        if (!$this->tableReady()) return $this->notReady();
        $sch = $this->getSchedule($request->input('schedule_id'));
        if (!$sch) {
            return response()->json((object)['status' => 0, 'message' => 'Không tìm thấy buổi học hoặc bạn không có quyền', 'data' => null]);
        }
        $sch = $this->reportScheduleOf($sch);
        $now = date('Y-m-d H:i:s');
        $userId = Auth::user()->id;

        // Validate học sinh trước khi ghi
        $studentsIn = $request->input('students');
        $rowsToSave = [];
        if (is_array($studentsIn) && count($studentsIn)) {
            $valid = $this->validWeekStudents($sch);
            foreach ($studentsIn as $st) {
                $sid = (int)data_get($st, 'student_id');
                if (!isset($valid[$sid])) {
                    return response()->json((object)['status' => 0, 'message' => 'Học sinh không thuộc lớp trong tuần này', 'data' => null]);
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

    /** Đảm bảo mọi học sinh của tuần có dòng + link riêng; trả về [student_id => token] */
    private function ensureStudentTokens($sch, $reportId)
    {
        $now = date('Y-m-d H:i:s');
        foreach ($this->validWeekStudents($sch) as $sid => $v) {
            DB::insert("INSERT INTO lesson_report_students (lesson_report_id, student_id, schedule_has_student_id, make_up_session_id, is_makeup, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE id = id", [$reportId, $sid, $v['shs'], $v['mk'], $v['mk'] ? 1 : 0, $now, $now]);
        }
        foreach (DB::select("SELECT id FROM lesson_report_students WHERE lesson_report_id = ? AND share_token IS NULL", [$reportId]) as $row) {
            DB::update("UPDATE lesson_report_students SET share_token = ? WHERE id = ?", [Str::random(32), $row->id]);
        }
        $tokens = [];
        foreach (DB::select("SELECT student_id, share_token FROM lesson_report_students WHERE lesson_report_id = ?", [$reportId]) as $row) {
            $tokens[(int)$row->student_id] = $row->share_token;
        }
        return $tokens;
    }

    /* ======================= Trạng thái ======================= */
    public function updateStatus(Request $request)
    {
        if (!$this->tableReady()) return $this->notReady();
        $sch = $this->getSchedule($request->input('schedule_id'));
        if (!$sch) {
            return response()->json((object)['status' => 0, 'message' => 'Không tìm thấy buổi học hoặc bạn không có quyền', 'data' => null]);
        }
        $sch = $this->reportScheduleOf($sch);
        $status = (int)$request->input('status');
        if (!in_array($status, [self::STATUS_DRAFT, self::STATUS_DONE, self::STATUS_SENT], true)) {
            return response()->json((object)['status' => 0, 'message' => 'Trạng thái không hợp lệ', 'data' => null]);
        }
        $report = $this->ensureReport($sch);
        $full = DB::selectOne("SELECT * FROM lesson_reports WHERE id = ?", [$report->id]);
        if ($status >= self::STATUS_DONE && $this->emptyHtml($full->general_comment) && trim((string)$full->lesson_title) === '') {
            return response()->json((object)['status' => 0, 'message' => 'Vui lòng nhập tên bài học hoặc nội dung bài học trước khi hoàn thành', 'data' => null]);
        }
        $update = ['status' => $status, 'updator_id' => Auth::user()->id, 'updated_at' => date('Y-m-d H:i:s')];
        if (!$full->share_token) $update['share_token'] = Str::random(32);
        if ($status === self::STATUS_SENT) {
            $update['sent_at'] = date('Y-m-d H:i:s');
            $update['sent_by'] = Auth::user()->id;
        }
        DB::table('lesson_reports')->where('id', $report->id)->update($update);
        $tokens = $status >= self::STATUS_DONE ? $this->ensureStudentTokens($sch, $report->id) : [];
        $token = $update['share_token'] ?? $full->share_token;
        $messages = [self::STATUS_DRAFT => 'Đã mở lại để chỉnh sửa', self::STATUS_DONE => 'Đã đánh dấu hoàn thành', self::STATUS_SENT => 'Đã đánh dấu gửi phụ huynh'];
        return response()->json((object)['status' => 1, 'message' => $messages[$status], 'data' => ['status' => $status, 'share_token' => $token, 'student_tokens' => (object)$tokens]]);
    }

    public function copySources(Request $request, $schedule_id)
    {
        if (!$this->tableReady()) return $this->notReady();
        $sch = $this->getSchedule($schedule_id);
        if (!$sch) {
            return response()->json((object)['status' => 0, 'message' => 'Không tìm thấy buổi học hoặc bạn không có quyền', 'data' => []]);
        }
        $sch = $this->reportScheduleOf($sch);
        $scope = $this->scope();
        $weekNo = $this->weekNo($sch->class_id, $sch->class_date);
        $rows = u::query("SELECT lr.schedule_id, lr.class_date, lr.week_no, lr.subject_stt, lr.lesson_title, lr.general_comment, lr.homework_deadline, cl.cls_name,
                (lr.class_id = " . (int)$sch->class_id . ") AS same_class
            FROM lesson_reports AS lr JOIN classes AS cl ON cl.id = lr.class_id
            WHERE lr.schedule_id <> " . (int)$sch->schedule_id . " AND lr.general_comment IS NOT NULL
              AND cl.product_id " . ($sch->product_id ? "= " . (int)$sch->product_id : "IS NULL") . "
              AND cl.branch_id IN ({$scope->branch_ids})
              AND lr.class_date >= DATE_SUB(CURRENT_DATE, INTERVAL 180 DAY)
            ORDER BY (lr.week_no = $weekNo) DESC, lr.class_date DESC LIMIT 30");
        return response()->json((object)['status' => 1, 'message' => '', 'data' => $rows]);
    }

    /* ======================= Lộ trình bài học (khóa học × tuần) ======================= */

    private function findPlan($productId, $weekNo)
    {
        if (!(int)$productId || !(int)$weekNo) return null;
        return DB::selectOne("SELECT id, product_id, week_no, lesson_title, content, homework_deadline FROM lesson_plans WHERE product_id = ? AND week_no = ?", [(int)$productId, (int)$weekNo]);
    }

    private function denyPlanEdit()
    {
        return $this->scope()->teacher_only
            ? response()->json((object)['status' => 0, 'message' => 'Bạn không có quyền sửa lộ trình bài học', 'data' => null])
            : null;
    }

    public function planProducts(Request $request)
    {
        if (!$this->tableReady()) return $this->notReady();
        $rows = u::query("SELECT p.id, p.name, (SELECT COUNT(*) FROM lesson_plans AS lp WHERE lp.product_id = p.id) AS plan_count
            FROM products AS p WHERE p.status = 1 ORDER BY p.name");
        return response()->json((object)['status' => 1, 'message' => '', 'data' => $rows, 'can_edit' => !$this->scope()->teacher_only]);
    }

    public function planList(Request $request)
    {
        if (!$this->tableReady()) return $this->notReady();
        $rows = DB::select("SELECT lp.id, lp.product_id, lp.week_no, lp.lesson_title, lp.content, lp.homework_deadline, lp.updated_at,
                (SELECT name FROM users WHERE id = lp.updator_id) AS updator_name
            FROM lesson_plans AS lp WHERE lp.product_id = ? ORDER BY lp.week_no", [(int)$request->input('product_id')]);
        return response()->json((object)['status' => 1, 'message' => '', 'data' => $rows]);
    }

    public function planSave(Request $request)
    {
        if (!$this->tableReady()) return $this->notReady();
        if ($deny = $this->denyPlanEdit()) return $deny;
        $productId = (int)$request->input('product_id');
        $weekNo = (int)$request->input('week_no');
        if (!$productId || !DB::selectOne("SELECT id FROM products WHERE id = ?", [$productId])) {
            return response()->json((object)['status' => 0, 'message' => 'Vui lòng chọn khóa học', 'data' => null]);
        }
        if ($weekNo < 1 || $weekNo > 200) {
            return response()->json((object)['status' => 0, 'message' => 'Tuần phải từ 1 đến 200', 'data' => null]);
        }
        $id = (int)$request->input('id');
        $dup = DB::selectOne("SELECT id FROM lesson_plans WHERE product_id = ? AND week_no = ? AND id <> ?", [$productId, $weekNo, $id]);
        if ($dup && $id) {
            return response()->json((object)['status' => 0, 'message' => "Tuần $weekNo đã có nội dung", 'data' => null]);
        }
        if ($dup) $id = (int)$dup->id; // lưu từ phiếu nhận xét → ghi đè nội dung tuần đã có
        $html = $this->sanitizeHtml($request->input('content'));
        $data = [
            'product_id' => $productId,
            'week_no' => $weekNo,
            'lesson_title' => mb_substr(trim((string)$request->input('lesson_title')), 0, 255) ?: null,
            'content' => $this->emptyHtml($html) ? null : $html,
            'homework_deadline' => mb_substr(trim((string)$request->input('homework_deadline')), 0, 255) ?: null,
            'updator_id' => Auth::user()->id,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        if (!$data['lesson_title'] && !$data['content']) {
            return response()->json((object)['status' => 0, 'message' => 'Vui lòng nhập tên bài học hoặc nội dung', 'data' => null]);
        }
        if ($id) {
            DB::table('lesson_plans')->where('id', $id)->update($data);
        } else {
            $data['creator_id'] = Auth::user()->id;
            $data['created_at'] = $data['updated_at'];
            $id = DB::table('lesson_plans')->insertGetId($data);
        }
        return response()->json((object)['status' => 1, 'message' => "Đã lưu nội dung tuần $weekNo", 'data' => ['id' => $id]]);
    }

    public function planDelete(Request $request)
    {
        if (!$this->tableReady()) return $this->notReady();
        if ($deny = $this->denyPlanEdit()) return $deny;
        DB::delete("DELETE FROM lesson_plans WHERE id = ?", [(int)$request->input('id')]);
        return response()->json((object)['status' => 1, 'message' => 'Đã xóa', 'data' => null]);
    }

    /* ======================= Thời khoá biểu giáo viên (Dashboard → tab Giáo viên) ======================= */

    /**
     * Lịch dạy theo tuần của 1 giáo viên. Phân loại buổi:
     *  main = lớp được phân công dạy chính, substitute = dạy thay lớp của GV khác, ta = trợ giảng,
     *  replaced = lớp của mình nhưng buổi này GV khác dạy thay, holiday = trùng lịch học nhưng nghỉ lễ/tết.
     */
    public function teacherTimetable(Request $request)
    {
        $from = (string)$request->input('from_date');
        $to = (string)$request->input('to_date');
        if (!$this->validDate($from) || !$this->validDate($to) || $from > $to || (strtotime($to) - strtotime($from)) / 86400 > 31) {
            return response()->json((object)['status' => 0, 'message' => 'Khoảng ngày không hợp lệ (tối đa 1 tháng)', 'data' => null]);
        }
        $scope = $this->scope();
        $tid = $scope->teacher_only ? $scope->user_id : ((int)$request->input('teacher_id') ?: $scope->user_id);

        $teachers = [];
        if (!$scope->teacher_only) {
            $teachers = u::query("SELECT DISTINCT u.id, u.name FROM users AS u
                JOIN role_has_user AS ru ON ru.user_id = u.id
                JOIN branch_has_user AS bu ON bu.user_id = u.id
                WHERE u.status = 1 AND ru.role_id IN (36, 37, 54) AND bu.branch_id IN ({$scope->branch_ids}) ORDER BY u.name");
        }
        $teacher = DB::selectOne("SELECT id, name, hrm_id FROM users WHERE id = ?", [$tid]);

        $rows = DB::select("SELECT s.id AS schedule_id, s.class_id, s.class_date, s.subject_stt, s.teacher_id AS schedule_teacher_id,
                cl.cls_name, cl.branch_id, cl.teacher_id AS class_teacher_id, cl.ta_id,
                (SELECT name FROM branches WHERE id = cl.branch_id) AS branch_name,
                (SELECT name FROM products WHERE id = cl.product_id) AS product_name,
                (SELECT name FROM users WHERE id = COALESCE(NULLIF(s.teacher_id, 0), cl.teacher_id)) AS teacher_name,
                (SELECT count(DISTINCT ss.student_id) FROM schedule_has_student AS ss WHERE ss.class_id = s.class_id AND ss.class_date = s.class_date) AS total_students,
                (SELECT count(DISTINCT ss.student_id) FROM schedule_has_student AS ss WHERE ss.class_id = s.class_id AND ss.class_date = s.class_date AND ss.attendance_status IN (1, 3)) AS present,
                (SELECT count(DISTINCT ss.student_id) FROM schedule_has_student AS ss WHERE ss.class_id = s.class_id AND ss.class_date = s.class_date AND ss.attendance_status = 0) AS pending_attendance,
                (SELECT count(DISTINCT c.student_id) FROM contracts AS c WHERE c.class_id = s.class_id AND c.type = 1 AND c.status = 6
                    AND (c.enrolment_start_date IS NULL OR c.enrolment_start_date <= s.class_date) AND (c.enrolment_last_date IS NULL OR c.enrolment_last_date >= s.class_date)) AS expected_students,
                (SELECT s2.id FROM schedules AS s2 WHERE s2.class_id = s.class_id AND s2.status = 1 AND YEARWEEK(s2.class_date, 3) = YEARWEEK(s.class_date, 3)
                    ORDER BY s2.class_date DESC, s2.id DESC LIMIT 1) AS report_schedule_id
            FROM schedules AS s JOIN classes AS cl ON cl.id = s.class_id
            WHERE s.status = 1 AND s.class_date BETWEEN ? AND ? AND cl.branch_id IN ({$scope->branch_ids})
              AND (s.teacher_id = ? OR cl.teacher_id = ? OR cl.ta_id = ?)
            ORDER BY s.class_date, cl.cls_name", [$from, $to, $tid, $tid, $tid]);

        // Trạng thái phiếu nhận xét tuần + giờ học (lấy theo phiếu gần nhất của lớp)
        $reports = []; $times = [];
        if ($rows && $this->tableReady()) {
            $rids = implode(',', array_unique(array_map(function ($r) { return (int)$r->report_schedule_id; }, $rows)));
            foreach (u::query("SELECT schedule_id, status FROM lesson_reports WHERE schedule_id IN ($rids)") as $lr) $reports[(int)$lr->schedule_id] = (int)$lr->status;
            $cids = implode(',', array_unique(array_map(function ($r) { return (int)$r->class_id; }, $rows)));
            foreach (u::query("SELECT lr.class_id, lr.start_time, lr.end_time FROM lesson_reports AS lr
                    JOIN (SELECT class_id, MAX(class_date) AS d FROM lesson_reports WHERE class_id IN ($cids) AND start_time IS NOT NULL GROUP BY class_id) AS x
                      ON x.class_id = lr.class_id AND x.d = lr.class_date") as $t) $times[(int)$t->class_id] = $t;
        }

        $items = [];
        $scheduledDates = [];
        foreach ($rows as $r) {
            $schTeacher = (int)$r->schedule_teacher_id;
            $clsTeacher = (int)$r->class_teacher_id;
            if ($schTeacher === $tid && $clsTeacher !== $tid) $type = 'substitute';
            elseif ($clsTeacher === $tid && $schTeacher && $schTeacher !== $tid) $type = 'replaced';
            elseif ($clsTeacher === $tid || $schTeacher === $tid) $type = 'main';
            else $type = 'ta';
            $scheduledDates[$r->class_id . '|' . $r->class_date] = true;
            $rs = (int)$r->report_schedule_id ?: (int)$r->schedule_id;
            $st = $reports[$rs] ?? null;
            $items[] = [
                'type' => $type,
                'schedule_id' => (int)$r->schedule_id,
                'report_schedule_id' => $rs,
                'is_report_session' => $rs === (int)$r->schedule_id ? 1 : 0,
                'class_id' => (int)$r->class_id,
                'class_date' => $r->class_date,
                'subject_stt' => $r->subject_stt,
                'cls_name' => $r->cls_name,
                'branch_name' => $r->branch_name,
                'product_name' => $r->product_name,
                'teacher_name' => $r->teacher_name,
                'start_time' => $times[(int)$r->class_id]->start_time ?? null,
                'end_time' => $times[(int)$r->class_id]->end_time ?? null,
                'total_students' => (int)$r->total_students,
                'present' => (int)$r->present,
                'pending_attendance' => (int)$r->pending_attendance,
                // Chưa sinh danh sách điểm danh (schedule_has_student) → hiển thị sĩ số dự kiến theo hợp đồng đang học
                'expected_students' => (int)$r->expected_students,
                'roster_missing' => (int)$r->total_students === 0 && (int)$r->expected_students > 0 ? 1 : 0,
                'report_status' => $st === null ? 'none' : ($st === self::STATUS_SENT ? 'sent' : ($st === self::STATUS_DONE ? 'done' : 'draft')),
            ];
        }

        // Buổi nghỉ lễ/tết: ngày trùng lịch cố định của lớp (classes.class_day: 2 = T2 … 8 = CN) nhưng không có buổi học
        $classes = DB::select("SELECT cl.id, cl.cls_name, cl.branch_id, cl.product_id, cl.class_day, (SELECT name FROM branches WHERE id = cl.branch_id) AS branch_name
            FROM classes AS cl WHERE cl.status = 1 AND cl.branch_id IN ({$scope->branch_ids}) AND (cl.teacher_id = ? OR cl.ta_id = ?)
              AND (cl.cls_startdate IS NULL OR cl.cls_startdate <= ?) AND (cl.cls_enddate IS NULL OR cl.cls_enddate >= ?)", [$tid, $tid, $to, $from]);
        $holidays = DB::select("SELECT name, start_date, end_date, products, branch_id FROM public_holiday WHERE status > 0 AND start_date <= ? AND end_date >= ?", [$to, $from]);
        $inList = function ($csv, $id) {
            return in_array((string)$id, array_map('trim', explode(',', (string)$csv)), true);
        };
        if ($holidays) {
            foreach ($classes as $c) {
                $days = array_filter(array_map('intval', explode(',', (string)$c->class_day)));
                for ($d = strtotime($from); $d <= strtotime($to); $d += 86400) {
                    $date = date('Y-m-d', $d);
                    if (!in_array((int)date('N', $d) + 1, $days, true) || isset($scheduledDates[$c->id . '|' . $date])) continue;
                    foreach ($holidays as $h) {
                        if ($date < $h->start_date || $date > $h->end_date || !$inList($h->branch_id, $c->branch_id)) continue;
                        if ($h->products && $c->product_id && !$inList($h->products, $c->product_id)) continue;
                        $items[] = ['type' => 'holiday', 'class_id' => (int)$c->id, 'class_date' => $date, 'cls_name' => $c->cls_name,
                            'branch_name' => $c->branch_name, 'holiday_name' => $h->name];
                        break;
                    }
                }
            }
        }

        $summary = ['main' => 0, 'substitute' => 0, 'ta' => 0, 'replaced' => 0, 'holiday' => 0, 'pending_attendance' => 0, 'report_todo' => 0];
        $today = date('Y-m-d');
        foreach ($items as $it) {
            $summary[$it['type']]++;
            if ($it['type'] === 'holiday' || $it['type'] === 'replaced' || $it['class_date'] > $today) continue;
            if ($it['pending_attendance'] > 0) $summary['pending_attendance']++;
            if ($it['is_report_session'] && in_array($it['report_status'], ['none', 'draft'], true)) $summary['report_todo']++;
        }

        return response()->json((object)['status' => 1, 'message' => '', 'data' => [
            'teacher' => $teacher,
            'teacher_only' => $scope->teacher_only,
            'teachers' => $teachers,
            'items' => $items,
            'summary' => $summary,
        ]]);
    }

    /* ======================= Bản public cho phụ huynh ======================= */
    public function publicReport(Request $request, $token)
    {
        if (!$this->tableReady() || !preg_match('/^[A-Za-z0-9]{32}$/', (string)$token)) {
            return response()->json((object)['status' => 0, 'message' => 'Không tìm thấy báo cáo', 'data' => null]);
        }
        // Ưu tiên link riêng của học sinh; link chung của lớp chỉ hiển thị nội dung bài học
        $onlyStudent = -1;
        $lr = null;
        $own = DB::selectOne("SELECT lesson_report_id, student_id FROM lesson_report_students WHERE share_token = ?", [$token]);
        if ($own) {
            $lr = DB::selectOne("SELECT schedule_id, status FROM lesson_reports WHERE id = ?", [(int)$own->lesson_report_id]);
            $onlyStudent = (int)$own->student_id;
        } else {
            $lr = DB::selectOne("SELECT schedule_id, status FROM lesson_reports WHERE share_token = ?", [$token]);
        }
        if (!$lr) {
            return response()->json((object)['status' => 0, 'message' => 'Không tìm thấy báo cáo', 'data' => null]);
        }
        if ((int)$lr->status < self::STATUS_DONE) {
            return response()->json((object)['status' => 0, 'message' => 'Báo cáo đang được giáo viên hoàn thiện, vui lòng quay lại sau.', 'data' => null]);
        }
        $sch = u::first("SELECT " . $this->scheduleCols() . " FROM schedules AS s JOIN classes AS cl ON cl.id = s.class_id WHERE s.id = " . (int)$lr->schedule_id);
        if (!$sch) {
            return response()->json((object)['status' => 0, 'message' => 'Không tìm thấy báo cáo', 'data' => null]);
        }
        $data = $this->buildDetail($sch, true, $onlyStudent);
        $data->view = $onlyStudent > 0 ? 'student' : 'class';
        if ($onlyStudent > 0) {
            // Không lộ sĩ số / thống kê của cả lớp trên link riêng
            unset($data->session->total_students, $data->session->present, $data->session->homework_done);
        }
        return response()->json((object)['status' => 1, 'message' => '', 'data' => $data]);
    }
}
