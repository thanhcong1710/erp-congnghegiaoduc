

<template>

  <div id="page-roles-list">
    <vx-card no-shadow class="mt-5">
      <h5>BÁO CÁO CUỘC GỌI - TỔNG HỢP</h5>
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
            <label for="ccmonth">Nguồn</label>
              <multiselect
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
              @input="saveSource"
            >
              <span slot="noResult">Không tìm thấy dữ liệu</span>
            </multiselect>   
          </div>
          <div class="vx-col sm:w-1/4 w-full mb-4">
          <label for="ccmonth">Nguồn chi tiết</label>
            <multiselect
              placeholder="Chọn nguồn chi tiết"
              select-label="Chọn nguồn chi tiết "
              v-model="searchData.arr_source_detail"
              :options="source_detail_list"
              label="name"
              :close-on-select="false"
              :hide-selected="true"
              :multiple="true"
              :searchable="true"
              track-by="id"
            >
              <span slot="noResult">Không tìm thấy dữ liệu</span>
            </multiselect>   
          </div>  
          <!-- <div class="vx-col sm:w-1/4 w-full mb-4">
            <label for="" class="vs-input--label">Từ khóa</label>
            <vs-input class="w-full" placeholder="Mã học sinh, tên học sinh" v-model="searchData.keyword"></vs-input>
          </div> -->
          
        </div>
        <div class="vx-row mt-3">
          <div class="vx-col w-full">
            <vs-button class="mr-3 mb-2" @click="getData"><i class="fa fa-search"></i> Tìm kiếm</vs-button>
            <vs-button color="dark" type="border" class="mr-3 mb-2" @click="reset" ><i class="fas fa-undo-alt"></i> Hủy</vs-button>
            <!-- <vs-button color="success"  class="mb-2" @click="exportExcel" ><i class="fa fa-file-excel"></i> Export</vs-button> -->
          </div>
        </div>
      </div>
      <div class="vx-row">
        <div class="vx-col sm:w-1/3 w-full mb-4">
          <div class="vs-component vs-con-table stripe vs-table-primary">
            <div class="con-tablex vs-table--content">
              <div class="vs-con-tbody vs-table--tbody ">
                <table class="vs-table vs-table--tbody-table">
                  <tr class="tr-values vs-table--tr tr-table-state-null" style="background-color: rgba(0, 0, 21, 0.05)">
                    <td colspan="2">
                      Thông tin chung
                    </td>
                  </tr>
                  <tr class="tr-values vs-table--tr tr-table-state-null">
                      <td class="text-left">Tổng số data đã chia chưa xử lý</td>
                      <td class="text-right"> {{data_report.total_new}}</td>
                    </tr>
                    <tr class="tr-values vs-table--tr tr-table-state-null">
                      <td  class="text-left">Tổng số data kết nối được</td>
                      <td class="text-right">{{data_report.total_connect}}</td>
                    </tr>
                    <tr class="tr-values vs-table--tr tr-table-state-null">
                      <td  class="text-left">Tổng số data không kết nối được</td>
                      <td class="text-right">{{data_report.total_not_connect}}</td>
                    </tr>
                </table>
                
              </div>
            </div>
          </div>
        </div>
        <div class="vx-col sm:w-2/3 w-full mb-4">
          <div class="vs-component vs-con-table stripe vs-table-primary">
            <div class="con-tablex vs-table--content">
              <div class="vs-con-tbody vs-table--tbody ">
                <table class="vs-table vs-table--tbody-table">
                  <tr class="tr-values vs-table--tr tr-table-state-null" style="background-color: rgba(0, 0, 21, 0.05)">
                    <td colspan="2">
                      Thông tin chi tiết
                    </td>
                  </tr>
                  <tr class="tr-values vs-table--tr tr-table-state-null">
                      <td class="text-left">1. Blank</td>
                      <td class="text-right">{{data_report.detail_1}}</td>
                    </tr>
                    <tr class="tr-values vs-table--tr tr-table-state-null">
                      <td  class="text-left">2. Thuê bao - Tắt máy - Sai số</td>
                      <td class="text-right">{{data_report.detail_2}}</td>
                    </tr>
                    <tr class="tr-values vs-table--tr tr-table-state-null">
                      <td  class="text-left">3. Location</td>
                      <td class="text-right">{{data_report.detail_3}}</td>
                    </tr>
                    <tr class="tr-values vs-table--tr tr-table-state-null">
                      <td  class="text-left">4. Máy bận - Không nghe máy</td>
                      <td class="text-right">{{data_report.detail_4}}</td>
                    </tr>
                    <tr class="tr-values vs-table--tr tr-table-state-null">
                      <td  class="text-left">5. KH hẹn gọi lại sau</td>
                      <td class="text-right">{{data_report.detail_5}}</td>
                    </tr>
                    <tr class="tr-values vs-table--tr tr-table-state-null">
                      <td  class="text-left">6. KH không có nhu cầu</td>
                      <td class="text-right">{{data_report.detail_6}}</td>
                    </tr>
                    <tr class="tr-values vs-table--tr tr-table-state-null">
                      <td  class="text-left">7. Không có con/Không có con trong độ tuổi CMS</td>
                      <td class="text-right">{{data_report.detail_7}}</td>
                    </tr>
                    <tr class="tr-values vs-table--tr tr-table-state-null">
                      <td  class="text-left">8. Lý do khác</td>
                      <td class="text-right">{{data_report.detail_8}}</td>
                    </tr>
                    <tr class="tr-values vs-table--tr tr-table-state-null">
                      <td  class="text-left">9. KH quan tâm cần follow update</td>
                      <td class="text-right">{{data_report.detail_9}}</td>
                    </tr>
                    <tr class="tr-values vs-table--tr tr-table-state-null">
                      <td  class="text-left">10.KH đồng ý đặt lịch check in</td>
                      <td class="text-right">{{data_report.detail_10}}</td>
                    </tr>
                    <tr class="tr-values vs-table--tr tr-table-state-null">
                      <td  class="text-left">11. Danh sách đen</td>
                      <td class="text-right">{{data_report.detail_11}}</td>
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
          dateRange: [new Date(), new Date()],
          arr_owner: "",
          owner_id:"",
          arr_source: "",
          source_id:"",
          arr_source_detail: "",
          source_detail_id:"",
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
      }
    },
    created() {
      axios.g(`/api/system/branches-has-user`)
        .then(response => {
        this.branch_list = response.data
      })
      this.getData();
      axios.g(`/api/system/sources`)
        .then(response => {
        this.source_list = response.data
      })
      this.localeDataSourceDetail();
      this.localeDataOwner();
    },
    methods: {
      localeDataSourceDetail(){
        axios.p(`/api/system/source_detail`,{
          arr_source: this.searchData.arr_source
        })
          .then(response => {
          this.source_detail_list = response.data
        })
      },
      localeDataOwner(){
        axios.p(`/api/system/owner-by-branch`,{
          arr_branch: this.searchData.arr_branch
        })
          .then(response => {
          this.users_manager = response.data
        })
      },
      saveSource(data = null){
        this.searchData.arr_source_detail=""
        this.localeDataSourceDetail();
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
        const ids_source = []
        if (this.searchData.arr_source && this.searchData.arr_source.length) {
          this.searchData.arr_owner.map(item => {
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
            keyword: this.searchData.keyword,
            branch_id: this.searchData.branch_id,
            owner_id: this.searchData.owner_id,
            source_id: this.searchData.source_id,
            ids_source_detail: this.searchData.ids_source_detail,
            start_date: startDate,
            end_date: endDate,
            pagination:this.pagination,
          }

        this.$vs.loading()
        axios.p('/api/lms/reports/04', data)
          .then((response) => {
            this.$vs.loading.close()
            this.data_report = response.data;
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
        var url = `/api/lms/exports/report01/`;
        var ids_branch = "";
        if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
          this.searchData.arr_branch.map(item => {
            ids_branch += ids_branch ? "-" + item.id : item.id;
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
        if (this.searchData.dateRange){
          this.key += "start_date,"
          this.value +=u.getDateMonth(this.searchData.dateRange)+","
          console.log(u.getDateMonth(this.searchData.dateRange))
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