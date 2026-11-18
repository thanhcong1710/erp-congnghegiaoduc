<template>
  <div class="db-wrap">
    <div class="db-kpis">
      <div class="db-kpi brand">
        <div class="db-kpi__k">Thu hôm nay</div>
        <div class="db-kpi__v">{{ money(k.today) }}</div>
        <div class="db-kpi__s">{{ k.today_count }} phiếu đã duyệt</div>
      </div>
      <div class="db-kpi">
        <div class="db-kpi__k">Thực thu tháng</div>
        <div class="db-kpi__v">{{ money(k.revenue) }}</div>
        <div class="db-kpi__s">{{ k.revenue_count }} phiếu · tháng trước {{ money(k.revenue_prev) }}</div>
      </div>
      <div class="db-kpi" :class="{ warn: k.pending_receipts }">
        <div class="db-kpi__k">Phiếu thu chờ duyệt</div>
        <div class="db-kpi__v">{{ k.pending_receipts }}</div>
        <div class="db-kpi__s">{{ money(k.pending_amount) }}</div>
      </div>
      <div class="db-kpi" :class="{ warn: k.debt > 0 }">
        <div class="db-kpi__k">Tổng công nợ</div>
        <div class="db-kpi__v">{{ money(k.debt) }}</div>
        <div class="db-kpi__s">{{ k.debt_count }} hợp đồng</div>
      </div>
      <div class="db-kpi">
        <div class="db-kpi__k">HĐ đang đặt cọc</div>
        <div class="db-kpi__v">{{ k.deposit_count }}</div>
        <div class="db-kpi__s">Thu dưới 30% học phí</div>
      </div>
    </div>

    <div class="db-card">
      <div class="db-card__title">Phiếu thu chờ duyệt <span class="db-pill">{{ d.pending_list.length }}</span>
        <button type="button" class="db-link" @click="go('/lms/waitcharges')">Mở màn duyệt phiếu <i class="fa-solid fa-arrow-right"></i></button>
      </div>
      <div v-if="!d.pending_list.length" class="db-empty">Không có phiếu chờ duyệt</div>
      <div class="db-table-wrap" v-else>
        <table class="db-table">
          <thead><tr><th class="l">Mã phiếu</th><th class="l">Học sinh / HĐ</th><th class="l c-branch">Cơ sở</th><th>Ngày thu</th><th>Hình thức</th><th>Số tiền</th><th class="l">Người lập</th></tr></thead>
          <tbody>
            <tr v-for="r in d.pending_list" :key="r.id" class="db-click" @click="go(`/lms/contracts/${r.contract_id}/detail`)">
              <td class="l nowrap">{{ r.code || '#' + r.id }}</td>
              <td class="l"><div class="db-strong">{{ r.student_name }}</div><div class="db-sub">{{ r.contract_code }}</div></td>
              <td class="l c-branch"><span class="nowrap">{{ r.branch_name }}</span></td>
              <td class="nowrap">{{ fmtDate(r.charge_date) }}</td>
              <td class="nowrap">{{ r.method_name }}</td>
              <td><b>{{ fullMoney(r.charge_amount) }}</b></td>
              <td class="l nowrap">{{ r.creator_name }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="db-grid two">
      <div class="db-card">
        <div class="db-card__title">Thực thu 6 tháng gần nhất</div>
        <vue-apex-charts type="bar" height="260" :options="colOptions(d.revenue_trend.labels)" :series="[{ name: 'Thực thu', data: d.revenue_trend.values }]" />
      </div>
      <div class="db-card">
        <div class="db-card__title">Thực thu tháng theo hình thức</div>
        <div v-if="!d.by_method.values.length" class="db-empty">Chưa có phiếu thu trong tháng</div>
        <vue-apex-charts v-else type="bar" :height="chartH(d.by_method.labels)" :options="barOptions(d.by_method.labels, true)" :series="[{ name: 'Thực thu', data: d.by_method.values }]" />
      </div>
    </div>

    <div class="db-card">
      <div class="db-card__title">Công nợ lớn nhất</div>
      <div v-if="!d.debts.length" class="db-empty">Không có công nợ</div>
      <div class="db-table-wrap" v-else>
        <table class="db-table">
          <thead><tr><th class="l">Học sinh</th><th class="l">Hợp đồng</th><th class="l c-branch">Cơ sở</th><th class="l">EC</th><th>Phải thu</th><th>Đã thu</th><th>Còn nợ</th></tr></thead>
          <tbody>
            <tr v-for="c in d.debts" :key="c.contract_id" class="db-click" @click="go(`/lms/contracts/${c.contract_id}/detail`)">
              <td class="l db-strong">{{ c.name }}</td>
              <td class="l nowrap">{{ c.code }}</td>
              <td class="l c-branch"><span class="nowrap">{{ c.branch_name }}</span></td>
              <td class="l nowrap">{{ c.ec_name || '—' }}</td>
              <td>{{ money(c.must_charge) }}</td>
              <td>{{ money(c.total_charged) }}</td>
              <td><b class="db-money">{{ money(c.debt_amount) }}</b></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import dash from './dashMixin.js'

export default {
  name: 'AccountantDashboard',
  mixins: [dash],
  props: { d: { type: Object, required: true } },
  computed: { k() { return this.d.kpis || {} } }
}
</script>
