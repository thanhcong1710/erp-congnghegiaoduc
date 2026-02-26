<template>
  <div id="page-report-14">
    <div class="rpt-header">
      <div class="rpt-header__icon"><i class="fas fa-building"></i></div>
      <div>
        <h3 class="rpt-header__title">BÁO CÁO TỔNG QUAN DOANH THU THEO TRUNG TÂM</h3>
        <p class="rpt-header__sub">Tổng hợp doanh thu học sinh đi học theo trung tâm</p>
      </div>
    </div>

    <vx-card no-shadow class="rpt-card">
      <div class="rpt-filter-grid mb-5" style="max-width:320px;">
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
              <th class="text-center">Tổng số HS</th>
              <th class="text-center">Số buổi học</th>
              <th class="text-right">Doanh thu (VNĐ)</th>
            </tr>
          </thead>
          <tbody>
            <tr class="rpt-row" v-for="(item, index) in datas" :key="index">
              <td class="text-center text-muted">{{ index + 1 }}</td>
              <td class="font-medium">{{ item.branch_name }}</td>
              <td class="text-center num-cell">{{ item.total_students }}</td>
              <td class="text-center num-cell">{{ item.total_sessions }}</td>
              <td class="text-right money-cell">{{ item.total_revenue | formatMoney }}</td>
            </tr>
          </tbody>
        </table>
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
        searchData: { dateRange:'' },
        datepickerOptions: { lang: { days:['CN','T2','T3','T4','T5','T6','T7'], months:['Tháng 1','Tháng 2','Tháng 3','Tháng 4','Tháng 5','Tháng 6','Tháng 7','Tháng 8','Tháng 9','Tháng 10','Tháng 11','Tháng 12'] } },
        datas: [], totalRevenue: 0,
      }
    },
    created() { this.getData() },
    methods: {
      reset() { this.searchData.dateRange = ''; this.getData() },
      getData() {
        let start_date = '', end_date = ''
        if (this.searchData.dateRange && this.searchData.dateRange.length === 2) { start_date = u.dateToString(this.searchData.dateRange[0]); end_date = u.dateToString(this.searchData.dateRange[1]) }
        this.$vs.loading()
        axios.p('/api/lms/reports/14', { start_date, end_date }).then(res => { this.$vs.loading.close(); this.datas = res.data.list; this.totalRevenue = res.data.total_revenue || 0 }).catch(e => { console.error(e); this.$vs.loading.close() })
      },
      exportExcel() {
        let keys = [], values = []
        if (this.searchData.dateRange && this.searchData.dateRange.length === 2) { keys.push('start_date'); values.push(u.dateToString(this.searchData.dateRange[0])); keys.push('end_date'); values.push(u.dateToString(this.searchData.dateRange[1])) }
        if (keys.length === 0) { keys.push('k'); values.push('v') }
        window.open(`/api/lms/exports/report14/${keys.join(',')}/${values.join(',')}?token=${localStorage.getItem('accessToken')}`, '_blank')
      }
    },
    filters: {
      formatMoney(v) { if (!v) return '0'; return Number(v).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g,'$1,') }
    }
  }
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
#page-report-14 { font-family:'Inter',sans-serif; }
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
.rpt-table thead th { color:white; font-weight:600; padding:11px 10px; white-space:nowrap; border:none; }
.rpt-row { border-bottom:1px solid #f3f4f6; transition:background .15s; }
.rpt-row:hover { background:#f8f7ff; }
.rpt-row td { padding:10px; vertical-align:middle; }
.font-medium { font-weight:500; }
.num-cell { font-weight:600; color:#374151; text-align:center; }
.money-cell { font-weight:700; color:#4338ca; }
.text-muted { color:#9ca3af; }
</style>
