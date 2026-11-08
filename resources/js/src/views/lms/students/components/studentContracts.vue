<template>
  <div class="student-detail-contracts mt-3">
    <!-- Empty state -->
    <div v-if="!contracts || contracts.length === 0" class="empty-state-box">
      <i class="fa-solid fa-file-invoice-dollar empty-state-icon"></i>
      <h5 class="empty-state-title">Chưa có gói phí / hợp đồng nào</h5>
      <p class="empty-state-desc">Học sinh chưa được tạo gói phí nhập học hoặc nạp phí trên hệ thống.</p>
    </div>

    <!-- Contracts Table -->
    <div v-else class="table-container-card">
      <div class="table-responsive">
        <table class="detail-data-table">
          <thead>
            <tr>
              <th style="width: 170px">Thời gian & Người tạo</th>
              <th style="width: 220px">Trung tâm & Phụ trách</th>
              <th>Gói phí & Khóa học</th>
              <th style="width: 230px">Tài chính & Thu phí</th>
              <th style="width: 200px">Trạng thái & Lộ trình</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in contracts" :key="index" class="table-row-hover">
              <!-- Col 1: Thời gian -->
              <td class="align-top">
                <div class="font-semibold text-gray-800 flex items-center mb-1">
                  <i class="fa-regular fa-clock text-gray-400 mr-1.5"></i>
                  {{ item.created_at }}
                </div>
                <div class="text-gray-500 flex items-center">
                  <i class="fa-regular fa-user text-gray-400 mr-1.5"></i>
                  <span>{{ item.creator_name || 'Hệ thống' }}</span>
                </div>
              </td>

              <!-- Col 2: Trung tâm -->
              <td class="align-top">
                <div class="font-bold text-gray-900 mb-1 flex items-center">
                  <i class="fa-solid fa-building text-primary mr-1.5"></i>
                  {{ item.branch_name }}
                </div>
                <div class="text-gray-600 space-y-1">
                  <div v-if="item.ec_name"><span class="text-gray-400 text-xs">EC:</span> <strong>{{ item.ec_name }}</strong></div>
                  <div v-if="item.cm_name"><span class="text-gray-400 text-xs">CM:</span> <strong>{{ item.cm_name }}</strong></div>
                  <div v-if="item.class_name" class="class-tag-pill mt-1">
                    <i class="fa-solid fa-chalkboard mr-1"></i> {{ item.class_name }}
                  </div>
                </div>
              </td>

              <!-- Col 3: Gói phí -->
              <td class="align-top">
                <div class="flex items-center gap-2 mb-1.5">
                  <span class="contract-code-badge">
                    <i class="fa-solid fa-file-contract mr-1"></i> {{ item.code }}
                  </span>
                </div>
                <div class="font-bold text-gray-900 text-base mb-0.5">{{ item.product_name }}</div>
                <div class="text-gray-600 mb-1">{{ item.tuition_fee_name }}</div>
                <div class="text-gray-500 session-info-row">
                  <span>Số buổi: <strong class="text-gray-800">{{ item.debt_amount == 0 ? item.summary_sessions : item.total_sessions }}</strong> buổi</span>
                  <span v-if="item.bonus_sessions" class="text-emerald-600 ml-1 font-semibold">(+{{ item.bonus_sessions }} HB)</span>
                </div>
                <div class="text-gray-500 session-info-row mt-0.5">
                  Bảo lưu: {{ item.reserved_sessions || 0 }}/{{ item.reservable_sessions || 0 }} buổi
                </div>
              </td>

              <!-- Col 4: Tài chính -->
              <td class="align-top">
                <div class="financial-summary-card">
                  <div class="flex justify-between text-gray-500 mb-1">
                    <span>Giá gốc:</span>
                    <span>{{ item.init_tuition_fee_amount | formatMoney }}</span>
                  </div>
                  <div class="flex justify-between font-semibold text-gray-800 mb-1">
                    <span>Phải đóng:</span>
                    <span>{{ item.must_charge | formatMoney }}</span>
                  </div>
                  <div class="flex justify-between font-bold text-emerald-600 mb-1">
                    <span>Đã đóng:</span>
                    <span>{{ item.total_charged | formatMoney }}</span>
                  </div>
                  <div v-if="item.debt_amount > 0" class="flex justify-between font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded border border-rose-100">
                    <span>Công nợ:</span>
                    <span>{{ item.debt_amount | formatMoney }}</span>
                  </div>
                </div>
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

              <!-- Col 5: Trạng thái & Lộ trình -->
              <td class="align-top">
                <div class="mb-2">
                  <span class="status-pill-contract" :class="getStatusBadgeClass(item.status)">
                    <i class="fa-solid fa-circle mr-1.5" style="font-size: 7px;"></i>
                    {{ item.label_status || 'Đang hiệu lực' }}
                  </span>
                </div>
                <div class="text-gray-600 space-y-1">
                  <div v-if="item.enrolment_start_date" class="flex items-center text-sm">
                    <i class="fa-regular fa-calendar-check text-emerald-500 mr-1.5"></i>
                    <span>Bắt đầu: {{ item.enrolment_start_date }}</span>
                  </div>
                  <div v-if="item.enrolment_last_date" class="flex items-center text-sm">
                    <i class="fa-regular fa-calendar-xmark text-rose-400 mr-1.5"></i>
                    <span>Kết thúc: {{ item.enrolment_last_date }}</span>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
  import axios from '../../../../http/axios.js'
  import moment from 'moment';
  import u from '../../../../until/helper.js'
  import select from 'vue-select'

  export default {
    components: {
      "vue-select": select,
      moment
    },
    props: {
      student_info: {
        type: Object,
        default: () => {}
      },
    },
    data() {
      return {
        contracts:[],
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
    filters: {
      formatMoney(num) {
        if (!num) return '0 đ';
        return Number(num).toLocaleString('vi-VN') + ' đ';
      }
    },
    async created() {
      this.getContracts();
    },
    methods: {
      getStatusBadgeClass(status) {
        switch (Number(status)) {
          case 1: return 'badge-deposited';
          case 2: return 'badge-official';
          case 3: return 'badge-waiting';
          case 4: return 'badge-paused';
          case 5: return 'badge-finished';
          case 6: return 'badge-withdrawn';
          case 7: return 'badge-cancelled';
          default: return 'badge-default';
        }
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
            this.contracts = response.data.contracts
          })
          .catch((error) => {
            console.log(error);
            this.$vs.loading.close();
          })
      },
      confirmExitDepost(contract_id) {
        this.$vs.dialog({
          type: 'confirm',
          color: 'danger',
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
.empty-state-box {
  padding: 40px 20px;
  text-align: center;
  background: #f8fafc;
  border-radius: 8px;
  border: 1px dashed #cbd5e1;
}

.empty-state-icon {
  font-size: 36px;
  color: #94a3b8;
  margin-bottom: 8px;
}

.empty-state-title {
  color: #334155;
  font-weight: 700;
  margin-bottom: 4px;
}

.empty-state-desc {
  color: #64748b;
  font-size: 14px;
}

.table-container-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

.table-responsive {
  width: 100%;
  overflow-x: auto;
}

.detail-data-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  font-size: 14px;
}

.detail-data-table thead th {
  background: #f8fafc;
  color: #475569;
  font-weight: 700;
  font-size: 13.5px;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  padding: 12px 16px;
  border-bottom: 2px solid #e2e8f0;
  white-space: nowrap;
}

.detail-data-table tbody td {
  padding: 14px 16px;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: top;
  font-size: 14px;
  color: #334155;
}

.table-row-hover:hover {
  background-color: #f8fafc;
}

.detail-data-table tbody tr:last-child td {
  border-bottom: none;
}

.contract-code-badge {
  display: inline-flex;
  align-items: center;
  padding: 3px 8px;
  border-radius: 5px;
  font-size: 13px;
  font-weight: 700;
  background-color: #eff6ff;
  color: #1d4ed8;
  border: 1px solid #bfdbfe;
}

.class-tag-pill {
  display: inline-flex;
  align-items: center;
  background: #f5f3ff;
  color: #6d28d9;
  border: 1px solid #ddd6fe;
  padding: 2px 8px;
  border-radius: 4px;
  font-size: 13px;
  font-weight: 600;
}

.financial-summary-card {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  padding: 8px 12px;
  font-size: 13.5px;
}

.session-info-row {
  font-size: 13.5px;
}

.status-pill-contract {
  display: inline-flex;
  align-items: center;
  padding: 3px 10px;
  border-radius: 9999px;
  font-size: 12.5px;
  font-weight: 700;
  white-space: nowrap;
}

.badge-official { background: #dcfce7; color: #15803d; }
.badge-deposited { background: #e0f2fe; color: #0369a1; }
.badge-waiting { background: #fef9c3; color: #a16207; }
.badge-paused { background: #ffedd5; color: #c2410c; }
.badge-finished { background: #f1f5f9; color: #475569; }
.badge-withdrawn { background: #fee2e2; color: #b91c1c; }
.badge-cancelled { background: #f3f4f6; color: #6b7280; }
.badge-default { background: #f1f5f9; color: #475569; }
</style>
