<template>
  <div id="lms-test-catalog" class="ev-page">
    <eval-module-nav />

    <div class="ev-card">
      <div class="ev-hero">
        <div class="ev-hero__main">
          <span class="ev-hero__icon" :class="selectedType === 'UCREA' ? '' : 'is-blue'"><i class="feather icon-book-open"></i></span>
          <div>
            <h4 class="ev-hero__title">{{ pageTitle }}</h4>
            <p class="ev-hero__sub">Danh mục đề thi – xem, tải tài liệu PDF và thông tin cấu hình bài khảo sát</p>
          </div>
        </div>
        <div v-if="!routeFilterType" class="ev-tabs">
          <button v-for="t in typeTabs" :key="t.value" class="ev-tab" :class="{ 'is-active': selectedType === t.value }" @click="setType(t.value)">
            {{ t.label }}
          </button>
        </div>
      </div>

      <div class="ev-filter">
        <div style="grid-column: span 2;">
          <label class="ev-field__label">Tìm kiếm</label>
          <div class="ev-search">
            <i class="feather icon-search"></i>
            <input v-model="search" class="ev-input" placeholder="Tên bài kiểm tra, mã test, level..." @keyup.enter="onFilterChange" />
          </div>
        </div>
        <div class="ev-filter__actions">
          <button class="ev-btn ev-btn--dark" @click="onFilterChange"><i class="feather icon-search"></i> Tìm</button>
          <button class="ev-btn ev-btn--ghost" @click="resetSearch"><i class="feather icon-rotate-ccw"></i> Đặt lại</button>
        </div>
      </div>

      <div class="ev-table-wrap">
        <table class="ev-table">
          <thead>
            <tr>
              <th class="col-stt">#</th>
              <th class="col-wide">Tên bài kiểm tra</th>
              <th class="is-center">Hệ thống</th>
              <th class="is-center">Loại</th>
              <th class="is-center">Mã bài test</th>
              <th class="is-center">Level</th>
              <th class="is-center">Test Seq</th>
              <th class="is-center">Tài liệu đề thi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="8" class="ev-state"><div class="ev-spinner"></div>Đang tải danh mục bài test...</td>
            </tr>
            <tr v-else-if="tests.length === 0">
              <td colspan="8" class="ev-state">
                <i class="feather icon-inbox"></i>
                <div class="ev-state__title">Không tìm thấy bài kiểm tra nào</div>
              </td>
            </tr>
            <tr v-for="(item, index) in tests" v-else :key="item.id">
              <td class="col-stt">{{ (pagination.current_page - 1) * pagination.per_page + index + 1 }}</td>
              <td class="ev-strong">{{ item.name }}</td>
              <td class="is-center">
                <span class="ev-badge no-dot" :class="item.test_type === 'UCREA' ? 'ev-badge--primary' : 'ev-badge--info'">{{ item.test_type }}</span>
              </td>
              <td class="is-center"><span class="ev-badge ev-badge--gray no-dot">{{ kindOf(item) }}</span></td>
              <td class="is-center"><code class="ev-code">{{ item.test_cd || '—' }}</code></td>
              <td class="is-center">{{ item.level_cd || '—' }}</td>
              <td class="is-center ev-strong">{{ item.test_seq }}</td>
              <td class="is-center">
                <div class="ev-actions">
                  <template v-if="item.local_pdf_path">
                    <a :href="`/${item.local_pdf_path}`" target="_blank" class="ev-act-text ev-act-text--primary" title="Xem đề PDF (tab mới)">
                      <i class="feather icon-eye"></i> Xem đề
                    </a>
                    <a :href="`/${item.local_pdf_path}`" download class="ev-act ev-act--info" title="Tải về file PDF">
                      <i class="feather icon-download"></i>
                    </a>
                  </template>
                  <a v-else-if="item.pdf_url" :href="item.pdf_url" target="_blank" class="ev-act-text ev-act-text--primary" title="Mở trên LMS">
                    <i class="feather icon-external-link"></i> Link LMS
                  </a>
                  <span v-else class="ev-muted">—</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <eval-table-foot
        :page="pagination.current_page" :per-page="pagination.per_page" :total="pagination.total" unit="bài kiểm tra"
        @page="goPage" @per-page="setPerPage" />
    </div>
  </div>
</template>

<script>
import axios from '@/http/axios.js'
import '../_shared/eval-ui.css'
import EvalModuleNav from '../_shared/EvalModuleNav.vue'
import EvalTableFoot from '../_shared/EvalTableFoot.vue'

const UCREA_KIND = { TT001: 'Đầu kỳ', TT002: 'Giữa kỳ', TT003: 'Cuối kỳ' }

export default {
  components: { EvalModuleNav, EvalTableFoot },
  data() {
    return {
      selectedType: '',
      routeFilterType: '',
      search: '',
      loading: false,
      tests: [],
      typeTabs: [
        { value: '', label: 'Tất cả' },
        { value: 'UCREA', label: 'UCREA' },
        { value: 'IG.BH', label: 'IG.BH' }
      ],
      pagination: { current_page: 1, per_page: 20, total: 0 }
    }
  },
  computed: {
    pageTitle() {
      if (this.selectedType === 'UCREA') return 'Đề kiểm tra UCREA'
      if (this.selectedType === 'IG.BH') return 'Đề kiểm tra IG.BH'
      return 'Danh mục đề kiểm tra'
    }
  },
  watch: {
    '$route.path'() {
      this.checkRouteType()
      this.onFilterChange()
    }
  },
  created() {
    this.checkRouteType()
    this.fetchData()
  },
  methods: {
    kindOf(item) {
      if (item.test_type === 'UCREA') return UCREA_KIND[item.test_cd] || 'UCREA'
      return /summative/i.test(item.name || '') ? 'Tổng kết' : 'Đầu vào'
    },
    checkRouteType() {
      let t = ''
      if (this.$route.meta && this.$route.meta.filterType) t = this.$route.meta.filterType
      else if (this.$route.path.includes('/ucrea/')) t = 'UCREA'
      else if (this.$route.path.includes('/igbh/')) t = 'IG.BH'
      this.routeFilterType = t
      this.selectedType = t
    },
    setType(type) {
      this.selectedType = type
      this.onFilterChange()
    },
    fetchData() {
      this.loading = true
      const params = {
        type: this.selectedType,
        search: this.search,
        page: this.pagination.current_page,
        per_page: this.pagination.per_page
      }
      axios.g('/api/lms/evaluations/tests', params).then(res => {
        this.loading = false
        if (res && res.data && res.data.data) {
          this.tests = res.data.data
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
    resetSearch() {
      this.search = ''
      this.onFilterChange()
    }
  }
}
</script>

<style>
.ev-code {
  font-family: SFMono-Regular, Menlo, Consolas, monospace;
  font-size: 13px;
  background: #f1f5f9;
  color: #334155;
  padding: 3px 8px;
  border-radius: 6px;
}
</style>
