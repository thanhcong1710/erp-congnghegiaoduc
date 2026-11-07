<template>
  <div class="student-detail-contracts mt-2">
    <!-- Empty state -->
    <div v-if="!contracts || contracts.length === 0" class="p-8 text-center bg-slate-50 rounded-lg border border-slate-200">
      <i class="fa-solid fa-file-invoice-dollar text-4xl text-gray-300 mb-2"></i>
      <h5 class="text-gray-600 font-semibold mb-1">Chưa có gói phí / hợp đồng nào</h5>
      <p class="text-xs text-gray-400">Học sinh chưa được tạo gói phí nhập học hoặc nạp phí trên hệ thống.</p>
    </div>

    <!-- Contracts Table -->
    <div v-else class="table-responsive rounded-lg border border-slate-200 overflow-hidden shadow-sm">
      <table class="w-full text-left text-xs bg-white">
        <thead class="bg-slate-50 text-gray-600 font-bold border-b border-slate-200 uppercase text-2xs tracking-wider">
          <tr>
            <th class="py-3 px-4">Thời gian & Người tạo</th>
            <th class="py-3 px-4">Trung tâm & Phụ trách</th>
            <th class="py-3 px-4">Gói phí & Khóa học</th>
            <th class="py-3 px-4">Tài chính & Thu phí</th>
            <th class="py-3 px-4">Trạng thái & Lộ trình</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="(item, index) in contracts" :key="index" class="hover:bg-slate-50 transition-colors">
            <!-- Col 1: Thời gian -->
            <td class="py-3 px-4 align-top">
              <div class="font-semibold text-gray-800 flex items-center">
                <i class="fa-regular fa-clock text-gray-400 mr-1.5"></i>
                {{ item.created_at }}
              </div>
              <div class="text-gray-500 mt-1 flex items-center">
                <i class="fa-regular fa-user text-gray-400 mr-1.5"></i>
                {{ item.creator_name || 'Hệ thống' }}
              </div>
            </td>

            <!-- Col 2: Trung tâm -->
            <td class="py-3 px-4 align-top">
              <div class="font-bold text-gray-900 mb-1 flex items-center">
                <i class="fa-solid fa-building text-primary mr-1.5"></i>
                {{ item.branch_name }}
              </div>
              <div class="text-gray-600 text-xs space-y-0.5">
                <div v-if="item.ec_name"><span class="text-gray-400">EC:</span> <strong>{{ item.ec_name }}</strong></div>
                <div v-if="item.cm_name"><span class="text-gray-400">CM:</span> <strong>{{ item.cm_name }}</strong></div>
                <div v-if="item.class_name" class="text-primary font-medium">
                  <i class="fa-solid fa-chalkboard mr-1"></i> {{ item.class_name }}
                </div>
              </div>
            </td>

            <!-- Col 3: Gói phí -->
            <td class="py-3 px-4 align-top">
              <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                  <i class="fa-solid fa-file-contract mr-1"></i> {{ item.code }}
                </span>
              </div>
              <div class="font-bold text-gray-900">{{ item.product_name }}</div>
              <div class="text-gray-600 text-xs">{{ item.tuition_fee_name }}</div>
              <div class="mt-1 text-xs text-gray-500">
                <span>Số buổi: <strong>{{ item.debt_amount == 0 ? item.summary_sessions : item.total_sessions }}</strong> buổi</span>
                <span v-if="item.bonus_sessions" class="text-emerald-600 ml-1 font-semibold">(+{{ item.bonus_sessions }} HB)</span>
              </div>
              <div class="text-2xs text-gray-400 mt-0.5">
                Bảo lưu: {{ item.reserved_sessions || 0 }}/{{ item.reservable_sessions || 0 }} buổi
              </div>
            </td>

            <!-- Col 4: Tài chính -->
            <td class="py-3 px-4 align-top">
              <div class="space-y-1">
                <div class="flex justify-between text-xs text-gray-500">
                  <span>Giá gốc:</span>
                  <span>{{ item.init_tuition_fee_amount | formatMoney }}</span>
                </div>
                <div class="flex justify-between text-xs font-semibold text-gray-800">
                  <span>Phải đóng:</span>
                  <span>{{ item.must_charge | formatMoney }}</span>
                </div>
                <div class="flex justify-between text-xs font-bold text-emerald-600">
                  <span>Đã đóng:</span>
                  <span>{{ item.total_charged | formatMoney }}</span>
                </div>
                <div v-if="item.debt_amount > 0" class="flex justify-between text-xs font-bold text-rose-600 bg-rose-50 px-1.5 py-0.5 rounded">
                  <span>Công nợ:</span>
                  <span>{{ item.debt_amount | formatMoney }}</span>
                </div>
              </div>
              <vs-button
                v-if="item.debt_amount > 0 && item.total_charged && item.status == 2"
                size="small"
                color="warning"
                type="relief"
                class="mt-2 w-full text-xs font-semibold"
                @click="confirmExitDepost(item.contract_id)"
              >
                <i class="fa-solid fa-arrow-right-arrow-left mr-1"></i> Quy đổi cọc
              </vs-button>
            </td>

            <!-- Col 5: Trạng thái & Lộ trình -->
            <td class="py-3 px-4 align-top">
              <div class="mb-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold" :class="getStatusBadgeClass(item.status)">
                  <i class="fa-solid fa-circle text-2xs mr-1.5"></i>
                  {{ item.label_status || 'Đang hiệu lực' }}
                </span>
              </div>
              <div class="text-xs text-gray-600 space-y-0.5">
                <div v-if="item.enrolment_start_date" class="flex items-center">
                  <i class="fa-regular fa-calendar-check text-emerald-500 mr-1.5"></i>
                  <span>Bắt đầu: {{ item.enrolment_start_date }}</span>
                </div>
                <div v-if="item.enrolment_last_date" class="flex items-center">
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
        contract_id:'',
      }
    },
    async created() {
      this.getContracts();
    },
    methods: {
      getStatusBadgeClass(status) {
        switch (Number(status)) {
          case 1:
            return 'bg-amber-100 text-amber-800 border border-amber-300';
          case 2:
            return 'bg-emerald-100 text-emerald-800 border border-emerald-300';
          case 3:
            return 'bg-blue-100 text-blue-800 border border-blue-300';
          case 4:
            return 'bg-purple-100 text-purple-800 border border-purple-300';
          case 5:
          case 6:
            return 'bg-rose-100 text-rose-800 border border-rose-300';
          default:
            return 'bg-slate-100 text-slate-800 border border-slate-300';
        }
      },
      getContracts(){
        const data = {
          student_id: this.student_info.id,
        }
        this.$vs.loading()
        axios.p('/api/lms/students/contracts', data)
          .then((response) => {
            this.$vs.loading.close()
            this.contracts = response.data
          })
          .catch((error) => {
            console.log(error);
            this.$vs.loading.close();
          })
      },
      confirmExitDepost (contract_id) {
        this.contract_id = contract_id
        this.$vs.dialog({
          type: 'confirm',
          color: 'danger',
          title: 'Thông báo',
          text: `Bạn chắc chắn quy đổi gói phí cọc trên? không thể hủy sau khi đã quy đổi`,
          accept: this.exitDepost,
          acceptText: 'Quy đổi cọc',
          cancelText: 'Hủy'
        })
      },
      exitDepost(){
        const data = {
          contract_id: this.contract_id,
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
.table-responsive {
  width: 100%;
  overflow-x: auto;
}
</style>
