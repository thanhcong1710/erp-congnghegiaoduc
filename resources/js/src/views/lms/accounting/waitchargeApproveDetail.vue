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
      <div  v-if="payment.status==0">
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
              :disabled="payment.status!=0 || !checkPermission('approve_add_fee')"
            />
          </div>
          <div class="vx-col md:w-1/4 w-full mb-4">
            <label>Công nợ</label>
            <input
              class="vs-inputx vs-input--input normal"
              type="text"
              :value="agreement_info.debt_amount | formatNumber"
              disabled="true"
            />
          </div>
          <!-- ======= GIẢM TRỪ ======= -->
          <div class="vx-col w-full mb-2" v-if="payment.status==0 && checkPermission('approve_add_fee')">
            <vs-divider />
            <h6 class="mb-3" style="color:#7c3aed"><i class="fas fa-tag mr-1"></i> Giảm trừ (Discount)</h6>
          </div>
          <div class="vx-col md:w-1/4 w-full mb-4" v-if="payment.status==0 && checkPermission('approve_add_fee')">
            <label>Số tiền giảm trừ</label>
            <input
              class="vs-inputx vs-input--input normal"
              type="number"
              min="0"
              v-model.number="discountAmount"
              placeholder="Nhập số tiền giảm trừ"
            />
          </div>
          <div class="vx-col md:w-2/4 w-full mb-4" v-if="payment.status==0 && checkPermission('approve_add_fee')">
            <label>Lý do giảm trừ</label>
            <input
              class="vs-inputx vs-input--input normal"
              type="text"
              v-model="discountNote"
              placeholder="Nhập lý do giảm trừ"
            />
          </div>
          <div class="vx-col md:w-1/4 w-full mb-4 flex items-end" v-if="payment.status==0 && checkPermission('approve_add_fee')">
            <vs-button color="warning" type="filled" @click="applyDiscount" icon-pack="feather" icon="icon-scissors">
              Áp dụng giảm trừ
            </vs-button>
          </div>
          <!-- Hiển thị discount đã áp dụng -->
          <div class="vx-col w-full mb-4" v-if="agreement_info.discount_amount > 0">
            <vs-alert color="warning" active="true">
              <span><strong>Đã giảm trừ: {{ agreement_info.discount_amount | formatNumber }} đ</strong></span>
              <span v-if="agreement_info.discount_note"> — {{ agreement_info.discount_note }}</span>
            </vs-alert>
          </div>
          <div class="vx-col w-full mb-2" v-if="payment.status==0 && checkPermission('approve_add_fee')">
            <vs-divider />
          </div>
          <!-- ======================== -->
          <div class="vx-col md:w-1/4 w-full mb-4">
            <label>Phương thức đóng phí</label>
            <select class="vs-inputx vs-input--input normal" v-model="payment.method" :disabled="payment.status!=0 || !checkPermission('approve_add_fee')">
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
              :disabled="payment.status!=0 || !checkPermission('approve_add_fee')"
            />
          </div>
          <div class="vx-col md:w-1/2 w-full mb-4">
            <label>Ghi chú</label>
            <textarea class="vs-inputx vs-input--input normal" v-model="payment.note" :disabled="payment.status!=0 || !checkPermission('approve_add_fee')"></textarea>
          </div>
          <div class="vx-col md:w-1/2 w-full mb-4" v-if="payment.status==0 && checkPermission('approve_add_fee')">
            <label>Đính kèm thêm ảnh chuyển khoản</label>
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
                    @click="removeNewFile(index)"
                  ></vs-button>
                  <div class="text-xs mt-1 truncate" :title="file.name">{{ file.name }}</div>
                </div>
              </div>
            </div>
          </div>
          <div class="vx-col w-full mb-4" v-if="existingAttachments.length > 0">
            <label>Ảnh đã đính kèm</label>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-2">
              <div v-for="(attachment, index) in existingAttachments" :key="index" class="relative">
                <img :src="getAttachmentUrl(attachment)" class="w-full h-32 object-cover rounded cursor-pointer" @click="viewImage(getAttachmentUrl(attachment))"/>
                <vs-button 
                  v-if="payment.status==0 && checkPermission('approve_add_fee')"
                  size="small" 
                  color="danger" 
                  type="filled" 
                  icon-pack="feather" 
                  icon="icon-trash" 
                  class="absolute top-1 right-1"
                  @click="removeExistingFile(index)"
                ></vs-button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-else>
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
              :value="(payment.total_charged - payment.charge_amount) | formatNumber"
              disabled="true"
            />
          </div>
          <div class="vx-col md:w-1/4 w-full mb-4">
            <label>Số tiền thu</label>
            <input
              class="vs-inputx vs-input--input normal"
              type="text"
              name="title"
               :value="payment.charge_amount | formatNumber"
              :disabled="true"
            />
          </div>
          <div class="vx-col md:w-1/4 w-full mb-4">
            <label>Công nợ</label>
            <input
              class="vs-inputx vs-input--input normal"
              type="text"debt_amount
              :value="payment.debt_amount | formatNumber"
              disabled="true"
            />
          </div>
          <!-- Hiển thị discount đã áp dụng -->
          <div class="vx-col w-full mb-4" v-if="agreement_info.discount_amount > 0">
            <vs-alert color="warning" active="true">
              <span><strong>Đã giảm trừ: {{ agreement_info.discount_amount | formatNumber }} đ</strong></span>
              <span v-if="agreement_info.discount_note"> — {{ agreement_info.discount_note }}</span>
            </vs-alert>
          </div>
          <div class="vx-col w-full mb-2" v-if="payment.status==0 && checkPermission('approve_add_fee')">
            <vs-divider />
          </div>
          <!-- ======================== -->
          <div class="vx-col md:w-1/4 w-full mb-4">
            <label>Phương thức đóng phí</label>
            <select class="vs-inputx vs-input--input normal" v-model="payment.method" :disabled="true">
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
              :disabled="true"
            />
          </div>
          <div class="vx-col md:w-1/2 w-full mb-4">
            <label>Ghi chú</label>
            <textarea class="vs-inputx vs-input--input normal" v-model="payment.note" :disabled="true"></textarea>
          </div>
          <div class="vx-col md:w-1/2 w-full mb-4" v-if="payment.status==0 && checkPermission('approve_add_fee')">
            <label>Đính kèm thêm ảnh chuyển khoản</label>
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
                    @click="removeNewFile(index)"
                  ></vs-button>
                  <div class="text-xs mt-1 truncate" :title="file.name">{{ file.name }}</div>
                </div>
              </div>
            </div>
          </div>
          <div class="vx-col w-full mb-4" v-if="existingAttachments.length > 0">
            <label>Ảnh đã đính kèm</label>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-2">
              <div v-for="(attachment, index) in existingAttachments" :key="index" class="relative">
                <img :src="getAttachmentUrl(attachment)" class="w-full h-32 object-cover rounded cursor-pointer" @click="viewImage(getAttachmentUrl(attachment))"/>
                <vs-button 
                  v-if="payment.status==0 && checkPermission('approve_add_fee')"
                  size="small" 
                  color="danger" 
                  type="filled" 
                  icon-pack="feather" 
                  icon="icon-trash" 
                  class="absolute top-1 right-1"
                  @click="removeExistingFile(index)"
                ></vs-button>
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
          <router-link class="btn btn-danger" :to="`/lms/waitcharge-approve`">
            <vs-button color="dark" type="border" class="mb-2 mr-3" >Thoát</vs-button>
          </router-link>
          <vs-button class="mb-2 mr-3" color="success" @click="save" v-if="payment.status==0 && checkPermission('approve_add_fee')">Lưu</vs-button>
          <vs-button class="mb-2 mr-3" color="success" @click="approve(1)" v-if="payment.status==0 && checkPermission('approve_add_fee')">Phê duyệt</vs-button>
          <vs-button class="mb-2 mr-3" color="danger" @click="approve(2)" v-if="payment.status==0 && checkPermission('approve_add_fee')">Từ chối</vs-button>
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
        status:'',
        discountAmount: 0,
        discountNote: '',
        selectedFiles: [],
        existingAttachments: []
      }
    },
    created() {
      this.loadDetail();
    },
    watch: {
      amount: function (val) {
        if (this.agreement_info.must_charge) {
          const value     = u.fmc(val)
          const must      = parseInt(this.agreement_info.must_charge)    || 0
          const charged   = parseInt(this.agreement_info.total_charged)  || 0
          const discount  = parseInt(this.discountAmount)                || 0
          // Số tiền tối đa có thể thu = phần còn lại sau khi trừ giảm trừ
          const maxAmount = must - charged - discount
          const suma      = value.n + charged
          if (value.n > maxAmount) {
            // Không cho vượt quá
            this.amount = maxAmount > 0 ? maxAmount : 0
            this.payment.amount = maxAmount > 0 ? maxAmount : 0
            this.agreement_info.debt_amount = 0
          } else {
            // debt = must - charged - amount - discount
            this.agreement_info.debt_amount = must - suma - discount
            this.amount = value.s
            this.payment.amount = value.n
          }
        }
      },
      discountAmount: function (val) {
        if (this.agreement_info.must_charge) {
          const must      = parseInt(this.agreement_info.must_charge)    || 0
          const charged   = parseInt(this.agreement_info.total_charged)  || 0
          const amount    = parseInt(this.payment.amount)                || 0
          const discount  = parseInt(val)                                || 0
          
          const maxAmount = must - charged - discount
          if (amount > maxAmount) {
            this.amount = maxAmount > 0 ? maxAmount : 0
            this.payment.amount = maxAmount > 0 ? maxAmount : 0
            this.agreement_info.debt_amount = 0
          } else {
            this.agreement_info.debt_amount = must - charged - amount - discount
          }
        }
      }
    },
    methods: {
      checkPermission(text){
        return u.checkPermission(this.$store.state.AppActiveUser, text)
      },
      selectDate(date){
        if (date) {
          this.payment.charge_date = moment(date).format("YYYY-MM-DD");
        }
      },
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
      removeNewFile(index) {
        this.selectedFiles.splice(index, 1);
        if (this.selectedFiles.length === 0 && this.$refs.fileInput) {
          this.$refs.fileInput.value = '';
        }
      },
      removeExistingFile(index) {
        this.existingAttachments.splice(index, 1);
      },
      viewImage(url) {
        window.open(url, '_blank');
      },
      viewImagePreview(url) {
        window.open(url, '_blank');
      },
      getAttachmentUrl(path) {
        if (!path) return '';
        if (path.indexOf('static/upload') !== -1) {
          return path.startsWith('/') ? path : '/' + path;
        }
        return `/storage/${path}`;
      },
      loadDetail(){
        this.$vs.loading();
        axios.g(`/api/lms/accounting/waitcharge-approve/${this.$route.params.id}`)
          .then(response => {
          this.$vs.loading.close();
          this.payment = response.data.payment_info
          this.agreement_info = response.data.agreement_info
          this.amount = this.payment.charge_amount
          // Load discount đã lưu
          this.discountAmount = this.agreement_info.discount_amount || 0
          this.discountNote   = this.agreement_info.discount_note   || ''
          
          // Load existing attachments
          if (this.payment.attachments) {
            if (typeof this.payment.attachments === 'string') {
              try {
                this.existingAttachments = JSON.parse(this.payment.attachments);
              } catch (e) {
                this.existingAttachments = [];
              }
            } else {
              this.existingAttachments = this.payment.attachments;
            }
          }
        })
      },
      save() {
        let mess = '';
        let resp = true;

        const must     = parseInt(this.agreement_info.must_charge)    || 0
        const charged  = parseInt(this.agreement_info.total_charged)  || 0
        const amount   = parseInt(this.payment.amount)                || 0
        const debt     = parseInt(this.agreement_info.debt_amount)    || 0
        const discount = parseInt(this.agreement_info.discount_amount)|| 0

        if (!amount) {
          mess += ' - Số tiền thu không được để trống<br/>';
          resp = false;
        }
        if (!this.payment.charge_date) {
          mess += ' - Ngày thu phí không được để trống<br/>';
          resp = false;
        }

        const inputDiscount = parseInt(this.discountAmount) || 0;
        if (inputDiscount !== discount) {
          mess += ' - Bạn đã thay đổi số tiền giảm trừ nhưng chưa ấn "Áp dụng giảm trừ". Vui lòng ấn "Áp dụng giảm trừ" trước khi lưu phiếu thu.<br/>';
          resp = false;
        }

        // Kiểm tra cân bằng: phải đóng = đã thu + số tiền thu + công nợ + giảm trừ
        if (resp) {
          const balance = charged + amount + debt + discount
          if (balance !== must) {
            mess += ` - Số liệu không cân bằng:<br/>`
              + `&nbsp;&nbsp;Đã thu: <b>${charged.toLocaleString('vi-VN')}</b>`
              + ` + Thu: <b>${amount.toLocaleString('vi-VN')}</b>`
              + ` + Công nợ: <b>${debt.toLocaleString('vi-VN')}</b>`
              + ` + Giảm trừ: <b>${discount.toLocaleString('vi-VN')}</b>`
              + ` = <b>${balance.toLocaleString('vi-VN')}</b>`
              + ` ≠ phải đóng <b>${must.toLocaleString('vi-VN')}</b><br/>`
            resp = false;
          }
        }

        if (!resp) {
          this.alert.color = 'danger'
          this.alert.body  = mess;
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
          text: `Bạn chắc chắn đã nhập đúng thông tin?`,
          accept: this.processSave,
          acceptText: 'Lưu phiếu thu',
          cancelText: 'Hủy'
        })
      },
      processSave(){
        const formData = new FormData();
        formData.append('id', this.$route.params.id);
        formData.append('agreement_id', this.agreement_info.id);
        formData.append('note', this.payment.note);
        formData.append('charge_date', this.payment.charge_date);
        formData.append('amount', this.payment.amount);
        formData.append('method', this.payment.method);
        
        // Append new files - extract file object from wrapper
        this.selectedFiles.forEach((fileObj, index) => {
          formData.append(`attachments[${index}]`, fileObj.file);
        });
        
        // Send existing attachments as JSON to preserve them
        formData.append('existing_attachments', JSON.stringify(this.existingAttachments));

        this.$vs.loading();
        axios.p(`/api/lms/accounting/charges/update`, formData)
        .then((response) => {
          this.$vs.loading.close();
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          this.loadDetail();  
        })
      },
      applyDiscount() {
        if ( this.discountAmount < 0) {
          this.$vs.notify({ title: 'Cảnh báo', text: 'Vui lòng nhập số tiền giảm trừ >= 0', color: 'warning' })
          return
        }
        if (!this.discountNote.trim()) {
          this.$vs.notify({ title: 'Cảnh báo', text: 'Vui lòng nhập lý do giảm trừ', color: 'warning' })
          return
        }
        this.$vs.dialog({
          type: 'confirm',
          color: 'warning',
          title: 'Xác nhận giảm trừ',
          text: `Áp dụng giảm trừ ${this.discountAmount.toLocaleString('vi-VN')} đ với lý do: "${this.discountNote}"?`,
          accept: () => {
            this.$vs.loading()
            axios.p('/api/lms/accounting/charges/apply-discount', {
              agreement_id:    this.agreement_info.id,
              discount_amount: this.discountAmount,
              discount_note:   this.discountNote,
            }).then(res => {
              this.$vs.loading.close()
              this.$vs.notify({ title: 'Thành công', text: res.data.message, color: 'success', icon: 'icon-check', iconPack: 'feather' })
              this.loadDetail()
            }).catch(err => {
              this.$vs.loading.close()
              const msg = err.response && err.response.data && err.response.data.message
                ? err.response.data.message : 'Có lỗi xảy ra'
              this.$vs.notify({ title: 'Lỗi', text: msg, color: 'danger' })
            })
          },
          acceptText: 'Áp dụng',
          cancelText: 'Hủy'
        })
      },
      approve(status){
        this.status = status;
        this.$vs.dialog({
          type: 'confirm',
          color: 'danger',
          title: 'Thông báo',
          text: status==1 ? `Bạn chắc chắn muốn duyệt phiếu thu?` : 'Bạn chắc chắn muốn từ chối phiếu thu?',
          accept: this.processApprove,
          acceptText: status==1 ? 'Duyệt' : 'Từ chối',
          cancelText: 'Hủy'
        })
      },
      processApprove(){
        const data = {
          id: this.$route.params.id,
          status: this.status
        };
        this.$vs.loading();
        axios.p(`/api/lms/accounting/waitcharge-approve/update`, data)
        .then((response) => {
          this.$vs.loading.close();
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          this.$router.push('/lms/waitcharge-approve')
        })
        .catch((err) => {
          this.$vs.loading.close();
          const msg = err.response && err.response.data && err.response.data.message
            ? err.response.data.message : 'Có lỗi xảy ra khi duyệt phiếu thu'
          this.alert.color  = 'danger'
          this.alert.body   = '⚠️ ' + msg
          this.alert.active = true
          // scroll lên alert
          window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' })
        })
      }
    },
  }
</script>