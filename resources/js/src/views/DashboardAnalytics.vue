<!-- =========================================================================================
  File Name: DashboardAnalytics.vue
  Description: Dashboard Analytics
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->

<template>
  <div id="dashboard-analytics">
    <div class="vx-row">
      <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
          <statistics-card-line
            v-if="subscribersGained.analyticsData"
            icon="UsersIcon"
            :statistic="subscribersGained.analyticsData.subscribers | k_formatter"
            statisticTitle="Tổng số học sinh hiện tại"
            :chartData="subscribersGained.series"
            type="area" />
      </div>

      <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
          <statistics-card-line
            v-if="revenueGenerated.analyticsData"
            icon="UserPlusIcon"
            :statistic="revenueGenerated.analyticsData.revenue | k_formatter"
            statisticTitle="Số học sinh tuyển mới tháng"
            :chartData="revenueGenerated.series"
            color="success"
            type="area" />
      </div>

      <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
          <statistics-card-line
            v-if="quarterlySales.analyticsData"
            icon="HomeIcon"
            :statistic="quarterlySales.analyticsData.sales"
            statisticTitle="Số học sinh trung bình trên lớp"
            :chartData="quarterlySales.series"
            color="danger"
            type="area" />
      </div>
      <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
          <statistics-card-line
            v-if="ordersRecevied.analyticsData"
            icon="DivideIcon"
            :statistic="ordersRecevied.analyticsData.orders | k_formatter"
            statisticTitle="Tỷ lệ lấp đầy lớp"
            :chartData="ordersRecevied.series"
            color="warning"
            type="area" />
      </div>
      <div class="vx-col w-full md:w-1/3 mb-base">
        <vx-card class="text">
          <h5 class="mb-4">Cơ cấu khóa học</h5>
          <vue-apex-charts type="pie" height="350" :options="pieChart.chartOptions" :series="pieChart.series"></vue-apex-charts>
        </vx-card>
      </div>
      <div class="vx-col w-full md:w-2/3 mb-base">
        <vx-card>
          <h5>Kết quả kinh doanh</h5>
          <vue-apex-charts type="bar" height="350" :options="columnChart.chartOptions" :series="columnChart.series"></vue-apex-charts>
        </vx-card>
      </div>
    </div>
  </div>
</template>

<script>
import StatisticsCardLine from '@/components/statistics-cards/StatisticsCardLine.vue'
import VueApexCharts from 'vue-apexcharts'
export default {
  components: {
    StatisticsCardLine,
    VueApexCharts
  },
  data () {
    return {
      subscribersGained: {
        series: [
          {
            name: 'Học sinh',
            data: [ 3328, 4320, 5361, 4980, 5690, 5789, 6489]
          }
        ],
        analyticsData: {
          subscribers: 6489
        }
      },
      revenueGenerated: {
        series: [
          {
            name: 'Học sinh',
            data: [350, 275, 400, 300, 350, 300, 307]
          }
        ],
        analyticsData: {
          revenue: 307
        }
      },
      quarterlySales: {
        series: [
          {
            name: 'Học sinh',
            data: [10.4, 7.5, 9, 8, 11, 10.6]
          }
        ],
        analyticsData: {
          sales: '10.6'
        }
      },
      ordersRecevied: {
        series: [
          {
            name: 'Tỷ lệ(%)',
            data: [30, 45, 50, 55, 67, 58, 59]
          }
        ],
        analyticsData: {
          orders: '59%'
        }
      },
      pieChart: {
        series: [440, 550, 130, 430],
        chartOptions: {
          labels: ['Khóa học A', 'Khóa học B', 'Khóa học C', 'Khóa học D'],
          colors: ['#7367F0', '#28C76F', '#EA5455', '#FF9F43', '#1E1E1E'],
          responsive: [{
            breakpoint: 480,
            options: {
              chart: {
                width: 300
              },
              legend: {
                position: 'bottom'
              }
            }
          }]
        }
      },
      columnChart: {
        series: [{
            name: 'Lãi/Lỗ',
            data: [ 560, 610, 580, 630, 600, 660]
          }, {
            name: 'Doanh thu',
            data: [ 980, 870, 1050, 910, 1140, 940]
          }, {
            name: 'Chi phí',
            data: [ 420, 260, 470, 280, 540, 220]
        }],
        chartOptions: {
          colors: ['#7367F0', '#28C76F', '#EA5455', '#FF9F43', '#1E1E1E'],
          plotOptions: {
            bar: {
              horizontal: false,
              endingShape: 'rounded',
              columnWidth: '55%',
            },
          },
          dataLabels: {
            enabled: false
          },
          stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
          },

          xaxis: {
            categories: ['T12/2023', 'T1/2024', 'T2/2024', 'T3/2024', 'T4/2024', 'T5/2024'],
          },
          yaxis: {
            title: {
              text: 'VND (triệu)'
            }
          },
          fill: {
            opacity: 1

          },
          tooltip: {
            y: {
              formatter: function(val) {
                return  val + " triệu đồng"
              }
            }
          }
        }
      }
    }
  },
  created () {
  }
}
</script>

<style lang="scss">
/*! rtl:begin:ignore */
#dashboard-analytics {
  .greet-user{
    position: relative;

    .decore-left{
      position: absolute;
      left:0;
      top: 0;
    }
    .decore-right{
      position: absolute;
      right:0;
      top: 0;
    }
  }

  @media(max-width: 576px) {
    .decore-left, .decore-right{
      width: 140px;
    }
  }
}
/*! rtl:end:ignore */
</style>
