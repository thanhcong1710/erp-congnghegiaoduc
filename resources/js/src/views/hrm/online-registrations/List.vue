<template>
  <div id="hrm-online-registrations">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between mb-4">
      <div>
        <h3 class="font-bold text-gray-800">QUẢN LÝ ĐĂNG KÝ LÀM ONLINE</h3>
        <p class="text-xs text-gray-500 mt-1">
          Dành cho nhân sự được xếp ca làm việc khác online đăng ký ngày làm online trong tương lai (cần Kế toán hoặc Admin phê duyệt)
        </p>
      </div>
      <div class="flex items-center space-x-2">
        <router-link to="/hrm/attendances" class="mr-2">
          <vs-button color="dark" type="border" class="p-3">
            <i class="fa-solid fa-arrow-left mr-1"></i> Bảng Chấm Công
          </vs-button>
        </router-link>
        <vs-button color="primary" type="filled" class="p-3" @click="openCreateModal()">
          <i class="fa-solid fa-plus mr-1"></i> Đăng Ký Làm Online
        </vs-button>
      </div>
    </div>

    <!-- Bộ lọc & Thống kê -->
    <vx-card no-shadow class="mb-4">
      <div class="flex flex-wrap items-center justify-between">
        <div class="flex flex-wrap items-center">
          <!-- Chọn Tháng -->
          <div class="mr-3 mb-2 flex items-center">
            <span class="text-sm font-medium mr-2 text-gray-700">Tháng:</span>
            <select
              v-model="filterMonth"
              @change="fetchRegistrations"
              class="vs-inputx vs-input--input normal"
              style="padding: 8px 12px; font-size: 13px; width: 110px"
            >
              <option :value="m" v-for="m in 12" :key="m">Tháng {{ m }}</option>
            </select>
          </div>

          <!-- Chọn Năm -->
          <div class="mr-3 mb-2 flex items-center">
            <span class="text-sm font-medium mr-2 text-gray-700">Năm:</span>
            <select
              v-model="filterYear"
              @change="fetchRegistrations"
              class="vs-inputx vs-input--input normal"
              style="padding: 8px 12px; font-size: 13px; width: 100px"
            >
              <option :value="y" v-for="y in [2024, 2025, 2026, 2027]" :key="y">{{ y }}</option>
            </select>
          </div>

          <!-- Chọn Phòng ban -->
          <div class="mr-3 mb-2 flex items-center">
            <span class="text-sm font-medium mr-2 text-gray-700">Phòng ban:</span>
            <select
              v-model="filterDepartment"
              @change="fetchRegistrations"
              class="vs-inputx vs-input--input normal"
              style="padding: 8px 12px; font-size: 13px; min-width: 170px"
            >
              <option value="all">-- Tất cả phòng ban --</option>
              <option :value="dept.id" v-for="dept in departments" :key="dept.id">
                {{ dept.name }}
              </option>
            </select>
          </div>

          <!-- Chọn Trạng thái -->
          <div class="mr-3 mb-2 flex items-center">
            <span class="text-sm font-medium mr-2 text-gray-700">Trạng thái:</span>
            <select
              v-model="filterStatus"
              @change="fetchRegistrations"
              class="vs-inputx vs-input--input normal"
              style="padding: 8px 12px; font-size: 13px; min-width: 150px"
            >
              <option value="all">-- Tất cả trạng thái --</option>
              <option :value="0">⏳ Chờ duyệt</option>
              <option :value="1">✓ Đã duyệt</option>
              <option :value="2">✗ Từ chối</option>
            </select>
          </div>

          <!-- Tìm kiếm từ khóa -->
          <div class="relative mr-3 mb-2" style="min-width: 220px;">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
              <i class="fa-solid fa-magnifying-glass text-xs"></i>
            </div>
            <input
              type="text"
              class="vs-inputx vs-input--input normal w-full"
              style="padding: 8px 12px 8px 30px; font-size: 13px;"
              placeholder="Lọc tên, mã NS, lý do..."
              v-model="searchKeyword"
              @keyup.enter="fetchRegistrations"
            />
          </div>
        </div>

        <div class="mb-2">
          <vs-button color="dark" type="border" class="p-3" @click="fetchRegistrations">
            <i class="fa-solid fa-rotate-right mr-1"></i> Làm mới
          </vs-button>
        </div>
      </div>
    </vx-card>

    <!-- Bảng danh sách đơn đăng ký làm online -->
    <vx-card no-shadow>
      <div class="vs-component vs-con-table stripe vs-table-primary">
        <div class="con-tablex vs-table--content">
          <div class="vs-con-tbody vs-table--tbody">
            <table class="vs-table vs-table--tbody-table">
              <thead class="vs-table--thead">
                <tr>
                  <th width="4%" class="text-center">STT</th>
                  <th width="9%">Mã NS</th>
                  <th width="15%">Họ và tên</th>
                  <th width="13%">Phòng ban</th>
                  <th width="11%">Ca hiện tại</th>
                  <th width="10%" class="text-center">Ngày làm online</th>
                  <th width="14%">Lý do</th>
                  <th width="8%" class="text-center">Đính kèm</th>
                  <th width="9%" class="text-center">Trạng thái</th>
                  <th width="12%">Người duyệt</th>
                  <th width="9%" class="text-center">Thao tác</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  class="tr-values vs-table--tr"
                  v-for="(tr, index) in paginatedList"
                  :key="tr.id"
                >
                  <td class="td vs-table--td text-center">
                    {{ (currentPage - 1) * perPage + index + 1 }}
                  </td>
                  <td class="td vs-table--td font-bold text-primary">
                    {{ tr.employee_code || "---" }}
                  </td>
                  <td class="td vs-table--td font-semibold text-gray-900">
                    {{ tr.employee_name }}
                  </td>
                  <td class="td vs-table--td">{{ tr.team_name }}</td>
                  <td class="td vs-table--td">
                    <span class="inline-block px-2 py-0.5 text-xs rounded bg-gray-100 text-gray-700">
                      {{ tr.current_shift_name }}
                    </span>
                  </td>
                  <td class="td vs-table--td text-center font-bold text-primary">
                    {{ tr.date | formatDateView }}
                  </td>
                  <td class="td vs-table--td text-sm">
                    <div class="truncate max-w-xs" :title="tr.reason">
                      {{ tr.reason || '---' }}
                    </div>
                  </td>
                  <td class="td vs-table--td text-center">
                    <a
                      v-if="tr.attachment_path"
                      :href="tr.attachment_path"
                      target="_blank"
                      class="text-primary hover:underline text-xs flex items-center justify-center font-medium"
                      :title="tr.attachment_name"
                    >
                      <i class="fa-solid fa-paperclip mr-1 text-sm"></i> Tải về
                    </a>
                    <span v-else class="text-gray-400 text-xs">---</span>
                  </td>
                  <td class="td vs-table--td text-center">
                    <vs-chip :color="getStatusBadgeColor(tr.status)">
                      {{ getStatusText(tr.status) }}
                    </vs-chip>
                  </td>
                  <td class="td vs-table--td text-xs">
                    <div v-if="tr.status === 1">
                      <div class="font-semibold text-success">{{ tr.approved_by_name || 'Kế toán/Admin' }}</div>
                      <div class="text-gray-400">{{ tr.approved_at }}</div>
                    </div>
                    <div v-else-if="tr.status === 2">
                      <div class="font-semibold text-danger">{{ tr.approved_by_name || 'Từ chối' }}</div>
                      <div class="text-gray-500 italic" :title="tr.rejected_reason">Ghi chú: {{ tr.rejected_reason }}</div>
                    </div>
                  </td>
                  <td class="td vs-table--td text-center" style="white-space: nowrap; min-width: 120px;">
                    <div class="flex items-center justify-center space-x-1" v-if="tr.status === 0">
                      <!-- Nút Duyệt cho Kế toán & Admin -->
                      <vs-button
                        v-if="canApproveRegistration"
                        size="small"
                        color="success"
                        type="flat"
                        class="p-2"
                        title="Duyệt đơn"
                        @click="approveRegistration(tr)"
                      >
                        <i class="fa-solid fa-check text-base"></i>
                      </vs-button>

                      <!-- Nút Từ chối cho Kế toán & Admin -->
                      <vs-button
                        v-if="canApproveRegistration"
                        size="small"
                        color="danger"
                        type="flat"
                        class="p-2"
                        title="Từ chối đơn"
                        @click="openRejectModal(tr)"
                      >
                        <i class="fa-solid fa-xmark text-base"></i>
                      </vs-button>

                      <!-- Nút Xóa (khi chưa duyệt) -->
                      <vs-button
                        v-if="canApproveRegistration || canDeleteRegistration(tr)"
                        size="small"
                        color="danger"
                        type="flat"
                        class="p-2"
                        title="Xóa đơn"
                        @click="deleteRegistration(tr)"
                      >
                        <i class="fa-solid fa-trash-can text-sm"></i>
                      </vs-button>
                    </div>
                    <span v-else class="text-xs text-gray-400">---</span>
                  </td>
                </tr>
                <tr v-if="!registrations.length">
                  <td colspan="11" class="text-center p-4 text-gray-500">
                    Chưa có đơn đăng ký làm online nào trong khoảng thời gian này
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Phân trang -->
      <div
        class="flex flex-wrap items-center justify-between mt-5"
        v-if="filteredList.length > 0"
      >
        <div class="text-sm text-gray-600 mb-2 md:mb-0">
          Hiển thị
          <strong>{{ (currentPage - 1) * perPage + 1 }}</strong>
          -
          <strong>{{ Math.min(currentPage * perPage, filteredList.length) }}</strong>
          trong tổng số
          <strong>{{ filteredList.length }}</strong> đơn đăng ký
        </div>
        <div class="flex items-center" v-if="filteredList.length > perPage">
          <span class="text-sm text-gray-600 mr-2">Mỗi trang:</span>
          <select
            v-model="perPage"
            @change="currentPage = 1"
            class="vs-inputx vs-input--input normal mr-4"
            style="padding: 6px 10px; font-size: 13px"
          >
            <option :value="20">20</option>
            <option :value="50">50</option>
            <option :value="100">100</option>
          </select>
          <vs-pagination :total="totalPages" v-model="currentPage" />
        </div>
      </div>
    </vx-card>

    <!-- Modal Đăng Ký Làm Online -->
    <vs-popup title="ĐĂNG KÝ LÀM ONLINE" :active.sync="createModalActive">
      <div class="p-4">
        <!-- Chú ý quy tắc -->
        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded text-xs text-blue-800 leading-relaxed">
          <p class="font-bold mb-1">
            <i class="fa-solid fa-circle-info mr-1"></i> Quy định đăng ký làm online:
          </p>
          <ul class="list-disc pl-4 space-y-1">
            <li>Nhân sự được xếp ca làm việc khác online có thể đăng ký các ngày trong tháng để làm việc online.</li>
            <li><strong>Chỉ được đăng ký ngày trong tương lai (từ ngày mai trở đi)</strong>. Hệ thống chặn chọn ngày trong quá khứ và cả ngày hôm nay.</li>
            <li>Đơn đăng ký cần được <strong>Kế toán</strong> hoặc <strong>Admin</strong> phê duyệt trước khi có hiệu lực.</li>
            <li>Khi được duyệt, nhân sự <strong>không cần điểm danh</strong> vào ngày đó và được tính vào số ngày làm online của tháng.</li>
          </ul>
        </div>

        <!-- Chọn nhân sự (nếu có quyền chọn hoặc hiển thị nhân sự hiện tại) -->
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Nhân sự (*)</label>
            <v-select
              class="w-full"
              v-model="form.employee_id"
              :options="employees"
              :reduce="item => item.id"
              label="displayName"
              placeholder="Chọn nhân viên đăng ký..."
              @input="onEmployeeChange"
            />
          </div>
        </div>

        <!-- Cảnh báo nếu nhân sự đang có ca cố định là Online -->
        <div v-if="selectedEmployeeShiftIsOnline" class="mb-4 p-3 bg-yellow-50 border border-yellow-300 rounded text-xs text-yellow-800">
          <i class="fa-solid fa-triangle-exclamation mr-1"></i>
          <strong>Lưu ý:</strong> Nhân sự này hiện đã được xếp ca làm việc cố định là <strong>Online</strong>. Nhân sự làm ca Online toàn thời gian không cần điểm danh và không cần tạo đơn đăng ký online theo ngày!
        </div>

        <!-- Chọn ngày làm online (Hỗ trợ chọn nhiều ngày trong tương lai) -->
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <label class="text-sm font-semibold text-gray-700 block mb-1">
              Ngày làm online (*) <span class="text-xs text-danger font-normal">(Chỉ được chọn từ ngày mai trở đi)</span>
            </label>
            <flat-pickr
              v-model="form.dates_input"
              :config="flatpickrFutureConfig"
              class="vs-inputx vs-input--input normal w-full"
              style="padding: 10px 14px; font-size: 13px;"
              placeholder="Chọn các ngày làm online trong tương lai..."
            />
            <div class="text-xs text-gray-500 mt-1">
              Bạn có thể click chọn một hoặc nhiều ngày làm online trong tương lai (ngày hiển thị định dạng dd/mm/yyyy).
            </div>
          </div>
        </div>

        <!-- Lý do làm online -->
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Lý do làm online (*)</label>
            <textarea
              placeholder="Nhập chi tiết lý do xin làm việc online..."
              rows="3"
              class="vs-inputx vs-input--input normal w-full"
              style="padding: 10px 14px; font-size: 13px;"
              v-model="form.reason"
            ></textarea>
          </div>
        </div>

        <!-- Đính kèm file -->
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Đính kèm file (nếu có)</label>
            <div class="border border-dashed border-gray-300 rounded-lg p-3 text-center bg-gray-50 hover:bg-gray-100 transition cursor-pointer relative">
              <input
                type="file"
                ref="fileInput"
                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                @change="handleFileChange"
              />
              <div v-if="!selectedFile">
                <i class="fa-solid fa-cloud-arrow-up text-2xl text-gray-400 mb-1"></i>
                <div class="text-xs text-gray-600 font-medium">Bấm để chọn file đính kèm (Ảnh, PDF, Word, Excel,...)</div>
                <div class="text-xs text-gray-400 mt-0.5">Dung lượng tối đa 10MB</div>
              </div>
              <div v-else class="flex items-center justify-between text-left px-2">
                <div class="flex items-center space-x-2">
                  <i class="fa-solid fa-file text-primary text-xl"></i>
                  <div>
                    <div class="text-xs font-bold text-gray-800">{{ selectedFile.name }}</div>
                    <div class="text-xs text-gray-500">{{ formatFileSize(selectedFile.size) }}</div>
                  </div>
                </div>
                <button
                  type="button"
                  @click.stop="removeSelectedFile"
                  class="text-danger hover:text-red-700 p-1"
                  title="Gỡ file"
                >
                  <i class="fa-solid fa-xmark text-base"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Buttons -->
        <div class="vx-row mt-6">
          <div class="vx-col w-full flex justify-end">
            <vs-button
              @click="submitRegistration"
              color="primary"
              type="filled"
              class="p-3 mr-2"
              :disabled="selectedEmployeeShiftIsOnline"
            >
              <i class="fa-solid fa-paper-plane mr-1"></i> Gửi Đơn Đăng Ký
            </vs-button>
            <vs-button @click="createModalActive = false" color="dark" type="border" class="p-3">
              <i class="fa-solid fa-xmark mr-1"></i> Hủy
            </vs-button>
          </div>
        </div>
      </div>
    </vs-popup>

    <!-- Modal Từ Chối Đơn -->
    <vs-popup title="TỪ CHỐI ĐƠN ĐĂNG KÝ LÀM ONLINE" :active.sync="rejectModalActive">
      <div class="p-4" v-if="targetRegistration">
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-xs text-red-800">
          Từ chối đơn đăng ký online ngày <strong>{{ targetRegistration.date | formatDateView }}</strong> của nhân viên <strong>{{ targetRegistration.employee_name }}</strong>.
        </div>
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Ghi chú / Lý do từ chối (*)</label>
            <textarea
              placeholder="Nhập ghi chú lý do Kế toán/Admin từ chối..."
              rows="3"
              class="vs-inputx vs-input--input normal w-full"
              style="padding: 10px 14px; font-size: 13px;"
              v-model="rejectReason"
            ></textarea>
          </div>
        </div>
        <div class="vx-row mt-6">
          <div class="vx-col w-full flex justify-end">
            <vs-button @click="confirmReject" color="danger" type="filled" class="p-3 mr-2">
              <i class="fa-solid fa-ban mr-1"></i> Xác Nhận Từ Chối
            </vs-button>
            <vs-button @click="rejectModalActive = false" color="dark" type="border" class="p-3">
              <i class="fa-solid fa-xmark mr-1"></i> Hủy
            </vs-button>
          </div>
        </div>
      </div>
    </vs-popup>
  </div>
</template>

<script>
import axios from '../../../http/axios.js';
import vSelect from 'vue-select';

export default {
  components: {
    'v-select': vSelect
  },
  data() {
    // Tính ngày mai để chặn quá khứ và hiện tại
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const tomorrowStr = tomorrow.toISOString().split('T')[0];

    return {
      filterMonth: new Date().getMonth() + 1,
      filterYear: new Date().getFullYear(),
      filterDepartment: 'all',
      filterStatus: 'all',
      searchKeyword: '',
      departments: [],
      employees: [],
      registrations: [],
      canApprove: false,

      // Pagination
      currentPage: 1,
      perPage: 20,

      // Modal tạo đơn
      createModalActive: false,
      selectedFile: null,
      selectedEmployeeShiftIsOnline: false,
      form: {
        employee_id: null,
        dates_input: '',
        reason: ''
      },

      // Cấu hình Flatpickr chỉ chọn ngày tương lai (từ ngày mai trở đi)
      flatpickrFutureConfig: {
        mode: 'multiple',
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd/m/Y',
        allowInput: false,
        minDate: tomorrowStr
      },

      // Modal từ chối
      rejectModalActive: false,
      targetRegistration: null,
      rejectReason: ''
    };
  },
  computed: {
    filteredList() {
      let list = this.registrations;
      if (this.searchKeyword) {
        const kw = this.searchKeyword.toLowerCase().trim();
        list = list.filter(item => {
          return (
            (item.employee_name && item.employee_name.toLowerCase().includes(kw)) ||
            (item.employee_code && item.employee_code.toLowerCase().includes(kw)) ||
            (item.team_name && item.team_name.toLowerCase().includes(kw)) ||
            (item.reason && item.reason.toLowerCase().includes(kw))
          );
        });
      }
      return list;
    },
    paginatedList() {
      const start = (this.currentPage - 1) * this.perPage;
      return this.filteredList.slice(start, start + this.perPage);
    },
    totalPages() {
      return Math.ceil(this.filteredList.length / this.perPage) || 1;
    },
    canApproveRegistration() {
      if (this.canApprove) return true;
      const user = this.$store.state.AppActiveUser || JSON.parse(localStorage.getItem('userInfo')) || {};
      if (user.uid === 1 || user.id === 1) return true;
      const adminRoleNames = ['admin', 'hrm_manager', 'accountant', 'ke_toan', 'ketoan'];
      const roleName = (user.roleName || user.role_name || '').toLowerCase();
      return adminRoleNames.some(r => roleName.includes(r));
    }
  },
  created() {
    this.fetchDepartments();
    this.fetchEmployees();
    this.fetchRegistrations();
  },
  methods: {
    canDeleteRegistration(tr) {
      if (this.canApproveRegistration) return true;
      const user = this.$store.state.AppActiveUser || JSON.parse(localStorage.getItem('userInfo')) || {};
      const currentUserId = user ? (user.id || user.uid) : null;
      return tr.status === 0 && currentUserId && (tr.user_id == currentUserId || tr.created_by == currentUserId);
    },
    fetchDepartments() {
      axios.p('/api/hrm/departments/list', { limit: 100 }).then(res => {
        this.departments = res.data.data || [];
      }).catch(err => console.error(err));
    },
    fetchEmployees() {
      axios.p('/api/hrm/employees/list', { limit: 500 }).then(res => {
        const raw = res.data.data || [];
        this.employees = raw.map(e => ({
          ...e,
          displayName: `${e.employee_code ? '[' + e.employee_code + '] ' : ''}${e.full_name || (e.user ? e.user.name : 'N/A')} - ${e.department ? e.department.name : 'Chưa xếp phòng'}`
        }));
      }).catch(err => console.error(err));
    },
    fetchRegistrations() {
      this.$vs.loading();
      axios.p('/api/hrm/online-registrations/list', {
        month: this.filterMonth,
        year: this.filterYear,
        department_id: this.filterDepartment,
        status: this.filterStatus,
        keyword: this.searchKeyword
      }).then(res => {
        this.$vs.loading.close();
        if (res.data.status === 1) {
          this.registrations = res.data.data || [];
          this.canApprove = !!res.data.can_approve;
          this.currentPage = 1;
        }
      }).catch(err => {
        this.$vs.loading.close();
        this.$vs.notify({ title: 'Lỗi', text: 'Không thể tải danh sách đăng ký làm online', color: 'danger' });
      });
    },
    openCreateModal() {
      // Tìm profile của user hiện tại
      const currentUserId = this.$store.state.AppActiveUser ? this.$store.state.AppActiveUser.id : null;
      let defaultEmpId = null;
      if (currentUserId) {
        const found = this.employees.find(e => e.user_id === currentUserId);
        if (found) {
          defaultEmpId = found.id;
        }
      }

      this.form = {
        employee_id: defaultEmpId,
        dates_input: '',
        reason: ''
      };
      this.selectedFile = null;
      if (this.$refs.fileInput) {
        this.$refs.fileInput.value = '';
      }
      this.onEmployeeChange(defaultEmpId);
      this.createModalActive = true;
    },
    onEmployeeChange(empId) {
      this.selectedEmployeeShiftIsOnline = false;
      if (!empId) return;
      const emp = this.employees.find(e => e.id === empId);
      if (emp && emp.shift && emp.shift.name) {
        if (emp.shift.name.toLowerCase().includes('online')) {
          this.selectedEmployeeShiftIsOnline = true;
        }
      }
    },
    handleFileChange(e) {
      const file = e.target.files[0];
      if (!file) return;
      if (file.size > 10 * 1024 * 1024) {
        this.$vs.notify({ title: 'Lỗi', text: 'Dung lượng file không được vượt quá 10MB', color: 'danger' });
        e.target.value = '';
        return;
      }
      this.selectedFile = file;
    },
    removeSelectedFile() {
      this.selectedFile = null;
      if (this.$refs.fileInput) {
        this.$refs.fileInput.value = '';
      }
    },
    formatFileSize(bytes) {
      if (!bytes) return '0 B';
      const k = 1024;
      const sizes = ['B', 'KB', 'MB', 'GB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    },
    submitRegistration() {
      if (!this.form.employee_id) {
        this.$vs.notify({ title: 'Cảnh báo', text: 'Vui lòng chọn nhân sự đăng ký!', color: 'warning' });
        return;
      }
      if (this.selectedEmployeeShiftIsOnline) {
        this.$vs.notify({ title: 'Cảnh báo', text: 'Nhân sự đã được xếp ca Online cố định, không cần đăng ký!', color: 'warning' });
        return;
      }
      if (!this.form.dates_input) {
        this.$vs.notify({ title: 'Cảnh báo', text: 'Vui lòng chọn ít nhất một ngày làm online!', color: 'warning' });
        return;
      }
      if (!this.form.reason || !this.form.reason.trim()) {
        this.$vs.notify({ title: 'Cảnh báo', text: 'Vui lòng nhập lý do đăng ký làm online!', color: 'warning' });
        return;
      }

      // Kiểm tra ngày tương lai
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      const selectedDates = this.form.dates_input.split(';').map(d => d.trim()).filter(Boolean);
      for (const d of selectedDates) {
        const parts = d.split('-');
        if (parts.length === 3) {
          const checkDate = new Date(parts[0], parts[1] - 1, parts[2]);
          checkDate.setHours(0, 0, 0, 0);
          if (checkDate <= today) {
            this.$vs.notify({
              title: 'Ngày không hợp lệ',
              text: `Ngày ${d} không hợp lệ! Không được đăng ký ngày trong quá khứ hoặc ngày hiện tại. Chỉ được chọn ngày trong tương lai (từ ngày mai trở đi)!`,
              color: 'danger'
            });
            return;
          }
        }
      }

      const formData = new FormData();
      formData.append('employee_id', this.form.employee_id);
      formData.append('dates', this.form.dates_input);
      formData.append('reason', this.form.reason);
      if (this.selectedFile) {
        formData.append('file', this.selectedFile);
      }

      this.$vs.loading();
      axios.post('/api/hrm/online-registrations/store', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      }).then(res => {
        this.$vs.loading.close();
        if (res.data.status === 1) {
          this.$vs.notify({ title: 'Thành công', text: res.data.message, color: 'success' });
          this.createModalActive = false;
          this.fetchRegistrations();
        } else {
          this.$vs.notify({ title: 'Lỗi', text: res.data.message, color: 'danger' });
        }
      }).catch(err => {
        this.$vs.loading.close();
        const msg = err.response && err.response.data && err.response.data.message
          ? err.response.data.message
          : 'Không thể gửi đơn đăng ký làm online!';
        this.$vs.notify({ title: 'Lỗi', text: msg, color: 'danger' });
      });
    },
    approveRegistration(tr) {
      this.$vs.dialog({
        type: 'confirm',
        color: 'success',
        title: 'Xác nhận duyệt',
        text: `Bạn có chắc chắn muốn phê duyệt đơn làm online ngày ${this.$options.filters.formatDateView(tr.date)} của nhân viên "${tr.employee_name}"?`,
        acceptText: 'Duyệt đơn',
        cancelText: 'Hủy',
        accept: () => {
          this.$vs.loading();
          axios.p(`/api/hrm/online-registrations/approve/${tr.id}`).then(res => {
            this.$vs.loading.close();
            if (res.data.status === 1) {
              this.$vs.notify({ title: 'Thành công', text: res.data.message, color: 'success' });
              this.fetchRegistrations();
            } else {
              this.$vs.notify({ title: 'Lỗi', text: res.data.message, color: 'danger' });
            }
          }).catch(err => {
            this.$vs.loading.close();
            const msg = err.response && err.response.data && err.response.data.message ? err.response.data.message : 'Lỗi duyệt đơn!';
            this.$vs.notify({ title: 'Lỗi', text: msg, color: 'danger' });
          });
        }
      });
    },
    openRejectModal(tr) {
      this.targetRegistration = tr;
      this.rejectReason = '';
      this.rejectModalActive = true;
    },
    confirmReject() {
      if (!this.rejectReason || !this.rejectReason.trim()) {
        this.$vs.notify({ title: 'Cảnh báo', text: 'Vui lòng nhập lý do từ chối!', color: 'warning' });
        return;
      }

      this.$vs.loading();
      axios.p(`/api/hrm/online-registrations/reject/${this.targetRegistration.id}`, {
        rejected_reason: this.rejectReason
      }).then(res => {
        this.$vs.loading.close();
        if (res.data.status === 1) {
          this.$vs.notify({ title: 'Thành công', text: res.data.message, color: 'success' });
          this.rejectModalActive = false;
          this.fetchRegistrations();
        } else {
          this.$vs.notify({ title: 'Lỗi', text: res.data.message, color: 'danger' });
        }
      }).catch(err => {
        this.$vs.loading.close();
        const msg = err.response && err.response.data && err.response.data.message ? err.response.data.message : 'Lỗi từ chối đơn!';
        this.$vs.notify({ title: 'Lỗi', text: msg, color: 'danger' });
      });
    },
    deleteRegistration(tr) {
      this.$vs.dialog({
        type: 'confirm',
        color: 'danger',
        title: 'Xác nhận xóa',
        text: `Bạn có chắc chắn muốn xóa đơn đăng ký ngày ${this.$options.filters.formatDateView(tr.date)} của "${tr.employee_name}"?`,
        acceptText: 'Xóa ngay',
        cancelText: 'Hủy',
        accept: () => {
          this.$vs.loading();
          axios.p(`/api/hrm/online-registrations/delete/${tr.id}`).then(res => {
            this.$vs.loading.close();
            if (res.data.status === 1) {
              this.$vs.notify({ title: 'Thành công', text: res.data.message, color: 'success' });
              this.fetchRegistrations();
            } else {
              this.$vs.notify({ title: 'Lỗi', text: res.data.message, color: 'danger' });
            }
          }).catch(err => {
            this.$vs.loading.close();
            const msg = err.response && err.response.data && err.response.data.message ? err.response.data.message : 'Lỗi xóa đơn!';
            this.$vs.notify({ title: 'Lỗi', text: msg, color: 'danger' });
          });
        }
      });
    },
    getStatusBadgeColor(status) {
      switch (status) {
        case 1:
          return 'success';
        case 2:
          return 'danger';
        default:
          return 'warning';
      }
    },
    getStatusText(status) {
      switch (status) {
        case 1:
          return '✓ Đã duyệt';
        case 2:
          return '✗ Từ chối';
        default:
          return '⏳ Chờ duyệt';
      }
    }
  }
};
</script>

<style scoped>
.bg-primary-light {
  background-color: #f0f4ff;
}
</style>

