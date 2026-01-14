<template>
  <div class="student-detail-logs mt-5">
    <h5 class="mb-3">Đã đóng phí</h5>
    <div class="vs-component vs-con-table stripe vs-table-primary">
      <div class="con-tablex vs-table--content">
        <div class="vs-con-tbody vs-table--tbody ">
          <table class="vs-table vs-table--tbody-table">
            <thead class="vs-table--thead">
              <tr>
                <!---->
                <th colspan="1" rowspan="1">Loại thu phí</th>
                <th colspan="1" rowspan="1">Thông tin</th>
                <th colspan="1" rowspan="1">Phải đóng</th>
                <th colspan="1" rowspan="1">Công nợ</th>
                <th colspan="1" rowspan="1">Số tiền</th>
                <th colspan="1" rowspan="1">Thời gian</th>
              </tr>
            </thead>
            <tr  v-if="payments && payments.length > 0" class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in payments" :key="index">
              <td class="td vs-table--td">
                <p>{{item.type ==1 ? 'Phí nhập học' : 'Phí bảo lưu'}}</p>
              </td>
              <td class="td vs-table--td">
                <p>{{item.type ==1 ? item.tuition_fee_name : ''}}</p>
              </td>
              <td class="td vs-table--td">
                <p>{{item.must_charge | formatMoney}}</p>
              </td>
              <td class="td vs-table--td">
                <p>{{item.debt | formatMoney}}</p>
              </td>
              <td class="td vs-table--td">
                <p><strong>{{item.amount | formatMoney}}</strong></p>
              </td>
              <td class="td vs-table--td">
                <p>{{ item.charge_date }}</p>
              </td>
            </tr>
            <tr v-else>
              <td class="td vs-table--td text-center" colspan="6">
                <p>Không có dữ liệu</p>
              </td>
            </tr>
          </table>
        </div>
      </div>
    </div>
    <vs-divider/>
    <h5 class="mt-3 mb-3">Chờ đóng phí</h5>
    <div class="vs-component vs-con-table stripe vs-table-primary">
      <div class="con-tablex vs-table--content">
        <div class="vs-con-tbody vs-table--tbody ">
          <table class="vs-table vs-table--tbody-table">
            <thead class="vs-table--thead">
              <tr>
                <!---->
                <th colspan="1" rowspan="1">Loại thu phí</th>
                <th colspan="1" rowspan="1">Thông tin</th>
                <th colspan="1" rowspan="1">Phải đóng</th>
                <th colspan="1" rowspan="1">Đã đóng</th>
                <th colspan="1" rowspan="1">Công nợ</th>
                <th colspan="1" rowspan="1">Thời gian tạo</th>
              </tr>
            </thead>
            <tr  v-if="agreements && agreements.length > 0" class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in agreements" :key="index">
              <td class="td vs-table--td">
                <p>'Phí nhập học'</p>
              </td>
              <td class="td vs-table--td">
                <p>{{item.tuition_fee_name}}</p>
              </td>
              <td class="td vs-table--td">
                <p>{{item.must_charge | formatMoney}}</p>
              </td>
              <td class="td vs-table--td">
                <p>{{item.total_charged | formatMoney}}</p>
              </td>
              <td class="td vs-table--td">
                <p><strong>{{item.debt_amount | formatMoney}}</strong></p>
              </td>
              <td class="td vs-table--td">
                <p>{{ item.created_at }}</p>
              </td>
            </tr>
            <tr  v-if="reserves && reserves.length > 0" class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in reserves" :key="index">
             <td class="td vs-table--td">
                <p>'Phí bảo lưu'</p>
              </td>
              <td class="td vs-table--td">
                <p></p>
              </td>
              <td class="td vs-table--td">
                <p>{{item.must_charge | formatMoney}}</p>
              </td>
              <td class="td vs-table--td">
                <p>{{item.total_charged | formatMoney}}</p>
              </td>
              <td class="td vs-table--td">
                <p><strong>{{item.debt_amount | formatMoney}}</strong></p>
              </td>
              <td class="td vs-table--td">
                <p>{{ item.created_at }}</p>
              </td>
            </tr>
            <tr  v-if="agreements.length == 0 && reserves.length == 0">
              <td class="td vs-table--td text-center" colspan="6">
                <p>Không có dữ liệu</p>
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
        payments:[],
        agreements:[],
        reserves:[],
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
        axios.p('/api/lms/students/payments', data)
          .then((response) => {
            this.$vs.loading.close()
            this.payments = response.data.payments
            this.agreements = response.data.agreements
            this.reserves = response.data.reserves
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
.td.vs-table--td{
  vertical-align: top;
}
</style>