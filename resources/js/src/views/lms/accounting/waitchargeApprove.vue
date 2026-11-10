<template>
  <div id="page-waitcharge-approve-list">
    <!-- Header Banner -->
    <div class="approve-page-header">
      <div>
        <h4 class="font-bold text-gray-800 flex items-center text-lg m-0">
          <i class="fa-solid fa-clipboard-check text-primary mr-2" style="font-size: 20px;"></i>
          Phê Duyệt Phiếu Thu Học Phí
        </h4>
        <span class="text-gray-500 text-sm">Kiểm tra, xác thực và phê duyệt các khoản thu học phí từ bộ phận tư vấn</span>
      </div>
      <div class="approve-header-badge">
        <span class="badge-total-items">
          <i class="fa-solid fa-receipt mr-1.5"></i>
          Tổng cộng: <strong>{{ pagination.total }}</strong> phiếu thu
        </span>
      </div>
    </div>

    <!-- Filter Card -->
    <vx-card no-shadow class="mb-4 shadow-sm border border-solid border-grey-light rounded-lg">
      <div class="vx-row">
        <!-- Trung tâm -->
        <div class="vx-col md:w-1/4 sm:w-1/2 w-full mb-3">
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
        <div class="vx-col md:w-1/4 sm:w-1/2 w-full mb-3">
          <label class="filter-label"><i class="fa-solid fa-magnifying-glass text-primary mr-1"></i> Tìm kiếm</label>
          <div class="filter-search-wrapper">
            <i class="fa-solid fa-search filter-search-icon"></i>
            <input
              type="text"
              class="filter-input-text"
              placeholder="Mã phiếu, mã HS, tên HS, mã HĐ..."
              v-model="searchData.keyword"
              @keyup.enter="getData"
            />
          </div>
        </div>

        <!-- Trạng thái phiếu thu -->
        <div class="vx-col md:w-1/4 sm:w-1/2 w-full mb-3">
          <label class="filter-label"><i class="fa-solid fa-filter text-primary mr-1"></i> Trạng thái phiếu thu</label>
          <select class="custom-select-box" v-model="searchData.status" @change="getData">
            <option value="">-- Tất cả trạng thái --</option>
            <option value="0">Chờ duyệt</option>
            <option value="1">Đã duyệt</option>
            <option value="2">Từ chối duyệt</option>
          </select>
        </div>

        <!-- Ngày đóng phí -->
        <div class="vx-col md:w-1/4 sm:w-1/2 w-full mb-3">
          <label class="filter-label"><i class="fa-regular fa-calendar-days text-primary mr-1"></i> Ngày đóng phí</label>
          <date-picker
            name="item-date"
            class="w-full custom-datepicker"
            v-model="searchData.dateRange"
            range
            format="YYYY-MM-DD"
            style="width: 100%"
            :clearable="true"
            :lang="datepickerOptions.lang"
            placeholder="Chọn khoảng thời gian"
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
        <div v-if="!contracts || contracts.length === 0" class="text-center py-8 text-gray-500 bg-white rounded-lg border border-dashed border-gray-300">
          <i class="fa-solid fa-folder-open text-gray-300 mb-2" style="font-size: 36px; display: block;"></i>
          Không tìm thấy phiếu thu nào phù hợp
        </div>

        <div
          v-for="(item, index) in contracts"
          :key="'card-' + index"
          class="mobile-item-card mb-3 p-3 bg-white rounded-lg border border-solid border-grey-light shadow-sm"
        >
          <!-- Header -->
          <div class="flex items-start justify-between gap-2 pb-2.5 border-b border-gray-100">
            <div class="min-w-0">
              <router-link :to="`/lms/waitcharge-approve/${item.id}/detail`" class="font-bold text-gray-900 hover:text-primary text-base truncate block">
                {{ item.name }}
              </router-link>
              <div class="flex flex-wrap items-center gap-1.5 mt-1">
                <span class="receipt-code-badge" @click="copyCode(item.receipt_code || item.code)" title="Bấm để sao chép mã phiếu thu">
                  <i class="fa-solid fa-receipt mr-1"></i>
                  <strong>{{ item.receipt_code || item.code }}</strong>
                  <i class="fa-regular fa-copy ml-1 opacity-70"></i>
                </span>
                <span class="contract-code-badge" @click="copyCode(item.contract_code)" title="Bấm để sao chép mã hợp đồng">
                  <i class="fa-solid fa-file-contract mr-1"></i>
                  <strong>{{ item.contract_code }}</strong>
                  <i class="fa-regular fa-copy ml-1 opacity-70"></i>
                </span>
              </div>
            </div>
            <div class="flex flex-col items-end gap-1 flex-shrink-0">
              <span class="text-xs text-gray-400 font-semibold">
                #{{ index + 1 + (pagination.cpage - 1) * pagination.limit }}
              </span>
              <span :class="getApproveBadgeClass(item.status)" class="text-xs font-semibold">
                {{ item.status == 0 ? 'Chờ duyệt' : (item.status == 1 ? 'Đã duyệt' : 'Từ chối') }}
              </span>
            </div>
          </div>

          <!-- Body -->
          <div class="py-2.5 text-sm space-y-1.5">
            <div class="flex items-center justify-between text-gray-600">
              <span class="text-gray-500"><i class="fa-solid fa-money-bill-wave text-success mr-1.5"></i>Số tiền thu:</span>
              <span class="font-bold text-danger text-base">{{ item.charge_amount | formatMoney }}</span>
            </div>

            <div class="flex items-center justify-between text-gray-600">
              <span class="text-gray-500"><i class="fa-solid fa-credit-card text-gray-400 mr-1.5"></i>Hình thức:</span>
              <span class="method-badge">
                <i :class="item.method == 0 ? 'fa-solid fa-money-bill' : (item.method == 1 ? 'fa-solid fa-building-columns' : 'fa-solid fa-credit-card')" class="mr-1"></i>
                {{ item.method == 0 ? 'Tiền mặt' : (item.method == 1 ? 'Chuyển khoản' : 'Thẻ tín dụng') }}
              </span>
            </div>

            <div class="flex items-center justify-between text-gray-600">
              <span class="text-gray-500"><i class="fa-solid fa-building text-primary mr-1.5"></i>Trung tâm:</span>
              <span class="font-medium text-gray-800">{{ item.branch_name }}</span>
            </div>

            <div class="flex items-center justify-between text-gray-600" v-if="item.product_name">
              <span class="text-gray-500"><i class="fa-solid fa-book-open text-indigo-500 mr-1.5"></i>Khóa học:</span>
              <span class="text-gray-700">{{ item.product_name }}</span>
            </div>

            <div class="flex items-center justify-between text-gray-500 text-xs pt-1 border-t border-dashed border-gray-100">
              <span><i class="fa-regular fa-calendar text-gray-400 mr-1"></i>Ngày thu: <strong>{{ item.charge_date }}</strong></span>
              <span v-if="item.creator_name"><i class="fa-solid fa-user text-gray-400 mr-1"></i>Sale: <strong>{{ item.creator_name }}</strong></span>
            </div>

            <div class="flex items-center justify-between text-gray-500 text-xs" v-if="item.approver_name">
              <span><i class="fa-solid fa-user-check text-emerald-500 mr-1"></i>Người duyệt: <strong>{{ item.approver_name }}</strong></span>
              <span>{{ item.approved_at }}</span>
            </div>
          </div>

          <!-- Actions -->
          <div class="pt-2 border-t border-gray-100 flex flex-wrap gap-2">
            <router-link :to="`/lms/waitcharge-approve/${item.id}/detail`" class="flex-1">
              <vs-button size="small" color="primary" type="relief" class="w-full flex items-center justify-center font-semibold">
                <i class="fa-solid fa-eye mr-1.5"></i> Chi tiết
              </vs-button>
            </router-link>
            <vs-button
              v-if="item.status == 0 && canApprove"
              size="small"
              color="success"
              type="relief"
              class="flex-1 flex items-center justify-center font-semibold"
              @click="confirmApprove(item)"
            >
              <i class="fa-solid fa-check mr-1.5"></i> Duyệt
            </vs-button>
            <vs-button
              v-if="item.status == 0 && canApprove"
              size="small"
              color="danger"
              type="relief"
              class="flex-1 flex items-center justify-center font-semibold"
              @click="confirmReject(item)"
            >
              <i class="fa-solid fa-times mr-1.5"></i> Từ chối
            </vs-button>
            <router-link :to="`/pages/print/receipt/${item.id}`" target="_blank">
              <vs-button size="small" color="#f59e0b" type="relief" title="In phiếu thu">
                <i class="fa-solid fa-print"></i>
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
                <th class="text-center" style="width: 45px">STT</th>
                <th style="width: 170px">Mã phiếu & HĐ</th>
                <th style="width: 200px">Học sinh</th>
                <th style="width: 190px">Trung tâm & Khóa học</th>
                <th style="width: 160px" class="text-right">Số tiền thu</th>
                <th style="width: 130px" class="text-center">Hình thức</th>
                <th style="width: 120px" class="text-center">Ngày thu</th>
                <th style="width: 140px">Người tạo</th>
                <th style="width: 120px" class="text-center">Trạng thái</th>
                <th style="width: 140px">Người duyệt</th>
                <th style="width: 140px" class="text-center">Thao tác</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="!contracts || contracts.length === 0">
                <td colspan="11" class="text-center py-8 text-gray-500">
                  <i class="fa-solid fa-folder-open text-gray-300 mb-2" style="font-size: 36px; display: block;"></i>
                  Không tìm thấy phiếu thu nào phù hợp
                </td>
              </tr>

              <tr v-for="(item, index) in contracts" :key="index" class="table-row-hover">
                <!-- STT -->
                <td class="text-center text-gray-500 font-semibold">
                  {{ index + 1 + (pagination.cpage - 1) * pagination.limit }}
                </td>

                <!-- Mã phiếu & HĐ -->
                <td>
                  <div class="mb-1">
                    <router-link :to="`/lms/waitcharge-approve/${item.id}/detail`">
                      <span class="receipt-code-badge" title="Mã phiếu thu - Bấm xem chi tiết">
                        <i class="fa-solid fa-receipt mr-1"></i>
                        <strong>{{ item.receipt_code || item.code }}</strong>
                      </span>
                    </router-link>
                  </div>
                  <div>
                    <router-link :to="`/lms/contracts/${item.contract_id}/detail`">
                      <span class="contract-code-badge" title="Mã hợp đồng">
                        <i class="fa-solid fa-file-contract mr-1"></i>
                        <strong>{{ item.contract_code }}</strong>
                      </span>
                    </router-link>
                  </div>
                </td>

                <!-- Học sinh -->
                <td>
                  <div class="item-title-text mb-0.5">
                    {{ item.name }}
                  </div>
                  <div class="flex items-center gap-1">
                    <span class="stk-code-badge" @click="copyCode(item.lms_code)" title="Bấm để sao chép mã học sinh">
                      <i class="fa-solid fa-id-badge mr-1"></i>
                      <strong>{{ item.lms_code || '---' }}</strong>
                      <i class="fa-regular fa-copy ml-1 opacity-70"></i>
                    </span>
                  </div>
                </td>

                <!-- Trung tâm & Khóa học -->
                <td>
                  <div class="font-semibold text-gray-800 text-xs mb-0.5 flex items-center">
                    <i class="fa-solid fa-building text-primary mr-1 text-xs"></i>
                    {{ item.branch_name }}
                  </div>
                  <div class="text-xs text-gray-700 truncate" :title="item.product_name" v-if="item.product_name">
                    <i class="fa-solid fa-book-open text-indigo-500 mr-1 text-xs"></i>{{ item.product_name }}
                  </div>
                  <div class="text-xs text-gray-400 truncate" v-if="item.tuition_fee_name">
                    {{ item.tuition_fee_name }}
                  </div>
                </td>

                <!-- Số tiền thu -->
                <td class="text-right">
                  <span class="amount-strong text-danger">
                    {{ item.charge_amount | formatMoney }}
                  </span>
                </td>

                <!-- Hình thức -->
                <td class="text-center">
                  <span class="method-badge">
                    <i :class="item.method == 0 ? 'fa-solid fa-money-bill' : (item.method == 1 ? 'fa-solid fa-building-columns' : 'fa-solid fa-credit-card')" class="mr-1"></i>
                    {{ item.method == 0 ? 'Tiền mặt' : (item.method == 1 ? 'Chuyển khoản' : 'Thẻ tín dụng') }}
                  </span>
                </td>

                <!-- Ngày thu -->
                <td class="text-center text-xs text-gray-700 font-medium">
                  {{ item.charge_date }}
                </td>

                <!-- Người tạo -->
                <td>
                  <div class="text-xs font-semibold text-gray-800 mb-0.5">
                    {{ item.creator_name || '—' }}
                  </div>
                  <div class="text-xs text-gray-400" v-if="item.created_at">
                    {{ item.created_at }}
                  </div>
                </td>

                <!-- Trạng thái -->
                <td class="text-center">
                  <span :class="getApproveBadgeClass(item.status)">
                    {{ item.status == 0 ? 'Chờ duyệt' : (item.status == 1 ? 'Đã duyệt' : 'Từ chối') }}
                  </span>
                </td>

                <!-- Người duyệt -->
                <td>
                  <div class="text-xs font-semibold text-gray-800 mb-0.5" v-if="item.approver_name">
                    {{ item.approver_name }}
                  </div>
                  <div class="text-xs text-gray-400" v-if="item.approved_at">
                    {{ item.approved_at }}
                  </div>
                  <span v-if="!item.approver_name" class="text-gray-400 text-xs">—</span>
                </td>

                <!-- Thao tác -->
                <td class="text-center">
                  <div class="flex items-center justify-center gap-1">
                    <router-link :to="`/lms/waitcharge-approve/${item.id}/detail`">
                      <button class="action-circle-btn action-view" title="Xem chi tiết phiếu thu">
                        <i class="fa-solid fa-eye"></i>
                      </button>
                    </router-link>
                    <button
                      v-if="item.status == 0 && canApprove"
                      class="action-circle-btn action-approve"
                      @click="confirmApprove(item)"
                      title="Duyệt phiếu thu"
                    >
                      <i class="fa-solid fa-check"></i>
                    </button>
                    <button
                      v-if="item.status == 0 && canApprove"
                      class="action-circle-btn action-reject"
                      @click="confirmReject(item)"
                      title="Từ chối duyệt"
                    >
                      <i class="fa-solid fa-times"></i>
                    </button>
                    <router-link :to="`/pages/print/receipt/${item.id}`" target="_blank">
                      <button class="action-circle-btn action-print-warning" title="In phiếu thu">
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
import datepicker from 'vue2-datepicker';
import moment from 'moment';

export default {
  components: {
    vSelect,
    Multiselect,
    datepicker
  },
  data() {
    return {
      windowWidth: typeof window !== 'undefined' ? window.innerWidth : 1024,
      viewMode: 'table',
      branch_list: [],
      datepickerOptions: {
        lang: {
          days: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
          months: [
            'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6',
            'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'
          ]
        }
      },
      searchData: {
        arr_branch: '',
        branch_id: '',
        keyword: '',
        status: '',
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
    },
    canApprove() {
      const user = this.$store.state.AppActiveUser || {};
      return (
        u.checkPermission(user, 'approve_add_fee') ||
        u.checkPermission(user, 'lms_charges') ||
        u.checkPermission(user, 'lms_waitcharges') ||
        [999999, 686868, 1000002].includes(Number(user.role_id)) ||
        ['admin', 'cashier', 'ceo'].includes(user.roleName)
      );
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
    getApproveBadgeClass(status) {
      if (status == 0) return 'status-badge status-warning';
      if (status == 1) return 'status-badge status-success';
      if (status == 2) return 'status-badge status-danger';
      return 'status-badge status-default';
    },
    reset() {
      this.searchData.keyword = '';
      this.searchData.arr_branch = '';
      this.searchData.branch_id = '';
      this.searchData.status = '';
      this.searchData.dateRange = '';
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
        status: this.searchData.status,
        start_date:
          this.searchData.dateRange && this.searchData.dateRange[0]
            ? moment(this.searchData.dateRange[0]).format('YYYY-MM-DD')
            : '',
        end_date:
          this.searchData.dateRange && this.searchData.dateRange[1]
            ? moment(this.searchData.dateRange[1]).format('YYYY-MM-DD')
            : '',
        pagination: this.pagination
      };

      this.$vs.loading();
      axios
        .p('/api/lms/accounting/waitcharge-approve/list', data)
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
    confirmApprove(item) {
      this.$vs.dialog({
        type: 'confirm',
        color: 'success',
        title: 'Xác nhận duyệt phiếu thu',
        text: `Bạn có chắc chắn muốn duyệt phiếu thu ${item.receipt_code || item.code} của học sinh ${item.name} với số tiền ${u.formatMoney(item.charge_amount)}? Tiền sẽ được ghi nhận vào hợp đồng.`,
        acceptText: 'Duyệt phiếu thu',
        cancelText: 'Hủy',
        accept: () => {
          this.processApproval(item.id, 1);
        }
      });
    },
    confirmReject(item) {
      this.$vs.dialog({
        type: 'prompt',
        color: 'danger',
        title: 'Từ chối duyệt phiếu thu',
        text: `Nhập lý do từ chối duyệt phiếu thu ${item.receipt_code || item.code}:`,
        inputPlaceholder: 'Lý do từ chối...',
        acceptText: 'Từ chối',
        cancelText: 'Hủy',
        accept: (val) => {
          this.processApproval(item.id, 2, val);
        }
      });
    },
    processApproval(id, status, rejectReason = '') {
      this.$vs.loading();
      axios
        .p('/api/lms/accounting/waitcharge-approve/update', {
          id: id,
          status: status,
          reject_reason: rejectReason
        })
        .then((response) => {
          this.$vs.loading.close();
          this.$vs.notify({
            title: 'Thành công',
            text:
              response.data.message ||
              (status === 1 ? 'Duyệt thành công' : 'Từ chối duyệt thành công'),
            color: status === 1 ? 'success' : 'warning',
            iconPack: 'feather',
            icon: status === 1 ? 'icon-check' : 'icon-alert-triangle'
          });
          this.getData();
        })
        .catch((err) => {
          this.$vs.loading.close();
          this.$vs.notify({
            title: 'Lỗi',
            text:
              err.response && err.response.data && err.response.data.message
                ? err.response.data.message
                : 'Có lỗi xảy ra',
            color: 'danger',
            iconPack: 'feather',
            icon: 'icon-alert-circle'
          });
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
#page-waitcharge-approve-list {
  width: 100% !important;
  max-width: 100% !important;
  overflow-x: hidden !important;
}

.approve-page-header {
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

.filter-input-text:focus,
.custom-select-box:focus {
  border-color: #7367f0 !important;
  box-shadow: 0 0 0 2px rgba(115, 103, 240, 0.15) !important;
}

.custom-select-box {
  box-sizing: border-box !important;
  width: 100% !important;
  height: 38px !important;
  border: 1px solid #cbd5e1 !important;
  border-radius: 6px !important;
  padding: 6px 12px !important;
  font-size: 14px !important;
  color: #1e293b !important;
  outline: none !important;
  background: #ffffff !important;
  transition: all 0.2s ease !important;
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
  .approve-page-header {
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
  min-width: 1100px;
  border-collapse: separate;
  border-spacing: 0;
  font-size: 13px;
}

.modern-lms-table thead th {
  background: #f8fafc;
  color: #475569;
  font-weight: 700;
  font-size: 12.5px;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  padding: 12px 12px;
  border-bottom: 2px solid #e2e8f0;
  white-space: nowrap;
}

.modern-lms-table tbody td {
  padding: 12px 12px;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
}

.table-row-hover:hover {
  background-color: #f8fafc;
}

.item-title-text {
  font-size: 14px;
  font-weight: 700;
  color: #1e293b;
}

.stk-code-badge {
  display: inline-flex;
  align-items: center;
  font-size: 12px;
  font-weight: 700;
  color: #7367f0;
  background-color: #f5f3ff;
  border: 1px solid #ddd6fe;
  padding: 2px 6px;
  border-radius: 5px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.stk-code-badge:hover {
  background-color: #ede9fe;
}

.receipt-code-badge {
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
  transition: all 0.2s ease;
}

.receipt-code-badge:hover {
  background-color: #ede9fe;
}

.contract-code-badge {
  display: inline-flex;
  align-items: center;
  font-size: 12px;
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

.amount-strong {
  font-size: 14.5px;
  font-weight: 700;
}

.method-badge {
  display: inline-flex;
  align-items: center;
  background: #f1f5f9;
  color: #475569;
  padding: 2px 7px;
  border-radius: 4px;
  font-size: 11.5px;
  font-weight: 600;
  white-space: nowrap;
}

.status-badge {
  display: inline-block;
  padding: 3px 8px;
  border-radius: 5px;
  font-size: 12px;
  font-weight: 600;
  white-space: nowrap;
}

.status-success {
  background-color: #dcfce7;
  color: #15803d;
}

.status-warning {
  background-color: #fef3c7;
  color: #b45309;
}

.status-danger {
  background-color: #fee2e2;
  color: #b91c1c;
}

.status-default {
  background-color: #f1f5f9;
  color: #475569;
}

/* Action Circle Buttons */
.action-circle-btn {
  width: 30px;
  height: 30px;
  border-radius: 6px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 12.5px;
  border: 1px solid transparent;
  cursor: pointer;
  transition: all 0.2s ease;
}

.action-view {
  background: #f5f3ff;
  color: #7367f0;
  border-color: #ddd6fe;
}
.action-view:hover {
  background: #7367f0;
  color: #ffffff;
}

.action-approve {
  background: #dcfce7;
  color: #15803d;
  border-color: #bbf7d0;
}
.action-approve:hover {
  background: #15803d;
  color: #ffffff;
}

.action-reject {
  background: #fee2e2;
  color: #dc2626;
  border-color: #fecaca;
}
.action-reject:hover {
  background: #dc2626;
  color: #ffffff;
}

.action-print-warning {
  background: #fffbeb;
  color: #d97706;
  border-color: #fde68a;
}
.action-print-warning:hover {
  background: #d97706;
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
