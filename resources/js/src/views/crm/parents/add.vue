<template>

  <div id="page-users-list" class="crm-parent-form">
    <vx-card no-shadow class="mt-5">
      <!-- ===== THÔNG TIN PHỤ HUYNH ===== -->
      <div class="pf-section">
        <h5 class="pf-title">Thông tin phụ huynh</h5>
        <div class="pf-grid">
          <div class="pf-field pf-c2">
            <label>Danh xưng <span class="text-danger">(*)</span></label>
            <select class="pf-control pf-select" v-model="parent.gender">
              <option value="" disabled>Chọn</option>
              <option value="M">Ông</option>
              <option value="F">Bà</option>
            </select>
          </div>
          <div class="pf-field pf-c4">
            <label>Họ tên <span class="text-danger">(*)</span></label>
            <input class="pf-control" type="text" v-model="parent.name" placeholder="Nhập họ tên phụ huynh" />
          </div>
          <div class="pf-field pf-c3">
            <label>Điện thoại <span class="text-danger">(*)</span></label>
            <input class="pf-control" type="text" inputmode="tel" v-model="parent.mobile_1" @change="validatePhone" placeholder="VD: 0912345678" />
          </div>
          <div class="pf-field pf-c3">
            <label>Điện thoại 2</label>
            <input class="pf-control" type="text" inputmode="tel" v-model="parent.mobile_2" @change="validatePhone2" />
          </div>

          <div class="pf-field pf-c3">
            <label>Email</label>
            <input class="pf-control" type="email" v-model="parent.email" placeholder="email@example.com" />
          </div>
          <div class="pf-field pf-c3">
            <label>Ngày sinh</label>
            <datepicker class="pf-date"
              v-model="parent.birthday"
              placeholder="Chọn ngày sinh"
              :lang="datepickerOptions.lang"
              @change="selectDate"
            />
          </div>
          <div class="pf-field pf-c3">
            <label>Tỉnh / Thành phố</label>
            <vue-select
              label="name"
              placeholder="Chọn Tỉnh/Thành phố"
              :options="html.province.list"
              v-model="parent.province"
              :searchable="true"
              language="tv-VN"
              @input="getDistrict"
            ></vue-select>
          </div>
          <div class="pf-field pf-c3">
            <label>Quận / Huyện</label>
            <vue-select
              label="name"
              placeholder="Chọn Quận/Huyện/Thị xã"
              :options="html.district.list"
              v-model="parent.district"
              :searchable="true"
              language="tv-VN"
              @input="saveDistrict"
            ></vue-select>
          </div>

          <div class="pf-field pf-c3">
            <label>Nghề nghiệp</label>
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
        </div>
      </div>

      <!-- ===== CHĂM SÓC & NGUỒN ===== -->
      <div class="pf-section">
        <h5 class="pf-title">Phụ trách &amp; nguồn khách hàng</h5>
        <div class="pf-grid">
          <div class="pf-field pf-c3">
            <label>Người phụ trách <span class="text-danger">(*)</span></label>
            <select class="pf-control pf-select" v-model="parent.owner_id">
              <option value="" disabled>Chọn người phụ trách</option>
              <option :value="item.id" v-for="(item, index) in users_manager" :key="index">{{item.label_name}}</option>
            </select>
          </div>
          <div class="pf-field pf-c3">
            <label>Nguồn <span class="text-danger">(*)</span></label>
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
          <div class="pf-field pf-c3">
            <label>Nguồn chi tiết</label>
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
          <div class="pf-field pf-c3">
            <label>Trạng thái khách hàng</label>
            <select class="pf-control pf-select" v-model="parent.status">
              <option v-for="option in levelOptionsParent" :key="option.id" :value="option.id">
                {{ option.label }}
              </option>
            </select>
          </div>

          <div class="pf-field pf-c3" v-if="parent.source_id==3">
            <label>Người giới thiệu (ĐT)</label>
            <input class="pf-control" type="text" inputmode="tel" v-model="parent.c2c_mobile" @change="validatePhoneC2C" />
          </div>
          <div class="pf-field pf-c9 pf-c2c-info" v-if="parent.source_id==3 && c2c_info">
            <i>{{c2c_info}}</i>
          </div>

          <div class="pf-field pf-c12">
            <label>Ghi chú</label>
            <textarea class="pf-control pf-textarea" rows="3" v-model="parent.note" placeholder="Ghi chú thêm về khách hàng..."></textarea>
          </div>
        </div>
      </div>

      <!-- ===== THÔNG TIN HỌC SINH ===== -->
      <div class="pf-section">
        <h5 class="pf-title">Thông tin học sinh</h5>
        <div class="pf-student" v-for="(student, index) in students" :key="index">
          <div class="pf-student-no">{{ index + 1 }}</div>
          <div class="pf-grid pf-student-grid">
            <div class="pf-field pf-c4">
              <label>Họ tên học sinh</label>
              <input class="pf-control" type="text" v-model="student.name" placeholder="Nhập họ tên học sinh">
            </div>
            <div class="pf-field pf-c3">
              <label>Ngày sinh / Năm sinh</label>
              <birthday-input v-model="student.birthday" />
            </div>
            <div class="pf-field pf-c2">
              <label>Giới tính</label>
              <select class="pf-control pf-select" v-model="student.gender">
                <option value="" disabled>Chọn</option>
                <option value="M">Nam</option>
                <option value="F">Nữ</option>
              </select>
            </div>
            <div class="pf-field pf-c3">
              <label>Trường học</label>
              <input class="pf-control" type="text" v-model="student.school" placeholder="Tên trường">
            </div>
          </div>
          <button v-if="students.length > 1" type="button" class="pf-remove" title="Xoá học sinh" @click="removeStudent(index)">
            <feather-icon icon="Trash2Icon" svgClasses="w-5 h-5" />
          </button>
        </div>
        <vs-button type="border" color="primary" icon-pack="feather" icon="icon-plus" @click="addStudent">
          Thêm học sinh
        </vs-button>
      </div>

      <vs-alert :active.sync="alert.active" class="mb-5 mt-4" :color="alert.color" closable icon-pack="feather" close-icon="icon-x">
         <div v-html="alert.body"></div>
      </vs-alert>
      <div class="pf-actions">
        <vs-button color="dark" type="border" @click="$router.push(`/crm/parent`)">Hủy</vs-button>
        <vs-button color="success" icon-pack="feather" icon="icon-check" @click="save">Thêm mới</vs-button>
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
  import BirthdayInput from "../../../components/BirthdayInput.vue";
  import moment, { localeData } from 'moment';

  export default {
    components: {
      datepicker,
      BirthdayInput,
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
          status: 1,
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
        students:[
          {
            name: '',
            birthday: '',
            gender: '',
            school: '',
          }
        ],
        levelOptionsParent: u.levelOptionsParent,
        checkDuplicate: 0,
      }
    },
    methods: {
      addStudent() {
        this.students.push({
          name: '',
          birthday: '',
          gender: '',
          school: '',
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
        if(this.parent.mobile_2){
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
        }
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
        this.$vs.loading()
        this.modal_overwrite.show = false;
        axios.p(`/api/crm/parents/overwrite`,data).then(response => {
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
      async save() {
        this.checkDuplicate = 0;
        if(this.parent.mobile_1){
          const data = {
            phone: this.parent.mobile_1,
          };
          this.$vs.loading()
          await axios.p(`/api/crm/parents/validate_phone`,data).then(response => {
            this.$vs.loading.close();
            if(response.data.status==0){
              this.change_source_parent_id = response.data.dup_parent_id
              this.parent.mobile_1 ="";
              this.modal.color = "warning";
              this.modal.body = response.data.message;
              this.modal.show = true;
              this.checkDuplicate = 1
            }else if(response.data.status==2){
              this.modal_overwrite.show = true;
              this.modal.color = "info";
              this.modal_overwrite.message = response.data.message;
              this.checkDuplicate = 1
            }
          })
        }
        if(this.checkDuplicate){
          return false;
        }
        this.checkDuplicate = 0;
        if(this.parent.mobile_2){
          const data = {
            phone: this.parent.mobile_2,
          };
          this.$vs.loading()
          await axios.p(`/api/crm/parents/validate_phone`,data).then(response => {
            this.$vs.loading.close();
            if(response.data.status==0){
              this.change_source_parent_id = response.data.dup_parent_id
              this.parent.mobile_2 ="";
              this.modal.color = "warning";
              this.modal.body = response.data.message;
              this.modal.show = true;
              this.checkDuplicate = 1
            }else if(response.data.status==2){
              this.modal_overwrite.show = true;
              this.modal.color = "info";
              this.modal_overwrite.message = response.data.message;
              this.checkDuplicate = 1
            }
          })
        }
        if(this.checkDuplicate){
          return false;
        }
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
        const students = this.students.map((st, i) => {
          const bd = u.parseManualBirthday(st.birthday)
          if (bd === null) {
            mess += ` - Ngày sinh học sinh ${st.name || (i + 1)} không đúng định dạng (yyyy hoặc dd/mm/yyyy)<br/>`;
            resp = false;
          }
          return { ...st, birthday: (bd && bd.date) || null, birthday_year_only: bd ? bd.yearOnly : 0 }
        })
        if (!resp) {
          this.alert.color = 'danger'
          this.alert.body = mess;
          this.alert.active = true;
          return false;
        }
        this.$vs.loading()
        axios.p("/api/crm/parents/add",{
          'parent': this.parent,
          'students': students,
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
          this.$router.push('/crm/parent')
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
<style>
/* ===== Form thêm mới khách hàng (crm/parents/add) ===== */
.crm-parent-form .pf-section { padding-bottom: 8px; margin-bottom: 20px; border-bottom: 1px solid #eef2f7; }
.crm-parent-form .pf-section:last-of-type { border-bottom: 0; }
.crm-parent-form .pf-title {
  font-size: 16px; font-weight: 700; color: #1e293b; text-transform: uppercase;
  letter-spacing: .3px; margin-bottom: 16px;
}
.crm-parent-form .pf-grid { display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: 16px 20px; margin-bottom: 16px; }
.crm-parent-form .pf-c2 { grid-column: span 2; }
.crm-parent-form .pf-c3 { grid-column: span 3; }
.crm-parent-form .pf-c4 { grid-column: span 4; }
.crm-parent-form .pf-c9 { grid-column: span 9; }
.crm-parent-form .pf-c12 { grid-column: span 12; }
@media (max-width: 1199px) {
  .crm-parent-form .pf-c2, .crm-parent-form .pf-c3, .crm-parent-form .pf-c4, .crm-parent-form .pf-c9 { grid-column: span 6; }
}
@media (max-width: 575px) {
  .crm-parent-form .pf-grid > .pf-field { grid-column: span 12; }
}
.crm-parent-form .pf-field { min-width: 0; }
.crm-parent-form .pf-field > label {
  display: block; font-size: 13.5px; font-weight: 600; color: #334155; margin-bottom: 6px; white-space: nowrap;
}
.crm-parent-form .pf-c2c-info { align-self: end; font-size: 13.5px; color: #475569; padding-bottom: 10px; }

/* --- Control chung: tất cả cao 40px, cùng viền --- */
.crm-parent-form .pf-control,
.crm-parent-form .birthday-input .bi-field,
.crm-parent-form .pf-date .mx-input,
.crm-parent-form .v-select .vs__dropdown-toggle {
  width: 100%; height: 40px; min-height: 40px; box-sizing: border-box;
  background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 6px;
  font-size: 14px; color: #1e293b; padding: 0 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  transition: border-color .15s, box-shadow .15s;
}
.crm-parent-form .pf-control { cursor: text; }
.crm-parent-form .pf-control::placeholder,
.crm-parent-form .pf-date .mx-input::placeholder { color: #94a3b8; }
.crm-parent-form .pf-control:hover,
.crm-parent-form .pf-date .mx-input:hover,
.crm-parent-form .v-select .vs__dropdown-toggle:hover { border-color: #a5b4fc; }
.crm-parent-form .pf-control:focus,
.crm-parent-form .birthday-input .bi-field:focus,
.crm-parent-form .pf-date .mx-input:focus,
.crm-parent-form .v-select.vs--open .vs__dropdown-toggle {
  outline: none; border-color: #7367f0; box-shadow: 0 0 0 3px rgba(115, 103, 240, 0.2);
}

/* native select: mũi tên rõ ràng */
.crm-parent-form .pf-select {
  -webkit-appearance: none; -moz-appearance: none; appearance: none; cursor: pointer; padding-right: 34px;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23334155' stroke-width='1.8' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
  background-repeat: no-repeat; background-position: right 12px center;
}
.crm-parent-form .pf-textarea { height: auto; min-height: 84px; padding: 10px 12px; resize: vertical; line-height: 1.5; }

/* vue-select */
.crm-parent-form .v-select .vs__dropdown-toggle { display: flex; align-items: center; padding: 0 6px 0 8px; cursor: pointer; }
.crm-parent-form .v-select .vs__selected-options { flex-wrap: nowrap; overflow: hidden; padding: 0; }
.crm-parent-form .v-select .vs__selected { margin: 0; padding: 0 4px; font-size: 14px; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; border: 0; }
.crm-parent-form .v-select .vs__search,
.crm-parent-form .v-select .vs__search:focus {
  border: none !important; background: transparent !important; box-shadow: none !important;
  margin: 0; padding: 0 4px; font-size: 14px; height: 36px; line-height: 36px;
}
.crm-parent-form .v-select .vs__search::placeholder { color: #94a3b8; }
.crm-parent-form .v-select .vs__actions { padding: 0 4px; }

/* datepicker */
.crm-parent-form .pf-date.mx-datepicker { width: 100% !important; }
.crm-parent-form .pf-date .mx-input { cursor: pointer; }

/* birthday-input: gợi ý dưới ô không làm lệch hàng */
.crm-parent-form .birthday-input .bi-hint { font-size: 13px; }

/* --- Học sinh --- */
.crm-parent-form .pf-student {
  position: relative; display: flex; align-items: flex-start; gap: 14px;
  padding: 16px 16px 0; margin-bottom: 14px; border: 1px solid #e2e8f0; border-radius: 8px; background: #fafbfd;
}
.crm-parent-form .pf-student-no {
  flex: 0 0 28px; height: 28px; margin-top: 30px; border-radius: 50%;
  background: rgba(115, 103, 240, .12); color: #7367f0; font-size: 13px; font-weight: 700;
  display: flex; align-items: center; justify-content: center;
}
.crm-parent-form .pf-student-grid { flex: 1; }
.crm-parent-form .pf-remove {
  flex: 0 0 40px; height: 40px; margin-top: 27px; border-radius: 6px; cursor: pointer;
  border: 1.5px solid #fecaca; background: #fff; color: #ea5455; display: flex; align-items: center; justify-content: center;
}
.crm-parent-form .pf-remove:hover { background: #fef2f2; border-color: #ea5455; }

.crm-parent-form .pf-actions { display: flex; gap: 12px; margin-top: 8px; }
.crm-parent-form .pf-actions .vs-button { min-width: 120px; font-size: 14px; }
</style>
