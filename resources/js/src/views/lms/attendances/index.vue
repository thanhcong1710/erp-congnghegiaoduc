<template>

  <div id="page-attendances">
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
          <div style="background: #72ae7517; padding: 10px; font-size: 14px;">
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
                  <span>AF - Quản lý lớp học:</span>
                </div>
                <div class="vx-col md:w-2/3 w-full text-left">
                  <span>{{class_info.cm_name}}</span>
                </div>
            </div>
          </div>

          <div class="vs-component vs-con-table stripe vs-table-primary mt-5">
            <div class="con-tablex vs-table--content">
              <div class="vs-con-tbody vs-table--tbody ">
                <table class="vs-table vs-table--tbody-table">
                  <thead class="vs-table--thead">
                    <tr>
                      <th colspan="1" rowspan="2" class="text-center">STT</th>
                      <th colspan="1" rowspan="2" style="min-width:126px">Học sinh</th>
                      <th :colspan="shedules.length" rowspan="1" class="text-center">{{att.date_select}}</th>
                    </tr>
                    <tr>
                      <th colspan="1" rowspan="1" class="text-center" v-for="(item, index) in shedules" :key="index">
                        <p style="font-weight: normal">{{item.code}} - buổi {{item.subject_stt}}</p>
                        <p style="font-weight: normal; font-size: 11px">{{item.class_date | formatDateViewDay}}</p>
                      </th>
                    </tr>
                  </thead>
                  <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in students" :key="index">
                    <td class="td vs-table--td text-center">{{index+1}}</td> 
                    <td class="td vs-table--td">
                      <p>{{item.name}}</p>
                      <p>Mã: {{item.lms_code}}</p> 
                    </td> 
                    <td class="td vs-table--td text-center" v-for="(att, index_att) in item.attendances" :key="index_att">
                       <select v-if="att.id" class="input-attendance vs-inputx vs-input--input normal" :disabled="att.attendance_status ==2 || att.attendance_status ==3" v-model="att.attendance_status" @change="save(att, item)">
                          <option value="0">Chưa điểm danh</option>
                          <option value="1">Đi học</option>
                          <option value="2">Học bù</option>
                          <option value="3">Đã học bù</option>
                        </select>
                        <p v-if="att.to_class_name">{{att.to_class_name}}</p>
                        <p v-if="att.to_class_name">({{att.to_class_date | formatDateViewDay}})</p>
                        <p v-if="att.to_class_status==2 || att.to_class_status==3">
                          <span :class="att.to_class_status==2? 'text-success' : 'text-danger'">{{att.to_class_status==2 ? 'Đã học bù' :'Không đến học bù'}}</span>
                        </p>
                    </td>
                  </tr>
                </table>
              </div>
            </div>
          </div>
          <div v-if="make_up_sessions.length > 0">
            <vs-divider/>
            <h4>Đăng ký học bù</h4>
            <div class="vs-component vs-con-table stripe vs-table-primary mt-5">
              <div class="con-tablex vs-table--content">
                <div class="vs-con-tbody vs-table--tbody ">
                  <table class="vs-table vs-table--tbody-table">
                    <thead class="vs-table--thead">
                      <tr>
                        <th colspan="1" rowspan="1" class="text-center">STT</th>
                        <th colspan="1" rowspan="1" class="text-center">Mã học sinh</th>
                        <th colspan="1" rowspan="1" class="text-center">Tên học sinh</th>
                        <th colspan="1" rowspan="1" class="text-center">Ngày học bù</th>
                        <th colspan="1" rowspan="1" class="text-center">Trạng thái</th>
                      </tr>
                    </thead>
                    <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in make_up_sessions" :key="index">
                      <td class="td vs-table--td text-center">{{index+1}}</td> 
                      <td class="td vs-table--td text-center">{{item.lms_code}}</td>
                      <td class="td vs-table--td text-center">{{item.name}}</td>
                       <td class="td vs-table--td text-center">{{item.to_class_date | formatDateViewDay}}</td>
                      <td class="td vs-table--td text-center" style="width: 146px">
                        <select v-if="item.status==1" class="input-attendance vs-inputx vs-input--input normal"  v-model="item.status" @change="saveMakeUpSessions(item)">
                            <option value="1">Đăng ký học bù</option>
                            <option value="2">Đã học bù</option>
                            <option value="3">Không đi học bù</option>
                          </select>
                          <span v-if="item.status==2 || item.status==3">{{item.status==2?'Đã học bù' : 'Không đi học bù'}}</span>
                      </td>
                    </tr>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <vs-popup :class="'modal_'+ modal_re_session.color" :title="modal_re_session.title" :active.sync="modal_re_session.show">
        <div class="vx-row" > 
          <div class="vx-col w-full mb-4">
            <p>Học sinh: {{modal_re_session.student_name}}</p>
            <p>Buổi nghỉ: {{modal_re_session.label_session}} ({{modal_re_session.class_date | formatDateViewDay}})</p>
          </div>
          <div class="vx-col w-full mb-4">
            <vue-select
                label="cls_name"
                placeholder="Chọn lớp học bù"
                :options="html.re_classes.list"
                v-model="html.re_classes.item"
                :searchable="true"
                language="tv-VN"
                @input="saveReClass"
            ></vue-select>
          </div>
          <div class="vx-col w-full mb-4">
            <vue-select
                label="session_label"
                placeholder="Chọn buổi học bù"
                :options="html.re_sessions.list"
                v-model="html.re_sessions.item"
                :searchable="true"
                language="tv-VN"
                @input="saveReSession"
            ></vue-select>
          </div>
        </div>
        <vs-alert :active.sync="alert.active" class="mb-5" :color="alert.color" closable icon-pack="feather" close-icon="icon-x">
         <div v-html="alert.body"></div>
        </vs-alert>
        <div class="vx-col w-full">
          <vs-button color="dark" type="border" class="mr-3" @click="cancelReSession()">Hủy</vs-button>
          <vs-button color="success" @click="addReSession()">Lưu</vs-button>
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
        modal_re_session: {
          title: "ĐĂNG KÝ HỌC BÙ",
          show: false,
          color: "info",
          closeOnBackdrop: true,
          error_message:"",
          student_name:"",
          label_session:"",
          class_date:"",
          to_class_id:"",
          to_class_date:"",
          to_schedule_id:"",
          schedule_student_id:"",
          student_id:"",
        },
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
          },
          re_classes:{
            item: '',
            list: []
          },
          re_sessions:{
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
        class_info:{},
        shedules:[],
        make_up_sessions:[],
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
            this.html.re_classes.list = response.data
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
            this.shedules = response.data.shedules
            this.make_up_sessions = response.data.make_up_sessions
          })
        }else{
          this.class_info ={}
          this.students=[]
        }
      },
      save(data, student){
        if(data.attendance_status ==2){
          this.modal_re_session.show = true
          this.modal_re_session.student_name = student.name + ' - ' + student.lms_code
          this.modal_re_session.label_session = data.subject_code + ' - buổi ' + data.subject_stt
          this.modal_re_session.class_date =  data.class_date
          this.modal_re_session.student_id = student.student_id
          this.modal_re_session.schedule_student_id = data.id
        }else{
          this.$vs.loading();
          axios.p(`/api/lms/attendances/save`, data)
            .then(response => {
            this.$vs.loading.close();
            this.$vs.notify({
              title: 'Thành Công',
              text: response.data.message,
              color: 'success',
              iconPack: 'feather',
              icon: 'icon-check'
            })
          })
        }
      },
      saveReClass(data = null){
        if (data && typeof data === 'object') {
          const to_class_id = data.id
          this.modal_re_session.to_class_id = to_class_id
          this.loadReSessions()
        }else{
          this.modal_re_session.to_class_id = ""
        }
      },
      loadReSessions(){
        this.$vs.loading();
        axios.g(`/api/lms/attendances/loadReSessions/${this.modal_re_session.to_class_id}`)
          .then(response => {
          this.$vs.loading.close();
          this.html.re_sessions.list = response.data
        })
      },
      saveReSession(data = null){
        if (data && typeof data === 'object') {
          const to_schedule_id = data.id
          this.modal_re_session.to_class_date = data.class_date
          this.modal_re_session.to_schedule_id = to_schedule_id
        }else{
          this.modal_re_session.to_class_date = ""
          this.modal_re_session.to_schedule_id = ""
        }
      },
      cancelReSession(){
        this.modal_re_session.show = false
        this.loadStudents();
      },
      addReSession(){
        let mess = "";
        let resp = true;
        if (!this.modal_re_session.to_class_id) {
          mess += " - Lớp học bù không để trống<br/>";
          resp = false;
        }
        if (!this.modal_re_session.to_schedule_id) {
          mess += " - Buổi học bù không để trống<br/>";
          resp = false;
        }
        if(this.modal_re_session.to_class_id == this.att.class_id){
          mess += " - Lớp học bù phải khác lớp đang học<br/>";
          resp = false;
        }
        if (!resp) {
          this.alert.color = 'danger'
          this.alert.body = mess;
          this.alert.active = true;
          return false;
        }
        this.$vs.loading();
        axios.p(`/api/lms/attendances/addReSession`, {
          'student_id' : this.modal_re_session.student_id,
          'schedule_student_id' : this.modal_re_session.schedule_student_id,
          'to_class_id': this.modal_re_session.to_class_id,
          'to_class_date': this.modal_re_session.to_class_date
        })
          .then(response => {
          this.$vs.loading.close();
          this.modal_re_session.show =false
          this.loadStudents();
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
        })
      },
      saveMakeUpSessions(data){
        this.$vs.loading();
        axios.p(`/api/lms/attendances/updateReSession`, data)
          .then(response => {
          this.$vs.loading.close();
          this.loadStudents();
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
        })
      }
    },
  }
</script>
<style>
.td.vs-table--td{
  vertical-align: top;
}

[dir] #page-attendances .vs-con-table .vs-con-tbody .vs-table--tbody-table .vs-table--thead th{
  border: 1px solid #ccc;
  padding: 5px 8px;
}
[dir] #page-attendances .vs-con-table .vs-con-tbody .vs-table--tbody-table .tr-values .vs-table--td{
  border: 1px solid #ccc;
  padding: 5px 8px;
}
[dir] #page-attendances .input-attendance.vs-input--input.normal{
  padding: 2px !important;
  font-size: 12px !important;
}
 #page-attendances .vs-con-table .vs-con-tbody .vs-table--tbody-table{
  font-size: 12px;
}
</style>