<!-- =========================================================================================
  File Name: DashboardAnalytics.vue
  Description: Dashboard Analytics
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->

<template>
  <div id="dashboard-analytics" v-if="overviewData">
    <!-- GREETING CARD (Common for all roles) -->
    <div class="vx-row mb-6">
      <div class="vx-col w-full">
        <vx-card slot="no-body" class="p-6 text-white rounded-lg shadow-lg relative overflow-hidden h-full" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);">
          <div class="relative z-10 flex items-center justify-between">
            <div>
              <h2 class="text-3xl font-bold mb-2 text-white">Xin chào, {{ $store.state.AppActiveUser.displayName }}! 👋</h2>
              <p class="text-lg text-white opacity-90">{{ textGreeting }}</p>
            </div>
            <div class="hidden sm:flex items-center justify-center p-4 bg-white rounded-full shadow-md" style="color: #4f46e5;">
              <feather-icon icon="AwardIcon" svgClasses="w-10 h-10"></feather-icon>
            </div>
          </div>
          <img src="@assets/images/elements/decore-left.png" class="absolute left-0 top-0 opacity-50 pointer-events-none" alt="Decore Left" width="200" />
          <img src="@assets/images/elements/decore-right.png" class="absolute right-0 top-0 opacity-50 pointer-events-none" alt="Decore Right" width="175" />
        </vx-card>
      </div>
    </div>

    <!-- SALES DASHBOARD -->
    <template v-if="overviewData.is_sales && !overviewData.is_admin">
      <div class="mb-5 flex items-center">
        <feather-icon icon="TrendingUpIcon" class="mr-2 text-primary" svgClasses="w-6 h-6"></feather-icon>
        <h3 class="text-xl font-bold text-gray-800">Hiệu suất Cá nhân</h3>
      </div>
      
      <!-- Current Month KPIs -->
      <h4 class="mb-3 text-lg font-semibold text-gray-700">Tháng hiện tại</h4>
      <div class="vx-row mb-base flex items-stretch">
        <!-- New Contracts -->
        <div class="vx-col w-full sm:w-1/3 mb-4">
          <vx-card class="shadow-md border-t-4 border-success h-full flex flex-col justify-between">
            <div class="flex justify-between items-center mb-4">
              <h4 class="text-gray-500 font-medium uppercase text-sm">Hợp đồng Mới</h4>
              <feather-icon icon="FilePlusIcon" class="text-success bg-success-light p-2 rounded-lg" svgClasses="w-5 h-5"></feather-icon>
            </div>
            <div class="flex items-end justify-between mt-auto">
              <div>
                <h2 class="text-3xl font-bold text-success">{{ overviewData.sales.current_month.new_contracts }} <span class="text-base font-normal text-gray-500">HĐ</span></h2>
              </div>
              <div class="text-right">
                <span class="text-sm text-gray-500">Giá trị: </span>
                <span class="font-bold text-success">{{ (overviewData.sales.current_month.new_contracts_value / 1000000) | formatNumber }}tr</span>
              </div>
            </div>
          </vx-card>
        </div>
        
        <!-- Up-level Contracts -->
        <div class="vx-col w-full sm:w-1/3 mb-4">
          <vx-card class="shadow-md border-t-4 border-primary h-full flex flex-col justify-between">
            <div class="flex justify-between items-center mb-4">
              <h4 class="text-gray-500 font-medium uppercase text-sm">Hợp đồng Up-level</h4>
              <feather-icon icon="ArrowUpCircleIcon" class="text-primary bg-primary-light p-2 rounded-lg" svgClasses="w-5 h-5"></feather-icon>
            </div>
            <div class="flex items-end justify-between mt-auto">
              <div>
                <h2 class="text-3xl font-bold text-primary">{{ overviewData.sales.current_month.uplevel_contracts }} <span class="text-base font-normal text-gray-500">HĐ</span></h2>
              </div>
              <div class="text-right">
                <span class="text-sm text-gray-500">Giá trị: </span>
                <span class="font-bold text-primary">{{ (overviewData.sales.current_month.uplevel_contracts_value / 1000000) | formatNumber }}tr</span>
              </div>
            </div>
          </vx-card>
        </div>
        
        <!-- Generated Revenue -->
        <div class="vx-col w-full sm:w-1/3 mb-4">
          <vx-card class="shadow-md border-t-4 border-warning h-full flex flex-col justify-between">
            <div class="flex justify-between items-center mb-4">
              <h4 class="text-gray-500 font-medium uppercase text-sm">Doanh thu phát sinh</h4>
              <feather-icon icon="DollarSignIcon" class="text-warning bg-warning-light p-2 rounded-lg" svgClasses="w-5 h-5"></feather-icon>
            </div>
            <div class="flex items-end justify-between mt-auto">
              <div>
                <h2 class="text-3xl font-bold text-warning">{{ (overviewData.sales.current_month.revenue / 1000000) | formatNumber }} <span class="text-base font-normal text-gray-500">tr</span></h2>
              </div>
            </div>
          </vx-card>
        </div>
      </div>

      <!-- Current Year KPIs -->
      <h4 class="mb-3 text-lg font-semibold text-gray-700">Lũy kế năm hiện tại</h4>
      <div class="vx-row mb-base flex items-stretch">
        <!-- Total Contracts -->
        <div class="vx-col w-full sm:w-1/3 mb-4">
          <vx-card class="shadow-md h-full flex flex-col justify-center">
            <div class="flex items-center h-full">
              <div class="p-3 bg-primary-light text-primary rounded-lg mr-4">
                <feather-icon icon="LayersIcon" svgClasses="w-6 h-6"></feather-icon>
              </div>
              <div class="w-full flex justify-between items-center">
                <div>
                  <p class="text-gray-500 font-medium text-sm uppercase">Tổng số HĐ</p>
                  <h2 class="text-2xl font-bold text-primary">{{ overviewData.sales.current_year.total_contracts }}</h2>
                </div>
                <div class="text-right">
                  <p class="text-sm text-gray-500">G/trị: <span class="font-bold">{{ (overviewData.sales.current_year.total_contracts_value / 1000000) | formatNumber }}tr</span></p>
                </div>
              </div>
            </div>
          </vx-card>
        </div>

        <!-- Total Revenue -->
        <div class="vx-col w-full sm:w-1/3 mb-4">
          <vx-card class="shadow-md h-full flex flex-col justify-center">
            <div class="flex items-center h-full">
              <div class="p-3 bg-success-light text-success rounded-lg mr-4">
                <feather-icon icon="CreditCardIcon" svgClasses="w-6 h-6"></feather-icon>
              </div>
              <div>
                <p class="text-gray-500 font-medium text-sm uppercase">Tổng Doanh thu</p>
                <h2 class="text-2xl font-bold text-success">{{ (overviewData.sales.current_year.total_revenue / 1000000) | formatNumber }} tr</h2>
              </div>
            </div>
          </vx-card>
        </div>

        <!-- Total Debt -->
        <div class="vx-col w-full sm:w-1/3 mb-4">
          <vx-card class="shadow-md bg-danger-gradient text-white h-full flex flex-col justify-center">
            <div class="flex items-center h-full">
              <div class="p-3 rounded-lg mr-4" style="background: rgba(0,0,0,0.1);">
                <feather-icon icon="AlertTriangleIcon" svgClasses="w-6 h-6"></feather-icon>
              </div>
              <div>
                <p class="font-medium text-sm uppercase opacity-80">Tổng Công nợ</p>
                <h2 class="text-2xl font-bold text-white">{{ (overviewData.sales.current_year.total_debt / 1000000) | formatNumber }} tr</h2>
              </div>
            </div>
          </vx-card>
        </div>
      </div>
    </template>


    <!-- LEADER DASHBOARD -->
    <template v-if="overviewData.is_leader && !overviewData.is_admin">
      <div class="mb-5 flex items-center mt-8">
        <feather-icon icon="UsersIcon" class="mr-2 text-primary" svgClasses="w-6 h-6"></feather-icon>
        <h3 class="text-xl font-bold text-gray-800">Hiệu suất Đội Nhóm (Team)</h3>
      </div>
      
      <!-- Current Month KPIs Leader -->
      <h4 class="mb-3 text-lg font-semibold text-gray-700">Tháng hiện tại</h4>
      <div class="vx-row mb-base flex items-stretch">
        <!-- New Contracts -->
        <div class="vx-col w-full sm:w-1/3 mb-4">
          <vx-card class="shadow-md border-t-4 border-success h-full flex flex-col justify-between">
            <div class="flex justify-between items-center mb-4">
              <h4 class="text-gray-500 font-medium uppercase text-sm">Hợp đồng Mới</h4>
              <feather-icon icon="FilePlusIcon" class="text-success bg-success-light p-2 rounded-lg" svgClasses="w-5 h-5"></feather-icon>
            </div>
            <div class="flex items-end justify-between mt-auto">
              <div>
                <h2 class="text-3xl font-bold text-success">{{ overviewData.leader.current_month.new_contracts }} <span class="text-base font-normal text-gray-500">HĐ</span></h2>
              </div>
              <div class="text-right">
                <span class="text-sm text-gray-500">Giá trị: </span>
                <span class="font-bold text-success">{{ (overviewData.leader.current_month.new_contracts_value / 1000000) | formatNumber }}tr</span>
              </div>
            </div>
          </vx-card>
        </div>
        
        <!-- Up-level Contracts -->
        <div class="vx-col w-full sm:w-1/3 mb-4">
          <vx-card class="shadow-md border-t-4 border-primary h-full flex flex-col justify-between">
            <div class="flex justify-between items-center mb-4">
              <h4 class="text-gray-500 font-medium uppercase text-sm">Hợp đồng Up-level</h4>
              <feather-icon icon="ArrowUpCircleIcon" class="text-primary bg-primary-light p-2 rounded-lg" svgClasses="w-5 h-5"></feather-icon>
            </div>
            <div class="flex items-end justify-between mt-auto">
              <div>
                <h2 class="text-3xl font-bold text-primary">{{ overviewData.leader.current_month.uplevel_contracts }} <span class="text-base font-normal text-gray-500">HĐ</span></h2>
              </div>
              <div class="text-right">
                <span class="text-sm text-gray-500">Giá trị: </span>
                <span class="font-bold text-primary">{{ (overviewData.leader.current_month.uplevel_contracts_value / 1000000) | formatNumber }}tr</span>
              </div>
            </div>
          </vx-card>
        </div>
        
        <!-- Generated Revenue -->
        <div class="vx-col w-full sm:w-1/3 mb-4">
          <vx-card class="shadow-md border-t-4 border-warning h-full flex flex-col justify-between">
            <div class="flex justify-between items-center mb-4">
              <h4 class="text-gray-500 font-medium uppercase text-sm">Doanh thu phát sinh</h4>
              <feather-icon icon="DollarSignIcon" class="text-warning bg-warning-light p-2 rounded-lg" svgClasses="w-5 h-5"></feather-icon>
            </div>
            <div class="flex items-end justify-between mt-auto">
              <div>
                <h2 class="text-3xl font-bold text-warning">{{ (overviewData.leader.current_month.revenue / 1000000) | formatNumber }} <span class="text-base font-normal text-gray-500">tr</span></h2>
              </div>
            </div>
          </vx-card>
        </div>
      </div>

      <!-- Current Year KPIs Leader -->
      <h4 class="mb-3 text-lg font-semibold text-gray-700">Lũy kế năm hiện tại</h4>
      <div class="vx-row mb-base flex items-stretch">
        <!-- Total Contracts -->
        <div class="vx-col w-full sm:w-1/3 mb-4">
          <vx-card class="shadow-md h-full flex flex-col justify-center">
            <div class="flex items-center h-full">
              <div class="p-3 bg-primary-light text-primary rounded-lg mr-4">
                <feather-icon icon="LayersIcon" svgClasses="w-6 h-6"></feather-icon>
              </div>
              <div class="w-full flex justify-between items-center">
                <div>
                  <p class="text-gray-500 font-medium text-sm uppercase">Tổng số HĐ</p>
                  <h2 class="text-2xl font-bold text-primary">{{ overviewData.leader.current_year.total_contracts }}</h2>
                </div>
                <div class="text-right">
                  <p class="text-sm text-gray-500">G/trị: <span class="font-bold">{{ (overviewData.leader.current_year.total_contracts_value / 1000000) | formatNumber }}tr</span></p>
                </div>
              </div>
            </div>
          </vx-card>
        </div>

        <!-- Total Revenue -->
        <div class="vx-col w-full sm:w-1/3 mb-4">
          <vx-card class="shadow-md h-full flex flex-col justify-center">
            <div class="flex items-center h-full">
              <div class="p-3 bg-success-light text-success rounded-lg mr-4">
                <feather-icon icon="CreditCardIcon" svgClasses="w-6 h-6"></feather-icon>
              </div>
              <div>
                <p class="text-gray-500 font-medium text-sm uppercase">Tổng Doanh thu</p>
                <h2 class="text-2xl font-bold text-success">{{ (overviewData.leader.current_year.total_revenue / 1000000) | formatNumber }} tr</h2>
              </div>
            </div>
          </vx-card>
        </div>

        <!-- Total Debt -->
        <div class="vx-col w-full sm:w-1/3 mb-4">
          <vx-card class="shadow-md bg-danger-gradient text-white h-full flex flex-col justify-center">
            <div class="flex items-center h-full">
              <div class="p-3 rounded-lg mr-4" style="background: rgba(0,0,0,0.1);">
                <feather-icon icon="AlertTriangleIcon" svgClasses="w-6 h-6"></feather-icon>
              </div>
              <div>
                <p class="font-medium text-sm uppercase opacity-80">Tổng Công nợ</p>
                <h2 class="text-2xl font-bold text-white">{{ (overviewData.leader.current_year.total_debt / 1000000) | formatNumber }} tr</h2>
              </div>
            </div>
          </vx-card>
        </div>
      </div>
      
                        <!-- Leaderboard & Chart -->
      <div class="vx-row mb-base mt-8 flex flex-wrap items-stretch">
        <!-- Table -->
        <div class="vx-col w-full lg:w-7/12 mb-4">
          <vx-card class="shadow-md border-0 h-full" title="🏆 Top Thành Viên">
            <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
              <table class="w-full text-left border-collapse border border-gray-200">
                <thead class="sticky top-0 z-10 shadow-sm">
                  <tr class="text-gray-500 uppercase text-xs tracking-wider border-b border-gray-200">
                    <th class="py-3 px-3 font-semibold text-center border-r border-b border-gray-200 w-16" style="background-color: #f8f8f8;">STT</th>
                    <th class="py-3 px-3 font-semibold border-r border-b border-gray-200" style="background-color: #f8f8f8;">Thành viên</th>
                    <th class="py-3 px-3 font-semibold text-center w-24 border-r border-b border-gray-200" style="background-color: #f8f8f8;">Hợp đồng</th>
                    <th class="py-3 px-3 font-semibold w-1/4 border-r border-b border-gray-200" style="background-color: #f8f8f8;">Tiến độ</th>
                    <th class="py-3 px-3 font-semibold text-right border-b border-gray-200" style="background-color: #f8f8f8;">Doanh thu</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(mem, idx) in overviewData.leader.members" :key="idx" class="border-b border-gray-200 hover:bg-gray-50 transition-colors">
                    <td class="py-3 px-3 text-center border-r border-gray-200">
                      <div class="font-bold text-gray-600">
                        {{ idx + 1 }}
                      </div>
                    </td>
                    <td class="py-3 px-3 border-r border-gray-200">
                      <div class="flex items-center">
                        <div class="w-8 h-8 rounded bg-primary-light text-primary flex items-center justify-center font-bold mr-3 shadow-sm text-xs">
                          {{ mem.name.charAt(0).toUpperCase() }}
                        </div>
                        <div>
                          <p class="font-bold text-gray-800 text-sm">{{ mem.name }}</p>
                          <p class="text-xs text-gray-500">Sales</p>
                        </div>
                      </div>
                    </td>
                    <td class="py-3 px-3 text-center border-r border-gray-200">
                      <span class="font-medium text-gray-600">{{ mem.contracts }}</span>
                    </td>
                    <td class="py-3 px-3 border-r border-gray-200">
                      <div class="flex items-center justify-between text-xs mb-1">
                        <span class="font-bold text-gray-700">{{ ((mem.revenue / (overviewData.leader.members[0].revenue || 1)) * 100).toFixed(0) }}%</span>
                      </div>
                      <div class="w-full bg-gray-200 rounded-full h-1.5">
                        <div class="bg-primary h-1.5 rounded-full" :style="'width: ' + ((mem.revenue / (overviewData.leader.members[0].revenue || 1)) * 100) + '%'"></div>
                      </div>
                    </td>
                    <td class="py-3 px-3 text-right">
                      <div class="flex items-center justify-end text-success font-bold text-sm">
                        {{ mem.revenue | formatNumber }} đ
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </vx-card>
        </div>
        
        <!-- Chart -->
        <div class="vx-col w-full lg:w-5/12 mb-4">
          <vx-card class="shadow-md h-full" title="📈 Doanh thu 6 tháng gần nhất">
            <div style="height: 380px;">
              <vue-apex-charts 
                type="bar" 
                height="100%" 
                :options="{
                  chart: { toolbar: { show: false } },
                  colors: ['#7367F0'],
                  plotOptions: { bar: { borderRadius: 4, columnWidth: '45%' } },
                  dataLabels: { enabled: false },
                  xaxis: { categories: overviewData.leader.chart_6m.labels },
                  yaxis: { labels: { formatter: function (val) { return (val / 1000000).toFixed(0) + ' tr' } } }
                }" 
                :series="[{ name: 'Doanh thu', data: overviewData.leader.chart_6m.data }]">
              </vue-apex-charts>
            </div>
          </vx-card>
        </div>
      </div>
    </template>

    <!-- ADMIN DASHBOARD -->
    <template v-if="overviewData.is_admin">
      <div class="mb-5 flex items-center mt-8">
        <feather-icon icon="GlobeIcon" class="mr-2 text-primary" svgClasses="w-6 h-6"></feather-icon>
        <h3 class="text-xl font-bold text-gray-800">Tổng quan Hệ thống</h3>
      </div>

      <!-- Branch selection -->
      <div class="vx-row">
        <div class="vx-col w-full mb-4 dash-select-branch">
          <vx-input-group class="mb-base shadow-md rounded-lg bg-white p-2">
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
      
      <!-- Current Month KPIs Admin -->
      <h4 class="mb-3 text-lg font-semibold text-gray-700">Tháng hiện tại</h4>
      <div class="vx-row mb-base flex items-stretch">
        <!-- New Contracts -->
        <div class="vx-col w-full sm:w-1/3 mb-4">
          <vx-card class="shadow-md border-t-4 border-success h-full flex flex-col justify-between">
            <div class="flex justify-between items-center mb-4">
              <h4 class="text-gray-500 font-medium uppercase text-sm">Hợp đồng Mới</h4>
              <feather-icon icon="FilePlusIcon" class="text-success bg-success-light p-2 rounded-lg" svgClasses="w-5 h-5"></feather-icon>
            </div>
            <div class="flex items-end justify-between mt-auto">
              <div>
                <h2 class="text-3xl font-bold text-success">{{ overviewData.admin.current_month.new_contracts }} <span class="text-base font-normal text-gray-500">HĐ</span></h2>
              </div>
              <div class="text-right">
                <span class="text-sm text-gray-500">Giá trị: </span>
                <span class="font-bold text-success">{{ (overviewData.admin.current_month.new_contracts_value / 1000000) | formatNumber }}tr</span>
              </div>
            </div>
          </vx-card>
        </div>
        
        <!-- Up-level Contracts -->
        <div class="vx-col w-full sm:w-1/3 mb-4">
          <vx-card class="shadow-md border-t-4 border-primary h-full flex flex-col justify-between">
            <div class="flex justify-between items-center mb-4">
              <h4 class="text-gray-500 font-medium uppercase text-sm">Hợp đồng Up-level</h4>
              <feather-icon icon="ArrowUpCircleIcon" class="text-primary bg-primary-light p-2 rounded-lg" svgClasses="w-5 h-5"></feather-icon>
            </div>
            <div class="flex items-end justify-between mt-auto">
              <div>
                <h2 class="text-3xl font-bold text-primary">{{ overviewData.admin.current_month.uplevel_contracts }} <span class="text-base font-normal text-gray-500">HĐ</span></h2>
              </div>
              <div class="text-right">
                <span class="text-sm text-gray-500">Giá trị: </span>
                <span class="font-bold text-primary">{{ (overviewData.admin.current_month.uplevel_contracts_value / 1000000) | formatNumber }}tr</span>
              </div>
            </div>
          </vx-card>
        </div>
        
        <!-- Generated Revenue -->
        <div class="vx-col w-full sm:w-1/3 mb-4">
          <vx-card class="shadow-md border-t-4 border-warning h-full flex flex-col justify-between">
            <div class="flex justify-between items-center mb-4">
              <h4 class="text-gray-500 font-medium uppercase text-sm">Doanh thu phát sinh</h4>
              <feather-icon icon="DollarSignIcon" class="text-warning bg-warning-light p-2 rounded-lg" svgClasses="w-5 h-5"></feather-icon>
            </div>
            <div class="flex items-end justify-between mt-auto">
              <div>
                <h2 class="text-3xl font-bold text-warning">{{ (overviewData.admin.current_month.revenue / 1000000) | formatNumber }} <span class="text-base font-normal text-gray-500">tr</span></h2>
              </div>
            </div>
          </vx-card>
        </div>
      </div>

      <!-- Current Year KPIs Admin -->
      <h4 class="mb-3 text-lg font-semibold text-gray-700">Lũy kế năm hiện tại</h4>
      <div class="vx-row mb-base flex items-stretch">
        <!-- Total Contracts -->
        <div class="vx-col w-full sm:w-1/3 mb-4">
          <vx-card class="shadow-md h-full flex flex-col justify-center">
            <div class="flex items-center h-full">
              <div class="p-3 bg-primary-light text-primary rounded-lg mr-4">
                <feather-icon icon="LayersIcon" svgClasses="w-6 h-6"></feather-icon>
              </div>
              <div class="w-full flex justify-between items-center">
                <div>
                  <p class="text-gray-500 font-medium text-sm uppercase">Tổng số HĐ</p>
                  <h2 class="text-2xl font-bold text-primary">{{ overviewData.admin.current_year.total_contracts }}</h2>
                </div>
                <div class="text-right">
                  <p class="text-sm text-gray-500">G/trị: <span class="font-bold">{{ (overviewData.admin.current_year.total_contracts_value / 1000000) | formatNumber }}tr</span></p>
                </div>
              </div>
            </div>
          </vx-card>
        </div>

        <!-- Total Revenue -->
        <div class="vx-col w-full sm:w-1/3 mb-4">
          <vx-card class="shadow-md h-full flex flex-col justify-center">
            <div class="flex items-center h-full">
              <div class="p-3 bg-success-light text-success rounded-lg mr-4">
                <feather-icon icon="CreditCardIcon" svgClasses="w-6 h-6"></feather-icon>
              </div>
              <div>
                <p class="text-gray-500 font-medium text-sm uppercase">Tổng Doanh thu</p>
                <h2 class="text-2xl font-bold text-success">{{ (overviewData.admin.current_year.total_revenue / 1000000) | formatNumber }} tr</h2>
              </div>
            </div>
          </vx-card>
        </div>

        <!-- Total Debt -->
        <div class="vx-col w-full sm:w-1/3 mb-4">
          <vx-card class="shadow-md bg-danger-gradient text-white h-full flex flex-col justify-center">
            <div class="flex items-center h-full">
              <div class="p-3 rounded-lg mr-4" style="background: rgba(0,0,0,0.1);">
                <feather-icon icon="AlertTriangleIcon" svgClasses="w-6 h-6"></feather-icon>
              </div>
              <div>
                <p class="font-medium text-sm uppercase opacity-80">Tổng Công nợ</p>
                <h2 class="text-2xl font-bold text-white">{{ (overviewData.admin.current_year.total_debt / 1000000) | formatNumber }} tr</h2>
              </div>
            </div>
          </vx-card>
        </div>
      </div>
      
      <!-- Student counts -->
      <div class="vx-row mb-base flex items-stretch">
        <div class="vx-col w-full sm:w-1/2 mb-4">
          <vx-card class="shadow-md bg-primary-gradient text-white h-full">
            <div class="flex items-center justify-between">
              <div>
                <p class="font-medium text-sm uppercase opacity-90 mb-1">Tổng số Học sinh (Hệ thống)</p>
                <h2 class="text-4xl font-bold text-white">{{ overviewData.admin.total_students | formatNumber }}</h2>
              </div>
              <feather-icon icon="UsersIcon" svgClasses="w-12 h-12" class="opacity-50"></feather-icon>
            </div>
          </vx-card>
        </div>
        <div class="vx-col w-full sm:w-1/2 mb-4">
          <vx-card class="shadow-md bg-success-gradient text-white h-full">
            <div class="flex items-center justify-between">
              <div>
                <p class="font-medium text-sm uppercase opacity-90 mb-1">Học sinh Đang học</p>
                <h2 class="text-4xl font-bold text-white">{{ overviewData.admin.active_students | formatNumber }}</h2>
              </div>
              <feather-icon icon="UserCheckIcon" svgClasses="w-12 h-12" class="opacity-50"></feather-icon>
            </div>
          </vx-card>
        </div>
      </div>

                        <!-- Admin Teams Leaderboard & Chart -->
      <div class="vx-row mb-base mt-8 flex flex-wrap items-stretch">
        <!-- Table -->
        <div class="vx-col w-full lg:w-7/12 mb-4">
          <vx-card class="shadow-md border-0 h-full" title="🏆 Top Team Sales">
            <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
              <table class="w-full text-left border-collapse border border-gray-200">
                <thead class="sticky top-0 z-10 shadow-sm">
                  <tr class="text-gray-500 uppercase text-xs tracking-wider border-b border-gray-200">
                    <th class="py-3 px-3 font-semibold text-center border-r border-b border-gray-200 w-16" style="background-color: #f8f8f8;">STT</th>
                    <th class="py-3 px-3 font-semibold border-r border-b border-gray-200" style="background-color: #f8f8f8;">Team (Leader)</th>
                    <th class="py-3 px-3 font-semibold text-center w-24 border-r border-b border-gray-200" style="background-color: #f8f8f8;">Hợp đồng</th>
                    <th class="py-3 px-3 font-semibold w-1/4 border-r border-b border-gray-200" style="background-color: #f8f8f8;">Tiến độ</th>
                    <th class="py-3 px-3 font-semibold text-right border-b border-gray-200" style="background-color: #f8f8f8;">Doanh thu</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(team, idx) in overviewData.admin.teams" :key="idx" class="border-b border-gray-200 hover:bg-gray-50 transition-colors">
                    <td class="py-3 px-3 text-center border-r border-gray-200">
                      <div class="font-bold text-gray-600">
                        {{ idx + 1 }}
                      </div>
                    </td>
                    <td class="py-3 px-3 border-r border-gray-200">
                      <div class="flex items-center">
                        <div class="w-8 h-8 rounded bg-warning-light text-warning flex items-center justify-center font-bold mr-3 shadow-sm text-xs">
                          {{ team.name.charAt(0).toUpperCase() }}
                        </div>
                        <div>
                          <p class="font-bold text-gray-800 text-sm">{{ team.name }}</p>
                          <p class="text-xs text-gray-500">Team Sales</p>
                        </div>
                      </div>
                    </td>
                    <td class="py-3 px-3 text-center border-r border-gray-200">
                      <span class="font-medium text-gray-600">{{ team.contracts }}</span>
                    </td>
                    <td class="py-3 px-3 border-r border-gray-200">
                      <div class="flex items-center justify-between text-xs mb-1">
                        <span class="font-bold text-gray-700">{{ ((team.revenue / (overviewData.admin.teams[0].revenue || 1)) * 100).toFixed(0) }}%</span>
                      </div>
                      <div class="w-full bg-gray-200 rounded-full h-1.5">
                        <div class="bg-warning h-1.5 rounded-full" :style="'width: ' + ((team.revenue / (overviewData.admin.teams[0].revenue || 1)) * 100) + '%'"></div>
                      </div>
                    </td>
                    <td class="py-3 px-3 text-right">
                      <div class="flex items-center justify-end text-success font-bold text-sm">
                        {{ team.revenue | formatNumber }} đ
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </vx-card>
        </div>

        <!-- Chart -->
        <div class="vx-col w-full lg:w-5/12 mb-4">
          <vx-card class="shadow-md h-full" title="📈 Doanh thu 6 tháng gần nhất">
            <div style="height: 380px;">
              <vue-apex-charts 
                type="bar" 
                height="100%" 
                :options="{
                  chart: { toolbar: { show: false } },
                  colors: ['#FF9F43'],
                  plotOptions: { bar: { borderRadius: 4, columnWidth: '45%' } },
                  dataLabels: { enabled: false },
                  xaxis: { categories: overviewData.admin.chart_6m.labels },
                  yaxis: { labels: { formatter: function (val) { return (val / 1000000).toFixed(0) + ' tr' } } }
                }" 
                :series="[{ name: 'Doanh thu', data: overviewData.admin.chart_6m.data }]">
              </vue-apex-charts>
            </div>
          </vx-card>
        </div>
      </div>
    </template>
  </div>
</template>


<script>
import Multiselect from "vue-multiselect";
import axios from '../http/axios.js'
import VueApexCharts from 'vue-apexcharts'
import u from '../until/helper.js';

export default {
  components: {
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
      overviewData: null,
      searchData: {
        arr_branch: "",
        branch_id:"",
      },
    }
  },
  created () {
    this.getTimeGreeting()
    this.loadOverview()
    axios.g(`/api/system/branches-has-user`)
      .then(response => {
      this.branch_list = response.data
    })
    this.loadData()
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
    loadData() {
      this.loadOverview();
    },
    loadOverview() {
      const ids_branch = []
      if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
        this.searchData.arr_branch.map(item => {
          ids_branch.push(item.id)
        })
      }
      this.searchData.branch_id = ids_branch;

      this.$vs.loading();
      axios.p('/api/dashboard/overview', { branch_id: this.searchData.branch_id }).then(res => {
        this.$vs.loading.close();
        this.overviewData = res.data;
      }).catch(() => {
        this.$vs.loading.close();
      });
    },
    checkPermission(text){
      return u.checkPermission(this.$store.state.AppActiveUser, text)
    },
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
