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
      <div class="vx-col sm:w-1/2 w-full mb-4">
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
            @input="loadData"
          >
            <span slot="noResult">Không tìm thấy dữ liệu</span>
          </multiselect>
      </div>
    </div>
    <div class="vx-row">
      <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
          <statistics-card-line
            ref="apexChart"
            v-if="numDashStudent.analyticsData"
            icon="UsersIcon"
            :statistic="numDashStudent.analyticsData.data | formatNumber"
            statisticTitle="HỌC VIÊN ĐANG HỌC"
            :chartData="numDashStudent.series"
            type="area" />
      </div>

      <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
          <statistics-card-line
            v-if="numDashClass.analyticsData"
            icon="FileIcon"
            :statistic="numDashClass.analyticsData.data | formatNumber"
            statisticTitle="TỔNG SỐ LỚP"
            :chartData="numDashClass.series"
            color="success"
            type="area" />
      </div>

      <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
          <statistics-card-line
            v-if="numDashTeacher.analyticsData"
            icon="UserIcon"
            :statistic="numDashTeacher.analyticsData.data | formatNumber"
            statisticTitle="TỔNG SỐ GIÁO VIÊN"
            :chartData="numDashTeacher.series"
            color="danger"
            type="area" />
      </div>
      <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
          <statistics-card-line
            v-if="numDashRooms.analyticsData"
            icon="LayersIcon"
            :statistic="numDashRooms.analyticsData.data | formatNumber"
            statisticTitle="TỔNG SỐ PHÒNG HỌC"
            :chartData="numDashRooms.series"
            color="warning"
            type="area" />
      </div>
      <div class="vx-col w-full md:w-1/2 mb-base">
        <vx-card class="text">
          <h5 class="mb-4">Biểu đồ tình hình học viên</h5>
          <vue-apex-charts type="pie" :options="pieChartStudent.chartOptions" :series="pieChartStudent.series"></vue-apex-charts>
        </vx-card>
      </div>
      <div class="vx-col w-full md:w-1/2 mb-base">
        <vx-card class="text">
          <h5 class="mb-4">Biểu đồ đăng ký khóa học</h5>
          <vue-apex-charts type="pie" :options="pieChartProduct.chartOptions" :series="pieChartProduct.series"></vue-apex-charts>
        </vx-card>
      </div>
      <div class="vx-col w-full mb-base">
        <vx-card class="text">
          <h5 class="mb-4">Biểu đồ doanh thu theo trung tâm</h5>
          <vue-apex-charts type="line" :options="lineChartRevenue.chartOptions" :series="lineChartRevenue.series"></vue-apex-charts>
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
        arr_branch: "",
        branch_id:"",
      },
      numDashStudent: {
      },
      numDashClass: {
      },
      numDashTeacher: {
      },
      numDashRooms: {
      },
      pieChartStudent: {
        series: [],
        chartOptions: {
          labels: [], 
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
      pieChartProduct: {
        series: [],
        chartOptions: {
          labels: [], 
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
      lineChartRevenue:{
        chartOptions: {
          xaxis: {
            type: 'text',
            categories: ['10/2024','11/2024','12/2024','01/2025'],
          },
        },
        series: [{
          name: 'Series A',
          data: [30, 40, 45, 50, 49, 60, 70, 91]
        }, {
          name: 'Series B',
          data: [23, 43, 54, 12, 44, 52, 32, 11]
        }]
      }
    }
  },
  created () {
    axios.g(`/api/system/branches-has-user`)
      .then(response => {
      this.branch_list = response.data
    })
    this.loadData();
  },
  methods: {
    loadData(){
      const ids_branch = []
      if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
        this.searchData.arr_branch.map(item => {
          ids_branch.push(item.id)
        })
      }
      this.searchData.branch_id = ids_branch
      this.$vs.loading()
      axios.p(`/api/dashboard`,{
        branch_id: this.searchData.branch_id,
      })
      .then(response => {
        this.$vs.loading.close()
        this.numDashStudent = {
          series: [
            {
              name: 'Học sinh',
              data: response.data.numDashStudent.series
            }
          ],
          analyticsData: {
            data: response.data.numDashStudent.data
          }
        }
        this.numDashClass = {
          series: [
            {
              name: 'Lớp',
              data: response.data.numDashClass.series
            }
          ],
          analyticsData: {
            data: response.data.numDashClass.data
          }
        }
        this.numDashTeacher = {
          series: [
            {
              name: 'Giáo viên',
              data: response.data.numDashTeacher.series
            }
          ],
          analyticsData: {
            data: response.data.numDashTeacher.data
          }
        }
        this.numDashRooms = {
          series: [
            {
              name: 'Phòng học',
              data: response.data.numDashRooms.series
            }
          ],
          analyticsData: {
            data: response.data.numDashRooms.data
          }
        }
        this.pieChartStudent = {
          series: [response.data.pieChartStudent.studentActive, response.data.pieChartStudent.studentTrial, response.data.pieChartStudent.studentPending, response.data.pieChartStudent.studentWithdraw],
          chartOptions: {
            labels: ['Đang đi học', 'Học thử', 'Bảo lưu & pending', 'Hết phí'], 
            colors: ['#28C76F', '#1E1E1E', '#7367F0', '#EA5455'],
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
        this.pieChartProduct = {
          series: response.data.pieChartProduct.seriesChartProduct,
          chartOptions: {
            labels: response.data.pieChartProduct.labelsChartProduct, 
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
        }
      })
    }
  },
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
