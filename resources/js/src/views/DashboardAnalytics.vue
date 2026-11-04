<!-- =========================================================================================
  File Name: DashboardAnalytics.vue
  Description: Dashboard Analytics
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->

<template>
  <div id="dashboard-analytics" class="p-4 dashboard-crm">
    <div class="mb-6 flex justify-between items-center">
      <div>
        <h2 class="text-3xl font-extrabold mb-2 text-primary">Master Dashboard Analytics</h2>
        <p class="text-gray-500 text-lg">Hệ thống báo cáo toàn diện (Tất cả chỉ số cho mọi vai trò)</p>
      </div>
      <div class="w-1/3">
      </div>
    </div>

    <!-- TABS CHO CÁC ROLE -->
    <vs-tabs v-model="activeTab" position="top" class="dashboard-tabs" alignment="fixed">
      
      <!-- ============================================== -->
      <!-- ADMIN / BAN GIÁM ĐỐC -->
      <!-- ============================================== -->
      <vs-tab label="Ban Giám Đốc (Toàn cảnh)" icon="globe">
        <div class="pt-6">
          <h3 class="mb-4 text-primary font-bold"><feather-icon icon="TrendingUpIcon" class="mr-2"/> Chỉ Số Tài Chính & Tăng Trưởng (Macro)</h3>
          <!-- KPI Row 1: 6 Cards -->
          <div class="vx-row mb-base">
            <div class="vx-col w-1/2 md:w-1/3 lg:w-1/6 mb-4">
              <vx-card class="h-full dash-kpi-card text-center cursor-pointer hover:shadow-lg" @click="goToReport('Doanh Thu MTD')">
                <p class="text-gray-500 text-sm font-semibold mb-1">Doanh Thu (MTD)</p>
                <h2 class="text-2xl font-bold text-primary">4.52 Tỷ</h2>
                <span class="text-success text-xs">▲ 15%</span>
              </vx-card>
            </div>
            <div class="vx-col w-1/2 md:w-1/3 lg:w-1/6 mb-4">
              <vx-card class="h-full dash-kpi-card text-center cursor-pointer hover:shadow-lg" @click="goToReport('Lợi Nhuận Gộp')">
                <p class="text-gray-500 text-sm font-semibold mb-1">Lợi Nhuận Gộp</p>
                <h2 class="text-2xl font-bold text-success">38%</h2>
                <span class="text-success text-xs">▲ 2%</span>
              </vx-card>
            </div>
            <div class="vx-col w-1/2 md:w-1/3 lg:w-1/6 mb-4">
              <vx-card class="h-full dash-kpi-card text-center cursor-pointer hover:shadow-lg" @click="goToReport('Học Viên Active')">
                <p class="text-gray-500 text-sm font-semibold mb-1">Học Viên Active</p>
                <h2 class="text-2xl font-bold text-warning">12,500</h2>
                <span class="text-success text-xs">▲ 450 HS</span>
              </vx-card>
            </div>
            <div class="vx-col w-1/2 md:w-1/3 lg:w-1/6 mb-4">
              <vx-card class="h-full dash-kpi-card text-center cursor-pointer hover:shadow-lg" @click="goToReport('Renew Rate')">
                <p class="text-gray-500 text-sm font-semibold mb-1">Renew Rate</p>
                <h2 class="text-2xl font-bold text-primary">78.5%</h2>
                <span class="text-danger text-xs">▼ 1.2%</span>
              </vx-card>
            </div>
            <div class="vx-col w-1/2 md:w-1/3 lg:w-1/6 mb-4">
              <vx-card class="h-full dash-kpi-card text-center cursor-pointer hover:shadow-lg" @click="goToReport('ARPS')">
                <p class="text-gray-500 text-sm font-semibold mb-1">ARPS (Tr/HS)</p>
                <h2 class="text-2xl font-bold text-dark">3.2 Tr</h2>
                <span class="text-success text-xs">▲ 0.1 Tr</span>
              </vx-card>
            </div>
            <div class="vx-col w-1/2 md:w-1/3 lg:w-1/6 mb-4">
              <vx-card class="h-full dash-kpi-card text-center cursor-pointer hover:shadow-lg" @click="goToReport('CAC')">
                <p class="text-gray-500 text-sm font-semibold mb-1">CAC (Chi phí/HS)</p>
                <h2 class="text-2xl font-bold text-danger">850k</h2>
                <span class="text-success text-xs">▼ Giảm 5%</span>
              </vx-card>
            </div>
          </div>

          <!-- Charts Row 2: Finance & Demographics -->
          <div class="vx-row mb-base">
            <div class="vx-col w-full lg:w-1/2 mb-base">
              <vx-card title="Phễu Chuyển Đổi Tổng & LTV" class="h-full">
                <vue-apex-charts type="bar" height="280" :options="adminFunnelOptions" :series="adminFunnelSeries" ></vue-apex-charts>
              </vx-card>
            </div>
            <div class="vx-col w-full lg:w-1/4 mb-base">
              <vx-card title="Churn Rate (Rời bỏ)" class="h-full text-center">
                <vue-apex-charts type="radialBar" height="280" :options="adminChurnOptions" :series="adminChurnSeries" ></vue-apex-charts>
              </vx-card>
            </div>
            <div class="vx-col w-full lg:w-1/4 mb-base">
              <vx-card title="Doanh Thu Theo SP" class="h-full">
                <vue-apex-charts type="donut" height="280" :options="adminProductOptions" :series="adminProductSeries" ></vue-apex-charts>
              </vx-card>
            </div>
          </div>

          <!-- Charts Row 3: Leaderboards & Operations -->
          <h3 class="mb-4 text-primary font-bold mt-8"><feather-icon icon="BarChartIcon" class="mr-2"/> Xếp Hạng & Vận Hành Hệ Thống</h3>
          <div class="vx-row mb-base">
            <div class="vx-col w-full lg:w-1/2 mb-base">
              <vx-card title="Bảng Xếp Hạng TT (Doanh thu & Lấp đầy)" class="h-full">
                <vue-apex-charts type="bar" height="300" :options="adminLeaderboardOptions" :series="adminLeaderboardSeries" ></vue-apex-charts>
              </vx-card>
            </div>
            <div class="vx-col w-full lg:w-1/2 mb-base">
              <vx-card title="Dòng Tiền & Công Nợ & Quỹ Lương" class="h-full">
                <vue-apex-charts type="line" height="300" :options="adminCashflowOptions" :series="adminCashflowSeries" ></vue-apex-charts>
              </vx-card>
            </div>
          </div>

                    <!-- Row 4: Lists -->
          <div class="vx-row">
            <div class="vx-col w-full lg:w-1/3 mb-base">
              <vx-card title="Tỉ Lệ Nhân Sự Giảng Dạy" class="h-full">
                <div class="flex justify-between items-center mb-2">
                    <span class="font-bold flex items-center"><feather-icon icon="UserIcon" class="text-primary mr-2"/> GV Nước Ngoài:</span>
                    <span class="font-bold text-primary">45 <span class="text-sm font-normal text-gray-500">(30%)</span></span>
                </div>
                <vs-progress :percent="30" color="primary"></vs-progress>
                <div class="flex justify-between text-xs text-gray-500 mt-1 mb-6">
                    <span>Phụ trách: 120 Lớp</span>
                    <span>Lương TB: 450k/h</span>
                </div>

                <div class="flex justify-between items-center mb-2 mt-4">
                    <span class="font-bold flex items-center"><feather-icon icon="UserCheckIcon" class="text-success mr-2"/> GV Việt Nam:</span> 
                    <span class="font-bold text-success">105 <span class="text-sm font-normal text-gray-500">(70%)</span></span>
                </div>
                <vs-progress :percent="70" color="success"></vs-progress>
                <div class="flex justify-between text-xs text-gray-500 mt-1">
                    <span>Phụ trách: 350 Lớp</span>
                    <span>Lương TB: 150k/h</span>
                </div>
              </vx-card>
            </div>
            <div class="vx-col w-full lg:w-2/3 mb-base">
              <vx-card title="Cảnh Báo & Vấn Đề Chờ Xử Lý" class="h-full">
                <vs-table :data="[{id: 1, issue: 'Phụ huynh khiếu nại chất lượng lớp PT19, đòi hoàn phí', branch: 'Cầu Giấy', status: 'Đang xử lý', time: '2 giờ trước', pic: 'Nguyễn Văn A'}, {id: 2, issue: 'Chưa thanh toán mặt bằng tháng 7', branch: 'Hà Đông', status: 'Khẩn cấp', time: '1 ngày trước', pic: 'Trần Thị B'}]">
                  <template slot="thead">
                    <vs-th>Sự cố / Vấn đề</vs-th>
                    <vs-th>Cơ sở</vs-th>
                    <vs-th>Phụ trách</vs-th>
                    <vs-th>Mức độ</vs-th>
                    <vs-th>Thao tác</vs-th>
                  </template>
                  <template slot-scope="{data}">
                    <vs-tr :key="indextr" v-for="(tr, indextr) in data">
                      <vs-td>
                        <p class="font-semibold text-dark truncate w-48" :title="data[indextr].issue">{{ data[indextr].issue }}</p>
                        <span class="text-xs text-gray-500">{{ data[indextr].time }}</span>
                      </vs-td>
                      <vs-td>{{ data[indextr].branch }}</vs-td>
                      <vs-td>{{ data[indextr].pic }}</vs-td>
                      <vs-td><vs-chip transparent :color="data[indextr].status === 'Khẩn cấp' ? 'danger' : 'warning'">{{ data[indextr].status }}</vs-chip></vs-td>
                      <vs-td><feather-icon icon="ArrowRightCircleIcon" class="cursor-pointer text-primary" /></vs-td>
                    </vs-tr>
                  </template>
                </vs-table>
              </vx-card>
            </div>
          </div>
        </div>
      </vs-tab>

      <!-- ============================================== -->
      <!-- GIÁM ĐỐC TRUNG TÂM -->
      <!-- ============================================== -->
      <vs-tab label="GĐ Trung Tâm (BM)" icon="store">
        <div class="pt-6">
          <h3 class="mb-4 text-primary font-bold"><feather-icon icon="TargetIcon" class="mr-2"/> Chỉ Tiêu Chi Nhánh</h3>
          
          <div class="vx-row mb-base">
            <!-- 6 KPI Cards -->
            <div class="vx-col w-1/2 md:w-1/3 lg:w-1/6 mb-4">
              <vx-card class="h-full dash-kpi-card text-center cursor-pointer hover:shadow-lg bg-primary-gradient text-white" @click="goToReport('DT vs Chỉ Tiêu')">
                <p class="text-white text-sm font-semibold mb-1 opacity-75">DT vs Chỉ Tiêu</p>
                <h2 class="text-2xl font-bold text-white">85%</h2>
              </vx-card>
            </div>
            <div class="vx-col w-1/2 md:w-1/3 lg:w-1/6 mb-4">
              <vx-card class="h-full dash-kpi-card text-center cursor-pointer hover:shadow-lg" @click="goToReport('Lượt Walk-in')">
                <p class="text-gray-500 text-sm font-semibold mb-1">Lượt Walk-in</p>
                <h2 class="text-2xl font-bold text-success">320</h2>
              </vx-card>
            </div>
            <div class="vx-col w-1/2 md:w-1/3 lg:w-1/6 mb-4">
              <vx-card class="h-full dash-kpi-card text-center cursor-pointer hover:shadow-lg" @click="goToReport('HS Active')">
                <p class="text-gray-500 text-sm font-semibold mb-1">HS Active</p>
                <h2 class="text-2xl font-bold text-primary">850</h2>
              </vx-card>
            </div>
            <div class="vx-col w-1/2 md:w-1/3 lg:w-1/6 mb-4">
              <vx-card class="h-full dash-kpi-card text-center cursor-pointer hover:shadow-lg" @click="goToReport('Occupancy')">
                <p class="text-gray-500 text-sm font-semibold mb-1">Occupancy</p>
                <h2 class="text-2xl font-bold text-warning">88%</h2>
              </vx-card>
            </div>
            <div class="vx-col w-1/2 md:w-1/3 lg:w-1/6 mb-4">
              <vx-card class="h-full dash-kpi-card text-center cursor-pointer hover:shadow-lg" @click="goToReport('HS Đã Renew')">
                <p class="text-gray-500 text-sm font-semibold mb-1">HS Đã Renew</p>
                <h2 class="text-2xl font-bold text-success">145</h2>
              </vx-card>
            </div>
            <div class="vx-col w-1/2 md:w-1/3 lg:w-1/6 mb-4">
              <vx-card class="h-full dash-kpi-card text-center cursor-pointer hover:shadow-lg" @click="goToReport('Điểm NPS')">
                <p class="text-gray-500 text-sm font-semibold mb-1">Điểm NPS</p>
                <h2 class="text-2xl font-bold text-dark">9.2/10</h2>
              </vx-card>
            </div>
          </div>

          <div class="vx-row mb-base">
            <div class="vx-col w-full lg:w-1/2 mb-base">
              <vx-card title="Phễu Chuyển Đổi Nhánh (Checkin -> Full)" class="h-full">
                <vue-apex-charts type="bar" height="280" :options="bmFunnelOptions" :series="bmFunnelSeries" ></vue-apex-charts>
              </vx-card>
            </div>
            <div class="vx-col w-full lg:w-1/2 mb-base">
              <vx-card title="Phân bổ Học viên / Số lớp" class="h-full">
                <vue-apex-charts type="pie" height="280" :options="bmClassOptions" :series="bmClassSeries" ></vue-apex-charts>
              </vx-card>
            </div>
          </div>

          <div class="vx-row mb-base">
            <div class="vx-col w-full lg:w-1/3 mb-base">
              <vx-card title="Xếp Hạng KPI Sale" class="h-full">
                <div class="mt-4 overflow-y-auto pr-2" style="max-height: 320px;">
                  <div v-for="i in 10" :key="'sale-'+i" class="mb-5 flex items-center justify-between">
                    <div class="flex items-center">
                        <vs-avatar :color="i === 1 ? 'success' : (i === 2 ? 'primary' : (i === 3 ? 'warning' : 'dark'))" :text="'S' + i" class="mr-3 shadow-md" />
                        <div>
                            <p class="font-bold text-sm">{{i}}. Nhân viên Sale {{i}}</p>
                            <p class="text-xs text-gray-500" v-if="i === 1">Top 1 Doanh Thu</p>
                            <p class="text-xs text-gray-500" v-else-if="i === 2">Kỷ lục Walk-in</p>
                            <p class="text-xs text-gray-500" v-else-if="i === 3">Best Telesale</p>
                            <p class="text-xs text-gray-500" v-else>Đang nỗ lực bứt phá</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="font-bold text-lg" :class="i === 1 ? 'text-success' : (i === 2 ? 'text-primary' : (i === 3 ? 'text-warning' : 'text-dark'))">{{ 90 - (i-1)*4 }}%</span>
                        <vs-progress :percent="90 - (i-1)*4" :color="i === 1 ? 'success' : (i === 2 ? 'primary' : (i === 3 ? 'warning' : 'dark'))" class="mt-1" height="6"></vs-progress>
                    </div>
                  </div>
                </div>
              </vx-card>
            </div>
            <div class="vx-col w-full lg:w-1/3 mb-base">
              <vx-card title="Xếp Hạng Tái Phí CM" class="h-full">
                <div class="mt-4 overflow-y-auto pr-2" style="max-height: 320px;">
                  <div v-for="i in 10" :key="'cm-'+i" class="mb-5 flex items-center justify-between">
                    <div class="flex items-center">
                        <vs-avatar :color="i === 1 ? 'success' : (i === 2 ? 'primary' : (i === 3 ? 'warning' : 'dark'))" :icon="i === 1 ? 'icon-star' : (i === 2 ? 'icon-user-check' : 'icon-heart')" icon-pack="feather" class="mr-3 shadow-md" />
                        <div>
                            <p class="font-bold text-sm">{{i}}. Chăm sóc KH {{i}}</p>
                            <p class="text-xs text-gray-500" v-if="i === 1">Chăm sóc chuẩn</p>
                            <p class="text-xs text-gray-500" v-else-if="i === 2">Phản hồi nhanh</p>
                            <p class="text-xs text-gray-500" v-else-if="i === 3">Tái phí ổn định</p>
                            <p class="text-xs text-gray-500" v-else>Cần cố gắng thêm</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="font-bold text-lg" :class="i === 1 ? 'text-success' : (i === 2 ? 'text-primary' : (i === 3 ? 'text-warning' : 'text-dark'))">{{ 85 - (i-1)*3 }}%</span>
                        <vs-progress :percent="85 - (i-1)*3" :color="i === 1 ? 'success' : (i === 2 ? 'primary' : (i === 3 ? 'warning' : 'dark'))" class="mt-1" height="6"></vs-progress>
                    </div>
                  </div>
                </div>
              </vx-card>
            </div>
            <div class="vx-col w-full lg:w-1/3 mb-base">
              <vx-card title="Khung Giờ & Chuyên Cần" class="h-full">
                <vue-apex-charts type="radar" height="350" :options="bmRadarOptions" :series="bmRadarSeries" ></vue-apex-charts>
              </vx-card>
            </div>
          </div>

          <div class="vx-row">
            <div class="vx-col w-full lg:w-1/3 mb-base">
              <vx-card class="text-white overflow-hidden relative shadow-lg" style="background: linear-gradient(118deg, #ea5455, rgba(234, 84, 85, 0.7));">
                <feather-icon icon="DollarSignIcon" class="absolute right-0 bottom-0 text-white opacity-25" size="120" style="right: -20px; bottom: -20px;" />
                <h3 class="text-white mb-2 font-bold flex items-center"><feather-icon icon="AlertTriangleIcon" class="mr-2"/> Cảnh Báo Công Nợ</h3>
                <h2 class="text-4xl font-extrabold text-white mt-4">120 Tr</h2>
                <p class="mt-2 font-semibold">Cần thu hồi ngay trong tuần này.</p>
                <vs-button color="white" text-color="#ea5455" class="mt-4 w-full font-bold">Xem Danh Sách Nợ</vs-button>
              </vx-card>
            </div>
            <div class="vx-col w-full lg:w-1/3 mb-base">
               <vx-card class="text-white overflow-hidden relative shadow-lg" style="background: linear-gradient(118deg, #ff9f43, rgba(255, 159, 67, 0.7));">
                <feather-icon icon="ClockIcon" class="absolute right-0 bottom-0 text-white opacity-25" size="120" style="right: -20px; bottom: -20px;" />
                <h3 class="text-white mb-2 font-bold flex items-center"><feather-icon icon="BellIcon" class="mr-2"/> HS Sắp Hết Phí</h3>
                <h2 class="text-4xl font-extrabold text-white mt-4">45 HS</h2>
                <p class="mt-2 font-semibold">Học sinh còn dưới 5 buổi.</p>
                <vs-button color="white" text-color="#ff9f43" class="mt-4 w-full font-bold">Gửi Tin Nhắn Loạt</vs-button>
              </vx-card>
            </div>
            <div class="vx-col w-full lg:w-1/3 mb-base">
               <vx-card class="text-white overflow-hidden relative shadow-lg" style="background: linear-gradient(118deg, #28c76f, rgba(40, 199, 111, 0.7));">
                <feather-icon icon="UsersIcon" class="absolute right-0 bottom-0 text-white opacity-25" size="120" style="right: -20px; bottom: -20px;" />
                <h3 class="text-white mb-2 font-bold flex items-center"><feather-icon icon="RefreshCwIcon" class="mr-2"/> Chuyển/Hủy Lớp</h3>
                <h2 class="text-4xl font-extrabold text-white mt-4">12 / 2</h2>
                <p class="mt-2 font-semibold">HS chuyển lớp / Hủy lớp tháng này.</p>
                <vs-button color="white" text-color="#28c76f" class="mt-4 w-full font-bold">Duyệt Yêu Cầu</vs-button>
              </vx-card>
            </div>
          </div>
        </div>
      </vs-tab>

      <!-- ============================================== -->
      <!-- SALE & TUYỂN SINH -->
      <!-- ============================================== -->
      <vs-tab label="Sale & Tư Vấn (EC)" icon="shopping_cart">
        <div class="pt-6">
          <h3 class="mb-4 text-primary font-bold"><feather-icon icon="CrosshairIcon" class="mr-2"/> Mục Tiêu & Hành Động Hằng Ngày</h3>
          
          <div class="vx-row mb-base">
            <!-- KPIs -->
            <div class="vx-col w-1/2 md:w-1/4 lg:w-1/5 mb-4">
               <vx-card class="text-center h-full"><h2 class="text-primary font-bold text-3xl">65%</h2><p>Tiến độ KPI (Goal)</p></vx-card>
            </div>
            <div class="vx-col w-1/2 md:w-1/4 lg:w-1/5 mb-4">
               <vx-card class="text-center h-full"><h2 class="text-success font-bold text-3xl">15</h2><p>New Leads Hôm nay</p></vx-card>
            </div>
            <div class="vx-col w-1/2 md:w-1/4 lg:w-1/5 mb-4">
               <vx-card class="text-center h-full"><h2 class="text-warning font-bold text-3xl">45/60</h2><p>Cuộc gọi Outbound</p></vx-card>
            </div>
            <div class="vx-col w-1/2 md:w-1/4 lg:w-1/5 mb-4">
               <vx-card class="text-center h-full"><h2 class="text-primary font-bold text-3xl">8</h2><p>Lịch hẹn Test</p></vx-card>
            </div>
            <div class="vx-col w-1/2 md:w-1/4 lg:w-1/5 mb-4">
               <vx-card class="text-center h-full"><h2 class="text-success font-bold text-3xl">75%</h2><p>Tỉ lệ Show-up</p></vx-card>
            </div>
          </div>

          <div class="vx-row mb-base">
            <div class="vx-col w-full lg:w-2/3 mb-base">
              <vx-card title="Lịch Sử Chuyển Đổi & Chốt Deal" class="h-full">
                <vue-apex-charts type="area" height="300" :options="saleLineOptions" :series="saleLineSeries" ></vue-apex-charts>
              </vx-card>
            </div>
            <div class="vx-col w-full lg:w-1/3 mb-base">
              <vx-card title="Phân Bổ Hot Lead & Nguồn" class="h-full">
                <vue-apex-charts type="donut" height="300" :options="saleSourceOptions" :series="saleSourceSeries" ></vue-apex-charts>
              </vx-card>
            </div>
          </div>

          <div class="vx-row">
            <div class="vx-col w-full lg:w-1/3 mb-base">
              <vx-card title="Thành Tích Cá Nhân & Thất Bại" class="h-full">
                <div class="flex justify-between items-end mb-2">
                    <div>
                        <p class="text-gray-500 text-sm">Hạng Của Bạn (Nhóm Cầu Giấy)</p>
                        <h3 class="font-bold text-primary text-2xl">#2 <span class="text-lg text-gray-400">/ 15</span></h3>
                    </div>
                    <vs-avatar color="primary" icon="icon-award" icon-pack="feather" />
                </div>
                <vs-progress :percent="85" color="success" class="mb-6"></vs-progress>
                
                <div class="p-3 bg-white rounded-lg border border-danger mt-4">
                    <p class="font-bold text-danger mb-2 flex items-center"><feather-icon icon="TrendingDownIcon" class="mr-2" size="18"/> Hợp Đồng Thất Bại: 5</p>
                    <p class="text-xs text-gray-600 mb-2">Tổng thiệt hại ước tính: <b>150 Triệu VNĐ</b></p>
                    <ul class="ml-4 list-disc text-sm text-gray-700">
                        <li>Khách chê học phí cao (3)</li>
                        <li>Chuyển sang trung tâm đối thủ (1)</li>
                        <li>Sai thông tin liên lạc (1)</li>
                    </ul>
                </div>
              </vx-card>
            </div>
            <div class="vx-col w-full lg:w-1/3 mb-base">
              <vx-card title="Phân Tích Chỉ Số Chốt Sales" class="h-full">
                <div class="mb-6">
                    <p class="text-gray-500 text-sm mb-1">Doanh Thu Tái Phí Đạt Được</p>
                    <div class="flex items-center">
                        <h2 class="text-primary font-bold mr-2">15.000.000đ</h2>
                        <span class="text-success text-xs font-bold bg-white border border-success px-1 py-1 rounded">+5%</span>
                    </div>
                </div>
                <div class="mb-6">
                    <p class="text-gray-500 text-sm mb-1">Thời Gian Chốt Khách TB (Sales Cycle)</p>
                    <div class="flex items-center">
                        <h2 class="text-warning font-bold mr-2">12 Ngày</h2>
                        <span class="text-success text-xs font-bold bg-white border border-success px-1 py-1 rounded">Nhanh hơn 2 ngày</span>
                    </div>
                </div>
                <div>
                    <p class="text-gray-500 text-sm mb-1">Hoa Hồng Dự Kiến Tháng Này</p>
                    <div class="flex items-center">
                        <h2 class="text-success font-bold mr-2">3.500.000đ</h2>
                    </div>
                    <vs-progress :percent="60" color="success" class="mt-2"></vs-progress>
                </div>
              </vx-card>
            </div>
            <div class="vx-col w-full lg:w-1/3 mb-base">
              <vx-card title="Công Việc Cần Theo Dõi" class="h-full">
                <ul class="activity-timeline">
                  <li class="relative pb-4 border-l-2 border-warning pl-6 ml-2">
                    <div class="absolute w-4 h-4 bg-warning rounded-full top-1 border-2 border-white" style="left: -9px;"></div>
                    <div class="flex justify-between">
                        <p class="font-bold text-dark">Gọi lại Chị Hương</p>
                        <span class="text-xs text-gray-500">15:00 Hôm nay</span>
                    </div>
                    <span class="text-sm text-gray-600">Khách hứa chuyển khoản đóng cọc 5 triệu.</span>
                    <div class="mt-2 flex gap-2">
                        <vs-button size="small" type="flat" icon="icon-phone" icon-pack="feather"></vs-button>
                        <vs-button size="small" type="flat" color="success" icon="icon-check" icon-pack="feather"></vs-button>
                    </div>
                  </li>
                  <li class="relative pb-4 border-l-2 border-primary pl-6 ml-2 mt-4">
                    <div class="absolute w-4 h-4 bg-primary rounded-full top-1 border-2 border-white" style="left: -9px;"></div>
                    <div class="flex justify-between">
                        <p class="font-bold text-dark">Xác nhận lịch kiểm tra Anh Tuấn</p>
                        <span class="text-xs text-gray-500">17:00 Hôm nay</span>
                    </div>
                    <span class="text-sm text-gray-600">Kiểm tra đầu vào IELTS 5.0 cho bé Trang.</span>
                  </li>
                </ul>
                <vs-button class="w-full mt-4" type="border" color="primary">Xem tất cả công việc</vs-button>
              </vx-card>
            </div>
          </div>
        </div>
      </vs-tab>
<!-- ============================================== -->
      <!-- CHĂM SÓC KHÁCH HÀNG / HỌC THUẬT -->
      <!-- ============================================== -->
      <vs-tab label="Chăm Sóc Khách Hàng (CM)" icon="heart">
        <div class="pt-6">
          <h3 class="mb-4 text-primary font-bold"><feather-icon icon="SmileIcon" class="mr-2"/> Chất Lượng Trải Nghiệm & Tái Phí</h3>
          
          <div class="vx-row mb-base">
            <div class="vx-col w-1/2 md:w-1/4 lg:w-1/5 mb-4"><vx-card class="text-center h-full"><h2 class="text-primary font-bold text-3xl">12</h2><p>Lớp Quản Lý</p></vx-card></div>
            <div class="vx-col w-1/2 md:w-1/4 lg:w-1/5 mb-4"><vx-card class="text-center h-full"><h2 class="text-success font-bold text-3xl">185</h2><p>Tổng Học Viên</p></vx-card></div>
            <div class="vx-col w-1/2 md:w-1/4 lg:w-1/5 mb-4"><vx-card class="text-center h-full"><h2 class="text-warning font-bold text-3xl">82%</h2><p>Tỉ lệ Renew (Cá nhân)</p></vx-card></div>
            <div class="vx-col w-1/2 md:w-1/4 lg:w-1/5 mb-4"><vx-card class="text-center h-full"><h2 class="text-primary font-bold text-3xl">96%</h2><p>Tỉ lệ Đi học</p></vx-card></div>
            <div class="vx-col w-1/2 md:w-1/4 lg:w-1/5 mb-4"><vx-card class="text-center h-full"><h2 class="text-danger font-bold text-3xl">2</h2><p>Phản hồi chưa XL</p></vx-card></div>
          </div>

          <div class="vx-row mb-base">
            <div class="vx-col w-full lg:w-1/2 mb-base">
              <vx-card title="Điểm TB Học Viên & Mức Độ Đạt" class="h-full">
                <vue-apex-charts type="bar" height="280" :options="cmAcademicOptions" :series="cmAcademicSeries" ></vue-apex-charts>
              </vx-card>
            </div>
            <div class="vx-col w-full lg:w-1/4 mb-base">
              <vx-card title="Đánh Giá Phụ Huynh (Rating)" class="h-full text-center">
                <vue-apex-charts type="radialBar" height="280" :options="cmRatingOptions" :series="cmRatingSeries" ></vue-apex-charts>
              </vx-card>
            </div>
            <div class="vx-col w-full lg:w-1/4 mb-base">
              <vx-card title="Lý Do Churn/Bảo Lưu" class="h-full">
                <vue-apex-charts type="pie" height="280" :options="cmChurnOptions" :series="cmChurnSeries" ></vue-apex-charts>
              </vx-card>
            </div>
          </div>

          <div class="vx-row">
            <div class="vx-col w-full lg:w-1/2 mb-base">
              <vx-card title="Học Sinh Cảnh Báo (Cần Chăm Sóc Gấp)" class="h-full">
                 <ul class="activity-timeline">
                  <li class="relative pb-4 border-l-2 border-danger pl-6 ml-2">
                    <div class="absolute w-4 h-4 bg-danger rounded-full  top-1 border-2 border-white flex items-center justify-center"><feather-icon icon="AlertCircleIcon" size="10" class="text-white"/></div>
                    <div class="bg-white p-3 rounded-lg border border-danger">
                        <p class="font-bold text-danger mb-1 text-base">3 HS Vắng 3 buổi liên tiếp (Rủi ro Churn)</p>
                        <p class="text-sm text-gray-700 mb-2"><b>Lớp PT199:</b> Bé Nguyễn Văn A, Trần Thị B, Lê Văn C.</p>
                        <div class="flex gap-2">
                            <vs-button size="small" color="danger" icon="icon-phone" icon-pack="feather">Gọi Phụ Huynh</vs-button>
                            <vs-button size="small" type="border" color="danger">Xem Lịch Sử Học</vs-button>
                        </div>
                    </div>
                  </li>
                  <li class="relative pl-6 ml-2 mt-4">
                    <div class="absolute w-4 h-4 bg-warning rounded-full  top-1 border-2 border-white flex items-center justify-center"><feather-icon icon="ClockIcon" size="10" class="text-white"/></div>
                    <div class="p-3 rounded-lg border" style="background-color: rgba(255, 159, 67, 0.1); border-color: rgba(255, 159, 67, 0.3);">
                        <p class="font-bold text-warning mb-1 text-base">15 HS Sắp Hết Phí (Còn dưới 5 buổi)</p>
                        <p class="text-sm text-gray-700 mb-2">Nhóm học sinh chuẩn bị kết thúc khóa trong tuần tới. Cần gửi thư báo giá mới.</p>
                        <vs-button size="small" color="warning" icon="icon-mail" icon-pack="feather">Gửi Thông Báo Tái Phí</vs-button>
                    </div>
                  </li>
                </ul>
              </vx-card>
            </div>
            <div class="vx-col w-full lg:w-1/2 mb-base">
              <vx-card title="Sự Kiện & Lịch Trình Tương Lai" class="h-full">
                <div class="flex items-start mb-6 p-3 rounded-lg border" style="background-color: rgba(115, 103, 240, 0.1); border-color: rgba(115, 103, 240, 0.3);">
                    <div class="p-3 bg-primary text-white rounded-lg mr-4 shadow"><feather-icon icon="GiftIcon" size="24"/></div>
                    <div>
                        <h4 class="font-bold text-primary mb-1">5 Học Sinh Sinh Nhật Tuần Này</h4>
                        <p class="text-sm text-gray-600 mb-2">Bé Mai (12/07), Bé Hùng (14/07), Bé Lan (15/07)...</p>
                        <span class="text-xs font-semibold cursor-pointer text-primary hover:underline">Gửi tin nhắn chúc mừng tự động</span>
                    </div>
                </div>
                <div class="flex items-start p-3 bg-white border border-success rounded-lg">
                    <div class="p-3 bg-success text-white rounded-lg mr-4 shadow"><feather-icon icon="ActivityIcon" size="24"/></div>
                    <div>
                        <h4 class="font-bold text-success mb-1">45 HS Đăng Ký Ngoại Khóa Mùa Hè</h4>
                        <p class="text-sm text-gray-600 mb-2">Chương trình "English Summer Camp" ngày 20/07.</p>
                        <span class="text-xs font-semibold cursor-pointer text-success hover:underline">Xem danh sách chuẩn bị</span>
                    </div>
                </div>
                <vs-button class="mt-6 w-full shadow-lg" color="primary" type="gradient">Lập Kịch Bản Chăm Sóc Hằng Tuần</vs-button>
              </vx-card>
            </div>
          </div>
        </div>
      </vs-tab>


    </vs-tabs>
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
      activeTab: 0,
      checkoutTracker: [],
      branch_id: [],
      branches: [
        { id: 1, name: 'Cầu Giấy' },
        { id: 2, name: 'Đống Đa' },
        { id: 3, name: 'Hà Đông' },
        { id: 4, name: 'Thanh Xuân' },
        { id: 5, name: 'Ba Đình' },
        { id: 6, name: 'Long Biên' },
        { id: 7, name: 'Hai Bà Trưng' }
      ],
      
        // --- MASTER DEMO UI DATA ---
        // ADMIN CHARTS
        adminFunnelSeries: [{ name: 'Số lượng', data: [5000, 3500, 2000, 1500] }],
        adminFunnelOptions: { chart: { events: { dataPointSelection: function(e, c, o) { this.handleChartClick(e, c, o, 'Phễu Chuyển Đổi Tổng & LTV') }.bind(this) },  type: 'bar', toolbar: { show: false } }, plotOptions: { bar: { horizontal: true, borderRadius: 4, distributed: true } }, colors: ['#7367F0', '#00CFE8', '#FF9F43', '#28C76F'], xaxis: { categories: ['Lead', 'Test', 'Deposit', 'Full Fee'] } },
        
        adminChurnSeries: [12],
        adminChurnOptions: { chart: { events: { dataPointSelection: function(e, c, o) { this.handleChartClick(e, c, o, 'Churn Rate (Rời bỏ)') }.bind(this) },  type: 'radialBar' }, colors: ['#EA5455'], plotOptions: { radialBar: { dataLabels: { name: { show: true }, value: { formatter: function(val) { return val + "%" } } } } }, labels: ['Rời bỏ'] },

        adminProductSeries: [45, 30, 25],
        adminProductOptions: { chart: { events: { dataPointSelection: function(e, c, o) { this.handleChartClick(e, c, o, 'Doanh Thu Theo SP') }.bind(this) },  type: 'donut' }, labels: ['Kids', 'Teens', 'Kinder'], colors: ['#28C76F', '#7367F0', '#FF9F43'], plotOptions: { pie: { donut: { size: '65%' } } } , legend: { position: 'bottom' } },

        adminLeaderboardSeries: [{ name: 'Doanh thu (Tỷ)', data: [1.2, 0.9, 0.8, 0.6, 0.5] }],
        adminLeaderboardOptions: { chart: { events: { dataPointSelection: function(e, c, o) { this.handleChartClick(e, c, o, 'Bảng Xếp Hạng TT (Doanh thu & Lấp đầy)') }.bind(this) },  type: 'bar' }, colors: ['#7367F0'], xaxis: { categories: ['Cầu Giấy', 'Hà Đông', 'Đống Đa', 'Thanh Xuân', 'Long Biên'] } },

        adminCashflowSeries: [{ name: 'Dòng tiền vào', data: [3.5, 4.0, 3.8, 4.5] }, { name: 'Chi phí (Quỹ lương+Opex)', data: [2.0, 2.2, 2.1, 2.3] }],
        adminCashflowOptions: { chart: { events: { dataPointSelection: function(e, c, o) { this.handleChartClick(e, c, o, 'Dòng Tiền & Công Nợ & Quỹ Lương') }.bind(this) },  type: 'line', toolbar: { show: false } }, stroke: { curve: 'smooth' }, colors: ['#28C76F', '#EA5455'], xaxis: { categories: ['T1', 'T2', 'T3', 'T4'] } },

        // BM CHARTS
        bmFunnelSeries: [{ name: 'Số lượng', data: [850, 400, 200, 150] }],
        bmFunnelOptions: { chart: { events: { dataPointSelection: function(e, c, o) { this.handleChartClick(e, c, o, 'Phễu Chuyển Đổi Nhánh (Checkin -> Full)') }.bind(this) },  type: 'bar' }, plotOptions: { bar: { horizontal: true, borderRadius: 4, distributed: true } }, colors: ['#00CFE8', '#FF9F43', '#7367F0', '#28C76F'], xaxis: { categories: ['Check-in', 'Test', 'Cọc', 'Full'] } },

        bmClassSeries: [120, 80, 40],
        bmClassOptions: { chart: { events: { dataPointSelection: function(e, c, o) { this.handleChartClick(e, c, o, 'Phân bổ Học viên / Số lớp') }.bind(this) },  type: 'pie' }, labels: ['Kids', 'Teens', 'IELTS'], colors: ['#7367F0', '#28C76F', '#FF9F43'] , legend: { position: 'bottom' } },

        bmRadarSeries: [{ name: 'Chuyên cần', data: [90, 85, 95, 80, 88] }],
        bmRadarOptions: { chart: { events: { dataPointSelection: function(e, c, o) { this.handleChartClick(e, c, o, 'Khung Giờ & Chuyên Cần') }.bind(this) },  type: 'radar' }, xaxis: { categories: ['T2-T4', 'T3-T5', 'T7-CN Sáng', 'T7-CN Chiều', 'Tối'] }, colors: ['#7367F0'] },

        // SALE CHARTS
        saleLineSeries: [{ name: 'Chốt Deal', data: [2, 5, 3, 8, 4, 10, 6] }],
        saleLineOptions: { chart: { events: { dataPointSelection: function(e, c, o) { this.handleChartClick(e, c, o, 'Lịch Sử Chuyển Đổi & Chốt Deal') }.bind(this) },  type: 'area', toolbar: { show: false } }, stroke: { curve: 'smooth' }, colors: ['#28C76F'], fill: { type: 'gradient' }, xaxis: { categories: ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'] } },

        saleSourceSeries: [50, 30, 20],
        saleSourceOptions: { chart: { events: { dataPointSelection: function(e, c, o) { this.handleChartClick(e, c, o, 'Phân Bổ Hot Lead & Nguồn') }.bind(this) },  type: 'donut' }, labels: ['Facebook', 'Referral', 'Walk-in'], colors: ['#7367F0', '#00CFE8', '#FF9F43'] , legend: { position: 'bottom' } },

        // CM CHARTS
        cmAcademicSeries: [{ name: 'Điểm TB', data: [8.5, 7.8, 9.0, 6.5] }],
        cmAcademicOptions: { chart: { events: { dataPointSelection: function(e, c, o) { this.handleChartClick(e, c, o, 'Điểm TB Học Viên & Mức Độ Đạt') }.bind(this) },  type: 'bar', toolbar: { show: false } }, colors: ['#7367F0'], xaxis: { categories: ['PT199', 'KT12', 'TN05', 'KD01'] } },

        cmRatingSeries: [92],
        cmRatingOptions: { chart: { events: { dataPointSelection: function(e, c, o) { this.handleChartClick(e, c, o, 'Đánh Giá Phụ Huynh (Rating)') }.bind(this) },  type: 'radialBar' }, colors: ['#FF9F43'], labels: ['Hài Lòng'] },

        cmChurnSeries: [45, 30, 25],
        cmChurnOptions: { chart: { events: { dataPointSelection: function(e, c, o) { this.handleChartClick(e, c, o, 'Lý Do Churn/Bảo Lưu') }.bind(this) },  type: 'pie' }, labels: ['Chuyển nhà', 'Không hiệu quả', 'Hết tiền'], colors: ['#EA5455', '#FF9F43', '#7367F0'] , legend: { position: 'bottom' } },
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
      totalRevenueWeek: { analyticsData: { data: 0 } },
      totalRevenueMonth: { analyticsData: { data: 0 } },
      numDashStudent: { analyticsData: { data: 0 } },
      numDashClass: { analyticsData: { data: 0 } },
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
          chart: { events: { dataPointSelection: this.handleChartClick }, 
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
      },
      reenrollmentTableData: [],
      totalReenrollment: {
        ratio_t: '0/0',
        ratio_t1: '0/0',
        ratio_t2: '0/0',
        ratio_t3: '0/0'
      },
      expiredStudentsData: []
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
  mounted() {
    if (this.$route.query.tab !== undefined) {
      this.activeTab = parseInt(this.$route.query.tab) || 0;
    }
    if (this.$route.query.scroll) {
      setTimeout(() => {
        window.scrollTo({ top: parseInt(this.$route.query.scroll), behavior: 'smooth' });
      }, 500);
    }
  },
  methods: {
    handleChartClick(event, chartContext, config, chartTitle) {
      let label = 'chart-clicked';
      let value = '';
      if (config && config.w && config.w.globals) {
        if (config.w.globals.labels) {
          label = config.w.globals.labels[config.dataPointIndex] || label;
        }
        try {
          let seriesIdx = config.seriesIndex > -1 ? config.seriesIndex : 0;
          if (config.w.globals.series && config.w.globals.series[seriesIdx] && config.w.globals.series[seriesIdx].length > config.dataPointIndex) {
            value = config.w.globals.series[seriesIdx][config.dataPointIndex];
          } else if (config.w.config.series && typeof config.w.config.series[config.dataPointIndex] === 'number') {
            value = config.w.config.series[config.dataPointIndex];
          }
        } catch(e) {}
      }
      if (chartTitle) {
         label = chartTitle + ' - ' + label;
      }
      this.goToReport(label, value);
    },
    goToReport(filterValue, dataValue = '') {
      const scrollY = window.scrollY || document.documentElement.scrollTop;
      this.$router.push({ path: '/admin/dashboard/report', query: { filter: filterValue, value: dataValue, scroll: scrollY, tab: this.activeTab } });
    },
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
            chart: { events: { dataPointSelection: this.handleChartClick }, 
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
      if(this.checkPermission('dashboard_13')){
        this.loadDataDashboard13();
      }
      if(this.checkPermission('dashboard_14')){
        this.loadDataDashboard14();
      }
      if(this.checkPermission('dashboard_15')){
        this.loadDataDashboard15();
      }
      if(this.checkPermission('dashboard_16')){
        this.loadDataDashboard16();
      }
      this.loadDataDashboard17();
      this.loadDataDashboard18();
      this.loadDataDashboard19();
      this.loadDataDashboard20();
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
    },
    loadDataDashboard19(){
      const ids_branch = []
      if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
        this.searchData.arr_branch.map(item => {
          ids_branch.push(item.id)
        })
      }
      this.searchData.branch_id = ids_branch
      this.$vs.loading()
      axios.p(`/api/dashboard/19`,{
        branch_id: this.searchData.branch_id,
      })
      .then(response => {
        this.$vs.loading.close()
        this.reenrollmentTableData = response.data
        this.calculateTotalReenrollment()
      })
      .catch(error => {
        this.$vs.loading.close()
        console.error('Error loading re-enrollment table:', error)
      })
    },
    calculateTotalReenrollment() {
      let totalExpiredT = 0
      let totalRenewedT = 0
      let totalExpiredT1 = 0
      let totalRenewedT1 = 0
      let totalExpiredT2 = 0
      let totalRenewedT2 = 0
      let totalExpiredT3 = 0
      let totalRenewedT3 = 0
      
      this.reenrollmentTableData.forEach(item => {
        totalExpiredT += item.total_expired_t
        totalRenewedT += item.total_renewed_t
        totalExpiredT1 += item.total_expired_t1
        totalRenewedT1 += item.total_renewed_t1
        totalExpiredT2 += item.total_expired_t2
        totalRenewedT2 += item.total_renewed_t2
        totalExpiredT3 += item.total_expired_t3
        totalRenewedT3 += item.total_renewed_t3
      })
      
      this.totalReenrollment = {
        ratio_t: totalExpiredT > 0 ? `${totalRenewedT}/${totalExpiredT}` : '0/0',
        ratio_t1: totalExpiredT1 > 0 ? `${totalRenewedT1}/${totalExpiredT1}` : '0/0',
        ratio_t2: totalExpiredT2 > 0 ? `${totalRenewedT2}/${totalExpiredT2}` : '0/0',
        ratio_t3: totalExpiredT3 > 0 ? `${totalRenewedT3}/${totalExpiredT3}` : '0/0'
      }
    },
    loadDataDashboard20(){
      const ids_branch = []
      if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
        this.searchData.arr_branch.map(item => {
          ids_branch.push(item.id)
        })
      }
      this.searchData.branch_id = ids_branch
      this.$vs.loading()
      axios.p(`/api/dashboard/20`,{
        branch_id: this.searchData.branch_id,
      })
      .then(response => {
        this.$vs.loading.close()
        this.expiredStudentsData = response.data
      })
      .catch(error => {
        this.$vs.loading.close()
        console.error('Error loading expired students table:', error)
      })
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

  // Re-enrollment Table Styles
  .renew-ratio {
    font-weight: 600;
    font-size: 1rem;
    color: #2c3e50;
  }

  // Expired Students Table Styles
  .status-withdraw {
    color: #ea5455;
    font-weight: 600;
  }

  .status-processing {
    color: #ff9f43;
    font-weight: 600;
  }

  .status-renewed {
    color: #28c76f;
    font-weight: 600;
  }

  .status-upcoming {
    color: #7367f0;
    font-weight: 600;
  }

  .vs-table--thead th {
    vertical-align: middle;
  }

  .vs-table--thead th small {
    display: block;
    font-weight: normal;
    font-size: 0.75rem;
    color: #6c757d;
    margin-top: 4px;
  }
}
/*! rtl:end:ignore */
</style>
