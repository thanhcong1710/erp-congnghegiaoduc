<template>

  <div id="page-users-list">
    <vx-card no-shadow class="mt-5">
      <div class="vx-row">
        <div class="vx-col md:w-1/2 w-full item-first">
          <h5 class="w-full mb-3"><i class="fa-solid fa-user-plus mr-1"></i> Thông tin học sinh mới</h5>
          <div class="vx-row">
            <div class="vx-col w-full mb-4">
              <label>Chọn trung tâm <span class="text-danger">(*)</span></label>
              <vue-select
                    label="name"
                    placeholder="Chọn trung tâm"
                    :options="html.branches.list"
                    v-model="html.branches.item"
                    :searchable="true"
                    language="tv-VN"
                    @input="saveBranch"
                ></vue-select>
            </div>
            
            <div class="vx-col w-full mb-4">
              <label>Họ và tên học viên <span class="text-danger">(*)</span></label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                v-model="student.name"
                placeholder="Nhập họ tên đầy đủ"
              />
            </div>

            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Số điện thoại <span class="text-danger">(*)</span></label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                v-model="student.phone"
                placeholder="Nhập số điện thoại"
                @blur="validatePhone"
              />
            </div>

            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Email</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                v-model="student.email"
                placeholder="Nhập email"
              />
            </div>

            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Giới tính</label>
              <vue-select
                    label="label"
                    placeholder="Chọn giới tính"
                    :options="[{label: 'Nam', value: 'M'}, {label: 'Nữ', value: 'F'}]"
                    v-model="student.gender_obj"
                    :searchable="false"
                    @input="saveGender"
                ></vue-select>
            </div>

            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Ngày sinh</label>
              <datepicker class="w-full"
                v-model="student.birthday"
                placeholder="Chọn ngày sinh"
                :lang="datepickerOptions.lang"
                @change="selectBirthday"
              />
            </div>

            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Link Facebook</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                v-model="student.link_facebook"
                placeholder="Nhập link Facebook"
              />
            </div>

            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Điểm đầu vào TOEIC</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="number"
                v-model="student.point_toeic"
                placeholder="Nhập điểm TOEIC"
              />
            </div>

            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Tỉnh/Thành phố</label>
              <vue-select
                    label="name"
                    placeholder="Chọn Tỉnh/Thành phố"
                    :options="html.province.list"
                    v-model="html.province.item"
                    :searchable="true"
                    @input="getDistrict"
                ></vue-select>
            </div>

            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Xã/Phường</label>
              <vue-select
                    label="name"
                    placeholder="Chọn Xã/Phường"
                    :options="html.district.list"
                    v-model="html.district.item"
                    :searchable="true"
                    @input="saveDistrict"
                ></vue-select>
            </div>

            <div class="vx-col w-full mb-4">
              <label>Địa chỉ</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                v-model="student.address"
                placeholder="Nhập địa chỉ đầy đủ"
              />
            </div>

            <div class="vx-col w-full mb-4">
              <label>Nguồn <span class="text-danger">(*)</span></label>
              <vue-select
                    label="name"
                    placeholder="Chọn nguồn"
                    :options="html.source.list"
                    v-model="html.source.item"
                    :searchable="true"
                    @input="saveSource"
                ></vue-select>
            </div>

            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>EC (Sale) <span class="text-danger">(*)</span></label>
              <vue-select
                    label="label_name"
                    placeholder="Chọn EC"
                    :options="html.ec.list"
                    v-model="html.ec.item"
                    :searchable="true"
                    @input="saveEC"
                ></vue-select>
            </div>

            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>EC Leader</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                v-model="student.ec_leader_name"
                placeholder="Tự động lấy khi chọn EC"
                disabled
              />
            </div>
          </div>
        </div>
        <div class="vx-col md:w-1/2 w-full item-last">
          <h5 class="w-full mb-3"><i class="fa-solid fa-file-contract mr-1"></i> Thông tin đăng ký học</h5>
          <div class="vx-row">
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Hình thức học <span class="text-danger">(*)</span></label>
              <vue-select
                    label="label"
                    placeholder="Chọn hình thức học"
                    :options="[{label: 'Offline', value: 1}, {label: 'Online', value: 2}]"
                    v-model="agreement.study_type_obj"
                    :searchable="false"
                    @input="saveStudyType"
                ></vue-select>
            </div>

            <div class="vx-col md:w-1/2 w-full mb-4">
            </div>

            <div class="vx-col w-full mb-4">
              <label >Gói học phí <span class="text-danger">(*)</span></label>
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
            <div class="vx-col w-full mb-4">
              <label>Chọn lớp để xếp lớp ngay <span class="text-danger">(*)</span></label>
              <vue-select
                    label="label"
                    placeholder="Chọn lớp (chỉ hiện lớp có ngày bắt đầu <= hôm nay)"
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
              <!-- <datepicker class="w-full"
                v-model="agreement.start_date"
                placeholder="Chọn ngày dự kiến học"
                :lang="datepickerOptions.lang"
                @change="selectDate"
              /> -->
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="agreement.start_date"
                disabled="true"
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
          <vs-button class="mb-2" color="success" @click="save">Lưu và nhập học</vs-button>
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
  
  export default {
    components: {
      datepicker,
      "vue-select": select,
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
          ec:{
            item: '',
            list: []
          },
          province: {
            item: '',
            list: []
          },
          district: {
            item: '',
            list: []
          },
          source: {
            item: '',
            list: []
          },
        },
        student:{
          name: '',
          phone: '',
          email: '',
          gender: '',
          gender_obj: null,
          birthday: '',
          birthday_formatted: '',
          link_facebook: '',
          point_toeic: '',
          address: '',
          province_id: '',
          district_id: '',
          source_id: '',
          ec_leader_id: '',
          ec_leader_name: '',
        },
        agreement:{
          branch_id:'',
          ec_id: '',
          study_type: '',
          study_type_obj: null,
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
          class_id:'',
        },
        classInfo: null,
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
      axios.g(`/api/system/provinces`)
        .then(response => {
        this.html.province.list = response.data
      })
      axios.g(`/api/system/sources`)
        .then(response => {
        this.html.source.list = response.data
      })
      this.loadECList();
    },
    methods: {
      loadECList(){
        axios.g(`/api/users/get-data/users-manager`)
          .then(response => {
          this.html.ec.list = response.data
        })
      },
      validatePhone(){
        if(this.student.phone){
          const data = {
            phone: this.student.phone,
          };
          this.$vs.loading()
          axios.p(`/api/crm/parents/validate_phone`,data).then(response => {
            this.$vs.loading.close();
            if(response.data.status==0){
              this.student.phone ="";
              this.$vs.notify({
                title: 'Cảnh báo',
                text: 'Số điện thoại đã tồn tại, vui lòng chọn form đăng ký lại',
                color: 'danger',
                iconPack: 'feather',
                icon: 'icon-alert-triangle',
                time: 5000
              })
              return false;
            }
          })
        }
      },
      getDistrict(data = null){
        if (data && typeof data === 'object') {
          const province_id = data.id
          this.html.province.item = data
          this.student.province_id = province_id
          this.$vs.loading()
          axios.g(`/api/system/provinces/${province_id}/districts`).then(response => {
            this.$vs.loading.close();
            this.html.district.list = response.data
            this.student.district_id = ""
            this.html.district.item = ""
          })
        }else{
          this.html.province.item = ""
          this.student.province_id = ""
          this.html.district.list = []
          this.html.district.item = ""
          this.student.district_id = ""
        }
      },
      saveDistrict(data = null){
        if (data && typeof data === 'object') {
          const district_id = data.id
          this.html.district.item = data
          this.student.district_id = district_id
        }else{
          this.html.district.item = ""
          this.student.district_id = ""
        }
      },
      saveSource(data = null){
        if (data && typeof data === 'object') {
          const source_id = data.id
          this.html.source.item = data
          this.student.source_id = source_id
        }else{
          this.html.source.item = ""
          this.student.source_id = ""
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
        this.resetClass()
      },
      saveStudyType(data = null){
        if (data && typeof data === 'object') {
          this.agreement.study_type = data.value
          this.agreement.type_obj = data.value // Lưu type_obj
          this.loadTuitionFee();
        }else{
          this.agreement.study_type = ""
          this.agreement.type_obj = ""
        }
        this.resetTuitionFee()
        this.html.tuition_fee.item =''
        this.resetClass()
      },
      saveGender(data = null){
        if (data && typeof data === 'object') {
          this.student.gender = data.value
        }else{
          this.student.gender = ""
        }
      },
      saveEC(data = null){
        if (data && typeof data === 'object') {
          this.agreement.ec_id = data.id
          
          // Lấy EC Leader
          axios.p('/api/lms/agreements/get-ec-leader', {
            ec_id: data.id
          }).then(response => {
            if (response.data.status == 1 && response.data.data) {
              this.student.ec_leader_id = response.data.data.id;
              this.student.ec_leader_name = response.data.data.name;
            }
          }).catch(e => {
            console.log(e);
          });
        }else{
          this.agreement.ec_id = ""
          this.student.ec_leader_id = ""
          this.student.ec_leader_name = ""
        }
      },
      resetTuitionFee(){
        this.agreement.tuition_fee_id = ''
        this.agreement.tuition_fee_amount = ''
        this.agreement.tuition_fee_session = ''
        this.agreement.tuition_fee_type = ''
        this.agreement.tuition_fee_relation = []
        this.agreement.session = ''
      },
      resetClass(){
        this.html.classes.list = []
        this.html.classes.item = ''
        this.agreement.class_id = ''
        this.classInfo = null
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
      loadTuitionFee(){
        if(this.agreement.branch_id && this.agreement.study_type !== ''){
          this.$vs.loading();
          axios.p(`/api/lms/agreements/load-tuition-fee`,{
            branch_id: this.agreement.branch_id,
            study_type: this.agreement.study_type,
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
      selectBirthday(date){
        if (date) {
          this.student.birthday_formatted = moment(date).format("YYYY-MM-DD");
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
        if (this.student.name == "") {
          mess += " - Họ tên học sinh không được để trống<br/>";
          resp = false;
        }
        if (this.student.phone == "") {
          mess += " - Số điện thoại không được để trống<br/>";
          resp = false;
        }
        if (this.agreement.ec_id == "") {
          mess += " - EC không được để trống<br/>";
          resp = false;
        }
        if (this.student.source_id == "") {
          mess += " - Nguồn không được để trống<br/>";
          resp = false;
        }
        if (this.agreement.study_type === "") {
          mess += " - Hình thức học không được để trống<br/>";
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

        const data = {
          branch_id: this.agreement.branch_id,
          student_name: this.student.name,
          student_phone: this.student.phone,
          student_email: this.student.email,
          student_gender: this.student.gender,
          student_birthday: this.student.birthday_formatted,
          student_facebook: this.student.link_facebook,
          student_point_toeic: this.student.point_toeic,
          student_address: this.student.address,
          province_id: this.student.province_id,
          district_id: this.student.district_id,
          source_id: this.student.source_id,
          ec_id: this.agreement.ec_id,
          type_obj: this.agreement.type_obj,
          tuition_fee_id: this.agreement.tuition_fee_id,
          tuition_fee_type: this.agreement.tuition_fee_type,
          tuition_fee_amount: this.agreement.tuition_fee_amount,
          start_date: this.agreement.start_date,
          note: this.agreement.note,
          class_id: this.agreement.class_id,
        };

        this.$vs.loading()
        axios.p("/api/lms/agreements/add-with-new-student", data)
        .then((response) => {
          this.$vs.loading.close();
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message + ' - Mã HS: ' + response.data.lms_code,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          this.$router.push('/lms/agreements')
        })
        .catch((e) => {
          console.log(e);
          this.$vs.loading.close();
          this.$vs.notify({
            title: 'Lỗi',
            text: 'Có lỗi xảy ra, vui lòng thử lại',
            color: 'danger',
            iconPack: 'feather',
            icon: 'icon-x'
          })
        });
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
      getAvailabilityClass(enrolled, max) {
        const ratio = enrolled / max
        if (ratio >= 1) return 'text-danger'
        if (ratio >= 0.8) return 'text-warning'
        return 'text-success'
      },
      getStatusClass(status) {
        switch(status) {
          case 'Sắp khai giảng': return 'text-info'
          case 'Đang diễn ra': return 'text-success'
          case 'Đã kết thúc': return 'text-secondary'
          default: return 'text-muted'
        }
      },
      getAvailabilityBadgeClass(enrolled, max) {
        const ratio = enrolled / max
        if (ratio >= 1) return 'bg-red-100 text-red-800'
        if (ratio >= 0.8) return 'bg-yellow-100 text-yellow-800'
        return 'bg-green-100 text-green-800'
      },
      getProgressBarClass(enrolled, max) {
        const ratio = enrolled / max
        if (ratio >= 1) return 'bg-red-500'
        if (ratio >= 0.8) return 'bg-yellow-500'
        return 'bg-green-500'
      },
    },
  }

</script>
