<template>
  <div id="igbh-class-summative-list" class="ev-page">
    <eval-module-nav />

    <div class="ev-card">
      <div class="ev-hero">
        <div class="ev-hero__main">
          <span class="ev-hero__icon is-orange"><i class="feather icon-award"></i></span>
          <div>
            <h4 class="ev-hero__title">Đánh giá Tổng kết Lớp IG.BH (Summative)</h4>
            <p class="ev-hero__sub">Khởi tạo đợt tổng kết cuối kỳ cho lớp – tổng hợp từ 12 tuần đánh giá và bài kiểm tra tự luận</p>
          </div>
        </div>
        <div class="ev-hero__actions">
          <router-link to="/lms/evaluations/igbh/summative/students" class="ev-btn ev-btn--ghost">
            <i class="feather icon-users"></i> Xem theo học sinh
          </router-link>
          <button class="ev-btn ev-btn--primary" @click="openCreateModal">
            <i class="feather icon-plus"></i> Khởi tạo tổng kết lớp
          </button>
        </div>
      </div>

      <div class="ev-filter">
        <div>
          <label class="ev-field__label">Lớp học</label>
          <multiselect v-model="selectedClass" :options="classes" label="cls_name" track-by="id"
            placeholder="Tất cả lớp" selectLabel="" deselectLabel="Bỏ chọn" @input="onFilterChange" />
        </div>
        <div>
          <label class="ev-field__label">Bài kiểm tra</label>
          <multiselect v-model="selectedTest" :options="tests" label="test_nm" track-by="test_seq"
            placeholder="Tất cả bài kiểm tra" selectLabel="" deselectLabel="Bỏ chọn" @input="onFilterChange" />
        </div>
        <div>
          <label class="ev-field__label">Tìm kiếm</label>
          <div class="ev-search">
            <i class="feather icon-search"></i>
            <input v-model="search" class="ev-input" placeholder="Tên lớp, giáo viên, bài test..." @keyup.enter="onFilterChange" />
          </div>
        </div>
        <div class="ev-filter__actions">
          <button class="ev-btn ev-btn--dark" @click="onFilterChange"><i class="feather icon-search"></i> Tìm</button>
          <button class="ev-btn ev-btn--ghost" @click="resetFilters"><i class="feather icon-rotate-ccw"></i> Đặt lại</button>
        </div>
      </div>

      <div class="ev-table-wrap">
        <table class="ev-table">
          <thead>
            <tr>
              <th class="col-stt">#</th>
              <th class="col-wide">Lớp học</th>
              <th>Bài kiểm tra</th>
              <th>Giáo viên phụ trách</th>
              <th class="is-center">Ngày đánh giá</th>
              <th class="is-center">Trạng thái</th>
              <th class="is-center">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="7" class="ev-state"><div class="ev-spinner"></div>Đang tải dữ liệu...</td>
            </tr>
            <tr v-else-if="results.length === 0">
              <td colspan="7" class="ev-state">
                <i class="feather icon-inbox"></i>
                <div class="ev-state__title">Chưa có đợt đánh giá tổng kết nào</div>
                <div class="ev-state__sub">Hoàn thành đánh giá tuần cho lớp, sau đó bấm “Khởi tạo tổng kết lớp”.</div>
              </td>
            </tr>
            <tr v-for="(item, index) in results" v-else :key="item.id">
              <td class="col-stt">{{ (pagination.from || 1) + index }}</td>
              <td class="ev-strong">{{ item.erp_class_name || item.class_nm }}</td>
              <td>{{ item.test_nm || '—' }}</td>
              <td>{{ item.teacher_nm || '—' }}</td>
              <td class="is-center">{{ fmtDate(item.eval_ymd) }}</td>
              <td class="is-center">
                <span v-if="item.status === 'completed'" class="ev-badge ev-badge--success">Đã chốt</span>
                <span v-else class="ev-badge ev-badge--warning">Bản nháp</span>
              </td>
              <td class="is-center">
                <div class="ev-actions">
                  <button class="ev-act-text ev-act-text--primary" @click="viewClassStudents(item)">
                    <i class="feather icon-list"></i> Danh sách học sinh
                  </button>
                  <button class="ev-act ev-act--danger" title="Xóa đợt đánh giá" @click="confirmDelete(item)">
                    <i class="feather icon-trash-2"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <eval-table-foot
        :page="pagination.current_page" :per-page="pagination.per_page" :total="pagination.total" unit="đợt tổng kết"
        @page="goPage" @per-page="setPerPage" />
    </div>

    <!-- Modal khởi tạo -->
    <vs-popup class="ev-modal ev-page" title="Khởi tạo đánh giá tổng kết cho lớp" :active.sync="showCreateModal">
      <div class="ev-form-grid">
        <div class="is-full">
          <label class="ev-field__label">Bài kiểm tra Summative <span class="ev-req">*</span></label>
          <multiselect v-model="createForm.test" :options="tests" label="test_nm" track-by="test_seq"
            placeholder="Chọn bài kiểm tra" selectLabel="" @input="checkWeeks" />
        </div>
        <div>
          <label class="ev-field__label">Lớp học <span class="ev-req">*</span></label>
          <multiselect v-model="createForm.class" :options="classes" label="cls_name" track-by="id"
            placeholder="Chọn lớp học" selectLabel="" @input="checkWeeks" />
        </div>
        <div>
          <label class="ev-field__label">Ngày đánh giá</label>
          <input v-model="createForm.eval_ymd" type="date" class="ev-input" />
        </div>
        <div v-if="weekCheck !== null" class="is-full">
          <div class="ev-weekcheck" :class="weekCheck >= 12 ? 'is-ok' : 'is-warn'">
            <i class="feather" :class="weekCheck >= 12 ? 'icon-check-circle' : 'icon-alert-triangle'"></i>
            <div>
              <strong>Đánh giá tuần: {{ weekCheck }}/12 tuần</strong>
              <div v-if="weekCheck >= 12">Lớp đã có đủ dữ liệu đánh giá tuần – báo cáo tổng kết sẽ hiển thị đầy đủ.</div>
              <div v-else>Lớp chưa chấm đủ 12 tuần. Báo cáo tổng kết sẽ thiếu dữ liệu thái độ/năng lực của các tuần còn trống.</div>
            </div>
          </div>
        </div>
      </div>
      <div class="ev-modal__foot">
        <button class="ev-btn ev-btn--ghost" @click="showCreateModal = false">Hủy</button>
        <button class="ev-btn ev-btn--primary" :disabled="submitting" @click="submitCreate">
          <i class="feather icon-check"></i> Khởi tạo
        </button>
      </div>
    </vs-popup>

    <!-- Modal danh sách học sinh -->
    <vs-popup class="ev-modal ev-modal--wide ev-page" title="Học sinh trong đợt tổng kết" :active.sync="showClassStudentsModal">
      <div v-if="selectedClassEval">
        <div class="ev-classbar">
          <div>
            <div class="ev-strong" style="font-size: 16px;">{{ selectedClassEval.class_nm }}</div>
            <div class="ev-muted" style="font-size: 13.5px;">{{ selectedClassEval.test_nm }} · {{ fmtDate(selectedClassEval.eval_ymd) }}</div>
          </div>
          <div class="ev-classbar__stats">
            <div><span>{{ classStudents.length }}</span>Học sinh</div>
            <div><span>{{ gradedCount }}</span>Đã có điểm</div>
            <div><span>{{ avgScore }}</span>Điểm TB</div>
          </div>
          <button class="ev-btn ev-btn--sm" :class="selectedClassEval.status === 'completed' ? 'ev-btn--warning' : 'ev-btn--success'" @click="toggleClassStatus">
            <i class="feather" :class="selectedClassEval.status === 'completed' ? 'icon-unlock' : 'icon-lock'"></i>
            {{ selectedClassEval.status === 'completed' ? 'Mở lại (bản nháp)' : 'Chốt hoàn thành' }}
          </button>
        </div>

        <div class="ev-table-wrap" style="border: 1px solid #e2e8f0; border-radius: 10px;">
          <table class="ev-table">
            <thead>
              <tr>
                <th class="col-stt">#</th>
                <th>Học sinh</th>
                <th class="is-center">Điểm tổng kết</th>
                <th class="is-center">Trạng thái</th>
                <th class="is-center">Thao tác</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="classStudents.length === 0">
                <td colspan="5" class="ev-state">Lớp chưa có học sinh nào trong đợt tổng kết</td>
              </tr>
              <tr v-for="(stu, idx) in classStudents" :key="stu.id">
                <td class="col-stt">{{ idx + 1 }}</td>
                <td>
                  <div class="ev-person">
                    <span class="ev-avatar" :style="avatarStyle(stu.stu_nm)">{{ initials(stu.stu_nm) }}</span>
                    <div>
                      <div class="ev-person__name">{{ stu.stu_nm }}</div>
                      <div class="ev-person__meta">{{ stu.stu_seq }}</div>
                    </div>
                  </div>
                </td>
                <td class="is-center">
                  <span v-if="stu.total_score > 0" class="ev-score is-primary">{{ stu.total_score }}</span>
                  <span v-else class="ev-badge ev-badge--warning">Chưa chấm</span>
                </td>
                <td class="is-center">
                  <span v-if="stu.status === 'completed'" class="ev-badge ev-badge--success">Đã chốt</span>
                  <span v-else class="ev-badge ev-badge--gray">Bản nháp</span>
                </td>
                <td class="is-center">
                  <div class="ev-actions">
                    <router-link :to="`/lms/evaluations/igbh/summative/form/${stu.id}`" class="ev-act-text ev-act-text--primary">
                      <i class="feather icon-edit-3"></i> Chấm
                    </router-link>
                    <router-link :to="`/lms/evaluations/igbh/summative/report/${stu.id}`" class="ev-act-text ev-act-text--success">
                      <i class="feather icon-file-text"></i> Báo cáo
                    </router-link>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </vs-popup>
  </div>
</template>

<script>
import axios from '@/http/axios.js'
import multiselect from 'vue-multiselect'
import '../../_shared/eval-ui.css'
import EvalModuleNav from '../../_shared/EvalModuleNav.vue'
import EvalTableFoot from '../../_shared/EvalTableFoot.vue'
import { initials, avatarStyle, fmtDate, todayISO } from '../../_shared/helpers'

export default {
  components: { multiselect, EvalModuleNav, EvalTableFoot },
  data() {
    return {
      search: '',
      loading: false,
      submitting: false,
      results: [],
      classes: [],
      tests: [],
      selectedClass: null,
      selectedTest: null,
      pagination: { current_page: 1, per_page: 20, total: 0, from: 1 },
      showCreateModal: false,
      createForm: { test: null, class: null, eval_ymd: todayISO() },
      weekCheck: null,
      showClassStudentsModal: false,
      selectedClassEval: null,
      classStudents: []
    }
  },
  computed: {
    gradedCount() {
      return this.classStudents.filter(s => Number(s.total_score) > 0).length
    },
    avgScore() {
      const g = this.classStudents.filter(s => Number(s.total_score) > 0)
      if (!g.length) return '—'
      return (g.reduce((a, s) => a + Number(s.total_score), 0) / g.length).toFixed(1)
    }
  },
  created() {
    this.loadInitData()
    this.fetchData()
  },
  methods: {
    initials,
    avatarStyle,
    fmtDate,
    loadInitData() {
      axios.g('/api/lms/evaluations/summative/classes/init-data').then(res => {
        if (res && res.data) {
          this.classes = res.data.classes || []
          this.tests = res.data.tests || []
        }
      })
    },
    fetchData() {
      this.loading = true
      const params = {
        search: this.search,
        page: this.pagination.current_page,
        per_page: this.pagination.per_page,
        class_id: this.selectedClass ? this.selectedClass.id : null,
        test_seq: this.selectedTest ? this.selectedTest.test_seq : null
      }
      axios.g('/api/lms/evaluations/summative/classes', params).then(res => {
        this.loading = false
        if (res && res.data) {
          this.results = res.data.data || []
          this.pagination.total = res.data.total || 0
          this.pagination.current_page = res.data.current_page || 1
          this.pagination.from = res.data.from || 1
        }
      }).catch(() => { this.loading = false })
    },
    onFilterChange() {
      this.pagination.current_page = 1
      this.fetchData()
    },
    goPage(p) {
      this.pagination.current_page = p
      this.fetchData()
    },
    setPerPage(n) {
      this.pagination.per_page = n
      this.onFilterChange()
    },
    resetFilters() {
      this.search = ''
      this.selectedClass = null
      this.selectedTest = null
      this.onFilterChange()
    },
    openCreateModal() {
      this.weekCheck = null
      this.createForm = {
        test: this.selectedTest || this.tests[0] || null,
        class: this.selectedClass || null,
        eval_ymd: todayISO()
      }
      this.showCreateModal = true
      this.checkWeeks()
    },
    checkWeeks() {
      this.weekCheck = null
      if (!this.createForm.test || !this.createForm.class) return
      axios.p('/api/lms/evaluations/weekly/existing-weeks', {
        test_seq: this.createForm.test.test_seq,
        class_id: this.createForm.class.id
      }).then(res => {
        this.weekCheck = Array.isArray(res.data) ? res.data.length : 0
      })
    },
    submitCreate() {
      if (!this.createForm.test || !this.createForm.class) {
        this.$vs.notify({ title: 'Thiếu thông tin', text: 'Vui lòng chọn bài kiểm tra và lớp học', color: 'danger' })
        return
      }
      this.submitting = true
      axios.p('/api/lms/evaluations/summative/classes', {
        test_seq: this.createForm.test.test_seq,
        class_id: this.createForm.class.id,
        eval_ymd: this.createForm.eval_ymd
      }).then(res => {
        this.submitting = false
        if (res && res.data && res.data.id) {
          this.$vs.notify({ title: 'Thành công', text: 'Khởi tạo đánh giá lớp thành công', color: 'success' })
          this.showCreateModal = false
          this.fetchData()
          this.viewClassStudents({ id: res.data.id })
        }
      }).catch(err => {
        this.submitting = false
        const msg = err.response && err.response.data && err.response.data.message ? err.response.data.message : err.message
        this.$vs.notify({ title: 'Lỗi', text: msg || 'Không thể khởi tạo', color: 'danger' })
      })
    },
    viewClassStudents(item) {
      axios.g(`/api/lms/evaluations/summative/classes/${item.id}`).then(res => {
        if (res && res.data) {
          this.selectedClassEval = res.data.evaluation
          this.classStudents = res.data.results || []
          this.showClassStudentsModal = true
        }
      })
    },
    toggleClassStatus() {
      const newStatus = this.selectedClassEval.status === 'completed' ? 'draft' : 'completed'
      axios.p(`/api/lms/evaluations/summative/classes/${this.selectedClassEval.id}/status`, { status: newStatus }).then(() => {
        this.selectedClassEval.status = newStatus
        this.classStudents.forEach(s => { s.status = newStatus })
        this.$vs.notify({ title: 'Thành công', text: 'Cập nhật trạng thái thành công', color: 'success' })
        this.fetchData()
      })
    },
    confirmDelete(item) {
      this.$vs.dialog({
        type: 'confirm',
        color: 'danger',
        title: 'Xác nhận xóa',
        text: `Xóa đợt đánh giá tổng kết của lớp ${item.class_nm}?`,
        acceptText: 'Xóa',
        cancelText: 'Hủy',
        accept: () => {
          axios.del(`/api/lms/evaluations/summative/classes/${item.id}`).then(() => {
            this.$vs.notify({ title: 'Đã xóa', text: 'Xóa đợt đánh giá thành công', color: 'success' })
            this.fetchData()
          })
        }
      })
    }
  }
}
</script>

<style>
.ev-modal--wide .vs-popup { width: 860px !important; }
.ev-classbar {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 14px;
  padding: 14px 16px;
  margin-bottom: 14px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
}
.ev-classbar__stats { display: flex; gap: 18px; }
.ev-classbar__stats > div { font-size: 13px; color: #64748b; text-align: center; white-space: nowrap; }
.ev-classbar__stats span { display: block; font-size: 18px; font-weight: 800; color: #0f172a; }
.ev-weekcheck {
  display: flex;
  gap: 10px;
  align-items: flex-start;
  padding: 12px 14px;
  border-radius: 10px;
  font-size: 13.5px;
  line-height: 1.5;
}
.ev-weekcheck i { font-size: 18px; margin-top: 1px; }
.ev-weekcheck.is-ok { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
.ev-weekcheck.is-warn { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
</style>
