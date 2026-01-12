<template>

  <div id="page-users-list">
    <vx-card no-shadow class="mt-5">
      <div class="vx-row">
        <div class="vx-col md:w-1/2 w-full item-first">
          <h5 class="w-full mb-3"><i class="fa-solid fa-user-graduate mr-1"></i> Thông tin học sinh</h5>
          <div class="vx-row">
            <div class="vx-col w-full mb-4">
              <label> Chọn học sinh <span class="text-danger"> (*)</span></label>
              <search
              placeholderStudent="Tìm kiếm theo tên hoặc SDT học sinh"
                  :endpoint="filter.search.link"
                  :suggestStudents="filter.search.find"
                  :onSelectStudent="filter.search.action">
              </search>
            </div>
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
                    :disabled="disabled_branch"
                ></vue-select>
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
              <label>Số điện thoại</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="student_info.gud_mobile1"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Email</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="student_info.gud_email1"
                disabled="true"
              />
            </div>
            <div class="vx-col w-full mb-4">
              <label>Địa chỉ</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="student_info.address"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>EC</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="student_info.ec_name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>EC Leader</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="student_info.ec_leader_name"
                disabled="true"
              />
            </div>
          </div>
        </div>
        <div class="vx-col md:w-1/2 w-full item-last">
          <h5 class="w-full mb-3"><i class="fa-solid fa-file-contract mr-1"></i> Thông tin đăng ký học</h5>
          <div class="vx-row">
            <div class="vx-col w-full mb-4">
              <label >Gói học phí</label>
              <vue-select
                    label="label"
                    placeholder="Chọn gói học phí"
                    :options="html.tuition_fee.list"
                    v-model="html.tuition_fee.item"
                    :searchable="true"
                    language="tv-VN"
                    @input="saveTuitionFee"
                    :disabled="!agreement.branch_id"
                ></vue-select>
            </div>
            <div class="vx-col w-full mb-4 vs-con-table stripe vs-table-primary" v-if="agreement.tuition_fee_type==2">
              <div class="con-tablex vs-table--content">
                <div class="vs-con-tbody vs-table--tbody ">
                  <table class="vs-table vs-table--tbody-table">
                    <thead class="vs-table--thead">
                      <tr>
                        <!---->
                        <th colspan="1" rowspan="1">Tên gói phí lẻ</th>
                        <th colspan="1" rowspan="1" class="text-center">Số buổi</th>
                        <th colspan="1" rowspan="1" class="text-center">Giá bán</th>
                        <th colspan="1" rowspan="1" class="text-center">Giá bán combo</th>
                      </tr>
                    </thead>
                    <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in agreement.tuition_fee_relation" :key="index">
                      <!---->
                      <td class="td vs-table--td">{{item.name}}</td>
                      <td class="td vs-table--td text-center">{{item.session}}</td>
                      <td class="td vs-table--td text-center">{{item.price | formatMoney}}</td>
                      <td class="td vs-table--td text-center">{{item.price_combo | formatMoney}}</td>
                    </tr>
                  </table>
                  
                </div>
              </div>
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Giá bán</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                :value="agreement.tuition_fee_amount | formatNumber"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4" v-if="agreement.tuition_fee_type==1">
              <label>Số buổi</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="agreement.tuition_fee_session"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Ngày dự kiến học</label>
              <datepicker class="w-full"
                v-model="agreement.start_date"
                placeholder="Chọn ngày dự kiến học"
                :lang="datepickerOptions.lang"
                @change="selectDate"
              />
            </div>
            <div class="vx-col w-full mb-4">
              <label>Ghi chú</label>
              <textarea class="vs-inputx vs-input--input normal" v-model="agreement.note"></textarea>
            </div>
            <vs-divider/>
            <div class="vx-col md:w-1/3 w-full mb-4">
            </div>
            <div class="vx-col md:w-2/3 w-full mb-4">
              <div  class="invoice-total-wrapper">
                  <div  class="invoice-total-item" style="font-weight: bold;">
                      <p  class="invoice-total-title"> Tổng tiền phải đóng: </p>
                      <p  class="invoice-total-amount"> {{ agreement.total_amount | formatMoney}} </p>
                  </div>
              </div>
            </div>
          </div>

          <vs-alert :active.sync="alert.active" class="mb-5" :color="alert.color" closable icon-pack="feather" close-icon="icon-x">
            <div v-html="alert.body"></div>
          </vs-alert>
        </div>
      </div>

      <div class="vx-row mt-5">
        <div class="vx-col w-full text-right">
          <router-link class="btn btn-danger" :to="`/lms/agreements`">
            <vs-button color="dark" type="border" class="mb-2 mr-3" >Hủy</vs-button>
          </router-link>
          <vs-button class="mb-2" color="success" @click="save">Thêm mới</vs-button>
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
        disabled_branch: true,
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
          tuition_fee:{
            item: '',
            list: []
          },
        },
        agreement:{
          branch_id:'',
          parent_id: '',
          type:'',
          tuition_fee_id:'', 
          tuition_fee_amount:'',
          tuition_fee_receivable:'',
          tuition_fee_session:'',
          tuition_fee_type:'',
          tuition_fee_relation:[],
          total_amount:'',
          total_session:'',
          start_date:'',
          note:'',
        },
        student_info:{

        },
        alert:{
          active: false,
          body: '',
          color:'',
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
        if (keyword && keyword.length >= 3 && this.calling === false) {
          this.calling = true
          return new Promise((resolve, reject) => {
            axios.p(`/api/lms/students/search-agreement`,{
              branch_id: this.agreement.branch_id,
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
        this.agreement.parent_id = student.parent_id
        if (student.branch_id){
          this.disabled_branch = true
          this.html.branches.item = this.html.branches.list.filter(item => item.id == student.branch_id)[0]
          this.agreement.branch_id = student.branch_id
          this.loadTuitionFee();
        } else {
          this.disabled_branch = false
        }
        
        
      },
      saveBranch(data = null){
        if (data && typeof data === 'object') {
          const branch_id = data.id
          this.agreement.branch_id = branch_id
          this.loadTuitionFee();
        }else{
          this.agreement.branch_id = ""
          this.html.tuition_fee.list =[]
        }
         this.resetTuitionFee()
        this.html.tuition_fee.item =''
      },
      resetTuitionFee(){
        this.agreement.tuition_fee_id = ''
        this.agreement.tuition_fee_amount = ''
        this.agreement.tuition_fee_session = ''
        this.agreement.tuition_fee_type = ''
        this.agreement.tuition_fee_relation = []
        this.agreement.session = ''
      },
      saveTuitionFee(data = null){
        if (data && typeof data === 'object') {
          const tuition_fee_id = data.id
          this.agreement.tuition_fee_id = tuition_fee_id
          this.agreement.tuition_fee_amount = data.price
          this.agreement.tuition_fee_session = data.session
          this.agreement.tuition_fee_type = data.type_fee
          this.agreement.tuition_fee_relation = data.tuition_fee_relation
          this.caculatorSession();
        }else{
          this.agreement.tuition_fee_id = ""
        }
      },
      loadTuitionFee(){
        if(this.agreement.branch_id){
          this.$vs.loading();
          axios.p(`/api/lms/agreements/load-tuition-fee`,{
            branch_id: this.agreement.branch_id,
          }).then((response) => {
            this.$vs.loading.close();
            this.html.tuition_fee.list = response.data
          }).catch(e => console.log(e))
        }
      },
      selectDate(date){
        if (date) {
          this.agreement.start_date = moment(date).format("YYYY-MM-DD");
        }
      },
      caculatorSession(){
        this.agreement.total_amount = Number(this.agreement.tuition_fee_amount) > 0 ? Number(this.agreement.tuition_fee_amount) : 0;
        this.agreement.total_session = Number(this.agreement.tuition_fee_session);
      },
      save() {
        let mess = "";
        let resp = true;
        if (this.agreement.branch_id == "") {
          mess += " - Trung tâm không được để trống<br/>";
          resp = false;
        }
        if (this.agreement.parent_id == "") {
          mess += " - Học sinh không được để trống<br/>";
          resp = false;
        }
        if (this.agreement.tuition_fee_id == "") {
          mess += " - Gói học phí không được để trống<br/>";
          resp = false;
        }
        if (this.agreement.start_date == "") {
          mess += " - Ngày dự kiến học không được để trống<br/>";
          resp = false;
        }
        if (!resp) {
          this.alert.color = 'danger'
          this.alert.body = mess;
          this.alert.active = true;
          return false;
        }
        this.$vs.loading()
        axios.p("/api/lms/agreements/add",this.agreement)
        .then((response) => {
          this.$vs.loading.close();
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          this.$router.push('/lms/agreements')
        })
        .catch((e) => {
          console.log(e);
          this.$vs.loading.close();
        });
      }
    },
  }
</script>