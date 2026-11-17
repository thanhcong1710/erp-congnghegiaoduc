<template>
  <div id="page-hrm-leave-types">
    <!-- Header -->
    <div class="hrm-page-header">
      <div>
        <h3><feather-icon icon="CalendarIcon" class="mr-2 text-primary" /> Loại Nghỉ Phép</h3>
        <p>Cấu hình các loại nghỉ, số ngày tối đa mỗi năm và chế độ hưởng lương</p>
      </div>
      <div class="hrm-page-actions">
        <vs-button color="dark" type="border" @click="fetchData"><i class="fa-solid fa-rotate-right mr-1"></i> Tải lại</vs-button>
        <vs-button color="primary" @click="openPopup()"><i class="fa-solid fa-plus mr-1"></i> Thêm loại phép</vs-button>
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
            <input v-model="keyword" type="text" class="vs-inputx vs-input--input normal hrm-input" placeholder="Tìm theo tên loại phép...">
          </div>
        </div>
      </div>

      <vs-table :data="filteredTypes" pagination :max-items="20" noDataText="">
        <template slot="thead">
          <vs-th style="width: 60px;">STT</vs-th>
          <vs-th>Tên loại phép</vs-th>
          <vs-th class="text-center">Số ngày tối đa/năm</vs-th>
          <vs-th>Hưởng lương</vs-th>
          <vs-th>Trạng thái</vs-th>
          <vs-th style="width: 90px;">Thao tác</vs-th>
        </template>
        <template slot-scope="{data}">
          <vs-tr :key="tr.id || indextr" v-for="(tr, indextr) in data">
            <vs-td class="text-gray-500">{{ indextr + 1 }}</vs-td>
            <vs-td><span class="font-semibold text-gray-800">{{ tr.name }}</span></vs-td>
            <vs-td class="text-center">
              <span class="font-bold text-primary">{{ tr.days_per_year }}</span> <span class="text-xs text-gray-500">ngày</span>
            </vs-td>
            <vs-td>
              <span class="hrm-badge" :class="tr.is_paid == 1 ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'">
                <i :class="tr.is_paid == 1 ? 'fa-solid fa-sack-dollar' : 'fa-solid fa-ban'"></i>
                {{ tr.is_paid == 1 ? 'Có lương' : 'Không lương' }}
              </span>
            </vs-td>
            <vs-td>
              <span class="hrm-badge" :class="tr.status == 1 ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700'">
                {{ tr.status == 1 ? 'Đang áp dụng' : 'Tạm dừng' }}
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

      <div v-if="!filteredTypes.length" class="hrm-empty">
        <i class="fa-regular fa-folder-open"></i>
        <div class="hrm-empty-title">Không có loại phép nào</div>
        <span class="text-sm">Thử đổi bộ lọc hoặc thêm loại phép mới</span>
      </div>
    </vx-card>

    <!-- Popup thêm / sửa -->
    <vs-popup class="hrm-popup" :title="isEdit ? 'Cập nhật loại phép' : 'Thêm loại phép'" :active.sync="popupActive">
      <div class="vx-row mb-4">
        <div class="vx-col w-full">
          <label class="hrm-form-label">Tên loại phép <span class="text-danger">*</span></label>
          <input v-model.trim="form.name" type="text" class="vs-inputx vs-input--input normal hrm-input" placeholder="VD: Nghỉ phép năm, Nghỉ ốm...">
        </div>
      </div>
      <div class="vx-row mb-4">
        <div class="vx-col w-full">
          <label class="hrm-form-label">Số ngày tối đa trong năm <span class="text-danger">*</span></label>
          <input v-model.number="form.days_per_year" type="number" min="0" step="0.5" class="vs-inputx vs-input--input normal hrm-input">
        </div>
      </div>
      <div class="vx-row">
        <div class="vx-col w-1/2">
          <vs-checkbox v-model="form.is_paid">Nghỉ hưởng lương</vs-checkbox>
        </div>
        <div class="vx-col w-1/2">
          <vs-checkbox v-model="form.status">Đang áp dụng</vs-checkbox>
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

const emptyForm = () => ({ id: null, name: '', days_per_year: 12, is_paid: true, status: true });

export default {
  data() {
    return {
      types: [],
      keyword: '',
      filterStatus: 'all',
      statusTabs: [
        { value: 'all', label: 'Tất cả', color: 'primary' },
        { value: 1, label: 'Đang áp dụng', color: 'success' },
        { value: 0, label: 'Tạm dừng', color: 'danger' }
      ],
      popupActive: false,
      isEdit: false,
      saving: false,
      form: emptyForm()
    };
  },
  computed: {
    filteredTypes() {
      const kw = this.keyword.trim().toLowerCase();
      return this.types.filter(t => {
        const matchStatus = this.filterStatus === 'all' || Number(t.status) === this.filterStatus;
        const matchKw = !kw || (t.name || '').toLowerCase().includes(kw);
        return matchStatus && matchKw;
      });
    }
  },
  created() {
    this.fetchData();
  },
  methods: {
    countByStatus(status) {
      if (status === 'all') return this.types.length;
      return this.types.filter(t => Number(t.status) === status).length;
    },
    fetchData() {
      this.$vs.loading();
      axios.p('/api/hrm/leave-types/list')
        .then(res => { this.types = res.data || []; })
        .catch(() => this.notify('Lỗi', 'Không thể tải danh sách loại phép', 'danger'))
        .finally(() => this.$vs.loading.close());
    },
    openPopup(item = null) {
      this.isEdit = !!item;
      this.form = item
        ? { ...item, is_paid: item.is_paid == 1, status: item.status == 1 }
        : emptyForm();
      this.popupActive = true;
    },
    saveData() {
      // Kiểm tra dữ liệu bắt buộc trước khi gửi
      if (!this.form.name) return this.notify('Thiếu thông tin', 'Vui lòng nhập tên loại phép', 'warning');
      if (this.form.days_per_year === '' || this.form.days_per_year < 0) {
        return this.notify('Thiếu thông tin', 'Số ngày tối đa không hợp lệ', 'warning');
      }
      const payload = { ...this.form, is_paid: this.form.is_paid ? 1 : 0, status: this.form.status ? 1 : 0 };
      const url = this.isEdit ? `/api/hrm/leave-types/update/${this.form.id}` : '/api/hrm/leave-types/add';
      this.saving = true;
      axios.p(url, payload)
        .then(res => {
          if (res.data.status === 1) {
            this.notify('Thành công', res.data.message, 'success');
            this.popupActive = false;
            this.fetchData();
          } else {
            this.notify('Lỗi', res.data.message, 'danger');
          }
        })
        .catch(() => this.notify('Lỗi', 'Không thể lưu loại phép', 'danger'))
        .finally(() => { this.saving = false; });
    },
    notify(title, text, color) {
      this.$vs.notify({ title, text, color });
    }
  }
};
</script>
