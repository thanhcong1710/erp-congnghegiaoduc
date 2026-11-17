<template>
  <div id="page-hrm-documents">
    <!-- Header -->
    <div class="hrm-page-header">
      <div>
        <h3><feather-icon icon="FolderIcon" class="mr-2 text-primary" /> Hồ Sơ Nhân Sự</h3>
        <p>Theo dõi ngày nhận hồ sơ, tình trạng đầy đủ và các giấy tờ còn thiếu</p>
      </div>
      <div class="hrm-page-actions">
        <vs-button color="dark" type="border" @click="fetchDocuments"><i class="fa-solid fa-rotate-right mr-1"></i> Tải lại</vs-button>
      </div>
    </div>

    <!-- Thống kê nhanh: click để lọc -->
    <div class="hrm-stat-grid">
      <div
        v-for="card in statCards"
        :key="card.value"
        class="hrm-stat-card is-clickable"
        :class="{ 'is-active': filterStatus === card.value }"
        @click="setFilter(card.value)"
      >
        <div>
          <div class="hrm-stat-label">{{ card.label }}</div>
          <div class="hrm-stat-value" :class="card.text">{{ stats[card.value] }}</div>
        </div>
        <div class="hrm-stat-icon" :class="card.bg"><i :class="card.icon"></i></div>
      </div>
    </div>

    <vx-card no-shadow>
      <div class="hrm-toolbar">
        <div class="hrm-toolbar-group">
          <span class="text-sm text-gray-600">Đang hiển thị <strong>{{ filteredDocuments.length }}</strong> hồ sơ</span>
        </div>
        <div class="hrm-toolbar-group">
          <div class="hrm-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input v-model="searchKeyword" type="text" class="vs-inputx vs-input--input normal hrm-input" placeholder="Mã nhân sự, họ tên, team...">
          </div>
        </div>
      </div>

      <!-- Bảng hồ sơ -->
      <div class="vs-component vs-con-table stripe vs-table-primary">
        <div class="con-tablex vs-table--content">
          <table class="vs-table vs-table--tbody-table">
            <thead class="vs-table--thead">
              <tr>
                <th class="text-center" style="width: 56px;">STT</th>
                <th>Nhân sự</th>
                <th>Team / Bộ phận</th>
                <th>Ngày nhận hồ sơ</th>
                <th style="min-width: 160px;">Mức độ đầy đủ</th>
                <th>Ghi chú</th>
                <th class="text-center" style="width: 80px;">Thao tác</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(tr, index) in paginatedDocuments" :key="tr.id || index" class="tr-values vs-table--tr">
                <td class="td vs-table--td text-center text-gray-500">{{ (currentPage - 1) * perPage + index + 1 }}</td>
                <td class="td vs-table--td">
                  <div class="font-semibold text-gray-900">{{ tr.user_name }}</div>
                  <div class="text-xs text-primary font-semibold">{{ tr.employee_code || '---' }}</div>
                </td>
                <td class="td vs-table--td">{{ tr.team_name || '---' }}</td>
                <td class="td vs-table--td whitespace-nowrap">
                  <span v-if="tr.profile_received_date">{{ fmtDate(tr.profile_received_date) }}</span>
                  <span v-else class="text-xs italic text-gray-400">Chưa nhận</span>
                </td>
                <td class="td vs-table--td">
                  <div class="flex items-center">
                    <div class="doc-progress mr-2">
                      <div class="doc-progress-bar" :class="tr.profile_status === 'full' ? 'bg-success' : 'bg-warning'" :style="{ width: progressPercent(tr) + '%' }"></div>
                    </div>
                    <span class="text-xs font-semibold whitespace-nowrap">{{ collectedCount(tr) }}/{{ allRequiredDocs.length }}</span>
                  </div>
                </td>
                <td class="td vs-table--td">
                  <div v-if="tr.profile_status === 'missing'" class="text-xs text-danger font-semibold">
                    {{ tr.profile_note || formatMissingList(tr.missing_documents) }}
                  </div>
                  <div v-else class="text-xs text-success font-semibold">
                    <i class="fa-solid fa-circle-check mr-1"></i> Đầy đủ giấy tờ
                  </div>
                </td>
                <td class="td vs-table--td text-center list-action">
                  <vx-tooltip text="Cập nhật hồ sơ">
                    <vs-button size="small" color="primary" type="border" @click="openEditModal(tr)"><i class="fa-solid fa-pen-to-square"></i></vs-button>
                  </vx-tooltip>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div v-if="!filteredDocuments.length" class="hrm-empty">
        <i class="fa-regular fa-folder-open"></i>
        <div class="hrm-empty-title">Không có dữ liệu hồ sơ</div>
        <span class="text-sm">Thử đổi bộ lọc hoặc từ khóa tìm kiếm</span>
      </div>

      <!-- Phân trang -->
      <div v-if="filteredDocuments.length" class="hrm-pagination">
        <div class="text-sm text-gray-600">
          Hiển thị <strong>{{ (currentPage - 1) * perPage + 1 }}</strong> – <strong>{{ Math.min(currentPage * perPage, filteredDocuments.length) }}</strong>
          / <strong>{{ filteredDocuments.length }}</strong> hồ sơ
        </div>
        <div class="flex flex-wrap items-center">
          <span class="text-sm text-gray-600 mr-2">Mỗi trang:</span>
          <select v-model.number="perPage" class="vs-inputx vs-input--input normal hrm-input mr-3" style="width: auto;" @change="currentPage = 1">
            <option v-for="n in [10, 20, 50, 100]" :key="n" :value="n">{{ n }}</option>
          </select>
          <vs-pagination v-if="totalPages > 1" :total="totalPages" v-model="currentPage" />
        </div>
      </div>
    </vx-card>

    <!-- Popup cập nhật checklist hồ sơ -->
    <vs-popup class="hrm-popup" title="Cập nhật tình trạng hồ sơ" :active.sync="modalActive">
      <div v-if="selectedItem">
        <div class="hrm-info-box">
          <div class="font-bold text-gray-800">{{ selectedItem.user_name }} <span class="text-sm font-normal text-gray-500">({{ selectedItem.employee_code || '---' }})</span></div>
          <div class="text-sm text-gray-600 mt-1">Team / Bộ phận: {{ selectedItem.team_name || '---' }}</div>
        </div>

        <div class="mb-4">
          <label class="hrm-form-label">Ngày nhận hồ sơ</label>
          <flat-pickr v-model="editForm.profile_received_date" :config="flatpickrDateConfig" class="vs-inputx vs-input--input normal hrm-input" placeholder="dd/mm/yyyy" />
        </div>

        <div class="mb-4">
          <div class="flex items-center justify-between mb-2">
            <label class="hrm-form-label mb-0">Giấy tờ đã thu ({{ collectedDocuments.length }}/{{ allRequiredDocs.length }})</label>
            <a href="javascript:void(0)" class="text-xs text-primary font-semibold" @click="toggleAll">
              {{ collectedDocuments.length === allRequiredDocs.length ? 'Bỏ chọn tất cả' : 'Chọn tất cả' }}
            </a>
          </div>
          <div class="border border-gray-200 rounded p-2">
            <label
              v-for="(doc, idx) in allRequiredDocs"
              :key="doc.key"
              class="flex items-center p-2 rounded cursor-pointer hover:bg-gray-50"
            >
              <input v-model="collectedDocuments" type="checkbox" :value="doc.key" class="mr-3">
              <span class="text-sm font-medium text-gray-800">{{ idx + 1 }}. {{ doc.label }}</span>
            </label>
          </div>
        </div>

        <div>
          <label class="hrm-form-label">Ghi chú (tự động tổng hợp)</label>
          <div class="text-sm font-semibold" :class="pendingMissing.length ? 'text-danger' : 'text-success'">{{ autoNote }}</div>
        </div>

        <div class="hrm-popup-footer">
          <vs-button color="dark" type="border" @click="modalActive = false"><i class="fa-solid fa-xmark mr-1"></i> Hủy</vs-button>
          <vs-button color="primary" :disabled="saving" @click="saveProfileDocuments"><i class="fa-solid fa-floppy-disk mr-1"></i> Lưu lại</vs-button>
        </div>
      </div>
    </vs-popup>
  </div>
</template>

<script>
import axios from '../../../http/axios.js';
import moment from 'moment';

const REQUIRED_DOCS = [
  { key: 'don_xin_viec', label: 'Đơn xin việc', short: 'Đơn xin việc' },
  { key: 'giay_kham_sk', label: 'Giấy khám sức khỏe', short: 'Giấy khám SK' },
  { key: 'vneid', label: 'Thông tin cư trú trên VNeID', short: 'Cư trú VNeID' },
  { key: 'cccd_cong_chung', label: 'CCCD công chứng', short: 'CCCD công chứng' },
  { key: 'so_yeu_ly_lich', label: 'Sơ yếu lý lịch', short: 'Sơ yếu lý lịch' }
];

export default {
  data() {
    return {
      flatpickrDateConfig: { dateFormat: 'Y-m-d', altInput: true, altFormat: 'd/m/Y', allowInput: true },
      allRequiredDocs: REQUIRED_DOCS,
      documentsList: [],
      filterStatus: 'all',
      searchKeyword: '',
      statCards: [
        { value: 'all', label: 'Tổng hồ sơ', icon: 'fa-solid fa-folder', bg: 'bg-primary-light text-primary', text: 'text-primary' },
        { value: 'full', label: 'Đã đủ giấy tờ', icon: 'fa-solid fa-circle-check', bg: 'bg-success-light text-success', text: 'text-success' },
        { value: 'missing', label: 'Còn thiếu', icon: 'fa-solid fa-triangle-exclamation', bg: 'bg-danger-light text-danger', text: 'text-danger' }
      ],
      modalActive: false,
      saving: false,
      selectedItem: null,
      collectedDocuments: [],
      editForm: { profile_received_date: '' },
      currentPage: 1,
      perPage: 20
    };
  },
  computed: {
    stats() {
      const full = this.documentsList.filter(d => d.profile_status === 'full').length;
      return { all: this.documentsList.length, full, missing: this.documentsList.length - full };
    },
    // Lọc phía client để chuyển tab/tìm kiếm tức thì, không gọi lại API
    filteredDocuments() {
      const kw = this.normalize(this.searchKeyword.trim());
      return this.documentsList.filter(d => {
        const matchStatus = this.filterStatus === 'all'
          || (this.filterStatus === 'full' ? d.profile_status === 'full' : d.profile_status !== 'full');
        const text = this.normalize(`${d.user_name || ''} ${d.employee_code || ''} ${d.team_name || ''}`);
        return matchStatus && (!kw || text.includes(kw));
      });
    },
    paginatedDocuments() {
      const start = (this.currentPage - 1) * this.perPage;
      return this.filteredDocuments.slice(start, start + this.perPage);
    },
    totalPages() {
      return Math.ceil(this.filteredDocuments.length / this.perPage) || 1;
    },
    pendingMissing() {
      return this.allRequiredDocs.map(d => d.key).filter(k => !this.collectedDocuments.includes(k));
    },
    autoNote() {
      return this.pendingMissing.length ? this.formatMissingList(this.pendingMissing) : 'Đã đủ toàn bộ giấy tờ';
    }
  },
  watch: {
    searchKeyword() {
      this.currentPage = 1;
    }
  },
  created() {
    this.fetchDocuments();
  },
  methods: {
    // Chuẩn hóa chuỗi: chữ thường + bỏ dấu tiếng Việt để tìm kiếm không dấu
    normalize(str) {
      return String(str || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/đ/g, 'd');
    },
    fmtDate(value, fallback = '') {
      return value ? moment(value).format('DD/MM/YYYY') : fallback;
    },
    setFilter(value) {
      this.filterStatus = value;
      this.currentPage = 1;
    },
    collectedCount(item) {
      return this.allRequiredDocs.length - (item.missing_documents || []).length;
    },
    progressPercent(item) {
      return Math.round((this.collectedCount(item) / this.allRequiredDocs.length) * 100);
    },
    fetchDocuments() {
      this.$vs.loading();
      axios.p('/api/hrm/employees/documents-overview', { profile_status: 'all', keyword: '' })
        .then(res => { this.documentsList = Array.isArray(res.data) ? res.data : []; })
        .catch(() => this.notify('Lỗi', 'Không thể tải danh sách hồ sơ', 'danger'))
        .finally(() => this.$vs.loading.close());
    },
    formatMissingList(missingKeys) {
      if (!missingKeys || missingKeys.length === 0) return 'Đã đủ';
      const labels = missingKeys.map(k => (REQUIRED_DOCS.find(d => d.key === k) || { short: k }).short);
      return 'Thiếu: ' + labels.join(', ');
    },
    toggleAll() {
      this.collectedDocuments = this.collectedDocuments.length === this.allRequiredDocs.length
        ? []
        : this.allRequiredDocs.map(d => d.key);
    },
    openEditModal(item) {
      this.selectedItem = item;
      this.editForm = { profile_received_date: item.profile_received_date || moment().format('YYYY-MM-DD') };
      const missing = item.missing_documents || [];
      this.collectedDocuments = this.allRequiredDocs.map(d => d.key).filter(k => !missing.includes(k));
      this.modalActive = true;
    },
    saveProfileDocuments() {
      this.saving = true;
      axios.p(`/api/hrm/employees/update-documents/${this.selectedItem.id}`, {
        profile_received_date: this.editForm.profile_received_date,
        missing_documents: this.pendingMissing,
        profile_note: this.autoNote
      })
        .then(res => {
          this.notify('Thành công', res.data.message, 'success');
          this.modalActive = false;
          this.fetchDocuments();
        })
        .catch(() => this.notify('Lỗi', 'Cập nhật thất bại', 'danger'))
        .finally(() => { this.saving = false; });
    },
    notify(title, text, color) {
      this.$vs.notify({ title, text, color });
    }
  }
};
</script>

<style scoped>
.doc-progress {
  flex: 1;
  min-width: 80px;
  height: 6px;
  background: #e5e7eb;
  border-radius: 999px;
  overflow: hidden;
}
.doc-progress-bar {
  height: 100%;
  border-radius: 999px;
  transition: width 0.2s ease;
}
</style>
