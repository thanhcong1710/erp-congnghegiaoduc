<template>
  <div id="page-report-26">
    <vx-card no-shadow class="rpt-card">
      <div class="mb-6 flex items-center">
        <div class="mr-3 flex items-center justify-center p-3 rounded-lg" style="background:rgba(79, 70, 229, 0.1); color:#4f46e5;">
          <i class="fas fa-users text-xl"></i>
        </div>
        <div>
          <h3 class="text-lg font-bold uppercase" style="color:#4f46e5; margin:0;">BÁO CÁO CHI TIẾT ĐÃ XẾP LỚP</h3>
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
          <label class="rpt-label">Nhân viên sale</label>
          <multiselect v-model="searchData.ec_obj" :options="ec_list" label="name" track-by="id"
            placeholder="Chọn sale" :searchable="true" selectedLabel="" selectLabel="" deselectLabel="">
            <span slot="noResult">Không tìm thấy</span>
          </multiselect>
        </div>
        <div>
          <label class="rpt-label">Khóa lẻ</label>
          <multiselect v-model="searchData.product_obj" :options="product_list" label="name" track-by="id"
            placeholder="Chọn khóa lẻ" :searchable="true" selectedLabel="" selectLabel="" deselectLabel="">
            <span slot="noResult">Không tìm thấy</span>
          </multiselect>
        </div>
        <div>
          <label class="rpt-label">Tìm kiếm học sinh</label>
          <vs-input v-model="searchData.keyword" placeholder="Mã HV / Họ tên / SĐT" class="w-full" />
        </div>
        <div>
          <label class="rpt-label">Lớp học</label>
          <vs-input v-model="searchData.class_keyword" placeholder="Mã / Tên lớp" class="w-full" />
        </div>
        <div>
          <label class="rpt-label">Ngày khai giảng (từ — đến)</label>
          <date-picker v-model="searchData.start_date_range" type="date" range :clearable="true"
            format="YYYY-MM-DD" style="width:100%" :lang="dpLang" placeholder="Từ ngày — Đến ngày" />
        </div>
        <div>
          <label class="rpt-label">Ngày tạo (từ — đến)</label>
          <date-picker v-model="searchData.date_range" type="date" range :clearable="true"
            format="YYYY-MM-DD" style="width:100%" :lang="dpLang" placeholder="Từ ngày — Đến ngày" />
        </div>
      </div>

      <div class="rpt-actions mb-5">
        <vs-button class="rpt-btn" @click="getData"><i class="fa fa-search"></i> Tìm kiếm</vs-button>
        <vs-button color="dark" type="border" class="rpt-btn" @click="reset"><i class="fas fa-undo-alt"></i> Hủy</vs-button>
        <vs-button color="success" class="rpt-btn" @click="exportExcel"><i class="fa fa-file-excel"></i> Xuất Excel</vs-button>
        <span class="rpt-badge-count">{{ pagination.total }} bản ghi</span>
      </div>


      <!-- Table -->
      <div class="rpt-table-wrap">
        <table class="rpt-table">
          <thead>
            <tr>
              <th style="min-width:50px" class="text-center">STT</th>
              <th style="min-width:120px">Ngày tạo</th>
              <th style="min-width:180px">Họ tên HS</th>
              <th style="min-width:120px">Mã HS</th>
              <th style="min-width:120px">SĐT</th>
              <th style="min-width:120px">SĐT phụ huynh</th>
              <th style="min-width:200px">Khóa học đăng ký</th>
              <th style="min-width:150px">Khóa lẻ</th>
              <th style="min-width:150px">Lớp đăng ký</th>
              <th style="min-width:120px" class="text-center">Ngày khai giảng</th>
              <th style="min-width:150px">Team kinh doanh</th>
              <th style="min-width:150px">Thành viên sale</th>
            </tr>
          </thead>
          <tbody>
            <tr class="rpt-row" v-for="(row, idx) in datas" :key="idx">
              <td class="text-center">{{ idx + 1 + (pagination.cpage - 1) * pagination.limit }}</td>
              <td class="date-cell">{{ row.created_at }}</td>
              <td class="student-name">{{ row.student_name }}</td>
              <td class="lms-code">{{ row.lms_code }}</td>
              <td class="student-phone font-bold text-black" style="color:#000;">{{ row.phone }}</td>
              <td class="student-phone">{{ row.parent_phone }}</td>
              <td>{{ row.course_name || '—' }}</td>
              <td>{{ row.product_name || '—' }}</td>
              <td>{{ row.class_name || '—' }}</td>
              <td class="text-center date-cell">{{ row.start_date || '—' }}</td>
              <td>{{ row.team_name || '—' }}</td>
              <td>{{ row.ec_name || '—' }}</td>
            </tr>
            <tr v-if="datas.length === 0">
              <td colspan="12" class="text-center py-8">Không có dữ liệu · Nhấn Tìm kiếm để tải</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination (giống report07) -->
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
        <vs-pagination style="width:calc(100% - 180px);"
          v-if="Math.ceil(pagination.total / pagination.limit) > 1"
          :total="Math.ceil(pagination.total / pagination.limit)"
          :max="7"
          v-model="pagination.cpage"
          @change="changePage()" />
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
        branch_list: [], team_list: [], ec_list: [], product_list: [],
        datas: [],
        summary: null,
        limitSource: [20, 50, 100, 500],
        pagination: { cpage: 1, total: 0, limit: 20, init: 0 },
        searchData: {
          arr_branch: [], branch_id: [],
          team_obj: null, ec_obj: null, class_status_obj: null, product_obj: null,
          keyword: '', class_keyword: '',
          date_range: '',
          start_date_range: '',
        },
        dpLang: {
          days: ['CN','T2','T3','T4','T5','T6','T7'],
          months: ['Tháng 1','Tháng 2','Tháng 3','Tháng 4','Tháng 5','Tháng 6','Tháng 7','Tháng 8','Tháng 9','Tháng 10','Tháng 11','Tháng 12'],
        },
        user_role: { user_id: 0, is_sale: false, is_sale_leader: false },
      }
    },
    created() {
      axios.g(`/api/system/current-user-role`).then(response => { this.user_role = response.data })
      axios.g('/api/system/branches-has-user').then(r => { this.branch_list = r.data })
      axios.g('/api/system/users?role_id=69').then(r => { this.team_list = r.data || [] })
      axios.g('/api/system/users?role_id=68,69').then(r => { this.ec_list = r.data || [] })
      axios.g('/api/system/products').then(r => { this.product_list = r.data || [] })
      this.getData()
    },
    methods: {
      reset() {
        this.searchData = { arr_branch: [], branch_id: [], team_obj: null, ec_obj: null, class_status_obj: null, product_obj: null, keyword: '', class_keyword: '', date_range: '', start_date_range: '' }
        this.pagination.cpage = 1
        this.getData()
      },
      fmtDate(d) {
        if (!d) return ''
        if (d instanceof Date) {
          const y = d.getFullYear(), m = String(d.getMonth() + 1).padStart(2,'0'), day = String(d.getDate()).padStart(2,'0')
          return `${y}-${m}-${day}`
        }
        return d
      },
      fmtMoney(v) {
        if (v === null || v === undefined) return '0'
        const n = parseFloat(v)
        if (isNaN(n)) return v
        return n.toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
      },
      isDue(date) {
        if (!date) return false
        return new Date(date) < new Date()
      },
      buildPayload() {
        const branch_id = (this.searchData.arr_branch || []).map(b => b.id)
        let start_date = '', end_date = ''
        if (Array.isArray(this.searchData.date_range) && this.searchData.date_range[0]) {
          start_date = this.fmtDate(this.searchData.date_range[0])
          end_date   = this.fmtDate(this.searchData.date_range[1])
        }
        let cls_start_date = '', cls_end_date = ''
        if (Array.isArray(this.searchData.start_date_range) && this.searchData.start_date_range[0]) {
          cls_start_date = this.fmtDate(this.searchData.start_date_range[0])
          cls_end_date   = this.fmtDate(this.searchData.start_date_range[1])
        }
        return {
          branch_id,
          team_id:   this.searchData.team_obj ? this.searchData.team_obj.id : 0,
          ec_id:     this.searchData.ec_obj   ? this.searchData.ec_obj.id   : 0,
          class_status: this.searchData.class_status_obj ? this.searchData.class_status_obj.id : -1,
          product_id: this.searchData.product_obj ? this.searchData.product_obj.id : 0,
          keyword:   this.searchData.keyword  || '',
          class_keyword: this.searchData.class_keyword || '',
          start_date, end_date,
          cls_start_date, cls_end_date,
          pagination: this.pagination,
        }
      },
      getData() {
        const ids = []
        if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
          this.searchData.arr_branch.forEach(i => ids.push(i.id))
        }
        this.searchData.branch_id = ids
        this.$vs.loading()
        axios.p('/api/lms/reports/26', this.buildPayload())
          .then(res => {
            this.$vs.loading.close()
            this.datas   = res.data.list    || []
            this.pagination = res.data.paging || this.pagination
            setTimeout(() => { this.pagination.init = 1 }, 500)
          })
          .catch(e => { console.error(e); this.$vs.loading.close() })
      },
      changePage()           { if (this.pagination.init) this.getData() },
      changePageLimit(limit) { this.pagination.cpage = 1; this.pagination.limit = limit; this.getData() },
      exportExcel() {
        const p = this.buildPayload()
        const keys = [], values = []
        if (p.branch_id && p.branch_id.length)  { keys.push('branch_id');  values.push(p.branch_id.join('-')) }
        if (p.team_id  > 0)  { keys.push('team_id');  values.push(p.team_id) }
        if (p.ec_id    > 0)  { keys.push('ec_id');    values.push(p.ec_id) }
        if (p.class_status !== -1) { keys.push('class_status'); values.push(p.class_status) }
        if (p.product_id > 0) { keys.push('product_id'); values.push(p.product_id) }
        if (p.keyword)       { keys.push('keyword');  values.push(encodeURIComponent(p.keyword)) }
        if (p.class_keyword) { keys.push('class_keyword'); values.push(encodeURIComponent(p.class_keyword)) }
        if (p.start_date)     { keys.push('start_date'); values.push(p.start_date) }
        if (p.end_date)       { keys.push('end_date');   values.push(p.end_date) }
        if (p.cls_start_date) { keys.push('cls_start_date'); values.push(p.cls_start_date) }
        if (p.cls_end_date)   { keys.push('cls_end_date');   values.push(p.cls_end_date) }
        if (keys.length === 0) { keys.push('k'); values.push('v') }
        window.open(`/api/lms/exports/report26/${keys.join(',')}/${values.join(',')}?token=${localStorage.getItem('accessToken')}`, '_blank')
      },
    },
  }
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
#page-report-26 { font-family: 'Inter', sans-serif; }

.rpt-header { display:flex; align-items:center; gap:16px; background:linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%); color:white; padding:20px 24px; border-radius:12px; box-shadow:0 4px 20px rgba(79,70,229,.3); margin-bottom:20px; }
.rpt-header__icon { font-size:26px; width:50px; height:50px; background:rgba(255,255,255,.2); border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.rpt-header__title { font-size:1.05rem; font-weight:700; margin:0; }
.rpt-header__sub { font-size:.82rem; opacity:.8; margin:3px 0 0; }

.rpt-card { border-radius:12px !important; box-shadow:0 2px 16px rgba(0,0,0,.06) !important; }
.rpt-filter-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(230px,1fr)); gap:16px; }
.rpt-label { display:block; font-size:.75rem; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.05em; margin-bottom:6px; }
.rpt-actions { display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
.rpt-btn { border-radius:8px !important; }
.rpt-badge-count { margin-left:auto; background:#eef2ff; color:#4338ca; padding:6px 14px; border-radius:20px; font-weight:600; font-size:.85rem; }

/* Summary pills */
.rpt-summary { display:flex; gap:12px; flex-wrap:wrap; }
.rpt-pill { padding:10px 18px; border-radius:10px; min-width:150px; }
.rpt-pill__label { display:block; font-size:.7rem; font-weight:600; text-transform:uppercase; opacity:.7; }
.rpt-pill__value { display:block; font-size:1rem; font-weight:700; }
.rpt-pill--blue  { background:#eff6ff; color:#1d4ed8; }
.rpt-pill--green { background:#d1fae5; color:#065f46; }
.rpt-pill--red   { background:#fee2e2; color:#b91c1c; }

/* Table */
.rpt-table-wrap { overflow-x:auto; max-height:75vh; overflow-y:auto; border-radius:10px; border:1px solid #e5e7eb; }
.rpt-table { width:100%; border-collapse:collapse; font-size:15px; }
.rpt-table thead tr { background:linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%); }
.rpt-table thead th { color:white; font-weight:600; padding:11px 10px; white-space:nowrap; border:1px solid rgba(255,255,255,0.2); text-align:left;  font-size:15px; position:sticky; top:0; z-index:2;  background:linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%); }
.rpt-table thead th.text-right  { text-align:right; }
.rpt-table thead th.text-center { text-align:center; }

.rpt-row { border-bottom:1px solid #f3f4f6; transition:background .15s; }
.rpt-row:hover { background:#f8f7ff; }
.rpt-row td { padding:9px 10px; vertical-align:middle; border:1px solid #e5e7eb; }

.lms-code { font-weight:600; color:#4338ca; font-size:15px; }
.student-name { font-weight:600; }
.student-phone { font-size:15px; }
.text-center { text-align:center; }
.text-right  { text-align:right; }
.py-8 { padding:32px 0; }

.money-cell  { font-weight:600; color:#374151; }
.money-green { font-weight:600; color:#059669; }
.money-red   { font-weight:700; color:#dc2626; }
.date-cell   { font-size:15px; }
.overdue     { color:#dc2626; font-weight:700; }

/* Pagination */
.rpt-paging { display:flex; align-items:center; flex-wrap:wrap; margin-top:16px; }
.paging-limit-btn { display:flex; align-items:center; gap:8px; padding:8px 14px; border:1px solid #e5e7eb; border-radius:8px; cursor:pointer; background:white; font-size:.85rem; font-weight:500; }
.mr-4 { margin-right:16px; }
.multiselect { z-index:999; }
</style>
