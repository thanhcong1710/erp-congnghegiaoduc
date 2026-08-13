<template>
  <div class="student-detail-sessions mt-5">
    <div class="vs-component vs-con-table stripe vs-table-primary">
      <div class="con-tablex vs-table--content">
        <div class="vs-con-tbody vs-table--tbody ">
          <table class="vs-table vs-table--tbody-table">
            <thead class="vs-table--thead">
              <tr>
                <!---->
                <th colspan="1" rowspan="1">Trung tâm</th>
                <th colspan="1" rowspan="1">Hợp đồng</th>
                <th colspan="1" rowspan="1">Đóng phí</th>
                <th colspan="1" rowspan="1" class="text-center">Trạng thái</th>
                <th colspan="1" rowspan="1" class="text-center">Thao tác</th>
              </tr>
            </thead>
            <tr
                class="tr-values vs-table--tr"
                v-for="(item, index) in contracts"
                :key="index"
                :class="{
                  'contract-active': item.id === tmp_active
                }"
                @click="getSessions(item.id)"
              >
                <td class="td vs-table--td">
                  <p class="fw-bold">{{ item.branch_name }}</p>
                  <p>EC: {{ item.ec_name }}</p>
                  <p>AF: {{ item.cm_name }}</p>
                  <p>Lớp: <strong>{{ item.cls_name }}</strong></p>
                </td>

                <td class="td vs-table--td">
                  <p>Mã: <strong>{{ item.code }}</strong></p>
                  <p>Gói phí: {{ item.tuition_fee_name }}</p>
                  <p>Số buổi: {{ item.total_sessions }}</p>
                </td>

                <td class="td vs-table--td">
                  <p>Phải đóng: <strong>{{ item.must_charge | formatMoney }}</strong></p>
                  <p class="text-danger">Công nợ: {{ item.debt_amount | formatMoney }}</p>
                  <p class="text-warning mt-1" v-if="item.agreement_student_id && item.agreement_student_id != student_info.id">
                    <i>(Nhận chuyển phí từ học sinh {{ item.transfer_from_student_name || 'khác' }})</i>
                  </p>
                </td>

                <td class="td vs-table--td text-center">
                  <span class="status-badge">
                    {{ item.label_status }}
                  </span>
                  <div class="mt-2" v-if="item.enrolment_start_date || item.enrolment_last_date">
                    (<span v-if="item.enrolment_start_date">{{item.enrolment_start_date | formatDateView}}</span><span v-else>...</span> - <span v-if="item.enrolment_last_date">{{item.enrolment_last_date | formatDateView}}</span><span v-else>...</span>)
                  </div>
                </td>

                <td class="td vs-table--td text-center">
                  <vs-button size="small" icon style="font-size: 14px; padding: 8px;">
                    <i class="fa fa-eye" @click="getSessions(item.id)"></i>
                  </vs-button>
                </td>
              </tr>
          </table>
        </div>
      </div>
    </div>
    <vs-divider/>
    <div class="vx-row">
      <div class="vx-col md:w-1/4 w-full mb-4">
        <label>Tổng số buổi: <strong>{{contract_info.summary_sessions}}</strong></label>
      </div>
      <div class="vx-col md:w-1/4 w-full mb-4">
        <label>Số buổi đã học: <strong>{{contract_info.done_sessions}}</strong></label>
      </div>
    </div>
    <div>
      <div class="flex flex-wrap mt-5">
        <div :class="item.status ==2 || item.status ==3 ? 'box-info' : (item.attendance_status ==1 || item.attendance_status ==3 ? 'box-active' : 'box-danger')" v-for="(item, index) in done_sessions" :key="'done_'+index">
          <div class="box-item-student border border-gray-300 rounded min-w-125px py-3 px-5 me-6 mb-3 mr-1 ml-1">
              <div class="label-box-schedule text-center">{{item.cls_name}}</div>
              <div class="text-date-box-schedule text-center">Buổi {{index+1}} - {{item.class_date | formatDateViewDay}}</div>
              <div class="text-center">
                <span class="box-status">{{item.status ==2 || item.status ==3 ? (item.status ==3 ? 'Nghỉ lễ' : 'Bảo lưu') : (item.attendance_status ==1 ? 'Đã học' : (item.attendance_status ==3 ? 'Đã học bù' : 'Đã chạy phí'))}}</span>
              </div>
          </div>
        </div>
        <div v-for="(item, index) in next_sessions" :key="index">
          <div class="box-item-student border border-gray-300 rounded min-w-125px py-3 px-5 me-6 mb-3 mr-1 ml-1">
              <div class="label-box-schedule text-center">{{item.cls_name}}</div>
              <div class="text-date-box-schedule text-center">Buổi {{index+1 + done_sessions.length}} - {{item.class_date | formatDateViewDay}}</div>
              <div class="text-center"><span class="box-status">Sắp học</span></div>
          </div>
        </div>
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
        done_sessions:[],
        next_sessions:[],
        contract_info:{},
        tmp_active:0
      }
    },
    async created() {
      this.getContracts();
    },
    methods: {
      getContracts(){
        const data = {
          student_id: this.student_info.id,
        }
        this.$vs.loading()
        axios.p('/api/lms/students/contracts', data)
          .then((response) => {
            this.$vs.loading.close()
            this.contracts = response.data
            this.getSessions(0)
          })
          .catch((error) => {
            console.log(error);
            this.$vs.loading.close();
          })
      },
      getSessions(contract_id){
        const data = {
          student_id: this.student_info.id,
          contract_id: contract_id
        }
        this.$vs.loading()
        axios.p('/api/lms/students/sessions', data)
          .then((response) => {
            this.$vs.loading.close()
            this.contract_info = response.data.contract_info
            this.done_sessions = response.data.done_sessions
            this.next_sessions = response.data.next_sessions
            this.tmp_active = this.contract_info.id
          })
          .catch((error) => {
            console.log(error);
            this.$vs.loading.close();
          })
      },
    }
  }
</script>
<style>
.student-detail-sessions .box-item-student .label-box-schedule{
  font-size: 13px;
}
.student-detail-sessions .box-item-student .text-date-box-schedule{
  font-size: 11px;
}
.student-detail-sessions .box-item-student .box-status{
    font-size: 10px;
    background: rgba(var(--vs-primary),1);
    padding: 5px;
    border-radius: 6px;
    color: #fff;
}
.student-detail-sessions .box-item-student{
  border: 1px solid rgba(var(--vs-primary),1);
  background: transparent !important;
}

.student-detail-sessions .box-active .box-item-student{
  border: 1px solid rgba(var(--vs-success), 1);
}
.student-detail-sessions .box-active .box-item-student .box-status{
    background: rgba(var(--vs-success), 1);
}
.student-detail-sessions .box-danger .box-item-student{
  border: 1px solid rgba(var(--vs-danger), 1);
}
.student-detail-sessions .box-danger .box-item-student .box-status{
    background: rgba(var(--vs-danger), 1);
}
/* ===== TABLE ===== */
.student-detail-sessions table tr {
  cursor: pointer;
  transition: all 0.2s ease;
}

.student-detail-sessions table tr:hover {
  background: rgba(var(--vs-primary), 0.05);
}

/* Contract đang active */
.student-detail-sessions .contract-active {
  background: rgba(var(--vs-primary), 0.12) !important;
  border-left: 4px solid rgba(var(--vs-primary), 1);
}

/* Text helper */
.text-muted {
  font-size: 12px;
  color: #6b7280;
}

.fw-bold {
  font-weight: 600;
}

/* Status badge */
.status-badge {
  font-size: 11px;
  padding: 5px 10px;
  border-radius: 999px;
  background: rgba(var(--vs-success), 0.15);
  color: rgba(var(--vs-success), 1);
  font-weight: 600;
}

/* ===== SESSION BOX ===== */
.student-detail-sessions .box-item-student {
  transition: all 0.2s ease;
}

.student-detail-sessions .box-item-student:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
</style>