<template>
  <div id="rate-csat">
    <div class="mb-6 flex justify-between items-center">
      <div>
        <h2 class="text-2xl font-bold text-slate-800">Báo cáo CSAT (Customer Satisfaction)</h2>
        <p class="text-slate-500">Bảng thống kê mức độ hài lòng của khách hàng và cảnh báo tiêu cực.</p>
      </div>
      <vs-button type="filled" icon-pack="feather" icon="icon-calendar">Tháng 8 / 2026</vs-button>
    </div>

    <!-- Tổng quan CSAT -->
    <div class="vx-row mb-base">
      <div class="vx-col w-full sm:w-1/2 md:w-1/4 mb-base">
        <vx-card>
          <p class="text-slate-500 font-medium mb-1">Điểm CSAT Trung bình</p>
          <h2 class="text-3xl font-bold text-primary flex items-center">
            4.6 <feather-icon icon="StarIcon" svgClasses="w-6 h-6 ml-2 text-warning fill-current"></feather-icon>
          </h2>
          <p class="text-success text-sm mt-1">▲ Tăng 0.2 so với tháng trước</p>
        </vx-card>
      </div>
      <div class="vx-col w-full sm:w-1/2 md:w-1/4 mb-base">
        <vx-card>
          <p class="text-slate-500 font-medium mb-1">Tổng lượt đánh giá</p>
          <h2 class="text-3xl font-bold text-slate-800">1,245</h2>
          <p class="text-success text-sm mt-1">▲ +15% so với tháng trước</p>
        </vx-card>
      </div>
      <div class="vx-col w-full sm:w-1/2 md:w-1/4 mb-base">
        <vx-card>
          <p class="text-slate-500 font-medium mb-1">Đánh giá 5 Sao</p>
          <h2 class="text-3xl font-bold text-success">980</h2>
          <p class="text-slate-500 text-sm mt-1">Chiếm 78.7% tổng số</p>
        </vx-card>
      </div>
      <div class="vx-col w-full sm:w-1/2 md:w-1/4 mb-base">
        <vx-card>
          <p class="text-slate-500 font-medium mb-1 text-danger">Cảnh báo Tiêu cực (1-3 Sao)</p>
          <h2 class="text-3xl font-bold text-danger">15</h2>
          <p class="text-danger text-sm mt-1">▼ Cần liên hệ CSKH ngay</p>
        </vx-card>
      </div>
    </div>

    <!-- Charts -->
    <div class="vx-row">
      <div class="vx-col w-full md:w-2/3 mb-base">
        <vx-card title="Phân bổ đánh giá theo Tuần">
          <vue-apex-charts type="bar" height="300" :options="barChartOptions" :series="barChartSeries"></vue-apex-charts>
        </vx-card>
      </div>
      <div class="vx-col w-full md:w-1/3 mb-base">
        <vx-card title="Tỉ lệ hài lòng">
          <vue-apex-charts type="donut" height="300" :options="donutChartOptions" :series="donutChartSeries"></vue-apex-charts>
        </vx-card>
      </div>
    </div>

  </div>
</template>

<script>
import VueApexCharts from 'vue-apexcharts'

export default {
  components: {
    VueApexCharts
  },
  data() {
    return {
      barChartSeries: [
        { name: '5 Sao', data: [120, 150, 140, 180] },
        { name: '4 Sao', data: [30, 40, 35, 50] },
        { name: '1-3 Sao', data: [5, 2, 8, 3] }
      ],
      barChartOptions: {
        chart: { type: 'bar', stacked: true, toolbar: { show: false } },
        colors: ['#28C76F', '#7367F0', '#EA5455'],
        plotOptions: { bar: { columnWidth: '40%', borderRadius: 4 } },
        xaxis: { categories: ['Tuần 1', 'Tuần 2', 'Tuần 3', 'Tuần 4'] },
        legend: { position: 'top' },
        dataLabels: { enabled: false }
      },
      donutChartSeries: [78.7, 18.5, 2.8],
      donutChartOptions: {
        chart: { type: 'donut' },
        labels: ['Rất hài lòng (5*)', 'Hài lòng (4*)', 'Chưa hài lòng (1-3*)'],
        colors: ['#28C76F', '#7367F0', '#EA5455'],
        legend: { position: 'bottom' }
      }
    }
  }
}
</script>
