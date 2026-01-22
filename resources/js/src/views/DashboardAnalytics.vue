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
      <div class="vx-col w-full mb-base">
        <vx-card slot="no-body" class="text-center bg-primary-gradient greet-user">
                    <img src="@assets/images/elements/decore-left.png" class="decore-left" alt="Decore Left" width="200" >
                    <img src="@assets/images/elements/decore-right.png" class="decore-right" alt="Decore Right" width="175">
          <feather-icon icon="AwardIcon" class="p-6 mb-8 bg-primary inline-flex rounded-full text-white shadow" svgClasses="h-8 w-8"></feather-icon>
          <h1 class="mb-6 text-white">Xin chào {{ $store.state.AppActiveUser.displayName }},</h1>
          <p class="xl:w-3/4 lg:w-4/5 md:w-2/3 w-4/5 mx-auto text-white" v-html="textGreeting"></p>
        </vx-card>
      </div>
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
      <!-- <div class="vx-col w-1/2 sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base" v-if="checkPermission('dashboard_01')">
        <div class="vx-card overflow-hidden">
          <div class="vx-card__collapsible-content vs-con-loading__container">
              <div>
                  <div class="p-6">
                      <span
                          class="p-3 inline-flex rounded-full feather-icon select-none relative text-primary mb-4"
                          style="background: rgba(var(--vs-primary),.15);">
                          <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px"
                              viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                              stroke-linecap="round" stroke-linejoin="round"
                              class="feather feather-dollar-sign ">
                              <line x1="12" y1="1" x2="12" y2="23"></line>
                              <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                          </svg>
                      </span>
                      <div class="truncate">
                          <h2 class="mb-1 font-bold">{{totalRevenueWeek.analyticsData.data}}</h2>
                          <span>Doanh số tuần (triệu đồng)</span>
                      </div>
                  </div>
              </div> 
          </div>
        </div>
      </div>
      <div class="vx-col w-1/2 sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base" v-if="checkPermission('dashboard_02')">
        <div class="vx-card overflow-hidden">
          <div class="vx-card__collapsible-content vs-con-loading__container">
              <div>
                  <div class="p-6">
                      <span
                          class="p-3 inline-flex rounded-full feather-icon select-none relative text-warning mb-4"
                          style="background: rgba(var(--vs-warning),.15);">
                          <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px"
                              viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                              stroke-linecap="round" stroke-linejoin="round"
                              class="feather feather-dollar-sign ">
                              <line x1="12" y1="1" x2="12" y2="23"></line>
                              <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                          </svg>
                      </span>
                      <div class="truncate">
                          <h2 class="mb-1 font-bold">{{totalRevenueMonth.analyticsData.data}}</h2>
                          <span>Doanh số tháng (triệu đồng)</span>
                      </div>
                  </div>
              </div> 
          </div>
        </div>
      </div>
      <div class="vx-col w-1/2 sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base" v-if="checkPermission('dashboard_03')">
        <div class="vx-card overflow-hidden">
          <div class="vx-card__collapsible-content vs-con-loading__container">
              <div>
                  <div class="p-6">
                    <span class="p-3 inline-flex rounded-full feather-icon select-none relative text-primary mb-4"
                        style="background: rgba(var(--vs-primary),.15);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="feather feather-users ">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </span>
                      <div class="truncate">
                          <h2 class="mb-1 font-bold">{{numDashStudent.analyticsData.data | formatNumber}}</h2>
                          <span>Học viên đang học</span>
                      </div>
                  </div>
              </div> 
          </div>
        </div>
      </div>
      <div class="vx-col w-1/2 sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base" v-if="checkPermission('dashboard_04')">
        <div class="vx-card overflow-hidden">
          <div class="vx-card__collapsible-content vs-con-loading__container">
              <div>
                  <div class="p-6">
                    <span class="p-3 inline-flex rounded-full feather-icon select-none relative text-success mb-4"
                        style="background: rgba(var(--vs-success),.15);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="feather feather-file ">
                            <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                            <polyline points="13 2 13 9 20 9"></polyline>
                        </svg>
                    </span>
                      <div class="truncate">
                          <h2 class="mb-1 font-bold">{{numDashClass.analyticsData.data | formatNumber}}</h2>
                          <span>Tổng số lớp</span>
                      </div>
                  </div>
              </div> 
          </div>
        </div>
      </div>
      <div class="vx-col w-full md:w-1/3 mb-base"  v-if="checkPermission('dashboard_05')">
        <vx-card class="text">
          <h5 class="mb-4 text-center">Trạng thái học viên</h5>
          <vue-apex-charts type="pie"  :options="pieChartStudent.chartOptions" :series="pieChartStudent.series"></vue-apex-charts>
        </vx-card>
      </div>
      <div class="vx-col w-full md:w-1/3 mb-base"  v-if="checkPermission('dashboard_07')">
        <vx-card class="text">
          <h5 class="mb-4 text-center">Trạng thái đóng phí</h5>
          <vue-apex-charts type="pie"  :options="pieChartFee.chartOptions" :series="pieChartFee.series"></vue-apex-charts>
        </vx-card>
      </div>
      <div class="vx-col w-full md:w-1/3 mb-base" v-if="checkPermission('dashboard_06')">
        <vx-card class="text">
          <h5 class="mb-4 text-center">Đăng ký khóa học</h5>
          <vue-apex-charts type="pie"  :options="pieChartProduct.chartOptions" :series="pieChartProduct.series"></vue-apex-charts>
        </vx-card>
      </div>
      <div class="vx-col w-full mb-base" v-if="checkPermission('dashboard_08')">
        <vx-card class="text">
          <h5 class="mb-4 text-center">Doanh thu theo trung tâm</h5>
          <vue-apex-charts type="line" height="480" :options="lineChartRevenue.chartOptions" :series="lineChartRevenue.series"></vue-apex-charts>
        </vx-card>
      </div>
      <div class="vx-col w-full mb-base" v-if="checkPermission('dashboard_09')">
        <vx-card class="text">
          <h5 class="mb-4 text-center">Doanh thu RENEW theo trung tâm</h5>
          <vue-apex-charts type="line" height="480" :options="lineChartRevenueRenew.chartOptions" :series="lineChartRevenueRenew.series"></vue-apex-charts>
        </vx-card>
      </div>
      <div class="vx-col w-full mb-base" v-if="checkPermission('dashboard_10')">
        <vx-card class="text">
          <h5 class="mb-4 text-center">Tỷ lệ RENEW theo trung tâm</h5>
          <vue-apex-charts type="line" height="480" :options="lineChartRenew.chartOptions" :series="lineChartRenew.series"></vue-apex-charts>
        </vx-card>
      </div>
      <div class="vx-col w-full mb-base" v-if="checkPermission('dashboard_11')">
        <vx-card class="text">
          <h5 class="mb-4 text-center">Doanh thu theo EC</h5>
          <vue-apex-charts type="line" height="480" :options="lineChartRevenueEC.chartOptions" :series="lineChartRevenueEC.series"></vue-apex-charts>
        </vx-card>
      </div>
      <div class="vx-col w-full mb-base" v-if="checkPermission('dashboard_12')">
        <vx-card class="text">
          <h5 class="mb-4 text-center">Học sinh Renew theo CM</h5>
          <vue-apex-charts type="bar" :options="lineChartRenewCM.chartOptions" :series="lineChartRenewCM.series"></vue-apex-charts>
        </vx-card>
      </div>
      <div class="vx-col w-full mb-base" v-if="checkPermission('dashboard_13')">
        <vx-card>
          <h5 class="text-center mb-3">Danh sách học sinh học thử</h5>
          <div class="vs-component vs-con-table stripe vs-table-primary">
            <div class="con-tablex vs-table--content">
              <div class="vs-con-tbody vs-table--tbody ">
                <table class="vs-table vs-table--tbody-table">
                  <thead class="vs-table--thead">
                    <tr>
                      
                      <th colspan="1" rowspan="1" class="text-center">#</th>
                      <th colspan="1" rowspan="1" >Họ tên</th>
                      <th colspan="1" rowspan="1" >Số điện thoại</th>
                      <th colspan="1" rowspan="1" >Lớp đang học</th>
                      <th colspan="1" rowspan="1" >Số buổi học thử còn lại</th>
                    </tr>
                  </thead>
                  <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in dataStudentTrial" :key="index">
                    <td class="td vs-table--td text-center">{{ index + 1 }}</td>
                    <td class="td vs-table--td">{{item.name}}</td>
                    <td class="td vs-table--td">{{item.gud_mobile1}}</td>
                    <td class="td vs-table--td">{{item.cls_name}}</td>
                    <td class="td vs-table--td">{{item.left_sessions}}</td>
                  </tr>
                </table>
                
              </div>
            </div>
          </div>
        </vx-card>
      </div>

      <div class="vx-col w-full mb-base" v-if="checkPermission('dashboard_14')">
        <vx-card>
          <h5 class="text-center mb-3">Danh sách học sinh đã cọc</h5>
          <div class="vs-component vs-con-table stripe vs-table-primary">
            <div class="con-tablex vs-table--content">
              <div class="vs-con-tbody vs-table--tbody ">
                <table class="vs-table vs-table--tbody-table">
                  <thead class="vs-table--thead">
                    <tr>
                      
                      <th colspan="1" rowspan="1" class="text-center">#</th>
                      <th colspan="1" rowspan="1" >Họ tên</th>
                      <th colspan="1" rowspan="1" >Số điện thoại</th>
                      <th colspan="1" rowspan="1" >Gói học phí</th>
                      <th colspan="1" rowspan="1" >Số tiền</th>
                      <th colspan="1" rowspan="1" >Đã thanh toán</th>
                      <th colspan="1" rowspan="1" >Cần nộp</th>
                    </tr>
                  </thead>
                  <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in dataStudentDeposit" :key="index">
                    <td class="td vs-table--td text-center">{{ index + 1 }}</td>
                    <td class="td vs-table--td">{{item.name}}</td>
                    <td class="td vs-table--td">{{item.gud_mobile1}}</td>
                    <td class="td vs-table--td">{{item.tuition_fee_name}}</td>
                    <td class="td vs-table--td">{{item.must_charge | formatNumber}}</td>
                    <td class="td vs-table--td">{{item.init_total_charged | formatNumber}}</td>
                    <td class="td vs-table--td">{{item.debt_amount | formatNumber}}</td>
                  </tr>
                </table>
                
              </div>
            </div>
          </div>
        </vx-card>
      </div>

      <div class="vx-col w-full mb-base" v-if="checkPermission('dashboard_15')">
        <vx-card>
          <h5 class="text-center mb-3">Danh sách học sinh Pending</h5>
          <div class="vs-component vs-con-table stripe vs-table-primary">
            <div class="con-tablex vs-table--content">
              <div class="vs-con-tbody vs-table--tbody ">
                <table class="vs-table vs-table--tbody-table">
                  <thead class="vs-table--thead">
                    <tr>
                      
                      <th colspan="1" rowspan="1" class="text-center">#</th>
                      <th colspan="1" rowspan="1" >Họ tên</th>
                      <th colspan="1" rowspan="1" >Mã LMS</th>
                      <th colspan="1" rowspan="1" >Số điện thoại</th>
                      <th colspan="1" rowspan="1" >Trình độ</th>
                      <th colspan="1" rowspan="1" >Ngày dự kiến học</th>
                    </tr>
                  </thead>
                  <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in dataStudentPending" :key="index">
                    <td class="td vs-table--td text-center">{{ index + 1 }}</td>
                    <td class="td vs-table--td">{{item.name}}</td>
                    <td class="td vs-table--td">{{item.lms_id}}</td>
                    <td class="td vs-table--td">{{item.gud_mobile1}}</td>
                    <td class="td vs-table--td">{{item.product_name}}</td>
                    <td class="td vs-table--td">{{item.start_date}}</td>
                  </tr>
                </table>
                
              </div>
            </div>
          </div>
        </vx-card>
      </div>
      <div class="vx-col w-full mb-base" v-if="checkPermission('dashboard_16')">
        <vx-card>
          <h5 class="text-center mb-3">Danh sách học sinh hết phí chưa withdraw</h5>
          <div class="vs-component vs-con-table stripe vs-table-primary">
            <div class="con-tablex vs-table--content">
              <div class="vs-con-tbody vs-table--tbody ">
                <table class="vs-table vs-table--tbody-table">
                  <thead class="vs-table--thead">
                    <tr>
                      
                      <th colspan="1" rowspan="1" class="text-center">#</th>
                      <th colspan="1" rowspan="1" >Họ tên</th>
                      <th colspan="1" rowspan="1" >Mã LMS</th>
                      <th colspan="1" rowspan="1" >Lớp đang học</th>
                      <th colspan="1" rowspan="1" >Ngày hết phí</th>
                    </tr>
                  </thead>
                  <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in dataStudentWithdraw" :key="index">
                    <td class="td vs-table--td text-center">{{ index + 1 }}</td>
                    <td class="td vs-table--td">{{item.name}}</td>
                    <td class="td vs-table--td">{{item.lms_id}}</td>
                    <td class="td vs-table--td">{{item.cls_name}}</td>
                    <td class="td vs-table--td">{{item.enrolment_last_date}}</td>
                  </tr>
                </table>
                
              </div>
            </div>
          </div>
        </vx-card>
      </div> -->

      <!-- Revenue Table Section - Dashboard 17 -->
      <div class="vx-col w-full mb-base">
        <vx-card>
          <h4 class="mb-4">1. DOANH SỐ THEO TRUNG TÂM</h4>
          
          <!-- Revenue Table -->
          <div class="vs-component vs-con-table stripe vs-table-primary">
            <div class="con-tablex vs-table--content">
              <div class="vs-con-tbody vs-table--tbody">
                <table class="vs-table vs-table--tbody-table" style="width: 100%">
                  <thead class="vs-table--thead">
                    <tr>
                      <th class="text-center" style="width: 60px">STT</th>
                      <th>Trung tâm</th>
                      <th class="text-right">Doanh số ngày</th>
                      <th class="text-right">Doanh số 3 ngày gần nhất</th>
                      <th class="text-right">Doanh số tháng</th>
                      <th class="text-right">Doanh số 3 tháng gần nhất</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr 
                      v-for="(item, index) in revenueTableData" 
                      :key="index"
                      class="tr-values vs-table--tr"
                    >
                      <td class="td vs-table--td text-center">{{ item.stt }}</td>
                      <td class="td vs-table--td">
                        <strong>{{ item.branch_name }}</strong>
                      </td>
                      <td class="td vs-table--td text-right">
                        <span class="revenue-amount">{{ item.revenue_today | formatNumber }}</span>
                      </td>
                      <td class="td vs-table--td text-right">
                        <span class="revenue-amount">{{ item.revenue_3days | formatNumber }}</span>
                      </td>
                      <td class="td vs-table--td text-right">
                        <span class="revenue-amount highlight-month">{{ item.revenue_month | formatNumber }}</span>
                      </td>
                      <td class="td vs-table--td text-right">
                        <span class="revenue-amount highlight-3months">{{ item.revenue_3months | formatNumber }}</span>
                      </td>
                    </tr>
                    <!-- Total Row - Only show when more than 1 branch -->
                    <tr v-if="revenueTableData.length > 1" class="tr-values vs-table--tr total-row">
                      <td class="td vs-table--td text-center" colspan="2">
                        <strong>TỔNG CỘNG</strong>
                      </td>
                      <td class="td vs-table--td text-right">
                        <strong class="revenue-amount">{{ totalRevenueTable.today | formatNumber }}</strong>
                      </td>
                      <td class="td vs-table--td text-right">
                        <strong class="revenue-amount">{{ totalRevenueTable.days3 | formatNumber }}</strong>
                      </td>
                      <td class="td vs-table--td text-right">
                        <strong class="revenue-amount highlight-month">{{ totalRevenueTable.month | formatNumber }}</strong>
                      </td>
                      <td class="td vs-table--td text-right">
                        <strong class="revenue-amount highlight-3months">{{ totalRevenueTable.months3 | formatNumber }}</strong>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </vx-card>
      </div>

      <!-- Operations Dashboard Section - Dashboard 18 -->
      <div class="vx-col w-full mb-base">
        <vx-card>
          <h4 class="mb-4">2. VẬN HÀNH</h4>
          
          <!-- Operations Table -->
          <div class="vs-component vs-con-table stripe vs-table-primary">
            <div class="con-tablex vs-table--content">
              <div class="vs-con-tbody vs-table--tbody">
                <table class="vs-table vs-table--tbody-table" style="width: 100%">
                  <thead class="vs-table--thead">
                    <tr>
                      <th class="text-center" style="width: 60px">STT</th>
                      <th>Trung tâm</th>
                      <th class="text-center">Số học sinh Check in mới</th>
                      <th class="text-center">Số học sinh đăng ký mới</th>
                      <th class="text-center">Số học sinh hết phí trong tháng</th>
                      <th class="text-center">Tổng số hs active</th>
                      <th class="text-center">Tổng số lớp</th>
                      <th class="text-center">Tỉ lệ ACS</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr 
                      v-for="(item, index) in operationsTableData" 
                      :key="index"
                      class="tr-values vs-table--tr"
                    >
                      <td class="td vs-table--td text-center">{{ item.stt }}</td>
                      <td class="td vs-table--td">
                        <strong>{{ item.branch_name }}</strong>
                      </td>
                      <td class="td vs-table--td text-center">
                        <span class="ops-number">{{ item.checkin_students | formatNumber }}</span>
                      </td>
                      <td class="td vs-table--td text-center">
                        <span class="ops-number">{{ item.registered_students | formatNumber }}</span>
                      </td>
                      <td class="td vs-table--td text-center">
                        <span class="ops-number highlight-warning">{{ item.expired_students | formatNumber }}</span>
                      </td>
                      <td class="td vs-table--td text-center">
                        <span class="ops-number highlight-success">{{ item.active_students | formatNumber }}</span>
                      </td>
                      <td class="td vs-table--td text-center">
                        <span class="ops-number">{{ item.total_classes | formatNumber }}</span>
                      </td>
                      <td class="td vs-table--td text-center">
                        <span class="ops-number highlight-primary">{{ item.acs_ratio }}</span>
                      </td>
                    </tr>
                    <!-- Total Row - Only show when more than 1 branch -->
                    <tr v-if="operationsTableData.length > 1" class="tr-values vs-table--tr total-row">
                      <td class="td vs-table--td text-center" colspan="2">
                        <strong>TỔNG CỘNG</strong>
                      </td>
                      <td class="td vs-table--td text-center">
                        <strong class="ops-number">{{ totalOperations.checkin | formatNumber }}</strong>
                      </td>
                      <td class="td vs-table--td text-center">
                        <strong class="ops-number">{{ totalOperations.registered | formatNumber }}</strong>
                      </td>
                      <td class="td vs-table--td text-center">
                        <strong class="ops-number highlight-warning">{{ totalOperations.expired | formatNumber }}</strong>
                      </td>
                      <td class="td vs-table--td text-center">
                        <strong class="ops-number highlight-success">{{ totalOperations.active | formatNumber }}</strong>
                      </td>
                      <td class="td vs-table--td text-center">
                        <strong class="ops-number">{{ totalOperations.classes | formatNumber }}</strong>
                      </td>
                      <td class="td vs-table--td text-center">
                        <strong class="ops-number highlight-primary">{{ totalOperations.acsRatio }}</strong>
                      </td>
                    </tr>
                  </tbody>
                </table>
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
import u from '../until/helper.js';

export default {
  components: {
    StatisticsCardLine,
    VueApexCharts,
    Multiselect,
  },
  data () {
    return {
      textGreeting : '',
      greetingsByTime: {
        morning: [
          "Chào buổi sáng! Hôm nay bạn sẽ làm rất tốt!",
          "Buổi sáng tốt lành! Bắt đầu ngày mới thôi!",
          "Chúc bạn một ngày làm việc hiệu quả và đầy cảm hứng!",
          "Cà phê đã sẵn sàng, bạn thì sao?",
          "Hôm nay là một ngày tuyệt vời để thành công!",
          "Đăng nhập thành công – năng lượng tích cực đang chờ bạn!",
          "Hãy để hôm nay là ngày bạn tự hào về những gì đã làm!",
          "Bắt đầu nhẹ nhàng, kết thúc rực rỡ!",
          "Tinh thần tốt tạo nên một ngày tốt – bạn đã sẵn sàng!",
          "Nụ cười buổi sáng là bước đầu của sự thành công!"
        ],
        afternoon: [
          "Chào buổi chiều! Tiếp tục duy trì phong độ nhé!",
          "Buổi chiều vui vẻ, hãy hoàn thành mục tiêu nhé!",
          "Nửa ngày đã trôi qua, bạn đang làm rất tốt!",
          "Giữ vững tinh thần, sắp tới đích rồi!",
          "Hãy tự thưởng cho mình một chút thư giãn trước khi tiếp tục!",
          "Cố lên! Những điều tốt đẹp đang chờ bạn ở cuối ngày!",
          "Tinh thần vẫn ổn chứ? Bạn đang đi đúng hướng!",
          "Một chút động lực buổi chiều: Bạn làm được!",
          "Buổi chiều là cơ hội thứ hai để hoàn thành mục tiêu!",
          "Chúc bạn một buổi chiều năng suất và hiệu quả!"
        ],
        evening: [
          "Chúc bạn một buổi tối thư giãn!",
          "Hôm nay bạn đã cố gắng rất nhiều – tuyệt vời!",
          "Đã đến lúc bạn nghỉ ngơi và nạp lại năng lượng.",
          "Cảm ơn vì một ngày làm việc đầy cống hiến!",
          "Bạn xứng đáng được nghỉ ngơi – nghỉ sớm nhé!",
          "Mỗi nỗ lực hôm nay là bước đệm cho thành công ngày mai.",
          "Thời gian để tạm dừng và tự hào về những gì bạn đã làm!",
          "Kết thúc một ngày bằng lòng biết ơn và sự nhẹ nhõm.",
          "Bạn đã làm rất tốt hôm nay – chúc ngủ ngon!",
          "Chúc bạn một buổi tối bình yên và tràn đầy yêu thương!"
        ]
      },
      branch_list: [],
      searchData: {
        arr_branch: "",
        branch_id:"",
      },
      totalRevenueWeek: {},
      totalRevenueMonth: {},
      numDashStudent: {},
      numDashClass: {},
      pieChartStudent: {
        series: [],
        chartOptions: {
          // chart: {
          //   type: 'pie',
          //   events: {
          //     dataPointSelection: (event, chartContext, config) => {
          //       const dataPointIndex = config.dataPointIndex;
          //       const links = [
          //         "https://example.com/student-1",
          //         "https://example.com/student-2",
          //         "https://example.com/student-3"
          //       ];

          //       if (links[dataPointIndex]) {
          //         window.open(links[dataPointIndex], '_blank');
          //       }
          //     }
          //   }
          // },
          labels: [], 
          legend: {
            position: 'bottom',  
          },
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
          legend: {
            position: 'bottom',  
          },
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
      pieChartFee: {
        series: [],
        chartOptions: {
          labels: [], 
          legend: {
            position: 'bottom',  
          },
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
      lineChartRevenueRenew:{
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
        chartOptions: {
          xaxis: {
            type: 'text',
            categories: [],
          },
          yaxis: {
            labels: {
              formatter: function (value) {
                return value + " %";
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
      lineChartRenewCM: {
        series: [],
        chartOptions: {
          chart: {
            type: 'bar',
            height: 350,
            stacked: true,
          },
          stroke: {
            width: 1,
            colors: ['#fff']
          },
          dataLabels: {
            formatter: (val) => {
              return val + ' học sinh'
            }
          },
          plotOptions: {
            bar: {
              horizontal: false
            }
          },
          xaxis: {
            categories: []
          },
          fill: {
            opacity: 1
          },
          // 🎨 Áp dụng màu riêng cho từng series
          colors: [
            '#008FFB', '#80c7fd',  // Q1: xanh
            '#00E396', '#80f1cb',  // Q2: xanh lá
            '#FEB019', '#FFE0A3'   // Q3: vàng
          ],
          yaxis: {
            labels: {
              formatter: (val) => {
                return val + ' học sinh'
              }
            }
          },
          legend: {
            position: 'top',
            horizontalAlign: 'left'
          }
        }
      },
      dataStudentTrial:[],
      dataStudentDeposit:[],
      dataStudentPending:[],
      dataStudentWithdraw:[],
      revenueTableData: [],
      totalRevenueTable: {
        today: 0,
        days3: 0,
        month: 0,
        months3: 0
      },
      operationsTableData: [],
      totalOperations: {
        checkin: 0,
        registered: 0,
        expired: 0,
        active: 0,
        classes: 0,
        acsRatio: '0'
      }
    }
  },
  created () {
    this.getTimeGreeting();
    axios.g(`/api/system/branches-has-user`)
      .then(response => {
      this.branch_list = response.data
    })
    this.loadData();
  },
  methods: {
    getTimeGreeting() {
      const hour = new Date().getHours();
      let period = "morning";
      if (hour >= 12 && hour < 18) period = "afternoon";
      else if (hour >= 18 || hour < 5) period = "evening";

      const greetings = this.greetingsByTime[period];
      this.textGreeting = greetings[Math.floor(Math.random() * greetings.length)];
    },
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
        this.totalRevenueWeek = {
          analyticsData: {
            data: response.data.totalRevenueWeek.data
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
        this.totalRevenueMonth = {
          analyticsData: {
            data: response.data.totalRevenueMonth.data
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
        this.numDashStudent = {
          analyticsData: {
            data: response.data.numDashStudent.data
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
        this.numDashClass = {
          analyticsData: {
            data: response.data.numDashClass.data
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
            labels: ['Học chính thức', 'Học thử', 'Bảo lưu & pending', 'Hết phí'], 
            colors: ['#28C76F', '#1E1E1E', '#7367F0', '#EA5455'],
            legend: {
              position: 'bottom',  
            },
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
        this.pieChartFee = {
          series: [response.data.pieChartFee.fullFee, response.data.pieChartFee.init, response.data.pieChartFee.deposit, response.data.pieChartFee.withdraw],
          chartOptions: {
            labels: ['Full fee', 'Chưa đóng phí', 'Đặt cọc', 'Hết phí'], 
            colors: ['#28C76F', '#1E1E1E', '#7367F0', '#EA5455'],
            legend: {
              position: 'bottom',  
            },
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
            legend: {
              position: 'bottom',  
            },
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
        this.lineChartRevenueRenew = {
          chartOptions: {
            xaxis: {
              type: 'text',
              categories: response.data.lineChartRevenueRenew.categories,
            },
          },
          series: response.data.lineChartRevenueRenew.series
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
        this.lineChartRenew = {
          chartOptions: {
            xaxis: {
              type: 'text',
              categories: response.data.lineChartRenew.categories,
            },
          },
          series: response.data.lineChartRenew.series
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
        this.lineChartRenewCM = {
          series: response.data.lineChartRenewCM.series,
          chartOptions: {
            chart: {
              type: 'bar',
              height: 350,
              stacked: true,
            },
            stroke: {
              width: 1,
              colors: ['#fff']
            },
            dataLabels: {
              formatter: (val) => {
                return val + ' học sinh'
              }
            },
            plotOptions: {
              bar: {
                horizontal: false
              }
            },
            xaxis: {
              categories: response.data.lineChartRenewCM.categories
            },
            fill: {
              opacity: 1
            },
            // 🎨 Áp dụng màu riêng cho từng series
            colors: [
              '#008FFB', '#80c7fd',  // Q1: xanh
              '#00E396', '#80f1cb',  // Q2: xanh lá
              '#FEB019', '#FFE0A3'   // Q3: vàng
            ],
            yaxis: {
              labels: {
                formatter: (val) => {
                  return val + ' học sinh'
                }
              }
            },
            legend: {
              position: 'top',
              horizontalAlign: 'left'
            }
          }
        }
      })
    },
    loadDataDashboard13(){
      const ids_branch = []
      if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
        this.searchData.arr_branch.map(item => {
          ids_branch.push(item.id)
        })
      }
      this.searchData.branch_id = ids_branch
      this.$vs.loading()
      axios.p(`/api/dashboard/13`,{
        branch_id: this.searchData.branch_id,
      })
      .then(response => {
        this.$vs.loading.close()
        this.dataStudentTrial = response.data
      })
    },
    loadDataDashboard14(){
      const ids_branch = []
      if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
        this.searchData.arr_branch.map(item => {
          ids_branch.push(item.id)
        })
      }
      this.searchData.branch_id = ids_branch
      this.$vs.loading()
      axios.p(`/api/dashboard/14`,{
        branch_id: this.searchData.branch_id,
      })
      .then(response => {
        this.$vs.loading.close()
        this.dataStudentDeposit = response.data
      })
    },
    loadDataDashboard15(){
      const ids_branch = []
      if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
        this.searchData.arr_branch.map(item => {
          ids_branch.push(item.id)
        })
      }
      this.searchData.branch_id = ids_branch
      this.$vs.loading()
      axios.p(`/api/dashboard/15`,{
        branch_id: this.searchData.branch_id,
      })
      .then(response => {
        this.$vs.loading.close()
        this.dataStudentPending = response.data
      })
    },
    loadDataDashboard16(){
      const ids_branch = []
      if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
        this.searchData.arr_branch.map(item => {
          ids_branch.push(item.id)
        })
      }
      this.searchData.branch_id = ids_branch
      this.$vs.loading()
      axios.p(`/api/dashboard/16`,{
        branch_id: this.searchData.branch_id,
      })
      .then(response => {
        this.$vs.loading.close()
        this.dataStudentWithdraw = response.data
      })
    },
    loadData(){
      this.loadDataDashboard17();
      this.loadDataDashboard18();
    },
    loadDataDashboard17(){
      const ids_branch = []
      if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
        this.searchData.arr_branch.map(item => {
          ids_branch.push(item.id)
        })
      }
      this.searchData.branch_id = ids_branch
      this.$vs.loading()
      axios.p(`/api/dashboard/17`,{
        branch_id: this.searchData.branch_id,
      })
      .then(response => {
        this.$vs.loading.close()
        this.revenueTableData = response.data
        this.calculateTotalRevenue()
      })
      .catch(error => {
        this.$vs.loading.close()
        console.error('Error loading revenue table:', error)
      })
    },
    calculateTotalRevenue() {
      this.totalRevenueTable = {
        today: 0,
        days3: 0,
        month: 0,
        months3: 0
      }
      
      this.revenueTableData.forEach(item => {
        this.totalRevenueTable.today += item.revenue_today
        this.totalRevenueTable.days3 += item.revenue_3days
        this.totalRevenueTable.month += item.revenue_month
        this.totalRevenueTable.months3 += item.revenue_3months
      })
    },
    loadDataDashboard18(){
      const ids_branch = []
      if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
        this.searchData.arr_branch.map(item => {
          ids_branch.push(item.id)
        })
      }
      this.searchData.branch_id = ids_branch
      this.$vs.loading()
      axios.p(`/api/dashboard/18`,{
        branch_id: this.searchData.branch_id,
      })
      .then(response => {
        this.$vs.loading.close()
        this.operationsTableData = response.data
        this.calculateTotalOperations()
      })
      .catch(error => {
        this.$vs.loading.close()
        console.error('Error loading operations table:', error)
      })
    },
    calculateTotalOperations() {
      this.totalOperations = {
        checkin: 0,
        registered: 0,
        expired: 0,
        active: 0,
        classes: 0,
        acsRatio: '0'
      }
      
      this.operationsTableData.forEach(item => {
        this.totalOperations.checkin += item.checkin_students
        this.totalOperations.registered += item.registered_students
        this.totalOperations.expired += item.expired_students
        this.totalOperations.active += item.active_students
        this.totalOperations.classes += item.total_classes
      })
      
      // Calculate average ACS ratio
      if (this.totalOperations.classes > 0) {
        this.totalOperations.acsRatio = (this.totalOperations.active / this.totalOperations.classes).toFixed(2)
      }
    }
  }
}
</script>

<style lang="scss">
.dash-select-branch {
  position: relative;
  z-index: 1000;
}
.dash-select-branch .multiselect.vs-inputx.vs-input--input{
  padding: 0px;
  border: none;
}
.dash-select-branch .multiselect__tags{
  border-top-right-radius: 0px;
  border-bottom-right-radius: 0px;
}
.dash-select-branch .multiselect__content-wrapper{
  z-index: 10000 !important;
  position: absolute !important;
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

  // Revenue Table Styles
  .revenue-amount {
    font-weight: 600;
    color: #2c3e50;
  }

  .highlight-month {
    color: #f39c12 !important;
    font-weight: 700;
  }

  .highlight-3months {
    color: #e74c3c !important;
    font-weight: 700;
  }

  .total-row {
    background-color: #f8f9fa;
    border-top: 2px solid #dee2e6;
    font-size: 1.05rem;
  }

  .total-row td {
    padding: 15px 10px !important;
  }

  .text-muted {
    color: #a0aec0;
  }

  // Ensure cards don't cover dropdown
  .vx-card {
    position: relative;
    z-index: 1;
  }

  // Operations Table Styles
  .ops-number {
    font-weight: 700;
    font-size: 1.1rem;
    display: block;
    margin-bottom: 5px;
  }

  .ops-note {
    font-size: 0.75rem;
    color: #718096;
    font-style: italic;
    line-height: 1.3;
  }

  .highlight-warning {
    color: #f39c12 !important;
  }

  .highlight-success {
    color: #28c76f !important;
  }

  .highlight-primary {
    color: #7367f0 !important;
  }
}
/*! rtl:end:ignore */
</style>
