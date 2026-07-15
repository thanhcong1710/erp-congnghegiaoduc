<template>

  <div id="page-enrolments-list">
    <vx-card no-shadow class="mt-5">
      <div class="vx-row">
        <div class="vx-col md:w-1/4 w-full item-first" style="border-right: 1px solid #ccc;">
          <div class="vx-row">
            <div class="vx-col w-full mb-4">
              <label>Trung tâm <span class="text-danger"> (*)</span></label>
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
              <label >Khóa học</label>
              <vue-select
                    label="name"
                    placeholder="Chọn khóa học"
                    :options="html.products.list"
                    v-model="html.products.item"
                    :searchable="true"
                    language="tv-VN"
                    @input="saveProduct"
                ></vue-select>
            </div>
            <div class="vx-col w-full mb-4">
              <label>Trạng thái lớp</label>
              <vue-select
                    label="label"
                    placeholder="Chọn trạng thái"
                    :options="html.class_status.list"
                    v-model="html.class_status.item"
                    :searchable="false"
                    @input="loadClasses"
                ></vue-select>
            </div>
            <vs-divider/>
            <div class="vx-col w-full mb-4">
              <div class="flex items-center justify-between mb-2">
                <span>Danh sách lớp học</span>
              </div>
              <vs-input v-model="class_search_keyword" placeholder="Tìm tên lớp học..." class="w-full mb-4" icon="icon-search" icon-pack="feather" />
              <tree
                :data="filteredClasses"
                text-field-name="text"
                allow-batch
                @item-click="selectClass"
              >
                <template slot-scope="_">
                  <div style="display: inherit; cursor: pointer; width: 100%;" @click="_.vm.itemClick()">
                    <i :class="_.model.icon" style="margin-right: 5px;"></i>
                    <span v-html="_.model.text"></span>
                  </div>
                </template>
              </tree>
            </div>
          </div>
        </div>
        <div class="vx-col md:w-3/4 w-full item-last">
          <h5 class="w-full mb-3"><i class="fa-solid fa-file-contract mr-1"></i> Thông tin lớp học</h5>
          <div v-if="class_info.class_id" style="background: #72ae7517; padding: 10px; font-size: 15px;">
            <div class="vx-row">
                <div class="vx-col md:w-1/3 w-full text-right">
                  <span>Tên lớp học:</span>
                </div>
                <div class="vx-col md:w-2/3 w-full text-left">
                  <strong>{{class_info.cls_name}}</strong>
                </div>
            </div>
            <div class="vx-row">
                <div class="vx-col md:w-1/3 w-full text-right">
                  <span>Thời gian:</span>
                </div>
                <div class="vx-col md:w-2/3 w-full text-left">
                  <span>{{class_info.cls_startdate | formatDateView}} - {{class_info.cls_enddate | formatDateView}} (<strong v-if="class_info.class_day_text">{{class_info.class_day_text}}</strong>)</span>
                </div>
            </div>
            <div class="vx-row">
                <div class="vx-col md:w-1/3 w-full text-right">
                  <span>Giáo viên:</span>
                </div>
                <div class="vx-col md:w-2/3 w-full text-left">
                  <span>{{class_info.teacher_name}}</span>
                </div>
            </div>
            <div class="vx-row">
                <div class="vx-col md:w-1/3 w-full text-right">
                  <span>Sỹ số:</span>
                </div>
                <div class="vx-col md:w-2/3 w-full text-left" :style="class_info.num_students >= class_info.max_students ? 'color:red':''">
                  <span><strong>{{class_info.num_students}}</strong>/{{class_info.max_students}}</span>
                </div>
            </div>
            <div class="vx-row">
                <div class="vx-col md:w-1/3 w-full text-right">
                  <span>Ca học, phòng học:</span>
                </div>
                <div class="vx-col md:w-2/3 w-full text-left">
                  <span>{{class_info.shift_text}} {{class_info.room_text}}</span>
                </div>
            </div>
            <div class="vx-row">
                <div class="vx-col md:w-1/3 w-full text-right">
                  <span>Quản lý lớp học:</span>
                </div>
                <div class="vx-col md:w-2/3 w-full text-left">
                  <span>{{class_info.cm_name}}</span>
                </div>
            </div>
            <div class="vx-row">
                <div class="vx-col md:w-1/3 w-full text-right">
                  <span>Trợ giảng:</span>
                </div>
                <div class="vx-col md:w-2/3 w-full text-left">
                  <span>{{class_info.ta_name}}</span>
                </div>
            </div>
            <div >
              <div class="flex flex-wrap mt-5">
                <div class="box-item-student active border border-gray-300 rounded min-w-125px py-3 px-5 me-6 mb-3 mr-1 ml-1" v-for="(item, index) in pre_schedules" :key="'P'+index">
                    <div class="label-box-schedule text-center">Buổi {{item.subject_stt}}</div>
                    <div class="text-date-box-schedule text-center">{{item.class_date | formatDateViewDay}}</div>
                    <div class="text-center"><span class="box-status">Đã học</span></div>
                </div>
                <div class="box-item-student border border-gray-300 rounded min-w-125px py-3 px-5 me-6 mb-3 mr-1 ml-1" v-for="(item, index) in next_schedules" :key="'N'+index">
                    <div class="label-box-schedule text-center">Buổi {{item.subject_stt}}</div>
                    <div class="text-date-box-schedule text-center">{{item.class_date | formatDateViewDay}}</div>
                    <div class="text-center"><span class="box-status">Sắp học</span></div>
                </div>
              </div>
            </div>
          </div>
          <div class="mt-5" v-if="class_info.class_id">
            <vs-tabs v-model="activeTab">
              <vs-tab label="Danh sách học sinh">
                <div class="pt-4">
                  <div style="float: left; margin-bottom: 15px;">
                    <select v-model="filterAddClassStatus" class="vs-inputx vs-input--input normal" style="padding: 6px; width: 200px;">
                      <option value="">Tất cả trạng thái</option>
                      <option value="0">Chưa chọn</option>
                      <option value="1">Đã gửi tin nhắn</option>
                      <option value="2">LỖI LINK FB</option>
                      <option value="3">Chờ feedback</option>
                      <option value="4">DONE</option>
                    </select>
                  </div>

                  <vs-button style="float: right" class="mb-3 ml-2" type="border" color="success"  @click.native="exportClassStudents()"><i class="fa fa-file-excel"></i> Xuất Excel</vs-button>
                  <vs-button style="float: right" class="mb-3 ml-2" type="border" color="primary"  @click.native="copyAllFacebookLinks()"><i class="fa-brands fa-facebook"></i> Copy FB</vs-button>
                  <vs-button style="float: right" class="mb-3 ml-2" type="border" color="warning"  @click.native="copyAllPhones()"><i class="fa fa-phone"></i> Copy SĐT</vs-button>
                  <vs-button style="float: right" class="mb-3 ml-2" type="border" color="dark"  @click.native="copyAllNames()"><i class="fa fa-user"></i> Copy Tên</vs-button>
                  <vs-button style="float: right" class="mb-3" type="border" color="success" @click="showModalEnrol"  :disabled="isDisabledAddStudent"><i class="fa fa-plus"></i> Thêm học sinh</vs-button>
                  <div class="vs-component vs-con-table stripe vs-table-primary">
              <div class="con-tablex vs-table--content">
                <div class="vs-con-tbody vs-table--tbody ">
                  <table class="vs-table vs-table--tbody-table">
                    <thead class="vs-table--thead">
                     <tr>
                        <th colspan="1" rowspan="1" class="text-center">STT</th>
                        <th colspan="1" rowspan="1">Học sinh</th>
                        <th colspan="1" rowspan="1">Hợp đồng</th>
                        <th colspan="1" rowspan="1">Buổi học</th>
                        <th colspan="1" rowspan="1" class="text-center">Add lớp</th>
                      </tr>
                    </thead>
                    <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in filteredStudents" :key="index">
                      <td class="td vs-table--td">{{index+1}}</td>
                      <td class="td vs-table--td" style="max-width:250px;">
                        <div v-if="item.added_at" style="margin-top: 5px; font-size: 12px; color: #888;">
                           <i class="fa-regular fa-clock mr-1"></i>{{ item.added_at }}
                         </div>
                        <p>Tên HS: <strong>{{item.name}}</strong></p>
                        <p>Mã HS: <strong>{{item.lms_code}}</strong></p>
                        <p v-if="item.gud_mobile1">SĐT: <strong>{{item.gud_mobile1}}</strong></p>
                        <div v-if="item.link_facebook" style="margin-top:4px;">
                          <a :href="item.link_facebook" target="_blank" rel="noopener noreferrer"
                            style="font-size:12px; color:#1877f2; word-break:break-all;"
                            :title="item.link_facebook"
                          ><i class="fa-brands fa-facebook mr-1"></i>{{ item.link_facebook }}</a>
                        </div>
                      </td>
                      <td class="td vs-table--td">
                        <p><strong>Mã: {{item.contract_code}}</strong></p>
                        <p>Gói: {{item.tuition_fee_name}}</p>
                        <p>Phải đóng: {{item.must_charge | formatMoney}}</p>
                        <p><strong>Đã đóng: {{item.total_charged | formatMoney}}</strong></p>
                        <p v-if="item.source_name">Nguồn: {{ item.source_name }}</p>
                        <p v-if="item.ec_name">Sale: {{item.ec_name}}</p>
                        <p v-if="item.team_name"><strong>Team KD: {{item.team_name}}</strong></p>
                      </td>
                      <td class="td vs-table--td">
                        <p>Ngày bắt đầu: {{item.enrolment_start_date}}</p>
                        <p>Ngày kết thúc: {{item.enrolment_last_date}}</p>
                        <p>Số buổi đã học: <strong>{{item.done_sessions}}</strong></p>
                        <p>Tổng số buổi: {{item.summary_sessions}}</p>
                      </td>
                      <td class="td vs-table--td text-center">
                         <select
                           v-model="item.add_class_status"
                           class="vs-inputx vs-input--input normal"
                           :style="`width:160px; padding:5px !important; ${item.add_class_status == 4 ? 'background-color: #d4edda !important;' : item.add_class_status == 3 ? 'background-color: #fff3cd !important;' : item.add_class_status == 2 ? 'background-color: #f8d7da !important;' : ''}`"
                           @change="updateAddClassStatus(item)"
                           :disabled="user_role.is_sale || user_role.is_sale_leader"
                         >
                           <option :value="0">-- Chưa chọn --</option>
                           <option :value="1">Đã gửi tin nhắn</option>
                           <option :value="2">LỖI LINK FB</option>
                           <option :value="3">Chờ feedback</option>
                           <option :value="4">DONE</option>
                         </select>
                         <br>
                        <vs-button 
                          v-if="canRemoveStudent(item)"
                          size="small" 
                          color="danger" 
                          type="border" 
                          icon-pack="feather" 
                          icon="icon-trash-2"
                          style="margin: auto; margin-top: 10px;"
                          @click="confirmRemoveStudent(item)"
                        >
                          Xóa
                        </vs-button>
                        <span v-else class="text-muted" style="font-size: 12px;">Đã học</span>
                      </td>
                    </tr>
                  </table>
                </div>
              </div>
            </div>
                </div>
              </vs-tab>
              <vs-tab label="Lịch sử xếp lớp" @click="loadClassLogs">
                <div class="pt-4">
                  <div class="vs-component vs-con-table stripe vs-table-primary">
                    <div class="con-tablex vs-table--content">
                      <div class="vs-con-tbody vs-table--tbody ">
                        <table class="vs-table vs-table--tbody-table">
                          <thead class="vs-table--thead">
                           <tr>
                              <th class="text-center">Thời gian</th>
                              <th>Thao tác</th>
                              <th>Học sinh</th>
                              <th>Hợp đồng</th>
                              <th>Người thực hiện</th>
                            </tr>
                          </thead>
                          <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(log, idx) in class_logs" :key="'log'+idx">
                            <td class="td vs-table--td text-center">{{ log.created_at }}</td>
                            <td class="td vs-table--td">
                              <strong :class="log.action == 1 ? 'text-success' : 'text-danger'">
                                {{ log.action == 1 ? 'Xếp vào lớp' : 'Xóa khỏi lớp' }}
                              </strong>
                            </td>
                            <td class="td vs-table--td">
                              <p>Tên HS: {{ log.student_name }}</p>
                              <p>Mã HS: {{ log.lms_code }}</p>
                            </td>
                            <td class="td vs-table--td">{{ log.contract_code }}</td>
                            <td class="td vs-table--td">{{ log.creator_name }}</td>
                          </tr>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </vs-tab>
            </vs-tabs>
          </div>
        </div>
      </div>
      <vs-popup :class="'view-enrolments modal_'+ modal_enrol.color" :title="modal_enrol.title" :active.sync="modal_enrol.show" v-if="class_info.class_id">
        <div class="vx-row" >
          <div class="vx-col sm:w-1/4 w-full mb-4">
            <label>Tìm kiếm</label>
            <vs-input class="w-full" placeholder="Nhập tên, mã học sinh" v-model="searchData.keyword"></vs-input>
          </div>
          <div class="vx-col sm:w-1/4 w-full mb-4" v-if="!user_role.is_sale && !user_role.is_sale_leader">
            <label>Team KD (quản lý)</label>
            <vue-select
              label="label"
              placeholder="Tất cả"
              :options="ec_managers"
              v-model="searchData.ec_selected"
              :searchable="true"
            ></vue-select>
          </div>
          <div class="vx-col sm:w-1/4 w-full mb-4">
            <label>Ngày học dự kiến</label>
            <datepicker v-model="searchData.dateRange" format="YYYY-MM-DD" style="width:100%" type="date" range :clearable="true" :lang="datepickerOptions.lang" placeholder="Từ ngày — Đến ngày"></datepicker>
          </div>
          <div class="vx-col sm:w-1/4 w-full mb-4">
            Số chỗ trống còn lại trong lớp: <strong>{{class_info.max_students - class_info.num_students - checked_list.length}}</strong>
          </div>
        </div>
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <vs-button class="mr-3" @click="getData">Tìm kiếm</vs-button>
            <vs-button color="success" @click="addStudent">Thêm học sinh</vs-button>
          </div>
        </div>
        <vs-alert :active.sync="alert.active" class="mb-5" :color="alert.color" closable icon-pack="feather" close-icon="icon-x">
         <div v-html="alert.body"></div>
        </vs-alert>
        <div>
          <div class="vs-component vs-con-table stripe vs-table-primary">
            <div class="con-tablex vs-table--content">
              <div class="vs-con-tbody vs-table--tbody ">
                <table class="vs-table vs-table--tbody-table">
                  <thead class="vs-table--thead">
                    <tr>
                       <th colspan="1" rowspan="1" class="text-center">
                        #
                      </th>
                      <th colspan="1" rowspan="1" class="text-center">STT</th>
                      <th colspan="1" rowspan="1">Tên học sinh</th>
                      <th colspan="1" rowspan="1" class="text-center">Mã học sinh</th>
                      <th colspan="1" rowspan="1" class="text-center">Ngày học dự kiến</th>
                      <th colspan="1" rowspan="1" class="text-center">Ngày bắt đầu học</th>
                      <th colspan="1" rowspan="1">Gói phí</th>
                      <th colspan="1" rowspan="1" class="text-center">Số buổi còn lại</th>
                      <th colspan="1" rowspan="1" >EC</th>
                    </tr>
                  </thead>
                  <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in studentSearch" :key="index">
                    <td class="td vs-table--td">
                      <div class="vs-component con-vs-checkbox vs-checkbox-primary vs-checkbox-default">
                        <input type="checkbox" v-model="checked_list" :value="item.contract_id" class="vs-checkbox--input" :disabled="isStudentCheckboxDisabled(item)">
                        <span class="checkbox_x vs-checkbox" style="border: 2px solid rgb(180, 180, 180);">
                          <span class="vs-checkbox--check">
                            <i class="vs-icon notranslate icon-scale vs-checkbox--icon  material-icons null">check</i>
                          </span>
                        </span>
                      </div>
                    </td>
                    <td class="td vs-table--td text-center">{{ index + 1 + (pagination.cpage - 1) * pagination.limit }}</td>
                    <td class="td vs-table--td">
                      {{item.name}}
                      <p class="text-danger mt-1 text-sm" v-if="isStudentCheckboxDisabled(item)" style="font-size: 11px;">
                        <i>* Chỉ được xếp lớp khi có bill từ 2 triệu (hiện tại: {{ (item.tmp_payment_amount || 0) | formatMoney }})</i>
                      </p>
                    </td>
                    <td class="td vs-table--td text-center">{{item.lms_code}}</td>
                    <td class="td vs-table--td text-center">{{item.start_date}}</td>
                    <td class="td vs-table--td text-center">
                      <select v-model="item.class_date" class="vs-inputx vs-input--input normal" style="width: 154px; padding: 5px !important;">
                        <option value="">Chọn ngày bắt đầu</option>
                        <option :value="`${class_date.class_date}`" v-for="(class_date, ind) in filterStartDate(item, class_dates)" :key="ind">{{class_date.class_date}}</option>
                      </select>
                    </td>
                    <td class="td vs-table--td">{{item.tuition_fee_name}}</td>
                    <td class="td vs-table--td text-center">{{item.left_session}}</td>
                    <td class="td vs-table--td">{{item.ec_name}}</td>
                  </tr>
                </table>
              </div>
            </div>
          </div>
          <div class="flex flex-wrap items-center mt-5">
            <vs-dropdown vs-trigger-click class="cursor-pointer mr-4 items-per-page-handler">
              <div class="p-4 border border-solid d-theme-border-grey-light rounded-full d-theme-dark-bg cursor-pointer flex items-center justify-between font-medium">
                <span class="mr-2">{{ pagination.cpage * pagination.limit - (pagination.limit - 1) }} - {{ pagination.total - pagination.cpage * pagination.limit > 0 ? pagination.cpage * pagination.limit : pagination.total }} of {{ pagination.total }}</span>
                <feather-icon icon="ChevronDownIcon" svgClasses="h-4 w-4" />
              </div>
              <vs-dropdown-menu>
                <vs-dropdown-item v-for="(item, index) in limitSource" :key="index" @click="pagination.limit=item" >
                  <span>{{item}}</span>
                </vs-dropdown-item>
              </vs-dropdown-menu>
            </vs-dropdown>
            <vs-pagination
                  style="width: calc(100% - 160px);"
                  v-if="Math.ceil(pagination.total / pagination.limit) >1"
                  :total="Math.ceil(pagination.total / pagination.limit)"
                  :max="7"
                  v-model="pagination.cpage" @change="changePage()"/>
          </div>
        </div>
      </vs-popup>
    </vx-card>
  </div>

</template>

<script>

  import select from 'vue-select'
  import axios from '../../../http/axios.js'
  import u from '../../../until/helper.js'
  import datepicker from "vue2-datepicker";
  import moment from 'moment';
  import tree from 'vue-jstree'
  
  export default {
    components: {
      datepicker,
      "vue-select": select,
      tree,
    },
    data() {
      return {
        modal_enrol: {
          title: "THÊM HỌC SINH VÀO LỚP",
          show: false,
          color: "info",
          closeOnBackdrop: true,
          error_message:""
        },
        alert:{
          active: false,
          body: '',
          color:'',
        },
        class_search_keyword: '',
        filterAddClassStatus: '',
        classes: [],
        html:{
          branches: {
            item: '',
            list: []
          },
          products: {
            item: '',
            list: []
          },
          class_status: {
            item: {id: 1, label: 'Sắp khai giảng'},
            list: [
              {id: 0, label: 'Tất cả'},
              {id: 1, label: 'Sắp khai giảng'},
              {id: 2, label: 'Đang học'},
              {id: 3, label: 'Đã kết thúc'}
            ]
          }
        },
        enrol:{
          branch_id:'',
          product_id:'',
        },
        class_info:{
          class_id:'',
        },
        students:[],
        class_dates: [],
        searchData:{
          keyword:'',
          ec_selected: null,
          dateRange: [],
        },
        ec_managers: [],
        datepickerOptions: { lang: { days:['CN','T2','T3','T4','T5','T6','T7'], months:['Tháng 1','Tháng 2','Tháng 3','Tháng 4','Tháng 5','Tháng 6','Tháng 7','Tháng 8','Tháng 9','Tháng 10','Tháng 11','Tháng 12'] } },
        studentSearch:[],
        limitSource: [20, 50, 100, 500],
        pagination: {
          url: "/api/roles/list",
          id: "",
          style: "line",
          class: "",
          spage: 1,
          ppage: 1,
          npage: 0,
          lpage: 1,
          cpage: 1,
          total: 0,
          limit: 20,
          pages: [],
          init: 0
        },
        search_student : false,
        checked_list: [],
        next_schedules:[],
        pre_schedules:[],
        class_logs: [],
        activeTab: 0,
        user_role: {
          user_id: 0,
          is_admin: false,
          is_sale: false,
          is_sale_leader: false,
        },
        minPaymentForClass: 2000000,
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
      axios.g(`/api/system/users?role_id=69`)
        .then(response => {
        this.ec_managers = response.data || []
      })
    },
    computed: {
      filteredStudents() {
        if (!this.students) return [];
        return this.students.filter(s => {
          if (this.filterAddClassStatus === '') return true;
          return s.add_class_status == this.filterAddClassStatus;
        });
      },
      isDisabledAddStudent() {
        if (!this.class_info || !this.class_info.class_id) return true;
        if (this.class_info.num_students >= this.class_info.max_students) {
          return true;
        }
        if (this.user_role.is_sale && !this.user_role.is_sale_leader) {
          return true;
        }
        if (this.user_role.is_sale_leader && this.class_info.cls_startdate) {
          const startDate = moment(this.class_info.cls_startdate, 'YYYY-MM-DD');
          const today = moment().startOf('day');
          if (today.isAfter(startDate)) {
            return true;
          }
        }
        return false;
      },
      filteredClasses() {
        if (!this.class_search_keyword) return this.classes;
        let kw = this.class_search_keyword.toLowerCase();
        return this.classes.filter(c => c.cls_name && c.cls_name.toLowerCase().includes(kw));
      }
    },
    methods: {
      saveBranch(data = null){
        if (data && typeof data === 'object') {
          const branch_id = data.id
          this.enrol.branch_id = branch_id
        }else{
          this.enrol.branch_id = ""
        }
        this.loadClasses();
      },
      isStudentCheckboxDisabled(item) {
        if (this.user_role.is_sale || this.user_role.is_sale_leader) {
          const totalPaid = Number(item.tmp_payment_amount) || 0;
          if (totalPaid < this.minPaymentForClass) {
            return true;
          }
        }
        return false;
      },
      saveProduct(data = null){
        if (data && typeof data === 'object') {
          const product_id = data.id
          this.enrol.product_id = product_id
        }else{
          this.enrol.product_id = ""
        }
        this.loadClasses();
      },
      loadClasses(){
        if(this.enrol.branch_id && this.enrol.product_id){
          this.$vs.loading();
          axios.p(`/api/lms/enrolments/load-classes`, {
            branch_id: this.enrol.branch_id,
            product_id: this.enrol.product_id,
            class_status: this.html.class_status.item ? this.html.class_status.item.id : 0
          })
            .then(response => {
            this.$vs.loading.close();
            this.classes = response.data
          })
        }else{
          this.classes =[]
        }
      },
      loadClassLogs() {
        if (!this.class_info.class_id) return;
        this.$vs.loading();
        axios.g(`/api/lms/enrolments/class-logs/${this.class_info.class_id}`)
          .then(response => {
            this.$vs.loading.close();
            this.class_logs = response.data;
          })
          .catch(e => {
            console.log(e);
            this.$vs.loading.close();
          });
      },
      selectClass(selected_class) {
        if (selected_class.model.item_type === 'class') {
          this.loadDataClassSelected(selected_class.model.item_id)
        } else {
          this.class_info = {};
          this.students= []
          this.class_dates =[]
          this.next_schedules=[]
          this.pre_schedules=[]
        }
      },
      loadDataClassSelected(class_id){
        this.activeTab = 0;
        this.$vs.loading();
        axios.g(`/api/lms/enrolments/info-class/${class_id}`)
          .then(response => {
          this.$vs.loading.close();
          this.class_info = response.data.class_info
          this.class_dates = response.data.class_dates
          this.students = response.data.students
          this.next_schedules = response.data.next_schedules
          this.pre_schedules = response.data.pre_schedules
          if (response.data.user_role) {
            this.user_role = response.data.user_role
          }
        })
      },
      showModalEnrol(){
        this.modal_enrol.show =true
        this.checked_list =[]
        this.studentSearch = []
        this.searchData.keyword = ''
        this.searchData.dateRange = []
        // Auto-set ec_id for sale_leader role (role 69 is in ec_managers list)
        if (this.user_role.is_sale_leader) {
          const matched = this.ec_managers.find(m => m.id === this.user_role.user_id)
          this.searchData.ec_selected = matched || null
        } else {
          this.searchData.ec_selected = null
        }
        this.getData()
      },
      changePage() {
        if (this.pagination.init) {
          this.getData();
        }
      },
      changePageLimit(limit) {
        this.pagination.cpage = 1
        this.pagination.limit = limit
        this.getData();
      },
      getData() {
        if(this.class_info.class_id){
          const data = {
            class_id: this.class_info.class_id,
            keyword: this.searchData.keyword,
            ec_id: this.searchData.ec_selected ? this.searchData.ec_selected.id : '',
            start_date_from: this.searchData.dateRange && this.searchData.dateRange[0] ? moment(this.searchData.dateRange[0]).format('YYYY-MM-DD') : '',
            start_date_to: this.searchData.dateRange && this.searchData.dateRange[1] ? moment(this.searchData.dateRange[1]).format('YYYY-MM-DD') : '',
            pagination:this.pagination
          }
          this.$vs.loading()
          axios.p('/api/lms/enrolments/get-students-add', data)
            .then((response) => {
              this.$vs.loading.close()
              this.studentSearch = response.data.list
              this.total = response.data.detail_total
              this.pagination = response.data.paging;
              setTimeout(() => {
              this.pagination.init = 1;
            }, 500)
            })
            .catch((error) => {
              console.log(error);
              this.$vs.loading.close();
            })
        }else{
          this.studentSearch = []
        }
        
      },
      filterStartDate(student, class_dates) {
        let start_dates = class_dates
        const resp = []
        if (start_dates.length) {
          start_dates.map(item => {
            if (1==1  || moment(item.class_date).isSameOrAfter(student.start_date)) {
              resp.push(item)
            }
            return item
          })
        }
        return resp
      },
      addStudent(){  
        let mess = "";
        let resp = true;
        if (this.checked_list.length == 0) {
          mess += " - Chưa chọn học sinh<br/>";
          resp = false;
        }
        if (this.class_info.max_students - this.class_info.num_students - this.checked_list.length < 0) {
          mess += " - Số học sinh đã chọn lớn hơn số chỗ trống còn lại trong lớp<br/>";
          resp = false;
        }
        const selected_contracts = this.studentSearch.filter(item => this.checked_list.indexOf(item.contract_id) > -1)
        selected_contracts.map(selected_contract => {
          if (!selected_contract.class_date || selected_contract.class_date.toString() == '') {
            mess += ` - Học sinh đã chọn " ${selected_contract.name} - ${selected_contract.lms_code}" chưa được chỉ định ngày bắt đầu học<br/>`;
            resp = false;
          }
        })
        if (!resp) {
          this.alert.color = 'danger'
          this.alert.body = mess;
          this.alert.active = true;
          return false;
        }
        this.$vs.loading()
        axios.p("/api/lms/enrolments/add-student",{
          class_id : this.class_info.class_id,
          contracts: selected_contracts
        })
        .then((response) => {
          this.$vs.loading.close();;
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          this.loadDataClassSelected(this.class_info.class_id);
          this.modal_enrol.show =false
        })
        .catch((e) => {
          console.log(e);
          this.$vs.loading.close();
        });
      },
      canRemoveStudent(student) {
        // Admin được phép xóa luôn
        if (this.user_role.is_admin) {
          return true
        }
        // Role 68 (sale): chỉ được view, không được xóa
        if (this.user_role.is_sale && !this.user_role.is_sale_leader) {
          return false
        }
        // Chỉ cho phép xóa nếu lớp chưa bắt đầu (done_sessions = 0)
        if (student.done_sessions !== 0 && student.done_sessions !== '0') {
          return false
        }
        // Role 69 (sale leader): xóa HS có ec_id hoặc ec_leader_id = user_id
        if (this.user_role.is_sale_leader) {
          return parseInt(student.ec_id) === parseInt(this.user_role.user_id) || parseInt(student.ec_leader_id) === parseInt(this.user_role.user_id)
        }
        // Các role khác: cho phép xóa
        return true
      },
      confirmRemoveStudent(student) {
        this.$vs.dialog({
          type: 'confirm',
          color: 'danger',
          title: 'Xác nhận xóa học sinh',
          text: `Bạn có chắc chắn muốn xóa học sinh "${student.name}" (${student.lms_code}) khỏi lớp này?`,
          accept: () => this.removeStudent(student),
          acceptText: 'Xóa',
          cancelText: 'Hủy'
        })
      },
      removeStudent(student) {
        this.$vs.loading()
        axios.p("/api/lms/enrolments/remove-student", {
          contract_id: student.contract_id,
          class_id: this.class_info.class_id
        })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          this.loadDataClassSelected(this.class_info.class_id)
        })
        .catch((e) => {
          console.log(e)
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Lỗi',
            text: e.response?.data?.message || 'Có lỗi xảy ra khi xóa học sinh',
            color: 'danger',
            iconPack: 'feather',
            icon: 'icon-alert-circle'
          })
        })
      },
      copyFacebookLink(link) {
        if (!link) return;
        if (navigator.clipboard && window.isSecureContext) {
          navigator.clipboard.writeText(link).then(() => {
            this.$vs.notify({
              title: 'Đã copy',
              text: 'Đã sao chép link Facebook vào clipboard',
              color: 'success',
              iconPack: 'feather',
              icon: 'icon-copy'
            })
          }).catch(() => {
            this.fallbackCopy(link)
          })
        } else {
          this.fallbackCopy(link)
        }
      },
      copyAllFacebookLinks() {
        const links = this.filteredStudents
          .map(s => s.link_facebook)
          .filter(l => !!l)
        if (links.length === 0) {
          this.$vs.notify({
            title: 'Thông báo',
            text: 'Không có học sinh nào có link Facebook',
            color: 'warning',
            iconPack: 'feather',
            icon: 'icon-alert-circle'
          })
          return
        }
        const text = links.join('\n')
        if (navigator.clipboard && window.isSecureContext) {
          navigator.clipboard.writeText(text).then(() => {
            this.$vs.notify({
              title: 'Đã copy',
              text: `Đã sao chép ${links.length} link Facebook vào clipboard`,
              color: 'success',
              iconPack: 'feather',
              icon: 'icon-copy'
            })
          }).catch(() => {
            this.fallbackCopy(text)
          })
        } else {
          this.fallbackCopy(text)
        }
      },
      copyAllNames() {
        const names = this.filteredStudents
          .map(s => s.name)
          .filter(n => !!n)
        if (names.length === 0) {
          this.$vs.notify({
            title: 'Thông báo',
            text: 'Không có học sinh nào',
            color: 'warning',
            iconPack: 'feather',
            icon: 'icon-alert-circle'
          })
          return
        }
        const text = names.join('\n')
        if (navigator.clipboard && window.isSecureContext) {
          navigator.clipboard.writeText(text).then(() => {
            this.$vs.notify({
              title: 'Đã copy',
              text: `Đã sao chép ${names.length} tên học sinh vào clipboard`,
              color: 'success',
              iconPack: 'feather',
              icon: 'icon-copy'
            })
          }).catch(() => {
            this.fallbackCopy(text)
          })
        } else {
          this.fallbackCopy(text)
        }
      },
      exportClassStudents() {
        if (!this.class_info || !this.class_info.class_id) return;
        window.open(`/api/lms/exports/class-students/${this.class_info.class_id}?token=${localStorage.getItem('accessToken')}`, '_blank');
      },
      copyAllPhones() {
        const phones = this.filteredStudents
          .map(s => s.gud_mobile1)
          .filter(p => !!p)
        if (phones.length === 0) {
          this.$vs.notify({
            title: 'Thông báo',
            text: 'Không có học sinh nào có số điện thoại',
            color: 'warning',
            iconPack: 'feather',
            icon: 'icon-alert-circle'
          })
          return
        }
        const text = phones.join('\n')
        if (navigator.clipboard && window.isSecureContext) {
          navigator.clipboard.writeText(text).then(() => {
            this.$vs.notify({
              title: 'Đã copy',
              text: `Đã sao chép ${phones.length} số điện thoại vào clipboard`,
              color: 'success',
              iconPack: 'feather',
              icon: 'icon-copy'
            })
          }).catch(() => {
            this.fallbackCopy(text)
          })
        } else {
          this.fallbackCopy(text)
        }
      },
      fallbackCopy(text) {
        const el = document.createElement('textarea')
        el.value = text
        el.style.position = 'fixed'
        el.style.opacity = '0'
        document.body.appendChild(el)
        el.focus()
        el.select()
        try {
          document.execCommand('copy')
          this.$vs.notify({
            title: 'Đã copy',
            text: 'Đã sao chép link Facebook vào clipboard',
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-copy'
          })
        } catch (e) {
          this.$vs.notify({
            title: 'Lỗi',
            text: 'Không thể copy, vui lòng copy thủ công',
            color: 'danger',
            iconPack: 'feather',
            icon: 'icon-alert-circle'
          })
        }
        document.body.removeChild(el)
      },
      updateAddClassStatus(item) {
        axios.p('/api/lms/enrolments/update-add-class-status', {
          contract_id: item.contract_id,
          add_class_status: item.add_class_status
        }).catch((e) => {
          console.log(e)
          this.$vs.notify({
            title: 'Lỗi',
            text: 'Cập nhật trạng thái thất bại',
            color: 'danger',
            iconPack: 'feather',
            icon: 'icon-alert-circle'
          })
        })
      }
    },
  }
</script>
<style>
.view-enrolments.con-vs-popup .vs-popup{
  width: 90%;
}

.td.vs-table--td{
  vertical-align: top;
  font-size: 15px;
}
.td.vs-table--td p {
  font-size: 15px;
  margin-bottom: 2px;
}
.box-item-student.active{
  border: 1px solid rgba(var(--vs-success), 1);
  background: transparent !important;
}
.box-item-student .label-box-schedule{
  font-size: 13px;
}
.box-item-student .text-date-box-schedule{
  font-size: 11px;
}
.box-item-student.active .box-status{
    font-size: 10px;
    background: rgba(var(--vs-success), 1);
    padding: 5px;
    border-radius: 6px;
    color: #fff;
}
.box-item-student .box-status{
    font-size: 10px;
    background: rgba(var(--vs-primary),1);
    padding: 5px;
    border-radius: 6px;
    color: #fff;
}
.box-item-student{
  border: 1px solid rgba(var(--vs-primary),1);
  background: transparent !important;
}
</style>