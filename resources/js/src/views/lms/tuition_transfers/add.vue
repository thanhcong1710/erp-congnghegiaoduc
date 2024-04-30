<template>

  <div id="page-users-list">
    <vx-card no-shadow class="mt-5">
      <div class="vx-row">
        <div class="vx-col md:w-1/2 w-full item-first">
          <h5 class="w-full mb-3"><i class="fa-solid fa-user-graduate mr-1"></i> Học sinh chuyển phí</h5>
          <div class="vx-row">
            <div class="vx-col w-full mb-4">
              <label>Chọn trung tâm học sinh chuyển<span class="text-danger"> (*)</span></label>
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
            <div class="vx-col w-full mb-4" v-if="tuition_transfer.from_branch_id">
              <label> Chọn học sinh chuyển<span class="text-danger"> (*)</span></label>
              <search
                  :endpoint="filter.search.link"
                  :suggestStudents="filter.search.find"
                  :onSelectStudent="filter.search.action">
              </search>
            </div>
            <div class="vx-col w-full mb-4" v-if="tuition_transfer.from_student_id">
              <div class="vx-col w-full mb-4">
                <label>Hồ sơ học sinh</label>
                <div class="vs-component vs-con-table stripe vs-table-primary">
                  <div class="vs-con-tbody vs-table--tbody ">
                    <table class="vs-table vs-table--tbody-table">
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td" width="124px"><span>Họ tên:</span></td> 
                        <td class="td vs-table--td"><span>{{from_student_info.name}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Mã LMS:</span></td> 
                        <td class="td vs-table--td"><strong>{{from_student_info.lms_code}}</strong></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Phụ huynh:</span></td> 
                        <td class="td vs-table--td"><span>{{from_student_info.gud_name1}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Điện thoại:</span></td> 
                        <td class="td vs-table--td"><span>{{from_student_info.gud_mobile1}}</span></td> 
                      </tr>
                       <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Email:</span></td> 
                        <td class="td vs-table--td"><span>{{from_student_info.gud_email1}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Điạ chỉ:</span></td> 
                        <td class="td vs-table--td"><span>{{from_student_info.address}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>EC:</span></td> 
                        <td class="td vs-table--td"><span>{{from_student_info.ec_name}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>CM:</span></td> 
                        <td class="td vs-table--td"><span>{{from_student_info.cm_name}}</span></td> 
                      </tr>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <div class="vx-col w-full mb-4" v-if="tuition_transfer.from_student_id && from_contracts.length > 0 && transferred_contracts.length == 0">
              <label>Danh sách gói phí của học sinh chuyển</label>
              <div class="vs-component vs-con-table stripe vs-table-primary mb-5" v-for="(item, index) in from_contracts" :key="index">
                <div class="vs-con-tbody vs-table--tbody ">
                  <table class="vs-table vs-table--tbody-table" >
                    <thead class="vs-table--thead">
                      <tr>
                        <!---->
                        <th colspan="2" rowspan="1" class="bg-primary" style="color:#fff">
                          <div class="vs-table-text">{{index+1}}. Hợp đồng số {{item.code}} (gói phí {{item.tuition_fee_name}})</div>
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td" width="160px"><span>Số phí đã đóng:</span></td> 
                        <td class="td vs-table--td"><span>{{item.total_charged | formatMoney}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Số buổi chính thức:</span></td> 
                        <td class="td vs-table--td"><span>{{item.real_sessions}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Số buổi học bổng:</span></td> 
                        <td class="td vs-table--td"><span>{{item.bonus_sessions}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Số buổi đã học:</span></td> 
                        <td class="td vs-table--td"><strong>{{item.done_sessions}}</strong></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Số buổi còn:</span></td> 
                        <td class="td vs-table--td"><strong>{{item.left_sessions}}</strong></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Khóa học:</span></td> 
                        <td class="td vs-table--td"><span>{{item.product_name}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null" v-if="item.class_id">
                        <td class="td vs-table--td"><span>Lớp học:</span></td> 
                        <td class="td vs-table--td"><span>{{item.class_name}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null" v-if="item.class_id">
                        <td class="td vs-table--td"><span>Thời gian:</span></td> 
                        <td class="td vs-table--td"><span>{{item.enrolment_start_date | formatDateView}} - {{item.enrolment_last_date | formatDateView}}</span></td> 
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="vx-col w-full mb-4" v-if="tuition_transfer.from_student_id && transferred_contracts.length > 0">
              <label>Danh sách gói phí chuyển (tính đến thời điểm chuyển)</label>
              <div class="vs-component vs-con-table stripe vs-table-primary mb-5" v-for="(item, index) in transferred_contracts" :key="index">
                <div class="vs-con-tbody vs-table--tbody ">
                  <table class="vs-table vs-table--tbody-table" >
                    <thead class="vs-table--thead">
                      <tr>
                        <!---->
                        <th colspan="2" rowspan="1"  class="bg-success" style="color:#fff">
                          <div class="vs-table-text">{{index+1}}. Hợp đồng số {{item.code}} (gói phí {{item.tuition_fee_name}})</div>
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td" width="160px"><span>Số phí đã đóng:</span></td> 
                        <td class="td vs-table--td"><span>{{item.total_charged | formatMoney}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Số buổi chính thức:</span></td> 
                        <td class="td vs-table--td"><span>{{item.real_sessions}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Số buổi học bổng:</span></td> 
                        <td class="td vs-table--td"><span>{{item.bonus_sessions}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Số buổi đã học:</span></td> 
                        <td class="td vs-table--td"><strong>{{item.done_sessions}}</strong></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Số buổi còn lại:</span></td> 
                        <td class="td vs-table--td"><strong>{{item.left_sessions}}</strong> (<span>{{item.left_real_sessions}}</span> buổi chính thức)</td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td" width="160px"><span>Số phí còn lại:</span></td> 
                        <td class="td vs-table--td"><span>{{item.left_amount | formatMoney}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Khóa học:</span></td> 
                        <td class="td vs-table--td"><span>{{item.product_name}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null" v-if="item.class_id">
                        <td class="td vs-table--td"><span>Lớp học:</span></td> 
                        <td class="td vs-table--td"><span>{{item.class_name}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null" v-if="item.class_id">
                        <td class="td vs-table--td"><span>Thời gian:</span></td> 
                        <td class="td vs-table--td"><span>{{item.enrolment_start_date | formatDateView}} - {{item.enrolment_last_date | formatDateView}}</span></td> 
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="vx-col w-full mb-4" v-if="tuition_transfer.from_student_id && transferred_contracts.length > 0">
              <label>Tổng số tiền còn lại</label>
              <input class="vs-inputx vs-input--input normal" :value="tuition_transfer.transferred_amount | formatMoney"  disabled="true" />
            </div>
          </div>
        </div>
        <div class="vx-col md:w-1/2 w-full item-last">
          <h5 class="w-full mb-3"><i class="fa-solid fa-user-graduate mr-1"></i> Học sinh nhận phí</h5>
          <div class="vx-row">
            <div class="vx-col w-full mb-4">
              <label>Chọn trung tâm học sinh nhận <span class="text-danger"> (*)</span></label>
              <vue-select
                    label="name"
                    placeholder="Chọn trung tâm nhận"
                    :options="html.to_branchs.list"
                    v-model="html.to_branchs.item"
                    :searchable="true"
                    language="tv-VN"
                    @input="saveToBranch"
                    :disabled="input_disabled"
                ></vue-select>
            </div>
            <div class="vx-col w-full mb-4" v-if="tuition_transfer.to_branch_id">
              <label> Chọn học sinh nhận<span class="text-danger"> (*)</span></label>
              <search
                  :endpoint="filter.search_to.link"
                  :suggestStudents="filter.search_to.find"
                  :onSelectStudent="filter.search_to.action">
              </search>
            </div>
            <div class="vx-col w-full mb-4" v-if="tuition_transfer.to_student_id">
              <div class="vx-col w-full mb-4">
                <label>Hồ sơ học sinh</label>
                <div class="vs-component vs-con-table stripe vs-table-primary">
                  <div class="vs-con-tbody vs-table--tbody ">
                    <table class="vs-table vs-table--tbody-table">
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td" width="124px"><span>Họ tên:</span></td> 
                        <td class="td vs-table--td"><span>{{to_student_info.name}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Mã LMS:</span></td> 
                        <td class="td vs-table--td"><strong>{{to_student_info.lms_code}}</strong></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Phụ huynh:</span></td> 
                        <td class="td vs-table--td"><span>{{to_student_info.gud_name1}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Điện thoại:</span></td> 
                        <td class="td vs-table--td"><span>{{to_student_info.gud_mobile1}}</span></td> 
                      </tr>
                       <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Email:</span></td> 
                        <td class="td vs-table--td"><span>{{to_student_info.gud_email1}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Điạ chỉ:</span></td> 
                        <td class="td vs-table--td"><span>{{to_student_info.address}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>EC:</span></td> 
                        <td class="td vs-table--td"><span>{{to_student_info.ec_name}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>CM:</span></td> 
                        <td class="td vs-table--td"><span>{{to_student_info.cm_name}}</span></td> 
                      </tr>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <div class="vx-col w-full mb-4" v-if="tuition_transfer.to_student_id && to_contracts.length > 0  && received_contracts.length == 0">
              <label>Danh sách gói phí của học sinh nhận</label>
              <div class="vs-component vs-con-table stripe vs-table-primary mb-5" v-for="(item, index) in to_contracts" :key="index">
                <div class="vs-con-tbody vs-table--tbody ">
                  <table class="vs-table vs-table--tbody-table" >
                    <thead class="vs-table--thead">
                      <tr>
                        <!---->
                        <th colspan="2" rowspan="1" class="bg-primary" style="color:#fff">
                          <div class="vs-table-text">{{index+1}}. Hợp đồng số {{item.code}} (gói phí {{item.tuition_fee_name}})</div>
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td" width="160px"><span>Số phí đã đóng:</span></td> 
                        <td class="td vs-table--td"><span>{{item.total_charged | formatMoney}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Số buổi chính thức:</span></td> 
                        <td class="td vs-table--td"><span>{{item.real_sessions}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Số buổi học bổng:</span></td> 
                        <td class="td vs-table--td"><span>{{item.bonus_sessions}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Số buổi đã học:</span></td> 
                        <td class="td vs-table--td"><strong>{{item.done_sessions}}</strong></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Số buổi còn lại:</span></td> 
                        <td class="td vs-table--td"><strong>{{item.left_sessions}}</strong></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Khóa học:</span></td> 
                        <td class="td vs-table--td"><span>{{item.product_name}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null" v-if="item.class_id">
                        <td class="td vs-table--td"><span>Lớp học:</span></td> 
                        <td class="td vs-table--td"><span>{{item.class_name}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null" v-if="item.class_id">
                        <td class="td vs-table--td"><span>Thời gian:</span></td> 
                        <td class="td vs-table--td"><span>{{item.enrolment_start_date | formatDateView}} - {{item.enrolment_last_date | formatDateView}}</span></td> 
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="vx-col w-full mb-4" v-if="tuition_transfer.to_student_id && received_contracts.length > 0">
              <label>Danh sách gói phí nhận</label>
              <div class="vs-component vs-con-table stripe vs-table-primary mb-5" v-for="(item, index) in received_contracts" :key="index">
                <div class="vs-con-tbody vs-table--tbody ">
                  <table class="vs-table vs-table--tbody-table" >
                    <thead class="vs-table--thead">
                      <tr>
                        <!---->
                        <th colspan="2" rowspan="1"  class="bg-success" style="color:#fff">
                          <div class="vs-table-text">{{index+1}}.Gói phí {{item.tuition_fee_name}}</div>
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td" width="160px"><span>Số phí đã đóng:</span></td> 
                        <td class="td vs-table--td"><span>{{item.total_charged | formatMoney}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Số buổi chính thức:</span></td> 
                        <td class="td vs-table--td"><span>{{item.real_sessions}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Số buổi học bổng:</span></td> 
                        <td class="td vs-table--td"><span>{{item.bonus_sessions}}</span></td> 
                      </tr>
                      <tr class="tr-values vs-table--tr tr-table-state-null">
                        <td class="td vs-table--td"><span>Khóa học:</span></td> 
                        <td class="td vs-table--td"><span>{{item.product_name}}</span></td> 
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="vx-col w-full mb-4" v-if="tuition_transfer.to_student_id && received_contracts.length > 0">
              <label>Tổng số tiền nhận</label>
              <input class="vs-inputx vs-input--input normal" :value="tuition_transfer.received_amount | formatMoney"  disabled="true" />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4" v-if="tuition_transfer.to_student_id">
              <label>Khóa học quy đổi <span class="text-danger"> (*)</span></label>
              <select class="vs-inputx vs-input--input normal" v-model="tuition_transfer.to_product_id" @change="prepareTransferData()" :disabled="input_disabled">
                <option value="" disabled>Chọn khóa học</option>
                <option :value="item.id" v-for="(item, index) in html.products.list" :key="index">{{item.name}}</option>
              </select>
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4" v-if="tuition_transfer.to_student_id">
              <label>Ngày bắt đầu chuyển <span class="text-danger"> (*)</span></label>
              <datepicker class="w-full"
                v-model="tuition_transfer.transfer_date"
                placeholder="Chọn ngày bắt đầu chuyển"
                :lang="datepickerOptions.lang"
                @change="selectDate"
                :disabled="input_disabled"
                :not-before="temp.min_date"
              />
            </div>
            <div class="vx-col w-full mb-4" v-if="tuition_transfer.to_student_id">
              <label>Ghi chú</label>
              <textarea class="vs-inputx vs-input--input normal" v-model="tuition_transfer.note"></textarea>
            </div>
          </div>

          <vs-alert :active.sync="alert.active" class="mb-5" :color="alert.color" closable icon-pack="feather" close-icon="icon-x">
            <div v-html="alert.body"></div>
          </vs-alert>
          <div class="vx-col w-full text-right">
            <router-link class="btn btn-danger" :to="`/lms/tuition_transfers`">
              <vs-button color="dark" type="border" class="mb-2 mr-3" >Hủy</vs-button>
            </router-link>
            <vs-button class="mb-2" color="success" @click="save">Thêm mới</vs-button>
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
              action: student => this.selectStudent(student),
          },
          search_to: {
              link: 0,
              display: 'hidden',
              find: keyword => this.searchSuggestToStudent(keyword),
              action: student => this.selectToStudent(student),
          }
        },
        html:{
          branches: {
            item: '',
            list: []
          },
          to_branchs: {
            item: '',
            list: []
          },
          products: {
            item: '',
            list: []
          },
        },
        tuition_transfer:{
          from_branch_id:'',
          from_student_id: '',
          to_branch_id:'',
          to_student_id:'',
          to_product_id:'',
          note:'',
          transfer_date:'',
          received_amount:'',
          transferred_amount:'',
        },
        from_student_info:{},
        to_student_info:{},
        from_contracts:[],
        to_contracts:[],
        transferred_contracts:[],
        received_contracts:[],
        alert:{
          active: false,
          body: '',
          color:'',
        },
        input_disabled: true,
        temp:{
          min_date: new Date()
        }
      }
    },
    created() {
      axios.g(`/api/system/branches-has-user`)
        .then(response => {
        this.html.branches.list = response.data
        this.html.to_branchs.list = response.data
      })
      axios.g(`/api/system/products`)
        .then(response => {
        this.html.products.list = response.data
      })
    },
    methods: {
      searchSuggestStudent(keyword) {
        this.resetFromStudent();
        if (keyword && keyword.length >= 3 && this.calling === false) {
          this.calling = true
          return new Promise((resolve, reject) => {
            axios.p(`/api/lms/tuition_transfers/search-student`,{
              branch_id: this.tuition_transfer.from_branch_id,
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
        this.from_student_info = student
        this.tuition_transfer.from_student_id = student.student_id
        this.input_disabled = false
        this.getContractsActive(student.student_id)
      },
      getContractsActive(student_id){
        this.$vs.loading()
        axios.p("/api/lms/tuition_transfers/get-data-contract",{
          student_id: student_id
        })
        .then((response) => {
          this.from_contracts = response.data
          this.$vs.loading.close();
        })
        .catch((e) => {
          console.log(e);
          this.$vs.loading.close();
        });
      },
      searchSuggestToStudent(keyword) {
        this.resetToStudent();
        if (keyword && keyword.length >= 3 && this.calling === false) {
          this.calling = true
          return new Promise((resolve, reject) => {
            axios.p(`/api/lms/tuition_transfers/search-student`,{
              branch_id: this.tuition_transfer.to_branch_id,
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
      selectToStudent(student) {
        this.to_student_info = student
        this.tuition_transfer.to_student_id = student.student_id
        this.input_disabled = false
        this.getToContractsActive(student.student_id)
      },
      getToContractsActive(student_id){
        this.$vs.loading()
        axios.p("/api/lms/tuition_transfers/get-data-contract",{
          student_id: student_id
        })
        .then((response) => {
          this.to_contracts = response.data
          if(this.to_contracts[0].product_id){
            this.tuition_transfer.to_product_id = this.to_contracts[0].product_id
          }
          this.$vs.loading.close();
        })
        .catch((e) => {
          console.log(e);
          this.$vs.loading.close();
        });
      },
      saveBranch(data = null){
        if (data && typeof data === 'object') {
          const branch_id = data.id
          this.tuition_transfer.from_branch_id = branch_id
        }else{
          this.tuition_transfer.from_branch_id = ""
        }
        this.from_student_info = {}
        this.tuition_transfer.from_student_id=""
      },
      saveToBranch(data = null){
        if (data && typeof data === 'object') {
          const to_branch_id = data.id
          this.tuition_transfer.to_branch_id = to_branch_id
        }else{
          this.tuition_transfer.to_branch_id = ""
        }
      },
      selectDate(date){
        if (date) {
          this.tuition_transfer.transfer_date = moment(date).format("YYYY-MM-DD");
          this.prepareTransferData()
        }
      },
      resetFromStudent(){
        this.from_student_info = {}
        this.tuition_transfer.from_student_id = ''
        this.from_contracts =[]
        this.resetToStudent();
        this.resetDataPrepareTransfer();
      },
      resetToStudent(){
        this.to_student_info = {}
        this.tuition_transfer.to_student_id = ''
        this.to_contracts =[]
        this.tuition_transfer.to_product_id = ''
        this.input_disabled = true
        this.resetDataPrepareTransfer();
      },
      resetDataPrepareTransfer(){
        this.received_contracts = [];
        this.transferred_contracts = [];
        this.tuition_transfer.received_amount = '';
        this.tuition_transfer.transferred_amount = '';
      },
      prepareTransferData(){
        if(this.tuition_transfer.transfer_date && this.tuition_transfer.to_product_id){
          this.$vs.loading()
          axios.p("/api/lms/tuition_transfers/prepare-transfer-data",{
            from_contracts: this.from_contracts,
            transfer_date: this.tuition_transfer.transfer_date,
            to_product_id: this.tuition_transfer.to_product_id,
            to_branch_id: this.tuition_transfer.to_branch_id
          })
          .then((response) => {  
            this.$vs.loading.close();
            if(response.data.status==1){
              this.received_contracts = response.data.received_contracts
              this.transferred_contracts = response.data.transferred_contracts
              this.tuition_transfer.received_amount = response.data.total_amount_transfer
              this.tuition_transfer.transferred_amount =  response.data.total_amount_transfer
            }else{
              this.$vs.notify({
                title: 'Lỗi',
                text: response.data.message,
                iconPack: 'feather',
                icon: 'icon-alert-circle',
                color: 'warning'
              })
            }
          })
          .catch((e) => {
            console.log(e);
            this.$vs.loading.close();
          });
        }
      },
      
      save() {
        let mess = "";
        let resp = true;
        if (this.tuition_transfer.from_student_id == "") {
          mess += " - Học sinh chuyển không được để trống<br/>";
          resp = false;
        }
        if (this.tuition_transfer.to_branch_id == "") {
          mess += " - Trung tâm học sinh nhận không được để trống<br/>";
          resp = false;
        }
        if (this.tuition_transfer.to_student_id == "") {
          mess += " - Học sinh nhận không được để trống<br/>";
          resp = false;
        }
        if (this.tuition_transfer.to_student_id!="" && this.tuition_transfer.to_student_id == this.tuition_transfer.from_student_id) {
          mess += " - Học sinh nhận phải khác học sinh chuyển<br/>";
          resp = false;
        }
        if (this.tuition_transfer.to_product_id == "") {
          mess += " - Khóa học quy đổi không được để trống<br/>";
          resp = false;
        }
        if (this.tuition_transfer.transfer_date == "") {
          mess += " - Ngày bắt đầu chuyển phí không được để trống<br/>";
          resp = false;
        }
        if (this.tuition_transfer.transferred_amount <= 0) {
          mess += " - Tổng số tiền nhận phải lớn hơn 0<br/>";
          resp = false;
        }
        if (!resp) {
          this.alert.color = 'danger'
          this.alert.body = mess;
          this.alert.active = true;
          return false;
        }
        this.$vs.loading()
        axios.p("/api/lms/tuition_transfers/add",{
          tuition_transfer: this.tuition_transfer,
          from_student_info: this.from_student_info,
          from_contracts : this.from_contracts,
          to_student_info: this.to_student_info,
          to_contracts : this.to_contracts,
          transferred_contracts: this.transferred_contracts,
          received_contracts: this.received_contracts
        })
        .then((response) => {
          this.$vs.loading.close();
          if(response.data.status ==1){
            this.$vs.notify({
              title: 'Thành Công',
              text: response.data.message,
              color: 'success',
              iconPack: 'feather',
              icon: 'icon-check'
            })
            this.$router.push('/lms/tuition_transfers')
          }else{
            this.$vs.notify({
              title: 'Lỗi',
              text: response.data.message,
              iconPack: 'feather',
              icon: 'icon-alert-circle',
              color: 'warning'
            })
          }
        })
        .catch((e) => {
          console.log(e);
          this.$vs.loading.close();
        });
      }
    },
  }
</script>
<style>
[dir] .vs-con-table .vs-con-tbody .vs-table--tbody-table .tr-values .vs-table--td{
  padding: 4px 15px;
}
[dir] .vs-con-table .vs-con-tbody .vs-table--tbody-table .vs-table--thead th{
  padding: 4px 15px;
}
</style>