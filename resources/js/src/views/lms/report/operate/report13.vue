<template>
  <div id="page-report-13">
    <vx-card no-shadow class="rpt-card">
      <div class="mb-6 flex items-center">
        <div class="mr-3 flex items-center justify-center p-3 rounded-lg" style="background:rgba(79, 70, 229, 0.1); color:#4f46e5;">
          <i class="fas fa-dollar-sign text-xl"></i>
        </div>
        <div>
          <h3 class="text-lg font-bold uppercase" style="color:#4f46e5; margin:0;">BÁO CÁO DOANH SỐ CHI TIẾT HỌC SINH ĐI HỌC</h3>
        </div>
      </div>
      <div class="rpt-filter-grid mb-5">
        <div>
          <label class="rpt-label">Trung tâm</label>
          <multiselect name="search_branch" placeholder="Chọn trung tâm" v-model="searchData.arr_branch" :options="branch_list" label="name" :close-on-select="false" :hide-selected="true" :multiple="true" :searchable="true" track-by="id" selectedLabel="" selectLabel="" deselectLabel=""><span slot="noResult">Không tìm thấy</span></multiselect>
        </div>
        <div>
          <label class="rpt-label">Từ khóa</label>
          <vs-input class="w-full" placeholder="Mã học sinh, tên học sinh" v-model="searchData.keyword"></vs-input>
        </div>
        <div>
          <label class="rpt-label">Thời gian</label>
          <date-picker v-model="searchData.dateRange" format="YYYY-MM-DD" style="width:100%" type="date" range :clearable="true" :lang="datepickerOptions.lang" placeholder="Từ ngày — Đến ngày"></date-picker>
        </div>
      </div>
      <div class="rpt-actions mb-5">
        <vs-button class="rpt-btn" @click="getData"><i class="fa fa-search"></i> Tìm kiếm</vs-button>
        <vs-button color="dark" type="border" class="rpt-btn" @click="reset"><i class="fas fa-undo-alt"></i> Hủy</vs-button>
        <vs-button color="success" class="rpt-btn" @click="exportExcel"><i class="fa fa-file-excel"></i> Xuất Excel</vs-button>
        <div class="rpt-revenue-pill">
          <span class="rpt-pill__label">Tổng doanh thu</span>
          <span class="rpt-pill__value">{{ totalRevenue | formatMoney }} ₫</span>
        </div>
      </div>

      <div class="rpt-table-wrap">
        <table class="rpt-table">
          <thead>
            <tr>
              <th style="width:44px" class="text-center">STT</th>
              <th>Trung tâm</th>
              <th>Mã HS</th>
              <th>Tên HS</th>
              <th>Lớp học</th>
              <th class="text-center">Ngày học</th>
              <th class="text-right">Doanh số (VNĐ)</th>
            </tr>
          </thead>
          <tbody>
            <tr class="rpt-row" v-for="(item, index) in datas" :key="index">
              <td class="text-center text-muted">{{ index + 1 + (pagination.cpage - 1) * pagination.limit }}</td>
              <td class="text-muted small">{{ item.branch_name }}</td>
              <td><span class="badge-code">{{ item.lms_code }}</span></td>
              <td class="font-medium">{{ item.student_name }}</td>
              <td>{{ item.class_name }}</td>
              <td class="text-center text-muted">{{ item.class_date }}</td>
              <td class="text-right money-cell">{{ item.session_value | formatMoney }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="rpt-paging">
        <vs-dropdown vs-trigger-click class="cursor-pointer mr-4">
          <div class="paging-limit-btn">
            <span>{{ pagination.cpage * pagination.limit - (pagination.limit - 1) }} – {{ Math.min(pagination.cpage * pagination.limit, pagination.total) }} / {{ pagination.total }}</span>
            <feather-icon icon="ChevronDownIcon" svgClasses="h-4 w-4" />
          </div>
          <vs-dropdown-menu>
            <vs-dropdown-item v-for="item in limitSource" :key="item" @click="changePageLimit(item)">{{ item }}</vs-dropdown-item>
          </vs-dropdown-menu>
        </vs-dropdown>
        <vs-pagination style="width:calc(100% - 180px);" v-if="Math.ceil(pagination.total / pagination.limit) > 1" :total="Math.ceil(pagination.total / pagination.limit)" :max="7" v-model="pagination.cpage" @change="changePage()"/>
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
        searchData: { arr_branch:'', branch_id:'', keyword:'', dateRange:'' },
        datepickerOptions: { lang: { days:['CN','T2','T3','T4','T5','T6','T7'], months:['Tháng 1','Tháng 2','Tháng 3','Tháng 4','Tháng 5','Tháng 6','Tháng 7','Tháng 8','Tháng 9','Tháng 10','Tháng 11','Tháng 12'] } },
        datas: [], totalRevenue: 0,
        limitSource: [20, 50, 100, 500],
        pagination: { cpage:1, total:0, limit:20, init:0 },
      }
    },
    created() {
      axios.g('/api/system/branches-has-user').then(r => { this.branch_list = r.data })
      this.getData()
    },
    methods: {
      reset() { this.searchData = { arr_branch:'', branch_id:'', keyword:'', dateRange:'' }; this.getData() },
      getData() {
        const ids = []
        if (this.searchData.arr_branch && this.searchData.arr_branch.length) this.searchData.arr_branch.forEach(i => ids.push(i.id))
        this.searchData.branch_id = ids
        let start_date = '', end_date = ''
        if (this.searchData.dateRange && this.searchData.dateRange.length === 2) { start_date = u.dateToString(this.searchData.dateRange[0]); end_date = u.dateToString(this.searchData.dateRange[1]) }
        this.$vs.loading()
        axios.p('/api/lms/reports/13', { keyword: this.searchData.keyword, branch_id: this.searchData.branch_id, start_date, end_date, pagination: this.pagination })
          .then(res => { this.$vs.loading.close(); this.datas = res.data.list; this.pagination = res.data.paging; this.totalRevenue = res.data.total_revenue || 0; setTimeout(() => { this.pagination.init = 1 }, 500) }).catch(e => { console.error(e); this.$vs.loading.close() })
      },
      changePage() { if (this.pagination.init) this.getData() },
      changePageLimit(limit) { this.pagination.cpage = 1; this.pagination.limit = limit; this.getData() },
      exportExcel() {
        let keys = [], values = []
        const ids = []
        if (this.searchData.arr_branch && this.searchData.arr_branch.length) this.searchData.arr_branch.forEach(i => ids.push(i.id))
        if (ids.length) { keys.push('branch_id'); values.push(ids.join('-')) }
        if (this.searchData.keyword) { keys.push('keyword'); values.push(this.searchData.keyword) }
        if (this.searchData.dateRange && this.searchData.dateRange.length === 2) { keys.push('start_date'); values.push(u.dateToString(this.searchData.dateRange[0])); keys.push('end_date'); values.push(u.dateToString(this.searchData.dateRange[1])) }
        if (keys.length === 0) { keys.push('k'); values.push('v') }
        window.open(`/api/lms/exports/report13/${keys.join(',')}/${values.join(',')}?token=${localStorage.getItem('accessToken')}`, '_blank')
      }
    },
    filters: {
      formatMoney(v) { if (!v) return '0'; return Number(v).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g,'$1,') }
    }
  }
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
#page-report-13 { font-family:'Inter',sans-serif; }
.rpt-header { display:flex; align-items:center; gap:16px; background:linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%); color:white; padding:20px 24px; border-radius:12px; box-shadow:0 4px 20px rgba(79,70,229,.3); margin-bottom:20px; }
.rpt-header__icon { font-size:26px; width:50px; height:50px; background:rgba(255,255,255,.2); border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.rpt-header__title { font-size:1.05rem; font-weight:700; margin:0; }
.rpt-header__sub { font-size:.82rem; opacity:.8; margin:3px 0 0; }
.rpt-card { border-radius:12px !important; box-shadow:0 2px 16px rgba(0,0,0,.06) !important; }
.rpt-filter-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:16px; }
.rpt-label { display:block; font-size:.75rem; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.05em; margin-bottom:6px; }
.rpt-actions { display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
.rpt-btn { border-radius:8px !important; }
.rpt-revenue-pill { margin-left:auto; background:#eef2ff; color:#4338ca; padding:8px 16px; border-radius:10px; display:flex; flex-direction:column; gap:2px; }
.rpt-pill__label { font-size:.7rem; font-weight:600; text-transform:uppercase; opacity:.7; }
.rpt-pill__value { font-size:.95rem; font-weight:700; }
.rpt-table-wrap { overflow-x:auto; border-radius:10px; border:1px solid #e5e7eb; }
.rpt-table { width:100%; border-collapse:collapse; font-size:.85rem; }
.rpt-table thead tr { background:linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%); }
.rpt-table thead th { color:white; font-weight:600; padding:11px 10px; white-space:nowrap; border:1px solid rgba(255,255,255,0.2); }
.rpt-row { border-bottom:1px solid #f3f4f6; transition:background .15s; }
.rpt-row:hover { background:#f8f7ff; }
.rpt-row td { padding:9px 10px; vertical-align:middle; border:1px solid #e5e7eb; }
.badge-code { background:#eef2ff; color:#4338ca; border-radius:6px; padding:2px 8px; font-size:.78rem; font-weight:600; }
.font-medium { font-weight:500; }
.money-cell { font-weight:700; color:#4338ca; }
.text-muted { color:#9ca3af; }
.small { font-size:.8rem; }
.rpt-paging { display:flex; align-items:center; flex-wrap:wrap; margin-top:16px; }
.paging-limit-btn { display:flex; align-items:center; gap:8px; padding:8px 14px; border:1px solid #e5e7eb; border-radius:8px; cursor:pointer; background:white; font-size:.85rem; font-weight:500; }
.multiselect { z-index:999; }
</style>
