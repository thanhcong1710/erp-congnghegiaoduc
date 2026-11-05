<template>
  <div class="student-detail-evaluations mt-5">
    <div class="mb-6 flex justify-between items-center">
      <h4 class="font-bold text-lg text-gray-800">Lịch Sử Kiểm Tra & Đánh Giá Năng Lực (UCREA & IG.BH)</h4>
      <div class="flex gap-2">
        <router-link to="/lms/evaluations/ucrea">
          <vs-button size="small" color="primary" type="border">Vào Quản lý UCREA</vs-button>
        </router-link>
        <router-link to="/lms/evaluations/igbh/diagnostic">
          <vs-button size="small" color="primary" type="border">Vào Quản lý IG.BH</vs-button>
        </router-link>
      </div>
    </div>

    <!-- Section 1: UCREA Tests -->
    <div class="border rounded-lg p-4 mb-6 bg-white shadow-sm">
      <h5 class="font-bold text-base text-blue-900 mb-3 flex items-center">
        <i class="feather icon-check-circle mr-2 text-primary"></i> 1. Đánh giá năng lực UCREA
      </h5>
      <table class="w-full text-sm border-collapse border border-gray-200">
        <thead>
          <tr class="bg-gray-100 text-xs font-bold text-gray-700">
            <th class="border p-2 text-center w-12">STT</th>
            <th class="border p-2">Bài kiểm tra</th>
            <th class="border p-2 text-center">Level</th>
            <th class="border p-2">Lớp</th>
            <th class="border p-2">Giáo viên</th>
            <th class="border p-2 text-center">Ngày thi</th>
            <th class="border p-2 text-center">Trạng thái</th>
            <th class="border p-2 text-center w-28">Báo cáo</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="ucreaList.length === 0">
            <td colspan="8" class="text-center py-4 text-gray-500">Chưa có bài đánh giá UCREA nào</td>
          </tr>
          <tr v-for="(item, idx) in ucreaList" :key="'uc_' + item.id">
            <td class="border p-2 text-center">{{ idx + 1 }}</td>
            <td class="border p-2 font-semibold">{{ item.test_nm }}</td>
            <td class="border p-2 text-center"><vs-chip color="primary">{{ item.level_cd }}</vs-chip></td>
            <td class="border p-2">{{ item.erp_class_name || item.class_nm || '-' }}</td>
            <td class="border p-2">{{ item.memb_nm || '-' }}</td>
            <td class="border p-2 text-center">{{ item.eval_dt ? item.eval_dt.substring(0, 10) : '-' }}</td>
            <td class="border p-2 text-center">
              <vs-chip v-if="item.result_cd === 'IS002'" color="success">Đã chấm</vs-chip>
              <vs-chip v-else color="warning">Chưa chấm</vs-chip>
            </td>
            <td class="border p-2 text-center">
              <router-link
                v-if="item.result_cd === 'IS002'"
                :to="`/lms/evaluations/ucrea/report/${item.id}`"
                class="text-primary font-bold hover:underline"
              >
                Xem Radar Chart
              </router-link>
              <router-link
                v-else
                :to="`/lms/evaluations/ucrea/form/${item.id}`"
                class="text-warning font-bold hover:underline"
              >
                Chấm điểm
              </router-link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Section 2: IG.BH Diagnostic Tests -->
    <div class="border rounded-lg p-4 mb-6 bg-white shadow-sm">
      <h5 class="font-bold text-base text-yellow-800 mb-3 flex items-center">
        <i class="feather icon-award mr-2 text-yellow-600"></i> 2. Khảo sát đầu vào (IG.BH Diagnostic)
      </h5>
      <table class="w-full text-sm border-collapse border border-gray-200">
        <thead>
          <tr class="bg-gray-100 text-xs font-bold text-gray-700">
            <th class="border p-2 text-center w-12">STT</th>
            <th class="border p-2">Bài kiểm tra</th>
            <th class="border p-2 text-center">Điểm môn học</th>
            <th class="border p-2 text-center">Điểm tư duy</th>
            <th class="border p-2 text-center">Tổng điểm</th>
            <th class="border p-2 text-center">Level gán</th>
            <th class="border p-2 text-center">Ngày thi</th>
            <th class="border p-2 text-center w-28">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="diagnosticList.length === 0">
            <td colspan="8" class="text-center py-4 text-gray-500">Chưa có bài khảo sát đầu vào nào</td>
          </tr>
          <tr v-for="(item, idx) in diagnosticList" :key="'diag_' + item.id">
            <td class="border p-2 text-center">{{ idx + 1 }}</td>
            <td class="border p-2 font-semibold">{{ item.test_nm }}</td>
            <td class="border p-2 text-center">{{ item.subject_total || 0 }}</td>
            <td class="border p-2 text-center">{{ item.thinking_total || 0 }}</td>
            <td class="border p-2 text-center font-bold text-primary">{{ item.total_score || 0 }}</td>
            <td class="border p-2 text-center">
              <vs-chip v-if="item.assigned_level" color="primary">{{ item.assigned_level }}</vs-chip>
              <span v-else class="text-gray-400">-</span>
            </td>
            <td class="border p-2 text-center">{{ item.eval_dt ? item.eval_dt.substring(0, 10) : '-' }}</td>
            <td class="border p-2 text-center">
              <router-link
                :to="`/lms/evaluations/igbh/diagnostic/form/${item.id}`"
                class="text-primary font-bold hover:underline"
              >
                Chấm / Chi tiết
              </router-link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Section 3: IG.BH Summative Final Evaluations -->
    <div class="border rounded-lg p-4 bg-white shadow-sm">
      <h5 class="font-bold text-base text-purple-900 mb-3 flex items-center">
        <i class="feather icon-star mr-2 text-purple-600"></i> 3. Đánh giá tổng kết kỳ (IG.BH Summative)
      </h5>
      <table class="w-full text-sm border-collapse border border-gray-200">
        <thead>
          <tr class="bg-gray-100 text-xs font-bold text-gray-700">
            <th class="border p-2 text-center w-12">STT</th>
            <th class="border p-2">Bài kiểm tra</th>
            <th class="border p-2">Lớp</th>
            <th class="border p-2 text-center">Tổng điểm</th>
            <th class="border p-2 text-center">Ngày đánh giá</th>
            <th class="border p-2 text-center">Trạng thái</th>
            <th class="border p-2 text-center w-28">Báo cáo</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="summativeList.length === 0">
            <td colspan="7" class="text-center py-4 text-gray-500">Chưa có kết quả tổng kết kỳ nào</td>
          </tr>
          <tr v-for="(item, idx) in summativeList" :key="'smtv_' + item.id">
            <td class="border p-2 text-center">{{ idx + 1 }}</td>
            <td class="border p-2 font-semibold">{{ item.test_nm }}</td>
            <td class="border p-2">{{ item.erp_class_name || item.class_nm || '-' }}</td>
            <td class="border p-2 text-center font-bold text-primary">{{ item.total_score || 0 }}</td>
            <td class="border p-2 text-center">{{ item.eval_dt ? item.eval_dt.substring(0, 10) : '-' }}</td>
            <td class="border p-2 text-center">
              <vs-chip v-if="item.status === 'completed'" color="success">Đã hoàn thành</vs-chip>
              <vs-chip v-else color="warning">Bản nháp</vs-chip>
            </td>
            <td class="border p-2 text-center">
              <router-link
                :to="`/lms/evaluations/igbh/summative/report/${item.id}`"
                class="text-primary font-bold hover:underline"
              >
                Báo cáo 12 tuần
              </router-link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import axios from '@/http/axios.js'

export default {
  props: {
    student_info: {
      type: Object,
      default: () => ({})
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
      handler(newVal) {
        if (newVal) this.loadStudentEvaluations()
      },
      immediate: true
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
      axios.g('/api/lms/evaluations/igbh/results', { student_id: studentId, per_page: 50 }).then(res => {
        if (res && res.data && res.data.data) {
          this.diagnosticList = res.data.data
        }
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

