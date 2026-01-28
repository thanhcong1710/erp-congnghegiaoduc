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
              v-model="agreement_info.name"
              disabled="true"
            />
          </div>
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label>Mã LMS</label>
            <input
              class="vs-inputx vs-input--input normal"
              type="text"
              name="title"
              v-model="agreement_info.lms_code"
              disabled="true"
            />
          </div>
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label>Số điện thoại</label>
            <input
              class="vs-inputx vs-input--input normal"
              type="text"
              name="title"
              v-model="agreement_info.gud_mobile1"
              disabled="true"
            />
          </div>
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label>Email</label>
            <input
              class="vs-inputx vs-input--input normal"
              type="text"
              name="title"
              v-model="agreement_info.gud_email1"
              disabled="true"
            />
          </div>
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label>Địa chỉ</label>
            <input
              class="vs-inputx vs-input--input normal"
              type="text"
              name="title"
              v-model="agreement_info.address"
              disabled="true"
            />
          </div>
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label>Trung tâm</label>
            <input
              class="vs-inputx vs-input--input normal"
              type="text"
              name="title"
              v-model="agreement_info.branch_name"
              disabled="true"
            />
          </div>
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label>EC</label>
            <input
              class="vs-inputx vs-input--input normal"
              type="text"
              name="title"
              v-model="agreement_info.ec_name"
              disabled="true"
            />
          </div>
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label>EC Leader</label>
            <input
              class="vs-inputx vs-input--input normal"
              type="text"
              name="title"
              v-model="agreement_info.ec_leader_name"
              disabled="true"
            />
          </div>
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label>Người tạo</label>
            <input
              class="vs-inputx vs-input--input normal"
              type="text"
              name="title"
              v-model="agreement_info.creator_name"
              disabled="true"
            />
          </div>
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label>Thời gian tạo</label>
            <input
              class="vs-inputx vs-input--input normal"
              type="text"
              name="title"
              v-model="agreement_info.created_at"
              disabled="true"
            />
          </div>
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label>Gói phí</label>
            <input
              class="vs-inputx vs-input--input normal"
              type="text"
              name="title"
              v-model="agreement_info.tuition_fee_name"
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
              :value="agreement_info.must_charge | formatNumber"
              disabled="true"
            />
          </div>
          <div class="vx-col md:w-1/4 w-full mb-4">
            <label>Tổng tiền đã thu</label>
            <input
              class="vs-inputx vs-input--input normal"
              type="text"
              name="title"
              :value="agreement_info.total_charged | formatNumber"
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
              :value="agreement_info.debt_amount | formatNumber"
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
          <div class="vx-col md:w-1/2 w-full mb-4">
            <label>Đính kèm ảnh chuyển khoản</label>
            <input 
              type="file" 
              ref="fileInput"
              @change="handleFileUpload" 
              multiple 
              accept="image/*"
              class="vs-inputx vs-input--input normal"
            />
            <small class="text-muted">Có thể chọn nhiều ảnh</small>
            <div v-if="selectedFiles.length > 0" class="mt-3">
              <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div v-for="(file, index) in selectedFiles" :key="index" class="relative">
                  <img 
                    :src="file.preview" 
                    class="w-full h-32 object-cover rounded border-2 border-gray-300 cursor-pointer" 
                    @click="viewImagePreview(file.preview)"
                  />
                  <vs-button 
                    size="small" 
                    color="danger" 
                    type="filled" 
                    icon-pack="feather" 
                    icon="icon-trash" 
                    class="absolute top-1 right-1"
                    @click="removeFile(index)"
                  ></vs-button>
                  <div class="text-xs mt-1 truncate" :title="file.name">{{ file.name }}</div>
                </div>
              </div>
            </div>
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
        agreement_info:{},
        payment:{
          method:1,
          note:'',
          charge_date:'',
          amount:''
        },
        amount:'',
        selectedFiles: []
      }
    },
    created() {
      this.loadDetail();
    },
    watch: {
      amount: function (val) {
        if (this.agreement_info.must_charge) {
          const value = u.fmc(val)
          const suma = value.n + parseInt(this.agreement_info.total_charged)
          const debt = parseInt(this.agreement_info.must_charge) - parseInt(suma)
          if (suma > parseInt(this.agreement_info.must_charge)) {
            this.amount = parseInt(this.agreement_info.must_charge, 10) - parseInt(this.agreement_info.total_charged, 10)
            this.amount = this.amount > 1000 && this.amount % 1000 > 0 ? ((this.amount / 1000) + 1) * 1000 : this.amount
          } else {
            this.agreement_info.debt_amount = debt
            this.amount = value.s
          }
          this.payment.amount = value.n
        }
      }
    },
    methods: {
      handleFileUpload(event) {
        const files = Array.from(event.target.files);
        files.forEach(file => {
          // Create preview URL for image
          const reader = new FileReader();
          reader.onload = (e) => {
            this.selectedFiles.push({
              file: file,
              name: file.name,
              preview: e.target.result
            });
          };
          reader.readAsDataURL(file);
        });
      },
      removeFile(index) {
        this.selectedFiles.splice(index, 1);
        if (this.selectedFiles.length === 0 && this.$refs.fileInput) {
          this.$refs.fileInput.value = '';
        }
      },
      viewImagePreview(url) {
        window.open(url, '_blank');
      },
      selectDate(date){
        if (date) {
          this.payment.charge_date = moment(date).format("YYYY-MM-DD");
        }
      },
      loadDetail(){
        this.$vs.loading();
        axios.g(`/api/lms/agreements/show/${this.$route.params.id}`)
          .then(response => {
          this.$vs.loading.close();
          this.agreement_info = response.data
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
        const formData = new FormData();
        formData.append('agreement_id', this.agreement_info.id);
        formData.append('note', this.payment.note);
        formData.append('charge_date', this.payment.charge_date);
        formData.append('amount', this.payment.amount);
        formData.append('method', this.payment.method);
        formData.append('type', 1);
        
        // Append files - extract file object from wrapper
        this.selectedFiles.forEach((fileObj, index) => {
          formData.append(`attachments[${index}]`, fileObj.file);
        });

        this.$vs.loading();
        axios.p(`/api/lms/accounting/charges/add`, formData)
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