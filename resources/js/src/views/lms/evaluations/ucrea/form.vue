<template>
  <div class="ucrea-form-page">
    <!-- Saving Overlay -->
    <div v-if="submitting" class="saving-overlay">
      <div class="saving-modal">
        <div class="saving-spinner"></div>
        <p class="saving-title">Đang lưu dữ liệu...</p>
        <p class="saving-sub">Vui lòng đợi trong giây lát</p>
      </div>
    </div>

    <!-- Top Header -->
    <div class="header-section">
      <div class="header-left">
        <h1 class="page-title">Đánh Giá Năng Lực UCREA</h1>
        <p class="page-subtitle">Nhập điểm đánh giá chi tiết cho học sinh theo từng tiêu chí</p>
      </div>
      <div class="header-right">
        <router-link to="/lms/evaluations/ucrea" class="btn-back">
          Quay lại danh sách
        </router-link>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="loading-container">
      <div class="spinner"></div>
      <p class="loading-text">Đang tải dữ liệu đánh giá...</p>
    </div>

    <div v-else-if="result" class="form-content">
      <!-- General Info Card -->
      <div class="info-card">
        <div class="info-fields">
          <div class="info-item">
            <span class="info-label">Học sinh</span>
            <span class="info-value student-name">{{ result.stu_nm }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Bài đánh giá</span>
            <span class="info-value">{{ result.test_nm }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Cấp độ</span>
            <span class="info-value level-tag">{{ result.level_cd_nm || result.level_cd }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Lớp</span>
            <span class="info-value">{{ result.class_nm || '-' }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Giáo viên đánh giá</span>
            <span class="info-value">{{ result.memb_nm || '-' }}</span>
          </div>
        </div>

        <div class="info-action">
          <button
            @click="submitGrades"
            :disabled="submitting"
            type="button"
            class="btn-submit-top"
          >
            {{ submitting ? 'Đang lưu...' : 'Lưu Kết Quả' }}
          </button>
        </div>
      </div>

      <!-- Evaluation Table Card -->
      <div class="table-card">
        <div class="table-card-header">
          <h2 class="table-card-title">Bảng Nhập Điểm</h2>
          <p class="table-card-subtitle">Chọn điểm số thực tế của học sinh đạt được cho mỗi tiêu chí dưới đây.</p>
        </div>

        <div class="table-responsive">
          <table class="rubric-table">
            <thead>
              <tr>
                <th class="th-no">Vấn đề số</th>
                <th class="th-main">Lĩnh vực kiểm tra</th>
                <th class="th-sub">Lĩnh vực chi tiết</th>
                <th class="th-time">Thời gian</th>
                <th class="th-scores" colspan="5">Điểm thực tế</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in rubrics" :key="index" class="rubric-row">
                <!-- Question Number -->
                <td class="td-no">
                  {{ getQuestionNumber(index) }}
                </td>

                <!-- Main Skill -->
                <td class="td-main">
                  {{ item.main }}
                </td>

                <!-- Sub Skill -->
                <td class="td-sub">
                  <div class="sub-name">{{ item.sub }}</div>
                  <div class="detail-name">{{ item.name }}</div>
                </td>

                <!-- Time limit -->
                <td class="td-time">
                  {{ item.key.startsWith('sk3') ? '120' : (item.key === 'sk13' ? '0' : '60') }} giây
                </td>

                <!-- 5 Option Columns -->
                <td
                  v-for="(opt, optIdx) in item.options"
                  :key="optIdx"
                  class="td-opt"
                  :class="{ 'opt-selected': item.score === opt }"
                  @click="item.score = opt"
                >
                  <label class="choice-label" @click.stop>
                    <input
                      type="radio"
                      :name="'rubric_' + index"
                      :value="opt"
                      v-model="item.score"
                      class="choice-radio-hidden"
                    />
                    <div class="custom-radio-dot" :class="{ 'is-active': item.score === opt }">
                      <div class="radio-inner-circle"></div>
                    </div>
                    <span class="choice-text" :class="{ 'text-active': item.score === opt }">
                      {{ opt }}
                    </span>
                  </label>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Bottom Action Bar -->
      <div class="bottom-action-bar">
        <router-link to="/lms/evaluations/ucrea" class="btn-cancel">
          Quay lại danh sách
        </router-link>
        <div class="bottom-right-btns">
          <router-link
            v-if="hasResultData"
            :to="`/lms/evaluations/ucrea/report/${result.id}`"
            class="btn-view-report"
          >
            Xem Báo Cáo
          </router-link>
          <button
            @click="submitGrades"
            :disabled="submitting"
            type="button"
            class="btn-submit-bottom"
          >
            {{ submitting ? 'Đang lưu...' : 'Hoàn tất & Lưu Kết Quả' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from '@/http/axios.js'

export default {
  name: 'UcreaEvalForm',
  data() {
    return {
      result: null,
      loading: true,
      submitting: false,
      hasResultData: false,
      rubrics: [
        { main: 'Khả năng suy nghĩ', sub: 'Kỹ năng tư duy cơ bản', name: 'Chú ý', skillName: 'Năng lực chú ý', key: 'sk11', options: ['4', '3', '2', '1', '0'], score: null },
        { main: 'Khả năng suy nghĩ', sub: 'Kỹ năng tư duy cơ bản', name: 'Quan sát', skillName: 'Năng lực quan sát', key: 'sk12', options: ['9-10', '7-8', '5-6', '3-4', '0-2'], score: null },
        { main: 'Khả năng suy nghĩ', sub: 'Kỹ năng tư duy cơ bản', name: 'Ghi nhớ', skillName: 'Năng lực ghi nhớ', key: 'sk13', options: ['5-6', '4', '2-3', '1', '0'], score: null },
        { main: 'Khả năng suy nghĩ', sub: 'Kỹ năng tư duy logic', name: 'Hiểu', skillName: 'Hiểu biết', key: 'sk21', options: ['6', '4-5', '2-3', '1', '0'], score: null },
        { main: 'Khả năng suy nghĩ', sub: 'Kỹ năng tư duy logic', name: 'Áp dụng', skillName: 'Ứng dụng', key: 'sk22', options: ['4', '3', '2', '1', '0'], score: null },
        { main: 'Khả năng suy nghĩ', sub: 'Kỹ năng tư duy logic', name: 'Phân tích', skillName: 'Phân tích', key: 'sk23', options: ['4', '3', '2', '1', '0'], score: null },
        { main: 'Khả năng suy nghĩ', sub: 'Kỹ năng tư duy logic', name: 'Tổng hợp', skillName: 'Tổng hợp', key: 'sk24', options: ['4', '3', '2', '1', '0'], score: null },
        { main: 'Kiến thức tích lũy', sub: 'Kỹ năng tư duy toán học', name: 'Con số và hoạt động', skillName: 'Số và tính toán', key: 'kn11', options: ['4', '3', '2', '1', '0'], score: null },
        { main: 'Kiến thức tích lũy', sub: 'Kỹ năng tư duy toán học', name: 'Hình học', skillName: 'Hình học không gian', key: 'kn12', options: ['4', '3', '2', '1', '0'], score: null },
        { main: 'Kiến thức tích lũy', sub: 'Kỹ năng tư duy toán học', name: 'Đo lường', skillName: 'Đo lường', key: 'kn13', options: ['4', '3', '2', '1', '0'], score: null },
        { main: 'Kiến thức tích lũy', sub: 'Kỹ năng tư duy toán học', name: 'Kiểu Mẫu', skillName: 'Kiểu mẫu', key: 'kn14', options: ['4', '3', '2', '1', '0'], score: null },
        { main: 'Kiến thức tích lũy', sub: 'Kỹ năng tư duy toán học', name: 'Dữ liệu', skillName: 'Dữ liệu', key: 'kn15', options: ['order O, 3', 'order O, 2', 'order O, 0-1', 'order X, 2-3', 'order X, 0-1'], score: null },
        { main: 'Khả năng suy nghĩ', sub: 'Kỹ năng tư duy sáng tạo', name: 'Lưu loát', skillName: 'Sự trôi chảy', key: 'sk31', options: ['6', '4-5', '2-3', '1', '0'], score: null },
        { main: 'Khả năng suy nghĩ', sub: 'Kỹ năng tư duy sáng tạo', name: 'Tính linh hoạt', skillName: 'Tính linh hoạt', key: 'sk32', options: ['8', '6-7', '3-5', '1-2', '0'], score: null },
        { main: 'Khả năng suy nghĩ', sub: 'Kỹ năng tư duy sáng tạo', name: 'Độc đáo', skillName: 'Tính độc đáo', key: 'sk33', options: ['4', '3', '2', '1', '0'], score: null },
        { main: 'Khả năng suy nghĩ', sub: 'Kỹ năng tư duy sáng tạo', name: 'Xây dựng', skillName: 'Tính chính xác', key: 'sk34', options: ['4', '3', '2', '1', '0'], score: null }
      ]
    }
  },
  created() {
    this.fetchInfo()
  },
  methods: {
    getQuestionNumber(index) {
      if (!this.result) return index + 1
      const isL1L2 = ['L1', 'L2'].includes(this.result.level_cd)
      if (isL1L2) {
        if (index >= 10) return '11'
        return index + 1
      } else {
        if (index >= 12) return '13,14'
        return index + 1
      }
    },
    fetchInfo() {
      this.loading = true
      const id = this.$route.params.id
      axios.g(`/api/lms/evaluations/ucrea/results/${id}`).then(res => {
        this.loading = false
        if (res && res.data && res.data.status === 'success') {
          this.result = res.data.data.general
          if (this.result && this.result.report_data) {
            this.hasResultData = true
          }

          if (['L1', 'L2'].includes(this.result.level_cd)) {
            this.rubrics = this.rubrics.filter((r, idx) => idx !== 6 && idx !== 11)
          }

          const savedRubrics = res.data.data.rubrics || []
          if (savedRubrics.length > 0) {
            this.rubrics.forEach(rub => {
              const found = savedRubrics.find(sr => sr.rubric_name === rub.name)
              if (found) {
                rub.score = found.assigned_score
              }
            })
          }
        }
      }).catch(err => {
        this.loading = false
        console.error('Error fetching UCREA info:', err)
      })
    },
    submitGrades() {
      const unanswered = this.rubrics.filter(r => !r.score)
      if (unanswered.length > 0) {
        this.$vs.notify({
          title: 'Chưa hoàn thành',
          text: `Vui lòng hoàn thành đánh giá cho tất cả ${this.rubrics.length} tiêu chí!`,
          color: 'warning'
        })
        return
      }

      this.submitting = true
      const id = this.$route.params.id
      const payload = {
        rubrics: this.rubrics
      }

      axios.p(`/api/lms/evaluations/ucrea/results/${id}/grade`, payload).then(res => {
        this.submitting = false
        if (res && res.data && res.data.status === 'success') {
          this.hasResultData = true
          this.$vs.notify({
            title: 'Thành công',
            text: 'Đã cập nhật điểm đánh giá UCREA thành công!',
            color: 'success'
          })
          this.$router.push(`/lms/evaluations/ucrea/report/${id}`)
        } else {
          this.$vs.notify({
            title: 'Lỗi',
            text: 'Có lỗi xảy ra khi lưu điểm.',
            color: 'danger'
          })
        }
      }).catch(err => {
        this.submitting = false
        console.error('Submit grade error:', err)
        this.$vs.notify({
          title: 'Lỗi',
          text: 'Có lỗi xảy ra khi lưu điểm. Vui lòng thử lại.',
          color: 'danger'
        })
      })
    }
  }
}
</script>

<style scoped>
.ucrea-form-page {
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

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Top Header */
.header-section {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 24px;
  flex-wrap: wrap;
  gap: 16px;
}

.page-title {
  font-size: 24px;
  font-weight: 800;
  color: #0f172a;
  margin: 0 0 4px 0;
}

.page-subtitle {
  font-size: 14px;
  color: #64748b;
  margin: 0;
}

.btn-back {
  display: inline-block;
  padding: 8px 18px;
  background: #ffffff;
  border: 1px solid #cbd5e1;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 600;
  color: #475569;
  text-decoration: none;
  transition: all 0.15s;
}

.btn-back:hover {
  background: #f1f5f9;
  color: #1e293b;
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

/* Info Card */
.info-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 22px 28px;
  margin-bottom: 24px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 20px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

.info-fields {
  display: flex;
  flex-wrap: wrap;
  gap: 24px;
}

.info-item {
  display: flex;
  flex-direction: column;
}

.info-label {
  font-size: 13px;
  font-weight: 700;
  text-transform: uppercase;
  color: #64748b;
  letter-spacing: 0.05em;
  margin-bottom: 4px;
}

.info-value {
  font-size: 15px;
  font-weight: 600;
  color: #1e293b;
}

.student-name {
  color: #4f46e5;
  font-weight: 800;
}

.level-tag {
  color: #0284c7;
}

.btn-submit-top {
  padding: 10px 24px;
  background: #4f46e5;
  border: 1px solid #4f46e5;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 700;
  color: #ffffff;
  cursor: pointer;
  box-shadow: 0 4px 10px rgba(79, 70, 229, 0.25);
  transition: all 0.15s;
}

.btn-submit-top:hover {
  background: #4338ca;
  box-shadow: 0 6px 14px rgba(79, 70, 229, 0.35);
}

.btn-submit-top:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Table Card */
.table-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
  margin-bottom: 24px;
}

.table-card-header {
  padding: 18px 24px;
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
}

.table-card-title {
  font-size: 16px;
  font-weight: 700;
  color: #1e293b;
  margin: 0;
}

.table-card-subtitle {
  font-size: 13px;
  color: #64748b;
  margin: 4px 0 0 0;
}

.table-responsive {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

.rubric-table {
  width: 100%;
  border-collapse: collapse;
  min-width: 820px;
}

.rubric-table th {
  padding: 12px 16px;
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
  font-size: 13px;
  font-weight: 700;
  color: #475569;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  text-align: left;
}

.th-no {
  width: 90px;
  text-align: center !important;
}

.th-main {
  width: 170px;
}

.th-sub {
  width: 200px;
}

.th-time {
  width: 100px;
  text-align: center !important;
}

.th-scores {
  text-align: center !important;
}

.rubric-row {
  border-bottom: 1px solid #f1f5f9;
  transition: background-color 0.15s;
}

.rubric-row:hover {
  background-color: #f8fafc;
}

.td-no {
  padding: 14px 16px;
  text-align: center;
  font-weight: 800;
  color: #64748b;
  font-size: 14px;
}

.td-main {
  padding: 14px 16px;
  font-weight: 700;
  color: #1e293b;
  font-size: 14px;
}

.td-sub {
  padding: 14px 16px;
}

.sub-name {
  font-size: 14px;
  font-weight: 600;
  color: #1e293b;
}

.detail-name {
  font-size: 13px;
  font-weight: 700;
  color: #4f46e5;
  margin-top: 2px;
}

.td-time {
  padding: 14px 16px;
  text-align: center;
  font-size: 13px;
  color: #64748b;
  white-space: nowrap;
}

/* Option Columns */
.td-opt {
  padding: 8px 10px;
  text-align: center;
  width: 95px;
  border-left: 1px solid #f1f5f9;
  cursor: pointer;
  transition: background-color 0.15s;
  user-select: none;
}

.td-opt:hover {
  background-color: #f0f4ff;
}

.opt-selected {
  background-color: #eef2ff !important;
}

.choice-label {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  width: 100%;
}

.choice-radio-hidden {
  position: absolute;
  opacity: 0;
  pointer-events: none;
}

.custom-radio-dot {
  width: 20px;
  height: 20px;
  border: 2px solid #cbd5e1;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.15s;
  background: #ffffff;
}

.custom-radio-dot.is-active {
  border-color: #4f46e5;
  background: #4f46e5;
}

.radio-inner-circle {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #ffffff;
  transform: scale(0);
  transition: transform 0.15s cubic-bezier(0.4, 0, 0.2, 1);
}

.custom-radio-dot.is-active .radio-inner-circle {
  transform: scale(1);
}

.choice-text {
  font-size: 13px;
  font-weight: 600;
  color: #64748b;
  margin-top: 5px;
  transition: color 0.15s;
  white-space: nowrap;
}

.choice-text.text-active {
  color: #4f46e5;
  font-weight: 800;
}

/* Bottom Action Bar */
.bottom-action-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-top: 16px;
  border-top: 1px solid #e2e8f0;
}

.btn-cancel {
  padding: 10px 22px;
  background: #ffffff;
  border: 1px solid #cbd5e1;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 600;
  color: #475569;
  text-decoration: none;
  transition: all 0.15s;
}

.btn-cancel:hover {
  background: #f1f5f9;
  color: #1e293b;
}

.bottom-right-btns {
  display: flex;
  gap: 12px;
}

.btn-view-report {
  display: inline-block;
  padding: 10px 22px;
  background: #ffffff;
  border: 1px solid #10b981;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 700;
  color: #059669;
  text-decoration: none;
  transition: all 0.15s;
}

.btn-view-report:hover {
  background: #ecfdf5;
}

.btn-submit-bottom {
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

.btn-submit-bottom:hover {
  background: #4338ca;
  box-shadow: 0 6px 14px rgba(79, 70, 229, 0.4);
}

.btn-submit-bottom:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>
