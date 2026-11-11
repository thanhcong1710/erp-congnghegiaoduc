<template>
  <div class="student-detail-contracts mt-2">
    <!-- Empty state -->
    <div v-if="!contracts || contracts.length === 0" class="empty-state-box">
      <i class="fa-solid fa-file-invoice-dollar empty-state-icon"></i>
      <h5 class="empty-state-title">Chưa có gói phí / hợp đồng nào</h5>
      <p class="empty-state-desc">Học sinh chưa được tạo gói phí nhập học hoặc nạp phí trên hệ thống.</p>
      <router-link :to="`/lms/contracts/add?student_id=${student_info.id}&branch_id=${student_info.branch_id || ''}`" class="mt-3 inline-block">
        <vs-button color="success" class="shadow-md">
          <i class="fa fa-plus-circle mr-1.5"></i> Tạo gói phí / Nhập học ngay
        </vs-button>
      </router-link>
    </div>

    <div v-else>
      <!-- Top Action Bar -->
      <div class="flex items-center justify-between mb-3">
        <span class="text-sm text-gray-500 font-medium">Hồ sơ gói học phí & hợp đồng nhập học</span>
        <router-link :to="`/lms/contracts/add?student_id=${student_info.id}&branch_id=${student_info.branch_id || ''}`">
          <vs-button color="primary" size="small" class="shadow-sm">
            <i class="fa fa-plus-circle mr-1"></i> Nhập học / Thêm gói phí
          </vs-button>
        </router-link>
      </div>
      <!-- 1. Top Summary KPI Cards -->
      <div class="vx-row mb-4">
        <div class="vx-col sm:w-1/4 w-full mb-2">
          <div class="kpi-summary-card border-l-blue">
            <div class="kpi-icon-box bg-blue-50 text-blue-600">
              <i class="fa-solid fa-file-contract"></i>
            </div>
            <div class="kpi-info">
              <div class="kpi-label">Tổng số gói phí</div>
              <div class="kpi-value text-gray-800">{{ summaryStats.totalContracts }} <span class="kpi-unit">gói</span></div>
            </div>
          </div>
        </div>

        <div class="vx-col sm:w-1/4 w-full mb-2">
          <div class="kpi-summary-card border-l-indigo">
            <div class="kpi-icon-box bg-indigo-50 text-indigo-600">
              <i class="fa-solid fa-money-bill-wave"></i>
            </div>
            <div class="kpi-info">
              <div class="kpi-label">Tổng học phí phải đóng</div>
              <div class="kpi-value text-indigo-700">{{ summaryStats.totalMustCharge | formatMoney }}</div>
            </div>
          </div>
        </div>

        <div class="vx-col sm:w-1/4 w-full mb-2">
          <div class="kpi-summary-card border-l-emerald">
            <div class="kpi-icon-box bg-emerald-50 text-emerald-600">
              <i class="fa-solid fa-circle-dollar-to-slot"></i>
            </div>
            <div class="kpi-info">
              <div class="kpi-label">Tổng đã thu</div>
              <div class="kpi-value text-emerald-600">{{ summaryStats.totalCharged | formatMoney }}</div>
              <div class="kpi-sub-text" v-if="summaryStats.totalDebt > 0">
                Còn nợ: <strong class="text-rose-600">{{ summaryStats.totalDebt | formatMoney }}</strong>
              </div>
              <div class="kpi-sub-text text-emerald-600 font-medium" v-else>
                <i class="fa-solid fa-check-double mr-1"></i> Đã hoàn tất thanh toán
              </div>
            </div>
          </div>
        </div>

        <div class="vx-col sm:w-1/4 w-full mb-2">
          <div class="kpi-summary-card border-l-amber">
            <div class="kpi-icon-box bg-amber-50 text-amber-600">
              <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div class="kpi-info">
              <div class="kpi-label">Tổng số buổi học</div>
              <div class="kpi-value text-amber-600">{{ summaryStats.totalSessions }} <span class="kpi-unit">buổi</span></div>
            </div>
          </div>
        </div>
      </div>

      <!-- 2. Modern Clean Contracts Table -->
      <div class="contracts-table-card">
        <div class="table-responsive">
          <table class="modern-contracts-table">
            <thead>
              <tr>
                <th class="th-stt">STT</th>
                <th style="min-width: 170px;">Hợp đồng & Thời gian</th>
                <th style="min-width: 250px;">Gói phí & Khóa học</th>
                <th style="min-width: 210px;">Cơ sở & Lớp học</th>
                <th style="min-width: 180px;">Số buổi & Lộ trình</th>
                <th style="min-width: 230px;">Tài chính & Thu phí</th>
                <th style="min-width: 170px;" class="text-center">Trạng thái</th>
                <th style="width: 100px;" class="text-center">Thao tác</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in contracts" :key="index" class="contract-row">
                <!-- STT -->
                <td class="text-center font-bold text-gray-400 td-stt">
                  {{ index + 1 }}
                </td>

                <!-- Col 1: Hợp đồng & Thời gian -->
                <td class="align-top">
                  <div class="mb-1.5 flex items-center">
                    <router-link
                      :to="`/lms/contracts/${item.contract_id}/detail`"
                      class="contract-code-tag"
                      title="Bấm để xem chi tiết hợp đồng"
                    >
                      <i class="fa-solid fa-file-contract mr-1.5 text-primary"></i>
                      <span>{{ item.code }}</span>
                    </router-link>
                    <button
                      class="copy-btn-mini"
                      @click="copyText(item.code, 'mã hợp đồng')"
                      title="Sao chép mã"
                    >
                      <i class="fa-regular fa-copy"></i>
                    </button>
                  </div>
                  <div class="meta-item">
                    <i class="fa-regular fa-clock text-gray-400 mr-1"></i>
                    <span>{{ item.created_at }}</span>
                  </div>
                  <div class="meta-item" v-if="item.creator_name">
                    <i class="fa-regular fa-user text-gray-400 mr-1"></i>
                    <span class="text-gray-600">{{ item.creator_name }}</span>
                  </div>
                </td>

                <!-- Col 2: Gói phí & Khóa học -->
                <td class="align-top">
                  <div class="mb-1" v-if="item.product_name">
                    <span class="product-badge">
                      <i class="fa-solid fa-book-open mr-1"></i> {{ item.product_name }}
                    </span>
                  </div>
                  <div class="contract-name-title">{{ item.tuition_fee_name || 'Gói học phí tiêu chuẩn' }}</div>
                </td>

                <!-- Col 3: Cơ sở & Lớp học -->
                <td class="align-top">
                  <div class="branch-title mb-1.5">
                    <i class="fa-solid fa-school text-primary mr-1.5"></i>
                    <span>{{ item.branch_name || 'Chưa phân cơ sở' }}</span>
                  </div>
                  <div v-if="item.class_name" class="class-pill mb-1.5">
                    <i class="fa-solid fa-chalkboard mr-1.5 text-indigo-500"></i>
                    <span>{{ item.class_name }}</span>
                  </div>
                  <div class="staff-assignment">
                    <span v-if="item.ec_name" class="staff-chip" title="Nhân viên tư vấn (EC)">
                      <span class="chip-label">EC:</span> {{ item.ec_name }}
                    </span>
                    <span v-if="item.cm_name" class="staff-chip" title="Chăm sóc học viên (CM)">
                      <span class="chip-label">CM:</span> {{ item.cm_name }}
                    </span>
                  </div>
                </td>

                <!-- Col 4: Số buổi & Lộ trình -->
                <td class="align-top">
                  <div class="session-highlight-box mb-2">
                    <div class="session-main-count">
                      <span class="count-number">{{ item.debt_amount == 0 ? item.summary_sessions : item.total_sessions }}</span>
                      <span class="count-unit">buổi</span>
                      <span v-if="item.bonus_sessions" class="bonus-tag">
                        +{{ item.bonus_sessions }} HB
                      </span>
                    </div>
                    <div class="session-sub-info">
                      Bảo lưu: <strong>{{ item.reserved_sessions || 0 }}</strong> / {{ item.reservable_sessions || 0 }} buổi
                    </div>
                  </div>

                  <div class="roadmap-timeline">
                    <div v-if="item.enrolment_start_date" class="timeline-row start-date">
                      <i class="fa-regular fa-calendar-check text-emerald-600 mr-1"></i>
                      <span>Bắt đầu: <strong>{{ item.enrolment_start_date }}</strong></span>
                    </div>
                    <div v-if="item.enrolment_last_date" class="timeline-row end-date">
                      <i class="fa-regular fa-calendar-xmark text-rose-500 mr-1"></i>
                      <span>Kết thúc: <strong>{{ item.enrolment_last_date }}</strong></span>
                    </div>
                  </div>
                </td>

                <!-- Col 5: Tài chính & Thu phí -->
                <td class="align-top">
                  <div class="finance-compact-card">
                    <div class="finance-entry" v-if="item.init_tuition_fee_amount && item.init_tuition_fee_amount != item.must_charge">
                      <span class="entry-label">Giá niêm yết:</span>
                      <span class="entry-val text-gray-500 line-through">{{ item.init_tuition_fee_amount | formatMoney }}</span>
                    </div>
                    <div class="finance-entry">
                      <span class="entry-label font-medium text-gray-700">Phải đóng:</span>
                      <span class="entry-val font-semibold text-gray-900">{{ item.must_charge | formatMoney }}</span>
                    </div>
                    <div class="finance-entry entry-highlight-paid">
                      <span class="entry-label font-bold text-emerald-800">Đã đóng:</span>
                      <span class="entry-val font-bold text-emerald-700 text-sm">{{ item.total_charged | formatMoney }}</span>
                    </div>
                    <div v-if="item.debt_amount > 0" class="finance-entry entry-highlight-debt">
                      <span class="entry-label font-bold text-rose-800">Còn nợ:</span>
                      <span class="entry-val font-bold text-rose-700 text-sm">{{ item.debt_amount | formatMoney }}</span>
                    </div>
                    <div v-else class="paid-full-badge mt-1">
                      <i class="fa-solid fa-circle-check text-emerald-600 mr-1"></i> Đã đóng đủ 100%
                    </div>
                  </div>

                  <!-- Quy đổi tiền cọc button if debt > 0 and deposited -->
                  <vs-button
                    v-if="item.debt_amount > 0 && item.total_charged && item.status == 2"
                    size="small"
                    color="warning"
                    type="relief"
                    class="mt-2 w-full font-semibold"
                    @click="confirmExitDepost(item.contract_id)"
                  >
                    <i class="fa-solid fa-arrow-right-arrow-left mr-1"></i> Quy đổi cọc
                  </vs-button>
                </td>

                <!-- Col 6: Trạng thái -->
                <td class="align-top text-center">
                  <div class="inline-flex flex-col items-center">
                    <span class="contract-status-pill" :class="getStatusBadgeInfo(item).class">
                      <i :class="getStatusBadgeInfo(item).icon" class="status-dot-icon"></i>
                      <span>{{ getStatusBadgeInfo(item).label }}</span>
                    </span>
                  </div>
                </td>

                <!-- Col 7: Thao tác -->
                <td class="align-top text-center td-actions">
                  <div class="flex items-center justify-center gap-1.5">
                    <router-link
                      :to="`/lms/contracts/${item.contract_id}/detail`"
                      class="action-btn action-view"
                      title="Xem chi tiết hợp đồng"
                    >
                      <i class="fa-regular fa-eye"></i>
                    </router-link>
                    <a
                      :href="`/pages/print/contract/${item.contract_id}`"
                      target="_blank"
                      class="action-btn action-print"
                      title="In hợp đồng"
                    >
                      <i class="fa-solid fa-print"></i>
                    </a>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import axios from '../../../../http/axios.js'
  import moment from 'moment'
  import u from '../../../../until/helper.js'
  import select from 'vue-select'

  export default {
    name: 'StudentContracts',
    components: {
      "vue-select": select,
      moment
    },
    props: {
      student_info: {
        type: Object,
        default: () => ({})
      },
    },
    data() {
      return {
        contracts: [],
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
      }
    },
    computed: {
      summaryStats() {
        if (!this.contracts || !this.contracts.length) {
          return {
            totalContracts: 0,
            totalMustCharge: 0,
            totalCharged: 0,
            totalDebt: 0,
            totalSessions: 0
          }
        }
        return this.contracts.reduce((acc, cur) => {
          acc.totalContracts += 1;
          acc.totalMustCharge += Number(cur.must_charge || 0);
          acc.totalCharged += Number(cur.total_charged || 0);
          acc.totalDebt += Number(cur.debt_amount || 0);
          acc.totalSessions += Number(cur.debt_amount == 0 ? (cur.summary_sessions || cur.total_sessions || 0) : (cur.total_sessions || 0));
          return acc;
        }, {
          totalContracts: 0,
          totalMustCharge: 0,
          totalCharged: 0,
          totalDebt: 0,
          totalSessions: 0
        });
      }
    },
    filters: {
      formatMoney(num) {
        if (!num) return '0 đ';
        return Number(num).toLocaleString('vi-VN') + ' đ';
      }
    },
    created() {
      this.getContracts();
    },
    methods: {
      copyText(text, label = 'mã') {
        if (!text) return;
        if (navigator && navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(text).then(() => {
            this.$vs.notify({
              title: 'Đã sao chép',
              text: `Đã sao chép ${label}: ${text}`,
              color: 'success',
              iconPack: 'feather',
              icon: 'icon-check',
              time: 2500
            });
          }).catch(() => {
            this.$vs.notify({
              title: 'Thông báo',
              text: text,
              color: 'primary',
              time: 2500
            });
          });
        }
      },
      getStatusBadgeInfo(item) {
        const status = Number(item.status);
        const label = item.label_status ? item.label_status.trim() : '';

        // Đang học chính thức (status == 6 hoặc label chứa 'chính thức')
        if (status === 6 || label.includes('chính thức')) {
          return {
            class: 'badge-status-official',
            icon: 'fa-solid fa-circle-check',
            label: label || 'Đang học chính thức'
          };
        }
        // Đang học thử
        if (label.includes('học thử')) {
          return {
            class: 'badge-status-trial',
            icon: 'fa-solid fa-graduation-cap',
            label: label || 'Đang học thử'
          };
        }
        // Đặt cọc
        if (status === 2 || label.includes('cọc')) {
          return {
            class: 'badge-status-deposit',
            icon: 'fa-solid fa-coins',
            label: label || 'Đặt cọc'
          };
        }
        // Chờ xếp lớp
        if ([3, 4, 5].includes(status) || label.includes('Chờ')) {
          return {
            class: 'badge-status-waiting',
            icon: 'fa-solid fa-hourglass-half',
            label: label || 'Chờ xếp lớp'
          };
        }
        // Hết phí
        if (status === 7 || label.includes('Hết')) {
          return {
            class: 'badge-status-finished',
            icon: 'fa-solid fa-flag-checkered',
            label: label || 'Hết phí'
          };
        }
        // Chưa đóng phí
        if (status === 1 || label.includes('Chưa đóng')) {
          return {
            class: 'badge-status-unpaid',
            icon: 'fa-solid fa-circle-exclamation',
            label: label || 'Chưa đóng phí'
          };
        }
        return {
          class: 'badge-status-default',
          icon: 'fa-solid fa-circle-info',
          label: label || 'Đang hiệu lực'
        };
      },
      getContracts(){
        const data = {
          student_id: this.student_info.id,
          pagination: this.pagination,
        }
        this.$vs.loading()
        axios.p('/api/lms/students/contracts', data)
          .then((response) => {
            this.$vs.loading.close()
            const resData = response.data
            this.contracts = Array.isArray(resData) ? resData : (resData && (resData.contracts || resData.list) ? (resData.contracts || resData.list) : [])
          })
          .catch((error) => {
            console.log(error);
            this.$vs.loading.close();
          })
      },
      confirmExitDepost(contract_id) {
        this.$vs.dialog({
          type: 'confirm',
          color: 'warning',
          title: 'Quy đổi tiền cọc',
          text: `Bạn có chắc chắn muốn quy đổi tiền cọc hợp đồng này không?`,
          accept: () => {
            this.exitDepost(contract_id)
          }
        })
      },
      exitDepost(contract_id){
        const data = {
          contract_id: contract_id
        }
        this.$vs.loading()
        axios.p('/api/lms/contracts/exit-depost', data)
          .then((response) => {
            this.$vs.loading.close()
            this.getContracts();
          })
          .catch((error) => {
            console.log(error);
            this.$vs.loading.close();
          })
      }
    }
  }
</script>

<style scoped>
/* 1. Empty state */
.empty-state-box {
  padding: 48px 24px;
  text-align: center;
  background: #f8fafc;
  border-radius: 12px;
  border: 1px dashed #cbd5e1;
}

.empty-state-icon {
  font-size: 42px;
  color: #94a3b8;
  margin-bottom: 12px;
}

.empty-state-title {
  color: #1e293b;
  font-weight: 700;
  font-size: 16px;
  margin-bottom: 6px;
}

.empty-state-desc {
  color: #64748b;
  font-size: 14px;
}

/* 2. Top Summary KPI Cards */
.kpi-summary-card {
  display: flex;
  align-items: center;
  background: #ffffff;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  border-left-width: 4px;
  padding: 12px 14px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
  min-height: 74px;
  transition: all 0.2s ease;
}

.kpi-summary-card:hover {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.08);
  transform: translateY(-1px);
}

.border-l-blue { border-left-color: #2563eb; }
.border-l-indigo { border-left-color: #6366f1; }
.border-l-emerald { border-left-color: #10b981; }
.border-l-amber { border-left-color: #f59e0b; }

.kpi-icon-box {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  margin-right: 12px;
  flex-shrink: 0;
}

.kpi-info {
  flex: 1;
  min-width: 0;
}

.kpi-label {
  font-size: 12px;
  font-weight: 600;
  color: #64748b;
  margin-bottom: 2px;
}

.kpi-value {
  font-size: 16px;
  font-weight: 800;
  line-height: 1.2;
}

.kpi-unit {
  font-size: 12px;
  font-weight: normal;
  color: #64748b;
}

.kpi-sub-text {
  font-size: 11px;
  margin-top: 2px;
  color: #64748b;
}

/* 3. Modern Contracts Table */
.contracts-table-card {
  background: #ffffff;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

.table-responsive {
  width: 100%;
  overflow-x: auto;
}

.modern-contracts-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  font-size: 13.5px;
}

.modern-contracts-table thead th {
  background: #f8fafc;
  color: #475569;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  padding: 12px 14px;
  border-bottom: 1px solid #e2e8f0;
  white-space: nowrap;
}

.modern-contracts-table tbody td {
  padding: 14px 14px;
  border-bottom: 1px solid #f1f5f9;
  color: #334155;
  vertical-align: top;
}

.contract-row:hover td {
  background-color: #f8fafc;
}

.contract-row:last-child td {
  border-bottom: none;
}

.th-stt, .td-stt {
  width: 48px;
  text-align: center;
}

/* Col 1 items */
.contract-code-tag {
  display: inline-flex;
  align-items: center;
  padding: 4px 9px;
  border-radius: 6px;
  font-size: 13px;
  font-weight: 700;
  background-color: #eff6ff;
  color: #1d4ed8;
  border: 1px solid #bfdbfe;
  text-decoration: none;
  transition: all 0.15s ease;
}

.contract-code-tag:hover {
  background-color: #dbeafe;
  border-color: #93c5fd;
}

.copy-btn-mini {
  background: transparent;
  border: none;
  cursor: pointer;
  color: #94a3b8;
  padding: 3px 6px;
  margin-left: 4px;
  border-radius: 4px;
  transition: all 0.15s;
}

.copy-btn-mini:hover {
  color: #1d4ed8;
  background: #eff6ff;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  color: #64748b;
  margin-top: 4px;
}

/* Col 2 items */
.product-badge {
  display: inline-flex;
  align-items: center;
  padding: 2px 8px;
  border-radius: 4px;
  font-size: 11.5px;
  font-weight: 700;
  background: #f0fdf4;
  color: #15803d;
  border: 1px solid #bbf7d0;
}

.contract-name-title {
  font-weight: 700;
  font-size: 14px;
  color: #0f172a;
  line-height: 1.35;
}

/* Col 3 items */
.branch-title {
  font-weight: 700;
  color: #1e293b;
  font-size: 13px;
  display: flex;
  align-items: center;
}

.class-pill {
  display: inline-flex;
  align-items: center;
  background: #f5f3ff;
  color: #5b21b6;
  border: 1px solid #ddd6fe;
  padding: 3px 8px;
  border-radius: 5px;
  font-size: 12px;
  font-weight: 600;
}

.staff-assignment {
  display: flex;
  flex-direction: column;
  gap: 3px;
}

.staff-chip {
  font-size: 12px;
  color: #475569;
}

.chip-label {
  font-weight: 700;
  color: #94a3b8;
  font-size: 11px;
}

/* Col 4 items */
.session-highlight-box {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  padding: 6px 10px;
}

.session-main-count {
  display: flex;
  align-items: baseline;
  gap: 4px;
}

.count-number {
  font-size: 16px;
  font-weight: 800;
  color: #2563eb;
}

.count-unit {
  font-size: 12px;
  color: #64748b;
  font-weight: 500;
}

.bonus-tag {
  background: #ecfdf5;
  color: #047857;
  border: 1px solid #a7f3d0;
  border-radius: 4px;
  padding: 1px 5px;
  font-size: 11px;
  font-weight: 700;
  margin-left: 4px;
}

.session-sub-info {
  font-size: 11.5px;
  color: #64748b;
  margin-top: 2px;
}

.roadmap-timeline {
  display: flex;
  flex-direction: column;
  gap: 3px;
  font-size: 12px;
}

.timeline-row {
  display: flex;
  align-items: center;
  gap: 6px;
  color: #475569;
}

/* Col 5 items */
.finance-compact-card {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 8px 10px;
}

.finance-entry {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 12.5px;
  padding: 2px 0;
}

.entry-label {
  color: #64748b;
}

.entry-val {
  color: #1e293b;
}

.entry-highlight-paid {
  background: #ecfdf5;
  padding: 3px 6px;
  border-radius: 4px;
  margin-top: 3px;
  border: 1px solid #d1fae5;
}

.entry-highlight-debt {
  background: #fef2f2;
  padding: 3px 6px;
  border-radius: 4px;
  margin-top: 3px;
  border: 1px solid #fee2e2;
}

.paid-full-badge {
  font-size: 11.5px;
  font-weight: 700;
  color: #047857;
  text-align: right;
  padding-right: 2px;
}

/* Col 6 Status Pill */
.contract-status-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 5px 12px;
  border-radius: 9999px;
  font-size: 12px;
  font-weight: 700;
  white-space: nowrap;
}

.status-dot-icon {
  font-size: 9px;
}

.badge-status-official {
  background: #ecfdf5;
  color: #047857;
  border: 1px solid #a7f3d0;
}

.badge-status-trial {
  background: #f5f3ff;
  color: #6d28d9;
  border: 1px solid #ddd6fe;
}

.badge-status-deposit {
  background: #fffbeb;
  color: #b45309;
  border: 1px solid #fde68a;
}

.badge-status-waiting {
  background: #eff6ff;
  color: #1d4ed8;
  border: 1px solid #bfdbfe;
}

.badge-status-finished {
  background: #f1f5f9;
  color: #475569;
  border: 1px solid #cbd5e1;
}

.badge-status-unpaid {
  background: #fef2f2;
  color: #b91c1c;
  border: 1px solid #fecaca;
}

.badge-status-default {
  background: #f8fafc;
  color: #475569;
  border: 1px solid #e2e8f0;
}

/* Col 7 Actions */
.action-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: 6px;
  font-size: 13px;
  transition: all 0.15s ease;
  text-decoration: none;
}

.action-view {
  background: #eff6ff;
  color: #1d4ed8;
  border: 1px solid #bfdbfe;
}

.action-view:hover {
  background: #1d4ed8;
  color: #ffffff;
}

.action-print {
  background: #f0fdf4;
  color: #15803d;
  border: 1px solid #bbf7d0;
}

.action-print:hover {
  background: #15803d;
  color: #ffffff;
}
</style>
