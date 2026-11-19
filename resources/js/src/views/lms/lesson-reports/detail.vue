<template>
  <div id="page-lesson-report-detail" class="lrd-page mt-5">
    <!-- ============ THANH ĐIỀU HƯỚNG ============ -->
    <div class="lrd-topbar">
      <button type="button" class="lrd-btn ghost" @click="$router.push('/lms/lesson-reports')"><i class="fa-solid fa-arrow-left"></i>Lịch nhận xét</button>
      <div class="lrd-topbar__nav">
        <button type="button" class="lrd-btn ghost" :disabled="!prevId" @click="go(prevId)"><i class="fa-solid fa-chevron-left"></i>Tuần trước</button>
        <button type="button" class="lrd-btn ghost" :disabled="!nextId" @click="go(nextId)">Tuần sau<i class="fa-solid fa-chevron-right ml-2"></i></button>
      </div>
      <div class="lrd-savestate" :class="saveState.cls">
        <i :class="saveState.icon"></i>{{ saveState.text }}
      </div>
    </div>

    <div class="lrd-body">
      <div v-if="!loaded" class="lrd-card lrd-empty">Đang tải...</div>
      <div v-else-if="error" class="lrd-card lrd-empty"><i class="fa-solid fa-circle-exclamation"></i> {{ error }}</div>

      <template v-else>
        <!-- ============ BANNER ============ -->
        <div class="lrd-card lrd-hero">
          <div class="lrd-hero__avatar"><i class="fa-solid fa-chalkboard"></i></div>
          <div class="lrd-hero__info">
            <div class="lrd-hero__name">
              {{ session.cls_name }}
              <span class="lrd-badge" :class="statusKey">{{ statusText }}</span>
            </div>
            <div class="lrd-hero__meta">
              <span><i class="fa-regular fa-calendar"></i>{{ dateText }}</span>
              <span><i class="fa-solid fa-book-open"></i>Tuần {{ session.week_no || '?' }}<template v-if="session.total_weeks">/{{ session.total_weeks }}</template></span>
              <span><i class="fa-solid fa-list-check"></i>{{ weekSessions.length }} buổi: {{ sessionsText }}</span>
              <span v-if="session.branch_name"><i class="fa-solid fa-building"></i><span class="nowrap">{{ session.branch_name }}</span></span>
            </div>
            <div class="lrd-hero__meta">
              <span><i class="fa-solid fa-chalkboard-user"></i>GV: <b>{{ session.teacher_name || '—' }}</b></span>
              <span><i class="fa-solid fa-user-group"></i>TG: <b>{{ session.ta_name || '—' }}</b></span>
              <span><i class="fa-solid fa-headset"></i>CSKH: <b>{{ session.cm_name || '—' }}</b></span>
            </div>
          </div>
          <div class="lrd-kpis">
            <div class="lrd-kpi"><div class="lrd-kpi__k">Sĩ số</div><div class="lrd-kpi__v">{{ students.length }}</div></div>
            <div class="lrd-kpi ok"><div class="lrd-kpi__k">Lượt có mặt</div><div class="lrd-kpi__v">{{ stats.present }}</div></div>
            <div class="lrd-kpi" :class="{ danger: stats.absent }"><div class="lrd-kpi__k">Lượt vắng</div><div class="lrd-kpi__v">{{ stats.absent }}</div></div>
            <div class="lrd-kpi" :class="{ warn: stats.pending }"><div class="lrd-kpi__k">Chưa điểm danh</div><div class="lrd-kpi__v">{{ stats.pending }}</div></div>
            <div class="lrd-kpi brand"><div class="lrd-kpi__k">Làm BTVN</div><div class="lrd-kpi__v">{{ stats.homework }}</div></div>
          </div>
        </div>

        <div v-if="report.status === 2" class="lrd-note">
          <i class="fa-solid fa-circle-info"></i>Báo cáo đã gửi phụ huynh. Mọi chỉnh sửa sẽ cập nhật ngay trên link đã gửi.
        </div>
        <div v-if="prefillPending" class="lrd-note">
          <i class="fa-solid fa-route"></i>Nội dung bài học được lấy từ <b>lộ trình tuần {{ session.week_no }}</b>. Bạn có thể bổ sung, nội dung sẽ lưu khi bạn chỉnh sửa hoặc nhận xét học viên.
        </div>

        <!-- ============ NỘI DUNG BUỔI HỌC ============ -->
        <div class="lrd-card">
          <div class="lrd-card__title"><span><i class="fa-solid fa-comment-dots"></i>Nội dung bài học tuần {{ session.week_no || '' }}</span>
            <div class="lrd-card__actions">
              <button v-if="plan" type="button" class="lrd-btn soft" @click="applyPlan(true)"><i class="fa-solid fa-route"></i>Lấy theo lộ trình</button>
              <button v-if="canEditPlan" type="button" class="lrd-btn ghost" @click="saveToPlan"><i class="fa-solid fa-floppy-disk"></i>Lưu vào lộ trình tuần {{ session.week_no }}</button>
              <button type="button" class="lrd-btn soft" @click="insertTemplate"><i class="fa-solid fa-wand-magic-sparkles"></i>Chèn khung mẫu</button>
              <button type="button" class="lrd-btn soft" @click="openCopy"><i class="fa-regular fa-copy"></i>Sao chép từ lớp khác</button>
            </div>
          </div>
          <div class="lrd-grid">
            <div class="lrd-field span-2">
              <label>Tên bài học</label>
              <input type="text" class="lrd-input" v-model="report.lesson_title" placeholder="VD: Unit 2 - Lesson 4 - Letters and sounds" @input="queueReport" />
            </div>
            <div class="lrd-field">
              <label>Giờ học</label>
              <div class="lrd-time">
                <input type="time" class="lrd-input" v-model="report.start_time" @change="queueReport" />
                <span>–</span>
                <input type="time" class="lrd-input" v-model="report.end_time" @change="queueReport" />
              </div>
            </div>
            <div class="lrd-field">
              <label>Thời gian nộp bài</label>
              <input type="text" class="lrd-input" v-model="report.homework_deadline" placeholder="VD: Trước buổi học tiếp theo" @input="queueReport" />
            </div>
          </div>
          <div class="lrd-editor">
            <quill-editor ref="editor" v-model="report.general_comment" :options="editorOptions" @ready="onEditorReady" />
          </div>
        </div>

        <!-- ============ BẢNG HỌC VIÊN ============ -->
        <div class="lrd-card">
          <div class="lrd-card__title">
            <span><i class="fa-solid fa-user-graduate"></i>Nhận xét từng học viên <span class="lrd-pill">{{ stats.commented }}/{{ students.length }} đã nhận xét</span></span>
            <div class="lrd-card__actions" v-if="weekStarted">
              <button type="button" class="lrd-btn success" v-if="stats.pending" @click="bulkPresent"><i class="fa-solid fa-check-double"></i>Cả lớp có mặt</button>
              <button type="button" class="lrd-btn soft" @click="bulkHomework"><i class="fa-solid fa-book"></i>Cả lớp đã làm BTVN</button>
              <button type="button" class="lrd-btn soft" @click="tpl.open = true"><i class="fa-solid fa-paste"></i>Áp mẫu nhận xét</button>
            </div>
          </div>

          <div v-if="!weekStarted" class="lrd-note lock"><i class="fa-solid fa-lock"></i>Tuần học chưa diễn ra buổi nào ({{ sessionsText }}). Điểm danh và nhận xét học viên sẽ mở khi tới ngày học.</div>
          <div v-else-if="futureSessions.length" class="lrd-note lock"><i class="fa-solid fa-lock"></i>Buổi {{ futureSessions.map(w => shortDate(w.class_date)).join(', ') }} chưa diễn ra – chỉ điểm danh được khi tới ngày học.</div>

          <div class="lrd-hint"><i class="fa-regular fa-keyboard"></i>Nhận xét tự lưu. Nhấn <kbd>Ctrl</kbd> + <kbd>Enter</kbd> để chuyển sang học viên tiếp theo.</div>

          <div class="lrd-table-wrap">
            <table class="lrd-table">
              <thead>
                <tr>
                  <th class="c-stt">#</th>
                  <th class="c-name">Học viên</th>
                  <th class="c-att">Điểm danh <span class="lrd-th-sub">(bấm để đổi)</span></th>
                  <th class="c-hw">BTVN</th>
                  <th class="c-score">Điểm BTVN</th>
                  <th>Nhận xét</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(st, idx) in students" :key="st.student_id" :class="{ absent: weekAbsent(st) }">
                  <td class="c-stt">{{ idx + 1 }}</td>
                  <td class="c-name">
                    <div class="lrd-student">
                      <div class="lrd-student__avatar">{{ initials(st.name) }}</div>
                      <div>
                        <div class="lrd-strong">{{ st.name }}</div>
                        <div class="lrd-sub">{{ st.lms_code }}</div>
                        <span v-if="st.is_makeup" class="lrd-tag" :title="'Học bù từ lớp ' + (st.makeup_from || '')">Học bù<template v-if="st.makeup_from"> · {{ st.makeup_from }}</template></span>
                      </div>
                    </div>
                  </td>
                  <td class="c-att">
                    <!-- 1 ô duy nhất: mỗi buổi trong tuần là 1 chip, bấm để đổi Chưa → Có mặt → Vắng -->
                    <div class="lrd-days">
                      <template v-for="w in weekSessions">
                        <span v-if="!att(st, w)" :key="'a' + w.schedule_id" class="lrd-day none" title="Học viên không có lịch buổi này">
                          <span class="lrd-day__d">{{ shortDate(w.class_date) }}</span><span class="lrd-day__s">—</span>
                        </span>
                        <span v-else-if="att(st, w).is_makeup" :key="'a' + w.schedule_id" class="lrd-day" :class="att(st, w).status == 1 ? 'present' : 'pending'" title="Học bù – xác nhận tại màn Điểm danh/Học bù">
                          <span class="lrd-day__d">{{ shortDate(w.class_date) }}</span><span class="lrd-day__s">{{ att(st, w).status == 1 ? 'Đã học bù' : 'Chờ học bù' }}</span>
                        </span>
                        <span v-else-if="isFuture(w)" :key="'a' + w.schedule_id" class="lrd-day future" title="Buổi học chưa diễn ra, chưa thể điểm danh">
                          <span class="lrd-day__d">{{ shortDate(w.class_date) }}</span><span class="lrd-day__s"><i class="fa-solid fa-lock"></i>Chưa học</span>
                        </span>
                        <button v-else :key="'a' + w.schedule_id" type="button" class="lrd-day clickable" :class="[attKey(att(st, w)), { busy: att(st, w)._saving }]"
                          :title="'Bấm để đổi trạng thái điểm danh ' + shortDate(w.class_date)" @click="cycleAttendance(st, w)">
                          <span class="lrd-day__d">{{ shortDate(w.class_date) }}</span>
                          <span class="lrd-day__s"><i :class="attIcon(att(st, w))"></i>{{ attText(att(st, w)) }}</span>
                        </button>
                      </template>
                    </div>
                  </td>
                  <td class="c-hw">
                    <div class="lrd-seg sm" :class="{ locked: !weekStarted }">
                      <button type="button" :disabled="!weekStarted" :class="{ on: st.homework_done === 1, ok: true }" title="Đã làm BTVN" @click="setHomework(st, 1)"><i class="fa-solid fa-check"></i></button>
                      <button type="button" :disabled="!weekStarted" :class="{ on: st.homework_done === 0, no: true }" title="Chưa làm BTVN" @click="setHomework(st, 0)"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                  </td>
                  <td class="c-score">
                    <input
                      type="text" inputmode="decimal" class="lrd-input lrd-score" :class="{ invalid: st._scoreErr }"
                      v-model="st.homework_score_text" placeholder="0–10" maxlength="4" :disabled="!weekStarted"
                      @input="onScore(st)"
                    />
                    <div v-if="st._scoreErr" class="lrd-err">0 – 10</div>
                  </td>
                  <td class="c-note">
                    <div class="lrd-note-cell">
                      <textarea
                        :ref="'note_' + idx" class="lrd-textarea" rows="3" v-model="st.comment"
                        :disabled="!weekStarted"
                        :placeholder="weekStarted ? 'Nhận xét cho ' + givenName(st.name) + '...' : 'Chưa đến buổi học – chưa thể nhận xét'"
                        @input="autoGrow($event); queueStudent(st)"
                        @keydown.ctrl.enter.prevent="focusNext(idx)"
                        @keydown.meta.enter.prevent="focusNext(idx)"
                      ></textarea>
                      <span class="lrd-flag" v-if="st._saving"><i class="fa-solid fa-spinner fa-spin"></i></span>
                      <span class="lrd-flag ok" v-else-if="st._saved"><i class="fa-solid fa-check"></i></span>
                      <span class="lrd-flag err" v-else-if="st._error" title="Lưu lỗi – sẽ thử lại khi bạn sửa tiếp"><i class="fa-solid fa-exclamation"></i></span>
                    </div>
                  </td>
                </tr>
                <tr v-if="!students.length">
                  <td colspan="6" class="lrd-empty-row">Tuần học chưa có học viên nào.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- ============ THANH HÀNH ĐỘNG ============ -->
        <div class="lrd-actionbar">
          <div class="lrd-actionbar__progress">
            <div class="lrd-progress"><div class="lrd-progress__bar" :style="{ width: progress + '%' }"></div></div>
            <span>Đã nhận xét <b>{{ stats.commented }}/{{ students.length }}</b> học viên</span>
          </div>
          <div class="lrd-actionbar__btns">
            <button v-if="report.status >= 1" type="button" class="lrd-btn ghost" @click="setStatus(0)"><i class="fa-solid fa-rotate-left"></i>Chuyển về nháp</button>
            <button v-if="!(report.status >= 1)" type="button" class="lrd-btn success" :disabled="!weekEnded" :title="weekEnded ? '' : 'Chưa học hết các buổi trong tuần'" @click="setStatus(1)"><i class="fa-solid fa-circle-check"></i>Đánh dấu hoàn thành</button>
            <button type="button" class="lrd-btn primary" @click="openPreview"><i class="fa-solid fa-eye"></i>Xem trước &amp; gửi phụ huynh</button>
          </div>
        </div>
      </template>
    </div>

    <!-- ============ POPUP: XEM TRƯỚC ============ -->
    <vs-popup class="lrd-popup lrd-popup--preview" title="Báo cáo tuần gửi phụ huynh" :active.sync="preview.open" fullscreen>
      <div v-if="preview.open" class="lrd-preview">
        <div class="lrd-preview__bar">
          <div class="lrd-preview__info">
            <span class="nowrap">Xem trước:</span>
            <select class="lrd-select" v-model="preview.studentId">
              <option :value="0">Bản đầy đủ (nội bộ)</option>
              <option v-for="st in students" :key="st.student_id" :value="st.student_id">{{ st.name }}</option>
            </select>
            <template v-if="!(report.status >= 1)">
              <i class="fa-solid fa-circle-info"></i>Đánh dấu hoàn thành để tạo link riêng cho từng phụ huynh.
            </template>
          </div>
          <div class="lrd-preview__btns">
            <button type="button" class="lrd-btn ghost" @click="printReport"><i class="fa-solid fa-print"></i>In / Lưu PDF</button>
            <template v-if="report.status >= 1">
              <button type="button" class="lrd-btn soft" @click="copyAllLinks"><i class="fa-regular fa-copy"></i>Sao chép link cả lớp</button>
              <button v-if="report.status !== 2" type="button" class="lrd-btn primary" @click="markSent"><i class="fa-solid fa-paper-plane"></i>Đánh dấu đã gửi</button>
              <span v-else class="lrd-badge sent"><i class="fa-solid fa-paper-plane mr-1"></i>Đã gửi {{ sentText }}</span>
            </template>
            <button v-else type="button" class="lrd-btn success" :disabled="!weekEnded" :title="weekEnded ? '' : 'Chưa học hết các buổi trong tuần'" @click="setStatus(1)"><i class="fa-solid fa-circle-check"></i>Hoàn thành &amp; tạo link</button>
          </div>
        </div>
        <div v-if="report.status >= 1" class="lrd-links">
          <div class="lrd-links__head"><i class="fa-solid fa-link"></i>Link riêng từng học viên – mỗi phụ huynh chỉ xem được nhận xét của con mình</div>
          <div v-for="st in students" :key="'l' + st.student_id" class="lrd-links__row">
            <span class="lrd-strong nowrap">{{ st.name }}</span>
            <span class="lrd-preview__url">{{ studentUrl(st) || 'Đang tạo link...' }}</span>
            <span class="lrd-links__btns">
              <button type="button" class="lrd-btn soft sm" :disabled="!studentUrl(st)" @click="copyMessage(st)"><i class="fa-regular fa-copy"></i>Tin nhắn Zalo</button>
              <a v-if="studentUrl(st)" class="lrd-btn ghost sm" :href="studentUrl(st)" target="_blank" rel="noopener"><i class="fa-solid fa-up-right-from-square"></i>Mở</a>
            </span>
          </div>
        </div>
        <div class="lrd-preview__sheet" id="lesson-report-print">
          <report-sheet :session="previewSession" :report="report" :students="previewStudents" :view="preview.studentId ? 'student' : ''" />
        </div>
      </div>
    </vs-popup>

    <!-- ============ POPUP: SAO CHÉP ============ -->
    <vs-popup class="lrd-popup" title="Sao chép nội dung bài học" :active.sync="copy.open">
      <div class="lrd-copy">
        <div v-if="!copy.list.length" class="lrd-empty-row">Chưa có phiếu nào cùng khóa học để sao chép.</div>
        <div v-for="c in copy.list" :key="c.schedule_id" class="lrd-copy__item" @click="applyCopy(c)">
          <div class="lrd-copy__head">
            <b>{{ c.cls_name }}</b>
            <span class="lrd-sub">Tuần {{ c.week_no || "?" }} · {{ fmtDate(c.class_date) }}</span>
            <span v-if="c.week_no && c.week_no == session.week_no" class="lrd-badge done">Cùng tuần</span>
            <span v-if="c.same_class == 1" class="lrd-badge sent">Lớp này</span>
          </div>
          <div class="lrd-copy__title" v-if="c.lesson_title">{{ c.lesson_title }}</div>
          <div class="lrd-copy__preview">{{ stripHtml(c.general_comment) }}</div>
        </div>
      </div>
    </vs-popup>

    <!-- ============ POPUP: MẪU NHẬN XÉT ============ -->
    <vs-popup class="lrd-popup" title="Áp mẫu nhận xét cho cả lớp" :active.sync="tpl.open">
      <div class="lrd-tpl">
        <div class="lrd-hint">Dùng <code>{ten}</code> để chèn tên gọi, <code>{ho_ten}</code> để chèn họ tên học viên. Sau khi áp mẫu, bạn vẫn sửa riêng từng em.</div>
        <textarea class="lrd-textarea" rows="6" v-model="tpl.text" placeholder="VD: Trong bài Unit 2 – Lesson 4, {ten} được học các từ vựng ..."></textarea>
        <label class="lrd-check"><input type="checkbox" v-model="tpl.onlyEmpty" />Chỉ áp cho học viên chưa có nhận xét</label>
        <div class="lrd-tpl__btns">
          <button type="button" class="lrd-btn ghost" @click="tpl.open = false">Hủy</button>
          <button type="button" class="lrd-btn primary" :disabled="!tpl.text.trim()" @click="applyTemplate"><i class="fa-solid fa-check"></i>Áp dụng</button>
        </div>
      </div>
    </vs-popup>
  </div>
</template>

<script>
import moment from 'moment'
import { quillEditor } from 'vue-quill-editor'
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import axios from '../../../http/axios.js'
import ReportSheet from './ReportSheet.vue'

const DOW_SHORT = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7']
const GENERAL_TEMPLATE = '<p><strong><u>NỘI DUNG BÀI HỌC:</u></strong></p><p><strong>Unit ...: ...</strong></p><p><strong>Lesson ...: ...</strong></p><p>- Từ vựng: ...</p><p>- Chữ cái / Mẫu câu: ...</p><p>- Kỹ năng: ...</p><p><br></p><p><strong><u>BÀI TẬP VỀ NHÀ:</u></strong></p><p>- Hoàn thành ...</p>'

export default {
  components: { quillEditor, ReportSheet },
  data() {
    return {
      loaded: false,
      error: '',
      session: {},
      report: {},
      students: [],
      prevId: null,
      nextId: null,
      plan: null,
      canEditPlan: false,
      prefillPending: false,
      pendingReport: null,
      pendingStudents: {},
      inflight: 0,
      lastSavedAt: null,
      hasError: false,
      editorOptions: {
        placeholder: 'Nội dung bài học, từ vựng, kỹ năng, bài tập về nhà...',
        modules: {
          toolbar: [
            [{ header: [2, 3, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ color: [] }, { background: [] }],
            [{ list: 'ordered' }, { list: 'bullet' }],
            ['clean']
          ]
        }
      },
      preview: { open: false, studentId: 0 },
      copy: { open: false, list: [] },
      tpl: { open: false, text: '', onlyEmpty: true }
    }
  },
  computed: {
    scheduleId() { return parseInt(this.$route.params.schedule_id) },
    weekSessions() { return this.session.sessions || [] },
    today() { return moment().format('YYYY-MM-DD') },
    futureSessions() { return this.weekSessions.filter(w => this.isFuture(w)) },
    // Có ít nhất 1 buổi đã diễn ra → mới được nhận xét; học hết các buổi → mới được hoàn thành phiếu
    weekStarted() { return this.weekSessions.some(w => !this.isFuture(w)) },
    weekEnded() { return this.weekSessions.length > 0 && !this.futureSessions.length },
    dateText() {
      const a = moment(this.session.week_start), b = moment(this.session.week_end)
      return a.isValid() && b.isValid() ? `${a.format('DD/MM')} – ${b.format('DD/MM/YYYY')}` : ''
    },
    sessionsText() {
      return this.weekSessions.map(w => this.shortDate(w.class_date)).join(', ')
    },
    statusKey() {
      if (this.report.status === 2) return 'sent'
      if (this.report.status === 1) return 'done'
      if (this.report.id) return 'draft'
      return 'none'
    },
    statusText() {
      return { none: 'Chưa nhận xét', draft: 'Đang làm', done: 'Hoàn thành', sent: 'Đã gửi PH' }[this.statusKey]
    },
    stats() {
      // Đếm theo lượt (học viên × buổi); "chưa điểm danh" chỉ tính các buổi đã diễn ra
      const today = moment().format('YYYY-MM-DD')
      let present = 0, absent = 0, pending = 0, homework = 0, commented = 0
      this.students.forEach(s => {
        this.weekSessions.forEach(w => {
          const a = this.att(s, w)
          if (!a) return
          if (a.status == 1) present++
          else if (a.status == 2) absent++
          else if (w.class_date <= today) pending++
        })
        if (s.homework_done === 1) homework++
        if ((s.comment || '').trim()) commented++
      })
      return { present, absent, pending, homework, commented }
    },
    progress() { return this.students.length ? Math.round(this.stats.commented * 100 / this.students.length) : 0 },
    saveState() {
      if (this.inflight > 0 || this.pendingReport || Object.keys(this.pendingStudents).length) return { cls: 'busy', icon: 'fa-solid fa-spinner fa-spin', text: 'Đang lưu...' }
      if (this.hasError) return { cls: 'err', icon: 'fa-solid fa-triangle-exclamation', text: 'Có lỗi khi lưu' }
      if (this.lastSavedAt) return { cls: 'ok', icon: 'fa-solid fa-cloud', text: 'Đã lưu lúc ' + this.lastSavedAt }
      return { cls: '', icon: 'fa-regular fa-cloud', text: 'Tự động lưu' }
    },
    sentText() { return this.report.sent_at ? moment(this.report.sent_at).format('HH:mm DD/MM') : '' },
    previewSession() {
      const present = this.students.filter(s => this.weekSessions.some(w => { const a = this.att(s, w); return a && a.status == 1 })).length
      return Object.assign({}, this.session, {
        total_students: this.students.length, present, homework_done: this.stats.homework
      })
    },
    previewStudents() {
      const list = this.preview.studentId ? this.students.filter(s => s.student_id === this.preview.studentId) : this.students
      return list.map(s => ({
        student_id: s.student_id, name: s.name, is_makeup: s.is_makeup, attendance: s.attendance,
        homework_done: s.homework_done, homework_score: this.parseScore(s.homework_score_text).value, comment: s.comment
      }))
    }
  },
  watch: {
    '$route.params.schedule_id'() { this.flush().then(() => this.load()) }
  },
  created() {
    this.load()
    window.addEventListener('beforeunload', this.onUnload)
  },
  beforeDestroy() {
    window.removeEventListener('beforeunload', this.onUnload)
  },
  beforeRouteLeave(to, from, next) {
    this.flush().then(() => next())
  },
  methods: {
    onUnload(e) {
      if (this.pendingReport || Object.keys(this.pendingStudents).length || this.inflight) {
        this.flush()
        e.preventDefault()
        e.returnValue = ''
      }
    },
    fmtDate(v) { const m = moment(v); return m.isValid() ? m.format('DD/MM/YYYY') : '' },
    shortDate(v) { const m = moment(v); return m.isValid() ? `${DOW_SHORT[m.day()]} ${m.format('DD/MM')}` : '' },
    att(st, w) { const a = st.attendance || {}; return a[w.schedule_id] || null },
    isFuture(w) { return w.class_date > this.today },
    attKey(a) { return a.status == 1 ? 'present' : (a.status == 2 ? 'absent' : 'pending') },
    attText(a) { return a.status == 1 ? 'Có mặt' : (a.status == 2 ? 'Vắng' : 'Chưa ĐD') },
    attIcon(a) { return a.status == 1 ? 'fa-solid fa-check' : (a.status == 2 ? 'fa-solid fa-xmark' : 'fa-regular fa-circle') },
    cycleAttendance(st, w) {
      // Chưa điểm danh → Có mặt → Vắng → Chưa điểm danh
      const a = this.att(st, w)
      if (!a) return
      this.setAttendance(st, w, a.status == 1 ? 2 : (a.status == 2 ? 0 : 1))
    },
    weekAbsent(st) {
      const list = this.weekSessions.map(w => this.att(st, w)).filter(Boolean)
      return list.length > 0 && list.every(a => a.status == 2)
    },
    studentUrl(st) { return st.share_token && this.report.status >= 1 ? `${window.location.origin}/pages/lesson-report/${st.share_token}` : '' },
    initials(name) { const p = (name || '').trim().split(/\s+/); return p.length ? p[p.length - 1].charAt(0).toUpperCase() : '?' },
    givenName(name) { const p = (name || '').trim().split(/\s+/); return p[p.length - 1] || 'học viên' },
    stripHtml(html) { return (html || '').replace(/<\/p>/g, ' ').replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').trim().slice(0, 260) },
    go(id) { if (id) this.$router.push(`/lms/lesson-reports/${id}`) },
    notify(text, color = 'success') {
      this.$vs.notify({ title: color === 'danger' ? 'Lỗi' : 'Thông báo', text, color, iconPack: 'feather', icon: color === 'danger' ? 'icon-alert-triangle' : 'icon-check' })
    },
    load() {
      this.loaded = false
      this.error = ''
      axios.g(`/api/lms/lesson-reports/show/${this.scheduleId}`).then(res => {
        const d = res.data || {}
        if (d.status !== 1) { this.error = d.message || 'Không tải được dữ liệu'; this.loaded = true; return }
        const data = d.data
        if (data.redirect_schedule_id) {
          // Buổi đầu tuần → mở phiếu tuần (gắn buổi cuối tuần)
          this.$router.replace(`/lms/lesson-reports/${data.redirect_schedule_id}`)
          return
        }
        this.session = data.session
        this.report = Object.assign({ general_comment: '' }, data.report, { general_comment: data.report.general_comment || '' })
        this.students = (data.students || []).map(s => Object.assign(s, {
          attendance: this.normalizeAttendance(s.attendance),
          comment: s.comment || '',
          homework_score_text: s.homework_score === null || s.homework_score === undefined ? '' : String(s.homework_score).replace('.', ','),
          _saving: false, _saved: false, _error: false, _scoreErr: false
        }))
        this.prevId = data.prev_schedule_id
        this.nextId = data.next_schedule_id
        this.plan = data.plan || null
        this.canEditPlan = !!data.can_edit_plan
        this.prefillPending = false
        this.preview.studentId = 0
        // Phiếu còn trống → tự điền nội dung theo lộ trình (chưa lưu cho tới khi GV thao tác)
        if (this.plan && !this.report.lesson_title && !this.stripHtml(this.report.general_comment)) this.applyPlan(false)
        this.lastSavedAt = null
        this.hasError = false
        this.loaded = true
        this.$nextTick(() => this.growAll())
      }).catch(() => { this.error = 'Không tải được dữ liệu'; this.loaded = true })
    },

    /* ---------- Autosave ---------- */
    onEditorReady(quill) {
      // Chỉ tự lưu khi người dùng gõ (bỏ qua lần nạp nội dung từ API)
      quill.on('text-change', (delta, old, source) => {
        if (source === 'user') this.$nextTick(() => this.queueReport())
      })
    },
    queueReport() {
      if (!this.loaded) return
      this.prefillPending = false
      clearTimeout(this._reportTimer)
      this.pendingReport = true
      this._reportTimer = setTimeout(() => this.saveReport(), 900)
    },
    saveReport() {
      clearTimeout(this._reportTimer)
      if (!this.pendingReport) return Promise.resolve()
      this.pendingReport = null
      const r = this.report
      return this.post({
        report: { lesson_title: r.lesson_title || '', start_time: r.start_time || '', end_time: r.end_time || '', homework_deadline: r.homework_deadline || '', general_comment: r.general_comment || '' }
      })
    },
    queueStudent(st) {
      const v = this.parseScore(st.homework_score_text)
      st._scoreErr = !v.ok
      if (!v.ok) return
      this.$set(this.pendingStudents, st.student_id, true)
      clearTimeout(this._studentTimer)
      this._studentTimer = setTimeout(() => this.saveStudents(), 800)
    },
    takePrefill() {
      // Lần lưu đầu tiên sau khi tự điền lộ trình → lưu kèm nội dung bài học
      if (this.prefillPending) { this.prefillPending = false; this.pendingReport = true }
    },
    saveStudents() {
      this.takePrefill()
      if (this.pendingReport) this.saveReport()
      clearTimeout(this._studentTimer)
      const ids = Object.keys(this.pendingStudents)
      if (!ids.length) return Promise.resolve()
      this.pendingStudents = {}
      const rows = this.students.filter(s => ids.includes(String(s.student_id)))
      rows.forEach(s => { s._saving = true; s._saved = false; s._error = false })
      return this.post({
        students: rows.map(s => ({ student_id: s.student_id, homework_done: s.homework_done, homework_score: this.parseScore(s.homework_score_text).value, comment: s.comment }))
      }).then(ok => {
        rows.forEach(s => {
          s._saving = false
          s._saved = ok
          s._error = !ok
          if (ok) setTimeout(() => { s._saved = false }, 1500)
        })
      })
    },
    post(payload) {
      this.inflight++
      return axios.p('/api/lms/lesson-reports/save', Object.assign({ schedule_id: this.scheduleId }, payload))
        .then(res => {
          this.inflight--
          const d = res.data || {}
          if (d.status !== 1) { this.hasError = true; this.notify(d.message || 'Không lưu được', 'danger'); return false }
          this.hasError = false
          this.lastSavedAt = moment().format('HH:mm:ss')
          if (d.data && d.data.report_id && !this.report.id) {
            this.report.id = d.data.report_id
            this.report.status = d.data.status
          }
          return true
        })
        .catch(() => { this.inflight--; this.hasError = true; return false })
    },
    flush() {
      this.takePrefill()
      return Promise.all([this.saveReport(), this.saveStudents()])
    },
    parseScore(text) {
      const t = (text === null || text === undefined ? '' : String(text)).trim().replace(',', '.')
      if (t === '') return { ok: true, value: null }
      if (!/^\d{1,2}(\.\d)?$/.test(t)) return { ok: false, value: null }
      const n = parseFloat(t)
      return n >= 0 && n <= 10 ? { ok: true, value: n } : { ok: false, value: null }
    },
    onScore(st) {
      // Có điểm thì mặc định đã làm BTVN
      const v = this.parseScore(st.homework_score_text)
      if (v.ok && v.value !== null && st.homework_done === null) st.homework_done = 1
      this.queueStudent(st)
    },
    setHomework(st, val) {
      if (!this.weekStarted) return
      st.homework_done = st.homework_done === val ? null : val
      this.queueStudent(st)
    },

    /* ---------- Điểm danh (ghi thẳng schedule_has_student) ---------- */
    normalizeAttendance(att) {
      const out = {}
      Object.keys(att || {}).forEach(k => { out[k] = Object.assign({ _saving: false }, att[k]) })
      return out
    },
    setAttendance(st, w, val) {
      const a = this.att(st, w)
      if (!a || a.is_makeup || !a.shs_id || a._saving || this.isFuture(w)) return Promise.resolve(false)
      const prev = a.status
      const next = prev == val ? 0 : val
      a.status = next
      a._saving = true
      return axios.p('/api/lms/attendances/save', { id: a.shs_id, attendance_status: next, note: a.note })
        .then(res => {
          a._saving = false
          if (!res.data || res.data.status !== 1) { a.status = prev; this.notify((res.data && res.data.message) || 'Không lưu được điểm danh', 'danger'); return false }
          return true
        })
        .catch(() => { a._saving = false; a.status = prev; return false })
    },
    bulkPresent() {
      // Điểm danh có mặt cho các lượt chưa điểm danh của những buổi đã diễn ra
      const today = moment().format('YYYY-MM-DD')
      const list = []
      this.students.forEach(s => this.weekSessions.forEach(w => {
        const a = this.att(s, w)
        if (a && !a.is_makeup && a.status == 0 && w.class_date <= today) list.push([s, w])
      }))
      if (!list.length) return
      this.$vs.loading()
      Promise.all(list.map(([s, w]) => this.setAttendance(s, w, 1))).then(r => {
        this.$vs.loading.close()
        const fail = r.filter(x => !x).length
        this.notify(fail ? `Lỗi ${fail} lượt điểm danh` : `Đã điểm danh có mặt ${r.length} lượt`, fail ? 'warning' : 'success')
      })
    },
    bulkHomework() {
      if (!this.weekStarted) return
      this.students.forEach(s => {
        if (!this.weekAbsent(s) && s.homework_done !== 1) { s.homework_done = 1; this.$set(this.pendingStudents, s.student_id, true) }
      })
      this.saveStudents()
    },

    /* ---------- Lộ trình ---------- */
    applyPlan(confirm) {
      const p = this.plan
      if (!p) return
      const apply = () => {
        this.report.lesson_title = p.lesson_title || this.report.lesson_title || ''
        this.report.general_comment = p.content || ''
        if (p.homework_deadline && !this.report.homework_deadline) this.report.homework_deadline = p.homework_deadline
        if (confirm) { this.queueReport(); this.notify('Đã lấy nội dung theo lộ trình') } else this.prefillPending = true
      }
      if (!confirm || !this.stripHtml(this.report.general_comment)) return apply()
      this.$vs.dialog({ type: 'confirm', color: 'warning', title: 'Lấy nội dung theo lộ trình', text: 'Nội dung bài học hiện tại sẽ bị thay thế. Tiếp tục?', acceptText: 'Thay thế', cancelText: 'Hủy', accept: apply })
    },
    saveToPlan() {
      if (!this.session.product_id) return this.notify('Lớp chưa gắn khóa học, không lưu được lộ trình', 'danger')
      if (!this.report.lesson_title && !this.stripHtml(this.report.general_comment)) return this.notify('Chưa có nội dung bài học để lưu', 'danger')
      const doSave = () => axios.p('/api/lms/lesson-reports/plans/save', {
        product_id: this.session.product_id, week_no: this.session.week_no,
        lesson_title: this.report.lesson_title || '', content: this.report.general_comment || '', homework_deadline: this.report.homework_deadline || ''
      }).then(res => {
        const d = res.data || {}
        if (d.status !== 1) return this.notify(d.message || 'Không lưu được', 'danger')
        this.plan = { lesson_title: this.report.lesson_title, content: this.report.general_comment, homework_deadline: this.report.homework_deadline }
        this.notify(d.message)
      })
      if (!this.plan) return doSave()
      this.$vs.dialog({ type: 'confirm', color: 'warning', title: 'Lưu vào lộ trình', text: `Tuần ${this.session.week_no} của khóa học đã có nội dung lộ trình. Ghi đè bằng nội dung hiện tại?`, acceptText: 'Ghi đè', cancelText: 'Hủy', accept: doSave })
    },

    /* ---------- Mẫu / sao chép ---------- */
    insertTemplate() {
      const cur = this.stripHtml(this.report.general_comment)
      const apply = () => { this.report.general_comment = GENERAL_TEMPLATE; this.queueReport() }
      if (!cur) return apply()
      this.$vs.dialog({ type: 'confirm', color: 'warning', title: 'Chèn khung mẫu', text: 'Nội dung nhận xét chung hiện tại sẽ bị thay thế. Tiếp tục?', acceptText: 'Thay thế', cancelText: 'Hủy', accept: apply })
    },
    openCopy() {
      this.$vs.loading()
      axios.g(`/api/lms/lesson-reports/copy-sources/${this.scheduleId}`).then(res => {
        this.$vs.loading.close()
        this.copy.list = (res.data && res.data.data) || []
        this.copy.open = true
      }).catch(() => this.$vs.loading.close())
    },
    applyCopy(c) {
      const apply = () => {
        this.report.general_comment = c.general_comment || ''
        if (!this.report.lesson_title && c.lesson_title) this.report.lesson_title = c.lesson_title
        if (!this.report.homework_deadline && c.homework_deadline) this.report.homework_deadline = c.homework_deadline
        this.copy.open = false
        this.queueReport()
        this.notify('Đã sao chép, bạn có thể chỉnh sửa lại cho phù hợp')
      }
      if (!this.stripHtml(this.report.general_comment)) return apply()
      this.$vs.dialog({ type: 'confirm', color: 'warning', title: 'Sao chép nhận xét', text: 'Nội dung nhận xét chung hiện tại sẽ bị thay thế. Tiếp tục?', acceptText: 'Thay thế', cancelText: 'Hủy', accept: apply })
    },
    applyTemplate() {
      const text = this.tpl.text.trim()
      let n = 0
      this.students.forEach(s => {
        if (this.tpl.onlyEmpty && (s.comment || '').trim()) return
        s.comment = text.replace(/\{ten\}/gi, this.givenName(s.name)).replace(/\{ho_ten\}/gi, s.name)
        this.$set(this.pendingStudents, s.student_id, true)
        n++
      })
      this.tpl.open = false
      this.saveStudents()
      this.$nextTick(() => this.growAll())
      this.notify(`Đã áp mẫu cho ${n} học viên`)
    },

    /* ---------- Bàn phím / textarea ---------- */
    autoGrow(e) {
      const el = e.target || e
      el.style.height = 'auto'
      el.style.height = Math.max(72, el.scrollHeight + 2) + 'px'
    },
    growAll() {
      this.students.forEach((s, i) => { const r = this.$refs['note_' + i]; if (r && r[0]) this.autoGrow(r[0]) })
    },
    focusNext(idx) {
      const r = this.$refs['note_' + (idx + 1)]
      if (r && r[0]) { r[0].focus(); r[0].scrollIntoView({ block: 'center', behavior: 'smooth' }) }
    },

    /* ---------- Trạng thái / gửi ---------- */
    setStatus(status) {
      return this.flush().then(() => axios.p('/api/lms/lesson-reports/status', { schedule_id: this.scheduleId, status }))
        .then(res => {
          const d = res.data || {}
          if (d.status !== 1) { this.notify(d.message, 'danger'); return false }
          this.report.status = d.data.status
          this.report.share_token = d.data.share_token
          const tokens = d.data.student_tokens || {}
          this.students.forEach(s => { if (tokens[s.student_id]) this.$set(s, 'share_token', tokens[s.student_id]) })
          if (!this.report.id) this.report.id = true
          if (status === 2) this.report.sent_at = moment().format('YYYY-MM-DD HH:mm:ss')
          this.notify(d.message)
          return true
        })
    },
    openPreview() {
      this.flush()
      if (this.stats.pending) this.notify(`Còn ${this.stats.pending} lượt chưa điểm danh`, 'warning')
      this.preview.open = true
    },
    markSent() { this.setStatus(2) },
    zaloMessage(st) {
      const lines = [
        `📚 BÁO CÁO HỌC TẬP TUẦN ${this.session.week_no || ''} – LỚP ${this.session.cls_name}`,
        `👦 Học viên: ${st.name}`,
        `🗓 ${this.dateText} (${this.sessionsText})`,
        this.report.lesson_title ? `📖 ${this.report.lesson_title}` : '',
        this.report.homework_deadline ? `⏰ Hạn nộp bài: ${this.report.homework_deadline}` : '',
        `👉 Quý phụ huynh xem nhận xét của con tại: ${this.studentUrl(st)}`
      ]
      return lines.filter(Boolean).join('\n')
    },
    copyAllLinks() {
      const rows = this.students.filter(s => this.studentUrl(s)).map(s => `${s.name}: ${this.studentUrl(s)}`)
      if (!rows.length) return this.notify('Chưa có link, vui lòng đánh dấu hoàn thành lại', 'danger')
      const text = `Báo cáo tuần ${this.session.week_no || ''} – lớp ${this.session.cls_name} (${this.dateText})\n` + rows.join('\n')
      const done = () => this.notify(`Đã sao chép ${rows.length} link – gửi riêng cho từng phụ huynh`)
      if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(done).catch(() => this.fallbackCopy(text, done))
      } else this.fallbackCopy(text, done)
    },
    copyMessage(st) {
      const text = this.zaloMessage(st)
      const done = () => this.notify(`Đã sao chép – gửi riêng cho phụ huynh ${st.name}`)
      if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(done).catch(() => this.fallbackCopy(text, done))
      } else this.fallbackCopy(text, done)
    },
    fallbackCopy(text, done) {
      const ta = document.createElement('textarea')
      ta.value = text
      ta.style.position = 'fixed'; ta.style.opacity = '0'
      document.body.appendChild(ta)
      ta.select()
      try { document.execCommand('copy'); done() } catch (e) { this.notify('Trình duyệt không cho phép sao chép', 'danger') }
      document.body.removeChild(ta)
    },
    printReport() {
      const el = document.getElementById('lesson-report-print')
      if (!el) return
      const styles = Array.from(document.querySelectorAll('style, link[rel="stylesheet"]')).map(n => n.outerHTML).join('')
      const w = window.open('', '_blank')
      if (!w) return this.notify('Trình duyệt chặn cửa sổ in, vui lòng cho phép pop-up', 'danger')
      w.document.write(`<!doctype html><html><head><meta charset="utf-8"><title>Báo cáo tuần ${this.session.week_no || ''} ${this.session.cls_name}</title><base href="${window.location.origin}/">${styles}<style>body{background:#fff;margin:0;padding:12px}</style></head><body>${el.innerHTML}</body></html>`)
      w.document.close()
      w.onload = () => setTimeout(() => { w.focus(); w.print() }, 300)
    }
  }
}
</script>

<style>
.lrd-page { font-size: 14px; color: #334155; padding-bottom: 80px; }
.lrd-body { display: flex; flex-direction: column; gap: 16px; }
.lrd-card { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(15, 23, 42, 0.06); padding: 18px 20px; }
.lrd-empty { text-align: center; padding: 60px 20px; color: #64748b; font-size: 15px; }
.nowrap { white-space: nowrap !important; }

/* Top bar */
.lrd-topbar { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 14px; }
.lrd-topbar__nav { display: flex; gap: 8px; }
.lrd-savestate { margin-left: auto; font-size: 13px; color: #64748b; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
.lrd-savestate.ok { color: #16a34a; }
.lrd-savestate.busy { color: #7367f0; }
.lrd-savestate.err { color: #dc2626; }

/* Buttons */
.lrd-btn { display: inline-flex; align-items: center; gap: 7px; white-space: nowrap; height: 38px; padding: 0 14px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; border: 1.5px solid transparent; transition: all .15s; text-decoration: none !important; }
.lrd-btn:disabled { background: #f1f5f9 !important; border: 1px dashed #cbd5e1 !important; color: #94a3b8 !important; cursor: not-allowed; box-shadow: none !important; }
.lrd-btn.ghost { background: #fff; border-color: #cbd5e1; color: #475569; }
.lrd-btn.ghost:not(:disabled):hover { border-color: #7367f0; color: #7367f0; box-shadow: 0 0 0 3px rgba(115, 103, 240, 0.2); }
.lrd-btn.soft { background: rgba(115, 103, 240, 0.1); color: #5e50ee; }
.lrd-btn.soft:hover { background: rgba(115, 103, 240, 0.18); }
.lrd-btn.primary { background: #7367f0; color: #fff; }
.lrd-btn.primary:hover { box-shadow: 0 6px 14px rgba(115, 103, 240, 0.35); }
.lrd-btn.success { background: #28c76f; color: #fff; }
.lrd-btn.success:hover { box-shadow: 0 6px 14px rgba(40, 199, 111, 0.35); }

/* Hero */
.lrd-hero { display: flex; align-items: center; gap: 18px; flex-wrap: wrap; }
.lrd-hero__avatar { width: 58px; height: 58px; border-radius: 14px; background: linear-gradient(135deg, #f05a28, #ff8a5c); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 26px; flex-shrink: 0; }
.lrd-hero__info { flex: 1; min-width: 280px; }
.lrd-hero__name { font-size: 20px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.lrd-hero__meta { display: flex; flex-wrap: wrap; gap: 4px 18px; font-size: 14px; color: #475569; margin-top: 6px; }
.lrd-hero__meta i { color: #f05a28; margin-right: 6px; }
.lrd-kpis { display: flex; gap: 10px; flex-wrap: wrap; }
.lrd-kpi { min-width: 104px; background: #f8fafc; border: 1px solid #eef2f7; border-radius: 10px; padding: 8px 12px; }
.lrd-kpi__k { font-size: 13px; color: #64748b; font-weight: 600; white-space: nowrap; }
.lrd-kpi__v { font-size: 22px; font-weight: 800; color: #1e293b; line-height: 1.25; }
.lrd-kpi.ok .lrd-kpi__v { color: #16a34a; }
.lrd-kpi.brand .lrd-kpi__v { color: #f05a28; }
.lrd-kpi.warn { background: #fff7ed; border-color: #fed7aa; }
.lrd-kpi.warn .lrd-kpi__v { color: #ea580c; }
.lrd-kpi.danger { background: #fef2f2; border-color: #fecaca; }
.lrd-kpi.danger .lrd-kpi__v { color: #dc2626; }
.lrd-note { background: rgba(115, 103, 240, 0.08); color: #4c3fd6; border-radius: 10px; padding: 10px 14px; font-size: 14px; }
.lrd-note i { margin-right: 8px; }
.lrd-note.lock { background: #f1f5f9; color: #475569; margin-bottom: 10px; }

/* Card title */
.lrd-card__title { display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap; font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 14px; }
.lrd-card__title > span > i { color: #f05a28; margin-right: 8px; }
.lrd-card__actions { display: flex; gap: 8px; flex-wrap: wrap; }
.lrd-pill { display: inline-flex; white-space: nowrap; font-size: 13px; font-weight: 600; background: #f1f5f9; color: #475569; border-radius: 99px; padding: 3px 10px; margin-left: 6px; }
.lrd-hint { font-size: 13px; color: #64748b; margin-bottom: 10px; }
.lrd-hint i { margin-right: 6px; }
.lrd-hint kbd { font-size: 13px; background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0 5px; color: #334155; box-shadow: none; }

/* Fields */
.lrd-grid { display: grid; grid-template-columns: 2fr 1.2fr 1.4fr; gap: 14px; margin-bottom: 14px; }
.lrd-grid .span-2 { grid-column: span 1; }
@media (max-width: 992px) { .lrd-grid { grid-template-columns: 1fr; } }
.lrd-field > label { display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; }
.lrd-time { display: flex; align-items: center; gap: 6px; }
.lrd-page input.lrd-input, .lrd-page input.lrd-input:not(:disabled):not([readonly]), .lrd-popup input.lrd-input {
  width: 100%; height: 40px; background: #fff !important; border: 1.5px solid #cbd5e1 !important; border-radius: 8px !important;
  padding: 0 12px !important; font-size: 14px !important; color: #1e293b !important; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05) !important; cursor: text; transition: border-color .15s, box-shadow .15s;
}
.lrd-page input.lrd-input:not(:disabled):not([readonly]):hover, .lrd-page input.lrd-input:not(:disabled):not([readonly]):focus {
  border-color: #7367f0 !important; box-shadow: 0 0 0 3px rgba(115, 103, 240, 0.2) !important; outline: none;
}
.lrd-page input.lrd-input.invalid:not(:disabled) { border-color: #ef4444 !important; box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15) !important; }
.lrd-page textarea.lrd-textarea, .lrd-popup textarea.lrd-textarea {
  width: 100%; min-height: 72px; resize: vertical; background: #fff !important; border: 1.5px solid #cbd5e1 !important; border-radius: 8px !important;
  padding: 8px 12px !important; font-size: 14px !important; line-height: 1.55; color: #1e293b !important; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05) !important; transition: border-color .15s, box-shadow .15s; font-family: inherit;
}
.lrd-page textarea.lrd-textarea:hover, .lrd-page textarea.lrd-textarea:focus, .lrd-popup textarea.lrd-textarea:focus {
  border-color: #7367f0 !important; box-shadow: 0 0 0 3px rgba(115, 103, 240, 0.2) !important; outline: none;
}

/* Quill */
.lrd-editor .ql-toolbar.ql-snow { border: 1.5px solid #cbd5e1; border-radius: 8px 8px 0 0; background: #f8fafc; }
.lrd-editor .ql-container.ql-snow { border: 1.5px solid #cbd5e1; border-top: none; border-radius: 0 0 8px 8px; font-size: 14px; font-family: inherit; }
.lrd-editor .ql-editor { min-height: 220px; line-height: 1.7; }
.lrd-editor .ql-editor.ql-blank::before { font-style: normal; color: #94a3b8; font-size: 14px; }
.lrd-editor:focus-within .ql-container.ql-snow, .lrd-editor:focus-within .ql-toolbar.ql-snow { border-color: #7367f0; }

/* Table */
.lrd-table-wrap { overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 10px; }
.lrd-table { width: 100%; border-collapse: separate; border-spacing: 0; font-size: 14px; }
.lrd-table th { position: sticky; top: 0; z-index: 1; background: #f8fafc; color: #475569; font-weight: 700; font-size: 13px; text-align: center; padding: 10px; border-bottom: 1px solid #e2e8f0; white-space: nowrap; }
.lrd-table td { padding: 10px; border-bottom: 1px solid #eef2f7; vertical-align: top; background: #fff; }
.lrd-table tbody tr:hover td { background: #fafaff; }
.lrd-table tr.absent td { background: #fafafa; }
.lrd-table tr.absent .lrd-strong { color: #94a3b8; }
.lrd-table .c-stt { width: 44px; text-align: center; color: #64748b; padding-top: 18px; }
.lrd-table .c-name { min-width: 220px; width: 240px; }
.lrd-table .c-att { width: 1%; text-align: left; padding-top: 12px; }
.lrd-th-sub { font-weight: 500; color: #94a3b8; }

/* Điểm danh: 1 ô / học viên, mỗi buổi 1 chip */
.lrd-days { display: flex; gap: 6px; flex-wrap: nowrap; }
.lrd-day { display: inline-flex; flex-direction: column; align-items: flex-start; min-width: 92px; padding: 5px 10px; border-radius: 8px; border: 1.5px solid #cbd5e1; background: #fff; font-size: 13px; line-height: 1.35; text-align: left; white-space: nowrap !important; transition: border-color .12s, box-shadow .12s, background .12s; }
.lrd-day__d { font-weight: 700; color: #475569; }
.lrd-day__s { font-weight: 600; display: inline-flex; align-items: center; gap: 5px; }
.lrd-day.clickable { cursor: pointer; }
.lrd-day.clickable:hover { border-color: #7367f0; box-shadow: 0 0 0 3px rgba(115, 103, 240, 0.2); }
.lrd-day.busy { opacity: .6; pointer-events: none; }
.lrd-day.pending .lrd-day__s { color: #ea580c; }
.lrd-day.present { background: #f0fdf4; border-color: #86efac; }
.lrd-day.present .lrd-day__s { color: #15803d; }
.lrd-day.absent { background: #fef2f2; border-color: #fca5a5; }
.lrd-day.absent .lrd-day__s { color: #b91c1c; }
.lrd-day.future, .lrd-day.none { background: #f1f5f9; border: 1px dashed #cbd5e1; cursor: not-allowed; }
.lrd-day.future .lrd-day__d, .lrd-day.future .lrd-day__s, .lrd-day.none .lrd-day__d, .lrd-day.none .lrd-day__s { color: #94a3b8; }
.lrd-table .c-hw { width: 100px; text-align: center; padding-top: 14px; }
.lrd-table .c-score { width: 96px; text-align: center; padding-top: 12px; }
.lrd-table .c-note { min-width: 360px; }
.lrd-student { display: flex; align-items: flex-start; gap: 10px; }
.lrd-student__avatar { width: 36px; height: 36px; border-radius: 50%; background: #fff1ea; color: #f05a28; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.lrd-strong { font-weight: 600; color: #1e293b; }
.lrd-sub { font-size: 13px; color: #64748b; }
.lrd-tag { display: inline-flex; white-space: nowrap; font-size: 13px; font-weight: 600; color: #7c3aed; background: #f3e8ff; border-radius: 99px; padding: 1px 8px; margin-top: 3px; }
.lrd-empty-row { text-align: center; color: #94a3b8; padding: 30px 10px !important; }

/* Segmented */
.lrd-seg { display: inline-flex; border: 1.5px solid #cbd5e1; border-radius: 8px; overflow: hidden; background: #fff; }
.lrd-seg.busy { opacity: .6; pointer-events: none; }
.lrd-seg button { border: none; background: #fff; color: #64748b; font-size: 13.5px; font-weight: 600; padding: 7px 10px; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; white-space: nowrap; transition: background .12s, color .12s; }
.lrd-seg button + button { border-left: 1.5px solid #cbd5e1; }
.lrd-seg button:hover { background: #f5f3ff; color: #5e50ee; }
.lrd-seg button.ok.on { background: #dcfce7; color: #15803d; }
.lrd-seg button.no.on { background: #fee2e2; color: #b91c1c; }
.lrd-seg.sm button { padding: 7px 12px; }
.lrd-seg.locked { background: #f1f5f9; border: 1px dashed #cbd5e1; }
.lrd-seg.locked button { background: #f1f5f9; color: #94a3b8; cursor: not-allowed; }
.lrd-page input.lrd-input:disabled, .lrd-page textarea.lrd-textarea:disabled { background: #f1f5f9 !important; border: 1px dashed #cbd5e1 !important; color: #94a3b8 !important; cursor: not-allowed; box-shadow: none !important; }
.lrd-score { text-align: center; font-weight: 700; }
.lrd-page input.lrd-score { padding: 0 6px !important; width: 72px; }
.lrd-err { color: #dc2626; font-size: 13px; margin-top: 2px; }
.lrd-note-cell { position: relative; }
.lrd-flag { position: absolute; right: 8px; top: 8px; width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 13px; color: #7367f0; background: #fff; }
.lrd-flag.ok { background: #22c55e; color: #fff; }
.lrd-flag.err { background: #ef4444; color: #fff; }

/* Badges */
.lrd-badge { display: inline-flex !important; align-items: center; white-space: nowrap !important; font-size: 13px; font-weight: 600; border-radius: 99px; padding: 3px 10px; }
.lrd-badge.none { background: #fee2e2; color: #b91c1c; }
.lrd-badge.draft { background: #fef3c7; color: #b45309; }
.lrd-badge.done { background: #dcfce7; color: #15803d; }
.lrd-badge.sent { background: rgba(115, 103, 240, 0.12); color: #5e50ee; }

/* Action bar */
.lrd-actionbar { position: sticky; bottom: 0; z-index: 5; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; background: rgba(255, 255, 255, 0.96); backdrop-filter: blur(6px); border-radius: 12px; box-shadow: 0 -4px 20px rgba(15, 23, 42, 0.1); padding: 12px 18px; }
.lrd-actionbar__progress { display: flex; align-items: center; gap: 12px; font-size: 14px; color: #475569; }
.lrd-actionbar__btns { display: flex; gap: 8px; flex-wrap: wrap; }
.lrd-progress { width: 180px; height: 8px; background: #f1f5f9; border-radius: 99px; overflow: hidden; }
.lrd-progress__bar { height: 100%; background: linear-gradient(90deg, #f05a28, #ff8a5c); border-radius: 99px; transition: width .3s; }

/* Popups */
.lrd-popup .vs-popup--title h3 { font-size: 17px; }
.lrd-popup--preview .vs-popup { background: #f1f5f9; }
.lrd-preview__bar { position: sticky; top: 0; z-index: 2; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; background: #fff; border-radius: 10px; padding: 10px 14px; margin-bottom: 14px; box-shadow: 0 2px 10px rgba(15, 23, 42, 0.06); }
.lrd-preview__info { font-size: 14px; color: #475569; display: flex; align-items: center; gap: 8px; min-width: 0; }
.lrd-preview__url { color: #5e50ee; font-weight: 600; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 520px; }
.lrd-preview__btns { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
.lrd-copy { max-height: 60vh; overflow-y: auto; display: flex; flex-direction: column; gap: 10px; }
.lrd-copy__item { border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 10px 14px; cursor: pointer; transition: all .15s; }
.lrd-copy__item:hover { border-color: #7367f0; box-shadow: 0 0 0 3px rgba(115, 103, 240, 0.15); }
.lrd-copy__head { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; font-size: 14px; }
.lrd-copy__title { font-size: 14px; font-weight: 600; color: #f05a28; margin-top: 4px; }
.lrd-copy__preview { font-size: 13px; color: #64748b; margin-top: 4px; }
.lrd-tpl { display: flex; flex-direction: column; gap: 12px; }
.lrd-tpl code { font-size: 13px; background: #f1f5f9; padding: 0 4px; border-radius: 4px; color: #be185d; }
.lrd-tpl__btns { display: flex; justify-content: flex-end; gap: 8px; }
.lrd-select { height: 36px; min-width: 200px; max-width: 280px; background: #fff; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 0 8px; font-size: 14px; color: #1e293b; cursor: pointer; }
.lrd-select:hover, .lrd-select:focus { border-color: #7367f0; box-shadow: 0 0 0 3px rgba(115, 103, 240, 0.2); outline: none; }
.lrd-btn.sm { height: 32px; padding: 0 10px; font-size: 13px; }
.lrd-links { background: #fff; border-radius: 10px; padding: 12px 14px; margin-bottom: 14px; box-shadow: 0 2px 10px rgba(15, 23, 42, 0.06); max-height: 240px; overflow-y: auto; }
.lrd-links__head { font-size: 14px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
.lrd-links__head i { color: #f05a28; margin-right: 8px; }
.lrd-links__row { display: grid; grid-template-columns: 200px minmax(0, 1fr) auto; align-items: center; gap: 10px; padding: 6px 0; border-top: 1px dashed #e2e8f0; font-size: 14px; }
.lrd-links__btns { display: flex; gap: 6px; }
@media (max-width: 768px) { .lrd-links__row { grid-template-columns: 1fr; } }
.lrd-check { display: inline-flex; align-items: center; gap: 8px; font-size: 14px; cursor: pointer; }
</style>
