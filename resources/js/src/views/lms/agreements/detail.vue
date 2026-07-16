<template>

  <div id="page-users-list">
    <vx-card no-shadow class="mt-5">
      <div class="vx-row">
        <div class="vx-col md:w-1/2 w-full item-first">
          <h5 class="w-full mb-3"><i class="fa-solid fa-user-graduate mr-1"></i> Thông tin học sinh</h5>
          <div class="vx-row">
            
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Họ tên</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="agreement.name"
                disabled="true"
              />
            </div>
            <div class="vx-col w-full mb-4">
              <label>Trung tâm <span class="text-danger"> (*)</span></label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="agreement.branch_name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Mã LMS</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="agreement.lms_code"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Số điện thoại</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="agreement.gud_mobile1"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Email</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="agreement.gud_email1"
                disabled="true"
              />
            </div>
            <div class="vx-col w-full mb-4">
              <label>Địa chỉ</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="agreement.address"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>EC</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="agreement.ec_name"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>EC Leader</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="agreement.ec_leader_name"
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
                    disabled="true"
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
                 disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Đăng ký nhận sách</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                :value="getBookReceiveLabel(agreement.book_receive)"
                disabled="true"
              />
            </div>

            <div class="vx-col w-full mb-4">
              <label>Địa chỉ nhận sách</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                v-model="agreement.book_receive_address"
                disabled="true"
              />
            </div>

            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Đăng ký nhận hợp đồng</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                :value="getContractReceiveLabel(agreement.contract_receive)"
                disabled="true"
              />
            </div>

            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Đăng ký theo nhóm</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                :value="getGroupTypeLabel(agreement.group_type)"
                disabled="true"
              />
            </div>
            <div class="vx-col w-full mb-4">
              <label>Ghi chú</label>
              <textarea class="vs-inputx vs-input--input normal" v-model="agreement.note"  disabled="true"></textarea>
            </div>

            <!-- Hiển thị thông tin lớp học đã xếp -->
            <div class="vx-col w-full mb-4" v-if="agreement.class_id">
              <div class="border border-gray-300 rounded shadow-sm overflow-hidden mt-2">
                <div class="bg-gray-100 px-4 py-3 border-b border-gray-300 flex items-center">
                  <h6 class="font-bold flex items-center text-primary mb-0">
                    <i class="fa-solid fa-school-flag mr-2"></i> THÔNG TIN LỚP HỌC ĐÃ XẾP
                  </h6>
                </div>
                <!-- HIỂN THỊ CHI TIẾT LỚP HỌC -->
                <div class="p-4 bg-white" v-if="classInfo">
                   <div class="vx-row">
                      <div class="vx-col sm:w-1/2 w-full mb-3 text-sm">
                         <span class="text-gray-500 font-medium">Lớp học:</span> <span class="ml-2 font-bold">{{ agreement.class_name }}</span>
                      </div>
                      <div class="vx-col sm:w-1/2 w-full mb-3 text-sm">
                         <span class="text-gray-500 font-medium">Giáo viên:</span> <span class="ml-2 font-medium">{{ classInfo.teacher_name || '---' }}</span>
                      </div>
                      <div class="vx-col sm:w-1/2 w-full mb-3 text-sm">
                         <span class="text-gray-500 font-medium">CM:</span> <span class="ml-2 font-medium">{{ classInfo.cm_name || '---' }}</span>
                      </div>
                      <div class="vx-col sm:w-1/2 w-full mb-3 text-sm">
                         <span class="text-gray-500 font-medium">Thời gian học:</span> <span class="ml-2 font-medium">{{ formatDate(agreement.class_start_date) }} - {{ formatDate(classInfo.cls_enddate) }}</span>
                      </div>
                      <div class="vx-col sm:w-1/2 w-full mb-3 text-sm">
                         <span class="text-gray-500 font-medium">Sĩ số:</span> 
                         <span class="font-bold ml-2">{{ classInfo.enrolled_students || 0 }}/{{ classInfo.max_students || 0 }}</span>
                         <span class="ml-2 py-0.5 px-2 rounded-lg text-xs" :class="classInfo.enrolled_students >= classInfo.max_students ? 'bg-danger text-white' : 'bg-success text-white'">
                            {{ classInfo.availability_text || 'Còn chỗ' }}
                         </span>
                      </div>
                   </div>
                </div>
                <!-- TRƯỜNG HỢP CHƯA LOAD XONG CHI TIẾT -->
                <div class="p-6 bg-white text-center" v-else>
                   <p class="text-gray-500 text-sm mb-0 italic">Đang tải chi tiết lớp học...</p>
                </div>
              </div>
            </div>
            <vs-divider/>
            <div class="vx-col md:w-1/3 w-full mb-4">
            </div>
            <div class="vx-col md:w-2/3 w-full mb-4">
              <div  class="invoice-total-wrapper">
                  <div  class="invoice-total-item">
                      <p  class="invoice-total-title"> Tổng tiền phải đóng: </p>
                      <p  class="invoice-total-amount"> {{ agreement.total_amount | formatMoney}} </p>
                  </div>
                  <div  class="invoice-total-item">
                      <p  class="invoice-total-title"> Số tiền đã đóng: </p>
                      <p  class="invoice-total-amount"> {{ agreement.total_charged | formatMoney}} </p>
                  </div>
                  <div  class="invoice-total-item">
                      <p  class="invoice-total-title"> Công nợ: </p>
                      <p  class="invoice-total-amount"  style="font-weight: bold;"> {{ agreement.debt_amount | formatMoney}} </p>
                  </div>
                  <div  class="invoice-total-item">
                      <p  class="invoice-total-title"> Số tiền còn lại: </p>
                      <p  class="invoice-total-amount" style="font-weight: bold;color: red;"> {{ agreement.total_left_amount | formatMoney}} </p>
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
          <router-link class="btn btn-success" :to="`/lms/agreements/${agreement.id}/edit`">
            <vs-button class="mb-2" color="success">Cập nhật thông tin</vs-button>
          </router-link>
        </div>
      </div>
      <vs-divider/>
      <div class="vx-col w-full item-last">
          <h5 class="w-full mb-3"><i class="fa-solid fa-file-contract mr-1"></i> Thông tin hợp đồng</h5>
          <div class="vx-row">
            <div class="vx-col w-full mb-4 vs-con-table stripe vs-table-primary">
              <div class="con-tablex vs-table--content">
                <div class="vs-con-tbody vs-table--tbody ">
                  <table class="vs-table vs-table--tbody-table">
                    <thead class="vs-table--thead">
                      <tr>
                        <!---->
                        <th colspan="1" rowspan="1"> Mã hợp đồng</th>
                        <th colspan="1" rowspan="1"> Gói phí</th>
                        <th colspan="1" rowspan="1" class="text-center">Giá</th>
                        <th colspan="1" rowspan="1" class="text-center">Đã đóng</th>
                        <th colspan="1" rowspan="1" class="text-center">Còn lại</th>
                        <th colspan="1" rowspan="1" class="text-center">Số buổi</th>
                        <th colspan="1" rowspan="1" class="text-center">Số buổi đã học</th>
                        <th colspan="1" rowspan="1" class="text-center">Trạng thái</th>
                      </tr>
                    </thead>
                    <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in agreement.contracts" :key="index">
                      <!---->
                      <td class="td vs-table--td">{{item.code}}</td>
                      <td class="td vs-table--td">{{item.tuition_fee_name}}</td>
                      <td class="td vs-table--td text-center">{{item.must_charge | formatMoney}}</td>
                      <td class="td vs-table--td text-center">{{item.total_charged | formatMoney}}</td>
                      <td class="td vs-table--td text-center">{{item.left_amount | formatMoney}}</td>
                      <td class="td vs-table--td text-center">{{item.real_sessions }}</td>
                      <td class="td vs-table--td text-center">{{item.done_sessions}}</td>
                      <td class="td vs-table--td text-center">{{item.label_status}}</td>
                    </tr>
                  </table>
                  
                </div>
              </div>
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
          }
        },
        agreement:{
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
          b2b_campaign_id:'',
          b2b_amount:'',
          b2b_bonus_session:'',
          tuition_fee_relation:[],
          tuition_fee_type:'',
          class_id: null,
          class_name: '',
          class_start_date: ''
        },
        classInfo: null,
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
        axios.g(`/api/lms/agreements/show/${this.$route.params.id}`)
          .then(response => {
          this.$vs.loading.close();
          this.agreement = response.data
          this.agreement.total_amount = response.data.must_charge
          this.agreement.total_session = response.data.total_sessions
          this.agreement.tuition_fee_amount = response.data.init_tuition_fee_amount
          this.agreement.tuition_fee_session = response.data.init_tuition_fee_session
          this.tmp_tuition_fee_id = response.data.tuition_fee_id
          this.agreement.contracts = response.data.contracts
          this.loadTuitionFee(response.data.tuition_fee_id);
          if (this.agreement.class_id) {
             this.loadClassInfo(this.agreement.class_id)
          }
        })
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
      loadTuitionFee(tuition_fee_id=0){
        if(this.agreement.branch_id){
          this.$vs.loading();
          axios.p(`/api/lms/agreements/load-tuition-fee`,{
            branch_id: this.agreement.branch_id,
          }).then((response) => {
            this.$vs.loading.close();
            this.html.tuition_fee.list = response.data
            if(tuition_fee_id){
              this.html.tuition_fee.item = this.html.tuition_fee.list.filter(item => item.id == tuition_fee_id)[0]
              this.agreement.tuition_fee_relation = this.html.tuition_fee.item.tuition_fee_relation
              this.agreement.tuition_fee_type = this.html.tuition_fee.item.type_fee
              this.agreement.tuition_fee_amount = this.html.tuition_fee.item.price
            }
          }).catch(e => console.log(e))
        }
      },
      selectDate(date){
        if (date) {
          this.agreement.start_date = moment(date).format("YYYY-MM-DD");
        }
      },
      caculatorSession(){
        console.log(this.agreement);
        this.agreement.total_amount = Number(this.agreement.tuition_fee_amount) - Number(this.agreement.discount_code_amount) - Number(this.agreement.coupon_amount) - Number(this.agreement.b2b_amount) > 0 ? Number(this.agreement.tuition_fee_amount) - Number(this.agreement.discount_code_amount) - Number(this.agreement.coupon_amount) - Number(this.agreement.b2b_amount): 0;
      },
      getBookReceiveLabel(value) {
        const labels = {
          0: 'Chưa điền',
          1: 'Có nhận',
          2: 'Không nhận',
          3: 'Đã nhận'
        };
        return labels[value] || 'Chưa điền';
      },
      getContractReceiveLabel(value) {
        const labels = {
          0: 'Chưa điền',
          1: 'Có nhận',
          2: 'Không nhận'
        };
        return labels[value] || 'Chưa điền';
      },
      getGroupTypeLabel(value) {
        const labels = {
          0: 'Không',
          2: 'Nhóm 2',
          3: 'Nhóm 3',
          4: 'Nhóm 4',
          5: 'Nhóm 5',
          6: 'Nhóm 6'
        };
        return labels[value] || 'Không';
      },
      formatDate(date) {
        return date ? moment(date).format('DD/MM/YYYY') : '---';
      },
      viewClassDetail(class_id) {
        this.$router.push(`/lms/classes/${class_id}/detail`);
      },
      loadClassInfo(classId) {
        axios.p(`/api/lms/class-info`, {
          class_id: classId
        }).then(response => {
           if (response.data.status == 1) {
             this.classInfo = response.data.data
           }
        }).catch(e => console.log(e))
      }
    },
  }
</script>