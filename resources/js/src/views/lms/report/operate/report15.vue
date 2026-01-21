
<template>

  <div id="page-report-15">
    <vx-card no-shadow class="mt-5">
      <h5>BÁO CÁO SỐ TIỀN CÒN LẠI THEO CONTRACTS</h5>
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
            <label for="" class="vs-input--label">Sản phẩm</label>
            <multiselect
                name="search_product"
                placeholder="Chọn sản phẩm"
                v-model="searchData.arr_product"
                :options="product_list"
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
            <label for="" class="vs-input--label">Từ khóa</label>
            <vs-input class="w-full" placeholder="Mã HS, Tên HS, Mã contract" v-model="searchData.keyword"></vs-input>
          </div>
          <div class="vx-col sm:w-1/4 w-full mb-4">
            <label for="" class="vs-input--label">Thời gian tạo contract</label>
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
      <div class="summary-box mb-4">
        <div class="vx-row">
          <div class="vx-col sm:w-1/3 w-full mb-2">
            <div class="summary-item">
              <span class="summary-label">Tổng số tiền còn lại:</span>
              <span class="summary-value text-primary">{{ summary.total_left_amount | formatMoney }} VNĐ</span>
            </div>
          </div>
          <div class="vx-col sm:w-1/3 w-full mb-2">
            <div class="summary-item">
              <span class="summary-label">Tổng số tiền đã đóng:</span>
              <span class="summary-value">{{ summary.total_charged_sum | formatMoney }} VNĐ</span>
            </div>
          </div>
          <div class="vx-col sm:w-1/3 w-full mb-2">
            <div class="summary-item">
              <span class="summary-label">Tổng số tiền nợ:</span>
              <span class="summary-value text-danger">{{ summary.total_debt_amount | formatMoney }} VNĐ</span>
            </div>
          </div>
        </div>
        <div class="vx-row">
          <div class="vx-col sm:w-1/3 w-full mb-2">
            <div class="summary-item">
              <span class="summary-label">Tổng buổi còn lại:</span>
              <span class="summary-value">{{ summary.total_left_sessions | formatNumber }} buổi</span>
            </div>
          </div>
          <div class="vx-col sm:w-1/3 w-full mb-2">
            <div class="summary-item">
              <span class="summary-label">Tổng số buổi:</span>
              <span class="summary-value">{{ summary.total_summary_sessions | formatNumber }} buổi</span>
            </div>
          </div>
          <div class="vx-col sm:w-1/3 w-full mb-2">
            <div class="summary-item">
              <span class="summary-label">Tổng contracts:</span>
              <span class="summary-value">{{ pagination.total | formatNumber }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="vs-component vs-con-table stripe vs-table-primary">
        <div class="con-tablex vs-table--content">
          <div class="vs-con-tbody vs-table--tbody ">
            <table class="vs-table vs-table--tbody-table" style="width: 100%">
              <thead class="vs-table--thead">
                <tr>
                  <th class="text-center" style="width: 60px">STT</th>
                  <th>Học sinh</th>
                  <th>Contract</th>
                  <th>Trung tâm / Sản phẩm</th>
                  <th class="text-center">Số buổi</th>
                  <th class="text-right">Tài chính</th>
                  <th class="text-right" style="width: 150px">Số tiền còn lại</th>
                </tr>
              </thead>
              <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in contracts" :key="index">
                <td class="td vs-table--td text-center">{{ index + 1 + (pagination.cpage - 1) * pagination.limit }}</td>
                <td class="td vs-table--td">
                  <p><strong>{{item.student_name}}</strong></p>
                  <p>Mã: {{item.lms_code}}</p>
                  <p>SĐT: {{item.gud_mobile1}}</p>
                </td>
                <td class="td vs-table--td">
                  <p><strong>{{item.contract_code}}</strong></p>
                  <p>Trạng thái: <span :class="getStatusClass(item.status)">{{item.label_status}}</span></p>
                  <p>Lớp: {{item.class_name || 'Chưa xếp lớp'}}</p>
                  <p>Gói phí: {{item.tuition_fee_name}}</p>
                </td>
                <td class="td vs-table--td">
                  <p>{{item.branch_name}}</p>
                  <p>{{item.product_name}}</p>
                  <p>EC: {{item.ec_name}}</p>
                  <p>CM: {{item.cm_name}}</p>
                </td>
                <td class="td vs-table--td text-center">
                  <div style="font-size: 14px;">
                    <span style="font-size: 20px; font-weight: bold; color: #28a745;">{{item.done_sessions}}</span> / 
                    <span style="font-size: 16px;">{{item.summary_sessions}}</span>
                  </div>
                  <div style="font-size: 12px; color: #666;">
                    Còn lại: <strong>{{item.left_sessions}}</strong> buổi
                  </div>
                  <vs-progress v-if="item.summary_sessions" :height="6" :percent="Math.round(item.done_sessions*100 / item.summary_sessions)" color="success"></vs-progress>
                </td>
                <td class="td vs-table--td text-right">
                  <p>Phải đóng: <strong>{{item.must_charge | formatMoney}}</strong></p>
                  <p>Đã đóng: {{item.total_charged | formatMoney}}</p>
                  <p class="text-danger">Nợ: {{item.debt_amount | formatMoney}}</p>
                </td>
                <td class="td vs-table--td text-right">
                  <div class="left-amount-box">
                    <strong>{{item.left_amount | formatMoney}}</strong> VNĐ
                  </div>
                </td>
              </tr>
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
        product_list: [],
        statusOptions:[
          {id:'2',label:'Đặt cọc'},
          {id:'3-4-5',label:'Chờ xếp lớp'},
          {id:'6',label:'Đang học'},
        ],
        searchData: {
          arr_branch: "",
          branch_id: "",
          arr_product: "",
          product_id: "",
          arr_status: "",
          status: "",
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
          total_left_amount: 0,
          total_charged_sum: 0,
          total_must_charge: 0,
          total_debt_amount: 0,
          total_left_sessions: 0,
          total_summary_sessions: 0,
        },
        limitSource: [20, 50, 100, 500],
        pagination: {
          url: "/api/lms/reports/15",
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
      
      // Load products
      axios.g(`/api/system/products`)
        .then(response => {
        this.product_list = response.data
      })

      this.getData();
    },
    methods: {
      reset() {
        this.searchData.keyword = ""
        this.searchData.arr_branch = ""
        this.searchData.arr_product = ""
        this.searchData.arr_status = ""
        this.searchData.branch_id = ""
        this.searchData.product_id = ""
        this.searchData.status = ""
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

        const product_ids = []
        if (this.searchData.arr_product && this.searchData.arr_product.length) {
          this.searchData.arr_product.map(item => {
            product_ids.push(item.id)
          })
        }
        this.searchData.product_id = product_ids

        const status_ids = []
        if (this.searchData.arr_status && this.searchData.arr_status.length) {
          this.searchData.arr_status.map(item => {
            // Nếu là '3-4-5', expand thành [3,4,5]
            if (item.id === '3-4-5') {
              status_ids.push(3, 4, 5)
            } else {
              status_ids.push(parseInt(item.id))
            }
          })
        }
        this.searchData.status = status_ids

        let start_date = ''
        let end_date = ''
        if(this.searchData.dateRange && this.searchData.dateRange.length == 2){
            start_date = u.dateToString(this.searchData.dateRange[0])
            end_date = u.dateToString(this.searchData.dateRange[1])
        }

        const data = {
            branch_id: this.searchData.branch_id,
            product_id: this.searchData.product_id,
            status: this.searchData.status,
            keyword: this.searchData.keyword,
            start_date: start_date,
            end_date: end_date,
            pagination: this.pagination
          }

        this.$vs.loading()
        axios.p('/api/lms/reports/15', data)
          .then((response) => {
            this.$vs.loading.close()
            this.contracts = response.data.list
            this.summary = response.data.summary || {
              total_left_amount: 0,
              total_charged_sum: 0,
              total_must_charge: 0,
              total_debt_amount: 0,
              total_left_sessions: 0,
              total_summary_sessions: 0,
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

        // Product IDs
        if(this.searchData.product_id && this.searchData.product_id.length > 0){
          keys.push('product_id')
          values.push(this.searchData.product_id.join('-'))
        }

        // Status IDs
        if(this.searchData.status && this.searchData.status.length > 0){
          keys.push('status')
          values.push(this.searchData.status.join('-'))
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

        const link = `/api/lms/exports/report15/${keys.join(',')}/${values.join(',')}?token=${localStorage.getItem("accessToken")}`
        window.open(link, '_blank')
      },
      getStatusClass(status) {
        switch(status) {
          case 6: return 'text-success font-bold'
          case 2: return 'text-warning font-bold'
          case 3:
          case 4:
          case 5: return 'text-info font-bold'
          default: return ''
        }
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
  #page-report-15 .vs-table--search {
    max-width: 360px;
  }
  #page-report-15 .vs-table--search-input{
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
