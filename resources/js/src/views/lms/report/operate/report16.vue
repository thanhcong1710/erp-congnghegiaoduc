
<template>
  <div id="page-report-16">
    <vx-card no-shadow class="mt-5">
      <h5>BÁO CÁO DOANH THU CHƯA PHÂN BỔ THEO TRUNG TÂM</h5>
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

      <!-- Summary Statistics Section -->
      <div class="summary-box mb-6">
        <div class="vx-row">
          <div class="vx-col sm:w-1/4 w-full mb-2">
            <div class="summary-item">
              <span class="summary-label">Tổng số tiền còn lại:</span>
              <span class="summary-value text-primary">{{ summary.total_left_amount | formatMoney }}</span>
            </div>
          </div>
          <div class="vx-col sm:w-1/4 w-full mb-2">
            <div class="summary-item">
              <span class="summary-label">Tổng số tiền đã đóng:</span>
              <span class="summary-value">{{ summary.total_charged | formatMoney }}</span>
            </div>
          </div>
          <div class="vx-col sm:w-1/4 w-full mb-2">
            <div class="summary-item">
              <span class="summary-label">Tổng số tiền nợ:</span>
              <span class="summary-value text-danger">{{ summary.total_debt_amount | formatMoney }}</span>
            </div>
          </div>
          <div class="vx-col sm:w-1/4 w-full mb-2">
            <div class="summary-item">
              <span class="summary-label">Tổng số contracts:</span>
              <span class="summary-value">{{ summary.total_contracts | formatNumber }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Detailed Branch Table -->
      <div class="vs-component vs-con-table stripe vs-table-primary">
        <div class="con-tablex vs-table--content">
          <div class="vs-con-tbody vs-table--tbody ">
            <table class="vs-table vs-table--tbody-table" style="width: 100%">
              <thead class="vs-table--thead">
                <tr>
                  <th class="text-center" style="width: 60px">STT</th>
                  <th>Trung tâm</th>
                  <th class="text-center">Số contracts</th>
                  <th class="text-right">Tổng phải đóng</th>
                  <th class="text-right">Tổng đã đóng</th>
                  <th class="text-right">Tổng nợ</th>
                  <th class="text-right">Tổng số tiền còn lại</th>
                </tr>
              </thead>
              <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in datas" :key="index">
                <td class="td vs-table--td text-center">{{ index + 1 }}</td>
                <td class="td vs-table--td"><strong>{{item.branch_name}}</strong></td>
                <td class="td vs-table--td text-center">{{item.total_contracts | formatNumber}}</td>
                <td class="td vs-table--td text-right">{{item.total_must_charge | formatMoney}}</td>
                <td class="td vs-table--td text-right">{{item.total_charged | formatMoney}}</td>
                <td class="td vs-table--td text-right text-danger">{{item.total_debt_amount | formatMoney}}</td>
                <td class="td vs-table--td text-right">
                  <span class="left-amount-badge">{{item.total_left_amount | formatMoney}}</span>
                </td>
              </tr>
              <!-- Footer Row for Page Totals -->
              <tr class="tr-values vs-table--tr font-bold bg-light" v-if="datas.length > 0">
                 <td class="td vs-table--td text-center" colspan="2">TỔNG CỘNG</td>
                 <td class="td vs-table--td text-center">{{summary.total_contracts | formatNumber}}</td>
                 <td class="td vs-table--td text-right">{{summary.total_must_charge | formatMoney}}</td>
                 <td class="td vs-table--td text-right">{{summary.total_charged | formatMoney}}</td>
                 <td class="td vs-table--td text-right text-danger">{{summary.total_debt_amount | formatMoney}}</td>
                 <td class="td vs-table--td text-right">
                   <span class="left-amount-badge-total">{{summary.total_left_amount | formatMoney}}</span>
                 </td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </vx-card>
  </div>
</template>

<script>
  import axios from '../../../../http/axios.js'
  import Multiselect from "vue-multiselect";
  import DatePicker from "vue2-datepicker";
  import u from '../../../../until/helper.js'

  export default {
    components: { 
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
          dateRange: "",
        },
        datepickerOptions: {
          lang: {
            days: ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
            months: ["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"]
          }
        },
        datas: [],
        summary: {
          total_contracts: 0,
          total_must_charge: 0,
          total_charged: 0,
          total_debt_amount: 0,
          total_left_amount: 0,
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
        this.searchData = {
          arr_branch: "",
          branch_id: "",
          arr_product: "",
          product_id: "",
          arr_status: "",
          status: "",
          dateRange: "",
        }
        this.getData();
      },
      getData() {
        const branch_ids = []
        if (this.searchData.arr_branch && this.searchData.arr_branch.length) {
          this.searchData.arr_branch.map(item => { branch_ids.push(item.id) })
        }
        this.searchData.branch_id = branch_ids

        const product_ids = []
        if (this.searchData.arr_product && this.searchData.arr_product.length) {
          this.searchData.arr_product.map(item => { product_ids.push(item.id) })
        }
        this.searchData.product_id = product_ids

        const status_ids = []
        if (this.searchData.arr_status && this.searchData.arr_status.length) {
          this.searchData.arr_status.map(item => {
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
            start_date: start_date,
            end_date: end_date,
          }

        this.$vs.loading()
        axios.p('/api/lms/reports/16', data)
          .then((response) => {
            this.$vs.loading.close()
            this.datas = response.data.list
            this.summary = response.data.summary
          })
          .catch((error) => {
            console.log(error);
            this.$vs.loading.close();
          })
      },
      exportExcel() {
        let keys = []
        let values = []
        
        if(this.searchData.branch_id && this.searchData.branch_id.length > 0){
          keys.push('branch_id'); values.push(this.searchData.branch_id.join('-'))
        }
        if(this.searchData.product_id && this.searchData.product_id.length > 0){
          keys.push('product_id'); values.push(this.searchData.product_id.join('-'))
        }
        if(this.searchData.status && this.searchData.status.length > 0){
          keys.push('status'); values.push(this.searchData.status.join('-'))
        }
        if(this.searchData.dateRange && this.searchData.dateRange.length == 2){
          keys.push('start_date'); values.push(u.dateToString(this.searchData.dateRange[0]))
          keys.push('end_date'); values.push(u.dateToString(this.searchData.dateRange[1]))
        }

        if (keys.length == 0) { keys.push('k'); values.push('v') }

        const link = `/api/lms/exports/report16/${keys.join(',')}/${values.join(',')}?token=${localStorage.getItem("accessToken")}`
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
      }
    }
  }
</script>

<style scoped>
.summary-box {
  background: #f0f4f8;
  padding: 25px;
  border-radius: 12px;
  border: 1px solid #d1d9e6;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.summary-item {
  display: flex;
  flex-direction: column;
}

.summary-label {
  font-size: 0.85rem;
  color: #5a6b7d;
  font-weight: 500;
  margin-bottom: 8px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.summary-value {
  font-size: 1.5rem;
  font-weight: 800;
  color: #2c3e50;
}

.text-primary { color: #3498db !important; }
.text-danger { color: #e74c3c !important; }

.left-amount-badge {
  background: #ebf5ff;
  color: #007bff;
  padding: 6px 12px;
  border-radius: 20px;
  font-weight: 700;
  border: 1px solid #cce5ff;
}

.left-amount-badge-total {
  background: #28a745;
  color: #ffffff;
  padding: 8px 16px;
  border-radius: 20px;
  font-weight: 800;
  box-shadow: 0 4px 6px rgba(40, 167, 69, 0.2);
}

.bg-light { background-color: #f8f9fa !important; }
.font-bold { font-weight: bold !important; }

.vs-table--thead th {
  background-color: #f1f3f5;
  color: #495057;
  font-weight: 600;
}

.multiselect { z-index: 1000; }
</style>
