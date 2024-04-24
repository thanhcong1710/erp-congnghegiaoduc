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
            <div class="vx-col w-full mb-4" v-if="reserve.branch_id">
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
              <label>Số buổi được phép bảo lưu</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="student_info.reservable_sessions"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Số buổi đã bảo lưu</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="student_info.reserved_sessions"
                disabled="true"
              />
            </div>
          </div>
        </div>
        <div class="vx-col md:w-1/2 w-full item-last">
          <h5 class="w-full mb-3"><i class="fa-solid fa-file-contract mr-1"></i> Thông tin bảo lưu</h5>
          <div class="vx-row">
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Loại bảo lưu <span class="text-danger"> (*)</span></label>
              <select class="vs-inputx vs-input--input normal" v-model="reserve.is_reserved" :disabled="input_disabled">
                <option value="0">Bảo lưu không giữ chỗ</option>
                <option value="1">Bảo lưu giữ chỗ</option>
              </select>
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Số buổi bảo lưu <span class="text-danger"> (*)</span></label>
              <input
                class="vs-inputx vs-input--input normal"
                type="number"
                name="title"
                v-model="reserve.session"
                :disabled="input_disabled"
                @change="getEndDate()"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Ngày bắt đầu bảo lưu <span class="text-danger"> (*)</span></label>
              <datepicker class="w-full"
                v-model="reserve.start_date"
                placeholder="Chọn ngày bắt đầu"
                :lang="datepickerOptions.lang"
                @change="selectDate"
                :disabled="input_disabled"
                :not-before="temp.min_date"
                :not-after="temp.max_date"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Ngày kết thúc bảo lưu</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="reserve.end_date"
                disabled="true"
              />
            </div>
            <div class="vx-col w-full mb-4">
              <label>Ghi chú</label>
              <textarea class="vs-inputx vs-input--input normal" v-model="reserve.note"></textarea>
            </div>
          </div>

          <vs-alert :active.sync="alert.active" class="mb-5" :color="alert.color" closable icon-pack="feather" close-icon="icon-x">
            <div v-html="alert.body"></div>
          </vs-alert>
          <div class="vx-col w-full text-right">
            <router-link class="btn btn-danger" :to="`/lms/reserves`">
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
              action: student => this.selectStudent(student)
          }
        },
        html:{
          branches: {
            item: '',
            list: []
          },
        },
        reserve:{
          branch_id:'',
          student_id: '',
          contract_id:'',
          note:'',
          start_date:'',
          end_date:'',
          session:'',
          is_reserved: 1,
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
    },
    methods: {
      searchSuggestStudent(keyword) {
        if (keyword && keyword.length >= 3 && this.calling === false) {
          this.calling = true
          return new Promise((resolve, reject) => {
            axios.p(`/api/lms/reserves/search-student`,{
              branch_id: this.reserve.branch_id,
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
        this.reserve.student_id = student.student_id
        this.reserve.contract_id = student.contract_id
        if(!u.isGreaterThan(u.convertDateToString(this.temp.min_date), student.enrolment_start_date)){
          this.temp.min_date = new Date(student.enrolment_start_date)
        }
        if(!u.isGreaterThan(u.convertDateToString(this.temp.max_date), student.enrolment_last_date)){
          this.temp.max_date = new Date(student.enrolment_last_date)
        }
        this.input_disabled = false
      },
      saveBranch(data = null){
        if (data && typeof data === 'object') {
          const branch_id = data.id
          this.reserve.branch_id = branch_id
        }else{
          this.reserve.branch_id = ""
        }
        this.student_info = {}
        this.reserve.student_id=""
      },
      selectDate(date){
        if (date) {
          this.reserve.start_date = moment(date).format("YYYY-MM-DD");
          this.getEndDate()
        }
      },
      getEndDate(){
        if(this.reserve.start_date && this.reserve.session){
          this.$vs.loading()
          axios.p("/api/system/get-enddate-in-class",{
            session: this.reserve.session,
            start_date: this.reserve.start_date,
            class_id: this.student_info.class_id
          })
          .then((response) => {
            this.reserve.end_date = response.data
            this.$vs.loading.close();
          })
          .catch((e) => {
            console.log(e);
            this.$vs.loading.close();
          });
        }else{
          this.reserve.end_date = ''
        }
      },
      save() {
        let mess = "";
        let resp = true;
        if (this.reserve.branch_id == "") {
          mess += " - Trung tâm không được để trống<br/>";
          resp = false;
        }
        if (this.reserve.contract_id == "") {
          mess += " - Học sinh không được để trống<br/>";
          resp = false;
        }
        if (this.reserve.session == "") {
          mess += " - Số buổi bảo lưu không được để trống<br/>";
          resp = false;
        }
        if (this.reserve.start_date == "") {
          mess += " - Ngày bắt đầu bảo lưu không được để trống<br/>";
          resp = false;
        }
        if (!resp) {
          this.alert.color = 'danger'
          this.alert.body = mess;
          this.alert.active = true;
          return false;
        }
        this.$vs.loading()
        axios.p("/api/lms/reserves/add",{
          reserve: this.reserve,
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
            this.$router.push('/lms/reserves')
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