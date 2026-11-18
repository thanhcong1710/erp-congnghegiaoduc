<template>
  <div id="page-lesson-reports" class="lr-page mt-5">
    <div v-if="loaded && !ready" class="lr-alert">
      <i class="fa-solid fa-triangle-exclamation"></i>
      Chưa khởi tạo bảng dữ liệu nhận xét. Vui lòng liên hệ quản trị để chạy migration <b>2026_11_18_000001_create_lesson_reports_tables</b> và <b>2026_11_18_000002_lesson_reports_weekly_and_plans</b>.
    </div>

    <!-- ============ BỘ LỌC ============ -->
    <div class="lr-card lr-filter">
      <div class="lr-week">
        <button type="button" class="lr-nav" title="Tuần trước" @click="shiftWeek(-1)"><i class="fa-solid fa-chevron-left"></i></button>
        <div class="lr-week__label">
          <div class="lr-week__range">{{ weekLabel }}</div>
          <div class="lr-week__sub">{{ isCurrentWeek ? 'Tuần này' : 'Tuần ' + weekStart.isoWeek() }}</div>
        </div>
        <button type="button" class="lr-nav" title="Tuần sau" @click="shiftWeek(1)"><i class="fa-solid fa-chevron-right"></i></button>
        <button type="button" class="lr-today" v-if="!isCurrentWeek" @click="goToday">Hôm nay</button>
      </div>
      <button type="button" class="lr-today" @click="$router.push('/lms/lesson-plans')"><i class="fa-solid fa-route mr-1"></i>Lộ trình bài học</button>

      <div class="lr-filter__fields">
        <div class="lr-field" v-if="branches.length > 1">
          <vue-select label="name" placeholder="Tất cả cơ sở" :options="branches" v-model="filter.branch" @input="onBranch"></vue-select>
        </div>
        <div class="lr-field grow">
          <vue-select label="cls_name" placeholder="Tất cả lớp" :options="classOptions" v-model="filter.cls" @input="persistAndLoad"></vue-select>
        </div>
        <div class="lr-field" v-if="!teacherOnly">
          <vue-select label="name" placeholder="Tất cả giáo viên" :options="teachers" v-model="filter.teacher" @input="persistAndLoad"></vue-select>
        </div>
      </div>
    </div>

    <!-- ============ KPI / trạng thái ============ -->
    <div class="lr-chips">
      <button
        v-for="s in statusTabs" :key="s.value" type="button"
        class="lr-chip" :class="[s.value, { active: filter.status === s.value }]"
        @click="setStatus(s.value)"
      >
        <i :class="s.icon"></i>{{ s.label }}<span class="lr-chip__n">{{ counts[s.value] || 0 }}</span>
      </button>
    </div>

    <!-- ============ LỊCH TUẦN ============ -->
    <div class="lr-week-grid">
      <div v-for="day in days" :key="day.date" class="lr-day" :class="{ today: day.date === today, past: day.date < today }">
        <div class="lr-day__head">
          <span class="lr-day__dow">{{ day.dow }}</span>
          <span class="lr-day__date">{{ day.label }}</span>
          <span v-if="day.date === today" class="lr-day__tag">Hôm nay</span>
        </div>
        <div class="lr-day__body">
          <div
            v-for="s in sessionsByDay[day.date]" :key="s.schedule_id"
            class="lr-session" :class="s.is_report_session ? 'st-' + s.ui_status : 'st-merged'"
            @click="open(s)"
          >
            <div class="lr-session__top">
              <div class="lr-session__cls" :title="s.cls_name">{{ s.cls_name }}</div>
              <span v-if="s.is_report_session" class="lr-badge" :class="s.ui_status">{{ statusLabel[s.ui_status] }}</span>
              <span v-else class="lr-badge merged">Chỉ điểm danh</span>
            </div>
            <div class="lr-session__meta">
              <span><i class="fa-solid fa-book-open"></i>Buổi {{ s.subject_stt || '?' }}<template v-if="s.lesson_title"> · {{ s.lesson_title }}</template></span>
            </div>
            <div class="lr-session__meta" v-if="s.week_session_count > 1">
              <span v-if="s.is_report_session"><i class="fa-solid fa-layer-group"></i>Phiếu tuần · gộp {{ s.week_session_count }} buổi</span>
              <span v-else><i class="fa-solid fa-share"></i>Nhận xét gộp vào buổi {{ fmtShort(s.report_date) }}</span>
            </div>
            <div class="lr-session__meta" v-if="s.start_time || !teacherOnly">
              <span v-if="s.start_time"><i class="fa-regular fa-clock"></i>{{ s.start_time }}<template v-if="s.end_time"> – {{ s.end_time }}</template></span>
              <span v-if="!teacherOnly"><i class="fa-solid fa-chalkboard-user"></i>{{ s.teacher_name || '—' }}</span>
            </div>
            <div class="lr-session__att" :class="{ warn: s.pending_attendance > 0 && s.class_date <= today }">
              <i class="fa-solid fa-clipboard-user"></i>
              <template v-if="s.pending_attendance > 0 && s.class_date <= today">Chưa điểm danh {{ s.pending_attendance }} HS</template>
              <template v-else>Điểm danh {{ s.present }}/{{ s.total_students }}</template>
            </div>
            <template v-if="s.is_report_session">
              <div class="lr-progress" :title="'Đã nhận xét ' + s.commented + '/' + s.total_students">
                <div class="lr-progress__bar" :style="{ width: pct(s) + '%' }"></div>
              </div>
              <div class="lr-session__foot">
                <span>Nhận xét {{ s.commented }}/{{ s.total_students }}</span>
                <span class="lr-session__go">{{ s.ui_status === 'none' ? 'Bắt đầu' : 'Mở' }} <i class="fa-solid fa-arrow-right"></i></span>
              </div>
            </template>
            <div v-else class="lr-session__foot">
              <span></span>
              <span class="lr-session__go">Điểm danh <i class="fa-solid fa-arrow-right"></i></span>
            </div>
          </div>
          <div v-if="!sessionsByDay[day.date] || !sessionsByDay[day.date].length" class="lr-day__empty">Không có buổi</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import select from 'vue-select'
import moment from 'moment'
import axios from '../../../http/axios.js'

const LS_KEY = 'lesson_reports_filter'
const DOW = ['CN', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7']

export default {
  components: { 'vue-select': select },
  data() {
    return {
      loaded: false,
      ready: true,
      teacherOnly: false,
      branches: [],
      classes: [],
      teachers: [],
      sessions: [],
      today: moment().format('YYYY-MM-DD'),
      weekStart: moment().startOf('isoWeek'),
      filter: { branch: null, cls: null, teacher: null, status: 'all' },
      statusTabs: [
        { value: 'all', label: 'Tất cả buổi', icon: 'fa-solid fa-layer-group' },
        { value: 'todo', label: 'Cần nhận xét', icon: 'fa-solid fa-pen-to-square' },
        { value: 'draft', label: 'Đang làm', icon: 'fa-solid fa-hourglass-half' },
        { value: 'done', label: 'Hoàn thành', icon: 'fa-solid fa-circle-check' },
        { value: 'sent', label: 'Đã gửi PH', icon: 'fa-solid fa-paper-plane' }
      ],
      statusLabel: { none: 'Chưa nhận xét', draft: 'Đang làm', done: 'Hoàn thành', sent: 'Đã gửi PH' }
    }
  },
  computed: {
    weekEnd() { return this.weekStart.clone().add(6, 'days') },
    isCurrentWeek() { return this.weekStart.isSame(moment().startOf('isoWeek'), 'day') },
    weekLabel() { return `${this.weekStart.format('DD/MM')} – ${this.weekEnd.format('DD/MM/YYYY')}` },
    days() {
      return Array.from({ length: 7 }, (_, i) => {
        const d = this.weekStart.clone().add(i, 'days')
        return { date: d.format('YYYY-MM-DD'), dow: DOW[d.day()], label: d.format('DD/MM') }
      })
    },
    classOptions() {
      const b = this.filter.branch
      return b ? this.classes.filter(c => c.branch_id == b.id) : this.classes
    },
    reportSessions() { return this.sessions.filter(s => s.is_report_session) },
    counts() {
      const c = { all: this.sessions.length, todo: 0, draft: 0, done: 0, sent: 0 }
      this.reportSessions.forEach(s => {
        if (s.ui_status === 'none' && s.class_date <= this.today) c.todo++
        if (s.ui_status === 'draft') { c.draft++; if (s.class_date <= this.today) c.todo++ }
        if (s.ui_status === 'done') c.done++
        if (s.ui_status === 'sent') c.sent++
      })
      return c
    },
    filteredSessions() {
      const st = this.filter.status
      if (st === 'all') return this.sessions
      if (st === 'todo') return this.reportSessions.filter(s => ['none', 'draft'].includes(s.ui_status) && s.class_date <= this.today)
      return this.reportSessions.filter(s => s.ui_status === st)
    },
    sessionsByDay() {
      const map = {}
      this.filteredSessions.forEach(s => { (map[s.class_date] = map[s.class_date] || []).push(s) })
      Object.keys(map).forEach(k => map[k].sort((a, b) => (a.start_time || '99').localeCompare(b.start_time || '99') || a.cls_name.localeCompare(b.cls_name)))
      return map
    }
  },
  created() {
    const saved = this.readSaved()
    if (saved.week && moment(saved.week, 'YYYY-MM-DD', true).isValid()) this.weekStart = moment(saved.week).startOf('isoWeek')
    if (saved.status) this.filter.status = saved.status
    axios.g('/api/lms/lesson-reports/filters').then(res => {
      const d = (res.data && res.data.data) || {}
      this.branches = d.branches || []
      this.classes = d.classes || []
      this.teachers = d.teachers || []
      this.teacherOnly = !!d.teacher_only
      this.ready = d.ready !== false
      this.filter.branch = this.branches.find(b => b.id == saved.branch_id) || null
      this.filter.cls = this.classes.find(c => c.id == saved.class_id) || null
      this.filter.teacher = this.teachers.find(t => t.id == saved.teacher_id) || null
      this.loaded = true
      this.load()
    })
  },
  methods: {
    readSaved() {
      try { return JSON.parse(localStorage.getItem(LS_KEY)) || {} } catch (e) { return {} }
    },
    persist() {
      try {
        localStorage.setItem(LS_KEY, JSON.stringify({
          branch_id: this.filter.branch && this.filter.branch.id,
          class_id: this.filter.cls && this.filter.cls.id,
          teacher_id: this.filter.teacher && this.filter.teacher.id,
          status: this.filter.status,
          week: this.weekStart.format('YYYY-MM-DD')
        }))
      } catch (e) { /* ignore */ }
    },
    persistAndLoad() { this.persist(); this.load() },
    onBranch() {
      if (this.filter.cls && this.filter.branch && this.filter.cls.branch_id != this.filter.branch.id) this.filter.cls = null
      this.persistAndLoad()
    },
    setStatus(v) { this.filter.status = v; this.persist() },
    shiftWeek(step) { this.weekStart = this.weekStart.clone().add(step, 'weeks'); this.persistAndLoad() },
    goToday() { this.weekStart = moment().startOf('isoWeek'); this.persistAndLoad() },
    fmtShort(d) { const m = moment(d); return m.isValid() ? `${DOW[m.day()]} ${m.format('DD/MM')}` : '' },
    pct(s) { return s.total_students ? Math.min(100, Math.round(s.commented * 100 / s.total_students)) : 0 },
    load() {
      this.$vs.loading()
      axios.p('/api/lms/lesson-reports/sessions', {
        from_date: this.weekStart.format('YYYY-MM-DD'),
        to_date: this.weekEnd.format('YYYY-MM-DD'),
        branch_id: this.filter.branch ? this.filter.branch.id : '',
        class_id: this.filter.cls ? this.filter.cls.id : '',
        teacher_id: this.filter.teacher ? this.filter.teacher.id : ''
      }).then(res => {
        this.$vs.loading.close()
        const d = res.data || {}
        if (d.status === 0) {
          this.$vs.notify({ title: 'Lỗi', text: d.message, color: 'danger' })
          this.sessions = []
          return
        }
        this.ready = d.ready !== false
        this.sessions = d.data || []
      }).catch(() => this.$vs.loading.close())
    },
    open(s) {
      // Mọi buổi trong tuần đều mở phiếu tuần (gắn buổi cuối tuần) – điểm danh từng buổi nằm trong phiếu
      this.$router.push(`/lms/lesson-reports/${s.report_schedule_id || s.schedule_id}`)
    }
  }
}
</script>

<style>
.lr-page { font-size: 14px; color: #334155; display: flex; flex-direction: column; gap: 16px; }
.lr-card { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(15, 23, 42, 0.06); padding: 16px 20px; }
.lr-alert { background: #fff7ed; border: 1px solid #fed7aa; color: #9a3412; border-radius: 10px; padding: 12px 16px; font-size: 14px; }
.lr-alert i { margin-right: 8px; }

/* Filter */
.lr-filter { display: flex; align-items: center; gap: 18px; flex-wrap: wrap; }
.lr-filter__fields { display: flex; gap: 12px; flex: 1; flex-wrap: wrap; justify-content: flex-end; }
.lr-field { width: 220px; }
.lr-field.grow { flex: 1; min-width: 240px; max-width: 360px; }
.lr-week { display: flex; align-items: center; gap: 8px; }
.lr-week__label { min-width: 170px; text-align: center; }
.lr-week__range { font-size: 17px; font-weight: 700; color: #1e293b; white-space: nowrap; }
.lr-week__sub { font-size: 13px; color: #64748b; }
.lr-nav { width: 40px; height: 40px; border-radius: 8px; border: 1.5px solid #cbd5e1; background: #fff; color: #475569; cursor: pointer; font-size: 14px; transition: all .15s; }
.lr-nav:hover { border-color: #7367f0; color: #7367f0; box-shadow: 0 0 0 3px rgba(115, 103, 240, 0.2); }
.lr-today { border: none; background: rgba(115, 103, 240, 0.1); color: #5e50ee; font-weight: 600; font-size: 13px; padding: 0 12px; height: 40px; border-radius: 8px; cursor: pointer; white-space: nowrap; }
.lr-page .v-select .vs__dropdown-toggle { min-height: 40px; background: #fff; border: 1.5px solid #cbd5e1; border-radius: 8px; cursor: pointer; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); }
.lr-page .v-select .vs__dropdown-toggle:hover, .lr-page .v-select.vs--open .vs__dropdown-toggle { border-color: #7367f0; box-shadow: 0 0 0 3px rgba(115, 103, 240, 0.2); }
.lr-page .v-select .vs__search, .lr-page .v-select .vs__search:focus { border: none !important; background: transparent !important; box-shadow: none !important; font-size: 14px; }
.lr-page .v-select .vs__selected { font-size: 14px; }

/* Chips */
.lr-chips { display: flex; gap: 10px; flex-wrap: wrap; }
.lr-chip { display: inline-flex; align-items: center; gap: 8px; white-space: nowrap; border: 1.5px solid #e2e8f0; background: #fff; color: #475569; font-size: 14px; font-weight: 600; padding: 8px 14px; border-radius: 99px; cursor: pointer; transition: all .15s; }
.lr-chip:hover { border-color: #7367f0; }
.lr-chip__n { background: #f1f5f9; color: #334155; border-radius: 99px; padding: 0 8px; font-size: 13px; min-width: 24px; text-align: center; }
.lr-chip.active { background: #7367f0; border-color: #7367f0; color: #fff; }
.lr-chip.active .lr-chip__n { background: rgba(255, 255, 255, 0.25); color: #fff; }
.lr-chip.todo:not(.active) .lr-chip__n { background: #fee2e2; color: #b91c1c; }

/* Week grid */
.lr-week-grid { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); gap: 10px; }
@media (max-width: 1400px) { .lr-week-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); } }
@media (max-width: 992px) { .lr-week-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
@media (max-width: 600px) { .lr-week-grid { grid-template-columns: 1fr; } .lr-filter__fields { justify-content: stretch; } .lr-field, .lr-field.grow { width: 100%; max-width: none; } }
.lr-day { background: #f8fafc; border: 1px solid #eef2f7; border-radius: 12px; display: flex; flex-direction: column; min-height: 160px; }
.lr-day.today { border-color: #7367f0; box-shadow: 0 0 0 2px rgba(115, 103, 240, 0.15); background: #faf9ff; }
.lr-day__head { display: flex; align-items: baseline; gap: 8px; padding: 10px 12px; border-bottom: 1px solid #eef2f7; }
.lr-day__dow { font-size: 15px; font-weight: 700; color: #1e293b; }
.lr-day__date { font-size: 13px; color: #64748b; font-weight: 600; }
.lr-day__tag { margin-left: auto; font-size: 13px; font-weight: 700; color: #fff; background: #7367f0; border-radius: 99px; padding: 0 8px; white-space: nowrap; }
.lr-day__body { padding: 10px; display: flex; flex-direction: column; gap: 10px; }
.lr-day__empty { color: #cbd5e1; font-size: 13px; text-align: center; padding: 18px 0; }

.lr-session { background: #fff; border: 1px solid #e2e8f0; border-left: 4px solid #cbd5e1; border-radius: 10px; padding: 10px 12px; cursor: pointer; transition: transform .12s, box-shadow .12s, border-color .12s; }
.lr-session:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(15, 23, 42, 0.1); border-color: #7367f0; }
.lr-session.st-none { border-left-color: #ef4444; }
.lr-session.st-draft { border-left-color: #f59e0b; }
.lr-session.st-done { border-left-color: #22c55e; }
.lr-session.st-sent { border-left-color: #7367f0; }
.lr-session.st-merged { border-left-color: #cbd5e1; border-left-style: dashed; background: #fcfcfd; }
.lr-session.st-merged .lr-session__cls { color: #475569; }
.lr-session__top { display: flex; align-items: flex-start; justify-content: space-between; gap: 6px; }
.lr-session__cls { font-size: 15px; font-weight: 700; color: #1e293b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.lr-session__meta { display: flex; flex-wrap: wrap; gap: 2px 12px; font-size: 13px; color: #64748b; margin-top: 4px; }
.lr-session__meta i { color: #94a3b8; margin-right: 5px; }
.lr-session__att { font-size: 13px; color: #16a34a; font-weight: 600; margin-top: 6px; }
.lr-session__att.warn { color: #ea580c; }
.lr-session__att i { margin-right: 5px; }
.lr-progress { height: 6px; background: #f1f5f9; border-radius: 99px; overflow: hidden; margin-top: 8px; }
.lr-progress__bar { height: 100%; background: linear-gradient(90deg, #7367f0, #9e95f5); border-radius: 99px; transition: width .3s; }
.lr-session__foot { display: flex; justify-content: space-between; align-items: center; font-size: 13px; color: #64748b; margin-top: 6px; }
.lr-session__go { color: #7367f0; font-weight: 700; white-space: nowrap; }

.lr-badge { display: inline-flex !important; align-items: center; white-space: nowrap !important; font-size: 13px; font-weight: 600; border-radius: 99px; padding: 2px 8px; flex-shrink: 0; }
.lr-badge.none { background: #fee2e2; color: #b91c1c; }
.lr-badge.draft { background: #fef3c7; color: #b45309; }
.lr-badge.done { background: #dcfce7; color: #15803d; }
.lr-badge.sent { background: rgba(115, 103, 240, 0.12); color: #5e50ee; }
.lr-badge.merged { background: #f1f5f9; color: #64748b; }
</style>
