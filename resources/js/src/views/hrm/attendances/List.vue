<template>
  <div>
    <!-- Thanh tra cứu nhanh nhân sự theo Tên / SĐT / Mã NS -->
    <vs-card class="mb-4">
      <div class="vx-row items-center">
        <div class="vx-col w-full md:w-1/2">
          <h4 class="mb-2"><i class="fa fa-calendar-check text-primary"></i> Quản lý Chấm công & Điểm danh</h4>
          <span class="text-sm text-gray-500">Theo dõi đi làm muộn, nghỉ hàng ngày và tổng hợp công tự động</span>
        </div>
        <div class="vx-col w-full md:w-1/2 flex justify-end items-center mt-3 md:mt-0">
          <vs-input 
            icon-pack="feather" 
            icon="icon-search" 
            placeholder="Tra cứu nhanh theo Tên, SĐT, Mã NS..." 
            v-model="quickSearchKeyword" 
            class="mr-2 w-full md:w-80"
            @keyup.enter="handleQuickSearch"
          />
          <vs-button color="primary" @click="handleQuickSearch"><i class="fa fa-search"></i> Tra cứu</vs-button>
        </div>
      </div>
    </vs-card>

    <!-- Modal kết quả tra cứu nhanh -->
    <vs-popup title="Kết quả tra cứu công nhân sự" :active.sync="quickSearchModalActive">
      <div v-if="quickSearchResult" class="p-4">
        <div class="p-4 bg-primary-light rounded-lg border border-primary text-gray-800">
          <h4 class="text-primary font-bold mb-3 flex items-center">
            <i class="fa fa-user mr-2"></i> {{ quickSearchResult.name }} ({{ quickSearchResult.employee_code }})
          </h4>
          <div class="grid grid-cols-2 gap-2 text-sm mb-4">
            <div><strong>Phòng ban:</strong> {{ quickSearchResult.team_name }}</div>
            <div><strong>Số điện thoại:</strong> {{ quickSearchResult.phone || 'Chưa cập nhật' }}</div>
            <div><strong>Tháng tra cứu:</strong> {{ quickSearchResult.month }}/{{ quickSearchResult.year }}</div>
          </div>
          <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <div class="flex justify-between py-2 border-b border-gray-100">
              <span class="font-medium text-gray-700">Công chuẩn:</span>
              <span class="font-bold text-gray-900">{{ quickSearchResult.standard_days }}</span>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-100">
              <span class="font-medium text-gray-700">Đã làm:</span>
              <span class="font-bold text-success">{{ quickSearchResult.worked_days }}</span>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-100">
              <span class="font-medium text-gray-700">Đi muộn:</span>
              <span class="font-bold text-warning">{{ quickSearchResult.late_fined_count }} lần</span>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-100">
              <span class="font-medium text-gray-700">Nghỉ phép:</span>
              <span class="font-bold text-primary">{{ quickSearchResult.leave_approved_days }} ngày</span>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-100">
              <span class="font-medium text-gray-700">Nghỉ không phép:</span>
              <span class="font-bold text-danger">{{ quickSearchResult.unexcused_absent_days }} ngày</span>
            </div>
            <div class="flex justify-between py-2">
              <span class="font-medium text-gray-700">Gửi đơn muộn:</span>
              <span class="font-bold text-dark">{{ quickSearchResult.late_submitted_count }} lần</span>
            </div>
          </div>
        </div>
      </div>
    </vs-popup>

    <!-- Tabs chuyển đổi: Điểm danh ngày & Bảng tổng hợp công tháng -->
    <vs-tabs>
      <!-- TAB 1: Điểm danh hàng ngày -->
      <vs-tab label="Điểm danh hàng ngày" icon="today">
        <vs-card class="mt-4">
          <div class="flex flex-wrap items-center justify-between mb-4">
            <div class="flex flex-wrap items-center space-x-3 mb-2">
              <div class="mr-4">
                <label class="block text-xs font-medium text-gray-600 mb-1">Chọn ngày điểm danh:</label>
                <input 
                  type="date" 
                  v-model="selectedDate" 
                  @change="fetchDailySheet" 
                  class="vs-inputx vs-input--input normal p-2 border rounded"
                />
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Lọc theo Team:</label>
                <select 
                  v-model="selectedDepartment" 
                  @change="fetchDailySheet" 
                  class="vs-inputx vs-input--input normal p-2 border rounded"
                >
                  <option value="">-- Tất cả team --</option>
                  <option :value="dept.id" v-for="dept in departments" :key="dept.id">{{ dept.name }}</option>
                </select>
              </div>
            </div>
            <div class="flex space-x-2">
              <vs-button color="success" @click="saveDailyAttendance">
                <i class="fa fa-save"></i> Lưu điểm danh ngày
              </vs-button>
            </div>
          </div>

          <vs-table :data="dailyRecords">
            <template slot="thead">
              <vs-th>Mã NS</vs-th>
              <vs-th>Họ và tên</vs-th>
              <vs-th>Team / Bộ phận</vs-th>
              <vs-th>Trạng thái điểm danh</vs-th>
              <vs-th>Ghi chú</vs-th>
            </template>
            <template slot-scope="{data}">
              <vs-tr :key="indextr" v-for="(tr, indextr) in data">
                <vs-td>
                  <span class="font-bold text-primary">{{ tr.employee_code || '---' }}</span>
                </vs-td>
                <vs-td class="font-medium text-gray-800">{{ tr.name }}</vs-td>
                <vs-td>{{ tr.team_name }}</vs-td>
                <vs-td style="min-width: 260px;">
                  <select 
                    v-model="tr.attendance_status" 
                    class="vs-inputx vs-input--input normal p-2 border rounded w-full font-medium"
                    :style="getStatusStyle(tr.attendance_status)"
                  >
                    <option value="present"> Có mặt / Đúng giờ</option>
                    <option value="leave_approved"> Đã gửi phép</option>
                    <option value="unexcused_absent"> Vắng k phép</option>
                    <option value="late_fined"> Đi muộn (đã nộp phạt)</option>
                    <option value="late_submitted"> Gửi đơn muộn</option>
                  </select>
                </vs-td>
                <vs-td>
                  <vs-input placeholder="Nhập ghi chú..." v-model="tr.note" class="w-full" />
                </vs-td>
              </vs-tr>
            </template>
          </vs-table>
        </vs-card>
      </vs-tab>

      <!-- TAB 2: Tổng hợp công tháng -->
      <vs-tab label="Tổng hợp công tháng" icon="assessment">
        <vs-card class="mt-4">
          <div class="flex flex-wrap items-center justify-between mb-4">
            <div class="flex items-center space-x-3">
              <div class="mr-3">
                <label class="block text-xs font-medium text-gray-600 mb-1">Tháng:</label>
                <select v-model="selectedMonth" @change="fetchMonthlySummary" class="p-2 border rounded">
                  <option v-for="m in 12" :key="m" :value="m">Tháng {{ m }}</option>
                </select>
              </div>
              <div class="mr-3">
                <label class="block text-xs font-medium text-gray-600 mb-1">Năm:</label>
                <select v-model="selectedYear" @change="fetchMonthlySummary" class="p-2 border rounded">
                  <option :value="2025">2025</option>
                  <option :value="2026">2026</option>
                  <option :value="2027">2027</option>
                </select>
              </div>
            </div>
            <div class="text-sm font-medium text-primary">
              <i class="fa fa-info-circle"></i> Công chuẩn tháng: <strong>{{ standardDays }} công</strong>
            </div>
          </div>

          <vs-table :data="monthlySummary" search pagination :max-items="20">
            <template slot="thead">
              <vs-th>Mã NS</vs-th>
              <vs-th>Họ và tên</vs-th>
              <vs-th>Team</vs-th>
              <vs-th>Công chuẩn</vs-th>
              <vs-th>Đã làm</vs-th>
              <vs-th>Đi muộn</vs-th>
              <vs-th>Nghỉ phép</vs-th>
              <vs-th>Nghỉ không phép</vs-th>
              <vs-th>Gửi đơn muộn</vs-th>
            </template>
            <template slot-scope="{data}">
              <vs-tr :key="indextr" v-for="(tr, indextr) in data">
                <vs-td class="font-bold text-primary">{{ tr.employee_code || '---' }}</vs-td>
                <vs-td class="font-medium text-gray-900">{{ tr.name }}</vs-td>
                <vs-td>{{ tr.team_name }}</vs-td>
                <vs-td class="font-semibold">{{ tr.standard_days }}</vs-td>
                <vs-td class="font-bold text-success">{{ tr.worked_days }}</vs-td>
                <vs-td class="text-warning font-semibold">{{ tr.late_fined_count }} lần</vs-td>
                <vs-td class="text-primary font-semibold">{{ tr.leave_approved_days }} ngày</vs-td>
                <vs-td class="text-danger font-semibold">{{ tr.unexcused_absent_days }} ngày</vs-td>
                <vs-td class="text-dark font-semibold">{{ tr.late_submitted_count }} lần</vs-td>
              </vs-tr>
            </template>
          </vs-table>
        </vs-card>
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
      selectedMonth: new Date().getMonth() + 1,
      selectedYear: new Date().getFullYear(),
      monthlySummary: [],
      standardDays: 26,
      quickSearchKeyword: '',
      quickSearchModalActive: false,
      quickSearchResult: null,
    };
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
          return 'color: #28c76f; font-weight: 600; border-color: #28c76f;';
        case 'leave_approved':
          return 'color: #7367f0; font-weight: 600; border-color: #7367f0;';
        case 'unexcused_absent':
          return 'color: #ea5455; font-weight: 600; border-color: #ea5455;';
        case 'late_fined':
          return 'color: #ff9f43; font-weight: 600; border-color: #ff9f43;';
        case 'late_submitted':
          return 'color: #82868b; font-weight: 600; border-color: #82868b;';
        default:
          return '';
      }
    }
  }
};
</script>
