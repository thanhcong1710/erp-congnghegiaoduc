<template>

  <div id="page-users-list">
    <vx-card no-shadow class="mt-5">
      <div class="vx-row">
        <div class="vx-col md:w-1/2 w-full item-first">
          <h5 class="w-full mb-3"><i class="fa-solid fa-user-graduate mr-1"></i> Học sinh chuyển phí</h5>
          <div class="vx-row">
            <div class="vx-col w-full mb-4">
              <label>Trung tâm học sinh chuyển </label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="tuition_transfer_info.from_branch_name"
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
                        <td class="td vs-table--td"><span>{{tuition_transfer_info.meta_data.from_student_info.name}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Mã LMS:</span></td> 
                        <td class="td vs-table--td"><strong>{{tuition_transfer_info.meta_data.from_student_info.lms_code}}</strong></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Phụ huynh:</span></td> 
                        <td class="td vs-table--td"><span>{{tuition_transfer_info.meta_data.from_student_info.gud_name1}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Điện thoại:</span></td> 
                        <td class="td vs-table--td"><span>{{tuition_transfer_info.meta_data.from_student_info.gud_mobile1}}</span></td> 
                      </tr>
                       <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Email:</span></td> 
                        <td class="td vs-table--td"><span>{{tuition_transfer_info.meta_data.from_student_info.gud_email1}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Điạ chỉ:</span></td> 
                        <td class="td vs-table--td"><span>{{tuition_transfer_info.meta_data.from_student_info.address}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>EC:</span></td> 
                        <td class="td vs-table--td"><span>{{tuition_transfer_info.meta_data.from_student_info.ec_name}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>CM:</span></td> 
                        <td class="td vs-table--td"><span>{{tuition_transfer_info.meta_data.from_student_info.cm_name}}</span></td> 
                      </tr>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <div class="vx-col w-full mb-4">
              <label>Danh sách gói phí chuyển (tính đến thời điểm chuyển)</label>
              <div class="vs-component vs-con-table stripe vs-table-primary mb-5" v-for="(item, index) in tuition_transfer_info.meta_data.transferred_contracts" :key="index">
                <div class="vs-con-tbody vs-table--tbody ">
                  <table class="vs-table vs-table--tbody-table" >
                    <thead class="vs-table--thead">
                      <tr>
                        <!---->
                        <th colspan="2" rowspan="1"  class="bg-success" style="color:#fff">
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
                        <td class="td vs-table--td"><span>Số buổi còn lại:</span></td> 
                        <td class="td vs-table--td"><strong>{{item.left_sessions}}</strong> (<span>{{item.left_real_sessions}}</span> buổi chính thức)</td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td" width="160px"><span>Số phí còn lại:</span></td> 
                        <td class="td vs-table--td"><span>{{item.left_amount | formatMoney}}</span></td> 
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
            <div class="vx-col w-full mb-4">
              <label>Tổng số tiền còn lại</label>
              <input class="vs-inputx vs-input--input normal" :value="tuition_transfer_info.transferred_amount | formatMoney"  disabled="true" />
            </div>
          </div>
        </div>
        <div class="vx-col md:w-1/2 w-full item-last">
          <h5 class="w-full mb-3"><i class="fa-solid fa-user-graduate mr-1"></i> Học sinh nhận phí</h5>
          <div class="vx-row">
            <div class="vx-col w-full mb-4">
              <label>Trung tâm nhận học sinh nhận</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                 v-model="tuition_transfer_info.to_branch_name"
                disabled="true"
              />
            </div>
            <div class="vx-col w-full mb-4" v-if="tuition_transfer_info.to_student_id">
              <div class="vx-col w-full mb-4">
                <label>Hồ sơ học sinh</label>
                <div class="vs-component vs-con-table stripe vs-table-primary">
                  <div class="vs-con-tbody vs-table--tbody ">
                    <table class="vs-table vs-table--tbody-table">
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td" width="124px"><span>Họ tên:</span></td> 
                        <td class="td vs-table--td"><span>{{tuition_transfer_info.meta_data.to_student_info.name}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Mã LMS:</span></td> 
                        <td class="td vs-table--td"><strong>{{tuition_transfer_info.meta_data.to_student_info.lms_code}}</strong></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Phụ huynh:</span></td> 
                        <td class="td vs-table--td"><span>{{tuition_transfer_info.meta_data.to_student_info.gud_name1}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Điện thoại:</span></td> 
                        <td class="td vs-table--td"><span>{{tuition_transfer_info.meta_data.to_student_info.gud_mobile1}}</span></td> 
                      </tr>
                       <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Email:</span></td> 
                        <td class="td vs-table--td"><span>{{tuition_transfer_info.meta_data.to_student_info.gud_email1}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Điạ chỉ:</span></td> 
                        <td class="td vs-table--td"><span>{{tuition_transfer_info.meta_data.to_student_info.address}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>EC:</span></td> 
                        <td class="td vs-table--td"><span>{{tuition_transfer_info.meta_data.to_student_info.ec_name}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>CM:</span></td> 
                        <td class="td vs-table--td"><span>{{tuition_transfer_info.meta_data.to_student_info.cm_name}}</span></td> 
                      </tr>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <div class="vx-col w-full mb-4" v-if="tuition_transfer_info.to_student_id && tuition_transfer_info.meta_data.received_contracts.length > 0">
              <label>Danh sách gói phí nhận</label>
              <div class="vs-component vs-con-table stripe vs-table-primary mb-5" v-for="(item, index) in tuition_transfer_info.meta_data.received_contracts" :key="index">
                <div class="vs-con-tbody vs-table--tbody ">
                  <table class="vs-table vs-table--tbody-table" >
                    <thead class="vs-table--thead">
                      <tr>
                        <!---->
                        <th colspan="2" rowspan="1"  class="bg-success" style="color:#fff">
                          <div class="vs-table-text">{{index+1}}.Gói phí {{item.tuition_fee_name}}</div>
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
                        <td class="td vs-table--td"><span>Khóa học:</span></td> 
                        <td class="td vs-table--td"><span>{{item.product_name}}</span></td> 
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="vx-col w-full mb-4">
              <label>Tổng số tiền nhận</label>
              <input class="vs-inputx vs-input--input normal" :value="tuition_transfer_info.received_amount | formatMoney"  disabled="true" />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Khóa học quy đổi</label>
              <input class="vs-inputx vs-input--input normal" :value="tuition_transfer_info.to_product_name"  disabled="true" />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Ngày bắt đầu chuyển</label>
              <input class="vs-inputx vs-input--input normal" :value="tuition_transfer_info.transfer_date"  disabled="true" />
            </div>
            <div class="vx-col w-full mb-4">
              <label>Ghi chú</label>
              <textarea class="vs-inputx vs-input--input normal" disabled="true" v-model="tuition_transfer_info.note"></textarea>
            </div>
            <div class="vx-col w-full mb-4">
              <p>Người tạo: {{tuition_transfer_info.creator_name}}</p>
              <p class="mb-3">Thời gian tạo: {{tuition_transfer_info.created_at}}</p>

              <p v-if="tuition_transfer_info.ceo_approver_id">GĐTT duyệt: {{tuition_transfer_info.ceo_approver_name}}</p>
              <p v-if="tuition_transfer_info.ceo_approver_id">Thời gian duyệt: {{tuition_transfer_info.ceo_approved_at}}</p>
              <p class="mb-3" v-if="tuition_transfer_info.ceo_approver_id">Ghi chú duyệt: {{tuition_transfer_info.ceo_comment}}</p>

              <p v-if="tuition_transfer_info.accounting_approver_id">Kế toán duyệt: {{tuition_transfer_info.accounting_approver_name}}</p>
              <p v-if="tuition_transfer_info.accounting_approver_id">Thời gian duyệt: {{tuition_transfer_info.accounting_approved_at}}</p>
              <p class="mb-3" v-if="tuition_transfer_info.accounting_approver_id">Ghi chú duyệt: {{tuition_transfer_info.accounting_comment}}</p>

              <label>Trạng thái: </label>
              <strong :class="getStatusTextColor(tuition_transfer_info.status)">{{tuition_transfer_info.status | getStatusName}}</strong>
            </div>
            <div class="vx-col w-full mb-4" v-if="showAction(tuition_transfer_info.status)">
              <label>Ghi chú phê duyệt</label>
              <textarea class="vs-inputx vs-input--input normal" v-model="comment" :disabled="!showAction(tuition_transfer_info.status)"></textarea>
            </div>
            <vs-alert :active.sync="alert.active" class="mb-5 mr-4 ml-4" :color="alert.color" closable icon-pack="feather" close-icon="icon-x">
              <div v-html="alert.body"></div>
            </vs-alert>
          </div>

          <div class="vx-col w-full text-right">
            <router-link class="btn btn-danger" :to="`/lms/tuition_transfers`">
              <vs-button color="dark" type="border" class="mb-2 mr-3" >Thoát</vs-button>
            </router-link>
            <vs-button class="mb-2  mr-3" color="success" @click="save(true)" v-if="showAction(tuition_transfer_info.status)">Phê duyệt</vs-button>
            <vs-button class="mb-2" color="danger" @click="save(false)" v-if="showAction(tuition_transfer_info.status)">Từ chối phê duyệt</vs-button>
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
        tuition_transfer_info:{},
        comment:'',
        approve:'',
      }
    },
    created() {
      this.loadDetail(this.$route.params.id);
    },
    methods: {
      loadDetail(id){
        this.$vs.loading();
        axios.g(`/api/lms/tuition_transfers/show/${id}`)
          .then(response => {
          this.$vs.loading.close();
          this.tuition_transfer_info = response.data
          this.status = response.data.status
        })
      },
      confirmApprove (approve) {
        this.approve = approve
        this.$vs.dialog({
          type: 'confirm',
          color: !approve ?'danger' : 'success',
          title: 'Thông báo',
          text: !approve ? `Bạn chắc chắn muốn từ chối phê duyệt bản ghi chuyển phí trên?` : `Bạn chắc chắn muốn phê duyệt bản ghi chuyển phí trên?`,
          accept: this.approveContract,
          acceptText: !approve ? 'Từ chối phê duyệt' : 'Phê duyệt',
          cancelText: 'Hủy'
        })
      },
      approveContract(){
        this.$vs.loading()
        axios.p("/api/lms/tuition_transfers/approve",{
          tuition_transfer_id: this.tuition_transfer_info.id,
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
          this.$router.push('/lms/tuition_transfers')
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