<template>

  <div id="page-users-list">
    <vx-card no-shadow class="mt-5">
        <h5 class="w-full mb-3"><i class="fa-solid fa-file-contract mr-1"></i> Thông tin hợp đồng</h5>
        <div class="vx-row">
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label>Tên học sinh</label>
            <input
              class="vs-inputx vs-input--input normal"
              type="text"
              name="title"
              v-model="contract_info.name"
              disabled="true"
            />
          </div>
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label>Mã LMS</label>
            <input
              class="vs-inputx vs-input--input normal"
              type="text"
              name="title"
              v-model="contract_info.lms_code"
              disabled="true"
            />
          </div>
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label>Phụ huynh</label>
            <input
              class="vs-inputx vs-input--input normal"
              type="text"
              name="title"
              v-model="contract_info.gud_name1"
              disabled="true"
            />
          </div>
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label>Số điện thoại</label>
            <input
              class="vs-inputx vs-input--input normal"
              type="text"
              name="title"
              v-model="contract_info.gud_mobile1"
              disabled="true"
            />
          </div>
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label>Email</label>
            <input
              class="vs-inputx vs-input--input normal"
              type="text"
              name="title"
              v-model="contract_info.gud_email1"
              disabled="true"
            />
          </div>
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label>Địa chỉ</label>
            <input
              class="vs-inputx vs-input--input normal"
              type="text"
              name="title"
              v-model="contract_info.address"
              disabled="true"
            />
          </div>
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label>Trung tâm</label>
            <input
              class="vs-inputx vs-input--input normal"
              type="text"
              name="title"
              v-model="contract_info.branch_name"
              disabled="true"
            />
          </div>
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label>EC</label>
            <input
              class="vs-inputx vs-input--input normal"
              type="text"
              name="title"
              v-model="contract_info.ec_name"
              disabled="true"
            />
          </div>
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label>EC Leader</label>
            <input
              class="vs-inputx vs-input--input normal"
              type="text"
              name="title"
              v-model="contract_info.ec_leader_name"
              disabled="true"
            />
          </div>
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label>Người tạo</label>
            <input
              class="vs-inputx vs-input--input normal"
              type="text"
              name="title"
              v-model="contract_info.creator_name"
              disabled="true"
            />
          </div>
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label>Thời gian tạo</label>
            <input
              class="vs-inputx vs-input--input normal"
              type="text"
              name="title"
              v-model="contract_info.created_at"
              disabled="true"
            />
          </div>
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label>Tổng số tiền giảm trừ</label>
            <input
              class="vs-inputx vs-input--input normal"
              type="text"
              name="title"
              :value="contract_info.total_discount | formatNumber"
              disabled="true"
            />
          </div>
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label>Khóa học</label>
            <input
              class="vs-inputx vs-input--input normal"
              type="text"
              name="title"
              v-model="contract_info.product_name"
              disabled="true"
            />
          </div>
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label>Gói phí</label>
            <input
              class="vs-inputx vs-input--input normal"
              type="text"
              name="title"
              v-model="contract_info.tuition_fee_name"
              disabled="true"
            />
          </div>
      </div>
      <vs-divider/>

      <h5 class="w-full mb-3"><i class="fa-brands fa-cc-amazon-pay"></i> Thông tin thu phí</h5>
        <div class="vx-row">
          <div class="vx-col md:w-1/4 w-full mb-4">
            <label>Số tiền phải đóng</label>
            <input
              class="vs-inputx vs-input--input normal"
              type="text"
              name="title"
              :value="contract_info.must_charge | formatNumber"
              disabled="true"
            />
          </div>
          <div class="vx-col md:w-1/4 w-full mb-4">
            <label>Tổng tiền đã thu</label>
            <input
              class="vs-inputx vs-input--input normal"
              type="text"
              name="title"
              :value="contract_info.total_charged | formatNumber"
              disabled="true"
            />
          </div>
          <div class="vx-col md:w-1/4 w-full mb-4">
            <label>Số tiền thu</label>
            <input
              class="vs-inputx vs-input--input normal"
              type="text"
              name="title"
              v-model="amount"
            />
          </div>
          <div class="vx-col md:w-1/4 w-full mb-4">
            <label>Công nợ</label>
            <input
              class="vs-inputx vs-input--input normal"
              type="text"
              name="title"
              :value="contract_info.debt_amount | formatNumber"
              disabled="true"
            />
          </div>
          <div class="vx-col md:w-1/4 w-full mb-4">
            <label>Phương thức đóng phí</label>
            <select class="vs-inputx vs-input--input normal" v-model="payment.method">
              <option value="0">Tiền mặt</option>
              <option value="1">Chuyển khoản</option>
              <option value="2">Thẻ tín dụng</option>
            </select>
          </div>
          <div class="vx-col md:w-1/4 w-full mb-4">
            <label>Ngày thu phí</label>
            <datepicker class="w-full"
              v-model="payment.charge_date"
              placeholder="Chọn ngày thu phí"
              :lang="datepickerOptions.lang"
              @change="selectDate"
            />
          </div>
          <div class="vx-col md:w-1/2 w-full mb-4">
            <label>Ghi chú</label>
            <textarea class="vs-inputx vs-input--input normal" v-model="payment.note"></textarea>
          </div>
        </div>
        <vs-alert :active.sync="alert.active" class="mb-5" :color="alert.color" closable icon-pack="feather" close-icon="icon-x">
            <div v-html="alert.body"></div>
          </vs-alert>
      <div class="vx-row mt-5">
        <div class="vx-col w-full text-right">
          <router-link class="btn btn-danger" :to="`/lms/waitcharges`">
            <vs-button color="dark" type="border" class="mb-2 mr-3" >Thoát</vs-button>
          </router-link>
          <vs-button class="mb-2" color="success" @click="save">Thêm phiếu thu</vs-button>
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
        alert:{
          active: false,
          body: '',
          color:'',
        },
        contract_info:{},
        payment:{
          method:1,
          note:'',
          charge_date:'',
          amount:''
        },
        amount:''
      }
    },
    created() {
      this.loadDetail();
    },
    watch: {
      amount: function (val) {
        if (this.contract_info.must_charge) {
          const value = u.fmc(val)
          const suma = value.n + parseInt(this.contract_info.total_charged)
          const debt = parseInt(this.contract_info.must_charge) - parseInt(suma)
          if (suma > parseInt(this.contract_info.must_charge)) {
            this.amount = parseInt(this.contract_info.must_charge, 10) - parseInt(this.contract_info.total_charged, 10)
            this.amount = this.amount > 1000 && this.amount % 1000 > 0 ? ((this.amount / 1000) + 1) * 1000 : this.amount
          } else {
            this.contract_info.debt_amount = debt
            this.amount = value.s
          }
          this.payment.amount = value.n
        }
      }
    },
    methods: {
      selectDate(date){
        if (date) {
          this.payment.charge_date = moment(date).format("YYYY-MM-DD");
        }
      },
      loadDetail(){
        this.$vs.loading();
        axios.g(`/api/lms/contracts/show/${this.$route.params.id}`)
          .then(response => {
          this.$vs.loading.close();
          this.contract_info = response.data
        })
      },
      save() {
        let mess = "";
        let resp = true;
        if (this.payment.amount == "") {
          mess += " - Số tiền thu không được để trống<br/>";
          resp = false;
        }
        if (this.payment.charge_date == "") {
          mess += " - Ngày thu phí không được để trống<br/>";
          resp = false;
        }
        if (!resp) {
          this.alert.color = 'danger'
          this.alert.body = mess;
          this.alert.active = true;
          return false;
        }
        this.confirmSave();
      },
      confirmSave () {
        this.$vs.dialog({
          type: 'confirm',
          color: 'danger',
          title: 'Thông báo',
          text: `Bạn chắc chắn đã nhập đúng thông tin? không thể hủy phiếu thu sau khi đã thêm`,
          accept: this.processSave,
          acceptText: 'Thêm phiếu thu',
          cancelText: 'Hủy'
        })
      },
      processSave(){
        const data = {
          contract_id: this.contract_info.id,
          note: this.payment.note,
          charge_date: this.payment.charge_date,
          amount: this.payment.amount,
          method: this.payment.method
        };
        this.$vs.loading();
        axios.p(`/api/lms/accounting/charges/add`,data)
        .then((response) => {
          this.$vs.loading.close();
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          this.$router.push('/lms/waitcharges')
        })
      },
    },
  }
</script>