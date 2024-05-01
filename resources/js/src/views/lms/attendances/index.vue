<template>

  <div id="page-users-list">
    <vx-card no-shadow class="mt-5">
      <div class="vx-row">
        <div class="vx-col md:w-1/3 w-full mb-4">
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
        <div class="vx-col md:w-1/3 w-full mb-4">
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
        <div class="vx-col md:w-1/3 w-full mb-4">
          <label>Chương trình học</label>
          <vue-select
                label="name"
                placeholder="Chọn khóa học"
                :options="html.programs.list"
                v-model="html.programs.item"
                :searchable="true"
                language="tv-VN"
                @input="saveProgram"
            ></vue-select>
        </div>
        <div class="vx-col md:w-1/3 w-full mb-4">
          <label>Lớp học</label>
          <vue-select
                label="cls_name"
                placeholder="Chọn lớp học"
                :options="html.classes.list"
                v-model="html.classes.item"
                :searchable="true"
                language="tv-VN"
                @input="saveClass"
            ></vue-select>
        </div>
        <div class="vx-col md:w-1/3 w-full mb-4">
          <label>Chọn tháng</label>
           <datepicker class="w-full"
                v-model="att.date_select"
                placeholder="Chọn tháng điểm danh"
                :lang="datepickerOptions.lang"
                :format="'YYYY-MM'"
                type="month"
                @change="changeMonth"
              />
        </div>
        <div class="vx-col w-full item-last" v-if="class_info.class_id">
          <div style="background: #72ae7517; padding: 10px; font-size: 16px;">
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
                  <span>{{class_info.cls_startdate}} - {{class_info.cls_enddate}}</span>
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
                  <span>Ca học:</span>
                </div>
                <div class="vx-col md:w-2/3 w-full text-left">
                  <span>{{class_info.shift_text}}</span>
                </div>
            </div>
            <div class="vx-row">
                <div class="vx-col md:w-1/3 w-full text-right">
                  <span>Phòng học:</span>
                </div>
                <div class="vx-col md:w-2/3 w-full text-left">
                  <span>{{class_info.room_text}}</span>
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

          <div class="vs-component vs-con-table stripe vs-table-primary">
            <div class="con-tablex vs-table--content">
              <div class="vs-con-tbody vs-table--tbody ">
                <table class="vs-table vs-table--tbody-table">
                  <thead class="vs-table--thead">
                    <tr>
                      <th colspan="1" rowspan="1" class="text-center">STT</th>
                      <th colspan="1" rowspan="1">Tên học sinh</th>
                      <th colspan="1" rowspan="1" class="text-center">
                        Mã học sinh
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
        alert:{
          active: false,
          body: '',
          color:'',
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
          programs:{
            item: '',
            list: []
          },
          classes:{
            item: '',
            list: []
          }
        },
        att:{
          branch_id:'',
          product_id:'',
          program_id:'',
          class_id:'',
          date_select:moment(new Date()).format('YYYY-MM')
        },
        students:[],
        class_info:{}
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
          this.att.branch_id = branch_id
          this.loadClasses();
        }else{
          this.att.branch_id = ""
        }
      },
      saveProduct(data = null){
        if (data && typeof data === 'object') {
          const product_id = data.id
          this.att.product_id = product_id
          this.loadPrograms();
        }else{
          this.att.product_id = ""
          this.html.programs.list =[]
        }
      },
      saveProgram(data = null){
        if (data && typeof data === 'object') {
          const program_id = data.id
          this.att.program_id = program_id
          this.loadClasses();
        }else{
          this.att.program_id = ""
        }
      },
      saveClass(data = null){
        if (data && typeof data === 'object') {
          const class_id = data.id
          this.att.class_id = class_id
          this.loadStudents()
        }else{
          this.att.class_id = ""
        }
      },
      loadPrograms(){
        if(this.att.product_id){
          axios.g(`/api/system/programs/${this.att.product_id}`)
            .then(response => {
            this.html.programs.list = response.data
          })
        }else{
          this.html.programs.list =[]
        }
      },
      loadClasses(){
        if(this.att.branch_id && this.att.program_id){
          this.$vs.loading();
          axios.p(`/api/lms/attendances/load-classes`, {
            branch_id: this.att.branch_id,
            program_id: this.att.program_id
          })
            .then(response => {
            this.$vs.loading.close();
            this.html.classes.list = response.data
          })
        }else{
          this.html.classes.list =[]
        }
      },
      changeMonth(date) {
        this.att.date_select = moment(date).format('YYYY-MM')
        this.loadStudents()
      },
      loadStudents(){
        console.log(this.att.date_select)
        if(this.att.class_id && this.att.date_select){
          this.$vs.loading();
          axios.p(`/api/lms/attendances/load-students`, {
            class_id: this.att.class_id,
            date_select: this.att.date_select
          })
            .then(response => {
            this.$vs.loading.close();
            this.class_info = response.data.class_info
            this.students = response.data.students
          })
        }else{
          this.class_info ={}
          this.students=[]
        }
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