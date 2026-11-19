<template>
  <div class="rr-wrap">
    <!-- Header -->
    <div class="rr-header">
      <div class="rr-header-left">
        <button type="button" class="rr-btn-back" @click="$emit('back')">
          <i class="fa fa-arrow-left mr-1"></i> Danh sách báo cáo
        </button>
        <div>
          <h2 class="rr-title">{{ report.number }}. {{ report.title }}</h2>
          <p class="rr-subtitle">
            Phạm vi dữ liệu: <strong>{{ levelLabel }}</strong>
            <span class="rr-dot">•</span>
            Dành cho: <span v-for="r in report.role_names" :key="r" class="rr-role-chip">{{ r }}</span>
          </p>
        </div>
      </div>
      <button type="button" class="rr-btn-export" :disabled="!rows.length" @click="exportCsv">
        <i class="fa fa-file-excel mr-1"></i> Export Excel
      </button>
    </div>

    <vx-card no-shadow class="rr-body">
      <!-- Chế độ xem -->
      <div v-if="has('group_by') && report.group_by.length" class="rr-mode-bar">
        <span class="rr-mode-label">Chế độ xem:</span>
        <button
          v-for="g in report.group_by"
          :key="g.value"
          type="button"
          class="rr-mode-pill"
          :class="{ active: filters.group_by === g.value }"
          @click="filters.group_by = g.value; fetch()"
        >{{ g.label }}</button>
      </div>

      <!-- Bộ lọc -->
      <div class="rr-filter-grid">
        <div v-if="has('date')" class="rr-cell">
          <label class="rr-label">Khoảng ngày</label>
          <date-picker
            v-model="filters.dateRange"
            range
            format="DD/MM/YYYY"
            :clearable="false"
            :lang="datepickerLang"
            placeholder="Từ ngày - Đến ngày"
            style="width: 100%"
          />
        </div>
        <div v-if="has('branch')" class="rr-cell">
          <label class="rr-label">Cơ sở</label>
          <select v-model.number="filters.branch_id" class="rr-control">
            <option :value="0">- Tất cả cơ sở được phân quyền -</option>
            <option v-for="b in meta.branches" :key="b.id" :value="b.id">{{ b.name }}</option>
          </select>
        </div>
        <div v-if="has('staff')" class="rr-cell">
          <label class="rr-label">Nhân viên</label>
          <select v-model.number="filters.staff_id" class="rr-control" :disabled="staffLevel === 'self'">
            <option :value="0">{{ staffLevel === 'self' ? '- Chỉ dữ liệu của bạn -' : '- Tất cả nhân viên trong phạm vi -' }}</option>
            <option v-for="s in staffOptions" :key="s.id" :value="s.id">{{ s.name }}{{ s.hrm_id ? ' (' + s.hrm_id + ')' : '' }}</option>
          </select>
        </div>
        <div v-if="has('source')" class="rr-cell">
          <label class="rr-label">Nguồn khách hàng</label>
          <select v-model.number="filters.source_id" class="rr-control">
            <option :value="0">- Tất cả nguồn -</option>
            <option v-for="s in meta.sources" :key="s.id" :value="s.id">{{ s.name }}</option>
          </select>
        </div>
        <div v-if="has('product')" class="rr-cell">
          <label class="rr-label">Sản phẩm</label>
          <select v-model.number="filters.product_id" class="rr-control">
            <option :value="0">- Tất cả sản phẩm -</option>
            <option v-for="p in meta.products" :key="p.id" :value="p.id">{{ p.name }}</option>
          </select>
        </div>
        <div v-if="has('method')" class="rr-cell">
          <label class="rr-label">Hình thức thu</label>
          <select v-model="filters.method" class="rr-control">
            <option value="">- Tất cả hình thức -</option>
            <option v-for="m in meta.methods" :key="m.id" :value="String(m.id)">{{ m.name }}</option>
          </select>
        </div>
        <div v-if="has('renew_type')" class="rr-cell">
          <label class="rr-label">Nhóm học sinh</label>
          <select v-model="filters.renew_type" class="rr-control">
            <option value="near_end">Sắp hết phí (đang học)</option>
            <option value="ended">Đã hết phí trong kỳ</option>
            <option value="all">Cả hai</option>
          </select>
        </div>
        <div v-if="has('threshold')" class="rr-cell">
          <label class="rr-label">Còn lại ≤ (buổi)</label>
          <input v-model.number="filters.threshold" type="number" min="0" max="30" class="rr-control" :disabled="filters.renew_type === 'ended'" />
        </div>
        <div class="rr-cell rr-actions">
          <button type="button" class="rr-btn-filter" :disabled="loading" @click="search">
            <i v-if="loading" class="fa fa-spinner fa-spin mr-1"></i> Lọc
          </button>
          <button type="button" class="rr-btn-reset" :disabled="loading" @click="reset">Đặt lại</button>
        </div>
      </div>

      <!-- Thẻ tổng hợp -->
      <div v-if="cards.length" class="rr-cards">
        <div v-for="(c, i) in cards" :key="i" class="rr-card" :class="'tone-' + (c.tone || 'default')">
          <div class="rr-card-label">{{ c.label }}</div>
          <div class="rr-card-value">{{ fmt(c.value, c.type) }}</div>
          <div v-if="c.sub" class="rr-card-sub">{{ c.sub }}</div>
        </div>
      </div>

      <!-- Bảng -->
      <div class="rr-table-wrap">
        <table class="rr-table">
          <thead>
            <tr>
              <th class="col-stt">STT</th>
              <th
                v-for="col in columns"
                :key="col.key"
                :class="[isNum(col) ? 'col-num' : '', col.highlight ? 'col-hl' : '', col.branch ? 'col-branch' : '']"
              >{{ col.label }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td :colspan="columns.length + 1" class="td-empty">
                <i class="fa fa-spinner fa-spin mr-2"></i> Đang tải dữ liệu báo cáo...
              </td>
            </tr>
            <tr v-else-if="!rows.length">
              <td :colspan="columns.length + 1" class="td-empty">{{ error || 'Không có dữ liệu phù hợp với bộ lọc đã chọn.' }}</td>
            </tr>
            <template v-else>
              <tr v-for="(row, idx) in pagedRows" :key="idx">
                <td class="col-stt">{{ (page - 1) * limit + idx + 1 }}</td>
                <td
                  v-for="col in columns"
                  :key="col.key"
                  :class="[isNum(col) ? 'col-num' : '', col.wide ? 'col-wide' : '', col.branch ? 'col-branch' : '', col.tone ? 'txt-' + col.tone : '']"
                >
                  <span
                    v-if="col.type === 'badge' || col.type === 'badge_percent'"
                    class="rr-badge"
                    :class="'badge-' + (row[col.key + '_tone'] || 'neutral')"
                  >{{ fmt(row[col.key], col.type) }}</span>
                  <span v-else :class="{ 'font-semibold': col.wide || col.highlight }">{{ fmt(row[col.key], col.type) }}</span>
                  <div v-if="col.sub && row[col.sub]" class="rr-sub">{{ row[col.sub] }}</div>
                </td>
              </tr>
            </template>
          </tbody>
          <tfoot v-if="summary && rows.length && !loading">
            <tr class="rr-total">
              <td></td>
              <td
                v-for="col in columns"
                :key="'sum-' + col.key"
                :class="[isNum(col) ? 'col-num' : '', col.tone ? 'txt-' + col.tone : '']"
              >
                <template v-if="summary[col.key] !== undefined">
                  <span
                    v-if="col.type === 'badge_percent'"
                    class="rr-badge"
                    :class="'badge-' + (summary[col.key + '_tone'] || 'neutral')"
                  >{{ fmt(summary[col.key], col.type) }}</span>
                  <span v-else>{{ typeof summary[col.key] === 'string' ? summary[col.key] : fmt(summary[col.key], col.type) }}</span>
                </template>
              </td>
            </tr>
          </tfoot>
        </table>
      </div>

      <!-- Phân trang -->
      <div v-if="rows.length > limit" class="rr-pager">
        <select v-model.number="limit" class="rr-control rr-limit" @change="page = 1">
          <option v-for="n in [20, 50, 100, 500]" :key="n" :value="n">{{ n }} dòng / trang</option>
        </select>
        <vs-pagination :total="Math.ceil(rows.length / limit)" :max="7" v-model="page" />
      </div>

      <p v-if="note" class="rr-note"><i class="fa fa-circle-info mr-1"></i> {{ note }}</p>
    </vx-card>
  </div>
</template>

<script>
import DatePicker from 'vue2-datepicker'
import axios from '../../../http/axios.js'
import u from '../../../until/helper.js'

const LEVEL_LABELS = {
  all: 'Toàn hệ thống (Admin)',
  branch: 'Các cơ sở được phân quyền',
  team: 'Đội nhóm của bạn',
  self: 'Cá nhân bạn'
}

export default {
  name: 'RoleReport',
  components: { DatePicker },
  props: {
    report: { type: Object, required: true },
    meta: { type: Object, required: true }
  },
  data() {
    return {
      loading: false,
      error: '',
      filters: this.defaultFilters(),
      columns: [],
      rows: [],
      summary: null,
      cards: [],
      note: '',
      level: '',
      page: 1,
      limit: 50,
      datepickerLang: {
        days: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
        months: ['Th1', 'Th2', 'Th3', 'Th4', 'Th5', 'Th6', 'Th7', 'Th8', 'Th9', 'Th10', 'Th11', 'Th12'],
        placeholder: { date: 'Chọn ngày', dateRange: 'Từ ngày - Đến ngày' }
      }
    }
  },
  computed: {
    staffInfo() {
      return (this.meta.staff_by_report || {})[this.report.code] || { level: '', staff: [] }
    },
    staffLevel() {
      return this.staffInfo.level
    },
    staffOptions() {
      return this.staffInfo.staff || []
    },
    levelLabel() {
      return LEVEL_LABELS[this.level || this.staffLevel] || (this.meta.is_admin ? LEVEL_LABELS.all : LEVEL_LABELS.branch)
    },
    pagedRows() {
      const s = (this.page - 1) * this.limit
      return this.rows.slice(s, s + this.limit)
    }
  },
  watch: {
    'report.code'() {
      this.filters = this.defaultFilters()
      this.fetch()
    }
  },
  created() {
    this.fetch()
  },
  methods: {
    defaultFilters() {
      const now = new Date()
      return {
        dateRange: [new Date(now.getFullYear(), now.getMonth(), 1), new Date(now.getFullYear(), now.getMonth(), now.getDate())],
        branch_id: 0,
        staff_id: 0,
        source_id: 0,
        product_id: 0,
        method: '',
        group_by: this.report && this.report.group_by && this.report.group_by.length ? this.report.group_by[0].value : '',
        renew_type: 'near_end',
        threshold: 4
      }
    },
    has(f) {
      return (this.report.filters || []).indexOf(f) !== -1
    },
    isNum(col) {
      return ['number', 'money', 'percent', 'badge_percent'].indexOf(col.type) !== -1
    },
    search() {
      this.page = 1
      this.fetch()
    },
    reset() {
      this.filters = this.defaultFilters()
      this.search()
    },
    async fetch() {
      this.loading = true
      this.error = ''
      const { dateRange, ...rest } = this.filters
      const payload = { ...rest, code: this.report.code }
      if (Array.isArray(dateRange) && dateRange[0] && dateRange[1]) {
        payload.start_date = u.dateToString(dateRange[0])
        payload.end_date = u.dateToString(dateRange[1])
      }
      try {
        const res = await axios.p('/api/lms/reports/role-data', payload)
        const d = res && res.data
        if (d && d.status === 1) {
          this.columns = d.data.columns || []
          this.rows = d.data.rows || []
          this.summary = d.data.summary || null
          this.cards = d.data.cards || []
          this.note = d.data.note || ''
          this.level = (d.data.meta && d.data.meta.level) || ''
          this.page = 1
        } else {
          this.rows = []
          this.cards = []
          this.error = (d && d.message) || 'Không tải được báo cáo'
        }
      } catch (e) {
        this.rows = []
        this.cards = []
        this.error = (e && e.response && e.response.data && e.response.data.message) || 'Không tải được báo cáo'
      } finally {
        this.loading = false
      }
    },
    fmt(v, type) {
      if (v === null || v === undefined || v === '') return type === 'text' || type === 'badge' || type === 'date' ? '—' : '0'
      if (type === 'money' || type === 'number') {
        const n = Number(v) || 0
        return n.toLocaleString('en-US', { maximumFractionDigits: type === 'money' ? 0 : 1 })
      }
      if (type === 'percent' || type === 'badge_percent') return (Number(v) || 0).toFixed(2) + '%'
      if (type === 'date') {
        const m = String(v).match(/^(\d{4})-(\d{2})-(\d{2})/)
        return m ? `${m[3]}/${m[2]}/${m[1]}` : v
      }
      return v
    },
    exportCsv() {
      const cell = (row, col) => {
        let v = row[col.key]
        if (col.type === 'money' || col.type === 'number') v = Number(v || 0)
        else v = this.fmt(v, col.type)
        if (col.sub && row[col.sub]) v = `${v} (${row[col.sub]})`
        return v
      }
      const headers = ['STT', ...this.columns.map(c => c.label)]
      const lines = this.rows.map((r, i) => [i + 1, ...this.columns.map(c => cell(r, c))])
      if (this.summary) {
        lines.push(['', ...this.columns.map(c => (this.summary[c.key] === undefined ? '' : (typeof this.summary[c.key] === 'string' ? this.summary[c.key] : cell(this.summary, { ...c, sub: null }))))])
      }
      const csv = '﻿' + [headers, ...lines]
        .map(l => l.map(x => `"${String(x === null || x === undefined ? '' : x).replace(/"/g, '""')}"`).join(','))
        .join('\n')
      const link = document.createElement('a')
      link.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8;' }))
      link.setAttribute('download', `bao-cao-${this.report.number}-${this.report.code}-${new Date().toISOString().slice(0, 10)}.csv`)
      document.body.appendChild(link)
      link.click()
      document.body.removeChild(link)
    }
  }
}
</script>

<style scoped>
.rr-wrap { font-size: 14px; color: #1e293b; }

.rr-header {
  display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;
  background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px 20px; margin-bottom: 16px;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
}
.rr-header-left { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; }
.rr-title { font-size: 17px; font-weight: 700; margin: 0 0 4px 0; color: #1e293b; }
.rr-subtitle { font-size: 13px; color: #64748b; margin: 0; display: flex; align-items: center; flex-wrap: wrap; gap: 4px; }
.rr-dot { margin: 0 4px; color: #cbd5e1; }
.rr-role-chip {
  display: inline-flex; align-items: center; white-space: nowrap; font-size: 13px; font-weight: 600;
  padding: 1px 8px; border-radius: 10px; background: #f1f5f9; color: #334155; border: 1px solid #e2e8f0;
}
.rr-btn-back, .rr-btn-export, .rr-btn-filter, .rr-btn-reset {
  height: 36px; padding: 0 14px; font-size: 13px; font-weight: 600; border-radius: 6px; cursor: pointer; white-space: nowrap;
}
.rr-btn-back { background: #f8fafc; color: #1e293b; border: 1px solid #cbd5e1; }
.rr-btn-back:hover { background: #e2e8f0; }
.rr-btn-export { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
.rr-btn-export:hover { background: #dcfce7; }
.rr-btn-export:disabled { opacity: .5; cursor: not-allowed; }
.rr-btn-filter { background: #28a745; color: #ffffff; border: none; padding: 0 18px; }
.rr-btn-filter:hover { background: #218838; }
.rr-btn-reset { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }

.rr-body { border: 1px solid #e2e8f0; border-radius: 8px; }

.rr-mode-bar {
  display: flex; align-items: center; flex-wrap: wrap; gap: 8px; padding: 10px 14px;
  background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; margin-bottom: 14px;
}
.rr-mode-label { font-size: 13px; font-weight: 700; color: #334155; margin-right: 4px; }
.rr-mode-pill {
  height: 34px; padding: 0 14px; font-size: 13px; font-weight: 600; color: #475569; background: #ffffff;
  border: 1.5px solid #cbd5e1; border-radius: 6px; cursor: pointer; white-space: nowrap;
}
.rr-mode-pill:hover { border-color: #7367f0; }
.rr-mode-pill.active { background: #1e293b; color: #ffffff; border-color: #1e293b; }

.rr-filter-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-bottom: 16px; align-items: end; }
@media (max-width: 1200px) { .rr-filter-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
@media (max-width: 992px) { .rr-filter-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
@media (max-width: 576px) { .rr-filter-grid { grid-template-columns: 1fr; } }
.rr-cell { min-width: 0; }
.rr-actions { display: flex; gap: 8px; }
.rr-label { display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 4px; }
.rr-control {
  width: 100%; height: 38px; padding: 6px 10px; font-size: 13.5px; color: #1e293b; background: #ffffff;
  border: 1.5px solid #cbd5e1; border-radius: 6px; outline: none; cursor: pointer; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}
input.rr-control { cursor: text; }
.rr-control:hover, .rr-control:focus { border-color: #7367f0; box-shadow: 0 0 0 3px rgba(115, 103, 240, 0.2); }
.rr-control:disabled {
  background: #f1f5f9 !important; border: 1px dashed #cbd5e1 !important; color: #94a3b8 !important;
  cursor: not-allowed !important; box-shadow: none !important;
}
.rr-cell >>> .mx-datepicker { width: 100% !important; }
.rr-cell >>> .mx-input {
  height: 38px !important; font-size: 13.5px !important; color: #1e293b !important; background: #ffffff !important;
  border: 1.5px solid #cbd5e1 !important; border-radius: 6px !important; cursor: pointer;
}
.rr-cell >>> .mx-input:hover, .rr-cell >>> .mx-input:focus { border-color: #7367f0 !important; }

.rr-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-bottom: 16px; }
.rr-card { background: #ffffff; border: 1px solid #e2e8f0; border-left: 4px solid #94a3b8; border-radius: 8px; padding: 12px 14px; }
.rr-card-label { font-size: 13px; color: #64748b; font-weight: 600; }
.rr-card-value { font-size: 20px; font-weight: 700; color: #0f172a; margin-top: 2px; white-space: nowrap; }
.rr-card-sub { font-size: 13px; color: #64748b; margin-top: 2px; }
.rr-card.tone-primary { border-left-color: #7367f0; }
.rr-card.tone-success { border-left-color: #16a34a; }
.rr-card.tone-success .rr-card-value { color: #15803d; }
.rr-card.tone-warning { border-left-color: #f59e0b; }
.rr-card.tone-warning .rr-card-value { color: #b45309; }
.rr-card.tone-danger { border-left-color: #dc2626; }
.rr-card.tone-danger .rr-card-value { color: #b91c1c; }

.rr-table-wrap { width: 100%; overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 4px; }
.rr-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
.rr-table thead th {
  background: #2b3441; color: #ffffff; font-weight: 700; font-size: 13px; padding: 11px 12px;
  border: 1px solid #3b4656; white-space: nowrap; text-align: left;
}
.rr-table thead th.col-num { text-align: right; }
.rr-table thead th.col-hl { background: #1e3a8a; }
.rr-table tbody td { padding: 9px 12px; border: 1px solid #e2e8f0; vertical-align: middle; white-space: nowrap; }
.rr-table tbody tr:nth-child(even) { background: #f8fafc; }
.rr-table tbody tr:hover { background: #f1f5f9; }
.col-stt { width: 52px; text-align: center !important; }
.col-num { text-align: right; white-space: nowrap; }
.col-wide { min-width: 200px; }
.col-branch { min-width: 240px; white-space: nowrap !important; }
.rr-sub { font-size: 13px; color: #64748b; margin-top: 2px; font-weight: 400; }
.txt-success { color: #15803d; }
.txt-danger { color: #b91c1c; }
.txt-warning { color: #b45309; }
.rr-total td { background: #f1f5f9 !important; border: 1px solid #cbd5e1; padding: 11px 12px; font-weight: 700; white-space: nowrap; }
.td-empty { text-align: center; padding: 28px 16px !important; color: #64748b; font-size: 14px; }

.rr-badge {
  display: inline-flex !important; align-items: center; white-space: nowrap !important;
  padding: 3px 9px; border-radius: 4px; font-size: 13px; font-weight: 700;
}
.badge-success { background: #dcfce7; color: #15803d; }
.badge-warning { background: #fef3c7; color: #b45309; }
.badge-danger { background: #fee2e2; color: #b91c1c; }
.badge-info { background: #e0f2fe; color: #0369a1; }
.badge-neutral { background: #f1f5f9; color: #475569; }

.rr-pager { display: flex; align-items: center; gap: 12px; margin-top: 16px; flex-wrap: wrap; }
.rr-limit { width: 170px; }
.rr-note { font-size: 13px; color: #64748b; margin: 14px 0 0 0; line-height: 1.5; }
</style>
