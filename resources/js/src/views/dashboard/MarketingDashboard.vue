<template>
  <div class="db-wrap">
    <div class="db-kpis">
      <div class="db-kpi brand">
        <div class="db-kpi__k">Lead mới tháng</div>
        <div class="db-kpi__v">{{ k.leads }}</div>
        <div class="db-kpi__s">
          <span v-if="diffPct(k.leads, k.leads_prev) !== null" :class="diffPct(k.leads, k.leads_prev) >= 0 ? 'up' : 'down'">
            {{ diffPct(k.leads, k.leads_prev) >= 0 ? '▲' : '▼' }} {{ Math.abs(diffPct(k.leads, k.leads_prev)) }}%
          </span>
          <span v-else>Tháng trước: {{ k.leads_prev }}</span>
        </div>
      </div>
      <div class="db-kpi">
        <div class="db-kpi__k">Tỉ lệ đến checkin</div>
        <div class="db-kpi__v">{{ pctText(k.checkin_rate) }}</div>
        <div class="db-kpi__s">{{ k.checkin }} lead đã checkin · hẹn {{ k.booked }}</div>
      </div>
      <div class="db-kpi">
        <div class="db-kpi__k">Tỉ lệ mua gói</div>
        <div class="db-kpi__v">{{ pctText(k.conversion) }}</div>
        <div class="db-kpi__s">{{ k.bought }} lead đã mua gói phí</div>
      </div>
      <div class="db-kpi" :class="{ warn: k.bad_rate >= 20 }">
        <div class="db-kpi__k">Lead kém chất lượng</div>
        <div class="db-kpi__v">{{ pctText(k.bad_rate) }}</div>
        <div class="db-kpi__s">{{ k.bad }} lead sai số / không nhu cầu / sai đối tượng</div>
      </div>
      <div class="db-kpi">
        <div class="db-kpi__k">HĐ mới tháng</div>
        <div class="db-kpi__v">{{ k.new_contracts }}</div>
        <div class="db-kpi__s">Giá trị {{ money(k.new_contract_amount) }}</div>
      </div>
      <div class="db-kpi">
        <div class="db-kpi__k">Chiến dịch đang chạy</div>
        <div class="db-kpi__v">{{ k.campaigns }}</div>
        <div class="db-kpi__s">Trong tháng đang xem</div>
      </div>
    </div>

    <div v-if="k.no_source" class="db-actions">
      <button type="button" class="db-action hot" @click="go('/crm/parent/detail-list')">
        <i class="fa-solid fa-triangle-exclamation"></i><b>{{ k.no_source }}</b> lead chưa gắn nguồn – không đo được hiệu quả kênh
      </button>
      <button type="button" class="db-action" @click="go('/settings/sources')">
        <i class="fa-solid fa-sitemap"></i>Cấu hình nguồn & kênh ngách
      </button>
    </div>

    <div class="db-grid two">
      <div class="db-card">
        <div class="db-card__title">Lead mới 6 tháng gần nhất</div>
        <vue-apex-charts type="bar" height="260" :options="colOptions(d.lead_trend.labels, false)" :series="[{ name: 'Lead mới', data: d.lead_trend.values }]" />
      </div>
      <div class="db-card">
        <div class="db-card__title">Phễu chuyển đổi lead trong tháng</div>
        <vue-apex-charts type="bar" height="260" :options="barOptions(d.funnel.labels)" :series="[{ name: 'Số lead', data: d.funnel.values }]" />
      </div>
    </div>

    <div class="db-card">
      <div class="db-card__title">Hiệu quả theo nguồn khách hàng</div>
      <div v-if="!d.by_source.length" class="db-empty">Chưa có lead trong tháng</div>
      <div class="db-table-wrap" v-else>
        <table class="db-table">
          <thead><tr><th class="l">Nguồn</th><th>Lead</th><th>Hẹn checkin</th><th>Đã checkin</th><th>Mua gói</th><th>Tỉ lệ mua</th><th>HĐ mới</th><th>Giá trị HĐ</th></tr></thead>
          <tbody>
            <tr v-for="r in d.by_source" :key="r.name">
              <td class="l nowrap db-strong">{{ r.name }}</td>
              <td><b>{{ r.leads }}</b></td>
              <td>{{ r.booked }}</td>
              <td>{{ r.checkin }}</td>
              <td>{{ r.bought }}</td>
              <td><span class="db-pct" :class="convCls(r.conversion)">{{ pctText(r.conversion) }}</span></td>
              <td>{{ r.contracts }}</td>
              <td>{{ money(r.revenue) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="db-grid two">
      <div class="db-card">
        <div class="db-card__title">Top kênh ngách</div>
        <div v-if="!d.by_detail.length" class="db-empty">Chưa có dữ liệu</div>
        <div class="db-table-wrap" v-else>
          <table class="db-table">
            <thead><tr><th class="l">Kênh ngách</th><th>Lead</th><th>Checkin</th><th>Mua gói</th><th>Tỉ lệ mua</th></tr></thead>
            <tbody>
              <tr v-for="(r, i) in d.by_detail" :key="i">
                <td class="l"><div class="db-strong">{{ r.name }}</div><div class="db-sub">{{ r.source_name }}</div></td>
                <td><b>{{ r.leads }}</b></td>
                <td>{{ r.checkin }}</td>
                <td>{{ r.bought }}</td>
                <td><span class="db-pct" :class="convCls(r.conversion)">{{ pctText(r.conversion) }}</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="db-card">
        <div class="db-card__title">Lead mới theo cơ sở</div>
        <div v-if="!d.by_branch.values.length" class="db-empty">Chưa có lead trong tháng</div>
        <vue-apex-charts v-else type="bar" :height="chartH(d.by_branch.labels)" :options="barOptions(d.by_branch.labels)" :series="[{ name: 'Lead', data: d.by_branch.values }]" />
      </div>
    </div>

    <div class="db-card">
      <div class="db-card__title">Chiến dịch đang chạy trong tháng <span class="db-pill">{{ d.campaigns.length }}</span>
        <button type="button" class="db-link" @click="go('/marketing/campaigns')">Quản lý chiến dịch <i class="fa-solid fa-arrow-right"></i></button>
      </div>
      <div v-if="!d.campaigns.length" class="db-empty">Không có chiến dịch đang chạy</div>
      <div v-for="c in d.campaigns" :key="c.id" class="db-row" @click="go('/marketing/campaigns')">
        <div class="db-strong">{{ c.title }}</div>
        <span class="db-badge brand">{{ fmtDate(c.start_date) }} – {{ fmtDate(c.end_date) }}</span>
      </div>
    </div>
  </div>
</template>

<script>
import dash from './dashMixin.js'

export default {
  name: 'MarketingDashboard',
  mixins: [dash],
  props: { d: { type: Object, required: true } },
  computed: { k() { return this.d.kpis || {} } },
  methods: {
    convCls(v) { if (v === null || v === undefined) return ''; return v >= 20 ? 'good' : v >= 8 ? 'mid' : 'low' }
  }
}
</script>
