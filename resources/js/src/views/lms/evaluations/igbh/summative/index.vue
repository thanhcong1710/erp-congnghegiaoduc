<template>
  <div id="igbh-summative-list" class="ev-page">
    <eval-module-nav />

    <div class="ev-card">
      <div class="ev-hero">
        <div class="ev-hero__main">
          <span class="ev-hero__icon is-orange"><i class="feather icon-users"></i></span>
          <div>
            <h4 class="ev-hero__title">Tổng kết Học sinh IG.BH (Summative)</h4>
            <p class="ev-hero__sub">Kết quả tổng kết cuối kỳ của từng học sinh – chấm bài tự luận, xem và in báo cáo 12 tuần</p>
          </div>
        </div>
        <div class="ev-hero__actions">
          <router-link to="/lms/evaluations/igbh/summative" class="ev-btn ev-btn--ghost">
            <i class="feather icon-grid"></i> Quản lý theo lớp
          </router-link>
        </div>
      </div>

      <div class="ev-filter">
        <div>
          <label class="ev-field__label">Lớp học</label>
          <multiselect v-model="selectedClass" :options="classes" label="cls_name" track-by="id"
            placeholder="Tất cả lớp" selectLabel="" deselectLabel="Bỏ chọn" @input="onFilterChange" />
        </div>
        <div>
          <label class="ev-field__label">Trạng thái</label>
          <multiselect v-model="selectedStatus" :options="statusOptions" label="label" track-by="value"
            placeholder="Tất cả trạng thái" selectLabel="" deselectLabel="Bỏ chọn" :searchable="false" @input="onFilterChange" />
        </div>
        <div>
          <label class="ev-field__label">Tìm kiếm</label>
          <div class="ev-search">
            <i class="feather icon-search"></i>
            <input v-model="search" class="ev-input" placeholder="Tên/mã học sinh, lớp, bài test..." @keyup.enter="onFilterChange" />
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
              <th>Lớp học</th>
              <th>Bài kiểm tra</th>
              <th>Giáo viên</th>
              <th class="is-center">Tổng điểm</th>
              <th class="is-center">Ngày đánh giá</th>
              <th class="is-center">Trạng thái</th>
              <th class="is-center">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="9" class="ev-state"><div class="ev-spinner"></div>Đang tải dữ liệu...</td>
            </tr>
            <tr v-else-if="results.length === 0">
              <td colspan="9" class="ev-state">
                <i class="feather icon-inbox"></i>
                <div class="ev-state__title">Không có kết quả tổng kết</div>
                <div class="ev-state__sub">Học sinh được thêm tự động khi khởi tạo tổng kết cho lớp.</div>
              </td>
            </tr>
            <tr v-for="(item, index) in results" v-else :key="item.id">
              <td class="col-stt">{{ (pagination.from || 1) + index }}</td>
              <td>
                <div class="ev-person">
                  <span class="ev-avatar" :style="avatarStyle(item.stu_nm)">{{ initials(item.stu_nm) }}</span>
                  <div>
                    <div class="ev-person__name">{{ item.stu_nm }}</div>
                    <div class="ev-person__meta">{{ item.erp_student_code || item.stu_seq || '—' }}</div>
                  </div>
                </div>
              </td>
              <td>{{ item.erp_class_name || item.class_nm || '—' }}</td>
              <td>{{ item.test_nm || '—' }}</td>
              <td>{{ item.teacher_nm || '—' }}</td>
              <td class="is-center">
                <span v-if="item.total_score > 0" class="ev-score is-primary">{{ item.total_score }}</span>
                <span v-else class="ev-badge ev-badge--warning">Chưa chấm</span>
              </td>
              <td class="is-center">{{ fmtDate(item.eval_dt) }}</td>
              <td class="is-center">
                <span v-if="item.status === 'completed'" class="ev-badge ev-badge--success">Đã chốt</span>
                <span v-else class="ev-badge ev-badge--gray">Bản nháp</span>
              </td>
              <td class="is-center">
                <div class="ev-actions">
                  <router-link :to="`/lms/evaluations/igbh/summative/report/${item.id}`" class="ev-act-text ev-act-text--success">
                    <i class="feather icon-file-text"></i> Báo cáo
                  </router-link>
                  <router-link :to="{ path: `/lms/evaluations/igbh/summative/report/${item.id}`, query: { print: 1 } }" class="ev-act ev-act--info" title="In báo cáo / Lưu PDF">
                    <i class="feather icon-printer"></i>
                  </router-link>
                  <router-link :to="`/lms/evaluations/igbh/summative/form/${item.id}`" class="ev-act ev-act--primary" title="Chấm điểm">
                    <i class="feather icon-edit-2"></i>
                  </router-link>
                  <button class="ev-act ev-act--danger" title="Xóa đánh giá" @click="confirmDelete(item)">
                    <i class="feather icon-trash-2"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <eval-table-foot
        :page="pagination.current_page" :per-page="pagination.per_page" :total="pagination.total" unit="học sinh"
        @page="goPage" @per-page="setPerPage" />
    </div>
  </div>
</template>

<script>
import axios from '@/http/axios.js'
import multiselect from 'vue-multiselect'
import '../../_shared/eval-ui.css'
import EvalModuleNav from '../../_shared/EvalModuleNav.vue'
import EvalTableFoot from '../../_shared/EvalTableFoot.vue'
import { initials, avatarStyle, fmtDate } from '../../_shared/helpers'

export default {
  components: { multiselect, EvalModuleNav, EvalTableFoot },
  data() {
    return {
      search: '',
      loading: false,
      results: [],
      classes: [],
      selectedClass: null,
      selectedStatus: null,
      statusOptions: [
        { value: 'completed', label: 'Đã chốt' },
        { value: 'draft', label: 'Bản nháp' }
      ],
      pagination: { current_page: 1, per_page: 20, total: 0, from: 1 }
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
        if (res && res.data) this.classes = res.data.classes || []
      })
    },
    fetchData() {
      this.loading = true
      const params = {
        search: this.search,
        page: this.pagination.current_page,
        per_page: this.pagination.per_page,
        class_id: this.selectedClass ? this.selectedClass.id : null,
        status: this.selectedStatus ? this.selectedStatus.value : null
      }
      axios.g('/api/lms/evaluations/summative/results', params).then(res => {
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
      this.selectedStatus = null
      this.onFilterChange()
    },
    confirmDelete(item) {
      this.$vs.dialog({
        type: 'confirm',
        color: 'danger',
        title: 'Xác nhận xóa',
        text: `Xóa kết quả tổng kết của ${item.stu_nm}?`,
        acceptText: 'Xóa',
        cancelText: 'Hủy',
        accept: () => {
          axios.del(`/api/lms/evaluations/summative/results/${item.id}`).then(() => {
            this.$vs.notify({ title: 'Đã xóa', text: 'Xóa thành công', color: 'success' })
            this.fetchData()
          })
        }
      })
    }
  }
}
</script>
