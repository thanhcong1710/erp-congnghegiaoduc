<template>
  <div id="page-report-17">
    <!-- Header Card -->
    <vx-card no-shadow class="rpt-card">
      <div class="mb-6 flex items-center">
        <div class="mr-3 flex items-center justify-center p-3 rounded-lg" style="background:rgba(79, 70, 229, 0.1); color:#4f46e5;">
          <i class="fas fa-chart-line text-xl"></i>
        </div>
        <div>
          <h3 class="text-lg font-bold uppercase" style="color:#4f46e5; margin:0;">BÁO CÁO TIẾN ĐỘ HỌC SAU 01 NĂM</h3>
        </div>
      </div>
      <!-- Filters -->
      <div class="rpt-filter-grid mb-5">
        <div v-show="false" class="rpt-filter-item">
          <label class="rpt-label">Trung tâm</label>
          <multiselect
            name="search_branch"
            placeholder="Chọn trung tâm"
            v-model="searchData.arr_branch"
            :options="branch_list"
            label="name"
            :close-on-select="false"
            :hide-selected="true"
            :multiple="true"
            :searchable="true"
            track-by="id"
            selectedLabel="" selectLabel="" deselectLabel=""
          ><span slot="noResult">Không tìm thấy dữ liệu</span></multiselect>
        </div>
        <div class="rpt-filter-item">
          <label class="rpt-label">Năm học</label>
          <multiselect
            name="search_school_year"
            placeholder="Chọn năm học"
            v-model="searchData.school_year"
            :options="schoolYearOptions"
            label="label"
            :close-on-select="true"
            :multiple="false"
            :searchable="false"
            track-by="id"
            selectedLabel="" selectLabel="" deselectLabel=""
          ><span slot="noResult">Không tìm thấy</span></multiselect>
        </div>
        <div class="rpt-filter-item">
          <label class="rpt-label">Từ khóa</label>
          <vs-input class="w-full" placeholder="Mã HS, Tên HS" v-model="searchData.keyword"></vs-input>
        </div>
        <div class="rpt-filter-item">
          <label class="rpt-label">Thời gian full fee</label>
          <date-picker name="item-date" v-model="searchData.dateRange" format="YYYY-MM-DD"
            style="width:100%" type="date" range :clearable="true" :lang="datepickerOptions.lang"
            placeholder="Từ ngày — Đến ngày"></date-picker>
        </div>
      </div>
      <div class="rpt-actions mb-5">
        <vs-button class="rpt-btn-search" @click="getData"><i class="fa fa-search"></i> Tìm kiếm</vs-button>
        <vs-button color="dark" type="border" class="rpt-btn-reset" @click="reset"><i class="fas fa-undo-alt"></i> Hủy</vs-button>
        <vs-button color="success" class="rpt-btn-export" @click="exportExcel"><i class="fa fa-file-excel"></i> Xuất Excel</vs-button>
      </div>

      <!-- Summary Pills -->
      <div class="rpt-summary mb-5">
        <div class="rpt-pill rpt-pill--blue">
          <span class="rpt-pill__label">Tổng combos</span>
          <span class="rpt-pill__value">{{ pagination.total | formatNumber }}</span>
        </div>
        <div class="rpt-pill rpt-pill--indigo">
          <span class="rpt-pill__label">Tổng học phí</span>
          <span class="rpt-pill__value">{{ summary.total_combo_fee | formatMoney }}</span>
        </div>
        <div class="rpt-pill rpt-pill--green">
          <span class="rpt-pill__label">Đã sử dụng</span>
          <span class="rpt-pill__value">{{ summary.total_used_value | formatMoney }}</span>
        </div>
        <div class="rpt-pill rpt-pill--orange">
          <span class="rpt-pill__label">Còn lại</span>
          <span class="rpt-pill__value">{{ summary.total_left_value | formatMoney }}</span>
        </div>
        <div class="rpt-pill rpt-pill--gray">
          <span class="rpt-pill__label">Tổng buổi</span>
          <span class="rpt-pill__value">{{ summary.total_sessions | formatNumber }}</span>
        </div>
        <div class="rpt-pill rpt-pill--teal">
          <span class="rpt-pill__label">Đã học</span>
          <span class="rpt-pill__value">{{ summary.total_done_sessions | formatNumber }}</span>
        </div>
        <div class="rpt-pill rpt-pill--red">
          <span class="rpt-pill__label">Chưa học</span>
          <span class="rpt-pill__value">{{ summary.total_left_sessions | formatNumber }}</span>
        </div>
      </div>

      <!-- Table -->
      <div class="rpt-table-wrap">
        <table class="rpt-table">
          <thead>
            <tr>
              <th style="width:44px">STT</th>
              <th style="width:100px">Mã HV</th>
              <th style="width:200px">Họ tên</th>
              <th style="width:220px">Combo</th>
              <th style="width:80px" class="text-center">Khóa</th>
              <th style="width:130px">Bắt đầu</th>
              <th style="width:130px">Full fee</th>
              <th style="width:100px" class="text-center">Buổi</th>
              <th style="width:130px" class="text-right">Học phí</th>
              <th style="width:100px" class="text-center">Đã học</th>
              <th style="width:80px" class="text-center">%</th>
              <th style="width:100px" class="text-center">Còn lại</th>
              <th style="width:130px" class="text-right">Đã dùng</th>
              <th style="width:130px" class="text-right">Còn lại</th>
            </tr>
          </thead>
          <tbody>
            <template v-for="(tr, idx) in contracts">
              <tr v-if="!tr.is_summary" :key="'r-'+idx" class="rpt-table__row">
                <td class="text-center">{{ (pagination.cpage - 1) * pagination.limit + idx + 1 }}</td>
                <td><span class="badge-code">{{ tr.student_code }}</span></td>
                <td class="font-medium">{{ tr.student_name }}</td>
                <td>{{ tr.combo_name }}</td>
                <td class="text-center">{{ tr.total_courses }}</td>
                <td class="small">{{ tr.first_course_start_date | formatDateView }}</td>
                <td class="small">{{ tr.full_fee_date | formatDateView }}</td>
                <td class="text-center num-cell">{{ tr.total_sessions }}</td>
                <td class="text-right money-cell">{{ tr.combo_fee | formatMoney }}</td>
                <td class="text-center num-cell">{{ tr.done_sessions }}</td>
                <td class="text-center">
                  <span class="pct-badge" :class="percentClass(tr.completion_rate)">{{ tr.completion_rate }}%</span>
                </td>
                <td class="text-center num-cell">{{ tr.left_sessions }}</td>
                <td class="text-right money-cell">{{ tr.used_value | formatMoney }}</td>
                <td class="text-right money-cell highlight-left">{{ tr.left_value | formatMoney }}</td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="rpt-paging mt-4">
        <vs-dropdown vs-trigger-click class="cursor-pointer mr-4">
          <div class="paging-limit-btn">
            <span>{{ pagination.cpage * pagination.limit - (pagination.limit - 1) }} – {{ Math.min(pagination.cpage * pagination.limit, pagination.total) }} / {{ pagination.total }}</span>
            <feather-icon icon="ChevronDownIcon" svgClasses="h-4 w-4" />
          </div>
          <vs-dropdown-menu>
            <vs-dropdown-item v-for="item in limitSource" :key="item" @click="pagination.limit=item; getData()">{{ item }}</vs-dropdown-item>
          </vs-dropdown-menu>
        </vs-dropdown>
        <vs-pagination
          style="width: calc(100% - 180px);"
          v-if="Math.ceil(pagination.total / pagination.limit) > 1"
          :total="Math.ceil(pagination.total / pagination.limit)" :max="7"
          v-model="pagination.cpage" @change="changePage()"/>
      </div>
    </vx-card>
  </div>
</template>

<script>
  import vSelect from 'vue-select'
  import axios from '../../../../http/axios.js'
  import Multiselect from 'vue-multiselect'
  import DatePicker from 'vue2-datepicker'
  import u from '../../../../until/helper.js'

  export default {
    components: { vSelect, Multiselect, DatePicker },
    data() {
      return {
        branch_list: [],
        schoolYearOptions: [
          {id:'2023',label:'Năm 2023'},{id:'2024',label:'Năm 2024'},
          {id:'2025',label:'Năm 2025'},{id:'2026',label:'Năm 2026'},
        ],
        searchData: { arr_branch: '', branch_id: '', school_year: '', keyword: '', dateRange: '' },
        datepickerOptions: {
          lang: {
            days: ['CN','T2','T3','T4','T5','T6','T7'],
            months: ['Tháng 1','Tháng 2','Tháng 3','Tháng 4','Tháng 5','Tháng 6','Tháng 7','Tháng 8','Tháng 9','Tháng 10','Tháng 11','Tháng 12']
          }
        },
        contracts: [],
        summary: { total_left_value:0, total_used_value:0, total_combo_fee:0, total_sessions:0, total_done_sessions:0, total_left_sessions:0 },
        limitSource: [20, 50, 100, 500],
        pagination: { url:'/api/lms/reports/17', id:'', style:'line', class:'', spage:1, ppage:1, npage:0, lpage:1, cpage:1, total:0, limit:20, pages:[], init:0 },
      }
    },
    created() {
      axios.g('/api/system/branches-has-user').then(r => { this.branch_list = r.data })
      this.getData()
    },
    methods: {
      reset() {
        this.searchData = { arr_branch:'', branch_id:'', school_year:'', keyword:'', dateRange:'' }
        this.getData()
      },
      percentClass(v) {
        if (v >= 80) return 'pct-high'
        if (v >= 50) return 'pct-mid'
        return 'pct-low'
      },
      getData() {
        const branch_ids = []
        if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
          this.searchData.arr_branch.forEach(i => branch_ids.push(i.id))
        }
        this.searchData.branch_id = branch_ids
        let start_date = '', end_date = ''
        if (this.searchData.dateRange && this.searchData.dateRange.length === 2) {
          start_date = u.dateToString(this.searchData.dateRange[0])
          end_date   = u.dateToString(this.searchData.dateRange[1])
        }
        const data = {
          branch_id: this.searchData.branch_id,
          school_year: this.searchData.school_year ? this.searchData.school_year.id : '',
          keyword: this.searchData.keyword, start_date, end_date, pagination: this.pagination
        }
        this.$vs.loading()
        axios.p('/api/lms/reports/17', data)
          .then(res => {
            this.$vs.loading.close()
            this.contracts = res.data.list
            this.summary   = res.data.summary || this.summary
            this.pagination = res.data.paging
            setTimeout(() => { this.pagination.init = 1 }, 500)
          }).catch(e => { console.error(e); this.$vs.loading.close() })
      },
      changePage() { if (this.pagination.init) this.getData() },
      exportExcel() {
        let keys = [], values = []
        if (this.searchData.branch_id && this.searchData.branch_id.length > 0) { keys.push('branch_id'); values.push(this.searchData.branch_id.join('-')) }
        if (this.searchData.school_year && this.searchData.school_year.id) { keys.push('school_year'); values.push(this.searchData.school_year.id) }
        if (this.searchData.keyword) { keys.push('keyword'); values.push(this.searchData.keyword) }
        if (this.searchData.dateRange && this.searchData.dateRange.length === 2) {
          keys.push('start_date'); values.push(u.dateToString(this.searchData.dateRange[0]))
          keys.push('end_date');   values.push(u.dateToString(this.searchData.dateRange[1]))
        }
        if (keys.length === 0) { keys.push('k'); values.push('v') }
        window.open(`/api/lms/exports/report17/${keys.join(',')}/${values.join(',')}?token=${localStorage.getItem('accessToken')}`, '_blank')
      }
    },
    filters: {
      formatMoney(v) { if (!v) return '0'; return Number(v).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g,'$1,') },
      formatNumber(v) { if (!v) return '0'; return Number(v).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g,'$1,') },
      formatDateView(v) {
        if (!v) return ''
        const d = new Date(v); if (isNaN(d.getTime())) return v
        return `${String(d.getDate()).padStart(2,'0')}/${String(d.getMonth()+1).padStart(2,'0')}/${d.getFullYear()}`
      }
    }
  }
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

#page-report-17 { font-family: 'Inter', sans-serif; }

/* Header */
.rpt-header {
  display: flex; align-items: center; gap: 16px;
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  color: white; padding: 20px 24px; border-radius: 12px;
  box-shadow: 0 4px 20px rgba(79,70,229,.3);
}
.rpt-header__icon { font-size: 28px; width: 52px; height: 52px;
  background: rgba(255,255,255,.2); border-radius: 12px;
  display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.rpt-header__title { font-size: 1.1rem; font-weight: 700; margin: 0; }
.rpt-header__sub { font-size: .85rem; opacity: .8; margin: 2px 0 0; }

/* Card */
.rpt-card { border-radius: 12px !important; box-shadow: 0 2px 16px rgba(0,0,0,.06) !important; }

/* Filters */
.rpt-filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; }
.rpt-label { display: block; font-size: .78rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 6px; }
.rpt-actions { display: flex; gap: 10px; flex-wrap: wrap; }
.rpt-btn-search, .rpt-btn-reset, .rpt-btn-export { border-radius: 8px !important; }

/* Summary pills */
.rpt-summary { display: flex; flex-wrap: wrap; gap: 10px; }
.rpt-pill {
  flex: 1; min-width: 120px; padding: 12px 16px; border-radius: 10px;
  display: flex; flex-direction: column; gap: 4px;
}
.rpt-pill__label { font-size: .72rem; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; opacity: .75; }
.rpt-pill__value { font-size: 1rem; font-weight: 700; }
.rpt-pill--blue   { background: #eff6ff; color: #1d4ed8; }
.rpt-pill--indigo { background: #eef2ff; color: #4338ca; }
.rpt-pill--green  { background: #f0fdf4; color: #15803d; }
.rpt-pill--orange { background: #fff7ed; color: #c2410c; }
.rpt-pill--gray   { background: #f9fafb; color: #374151; }
.rpt-pill--teal   { background: #f0fdfa; color: #0f766e; }
.rpt-pill--red    { background: #fef2f2; color: #b91c1c; }

/* Table */
.rpt-table-wrap { overflow-x:auto; max-height:75vh; overflow-y:auto; border-radius: 10px; border: 1px solid #e5e7eb; }
.rpt-table { width: 100%; border-collapse: collapse; font-size:15px; }
.rpt-table thead tr { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); }
.rpt-table thead th { color:white; font-weight:600; padding:11px 10px; white-space:nowrap; border:1px solid rgba(255,255,255,0.2); font-size:15px; position:sticky; top:0; z-index:2;  background:linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%); }
.rpt-total td {
  background: #eef2ff;
  font-weight: 700;
  padding: 11px 10px;
  font-size: .88rem;
  border-top: 2px solid #4f46e5;
  color: #1e1b4b;
}
.rpt-table__row { border-bottom: 1px solid #f3f4f6; transition: background .15s; }
.rpt-table__row:hover { background: #f8f7ff; }
.rpt-table__row td { padding: 9px 10px; }

.badge-code { background: #eef2ff; color: #4338ca; border-radius: 6px; padding: 2px 8px; font-size:15px; font-weight: 600; }
.num-cell { font-weight: 600; color: #374151; }
.money-cell { font-weight: 600; color: #374151; }
.highlight-left { color: #b91c1c; font-weight: 700; }
.small { font-size:15px; }

.pct-badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: .78rem; font-weight: 700; }
.pct-high { background: #dcfce7; color: #15803d; }
.pct-mid  { background: #fef9c3; color: #a16207; }
.pct-low  { background: #fee2e2; color: #b91c1c; }

/* Paging */
.rpt-paging { display: flex; align-items: center; flex-wrap: wrap; }
.paging-limit-btn {
  display: flex; align-items: center; gap: 8px; padding: 8px 14px;
  border: 1px solid #e5e7eb; border-radius: 8px; cursor: pointer;
  background: white; font-size:.85rem; font-weight:500; }

.multiselect { z-index: 999; }
</style>
