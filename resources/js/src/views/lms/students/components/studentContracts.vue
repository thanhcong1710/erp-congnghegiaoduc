<template>
  <div class="student-detail-logs mt-5">
    <div class="vs-component vs-con-table stripe vs-table-primary">
      <div class="con-tablex vs-table--content">
        <div class="vs-con-tbody vs-table--tbody ">
          <table class="vs-table vs-table--tbody-table">
            <thead class="vs-table--thead">
              <tr>
                <!---->
                <th colspan="1" rowspan="1">Thời gian thực hiện</th>
                <th colspan="1" rowspan="1">Trung tâm</th>
                <th colspan="1" rowspan="1">Hợp đồng</th>
                <th colspan="1" rowspan="1">Đóng phí</th>
                <th colspan="1" rowspan="1">Trạng thái</th>
              </tr>
            </thead>
            <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in contracts" :key="index">
              <td class="td vs-table--td">
                <p>{{ item.created_at }}</p>
                <p>{{ item.creator_name }}</p>
              </td>
              <td class="td vs-table--td">
                <p>{{item.branch_name}}</p>
                <p>EC: {{item.ec_name}}</p>
                <p>CM: {{item.cm_name}}</p>
                <p>Lớp: {{item.class_name}}</p>
              </td>
              <td class="td vs-table--td">
                <p>Mã: <strong>{{item.code}}</strong></p>
                <p>Khóa học: {{item.product_name}}</p>
                <p>Gói phí: {{item.tuition_fee_name}}</p>
                <p>Số buổi: {{item.total_sessions}} ({{item.bonus_sessions ? item.bonus_sessions : 0}} học bổng)</p>
              </td>
              <td class="td vs-table--td">
                <p>Giá gốc: {{item.init_tuition_fee_amount | formatMoney}}</p>
                <p>Phải đóng: {{item.must_charge | formatMoney}}</p>
                <p>Công nợ: {{item.debt_amount | formatMoney}}</p>
                <p v-if="item.debt_amount>0 && item.total_charged"><strong>Đặt cọc: {{ item.summary_sessions }} buổi</strong></p>
                <vs-button v-if="item.debt_amount>0 && item.total_charged" class="mr-3 mb-2" @click="exitDepost(item.id)">Quy đổi cọc</vs-button>
              </td>
              <td class="td vs-table--td">
                <strong>{{item.label_status}}</strong>
                <p v-if="item.enrolment_start_date">Ngày bắt đầu: {{item.enrolment_start_date}}</p>
                <p v-if="item.enrolment_last_date">Ngày kết thúc: {{item.enrolment_last_date}}</p>
              </td>
            </tr>
          </table>
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
          })
          .catch((error) => {
            console.log(error);
            this.$vs.loading.close();
          })
      },
      confirmExitDepost () {
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
      exitDepost(contract_id){
        const data = {
          contract_id: contract_id,
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
<style>
.td.vs-table--td{
  vertical-align: top;
}
</style>