<template>

  <div id="page-users-list">
    <vx-card no-shadow class="mt-5">
      <div class="vx-row">
        <div class="vx-col md:w-1/2 w-full item-first">
          <h5 class="w-full mb-3"><i class="fa-solid fa-user-graduate mr-1"></i> Thông tin học sinh</h5>
          <div class="vx-row">
            <div class="vx-col w-full mb-4">
              <label>Trung tâm <span class="text-danger"> (*)</span></label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract_info.branch_name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Họ tên</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract_info.name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Mã học sinh</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract_info.lms_code"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Phụ huynh</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract_info.gud_name1"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Số điện thoại</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract_info.gud_mobile1"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Email</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract_info.gud_email1"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Địa chỉ</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract_info.address"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Trung tâm</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract_info.branch_name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>EC</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract_info.ec_name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>EC Leader</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract_info.ec_leader_name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Giám đốc trung tâm</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract_info.ceo_branch_name"
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
              <select class="vs-inputx vs-input--input normal" v-model="contract_info.type" disabled>
                <option value="" disabled>Chọn loại hợp đồng</option>
                <option value="0">Học thử</option>
                <option value="1">Chính thức</option>
              </select>
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label >Khóa học</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract_info.product_name"
                disabled="true"
              />
            </div>
            <div class="vx-col w-full mb-4">
              <label >Gói học phí</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract_info.tuition_fee_name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Giá gốc</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                :value="contract_info.tuition_fee_amount | formatNumber"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Số buổi chính thức</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract_info.tuition_fee_session"
                disabled="true"
              />
            </div>
            <!-- TẠM ẨN: MÃ CHIẾT KHẤU
            <div class="vx-col w-full mb-4">
              <label >Mã chiết khấu</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract_info.discount_code_name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Phần trăm chiết khấu(%)</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract_info.discount_code_percent"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Số tiền giảm trừ theo mã chiết khấu</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                :value="contract_info.discount_code_amount | formatNumber"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Số buổi học bổng</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract_info.discount_code_session"
                disabled="true"
              />
            </div>
            -->
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label> <input type="checkbox" v-model="contract_info.sibling"  disabled="true">
                Giảm trừ anh chị em học cùng 5%</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                :value="contract_info.sibling_discount | formatNumber"
                disabled="true"
              />
            </div>
            <!-- TẠM ẨN: MÃ VOUCHER
            <div class="vx-col w-full mb-4">
              <label>Mã voucher</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract_info.coupon_code"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Số tiền voucher</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                :value="contract_info.coupon_amount | formatNumber"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Số buổi học bổng</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract_info.coupon_session"
                disabled="true"
              />
            </div>
            -->
            <!-- TẠM ẨN: CHÍNH SÁCH B2B
            <div class="vx-col w-full mb-4" v-if="contract_info.b2b_campaign_id">
              <label >Chính sách B2B</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                :value="contract_info.b2b_campaign_title"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4"  v-if="contract_info.b2b_campaign_id">
              <label>Số tiền giảm B2B</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                :value="contract_info.b2b_amount | formatNumber"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4"  v-if="contract_info.b2b_campaign_id">
              <label>Số buổi học bổng B2B</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract_info.b2b_bonus_session"
                disabled="true"
              />
            </div>
            -->

            <!-- SÁCH & HỌC LIỆU MUA KÈM -->
            <div class="vx-col w-full mb-3" v-if="contract_info.materials && contract_info.materials.length > 0">
              <vx-card no-shadow style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px;">
                <h5 class="mb-3 font-semibold text-primary">Sách & Học liệu mua kèm</h5>
                <table class="table table-bordered w-full text-sm" style="width: 100%; border-collapse: collapse;">
                  <thead>
                    <tr style="background: #e2e8f0;">
                      <th style="padding: 6px; border: 1px solid #cbd5e1;">Tên sách/Học liệu</th>
                      <th style="padding: 6px; border: 1px solid #cbd5e1; text-align: center;">SL</th>
                      <th style="padding: 6px; border: 1px solid #cbd5e1; text-align: right;">Giá gốc</th>
                      <th style="padding: 6px; border: 1px solid #cbd5e1; text-align: center;">Chiết khấu</th>
                      <th style="padding: 6px; border: 1px solid #cbd5e1; text-align: right;">Thành tiền</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(m, idx) in contract_info.materials" :key="idx">
                      <td style="padding: 6px; border: 1px solid #cbd5e1;">{{ m.material_name }}</td>
                      <td style="padding: 6px; border: 1px solid #cbd5e1; text-align: center;">{{ m.quantity }}</td>
                      <td style="padding: 6px; border: 1px solid #cbd5e1; text-align: right;">{{ m.original_price | formatNumber }} đ</td>
                      <td style="padding: 6px; border: 1px solid #cbd5e1; text-align: center;">{{ m.discount_percent }}%</td>
                      <td style="padding: 6px; border: 1px solid #cbd5e1; text-align: right; font-weight: bold;">{{ m.final_price | formatNumber }} đ</td>
                    </tr>
                  </tbody>
                </table>
              </vx-card>
            </div>
            <div class="vx-col w-full mb-2" v-if="contract_info.gift_note">
              <span class="text-sm"><b>Quà tặng & Quyền lợi kèm theo:</b> <span class="text-warning font-bold">{{ contract_info.gift_note }}</span></span>
            </div>

            <vs-divider/>
            <div class="vx-col md:w-1/3 w-full mb-4" v-if="contract_info.tuition_receivable">
              <label>Học phí thực thu</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                :value="contract_info.tuition_receivable | formatNumber"
                disabled="true"
                style="font-weight: bold; color: #2563eb;"
              />
            </div>
            <div class="vx-col md:w-1/3 w-full mb-4" v-if="contract_info.book_receivable">
              <label>Tiền sách thực thu</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                :value="contract_info.book_receivable | formatNumber"
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
                :value="contract_info.must_charge | formatNumber"
                disabled="true"
                style="font-weight: bold; color: #dc2626;"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Tổng số buổi học</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract_info.total_sessions"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Ngày dự kiến học</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="contract_info.start_date"
                disabled="true"
              />
            </div>
            <div class="vx-col w-full mb-4">
              <label>Ghi chú</label>
              <textarea class="vs-inputx vs-input--input normal" v-model="contract_info.note" disabled="true"></textarea>
            </div>
          </div>
        </div>
      </div>

      <div class="vx-row mt-5">
        <div class="vx-col w-full text-right">
          <router-link class="btn btn-danger" :to="`/lms/contracts`">
            <vs-button color="dark" type="border" class="mb-2 mr-3" >Thoát</vs-button>
          </router-link>
          <router-link class="btn btn-danger" :to="`/lms/contracts/${contract_info.contract_id}/edit`" v-if="contract_info.debt_amount > 0">
            <vs-button class="mb-2" color="success" >Cập nhật hợp đồng</vs-button>
          </router-link>
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
        contract_info:{},
      }
    },
    created() {
      this.loadDetail();
    },
    methods: {
      loadDetail(){
        this.$vs.loading();
        axios.g(`/api/lms/contracts/show/${this.$route.params.id}`)
          .then(response => {
          this.$vs.loading.close();
          this.contract_info = response.data
          this.contract_info.tuition_fee_amount = response.data.init_tuition_fee_amount
          this.contract_info.tuition_fee_session = response.data.init_tuition_fee_session
          this.contract_info.tuition_fee_receivable = response.data.init_tuition_fee_receivable
          this.contract_info.sibling = response.data.sibling_discount > 0 ? true : false;
        })
      },
    },
  }
</script>