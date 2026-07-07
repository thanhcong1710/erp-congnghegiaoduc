<template>
  <div id="page-report-27">
    <vx-card no-shadow class="rpt-card">
      <div class="mb-6 flex items-center">
        <div class="mr-3 flex items-center justify-center p-3 rounded-lg" style="background:rgba(79, 70, 229, 0.1); color:#4f46e5;">
          <i class="fas fa-book text-xl"></i>
        </div>
        <div>
          <h3 class="text-lg font-bold uppercase" style="color:#4f46e5; margin:0;">BÁO CÁO PHÁT SÁCH</h3>
        </div>
      </div>
      <div class="rpt-filter-grid mb-5">
        <div>
          <label class="rpt-label">Trung tâm</label>
          <multiselect name="search_branch" placeholder="Chọn trung tâm" v-model="searchData.arr_branch" :options="branch_list" label="name" :close-on-select="false" :hide-selected="true" :multiple="true" :searchable="true" track-by="id" selectedLabel="" selectLabel="" deselectLabel=""><span slot="noResult">Không tìm thấy</span></multiselect>
        </div>
        <div>
          <label class="rpt-label">Sản phẩm</label>
          <multiselect name="search_product" placeholder="Chọn sản phẩm" v-model="searchData.product" :options="products" label="name" :close-on-select="true" :multiple="false" :searchable="true" track-by="id" selectedLabel="" selectLabel="" deselectLabel=""><span slot="noResult">Không tìm thấy</span></multiselect>
        </div>
        <div>
          <label class="rpt-label">Trạng thái phát sách</label>
          <multiselect name="search_status" placeholder="Chọn trạng thái" v-model="searchData.status" :options="status_list" label="label" :close-on-select="true" :multiple="false" :searchable="false" track-by="id" selectedLabel="" selectLabel="" deselectLabel=""><span slot="noResult">Không tìm thấy</span></multiselect>
        </div>
        <div>
          <label class="rpt-label">Từ khóa</label>
          <vs-input class="w-full" placeholder="Mã HV, tên HV, mã lớp" v-model="searchData.keyword"></vs-input>
        </div>
        <div>
          <label class="rpt-label">Ngày phát sách</label>
          <date-picker style="width:100%" v-model="searchData.dateRange" type="date" range :clearable="true" format="YYYY-MM-DD" :lang="datepickerOptions.lang" placeholder="Từ ngày — Đến ngày"></date-picker>
        </div>
      </div>
      <div class="rpt-actions mb-5">
        <vs-button class="rpt-btn" @click="getData"><i class="fa fa-search"></i> Tìm kiếm</vs-button>
        <vs-button color="dark" type="border" class="rpt-btn" @click="reset"><i class="fas fa-undo-alt"></i> Hủy</vs-button>
        <vs-button color="success" class="rpt-btn" @click="exportExcel"><i class="fa fa-file-excel"></i> Xuất Excel</vs-button>
        <span class="rpt-badge-count">{{ pagination.total }} bản ghi</span>
      </div>

      <div class="mb-4 flex items-end gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200" v-if="selected_items.length > 0">
        <div>
          <label class="rpt-label">Ngày phát sách cho {{ selected_items.length }} học viên đã chọn</label>
          <date-picker style="width:250px" v-model="batch_date" type="date" format="YYYY-MM-DD" :lang="datepickerOptions.lang" placeholder="Chọn ngày phát sách"></date-picker>
        </div>
        <vs-button color="primary" class="rpt-btn" @click="updateBatchDate"><i class="fas fa-save"></i> Cập nhật</vs-button>
      </div>

      <div class="rpt-table-wrap">
        <table class="rpt-table">
          <thead>
            <tr>
              <th style="width:40px" class="text-center">
                <vs-checkbox v-model="selectAll" @input="toggleSelectAll"></vs-checkbox>
              </th>
              <th style="width:50px" class="text-center">STT</th>
              <th>Trung tâm</th>
              <th>Mã HV</th>
              <th>Họ tên</th>
              <th>Mã lớp</th>
              <th>Sản phẩm</th>
              <th style="width:200px">Ngày phát sách</th>
            </tr>
          </thead>
          <tbody>
            <tr class="rpt-row" v-for="(item, index) in datas" :key="index">
              <td class="text-center">
                <vs-checkbox v-model="selected_items" :vs-value="item.contract_id"></vs-checkbox>
              </td>
              <td class="text-center text-muted">{{ index + 1 + (pagination.cpage - 1) * pagination.limit }}</td>
              <td class="text-muted small">{{ item.branch_name }}</td>
              <td><span class="badge-code">{{ item.lms_code }}</span></td>
              <td class="font-bold">{{ item.student_name }}</td>
              <td><span class="badge-code">{{ item.cls_name }}</span></td>
              <td>{{ item.product_name }}</td>
              <td>
                <div class="flex items-center gap-2">
                  <date-picker style="width:140px" v-model="item.book_delivered_date" type="date" format="YYYY-MM-DD" :lang="datepickerOptions.lang" placeholder="Chọn ngày" @change="updateSingleDate(item)"></date-picker>
                </div>
              </td>
            </tr>
            <tr v-if="datas.length === 0">
              <td colspan="8" class="text-center py-4 text-muted">Không có dữ liệu</td>
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

  export default {
    components: { vSelect, Multiselect, DatePicker },
    data() {
      return {
        branch_list: [], products: [],
        status_list: [ {id:'1',label:'Đã phát sách'}, {id:'0',label:'Chưa phát sách'} ],
        searchData: { arr_branch:'', branch_id:'', keyword:'', dateRange:'', product:'', status:'' },
        datepickerOptions: { lang: { days:['CN','T2','T3','T4','T5','T6','T7'], months:['Tháng 1','Tháng 2','Tháng 3','Tháng 4','Tháng 5','Tháng 6','Tháng 7','Tháng 8','Tháng 9','Tháng 10','Tháng 11','Tháng 12'] } },
        datas: [],
        limitSource: [20, 50, 100, 500],
        pagination: { cpage:1, total:0, limit:20, init:0 },
        selected_items: [],
        selectAll: false,
        batch_date: ''
      }
    },
    created() {
      axios.g('/api/system/branches-has-user').then(r => { this.branch_list = r.data })
      axios.g('/api/system/products').then(r => { this.products = r.data })
      this.getData()
    },
    watch: {
      selected_items(val) {
        if (val.length > 0 && val.length === this.datas.length) {
          this.selectAll = true
        } else {
          this.selectAll = false
        }
      }
    },
    methods: {
      toggleSelectAll(val) {
        if (val) {
          this.selected_items = this.datas.map(i => i.contract_id)
        } else {
          this.selected_items = []
        }
      },
      fmtDate(d) {
        if (!d) return ''
        if (typeof d === 'string' && d.length === 10) return d
        if (d instanceof Date) {
          const y = d.getFullYear(), m = String(d.getMonth() + 1).padStart(2,'0'), day = String(d.getDate()).padStart(2,'0')
          return `${y}-${m}-${day}`
        }
        return d
      },
      reset() {
        this.searchData = { arr_branch:'', branch_id:'', keyword:'', dateRange:'', product:'', status:'' }
        this.getData()
      },
      getData() {
        this.selected_items = []
        this.selectAll = false
        const ids = []
        if (this.searchData.arr_branch && this.searchData.arr_branch.length) this.searchData.arr_branch.forEach(i => ids.push(i.id))
        this.searchData.branch_id = ids
        const start_date = this.searchData.dateRange && this.searchData.dateRange[0] ? this.fmtDate(this.searchData.dateRange[0]) : ''
        const end_date = this.searchData.dateRange && this.searchData.dateRange[1] ? this.fmtDate(this.searchData.dateRange[1]) : ''
        
        const data = { 
          keyword: this.searchData.keyword, 
          branch_id: this.searchData.branch_id, 
          product_id: this.searchData.product ? this.searchData.product.id : '', 
          status: this.searchData.status ? this.searchData.status.id : '', 
          start_date: start_date, 
          end_date: end_date, 
          pagination: this.pagination 
        }
        this.$vs.loading()
        axios.p('/api/lms/reports/book-delivered', data).then(res => { 
          this.$vs.loading.close()
          this.datas = res.data.list.map(item => {
            return {
              ...item,
              book_delivered_date: item.book_delivered_date ? new Date(item.book_delivered_date) : ''
            }
          })
          this.pagination = res.data.paging
          setTimeout(() => { this.pagination.init = 1 }, 500) 
        }).catch(e => { console.error(e); this.$vs.loading.close() })
      },
      changePage() { if (this.pagination.init) this.getData() },
      changePageLimit(limit) { this.pagination.cpage = 1; this.pagination.limit = limit; this.getData() },
      
      updateSingleDate(item) {
        if (!item.book_delivered_date) return;
        const date_str = this.fmtDate(item.book_delivered_date);
        this.$vs.loading();
        axios.p('/api/lms/reports/update-book-delivered-date', {
          contract_ids: [item.contract_id],
          book_delivered_date: date_str
        }).then(res => {
          this.$vs.loading.close();
          this.$vs.notify({ title: 'Thành công', text: 'Cập nhật ngày phát sách thành công', color: 'success' });
        }).catch(err => {
          this.$vs.loading.close();
          this.$vs.notify({ title: 'Lỗi', text: 'Có lỗi xảy ra', color: 'danger' });
        });
      },
      
      updateBatchDate() {
        if (!this.batch_date) {
          this.$vs.notify({ title: 'Lỗi', text: 'Vui lòng chọn ngày phát sách', color: 'warning' });
          return;
        }
        if (this.selected_items.length === 0) return;
        
        const date_str = this.fmtDate(this.batch_date);
        this.$vs.loading();
        axios.p('/api/lms/reports/update-book-delivered-date', {
          contract_ids: this.selected_items,
          book_delivered_date: date_str
        }).then(res => {
          this.$vs.loading.close();
          this.$vs.notify({ title: 'Thành công', text: `Cập nhật ngày phát sách cho ${this.selected_items.length} học viên thành công`, color: 'success' });
          this.getData(); // reload data
        }).catch(err => {
          this.$vs.loading.close();
          this.$vs.notify({ title: 'Lỗi', text: 'Có lỗi xảy ra', color: 'danger' });
        });
      },
      
      exportExcel() {
        let keys = [], values = []
        const ids = []
        if (this.searchData.arr_branch && this.searchData.arr_branch.length) this.searchData.arr_branch.forEach(i => ids.push(i.id))
        if (ids.length) { keys.push('branch_id'); values.push(ids.join('-')) }
        if (this.searchData.keyword) { keys.push('keyword'); values.push(this.searchData.keyword) }
        if (this.searchData.product) { keys.push('product_id'); values.push(this.searchData.product.id) }
        if (this.searchData.status) { keys.push('status'); values.push(this.searchData.status.id) }
        if (this.searchData.dateRange && this.searchData.dateRange[0]) {
          keys.push('start_date')
          values.push(this.fmtDate(this.searchData.dateRange[0]))
        }
        if (this.searchData.dateRange && this.searchData.dateRange[1]) {
          keys.push('end_date')
          values.push(this.fmtDate(this.searchData.dateRange[1]))
        }
        if (keys.length === 0) { keys.push('k'); values.push('v') }
        window.open(`/api/lms/exports/book-delivered/${keys.join(',')}/${values.join(',')}?token=${localStorage.getItem('accessToken')}`, '_blank')
      }
    }
  }
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
#page-report-27 { font-family: 'Inter', sans-serif; }
.rpt-header { display:flex; align-items:center; gap:16px; background:linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%); color:white; padding:20px 24px; border-radius:12px; box-shadow:0 4px 20px rgba(79,70,229,.3); margin-bottom:20px; }
.rpt-header__icon { font-size:26px; width:50px; height:50px; background:rgba(255,255,255,.2); border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.rpt-header__title { font-size:1.05rem; font-weight:700; margin:0; }
.rpt-header__sub { font-size:.82rem; opacity:.8; margin:3px 0 0; }
.rpt-card { border-radius:12px !important; box-shadow:0 2px 16px rgba(0,0,0,.06) !important; }
.rpt-filter-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:16px; }
.rpt-label { display:block; font-size:.75rem; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.05em; margin-bottom:6px; }
.rpt-actions { display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
.rpt-btn { border-radius:8px !important; }
.rpt-badge-count { margin-left:auto; background:#eef2ff; color:#4338ca; padding:6px 14px; border-radius:20px; font-weight:600; font-size:.85rem; }
.rpt-table-wrap { overflow-x:auto; border-radius:10px; border:1px solid #e5e7eb; }
.rpt-table { width:100%; border-collapse:collapse; font-size:.84rem; }
.rpt-table thead tr { background:linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%); }
.rpt-table thead th { color:white; font-weight:600; padding:11px 10px; white-space:nowrap; border:1px solid rgba(255,255,255,0.2); }
.rpt-row { border-bottom:1px solid #f3f4f6; transition:background .15s; }
.rpt-row:hover { background:#f8f7ff; }
.rpt-row td { padding:9px 10px; vertical-align:middle; border:1px solid #e5e7eb; }
.badge-code { background:#eef2ff; color:#4338ca; border-radius:6px; padding:2px 8px; font-size:.78rem; font-weight:600; }
.status-badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:.78rem; font-weight:600; }
.text-muted { color:#9ca3af; }
.small { font-size:.8rem; }
.rpt-paging { display:flex; align-items:center; flex-wrap:wrap; margin-top:16px; }
.paging-limit-btn { display:flex; align-items:center; gap:8px; padding:8px 14px; border:1px solid #e5e7eb; border-radius:8px; cursor:pointer; background:white; font-size:.85rem; font-weight:500; }
.multiselect { z-index:999; }
.flex { display: flex; }
.items-center { align-items: center; }
.gap-2 { gap: 0.5rem; }
.gap-4 { gap: 1rem; }
.bg-gray-50 { background-color: #f9fafb; }
.p-4 { padding: 1rem; }
.rounded-lg { border-radius: 0.5rem; }
.border { border-width: 1px; }
.border-gray-200 { border-color: #e5e7eb; }
.font-bold { font-weight: bold; }
</style>