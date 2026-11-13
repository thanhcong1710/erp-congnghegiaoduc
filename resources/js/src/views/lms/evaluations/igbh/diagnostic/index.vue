<template>
  <div id="igbh-diagnostic-list" class="ev-page">
    <eval-module-nav />

    <div class="ev-card">
      <div class="ev-hero">
        <div class="ev-hero__main">
          <span class="ev-hero__icon is-blue"><i class="feather icon-target"></i></span>
          <div>
            <h4 class="ev-hero__title">Khảo sát Đầu vào IG.BH (Diagnostic)</h4>
            <p class="ev-hero__sub">Chẩn đoán năng lực kiến thức cơ bản và tư duy toán học để xếp lớp, gán level cho học sinh</p>
          </div>
        </div>
        <div class="ev-hero__actions">
          <button class="ev-btn ev-btn--primary" @click="openCreateModal">
            <i class="feather icon-plus"></i> Khởi tạo khảo sát
          </button>
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
            <input v-model="search" class="ev-input" placeholder="Tên/mã học sinh, người chấm, bài test..." @keyup.enter="onFilterChange" />
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
              <th>Học sinh</th>
              <th>Bài khảo sát</th>
              <th>Lớp học</th>
              <th class="is-center">Kiến thức (40)</th>
              <th class="is-center">Tư duy (60)</th>
              <th class="is-center">Tổng điểm</th>
              <th class="is-center">Level gán</th>
              <th class="is-center">Ngày thi</th>
              <th>Người chấm</th>
              <th class="is-center">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="11" class="ev-state"><div class="ev-spinner"></div>Đang tải dữ liệu...</td>
            </tr>
            <tr v-else-if="results.length === 0">
              <td colspan="11" class="ev-state">
                <i class="feather icon-inbox"></i>
                <div class="ev-state__title">Chưa có kết quả khảo sát nào</div>
                <div class="ev-state__sub">Bấm “Khởi tạo khảo sát” để tạo bài cho học sinh.</div>
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
              <td>{{ item.erp_class_name || '—' }}</td>
              <template v-if="isGraded(item)">
                <td class="is-center"><span class="ev-score">{{ item.subject_total || 0 }}</span></td>
                <td class="is-center"><span class="ev-score">{{ item.thinking_total || 0 }}</span></td>
                <td class="is-center">
                  <span class="ev-score is-primary">{{ item.total_score || 0 }}<small>/100</small></span>
                  <div class="ev-bar" style="margin: 5px auto 0;"><span :style="{ width: Math.min(100, item.total_score || 0) + '%' }"></span></div>
                </td>
              </template>
              <td v-else colspan="3" class="is-center"><span class="ev-badge ev-badge--warning">Chưa chấm điểm</span></td>
              <td class="is-center">
                <span v-if="item.assigned_level" class="ev-badge ev-badge--primary no-dot">{{ item.assigned_level }}</span>
                <span v-else class="ev-muted">—</span>
              </td>
              <td class="is-center">{{ fmtDate(item.eval_dt) }}</td>
              <td>{{ item.reg_name || '—' }}</td>
              <td class="is-center">
                <div class="ev-actions">
                  <router-link v-if="isGraded(item)" :to="`/lms/evaluations/igbh/diagnostic/report/${item.id}`" class="ev-act-text ev-act-text--success">
                    <i class="feather icon-file-text"></i> Báo cáo
                  </router-link>
                  <router-link v-else :to="`/lms/evaluations/igbh/diagnostic/form/${item.id}`" class="ev-act-text ev-act-text--primary">
                    <i class="feather icon-edit-3"></i> Chấm điểm
                  </router-link>
                  <router-link v-if="isGraded(item)" :to="{ path: `/lms/evaluations/igbh/diagnostic/report/${item.id}`, query: { print: 1 } }" class="ev-act ev-act--info" title="In báo cáo / Lưu PDF">
                    <i class="feather icon-printer"></i>
                  </router-link>
                  <router-link v-if="isGraded(item)" :to="`/lms/evaluations/igbh/diagnostic/form/${item.id}`" class="ev-act ev-act--primary" title="Sửa điểm">
                    <i class="feather icon-edit-2"></i>
                  </router-link>
                  <button class="ev-act ev-act--danger" title="Xóa kết quả" @click="confirmDelete(item)">
                    <i class="feather icon-trash-2"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <eval-table-foot
        :page="pagination.current_page" :per-page="pagination.per_page" :total="pagination.total" unit="bài khảo sát"
        @page="goPage" @per-page="setPerPage" />
    </div>

    <vs-popup class="ev-modal ev-page" title="Khởi tạo khảo sát đầu vào IG.BH" :active.sync="showCreateModal">
      <div class="ev-form-grid">
        <div>
          <label class="ev-field__label">Trung tâm <span class="ev-req">*</span></label>
          <multiselect v-model="createForm.branch" :options="branches" label="name" track-by="id" placeholder="Chọn trung tâm" selectLabel="" />
        </div>
        <div>
          <label class="ev-field__label">Lớp học</label>
          <multiselect v-model="createForm.class" :options="modalClasses" label="cls_name" track-by="id"
            placeholder="Chọn lớp (tùy chọn)" selectLabel="" @input="onModalClassChange" />
        </div>
        <div class="is-full">
          <label class="ev-field__label">Học sinh <span class="ev-req">*</span></label>
          <multiselect v-model="createForm.student" :options="modalFilteredStudents" label="display_name" track-by="student_id"
            placeholder="Gõ tên hoặc mã học sinh" selectLabel="" />
        </div>
        <div class="is-full">
          <label class="ev-field__label">Bài khảo sát <span class="ev-req">*</span></label>
          <multiselect v-model="createForm.test" :options="tests" label="test_nm" track-by="test_seq" placeholder="Chọn bài khảo sát" selectLabel="" />
        </div>
        <div>
          <label class="ev-field__label">Giáo viên / Người chấm</label>
          <multiselect v-model="createForm.teacher" :options="teachers" label="name" track-by="id" placeholder="Chọn giáo viên" selectLabel="" />
        </div>
        <div>
          <label class="ev-field__label">Ngày khảo sát</label>
          <input v-model="createForm.eval_dt" type="date" class="ev-input" />
        </div>
      </div>
      <div class="ev-modal__foot">
        <button class="ev-btn ev-btn--ghost" @click="showCreateModal = false">Hủy</button>
        <button class="ev-btn ev-btn--primary" :disabled="submitting" @click="submitCreate">
          <i class="feather icon-check"></i> Tạo & chấm điểm
        </button>
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
      branches: [],
      classes: [],
      teachers: [],
      tests: [],
      contracts: [],
      selectedBranch: null,
      selectedClass: null,
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
    modalClasses() {
      if (!this.createForm.branch) return this.classes
      return this.classes.filter(c => c.branch_id === this.createForm.branch.id)
    },
    modalFilteredStudents() {
      let list = this.contracts
      if (this.createForm.class) list = list.filter(c => c.class_id === this.createForm.class.id)
      const seen = {}
      return list.filter(c => (seen[c.student_id] ? false : (seen[c.student_id] = true))).map(c => ({
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
    isGraded(item) {
      return (item.total_score || 0) > 0 || !!item.assigned_level
    },
    loadInitData() {
      axios.g('/api/lms/evaluations/igbh/init-data').then(res => {
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
    fetchData() {
      this.loading = true
      const params = {
        search: this.search,
        page: this.pagination.current_page,
        per_page: this.pagination.per_page,
        branch_id: this.selectedBranch ? this.selectedBranch.id : null,
        class_id: this.selectedClass ? this.selectedClass.id : null
      }
      axios.g('/api/lms/evaluations/igbh/results', params).then(res => {
        this.loading = false
        if (res && res.data && res.data.data) {
          this.results = res.data.data
          if (res.data.pagination) {
            this.pagination.total = res.data.pagination.total
            this.pagination.current_page = res.data.pagination.current_page
          }
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
        this.$vs.notify({ title: 'Thiếu thông tin', text: 'Vui lòng chọn bài khảo sát', color: 'danger' })
        return
      }
      const payload = {
        test_seq: this.createForm.test.test_seq,
        student_id: this.createForm.student.student_id,
        contract_id: this.createForm.student.contract_id,
        class_id: this.createForm.class ? this.createForm.class.id : this.createForm.student.class_id,
        branch_id: this.createForm.branch ? this.createForm.branch.id : null,
        teacher_id: this.createForm.teacher ? this.createForm.teacher.id : null,
        eval_dt: this.createForm.eval_dt
      }
      this.submitting = true
      axios.p('/api/lms/evaluations/igbh/results', payload).then(res => {
        this.submitting = false
        if (res && res.data && res.data.status === 'success') {
          this.$vs.notify({ title: 'Thành công', text: 'Đã khởi tạo bài khảo sát – chuyển sang chấm điểm', color: 'success' })
          this.showCreateModal = false
          this.$router.push(`/lms/evaluations/igbh/diagnostic/form/${res.data.data.id}`)
        }
      }).catch(err => {
        this.submitting = false
        this.$vs.notify({ title: 'Lỗi', text: err.message || 'Không thể khởi tạo', color: 'danger' })
      })
    },
    confirmDelete(item) {
      this.$vs.dialog({
        type: 'confirm',
        color: 'danger',
        title: 'Xác nhận xóa',
        text: `Xóa kết quả khảo sát của học sinh ${item.stu_nm}? Thao tác không thể hoàn tác.`,
        acceptText: 'Xóa',
        cancelText: 'Hủy',
        accept: () => {
          axios.del(`/api/lms/evaluations/igbh/results/${item.id}`).then(() => {
            this.$vs.notify({ title: 'Đã xóa', text: 'Xóa khảo sát thành công', color: 'success' })
            this.fetchData()
          })
        }
      })
    }
  }
}
</script>
