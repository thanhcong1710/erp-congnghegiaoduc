<template>
  <div class="student-detail-evaluations mt-3">
    <div class="mb-5 flex flex-wrap justify-between items-center gap-3">
      <h4 class="font-bold text-base text-gray-800 flex items-center">
        <i class="fa-solid fa-award text-primary mr-2"></i>
        Lịch Sử Kiểm Tra & Đánh Giá Năng Lực (UCREA & IG.BH)
      </h4>
      <div class="flex gap-2">
        <router-link to="/lms/evaluations/ucrea">
          <vs-button size="small" color="primary" type="border" class="font-semibold">Vào Quản lý UCREA</vs-button>
        </router-link>
        <router-link to="/lms/evaluations/igbh/diagnostic">
          <vs-button size="small" color="primary" type="border" class="font-semibold">Vào Quản lý IG.BH</vs-button>
        </router-link>
      </div>
    </div>

    <!-- Section 1: UCREA Tests -->
    <div class="eval-section-block mb-5">
      <div class="eval-section-header">
        <span class="eval-section-title text-blue-800">
          <i class="fa-solid fa-circle-check text-primary mr-1.5"></i> 1. Đánh giá năng lực UCREA
        </span>
        <span class="eval-count-badge">{{ ucreaList ? ucreaList.length : 0 }} bài</span>
      </div>
      <div class="bordered-table-container">
        <table class="grid-border-table">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px">STT</th>
              <th>Bài kiểm tra</th>
              <th class="text-center" style="width: 100px">Level</th>
              <th>Lớp</th>
              <th>Giáo viên</th>
              <th class="text-center" style="width: 130px">Ngày thi</th>
              <th class="text-center" style="width: 130px">Trạng thái</th>
              <th class="text-center" style="width: 140px">Báo cáo</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="!ucreaList || ucreaList.length === 0">
              <td colspan="8" class="text-center py-5 text-gray-400 italic">Chưa có bài đánh giá UCREA nào</td>
            </tr>
            <tr v-for="(item, idx) in ucreaList" :key="'uc_' + item.id" class="grid-row-hover">
              <td class="text-center font-bold text-gray-500">{{ idx + 1 }}</td>
              <td class="font-bold text-gray-800">{{ item.test_nm }}</td>
              <td class="text-center"><span class="badge-level">{{ item.level_cd }}</span></td>
              <td class="text-gray-700 font-medium">{{ item.erp_class_name || item.class_nm || '-' }}</td>
              <td class="text-gray-700 font-medium">{{ item.memb_nm || '-' }}</td>
              <td class="text-center text-gray-700 font-semibold">{{ item.eval_dt ? item.eval_dt.substring(0, 10) : '-' }}</td>
              <td class="text-center">
                <span v-if="item.result_cd === 'IS002'" class="badge-status-eval badge-done">Đã chấm</span>
                <span v-else class="badge-status-eval badge-pending">Chưa chấm</span>
              </td>
              <td class="text-center">
                <router-link
                  v-if="item.result_cd === 'IS002'"
                  :to="`/lms/evaluations/ucrea/report/${item.id}`"
                  class="action-link-btn text-primary font-bold hover:underline"
                >
                  <i class="fa-solid fa-chart-pie mr-1"></i> Radar Chart
                </router-link>
                <router-link
                  v-else
                  :to="`/lms/evaluations/ucrea/form/${item.id}`"
                  class="action-link-btn text-amber-600 font-bold hover:underline"
                >
                  <i class="fa-solid fa-pen-nib mr-1"></i> Chấm điểm
                </router-link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Section 2: IG.BH Diagnostic Tests -->
    <div class="eval-section-block mb-5">
      <div class="eval-section-header">
        <span class="eval-section-title text-amber-800">
          <i class="fa-solid fa-award text-amber-600 mr-1.5"></i> 2. Khảo sát đầu vào (IG.BH Diagnostic)
        </span>
        <span class="eval-count-badge">{{ diagnosticList ? diagnosticList.length : 0 }} bài</span>
      </div>
      <div class="bordered-table-container">
        <table class="grid-border-table">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px">STT</th>
              <th>Bài kiểm tra</th>
              <th class="text-center" style="width: 120px">Điểm môn học</th>
              <th class="text-center" style="width: 120px">Điểm tư duy</th>
              <th class="text-center" style="width: 110px">Tổng điểm</th>
              <th class="text-center" style="width: 110px">Level gán</th>
              <th class="text-center" style="width: 130px">Ngày thi</th>
              <th class="text-center" style="width: 150px">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="!diagnosticList || diagnosticList.length === 0">
              <td colspan="8" class="text-center py-5 text-gray-400 italic">Chưa có bài khảo sát đầu vào nào</td>
            </tr>
            <tr v-for="(item, idx) in diagnosticList" :key="'diag_' + item.id" class="grid-row-hover">
              <td class="text-center font-bold text-gray-500">{{ idx + 1 }}</td>
              <td class="font-bold text-gray-800">{{ item.test_nm }}</td>
              <td class="text-center font-bold text-gray-800">{{ item.subject_total || 0 }}</td>
              <td class="text-center font-bold text-gray-800">{{ item.thinking_total || 0 }}</td>
              <td class="text-center font-bold text-primary">{{ item.total_score || 0 }}</td>
              <td class="text-center">
                <span v-if="item.assigned_level" class="badge-level">{{ item.assigned_level }}</span>
                <span v-else class="text-gray-400">-</span>
              </td>
              <td class="text-center text-gray-700 font-semibold">{{ item.eval_dt ? item.eval_dt.substring(0, 10) : '-' }}</td>
              <td class="text-center">
                <div class="flex items-center justify-center gap-2">
                  <router-link
                    :to="`/lms/evaluations/igbh/diagnostic/report/${item.id}`"
                    class="action-link-btn text-primary font-bold hover:underline"
                  >
                    Báo cáo
                  </router-link>
                  <span class="text-gray-400">|</span>
                  <router-link
                    :to="`/lms/evaluations/igbh/diagnostic/form/${item.id}`"
                    class="action-link-btn text-emerald-600 font-bold hover:underline"
                  >
                    Chấm
                  </router-link>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Section 3: IG.BH Summative Final Evaluations -->
    <div class="eval-section-block mb-3">
      <div class="eval-section-header">
        <span class="eval-section-title text-purple-800">
          <i class="fa-solid fa-star text-purple-600 mr-1.5"></i> 3. Đánh giá tổng kết kỳ (IG.BH Summative)
        </span>
        <span class="eval-count-badge">{{ summativeList ? summativeList.length : 0 }} bài</span>
      </div>
      <div class="bordered-table-container">
        <table class="grid-border-table">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px">STT</th>
              <th>Bài kiểm tra</th>
              <th>Lớp</th>
              <th class="text-center" style="width: 110px">Tổng điểm</th>
              <th class="text-center" style="width: 140px">Ngày đánh giá</th>
              <th class="text-center" style="width: 140px">Trạng thái</th>
              <th class="text-center" style="width: 150px">Báo cáo</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="!summativeList || summativeList.length === 0">
              <td colspan="7" class="text-center py-5 text-gray-400 italic">Chưa có kết quả tổng kết kỳ nào</td>
            </tr>
            <tr v-for="(item, idx) in summativeList" :key="'smtv_' + item.id" class="grid-row-hover">
              <td class="text-center font-bold text-gray-500">{{ idx + 1 }}</td>
              <td class="font-bold text-gray-800">{{ item.test_nm }}</td>
              <td class="text-gray-700 font-medium">{{ item.erp_class_name || item.class_nm || '-' }}</td>
              <td class="text-center font-bold text-primary">{{ item.total_score || 0 }}</td>
              <td class="text-center text-gray-700 font-semibold">{{ item.eval_dt ? item.eval_dt.substring(0, 10) : '-' }}</td>
              <td class="text-center">
                <span v-if="item.status === 'completed'" class="badge-status-eval badge-done">Đã hoàn thành</span>
                <span v-else class="badge-status-eval badge-pending">Bản nháp</span>
              </td>
              <td class="text-center">
                <router-link
                  :to="`/lms/evaluations/igbh/summative/report/${item.id}`"
                  class="action-link-btn text-primary font-bold hover:underline"
                >
                  <i class="fa-solid fa-file-waveform mr-1"></i> Báo cáo 12 tuần
                </router-link>
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
  name: 'StudentEvaluations',
  props: {
    student_info: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      ucreaList: [],
      diagnosticList: [],
      summativeList: []
    }
  },
  watch: {
    'student_info.id': {
      immediate: true,
      handler(newVal) {
        if (newVal) {
          this.loadStudentEvaluations()
        }
      }
    }
  },
  methods: {
    loadStudentEvaluations() {
      const studentId = this.student_info.id
      if (!studentId) return

      // 1. Load UCREA
      axios.g('/api/lms/evaluations/ucrea/results', { student_id: studentId, per_page: 50 }).then(res => {
        if (res && res.data && res.data.data) {
          this.ucreaList = res.data.data
        }
      })

      // 2. Load IG.BH Diagnostic
      axios.g('/api/lms/evaluations/igbh/diagnostic/results', { student_id: studentId, per_page: 50 }).then(res => {
        if (res && res.data && res.data.data) {
          this.diagnosticList = res.data.data
        }
      }).catch(() => {
        axios.g('/api/lms/evaluations/igbh/results', { student_id: studentId, per_page: 50 }).then(res => {
          if (res && res.data && res.data.data) {
            this.diagnosticList = res.data.data
          }
        })
      })

      // 3. Load IG.BH Summative
      axios.g('/api/lms/evaluations/summative/results', { student_id: studentId, per_page: 50 }).then(res => {
        if (res && res.data && res.data.data) {
          this.summativeList = res.data.data
        }
      })
    }
  }
}
</script>

<style scoped>
.eval-section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 8px;
}

.eval-section-title {
  font-size: 15px;
  font-weight: 700;
  display: flex;
  align-items: center;
}

.eval-count-badge {
  font-size: 12px;
  font-weight: 700;
  color: #64748b;
  background: #f1f5f9;
  padding: 3px 10px;
  border-radius: 9999px;
  border: 1px solid #cbd5e1;
}

.bordered-table-container {
  width: 100%;
  overflow-x: auto;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  background: #ffffff;
}

.grid-border-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
}

.grid-border-table thead th {
  background: #f1f5f9;
  color: #1e293b;
  font-weight: 700;
  font-size: 13.5px;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  padding: 11px 14px;
  border: 1px solid #cbd5e1;
  white-space: nowrap;
}

.grid-border-table tbody td {
  padding: 12px 14px;
  border: 1px solid #cbd5e1;
  vertical-align: middle;
  font-size: 14px;
  color: #334155;
}

.grid-row-hover:hover td {
  background-color: #f8fafc;
}

.badge-level {
  display: inline-flex;
  align-items: center;
  font-size: 12px;
  font-weight: 700;
  color: #7367f0;
  background-color: #f5f3ff;
  border: 1px solid #ddd6fe;
  padding: 2px 7px;
  border-radius: 4px;
}

.badge-status-eval {
  display: inline-flex;
  align-items: center;
  padding: 3px 10px;
  border-radius: 9999px;
  font-size: 12.5px;
  font-weight: 700;
  white-space: nowrap;
}

.badge-done {
  background: #dcfce7;
  color: #15803d;
  border: 1px solid #bbf7d0;
}

.badge-pending {
  background: #fef9c3;
  color: #a16207;
  border: 1px solid #fef08a;
}

.action-link-btn {
  font-size: 13.5px;
  display: inline-flex;
  align-items: center;
}
</style>
