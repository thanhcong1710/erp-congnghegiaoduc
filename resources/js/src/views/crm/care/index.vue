<template>
  <div id="page-crm-care-list">
    <!-- KHUNG BỘ LỌC QUẢN LÝ CHĂM SÓC HIỆN ĐẠI -->
    <vx-card no-shadow class="mt-5 filter-box-card">
      <div class="filter-header-bar flex items-center justify-between pb-3 mb-4 border-b border-gray-100">
        <div class="flex items-center">
          <div class="filter-icon-badge mr-2">
            <i class="feather icon-filter"></i>
          </div>
          <div>
            <h5 class="font-bold text-gray-800 text-base mb-0">Bộ lọc lịch sử chăm sóc</h5>
            <p class="text-xs text-gray-400 mb-0">Tra cứu nhanh cuộc gọi, ghi chú chăm sóc theo nhân viên và trạng thái</p>
          </div>
        </div>
        <div class="flex items-center">
          <span class="active-filter-badge mr-3" v-if="activeFilterCount > 0">
            <i class="feather icon-check-circle mr-1"></i> Đang lọc {{ activeFilterCount }} tiêu chí
          </span>
          <button 
            type="button" 
            class="btn-toggle-filter" 
            @click="isFilterCollapsed = !isFilterCollapsed"
            :title="isFilterCollapsed ? 'Mở rộng bộ lọc' : 'Thu gọn bộ lọc'"
          >
            <i :class="isFilterCollapsed ? 'feather icon-chevron-down' : 'feather icon-chevron-up'"></i>
            {{ isFilterCollapsed ? 'Mở rộng' : 'Thu gọn' }}
          </button>
        </div>
      </div>

      <!-- NỘI DUNG CÁC TRƯỜNG LỌC -->
      <div v-show="!isFilterCollapsed" class="filter-body-content">
        <div class="vx-row">
          <!-- 1. Từ khóa -->
          <div class="vx-col sm:w-1/2 md:w-1/4 w-full mb-4">
            <label class="custom-filter-label">
              <i class="feather icon-search text-primary mr-1"></i> Từ khóa
            </label>
            <vs-input
              class="w-full custom-input"
              placeholder="Tên khách hàng, số điện thoại..."
              v-model="searchData.keyword"
              @keyup.enter="search"
            ></vs-input>
          </div>

          <!-- 2. Người phụ trách -->
          <div class="vx-col sm:w-1/2 md:w-1/4 w-full mb-4">
            <label class="custom-filter-label">
              <i class="feather icon-user text-primary mr-1"></i> Người phụ trách
            </label>
            <multiselect
              name="search_creator"
              placeholder="Chọn người phụ trách"
              v-model="searchData.arr_creator"
              :options="care_users_list"
              label="label_name"
              :close-on-select="false"
              :hide-selected="true"
              :multiple="true"
              :searchable="true"
              track-by="id"
              selectedLabel=""
              selectLabel=""
              deselectLabel=""
            >
              <span slot="noResult">Không tìm thấy dữ liệu</span>
            </multiselect>
          </div>

          <!-- 3. Khoảng thời gian chăm sóc -->
          <div class="vx-col sm:w-1/2 md:w-1/4 w-full mb-4">
            <label class="custom-filter-label">
              <i class="feather icon-calendar text-primary mr-1"></i> Khoảng thời gian chăm sóc
            </label>
            <date-picker
              name="item-date"
              v-model="searchData.dateRange"
              range
              format="YYYY-MM-DD"
              style="width: 100%;"
              :clearable="true"
              :lang="datepickerOptions.lang"
              placeholder="Chọn khoảng ngày chăm sóc"
            ></date-picker>
          </div>

          <!-- 4. Trạng thái cuộc gọi -->
          <div class="vx-col sm:w-1/2 md:w-1/4 w-full mb-4">
            <label class="custom-filter-label">
              <i class="feather icon-phone-call text-primary mr-1"></i> Trạng thái cuộc gọi
            </label>
            <multiselect
              name="search_call_status"
              placeholder="Tất cả trạng thái cuộc gọi"
              v-model="searchData.arr_call_status"
              :options="callStatusOptions"
              label="label"
              :close-on-select="false"
              :hide-selected="true"
              :multiple="true"
              :searchable="true"
              track-by="id"
              selectedLabel=""
              selectLabel=""
              deselectLabel=""
            >
              <span slot="noResult">Không tìm thấy dữ liệu</span>
            </multiselect>
          </div>
        </div>

        <!-- ACTION BUTTONS -->
        <div class="vx-row mt-1 pt-3 border-t border-gray-100 items-center justify-between">
          <div class="vx-col sm:w-auto w-full mb-2 flex items-center">
            <vs-button class="mr-3 mb-2 shadow-btn" color="primary" @click="search">
              <i class="fa fa-search mr-1"></i> Tìm kiếm
            </vs-button>
            <vs-button color="dark" type="border" class="mb-2 mr-3" @click="reset">
              <i class="fas fa-undo-alt mr-1"></i> Đặt lại
            </vs-button>
          </div>

          <div class="vx-col sm:w-auto w-full mb-2 text-sm text-gray-500 font-medium">
            <i class="feather icon-list mr-1 text-primary"></i> Tổng số: <strong class="text-primary">{{ pagination.total }}</strong> lượt chăm sóc
          </div>
        </div>
      </div>

      <!-- BẢNG DANH SÁCH CHĂM SÓC NÂNG CẤP CHUẨN ERP -->
      <div class="care-table-wrapper mt-3">
        <div class="care-table-scroll">
          <table class="care-custom-table" style="min-width: 1450px">
            <thead>
              <tr>
                <th class="th-center" style="width: 60px;">STT</th>
                <th class="th-center" style="width: 140px;">Thời gian</th>
                <th style="width: 190px;">Tên khách hàng</th>
                <th class="th-center" style="width: 130px;">Số điện thoại</th>
                <th style="width: 160px;">Người phụ trách</th>
                <th class="th-center" style="width: 130px;">Phương thức</th>
                <th class="th-center" style="width: 180px;">Trạng thái cuộc gọi</th>
                <th style="width: 380px;">Nội dung chăm sóc</th>
                <th class="th-center" style="width: 140px;">Lịch hẹn tiếp theo</th>
                <th class="th-center" style="width: 90px;">Thao tác</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(item, index) in cares"
                :key="item.id || index"
              >
                <!-- STT -->
                <td class="td-center font-medium text-gray-500">
                  {{ index + 1 + (pagination.cpage - 1) * pagination.limit }}
                </td>

                <!-- Thời gian -->
                <td class="td-center">
                  <div class="font-medium text-dark text-sm">{{ formatDateOnly(item.care_date) }}</div>
                  <div class="text-xs text-muted">{{ formatTimeOnly(item.care_date) }}</div>
                </td>

                <!-- Tên khách hàng -->
                <td>
                  <router-link v-if="item.parent_id" :to="`/crm/parent/${item.parent_id}/detail`" class="font-semibold text-blue-600 hover:underline">
                    {{ item.parent_name }}
                  </router-link>
                  <span v-else class="font-medium text-dark">{{ item.parent_name }}</span>
                </td>

                <!-- Số điện thoại -->
                <td class="td-center">
                  <router-link v-if="item.parent_id" :to="`/crm/parent/${item.parent_id}/detail`" class="font-medium text-blue-600 hover:underline">
                    <i class="feather icon-phone text-xs mr-1 text-primary"></i>{{ item.parent_phone }}
                  </router-link>
                  <span v-else class="text-dark">{{ item.parent_phone }}</span>
                </td>

                <!-- Người phụ trách -->
                <td class="font-medium text-gray-800 text-sm">
                  {{ item.creator_name || '-' }}
                </td>

                <!-- Phương thức liên hệ -->
                <td class="td-center">
                  <span class="method-badge" :class="getMethodClass(item.method_id)">
                    <i :class="getMethodIcon(item.method_id)" class="mr-1"></i>
                    {{ item.method_name || 'Khác' }}
                  </span>
                </td>

                <!-- Trạng thái cuộc gọi -->
                <td class="td-center">
                  <span v-if="item.call_status_label" class="call-status-badge" :class="getCallStatusClass(item.call_status)">
                    {{ item.call_status_label }}
                  </span>
                  <span v-else class="text-gray-400 text-xs">-</span>
                </td>

                <!-- Nội dung chăm sóc -->
                <td>
                  <div class="care-content-box">
                    <div class="care-note-html text-gray-700" v-html="item.note"></div>
                    <div v-if="item.attached_file" class="mt-1">
                      <a :href="item.attached_file" target="_blank" class="attached-file-link inline-flex items-center">
                        <i class="feather icon-paperclip mr-1"></i> File đính kèm
                      </a>
                    </div>
                  </div>
                </td>

                <!-- Lịch hẹn tiếp theo -->
                <td class="td-center text-xs">
                  <span v-if="item.next_care_date" class="font-semibold text-primary">
                    <i class="feather icon-calendar mr-1"></i>{{ item.next_care_date }}
                  </span>
                  <span v-else class="text-gray-400">-</span>
                </td>

                <!-- Thao tác -->
                <td class="td-center">
                  <router-link v-if="item.parent_id" :to="`/crm/parent/${item.parent_id}/detail`" title="Xem chi tiết hồ sơ">
                    <button class="btn-action-view">
                      <i class="feather icon-eye"></i>
                    </button>
                  </router-link>
                  <span v-else class="text-gray-400">-</span>
                </td>
              </tr>

              <!-- Trống dữ liệu -->
              <tr v-if="!cares || cares.length === 0">
                <td colspan="10" class="text-center py-8 text-gray-400">
                  <i class="feather icon-inbox text-3xl mb-2 block"></i>
                  Không có dữ liệu chăm sóc nào phù hợp với bộ lọc
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- PHÂN TRANG HIỆN ĐẠI -->
      <div class="flex flex-wrap items-center justify-between mt-5" v-if="pagination.total > 0">
        <div class="flex items-center">
          <vs-dropdown vs-trigger-click class="cursor-pointer mr-4 items-per-page-handler">
            <div class="p-3 border border-solid d-theme-border-grey-light rounded-full d-theme-dark-bg cursor-pointer flex items-center justify-between font-medium text-sm">
              <span class="mr-2">
                {{ pagination.cpage * pagination.limit - (pagination.limit - 1) }} -
                {{ pagination.total - pagination.cpage * pagination.limit > 0 ? pagination.cpage * pagination.limit : pagination.total }}
                của {{ pagination.total }}
              </span>
              <feather-icon icon="ChevronDownIcon" svgClasses="h-4 w-4" />
            </div>
            <vs-dropdown-menu>
              <vs-dropdown-item
                v-for="(item, index) in limitSource"
                :key="index"
                @click="changePageLimit(item)"
              >
                <span>{{ item }} dòng / trang</span>
              </vs-dropdown-item>
            </vs-dropdown-menu>
          </vs-dropdown>
          <span class="text-sm text-gray-500">Hiển thị dữ liệu trang {{ pagination.cpage }}</span>
        </div>

        <vs-pagination
          v-if="Math.ceil(pagination.total / pagination.limit) > 1"
          :total="Math.ceil(pagination.total / pagination.limit)"
          :max="7"
          v-model="pagination.cpage"
          @change="changePage()"
        />
      </div>
    </vx-card>
  </div>
</template>

<script>
import axios from '../../../http/axios.js'
import Multiselect from 'vue-multiselect'
import DatePicker from 'vue2-datepicker'
import u from '../../../until/helper.js'
import moment from 'moment'

export default {
  name: 'CrmCareList',
  components: {
    DatePicker,
    Multiselect
  },
  data() {
    return {
      isFilterCollapsed: false,
      searchData: {
        keyword: '',
        arr_creator: [],
        creator_id: [],
        arr_call_status: [],
        call_status: [],
        dateRange: ''
      },
      callStatusOptions: u.statusOptionsCare,
      care_users_list: [],
      datepickerOptions: {
        closed: true,
        value: '',
        minDate: '',
        lang: {
          days: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
          months: [
            'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6',
            'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'
          ]
        }
      },
      cares: [],
      limitSource: [20, 50, 100, 500],
      pagination: {
        url: '/api/crm/care/list',
        id: '',
        style: 'line',
        class: '',
        spage: 1,
        ppage: 1,
        npage: 0,
        lpage: 1,
        cpage: 1,
        total: 0,
        limit: 20,
        pages: [],
        init: 0
      }
    }
  },
  computed: {
    activeFilterCount() {
      let count = 0
      if (this.searchData.keyword && this.searchData.keyword.trim()) count++
      if (this.searchData.dateRange && this.searchData.dateRange.length && this.searchData.dateRange[0]) count++
      if (this.searchData.arr_creator && this.searchData.arr_creator.length) count++
      if (this.searchData.arr_call_status && this.searchData.arr_call_status.length) count++
      return count
    }
  },
  created() {
    axios.g('/api/crm/care/users').then((response) => {
      this.care_users_list = response.data
    })
    this.getData()
  },
  methods: {
    search() {
      this.pagination.cpage = 1
      this.getData()
    },
    reset() {
      this.searchData.keyword = ''
      this.searchData.arr_creator = []
      this.searchData.creator_id = []
      this.searchData.arr_call_status = []
      this.searchData.call_status = []
      this.searchData.dateRange = ''
      this.pagination.cpage = 1
      this.getData()
    },
    getData() {
      const startDate =
        typeof this.searchData.dateRange !== 'undefined' &&
        this.searchData.dateRange !== '' &&
        this.searchData.dateRange &&
        this.searchData.dateRange[0]
          ? `${u.dateToString(this.searchData.dateRange[0])}`
          : ''
      const endDate =
        typeof this.searchData.dateRange !== 'undefined' &&
        this.searchData.dateRange !== '' &&
        this.searchData.dateRange &&
        this.searchData.dateRange[1]
          ? `${u.dateToString(this.searchData.dateRange[1])}`
          : ''

      const ids_creator = []
      if (this.searchData.arr_creator && this.searchData.arr_creator.length) {
        this.searchData.arr_creator.forEach((item) => {
          ids_creator.push(item.id)
        })
      }
      this.searchData.creator_id = ids_creator

      const ids_call_status = []
      if (this.searchData.arr_call_status && this.searchData.arr_call_status.length) {
        this.searchData.arr_call_status.forEach((item) => {
          ids_call_status.push(item.id)
        })
      }
      this.searchData.call_status = ids_call_status

      const data = {
        keyword: this.searchData.keyword,
        creator_id: this.searchData.creator_id,
        call_status: this.searchData.call_status,
        start_date: startDate,
        end_date: endDate,
        pagination: this.pagination
      }

      this.$vs.loading()
      axios
        .p('/api/crm/care/list', data)
        .then((response) => {
          this.$vs.loading.close()
          this.cares = response.data.list
          this.pagination = response.data.paging
          setTimeout(() => {
            this.pagination.init = 1
          }, 300)
        })
        .catch((error) => {
          console.error(error)
          this.$vs.loading.close()
        })
    },
    changePage() {
      if (this.pagination.init) {
        this.getData()
      }
    },
    changePageLimit(limit) {
      this.pagination.cpage = 1
      this.pagination.limit = limit
      this.getData()
    },

    formatDateOnly(dateStr) {
      if (!dateStr) return '-'
      return moment(dateStr).format('DD/MM/YYYY')
    },
    formatTimeOnly(dateStr) {
      if (!dateStr) return ''
      return moment(dateStr).format('HH:mm')
    },

    getMethodIcon(methodId) {
      const m = Number(methodId)
      if (m === 1) return 'fa fa-phone'
      if (m === 2) return 'fa fa-envelope'
      if (m === 3) return 'fa fa-users'
      if (m === 4) return 'fa fa-comment'
      if (m === 5) return 'fa fa-comment-dots'
      return 'fa fa-headset'
    },
    getMethodClass(methodId) {
      const m = Number(methodId)
      if (m === 1) return 'method-phone'
      if (m === 2) return 'method-email'
      if (m === 3) return 'method-direct'
      if (m === 4) return 'method-sms'
      if (m === 5) return 'method-zalo'
      return 'method-default'
    },
    getCallStatusClass(status) {
      const s = Number(status)
      if (s === 1 || s === 9 || s === 10) return 'status-badge-success'
      if (s === 2 || s === 4 || s === 11) return 'status-badge-danger'
      if (s === 5 || s === 3) return 'status-badge-warning'
      return 'status-badge-default'
    }
  }
}
</script>

<style scoped>
/* KHUNG BỘ LỌC HIỆN ĐẠI */
.filter-box-card {
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
}
.filter-icon-badge {
  width: 34px;
  height: 34px;
  background: #ede9fe;
  color: #7367f0;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
}
.active-filter-badge {
  font-size: 12px;
  background: #e0f2fe;
  color: #0369a1;
  padding: 4px 10px;
  border-radius: 20px;
  font-weight: 600;
}
.btn-toggle-filter {
  background: #f1f5f9;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  padding: 5px 12px;
  font-size: 12.5px;
  color: #475569;
  font-weight: 500;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  transition: all 0.2s;
}
.btn-toggle-filter:hover {
  background: #e2e8f0;
  color: #1e293b;
}

/* NHÃN FILTER VÀ INPUTS */
.custom-filter-label {
  display: flex;
  align-items: center;
  font-size: 12.5px;
  font-weight: 600;
  color: #475569;
  margin-bottom: 6px;
}
.custom-input input {
  height: 40px !important;
  border-radius: 6px !important;
  border: 1px solid #cbd5e1 !important;
  background: #f8fafc !important;
  transition: all 0.2s;
}
.custom-input input:focus {
  border-color: #7367f0 !important;
  background: #fff !important;
  box-shadow: 0 0 0 3px rgba(115, 103, 240, 0.12) !important;
}
.shadow-btn {
  box-shadow: 0 3px 10px rgba(115, 103, 240, 0.3) !important;
}

/* BẢNG CHĂM SÓC NÂNG CẤP */
.care-table-wrapper {
  background: #fff;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
}
.care-table-scroll {
  overflow-x: auto;
  max-width: 100%;
}
.care-custom-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  font-size: 13px;
}
.care-custom-table thead tr {
  background-color: #2b3346;
  color: #ffffff;
}
.care-custom-table th {
  padding: 12px 14px;
  font-size: 12.5px;
  font-weight: 600;
  border-right: 1px solid rgba(255, 255, 255, 0.12);
  white-space: nowrap;
  vertical-align: middle;
}
.care-custom-table th:last-child {
  border-right: none;
}
.th-center, .td-center {
  text-align: center !important;
}
.care-custom-table tbody tr {
  background-color: #ffffff;
  transition: background-color 0.15s ease;
  border-bottom: 1px solid #edf2f7;
}
.care-custom-table tbody tr:nth-child(even) {
  background-color: #fbfcfe;
}
.care-custom-table tbody tr:hover {
  background-color: #f1f5f9;
}
.care-custom-table td {
  padding: 10px 14px;
  border-right: 1px solid #f1f5f9;
  border-bottom: 1px solid #e2e8f0;
  vertical-align: middle;
  line-height: 1.45;
}
.care-custom-table td:last-child {
  border-right: none;
}

/* PHƯƠNG THỨC LIÊN HỆ BADGES */
.method-badge {
  display: inline-flex;
  align-items: center;
  padding: 3px 9px;
  border-radius: 4px;
  font-size: 11.5px;
  font-weight: 600;
}
.method-phone {
  background: #ede9fe;
  color: #6d28d9;
}
.method-email {
  background: #e0f2fe;
  color: #0369a1;
}
.method-direct {
  background: #dcfce7;
  color: #15803d;
}
.method-sms {
  background: #fef3c7;
  color: #b45309;
}
.method-zalo {
  background: #e0e7ff;
  color: #3730a3;
}
.method-default {
  background: #f1f5f9;
  color: #475569;
}

/* TRẠNG THÁI CUỘC GỌI BADGES */
.call-status-badge {
  display: inline-block;
  padding: 2px 8px;
  border-radius: 12px;
  font-size: 11.5px;
  font-weight: 500;
}
.status-badge-success {
  background: #dcfce7;
  color: #16a34a;
}
.status-badge-danger {
  background: #fee2e2;
  color: #dc2626;
}
.status-badge-warning {
  background: #fef3c7;
  color: #d97706;
}
.status-badge-default {
  background: #f1f5f9;
  color: #64748b;
}

/* NỘI DUNG CHĂM SÓC */
.care-content-box {
  max-width: 380px;
  font-size: 12.5px;
  line-height: 1.5;
}
.care-note-html {
  word-break: break-word;
}
.attached-file-link {
  font-size: 11.5px;
  color: #2563eb;
  background: #eff6ff;
  border: 1px solid #bfdbfe;
  padding: 1px 6px;
  border-radius: 4px;
  transition: all 0.15s;
}
.attached-file-link:hover {
  background: #dbeafe;
  text-decoration: underline;
}

/* THAO TÁC */
.btn-action-view {
  width: 30px;
  height: 30px;
  border-radius: 6px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border: none;
  background: #7367f0;
  color: #fff;
  cursor: pointer;
  font-size: 12px;
  transition: all 0.15s;
}
.btn-action-view:hover {
  background: #5e50ee;
  transform: translateY(-1px);
}

/* ĐỒNG BỘ MULTISELECT & DATEPICKER */
>>> .multiselect {
  min-height: 40px;
}
>>> .multiselect .multiselect__tags {
  min-height: 40px !important;
  padding: 8px 40px 0 10px !important;
  border-radius: 6px !important;
  border: 1px solid #cbd5e1 !important;
  background: #f8fafc !important;
  box-sizing: border-box !important;
}
>>> .multiselect--active .multiselect__tags {
  border-color: #7367f0 !important;
  background: #fff !important;
  box-shadow: 0 0 0 3px rgba(115, 103, 240, 0.12) !important;
}
>>> .mx-datepicker {
  width: 100% !important;
}
>>> .mx-datepicker .mx-input {
  height: 40px !important;
  line-height: 40px !important;
  font-size: 13.5px !important;
  border-radius: 6px !important;
  border: 1px solid #cbd5e1 !important;
  background-color: #f8fafc !important;
  box-shadow: none !important;
}
>>> .mx-datepicker .mx-input:focus {
  border-color: #7367f0 !important;
  background-color: #fff !important;
  box-shadow: 0 0 0 3px rgba(115, 103, 240, 0.12) !important;
}
</style>
