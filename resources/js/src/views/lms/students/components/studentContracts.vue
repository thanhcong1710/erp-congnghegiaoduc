<template>
  <div class="student-detail-contracts mt-3">
    <!-- Empty state -->
    <div v-if="!contracts || contracts.length === 0" class="empty-state-box">
      <i class="fa-solid fa-file-invoice-dollar empty-state-icon"></i>
      <h5 class="empty-state-title">Chưa có gói phí / hợp đồng nào</h5>
      <p class="empty-state-desc">Học sinh chưa được tạo gói phí nhập học hoặc nạp phí trên hệ thống.</p>
    </div>

    <!-- Contracts Table with Solid Borders -->
    <div v-else class="bordered-table-container">
      <table class="grid-border-table">
        <thead>
          <tr>
            <th class="text-center" style="width: 50px">STT</th>
            <th style="width: 170px">Thời gian & Người tạo</th>
            <th style="width: 220px">Trung tâm & Phụ trách</th>
            <th>Gói phí & Khóa học</th>
            <th style="width: 240px">Tài chính & Thu phí</th>
            <th style="width: 210px">Trạng thái & Lộ trình</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in contracts" :key="index" class="grid-row-hover">
            <!-- STT -->
            <td class="text-center font-bold text-gray-500 align-top">
              {{ index + 1 }}
            </td>

            <!-- Col 1: Thời gian -->
            <td class="align-top">
              <div class="font-bold text-gray-900 flex items-center mb-1">
                <i class="fa-regular fa-clock text-primary mr-1.5"></i>
                {{ item.created_at }}
              </div>
              <div class="text-gray-600 flex items-center text-sm">
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
              <div v-if="item.class_name" class="class-tag-pill mb-1.5">
                <i class="fa-solid fa-chalkboard mr-1 text-primary"></i> {{ item.class_name }}
              </div>
              <div class="staff-info-block">
                <div v-if="item.ec_name"><span class="label-text">EC:</span> <strong>{{ item.ec_name }}</strong></div>
                <div v-if="item.cm_name"><span class="label-text">CM:</span> <strong>{{ item.cm_name }}</strong></div>
              </div>
            </td>

            <!-- Col 3: Gói phí -->
            <td class="align-top">
              <div class="flex items-center gap-2 mb-1">
                <span class="contract-code-badge">
                  <i class="fa-solid fa-file-contract mr-1"></i> {{ item.code }}
                </span>
              </div>
              <div class="font-bold text-gray-900 text-base mb-0.5">{{ item.product_name }}</div>
              <div class="text-gray-600 mb-2 font-medium">{{ item.tuition_fee_name }}</div>
              
              <div class="session-stat-box">
                <div class="session-stat-row">
                  <span>Số buổi học:</span>
                  <strong class="text-primary">{{ item.debt_amount == 0 ? item.summary_sessions : item.total_sessions }} buổi</strong>
                  <span v-if="item.bonus_sessions" class="bonus-badge ml-1">(+{{ item.bonus_sessions }} HB)</span>
                </div>
                <div class="session-stat-row text-gray-500 mt-0.5">
                  <span>Bảo lưu:</span>
                  <span>{{ item.reserved_sessions || 0 }}/{{ item.reservable_sessions || 0 }} buổi</span>
                </div>
              </div>
            </td>

            <!-- Col 4: Tài chính -->
            <td class="align-top">
              <div class="financial-card">
                <div class="finance-row text-gray-600">
                  <span>Giá gốc:</span>
                  <span class="font-medium">{{ item.init_tuition_fee_amount | formatMoney }}</span>
                </div>
                <div class="finance-row text-gray-800">
                  <span>Phải đóng:</span>
                  <span class="font-bold">{{ item.must_charge | formatMoney }}</span>
                </div>
                <div class="finance-row text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                  <span class="font-semibold">Đã đóng:</span>
                  <span class="font-bold">{{ item.total_charged | formatMoney }}</span>
                </div>
                <div v-if="item.debt_amount > 0" class="finance-row text-rose-700 bg-rose-50 px-2 py-0.5 rounded border border-rose-200 mt-1">
                  <span class="font-semibold">Công nợ:</span>
                  <span class="font-bold">{{ item.debt_amount | formatMoney }}</span>
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
              <div class="roadmap-dates">
                <div v-if="item.enrolment_start_date" class="date-row">
                  <i class="fa-regular fa-calendar-check text-emerald-600 mr-1.5"></i>
                  <span>Bắt đầu: <strong>{{ item.enrolment_start_date }}</strong></span>
                </div>
                <div v-if="item.enrolment_last_date" class="date-row mt-1">
                  <i class="fa-regular fa-calendar-xmark text-rose-500 mr-1.5"></i>
                  <span>Kết thúc: <strong>{{ item.enrolment_last_date }}</strong></span>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
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

/* BORDERED GRID TABLE */
.bordered-table-container {
  width: 100%;
  overflow-x: auto;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  background: #ffffff;
}

.grid-border-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
}

.grid-border-table thead th {
  background: #f1f5f9;
  color: #1e293b;
  font-weight: 700;
  font-size: 13.5px;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  padding: 11px 14px;
  border: 1px solid #cbd5e1;
  white-space: nowrap;
}

.grid-border-table tbody td {
  padding: 12px 14px;
  border: 1px solid #cbd5e1;
  vertical-align: top;
  font-size: 14px;
  color: #334155;
}

.grid-row-hover:hover td {
  background-color: #f8fafc;
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

.staff-info-block {
  font-size: 13px;
  color: #475569;
}

.label-text {
  color: #94a3b8;
  font-size: 12px;
}

.session-stat-box {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  padding: 6px 10px;
  font-size: 13px;
}

.session-stat-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.bonus-badge {
  color: #059669;
  font-weight: 700;
  font-size: 12px;
}

.financial-card {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  padding: 8px 10px;
  font-size: 13px;
}

.finance-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 4px;
}

.finance-row:last-child {
  margin-bottom: 0;
}

.roadmap-dates {
  font-size: 13px;
}

.date-row {
  display: flex;
  align-items: center;
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

.badge-official { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.badge-deposited { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
.badge-waiting { background: #fef9c3; color: #a16207; border: 1px solid #fef08a; }
.badge-paused { background: #ffedd5; color: #c2410c; border: 1px solid #fed7aa; }
.badge-finished { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
.badge-withdrawn { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
.badge-cancelled { background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb; }
.badge-default { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
</style>
