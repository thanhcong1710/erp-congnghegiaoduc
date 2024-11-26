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
      <div class="vx-col sm:w-1/3 w-full mb-4">
        <multiselect
            name="search_branch"
            placeholder="Chọn trung tâm để hiển thị dữ liệu"
            v-model="searchData.arr_branch"
            :options="branch_list"
            label="name"
            :close-on-select="false"
            :hide-selected="true"
            :multiple="true"
            :searchable="true"
            track-by="id"
            selectedLabel="" selectLabel="" deselectLabel=""
          >
            <span slot="noResult">Không tìm thấy dữ liệu</span>
          </multiselect>
      </div>
    </div>
    <div class="vx-row">
      <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
          <statistics-card-line
            v-if="subscribersGained.analyticsData"
            icon="UsersIcon"
            :statistic="subscribersGained.analyticsData.subscribers | k_formatter"
            statisticTitle="HỌC VIÊN ĐANG HỌC"
            :chartData="subscribersGained.series"
            type="area" />
      </div>

      <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
          <statistics-card-line
            v-if="revenueGenerated.analyticsData"
            icon="FileIcon"
            :statistic="revenueGenerated.analyticsData.revenue | k_formatter"
            statisticTitle="TỔNG SỐ LỚP"
            :chartData="revenueGenerated.series"
            color="success"
            type="area" />
      </div>

      <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
          <statistics-card-line
            v-if="quarterlySales.analyticsData"
            icon="UserIcon"
            :statistic="quarterlySales.analyticsData.sales"
            statisticTitle="TỔNG SỐ NHÂN SỰ"
            :chartData="quarterlySales.series"
            color="danger"
            type="area" />
      </div>
      <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
          <statistics-card-line
            v-if="ordersRecevied.analyticsData"
            icon="LayersIcon"
            :statistic="ordersRecevied.analyticsData.orders | k_formatter"
            statisticTitle="CÔNG VIỆC CẦN THỰC HIỆN"
            :chartData="ordersRecevied.series"
            color="warning"
            type="area" />
      </div>
      <div class="vx-col w-full md:w-1/2 mb-base">
        <vx-card class="text">
          <h5 class="mb-4">Biểu đồ tình hình học viên vào ra</h5>
          <vue-apex-charts type="pie" :options="pieChart.chartOptions" :series="pieChart.series"></vue-apex-charts>
        </vx-card>
      </div>
      <div class="vx-col w-full md:w-1/2 mb-base">
        <vx-card class="text">
          <h5 class="mb-4">Biểu đồ đăng ký khóa học</h5>
          <vue-apex-charts type="pie" :options="pieChart1.chartOptions" :series="pieChart1.series"></vue-apex-charts>
        </vx-card>
      </div>
      <div class="vx-col w-full md:w-1/2 mb-base">
        <vx-card>
          <h5>Biểu đồ đăng ký mới</h5>
          <vue-apex-charts type="bar" :options="columnChart.chartOptions" :series="columnChart.series"></vue-apex-charts>
        </vx-card>
      </div>
      <div class="vx-col w-full md:w-1/2 mb-base">
        <vx-card>
          <h5>Biểu đồ gia hạn</h5>
          <vue-apex-charts type="bar" :options="columnChart1.chartOptions" :series="columnChart1.series"></vue-apex-charts>
        </vx-card>
      </div>

      <div class="vx-col w-full md:w-1/2 mb-base">
        <vx-card title="Học viên đăng ký mới" height="385">
          <div slot="no-body" class="mt-4">
            <div class="vs-component vs-con-table stripe vs-table-primary">
              <div class="con-tablex vs-table--content">
                <div class="vs-con-tbody vs-table--tbody ">
                  <table class="vs-table vs-table--tbody-table">
                    <thead class="vs-table--thead">
                      <tr>
                        <th colspan="1" rowspan="1" class="text-center">STT</th>
                        <th colspan="1" rowspan="1">Đăng ký</th>
                        <th colspan="1" rowspan="1">Học viên</th>
                        <th colspan="1" rowspan="1">Khóa học</th>
                        <th colspan="1" rowspan="1">Gói phí</th>
                      </tr>
                    </thead>
                    <tr class="tr-values vs-table--tr tr-table-state-null">
                      <td class="td vs-table--td text-center">1</td>
                      <td class="td vs-table--td">20/11/2024</td>
                      <td class="td vs-table--td">Nguyễn Văn A</td>
                      <td class="td vs-table--td">KIDS 100% GVNN</td>
                      <td class="td vs-table--td">24.000.000</td>
                    </tr>
                    <tr class="tr-values vs-table--tr tr-table-state-null">
                      <td class="td vs-table--td text-center">2</td>
                      <td class="td vs-table--td">20/11/2024</td>
                      <td class="td vs-table--td">Nguyễn Văn B</td>
                      <td class="td vs-table--td">KIDS 50% GVNN</td>
                      <td class="td vs-table--td">18.000.000</td>
                    </tr>
                    <tr class="tr-values vs-table--tr tr-table-state-null">
                      <td class="td vs-table--td text-center">3</td>
                      <td class="td vs-table--td">20/11/2024</td>
                      <td class="td vs-table--td">Nguyễn Văn C</td>
                      <td class="td vs-table--td">KIDS 50% GVNN</td>
                      <td class="td vs-table--td">18.000.000</td>
                    </tr>
                    <tr class="tr-values vs-table--tr tr-table-state-null">
                      <td class="td vs-table--td text-center">4</td>
                      <td class="td vs-table--td">20/11/2024</td>
                      <td class="td vs-table--td">Nguyễn Văn D</td>
                      <td class="td vs-table--td">KIDS 100% GVNN</td>
                      <td class="td vs-table--td">24.000.000</td>
                    </tr>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </vx-card>
      </div>
      <div class="vx-col w-full md:w-1/2 mb-base">
        <vx-card title="Học viên gia hạn mới" height="385">
          <div slot="no-body" class="mt-4">
            <div class="vs-component vs-con-table stripe vs-table-primary">
              <div class="con-tablex vs-table--content">
                <div class="vs-con-tbody vs-table--tbody ">
                  <table class="vs-table vs-table--tbody-table">
                    <thead class="vs-table--thead">
                      <tr>
                        <th colspan="1" rowspan="1" class="text-center">STT</th>
                        <th colspan="1" rowspan="1">Đăng ký</th>
                        <th colspan="1" rowspan="1">Học viên</th>
                        <th colspan="1" rowspan="1">Khóa học</th>
                        <th colspan="1" rowspan="1">Gói phí</th>
                      </tr>
                    </thead>
                    <tr class="tr-values vs-table--tr tr-table-state-null">
                      <td class="td vs-table--td text-center">1</td>
                      <td class="td vs-table--td">20/11/2024</td>
                      <td class="td vs-table--td">Nguyễn Văn A</td>
                      <td class="td vs-table--td">KIDS 100% GVNN</td>
                      <td class="td vs-table--td">24.000.000</td>
                    </tr>
                    <tr class="tr-values vs-table--tr tr-table-state-null">
                      <td class="td vs-table--td text-center">2</td>
                      <td class="td vs-table--td">20/11/2024</td>
                      <td class="td vs-table--td">Nguyễn Văn B</td>
                      <td class="td vs-table--td">KIDS 50% GVNN</td>
                      <td class="td vs-table--td">18.000.000</td>
                    </tr>
                    <tr class="tr-values vs-table--tr tr-table-state-null">
                      <td class="td vs-table--td text-center">3</td>
                      <td class="td vs-table--td">20/11/2024</td>
                      <td class="td vs-table--td">Nguyễn Văn C</td>
                      <td class="td vs-table--td">KIDS 50% GVNN</td>
                      <td class="td vs-table--td">18.000.000</td>
                    </tr>
                    <tr class="tr-values vs-table--tr tr-table-state-null">
                      <td class="td vs-table--td text-center">4</td>
                      <td class="td vs-table--td">20/11/2024</td>
                      <td class="td vs-table--td">Nguyễn Văn D</td>
                      <td class="td vs-table--td">KIDS 100% GVNN</td>
                      <td class="td vs-table--td">24.000.000</td>
                    </tr>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </vx-card>
      </div>
    </div>
  </div>
</template>

<script>
import Multiselect from "vue-multiselect";
import StatisticsCardLine from '@/components/statistics-cards/StatisticsCardLine.vue'
import axios from '../http/axios.js'
import VueApexCharts from 'vue-apexcharts'
export default {
  components: {
    StatisticsCardLine,
    VueApexCharts,
    Multiselect,
  },
  data () {
    return {
      branch_list: [],
      searchData: {
        branch_id:"",
      },
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
            name: 'Lớp',
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
            name: 'Nhân sự',
            data: [50, 56, 67, 60, 77, 76]
          }
        ],
        analyticsData: {
          sales: '76'
        }
      },
      ordersRecevied: {
        series: [
          {
            name: 'Công việc',
            data: [30, 45, 50, 55, 67, 58, 59]
          }
        ],
        analyticsData: {
          orders: '59'
        }
      },
      pieChart: {
        series: [440, 350, 130],
        chartOptions: {
          labels: ['Đăng ký mới', 'Gia hạn', 'Bảolưu'], 
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
      pieChart1: {
        series: [50, 35, 65, 70, 75],
        chartOptions: {
          labels: ['KIDS 100% GVNN', 'KIDS 50% GVNN', 'KIDS 4A', 'KINDY 6 tháng' , 'KINDY 12 tháng'], 
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
            name: 'Đăng ký mới',
            data: [ 80, 110, 180, 130, 200, 260]
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
            categories: ['T6/2023', 'T7/2024', 'T8/2024', 'T9/2024', 'T10/2024', 'T11/2024'],
          },
          yaxis: {
            title: {
              text: 'Số lượng'
            }
          },
          fill: {
            opacity: 1

          },
          tooltip: {
            y: {
              formatter: function(val) {
                return  val + " học sinh"
              }
            }
          }
        }
      },
      columnChart1: {
        series: [{
            name: 'Gia hạn',
            data: [ 56, 61, 80, 130, 100, 160]
          }],
        chartOptions: {
          colors: [ '#FF9F43', '#1E1E1E'],
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
            categories: ['T6/2023', 'T7/2024', 'T8/2024', 'T9/2024', 'T10/2024', 'T11/2024'],
          },
          yaxis: {
            title: {
              text: 'Số lượng'
            }
          },
          fill: {
            opacity: 1

          },
          tooltip: {
            y: {
              formatter: function(val) {
                return  val + " học sinh"
              }
            }
          }
        }
      },
      dispatchedOrders:[]
    }
  },
  created () {
    axios.g(`/api/dashboard`)
      .then(response => {
        console.log(response.data)
    }),
    axios.g(`/api/system/branches-has-user`)
      .then(response => {
      this.branch_list = response.data
    })
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
