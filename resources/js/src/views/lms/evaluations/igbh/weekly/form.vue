<template>
  <div class="igbh-weekly-form-page">
    <!-- Saving Overlay -->
    <div v-if="saving" class="saving-overlay">
      <div class="saving-modal">
        <div class="saving-spinner"></div>
        <p class="saving-title">Đang lưu dữ liệu...</p>
        <p class="saving-sub">Vui lòng đợi trong giây lát</p>
      </div>
    </div>

    <!-- Header & Controls -->
    <div class="header-container">
      <div class="header-info">
        <router-link to="/lms/evaluations/igbh/weekly" class="back-link">
          &larr; Danh sách
        </router-link>
        <h1 class="page-title">
          Đánh giá hàng tuần IG.BH - {{ evaluation ? evaluation.each_cd_nm : '' }}
        </h1>
        <p class="page-meta" v-if="evaluation">
          Lớp: <strong class="highlight-indigo">{{ evaluation.class_nm }}</strong>
          <span class="meta-sep">|</span>
          Bài kiểm tra: <strong class="highlight-dark">{{ evaluation.test_nm }}</strong>
        </p>
      </div>

      <!-- Action controls -->
      <div class="header-controls">
        <!-- Week select -->
        <select v-model="selectedWeek" @change="changeWeek" class="form-select week-select">
          <option value="" disabled>Chọn tuần</option>
          <option v-for="w in weeksWithStatus" :key="w.each_cd" :value="w.each_cd">
            {{ w.each_cd_nm }} {{ w.statusText }}
          </option>
        </select>

        <!-- Date select -->
        <input type="date" v-model="selectedDate" class="form-input date-input" />

        <!-- Status select -->
        <select
          v-model="selectedStatus"
          class="form-select status-select"
          :class="selectedStatus === 'Completed' ? 'status-completed' : 'status-draft'"
        >
          <option value="Draft">Đang nhập</option>
          <option value="Completed">Đã hoàn thành</option>
        </select>

        <!-- Add student button -->
        <button
          v-if="missingStudents.length > 0"
          type="button"
          @click="showAddStudentModal = true"
          class="btn-secondary"
        >
          + Thêm học sinh ({{ missingStudents.length }})
        </button>

        <!-- Save button -->
        <button
          type="button"
          @click="saveGrades"
          :disabled="saving"
          class="btn-primary-save"
        >
          {{ saving ? 'Đang lưu...' : 'Lưu Điểm' }}
        </button>
      </div>
    </div>

    <!-- Add Missing Student Modal -->
    <div v-if="showAddStudentModal" class="modal-backdrop">
      <div class="modal-card">
        <div class="modal-header">
          <h3 class="modal-title">Thêm học sinh vào đánh giá</h3>
          <button type="button" @click="showAddStudentModal = false" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
          <p v-if="missingStudents.length === 0" class="no-data-text">Không có học sinh nào trống.</p>
          <ul class="student-list" v-else>
            <li v-for="student in missingStudents" :key="student.student_id || student.stu_seq" class="student-list-item">
              <span class="item-name">{{ student.stu_nm }}</span>
              <button type="button" @click="addMissingStudent(student)" class="btn-add-item">
                + Thêm
              </button>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="loading-container">
      <div class="spinner"></div>
      <p class="loading-text">Đang tải thông tin đánh giá tuần...</p>
    </div>

    <!-- Table Card -->
    <div v-else class="table-card">
      <div class="table-responsive">
        <table class="weekly-table">
          <thead>
            <tr class="thead-row-1">
              <th rowspan="2" class="th-student">Học sinh Tên</th>
              <th rowspan="2" class="th-workbook">Sách bài tập<br>(1~20)</th>
              <th colspan="4" class="th-group th-attitude">Thái độ học tập Đánh giá</th>
              <th colspan="4" class="th-group th-skills">Đánh giá theo lĩnh vực</th>
            </tr>
            <tr class="thead-row-2">
              <th class="th-sub">Khả năng lắng nghe<br><span class="scale-text">(0~5 Điểm)</span></th>
              <th class="th-sub">Tham gia bài học<br><span class="scale-text">(0~5 Điểm)</span></th>
              <th class="th-sub">Khả năng thể hiện<br><span class="scale-text">(0~5 Điểm)</span></th>
              <th class="th-sub">Sự hợp tác<br><span class="scale-text">(0~5 Điểm)</span></th>
              <th class="th-sub">Kỹ năng cơ bản<br><span class="scale-text">(0~5 Điểm)</span></th>
              <th class="th-sub">Khả năng lãnh đạo<br><span class="scale-text">(0~5 Điểm)</span></th>
              <th class="th-sub">Khả năng toán học<br><span class="scale-text">(0~5 Điểm)</span></th>
              <th class="th-sub">Tính sáng tạo<br><span class="scale-text">(0~5 Điểm)</span></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="std in students" :key="std.student_id || std.stu_seq" class="tbody-row">
              <td class="td-student">
                {{ std.stu_nm }}
              </td>
              <td class="td-input td-wb">
                <input
                  type="number"
                  min="0"
                  max="20"
                  v-model.number="std.workbook"
                  @input="limitInput(std, 'workbook', 0, 20)"
                  class="num-input wb-input"
                />
              </td>
              <td class="td-input">
                <input
                  type="number"
                  min="0"
                  max="5"
                  v-model.number="std.attd_listen"
                  @input="limitInput(std, 'attd_listen', 0, 5)"
                  class="num-input"
                />
              </td>
              <td class="td-input">
                <input
                  type="number"
                  min="0"
                  max="5"
                  v-model.number="std.attd_join"
                  @input="limitInput(std, 'attd_join', 0, 5)"
                  class="num-input"
                />
              </td>
              <td class="td-input">
                <input
                  type="number"
                  min="0"
                  max="5"
                  v-model.number="std.attd_express"
                  @input="limitInput(std, 'attd_express', 0, 5)"
                  class="num-input"
                />
              </td>
              <td class="td-input">
                <input
                  type="number"
                  min="0"
                  max="5"
                  v-model.number="std.attd_coop"
                  @input="limitInput(std, 'attd_coop', 0, 5)"
                  class="num-input"
                />
              </td>
              <td class="td-input">
                <input
                  type="number"
                  min="0"
                  max="5"
                  v-model.number="std.detect_normal"
                  @input="limitInput(std, 'detect_normal', 0, 5)"
                  class="num-input"
                />
              </td>
              <td class="td-input">
                <input
                  type="number"
                  min="0"
                  max="5"
                  v-model.number="std.detect_leadersh"
                  @input="limitInput(std, 'detect_leadersh', 0, 5)"
                  class="num-input"
                />
              </td>
              <td class="td-input">
                <input
                  type="number"
                  min="0"
                  max="5"
                  v-model.number="std.detect_math"
                  @input="limitInput(std, 'detect_math', 0, 5)"
                  class="num-input"
                />
              </td>
              <td class="td-input">
                <input
                  type="number"
                  min="0"
                  max="5"
                  v-model.number="std.detect_creative"
                  @input="limitInput(std, 'detect_creative', 0, 5)"
                  class="num-input"
                />
              </td>
            </tr>
            <tr v-if="students.length === 0">
              <td colspan="10" class="no-students-cell">
                Lớp này chưa có học sinh nào.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import axios from '@/http/axios.js'

export default {
  name: 'IgbhWeeklyEvalForm',
  data() {
    return {
      evaluation: null,
      students: [],
      loading: true,
      saving: false,
      weeks: [],
      selectedWeek: '',
      selectedDate: '',
      selectedStatus: 'Draft',
      existingWeeks: [],
      allStudents: [],
      showAddStudentModal: false
    }
  },
  computed: {
    missingStudents() {
      if (!this.allStudents || this.allStudents.length === 0) return []
      const currentIds = this.students.map(s => s.student_id || s.stu_seq)
      return this.allStudents.filter(s => !currentIds.includes(s.student_id || s.stu_seq))
    },
    weeksWithStatus() {
      return this.weeks.map(w => {
        const existing = this.existingWeeks.find(ex => ex.each_cd === w.each_cd)
        let statusText = ''
        if (existing) {
          if (existing.status === 'Completed') {
            statusText = '- (Đã hoàn thành)'
          } else {
            statusText = '- (Đang nhập)'
          }
        }
        return {
          ...w,
          statusText
        }
      })
    }
  },
  async created() {
    await this.fetchInitData()
    await this.fetchDetails()
  },
  methods: {
    limitInput(obj, field, min, max) {
      if (obj[field] !== null && obj[field] !== '') {
        let val = parseInt(obj[field], 10)
        if (isNaN(val)) {
          obj[field] = null
          return
        }
        if (val < min) val = min
        if (val > max) val = max
        obj[field] = val
      }
    },
    async fetchInitData() {
      try {
        const response = await axios.g('/api/lms/evaluations/weekly/init-data')
        if (response && response.data) {
          this.weeks = response.data.weeks || []
        }
      } catch (error) {
        console.error('Error fetching weekly init data', error)
      }
    },
    async fetchDetails() {
      this.loading = true
      try {
        const id = this.$route.params.id
        const response = await axios.g(`/api/lms/evaluations/weekly/results/${id}`)
        if (response && response.data) {
          this.evaluation = response.data.evaluation
          this.students = response.data.students || []
          this.allStudents = response.data.all_students || []
          if (this.evaluation) {
            this.selectedWeek = this.evaluation.each_cd
            this.selectedDate = this.evaluation.eval_ymd
              ? this.evaluation.eval_ymd.substring(0, 10)
              : new Date().toISOString().substring(0, 10)
            this.selectedStatus = this.evaluation.status || 'Draft'
            await this.fetchExistingWeeks()
          }
        }
      } catch (error) {
        console.error('Error fetching weekly details', error)
        this.$vs.notify({
          title: 'Lỗi',
          text: 'Lỗi khi tải thông tin đánh giá tuần.',
          color: 'danger'
        })
      } finally {
        this.loading = false
      }
    },
    async fetchExistingWeeks() {
      if (!this.evaluation) return
      try {
        const response = await axios.p('/api/lms/evaluations/weekly/existing-weeks', {
          test_seq: this.evaluation.test_seq,
          class_seq: this.evaluation.class_id || this.evaluation.class_seq
        })
        this.existingWeeks = (response && response.data) ? response.data : []
      } catch (error) {
        console.error('Error fetching existing weeks', error)
      }
    },
    addMissingStudent(student) {
      this.students.push({
        student_id: student.student_id,
        stu_seq: student.stu_seq || student.student_id,
        stu_nm: student.stu_nm,
        workbook: 0,
        attd_listen: 5,
        attd_join: 5,
        attd_express: 5,
        attd_coop: 5,
        detect_normal: 5,
        detect_leadersh: 5,
        detect_math: 5,
        detect_creative: 5
      })
      this.showAddStudentModal = false
    },
    async changeWeek() {
      if (!this.evaluation) return
      this.loading = true
      try {
        const response = await axios.p('/api/lms/evaluations/weekly/results', {
          test_seq: this.evaluation.test_seq,
          class_seq: this.evaluation.class_id || this.evaluation.class_seq,
          each_cd: this.selectedWeek,
          eval_ymd: this.selectedDate
        })

        if (response && response.data && response.data.id) {
          const newId = response.data.id
          if (newId != this.$route.params.id) {
            this.$router.replace(`/lms/evaluations/igbh/weekly/form/${newId}`)
            setTimeout(() => {
              this.fetchDetails()
            }, 100)
          } else {
            this.fetchDetails()
          }
        }
      } catch (error) {
        console.error('Error changing week', error)
        this.loading = false
        this.$vs.notify({
          title: 'Lỗi',
          text: 'Không thể chuyển tuần. Vui lòng thử lại.',
          color: 'danger'
        })
      }
    },
    async saveGrades() {
      this.saving = true
      try {
        const id = this.$route.params.id

        // Validate
        for (const s of this.students) {
          if (s.workbook < 0 || s.workbook > 20) {
            this.$vs.notify({ title: 'Cảnh báo', text: 'Điểm Sách BT phải từ 0-20.', color: 'warning' })
            this.saving = false
            return
          }
        }

        const payload = {
          students: this.students,
          eval_ymd: this.selectedDate,
          status: this.selectedStatus
        }

        const response = await axios.p(`/api/lms/evaluations/weekly/results/${id}/grade`, payload)
        if (response) {
          this.$vs.notify({
            title: 'Thành công',
            text: 'Đã lưu điểm đánh giá tuần thành công!',
            color: 'success'
          })
          this.$router.push('/lms/evaluations/igbh/weekly')
        }
      } catch (error) {
        console.error('Error saving weekly grades', error)
        this.$vs.notify({
          title: 'Lỗi',
          text: 'Có lỗi xảy ra khi lưu điểm.',
          color: 'danger'
        })
      } finally {
        this.saving = false
      }
    }
  }
}
</script>

<style scoped>
.igbh-weekly-form-page {
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  color: #1e293b;
  max-width: 1400px;
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
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
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

/* Header & Controls */
.header-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 16px;
  margin-bottom: 24px;
}

.back-link {
  display: inline-block;
  color: #6366f1;
  font-size: 14px;
  font-weight: 600;
  text-decoration: none;
  margin-bottom: 4px;
  transition: color 0.15s;
}

.back-link:hover {
  color: #4f46e5;
}

.page-title {
  font-size: 24px;
  font-weight: 800;
  color: #0f172a;
  margin: 0 0 6px 0;
}

.page-meta {
  font-size: 14px;
  color: #64748b;
  margin: 0;
}

.highlight-indigo {
  color: #4f46e5;
  font-weight: 700;
}

.highlight-dark {
  color: #1e293b;
  font-weight: 700;
}

.meta-sep {
  color: #cbd5e1;
  margin: 0 6px;
}

.header-controls {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 10px;
}

.form-select, .form-input {
  height: 40px;
  padding: 0 12px;
  border: 1px solid #cbd5e1;
  border-radius: 10px;
  font-size: 14px;
  color: #1e293b;
  background: #ffffff;
  outline: none;
  transition: border-color 0.15s;
}

.form-select:focus, .form-input:focus {
  border-color: #6366f1;
}

.week-select {
  min-width: 170px;
}

.date-input {
  width: 145px;
}

.status-select {
  font-weight: 700;
}

.status-draft {
  background-color: #fffbeb;
  color: #b45309;
  border-color: #fcd34d;
}

.status-completed {
  background-color: #ecfdf5;
  color: #047857;
  border-color: #6ee7b7;
}

.btn-secondary {
  height: 40px;
  padding: 0 16px;
  background: #f1f5f9;
  border: 1px solid #cbd5e1;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 700;
  color: #334155;
  cursor: pointer;
  transition: all 0.15s;
}

.btn-secondary:hover {
  background: #e2e8f0;
}

.btn-primary-save {
  height: 40px;
  padding: 0 24px;
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

.btn-primary-save:hover {
  background: #4338ca;
  box-shadow: 0 6px 14px rgba(79, 70, 229, 0.35);
}

.btn-primary-save:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Modal */
.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.5);
  backdrop-filter: blur(4px);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
}

.modal-card {
  background: #ffffff;
  border-radius: 14px;
  width: 100%;
  max-width: 440px;
  overflow: hidden;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
  padding: 16px 20px;
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.modal-title {
  font-size: 16px;
  font-weight: 700;
  color: #1e293b;
  margin: 0;
}

.modal-close {
  background: none;
  border: none;
  font-size: 24px;
  line-height: 1;
  color: #94a3b8;
  cursor: pointer;
}

.modal-body {
  padding: 20px;
  max-height: 380px;
  overflow-y: auto;
}

.student-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.student-list-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 14px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
}

.item-name {
  font-weight: 600;
  color: #1e293b;
}

.btn-add-item {
  padding: 6px 12px;
  background: #e0e7ff;
  color: #4338ca;
  font-size: 13px;
  font-weight: 700;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.15s;
}

.btn-add-item:hover {
  background: #c7d2fe;
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

/* Table Card */
.table-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

.table-responsive {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

.weekly-table {
  width: 100%;
  border-collapse: collapse;
  min-width: 1100px;
}

.weekly-table th {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  padding: 10px 6px;
  font-size: 13px;
  font-weight: 700;
  color: #475569;
  text-align: center;
}

.th-student {
  width: 140px;
}

.th-workbook {
  width: 95px;
}

.th-sub {
  width: 100px;
  line-height: 1.3;
}

.scale-text {
  font-size: 13px;
  font-weight: 600;
  color: #64748b;
}

.tbody-row {
  border-bottom: 1px solid #e2e8f0;
  transition: background 0.15s;
}

.tbody-row:hover {
  background: #f8fafc;
}

.td-student {
  padding: 10px 14px;
  font-weight: 700;
  color: #4f46e5;
  border: 1px solid #e2e8f0;
  text-align: center;
  background: #ffffff;
}

.td-input {
  padding: 6px 4px;
  border: 1px solid #e2e8f0;
  text-align: center;
}

.td-wb {
  background: #f8fafc;
}

.num-input {
  width: 58px;
  height: 34px;
  text-align: center;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 600;
  color: #1e293b;
  background: #ffffff;
  outline: none;
  transition: all 0.15s;
  box-sizing: border-box;
}

.num-input:focus {
  border-color: #6366f1;
  box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.15);
}

.wb-input {
  width: 64px;
  font-weight: 800;
  color: #047857;
  border-color: #a7f3d0;
  background: #f0fdf4;
}

.no-students-cell {
  padding: 30px;
  text-align: center;
  color: #94a3b8;
  font-size: 14px;
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
