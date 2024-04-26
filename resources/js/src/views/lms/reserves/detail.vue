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
                v-model="reserve_info.branch_name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Họ tên</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="reserve_info.meta_data.student_info.name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Mã LMS</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="reserve_info.meta_data.student_info.lms_code"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Khóa học</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="reserve_info.meta_data.student_info.product_name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Chương trình</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="reserve_info.meta_data.student_info.program_name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Lớp học</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="reserve_info.meta_data.student_info.class_name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Gói phí</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="reserve_info.meta_data.student_info.tuition_fee_name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Số phí đã đóng</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                :value="reserve_info.meta_data.student_info.total_charged | formatNumber"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Tổng số buổi học</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                 v-model="reserve_info.meta_data.student_info.summary_sessions"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Số buổi được phép bảo lưu</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                 v-model="reserve_info.meta_data.student_info.reservable_sessions"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Số buổi đã bảo lưu</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                 v-model="reserve_info.meta_data.student_info.reserved_sessions"
                disabled="true"
              />
            </div>
          </div>
        </div>
        <div class="vx-col md:w-1/2 w-full item-last">
          <h5 class="w-full mb-3"><i class="fa-solid fa-file-contract mr-1"></i> Thông tin bảo lưu</h5>
          <div class="vx-row">
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Loại bảo lưu</label>
              <select class="vs-inputx vs-input--input normal" v-model="reserve_info.meta_data.reserve.is_reserved" disabled="true">
                <option value="0">Bảo lưu không giữ chỗ</option>
                <option value="1">Bảo lưu giữ chỗ</option>
              </select>
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Số buổi bảo lưu</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                 v-model="reserve_info.meta_data.reserve.session"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Ngày bắt đầu bảo lưu</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="reserve_info.meta_data.reserve.start_date"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Ngày kết thúc bảo lưu</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="reserve_info.meta_data.reserve.end_date"
                disabled="true"
              />
            </div>
            <div class="vx-col w-full mb-4">
              <label>Ghi chú</label>
              <textarea class="vs-inputx vs-input--input normal" disabled="true" v-model="reserve_info.meta_data.reserve.note"></textarea>
            </div>
            <div class="vx-col w-full mb-4">
              <label>Ghi chú phê duyệt</label>
              <textarea class="vs-inputx vs-input--input normal" v-model="comment" :disabled="status!=1"></textarea>
            </div>
            <vs-alert :active.sync="alert.active" class="mb-5 mr-4 ml-4" :color="alert.color" closable icon-pack="feather" close-icon="icon-x">
              <div v-html="alert.body"></div>
            </vs-alert>
            <div class="vx-col w-full mb-4" v-if="status!=1">
              <label>Trạng thái: <strong :class="status==2? 'text-success' :'text-danger'">{{status==2? 'Đã phê duyệt' : 'Từ chối phê duyệt'}}</strong></label>
            </div>
          </div>

          <div class="vx-col w-full text-right">
            <router-link class="btn btn-danger" :to="`/lms/reserves`">
              <vs-button color="dark" type="border" class="mb-2 mr-3" >Thoát</vs-button>
            </router-link>
            <vs-button class="mb-2  mr-3" color="success" @click="save(2)" v-if="status==1">Phê duyệt</vs-button>
            <vs-button class="mb-2" color="danger" @click="save(3)" v-if="status==1">Từ chối phê duyệt</vs-button>
          </div>
        </div>
      </div>
    </vx-card>
    
    <vx-card no-shadow class="mt-5">
      <div class="vx-row">
        <div class="vx-col w-full item-first">
          <h5 class="w-full mb-3"><i class="fa-solid fa-file-lines mr-1"></i> Lịch sử bảo lưu</h5>
          <div class="vs-component vs-con-table stripe vs-table-primary">
            <div class="con-tablex vs-table--content">
              <div class="vs-con-tbody vs-table--tbody ">
                <table class="vs-table vs-table--tbody-table">
                  <thead class="vs-table--thead">
                    <tr>
                      <th colspan="1" rowspan="1">
                        <div class="vs-table-text">Người tạo
                          <!---->
                        </div>
                      </th>
                      <th colspan="1" rowspan="1">
                        <div class="vs-table-text">Lớp học
                          <!---->
                        </div>
                      </th>

                      <th colspan="1" rowspan="1" class="text-center">
                        <div class="vs-table-text">Thông tin bảo lưu
                          <!---->
                        </div>
                      </th>
                      <th colspan="1" rowspan="1" class="text-center">
                        <div class="vs-table-text"> Phê duyệt
                          <!---->
                        </div>
                      </th>
                      <th colspan="1" rowspan="1" class="text-center">
                        <div class="vs-table-text"> Thao tác
                          <!---->
                        </div>
                      </th>
                    </tr>
                  </thead>
                  <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in logs" :key="index">
                    <!---->
                    
                    <td class="td vs-table--td">
                      <p>{{item.creator_name}}</p>
                      <p>Thời gian tạo: {{item.created_at}}</p>
                    </td>
                    <td class="td vs-table--td">
                      <p>{{item.class_name}}</p>
                      <p>{{item.branch_name}}</p>
                    </td>
                    <td class="td vs-table--td">
                      <p>Số buổi: <strong>{{item.session}}</strong></p>
                      <p>Ngày bắt đầu: {{item.start_date}}</p>
                      <p>Loại: {{item.is_reserved ? 'Giữ chỗ' : 'Không giữ chỗ'}}</p>
                    </td>
                    <td class="td vs-table--td">
                      <p><strong>{{ item.status | getStatusName}}</strong></p>
                      <p>Ngày duyệt: {{item.approved_at}}</p>
                      <p>Người duyệt: {{item.approver_name}}</p>
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
        reserve_info:{},
        comment:'',
        status:'',
        logs:[],
      }
    },
    created() {
      this.loadDetail(this.$route.params.id);
    },
    methods: {
      getLogs(student_id){
        this.$vs.loading();
        axios.g(`/api/lms/reserves/logs/${student_id}`)
          .then(response => {
          this.$vs.loading.close();
          this.logs = response.data
        })
      },
      loadDetail(id){
        this.$vs.loading();
        axios.g(`/api/lms/reserves/show/${id}`)
          .then(response => {
          this.$vs.loading.close();
          this.reserve_info = response.data
          this.status = response.data.status
          this.comment = response.data.comment
          this.getLogs(response.data.student_id)
        })
      },
      confirmApprove (status) {
        this.status = status
        this.$vs.dialog({
          type: 'confirm',
          color: status == 3 ?'danger' : 'success',
          title: 'Thông báo',
          text: status == 3 ? `Bạn chắc chắn muốn từ chối phê duyệt bản ghi bảo lưu trên?` : `Bạn chắc chắn muốn phê duyệt bản ghi bảo lưu trên?`,
          accept: this.approveContract,
          acceptText: status == 3 ? 'Từ chối phê duyệt' : 'Phê duyệt',
          cancelText: 'Hủy'
        })
      },
      approveContract(){
        this.$vs.loading()
        axios.p("/api/lms/reserves/approve",{
          reserve_id: this.reserve_info.id,
          status: this.status,
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
          this.$router.push('/lms/reserves')
        })
        .catch((e) => {
          console.log(e);
          this.$vs.loading.close();
        });
      },
      save(status){
        let mess = "";
        let resp = true;
        if (status == 3 && this.comment == "") {
          mess += " - Ghi chú phê duyệt không được để trống khi từ chối phê duyệt<br/>";
          resp = false;
        }
        if (!resp) {
          this.alert.color = 'danger'
          this.alert.body = mess;
          this.alert.active = true;
          return false;
        }
        this.confirmApprove(status)
      }
    },
    filters: {
      getStatusName(value) {
        let resp = ''
        switch (Number(value)) {
            case 1:
                resp = 'Chờ phê duyệt';
                break;
            case 2:
                resp = 'Đã phê duyệt';
                break;
            case 3:
                resp = 'Từ chối phê duyệt';
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