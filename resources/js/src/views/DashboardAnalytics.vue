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
      <div class="vx-col w-full mb-4 dash-select-branch">
        <vx-input-group class="mb-base mr-3">
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
              class="vs-inputx vs-input--input"
            >
              <span slot="noResult">Không tìm thấy dữ liệu</span>
            </multiselect>

            <template slot="append">
              <div class="append-text btn-addon">
                <vs-button class="whitespace-no-wrap" @click="loadData">Tìm kiếm</vs-button>
              </div>
            </template>
          </vx-input-group>
        
      </div>
    </div>
    <div class="vx-row">
      <div class="vx-col w-full lg:w-1/2 mb-base">
        <vx-card slot="no-body" class="text-center bg-primary-gradient greet-user">
                    <img src="@assets/images/elements/decore-left.png" class="decore-left" alt="Decore Left" width="200" >
                    <img src="@assets/images/elements/decore-right.png" class="decore-right" alt="Decore Right" width="175">
          <feather-icon icon="AwardIcon" class="p-6 mb-8 bg-primary inline-flex rounded-full text-white shadow" svgClasses="h-8 w-8"></feather-icon>
          <h1 class="mb-6 text-white">Xin chào {{ $store.state.AppActiveUser.displayName }},</h1>
          <p class="xl:w-3/4 lg:w-4/5 md:w-2/3 w-4/5 mx-auto text-white">You have done <b>57.6%</b> more sales today. Check your new badge in your profile.</p>
        </vx-card>
      </div>

      <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base" v-if="checkPermission('dashboard_09')">
        <statistics-card-line icon="DollarSignIcon" 
          v-if="totalRevenueDay.analyticsData" 
          :statistic="totalRevenueDay.analyticsData.data" 
          statisticTitle="Doanh số ngày (triệu đồng)" 
          :chartData="totalRevenueDay.series" 
          type="area"
        ></statistics-card-line>
      </div>

      <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base" v-if="checkPermission('dashboard_10')">
        <statistics-card-line icon="DollarSignIcon" 
          v-if="totalRevenueMonth.analyticsData" 
          :statistic="totalRevenueMonth.analyticsData.data " 
          statisticTitle="Doanh số tháng (triệu đồng)" 
          :chartData="totalRevenueMonth.series" 
          color="warning" type="area"
        ></statistics-card-line>
      </div>
      <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base" v-if="checkPermission('dashboard_01')">
          <statistics-card-line
            ref="apexChart"
            v-if="numDashStudent.analyticsData"
            icon="UsersIcon"
            :statistic="numDashStudent.analyticsData.data | formatNumber"
            statisticTitle="Học viên đang học"
            :chartData="numDashStudent.series"
            type="area" />
      </div>

      <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base" v-if="checkPermission('dashboard_02')">
          <statistics-card-line
            v-if="numDashClass.analyticsData"
            icon="FileIcon"
            :statistic="numDashClass.analyticsData.data | formatNumber"
            statisticTitle="Tổng số lớp"
            :chartData="numDashClass.series"
            color="success"
            type="area" />
      </div>

      <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base"  v-if="checkPermission('dashboard_03')">
          <statistics-card-line
            v-if="numDashTeacher.analyticsData"
            icon="UserIcon"
            :statistic="numDashTeacher.analyticsData.data | formatNumber"
            statisticTitle="Tổng số giáo viên"
            :chartData="numDashTeacher.series"
            color="danger"
            type="area" />
      </div>
      <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base" v-if="checkPermission('dashboard_04')">
          <statistics-card-line
            v-if="numDashRooms.analyticsData"
            icon="LayersIcon"
            :statistic="numDashRooms.analyticsData.data | formatNumber"
            statisticTitle="Tổng số phòng học"
            :chartData="numDashRooms.series"
            color="warning"
            type="area" />
      </div>
      <div class="vx-col w-full md:w-1/2 mb-base"  v-if="checkPermission('dashboard_05')">
        <vx-card class="text">
          <h5 class="mb-4 text-center">Tình hình học viên</h5>
          <vue-apex-charts type="pie" :options="pieChartStudent.chartOptions" :series="pieChartStudent.series"></vue-apex-charts>
        </vx-card>
      </div>
      <div class="vx-col w-full md:w-1/2 mb-base" v-if="checkPermission('dashboard_06')">
        <vx-card class="text">
          <h5 class="mb-4 text-center">Đăng ký chương trình học</h5>
          <vue-apex-charts type="pie" :options="pieChartProduct.chartOptions" :series="pieChartProduct.series"></vue-apex-charts>
        </vx-card>
      </div>
      <div class="vx-col w-full md:w-1/2 mb-base" v-if="checkPermission('dashboard_07')">
        <vx-card class="text">
          <h5 class="mb-4 text-center">Doanh thu theo trung tâm</h5>
          <vue-apex-charts type="line" :options="lineChartRevenue.chartOptions" :series="lineChartRevenue.series"></vue-apex-charts>
        </vx-card>
      </div>
      <div class="vx-col w-full md:w-1/2 mb-base" v-if="checkPermission('dashboard_08')">
        <vx-card class="text">
          <h5 class="mb-4 text-center">Doanh thu theo EC</h5>
          <vue-apex-charts type="line" :options="lineChartRevenueEC.chartOptions" :series="lineChartRevenueEC.series"></vue-apex-charts>
        </vx-card>
      </div>
      <div class="vx-col w-full md:w-1/2 mb-base" v-if="checkPermission('dashboard_11')">
        <vx-card class="text">
          <h5 class="mb-4 text-center">Học sinh Renew theo trung tâm</h5>
          <vue-apex-charts type="bar" :options="lineChartRenew.chartOptions" :series="lineChartRenew.series"></vue-apex-charts>
        </vx-card>
      </div>
      <div class="vx-col w-full md:w-1/2 mb-base" v-if="checkPermission('dashboard_12')">
        <vx-card class="text">
          <h5 class="mb-4 text-center">Học sinh Renew theo AF</h5>
          <vue-apex-charts type="bar" :options="lineChartRenewAF.chartOptions" :series="lineChartRenewAF.series"></vue-apex-charts>
        </vx-card>
      </div>
      <div class="vx-col w-full md:w-1/2 mb-base" v-if="checkPermission('dashboard_13')">
        <vx-card class="text">
          <h5 class="mb-4 text-center">Học sinh Renew theo tháng</h5>
          <vue-apex-charts type="bar" :options="lineChartRenewLog.chartOptions" :series="lineChartRenewLog.series"></vue-apex-charts>
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
import u from '../until/helper.js';

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
      totalRevenueDay: {},
      totalRevenueMonth: {},
      numDashStudent: {},
      numDashClass: {},
      numDashTeacher: {},
      numDashRooms: {},
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
            categories: [],
          },
          yaxis: {
            labels: {
              formatter: function (value) {
                return value + " tr";
              }
            },
          },
        },
        series: []
      },
      lineChartRevenueEC:{
        chartOptions: {
          xaxis: {
            type: 'text',
            categories: [],
          },
          yaxis: {
            labels: {
              formatter: function (value) {
                return value + " tr";
              }
            },
          },
        },
        series: []
      },
      lineChartRenew:{
        series: [{
          name: 'Thành công',
          data: [44, 55, 41, 37, 22, 43, 21]
        }, {
          name: 'Thất bại',
          data: [53, 32, 33, 52, 13, 43, 32]
        }],
        chartOptions: {
          chart: {
            type: 'bar',
            height: 350,
            stacked: true,
          },
          plotOptions: {
            bar: {
              horizontal: true,
              dataLabels: {
                total: {
                  enabled: true,
                  offsetX: 0,
                  style: {
                    fontSize: '13px',
                    fontWeight: 900
                  }
                }
              }
            },
          },
          stroke: {
            width: 1,
            colors: ['#fff']
          },
          xaxis: {
            categories: [2008, 2009, 2010, 2011, 2012, 2013, 2014],
            labels: {
              formatter: function (val) {
                return val
              }
            }
          },
          yaxis: {
            title: {
              text: undefined
            },
          },
          tooltip: {
            y: {
              formatter: function (val) {
                return val
              }
            }
          },
          fill: {
            opacity: 1
          },
          legend: {
            position: 'top',
            horizontalAlign: 'left',
            offsetX: 40
          }
        },
      },
      lineChartRenewAF:{
        series: [{
          name: 'Thành công',
          data: [44, 55, 41, 37, 22, 43, 21]
        }, {
          name: 'Thất bại',
          data: [53, 32, 33, 52, 13, 43, 32]
        }],
        chartOptions: {
          chart: {
            type: 'bar',
            height: 350,
            stacked: true,
          },
          plotOptions: {
            bar: {
              horizontal: true,
              dataLabels: {
                total: {
                  enabled: true,
                  offsetX: 0,
                  style: {
                    fontSize: '13px',
                    fontWeight: 900
                  }
                }
              }
            },
          },
          stroke: {
            width: 1,
            colors: ['#fff']
          },
          xaxis: {
            categories: [2008, 2009, 2010, 2011, 2012, 2013, 2014],
            labels: {
              formatter: function (val) {
                return val
              }
            }
          },
          yaxis: {
            title: {
              text: undefined
            },
          },
          tooltip: {
            y: {
              formatter: function (val) {
                return val
              }
            }
          },
          fill: {
            opacity: 1
          },
          legend: {
            position: 'top',
            horizontalAlign: 'left',
            offsetX: 40
          }
        },
      },
      lineChartRenewLog: {
        series: [{
          name: 'Thất bại',
          data: [44, 55, 41, 67, 22, 43]
        }, {
          name: 'Thành công',
          data: [13, 23, 20, 8, 13, 27]
        }],
        chartOptions: {
          chart: {
            type: 'bar',
            height: 350,
            stacked: true,
            toolbar: {
              show: true
            },
            zoom: {
              enabled: true
            }
          },
          responsive: [{
            breakpoint: 480,
            options: {
              legend: {
                position: 'bottom',
                offsetX: -10,
                offsetY: 0
              }
            }
          }],
          plotOptions: {
            bar: {
              horizontal: false,
              borderRadiusApplication: 'end', // 'around', 'end'
              borderRadiusWhenStacked: 'last', // 'all', 'last'
              dataLabels: {
                total: {
                  enabled: true,
                  style: {
                    fontSize: '13px',
                    fontWeight: 900
                  }
                }
              }
            },
          },
          xaxis: {
            type: 'text',
            categories: ['08/2024', '09/2024', '10/2024', '11/2024', '12/2024', '01/2025'
            ],
          },
          legend: {
            position: 'bottom',
            horizontalAlign: 'center',
            offsetX: 20
          },
          fill: {
            opacity: 1
          }
        },
      },
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
    checkPermission(text){
      return u.checkPermission(this.$store.state.AppActiveUser, text)
    },
    loadDataDashboard01(){
      const ids_branch = []
      if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
        this.searchData.arr_branch.map(item => {
          ids_branch.push(item.id)
        })
      }
      this.searchData.branch_id = ids_branch
      this.$vs.loading()
      axios.p(`/api/dashboard/01`,{
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
      })
    },
    loadDataDashboard02(){
      const ids_branch = []
      if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
        this.searchData.arr_branch.map(item => {
          ids_branch.push(item.id)
        })
      }
      this.searchData.branch_id = ids_branch
      this.$vs.loading()
      axios.p(`/api/dashboard/02`,{
        branch_id: this.searchData.branch_id,
      })
      .then(response => {
        this.$vs.loading.close()
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
      })
    },
    loadDataDashboard03(){
      const ids_branch = []
      if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
        this.searchData.arr_branch.map(item => {
          ids_branch.push(item.id)
        })
      }
      this.searchData.branch_id = ids_branch
      this.$vs.loading()
      axios.p(`/api/dashboard/03`,{
        branch_id: this.searchData.branch_id,
      })
      .then(response => {
        this.$vs.loading.close()
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
      })
    },
    loadDataDashboard04(){
      const ids_branch = []
      if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
        this.searchData.arr_branch.map(item => {
          ids_branch.push(item.id)
        })
      }
      this.searchData.branch_id = ids_branch
      this.$vs.loading()
      axios.p(`/api/dashboard/04`,{
        branch_id: this.searchData.branch_id,
      })
      .then(response => {
        this.$vs.loading.close()
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
      })
    },
    loadDataDashboard05(){
      const ids_branch = []
      if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
        this.searchData.arr_branch.map(item => {
          ids_branch.push(item.id)
        })
      }
      this.searchData.branch_id = ids_branch
      this.$vs.loading()
      axios.p(`/api/dashboard/05`,{
        branch_id: this.searchData.branch_id,
      })
      .then(response => {
        this.$vs.loading.close()
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
        }
      })
    },
    loadDataDashboard06(){
      const ids_branch = []
      if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
        this.searchData.arr_branch.map(item => {
          ids_branch.push(item.id)
        })
      }
      this.searchData.branch_id = ids_branch
      this.$vs.loading()
      axios.p(`/api/dashboard/06`,{
        branch_id: this.searchData.branch_id,
      })
      .then(response => {
        this.$vs.loading.close()
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
    },
    loadDataDashboard07(){
      const ids_branch = []
      if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
        this.searchData.arr_branch.map(item => {
          ids_branch.push(item.id)
        })
      }
      this.searchData.branch_id = ids_branch
      this.$vs.loading()
      axios.p(`/api/dashboard/07`,{
        branch_id: this.searchData.branch_id,
      })
      .then(response => {
        this.$vs.loading.close()
        this.lineChartRevenue = {
          chartOptions: {
            xaxis: {
              type: 'text',
              categories: response.data.lineChartRevenue.categories,
            },
          },
          series: response.data.lineChartRevenue.series
        }
      })
    },
    loadDataDashboard08(){
      const ids_branch = []
      if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
        this.searchData.arr_branch.map(item => {
          ids_branch.push(item.id)
        })
      }
      this.searchData.branch_id = ids_branch
      this.$vs.loading()
      axios.p(`/api/dashboard/08`,{
        branch_id: this.searchData.branch_id,
      })
      .then(response => {
        this.$vs.loading.close()
        this.lineChartRevenueEC = {
          chartOptions: {
            xaxis: {
              type: 'text',
              categories: response.data.lineChartRevenueEC.categories,
            },
          },
          series: response.data.lineChartRevenueEC.series
        }
      })
    },
    loadDataDashboard09(){
      const ids_branch = []
      if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
        this.searchData.arr_branch.map(item => {
          ids_branch.push(item.id)
        })
      }
      this.searchData.branch_id = ids_branch
      this.$vs.loading()
      axios.p(`/api/dashboard/09`,{
        branch_id: this.searchData.branch_id,
      })
      .then(response => {
        this.$vs.loading.close()
        this.totalRevenueDay = {
          series: [
            {
              name: 'Doanh số ngày',
              data: response.data.totalRevenueDay.series
            }
          ],
          analyticsData: {
            data: response.data.totalRevenueDay.data
          }
        }
      })
    },
    loadDataDashboard10(){
      const ids_branch = []
      if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
        this.searchData.arr_branch.map(item => {
          ids_branch.push(item.id)
        })
      }
      this.searchData.branch_id = ids_branch
      this.$vs.loading()
      axios.p(`/api/dashboard/10`,{
        branch_id: this.searchData.branch_id,
      })
      .then(response => {
        this.$vs.loading.close()
        this.totalRevenueMonth = {
          series: [
            {
              name: 'Doanh số tháng',
              data: response.data.totalRevenueMonth.series
            }
          ],
          analyticsData: {
            data: response.data.totalRevenueMonth.data
          }
        }
      })
    },
    loadDataDashboard11(){
      const ids_branch = []
      if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
        this.searchData.arr_branch.map(item => {
          ids_branch.push(item.id)
        })
      }
      this.searchData.branch_id = ids_branch
      this.$vs.loading()
      axios.p(`/api/dashboard/11`,{
        branch_id: this.searchData.branch_id,
      })
      .then(response => {
        this.$vs.loading.close()
        this.lineChartRenew = {
          series: [ {
            name: 'Thất bại',
            data: response.data.lineChartRenew.dataFalseRenew
          },{
            name: 'Thành công',
            data: response.data.lineChartRenew.dataRenew
          }],
          chartOptions: {
            chart: {
              type: 'bar',
              height: 350,
              stacked: true,
            },
            plotOptions: {
              bar: {
                horizontal: true,
                dataLabels: {
                  total: {
                    enabled: true,
                    offsetX: 0,
                    style: {
                      fontSize: '13px',
                      fontWeight: 900
                    }
                  }
                }
              },
            },
            stroke: {
              width: 1,
              colors: ['#fff']
            },
            xaxis: {
              categories: response.data.lineChartRenew.categories,
              labels: {
                formatter: function (val) {
                  return val
                }
              }
            },
            yaxis: {
              title: {
                text: undefined
              },
            },
            tooltip: {
              y: {
                formatter: function (val) {
                  return val
                }
              }
            },
            fill: {
              opacity: 1
            },
            legend: {
              position: 'top',
              horizontalAlign: 'left',
              offsetX: 40
            }
          },
        }
      })
    },
    loadDataDashboard12(){
      const ids_branch = []
      if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
        this.searchData.arr_branch.map(item => {
          ids_branch.push(item.id)
        })
      }
      this.searchData.branch_id = ids_branch
      this.$vs.loading()
      axios.p(`/api/dashboard/12`,{
        branch_id: this.searchData.branch_id,
      })
      .then(response => {
        this.$vs.loading.close()
        this.lineChartRenewAF = {
          series: [ {
            name: 'Thất bại',
            data: response.data.lineChartRenewAF.dataFalseRenew
          },{
            name: 'Thành công',
            data: response.data.lineChartRenewAF.dataRenew
          }],
          chartOptions: {
            chart: {
              type: 'bar',
              height: 350,
              stacked: true,
            },
            plotOptions: {
              bar: {
                horizontal: true,
                dataLabels: {
                  total: {
                    enabled: true,
                    offsetX: 0,
                    style: {
                      fontSize: '13px',
                      fontWeight: 900
                    }
                  }
                }
              },
            },
            stroke: {
              width: 1,
              colors: ['#fff']
            },
            xaxis: {
              categories: response.data.lineChartRenewAF.categories,
              labels: {
                formatter: function (val) {
                  return val
                }
              }
            },
            yaxis: {
              title: {
                text: undefined
              },
            },
            tooltip: {
              y: {
                formatter: function (val) {
                  return val
                }
              }
            },
            fill: {
              opacity: 1
            },
            legend: {
              position: 'top',
              horizontalAlign: 'left',
              offsetX: 40
            }
          },
        }
      })
    },
    loadData(){
      if(this.checkPermission('dashboard_01')){
        this.loadDataDashboard01();
      }
      if(this.checkPermission('dashboard_02')){
        this.loadDataDashboard02();
      }
      if(this.checkPermission('dashboard_03')){
        this.loadDataDashboard03();
      }
      if(this.checkPermission('dashboard_04')){
        this.loadDataDashboard04();
      }
      if(this.checkPermission('dashboard_05')){
        this.loadDataDashboard05();
      }
      if(this.checkPermission('dashboard_06')){
        this.loadDataDashboard06();
      }
      if(this.checkPermission('dashboard_07')){
        this.loadDataDashboard07();
      }
      if(this.checkPermission('dashboard_08')){
        this.loadDataDashboard08();
      }
      if(this.checkPermission('dashboard_09')){
        this.loadDataDashboard09();
      }
      if(this.checkPermission('dashboard_10')){
        this.loadDataDashboard10();
      }
      if(this.checkPermission('dashboard_11')){
        this.loadDataDashboard11();
      }
      if(this.checkPermission('dashboard_12')){
        this.loadDataDashboard12();
      }
    }
  },
}
</script>

<style lang="scss">
.dash-select-branch .multiselect.vs-inputx.vs-input--input{
  padding: 0px;
  border: none;
}
.dash-select-branch .multiselect__tags{
  border-top-right-radius: 0px;
  border-bottom-right-radius: 0px;
}
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
