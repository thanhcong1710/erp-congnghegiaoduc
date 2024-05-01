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
            statisticTitle="Subscribers Gained"
            :chartData="subscribersGained.series"
            type="area" />
      </div>

      <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
          <statistics-card-line
            v-if="revenueGenerated.analyticsData"
            icon="DollarSignIcon"
            :statistic="revenueGenerated.analyticsData.revenue | k_formatter"
            statisticTitle="Revenue Generated"
            :chartData="revenueGenerated.series"
            color="success"
            type="area" />
      </div>

      <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
          <statistics-card-line
            v-if="quarterlySales.analyticsData"
            icon="ShoppingCartIcon"
            :statistic="quarterlySales.analyticsData.sales"
            statisticTitle="Quarterly Sales"
            :chartData="quarterlySales.series"
            color="danger"
            type="area" />
      </div>
      <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
          <statistics-card-line
            v-if="ordersRecevied.analyticsData"
            icon="ShoppingBagIcon"
            :statistic="ordersRecevied.analyticsData.orders | k_formatter"
            statisticTitle="Orders Received"
            :chartData="ordersRecevied.series"
            color="warning"
            type="area" />
      </div>
      <div class="vx-col w-full md:w-1/3 mb-base">
        <vx-card>
          <vue-apex-charts type="pie" height="350" :options="pieChart.chartOptions" :series="pieChart.series"></vue-apex-charts>
        </vx-card>
      </div>
      <div class="vx-col w-full md:w-2/3 mb-base">
        <vx-card>
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
            name: 'Subscribers',
            data: [28, 40, 36, 52, 38, 60, 55]
          }
        ],
        analyticsData: {
          subscribers: 92600
        }
      },
      revenueGenerated: {
        series: [
          {
            name: 'Revenue',
            data: [350, 275, 400, 300, 350, 300, 450]
          }
        ],
        analyticsData: {
          revenue: 97500
        }
      },
      quarterlySales: {
        series: [
          {
            name: 'Sales',
            data: [10, 15, 7, 12, 3, 16]
          }
        ],
        analyticsData: {
          sales: '36%'
        }
      },
      ordersRecevied: {
        series: [
          {
            name: 'Orders',
            data: [10, 15, 8, 15, 7, 12, 8]
          }
        ],
        analyticsData: {
          orders: 97500
        }
      },
      pieChart: {
        series: [44, 55, 13, 43],
        chartOptions: {
          labels: ['Team A', 'Team B', 'Team C', 'Team D'],
          colors: ['#7367F0', '#28C76F', '#EA5455', '#FF9F43', '#1E1E1E'],
          responsive: [{
            breakpoint: 480,
            options: {
              chart: {
                width: 200
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
            name: 'Net Profit',
            data: [44, 55, 57, 56, 61, 58, 63, 60, 66]
          }, {
            name: 'Revenue',
            data: [76, 85, 101, 98, 87, 105, 91, 114, 94]
          }, {
            name: 'Free Cash Flow',
            data: [35, 41, 36, 26, 45, 48, 52, 53, 41]
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
            categories: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
          },
          yaxis: {
            title: {
              text: '$ (thousands)'
            }
          },
          fill: {
            opacity: 1

          },
          tooltip: {
            y: {
              formatter: function(val) {
                return "$ " + val + " thousands"
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
