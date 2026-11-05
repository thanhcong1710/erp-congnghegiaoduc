<template>
  <div class="summative-report-wrapper">
    <!-- Action buttons (Hidden on print) -->
    <div class="action-toolbar print-hide">
      <div class="toolbar-info">
        <router-link to="/lms/evaluations/igbh/summative" class="back-link">
          <i class="feather icon-arrow-left"></i> Quay lại danh sách lớp
        </router-link>
        <h2 class="toolbar-title">Báo Cáo Đánh Giá Tổng Kết 12 Tuần (IG.BH)</h2>
        <div class="toolbar-sub" v-if="reportData && reportData.result">
          <span>Học sinh: <strong class="text-name">{{ reportData.result.stu_nm }}</strong></span>
          <span class="sep">|</span>
          <span>Lớp: {{ reportData.result.class_nm || '-' }}</span>
        </div>
      </div>
      <div class="toolbar-btns" v-if="reportData">
        <button class="btn-print" @click="printReport">
          <i class="feather icon-printer"></i> In Báo Cáo (PDF)
        </button>
        <router-link :to="`/lms/evaluations/igbh/summative/form/${reportData.result.id}`" class="btn-edit">
          <i class="feather icon-edit"></i> Chấm Điểm Cuối Kỳ
        </router-link>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="loading-state print-hide">
      <div class="spinner"></div>
      <p>Đang tải dữ liệu báo cáo...</p>
    </div>

    <!-- Main Report Document -->
    <div v-else-if="reportData" id="summative-report-doc" class="report-document">
      <div class="report-sheet">
        <!-- Header Banner -->
        <div class="sheet-header">
          <div class="header-brand-block">
            <img src="@assets/images/logo/stemkey-logo.png" alt="STEMKEY" class="brand-logo" />
            <div class="header-titles">
              <h1 class="main-title">ĐÁNH GIÁ CUỐI KỲ IG.BH</h1>
              <p class="sub-title">BÁO CÁO NĂNG LỰC TOÀN DIỆN 12 TUẦN</p>
            </div>
          </div>
          <div class="student-pill">
            <span class="pill-name">{{ reportData.result.stu_nm }}</span>
            <span class="pill-sep">|</span>
            <span>{{ reportData.result.class_nm || '-' }}</span>
            <span class="pill-sep">|</span>
            <span>{{ reportData.result.date_of_birth || '-' }}</span>
          </div>
        </div>

        <div class="sheet-body">
          <!-- Section 1: Sách bài tập 12 tuần -->
          <div class="report-block">
            <div class="block-title">
              <span class="title-bar"></span>
              <h3>1. Tiến trình làm bài tập 12 tuần</h3>
            </div>
            <div class="table-container">
              <table class="grid-table text-center">
                <thead>
                  <tr class="header-row-yellow">
                    <th style="width: 14%;">Tuần</th>
                    <th v-for="w in 12" :key="w" class="col-week">T.{{ w }}</th>
                    <th style="width: 14%;">Tổng điểm</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="cell-label-yellow font-bold">Bài tập</td>
                    <td v-for="(w, idx) in reportData.report_data" :key="idx" class="cell-week-val">
                      {{ w.weekly_workbook !== null ? w.weekly_workbook : '-' }}
                    </td>
                    <td class="cell-sum-red font-bold">
                      {{ reportData.total_workbook }} / {{ reportData.max_workbook }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Section 2: Radar Charts (Thái độ & Quan sát) -->
          <div class="report-block">
            <div class="block-title">
              <span class="title-bar"></span>
              <h3>2. Biểu đồ đánh giá thái độ & năng lực quan sát</h3>
            </div>
            <div class="two-col-layout">
              <div class="col-half">
                <div class="chart-panel-box">
                  <h4 class="chart-box-title">Thái độ học tập trên lớp</h4>
                  <div ref="chartAttitude" class="chart-canvas-box"></div>
                  <div class="chart-summary-line">
                    Lắng nghe: <strong>{{ reportData.avg_attitude.listen }}</strong> |
                    Tham gia: <strong>{{ reportData.avg_attitude.join }}</strong> |
                    Thể hiện: <strong>{{ reportData.avg_attitude.express }}</strong> |
                    Hợp tác: <strong>{{ reportData.avg_attitude.coop }}</strong>
                  </div>
                </div>
              </div>

              <div class="col-half">
                <div class="chart-panel-box">
                  <h4 class="chart-box-title">Năng lực quan sát & tư duy</h4>
                  <div ref="chartDetection" class="chart-canvas-box"></div>
                  <div class="chart-summary-line">
                    Cơ bản: <strong>{{ reportData.avg_detection.normal }}</strong> |
                    Lãnh đạo: <strong>{{ reportData.avg_detection.leadersh }}</strong> |
                    Toán học: <strong>{{ reportData.avg_detection.math }}</strong> |
                    Sáng tạo: <strong>{{ reportData.avg_detection.creative }}</strong>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Section 3: Điểm thi tự luận cuối kỳ -->
          <div class="report-block">
            <div class="block-title">
              <span class="title-bar"></span>
              <h3>3. Kết quả bài kiểm tra tự luận cuối kỳ</h3>
            </div>
            <div class="table-container">
              <table class="grid-table text-center">
                <thead>
                  <tr class="header-row-yellow">
                    <th>Câu hỏi</th>
                    <th>Khái niệm</th>
                    <th>Chiến lược</th>
                    <th>Tính toán</th>
                    <th>Diễn đạt</th>
                    <th>Điểm đạt</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(sub, idx) in reportData.subjective_details" :key="idx">
                    <td class="font-bold">Câu {{ sub.sort_no }}</td>
                    <td>{{ sub.concept || 0 }}</td>
                    <td>{{ sub.strategy || 0 }}</td>
                    <td>{{ sub.calculation || 0 }}</td>
                    <td>{{ sub.expression || 0 }}</td>
                    <td class="font-bold text-score-blue">{{ sub.score }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Section 4: Nhận xét của giáo viên -->
          <div class="comment-box mb-24">
            <h4 class="comment-box-title">Nhận xét của giáo viên phụ trách</h4>
            <p class="comment-box-text">
              {{ teacherComment || 'Học sinh có tinh thần học tập tốt, hoàn thành đầy đủ bài tập và tích cực tham gia các hoạt động tư duy trong suốt 12 tuần.' }}
            </p>
          </div>

          <!-- Signatures -->
          <div class="signatures-row">
            <div class="sig-col">
              <p class="sig-title">Giáo viên phụ trách</p>
              <div class="sig-space"></div>
              <p class="sig-name">{{ reportData.result.teacher_name || 'Giáo viên' }}</p>
            </div>
            <div class="sig-col">
              <p class="sig-title">Trung tâm STEMKEY</p>
              <div class="sig-space"></div>
              <p class="sig-name">Ban Đào Tạo & Chuyên Môn</p>
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
  name: 'IgbhSummativeReport',
  data() {
    return {
      reportData: null,
      loading: true,
      teacherComment: ''
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
        if (res && res.data && res.data.status === 'success') {
          this.reportData = res.data.data
          this.teacherComment = this.reportData.result.teacher_comment || ''
          this.$nextTick(() => {
            this.initCharts()
          })
        }
      }).catch(err => {
        this.loading = false
      })
    },
    initCharts() {
      if (!this.reportData) return

      // Chart 1: Thái độ
      if (this.$refs.chartAttitude) {
        const c1 = echarts.getInstanceByDom(this.$refs.chartAttitude) || echarts.init(this.$refs.chartAttitude)
        const att = this.reportData.avg_attitude
        c1.setOption({
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
              areaStyle: { color: 'rgba(234, 88, 12, 0.25)' },
              itemStyle: { color: '#ea580c' }
            }]
          }]
        })
      }

      // Chart 2: Phát hiện
      if (this.$refs.chartDetection) {
        const c2 = echarts.getInstanceByDom(this.$refs.chartDetection) || echarts.init(this.$refs.chartDetection)
        const det = this.reportData.avg_detection
        c2.setOption({
          radar: {
            indicator: [
              { name: 'Cơ bản', max: 5 },
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
              areaStyle: { color: 'rgba(217, 119, 6, 0.25)' },
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
/* ============================================================
   SUMMATIVE REPORT INDEPENDENT CSS
   ============================================================ */

.summative-report-wrapper {
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
  color: #d97706;
  border: 1px solid #fde68a;
  border-radius: 8px;
  padding: 8px 16px;
  font-size: 13px;
  font-weight: 600;
  text-decoration: none;
}
.btn-edit:hover { background: #fffbeb; }

/* Loading */
.loading-state { text-align: center; padding: 80px 0; color: #64748b; }
.spinner {
  width: 36px;
  height: 36px;
  border: 3px solid #e2e8f0;
  border-top-color: #f59e0b;
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
  background-color: #f59e0b;
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
  background: rgba(255, 255, 255, 0.2);
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
  color: #fef3c7;
  margin: 2px 0 0 0;
  letter-spacing: 0.8px;
}

.student-pill {
  background: #ffffff;
  color: #92400e;
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

.report-block {
  margin-bottom: 24px;
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
  background-color: #d97706;
  border-radius: 3px;
  display: inline-block;
}
.block-title h3 {
  font-size: 14px;
  font-weight: 700;
  color: #92400e;
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
  padding: 8px;
  border: 1px solid #e2e8f0;
}
.grid-table td {
  padding: 8px;
  border: 1px solid #e2e8f0;
}

.header-row-yellow th {
  background-color: #fef3c7;
  color: #92400e;
  font-weight: 700;
}

.cell-label-yellow {
  background-color: #fef9c3;
  color: #854d0e;
}
.cell-week-val {
  color: #334155;
}
.cell-sum-red {
  background-color: #fef2f2;
  color: #ef4444;
}
.text-score-blue {
  color: #2563eb;
}

/* 2-Columns */
.two-col-layout {
  display: flex;
  gap: 20px;
}
.col-half {
  flex: 1;
  min-width: 0;
}

.chart-panel-box {
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  padding: 16px;
  background: #ffffff;
  text-align: center;
}
.chart-box-title {
  font-size: 13px;
  font-weight: 700;
  color: #1e3a8a;
  margin: 0 0 8px 0;
}
.chart-canvas-box {
  width: 100%;
  height: 230px;
}
.chart-summary-line {
  font-size: 11px;
  color: #64748b;
  margin-top: 10px;
}

/* Comment Box */
.comment-box {
  background-color: #f8fafc;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  padding: 16px 20px;
}
.comment-box.mb-24 { margin-bottom: 24px; }
.comment-box-title {
  font-size: 13px;
  font-weight: 700;
  color: #1e293b;
  margin: 0 0 6px 0;
  text-transform: uppercase;
}
.comment-box-text {
  font-size: 13px;
  color: #475569;
  font-style: italic;
  line-height: 1.6;
  margin: 0;
}

/* Signatures */
.signatures-row {
  display: flex;
  justify-content: space-around;
  text-align: center;
  padding-top: 16px;
  border-top: 1px solid #e2e8f0;
}
.sig-col {
  width: 200px;
}
.sig-title {
  font-size: 12px;
  font-weight: 700;
  color: #475569;
  text-transform: uppercase;
  margin: 0;
}
.sig-space {
  height: 60px;
}
.sig-name {
  font-size: 12px;
  font-weight: 600;
  color: #1e293b;
  margin: 0;
}

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

  .summative-report-wrapper {
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
