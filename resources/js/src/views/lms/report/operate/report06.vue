

<template>

  <div id="page-roles-list">
    <vx-card no-shadow class="mt-5">
      <h5>BÁO CÁO DOANH THU</h5>
      <hr class="mt-2 mb-4" style="border: 0.5px solid #ccc;">
      <div class="mb-5">
        <div class="vx-row">
          <div class="vx-col sm:w-1/4 w-full mb-4">
            <label for="" class="vs-input--label">Thời gian</label>
            <date-picker name="item-date" v-model="searchData.dateRange" format="YYYY-MM-DD" style="width: 100%" range
              :clearable="true" :lang="datepickerOptions.lang" placeholder="Chọn khoảng thời gian tìm kiếm"></date-picker>
          </div>
          <div class="vx-col sm:w-1/4 w-full mb-4">
            <label for="" class="vs-input--label">Trung tâm</label>
            <multiselect
                name="search_branch"
                placeholder="Chọn trung tâm"
                v-model="searchData.arr_branch"
                :options="branch_list"
                label="name"
                :close-on-select="false"
                :hide-selected="true"
                :multiple="true"
                :searchable="true"
                track-by="id"
                selectedLabel="" selectLabel="" deselectLabel=""
                @input="saveBranch"
              >
                <span slot="noResult">Không tìm thấy dữ liệu</span>
              </multiselect>
          </div>
          <div class="vx-col sm:w-1/4 w-full mb-4">
            <label for="ccmonth">Người phụ trách</label>
            <multiselect
              placeholder="Chọn người phụ trách"
              select-label="Chọn một người phụ trách"
              v-model="searchData.arr_owner"
              :options="users_manager"
              label="label_name"
              :close-on-select="false"
              :hide-selected="true"
              :multiple="true"
              :searchable="true"
              track-by="id"
            >
              <span slot="noResult">Không tìm thấy dữ liệu</span>
            </multiselect>  
          </div> 
          <div class="vx-col sm:w-1/4 w-full mb-4">
            <label for="" class="vs-input--label">Từ khóa</label>
            <vs-input class="w-full" placeholder="Tên, mã học sinh" v-model="searchData.keyword"></vs-input>
          </div>
        </div>
        <div class="vx-row mt-3">
          <div class="vx-col w-full">
            <vs-button class="mr-3 mb-2" @click="getData"><i class="fa fa-search"></i> Tìm kiếm</vs-button>
            <vs-button color="dark" type="border" class="mr-3 mb-2" @click="reset" ><i class="fas fa-undo-alt"></i> Hủy</vs-button>
            <vs-button color="success"  class="mb-2" @click="exportExcel" ><i class="fa fa-file-excel"></i> Export</vs-button>
          </div>
        </div>
      </div>
      <p class="mb-2 float-right"><span>Tổng doanh thu: <strong>{{ total_summary | formatNumber }}</strong> đ</span></p>
      <div class="vs-component vs-con-table stripe vs-table-primary">
        <div class="con-tablex vs-table--content">
          <div class="vs-con-tbody vs-table--tbody ">
            <table class="vs-table vs-table--tbody-table" style="width: 3000px">
              <thead class="vs-table--thead">
                <tr>
                  <!---->
                  <th colspan="1" rowspan="1" class="text-center">STT</th>
                  <th colspan="1" rowspan="1">Ngày hạch toán</th>
                  <th colspan="1" rowspan="1">Ngày thu</th>
                  <th colspan="1" rowspan="1">Phân loại</th>
                  <th colspan="1" rowspan="1">Mã học sinh</th>
                  <th colspan="1" rowspan="1">Tên học sinh</th>
                  <th colspan="1" rowspan="1">Tên phụ huynh</th>
                  <th colspan="1" rowspan="1">Tên gói phí</th>
                  <th colspan="1" rowspan="1">Trung tâm</th>
                  <th colspan="1" rowspan="1">Mã nhân viên</th>
                  <th colspan="1" rowspan="1">Tên tư vấn viên</th>
                  <th colspan="1" rowspan="1">GT khóa học</th>
                  <th colspan="1" rowspan="1">Mã CK</th>
                  <th colspan="1" rowspan="1">Giảm trừ theo mã CK</th>
                  <th colspan="1" rowspan="1">Tiền giảm khác</th>
                  <th colspan="1" rowspan="1">Tiền sau giảm trừ</th>
                  <th colspan="1" rowspan="1">Tổng đã nộp</th>
                  <th colspan="1" rowspan="1">Số tiền còn lại phải thu</th>
                  <th colspan="1" rowspan="1">Số tiền đóng</th>
                  <th colspan="1" rowspan="1">Doanh số thực thu của trung tâm</th>
                </tr>
              </thead>
              <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in datas" :key="index">
                <!---->
                
                <td class="td vs-table--td text-center">{{ index + 1 + (pagination.cpage - 1) * pagination.limit }}</td>
                <td class="td vs-table--td">{{item.created_date}}</td>
                <td class="td vs-table--td">{{item.charge_date}}</td>
                <td class="td vs-table--td">{{item.count_recharge ==0 ? 'Mới' : 'Tái tục' }}</td>
                <td class="td vs-table--td">{{ item.lms_code}}</td>
                <td class="td vs-table--td">{{ item.name}}</td>
                <td class="td vs-table--td">{{ item.gud_name1}}</td>
                <td class="td vs-table--td">{{ item.tuition_fee_name}}</td>
                <td class="td vs-table--td">{{ item.branch_name}}</td>
                <td class="td vs-table--td">{{ item.ec_hrm}}</td>
                <td class="td vs-table--td">{{ item.ec_name}}</td>
                <td class="td vs-table--td">{{ item.init_tuition_fee_amount | formatNumber}}</td>
                <td class="td vs-table--td">{{ item.discount_code}}</td>
                <td class="td vs-table--td">{{ item.discount_code_amount | formatNumber}}</td>
                <td class="td vs-table--td">{{ item.total_discount - item.discount_code_amount | formatNumber}}</td>
                <td class="td vs-table--td">{{ item.must_charge | formatNumber}}</td>
                <td class="td vs-table--td">{{ item.total | formatNumber}}</td>
                <td class="td vs-table--td">{{ item.debt | formatNumber}}</td>
                <td class="td vs-table--td"><strong>{{ item.amount | formatNumber}}</strong></td>
                <td class="td vs-table--td">{{ item.amount | formatNumber}}</td>
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
  </div>

</template>

<script>

  import vSelect from 'vue-select'
  import axios from '../../../../http/axios.js'
  import Multiselect from "vue-multiselect";
  import DatePicker from "vue2-datepicker";
  import u from '../../../../until/helper.js'

  export default {
    components: { 
      vSelect,
      Multiselect,
      DatePicker
    },
    data() {
      return {
        users_manager: [],
        branch_list: [],
        source_list:[],
        source_detail_list:[],
        searchData: {
          arr_branch: "",
          branch_id:"",
          keyword: "",
          dateRange: [
            new Date(new Date().getFullYear(),new Date().getMonth(), 1),
            new Date()
          ],
          arr_owner: "",
          owner_id:"",
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
        datas: [],
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
        delete_id:'',
        data_report: [],
        total_summary:0
      }
    },
    created() {
      axios.g(`/api/system/branches-has-user`)
        .then(response => {
        this.branch_list = response.data
      })
      this.getData();
      this.localeDataOwner();
    },
    methods: {
      localeDataOwner(){
        axios.p(`/api/system/owner-by-branch`,{
          arr_branch: this.searchData.arr_branch
        })
          .then(response => {
          this.users_manager = response.data
        })
      },
      saveBranch(data = null){
        this.searchData.arr_owner=""
        this.localeDataOwner();
      },
      reset() {
        location.reload();
      },
      getData() {
        const startDate = this.searchData.dateRange!='' && this.searchData.dateRange!= undefined && this.searchData.dateRange[0] ?`${u.dateToString(this.searchData.dateRange[0])}`:''
        const endDate = this.searchData.dateRange!='' && this.searchData.dateRange!= undefined && this.searchData.dateRange[1] ?`${u.dateToString(this.searchData.dateRange[1])}`:''

        const ids_branch = []
        if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
          this.searchData.arr_branch.map(item => {
            ids_branch.push(item.id)
          })
        }
        this.searchData.branch_id = ids_branch
        const ids_owner = []
        if (this.searchData.arr_owner && this.searchData.arr_owner.length) {
          this.searchData.arr_owner.map(item => {
            ids_owner.push(item.id)
          })
        }
        this.searchData.owner_id = ids_owner
        const data = {
            keyword: this.searchData.keyword,
            branch_id: this.searchData.branch_id,
            owner_id: this.searchData.owner_id,
            start_date: startDate,
            end_date: endDate,
            pagination:this.pagination,
          }

        this.$vs.loading()
        axios.p('/api/lms/reports/06', data)
          .then((response) => {
            this.$vs.loading.close()
            this.datas = response.data.list;
            this.total_summary = response.data.summary
            this.pagination.spage = response.data.paging.spage;
            this.pagination.ppage = response.data.paging.ppage;
            this.pagination.npage = response.data.paging.npage;
            this.pagination.lpage = response.data.paging.lpage;
            this.pagination.cpage = response.data.paging.cpage;
            this.pagination.total = response.data.paging.total;
            this.pagination.limit = response.data.paging.limit;
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
      exportExcel() {
        const startDate = this.searchData.dateRange!='' && this.searchData.dateRange!= undefined && this.searchData.dateRange[0] ?`${u.dateToString(this.searchData.dateRange[0])}`:''
        const endDate = this.searchData.dateRange!='' && this.searchData.dateRange!= undefined && this.searchData.dateRange[1] ?`${u.dateToString(this.searchData.dateRange[1])}`:''
      
        var url = `/api/lms/exports/report06/`;
        var ids_branch = "";
        if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
          this.searchData.arr_branch.map(item => {
            ids_branch += ids_branch ? "-" + item.id : item.id;
          })
        }
        const ids_owner = ""
        if (this.searchData.arr_owner && this.searchData.arr_owner.length) {
          this.searchData.arr_owner.map(item => {
            ids_owner += ids_owner ? "-" + item.id : item.id;
          })
        }
        
        this.key ='';
        this.value = ''
        if (this.searchData.keyword){
          this.key += "keyword,"
          this.value += this.searchData.keyword+","
        }
        if (ids_branch){
          this.key += "branch_id,"
          this.value += ids_branch+","
        }
        if (ids_owner){
          this.key += "owner_id,"
          this.value += ids_owner+","
        }
        if (startDate){
          this.key += "start_date,"
          this.value += startDate+","
        }
        if (endDate){
          this.key += "end_date,"
          this.value += endDate+","
        }
        this.key = this.key? this.key.substring(0, this.key.length - 1):'_'
        this.value = this.value? this.value.substring(0, this.value.length - 1) : "_"
        url += this.key+"/"+this.value +`?token=${localStorage.getItem("accessToken")}`
        window.open(url, '_blank');
      },
    },
    filters: {
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
.td.vs-table--td{
  vertical-align: top;
}
</style>