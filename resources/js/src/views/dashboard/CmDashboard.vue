<template>
  <div class="db-wrap">
    <div class="db-kpis">
      <div class="db-kpi brand">
        <div class="db-kpi__k">HS đang phụ trách</div>
        <div class="db-kpi__v">{{ k.students }}</div>
        <div class="db-kpi__s">{{ k.classes }} lớp quản lý</div>
      </div>
      <div class="db-kpi">
        <div class="db-kpi__k">Chuyên cần tháng</div>
        <div class="db-kpi__v">{{ pctText(k.attendance_rate) }}</div>
        <div class="db-kpi__s">Theo các buổi đã điểm danh</div>
      </div>
      <div class="db-kpi" :class="{ warn: k.pending_attendance }">
        <div class="db-kpi__k">Lượt chưa điểm danh</div>
        <div class="db-kpi__v">{{ k.pending_attendance }}</div>
        <div class="db-kpi__s">Các buổi đã diễn ra trong tháng</div>
      </div>
      <div class="db-kpi" :class="{ warn: k.near_end }">
        <div class="db-kpi__k">Sắp hết phí</div>
        <div class="db-kpi__v">{{ k.near_end }}</div>
        <div class="db-kpi__s">Còn ≤ 4 buổi – cần tư vấn tái phí</div>
      </div>
      <div class="db-kpi" :class="{ danger: k.absent_streak }">
        <div class="db-kpi__k">Vắng liên tiếp</div>
        <div class="db-kpi__v">{{ k.absent_streak }}</div>
        <div class="db-kpi__s">Vắng ≥ 2 buổi gần nhất</div>
      </div>
      <div class="db-kpi">
        <div class="db-kpi__k">Bảo lưu / Hết phí tháng</div>
        <div class="db-kpi__v">{{ k.reserved }} <small>/ {{ k.ended_month }}</small></div>
        <div class="db-kpi__s">Theo dõi để kéo học lại</div>
      </div>
      <div class="db-kpi" :class="{ warn: k.debt > 0 }">
        <div class="db-kpi__k">Công nợ HS phụ trách</div>
        <div class="db-kpi__v">{{ money(k.debt) }}</div>
        <div class="db-kpi__s">{{ k.debt_count }} hợp đồng</div>
      </div>
    </div>

    <div class="db-grid three">
      <div class="db-card">
        <div class="db-card__title">HS sắp hết phí <span class="db-pill">{{ d.near_end.length }}</span></div>
        <div v-if="!d.near_end.length" class="db-empty">Không có học sinh sắp hết phí</div>
        <div v-for="s in d.near_end" :key="'n' + s.contract_id" class="db-row" @click="go(`/lms/students/${s.student_id}/detail`)">
          <div><div class="db-strong">{{ s.name }}</div><div class="db-sub">{{ s.cls_name || 'Chưa xếp lớp' }} · PH {{ s.gud_mobile1 || '—' }}</div></div>
          <span class="db-badge warn">Còn {{ s.left_sessions }} buổi</span>
        </div>
      </div>
      <div class="db-card">
        <div class="db-card__title">HS vắng liên tiếp <span class="db-pill">{{ d.absent.length }}</span></div>
        <div v-if="!d.absent.length" class="db-empty">Không có học sinh vắng liên tiếp</div>
        <div v-for="s in d.absent" :key="'a' + s.student_id" class="db-row" @click="go(`/lms/students/${s.student_id}/detail`)">
          <div><div class="db-strong">{{ s.name }}</div><div class="db-sub">{{ s.cls_name }} · PH {{ s.gud_mobile1 || '—' }}</div></div>
          <span class="db-badge danger">Vắng {{ s.streak }} buổi</span>
        </div>
      </div>
      <div class="db-card">
        <div class="db-card__title">Sinh nhật 7 ngày tới <span class="db-pill">{{ d.birthdays.length }}</span></div>
        <div v-if="!d.birthdays.length" class="db-empty">Không có sinh nhật trong tuần</div>
        <div v-for="s in d.birthdays" :key="'b' + s.student_id" class="db-row" @click="go(`/lms/students/${s.student_id}/detail`)">
          <div><div class="db-strong">{{ s.name }}</div><div class="db-sub">{{ s.cls_name || '' }}</div></div>
          <span class="db-badge brand"><i class="fa-solid fa-cake-candles mr-1"></i>{{ birthday(s.date_of_birth) }}</span>
        </div>
      </div>
    </div>

    <div class="db-grid two">
      <div class="db-card">
        <div class="db-card__title">Lớp đang quản lý</div>
        <div v-if="!d.classes.length" class="db-empty">Chưa được gán quản lý lớp nào</div>
        <div class="db-table-wrap" v-else>
          <table class="db-table">
            <thead><tr><th class="l">Lớp</th><th class="l">Giáo viên</th><th>Sĩ số</th><th>Chuyên cần tháng</th></tr></thead>
            <tbody>
              <tr v-for="c in d.classes" :key="c.id">
                <td class="l db-strong">{{ c.cls_name }}</td>
                <td class="l">{{ c.teacher_name || '—' }}</td>
                <td>{{ c.students }}/{{ c.max_students || '?' }}</td>
                <td><span class="db-pct" :class="attCls(c.attendance_rate)">{{ c.attendance_rate === null ? '—' : c.attendance_rate + '%' }}</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="db-card">
        <div class="db-card__title">Hợp đồng còn công nợ</div>
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
  name: 'CmDashboard',
  mixins: [dash],
  props: { d: { type: Object, required: true } },
  computed: { k() { return this.d.kpis || {} } },
  methods: {
    attCls(v) { if (v === null || v === undefined) return ''; const n = Number(v); return n >= 90 ? 'good' : n >= 75 ? 'mid' : 'low' },
    birthday(d) { const s = String(d || '').slice(5, 10).split('-'); return s.length === 2 ? `${s[1]}/${s[0]}` : '' }
  }
}
</script>
