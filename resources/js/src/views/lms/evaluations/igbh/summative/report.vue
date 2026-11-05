<template>
  <div id="igbh-summative-report">
    <!-- Action Bar -->
    <div class="flex flex-wrap items-center justify-between gap-4 mb-4 print:hidden">
      <div>
        <h4 class="font-bold text-xl">Báo Cáo Đánh Giá Cuối Kỳ (IG.BH Summative)</h4>
        <p class="text-sm text-gray-500">Báo cáo tổng kết 12 tuần học tập và kết quả bài thi cuối kỳ</p>
      </div>
      <div v-if="reportData" class="flex items-center gap-2">
        <router-link :to="`/lms/evaluations/igbh/summative/form/${reportData.result.id}`">
          <vs-button color="warning" icon="edit">Sửa Điểm</vs-button>
        </router-link>
        <vs-button color="primary" icon="print" @click="printReport">In Báo Cáo</vs-button>
        <router-link to="/lms/evaluations/igbh/summative">
          <vs-button color="dark" type="border" icon="arrow_back">Quay lại</vs-button>
        </router-link>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12 text-gray-500">
      <i class="fa fa-spinner fa-spin text-2xl mb-2"></i>
      <p>Đang tải dữ liệu báo cáo...</p>
    </div>

    <!-- Main Report -->
    <div v-else-if="reportData" id="printable-report" class="bg-white text-black p-6 mx-auto w-full max-w-4xl shadow border border-gray-200 print:shadow-none print:border-none print:p-0">
      <!-- Header Banner -->
      <div class="bg-yellow-500 text-white p-5 rounded-t-lg print:rounded-none flex flex-wrap justify-between items-center">
        <div>
          <h2 class="text-2xl font-black uppercase tracking-wider drop-shadow">ĐÁNH GIÁ CUỐI KỲ IG.BH</h2>
          <p class="text-xs text-yellow-100 uppercase mt-0.5">BÁO CÁO NĂNG LỰC TOÀN DIỆN 12 TUẦN</p>
        </div>
        <div class="bg-white text-yellow-800 font-bold text-xs px-4 py-2 rounded-full shadow mt-2 sm:mt-0">
          <span>{{ reportData.result.stu_nm }}</span>
          <span class="mx-2">|</span>
          <span>{{ reportData.result.class_nm || '-' }}</span>
          <span class="mx-2">|</span>
          <span>{{ reportData.result.date_of_birth || '-' }}</span>
        </div>
      </div>

      <!-- Section 1: Sách bài tập 12 tuần -->
      <div class="mt-6">
        <h4 class="font-bold text-base text-yellow-800 border-b-2 border-yellow-500 pb-1 mb-3 uppercase">
          1. Tiến trình làm bài tập 12 tuần
        </h4>
        <table class="w-full text-center border-collapse border border-gray-300 text-xs">
          <thead class="bg-yellow-50">
            <tr>
              <th class="border p-2">Tuần</th>
              <th v-for="w in 12" :key="w" class="border p-2 w-10">T.{{ w }}</th>
              <th class="border p-2 font-bold w-20">Tổng</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="border p-2 font-bold bg-yellow-100 text-yellow-900">Bài tập</td>
              <td v-for="(w, idx) in reportData.report_data" :key="idx" class="border p-2">
                {{ w.weekly_workbook !== null ? w.weekly_workbook : '-' }}
              </td>
              <td class="border p-2 font-black text-red-600 bg-red-50 text-sm">
                {{ reportData.total_workbook }} / {{ reportData.max_workbook }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Section 2: Radar Charts (Thái độ & Quan sát) -->
      <div class="mt-8">
        <h4 class="font-bold text-base text-yellow-800 border-b-2 border-yellow-500 pb-1 mb-4 uppercase">
          2. Biểu đồ đánh giá thái độ & năng lực quan sát
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="border border-gray-200 rounded p-4">
            <h5 class="font-bold text-sm text-center text-blue-900 mb-2">Thái độ học tập trên lớp</h5>
            <div ref="chartAttitude" style="width: 100%; height: 240px;"></div>
            <div class="text-xs text-gray-600 mt-2 text-center">
              Lắng nghe: <strong>{{ reportData.avg_attitude.listen }}</strong> |
              Tham gia: <strong>{{ reportData.avg_attitude.join }}</strong> |
              Thể hiện: <strong>{{ reportData.avg_attitude.express }}</strong> |
              Hợp tác: <strong>{{ reportData.avg_attitude.coop }}</strong>
            </div>
          </div>

          <div class="border border-gray-200 rounded p-4">
            <h5 class="font-bold text-sm text-center text-blue-900 mb-2">Năng lực quan sát & tư duy</h5>
            <div ref="chartDetection" style="width: 100%; height: 240px;"></div>
            <div class="text-xs text-gray-600 mt-2 text-center">
              Cơ bản: <strong>{{ reportData.avg_detection.normal }}</strong> |
              Lãnh đạo: <strong>{{ reportData.avg_detection.leadersh }}</strong> |
              Toán học: <strong>{{ reportData.avg_detection.math }}</strong> |
              Sáng tạo: <strong>{{ reportData.avg_detection.creative }}</strong>
            </div>
          </div>
        </div>
      </div>

      <!-- Section 3: Điểm thi tự luận cuối kỳ -->
      <div class="mt-8">
        <h4 class="font-bold text-base text-yellow-800 border-b-2 border-yellow-500 pb-1 mb-3 uppercase">
          3. Kết quả bài kiểm tra tự luận cuối kỳ
        </h4>
        <table class="w-full text-center border-collapse border border-gray-300 text-xs">
          <thead class="bg-gray-100">
            <tr>
              <th class="border p-2">Câu hỏi</th>
              <th class="border p-2">Khái niệm</th>
              <th class="border p-2">Chiến lược</th>
              <th class="border p-2">Tính toán</th>
              <th class="border p-2">Diễn đạt</th>
              <th class="border p-2 font-bold">Điểm đạt</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(sub, idx) in reportData.subjective_details" :key="idx">
              <td class="border p-2 font-bold">Câu {{ sub.sort_no }}</td>
              <td class="border p-2">{{ sub.concept || 0 }}</td>
              <td class="border p-2">{{ sub.strategy || 0 }}</td>
              <td class="border p-2">{{ sub.calculation || 0 }}</td>
              <td class="border p-2">{{ sub.expression || 0 }}</td>
              <td class="border p-2 font-bold text-primary">{{ sub.score }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Section 4: Nhận xét của giáo viên -->
      <div class="mt-8 border border-gray-300 rounded p-4 bg-gray-50">
        <h5 class="font-bold text-sm text-gray-800 mb-2 uppercase">Nhận xét của giáo viên phụ trách</h5>
        <p class="text-sm text-gray-700 italic leading-relaxed">
          {{ teacherComment || 'Học sinh có tinh thần học tập tốt, hoàn thành đầy đủ bài tập và tích cực tham gia các hoạt động tư duy trong suốt 12 tuần.' }}
        </p>
      </div>

      <!-- Signatures -->
      <div class="mt-10 pt-6 border-t border-gray-300 grid grid-cols-2 text-center text-xs">
        <div>
          <p class="font-bold text-gray-700 uppercase">Giáo viên phụ trách</p>
          <div class="h-16"></div>
          <p class="font-semibold text-gray-800">{{ reportData.result.teacher_nm || 'Giáo viên' }}</p>
        </div>
        <div>
          <p class="font-bold text-gray-700 uppercase">Học viện / Trung tâm</p>
          <div class="h-16"></div>
          <p class="font-semibold text-gray-800">Ban Chuyên Môn IG.BH</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from '@/http/axios.js'
import * as echarts from 'echarts'

export default {
  data() {
    return {
      reportData: null,
      loading: true
    }
  },
  computed: {
    teacherComment() {
      if (this.reportData && this.reportData.subjective_analysis) {
        return this.reportData.subjective_analysis.teacher_comment || ''
      }
      return ''
    }
  },
  created() {
    this.fetchData()
  },
  methods: {
    fetchData() {
      this.loading = true
      const id = this.$route.params.id
      axios.g(`/api/lms/evaluations/summative/results/${id}/report`).then(res => {
        this.loading = false
        if (res && res.data) {
          this.reportData = res.data
          this.$nextTick(() => {
            this.renderCharts()
          })
        }
      }).catch(err => {
        this.loading = false
      })
    },
    renderCharts() {
      if (this.$refs.chartAttitude && this.reportData.avg_attitude) {
        const att = this.reportData.avg_attitude
        const cAtt = echarts.init(this.$refs.chartAttitude)
        cAtt.setOption({
          radar: {
            indicator: [
              { name: 'Lắng nghe', max: 5 },
              { name: 'Tham gia', max: 5 },
              { name: 'Thể hiện', max: 5 },
              { name: 'Hợp tác', max: 5 }
            ],
            splitNumber: 5,
            radius: '65%'
          },
          series: [{
            type: 'radar',
            data: [{
              value: [att.listen, att.join, att.express, att.coop],
              areaStyle: { color: 'rgba(59, 130, 246, 0.4)' },
              itemStyle: { color: '#2563eb' }
            }]
          }]
        })
      }

      if (this.$refs.chartDetection && this.reportData.avg_detection) {
        const det = this.reportData.avg_detection
        const cDet = echarts.init(this.$refs.chartDetection)
        cDet.setOption({
          radar: {
            indicator: [
              { name: 'Kỹ năng cơ bản', max: 5 },
              { name: 'Lãnh đạo', max: 5 },
              { name: 'Toán học', max: 5 },
              { name: 'Sáng tạo', max: 5 }
            ],
            splitNumber: 5,
            radius: '65%'
          },
          series: [{
            type: 'radar',
            data: [{
              value: [det.normal, det.leadersh, det.math, det.creative],
              areaStyle: { color: 'rgba(245, 158, 11, 0.4)' },
              itemStyle: { color: '#d97706' }
            }]
          }]
        })
      }
    },
    printReport() {
      window.print()
    }
  }
}
</script>

<style scoped>
@media print {
  body * {
    visibility: hidden;
  }
  #printable-report, #printable-report * {
    visibility: visible;
  }
  #printable-report {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
  }
}
</style>

