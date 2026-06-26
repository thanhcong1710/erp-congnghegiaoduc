<template>
  <div id="page-report-20">
    <div class="rpt-header mb-5">
      <div class="rpt-header__icon"><i class="fas fa-chart-bar"></i></div>
      <div>
        <h3 class="rpt-header__title">DOANH SỐ CHI TIẾT THEO TỪNG KHÓA HỌC</h3>
        <p class="rpt-header__sub">Phân loại Mới &amp; Up Level theo ngày thu đủ học phí</p>
      </div>
    </div>

    <vx-card no-shadow class="rpt-card">
      <div class="rpt-filter-grid mb-5">
        <div>
          <label class="rpt-label">Trung tâm</label>
          <multiselect name="search_branch" placeholder="Chọn trung tâm" v-model="searchData.arr_branch"
            :options="branch_list" label="name" :close-on-select="false" :hide-selected="true"
            :multiple="true" :searchable="true" track-by="id"
            selectedLabel="" selectLabel="" deselectLabel="">
            <span slot="noResult">Không tìm thấy</span>
          </multiselect>
        </div>
        <div>
          <label class="rpt-label">Thời gian full fee</label>
          <date-picker v-model="searchData.dateRange" format="YYYY-MM-DD" style="width:100%"
            type="date" range :clearable="true" :lang="datepickerOptions.lang"
            placeholder="Từ ngày — Đến ngày">
          </date-picker>
        </div>
      </div>

      <div class="rpt-actions mb-5">
        <vs-button class="rpt-btn" @click="getData"><i class="fa fa-search"></i> Tìm kiếm</vs-button>
        <vs-button color="dark" type="border" class="rpt-btn" @click="reset"><i class="fas fa-undo-alt"></i> Hủy</vs-button>
        <vs-button color="success" class="rpt-btn" @click="exportExcel"><i class="fa fa-file-excel"></i> Xuất Excel</vs-button>
      </div>

      <!-- Summary -->
      <div class="rpt-summary mb-5" v-if="rows.length > 0">
        <div class="rpt-pill rpt-pill--indigo">
          <span class="rpt-pill__label">Tổng khóa</span>
          <span class="rpt-pill__value">{{ rows.length }}</span>
        </div>
        <div class="rpt-pill rpt-pill--green">
          <span class="rpt-pill__label">🆕 Mới</span>
          <span class="rpt-pill__value">{{ total_new | formatNumber }}</span>
        </div>
        <div class="rpt-pill rpt-pill--orange">
          <span class="rpt-pill__label">⬆️ Up Level</span>
          <span class="rpt-pill__value">{{ total_uplevel | formatNumber }}</span>
        </div>
        <div class="rpt-pill rpt-pill--violet">
          <span class="rpt-pill__label">Tổng đơn</span>
          <span class="rpt-pill__value">{{ total_agreements | formatNumber }}</span>
        </div>
      </div>

      <!-- Table -->
      <div class="rpt-table-wrap">
        <table class="rpt-table">
          <thead>
            <tr>
              <th style="width:44px" class="text-center">STT</th>
              <th>Khóa học đăng ký</th>
              <th style="width:140px" class="text-center">Số đơn sau tách</th>
              <th style="width:180px" class="text-right">Giá khóa học (VNĐ)</th>
              <th style="width:100px" class="text-center">🆕 Mới</th>
              <th style="width:110px" class="text-center">⬆️ Up Level</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(row, idx) in rows" :key="idx" class="rpt-row">
              <td class="text-center text-muted">{{ idx + 1 }}</td>
              <td class="font-medium">{{ row.tuition_fee_name || '—' }}</td>
              <td class="text-center">{{ row.so_don_sau_tach }}</td>
              <td class="text-right money-cell">{{ row.gia_khoa_hoc | formatMoney }}</td>
              <td class="text-center">
                <span class="badge-new">{{ row.count_new | formatNumber }}</span>
              </td>
              <td class="text-center">
                <span class="badge-uplevel">{{ row.count_uplevel | formatNumber }}</span>
              </td>
            </tr>
            <tr class="rpt-total" v-if="rows.length > 0">
              <td colspan="4" class="text-right">TỔNG CỘNG</td>
              <td class="text-center">{{ total_new | formatNumber }}</td>
              <td class="text-center">{{ total_uplevel | formatNumber }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="rpt-empty" v-if="rows.length === 0 && !loading">
        <i class="fas fa-inbox"></i><p>Không có dữ liệu</p>
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
        branch_list: [], rows: [], total_agreements: 0, total_new: 0, total_uplevel: 0, loading: false,
        searchData: { arr_branch: '', branch_id: [], dateRange: '' },
        datepickerOptions: { lang: { days: ['CN','T2','T3','T4','T5','T6','T7'], months: ['Tháng 1','Tháng 2','Tháng 3','Tháng 4','Tháng 5','Tháng 6','Tháng 7','Tháng 8','Tháng 9','Tháng 10','Tháng 11','Tháng 12'] } },
      }
    },
    created() {
      axios.g('/api/system/branches-has-user').then(r => { this.branch_list = r.data })
      this.getData()
    },
    methods: {
      reset() { this.searchData = { arr_branch: '', branch_id: [], dateRange: '' }; this.getData() },
      getData() {
        const branch_ids = []
        if (this.searchData.arr_branch && this.searchData.arr_branch.length) this.searchData.arr_branch.forEach(i => branch_ids.push(i.id))
        this.searchData.branch_id = branch_ids
        let start_date = '', end_date = ''
        if (this.searchData.dateRange && this.searchData.dateRange.length === 2) {
          start_date = u.dateToString(this.searchData.dateRange[0])
          end_date   = u.dateToString(this.searchData.dateRange[1])
        }
        this.loading = true
        this.$vs.loading()
        axios.p('/api/lms/reports/20', { branch_id: this.searchData.branch_id, start_date, end_date })
          .then(res => {
            this.$vs.loading.close(); this.loading = false
            this.rows             = res.data.list || []
            this.total_agreements = res.data.total_agreements || 0
            this.total_new        = res.data.total_new || 0
            this.total_uplevel    = res.data.total_uplevel || 0
          }).catch(e => { console.error(e); this.$vs.loading.close(); this.loading = false })
      },
      exportExcel() {
        let keys = [], values = []
        if (this.searchData.branch_id && this.searchData.branch_id.length > 0) { keys.push('branch_id'); values.push(this.searchData.branch_id.join('-')) }
        if (this.searchData.dateRange && this.searchData.dateRange.length === 2) {
          keys.push('start_date'); values.push(u.dateToString(this.searchData.dateRange[0]))
          keys.push('end_date');   values.push(u.dateToString(this.searchData.dateRange[1]))
        }
        if (keys.length === 0) { keys.push('k'); values.push('v') }
        window.open(`/api/lms/exports/report20/${keys.join(',')}/${values.join(',')}?token=${localStorage.getItem('accessToken')}`, '_blank')
      }
    },
    filters: {
      formatMoney(v) { if (!v) return '0'; return Number(v).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,') },
      formatNumber(v) { if (!v) return '0'; return Number(v).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,') }
    }
  }
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
#page-report-20 { font-family: 'Inter', sans-serif; }

.rpt-header { display: flex; align-items: center; gap: 16px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white; padding: 20px 24px; border-radius: 12px; box-shadow: 0 4px 20px rgba(79,70,229,.3); }
.rpt-header__icon { font-size: 26px; width: 50px; height: 50px; background: rgba(255,255,255,.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.rpt-header__title { font-size: 1.05rem; font-weight: 700; margin: 0; }
.rpt-header__sub { font-size: .82rem; opacity: .8; margin: 3px 0 0; }

.rpt-card { border-radius: 12px !important; box-shadow: 0 2px 16px rgba(0,0,0,.06) !important; }
.rpt-filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; }
.rpt-label { display: block; font-size: .75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 6px; }
.rpt-actions { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
.rpt-btn { border-radius: 8px !important; }

.rpt-summary { display: flex; gap: 12px; flex-wrap: wrap; }
.rpt-pill { padding: 12px 18px; border-radius: 10px; display: flex; flex-direction: column; gap: 4px; min-width: 130px; }
.rpt-pill__label { font-size: .7rem; font-weight: 600; text-transform: uppercase; opacity: .7; }
.rpt-pill__value { font-size: 1rem; font-weight: 700; }
.rpt-pill--indigo { background: #eef2ff; color: #4338ca; }
.rpt-pill--green  { background: #d1fae5; color: #065f46; }
.rpt-pill--orange { background: #fef3c7; color: #92400e; }
.rpt-pill--violet { background: #f5f3ff; color: #6d28d9; }

.rpt-table-wrap { overflow-x: auto; border-radius: 10px; border: 1px solid #e5e7eb; }
.rpt-table { width: 100%; border-collapse: collapse; font-size: .85rem; }
.rpt-table thead tr { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); }
.rpt-table thead th { color: white; font-weight: 600; padding: 11px 12px; white-space: nowrap; border:1px solid rgba(255,255,255,0.2); }
.rpt-row { border-bottom: 1px solid #f3f4f6; transition: background .15s; }
.rpt-row:hover { background: #f8f7ff; }
.rpt-row td { padding: 10px 12px; vertical-align:middle; border:1px solid #e5e7eb; }

.font-medium { font-weight: 500; }
.text-muted   { color: #9ca3af; }
.money-cell   { font-weight: 700; color: #4338ca; }

.badge-new     { display: inline-block; background: #d1fae5; color: #065f46; border-radius: 20px; padding: 3px 16px; font-weight: 700; font-size: .9rem; }
.badge-uplevel { display: inline-block; background: #fef3c7; color: #92400e; border-radius: 20px; padding: 3px 16px; font-weight: 700; font-size: .9rem; }

.rpt-total td { background: #eef2ff; font-weight: 700; padding: 11px 12px; font-size: .88rem; border-top: 2px solid #4f46e5; color: #1e1b4b; }

.rpt-empty { text-align: center; padding: 60px 0; color: #9ca3af; }
.rpt-empty i { font-size: 3rem; margin-bottom: 12px; display: block; }
.rpt-empty p { font-size: 1rem; }
.multiselect { z-index: 999; }
</style>
