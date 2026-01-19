
<template>

  <div id="page-report-13">
    <vx-card no-shadow class="mt-5">
      <h5>BÁO CÁO DOANH SỐ CHI TIẾT HỌC SINH ĐI HỌC</h5>
      <hr class="mt-2 mb-4" style="border: 0.5px solid #ccc;">
      <div class="mb-5">
        <div class="vx-row">
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
              >
                <span slot="noResult">Không tìm thấy dữ liệu</span>
              </multiselect>
          </div>
          <div class="vx-col sm:w-1/4 w-full mb-4">
            <label for="" class="vs-input--label">Từ khóa</label>
            <vs-input class="w-full" placeholder="Mã học sinh, tên học sinh" v-model="searchData.keyword"></vs-input>
          </div>
          <div class="vx-col sm:w-1/4 w-full mb-4">
            <label for="" class="vs-input--label">Thời gian</label>
            <date-picker name="item-date" v-model="searchData.dateRange" format="YYYY-MM-DD" style="width: 100%" type="date" range
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
            <table class="vs-table vs-table--tbody-table" style="width: 100%">
              <thead class="vs-table--thead">
                <tr>
                  <th class="text-center">STT</th>
                  <th>Trung tâm</th>
                  <th>Mã HS</th>
                  <th>Tên HS</th>
                  <th>Lớp học</th>
                  <th class="text-center">Ngày học</th>
                  <th class="text-right">Doanh số (VNĐ)</th>
                </tr>
              </thead>
              <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in datas" :key="index">
                <td class="td vs-table--td text-center">{{ index + 1 + (pagination.cpage - 1) * pagination.limit }}</td>
                <td class="td vs-table--td">{{item.branch_name}}</td>
                <td class="td vs-table--td">{{item.lms_code}}</td>
                <td class="td vs-table--td">{{item.student_name}}</td>
                <td class="td vs-table--td">{{item.class_name}}</td>
                <td class="td vs-table--td text-center">{{item.class_date}}</td>
                <td class="td vs-table--td text-right">{{item.session_value | formatMoney}}</td>
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
            <vs-dropdown-item v-for="(item, index) in limitSource" :key="index" @click="changePageLimit(item)" >
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
        branch_list: [],
        products: [],
        searchData: {
          arr_branch: "",
          branch_id: "",
          keyword: "",
          dateRange: "",
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
      reset() {
        this.searchData.keyword = ""
        this.searchData.arr_branch= ""
        this.searchData.branch_id= ""
        this.searchData.dateRange= ""
        this.getData();
      },
      getData() {
        const ids_branch = []
        if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
          this.searchData.arr_branch.map(item => {
            ids_branch.push(item.id)
          })
        }
        this.searchData.branch_id = ids_branch
        
        let start_date = ''
        let end_date = ''
        if(this.searchData.dateRange && this.searchData.dateRange.length == 2){
            start_date = u.dateToString(this.searchData.dateRange[0])
            end_date = u.dateToString(this.searchData.dateRange[1])
        }

        const data = {
            keyword: this.searchData.keyword,
            branch_id: this.searchData.branch_id,
            start_date: start_date,
            end_date: end_date,
            pagination:this.pagination,
          }

        this.$vs.loading()
        axios.p('/api/lms/reports/13', data)
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
        let keys = []
        let values = []
        
        // Branch
        const ids_branch = []
        if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
          this.searchData.arr_branch.map(item => {
            ids_branch.push(item.id)
          })
        }
        if (ids_branch.length) {
            keys.push('branch_id')
            values.push(ids_branch.join('-'))
        }

        // Keyword
        if (this.searchData.keyword) {
            keys.push('keyword')
            values.push(this.searchData.keyword)
        }
        
        // Date Range
        if(this.searchData.dateRange && this.searchData.dateRange.length == 2){
          keys.push('start_date')
          values.push(u.dateToString(this.searchData.dateRange[0]))
          keys.push('end_date')
          values.push(u.dateToString(this.searchData.dateRange[1]))
        }

        if (keys.length == 0) {
            keys.push('k')
            values.push('v')
        }

        const link = `/api/lms/exports/report13/${keys.join(',')}/${values.join(',')}?token=${localStorage.getItem("accessToken")}`
        window.open(link, '_blank')
      },
    },
    filters: {
      formatMoney(value) {
        if (!value) return 0;
        return Number(value).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
      }
    }
  }
</script>
<style>
@media only screen and (min-width: 600px) {
  #page-report-13 .vs-table--search {
    max-width: 360px;
  }
  #page-report-13 .vs-table--search-input{
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
