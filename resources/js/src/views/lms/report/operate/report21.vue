<template>
  <div id="page-report-21">
    <vx-card no-shadow class="rpt-card">
      <div class="mb-6 flex items-center">
        <div class="mr-3 flex items-center justify-center p-3 rounded-lg" style="background:rgba(79, 70, 229, 0.1); color:#4f46e5;">
          <i class="fas fa-table text-xl"></i>
        </div>
        <div>
          <h3 class="text-lg font-bold uppercase" style="color:#4f46e5; margin:0;">BẢNG TỔNG QUAN QUẢN LÝ HỌC SINH NĂM {{ displayYear }}</h3>
        </div>
      </div>
      <!-- Filters -->
      <div class="rpt-filter-grid mb-5">
        <div v-show="false">
          <label class="rpt-label">Trung tâm</label>
          <multiselect name="search_branch" placeholder="Chọn trung tâm" v-model="searchData.arr_branch"
            :options="branch_list" label="name" :close-on-select="false" :hide-selected="true"
            :multiple="true" :searchable="true" track-by="id"
            selectedLabel="" selectLabel="" deselectLabel="">
            <span slot="noResult">Không tìm thấy</span>
          </multiselect>
        </div>
        <div>
          <label class="rpt-label">Năm học</label>
          <vs-select v-model="searchData.school_year" class="w-full">
            <vs-select-item v-for="y in yearOptions" :key="y" :value="String(y)" :text="'Năm ' + y" />
          </vs-select>
        </div>
      </div>

      <div class="rpt-actions mb-5">
        <vs-button class="rpt-btn" @click="getData"><i class="fa fa-search"></i> Tìm kiếm</vs-button>
        <vs-button color="dark" type="border" class="rpt-btn" @click="reset"><i class="fas fa-undo-alt"></i> Hủy</vs-button>
        <vs-button color="success" class="rpt-btn" @click="exportExcel"><i class="fa fa-file-excel"></i> Xuất Excel</vs-button>
      </div>

      <!-- KPI Table -->
      <div class="rpt-table-wrap" v-if="rows.length > 0">
        <table class="rpt-table">
          <thead>
            <tr>
              <th class="th-label">Chỉ số</th>
              <th class="th-num">Giá trị</th>
              <th class="th-num">Giá trị khóa học sau tách</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(row, idx) in rows" :key="idx" class="rpt-row"
              :class="{ 'row-bold': row.is_bold, 'row-even': idx % 2 === 1 && !row.is_bold }">
              <td class="td-label">{{ row.label }}</td>
              <td :class="cellClass(row.value, row.is_money)">{{ fmt(row.value, row.is_money) }}</td>
              <td :class="cellClass(row.value_split, row.is_money)">{{ fmt(row.value_split, row.is_money) }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="rpt-empty" v-if="rows.length === 0 && !loading">
        <i class="fas fa-inbox"></i><p>Chưa có dữ liệu · Nhấn Tìm kiếm để tải</p>
      </div>
    </vx-card>
  </div>
</template>

<script>
  import axios from '../../../../http/axios.js'
  import Multiselect from 'vue-multiselect'

  export default {
    components: { Multiselect },
    data() {
      const currentYear = new Date().getFullYear()
      const years = []
      for (let y = currentYear + 1; y >= currentYear - 5; y--) { years.push(y) }
      return {
        branch_list: [], rows: [], loading: false,
        searchData: { arr_branch: [], branch_id: [], school_year: String(currentYear) },
        yearOptions: years,
        displayYear: String(currentYear),
      }
    },
    created() {
      axios.g('/api/system/branches-has-user').then(r => { this.branch_list = r.data })
      this.getData()
    },
    methods: {
      reset() {
        this.searchData = { arr_branch: [], branch_id: [], school_year: String(new Date().getFullYear()) }
        this.getData()
      },
      getData() {
        const branch_ids = []
        if (this.searchData.arr_branch && this.searchData.arr_branch.length) this.searchData.arr_branch.forEach(i => branch_ids.push(i.id))
        this.searchData.branch_id = branch_ids
        this.loading = true
        this.$vs.loading()
        axios.p('/api/lms/reports/21', { branch_id: this.searchData.branch_id, school_year: this.searchData.school_year })
          .then(res => {
            this.$vs.loading.close(); this.loading = false
            this.rows = res.data.rows || []
            this.displayYear = res.data.school_year || String(new Date().getFullYear())
          }).catch(e => { console.error(e); this.$vs.loading.close(); this.loading = false })
      },
      exportExcel() {
        let keys = [], values = []
        if (this.searchData.branch_id && this.searchData.branch_id.length > 0) { keys.push('branch_id'); values.push(this.searchData.branch_id.join('-')) }
        if (this.searchData.school_year) { keys.push('school_year'); values.push(this.searchData.school_year) }
        if (keys.length === 0) { keys.push('k'); values.push('v') }
        window.open(`/api/lms/exports/report21/${keys.join(',')}/${values.join(',')}?token=${localStorage.getItem('accessToken')}`, '_blank')
      },
      /* Kiểm tra giá trị có phải số không (số nguyên/thực, không phải %) */
      isNumeric(v) {
        if (v === null || v === undefined) return false
        const s = String(v)
        if (s.includes('%')) return false
        return !isNaN(parseFloat(s)) && isFinite(+s)
      },
      /* Format hiển thị: số → có dấu phân cách, % → giữ nguyên */
      fmt(v, isMoney) {
        if (v === null || v === undefined || v === '') return '—'
        const s = String(v)
        if (s.includes('%')) return s
        const num = parseFloat(s)
        if (isNaN(num)) return s
        const formatted = num.toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
        return formatted
      },
      /* Class CSS cho ô dữ liệu */
      cellClass(v, isMoney) {
        if (isMoney) return 'td-money'
        return 'td-num'  // số và % đều căn phải
      },
    }
  }
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
#page-report-21 { font-family: 'Inter', sans-serif; }

.rpt-header { display: flex; align-items: center; gap: 16px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white; padding: 20px 24px; border-radius: 12px; box-shadow: 0 4px 20px rgba(79,70,229,.3); }
.rpt-header__icon { font-size: 26px; width: 50px; height: 50px; background: rgba(255,255,255,.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.rpt-header__title { font-size: 1rem; font-weight: 700; margin: 0; }
.rpt-header__sub { font-size: .82rem; opacity: .8; margin: 3px 0 0; }

.rpt-card { border-radius: 12px !important; box-shadow: 0 2px 16px rgba(0,0,0,.06) !important; }
.rpt-filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; }
.rpt-label { display: block; font-size: .75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 6px; }
.rpt-actions { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
.rpt-btn { border-radius: 8px !important; }

/* Table */
.rpt-table-wrap { overflow-x: auto; border-radius: 10px; border: 1px solid #e5e7eb; max-width: 820px; }
.rpt-table { width: 100%; border-collapse: collapse; font-size: .88rem; }
.rpt-table thead tr { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); }
.rpt-table thead th { color: white; font-weight: 600; padding: 12px 16px; white-space: nowrap; border:1px solid rgba(255,255,255,0.2); }

/* Header alignment */
.th-label { text-align: left;  width: 44%; }
.th-num   { text-align: right; width: 28%; }

/* Row */
.rpt-row { border-bottom: 1px solid #f3f4f6; }
.rpt-row td { padding: 11px 16px; vertical-align:middle; border:1px solid #e5e7eb; }
.row-bold td { background: #f0f4ff; font-weight: 700; font-size: .95rem; }
.row-even td { background: #fafaff; }
.rpt-row:hover td { background: #eef2ff; transition: background .12s; }

/* Cell alignment */
.td-label { text-align: left;  font-weight: 500; color: #374151; }
.td-num   { text-align: right; font-weight: 600; color: #1e1b4b; }
.td-money { text-align: right; font-weight: 600; color: #059669; }
.td-text  { text-align: left;  font-weight: 500; color: #374151; }  /* % hoàn thành */

.rpt-empty { text-align: center; padding: 60px 0; color: #9ca3af; }
.rpt-empty i { font-size: 3rem; margin-bottom: 12px; display: block; }
.rpt-empty p { font-size: 1rem; }
.multiselect { z-index: 999; }
</style>
