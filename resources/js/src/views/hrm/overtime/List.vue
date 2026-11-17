<template>
  <div id="page-hrm-overtime">
    <!-- Header -->
    <div class="hrm-page-header">
      <div>
        <h3><feather-icon icon="WatchIcon" class="mr-2 text-primary" /> Làm Thêm Giờ (OT)</h3>
        <p>Tạo đơn, theo dõi và phê duyệt đơn làm thêm giờ của nhân sự</p>
      </div>
      <div class="hrm-page-actions">
        <vs-button color="dark" type="border" @click="fetchData"><i class="fa-solid fa-rotate-right mr-1"></i> Tải lại</vs-button>
        <vs-button color="primary" @click="openPopup"><i class="fa-solid fa-plus mr-1"></i> Tạo đơn OT</vs-button>
      </div>
    </div>

    <!-- Thống kê nhanh: click để lọc -->
    <div class="hrm-stat-grid">
      <div
        v-for="card in statCards"
        :key="card.value"
        class="hrm-stat-card is-clickable"
        :class="{ 'is-active': filterStatus === card.value }"
        @click="filterStatus = card.value"
      >
        <div>
          <div class="hrm-stat-label">{{ card.label }}</div>
          <div class="hrm-stat-value" :class="card.text">{{ countByStatus(card.value) }}</div>
        </div>
        <div class="hrm-stat-icon" :class="card.bg"><i :class="card.icon"></i></div>
      </div>
    </div>

    <vx-card no-shadow>
      <div class="hrm-toolbar">
        <div class="hrm-toolbar-group text-sm text-gray-600">
          Đang hiển thị <strong class="mx-1">{{ filteredRequests.length }}</strong> đơn
        </div>
        <div class="hrm-toolbar-group">
          <div class="hrm-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input v-model="keyword" type="text" class="vs-inputx vs-input--input normal hrm-input" placeholder="Tìm theo tên nhân viên, lý do...">
          </div>
        </div>
      </div>

      <vs-table :data="filteredRequests" pagination :max-items="20" noDataText="">
        <template slot="thead">
          <vs-th>Nhân viên</vs-th>
          <vs-th>Ngày OT</vs-th>
          <vs-th>Thời gian</vs-th>
          <vs-th>Lý do</vs-th>
          <vs-th>Trạng thái</vs-th>
          <vs-th>Người duyệt</vs-th>
          <vs-th style="width: 110px;">Thao tác</vs-th>
        </template>
        <template slot-scope="{data}">
          <vs-tr :key="tr.id || indextr" v-for="(tr, indextr) in data">
            <vs-td>
              <div class="font-semibold text-gray-800">{{ tr.user ? tr.user.name : ('#' + tr.user_id) }}</div>
              <div v-if="tr.user && tr.user.hrm_id" class="text-xs text-gray-500">{{ tr.user.hrm_id }}</div>
            </vs-td>
            <vs-td class="whitespace-nowrap">{{ tr.date | formatDateView }}</vs-td>
            <vs-td>
              <div class="whitespace-nowrap">{{ shortTime(tr.start_time) }} – {{ shortTime(tr.end_time) }}</div>
              <div class="text-xs text-primary font-semibold">{{ otHours(tr) }}</div>
            </vs-td>
            <vs-td>
              <span v-if="tr.reason" class="text-sm text-gray-700 line-clamp-2" :title="tr.reason">{{ tr.reason }}</span>
              <span v-else class="text-xs italic text-gray-400">Không có</span>
            </vs-td>
            <vs-td>
              <span class="hrm-badge" :class="statusMeta(tr.status).badge">{{ statusMeta(tr.status).label }}</span>
            </vs-td>
            <vs-td>
              <span v-if="tr.approver" class="text-sm">{{ tr.approver.name }}</span>
              <span v-else class="text-xs italic text-gray-400">Chưa có</span>
            </vs-td>
            <vs-td class="list-action">
              <template v-if="Number(tr.status) === 0">
                <vx-tooltip text="Phê duyệt" class="inline-block">
                  <vs-button size="small" color="success" @click="confirmStatus(tr, 1)"><i class="fa-solid fa-check"></i></vs-button>
                </vx-tooltip>
                <vx-tooltip text="Từ chối" class="inline-block ml-1">
                  <vs-button size="small" color="danger" type="border" @click="confirmStatus(tr, 2)"><i class="fa-solid fa-xmark"></i></vs-button>
                </vx-tooltip>
              </template>
              <span v-else class="text-xs text-gray-400">Đã xử lý</span>
            </vs-td>
          </vs-tr>
        </template>
      </vs-table>

      <div v-if="!filteredRequests.length" class="hrm-empty">
        <i class="fa-regular fa-clock"></i>
        <div class="hrm-empty-title">Không có đơn OT nào</div>
        <span class="text-sm">Thử đổi bộ lọc hoặc tạo đơn mới</span>
      </div>
    </vx-card>

    <!-- Popup tạo đơn OT -->
    <vs-popup class="hrm-popup" title="Tạo đơn làm thêm giờ (OT)" :active.sync="popupActive">
      <div class="vx-row mb-4">
        <div class="vx-col w-full">
          <label class="hrm-form-label">Nhân viên <span class="text-danger">*</span></label>
          <v-select
            v-model="form.user_id"
            :options="employeeOptions"
            :reduce="opt => opt.value"
            label="label"
            placeholder="Gõ tên hoặc mã nhân sự để tìm..."
          >
            <span slot="no-options">Không tìm thấy nhân sự</span>
          </v-select>
        </div>
      </div>
      <div class="vx-row mb-4">
        <div class="vx-col w-full">
          <label class="hrm-form-label">Ngày OT <span class="text-danger">*</span></label>
          <flat-pickr v-model="form.date" :config="flatpickrDateConfig" class="vs-inputx vs-input--input normal hrm-input" placeholder="dd/mm/yyyy" />
        </div>
      </div>
      <div class="vx-row mb-4">
        <div class="vx-col w-1/2">
          <label class="hrm-form-label">Từ giờ <span class="text-danger">*</span></label>
          <input v-model="form.start_time" type="time" class="vs-inputx vs-input--input normal hrm-input">
        </div>
        <div class="vx-col w-1/2">
          <label class="hrm-form-label">Đến giờ <span class="text-danger">*</span></label>
          <input v-model="form.end_time" type="time" class="vs-inputx vs-input--input normal hrm-input">
        </div>
      </div>
      <div v-if="form.start_time && form.end_time" class="text-sm text-gray-600 mb-4">
        Tổng thời gian OT: <strong class="text-primary">{{ otHours(form) }}</strong>
      </div>
      <div class="vx-row">
        <div class="vx-col w-full">
          <label class="hrm-form-label">Lý do làm thêm</label>
          <textarea v-model.trim="form.reason" rows="3" class="vs-inputx vs-input--input normal hrm-input" placeholder="Mô tả công việc cần làm thêm..."></textarea>
        </div>
      </div>
      <div class="hrm-popup-footer">
        <vs-button color="dark" type="border" @click="popupActive = false"><i class="fa-solid fa-xmark mr-1"></i> Hủy</vs-button>
        <vs-button color="primary" :disabled="saving" @click="saveData"><i class="fa-solid fa-paper-plane mr-1"></i> Gửi đơn</vs-button>
      </div>
    </vs-popup>
  </div>
</template>

<script>
import axios from '../../../http/axios.js';
import moment from 'moment';
import vSelect from 'vue-select';

const STATUS_META = {
  0: { label: 'Chờ duyệt', badge: 'bg-amber-100 text-amber-800' },
  1: { label: 'Đã duyệt', badge: 'bg-green-100 text-green-700' },
  2: { label: 'Từ chối', badge: 'bg-red-100 text-red-700' }
};

export default {
  components: { 'v-select': vSelect },
  data() {
    return {
      flatpickrDateConfig: { dateFormat: 'Y-m-d', altInput: true, altFormat: 'd/m/Y', allowInput: true },
      requests: [],
      employees: [],
      keyword: '',
      filterStatus: 'all',
      statCards: [
        { value: 'all', label: 'Tất cả đơn', icon: 'fa-solid fa-list', bg: 'bg-primary-light text-primary', text: 'text-primary' },
        { value: 0, label: 'Chờ duyệt', icon: 'fa-solid fa-hourglass-half', bg: 'bg-warning-light text-warning', text: 'text-warning' },
        { value: 1, label: 'Đã duyệt', icon: 'fa-solid fa-circle-check', bg: 'bg-success-light text-success', text: 'text-success' },
        { value: 2, label: 'Từ chối', icon: 'fa-solid fa-circle-xmark', bg: 'bg-danger-light text-danger', text: 'text-danger' }
      ],
      popupActive: false,
      saving: false,
      form: this.emptyForm()
    };
  },
  computed: {
    filteredRequests() {
      const kw = this.normalize(this.keyword.trim());
      return this.requests.filter(r => {
        const matchStatus = this.filterStatus === 'all' || Number(r.status) === this.filterStatus;
        const text = this.normalize(`${r.user ? r.user.name : ''} ${r.reason || ''}`);
        return matchStatus && (!kw || text.includes(kw));
      });
    },
    // Chỉ nhân sự đã liên kết tài khoản (có user_id) mới tạo được đơn OT
    employeeOptions() {
      return this.employees
        .filter(e => e.user_id)
        .map(e => ({
          value: e.user_id,
          label: `${e.full_name || (e.user ? e.user.name : '')}${e.employee_code ? ' – ' + e.employee_code : ''}`
        }));
    }
  },
  created() {
    this.fetchData();
    this.fetchEmployees();
  },
  methods: {
    // Chữ thường + bỏ dấu tiếng Việt để tìm kiếm không dấu
    normalize(str) {
      return String(str || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/đ/g, 'd');
    },
    emptyForm() {
      return { user_id: null, date: moment().format('YYYY-MM-DD'), start_time: '18:00', end_time: '20:00', reason: '' };
    },
    statusMeta(status) {
      return STATUS_META[Number(status)] || STATUS_META[0];
    },
    countByStatus(status) {
      if (status === 'all') return this.requests.length;
      return this.requests.filter(r => Number(r.status) === status).length;
    },
    shortTime(time) {
      return time ? String(time).substring(0, 5) : '--:--';
    },
    // Số giờ OT (hỗ trợ qua đêm)
    otHours(item) {
      if (!item.start_time || !item.end_time) return '';
      const toMin = t => { const [h, m] = String(t).split(':'); return Number(h) * 60 + Number(m); };
      let diff = toMin(item.end_time) - toMin(item.start_time);
      if (diff < 0) diff += 24 * 60;
      return `${Math.round(diff / 6) / 10} giờ`;
    },
    fetchData() {
      this.$vs.loading();
      axios.p('/api/hrm/overtime/list')
        .then(res => { this.requests = res.data || []; })
        .catch(() => this.notify('Lỗi', 'Không thể tải danh sách đơn OT', 'danger'))
        .finally(() => this.$vs.loading.close());
    },
    fetchEmployees() {
      axios.p('/api/hrm/employees/list', { work_status: 'working' })
        .then(res => {
          // API trả về { status, data: [...] } (bản cũ trả mảng trực tiếp)
          const body = res.data || {};
          this.employees = Array.isArray(body) ? body : (Array.isArray(body.data) ? body.data : []);
        })
        .catch(() => { this.employees = []; });
    },
    openPopup() {
      this.form = this.emptyForm();
      this.popupActive = true;
    },
    saveData() {
      if (!this.form.user_id || !this.form.date || !this.form.start_time || !this.form.end_time) {
        return this.notify('Thiếu thông tin', 'Vui lòng chọn nhân viên, ngày và khung giờ OT', 'warning');
      }
      this.saving = true;
      axios.p('/api/hrm/overtime/add', this.form)
        .then(res => {
          if (res.data.status === 1) {
            this.notify('Thành công', res.data.message, 'success');
            this.popupActive = false;
            this.fetchData();
          } else {
            this.notify('Lỗi', res.data.message, 'danger');
          }
        })
        .catch(() => this.notify('Lỗi', 'Không thể gửi đơn OT', 'danger'))
        .finally(() => { this.saving = false; });
    },
    confirmStatus(item, status) {
      const isApprove = status === 1;
      this.$vs.dialog({
        type: 'confirm',
        color: isApprove ? 'success' : 'danger',
        title: isApprove ? 'Phê duyệt đơn OT' : 'Từ chối đơn OT',
        text: `${isApprove ? 'Phê duyệt' : 'Từ chối'} đơn OT ngày ${moment(item.date).format('DD/MM/YYYY')} của ${item.user ? item.user.name : 'nhân viên'}?`,
        acceptText: 'Xác nhận',
        cancelText: 'Hủy',
        accept: () => this.updateStatus(item.id, status)
      });
    },
    updateStatus(id, status) {
      this.$vs.loading();
      axios.p(`/api/hrm/overtime/update-status/${id}`, { status })
        .then(res => {
          this.notify('Thành công', res.data.message, 'success');
          this.fetchData();
        })
        .catch(() => {
          this.$vs.loading.close();
          this.notify('Lỗi', 'Không thể cập nhật trạng thái', 'danger');
        });
    },
    notify(title, text, color) {
      this.$vs.notify({ title, text, color });
    }
  }
};
</script>
