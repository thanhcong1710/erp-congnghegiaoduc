<template>
  <div id="page-hrm-employees-list">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between mb-4">
      <div class="mb-2 md:mb-0">
        <h3 class="font-bold text-gray-800 flex items-center">
          <feather-icon icon="UsersIcon" class="mr-2 text-primary" /> Quản Lý Danh Sách Nhân Sự
        </h3>
        <p class="text-sm text-gray-500 mt-1">Phân loại Fulltime, CTV, Thử việc & theo dõi trạng thái hoạt động</p>
      </div>
      <div>
        <router-link :to="'/hrm/employees/add'">
          <vs-button class="p-3" color="success" type="border">
            <i class="fa-solid fa-user-plus mr-2"></i> Thêm Nhân Sự Mới
          </vs-button>
        </router-link>
      </div>
    </div>

    <vx-card no-shadow>
      <!-- Toolbar lọc & tìm kiếm -->
      <div class="flex flex-wrap items-center justify-between mb-4 pb-3 border-b border-gray-200">
        <!-- Bộ lọc phân loại Tab -->
        <div class="flex flex-wrap items-center space-x-2 mb-2 md:mb-0">
          <vs-button 
            :type="filterType === 'all' ? 'filled' : 'border'" 
            color="primary" 
            size="small" 
            class="py-2 px-3"
            @click="setFilterType('all')"
          >
            Tất cả ({{ employees.length }})
          </vs-button>
          <vs-button 
            :type="filterType === 'fulltime' ? 'filled' : 'border'" 
            color="success" 
            size="small" 
            class="py-2 px-3"
            @click="setFilterType('fulltime')"
          >
            Fulltime
          </vs-button>
          <vs-button 
            :type="filterType === 'ctv' ? 'filled' : 'border'" 
            color="warning" 
            size="small" 
            class="py-2 px-3"
            @click="setFilterType('ctv')"
          >
            Cộng tác viên (CTV)
          </vs-button>
          <vs-button 
            :type="filterType === 'probation' ? 'filled' : 'border'" 
            color="dark" 
            size="small" 
            class="py-2 px-3"
            @click="setFilterType('probation')"
          >
            Thử việc
          </vs-button>
        </div>

        <!-- Bộ lọc trạng thái & ô tìm kiếm -->
        <div class="flex flex-wrap items-center">
          <div class="mr-3 mb-2 md:mb-0">
            <select v-model="filterWorkStatus" @change="fetchEmployees" class="vs-inputx vs-input--input normal" style="padding: 10px 14px; font-size: 13px; min-width: 170px;">
              <option value="all">-- Tất cả trạng thái --</option>
              <option value="working">Đang làm việc</option>
              <option value="resigned">Đã nghỉ việc</option>
            </select>
          </div>

          <div class="con-input-search vs-table--search">
            <input 
              type="text" 
              class="input-search vs-table--search-input" 
              style="padding: 12px 36px 12px 14px; font-size: 13px; min-width: 240px;" 
              placeholder="Mã, tên, số điện thoại..." 
              v-model="searchKeyword" 
              @input="fetchEmployees"
            >
            <i class="vs-icon notranslate icon-scale material-icons null" style="font-size: 20px;">search</i>
          </div>
        </div>
      </div>

      <!-- Table chuẩn hệ thống -->
      <div class="vs-component vs-con-table stripe vs-table-primary">
        <div class="con-tablex vs-table--content">
          <div class="vs-con-tbody vs-table--tbody">
            <table class="vs-table vs-table--tbody-table">
              <thead class="vs-table--thead">
                <tr>
                  <th width="5%" class="text-center">STT</th>
                  <th width="10%">Mã NS</th>
                  <th width="20%">Họ và tên</th>
                  <th width="15%">Team / Bộ phận</th>
                  <th width="13%">Phân loại</th>
                  <th width="15%">Thời gian thử việc</th>
                  <th width="12%">Trạng thái</th>
                  <th width="120" class="text-center" style="white-space: nowrap;">Thao tác</th>
                </tr>
              </thead>
              <tbody>
                <tr class="tr-values vs-table--tr" v-for="(tr, index) in filteredEmployees" :key="index">
                  <td class="td vs-table--td text-center">{{ index + 1 }}</td>
                  <td class="td vs-table--td font-bold text-primary">{{ tr.employee_code || '---' }}</td>
                  <td class="td vs-table--td">
                    <div class="font-semibold text-gray-900">{{ tr.full_name || (tr.user ? tr.user.name : '') }}</div>
                    <div class="text-xs text-gray-500">{{ tr.phone || tr.email || (tr.user ? tr.user.email : '') }}</div>
                  </td>
                  <td class="td vs-table--td">{{ tr.department ? tr.department.name : 'Chưa phân bổ' }}</td>
                  <td class="td vs-table--td">
                    <vs-chip :color="getEmployeeTypeColor(tr.employee_type)">
                      {{ getEmployeeTypeText(tr.employee_type) }}
                    </vs-chip>
                    <div v-if="tr.employee_type === 'ctv' && tr.approval_status === 0" class="mt-1">
                      <span class="text-xs text-danger font-semibold"><i class="fa fa-clock"></i> Chờ HR duyệt</span>
                    </div>
                  </td>
                  <td class="td vs-table--td text-xs">
                    <div v-if="tr.employee_type === 'probation'">
                      <div>Từ: <strong>{{ tr.probation_start_date || '---' }}</strong></div>
                      <div>Đến: <strong>{{ tr.probation_end_date || '---' }}</strong></div>
                      <vs-button 
                        size="small" 
                        color="success" 
                        type="border" 
                        class="mt-1 py-1 px-2 text-xs" 
                        @click="convertToFulltime(tr.id)"
                      >
                        <i class="fa fa-arrow-up mr-1"></i> Lên Fulltime
                      </vs-button>
                    </div>
                    <span v-else class="text-gray-400">---</span>
                  </td>
                  <td class="td vs-table--td">
                    <vs-chip :color="tr.work_status === 'working' ? 'success' : 'danger'">
                      {{ tr.work_status === 'working' ? 'Đang làm' : 'Đã nghỉ' }}
                    </vs-chip>
                    <div v-if="tr.work_status === 'resigned' && tr.resigned_reason" class="text-xs text-gray-500 mt-1">
                      {{ tr.resigned_reason }}
                    </div>
                  </td>
                  <td class="td vs-table--td text-center list-action" style="white-space: nowrap;">
                    <!-- Nút HR duyệt CTV -->
                    <vs-button 
                      v-if="tr.employee_type === 'ctv' && tr.approval_status === 0" 
                      size="small" 
                      color="primary" 
                      class="mr-1"
                      @click="approveCtv(tr.id)"
                      title="Duyệt CTV"
                    >
                      <i class="fa-solid fa-check"></i>
                    </vs-button>

                    <!-- Nút đổi trạng thái hoạt động -->
                    <vs-button 
                      size="small" 
                      color="warning" 
                      type="border" 
                      class="mr-1"
                      @click="openWorkStatusModal(tr)" 
                      title="Đổi trạng thái hoạt động"
                    >
                      <i class="fa-solid fa-user-clock"></i>
                    </vs-button>

                    <!-- Nút sửa hồ sơ -->
                    <router-link :to="`/hrm/employees/edit/${tr.id}`">
                      <vs-button size="small" color="success" title="Chỉnh sửa"><i class="fa-solid fa-edit"></i></vs-button>
                    </router-link>
                  </td>
                </tr>
                <tr v-if="!filteredEmployees.length">
                  <td colspan="8" class="text-center p-4 text-gray-500">Không có dữ liệu nhân sự</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </vx-card>

    <!-- Modal Cập nhật trạng thái hoạt động (Đang làm / Đã nghỉ) -->
    <vs-popup title="CẬP NHẬT TRẠNG THÁI HOẠT ĐỘNG" :active.sync="statusModalActive">
      <div class="p-4" v-if="selectedEmployee">
        <p class="mb-4 text-sm text-gray-700">
          Nhân sự: <strong class="text-primary">{{ selectedEmployee.full_name || (selectedEmployee.user ? selectedEmployee.user.name : '') }}</strong> (Mã NS: {{ selectedEmployee.employee_code }})
        </p>
        
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Trạng thái hoạt động (*)</label>
            <select v-model="statusForm.work_status" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;">
              <option value="working">Đang làm việc</option>
              <option value="resigned">Đã nghỉ việc</option>
            </select>
          </div>
        </div>

        <div v-if="statusForm.work_status === 'resigned'">
          <div class="vx-row mb-4">
            <div class="vx-col w-full">
              <label class="text-sm font-semibold text-gray-700 block mb-1">Ngày nghỉ việc</label>
              <input type="date" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="statusForm.resigned_date" />
            </div>
          </div>
          <div class="vx-row mb-4">
            <div class="vx-col w-full">
              <label class="text-sm font-semibold text-gray-700 block mb-1">Lý do nghỉ việc (*)</label>
              <textarea placeholder="Nhập lý do nghỉ việc..." rows="3" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="statusForm.resigned_reason"></textarea>
            </div>
          </div>
        </div>

        <div class="vx-row mt-6">
          <div class="vx-col w-full flex justify-end">
            <vs-button @click="saveWorkStatus()" color="success" type="filled" class="p-3 mr-2"><i class="fa-solid fa-floppy-disk mr-1"></i> Cập Nhật</vs-button>
            <vs-button @click="statusModalActive = false" color="dark" type="border" class="p-3"><i class="fa-solid fa-xmark mr-1"></i> Hủy</vs-button>
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
      filterType: 'all',
      filterWorkStatus: 'all',
      searchKeyword: '',
      statusModalActive: false,
      selectedEmployee: null,
      statusForm: {
        work_status: 'working',
        resigned_date: '',
        resigned_reason: ''
      }
    };
  },
  computed: {
    filteredEmployees() {
      let list = this.employees;
      if (this.filterType !== 'all') {
        list = list.filter(emp => emp.employee_type === this.filterType);
      }
      return list;
    }
  },
  created() {
    this.fetchEmployees();
  },
  methods: {
    setFilterType(type) {
      this.filterType = type;
    },
    fetchEmployees() {
      this.$vs.loading();
      axios.p('/api/hrm/employees/list', {
        work_status: this.filterWorkStatus,
        keyword: this.searchKeyword
      }).then(res => {
        this.employees = res.data || [];
        this.$vs.loading.close();
      }).catch(err => {
        this.$vs.loading.close();
        console.error(err);
      });
    },
    getEmployeeTypeText(type) {
      const map = {
        fulltime: 'Fulltime',
        ctv: 'Cộng tác viên (CTV)',
        probation: 'Thử việc'
      };
      return map[type] || 'Fulltime';
    },
    getEmployeeTypeColor(type) {
      const map = {
        fulltime: 'success',
        ctv: 'warning',
        probation: 'dark'
      };
      return map[type] || 'primary';
    },
    approveCtv(id) {
      this.$vs.loading();
      axios.p(`/api/hrm/employees/approve-ctv/${id}`).then(res => {
        this.$vs.loading.close();
        this.$vs.notify({ title: 'Thành công', text: res.data.message, color: 'success' });
        this.fetchEmployees();
      }).catch(err => {
        this.$vs.loading.close();
        this.$vs.notify({ title: 'Lỗi', text: 'Không thể duyệt CTV', color: 'danger' });
      });
    },
    convertToFulltime(id) {
      this.$vs.loading();
      axios.p(`/api/hrm/employees/convert-fulltime/${id}`).then(res => {
        this.$vs.loading.close();
        this.$vs.notify({ title: 'Thành công', text: res.data.message, color: 'success' });
        this.fetchEmployees();
      }).catch(err => {
        this.$vs.loading.close();
        this.$vs.notify({ title: 'Lỗi', text: 'Chuyển đổi thất bại', color: 'danger' });
      });
    },
    openWorkStatusModal(emp) {
      this.selectedEmployee = emp;
      this.statusForm = {
        work_status: emp.work_status || 'working',
        resigned_date: emp.resigned_date || new Date().toISOString().split('T')[0],
        resigned_reason: emp.resigned_reason || ''
      };
      this.statusModalActive = true;
    },
    saveWorkStatus() {
      if (this.statusForm.work_status === 'resigned' && !this.statusForm.resigned_reason) {
        this.$vs.notify({ title: 'Lỗi', text: 'Vui lòng nhập lý do nghỉ việc', color: 'danger' });
        return;
      }
      this.$vs.loading();
      axios.p(`/api/hrm/employees/update-work-status/${this.selectedEmployee.id}`, this.statusForm).then(res => {
        this.$vs.loading.close();
        this.$vs.notify({ title: 'Thành công', text: res.data.message, color: 'success' });
        this.statusModalActive = false;
        this.fetchEmployees();
      }).catch(err => {
        this.$vs.loading.close();
        this.$vs.notify({ title: 'Lỗi', text: 'Cập nhật thất bại', color: 'danger' });
      });
    }
  }
};
</script>
