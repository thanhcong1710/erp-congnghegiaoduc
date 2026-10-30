<template>
  <div id="page-hrm-contracts">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between mb-4">
      <div class="mb-2 md:mb-0">
        <h3 class="font-bold text-gray-800 flex items-center">
          <feather-icon icon="FileTextIcon" class="mr-2 text-primary" /> Quản Lý Hợp Đồng Lao Động
        </h3>
        <p class="text-sm text-gray-500 mt-1">Mã hợp đồng tự sinh theo format CHAM01, CHAM02... và đồng bộ theo nhân sự</p>
      </div>
      <div>
        <vs-button class="p-3" color="success" type="border" @click="openAddModal">
          <i class="fa-solid fa-plus mr-2"></i> Thêm Hợp Đồng Mới
        </vs-button>
      </div>
    </div>

    <vx-card no-shadow>
      <!-- Toolbar tìm kiếm -->
      <div class="flex flex-wrap items-center justify-between mb-4">
        <div></div>
        <div class="con-input-search vs-table--search">
          <input 
            type="text" 
            class="input-search vs-table--search-input" 
            style="padding: 12px 36px 12px 14px; font-size: 13px; min-width: 260px;" 
            placeholder="Số hợp đồng, tên nhân sự..." 
            v-model="searchKeyword" 
            @input="fetchContracts"
          >
          <i class="vs-icon notranslate icon-scale material-icons null" style="font-size: 20px;">search</i>
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
                  <th width="15%">SỐ HỢP ĐỒNG</th>
                  <th width="20%">NHÂN SỰ</th>
                  <th width="15%">LOẠI HỢP ĐỒNG</th>
                  <th width="12%">TỪ NGÀY</th>
                  <th width="12%">ĐẾN NGÀY</th>
                  <th width="11%">LƯƠNG CƠ BẢN</th>
                  <th width="110" class="text-center" style="white-space: nowrap;">TRẠNG THÁI</th>
                </tr>
              </thead>
              <tbody>
                <tr class="tr-values vs-table--tr" v-for="(tr, index) in contracts.data" :key="index">
                  <td class="td vs-table--td text-center">{{ index + 1 + ((contracts.current_page || 1) - 1) * (contracts.per_page || 20) }}</td>
                  <td class="td vs-table--td font-bold text-primary">{{ tr.contract_number }}</td>
                  <td class="td vs-table--td">
                    <div class="font-semibold text-gray-900">{{ tr.employee ? tr.employee.full_name : (tr.user ? tr.user.name : 'N/A') }}</div>
                    <div v-if="tr.employee && tr.employee.employee_code" class="text-xs text-gray-500 font-normal">Mã NS: {{ tr.employee.employee_code }}</div>
                  </td>
                  <td class="td vs-table--td">{{ tr.contract_type }}</td>
                  <td class="td vs-table--td">{{ tr.start_date }}</td>
                  <td class="td vs-table--td">{{ tr.end_date || 'Không thời hạn' }}</td>
                  <td class="td vs-table--td font-semibold text-gray-800">{{ tr.basic_salary | formatCurrency }}</td>
                  <td class="td vs-table--td text-center" style="white-space: nowrap;">
                    <span 
                      class="inline-block px-3 py-1 text-xs font-semibold rounded-full" 
                      :class="tr.status === 1 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                      style="white-space: nowrap; line-height: 1.4;"
                    >
                      {{ tr.status === 1 ? 'Hiệu lực' : 'Hết hạn' }}
                    </span>
                  </td>
                </tr>
                <tr v-if="!contracts.data || !contracts.data.length">
                  <td colspan="8" class="text-center p-4 text-gray-500">Chưa có hợp đồng nào</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Phân trang -->
      <div class="flex flex-wrap items-center mt-5" v-if="contracts.total > contracts.per_page">
        <vs-pagination
          :total="Math.ceil(contracts.total / contracts.per_page)"
          v-model="contracts.current_page"
          @change="changePage"
        />
      </div>
    </vx-card>

    <!-- Modal thêm hợp đồng mới -->
    <vs-popup title="THÊM HỢP ĐỒNG MỚI" :active.sync="modalActive">
      <div class="p-4">
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Chọn Nhân sự (*)</label>
            <select v-model="form.employee_id" @change="onEmployeeChange" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;">
              <option :value="emp.id" v-for="emp in employees" :key="emp.id">
                {{ emp.full_name || (emp.user ? emp.user.name : 'N/A') }} (Mã NS: {{ emp.employee_code }})
              </option>
            </select>
          </div>
        </div>

        <div class="vx-row mb-4">
          <div class="vx-col w-1/2">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Số hợp đồng (Để trống tự sinh CHAMxx)</label>
            <input type="text" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" placeholder="CHAM01" v-model="form.contract_number" />
          </div>
          <div class="vx-col w-1/2">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Loại hợp đồng (*)</label>
            <select v-model="form.contract_type" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;">
              <option value="Thử việc">Thử việc (2 tháng)</option>
              <option value="Hợp đồng 1 năm">Hợp đồng 1 năm</option>
              <option value="Hợp đồng 3 năm">Hợp đồng 3 năm</option>
              <option value="Không xác định thời hạn">Không xác định thời hạn</option>
              <option value="Cộng tác viên">Hợp đồng Cộng tác viên (CTV)</option>
            </select>
          </div>
        </div>

        <div class="vx-row mb-4">
          <div class="vx-col w-1/2">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Từ ngày (*)</label>
            <input type="date" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.start_date" />
          </div>
          <div class="vx-col w-1/2">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Đến ngày</label>
            <input type="date" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.end_date" />
          </div>
        </div>

        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Lương cơ bản (VNĐ)</label>
            <input type="number" placeholder="Nhập mức lương..." class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.basic_salary" />
          </div>
        </div>

        <div class="vx-row mt-6">
          <div class="vx-col w-full flex justify-end">
            <vs-button @click="saveContract()" color="success" type="filled" class="p-3 mr-2"><i class="fa-solid fa-floppy-disk mr-1"></i> Lưu Hợp Đồng</vs-button>
            <vs-button @click="modalActive = false" color="dark" type="border" class="p-3"><i class="fa-solid fa-xmark mr-1"></i> Hủy</vs-button>
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
      contracts: { data: [], total: 0, per_page: 20, current_page: 1 },
      employees: [],
      searchKeyword: '',
      modalActive: false,
      form: {
        employee_id: null,
        user_id: null,
        contract_number: '',
        contract_type: 'Hợp đồng 1 năm',
        start_date: '',
        end_date: '',
        basic_salary: 0
      }
    };
  },
  filters: {
    formatCurrency(val) {
      if (!val) return '0 VNĐ';
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val);
    }
  },
  created() {
    this.fetchContracts();
    this.fetchEmployees();
  },
  methods: {
    fetchContracts() {
      this.$vs.loading();
      axios.p('/api/hrm/employees/contracts-list', {
        page: this.contracts.current_page || 1,
        keyword: this.searchKeyword
      }).then(res => {
        this.contracts = res.data;
        this.$vs.loading.close();
      }).catch(err => {
        this.$vs.loading.close();
      });
    },
    changePage(p) {
      this.contracts.current_page = p;
      this.fetchContracts();
    },
    fetchEmployees() {
      axios.p('/api/hrm/employees/list', {}).then(res => {
        this.employees = res.data || [];
        if (this.employees.length > 0) {
          this.form.employee_id = this.employees[0].id;
          this.form.user_id = this.employees[0].user_id || null;
        }
      });
    },
    onEmployeeChange() {
      const found = this.employees.find(e => e.id === this.form.employee_id);
      this.form.user_id = found && found.user_id ? found.user_id : null;
    },
    openAddModal() {
      const firstEmp = this.employees.length > 0 ? this.employees[0] : null;
      this.form = {
        employee_id: firstEmp ? firstEmp.id : null,
        user_id: firstEmp && firstEmp.user_id ? firstEmp.user_id : null,
        contract_number: '',
        contract_type: 'Hợp đồng 1 năm',
        start_date: new Date().toISOString().split('T')[0],
        end_date: '',
        basic_salary: 0
      };
      this.modalActive = true;
    },
    saveContract() {
      if ((!this.form.employee_id && !this.form.user_id) || !this.form.start_date) {
        this.$vs.notify({ title: 'Lỗi', text: 'Vui lòng chọn nhân sự và ngày bắt đầu hợp đồng', color: 'danger' });
        return;
      }
      this.$vs.loading();
      axios.p('/api/hrm/employees/add-contract', this.form).then(res => {
        this.$vs.loading.close();
        if (res.data.status === 1) {
          this.$vs.notify({ title: 'Thành công', text: res.data.message, color: 'success' });
          this.modalActive = false;
          this.fetchContracts();
        } else {
          this.$vs.notify({ title: 'Lỗi', text: res.data.message, color: 'danger' });
        }
      }).catch(err => {
        this.$vs.loading.close();
        this.$vs.notify({ title: 'Lỗi', text: 'Thêm hợp đồng thất bại', color: 'danger' });
      });
    }
  }
};
</script>
