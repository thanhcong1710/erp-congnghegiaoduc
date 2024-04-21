<template>

  <div id="page-users-list">
    <vx-card no-shadow class="mt-5">
      <div class="vx-row">
        <div class="vx-col md:w-1/2 w-full item-first">
          <div class="vx-row">
            <div class="vx-col md:w-1/3 w-full mb-4">
              <label>Danh xưng <span class="text-danger"> (*)</span></label>
              <select class="vs-inputx vs-input--input normal" v-model="parent.gender">
                <option value="M">Ông</option>
                <option value="F">Bà</option>
              </select>
            </div>
            <div class="vx-col md:w-2/3 w-full mb-4">
              <label>Họ tên <span class="text-danger"> (*)</span></label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="parent.name"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label  >Điện thoại <span class="text-danger"> (*)</span></label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="parent.mobile_1"
                @change="validatePhone"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label  >Điện thoại 2</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="parent.mobile_2"
                @change="validatePhone2"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
               <label >Email</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="parent.email"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label >Ngày sinh </label>
              <datepicker class="w-full"
                v-model="parent.birthday"
                placeholder="Chọn ngày sinh nhật"
                :lang="datepickerOptions.lang"
                @change="selectDate"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label >Nghề nghiệp</label>
              <vue-select
                    label="title"
                    placeholder="Chọn nghề nghiệp"
                    :options="html.jobs.list"
                    v-model="parent.job"
                    :searchable="true"
                    language="tv-VN"
                     @input="saveJob"
                ></vue-select>
            </div>
            <div class="vx-col w-full mb-4">
              <label >Ghi chú</label>
              <textarea class="vs-inputx vs-input--input normal" v-model="parent.note"></textarea>
            </div>
          </div>
        </div>
        <div class="vx-col md:w-1/2 w-full item-last">
          <div class="vx-row">
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label >Tỉnh Thành Phố</label>
              <vue-select
                label="name"
                placeholder="Chọn Tỉnh/Thành Phố"
                :options="html.province.list"
                v-model="parent.province"
                :searchable="true"
                language="tv-VN"
                @input="getDistrict"
              ></vue-select>
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label >Quận huyện</label>
              <vue-select
                    label="name"
                    placeholder="Chọn Quận/Huyện/Thị Xã"
                    :options="html.district.list"
                    v-model="parent.district"
                    :searchable="true"
                    language="tv-VN"
                    @input="saveDistrict"
                ></vue-select>
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label >Người phụ trách <span class="text-danger"> (*)</span></label>
              <select class="vs-inputx vs-input--input normal" v-model="parent.owner_id">
                <option :value="item.id" v-for="(item, index) in users_manager" :key="index">{{item.label_name}}</option>
              </select>
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
               <label >Nguồn  <span class="text-danger"> (*)</span></label>
              <vue-select
                    label="name"
                    placeholder="Chọn nguồn"
                    :options="html.source.list"
                    v-model="parent.source"
                    :searchable="true"
                    language="tv-VN"
                     @input="saveSource"
                ></vue-select>
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label >Nguồn chi tiết</label>
              <vue-select
                    label="name"
                    placeholder="Chọn nguồn chi tiết"
                    :options="html.source_detail.list"
                    v-model="parent.source_detail"
                    :searchable="true"
                    language="tv-VN"
                     @input="saveSourceDetail"
                ></vue-select>
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <div class="form-group col-sm-6">
                <label >Trạng thái</label>
                <select class="vs-inputx vs-input--input normal" v-model="parent.status">
                  <option value="0">0. KH mới</option>
                  <option value="10">1. KH không liên lạc được</option>
                  <option value="20">2. KH ở vùng CMS không có cơ sở</option>
                  <option value="30">3. KH không nghe máy</option>
                  <option value="40">4. KH hẹn gọi lại sau</option>
                  <option value="50">5. KH không quan tâm</option>
                  <option value="60">6. KH không tiềm năng</option>
                  <option value="71">7.1. KH quan tâm, cần follow up date</option>
                  <option value="72">7.2. KH tiềm năng nhưng không muốn làm phiền</option>
                  <option value="73">7.3. KH đồng ý đặt lịch Checkin</option>
                  <option value="81">8.1. KH đã đến checkin</option>
                  <option value="82">8.2. KH đã mua gói phí</option>
                  <option value="83">8.3. KH đến hạn tái tục</option>
                  <option value="90">9. Danh sách đen</option>
                </select>
              </div>
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label  >Người giới thiệu (ĐT)</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="parent.c2c_mobile"
                @change="validatePhoneC2C"
              />
            </div>
            <div class="vx-col w-full mb-4">
               <p><i>{{c2c_info}}</i></p>
            </div>
          </div>
        </div>
      </div>

      <vs-alert :active.sync="alert.active" class="mb-5" :color="alert.color" closable icon-pack="feather" close-icon="icon-x">
         <div v-html="alert.body"></div>
      </vs-alert>
      <div class="vx-row">
        <div class="vx-col w-full">
          <router-link class="btn btn-danger" :to="`/settings/users`">
            <vs-button color="dark" type="border" class="mb-2 mr-3" >Hủy</vs-button>
          </router-link>
          <vs-button class="mb-2" color="success" @click="save">Thêm mới</vs-button>
        </div>
      </div>
    </vx-card>
    <vs-popup :class="'modal_'+ modal.color" :title="modal.title" :active.sync="modal.show">
      <div v-html="modal.body"></div>
    </vs-popup>

    <vs-popup :class="'modal_'+ modal_overwrite.color" :title="modal_overwrite.title" :active.sync="modal_overwrite.show">
      <div v-html="modal_overwrite.message"></div>
      <vs-button color="dark" type="border" class="mt-3 mr-3" @click="exit_overwrite">Hủy</vs-button>
      <vs-button class="mt-3" color="success" @click="overwrite">Ghi đè</vs-button>
    </vs-popup>
  </div>

</template>

<script>

  import select from 'vue-select'
  import axios from '../../../http/axios.js'
  import u from '../../../until/helper.js'
  import datepicker from "vue2-datepicker";
  import moment from 'moment';

  export default {
    components: {
      datepicker,
      "vue-select": select
    },
    data() {
      return {
        datepickerOptions: {
          closed: true,
          value: "",
          minDate: "",
          lang: {
            days: ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
            months: [
              "Tháng 1",
              "Tháng 2",
              "Tháng 3",
              "Tháng 4",
              "Tháng 5",
              "Tháng 6",
              "Tháng 7",
              "Tháng 8",
              "Tháng 9",
              "Tháng 10",
              "Tháng 11",
              "Tháng 12"
            ]
          }
        },
        alert:{
          active: false,
          body: '',
          color:'',
        },
        modal: {
          title: "THÔNG BÁO",
          show: false,
          color: "success",
          body: "Thêm mới lớp học thành công",
          action_exit: "exit",
        },
        modal_overwrite: {
          title: "GHI ĐÈ QUYỀN CHĂM SÓC KHÁCH HÀNG",
          show: false,
          color: "info",
          size:"lg",
          message:"",
        },
        html:{
          province: {
            item: '',
            list: []
          },
          district: {
            item: '',
            list: []
          },
          jobs: {
            item: '',
            list: []
          },
          source: {
            item: '',
            list: []
          },
          source_detail: {
            item: '',
            list: []
          },
        },
        parent: {
          gender: "",
          name: "",
          birthday: "",
          mobile_1: "",
          mobile_2:"",
          note: "",
          email: "",
          status: 0,
          province_id:"",
          district_id:"",
          job_id:"",
          source_id:"",
          source:"",
          source_detail_id:"",
          source_detail:"",
          job:"",
          province:"",
          district:"",
          address:"",
          owner_id:"",
          c2c_mobile:"",
        },
        users_manager:[],
        c2c_info:"",
        change_source: false,
        change_source_parent_id:"",
      }
    },
    methods: {
      selectDate(date) {
        if (date) {
          this.parent.birthday = moment(date).format("YYYY-MM-DD");
        }
      },
      validatePhone(){
        if(this.parent.mobile_1){
          const data = {
            phone: this.parent.mobile_1,
          };
          this.$vs.loading()
          axios.p(`/api/crm/parents/validate_phone`,data).then(response => {
            this.$vs.loading.close();
            if(response.data.status==0){
              this.change_source_parent_id = response.data.dup_parent_id
              this.parent.mobile_1 ="";
              this.modal.color = "warning";
              this.modal.body = response.data.message;
              this.modal.show = true;
            }else if(response.data.status==2){
              this.modal_overwrite.show = true;
              this.modal.color = "info";
              this.modal_overwrite.message = response.data.message;
            }
          })
        }
      },
      validatePhone2(){
        const data = {
          phone: this.parent.mobile_2,
        };
        this.$vs.loading()
        axios.p(`/api/crm/parents/validate_phone`,data).then(response => {
          this.$vs.loading.close();
          if(response.data.status==0){
            this.change_source_parent_id = response.data.dup_parent_id
            this.parent.mobile_2 ="";
            this.modal.color = "warning";
            this.modal.body = response.data.message;
            this.modal.show = true;
          }else if(response.data.status==2){
            this.modal_overwrite.show = true;
            this.modal.color = "info";
            this.modal_overwrite.message = response.data.message;
          }
        })
      },
      getDistrict(data = null){
        if (data && typeof data === 'object') {
          const province_id = data.id
          this.parent.province = data
          this.parent.province_id = province_id
          this.$vs.loading()
          axios.g(`/api/system/provinces/${province_id}/districts`).then(response => {
            this.$vs.loading.close();
            this.html.district.list = response.data
            this.parent.district_id = ""
            this.parent.district = ""
          })
        }else{
          this.parent.province = ""
          this.parent.province_id = ""
          this.html.district.list = []
          this.parent.district = ""
          this.parent.district_id = ""
        }
      },
      saveDistrict(data = null){
        if (data && typeof data === 'object') {
          const district_id = data.id
          this.parent.district = data
          this.parent.district_id = district_id
        }else{
          this.parent.district = ""
          this.parent.district_id = ""
        }
      },
      saveJob(data = null){
        if (data && typeof data === 'object') {
          const job_id = data.id
          this.parent.job = data
          this.parent.job_id = job_id
        }else{
          this.parent.job = ""
          this.parent.job_id = ""
        }
      },
      saveSource(data = null){
        if (data && typeof data === 'object') {
          const source_id = data.id
          this.parent.source = data
          this.parent.source_id = source_id
        }else{
          this.parent.source = ""
          this.parent.source_id = ""
        }
      },
      saveSourceDetail(data = null){
        if (data && typeof data === 'object') {
          const source_id = data.id
          this.parent.source_detail = data
          this.parent.source_detail_id = source_id
        }else{
          this.parent.source_detail = ""
          this.parent.source_detail_id = ""
        }
      },
      validatePhoneC2C(){
        this.c2c_info=""
        if(this.parent.c2c_mobile){
          const data = {
            phone: this.parent.c2c_mobile,
          };
          this.$vs.loading()
          axios.p(`/api/crm/parents/validate_c2c_phone`,data).then(response => {
            this.$vs.loading.close();
            if(response.data.status==0){
              this.parent.c2c_mobile ="";
              this.modal.color = "warning";
              this.modal.body = response.data.message;
              this.modal.show = true;
            }else{
              this.c2c_info = response.data.message
            }
          })
        }
      },
      exit_overwrite(){
        this.modal_overwrite.show = false;
        this.parent.mobile_1 ="";
        this.parent.mobile_2 ="";
      },
      overwrite(){
        const data = {
          phone: this.parent.mobile_1,
        };
        this.loading.processing = true
        this.modal_overwrite.show = false;
        u.p(`/api/crm/parents/overwrite`,data).then(response => {
          this.$vs.loading.close();
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          this.$router.push('/crm/parent')
        })
      },
      save() {
        let mess = "";
        let resp = true;
        if (this.parent.gender == "") {
          mess += " - Danh xưng không được để trống<br/>";
          resp = false;
        }
        if (this.parent.name == "") {
          mess += " - Họ tên không được để trống<br/>";
          resp = false;
        }
        if (this.parent.mobile_1 == "") {
          mess += " - Số điện thoại không được để trống<br/>";
          resp = false;
        }
        if (this.parent.mobile_1 != "" && !u.vld.phone(this.parent.mobile_1)) {
          mess += " - Số điện thoại không đúng định dạng<br/>";
          resp = false;
        }
        if (this.parent.mobile_2 != "" && !u.vld.phone(this.parent.mobile_2)) {
          mess += " - Số điện thoại 2 không đúng định dạng<br/>";
          resp = false;
        }
        if (this.parent.owner_id == "") {
          mess += " - Người phụ trách không được để trống<br/>";
          resp = false;
        }
        if (this.parent.source_id == "") {
          mess += " - Nguồn không được để trống<br/>";
          resp = false;
        }
        if (!resp) {
          this.alert.color = 'danger'
          this.alert.body = mess;
          this.alert.active = true;
          return false;
        }
        this.$vs.loading()
        axios.p("/api/crm/parents/add",this.parent)
        .then((response) => {
          this.$vs.loading.close();;
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          this.$router.push('/crm/parent')
        })
        .catch((e) => {
          console.log(e);
          this.$vs.loading.close();
        });
      }
    },
    created() {
      axios.g(`/api/users/get-data/users-manager`)
        .then(response => {
        this.users_manager = response.data
      })
      axios.g(`/api/system/provinces`)
        .then(response => {
        this.html.province.list = response.data
      })
      axios.g(`/api/system/jobs`)
        .then(response => {
        this.html.jobs.list = response.data
      })
      axios.g(`/api/system/sources`)
        .then(response => {
        this.html.source.list = response.data
      })
      axios.g(`/api/system/source_detail`)
        .then(response => {
        this.html.source_detail.list = response.data
      })
    },
  }
</script>