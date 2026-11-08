<template>
  <div class="student-detail-operating mt-3">
    <!-- 1. Thông tin chuyển lớp -->
    <div class="operating-section mb-5">
      <div class="section-card-header">
        <span class="section-card-title text-indigo-700">
          <i class="fa-solid fa-arrow-right-arrow-left mr-2"></i>
          1. Thông tin chuyển lớp
        </span>
        <span class="section-count-badge">
          {{ class_transfer ? class_transfer.length : 0 }} lượt
        </span>
      </div>
      <div class="bordered-table-container">
        <table class="grid-border-table">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px">STT</th>
              <th>Học sinh</th>
              <th>Trung tâm</th>
              <th>Lớp đi</th>
              <th>Lớp đến</th>
              <th class="text-center" style="width: 140px">Ngày chuyển</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="!class_transfer || class_transfer.length === 0">
              <td colspan="6" class="text-center py-5 text-gray-400 italic">
                Chưa có dữ liệu chuyển lớp
              </td>
            </tr>
            <tr v-for="(item, index) in class_transfer" :key="'ct_' + index" class="grid-row-hover">
              <td class="text-center font-bold text-gray-500">{{ index + 1 }}</td>
              <td class="font-bold text-gray-800">
                {{ item.name }}
                <span class="stk-code-badge ml-1">{{ item.lms_code }}</span>
              </td>
              <td class="text-gray-700">{{ item.from_branch_name }}</td>
              <td>
                <span class="class-pill class-from">
                  <i class="fa-solid fa-arrow-left text-xs mr-1"></i> {{ item.from_class_name }}
                </span>
              </td>
              <td>
                <span class="class-pill class-to">
                  <i class="fa-solid fa-arrow-right text-xs mr-1"></i> {{ item.to_class_name }}
                </span>
              </td>
              <td class="text-center text-gray-700 font-semibold">
                {{ item.transfer_date | formatDateView }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- 2. Thông tin chuyển trung tâm -->
    <div class="operating-section mb-5">
      <div class="section-card-header">
        <span class="section-card-title text-emerald-700">
          <i class="fa-solid fa-building-circle-arrow-right mr-2"></i>
          2. Thông tin chuyển trung tâm
        </span>
        <span class="section-count-badge">
          {{ branch_transfer ? branch_transfer.length : 0 }} lượt
        </span>
      </div>
      <div class="bordered-table-container">
        <table class="grid-border-table">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px">STT</th>
              <th>Học sinh</th>
              <th>Trung tâm chuyển</th>
              <th>Trung tâm nhận</th>
              <th class="text-center" style="width: 140px">Ngày chuyển</th>
              <th class="text-center" style="width: 130px">Trạng thái</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="!branch_transfer || branch_transfer.length === 0">
              <td colspan="6" class="text-center py-5 text-gray-400 italic">
                Chưa có dữ liệu chuyển trung tâm
              </td>
            </tr>
            <tr v-for="(item, index) in branch_transfer" :key="'bt_' + index" class="grid-row-hover">
              <td class="text-center font-bold text-gray-500">{{ index + 1 }}</td>
              <td class="font-bold text-gray-800">
                {{ item.name }}
                <span class="stk-code-badge ml-1">{{ item.lms_code }}</span>
              </td>
              <td class="text-rose-600 font-bold">
                <i class="fa-solid fa-building mr-1 text-gray-400"></i> {{ item.from_branch_name }}
              </td>
              <td class="text-emerald-600 font-bold">
                <i class="fa-solid fa-building mr-1 text-emerald-500"></i> {{ item.to_branch_name }}
              </td>
              <td class="text-center text-gray-700 font-semibold">
                {{ item.transfer_date | formatDateView }}
              </td>
              <td class="text-center">
                <vs-chip :color="getStatusColor(item.status)">{{ item.status | getStatusName }}</vs-chip>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- 3. Thông tin chuyển phí -->
    <div class="operating-section mb-3">
      <div class="section-card-header">
        <span class="section-card-title text-amber-700">
          <i class="fa-solid fa-money-bill-transfer mr-2"></i>
          3. Thông tin chuyển phí
        </span>
        <span class="section-count-badge">
          {{ tuition_transfer ? tuition_transfer.length : 0 }} lượt
        </span>
      </div>
      <div class="bordered-table-container">
        <table class="grid-border-table">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px">STT</th>
              <th>Học sinh chuyển</th>
              <th>Học sinh nhận</th>
              <th>Số tiền chuyển</th>
              <th class="text-center" style="width: 140px">Ngày chuyển</th>
              <th class="text-center" style="width: 130px">Trạng thái</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="!tuition_transfer || tuition_transfer.length === 0">
              <td colspan="6" class="text-center py-5 text-gray-400 italic">
                Chưa có dữ liệu chuyển phí
              </td>
            </tr>
            <tr v-for="(item, index) in tuition_transfer" :key="'tt_' + index" class="grid-row-hover">
              <td class="text-center font-bold text-gray-500">{{ index + 1 }}</td>
              <td class="text-rose-600 font-bold">
                {{ item.from_student_name }}
                <span class="stk-code-badge ml-1" v-if="item.from_student_lms_code">{{ item.from_student_lms_code }}</span>
              </td>
              <td class="text-emerald-600 font-bold">
                {{ item.to_student_name }}
                <span class="stk-code-badge ml-1" v-if="item.to_student_lms_code">{{ item.to_student_lms_code }}</span>
              </td>
              <td class="font-bold text-gray-900">
                {{ item.transferred_amount | formatMoney }}
              </td>
              <td class="text-center text-gray-700 font-semibold">
                {{ item.transfer_date | formatDateView }}
              </td>
              <td class="text-center">
                <vs-chip :color="getStatusColor(item.status)">{{ item.status | getStatusName }}</vs-chip>
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
        class_transfer: [],
        branch_transfer: [],
        tuition_transfer: [],
      }
    },
    filters: {
      formatDateView(date) {
        if (!date) return '---';
        return moment(date).format('DD/MM/YYYY');
      },
      formatMoney(num) {
        if (!num) return '0 đ';
        return Number(num).toLocaleString('vi-VN') + ' đ';
      },
      getStatusName(status) {
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
        axios.p('/api/lms/students/operating', data)
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
.section-card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 8px;
}

.section-card-title {
  font-size: 15px;
  font-weight: 700;
  display: flex;
  align-items: center;
}

.section-count-badge {
  font-size: 12px;
  font-weight: 700;
  color: #64748b;
  background: #f1f5f9;
  padding: 3px 10px;
  border-radius: 9999px;
  border: 1px solid #cbd5e1;
}

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
  vertical-align: middle;
  font-size: 14px;
  color: #334155;
}

.grid-row-hover:hover td {
  background-color: #f8fafc;
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
  border-radius: 4px;
}

.class-pill {
  display: inline-flex;
  align-items: center;
  padding: 3px 8px;
  border-radius: 4px;
  font-size: 13px;
  font-weight: 600;
}

.class-from {
  background: #fee2e2;
  color: #b91c1c;
  border: 1px solid #fecaca;
}

.class-to {
  background: #dcfce7;
  color: #15803d;
  border: 1px solid #bbf7d0;
}
</style>
