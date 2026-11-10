<template>
  <div id="page-charges-list">
    <!-- Header Banner -->
    <div class="charges-page-header">
      <div>
        <h4 class="font-bold text-gray-800 flex items-center text-lg m-0">
          <i class="fa-solid fa-receipt text-primary mr-2" style="font-size: 20px;"></i>
          Danh Sách Phiếu Thu Học Phí
        </h4>
        <span class="text-gray-500 text-sm">Quản lý lịch sử các đợt thu học phí, theo dõi chứng từ kế toán và tiến độ thanh toán</span>
      </div>
      <div class="charges-header-badge">
        <span class="badge-total-items">
          <i class="fa-solid fa-file-invoice-dollar mr-1.5"></i>
          Tổng cộng: <strong>{{ pagination.total }}</strong> phiếu thu
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

        <!-- Từ khóa -->
        <div class="vx-col md:w-1/3 sm:w-1/2 w-full mb-3">
          <label class="filter-label"><i class="fa-solid fa-magnifying-glass text-primary mr-1"></i> Tìm kiếm</label>
          <div class="filter-search-wrapper">
            <i class="fa-solid fa-search filter-search-icon"></i>
            <input
              type="text"
              class="filter-input-text"
              placeholder="Mã HS, tên HS, mã hợp đồng..."
              v-model="searchData.keyword"
              @keyup.enter="getData"
            />
          </div>
        </div>

        <!-- Ngày thu phí -->
        <div class="vx-col md:w-1/3 sm:w-1/2 w-full mb-3">
          <label class="filter-label"><i class="fa-regular fa-calendar-days text-primary mr-1"></i> Ngày thu phí</label>
          <date-picker
            name="item-date"
            class="w-full custom-datepicker"
            v-model="searchData.dateRange"
            range
            format="YYYY-MM-DD"
            style="width: 100%"
            :clearable="true"
            :lang="datepickerOptions.lang"
            placeholder="Chọn khoảng thời gian thu phí"
          ></date-picker>
        </div>
      </div>

      <!-- Action Buttons Row -->
      <div class="filter-actions-bar">
        <button class="btn-filter-action btn-filter-submit" @click="getData" title="Tìm kiếm">
          <i class="fa fa-search mr-1.5"></i> Tìm kiếm
        </button>
        <button class="btn-filter-action btn-filter-reset" @click="reset" title="Đặt lại bộ lọc">
          <i class="fas fa-undo-alt mr-1.5"></i> Đặt lại
        </button>
      </div>
    </vx-card>

    <!-- Table / Content Card -->
    <vx-card no-shadow class="shadow-sm border border-solid border-grey-light rounded-lg compact-table-card">
      <!-- Mobile View Mode Toggle -->
      <div class="mobile-view-toggle-bar">
        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Chế độ hiển thị</span>
        <div class="inline-flex rounded shadow-sm">
          <button
            type="button"
            class="mobile-view-toggle-btn"
            :class="{ 'active': viewMode === 'card' }"
            @click="viewMode = 'card'"
          >
            <i class="fa-solid fa-id-card mr-1"></i> Dạng thẻ
          </button>
          <button
            type="button"
            class="mobile-view-toggle-btn"
            :class="{ 'active': viewMode === 'table' }"
            @click="viewMode = 'table'"
          >
            <i class="fa-solid fa-table mr-1"></i> Dạng bảng
          </button>
        </div>
      </div>

      <!-- Mobile Card View -->
      <div v-if="!isDesktop && viewMode === 'card'" class="mobile-cards-list">
        <div v-if="!payments || payments.length === 0" class="text-center py-8 text-gray-500 bg-white rounded-lg border border-dashed border-gray-300">
          <i class="fa-solid fa-folder-open text-gray-300 mb-2" style="font-size: 36px; display: block;"></i>
          Không tìm thấy phiếu thu nào phù hợp
        </div>

        <div
          v-for="(item, index) in payments"
          :key="'card-' + index"
          class="mobile-item-card mb-3 p-3 bg-white rounded-lg border border-solid border-grey-light shadow-sm"
        >
          <!-- Header -->
          <div class="flex items-start justify-between gap-2 pb-2.5 border-b border-gray-100">
            <div class="min-w-0">
              <div class="font-bold text-gray-900 text-base truncate">
                {{ item.name }}
              </div>
              <div class="flex flex-wrap items-center gap-1.5 mt-1">
                <span class="stk-code-badge" @click="copyCode(item.lms_code)" title="Bấm để sao chép mã học sinh">
                  <i class="fa-solid fa-id-badge mr-1"></i>
                  <strong>{{ item.lms_code || '---' }}</strong>
                  <i class="fa-regular fa-copy ml-1 opacity-70"></i>
                </span>
                <span class="contract-code-badge" @click="copyCode(item.code)" title="Bấm để sao chép mã hợp đồng">
                  <i class="fa-solid fa-file-contract mr-1"></i>
                  <strong>{{ item.code }}</strong>
                  <i class="fa-regular fa-copy ml-1 opacity-70"></i>
                </span>
              </div>
            </div>
            <div class="flex flex-col items-end gap-1 flex-shrink-0">
              <span class="text-xs text-gray-400 font-semibold">
                #{{ index + 1 + (pagination.cpage - 1) * pagination.limit }}
              </span>
              <span class="amount-tag text-xs font-bold">
                {{ item.amount | formatMoney }}
              </span>
            </div>
          </div>

          <!-- Body -->
          <div class="py-2.5 text-sm space-y-1.5">
            <div class="flex items-center justify-between text-gray-600" v-if="item.product_name">
              <span class="text-gray-500"><i class="fa-solid fa-book-open text-indigo-500 mr-1.5"></i>Khóa học:</span>
              <span class="font-medium text-gray-800">{{ item.product_name }}</span>
            </div>

            <div class="flex items-center justify-between text-gray-600" v-if="item.tuition_fee_name">
              <span class="text-gray-500"><i class="fa-solid fa-tags text-gray-400 mr-1.5"></i>Gói phí:</span>
              <span class="text-gray-700">{{ item.tuition_fee_name }}</span>
            </div>

            <div class="flex items-center justify-between text-gray-600 pt-1 border-t border-dashed border-gray-100">
              <span class="text-gray-500"><i class="fa-solid fa-coins text-amber-500 mr-1.5"></i>Phải đóng:</span>
              <span class="font-medium text-gray-800">{{ item.must_charge | formatMoney }}</span>
            </div>

            <div class="flex items-center justify-between text-gray-600">
              <span class="text-gray-500"><i class="fa-solid fa-circle-check text-success mr-1.5"></i>Tổng đã thu:</span>
              <span class="font-bold text-success">{{ item.total | formatMoney }}</span>
            </div>

            <div class="flex items-center justify-between text-gray-600">
              <span class="text-gray-500"><i class="fa-solid fa-scale-balanced text-danger mr-1.5"></i>Còn lại (nợ):</span>
              <span class="font-bold text-danger">{{ item.debt | formatMoney }}</span>
            </div>

            <div class="flex items-center justify-between text-gray-500 text-xs pt-1 border-t border-dashed border-gray-100">
              <span><i class="fa-regular fa-calendar-check text-primary mr-1"></i>Ngày thu: <strong>{{ item.charge_date }}</strong></span>
              <span v-if="item.creator_name"><i class="fa-solid fa-user-pen text-gray-400 mr-1"></i>Lập bởi: <strong>{{ item.creator_name }}</strong></span>
            </div>
          </div>

          <!-- Actions -->
          <div class="pt-2 border-t border-gray-100 flex items-center justify-end">
            <router-link :to="`/pages/print/receipt/${item.id}`" target="_blank" class="w-full">
              <vs-button size="small" color="#0284c7" type="relief" class="w-full flex items-center justify-center font-semibold">
                <i class="fa-solid fa-print mr-1.5"></i> In phiếu thu
              </vs-button>
            </router-link>
          </div>
        </div>
      </div>

      <!-- Desktop Table View -->
      <div v-show="isDesktop || viewMode === 'table'" class="desktop-table-wrapper">
        <div class="table-responsive">
          <table class="modern-lms-table">
            <thead>
              <tr>
                <th class="text-center" style="width: 50px">STT</th>
                <th style="width: 230px">Học sinh</th>
                <th style="width: 240px">Hợp đồng & Khóa học</th>
                <th style="width: 250px">Thông tin thu phí</th>
                <th style="width: 210px">Ngày thu & Thu ngân</th>
                <th style="width: 110px" class="text-center">Thao tác</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="!payments || payments.length === 0">
                <td colspan="6" class="text-center py-8 text-gray-500">
                  <i class="fa-solid fa-folder-open text-gray-300 mb-2" style="font-size: 36px; display: block;"></i>
                  Không tìm thấy phiếu thu nào phù hợp
                </td>
              </tr>

              <tr v-for="(item, index) in payments" :key="index" class="table-row-hover">
                <!-- STT -->
                <td class="text-center text-gray-500 font-semibold">
                  {{ index + 1 + (pagination.cpage - 1) * pagination.limit }}
                </td>

                <!-- Học sinh -->
                <td>
                  <div class="item-title-text mb-1">
                    {{ item.name }}
                  </div>
                  <div class="flex items-center gap-1.5 flex-wrap">
                    <span class="stk-code-badge" @click="copyCode(item.lms_code)" title="Bấm để sao chép mã học sinh">
                      <i class="fa-solid fa-id-badge mr-1"></i>
                      <strong>{{ item.lms_code || '---' }}</strong>
                      <i class="fa-regular fa-copy ml-1 opacity-70"></i>
                    </span>
                  </div>
                </td>

                <!-- Hợp đồng & Khóa học -->
                <td>
                  <div class="flex items-center gap-1.5 mb-1">
                    <span class="contract-code-badge" @click="copyCode(item.code)" title="Bấm để sao chép mã hợp đồng">
                      <i class="fa-solid fa-file-contract mr-1"></i>
                      <strong>{{ item.code }}</strong>
                      <i class="fa-regular fa-copy ml-1 opacity-70"></i>
                    </span>
                  </div>
                  <div class="text-gray-800 font-semibold text-sm mb-0.5" v-if="item.product_name">
                    <i class="fa-solid fa-book-open text-indigo-500 mr-1 text-xs"></i>{{ item.product_name }}
                  </div>
                  <div class="text-xs text-gray-500" v-if="item.tuition_fee_name">
                    Gói phí: {{ item.tuition_fee_name }}
                  </div>
                </td>

                <!-- Thông tin thu phí -->
                <td>
                  <div class="flex items-center gap-2 mb-1">
                    <span class="text-xs text-gray-500">Thu đợt này:</span>
                    <span class="amount-tag font-bold text-sm">
                      <i class="fa-solid fa-money-bill-wave mr-1"></i>
                      {{ item.amount | formatMoney }}
                    </span>
                  </div>
                  <div class="text-xs text-gray-600 mb-0.5">
                    Phải đóng: <strong>{{ item.must_charge | formatMoney }}</strong> &bull; Đã thu: <strong class="text-success">{{ item.total | formatMoney }}</strong>
                  </div>
                  <div class="text-xs text-gray-500">
                    Công nợ còn: <strong :class="item.debt > 0 ? 'text-danger' : 'text-success'">{{ item.debt | formatMoney }}</strong>
                  </div>
                </td>

                <!-- Ngày thu & Thu ngân -->
                <td>
                  <div class="font-semibold text-gray-800 text-sm mb-1 flex items-center">
                    <i class="fa-regular fa-calendar-check text-primary mr-1.5"></i>
                    {{ item.charge_date }}
                  </div>
                  <div class="text-xs text-gray-500 mb-0.5" v-if="item.creator_name">
                    <i class="fa-solid fa-user-pen text-gray-400 mr-1"></i>Người lập: <strong>{{ item.creator_name }}</strong>
                  </div>
                  <div class="text-xs text-gray-400" v-if="item.created_at">
                    <i class="fa-regular fa-clock mr-1"></i>{{ item.created_at }}
                  </div>
                </td>

                <!-- Thao tác -->
                <td class="text-center">
                  <div class="flex items-center justify-center gap-1.5">
                    <router-link :to="`/pages/print/receipt/${item.id}`" target="_blank">
                      <button class="action-circle-btn action-print" title="In phiếu thu">
                        <i class="fa-solid fa-print"></i>
                      </button>
                    </router-link>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Pagination -->
      <div class="pagination-footer-container">
        <vs-dropdown vs-trigger-click class="cursor-pointer items-per-page-handler">
          <div class="pagination-dropdown-pill">
            <span class="mr-2">{{ pagination.total > 0 ? (pagination.cpage * pagination.limit - (pagination.limit - 1)) : 0 }} - {{ pagination.total - pagination.cpage * pagination.limit > 0 ? pagination.cpage * pagination.limit : pagination.total }} of {{ pagination.total }}</span>
            <feather-icon icon="ChevronDownIcon" svgClasses="h-4 w-4" />
          </div>
          <vs-dropdown-menu>
            <vs-dropdown-item v-for="(item, index) in limitSource" :key="index" @click="changePageLimit(item)">
              <span>{{ item }}</span>
            </vs-dropdown-item>
          </vs-dropdown-menu>
        </vs-dropdown>
        <div class="pagination-control-wrapper" v-if="pagination.total && Math.ceil(pagination.total / pagination.limit) > 1">
          <vs-pagination
            :total="Math.ceil(pagination.total / pagination.limit)"
            :max="7"
            v-model="pagination.cpage"
            @change="changePage()"
          />
        </div>
      </div>
    </vx-card>
  </div>
</template>

<script>
import vSelect from 'vue-select';
import axios from '../../../http/axios.js';
import Multiselect from 'vue-multiselect';
import u from '../../../until/helper.js';
import DatePicker from 'vue2-datepicker';

export default {
  components: {
    vSelect,
    Multiselect,
    DatePicker
  },
  data() {
    return {
      windowWidth: typeof window !== 'undefined' ? window.innerWidth : 1024,
      viewMode: 'table',
      branch_list: [],
      searchData: {
        arr_branch: '',
        branch_id: '',
        keyword: '',
        dateRange: ''
      },
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
      payments: [],
      limitSource: [20, 50, 100, 500],
      pagination: {
        url: '/api/roles/list',
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
      },
      delete_id: ''
    };
  },
  computed: {
    isDesktop() {
      return this.windowWidth >= 768;
    }
  },
  created() {
    axios.g(`/api/system/branches-has-user`).then((response) => {
      this.branch_list = response.data;
    });
    this.getData();
  },
  mounted() {
    window.addEventListener('resize', this.handleResize);
  },
  beforeDestroy() {
    window.removeEventListener('resize', this.handleResize);
  },
  methods: {
    handleResize() {
      this.windowWidth = window.innerWidth;
    },
    copyCode(code) {
      if (!code) return;
      navigator.clipboard.writeText(code).then(() => {
        this.$vs.notify({
          title: 'Đã sao chép',
          text: `Mã "${code}" đã được lưu vào clipboard`,
          color: 'success',
          iconPack: 'feather',
          icon: 'icon-check',
          time: 2500
        });
      });
    },
    reset() {
      this.searchData.keyword = '';
      this.searchData.arr_branch = '';
      this.searchData.branch_id = '';
      this.searchData.pagination = this.pagination;
      this.searchData.dateRange = '';
      this.getData();
    },
    getData() {
      const startDate =
        typeof this.searchData.dateRange !== 'undefined' &&
        this.searchData.dateRange !== '' &&
        this.searchData.dateRange[0]
          ? `${u.dateToString(this.searchData.dateRange[0])}`
          : '';
      const endDate =
        typeof this.searchData.dateRange !== 'undefined' &&
        this.searchData.dateRange !== '' &&
        this.searchData.dateRange[1]
          ? `${u.dateToString(this.searchData.dateRange[1])}`
          : '';
      const ids_branch = [];
      if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
        this.searchData.arr_branch.map((item) => {
          ids_branch.push(item.id);
        });
      }
      this.searchData.branch_id = ids_branch;
      const data = {
        start_date: startDate,
        end_date: endDate,
        keyword: this.searchData.keyword,
        branch_id: this.searchData.branch_id,
        pagination: this.pagination
      };

      this.$vs.loading();
      axios
        .p('/api/lms/accounting/charges/list', data)
        .then((response) => {
          this.$vs.loading.close();
          this.payments = response.data.list;
          this.pagination = response.data.paging;
          setTimeout(() => {
            this.pagination.init = 1;
          }, 500);
        })
        .catch((error) => {
          console.log(error);
          this.$vs.loading.close();
        });
    },
    changePage() {
      if (this.pagination.init) {
        this.getData();
      }
    },
    changePageLimit(limit) {
      this.pagination.cpage = 1;
      this.pagination.limit = limit;
      this.getData();
    }
  }
};
</script>

<style scoped>
#page-charges-list {
  width: 100% !important;
  max-width: 100% !important;
  overflow-x: hidden !important;
}

.charges-page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 16px;
  margin-top: 4px;
}

.badge-total-items {
  display: inline-flex;
  align-items: center;
  background-color: #eff6ff;
  color: #1d4ed8;
  font-size: 13.5px;
  padding: 6px 14px;
  border-radius: 9999px;
  border: 1px solid #bfdbfe;
}

.filter-label {
  font-size: 13.5px;
  font-weight: 600;
  color: #334155;
  margin-bottom: 6px;
  display: block;
}

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
  font-size: 14px;
  pointer-events: none;
}

.filter-input-text {
  box-sizing: border-box !important;
  width: 100% !important;
  height: 38px !important;
  border: 1px solid #cbd5e1 !important;
  border-radius: 6px !important;
  padding: 6px 12px 6px 34px !important;
  font-size: 14px !important;
  color: #1e293b !important;
  outline: none !important;
  background: #ffffff !important;
  transition: all 0.2s ease !important;
}

.filter-input-text:focus {
  border-color: #7367f0 !important;
  box-shadow: 0 0 0 2px rgba(115, 103, 240, 0.15) !important;
}

.custom-multiselect >>> .multiselect {
  min-height: 38px !important;
}

.custom-multiselect >>> .multiselect__tags {
  box-sizing: border-box !important;
  min-height: 38px !important;
  padding: 6px 32px 6px 12px !important;
  border: 1px solid #cbd5e1 !important;
  border-radius: 6px !important;
  font-size: 14px !important;
  background: #ffffff !important;
  display: flex !important;
  flex-wrap: wrap !important;
  align-items: center !important;
}

.custom-multiselect >>> .multiselect__select {
  height: 36px !important;
  width: 30px !important;
  padding: 4px 8px !important;
}

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
  font-size: 14px;
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

/* Mobile Toggle Bar */
.mobile-view-toggle-bar {
  display: none;
  align-items: center;
  justify-content: space-between;
  padding-bottom: 12px;
  margin-bottom: 12px;
  border-bottom: 1px solid #f1f5f9;
}

.mobile-view-toggle-btn {
  padding: 6px 12px;
  font-size: 13px;
  font-weight: 600;
  color: #64748b;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  cursor: pointer;
  transition: all 0.2s ease;
}

.mobile-view-toggle-btn:first-child {
  border-top-left-radius: 6px;
  border-bottom-left-radius: 6px;
  border-right: none;
}

.mobile-view-toggle-btn:last-child {
  border-top-right-radius: 6px;
  border-bottom-right-radius: 6px;
}

.mobile-view-toggle-btn.active {
  background: #7367f0;
  color: #ffffff;
  border-color: #7367f0;
}

@media (max-width: 767px) {
  .mobile-view-toggle-bar {
    display: flex;
  }
  .charges-page-header {
    flex-direction: column !important;
    align-items: flex-start !important;
  }
}

/* Table Style */
.compact-table-card {
  width: 100% !important;
  overflow: hidden !important;
}

.compact-table-card >>> .vx-card__body {
  padding: 16px !important;
}

.table-responsive {
  width: 100% !important;
  overflow-x: auto !important;
  -webkit-overflow-scrolling: touch !important;
}

.modern-lms-table {
  width: 100%;
  min-width: 950px;
  border-collapse: separate;
  border-spacing: 0;
  font-size: 13.5px;
}

.modern-lms-table thead th {
  background: #f8fafc;
  color: #475569;
  font-weight: 700;
  font-size: 13px;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  padding: 12px 14px;
  border-bottom: 2px solid #e2e8f0;
  white-space: nowrap;
}

.modern-lms-table tbody td {
  padding: 12px 14px;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
}

.table-row-hover:hover {
  background-color: #f8fafc;
}

.item-title-text {
  font-size: 14.5px;
  font-weight: 700;
  color: #1e293b;
}

.stk-code-badge {
  display: inline-flex;
  align-items: center;
  font-size: 12.5px;
  font-weight: 700;
  color: #7367f0;
  background-color: #f5f3ff;
  border: 1px solid #ddd6fe;
  padding: 2px 7px;
  border-radius: 5px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.stk-code-badge:hover {
  background-color: #ede9fe;
}

.contract-code-badge {
  display: inline-flex;
  align-items: center;
  font-size: 12.5px;
  font-weight: 700;
  color: #0284c7;
  background-color: #f0f9ff;
  border: 1px solid #bae6fd;
  padding: 2px 7px;
  border-radius: 5px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.contract-code-badge:hover {
  background-color: #e0f2fe;
}

.amount-tag {
  display: inline-flex;
  align-items: center;
  color: #15803d;
  background-color: #dcfce7;
  padding: 3px 8px;
  border-radius: 5px;
  border: 1px solid #bbf7d0;
}

/* Action Circle Buttons */
.action-circle-btn {
  width: 32px;
  height: 32px;
  border-radius: 6px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  border: 1px solid transparent;
  cursor: pointer;
  transition: all 0.2s ease;
}

.action-print {
  background: #e0f2fe;
  color: #0284c7;
  border-color: #bae6fd;
}
.action-print:hover {
  background: #0284c7;
  color: #ffffff;
}

/* Pagination Footer */
.pagination-footer-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-top: 16px;
  margin-top: 16px;
  border-top: 1px solid #f1f5f9;
  flex-wrap: wrap;
  gap: 12px;
}

.pagination-dropdown-pill {
  padding: 8px 16px;
  border: 1px solid #e2e8f0;
  border-radius: 20px;
  background: #ffffff;
  display: flex;
  align-items: center;
  font-size: 13px;
  font-weight: 500;
  color: #475569;
}

.pagination-control-wrapper {
  margin-left: auto;
}
</style>
