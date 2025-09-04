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
              <label >Loại</label>
              <select class="vs-inputx vs-input--input normal" disabled v-model="select_type" >
                <option value="0">Tất cả các lớp</option>
                <option value="1">Lớp đang học</option>
                <option value="2">Lớp đã kết thúc</option>
              </select>
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
          <h5 class="w-full mb-3"><i class="fa-solid fa-file-contract mr-1"></i> Thông tin bảo lưu</h5>
          <div class="vx-row">
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Số buổi bảo lưu <span class="text-danger"> (*)</span></label>
              <input
                class="vs-inputx vs-input--input normal"
                type="number"
                name="title"
                v-model="reserve.session"
                @change="getEndDate()"
                :disabled="input_disabled"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Ngày bắt đầu bảo lưu <span class="text-danger"> (*)</span></label>
              <datepicker class="w-full"
                v-model="reserve.start_date"
                placeholder="Chọn ngày bắt đầu"
                :lang="datepickerOptions.lang"
                @change="selectDate"
                :disabled="input_disabled"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Ngày kết thúc bảo lưu</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="reserve.end_date"
                disabled="true"
              />
            </div>
            <div class="vx-col w-full mb-4">
              <label>Ghi chú</label>
              <textarea class="vs-inputx vs-input--input normal" v-model="reserve.note" :disabled="input_disabled"></textarea>
            </div>
          </div>
          <div class="mt-5" v-if="class_info.class_id">
            <div class="vs-component vs-con-table stripe vs-table-primary">
              <div class="con-tablex vs-table--content">
                <div class="vs-con-tbody vs-table--tbody ">
                  <table class="vs-table vs-table--tbody-table">
                    <thead class="vs-table--thead">
                      <tr>
                        <th colspan="1" rowspan="1" class="text-center">
                          <div class="vs-component con-vs-checkbox vs-checkbox-primary vs-checkbox-default">
                            <input type="checkbox" v-model="selectAll" class="vs-checkbox--input" >
                            <span class="checkbox_x vs-checkbox" style="border: 2px solid rgb(180, 180, 180);">
                              <span class="vs-checkbox--check">
                                <i class="vs-icon notranslate icon-scale vs-checkbox--icon  material-icons null">check</i>
                              </span>
                            </span>
                          </div>
                        </th>
                        <th colspan="1" rowspan="1" >Học sinh</th>
                        <th colspan="1" rowspan="1" class="text-center">Mã LMS</th>
                        <th colspan="1" rowspan="1" class="text-center">Buổi học đã học</th>
                        <th colspan="1" rowspan="1" class="text-center">Tổng số buổi</th>
                        <th colspan="1" rowspan="1" class="text-center">Trạng thái</th>
                      </tr>
                    </thead>
                    <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in students" :key="index">
                      <td class="td vs-table--td text-center">
                        <div class="vs-component con-vs-checkbox vs-checkbox-primary vs-checkbox-default">
                          <input type="checkbox" v-model="checked_list" :value="item.student_id" class="vs-checkbox--input" >
                          <span class="checkbox_x vs-checkbox" style="border: 2px solid rgb(180, 180, 180);">
                            <span class="vs-checkbox--check">
                              <i class="vs-icon notranslate icon-scale vs-checkbox--icon  material-icons null">check</i>
                            </span>
                          </span>
                        </div>
                      </td> 
                      <td class="td vs-table--td">{{item.name}}</td>
                      <td class="td vs-table--td text-center">{{item.lms_id}}</td>
                      <td class="td vs-table--td text-center">{{item.done_sessions}}</td>
                      <td class="td vs-table--td text-center">{{item.summary_sessions}}</td>
                      <td class="td vs-table--td text-center"> 
                          <span v-if="item.left_sessions > 0">Đang học</span>
                          <span v-else>Hết số buổi học</span>
                      </td>
                    </tr>
                  </table>
                </div>
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
  import tree from 'vue-jstree'
  
  export default {
    components: {
      datepicker,
      "vue-select": select,
      tree,
    },
    data() {
      return {
        input_disabled: true,
        checked_list: [],
        select_type:1,
        alert:{
          active: false,
          body: '',
          color:'',
        },
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
        reserve:{
          note:'',
          start_date:'',
          end_date:'',
          session:'',
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
        next_schedules:[],
        pre_schedules:[],
        withdraw_id: '',
        modal_join: {
          title: "NỐI PHÍ CHO HỌC SINH",
          show: false,
          color: "info",
          closeOnBackdrop: true,
          error_message:"",
          join_contract:{},
          student_id:""
        },
      }
    },
    computed: {
      selectAll: {
        get: function() {
          return (
            parseInt(this.checked_list.length) === parseInt(this.students.length)
          );
        },
        set: function(value) {
          const selected_list = [];
          if (value) {
            this.students.forEach(student => {
              selected_list.push(student.student_id);
            });
          }
          this.checked_list = selected_list;
        }
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
      selectDate(date){
        if (date) {
          this.reserve.start_date = moment(date).format("YYYY-MM-DD");
          this.getEndDate()
        }
      },
      getEndDate(){
        if(this.reserve.start_date && this.reserve.session){
          this.$vs.loading()
          axios.p("/api/system/get-enddate-in-class",{
            session: this.reserve.session,
            start_date: this.reserve.start_date,
            class_id: this.class_info.class_id
          })
          .then((response) => {
            this.reserve.end_date = response.data
            this.$vs.loading.close();
          })
          .catch((e) => {
            console.log(e);
            this.$vs.loading.close();
          });
        }else{
          this.reserve.end_date = ''
        }
      },
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
            product_id: this.enrol.product_id,
            select_type: this.select_type
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
          this.input_disabled = false
          this.reserve.start_date =''
          this.reserve.end_date =''
          this.reserve.session =''
        } else {
          this.class_info = {};
          this.students= []
          this.class_dates =[]
          this.next_schedules=[]
          this.pre_schedules=[]
          this.input_disabled = true
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
          this.next_schedules = response.data.next_schedules
          this.pre_schedules = response.data.pre_schedules
        })
      },
    },
  }
</script>
<style>
.view-enrolments.con-vs-popup .vs-popup{
  width: 90%;
}

.td.vs-table--td{
  vertical-align: top;
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