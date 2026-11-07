<template>
  <div id="page-checkin-list">
    <!-- Header Banner -->
    <div class="flex flex-wrap items-center justify-between mb-4 mt-2">
      <div>
        <h4 class="font-bold text-gray-800 flex items-center">
          <i class="fa-solid fa-clipboard-user text-primary mr-2" style="font-size: 24px;"></i>
          Danh Sách Checkin Học Sinh
        </h4>
        <p class="text-sm text-gray-500 mt-1">Quản lý tiếp nhận học sinh đến cơ sở, trải nghiệm học thử và chuyển lên chính thức</p>
      </div>
      <div class="flex items-center gap-3 mt-2 sm:mt-0">
        <span class="badge-total-checkin">
          <i class="fa-solid fa-list-check mr-1"></i>
          Tổng checkin: <strong>{{ pagination.total }}</strong>
        </span>
        <router-link :to="'/lms/checkin/add'">
          <vs-button color="success" class="flex items-center shadow-md">
            <i class="fa fa-plus mr-1.5"></i> Thêm mới Checkin
          </vs-button>
        </router-link>
      </div>
    </div>

    <!-- Filter Card - Compact & Balanced -->
    <vx-card no-shadow class="mb-4 shadow-sm border border-solid border-grey-light rounded-lg compact-filter-card">
      <div class="filter-grid-checkin">
        <!-- Trung tâm -->
        <div class="filter-item">
          <label class="filter-label"><i class="fa-solid fa-building text-primary mr-1"></i> Trung tâm checkin</label>
          <multiselect
            class="compact-multiselect"
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
        <div class="filter-item">
          <label class="filter-label"><i class="fa-solid fa-magnifying-glass text-primary mr-1"></i> Tìm kiếm</label>
          <div class="compact-search-input-wrapper">
            <i class="fa-solid fa-search input-search-icon"></i>
            <input
              type="text"
              class="compact-text-input"
              placeholder="Tên học sinh, phụ huynh, SĐT..."
              v-model="searchData.keyword"
              @keyup.enter="getData"
            />
          </div>
        </div>

        <!-- Trạng thái -->
        <div class="filter-item">
          <label class="filter-label"><i class="fa-solid fa-tags text-primary mr-1"></i> Trạng thái</label>
          <multiselect
            class="compact-multiselect"
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

        <!-- Người phụ trách (EC) -->
        <div class="filter-item">
          <label class="filter-label"><i class="fa-solid fa-user-tie text-primary mr-1"></i> Người phụ trách (EC)</label>
          <multiselect
            class="compact-multiselect"
            name="search_owner"
            placeholder="Tất cả nhân viên"
            v-model="searchData.arr_creator"
            :options="users_list"
            label="label_name"
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

        <!-- Nguồn khách hàng -->
        <div class="filter-item">
          <label class="filter-label"><i class="fa-solid fa-bullhorn text-primary mr-1"></i> Nguồn khách hàng</label>
          <multiselect
            class="compact-multiselect"
            name="search_source"
            placeholder="Tất cả nguồn"
            v-model="searchData.arr_source"
            :options="source_list"
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

        <!-- Thời gian checkin -->
        <div class="filter-item">
          <label class="filter-label"><i class="fa-regular fa-calendar-days text-primary mr-1"></i> Thời gian checkin</label>
          <date-picker
            class="compact-datepicker w-full"
            name="item-date"
            v-model="searchData.dateRange"
            range
            format="YYYY-MM-DD"
            style="width: 100%"
            :clearable="true"
            :lang="datepickerOptions.lang"
            placeholder="Chọn khoảng thời gian"
          ></date-picker>
        </div>

        <!-- Action Buttons - span across or bottom right -->
        <div class="filter-actions-checkin">
          <button class="btn-filter-action btn-filter-submit" @click="getData" title="Tìm kiếm">
            <i class="fa fa-search mr-1"></i> Tìm kiếm
          </button>
          <button class="btn-filter-action btn-filter-reset" @click="reset" title="Đặt lại">
            <i class="fas fa-undo-alt mr-1"></i> Đặt lại
          </button>
        </div>
      </div>
    </vx-card>

    <!-- Table Card -->
    <vx-card no-shadow class="shadow-sm border border-solid border-grey-light rounded-lg">
      <div class="table-responsive">
        <table class="modern-checkin-table">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px">STT</th>
              <th style="width: 230px">Học sinh</th>
              <th style="width: 230px">Phụ huynh</th>
              <th style="width: 240px">Thông tin Checkin</th>
              <th style="width: 180px" class="text-center">Trạng thái</th>
              <th style="width: 140px" class="text-center">Thao tác</th>
              <th style="min-width: 180px">Ghi chú</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="!checkin_students || checkin_students.length === 0">
              <td colspan="7" class="text-center py-8 text-gray-500">
                <i class="fa-solid fa-folder-open text-gray-300 mb-2" style="font-size: 36px; display: block;"></i>
                Không tìm thấy lượt checkin nào phù hợp
              </td>
            </tr>
            <tr v-for="(item, index) in checkin_students" :key="item.id" class="hover:bg-slate-50 transition-colors">
              <!-- STT -->
              <td class="text-center font-medium text-gray-500">
                {{ index + 1 + (pagination.cpage - 1) * pagination.limit }}
              </td>

              <!-- Học sinh -->
              <td>
                <div class="font-bold text-gray-900 text-sm">
                  {{ item.name }}
                </div>
                <div class="mt-1 flex items-center text-xs text-gray-500 gap-2">
                  <span v-if="item.gender">
                    <i :class="item.gender == 'F' ? 'fa-solid fa-venus text-pink-500' : 'fa-solid fa-mars text-blue-500'"></i>
                    {{ item.gender == 'F' ? 'Nữ' : 'Nam' }}
                  </span>
                  <span v-if="item.birthday">
                    <i class="fa-regular fa-calendar text-gray-400 mr-1"></i>{{ item.birthday }}
                  </span>
                </div>
              </td>

              <!-- Phụ huynh -->
              <td>
                <div class="font-semibold text-gray-800 flex items-center">
                  <i class="fa-solid fa-user text-gray-400 mr-1.5" style="font-size: 13px;"></i>
                  {{ item.parent_name }}
                </div>
                <div class="mt-1 text-xs">
                  <a v-if="item.mobile_1" :href="'tel:' + item.mobile_1" class="text-primary hover:underline font-medium inline-flex items-center">
                    <i class="fa-solid fa-phone text-success mr-1"></i> {{ item.mobile_1 }}
                  </a>
                  <span v-else class="text-gray-400">Chưa có SĐT</span>
                </div>
                <div class="mt-1 flex flex-wrap items-center gap-1">
                  <span class="subtle-source-tag" v-if="item.source_name">
                    <i class="fa-solid fa-bullhorn text-gray-400 mr-1"></i>{{ item.source_name }}
                  </span>
                  <span class="subtle-ec-tag" v-if="item.checkin_owner_name" title="EC phụ trách">
                    <i class="fa-solid fa-user-tag mr-1"></i>{{ item.checkin_owner_name }}
                  </span>
                </div>
              </td>

              <!-- Thông tin Checkin -->
              <td>
                <div class="text-xs font-semibold text-gray-800 flex items-center mb-1">
                  <i class="fa-solid fa-location-dot text-primary mr-1.5"></i>
                  {{ item.checkin_branch_name }}
                </div>
                <div class="text-xs text-gray-600 mb-1" v-if="item.checkin_at">
                  <i class="fa-regular fa-clock text-gray-400 mr-1"></i>{{ item.checkin_at }}
                </div>
                <div class="mt-1" v-if="item.checkin_product_name">
                  <span class="course-pill"><i class="fa-solid fa-book-open mr-1"></i>{{ item.checkin_product_name }}</span>
                </div>
              </td>

              <!-- Trạng thái -->
              <td class="text-center">
                <span :class="getStatusBadgeClass(item.status)">
                  <i :class="getStatusIconClass(item.status)" class="mr-1"></i>
                  {{ item.status | getStatusName }}
                </span>
              </td>

              <!-- Thao tác -->
              <td class="text-center">
                <vs-button
                  v-if="item.status == 1 || item.status == 2"
                  size="small"
                  color="success"
                  type="relief"
                  class="action-promote-btn shadow-sm"
                  @click="openConfirmUpStudent(item.id, item.name)"
                  title="Chuyển học sinh này lên danh sách chính thức"
                >
                  <i class="fa-solid fa-user-check mr-1"></i> Lên chính thức
                </vs-button>
                <span v-else class="done-tag">
                  <i class="fa-solid fa-check-double mr-1"></i> Đã kích hoạt
                </span>
              </td>

              <!-- Ghi chú -->
              <td>
                <div class="note-box" v-if="item.checkined_note">
                  <i class="fa-solid fa-comment-dots text-gray-400 mr-1"></i>
                  {{ item.checkined_note }}
                </div>
                <span v-else class="text-gray-400 text-xs italic">—</span>
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

    <!-- Modal Checkin (Nếu dùng) -->
    <vs-popup :class="'modal_' + modal_checkin.color" :title="modal_checkin.title" :active.sync="modal_checkin.show">
      <div class="vx-row">
        <div class="vx-col w-full mb-4">
          <label class="filter-label">Thời gian đến checkin <span class="text-danger">(*)</span></label>
          <date-picker
            id="checkin-at"
            :value="modal_checkin.checkined_at"
            v-model="modal_checkin.checkined_at"
            placeholder="Chọn ngày giờ"
            :lang="datepickerOptions.lang"
            type="datetime"
            format="YYYY-MM-DD HH:mm"
            style="width: 100%"
          ></date-picker>
        </div>
        <div class="vx-col w-full mb-4">
          <label class="filter-label">Ghi chú <span class="text-danger">(*)</span></label>
          <textarea class="vs-inputx vs-input--input normal" v-model="modal_checkin.note" rows="3"></textarea>
        </div>
        <vs-alert :active.sync="modal_checkin.alert.active" class="mb-5 mr-4 ml-4" :color="modal_checkin.alert.color" closable icon-pack="feather" close-icon="icon-x">
          <div v-html="modal_checkin.alert.body"></div>
        </vs-alert>
        <div class="vx-col w-full flex justify-end">
          <vs-button color="dark" type="border" class="mr-3" @click="modal_checkin.show = false">Hủy</vs-button>
          <vs-button color="success" @click="updateCheckin">Lưu xác nhận</vs-button>
        </div>
      </div>
    </vs-popup>
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
      modal_checkin: {
        title: "XÁC NHẬN CHECKIN",
        show: false,
        color: "info",
        closeOnBackdrop: false,
        size: "lg",
        error_message: "",
        alert: {
          active: false,
          body: '',
          color: '',
        },
        student_id: '',
        checkined_at: '',
        note: ''
      },
      branch_list: [],
      searchData: {
        arr_branch: "",
        branch_id: "",
        keyword: "",
        arr_status: "",
        arr_creator: "",
        arr_source: "",
        status: "",
        checkin_owner_id: "",
        source_id: "",
        pagination: this.pagination,
        dateRange: "",
      },
      statusOptions: [
        { id: 1, label: 'Chưa đến checkin' },
        { id: 2, label: 'Đã checkin' },
        { id: 3, label: 'Đã lên chính thức' },
      ],
      users_list: [],
      source_list: [],
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
      checkin_students: [],
      limitSource: [20, 50, 100, 500],
      pagination: {
        url: "/api/roles/list",
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
      checkin_owner_id: "",
      up_student_id: "",
    }
  },
  created() {
    axios.g(`/api/system/branches-has-user`).then(response => {
      this.branch_list = response.data
    })
    axios.g(`/api/users/get-data/all`).then(response => {
      this.users_list = response.data
    })
    axios.g(`/api/system/sources`).then(response => {
      this.source_list = response.data
    })
    this.getData();
  },
  methods: {
    reset() {
      this.searchData.keyword = ""
      this.searchData.arr_status = ""
      this.searchData.arr_creator = ""
      this.searchData.arr_source = ""
      this.searchData.arr_branch = ""
      this.searchData.branch_id = ""
      this.searchData.status = ""
      this.searchData.checkin_owner_id = ""
      this.searchData.source_id = ""
      this.searchData.pagination = this.pagination
      this.searchData.dateRange = ""
      this.getData();
    },
    getData() {
      const startDate = typeof this.searchData.dateRange != 'undefined' && this.searchData.dateRange != '' && this.searchData.dateRange[0] ? `${u.dateToString(this.searchData.dateRange[0])}` : ''
      const endDate = typeof this.searchData.dateRange != 'undefined' && this.searchData.dateRange != '' && this.searchData.dateRange[1] ? `${u.dateToString(this.searchData.dateRange[1])}` : ''
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

      const ids_creator = []
      if (this.searchData.arr_creator && this.searchData.arr_creator.length) {
        this.searchData.arr_creator.map(item => {
          ids_creator.push(item.id)
        })
      }
      this.searchData.checkin_owner_id = ids_creator

      const ids_source = []
      if (this.searchData.arr_source && this.searchData.arr_source.length) {
        this.searchData.arr_source.map(item => {
          ids_source.push(item.id)
        })
      }
      this.searchData.source_id = ids_source

      const data = {
        keyword: this.searchData.keyword,
        checkin_branch_id: this.searchData.branch_id,
        status: this.searchData.status,
        checkin_owner_id: this.searchData.checkin_owner_id,
        source_id: this.searchData.source_id,
        start_date: startDate,
        end_date: endDate,
        pagination: this.pagination,
        type_search: this.searchData.type_search,
      }

      this.$vs.loading()
      axios.p('/api/lms/checkin/list', data)
        .then((response) => {
          this.$vs.loading.close()
          this.checkin_students = response.data.list
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
    openConfirmUpStudent(id, name) {
      this.up_student_id = id
      this.$vs.dialog({
        type: 'confirm',
        color: 'primary',
        title: 'Xác nhận chuyển lên chính thức',
        text: `Bạn có chắc chắn muốn chuyển học sinh "${name}" lên danh sách học sinh chính thức?`,
        accept: this.upStudent,
        acceptText: 'Đồng ý',
        cancelText: 'Hủy'
      })
    },
    upStudent() {
      const data = {
        crm_student_id: this.up_student_id,
      };
      this.$vs.loading();
      axios.p(`/api/lms/checkin/upgrade`, data)
        .then((response) => {
          this.$vs.loading.close();
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          this.$router.push(`/lms/students/${response.data.data.lms_student_id}/detail`)
        })
        .catch(() => {
          this.$vs.loading.close();
        });
    },
    showModalCheckin(item) {
      this.modal_checkin.show = true
      this.modal_checkin.error_message = ""
      this.modal_checkin.student_id = item.id
      this.modal_checkin.note = ""
      this.modal_checkin.checkined_at = ""
      this.modal_checkin.crm_student_checkin_id = item.crm_student_checkin_id
    },
    updateCheckin() {
      let mess = "";
      let resp = true;
      if (this.modal_checkin.checkined_at == "") {
        mess += " - Thời gian đến checkin không được để trống<br/>";
        resp = false;
      }
      if (this.modal_checkin.note == "") {
        mess += " - Ghi chú không được để trống<br/>";
        resp = false;
      }
      if (!resp) {
        this.modal_checkin.alert.color = 'danger'
        this.modal_checkin.alert.body = mess;
        this.modal_checkin.alert.active = true;
        return false;
      }
      this.$vs.loading();
      this.modal_checkin.show = false
      axios.p(`/api/lms/checkin/student/checked`, {
        student_id: this.modal_checkin.student_id,
        note: this.modal_checkin.note,
        checkined_at: this.modal_checkin.checkined_at,
        crm_student_checkin_id: this.modal_checkin.crm_student_checkin_id
      })
        .then((response) => {
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          this.$vs.loading.close();
          this.getData();
        })
        .catch(() => {
          this.$vs.loading.close();
        });
    },
    getStatusBadgeClass(val) {
      const v = Number(val);
      if (v === 3) return 'status-badge badge-success';
      if (v === 2) return 'status-badge badge-info';
      return 'status-badge badge-warning';
    },
    getStatusIconClass(val) {
      const v = Number(val);
      if (v === 3) return 'fa-solid fa-circle-check';
      if (v === 2) return 'fa-solid fa-clipboard-check';
      return 'fa-regular fa-clock';
    }
  },
  filters: {
    getStatusName(value) {
      let resp = ''
      switch (Number(value)) {
        case 2:
          resp = 'Đã checkin';
          break;
        case 3:
          resp = 'Đã lên chính thức';
          break;
        default:
          resp = 'Chưa đến checkin'
          break
      }
      return resp
    },
  }
}
</script>

<style scoped>
.compact-filter-card >>> .vx-card__collapsible-content .vx-card__body {
  padding: 12px 16px !important;
}

.filter-grid-checkin {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
  align-items: flex-end;
}

@media (max-width: 991px) {
  .filter-grid-checkin {
    grid-template-columns: 1fr 1fr;
  }
}

@media (max-width: 640px) {
  .filter-grid-checkin {
    grid-template-columns: 1fr;
  }
}

.filter-item {
  display: flex;
  flex-direction: column;
}

.filter-label {
  font-size: 13px;
  font-weight: 600;
  color: #334155;
  margin-bottom: 6px;
  display: block;
}

/* Custom Multiselect - Exactly 38px */
.compact-multiselect >>> .multiselect {
  min-height: 38px !important;
}

.compact-multiselect >>> .multiselect__tags {
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

.compact-multiselect >>> .multiselect__select {
  height: 36px !important;
  width: 30px !important;
  padding: 4px 8px !important;
}

.compact-multiselect >>> .multiselect__placeholder {
  margin-bottom: 0 !important;
  padding-top: 0 !important;
  line-height: 24px !important;
  font-size: 13.5px !important;
  color: #94a3b8 !important;
}

.compact-multiselect >>> .multiselect__single {
  line-height: 24px !important;
  font-size: 13.5px !important;
  margin-bottom: 0 !important;
}

.compact-multiselect >>> .multiselect__input {
  line-height: 24px !important;
  font-size: 13.5px !important;
  margin-bottom: 0 !important;
  height: 24px !important;
}

.compact-multiselect >>> .multiselect__tag {
  height: 24px !important;
  line-height: 24px !important;
  padding: 0 20px 0 8px !important;
  font-size: 12px !important;
  margin-bottom: 2px !important;
  margin-top: 2px !important;
}

.compact-multiselect >>> .multiselect__tag-icon {
  line-height: 24px !important;
}

/* Compact Search Text Input - Exactly 38px */
.compact-search-input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
  width: 100%;
}

.input-search-icon {
  position: absolute;
  left: 12px;
  color: #94a3b8;
  font-size: 13.5px;
  pointer-events: none;
}

.compact-text-input {
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

.compact-text-input:focus {
  border-color: #7367f0 !important;
  box-shadow: 0 0 0 2px rgba(115, 103, 240, 0.15) !important;
}

/* Compact Datepicker - Exactly 38px */
.compact-datepicker >>> input {
  box-sizing: border-box !important;
  height: 38px !important;
  border: 1px solid #cbd5e1 !important;
  border-radius: 6px !important;
  font-size: 13.5px !important;
  padding: 6px 12px !important;
}

/* Action Buttons */
.filter-actions-checkin {
  grid-column: span 3;
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 10px;
  padding-top: 10px;
  border-top: 1px solid #f1f5f9;
}

@media (max-width: 991px) {
  .filter-actions-checkin {
    grid-column: span 2;
  }
}

@media (max-width: 640px) {
  .filter-actions-checkin {
    grid-column: span 1;
  }
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

/* Compact Pagination Bar */
.compact-pagination-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 12px;
  padding-top: 10px;
  border-top: 1px solid #e2e8f0;
}

.compact-page-select {
  height: 28px;
  padding: 2px 6px;
  border: 1px solid #cbd5e1;
  border-radius: 4px;
  font-size: 12px;
  background: #ffffff;
  color: #334155;
  outline: none;
}

.badge-total-checkin {
  background: #f0fdf4;
  color: #15803d;
  border: 1px solid #bbf7d0;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  display: inline-flex;
  align-items: center;
}

.table-responsive {
  width: 100%;
  overflow-x: auto;
}

.modern-checkin-table {
  width: 100%;
  border-collapse: collapse;
}

.modern-checkin-table thead tr {
  background: #f8fafc;
  border-bottom: 2px solid #e2e8f0;
}

.modern-checkin-table th {
  padding: 12px 14px;
  font-size: 12px;
  font-weight: 700;
  color: #475569;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.modern-checkin-table td {
  padding: 14px;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
  font-size: 13px;
}

.subtle-source-tag {
  background: #f1f5f9;
  color: #64748b;
  padding: 2px 7px;
  border-radius: 10px;
  font-size: 11px;
}

.subtle-ec-tag {
  background: #eff6ff;
  color: #1d4ed8;
  padding: 2px 7px;
  border-radius: 10px;
  font-size: 11px;
}

.course-pill {
  background: #fef3c7;
  color: #92400e;
  border: 1px solid #fde68a;
  padding: 2px 8px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 600;
  display: inline-block;
}

.status-badge {
  display: inline-flex;
  align-items: center;
  padding: 4px 10px;
  border-radius: 12px;
  font-size: 12px;
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

.action-promote-btn {
  font-size: 12px;
  padding: 6px 12px !important;
  border-radius: 6px;
}

.done-tag {
  color: #16a34a;
  font-size: 12px;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
}

.note-box {
  background: #f8fafc;
  border-left: 3px solid #cbd5e1;
  padding: 4px 8px;
  font-size: 12px;
  color: #475569;
  border-radius: 0 4px 4px 0;
  line-height: 1.4;
}
</style>
