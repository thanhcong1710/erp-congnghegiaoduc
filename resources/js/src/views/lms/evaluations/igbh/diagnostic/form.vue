<template>
  <div class="igbh-form-page">
    <!-- Saving Overlay -->
    <div v-if="saving" class="saving-overlay">
      <div class="saving-modal">
        <div class="saving-spinner"></div>
        <p class="saving-title">Đang lưu dữ liệu...</p>
        <p class="saving-sub">Vui lòng đợi trong giây lát</p>
      </div>
    </div>

    <!-- Header -->
    <div class="header-container">
      <div class="breadcrumb-nav">
        <router-link to="/lms/evaluations/igbh/diagnostic" class="breadcrumb-link">
          Danh sách
        </router-link>
      </div>
      <h1 class="page-title">Nhập Điểm - IG.BH Đầu Vào</h1>
      <p class="page-meta" v-if="general">
        <span>{{ general.test_nm }}</span>
        <span class="meta-sep">|</span>
        <span>Học sinh: <strong class="student-highlight">{{ general.stu_nm }}</strong></span>
        <span class="meta-sep" v-if="general.stu_birth_dt">|</span>
        <span v-if="general.stu_birth_dt">Ngày sinh: {{ general.stu_birth_dt }}</span>
        <span class="meta-sep" v-if="general.reg_name">|</span>
        <span v-if="general.reg_name">GV: {{ general.reg_name }}</span>
      </p>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="loading-container">
      <div class="spinner"></div>
      <p class="loading-text">Đang tải đề thi và dữ liệu đánh giá...</p>
    </div>

    <template v-else-if="general">
      <!-- Top Card: Ngày thi & Level -->
      <div class="form-card general-card">
        <div class="fields-row">
          <div class="field-col">
            <label class="field-label">NGÀY THI</label>
            <div class="input-wrapper">
              <input
                type="date"
                v-model="form.eval_dt"
                class="form-control"
              />
            </div>
          </div>
          <div class="field-col">
            <label class="field-label">LEVEL ĐƯỢC GÁN</label>
            <div class="input-wrapper">
              <input
                type="text"
                v-model="form.assigned_level"
                placeholder="VD: L1, L2..."
                class="form-control"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Card 1: Đáp án dựa trên chương trình giảng dạy (20 câu) -->
      <div class="form-card curriculum-card">
        <div class="card-header">
          <h2 class="card-title">📚 Đáp án dựa trên chương trình giảng dạy</h2>
          <p class="card-subtitle">Nhập số đáp án (1~5) cho từng câu. Mỗi câu đúng = 2 điểm.</p>
        </div>
        <div class="card-body">
          <div class="table-scroll-wrap">
            <table class="grid-table">
              <thead>
                <tr>
                  <th v-for="q in curriculum" :key="'h_' + q.question_no" class="cell-head">
                    {{ q.question_no }}
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td
                    v-for="(q, idx) in curriculum"
                    :key="'in_' + q.question_no"
                    :class="['cell-input', q.is_correct === 'O' ? 'status-correct-bg' : (q.is_correct === 'X' ? 'status-wrong-bg' : '')]"
                  >
                    <input
                      type="text"
                      v-model="q.assigned_score"
                      maxlength="1"
                      :id="'answer_' + q.question_no"
                      @input="onCurriculumInput(idx, $event)"
                      @keyup.enter="focusNext('answer', idx + 1, curriculum.length, 'score', 0)"
                      class="ans-box"
                      :class="q.is_correct === 'O' ? 'border-correct' : (q.is_correct === 'X' ? 'border-wrong' : 'border-normal')"
                    />
                  </td>
                </tr>
                <tr>
                  <td
                    v-for="q in curriculum"
                    :key="'r_' + q.question_no"
                    class="cell-result"
                  >
                    <span v-if="q.is_correct === 'O'" class="mark-o">O</span>
                    <span v-else-if="q.is_correct === 'X'" class="mark-x">X</span>
                    <span v-else class="mark-dash">-</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="section-summary">
            <span class="summary-item">
              Số câu đúng: <strong class="val-green">{{ correctCount }}</strong>/{{ curriculum.length }}
            </span>
            <span class="summary-item">
              Điểm kiến thức: <strong class="val-blue">{{ subjectTotal }}</strong>
            </span>
          </div>
        </div>
      </div>

      <!-- Card 2: Kỹ năng tư duy toán học (10 câu) -->
      <div class="form-card thinking-card">
        <div class="card-header">
          <h2 class="card-title">🧮 Kỹ năng tư duy toán học</h2>
          <p class="card-subtitle">Score_8~9: 0~7</p>
        </div>
        <div class="card-body">
          <div class="table-scroll-wrap">
            <table class="grid-table">
              <thead>
                <tr>
                  <th v-for="q in thinking" :key="'th_h_' + q.question_no" class="cell-head">
                    {{ q.question_no }} (0~{{ q.max_score }})
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td
                    v-for="(q, idx) in thinking"
                    :key="'th_in_' + q.question_no"
                    class="cell-input"
                  >
                    <input
                      type="number"
                      v-model.number="q.assigned_score"
                      :min="0"
                      :max="q.max_score"
                      :id="'score_' + q.question_no"
                      @input="onThinkingInput(idx, q)"
                      @keyup.enter="focusNext('score', idx + 1, thinking.length, 'save', 0)"
                      class="ans-box-thinking"
                    />
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="section-summary">
            <span class="summary-item">
              Tổng điểm tư duy: <strong class="val-blue">{{ thinkingTotal }}</strong>
            </span>
          </div>
        </div>
      </div>

      <!-- Big Total Summary Banner -->
      <div class="total-summary-banner">
        <div class="summary-col">
          <div class="summary-label">ĐIỂM KIẾN THỨC:</div>
          <div class="summary-val val-green-big">{{ subjectTotal }}</div>
        </div>
        <div class="summary-op">+</div>
        <div class="summary-col">
          <div class="summary-label">TỔNG ĐIỂM TƯ DUY:</div>
          <div class="summary-val val-purple-big">{{ thinkingTotal }}</div>
        </div>
        <div class="summary-op">=</div>
        <div class="summary-col">
          <div class="summary-label">TỔNG ĐIỂM</div>
          <div class="summary-val val-dark-big">{{ totalScore }}</div>
        </div>
      </div>

      <!-- Actions Footer -->
      <div class="action-footer">
        <button
          @click="$router.push('/lms/evaluations/igbh/diagnostic')"
          type="button"
          class="btn-cancel"
        >
          Hủy
        </button>
        <div class="action-right">
          <button
            v-if="general.total_score > 0 || isGraded"
            @click="$router.push(`/lms/evaluations/igbh/diagnostic/report/${resultId}`)"
            type="button"
            class="btn-view-report"
          >
            Xem kết quả
          </button>
          <button
            @click="saveGrades"
            :disabled="saving"
            type="button"
            class="btn-save"
            id="save_btn"
          >
            {{ saving ? 'Đang lưu...' : 'Lưu Điểm' }}
          </button>
        </div>
      </div>
    </template>
  </div>
</template>

<script>
import axios from '@/http/axios.js'

export default {
  name: 'IgbhDiagnosticForm',
  data() {
    return {
      resultId: null,
      general: null,
      curriculum: [],
      thinking: [],
      questions: [],
      form: {
        eval_dt: new Date().toISOString().substring(0, 10),
        assigned_level: ''
      },
      loading: false,
      saving: false,
      isGraded: false
    }
  },
  computed: {
    correctCount() {
      return this.curriculum.filter(q => q.is_correct === 'O').length
    },
    subjectTotal() {
      return this.curriculum.reduce((sum, q) => q.is_correct === 'O' ? sum + (q.point || 2) : sum, 0)
    },
    thinkingTotal() {
      return this.thinking.reduce((sum, q) => sum + (parseInt(q.assigned_score) || 0), 0)
    },
    totalScore() {
      return this.subjectTotal + this.thinkingTotal
    }
  },
  created() {
    this.resultId = this.$route.params.id
    this.fetchData()
  },
  methods: {
    fetchData() {
      this.loading = true
      axios.g(`/api/lms/evaluations/igbh/results/${this.resultId}`).then(res => {
        this.loading = false
        if (res && res.data && res.data.status === 'success') {
          const d = res.data.data
          this.general = d.general
          this.form.eval_dt = d.general.eval_dt ? d.general.eval_dt.substring(0, 10) : new Date().toISOString().substring(0, 10)
          this.form.assigned_level = d.general.assigned_level || ''
          if (d.general.total_score > 0) {
            this.isGraded = true
          }

          const currConfigs = d.questions ? d.questions.filter(q => q.question_type === 'curriculum') : []
          const currLength = currConfigs.length > 0 ? currConfigs.length : 20
          const savedCurr = d.curriculum || []

          // Build Curriculum questions
          this.curriculum = Array.from({ length: currLength }, (_, i) => {
            const no = i + 1
            const q = savedCurr.find(item => parseInt(item.question_no) === no)
            const conf = currConfigs.find(c => c.sort_no === no)
            const ans = q ? q.assigned_score : ''
            const correctAns = conf ? conf.answer : null
            let isCorrect = q ? q.is_correct : null
            if (!isCorrect && ans && correctAns) {
              isCorrect = (ans == correctAns) ? 'O' : 'X'
            }

            return {
              question_no: no,
              seq_id: conf ? conf.id : (q ? q.seq_id : null),
              assigned_score: ans,
              unit: conf ? conf.sector : (q ? q.unit : null),
              correct_answer: correctAns,
              is_correct: isCorrect,
              point: conf ? conf.standard_point : (q ? (q.point || 2) : 2)
            }
          })

          // Build Thinking questions
          const thkConfigs = d.questions ? d.questions.filter(q => q.question_type === 'thinking') : []
          const thkLength = thkConfigs.length > 0 ? thkConfigs.length : 10
          const savedThinking = d.thinking || []

          this.thinking = Array.from({ length: thkLength }, (_, i) => {
            const no = i + 1
            const q = savedThinking.find(item => parseInt(item.question_no) === no)
            const conf = thkConfigs.find(c => c.sort_no === no)

            return {
              question_no: no,
              seq_id: conf ? conf.id : (q ? q.seq_id : null),
              assigned_score: q ? (parseInt(q.assigned_score) || 0) : 0,
              max_score: conf ? (conf.standard_point || 5) : (q ? (q.max_score || 5) : (no <= 7 ? 5 : (no <= 9 ? 7 : 11)))
            }
          })
        }
      }).catch(err => {
        this.loading = false
        console.error('Fetch error:', err)
      })
    },
    onCurriculumInput(idx, event) {
      const val = event.target.value
      const q = this.curriculum[idx]

      // Allow only numbers 1-5
      if (val && (isNaN(val) || val < 1 || val > 5)) {
        this.curriculum[idx].assigned_score = ''
        this.curriculum[idx].is_correct = null
        return
      }

      if (val && val.length >= 1) {
        if (q.correct_answer) {
          this.curriculum[idx].is_correct = (val == q.correct_answer) ? 'O' : 'X'
        } else {
          this.curriculum[idx].is_correct = 'O'
        }

        const nextIdx = idx + 1
        if (nextIdx < this.curriculum.length) {
          this.$nextTick(() => {
            const nextEl = document.getElementById('answer_' + (nextIdx + 1))
            if (nextEl) nextEl.select()
          })
        } else {
          this.$nextTick(() => {
            const thEl = document.getElementById('score_1')
            if (thEl) thEl.select()
          })
        }
      } else {
        this.curriculum[idx].is_correct = null
      }
    },
    onThinkingInput(idx, q) {
      const max = q.max_score
      if (q.assigned_score > max) {
        this.$vs.notify({
          title: 'Vượt quá thang điểm',
          text: `Câu ${q.question_no} điểm tối đa là ${max}`,
          color: 'warning'
        })
        this.thinking[idx].assigned_score = max
        return
      }
      if (q.assigned_score < 0 || isNaN(q.assigned_score)) {
        this.thinking[idx].assigned_score = 0
      }
    },
    focusNext(prefix, nextIdx, maxIdx, altPrefix, altIdx) {
      if (nextIdx < maxIdx) {
        const el = document.getElementById(`${prefix}_${nextIdx + 1}`)
        if (el) el.select()
      } else {
        const altEl = document.getElementById(`${altPrefix}_${altIdx + 1}`)
        if (altEl) altEl.select()
      }
    },
    saveGrades() {
      this.saving = true
      const payload = {
        eval_dt: this.form.eval_dt,
        assigned_level: this.form.assigned_level,
        curriculum: this.curriculum.map(q => ({
          question_no: q.question_no,
          seq_id: q.seq_id,
          assigned_score: q.assigned_score,
          unit: q.unit
        })),
        thinking: this.thinking.map(q => ({
          question_no: q.question_no,
          seq_id: q.seq_id,
          assigned_score: q.assigned_score,
          max_score: q.max_score
        }))
      }

      axios.p(`/api/lms/evaluations/igbh/results/${this.resultId}/grade`, payload).then(res => {
        this.saving = false
        if (res && res.data && res.data.status === 'success') {
          this.isGraded = true
          this.$vs.notify({
            title: 'Thành công',
            text: 'Đã lưu điểm khảo sát thành công!',
            color: 'success'
          })
          this.fetchData()
        } else {
          this.$vs.notify({
            title: 'Lỗi',
            text: 'Không thể lưu điểm. Vui lòng thử lại.',
            color: 'danger'
          })
        }
      }).catch(err => {
        this.saving = false
        console.error('Save grade error:', err)
        this.$vs.notify({
          title: 'Lỗi',
          text: 'Có lỗi xảy ra khi lưu điểm.',
          color: 'danger'
        })
      })
    }
  }
}
</script>

<style scoped>
.igbh-form-page {
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  color: #1e293b;
  max-width: 1200px;
  margin: 0 auto;
  padding: 10px 0 40px 0;
}

/* Saving Overlay */
.saving-overlay {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.45);
  backdrop-filter: blur(4px);
  z-index: 99999;
  display: flex;
  align-items: center;
  justify-content: center;
}

.saving-modal {
  background: #ffffff;
  padding: 28px 36px;
  border-radius: 16px;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  text-align: center;
  min-width: 240px;
}

.saving-spinner {
  width: 44px;
  height: 44px;
  border: 4px solid #e0e7ff;
  border-top-color: #4f46e5;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin: 0 auto 16px auto;
}

.saving-title {
  font-weight: 700;
  font-size: 16px;
  color: #1e293b;
  margin: 0;
}

.saving-sub {
  font-size: 13px;
  color: #64748b;
  margin-top: 4px;
}

/* Header */
.header-container {
  margin-bottom: 24px;
}

.breadcrumb-nav {
  margin-bottom: 6px;
}

.breadcrumb-link {
  font-size: 14px;
  color: #64748b;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.2s;
}

.breadcrumb-link:hover {
  color: #334155;
}

.page-title {
  font-size: 24px;
  font-weight: 800;
  color: #0f172a;
  margin: 0 0 6px 0;
  letter-spacing: -0.01em;
}

.page-meta {
  font-size: 14px;
  color: #64748b;
  margin: 0;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 8px;
}

.meta-sep {
  color: #cbd5e1;
}

.student-highlight {
  color: #4f46e5;
  font-weight: 700;
}

/* Loading */
.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 80px 0;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 3px solid #e2e8f0;
  border-top-color: #4f46e5;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin-bottom: 12px;
}

.loading-text {
  font-size: 14px;
  color: #64748b;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Form Cards */
.form-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
  margin-bottom: 24px;
  overflow: hidden;
}

.general-card {
  padding: 20px 24px;
}

.fields-row {
  display: flex;
  flex-wrap: wrap;
  gap: 24px;
}

.field-col {
  flex: 1;
  min-width: 240px;
}

.field-label {
  display: block;
  font-size: 13px;
  font-weight: 700;
  text-transform: uppercase;
  color: #64748b;
  letter-spacing: 0.05em;
  margin-bottom: 8px;
}

.form-control {
  width: 100%;
  height: 42px;
  padding: 0 14px;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  font-size: 14px;
  color: #1e293b;
  background: #ffffff;
  transition: border-color 0.15s, box-shadow 0.15s;
  box-sizing: border-box;
}

.form-control:focus {
  outline: none;
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
}

/* Card Header & Body */
.card-header {
  padding: 16px 24px;
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
}

.card-title {
  font-size: 16px;
  font-weight: 700;
  color: #1e293b;
  margin: 0;
}

.card-subtitle {
  font-size: 13px;
  color: #64748b;
  margin: 4px 0 0 0;
}

.card-body {
  padding: 20px 24px;
}

/* Table scroll & grid */
.table-scroll-wrap {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

.grid-table {
  width: 100%;
  border-collapse: collapse;
  text-align: center;
}

.cell-head {
  padding: 10px 4px;
  font-size: 13px;
  font-weight: 700;
  color: #475569;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  white-space: nowrap;
}

.cell-input {
  border: 1px solid #e2e8f0;
  padding: 6px 4px;
  background: #ffffff;
  transition: background-color 0.15s;
}

.status-correct-bg {
  background-color: #ecfdf5 !important;
}

.status-wrong-bg {
  background-color: #fef2f2 !important;
}

.ans-box {
  width: 38px;
  height: 38px;
  text-align: center;
  font-size: 15px;
  font-weight: 800;
  color: #1e293b;
  border-radius: 8px;
  outline: none;
  transition: all 0.15s;
  background: #ffffff;
}

.border-normal {
  border: 1px solid #cbd5e1;
}

.border-correct {
  border: 2px solid #10b981;
  color: #047857;
  background: #f0fdf4;
}

.border-wrong {
  border: 2px solid #f87171;
  color: #b91c1c;
  background: #fef2f2;
}

.ans-box:focus {
  border-color: #6366f1;
  box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
}

.cell-result {
  border: 1px solid #e2e8f0;
  padding: 6px 4px;
  font-size: 14px;
  font-weight: 800;
}

.mark-o {
  color: #10b981;
}

.mark-x {
  color: #ef4444;
}

.mark-dash {
  color: #94a3b8;
}

/* Thinking Inputs */
.ans-box-thinking {
  width: 48px;
  height: 38px;
  text-align: center;
  font-size: 15px;
  font-weight: 700;
  color: #1e293b;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  outline: none;
  transition: all 0.15s;
}

.ans-box-thinking:focus {
  border-color: #6366f1;
  box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
}

/* Summary below table */
.section-summary {
  display: flex;
  gap: 20px;
  margin-top: 14px;
  font-size: 14px;
  color: #64748b;
}

.val-green {
  color: #10b981;
  font-weight: 800;
}

.val-blue {
  color: #4f46e5;
  font-weight: 800;
}

/* Total Summary Banner */
.total-summary-banner {
  background: #eff2fe;
  border: 1px solid #dbeafe;
  border-radius: 16px;
  padding: 24px 32px;
  display: flex;
  align-items: center;
  justify-content: flex-start;
  gap: 36px;
  margin-bottom: 24px;
}

.summary-col {
  text-align: left;
}

.summary-label {
  font-size: 13px;
  font-weight: 700;
  text-transform: uppercase;
  color: #6366f1;
  letter-spacing: 0.05em;
  margin-bottom: 4px;
}

.summary-val {
  font-size: 32px;
  line-height: 1;
  font-weight: 900;
}

.val-green-big {
  color: #10b981;
}

.val-purple-big {
  color: #4f46e5;
}

.val-dark-big {
  color: #0f172a;
}

.summary-op {
  font-size: 26px;
  font-weight: 400;
  color: #94a3b8;
}

/* Actions Footer */
.action-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-top: 16px;
  border-top: 1px solid #e2e8f0;
}

.action-right {
  display: flex;
  gap: 12px;
}

.btn-cancel {
  padding: 10px 22px;
  background: #ffffff;
  border: 1px solid #cbd5e1;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 600;
  color: #475569;
  cursor: pointer;
  transition: all 0.15s;
}

.btn-cancel:hover {
  background: #f1f5f9;
  color: #1e293b;
}

.btn-view-report {
  padding: 10px 22px;
  background: #ffffff;
  border: 1px solid #10b981;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 700;
  color: #059669;
  cursor: pointer;
  transition: all 0.15s;
}

.btn-view-report:hover {
  background: #ecfdf5;
}

.btn-save {
  padding: 10px 28px;
  background: #4f46e5;
  border: 1px solid #4f46e5;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 700;
  color: #ffffff;
  cursor: pointer;
  box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3);
  transition: all 0.15s;
}

.btn-save:hover {
  background: #4338ca;
  box-shadow: 0 6px 14px rgba(79, 70, 229, 0.4);
}

.btn-save:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

@media (max-width: 768px) {
  .total-summary-banner {
    flex-direction: column;
    align-items: flex-start;
    gap: 16px;
  }
  .summary-op {
    display: none;
  }
}
</style>
