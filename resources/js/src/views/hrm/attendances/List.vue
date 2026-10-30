<template>
  <div id="page-hrm-attendances">
    <!-- Header & Tra cứu nhanh -->
    <div class="flex flex-wrap items-center justify-between mb-4">
      <div class="mb-2 md:mb-0">
        <h3 class="font-bold text-gray-800 flex items-center">
          <feather-icon icon="ClockIcon" class="mr-2 text-primary" /> Bảng Chấm Công & Điểm Danh
        </h3>
        <p class="text-sm text-gray-500 mt-1">Theo dõi nhân sự đi làm muộn, nghỉ hàng ngày và tổng hợp công tự động</p>
      </div>
      <div class="flex items-center flex-wrap">
        <div class="con-input-search vs-table--search mr-3">
          <input 
            type="text" 
            class="input-search vs-table--search-input" 
            style="padding: 12px 36px 12px 14px; font-size: 13px; min-width: 260px;" 
            placeholder="Tìm theo Tên, SĐT, Mã NS..." 
            v-model="quickSearchKeyword" 
            @keyup.enter="handleQuickSearch"
          >
          <i class="vs-icon notranslate icon-scale material-icons null" style="font-size: 20px; cursor: pointer;" @click="handleQuickSearch">search</i>
        </div>
        <vs-button color="primary" type="filled" class="p-3" @click="handleQuickSearch">
          <i class="fa-solid fa-magnifying-glass mr-1"></i> Tra cứu
        </vs-button>
      </div>
    </div>

    <!-- Modal kết quả tra cứu nhanh -->
    <vs-popup title="KẾT QUẢ TRA CỨU CÔNG NHÂN SỰ" :active.sync="quickSearchModalActive">
      <div v-if="quickSearchResult" class="p-4">
        <div class="p-4 rounded-lg border border-primary bg-primary-light text-gray-800">
          <div class="flex items-center justify-between border-b pb-3 mb-3 border-gray-200">
            <div>
              <h4 class="text-primary font-bold">{{ quickSearchResult.name }}</h4>
              <span class="text-xs text-gray-600 font-semibold">Mã NS: {{ quickSearchResult.employee_code }} | SĐT: {{ quickSearchResult.phone || 'Chưa có' }}</span>
            </div>
            <vs-chip color="primary">{{ quickSearchResult.team_name }}</vs-chip>
          </div>
          <div class="grid grid-cols-2 gap-3 text-sm">
            <div class="p-3 bg-white rounded shadow-sm flex justify-between items-center">
              <span class="text-gray-600">Công chuẩn tháng:</span>
              <span class="font-bold text-gray-900 text-base">{{ quickSearchResult.standard_days }}</span>
            </div>
            <div class="p-3 bg-white rounded shadow-sm flex justify-between items-center">
              <span class="text-gray-600">Đã làm:</span>
              <span class="font-bold text-success text-base">{{ quickSearchResult.worked_days }} ngày</span>
            </div>
            <div class="p-3 bg-white rounded shadow-sm flex justify-between items-center">
              <span class="text-gray-600">Đi muộn:</span>
              <span class="font-bold text-warning text-base">{{ quickSearchResult.late_fined_count }} lần</span>
            </div>
            <div class="p-3 bg-white rounded shadow-sm flex justify-between items-center">
              <span class="text-gray-600">Nghỉ phép:</span>
              <span class="font-bold text-primary text-base">{{ quickSearchResult.leave_approved_days }} ngày</span>
            </div>
            <div class="p-3 bg-white rounded shadow-sm flex justify-between items-center">
              <span class="text-gray-600">Nghỉ không phép:</span>
              <span class="font-bold text-danger text-base">{{ quickSearchResult.unexcused_absent_days }} ngày</span>
            </div>
            <div class="p-3 bg-white rounded shadow-sm flex justify-between items-center">
              <span class="text-gray-600">Gửi đơn muộn:</span>
              <span class="font-bold text-dark text-base">{{ quickSearchResult.late_submitted_count }} lần</span>
            </div>
          </div>
        </div>
      </div>
    </vs-popup>

    <!-- Tabs chuyển đổi -->
    <vs-tabs alignment="left" class="mt-4">
      <!-- TAB 1: Điểm danh hàng ngày -->
      <vs-tab label="Điểm danh theo ngày" icon-pack="feather" icon="icon-calendar">
        <vx-card no-shadow class="mt-4">
          <!-- Toolbar Lọc & Lưu -->
          <div class="flex flex-wrap items-center justify-between mb-4">
            <div class="flex flex-wrap items-center">
              <div class="mr-3 mb-2 flex items-center">
                <span class="text-sm font-medium mr-2 text-gray-700">Ngày:</span>
                <input 
                  type="date" 
                  v-model="selectedDate" 
                  @change="fetchDailySheet" 
                  class="vs-inputx vs-input--input normal"
                  style="padding: 8px 12px; font-size: 13px; width: 160px;"
                />
              </div>
              <div class="mr-3 mb-2 flex items-center">
                <span class="text-sm font-medium mr-2 text-gray-700">Team:</span>
                <select 
                  v-model="selectedDepartment" 
                  @change="fetchDailySheet" 
                  class="vs-inputx vs-input--input normal"
                  style="padding: 8px 12px; font-size: 13px; min-width: 180px;"
                >
                  <option value="">-- Tất cả team --</option>
                  <option :value="dept.id" v-for="dept in departments" :key="dept.id">{{ dept.name }}</option>
                </select>
              </div>
              <div class="con-input-search vs-table--search mr-3 mb-2">
                <input 
                  type="text" 
                  class="input-search vs-table--search-input" 
                  style="padding: 8px 30px 8px 12px; font-size: 13px; min-width: 200px;" 
                  placeholder="Lọc tên, mã NS..." 
                  v-model="dailySearchKeyword" 
                >
                <i class="vs-icon notranslate icon-scale material-icons null" style="font-size: 18px;">search</i>
              </div>
            </div>
            <div class="mb-2">
              <vs-button class="p-3" color="success" type="filled" @click="saveDailyAttendance">
                <i class="fa-solid fa-floppy-disk mr-2"></i> Lưu Bảng Điểm Danh
              </vs-button>
            </div>
          </div>

          <!-- Bảng điểm danh đồng bộ table của hệ thống -->
          <div class="vs-component vs-con-table stripe vs-table-primary">
            <div class="con-tablex vs-table--content">
              <div class="vs-con-tbody vs-table--tbody">
                <table class="vs-table vs-table--tbody-table">
                  <thead class="vs-table--thead">
                    <tr>
                      <th width="5%" class="text-center">STT</th>
                      <th width="12%">Mã nhân sự</th>
                      <th width="20%">Họ và tên</th>
                      <th width="18%">Team / Phòng ban</th>
                      <th width="25%">Trạng thái điểm danh</th>
                      <th width="20%">Ghi chú</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="tr-values vs-table--tr" v-for="(tr, index) in paginatedDailyRecords" :key="tr.user_id || tr.employee_id || index">
                      <td class="td vs-table--td text-center">{{ (dailyPage - 1) * dailyPerPage + index + 1 }}</td>
                      <td class="td vs-table--td font-bold text-primary">{{ tr.employee_code || '---' }}</td>
                      <td class="td vs-table--td font-semibold text-gray-900">{{ tr.name }}</td>
                      <td class="td vs-table--td">{{ tr.team_name }}</td>
                      <td class="td vs-table--td">
                        <select 
                          v-model="tr.attendance_status" 
                          class="vs-inputx vs-input--input normal w-full font-semibold"
                          style="padding: 8px 12px; font-size: 13px;"
                          :style="getStatusStyle(tr.attendance_status)"
                        >
                          <option value="present">✓ Có mặt / Đúng giờ</option>
                          <option value="leave_approved">📅 Đã gửi phép</option>
                          <option value="unexcused_absent">✗ Vắng k phép</option>
                          <option value="late_fined">⏱ Đi muộn (đã nộp phạt)</option>
                          <option value="late_submitted">⚠ Gửi đơn muộn</option>
                        </select>
                      </td>
                      <td class="td vs-table--td">
                        <input 
                          type="text" 
                          v-model="tr.note" 
                          placeholder="Nhập ghi chú..." 
                          class="vs-inputx vs-input--input normal w-full"
                          style="padding: 8px 12px; font-size: 13px;"
                        />
                      </td>
                    </tr>
                    <tr v-if="!dailyRecords.length">
                      <td colspan="6" class="text-center p-4 text-gray-500">Không có dữ liệu nhân viên cần điểm danh</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Phân trang Tab 1 -->
          <div class="flex flex-wrap items-center justify-between mt-5" v-if="filteredDailyRecords.length > 0">
            <div class="text-sm text-gray-600 mb-2 md:mb-0">
              Hiển thị <strong>{{ filteredDailyRecords.length ? (dailyPage - 1) * dailyPerPage + 1 : 0 }}</strong> - <strong>{{ Math.min(dailyPage * dailyPerPage, filteredDailyRecords.length) }}</strong> trong tổng số <strong>{{ filteredDailyRecords.length }}</strong> nhân sự
            </div>
            <div class="flex items-center" v-if="filteredDailyRecords.length > dailyPerPage">
              <span class="text-sm text-gray-600 mr-2">Mỗi trang:</span>
              <select v-model="dailyPerPage" @change="dailyPage = 1" class="vs-inputx vs-input--input normal mr-4" style="padding: 6px 10px; font-size: 13px;">
                <option :value="20">20</option>
                <option :value="50">50</option>
                <option :value="100">100</option>
              </select>
              <vs-pagination
                :total="totalDailyPages"
                v-model="dailyPage"
              />
            </div>
          </div>
        </vx-card>
      </vs-tab>

      <!-- TAB 2: Tổng hợp công tháng -->
      <vs-tab label="Tổng hợp công tháng" icon-pack="feather" icon="icon-bar-chart-2">
        <vx-card no-shadow class="mt-4">
          <div class="flex flex-wrap items-center justify-between mb-4">
            <div class="flex flex-wrap items-center">
              <div class="mr-3 mb-2 flex items-center">
                <span class="text-sm font-medium mr-2 text-gray-700">Tháng:</span>
                <select v-model="selectedMonth" @change="fetchMonthlySummary" class="vs-inputx vs-input--input normal" style="padding: 8px 12px; font-size: 13px;">
                  <option v-for="m in 12" :key="m" :value="m">Tháng {{ m }}</option>
                </select>
              </div>
              <div class="mr-3 mb-2 flex items-center">
                <span class="text-sm font-medium mr-2 text-gray-700">Năm:</span>
                <select v-model="selectedYear" @change="fetchMonthlySummary" class="vs-inputx vs-input--input normal" style="padding: 8px 12px; font-size: 13px;">
                  <option :value="2025">2025</option>
                  <option :value="2026">2026</option>
                  <option :value="2027">2027</option>
                </select>
              </div>
              <div class="con-input-search vs-table--search mr-3 mb-2">
                <input 
                  type="text" 
                  class="input-search vs-table--search-input" 
                  style="padding: 8px 30px 8px 12px; font-size: 13px; min-width: 200px;" 
                  placeholder="Lọc tên, mã NS..." 
                  v-model="monthlySearchKeyword" 
                >
                <i class="vs-icon notranslate icon-scale material-icons null" style="font-size: 18px;">search</i>
              </div>
            </div>
            <div class="text-sm font-semibold text-primary mb-2">
              <i class="fa-solid fa-circle-info mr-1"></i> Công chuẩn tháng: <strong class="text-base text-gray-900">{{ standardDays }}</strong> công
            </div>
          </div>

          <!-- Bảng tổng hợp công tháng đồng bộ -->
          <div class="vs-component vs-con-table stripe vs-table-primary">
            <div class="con-tablex vs-table--content">
              <div class="vs-con-tbody vs-table--tbody">
                <table class="vs-table vs-table--tbody-table">
                  <thead class="vs-table--thead">
                    <tr>
                      <th width="5%" class="text-center">STT</th>
                      <th width="10%">Mã NS</th>
                      <th width="18%">Họ và tên</th>
                      <th width="15%">Team</th>
                      <th width="10%" class="text-center">Công chuẩn</th>
                      <th width="10%" class="text-center">Đã làm</th>
                      <th width="10%" class="text-center">Đi muộn</th>
                      <th width="10%" class="text-center">Nghỉ phép</th>
                      <th width="12%" class="text-center">Vắng k phép</th>
                      <th width="10%" class="text-center">Gửi muộn</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="tr-values vs-table--tr" v-for="(tr, index) in paginatedMonthlySummary" :key="tr.user_id || tr.employee_id || index">
                      <td class="td vs-table--td text-center">{{ (monthlyPage - 1) * monthlyPerPage + index + 1 }}</td>
                      <td class="td vs-table--td font-bold text-primary">{{ tr.employee_code || '---' }}</td>
                      <td class="td vs-table--td font-semibold text-gray-900">{{ tr.name }}</td>
                      <td class="td vs-table--td">{{ tr.team_name }}</td>
                      <td class="td vs-table--td text-center font-semibold">{{ tr.standard_days }}</td>
                      <td class="td vs-table--td text-center font-bold text-success">{{ tr.worked_days }}</td>
                      <td class="td vs-table--td text-center font-semibold text-warning">{{ tr.late_fined_count }} lần</td>
                      <td class="td vs-table--td text-center font-semibold text-primary">{{ tr.leave_approved_days }} ngày</td>
                      <td class="td vs-table--td text-center font-semibold text-danger">{{ tr.unexcused_absent_days }} ngày</td>
                      <td class="td vs-table--td text-center font-semibold text-dark">{{ tr.late_submitted_count }} lần</td>
                    </tr>
                    <tr v-if="!filteredMonthlySummary.length">
                      <td colspan="10" class="text-center p-4 text-gray-500">Chưa có dữ liệu tổng hợp</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Phân trang Tab 2 -->
          <div class="flex flex-wrap items-center justify-between mt-5" v-if="filteredMonthlySummary.length > 0">
            <div class="text-sm text-gray-600 mb-2 md:mb-0">
              Hiển thị <strong>{{ filteredMonthlySummary.length ? (monthlyPage - 1) * monthlyPerPage + 1 : 0 }}</strong> - <strong>{{ Math.min(monthlyPage * monthlyPerPage, filteredMonthlySummary.length) }}</strong> trong tổng số <strong>{{ filteredMonthlySummary.length }}</strong> nhân sự
            </div>
            <div class="flex items-center" v-if="filteredMonthlySummary.length > monthlyPerPage">
              <span class="text-sm text-gray-600 mr-2">Mỗi trang:</span>
              <select v-model="monthlyPerPage" @change="monthlyPage = 1" class="vs-inputx vs-input--input normal mr-4" style="padding: 6px 10px; font-size: 13px;">
                <option :value="20">20</option>
                <option :value="50">50</option>
                <option :value="100">100</option>
              </select>
              <vs-pagination
                :total="totalMonthlyPages"
                v-model="monthlyPage"
              />
            </div>
          </div>
        </vx-card>
      </vs-tab>
    </vs-tabs>
  </div>
</template>

<script>
import axios from '../../../http/axios.js';

export default {
  data() {
    return {
      selectedDate: new Date().toISOString().split('T')[0],
      selectedDepartment: '',
      departments: [],
      dailyRecords: [],
      dailySearchKeyword: '',
      dailyPage: 1,
      dailyPerPage: 20,
      selectedMonth: new Date().getMonth() + 1,
      selectedYear: new Date().getFullYear(),
      monthlySummary: [],
      monthlySearchKeyword: '',
      monthlyPage: 1,
      monthlyPerPage: 20,
      standardDays: 26,
      quickSearchKeyword: '',
      quickSearchModalActive: false,
      quickSearchResult: null,
    };
  },
  watch: {
    dailySearchKeyword() {
      this.dailyPage = 1;
    },
    monthlySearchKeyword() {
      this.monthlyPage = 1;
    }
  },
  computed: {
    filteredDailyRecords() {
      if (!this.dailySearchKeyword) return this.dailyRecords;
      const kw = this.dailySearchKeyword.toLowerCase().trim();
      return this.dailyRecords.filter(r => 
        (r.name && r.name.toLowerCase().includes(kw)) ||
        (r.employee_code && r.employee_code.toLowerCase().includes(kw)) ||
        (r.team_name && r.team_name.toLowerCase().includes(kw))
      );
    },
    paginatedDailyRecords() {
      const start = (this.dailyPage - 1) * this.dailyPerPage;
      return this.filteredDailyRecords.slice(start, start + this.dailyPerPage);
    },
    totalDailyPages() {
      return Math.ceil(this.filteredDailyRecords.length / this.dailyPerPage) || 1;
    },
    filteredMonthlySummary() {
      if (!this.monthlySearchKeyword) return this.monthlySummary;
      const kw = this.monthlySearchKeyword.toLowerCase().trim();
      return this.monthlySummary.filter(r => 
        (r.name && r.name.toLowerCase().includes(kw)) ||
        (r.employee_code && r.employee_code.toLowerCase().includes(kw)) ||
        (r.team_name && r.team_name.toLowerCase().includes(kw))
      );
    },
    paginatedMonthlySummary() {
      const start = (this.monthlyPage - 1) * this.monthlyPerPage;
      return this.filteredMonthlySummary.slice(start, start + this.monthlyPerPage);
    },
    totalMonthlyPages() {
      return Math.ceil(this.filteredMonthlySummary.length / this.monthlyPerPage) || 1;
    }
  },
  created() {
    this.fetchDepartments();
    this.fetchDailySheet();
    this.fetchMonthlySummary();
  },
  methods: {
    fetchDepartments() {
      axios.p('/api/hrm/departments/list', { limit: 100 }).then(res => {
        if (res.data) {
          this.departments = res.data;
        }
      });
    },
    fetchDailySheet() {
      this.$vs.loading();
      axios.p('/api/hrm/attendances/daily-sheet', {
        date: this.selectedDate,
        department_id: this.selectedDepartment || null
      }).then(res => {
        this.dailyRecords = res.data.data || [];
        this.$vs.loading.close();
      }).catch(err => {
        this.$vs.loading.close();
        this.$vs.notify({ title: 'Lỗi', text: 'Không thể tải bảng điểm danh', color: 'danger' });
      });
    },
    saveDailyAttendance() {
      this.$vs.loading();
      axios.p('/api/hrm/attendances/update-status', {
        date: this.selectedDate,
        records: this.dailyRecords
      }).then(res => {
        this.$vs.loading.close();
        this.$vs.notify({ title: 'Thành công', text: res.data.message, color: 'success' });
        this.fetchMonthlySummary();
      }).catch(err => {
        this.$vs.loading.close();
        this.$vs.notify({ title: 'Lỗi', text: 'Lưu điểm danh thất bại', color: 'danger' });
      });
    },
    fetchMonthlySummary() {
      axios.p('/api/hrm/attendances/monthly-summary', {
        month: this.selectedMonth,
        year: this.selectedYear
      }).then(res => {
        this.monthlySummary = res.data.data || [];
        this.standardDays = res.data.standard_days || 26;
      });
    },
    handleQuickSearch() {
      if (!this.quickSearchKeyword) {
        this.$vs.notify({ title: 'Thông báo', text: 'Vui lòng nhập tên, sđt hoặc mã nhân sự', color: 'warning' });
        return;
      }
      this.$vs.loading();
      axios.p('/api/hrm/attendances/quick-search', {
        keyword: this.quickSearchKeyword,
        month: this.selectedMonth,
        year: this.selectedYear
      }).then(res => {
        this.$vs.loading.close();
        if (res.data.status === 1) {
          this.quickSearchResult = res.data.data;
          this.quickSearchModalActive = true;
        } else {
          this.$vs.notify({ title: 'Thông báo', text: res.data.message, color: 'warning' });
        }
      }).catch(err => {
        this.$vs.loading.close();
        this.$vs.notify({ title: 'Lỗi', text: 'Lỗi khi tra cứu', color: 'danger' });
      });
    },
    getStatusStyle(status) {
      switch (status) {
        case 'present':
          return 'color: #28c76f; border-color: #28c76f; background-color: #f0fdf4;';
        case 'leave_approved':
          return 'color: #7367f0; border-color: #7367f0; background-color: #f5f3ff;';
        case 'unexcused_absent':
          return 'color: #ea5455; border-color: #ea5455; background-color: #fef2f2;';
        case 'late_fined':
          return 'color: #ff9f43; border-color: #ff9f43; background-color: #fffbeb;';
        case 'late_submitted':
          return 'color: #82868b; border-color: #82868b; background-color: #f9fafb;';
        default:
          return '';
      }
    }
  }
};
</script>
