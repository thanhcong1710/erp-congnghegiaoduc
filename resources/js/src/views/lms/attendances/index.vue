<template>
  <div id="page-attendances" class="att-page mt-5">
    <!-- ============ BỘ LỌC ============ -->
    <div class="att-card att-filter">
      <div class="att-field">
        <label>Trung tâm <span class="text-danger">(*)</span></label>
        <vue-select
          label="name"
          placeholder="Chọn trung tâm"
          :options="html.branches.list"
          v-model="html.branches.item"
          :searchable="true"
          language="tv-VN"
          @input="saveBranch"
        ></vue-select>
      </div>
      <div class="att-field">
        <label>Khóa học <span class="text-danger">(*)</span></label>
        <vue-select
          label="name"
          placeholder="Chọn khóa học"
          :options="html.products.list"
          v-model="html.products.item"
          :searchable="true"
          language="tv-VN"
          @input="saveProduct"
        ></vue-select>
      </div>
      <div class="att-field grow">
        <label>Lớp học <span class="text-danger">(*)</span></label>
        <vue-select
          label="cls_name"
          :placeholder="html.classes.list.length ? 'Chọn lớp học' : 'Chọn trung tâm & khóa học trước'"
          :options="html.classes.list"
          v-model="html.classes.item"
          :searchable="true"
          :disabled="!html.classes.list.length"
          language="tv-VN"
          @input="saveClass"
        ></vue-select>
      </div>
      <div class="att-field">
        <label>Tháng điểm danh</label>
        <div class="att-month">
          <button type="button" class="att-month__nav" title="Tháng trước" @click="shiftMonth(-1)"><i class="fa-solid fa-chevron-left"></i></button>
          <datepicker
            class="att-month__picker"
            v-model="att.date_select"
            value-type="format"
            format="MM/YYYY"
            type="month"
            :clearable="false"
            :lang="datepickerOptions.lang"
            @change="loadStudents"
          />
          <button type="button" class="att-month__nav" title="Tháng sau" @click="shiftMonth(1)"><i class="fa-solid fa-chevron-right"></i></button>
          <button type="button" class="att-month__today" v-if="att.date_select !== currentMonth" @click="goCurrentMonth">Tháng này</button>
        </div>
      </div>
    </div>

    <!-- Wrapper cố định: vs-popup tự di chuyển DOM ra <body>, nếu khối v-if/v-else nằm sát popup
         thì Vue sẽ insertBefore vào node đã bị chuyển đi → lỗi, bảng không render -->
    <div class="att-body">
    <!-- ============ EMPTY ============ -->
    <div v-if="!class_info.class_id" class="att-card att-empty">
      <div class="att-empty__icon"><i class="fa-solid fa-clipboard-user"></i></div>
      <div class="att-empty__title">Chưa chọn lớp học</div>
      <div class="att-empty__desc">Chọn trung tâm, khóa học và lớp học để bắt đầu điểm danh.</div>
    </div>

    <template v-else>
      <!-- ============ BANNER LỚP ============ -->
      <div class="att-card att-hero">
        <div class="att-hero__avatar"><i class="fa-solid fa-people-group"></i></div>
        <div class="att-hero__info">
          <div class="att-hero__name">{{ class_info.cls_name }}</div>
          <div class="att-hero__meta">
            <span><i class="fa-regular fa-calendar"></i>{{ fmtDate(class_info.cls_startdate) }} – {{ fmtDate(class_info.cls_enddate) }}</span>
            <span v-if="class_info.class_day_text"><i class="fa-regular fa-clock"></i>{{ class_info.class_day_text }}</span>
            <span v-if="class_info.shift_text || class_info.room_text"><i class="fa-solid fa-door-open"></i>{{ [class_info.shift_text, class_info.room_text].filter(Boolean).join(' · ') }}</span>
          </div>
          <div class="att-hero__meta">
            <span><i class="fa-solid fa-chalkboard-user"></i>GV: <strong>{{ class_info.teacher_name || '—' }}</strong></span>
            <span><i class="fa-solid fa-user-tie"></i>CM: <strong>{{ class_info.cm_name || '—' }}</strong></span>
          </div>
        </div>
        <div class="att-kpis">
          <div class="att-kpi">
            <div class="att-kpi__label">Sĩ số</div>
            <div class="att-kpi__value">{{ class_info.num_students }}<small>/{{ class_info.max_students }}</small></div>
          </div>
          <div class="att-kpi">
            <div class="att-kpi__label">Buổi trong tháng</div>
            <div class="att-kpi__value">{{ shedules.length }}</div>
          </div>
          <div class="att-kpi" :class="{ warn: stats.pending > 0 }">
            <div class="att-kpi__label">Chưa điểm danh</div>
            <div class="att-kpi__value">{{ stats.pending }}</div>
          </div>
          <div class="att-kpi" :class="{ danger: stats.absent > 0 }">
            <div class="att-kpi__label">Lượt nghỉ học</div>
            <div class="att-kpi__value">{{ stats.absent }}</div>
          </div>
          <div class="att-kpi ok">
            <div class="att-kpi__label">Tỉ lệ đi học</div>
            <div class="att-kpi__value">{{ stats.rate }}<small>%</small></div>
          </div>
        </div>
      </div>

      <!-- ============ BẢNG ĐIỂM DANH ============ -->
      <div class="att-card">
        <div class="att-toolbar">
          <div class="att-toolbar__title">
            <i class="fa-solid fa-list-check mr-2"></i>Điểm danh tháng {{ monthLabel }}
            <span class="att-pill">{{ students.length }} học sinh</span>
          </div>
          <div class="att-toolbar__right">
            <div class="att-legend">
              <span v-for="s in statusList" :key="s.value" :class="'st-' + s.value"><i></i>{{ s.label }}</span>
            </div>
            <div class="att-search">
              <i class="fa-solid fa-magnifying-glass"></i>
              <input type="text" class="att-search__input" v-model="keyword" placeholder="Lọc theo tên, mã học sinh..." />
            </div>
          </div>
        </div>

        <div v-if="!shedules.length" class="att-table-empty">
          <i class="fa-regular fa-calendar-xmark"></i> Lớp không có buổi học nào trong tháng {{ monthLabel }}
        </div>
        <div v-else class="att-grid-wrap">
          <table class="att-grid">
            <thead>
              <tr>
                <th class="sticky c-stt">STT</th>
                <th class="sticky c-name">Học sinh</th>
                <th
                  v-for="(item, index) in shedules"
                  :key="index"
                  class="c-session"
                  :class="{ today: item.class_date === today, future: item.class_date > today }"
                >
                  <div class="att-sess__code">{{ item.code }} · B{{ item.subject_stt }}</div>
                  <div class="att-sess__date">{{ item.class_date | formatDateViewDay }}</div>
                  <div class="att-sess__count">
                    <span class="ok" title="Đi học / tổng">{{ columnStats[item.date_key].present }}/{{ columnStats[item.date_key].total }}</span>
                    <span class="absent" v-if="columnStats[item.date_key].absent" title="Nghỉ học">· nghỉ {{ columnStats[item.date_key].absent }}</span>
                  </div>
                  <button
                    v-if="columnStats[item.date_key].pending > 0 && item.class_date <= today"
                    type="button"
                    class="att-sess__bulk"
                    :title="'Đánh dấu ĐI HỌC cho ' + columnStats[item.date_key].pending + ' học sinh chưa điểm danh'"
                    @click="bulkPresent(item)"
                  ><i class="fa-solid fa-check-double"></i> Cả lớp đi học</button>
                </th>
                <th class="c-sum">Đi học</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in filteredStudents" :key="item.student_id">
                <td class="sticky c-stt">{{ index + 1 }}</td>
                <td class="sticky c-name">
                  <div class="att-student">
                    <div class="att-student__avatar">{{ initials(item.name) }}</div>
                    <div>
                      <div class="att-strong">{{ item.name }}</div>
                      <div class="att-sub">{{ item.lms_code }}</div>
                    </div>
                  </div>
                </td>
                <td
                  v-for="sche in shedules"
                  :key="sche.date_key"
                  class="c-session"
                  :class="{ today: sche.class_date === today }"
                >
                  <template v-if="cell(item, sche) && cell(item, sche).id">
                    <div class="att-cell">
                      <select
                        class="att-status"
                        :class="'st-' + cell(item, sche).attendance_status"
                        :disabled="cell(item, sche).saving"
                        v-model="cell(item, sche).attendance_status"
                        @focus="cell(item, sche).prev_status = cell(item, sche).attendance_status"
                        @change="save(cell(item, sche))"
                      >
                        <option v-for="st in statusList" :key="st.value" :value="String(st.value)">{{ st.label }}</option>
                      </select>
                      <span class="att-cell__flag" v-if="cell(item, sche).saving"><i class="fa-solid fa-spinner fa-spin"></i></span>
                      <span class="att-cell__flag ok" v-else-if="cell(item, sche).saved"><i class="fa-solid fa-check"></i></span>
                    </div>
                  </template>
                  <span v-else class="att-na" title="Học sinh không có lịch buổi này">—</span>
                </td>
                <td class="c-sum">
                  <strong>{{ rowStats(item).present }}</strong><span class="att-sub">/{{ rowStats(item).total }}</span>
                  <div v-if="rowStats(item).absent" class="att-absent-text">nghỉ {{ rowStats(item).absent }}</div>
                </td>
              </tr>
              <tr v-if="!filteredStudents.length">
                <td :colspan="shedules.length + 3" class="att-table-empty">
                  <i class="fa-regular fa-face-meh-blank"></i>
                  {{ students.length ? 'Không có học sinh khớp từ khóa lọc' : 'Lớp chưa có học sinh nào trong tháng này' }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </template>
    </div>

  </div>
</template>

<script>
  import select from 'vue-select'
  import axios from '../../../http/axios.js'
  import datepicker from "vue2-datepicker";
  import moment from 'moment';

  const LS_KEY = 'attendances_filter'

  export default {
    components: {
      datepicker,
      "vue-select": select,
    },
    data() {
      return {
        datepickerOptions: {
          lang: {
            days: ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
            months: ["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"]
          }
        },
        statusList: [
          { value: 0, label: 'Chưa điểm danh' },
          { value: 1, label: 'Đi học' },
          { value: 2, label: 'Nghỉ học' },
        ],
        html: {
          branches: { item: '', list: [] },
          products: { item: '', list: [] },
          classes: { item: '', list: [] }
        },
        att: {
          branch_id: '',
          product_id: '',
          class_id: '',
          date_select: moment().format('MM/YYYY')
        },
        keyword: '',
        today: moment().format('YYYY-MM-DD'),
        currentMonth: moment().format('MM/YYYY'),
        students: [],
        class_info: {},
        shedules: [],
      }
    },
    computed: {
      apiMonth() {
        const m = moment(this.att.date_select, 'MM/YYYY')
        return m.isValid() ? m.format('YYYY-MM') : ''
      },
      monthLabel() {
        return this.att.date_select
      },
      filteredStudents() {
        const kw = this.normalize(this.keyword)
        if (!kw) return this.students
        return this.students.filter(s => [s.name, s.lms_code].some(v => this.normalize(v).indexOf(kw) > -1))
      },
      columnStats() {
        const res = {}
        this.shedules.forEach(sc => {
          let total = 0, present = 0, pending = 0, absent = 0
          this.students.forEach(st => {
            const c = st.attendances && st.attendances[sc.date_key]
            if (!c || !c.id) return
            total++
            if (c.attendance_status == 1) present++
            else if (c.attendance_status == 2) absent++
            else pending++
          })
          res[sc.date_key] = { total, present, pending, absent }
        })
        return res
      },
      stats() {
        let pending = 0, present = 0, absent = 0
        this.shedules.forEach(sc => {
          const c = this.columnStats[sc.date_key]
          present += c.present
          absent += c.absent
          if (sc.class_date <= this.today) pending += c.pending
        })
        const done = present + absent
        return { pending, absent, rate: done ? Math.round(present * 100 / done) : 0 }
      },
    },
    created() {
      const saved = this.readSaved()
      Promise.all([
        axios.g(`/api/system/branches-has-user`),
        axios.g(`/api/system/products`)
      ]).then(([branches, products]) => {
        this.html.branches.list = branches.data
        this.html.products.list = products.data
        let branch = (branches.data || []).find(b => b.id == saved.branch_id)
        if (!branch && branches.data && branches.data.length === 1) branch = branches.data[0]
        const product = (products.data || []).find(p => p.id == saved.product_id)
        if (branch) { this.html.branches.item = branch; this.att.branch_id = branch.id }
        if (product) { this.html.products.item = product; this.att.product_id = product.id }
        this.loadClasses(saved.class_id)
      })
    },
    methods: {
      normalize(v) {
        return (v || '').toString().toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '').replace(/đ/g, 'd')
      },
      fmtDate(v) {
        if (!v || v === '0000-00-00') return ''
        const m = moment(v)
        return m.isValid() ? m.format('DD/MM/YYYY') : v
      },
      initials(name) {
        const parts = (name || '').trim().split(/\s+/)
        return parts.length ? parts[parts.length - 1].charAt(0).toUpperCase() : '?'
      },
      cell(student, sche) {
        return student.attendances ? student.attendances[sche.date_key] : null
      },
      rowStats(student) {
        let total = 0, present = 0, absent = 0
        this.shedules.forEach(sc => {
          const c = this.cell(student, sc)
          if (!c || !c.id) return
          total++
          if (c.attendance_status == 1) present++
          else if (c.attendance_status == 2) absent++
        })
        return { total, present, absent }
      },
      readSaved() {
        try { return JSON.parse(localStorage.getItem(LS_KEY)) || {} } catch (e) { return {} }
      },
      persistFilter() {
        try {
          localStorage.setItem(LS_KEY, JSON.stringify({
            branch_id: this.att.branch_id,
            product_id: this.att.product_id,
            class_id: this.att.class_id
          }))
        } catch (e) { /* ignore */ }
      },
      resetClass() {
        this.att.class_id = ''
        this.html.classes.item = ''
        this.class_info = {}
        this.students = []
        this.shedules = []
      },
      saveBranch(data = null) {
        this.att.branch_id = data && typeof data === 'object' ? data.id : ''
        this.resetClass()
        this.loadClasses()
      },
      saveProduct(data = null) {
        this.att.product_id = data && typeof data === 'object' ? data.id : ''
        this.resetClass()
        this.loadClasses()
      },
      saveClass(data = null) {
        this.att.class_id = data && typeof data === 'object' ? data.id : ''
        this.keyword = ''
        this.persistFilter()
        this.loadStudents()
      },
      loadClasses(restoreClassId = null) {
        this.persistFilter()
        if (this.att.branch_id && this.att.product_id) {
          this.$vs.loading();
          axios.p(`/api/lms/attendances/load-classes`, {
            branch_id: this.att.branch_id,
            product_id: this.att.product_id
          })
            .then(response => {
              this.$vs.loading.close();
              this.html.classes.list = response.data || []
              if (restoreClassId) {
                const cls = this.html.classes.list.find(c => c.id == restoreClassId)
                if (cls) {
                  this.html.classes.item = cls
                  this.att.class_id = cls.id
                  this.loadStudents()
                }
              }
            })
            .catch(() => this.$vs.loading.close())
        } else {
          this.html.classes.list = []
        }
      },
      shiftMonth(step) {
        const m = moment(this.att.date_select, 'MM/YYYY')
        this.att.date_select = (m.isValid() ? m : moment()).add(step, 'months').format('MM/YYYY')
        this.loadStudents()
      },
      goCurrentMonth() {
        this.att.date_select = this.currentMonth
        this.loadStudents()
      },
      loadStudents() {
        if (this.att.class_id && this.apiMonth) {
          this.$vs.loading();
          axios.p(`/api/lms/attendances/load-students`, {
            class_id: this.att.class_id,
            date_select: this.apiMonth
          })
            .then(response => {
              this.$vs.loading.close();
              const students = response.data.students || []
              // Bổ sung field trạng thái UI để Vue reactive
              students.forEach(st => {
                Object.keys(st.attendances || {}).forEach(k => {
                  const c = st.attendances[k]
                  if (c && c.id) {
                    // Dữ liệu cũ: 3 = "Đã học bù" → coi như Đi học
                    const st = c.attendance_status == 3 ? 1 : (c.attendance_status == null ? 0 : c.attendance_status)
                    c.attendance_status = String(st)
                    this.$set(c, 'saving', false)
                    this.$set(c, 'saved', false)
                    this.$set(c, 'prev_status', c.attendance_status)
                  }
                })
              })
              this.class_info = response.data.class_info || {}
              this.students = students
              this.shedules = response.data.shedules || []
            })
            .catch(() => this.$vs.loading.close())
        } else {
          this.class_info = {}
          this.students = []
          this.shedules = []
        }
      },
      payload(c) {
        return { id: c.id, attendance_status: c.attendance_status, note: c.note }
      },
      save(data) {
        data.saving = true
        data.saved = false
        axios.p(`/api/lms/attendances/save`, this.payload(data))
          .then(() => {
            data.saving = false
            data.saved = true
            data.prev_status = data.attendance_status
            setTimeout(() => { data.saved = false }, 1500)
          })
          .catch(() => {
            data.saving = false
            data.attendance_status = data.prev_status
            this.$vs.notify({ title: 'Lỗi', text: 'Không lưu được điểm danh, vui lòng thử lại', color: 'danger' })
          })
      },
      bulkPresent(sche) {
        const cells = this.students
          .map(st => this.cell(st, sche))
          .filter(c => c && c.id && c.attendance_status == 0)
        if (!cells.length) return
        this.$vs.dialog({
          type: 'confirm',
          color: 'success',
          title: 'Điểm danh nhanh',
          text: `Đánh dấu ĐI HỌC cho ${cells.length} học sinh chưa điểm danh buổi ${sche.code} - B${sche.subject_stt} (${this.fmtDate(sche.class_date)})?`,
          acceptText: 'Xác nhận',
          cancelText: 'Hủy',
          accept: () => {
            this.$vs.loading()
            const jobs = cells.map(c => {
              c.attendance_status = '1'
              return axios.p(`/api/lms/attendances/save`, this.payload(c))
                .then(() => { c.prev_status = '1'; return true })
                .catch(() => { c.attendance_status = c.prev_status; return false })
            })
            Promise.all(jobs).then(results => {
              this.$vs.loading.close()
              const fail = results.filter(r => !r).length
              this.$vs.notify({
                title: fail ? 'Hoàn tất (có lỗi)' : 'Thành Công',
                text: fail ? `Lưu thành công ${results.length - fail}, lỗi ${fail} học sinh` : `Đã điểm danh ${results.length} học sinh`,
                color: fail ? 'warning' : 'success',
                iconPack: 'feather',
                icon: fail ? 'icon-alert-triangle' : 'icon-check'
              })
            })
          }
        })
      },
    },
  }
</script>

<style>
/* ---------- Layout ---------- */
.att-page { font-size: 14px; color: #334155; display: flex; flex-direction: column; gap: 16px; }
.att-body { display: flex; flex-direction: column; gap: 16px; }
.att-card { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(15, 23, 42, 0.06); padding: 18px 20px; }
.att-filter { display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end; }
.att-filter .att-field { width: 240px; margin-bottom: 0; }
.att-filter .att-field.grow { flex: 1; min-width: 260px; }
.att-field { margin-bottom: 14px; }
.att-field > label { display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; }
@media (max-width: 768px) { .att-filter .att-field { width: 100%; } }

/* ---------- Inputs ---------- */
.att-page .v-select .vs__dropdown-toggle {
  min-height: 40px; background: #fff; border: 1.5px solid #cbd5e1; border-radius: 8px; cursor: pointer;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); transition: border-color .15s, box-shadow .15s;
}
.att-page .v-select .vs__dropdown-toggle:hover, .att-page .v-select.vs--open .vs__dropdown-toggle {
  border-color: #7367f0; box-shadow: 0 0 0 3px rgba(115, 103, 240, 0.2);
}
.att-page .v-select.vs--disabled .vs__dropdown-toggle { background: #f1f5f9; border: 1px dashed #cbd5e1; box-shadow: none; cursor: not-allowed; }
.att-page .v-select.vs--disabled .vs__search { cursor: not-allowed; }
.att-page .v-select .vs__search,
.att-page .v-select .vs__search:focus { border: none !important; background: transparent !important; box-shadow: none !important; font-size: 14px; }
.att-page .v-select .vs__selected { font-size: 14px; }

/* Month navigator */
.att-month { display: flex; align-items: center; gap: 6px; }
.att-month__nav { width: 38px; height: 40px; border-radius: 8px; border: 1.5px solid #cbd5e1; background: #fff; color: #475569; cursor: pointer; flex-shrink: 0; font-size: 13px; transition: all .15s; }
.att-month__nav:hover { border-color: #7367f0; color: #7367f0; box-shadow: 0 0 0 3px rgba(115, 103, 240, 0.2); }
.att-month__picker.mx-datepicker { width: 150px !important; min-width: 150px; }
.att-month__picker .mx-input { height: 40px; border-radius: 8px; padding: 0 30px 0 12px !important; font-weight: 600; cursor: pointer; }
.att-month__today { border: none; background: rgba(115, 103, 240, 0.1); color: #5e50ee; font-weight: 600; font-size: 13px; padding: 0 10px; height: 40px; border-radius: 8px; cursor: pointer; white-space: nowrap; }
.att-month__today:hover { background: rgba(115, 103, 240, 0.18); }

/* Search */
.att-search { display: flex; align-items: center; gap: 8px; padding: 0 12px; height: 38px; width: 260px; background: #fff; border: 1.5px solid #cbd5e1; border-radius: 8px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); cursor: text; transition: border-color .15s, box-shadow .15s; }
.att-search:hover, .att-search:focus-within { border-color: #7367f0; box-shadow: 0 0 0 3px rgba(115, 103, 240, 0.2); }
.att-search i { color: #94a3b8; font-size: 14px; }
/* Specificity cao hơn rule global input[type="text"] trong _globalFontFix.scss */
.att-search.att-search input.att-search__input[type="text"],
.att-search.att-search input.att-search__input[type="text"]:not(:disabled):not([readonly]),
.att-search.att-search input.att-search__input[type="text"]:not(:disabled):not([readonly]):hover,
.att-search.att-search input.att-search__input[type="text"]:not(:disabled):not([readonly]):focus {
  flex: 1; min-width: 0; height: 100%; padding: 0 !important; margin: 0;
  border: none !important; outline: none !important; box-shadow: none !important;
  background: transparent !important; background-color: transparent !important; border-radius: 0 !important;
  font-size: 14px; color: #334155 !important;
}

/* ---------- Empty ---------- */
.att-empty { text-align: center; padding: 70px 20px; }
.att-empty__icon { width: 84px; height: 84px; margin: 0 auto 16px; border-radius: 50%; background: rgba(115, 103, 240, 0.1); color: #7367f0; display: flex; align-items: center; justify-content: center; font-size: 36px; }
.att-empty__title { font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 6px; }
.att-empty__desc { font-size: 14px; color: #64748b; }

/* ---------- Hero ---------- */
.att-hero { display: flex; align-items: center; gap: 18px; flex-wrap: wrap; }
.att-hero__avatar { width: 58px; height: 58px; border-radius: 14px; background: linear-gradient(135deg, #7367f0, #9e95f5); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 26px; flex-shrink: 0; }
.att-hero__info { flex: 1; min-width: 260px; }
.att-hero__name { font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 6px; }
.att-hero__meta { display: flex; flex-wrap: wrap; gap: 6px 18px; font-size: 14px; color: #475569; margin-top: 4px; }
.att-hero__meta i { color: #7367f0; margin-right: 6px; }
.att-kpis { display: flex; gap: 10px; flex-wrap: wrap; }
.att-kpi { min-width: 118px; background: #f8fafc; border: 1px solid #eef2f7; border-radius: 10px; padding: 10px 14px; }
.att-kpi__label { font-size: 13px; color: #64748b; font-weight: 600; white-space: nowrap; }
.att-kpi__value { font-size: 24px; font-weight: 800; color: #1e293b; line-height: 1.25; }
.att-kpi__value small { font-size: 15px; color: #94a3b8; font-weight: 600; }
.att-kpi.warn { background: #fff7ed; border-color: #fed7aa; }
.att-kpi.warn .att-kpi__value { color: #ea580c; }
.att-kpi.ok .att-kpi__value { color: #1f9d57; }

/* ---------- Toolbar ---------- */
.att-toolbar { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 14px; }
.att-toolbar__title { font-size: 16px; font-weight: 700; color: #1e293b; display: flex; align-items: center; flex-wrap: wrap; gap: 4px; }
.att-toolbar__right { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; }
.att-legend { display: flex; gap: 12px; flex-wrap: wrap; font-size: 13px; color: #475569; }
.att-legend span { display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
.att-legend i { width: 12px; height: 12px; border-radius: 3px; display: inline-block; border: 1.5px solid; }
.att-legend .st-0 i { background: #fff; border-color: #cbd5e1; }
.att-legend .st-1 i { background: #dcfce7; border-color: #22c55e; }
.att-legend .st-2 i { background: #fee2e2; border-color: #ef4444; }

/* ---------- Pills / badges ---------- */
.att-pill, .att-badge { display: inline-flex !important; align-items: center; white-space: nowrap !important; font-size: 13px; font-weight: 600; border-radius: 99px; line-height: 1; }
.att-pill { padding: 4px 10px; background: #f1f5f9; color: #475569; margin-left: 6px; }
.att-pill.warning { background: rgba(255, 159, 67, 0.16); color: #d97706; }
.att-badge { padding: 5px 10px; }
.att-badge.success { background: rgba(40, 199, 111, 0.14); color: #1f9d57; }
.att-badge.danger { background: rgba(234, 84, 85, 0.12); color: #d63b3c; }
.att-badge.warning { background: rgba(255, 159, 67, 0.16); color: #d97706; }
.att-badge.primary { background: rgba(115, 103, 240, 0.12); color: #5e50ee; }

/* ---------- Attendance grid ---------- */
.att-grid-wrap { overflow: auto; max-height: calc(100vh - 220px); border: 1px solid #e2e8f0; border-radius: 10px; }
.att-grid { border-collapse: separate; border-spacing: 0; width: 100%; font-size: 14px; }
.att-grid th, .att-grid td { border-right: 1px solid #eef2f7; border-bottom: 1px solid #eef2f7; padding: 10px; background: #fff; white-space: nowrap; }
.att-grid thead th { position: sticky; top: 0; z-index: 2; background: #f8fafc; color: #475569; font-weight: 700; font-size: 13px; text-align: center; vertical-align: top; border-bottom: 1px solid #e2e8f0; }
.att-grid .sticky { position: sticky; z-index: 1; }
.att-grid thead .sticky { z-index: 3; vertical-align: middle; }
.att-grid .c-stt { left: 0; width: 56px; min-width: 56px; text-align: center; color: #64748b; }
.att-grid .c-name { left: 56px; min-width: 220px; text-align: left; box-shadow: 4px 0 6px -4px rgba(15, 23, 42, 0.12); }
.att-grid .c-session { min-width: 168px; text-align: center; vertical-align: top; }
.att-grid .c-sum { min-width: 84px; text-align: center; vertical-align: middle; }
.att-grid tbody tr:hover td { background: #fafaff; }
.att-grid thead th.today { background: rgba(115, 103, 240, 0.12); color: #5e50ee; box-shadow: inset 0 3px 0 #7367f0; }
.att-grid thead th.future { color: #94a3b8; }
.att-grid tbody td.today { background: rgba(115, 103, 240, 0.04); }
.att-sess__code { font-size: 13px; font-weight: 700; }
.att-sess__date { font-size: 13px; font-weight: 500; margin-top: 2px; }
.att-sess__count { font-size: 13px; font-weight: 600; margin-top: 4px; }
.att-sess__count .ok { color: #1f9d57; }
.att-sess__count .absent { color: #dc2626; margin-left: 2px; }
.att-absent-text { font-size: 13px; color: #dc2626; font-weight: 600; }
.att-kpi.danger { background: #fef2f2; border-color: #fecaca; }
.att-kpi.danger .att-kpi__value { color: #dc2626; }
.att-sess__bulk { margin-top: 6px; border: 1.5px solid #28c76f; background: #fff; color: #1f9d57; border-radius: 6px; font-size: 13px; font-weight: 600; padding: 4px 8px; cursor: pointer; white-space: nowrap; transition: all .15s; }
.att-sess__bulk:hover { background: #28c76f; color: #fff; }
.att-table-empty { text-align: center; color: #94a3b8; padding: 36px 12px !important; font-size: 14px; }
.att-table-empty i { margin-right: 6px; }

.att-student { display: flex; align-items: center; gap: 10px; }
.att-student__avatar { width: 34px; height: 34px; border-radius: 50%; background: rgba(115, 103, 240, 0.12); color: #7367f0; font-weight: 700; font-size: 14px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.att-strong { font-weight: 600; color: #1e293b; }
.att-sub { font-size: 13px; color: #64748b; }
.att-na { color: #cbd5e1; }

.att-cell { position: relative; display: flex; align-items: center; justify-content: center; }
.att-cell__flag { position: absolute; right: -6px; top: -8px; width: 18px; height: 18px; border-radius: 50%; background: #fff; color: #7367f0; font-size: 13px; display: flex; align-items: center; justify-content: center; }
.att-cell__flag.ok { background: #22c55e; color: #fff; font-size: 13px; }

/* Status select – màu theo trạng thái (override select global) */
.att-page select.att-status,
.att-page select.att-status:not(:disabled):not([readonly]) {
  width: 148px; height: 34px; padding: 0 8px !important; border-radius: 8px; font-size: 13.5px !important; font-weight: 600;
  cursor: pointer !important; transition: all .15s;
}
.att-page select.att-status.st-0:not(:disabled) { background-color: #fff !important; border: 1.5px solid #cbd5e1 !important; color: #475569 !important; }
.att-page select.att-status.st-1:not(:disabled) { background-color: #dcfce7 !important; border: 1.5px solid #22c55e !important; color: #15803d !important; }
.att-page select.att-status.st-2:not(:disabled) { background-color: #fee2e2 !important; border: 1.5px solid #ef4444 !important; color: #b91c1c !important; }
.att-page select.att-status:not(:disabled):hover,
.att-page select.att-status:not(:disabled):focus { border-color: #7367f0 !important; box-shadow: 0 0 0 3px rgba(115, 103, 240, 0.2) !important; }
.att-page select.att-status:disabled { cursor: not-allowed !important; opacity: 1; font-weight: 600; }
.att-page select.att-status:disabled { background-color: #f1f5f9 !important; border: 1px dashed #cbd5e1 !important; color: #94a3b8 !important; }



</style>
