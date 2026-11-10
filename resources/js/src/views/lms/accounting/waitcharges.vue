<template>
  <div id="page-waitcharges-list">
    <!-- Header Banner -->
    <div class="waitcharges-page-header">
      <div>
        <h4 class="font-bold text-gray-800 flex items-center text-lg m-0">
          <i class="fa-solid fa-clock-rotate-left text-warning mr-2" style="font-size: 20px;"></i>
          Danh Sách Hợp Đồng Chờ Thu Phí
        </h4>
        <span class="text-gray-500 text-sm">Theo dõi các hợp đồng học sinh còn công nợ và thực hiện thu phí học sinh</span>
      </div>
      <div class="waitcharges-header-badge">
        <span class="badge-total-items">
          <i class="fa-solid fa-hourglass-half mr-1.5"></i>
          Tổng cộng: <strong>{{ pagination.total }}</strong> hợp đồng chờ thu
        </span>
      </div>
    </div>

    <!-- Filter Card -->
    <vx-card no-shadow class="mb-4 shadow-sm border border-solid border-grey-light rounded-lg">
      <div class="vx-row">
        <!-- Trung tâm -->
        <div class="vx-col md:w-1/2 sm:w-1/2 w-full mb-3">
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
        <div class="vx-col md:w-1/2 sm:w-1/2 w-full mb-3">
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
        <div v-if="!contracts || contracts.length === 0" class="text-center py-8 text-gray-500 bg-white rounded-lg border border-dashed border-gray-300">
          <i class="fa-solid fa-folder-open text-gray-300 mb-2" style="font-size: 36px; display: block;"></i>
          Không tìm thấy hợp đồng chờ thu phí nào
        </div>

        <div
          v-for="(item, index) in contracts"
          :key="'card-' + index"
          class="mobile-item-card mb-3 p-3 bg-white rounded-lg border border-solid border-grey-light shadow-sm"
        >
          <!-- Header -->
          <div class="flex items-start justify-between gap-2 pb-2.5 border-b border-gray-100">
            <div class="min-w-0">
              <router-link :to="`/lms/waitcharge/${item.contract_id}/detail`" class="font-bold text-gray-900 hover:text-primary text-base truncate block">
                {{ item.name }}
              </router-link>
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
              <span class="debt-tag text-xs font-bold">
                {{ item.debt_amount | formatMoney }}
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

            <div class="flex items-center justify-between text-gray-500 text-xs pt-1 border-t border-dashed border-gray-100" v-if="item.ec_name">
              <span><i class="fa-solid fa-user-tag text-gray-400 mr-1"></i>EC tư vấn:</span>
              <span class="font-semibold text-gray-800">{{ item.ec_name }}</span>
            </div>
          </div>

          <!-- Actions -->
          <div class="pt-2 border-t border-gray-100">
            <router-link :to="`/lms/waitcharge/${item.contract_id}/detail`" class="w-full block">
              <vs-button size="small" color="success" type="relief" class="w-full flex items-center justify-center font-semibold py-2">
                <i class="fa-solid fa-file-invoice-dollar mr-1.5"></i> Thu phí ngay
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
                <th style="width: 220px">Học sinh</th>
                <th style="width: 230px">Hợp đồng</th>
                <th style="width: 240px">Khóa học & Gói phí</th>
                <th style="width: 170px">EC tư vấn</th>
                <th style="width: 180px" class="text-right">Số tiền còn phải đóng</th>
                <th style="width: 120px" class="text-center">Thao tác</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="!contracts || contracts.length === 0">
                <td colspan="7" class="text-center py-8 text-gray-500">
                  <i class="fa-solid fa-folder-open text-gray-300 mb-2" style="font-size: 36px; display: block;"></i>
                  Không tìm thấy hợp đồng chờ thu phí nào
                </td>
              </tr>

              <tr v-for="(item, index) in contracts" :key="index" class="table-row-hover">
                <!-- STT -->
                <td class="text-center text-gray-500 font-semibold">
                  {{ index + 1 + (pagination.cpage - 1) * pagination.limit }}
                </td>

                <!-- Học sinh -->
                <td>
                  <div class="item-title-text mb-1">
                    <router-link :to="`/lms/waitcharge/${item.contract_id}/detail`" class="hover:text-primary transition-colors">
                      {{ item.name }}
                    </router-link>
                  </div>
                  <div class="flex items-center gap-1.5 flex-wrap">
                    <span class="stk-code-badge" @click="copyCode(item.lms_code)" title="Bấm để sao chép mã học sinh">
                      <i class="fa-solid fa-id-badge mr-1"></i>
                      <strong>{{ item.lms_code || '---' }}</strong>
                      <i class="fa-regular fa-copy ml-1 opacity-70"></i>
                    </span>
                  </div>
                </td>

                <!-- Hợp đồng -->
                <td>
                  <router-link :to="`/lms/waitcharge/${item.contract_id}/detail`">
                    <span class="contract-code-badge" title="Xem chi tiết hợp đồng chờ thu">
                      <i class="fa-solid fa-file-contract mr-1"></i>
                      <strong>{{ item.code }}</strong>
                    </span>
                  </router-link>
                </td>

                <!-- Khóa học & Gói phí -->
                <td>
                  <div class="text-gray-800 font-semibold text-sm mb-0.5" v-if="item.product_name">
                    <i class="fa-solid fa-book-open text-indigo-500 mr-1 text-xs"></i>{{ item.product_name }}
                  </div>
                  <div class="text-xs text-gray-500" v-if="item.tuition_fee_name">
                    Gói phí: {{ item.tuition_fee_name }}
                  </div>
                </td>

                <!-- EC tư vấn -->
                <td>
                  <div class="text-sm font-medium text-gray-700 flex items-center">
                    <i class="fa-solid fa-user-tag text-gray-400 mr-1.5 text-xs"></i>
                    {{ item.ec_name || 'Chưa cập nhật' }}
                  </div>
                </td>

                <!-- Số tiền còn phải đóng -->
                <td class="text-right">
                  <span class="debt-tag font-bold text-base">
                    {{ item.debt_amount | formatMoney }}
                  </span>
                </td>

                <!-- Thao tác -->
                <td class="text-center">
                  <div class="flex items-center justify-center gap-1.5">
                    <router-link :to="`/lms/waitcharge/${item.contract_id}/detail`">
                      <button class="action-charge-btn" title="Thu tiền cho hợp đồng này">
                        <i class="fa-solid fa-file-invoice-dollar mr-1"></i> Thu phí
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

export default {
  components: {
    vSelect,
    Multiselect
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
      contracts: [],
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
      this.getData();
    },
    getData() {
      const ids_branch = [];
      if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
        this.searchData.arr_branch.map((item) => {
          ids_branch.push(item.id);
        });
      }
      this.searchData.branch_id = ids_branch;
      const data = {
        keyword: this.searchData.keyword,
        branch_id: this.searchData.branch_id,
        pagination: this.pagination
      };

      this.$vs.loading();
      axios
        .p('/api/lms/accounting/waitcharges/list', data)
        .then((response) => {
          this.$vs.loading.close();
          this.contracts = response.data.list;
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
#page-waitcharges-list {
  width: 100% !important;
  max-width: 100% !important;
  overflow-x: hidden !important;
}

.waitcharges-page-header {
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
  background-color: #fffbeb;
  color: #b45309;
  font-size: 13.5px;
  padding: 6px 14px;
  border-radius: 9999px;
  border: 1px solid #fde68a;
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
  .waitcharges-page-header {
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
  min-width: 900px;
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

.debt-tag {
  display: inline-flex;
  align-items: center;
  color: #dc2626;
  background-color: #fee2e2;
  padding: 3px 8px;
  border-radius: 5px;
  border: 1px solid #fecaca;
}

.action-charge-btn {
  padding: 6px 12px;
  border-radius: 6px;
  background: #dcfce7;
  color: #15803d;
  border: 1px solid #bbf7d0;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  transition: all 0.2s ease;
}

.action-charge-btn:hover {
  background: #15803d;
  color: #ffffff;
  box-shadow: 0 2px 6px rgba(21, 128, 61, 0.25);
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
