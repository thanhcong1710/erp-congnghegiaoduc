<template>
  <div id="igbh-weekly-eval-list" class="ev-page">
    <eval-module-nav />

    <div class="ev-card">
      <div class="ev-hero">
        <div class="ev-hero__main">
          <span class="ev-hero__icon is-green"><i class="feather icon-calendar"></i></span>
          <div>
            <h4 class="ev-hero__title">Đánh giá Tuần IG.BH (Weekly)</h4>
            <p class="ev-hero__sub">Chấm sách bài tập, thái độ và năng lực quan sát theo lớp, từ tuần 1 đến tuần 12 – dữ liệu đầu vào cho đánh giá cuối kỳ</p>
          </div>
        </div>
        <div class="ev-hero__actions">
          <button class="ev-btn ev-btn--primary" @click="openCreateModal">
            <i class="feather icon-plus"></i> Chấm tuần mới
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
          <label class="ev-field__label">Bài kiểm tra (Summative)</label>
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
              <th class="is-center">Tuần</th>
              <th>Bài kiểm tra</th>
              <th>Giáo viên</th>
              <th class="is-center">Ngày đánh giá</th>
              <th class="is-center">Trạng thái</th>
              <th class="is-center">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="8" class="ev-state"><div class="ev-spinner"></div>Đang tải dữ liệu...</td>
            </tr>
            <tr v-else-if="results.length === 0">
              <td colspan="8" class="ev-state">
                <i class="feather icon-inbox"></i>
                <div class="ev-state__title">Chưa có đợt đánh giá tuần nào</div>
                <div class="ev-state__sub">Bấm “Chấm tuần mới”, chọn lớp và tuần để bắt đầu.</div>
              </td>
            </tr>
            <tr v-for="(item, index) in results" v-else :key="item.id">
              <td class="col-stt">{{ (pagination.from || 1) + index }}</td>
              <td class="ev-strong">{{ item.erp_class_name || item.class_nm }}</td>
              <td class="is-center"><span class="ev-badge ev-badge--primary no-dot">{{ item.each_cd_nm || item.each_cd }}</span></td>
              <td>{{ item.test_nm || '—' }}</td>
              <td>{{ item.teacher_nm || '—' }}</td>
              <td class="is-center">{{ fmtDate(item.eval_ymd) }}</td>
              <td class="is-center">
                <span v-if="item.status === 'Completed'" class="ev-badge ev-badge--success">Đã hoàn thành</span>
                <span v-else class="ev-badge ev-badge--warning">Đang nhập</span>
              </td>
              <td class="is-center">
                <div class="ev-actions">
                  <router-link :to="`/lms/evaluations/igbh/weekly/form/${item.id}`" class="ev-act-text ev-act-text--primary">
                    <i class="feather icon-edit-3"></i> {{ item.status === 'Completed' ? 'Xem / sửa' : 'Chấm điểm' }}
                  </router-link>
                  <button class="ev-act ev-act--danger" title="Xóa đánh giá tuần" @click="confirmDelete(item)">
                    <i class="feather icon-trash-2"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <eval-table-foot
        :page="pagination.current_page" :per-page="pagination.per_page" :total="pagination.total" unit="đợt đánh giá"
        @page="goPage" @per-page="setPerPage" />
    </div>

    <vs-popup class="ev-modal ev-page" title="Chấm đánh giá tuần cho lớp" :active.sync="showCreateModal">
      <div class="ev-form-grid">
        <div class="is-full">
          <label class="ev-field__label">Bài kiểm tra Summative <span class="ev-req">*</span></label>
          <multiselect v-model="createForm.test" :options="tests" label="test_nm" track-by="test_seq"
            placeholder="Chọn bài kiểm tra" selectLabel="" @input="fetchExistingWeeks" />
        </div>
        <div>
          <label class="ev-field__label">Lớp học <span class="ev-req">*</span></label>
          <multiselect v-model="createForm.class" :options="classes" label="cls_name" track-by="id"
            placeholder="Chọn lớp học" selectLabel="" @input="fetchExistingWeeks" />
        </div>
        <div>
          <label class="ev-field__label">Ngày đánh giá</label>
          <input v-model="createForm.eval_ymd" type="date" class="ev-input" />
        </div>
        <div class="is-full">
          <label class="ev-field__label">Tuần đánh giá <span class="ev-req">*</span></label>
          <div class="ev-weeks">
            <button
              v-for="w in weeks"
              :key="w.each_cd"
              type="button"
              class="ev-week"
              :class="{ 'is-selected': createForm.week && createForm.week.each_cd === w.each_cd, 'is-done': existingWeeks.includes(w.each_cd) }"
              :title="existingWeeks.includes(w.each_cd) ? 'Đã có dữ liệu – mở để sửa' : w.each_cd_nm"
              @click="createForm.week = w"
            >
              T{{ parseInt(w.each_cd.replace('SE', ''), 10) }}
            </button>
          </div>
          <div class="ev-hint">
            Ô màu xanh là tuần đã chấm ({{ existingWeeks.length }}/12) – chọn lại sẽ mở bản đã có để chỉnh sửa.
          </div>
        </div>
      </div>
      <div class="ev-modal__foot">
        <button class="ev-btn ev-btn--ghost" @click="showCreateModal = false">Hủy</button>
        <button class="ev-btn ev-btn--primary" @click="submitCreate">
          <i class="feather icon-arrow-right"></i>
          {{ createForm.week && existingWeeks.includes(createForm.week.each_cd) ? 'Mở tuần đã chấm' : 'Bắt đầu chấm điểm' }}
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
import { fmtDate, todayISO } from '../../_shared/helpers'

export default {
  components: { multiselect, EvalModuleNav, EvalTableFoot },
  data() {
    return {
      search: '',
      loading: false,
      results: [],
      classes: [],
      tests: [],
      weeks: [],
      existingWeeks: [],
      selectedClass: null,
      selectedTest: null,
      pagination: { current_page: 1, per_page: 20, total: 0, from: 1 },
      showCreateModal: false,
      createForm: { test: null, class: null, week: null, eval_ymd: todayISO() }
    }
  },
  created() {
    this.loadInitData()
    this.fetchData()
  },
  methods: {
    fmtDate,
    loadInitData() {
      axios.g('/api/lms/evaluations/weekly/init-data').then(res => {
        if (res && res.data) {
          this.classes = res.data.classes || []
          this.tests = res.data.tests || []
          this.weeks = res.data.weeks || []
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
      axios.g('/api/lms/evaluations/weekly/results', params).then(res => {
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
      this.existingWeeks = []
      this.createForm = {
        test: this.selectedTest || this.tests[0] || null,
        class: this.selectedClass || null,
        week: null,
        eval_ymd: todayISO()
      }
      this.showCreateModal = true
      this.fetchExistingWeeks()
    },
    fetchExistingWeeks() {
      this.existingWeeks = []
      if (!this.createForm.test || !this.createForm.class) return
      axios.p('/api/lms/evaluations/weekly/existing-weeks', {
        test_seq: this.createForm.test.test_seq,
        class_id: this.createForm.class.id
      }).then(res => {
        this.existingWeeks = res.data || []
        if (!this.createForm.week) {
          this.createForm.week = this.weeks.find(w => !this.existingWeeks.includes(w.each_cd)) || null
        }
      })
    },
    submitCreate() {
      if (!this.createForm.test || !this.createForm.class || !this.createForm.week) {
        this.$vs.notify({ title: 'Thiếu thông tin', text: 'Vui lòng chọn bài kiểm tra, lớp học và tuần', color: 'danger' })
        return
      }
      axios.p('/api/lms/evaluations/weekly/results', {
        test_seq: this.createForm.test.test_seq,
        class_id: this.createForm.class.id,
        each_cd: this.createForm.week.each_cd,
        eval_ymd: this.createForm.eval_ymd
      }).then(res => {
        if (res && res.data && res.data.id) {
          this.showCreateModal = false
          this.$router.push(`/lms/evaluations/igbh/weekly/form/${res.data.id}`)
        }
      }).catch(err => {
        this.$vs.notify({ title: 'Lỗi', text: err.message || 'Không thể khởi tạo', color: 'danger' })
      })
    },
    confirmDelete(item) {
      this.$vs.dialog({
        type: 'confirm',
        color: 'danger',
        title: 'Xác nhận xóa',
        text: `Xóa đánh giá ${item.each_cd_nm} của lớp ${item.class_nm}? Điểm của toàn bộ học sinh trong tuần này sẽ bị xóa.`,
        acceptText: 'Xóa',
        cancelText: 'Hủy',
        accept: () => {
          axios.del(`/api/lms/evaluations/weekly/results/${item.id}`).then(() => {
            this.$vs.notify({ title: 'Đã xóa', text: 'Xóa đánh giá tuần thành công', color: 'success' })
            this.fetchData()
          })
        }
      })
    }
  }
}
</script>
