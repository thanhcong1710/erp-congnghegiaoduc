<template>
  <div class="db-wrap">
    <!-- KPI -->
    <div class="db-kpis">
      <div class="db-kpi brand">
        <div class="db-kpi__k">Thực thu tháng</div>
        <div class="db-kpi__v">{{ money(k.revenue) }}</div>
        <div class="db-kpi__s">
          <span v-if="diffPct(k.revenue, k.revenue_prev) !== null" :class="diffPct(k.revenue, k.revenue_prev) >= 0 ? 'up' : 'down'">
            {{ diffPct(k.revenue, k.revenue_prev) >= 0 ? '▲' : '▼' }} {{ Math.abs(diffPct(k.revenue, k.revenue_prev)) }}%
          </span>
          <span v-else>Tháng trước: {{ money(k.revenue_prev) }}</span>
        </div>
      </div>
      <div class="db-kpi">
        <div class="db-kpi__k">Đạt chỉ tiêu</div>
        <div class="db-kpi__v">{{ pctText(k.target_pct) }}</div>
        <div class="db-kpi__s">Chỉ tiêu {{ money(k.target) }}</div>
        <div class="db-bar"><i :style="{ width: Math.min(100, k.target_pct || 0) + '%' }"></i></div>
      </div>
      <div class="db-kpi">
        <div class="db-kpi__k">Hợp đồng mới</div>
        <div class="db-kpi__v">{{ k.new_contracts }}</div>
        <div class="db-kpi__s">Giá trị {{ money(k.new_contract_amount) }}</div>
      </div>
      <div class="db-kpi">
        <div class="db-kpi__k">HS đang học</div>
        <div class="db-kpi__v">{{ k.active_students }}</div>
        <div class="db-kpi__s">Chờ xếp lớp {{ k.waiting_students }} · Bảo lưu {{ k.reserved_students }}</div>
      </div>
      <div class="db-kpi" :class="{ warn: k.debt > 0 }">
        <div class="db-kpi__k">Công nợ</div>
        <div class="db-kpi__v">{{ money(k.debt) }}</div>
        <div class="db-kpi__s">{{ k.debt_count }} hợp đồng còn nợ</div>
      </div>
      <div class="db-kpi">
        <div class="db-kpi__k">Lead mới tháng</div>
        <div class="db-kpi__v">{{ k.leads }}</div>
        <div class="db-kpi__s">Mua gói {{ k.leads_bought }} · {{ pctText(k.conversion) }}</div>
      </div>
      <div class="db-kpi">
        <div class="db-kpi__k">Chuyên cần tháng</div>
        <div class="db-kpi__v">{{ pctText(k.attendance_rate) }}</div>
        <div class="db-kpi__s">Theo điểm danh các buổi đã học</div>
      </div>
      <div class="db-kpi">
        <div class="db-kpi__k">Lấp đầy lớp</div>
        <div class="db-kpi__v">{{ pctText(k.occupancy) }}</div>
        <div class="db-kpi__s">{{ k.class_count }} lớp đang chạy</div>
      </div>
    </div>

    <!-- Việc cần xử lý -->
    <div class="db-actions">
      <button type="button" class="db-action" :class="{ hot: k.pending_receipts }" @click="go('/lms/waitcharges')">
        <i class="fa-solid fa-receipt"></i><b>{{ k.pending_receipts }}</b> phiếu thu chờ duyệt<span>{{ money(k.pending_amount) }}</span>
      </button>
      <button type="button" class="db-action" :class="{ hot: d.near_end.length }" @click="scrollTo('db-near-end')">
        <i class="fa-solid fa-hourglass-end"></i><b>{{ d.near_end.length }}</b> HS sắp hết phí (≤ 4 buổi)
      </button>
      <button type="button" class="db-action" :class="{ hot: d.absent.length }" @click="scrollTo('db-absent')">
        <i class="fa-solid fa-user-xmark"></i><b>{{ d.absent.length }}</b> HS vắng liên tiếp
      </button>
      <button type="button" class="db-action" @click="go('/crm/parent/detail-list')">
        <i class="fa-solid fa-box-archive"></i><b>{{ k.warehouse }}</b> data trong kho
      </button>
    </div>

    <!-- Biểu đồ -->
    <div class="db-grid two">
      <div class="db-card">
        <div class="db-card__title">Thực thu 6 tháng gần nhất</div>
        <vue-apex-charts type="bar" height="260" :options="colOptions(d.revenue_trend.labels)" :series="[{ name: 'Thực thu', data: d.revenue_trend.values }]" />
      </div>
      <div class="db-card">
        <div class="db-card__title">Phễu chuyển đổi lead trong tháng</div>
        <vue-apex-charts type="bar" height="260" :options="barOptions(d.funnel.labels)" :series="[{ name: 'Số lead', data: d.funnel.values }]" />
      </div>
      <div class="db-card">
        <div class="db-card__title">Lead mới theo nguồn</div>
        <div v-if="!d.leads_by_source.values.length" class="db-empty">Chưa có lead trong tháng</div>
        <vue-apex-charts v-else type="bar" :height="chartH(d.leads_by_source.labels)" :options="barOptions(d.leads_by_source.labels)" :series="[{ name: 'Lead', data: d.leads_by_source.values }]" />
      </div>
      <div class="db-card">
        <div class="db-card__title">Giá trị hợp đồng mới theo sản phẩm</div>
        <div v-if="!d.contracts_by_product.values.length" class="db-empty">Chưa có hợp đồng mới trong tháng</div>
        <vue-apex-charts v-else type="bar" :height="chartH(d.contracts_by_product.labels)" :options="barOptions(d.contracts_by_product.labels, true)" :series="[{ name: 'Giá trị HĐ', data: d.contracts_by_product.values }]" />
      </div>
    </div>

    <!-- Theo cơ sở -->
    <div v-if="d.by_branch" class="db-card">
      <div class="db-card__title">Kết quả theo cơ sở</div>
      <div class="db-table-wrap">
        <table class="db-table">
          <thead><tr><th class="l c-branch">Cơ sở</th><th>Chỉ tiêu</th><th>Thực thu</th><th>% đạt</th><th>HS đang học</th><th>Lead mới</th></tr></thead>
          <tbody>
            <tr v-for="b in d.by_branch" :key="b.id">
              <td class="l c-branch"><span class="nowrap">{{ b.name }}</span></td>
              <td>{{ money(b.target) }}</td>
              <td><b>{{ money(b.actual) }}</b></td>
              <td><span class="db-pct" :class="pctCls(b.pct)">{{ pctText(b.pct) }}</span></td>
              <td>{{ b.students }}</td>
              <td>{{ b.leads }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="db-grid two">
      <!-- Xếp hạng sale -->
      <div class="db-card">
        <div class="db-card__title">Xếp hạng Sale tháng</div>
        <div v-if="!d.sale_ranking.length" class="db-empty">Chưa có nhân sự Sale</div>
        <div class="db-table-wrap" v-else>
          <table class="db-table">
            <thead><tr><th class="l">#</th><th class="l">Sale</th><th>Chỉ tiêu</th><th>Thực thu</th><th>% đạt</th><th>HĐ mới</th><th>Lead</th></tr></thead>
            <tbody>
              <tr v-for="(s, i) in d.sale_ranking" :key="s.id">
                <td class="l"><span class="db-rank" :class="'r' + (i + 1)">{{ i + 1 }}</span></td>
                <td class="l nowrap">{{ s.name }}</td>
                <td>{{ money(s.target) }}</td>
                <td><b>{{ money(s.actual) }}</b></td>
                <td><span class="db-pct" :class="pctCls(s.pct)">{{ pctText(s.pct) }}</span></td>
                <td>{{ s.contracts }}</td>
                <td>{{ s.leads }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <!-- Lớp lấp đầy thấp -->
      <div class="db-card">
        <div class="db-card__title">Lớp có tỉ lệ lấp đầy thấp</div>
        <div v-if="!d.classes_low.length" class="db-empty">Không có lớp đang chạy</div>
        <div class="db-table-wrap" v-else>
          <table class="db-table">
            <thead><tr><th class="l">Lớp</th><th class="l">Giáo viên</th><th>Sĩ số</th><th>Lấp đầy</th></tr></thead>
            <tbody>
              <tr v-for="c in d.classes_low" :key="c.id">
                <td class="l"><div class="db-strong">{{ c.cls_name }}</div><div class="db-sub nowrap">{{ c.branch_name }}</div></td>
                <td class="l">{{ c.teacher_name || '—' }}</td>
                <td>{{ c.students }}/{{ c.max_students || '?' }}</td>
                <td><span class="db-pct" :class="pctCls(c.fill)">{{ pctText(c.fill) }}</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="db-grid three">
      <div class="db-card" id="db-near-end">
        <div class="db-card__title">HS sắp hết phí <span class="db-pill">{{ d.near_end.length }}</span></div>
        <div v-if="!d.near_end.length" class="db-empty">Không có học sinh sắp hết phí</div>
        <div v-for="s in d.near_end" :key="'n' + s.contract_id" class="db-row" @click="go(`/lms/students/${s.student_id}/detail`)">
          <div><div class="db-strong">{{ s.name }}</div><div class="db-sub">{{ s.cls_name || 'Chưa xếp lớp' }}</div></div>
          <span class="db-badge warn">Còn {{ s.left_sessions }} buổi</span>
        </div>
      </div>
      <div class="db-card" id="db-absent">
        <div class="db-card__title">HS vắng liên tiếp <span class="db-pill">{{ d.absent.length }}</span></div>
        <div v-if="!d.absent.length" class="db-empty">Không có học sinh vắng liên tiếp</div>
        <div v-for="s in d.absent" :key="'a' + s.student_id" class="db-row" @click="go(`/lms/students/${s.student_id}/detail`)">
          <div><div class="db-strong">{{ s.name }}</div><div class="db-sub">{{ s.cls_name }} · gần nhất {{ fmtDate(s.last_date) }}</div></div>
          <span class="db-badge danger">Vắng {{ s.streak }} buổi</span>
        </div>
      </div>
      <div class="db-card">
        <div class="db-card__title">Công nợ lớn nhất</div>
        <div v-if="!d.debts.length" class="db-empty">Không có công nợ</div>
        <div v-for="c in d.debts" :key="'d' + c.contract_id" class="db-row" @click="go(`/lms/contracts/${c.contract_id}/detail`)">
          <div><div class="db-strong">{{ c.name }}</div><div class="db-sub">{{ c.code }} · EC {{ c.ec_name || '—' }}</div></div>
          <span class="db-money">{{ money(c.debt_amount) }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import dash from './dashMixin.js'

export default {
  name: 'OverviewDashboard',
  mixins: [dash],
  props: { d: { type: Object, required: true } },
  computed: { k() { return this.d.kpis || {} } }
}
</script>
