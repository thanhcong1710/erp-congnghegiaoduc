

<template>

  <div id="page-roles-list">
    <vx-card no-shadow class="mt-5">
      <h5>BÁO CÁO CHI TIẾT HỌC SINH TÁI PHÍ</h5>
      <hr class="mt-2 mb-4" style="border: 0.5px solid #ccc;">
      <div class="mb-5 mt-5">
        <div class="vx-row">
          <div class="vx-col sm:w-1/3 w-full mb-4">
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
                @input="slectBranch"
              >
                <span slot="noResult">Không tìm thấy dữ liệu</span>
              </multiselect>
          </div>
          <div class="vx-col sm:w-1/3 w-full mb-4">
            <label>AF - Quản lý lớp học</label>
            <vue-select
                  label="label"
                  placeholder="Chọn quản lý lớp học"
                  :options="cms_list"
                  v-model="cms_item"
                  :searchable="true"
                  language="tv-VN"
                  @input="saveAF"
              ></vue-select>
          </div>
          <div class="vx-col sm:w-1/3 w-full mb-4">
            <label>Lớp học</label>
            <vue-select
                  label="cls_name"
                  placeholder="Chọn lớp học"
                  :options="class_list"
                  v-model="class_item"
                  :searchable="true"
                  language="tv-VN"
                  @input="saveClass"
              ></vue-select>
          </div>
          <div class="vx-col sm:w-1/3 w-full mb-4">
            <label for="" class="vs-input--label">Từ khóa</label>
            <vs-input class="w-full" placeholder="Mã tên học sinh, mã học sinh" v-model="searchData.keyword"></vs-input>
          </div>
          <div class="vx-col sm:w-1/3 w-full mb-4">
            <label for="" class="vs-input--label">Thời gian</label>
            <date-picker name="item-date" v-model="searchData.dateRange" range format="YYYY-MM-DD" style="width: 100%"
              :clearable="true" :lang="datepickerOptions.lang" placeholder="Chọn khoảng thời gian tìm kiếm"></date-picker>
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

      <div class="vs-component vs-con-table stripe vs-table-primary">
        <div class="con-tablex vs-table--content">
          <div class="vs-con-tbody vs-table--tbody ">
            <table class="vs-table vs-table--tbody-table" style="width: 1800px">
              <thead class="vs-table--thead">
                <tr>
                  <!---->
                  <th colspan="1" rowspan="1" class="text-center">STT</th>
                  <th colspan="1" rowspan="1">Trung tâm</th>
                  <th colspan="1" rowspan="1">Mã học sinh</th>
                  <th colspan="1" rowspan="1">Học sinh</th>
                  <th colspan="1" rowspan="1">Sản phẩm</th>
                  <th colspan="1" rowspan="1">Lớp học</th>
                  <th colspan="1" rowspan="1">Ngày đến hạn tái tục</th>
                  <th colspan="1" rowspan="1">Kết quả</th>
                  <th colspan="1" rowspan="1">Gói tái phí</th>
                  <th colspan="1" rowspan="1">Số tiền tái phí</th>
                  <th colspan="1" rowspan="1">AF</th>
                </tr>
              </thead>
              <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in datas" :key="index">
                <!---->
                
                <td class="td vs-table--td text-center">{{ index + 1 + (pagination.cpage - 1) * pagination.limit }}</td>
                <td class="td vs-table--td">{{item.branch_name}}</td>
                <td class="td vs-table--td">{{item.lms_code}}</td>
                <td class="td vs-table--td">{{item.student_name}}</td>
                <td class="td vs-table--td">{{item.product_name}}</td>
                <td class="td vs-table--td">{{ item.class_name}}</td>
                <td class="td vs-table--td">{{ item.last_date}}</td>
                <td class="td vs-table--td">{{ item.status_title}}</td>
                <td class="td vs-table--td"><span v-if="item.status==1">{{ item.tuition_fee_name }}</span></td>
                <td class="td vs-table--td"><span v-if="item.status==1">{{ item.renew_amount | formatMoney }}</span></td>
                <td class="td vs-table--td">{{ item.cm_name}}</td>
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
      DatePicker,
      "vue-select": vSelect,
    },
    data() {
      return {
        branch_list: [],
        cms_list: [],
        cms_item:"",
        class_list: [],
        class_item:"",
        searchData: {
          arr_branch: "",
          branch_id:"",
          keyword: "",
          dateRange: "",
          cm_id:"",
          class_id:"",
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
      }
    },
    created() {
      axios.g(`/api/system/branches-has-user`)
        .then(response => {
        this.branch_list = response.data
      })
      this.getData();
    },
    methods: {
      slectBranch(){
        var branch_id = 0
        if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
          this.searchData.arr_branch.map(item => {
            branch_id = item.id
          })
        }
        axios.g(`/api/system/cms/${branch_id}`)
        .then(response => {
          this.cms_list = response.data
          this.cms_item=""
          this.searchData.cm_id = ''
          this.class_list = ""
          this.class_item = ""
        })
      },
      saveAF(data = null){
        if (data && typeof data === 'object') {
          const cm_id = data.id
          this.searchData.cm_id = cm_id
          axios.g(`/api/system/get-class-by-cm/${cm_id}`)
            .then(response => {
              this.class_list = response.data
              this.searchData.class_id = ''
              this.class_item = ""
            })
        }else{
          this.searchData.cm_id=""
          this.searchData.class_id = ""
          this.class_item = ""
        }
      },
      saveClass(data = null){
        if (data && typeof data === 'object') {
          const class_id = data.id
          this.searchData.class_id = class_id
        }else{
          this.searchData.class_id = ""
        }
      },
      reset() {
        location.reload();
      },
      getData() {
        const startDate = typeof this.searchData.dateRange != 'undefined' && this.searchData.dateRange!='' && this.searchData.dateRange[0] ?`${u.dateToString(this.searchData.dateRange[0])}`:''
        const endDate = typeof this.searchData.dateRange != 'undefined' && this.searchData.dateRange!='' && this.searchData.dateRange[1] ?`${u.dateToString(this.searchData.dateRange[1])}`:''
        const ids_branch = []
        if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
          this.searchData.arr_branch.map(item => {
            ids_branch.push(item.id)
          })
        }
        this.searchData.branch_id = ids_branch
        const data = {
            keyword: this.searchData.keyword,
            branch_id: this.searchData.branch_id,
            cm_id: this.searchData.cm_id,
            class_id: this.searchData.class_id, 
            start_date: startDate,
            end_date: endDate,
            pagination:this.pagination,
          }

        this.$vs.loading()
        axios.p('/api/lms/reports/02a', data)
          .then((response) => {
            this.$vs.loading.close()
            this.datas = response.data.list
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
      exportExcel() {
        var url = `/api/lms/exports/report02a/`;
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
        const startDate = typeof this.searchData.dateRange != 'undefined' && this.searchData.dateRange!='' && this.searchData.dateRange[0] ?`${u.dateToString(this.searchData.dateRange[0])}`:''
        const endDate = typeof this.searchData.dateRange != 'undefined' && this.searchData.dateRange!='' && this.searchData.dateRange[1] ?`${u.dateToString(this.searchData.dateRange[1])}`:''
        
        if (startDate){
          this.key += "start_date,"
          this.value +=startDate+","
        }
        if (endDate){
          this.key += "end_date,"
          this.value +=endDate+","
        }
        if (this.searchData.cm_id){
          this.key += "cm_id,"
          this.value +=this.searchData.cm_id+","
        }
        if (this.searchData.class_id){
          this.key += "class_id,"
          this.value +=this.searchData.class_id+","
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