<template>
  <div class="igbh-summative-form-page">
    <!-- Saving Overlay -->
    <div v-if="saving" class="saving-overlay">
      <div class="saving-modal">
        <div class="saving-spinner"></div>
        <p class="saving-title">Đang lưu dữ liệu...</p>
        <p class="saving-sub">Vui lòng đợi trong giây lát</p>
      </div>
    </div>

    <!-- Header -->
    <div class="header-section">
      <div class="header-left">
        <router-link to="/lms/evaluations/igbh/summative" class="back-link">
          &larr; Danh sách
        </router-link>
        <h1 class="page-title">Nhập điểm đánh giá cuối kỳ</h1>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="loading-container">
      <div class="spinner"></div>
      <p class="loading-text">Đang tải thông tin bài thi cuối kỳ...</p>
    </div>

    <!-- Form Container -->
    <div v-else-if="formData" class="form-card">
      <!-- Top Header Summary Bar -->
      <div class="top-meta-bar">
        <div class="meta-item">
          <div class="meta-label">LEVEL</div>
          <div class="meta-value">{{ formData.student_info.test_nm }}</div>
        </div>
        <div class="meta-item">
          <div class="meta-label">Tên lớp</div>
          <div class="meta-value">{{ formData.student_info.class_nm }}</div>
        </div>
        <div class="meta-item meta-date">
          <div class="meta-label">Ngày kiểm tra</div>
          <div class="meta-value">
            <input
              type="date"
              v-model="formData.student_info.eval_dt"
              class="date-control"
            />
          </div>
        </div>
      </div>

      <!-- Main Matrix Grading Table -->
      <div class="table-wrap">
        <table class="matrix-table">
          <tbody>
            <!-- Row 1 -->
            <tr class="tr-bg-header">
              <th class="th-student-col">Học sinh Tên</th>
              <th colspan="16" class="th-main-title">Đánh giá Kiểu</th>
            </tr>

            <!-- Row 2 -->
            <tr class="tr-bg-header">
              <td class="td-student-name" rowspan="8">
                {{ formData.student_info.stu_nm }}
              </td>
              <th colspan="16" class="th-section-title">
                Đánh giá nội dung câu hỏi tự luận
              </th>
            </tr>

            <!-- Row 3: Question 1 - 4 labels -->
            <tr class="tr-white">
              <th colspan="4" class="th-q-num">No. 1</th>
              <th colspan="4" class="th-q-num">No. 2</th>
              <th colspan="4" class="th-q-num">No. 3</th>
              <th colspan="4" class="th-q-num">No. 4</th>
            </tr>

            <!-- Row 4: Criteria headers for 1 - 4 -->
            <tr class="tr-white">
              <template v-for="n in 4">
                <th :key="'h_c_' + n" class="th-sub-criterion">Khái niệm<br>hiểu</th>
                <th :key="'h_s_' + n" class="th-sub-criterion">Chiến<br>lược<br>suy luận</th>
                <th :key="'h_calc_' + n" class="th-sub-criterion">Tính toán<br>thực hành</th>
                <th :key="'h_exp_' + n" class="th-sub-criterion">Diễn đạt<br>biểu hiện</th>
              </template>
            </tr>

            <!-- Row 5: Inputs for Questions 1 - 4 -->
            <tr class="tr-white">
              <template v-for="n in 4">
                <td :key="'in_c_' + n" class="td-input-cell">
                  <input
                    type="number"
                    v-model.number="formData.subjective_data[n - 1].concept"
                    @input="limitInput(formData.subjective_data[n - 1], 'concept', 0, 6, $event)"
                    min="0"
                    max="6"
                    class="score-input"
                  />
                </td>
                <td :key="'in_s_' + n" class="td-input-cell">
                  <input
                    type="number"
                    v-model.number="formData.subjective_data[n - 1].strategy"
                    @input="limitInput(formData.subjective_data[n - 1], 'strategy', 0, 6, $event)"
                    min="0"
                    max="6"
                    class="score-input"
                  />
                </td>
                <td :key="'in_calc_' + n" class="td-input-cell">
                  <input
                    type="number"
                    v-model.number="formData.subjective_data[n - 1].calculation"
                    @input="limitInput(formData.subjective_data[n - 1], 'calculation', 0, 6, $event)"
                    min="0"
                    max="6"
                    class="score-input"
                  />
                </td>
                <td :key="'in_exp_' + n" class="td-input-cell">
                  <input
                    type="number"
                    v-model.number="formData.subjective_data[n - 1].expression"
                    @input="limitInput(formData.subjective_data[n - 1], 'expression', 0, 6, $event)"
                    min="0"
                    max="6"
                    class="score-input"
                  />
                </td>
              </template>
            </tr>

            <!-- Row 6: Split headers -->
            <tr class="tr-bg-header">
              <th colspan="4" class="th-section-title">
                Đánh giá nội dung câu hỏi tự luận
              </th>
              <th colspan="12" class="th-section-title">
                Thành tích theo từng bài học
              </th>
            </tr>

            <!-- Row 7: Question 5 & Lessons 6-17 -->
            <tr class="tr-white">
              <th colspan="4" class="th-q-num">No. 5</th>
              <template v-for="n in 12">
                <th :key="'hw_' + n" class="th-lesson-col" rowspan="2">
                  No. {{ n + 5 }}<br>
                  <span class="max-pts-text" v-if="formData.weekly_data && formData.weekly_data[n - 1]">
                    ({{ formData.weekly_data[n - 1].max_score }} Điểm)
                  </span>
                </th>
              </template>
            </tr>

            <!-- Row 8: Question 5 criteria headers -->
            <tr class="tr-white">
              <th class="th-sub-criterion">Khái niệm<br>hiểu</th>
              <th class="th-sub-criterion">Chiến<br>lược<br>suy luận</th>
              <th class="th-sub-criterion">Tính toán<br>thực hành</th>
              <th class="th-sub-criterion">Diễn đạt<br>biểu hiện</th>
            </tr>

            <!-- Row 9: Question 5 inputs + Lessons 6-17 inputs -->
            <tr class="tr-white">
              <!-- Q5 Concept -->
              <td class="td-input-cell">
                <input
                  type="number"
                  v-model.number="formData.subjective_data[4].concept"
                  @input="limitInput(formData.subjective_data[4], 'concept', 0, 6, $event)"
                  min="0"
                  max="6"
                  class="score-input"
                />
              </td>
              <!-- Q5 Strategy -->
              <td class="td-input-cell">
                <input
                  type="number"
                  v-model.number="formData.subjective_data[4].strategy"
                  @input="limitInput(formData.subjective_data[4], 'strategy', 0, 6, $event)"
                  min="0"
                  max="6"
                  class="score-input"
                />
              </td>
              <!-- Q5 Calculation -->
              <td class="td-input-cell">
                <input
                  type="number"
                  v-model.number="formData.subjective_data[4].calculation"
                  @input="limitInput(formData.subjective_data[4], 'calculation', 0, 6, $event)"
                  min="0"
                  max="6"
                  class="score-input"
                />
              </td>
              <!-- Q5 Expression -->
              <td class="td-input-cell">
                <input
                  type="number"
                  v-model.number="formData.subjective_data[4].expression"
                  @input="limitInput(formData.subjective_data[4], 'expression', 0, 6, $event)"
                  min="0"
                  max="6"
                  class="score-input"
                />
              </td>

              <!-- Weekly workbooks 6 - 17 -->
              <template v-for="(wd, index) in formData.weekly_data">
                <td :key="'inw_' + index" class="td-input-cell">
                  <input
                    type="number"
                    v-model.number="wd.workbook"
                    @input="limitInput(wd, 'workbook', 0, wd.max_score, $event)"
                    :min="0"
                    :max="wd.max_score"
                    class="score-input wb-score-input"
                  />
                </td>
              </template>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Teacher Comment Section -->
      <div class="comment-section">
        <div class="comment-header">
          Nhận xét của giáo viên
        </div>
        <div class="comment-body">
          <textarea
            v-model="formData.teacher_comment"
            rows="4"
            placeholder="Nhập tối đa 480 ký tự..."
            maxlength="480"
            class="comment-textarea"
          ></textarea>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="action-row">
        <button
          type="button"
          @click="save"
          :disabled="saving"
          class="btn-save-teal"
        >
          {{ saving ? 'Đang lưu...' : 'Lưu kết quả' }}
        </button>
        <button
          type="button"
          @click="cancel"
          class="btn-cancel-slate"
        >
          Bỏ qua
        </button>
        <router-link
          v-if="formData.student_info.total_score > 0"
          :to="`/lms/evaluations/igbh/summative/report/${resultId}`"
          class="btn-report-link"
        >
          Xem báo cáo
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import axios from '@/http/axios.js'

export default {
  name: 'IgbhSummativeEvalForm',
  data() {
    return {
      resultId: null,
      loading: true,
      saving: false,
      formData: null
    }
  },
  created() {
    this.resultId = this.$route.params.id
    this.fetchData()
  },
  methods: {
    fetchData() {
      this.loading = true
      axios.g(`/api/lms/evaluations/summative/results/${this.resultId}/form`).then(res => {
        this.loading = false
        if (res && res.data) {
          const data = res.data

          // Ensure 5 subjective questions exist
          const subjective_data = []
          const existingSubs = data.subjective_data || []
          for (let i = 1; i <= 5; i++) {
            const found = existingSubs.find(s => parseInt(s.sort_no) === i)
            if (found) {
              subjective_data.push({
                sort_no: i,
                concept: found.concept !== null ? parseFloat(found.concept) : null,
                strategy: found.strategy !== null ? parseFloat(found.strategy) : null,
                calculation: found.calculation !== null ? parseFloat(found.calculation) : null,
                expression: found.expression !== null ? parseFloat(found.expression) : null
              })
            } else {
              subjective_data.push({
                sort_no: i,
                concept: null,
                strategy: null,
                calculation: null,
                expression: null
              })
            }
          }
          data.subjective_data = subjective_data

          // Ensure 12 weekly data exist
          if (!data.weekly_data || data.weekly_data.length === 0) {
            data.weekly_data = Array.from({ length: 12 }, (_, i) => ({
              sort_no: i + 1,
              each_cd: 'SE' + String(i + 1).padStart(3, '0'),
              max_score: 3,
              workbook: null
            }))
          }

          if (data.student_info && data.student_info.eval_dt) {
            data.student_info.eval_dt = data.student_info.eval_dt.substring(0, 10)
          } else if (data.student_info) {
            data.student_info.eval_dt = new Date().toISOString().substring(0, 10)
          }

          this.formData = data
        }
      }).catch(err => {
        this.loading = false
        console.error('Error fetching form data', err)
        this.$vs.notify({
          title: 'Lỗi',
          text: 'Không thể tải dữ liệu đánh giá cuối kỳ.',
          color: 'danger'
        })
      })
    },
    limitInput(obj, key, min, max, event) {
      if (obj[key] === '' || obj[key] === null || obj[key] === undefined) return
      let val = parseInt(obj[key], 10)
      if (isNaN(val)) {
        obj[key] = null
        return
      }
      if (val > max) {
        val = max
        obj[key] = max
      } else if (val < min) {
        val = min
        obj[key] = min
      }

      // Auto jump to next number input
      if (event && event.target) {
        const valStr = val.toString()
        const maxStr = max.toString()
        if (valStr.length >= maxStr.length || (val * 10 > max)) {
          this.$nextTick(() => {
            const inputs = Array.from(document.querySelectorAll('.matrix-table input[type="number"]'))
            const index = inputs.indexOf(event.target)
            if (index > -1 && index < inputs.length - 1) {
              inputs[index + 1].focus()
              inputs[index + 1].select()
            }
          })
        }
      }
    },
    save() {
      this.saving = true
      const payload = {
        subjective_data: this.formData.subjective_data,
        weekly_data: this.formData.weekly_data,
        teacher_comment: this.formData.teacher_comment,
        eval_dt: this.formData.student_info.eval_dt
      }

      axios.p(`/api/lms/evaluations/summative/results/${this.resultId}/grade`, payload).then(res => {
        this.saving = false
        if (res && res.data && res.data.status === 'success') {
          this.$vs.notify({
            title: 'Thành công',
            text: 'Đã lưu điểm đánh giá cuối kỳ thành công!',
            color: 'success'
          })
          this.$router.push('/lms/evaluations/igbh/summative')
        } else {
          this.$vs.notify({
            title: 'Lỗi',
            text: 'Có lỗi xảy ra khi lưu.',
            color: 'danger'
          })
        }
      }).catch(err => {
        this.saving = false
        console.error('Error saving form', err)
        this.$vs.notify({
          title: 'Lỗi',
          text: 'Có lỗi xảy ra khi lưu.',
          color: 'danger'
        })
      })
    },
    cancel() {
      this.$router.push('/lms/evaluations/igbh/summative')
    }
  }
}
</script>

<style scoped>
.igbh-summative-form-page {
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  color: #1e293b;
  max-width: 100%;
  margin: 0 auto;
  padding: 10px 10px 40px 10px;
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
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
  text-align: center;
  min-width: 240px;
}

.saving-spinner {
  width: 44px;
  height: 44px;
  border: 4px solid #e0e7ff;
  border-top-color: #1ba494;
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
.header-section {
  margin-bottom: 20px;
}

.back-link {
  display: inline-block;
  color: #6366f1;
  font-size: 14px;
  font-weight: 600;
  text-decoration: none;
  margin-bottom: 6px;
  transition: color 0.15s;
}

.back-link:hover {
  color: #4f46e5;
}

.page-title {
  font-size: 24px;
  font-weight: 800;
  color: #0f172a;
  margin: 0;
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
  border-top-color: #1ba494;
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

/* Form Card */
.form-card {
  background: #ffffff;
  border: 1px solid #d1d5db;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}

/* Top Meta Bar */
.top-meta-bar {
  display: flex;
  flex-wrap: wrap;
  border: 1px solid #d1d5db;
  background: #f9fafb;
  margin-bottom: 24px;
  border-radius: 4px;
}

.meta-item {
  flex: 1;
  min-width: 200px;
  display: flex;
  border-right: 1px solid #d1d5db;
}

.meta-item:last-child {
  border-right: none;
}

.meta-label {
  width: 100px;
  background: #f3f4f6;
  padding: 10px 12px;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  color: #4b5563;
  border-right: 1px solid #d1d5db;
  display: flex;
  align-items: center;
  justify-content: center;
  letter-spacing: 0.03em;
}

.meta-value {
  padding: 10px 14px;
  font-size: 14px;
  font-weight: 600;
  color: #1f2937;
  flex: 1;
  display: flex;
  align-items: center;
}

.date-control {
  width: 100%;
  padding: 6px 10px;
  border: 1px solid #d1d5db;
  border-radius: 4px;
  font-size: 14px;
  background: #ffffff;
  outline: none;
  transition: border-color 0.15s;
}

.date-control:focus {
  border-color: #1ba494;
}

/* Table Wrap */
.table-wrap {
  border: 1px solid #d1d5db;
  border-radius: 4px;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

.matrix-table {
  width: 100%;
  border-collapse: collapse;
  text-align: center;
  font-size: 12px;
  min-width: 1350px;
}

.matrix-table th, .matrix-table td {
  border: 1px solid #d1d5db;
  padding: 8px 4px;
  vertical-align: middle;
}

.tr-bg-header {
  background: #f9fafb;
}

.tr-white {
  background: #ffffff;
}

.th-student-col {
  width: 130px;
  font-weight: 700;
  color: #4b5563;
  font-size: 13px;
  padding: 10px;
}

.th-main-title, .th-section-title {
  font-weight: 700;
  color: #374151;
  font-size: 13px;
  padding: 8px;
}

.td-student-name {
  font-size: 15px;
  font-weight: 800;
  color: #1f2937;
  background: #ffffff;
  padding: 16px 8px;
}

.th-q-num {
  font-weight: 700;
  color: #4b5563;
  font-size: 13px;
  padding: 8px 4px;
}

.th-sub-criterion {
  font-weight: 600;
  color: #6b7280;
  font-size: 11px;
  line-height: 1.25;
  padding: 6px 2px;
  width: 70px;
}

.th-lesson-col {
  font-weight: 700;
  color: #374151;
  font-size: 12px;
  line-height: 1.3;
  padding: 8px 4px;
  width: 65px;
}

.max-pts-text {
  font-weight: 400;
  font-size: 11px;
  color: #6b7280;
}

.td-input-cell {
  padding: 6px 4px;
}

.score-input {
  width: 48px;
  height: 34px;
  text-align: center;
  border: 1px solid #d1d5db;
  border-radius: 4px;
  font-size: 14px;
  font-weight: 700;
  color: #111827;
  background: #ffffff;
  outline: none;
  transition: all 0.15s;
  box-sizing: border-box;
}

.score-input:focus {
  border-color: #1ba494;
  box-shadow: 0 0 0 2px rgba(27, 164, 148, 0.2);
}

.wb-score-input {
  font-weight: 800;
  color: #047857;
  border-color: #a7f3d0;
  background: #f0fdf4;
}

/* Comment Section */
.comment-section {
  margin-top: 24px;
  border: 1px solid #d1d5db;
  border-radius: 4px;
  overflow: hidden;
}

.comment-header {
  background: #f3f4f6;
  padding: 10px 14px;
  border-bottom: 1px solid #d1d5db;
  font-size: 13px;
  font-weight: 700;
  color: #374151;
  text-align: center;
}

.comment-body {
  padding: 12px;
  background: #ffffff;
}

.comment-textarea {
  width: 100%;
  border: 1px solid #d1d5db;
  border-radius: 4px;
  padding: 10px 12px;
  font-size: 14px;
  line-height: 1.5;
  color: #1f2937;
  resize: vertical;
  outline: none;
  transition: border-color 0.15s;
  box-sizing: border-box;
}

.comment-textarea:focus {
  border-color: #1ba494;
}

/* Action Buttons */
.action-row {
  display: flex;
  justify-content: center;
  gap: 16px;
  margin-top: 28px;
}

.btn-save-teal {
  padding: 10px 32px;
  background: #1ba494;
  border: 1px solid #1ba494;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 700;
  color: #ffffff;
  cursor: pointer;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
  transition: all 0.15s;
}

.btn-save-teal:hover {
  background: #158779;
}

.btn-save-teal:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-cancel-slate {
  padding: 10px 32px;
  background: #5c7080;
  border: 1px solid #5c7080;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 600;
  color: #ffffff;
  cursor: pointer;
  transition: all 0.15s;
}

.btn-cancel-slate:hover {
  background: #4a5a68;
}

.btn-report-link {
  display: inline-flex;
  align-items: center;
  padding: 10px 24px;
  background: #ffffff;
  border: 1px solid #10b981;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 700;
  color: #059669;
  text-decoration: none;
  transition: all 0.15s;
}

.btn-report-link:hover {
  background: #ecfdf5;
}

/* Hide spin buttons */
input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
input[type=number] {
  -moz-appearance: textfield;
}
</style>
