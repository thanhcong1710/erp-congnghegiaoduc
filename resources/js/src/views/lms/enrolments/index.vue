<template>

  <div id="page-users-list">
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
            <vs-divider/>
            <div class="vx-col w-full mb-4">
              <tree
                :data="classes"
                text-field-name="text"
                allow-batch
                @item-click="selectClass"
              >
              </tree>
            </div>
          </div>
        </div>
        <div class="vx-col md:w-3/4 w-full item-last">
          <h5 class="w-full mb-3"><i class="fa-solid fa-file-contract mr-1"></i> Thông tin lớp học</h5>
          <div v-if="class_info.class_id" style="background: #72ae7517; padding: 10px; font-size: 16px;">
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
                  <span>{{class_info.cls_startdate | formatDateView}} - {{class_info.cls_enddate | formatDateView}}</span>
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
                  <span>CM - giáo viên chủ nhiệm:</span>
                </div>
                <div class="vx-col md:w-2/3 w-full text-left">
                  <span>{{class_info.cm_name}}</span>
                </div>
            </div>
          </div>
          <div class="mt-5" v-if="class_info.class_id">
            <vs-button style="float: right" class="mb-3" type="border" color="success" @click="showModalEnrol"  :disabled="class_info.num_students >= class_info.max_students"><i class="fa fa-plus"></i> Thêm học sinh</vs-button>
            <div class="vs-component vs-con-table stripe vs-table-primary">
              <div class="con-tablex vs-table--content">
                <div class="vs-con-tbody vs-table--tbody ">
                  <table class="vs-table vs-table--tbody-table">
                    <thead class="vs-table--thead">
                      <tr>
                        <th colspan="1" rowspan="1" class="text-center">
                          <div class="vs-table-text text-center">STT
                            <!---->
                          </div>
                        </th>
                        <th colspan="1" rowspan="1" class="text-center">
                          <div class="vs-table-text">Hợp đồng
                            <!---->
                          </div>
                        </th>
                        <th colspan="1" rowspan="1" class="text-center">
                          <div class="vs-table-text">Gói phí
                            <!---->
                          </div>
                        </th>
                        <th colspan="1" rowspan="1" class="text-center">
                          <div class="vs-table-text">Buổi học
                            <!---->
                          </div>
                        </th>
                      </tr>
                    </thead>
                    <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in students" :key="index">
                      <td class="td vs-table--td">{{index+1}}</td> 
                      <td class="td vs-table--td">
                        <p><strong>{{item.contract_code}}</strong></p>
                        <p>Tên HS: {{item.name}}</p>
                        <p>Mã HS:{{item.lms_code}}</p>
                        <p>Ngày bắt đầu: {{item.enrolment_start_date}}</p>
                        <p>Ngày kết thúc: {{item.enrolment_last_date}}</p>
                      </td>
                      <td class="td vs-table--td">
                        <p><strong>{{item.tuition_fee_name}}</strong></p>
                        <p>Phải đóng: {{item.must_charge}}</p>
                        <p>Đã đóng: {{item.total_charged}}</p>
                      </td>
                      <td class="td vs-table--td">
                        <p>Số buổi đã học: <strong>{{item.done_session}}</strong></p>
                        <p>Tổng số buổi: {{item.summary_sessions}}</p>
                        <p>Trạng thái: <strong></strong></p>
                      </td>
                    </tr>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <vs-popup :class="'modal_'+ modal_enrol.color" :title="modal_enrol.title" :active.sync="modal_enrol.show" v-if="class_info.class_id">
        <div class="vx-row" > 
          <div class="vx-col sm:w-1/4 w-full mb-4">
            <vs-input class="w-full" placeholder="Nhập tên, mã học sinh" v-model="searchData.keyword"></vs-input>
          </div>
          <div class="vx-col sm:w-1/2 w-full mb-4">
            <vs-button class="mb-3 mr-3" @click="getData">Tìm kiếm</vs-button>
            <vs-button class="mb-3" color="success" @click="addStudent">Thêm học sinh</vs-button>
          </div>
          <div class="vx-col sm:w-1/4 w-full mb-4">
            Số chỗ trống còn lại trong lớp: <strong>{{class_info.max_students - class_info.num_students - checked_list.length}}</strong>
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
                      <th colspan="1" rowspan="1" class="text-center">
                        <div class="vs-table-text text-center">STT
                          <!---->
                        </div>
                      </th>
                      <th colspan="1" rowspan="1" class="text-center">
                        <div class="vs-table-text">Tên học sinh
                          <!---->
                        </div>
                      </th>
                      <th colspan="1" rowspan="1" class="text-center">
                        <div class="vs-table-text">Mã học sinh
                          <!---->
                        </div>
                      </th>
                      <th colspan="1" rowspan="1" class="text-center">
                        <div class="vs-table-text">Ngày học dự kiến
                          <!---->
                        </div>
                      </th>
                      <th colspan="1" rowspan="1" class="text-center">
                        <div class="vs-table-text">Ngày bắt đầu học
                          <!---->
                        </div>
                      </th>
                      <th colspan="1" rowspan="1" class="text-center">
                        <div class="vs-table-text">Gói phí
                          <!---->
                        </div>
                      </th>
                      <th colspan="1" rowspan="1" class="text-center">
                        <div class="vs-table-text">Số buổi còn lại
                          <!---->
                        </div>
                      </th>
                      <th colspan="1" rowspan="1" class="text-center">
                        <div class="vs-table-text">EC
                          <!---->
                        </div>
                      </th>
                    </tr>
                  </thead>
                  <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in studentSearch" :key="index">
                    <td class="td vs-table--td">
                      <div class="vs-component con-vs-checkbox vs-checkbox-primary vs-checkbox-default">
                        <input type="checkbox" v-model="checked_list" :value="item.contract_id" class="vs-checkbox--input">
                        <span class="checkbox_x vs-checkbox" style="border: 2px solid rgb(180, 180, 180);">
                          <span class="vs-checkbox--check">
                            <i class="vs-icon notranslate icon-scale vs-checkbox--icon  material-icons null">check</i>
                          </span>
                        </span>
                      </div>
                    </td>
                    <td class="td vs-table--td">{{ index + 1 + (pagination.cpage - 1) * pagination.limit }}</td>
                    <td class="td vs-table--td">{{item.name}}</td>
                    <td class="td vs-table--td">{{item.lms_code}}</td>
                    <td class="td vs-table--td">{{item.start_date}}</td>
                    <td class="td vs-table--td">
                      <select v-model="item.class_date" class="vs-inputx vs-input--input normal" style="width: 154px; padding: 5px !important;">
                        <option value="">Chọn ngày bắt đầu</option>
                        <option :value="`${class_date.class_date}`" v-for="(class_date, ind) in filterStartDate(item, class_dates)" :key="ind">{{class_date.class_date}}</option>
                      </select>
                    </td>
                    <td class="td vs-table--td">{{item.tuition_fee_name}}</td>
                    <td class="td vs-table--td">{{item.left_session}}</td>
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
          tuition_fee:{
            item: '',
            list: []
          },
          discount_codes:{
            item: '',
            list: []
          }
        },
        enrol:{
          branch_id:'',
          product_id:''
        },
        class_info:{
          class_id:'',
        },
        students:[],
        class_dates: [],
        searchData:{
          keyword:''
        },
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
      saveBranch(data = null){
        if (data && typeof data === 'object') {
          const branch_id = data.id
          this.enrol.branch_id = branch_id
        }else{
          this.enrol.branch_id = ""
        }
        this.loadClasses();
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
            product_id: this.enrol.product_id
          })
            .then(response => {
            this.$vs.loading.close();
            this.classes = response.data
          })
        }else{
          this.classes =[]
        }
      },
      selectClass(selected_class) {
        if (selected_class.model.item_type === 'class') {
          this.loadDataClassSelected(selected_class.model.item_id)
        } else {
          this.class_info = {};
          this.students= []
          this.class_dates =[]
        }
      },
      loadDataClassSelected(class_id){
        this.$vs.loading();
        axios.g(`/api/lms/enrolments/info-class/${class_id}`)
          .then(response => {
          this.$vs.loading.close();
          this.class_info = response.data.class_info
          this.class_dates = response.data.class_dates
          this.students = response.data.students
        })
      },
      showModalEnrol(){
        this.modal_enrol.show =true
        this.checked_list =[]
        this.studentSearch = []
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
            if (moment(item.cjrn_classdate).isSameOrAfter(student.start_date)) {
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
      }
    },
  }
</script>
<style>
.con-vs-popup .vs-popup{
  width: 90%;
}

.td.vs-table--td{
  vertical-align: top;
}
</style>