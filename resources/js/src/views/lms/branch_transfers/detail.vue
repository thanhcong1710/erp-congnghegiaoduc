<template>

  <div id="page-users-list">
    <vx-card no-shadow class="mt-5">
      <div class="vx-row">
        <div class="vx-col md:w-1/2 w-full item-first">
          <h5 class="w-full mb-3"><i class="fa-solid fa-user-graduate mr-1"></i> Thông tin học sinh</h5>
          <div class="vx-row">
            <div class="vx-col w-full mb-4">
              <label>Trung tâm </label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="branch_transfer_info.from_branch_name"
                disabled="true"
              />
            </div>
            <div class="vx-col w-full mb-4">
              <div class="vx-col w-full mb-4">
                <label>Hồ sơ học sinh</label>
                <div class="vs-component vs-con-table stripe vs-table-primary">
                  <div class="vs-con-tbody vs-table--tbody ">
                    <table class="vs-table vs-table--tbody-table">
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td" width="124px"><span>Họ tên:</span></td> 
                        <td class="td vs-table--td"><span>{{branch_transfer_info.meta_data.student_info.name}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Mã LMS:</span></td> 
                        <td class="td vs-table--td"><strong>{{branch_transfer_info.meta_data.student_info.lms_code}}</strong></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Phụ huynh:</span></td> 
                        <td class="td vs-table--td"><span>{{branch_transfer_info.meta_data.student_info.gud_name1}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Điện thoại:</span></td> 
                        <td class="td vs-table--td"><span>{{branch_transfer_info.meta_data.student_info.gud_mobile1}}</span></td> 
                      </tr>
                       <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Email:</span></td> 
                        <td class="td vs-table--td"><span>{{branch_transfer_info.meta_data.student_info.gud_email1}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Điạ chỉ:</span></td> 
                        <td class="td vs-table--td"><span>{{branch_transfer_info.meta_data.student_info.address}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>EC:</span></td> 
                        <td class="td vs-table--td"><span>{{branch_transfer_info.meta_data.student_info.ec_name}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>CM:</span></td> 
                        <td class="td vs-table--td"><span>{{branch_transfer_info.meta_data.student_info.cm_name}}</span></td> 
                      </tr>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <div class="vx-col w-full mb-4" v-if="branch_transfer_info.meta_data.from_contracts.length > 0">
              <label>Danh sách gói phí</label>
              <div class="vs-component vs-con-table stripe vs-table-primary mb-5" v-for="(item, index) in branch_transfer_info.meta_data.from_contracts" :key="index">
                <div class="vs-con-tbody vs-table--tbody ">
                  <table class="vs-table vs-table--tbody-table" >
                    <thead class="vs-table--thead">
                      <tr>
                        <!---->
                        <th colspan="2" rowspan="1" class="bg-primary" style="color:#fff">
                          <div class="vs-table-text">{{index+1}}. Hợp đồng số {{item.code}} (gói phí {{item.tuition_fee_name}})</div>
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td" width="160px"><span>Số phí đã đóng:</span></td> 
                        <td class="td vs-table--td"><span>{{item.total_charged | formatMoney}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Số buổi chính thức:</span></td> 
                        <td class="td vs-table--td"><span>{{item.real_sessions}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Số buổi học bổng:</span></td> 
                        <td class="td vs-table--td"><span>{{item.bonus_sessions}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Số buổi đã học:</span></td> 
                        <td class="td vs-table--td"><strong>{{item.done_sessions}}</strong></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Số buổi còn:</span></td> 
                        <td class="td vs-table--td"><strong>{{item.left_sessions}}</strong></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Khóa học:</span></td> 
                        <td class="td vs-table--td"><span>{{item.product_name}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null" v-if="item.class_id">
                        <td class="td vs-table--td"><span>Lớp học:</span></td> 
                        <td class="td vs-table--td"><span>{{item.class_name}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null" v-if="item.class_id">
                        <td class="td vs-table--td"><span>Thời gian:</span></td> 
                        <td class="td vs-table--td"><span>{{item.enrolment_start_date | formatDateView}} - {{item.enrolment_last_date | formatDateView}}</span></td> 
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="vx-col md:w-1/2 w-full item-last">
          <h5 class="w-full mb-3"><i class="fa-solid fa-file-contract mr-1"></i> Thông tin chuyển trung tâm</h5>
          <div class="vx-row">
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Trung tâm nhận</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                 v-model="branch_transfer_info.to_branch_name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Ngày bắt đầu chuyển</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="branch_transfer_info.transfer_date"
                disabled="true"
              />
            </div>
            <div class="vx-col w-full mb-4" v-if="branch_transfer_info.meta_data.to_contracts.length > 0">
              <label>Danh sách gói phí tại thời điểm chuyển</label>
              <div class="vs-component vs-con-table stripe vs-table-primary mb-5" v-for="(item, index) in branch_transfer_info.meta_data.to_contracts" :key="index">
                <div class="vs-con-tbody vs-table--tbody ">
                  <table class="vs-table vs-table--tbody-table" >
                    <thead class="vs-table--thead">
                      <tr>
                        <!---->
                        <th colspan="2" rowspan="1" class="bg-success" style="color:#fff">
                          <div class="vs-table-text">{{index+1}}. Hợp đồng số {{item.code}} (gói phí {{item.tuition_fee_name}})</div>
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td" width="160px"><span>Số phí đã đóng:</span></td> 
                        <td class="td vs-table--td"><span>{{item.total_charged | formatMoney}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Số buổi chính thức:</span></td> 
                        <td class="td vs-table--td"><span>{{item.real_sessions}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Số buổi học bổng:</span></td> 
                        <td class="td vs-table--td"><span>{{item.bonus_sessions}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Số buổi đã học:</span></td> 
                        <td class="td vs-table--td"><strong>{{item.done_sessions}}</strong></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Số buổi còn:</span></td> 
                        <td class="td vs-table--td"><strong>{{item.left_sessions}}</strong></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Khóa học:</span></td> 
                        <td class="td vs-table--td"><span>{{item.product_name}}</span></td> 
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              
            </div>
            <div class="vx-col w-full mb-4">
              <label>Ghi chú</label>
              <textarea class="vs-inputx vs-input--input normal" disabled="true" v-model="branch_transfer_info.note"></textarea>
            </div>
            <div class="vx-col w-full mb-4">
              <p>Người tạo: {{branch_transfer_info.creator_name}}</p>
              <p class="mb-3">Thời gian tạo: {{branch_transfer_info.created_at}}</p>

              <p v-if="branch_transfer_info.from_approver_id">Người duyệt đi: {{branch_transfer_info.from_approver_name}}</p>
              <p v-if="branch_transfer_info.from_approver_id">Thời gian duyệt đi: {{branch_transfer_info.from_approved_at}}</p>
              <p class="mb-3" v-if="branch_transfer_info.from_approver_id">Ghi chú duyệt đi: {{branch_transfer_info.from_approve_comment}}</p>

              <p v-if="branch_transfer_info.to_approver_id">Người duyệt đến: {{branch_transfer_info.to_approver_name}}</p>
              <p v-if="branch_transfer_info.to_approver_id">Thời gian duyệt đến: {{branch_transfer_info.to_approved_at}}</p>
              <p class="mb-3" v-if="branch_transfer_info.to_approver_id">Ghi chú duyệt đến: {{branch_transfer_info.to_approve_comment}}</p>

              <label>Trạng thái: </label>
              <strong :class="getStatusTextColor(branch_transfer_info.status)">{{branch_transfer_info.status | getStatusName}}</strong>
            </div>
            <div class="vx-col w-full mb-4" v-if="showAction(branch_transfer_info.status)">
              <label>Ghi chú phê duyệt</label>
              <textarea class="vs-inputx vs-input--input normal" v-model="comment" :disabled="!showAction(branch_transfer_info.status)"></textarea>
            </div>
            <vs-alert :active.sync="alert.active" class="mb-5 mr-4 ml-4" :color="alert.color" closable icon-pack="feather" close-icon="icon-x">
              <div v-html="alert.body"></div>
            </vs-alert>
          </div>

          <div class="vx-col w-full text-right">
            <router-link class="btn btn-danger" :to="`/lms/branch_transfers`">
              <vs-button color="dark" type="border" class="mb-2 mr-3" >Thoát</vs-button>
            </router-link>
            <vs-button class="mb-2  mr-3" color="success" @click="save(true)" v-if="showAction(branch_transfer_info.status)">Phê duyệt</vs-button>
            <vs-button class="mb-2" color="danger" @click="save(false)" v-if="showAction(branch_transfer_info.status)">Từ chối phê duyệt</vs-button>
          </div>
        </div>
      </div>
    </vx-card>
    
    <vx-card no-shadow class="mt-5">
      <div class="vx-row">
        <div class="vx-col w-full item-first">
          <h5 class="w-full mb-3"><i class="fa-solid fa-file-lines mr-1"></i> Lịch sử chuyển trung tâm</h5>
          <div class="vs-component vs-con-table stripe vs-table-primary">
            <div class="con-tablex vs-table--content">
              <div class="vs-con-tbody vs-table--tbody ">
                <table class="vs-table vs-table--tbody-table">
                  <thead class="vs-table--thead">
                    <tr>
                      <th colspan="1" rowspan="1" class="text-center">Ngày chuyển</th>
                      <th colspan="1" rowspan="1">Trung tâm chuyển</th>
                      <th colspan="1" rowspan="1">Trung tâm nhận</th>
                      <th colspan="1" rowspan="1">Trạng thái</th>
                      <th colspan="1" rowspan="1" class="text-center">Thao tác</th>
                    </tr>
                  </thead>
                  <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in logs" :key="index">
                    <!---->
                    
                    <td class="td vs-table--td text-center">{{item.transfer_date}}</td>
                    <td class="td vs-table--td">{{item.from_branch_name}}</td>
                    <td class="td vs-table--td">{{item.to_branch_name}}</td>
                    <td class="td vs-table--td">
                      <vs-chip :color="getStatusColor(item.status)">{{item.status | getStatusName}}</vs-chip>
                    </td>
                    <td class="td vs-table--td text-center list-action">
                      <vs-button size="small" @click="loadDetail(item.id)"><i class="fa fa-eye"></i></vs-button>
                    </td>
                  </tr>
                </table>
                
              </div>
            </div>
          </div>  
        </div>
      </div>
    </vx-card>
  </div>
</template>

<script>

  import select from 'vue-select'
  import axios from '../../../http/axios.js'
  import u from '../../../until/helper.js'
  import datepicker from "vue2-datepicker";
  import moment from 'moment';
  import search from '../../../components/StudentSearch'
  
  export default {
    components: {
      datepicker,
      "vue-select": select,
      search
    },
    data() {
      return {
        alert:{
          active: false,
          body: '',
          color:'',
        },
        branch_transfer_info:{},
        comment:'',
        approve:'',
        logs:[],
      }
    },
    created() {
      this.loadDetail(this.$route.params.id);
    },
    methods: {
      getLogs(student_id){
        this.$vs.loading();
        axios.g(`/api/lms/branch_transfers/logs/${student_id}`)
          .then(response => {
          this.$vs.loading.close();
          this.logs = response.data
        })
      },
      loadDetail(id){
        this.$vs.loading();
        axios.g(`/api/lms/branch_transfers/show/${id}`)
          .then(response => {
          this.$vs.loading.close();
          this.branch_transfer_info = response.data
          this.status = response.data.status
          this.getLogs(response.data.student_id)
        })
      },
      confirmApprove (approve) {
        this.approve = approve
        this.$vs.dialog({
          type: 'confirm',
          color: !approve ?'danger' : 'success',
          title: 'Thông báo',
          text: !approve ? `Bạn chắc chắn muốn từ chối phê duyệt bản ghi chuyển trung tâm trên?` : `Bạn chắc chắn muốn phê duyệt bản ghi chuyển trung tâm trên?`,
          accept: this.approveContract,
          acceptText: !approve ? 'Từ chối phê duyệt' : 'Phê duyệt',
          cancelText: 'Hủy'
        })
      },
      approveContract(){
        this.$vs.loading()
        axios.p("/api/lms/branch_transfers/approve",{
          branch_transfer_id: this.branch_transfer_info.id,
          approve: this.approve,
          comment: this.comment
        })
        .then((response) => {
          this.$vs.loading.close();
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          this.$router.push('/lms/branch_transfers')
        })
        .catch((e) => {
          console.log(e);
          this.$vs.loading.close();
        });
      },
      save(approve){
        let mess = "";
        let resp = true;
        if ( !approve && this.comment == "") {
          mess += " - Ghi chú phê duyệt không được để trống khi từ chối phê duyệt<br/>";
          resp = false;
        }
        if (!resp) {
          this.alert.color = 'danger'
          this.alert.body = mess;
          this.alert.active = true;
          return false;
        }
        this.confirmApprove(approve)
      },
      getStatusTextColor(value) {
        let resp = ''
        switch (Number(value)) {
            case 1:
                resp = 'text-primary';
                break;
            case 2:
                resp = 'text-danger';
                break;
            case 3:
                resp = 'text-success';
                break;
            case 4:
                resp = 'text-danger';
                break;
            case 5:
                resp = 'text-success';
                break;
            case 6:
                resp = 'text-done';
                break;
            default:
                resp = 'text-primary'
                break
        }
        return resp
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
      showAction(status){
        return [1,3].indexOf(status) != -1 ? true : false
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
    },
  }
</script>
<style>
.td.vs-table--td{
  vertical-align: top;
}
</style>
<style>
[dir] .vs-con-table .vs-con-tbody .vs-table--tbody-table .tr-values .vs-table--td{
  padding: 4px 15px;
}
[dir] .vs-con-table .vs-con-tbody .vs-table--tbody-table .vs-table--thead th{
  padding: 6px 15px;
}
.text-done{
  color: #24c1a0;
}
</style>