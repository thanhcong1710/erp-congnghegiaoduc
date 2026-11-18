<template>
  <div class="lrs-sheet">
    <!-- Brand -->
    <div class="lrs-brand">
      <img class="lrs-brand__logo" src="@assets/images/logo/stemkey-logo.png" alt="STEMKEY" />
      <div class="lrs-brand__info">
        <div class="lrs-brand__branch">{{ session.branch_name }}</div>
        <div class="lrs-brand__sub" v-if="session.branch_address"><i class="fa-solid fa-location-dot"></i>{{ session.branch_address }}</div>
        <div class="lrs-brand__sub" v-if="session.branch_hotline"><i class="fa-solid fa-phone"></i>{{ session.branch_hotline }}</div>
      </div>
    </div>

    <div class="lrs-title-wrap">
      <div class="lrs-title">Thông tin lớp học {{ session.cls_name }}</div>
      <div class="lrs-subtitle">Báo cáo buổi học gửi Quý phụ huynh</div>
    </div>

    <!-- Thông tin buổi -->
    <div class="lrs-info">
      <div class="lrs-info__col">
        <div class="lrs-info__row"><i class="fa-regular fa-calendar"></i><span>{{ dateText }}</span></div>
        <div class="lrs-info__row" v-if="timeText"><i class="fa-regular fa-clock"></i><span>{{ timeText }}</span></div>
        <div class="lrs-info__row"><i class="fa-solid fa-book-open"></i>
          <span><b>Buổi {{ session.subject_stt || '' }}<template v-if="report.lesson_title">:</template></b> {{ report.lesson_title }}</span>
        </div>
      </div>
      <div class="lrs-info__col">
        <div class="lrs-info__row"><span class="lrs-k">Giáo viên:</span><span class="lrs-link">{{ session.teacher_name || '—' }}</span></div>
        <div class="lrs-info__row" v-if="session.ta_name"><span class="lrs-k">Trợ giảng:</span><span class="lrs-link">{{ session.ta_name }}</span></div>
        <div class="lrs-info__row" v-if="session.cm_name"><span class="lrs-k">CSKH/QLLH:</span><span class="lrs-link">{{ session.cm_name }}</span></div>
      </div>
      <div class="lrs-info__col lrs-stats">
        <div class="lrs-stat"><div class="lrs-stat__v">{{ session.total_students }}</div><div class="lrs-stat__k">Sĩ số</div></div>
        <div class="lrs-stat ok"><div class="lrs-stat__v">{{ session.present }}</div><div class="lrs-stat__k">Có điểm danh</div></div>
        <div class="lrs-stat hw"><div class="lrs-stat__v">{{ session.homework_done }}</div><div class="lrs-stat__k">Làm bài tập</div></div>
      </div>
    </div>

    <!-- Nhận xét chung -->
    <div class="lrs-section">
      <div class="lrs-section__title"><i class="fa-solid fa-comment-dots"></i>Nhận xét chung</div>
      <div v-if="hasGeneral" class="lrs-rich ql-editor" v-html="report.general_comment"></div>
      <div v-else class="lrs-muted">Chưa có nhận xét chung.</div>
      <div class="lrs-deadline" v-if="report.homework_deadline">
        <i class="fa-solid fa-hourglass-half"></i><b>Thời gian nộp bài:</b> {{ report.homework_deadline }}
      </div>
    </div>

    <!-- Bảng học viên -->
    <div class="lrs-section">
      <div class="lrs-section__title"><i class="fa-solid fa-user-graduate"></i>Nhận xét từng học viên</div>
      <table class="lrs-table">
        <thead>
          <tr>
            <th class="c-name">Học viên</th>
            <th class="c-mid">Điểm danh</th>
            <th class="c-mid">BTVN</th>
            <th class="c-mid">Điểm BTVN</th>
            <th>Nhận xét</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="st in students" :key="st.student_id">
            <td class="c-name" data-k="Học viên">
              <div class="lrs-stname">{{ st.name }}</div>
              <span v-if="st.is_makeup" class="lrs-tag">Học bù</span>
            </td>
            <td class="c-mid" data-k="Điểm danh">
              <span v-if="st.attendance_status == 1" class="lrs-ic ok" title="Có mặt"><i class="fa-solid fa-check"></i></span>
              <span v-else-if="st.attendance_status == 2" class="lrs-badge absent">Vắng</span>
              <span v-else class="lrs-dash">–</span>
            </td>
            <td class="c-mid" data-k="BTVN">
              <span v-if="st.homework_done === 1" class="lrs-ic ok" title="Đã làm"><i class="fa-solid fa-check"></i></span>
              <span v-else-if="st.homework_done === 0" class="lrs-ic no" title="Chưa làm"><i class="fa-solid fa-xmark"></i></span>
              <span v-else class="lrs-dash">–</span>
            </td>
            <td class="c-mid" data-k="Điểm BTVN">
              <span v-if="st.homework_score !== null && st.homework_score !== '' && st.homework_score !== undefined" class="lrs-score">{{ fmtScore(st.homework_score) }}</span>
            </td>
            <td class="c-note" data-k="Nhận xét">{{ st.comment }}</td>
          </tr>
          <tr v-if="!students.length">
            <td colspan="5" class="lrs-muted c-mid">Buổi học chưa có học viên.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="lrs-footer">
      <img src="@assets/images/logo/stemkey-logo.png" alt="" />
      <span>Cảm ơn Quý phụ huynh đã đồng hành cùng {{ session.branch_name || 'STEMKEY' }}!</span>
    </div>
  </div>
</template>

<script>
import moment from 'moment'
import 'quill/dist/quill.core.css'

const WEEKDAYS = ['Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy']

export default {
  name: 'LessonReportSheet',
  props: {
    session: { type: Object, required: true },
    report: { type: Object, required: true },
    students: { type: Array, default: () => [] }
  },
  computed: {
    dateText() {
      const m = moment(this.session.class_date)
      return m.isValid() ? `${WEEKDAYS[m.day()]}, ${m.format('DD-MM-YYYY')}` : ''
    },
    timeText() {
      const { start_time: s, end_time: e } = this.report
      if (!s && !e) return ''
      return [s, e].filter(Boolean).join(' - ')
    },
    hasGeneral() {
      const html = this.report.general_comment || ''
      return html.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').trim() !== ''
    }
  },
  methods: {
    fmtScore(v) {
      const n = Number(v)
      return isNaN(n) ? v : (Math.round(n * 10) / 10).toString().replace('.', ',')
    }
  }
}
</script>

<style>
.lrs-sheet {
  --lrs-brand: #f05a28; --lrs-brand-soft: #fff1ea; --lrs-ink: #1e293b; --lrs-text: #334155; --lrs-muted: #64748b; --lrs-line: #f3d9cc;
  background: #fff; color: var(--lrs-text); font-size: 14px; line-height: 1.6;
  max-width: 1040px; margin: 0 auto; border-radius: 14px; overflow: hidden;
  box-shadow: 0 6px 24px rgba(15, 23, 42, 0.08);
}
.lrs-brand { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 18px 26px; border-bottom: 3px solid var(--lrs-brand); background: linear-gradient(90deg, #fff 60%, var(--lrs-brand-soft)); }
.lrs-brand__logo { height: 52px; width: auto; object-fit: contain; }
.lrs-brand__info { text-align: right; }
.lrs-brand__branch { font-size: 16px; font-weight: 700; color: var(--lrs-ink); }
.lrs-brand__sub { font-size: 13px; color: var(--lrs-muted); }
.lrs-brand__sub i { color: var(--lrs-brand); margin-right: 6px; }

.lrs-title-wrap { text-align: center; padding: 22px 16px 6px; }
.lrs-title { display: inline-block; background: var(--lrs-brand); color: #fff; font-size: 19px; font-weight: 700; padding: 10px 26px; border-radius: 12px; box-shadow: 0 6px 16px rgba(240, 90, 40, 0.28); }
.lrs-subtitle { font-size: 13px; color: var(--lrs-muted); margin-top: 8px; }

.lrs-info { display: grid; grid-template-columns: 1.25fr 1fr 1fr; gap: 14px 24px; padding: 16px 26px 18px; border-bottom: 1.5px solid #f6b9a3; }
.lrs-info__row { display: flex; gap: 8px; align-items: baseline; padding: 3px 0; font-size: 14px; }
.lrs-info__row > i { color: #94a3b8; width: 16px; text-align: center; flex-shrink: 0; }
.lrs-k { color: var(--lrs-muted); white-space: nowrap; }
.lrs-link { color: #1d7bd8; font-weight: 600; }
.lrs-stats { display: flex; gap: 10px; align-items: stretch; }
.lrs-stat { flex: 1; background: #f8fafc; border: 1px solid #eef2f7; border-radius: 10px; text-align: center; padding: 8px 6px; }
.lrs-stat__v { font-size: 22px; font-weight: 800; color: var(--lrs-ink); line-height: 1.2; }
.lrs-stat__k { font-size: 13px; color: var(--lrs-muted); white-space: nowrap; }
.lrs-stat.ok .lrs-stat__v { color: #16a34a; }
.lrs-stat.hw .lrs-stat__v { color: var(--lrs-brand); }

.lrs-section { padding: 18px 26px; }
.lrs-section + .lrs-section { padding-top: 4px; }
.lrs-section__title { font-size: 16px; font-weight: 700; color: #dc3545; text-decoration: underline; text-underline-offset: 4px; margin-bottom: 10px; }
.lrs-section__title i { margin-right: 8px; text-decoration: none; }
.lrs-rich.ql-editor { padding: 0; font-size: 14px; color: var(--lrs-text); line-height: 1.7; white-space: normal; height: auto; overflow: visible; }
.lrs-rich.ql-editor p { margin: 0 0 6px; }
.lrs-rich.ql-editor ul, .lrs-rich.ql-editor ol { padding-left: 1.5em; margin-bottom: 6px; }
.lrs-deadline { margin-top: 10px; display: inline-flex; align-items: center; gap: 8px; background: var(--lrs-brand-soft); color: #9a3412; border: 1px dashed #f6b9a3; border-radius: 8px; padding: 6px 12px; font-size: 14px; }
.lrs-muted { color: #94a3b8; font-size: 14px; }

.lrs-table { width: 100%; border-collapse: collapse; font-size: 14px; }
.lrs-table th { background: #fde3d6; color: var(--lrs-ink); font-weight: 700; text-align: center; padding: 10px 12px; border: 1px solid var(--lrs-line); white-space: nowrap; }
.lrs-table td { background: #fff7f3; border: 1px solid var(--lrs-line); padding: 12px; vertical-align: top; }
.lrs-table tbody tr:nth-child(even) td { background: #fffbf9; }
.lrs-table .c-name { width: 190px; text-align: center; }
.lrs-table .c-mid { width: 100px; text-align: center; white-space: nowrap; }
.lrs-table .c-note { text-align: justify; white-space: pre-line; color: var(--lrs-text); }
.lrs-stname { color: #1d7bd8; font-weight: 600; }
.lrs-tag { display: inline-flex; white-space: nowrap; font-size: 13px; font-weight: 600; color: #7c3aed; background: #f3e8ff; border-radius: 99px; padding: 1px 8px; margin-top: 4px; }
.lrs-ic { display: inline-flex; width: 26px; height: 26px; border-radius: 50%; align-items: center; justify-content: center; font-size: 14px; }
.lrs-ic.ok { color: #1e293b; }
.lrs-ic.no { color: #dc2626; }
.lrs-badge { display: inline-flex; white-space: nowrap; font-size: 13px; font-weight: 700; border-radius: 99px; padding: 2px 10px; }
.lrs-badge.absent { background: #fee2e2; color: #b91c1c; }
.lrs-dash { color: #cbd5e1; }
.lrs-score { display: inline-flex; min-width: 36px; justify-content: center; font-weight: 800; font-size: 15px; color: var(--lrs-brand); background: #fff; border: 1.5px solid #f6b9a3; border-radius: 8px; padding: 1px 8px; }

.lrs-footer { display: flex; align-items: center; justify-content: center; gap: 10px; padding: 14px 20px 20px; color: var(--lrs-muted); font-size: 13px; }
.lrs-footer img { height: 22px; opacity: .8; }

/* ---------- Mobile (phụ huynh mở từ Zalo) ---------- */
@media (max-width: 720px) {
  .lrs-sheet { border-radius: 0; box-shadow: none; }
  .lrs-brand { padding: 14px 16px; }
  .lrs-brand__logo { height: 40px; }
  .lrs-title { font-size: 17px; padding: 8px 18px; }
  .lrs-info { grid-template-columns: 1fr; padding: 14px 16px; }
  .lrs-section { padding: 16px; }
  .lrs-table thead { display: none; }
  .lrs-table, .lrs-table tbody, .lrs-table tr, .lrs-table td { display: block; width: 100% !important; }
  .lrs-table tr { border: 1px solid var(--lrs-line); border-radius: 12px; overflow: hidden; margin-bottom: 12px; background: #fff7f3; }
  .lrs-table td { border: none; border-bottom: 1px dashed var(--lrs-line); text-align: left !important; display: flex; gap: 10px; align-items: center; padding: 8px 12px; background: transparent !important; }
  .lrs-table td:last-child { border-bottom: none; display: block; }
  .lrs-table td::before { content: attr(data-k); font-size: 13px; font-weight: 600; color: var(--lrs-muted); min-width: 84px; }
  .lrs-table td.c-note::before { display: block; margin-bottom: 4px; }
  .lrs-table td.c-name { background: #fde3d6 !important; }
  .lrs-table td.c-name::before { display: none; }
  .lrs-stname { font-size: 15px; }
}

/* ---------- In / lưu PDF ---------- */
@media print {
  .lrs-sheet { box-shadow: none; max-width: none; border-radius: 0; }
  .lrs-table tr { page-break-inside: avoid; }
  .lrs-title, .lrs-table th, .lrs-table td, .lrs-brand { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
}
</style>
