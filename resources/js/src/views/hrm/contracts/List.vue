<template>
  <div id="page-hrm-contracts">
    <!-- Header -->
    <div class="hrm-page-header">
      <div>
        <h3><feather-icon icon="FileTextIcon" class="mr-2 text-primary" /> Hợp Đồng Lao Động</h3>
        <p>Mã hợp đồng tự sinh theo công ty (CHAMxx, MEGAxx...) và đồng bộ theo nhân sự</p>
      </div>
      <div class="hrm-page-actions">
        <vs-button color="dark" type="border" @click="fetchContracts"><i class="fa-solid fa-rotate-right mr-1"></i> Tải lại</vs-button>
        <vs-button color="primary" @click="openAddModal"><i class="fa-solid fa-plus mr-1"></i> Thêm hợp đồng</vs-button>
      </div>
    </div>

    <vx-card no-shadow>
      <!-- Toolbar tìm kiếm -->
      <div class="hrm-toolbar">
        <div class="hrm-toolbar-group text-sm text-gray-600">
          Tổng số <strong class="mx-1">{{ contracts.total || 0 }}</strong> hợp đồng
        </div>
        <div class="hrm-toolbar-group">
          <div class="hrm-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input
              v-model="searchKeyword"
              type="text"
              class="vs-inputx vs-input--input normal hrm-input"
              placeholder="Số hợp đồng, tên nhân sự..."
              @input="onSearchInput"
            >
          </div>
        </div>
      </div>

      <!-- Bảng hợp đồng -->
      <div class="vs-component vs-con-table stripe vs-table-primary">
        <div class="con-tablex vs-table--content">
          <table class="vs-table vs-table--tbody-table">
            <thead class="vs-table--thead">
              <tr>
                <th class="text-center" style="width: 56px;">STT</th>
                <th>Số hợp đồng</th>
                <th>Nhân sự</th>
                <th>Loại hợp đồng</th>
                <th>Thời hạn</th>
                <th class="text-right">Lương cơ bản</th>
                <th class="text-center">Trạng thái</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(tr, index) in contracts.data" :key="tr.id || index" class="tr-values vs-table--tr">
                <td class="td vs-table--td text-center text-gray-500">{{ rowNumber(index) }}</td>
                <td class="td vs-table--td">
                  <div class="font-bold text-primary">{{ tr.contract_number }}</div>
                  <span class="hrm-badge mt-1" :class="isMega(tr) ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'">
                    {{ isMega(tr) ? 'MEGA' : 'CHAM' }}
                  </span>
                </td>
                <td class="td vs-table--td">
                  <div class="font-semibold text-gray-900">{{ tr.employee ? tr.employee.full_name : (tr.user ? tr.user.name : 'N/A') }}</div>
                  <div v-if="tr.employee && tr.employee.employee_code" class="text-xs text-gray-500">Mã NS: {{ tr.employee.employee_code }}</div>
                </td>
                <td class="td vs-table--td">{{ tr.contract_type }}</td>
                <td class="td vs-table--td whitespace-nowrap">
                  <div>{{ fmtDate(tr.start_date) }} → {{ fmtDate(tr.end_date, 'Không thời hạn') }}</div>
                  <div v-if="expiryHint(tr)" class="text-xs font-semibold" :class="expiryHint(tr).cls">{{ expiryHint(tr).text }}</div>
                </td>
                <td class="td vs-table--td text-right font-semibold text-gray-800 whitespace-nowrap">{{ formatCurrency(tr.basic_salary) }}</td>
                <td class="td vs-table--td text-center">
                  <span class="hrm-badge" :class="tr.status === 1 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                    {{ tr.status === 1 ? 'Hiệu lực' : 'Hết hạn' }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div v-if="!contracts.data || !contracts.data.length" class="hrm-empty">
        <i class="fa-regular fa-file-lines"></i>
        <div class="hrm-empty-title">Chưa có hợp đồng nào</div>
        <span class="text-sm">Thử đổi từ khóa tìm kiếm hoặc thêm hợp đồng mới</span>
      </div>

      <!-- Phân trang -->
      <div v-if="contracts.total > contracts.per_page" class="hrm-pagination">
        <div class="text-sm text-gray-600">Trang {{ contracts.current_page }} / {{ totalPages }}</div>
        <vs-pagination :total="totalPages" v-model="contracts.current_page" @change="changePage" />
      </div>
    </vx-card>

    <!-- Popup thêm hợp đồng -->
    <vs-popup class="hrm-popup" title="Thêm hợp đồng mới" :active.sync="modalActive">
      <div class="vx-row mb-4">
        <div class="vx-col w-full">
          <label class="hrm-form-label">Nhân sự <span class="text-danger">*</span></label>
          <v-select
            v-model="form.employee_id"
            :options="employeeOptions"
            :reduce="opt => opt.value"
            label="label"
            placeholder="Gõ tên hoặc mã nhân sự để tìm..."
            @input="onEmployeeChange"
          >
            <span slot="no-options">Không tìm thấy nhân sự</span>
          </v-select>
        </div>
      </div>

      <div class="vx-row mb-4">
        <div class="vx-col w-1/2">
          <label class="hrm-form-label">Số hợp đồng</label>
          <input v-model.trim="form.contract_number" type="text" class="vs-inputx vs-input--input normal hrm-input" placeholder="Để trống để tự sinh">
        </div>
        <div class="vx-col w-1/2">
          <label class="hrm-form-label">Loại hợp đồng <span class="text-danger">*</span></label>
          <select v-model="form.contract_type" class="vs-inputx vs-input--input normal hrm-input" @change="suggestEndDate">
            <option v-for="t in contractTypes" :key="t.value" :value="t.value">{{ t.label }}</option>
          </select>
        </div>
      </div>

      <div class="vx-row mb-4">
        <div class="vx-col w-1/2">
          <label class="hrm-form-label">Từ ngày <span class="text-danger">*</span></label>
          <flat-pickr v-model="form.start_date" :config="flatpickrDateConfig" class="vs-inputx vs-input--input normal hrm-input" placeholder="dd/mm/yyyy" />
        </div>
        <div class="vx-col w-1/2">
          <label class="hrm-form-label">Đến ngày</label>
          <flat-pickr v-model="form.end_date" :config="flatpickrDateConfig" class="vs-inputx vs-input--input normal hrm-input" placeholder="Để trống nếu không thời hạn" />
        </div>
      </div>

      <div class="vx-row">
        <div class="vx-col w-full">
          <label class="hrm-form-label">Lương cơ bản (VNĐ)</label>
          <input v-model.number="form.basic_salary" type="number" min="0" step="100000" class="vs-inputx vs-input--input normal hrm-input" placeholder="Nhập mức lương...">
          <span v-if="form.basic_salary" class="hrm-form-hint">{{ formatCurrency(form.basic_salary) }}</span>
        </div>
      </div>

      <div class="hrm-popup-footer">
        <vs-button color="dark" type="border" @click="modalActive = false"><i class="fa-solid fa-xmark mr-1"></i> Hủy</vs-button>
        <vs-button color="primary" :disabled="saving" @click="saveContract"><i class="fa-solid fa-floppy-disk mr-1"></i> Lưu hợp đồng</vs-button>
      </div>
    </vs-popup>
  </div>
</template>

<script>
import axios from '../../../http/axios.js';
import moment from 'moment';
import vSelect from 'vue-select';

// Số tháng mặc định theo loại hợp đồng (dùng để gợi ý ngày kết thúc)
const CONTRACT_TYPES = [
  { value: 'Thử việc', label: 'Thử việc (2 tháng)', months: 2 },
  { value: 'Hợp đồng 1 năm', label: 'Hợp đồng 1 năm', months: 12 },
  { value: 'Hợp đồng 3 năm', label: 'Hợp đồng 3 năm', months: 36 },
  { value: 'Không xác định thời hạn', label: 'Không xác định thời hạn', months: 0 },
  { value: 'Cộng tác viên', label: 'Hợp đồng Cộng tác viên (CTV)', months: 0 }
];

export default {
  components: { 'v-select': vSelect },
  data() {
    return {
      flatpickrDateConfig: { dateFormat: 'Y-m-d', altInput: true, altFormat: 'd/m/Y', allowInput: true },
      contractTypes: CONTRACT_TYPES,
      contracts: { data: [], total: 0, per_page: 20, current_page: 1 },
      employees: [],
      searchKeyword: '',
      searchTimer: null,
      modalActive: false,
      saving: false,
      form: this.emptyForm()
    };
  },
  computed: {
    totalPages() {
      return Math.max(1, Math.ceil((this.contracts.total || 0) / (this.contracts.per_page || 20)));
    },
    employeeOptions() {
      return this.employees.map(e => ({
        value: e.id,
        label: `${e.full_name || (e.user ? e.user.name : 'N/A')}${e.employee_code ? ' – ' + e.employee_code : ''}`
      }));
    }
  },
  watch: {
    // Đổi ngày bắt đầu → gợi ý lại ngày kết thúc theo loại hợp đồng
    'form.start_date'(newVal, oldVal) {
      if (newVal && oldVal && newVal !== oldVal) this.suggestEndDate();
    }
  },
  created() {
    this.fetchContracts();
    this.fetchEmployees();
  },
  beforeDestroy() {
    clearTimeout(this.searchTimer);
  },
  methods: {
    emptyForm() {
      return {
        employee_id: null,
        user_id: null,
        contract_number: '',
        contract_type: 'Hợp đồng 1 năm',
        start_date: moment().format('YYYY-MM-DD'),
        end_date: moment().add(12, 'months').subtract(1, 'day').format('YYYY-MM-DD'),
        basic_salary: 0
      };
    },
    fmtDate(value, fallback = '') {
      return value ? moment(value).format('DD/MM/YYYY') : fallback;
    },
    formatCurrency(val) {
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(Number(val) || 0);
    },
    isMega(tr) {
      return (tr.contract_number && tr.contract_number.startsWith('MEGA')) || (tr.company && tr.company.code === 'MEGA');
    },
    rowNumber(index) {
      return index + 1 + ((this.contracts.current_page || 1) - 1) * (this.contracts.per_page || 20);
    },
    // Cảnh báo hợp đồng sắp hết hạn (≤ 30 ngày) hoặc đã quá hạn
    expiryHint(tr) {
      if (!tr.end_date || tr.status !== 1) return null;
      const days = moment(tr.end_date).startOf('day').diff(moment().startOf('day'), 'days');
      if (days < 0) return { text: `Đã quá hạn ${-days} ngày`, cls: 'text-danger' };
      if (days <= 30) return { text: `Còn ${days} ngày hết hạn`, cls: 'text-warning' };
      return null;
    },
    onSearchInput() {
      // Debounce: chỉ gọi API sau khi người dùng ngừng gõ 400ms
      clearTimeout(this.searchTimer);
      this.searchTimer = setTimeout(() => {
        this.contracts.current_page = 1;
        this.fetchContracts();
      }, 400);
    },
    fetchContracts() {
      this.$vs.loading();
      axios.p('/api/hrm/employees/contracts-list', {
        page: this.contracts.current_page || 1,
        keyword: this.searchKeyword
      })
        .then(res => { this.contracts = res.data || { data: [], total: 0, per_page: 20, current_page: 1 }; })
        .catch(() => this.notify('Lỗi', 'Không thể tải danh sách hợp đồng', 'danger'))
        .finally(() => this.$vs.loading.close());
    },
    changePage(page) {
      this.contracts.current_page = page;
      this.fetchContracts();
    },
    fetchEmployees() {
      axios.p('/api/hrm/employees/list', {})
        .then(res => {
          // API trả về { status, data: [...] } (bản cũ trả mảng trực tiếp)
          const body = res.data || {};
          this.employees = Array.isArray(body) ? body : (Array.isArray(body.data) ? body.data : []);
        })
        .catch(() => { this.employees = []; });
    },
    onEmployeeChange() {
      const found = this.employees.find(e => e.id === this.form.employee_id);
      this.form.user_id = found && found.user_id ? found.user_id : null;
    },
    suggestEndDate() {
      const type = CONTRACT_TYPES.find(t => t.value === this.form.contract_type);
      if (!type || !this.form.start_date) return;
      this.form.end_date = type.months
        ? moment(this.form.start_date).add(type.months, 'months').subtract(1, 'day').format('YYYY-MM-DD')
        : '';
    },
    openAddModal() {
      this.form = this.emptyForm();
      this.modalActive = true;
    },
    saveContract() {
      if (!this.form.employee_id || !this.form.start_date) {
        return this.notify('Thiếu thông tin', 'Vui lòng chọn nhân sự và ngày bắt đầu hợp đồng', 'warning');
      }
      if (this.form.end_date && moment(this.form.end_date).isBefore(this.form.start_date)) {
        return this.notify('Sai thông tin', 'Ngày kết thúc phải sau ngày bắt đầu', 'warning');
      }
      this.saving = true;
      axios.p('/api/hrm/employees/add-contract', this.form)
        .then(res => {
          if (res.data.status === 1) {
            this.notify('Thành công', res.data.message, 'success');
            this.modalActive = false;
            this.fetchContracts();
          } else {
            this.notify('Lỗi', res.data.message, 'danger');
          }
        })
        .catch(() => this.notify('Lỗi', 'Thêm hợp đồng thất bại', 'danger'))
        .finally(() => { this.saving = false; });
    },
    notify(title, text, color) {
      this.$vs.notify({ title, text, color });
    }
  }
};
</script>
