<template>
  <div class="ev-report dg-report">
    <div class="ev-rtoolbar">
      <div>
        <router-link to="/lms/evaluations/igbh/diagnostic" class="ev-rtoolbar__back">
          <i class="feather icon-arrow-left"></i> Danh sách khảo sát đầu vào
        </router-link>
        <h2 class="ev-rtoolbar__title">Báo cáo khảo sát đầu vào IG.BH<span v-if="general"> · {{ general.stu_nm }}</span></h2>
      </div>
      <div class="ev-rtoolbar__btns">
        <router-link :to="`/lms/evaluations/igbh/diagnostic/form/${resultId}`" class="ev-btn ev-btn--ghost ev-btn--sm">
          <i class="feather icon-edit-2"></i> Sửa điểm
        </router-link>
        <button class="ev-btn ev-btn--primary ev-btn--sm" @click="printReport">
          <i class="feather icon-printer"></i> In / Lưu PDF
        </button>
      </div>
    </div>

    <div v-if="loading" class="ev-state print-hide"><div class="ev-spinner"></div>Đang tải dữ liệu báo cáo...</div>

    <div v-else-if="general" class="ev-doc-scroll">
      <div class="ev-doc">
        <!-- ================= TRANG 1: KIẾN THỨC CƠ BẢN ================= -->
        <section class="ev-sheet">
          <header class="dg-head">
            <img src="@assets/images/logo/stemkey-logo.png" alt="STEMKEY" class="dg-head__logo" />
            <div class="dg-head__titles">
              <p class="dg-head__super">IG.BH DIAGNOSTIC TEST · PHẦN 1</p>
              <h1 class="dg-head__title">Đánh giá kiến thức cơ bản</h1>
            </div>
            <div class="dg-head__level">
              <span>Level gán</span>
              <strong>{{ general.assigned_level || '—' }}</strong>
            </div>
          </header>

          <div class="ev-sheet__body">
            <div class="ev-info-grid">
              <div class="ev-info-cell span-2">
                <div class="ev-info-cell__label">Họ và tên học sinh</div>
                <div class="ev-info-cell__val is-accent">{{ general.stu_nm }}</div>
              </div>
              <div class="ev-info-cell">
                <div class="ev-info-cell__label">Mã học sinh</div>
                <div class="ev-info-cell__val">{{ general.erp_student_code || general.stu_seq || '—' }}</div>
              </div>
              <div class="ev-info-cell">
                <div class="ev-info-cell__label">Ngày sinh</div>
                <div class="ev-info-cell__val">{{ fmtDate(general.date_of_birth || general.stu_birth_dt) }}</div>
              </div>
              <div class="ev-info-cell span-2">
                <div class="ev-info-cell__label">Bài khảo sát</div>
                <div class="ev-info-cell__val">{{ general.test_nm || '—' }}</div>
              </div>
              <div class="ev-info-cell">
                <div class="ev-info-cell__label">Người chấm</div>
                <div class="ev-info-cell__val">{{ general.reg_name || '—' }}</div>
              </div>
              <div class="ev-info-cell">
                <div class="ev-info-cell__label">Ngày thi</div>
                <div class="ev-info-cell__val">{{ fmtDate(general.eval_dt) }}</div>
              </div>
            </div>

            <div class="dg-kpis ev-avoid-break">
              <div class="dg-kpi">
                <span>Kiến thức cơ bản</span>
                <strong>{{ general.subject_total }}<small> / {{ currMaxTotal }}</small></strong>
                <em>{{ totalCurrCorrect }}/{{ curriculum.length }} câu đúng</em>
              </div>
              <div class="dg-kpi">
                <span>Tư duy toán học</span>
                <strong>{{ general.thinking_total }}<small> / {{ thinkingMaxTotal }}</small></strong>
                <em>Xếp loại: {{ thinkingLevel ? thinkingLevel.label : '—' }}</em>
              </div>
              <div class="dg-kpi is-main">
                <span>Tổng điểm</span>
                <strong>{{ general.total_score }}<small> / {{ currMaxTotal + thinkingMaxTotal }}</small></strong>
                <em>Level gán: {{ general.assigned_level || '—' }}</em>
              </div>
            </div>

            <h2 class="dg-sec">Kết quả theo từng câu hỏi</h2>
            <table class="dg-table dg-qtable ev-avoid-break">
              <thead>
                <tr>
                  <th class="dg-rowhead">Câu</th>
                  <th v-for="q in curriculum" :key="q.question_no">{{ q.question_no }}</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="dg-rowhead">Đơn vị</td>
                  <td v-for="q in curriculum" :key="'u' + q.question_no" class="dg-strong">{{ q.unit || '—' }}</td>
                </tr>
                <tr>
                  <td class="dg-rowhead">Đáp án</td>
                  <td v-for="q in curriculum" :key="'a' + q.question_no">{{ q.assigned_score || '—' }}</td>
                </tr>
                <tr>
                  <td class="dg-rowhead">Kết quả</td>
                  <td v-for="q in curriculum" :key="'r' + q.question_no">
                    <span v-if="q.is_correct === 'O'" class="dg-o">✓</span>
                    <span v-else-if="q.is_correct === 'X'" class="dg-x">✗</span>
                    <span v-else class="dg-muted">—</span>
                  </td>
                </tr>
              </tbody>
            </table>

            <div class="dg-row ev-avoid-break">
              <div class="dg-col">
                <h2 class="dg-sec">Đánh giá theo đơn vị kiến thức</h2>
                <table class="dg-table">
                  <thead><tr><th class="is-l">Đơn vị</th><th style="width: 24%;">Số câu đúng</th><th style="width: 18%;">Tỷ lệ</th></tr></thead>
                  <tbody>
                    <tr v-for="(unit, uKey) in unitStats" :key="uKey">
                      <td class="is-l">{{ uKey }}</td>
                      <td><strong>{{ unit.correct }}</strong><span class="dg-muted">/{{ unit.total }}</span></td>
                      <td><span class="dg-pct" :class="pctClass(unit.pct)">{{ unit.pct }}%</span></td>
                    </tr>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td class="is-l">Tổng</td>
                      <td><strong>{{ totalCurrCorrect }}</strong><span class="dg-muted">/{{ curriculum.length }}</span></td>
                      <td><span class="dg-pct" :class="pctClass(totalCurrPct)">{{ totalCurrPct }}%</span></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
              <div class="dg-col dg-chartcell">
                <canvas ref="currRadarCanvas" data-w="360" data-h="240"></canvas>
              </div>
            </div>

            <div class="dg-row ev-avoid-break">
              <div class="dg-col">
                <h2 class="dg-sec">Theo cấp độ câu hỏi</h2>
                <table class="dg-table">
                  <thead><tr><th class="is-l">Cấp độ</th><th>Số câu đúng</th><th>Tỷ lệ</th></tr></thead>
                  <tbody>
                    <tr v-for="stat in currDiffStats" :key="stat.label">
                      <td class="is-l">{{ stat.label }}</td>
                      <td><strong>{{ stat.correct }}</strong><span class="dg-muted">/{{ stat.total }}</span></td>
                      <td><span class="dg-pct" :class="pctClass(stat.pct)">{{ stat.pct }}%</span></td>
                    </tr>
                  </tbody>
                </table>
                <canvas ref="currDiffBarCanvas" data-w="360" data-h="170" class="dg-canvas-mt"></canvas>
              </div>
              <div class="dg-col">
                <h2 class="dg-sec">Toán có lời văn / không lời văn</h2>
                <table class="dg-table">
                  <thead><tr><th class="is-l">Dạng bài</th><th>Số câu đúng</th><th>Tỷ lệ</th></tr></thead>
                  <tbody>
                    <tr v-for="stat in currTypeStats" :key="stat.label">
                      <td class="is-l">{{ stat.label }}</td>
                      <td><strong>{{ stat.correct }}</strong><span class="dg-muted">/{{ stat.total }}</span></td>
                      <td><span class="dg-pct" :class="pctClass(stat.pct)">{{ stat.pct }}%</span></td>
                    </tr>
                  </tbody>
                </table>
                <canvas ref="currTypeBarCanvas" data-w="360" data-h="170" class="dg-canvas-mt"></canvas>
              </div>
            </div>

            <div v-if="generalComment" class="ev-avoid-break">
              <h2 class="dg-sec">Nhận xét kiến thức cơ bản</h2>
              <div class="dg-comment" v-html="generalComment"></div>
            </div>
          </div>
          <footer class="ev-sheet__foot"><span>STEMKEY · IG.BH Diagnostic</span><span>{{ general.stu_nm }} · Trang 1/2</span></footer>
        </section>

        <!-- ================= TRANG 2: TƯ DUY TOÁN HỌC ================= -->
        <section class="ev-sheet">
          <header class="dg-head is-slim">
            <img src="@assets/images/logo/stemkey-logo.png" alt="STEMKEY" class="dg-head__logo" />
            <div class="dg-head__titles">
              <p class="dg-head__super">PHẦN 2 · {{ general.stu_nm }}</p>
              <h1 class="dg-head__title">Đánh giá năng lực tư duy toán học</h1>
            </div>
            <div class="dg-head__level"><span>Điểm tư duy</span><strong>{{ general.thinking_total }}/{{ thinkingMaxTotal }}</strong></div>
          </header>

          <div class="ev-sheet__body">
            <h2 class="dg-sec">Kết quả theo từng câu hỏi</h2>
            <table class="dg-table dg-qtable ev-avoid-break">
              <thead>
                <tr>
                  <th rowspan="2" class="dg-rowhead">Câu</th>
                  <th colspan="7">Năng lực tư duy cơ bản</th>
                  <th colspan="2">Tư duy nâng cao</th>
                  <th>Sáng tạo</th>
                  <th rowspan="2">Tổng</th>
                </tr>
                <tr>
                  <th v-for="q in thinking" :key="'th' + q.question_no">{{ q.question_no }}</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="dg-rowhead">Điểm chuẩn</td>
                  <td v-for="q in thinking" :key="'tm' + q.question_no" class="dg-muted">{{ q.max_score }}</td>
                  <td class="dg-strong">{{ thinkingMaxTotal }}</td>
                </tr>
                <tr>
                  <td class="dg-rowhead">Điểm đạt</td>
                  <td v-for="q in thinking" :key="'ts' + q.question_no" class="dg-strong">{{ q.assigned_score }}</td>
                  <td class="dg-red">{{ general.thinking_total }}</td>
                </tr>
              </tbody>
            </table>

            <h2 class="dg-sec">Đánh giá theo lĩnh vực tư duy</h2>
            <div class="ev-avoid-break">
              <div>
                <table class="dg-table">
                  <thead>
                    <tr><th class="is-l" style="width: 30%;">Lĩnh vực</th><th class="is-l">Nội dung đánh giá</th><th>Câu</th><th>Đạt / Chuẩn</th><th>Tỷ lệ</th></tr>
                  </thead>
                  <tbody>
                    <tr v-for="(stat, idx) in thkAreaStats" :key="idx">
                      <td class="is-l dg-strong">{{ stat.label }}</td>
                      <td class="is-l dg-desc">{{ stat.desc }}</td>
                      <td>{{ stat.q_nums.join(', ') }}</td>
                      <td><strong class="dg-red">{{ stat.score }}</strong><span class="dg-muted">/{{ stat.max }}</span></td>
                      <td><span class="dg-pct" :class="pctClass(stat.pct)">{{ stat.pct }}%</span></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="dg-row ev-avoid-break">
              <div class="dg-col dg-chartcell">
                <canvas ref="thkRadarCanvas" data-w="340" data-h="230"></canvas>
              </div>
              <div class="dg-col">
                <div class="dg-chartcell">
                  <canvas ref="thkHorizontalBarCanvas" data-w="340" data-h="96"></canvas>
                  <div class="dg-caption">Tỉ lệ năng lực giải quyết vấn đề</div>
                </div>
                <h2 class="dg-sec">Theo cấp độ câu hỏi</h2>
                <table class="dg-table">
                  <thead><tr><th class="is-l">Cấp độ</th><th>Số câu</th><th>Câu</th><th>Đạt / Chuẩn</th><th>Tỷ lệ</th></tr></thead>
                  <tbody>
                    <tr v-for="stat in thkDiffStats" :key="stat.label">
                      <td class="is-l">{{ stat.label }}</td>
                      <td>{{ stat.q_nums.length }}</td>
                      <td>{{ stat.q_nums.join(', ') }}</td>
                      <td><strong class="dg-red">{{ stat.score }}</strong><span class="dg-muted">/{{ stat.max }}</span></td>
                      <td><span class="dg-pct" :class="pctClass(stat.pct)">{{ stat.pct }}%</span></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="ev-avoid-break">
              <h2 class="dg-sec">Xếp loại năng lực tư duy toán học</h2>
              <table class="dg-table">
                <thead>
                  <tr><th style="width: 14%;">Điểm</th><th style="width: 14%;">Xếp loại</th><th class="is-l">Ghi chú</th></tr>
                </thead>
                <tbody>
                  <tr v-for="(level, idx) in evaluationLevels" :key="idx" :class="{ 'dg-selected': isInRange(general.thinking_total, level.min, level.max) }">
                    <td class="dg-strong">{{ level.range }}</td>
                    <td class="dg-strong">
                      <span v-if="isInRange(general.thinking_total, level.min, level.max)" class="dg-mark">▶</span>{{ level.label }}
                    </td>
                    <td class="is-l dg-desc">{{ level.note }}</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="ev-signatures ev-avoid-break">
              <div>
                <p class="ev-sig__title">Phụ huynh</p>
                <p class="ev-sig__note">(Ký, ghi rõ họ tên)</p>
                <div class="ev-sig__space"></div>
              </div>
              <div>
                <p class="ev-sig__title">Người chấm</p>
                <p class="ev-sig__note">(Ký, ghi rõ họ tên)</p>
                <div class="ev-sig__space"></div>
                <p class="ev-sig__name">{{ general.reg_name || '' }}</p>
              </div>
              <div>
                <p class="ev-sig__title">Trung tâm STEMKEY</p>
                <p class="ev-sig__note">Ngày {{ fmtDate(general.eval_dt) }}</p>
                <div class="ev-sig__space"></div>
              </div>
            </div>
          </div>
          <footer class="ev-sheet__foot"><span>STEMKEY · IG.BH Diagnostic</span><span>{{ general.stu_nm }} · Trang 2/2</span></footer>
        </section>
      </div>
    </div>
  </div>
</template>

<script>
import axios from '@/http/axios.js'
import '../../_shared/eval-ui.css'
import { fmtDate, hiDpiCanvas } from '../../_shared/helpers'

const EVALUATION_LEVELS = [
  { min: 48, max: 60, range: '48~60', label: 'Ưu tú', note: 'Khả năng tư duy và giải quyết vấn đề trong phạm vi đa dạng rất ưu tú và vượt trội so với các bạn cùng lứa tuổi. Nên thử thách nhiều hơn với những bài toán khó cũng như nội dung mới trước chương trình học trên trường.' },
  { min: 32, max: 47, range: '32~47', label: 'Xuất sắc', note: 'Là học sinh có khả năng giải quyết vấn đề xuất sắc. Nên thử thách những bài toán có độ khó, hơn là việc chỉ tập trung giải những đề thi thông thường.' },
  { min: 18, max: 31, range: '18~31', label: 'Khá', note: 'Năng lực giải quyết vấn đề ở mức độ khá. Tuy nhiên, cần giải các bài toán một cách cẩn thận hơn. Cần bổ sung kiến thức ở những nội dung còn thiếu sót.' },
  { min: 9, max: 17, range: '9~17', label: 'Trung bình', note: 'Là học sinh có khả năng giải quyết vấn đề và khả năng ứng dụng chưa được tốt và chưa linh hoạt. Cần hình thành thói quen giải toán một cách tỉ mỉ dù là một bài toán đơn giản, đồng thời đa dạng hóa các dạng bài.' },
  { min: 0, max: 8, range: '0~8', label: 'Yếu', note: 'Cần đầu tư nhiều hơn nữa vào việc củng cố những kiến thức cơ bản trong toán học, bắt đầu từ việc hình thành những thói quen giải toán bằng việc hiểu được phương pháp của từng dạng đề xuất hiện trong sách giáo khoa.' }
]

export default {
  name: 'IgbhDiagnosticReport',
  data() {
    return {
      resultId: null,
      general: null,
      curriculum: [],
      thinking: [],
      unitLabels: {},
      testComments: [],
      loading: false,
      evaluationLevels: EVALUATION_LEVELS
    }
  },
  computed: {
    unitStats() {
      const stats = {}
      this.curriculum.forEach(q => {
        let labelName = q.unit
        if (q.unit && this.unitLabels[q.unit]) {
           labelName = this.unitLabels[q.unit]
        }
        const key = q.unit ? `${q.unit}.${labelName}` : 'Khác'
        if (!stats[key]) stats[key] = { correct: 0, total: 0, sectorCd: q.unit }
        stats[key].total++
        if (q.is_correct === 'O') stats[key].correct++
      })
      Object.keys(stats).forEach(k => {
        stats[k].pct = Math.round((stats[k].correct / stats[k].total) * 100)
      })
      return stats
    },
    totalCurrCorrect() {
      return this.curriculum.filter(q => q.is_correct === 'O').length
    },
    totalCurrPct() {
      if (this.curriculum.length === 0) return 0
      return Math.round((this.totalCurrCorrect / this.curriculum.length) * 100)
    },
    currDiffStats() {
      const stats = {
        'DC001': { label: 'Khó', correct: 0, total: 0 },
        'DC002': { label: 'Trung bình', correct: 0, total: 0 },
        'DC003': { label: 'Dễ', correct: 0, total: 0 }
      }
      this.curriculum.forEach(q => {
        if (q.difficulty && stats[q.difficulty]) {
          stats[q.difficulty].total++
          if (q.is_correct === 'O') stats[q.difficulty].correct++
        }
      })
      Object.keys(stats).forEach(k => {
        stats[k].pct = stats[k].total > 0 ? Math.round((stats[k].correct / stats[k].total) * 100) : 0
      })
      return [stats['DC001'], stats['DC002'], stats['DC003']].filter(s => s.total > 0)
    },
    currTypeStats() {
      const stats = {
        'ST002': { label: 'Bài toán có lời văn', correct: 0, total: 0 },
        'ST001': { label: 'Bài toán cơ bản', correct: 0, total: 0 }
      }
      this.curriculum.forEach(q => {
        if (q.type_cd && stats[q.type_cd]) {
          stats[q.type_cd].total++
          if (q.is_correct === 'O') stats[q.type_cd].correct++
        }
      })
      Object.keys(stats).forEach(k => {
        stats[k].pct = stats[k].total > 0 ? Math.round((stats[k].correct / stats[k].total) * 100) : 0
      })
      return [stats['ST002'], stats['ST001']].filter(s => s.total > 0)
    },
    generalComment() {
      if (!this.testComments || this.testComments.length === 0) return ''
      
      let goodUnitsCount = 0
      let goodUnitNames = []
      let weakUnitNames = []
      let weakUnitComments = []
      
      Object.values(this.unitStats).forEach(unit => {
        let uName = this.unitLabels[unit.sectorCd] || unit.sectorCd
        if (unit.pct >= 75) {
          goodUnitsCount++
          goodUnitNames.push(`<span class="comment-hl-good">${uName}</span>`)
        } else if (unit.sectorCd) {
          weakUnitNames.push(`<span class="comment-hl-weak">${uName}</span>`)
          
          const unitComment = this.testComments.find(c => c.comment_type === 'unit' && c.condition_value === unit.sectorCd)
          if (unitComment && unitComment.weak_comment) {
            let text = unitComment.weak_comment
            text = text.replace(/#weakUnitCdNms#/g, `<span class="comment-hl-weak">${uName}</span>`)
            text = text.replace(/#weakUnitCdNm#/g, `<span class="comment-hl-weak">${uName}</span>`)
            weakUnitComments.push(text)
          }
        }
      })
      
      const totalComment = this.testComments.find(c => c.comment_type === 'total' && parseInt(c.condition_value) === goodUnitsCount)
      
      let finalComment = ''
      if (totalComment && totalComment.good_comment) {
         let gc = totalComment.good_comment
         if (goodUnitNames.length > 0 || (!gc.includes('#goodUnitCdNms#') && !gc.includes('#weakUnitCdNms#'))) {
             gc = gc.replace(/#goodUnitCdNms#/g, goodUnitNames.join(', '))
             gc = gc.replace(/#weakUnitCdNms#/g, weakUnitNames.join(', '))
             gc = gc.replace(/<span class="co_red">#weakUnitCdN[^<]*<\/span>/g, weakUnitNames.join(', '))
             gc = gc.replace(/<span class="co_red">#weakUnitCdN.*/g, weakUnitNames.join(', '))
             gc = gc.replace(/#weakUnitCdN(?!m).*/g, weakUnitNames.join(', '))
             finalComment += gc + ' '
         }
      }
      
      if (totalComment && totalComment.weak_comment && weakUnitNames.length > 0) {
         let wc = totalComment.weak_comment
         wc = wc.replace(/#goodUnitCdNms#/g, goodUnitNames.join(', '))
         wc = wc.replace(/#weakUnitCdNms#/g, weakUnitNames.join(', '))
         wc = wc.replace(/<span class="co_red">#weakUnitCdN[^<]*<\/span>/g, weakUnitNames.join(', '))
         wc = wc.replace(/<span class="co_red">#weakUnitCdN.*/g, weakUnitNames.join(', '))
         wc = wc.replace(/#weakUnitCdN(?!m).*/g, weakUnitNames.join(', '))
         finalComment += (finalComment ? '<br/>' : '') + wc
      }

      if (weakUnitComments.length > 0) {
         finalComment += (finalComment ? '<br/>' : '') + weakUnitComments.join('<br/>')
      }
      
      finalComment = finalComment.replace(/<span class="co_red">$/, '')
      return finalComment.trim()
    },
    currMaxTotal() {
      return this.curriculum.reduce((acc, q) => acc + (Number(q.point) || 0), 0)
    },
    thinkingLevel() {
      if (!this.general) return null
      return this.evaluationLevels.find(l => this.isInRange(this.general.thinking_total, l.min, l.max)) || null
    },
    thinkingMaxTotal() {
       return this.thinking.reduce((acc, q) => acc + (q.max_score || 0), 0)
    },
    thkAreaStats() {
      const areaLabels = {
        A: { label: 'A. Khả năng hiểu toán', desc: 'Áp dụng kiến thức đã học, lập luận để giải quyết những bài toán mới.' },
        B: { label: 'B. Khả năng quan sát trực quan', desc: 'Tìm ra được mối quan hệ hoặc bản chất của sự vật thông qua trực quan để giải quyết vấn đề.' },
        C: { label: 'C. Khả năng tổng hợp thông tin', desc: 'Thu thập những thông tin cần thiết, phân loại và giải quyết vấn đề.' },
        D: { label: 'D. Khả năng dự đoán toán học', desc: 'Vận dụng và công thức hóa khái niệm hoặc ký hiệu toán học phù hợp để giải quyết vấn đề.' },
        E: { label: 'E. Khả năng nhận thức không gian', desc: 'Phát hiện và giải quyết những bài toán về hình học phẳng và không gian.' },
        F: { label: 'F. Khả năng suy luận toán học', desc: 'Sử dụng các phương pháp như quy nạp hay diễn dịch.' }
      }
      const stats = {}
      Object.keys(areaLabels).forEach(k => {
        stats[k] = { label: areaLabels[k].label, desc: areaLabels[k].desc, q_nums: [], max: 0, score: 0 }
      })
      
      this.thinking.forEach(q => {
        if (q.areas && Array.isArray(q.areas)) {
          q.areas.forEach(a => {
            if (stats[a]) {
              stats[a].q_nums.push(q.question_no)
              stats[a].max += (q.max_score || 0)
              stats[a].score += (q.assigned_score || 0)
            }
          })
        }
      })
      
      Object.keys(stats).forEach(k => {
        stats[k].pct = stats[k].max > 0 ? Math.round((stats[k].score / stats[k].max) * 100) : 0
      })
      
      return Object.values(stats).filter(s => s.q_nums.length > 0)
    },
    thkDiffStats() {
      const stats = {
        'DC001': { label: 'Khó', q_nums: [], max: 0, score: 0 },
        'DC002': { label: 'Trung bình', q_nums: [], max: 0, score: 0 },
        'DC003': { label: 'Dễ', q_nums: [], max: 0, score: 0 }
      }
      this.thinking.forEach(q => {
        if (q.difficulty && stats[q.difficulty]) {
          stats[q.difficulty].q_nums.push(q.question_no)
          stats[q.difficulty].max += (q.max_score || 0)
          stats[q.difficulty].score += (q.assigned_score || 0)
        }
      })
      Object.keys(stats).forEach(k => {
        stats[k].pct = stats[k].max > 0 ? Math.round((stats[k].score / stats[k].max) * 100) : 0
      })
      return [stats['DC001'], stats['DC002'], stats['DC003']].filter(s => s.q_nums.length > 0)
    }
  },
  created() {
    this.resultId = this.$route.params.id
    this.fetchDetail()
  },
  methods: {
    fmtDate,
    pctClass(p) {
      return p >= 75 ? 'is-good' : (p >= 50 ? 'is-mid' : 'is-low')
    },
    isInRange(score, min, max) {
      return score >= min && score <= max
    },
    fetchDetail() {
      this.loading = true
      axios.g(`/api/lms/evaluations/igbh/results/${this.resultId}`).then(res => {
        this.loading = false
        if (res && res.data && res.data.status === 'success') {
          const d = res.data.data
          const general = d.general
          const details = d.details || (d.curriculum && d.thinking ? [...d.curriculum, ...d.thinking] : (d.curriculum || []))
          const test_config = d.test_config || d.config
          const test_questions = d.test_questions || d.questions || []
          const test_comments = d.test_comments || d.comments || []

          this.general = general
          this.testComments = test_comments || []
          
          if (test_config) {
            if (test_config.sectors) {
              try {
                this.unitLabels = typeof test_config.sectors === 'string' ? JSON.parse(test_config.sectors) : test_config.sectors
              } catch (e) {}
            } else if (typeof test_config === 'object') {
              this.unitLabels = test_config
            }
          }

          const currConfigs = test_questions ? test_questions.filter(q => q.question_type === 'curriculum') : []
          const currLength = currConfigs.length > 0 ? currConfigs.length : 20

          this.curriculum = Array.from({ length: currLength }, (_, i) => {
            const no = i + 1
            const q = (details && Array.isArray(details)) ? details.find(d => d.question_type === 'curriculum' && parseInt(d.question_no) === no) : null
            const conf = currConfigs.find(c => c.sort_no === no)
            return {
              question_no: no,
              assigned_score: q ? q.assigned_score : '',
              unit: conf ? conf.sector : (q ? q.unit : null),
              is_correct: q ? q.is_correct : null,
              difficulty: conf ? conf.difficulty : null,
              type_cd: conf ? conf.type_cd : null,
              point: conf ? conf.standard_point : 2
            }
          })

          const thkConfigs = test_questions ? test_questions.filter(q => q.question_type === 'thinking') : []
          const thkLength = thkConfigs.length > 0 ? thkConfigs.length : 10

          this.thinking = Array.from({ length: thkLength }, (_, i) => {
            const no = i + 1
            const q = (details && Array.isArray(details)) ? details.find(d => d.question_type === 'thinking' && parseInt(d.question_no) === no) : null
            const conf = thkConfigs.find(c => c.sort_no === no)
            let areas = []
            if (conf && conf.areas) {
              try { areas = typeof conf.areas === 'string' ? JSON.parse(conf.areas) : conf.areas } catch(e) {}
            }
            return {
              question_no: no,
              assigned_score: q ? (parseInt(q.assigned_score) || 0) : 0,
              max_score: conf ? (conf.standard_point || 5) : (q ? q.max_score : 5),
              difficulty: conf ? conf.difficulty : null,
              areas: areas
            }
          })

          // Draw charts
          this.$nextTick(() => {
            if (this.$refs.currRadarCanvas) {
              this.drawRadarChart(this.$refs.currRadarCanvas, Object.values(this.unitStats).map(s => s.sectorCd || 'Khác'), Object.values(this.unitStats).map(s => s.pct))
            }
            if (this.$refs.thkRadarCanvas) {
              this.drawRadarChart(this.$refs.thkRadarCanvas, this.thkAreaStats.map(s => s.label.split('.')[0]), this.thkAreaStats.map(s => s.pct))
            }
            if (this.$refs.thkHorizontalBarCanvas && this.general) {
              this.drawHorizontalBarChart(this.$refs.thkHorizontalBarCanvas, this.general.thinking_total, this.thinkingMaxTotal)
            }
            if (this.$refs.currDiffBarCanvas) {
              this.drawBarChart(this.$refs.currDiffBarCanvas, this.currDiffStats)
            }
            if (this.$refs.currTypeBarCanvas) {
              this.drawBarChart(this.$refs.currTypeBarCanvas, this.currTypeStats)
            }
            if (this.$refs.thkDiffBarCanvas) {
              this.drawBarChart(this.$refs.thkDiffBarCanvas, this.thkDiffStats)
            }
          })
        }
      }).catch(err => {
        this.loading = false
        console.error('Fetch detail error:', err)
      })
    },
    drawRadarChart(canvas, labels, values) {
      if (!canvas) return
      const W = Number(canvas.dataset.w || 340)
      const H = Number(canvas.dataset.h || 150)
      const ctx = hiDpiCanvas(canvas, W, H)
      if (!ctx) return

      const cx = W / 2
      const cy = H / 2 - 12
      const radius = Math.min(cx, cy) - 24
      const n = labels.length
      if (n === 0) return

      ctx.clearRect(0, 0, W, H)

      // Grid rings
      for (let r = 1; r <= 5; r++) {
        ctx.beginPath()
        ctx.strokeStyle = r === 5 ? '#94a3b8' : '#e2e8f0'
        ctx.lineWidth = 0.8
        for (let i = 0; i <= n; i++) {
          const angle = (Math.PI * 2 * i) / n - Math.PI / 2
          const x = cx + (radius * r / 5) * Math.cos(angle)
          const y = cy + (radius * r / 5) * Math.sin(angle)
          i === 0 ? ctx.moveTo(x, y) : ctx.lineTo(x, y)
        }
        ctx.closePath()
        ctx.stroke()
      }

      // Spoke axes & labels
      for (let i = 0; i < n; i++) {
        const angle = (Math.PI * 2 * i) / n - Math.PI / 2
        ctx.beginPath()
        ctx.strokeStyle = '#94a3b8'
        ctx.lineWidth = 0.8
        ctx.moveTo(cx, cy)
        ctx.lineTo(cx + radius * Math.cos(angle), cy + radius * Math.sin(angle))
        ctx.stroke()

        const labelX = cx + (radius + 18) * Math.cos(angle)
        const labelY = cy + (radius + 18) * Math.sin(angle)
        ctx.fillStyle = '#1e293b'
        ctx.font = 'bold 13px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
        ctx.textAlign = 'center'
        ctx.textBaseline = 'middle'
        ctx.fillText(labels[i], labelX, labelY)
      }

      // Datasets
      const datasets = [
        { label: 'Các thí sinh', color: '#1e3a8a', bg: 'rgba(30,58,138,0.12)', data: values.map(() => 88) },
        { label: 'Học sinh STEMKEY', color: '#65a30d', bg: 'rgba(101,163,13,0.14)', data: values.map(() => 91) },
        { label: 'Thí sinh', color: '#ef4444', bg: 'rgba(239,68,68,0.45)', data: values }
      ]

      datasets.forEach(ds => {
        ctx.beginPath()
        ctx.fillStyle = ds.bg
        ctx.strokeStyle = ds.color
        ctx.lineWidth = 2
        for (let i = 0; i < n; i++) {
          const angle = (Math.PI * 2 * i) / n - Math.PI / 2
          const val = (ds.data[i] || 0) / 100
          const x = cx + radius * val * Math.cos(angle)
          const y = cy + radius * val * Math.sin(angle)
          i === 0 ? ctx.moveTo(x, y) : ctx.lineTo(x, y)
        }
        ctx.closePath()
        ctx.fill()
        ctx.stroke()

        // Point dots
        for (let i = 0; i < n; i++) {
          const angle = (Math.PI * 2 * i) / n - Math.PI / 2
          const val = (ds.data[i] || 0) / 100
          const x = cx + radius * val * Math.cos(angle)
          const y = cy + radius * val * Math.sin(angle)
          ctx.beginPath()
          ctx.arc(x, y, 3.5, 0, 2 * Math.PI)
          ctx.fillStyle = ds.color
          ctx.fill()
        }
      })
      
      // Bottom Legend
      const legendY = H - 12
      const legendW = 112
      const startX = cx - (datasets.length * legendW) / 2
      
      ctx.textAlign = 'left'
      ctx.font = '13px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
      datasets.forEach((ds, idx) => {
         ctx.fillStyle = ds.color
         ctx.fillRect(startX + idx*legendW, legendY - 6, 11, 11)
         ctx.fillStyle = '#334155'
         ctx.fillText(ds.label, startX + idx*legendW + 15, legendY + 3)
      })
    },
    drawBarChart(canvas, stats) {
       if (!canvas) return
       const W = Number(canvas.dataset.w || 340)
      const H = Number(canvas.dataset.h || 150)
      const ctx = hiDpiCanvas(canvas, W, H)
       if (!ctx) return
       
       const w = W
       const h = H
       const padX = 35
       const padY = 16
       const chartW = w - padX * 2
       const chartH = h - padY * 2 - 40
       
       ctx.clearRect(0, 0, w, h)
       
       // Grid lines
       ctx.strokeStyle = '#f1f5f9'
       ctx.lineWidth = 1
       ctx.fillStyle = '#64748b'
       ctx.font = '13px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
       ctx.textAlign = 'right'
       ctx.textBaseline = 'middle'
       
       for(let i=0; i<=5; i++) {
          const y = padY + (chartH / 5) * i
          ctx.beginPath()
          ctx.moveTo(padX, y)
          ctx.lineTo(padX + chartW, y)
          ctx.stroke()
          ctx.fillText((100 - i*20).toString(), padX - 6, y)
       }
       
       // Axes
       ctx.beginPath()
       ctx.strokeStyle = '#94a3b8'
       ctx.lineWidth = 1
       ctx.moveTo(padX, padY)
       ctx.lineTo(padX, padY + chartH)
       ctx.lineTo(padX + chartW, padY + chartH)
       ctx.stroke()
       
       if (stats.length === 0) return
       
       const numGroups = stats.length
       const groupWidth = chartW / numGroups
       const barWidth = 16
       const barSpacing = 3
       const totalBarsWidth = 3 * barWidth + 2 * barSpacing
       
       ctx.textAlign = 'center'
       ctx.textBaseline = 'top'
       
       stats.forEach((stat, i) => {
          const groupCenterX = padX + groupWidth * i + groupWidth / 2
          const startX = groupCenterX - totalBarsWidth / 2
          
          const val1 = stat.pct
          const val2 = 88
          const val3 = 90
          
          const bars = [
             { val: val1, color: '#ef4444' }, // Thí sinh
             { val: val2, color: '#1e3a8a' }, // Các thí sinh
             { val: val3, color: '#65a30d' }  // Học sinh STEMKEY
          ]
          
          bars.forEach((b, bIdx) => {
             const barH = (b.val / 100) * chartH
             const barY = padY + chartH - barH
             const bx = startX + bIdx * (barWidth + barSpacing)
             
             ctx.fillStyle = b.color
             ctx.fillRect(bx, barY, barWidth, barH)
          })
          
          ctx.fillStyle = '#1e293b'
          ctx.font = '13px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
          ctx.fillText(stat.label, groupCenterX, padY + chartH + 6)
       })
       
       // Legend
       const datasets = [
         { label: 'Thí sinh', color: '#ef4444' },
         { label: 'Các thí sinh', color: '#1e3a8a' },
         { label: 'Học sinh STEMKEY', color: '#65a30d' }
       ]
       const legendY = H - 10
       const legendW = 112
       const legendStartX = w/2 - (datasets.length * legendW) / 2
       
       ctx.textAlign = 'left'
       ctx.textBaseline = 'middle'
       ctx.font = '13px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
       datasets.forEach((ds, idx) => {
          ctx.fillStyle = ds.color
          ctx.fillRect(legendStartX + idx*legendW, legendY - 5, 10, 10)
          ctx.fillStyle = '#334155'
          ctx.fillText(ds.label, legendStartX + idx*legendW + 14, legendY + 1)
       })
    },
    drawHorizontalBarChart(canvas, score, maxScore) {
       if (!canvas) return
       const W = Number(canvas.dataset.w || 340)
      const H = Number(canvas.dataset.h || 150)
      const ctx = hiDpiCanvas(canvas, W, H)
       if (!ctx) return
       
       const w = W
       const h = H
       ctx.clearRect(0, 0, w, h)
       
       const studentPct = maxScore > 0 ? Math.round((score / maxScore) * 100) : 0
       const allPct = 88
       const cmsPct = 90

       const data = [
         { label: 'Thí sinh', color: '#ef4444', pct: studentPct },
         { label: 'Các thí sinh', color: '#1e3a8a', pct: allPct },
         { label: 'Học sinh STEMKEY', color: '#65a30d', pct: cmsPct }
       ]

       const barH = 18
       const gap = 10
       const startY = 8
       const chartX = 12
       const chartW = w - 150
       
       data.forEach((item, idx) => {
          const y = startY + idx * (barH + gap)
          
          ctx.fillStyle = '#f1f5f9'
          ctx.fillRect(chartX, y, chartW, barH)
          
          ctx.fillStyle = item.color
          const barWidth = chartW * (item.pct / 100)
          ctx.fillRect(chartX, y, barWidth, barH)
          
          ctx.fillStyle = '#ffffff'
          ctx.font = 'bold 13px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
          ctx.textAlign = 'right'
          ctx.textBaseline = 'middle'
          if (barWidth > 32) {
            ctx.fillText(item.pct + '%', chartX + barWidth - 6, y + barH/2)
          } else {
            ctx.fillStyle = '#475569'
            ctx.textAlign = 'left'
            ctx.fillText(item.pct + '%', chartX + barWidth + 6, y + barH/2)
          }
          
          const legendX = chartX + chartW + 12
          ctx.fillStyle = item.color
          ctx.fillRect(legendX, y + 4, 9, 9)
          ctx.fillStyle = '#334155'
          ctx.font = '13px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
          ctx.textAlign = 'left'
          ctx.fillText(item.label, legendX + 14, y + 9)
       })
    },
    printReport() {
      window.print()
    }
  }
}
</script>

<style scoped>
.dg-head { display: flex; align-items: center; gap: 5mm; background: #1e3a8a; color: #ffffff; padding: 6mm 11mm; }
.dg-head.is-slim { padding: 4mm 11mm; }
.dg-head__logo { height: 13mm; background: rgba(255, 255, 255, 0.95); padding: 1.5mm 2.5mm; border-radius: 2mm; }
.dg-head.is-slim .dg-head__logo { height: 9mm; }
.dg-head__titles { flex: 1; border-left: 1px solid rgba(255, 255, 255, 0.3); padding-left: 5mm; min-width: 0; }
.dg-head__super { font-size: 13px; font-weight: 600; letter-spacing: 1.2px; color: rgba(255, 255, 255, 0.75); margin: 0; }
.dg-head__title { font-size: 20px; font-weight: 800; color: #fde047; margin: 1mm 0 0; text-transform: uppercase; line-height: 1.2; }
.dg-head.is-slim .dg-head__title { font-size: 17px; }
.dg-head__level { text-align: center; background: rgba(255, 255, 255, 0.12); border-radius: 2mm; padding: 1.5mm 4mm; white-space: nowrap; }
.dg-head__level span { display: block; font-size: 13px; color: rgba(255, 255, 255, 0.75); }
.dg-head__level strong { font-size: 19px; color: #ffffff; }

.dg-kpis { display: grid; grid-template-columns: repeat(3, 1fr); gap: 3mm; margin-bottom: 5mm; }
.dg-kpi { border: 1px solid #dbeafe; background: #f8fbff; border-radius: 2mm; padding: 2.5mm 3.5mm; }
.dg-kpi span { display: block; font-size: 13px; color: #475569; font-weight: 600; }
.dg-kpi strong { font-size: 21px; font-weight: 900; color: #1e3a8a; }
.dg-kpi small { font-size: 13px; color: #64748b; font-weight: 600; }
.dg-kpi em { display: block; font-style: normal; font-size: 13px; color: #64748b; }
.dg-kpi.is-main { background: #1e3a8a; border-color: #1e3a8a; }
.dg-kpi.is-main span, .dg-kpi.is-main small, .dg-kpi.is-main em { color: rgba(255, 255, 255, 0.8); }
.dg-kpi.is-main strong { color: #fde047; }

.dg-sec { font-size: 15px; font-weight: 800; color: #1e3a8a; border-left: 4px solid #2563eb; padding-left: 2.5mm; margin: 0 0 2.5mm; line-height: 1.3; }

.dg-table { width: 100%; border-collapse: collapse; font-size: 13px; text-align: center; margin-bottom: 4.5mm; }
.dg-table th { background: #eff6ff; color: #1e3a8a; font-weight: 700; border: 1px solid #d6e4f5; padding: 1.6mm 1.5mm; line-height: 1.25; }
.dg-table td { border: 1px solid #e5e7eb; padding: 1.5mm 1.8mm; color: #334155; }
.dg-table .is-l { text-align: left; }
.dg-table tbody tr:nth-child(even) { background: #fafbfc; }
.dg-table tfoot td { background: #f1f5f9; font-weight: 700; }
.dg-qtable th, .dg-qtable td { padding: 1.4mm 0.5mm; }
.dg-rowhead { background: #f8fafc !important; color: #334155 !important; font-weight: 700; white-space: nowrap; padding: 1.4mm 2mm !important; }
.dg-strong { font-weight: 700; color: #0f172a; }
.dg-muted { color: #94a3b8; }
.dg-red { color: #dc2626; font-weight: 800; }
.dg-desc { color: #475569; line-height: 1.4; }
.dg-o { color: #16a34a; font-weight: 900; font-size: 14px; }
.dg-x { color: #dc2626; font-weight: 900; font-size: 14px; }
.dg-pct { display: inline-block; min-width: 11mm; padding: 0.5mm 1.5mm; border-radius: 99px; font-weight: 800; white-space: nowrap; }
.dg-pct.is-good { background: #dcfce7; color: #15803d; }
.dg-pct.is-mid { background: #fef3c7; color: #b45309; }
.dg-pct.is-low { background: #fee2e2; color: #b91c1c; }
.dg-selected td { background: #fef9c3 !important; }
.dg-mark { color: #dc2626; margin-right: 1.5mm; }

.dg-row { display: flex; gap: 5mm; align-items: flex-start; margin-bottom: 1mm; }
.dg-col { flex: 1; min-width: 0; }
.dg-col.is-wide { flex: 1.45; }
.dg-chartcell { display: flex; flex-direction: column; align-items: center; justify-content: center; border: 1px solid #e2e8f0; border-radius: 2mm; padding: 2mm; margin-bottom: 4.5mm; }
.dg-chartcell canvas, .dg-canvas-mt { display: block; margin: 0 auto; }
.dg-canvas-mt { margin-top: -2mm; margin-bottom: 4mm; }
.dg-caption { font-size: 13px; color: #64748b; margin-top: 1mm; }

.dg-comment {
  border: 1px solid #e2e8f0;
  border-left: 4px solid #2563eb;
  background: #f8fbff;
  border-radius: 2mm;
  padding: 3.5mm 4.5mm;
  font-size: 13.5px;
  line-height: 1.6;
  color: #334155;
}
.dg-comment >>> .comment-hl-good { color: #15803d; font-weight: 700; }
.dg-comment >>> .comment-hl-weak { color: #dc2626; font-weight: 700; }
</style>
