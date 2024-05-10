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
          <h5 class="w-full mb-3"><i class="fa-solid fa-file-contract mr-1"></i> Thông tin cấu hình lớp học</h5>
          <div class="vx-row">
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Tên lớp</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="config.title"
                :disabled="disabled_input"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Giáo viên</label>
              <vue-select
                  label="label"
                  placeholder="Chọn giáo viên"
                  :options="html.teachers.list"
                  v-model="html.teachers.item"
                  :searchable="true"
                  language="tv-VN"
                  @input="saveTeacher"
                  :disabled="disabled_input"
                ></vue-select>
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Ca học</label>
              <vue-select
                    label="name"
                    placeholder="Chọn ca học"
                    :options="html.shifts.list"
                    v-model="html.shifts.item"
                    :searchable="true"
                    language="tv-VN"
                    @input="saveShift"
                    :disabled="disabled_input"
                ></vue-select>
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Phòng học</label>
              <vue-select
                    label="label"
                    placeholder="Chọn phòng học"
                    :options="html.rooms.list"
                    v-model="html.rooms.item"
                    :searchable="true"
                    language="tv-VN"
                    @input="saveRoom"
                    :disabled="disabled_input"
                ></vue-select>
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>CM - Giáo viên chủ nhiệm</label>
              <vue-select
                    label="label"
                    placeholder="Chọn giáo viên chủ nhiệm"
                    :options="html.cms.list"
                    v-model="html.cms.item"
                    :searchable="true"
                    language="tv-VN"
                    @input="saveCM"
                    :disabled="disabled_input"
                ></vue-select>
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Trạng thái</label>
              <select class="vs-inputx vs-input--input normal" v-model="config.status" >
                <option value="" disabled>Chọn loại hợp đồng</option>
                <option value="0">Không hoạt động</option>
                <option value="1">Hoạt động</option>
              </select>
            </div>
            <div class="vx-col md:w-3/3 w-full mb-4">
              <label class>Ngày học</label>
               <div class="vx-row mt-2">
                <vs-checkbox v-model="config.class_day.day_2" class="vx-col md:w-1/5 mb-4 pl-2 pr-2" :disabled="disabled_input">Thứ 2</vs-checkbox>
                <vs-checkbox v-model="config.class_day.day_3" class="vx-col md:w-1/5 mb-4 pl-2 pr-2" :disabled="disabled_input">Thứ 3</vs-checkbox>
                <vs-checkbox v-model="config.class_day.day_4" class="vx-col md:w-1/5 mb-4 pl-2 pr-2" :disabled="disabled_input">Thứ 4</vs-checkbox>
                <vs-checkbox v-model="config.class_day.day_5" class="vx-col md:w-1/5 mb-4 pl-2 pr-2" :disabled="disabled_input">Thứ 5</vs-checkbox>
                <vs-checkbox v-model="config.class_day.day_6" class="vx-col md:w-1/5 mb-4 pl-2 pr-2" :disabled="disabled_input">Thứ 6</vs-checkbox>
                <vs-checkbox v-model="config.class_day.day_7" class="vx-col md:w-1/5 mb-4 pl-2 pr-2" :disabled="disabled_input">Thứ 7</vs-checkbox>
                <vs-checkbox v-model="config.class_day.day_8" class="vx-col md:w-1/5 mb-4 pl-2 pr-2" :disabled="disabled_input">Chủ nhật</vs-checkbox>
              </div>
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Ngày bắt đầu học</label>
              <datepicker class="w-full"
                v-model="config.start_date"
                placeholder="Chọn ngày bắt đầu học"
                :lang="datepickerOptions.lang"
                @change="selectDate"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Tổng số buổi học</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="config.session"
                disabled="true"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Số lượng học sinh tối đa</label>
              <input
                class="vs-inputx vs-input--input normal"
                type="text"
                name="title"
                v-model="config.max_students"
                :disabled="disabled_input"
              />
            </div>
            <div class="vx-col md:w-1/2 w-full mb-4">
              <label>Loại lớp học</label>
              <select class="vs-inputx vs-input--input normal" v-model="config.type" :disabled="disabled_input">
                <option value="" disabled>Chọn loại hợp đồng</option>
                <option value="0">Trải nghiệm</option>
                <option value="1">Chính thức</option>
              </select>
            </div>
            <div class="vx-col  md:w-1/2 w-full mb-4">
              <vue-select
                    label="name"
                    placeholder="Chọn môn học"
                    :options="html.subjects.list"
                    v-model="html.subjects.item"
                    :searchable="true"
                    language="tv-VN"
                    :disabled="disabled_input"
                    @input="addSubject"
                ></vue-select>
            </div>
            <div class="vx-col w-full mb-4">
              <vs-tabs>
                <vs-tab label="Danh sách môn học">
                  <div class="tab-text">
                    <div class=w-full>
                      <div class="vs-component vs-con-table stripe vs-table-primary">
                        <div class="con-tablex vs-table--content">
                          <div class="vs-con-tbody vs-table--tbody ">
                            <table class="vs-table vs-table--tbody-table">
                              <thead class="vs-table--thead">
                                <tr>
                                  <!---->
                                  <th colspan="1" rowspan="1" class="text-center">Mã</th>
                                  <th colspan="1" rowspan="1">Môn học</th>
                                  <th colspan="1" rowspan="1" class="text-center">Thứ tự</th>
                                  <th colspan="1" rowspan="1" class="text-center">Số buổi</th>
                                  <th colspan="1" rowspan="1" class="text-center">Thao tác</th>
                                </tr>
                              </thead>
                              <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in config.subjects" :key="index">
                                <td class="td vs-table--td text-center">{{item.code}}</td>
                                <td class="td vs-table--td">{{item.name}}</td>
                                <td class="td vs-table--td text-center">
                                  <input
                                    class="vs-inputx vs-input--input normal"
                                    type="number"
                                    name="title"
                                    style="width: 70px"
                                    v-model="item.stt"
                                  />
                                </td>
                                <td class="td vs-table--td text-center">
                                  <input
                                    class="vs-inputx vs-input--input normal"
                                    type="number"
                                    name="title"
                                    style="width: 70px"
                                    v-model="item.session"
                                    @change="caculatorTotalSession"
                                  />
                                </td>
                                <td class="td vs-table--td text-center list-action"> 
                                  <vs-button size="small" color="danger" @click="deleteSubject(item)"><i class="fa-solid fa-trash"></i></vs-button>
                                </td>
                              </tr>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </vs-tab>
                <vs-tab label="Danh sách buổi học">
                  <div class="tab-text">
                    <div class=w-full>
                      <div class="vs-component vs-con-table stripe vs-table-primary">
                        <div class="con-tablex vs-table--content">
                          <div class="vs-con-tbody vs-table--tbody ">
                            <table class="vs-table vs-table--tbody-table">
                              <thead class="vs-table--thead">
                                <tr>
                                  <!---->
                                  <th colspan="1" rowspan="1" class="text-center">STT</th>
                                  <th colspan="1" rowspan="1" class="text-center">Mã môn học</th>
                                  <th colspan="1" rowspan="1">Môn học</th>
                                  <th colspan="1" rowspan="1" class="text-center">Ngày học</th>
                                  <th colspan="1" rowspan="1" class="text-center">Trạng thái</th>
                                </tr>
                              </thead>
                              <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in list_sessions" :key="index">
                                <td class="td vs-table--td text-center">{{ index + 1 + (pagination.cpage - 1) * pagination.limit }}</td>
                                <td class="td vs-table--td text-center">{{item.subject_code}}</td>
                                <td class="td vs-table--td">{{item.subject_name}}</td>
                                <td class="td vs-table--td text-center">{{item.class_date | formatDateViewDay}}</td>
                                <td class="td vs-table--td text-center">{{item.status_label}}</td>
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
                  </div>
                </vs-tab>
              </vs-tabs>
            </div>
          </div>
          <vs-alert :active.sync="alert.active" class="mb-5" :color="alert.color" closable icon-pack="feather" close-icon="icon-x">
            <div v-html="alert.body"></div>
          </vs-alert>
        </div>
      </div>
      <div class="vx-row mt-5">
        <div class="vx-col w-full text-right">
          <vs-button color="dark" type="border" class="mb-2 mr-3" @click="reload()" >Hủy</vs-button>
          <vs-button class="mb-2" color="success" @click="save()">{{config.is_edit ? 'Cập nhật' : 'Thêm mới'}}</vs-button>
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
        disabled_input: true,
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
          teachers:{
            item: '',
            list: []
          },
          shifts:{
            item: '',
            list: []
          },
          rooms:{
            item: '',
            list: []
          },
          cms:{
            item: '',
            list: []
          },
          subjects:{
            item: '',
            list: []
          }
        },
        config:{
          is_edit:0,
          class_id:'',
          branch_id:'',
          product_id:'',
          session: 0,
          max_students: 16,
          type: 1,
          status: 1,
          start_date:'',
          cm_id:'',
          teacher_id:'',
          shift_id:'',
          room_id:'',
          class_day:{
            day_2: false,
            day_3: false,
            day_4: false,
            day_5: false,
            day_6: false,
            day_7: false,
            day_8: false,
          },
          title:'',
          program_id:'',
          subjects:[],
        },
        alert:{
          active: false,
          body: '',
          color:'',
        },
        list_sessions:[],
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
      axios.g(`/api/system/shifts`)
        .then(response => {
        this.html.shifts.list = response.data
      })
      axios.g(`/api/system/subjects`)
        .then(response => {
        this.html.subjects.list = response.data
      })
    },
    methods: {
      reload(){
        location.reload();
      },
      selectDate(date){
        if (date) {
          this.config.start_date = moment(date).format("YYYY-MM-DD");
        }
      },
      saveBranch(data = null){
        if (data && typeof data === 'object') {
          const branch_id = data.id
          this.config.branch_id = branch_id
        }else{
          this.config.branch_id = ""
        }
        this.loadClasses();
        this.loadRooms();
        this.loadCMs();
        this.loadTeachers();
      },
      saveProduct(data = null){
        if (data && typeof data === 'object') {
          const product_id = data.id
          this.config.product_id = product_id
        }else{
          this.config.product_id = ""
        }
        this.loadClasses();
      },
      saveCM(data = null){
        if (data && typeof data === 'object') {
          const cm_id = data.id
          this.config.cm_id = cm_id
        }else{
          this.config.cm_id = ""
        }
      },
      saveShift(data = null){
        if (data && typeof data === 'object') {
          const shift_id = data.id
          this.config.shift_id = shift_id
        }else{
          this.config.shift_id = ""
        }
      },
      saveTeacher(data = null){
        if (data && typeof data === 'object') {
          const teacher_id = data.id
          this.config.teacher_id = teacher_id
        }else{
          this.config.teacher_id = ""
        }
      },
      saveRoom(data = null){
        if (data && typeof data === 'object') {
          const room_id = data.id
          this.config.room_id = room_id
        }else{
          this.config.room_id = ""
        }
      },
      loadClasses(){
        if(this.config.branch_id && this.config.product_id){
          this.$vs.loading();
          axios.p(`/api/settings/classes/load-classes`, {
            branch_id: this.config.branch_id,
            product_id: this.config.product_id
          })
            .then(response => {
            this.$vs.loading.close();
            this.classes = response.data
          })
        }else{
          this.classes =[]
        }
      },
      loadRooms(){
        if(this.config.branch_id){
          this.$vs.loading();
          axios.g(`/api/system/rooms/${this.config.branch_id}`)
            .then(response => {
            this.$vs.loading.close();
            this.html.rooms.list = response.data
          })
        }else{
          this.html.rooms.list =[]
        }
      },
      loadCMs(){
        if(this.config.branch_id){
          this.$vs.loading();
          axios.g(`/api/system/cms/${this.config.branch_id}`)
            .then(response => {
            this.$vs.loading.close();
            this.html.cms.list = response.data
          })
        }else{
          this.html.cms.list =[]
        }
      },
      loadTeachers(){
        if(this.config.branch_id){
          this.$vs.loading();
          axios.g(`/api/system/teachers/${this.config.branch_id}`)
            .then(response => {
            this.$vs.loading.close();
            this.html.teachers.list = response.data
          })
        }else{
          this.html.teachers.list =[]
        }
      },
      selectClass(selected_class) {
        this.disabled_input = false
        if (selected_class.model.item_type === 'class') {
          this.config.is_edit=1
          this.$vs.loading();
          axios.g(`/api/settings/classes/info-config/${selected_class.model.item_id}`)
            .then(response => {
            this.$vs.loading.close();
            this.config = response.data
            this.html.teachers.item = this.html.teachers.list.filter(item => item.id == response.data.teacher_id)[0]
            this.html.cms.item = this.html.cms.list.filter(item => item.id == response.data.cm_id)[0]
            this.html.rooms.item = this.html.rooms.list.filter(item => item.id == response.data.room_id)[0]
            this.html.shifts.item = this.html.shifts.list.filter(item => item.id == response.data.shift_id)[0]
            this.getDataSessions();
          })
        } else {
          this.config.program_id = selected_class.model.item_id
          this.config.is_edit=0
          this.resetInput();
          this.list_sessions=[]
        }
      },
      resetInput(){
        this.config.class_day.day_2 = false
        this.config.class_day.day_3 = false
        this.config.class_day.day_4 = false
        this.config.class_day.day_5 = false
        this.config.class_day.day_6 = false
        this.config.class_day.day_7 = false
        this.config.class_day.day_8 = false
        this.config.session= 0
        this.config.max_students= 16
        this.config.type= 1
        this.config.start_date=''
        this.config.cm_id=''
        this.config.teacher_id=''
        this.config.shift_id=''
        this.config.room_id=''
        this.config.title=''
        this.html.rooms.item = ''
        this.html.teachers.item = ''
        this.html.cms.item = ''
        this.html.shifts.item = ''
        this.config.subjects=[]
      },
      save() {
        let mess = "";
        let resp = true;
        if (this.config.branch_id == "") {
          mess += " - Trung tâm không được để trống<br/>";
          resp = false;
        }
        if (this.config.product_id == "") {
          mess += " - Khóa học không được để trống<br/>";
          resp = false;
        }
        if (this.config.program_id == "") {
          mess += " - Chương trình học không được để trống<br/>";
          resp = false;
        }
        if (this.config.title == "") {
          mess += " - Tên lớp không được để trống<br/>";
          resp = false;
        }
        if (this.config.teacher_id == "") {
          mess += " - Giáo viên không được để trống<br/>";
          resp = false;
        }
        if (this.config.shift_id == "") {
          mess += " - Ca học không được để trống<br/>";
          resp = false;
        }
        if (this.config.room_id == "") {
          mess += " - Phòng học không được để trống<br/>";
          resp = false;
        }
        if (this.config.cm_id == "") {
          mess += " - CM - Giáo viên chủ nhiệm không được để trống<br/>";
          resp = false;
        }
        if (this.config.start_date == "") {
          mess += " - Ngày bắt đầu học không được để trống<br/>";
          resp = false;
        }
        if (this.config.session == "") {
          mess += " - Số buổi học không được để trống<br/>";
          resp = false;
        }
        if (!resp) {
          this.alert.color = 'danger'
          this.alert.body = mess;
          this.alert.active = true;
          return false;
        }
        this.$vs.loading()
        axios.p("/api/settings/classes/save",this.config)
        .then((response) => {
          this.$vs.loading.close();
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          this.config.is_edit=0
          this.resetInput();
          this.loadClasses()
        })
        .catch((e) => {
          console.log(e);
          this.$vs.loading.close();
        });
      },
      addSubject(data =null){
        if (data && typeof data === 'object') {
          const check_exit = 0;
          this.config.subjects.map(item => {
            if(item.id==data.id){
              check_exit = 1;
            }
          })
          if(!check_exit){
            this.config.subjects.push(data)
          }
          this.html.subjects.item=''
          this.caculatorTotalSession();
        }
      },
      deleteSubject(data){
        const ids_subject = []
        this.config.subjects.map(item => {
          if(data.id != item.id){
            ids_subject.push(item)
          }
        })
        this.config.subjects = ids_subject
        this.caculatorTotalSession();
      },
      caculatorTotalSession(){
        this.config.session = 0
        this.config.subjects.map(item => {
          this.config.session = Number(this.config.session) + Number(item.session)
        })
      },
      getDataSessions() {
        const data = {
            class_id : this.config.class_id,
            pagination:this.pagination,
          }

        this.$vs.loading()
        axios.p('/api/settings/classes/sessions', data)
          .then((response) => {
            this.$vs.loading.close()
            this.list_sessions = response.data.list
            this.pagination = response.data.paging;
            setTimeout(() => {
              this.pagination.init = 1;
            }, 500)
          })
          .catch((error) => {
            console.log(error);
            this.$vs.loading.close();
          })
      },
      changePage() {
        if (this.pagination.init) {
          this.getDataSessions();
        }
      },
      changePageLimit(limit) {
        this.pagination.cpage = 1
        this.pagination.limit = limit
        this.getDataSessions();
      },
    },
  }
</script>