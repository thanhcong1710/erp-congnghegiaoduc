<template>
  <div>
    <!-- Widget Hiển thị thông tin Phép năm của Nhân sự -->
    <div class="vx-row mb-4">
      <div class="vx-col w-full md:w-1/3 mb-2">
        <vx-card class="text-center bg-primary text-white">
          <div class="p-3">
            <h5 class="text-white mb-1">Phép năm được hưởng</h5>
            <h2 class="text-white font-bold">{{ leaveBalance.total_days }} ngày</h2>
          </div>
        </vx-card>
      </div>
      <div class="vx-col w-full md:w-1/3 mb-2">
        <vx-card class="text-center bg-warning text-white">
          <div class="p-3">
            <h5 class="text-white mb-1">Đã sử dụng</h5>
            <h2 class="text-white font-bold">{{ leaveBalance.used_days }} ngày</h2>
          </div>
        </vx-card>
      </div>
      <div class="vx-col w-full md:w-1/3 mb-2">
        <vx-card class="text-center bg-success text-white">
          <div class="p-3">
            <h5 class="text-white mb-1">Phép còn lại</h5>
            <h2 class="text-white font-bold">{{ leaveBalance.remaining_days }} ngày</h2>
          </div>
        </vx-card>
      </div>
    </div>

    <vs-card>
      <div class="flex flex-wrap items-center justify-between mb-4">
        <div>
          <h4>Quản lý Đơn nghỉ phép & Đổi ca</h4>
          <span class="text-sm text-gray-500">Tạo đơn xin nghỉ, đổi ca, chuyển off sang onl và phê duyệt</span>
        </div>
        <div class="flex space-x-2 mt-2 md:mt-0">
          <vs-button color="success" @click="openPopup()"><i class="fa fa-plus"></i> Tạo đơn xin phép</vs-button>
        </div>
      </div>
      
      <vs-table :data="leaves.data" search pagination :max-items="20">
        <template slot="thead">
          <vs-th>Mã NS</vs-th>
          <vs-th>Người làm đơn</vs-th>
          <vs-th>Loại yêu cầu</vs-th>
          <vs-th>Thời gian</vs-th>
          <vs-th>Số ngày</vs-th>
          <vs-th>Lý do / Ca đổi</vs-th>
          <vs-th>Trạng thái</vs-th>
          <vs-th>Hình thức duyệt</vs-th>
          <vs-th>Thao tác</vs-th>
        </template>
        <template slot-scope="{data}">
          <vs-tr :key="indextr" v-for="(tr, indextr) in data">
            <vs-td>
              <span class="font-bold text-primary">{{ tr.user ? (tr.user.hrm_id || '---') : '' }}</span>
            </vs-td>
            <vs-td>
              <span class="font-medium text-gray-900">{{ tr.user ? tr.user.name : 'Unknown' }}</span>
            </vs-td>
            <vs-td>
              <vs-chip :color="getRequestCategoryColor(tr.request_category)">
                {{ getRequestCategoryText(tr.request_category) }}
              </vs-chip>
            </vs-td>
            <vs-td>
              <span class="text-sm">{{ tr.start_date }} <i class="fa fa-arrow-right text-xs text-gray-400"></i> {{ tr.end_date }}</span>
            </vs-td>
            <vs-td>
              <span class="font-semibold">{{ tr.duration_days }} ngày</span>
            </vs-td>
            <vs-td>
              <div>{{ tr.reason }}</div>
              <div v-if="tr.target_shift_info" class="text-xs text-primary mt-1">
                <strong>Ca đổi:</strong> {{ tr.target_shift_info }}
              </div>
            </vs-td>
            <vs-td>
              <vs-chip :color="tr.status === 1 ? 'success' : (tr.status === 2 ? 'danger' : 'warning')">
                {{ tr.status === 1 ? 'Đã duyệt' : (tr.status === 2 ? 'Từ chối' : 'Chờ duyệt') }}
              </vs-chip>
            </vs-td>
            <vs-td>
              <span v-if="tr.status === 1" class="font-semibold text-sm" :class="getApprovalTypeClass(tr.approval_type)">
                {{ getApprovalTypeText(tr.approval_type) }}
              </span>
              <span v-else-if="tr.status === 2" class="text-danger text-sm">
                {{ tr.rejection_reason || 'Từ chối' }}
              </span>
              <span v-else class="text-gray-400">---</span>
            </vs-td>
            <vs-td>
              <div class="flex space-x-2">
                <vs-button v-if="tr.status === 0" size="small" color="primary" @click="openApprovePopup(tr)">
                  <i class="fa fa-check"></i> Duyệt đơn
                </vs-button>
              </div>
            </vs-td>
          </vs-tr>
        </template>
      </vs-table>
    </vs-card>

    <!-- Popup Create Leave Request -->
    <vs-popup title="Tạo Đơn xin nghỉ / Đổi ca" :active.sync="popupActive">
      <div class="p-4">
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <label class="text-base font-medium">Loại yêu cầu (*)</label>
            <select class="vs-inputx vs-input--input normal w-full mt-1 p-2 border rounded" v-model="form.request_category">
              <option value="leave"> Xin nghỉ phép thường</option>
              <option value="shift_change"> Xin đổi ca làm việc</option>
              <option value="off_to_onl"> Xin chuyển làm Off sang Onl (Hưởng 50% lương)</option>
            </select>
          </div>
        </div>

        <div class="vx-row mb-4" v-if="form.request_category === 'leave'">
          <div class="vx-col w-full">
            <label class="text-base font-medium">Loại nghỉ</label>
            <select class="vs-inputx vs-input--input normal w-full mt-1 p-2 border rounded" v-model="form.leave_type_id">
              <option :value="item.id" v-for="item in leaveTypes" :key="item.id">{{ item.name }}</option>
            </select>
          </div>
        </div>

        <div class="vx-row mb-4">
          <div class="vx-col w-1/3">
            <vs-input type="date" class="w-full" label="Từ ngày (*)" v-model="form.start_date" @change="calculateDays" />
          </div>
          <div class="vx-col w-1/3">
            <vs-input type="date" class="w-full" label="Đến ngày (*)" v-model="form.end_date" @change="calculateDays" />
          </div>
          <div class="vx-col w-1/3">
            <vs-input type="number" step="0.5" class="w-full" label="Số ngày nghỉ (*)" v-model="form.duration_days" />
          </div>
        </div>

        <div class="vx-row mb-4" v-if="form.request_category === 'shift_change'">
          <div class="vx-col w-full">
            <vs-input class="w-full" label="Thông tin ca xin đổi (Ví dụ: Ca sáng sang ca chiều ngày...)" v-model="form.target_shift_info" />
          </div>
        </div>

        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <vs-textarea label="Lý do chi tiết (*)" v-model="form.reason" />
          </div>
        </div>

        <div class="vx-row">
          <div class="vx-col w-full flex justify-end">
            <vs-button @click="saveData()" color="success" class="mr-2"><i class="fa fa-paper-plane"></i> Gửi đơn</vs-button>
            <vs-button @click="popupActive = false" color="dark" type="border"><i class="fas fa-undo-alt"></i> Hủy</vs-button>
          </div>
        </div>
      </div>
    </vs-popup>

    <!-- Popup Phê duyệt của HR (Theo ảnh 2) -->
    <vs-popup title="HR Phê duyệt Đơn xin phép" :active.sync="approvePopupActive">
      <div class="p-4" v-if="selectedLeave">
        <p class="mb-4">
          Phê duyệt đơn của nhân sự: <strong>{{ selectedLeave.user ? selectedLeave.user.name : '' }}</strong> 
          ({{ selectedLeave.duration_days }} ngày: {{ selectedLeave.start_date }} đến {{ selectedLeave.end_date }})
        </p>

        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <label class="text-base font-medium mb-2 block">Lựa chọn phân loại phê duyệt của HR (*)</label>
            <div class="space-y-2">
              <label class="flex items-center p-3 border rounded cursor-pointer hover:bg-gray-50" :class="{'border-success bg-green-50': approveForm.approval_type === 'approved'}">
                <input type="radio" value="approved" v-model="approveForm.approval_type" class="mr-3" />
                <div>
                  <span class="font-bold text-success block">Đã duyệt</span>
                  <span class="text-xs text-gray-500">Hưởng nguyên lương / Tự động trừ vào số ngày phép năm</span>
                </div>
              </label>

              <label class="flex items-center p-3 border rounded cursor-pointer hover:bg-gray-50" :class="{'border-warning bg-orange-50': approveForm.approval_type === 'half_salary'}">
                <input type="radio" value="half_salary" v-model="approveForm.approval_type" class="mr-3" />
                <div>
                  <span class="font-bold text-warning block">50% lương</span>
                  <span class="text-xs text-gray-500">Áp dụng cho nhân sự xin chuyển làm Off sang Onl</span>
                </div>
              </label>

              <label class="flex items-center p-3 border rounded cursor-pointer hover:bg-gray-50" :class="{'border-gray-500 bg-gray-50': approveForm.approval_type === 'late_unpaid'}">
                <input type="radio" value="late_unpaid" v-model="approveForm.approval_type" class="mr-3" />
                <div>
                  <span class="font-bold text-gray-700 block">Gửi đơn muộn (không lương)</span>
                  <span class="text-xs text-gray-500">Nhân viên gửi đơn muộn sau hạn quy định, không tính lương</span>
                </div>
              </label>

              <label class="flex items-center p-3 border rounded cursor-pointer hover:bg-gray-50" :class="{'border-danger bg-red-50': approveForm.approval_type === 'rejected'}">
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
            <vs-textarea label="Lý do từ chối (*)" v-model="approveForm.rejection_reason" />
          </div>
        </div>

        <div class="vx-row">
          <div class="vx-col w-full flex justify-end">
            <vs-button @click="submitApproval()" color="success" class="mr-2"><i class="fa fa-check"></i> Xác nhận duyệt</vs-button>
            <vs-button @click="approvePopupActive = false" color="dark" type="border"><i class="fas fa-undo-alt"></i> Đóng</vs-button>
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
      leaves: { data: [] },
      leaveTypes: [],
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
      axios.p('/api/hrm/leaves/list', { limit: 20 }).then(res => {
        this.leaves = res.data;
        this.$vs.loading.close();
      }).catch(err => {
        this.$vs.loading.close();
      });
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
