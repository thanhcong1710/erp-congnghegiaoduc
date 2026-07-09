<template>
  <div id="loyalty-dashboard">
    <div class="flex justify-between items-center mb-5">
      <h2 class="font-bold text-2xl text-slate-800">Tổng quan Loyalty</h2>
      <div class="flex items-center gap-4">
        <vs-button type="filled" icon="icon-calendar" icon-pack="feather">01/01/2026 - 30/01/2026</vs-button>
        <vs-button type="border" icon="icon-download" icon-pack="feather">Xuất báo cáo</vs-button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="vx-row">
      <div class="vx-col w-full sm:w-1/3 mb-base">
        <vx-card>
          <div class="flex items-center justify-between">
            <div>
              <p class="text-slate-500 font-medium mb-1">Tổng điểm đã cấp</p>
              <h2 class="text-3xl font-bold text-primary">15,420</h2>
              <p class="text-success text-sm mt-1 flex items-center">
                <feather-icon icon="TrendingUpIcon" svgClasses="w-4 h-4 mr-1"></feather-icon> 
                <span>+15% so với tháng trước</span>
              </p>
            </div>
            <div class="p-4 rounded-xl" style="background: rgba(var(--vs-primary),0.1)">
              <feather-icon icon="AwardIcon" class="text-primary" svgClasses="w-8 h-8"></feather-icon>
            </div>
          </div>
        </vx-card>
      </div>
      <div class="vx-col w-full sm:w-1/3 mb-base">
        <vx-card>
          <div class="flex items-center justify-between">
            <div>
              <p class="text-slate-500 font-medium mb-1">Điểm đã sử dụng</p>
              <h2 class="text-3xl font-bold text-danger">5,710</h2>
              <p class="text-danger text-sm mt-1 flex items-center">
                <feather-icon icon="TrendingDownIcon" svgClasses="w-4 h-4 mr-1"></feather-icon> 
                <span>-2.4% so với tháng trước</span>
              </p>
            </div>
            <div class="p-4 rounded-xl" style="background: rgba(var(--vs-danger),0.1)">
              <feather-icon icon="ShoppingCartIcon" class="text-danger" svgClasses="w-8 h-8"></feather-icon>
            </div>
          </div>
        </vx-card>
      </div>
      <div class="vx-col w-full sm:w-1/3 mb-base">
        <vx-card>
          <div class="flex items-center justify-between">
            <div>
              <p class="text-slate-500 font-medium mb-1">Điểm đã hết hạn</p>
              <h2 class="text-3xl font-bold text-warning">87,400</h2>
              <p class="text-success text-sm mt-1 flex items-center">
                <feather-icon icon="TrendingUpIcon" svgClasses="w-4 h-4 mr-1"></feather-icon> 
                <span>+5.1% so với tháng trước</span>
              </p>
            </div>
            <div class="p-4 rounded-xl" style="background: rgba(var(--vs-warning),0.1)">
              <feather-icon icon="ClockIcon" class="text-warning" svgClasses="w-8 h-8"></feather-icon>
            </div>
          </div>
        </vx-card>
      </div>
    </div>

    <!-- Charts -->
    <div class="vx-row">
      <div class="vx-col w-full md:w-2/3 mb-base">
        <vx-card title="Biểu đồ điểm Loyalty">
          <vue-apex-charts type="area" height="300" :options="areaChartOptions" :series="areaChartSeries"></vue-apex-charts>
        </vx-card>
      </div>
      <div class="vx-col w-full md:w-1/3 mb-base">
        <vx-card title="Phân loại nguồn điểm">
          <vue-apex-charts type="donut" height="300" :options="donutChartOptions" :series="donutChartSeries"></vue-apex-charts>
        </vx-card>
      </div>
    </div>

    <!-- Bar Charts (Rankings) -->
    <div class="vx-row">
      <div class="vx-col w-full md:w-1/2 mb-base">
        <vx-card title="Top 5 cơ sở có điểm cao nhất">
          <vue-apex-charts type="bar" height="300" :options="barChartOptions" :series="barChartSeries"></vue-apex-charts>
        </vx-card>
      </div>
      <div class="vx-col w-full md:w-1/2 mb-base">
        <vx-card title="Xếp hạng học sinh">
          <vue-apex-charts type="bar" height="300" :options="horizontalBarChartOptions" :series="horizontalBarChartSeries"></vue-apex-charts>
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
      areaChartSeries: [{
        name: 'Điểm cấp',
        data: [31, 40, 28, 51, 42, 109, 100]
      }, {
        name: 'Điểm sử dụng',
        data: [11, 32, 45, 32, 34, 52, 41]
      }],
      areaChartOptions: {
        chart: {
          type: 'area',
          toolbar: { show: false }
        },
        colors: ['#7367F0', '#28C76F'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth' },
        xaxis: {
          categories: ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'],
        }
      },
      donutChartSeries: [44, 55, 13],
      donutChartOptions: {
        chart: { type: 'donut' },
        labels: ['Học phí', 'Check-in', 'Khác'],
        colors: ['#7367F0', '#28C76F', '#FF9F43'],
        legend: { position: 'bottom' }
      },
      barChartSeries: [{
        name: 'Điểm',
        data: [400, 430, 448, 470, 540]
      }],
      barChartOptions: {
        chart: { type: 'bar', toolbar: { show: false } },
        colors: ['#7367F0'],
        plotOptions: {
          bar: { borderRadius: 4, columnWidth: '50%' }
        },
        dataLabels: { enabled: false },
        xaxis: {
          categories: ['CS1', 'CS2', 'CS3', 'CS4', 'CS5'],
        }
      },
      horizontalBarChartSeries: [{
        name: 'Điểm',
        data: [1200, 1100, 950, 800, 750]
      }],
      horizontalBarChartOptions: {
        chart: { type: 'bar', toolbar: { show: false } },
        colors: ['#FF9F43'],
        plotOptions: {
          bar: { borderRadius: 4, horizontal: true }
        },
        dataLabels: { enabled: false },
        xaxis: {
          categories: ['Nguyễn Văn A', 'Trần Thị B', 'Lê Văn C', 'Phạm Thị D', 'Hoàng Văn E'],
        }
      }
    }
  }
}
</script>

<style lang="scss" scoped>
#loyalty-dashboard {
  .vx-card {
    border-radius: 1rem;
    box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.05);
  }
}
</style>
