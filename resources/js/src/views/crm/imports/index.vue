<template>
  <div id="crm-import-list" class="imp">
    <!-- KPI -->
    <div class="imp-kpis">
      <div class="imp-kpi">
        <div class="imp-kpi__icon" style="background:#ede9fe;color:#7367f0"><i class="fas fa-file-excel"></i></div>
        <div><div class="imp-kpi__val">{{ fmt(summary.total_import) }}</div><div class="imp-kpi__lbl">Lượt import của tôi</div></div>
      </div>
      <div class="imp-kpi">
        <div class="imp-kpi__icon" style="background:#dcfce7;color:#16a34a"><i class="fas fa-user-check"></i></div>
        <div><div class="imp-kpi__val">{{ fmt(summary.total_customer) }}</div><div class="imp-kpi__lbl">Khách hàng đã nhập thành công</div></div>
      </div>
      <div class="imp-kpi imp-kpi--click" @click="quickPending">
        <div class="imp-kpi__icon" style="background:#fef3c7;color:#d97706"><i class="fas fa-hourglass-half"></i></div>
        <div><div class="imp-kpi__val">{{ fmt(summary.total_pending) }}</div><div class="imp-kpi__lbl">Chờ phân chia</div></div>
      </div>
      <router-link to="/crm/imports/add" class="imp-kpi imp-kpi--cta">
        <div class="imp-kpi__icon"><i class="fas fa-cloud-upload-alt"></i></div>
        <div><div class="imp-kpi__val" style="font-size:17px">Import mới</div><div class="imp-kpi__lbl">Tải file Excel khách hàng</div></div>
      </router-link>
    </div>

    <vx-card no-shadow class="mt-5">
      <div class="imp-filter">
        <div class="imp-field imp-field--grow">
          <label>Tên file</label>
          <input class="imp-input" v-model.trim="searchData.keyword" placeholder="Nhập tên file..." @keyup.enter="search" />
        </div>
        <div class="imp-field">
          <label>Trạng thái</label>
          <v-select :clearable="false" :searchable="false" :options="statusOptions" v-model="searchData.status" @input="search" />
        </div>
        <div class="imp-field">
          <label>Từ ngày</label>
          <input type="date" class="imp-input" v-model="searchData.from_date" @change="search" />
        </div>
        <div class="imp-field">
          <label>Đến ngày</label>
          <input type="date" class="imp-input" v-model="searchData.to_date" @change="search" />
        </div>
        <div class="imp-field imp-field--btn">
          <vs-button @click="search"><i class="fa fa-search"></i> Tìm kiếm</vs-button>
          <vs-button color="dark" type="border" @click="reset"><i class="fas fa-undo-alt"></i> Bỏ lọc</vs-button>
        </div>
      </div>
      <p class="imp-note"><i class="fas fa-lock"></i> Danh sách chỉ hiển thị các lượt import do chính bạn thực hiện.</p>

      <div class="imp-table-wrap">
        <table class="imp-table">
          <thead>
            <tr>
              <th style="width:56px">STT</th>
              <th>Tên file</th>
              <th>Trung tâm</th>
              <th>Thời gian import</th>
              <th class="tc">Tổng dòng</th>
              <th class="tc">Thành công</th>
              <th class="tc">Lỗi / bỏ qua</th>
              <th class="tc">Trạng thái</th>
              <th class="tc">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in imports" :key="item.id">
              <td>{{ index + 1 + (pagination.cpage - 1) * pagination.limit }}</td>
              <td class="imp-file">
                <i class="fas fa-file-excel" style="color:#16a34a"></i>
                <a :href="fileUrl(item.file_link)" target="_blank" rel="noopener" :title="item.file_name">{{ item.file_name }}</a>
              </td>
              <td class="nowrap" style="min-width:220px">{{ item.branch_name || '—' }}</td>
              <td class="nowrap">{{ item.created_at | dt }}</td>
              <td class="tc">{{ fmt(item.count_total) }}</td>
              <td class="tc"><b style="color:#16a34a">{{ item.status == 1 ? fmt(item.count_success) : '—' }}</b></td>
              <td class="tc"><b style="color:#dc2626">{{ item.status == 1 ? fmt(item.count_error) : fmt(item.count_total - item.count_valid - item.count_overwrite) }}</b></td>
              <td class="tc">
                <span v-if="item.status == 1" class="imp-badge imp-badge--ok"><i class="fas fa-check-circle"></i> Hoàn thành</span>
                <span v-else class="imp-badge imp-badge--wait"><i class="fas fa-hourglass-half"></i> Chờ phân chia</span>
              </td>
              <td class="tc nowrap">
                <router-link v-if="item.status != 1" :to="`/crm/imports/add?import_id=${item.id}`" class="imp-act imp-act--primary" title="Tiếp tục phân chia">
                  <i class="fas fa-play"></i> Tiếp tục
                </router-link>
                <span class="imp-act" title="Xem chi tiết" @click="openDetail(item)"><i class="fas fa-eye"></i></span>
                <span class="imp-act" title="Tải kết quả import" @click="exportExcel(item)"><i class="fas fa-download"></i></span>
              </td>
            </tr>
            <tr v-if="!loading && !imports.length">
              <td colspan="9" class="imp-empty">
                <i class="fas fa-inbox"></i>
                <div>Chưa có lượt import nào</div>
                <router-link to="/crm/imports/add">Import khách hàng ngay</router-link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex flex-wrap items-center mt-5">
        <vs-dropdown vs-trigger-click class="cursor-pointer mr-4 items-per-page-handler">
          <div class="p-3 border border-solid d-theme-border-grey-light rounded-full d-theme-dark-bg cursor-pointer flex items-center justify-between font-medium" style="font-size:13px">
            <span class="mr-2">{{ pagination.total ? (pagination.cpage - 1) * pagination.limit + 1 : 0 }} - {{ Math.min(pagination.cpage * pagination.limit, pagination.total) }} / {{ pagination.total }}</span>
            <feather-icon icon="ChevronDownIcon" svgClasses="h-4 w-4" />
          </div>
          <vs-dropdown-menu>
            <vs-dropdown-item v-for="(item, index) in limitSource" :key="index" @click="changePageLimit(item)">
              <span>{{ item }}</span>
            </vs-dropdown-item>
          </vs-dropdown-menu>
        </vs-dropdown>
        <vs-pagination
          style="width: calc(100% - 160px);"
          v-if="Math.ceil(pagination.total / pagination.limit) > 1"
          :total="Math.ceil(pagination.total / pagination.limit)"
          :max="7"
          v-model="pagination.cpage" @change="changePage()" />
      </div>
    </vx-card>

    <!-- Chi tiết lượt import -->
    <vs-popup class="imp-popup" :title="detail.import ? 'Chi tiết: ' + detail.import.file_name : 'Chi tiết import'" :active.sync="detailOpen">
      <div v-if="detail.import">
        <div class="imp-chips">
          <span v-for="t in detailTabs" :key="t.key" :class="['imp-chip', detailTab === t.key && 'is-active']" @click="detailTab = t.key">
            {{ t.label }} <b>{{ fmt(t.count) }}</b>
          </span>
        </div>
        <div class="imp-table-wrap" style="max-height:60vh">
          <table class="imp-table">
            <thead>
              <tr><th>STT</th><th>Tên phụ huynh</th><th>SĐT</th><th>Học sinh</th><th class="tc">Kết quả</th><th>Ghi chú</th></tr>
            </thead>
            <tbody>
              <tr v-for="(r, i) in detailRows" :key="r.id">
                <td>{{ i + 1 }}</td>
                <td class="nowrap">{{ r.name || '—' }}</td>
                <td class="nowrap">{{ r.gud_mobile1 }}<span v-if="r.gud_mobile2" class="imp-sub"><br>{{ r.gud_mobile2 }}</span></td>
                <td>{{ [r.student_name_1, r.student_name_2].filter(Boolean).join(', ') || '—' }}</td>
                <td class="tc"><span :class="['imp-badge', rowBadge(r).cls]">{{ rowBadge(r).label }}</span></td>
                <td class="imp-msg">{{ r.status == 6 ? '' : r.error_message }}</td>
              </tr>
              <tr v-if="!detailRows.length"><td colspan="6" class="imp-empty">Không có dữ liệu</td></tr>
            </tbody>
          </table>
        </div>
        <div class="flex justify-end mt-4">
          <vs-button color="success" type="border" class="mr-3" @click="exportExcel(detail.import)"><i class="fas fa-download"></i> Tải file kết quả</vs-button>
          <vs-button v-if="detail.import.status != 1" @click="detailOpen = false; $router.push(`/crm/imports/add?import_id=${detail.import.id}`)"><i class="fas fa-play"></i> Tiếp tục phân chia</vs-button>
        </div>
      </div>
    </vs-popup>
  </div>
</template>

<script>
  import vSelect from 'vue-select'
  import axios from '../../../http/axios.js'
  import { importRowBadge } from './importHelper.js'

  const STATUS_ALL = { label: 'Tất cả trạng thái', value: '-1' }

  export default {
    components: { vSelect },
    data() {
      return {
        statusOptions: [
          STATUS_ALL,
          { label: 'Chờ phân chia', value: '0' },
          { label: 'Hoàn thành', value: '1' },
        ],
        searchData: { keyword: '', status: STATUS_ALL, from_date: '', to_date: '' },
        summary: {},
        imports: [],
        loading: false,
        limitSource: [10, 20, 30, 50, 100],
        pagination: { cpage: 1, total: 0, limit: 20, init: 0 },
        detailOpen: false,
        detail: {},
        detailTab: 'all',
      }
    },
    computed: {
      detailTabs() {
        const d = this.detail || {}
        return [
          { key: 'all', label: 'Tất cả', count: d.total || 0 },
          { key: 'ok', label: d.import && d.import.status == 1 ? 'Đã nhập' : 'Hợp lệ', count: (d.total_success || 0) + (d.total_validate || 0) },
          { key: 'overwrite', label: 'Có thể ghi đè', count: d.total_open_lock || 0 },
          { key: 'error', label: 'Lỗi / bỏ qua', count: d.total_error || 0 },
        ]
      },
      detailRows() {
        const rows = (this.detail && this.detail.data) || []
        if (this.detailTab === 'all') return rows
        return rows.filter(r => this.rowBadge(r).group === this.detailTab)
      },
    },
    methods: {
      rowBadge: importRowBadge,
      fmt(n) { return Number(n || 0).toLocaleString('vi-VN') },
      // file_link lưu dạng 'static/upload/x.xlsx' (không có '/') => phải tính từ gốc domain
      fileUrl(link) {
        if (!link) return 'javascript:void(0)'
        if (/^https?:\/\//.test(link)) return link
        return window.location.origin + '/' + link.replace(/^\/+/, '')
      },
      reset() {
        this.searchData = { keyword: '', status: STATUS_ALL, from_date: '', to_date: '' }
        this.search()
      },
      quickPending() {
        this.searchData.status = this.statusOptions[1]
        this.search()
      },
      search() {
        this.pagination.cpage = 1
        this.getData()
      },
      getData() {
        this.loading = true
        this.$vs.loading()
        axios.p('/api/crm/imports/list', {
          keyword: this.searchData.keyword,
          status: this.searchData.status.value,
          from_date: this.searchData.from_date,
          to_date: this.searchData.to_date,
          pagination: this.pagination,
        })
          .then((response) => {
            this.imports = response.data.list || []
            this.summary = response.data.summary || {}
            this.pagination = Object.assign({}, this.pagination, response.data.paging, { init: 0 })
            setTimeout(() => { this.pagination.init = 1 }, 300)
          })
          .catch((error) => console.log(error))
          .finally(() => {
            this.loading = false
            this.$vs.loading.close()
          })
      },
      changePage() {
        if (this.pagination.init) this.getData()
      },
      changePageLimit(limit) {
        this.pagination.cpage = 1
        this.pagination.limit = limit
        this.getData()
      },
      openDetail(item) {
        this.$vs.loading()
        axios.p('/api/crm/imports/detail', { import_id: item.id })
          .then((res) => {
            if (res.data.error) {
              this.$vs.notify({ color: 'danger', title: 'Lỗi', text: res.data.message })
              return
            }
            this.detail = res.data
            this.detailTab = 'all'
            this.detailOpen = true
          })
          .finally(() => this.$vs.loading.close())
      },
      exportExcel(item) {
        window.open(`/api/export/import/${item.id}?token=${localStorage.getItem('accessToken')}`, '_blank')
      },
    },
    created() {
      this.getData()
    },
    filters: {
      dt(v) {
        if (!v) return ''
        const [d, t] = String(v).split(' ')
        const [y, m, day] = d.split('-')
        return `${day}/${m}/${y} ${(t || '').slice(0, 5)}`
      },
    },
  }
</script>

<style>
.imp { font-size: 14px; color: #1e293b; }
.imp .nowrap { white-space: nowrap !important; }
.imp .tc { text-align: center; }
.imp-kpis { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; }
@media (max-width: 1100px) { .imp-kpis { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
@media (max-width: 600px) { .imp-kpis { grid-template-columns: 1fr; } }
.imp-kpi { display: flex; align-items: center; gap: 14px; background: #fff; border-radius: 10px; padding: 16px 18px; box-shadow: 0 1px 3px rgba(15, 23, 42, .06); border: 1px solid #eef2f7; text-decoration: none; color: inherit; }
.imp-kpi--click { cursor: pointer; transition: box-shadow .15s; }
.imp-kpi--click:hover { box-shadow: 0 4px 14px rgba(115, 103, 240, .18); }
.imp-kpi--cta { background: linear-gradient(135deg, #7367f0, #9e95f5); color: #fff !important; border: 0; cursor: pointer; transition: transform .15s; }
.imp-kpi--cta:hover { transform: translateY(-2px); }
.imp-kpi--cta .imp-kpi__icon { background: rgba(255, 255, 255, .22); color: #fff; }
.imp-kpi--cta .imp-kpi__lbl { color: rgba(255, 255, 255, .9); }
.imp-kpi__icon { width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
.imp-kpi__val { font-size: 22px; font-weight: 700; line-height: 1.2; }
.imp-kpi__lbl { font-size: 13px; color: #64748b; }

.imp-filter { display: flex; flex-wrap: wrap; gap: 14px; align-items: flex-end; }
.imp-field { display: flex; flex-direction: column; min-width: 190px; }
.imp-field--grow { flex: 1 1 240px; }
.imp-field--btn { flex-direction: row; gap: 8px; min-width: 0; }
.imp-field > label { font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 5px; }
.imp-input { height: 40px; padding: 0 12px; font-size: 14px; background: #fff; border: 1.5px solid #cbd5e1; border-radius: 6px; box-shadow: 0 1px 3px rgba(0, 0, 0, .05); outline: none; color: #1e293b; }
.imp-input:hover, .imp-input:focus { border-color: #7367f0; box-shadow: 0 0 0 3px rgba(115, 103, 240, .2); }
.imp-input[type=date] { cursor: pointer; }
.imp .v-select .vs__dropdown-toggle { min-height: 40px; background: #fff; border: 1.5px solid #cbd5e1; cursor: pointer; }
.imp .v-select .vs__search { border: none !important; background: transparent !important; box-shadow: none !important; }
.imp-note { margin: 12px 0 0; font-size: 13px; color: #64748b; }

.imp-table-wrap { overflow-x: auto; margin-top: 16px; border: 1px solid #e2e8f0; border-radius: 8px; }
.imp-table { width: 100%; border-collapse: collapse; font-size: 14px; }
.imp-table th { position: sticky; top: 0; z-index: 1; background: #f8fafc; color: #475569; font-weight: 600; font-size: 13px; text-align: left; padding: 11px 12px; border-bottom: 1px solid #e2e8f0; white-space: nowrap; }
.imp-table th.tc { text-align: center; }
.imp-table td { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.imp-table tbody tr:hover td { background: #faf9ff; }
.imp-file { max-width: 320px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.imp-file a { color: #4f46e5; font-weight: 600; margin-left: 6px; }
.imp-sub { color: #64748b; font-size: 13px; }
.imp-msg { font-size: 13px; color: #b45309; min-width: 220px; }
.imp-badge { display: inline-flex !important; align-items: center; gap: 5px; white-space: nowrap !important; padding: 3px 10px; border-radius: 999px; font-size: 13px; font-weight: 600; }
.imp-badge--ok { background: #dcfce7; color: #15803d; }
.imp-badge--wait { background: #fef3c7; color: #b45309; }
.imp-badge--warn { background: #e0e7ff; color: #4338ca; }
.imp-badge--err { background: #fee2e2; color: #b91c1c; }
.imp-badge--muted { background: #f1f5f9; color: #475569; }
.imp-act { display: inline-flex; align-items: center; gap: 5px; height: 32px; min-width: 32px; justify-content: center; padding: 0 8px; margin: 0 2px; border-radius: 6px; color: #7367f0; background: #f5f3ff; cursor: pointer; font-size: 13px; font-weight: 600; text-decoration: none; }
.imp-act:hover { background: #ede9fe; }
.imp-act--primary { background: #7367f0; color: #fff !important; }
.imp-act--primary:hover { background: #5e50ee; }
.imp-empty { text-align: center; color: #94a3b8; padding: 36px 0 !important; }
.imp-empty i { font-size: 34px; display: block; margin-bottom: 8px; }
.imp-empty a { color: #7367f0; font-weight: 600; }
.imp-chips { display: flex; flex-wrap: wrap; gap: 8px; }
.imp-chip { cursor: pointer; padding: 6px 14px; border-radius: 999px; border: 1.5px solid #cbd5e1; background: #fff; font-size: 13px; white-space: nowrap; }
.imp-chip b { margin-left: 4px; }
.imp-chip.is-active { border-color: #7367f0; background: #7367f0; color: #fff; }
.imp-popup .vs-popup { width: min(1100px, 95vw) !important; }
</style>
