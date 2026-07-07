<template>
  <div id="page-report-15">
    <vx-card no-shadow class="rpt-card">
      <div class="mb-6 flex items-center">
        <div class="mr-3 flex items-center justify-center p-3 rounded-lg" style="background:rgba(79, 70, 229, 0.1); color:#4f46e5;">
          <i class="fas fa-file-invoice-dollar text-xl"></i>
        </div>
        <div>
          <h3 class="text-lg font-bold uppercase" style="color:#4f46e5; margin:0;">BÁO CÁO DOANH THU CHƯA PHÂN BỔ THEO HỌC SINH</h3>
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
          <label class="rpt-label">Từ khóa</label>
          <vs-input class="w-full" placeholder="Mã HS, Tên HS, Mã contract" v-model="searchData.keyword"></vs-input>
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
          <span class="rpt-pill__value">{{ pagination.total | formatNumber }}</span>
        </div>
        <div class="rpt-pill rpt-pill--blue">
          <span class="rpt-pill__label">Số tiền còn lại</span>
          <span class="rpt-pill__value">{{ summary.total_left_amount | formatMoney }} ₫</span>
        </div>
        <div class="rpt-pill rpt-pill--green">
          <span class="rpt-pill__label">Đã đóng</span>
          <span class="rpt-pill__value">{{ summary.total_charged_sum | formatMoney }} ₫</span>
        </div>
        <div class="rpt-pill rpt-pill--red">
          <span class="rpt-pill__label">Tổng nợ</span>
          <span class="rpt-pill__value">{{ summary.total_debt_amount | formatMoney }} ₫</span>
        </div>
        <div class="rpt-pill rpt-pill--gray">
          <span class="rpt-pill__label">Tổng buổi</span>
          <span class="rpt-pill__value">{{ summary.total_summary_sessions | formatNumber }}</span>
        </div>
        <div class="rpt-pill rpt-pill--teal">
          <span class="rpt-pill__label">Buổi còn lại</span>
          <span class="rpt-pill__value">{{ summary.total_left_sessions | formatNumber }}</span>
        </div>
      </div>

      <div class="rpt-table-wrap">
        <table class="rpt-table">
          <thead>
            <tr>
              <th style="width:44px" class="text-center">STT</th>
              <th style="width:180px">Học sinh</th>
              <th style="width:200px">Contract</th>
              <th style="width:180px">Trung tâm / SP</th>
              <th style="width:150px">EC / EC Leader</th>
              <th style="width:120px" class="text-center">Số buổi</th>
              <th style="width:150px" class="text-right">Tài chính</th>
              <th style="width:140px" class="text-right">Còn lại</th>
            </tr>
          </thead>
          <tbody>
            <tr class="rpt-row" v-for="(item, index) in contracts" :key="index">
              <td class="text-center text-muted">{{ index + 1 + (pagination.cpage - 1) * pagination.limit }}</td>
              <td>
                <p class="font-medium">{{ item.student_name }}</p>
                <p class="text-muted small">{{ item.lms_code }}</p>
                <p class="text-muted small">{{ item.gud_mobile1 }}</p>
              </td>
              <td>
                <p><span class="badge-code">{{ item.contract_code }}</span></p>
                <p class="small mt-1">TT: <span :class="getStatusClass(item.status)">{{ item.label_status }}</span></p>
                <p class="text-muted small">{{ item.class_name || 'Chưa xếp lớp' }}</p>
                <p class="text-muted small">{{ item.tuition_fee_name }}</p>
              </td>
              <td>
                <p class="small">{{ item.branch_name }}</p>
                <p class="text-muted small">{{ item.product_name }}</p>
              </td>
              <td>
                <p class="small">EC: {{ item.ec_name || '---' }}</p>
                <p class="text-muted small">Lead: {{ item.ec_leader_name || '---' }}</p>
              </td>
              <td class="text-center">
                <div>
                  <span style="font-size:1.1rem;font-weight:700;color:#4338ca">{{ item.done_sessions }}</span>
                  <span class="text-muted"> / {{ item.summary_sessions }}</span>
                </div>
                <div class="text-muted" style="font-size:.78rem;">Còn: <strong>{{ item.left_sessions }}</strong> buổi</div>
                <vs-progress v-if="item.summary_sessions" :height="5" :percent="Math.round(item.done_sessions*100/item.summary_sessions)" color="primary" style="margin-top:4px;"></vs-progress>
              </td>
              <td class="text-right">
                <p class="small">Phải đóng: <strong>{{ item.must_charge | formatMoney }}</strong></p>
                <p class="text-muted small">Đã đóng: {{ item.total_charged | formatMoney }}</p>
                <p class="small" style="color:#b91c1c;">Nợ: {{ item.debt_amount | formatMoney }}</p>
              </td>
              <td class="text-right"><span class="left-box">{{ item.left_amount | formatMoney }}</span></td>
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
            <vs-dropdown-item v-for="item in limitSource" :key="item" @click="pagination.limit=item;getData()">{{ item }}</vs-dropdown-item>
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
        branch_list: [], product_list: [],
        statusOptions: [ {id:'2',label:'Đặt cọc'}, {id:'3-4-5',label:'Chờ xếp lớp'}, {id:'6',label:'Đang học'} ],
        searchData: { arr_branch:'', branch_id:'', arr_product:'', product_id:'', arr_status:'', status:'', keyword:'', dateRange:'', pagination: this.pagination },
        datepickerOptions: { lang: { days:['CN','T2','T3','T4','T5','T6','T7'], months:['Tháng 1','Tháng 2','Tháng 3','Tháng 4','Tháng 5','Tháng 6','Tháng 7','Tháng 8','Tháng 9','Tháng 10','Tháng 11','Tháng 12'] } },
        contracts: [],
        summary: { total_left_amount:0, total_charged_sum:0, total_must_charge:0, total_debt_amount:0, total_left_sessions:0, total_summary_sessions:0 },
        limitSource: [20, 50, 100, 500],
        pagination: { cpage:1, total:0, limit:20, init:0 },
      }
    },
    created() {
      axios.g('/api/system/branches-has-user').then(r => { this.branch_list = r.data })
      axios.g('/api/system/products').then(r => { this.product_list = r.data })
      this.getData()
    },
    methods: {
      reset() { this.searchData = { arr_branch:'', branch_id:'', arr_product:'', product_id:'', arr_status:'', status:'', keyword:'', dateRange:'' }; this.getData() },
      getStatusClass(s) { if (s===6) return 'font-bold' + ' text-success'; if (s===2) return 'font-bold' + ' text-warning'; return '' },
      getData() {
        const branch_ids = [], product_ids = [], status_ids = []
        if (this.searchData.arr_branch && this.searchData.arr_branch.length) this.searchData.arr_branch.forEach(i => branch_ids.push(i.id))
        if (this.searchData.arr_product && this.searchData.arr_product.length) this.searchData.arr_product.forEach(i => product_ids.push(i.id))
        if (this.searchData.arr_status && this.searchData.arr_status.length) this.searchData.arr_status.forEach(i => { if (i.id === '3-4-5') { status_ids.push(3,4,5) } else { status_ids.push(parseInt(i.id)) } })
        this.searchData.branch_id = branch_ids; this.searchData.product_id = product_ids; this.searchData.status = status_ids
        let start_date = '', end_date = ''
        if (this.searchData.dateRange && this.searchData.dateRange.length === 2) { start_date = u.dateToString(this.searchData.dateRange[0]); end_date = u.dateToString(this.searchData.dateRange[1]) }
        this.$vs.loading()
        axios.p('/api/lms/reports/15', { branch_id: branch_ids, product_id: product_ids, status: status_ids, keyword: this.searchData.keyword, start_date, end_date, pagination: this.pagination })
          .then(res => { this.$vs.loading.close(); this.contracts = res.data.list; this.summary = res.data.summary || this.summary; this.pagination = res.data.paging; setTimeout(() => { this.pagination.init = 1 }, 500) }).catch(e => { console.error(e); this.$vs.loading.close() })
      },
      changePage() { if (this.pagination.init) this.getData() },
      exportExcel() {
        let keys = [], values = []
        if (this.searchData.branch_id && this.searchData.branch_id.length > 0) { keys.push('branch_id'); values.push(this.searchData.branch_id.join('-')) }
        if (this.searchData.product_id && this.searchData.product_id.length > 0) { keys.push('product_id'); values.push(this.searchData.product_id.join('-')) }
        if (this.searchData.status && this.searchData.status.length > 0) { keys.push('status'); values.push(this.searchData.status.join('-')) }
        if (this.searchData.keyword) { keys.push('keyword'); values.push(this.searchData.keyword) }
        if (this.searchData.dateRange && this.searchData.dateRange.length === 2) { keys.push('start_date'); values.push(u.dateToString(this.searchData.dateRange[0])); keys.push('end_date'); values.push(u.dateToString(this.searchData.dateRange[1])) }
        if (keys.length === 0) { keys.push('k'); values.push('v') }
        window.open(`/api/lms/exports/report15/${keys.join(',')}/${values.join(',')}?token=${localStorage.getItem('accessToken')}`, '_blank')
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
#page-report-15 { font-family:'Inter',sans-serif; }
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
.rpt-pill__label { font-size:.7rem; font-weight:600; text-transform:uppercase; letter-spacing:.05em; opacity:.7; }
.rpt-pill__value { font-size:1rem; font-weight:700; }
.rpt-pill--blue    { background:#eff6ff; color:#1d4ed8; }
.rpt-pill--indigo  { background:#eef2ff; color:#4338ca; }
.rpt-pill--green   { background:#f0fdf4; color:#15803d; }
.rpt-pill--red     { background:#fef2f2; color:#b91c1c; }
.rpt-pill--teal    { background:#f0fdfa; color:#0f766e; }
.rpt-pill--gray    { background:#f9fafb; color:#374151; }
.rpt-table-wrap { overflow-x:auto; border-radius:10px; border:1px solid #e5e7eb; }
.rpt-table { width:100%; border-collapse:collapse; font-size:.84rem; }
.rpt-table thead tr { background:linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%); }
.rpt-table thead th { color:white; font-weight:600; padding:11px 10px; white-space:nowrap; border:1px solid rgba(255,255,255,0.2); }
.rpt-row { border-bottom:1px solid #f3f4f6; transition:background .15s; }
.rpt-row:hover { background:#f8f7ff; }
.rpt-row td { padding:9px 10px; vertical-align:top; }
.badge-code { background:#eef2ff; color:#4338ca; border-radius:6px; padding:2px 8px; font-size:.78rem; font-weight:600; }
.font-medium { font-weight:500; }
.text-muted { color:#9ca3af; }
.small { font-size:.8rem; }
.mt-1 { margin-top:4px; }
.left-box { background:#eef2ff; color:#4338ca; padding:5px 12px; border-radius:8px; font-weight:700; font-size:.9rem; display:inline-block; }
.text-success { color:#15803d; }
.text-warning { color:#a16207; }
.font-bold { font-weight:700; }
.rpt-paging { display:flex; align-items:center; flex-wrap:wrap; margin-top:16px; }
.paging-limit-btn { display:flex; align-items:center; gap:8px; padding:8px 14px; border:1px solid #e5e7eb; border-radius:8px; cursor:pointer; background:white; font-size:.85rem; font-weight:500; }
.multiselect { z-index:999; }
</style>
