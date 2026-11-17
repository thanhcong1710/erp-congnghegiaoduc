<template>
  <div id="page-hrm-shifts">
    <!-- Header -->
    <div class="hrm-page-header">
      <div>
        <h3><feather-icon icon="ClockIcon" class="mr-2 text-primary" /> Ca Làm Việc</h3>
        <p>Khai báo khung giờ làm, thời gian cho phép đi muộn / về sớm của từng ca</p>
      </div>
      <div class="hrm-page-actions">
        <vs-button color="dark" type="border" @click="fetchData"><i class="fa-solid fa-rotate-right mr-1"></i> Tải lại</vs-button>
        <vs-button color="primary" @click="openPopup()"><i class="fa-solid fa-plus mr-1"></i> Thêm ca làm</vs-button>
      </div>
    </div>

    <vx-card no-shadow>
      <!-- Toolbar -->
      <div class="hrm-toolbar">
        <div class="hrm-toolbar-group">
          <vs-button
            v-for="tab in statusTabs"
            :key="tab.value"
            size="small"
            :color="tab.color"
            :type="filterStatus === tab.value ? 'filled' : 'border'"
            @click="filterStatus = tab.value"
          >
            {{ tab.label }} ({{ countByStatus(tab.value) }})
          </vs-button>
        </div>
        <div class="hrm-toolbar-group">
          <div class="hrm-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input v-model="keyword" type="text" class="vs-inputx vs-input--input normal hrm-input" placeholder="Tìm theo tên ca...">
          </div>
        </div>
      </div>

      <vs-table :data="filteredShifts" pagination :max-items="20" noDataText="">
        <template slot="thead">
          <vs-th style="width: 60px;">STT</vs-th>
          <vs-th>Tên ca</vs-th>
          <vs-th>Khung giờ</vs-th>
          <vs-th class="text-center">Số giờ</vs-th>
          <vs-th class="text-center">Cho phép muộn</vs-th>
          <vs-th class="text-center">Cho phép về sớm</vs-th>
          <vs-th>Trạng thái</vs-th>
          <vs-th style="width: 90px;">Thao tác</vs-th>
        </template>
        <template slot-scope="{data}">
          <vs-tr :key="tr.id || indextr" v-for="(tr, indextr) in data">
            <vs-td class="text-gray-500">{{ indextr + 1 }}</vs-td>
            <vs-td><span class="font-semibold text-gray-800">{{ tr.name }}</span></vs-td>
            <vs-td>
              <span class="hrm-badge bg-blue-50 text-blue-700">
                <i class="fa-regular fa-clock"></i> {{ shortTime(tr.start_time) }} – {{ shortTime(tr.end_time) }}
              </span>
            </vs-td>
            <vs-td class="text-center font-semibold">{{ shiftHours(tr) }}</vs-td>
            <vs-td class="text-center">{{ tr.late_grace_period || 0 }} phút</vs-td>
            <vs-td class="text-center">{{ tr.early_leave_grace_period || 0 }} phút</vs-td>
            <vs-td>
              <span class="hrm-badge" :class="tr.status == 1 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                {{ tr.status == 1 ? 'Hoạt động' : 'Tạm dừng' }}
              </span>
            </vs-td>
            <vs-td class="list-action">
              <vx-tooltip text="Chỉnh sửa">
                <vs-button size="small" color="success" type="border" @click="openPopup(tr)"><i class="fa-solid fa-pen-to-square"></i></vs-button>
              </vx-tooltip>
            </vs-td>
          </vs-tr>
        </template>
      </vs-table>

      <div v-if="!filteredShifts.length" class="hrm-empty">
        <i class="fa-regular fa-clock"></i>
        <div class="hrm-empty-title">Không có ca làm việc nào</div>
        <span class="text-sm">Thử đổi bộ lọc hoặc thêm ca mới</span>
      </div>
    </vx-card>

    <!-- Popup thêm / sửa -->
    <vs-popup class="hrm-popup" :title="isEdit ? 'Cập nhật ca làm việc' : 'Thêm ca làm việc'" :active.sync="popupActive">
      <div class="vx-row mb-4">
        <div class="vx-col w-full">
          <label class="hrm-form-label">Tên ca <span class="text-danger">*</span></label>
          <input v-model.trim="form.name" type="text" class="vs-inputx vs-input--input normal hrm-input" placeholder="VD: Ca hành chính, Ca tối...">
        </div>
      </div>
      <div class="vx-row mb-4">
        <div class="vx-col w-1/2">
          <label class="hrm-form-label">Giờ bắt đầu <span class="text-danger">*</span></label>
          <input v-model="form.start_time" type="time" class="vs-inputx vs-input--input normal hrm-input">
        </div>
        <div class="vx-col w-1/2">
          <label class="hrm-form-label">Giờ kết thúc <span class="text-danger">*</span></label>
          <input v-model="form.end_time" type="time" class="vs-inputx vs-input--input normal hrm-input">
        </div>
      </div>
      <div class="vx-row mb-4">
        <div class="vx-col w-1/2">
          <label class="hrm-form-label">Cho phép đi muộn (phút)</label>
          <input v-model.number="form.late_grace_period" type="number" min="0" class="vs-inputx vs-input--input normal hrm-input">
        </div>
        <div class="vx-col w-1/2">
          <label class="hrm-form-label">Cho phép về sớm (phút)</label>
          <input v-model.number="form.early_leave_grace_period" type="number" min="0" class="vs-inputx vs-input--input normal hrm-input">
        </div>
      </div>
      <div class="vx-row">
        <div class="vx-col w-full">
          <label class="hrm-form-label">Trạng thái</label>
          <select v-model.number="form.status" class="vs-inputx vs-input--input normal hrm-input">
            <option :value="1">Hoạt động</option>
            <option :value="0">Tạm dừng</option>
          </select>
        </div>
      </div>
      <div class="hrm-popup-footer">
        <vs-button color="dark" type="border" @click="popupActive = false"><i class="fa-solid fa-xmark mr-1"></i> Hủy</vs-button>
        <vs-button color="primary" :disabled="saving" @click="saveData"><i class="fa-solid fa-floppy-disk mr-1"></i> {{ isEdit ? 'Lưu thay đổi' : 'Tạo mới' }}</vs-button>
      </div>
    </vs-popup>
  </div>
</template>

<script>
import axios from '../../../http/axios.js';

const emptyForm = () => ({
  id: null, name: '', start_time: '08:00', end_time: '17:30',
  late_grace_period: 0, early_leave_grace_period: 0, status: 1
});

export default {
  data() {
    return {
      shifts: [],
      keyword: '',
      filterStatus: 'all',
      statusTabs: [
        { value: 'all', label: 'Tất cả', color: 'primary' },
        { value: 1, label: 'Hoạt động', color: 'success' },
        { value: 0, label: 'Tạm dừng', color: 'danger' }
      ],
      popupActive: false,
      isEdit: false,
      saving: false,
      form: emptyForm()
    };
  },
  computed: {
    filteredShifts() {
      const kw = this.keyword.trim().toLowerCase();
      return this.shifts.filter(s => {
        const matchStatus = this.filterStatus === 'all' || Number(s.status) === this.filterStatus;
        return matchStatus && (!kw || (s.name || '').toLowerCase().includes(kw));
      });
    }
  },
  created() {
    this.fetchData();
  },
  methods: {
    countByStatus(status) {
      if (status === 'all') return this.shifts.length;
      return this.shifts.filter(s => Number(s.status) === status).length;
    },
    shortTime(time) {
      return time ? String(time).substring(0, 5) : '--:--';
    },
    // Tính số giờ của ca (hỗ trợ ca qua đêm)
    shiftHours(shift) {
      if (!shift.start_time || !shift.end_time) return '-';
      const toMin = t => { const [h, m] = String(t).split(':'); return Number(h) * 60 + Number(m); };
      let diff = toMin(shift.end_time) - toMin(shift.start_time);
      if (diff < 0) diff += 24 * 60;
      return `${Math.round(diff / 6) / 10}h`;
    },
    fetchData() {
      this.$vs.loading();
      axios.p('/api/hrm/shifts/list')
        .then(res => { this.shifts = res.data || []; })
        .catch(() => this.notify('Lỗi', 'Không thể tải danh sách ca làm', 'danger'))
        .finally(() => this.$vs.loading.close());
    },
    openPopup(item = null) {
      this.isEdit = !!item;
      this.form = item
        ? { ...item, start_time: this.shortTime(item.start_time), end_time: this.shortTime(item.end_time), status: Number(item.status) }
        : emptyForm();
      this.popupActive = true;
    },
    saveData() {
      if (!this.form.name) return this.notify('Thiếu thông tin', 'Vui lòng nhập tên ca', 'warning');
      if (!this.form.start_time || !this.form.end_time) {
        return this.notify('Thiếu thông tin', 'Vui lòng nhập giờ bắt đầu và kết thúc', 'warning');
      }
      const url = this.isEdit ? `/api/hrm/shifts/update/${this.form.id}` : '/api/hrm/shifts/add';
      this.saving = true;
      axios.p(url, this.form)
        .then(res => {
          if (res.data.status === 1) {
            this.notify('Thành công', res.data.message, 'success');
            this.popupActive = false;
            this.fetchData();
          } else {
            this.notify('Lỗi', res.data.message, 'danger');
          }
        })
        .catch(() => this.notify('Lỗi', 'Không thể lưu ca làm việc', 'danger'))
        .finally(() => { this.saving = false; });
    },
    notify(title, text, color) {
      this.$vs.notify({ title, text, color });
    }
  }
};
</script>
