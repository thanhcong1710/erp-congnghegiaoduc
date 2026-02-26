<template>

  <div id="page-report-18">
    <vx-card no-shadow class="mt-5">
      <h5>BÁO CÁO CHI TIẾT THEO TỪNG KHÓA TRONG COMBO</h5>
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
            <label for="" class="vs-input--label">Năm học</label>
            <multiselect
                name="search_school_year"
                placeholder="Chọn năm học"
                v-model="searchData.school_year"
                :options="schoolYearOptions"
                label="label"
                :close-on-select="true"
                :hide-selected="true"
                :multiple="false"
                :searchable="true"
                track-by="id"
                selectedLabel="" selectLabel="" deselectLabel=""
              >
                <span slot="noResult">Không tìm thấy dữ liệu</span>
              </multiselect>
          </div>
          <div class="vx-col sm:w-1/4 w-full mb-4">
            <label for="" class="vs-input--label">Từ khóa</label>
            <vs-input class="w-full" placeholder="Mã HS, Tên HS" v-model="searchData.keyword"></vs-input>
          </div>
          <div class="vx-col sm:w-1/4 w-full mb-4">
            <label for="" class="vs-input--label">Thời gian full fee</label>
            <date-picker name="item-date" v-model="searchData.dateRange" format="YYYY-MM-DD" style="width: 100%" type="date" range
              :clearable="true" :lang="datepickerOptions.lang" placeholder="Chọn khoảng thời gian"></date-picker>
          </div>
        </div>
        <div class="vx-row mt-3">
          <div class="vx-col w-full">
            <vs-button class="mr-3 mb-2" @click="getData"><i class="fa fa-search"></i> Tìm kiếm</vs-button>
            <vs-button color="dark" type="border" class="mr-3 mb-2" @click="reset" ><i class="fas fa-undo-alt"></i> Hủy</vs-button>
            <vs-button color="success" class="mb-2" @click="exportExcel" ><i class="fa fa-file-excel"></i> Export</vs-button>
          </div>
        </div>
      </div>

      <!-- Summary Section -->

      <div class="vs-component vs-con-table stripe vs-table-primary">
        <div class="con-tablex vs-table--content" style="overflow-x: auto;">
          <div class="vs-con-tbody vs-table--tbody ">
            <table class="vs-table vs-table--tbody-table" style="min-width: 1200px">
              <thead class="vs-table--thead">
                <tr>
                  <th class="text-center" style="width: 50px">STT</th>
                  <vs-th class="vs-table-text" style="width: 100px;">Mã HV</vs-th>
                  <vs-th class="vs-table-text" style="width: 200px;">Họ tên học viên</vs-th>
                  <vs-th class="vs-table-text" style="width: 200px;">Tên khóa</vs-th>
                  <vs-th class="sort-th text-center" style="width: 150px;">Số buổi khóa</vs-th>
                  <vs-th class="sort-th text-center" style="width: 150px;">Đã học</vs-th>
                  <vs-th class="sort-th text-center" style="width: 150px;">Còn lại</vs-th>
                  <vs-th class="text-right" style="width: 150px;">Giá trị còn lại</vs-th>
                </tr>
              </thead>
               <tbody>
                <tr :key="indextr" v-for="(tr, indextr) in contracts">
                  <vs-td>{{ indextr + 1 }}</vs-td>
                  <vs-td>{{ tr.student_code }}</vs-td>
                  <vs-td>{{ tr.student_name }}</vs-td>
                  <vs-td >{{ tr.course_name }}</vs-td>
                  <vs-td class="text-center">{{ tr.summary_sessions }}</vs-td>
                  <vs-td class="text-center">{{ tr.done_sessions }}</vs-td>
                  <vs-td class="text-center">{{ tr.left_sessions }}</vs-td>
                  <vs-td class="text-right">{{ tr.left_value | formatMoney }}</vs-td>
                </tr>
                <!-- Dòng tổng cộng chung -->
                <tr class="tr-summary" v-if="contracts.length > 0">
                  <vs-td colspan="4" class="text-right"><strong>TỔNG CỘNG</strong></vs-td>
                  <vs-td class="text-center"><strong>{{ summary.total_summary_sessions | formatNumber }}</strong></vs-td>
                  <vs-td class="text-center"><strong>{{ summary.total_done_sessions | formatNumber }}</strong></vs-td>
                  <vs-td class="text-center"><strong>{{ summary.total_left_sessions | formatNumber }}</strong></vs-td>
                  <vs-td class="text-right"><strong>{{ summary.total_left_value | formatMoney }}</strong></vs-td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div class="flex flex-wrap items-center mt-5">
        <vs-dropdown vs-trigger-click class="cursor-pointer mr-4 items-per-page-handler">
          <div class="p-4 border border-solid d-theme-border-grey-light rounded-full d-theme-dark-bg cursor-pointer flex items-center justify-between font-medium">
            <span class="mr-2">{{ pagination.cpage * pagination.limit - (pagination.limit - 1) }} - {{ pagination.total - pagination.cpage * pagination.limit > 0 ? pagination.cpage * pagination.limit : pagination.total }} of {{ pagination.total }}</span>
            <feather-icon icon="ChevronDownIcon" svgClasses="h-4 w-4" />
          </div>
          <vs-dropdown-menu>
            <vs-dropdown-item v-for="(item, index) in limitSource" :key="index" @click="pagination.limit=item; getData()" >
              <span>{{item}}</span>
            </vs-dropdown-item>
          </vs-dropdown-menu>
        </vs-dropdown>
        <vs-pagination
              style="width: calc(100% - 160px);"
              v-if="Math.ceil(pagination.total / pagination.limit) > 1"
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
        schoolYearOptions:[
          {id:'2023',label:'Năm 2023'},
          {id:'2024',label:'Năm 2024'},
          {id:'2025',label:'Năm 2025'},
          {id:'2026',label:'Năm 2026'},
        ],
        searchData: {
          arr_branch: "",
          branch_id: "",
          school_year: "",
          keyword: "",
          dateRange: "",
          pagination: this.pagination
        },
        datepickerOptions: {
          closed: true,
          value: "",
          minDate: "",
          lang: {
            days: ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
            months: [
              "Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6",
              "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"
            ]
          }
        },
        contracts: [],
        summary: {
          total_summary_sessions: 0,
          total_done_sessions: 0,
          total_left_sessions: 0,
          total_left_value: 0,
        },
        limitSource: [20, 50, 100, 500],
        pagination: {
          url: "/api/lms/reports/18",
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
      // Load branches
      axios.g(`/api/system/branches-has-user`)
        .then(response => {
        this.branch_list = response.data
      })

      this.getData();
    },
    methods: {
      reset() {
        this.searchData.keyword = ""
        this.searchData.arr_branch = ""
        this.searchData.school_year = ""
        this.searchData.branch_id = ""
        this.searchData.dateRange = ""
        this.searchData.pagination = this.pagination
        this.getData();
      },
      getData() {
        const branch_ids = []
        if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
          this.searchData.arr_branch.map(item => {
            branch_ids.push(item.id)
          })
        }
        this.searchData.branch_id = branch_ids

        let start_date = ''
        let end_date = ''
        if(this.searchData.dateRange && this.searchData.dateRange.length == 2){
            start_date = u.dateToString(this.searchData.dateRange[0])
            end_date = u.dateToString(this.searchData.dateRange[1])
        }

        const data = {
            branch_id: this.searchData.branch_id,
            school_year: this.searchData.school_year ? this.searchData.school_year.id : "",
            keyword: this.searchData.keyword,
            start_date: start_date,
            end_date: end_date,
            pagination: this.pagination
          }

        this.$vs.loading()
        axios.p('/api/lms/reports/18', data)
          .then((response) => {
            this.$vs.loading.close()
            this.contracts = response.data.list
            this.summary = response.data.summary || {
              total_summary_sessions: 0,
              total_done_sessions: 0,
              total_left_sessions: 0,
              total_left_value: 0,
            }
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
      exportExcel() {
        let keys = []
        let values = []
        
        // Branch IDs
        if(this.searchData.branch_id && this.searchData.branch_id.length > 0){
          keys.push('branch_id')
          values.push(this.searchData.branch_id.join('-'))
        }

        // School Year
        if(this.searchData.school_year && this.searchData.school_year.id){
          keys.push('school_year')
          values.push(this.searchData.school_year.id)
        }

        // Keyword
        if(this.searchData.keyword){
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

        const link = `/api/lms/exports/report18/${keys.join(',')}/${values.join(',')}?token=${localStorage.getItem("accessToken")}`
        window.open(link, '_blank')
      }
    },
    filters: {
      formatMoney(value) {
        if (!value) return 0;
        return Number(value).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
      },
      formatNumber(value) {
        if (!value) return 0;
        return Number(value).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
      },
      formatDateView(value) {
        if (!value) return '';
        const d = new Date(value);
        if (isNaN(d.getTime())) return value;
        const day = String(d.getDate()).padStart(2, '0');
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const year = d.getFullYear();
        return `${day}/${month}/${year}`;
      }
    }
  }
</script>

<style scoped>
.summary-box {
  background: #f8f9fa;
  padding: 20px;
  border-radius: 8px;
  border: 1px solid #e0e0e0;
}

.summary-item {
  display: flex;
  flex-direction: column;
  padding: 10px;
}

.summary-label {
  font-size: 0.9rem;
  color: #666;
  margin-bottom: 5px;
}

.summary-value {
  font-size: 1.2rem;
  font-weight: bold;
  color: #333;
}

.left-amount-box {
  background: #e8f5e9;
  padding: 10px;
  border-radius: 6px;
  font-size: 1.1rem;
  color: #2e7d32;
}

.text-primary {
  color: #007bff !important;
}

.text-success {
  color: #28a745 !important;
}

.text-danger {
  color: #dc3545 !important;
}

.text-info {
  color: #17a2b8 !important;
}

.text-warning {
  color: #ffc107 !important;
}

.font-bold {
  font-weight: bold;
}

@media only screen and (min-width: 600px) {
  #page-report-18 .vs-table--search {
    max-width: 360px;
  }
  #page-report-18 .vs-table--search-input{
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

.tr-summary td {
  background-color: #fff3cd !important;
  font-weight: bold;
}

.course-name-cell {
  background-color: #FFFF00;
}
</style>
