<template>
  <div class="ev-report sm-report">
    <div class="ev-rtoolbar">
      <div>
        <router-link to="/lms/evaluations/igbh/summative/students" class="ev-rtoolbar__back">
          <i class="feather icon-arrow-left"></i> Danh sách tổng kết học sinh
        </router-link>
        <h2 class="ev-rtoolbar__title">Báo cáo tổng kết 12 tuần IG.BH<span v-if="r"> · {{ r.stu_nm }}</span></h2>
      </div>
      <div v-if="r" class="ev-rtoolbar__btns">
        <router-link :to="`/lms/evaluations/igbh/summative/form/${r.id}`" class="ev-btn ev-btn--ghost ev-btn--sm">
          <i class="feather icon-edit-2"></i> Chấm điểm cuối kỳ
        </router-link>
        <button class="ev-btn ev-btn--primary ev-btn--sm" @click="printReport">
          <i class="feather icon-printer"></i> In / Lưu PDF
        </button>
      </div>
    </div>

    <div v-if="loading" class="ev-state print-hide"><div class="ev-spinner"></div>Đang tải dữ liệu báo cáo...</div>

    <div v-else-if="r" class="ev-doc-scroll">
      <div class="ev-doc">
        <!-- ================= TRANG 1 ================= -->
        <section class="ev-sheet">
          <header class="sm-head">
            <img src="@assets/images/logo/stemkey-logo.png" alt="STEMKEY" class="sm-head__logo" />
            <div class="sm-head__titles">
              <p class="sm-head__super">IG.BH SUMMATIVE REPORT</p>
              <h1 class="sm-head__title">Báo cáo đánh giá cuối kỳ</h1>
            </div>
            <div class="sm-head__score">
              <span>Tổng điểm</span>
              <strong>{{ fmtNum(r.total_score) }}</strong>
            </div>
          </header>

          <div class="ev-sheet__body">
            <div class="ev-info-grid">
              <div class="ev-info-cell span-2">
                <div class="ev-info-cell__label">Họ và tên học sinh</div>
                <div class="ev-info-cell__val is-accent">{{ r.stu_nm }}</div>
              </div>
              <div class="ev-info-cell">
                <div class="ev-info-cell__label">Mã học sinh</div>
                <div class="ev-info-cell__val">{{ r.stu_seq || '—' }}</div>
              </div>
              <div class="ev-info-cell">
                <div class="ev-info-cell__label">Ngày sinh</div>
                <div class="ev-info-cell__val">{{ fmtDate(r.date_of_birth) }}</div>
              </div>
              <div class="ev-info-cell">
                <div class="ev-info-cell__label">Lớp học</div>
                <div class="ev-info-cell__val">{{ r.class_nm || '—' }}</div>
              </div>
              <div class="ev-info-cell">
                <div class="ev-info-cell__label">Bài kiểm tra</div>
                <div class="ev-info-cell__val">{{ r.test_nm || '—' }}</div>
              </div>
              <div class="ev-info-cell">
                <div class="ev-info-cell__label">Giáo viên phụ trách</div>
                <div class="ev-info-cell__val">{{ r.teacher_nm || '—' }}</div>
              </div>
              <div class="ev-info-cell">
                <div class="ev-info-cell__label">Ngày đánh giá</div>
                <div class="ev-info-cell__val">{{ fmtDate(r.eval_dt) }}</div>
              </div>
            </div>

            <div class="sm-kpis ev-avoid-break">
              <div class="sm-kpi">
                <span>Bài tự luận cuối kỳ</span>
                <strong>{{ fmtNum(subjectiveTotal) }}<small> / {{ subjectiveMax }}</small></strong>
              </div>
              <div class="sm-kpi">
                <span>Sách bài tập 12 tuần</span>
                <strong>{{ fmtNum(d.total_workbook) }}<small> / {{ d.max_workbook }}</small></strong>
              </div>
              <div class="sm-kpi">
                <span>Số tuần có đánh giá</span>
                <strong>{{ weeksWithData }}<small> / 12</small></strong>
              </div>
              <div class="sm-kpi">
                <span>Thái độ · Năng lực (TB /5)</span>
                <strong>{{ avgOf(d.avg_attitude) }} · {{ avgOf(d.avg_detection) }}</strong>
              </div>
            </div>

            <h2 class="sm-sec"><span>1</span> Tiến trình học tập 12 tuần</h2>
            <table class="sm-table ev-avoid-break">
              <thead>
                <tr>
                  <th style="width: 9%;">Tuần</th>
                  <th>Nội dung đánh giá</th>
                  <th style="width: 13%;">Bài tập tuần<br />(/20)</th>
                  <th style="width: 12%;">Điểm sách<br />bài tập</th>
                  <th style="width: 11%;">Thái độ<br />(TB /5)</th>
                  <th style="width: 11%;">Năng lực<br />(TB /5)</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="w in d.report_data" :key="w.week" :class="{ 'is-empty': !w.attitude }">
                  <td class="is-c sm-strong">T.{{ w.week }}</td>
                  <td class="sm-theme">{{ w.theme_desc || '—' }}</td>
                  <td class="is-c">{{ w.weekly_workbook !== null ? w.weekly_workbook : '—' }}</td>
                  <td class="is-c sm-strong">{{ w.score !== null ? fmtNum(w.score) : '—' }}<span class="sm-max"> / {{ w.max_score }}</span></td>
                  <td class="is-c">{{ w.attitude ? avgOf(w.attitude) : '—' }}</td>
                  <td class="is-c">{{ w.detection ? avgOf(w.detection) : '—' }}</td>
                </tr>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="3" class="sm-strong">Tổng điểm sách bài tập</td>
                  <td class="is-c sm-total">{{ fmtNum(d.total_workbook) }} / {{ d.max_workbook }}</td>
                  <td class="is-c sm-strong">{{ avgOf(d.avg_attitude) }}</td>
                  <td class="is-c sm-strong">{{ avgOf(d.avg_detection) }}</td>
                </tr>
              </tfoot>
            </table>

            <h2 class="sm-sec"><span>2</span> Thái độ học tập &amp; năng lực quan sát</h2>
            <div class="sm-charts ev-avoid-break">
              <div class="sm-chart-box">
                <div class="sm-chart-title">Thái độ học tập trên lớp</div>
                <div ref="chartAttitude" class="sm-chart"></div>
                <div class="sm-chart-legend">
                  <span v-for="a in attitudeItems" :key="a.key">{{ a.label }}: <strong>{{ d.avg_attitude[a.key] }}</strong></span>
                </div>
              </div>
              <div class="sm-chart-box">
                <div class="sm-chart-title">Năng lực quan sát &amp; tư duy</div>
                <div ref="chartDetection" class="sm-chart"></div>
                <div class="sm-chart-legend">
                  <span v-for="a in detectionItems" :key="a.key">{{ a.label }}: <strong>{{ d.avg_detection[a.key] }}</strong></span>
                </div>
              </div>
            </div>
          </div>
          <footer class="ev-sheet__foot"><span>STEMKEY · IG.BH Summative</span><span>{{ r.stu_nm }} · Trang 1/2</span></footer>
        </section>

        <!-- ================= TRANG 2 ================= -->
        <section class="ev-sheet">
          <header class="sm-head is-slim">
            <img src="@assets/images/logo/stemkey-logo.png" alt="STEMKEY" class="sm-head__logo" />
            <div class="sm-head__titles">
              <h1 class="sm-head__title">{{ r.stu_nm }} · {{ r.class_nm || '' }}</h1>
            </div>
            <div class="sm-head__score"><span>Tổng điểm</span><strong>{{ fmtNum(r.total_score) }}</strong></div>
          </header>

          <div class="ev-sheet__body">
            <h2 class="sm-sec"><span>3</span> Kết quả bài kiểm tra tự luận cuối kỳ</h2>
            <table class="sm-table ev-avoid-break">
              <thead>
                <tr>
                  <th style="width: 16%;">Câu hỏi</th>
                  <th>Khái niệm<br />hiểu biết</th>
                  <th>Chiến lược<br />suy luận</th>
                  <th>Tính toán<br />thực hành</th>
                  <th>Diễn đạt<br />biểu hiện</th>
                  <th style="width: 16%;">Điểm đạt<br />(/6)</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="!subjectiveRows.length"><td colspan="6" class="is-c sm-empty">Chưa nhập điểm bài tự luận</td></tr>
                <tr v-for="s in subjectiveRows" :key="s.sort_no">
                  <td class="is-c sm-strong">Câu {{ s.sort_no }}</td>
                  <td class="is-c">{{ fmtNum(s.concept) }}</td>
                  <td class="is-c">{{ fmtNum(s.strategy) }}</td>
                  <td class="is-c">{{ fmtNum(s.calculation) }}</td>
                  <td class="is-c">{{ fmtNum(s.expression) }}</td>
                  <td class="is-c sm-total">{{ fmtNum(s.score) }}</td>
                </tr>
              </tbody>
              <tfoot v-if="subjectiveRows.length">
                <tr>
                  <td class="sm-strong">Trung bình</td>
                  <td v-for="k in ['concept','strategy','calculation','expression']" :key="k" class="is-c sm-strong">{{ colAvg(k) }}</td>
                  <td class="is-c sm-total">{{ fmtNum(subjectiveTotal) }} / {{ subjectiveMax }}</td>
                </tr>
              </tfoot>
            </table>

            <h2 class="sm-sec"><span>4</span> Đánh giá theo tiêu chí</h2>
            <div class="sm-criteria ev-avoid-break">
              <div v-for="grp in criteriaGroups" :key="grp.title" class="sm-criteria__col">
                <div class="sm-criteria__title">{{ grp.title }}</div>
                <div v-for="it in grp.items" :key="it.key" class="sm-crit">
                  <span class="sm-crit__label">{{ it.label }}</span>
                  <span class="sm-crit__bar"><span :style="{ width: (Number(grp.src[it.key]) / 5 * 100) + '%', background: grp.color }"></span></span>
                  <span class="sm-crit__val">{{ grp.src[it.key] }}<small>/5</small></span>
                  <span class="sm-crit__lv">{{ levelText(grp.src[it.key]) }}</span>
                </div>
              </div>
            </div>

            <h2 class="sm-sec"><span>5</span> Nhận xét của giáo viên</h2>
            <div class="sm-comment ev-avoid-break">
              {{ teacherComment || 'Học sinh có tinh thần học tập tốt, hoàn thành đầy đủ bài tập và tích cực tham gia các hoạt động tư duy trong suốt 12 tuần.' }}
            </div>

            <div class="ev-signatures ev-avoid-break">
              <div>
                <p class="ev-sig__title">Phụ huynh</p>
                <p class="ev-sig__note">(Ký, ghi rõ họ tên)</p>
                <div class="ev-sig__space"></div>
              </div>
              <div>
                <p class="ev-sig__title">Giáo viên phụ trách</p>
                <p class="ev-sig__note">(Ký, ghi rõ họ tên)</p>
                <div class="ev-sig__space"></div>
                <p class="ev-sig__name">{{ r.teacher_nm || '' }}</p>
              </div>
              <div>
                <p class="ev-sig__title">Trung tâm STEMKEY</p>
                <p class="ev-sig__note">Ngày {{ fmtDate(r.eval_dt) }}</p>
                <div class="ev-sig__space"></div>
                <p class="ev-sig__name">Ban Đào tạo &amp; Chuyên môn</p>
              </div>
            </div>
          </div>
          <footer class="ev-sheet__foot"><span>STEMKEY · IG.BH Summative</span><span>{{ r.stu_nm }} · Trang 2/2</span></footer>
        </section>
      </div>
    </div>
  </div>
</template>

<script>
import axios from '@/http/axios.js'
import * as echarts from 'echarts'
import '../../_shared/eval-ui.css'
import { fmtDate } from '../../_shared/helpers'

export default {
  name: 'IgbhSummativeReport',
  data() {
    return {
      d: null,
      loading: true,
      teacherComment: '',
      attitudeItems: [
        { key: 'listen', label: 'Lắng nghe' },
        { key: 'join', label: 'Tham gia' },
        { key: 'express', label: 'Thể hiện' },
        { key: 'coop', label: 'Hợp tác' }
      ],
      detectionItems: [
        { key: 'normal', label: 'Cơ bản' },
        { key: 'leadersh', label: 'Lãnh đạo' },
        { key: 'math', label: 'Toán học' },
        { key: 'creative', label: 'Sáng tạo' }
      ]
    }
  },
  computed: {
    r() {
      return this.d ? this.d.result : null
    },
    subjectiveRows() {
      return (this.d && this.d.subjective_details) || []
    },
    subjectiveTotal() {
      return this.subjectiveRows.reduce((a, s) => a + Number(s.score || 0), 0)
    },
    subjectiveMax() {
      return this.subjectiveRows.reduce((a, s) => a + Number(s.max_score || 6), 0) || 30
    },
    weeksWithData() {
      return ((this.d && this.d.report_data) || []).filter(w => w.attitude).length
    },
    criteriaGroups() {
      return [
        { title: 'Thái độ học tập', items: this.attitudeItems, src: this.d.avg_attitude || {}, color: '#ea580c' },
        { title: 'Năng lực quan sát & tư duy', items: this.detectionItems, src: this.d.avg_detection || {}, color: '#2563eb' }
      ]
    }
  },
  created() {
    this.fetchData()
  },
  beforeDestroy() {
    ['chartAttitude', 'chartDetection'].forEach(n => {
      const inst = this.$refs[n] && echarts.getInstanceByDom(this.$refs[n])
      if (inst) inst.dispose()
    })
  },
  methods: {
    fmtDate,
    fmtNum(v) {
      if (v === null || v === undefined || v === '') return '—'
      const n = Number(v)
      return Number.isInteger(n) ? String(n) : n.toFixed(1).replace(/\.0$/, '')
    },
    avgOf(obj) {
      if (!obj) return '—'
      const vals = Object.values(obj).map(Number)
      if (!vals.length) return '—'
      return (vals.reduce((a, b) => a + b, 0) / vals.length).toFixed(1)
    },
    colAvg(k) {
      if (!this.subjectiveRows.length) return '—'
      return (this.subjectiveRows.reduce((a, s) => a + Number(s[k] || 0), 0) / this.subjectiveRows.length).toFixed(1)
    },
    levelText(v) {
      const n = Number(v)
      if (n >= 4.5) return 'Xuất sắc'
      if (n >= 3.5) return 'Tốt'
      if (n >= 2.5) return 'Khá'
      if (n > 0) return 'Cần cố gắng'
      return '—'
    },
    fetchData() {
      this.loading = true
      axios.g(`/api/lms/evaluations/summative/results/${this.$route.params.id}/report`).then(res => {
        this.loading = false
        const d = res && res.data ? (res.data.data || res.data) : null
        if (d && (d.result || d.report_data)) {
          this.d = d
          let sa = d.subjective_analysis
          if (!sa && d.result && d.result.subjective_analysis) {
            try { sa = typeof d.result.subjective_analysis === 'string' ? JSON.parse(d.result.subjective_analysis) : d.result.subjective_analysis } catch (e) { sa = null }
          }
          this.teacherComment = (sa && sa.teacher_comment) || ''
          this.$nextTick(() => this.initCharts())
        }
      }).catch(() => { this.loading = false })
    },
    radar(el, items, src, color, fill) {
      if (!el) return
      const c = echarts.getInstanceByDom(el) || echarts.init(el, null, { renderer: 'svg' })
      c.setOption({
        animation: false,
        radar: {
          indicator: items.map(i => ({ name: i.label, max: 5 })),
          splitNumber: 5,
          radius: '66%',
          center: ['50%', '54%'],
          name: { textStyle: { fontSize: 13, color: '#1e293b', fontWeight: 'bold' } },
          splitArea: { areaStyle: { color: ['#ffffff', '#f8fafc'] } },
          splitLine: { lineStyle: { color: '#e2e8f0' } },
          axisLine: { lineStyle: { color: '#cbd5e1' } }
        },
        series: [{
          type: 'radar',
          symbolSize: 6,
          label: { show: true, fontSize: 13, color, formatter: p => p.value },
          data: [{ value: items.map(i => Number(src[i.key] || 0)), areaStyle: { color: fill }, lineStyle: { color, width: 2 }, itemStyle: { color } }]
        }]
      }, true)
    },
    initCharts() {
      if (!this.d) return
      this.radar(this.$refs.chartAttitude, this.attitudeItems, this.d.avg_attitude || {}, '#ea580c', 'rgba(234, 88, 12, 0.22)')
      this.radar(this.$refs.chartDetection, this.detectionItems, this.d.avg_detection || {}, '#2563eb', 'rgba(37, 99, 235, 0.22)')
    },
    printReport() {
      window.print()
    }
  }
}
</script>

<style scoped>
.sm-head { display: flex; align-items: center; gap: 5mm; background: linear-gradient(90deg, #b45309, #f59e0b); color: #ffffff; padding: 6mm 11mm; }
.sm-head.is-slim { padding: 4mm 11mm; }
.sm-head__logo { height: 13mm; background: rgba(255, 255, 255, 0.95); padding: 1.5mm 2.5mm; border-radius: 2mm; }
.sm-head.is-slim .sm-head__logo { height: 9mm; }
.sm-head__titles { flex: 1; border-left: 1px solid rgba(255, 255, 255, 0.35); padding-left: 5mm; min-width: 0; }
.sm-head__super { font-size: 13px; font-weight: 600; letter-spacing: 1.5px; color: #fef3c7; margin: 0; }
.sm-head__title { font-size: 21px; font-weight: 800; margin: 1mm 0 0; text-transform: uppercase; color: #ffffff; line-height: 1.2; }
.sm-head.is-slim .sm-head__title { font-size: 16px; text-transform: none; margin: 0; }
.sm-head__score { text-align: center; background: #ffffff; color: #b45309; border-radius: 2mm; padding: 1.5mm 4mm; white-space: nowrap; }
.sm-head__score span { display: block; font-size: 13px; font-weight: 600; }
.sm-head__score strong { font-size: 22px; font-weight: 900; }
.sm-head.is-slim .sm-head__score strong { font-size: 18px; }

.sm-kpis { display: grid; grid-template-columns: repeat(4, 1fr); gap: 3mm; margin-bottom: 6mm; }
.sm-kpi { border: 1px solid #fde68a; background: #fffbeb; border-radius: 2mm; padding: 2.5mm 3mm; }
.sm-kpi span { display: block; font-size: 13px; color: #92400e; white-space: nowrap; }
.sm-kpi strong { font-size: 19px; font-weight: 900; color: #78350f; }
.sm-kpi small { font-size: 13px; font-weight: 600; color: #b45309; }

.sm-sec { display: flex; align-items: center; gap: 2.5mm; font-size: 16px; font-weight: 800; color: #92400e; margin: 0 0 3mm; }
.sm-sec span { width: 7mm; height: 7mm; border-radius: 50%; background: #f59e0b; color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 13px; }

.sm-table { width: 100%; border-collapse: collapse; font-size: 13px; margin-bottom: 6mm; }
.sm-table th { background: #fef3c7; color: #78350f; font-weight: 700; border: 1px solid #f3dca0; padding: 1.8mm 2mm; text-align: center; line-height: 1.25; }
.sm-table td { border: 1px solid #e5e7eb; padding: 1.5mm 2.4mm; color: #334155; }
.sm-table tbody tr:nth-child(even) { background: #fafafa; }
.sm-table tbody tr.is-empty td { color: #94a3b8; }
.sm-table tfoot td { background: #fff7ed; border-top: 2px solid #f59e0b; }
.sm-table .is-c { text-align: center; }
.sm-theme { line-height: 1.35; }
.sm-strong { font-weight: 700; color: #1e293b; }
.sm-total { font-weight: 900; color: #dc2626; white-space: nowrap; }
.sm-max { color: #94a3b8; font-weight: 500; }
.sm-empty { color: #94a3b8; font-style: italic; padding: 5mm !important; }

.sm-charts { display: grid; grid-template-columns: 1fr 1fr; gap: 5mm; }
.sm-chart-box { border: 1px solid #e2e8f0; border-radius: 2mm; padding: 3mm; text-align: center; }
.sm-chart-title { font-size: 14px; font-weight: 800; color: #1e3a8a; }
.sm-chart { width: 100%; height: 58mm; }
.sm-chart-legend { display: flex; flex-wrap: wrap; justify-content: center; gap: 1mm 4mm; font-size: 13px; color: #64748b; }
.sm-chart-legend strong { color: #0f172a; }

.sm-criteria { display: grid; grid-template-columns: 1fr 1fr; gap: 5mm; margin-bottom: 6mm; }
.sm-criteria__col { border: 1px solid #e2e8f0; border-radius: 2mm; padding: 3mm 4mm; }
.sm-criteria__title { font-size: 14px; font-weight: 800; color: #1e293b; margin-bottom: 2mm; }
.sm-crit { display: grid; grid-template-columns: 22mm 1fr 13mm 22mm; align-items: center; gap: 2mm; padding: 1.2mm 0; font-size: 13px; }
.sm-crit__label { font-weight: 600; color: #334155; white-space: nowrap; }
.sm-crit__bar { height: 2.4mm; background: #eef2f7; border-radius: 99px; overflow: hidden; }
.sm-crit__bar span { display: block; height: 100%; border-radius: 99px; }
.sm-crit__val { font-weight: 800; color: #0f172a; text-align: right; }
.sm-crit__val small { color: #94a3b8; font-weight: 600; }
.sm-crit__lv { color: #64748b; white-space: nowrap; }

.sm-comment {
  border: 1px solid #e2e8f0;
  border-left: 4px solid #f59e0b;
  background: #fffdf7;
  border-radius: 2mm;
  padding: 4mm 5mm;
  font-size: 14px;
  line-height: 1.65;
  color: #334155;
  min-height: 24mm;
  white-space: pre-line;
}
</style>
