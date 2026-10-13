<template>
  <div>
    <vs-card>
      <div class="flex flex-wrap items-center justify-between mb-4">
        <div>
          <h4><i class="fa fa-file-contract text-primary mr-2"></i> Quản lý Hợp đồng Lao động</h4>
          <span class="text-sm text-gray-500">Mã hợp đồng tự sinh theo format CHAM01, CHAM02... và đồng bộ theo nhân sự</span>
        </div>
        <div class="flex space-x-2 mt-2 md:mt-0">
          <vs-button color="success" @click="openAddModal">
            <i class="fa fa-plus"></i> Thêm Hợp đồng mới
          </vs-button>
        </div>
      </div>

      <vs-table :data="contracts.data" search pagination :max-items="15">
        <template slot="thead">
          <vs-th>SỐ HỢP ĐỒNG</vs-th>
          <vs-th>NHÂN SỰ</vs-th>
          <vs-th>LOẠI HỢP ĐỒNG</vs-th>
          <vs-th>TỪ NGÀY</vs-th>
          <vs-th>ĐẾN NGÀY</vs-th>
          <vs-th>LƯƠNG CƠ BẢN</vs-th>
          <vs-th>TRẠNG THÁI</vs-th>
        </template>
        <template slot-scope="{data}">
          <vs-tr :key="indextr" v-for="(tr, indextr) in data">
            <vs-td>
              <span class="font-bold text-primary">{{ tr.contract_number }}</span>
            </vs-td>
            <vs-td class="font-medium text-gray-900">
              {{ tr.user ? tr.user.name : 'N/A' }}
            </vs-td>
            <vs-td>{{ tr.contract_type }}</vs-td>
            <vs-td>{{ tr.start_date }}</vs-td>
            <vs-td>{{ tr.end_date || 'Không thời hạn' }}</vs-td>
            <vs-td class="font-semibold">{{ tr.basic_salary | formatCurrency }}</vs-td>
            <vs-td>
              <vs-chip :color="tr.status === 1 ? 'success' : 'danger'">
                {{ tr.status === 1 ? 'Hiệu lực' : 'Hết hiệu lực' }}
              </vs-chip>
            </vs-td>
          </vs-tr>
        </template>
      </vs-table>
    </vs-card>

    <!-- Modal thêm hợp đồng mới -->
    <vs-popup title="Thêm Hợp đồng mới" :active.sync="modalActive">
      <div class="p-4">
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <label class="text-base font-medium">Chọn Nhân sự (*)</label>
            <select v-model="form.user_id" class="vs-inputx vs-input--input normal w-full mt-1 p-2 border rounded">
              <option :value="emp.user_id" v-for="emp in employees" :key="emp.id">
                {{ emp.user ? emp.user.name : 'N/A' }} ({{ emp.employee_code }})
              </option>
            </select>
          </div>
        </div>

        <div class="vx-row mb-4">
          <div class="vx-col w-1/2">
            <vs-input class="w-full" label="Số hợp đồng (Để trống tự sinh CHAMxx)" v-model="form.contract_number" placeholder="CHAM01" />
          </div>
          <div class="vx-col w-1/2">
            <label class="text-base font-medium">Loại hợp đồng (*)</label>
            <select v-model="form.contract_type" class="vs-inputx vs-input--input normal w-full mt-1 p-2 border rounded">
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
            <vs-input type="date" class="w-full" label="Từ ngày (*)" v-model="form.start_date" />
          </div>
          <div class="vx-col w-1/2">
            <vs-input type="date" class="w-full" label="Đến ngày" v-model="form.end_date" />
          </div>
        </div>

        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <vs-input type="number" class="w-full" label="Lương cơ bản (VNĐ)" v-model="form.basic_salary" />
          </div>
        </div>

        <div class="vx-row">
          <div class="vx-col w-full flex justify-end">
            <vs-button @click="saveContract()" color="success" class="mr-2"><i class="fa fa-save"></i> Lưu hợp đồng</vs-button>
            <vs-button @click="modalActive = false" color="dark" type="border"><i class="fas fa-undo-alt"></i> Hủy</vs-button>
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
      contracts: { data: [] },
      employees: [],
      modalActive: false,
      form: {
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
      axios.p('/api/hrm/employees/contracts-list', {}).then(res => {
        this.contracts = res.data;
        this.$vs.loading.close();
      }).catch(err => {
        this.$vs.loading.close();
      });
    },
    fetchEmployees() {
      axios.p('/api/hrm/employees/list', {}).then(res => {
        this.employees = res.data || [];
        if (this.employees.length > 0) {
          this.form.user_id = this.employees[0].user_id;
        }
      });
    },
    openAddModal() {
      this.form = {
        user_id: this.employees.length > 0 ? this.employees[0].user_id : null,
        contract_number: '',
        contract_type: 'Hợp đồng 1 năm',
        start_date: new Date().toISOString().split('T')[0],
        end_date: '',
        basic_salary: 0
      };
      this.modalActive = true;
    },
    saveContract() {
      if (!this.form.user_id || !this.form.start_date) {
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
