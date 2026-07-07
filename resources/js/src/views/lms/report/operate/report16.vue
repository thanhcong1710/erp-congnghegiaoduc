<template>
  <div id="page-report-16">
    <vx-card no-shadow class="rpt-card">
      <div class="mb-6 flex items-center">
        <div class="mr-3 flex items-center justify-center p-3 rounded-lg" style="background:rgba(79, 70, 229, 0.1); color:#4f46e5;">
          <i class="fas fa-chart-pie text-xl"></i>
        </div>
        <div>
          <h3 class="text-lg font-bold uppercase" style="color:#4f46e5; margin:0;">BÁO CÁO DOANH THU CHƯA PHÂN BỔ THEO TRUNG TÂM</h3>
        </div>
      </div>
      <div class="rpt-filter-grid mb-5">
        <div v-show="false">
          <label class="rpt-label">Trung tâm</label>
          <multiselect name="search_branch" placeholder="Chọn trung tâm" v-model="searchData.arr_branch" :options="branch_list" label="name" :close-on-select="false" :hide-selected="true" :multiple="true" :searchable="true" track-by="id" selectedLabel="" selectLabel="" deselectLabel=""><span slot="noResult">Không tìm thấy</span></multiselect>
        </div>
        <div>
          <label class="rpt-label">Sản phẩm</label>
          <multiselect name="search_product" placeholder="Chọn sản phẩm" v-model="searchData.arr_product" :options="product_list" label="name" :close-on-select="false" :hide-selected="true" :multiple="true" :searchable="true" track-by="id" selectedLabel="" selectLabel="" deselectLabel=""><span slot="noResult">Không tìm thấy</span></multiselect>
        </div>
        <div>
          <label class="rpt-label">Trạng thái</label>
          <multiselect name="search_status" placeholder="Chọn trạng thái" v-model="searchData.arr_status" :options="statusOptions" label="label" :close-on-select="false" :hide-selected="true" :multiple="true" :searchable="false" track-by="id" selectedLabel="" selectLabel="" deselectLabel=""><span slot="noResult">Không tìm thấy</span></multiselect>
        </div>
        <div>
          <label class="rpt-label">Thời gian tạo contract</label>
          <date-picker v-model="searchData.dateRange" format="YYYY-MM-DD" style="width:100%" type="date" range :clearable="true" :lang="datepickerOptions.lang" placeholder="Từ ngày — Đến ngày"></date-picker>
        </div>
      </div>
      <div class="rpt-actions mb-5">
        <vs-button class="rpt-btn" @click="getData"><i class="fa fa-search"></i> Tìm kiếm</vs-button>
        <vs-button color="dark" type="border" class="rpt-btn" @click="reset"><i class="fas fa-undo-alt"></i> Hủy</vs-button>
        <vs-button color="success" class="rpt-btn" @click="exportExcel"><i class="fa fa-file-excel"></i> Xuất Excel</vs-button>
      </div>

      <!-- Summary Pills -->
      <div class="rpt-summary mb-5">
        <div class="rpt-pill rpt-pill--indigo">
          <span class="rpt-pill__label">Tổng contracts</span>
          <span class="rpt-pill__value">{{ summary.total_contracts | formatNumber }}</span>
        </div>
        <div class="rpt-pill rpt-pill--blue">
          <span class="rpt-pill__label">Tiền còn lại</span>
          <span class="rpt-pill__value">{{ summary.total_left_amount | formatMoney }} ₫</span>
        </div>
        <div class="rpt-pill rpt-pill--green">
          <span class="rpt-pill__label">Đã đóng</span>
          <span class="rpt-pill__value">{{ summary.total_charged | formatMoney }} ₫</span>
        </div>
        <div class="rpt-pill rpt-pill--red">
          <span class="rpt-pill__label">Tổng nợ</span>
          <span class="rpt-pill__value">{{ summary.total_debt_amount | formatMoney }} ₫</span>
        </div>
      </div>

      <div class="rpt-table-wrap">
        <table class="rpt-table">
          <thead>
            <tr>
              <th style="width:44px" class="text-center">STT</th>
              <th>Trung tâm</th>
              <th class="text-center">Số contracts</th>
              <th class="text-right">Tổng phải đóng</th>
              <th class="text-right">Tổng đã đóng</th>
              <th class="text-right">Tổng nợ</th>
              <th class="text-right">Tiền còn lại</th>
            </tr>
          </thead>
          <tbody>
            <tr class="rpt-row" v-for="(item, index) in datas" :key="index">
              <td class="text-center text-muted">{{ index + 1 }}</td>
              <td class="font-medium">{{ item.branch_name }}</td>
              <td class="text-center num-cell">{{ item.total_contracts | formatNumber }}</td>
              <td class="text-right num-cell">{{ item.total_must_charge | formatMoney }}</td>
              <td class="text-right num-cell">{{ item.total_charged | formatMoney }}</td>
              <td class="text-right" style="color:#b91c1c; font-weight:600;">{{ item.total_debt_amount | formatMoney }}</td>
              <td class="text-right"><span class="left-box">{{ item.total_left_amount | formatMoney }}</span></td>
            </tr>
            <!-- Tổng cộng -->
            <tr class="rpt-total" v-if="datas.length > 0">
              <td colspan="2" class="text-right">TỔNG CỘNG</td>
              <td class="text-center">{{ summary.total_contracts | formatNumber }}</td>
              <td class="text-right">{{ summary.total_must_charge | formatMoney }}</td>
              <td class="text-right">{{ summary.total_charged | formatMoney }}</td>
              <td class="text-right" style="color:#b91c1c;">{{ summary.total_debt_amount | formatMoney }}</td>
              <td class="text-right"><span class="left-box-total">{{ summary.total_left_amount | formatMoney }}</span></td>
            </tr>
          </tbody>
        </table>
      </div>
    </vx-card>
  </div>
</template>

<script>
  import axios from '../../../../http/axios.js'
  import Multiselect from 'vue-multiselect'
  import DatePicker from 'vue2-datepicker'
  import u from '../../../../until/helper.js'

  export default {
    components: { Multiselect, DatePicker },
    data() {
      return {
        branch_list: [], product_list: [],
        statusOptions: [ {id:'2',label:'Đặt cọc'}, {id:'3-4-5',label:'Chờ xếp lớp'}, {id:'6',label:'Đang học'} ],
        searchData: { arr_branch:'', branch_id:'', arr_product:'', product_id:'', arr_status:'', status:'', dateRange:'' },
        datepickerOptions: { lang: { days:['CN','T2','T3','T4','T5','T6','T7'], months:['Tháng 1','Tháng 2','Tháng 3','Tháng 4','Tháng 5','Tháng 6','Tháng 7','Tháng 8','Tháng 9','Tháng 10','Tháng 11','Tháng 12'] } },
        datas: [],
        summary: { total_contracts:0, total_must_charge:0, total_charged:0, total_debt_amount:0, total_left_amount:0 },
      }
    },
    created() {
      axios.g('/api/system/branches-has-user').then(r => { this.branch_list = r.data })
      axios.g('/api/system/products').then(r => { this.product_list = r.data })
      this.getData()
    },
    methods: {
      reset() { this.searchData = { arr_branch:'', branch_id:'', arr_product:'', product_id:'', arr_status:'', status:'', dateRange:'' }; this.getData() },
      getData() {
        const branch_ids = [], product_ids = [], status_ids = []
        if (this.searchData.arr_branch && this.searchData.arr_branch.length) this.searchData.arr_branch.forEach(i => branch_ids.push(i.id))
        if (this.searchData.arr_product && this.searchData.arr_product.length) this.searchData.arr_product.forEach(i => product_ids.push(i.id))
        if (this.searchData.arr_status && this.searchData.arr_status.length) this.searchData.arr_status.forEach(i => { if (i.id === '3-4-5') { status_ids.push(3,4,5) } else { status_ids.push(parseInt(i.id)) } })
        this.searchData.branch_id = branch_ids; this.searchData.product_id = product_ids; this.searchData.status = status_ids
        let start_date = '', end_date = ''
        if (this.searchData.dateRange && this.searchData.dateRange.length === 2) { start_date = u.dateToString(this.searchData.dateRange[0]); end_date = u.dateToString(this.searchData.dateRange[1]) }
        this.$vs.loading()
        axios.p('/api/lms/reports/16', { branch_id: branch_ids, product_id: product_ids, status: status_ids, start_date, end_date })
          .then(res => { this.$vs.loading.close(); this.datas = res.data.list; this.summary = res.data.summary }).catch(e => { console.error(e); this.$vs.loading.close() })
      },
      exportExcel() {
        let keys = [], values = []
        if (this.searchData.branch_id && this.searchData.branch_id.length > 0) { keys.push('branch_id'); values.push(this.searchData.branch_id.join('-')) }
        if (this.searchData.product_id && this.searchData.product_id.length > 0) { keys.push('product_id'); values.push(this.searchData.product_id.join('-')) }
        if (this.searchData.status && this.searchData.status.length > 0) { keys.push('status'); values.push(this.searchData.status.join('-')) }
        if (this.searchData.dateRange && this.searchData.dateRange.length === 2) { keys.push('start_date'); values.push(u.dateToString(this.searchData.dateRange[0])); keys.push('end_date'); values.push(u.dateToString(this.searchData.dateRange[1])) }
        if (keys.length === 0) { keys.push('k'); values.push('v') }
        window.open(`/api/lms/exports/report16/${keys.join(',')}/${values.join(',')}?token=${localStorage.getItem('accessToken')}`, '_blank')
      }
    },
    filters: {
      formatMoney(v) { if (!v) return '0'; return Number(v).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g,'$1,') },
      formatNumber(v) { if (!v) return '0'; return Number(v).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g,'$1,') }
    }
  }
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
#page-report-16 { font-family:'Inter',sans-serif; }
.rpt-header { display:flex; align-items:center; gap:16px; background:linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%); color:white; padding:20px 24px; border-radius:12px; box-shadow:0 4px 20px rgba(79,70,229,.3); margin-bottom:20px; }
.rpt-header__icon { font-size:26px; width:50px; height:50px; background:rgba(255,255,255,.2); border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.rpt-header__title { font-size:1.05rem; font-weight:700; margin:0; }
.rpt-header__sub { font-size:.82rem; opacity:.8; margin:3px 0 0; }
.rpt-card { border-radius:12px !important; box-shadow:0 2px 16px rgba(0,0,0,.06) !important; }
.rpt-filter-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:16px; }
.rpt-label { display:block; font-size:.75rem; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.05em; margin-bottom:6px; }
.rpt-actions { display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
.rpt-btn { border-radius:8px !important; }
.rpt-summary { display:flex; flex-wrap:wrap; gap:10px; }
.rpt-pill { flex:1; min-width:130px; padding:12px 16px; border-radius:10px; display:flex; flex-direction:column; gap:4px; }
.rpt-pill__label { font-size:.7rem; font-weight:600; text-transform:uppercase; opacity:.7; }
.rpt-pill__value { font-size:1rem; font-weight:700; }
.rpt-pill--blue   { background:#eff6ff; color:#1d4ed8; }
.rpt-pill--indigo { background:#eef2ff; color:#4338ca; }
.rpt-pill--green  { background:#f0fdf4; color:#15803d; }
.rpt-pill--red    { background:#fef2f2; color:#b91c1c; }
.rpt-table-wrap { overflow-x:auto; border-radius:10px; border:1px solid #e5e7eb; }
.rpt-table { width:100%; border-collapse:collapse; font-size:.85rem; }
.rpt-table thead tr { background:linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%); }
.rpt-table thead th { color:white; font-weight:600; padding:11px 10px; white-space:nowrap; border:1px solid rgba(255,255,255,0.2); }
.rpt-row { border-bottom:1px solid #f3f4f6; transition:background .15s; }
.rpt-row:hover { background:#f8f7ff; }
.rpt-row td { padding:10px; vertical-align:middle; border:1px solid #e5e7eb; }
.font-medium { font-weight:500; }
.num-cell { font-weight:600; }
.text-muted { color:#9ca3af; }
/* ── Dòng TỔNG CỘNG: clean indigo, không gradient ── */
.rpt-total td { background:#eef2ff; font-weight:700; padding:11px 10px; border-top:2px solid #4f46e5; color:#1e1b4b; font-size:.88rem; }
.left-box { background:#eef2ff; color:#4338ca; padding:5px 12px; border-radius:8px; font-weight:700; font-size:.88rem; display:inline-block; }
.left-box-total { background:#4f46e5; color:white; padding:6px 14px; border-radius:8px; font-weight:700; font-size:.88rem; display:inline-block; }
.multiselect { z-index:999; }
</style>
