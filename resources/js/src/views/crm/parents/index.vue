

<template>

  <div id="page-roles-list">
    <vx-card no-shadow class="mt-5">
      <div class="mb-5">
        <div class="vx-row">
          <div class="vx-col sm:w-1/4 w-full mb-4">
            <label for="" class="vs-input--label">Từ khóa</label>
            <vs-input class="w-full" placeholder="Tên khách hàng, số điện thoại" v-model="searchData.keyword"></vs-input>
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
                v-model="searchData.arr_owner"
                :options="users_manager_list"
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
            <label for="ccmonth">Nguồn</label>
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
            <label for="" class="vs-input--label">Nguồn chi tiết</label>
            <multiselect
                name="search_source_detail"
                placeholder="Chọn nguồn chi tiết"
                v-model="searchData.arr_source_detail"
                :options="source_detail_list"
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
            <label for="" class="vs-input--label">Lịch chăm sóc tiếp theo</label>
            <date-picker name="item-date" v-model="searchData.dateRange" range format="YYYY-MM-DD" style="width: 100%"
              :clearable="true" :lang="datepickerOptions.lang" placeholder="Chọn khoảng thời gian tìm kiếm"></date-picker>
          </div>
        </div>
        <div class="vx-row mt-3">
          <div class="vx-col w-full">
            <router-link class="btn btn-success" :to="'/crm/parent/add'">
              <vs-button class="mr-3 mb-2" color="success"><i class="fa fa-plus"></i> Thêm mới</vs-button>
            </router-link>  
            <vs-button class="mr-3 mb-2" @click="getData"><i class="fa fa-search"></i> Tìm kiếm</vs-button>
            <vs-button color="dark" type="border" class="mb-2" @click="reset" ><i class="fas fa-undo-alt"></i> Hủy</vs-button>
          </div>
        </div>
      </div>
      <div class="form-group col-sm-12" v-if="checked_list.length>0">
        <p style="text-align: end">Bạn đã lựa chọn <b>{{checked_list.length}}</b> khách hàng   
        <vs-button  style="margin-left:30px;" @click="showModalAssgin" color="danger"><i class="fa-solid fa-paper-plane mr-2"></i>Bàn giao</vs-button></p>
      </div>

      <vs-tabs v-model="activeItem">
        <vs-tab :label="'Tất cả ('+ total.total_0 +')'" @click.prevent="setActive(0)" ></vs-tab>
        <vs-tab :label="'Chưa chăm sóc ('+ total.total_1 +')'" @click.prevent="setActive(1)"></vs-tab>
        <vs-tab :label="'Lịch chăm sóc trong ngày ('+ total.total_2 +')'" @click.prevent="setActive(2)"></vs-tab>
        <vs-tab :label="'KH quá hạn xử lý ('+ total.total_3+')'" @click.prevent="setActive(3)"></vs-tab>
      </vs-tabs>
      <div class="vs-component vs-con-table stripe vs-table-primary">
        <div class="con-tablex vs-table--content">
          <div class="vs-con-tbody vs-table--tbody ">
            <table class="vs-table vs-table--tbody-table" style="width: 2000px">
              <thead class="vs-table--thead">
                <tr>
                  <!---->
                  <th colspan="1" rowspan="1" class="text-center">
                    <div class="vs-table-text text-center">
                      <div class="vs-component con-vs-checkbox vs-checkbox-primary vs-checkbox-default">
                        <input type="checkbox" v-model="selectAll" class="vs-checkbox--input" >
                        <span class="checkbox_x vs-checkbox" style="border: 2px solid rgb(180, 180, 180);">
                          <span class="vs-checkbox--check">
                            <i class="vs-icon notranslate icon-scale vs-checkbox--icon  material-icons null">check</i>
                          </span>
                        </span>
                      </div>
                    </div>
                  </th>
                  <th colspan="1" rowspan="1" class="text-center">
                    <div class="vs-table-text text-center">STT
                      <!---->
                    </div>
                  </th>
                  <th colspan="1" rowspan="1" class="text-center">
                    <div class="vs-table-text">Tên khách hàng
                      <!---->
                    </div>
                  </th>
                  <th colspan="1" rowspan="1" class="text-center">
                    <div class="vs-table-text">Số điện thoại
                      <!---->
                    </div>
                  </th>
                  <th colspan="1" rowspan="1" class="text-center">
                    <div class="vs-table-text">Học sinh
                      <!---->
                    </div>
                  </th>
                  <th colspan="1" rowspan="1" class="text-center">
                    <div class="vs-table-text">Nguồn
                      <!---->
                    </div>
                  </th>
                  <th colspan="1" rowspan="1" class="text-center">
                    <div class="vs-table-text">Nguồn chi tiết
                      <!---->
                    </div>
                  </th>
                  <th colspan="1" rowspan="1" class="text-center">
                    <div class="vs-table-text">Người phụ trách
                      <!---->
                    </div>
                  </th>
                  <th colspan="1" rowspan="1" class="text-center">
                    <div class="vs-table-text">Lịch chăm sóc tiếp theo
                      <!---->
                    </div>
                  </th>
                  <th colspan="1" rowspan="1" class="text-center">
                    <div class="vs-table-text">Lịch sử chăm sóc
                      <!---->
                    </div>
                  </th>
                  <th colspan="1" rowspan="1" class="text-center">
                    <div class="vs-table-text">Thời gian chăm sóc gần nhất
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
              <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in parents" :key="index">
                <!---->
                <td class="td vs-table--td">
                  <div class="vs-component con-vs-checkbox vs-checkbox-primary vs-checkbox-default">
                    <input type="checkbox" v-model="checked_list" :value="item.id" class="vs-checkbox--input" >
                    <span class="checkbox_x vs-checkbox" style="border: 2px solid rgb(180, 180, 180);">
                      <span class="vs-checkbox--check">
                        <i class="vs-icon notranslate icon-scale vs-checkbox--icon  material-icons null">check</i>
                      </span>
                    </span>
                  </div>
                </td>
                <td class="td vs-table--td">{{ index + 1 + (pagination.cpage - 1) * pagination.limit }}</td>
                <td class="td vs-table--td"><router-link :to="`/crm/parent/${item.id}/detail`"><a>{{ item.name }}</a></router-link></td>
                <td class="td vs-table--td"><router-link :to="`/crm/parent/${item.id}/detail`"><a>{{ item.mobile_1 }}</a></router-link></td>
                <td class="td vs-table--td">{{ item.hs1_name }}</td>
                <td class="td vs-table--td">{{ item.source_name }}</td>
                <td class="td vs-table--td">{{ item.source_detail_name }}</td>
                <td class="td vs-table--td">{{ item.owner_name }}</td>
                <td class="td vs-table--td text-center">{{ item.next_care_date }}</td>
                <td class="td vs-table--td">{{ item.last_care }}</td>
                <td class="td vs-table--td text-center">{{ item.last_time_care }}</td>
                <td class="td vs-table--td text-center">{{ item.status | getStatusName}}</td>
                <td class="text-center list-action"> 
                  <router-link :to="`/crm/parent/${item.id}/detail`" >
                    <vs-button size="small"><i class="fa fa-eye"></i></vs-button>
                  </router-link>    
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
    <vs-popup :class="'modal_'+ modal_assign.color" :title="modal_assign.title" :active.sync="modal_assign.show" >
        <div class="vx-row" > 
          <div class="vx-col w-full mb-4">
            <label>Chọn người phụ trách</label>
            <multiselect
              placeholder="Chọn người phụ trách"
              v-model="owners"
              :options="users_manager_list"
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
          
          <div class="vx-col w-full">
            <vs-button color="dark" type="border" class="mr-3" @click="modal_assign.show = false">Hủy</vs-button>
            <vs-button color="success" @click="assignCustomer">Lưu</vs-button>
          </div>
        </div>
      </vs-popup>
  </div>

</template>

<script>

  import axios from '../../../http/axios.js'
  import Multiselect from "vue-multiselect";
  import DatePicker from "vue2-datepicker";
  import u from '../../../until/helper.js'
  import select from 'vue-select'

  export default {
    components: { 
      "vue-select": select,
      DatePicker,
      Multiselect,
    },
    data() {
      return {
        checked_list: [],
        activeItem: 0,    
        total:{
          total_0:0,
          total_1:0,
          total_2:0,
          total_3:0,
        },
        searchData: {
          keyword: "",
          arr_status: "",
          arr_owner: "",
          arr_source: "",
          arr_source_detail: "",
          status: "",
          owner_id: "",
          source_id: "",
          source_detail_id: "",
          pagination: this.pagination,
          dateRange: "",
          type_seach: 1,
        },
        statusOptions:[
          {id:0,label:'KH mới'},
          {id:10,label:'KH không liên lạc được'},
          {id:20,label:'KH ở vùng CMS không có cơ sở'},
          {id:30,label:'KH không nghe máy'},
          {id:40,label:'KH hẹn gọi lại sau'},
          {id:50,label:'KH không quan tâm'},
          {id:60,label:'KH không tiềm năng'},
          {id:71,label:'KH quan tâm, cần follow up date'},
          {id:72,label:'KH tiềm năng nhưng không muốn làm phiền'},
          {id:73,label:'KH đồng ý đặt lịch Checkin'},
          {id:81,label:'KH đã đến checkin'},
          {id:82,label:'KH đã mua gói phí'},
          {id:83,label:'KH đến hạn tái tục'},
          {id:90,label:'Danh sách đen'}
        ],
        users_manager_list:[],
        source_list:[],
        source_detail_list:[],
        searchData: {
          status: '',
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

        parents: [],
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
        modal_assign: {
          title: "BÀN GIAO KHÁCH HÀNG",
          show: false,
          color: "info",
          closeOnBackdrop: true,
          error_message:""
        },
        owner_id:"",
        owners:[],
      }
    },
    computed: {
      selectAll: {
        get: function() {
          return (
            parseInt(this.checked_list.length) === parseInt(this.parents.length)
          );
        },
        set: function(value) {
          const selected_list = [];
          if (value) {
            this.parents.forEach(parent => {
              selected_list.push(parent.id);
            });
          }
          this.checked_list = selected_list;
        }
      }
    },
    methods: {
      showModalAssgin(){
        this.owner_id =""
        this.modal_assign.show =true
      },
      reset() {
        this.searchData.keyword = ""
        this.searchData.arr_status= ""
        this.searchData.arr_owner= ""
        this.searchData.arr_source= ""
        this.searchData.arr_source_detail= ""
        this.searchData.status= ""
        this.searchData.owner_id= ""
        this.searchData.source_id= ""
        this.searchData.source_detail_id= ""
        this.searchData.pagination= this.pagination
        this.searchData.dateRange= ""
        this.searchData.type_seach= 1
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
        const ids_owner = []
        if (this.searchData.arr_owner && this.searchData.arr_owner.length) {
          this.searchData.arr_owner.map(item => {
            ids_owner.push(item.id)
          })
        }
        this.searchData.owner_id = ids_owner

        const ids_source = []
        if (this.searchData.arr_source && this.searchData.arr_source.length) {
          this.searchData.arr_source.map(item => {
            ids_source.push(item.id)
          })
        }
        this.searchData.source_id = ids_source

        const ids_source_detail = []
        if (this.searchData.arr_source_detail && this.searchData.arr_source_detail.length) {
          this.searchData.arr_source_detail.map(item => {
            ids_source_detail.push(item.id)
          })
        }
        this.searchData.source_detail_id = ids_source_detail

        const data = {
            status: this.searchData.status,
            owner_id: this.searchData.owner_id,
            source_id: this.searchData.source_id,
            source_detail_id: this.searchData.source_detail_id,
            start_date:startDate,
            end_date:endDate,
            pagination:this.pagination,
            type_seach:this.searchData.type_seach
          }
        localStorage.setItem("parents_searchData", JSON.stringify(this.searchData));

        this.$vs.loading()
        axios.p('/api/crm/parents/list', data)
          .then((response) => {
            this.$vs.loading.close()
            this.parents = response.data.list
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
      setActive (menuItem) {
        this.searchData.type_seach = menuItem
        this.activeItem = menuItem
        this.getData();
      },
      assignCustomer(){
        if(this.owners.length){
          const ids = []
          this.owners = u.is.obj(this.owners) ? [this.owners] : this.owners
          if (this.owners.length) {
            this.owners.map(item => {
              ids.push(item.id)
            })
          }
          const data = {
            parents: this.checked_list,
            owners: ids,
          };
          this.modal_assign.show =false
          this.$vs.loading()
            axios.p(`/api/crm/parents/assign_list`,data)
            .then((response) => {
              this.$vs.loading.close()
              this.$vs.notify({
                title: 'Thành Công',
                text: response.data.message,
                color: 'success',
                iconPack: 'feather',
                icon: 'icon-check'
              })
              this.getData();
              this.checked_list=[]
            })
            .catch((e) => {
            });
        }
      },
    },
    created() {
      axios.g(`/api/users/get-data/users-manager`)
        .then(response => {
        this.users_manager_list = response.data
      })
      axios.g(`/api/system/sources`)
        .then(response => {
        this.source_list = response.data
      })
      axios.g(`/api/system/source_detail`)
        .then(response => {
        this.source_detail_list = response.data
      })
      if(localStorage.getItem("parents_searchData")){
        this.searchData =  JSON.parse(localStorage.getItem("parents_searchData"));
        this.activeItem = this.searchData.type_seach
      }

      this.getData();
    },
    filters: {
      getStatusName(value) {
        let resp = ''
        switch (Number(value)) {
            case 0:
                resp = 'KH mới';
                break;
            case 10:
                resp = 'KH không liên lạc được';
                break;
            case 20:
                resp = 'KH ở vùng CMS không có cơ sở';
                break;
            case 30:
                resp = 'KH không nghe máy';
                break;
            case 40:
                resp = 'KH hẹn gọi lại sau';
                break;
            case 50:
                resp = 'KH không quan tâm';
                break;
            case 60:
                resp = 'KH không tiềm năng';
                break;
            case 71:
                resp = 'KH quan tâm, cần follow up date';
                break;
            case 72:
                resp = 'KH tiềm năng nhưng không muốn làm phiền';
                break;
            case 73:
                resp = 'KH đồng ý đặt lịch Checkin';
                break;
            case 81:
                resp = 'KH đến hạn tái tục';
                break;
            case 82:
                resp = 'KH đã mua gói phí';
                break;
            case 83:
                resp = 'KH đến hạn tái tục';
                break;
            case 90:
                resp = 'Danh sách đen';
                break;
            default:
                resp = 'KH mới'
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