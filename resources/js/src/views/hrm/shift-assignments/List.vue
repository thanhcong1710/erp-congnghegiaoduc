<template>
  <div id="page-hrm-shift-assignments">
    <!-- Header & Bộ lọc -->
    <vx-card class="mb-4" no-shadow>
      <div class="flex flex-wrap items-center justify-between mb-4 pb-2 border-b border-gray-100">
        <div>
          <h3 class="font-bold text-gray-800 flex items-center">
            <feather-icon icon="CalendarIcon" class="mr-2 text-primary" /> Xếp Ca & Quản Lý Ca Làm Việc Nhân Sự
          </h3>
          <p class="text-sm text-gray-500 mt-1">Cài đặt ca làm việc cho nhân viên áp dụng cố định cho đến khi có thay đổi</p>
        </div>
        <div class="flex items-center flex-wrap mt-2 md:mt-0">
          <vs-button 
            color="primary" 
            type="filled" 
            class="p-3 mr-2 font-semibold" 
            :disabled="selectedEmployeeIds.length === 0" 
            @click="openBatchPopup()"
          >
            <i class="fa-solid fa-calendar-check mr-1"></i> Cập nhật ca ({{ selectedEmployeeIds.length }})
          </vs-button>
          <vs-button color="dark" type="border" class="p-3" @click="fetchData">
            <i class="fa-solid fa-arrows-rotate mr-1"></i> Làm mới
          </vs-button>
        </div>
      </div>

      <!-- Toolbar Bộ lọc chuẩn đẹp: Phòng ban, Ca làm việc, Tìm kiếm -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-1">
        <!-- 1. Phòng ban / Team -->
        <div>
          <label class="text-xs font-semibold text-gray-600 block mb-1">Phòng ban / Team</label>
          <select 
            v-model="selectedDepartment" 
            @change="onFilterChange" 
            class="custom-filter-input"
          >
            <option value="">-- Tất cả phòng ban --</option>
            <option :value="dept.id" v-for="dept in departments" :key="dept.id">{{ dept.name }}</option>
          </select>
        </div>

        <!-- 2. Ca làm việc -->
        <div>
          <label class="text-xs font-semibold text-gray-600 block mb-1">Ca làm việc</label>
          <select 
            v-model="selectedShift" 
            @change="onFilterChange" 
            class="custom-filter-input"
          >
            <option value="">-- Tất cả ca làm việc --</option>
            <option :value="item.id" v-for="item in shifts" :key="item.id">
              {{ item.name }} ({{ formatTime(item.start_time) }} - {{ formatTime(item.end_time) }})
            </option>
            <option value="unassigned">-- Chưa xếp ca --</option>
          </select>
        </div>

        <!-- 3. Ô tìm kiếm nhân sự (sửa lỗi vỡ layout) -->
        <div>
          <label class="text-xs font-semibold text-gray-600 block mb-1">Tìm kiếm nhân sự</label>
          <div class="relative w-full">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
              <i class="fa-solid fa-magnifying-glass text-sm"></i>
            </div>
            <input 
              type="text" 
              class="custom-filter-input pl-9 pr-8" 
              placeholder="Nhập tên, mã NV, SĐT..." 
              v-model="searchKeyword" 
              @keyup.enter="fetchData"
            />
            <button 
              type="button" 
              v-if="searchKeyword" 
              @click="searchKeyword = ''; onFilterChange();" 
              class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none"
            >
              <i class="fa-solid fa-xmark text-sm"></i>
            </button>
          </div>
        </div>
      </div>
    </vx-card>

    <!-- Danh sách nhân viên & Ca làm việc -->
    <vx-card no-shadow>
      <!-- Thanh công cụ chọn -->
      <div class="flex flex-wrap items-center justify-between mb-4 pb-3 border-b border-gray-100">
        <div class="flex items-center space-x-2">
          <vs-checkbox v-model="isAllSelected" @change="toggleSelectAll">
            <span class="font-semibold text-sm">Chọn tất cả ({{ filteredEmployees.length }})</span>
          </vs-checkbox>
          <span v-if="selectedEmployeeIds.length > 0" class="text-xs px-2 py-1 bg-primary text-white rounded font-bold ml-2">
            Đã chọn: {{ selectedEmployeeIds.length }}
          </span>
          <a 
            href="javascript:void(0)" 
            v-if="selectedEmployeeIds.length > 0" 
            @click="clearSelection" 
            class="text-xs text-danger ml-3 underline"
          >
            Bỏ chọn tất cả
          </a>
        </div>
        <div class="text-xs text-gray-500">
          Chế độ xếp ca: <strong class="text-success">Áp dụng đến khi thay đổi</strong>
        </div>
      </div>

      <!-- Bảng nhân sự -->
      <div class="vs-component vs-con-table stripe vs-table-primary">
        <div class="con-tablex vs-table--content">
          <div class="vs-con-tbody vs-table--tbody">
            <table class="vs-table vs-table--tbody-table">
              <thead class="vs-table--thead">
                <tr>
                  <th width="40" class="text-center">
                    <input 
                      type="checkbox" 
                      :checked="isAllSelected" 
                      @change="toggleSelectAll" 
                      style="cursor: pointer;"
                    />
                  </th>
                  <th width="50" class="text-center">STT</th>
                  <th width="120">Mã NV</th>
                  <th width="220">Họ và tên</th>
                  <th width="180">Phòng ban / Team</th>
                  <th width="220">Ca làm việc áp dụng</th>
                  <th width="160" class="text-center">Cập nhật gần nhất</th>
                  <th width="120" class="text-center">Thao tác</th>
                </tr>
              </thead>
              <tbody>
                <tr 
                  class="tr-values vs-table--tr" 
                  v-for="(emp, index) in paginatedEmployees" 
                  :key="emp.employee_id"
                  :class="{'bg-blue-50': isSelected(emp.employee_id)}"
                >
                  <td class="td vs-table--td text-center">
                    <input 
                      type="checkbox" 
                      :value="emp.employee_id" 
                      v-model="selectedEmployeeIds"
                      style="cursor: pointer;"
                    />
                  </td>
                  <td class="td vs-table--td text-center">{{ (currentPage - 1) * perPage + index + 1 }}</td>
                  <td class="td vs-table--td font-bold text-primary">{{ emp.employee_code || '---' }}</td>
                  <td class="td vs-table--td font-semibold text-gray-900">{{ emp.name }}</td>
                  <td class="td vs-table--td">{{ emp.team_name }}</td>
                  <td class="td vs-table--td">
                    <div v-if="emp.shift_name" class="flex items-center">
                      <vs-chip color="primary">
                        {{ emp.shift_name }}
                      </vs-chip>
                      <span v-if="emp.shift_start_time" class="text-xs text-gray-500 ml-1">
                        ({{ formatTime(emp.shift_start_time) }} - {{ formatTime(emp.shift_end_time) }})
                      </span>
                    </div>
                    <span v-else class="text-xs text-gray-400 italic">
                      <i class="fa-regular fa-clock mr-1"></i>Chưa xếp ca
                    </span>
                  </td>
                  <td class="td vs-table--td text-center text-xs text-gray-500">
                    {{ emp.updated_at || '---' }}
                  </td>
                  <td class="td vs-table--td text-center">
                    <vs-button 
                      size="small" 
                      color="primary" 
                      type="border" 
                      @click="openSinglePopup(emp)"
                    >
                      <i class="fa-solid fa-pen-to-square mr-1"></i> Xếp ca
                    </vs-button>
                  </td>
                </tr>
                <tr v-if="filteredEmployees.length === 0">
                  <td colspan="8" class="text-center p-4 text-gray-500">
                    Không tìm thấy nhân sự nào phù hợp
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Phân trang -->
      <div class="flex flex-wrap items-center justify-between mt-5" v-if="filteredEmployees.length > 0">
        <div class="text-sm text-gray-600 mb-2 md:mb-0">
          Hiển thị <strong>{{ (currentPage - 1) * perPage + 1 }}</strong> - <strong>{{ Math.min(currentPage * perPage, filteredEmployees.length) }}</strong> trong tổng số <strong>{{ filteredEmployees.length }}</strong> nhân sự
        </div>
        <div class="flex items-center" v-if="filteredEmployees.length > perPage">
          <span class="text-sm text-gray-600 mr-2">Mỗi trang:</span>
          <select v-model="perPage" @change="currentPage = 1" class="custom-filter-input mr-4" style="width: auto; padding: 6px 10px;">
            <option :value="20">20</option>
            <option :value="50">50</option>
            <option :value="100">100</option>
          </select>
          <vs-pagination
            :total="totalPages"
            v-model="currentPage"
          />
        </div>
      </div>
    </vx-card>

    <!-- Modal cấu hình Xếp Ca (áp dụng đến khi thay đổi) -->
    <vs-popup :title="popupTitle" :active.sync="popupActive">
      <div class="p-4">
        <!-- Thông tin đối tượng được xếp ca -->
        <div class="p-3 mb-4 rounded bg-primary-light border border-primary text-gray-800 text-sm">
          <div class="flex items-center">
            <feather-icon icon="UsersIcon" class="mr-2 text-primary" />
            <span class="font-semibold text-primary">Đối tượng áp dụng:</span>
            <span class="ml-2 font-bold">{{ targetDescription }}</span>
          </div>
          <div class="mt-2 text-xs text-gray-600 flex items-center">
            <i class="fa-solid fa-circle-info text-primary mr-1"></i>
            Ca làm việc này sẽ được áp dụng cho nhân sự từ thời điểm cập nhật cho đến khi có thay đổi tiếp theo.
          </div>
        </div>

        <!-- Chọn Ca làm việc -->
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Ca làm việc mới (*)</label>
            <select 
              v-model="form.shift_id" 
              class="custom-filter-input w-full font-semibold"
              style="padding: 10px 12px; font-size: 14px;"
            >
              <option :value="null" disabled>-- Chọn ca làm việc --</option>
              <option 
                :value="item.id" 
                v-for="item in shifts" 
                :key="item.id"
              >
                {{ item.name }} ({{ formatTime(item.start_time) }} - {{ formatTime(item.end_time) }})
              </option>
            </select>
          </div>
        </div>

        <!-- Buttons -->
        <div class="vx-row mt-6">
          <div class="vx-col w-full flex justify-end">
            <vs-button @click="handlePreSubmit" color="primary" class="mr-2 p-3 font-semibold">
              <i class="fa-solid fa-floppy-disk mr-1"></i> Xác nhận & Cập nhật ca
            </vs-button>
            <vs-button @click="popupActive = false" color="dark" type="border" class="p-3">
              <i class="fas fa-times mr-1"></i> Hủy
            </vs-button>
          </div>
        </div>
      </div>
    </vs-popup>
  </div>
</template>

<script>
import axios from '../../../http/axios.js';

export default {
  data() {
    return {
      employees: [],
      shifts: [],
      departments: [],
      selectedDepartment: '',
      selectedShift: '',
      searchKeyword: '',
      selectedEmployeeIds: [],
      isAllSelected: false,
      currentPage: 1,
      perPage: 20,
      
      // Popup
      popupActive: false,
      targetEmployee: null, // Khi chọn 1 nhân sự
      form: {
        shift_id: null
      }
    };
  },
  computed: {
    filteredEmployees() {
      if (!this.searchKeyword) return this.employees;
      const kw = this.searchKeyword.toLowerCase().trim();
      return this.employees.filter(emp => {
        return (
          (emp.name && emp.name.toLowerCase().includes(kw)) ||
          (emp.employee_code && emp.employee_code.toLowerCase().includes(kw)) ||
          (emp.team_name && emp.team_name.toLowerCase().includes(kw)) ||
          (emp.shift_name && emp.shift_name.toLowerCase().includes(kw))
        );
      });
    },
    paginatedEmployees() {
      const start = (this.currentPage - 1) * this.perPage;
      return this.filteredEmployees.slice(start, start + this.perPage);
    },
    totalPages() {
      return Math.ceil(this.filteredEmployees.length / this.perPage) || 1;
    },
    popupTitle() {
      if (this.targetEmployee) {
        return `Xếp ca cho nhân sự: ${this.targetEmployee.name}`;
      }
      return `Cập nhật ca làm việc (${this.selectedEmployeeIds.length} nhân sự đã chọn)`;
    },
    targetDescription() {
      if (this.targetEmployee) {
        return `${this.targetEmployee.name} (${this.targetEmployee.employee_code || '---'}) - ${this.targetEmployee.team_name}`;
      }
      return `${this.selectedEmployeeIds.length} nhân sự đã được chọn`;
    }
  },
  watch: {
    searchKeyword() {
      this.currentPage = 1;
      this.checkSelectAllStatus();
    },
    selectedEmployeeIds() {
      this.checkSelectAllStatus();
    }
  },
  created() {
    this.fetchDepartments();
    this.fetchShifts();
    this.fetchData();
  },
  methods: {
    fetchDepartments() {
      axios.p('/api/hrm/departments/list', { limit: 100 }).then(res => {
        if (res.data) {
          this.departments = res.data;
        }
      });
    },
    fetchShifts() {
      axios.p('/api/hrm/shifts/list').then(res => {
        this.shifts = res.data || [];
        if (this.shifts.length > 0 && !this.form.shift_id) {
          this.form.shift_id = this.shifts[0].id;
        }
      });
    },
    onFilterChange() {
      this.currentPage = 1;
      this.fetchData();
    },
    fetchData() {
      this.$vs.loading();
      axios.p('/api/hrm/shift-assignments/list', {
        department_id: this.selectedDepartment || null,
        shift_id: this.selectedShift || null,
        keyword: this.searchKeyword || null
      }).then(res => {
        this.$vs.loading.close();
        if (res.data && res.data.data) {
          this.employees = res.data.data;
        } else if (Array.isArray(res.data)) {
          this.employees = res.data;
        } else {
          this.employees = [];
        }
        this.checkSelectAllStatus();
      }).catch(err => {
        this.$vs.loading.close();
        console.error(err);
        this.$vs.notify({ title: 'Lỗi', text: 'Không thể tải danh sách nhân sự xếp ca', color: 'danger' });
      });
    },
    isSelected(empId) {
      return this.selectedEmployeeIds.includes(empId);
    },
    toggleSelectAll() {
      if (this.isAllSelected) {
        // Bỏ chọn tất cả
        this.selectedEmployeeIds = [];
        this.isAllSelected = false;
      } else {
        // Chọn tất cả nhân sự đang hiển thị theo bộ lọc
        this.selectedEmployeeIds = this.filteredEmployees.map(e => e.employee_id);
        this.isAllSelected = true;
      }
    },
    checkSelectAllStatus() {
      if (this.filteredEmployees.length === 0) {
        this.isAllSelected = false;
        return;
      }
      const allSelected = this.filteredEmployees.every(e => this.selectedEmployeeIds.includes(e.employee_id));
      this.isAllSelected = allSelected;
    },
    clearSelection() {
      this.selectedEmployeeIds = [];
      this.isAllSelected = false;
    },
    openSinglePopup(emp) {
      this.targetEmployee = emp;
      this.form.shift_id = emp.shift_id || (this.shifts.length > 0 ? this.shifts[0].id : null);
      this.popupActive = true;
    },
    openBatchPopup() {
      if (this.selectedEmployeeIds.length === 0) {
        this.$vs.notify({ title: 'Cảnh báo', text: 'Vui lòng chọn ít nhất 1 nhân sự để cập nhật ca', color: 'warning' });
        return;
      }
      this.targetEmployee = null;
      if (!this.form.shift_id && this.shifts.length > 0) {
        this.form.shift_id = this.shifts[0].id;
      }
      this.popupActive = true;
    },
    /**
     * BƯỚC CONFIRM:
     * Trước khi cập nhật ca làm việc áp dụng dài hạn, hiện dialog xác nhận rõ ràng
     */
    handlePreSubmit() {
      if (!this.form.shift_id) {
        this.$vs.notify({ title: 'Cảnh báo', text: 'Vui lòng chọn ca làm việc', color: 'warning' });
        return;
      }

      const shiftObj = this.shifts.find(s => s.id === this.form.shift_id);
      const shiftName = shiftObj ? shiftObj.name : `Ca #${this.form.shift_id}`;
      
      let targetText = '';
      if (this.targetEmployee) {
        targetText = `nhân sự "${this.targetEmployee.name}"`;
      } else {
        targetText = `${this.selectedEmployeeIds.length} nhân sự đã chọn`;
      }

      this.$vs.dialog({
        type: 'confirm',
        color: 'primary',
        title: 'Xác nhận cập nhật ca làm việc',
        text: `Bạn có chắc chắn muốn xếp ca "${shiftName}" cho ${targetText}? Ca làm việc này sẽ áp dụng cho đến khi có thay đổi tiếp theo.`,
        acceptText: 'Đồng ý cập nhật',
        cancelText: 'Hủy bỏ',
        accept: () => {
          this.executeSave();
        }
      });
    },
    executeSave() {
      const empIds = this.targetEmployee 
        ? [this.targetEmployee.employee_id] 
        : this.selectedEmployeeIds;

      if (!empIds || empIds.length === 0) {
        this.$vs.notify({ title: 'Cảnh báo', text: 'Không có nhân sự nào được chọn', color: 'warning' });
        return;
      }

      this.$vs.loading();
      axios.p('/api/hrm/shift-assignments/batch-assign', {
        employee_ids: empIds,
        shift_id: this.form.shift_id
      }).then(res => {
        this.$vs.loading.close();
        if (res.data.status === 1) {
          this.$vs.notify({ title: 'Thành công', text: res.data.message, color: 'success' });
          this.popupActive = false;
          this.clearSelection();
          this.fetchData();
        } else {
          this.$vs.notify({ title: 'Lỗi', text: res.data.message || 'Không thể cập nhật ca làm việc', color: 'danger' });
        }
      }).catch(err => {
        this.$vs.loading.close();
        console.error(err);
        this.$vs.notify({ title: 'Lỗi', text: 'Lỗi hệ thống khi cập nhật ca làm việc', color: 'danger' });
      });
    },
    formatTime(timeStr) {
      if (!timeStr) return '';
      return timeStr.substring(0, 5);
    }
  }
};
</script>

<style scoped>
.custom-filter-input {
  width: 100%;
  padding: 8px 12px;
  font-size: 13px;
  line-height: 1.5;
  color: #2c2c2c;
  background-color: #ffffff;
  border: 1px solid #d8d6de;
  border-radius: 5px;
  transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
  outline: none;
  box-sizing: border-box;
}

.custom-filter-input:focus {
  border-color: #7367f0;
  box-shadow: 0 3px 10px 0 rgba(115, 103, 240, 0.1);
}

.bg-primary-light {
  background-color: #f0f4ff;
}

.bg-blue-50 {
  background-color: #f8faff !important;
}
</style>
