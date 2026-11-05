<template>
  <div class="ucrea-report-wrapper">
    <!-- Action buttons (Hidden on print) -->
    <div class="action-toolbar print-hide">
      <div class="toolbar-info">
        <router-link to="/lms/evaluations/ucrea" class="back-link">
          <i class="feather icon-arrow-left"></i> Quay lại danh sách
        </router-link>
        <h2 class="toolbar-title">Báo Cáo Đánh Giá Năng Lực UCREA</h2>
        <div class="toolbar-sub" v-if="result">
          <span>{{ result.general.test_nm }}</span>
          <span class="sep">|</span>
          <span>Học sinh: <strong class="text-name">{{ result.general.stu_nm }}</strong></span>
          <span class="sep" v-if="result.general.class_nm">|</span>
          <span v-if="result.general.class_nm">Lớp: {{ result.general.class_nm }}</span>
        </div>
      </div>
      <div class="toolbar-btns" v-if="result">
        <button class="btn-print" @click="exportPdf">
          <i class="feather icon-printer"></i> In Báo Cáo (PDF)
        </button>
        <router-link :to="`/lms/evaluations/ucrea/form/${result.general.id}`" class="btn-edit">
          <i class="feather icon-edit"></i> Sửa Điểm
        </router-link>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="loading-state print-hide">
      <div class="spinner"></div>
      <p>Đang tải dữ liệu báo cáo UCREA...</p>
    </div>

    <!-- MAIN REPORT DOCUMENT -->
    <div v-else-if="result" id="ucrea-report-doc" class="report-document">
      
      <!-- ================= PAGE 1 ================= -->
      <div class="report-sheet" id="ucrea-sheet-1">
        
        <!-- Header -->
        <div class="sheet-header">
          <div class="header-brand-block">
            <img src="@assets/images/logo/stemkey-logo.png" alt="STEMKEY" class="brand-logo" />
            <div class="header-titles">
              <p class="super-title">UCREA ASSESSMENT</p>
              <h1 class="main-title">{{ reportData.name || 'ĐÁNH GIÁ ĐẦU KỲ' }}</h1>
            </div>
          </div>
          <!-- White pill student info -->
          <div class="student-pill">
             <span class="pill-name">{{ result.general.stu_nm }}</span>
             <span class="pill-sep">|</span>
             <span>{{ result.general.gender_nm || 'Nam' }}</span>
             <span class="pill-sep">|</span>
             <span>{{ result.general.age_nm || '3 Tuổi' }}</span>
             <span class="pill-sep">|</span>
             <span>{{ result.general.class_nm || '-' }}</span>
          </div>
        </div>

        <div class="sheet-body">
          <!-- 1. Lĩnh vực đánh giá -->
          <div class="section-title-wrap">
            <h2 class="section-title">Lĩnh vực đánh giá</h2>
          </div>
          
          <div class="two-col-layout mb-24">
            <div class="col-7">
              <table class="ucrea-grid-table">
                <tbody>
                  <tr>
                    <td class="cell-category-label">Tư duy cơ bản</td>
                    <td class="cell-category-desc">Đánh giá sự chú ý và tập trung, quan sát và ghi nhớ, đó là những nhân tố cơ bản cần thiết cho sự phát triển tư duy.</td>
                  </tr>
                  <tr>
                    <td class="cell-category-label">Tư duy toán học</td>
                    <td class="cell-category-desc">Đánh giá năng lực về số và phép toán, không gian, đo lường, kiểu mẫu, thống kê; đó là những nhân tố cơ bản cần thiết cho sự phát triển tư duy.</td>
                  </tr>
                  <tr>
                    <td class="cell-category-label">Tư duy logic</td>
                    <td class="cell-category-desc">Đánh giá sự hiểu biết, áp dụng, phân tích và tổng hợp; đó là các thành phần của kỹ năng tư duy logic, kỹ năng cốt lõi để phát triển tư duy.</td>
                  </tr>
                  <tr>
                    <td class="cell-category-label">Tư duy sáng tạo</td>
                    <td class="cell-category-desc">Đánh giá sự lưu loát, linh hoạt, độc đáo và tinh tế, đó là những kỹ năng cần thiết cho tư duy đa dạng và sâu sắc.</td>
                  </tr>
                </tbody>
              </table>
            </div>
            
            <div class="col-5">
              <table class="ucrea-grid-table text-center">
                <thead>
                  <tr class="header-row-orange">
                    <th colspan="3" class="text-center font-bold">Tiêu chuẩn và cấp độ</th>
                  </tr>
                  <tr class="sub-header-row">
                    <th style="width: 33%;">Tiêu chuẩn</th>
                    <th style="width: 33%;">Cấp độ</th>
                    <th style="width: 34%;">Điểm</th>
                  </tr>
                </thead>
                <tbody>
                  <tr><td class="font-medium text-slate-700">Vượt trội</td><td class="font-bold text-slate-800">S</td><td class="text-slate-500">91~100</td></tr>
                  <tr><td class="font-medium text-slate-700">Rất tốt</td><td class="font-bold text-slate-800">A</td><td class="text-slate-500">71~90</td></tr>
                  <tr><td class="font-medium text-slate-700">Trung bình</td><td class="font-bold text-slate-800">B</td><td class="text-slate-500">41~70</td></tr>
                  <tr><td class="font-medium text-slate-700">Yếu</td><td class="font-bold text-slate-800">C</td><td class="text-slate-500">0~40</td></tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- 2. Kết quả đánh giá -->
          <div class="section-title-wrap flex-between">
            <h2 class="section-title mb-0">Kết quả đánh giá</h2>
            
            <!-- Checkboxes for related tests -->
            <div class="test-period-checkboxes print-hide" v-if="allTests.length">
              <label v-for="t in allTests" :key="t.test_cd" 
                     class="checkbox-label"
                     :class="{ 'disabled-label': !t.hasData || t.isCurrent }">
                <input type="checkbox" 
                       :value="t.dataId" 
                       v-model="selectedTestIds" 
                       @change="initCharts" 
                       :disabled="!t.hasData || t.isCurrent"
                       class="check-input">
                <span class="check-text uppercase">{{ t.name }}</span>
              </label>
            </div>
          </div>
          
          <!-- Section: Tư duy cơ bản -->
          <div class="two-col-layout mb-24 align-center">
            <div class="col-5">
              <div class="chart-box">
                <div ref="chart1" class="chart-canvas-el"></div>
              </div>
            </div>
            <div class="col-7">
              <table class="ucrea-score-table">
                <thead>
                  <tr class="header-row-blue">
                    <th style="width: 28%; text-align: left; padding-left: 12px;">Lĩnh vực chi tiết</th>
                    <th style="width: 57%; text-align: left; padding-left: 12px;">Nội dung chi tiết</th>
                    <th style="width: 15%; text-align: center;">Cấp độ</th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="row-even">
                    <td class="cell-skill-name">Năng lực chú ý</td>
                    <td class="cell-skill-desc">Khả năng tập trung quan sát để giải quyết vấn đề.</td>
                    <td class="cell-skill-grade">{{ getGrade('Năng lực chú ý') }}</td>
                  </tr>
                  <tr class="row-odd">
                    <td class="cell-skill-name">Năng lực quan sát</td>
                    <td class="cell-skill-desc">Khả năng quan sát tỉ mỉ một sự vật nào đó.</td>
                    <td class="cell-skill-grade">{{ getGrade('Năng lực quan sát') }}</td>
                  </tr>
                  <tr class="row-even">
                    <td class="cell-skill-name">Năng lực ghi nhớ</td>
                    <td class="cell-skill-desc">Khả năng ghi nhớ một thông tin mới về sự vật, sự việc.</td>
                    <td class="cell-skill-grade">{{ getGrade('Năng lực ghi nhớ') }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Section: Tư duy toán học -->
          <div class="two-col-layout mb-20 align-center">
            <div class="col-5">
              <div class="chart-box">
                <div ref="chart2" class="chart-canvas-el"></div>
              </div>
            </div>
            <div class="col-7">
              <table class="ucrea-score-table">
                <thead>
                  <tr class="header-row-blue">
                    <th style="width: 28%; text-align: left; padding-left: 12px;">Lĩnh vực chi tiết</th>
                    <th style="width: 57%; text-align: left; padding-left: 12px;">Nội dung chi tiết</th>
                    <th style="width: 15%; text-align: center;">Cấp độ</th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="row-even">
                    <td class="cell-skill-name">Số và tính toán</td>
                    <td class="cell-skill-desc">Khả năng hiểu những khái niệm về tính toán cơ bản, hiểu được ý nghĩa của toán học trong đời sống sinh hoạt hàng ngày.</td>
                    <td class="cell-skill-grade">{{ getGrade('Số và tính toán') }}</td>
                  </tr>
                  <tr class="row-odd">
                    <td class="cell-skill-name">Hình học không gian</td>
                    <td class="cell-skill-desc">Khả năng nhận biết vị trí và hướng trong không gian và hiểu biết về hình dạng.</td>
                    <td class="cell-skill-grade">{{ getGrade('Hình học không gian') }}</td>
                  </tr>
                  <tr class="row-even">
                    <td class="cell-skill-name">Đo lường</td>
                    <td class="cell-skill-desc">Khả năng thể hiện số lượng bằng cách sử dụng số và so sánh các đồ vật với nhau.</td>
                    <td class="cell-skill-grade">{{ getGrade('Đo lường') }}</td>
                  </tr>
                  <tr class="row-odd">
                    <td class="cell-skill-name">Kiểu mẫu</td>
                    <td class="cell-skill-desc">Khả năng nhận diện và dự đoán mối quan hệ mang tính quy luật của đồ vật.</td>
                    <td class="cell-skill-grade">{{ getGrade('Kiểu mẫu') }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          
        </div>
      </div>

      <!-- PAGE BREAK DIVIDER -->
      <div class="page-break-divider"></div>

      <!-- ================= PAGE 2 ================= -->
      <div class="report-sheet sheet-page-2" id="ucrea-sheet-2">
        <div class="sheet-body pt-24">
          <!-- Section: Tư duy logic -->
          <div class="two-col-layout mb-24 align-center">
            <div class="col-5">
              <div class="chart-box">
                <div ref="chart3" class="chart-canvas-el"></div>
              </div>
            </div>
            <div class="col-7">
              <table class="ucrea-score-table">
                <thead>
                  <tr class="header-row-blue">
                    <th style="width: 28%; text-align: left; padding-left: 12px;">Lĩnh vực chi tiết</th>
                    <th style="width: 57%; text-align: left; padding-left: 12px;">Nội dung chi tiết</th>
                    <th style="width: 15%; text-align: center;">Cấp độ</th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="row-even">
                    <td class="cell-skill-name">Hiểu biết</td>
                    <td class="cell-skill-desc">Khả năng phân tích những dự kiện liên quan và hiểu về vấn đề cần giải quyết.</td>
                    <td class="cell-skill-grade">{{ getGrade('Hiểu biết') }}</td>
                  </tr>
                  <tr class="row-odd">
                    <td class="cell-skill-name">Ứng dụng</td>
                    <td class="cell-skill-desc">Khả năng vận dụng linh hoạt những vấn đề đã học vào thực hành thực tế.</td>
                    <td class="cell-skill-grade">{{ getGrade('Ứng dụng') }}</td>
                  </tr>
                  <tr class="row-even">
                    <td class="cell-skill-name">Phân tích</td>
                    <td class="cell-skill-desc">Năng lực so sánh các thuộc tính và khái quát hóa vấn đề.</td>
                    <td class="cell-skill-grade">{{ getGrade('Phân tích') }}</td>
                  </tr>
                  <tr v-if="!isL1L2" class="row-odd">
                    <td class="cell-skill-name">Tổng hợp</td>
                    <td class="cell-skill-desc">Khả năng phân tích tình huống và giải quyết toàn diện vấn đề.</td>
                    <td class="cell-skill-grade">{{ getGrade('Tổng hợp') }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Section: Tư duy sáng tạo -->
          <div class="two-col-layout mb-28 align-center">
            <div class="col-5">
              <div class="chart-box">
                <div ref="chart4" class="chart-canvas-el"></div>
              </div>
            </div>
            <div class="col-7">
              <table class="ucrea-score-table">
                <thead>
                  <tr class="header-row-blue">
                    <th style="width: 28%; text-align: left; padding-left: 12px;">Lĩnh vực chi tiết</th>
                    <th style="width: 57%; text-align: left; padding-left: 12px;">Nội dung chi tiết</th>
                    <th style="width: 15%; text-align: center;">Cấp độ</th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="row-even">
                    <td class="cell-skill-name">Sự trôi chảy</td>
                    <td class="cell-skill-desc">Khả năng thể hiện nhiều ý tưởng mới lạ.</td>
                    <td class="cell-skill-grade">{{ getGrade('Sự trôi chảy') }}</td>
                  </tr>
                  <tr class="row-odd">
                    <td class="cell-skill-name">Tính linh hoạt</td>
                    <td class="cell-skill-desc">Khả năng tìm và giải quyết vấn đề và những ý tưởng đa dạng bằng cách vận dụng một đối tượng thống nhất.</td>
                    <td class="cell-skill-grade">{{ getGrade('Tính linh hoạt') }}</td>
                  </tr>
                  <tr class="row-even">
                    <td class="cell-skill-name">Tính độc đáo</td>
                    <td class="cell-skill-desc">Khả năng đưa ra một ý tưởng độc đáo và mới lạ hoặc hoàn toàn khác biệt.</td>
                    <td class="cell-skill-grade">{{ getGrade('Tính độc đáo') }}</td>
                  </tr>
                  <tr class="row-odd">
                    <td class="cell-skill-name">Tính chính xác</td>
                    <td class="cell-skill-desc">Khả năng bổ sung thêm những điểm chi tiết của ý tưởng có tính phức tạp cao.</td>
                    <td class="cell-skill-grade">{{ getGrade('Tính chính xác') }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- 3. Đánh giá tổng quát -->
          <div class="overall-section-wrap">
            <div class="section-title-wrap">
              <h2 class="section-title">Đánh giá tổng quát</h2>
            </div>
            <div class="two-col-layout align-center">
              <div class="col-5">
                <div class="chart-box chart-overall-box">
                  <div ref="chartOverall" class="chart-canvas-el"></div>
                </div>
              </div>
              <div class="col-7">
                <table class="ucrea-summary-table">
                  <thead>
                    <tr class="header-row-orange">
                      <th style="width: 22%; text-align: left; padding-left: 12px;">Lĩnh vực</th>
                      <th style="width: 48%; text-align: left; padding-left: 12px;">Nội dung</th>
                      <th style="width: 15%;">Trung bình</th>
                      <th style="width: 15%;">Bình quân<br/>tất cả HS</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="row-peach">
                      <td class="cell-skill-name">Tư duy cơ bản</td>
                      <td class="cell-skill-desc">
                        Tư duy cơ bản của học sinh {{ result.general.stu_nm }} ở mức độ <strong class="grade-label-red">{{ getGradeLabel(getGrade('Tư duy cơ bản')) }}</strong>.
                      </td>
                      <td class="cell-skill-grade">{{ getGrade('Tư duy cơ bản') }}</td>
                      <td class="cell-peer-avg">B</td>
                    </tr>
                    <tr class="row-white">
                      <td class="cell-skill-name">Tư duy toán học</td>
                      <td class="cell-skill-desc">
                        Tư duy toán học của học sinh {{ result.general.stu_nm }} ở mức độ <strong class="grade-label-red">{{ getGradeLabel(getGrade('Tư duy toán học')) }}</strong>.
                      </td>
                      <td class="cell-skill-grade">{{ getGrade('Tư duy toán học') }}</td>
                      <td class="cell-peer-avg">B</td>
                    </tr>
                    <tr class="row-peach">
                      <td class="cell-skill-name">Tư duy logic</td>
                      <td class="cell-skill-desc">
                        Tư duy logic của học sinh {{ result.general.stu_nm }} ở mức độ <strong class="grade-label-red">{{ getGradeLabel(getGrade('Tư duy logic')) }}</strong>.
                      </td>
                      <td class="cell-skill-grade">{{ getGrade('Tư duy logic') }}</td>
                      <td class="cell-peer-avg">B</td>
                    </tr>
                    <tr class="row-white">
                      <td class="cell-skill-name">Tư duy sáng tạo</td>
                      <td class="cell-skill-desc">
                        Tư duy sáng tạo của học sinh {{ result.general.stu_nm }} ở mức độ <strong class="grade-label-red">{{ getGradeLabel(getGrade('Tư duy sáng tạo')) }}</strong>.
                      </td>
                      <td class="cell-skill-grade">{{ getGrade('Tư duy sáng tạo') }}</td>
                      <td class="cell-peer-avg">B</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
      </div>

    </div>
  </div>
</template>

<script>
import axios from '@/http/axios.js'
import * as echarts from 'echarts'

export default {
  name: 'UcreaReportView',
  data() {
    return {
      result: null,
      loading: true,
      charts: [],
      selectedTestIds: []
    }
  },
  computed: {
    isL1L2() {
      return this.result && ['L1', 'L2'].includes(this.result.general.level_cd)
    },
    availableTests() {
      if (!this.result || !this.result.related_results) return []
      return this.result.related_results.map(r => {
        let rd = {}
        try { rd = typeof r.report_data === 'string' ? JSON.parse(r.report_data) : r.report_data } catch(e) {}
        return {
          id: r.id,
          test_cd: r.test_cd,
          name: (rd && rd.name) ? rd.name : r.test_nm,
          reportData: rd || {}
        }
      }).sort((a,b) => {
        return (a.test_cd || '').localeCompare(b.test_cd || '')
      })
    },
    allTests() {
      const types = [
        { test_cd: 'TT001', name: 'Đầu kỳ' },
        { test_cd: 'TT002', name: 'Giữa kỳ' },
        { test_cd: 'TT003', name: 'Cuối kỳ' }
      ]

      return types.map(type => {
        const found = this.availableTests.find(t => t.test_cd === type.test_cd)
        return {
          test_cd: type.test_cd,
          name: type.name,
          hasData: !!found,
          dataId: found ? found.id : null,
          isCurrent: found && this.result && found.id == this.result.general.id
        }
      })
    },
    reportData() {
      if (!this.result || !this.result.general.report_data) return {}
      try {
        return typeof this.result.general.report_data === 'string' ? JSON.parse(this.result.general.report_data) : this.result.general.report_data
      } catch(e) {
        return {}
      }
    },
    parsedSkillsGrade() {
      if (!this.result || !this.result.general.skills_grade) return []
      try {
        return typeof this.result.general.skills_grade === 'string' ? JSON.parse(this.result.general.skills_grade) : this.result.general.skills_grade
      } catch(e) {
        return []
      }
    }
  },
  created() {
    this.fetchResult()
  },
  beforeDestroy() {
    this.charts.forEach(c => {
      if (c && !c.isDisposed()) c.dispose()
    })
  },
  methods: {
    fetchResult() {
      this.loading = true
      const id = this.$route.params.id
      axios.g(`/api/lms/evaluations/ucrea/results/${id}`).then(response => {
        this.loading = false
        if (response && response.data && response.data.status === 'success') {
          this.result = response.data.data
          this.selectedTestIds = this.availableTests.map(t => t.id)
          this.$nextTick(() => {
            this.initCharts()
          })
        }
      }).catch(err => {
        this.loading = false
      })
    },
    getGrade(skillName) {
       const found = this.parsedSkillsGrade.find(s => s.skill === skillName)
       if (found) {
           return found.grade === 'L' ? 'C' : found.grade
       }
       return 'B'
    },
    getGradeLabel(grade) {
      const mapping = {
        'S': 'Vượt trội',
        'A': 'Rất tốt',
        'B': 'Trung bình',
        'C': 'Yếu',
        'L': 'Yếu'
      }
      return mapping[grade] || grade || 'Trung bình'
    },
    exportPdf() {
      window.print()
    },
    initCharts() {
      const selectedData = this.availableTests.filter(t => this.selectedTestIds.includes(t.id))
      if (selectedData.length === 0) return

      const getColor = (test_cd) => {
          if (test_cd === 'TT001') return '#cbd5e1' // Đầu kỳ (Xám)
          if (test_cd === 'TT002') return '#1e3a8a' // Giữa kỳ (Xanh đậm)
          if (test_cd === 'TT003') return '#84cc16' // Cuối kỳ (Xanh lá)
          return '#ef4444'
      }

      const legendData = selectedData.map(t => t.name)
      const legendTopRight = { top: 0, right: 0, data: legendData, itemGap: 6, itemWidth: 14, itemHeight: 9, textStyle: { fontSize: 10, color: '#64748b' } }

      const commonOpts = {
        grid: { top: 32, bottom: 25, left: 28, right: 8 },
        tooltip: { trigger: 'axis' },
        yAxis: { type: 'value', max: 100, axisLabel: { fontSize: 10, color: '#64748b' }, splitLine: { lineStyle: { color: '#f1f5f9' } } },
        xAxis: { type: 'category', axisLabel: { interval: 0, fontSize: 9, color: '#334155' }, axisTick: { show: false } },
        animation: false
      }

      // 1. Tư duy cơ bản
      if (!this.$refs.chart1) return
      const c1 = echarts.getInstanceByDom(this.$refs.chart1) || echarts.init(this.$refs.chart1)
      c1.setOption({
        ...commonOpts,
        title: { text: 'Tư duy cơ bản', left: 'center', textStyle: { fontSize: 13, color: '#1e293b', fontWeight: 'bold' } },
        legend: legendTopRight,
        xAxis: { ...commonOpts.xAxis, data: ['Năng lực\nchú ý', 'Năng lực\nquan sát', 'Năng lực\nghi nhớ'] },
        series: selectedData.map(t => ({
          name: t.name,
          type: 'bar',
          data: [t.reportData.sk11, t.reportData.sk12, t.reportData.sk13],
          barMaxWidth: 18,
          itemStyle: { color: getColor(t.test_cd) }
        }))
      }, true)

      // 2. Tư duy toán học
      let mathData = ['Số và\ntính toán', 'Hình học\nkhông gian', 'Đo lường', 'Kiểu mẫu']
      const c2 = echarts.getInstanceByDom(this.$refs.chart2) || echarts.init(this.$refs.chart2)
      c2.setOption({
        ...commonOpts,
        title: { text: 'Tư duy toán học', left: 'center', textStyle: { fontSize: 13, color: '#1e293b', fontWeight: 'bold' } },
        legend: legendTopRight,
        xAxis: { ...commonOpts.xAxis, data: mathData },
        series: selectedData.map(t => {
          let data = [t.reportData.kn11, t.reportData.kn12, t.reportData.kn13, t.reportData.kn14]
          return {
            name: t.name,
            type: 'bar',
            data: data,
            barMaxWidth: 18,
            itemStyle: { color: getColor(t.test_cd) }
          }
        })
      }, true)

      // 3. Tư duy logic
      let logicData = ['Hiểu biết', 'Ứng dụng', 'Phân tích', 'Tổng hợp']
      if (this.isL1L2) {
          logicData = ['Hiểu biết', 'Ứng dụng', 'Phân tích']
      }
      const c3 = echarts.getInstanceByDom(this.$refs.chart3) || echarts.init(this.$refs.chart3)
      c3.setOption({
        ...commonOpts,
        title: { text: 'Tư duy logic', left: 'center', textStyle: { fontSize: 13, color: '#1e293b', fontWeight: 'bold' } },
        legend: legendTopRight,
        xAxis: { ...commonOpts.xAxis, data: logicData },
        series: selectedData.map(t => {
          let data = [t.reportData.sk21, t.reportData.sk22, t.reportData.sk23, t.reportData.sk24]
          if (this.isL1L2) {
             data = [t.reportData.sk21, t.reportData.sk22, t.reportData.sk23]
          }
          return {
            name: t.name,
            type: 'bar',
            data: data,
            barMaxWidth: 18,
            itemStyle: { color: getColor(t.test_cd) }
          }
        })
      }, true)

      // 4. Tư duy sáng tạo
      const c4 = echarts.getInstanceByDom(this.$refs.chart4) || echarts.init(this.$refs.chart4)
      c4.setOption({
        ...commonOpts,
        title: { text: 'Tư duy sáng tạo', left: 'center', textStyle: { fontSize: 13, color: '#1e293b', fontWeight: 'bold' } },
        legend: legendTopRight,
        xAxis: { ...commonOpts.xAxis, data: ['Sự\ntrôi chảy', 'Tính\nlinh hoạt', 'Tính\nđộc đáo', 'Tính\nchính xác'] },
        series: selectedData.map(t => ({
          name: t.name,
          type: 'bar',
          data: [t.reportData.sk31, t.reportData.sk32, t.reportData.sk33, t.reportData.sk34],
          barMaxWidth: 18,
          itemStyle: { color: getColor(t.test_cd) }
        }))
      }, true)

      // 5. Đánh giá tổng quát
      const cOverall = echarts.getInstanceByDom(this.$refs.chartOverall) || echarts.init(this.$refs.chartOverall)
      
      const overallSeries = selectedData.map(t => {
        const rd = t.reportData
        const avg1 = Math.round((Number(rd.sk11 || 0) + Number(rd.sk12 || 0) + Number(rd.sk13 || 0)) / 3)
        const avg2 = Math.round((Number(rd.kn11 || 0) + Number(rd.kn12 || 0) + Number(rd.kn13 || 0) + Number(rd.kn14 || 0)) / 4)
        const avg3 = this.isL1L2 ?
            Math.round((Number(rd.sk21 || 0) + Number(rd.sk22 || 0) + Number(rd.sk23 || 0)) / 3) :
            Math.round((Number(rd.sk21 || 0) + Number(rd.sk22 || 0) + Number(rd.sk23 || 0) + Number(rd.sk24 || 0)) / 4)
        const avg4 = Math.round((Number(rd.sk31 || 0) + Number(rd.sk32 || 0) + Number(rd.sk33 || 0) + Number(rd.sk34 || 0)) / 4)
        return {
          name: t.name,
          type: 'bar',
          data: [avg1, avg2, avg3, avg4],
          barMaxWidth: 18,
          itemStyle: { color: getColor(t.test_cd) }
        }
      })
      
      overallSeries.push({
         name: 'Bình quân tất cả HS', 
         type: 'bar', 
         data: [66, 71, 69, 61], 
         barMaxWidth: 18, 
         itemStyle: { color: '#f59e0b' }
      })

      cOverall.setOption({
        ...commonOpts,
        title: { text: 'Bình quân tổng quát', left: 'center', textStyle: { fontSize: 13, color: '#1e293b', fontWeight: 'bold' } },
        legend: { top: 0, right: 0, data: [...legendData, 'Bình quân tất cả HS'], itemGap: 5, itemWidth: 14, itemHeight: 9, textStyle: { fontSize: 9, color: '#64748b' } },
        xAxis: { ...commonOpts.xAxis, data: ['Tư duy\ncơ bản', 'Tư duy\ntoán học', 'Tư duy\nlogic', 'Tư duy\nsáng tạo'] },
        series: overallSeries
      }, true)

      this.charts = [c1, c2, c3, c4, cOverall]
    }
  }
}
</script>

<style scoped>
/* ============================================================
   EXACT UCREA REPORT CSS (INDEPENDENT FROM TAILWIND & VUEXY)
   ============================================================ */

.ucrea-report-wrapper {
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
}
.back-link:hover { color: #2563eb; }

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
.toolbar-sub .sep { margin: 0 8px; color: #cbd5e1; }
.toolbar-sub .text-name { color: #2563eb; }

.toolbar-btns { display: flex; gap: 10px; }

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
}
.btn-print:hover { background: #172554; }

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
}
.btn-edit:hover { background: #eff6ff; }

/* Loading */
.loading-state { text-align: center; padding: 80px 0; color: #64748b; }
.spinner {
  width: 36px;
  height: 36px;
  border: 3px solid #e2e8f0;
  border-top-color: #2563eb;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin: 0 auto 12px auto;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* Report Document */
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
  margin-bottom: 24px;
}

/* Header */
.sheet-header {
  background-color: #1b3664;
  color: #ffffff;
  padding: 18px 28px;
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
  height: 44px;
  width: auto;
  background: rgba(255, 255, 255, 0.15);
  padding: 2px 6px;
  border-radius: 6px;
  object-fit: contain;
}

.header-titles {
  border-left: 1px solid rgba(255, 255, 255, 0.25);
  padding-left: 14px;
}

.header-titles .super-title {
  font-size: 10px;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.7);
  letter-spacing: 1px;
  margin: 0;
  line-height: 1;
}

.header-titles .main-title {
  font-size: 18px;
  font-weight: 800;
  color: #f5c518;
  letter-spacing: 0.5px;
  margin: 4px 0 0 0;
  line-height: 1.1;
  text-transform: uppercase;
}

.student-pill {
  background: #ffffff;
  color: #1b3664;
  font-weight: 700;
  font-size: 12px;
  border-radius: 9999px;
  padding: 7px 22px;
  display: flex;
  align-items: center;
  gap: 10px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.12);
}
.student-pill .pill-name { font-size: 13px; font-weight: 800; }
.student-pill .pill-sep { color: #cbd5e1; }

/* Sheet Body */
.sheet-body {
  padding: 24px;
}

/* Section Title */
.section-title-wrap {
  border-bottom: 2px solid #1b3664;
  padding-bottom: 4px;
  margin-bottom: 16px;
}
.section-title-wrap.flex-between {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.section-title {
  font-size: 15px;
  font-weight: 800;
  color: #1b3664;
  margin: 0;
}

/* Checkboxes */
.test-period-checkboxes {
  display: flex;
  align-items: center;
  gap: 16px;
  font-size: 11px;
  font-weight: 700;
}
.checkbox-label {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  cursor: pointer;
}
.checkbox-label.disabled-label {
  opacity: 0.5;
  cursor: not-allowed;
}
.check-input {
  accent-color: #2563eb;
  width: 14px;
  height: 14px;
}
.check-text {
  color: #334155;
}

/* 2-Column Layout */
.two-col-layout {
  display: flex;
  gap: 20px;
}
.two-col-layout.align-center {
  align-items: center;
}
.two-col-layout.mb-24 { margin-bottom: 24px; }
.two-col-layout.mb-20 { margin-bottom: 20px; }
.two-col-layout.mb-28 { margin-bottom: 28px; }

.col-7 {
  flex: 7;
  min-width: 0;
}
.col-5 {
  flex: 5;
  min-width: 0;
}

/* Tables */
.ucrea-grid-table {
  width: 100%;
  border-collapse: collapse;
  border: 1px solid #cbd5e1;
  font-size: 11px;
}
.ucrea-grid-table td, .ucrea-grid-table th {
  padding: 7px 10px;
  border: 1px solid #e2e8f0;
}
.cell-category-label {
  width: 25%;
  text-align: center;
  font-weight: 700;
  color: #334155;
  background-color: #f8fafc;
}
.cell-category-desc {
  color: #475569;
  line-height: 1.45;
}

.header-row-orange th {
  background-color: #e86036;
  color: #ffffff;
  padding: 8px;
  font-size: 12px;
}
.sub-header-row th {
  background-color: #f1f5f9;
  color: #334155;
  font-weight: 600;
  font-size: 11px;
}

/* Chart Box */
.chart-box {
  width: 100%;
  height: 240px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 6px;
  background: #ffffff;
  position: relative;
}
.chart-overall-box {
  height: 260px;
}
.chart-canvas-el {
  width: 100%;
  height: 100%;
}

/* Score Tables */
.ucrea-score-table {
  width: 100%;
  border-collapse: collapse;
  border: 1px solid #0050a0;
  font-size: 11px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}
.header-row-blue th {
  background-color: #0050a0;
  color: #ffffff;
  font-weight: 700;
  padding: 8px;
  border-right: 1px solid rgba(255, 255, 255, 0.2);
}
.header-row-blue th:last-child { border-right: none; }

.ucrea-score-table td {
  padding: 8px 10px;
  border: 1px solid #e2e8f0;
}
.row-even { background-color: #ffffff; }
.row-odd { background-color: #f4f7fa; }

.cell-skill-name {
  font-weight: 700;
  color: #1e293b;
  padding-left: 12px !important;
}
.cell-skill-desc {
  color: #475569;
  line-height: 1.4;
  padding-left: 12px !important;
}
.cell-skill-grade {
  text-align: center;
  font-weight: 800;
  color: #0f172a;
  font-size: 13px;
}

/* Summary Table */
.ucrea-summary-table {
  width: 100%;
  border-collapse: collapse;
  border: 1px solid #e86036;
  font-size: 11px;
}
.ucrea-summary-table td {
  padding: 8px 10px;
  border: 1px solid #e2e8f0;
}
.row-peach { background-color: #fef6ef; }
.row-white { background-color: #ffffff; }
.grade-label-red { color: #dc3545; font-weight: 700; }
.cell-peer-avg {
  text-align: center;
  color: #64748b;
  font-weight: 600;
}

.page-break-divider {
  height: 12px;
}
.sheet-page-2 {
  margin-top: 20px;
}
.pt-24 { padding-top: 24px; }

/* ============================================================
   PRINT STYLES
   ============================================================ */
@media print {
  body, html, #app, .content-area__content, .layout--vertical-nav-menu, .router-view {
    background: #ffffff !important;
    background-color: #ffffff !important;
    padding: 0 !important;
    margin: 0 !important;
    width: 100% !important;
  }

  header, aside, nav, .print-hide, .vs-navbar, .the-navbar, .vx-navbar, .vs-sidebar, .v-nav-menu {
    display: none !important;
  }

  .ucrea-report-wrapper {
    background: #ffffff !important;
    padding: 0 !important;
    margin: 0 !important;
  }

  .report-document {
    max-width: 100% !important;
    width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
  }

  .report-sheet {
    box-shadow: none !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 0 !important;
    margin: 0 !important;
    page-break-after: always !important;
    break-after: page !important;
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
    margin: 0.8cm 0.6cm;
  }
}
</style>
