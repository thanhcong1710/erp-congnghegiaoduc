<template>
  <div id="page-hrm-departments">
    <!-- Header Section -->
    <div class="flex flex-wrap items-center justify-between mb-4">
      <div class="mb-2 md:mb-0">
        <h3 class="font-bold text-gray-800 flex items-center">
          <feather-icon icon="LayersIcon" class="mr-2 text-primary" /> Cơ Cấu Tổ Chức & Phòng Ban
        </h3>
        <p class="text-sm text-gray-500 mt-1">Quản lý mô hình sơ đồ phân cấp Khối - Phòng ban - Bộ phận trực thuộc</p>
      </div>
      <div class="flex items-center">
        <vs-button color="dark" type="border" class="mr-2" @click="fetchDepartments">
          <i class="fa fa-sync-alt mr-1"></i> Tải lại
        </vs-button>
        <vs-button color="primary" type="filled" @click="openPopup()">
          <i class="fa fa-plus mr-1"></i> Thêm phòng ban mới
        </vs-button>
      </div>
    </div>

    <!-- Stat Cards Overview -->
    <div class="vx-row mb-4">
      <div class="vx-col w-full sm:w-1/2 lg:w-1/4 mb-3">
        <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-100 flex items-center justify-between">
          <div>
            <p class="text-xs uppercase font-semibold text-gray-500">Tổng số phòng ban</p>
            <h3 class="font-bold text-primary mt-1">{{ stats.total }}</h3>
            <span class="text-xs text-gray-400">Toàn bộ cơ cấu tổ chức</span>
          </div>
          <div class="p-3 rounded-full bg-primary-light text-primary">
            <feather-icon icon="BriefcaseIcon" class="w-6 h-6" />
          </div>
        </div>
      </div>

      <div class="vx-col w-full sm:w-1/2 lg:w-1/4 mb-3">
        <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-100 flex items-center justify-between">
          <div>
            <p class="text-xs uppercase font-semibold text-gray-500">Khối cấp cao (Cấp 1)</p>
            <h3 class="font-bold text-warning mt-1">{{ stats.root }}</h3>
            <span class="text-xs text-gray-400">Trực thuộc Ban Lãnh Đạo</span>
          </div>
          <div class="p-3 rounded-full bg-warning-light text-warning">
            <feather-icon icon="GitBranchIcon" class="w-6 h-6" />
          </div>
        </div>
      </div>

      <div class="vx-col w-full sm:w-1/2 lg:w-1/4 mb-3">
        <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-100 flex items-center justify-between">
          <div>
            <p class="text-xs uppercase font-semibold text-gray-500">Tổng số nhân viên</p>
            <h3 class="font-bold text-info mt-1">{{ stats.totalEmployees }}</h3>
            <span class="text-xs text-gray-400">Đã phân bổ vào phòng ban</span>
          </div>
          <div class="p-3 rounded-full bg-info-light text-info">
            <feather-icon icon="UsersIcon" class="w-6 h-6" />
          </div>
        </div>
      </div>

      <div class="vx-col w-full sm:w-1/2 lg:w-1/4 mb-3">
        <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-100 flex items-center justify-between">
          <div>
            <p class="text-xs uppercase font-semibold text-gray-500">Đang hoạt động</p>
            <h3 class="font-bold text-success mt-1">{{ stats.active }}</h3>
            <span class="text-xs text-gray-400">Tạm dừng: {{ stats.inactive }}</span>
          </div>
          <div class="p-3 rounded-full bg-success-light text-success">
            <feather-icon icon="CheckCircleIcon" class="w-6 h-6" />
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
            Tất cả ({{ flatDepartments.length }})
          </vs-button>
          <vs-button 
            :type="filterType === 'root' ? 'filled' : 'border'" 
            color="warning" 
            size="small" 
            class="py-2 px-3 mr-2"
            @click="filterType = 'root'"
          >
            Khối Cấp 1 ({{ stats.root }})
          </vs-button>
          <vs-button 
            :type="filterType === 'sub' ? 'filled' : 'border'" 
            color="success" 
            size="small" 
            class="py-2 px-3 mr-2"
            @click="filterType = 'sub'"
          >
            Bộ phận con ({{ stats.sub }})
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
            placeholder="Tìm theo tên phòng ban, mô tả..." 
            v-model="searchKeyword"
          >
          <i class="vs-icon notranslate icon-scale material-icons null" style="font-size: 18px;">search</i>
        </div>
      </div>

      <!-- Department Table -->
      <vs-table :data="filteredDepartments" pagination :max-items="25">
        <template slot="thead">
          <vs-th style="width: 50px;">STT</vs-th>
          <vs-th style="min-width: 300px;">Tên phòng ban / Cơ cấu tổ chức</vs-th>
          <vs-th style="width: 130px;">Cấp bậc</vs-th>
          <vs-th style="width: 170px; text-align: center;">Số lượng nhân viên</vs-th>
          <vs-th style="min-width: 200px;">Mô tả chức năng</vs-th>
          <vs-th style="width: 120px;">Trạng thái</vs-th>
          <vs-th style="width: 160px; text-align: center;">Thao tác</vs-th>
        </template>

        <template slot-scope="{data}">
          <vs-tr :key="indextr" v-for="(tr, indextr) in data">
            <!-- STT -->
            <vs-td class="text-center font-semibold text-gray-500">
              {{ indextr + 1 }}
            </vs-td>

            <!-- Tên phòng ban với sơ đồ cây -->
            <vs-td>
              <!-- Cấp 1 (Khối cao nhất) -->
              <div v-if="tr.level === 0" class="flex items-center py-1">
                <div class="w-8 h-8 rounded bg-primary-light text-primary flex items-center justify-center mr-2 font-bold flex-shrink-0">
                  <feather-icon icon="LayersIcon" class="w-4 h-4" />
                </div>
                <div>
                  <div class="font-bold text-gray-800 text-base flex items-center">
                    {{ tr.name }}
                    <span v-if="tr.children_count > 0" class="ml-2 text-xs bg-blue-100 text-primary px-2 py-0.5 rounded-full font-medium">
                      {{ tr.children_count }} trực thuộc
                    </span>
                  </div>
                </div>
              </div>

              <!-- Cấp 2, 3+ (Phòng ban con / Team) -->
              <div v-else class="flex items-center py-1" :style="{ paddingLeft: ((tr.level - 1) * 24 + 16) + 'px' }">
                <div class="flex items-center text-gray-400 mr-2 flex-shrink-0">
                  <span class="text-gray-400 font-mono text-sm">└─</span>
                  <feather-icon :icon="tr.level === 1 ? 'FolderIcon' : 'FileTextIcon'" class="w-4 h-4 ml-1 text-gray-500" />
                </div>
                <div>
                  <div class="font-medium text-gray-800 flex items-center">
                    {{ tr.name }}
                    <span v-if="tr.children_count > 0" class="ml-2 text-xs bg-gray-200 text-gray-700 px-2 py-0.5 rounded-full font-normal">
                      {{ tr.children_count }} con
                    </span>
                  </div>
                  <div v-if="tr.parent_name" class="text-xs text-gray-400">
                    Thuộc: <span class="font-medium text-gray-500">{{ tr.parent_name }}</span>
                  </div>
                </div>
              </div>
            </vs-td>

            <!-- Cấp bậc -->
            <vs-td>
              <vs-chip v-if="tr.level === 0" color="primary">Khối (Cấp 1)</vs-chip>
              <vs-chip v-else-if="tr.level === 1" color="warning">Phòng (Cấp 2)</vs-chip>
              <vs-chip v-else color="dark">Team (Cấp {{ tr.level + 1 }})</vs-chip>
            </vs-td>

            <!-- Số lượng nhân viên -->
            <vs-td class="text-center">
              <div class="flex flex-col items-center justify-center">
                <span 
                  :class="(tr.employees_count || 0) > 0 ? 'bg-primary-light text-primary font-bold px-3 py-1 rounded-full text-sm inline-flex items-center' : 'text-gray-400 text-sm'"
                >
                  <feather-icon icon="UsersIcon" class="w-3.5 h-3.5 mr-1" v-if="(tr.employees_count || 0) > 0" />
                  {{ tr.employees_count || 0 }} nhân viên
                </span>
                <span 
                  v-if="tr.children_count > 0 && tr.total_tree_employees > tr.employees_count" 
                  class="text-xs text-primary font-semibold mt-1"
                  :title="'Bao gồm cả các bộ phận con: ' + tr.total_tree_employees + ' nhân viên'"
                >
                  (Tổng cả khối: {{ tr.total_tree_employees }})
                </span>
              </div>
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

            <!-- Thao tác: Thêm cấp con, Sửa, Xóa bản ghi thừa -->
            <vs-td>
              <div class="flex items-center justify-center space-x-1">
                <!-- Thêm cấp con trực thuộc -->
                <vx-tooltip text="Thêm bộ phận trực thuộc">
                  <vs-button 
                    size="small" 
                    color="primary" 
                    type="border" 
                    class="p-2 mr-1"
                    @click="openAddChild(tr)"
                  >
                    <i class="fa fa-plus-circle"></i>
                  </vs-button>
                </vx-tooltip>

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

                <!-- Xóa bản ghi thừa -->
                <vx-tooltip text="Xóa bản ghi thừa">
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
      <div v-if="filteredDepartments.length === 0" class="text-center py-8 text-gray-500">
        <feather-icon icon="InboxIcon" class="w-12 h-12 text-gray-300 mx-auto mb-2" />
        <p class="font-medium text-gray-600">Không tìm thấy phòng ban nào</p>
        <span class="text-sm text-gray-400">Thử thay đổi từ khóa tìm kiếm hoặc bộ lọc</span>
      </div>
    </vx-card>

    <!-- Popup Thêm / Cập nhật phòng ban -->
    <vs-popup :title="isEdit ? 'Cập Nhật Phòng Ban' : 'Thêm Phòng Ban Mới'" :active.sync="popupActive">
      <div class="p-3">
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <vs-input 
              class="w-full" 
              label="Tên phòng ban (*)" 
              placeholder="Ví dụ: Khối Công Nghệ, Phòng Kế Toán..." 
              v-model="form.name" 
            />
          </div>
        </div>

        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <label class="text-sm font-medium text-gray-700">Phòng ban cấp cha (Trực thuộc)</label>
            <vs-select class="w-full mt-1" v-model="form.parent_id" autocomplete>
              <vs-select-item :value="null" text="-- Là cấp cao nhất (Khối / Tổng công ty) --" />
              <vs-select-item 
                :key="index" 
                :value="item.id" 
                :text="item.displayName" 
                v-for="(item, index) in parentSelectOptions" 
                :disabled="isEdit && (item.id === form.id || isDescendant(item.id, form.id))" 
              />
            </vs-select>
            <small class="text-gray-400 mt-1 block">Chọn phòng ban cấp trên mà đơn vị này trực thuộc, hoặc để trống nếu là Khối cao nhất</small>
          </div>
        </div>

        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <vs-textarea 
              label="Mô tả chức năng nhiệm vụ" 
              placeholder="Nhập mô tả nhiệm vụ, phạm vi hoạt động của phòng ban..." 
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
      departments: [],
      flatDepartments: [],
      searchKeyword: '',
      filterType: 'all', // 'all', 'root', 'sub', 'inactive'
      popupActive: false,
      isEdit: false,
      form: {
        id: null,
        name: '',
        parent_id: null,
        description: '',
        status: 1
      }
    };
  },
  computed: {
    stats() {
      const total = this.flatDepartments.length;
      const root = this.flatDepartments.filter(d => d.level === 0).length;
      const sub = total - root;
      const active = this.flatDepartments.filter(d => d.status == 1).length;
      const inactive = total - active;
      const totalEmployees = this.flatDepartments.reduce((acc, d) => acc + Number(d.employees_count || 0), 0);
      return { total, root, sub, active, inactive, totalEmployees };
    },
    filteredDepartments() {
      let list = [...this.flatDepartments];

      // Tab filter
      if (this.filterType === 'root') {
        list = list.filter(d => d.level === 0);
      } else if (this.filterType === 'sub') {
        list = list.filter(d => d.level > 0);
      } else if (this.filterType === 'inactive') {
        list = list.filter(d => d.status == 0);
      }

      // Keyword search
      if (this.searchKeyword && this.searchKeyword.trim() !== '') {
        const kw = this.searchKeyword.trim().toLowerCase();
        list = list.filter(d => {
          const matchName = d.name && d.name.toLowerCase().includes(kw);
          const matchDesc = d.description && d.description.toLowerCase().includes(kw);
          const matchParent = d.parent_name && d.parent_name.toLowerCase().includes(kw);
          return matchName || matchDesc || matchParent;
        });
      }

      return list;
    },
    parentSelectOptions() {
      return this.flatDepartments.map(d => {
        const prefix = d.level > 0 ? '— '.repeat(d.level) : '';
        return {
          ...d,
          displayName: `${prefix}${d.name} (Cấp ${d.level + 1})`
        };
      });
    }
  },
  created() {
    this.fetchDepartments();
  },
  methods: {
    fetchDepartments() {
      this.$vs.loading();
      axios.p('/api/hrm/departments/list')
        .then(res => {
          this.$vs.loading.close();
          this.departments = res.data || [];
          this.flatDepartments = this.flattenDepartments(this.departments);
        })
        .catch(err => {
          this.$vs.loading.close();
          console.error(err);
          this.$vs.notify({
            title: 'Lỗi',
            text: 'Không thể tải danh sách phòng ban',
            color: 'danger',
            iconPack: 'feather',
            icon: 'icon-alert-circle'
          });
        });
    },
    calcTotalEmployees(dep) {
      let sum = Number(dep.employees_count || 0);
      if (dep.children && dep.children.length > 0) {
        dep.children.forEach(c => {
          sum += this.calcTotalEmployees(c);
        });
      }
      return sum;
    },
    flattenDepartments(deps, level = 0, parentName = '') {
      let flat = [];
      if (!Array.isArray(deps)) return flat;

      deps.forEach(d => {
        const totalTree = this.calcTotalEmployees(d);
        const item = { 
          ...d, 
          level: level,
          parent_name: parentName,
          children_count: d.children ? d.children.length : (d.children_count || 0),
          employees_count: Number(d.employees_count || 0),
          total_tree_employees: totalTree
        };
        flat.push(item);
        if (d.children && d.children.length > 0) {
          flat = flat.concat(this.flattenDepartments(d.children, level + 1, d.name));
        }
      });
      return flat;
    },
    isDescendant(candidateId, ancestorId) {
      if (!candidateId || !ancestorId) return false;
      const candidate = this.flatDepartments.find(d => d.id === candidateId);
      if (!candidate || !candidate.parent_id) return false;
      if (candidate.parent_id === ancestorId) return true;
      return this.isDescendant(candidate.parent_id, ancestorId);
    },
    openPopup(item = null) {
      if (item) {
        this.isEdit = true;
        this.form = {
          id: item.id,
          name: item.name,
          parent_id: item.parent_id || null,
          description: item.description || '',
          status: item.status !== undefined ? Number(item.status) : 1
        };
      } else {
        this.isEdit = false;
        this.form = {
          id: null,
          name: '',
          parent_id: null,
          description: '',
          status: 1
        };
      }
      this.popupActive = true;
    },
    openAddChild(parentItem) {
      this.isEdit = false;
      this.form = {
        id: null,
        name: '',
        parent_id: parentItem.id,
        description: '',
        status: 1
      };
      this.popupActive = true;
    },
    saveData() {
      if (!this.form.name || this.form.name.trim() === '') {
        this.$vs.notify({
          title: 'Cảnh báo',
          text: 'Vui lòng nhập tên phòng ban',
          color: 'warning',
          iconPack: 'feather',
          icon: 'icon-alert-triangle'
        });
        return;
      }

      this.$vs.loading();
      const url = this.isEdit 
        ? `/api/hrm/departments/update/${this.form.id}` 
        : '/api/hrm/departments/add';

      axios.p(url, this.form)
        .then(res => {
          this.$vs.loading.close();
          if (res.data.status === 1) {
            this.$vs.notify({
              title: 'Thành công',
              text: res.data.message || 'Lưu thông tin phòng ban thành công!',
              color: 'success',
              iconPack: 'feather',
              icon: 'icon-check'
            });
            this.popupActive = false;
            this.fetchDepartments();
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
      // Kiểm tra ràng buộc phòng ban con
      if (item.children_count > 0) {
        this.$vs.dialog({
          color: 'warning',
          title: 'Không thể xóa',
          text: `Phòng ban "${item.name}" đang có ${item.children_count} phòng ban/bộ phận trực thuộc. Vui lòng chuyển hoặc xóa các bộ phận con trước!`,
          acceptText: 'Đã hiểu'
        });
        return;
      }

      // Kiểm tra ràng buộc nhân sự
      if (item.employees_count > 0) {
        this.$vs.dialog({
          color: 'warning',
          title: 'Không thể xóa',
          text: `Phòng ban "${item.name}" đang có ${item.employees_count} nhân sự trực thuộc. Vui lòng điều chuyển nhân sự sang phòng ban khác trước!`,
          acceptText: 'Đã hiểu'
        });
        return;
      }

      // Xác nhận xóa bản ghi thừa
      this.$vs.dialog({
        type: 'confirm',
        color: 'danger',
        title: 'Xác nhận xóa bản ghi',
        text: `Bạn có chắc chắn muốn xóa phòng ban "${item.name}"? Thao tác này sẽ xóa vĩnh viễn dữ liệu bản ghi này.`,
        acceptText: 'Đồng ý xóa',
        cancelText: 'Hủy bỏ',
        accept: () => this.deleteDepartment(item)
      });
    },
    deleteDepartment(item) {
      this.$vs.loading();
      axios.p(`/api/hrm/departments/delete/${item.id}`)
        .then(res => {
          this.$vs.loading.close();
          if (res.data.status === 1) {
            this.$vs.notify({
              title: 'Thành công',
              text: res.data.message || 'Đã xóa phòng ban thành công!',
              color: 'success',
              iconPack: 'feather',
              icon: 'icon-check'
            });
            this.fetchDepartments();
          } else {
            this.$vs.dialog({
              color: 'danger',
              title: 'Không thể xóa',
              text: res.data.message || 'Có lỗi xảy ra khi xóa phòng ban',
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
