<template>
  <div class="ev-report ucrea-report">
    <!-- Thanh công cụ (ẩn khi in) -->
    <div class="ev-rtoolbar">
      <div>
        <router-link to="/lms/evaluations/ucrea" class="ev-rtoolbar__back">
          <i class="feather icon-arrow-left"></i> Danh sách đánh giá UCREA
        </router-link>
        <h2 class="ev-rtoolbar__title">Báo cáo năng lực UCREA<span v-if="result"> · {{ g.stu_nm }}</span></h2>
      </div>
      <div v-if="result" class="ev-rtoolbar__btns">
        <div v-if="allTests.length" class="uc-compare">
          <span class="ev-rtoolbar__hint">So sánh:</span>
          <label v-for="t in allTests" :key="t.test_cd" class="uc-check" :class="{ 'is-disabled': !t.hasData || t.isCurrent }">
            <input v-model="selectedTestIds" type="checkbox" :value="t.dataId" :disabled="!t.hasData || t.isCurrent" @change="initCharts" />
            {{ t.name }}
          </label>
        </div>
        <router-link :to="`/lms/evaluations/ucrea/form/${g.id}`" class="ev-btn ev-btn--ghost ev-btn--sm">
          <i class="feather icon-edit-2"></i> Sửa điểm
        </router-link>
        <button class="ev-btn ev-btn--print" title="In báo cáo hoặc lưu thành file PDF (Ctrl+P)" @click="exportPdf">
          <i class="feather icon-printer"></i> In báo cáo / Lưu PDF
        </button>
      </div>
    </div>

    <div v-if="loading" class="ev-state print-hide"><div class="ev-spinner"></div>Đang tải dữ liệu báo cáo...</div>

    <div v-else-if="result" class="ev-doc-scroll">
      <div class="ev-doc">
        <!-- ================= TRANG 1: TỔNG QUAN ================= -->
        <section class="ev-sheet">
          <header class="uc-head">
            <img src="@assets/images/logo/stemkey-logo.png" alt="STEMKEY" class="uc-head__logo" />
            <div class="uc-head__titles">
              <p class="uc-head__super">UCREA ASSESSMENT REPORT</p>
              <h1 class="uc-head__title">Báo cáo đánh giá năng lực · {{ periodName }}</h1>
            </div>
            <div class="uc-head__level">
              <span>Level</span>
              <strong>{{ g.level_cd }}</strong>
            </div>
          </header>

          <div class="ev-sheet__body">
            <div class="ev-info-grid">
              <div class="ev-info-cell span-2">
                <div class="ev-info-cell__label">Họ và tên học sinh</div>
                <div class="ev-info-cell__val is-accent">{{ g.stu_nm }}</div>
              </div>
              <div class="ev-info-cell">
                <div class="ev-info-cell__label">Mã học sinh</div>
                <div class="ev-info-cell__val">{{ g.erp_student_code || '—' }}</div>
              </div>
              <div class="ev-info-cell">
                <div class="ev-info-cell__label">Giới tính · Tuổi</div>
                <div class="ev-info-cell__val">{{ g.gender_nm || '—' }} · {{ g.age_nm || '—' }}</div>
              </div>
              <div class="ev-info-cell">
                <div class="ev-info-cell__label">Lớp học</div>
                <div class="ev-info-cell__val">{{ g.class_nm || '—' }}</div>
              </div>
              <div class="ev-info-cell">
                <div class="ev-info-cell__label">Bài kiểm tra</div>
                <div class="ev-info-cell__val">{{ g.test_nm || '—' }}</div>
              </div>
              <div class="ev-info-cell">
                <div class="ev-info-cell__label">Giáo viên đánh giá</div>
                <div class="ev-info-cell__val">{{ g.memb_nm || '—' }}</div>
              </div>
              <div class="ev-info-cell">
                <div class="ev-info-cell__label">Ngày kiểm tra</div>
                <div class="ev-info-cell__val">{{ fmtDate(g.eval_dt) }}</div>
              </div>
            </div>

            <h2 class="uc-sec">Kết quả theo lĩnh vực</h2>
            <div class="uc-summary ev-avoid-break">
              <div v-for="d in domains" :key="d.key" class="uc-summary__item">
                <div class="uc-summary__name">{{ d.name }}</div>
                <div class="uc-summary__grade" :class="'g-' + getGrade(d.name)">{{ getGrade(d.name) }}</div>
                <div class="uc-summary__label">{{ getGradeLabel(getGrade(d.name)) }}</div>
              </div>
            </div>

            <h2 class="uc-sec">Đánh giá tổng quát</h2>
            <div class="uc-row is-middle ev-avoid-break">
              <div class="uc-col-5"><div ref="chartOverall" class="uc-chart is-tall"></div></div>
              <div class="uc-col-7">
                <table class="uc-table">
                  <thead>
                    <tr class="uc-th-orange">
                      <th>Lĩnh vực</th>
                      <th style="width: 17%;" class="is-c">Cấp độ</th>
                      <th style="width: 22%;" class="is-c">TB học sinh</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="d in domains" :key="'o' + d.key">
                      <td>
                        <div class="uc-skill">{{ d.name }}</div>
                        <div class="uc-desc">Ở mức <strong class="uc-red">{{ getGradeLabel(getGrade(d.name)) }}</strong></div>
                      </td>
                      <td class="uc-grade" :class="'g-' + getGrade(d.name)">{{ getGrade(d.name) }}</td>
                      <td class="is-c uc-peer">B</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <h2 class="uc-sec">Lĩnh vực đánh giá &amp; thang cấp độ</h2>
            <div class="uc-row ev-avoid-break">
              <div class="uc-col-7">
                <table class="uc-table">
                  <tbody>
                    <tr v-for="d in domains" :key="'d' + d.key">
                      <td class="uc-cat">{{ d.name }}</td>
                      <td class="uc-desc">{{ d.desc }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="uc-col-5">
                <table class="uc-table is-center">
                  <thead>
                    <tr class="uc-th-sub"><th>Tiêu chuẩn</th><th>Cấp độ</th><th>Điểm</th></tr>
                  </thead>
                  <tbody>
                    <tr><td>Vượt trội</td><td class="uc-grade g-S">S</td><td>91~100</td></tr>
                    <tr><td>Rất tốt</td><td class="uc-grade g-A">A</td><td>71~90</td></tr>
                    <tr><td>Trung bình</td><td class="uc-grade g-B">B</td><td>41~70</td></tr>
                    <tr><td>Yếu</td><td class="uc-grade g-C">C</td><td>0~40</td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <footer class="ev-sheet__foot"><span>STEMKEY · UCREA Assessment</span><span>{{ g.stu_nm }} · Trang 1/3</span></footer>
        </section>

        <!-- ================= TRANG 2 & 3: CHI TIẾT ================= -->
        <section v-for="(pg, pIdx) in detailPages" :key="'pg' + pIdx" class="ev-sheet">
          <header class="uc-head is-slim">
            <img src="@assets/images/logo/stemkey-logo.png" alt="STEMKEY" class="uc-head__logo" />
            <div class="uc-head__titles">
              <p class="uc-head__super">KẾT QUẢ CHI TIẾT · {{ periodName.toUpperCase() }}</p>
              <h1 class="uc-head__title">{{ g.stu_nm }}<span v-if="g.class_nm"> · {{ g.class_nm }}</span></h1>
            </div>
            <div class="uc-head__level"><span>Level</span><strong>{{ g.level_cd }}</strong></div>
          </header>

          <div class="ev-sheet__body">
            <div v-for="sec in pg" :key="sec.ref" class="ev-avoid-break uc-block">
              <h2 class="uc-sec">
                {{ sec.title }}
                <span class="uc-sec__grade" :class="'g-' + getGrade(sec.title)">{{ getGrade(sec.title) }} · {{ getGradeLabel(getGrade(sec.title)) }}</span>
              </h2>
              <div class="uc-row is-middle">
                <div class="uc-col-5"><div :ref="sec.ref" class="uc-chart"></div></div>
                <div class="uc-col-7">
                  <table class="uc-table">
                    <thead>
                      <tr class="uc-th-blue"><th>Kỹ năng &amp; nội dung đánh giá</th><th style="width: 17%;" class="is-c">Cấp độ</th></tr>
                    </thead>
                    <tbody>
                      <tr v-for="s in sec.skills" :key="s.name">
                        <td>
                          <div class="uc-skill">{{ s.name }}</div>
                          <div class="uc-desc">{{ s.desc }}</div>
                        </td>
                        <td class="uc-grade" :class="'g-' + getGrade(s.name)">{{ getGrade(s.name) }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <template v-if="pIdx === detailPages.length - 1">
              <div class="ev-avoid-break uc-block">
                <h2 class="uc-sec">Nhận xét &amp; định hướng</h2>
                <div class="uc-reco">
                  <div class="uc-reco__col is-good">
                    <div class="uc-reco__title"><i class="feather icon-thumbs-up"></i> Điểm mạnh</div>
                    <ul v-if="strengths.length">
                      <li v-for="s in strengths" :key="s.name"><strong>{{ s.name }}</strong> ({{ s.grade }}) – {{ s.desc }}</li>
                    </ul>
                    <p v-else>Chưa có kỹ năng đạt mức A trở lên – cần tiếp tục rèn luyện đều các lĩnh vực.</p>
                  </div>
                  <div class="uc-reco__col is-improve">
                    <div class="uc-reco__title"><i class="feather icon-trending-up"></i> Cần cải thiện</div>
                    <ul v-if="improvements.length">
                      <li v-for="s in improvements" :key="s.name"><strong>{{ s.name }}</strong> ({{ s.grade }}) – {{ s.desc }}</li>
                    </ul>
                    <p v-else>Tất cả kỹ năng đều đạt mức Rất tốt trở lên. Nên thử thách con với nội dung nâng cao.</p>
                  </div>
                </div>
                <div class="uc-note">
                  <div class="uc-note__label">Ý kiến của giáo viên</div>
                  <div class="uc-note__line"></div>
                  <div class="uc-note__line"></div>
                </div>
              </div>

              <div class="ev-signatures ev-avoid-break">
                <div>
                  <p class="ev-sig__title">Phụ huynh</p>
                  <p class="ev-sig__note">(Ký, ghi rõ họ tên)</p>
                  <div class="ev-sig__space"></div>
                </div>
                <div>
                  <p class="ev-sig__title">Giáo viên đánh giá</p>
                  <p class="ev-sig__note">(Ký, ghi rõ họ tên)</p>
                  <div class="ev-sig__space"></div>
                  <p class="ev-sig__name">{{ g.memb_nm || '' }}</p>
                </div>
                <div>
                  <p class="ev-sig__title">Trung tâm STEMKEY</p>
                  <p class="ev-sig__note">Ngày {{ fmtDate(g.eval_dt) }}</p>
                  <div class="ev-sig__space"></div>
                </div>
              </div>
            </template>
          </div>
          <footer class="ev-sheet__foot"><span>STEMKEY · UCREA Assessment</span><span>{{ g.stu_nm }} · Trang {{ pIdx + 2 }}/3</span></footer>
        </section>
      </div>
    </div>
  </div>
</template>

<script>
import axios from '@/http/axios.js'
import * as echarts from 'echarts'
import '../_shared/eval-ui.css'
import { fmtDate, autoPrintIfRequested, installPrintFit } from '../_shared/helpers'

const PERIOD_COLORS = { TT001: '#94a3b8', TT002: '#1e3a8a', TT003: '#65a30d' }
const PEER_AVG = [66, 71, 69, 61]

export default {
  name: 'UcreaReportView',
  data() {
    return {
      result: null,
      loading: true,
      selectedTestIds: [],
      domains: [
        { key: 'co_ban', name: 'Tư duy cơ bản', short: 'Cơ bản', desc: 'Sự chú ý và tập trung, quan sát và ghi nhớ – nhân tố cơ bản cho sự phát triển tư duy.' },
        { key: 'toan', name: 'Tư duy toán học', short: 'Toán học', desc: 'Năng lực về số và phép toán, không gian, đo lường, kiểu mẫu và thống kê.' },
        { key: 'logic', name: 'Tư duy logic', short: 'Logic', desc: 'Sự hiểu biết, áp dụng, phân tích và tổng hợp – kỹ năng cốt lõi để phát triển tư duy.' },
        { key: 'sang_tao', name: 'Tư duy sáng tạo', short: 'Sáng tạo', desc: 'Sự lưu loát, linh hoạt, độc đáo và tinh tế – nền tảng cho tư duy đa dạng và sâu sắc.' }
      ]
    }
  },
  computed: {
    g() {
      return this.result ? this.result.general : {}
    },
    isL1L2() {
      return this.result && ['L1', 'L2'].includes(this.g.level_cd)
    },
    periodName() {
      return { TT001: 'Đầu kỳ', TT002: 'Giữa kỳ', TT003: 'Cuối kỳ' }[this.g.test_cd] || this.reportData.name || 'Đầu kỳ'
    },
    sections() {
      const logic = [
        { name: 'Hiểu biết', desc: 'Phân tích dữ kiện liên quan và hiểu vấn đề cần giải quyết.' },
        { name: 'Ứng dụng', desc: 'Vận dụng linh hoạt những điều đã học vào thực hành.' },
        { name: 'Phân tích', desc: 'So sánh các thuộc tính và khái quát hóa vấn đề.' }
      ]
      if (!this.isL1L2) logic.push({ name: 'Tổng hợp', desc: 'Phân tích tình huống và giải quyết toàn diện vấn đề.' })
      return [
        {
          ref: 'chart1', title: 'Tư duy cơ bản', keys: ['sk11', 'sk12', 'sk13'],
          skills: [
            { name: 'Năng lực chú ý', desc: 'Tập trung quan sát để giải quyết vấn đề.' },
            { name: 'Năng lực quan sát', desc: 'Quan sát tỉ mỉ một sự vật nào đó.' },
            { name: 'Năng lực ghi nhớ', desc: 'Ghi nhớ thông tin mới về sự vật, sự việc.' }
          ]
        },
        {
          ref: 'chart2', title: 'Tư duy toán học', keys: ['kn11', 'kn12', 'kn13', 'kn14'],
          skills: [
            { name: 'Số và tính toán', desc: 'Hiểu khái niệm tính toán cơ bản và ý nghĩa của toán học trong đời sống.' },
            { name: 'Hình học không gian', desc: 'Nhận biết vị trí, hướng trong không gian và hiểu biết về hình dạng.' },
            { name: 'Đo lường', desc: 'Thể hiện số lượng bằng số và so sánh các đồ vật với nhau.' },
            { name: 'Kiểu mẫu', desc: 'Nhận diện và dự đoán mối quan hệ mang tính quy luật.' }
          ]
        },
        {
          ref: 'chart3', title: 'Tư duy logic', keys: this.isL1L2 ? ['sk21', 'sk22', 'sk23'] : ['sk21', 'sk22', 'sk23', 'sk24'],
          skills: logic
        },
        {
          ref: 'chart4', title: 'Tư duy sáng tạo', keys: ['sk31', 'sk32', 'sk33', 'sk34'],
          skills: [
            { name: 'Sự trôi chảy', desc: 'Thể hiện được nhiều ý tưởng mới lạ.' },
            { name: 'Tính linh hoạt', desc: 'Tìm ra ý tưởng đa dạng bằng cách vận dụng một đối tượng thống nhất.' },
            { name: 'Tính độc đáo', desc: 'Đưa ra ý tưởng độc đáo, mới lạ hoặc hoàn toàn khác biệt.' },
            { name: 'Tính chính xác', desc: 'Bổ sung chi tiết cho ý tưởng có tính phức tạp cao.' }
          ]
        }
      ]
    },
    detailPages() {
      return [this.sections.slice(0, 3), this.sections.slice(3)]
    },
    gradedSkills() {
      const out = []
      this.sections.forEach(sec => sec.skills.forEach(s => out.push({ ...s, grade: this.getGrade(s.name) })))
      return out
    },
    strengths() {
      return this.gradedSkills.filter(s => s.grade === 'S' || s.grade === 'A').slice(0, 6)
    },
    improvements() {
      return this.gradedSkills.filter(s => s.grade === 'B' || s.grade === 'C').slice(0, 6)
    },
    availableTests() {
      if (!this.result || !this.result.related_results) return []
      return this.result.related_results.map(r => {
        let rd = {}
        try { rd = typeof r.report_data === 'string' ? JSON.parse(r.report_data) : r.report_data } catch (e) {}
        return { id: r.id, test_cd: r.test_cd, name: (rd && rd.name) ? rd.name : r.test_nm, reportData: rd || {} }
      }).sort((a, b) => (a.test_cd || '').localeCompare(b.test_cd || ''))
    },
    allTests() {
      return [
        { test_cd: 'TT001', name: 'Đầu kỳ' },
        { test_cd: 'TT002', name: 'Giữa kỳ' },
        { test_cd: 'TT003', name: 'Cuối kỳ' }
      ].map(type => {
        const found = this.availableTests.find(t => t.test_cd === type.test_cd)
        return {
          test_cd: type.test_cd,
          name: type.name,
          hasData: !!found,
          dataId: found ? found.id : null,
          isCurrent: !!(found && found.id == this.g.id)
        }
      })
    },
    reportData() {
      if (!this.result || !this.g.report_data) return {}
      try { return typeof this.g.report_data === 'string' ? JSON.parse(this.g.report_data) : this.g.report_data } catch (e) { return {} }
    },
    parsedSkillsGrade() {
      if (!this.result || !this.g.skills_grade) return []
      try { return typeof this.g.skills_grade === 'string' ? JSON.parse(this.g.skills_grade) : this.g.skills_grade } catch (e) { return [] }
    }
  },
  created() {
    this.fetchResult()
  },
  mounted() {
    installPrintFit(this)
  },
  beforeDestroy() {
    this.disposeCharts()
  },
  methods: {
    fmtDate,
    fetchResult() {
      this.loading = true
      axios.g(`/api/lms/evaluations/ucrea/results/${this.$route.params.id}`).then(response => {
        this.loading = false
        if (response && response.data && response.data.status === 'success') {
          this.result = response.data.data
          this.selectedTestIds = this.availableTests.map(t => t.id)
          this.$nextTick(() => {
            this.initCharts()
            autoPrintIfRequested(this)
          })
        }
      }).catch(() => { this.loading = false })
    },
    getGrade(skillName) {
      const found = this.parsedSkillsGrade.find(s => s.skill === skillName)
      if (found) return found.grade === 'L' ? 'C' : found.grade
      return 'B'
    },
    getGradeLabel(grade) {
      return { S: 'Vượt trội', A: 'Rất tốt', B: 'Trung bình', C: 'Yếu', L: 'Yếu' }[grade] || grade || 'Trung bình'
    },
    exportPdf() {
      window.print()
    },
    refEl(name) {
      const r = this.$refs[name]
      return Array.isArray(r) ? r[0] : r
    },
    disposeCharts() {
      ['chart1', 'chart2', 'chart3', 'chart4', 'chartOverall'].forEach(n => {
        const el = this.refEl(n)
        const inst = el && echarts.getInstanceByDom(el)
        if (inst) inst.dispose()
      })
    },
    getChart(name) {
      const el = this.refEl(name)
      if (!el) return null
      // SVG renderer → nét khi in / lưu PDF
      return echarts.getInstanceByDom(el) || echarts.init(el, null, { renderer: 'svg' })
    },
    // Ngắt nhãn trục X thành 2 dòng cân đối để không đè lên nhau (vd "Hình học\nkhông gian", "Phân\ntích")
    labelOf(name) {
      const words = String(name).split(' ')
      if (words.length < 2) return name
      const k = Math.floor(words.length / 2)
      return words.slice(0, k).join(' ') + '\n' + words.slice(k).join(' ')
    },
    initCharts() {
      const selected = this.availableTests.filter(t => this.selectedTestIds.includes(t.id))
      if (!selected.length) return
      const legend = {
        top: 4, left: 'center', data: selected.map(t => t.name),
        itemWidth: 14, itemHeight: 10, itemGap: 10, textStyle: { fontSize: 13, color: '#475569' }
      }
      const base = {
        animation: false,
        grid: { top: 36, bottom: 44, left: 36, right: 10 },
        tooltip: { trigger: 'axis' },
        yAxis: { type: 'value', max: 100, interval: 25, axisLabel: { fontSize: 13, color: '#64748b' }, splitLine: { lineStyle: { color: '#eef2f7' } } },
      }
      const bar = (t, data) => ({
        name: t.name, type: 'bar', data, barMaxWidth: 16,
        itemStyle: { color: PERIOD_COLORS[t.test_cd] || '#ef4444', barBorderRadius: [3, 3, 0, 0] },
        label: { show: selected.length === 1, position: 'top', fontSize: 13, color: '#334155' }
      })

      this.sections.forEach(sec => {
        const c = this.getChart(sec.ref)
        if (!c) return
        c.setOption({
          ...base,
          legend,
          xAxis: { type: 'category', data: sec.skills.map(s => this.labelOf(s.name)), axisTick: { show: false }, axisLabel: { interval: 0, fontSize: 13, color: '#334155', lineHeight: 15 } },
          series: selected.map(t => bar(t, sec.keys.map(k => Number(t.reportData[k] || 0))))
        }, true)
      })

      const c = this.getChart('chartOverall')
      if (c) {
        const avg = (rd, keys) => Math.round(keys.reduce((a, k) => a + Number(rd[k] || 0), 0) / keys.length)
        const series = selected.map(t => bar(t, this.sections.map(sec => avg(t.reportData, sec.keys))))
        series.push({ name: 'TB học sinh', type: 'bar', data: PEER_AVG, barMaxWidth: 16, itemStyle: { color: '#f59e0b', barBorderRadius: [3, 3, 0, 0] } })
        c.setOption({
          ...base,
          legend: { ...legend, data: [...selected.map(t => t.name), 'TB học sinh'] },
          xAxis: { type: 'category', data: this.domains.map(d => d.short), axisTick: { show: false }, axisLabel: { interval: 0, fontSize: 13, color: '#334155', lineHeight: 15 } },
          series
        }, true)
      }
    }
  }
}
</script>

<style scoped>
.uc-compare { display: inline-flex; align-items: center; gap: 10px; padding: 0 10px; border-right: 1px solid #e2e8f0; margin-right: 2px; }
.uc-check { display: inline-flex; align-items: center; gap: 5px; font-size: 13.5px; font-weight: 600; color: #334155; cursor: pointer; white-space: nowrap; }
.uc-check input { width: 16px; height: 16px; accent-color: #7367f0; cursor: pointer; }
.uc-check.is-disabled { color: #94a3b8; cursor: not-allowed; }
.uc-check.is-disabled input { cursor: not-allowed; }

/* Header tờ báo cáo */
.uc-head { display: flex; align-items: center; gap: 5mm; background: #1b3664; color: #ffffff; padding: 5mm 11mm; }
.uc-head.is-slim { padding: 3.5mm 11mm; }
.uc-head__logo { height: 12mm; background: rgba(255, 255, 255, 0.95); padding: 1.5mm 2.5mm; border-radius: 2mm; }
.uc-head.is-slim .uc-head__logo { height: 9mm; }
.uc-head__titles { flex: 1; border-left: 1px solid rgba(255, 255, 255, 0.3); padding-left: 5mm; min-width: 0; }
.uc-head__super { font-size: 13px; font-weight: 600; letter-spacing: 1.5px; color: rgba(255, 255, 255, 0.75); margin: 0; }
.uc-head__title { font-size: 20px; font-weight: 800; color: #f5c518; margin: 1mm 0 0; text-transform: uppercase; line-height: 1.2; }
.uc-head.is-slim .uc-head__title { font-size: 16px; color: #ffffff; text-transform: none; }
.uc-head__level { text-align: center; background: rgba(255, 255, 255, 0.12); border-radius: 2mm; padding: 1.5mm 4mm; white-space: nowrap; }
.uc-head__level span { display: block; font-size: 13px; color: rgba(255, 255, 255, 0.75); }
.uc-head__level strong { font-size: 20px; color: #ffffff; }
.uc-head.is-slim .uc-head__level strong { font-size: 17px; }

/* Tóm tắt 4 lĩnh vực */
.uc-summary { display: grid; grid-template-columns: repeat(4, 1fr); gap: 3mm; margin-bottom: 6mm; }
.uc-summary__item { border: 1px solid #e2e8f0; border-radius: 2mm; text-align: center; padding: 3mm 2mm; background: #f8fafc; }
.uc-summary__name { font-size: 14px; font-weight: 700; color: #334155; }
.uc-summary__grade { font-size: 32px; font-weight: 900; line-height: 1.15; margin-top: 1mm; }
.uc-summary__label { font-size: 13px; color: #64748b; }

.uc-sec {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 16px;
  font-weight: 800;
  color: #1b3664;
  border-bottom: 2px solid #1b3664;
  padding-bottom: 1.5mm;
  margin: 0 0 3.5mm;
}
.uc-sec__grade { font-size: 14px; font-weight: 800; }
.uc-block { margin-bottom: 6mm; }

.uc-row { display: flex; gap: 5mm; margin-bottom: 6mm; }
.uc-block .uc-row { margin-bottom: 0; }
.uc-row.is-middle { align-items: center; }
.uc-col-7 { flex: 7; min-width: 0; }
.uc-col-5 { flex: 5; min-width: 0; }

.uc-chart { width: 100%; height: 58mm; border: 1px solid #e2e8f0; border-radius: 2mm; background: #ffffff; }
.uc-chart.is-tall { height: 64mm; }

.uc-table { width: 100%; border-collapse: collapse; font-size: 13px; line-height: 1.35; }
.uc-table td, .uc-table th { border: 1px solid #dbe3ec; padding: 1.6mm 2.4mm; }
.uc-table.is-center td, .uc-table.is-center th, .uc-table .is-c { text-align: center; }
.uc-table tbody tr:nth-child(even) { background: #f6f9fc; }
.uc-th-orange th { background: #e86036; color: #ffffff; font-weight: 700; text-align: left; }
.uc-th-orange th.is-c { text-align: center; }
.uc-th-sub th { background: #f1f5f9; color: #334155; font-weight: 700; }
.uc-th-blue th { background: #0050a0; color: #ffffff; font-weight: 700; text-align: left; }
.uc-th-blue th.is-c { text-align: center; }
.uc-cat { width: 30%; font-weight: 700; color: #1e293b; background: #f8fafc; white-space: nowrap; }
.uc-skill { font-weight: 700; color: #1e293b; font-size: 13.5px; }
.uc-desc { color: #64748b; font-size: 13px; }
.uc-grade { text-align: center; font-weight: 900; font-size: 17px; }
.uc-peer { color: #64748b; font-weight: 700; }
.uc-red { color: #dc2626; }
.g-S { color: #7c3aed; }
.g-A { color: #2563eb; }
.g-B { color: #d97706; }
.g-C { color: #dc2626; }

/* Nhận xét */
.uc-reco { display: grid; grid-template-columns: 1fr 1fr; gap: 4mm; }
.uc-reco__col { border-radius: 2mm; padding: 3mm 4mm; font-size: 13px; line-height: 1.5; color: #334155; }
.uc-reco__col.is-good { background: #f0fdf4; border: 1px solid #bbf7d0; }
.uc-reco__col.is-improve { background: #fff7ed; border: 1px solid #fed7aa; }
.uc-reco__title { font-size: 14px; font-weight: 800; margin-bottom: 1.5mm; }
.is-good .uc-reco__title { color: #15803d; }
.is-improve .uc-reco__title { color: #c2410c; }
.uc-reco__col ul { margin: 0; padding-left: 4.5mm; list-style: disc; }
.uc-reco__col li { margin-bottom: 1mm; }
.uc-reco__col p { margin: 0; }
.uc-note { margin-top: 4mm; }
.uc-note__label { font-size: 13.5px; font-weight: 700; color: #334155; }
.uc-note__line { border-bottom: 1px dotted #94a3b8; height: 8mm; }
</style>
