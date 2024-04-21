<template>
  <div class="student-detail-info">
    <div class="mt-3">
      <h5 class="w-full"><i class="fa-solid fa-user-graduate"></i> THÔNG TIN HỌC SINH</h5>
      <div class="vx-row w-full mt-3">
        <div class="vx-col md:w-1/3 w-full mb-4">
          <label>Mã LMS</label>
          <input
            class="vs-inputx vs-input--input normal"
            type="text"
            name="title"
            v-model="student_info.lms_code"
            :disabled="true"
          />
        </div>
        <div class="vx-col md:w-1/3 w-full mb-4">
          <label>Tên học sinh <span class="text-danger"> (*)</span></label>
          <input
            class="vs-inputx vs-input--input normal"
            type="text"
            name="title"
            v-model="student_info.name"
            :disabled="disabled_edit"
          />
        </div>
        <div class="vx-col md:w-1/3 w-full mb-4">
          <label>Ngày sinh <span class="text-danger"> (*)</span></label>
          <datepicker class="w-full"
            v-model="student_info.date_of_birth"
            placeholder="Chọn ngày sinh nhật"
            :lang="datepickerOptions.lang"
            @change="selectDate"
            :disabled="disabled_edit"
          />
        </div>
        <div class="vx-col md:w-1/3 w-full mb-4">
          <label>Giới tính <span class="text-danger"> (*)</span></label>
          <select class="vs-inputx vs-input--input normal" v-model="student_info.gender" :disabled="disabled_edit">
            <option value="M">Nam</option>
            <option value="F">Nữ</option>
          </select>
        </div>
        <div class="vx-col md:w-1/3 w-full mb-4">
          <label >Tỉnh Thành Phố</label>
          <vue-select
            naem="input_province"
            label="name"
            placeholder="Chọn Tỉnh/Thành Phố"
            :options="html.province.list"
            v-model="html.province.item"
            :searchable="true"
            language="tv-VN"
            @input="getDistrict"
            :disabled="disabled_edit"
          ></vue-select>
        </div>
        <div class="vx-col md:w-1/3 w-full mb-4">
          <label >Quận huyện</label>
          <vue-select
                naem="input_district"
                label="name"
                placeholder="Chọn Quận/Huyện/Thị Xã"
                :options="html.district.list"
                v-model="html.district.item"
                :searchable="true"
                language="tv-VN"
                @input="saveDistrict"
                :disabled="disabled_edit"
            ></vue-select>
        </div>
        <div class="vx-col md:w-1/3 w-full mb-4">
          <label>Địa chỉ</label>
          <input
            class="vs-inputx vs-input--input normal"
            type="text"
            name="title"
            v-model="student_info.address"
            :disabled="disabled_edit"
          />
        </div>
        <div class="vx-col md:w-1/3 w-full mb-4">
          <label>Trường học</label>
          <input
            class="vs-inputx vs-input--input normal"
            type="text"
            name="title"
            v-model="student_info.school"
            :disabled="disabled_edit"
          />
        </div>
        <div class="vx-col md:w-1/3 w-full mb-4">
          <label >Nguồn </label>
          <input
            class="vs-inputx vs-input--input normal"
            type="text"
            name="title"
            v-model="student_info.source_name"
            disabled="true"
          />
        </div>
        <div class="vx-col md:w-1/3 w-full mb-4">
          <label >Trạng thái </label>
          <input
            class="vs-inputx vs-input--input normal"
            type="text"
            name="title"
            v-model="student_info.status_label"
            disabled="true"
          />
        </div>
      </div>
      <vs-divider/>
      <h5 class="w-full"><i class="fa-solid fa-person-breastfeeding"></i> THÔNG TIN PHỤ HUYNH</h5>
      <div class="vx-row w-full mt-3">
        <div class="vx-col md:w-1/3 w-full mb-4">
          <label>Họ tên phụ huynh 1 <span class="text-danger"> (*)</span></label>
          <input
            class="vs-inputx vs-input--input normal"
            type="text"
            name="title"
            v-model="student_info.gud_name1"
            :disabled="disabled_edit"
          />
        </div>
        <div class="vx-col md:w-1/3 w-full mb-4">
          <label>Số điện thoại  <span class="text-danger"> (*)</span></label>
          <input
            class="vs-inputx vs-input--input normal"
            type="text"
            name="title"
            v-model="student_info.gud_mobile1"
            disabled="true"
          />
        </div>
        <div class="vx-col md:w-1/3 w-full mb-4">
          <label>Email </label>
          <input
            class="vs-inputx vs-input--input normal"
            type="text"
            name="title"
            v-model="student_info.gud_email1"
            :disabled="disabled_edit"
          />
        </div>
        <div class="vx-col md:w-1/3 w-full mb-4">
          <label>Ngày sinh</label>
          <datepicker class="w-full"
            v-model="student_info.gud_birth_day1"
            placeholder="Chọn ngày sinh nhật"
            :lang="datepickerOptions.lang"
            @change="selectDateGudBirthDay1"
            :disabled="disabled_edit"
          />
        </div>
        <div class="vx-col md:w-1/3 w-full mb-4">
          <label >Nghề nghiệp</label>
          <vue-select
                label="title"
                placeholder="Chọn nghề nghiệp"
                :options="html.jobs.list"
                v-model="html.jobs.item"
                :searchable="true"
                language="tv-VN"
                @input="saveJob"
                :disabled="disabled_edit"
            ></vue-select>
        </div>
      </div>
      <div class="vx-row w-full mt-3">
        <div class="vx-col md:w-1/3 w-full mb-4">
          <label>Họ tên phụ huynh 2</label>
          <input
            class="vs-inputx vs-input--input normal"
            type="text"
            name="title"
            v-model="student_info.gud_name2"
            :disabled="disabled_edit"
          />
        </div>
        <div class="vx-col md:w-1/3 w-full mb-4">
          <label>Số điện thoại</label>
          <input
            class="vs-inputx vs-input--input normal"
            type="text"
            name="title"
            v-model="student_info.gud_mobile2"
            :disabled="disabled_edit"
          />
        </div>
        <div class="vx-col md:w-1/3 w-full mb-4">
          <label>Email </label>
          <input
            class="vs-inputx vs-input--input normal"
            type="text"
            name="title"
            v-model="student_info.gud_email2"
            :disabled="disabled_edit"
          />
        </div>
        <div class="vx-col md:w-1/3 w-full mb-4">
          <label>Ngày sinh</label>
          <datepicker class="w-full"
            v-model="student_info.gud_birth_day2"
            placeholder="Chọn ngày sinh nhật"
            :lang="datepickerOptions.lang"
            @change="selectDateGudBirthDay2"
            :disabled="disabled_edit"
          />
        </div>
        <div class="vx-col md:w-1/3 w-full mb-4">
          <label >Nghề nghiệp</label>
          <vue-select
                label="title"
                placeholder="Chọn nghề nghiệp"
                :options="html.jobs.list"
                v-model="html.jobs.item2"
                :searchable="true"
                language="tv-VN"
                @input="saveJob2"
                :disabled="disabled_edit"
            ></vue-select>
        </div>
      </div>
      <vs-divider/>
      <h5 class="w-full"><i class="fa-solid fa-school"></i> THÔNG TIN TRUNG TÂM</h5>
      <div class="vx-row w-full mt-3">
        <div class="vx-col md:w-1/3 w-full mb-4">
          <label>Trung tâm</label>
          <input
            class="vs-inputx vs-input--input normal"
            type="text"
            name="title"
            v-model="student_info.branch_name"
            disabled="true"
          />
        </div>
        <div class="vx-col md:w-1/3 w-full mb-4">
          <label>Nhân viên kinh doanh</label>
          <vue-select
                label="title"
                placeholder="Chọn nghề nghiệp"
                :options="html.jobs.list"
                v-model="html.jobs.item"
                :searchable="true"
                language="tv-VN"
                @input="saveJob"
                :disabled="disabled_edit"
            ></vue-select>
        </div>
        <div class="vx-col md:w-1/3 w-full mb-4">
          <label>Nhân viên vận hành lớp</label>
          <vue-select
                label="title"
                placeholder="Chọn nghề nghiệp"
                :options="html.jobs.list"
                v-model="html.jobs.item"
                :searchable="true"
                language="tv-VN"
                @input="saveJob"
                :disabled="disabled_edit"
            ></vue-select>
        </div>
      </div>
      <vs-alert :active.sync="alert.active" class="mb-5" :color="alert.color" closable icon-pack="feather" close-icon="icon-x">
        <div v-html="alert.body"></div>
      </vs-alert>
      <div class="mt-5">
         <vs-button class="mb-2" @click="disabled_edit=false" v-if="disabled_edit== true">Cập nhật thông tin</vs-button>
        <vs-button color="dark" class="mb-2 mr-3" @click="reloadPage" v-if="disabled_edit== false">Hủy</vs-button>
        <vs-button class="mb-2" color="success" @click="saveInfo" v-if="disabled_edit== false">Lưu</vs-button>
      </div>
    </div>
  </div>
</template>

<script>
  import axios from '../../../../http/axios.js'
  import moment from 'moment';
  import u from '../../../../until/helper.js'
  import datepicker from "vue2-datepicker";
  import select from 'vue-select'

  export default {
    components: {
      "vue-select": select,
      moment,
      datepicker
    },
    props: {
      student_info: {
        type: Object,
        default: () => {}
      },
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
        disabled_edit:true,
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
            item2: '',
            item: '',
            list: []
          },
        },
        tmp_district_id:'',
      }
    },
    async created() {
      await axios.g(`/api/system/provinces`)
        .then(response => {
        this.html.province.list = response.data
      })
      await axios.g(`/api/system/jobs`)
        .then(response => {
        this.html.jobs.list = response.data
      })
      this.html.jobs.item = this.html.jobs.list.filter(item => item.id == this.student_info.gud_job1)[0]
      this.html.jobs.item2 = this.html.jobs.list.filter(item => item.id == this.student_info.gud_job2)[0]
      this.html.province.item = this.html.province.list.filter(item => item.id == this.student_info.province_id)[0]
      this.tmp_district_id = this.student_info.district_id
      this.getDistrict(this.html.province.item);
    },
    methods: {
      selectDate(date) {
        if (date) {
          this.student_info.birthday = moment(date).format("YYYY-MM-DD");
        }
      },
      selectDateGudBirthDay1(date){
        if (date) {
          this.student_info.gud_birth_day1 = moment(date).format("YYYY-MM-DD");
        }
      },
      selectDateGudBirthDay2(date){
        if (date) {
          this.student_info.gud_birth_day2 = moment(date).format("YYYY-MM-DD");
        }
      },
      getDistrict(data = null){
        if (data && typeof data === 'object') {
          const province_id = data.id
          this.html.province.item = data
          this.student_info.province_id = province_id
          this.$vs.loading()
          axios.g(`/api/system/provinces/${province_id}/districts`).then(response => {
            this.$vs.loading.close();
            this.html.district.list = response.data
            if(this.tmp_district_id){
              this.html.district.item = this.html.district.list.filter(item => item.id == this.tmp_district_id)[0]
            }else{
              this.student_info.district_id = ""
              this.html.district.item = ""
            } 
          })
        }else{
          this.html.province.item = ""
          this.student_info.province_id = ""
          this.html.district.list = []
          this.html.district.item = ""
          this.student_info.district_id = ""
        }
      },
      saveDistrict(data = null){
        if (data && typeof data === 'object') {
          const district_id = data.id
          this.html.district.item = data
          this.student_info.district_id = district_id
        }else{
          this.html.district.item = ""
          this.student_info.district_id = ""
        }
      },
      saveJob(data = null){
        if (data && typeof data === 'object') {
          const job_id = data.id
          this.html.jobs.item = data
          this.student_info.gud_job1 = job_id
        }else{
          this.html.jobs.item = ""
          this.student_info.gud_job1 = ""
        }
      },
      saveJob2(data = null){
        if (data && typeof data === 'object') {
          const job_id = data.id
          this.html.jobs.item2 = data
          this.student_info.gud_job2 = job_id
        }else{
          this.html.jobs.item2 = ""
          this.student_info.gud_job2 = ""
        }
      },
      reloadPage(){
        location.reload();
      },
      saveInfo(){
        let mess = "";
        let resp = true;
        if (this.student_info.gender == "") {
          mess += " - Giới tính không được để trống<br/>";
          resp = false;
        }
        if (this.student_info.name == "") {
          mess += " - Họ tên không được để trống<br/>";
          resp = false;
        }
        if (this.student_info.gud_name1 == "") {
          mess += " - Họ tên phụ huynh 1 không được để trống<br/>";
          resp = false;
        }
        if (this.student_info.gud_mobile2 != null && this.student_info.gud_mobile2 != "" && !u.vld.phone(this.student_info.gud_mobile2)) {
          mess += " - Số điện thoại 2 không đúng định dạng<br/>";
          resp = false;
        }
        if (!resp) {
          this.alert.color = 'danger'
          this.alert.body = mess;
          this.alert.active = true;
          return false;
        }
        this.$vs.loading()
        axios.p("/api/lms/students/update",this.student_info)
        .then((response) => {
          this.$vs.loading.close();;
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          this.disabled_edit = true
        })
        .catch((e) => {
          console.log(e);
          this.$vs.loading.close();
        });
      },
    }
  }
</script>