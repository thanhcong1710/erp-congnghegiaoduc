<template>

  <div id="page-users-list">
    <vx-card no-shadow class="mt-5">
      <h5>THÔNG TIN PHỤ HUYNH</h5>
      <div class="vx-row mt-2">
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
            <div class="vx-col md:w-1/2 w-full mb-4" v-if="parent.source_id==3">
              <label  >Người giới thiệu (ĐT)</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="parent.c2c_mobile"
                @change="validatePhoneC2C"
              />
            </div>
            <div class="vx-col w-full mb-4" v-if="parent.source_id==3">
               <p><i>{{c2c_info}}</i></p>
            </div>
            
            <div class="vx-col md:w-1/2 w-full mb-4">
              <div class="form-group col-sm-6">
                <label >Trạng thái</label>
                <select class="vs-inputx vs-input--input normal" v-model="parent.status">
                  <option 
                    v-for="option in levelOptionsParent" 
                    :key="option.id" 
                    :value="option.id">
                    {{ option.id }}. {{ option.label }}
                  </option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>
      <h5>THÔNG TIN HỌC SINH</h5>
      <div class="vx-row mt-2" v-for="(student, index) in students" :key="index"> 
        <div class="vx-col md:w-1/4 w-full mb-4">
          <label v-if="students.length > 1">Họ tên học sinh ({{ index+1 }})</label>
          <label v-else>Họ tên học sinh</label>
          <input class="vs-inputx vs-input--input normal" type="text" v-model="student.name">
        </div>
        <div class="vx-col md:w-1/4 w-full mb-4">
          <label>Ngày sinh</label>
          <datepicker
            class=" w-full calendar"
            v-model="student.birthday"
            placeholder="Chọn ngày sinh nhật"
            :lang="datepickerOptions.lang"
            @change="selectDateStudent(index)"
          />
        </div>
        <div class="vx-col md:w-1/4 w-full mb-4">
          <label>Giới tính</label>
          <select class="vs-inputx vs-input--input normal" v-model="student.gender">
            <option value="M">Nam</option>
            <option value="F">Nữ</option>
          </select>
        </div>
        <div class="vx-col md:w-1/4 w-full mb-4">
          <label>Trường học</label>
          <input class="vs-inputx vs-input--input normal" type="text" v-model="student.school">
        </div>
        <div class="vx-col md:w-1/4 w-full mb-4">
          <label>Trung tâm checkin</label>
          <select class="vs-inputx vs-input--input normal" v-model="student.checkin_branch_id">
            <option value="">Chọn trung tâm</option>
            <option :value="item.id" v-for="(item, index) in branches" :key="index">{{item.name}}</option>
          </select>
        </div>
        <div class="vx-col md:w-1/4 w-full mb-4">
          <label>Ngày/Giờ Checkin</label>
          <datepicker
                    id="checkin-at"
                    class="w-full"
                    :value="student.checkin_at"
                    v-model="student.checkin_at"
                    placeholder="Chọn ngày giờ checkin"
                    :lang="datepickerOptions.lang"
                    type="datetime"
                    format="YYYY-MM-DD HH:mm"
            >
            </datepicker>
        </div>
        <div class="vx-col md:w-1/4 w-full mb-4">
          <label>Khóa học</label>
          <select class="vs-inputx vs-input--input normal" v-model="student.checkin_type_product">
            <option value="">Chọn khóa học</option>
              <option
              :value="product.id"
              v-for="(product, index) in products"
              :key="index"
            >{{product.name}}</option>
          </select>
        </div>
        <div class="vx-col md:w-1/4 w-full mb-4" >
          <vs-button v-if="students.length > 1" color="danger" type="flat" icon-pack="feather" icon="icon-trash-2" @click="removeStudent(index)">
            Xoá
          </vs-button>
          <vs-button v-if="index === students.length - 1" class="mb-4" type="border" color="primary" icon-pack="feather" icon="icon-plus" @click="addStudent">
            Thêm học sinh
          </vs-button>
        </div>
      </div> 
      <vs-alert :active.sync="alert.active" class="mb-5" :color="alert.color" closable icon-pack="feather" close-icon="icon-x">
         <div v-html="alert.body"></div>
      </vs-alert>
      <div class="vx-row mt-5">
        <div class="vx-col w-full">
          <router-link class="btn btn-danger" :to="`/lms/checkin`">
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
  import moment, { localeData } from 'moment';

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
        products:[],
        branches:[],
        students:[
          {
            name: '',
            birthday: '',
            gender: '',
            school: '',
            checkin_type_product: '',
            checkin_at:'',
            checkin_branch_id:'',
          }
        ],
        levelOptionsParent: u.levelOptionsParent
      }
    },
    methods: {
      addStudent() {
        this.students.push({
          name: '',
          birthday: '',
          gender: '',
          school: '',
          checkin_type_product: '',
          checkin_at:'',
          checkin_branch_id:'',
        });
      },
      removeStudent(index) {
        this.students.splice(index, 1);
      },
      selectDate(date) {
        if (date) {
          this.parent.birthday = moment(date).format("YYYY-MM-DD");
        }
      },
      selectDateStudent(index){
        if(this.students[index].birthday){
          this.students[index].birthday = moment(this.students[index].birthday).format("YYYY-MM-DD");
        }
        console.log(this.students[index].birthday);
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
        this.parent.source_detail = ""
        this.parent.source_detail_id = ""
        if (data && typeof data === 'object') {
          const source_id = data.id
          this.parent.source = data
          this.parent.source_id = source_id
          this.localeDataSourceDetail();
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
        axios.p("/api/lms/checkin/add",{
          'parent': this.parent,
          'students': this.students,
        })
        .then((response) => {
          this.$vs.loading.close();;
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          this.$router.push('/lms/checkin')
        })
        .catch((e) => {
          console.log(e);
          this.$vs.loading.close();
        });
      },
      localeDataSourceDetail(){
        axios.g(`/api/system/source_detail?source_id=${this.parent.source_id}`)
          .then(response => {
          this.html.source_detail.list = response.data
        })
      }
    },
    created() {
      axios.g(`/api/system/branches-has-user`)
        .then(response => {
        this.branches = response.data
      })
      axios.g(`/api/system/products`)
        .then(response => {
        this.products = response.data
      })
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
    },
  }
</script>