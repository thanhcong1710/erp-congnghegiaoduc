<template>

  <div id="page-users-list">
    <vx-card no-shadow class="mt-5">
      <div class="vx-row">
        <div class="vx-col md:w-1/2 w-full item-first">
          <h5 class="w-full mb-3"><i class="fa-solid fa-user-graduate mr-1"></i> Thông tin học sinh</h5>
          <div class="vx-row">
            <div class="vx-col w-full mb-4">
              <label>Chọn trung tâm <span class="text-danger"> (*)</span></label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract.branch_name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Họ tên</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract.name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Mã LMS</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract.lms_code"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Phụ huynh</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract.gud_name1"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Số điện thoại</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract.gud_mobile1"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Email</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract.gud_email1"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Địa chỉ</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract.address"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Trung tâm</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract.branch_name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>EC</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract.ec_name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>EC Leader</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract.ec_leader_name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Giám đốc trung tâm</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract.ceo_branch_name"
                disabled="true"
              />
            </div>
          </div>
        </div>
        <div class="vx-col md:w-1/2 w-full item-last">
          <h5 class="w-full mb-3"><i class="fa-solid fa-file-contract mr-1"></i> Thông tin đăng ký học</h5>
          <div class="vx-row">
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Loại hợp đồng <span class="text-danger"> (*)</span></label>
              <select class="vs-inputx vs-input--input normal" v-model="contract.type" @change="loadTuitionFee();">
                <option value="" disabled>Chọn loại hợp đồng</option>
                <option value="0">Học thử</option>
                <option value="1">Chính thức</option>
              </select>
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label >Khóa học</label>
              <vue-select
                    label="name"
                    placeholder="Chọn khóa học"
                    :options="html.products.list"
                    v-model="html.products.item"
                    :searchable="true"
                    language="tv-VN"
                    @input="saveProduct"
                ></vue-select>
            </div>
            <div class="vx-col w-full mb-4">
              <label >Gói học phí</label>
              <vue-select
                    label="name"
                    placeholder="Chọn gói học phí"
                    :options="html.tuition_fee.list"
                    v-model="html.tuition_fee.item"
                    :searchable="true"
                    language="tv-VN"
                    @input="saveTuitionFee"
                    :disabled="!contract.product_id"
                ></vue-select>
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Giá gốc</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                :value="contract.tuition_fee_amount | formatNumber"
                disabled="true"
              />
            </div>
            <!-- <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Giá bán</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                :value="contract.tuition_fee_receivable | formatNumber"
                disabled="true"
              />
            </div> -->
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Số buổi chính thức</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract.tuition_fee_session"
                disabled="true"
              />
            </div>
            <div class="vx-col w-full mb-4">
              <label >Mã chiết khấu</label>
              <vue-select
                    label="name"
                    placeholder="Chọn gói học phí"
                    :options="html.discount_codes.list"
                    v-model="html.discount_codes.item"
                    :searchable="true"
                    language="tv-VN"
                    @input="saveDiscountCode"
                ></vue-select>
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Phần trăm chiết khấu(%)</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract.discount_code_percent"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Số tiền giảm trừ theo mã chiết khấu</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                :value="contract.discount_code_amount | formatNumber"
                disabled="true"
              />
            </div>
            <div class="vx-col w-full mb-4">
              <label>Mã voucher</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract.coupon_code"
                @change="checkCoupon"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Số tiền voucher</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                :value="contract.coupon_amount | formatNumber"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Số buổi học bổng</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract.coupon_session"
                disabled="true"
              />
            </div>
            <vs-divider/>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Số tiền phải đóng</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                :value="contract.total_amount | formatNumber"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Tổng số buổi học</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract.total_session"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Ngày dự kiến học</label>
              <datepicker class="w-full"
                v-model="contract.start_date"
                placeholder="Chọn ngày dự kiến học"
                :lang="datepickerOptions.lang"
                @change="selectDate"
              />
            </div>
            <div class="vx-col w-full mb-4">
              <label>Ghi chú</label>
              <textarea class="vs-inputx vs-input--input normal" v-model="contract.note"></textarea>
            </div>
          </div>

          <vs-alert :active.sync="alert.active" class="mb-5" :color="alert.color" closable icon-pack="feather" close-icon="icon-x">
            <div v-html="alert.body"></div>
          </vs-alert>
        </div>
      </div>

      <div class="vx-row mt-5">
        <div class="vx-col w-full text-right">
          <router-link class="btn btn-danger" :to="`/lms/contracts/${contract.id}/detail`">
            <vs-button color="dark" type="border" class="mb-2 mr-3" >Hủy</vs-button>
          </router-link>
          <vs-button class="mb-2" color="success" @click="save">Lưu</vs-button>
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
          products: {
            item: '',
            list: []
          },
          tuition_fee:{
            item: '',
            list: []
          },
          discount_codes:{
            item: '',
            list: []
          }
        },
        contract:{
          branch_id:'',
          student_id: '',
          product_id: '',
          type:'',
          tuition_fee_id:'', 
          tuition_fee_amount:'',
          tuition_fee_receivable:'',
          tuition_fee_session:'',
          discount_code_id:'',
          discount_code:'',
          discount_code_amount:'',
          discount_code_percent:'',
          coupon_code_check:0,
          coupon_code:'',
          coupon_amount: '',
          coupon_session: '',
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
        },
        tmp_tuition_fee_id:'',
        tmp_discount_code_id:'',
      }
    },
    async created() {
      axios.g(`/api/system/branches-has-user`)
        .then(response => {
        this.html.branches.list = response.data
      })
      await axios.g(`/api/system/products`)
        .then(response => {
        this.html.products.list = response.data
      })
      this.loadDetail();
    },
    methods: {
      loadDetail(){
        this.$vs.loading();
        axios.g(`/api/lms/contracts/show/${this.$route.params.id}`)
          .then(response => {
          this.$vs.loading.close();
          this.contract = response.data
          this.contract.total_amount = response.data.must_charge
          this.contract.total_session = response.data.total_sessions
          if(this.contract.coupon_code){
            this.contract.coupon_code_check=1
          }
          this.tmp_tuition_fee_id = response.data.tuition_fee_id
          this.tmp_discount_code_id = response.data.discount_code_id
          this.html.products.item = this.html.products.list.filter(item => item.id == response.data.product_id)[0]
          this.loadTuitionFee();
        })
      },
      
      saveProduct(data = null){
        if (data && typeof data === 'object') {
          const product_id = data.id
          this.contract.product_id = product_id
          this.loadTuitionFee();
        }else{
          this.contract.product_id = ""
          this.html.tuition_fee.list =[]
        }
        this.resetTuitionFee()
        this.html.tuition_fee.item =''
      },
      resetTuitionFee(){
        this.contract.tuition_fee_id = ''
        this.contract.tuition_fee_amount = ''
        this.contract.tuition_fee_receivable = ''
        this.contract.session = ''
      },
      saveTuitionFee(data = null){
        if (data && typeof data === 'object') {
          const tuition_fee_id = data.id
          this.contract.tuition_fee_id = tuition_fee_id
          this.contract.tuition_fee_amount = data.price
          this.contract.tuition_fee_receivable = data.receivable
          this.contract.tuition_fee_session = data.session
          this.loadDiscountCode();
          this.caculatorSession()
        }else{
          this.contract.tuition_fee_id = ""
        }
      },
      loadTuitionFee(){
        if(this.contract.product_id){
          this.$vs.loading();
          axios.p(`/api/lms/contracts/load-tuition-fee`,{
            branch_id: this.contract.branch_id,
            product_id: this.contract.product_id,
            type_contract: this.contract.type,
          }).then((response) => {
            this.$vs.loading.close();
            this.html.tuition_fee.list = response.data
            if(this.tmp_tuition_fee_id){
              this.html.tuition_fee.item = this.html.tuition_fee.list.filter(item => item.id == this.tmp_tuition_fee_id)[0]
              this.loadDiscountCode()
            }
          }).catch(e => console.log(e))
        }
      },
      loadDiscountCode(){
        this.$vs.loading();
        axios.p(`/api/lms/contracts/load-discount-code`,{
          tuition_fee_id: this.contract.tuition_fee_id
        }).then((response) => {
          this.$vs.loading.close();
          this.html.discount_codes.list = response.data
          if(this.tmp_discount_code_id){
            this.html.discount_codes.item = this.html.discount_codes.list.filter(item => item.id == this.tmp_discount_code_id)[0]
          }
        }).catch(e => console.log(e))
      },
      saveDiscountCode(data = null){
        if (data && typeof data === 'object') {
          const discount_code_id = data.id
          this.contract.discount_code_id = discount_code_id
          this.contract.discount_code = data.code
          this.contract.discount_code_percent = data.percent
          this.contract.discount_code_amount = data.discount
           this.caculatorSession()
        }else{
          this.contract.discount_code_id = ""
          this.contract.discount_code_percent = ""
          this.contract.discount_code = ""
          this.contract.discount_code_amount = ""
        }
      },
      checkCoupon(){
        this.$vs.loading.close();
        this.contract.coupon_code_check = 0;
         axios.p(`/api/lms/contracts/check-coupon`,{
          coupon_code: this.contract.coupon_code
        }).then((response) => {
          this.$vs.loading.close();
          if(response.data.status == 0){
            this.$vs.notify({
              title: 'Lỗi',
              text: response.data.message,
              iconPack: 'feather',
              icon: 'icon-alert-circle',
              color: 'warning'
            })
            this.contract.coupon_amount = ''
            this.contract.coupon_session = ''
          }else{
            this.contract.coupon_code_check = 1;
            this.contract.coupon_amount = response.data.data.coupon_amount
            this.contract.coupon_session = response.data.data.coupon_session
          }
          this.caculatorSession()
        }).catch(e => console.log(e))
      },
      selectDate(date){
        if (date) {
          this.contract.start_date = moment(date).format("YYYY-MM-DD");
        }
      },
      caculatorSession(){
        this.contract.total_amount = this.contract.tuition_fee_amount - this.contract.discount_code_amount - this.contract.coupon_amount > 0 ? this.contract.tuition_fee_amount - this.contract.discount_code_amount - this.contract.coupon_amount : 0;
        this.contract.total_session = this.contract.tuition_fee_session + this.contract.coupon_session;
      },
      save() {
        let mess = "";
        let resp = true;
        if (this.contract.type == "") {
          mess += " - Loại hợp đồng không được để trống<br/>";
          resp = false;
        }
        if (this.contract.product_id == "") {
          mess += " - Khóa học không được để trống<br/>";
          resp = false;
        }
        if (this.contract.tuition_fee_id == "") {
          mess += " - Gói học phí không được để trống<br/>";
          resp = false;
        }
        if (!resp) {
          this.alert.color = 'danger'
          this.alert.body = mess;
          this.alert.active = true;
          return false;
        }
        this.$vs.loading()
        axios.p("/api/lms/contracts/update",this.contract)
        .then((response) => {
          this.$vs.loading.close();
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          this.$router.push(`/lms/contracts/${this.contract.id}/detail`)
        })
        .catch((e) => {
          console.log(e);
          this.$vs.loading.close();
        });
      }
    },
  }
</script>