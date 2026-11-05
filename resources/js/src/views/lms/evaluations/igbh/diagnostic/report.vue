<template>
  <div class="igbh-report-wrapper">
    <!-- Action Bar (Hidden on print) -->
    <div class="action-toolbar print-hide">
      <div class="toolbar-info">
        <router-link to="/lms/evaluations/igbh/diagnostic" class="back-link">
          <i class="feather icon-arrow-left"></i> Quay lại danh sách
        </router-link>
        <h2 class="toolbar-title">Báo Cáo Khảo Sát Năng Lực Đầu Vào IG.BH</h2>
        <div class="toolbar-sub" v-if="general">
          <span>{{ general.test_nm }}</span>
          <span class="sep">|</span>
          <span>Học sinh: <strong class="text-name">{{ general.stu_nm }}</strong></span>
          <span class="sep" v-if="general.stu_birth_dt">|</span>
          <span v-if="general.stu_birth_dt">Ngày sinh: {{ general.stu_birth_dt }}</span>
        </div>
      </div>
      <div class="toolbar-btns">
        <button class="btn-print" @click="printReport">
          <i class="feather icon-printer"></i> In Báo Cáo (PDF)
        </button>
        <router-link :to="`/lms/evaluations/igbh/diagnostic/form/${resultId}`" class="btn-edit">
          <i class="feather icon-edit"></i> Sửa Điểm
        </router-link>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="loading-state print-hide">
      <div class="spinner"></div>
      <p>Đang tải dữ liệu báo cáo...</p>
    </div>

    <!-- Main Report Document -->
    <div v-else-if="general" id="igbh-report-doc" class="report-document">
      
      <!-- ================= PAGE 1: KIẾN THỨC CƠ BẢN ================= -->
      <div class="report-sheet" id="report-sheet-1">
        <!-- Sheet Header -->
        <div class="sheet-header">
          <div class="header-brand-block">
            <img src="@assets/images/logo/stemkey-logo.png" alt="STEMKEY" class="brand-logo" />
            <div class="header-titles">
              <h1 class="main-title">KIẾN THỨC CƠ BẢN</h1>
              <p class="sub-title">ĐÁNH GIÁ ĐẦU KỲ</p>
            </div>
          </div>
          <div class="student-pill">
            <span class="pill-item">
              <span class="pill-label">Trình độ:</span>
              <span class="pill-val level-val">{{ general.assigned_level || '—' }}</span>
            </span>
            <span class="pill-item">
              <span class="pill-label">Sinh:</span>
              <span class="pill-val">{{ general.stu_birth_dt || '—' }}</span>
            </span>
            <span class="pill-name">{{ general.stu_nm }}</span>
          </div>
        </div>

        <div class="sheet-body">
          <!-- 1. Kết quả đánh giá theo câu hỏi riêng biệt -->
          <div class="report-block">
            <div class="block-title">
              <span class="title-bar"></span>
              <h3>Kết quả đánh giá theo câu hỏi riêng biệt</h3>
            </div>
            <div class="table-container">
              <table class="grid-table question-table">
                <thead>
                  <tr class="header-row">
                    <th class="col-muc">Mục</th>
                    <th v-for="q in curriculum" :key="q.question_no" class="col-qno">{{ q.question_no }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="row-unit">
                    <td class="cell-label">Đơn vị</td>
                    <td v-for="q in curriculum" :key="'u' + q.question_no" class="cell-val bold-txt">
                      {{ q.unit || '—' }}
                    </td>
                  </tr>
                  <tr class="row-answer">
                    <td class="cell-label">Đáp án</td>
                    <td v-for="q in curriculum" :key="'a' + q.question_no" class="cell-val">
                      {{ q.assigned_score || '—' }}
                    </td>
                  </tr>
                  <tr class="row-result">
                    <td class="cell-label">Kết quả</td>
                    <td v-for="q in curriculum" :key="'r' + q.question_no" class="cell-val">
                      <span v-if="q.is_correct === 'O'" class="res-o">o</span>
                      <span v-else-if="q.is_correct === 'X'" class="res-x">x</span>
                      <span v-else class="res-dash">—</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- 2. Đánh giá theo đơn vị riêng biệt (2 Cột: Bảng + Radar) -->
          <div class="report-block">
            <div class="two-columns-row">
              <div class="col-left">
                <div class="block-title">
                  <span class="title-bar"></span>
                  <h3>Đánh giá theo đơn vị riêng biệt</h3>
                </div>
                <div class="table-container">
                  <table class="grid-table stat-table">
                    <thead>
                      <tr class="header-row">
                        <th class="text-center" style="width: 52%;">Phân loại</th>
                        <th class="text-center" style="width: 28%;">Số câu đúng</th>
                        <th class="text-center" style="width: 20%;">Tỷ lệ</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(unit, uKey) in unitStats" :key="uKey">
                        <td class="text-center font-medium">{{ uKey }}</td>
                        <td class="text-center">
                          <span class="correct-cnt">{{ unit.correct }}</span>
                          <span class="total-cnt">/{{ unit.total }}</span>
                        </td>
                        <td class="text-center pct-cnt">{{ unit.pct }}%</td>
                      </tr>
                      <tr class="row-total">
                        <td class="text-center font-bold">Tổng</td>
                        <td class="text-center">
                          <span class="correct-cnt">{{ totalCurrCorrect }}</span>
                          <span class="total-cnt">/{{ curriculum.length }}</span>
                        </td>
                        <td class="text-center pct-cnt font-bold">{{ totalCurrPct }}%</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- Radar Canvas Right -->
              <div class="col-right">
                <div class="chart-panel radar-panel">
                  <canvas ref="currRadarCanvas" width="340" height="240" class="chart-canvas"></canvas>
                </div>
              </div>
            </div>
          </div>

          <!-- 3. So sánh năng lực giải quyết vấn đề theo cấp độ -->
          <div class="report-block">
            <div class="two-columns-row">
              <div class="col-left">
                <div class="block-title">
                  <span class="title-bar"></span>
                  <h3>So sánh năng lực giải quyết vấn đề theo cấp độ</h3>
                </div>
                <div class="table-container">
                  <table class="grid-table stat-table">
                    <thead>
                      <tr class="header-row">
                        <th class="text-center" style="width: 52%;">Phân loại</th>
                        <th class="text-center" style="width: 28%;">Số câu đúng</th>
                        <th class="text-center" style="width: 20%;">Tỷ lệ</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="stat in currDiffStats" :key="stat.label">
                        <td class="text-center font-medium">{{ stat.label }}</td>
                        <td class="text-center">
                          <span class="correct-cnt">{{ stat.correct }}</span>
                          <span class="total-cnt">/{{ stat.total }}</span>
                        </td>
                        <td class="text-center pct-cnt">{{ stat.pct }}%</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <div class="col-right">
                <div class="chart-panel bar-panel">
                  <canvas ref="currDiffBarCanvas" width="340" height="150" class="chart-canvas"></canvas>
                </div>
              </div>
            </div>
          </div>

          <!-- 4. So sánh khả năng giải toán có lời văn và không có lời văn -->
          <div class="report-block">
            <div class="two-columns-row">
              <div class="col-left">
                <div class="block-title">
                  <span class="title-bar"></span>
                  <h3>So sánh khả năng giải toán có lời văn và không có lời văn</h3>
                </div>
                <div class="table-container">
                  <table class="grid-table stat-table">
                    <thead>
                      <tr class="header-row">
                        <th class="text-center" style="width: 52%;">Phân loại</th>
                        <th class="text-center" style="width: 28%;">Số câu đúng</th>
                        <th class="text-center" style="width: 20%;">Tỷ lệ</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="stat in currTypeStats" :key="stat.label">
                        <td class="text-center font-medium">{{ stat.label }}</td>
                        <td class="text-center">
                          <span class="correct-cnt">{{ stat.correct }}</span>
                          <span class="total-cnt">/{{ stat.total }}</span>
                        </td>
                        <td class="text-center pct-cnt">{{ stat.pct }}%</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <div class="col-right">
                <div class="chart-panel bar-panel">
                  <canvas ref="currTypeBarCanvas" width="340" height="150" class="chart-canvas"></canvas>
                </div>
              </div>
            </div>
          </div>
          
          <!-- 5. Kết quả đánh giá kiến thức cơ bản (Comment) -->
          <div class="report-block" v-if="generalComment">
            <div class="block-title">
              <span class="title-bar"></span>
              <h3>Kết quả đánh giá kiến thức cơ bản</h3>
            </div>
            <div class="comment-box">
              <p class="comment-content" v-html="generalComment"></p>
            </div>
          </div>
        </div>
      </div>

      <!-- ================= PAGE 2: TƯ DUY TOÁN HỌC ================= -->
      <div class="report-sheet sheet-page-2" id="report-sheet-2">
        <!-- Sheet Header -->
        <div class="sheet-header">
          <div class="header-brand-block">
            <img src="@assets/images/logo/stemkey-logo.png" alt="STEMKEY" class="brand-logo" />
            <div class="header-titles">
              <h1 class="main-title">ĐÁNH GIÁ NĂNG LỰC TƯ DUY TOÁN HỌC</h1>
              <p class="sub-title">ĐÁNH GIÁ ĐẦU KỲ</p>
            </div>
          </div>
          <div class="student-pill">
            <span class="pill-name">{{ general.stu_nm }}</span>
          </div>
        </div>

        <div class="sheet-body">
          <!-- 1. Kết quả đánh giá theo câu hỏi riêng biệt (Thinking) -->
          <div class="report-block">
            <div class="block-title">
              <span class="title-bar"></span>
              <h3>Kết quả đánh giá theo câu hỏi riêng biệt</h3>
            </div>
            <div class="table-container">
              <table class="grid-table thinking-table">
                <thead>
                  <tr class="header-row-1">
                    <th rowspan="2" class="col-header-muc">Mục</th>
                    <th colspan="7" class="col-header-group">Năng lực tư duy cơ bản</th>
                    <th colspan="2" class="col-header-group">Tư duy nâng cao</th>
                    <th class="col-header-group">Ý tưởng sáng tạo<br/>giải quyết vấn đề</th>
                    <th rowspan="2" class="col-header-total">Tổng</th>
                  </tr>
                  <tr class="header-row-2">
                    <th v-for="q in thinking" :key="'th' + q.question_no" class="sub-qno">
                      {{ q.question_no }}
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="row-target-score">
                    <td class="cell-label">Điểm chuẩn</td>
                    <td v-for="q in thinking" :key="'tmax' + q.question_no" class="cell-val score-target">
                      {{ q.max_score }}
                    </td>
                    <td class="cell-val cell-sum-target">{{ thinkingMaxTotal }}</td>
                  </tr>
                  <tr class="row-actual-score">
                    <td class="cell-label">Điểm thực tế</td>
                    <td v-for="q in thinking" :key="'tscore' + q.question_no" class="cell-val score-actual">
                      {{ q.assigned_score }}
                    </td>
                    <td class="cell-val cell-sum-actual">{{ general.thinking_total }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- 2. Đánh giá theo lĩnh vực -->
          <div class="report-block">
            <div class="two-columns-row">
              <div class="col-left">
                <div class="block-title">
                  <span class="title-bar"></span>
                  <h3>Đánh giá theo lĩnh vực</h3>
                </div>
                <div class="table-container">
                  <table class="grid-table area-stat-table">
                    <thead>
                      <tr class="header-row">
                        <th style="width: 22%;">Phân loại</th>
                        <th style="width: 38%;">Nội dung đánh giá</th>
                        <th style="width: 10%;">Câu</th>
                        <th style="width: 10%;">Điểm<br/>chuẩn</th>
                        <th style="width: 10%;">Điểm<br/>thực tế</th>
                        <th style="width: 10%;">Tỷ lệ</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(stat, idx) in thkAreaStats" :key="idx">
                        <td class="text-center font-medium font-sm-cell">{{ stat.label }}</td>
                        <td class="font-xs-desc leading-tight text-center">{{ stat.desc }}</td>
                        <td class="text-center font-sm-cell">{{ stat.q_nums.join(',') }}</td>
                        <td class="text-center score-target">{{ stat.max }}</td>
                        <td class="text-center score-actual-red">{{ stat.score }}</td>
                        <td class="text-center pct-cnt">{{ stat.pct }}%</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- Radar + Horizontal bar -->
              <div class="col-right">
                <div class="chart-panel radar-panel flex-column-center">
                  <canvas ref="thkRadarCanvas" width="340" height="230" class="chart-canvas"></canvas>
                  <div class="horizontal-bar-box">
                    <canvas ref="thkHorizontalBarCanvas" width="340" height="95" class="chart-canvas"></canvas>
                    <p class="horizontal-bar-caption">Tỉ lệ năng lực giải quyết vấn đề</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- 3. So sánh năng lực giải quyết vấn đề theo cấp độ -->
          <div class="report-block">
            <div class="two-columns-row">
              <div class="col-left">
                <div class="block-title">
                  <span class="title-bar"></span>
                  <h3>So sánh năng lực giải quyết vấn đề theo cấp độ</h3>
                </div>
                <div class="table-container">
                  <table class="grid-table diff-stat-table">
                    <thead>
                      <tr class="header-row">
                        <th>Phân loại</th>
                        <th>Số lượng<br/>câu</th>
                        <th>Cụ thể</th>
                        <th>Điểm<br/>chuẩn</th>
                        <th>Điểm<br/>thực tế</th>
                        <th>Tỷ lệ</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="stat in thkDiffStats" :key="stat.label">
                        <td class="text-center font-medium">{{ stat.label }}</td>
                        <td class="text-center">{{ stat.q_nums.length }}</td>
                        <td class="text-center font-sm-cell">{{ stat.q_nums.join(',') }}</td>
                        <td class="text-center score-target">{{ stat.max }}</td>
                        <td class="text-center score-actual-red">{{ stat.score }}</td>
                        <td class="text-center pct-cnt">{{ stat.pct }}%</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <div class="col-right">
                <div class="chart-panel bar-panel">
                  <canvas ref="thkDiffBarCanvas" width="340" height="150" class="chart-canvas"></canvas>
                </div>
              </div>
            </div>
          </div>

          <!-- 4. Tiêu chí kết quả đánh giá năng lực tư duy toán học -->
          <div class="report-block">
            <div class="block-title">
              <span class="title-bar"></span>
              <h3>Kết quả đánh giá năng lực tư duy toán học</h3>
            </div>
            <div class="table-container">
              <table class="grid-table level-criteria-table">
                <thead>
                  <tr class="header-row">
                    <th style="width: 18%;">Điểm thực tế</th>
                    <th style="width: 14%;">Đánh giá</th>
                    <th style="width: 68%; text-align: left; padding-left: 16px;">Ghi chú</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(level, idx) in evaluationLevels" :key="idx"
                    :class="{ 'level-selected-row': isInRange(general.thinking_total, level.min, level.max) }">
                    <td class="text-center font-bold cell-range-bg">{{ level.range }}</td>
                    <td class="text-center font-bold cell-level-label">{{ level.label }}</td>
                    <td class="cell-level-note">{{ level.note }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </div>
      
    </div>
  </div>
</template>

<script>
import axios from '@/http/axios.js'

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
    isInRange(score, min, max) {
      return score >= min && score <= max
    },
    fetchDetail() {
      this.loading = true
      axios.g(`/api/lms/evaluations/igbh/results/${this.resultId}`).then(res => {
        this.loading = false
        if (res && res.data && res.data.status === 'success') {
          const { general, details, test_config, test_questions, test_comments } = res.data.data
          this.general = general
          this.testComments = test_comments || []
          
          if (test_config && test_config.sectors) {
             try {
                this.unitLabels = typeof test_config.sectors === 'string' ? JSON.parse(test_config.sectors) : test_config.sectors
             } catch (e) {}
          }

          const currConfigs = test_questions ? test_questions.filter(q => q.question_type === 'curriculum') : []
          const currLength = currConfigs.length > 0 ? currConfigs.length : 20

          this.curriculum = Array.from({ length: currLength }, (_, i) => {
            const no = i + 1
            const q = details.find(d => d.question_type === 'curriculum' && parseInt(d.question_no) === no)
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
            const q = details.find(d => d.question_type === 'thinking' && parseInt(d.question_no) === no)
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
            this.drawRadarChart(this.$refs.currRadarCanvas, Object.keys(this.unitStats), Object.values(this.unitStats).map(s => s.pct))
            this.drawRadarChart(this.$refs.thkRadarCanvas, this.thkAreaStats.map(s => s.label.split('.')[0]), this.thkAreaStats.map(s => s.pct))
            this.drawHorizontalBarChart(this.$refs.thkHorizontalBarCanvas, this.general.thinking_total, this.thinkingMaxTotal)
            this.drawBarChart(this.$refs.currDiffBarCanvas, this.currDiffStats)
            this.drawBarChart(this.$refs.currTypeBarCanvas, this.currTypeStats)
            this.drawBarChart(this.$refs.thkDiffBarCanvas, this.thkDiffStats)
          })
        }
      }).catch(err => {
        this.loading = false
      })
    },
    drawRadarChart(canvas, labels, values) {
      if (!canvas) return
      const ctx = canvas.getContext('2d')
      if (!ctx) return

      const cx = canvas.width / 2
      const cy = canvas.height / 2 - 12
      const radius = Math.min(cx, cy) - 24
      const n = labels.length
      if (n === 0) return

      ctx.clearRect(0, 0, canvas.width, canvas.height)

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
        ctx.font = 'bold 11px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
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
      const legendY = canvas.height - 12
      const legendW = 95
      const startX = cx - (datasets.length * legendW) / 2
      
      ctx.textAlign = 'left'
      ctx.font = '10px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
      datasets.forEach((ds, idx) => {
         ctx.fillStyle = ds.color
         ctx.fillRect(startX + idx*legendW, legendY - 6, 11, 11)
         ctx.fillStyle = '#334155'
         ctx.fillText(ds.label, startX + idx*legendW + 15, legendY + 3)
      })
    },
    drawBarChart(canvas, stats) {
       if (!canvas) return
       const ctx = canvas.getContext('2d')
       if (!ctx) return
       
       const w = canvas.width
       const h = canvas.height
       const padX = 35
       const padY = 16
       const chartW = w - padX * 2
       const chartH = h - padY * 2 - 24
       
       ctx.clearRect(0, 0, w, h)
       
       // Grid lines
       ctx.strokeStyle = '#f1f5f9'
       ctx.lineWidth = 1
       ctx.fillStyle = '#64748b'
       ctx.font = '10px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
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
          ctx.font = '11px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
          ctx.fillText(stat.label, groupCenterX, padY + chartH + 6)
       })
       
       // Legend
       const datasets = [
         { label: 'Thí sinh', color: '#ef4444' },
         { label: 'Các thí sinh', color: '#1e3a8a' },
         { label: 'Học sinh STEMKEY', color: '#65a30d' }
       ]
       const legendY = canvas.height - 10
       const legendW = 95
       const legendStartX = w/2 - (datasets.length * legendW) / 2
       
       ctx.textAlign = 'left'
       ctx.textBaseline = 'middle'
       ctx.font = '10px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
       datasets.forEach((ds, idx) => {
          ctx.fillStyle = ds.color
          ctx.fillRect(legendStartX + idx*legendW, legendY - 5, 10, 10)
          ctx.fillStyle = '#334155'
          ctx.fillText(ds.label, legendStartX + idx*legendW + 14, legendY + 1)
       })
    },
    drawHorizontalBarChart(canvas, score, maxScore) {
       if (!canvas) return
       const ctx = canvas.getContext('2d')
       if (!ctx) return
       
       const w = canvas.width
       const h = canvas.height
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
       const chartW = w - 120
       
       data.forEach((item, idx) => {
          const y = startY + idx * (barH + gap)
          
          ctx.fillStyle = '#f1f5f9'
          ctx.fillRect(chartX, y, chartW, barH)
          
          ctx.fillStyle = item.color
          const barWidth = chartW * (item.pct / 100)
          ctx.fillRect(chartX, y, barWidth, barH)
          
          ctx.fillStyle = '#ffffff'
          ctx.font = 'bold 11px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
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
          ctx.font = '10px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
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
/* ============================================================
   EXACT REPORT CSS (INDEPENDENT FROM TAILWIND & VUEXY)
   ============================================================ */

.igbh-report-wrapper {
  background-color: #f8fafc;
  padding: 24px 16px;
  min-height: 100vh;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  color: #1e293b;
}

/* Toolbar */
.action-toolbar {
  max-width: 900px;
  margin: 0 auto 20px auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #ffffff;
  padding: 16px 20px;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.back-link {
  font-size: 13px;
  color: #64748b;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 4px;
  margin-bottom: 4px;
  transition: color 0.15s ease;
}
.back-link:hover {
  color: #2563eb;
}

.toolbar-title {
  font-size: 18px;
  font-weight: 700;
  color: #0f172a;
  margin: 0;
}

.toolbar-sub {
  font-size: 12px;
  color: #64748b;
  margin-top: 4px;
}
.toolbar-sub .sep {
  margin: 0 8px;
  color: #cbd5e1;
}
.toolbar-sub .text-name {
  color: #2563eb;
}

.toolbar-btns {
  display: flex;
  gap: 10px;
}

.btn-print {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #1e3a8a;
  color: #ffffff;
  border: none;
  border-radius: 8px;
  padding: 8px 18px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.15s ease;
}
.btn-print:hover {
  background: #172554;
}

.btn-edit {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #ffffff;
  color: #2563eb;
  border: 1px solid #bfdbfe;
  border-radius: 8px;
  padding: 8px 16px;
  font-size: 13px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.15s ease;
}
.btn-edit:hover {
  background: #eff6ff;
  border-color: #93c5fd;
}

/* Loading */
.loading-state {
  text-align: center;
  padding: 80px 0;
  color: #64748b;
}
.spinner {
  width: 36px;
  height: 36px;
  border: 3px solid #e2e8f0;
  border-top-color: #2563eb;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin: 0 auto 12px auto;
}
@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Document Sheet */
.report-document {
  max-width: 900px;
  margin: 0 auto;
}

.report-sheet {
  background: #ffffff;
  border: 1px solid #cbd5e1;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 16px rgba(0,0,0,0.06);
  margin-bottom: 28px;
}

/* Header */
.sheet-header {
  background-color: #1e3a8a;
  color: #ffffff;
  padding: 16px 28px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-brand-block {
  display: flex;
  align-items: center;
  gap: 16px;
}

.brand-logo {
  height: 42px;
  width: auto;
  background: rgba(255, 255, 255, 0.15);
  padding: 2px 6px;
  border-radius: 6px;
  object-fit: contain;
}

.header-titles .main-title {
  font-size: 20px;
  font-weight: 800;
  letter-spacing: 0.5px;
  margin: 0;
  color: #ffffff;
  line-height: 1.2;
}

.header-titles .sub-title {
  font-size: 11px;
  font-weight: 600;
  color: #bfdbfe;
  margin: 2px 0 0 0;
  letter-spacing: 0.8px;
}

.student-pill {
  background: #ffffff;
  border-radius: 9999px;
  padding: 6px 22px;
  display: flex;
  align-items: center;
  gap: 20px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.12);
}

.pill-item {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  color: #1e3a8a;
  font-weight: 600;
}
.pill-item .pill-label {
  color: #1e3a8a;
}
.pill-item .pill-val {
  font-weight: 700;
}
.pill-item .level-val {
  color: #4f46e5;
}

.pill-name {
  font-size: 15px;
  font-weight: 800;
  color: #1e3a8a;
}

/* Body */
.sheet-body {
  padding: 24px;
}

.report-block {
  margin-bottom: 24px;
}
.report-block:last-child {
  margin-bottom: 0;
}

.block-title {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 10px;
}

.block-title .title-bar {
  width: 5px;
  height: 16px;
  background-color: #1e3a8a;
  border-radius: 3px;
  display: inline-block;
}

.block-title h3 {
  font-size: 14px;
  font-weight: 700;
  color: #1e3a8a;
  margin: 0;
}

/* Tables */
.table-container {
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  overflow: hidden;
}

.grid-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 12px;
}

.grid-table th {
  background-color: #1d4ed8;
  color: #ffffff;
  font-weight: 600;
  padding: 7px 4px;
  border-right: 1px solid #2563eb;
  border-bottom: 1px solid #2563eb;
}
.grid-table th:last-child {
  border-right: none;
}

.grid-table td {
  padding: 7px 4px;
  border-right: 1px solid #e2e8f0;
  border-bottom: 1px solid #e2e8f0;
  color: #334155;
}
.grid-table td:last-child {
  border-right: none;
}
.grid-table tr:last-child td {
  border-bottom: none;
}

/* Question 20-col table */
.question-table .col-muc {
  width: 70px;
  text-align: left;
  padding-left: 12px;
}
.question-table .col-qno {
  width: 38px;
  text-align: center;
}
.question-table .cell-label {
  background-color: #eff6ff;
  color: #1e3a8a;
  font-weight: 600;
  text-align: left;
  padding-left: 12px;
}
.question-table .cell-val {
  text-align: center;
  font-size: 12px;
}
.question-table .cell-val.bold-txt {
  font-weight: 700;
  color: #1e293b;
}

.res-o {
  color: #ef4444;
  font-weight: 800;
  font-size: 14px;
}
.res-x {
  color: #0d9488;
  font-weight: 800;
  font-size: 14px;
}
.res-dash {
  color: #94a3b8;
  font-size: 12px;
}

/* 2-Columns Row */
.two-columns-row {
  display: flex;
  gap: 20px;
  align-items: stretch;
}
.col-left {
  flex: 1;
  min-width: 0;
}
.col-right {
  flex: 1;
  min-width: 0;
  display: flex;
}

.chart-panel {
  width: 100%;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  padding: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #ffffff;
}

.chart-canvas {
  max-width: 100%;
  display: block;
}

.correct-cnt {
  color: #ef4444;
  font-weight: 700;
}
.total-cnt {
  color: #64748b;
  font-size: 11px;
}
.pct-cnt {
  color: #0d9488;
  font-weight: 700;
}

.row-total {
  background-color: #eff6ff;
  font-weight: 700;
}

/* Comment Box */
.comment-box {
  background-color: #f8fafc;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  padding: 16px 20px;
}
.comment-content {
  font-size: 13px;
  color: #334155;
  line-height: 1.65;
  margin: 0;
}
:deep(.comment-hl-good) {
  color: #0d9488;
  font-weight: 700;
}
:deep(.comment-hl-weak) {
  color: #ef4444;
  font-weight: 700;
}

/* Page 2 Specifics */
.header-row-1 th {
  background-color: #1d4ed8;
  padding: 6px;
  border: 1px solid #2563eb;
}
.header-row-2 th {
  background-color: #2563eb;
  padding: 5px;
  font-weight: 500;
  border: 1px solid #3b82f6;
  text-align: center;
  width: 50px;
}
.col-header-muc {
  width: 85px;
  text-align: center;
}
.col-header-group {
  text-align: center;
}
.col-header-total {
  width: 65px;
  text-align: center;
}

.score-target {
  color: #15803d;
  font-weight: 700;
}
.score-actual {
  color: #0d9488;
  font-weight: 700;
}
.score-actual-red {
  color: #ef4444;
  font-weight: 700;
}
.cell-sum-target {
  font-weight: 700;
  background-color: #eff6ff;
  color: #1e3a8a;
  text-align: center;
}
.cell-sum-actual {
  font-weight: 800;
  background-color: #dbeafe;
  color: #ef4444;
  text-align: center;
  font-size: 13px;
}

.font-sm-cell {
  font-size: 11px;
}
.font-xs-desc {
  font-size: 10px;
  color: #475569;
  line-height: 1.3;
}

.flex-column-center {
  flex-direction: column;
}
.horizontal-bar-box {
  width: 100%;
  border-top: 1px solid #e2e8f0;
  padding-top: 10px;
  margin-top: 10px;
  text-align: center;
}
.horizontal-bar-caption {
  font-size: 11px;
  font-weight: 700;
  color: #334155;
  margin: 4px 0 0 0;
}

/* Criteria table */
.cell-range-bg {
  background-color: #dee5f5;
  color: #1e3a8a;
}
.cell-level-label {
  color: #1e293b;
}
.cell-level-note {
  font-size: 11px;
  color: #334155;
  line-height: 1.5;
  padding: 8px 12px;
}

.level-selected-row {
  outline: 2px solid #f87171 !important;
  background-color: #fef2f2 !important;
}

</style>

<style>
/* ============================================================
   HIGH-PRECISION PRINT STYLES (UNSCOPED FOR FULL LAYOUT OVERRIDE)
   ============================================================ */
@media print {
  header,
  aside,
  nav,
  footer,
  .v-nav-menu,
  .v-nav-menu-swipe-area,
  .vx-navbar-wrapper,
  .the-navbar,
  .the-navbar-vertical,
  .the-navbar-horizontal,
  .the-footer,
  .the-customizer,
  .vx-tour,
  #content-overlay,
  .content-area__header,
  .print-hide,
  .action-toolbar,
  .vs-sidebar,
  .vs-sidebar--background,
  #btnVNavMenuMinToggler {
    display: none !important;
    visibility: hidden !important;
  }

  html, body {
    margin: 0 !important;
    padding: 0 !important;
    background: #ffffff !important;
    background-color: #ffffff !important;
    width: 100% !important;
    height: auto !important;
    overflow: visible !important;
  }

  #app,
  .layout--main,
  #content-area,
  .content-wrapper,
  .router-view,
  .router-content {
    position: static !important;
    margin: 0 !important;
    padding: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
    background: #ffffff !important;
    background-color: #ffffff !important;
    box-shadow: none !important;
    border: none !important;
    left: 0 !important;
    top: 0 !important;
  }

  #content-area {
    margin-left: 0 !important;
    padding: 0 !important;
  }

  .igbh-report-wrapper {
    background: #ffffff !important;
    padding: 0 !important;
    margin: 0 !important;
  }

  .report-document {
    max-width: 820px !important;
    width: 100% !important;
    margin: 0 auto !important;
    padding: 0 !important;
    box-shadow: none !important;
    border: none !important;
  }

  .report-sheet {
    box-shadow: none !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 0 !important;
    margin: 0 auto !important;
    padding: 24px 28px !important;
    page-break-after: always !important;
    break-after: page !important;
    box-sizing: border-box !important;
    width: 100% !important;
  }

  .sheet-page-2 {
    page-break-before: always !important;
    break-before: page !important;
    margin-top: 0 !important;
  }

  * {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    color-adjust: exact !important;
  }

  @page {
    size: A4 portrait;
    margin: 0.6cm;
  }
}
</style>
