<template>
  <div class="db-wrap">
    <!-- KPI -->
    <div class="db-kpis">
      <div class="db-kpi brand">
        <div class="db-kpi__k">Thực thu / chỉ tiêu</div>
        <div class="db-kpi__v">{{ money(k.revenue) }} <small>/ {{ money(k.target) }}</small></div>
        <div class="db-bar"><i :style="{ width: Math.min(100, k.target_pct || 0) + '%' }"></i></div>
        <div class="db-kpi__s">Đạt {{ pctText(k.target_pct) }}<template v-if="k.remaining > 0"> · còn thiếu {{ money(k.remaining) }}</template></div>
      </div>
      <div class="db-kpi">
        <div class="db-kpi__k">Cần thu mỗi ngày</div>
        <div class="db-kpi__v">{{ k.days_left && k.remaining ? money(k.remaining / k.days_left) : '—' }}</div>
        <div class="db-kpi__s">Còn {{ k.days_left }} ngày trong tháng</div>
      </div>
      <div class="db-kpi">
        <div class="db-kpi__k">Hợp đồng mới</div>
        <div class="db-kpi__v">{{ k.new_contracts }}</div>
        <div class="db-kpi__s">Tạm tính {{ money(k.new_contract_amount) }}</div>
      </div>
      <div class="db-kpi" :class="{ warn: k.pending_receipts }">
        <div class="db-kpi__k">Phiếu thu chờ duyệt</div>
        <div class="db-kpi__v">{{ k.pending_receipts }}</div>
        <div class="db-kpi__s">{{ money(k.pending_amount) }} – chưa tính vào thực thu</div>
      </div>
      <div class="db-kpi" :class="{ warn: k.debt > 0 }">
        <div class="db-kpi__k">Công nợ khách hàng</div>
        <div class="db-kpi__v">{{ money(k.debt) }}</div>
        <div class="db-kpi__s">{{ k.debt_count }} hợp đồng</div>
      </div>
      <div class="db-kpi">
        <div class="db-kpi__k">Data đang chăm sóc</div>
        <div class="db-kpi__v">{{ k.owned }}</div>
        <div class="db-kpi__s">Nhận mới tháng này {{ k.leads }}</div>
      </div>
      <div class="db-kpi">
        <div class="db-kpi__k">Chuyển đổi tháng</div>
        <div class="db-kpi__v">{{ pctText(k.conversion) }}</div>
        <div class="db-kpi__s">Checkin {{ k.leads_checkin }} · Mua gói {{ k.leads_bought }}</div>
      </div>
      <div class="db-kpi">
        <div class="db-kpi__k">Cuộc chăm sóc</div>
        <div class="db-kpi__v">{{ k.care_today }} <small>hôm nay</small></div>
        <div class="db-kpi__s">{{ k.care_month }} trong tháng</div>
      </div>
    </div>

    <!-- Việc hôm nay -->
    <div class="db-actions">
      <button type="button" class="db-action" :class="{ hot: k.overdue }" @click="scrollTo('db-todo')">
        <i class="fa-solid fa-phone-volume"></i><b>{{ k.overdue }}</b> khách quá hạn gọi lại
      </button>
      <button type="button" class="db-action" :class="{ hot: k.due_today }" @click="scrollTo('db-todo')">
        <i class="fa-solid fa-calendar-day"></i><b>{{ k.due_today }}</b> khách cần gọi hôm nay
      </button>
      <button type="button" class="db-action" :class="{ hot: k.warehouse_soon }" @click="scrollTo('db-soon')">
        <i class="fa-solid fa-box-archive"></i><b>{{ k.warehouse_soon }}</b> data sắp về kho (≤ 3 ngày)
      </button>
      <button type="button" class="db-action" @click="go('/crm/parent/detail-list')">
        <i class="fa-solid fa-address-book"></i>Mở danh sách khách hàng
      </button>
    </div>

    <div class="db-grid two">
      <div class="db-card" id="db-todo">
        <div class="db-card__title">Khách cần gọi (đến hạn & quá hạn) <span class="db-pill">{{ d.todo.length }}</span></div>
        <div v-if="!d.todo.length" class="db-empty">Không có khách đến hạn gọi lại 🎉</div>
        <div v-for="p in d.todo" :key="'t' + p.id" class="db-row" @click="go(`/crm/parent/${p.id}/detail`)">
          <div class="db-row__main">
            <div class="db-strong">{{ p.name }} <span class="db-sub">· {{ p.mobile_1 }}</span></div>
            <div class="db-sub">{{ p.status_label }}<template v-if="d.is_team"> · {{ p.owner_name }}</template></div>
            <div class="db-note" v-if="p.last_note">{{ p.last_note }}</div>
          </div>
          <span class="db-badge" :class="isOverdue(p.next_care_date) ? 'danger' : 'warn'">{{ isOverdue(p.next_care_date) ? 'Quá hạn ' : 'Hôm nay ' }}{{ fmtDate(p.next_care_date) }}</span>
        </div>
      </div>
      <div class="db-card">
        <div class="db-card__title">Thực thu luỹ kế trong tháng</div>
        <vue-apex-charts type="area" height="280" :options="cumulativeOptions(d.revenue_daily.labels)" :series="cumulativeSeries" />
      </div>
    </div>

    <div class="db-grid three">
      <div class="db-card">
        <div class="db-card__title">Phễu data nhận trong tháng</div>
        <vue-apex-charts type="bar" height="260" :options="barOptions(d.funnel.labels)" :series="[{ name: 'Số data', data: d.funnel.values }]" />
      </div>
      <div class="db-card">
        <div class="db-card__title">Data theo nguồn</div>
        <div v-if="!d.leads_by_source.values.length" class="db-empty">Chưa nhận data trong tháng</div>
        <vue-apex-charts v-else type="bar" :height="chartH(d.leads_by_source.labels)" :options="barOptions(d.leads_by_source.labels)" :series="[{ name: 'Data', data: d.leads_by_source.values }]" />
      </div>
      <div class="db-card">
        <div class="db-card__title">Kết quả cuộc gọi trong tháng</div>
        <div v-if="!d.calls.values.length" class="db-empty">Chưa có cuộc chăm sóc</div>
        <vue-apex-charts v-else type="bar" :height="chartH(d.calls.labels)" :options="barOptions(d.calls.labels)" :series="[{ name: 'Cuộc gọi', data: d.calls.values }]" />
      </div>
    </div>

    <div v-if="d.members" class="db-card">
      <div class="db-card__title">Thành viên trong team</div>
      <div class="db-table-wrap">
        <table class="db-table">
          <thead><tr><th class="l">Sale</th><th>Chỉ tiêu</th><th>Thực thu</th><th>% đạt</th><th>HĐ mới</th><th>Data nhận</th><th>Chăm sóc</th><th>Quá hạn gọi</th></tr></thead>
          <tbody>
            <tr v-for="m in d.members" :key="m.id">
              <td class="l nowrap"><b>{{ m.name }}</b> <span class="db-sub">{{ m.hrm_id }}</span></td>
              <td>{{ money(m.target) }}</td>
              <td><b>{{ money(m.actual) }}</b></td>
              <td><span class="db-pct" :class="pctCls(m.pct)">{{ pctText(m.pct) }}</span></td>
              <td>{{ m.contracts }}</td>
              <td>{{ m.leads }}</td>
              <td>{{ m.cares }}</td>
              <td><span :class="{ 'db-badge danger': m.overdue > 0 }">{{ m.overdue }}</span></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="db-grid two">
      <div class="db-card" id="db-soon">
        <div class="db-card__title">Data sắp về kho <span class="db-pill">{{ d.warehouse_soon.length }}</span></div>
        <div class="db-hint">Data chưa đăng ký học sẽ tự về kho sau 10 ngày kể từ ngày nhận – chăm sóc ngay để giữ data.</div>
        <div v-if="!d.warehouse_soon.length" class="db-empty">Không có data sắp về kho</div>
        <div v-for="p in d.warehouse_soon" :key="'w' + p.id" class="db-row" @click="go(`/crm/parent/${p.id}/detail`)">
          <div><div class="db-strong">{{ p.name }} <span class="db-sub">· {{ p.mobile_1 }}</span></div><div class="db-sub">{{ p.status_label }}</div></div>
          <span class="db-badge danger">Còn {{ p.days_left }} ngày</span>
        </div>
      </div>
      <div class="db-card">
        <div class="db-card__title">Hợp đồng còn công nợ</div>
        <div v-if="!d.debts.length" class="db-empty">Không có công nợ</div>
        <div v-for="c in d.debts" :key="'d' + c.contract_id" class="db-row" @click="go(`/lms/contracts/${c.contract_id}/detail`)">
          <div><div class="db-strong">{{ c.name }}</div><div class="db-sub">{{ c.code }} · đã thu {{ money(c.total_charged) }}/{{ money(c.must_charge) }}</div></div>
          <span class="db-money">{{ money(c.debt_amount) }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import moment from 'moment'
import dash from './dashMixin.js'

export default {
  name: 'SaleDashboard',
  mixins: [dash],
  props: { d: { type: Object, required: true } },
  computed: {
    k() { return this.d.kpis || {} },
    cumulativeSeries() {
      const n = this.d.revenue_daily.labels.length
      const target = this.k.target || 0
      const s = [{ name: 'Thực thu luỹ kế', data: this.d.revenue_daily.values }]
      if (target) s.push({ name: 'Chỉ tiêu (đều theo ngày)', data: this.d.revenue_daily.labels.map((_, i) => Math.round(target * (i + 1) / n)) })
      return s
    }
  },
  methods: {
    isOverdue(d) { return d && moment(d).isBefore(moment(), 'day') }
  }
}
</script>
