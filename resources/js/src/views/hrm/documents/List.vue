<template>
  <div id="page-hrm-documents">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between mb-4">
      <div class="mb-2 md:mb-0">
        <h3 class="font-bold text-gray-800 flex items-center">
          <feather-icon icon="FolderIcon" class="mr-2 text-primary" /> Lưu Trữ & Quản Lý Hồ Sơ Nhân Sự
        </h3>
        <p class="text-sm text-gray-500 mt-1">Theo dõi ngày nhận hồ sơ, tình trạng đầy đủ và phân loại các giấy tờ còn thiếu</p>
      </div>
    </div>

    <vx-card no-shadow>
      <!-- Toolbar lọc & tìm kiếm -->
      <div class="flex flex-wrap items-center justify-between mb-4">
        <div class="flex items-center mb-2 md:mb-0">
          <span class="text-sm font-medium mr-2 text-gray-700">Tình trạng hồ sơ:</span>
          <select v-model="filterStatus" @change="fetchDocuments" class="vs-inputx vs-input--input normal" style="padding: 10px 14px; font-size: 13px; min-width: 180px;">
            <option value="all">-- Tất cả hồ sơ --</option>
            <option value="full">Đã đủ giấy tờ</option>
            <option value="missing">Còn thiếu giấy tờ</option>
          </select>
        </div>

        <div class="con-input-search vs-table--search">
          <input 
            type="text" 
            class="input-search vs-table--search-input" 
            style="padding: 12px 36px 12px 14px; font-size: 13px; min-width: 260px;" 
            placeholder="Mã nhân sự, họ tên..." 
            v-model="searchKeyword" 
            @input="fetchDocuments"
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
                  <th width="12%">MÃ NHÂN SỰ</th>
                  <th width="18%">HỌ VÀ TÊN</th>
                  <th width="15%">THUỘC TEAM NÀO</th>
                  <th width="14%">NGÀY NHẬN HỒ SƠ</th>
                  <th width="12%">THÔNG TIN HỒ SƠ</th>
                  <th width="20%">GHI CHÚ (THIẾU GIẤY TỜ GÌ...)</th>
                  <th width="80" class="text-center" style="white-space: nowrap;">THAO TÁC</th>
                </tr>
              </thead>
              <tbody>
                <tr class="tr-values vs-table--tr" v-for="(tr, index) in documentsList" :key="index">
                  <td class="td vs-table--td text-center">{{ index + 1 }}</td>
                  <td class="td vs-table--td font-bold text-primary">{{ tr.employee_code || '---' }}</td>
                  <td class="td vs-table--td font-bold uppercase text-gray-900">{{ tr.user_name }}</td>
                  <td class="td vs-table--td">{{ tr.team_name }}</td>
                  <td class="td vs-table--td">{{ tr.profile_received_date || 'Chưa nhận' }}</td>
                  <td class="td vs-table--td">
                    <vs-chip :color="tr.profile_status === 'full' ? 'success' : 'danger'">
                      {{ tr.profile_status === 'full' ? 'Đã đủ' : 'Thiếu' }}
                    </vs-chip>
                  </td>
                  <td class="td vs-table--td">
                    <div v-if="tr.profile_status === 'missing'" class="text-xs text-danger font-semibold">
                      {{ tr.profile_note || formatMissingList(tr.missing_documents) }}
                    </div>
                    <div v-else class="text-xs text-success font-medium">
                      <i class="fa-solid fa-circle-check mr-1"></i> Đầy đủ giấy tờ
                    </div>
                  </td>
                  <td class="td vs-table--td text-center list-action">
                    <vs-button size="small" color="primary" @click="openEditModal(tr)" title="Cập nhật hồ sơ">
                      <i class="fa-solid fa-pen-to-square"></i>
                    </vs-button>
                  </td>
                </tr>
                <tr v-if="!documentsList.length">
                  <td colspan="8" class="text-center p-4 text-gray-500">Không có dữ liệu hồ sơ nhân sự</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </vx-card>

    <!-- Modal Cập nhật Checklist Hồ sơ -->
    <vs-popup title="CẬP NHẬT TÌNH TRẠNG HỒ SƠ & GIẤY TỜ" :active.sync="modalActive">
      <div class="p-4" v-if="selectedItem">
        <div class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded">
          <div class="font-bold text-gray-800">Nhân sự: <span class="text-primary">{{ selectedItem.user_name }}</span> (Mã NS: {{ selectedItem.employee_code }})</div>
          <div class="text-sm text-gray-600 mt-1">Phòng ban: {{ selectedItem.team_name }}</div>
        </div>

        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Ngày nhận hồ sơ</label>
            <input type="date" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="editForm.profile_received_date" />
          </div>
        </div>

        <div class="mb-4">
          <label class="text-sm font-semibold text-gray-800 block mb-2">Checklist giấy tờ (Đánh dấu các giấy tờ ĐÃ THU ĐỦ):</label>
          <div class="space-y-2 border border-gray-200 p-3 rounded bg-white">
            <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer transition">
              <input type="checkbox" value="don_xin_viec" v-model="collectedDocuments" class="mr-3" />
              <span class="text-sm font-medium text-gray-800">1. Đơn xin việc</span>
            </label>
            <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer transition">
              <input type="checkbox" value="giay_kham_sk" v-model="collectedDocuments" class="mr-3" />
              <span class="text-sm font-medium text-gray-800">2. Giấy khám sức khỏe</span>
            </label>
            <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer transition">
              <input type="checkbox" value="vneid" v-model="collectedDocuments" class="mr-3" />
              <span class="text-sm font-medium text-gray-800">3. Thông tin cư trú trên VNeID</span>
            </label>
            <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer transition">
              <input type="checkbox" value="cccd_cong_chung" v-model="collectedDocuments" class="mr-3" />
              <span class="text-sm font-medium text-gray-800">4. CCCD công chứng</span>
            </label>
            <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer transition">
              <input type="checkbox" value="so_yeu_ly_lich" v-model="collectedDocuments" class="mr-3" />
              <span class="text-sm font-medium text-gray-800">5. Sơ yếu lý lịch</span>
            </label>
          </div>
        </div>

        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Ghi chú hồ sơ</label>
            <textarea placeholder="Hệ thống tự tổng hợp giấy tờ thiếu hoặc nhập thêm..." rows="3" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="editForm.profile_note"></textarea>
          </div>
        </div>

        <div class="vx-row mt-6">
          <div class="vx-col w-full flex justify-end">
            <vs-button @click="saveProfileDocuments()" color="success" type="filled" class="p-3 mr-2"><i class="fa-solid fa-floppy-disk mr-1"></i> Lưu Lại</vs-button>
            <vs-button @click="modalActive = false" color="dark" type="border" class="p-3"><i class="fa-solid fa-xmark mr-1"></i> Hủy</vs-button>
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
      searchKeyword: '',
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
        profile_status: this.filterStatus,
        keyword: this.searchKeyword
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
      
      const missing = item.missing_documents || [];
      this.collectedDocuments = this.allRequiredDocs
        .map(d => d.key)
        .filter(k => !missing.includes(k));

      this.modalActive = true;
    },
    saveProfileDocuments() {
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
