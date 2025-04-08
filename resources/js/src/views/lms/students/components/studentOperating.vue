<template>
  <div class="student-detail-operating mt-5">
    <h4 class="mb-3">Thông tin chuyển lớp</h4>
    <div class="vs-component vs-con-table stripe vs-table-primary">
      <div class="con-tablex vs-table--content">
        <div class="vs-con-tbody vs-table--tbody ">
          <table class="vs-table vs-table--tbody-table">
            <thead class="vs-table--thead">
              <tr>
                <!---->
                <th colspan="1" rowspan="1">Học sinh</th>
                <th colspan="1" rowspan="1">Trung tâm</th>
                <th colspan="1" rowspan="1">Lớp đi</th>
                <th colspan="1" rowspan="1">Lớp đến </th>
                <th colspan="1" rowspan="1" class="text-center">Ngày chuyển</th>
              </tr>
            </thead>
            <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in class_transfer" :key="index">
              <td class="td vs-table--td">{{item.name}} - {{item.lms_code}}</td>
              <td class="td vs-table--td">{{item.from_branch_name}}</td>
              <td class="td vs-table--td">{{item.from_class_name}}</td>
              <td class="td vs-table--td">{{item.to_class_name}}</td>
              <td class="td vs-table--td text-center">{{item.transfer_date | formatDateView}}</td>
            </tr>
          </table>
        </div>
      </div>
    </div>
    <vs-divider/>
    <h4 class="mt-3 mb-3">Thông tin chuyển trung tâm</h4>
    <div class="vs-component vs-con-table stripe vs-table-primary">
      <div class="con-tablex vs-table--content">
        <div class="vs-con-tbody vs-table--tbody ">
          <table class="vs-table vs-table--tbody-table">
            <thead class="vs-table--thead">
              <tr>
                <!---->
                <th colspan="1" rowspan="1">Học sinh</th>
                <th colspan="1" rowspan="1">Trung tâm chuyển</th>
                <th colspan="1" rowspan="1">Trung tâm nhận</th>
                <th colspan="1" rowspan="1" class="text-center">Ngày chuyển</th>
                <th colspan="1" rowspan="1">Trạng thái</th>
              </tr>
            </thead>
            <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in branch_transfer" :key="index">
              <td class="td vs-table--td">{{item.name}} - {{item.lms_code}}</td>
              <td class="td vs-table--td">{{item.from_branch_name}}</td>
              <td class="td vs-table--td">{{item.to_branch_name}}</td>
              <td class="td vs-table--td text-center">{{item.transfer_date | formatDateView}}</td>
              <td class="td vs-table--td">
                <vs-chip :color="getStatusColor(item.status)">{{item.status | getStatusName}}</vs-chip>
              </td>
            </tr>
          </table>
        </div>
      </div>
    </div>
    <vs-divider/>
    <h4 class="mt-3 mb-3">Thông tin chuyển phí</h4>
    <div class="vs-component vs-con-table stripe vs-table-primary">
      <div class="con-tablex vs-table--content">
        <div class="vs-con-tbody vs-table--tbody ">
          <table class="vs-table vs-table--tbody-table">
            <thead class="vs-table--thead">
              <tr>
                <!---->
                <th colspan="1" rowspan="1">Học sinh chuyển</th>
                <th colspan="1" rowspan="1">Học sinh nhận</th>
                <th colspan="1" rowspan="1">Số tiền chuyển</th>
                <th colspan="1" rowspan="1" class="text-center">Ngày chuyển</th>
                <th colspan="1" rowspan="1" >Trạng thái</th>
              </tr>
            </thead>
            <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in tuition_transfer" :key="index">
              <td class="td vs-table--td">{{item.from_student_name}} - {{item.from_student_lms_code}}</td>
              <td class="td vs-table--td">{{item.to_student_name}} - {{item.to_student_lms_code}}</td>
              <td class="td vs-table--td">{{item.transferred_amount | formatMoney}}</td>
              <td class="td vs-table--td text-center">{{item.transfer_date | formatDateView}}</td>
              <td class="td vs-table--td">
                <vs-chip :color="getStatusColor(item.status)">{{item.status | getStatusNameTuitionTrasfer}}</vs-chip>
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
        class_transfer:[],
        branch_transfer:[],
        tuition_transfer:[],
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
      this.getOperating();
    },
    methods: {
      getOperating(){
        const data = {
          student_id: this.student_info.id,
        }
        this.$vs.loading()
        axios.p('/api/lms/students/operating', data)
          .then((response) => {
            this.$vs.loading.close()
            this.class_transfer = response.data.class_transfer
            this.branch_transfer = response.data.branch_transfer
            this.tuition_transfer = response.data.tuition_transfer
          })
          .catch((error) => {
            console.log(error);
            this.$vs.loading.close();
          })
      },
      getStatusColor(value) {
        let resp = ''
        switch (Number(value)) {
            case 1:
                resp = 'primary';
                break;
            case 2:
                resp = 'danger';
                break;
            case 3:
                resp = 'success';
                break;
            case 4:
                resp = 'danger';
                break;
            case 5:
                resp = 'success';
                break;
            case 6:
                resp = '#24c1a0';
                break;
            default:
                resp = 'primary'
                break
        }
        return resp
      },
    },
    filters: {
      getStatusName(value) {
        let resp = ''
        switch (Number(value)) {
            case 1:
                resp = 'Chờ duyệt đi';
                break;
            case 2:
                resp = 'Từ chối duyệt đi';
                break;
            case 3:
                resp = 'Chờ duyệt đến';
                break;
            case 4:
                resp = 'Từ chối duyệt đến';
                break;
            case 5:
                resp = 'Đã được duyệt đến';
                break;
            case 6:
                resp = 'Đã xử lý';
                break;
            default:
                resp = 'Chờ phê duyệt'
                break
        }
        return resp
      },
      getStatusNameTuitionTrasfer(value) {
        let resp = ''
        switch (Number(value)) {
            case 1:
                resp = 'Chờ GĐTT duyệt';
                break;
            case 2:
                resp = 'GĐTT từ chối duyệt';
                break;
            case 3:
                resp = 'Chờ kế toán duyệt';
                break;
            case 4:
                resp = 'Kế toán từ chối duyệt';
                break;
            case 5:
                resp = 'Đã được duyệt';
                break;
            case 6:
                resp = 'Đã xử lý';
                break;
            default:
                resp = 'Chờ GĐTT duyệt'
                break
        }
        return resp
      },
    },
  }
</script>
<style>
.td.vs-table--td{
  vertical-align: top;
}
</style>