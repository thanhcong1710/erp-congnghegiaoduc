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
              <label>Tổng (Học phí + Sách)</label>
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

      <!-- KHỐI PHIẾU THU TIỀN -->
      <vs-divider class="my-5" />
      <div class="vx-row">
        <div class="vx-col w-full">
          <div class="flex justify-between items-center mb-3">
            <h5 class="font-bold text-primary"><i class="fa-solid fa-receipt mr-1"></i> Lịch sử Phiếu thu & Đóng phí</h5>
          </div>
          <div class="overflow-x-auto">
            <table class="table table-bordered w-full text-sm" style="width: 100%; border-collapse: collapse;">
              <thead>
                <tr style="background: #f1f5f9; text-align: center;">
                  <th style="padding: 8px; border: 1px solid #cbd5e1;">STT</th>
                  <th style="padding: 8px; border: 1px solid #cbd5e1;">Mã phiếu thu</th>
                  <th style="padding: 8px; border: 1px solid #cbd5e1;">Ngày thu</th>
                  <th style="padding: 8px; border: 1px solid #cbd5e1;">Số tiền thu</th>
                  <th style="padding: 8px; border: 1px solid #cbd5e1;">Hình thức</th>
                  <th style="padding: 8px; border: 1px solid #cbd5e1;">Người tạo (Sale)</th>
                  <th style="padding: 8px; border: 1px solid #cbd5e1;">Trạng thái</th>
                  <th style="padding: 8px; border: 1px solid #cbd5e1;">Người duyệt (Kế toán)</th>
                  <th style="padding: 8px; border: 1px solid #cbd5e1;">Ghi chú</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="!contract_info.receipts || contract_info.receipts.length === 0">
                  <td colspan="9" class="text-center py-4 text-muted">Chưa có phiếu thu nào được tạo cho hợp đồng này.</td>
                </tr>
                <tr v-for="(rc, idx) in contract_info.receipts" :key="idx" style="text-align: center;">
                  <td style="padding: 8px; border: 1px solid #cbd5e1;">{{ idx + 1 }}</td>
                  <td style="padding: 8px; border: 1px solid #cbd5e1; font-weight: bold; color: #2563eb;">{{ rc.code }}</td>
                  <td style="padding: 8px; border: 1px solid #cbd5e1;">{{ rc.charge_date }}</td>
                  <td style="padding: 8px; border: 1px solid #cbd5e1; font-weight: bold; color: #dc2626;">{{ rc.charge_amount | formatNumber }} đ</td>
                  <td style="padding: 8px; border: 1px solid #cbd5e1;">
                    {{ rc.method == 0 ? 'Tiền mặt' : (rc.method == 1 ? 'Chuyển khoản' : 'Thẻ tín dụng') }}
                  </td>
                  <td style="padding: 8px; border: 1px solid #cbd5e1;">{{ rc.creator_name }}</td>
                  <td style="padding: 8px; border: 1px solid #cbd5e1;">
                    <span v-if="rc.status == 0" class="badge" style="background: #fef3c7; color: #b45309; padding: 3px 8px; border-radius: 4px; font-weight: bold;">
                      Chờ Kế toán duyệt
                    </span>
                    <span v-else-if="rc.status == 1" class="badge" style="background: #dcfce7; color: #15803d; padding: 3px 8px; border-radius: 4px; font-weight: bold;">
                      Kế toán đã duyệt
                    </span>
                    <span v-else-if="rc.status == 2" class="badge" style="background: #fee2e2; color: #b91c1c; padding: 3px 8px; border-radius: 4px; font-weight: bold;">
                      Từ chối duyệt
                    </span>
                  </td>
                  <td style="padding: 8px; border: 1px solid #cbd5e1;">
                    <span v-if="rc.approver_name">{{ rc.approver_name }} ({{ rc.approved_at }})</span>
                    <span v-else class="text-muted">-</span>
                  </td>
                  <td style="padding: 8px; border: 1px solid #cbd5e1; text-align: left;">{{ rc.note || '-' }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="vx-row mt-5">
        <div class="vx-col w-full text-right">
          <router-link class="btn btn-danger" :to="`/lms/contracts`">
            <vs-button color="dark" type="border" class="mb-2 mr-3" >Thoát</vs-button>
          </router-link>
          <vs-button class="mb-2 mr-3" color="primary" v-if="contract_info.debt_amount > 0" @click="openReceiptModal">
            <i class="fa-solid fa-receipt mr-1"></i> Lên phiếu thu mới
          </vs-button>
          <router-link class="btn btn-danger" :to="`/lms/contracts/${contract_info.contract_id}/edit`" v-if="contract_info.debt_amount > 0">
            <vs-button class="mb-2" color="success" >Cập nhật hợp đồng</vs-button>
          </router-link>
        </div>
      </div>
    </vx-card>

    <!-- MODAL LÊN PHIẾU THU -->
    <vs-popup title="Lên Phiếu Thu Tiền" :active.sync="receiptModal.active">
      <div class="vx-row">
        <div class="vx-col w-full mb-3" style="background: #f8fafc; padding: 12px; border-radius: 6px; border: 1px solid #e2e8f0;">
          <p class="mb-1"><strong>Học sinh:</strong> {{ contract_info.name }} (Mã: {{ contract_info.lms_code }})</p>
          <p class="mb-1"><strong>Hợp đồng:</strong> {{ contract_info.code }} - {{ contract_info.product_name }}</p>
          <p class="mb-1"><strong>Phải đóng:</strong> {{ contract_info.must_charge | formatNumber }} đ | <strong>Đã thu:</strong> <span class="text-success font-bold">{{ contract_info.total_charged | formatNumber }} đ</span></p>
          <p class="mb-0"><strong>Công nợ hiện tại:</strong> <span class="text-danger font-bold" style="font-size: 15px;">{{ contract_info.debt_amount | formatNumber }} đ</span></p>
        </div>
        <div class="vx-col w-full mb-3">
          <label class="vs-input--label">Số tiền thu <span class="text-danger">(*)</span></label>
          <div class="flex items-center">
            <input
              type="number"
              class="vs-inputx vs-input--input normal flex-1"
              v-model="receiptModal.amount"
              placeholder="Nhập số tiền thu"
              :max="contract_info.debt_amount"
              min="1000"
            />
            <vs-button size="small" class="ml-2" type="border" @click="receiptModal.amount = contract_info.debt_amount">Đóng hết nợ</vs-button>
          </div>
        </div>
        <div class="vx-col md:w-1/2 w-full mb-3">
          <label class="vs-input--label">Phương thức đóng phí</label>
          <select class="vs-inputx vs-input--input normal" v-model="receiptModal.method">
            <option :value="0">Tiền mặt</option>
            <option :value="1">Chuyển khoản</option>
            <option :value="2">Quẹt thẻ tín dụng</option>
          </select>
        </div>
        <div class="vx-col md:w-1/2 w-full mb-3">
          <label class="vs-input--label">Ngày thu phí</label>
          <datepicker
            v-model="receiptModal.charge_date"
            format="YYYY-MM-DD"
            value-type="format"
            style="width: 100%"
            :clearable="false"
          ></datepicker>
        </div>
        <div class="vx-col w-full mb-4">
          <label class="vs-input--label">Ghi chú</label>
          <textarea class="vs-inputx vs-input--input normal" v-model="receiptModal.note" placeholder="Nhập ghi chú thu phí (VD: Đóng học phí lần 2, Đặt cọc...)"></textarea>
        </div>
        <div class="vx-col w-full text-right">
          <vs-button color="dark" type="border" class="mr-2" @click="receiptModal.active = false">Hủy</vs-button>
          <vs-button color="success" @click="submitReceipt"><i class="fa fa-save mr-1"></i> Tạo phiếu thu</vs-button>
        </div>
      </div>
    </vs-popup>
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
        receiptModal: {
          active: false,
          amount: 0,
          method: 1,
          charge_date: moment().format('YYYY-MM-DD'),
          note: '',
        }
      }
    },
    created() {
      this.loadDetail();
    },
    methods: {
      openReceiptModal() {
        this.receiptModal = {
          active: true,
          amount: this.contract_info.debt_amount,
          method: 1,
          charge_date: moment().format('YYYY-MM-DD'),
          note: (this.contract_info.total_charged > 0) ? 'Thu phí đợt tiếp theo' : 'Thu tiền đợt 1',
        };
      },
      submitReceipt() {
        if (!this.receiptModal.amount || Number(this.receiptModal.amount) <= 0) {
          this.$vs.notify({
            title: 'Lỗi',
            text: 'Số tiền thu phải lớn hơn 0',
            color: 'danger',
            iconPack: 'feather',
            icon: 'icon-alert-circle'
          });
          return;
        }
        if (Number(this.receiptModal.amount) > Number(this.contract_info.debt_amount)) {
          this.$vs.notify({
            title: 'Lỗi',
            text: 'Số tiền thu không được vượt quá công nợ hiện tại',
            color: 'danger',
            iconPack: 'feather',
            icon: 'icon-alert-circle'
          });
          return;
        }
        this.$vs.loading();
        axios.p('/api/lms/contracts/add-receipt', {
          contract_id: this.contract_info.id || this.contract_info.contract_id,
          amount: this.receiptModal.amount,
          method: this.receiptModal.method,
          charge_date: this.receiptModal.charge_date,
          note: this.receiptModal.note
        }).then(response => {
          this.$vs.loading.close();
          this.receiptModal.active = false;
          this.$vs.notify({
            title: 'Thành công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          });
          this.loadDetail();
        }).catch(err => {
          this.$vs.loading.close();
          this.$vs.notify({
            title: 'Lỗi',
            text: err.response && err.response.data && err.response.data.message ? err.response.data.message : 'Có lỗi xảy ra khi tạo phiếu thu',
            color: 'danger',
            iconPack: 'feather',
            icon: 'icon-alert-circle'
          });
        });
      },
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