<template>
  <div id="page-hrm-leaves">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between mb-4">
      <div class="mb-2 md:mb-0">
        <h3 class="font-bold text-gray-800 flex items-center">
          <feather-icon icon="CalendarIcon" class="mr-2 text-primary" /> Quản Lý Đơn Nghỉ Phép & Đổi Ca
        </h3>
        <p class="text-sm text-gray-500 mt-1">Gửi đơn xin nghỉ, đổi ca, chuyển off sang onl và phê duyệt theo quy định</p>
      </div>
      <div>
        <vs-button class="p-3" color="success" type="border" @click="openPopup">
          <i class="fa-solid fa-plus mr-2"></i> Tạo Đơn Xin Phép
        </vs-button>
      </div>
    </div>

    <!-- 3 Widget Số Phép Năm Chuẩn Style VxCard -->
    <div class="vx-row mb-4">
      <div class="vx-col w-full md:w-1/3 mb-3 md:mb-0">
        <vx-card class="border-t-4 border-solid border-primary" no-shadow>
          <div class="flex items-center justify-between">
            <div>
              <span class="text-sm font-semibold text-gray-600 block mb-1">Phép năm được hưởng</span>
              <h2 class="font-bold text-primary">{{ leaveBalance.total_days }} <span class="text-sm text-gray-500">ngày</span></h2>
            </div>
            <div class="p-3 bg-primary-light rounded-full text-primary">
              <feather-icon icon="CalendarIcon" class="h-6 w-6" />
            </div>
          </div>
        </vx-card>
      </div>

      <div class="vx-col w-full md:w-1/3 mb-3 md:mb-0">
        <vx-card class="border-t-4 border-solid border-warning" no-shadow>
          <div class="flex items-center justify-between">
            <div>
              <span class="text-sm font-semibold text-gray-600 block mb-1">Đã sử dụng</span>
              <h2 class="font-bold text-warning">{{ leaveBalance.used_days }} <span class="text-sm text-gray-500">ngày</span></h2>
            </div>
            <div class="p-3 bg-warning-light rounded-full text-warning">
              <feather-icon icon="CheckCircleIcon" class="h-6 w-6" />
            </div>
          </div>
        </vx-card>
      </div>

      <div class="vx-col w-full md:w-1/3">
        <vx-card class="border-t-4 border-solid border-success" no-shadow>
          <div class="flex items-center justify-between">
            <div>
              <span class="text-sm font-semibold text-gray-600 block mb-1">Số ngày phép còn lại</span>
              <h2 class="font-bold text-success">{{ leaveBalance.remaining_days }} <span class="text-sm text-gray-500">ngày</span></h2>
            </div>
            <div class="p-3 bg-success-light rounded-full text-success">
              <feather-icon icon="ClockIcon" class="h-6 w-6" />
            </div>
          </div>
        </vx-card>
      </div>
    </div>

    <!-- Bảng danh sách đơn nghỉ phép -->
    <vx-card no-shadow>
      <!-- Bộ lọc và tìm kiếm -->
      <div class="flex flex-wrap items-center justify-between mb-4">
        <div class="flex items-center mb-2 md:mb-0">
          <span class="text-sm font-medium mr-2 text-gray-700">Trạng thái:</span>
          <select v-model="filterStatus" @change="fetchLeaves" class="vs-inputx vs-input--input normal" style="padding: 10px 14px; font-size: 13px; min-width: 170px;">
            <option value="all">-- Tất cả trạng thái --</option>
            <option value="0">Chờ duyệt</option>
            <option value="1">Đã phê duyệt</option>
            <option value="2">Bị từ chối</option>
          </select>
        </div>

        <div class="con-input-search vs-table--search">
          <input 
            type="text" 
            class="input-search vs-table--search-input" 
            style="padding: 12px 36px 12px 14px; font-size: 13px; min-width: 250px;" 
            placeholder="Tìm theo tên nhân viên..." 
            v-model="searchKeyword" 
            @input="fetchLeaves"
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
                  <th width="12%">Mã NS</th>
                  <th width="18%">Người làm đơn</th>
                  <th width="15%">Loại yêu cầu</th>
                  <th width="18%">Thời gian xin phép</th>
                  <th width="8%" class="text-center">Số ngày</th>
                  <th width="12%">Trạng thái</th>
                  <th width="15%">Phê duyệt</th>
                  <th width="10%" class="text-center">Thao tác</th>
                </tr>
              </thead>
              <tbody>
                <tr class="tr-values vs-table--tr" v-for="(tr, index) in leaves.data" :key="index">
                  <td class="td vs-table--td text-center">{{ index + 1 + ((leaves.current_page || 1) - 1) * (leaves.per_page || 20) }}</td>
                  <td class="td vs-table--td font-bold text-primary">{{ tr.user ? (tr.user.hrm_id || '---') : '' }}</td>
                  <td class="td vs-table--td font-semibold text-gray-900">{{ tr.user ? tr.user.name : 'Unknown' }}</td>
                  <td class="td vs-table--td">
                    <vs-chip :color="getRequestCategoryColor(tr.request_category)">
                      {{ getRequestCategoryText(tr.request_category) }}
                    </vs-chip>
                  </td>
                  <td class="td vs-table--td text-sm">
                    <div>{{ tr.start_date }} <i class="fa fa-arrow-right text-xs text-gray-400"></i> {{ tr.end_date }}</div>
                    <div class="text-xs text-gray-500 truncate" :title="tr.reason">Lý do: {{ tr.reason }}</div>
                  </td>
                  <td class="td vs-table--td text-center font-bold text-gray-800">{{ tr.duration_days }}</td>
                  <td class="td vs-table--td">
                    <vs-chip :color="tr.status === 1 ? 'success' : (tr.status === 2 ? 'danger' : 'warning')">
                      {{ tr.status === 1 ? 'Đã duyệt' : (tr.status === 2 ? 'Từ chối' : 'Chờ duyệt') }}
                    </vs-chip>
                  </td>
                  <td class="td vs-table--td">
                    <span v-if="tr.status === 1" class="font-semibold text-xs" :class="getApprovalTypeClass(tr.approval_type)">
                      {{ getApprovalTypeText(tr.approval_type) }}
                    </span>
                    <span v-else-if="tr.status === 2" class="text-danger text-xs">
                      {{ tr.rejection_reason || 'Từ chối' }}
                    </span>
                    <span v-else class="text-gray-400 text-xs">---</span>
                  </td>
                  <td class="td vs-table--td text-center list-action">
                    <vs-button 
                      v-if="tr.status === 0" 
                      size="small" 
                      color="primary" 
                      type="filled"
                      @click="openApprovePopup(tr)"
                      title="Phê duyệt đơn"
                    >
                      <i class="fa-solid fa-check mr-1"></i> Duyệt
                    </vs-button>
                  </td>
                </tr>
                <tr v-if="!leaves.data || !leaves.data.length">
                  <td colspan="9" class="text-center p-4 text-gray-500">Không có dữ liệu đơn nghỉ phép</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Phân trang -->
      <div class="flex flex-wrap items-center mt-5" v-if="leaves.total > leaves.per_page">
        <vs-pagination
          :total="Math.ceil(leaves.total / leaves.per_page)"
          v-model="leaves.current_page"
          @change="changePage"
        />
      </div>
    </vx-card>

    <!-- Popup Create Leave Request -->
    <vs-popup title="TẠO ĐƠN XIN NGHỈ / ĐỔI CA" :active.sync="popupActive">
      <div class="p-4">
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Loại yêu cầu (*)</label>
            <select class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.request_category">
              <option value="leave">Xin nghỉ phép thường</option>
              <option value="shift_change">Xin đổi ca làm việc</option>
              <option value="off_to_onl">Xin chuyển làm Off sang Onl (Hưởng 50% lương)</option>
            </select>
          </div>
        </div>

        <div class="vx-row mb-4" v-if="form.request_category === 'leave'">
          <div class="vx-col w-full">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Loại nghỉ phép</label>
            <select class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.leave_type_id">
              <option :value="item.id" v-for="item in leaveTypes" :key="item.id">{{ item.name }}</option>
            </select>
          </div>
        </div>

        <div class="vx-row mb-4">
          <div class="vx-col w-1/3">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Từ ngày (*)</label>
            <input type="date" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.start_date" @change="calculateDays" />
          </div>
          <div class="vx-col w-1/3">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Đến ngày (*)</label>
            <input type="date" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.end_date" @change="calculateDays" />
          </div>
          <div class="vx-col w-1/3">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Số ngày (*)</label>
            <input type="number" step="0.5" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.duration_days" />
          </div>
        </div>

        <div class="vx-row mb-4" v-if="form.request_category === 'shift_change'">
          <div class="vx-col w-full">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Thông tin ca xin đổi</label>
            <input type="text" placeholder="Ví dụ: Xin đổi từ ca Sáng sang ca Chiều ngày 15/10" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.target_shift_info" />
          </div>
        </div>

        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Lý do chi tiết (*)</label>
            <textarea placeholder="Nhập lý do xin nghỉ hoặc đổi ca..." rows="3" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.reason"></textarea>
          </div>
        </div>

        <div class="vx-row mt-6">
          <div class="vx-col w-full flex justify-end">
            <vs-button @click="saveData()" color="success" type="filled" class="p-3 mr-2"><i class="fa-solid fa-paper-plane mr-1"></i> Gửi Đơn</vs-button>
            <vs-button @click="popupActive = false" color="dark" type="border" class="p-3"><i class="fa-solid fa-xmark mr-1"></i> Hủy</vs-button>
          </div>
        </div>
      </div>
    </vs-popup>

    <!-- Popup Phê duyệt của HR (Theo ảnh 2) -->
    <vs-popup title="PHÊ DUYỆT ĐƠN NGHỈ PHÉP" :active.sync="approvePopupActive">
      <div class="p-4" v-if="selectedLeave">
        <p class="mb-4 text-sm text-gray-700">
          Phê duyệt đơn của nhân sự: <strong class="text-primary">{{ selectedLeave.user ? selectedLeave.user.name : '' }}</strong> 
          ({{ selectedLeave.duration_days }} ngày: {{ selectedLeave.start_date }} đến {{ selectedLeave.end_date }})
        </p>

        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <label class="text-sm font-semibold text-gray-800 mb-2 block">Lựa chọn phân loại phê duyệt của HR (*)</label>
            <div class="space-y-2">
              <label class="flex items-center p-3 border rounded cursor-pointer transition hover:bg-gray-50" :class="{'border-success bg-green-50': approveForm.approval_type === 'approved'}">
                <input type="radio" value="approved" v-model="approveForm.approval_type" class="mr-3" />
                <div>
                  <span class="font-bold text-success block">Đã duyệt</span>
                  <span class="text-xs text-gray-500">Hưởng 100% lương / Tự động trừ vào số ngày phép năm</span>
                </div>
              </label>

              <label class="flex items-center p-3 border rounded cursor-pointer transition hover:bg-gray-50" :class="{'border-warning bg-orange-50': approveForm.approval_type === 'half_salary'}">
                <input type="radio" value="half_salary" v-model="approveForm.approval_type" class="mr-3" />
                <div>
                  <span class="font-bold text-warning block">50% lương</span>
                  <span class="text-xs text-gray-500">Áp dụng cho nhân sự xin chuyển làm Off sang Onl</span>
                </div>
              </label>

              <label class="flex items-center p-3 border rounded cursor-pointer transition hover:bg-gray-50" :class="{'border-gray-500 bg-gray-50': approveForm.approval_type === 'late_unpaid'}">
                <input type="radio" value="late_unpaid" v-model="approveForm.approval_type" class="mr-3" />
                <div>
                  <span class="font-bold text-gray-700 block">Gửi đơn muộn (không lương)</span>
                  <span class="text-xs text-gray-500">Nhân viên gửi đơn muộn sau hạn quy định, không tính lương</span>
                </div>
              </label>

              <label class="flex items-center p-3 border rounded cursor-pointer transition hover:bg-gray-50" :class="{'border-danger bg-red-50': approveForm.approval_type === 'rejected'}">
                <input type="radio" value="rejected" v-model="approveForm.approval_type" class="mr-3" />
                <div>
                  <span class="font-bold text-danger block">Từ chối đơn</span>
                  <span class="text-xs text-gray-500">Không đồng ý phê duyệt đơn</span>
                </div>
              </label>
            </div>
          </div>
        </div>

        <div class="vx-row mb-4" v-if="approveForm.approval_type === 'rejected'">
          <div class="vx-col w-full">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Lý do từ chối (*)</label>
            <textarea placeholder="Nhập lý do từ chối..." rows="3" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="approveForm.rejection_reason"></textarea>
          </div>
        </div>

        <div class="vx-row mt-6">
          <div class="vx-col w-full flex justify-end">
            <vs-button @click="submitApproval()" color="success" type="filled" class="p-3 mr-2"><i class="fa-solid fa-check mr-1"></i> Xác Nhận Duyệt</vs-button>
            <vs-button @click="approvePopupActive = false" color="dark" type="border" class="p-3"><i class="fa-solid fa-xmark mr-1"></i> Đóng</vs-button>
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
      leaves: { data: [], total: 0, per_page: 20, current_page: 1 },
      leaveTypes: [],
      filterStatus: 'all',
      searchKeyword: '',
      leaveBalance: {
        total_days: 12,
        used_days: 0,
        remaining_days: 12
      },
      popupActive: false,
      approvePopupActive: false,
      selectedLeave: null,
      form: {
        request_category: 'leave',
        leave_type_id: null,
        start_date: '',
        end_date: '',
        duration_days: 1,
        target_shift_info: '',
        reason: ''
      },
      approveForm: {
        approval_type: 'approved',
        rejection_reason: ''
      }
    };
  },
  created() {
    this.fetchLeaveTypes();
    this.fetchLeaveBalance();
    this.fetchLeaves();
  },
  methods: {
    fetchLeaveTypes() {
      axios.p('/api/hrm/leave-types/list', { limit: 100 }).then(res => {
        if (res.data.data) {
          this.leaveTypes = res.data.data;
          if (this.leaveTypes.length > 0) {
            this.form.leave_type_id = this.leaveTypes[0].id;
          }
        }
      });
    },
    fetchLeaveBalance() {
      axios.p('/api/hrm/leaves/balance', {}).then(res => {
        if (res.data.status === 1) {
          this.leaveBalance = res.data.data;
        }
      });
    },
    fetchLeaves() {
      this.$vs.loading();
      axios.p('/api/hrm/leaves/list', { 
        limit: 20,
        page: this.leaves.current_page || 1,
        status: this.filterStatus,
        keyword: this.searchKeyword
      }).then(res => {
        this.leaves = res.data;
        this.$vs.loading.close();
      }).catch(err => {
        this.$vs.loading.close();
      });
    },
    changePage(p) {
      this.leaves.current_page = p;
      this.fetchLeaves();
    },
    calculateDays() {
      if (this.form.start_date && this.form.end_date) {
        const d1 = new Date(this.form.start_date);
        const d2 = new Date(this.form.end_date);
        const diffTime = d2 - d1;
        if (diffTime >= 0) {
          this.form.duration_days = Math.round(diffTime / (1000 * 60 * 60 * 24)) + 1;
        } else {
          this.form.duration_days = 1;
        }
      }
    },
    openPopup() {
      this.form = {
        request_category: 'leave',
        leave_type_id: this.leaveTypes.length > 0 ? this.leaveTypes[0].id : null,
        start_date: '',
        end_date: '',
        duration_days: 1,
        target_shift_info: '',
        reason: ''
      };
      this.popupActive = true;
    },
    saveData() {
      if (!this.form.start_date || !this.form.end_date || !this.form.reason) {
        this.$vs.notify({ title: 'Lỗi', text: 'Vui lòng điền đầy đủ các thông tin bắt buộc', color: 'danger' });
        return;
      }
      axios.p('/api/hrm/leaves/add', this.form).then(res => {
        if (res.data.status === 1) {
          this.$vs.notify({ title: 'Thành công', text: res.data.message, color: 'success' });
          this.popupActive = false;
          this.fetchLeaves();
          this.fetchLeaveBalance();
        } else {
          this.$vs.notify({ title: 'Lỗi', text: res.data.message, color: 'danger' });
        }
      });
    },
    openApprovePopup(leave) {
      this.selectedLeave = leave;
      this.approveForm = {
        approval_type: leave.request_category === 'off_to_onl' ? 'half_salary' : 'approved',
        rejection_reason: ''
      };
      this.approvePopupActive = true;
    },
    submitApproval() {
      if (this.approveForm.approval_type === 'rejected' && !this.approveForm.rejection_reason) {
        this.$vs.notify({ title: 'Lỗi', text: 'Vui lòng nhập lý do từ chối', color: 'danger' });
        return;
      }
      axios.p(`/api/hrm/leaves/approve/${this.selectedLeave.id}`, this.approveForm).then(res => {
        if (res.data.status === 1) {
          this.$vs.notify({ title: 'Thành công', text: res.data.message, color: 'success' });
          this.approvePopupActive = false;
          this.fetchLeaves();
          this.fetchLeaveBalance();
        } else {
          this.$vs.notify({ title: 'Lỗi', text: res.data.message, color: 'danger' });
        }
      });
    },
    getRequestCategoryText(cat) {
      const map = {
        leave: 'Nghỉ phép',
        shift_change: 'Đổi ca',
        off_to_onl: 'Off sang Onl (50% lương)'
      };
      return map[cat] || 'Khác';
    },
    getRequestCategoryColor(cat) {
      const map = {
        leave: 'primary',
        shift_change: 'warning',
        off_to_onl: 'dark'
      };
      return map[cat] || 'dark';
    },
    getApprovalTypeText(type) {
      const map = {
        approved: 'Đã duyệt (100% lương)',
        half_salary: '50% lương',
        late_unpaid: 'Gửi đơn muộn (không lương)',
        rejected: 'Từ chối'
      };
      return map[type] || 'Đã duyệt';
    },
    getApprovalTypeClass(type) {
      const map = {
        approved: 'text-success',
        half_salary: 'text-warning',
        late_unpaid: 'text-gray-600',
        rejected: 'text-danger'
      };
      return map[type] || 'text-success';
    }
  }
};
</script>
