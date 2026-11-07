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
            <div class="vx-col w-full mb-4" v-if="contract.branch_id">
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
              <label>Phụ huynh</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="student_info.gud_name1"
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
            <div class="vx-col md:w-1/2 w-full mb-4">
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
              <label>Trung tâm</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="student_info.student_branch_name"
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
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Giám đốc trung tâm</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="student_info.ceo_branch_name"
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
                <option value="0" v-if="disabled_trial==0">Học thử</option>
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
                    :disabled="!(contract.student_id && contract.type!=='')"
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
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Số buổi học bổng</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract.discount_code_session"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4" v-if="student_info.count_sibling > 0">
              <label> <input type="checkbox" v-model="contract.sibling" @change="caculatorSession">
                Giảm trừ anh chị em học cùng 5%</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                :value="contract.sibling_discount | formatNumber"
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
              <div class="vx-col w-full mb-4">
                <label >Chính sách B2B</label>
                <vue-select
                      label="title"
                      placeholder="Chọn chính sách cho đối tác B2B"
                      :options="html.b2b_campaign.list"
                      v-model="html.b2b_campaign.item"
                      :searchable="true"
                      language="tv-VN"
                      @input="saveDiscountB2B"
                  ></vue-select>
              </div>
              <div class="vx-col md:w-1/2 w-full mb-4">
                <label>Số tiền giảm B2B</label>
                <input
                  class="vs-inputx vs-input--input normal"
                  type="text"
                  name="title"
                  :value="contract.b2b_amount | formatNumber"
                  disabled="true"
                />
              </div>
              <div class="vx-col md:w-1/2 w-full mb-4">
                <label>Số buổi học bổng B2B</label>
                <input
                  class="vs-inputx vs-input--input normal"
                  type="text"
                  name="title"
                  v-model="contract.b2b_bonus_session"
                  disabled="true"
                />
              </div>

            <!-- CHÍNH SÁCH ƯU ĐÃI STEMKEY 2026 -->
            <div class="vx-col w-full mb-4" v-if="contract.tuition_fee_id">
              <vx-card no-shadow style="background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 8px;">
                <h5 class="mb-3 font-semibold text-primary">Chính sách ưu đãi STEMKey 2026</h5>
                
                <div class="vx-row">
                  <div class="vx-col md:w-1/2 w-full mb-3">
                    <span class="text-sm font-medium">Học bổng theo kỳ hạn:</span>
                    <span class="font-bold text-success ml-2">{{ contract.scholarship_percent }}% (-{{ contract.scholarship_amount | formatNumber }} đ)</span>
                  </div>

                  <div class="vx-col md:w-1/2 w-full mb-3" v-if="contract.gift_note">
                    <span class="text-sm font-medium">Quà tặng kèm:</span>
                    <span class="font-bold text-warning ml-2">{{ contract.gift_note }}</span>
                  </div>

                  <div class="vx-col md:w-1/2 w-full mb-3">
                    <label class="flex items-center cursor-pointer">
                      <input type="checkbox" v-model="contract.is_promo_extra" :disabled="Number(contract.number_of_months) < 6" @change="caculatorSession">
                      <span class="ml-2 font-medium">Ưu đãi tháng 11: Tặng thêm 10% (gói ≥ 6 tháng)</span>
                    </label>
                    <span v-if="contract.is_promo_extra" class="text-xs text-success block">Đã giảm thêm 10%: -{{ contract.promo_extra_amount | formatNumber }} đ</span>
                  </div>

                  <div class="vx-col md:w-1/2 w-full mb-3">
                    <label class="flex items-center cursor-pointer">
                      <input type="checkbox" v-model="contract.is_checkin_group" @change="caculatorSession">
                      <span class="ml-2 font-medium">Đăng ký nhóm 3 / Check-in (-{{ contract.checkin_group_percent }}%)</span>
                    </label>
                    <span v-if="contract.is_checkin_group" class="text-xs text-success block">Đã giảm: -{{ contract.checkin_group_amount | formatNumber }} đ</span>
                  </div>

                  <div class="vx-col md:w-1/2 w-full mb-3">
                    <label class="flex items-center cursor-pointer">
                      <input type="checkbox" v-model="contract.is_resident" @change="caculatorSession">
                      <span class="ml-2 font-medium">Khách hàng nội khu (-5% toàn khóa)</span>
                    </label>
                    <span v-if="contract.is_resident" class="text-xs text-success block">Đã giảm nội khu: -{{ contract.resident_amount | formatNumber }} đ</span>
                  </div>
                </div>
              </vx-card>
            </div>

            <!-- SÁCH & HỌC LIỆU MUA KÈM -->
            <div class="vx-col w-full mb-4" v-if="contract.product_id">
              <vx-card no-shadow style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px;">
                <h5 class="mb-3 font-semibold text-primary">Sách & Học liệu mua kèm (Phiếu thu gộp chung)</h5>
                <div class="vx-row mb-2">
                  <div class="vx-col md:w-8/12 w-full">
                    <vue-select
                      label="name"
                      placeholder="Chọn bộ sách / học liệu cần mua kèm"
                      :options="html.materials.list"
                      v-model="html.materials.item"
                      :searchable="true"
                      language="tv-VN"
                    ></vue-select>
                  </div>
                  <div class="vx-col md:w-4/12 w-full">
                    <vs-button color="primary" type="border" @click="addMaterial">Thêm sách</vs-button>
                  </div>
                </div>

                <table class="table table-bordered w-full text-sm mt-3" v-if="contract.materials && contract.materials.length > 0" style="width: 100%; border-collapse: collapse;">
                  <thead>
                    <tr style="background: #e2e8f0;">
                      <th style="padding: 6px; border: 1px solid #cbd5e1;">Tên sách/Học liệu</th>
                      <th style="padding: 6px; border: 1px solid #cbd5e1; text-align: center;">SL</th>
                      <th style="padding: 6px; border: 1px solid #cbd5e1; text-align: right;">Giá gốc</th>
                      <th style="padding: 6px; border: 1px solid #cbd5e1; text-align: center;">Chiết khấu</th>
                      <th style="padding: 6px; border: 1px solid #cbd5e1; text-align: right;">Thành tiền</th>
                      <th style="padding: 6px; border: 1px solid #cbd5e1; text-align: center;">Xóa</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(m, idx) in contract.materials" :key="idx">
                      <td style="padding: 6px; border: 1px solid #cbd5e1;">{{ m.material_name }}</td>
                      <td style="padding: 6px; border: 1px solid #cbd5e1; text-align: center;">{{ m.quantity }}</td>
                      <td style="padding: 6px; border: 1px solid #cbd5e1; text-align: right;">{{ m.original_price | formatNumber }} đ</td>
                      <td style="padding: 6px; border: 1px solid #cbd5e1; text-align: center;">{{ m.discount_percent }}%</td>
                      <td style="padding: 6px; border: 1px solid #cbd5e1; text-align: right; font-weight: bold;">{{ m.final_price | formatNumber }} đ</td>
                      <td style="padding: 6px; border: 1px solid #cbd5e1; text-align: center;">
                        <vs-button color="danger" type="flat" size="small" icon-pack="feather" icon="icon-trash" @click="removeMaterial(idx)"></vs-button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </vx-card>
            </div>

            <vs-divider/>
            <div class="vx-col md:w-1/3 w-full mb-4">
              <label>Học phí thực thu</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                :value="contract.tuition_receivable | formatNumber"
                disabled="true"
                style="font-weight: bold; color: #2563eb;"
              />
            </div>
            <div class="vx-col md:w-1/3 w-full mb-4">
              <label>Tiền sách thực thu</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                :value="contract.book_receivable | formatNumber"
                disabled="true"
                style="font-weight: bold; color: #059669;"
              />
            </div>
            <div class="vx-col md:w-1/3 w-full mb-4">
              <label>Tổng thanh toán (Học phí + Sách)</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                :value="contract.total_amount | formatNumber"
                disabled="true"
                style="font-weight: bold; color: #dc2626; font-size: 15px;"
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
              <label>Ngày dự kiến học <span class="text-danger"> (*)</span></label>
              <datepicker class="w-full"
                v-model="contract.start_date"
                placeholder="Chọn ngày dự kiến học"
                :lang="datepickerOptions.lang"
                @change="selectDate"
                :not-before="temp_min_date"
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
          <router-link class="btn btn-danger" :to="`/lms/contracts`">
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
          },
          b2b_campaign:{
            item: '',
            list: []
          },
          materials:{
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
          number_of_months: 0,
          tuition_receivable: 0,
          book_receivable: 0,
          scholarship_percent: 0,
          scholarship_amount: 0,
          is_promo_extra: false,
          promo_extra_percent: 0,
          promo_extra_amount: 0,
          is_checkin_group: false,
          checkin_group_percent: 0,
          checkin_group_amount: 0,
          is_resident: false,
          resident_percent: 0,
          resident_amount: 0,
          gift_note: '',
          materials: [],
          discount_code_id:'',
          discount_code:'',
          discount_code_amount:'',
          discount_code_percent:'',
          discount_code_session:'',
          coupon_code_check:0,
          coupon_code:'',
          coupon_amount: '',
          coupon_session: '',
          coupon_percent: '',
          total_amount:'',
          total_session:'',
          start_date:'',
          note:'',
          b2b_campaign_id:'',
          b2b_amount:'',
          b2b_bonus_session:'',
          sibling_discount:'',
          sibling: false,
        },
        student_info:{

        },
        alert:{
          active: false,
          body: '',
          color:'',
        },
        disabled_trial : 0,
        temp_min_date:'',
        temp_last_start_date:'',
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
            axios.p(`/api/lms/students/search-contract`,{
              branch_id: this.contract.branch_id,
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
        if (!student || !student.student_id) return;
        this.disabled_trial = student.disabled_trial;
        this.student_info = student
        this.contract.student_id = student.student_id
        this.discount_code_id=''
        this.discount_code=''
        this.discount_code_amount=''
        this.discount_code_percent=''
        this.discount_code_session=''
        this.coupon_code_check=0
        this.coupon_code=''
        this.coupon_amount= ''
        this.coupon_session= ''
        this.coupon_percent= ''
        this.total_amount=''
        this.total_session=''
        this.start_date=''
        this.note=''
        this.b2b_campaign_id=''
        this.b2b_amount=''
        this.b2b_bonus_session=''
        this.sibling_discount=''
        this.sibling= false
        this.contract.type =''
        this.temp_min_date = new Date(student.last_start_date)
        this.temp_last_start_date = student.last_start_date
      },
      saveBranch(data = null){
        if (data && typeof data === 'object') {
          const branch_id = data.id
          this.contract.branch_id = branch_id
        }else{
          this.contract.branch_id = ""
        }
        this.student_info = {}
        this.contract.student_id=""
      },
      saveProduct(data = null){
        if (data && typeof data === 'object') {
          const product_id = data.id
          this.contract.product_id = product_id
          this.loadTuitionFee();
          this.loadMaterialsForProduct();
        }else{
          this.contract.product_id = ""
          this.html.tuition_fee.list =[]
          this.html.materials.list = []
        }
        this.resetTuitionFee()
        this.html.tuition_fee.item =''
      },
      resetTuitionFee(){
        this.contract.tuition_fee_id = ''
        this.contract.tuition_fee_amount = ''
        this.contract.tuition_fee_receivable = ''
        this.contract.session = ''
        this.contract.number_of_months = 0
        this.contract.scholarship_percent = 0
        this.contract.scholarship_amount = 0
        this.contract.promo_extra_amount = 0
        this.contract.checkin_group_amount = 0
        this.contract.resident_amount = 0
        this.contract.gift_note = ''
        this.contract.materials = []
      },
      saveTuitionFee(data = null){
        if (data && typeof data === 'object') {
          const tuition_fee_id = data.id
          this.contract.tuition_fee_id = tuition_fee_id
          this.contract.tuition_fee_amount = data.price
          this.contract.tuition_fee_receivable = data.receivable
          this.contract.tuition_fee_session = data.session
          this.contract.number_of_months = data.number_of_months || 0
          this.applyStemkeyPolicy(data.number_of_months, this.contract.product_id)
          this.loadMaterialsForProduct()
          this.loadDiscountCode();
          this.caculatorSession();
          this.loadB2BCampaign();
        }else{
          this.contract.tuition_fee_id = ""
          this.contract.number_of_months = 0
          this.contract.scholarship_percent = 0
          this.contract.gift_note = ''
        }
      },
      applyStemkeyPolicy(months, productId) {
        let scholarship = 0;
        let checkin = 0;
        let gift = '';
        months = Number(months);
        productId = Number(productId);

        if (productId === 1 || productId === 2) {
          if (months === 3) {
            scholarship = 0; checkin = 5; gift = '';
          } else if (months === 6) {
            scholarship = 10; checkin = 5; gift = '2 buổi/tuần/3 tháng STEMKey homework';
          } else if (months === 12) {
            scholarship = 20; checkin = 5; gift = '2 buổi/tuần/6 tháng STEMKey homework';
          } else if (months === 24) {
            scholarship = 30; checkin = 10; gift = '2 buổi/tuần/12 tháng STEMKey homework';
          }
        } else if (productId === 4) {
          if (months === 1) {
            scholarship = 0; checkin = 0; gift = '';
          } else if (months === 6) {
            scholarship = 10; checkin = 3; gift = '';
          } else if (months === 9) {
            scholarship = 10; checkin = 5; gift = '';
          }
        } else if (productId === 3) {
          if (months === 4) {
            scholarship = 5; checkin = 5; gift = '2 buổi/tuần/3 tháng kèm bài tập';
          } else if (months === 8) {
            scholarship = 10; checkin = 5; gift = '2 buổi/tuần/6 tháng kèm bài tập';
          } else if (months === 12) {
            scholarship = 20; checkin = 5; gift = '2 buổi/tuần/10 tháng kèm bài tập';
          } else if (months === 24) {
            scholarship = 30; checkin = 5; gift = '2 buổi/tuần/12 tháng kèm bài tập';
          }
        }

        this.contract.scholarship_percent = scholarship;
        this.contract.checkin_group_percent = checkin;
        this.contract.gift_note = gift;
      },
      loadMaterialsForProduct() {
        if (!this.contract.product_id) {
          this.html.materials.list = [];
          return;
        }
        axios.p('/api/lms/contracts/load-materials', {
          product_id: this.contract.product_id,
          package_months: this.contract.number_of_months || null
        }).then(res => {
          this.html.materials.list = res.data;
        }).catch(err => console.log(err));
      },
      addMaterial() {
        const item = this.html.materials.item;
        if (!item || !item.id) {
          this.$vs.notify({
            title: 'Chưa chọn sách',
            text: 'Vui lòng chọn bộ sách trước khi bấm thêm!',
            color: 'warning'
          });
          return;
        }
        let discountRate = 0;
        const months = Number(this.contract.number_of_months) || Number(item.package_months) || 3;
        if (months >= 24) discountRate = 20;
        else if (months >= 12) discountRate = 15;
        else if (months >= 6) discountRate = 10;
        else if (months >= 3) {
          discountRate = (this.contract.product_id == 4) ? 10 : 5;
        }

        const origPrice = Number(item.unit_price) || 0;
        const finalPrice = Math.round(origPrice * (1 - discountRate / 100));

        this.contract.materials.push({
          material_id: item.id,
          material_name: item.name,
          quantity: 1,
          package_months: months,
          original_price: origPrice,
          discount_percent: discountRate,
          final_price: finalPrice
        });

        this.html.materials.item = '';
        this.caculatorSession();
      },
      removeMaterial(index) {
        this.contract.materials.splice(index, 1);
        this.caculatorSession();
      },
      loadTuitionFee(){
        if (this.contract.type == 0){
          this.temp_min_date = new Date()
        } else {
          this.temp_min_date = new Date(this.temp_last_start_date)
        }
        if(this.contract.product_id){
          this.$vs.loading();
          axios.p(`/api/lms/contracts/load-tuition-fee`,{
            branch_id: this.contract.branch_id,
            product_id: this.contract.product_id,
            type_contract: this.contract.type
          }).then((response) => {
            this.$vs.loading.close();
            this.html.tuition_fee.list = response.data
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
        }).catch(e => console.log(e))
      },
      saveDiscountCode(data = null){
        if (data && typeof data === 'object') {
          const discount_code_id = data.id
          this.contract.discount_code_id = discount_code_id
          this.contract.discount_code = data.code
          this.contract.discount_code_percent = data.percent
          this.contract.discount_code_amount = data.discount
          this.contract.discount_code_session = data.bonus_sessions
        }else{
          this.contract.discount_code_id = ""
          this.contract.discount_code_percent = ""
          this.contract.discount_code = ""
          this.contract.discount_code_amount = ""
          this.contract.discount_code_session = ""
        }
        this.caculatorSession()
      },
      saveDiscountB2B(data = null){
        console.log(data);
        if (data && typeof data === 'object') {
          const b2b_campaign_id = data.id
          this.contract.b2b_campaign_id = b2b_campaign_id
          this.contract.b2b_amount = data.amount
          this.contract.b2b_bonus_session = data.bonus_session
        }else{
          this.contract.b2b_campaign_id = ""
          this.contract.b2b_amount = ""
          this.contract.b2b_bonus_session = ""
        }
        this.caculatorSession()
      },
      loadB2BCampaign(){
        this.$vs.loading();
        axios.p(`/api/marketing/b2b/campaigns/load-b2b-campaign`,{
          tuition_fee_id: this.contract.tuition_fee_id,
          student_id: this.contract.student_id
        }).then((response) => {
          this.$vs.loading.close();
          this.html.b2b_campaign.list = response.data
        }).catch(e => console.log(e))
      },
      checkCoupon(){
        if(this.contract.coupon_code){
          this.$vs.loading.close();
          this.contract.coupon_code_check = 0;
          axios.p(`/api/lms/contracts/check-coupon`,{
            coupon_code: this.contract.coupon_code,
            student_id: this.contract.student_id,
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
              this.contract.coupon_amount = 0
              this.contract.coupon_session = 0
              this.contract.coupon_percent = 0,
              this.caculatorSession()
            }else{
              this.contract.coupon_code_check = 1;
              this.contract.coupon_amount = response.data.data.coupon_amount
              this.contract.coupon_session = response.data.data.coupon_session
              this.contract.coupon_percent = response.data.data.coupon_percent
              this.caculatorSession()
            }
          }).catch(e => console.log(e))
        }else{
          this.contract.coupon_amount = 0
          this.contract.coupon_session = 0
          this.contract.coupon_percent = 0,
          this.caculatorSession()
        }
      },
      selectDate(date){
        if (date) {
          this.contract.start_date = moment(date).format("YYYY-MM-DD");
        }
      },
      caculatorSession(){
        const basePrice = Number(this.contract.tuition_fee_amount) || 0;
        
        // 1. Học bổng kỳ hạn
        const scholarshipPercent = Number(this.contract.scholarship_percent) || 0;
        const scholarshipAmount = Math.round(basePrice * scholarshipPercent / 100);
        this.contract.scholarship_amount = scholarshipAmount;
        let currentTuition = basePrice - scholarshipAmount;

        // 2. Ưu đãi tháng 11 (Tặng thêm 10% nếu gói >= 6 tháng)
        let promoExtraAmount = 0;
        if (this.contract.is_promo_extra && Number(this.contract.number_of_months) >= 6) {
          this.contract.promo_extra_percent = 10;
          promoExtraAmount = Math.round(basePrice * 10 / 100);
        } else {
          this.contract.promo_extra_percent = 0;
        }
        this.contract.promo_extra_amount = promoExtraAmount;
        currentTuition = Math.max(0, currentTuition - promoExtraAmount);

        // 3. Đăng ký nhóm 3 / Check-in
        let checkinAmount = 0;
        if (this.contract.is_checkin_group && Number(this.contract.checkin_group_percent) > 0) {
          checkinAmount = Math.round(currentTuition * Number(this.contract.checkin_group_percent) / 100);
        }
        this.contract.checkin_group_amount = checkinAmount;
        currentTuition = Math.max(0, currentTuition - checkinAmount);

        // 4. Cư dân nội khu (5%)
        let residentAmount = 0;
        if (this.contract.is_resident) {
          this.contract.resident_percent = 5;
          residentAmount = Math.round(currentTuition * 5 / 100);
        } else {
          this.contract.resident_percent = 0;
        }
        this.contract.resident_amount = residentAmount;
        currentTuition = Math.max(0, currentTuition - residentAmount);

        // 5. Giảm trừ anh chị em học cùng (5%)
        if (this.contract.sibling) {
          this.contract.sibling_discount = Math.round(currentTuition * 5 / 100);
        } else {
          this.contract.sibling_discount = 0;
        }
        currentTuition = Math.max(0, currentTuition - Number(this.contract.sibling_discount || 0));

        // 6. Voucher / Coupon
        if (this.contract.coupon_percent && Number(this.contract.coupon_percent) > 0) {
          this.contract.coupon_amount = Math.round(currentTuition * Number(this.contract.coupon_percent) / 100);
        }
        currentTuition = Math.max(0, currentTuition - Number(this.contract.coupon_amount || 0));

        // 7. B2B
        currentTuition = Math.max(0, currentTuition - Number(this.contract.b2b_amount || 0));

        // 8. Mã chiết khấu thủ công
        currentTuition = Math.max(0, currentTuition - Number(this.contract.discount_code_amount || 0));

        this.contract.tuition_receivable = currentTuition;

        // 9. Tiền sách & Học liệu mua kèm
        let bookReceivable = 0;
        if (this.contract.materials && this.contract.materials.length > 0) {
          bookReceivable = this.contract.materials.reduce((sum, item) => sum + Number(item.final_price || 0), 0);
        }
        this.contract.book_receivable = bookReceivable;

        // Tổng tiền thanh toán (Học phí + Sách)
        this.contract.total_amount = this.contract.tuition_receivable + this.contract.book_receivable;

        // Tổng số buổi học
        this.contract.total_session = Number(this.contract.tuition_fee_session || 0) + 
          Number(this.contract.discount_code_session || 0) + 
          Number(this.contract.coupon_session || 0) + 
          Number(this.contract.b2b_bonus_session || 0);
      },
      save() {
        let mess = "";
        let resp = true;
        if (this.contract.branch_id == "") {
          mess += " - Trung tâm không được để trống<br/>";
          resp = false;
        }
        if (this.contract.student_id == "") {
          mess += " - Học sinh không được để trống<br/>";
          resp = false;
        }
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
        if (this.contract.start_date == "") {
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
        axios.p("/api/lms/contracts/add",this.contract)
        .then((response) => {
          this.$vs.loading.close();
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          this.$router.push('/lms/contracts')
        })
        .catch((e) => {
          console.log(e);
          this.$vs.loading.close();
        });
      }
    },
  }
</script>