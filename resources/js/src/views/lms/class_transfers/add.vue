<template>

  <div id="page-users-list">
    <vx-card no-shadow class="mt-5">
      <div class="vx-row">
        <div class="vx-col md:w-1/2 w-full item-first">
          <h5 class="w-full mb-3"><i class="fa-solid fa-user-graduate mr-1"></i> Thông tin học sinh</h5>
          <div class="vx-row">
            <div class="vx-col w-full mb-4">
              <label>Chọn trung tâm <span class="text-danger"> (*)</span></label>
              <vue-select
                    label="name"
                    placeholder="Chọn trung tâm để giới hạn phạm vi tìm kiếm"
                    :options="html.branches.list"
                    v-model="html.branches.item"
                    :searchable="true"
                    language="tv-VN"
                     @input="saveBranch"
                ></vue-select>
            </div>
            <div class="vx-col w-full mb-4" v-if="class_transfer.branch_id">
              <label> Chọn học sinh <span class="text-danger"> (*)</span></label>
              <search
                  :endpoint="filter.search.link"
                  :suggestStudents="filter.search.find"
                  :onSelectStudent="filter.search.action">
              </search>
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Họ tên</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="student_info.name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Mã LMS</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="student_info.lms_code"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Khóa học</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="student_info.product_name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Chương trình</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="student_info.program_name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Lớp học</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="student_info.class_name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Gói phí</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="student_info.tuition_fee_name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Số phí đã đóng</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                :value="student_info.total_charged | formatNumber"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Tổng số buổi học</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="student_info.summary_sessions"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Số buổi học bổng</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="student_info.bonus_sessions"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Số buổi đã học </label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="student_info.done_sessions"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Số buổi còn lại</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="student_info.left_sessions"
                disabled="true"
              />
            </div>
          </div>
        </div>
        <div class="vx-col md:w-1/2 w-full item-last">
          <h5 class="w-full mb-3"><i class="fa-solid fa-file-contract mr-1"></i> Thông tin lớp chuyển đến</h5>
          <div class="vx-row">
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Chọn khóa học <span class="text-danger"> (*)</span></label>
              <vue-select
                    label="name"
                    placeholder="Chọn khóa học"
                    :options="html.products.list"
                    v-model="html.products.item"
                    :searchable="true"
                    language="tv-VN"
                    @input="saveProduct"
                    :disabled="input_disabled"
                ></vue-select>
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Lớp chuyển đến <span class="text-danger"> (*)</span></label>
              <vue-select
                    label="label"
                    placeholder="Chọn lớp chuyển đến"
                    :options="html.classes.list"
                    v-model="html.classes.item"
                    :searchable="true"
                    language="tv-VN"
                    @input="saveClass"
                    :disabled="input_disabled"
                ></vue-select>
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Ngày bắt đầu chuyển lớp <span class="text-danger"> (*)</span></label>
              <datepicker class="w-full"
                v-model="class_transfer.transfer_date"
                placeholder="Chọn ngày bắt đầu"
                :lang="datepickerOptions.lang"
                @change="selectDate"
                :disabled="input_disabled"
                :not-before="temp.min_date"
                :not-after="temp.max_date"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Số buổi còn lại lúc chuyển</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="class_transfer.left_session"
                disabled="true"
              />
            </div>
            <div class="vx-col w-full mb-4">
              <label>Ghi chú</label>
              <textarea class="vs-inputx vs-input--input normal" v-model="class_transfer.note"></textarea>
            </div>
          </div>

          <vs-alert :active.sync="alert.active" class="mb-5" :color="alert.color" closable icon-pack="feather" close-icon="icon-x">
            <div v-html="alert.body"></div>
          </vs-alert>
          <div class="vx-col w-full text-right">
            <router-link class="btn btn-danger" :to="`/lms/class_transfers`">
              <vs-button color="dark" type="border" class="mb-2 mr-3" >Hủy</vs-button>
            </router-link>
            <vs-button class="mb-2" color="success" @click="save">Thêm mới</vs-button>
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
        calling:false,
        filter: {
          search: {
              link: 0,
              display: 'hidden',
              find: keyword => this.searchSuggestStudent(keyword),
              action: student => this.selectStudent(student),
          }
        },
        html:{
          branches: {
            item: '',
            list: []
          },
          products: {
            item: '',
            list: []
          },
          classes: {
            item: '',
            list: []
          },
        },
        class_transfer:{
          branch_id:'',
          student_id: '',
          contract_id:'',
          product_id:'',
          class_id:'',
          note:'',
          transfer_date:'',
          left_session:'',
        },
        student_info:{
        },
        alert:{
          active: false,
          body: '',
          color:'',
        },
        input_disabled: true,
        temp:{
          min_date: new Date(),
          max_date: new Date(),
        }
      }
    },
    created() {
      axios.g(`/api/system/branches-has-user`)
        .then(response => {
        this.html.branches.list = response.data
      })
      axios.g(`/api/system/products`)
        .then(response => {
        this.html.products.list = response.data
      })
    },
    methods: {
      searchSuggestStudent(keyword) {
        this.resetInputClassTransfer()
        if (keyword && keyword.length >= 3 && this.calling === false) {
          this.calling = true
          return new Promise((resolve, reject) => {
            axios.p(`/api/lms/class_transfers/search-student`,{
              branch_id: this.class_transfer.branch_id,
              keyword: keyword
            }).then((response) => {
                const resp = response.data.length ? response.data : [{
                    label: 'Không tìm thấy',
                    branch_name: 'Không có kết quả nào phù hợp'
                }]
                this.calling = false
                resolve(resp)
            }).catch(e => console.log(e))
          })
        }
      },
      selectStudent(student) {
        this.student_info = student
        this.class_transfer.student_id = student.student_id
        this.class_transfer.contract_id = student.contract_id
        if(!u.isGreaterThan(u.convertDateToString(this.temp.min_date), student.enrolment_start_date)){
          this.temp.min_date = new Date(student.enrolment_start_date)
        }
        if(!u.isGreaterThan(u.convertDateToString(this.temp.max_date), student.enrolment_last_date)){
          this.temp.max_date = new Date(student.enrolment_last_date)
        }
        this.input_disabled = false
      },
      resetInputClassTransfer(){
        this.input_disabled = true
        this.html.products.item =''
        this.html.classes.item =''
        this.class_transfer.student_id= ''
        this.class_transfer.contract_id=''
        this.class_transfer.product_id=''
        this.class_transfer.class_id=''
        this.class_transfer.note=''
        this.class_transfer.transfer_date=''
        this.class_transfer.left_session=''
      },
      saveBranch(data = null){
        if (data && typeof data === 'object') {
          const branch_id = data.id
          this.class_transfer.branch_id = branch_id
        }else{
          this.class_transfer.branch_id = ""
        }
        this.student_info = {}
        this.resetInputClassTransfer()
        this.class_transfer.student_id=""
      },
      saveProduct(data = null){
        if (data && typeof data === 'object') {
          const product_id = data.id
          this.class_transfer.product_id = product_id
          this.loadClasses();
        }else{
          this.class_transfer.product_id = ""
          this.html.classes.list =[]
        }
      },
      saveClass(data = null){
        if (data && typeof data === 'object') {
          const class_id = data.id
          this.class_transfer.class_id = class_id
          this.loadClasses();
        }else{
          this.class_transfer.class_id = ""
          this.html.classes.list =[]
        }
      },
      loadClasses(){
        this.$vs.loading()
        axios.p("/api/system/get-class-active-by-branch-product",{
          branch_id: this.class_transfer.branch_id,
          product_id: this.class_transfer.product_id
        })
        .then((response) => {
          this.html.classes.list = response.data
          this.$vs.loading.close();
        })
        .catch((e) => {
          console.log(e);
          this.$vs.loading.close();
        });
      },
      selectDate(date){
        if (date) {
          this.class_transfer.transfer_date = moment(date).format("YYYY-MM-DD");
          this.getLeftSession()
        }
      },
      getLeftSession(){
        if(this.class_transfer.transfer_date){
          this.$vs.loading()
          axios.p("/api/lms/class_transfers/get-left-sessions",{
            transfer_date: this.class_transfer.transfer_date,
            contract_id: this.student_info.contract_id
          })
          .then((response) => {
            this.class_transfer.left_session = response.data
            this.$vs.loading.close();
          })
          .catch((e) => {
            console.log(e);
            this.$vs.loading.close();
          });
        }else{
          this.class_transfer.end_date = ''
        }
      },
      save() {
        let mess = "";
        let resp = true;
        if (this.class_transfer.branch_id == "") {
          mess += " - Trung tâm không được để trống<br/>";
          resp = false;
        }
        if (this.class_transfer.contract_id == "") {
          mess += " - Học sinh không được để trống<br/>";
          resp = false;
        }
        if (this.class_transfer.product_id == "") {
          mess += " - khóa học không được để trống<br/>";
          resp = false;
        }
        if (this.class_transfer.class_id == "") {
          mess += " - Lớp học không được để trống<br/>";
          resp = false;
        }
        if (this.class_transfer.class_id == this.student_info.class_id) {
          mess += " - Lớp chuyển đến phải khác lớp hiện tại<br/>";
          resp = false;
        }
        if (this.class_transfer.transfer_date == "") {
          mess += " - Ngày bắt đầu chuyển lớp không được để trống<br/>";
          resp = false;
        }
        if (this.class_transfer.left_session !== "" && this.class_transfer.left_session === 0) {
          mess += " - Số buổi còn lại lúc chuyển phải lớn hơn 0<br/>";
          resp = false;
        }
        if (!resp) {
          this.alert.color = 'danger'
          this.alert.body = mess;
          this.alert.active = true;
          return false;
        }
        this.$vs.loading()
        axios.p("/api/lms/class_transfers/add",{
          class_transfer: this.class_transfer,
          student_info: this.student_info
        })
        .then((response) => {
          this.$vs.loading.close();
          if(response.data.status ==1){
            this.$vs.notify({
              title: 'Thành Công',
              text: response.data.message,
              color: 'success',
              iconPack: 'feather',
              icon: 'icon-check'
            })
            this.$router.push('/lms/class_transfers')
          }else{
            this.$vs.notify({
              title: 'Lỗi',
              text: response.data.message,
              iconPack: 'feather',
              icon: 'icon-alert-circle',
              color: 'warning'
            })
          }
        })
        .catch((e) => {
          console.log(e);
          this.$vs.loading.close();
        });
      }
    },
  }
</script>