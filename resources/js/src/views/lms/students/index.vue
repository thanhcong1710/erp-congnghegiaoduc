<template>
  <div id="page-students-list">
    <!-- Header Banner -->
    <div class="flex flex-wrap items-center justify-between mb-4 mt-2">
      <div>
        <h4 class="font-bold text-gray-800 flex items-center">
          <i class="fa-solid fa-user-graduate text-primary mr-2" style="font-size: 24px;"></i>
          Danh Sách Học Sinh
        </h4>
        <p class="text-sm text-gray-500 mt-1">Quản lý hồ sơ học sinh, tiến độ buổi học và thông tin lớp học</p>
      </div>
      <div class="flex items-center mt-2 sm:mt-0">
        <span class="badge-total-students">
          <i class="fa-solid fa-users mr-1"></i>
          Tổng cộng: <strong>{{ pagination.total }}</strong> học sinh
        </span>
      </div>
    </div>

    <!-- Filter Card -->
    <vx-card no-shadow class="mb-5 shadow-sm border border-solid border-grey-light rounded-lg">
      <div class="vx-row">
        <div class="vx-col md:w-1/3 sm:w-1/2 w-full mb-3">
          <label class="filter-label"><i class="fa-solid fa-location-dot text-primary mr-1"></i> Trung tâm</label>
          <multiselect
            name="search_branch"
            placeholder="Tất cả trung tâm"
            v-model="searchData.arr_branch"
            :options="branch_list"
            label="name"
            :close-on-select="false"
            :hide-selected="true"
            :multiple="true"
            :searchable="true"
            track-by="id"
            selectedLabel="" selectLabel="" deselectLabel=""
          >
            <span slot="noResult">Không tìm thấy dữ liệu</span>
          </multiselect>
        </div>

        <div class="vx-col md:w-1/3 sm:w-1/2 w-full mb-3">
          <label class="filter-label"><i class="fa-solid fa-magnifying-glass text-primary mr-1"></i> Tìm kiếm</label>
          <vs-input
            icon-pack="feather"
            icon="icon-search"
            class="w-full"
            placeholder="Mã HS, Tên học sinh, số điện thoại..."
            v-model="searchData.keyword"
            @keyup.enter="getData"
          ></vs-input>
        </div>

        <div class="vx-col md:w-1/3 sm:w-1/2 w-full mb-3">
          <label class="filter-label"><i class="fa-solid fa-tags text-primary mr-1"></i> Trạng thái học sinh</label>
          <multiselect
            name="search_status"
            placeholder="Tất cả trạng thái"
            v-model="searchData.arr_status"
            :options="statusOptions"
            label="label"
            :close-on-select="false"
            :hide-selected="true"
            :multiple="true"
            :searchable="true"
            track-by="id"
            selectedLabel="" selectLabel="" deselectLabel=""
          >
            <span slot="noResult">Không tìm thấy dữ liệu</span>
          </multiselect>
        </div>
      </div>

      <div class="flex flex-wrap items-center justify-between pt-2 border-t border-solid border-grey-light mt-1">
        <div class="text-xs text-gray-500 italic">
          * Nhấn Enter hoặc bấm "Tìm kiếm" để lọc dữ liệu
        </div>
        <div class="flex items-center mt-2 sm:mt-0">
          <vs-button color="primary" class="mr-3 flex items-center shadow-md" @click="getData">
            <i class="fa fa-search mr-1"></i> Tìm kiếm
          </vs-button>
          <vs-button color="gray" type="border" class="flex items-center" @click="reset">
            <i class="fas fa-undo-alt mr-1"></i> Đặt lại
          </vs-button>
        </div>
      </div>
    </vx-card>

    <!-- Table Card -->
    <vx-card no-shadow class="shadow-sm border border-solid border-grey-light rounded-lg">
      <div class="table-responsive">
        <table class="modern-student-table">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px">STT</th>
              <th style="width: 65px" class="text-center">Avatar</th>
              <th style="width: 250px">Học sinh</th>
              <th style="width: 230px">Phụ huynh</th>
              <th style="width: 240px">Lớp học & Trung tâm</th>
              <th style="width: 170px" class="text-center">Tiến độ buổi</th>
              <th style="width: 100px" class="text-center">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="!students || students.length === 0">
              <td colspan="7" class="text-center py-8 text-gray-500">
                <i class="fa-solid fa-folder-open text-gray-300 mb-2" style="font-size: 36px; display: block;"></i>
                Không tìm thấy dữ liệu học sinh nào phù hợp
              </td>
            </tr>
            <tr v-for="(item, index) in students" :key="item.id" class="hover:bg-slate-50 transition-colors">
              <!-- STT -->
              <td class="text-center font-medium text-gray-500">
                {{ index + 1 + (pagination.cpage - 1) * pagination.limit }}
              </td>

              <!-- Avatar -->
              <td class="text-center">
                <router-link :to="`/lms/students/${item.id}/detail`">
                  <div class="avatar-wrapper">
                    <img class="student-avatar-img" :src="item.avatar_url || (item.gender == 'F' ? '/images/common/avatar-girl.svg' : '/images/common/avatar-boy.svg')" alt="avatar" />
                  </div>
                </router-link>
              </td>

              <!-- Học sinh -->
              <td>
                <div class="student-name-box">
                  <router-link :to="`/lms/students/${item.id}/detail`" class="student-title-link">
                    {{ item.name }}
                  </router-link>
                </div>
                <div class="mt-1 flex flex-wrap items-center gap-1">
                  <span class="stk-code-badge" @click="copyCode(item.lms_code)" title="Bấm để copy mã học sinh">
                    <i class="fa-solid fa-id-badge text-primary mr-1"></i>
                    <strong>{{ item.lms_code }}</strong>
                    <i class="fa-regular fa-copy ml-1 text-gray-400 copy-icon"></i>
                  </span>
                </div>
                <div class="mt-1 flex items-center text-xs text-gray-500 gap-3">
                  <span v-if="item.gender">
                    <i :class="item.gender == 'F' ? 'fa-solid fa-venus text-pink-500' : 'fa-solid fa-mars text-blue-500'"></i>
                    {{ item.gender == 'F' ? 'Nữ' : 'Nam' }}
                  </span>
                  <span v-if="item.date_of_birth">
                    <i class="fa-regular fa-calendar text-gray-400 mr-1"></i>{{ item.date_of_birth | formatDateView }}
                  </span>
                </div>
              </td>

              <!-- Phụ huynh -->
              <td>
                <div class="font-semibold text-gray-800 flex items-center">
                  <i class="fa-solid fa-user-tie text-gray-400 mr-1.5" style="font-size: 13px;"></i>
                  {{ item.gud_name1 || 'Chưa cập nhật' }}
                </div>
                <div class="mt-1 text-xs">
                  <a v-if="item.gud_mobile1" :href="'tel:' + item.gud_mobile1" class="text-primary hover:underline font-medium inline-flex items-center">
                    <i class="fa-solid fa-phone text-success mr-1"></i> {{ item.gud_mobile1 }}
                  </a>
                  <span v-else class="text-gray-400">Chưa có SĐT</span>
                </div>
                <div class="mt-1" v-if="item.source_name">
                  <span class="subtle-tag"><i class="fa-solid fa-bullhorn text-gray-400 mr-1"></i>{{ item.source_name }}</span>
                </div>
              </td>

              <!-- Trung tâm & Lớp học & Trạng thái -->
              <td>
                <div class="text-xs font-semibold text-gray-700 flex items-center mb-1">
                  <i class="fa-solid fa-building text-primary mr-1.5"></i>
                  {{ item.branch_name || '—' }}
                </div>
                <div class="text-xs text-gray-600 mb-1" v-if="item.class_name">
                  <span class="class-pill"><i class="fa-solid fa-chalkboard-user mr-1"></i>{{ item.class_name }}</span>
                </div>
                <div class="mt-1.5">
                  <span :class="getStatusBadgeClass(item.label_status)">
                    {{ item.label_status || 'Chưa có' }}
                  </span>
                </div>
                <div class="text-xs text-gray-400 mt-1 flex items-center gap-2" v-if="item.ec_name || item.cm_name">
                  <span v-if="item.ec_name" title="EC phụ trách"><i class="fa-solid fa-user-tag mr-0.5"></i>EC: {{ item.ec_name }}</span>
                  <span v-if="item.cm_name" title="CM phụ trách"><i class="fa-solid fa-headset mr-0.5"></i>CM: {{ item.cm_name }}</span>
                </div>
              </td>

              <!-- Tiến độ buổi học -->
              <td class="text-center">
                <div v-if="item.summary_sessions && item.summary_sessions > 0">
                  <div class="flex items-center justify-between text-xs font-semibold mb-1">
                    <span class="text-success font-bold text-sm">{{ item.done_sessions || 0 }}</span>
                    <span class="text-gray-400">/ {{ item.summary_sessions }} buổi</span>
                  </div>
                  <div class="progress-bar-container">
                    <div
                      class="progress-bar-fill"
                      :style="{ width: Math.min(100, Math.round(((item.done_sessions || 0) * 100) / item.summary_sessions)) + '%' }"
                    ></div>
                  </div>
                  <div class="text-xs text-gray-400 mt-1 text-right">
                    {{ Math.round(((item.done_sessions || 0) * 100) / item.summary_sessions) }}%
                  </div>
                </div>
                <div v-else class="text-xs text-gray-400 italic">
                  Chưa có lịch học
                </div>
              </td>

              <!-- Thao tác -->
              <td class="text-center">
                <router-link :to="`/lms/students/${item.id}/detail`">
                  <vs-button size="small" color="primary" type="relief" class="action-view-btn shadow-sm" title="Xem chi tiết hồ sơ">
                    <i class="fa-solid fa-eye mr-1"></i> Chi tiết
                  </vs-button>
                </router-link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="flex flex-wrap items-center justify-between mt-5 pt-3 border-t border-solid border-grey-light">
        <div class="flex items-center mb-2 sm:mb-0">
          <span class="text-sm text-gray-500 mr-2">Hiển thị mỗi trang:</span>
          <vs-dropdown vs-trigger-click class="cursor-pointer">
            <div class="px-3 py-1.5 border border-solid border-grey-light rounded-md bg-white flex items-center justify-between font-medium text-sm">
              <span class="mr-2">{{ pagination.limit }}</span>
              <feather-icon icon="ChevronDownIcon" svgClasses="h-4 w-4" />
            </div>
            <vs-dropdown-menu>
              <vs-dropdown-item v-for="(limit, idx) in limitSource" :key="idx" @click="changePageLimit(limit)">
                <span>{{ limit }}</span>
              </vs-dropdown-item>
            </vs-dropdown-menu>
          </vs-dropdown>
          <span class="text-xs text-gray-400 ml-3">
            ({{ pagination.cpage * pagination.limit - (pagination.limit - 1) }} - {{ Math.min(pagination.total, pagination.cpage * pagination.limit) }} / {{ pagination.total }})
          </span>
        </div>

        <vs-pagination
          v-if="Math.ceil(pagination.total / pagination.limit) > 1"
          :total="Math.ceil(pagination.total / pagination.limit)"
          :max="7"
          v-model="pagination.cpage"
          @change="changePage"
        />
      </div>
    </vx-card>
  </div>
</template>

<script>
import vSelect from 'vue-select'
import axios from '../../../http/axios.js'
import Multiselect from "vue-multiselect";
import DatePicker from "vue2-datepicker";
import u from '../../../until/helper.js'

export default {
  components: {
    vSelect,
    Multiselect,
    DatePicker
  },
  data() {
    return {
      branch_list: [],
      searchData: {
        arr_branch: "",
        branch_id: "",
        keyword: "",
        arr_status: "",
        status: "",
        pagination: this.pagination
      },
      statusOptions: [
        { id: 1, label: 'Chờ xếp lớp học thử' },
        { id: 2, label: 'Đang học thử' },
        { id: 3, label: 'Kết thúc học thử' },
        { id: 4, label: 'Chưa đóng phí' },
        { id: 5, label: 'Đặt cọc' },
        { id: 6, label: 'Chờ xếp lớp' },
        { id: 7, label: 'Đang học chính thức' },
        { id: 8, label: 'Hết phí' },
      ],
      students: [],
      limitSource: [20, 50, 100, 500],
      pagination: {
        url: "/api/lms/students/list",
        id: "",
        style: "line",
        class: "",
        spage: 1,
        ppage: 1,
        npage: 0,
        lpage: 1,
        cpage: 1,
        total: 0,
        limit: 20,
        pages: [],
        init: 0
      },
    }
  },
  created() {
    axios.g(`/api/system/branches-has-user`).then(response => {
      this.branch_list = response.data
    })
    this.getData();
  },
  methods: {
    reset() {
      this.searchData.keyword = ""
      this.searchData.arr_status = ""
      this.searchData.arr_branch = ""
      this.searchData.branch_id = ""
      this.searchData.status = ""
      this.searchData.pagination = this.pagination
      this.getData();
    },
    getData() {
      const ids = []
      if (this.searchData.arr_status && this.searchData.arr_status.length) {
        this.searchData.arr_status.map(item => {
          ids.push(item.id)
        })
      }
      this.searchData.status = ids

      const ids_branch = []
      if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
        this.searchData.arr_branch.map(item => {
          ids_branch.push(item.id)
        })
      }
      this.searchData.branch_id = ids_branch

      const data = {
        branch_id: this.searchData.branch_id,
        status: this.searchData.status,
        keyword: this.searchData.keyword,
        pagination: this.pagination
      }

      this.$vs.loading()
      axios.p('/api/lms/students/list', data)
        .then((response) => {
          this.$vs.loading.close()
          this.students = response.data.list
          this.total = response.data.detail_total
          this.pagination = response.data.paging;
          setTimeout(() => {
            this.pagination.init = 1;
          }, 500)
        })
        .catch((error) => {
          console.log(error);
          this.$vs.loading.close();
        })
    },
    changePage() {
      if (this.pagination.init) {
        this.getData();
      }
    },
    changePageLimit(limit) {
      this.pagination.cpage = 1
      this.pagination.limit = limit
      this.getData();
    },
    copyCode(code) {
      if (!code) return;
      navigator.clipboard.writeText(code).then(() => {
        this.$vs.notify({
          title: 'Thành công',
          text: `Đã sao chép mã học sinh: ${code}`,
          color: 'success',
          icon: 'check',
          position: 'top-right'
        });
      });
    },
    getStatusBadgeClass(status) {
      if (!status) return 'status-badge badge-default';
      const s = status.toLowerCase();
      if (s.includes('chính thức')) return 'status-badge badge-success';
      if (s.includes('đang học thử')) return 'status-badge badge-info';
      if (s.includes('chờ')) return 'status-badge badge-warning';
      if (s.includes('hết phí') || s.includes('chưa đóng')) return 'status-badge badge-danger';
      if (s.includes('đặt cọc')) return 'status-badge badge-primary';
      return 'status-badge badge-default';
    }
  }
}
</script>

<style scoped>
.filter-label {
  font-size: 13px;
  font-weight: 600;
  color: #475569;
  margin-bottom: 6px;
  display: block;
}

.badge-total-students {
  background: #eff6ff;
  color: #1d4ed8;
  border: 1px solid #bfdbfe;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 13px;
  display: inline-flex;
  align-items: center;
}

.table-responsive {
  width: 100%;
  overflow-x: auto;
}

.modern-student-table {
  width: 100%;
  border-collapse: collapse;
}

.modern-student-table thead tr {
  background: #f8fafc;
  border-bottom: 2px solid #e2e8f0;
}

.modern-student-table th {
  padding: 12px 14px;
  font-size: 12px;
  font-weight: 700;
  color: #475569;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.modern-student-table td {
  padding: 14px;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
  font-size: 13px;
}

.avatar-wrapper {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  overflow: hidden;
  background: #f1f5f9;
  border: 2px solid #e2e8f0;
  margin: 0 auto;
  box-shadow: 0 1px 3px rgba(0,0,0,0.08);
  transition: transform 0.2s;
}

.avatar-wrapper:hover {
  transform: scale(1.08);
  border-color: #3b82f6;
}

.student-avatar-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.student-name-box {
  line-height: 1.3;
}

.student-title-link {
  font-weight: 700;
  font-size: 14px;
  color: #0f172a;
  text-decoration: none;
  transition: color 0.15s;
}

.student-title-link:hover {
  color: #2563eb;
}

.stk-code-badge {
  display: inline-flex;
  align-items: center;
  background: #f8fafc;
  border: 1px dashed #cbd5e1;
  color: #334155;
  padding: 2px 7px;
  border-radius: 4px;
  font-size: 11.5px;
  cursor: pointer;
  transition: all 0.2s;
}

.stk-code-badge:hover {
  background: #eff6ff;
  border-color: #3b82f6;
  color: #1d4ed8;
}

.subtle-tag {
  background: #f1f5f9;
  color: #64748b;
  padding: 2px 8px;
  border-radius: 12px;
  font-size: 11px;
  display: inline-block;
}

.class-pill {
  background: #f0fdf4;
  color: #15803d;
  border: 1px solid #bbf7d0;
  padding: 2px 8px;
  border-radius: 12px;
  font-size: 11.5px;
  font-weight: 600;
  display: inline-block;
}

.status-badge {
  display: inline-block;
  padding: 3px 10px;
  border-radius: 12px;
  font-size: 11.5px;
  font-weight: 600;
  line-height: 1.2;
}

.badge-success {
  background: #dcfce7;
  color: #166534;
}

.badge-info {
  background: #e0f2fe;
  color: #0369a1;
}

.badge-warning {
  background: #fef3c7;
  color: #b45309;
}

.badge-danger {
  background: #ffe4e6;
  color: #be123c;
}

.badge-primary {
  background: #f3e8ff;
  color: #7e22ce;
}

.badge-default {
  background: #f1f5f9;
  color: #475569;
}

.progress-bar-container {
  width: 100%;
  height: 7px;
  background: #e2e8f0;
  border-radius: 4px;
  overflow: hidden;
}

.progress-bar-fill {
  height: 100%;
  background: linear-gradient(90deg, #10b981, #059669);
  border-radius: 4px;
  transition: width 0.3s ease;
}

.action-view-btn {
  font-size: 12px;
  padding: 6px 12px !important;
  border-radius: 6px;
}
</style>
