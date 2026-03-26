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
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Điểm đầu vào TOEIC</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                placeholder="Nhập điểm TOEIC"
                v-model="agreement.point_toeic"
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
            <div class="vx-col w-full mb-4">
              <label>Chọn lớp để xếp lớp ngay <span class="text-danger">(*)</span></label>
              <vue-select
                    label="label"
                    placeholder="Chọn lớp (chỉ hiện lớp có ngày bắt đầu >= hôm nay)"
                    :options="html.classes.list"
                    v-model="html.classes.item"
                    :searchable="true"
                    @input="saveClass"
                    :disabled="!agreement.branch_id || !agreement.tuition_fee_id"
                ></vue-select>
            </div>
            
            <!-- Hiển thị thông tin chi tiết lớp học -->
            <div class="vx-col w-full mb-4" v-if="classInfo">
              <div class="border border-gray-300 rounded">
                <div class="bg-gray-100 px-3 py-2 border-b border-gray-300">
                  <h6 class="font-semibold">
                    <i class="fas fa-info-circle mr-2"></i> Thông tin lớp học
                  </h6>
                </div>
                <div class="p-3 pt-0">
                  <div class="grid grid-cols-1 md:grid-cols-2">
                    <div>
                      <span class="text-gray-600">Giáo viên:</span>
                      <span class="ml-2">{{ classInfo.teacher_name || 'Chưa có' }}</span>
                    </div>
                    <div>
                      <span class="text-gray-600">CM:</span>
                      <span class="ml-2">{{ classInfo.cm_name || 'Chưa có' }}</span>
                    </div>
                    <div>
                      <span class="text-gray-600">Lịch học:</span>
                      <span class="ml-2">{{ classInfo.schedule_text || 'Chưa có' }}</span>
                    </div>
                    <div>
                      <span class="text-gray-600">Phòng:</span>
                      <span class="ml-2">{{ classInfo.room_name || 'Chưa có' }}</span>
                    </div>
                    <div>
                      <span class="text-gray-600">Khóa học:</span>
                      <span class="ml-2">{{ classInfo.product_name || 'Chưa có' }}</span>
                    </div>
                    <div>
                      <span class="text-gray-600">Ca học:</span>
                      <span class="ml-2">{{ classInfo.shift_name || 'Chưa có' }}</span>
                    </div>
                    <div>
                      <span class="text-gray-600">Thời gian:</span>
                      <span class="ml-2">{{ formatDate(classInfo.cls_startdate) }} - {{ formatDate(classInfo.cls_enddate) }}</span>
                    </div>
                    <div>
                      <span class="text-gray-600">Sĩ số:</span>
                      <span class="ml-2 font-bold" :class="getSisoClass(classInfo.enrolled_students, classInfo.max_students)">
                        {{ classInfo.enrolled_students }}/{{ classInfo.max_students }}
                      </span>
                      <span class="ml-1 px-2 py-1 rounded text-sm font-medium" :class="getAvailabilityBadgeClass(classInfo.enrolled_students, classInfo.max_students)">
                        {{ classInfo.availability_text }}
                      </span>
                    </div>
                    <div>
                      <span class="text-gray-600">Trạng thái:</span>
                      <span class="ml-2 font-bold" :class="getStatusClass(classInfo.status_text)">
                        {{ classInfo.status_text }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Ngày dự kiến học</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="agreement.start_date"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Đăng ký nhận sách</label>
              <vue-select
                    label="label"
                    placeholder="Chọn trạng thái nhận sách"
                    :options="[{label: 'Có nhận', value: 1}, {label: 'Không nhận', value: 2}, {label: 'Đã nhận', value: 3}]"
                    v-model="agreement.book_receive_obj"
                    :searchable="false"
                    @input="saveBookReceive"
                ></vue-select>
            </div>

            <!-- <div class="vx-col w-full mb-4">
              <label>Địa chỉ nhận sách</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                v-model="agreement.book_receive_address"
                placeholder="Nhập địa chỉ nhận sách"
              />
            </div> -->

            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Đăng ký nhận hợp đồng</label>
              <vue-select
                    label="label"
                    placeholder="Chọn trạng thái nhận hợp đồng"
                    :options="[{label: 'Có nhận', value: 1}, {label: 'Không nhận', value: 2}]"
                    v-model="agreement.contract_receive_obj"
                    :searchable="false"
                    @input="saveContractReceive"
                ></vue-select>
            </div>

            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Đăng ký theo nhóm</label>
              <vue-select
                    label="label"
                    placeholder="Chọn nhóm đăng ký"
                    :options="[{label: 'Không', value: 0}, {label: 'Nhóm 2', value: 2}, {label: 'Nhóm 3', value: 3}, {label: 'Nhóm 4', value: 4}, {label: 'Nhóm 5', value: 5}, {label: 'Nhóm 6', value: 6}]"
                    v-model="agreement.group_type_obj"
                    :searchable="false"
                    @input="saveGroupType"
                ></vue-select>
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
                      <p  class="invoice-total-title" style="font-size: 1.2rem; color: #1e293b;"> Tổng tiền phải đóng: </p>
                      <p  class="invoice-total-amount" style="font-size: 1.5rem; color: #ef4444;"> {{ agreement.total_amount | formatMoney}} </p>
                  </div>
              </div>
            </div>

            <div class="vx-col w-full mb-2">
              <vs-button type="flat" icon-pack="feather" :icon="showPayment ? 'icon-chevron-down' : 'icon-chevron-right'" @click="showPayment = !showPayment" class="p-0 mb-4" style="font-size: 1.1rem; font-weight: 600;">
                <i class="fa-solid fa-money-bill-wave mr-2"></i> THÔNG TIN THU PHÍ (Click để mở rộng)
              </vs-button>
            </div>

            <div v-if="showPayment" class="vx-col w-full">
              <div class="vx-row p-4 border rounded bg-gray-50 mb-6">
                <div class="vx-col md:w-1/3 w-full mb-4">
                  <label>Số tiền thu <span class="text-danger">(*)</span></label>
                  <input
                    class="vs-inputx vs-input--input normal"
                    type="text"
                    v-model="paymentAmountText"
                    placeholder="Nhập số tiền thu"
                  />
                </div>
                <div class="vx-col md:w-1/3 w-full mb-4">
                  <label>Phương thức đóng phí <span class="text-danger">(*)</span></label>
                  <select class="vs-inputx vs-input--input normal" v-model="payment.method">
                    <option value="0">Tiền mặt</option>
                    <option value="1">Chuyển khoản</option>
                    <option value="2">Thẻ tín dụng</option>
                  </select>
                </div>
                <div class="vx-col md:w-1/3 w-full mb-4">
                  <label>Ngày thu phí <span class="text-danger">(*)</span></label>
                  <datepicker class="w-full"
                    v-model="payment.charge_date"
                    placeholder="Chọn ngày thu phí"
                    :lang="datepickerOptions.lang"
                    @change="selectChargeDate"
                  />
                </div>
                <div class="vx-col md:w-1/2 w-full mb-4">
                  <label>Ghi chú thu phí</label>
                  <textarea class="vs-inputx vs-input--input normal" v-model="payment.note" rows="3" placeholder="Ghi chú cho phiếu thu"></textarea>
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
                          class="w-full h-24 object-cover rounded border border-gray-300 cursor-pointer" 
                          @click="viewImagePreview(file.preview)"
                        />
                        <vs-button 
                          size="small" 
                          color="danger" 
                          type="filled" 
                          icon-pack="feather" 
                          icon="icon-trash" 
                          class="absolute top-1 right-1"
                          style="padding: 4px; min-width: 24px;"
                          @click="removeFile(index)"
                        ></vs-button>
                      </div>
                    </div>
                  </div>
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
          classes:{
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
          book_receive: 0,
          book_receive_obj: null,
          book_receive_address: '',
          contract_receive: 0,
          contract_receive_obj: null,
          group_type: 0,
          group_type_obj: null,
          class_id: '',
          point_toeic: '',
        },
        classInfo: null,
        student_info:{

        },
        alert:{
          active: false,
          body: '',
          color:'',
        },
        showPayment: false,
        payment: {
          amount: 0,
          method: 1,
          charge_date: moment().format('YYYY-MM-DD'),
          note: ''
        },
        paymentAmountText: '',
        selectedFiles: []
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
        this.agreement.point_toeic = student.point_toeic
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
          this.loadClassesForEnrolment();
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
      saveBookReceive(data = null){
        if (data && typeof data === 'object') {
          this.agreement.book_receive = data.value
        }else{
          this.agreement.book_receive = 0
        }
      },
      saveContractReceive(data = null){
        if (data && typeof data === 'object') {
          this.agreement.contract_receive = data.value
        }else{
          this.agreement.contract_receive = 0
        }
      },
      saveGroupType(data = null){
        if (data && typeof data === 'object') {
          this.agreement.group_type = data.value
        }else{
          this.agreement.group_type = 0
        }
      },
      saveClass(data = null){
        if (data && typeof data === 'object') {
          this.agreement.class_id = data.id
          this.loadClassInfo(data.id)
        }else{
          this.agreement.class_id = ""
          this.classInfo = null
        }
      },
      loadClassInfo(classId) {
        if (!classId) {
          this.classInfo = null
          return
        }
        
        this.$vs.loading()
        axios.p(`/api/lms/class-info`, {
          class_id: classId
        }).then(response => {
          this.$vs.loading.close()
          if (response.data.status == 1) {
            this.classInfo = response.data.data
            this.agreement.start_date = this.classInfo.cls_startdate
          } else {
            this.classInfo = null
          }
        }).catch(e => {
          console.log(e)
          this.$vs.loading.close()
          this.classInfo = null
        })
      },
      loadClassesForEnrolment(){
        this.resetClass()
        if(this.agreement.branch_id && this.agreement.tuition_fee_id && this.agreement.tuition_fee_type){
          this.$vs.loading();
          axios.p(`/api/lms/agreements/load-classes-for-enrolment`,{
            branch_id: this.agreement.branch_id,
            tuition_fee_id: this.agreement.tuition_fee_id,
            tuition_fee_type: this.agreement.tuition_fee_type,
          }).then((response) => {
            this.$vs.loading.close();
            this.html.classes.list = response.data || []
          }).catch(e => {
            console.log(e)
            this.$vs.loading.close();
          })
        }
      },
      resetClass(){
        this.html.classes.list = []
        this.html.classes.item = ''
        this.agreement.class_id = ''
        this.classInfo = null
      },
      formatDate(date) {
        if (!date) return 'Chưa có'
        return moment(date).format('DD/MM/YYYY')
      },
      getSisoClass(enrolled, max) {
        const ratio = enrolled / max
        if (ratio >= 1) return 'text-danger font-bold'
        if (ratio >= 0.8) return 'text-warning font-bold'
        return 'text-success'
      },
      getAvailabilityBadgeClass(enrolled, max) {
        const ratio = enrolled / max
        if (ratio >= 1) return 'bg-red-100 text-red-800'
        if (ratio >= 0.8) return 'bg-yellow-100 text-yellow-800'
        return 'bg-green-100 text-green-800'
      },
      getStatusClass(status) {
        switch(status) {
          case 'Sắp khai giảng': return 'text-info'
          case 'Đang diễn ra': return 'text-success'
          case 'Đã kết thúc': return 'text-secondary'
          default: return 'text-muted'
        }
      },
      handleFileUpload(event) {
        const files = Array.from(event.target.files);
        files.forEach(file => {
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
      selectChargeDate(date){
        if (date) {
          this.payment.charge_date = moment(date).format("YYYY-MM-DD");
        }
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
        if (this.showPayment) {
          if (this.payment.amount <= 0) {
            mess += " - Số tiền thu không được để trống khi chọn thu phí<br/>";
            resp = false;
          }
          if (this.payment.charge_date == "") {
            mess += " - Ngày thu phí không được để trống khi chọn thu phí<br/>";
            resp = false;
          }
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
          if (response.data.status == 1) {
             const agreementId = response.data.agreement_id;
             if (this.showPayment) {
               this.processPayment(agreementId, response.data.message);
             } else {
                this.$vs.loading.close();
                this.$vs.notify({
                  title: 'Thành Công',
                  text: response.data.message,
                  color: 'success',
                  iconPack: 'feather',
                  icon: 'icon-check'
                })
                this.$router.push('/lms/agreements')
             }
          } else {
            this.$vs.loading.close();
            this.$vs.notify({
              title: 'Lỗi',
              text: response.data.message,
              color: 'danger',
              iconPack: 'feather',
              icon: 'icon-alert-triangle'
            })
          }
        })
        .catch((e) => {
          console.log(e);
          this.$vs.loading.close();
        });
      },
      processPayment(agreementId, agreementMessage) {
        const formData = new FormData();
        formData.append('agreement_id', agreementId);
        formData.append('note', this.payment.note);
        formData.append('charge_date', this.payment.charge_date);
        formData.append('amount', this.payment.amount);
        formData.append('method', this.payment.method);
        formData.append('type', 1); // Agreement payment
        
        this.selectedFiles.forEach((fileObj, index) => {
          formData.append(`attachments[${index}]`, fileObj.file);
        });

        axios.p(`/api/lms/accounting/charges/add`, formData)
        .then((response) => {
          this.$vs.loading.close();
          this.$vs.notify({
            title: 'Thành Công',
            text: agreementMessage + ' và ' + response.data.message.toLowerCase(),
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          this.$router.push('/lms/agreements')
        })
        .catch(e => {
          this.$vs.loading.close();
          this.$vs.notify({
            title: 'Thông báo',
            text: agreementMessage + ' nhưng không thể tạo phiếu thu. Vui lòng tạo phiếu thu thủ công.',
            color: 'warning',
            iconPack: 'feather',
            icon: 'icon-alert-circle'
          })
          this.$router.push('/lms/agreements')
        })
      }
    },
  }
</script>