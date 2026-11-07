<template>
  <div id="page-students-list">
    <!-- Header Banner -->
    <div class="flex items-center justify-between mb-3 mt-1">
      <div>
        <h4 class="font-bold text-gray-800 flex items-center text-lg m-0">
          <i class="fa-solid fa-user-graduate text-primary mr-2" style="font-size: 20px;"></i>
          Danh Sách Học Sinh
        </h4>
        <span class="text-xs text-gray-500">Quản lý hồ sơ học sinh, tiến độ buổi học và thông tin lớp học</span>
      </div>
      <div>
        <span class="badge-total-students">
          <i class="fa-solid fa-users mr-1"></i>
          Tổng cộng: <strong>{{ pagination.total }}</strong> học sinh
        </span>
      </div>
    </div>

    <!-- Filter Card -->
    <vx-card no-shadow class="mb-4 shadow-sm border border-solid border-grey-light rounded-lg">
      <div class="vx-row">
        <!-- Trung tâm -->
        <div class="vx-col md:w-1/3 sm:w-1/2 w-full mb-3">
          <label class="filter-label"><i class="fa-solid fa-location-dot text-primary mr-1"></i> Trung tâm</label>
          <multiselect
            class="custom-multiselect"
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
            <span slot="noResult">Không tìm thấy</span>
          </multiselect>
        </div>

        <!-- Tìm kiếm -->
        <div class="vx-col md:w-1/3 sm:w-1/2 w-full mb-3">
          <label class="filter-label"><i class="fa-solid fa-magnifying-glass text-primary mr-1"></i> Tìm kiếm</label>
          <div class="filter-search-wrapper">
            <i class="fa-solid fa-search filter-search-icon"></i>
            <input
              type="text"
              class="filter-input-text"
              placeholder="Mã HS, Tên học sinh, số điện thoại..."
              v-model="searchData.keyword"
              @keyup.enter="getData"
            />
          </div>
        </div>

        <!-- Trạng thái -->
        <div class="vx-col md:w-1/3 sm:w-1/2 w-full mb-3">
          <label class="filter-label"><i class="fa-solid fa-tags text-primary mr-1"></i> Trạng thái học sinh</label>
          <multiselect
            class="custom-multiselect"
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
            <span slot="noResult">Không tìm thấy</span>
          </multiselect>
        </div>
      </div>

      <!-- Action Buttons Row (Below inputs like Checkin) -->
      <div class="filter-actions-bar">
        <button class="btn-filter-action btn-filter-submit" @click="getData" title="Tìm kiếm">
          <i class="fa fa-search mr-1.5"></i> Tìm kiếm
        </button>
        <button class="btn-filter-action btn-filter-reset" @click="reset" title="Đặt lại bộ lọc">
          <i class="fas fa-undo-alt mr-1.5"></i> Đặt lại
        </button>
      </div>
    </vx-card>

    <!-- Table Card -->
    <vx-card no-shadow class="shadow-sm border border-solid border-grey-light rounded-lg compact-table-card">
      <div class="table-responsive">
        <table class="modern-student-table">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px">STT</th>
              <th style="width: 60px" class="text-center">Avatar</th>
              <th style="width: 250px">Học sinh</th>
              <th style="width: 220px">Phụ huynh</th>
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

            <tr v-for="(item, index) in students" :key="index" class="student-row hover:bg-slate-50 transition-colors">
              <!-- STT -->
              <td class="text-center text-gray-500 font-semibold text-xs">
                {{ index + 1 + (pagination.cpage - 1) * pagination.limit }}
              </td>

              <!-- Avatar -->
              <td class="text-center">
                <div class="avatar-cell">
                  <img
                    class="student-avatar-img"
                    :src="item.avatar_url || (item.gender == 'F' ? '/images/common/avatar-girl.svg' : '/images/common/avatar-boy.svg')"
                    :alt="item.name"
                  />
                </div>
              </td>

              <!-- Học sinh -->
              <td>
                <div class="student-name-text font-bold text-gray-900 mb-1 flex items-center">
                  <router-link :to="`/lms/students/${item.id}/detail`" class="hover:text-primary transition-colors">
                    {{ item.name }}
                  </router-link>
                </div>
                <div class="flex items-center gap-2 mb-1">
                  <!-- Clickable STK Code Badge -->
                  <span
                    class="stk-code-badge"
                    @click="copyCode(item.lms_code)"
                    title="Bấm để sao chép mã học sinh"
                  >
                    <i class="fa-solid fa-id-badge mr-1"></i>
                    <strong>{{ item.lms_code || '---' }}</strong>
                    <i class="fa-regular fa-copy ml-1 opacity-70"></i>
                  </span>

                  <!-- Gender & Dob -->
                  <span v-if="item.gender" class="gender-pill">
                    <i :class="item.gender == 'F' ? 'fa-solid fa-venus text-pink-500' : 'fa-solid fa-mars text-blue-500'"></i>
                    {{ item.gender == 'F' ? 'Nữ' : 'Nam' }}
                  </span>
                </div>
                <div class="text-xs text-gray-500" v-if="item.date_of_birth">
                  <i class="fa-regular fa-calendar mr-1 text-gray-400"></i>
                  {{ item.date_of_birth | formatDateView }}
                </div>
              </td>

              <!-- Phụ huynh -->
              <td>
                <div class="text-xs font-semibold text-gray-800 flex items-center mb-1">
                  <i class="fa-solid fa-user-tie text-gray-400 mr-1.5"></i>
                  {{ item.gud_name1 || 'Chưa cập nhật' }}
                </div>
                <div class="text-xs text-gray-600 mb-1" v-if="item.gud_mobile1">
                  <i class="fa-solid fa-phone text-emerald-500 mr-1.5"></i>
                  <a :href="'tel:' + item.gud_mobile1" class="text-primary hover:underline font-semibold">{{ item.gud_mobile1 }}</a>
                </div>
                <div class="text-xs text-gray-500 truncate" v-if="item.address" :title="item.address">
                  <i class="fa-solid fa-location-dot text-rose-400 mr-1.5"></i>
                  {{ item.address }}
                </div>
              </td>

              <!-- Lớp học & Trung tâm -->
              <td>
                <div class="text-xs font-semibold text-gray-700 flex items-center mb-1">
                  <i class="fa-solid fa-building text-primary mr-1.5"></i>
                  {{ item.branch_name || '—' }}
                </div>
                <div class="text-xs text-gray-600 mb-1" v-if="item.class_name">
                  <span class="class-pill"><i class="fa-solid fa-chalkboard-user mr-1"></i>{{ item.class_name }}</span>
                </div>
                <div class="mt-1">
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

      <!-- Compact Pagination -->
      <div class="compact-pagination-bar">
        <div class="flex items-center">
          <span class="text-xs text-gray-500 mr-2">Hiển thị mỗi trang:</span>
          <select class="compact-page-select" v-model="pagination.limit" @change="changePageLimit(pagination.limit)">
            <option v-for="(limit, idx) in limitSource" :key="idx" :value="limit">{{ limit }}</option>
          </select>
          <span class="text-xs text-gray-400 ml-2">
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
    Multiselect,
    DatePicker,
    vSelect
  },
  data() {
    return {
      datepickerOptions: {
        closed: true,
        value: "",
        minDate: "",
        lang: {
          days: ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
          months: [
            "Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6",
            "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"
          ]
        }
      },
      statusOptions: [
        { id: 1, label: "Chờ xếp lớp học thử" },
        { id: 2, label: "Đang học thử" },
        { id: 3, label: "Chờ xếp lớp chính thức" },
        { id: 4, label: "Đang học chính thức" },
        { id: 5, label: "Bảo lưu" },
        { id: 6, label: "Đình chỉ" },
        { id: 7, label: "Bỏ học" },
        { id: 8, label: "Hết phí" },
        { id: 9, label: "Rút phí" },
        { id: 10, label: "Tốt nghiệp" },
      ],
      searchData: {
        keyword: "",
        arr_branch: [],
        arr_status: [],
      },
      branch_list: [],
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
  async created() {
    axios.g(`/api/system/branches-has-user`).then(response => {
      this.branch_list = response.data
    })
    this.getData();
  },
  methods: {
    copyCode(code) {
      if (!code) return;
      navigator.clipboard.writeText(code).then(() => {
        this.$vs.notify({
          title: 'Đã sao chép',
          text: `Mã học sinh "${code}" đã được lưu vào clipboard`,
          color: 'success',
          iconPack: 'feather',
          icon: 'icon-check',
          time: 2500
        });
      });
    },
    getStatusBadgeClass(status) {
      if (!status) return 'status-badge status-default';
      const s = status.toLowerCase();
      if (s.includes('chính thức')) return 'status-badge status-official';
      if (s.includes('học thử')) return 'status-badge status-trial';
      if (s.includes('chờ xếp')) return 'status-badge status-waiting';
      if (s.includes('bảo lưu')) return 'status-badge status-paused';
      if (s.includes('bỏ học') || s.includes('đình chỉ') || s.includes('hết phí')) return 'status-badge status-inactive';
      return 'status-badge status-default';
    },
    reset() {
      this.searchData.keyword = "";
      this.searchData.arr_branch = [];
      this.searchData.arr_status = [];
      this.pagination.cpage = 1;
      this.getData();
    },
    changePageLimit(limit) {
      this.pagination.limit = limit;
      this.pagination.cpage = 1;
      this.getData();
    },
    changePage() {
      this.getData();
    },
    getData() {
      const data = {
        keyword: this.searchData.keyword,
        arr_branch: this.searchData.arr_branch.map(item => item.id),
        arr_status: this.searchData.arr_status.map(item => item.id),
        pagination: this.pagination,
      };
      this.$vs.loading();
      axios.p(this.pagination.url, data)
        .then((response) => {
          this.$vs.loading.close();
          this.students = response.data.list;
          this.pagination.total = response.data.total;
        })
        .catch((error) => {
          console.log(error);
          this.$vs.loading.close();
        });
    },
  }
}
</script>

<style scoped>
.badge-total-students {
  display: inline-flex;
  align-items: center;
  background-color: #eff6ff;
  color: #1d4ed8;
  font-size: 13px;
  padding: 5px 14px;
  border-radius: 9999px;
  border: 1px solid #bfdbfe;
}

.filter-label {
  font-size: 13px;
  font-weight: 600;
  color: #334155;
  margin-bottom: 6px;
  display: block;
}

/* Custom Multiselect - Exactly 38px height, 13.5px font */
.custom-multiselect >>> .multiselect {
  min-height: 38px !important;
}

.custom-multiselect >>> .multiselect__tags {
  box-sizing: border-box !important;
  min-height: 38px !important;
  height: 38px !important;
  padding: 6px 32px 6px 12px !important;
  border: 1px solid #cbd5e1 !important;
  border-radius: 6px !important;
  font-size: 13.5px !important;
  background: #ffffff !important;
  display: flex !important;
  align-items: center !important;
}

.custom-multiselect >>> .multiselect__select {
  height: 36px !important;
  width: 30px !important;
  padding: 4px 8px !important;
}

.custom-multiselect >>> .multiselect__placeholder {
  margin-bottom: 0 !important;
  padding-top: 0 !important;
  line-height: 24px !important;
  font-size: 13.5px !important;
  color: #94a3b8 !important;
}

.custom-multiselect >>> .multiselect__single {
  line-height: 24px !important;
  font-size: 13.5px !important;
  margin-bottom: 0 !important;
}

.custom-multiselect >>> .multiselect__input {
  line-height: 24px !important;
  font-size: 13.5px !important;
  margin-bottom: 0 !important;
  height: 24px !important;
}

.custom-multiselect >>> .multiselect__tag {
  height: 24px !important;
  line-height: 24px !important;
  padding: 0 20px 0 8px !important;
  font-size: 12px !important;
  margin-bottom: 2px !important;
  margin-top: 2px !important;
}

.custom-multiselect >>> .multiselect__tag-icon {
  line-height: 24px !important;
}

/* Custom Search Text Input - Exactly 38px height, 13.5px font */
.filter-search-wrapper {
  position: relative;
  display: flex;
  align-items: center;
  width: 100%;
}

.filter-search-icon {
  position: absolute;
  left: 12px;
  color: #94a3b8;
  font-size: 13.5px;
  pointer-events: none;
}

.filter-input-text {
  box-sizing: border-box !important;
  width: 100% !important;
  height: 38px !important;
  border: 1px solid #cbd5e1 !important;
  border-radius: 6px !important;
  padding: 6px 12px 6px 34px !important;
  font-size: 13.5px !important;
  color: #1e293b !important;
  outline: none !important;
  background: #ffffff !important;
  transition: all 0.2s ease !important;
}

.filter-input-text:focus {
  border-color: #7367f0 !important;
  box-shadow: 0 0 0 2px rgba(115, 103, 240, 0.15) !important;
}

/* Action Buttons Bar - Positioned below inputs, aligned right */
.filter-actions-bar {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 10px;
  padding-top: 10px;
  border-top: 1px solid #f1f5f9;
}

.btn-filter-action {
  height: 38px;
  padding: 0 18px;
  font-size: 13.5px;
  font-weight: 600;
  border-radius: 6px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  white-space: nowrap;
}

.btn-filter-submit {
  background: #7367f0;
  color: #ffffff;
  border: 1px solid #7367f0;
}

.btn-filter-submit:hover {
  background: #5e50ee;
  box-shadow: 0 2px 6px rgba(115, 103, 240, 0.3);
}

.btn-filter-reset {
  background: #ffffff;
  color: #64748b;
  border: 1px solid #cbd5e1;
}

.btn-filter-reset:hover {
  background: #f8fafc;
  color: #334155;
  border-color: #94a3b8;
}

/* Modern Student Table - Readable fonts */
.modern-student-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  font-size: 13.5px;
}

.modern-student-table thead th {
  background: #f8fafc;
  color: #475569;
  font-weight: 700;
  font-size: 12.5px;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  padding: 12px 14px;
  border-bottom: 2px solid #e2e8f0;
  white-space: nowrap;
}

.modern-student-table tbody td {
  padding: 12px 14px;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
}

.student-row:hover {
  background-color: #f8fafc;
}

.avatar-cell {
  display: flex;
  justify-content: center;
}

.student-avatar-img {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #e2e8f0;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
}

.student-name-text {
  font-size: 14.5px;
  font-weight: 700;
}

.stk-code-badge {
  display: inline-flex;
  align-items: center;
  font-size: 12px;
  font-weight: 700;
  color: #7367f0;
  background-color: #f5f3ff;
  border: 1px solid #ddd6fe;
  padding: 2px 7px;
  border-radius: 5px;
  cursor: pointer;
}

.gender-pill {
  display: inline-flex;
  align-items: center;
  gap: 3px;
  font-size: 12px;
  color: #64748b;
  background: #f1f5f9;
  padding: 2px 7px;
  border-radius: 4px;
}

.class-pill {
  display: inline-flex;
  align-items: center;
  background: #eef2ff;
  color: #4f46e5;
  font-size: 12px;
  font-weight: 600;
  padding: 2px 8px;
  border-radius: 4px;
}

.status-badge {
  display: inline-flex;
  align-items: center;
  font-size: 11.5px;
  font-weight: 700;
  padding: 3px 10px;
  border-radius: 9999px;
  white-space: nowrap;
}

.status-official { background: #dcfce7; color: #15803d; }
.status-trial { background: #e0f2fe; color: #0369a1; }
.status-waiting { background: #fef9c3; color: #a16207; }
.status-paused { background: #ffedd5; color: #c2410c; }
.status-inactive { background: #fee2e2; color: #b91c1c; }
.status-default { background: #f1f5f9; color: #475569; }

.progress-bar-container {
  width: 100%;
  height: 6px;
  background-color: #e2e8f0;
  border-radius: 9999px;
  overflow: hidden;
}

.progress-bar-fill {
  height: 100%;
  background: linear-gradient(90deg, #7367f0, #28c76f);
  border-radius: 9999px;
}

.action-view-btn {
  font-size: 12px !important;
  font-weight: 600 !important;
  padding: 5px 12px !important;
}

/* Compact Pagination Bar */
.compact-pagination-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 14px;
  padding-top: 12px;
  border-top: 1px solid #e2e8f0;
}

.compact-page-select {
  height: 30px;
  padding: 2px 8px;
  border: 1px solid #cbd5e1;
  border-radius: 4px;
  font-size: 13px;
  background: #ffffff;
  color: #334155;
  outline: none;
}
</style>
