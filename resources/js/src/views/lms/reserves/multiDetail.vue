<template>

  <div id="page-enrolments-list">
    <vx-card no-shadow class="mt-5">
      <div class="vx-row">
        <div class="vx-col w-full item-last">
          <h5 class="w-full mb-3"><i class="fa-solid fa-file-contract mr-1"></i> Thông tin bảo lưu</h5>
          <div class="vx-row">
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Trung tâm <span class="text-danger"> (*)</span></label>
              <input
                class="vs-inputx vs-input--input normal"
                name="title"
                type="text"
                v-model="data.branch_name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Lớp <span class="text-danger"> (*)</span></label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="data.class_name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Số buổi bảo lưu <span class="text-danger"> (*)</span></label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="data.session"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Ngày bắt đầu bảo lưu <span class="text-danger"> (*)</span></label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="data.start_date"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Ngày kết thúc bảo lưu</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="data.end_date"
                disabled="true"
              />
            </div>
            <div class="vx-col w-full mb-4">
              <label>Ghi chú</label>
              <textarea class="vs-inputx vs-input--input normal" v-model="data.note" disabled="true"></textarea>
            </div>
          </div>
          <div class="mt-5">
            <h5 class="w-full mb-3"><i class="fa-solid fa-user-graduate"></i> Học sinh áp dụng</h5>
            <div class="vs-component vs-con-table stripe vs-table-primary">
              <div class="con-tablex vs-table--content">
                <div class="vs-con-tbody vs-table--tbody ">
                  <table class="vs-table vs-table--tbody-table">
                    <thead class="vs-table--thead">
                      <tr>
                        <th colspan="1" rowspan="1" class="text-center">
                          #
                        </th>
                        <th colspan="1" rowspan="1" >Học sinh</th>
                        <th colspan="1" rowspan="1" class="text-center">Mã LMS</th>
                        <th colspan="1" rowspan="1" class="text-center">Buổi học đã học</th>
                        <th colspan="1" rowspan="1" class="text-center">Tổng số buổi</th>
                        <th colspan="1" rowspan="1" class="text-center">Trạng thái</th>
                      </tr>
                    </thead>
                    <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in students" :key="index">
                      <td class="td vs-table--td text-center">
                        <div class="vs-component con-vs-checkbox vs-checkbox-primary vs-checkbox-default">
                          <input type="checkbox" :checked="item.checked" class="vs-checkbox--input" >
                          <span class="checkbox_x vs-checkbox" style="border: 2px solid rgb(180, 180, 180);">
                            <span class="vs-checkbox--check">
                              <i class="vs-icon notranslate icon-scale vs-checkbox--icon  material-icons null">check</i>
                            </span>
                          </span>
                        </div>
                      </td> 
                      <td class="td vs-table--td">{{item.name}}</td>
                      <td class="td vs-table--td text-center">{{item.lms_id}}</td>
                      <td class="td vs-table--td text-center">{{item.done_sessions}}</td>
                      <td class="td vs-table--td text-center">{{item.summary_sessions}}</td>
                      <td class="td vs-table--td text-center"> 
                          <span v-if="item.left_sessions > 0">Đang học</span>
                          <span v-else>Hết số buổi học</span>
                      </td>
                    </tr>
                  </table>
                </div>
              </div>
            </div> 
            <div class="vx-col w-full mb-4 mt-4" v-if="status!=1">
              <label>Trạng thái: <strong :class="status==2? 'text-success' :'text-danger'">{{status==4 ? 'Đã bảo lưu' : (status==2? 'Đã phê duyệt' : 'Từ chối phê duyệt')}}</strong></label>
            </div>
          </div>
          <div class="vx-col w-full mb-4 mt-4">
            <label>Ghi chú phê duyệt</label>
            <textarea class="vs-inputx vs-input--input normal" v-model="comment" :disabled="status!=1"></textarea>
          </div>
          <vs-alert :active.sync="alert.active" class="mb-5" :color="alert.color" closable icon-pack="feather" close-icon="icon-x">
            <div v-html="alert.body"></div>
          </vs-alert>
          <div class="vx-col w-full text-right mt-3">
            <router-link class="btn btn-danger" :to="`/lms/reserves`">
              <vs-button color="dark" type="border" class="mb-2 mr-3" >Hủy</vs-button>
            </router-link>
            <vs-button class="mb-2  mr-3" color="success" @click="save(2)" v-if="status==1 && checkPermission('approve_reverse')">Phê duyệt</vs-button>
            <vs-button class="mb-2" color="danger" @click="save(3)" v-if="status==1 && checkPermission('approve_reverse')">Từ chối phê duyệt</vs-button>
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
  import tree from 'vue-jstree'
  
  export default {
    components: {
      datepicker,
      "vue-select": select,
      tree,
    },
    data() {
      return {
        status:0,
        students:[],
        alert:{
          active: false,
          body: '',
          color:'',
        },
        data:{},
        comment:''
      }
    },
    created() {
      this.loadDetail(this.$route.params.id);
    },
    methods: {
      checkPermission(text){
        return u.checkPermission(this.$store.state.AppActiveUser, text)
      },
      loadDetail(id){
        this.$vs.loading();
        axios.g(`/api/lms/reserves-multi/show/${id}`)
          .then(response => {
          this.$vs.loading.close();
          this.data = response.data
          this.status = response.data.status
          this.students = response.data.meta_data.students
        })
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
        axios.p("/api/lms/reserves/approve-multi",{
          reserve_id: this.$route.params.id,
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
          this.$router.push('/lms/reserves-multi')
        })
        .catch((e) => {
          console.log(e);
          this.$vs.loading.close();
        });
      },
    },
  }
</script>
<style>
.view-enrolments.con-vs-popup .vs-popup{
  width: 90%;
}

.td.vs-table--td{
  vertical-align: top;
}
.box-item-student.active{
  border: 1px solid rgba(var(--vs-success), 1);
  background: transparent !important;
}
.box-item-student .label-box-schedule{
  font-size: 13px;
}
.box-item-student .text-date-box-schedule{
  font-size: 11px;
}
.box-item-student.active .box-status{
    font-size: 10px;
    background: rgba(var(--vs-success), 1);
    padding: 5px;
    border-radius: 6px;
    color: #fff;
}
.box-item-student .box-status{
    font-size: 10px;
    background: rgba(var(--vs-primary),1);
    padding: 5px;
    border-radius: 6px;
    color: #fff;
}
.box-item-student{
  border: 1px solid rgba(var(--vs-primary),1);
  background: transparent !important;
}
</style>