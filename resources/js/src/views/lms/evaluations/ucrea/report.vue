<template>
  <div id="ucrea-eval-report">
    <!-- Action buttons (Hidden on print) -->
    <div class="flex flex-wrap items-center justify-between gap-4 mb-4 print:hidden">
      <div>
        <h4 class="font-bold text-xl">Báo Cáo Đánh Giá Năng Lực UCREA</h4>
        <p class="text-sm text-gray-500">Báo cáo năng lực tư duy sáng tạo, toán học, logic</p>
      </div>
      <div v-if="result" class="flex items-center gap-2">
        <router-link :to="`/lms/evaluations/ucrea/form/${result.general.id}`">
          <vs-button color="warning" icon="edit">Sửa Điểm</vs-button>
        </router-link>
        <vs-button color="primary" icon="print" @click="printReport">In Báo Cáo</vs-button>
        <router-link to="/lms/evaluations/ucrea">
          <vs-button color="dark" type="border" icon="arrow_back">Quay lại</vs-button>
        </router-link>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12 text-gray-500">
      <i class="fa fa-spinner fa-spin text-2xl mb-2"></i>
      <p>Đang tải báo cáo...</p>
    </div>

    <!-- Main Report Sheet -->
    <div v-else-if="result" id="printable-report" class="bg-white text-black p-6 mx-auto w-full max-w-4xl shadow border border-gray-200 print:shadow-none print:border-none print:p-0">
      <!-- Header banner -->
      <div class="bg-blue-900 text-white p-5 rounded-t-lg print:rounded-none flex flex-wrap justify-between items-center">
        <div>
          <h2 class="text-2xl font-bold uppercase tracking-wider text-yellow-400">UCREA ASSESSMENT REPORT</h2>
          <p class="text-xs text-blue-200 uppercase mt-0.5">{{ reportData.name || 'BÁO CÁO ĐÁNH GIÁ NĂNG LỰC' }}</p>
        </div>
        <div class="bg-white text-blue-900 font-bold text-xs px-4 py-2 rounded-full shadow mt-2 sm:mt-0">
          <span>{{ result.general.stu_nm }}</span>
          <span class="mx-2">|</span>
          <span>{{ result.general.gender_nm || 'Nam' }}</span>
          <span class="mx-2">|</span>
          <span>{{ result.general.age_nm || '4 Tuổi' }}</span>
          <span class="mx-2">|</span>
          <span>{{ result.general.class_nm || '-' }}</span>
        </div>
      </div>

      <!-- Overview Radar Chart Section -->
      <div class="mt-6">
        <h4 class="font-bold text-base text-blue-900 border-b-2 border-blue-900 pb-1 mb-4 uppercase">
          1. Biểu đồ tổng quan 4 lĩnh vực tư duy
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
          <div ref="chartOverall" style="width: 100%; height: 280px;"></div>
          <div>
            <table class="w-full text-sm border-collapse border border-gray-300">
              <thead>
                <tr class="bg-gray-100 text-xs font-bold text-gray-700">
                  <th class="border border-gray-300 p-2 text-left">Lĩnh vực tư duy</th>
                  <th class="border border-gray-300 p-2 text-center w-24">Đánh giá</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(g, idx) in mainCategoryGrades" :key="idx" class="border-b border-gray-200">
                  <td class="border border-gray-300 p-2 font-semibold text-gray-800">{{ g.skill }}</td>
                  <td class="border border-gray-300 p-2 text-center">
                    <span :class="getGradeBadgeClass(g.grade)">{{ g.grade }}</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Detailed Categories Charts (4 Radar Charts) -->
      <div class="mt-8">
        <h4 class="font-bold text-base text-blue-900 border-b-2 border-blue-900 pb-1 mb-4 uppercase">
          2. Chi tiết từng kỹ năng thành phần
        </h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
          <!-- Chart 1: Tư duy cơ bản -->
          <div class="border border-gray-200 rounded p-3">
            <h5 class="font-bold text-sm text-center text-blue-800 mb-2">Tư duy cơ bản</h5>
            <div ref="chart1" style="width: 100%; height: 220px;"></div>
          </div>

          <!-- Chart 2: Tư duy toán học -->
          <div class="border border-gray-200 rounded p-3">
            <h5 class="font-bold text-sm text-center text-blue-800 mb-2">Tư duy toán học</h5>
            <div ref="chart2" style="width: 100%; height: 220px;"></div>
          </div>

          <!-- Chart 3: Tư duy logic -->
          <div class="border border-gray-200 rounded p-3">
            <h5 class="font-bold text-sm text-center text-blue-800 mb-2">Tư duy logic</h5>
            <div ref="chart3" style="width: 100%; height: 220px;"></div>
          </div>

          <!-- Chart 4: Tư duy sáng tạo -->
          <div class="border border-gray-200 rounded p-3">
            <h5 class="font-bold text-sm text-center text-blue-800 mb-2">Tư duy sáng tạo</h5>
            <div ref="chart4" style="width: 100%; height: 220px;"></div>
          </div>
        </div>
      </div>

      <!-- Rubric Summary Table -->
      <div class="mt-8">
        <h4 class="font-bold text-base text-blue-900 border-b-2 border-blue-900 pb-1 mb-4 uppercase">
          3. Bảng điểm chi tiết các tiêu chí Rubric
        </h4>
        <table class="w-full text-xs border-collapse border border-gray-300">
          <thead>
            <tr class="bg-gray-100 text-gray-700">
              <th class="border border-gray-300 p-2 text-center w-12">STT</th>
              <th class="border border-gray-300 p-2 text-left">Lĩnh vực kiểm tra</th>
              <th class="border border-gray-300 p-2 text-left">Tiêu chí chi tiết</th>
              <th class="border border-gray-300 p-2 text-center w-24">Điểm đạt được</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(r, idx) in result.rubrics" :key="idx" class="border-b border-gray-200">
              <td class="border border-gray-300 p-2 text-center text-gray-500">{{ idx + 1 }}</td>
              <td class="border border-gray-300 p-2 font-medium">{{ r.main_category }}</td>
              <td class="border border-gray-300 p-2">{{ r.sub_category }} - <strong>{{ r.rubric_name }}</strong></td>
              <td class="border border-gray-300 p-2 text-center font-bold text-blue-900">{{ r.assigned_score }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Footer Signatures -->
      <div class="mt-10 pt-6 border-t border-gray-300 grid grid-cols-2 text-center text-xs">
        <div>
          <p class="font-bold text-gray-700 uppercase">Giáo viên phụ trách</p>
          <div class="h-16"></div>
          <p class="font-semibold text-gray-800">{{ result.general.memb_nm || 'Giáo viên' }}</p>
        </div>
        <div>
          <p class="font-bold text-gray-700 uppercase">Trung tâm / Học viện</p>
          <div class="h-16"></div>
          <p class="font-semibold text-gray-800">Ban Chuyên Môn</p>
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
      result: null,
      reportData: {},
      skillsGrade: [],
      loading: true
    }
  },
  computed: {
    mainCategoryGrades() {
      const targets = ['Tư duy cơ bản', 'Tư duy toán học', 'Tư duy logic', 'Tư duy sáng tạo']
      return this.skillsGrade.filter(g => targets.includes(g.skill))
    }
  },
  created() {
    this.fetchData()
  },
  methods: {
    fetchData() {
      this.loading = true
      const id = this.$route.params.id
      axios.g(`/api/lms/evaluations/ucrea/results/${id}`).then(res => {
        this.loading = false
        if (res && res.data && res.data.status === 'success') {
          this.result = res.data.data
          if (this.result.general.report_data) {
            this.reportData = typeof this.result.general.report_data === 'string'
              ? JSON.parse(this.result.general.report_data)
              : this.result.general.report_data
          }
          if (this.result.general.skills_grade) {
            this.skillsGrade = typeof this.result.general.skills_grade === 'string'
              ? JSON.parse(this.result.general.skills_grade)
              : this.result.general.skills_grade
          }
          this.$nextTick(() => {
            this.renderCharts()
          })
        }
      }).catch(err => {
        this.loading = false
      })
    },
    renderCharts() {
      const d = this.reportData || {}
      const getVal = key => parseFloat(d[key] || 70)

      // 1. Chart Overall
      if (this.$refs.chartOverall) {
        const cOverall = echarts.init(this.$refs.chartOverall)
        const avgCoBan = (getVal('sk11') + getVal('sk12') + getVal('sk13')) / 3
        const avgToanHoc = (getVal('kn11') + getVal('kn12') + getVal('kn13') + getVal('kn14') + getVal('kn15')) / 5
        const avgLogic = (getVal('sk21') + getVal('sk22') + getVal('sk23') + getVal('sk24')) / 4
        const avgSangTao = (getVal('sk31') + getVal('sk32') + getVal('sk33') + getVal('sk34')) / 4

        cOverall.setOption({
          radar: {
            indicator: [
              { name: 'Tư duy cơ bản', max: 100 },
              { name: 'Tư duy toán học', max: 100 },
              { name: 'Tư duy logic', max: 100 },
              { name: 'Tư duy sáng tạo', max: 100 }
            ],
            splitNumber: 4,
            radius: '65%'
          },
          series: [{
            type: 'radar',
            data: [{
              value: [Math.round(avgCoBan), Math.round(avgToanHoc), Math.round(avgLogic), Math.round(avgSangTao)],
              name: 'Điểm tổng quan',
              areaStyle: { color: 'rgba(59, 130, 246, 0.4)' },
              itemStyle: { color: '#2563eb' }
            }]
          }]
        })
      }

      // 2. Chart 1: Tư duy cơ bản
      if (this.$refs.chart1) {
        const c1 = echarts.init(this.$refs.chart1)
        c1.setOption({
          radar: {
            indicator: [
              { name: 'Chú ý', max: 100 },
              { name: 'Quan sát', max: 100 },
              { name: 'Ghi nhớ', max: 100 }
            ],
            splitNumber: 4,
            radius: '60%'
          },
          series: [{
            type: 'radar',
            data: [{
              value: [getVal('sk11'), getVal('sk12'), getVal('sk13')],
              areaStyle: { color: 'rgba(16, 185, 129, 0.3)' },
              itemStyle: { color: '#059669' }
            }]
          }]
        })
      }

      // 3. Chart 2: Tư duy toán học
      if (this.$refs.chart2) {
        const c2 = echarts.init(this.$refs.chart2)
        c2.setOption({
          radar: {
            indicator: [
              { name: 'Số học', max: 100 },
              { name: 'Hình học', max: 100 },
              { name: 'Đo lường', max: 100 },
              { name: 'Kiểu mẫu', max: 100 },
              { name: 'Dữ liệu', max: 100 }
            ],
            splitNumber: 4,
            radius: '60%'
          },
          series: [{
            type: 'radar',
            data: [{
              value: [getVal('kn11'), getVal('kn12'), getVal('kn13'), getVal('kn14'), getVal('kn15')],
              areaStyle: { color: 'rgba(245, 158, 11, 0.3)' },
              itemStyle: { color: '#d97706' }
            }]
          }]
        })
      }

      // 4. Chart 3: Tư duy logic
      if (this.$refs.chart3) {
        const c3 = echarts.init(this.$refs.chart3)
        c3.setOption({
          radar: {
            indicator: [
              { name: 'Hiểu', max: 100 },
              { name: 'Áp dụng', max: 100 },
              { name: 'Phân tích', max: 100 },
              { name: 'Tổng hợp', max: 100 }
            ],
            splitNumber: 4,
            radius: '60%'
          },
          series: [{
            type: 'radar',
            data: [{
              value: [getVal('sk21'), getVal('sk22'), getVal('sk23'), getVal('sk24')],
              areaStyle: { color: 'rgba(99, 102, 241, 0.3)' },
              itemStyle: { color: '#4f46e5' }
            }]
          }]
        })
      }

      // 5. Chart 4: Tư duy sáng tạo
      if (this.$refs.chart4) {
        const c4 = echarts.init(this.$refs.chart4)
        c4.setOption({
          radar: {
            indicator: [
              { name: 'Lưu loát', max: 100 },
              { name: 'Linh hoạt', max: 100 },
              { name: 'Độc đáo', max: 100 },
              { name: 'Xây dựng', max: 100 }
            ],
            splitNumber: 4,
            radius: '60%'
          },
          series: [{
            type: 'radar',
            data: [{
              value: [getVal('sk31'), getVal('sk32'), getVal('sk33'), getVal('sk34')],
              areaStyle: { color: 'rgba(236, 72, 153, 0.3)' },
              itemStyle: { color: '#db2777' }
            }]
          }]
        })
      }
    },
    getGradeBadgeClass(grade) {
      if (grade === 'S') return 'px-2 py-0.5 rounded bg-purple-100 text-purple-800 font-bold'
      if (grade === 'A') return 'px-2 py-0.5 rounded bg-green-100 text-green-800 font-bold'
      if (grade === 'B') return 'px-2 py-0.5 rounded bg-blue-100 text-blue-800 font-bold'
      return 'px-2 py-0.5 rounded bg-gray-100 text-gray-800 font-bold'
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

