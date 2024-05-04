

<template>

  <div id="page-roles-list">
    <vx-card no-shadow class="mt-5">
      <div class="mb-5">
        <div class="vx-row">
          <div class="vx-col sm:w-1/4 w-full mb-4">
            <label for="" class="vs-input--label">Trung tâm</label>
            <multiselect
                name="search_branch"
                placeholder="Chọn trung tâm checkin"
                v-model="searchData.arr_branch"
                :options="branch_list"
                label="name"
                :close-on-select="false"
                :hide-selected="true"
                :multiple="true"
                :searchable="true"
                track-by="id"
                selectedLabel="" selectLabel="" deselectLabel=""
              >
                <span slot="noResult">Không tìm thấy dữ liệu</span>
              </multiselect>
          </div>
          <div class="vx-col sm:w-1/4 w-full mb-4">
            <label for="" class="vs-input--label">Từ khóa</label>
            <vs-input class="w-full" placeholder="Tên phụ huynh, số điện thoại" v-model="searchData.keyword"></vs-input>
          </div>
          <div class="vx-col sm:w-1/4 w-full mb-4">
            <label for="" class="vs-input--label">Trạng thái</label>
            <multiselect
                name="search_status"
                placeholder="Chọn trạng thái"
                v-model="searchData.arr_status"
                :options="statusOptions"
                label="label"
                :close-on-select="false"
                :hide-selected="true"
                :multiple="true"
                :searchable="true"
                track-by="id"
                selectedLabel="" selectLabel="" deselectLabel=""
              >
                <span slot="noResult">Không tìm thấy dữ liệu</span>
              </multiselect>
          </div>
          <div class="vx-col sm:w-1/4 w-full mb-4">
            <label for="" class="vs-input--label">Người phụ trách</label>
            <multiselect
                name="search_owner"
                placeholder="Chọn người phụ trách"
                v-model="searchData.arr_creator"
                :options="users_list"
                label="label_name"
                :close-on-select="false"
                :hide-selected="true"
                :multiple="true"
                :searchable="true"
                track-by="id"
                selectedLabel="" selectLabel="" deselectLabel=""
              >
                <span slot="noResult">Không tìm thấy dữ liệu</span>
              </multiselect>
          </div>
          
          <div class="vx-col sm:w-1/4 w-full mb-4">
            <label for="" class="vs-input--label">Nguồn</label>
            <multiselect
                name="search_source"
                placeholder="Chọn nguồn"
                select-label="Chọn nguồn"
                v-model="searchData.arr_source"
                :options="source_list"
                label="name"
                :close-on-select="false"
                :hide-selected="true"
                :multiple="true"
                :searchable="true"
                track-by="id"
                selectedLabel="" selectLabel="" deselectLabel=""
              >
                <span slot="noResult">Không tìm thấy dữ liệu</span>
              </multiselect>
          </div>
          
          <div class="vx-col sm:w-1/4 w-full mb-4">
            <label for="" class="vs-input--label">Thời gian đặt lịch checkin</label>
            <date-picker name="item-date" v-model="searchData.dateRange" range format="YYYY-MM-DD" style="width: 100%"
              :clearable="true" :lang="datepickerOptions.lang" placeholder="Chọn khoảng thời gian tìm kiếm"></date-picker>
          </div>
        </div>
        <div class="vx-row mt-3">
          <div class="vx-col w-full">
            <vs-button class="mr-3 mb-2" @click="getData"><i class="fa fa-search"></i> Tìm kiếm</vs-button>
            <vs-button color="dark" type="border" class="mb-2" @click="reset" ><i class="fas fa-undo-alt"></i> Hủy</vs-button>
          </div>
        </div>
      </div>

      <div class="vs-component vs-con-table stripe vs-table-primary">
        <div class="con-tablex vs-table--content">
          <div class="vs-con-tbody vs-table--tbody ">
            <table class="vs-table vs-table--tbody-table">
              <thead class="vs-table--thead">
                <tr>
                  <!---->
                  <th colspan="1" rowspan="1" class="text-center">
                    <div class="vs-table-text text-center">STT
                      <!---->
                    </div>
                  </th>
                  <th colspan="1" rowspan="1">
                    <div class="vs-table-text">Học sinh
                      <!---->
                    </div>
                  </th>
                  <th colspan="1" rowspan="1">
                    <div class="vs-table-text">Phụ huynh
                      <!---->
                    </div>
                  </th>
                  <th colspan="1" rowspan="1">
                    <div class="vs-table-text">Thông tin checkin
                      <!---->
                    </div>
                  </th>
                  <th colspan="1" rowspan="1" class="text-center">
                    <div class="vs-table-text">Trạng thái
                      <!---->
                    </div>
                  </th>
                  <th colspan="1" rowspan="1" class="text-center">
                    <div class="vs-table-text">Thao tác
                      <!---->
                    </div>
                  </th>
                </tr>
              </thead>
              <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in checkin_students" :key="index">
                <!---->
                
                <td class="td vs-table--td">{{ index + 1 + (pagination.cpage - 1) * pagination.limit }}</td>
                <td class="td vs-table--td">
                  <p><strong>{{ item.name }}</strong></p>
                  <p>Giới tính: {{ item.gender == 'F' ? 'Nữ' : 'Nam' }}</p>
                  <p>Ngày sinh: {{ item.birthday }}</p>
                </td>
                <td class="td vs-table--td">
                  <p><strong>{{ item.parent_name }}</strong></p>
                  <p>SĐT: {{ item.mobile_1 }}</p>
                  <p>Nguồn: {{ item.source_name }}</p>
                  <p>Phụ trách: {{ item.checkin_owner_name }}</p>
                </td>
                <td class="td vs-table--td">
                  <p>{{ item.checkin_branch_name }}</p>
                  <p>Thời gian: {{ item.checkin_at }}</p>
                </td>
                <td class="td vs-table--td text-center">{{ item.status | getStatusName}}</td>
                <td class="text-center list-action"> 
                    <vs-button size="small" color="success" v-if="item.status==1" @click="showModalCheckin(item.id)"><i class="fa-solid fa-clipboard-check"></i></vs-button>
                    <vs-button size="small" color="danger" v-if="item.status==2" @click="openConfirmUpStudent(item.id,item.name)"><i class="fa-solid fa-paper-plane"></i></vs-button>
                </td>
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
    </vx-card>
    <vs-popup :class="'modal_'+ modal_checkin.color" :title="modal_checkin.title" :active.sync="modal_checkin.show">
      <div class="vx-row"> 
        <div class="vx-col w-full mb-4">
          <label>Thời gian đến checkin <span class="text-danger"> (*)</span></label>
          <date-picker
                    id="checkin-at"
                    :value="modal_checkin.checkined_at"
                    v-model="modal_checkin.checkined_at"
                    placeholder="Chọn ngày giờ"
                    :lang="datepickerOptions.lang"
                    type="datetime"
                    format="YYYY-MM-DD HH:mm"
                    style="width: 100%"
            >
          </date-picker>
        </div>
        <div class="vx-col w-full mb-4">
          <label>Ghi chú <span class="text-danger"> (*)</span></label>
          <textarea class="vs-inputx vs-input--input normal" v-model="modal_checkin.note"></textarea>
        </div>
        <vs-alert :active.sync="modal_checkin.alert.active" class="mb-5  mr-4 ml-4" :color="modal_checkin.alert.color" closable icon-pack="feather" close-icon="icon-x">
          <div v-html="modal_checkin.alert.body"></div>
        </vs-alert>
        <div class="vx-col w-full">
          <vs-button color="dark" type="border" class="mr-3" @click="modal_checkin.show = false">Hủy</vs-button>
          <vs-button color="success" @click="updateCheckin">Lưu</vs-button>
        </div>
      </div>
    </vs-popup>
  </div>

</template>

<script>

  import vSelect from 'vue-select'
  import axios from '../../../http/axios.js'
  import Multiselect from "vue-multiselect";
  import DatePicker from "vue2-datepicker";
  import u from '../../../until/helper.js'

  export default {
    components: { 
      vSelect,
      Multiselect,
      DatePicker
    },
    data() {
      return {
        modal_checkin: {
          title: "XÁC NHẬN CHECKIN",
          show: false,
          color: "info",
          closeOnBackdrop: false,
          size:"lg",
          error_message:"",
          alert:{
            active: false,
            body: '',
            color:'',
          },
          student_id:'',
          checkined_at:'',
          note:''
        },
        branch_list: [],
        searchData: {
          arr_branch: "",
          branch_id:"",
          keyword: "",
          arr_status: "",
          arr_creator: "",
          arr_source: "",
          status: "",
          checkin_owner_id: "",
          source_id: "",
          pagination: this.pagination,
          dateRange: "",
        },
        statusOptions:[
          {id:1,label:'Chưa đến checkin'},
          {id:2,label:'Đã checkin'},
          {id:3,label:'Đã lên chính thức'},
        ],
        users_list:[],
        source_list:[],
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

        checkin_students: [],
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
        checkin_owner_id:"",
        up_student_id:"",
      }
    },
    created() {
      axios.g(`/api/system/branches-has-user`)
        .then(response => {
        this.branch_list = response.data
      })
      axios.g(`/api/users/get-data/all`)
        .then(response => {
        this.users_list = response.data
      })
      axios.g(`/api/system/sources`)
        .then(response => {
        this.source_list = response.data
      })
      this.getData();
    },
    methods: {
      reset() {
        this.searchData.keyword = ""
        this.searchData.arr_status= ""
        this.searchData.arr_creator= ""
        this.searchData.arr_source= ""
        this.searchData.arr_branch= ""
        this.searchData.branch_id= ""
        this.searchData.status= ""
        this.searchData.checkin_owner_id= ""
        this.searchData.source_id= ""
        this.searchData.pagination= this.pagination
        this.searchData.dateRange= ""
        this.getData();
      },
      getData() {
        const startDate = typeof this.searchData.dateRange != 'undefined' && this.searchData.dateRange!='' && this.searchData.dateRange[0] ?`${u.dateToString(this.searchData.dateRange[0])}`:''
        const endDate = typeof this.searchData.dateRange != 'undefined' && this.searchData.dateRange!='' && this.searchData.dateRange[1] ?`${u.dateToString(this.searchData.dateRange[1])}`:''
        const ids = []
        if (this.searchData.arr_status && this.searchData.arr_status.length) {
          this.searchData.arr_status.map(item => {
            ids.push(item.id)
          })
        }
        this.searchData.status = ids
        
        const ids_branch = []
        if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
          this.searchData.arr_branch.map(item => {
            ids_branch.push(item.id)
          })
        }
        this.searchData.branch_id = ids_branch

        const ids_creator = []
        if (this.searchData.arr_creator && this.searchData.arr_creator.length) {
          this.searchData.arr_creator.map(item => {
            ids_creator.push(item.id)
          })
        }
        this.searchData.checkin_owner_id = ids_creator

        const ids_source = []
        if (this.searchData.arr_source && this.searchData.arr_source.length) {
          this.searchData.arr_source.map(item => {
            ids_source.push(item.id)
          })
        }
        this.searchData.source_id = ids_source

        const data = {
            keyword: this.searchData.keyword,
            checkin_branch_id: this.searchData.branch_id,
            status: this.searchData.status,
            checkin_owner_id: this.searchData.checkin_owner_id,
            source_id: this.searchData.source_id,
            start_date:startDate,
            end_date:endDate,
            pagination:this.pagination,
            type_seach:this.searchData.type_seach,
          }

        this.$vs.loading()
        axios.p('/api/lms/checkin/list', data)
          .then((response) => {
            this.$vs.loading.close()
            this.checkin_students = response.data.list
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

      openConfirmUpStudent (id, name) {
        this.up_student_id = id
        this.$vs.dialog({
          type: 'confirm',
          color: 'danger',
          title: 'Thông báo',
          text: `Bạn chắc chắn muốn chuyển học sinh "${name}" lên danh sách chính thức?`,
          accept: this.upStudent,
          acceptText: 'Lưu',
          cancelText: 'Hủy'
        })
      },
      upStudent(){
        const data = {
          crm_student_id: this.up_student_id,
        };
        this.$vs.loading();
        axios.p(`/api/lms/checkin/upgrade`,data)
        .then((response) => {
          this.$vs.loading.close();
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          this.$router.push(`/lms/students/${response.data.lms_student_id}/detail`)
        })
      },

      showModalCheckin(id){
        this.modal_checkin.show = true
        this.modal_checkin.error_message=""
        this.modal_checkin.student_id=id
        this.modal_checkin.note=""
        this.modal_checkin.checkined_at=""
      },
      updateCheckin(){
        let mess = "";
        let resp = true;
        if (this.modal_checkin.checkined_at == "") {
          mess += " - Thời gian đến checkin không được để trống<br/>";
          resp = false;
        }
        if (this.modal_checkin.note == "") {
          mess += " - Ghi chú không được để trống<br/>";
          resp = false;
        }
        if (!resp) {
          this.modal_checkin.alert.color = 'danger'
          this.modal_checkin.alert.body = mess;
          this.modal_checkin.alert.active = true;
          return false;
        }
        this.$vs.loading();
        this.modal_checkin.show = false
        axios.p(`/api/lms/checkin/student/checked`,{
          student_id: this.modal_checkin.student_id,
          note: this.modal_checkin.note,
          checkined_at: this.modal_checkin.checkined_at
        })
        .then((response) => {
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          this.$vs.loading.close();
          this.getData();
        })
        .catch((e) => {
        });
      },
    },
    
    filters: {
      getStatusName(value) {
        let resp = ''
        switch (Number(value)) {
            case 2:
                resp = 'Đã checkin';
                break;
            case 3:
                resp = 'Đã lên chính thức';
                break;
            default:
                resp = 'Chưa đến checkin'
                break
        }
        return resp
      },
    },
  }
</script>
<style>
@media only screen and (min-width: 600px) {
  #page-roles-list .vs-table--search {
    max-width: 360px;
  }
  #page-roles-list .vs-table--search-input{
    width: 360px;
  }
}
th .sort-th, th .vs-table-text{
  display: contents;
}
.multiselect{
  z-index: 999;
}
</style>