

<template>

  <div id="page-roles-list">
    <vx-card no-shadow class="mt-5">
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
            <vs-input class="w-full" placeholder="Mã tên học sinh, mã hợp đồng" v-model="searchData.keyword"></vs-input>
          </div>
          <div class="vx-col sm:w-1/4 w-full mb-4">
            <label for="" class="vs-input--label">Thời gian tạo</label>
            <date-picker name="item-date" v-model="searchData.dateRange" range format="YYYY-MM-DD" style="width: 100%"
              :clearable="true" :lang="datepickerOptions.lang" placeholder="Chọn khoảng thời gian tìm kiếm"></date-picker>
          </div>
        </div>
        <div class="vx-row mt-3">
          <div class="vx-col w-full">
            <router-link class="btn btn-success" :to="'/lms/contracts/add'">
              <vs-button class="mr-3 mb-2" color="success"><i class="fa fa-plus"></i> Thêm mới</vs-button>
            </router-link>
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
                  <th colspan="1" rowspan="1" class="text-center">STT</th>
                  <th colspan="1" rowspan="1">Học sinh</th>
                  <th colspan="1" rowspan="1">Trung tâm</th>
                  <th colspan="1" rowspan="1">Hợp đồng</th>
                  <th colspan="1" rowspan="1">Đóng phí</th>
                  <th colspan="1" rowspan="1" class="text-center">Trạng thái</th>
                  <th colspan="1" rowspan="1" class="text-center">Thao tác</th>
                </tr>
              </thead>
              <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in contracts" :key="index">
                <!---->
                
                <td class="td vs-table--td text-center">{{ index + 1 + (pagination.cpage - 1) * pagination.limit }}</td>
                <td class="td vs-table--td">
                  <p><strong>{{ item.name }}</strong></p>
                  <p>Mã: {{item.lms_code}}</p>
                </td>
                <td class="td vs-table--td">
                  <p><strong>{{ item.branch_name }}</strong></p>
                  <p>EC: {{ item.ec_name }}</p>
                  <p>AF: {{ item.cm_name }}</p>
                </td>
                <td class="td vs-table--td">
                  <p>Mã:  <router-link :to="`/lms/contracts/${item.contract_id}/detail`" ><strong>{{ item.code }}</strong></router-link></p>
                  <p>Khóa học: {{ item.product_name }}</p>
                  <p>Gói phí: {{ item.tuition_fee_name }}</p>
                  <p>Số buổi: {{ item.total_sessions }} ({{ item.bonus_sessions }} buổi học bổng)</p>
                </td>
                <td class="td vs-table--td">
                  <p>Giá gốc: <strong>{{ item.init_tuition_fee_amount | formatMoney }}</strong></p>
                  <p>Phải đóng: {{ item.must_charge | formatMoney }}</p>
                  <p>Công nợ: {{ item.debt_amount | formatMoney }}</p>
                </td>
                <td class="td vs-table--td text-center">{{ item.label_status}}</td>
                <td class="td vs-table--td text-center list-action"> 
                    <router-link :to="`/lms/contracts/${item.contract_id}/detail`" >
                      <vs-button size="small"><i class="fa fa-eye"></i></vs-button>
                    </router-link> 
                    <vs-button size="small" style="background: rgb(19 128 213) !important"><i class="fa-solid fa-print"></i></vs-button>
                    <vs-button size="small" color="danger" v-if="(item.total_charged == 0 && item.type==1) || (item.status == 3 && item.type==0)" @click="confirmDelete(item)"><i class="fa-solid fa-trash"></i></vs-button>
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
        branch_list: [],
        searchData: {
          arr_branch: "",
          branch_id:"",
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
        contracts: [],
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
      reset() {
        this.searchData.keyword = ""
        this.searchData.arr_branch= ""
        this.searchData.branch_id= ""
        this.searchData.pagination= this.pagination
        this.searchData.dateRange= ""
        this.getData();
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
            start_date:startDate,
            end_date:endDate,
            pagination:this.pagination,
          }

        this.$vs.loading()
        axios.p('/api/lms/contracts/list', data)
          .then((response) => {
            this.$vs.loading.close()
            this.contracts = response.data.list
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
      confirmDelete (item) {
        this.delete_id = item.contract_id
        this.$vs.dialog({
          type: 'confirm',
          color: 'danger',
          title: 'Thông báo',
          text: `Bạn chắc chắn hủy hợp đồng nhập học - ${item.code}?`,
          accept: this.deleteContract,
          acceptText: 'Xóa',
          cancelText: 'Hủy'
        })
      },
      deleteContract(){
        const data = {
          contract_id: this.delete_id,
        };
        this.$vs.loading();
        axios.p(`/api/lms/contracts/delete`,data)
        .then((response) => {
          this.$vs.loading.close();
          this.getData();
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
        })
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