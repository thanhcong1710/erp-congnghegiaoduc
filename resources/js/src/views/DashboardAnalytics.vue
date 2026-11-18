<!-- =========================================================================================
  Dashboard theo vai trò (dữ liệu thật) – API: dashboard/role/init, dashboard/role/data
  - Admin: xem tất cả tab, mỗi tab chọn được tài khoản thuộc role đó (tab "Toàn hệ thống" chọn cơ sở)
  - Role khác: chỉ thấy tab của role mình; trưởng nhóm chọn được thành viên trong team
========================================================================================== -->
<template>
  <div id="dashboard-analytics" class="db-page">
    <!-- Header -->
    <div class="db-head">
      <div>
        <h2 class="db-head__title">{{ greeting }}, {{ userName }}</h2>
        <p class="db-head__sub">Bảng điều khiển {{ currentTab ? currentTab.label.toLowerCase() : '' }} · số liệu cập nhật trực tiếp từ hệ thống</p>
      </div>
      <div class="db-head__filters">
        <!-- Cơ sở (tab Toàn hệ thống) -->
        <div v-if="currentTab && currentTab.key === 'system' && branches.length > 1" class="db-filter">
          <label>Cơ sở</label>
          <select v-model="branchId" class="db-select" @change="load">
            <option :value="0">Tất cả cơ sở</option>
            <option v-for="b in branches" :key="b.id" :value="b.id">{{ b.name }}</option>
          </select>
        </div>
        <!-- Tài khoản trong role -->
        <div v-if="currentTab && currentTab.key !== 'system' && currentTab.accounts.length" class="db-filter">
          <label>Tài khoản</label>
          <select v-if="currentTab.can_switch" v-model="accountId" class="db-select wide" @change="onAccount">
            <option v-for="a in currentTab.accounts" :key="a.id" :value="a.id">{{ a.name }}{{ a.hrm_id ? ' - ' + a.hrm_id : '' }}{{ a.branch_name ? ' · ' + a.branch_name : '' }}</option>
          </select>
          <div v-else class="db-select static">{{ currentTab.accounts[0].name }}</div>
        </div>
        <!-- Tháng -->
        <div v-if="currentTab && currentTab.key !== 'teacher'" class="db-filter">
          <label>Tháng</label>
          <input type="month" v-model="month" class="db-select" :max="thisMonth" @change="load" />
        </div>
        <button v-if="currentTab && currentTab.key !== 'teacher'" type="button" class="db-refresh" title="Tải lại" @click="load"><i class="fa-solid fa-rotate"></i></button>
      </div>
    </div>

    <div v-if="initError" class="db-card db-empty">{{ initError }}</div>

    <!-- Tabs (chỉ hiện khi được xem > 1 tab) -->
    <div v-if="tabs.length > 1" class="db-tabs">
      <button v-for="t in tabs" :key="t.key" type="button" class="db-tab" :class="{ active: t.key === tabKey }" @click="selectTab(t.key)">
        <i class="material-icons">{{ t.icon }}</i>{{ t.label }}
      </button>
    </div>

    <template v-if="currentTab">
      <div v-if="currentTab.key !== 'system' && !currentTab.accounts.length && !['ceo', 'accountant', 'marketing'].includes(currentTab.key)" class="db-card db-empty">
        Chưa có tài khoản nào thuộc vai trò này.
      </div>
      <teacher-timetable v-else-if="currentTab.key === 'teacher'" :teacher-id="accountId" />
      <template v-else>
        <div v-if="error" class="db-card db-empty">{{ error }}</div>
        <div v-else-if="!data" class="db-card db-empty">Đang tải số liệu...</div>
        <overview-dashboard v-else-if="['system', 'ceo'].includes(currentTab.key)" :d="data" />
        <sale-dashboard v-else-if="currentTab.key === 'sale'" :d="data" />
        <cm-dashboard v-else-if="currentTab.key === 'cm'" :d="data" />
        <marketing-dashboard v-else-if="currentTab.key === 'marketing'" :d="data" />
        <accountant-dashboard v-else-if="currentTab.key === 'accountant'" :d="data" />
      </template>
    </template>
  </div>
</template>

<script>
import moment from 'moment'
import axios from '../http/axios.js'
import OverviewDashboard from './dashboard/OverviewDashboard.vue'
import SaleDashboard from './dashboard/SaleDashboard.vue'
import CmDashboard from './dashboard/CmDashboard.vue'
import AccountantDashboard from './dashboard/AccountantDashboard.vue'
import MarketingDashboard from './dashboard/MarketingDashboard.vue'
import TeacherTimetable from './dashboard/TeacherTimetable.vue'

const LS_KEY = 'dashboard_role_state'

export default {
  components: { OverviewDashboard, SaleDashboard, CmDashboard, AccountantDashboard, MarketingDashboard, TeacherTimetable },
  data() {
    return {
      tabs: [],
      branches: [],
      tabKey: null,
      accountId: null,
      accountByTab: {},
      branchId: 0,
      month: moment().format('YYYY-MM'),
      thisMonth: moment().format('YYYY-MM'),
      data: null,
      error: '',
      initError: '',
      reqId: 0
    }
  },
  computed: {
    currentTab() { return this.tabs.find(t => t.key === this.tabKey) || null },
    userName() {
      try { return (JSON.parse(localStorage.getItem('userInfo')) || {}).displayName || '' } catch (e) { return '' }
    },
    greeting() {
      const h = new Date().getHours()
      return h < 11 ? 'Chào buổi sáng' : h < 14 ? 'Chào buổi trưa' : h < 18 ? 'Chào buổi chiều' : 'Chào buổi tối'
    }
  },
  created() {
    let saved = {}
    try { saved = JSON.parse(localStorage.getItem(LS_KEY)) || {} } catch (e) { /* ignore */ }
    axios.g('/api/dashboard/role/init').then(res => {
      const d = res.data || {}
      if (d.status !== 1) { this.initError = d.message || 'Không tải được dashboard'; return }
      this.tabs = d.data.tabs || []
      this.branches = d.data.branches || []
      if (!this.tabs.length) { this.initError = 'Tài khoản chưa được phân vai trò để xem dashboard.'; return }
      // Nhớ tài khoản đã chọn theo từng tab (chỉ khi vẫn còn quyền)
      this.tabs.forEach(t => {
        const keep = saved.accounts && saved.accounts[t.key]
        this.$set(this.accountByTab, t.key, t.accounts.some(a => a.id === keep) ? keep : t.default_id)
      })
      if (saved.branch_id && this.branches.some(b => b.id === saved.branch_id)) this.branchId = saved.branch_id
      const q = this.$route.query.tab
      const want = this.tabs.find(t => t.key === q) || this.tabs.find(t => t.key === saved.tab) || this.tabs[0]
      this.selectTab(want.key)
    }).catch(() => { this.initError = 'Không tải được dashboard' })
  },
  methods: {
    persist() {
      try { localStorage.setItem(LS_KEY, JSON.stringify({ tab: this.tabKey, accounts: this.accountByTab, branch_id: this.branchId })) } catch (e) { /* ignore */ }
    },
    selectTab(key) {
      if (this.tabKey === key && this.data) return
      this.tabKey = key
      this.accountId = this.accountByTab[key] || null
      this.data = null
      this.persist()
      if (key !== 'teacher') this.load()
    },
    onAccount() {
      this.$set(this.accountByTab, this.tabKey, this.accountId)
      this.persist()
      if (this.tabKey !== 'teacher') this.load()
    },
    load() {
      const tab = this.currentTab
      if (!tab || tab.key === 'teacher') return
      this.persist()
      const id = ++this.reqId
      this.error = ''
      this.$vs.loading()
      axios.p('/api/dashboard/role/data', {
        tab: tab.key, user_id: this.accountId || '', branch_id: this.branchId || '', month: this.month || this.thisMonth
      }).then(res => {
        if (id !== this.reqId) return
        this.$vs.loading.close()
        const d = res.data || {}
        if (d.status !== 1) { this.error = d.message || 'Không tải được số liệu'; this.data = null; return }
        this.data = d.data
      }).catch(() => {
        if (id !== this.reqId) return
        this.$vs.loading.close()
        this.error = 'Không tải được số liệu'
      })
    }
  }
}
</script>

<style>
/* ===== Khung dashboard (dùng chung cho các tab) ===== */
.db-page { font-size: 14px; color: #334155; display: flex; flex-direction: column; gap: 16px; padding: 4px 0 24px; }
.db-head { display: flex; justify-content: space-between; align-items: flex-end; gap: 16px; flex-wrap: wrap; }
.db-head__title { font-size: 24px; font-weight: 800; color: #1e293b; margin: 0; }
.db-head__sub { font-size: 14px; color: #64748b; margin-top: 4px; }
.db-head__filters { display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap; }
.db-filter { display: flex; flex-direction: column; gap: 4px; }
.db-filter label { font-size: 13px; font-weight: 600; color: #64748b; }
.db-select { height: 40px; min-width: 160px; background: #fff; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 0 10px; font-size: 14px; color: #1e293b; cursor: pointer; font-family: inherit; }
.db-select.wide { min-width: 300px; max-width: 420px; }
.db-select:hover, .db-select:focus { border-color: #7367f0; box-shadow: 0 0 0 3px rgba(115, 103, 240, 0.2); outline: none; }
.db-select.static { display: flex; align-items: center; background: #f1f5f9; border: 1px dashed #cbd5e1; color: #475569; cursor: default; box-shadow: none; }
.db-refresh { width: 40px; height: 40px; border-radius: 8px; border: 1.5px solid #cbd5e1; background: #fff; color: #475569; cursor: pointer; font-size: 14px; }
.db-refresh:hover { border-color: #7367f0; color: #7367f0; box-shadow: 0 0 0 3px rgba(115, 103, 240, 0.2); }

.db-tabs { display: flex; gap: 6px; flex-wrap: wrap; border-bottom: 2px solid #e2e8f0; }
.db-tab { display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; background: none; border: none; border-bottom: 3px solid transparent; margin-bottom: -2px; padding: 10px 14px; font-size: 14px; font-weight: 600; color: #64748b; cursor: pointer; font-family: inherit; }
.db-tab .material-icons { font-size: 18px; }
.db-tab:hover { color: #7367f0; }
.db-tab.active { color: #7367f0; border-bottom-color: #7367f0; }

.db-wrap { display: flex; flex-direction: column; gap: 16px; }
.db-card { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(15, 23, 42, 0.06); padding: 16px 18px; min-width: 0; }
.db-card__title { display: flex; align-items: center; gap: 8px; font-size: 15px; font-weight: 700; color: #1e293b; margin-bottom: 10px; }
.db-empty { text-align: center; color: #94a3b8; font-size: 14px; padding: 24px 10px; }
.db-hint { font-size: 13px; color: #64748b; margin-bottom: 8px; }
.db-pill { font-size: 13px; font-weight: 600; background: #f1f5f9; color: #475569; border-radius: 99px; padding: 1px 9px; white-space: nowrap; }
.db-link { margin-left: auto; background: none; border: none; color: #7367f0; font-size: 13px; font-weight: 600; cursor: pointer; white-space: nowrap; font-family: inherit; }
.db-grid { display: grid; gap: 16px; }
.db-grid.two { grid-template-columns: repeat(2, minmax(0, 1fr)); }
.db-grid.three { grid-template-columns: repeat(3, minmax(0, 1fr)); }
@media (max-width: 1200px) { .db-grid.three { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
@media (max-width: 900px) { .db-grid.two, .db-grid.three { grid-template-columns: 1fr; } .db-select.wide { min-width: 220px; } }
.nowrap { white-space: nowrap !important; }

/* KPI */
.db-kpis { display: grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap: 12px; }
.db-kpi { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(15, 23, 42, 0.06); padding: 12px 14px; border-top: 3px solid #e2e8f0; min-width: 0; }
.db-kpi.brand { border-top-color: #2a78d6; }
.db-kpi.warn { border-top-color: #fab219; }
.db-kpi.danger { border-top-color: #d03b3b; }
.db-kpi__k { font-size: 13px; font-weight: 600; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.db-kpi__v { font-size: 24px; font-weight: 800; color: #1e293b; line-height: 1.3; white-space: nowrap; }
.db-kpi__v small { font-size: 14px; font-weight: 600; color: #64748b; }
.db-kpi__s { font-size: 13px; color: #64748b; margin-top: 2px; }
.db-kpi__s .up { color: #0ca30c; font-weight: 700; }
.db-kpi__s .down { color: #d03b3b; font-weight: 700; }
.db-bar { height: 6px; background: #f1f5f9; border-radius: 99px; overflow: hidden; margin: 6px 0 2px; }
.db-bar i { display: block; height: 100%; background: #2a78d6; border-radius: 99px; transition: width .3s; }

/* Việc cần xử lý */
.db-actions { display: flex; gap: 10px; flex-wrap: wrap; }
.db-action { display: inline-flex; align-items: center; gap: 8px; white-space: nowrap; background: #fff; border: 1.5px solid #e2e8f0; border-radius: 99px; padding: 8px 14px; font-size: 14px; color: #475569; cursor: pointer; font-family: inherit; }
.db-action i { color: #94a3b8; }
.db-action b { color: #1e293b; }
.db-action span { color: #64748b; font-size: 13px; }
.db-action:hover { border-color: #7367f0; box-shadow: 0 0 0 3px rgba(115, 103, 240, 0.15); }
.db-action.hot { border-color: #fbd38d; background: #fffaf0; }
.db-action.hot i { color: #dd6b20; }

/* Danh sách */
.db-row { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 9px 4px; border-top: 1px dashed #e2e8f0; cursor: pointer; }
.db-row:hover { background: #fafaff; }
.db-row__main { min-width: 0; }
.db-strong { font-weight: 600; color: #1e293b; }
.db-sub { font-size: 13px; color: #64748b; }
.db-note { font-size: 13px; color: #475569; background: #f8fafc; border-radius: 6px; padding: 3px 8px; margin-top: 3px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 420px; }
.db-badge { display: inline-flex; align-items: center; white-space: nowrap !important; font-size: 13px; font-weight: 600; border-radius: 99px; padding: 2px 10px; flex-shrink: 0; }
.db-badge.warn { background: #fef3c7; color: #92400e; }
.db-badge.danger { background: #fee2e2; color: #b91c1c; }
.db-badge.brand { background: #e8f1fc; color: #1c5cab; }
.db-money { font-weight: 700; color: #1e293b; white-space: nowrap; }

/* Bảng */
.db-table-wrap { overflow-x: auto; }
.db-table { width: 100%; border-collapse: collapse; font-size: 14px; }
.db-table th { background: #f8fafc; color: #475569; font-weight: 700; font-size: 13px; padding: 8px 10px; text-align: right; white-space: nowrap; border-bottom: 1px solid #e2e8f0; }
.db-table td { padding: 8px 10px; text-align: right; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.db-table .l { text-align: left; }
.db-table .c-branch { min-width: 240px; }
.db-table tr.db-click { cursor: pointer; }
.db-table tr.db-click:hover td { background: #fafaff; }
.db-pct { display: inline-block; white-space: nowrap; font-weight: 700; border-radius: 6px; padding: 1px 8px; background: #f1f5f9; color: #475569; }
.db-pct.good { background: #dcfce7; color: #166534; }
.db-pct.mid { background: #fef3c7; color: #92400e; }
.db-pct.low { background: #fee2e2; color: #b91c1c; }
.db-rank { display: inline-flex; width: 24px; height: 24px; border-radius: 50%; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; background: #f1f5f9; color: #475569; }
.db-rank.r1 { background: #2a78d6; color: #fff; }
.db-rank.r2 { background: #86b6ef; color: #0d366b; }
.db-rank.r3 { background: #cde2fb; color: #0d366b; }
</style>
