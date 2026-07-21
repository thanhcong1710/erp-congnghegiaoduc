<template>
  <div id="page-report-23">
    <vx-card no-shadow class="rpt-card">
      <div class="mb-6 flex items-center">
        <div class="mr-3 flex items-center justify-center p-3 rounded-lg" style="background:rgba(79, 70, 229, 0.1); color:#4f46e5;">
          <i class="fas fa-chart-bar text-xl"></i>
        </div>
        <div>
          <h3 class="text-lg font-bold uppercase" style="color:#4f46e5; margin:0;">THỐNG KÊ DOANH THU THEO TEAM SALE</h3>
        </div>
      </div>
      <!-- Filters -->
      <div class="rpt-filter-grid mb-5">
        <div v-show="false">
          <label class="rpt-label">Trung tâm</label>
          <multiselect v-model="searchData.arr_branch" :options="branch_list" label="name" track-by="id"
            placeholder="Chọn trung tâm" :multiple="true" :close-on-select="false" :hide-selected="true"
            :searchable="true" selectedLabel="" selectLabel="" deselectLabel="">
            <span slot="noResult">Không tìm thấy</span>
          </multiselect>
        </div>
        <div>
          <label class="rpt-label">Team KD (quản lý)</label>
          <multiselect v-model="searchData.team_obj" :options="team_list" label="name" track-by="id"
            placeholder="Chọn team KD" :searchable="true" selectedLabel="" selectLabel="" deselectLabel="">
            <span slot="noResult">Không tìm thấy</span>
          </multiselect>
        </div>
        <div>
          <label class="rpt-label">Trạng thái công nợ</label>
          <multiselect v-model="searchData.completion_status_obj" :options="[{id:1, name:'Đã hoàn thành'}, {id:2, name:'Đã chuyển khoản'}, {id:3, name:'Chưa chuyển khoản'}]" label="name" track-by="id"
            placeholder="Tất cả" :searchable="false" selectedLabel="" selectLabel="" deselectLabel="">
          </multiselect>
        </div>
        <div>
          <label class="rpt-label">Ngày tạo (từ — đến)</label>
          <date-picker v-model="searchData.dateRange" type="date" range :clearable="true"
            format="YYYY-MM-DD" style="width:100%" :lang="dpLang" placeholder="Từ ngày — Đến ngày" />
        </div>
        <div>
          <label class="rpt-label">Ngày CK gần nhất</label>
          <date-picker v-model="searchData.pay_date_range" type="date" range :clearable="true"
            format="YYYY-MM-DD" style="width:100%" :lang="dpLang" placeholder="Từ ngày — Đến ngày" />
        </div>
        <div>
          <label class="rpt-label">Tháng tính lương</label>
          <div class="flex items-center gap-2">
            <date-picker v-model="searchData.salary_month" type="month" format="YYYY-MM" value-type="format" :lang="dpLang" placeholder="Chọn tháng" style="flex:1" :disabled="searchData.no_salary_month" :append-to-body="true"></date-picker>
            <vs-checkbox v-model="searchData.no_salary_month" style="margin:0">Chưa có</vs-checkbox>
          </div>
        </div>
      </div>

      <div class="rpt-actions mb-5">
        <vs-button class="rpt-btn" @click="getData"><i class="fa fa-search"></i> Tìm kiếm</vs-button>
        <vs-button color="dark" type="border" class="rpt-btn" @click="reset"><i class="fas fa-undo-alt"></i> Hủy</vs-button>
        <vs-button color="success" class="rpt-btn" @click="exportExcel"><i class="fa fa-file-excel"></i> Xuất Excel</vs-button>
        <span class="rpt-badge-count">{{ rows.length }} team</span>
      </div>

      <!-- Table -->
      <div class="rpt-table-wrap">
        <table class="rpt-table">
          <thead>
            <tr class="group-header-row">
              <th rowspan="2" style="width:44px" class="text-center">STT</th>
              <th rowspan="2" style="min-width:180px">TEAM</th>
              <th colspan="4" class="text-center th-green">DOANH SỐ</th>
              <th rowspan="2" style="width:140px" class="text-right th-orange">DOANH THU</th>
              <th rowspan="2" style="width:140px" class="text-right th-orange">LƯƠNG SALE</th>
            </tr>
            <tr>
              <th class="text-center th-green" style="width:90px">MỚI</th>
              <th class="text-center th-green" style="width:90px">UP LV</th>
              <th class="text-center th-green-dark" style="width:120px">DOANH SỐ<br>(CHƯA TÁCH)</th>
              <th class="text-center th-green-dark" style="width:120px">DOANH SỐ<br>(SAU TÁCH)</th>
            </tr>
          </thead>
          <tbody>
            <tr class="rpt-row" v-for="(row, idx) in rows" :key="idx">
              <td class="text-center">{{ idx + 1 }}</td>
              <td class="team-name">{{ row.team_name }}</td>
              <td class="text-center num-cell">{{ row.new_count }}</td>
              <td class="text-center num-cell">{{ row.uplv_count }}</td>
              <td class="text-center num-cell">{{ row.unseparated_sales }}</td>
              <td class="text-center num-cell">{{ row.separated_sales }}</td>
              <td class="text-right money-cell">{{ fmtMoney(row.total_revenue) }}</td>
              <td class="text-right salary-cell">{{ fmtMoney(row.salary) }}</td>
            </tr>
            <tr v-if="rows.length === 0">
              <td colspan="8" class="text-center py-8">Không có dữ liệu</td>
            </tr>
          </tbody>
          <tfoot v-if="summary">
            <tr class="rpt-total">
              <td colspan="2" class="text-right">TỔNG</td>
              <td class="text-center">{{ summary.new_count }}</td>
              <td class="text-center">{{ summary.uplv_count }}</td>
              <td class="text-center">{{ summary.unseparated_sales }}</td>
              <td class="text-center">{{ summary.separated_sales }}</td>
              <td class="text-right">{{ fmtMoney(summary.total_revenue) }}</td>
              <td class="text-right">{{ fmtMoney(summary.salary) }}</td>
            </tr>
          </tfoot>
        </table>
      </div>
    </vx-card>
  </div>
</template>

<script>
  import axios from '../../../../http/axios.js'
  import Multiselect from 'vue-multiselect'
  import DatePicker from 'vue2-datepicker'

  export default {
    components: { Multiselect, DatePicker },
    data() {
      return {
        branch_list: [], team_list: [],
        rows: [], summary: null,
        searchData: { 
          arr_branch: [], 
          dateRange: '', 
          team_obj: null, 
          completion_status_obj: null, 
          pay_date_range: [new Date(new Date().getFullYear(), new Date().getMonth(), 1), new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0)],
          salary_month: '',
          no_salary_month: false,
        },
        dpLang: {
          days: ['CN','T2','T3','T4','T5','T6','T7'],
          months: ['Tháng 1','Tháng 2','Tháng 3','Tháng 4','Tháng 5','Tháng 6','Tháng 7','Tháng 8','Tháng 9','Tháng 10','Tháng 11','Tháng 12'],
        },
      }
    },
    created() {
      axios.g('/api/system/branches-has-user').then(r => { this.branch_list = r.data })
      axios.g('/api/system/users?role_id=69').then(r => { this.team_list = r.data || [] })
      this.getData()
    },
    methods: {
      reset() {
        this.searchData = { 
          arr_branch: [], 
          dateRange: '', 
          team_obj: null, 
          completion_status_obj: null, 
          pay_date_range: [new Date(new Date().getFullYear(), new Date().getMonth(), 1), new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0)],
          salary_month: '',
          no_salary_month: false,
        }
        this.getData()
      },
      fmtDate(d) {
        if (!d) return ''
        if (d instanceof Date) {
          const y = d.getFullYear()
          const m = String(d.getMonth() + 1).padStart(2, '0')
          const day = String(d.getDate()).padStart(2, '0')
          return `${y}-${m}-${day}`
        }
        return d
      },
      fmtMoney(v) {
        if (v === null || v === undefined) return '0'
        const n = parseFloat(v)
        if (isNaN(n)) return '0'
        return n.toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
      },
      buildPayload() {
        const branch_id = (this.searchData.arr_branch || []).map(b => b.id)
        let start_date = '', end_date = ''
        if (Array.isArray(this.searchData.dateRange) && this.searchData.dateRange[0]) {
          start_date = this.fmtDate(this.searchData.dateRange[0])
          end_date   = this.fmtDate(this.searchData.dateRange[1])
        }
        let pay_start_date = '', pay_end_date = ''
        if (Array.isArray(this.searchData.pay_date_range) && this.searchData.pay_date_range[0]) {
          pay_start_date = this.fmtDate(this.searchData.pay_date_range[0])
          pay_end_date   = this.fmtDate(this.searchData.pay_date_range[1])
        }
        let salary_month = ''
        if (this.searchData.no_salary_month) {
          salary_month = 'none'
        } else if (this.searchData.salary_month) {
          salary_month = this.searchData.salary_month
        }
        return { 
          branch_id, 
          start_date, 
          end_date,
          team_id: this.searchData.team_obj ? this.searchData.team_obj.id : 0,
          completion_status: this.searchData.completion_status_obj ? this.searchData.completion_status_obj.id : -1,
          pay_start_date,
          pay_end_date,
          salary_month
        }
      },
      getData() {
        this.$vs.loading()
        axios.p('/api/lms/reports/23', this.buildPayload())
          .then(res => {
            this.$vs.loading.close()
            this.rows    = res.data.rows    || []
            this.summary = res.data.summary || null
          })
          .catch(e => { console.error(e); this.$vs.loading.close() })
      },
      exportExcel() {
        const p = this.buildPayload()
        const keys = [], values = []
        if (p.branch_id && p.branch_id.length) { keys.push('branch_id'); values.push(p.branch_id.join('-')) }
        if (p.start_date) { keys.push('start_date'); values.push(p.start_date) }
        if (p.end_date)   { keys.push('end_date');   values.push(p.end_date) }
        if (p.team_id > 0) { keys.push('team_id'); values.push(p.team_id) }
        if (p.completion_status !== -1) { keys.push('completion_status'); values.push(p.completion_status) }
        if (p.pay_start_date) { keys.push('pay_start_date'); values.push(p.pay_start_date) }
        if (p.pay_end_date)   { keys.push('pay_end_date');   values.push(p.pay_end_date) }
        if (p.salary_month)   { keys.push('salary_month');   values.push(p.salary_month) }
        if (keys.length === 0) { keys.push('k'); values.push('v') }
        window.open(`/api/lms/exports/report23/${keys.join(',')}/${values.join(',')}?token=${localStorage.getItem('accessToken')}`, '_blank')
      },
    },
  }
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
#page-report-23 { font-family: 'Inter', sans-serif; }

.rpt-header { display:flex; align-items:center; gap:16px; background:linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%); color:white; padding:20px 24px; border-radius:12px; box-shadow:0 4px 20px rgba(79,70,229,.3); margin-bottom:20px; }
.rpt-header__icon { font-size:26px; width:50px; height:50px; background:rgba(255,255,255,.2); border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.rpt-header__title { font-size:1.05rem; font-weight:700; margin:0; }
.rpt-header__sub { font-size:.82rem; opacity:.8; margin:3px 0 0; }

.rpt-card { border-radius:12px !important; box-shadow:0 2px 16px rgba(0,0,0,.06) !important; }
.rpt-filter-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:16px; }
.rpt-label { display:block; font-size:.75rem; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.05em; margin-bottom:6px; }
.rpt-actions { display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
.rpt-btn { border-radius:8px !important; }
.rpt-badge-count { margin-left:auto; background:#eef2ff; color:#4338ca; padding:6px 14px; border-radius:20px; font-weight:600; font-size:.85rem; }

/* Table */
.rpt-table-wrap { overflow-x:auto; max-height:75vh; overflow-y:auto; border-radius:10px; border:1px solid #e5e7eb; }
.rpt-table { width:100%; border-collapse:collapse; font-size:15px; }

/* Group header row */
.rpt-table thead .group-header-row th { padding:10px; white-space:nowrap; border:1px solid rgba(255,255,255,.2);  position:sticky; top:0; z-index:3; font-size:15px; }
.rpt-table thead tr:nth-child(2) th { padding:8px 10px; white-space:nowrap; border:1px solid rgba(255,255,255,.2);  position:sticky; top:43px; z-index:2; font-size:15px; }

/* Column color groups */
.th-green  { background:#16a34a !important; color:white !important; font-weight:600; }
.th-green-dark { background:#518E47 !important; color:white !important; font-weight:600; }
.th-orange { background:#ea580c !important; color:white !important; font-weight:600; }
.rpt-table thead th:not(.th-green):not(.th-green-dark):not(.th-orange) { background:linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%); color:white; font-weight:600;  font-size:15px; position:sticky; top:0; z-index:2; }

.rpt-row { border-bottom:1px solid #f3f4f6; transition:background .15s; }
.rpt-row:hover { background:#f8f7ff; }
.rpt-row td { padding:9px 10px; vertical-align:middle; border:1px solid #e5e7eb; border-bottom:1px solid #f3f4f6; }

/* Total row */
.rpt-total td { background:#eef2ff; font-weight:700; padding:11px 10px; font-size:.88rem; border-top:2px solid #4f46e5; color:#1e1b4b; }

.team-name   { font-weight:600; color:#374151; }
.num-cell    { font-weight:600; color:#374151; }
.money-cell  { font-weight:600; color:#374151; }
.salary-cell { font-weight:700; color:#ea580c; }
.text-center { text-align:center; }
.text-right  { text-align:right; }
.py-8 { padding:32px 0; }
.multiselect { z-index:999; }
</style>
