<template>
  <div id="page-hrm-job-levels">
    <!-- Header Section -->
    <div class="flex flex-wrap items-center justify-between mb-4">
      <div class="mb-2 md:mb-0">
        <h3 class="font-bold text-gray-800 flex items-center">
          <feather-icon icon="AwardIcon" class="mr-2 text-primary" /> Quản Lý Cấp Bậc Nhân Sự
        </h3>
        <p class="text-sm text-gray-500 mt-1">Định nghĩa và thiết lập các thang cấp bậc năng lực nhân sự (Intern, Junior, Senior, Lead...)</p>
      </div>
      <div class="flex items-center">
        <vs-button color="dark" type="border" class="mr-2" @click="fetchLevels">
          <i class="fa fa-sync-alt mr-1"></i> Tải lại
        </vs-button>
        <vs-button color="primary" type="filled" @click="openPopup()">
          <i class="fa fa-plus mr-1"></i> Thêm cấp bậc mới
        </vs-button>
      </div>
    </div>

    <!-- Stat Cards Overview -->
    <div class="vx-row mb-4">
      <div class="vx-col w-full sm:w-1/2 lg:w-1/4 mb-3">
        <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-100 flex items-center justify-between">
          <div>
            <p class="text-xs uppercase font-semibold text-gray-500">Tổng số cấp bậc</p>
            <h3 class="font-bold text-primary mt-1">{{ stats.total }}</h3>
            <span class="text-xs text-gray-400">Thang năng lực hiện có</span>
          </div>
          <div class="p-3 rounded-full bg-primary-light text-primary">
            <feather-icon icon="AwardIcon" class="w-6 h-6" />
          </div>
        </div>
      </div>

      <div class="vx-col w-full sm:w-1/2 lg:w-1/4 mb-3">
        <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-100 flex items-center justify-between">
          <div>
            <p class="text-xs uppercase font-semibold text-gray-500">Đang áp dụng</p>
            <h3 class="font-bold text-success mt-1">{{ stats.active }}</h3>
            <span class="text-xs text-gray-400">Sẵn sàng phân bổ nhân sự</span>
          </div>
          <div class="p-3 rounded-full bg-success-light text-success">
            <feather-icon icon="CheckCircleIcon" class="w-6 h-6" />
          </div>
        </div>
      </div>

      <div class="vx-col w-full sm:w-1/2 lg:w-1/4 mb-3">
        <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-100 flex items-center justify-between">
          <div>
            <p class="text-xs uppercase font-semibold text-gray-500">Tạm dừng áp dụng</p>
            <h3 class="font-bold text-danger mt-1">{{ stats.inactive }}</h3>
            <span class="text-xs text-gray-400">Đã khóa / ngưng tuyển dụng</span>
          </div>
          <div class="p-3 rounded-full bg-danger-light text-danger">
            <feather-icon icon="AlertCircleIcon" class="w-6 h-6" />
          </div>
        </div>
      </div>

      <div class="vx-col w-full sm:w-1/2 lg:w-1/4 mb-3">
        <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-100 flex items-center justify-between">
          <div>
            <p class="text-xs uppercase font-semibold text-gray-500">Nhân sự đã gán</p>
            <h3 class="font-bold text-info mt-1">{{ stats.totalEmployees }}</h3>
            <span class="text-xs text-gray-400">Đang giữ các cấp bậc này</span>
          </div>
          <div class="p-3 rounded-full bg-info-light text-info">
            <feather-icon icon="UsersIcon" class="w-6 h-6" />
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content Card -->
    <vx-card no-shadow>
      <!-- Filter and Search Toolbar -->
      <div class="flex flex-wrap items-center justify-between mb-4 pb-3 border-b border-gray-200">
        <!-- Quick Filter Buttons -->
        <div class="flex flex-wrap items-center space-x-2 mb-2 md:mb-0">
          <vs-button 
            :type="filterType === 'all' ? 'filled' : 'border'" 
            color="primary" 
            size="small" 
            class="py-2 px-3 mr-2"
            @click="filterType = 'all'"
          >
            Tất cả ({{ levels.length }})
          </vs-button>
          <vs-button 
            :type="filterType === 'active' ? 'filled' : 'border'" 
            color="success" 
            size="small" 
            class="py-2 px-3 mr-2"
            @click="filterType = 'active'"
          >
            Đang hoạt động ({{ stats.active }})
          </vs-button>
          <vs-button 
            :type="filterType === 'inactive' ? 'filled' : 'border'" 
            color="danger" 
            size="small" 
            class="py-2 px-3"
            @click="filterType = 'inactive'"
          >
            Tạm dừng ({{ stats.inactive }})
          </vs-button>
        </div>

        <!-- Search Input -->
        <div class="con-input-search vs-table--search">
          <input 
            type="text" 
            class="input-search vs-table--search-input" 
            style="padding: 10px 32px 10px 14px; font-size: 13px; min-width: 260px;" 
            placeholder="Tìm theo tên cấp bậc, mô tả..." 
            v-model="searchKeyword"
          >
          <i class="vs-icon notranslate icon-scale material-icons null" style="font-size: 18px;">search</i>
        </div>
      </div>

      <!-- Job Levels Table -->
      <vs-table :data="filteredLevels" pagination :max-items="15">
        <template slot="thead">
          <vs-th style="width: 50px;">STT</vs-th>
          <vs-th style="min-width: 260px;">Tên cấp bậc năng lực</vs-th>
          <vs-th style="width: 180px; text-align: center;">Số lượng nhân viên</vs-th>
          <vs-th style="min-width: 280px;">Mô tả trách nhiệm & yêu cầu</vs-th>
          <vs-th style="width: 140px;">Trạng thái</vs-th>
          <vs-th style="width: 140px; text-align: center;">Thao tác</vs-th>
        </template>

        <template slot-scope="{data}">
          <vs-tr :key="indextr" v-for="(tr, indextr) in data">
            <!-- STT -->
            <vs-td class="text-center font-semibold text-gray-500">
              {{ indextr + 1 }}
            </vs-td>

            <!-- Tên cấp bậc -->
            <vs-td>
              <div class="flex items-center py-1">
                <div class="w-8 h-8 rounded bg-primary-light text-primary flex items-center justify-center mr-2 font-bold flex-shrink-0">
                  <feather-icon icon="AwardIcon" class="w-4 h-4" />
                </div>
                <div>
                  <div class="font-bold text-gray-800 text-base">
                    {{ tr.name }}
                  </div>
                </div>
              </div>
            </vs-td>

            <!-- Số lượng nhân sự -->
            <vs-td class="text-center">
              <span 
                :class="(tr.employees_count || 0) > 0 ? 'bg-primary-light text-primary font-bold px-3 py-1 rounded-full text-sm inline-flex items-center' : 'text-gray-400 text-sm'"
              >
                <feather-icon icon="UsersIcon" class="w-3.5 h-3.5 mr-1" v-if="(tr.employees_count || 0) > 0" />
                {{ tr.employees_count || 0 }} nhân viên
              </span>
            </vs-td>

            <!-- Mô tả -->
            <vs-td>
              <span v-if="tr.description" class="text-sm text-gray-600 line-clamp-2" :title="tr.description">
                {{ tr.description }}
              </span>
              <span v-else class="text-xs italic text-gray-400">Chưa có mô tả</span>
            </vs-td>

            <!-- Trạng thái -->
            <vs-td>
              <vs-chip :color="tr.status == 1 ? 'success' : 'danger'">
                {{ tr.status == 1 ? 'Hoạt động' : 'Tạm dừng' }}
              </vs-chip>
            </vs-td>

            <!-- Thao tác: Sửa, Xóa bản ghi -->
            <vs-td>
              <div class="flex items-center justify-center space-x-1">
                <!-- Chỉnh sửa -->
                <vx-tooltip text="Chỉnh sửa thông tin">
                  <vs-button 
                    size="small" 
                    color="success" 
                    type="border" 
                    class="p-2 mr-1"
                    @click="openPopup(tr)"
                  >
                    <i class="fa fa-edit"></i>
                  </vs-button>
                </vx-tooltip>

                <!-- Xóa bản ghi -->
                <vx-tooltip text="Xóa cấp bậc">
                  <vs-button 
                    size="small" 
                    color="danger" 
                    type="border" 
                    class="p-2"
                    @click="confirmDelete(tr)"
                  >
                    <i class="fa fa-trash"></i>
                  </vs-button>
                </vx-tooltip>
              </div>
            </vs-td>
          </vs-tr>
        </template>
      </vs-table>

      <!-- Empty State -->
      <div v-if="filteredLevels.length === 0" class="text-center py-8 text-gray-500">
        <feather-icon icon="InboxIcon" class="w-12 h-12 text-gray-300 mx-auto mb-2" />
        <p class="font-medium text-gray-600">Không tìm thấy cấp bậc nào</p>
        <span class="text-sm text-gray-400">Thử thay đổi từ khóa tìm kiếm hoặc bộ lọc</span>
      </div>
    </vx-card>

    <!-- Popup Thêm / Cập nhật cấp bậc -->
    <vs-popup :title="isEdit ? 'Cập Nhật Cấp Bậc' : 'Thêm Cấp Bậc Mới'" :active.sync="popupActive">
      <div class="p-3">
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <vs-input 
              class="w-full" 
              label="Tên cấp bậc (*)" 
              placeholder="Ví dụ: Intern, Fresher, Junior, Mid-Level, Senior, Lead..." 
              v-model="form.name" 
            />
          </div>
        </div>

        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <vs-textarea 
              label="Mô tả tiêu chuẩn & yêu cầu năng lực" 
              placeholder="Nhập mô tả khung năng lực, yêu cầu kinh nghiệm của cấp bậc này..." 
              v-model="form.description" 
              rows="3"
            />
          </div>
        </div>

        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <label class="text-sm font-medium text-gray-700">Trạng thái áp dụng</label>
            <vs-select class="w-full mt-1" v-model="form.status">
              <vs-select-item :value="1" text="Hoạt động" />
              <vs-select-item :value="0" text="Tạm dừng" />
            </vs-select>
          </div>
        </div>

        <div class="vx-row mt-6 pt-3 border-t border-gray-200">
          <div class="vx-col w-full flex justify-end">
            <vs-button @click="popupActive = false" color="dark" type="border" class="mr-2">
              <i class="fa fa-times mr-1"></i> Hủy
            </vs-button>
            <vs-button @click="saveData()" color="primary" type="filled">
              <i class="fa fa-save mr-1"></i> {{ isEdit ? 'Lưu thay đổi' : 'Tạo mới' }}
            </vs-button>
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
      levels: [],
      searchKeyword: '',
      filterType: 'all', // 'all', 'active', 'inactive'
      popupActive: false,
      isEdit: false,
      form: {
        id: null,
        name: '',
        description: '',
        status: 1
      }
    };
  },
  computed: {
    stats() {
      const total = this.levels.length;
      const active = this.levels.filter(l => l.status == 1).length;
      const inactive = total - active;
      const totalEmployees = this.levels.reduce((acc, l) => acc + Number(l.employees_count || 0), 0);
      return { total, active, inactive, totalEmployees };
    },
    filteredLevels() {
      let list = [...this.levels];

      if (this.filterType === 'active') {
        list = list.filter(l => l.status == 1);
      } else if (this.filterType === 'inactive') {
        list = list.filter(l => l.status == 0);
      }

      if (this.searchKeyword && this.searchKeyword.trim() !== '') {
        const kw = this.searchKeyword.trim().toLowerCase();
        list = list.filter(l => {
          const matchName = l.name && l.name.toLowerCase().includes(kw);
          const matchDesc = l.description && l.description.toLowerCase().includes(kw);
          return matchName || matchDesc;
        });
      }

      return list;
    }
  },
  created() {
    this.fetchLevels();
  },
  methods: {
    fetchLevels() {
      this.$vs.loading();
      axios.p('/api/hrm/job-levels/list')
        .then(res => {
          this.$vs.loading.close();
          this.levels = res.data || [];
        })
        .catch(err => {
          this.$vs.loading.close();
          console.error(err);
          this.$vs.notify({
            title: 'Lỗi',
            text: 'Không thể tải danh sách cấp bậc',
            color: 'danger',
            iconPack: 'feather',
            icon: 'icon-alert-circle'
          });
        });
    },
    openPopup(item = null) {
      if (item) {
        this.isEdit = true;
        this.form = {
          id: item.id,
          name: item.name,
          description: item.description || '',
          status: item.status !== undefined ? Number(item.status) : 1
        };
      } else {
        this.isEdit = false;
        this.form = {
          id: null,
          name: '',
          description: '',
          status: 1
        };
      }
      this.popupActive = true;
    },
    saveData() {
      if (!this.form.name || this.form.name.trim() === '') {
        this.$vs.notify({
          title: 'Cảnh báo',
          text: 'Vui lòng nhập tên cấp bậc',
          color: 'warning',
          iconPack: 'feather',
          icon: 'icon-alert-triangle'
        });
        return;
      }

      this.$vs.loading();
      const url = this.isEdit 
        ? `/api/hrm/job-levels/update/${this.form.id}` 
        : '/api/hrm/job-levels/add';

      axios.p(url, this.form)
        .then(res => {
          this.$vs.loading.close();
          if (res.data.status === 1) {
            this.$vs.notify({
              title: 'Thành công',
              text: res.data.message || 'Lưu thông tin cấp bậc thành công!',
              color: 'success',
              iconPack: 'feather',
              icon: 'icon-check'
            });
            this.popupActive = false;
            this.fetchLevels();
          } else {
            this.$vs.notify({
              title: 'Không thể lưu',
              text: res.data.message || 'Có lỗi xảy ra',
              color: 'danger',
              iconPack: 'feather',
              icon: 'icon-alert-circle'
            });
          }
        })
        .catch(err => {
          this.$vs.loading.close();
          console.error(err);
          this.$vs.notify({
            title: 'Lỗi',
            text: 'Không thể kết nối đến máy chủ',
            color: 'danger',
            iconPack: 'feather',
            icon: 'icon-alert-circle'
          });
        });
    },
    confirmDelete(item) {
      // Kiểm tra ràng buộc nhân sự
      if (Number(item.employees_count || 0) > 0) {
        this.$vs.dialog({
          color: 'warning',
          title: 'Không thể xóa',
          text: `Cấp bậc "${item.name}" hiện đang có ${item.employees_count} nhân sự trực thuộc. Vui lòng chuyển cấp bậc cho nhân sự sang cấp bậc khác trước khi xóa!`,
          acceptText: 'Đã hiểu'
        });
        return;
      }

      // Hộp thoại xác nhận xóa
      this.$vs.dialog({
        type: 'confirm',
        color: 'danger',
        title: 'Xác nhận xóa cấp bậc',
        text: `Bạn có chắc chắn muốn xóa cấp bậc "${item.name}"? Bản ghi này sẽ bị xóa vĩnh viễn khỏi hệ thống.`,
        acceptText: 'Đồng ý xóa',
        cancelText: 'Hủy bỏ',
        accept: () => this.deleteLevel(item)
      });
    },
    deleteLevel(item) {
      this.$vs.loading();
      axios.p(`/api/hrm/job-levels/delete/${item.id}`)
        .then(res => {
          this.$vs.loading.close();
          if (res.data.status === 1) {
            this.$vs.notify({
              title: 'Thành công',
              text: res.data.message || 'Đã xóa cấp bậc thành công!',
              color: 'success',
              iconPack: 'feather',
              icon: 'icon-check'
            });
            this.fetchLevels();
          } else {
            this.$vs.dialog({
              color: 'danger',
              title: 'Không thể xóa',
              text: res.data.message || 'Có lỗi xảy ra khi xóa cấp bậc',
              acceptText: 'Đã hiểu'
            });
          }
        })
        .catch(err => {
          this.$vs.loading.close();
          console.error(err);
          this.$vs.notify({
            title: 'Lỗi',
            text: 'Có lỗi xảy ra khi gửi yêu cầu xóa',
            color: 'danger',
            iconPack: 'feather',
            icon: 'icon-alert-circle'
          });
        });
    }
  }
};
</script>

<style scoped>
.bg-primary-light {
  background-color: rgba(var(--vs-primary), 0.12);
}
.bg-warning-light {
  background-color: rgba(var(--vs-warning), 0.12);
}
.bg-info-light {
  background-color: rgba(var(--vs-info), 0.12);
}
.bg-success-light {
  background-color: rgba(var(--vs-success), 0.12);
}
.bg-danger-light {
  background-color: rgba(var(--vs-danger), 0.12);
}
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
