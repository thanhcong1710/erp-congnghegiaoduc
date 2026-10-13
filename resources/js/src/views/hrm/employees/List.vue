<template>
  <div>
    <vs-card>
      <div class="flex flex-wrap items-center justify-between mb-4">
        <div>
          <h4>Quản lý Danh sách Nhân sự</h4>
          <span class="text-sm text-gray-500">Phân loại Fulltime, CTV, Thử việc & quản lý trạng thái hoạt động</span>
        </div>
        <div class="flex space-x-2 mt-2 md:mt-0">
          <router-link :to="'/hrm/employees/add'">
            <vs-button color="success"><i class="fa fa-user-plus"></i> Thêm nhân sự mới</vs-button>
          </router-link>
        </div>
      </div>

      <!-- Bộ lọc Tab theo loại nhân sự -->
      <div class="flex flex-wrap items-center justify-between mb-4 pb-3 border-b">
        <div class="flex space-x-2 mb-2 md:mb-0">
          <vs-button 
            :type="filterType === 'all' ? 'filled' : 'border'" 
            color="primary" 
            size="small" 
            @click="setFilterType('all')"
          >
            Tất cả ({{ employees.length }})
          </vs-button>
          <vs-button 
            :type="filterType === 'fulltime' ? 'filled' : 'border'" 
            color="success" 
            size="small" 
            @click="setFilterType('fulltime')"
          >
            Fulltime
          </vs-button>
          <vs-button 
            :type="filterType === 'ctv' ? 'filled' : 'border'" 
            color="warning" 
            size="small" 
            @click="setFilterType('ctv')"
          >
            Cộng tác viên (CTV)
          </vs-button>
          <vs-button 
            :type="filterType === 'probation' ? 'filled' : 'border'" 
            color="dark" 
            size="small" 
            @click="setFilterType('probation')"
          >
            Thử việc
          </vs-button>
        </div>

        <div class="flex items-center space-x-2">
          <select v-model="filterWorkStatus" @change="fetchEmployees" class="p-2 border rounded text-sm">
            <option value="all">-- Tất cả trạng thái --</option>
            <option value="working">Đang làm việc</option>
            <option value="resigned">Đã nghỉ việc</option>
          </select>
        </div>
      </div>
      
      <vs-table :data="filteredEmployees" search pagination :max-items="15">
        <template slot="thead">
          <vs-th>Mã NS</vs-th>
          <vs-th>Tên nhân viên</vs-th>
          <vs-th>Phòng ban / Team</vs-th>
          <vs-th>Phân loại</vs-th>
          <vs-th>Thời gian thử việc</vs-th>
          <vs-th>Trạng thái hoạt động</vs-th>
          <vs-th>Thao tác</vs-th>
        </template>
        <template slot-scope="{data}">
          <vs-tr :key="indextr" v-for="(tr, indextr) in data">
            <vs-td>
              <span class="font-bold text-primary">{{ tr.employee_code || '---' }}</span>
            </vs-td>
            <vs-td>
              <div class="font-medium text-gray-900">{{ tr.user ? tr.user.name : '' }}</div>
              <div class="text-xs text-gray-500">{{ tr.phone || tr.user.email }}</div>
            </vs-td>
            <vs-td>{{ tr.department ? tr.department.name : 'Chưa phân bổ' }}</vs-td>
            <vs-td>
              <vs-chip :color="getEmployeeTypeColor(tr.employee_type)">
                {{ getEmployeeTypeText(tr.employee_type) }}
              </vs-chip>
              <div v-if="tr.employee_type === 'ctv' && tr.approval_status === 0" class="mt-1">
                <span class="text-xs text-danger font-semibold"><i class="fa fa-clock"></i> Chờ HR duyệt</span>
              </div>
            </vs-td>
            <vs-td>
              <div v-if="tr.employee_type === 'probation'" class="text-xs">
                <div>Từ: <strong>{{ tr.probation_start_date || '---' }}</strong></div>
                <div>Đến: <strong>{{ tr.probation_end_date || '---' }}</strong></div>
                <vs-button 
                  size="small" 
                  color="success" 
                  type="border" 
                  class="mt-1 py-1 px-2 text-xs" 
                  @click="convertToFulltime(tr.id)"
                >
                  <i class="fa fa-arrow-up"></i> Chuyển Fulltime
                </vs-button>
              </div>
              <span v-else class="text-gray-400 text-xs">---</span>
            </vs-td>
            <vs-td>
              <div>
                <vs-chip :color="tr.work_status === 'working' ? 'success' : 'danger'">
                  {{ tr.work_status === 'working' ? 'Đang làm việc' : 'Đã nghỉ' }}
                </vs-chip>
              </div>
              <div v-if="tr.work_status === 'resigned' && tr.resigned_reason" class="text-xs text-gray-500 mt-1">
                Lý do: {{ tr.resigned_reason }}
              </div>
            </vs-td>
            <vs-td>
              <div class="flex items-center space-x-1">
                <!-- Nút HR duyệt CTV -->
                <vs-button 
                  v-if="tr.employee_type === 'ctv' && tr.approval_status === 0" 
                  size="small" 
                  color="primary" 
                  @click="approveCtv(tr.id)"
                  title="Duyệt CTV"
                >
                  <i class="fa fa-check"></i> Duyệt
                </vs-button>

                <!-- Nút cập nhật trạng thái hoạt động -->
                <vs-button 
                  size="small" 
                  color="warning" 
                  type="border" 
                  @click="openWorkStatusModal(tr)" 
                  title="Đổi trạng thái hoạt động"
                >
                  <i class="fa fa-user-clock"></i>
                </vs-button>

                <!-- Nút sửa hồ sơ -->
                <router-link :to="`/hrm/employees/edit/${tr.id}`">
                  <vs-button size="small" color="success" title="Chỉnh sửa"><i class="fa fa-edit"></i></vs-button>
                </router-link>
              </div>
            </vs-td>
          </vs-tr>
        </template>
      </vs-table>
    </vs-card>

    <!-- Modal Cập nhật trạng thái hoạt động (Đang làm / Đã nghỉ) -->
    <vs-popup title="Cập nhật Trạng thái Hoạt động" :active.sync="statusModalActive">
      <div class="p-4" v-if="selectedEmployee">
        <p class="mb-4">Nhân sự: <strong>{{ selectedEmployee.user ? selectedEmployee.user.name : '' }}</strong> ({{ selectedEmployee.employee_code }})</p>
        
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <label class="text-base font-medium">Trạng thái hoạt động (*)</label>
            <select v-model="statusForm.work_status" class="w-full p-2 border rounded mt-1">
              <option value="working"> Đang làm việc</option>
              <option value="resigned"> Đã nghỉ việc</option>
            </select>
          </div>
        </div>

        <div v-if="statusForm.work_status === 'resigned'">
          <div class="vx-row mb-4">
            <div class="vx-col w-full">
              <vs-input type="date" class="w-full" label="Ngày nghỉ việc" v-model="statusForm.resigned_date" />
            </div>
          </div>
          <div class="vx-row mb-4">
            <div class="vx-col w-full">
              <vs-textarea label="Lý do nghỉ việc (*)" v-model="statusForm.resigned_reason" />
            </div>
          </div>
        </div>

        <div class="vx-row">
          <div class="vx-col w-full flex justify-end">
            <vs-button @click="saveWorkStatus()" color="success" class="mr-2"><i class="fa fa-save"></i> Cập nhật</vs-button>
            <vs-button @click="statusModalActive = false" color="dark" type="border"><i class="fas fa-undo-alt"></i> Hủy</vs-button>
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
      if (this.filterType === 'all') return this.employees;
      return this.employees.filter(emp => emp.employee_type === this.filterType);
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
        work_status: this.filterWorkStatus
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
