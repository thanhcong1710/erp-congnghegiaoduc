<template>
  <div class="student-detail-operating mt-2">
    <!-- 1. Thông tin chuyển lớp -->
    <div class="operating-section mb-4">
      <div class="operating-header">
        <span class="operating-title">
          <i class="fa-solid fa-arrow-right-arrow-left text-primary mr-1.5"></i>
          1. Thông tin chuyển lớp
        </span>
        <span class="operating-count">
          {{ class_transfer ? class_transfer.length : 0 }} lượt
        </span>
      </div>
      <div class="table-responsive rounded border border-slate-200">
        <table class="w-full bg-white modern-op-table">
          <thead class="bg-slate-50 text-gray-700 font-bold border-b border-slate-200 uppercase modern-op-thead">
            <tr>
              <th class="py-2.5 px-3">Học sinh</th>
              <th class="py-2.5 px-3">Trung tâm</th>
              <th class="py-2.5 px-3">Lớp đi</th>
              <th class="py-2.5 px-3">Lớp đến</th>
              <th class="py-2.5 px-3 text-center">Ngày chuyển</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-if="!class_transfer || class_transfer.length === 0">
              <td colspan="5" class="text-center py-4 text-gray-400 italic">
                Chưa có dữ liệu chuyển lớp
              </td>
            </tr>
            <tr v-for="(item, index) in class_transfer" :key="'ct_' + index" class="hover:bg-slate-50">
              <td class="py-2 px-3 font-semibold text-gray-800">{{ item.name }} - <span class="text-primary">{{ item.lms_code }}</span></td>
              <td class="py-2 px-3">{{ item.from_branch_name }}</td>
              <td class="py-2 px-3 text-rose-600">{{ item.from_class_name }}</td>
              <td class="py-2 px-3 text-emerald-600 font-semibold">{{ item.to_class_name }}</td>
              <td class="py-2 px-3 text-center text-gray-500">{{ item.transfer_date | formatDateView }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- 2. Thông tin chuyển trung tâm -->
    <div class="operating-section mb-4">
      <div class="operating-header">
        <span class="operating-title">
          <i class="fa-solid fa-building-circle-arrow-right text-emerald-600 mr-1.5"></i>
          2. Thông tin chuyển trung tâm
        </span>
        <span class="operating-count">
          {{ branch_transfer ? branch_transfer.length : 0 }} lượt
        </span>
      </div>
      <div class="table-responsive rounded border border-slate-200">
        <table class="w-full bg-white modern-op-table">
          <thead class="bg-slate-50 text-gray-700 font-bold border-b border-slate-200 uppercase modern-op-thead">
            <tr>
              <th class="py-2.5 px-3">Học sinh</th>
              <th class="py-2.5 px-3">Trung tâm chuyển</th>
              <th class="py-2.5 px-3">Trung tâm nhận</th>
              <th class="py-2.5 px-3 text-center">Ngày chuyển</th>
              <th class="py-2.5 px-3 text-center">Trạng thái</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-if="!branch_transfer || branch_transfer.length === 0">
              <td colspan="5" class="text-center py-4 text-gray-400 italic">
                Chưa có dữ liệu chuyển trung tâm
              </td>
            </tr>
            <tr v-for="(item, index) in branch_transfer" :key="'bt_' + index" class="hover:bg-slate-50">
              <td class="py-2 px-3 font-semibold text-gray-800">{{ item.name }} - <span class="text-primary">{{ item.lms_code }}</span></td>
              <td class="py-2 px-3 text-rose-600">{{ item.from_branch_name }}</td>
              <td class="py-2 px-3 text-emerald-600 font-semibold">{{ item.to_branch_name }}</td>
              <td class="py-2 px-3 text-center text-gray-500">{{ item.transfer_date | formatDateView }}</td>
              <td class="py-2 px-3 text-center">
                <vs-chip :color="getStatusColor(item.status)">{{ item.status | getStatusName }}</vs-chip>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- 3. Thông tin chuyển phí -->
    <div class="operating-section mb-3">
      <div class="operating-header">
        <span class="operating-title">
          <i class="fa-solid fa-money-bill-transfer text-amber-600 mr-1.5"></i>
          3. Thông tin chuyển phí
        </span>
        <span class="operating-count">
          {{ tuition_transfer ? tuition_transfer.length : 0 }} lượt
        </span>
      </div>
      <div class="table-responsive rounded border border-slate-200">
        <table class="w-full bg-white modern-op-table">
          <thead class="bg-slate-50 text-gray-700 font-bold border-b border-slate-200 uppercase modern-op-thead">
            <tr>
              <th class="py-2.5 px-3">Học sinh chuyển</th>
              <th class="py-2.5 px-3">Học sinh nhận</th>
              <th class="py-2.5 px-3">Số tiền chuyển</th>
              <th class="py-2.5 px-3 text-center">Ngày chuyển</th>
              <th class="py-2.5 px-3 text-center">Trạng thái</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-if="!tuition_transfer || tuition_transfer.length === 0">
              <td colspan="5" class="text-center py-4 text-gray-400 italic">
                Chưa có dữ liệu chuyển phí
              </td>
            </tr>
            <tr v-for="(item, index) in tuition_transfer" :key="'tt_' + index" class="hover:bg-slate-50">
              <td class="py-2 px-3 text-rose-600 font-semibold">{{ item.from_student_name }} - {{ item.from_student_lms_code }}</td>
              <td class="py-2 px-3 text-emerald-600 font-semibold">{{ item.to_student_name }} - {{ item.to_student_lms_code }}</td>
              <td class="py-2 px-3 font-bold text-gray-900">{{ item.transferred_amount | formatMoney }}</td>
              <td class="py-2 px-3 text-center text-gray-500">{{ item.transfer_date | formatDateView }}</td>
              <td class="py-2 px-3 text-center">
                <vs-chip :color="getStatusColor(item.status)">{{ item.status | getStatusNameTuitionTrasfer }}</vs-chip>
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
        class_transfer:[],
        branch_transfer:[],
        tuition_transfer:[],
      }
    },
    filters: {
      getStatusName(status) {
        switch (Number(status)) {
          case 0: return 'Chờ duyệt';
          case 1: return 'Đã duyệt';
          case 2: return 'Từ chối';
          default: return 'Khác';
        }
      },
      getStatusNameTuitionTrasfer(status) {
        switch (Number(status)) {
          case 0: return 'Chờ duyệt';
          case 1: return 'Đã duyệt';
          case 2: return 'Từ chối';
          default: return 'Khác';
        }
      }
    },
    async created() {
      this.getTransfer();
    },
    methods: {
      getStatusColor(status) {
        switch (Number(status)) {
          case 0: return 'warning';
          case 1: return 'success';
          case 2: return 'danger';
          default: return 'primary';
        }
      },
      getTransfer(){
        const data = {
          student_id: this.student_info.id,
        }
        this.$vs.loading()
        axios.p('/api/lms/students/transfers', data)
          .then((response) => {
            this.$vs.loading.close()
            this.class_transfer = response.data.class_transfer || []
            this.branch_transfer = response.data.branch_transfer || []
            this.tuition_transfer = response.data.tuition_transfer || []
          })
          .catch((error) => {
            console.log(error);
            this.$vs.loading.close();
          })
      },
    }
  }
</script>

<style scoped>
.operating-section {
  background: #ffffff;
}

.operating-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 8px;
}

.operating-title {
  font-size: 14.5px;
  font-weight: 700;
  color: #334155;
}

.operating-count {
  font-size: 13px;
  background: #f1f5f9;
  color: #64748b;
  padding: 2px 10px;
  border-radius: 9999px;
  font-weight: 600;
}

.modern-op-table {
  font-size: 14px;
}

.modern-op-thead {
  font-size: 13.5px;
}

.table-responsive {
  width: 100%;
  overflow-x: auto;
}
</style>
