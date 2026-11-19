<template>
  <div class="tt-page">
    <!-- ============ Thanh công cụ ============ -->
    <div class="tt-card tt-toolbar">
      <div class="tt-week">
        <button type="button" class="tt-nav" title="Tuần trước" @click="shiftWeek(-1)"><i class="fa-solid fa-chevron-left"></i></button>
        <div class="tt-week__label">
          <div class="tt-week__range">{{ weekLabel }}</div>
          <div class="tt-week__sub">{{ isCurrentWeek ? 'Tuần này' : 'Tuần ' + weekStart.isoWeek() }}</div>
        </div>
        <button type="button" class="tt-nav" title="Tuần sau" @click="shiftWeek(1)"><i class="fa-solid fa-chevron-right"></i></button>
        <button type="button" class="tt-today" v-if="!isCurrentWeek" @click="goToday">Hôm nay</button>
      </div>

      <div class="tt-teacher">
        <div class="tt-teacher__avatar"><i class="fa-solid fa-chalkboard-user"></i></div>
        <div v-if="teacherId || teacherOnly || !teachers.length" class="tt-teacher__name">{{ teacher ? teacher.name : '—' }}</div>
        <div v-else class="tt-teacher__select">
          <vue-select label="name" placeholder="Chọn giáo viên" :options="teachers" v-model="selectedTeacher" :clearable="false" @input="onTeacher"></vue-select>
        </div>
      </div>

      <div class="tt-kpis">
        <div class="tt-kpi"><div class="tt-kpi__k">Buổi dạy</div><div class="tt-kpi__v">{{ teachingCount }}</div></div>
        <div class="tt-kpi" :class="{ warn: summary.pending_attendance }"><div class="tt-kpi__k">Chưa điểm danh</div><div class="tt-kpi__v">{{ summary.pending_attendance || 0 }}</div></div>
        <div class="tt-kpi" :class="{ danger: summary.report_todo }"><div class="tt-kpi__k">Phiếu cần nhận xét</div><div class="tt-kpi__v">{{ summary.report_todo || 0 }}</div></div>
      </div>
    </div>

    <!-- ============ Chú thích màu ============ -->
    <div class="tt-legend">
      <span v-for="t in legend" :key="t.key" class="tt-legend__item" :class="'ty-' + t.key">
        <i class="tt-dot"></i>{{ t.label }}<b>{{ summary[t.key] || 0 }}</b>
      </span>
    </div>

    <!-- ============ Lịch tuần ============ -->
    <div class="tt-grid">
      <div v-for="day in days" :key="day.date" class="tt-day" :class="{ today: day.date === today }">
        <div class="tt-day__head">
          <span class="tt-day__dow">{{ day.dow }}</span>
          <span class="tt-day__date">{{ day.label }}</span>
          <span v-if="day.date === today" class="tt-day__tag">Hôm nay</span>
        </div>
        <div class="tt-day__body">
          <div
            v-for="it in itemsByDay[day.date]" :key="it.type + '-' + (it.schedule_id || it.class_id)"
            class="tt-item" :class="['ty-' + it.type, isFuture(it) ? 'future' : '', { clickable: it.type !== 'holiday' && !isFuture(it) }]"
            :title="isFuture(it) ? 'Buổi học chưa diễn ra – chưa thể điểm danh / nhận xét' : ''"
            @click="open(it)"
          >
            <div class="tt-item__top">
              <span class="tt-item__cls" :title="it.cls_name">{{ it.cls_name }}</span>
              <span class="tt-type">{{ typeLabel[it.type] }}</span>
            </div>
            <template v-if="it.type === 'holiday'">
              <div class="tt-item__meta"><i class="fa-solid fa-umbrella-beach"></i>{{ it.holiday_name }}</div>
              <div class="tt-item__meta"><i class="fa-solid fa-building"></i><span class="nowrap">{{ it.branch_name }}</span></div>
            </template>
            <template v-else>
              <div class="tt-item__meta" v-if="it.start_time"><i class="fa-regular fa-clock"></i>{{ it.start_time }}<template v-if="it.end_time"> – {{ it.end_time }}</template></div>
              <div class="tt-item__meta"><i class="fa-solid fa-book-open"></i>Buổi {{ it.subject_stt || '?' }}<template v-if="it.product_name"> · {{ it.product_name }}</template></div>
              <div class="tt-item__meta"><i class="fa-solid fa-building"></i><span class="nowrap">{{ it.branch_name }}</span></div>
              <div class="tt-item__meta" v-if="it.type === 'replaced' || it.type === 'ta'"><i class="fa-solid fa-user"></i>GV: {{ it.teacher_name || '—' }}</div>
              <!-- 1 dòng trạng thái gộp điểm danh + nhận xét -->
              <div class="tt-st" :class="itemState(it).cls">
                <i :class="itemState(it).icon"></i><span>{{ itemState(it).text }}</span>
              </div>
            </template>
          </div>
          <div v-if="!itemsByDay[day.date] || !itemsByDay[day.date].length" class="tt-day__empty">Không có lịch</div>
        </div>
      </div>
    </div>
    <div class="tt-hint"><i class="fa-solid fa-circle-info"></i>Bấm vào lớp để mở phiếu tuần: điểm danh từng buổi và nhận xét học viên. Buổi chưa diễn ra chỉ xem, chưa điểm danh/nhận xét được.</div>
  </div>
</template>

<script>
import select from 'vue-select'
import moment from 'moment'
import axios from '../../http/axios.js'

const DOW = ['CN', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7']
const LS_KEY = 'dashboard_teacher_timetable'

export default {
  name: 'TeacherTimetable',
  components: { 'vue-select': select },
  // teacherId: dashboard cha đã chọn tài khoản GV → dùng luôn, ẩn ô chọn riêng
  props: { teacherId: { type: Number, default: null } },
  data() {
    return {
      weekStart: moment().startOf('isoWeek'),
      today: moment().format('YYYY-MM-DD'),
      teacher: null,
      teacherOnly: true,
      teachers: [],
      selectedTeacher: null,
      items: [],
      summary: {},
      legend: [
        { key: 'main', label: 'Dạy chính' },
        { key: 'substitute', label: 'Dạy thay' },
        { key: 'ta', label: 'Trợ giảng' },
        { key: 'replaced', label: 'GV khác phụ trách' },
        { key: 'holiday', label: 'Nghỉ lễ / Tết' }
      ],
      typeLabel: { main: 'Dạy chính', substitute: 'Dạy thay', ta: 'Trợ giảng', replaced: 'GV khác phụ trách', holiday: 'Nghỉ lễ' },
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
    teachingCount() { return (this.summary.main || 0) + (this.summary.substitute || 0) + (this.summary.ta || 0) },
    itemsByDay() {
      const order = { main: 1, substitute: 2, ta: 3, replaced: 4, holiday: 5 }
      const map = {}
      this.items.forEach(it => { (map[it.class_date] = map[it.class_date] || []).push(it) })
      Object.keys(map).forEach(k => map[k].sort((a, b) =>
        (a.start_time || '99').localeCompare(b.start_time || '99') || order[a.type] - order[b.type] || a.cls_name.localeCompare(b.cls_name)))
      return map
    }
  },
  watch: {
    teacherId() { this.load() }
  },
  created() {
    if (this.teacherId) return this.load()
    try {
      const saved = JSON.parse(localStorage.getItem(LS_KEY)) || {}
      if (saved.teacher_id) this.selectedTeacher = { id: saved.teacher_id }
    } catch (e) { /* ignore */ }
    this.load()
  },
  methods: {
    shiftWeek(step) { this.weekStart = this.weekStart.clone().add(step, 'weeks'); this.load() },
    goToday() { this.weekStart = moment().startOf('isoWeek'); this.load() },
    onTeacher() {
      try { localStorage.setItem(LS_KEY, JSON.stringify({ teacher_id: this.selectedTeacher && this.selectedTeacher.id })) } catch (e) { /* ignore */ }
      this.load()
    },
    load() {
      this.$vs.loading()
      axios.p('/api/lms/lesson-reports/teacher-timetable', {
        from_date: this.weekStart.format('YYYY-MM-DD'),
        to_date: this.weekEnd.format('YYYY-MM-DD'),
        teacher_id: this.teacherId || (this.selectedTeacher ? this.selectedTeacher.id : '')
      }).then(res => {
        this.$vs.loading.close()
        const d = res.data || {}
        if (d.status !== 1) {
          this.$vs.notify({ title: 'Lỗi', text: d.message || 'Không tải được lịch dạy', color: 'danger' })
          return
        }
        const data = d.data
        this.teacher = data.teacher
        this.teacherOnly = !!data.teacher_only
        this.teachers = data.teachers || []
        this.items = data.items || []
        this.summary = data.summary || {}
        if (this.teachers.length && data.teacher) {
          this.selectedTeacher = this.teachers.find(t => t.id === data.teacher.id) || { id: data.teacher.id, name: data.teacher.name }
        }
      }).catch(() => this.$vs.loading.close())
    },
    itemState(it) {
      const n = it.total_students || it.expected_students || 0
      if (it.class_date > this.today) return { cls: 'future', icon: 'fa-solid fa-lock', text: `Chưa đến ngày học · ${n} HS` }
      if (it.roster_missing) return { cls: 'muted', icon: 'fa-solid fa-users', text: `${n} HS · chưa có DS điểm danh` }
      const att = `${it.present}/${it.total_students} có mặt`
      if (it.type === 'replaced') return { cls: 'muted', icon: 'fa-solid fa-user', text: att }
      if (it.pending_attendance > 0) return { cls: 'warn', icon: 'fa-solid fa-clipboard-user', text: `Cần điểm danh ${it.pending_attendance} HS` }
      if (!it.is_report_session) return { cls: 'ok', icon: 'fa-solid fa-check', text: `${att} · nhận xét cuối tuần` }
      const rp = { none: ['danger', 'fa-solid fa-pen', 'cần nhận xét'], draft: ['draft', 'fa-solid fa-pen', 'đang nhận xét'],
        done: ['ok', 'fa-solid fa-circle-check', 'đã nhận xét'], sent: ['sent', 'fa-solid fa-paper-plane', 'đã gửi PH'] }[it.report_status] || ['muted', 'fa-solid fa-pen', '']
      return { cls: rp[0], icon: rp[1], text: `${att} · ${rp[2]}` }
    },
    isFuture(it) { return it.type !== 'holiday' && it.class_date > this.today },
    open(it) {
      if (it.type === 'holiday' || this.isFuture(it)) return
      this.$router.push(`/lms/lesson-reports/${it.report_schedule_id || it.schedule_id}`)
    }
  }
}
</script>

<style>
.tt-page { font-size: 14px; color: #334155; display: flex; flex-direction: column; gap: 14px; }
.tt-card { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(15, 23, 42, 0.06); padding: 14px 18px; }
.nowrap { white-space: nowrap !important; }

.tt-toolbar { display: flex; align-items: center; gap: 18px; flex-wrap: wrap; }
.tt-week { display: flex; align-items: center; gap: 8px; }
.tt-week__label { min-width: 170px; text-align: center; }
.tt-week__range { font-size: 17px; font-weight: 700; color: #1e293b; white-space: nowrap; }
.tt-week__sub { font-size: 13px; color: #64748b; }
.tt-nav { width: 40px; height: 40px; border-radius: 8px; border: 1.5px solid #cbd5e1; background: #fff; color: #475569; cursor: pointer; font-size: 14px; }
.tt-nav:hover { border-color: #7367f0; color: #7367f0; box-shadow: 0 0 0 3px rgba(115, 103, 240, 0.2); }
.tt-today { border: none; background: rgba(115, 103, 240, 0.1); color: #5e50ee; font-weight: 600; font-size: 13px; padding: 0 12px; height: 40px; border-radius: 8px; cursor: pointer; white-space: nowrap; }

.tt-teacher { display: flex; align-items: center; gap: 10px; flex: 1; min-width: 260px; }
.tt-teacher__avatar { width: 42px; height: 42px; border-radius: 12px; background: linear-gradient(135deg, #7367f0, #9e95f5); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
.tt-teacher__name { font-size: 16px; font-weight: 700; color: #1e293b; }
.tt-teacher__select { width: 280px; max-width: 100%; }
.tt-page .v-select .vs__dropdown-toggle { min-height: 40px; background: #fff; border: 1.5px solid #cbd5e1; border-radius: 8px; cursor: pointer; }
.tt-page .v-select .vs__dropdown-toggle:hover { border-color: #7367f0; box-shadow: 0 0 0 3px rgba(115, 103, 240, 0.2); }
.tt-page .v-select .vs__search, .tt-page .v-select .vs__selected { font-size: 14px; }

.tt-kpis { display: flex; gap: 10px; flex-wrap: wrap; }
.tt-kpi { min-width: 110px; background: #f8fafc; border: 1px solid #eef2f7; border-radius: 10px; padding: 6px 12px; }
.tt-kpi__k { font-size: 13px; color: #64748b; font-weight: 600; white-space: nowrap; }
.tt-kpi__v { font-size: 22px; font-weight: 800; color: #1e293b; line-height: 1.25; }
.tt-kpi.warn { background: #fff7ed; border-color: #fed7aa; }
.tt-kpi.warn .tt-kpi__v { color: #ea580c; }
.tt-kpi.danger { background: #fef2f2; border-color: #fecaca; }
.tt-kpi.danger .tt-kpi__v { color: #dc2626; }

/* Màu theo loại buổi */
.ty-main { --ty: #7367f0; --ty-bg: #f3f2fe; }
.ty-substitute { --ty: #f97316; --ty-bg: #fff4ec; }
.ty-ta { --ty: #0ea5e9; --ty-bg: #eef8fe; }
.ty-replaced { --ty: #94a3b8; --ty-bg: #f8fafc; }
.ty-holiday { --ty: #e11d48; --ty-bg: #fff1f3; }

.tt-legend { display: flex; flex-wrap: wrap; gap: 8px; }
.tt-legend__item { display: inline-flex; align-items: center; gap: 7px; white-space: nowrap; background: #fff; border: 1.5px solid #e2e8f0; border-radius: 99px; padding: 5px 12px; font-size: 13px; font-weight: 600; color: #475569; }
.tt-legend__item b { background: var(--ty-bg); color: var(--ty); border-radius: 99px; padding: 0 8px; }
.tt-dot { width: 10px; height: 10px; border-radius: 50%; background: var(--ty); display: inline-block; }

.tt-grid { position: relative; display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); gap: 10px; }
@media (max-width: 1400px) { .tt-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); } }
@media (max-width: 992px) { .tt-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
@media (max-width: 600px) { .tt-grid { grid-template-columns: 1fr; } .tt-teacher__select { width: 100%; } }
.tt-day { background: #f8fafc; border: 1px solid #eef2f7; border-radius: 12px; display: flex; flex-direction: column; min-height: 150px; }
.tt-day.today { border-color: #7367f0; box-shadow: 0 0 0 2px rgba(115, 103, 240, 0.15); background: #faf9ff; }
.tt-day__head { display: flex; align-items: baseline; gap: 8px; padding: 10px 12px; border-bottom: 1px solid #eef2f7; }
.tt-day__dow { font-size: 15px; font-weight: 700; color: #1e293b; }
.tt-day__date { font-size: 13px; color: #64748b; font-weight: 600; }
.tt-day__tag { margin-left: auto; font-size: 13px; font-weight: 700; color: #fff; background: #7367f0; border-radius: 99px; padding: 0 8px; white-space: nowrap; }
.tt-day__body { padding: 10px; display: flex; flex-direction: column; gap: 10px; }
.tt-day__empty { color: #cbd5e1; font-size: 13px; text-align: center; padding: 18px 0; }

.tt-item { background: var(--ty-bg); border: 1px solid #e2e8f0; border-left: 4px solid var(--ty); border-radius: 10px; padding: 9px 11px; transition: transform .12s, box-shadow .12s; }
.tt-item.clickable { cursor: pointer; }
.tt-item.clickable:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(15, 23, 42, 0.1); border-color: var(--ty); }
.tt-item.ty-replaced { opacity: .8; }
/* Buổi chưa tới ngày: nền xám, giữ vạch màu loại buổi, không bấm được */
.tt-item.future { background: #f1f5f9; border-style: dashed; border-left-style: solid; cursor: not-allowed; }
.tt-item.future .tt-item__cls { color: #64748b; }
.tt-item.future .tt-item__meta { color: #94a3b8; }
.tt-item.future .tt-st.future { background: #e2e8f0; border: none; color: #64748b; }
.tt-item__top { display: flex; flex-direction: column-reverse; gap: 2px; }
.tt-item__cls { font-size: 14px; font-weight: 700; color: #1e293b; line-height: 1.35; word-break: break-word; }
.tt-type { white-space: nowrap !important; font-size: 13px; font-weight: 700; color: var(--ty); }
.tt-item__meta { font-size: 13px; color: #64748b; margin-top: 3px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.tt-item__meta i { width: 14px; color: var(--ty); margin-right: 6px; }
.tt-st { display: flex; align-items: center; gap: 6px; margin-top: 8px; padding: 4px 9px; border-radius: 7px; font-size: 13px; font-weight: 600; line-height: 1.4; }
.tt-st i { flex-shrink: 0; }
.tt-st.ok { background: #dcfce7; color: #15803d; }
.tt-st.warn { background: #ffedd5; color: #c2410c; }
.tt-st.danger { background: #fee2e2; color: #b91c1c; }
.tt-st.draft { background: #fef3c7; color: #b45309; }
.tt-st.sent { background: rgba(115, 103, 240, 0.12); color: #5e50ee; }
.tt-st.muted { background: #f1f5f9; color: #64748b; }
.tt-st.future { background: #f1f5f9; color: #94a3b8; border: 1px dashed #cbd5e1; }
.tt-hint { font-size: 13px; color: #64748b; }
.tt-hint i { color: #7367f0; margin-right: 6px; }
</style>
