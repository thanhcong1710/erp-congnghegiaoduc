<template>
  <div id="page-hrm-job-titles">
    <!-- Header Section -->
    <div class="flex flex-wrap items-center justify-between mb-4">
      <div class="mb-2 md:mb-0">
        <h3 class="font-bold text-gray-800 flex items-center">
          <feather-icon icon="BriefcaseIcon" class="mr-2 text-primary" /> Quản Lý Chức Danh & Vị Trí
        </h3>
        <p class="text-sm text-gray-500 mt-1">Quản lý danh mục chức danh nghề nghiệp, liên kết phòng ban và số lượng nhân sự đảm nhiệm</p>
      </div>
      <div class="flex items-center">
        <vs-button color="dark" type="border" class="mr-2" @click="reloadData">
          <i class="fa fa-sync-alt mr-1"></i> Tải lại
        </vs-button>
        <vs-button color="primary" type="filled" @click="openPopup()">
          <i class="fa fa-plus mr-1"></i> Thêm chức danh mới
        </vs-button>
      </div>
    </div>

    <!-- Stat Cards Overview -->
    <div class="vx-row mb-4">
      <div class="vx-col w-full sm:w-1/2 lg:w-1/4 mb-3">
        <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-100 flex items-center justify-between">
          <div>
            <p class="text-xs uppercase font-semibold text-gray-500">Tổng chức danh</p>
            <h3 class="font-bold text-primary mt-1">{{ stats.total }}</h3>
            <span class="text-xs text-gray-400">Vị trí chức danh hệ thống</span>
          </div>
          <div class="p-3 rounded-full bg-primary-light text-primary">
            <feather-icon icon="BriefcaseIcon" class="w-6 h-6" />
          </div>
        </div>
      </div>

      <div class="vx-col w-full sm:w-1/2 lg:w-1/4 mb-3">
        <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-100 flex items-center justify-between">
          <div>
            <p class="text-xs uppercase font-semibold text-gray-500">Theo phòng ban</p>
            <h3 class="font-bold text-warning mt-1">{{ stats.withDept }}</h3>
            <span class="text-xs text-gray-400">Gắn riêng từng phòng ban</span>
          </div>
          <div class="p-3 rounded-full bg-warning-light text-warning">
            <feather-icon icon="FolderIcon" class="w-6 h-6" />
          </div>
        </div>
      </div>

      <div class="vx-col w-full sm:w-1/2 lg:w-1/4 mb-3">
        <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-100 flex items-center justify-between">
          <div>
            <p class="text-xs uppercase font-semibold text-gray-500">Dùng chung công ty</p>
            <h3 class="font-bold text-success mt-1">{{ stats.global }}</h3>
            <span class="text-xs text-gray-400">Áp dụng nhiều phòng ban</span>
          </div>
          <div class="p-3 rounded-full bg-success-light text-success">
            <feather-icon icon="Share2Icon" class="w-6 h-6" />
          </div>
        </div>
      </div>

      <div class="vx-col w-full sm:w-1/2 lg:w-1/4 mb-3">
        <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-100 flex items-center justify-between">
          <div>
            <p class="text-xs uppercase font-semibold text-gray-500">Nhân sự đảm nhiệm</p>
            <h3 class="font-bold text-info mt-1">{{ stats.totalEmployees }}</h3>
            <span class="text-xs text-gray-400">Đã gán chức danh</span>
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
            Tất cả ({{ titles.length }})
          </vs-button>
          <vs-button 
            :type="filterType === 'withDept' ? 'filled' : 'border'" 
            color="warning" 
            size="small" 
            class="py-2 px-3 mr-2"
            @click="filterType = 'withDept'"
          >
            Theo phòng ban ({{ stats.withDept }})
          </vs-button>
          <vs-button 
            :type="filterType === 'global' ? 'filled' : 'border'" 
            color="success" 
            size="small" 
            class="py-2 px-3 mr-2"
            @click="filterType = 'global'"
          >
            Dùng chung ({{ stats.global }})
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
            placeholder="Tìm theo chức danh, phòng ban..." 
            v-model="searchKeyword"
          >
          <i class="vs-icon notranslate icon-scale material-icons null" style="font-size: 18px;">search</i>
        </div>
      </div>

      <!-- Job Titles Table -->
      <vs-table :data="filteredTitles" pagination :max-items="15">
        <template slot="thead">
          <vs-th style="width: 50px;">STT</vs-th>
          <vs-th style="min-width: 250px;">Tên chức danh vị trí</vs-th>
          <vs-th style="min-width: 220px;">Phòng ban liên kết</vs-th>
          <vs-th style="width: 170px; text-align: center;">Số lượng nhân viên</vs-th>
          <vs-th style="min-width: 220px;">Mô tả nhiệm vụ</vs-th>
          <vs-th style="width: 130px;">Trạng thái</vs-th>
          <vs-th style="width: 140px; text-align: center;">Thao tác</vs-th>
        </template>

        <template slot-scope="{data}">
          <vs-tr :key="indextr" v-for="(tr, indextr) in data">
            <!-- STT -->
            <vs-td class="text-center font-semibold text-gray-500">
              {{ indextr + 1 }}
            </vs-td>

            <!-- Tên chức danh -->
            <vs-td>
              <div class="flex items-center py-1">
                <div class="w-8 h-8 rounded bg-primary-light text-primary flex items-center justify-center mr-2 font-bold flex-shrink-0">
                  <feather-icon icon="BriefcaseIcon" class="w-4 h-4" />
                </div>
                <div>
                  <div class="font-bold text-gray-800 text-base">
                    {{ tr.name }}
                  </div>
                </div>
              </div>
            </vs-td>

            <!-- Phòng ban liên kết -->
            <vs-td>
              <div v-if="tr.department" class="flex items-center text-gray-700">
                <feather-icon icon="FolderIcon" class="w-4 h-4 mr-1 text-primary flex-shrink-0" />
                <span class="font-medium">{{ tr.department.name }}</span>
              </div>
              <div v-else>
                <vs-chip color="dark">
                  Dùng chung toàn công ty
                </vs-chip>
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
                <vx-tooltip text="Xóa chức danh">
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
      <div v-if="filteredTitles.length === 0" class="text-center py-8 text-gray-500">
        <feather-icon icon="InboxIcon" class="w-12 h-12 text-gray-300 mx-auto mb-2" />
        <p class="font-medium text-gray-600">Không tìm thấy chức danh nào</p>
        <span class="text-sm text-gray-400">Thử thay đổi từ khóa tìm kiếm hoặc bộ lọc</span>
      </div>
    </vx-card>

    <!-- Popup Thêm / Cập nhật chức danh -->
    <vs-popup :title="isEdit ? 'Cập Nhật Chức Danh' : 'Thêm Chức Danh Mới'" :active.sync="popupActive">
      <div class="p-3">
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <vs-input 
              class="w-full" 
              label="Tên chức danh (*)" 
              placeholder="Ví dụ: Software Engineer, Sales Executive, Kế toán trưởng..." 
              v-model="form.name" 
            />
          </div>
        </div>

        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <label class="text-sm font-medium text-gray-700">Phòng ban liên kết</label>
            <vs-select class="w-full mt-1" v-model="form.department_id" autocomplete>
              <vs-select-item :value="null" text="-- Dùng chung toàn công ty (Không gắn phòng ban) --" />
              <vs-select-item 
                :key="index" 
                :value="item.id" 
                :text="item.displayName" 
                v-for="(item, index) in departments" 
              />
            </vs-select>
            <small class="text-gray-400 mt-1 block">Chọn phòng ban áp dụng chức danh này, hoặc để trống nếu là chức danh dùng chung</small>
          </div>
        </div>

        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <vs-textarea 
              label="Mô tả nhiệm vụ & phạm vi công việc" 
              placeholder="Nhập mô tả vai trò, trách nhiệm của chức danh này..." 
              v-model="form.description" 
              rows="3"
            />
          </div>
        </div>

        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <label class="text-sm font-medium text-gray-700">Trạng thái hoạt động</label>
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
      titles: [],
      departments: [],
      searchKeyword: '',
      filterType: 'all', // 'all', 'withDept', 'global', 'inactive'
      popupActive: false,
      isEdit: false,
      form: {
        id: null,
        name: '',
        department_id: null,
        description: '',
        status: 1
      }
    };
  },
  computed: {
    stats() {
      const total = this.titles.length;
      const withDept = this.titles.filter(t => t.department_id).length;
      const global = total - withDept;
      const active = this.titles.filter(t => t.status == 1).length;
      const inactive = total - active;
      const totalEmployees = this.titles.reduce((acc, t) => acc + Number(t.employees_count || 0), 0);
      return { total, withDept, global, active, inactive, totalEmployees };
    },
    filteredTitles() {
      let list = [...this.titles];

      if (this.filterType === 'withDept') {
        list = list.filter(t => t.department_id);
      } else if (this.filterType === 'global') {
        list = list.filter(t => !t.department_id);
      } else if (this.filterType === 'inactive') {
        list = list.filter(t => t.status == 0);
      }

      if (this.searchKeyword && this.searchKeyword.trim() !== '') {
        const kw = this.searchKeyword.trim().toLowerCase();
        list = list.filter(t => {
          const matchName = t.name && t.name.toLowerCase().includes(kw);
          const matchDesc = t.description && t.description.toLowerCase().includes(kw);
          const matchDept = t.department && t.department.name && t.department.name.toLowerCase().includes(kw);
          return matchName || matchDesc || matchDept;
        });
      }

      return list;
    }
  },
  created() {
    this.reloadData();
  },
  methods: {
    reloadData() {
      this.fetchTitles();
      this.fetchDepartments();
    },
    fetchTitles() {
      this.$vs.loading();
      axios.p('/api/hrm/job-titles/list')
        .then(res => {
          this.$vs.loading.close();
          this.titles = res.data || [];
        })
        .catch(err => {
          this.$vs.loading.close();
          console.error(err);
          this.$vs.notify({
            title: 'Lỗi',
            text: 'Không thể tải danh sách chức danh',
            color: 'danger',
            iconPack: 'feather',
            icon: 'icon-alert-circle'
          });
        });
    },
    fetchDepartments() {
      axios.p('/api/hrm/departments/list')
        .then(res => {
          this.departments = this.flattenDepartments(res.data || []);
        })
        .catch(err => console.error(err));
    },
    flattenDepartments(deps, level = 0) {
      let flat = [];
      if (!Array.isArray(deps)) return flat;

      deps.forEach(d => {
        const prefix = level > 0 ? '— '.repeat(level) : '';
        flat.push({
          id: d.id,
          name: d.name,
          displayName: `${prefix}${d.name}`
        });
        if (d.children && d.children.length > 0) {
          flat = flat.concat(this.flattenDepartments(d.children, level + 1));
        }
      });
      return flat;
    },
    openPopup(item = null) {
      if (item) {
        this.isEdit = true;
        this.form = {
          id: item.id,
          name: item.name,
          department_id: item.department_id || null,
          description: item.description || '',
          status: item.status !== undefined ? Number(item.status) : 1
        };
      } else {
        this.isEdit = false;
        this.form = {
          id: null,
          name: '',
          department_id: null,
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
          text: 'Vui lòng nhập tên chức danh',
          color: 'warning',
          iconPack: 'feather',
          icon: 'icon-alert-triangle'
        });
        return;
      }

      this.$vs.loading();
      const url = this.isEdit 
        ? `/api/hrm/job-titles/update/${this.form.id}` 
        : '/api/hrm/job-titles/add';

      axios.p(url, this.form)
        .then(res => {
          this.$vs.loading.close();
          if (res.data.status === 1) {
            this.$vs.notify({
              title: 'Thành công',
              text: res.data.message || 'Lưu thông tin chức danh thành công!',
              color: 'success',
              iconPack: 'feather',
              icon: 'icon-check'
            });
            this.popupActive = false;
            this.fetchTitles();
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
          text: `Chức danh "${item.name}" hiện đang có ${item.employees_count} nhân sự đảm nhiệm. Vui lòng chuyển chức danh cho nhân sự sang chức danh khác trước khi xóa!`,
          acceptText: 'Đã hiểu'
        });
        return;
      }

      // Hộp thoại xác nhận xóa
      this.$vs.dialog({
        type: 'confirm',
        color: 'danger',
        title: 'Xác nhận xóa chức danh',
        text: `Bạn có chắc chắn muốn xóa chức danh "${item.name}"? Bản ghi này sẽ bị xóa vĩnh viễn khỏi hệ thống.`,
        acceptText: 'Đồng ý xóa',
        cancelText: 'Hủy bỏ',
        accept: () => this.deleteTitle(item)
      });
    },
    deleteTitle(item) {
      this.$vs.loading();
      axios.p(`/api/hrm/job-titles/delete/${item.id}`)
        .then(res => {
          this.$vs.loading.close();
          if (res.data.status === 1) {
            this.$vs.notify({
              title: 'Thành công',
              text: res.data.message || 'Đã xóa chức danh thành công!',
              color: 'success',
              iconPack: 'feather',
              icon: 'icon-check'
            });
            this.fetchTitles();
          } else {
            this.$vs.dialog({
              color: 'danger',
              title: 'Không thể xóa',
              text: res.data.message || 'Có lỗi xảy ra khi xóa chức danh',
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
