<template>
  <div id="ucrea-eval-list" class="ev-page">
    <eval-module-nav />

    <div class="ev-card">
      <div class="ev-hero">
        <div class="ev-hero__main">
          <span class="ev-hero__icon"><i class="feather icon-star"></i></span>
          <div>
            <h4 class="ev-hero__title">Đánh giá Năng lực UCREA</h4>
            <p class="ev-hero__sub">Đánh giá tư duy cơ bản, toán học, logic và sáng tạo theo Rubric · Đầu kỳ / Giữa kỳ / Cuối kỳ</p>
          </div>
        </div>
        <div class="ev-hero__actions">
          <button class="ev-btn ev-btn--primary" @click="openCreateModal">
            <i class="feather icon-plus"></i> Khởi tạo bài đánh giá
          </button>
        </div>
      </div>

      <div class="ev-kpis">
        <div class="ev-kpi is-clickable" :class="{ 'is-active': activeTab === 'completed' }" @click="changeTab('completed')">
          <span class="ev-kpi__icon tone-success"><i class="feather icon-check-circle"></i></span>
          <div>
            <div class="ev-kpi__val">{{ counts.completed }}</div>
            <div class="ev-kpi__label">Bài đã chấm</div>
          </div>
        </div>
        <div class="ev-kpi is-clickable" :class="{ 'is-active': activeTab === 'pending' }" @click="changeTab('pending')">
          <span class="ev-kpi__icon tone-warning"><i class="feather icon-clock"></i></span>
          <div>
            <div class="ev-kpi__val">{{ counts.pending }}</div>
            <div class="ev-kpi__label">Bài chờ chấm</div>
          </div>
        </div>
        <div class="ev-kpi">
          <span class="ev-kpi__icon tone-primary"><i class="feather icon-layers"></i></span>
          <div>
            <div class="ev-kpi__val">{{ counts.completed + counts.pending }}</div>
            <div class="ev-kpi__label">Tổng số bài đánh giá</div>
          </div>
        </div>
      </div>

      <div class="ev-filter">
        <div>
          <label class="ev-field__label">Trung tâm</label>
          <multiselect v-model="selectedBranch" :options="branches" label="name" track-by="id"
            placeholder="Tất cả trung tâm" selectLabel="" deselectLabel="Bỏ chọn" @input="onFilterChange" />
        </div>
        <div>
          <label class="ev-field__label">Lớp học</label>
          <multiselect v-model="selectedClass" :options="filteredClasses" label="cls_name" track-by="id"
            placeholder="Tất cả lớp" selectLabel="" deselectLabel="Bỏ chọn" @input="onFilterChange" />
        </div>
        <div>
          <label class="ev-field__label">Tìm kiếm</label>
          <div class="ev-search">
            <i class="feather icon-search"></i>
            <input v-model="search" class="ev-input" placeholder="Tên/mã học sinh, giáo viên, bài test..." @keyup.enter="onFilterChange" />
          </div>
        </div>
        <div class="ev-filter__actions">
          <button class="ev-btn ev-btn--dark" @click="onFilterChange"><i class="feather icon-search"></i> Tìm</button>
          <button class="ev-btn ev-btn--ghost" @click="resetFilters"><i class="feather icon-rotate-ccw"></i> Đặt lại</button>
        </div>
      </div>

      <div style="padding: 0 22px 14px;">
        <div class="ev-tabs">
          <button class="ev-tab" :class="{ 'is-active': activeTab === 'completed' }" @click="changeTab('completed')">
            Đã chấm <span class="ev-tab__count is-success">{{ counts.completed }}</span>
          </button>
          <button class="ev-tab" :class="{ 'is-active': activeTab === 'pending' }" @click="changeTab('pending')">
            Chờ chấm <span class="ev-tab__count is-warning">{{ counts.pending }}</span>
          </button>
        </div>
      </div>

      <div class="ev-table-wrap">
        <table class="ev-table">
          <thead>
            <tr>
              <th class="col-stt">#</th>
              <th>Học sinh</th>
              <th>Bài kiểm tra</th>
              <th class="is-center">Kỳ</th>
              <th class="is-center">Level</th>
              <th>Lớp học</th>
              <th>Giáo viên</th>
              <th class="is-center">Ngày kiểm tra</th>
              <th class="is-center">Trạng thái</th>
              <th class="is-center">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="10" class="ev-state"><div class="ev-spinner"></div>Đang tải dữ liệu...</td>
            </tr>
            <tr v-else-if="results.length === 0">
              <td colspan="10" class="ev-state">
                <i class="feather icon-inbox"></i>
                <div class="ev-state__title">{{ activeTab === 'pending' ? 'Không có bài nào đang chờ chấm' : 'Chưa có bài đánh giá nào' }}</div>
                <div class="ev-state__sub">Bấm “Khởi tạo bài đánh giá” để tạo bài cho học sinh.</div>
              </td>
            </tr>
            <tr v-for="(item, index) in results" v-else :key="item.id">
              <td class="col-stt">{{ (pagination.current_page - 1) * pagination.per_page + index + 1 }}</td>
              <td>
                <div class="ev-person">
                  <span class="ev-avatar" :style="avatarStyle(item.stu_nm)">{{ initials(item.stu_nm) }}</span>
                  <div>
                    <div class="ev-person__name">{{ item.stu_nm }}</div>
                    <div class="ev-person__meta">{{ item.erp_student_code || '—' }}</div>
                  </div>
                </div>
              </td>
              <td class="ev-strong">{{ item.test_nm || '—' }}</td>
              <td class="is-center"><span class="ev-badge no-dot" :class="periodClass(item.test_cd)">{{ periodName(item.test_cd) }}</span></td>
              <td class="is-center"><span class="ev-badge ev-badge--gray no-dot">{{ item.level_cd }}</span></td>
              <td>{{ item.erp_class_name || item.class_nm || '—' }}</td>
              <td>{{ item.memb_nm || '—' }}</td>
              <td class="is-center">{{ fmtDate(item.eval_dt) }}</td>
              <td class="is-center">
                <span v-if="['IS002','IS003','IS004'].includes(item.result_cd)" class="ev-badge ev-badge--success">Đã chấm</span>
                <span v-else class="ev-badge ev-badge--warning">{{ item.result_cd_nm || 'Chưa nhập' }}</span>
              </td>
              <td class="is-center">
                <div class="ev-actions">
                  <template v-if="activeTab === 'pending'">
                    <router-link :to="`/lms/evaluations/ucrea/form/${item.id}`" class="ev-act-text ev-act-text--primary">
                      <i class="feather icon-edit-3"></i> Chấm điểm
                    </router-link>
                  </template>
                  <template v-else>
                    <router-link :to="`/lms/evaluations/ucrea/report/${item.id}`" class="ev-act-text ev-act-text--success">
                      <i class="feather icon-file-text"></i> Báo cáo
                    </router-link>
                    <router-link :to="`/lms/evaluations/ucrea/form/${item.id}`" class="ev-act ev-act--primary" title="Chấm lại">
                      <i class="feather icon-edit-2"></i>
                    </router-link>
                  </template>
                  <button class="ev-act ev-act--danger" title="Xóa bài đánh giá" @click="confirmDelete(item)">
                    <i class="feather icon-trash-2"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <eval-table-foot
        :page="pagination.current_page" :per-page="pagination.per_page" :total="pagination.total" unit="bài đánh giá"
        @page="goPage" @per-page="setPerPage" />
    </div>

    <!-- Modal khởi tạo -->
    <vs-popup class="ev-modal ev-page" title="Khởi tạo bài đánh giá UCREA" :active.sync="showCreateModal">
      <div class="ev-form-grid">
        <div>
          <label class="ev-field__label">Trung tâm <span class="ev-req">*</span></label>
          <multiselect v-model="createForm.branch" :options="branches" label="name" track-by="id" placeholder="Chọn trung tâm" selectLabel="" />
        </div>
        <div>
          <label class="ev-field__label">Lớp học</label>
          <multiselect v-model="createForm.class" :options="modalFilteredClasses" label="cls_name" track-by="id"
            placeholder="Chọn lớp (tùy chọn)" selectLabel="" @input="onModalClassChange" />
        </div>
        <div class="is-full">
          <label class="ev-field__label">Học sinh <span class="ev-req">*</span></label>
          <multiselect v-model="createForm.student" :options="modalFilteredStudents" label="display_name" track-by="student_id"
            placeholder="Gõ tên hoặc mã học sinh" selectLabel="" />
          <div class="ev-hint">Danh sách lọc theo lớp đã chọn ({{ modalFilteredStudents.length }} học sinh).</div>
        </div>
        <div class="is-full">
          <label class="ev-field__label">Bài kiểm tra <span class="ev-req">*</span></label>
          <multiselect v-model="createForm.test" :options="tests" :custom-label="testLabel" track-by="id" placeholder="Chọn bài kiểm tra" selectLabel="" />
        </div>
        <div>
          <label class="ev-field__label">Giáo viên chấm</label>
          <multiselect v-model="createForm.teacher" :options="teachers" label="name" track-by="id" placeholder="Chọn giáo viên" selectLabel="" />
        </div>
        <div>
          <label class="ev-field__label">Ngày kiểm tra</label>
          <input v-model="createForm.eval_dt" type="date" class="ev-input" />
        </div>
      </div>
      <div class="ev-modal__foot">
        <button class="ev-btn ev-btn--ghost" @click="showCreateModal = false">Hủy</button>
        <button class="ev-btn ev-btn--primary" :disabled="submitting" @click="submitCreate">
          <i class="feather icon-check"></i> Tạo bài đánh giá
        </button>
      </div>
    </vs-popup>
  </div>
</template>

<script>
import axios from '@/http/axios.js'
import multiselect from 'vue-multiselect'
import '../_shared/eval-ui.css'
import EvalModuleNav from '../_shared/EvalModuleNav.vue'
import EvalTableFoot from '../_shared/EvalTableFoot.vue'
import { initials, avatarStyle, fmtDate, todayISO } from '../_shared/helpers'

const PERIODS = {
  TT001: { name: 'Đầu kỳ', cls: 'ev-badge--info' },
  TT002: { name: 'Giữa kỳ', cls: 'ev-badge--primary' },
  TT003: { name: 'Cuối kỳ', cls: 'ev-badge--success' }
}

export default {
  components: { multiselect, EvalModuleNav, EvalTableFoot },
  data() {
    return {
      activeTab: 'completed',
      search: '',
      loading: false,
      submitting: false,
      results: [],
      branches: [],
      classes: [],
      teachers: [],
      tests: [],
      contracts: [],
      selectedBranch: null,
      selectedClass: null,
      counts: { completed: 0, pending: 0 },
      pagination: { current_page: 1, per_page: 20, total: 0 },
      showCreateModal: false,
      createForm: { branch: null, class: null, student: null, test: null, teacher: null, eval_dt: todayISO() }
    }
  },
  computed: {
    filteredClasses() {
      if (!this.selectedBranch) return this.classes
      return this.classes.filter(c => c.branch_id === this.selectedBranch.id)
    },
    modalFilteredClasses() {
      if (!this.createForm.branch) return this.classes
      return this.classes.filter(c => c.branch_id === this.createForm.branch.id)
    },
    modalFilteredStudents() {
      let list = this.contracts
      if (this.createForm.class) list = list.filter(c => c.class_id === this.createForm.class.id)
      return list.map(c => ({
        student_id: c.student_id,
        contract_id: c.contract_id,
        class_id: c.class_id,
        display_name: `${c.student_name} (${c.student_lms_id || c.student_id})`
      }))
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
    periodName(cd) { return (PERIODS[cd] || {}).name || cd || '—' },
    periodClass(cd) { return (PERIODS[cd] || {}).cls || 'ev-badge--gray' },
    testLabel(t) { return `${t.test_nm} · ${this.periodName(t.test_cd)} · ${t.level_cd_nm || t.level_cd}` },
    baseParams() {
      return {
        search: this.search,
        branch_id: this.selectedBranch ? this.selectedBranch.id : null,
        class_id: this.selectedClass ? this.selectedClass.id : null
      }
    },
    loadInitData() {
      axios.g('/api/lms/evaluations/ucrea/init-data').then(res => {
        if (res && res.data && res.data.data) {
          const d = res.data.data
          this.branches = d.branches || []
          this.classes = d.classes || []
          this.teachers = d.teachers || []
          this.tests = d.tests || []
          this.contracts = d.contracts || []
        }
      })
    },
    fetchCounts() {
      ['completed', 'pending'].forEach(status => {
        if (status === this.activeTab) return
        axios.g('/api/lms/evaluations/ucrea/results', { ...this.baseParams(), status, per_page: 1, page: 1 }).then(res => {
          if (res && res.data && res.data.pagination) this.counts[status] = res.data.pagination.total
        })
      })
    },
    fetchData() {
      this.loading = true
      const params = {
        ...this.baseParams(),
        status: this.activeTab,
        page: this.pagination.current_page,
        per_page: this.pagination.per_page
      }
      axios.g('/api/lms/evaluations/ucrea/results', params).then(res => {
        this.loading = false
        if (res && res.data && res.data.data) {
          this.results = res.data.data
          if (res.data.pagination) {
            this.pagination.total = res.data.pagination.total
            this.pagination.current_page = res.data.pagination.current_page
            this.counts[this.activeTab] = res.data.pagination.total
          }
        }
      }).catch(() => { this.loading = false })
      this.fetchCounts()
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
    changeTab(tab) {
      if (this.activeTab === tab) return
      this.activeTab = tab
      this.onFilterChange()
    },
    resetFilters() {
      this.search = ''
      this.selectedBranch = null
      this.selectedClass = null
      this.onFilterChange()
    },
    openCreateModal() {
      this.createForm = {
        branch: this.selectedBranch || this.branches[0] || null,
        class: this.selectedClass || null,
        student: null,
        test: null,
        teacher: null,
        eval_dt: todayISO()
      }
      if (this.createForm.class) this.onModalClassChange(this.createForm.class)
      this.showCreateModal = true
    },
    onModalClassChange(cls) {
      if (cls && cls.teacher_id) {
        const found = this.teachers.find(t => t.id === cls.teacher_id)
        if (found) this.createForm.teacher = found
      }
    },
    submitCreate() {
      if (!this.createForm.student) {
        this.$vs.notify({ title: 'Thiếu thông tin', text: 'Vui lòng chọn học sinh', color: 'danger' })
        return
      }
      if (!this.createForm.test) {
        this.$vs.notify({ title: 'Thiếu thông tin', text: 'Vui lòng chọn bài kiểm tra', color: 'danger' })
        return
      }
      const payload = {
        test_id: this.createForm.test.id,
        student_id: this.createForm.student.student_id,
        contract_id: this.createForm.student.contract_id,
        class_id: this.createForm.class ? this.createForm.class.id : this.createForm.student.class_id,
        branch_id: this.createForm.branch ? this.createForm.branch.id : null,
        teacher_id: this.createForm.teacher ? this.createForm.teacher.id : null,
        eval_dt: this.createForm.eval_dt
      }
      this.submitting = true
      axios.p('/api/lms/evaluations/ucrea/results', payload).then(res => {
        this.submitting = false
        if (res && res.data && res.data.status === 'success') {
          this.$vs.notify({ title: 'Thành công', text: 'Đã khởi tạo bài đánh giá – chuyển sang chấm điểm', color: 'success' })
          this.showCreateModal = false
          this.$router.push(`/lms/evaluations/ucrea/form/${res.data.data.id}`)
        }
      }).catch(err => {
        this.submitting = false
        this.$vs.notify({ title: 'Lỗi', text: err.message || 'Không thể tạo bài đánh giá', color: 'danger' })
      })
    },
    confirmDelete(item) {
      this.$vs.dialog({
        type: 'confirm',
        color: 'danger',
        title: 'Xác nhận xóa',
        text: `Xóa bài đánh giá "${item.test_nm}" của học sinh ${item.stu_nm}? Thao tác không thể hoàn tác.`,
        acceptText: 'Xóa',
        cancelText: 'Hủy',
        accept: () => {
          axios.del(`/api/lms/evaluations/ucrea/results/${item.id}`).then(() => {
            this.$vs.notify({ title: 'Đã xóa', text: 'Xóa bài đánh giá thành công', color: 'success' })
            this.fetchData()
          })
        }
      })
    }
  }
}
</script>
