<template>
  <div>
    <vs-card>
      <div class="flex flex-wrap items-center justify-between mb-4">
        <div>
          <h4><i class="fa fa-folder-open text-primary mr-2"></i> Lưu trữ & Quản lý Hồ sơ Nhân sự</h4>
          <span class="text-sm text-gray-500">Theo dõi ngày nhận hồ sơ, tình trạng đầy đủ và phân loại các giấy tờ còn thiếu</span>
        </div>
        <div class="flex items-center space-x-2 mt-2 md:mt-0">
          <select v-model="filterStatus" @change="fetchDocuments" class="p-2 border rounded text-sm">
            <option value="all">-- Tất cả hồ sơ --</option>
            <option value="full">Đã đủ giấy tờ</option>
            <option value="missing">Còn thiếu giấy tờ</option>
          </select>
        </div>
      </div>

      <!-- Bảng Hồ sơ theo đúng định dạng Ảnh 1 -->
      <vs-table :data="documentsList" search pagination :max-items="15">
        <template slot="thead">
          <vs-th>MÃ NHÂN SỰ</vs-th>
          <vs-th>HỌ VÀ TÊN</vs-th>
          <vs-th>THUỘC TEAM NÀO</vs-th>
          <vs-th>NGÀY NHẬN HỒ SƠ</vs-th>
          <vs-th>THÔNG TIN HỒ SƠ</vs-th>
          <vs-th>GHI CHÚ (THIẾU GIẤY TỜ GÌ...)</vs-th>
          <vs-th>THAO TÁC</vs-th>
        </template>
        <template slot-scope="{data}">
          <vs-tr :key="indextr" v-for="(tr, indextr) in data">
            <vs-td>
              <span class="font-bold text-primary">{{ tr.employee_code || '---' }}</span>
            </vs-td>
            <vs-td class="font-bold uppercase text-gray-900">{{ tr.user_name }}</vs-td>
            <vs-td>{{ tr.team_name }}</vs-td>
            <vs-td>
              <span>{{ tr.profile_received_date || 'Chưa nhận' }}</span>
            </vs-td>
            <vs-td>
              <vs-chip :color="tr.profile_status === 'full' ? 'success' : 'danger'">
                {{ tr.profile_status === 'full' ? 'Đã đủ' : 'Thiếu' }}
              </vs-chip>
            </vs-td>
            <vs-td>
              <div v-if="tr.profile_status === 'missing'" class="text-xs text-danger">
                <span class="font-semibold">{{ tr.profile_note || formatMissingList(tr.missing_documents) }}</span>
              </div>
              <div v-else class="text-xs text-success">
                <i class="fa fa-check-circle"></i> Đầy đủ giấy tờ
              </div>
            </vs-td>
            <vs-td>
              <vs-button size="small" color="primary" @click="openEditModal(tr)">
                <i class="fa fa-edit"></i> Cập nhật hồ sơ
              </vs-button>
            </vs-td>
          </vs-tr>
        </template>
      </vs-table>
    </vs-card>

    <!-- Modal Cập nhật Checklist Hồ sơ -->
    <vs-popup title="Cập nhật Tình trạng Hồ sơ & Giấy tờ" :active.sync="modalActive">
      <div class="p-4" v-if="selectedItem">
        <div class="mb-4 p-3 bg-gray-50 border rounded">
          <div class="font-bold text-gray-800">Nhân sự: {{ selectedItem.user_name }} ({{ selectedItem.employee_code }})</div>
          <div class="text-sm text-gray-600">Team: {{ selectedItem.team_name }}</div>
        </div>

        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <vs-input type="date" class="w-full" label="Ngày nhận hồ sơ" v-model="editForm.profile_received_date" />
          </div>
        </div>

        <div class="mb-4">
          <label class="text-base font-semibold block mb-2">Checklist giấy tờ (Đánh dấu các giấy tờ ĐÃ THU ĐỦ):</label>
          <div class="space-y-2 border p-3 rounded bg-white">
            <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
              <input type="checkbox" value="don_xin_viec" v-model="collectedDocuments" class="mr-3" />
              <span class="text-sm font-medium">1. Đơn xin việc</span>
            </label>
            <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
              <input type="checkbox" value="giay_kham_sk" v-model="collectedDocuments" class="mr-3" />
              <span class="text-sm font-medium">2. Giấy khám sức khỏe</span>
            </label>
            <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
              <input type="checkbox" value="vneid" v-model="collectedDocuments" class="mr-3" />
              <span class="text-sm font-medium">3. Thông tin cư trú trên VNeID</span>
            </label>
            <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
              <input type="checkbox" value="cccd_cong_chung" v-model="collectedDocuments" class="mr-3" />
              <span class="text-sm font-medium">4. CCCD công chứng</span>
            </label>
            <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
              <input type="checkbox" value="so_yeu_ly_lich" v-model="collectedDocuments" class="mr-3" />
              <span class="text-sm font-medium">5. Sơ yếu lý lịch</span>
            </label>
          </div>
        </div>

        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <vs-textarea label="Ghi chú hồ sơ (Hệ thống tự tổng hợp hoặc nhập thêm)" v-model="editForm.profile_note" />
          </div>
        </div>

        <div class="vx-row">
          <div class="vx-col w-full flex justify-end">
            <vs-button @click="saveProfileDocuments()" color="success" class="mr-2"><i class="fa fa-save"></i> Lưu lại</vs-button>
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
      documentsList: [],
      filterStatus: 'all',
      modalActive: false,
      selectedItem: null,
      collectedDocuments: [], // Danh sách các giấy tờ đã nộp
      editForm: {
        profile_received_date: '',
        profile_note: ''
      },
      allRequiredDocs: [
        { key: 'don_xin_viec', label: 'Đơn xin việc' },
        { key: 'giay_kham_sk', label: 'Giấy khám sức khỏe' },
        { key: 'vneid', label: 'Thông tin cư trú trên VNeID' },
        { key: 'cccd_cong_chung', label: 'CCCD công chứng' },
        { key: 'so_yeu_ly_lich', label: 'Sơ yếu lý lịch' }
      ]
    };
  },
  created() {
    this.fetchDocuments();
  },
  methods: {
    fetchDocuments() {
      this.$vs.loading();
      axios.p('/api/hrm/employees/documents-overview', {
        profile_status: this.filterStatus
      }).then(res => {
        this.documentsList = res.data || [];
        this.$vs.loading.close();
      }).catch(err => {
        this.$vs.loading.close();
        console.error(err);
      });
    },
    formatMissingList(missingKeys) {
      if (!missingKeys || missingKeys.length === 0) return 'Đã đủ';
      const map = {
        don_xin_viec: 'Đơn xin việc',
        giay_kham_sk: 'Giấy khám SK',
        vneid: 'Cư trú VNeID',
        cccd_cong_chung: 'CCCD công chứng',
        so_yeu_ly_lich: 'Sơ yếu lý lịch'
      };
      return 'Thiếu: ' + missingKeys.map(k => map[k] || k).join(', ');
    },
    openEditModal(item) {
      this.selectedItem = item;
      this.editForm = {
        profile_received_date: item.profile_received_date || new Date().toISOString().split('T')[0],
        profile_note: item.profile_note || ''
      };
      
      // Tính collectedDocuments ngược lại từ missing_documents
      const missing = item.missing_documents || [];
      this.collectedDocuments = this.allRequiredDocs
        .map(d => d.key)
        .filter(k => !missing.includes(k));

      this.modalActive = true;
    },
    saveProfileDocuments() {
      // Tìm các giấy tờ bị thiếu
      const missing = this.allRequiredDocs
        .map(d => d.key)
        .filter(k => !this.collectedDocuments.includes(k));

      let note = this.editForm.profile_note;
      if (missing.length > 0) {
        note = this.formatMissingList(missing);
      } else {
        note = 'Đã đủ toàn bộ giấy tờ';
      }

      this.$vs.loading();
      axios.p(`/api/hrm/employees/update-documents/${this.selectedItem.id}`, {
        profile_received_date: this.editForm.profile_received_date,
        missing_documents: missing,
        profile_note: note
      }).then(res => {
        this.$vs.loading.close();
        this.$vs.notify({ title: 'Thành công', text: res.data.message, color: 'success' });
        this.modalActive = false;
        this.fetchDocuments();
      }).catch(err => {
        this.$vs.loading.close();
        this.$vs.notify({ title: 'Lỗi', text: 'Cập nhật thất bại', color: 'danger' });
      });
    }
  }
};
</script>
