<template>
  <div id="page-teacher-payroll">
    <vx-card no-shadow class="mt-5">
      <div class="mb-5">
        <div class="vx-row">
          <div class="vx-col sm:w-1/2 w-full mb-4">
            <label for="" class="vs-input--label">Từ khóa</label>
            <vs-input class="w-full" placeholder="Nhập tên giáo viên, mã nhân viên, lớp" v-model="searchData.keyword"></vs-input>
          </div>
          <div class="vx-col sm:w-1/2 w-full mb-4">
            <label for="" class="vs-input--label">Thời gian tính lương</label>
            <date-picker name="item-date" v-model="searchData.dateRange" range format="YYYY-MM-DD" style="width: 100%"
              :clearable="true" :lang="datepickerOptions.lang" placeholder="Chọn khoảng thời gian"></date-picker>
          </div>
        </div>
        <div class="vx-row mt-3">
          <div class="vx-col w-full">
            <vs-button class="mr-3 mb-2" @click="getData"><i class="fa fa-search"></i> Tìm kiếm</vs-button>
            <vs-button color="success" class="mr-3 mb-2" @click="exportExcel"><i class="fa fa-file-excel"></i> Xuất Excel</vs-button>
            <vs-button color="dark" type="border" class="mb-2" @click="reset"><i class="fas fa-undo-alt"></i> Hủy</vs-button>
          </div>
        </div>
      </div>

      <div class="vs-component vs-con-table stripe vs-table-primary">
        <div class="con-tablex vs-table--content">
          <div class="vs-con-tbody vs-table--tbody ">
            <table class="vs-table vs-table--tbody-table">
              <thead class="vs-table--thead">
                <tr>
                  <th class="text-center">STT</th>
                  <th>Tên giáo viên</th>
                  <th>Mã nhân viên</th>
                  <th>Lớp</th>
                  <th class="text-center">Số buổi</th>
                  <th class="text-right">Lương giáo viên</th>
                </tr>
              </thead>
              <tr class="tr-values vs-table--tr" v-for="(item, index) in listData" :key="index">
                <td class="td vs-table--td text-center">{{ index + 1 + (pagination.cpage - 1) * pagination.limit }}</td>
                <td class="td vs-table--td">{{item.teacher_name}}</td>
                <td class="td vs-table--td">{{item.teacher_code}}</td>
                <td class="td vs-table--td">{{item.class_name}}</td>
                <td class="td vs-table--td text-center">{{item.total_sessions}}</td>
                <td class="td vs-table--td text-right font-medium text-success">{{item.salary | formatMoney}} VNĐ</td>
              </tr>
              <tr v-if="listData.length > 0" class="tr-values vs-table--tr" style="background: rgba(var(--vs-primary),.1);">
                <td colspan="5" class="td vs-table--td text-center font-bold">TỔNG</td>
                <td class="td vs-table--td text-right font-bold text-success">{{ total_salary | formatMoney }} VNĐ</td>
              </tr>
              <tr v-if="listData.length === 0">
                <td colspan="6" class="text-center p-5">Không có dữ liệu</td>
              </tr>
            </table>
          </div>
        </div>
      </div>
      <div class="flex flex-wrap items-center mt-5" v-if="listData.length > 0">
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
import DatePicker from "vue2-datepicker";
import axios from '../../../../http/axios.js'
import u from '../../../../until/helper.js';

export default {
  components: {
    DatePicker
  },
  data() {
    return {
      searchData: {
        keyword: "",
        dateRange: [],
      },
      listData: [],
      total_salary: 0,
      limitSource: [20, 50, 100, 500],
      pagination: {
        cpage: 1,
        total: 0,
        limit: 20,
        init: 0
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
    };
  },
  created() {
    this.resetDate();
    this.getData();
  },
  methods: {
    resetDate() {
      let from_date = new Date();
      this.searchData.dateRange = [
        new Date(from_date.getFullYear(), from_date.getMonth(), 1),
        new Date()
      ];
    },
    reset() {
      this.searchData.keyword = "";
      this.resetDate();
      this.pagination.cpage = 1;
      this.getData();
    },
    getData() {
      const startDate = typeof this.searchData.dateRange != 'undefined' && this.searchData.dateRange != '' && this.searchData.dateRange[0] ? `${u.dateToString(this.searchData.dateRange[0])}` : '';
      const endDate = typeof this.searchData.dateRange != 'undefined' && this.searchData.dateRange != '' && this.searchData.dateRange[1] ? `${u.dateToString(this.searchData.dateRange[1])}` : '';

      const data = {
        keyword: this.searchData.keyword,
        start_date: startDate,
        end_date: endDate,
        pagination: this.pagination,
      };

      this.$vs.loading();
      axios.p('/api/lms/reports/teacher-payroll', data)
        .then((response) => {
          this.$vs.loading.close();
          this.listData = response.data.list;
          this.total_salary = response.data.total_salary;
          this.pagination = response.data.paging;
          setTimeout(() => {
            this.pagination.init = 1;
          }, 500);
        })
        .catch((error) => {
          console.log(error);
          this.$vs.loading.close();
        });
    },
    changePage() {
      if (this.pagination.init) {
        this.getData();
      }
    },
    exportExcel() {
      const startDate = typeof this.searchData.dateRange != 'undefined' && this.searchData.dateRange != '' && this.searchData.dateRange[0] ? `${u.dateToString(this.searchData.dateRange[0])}` : '';
      const endDate = typeof this.searchData.dateRange != 'undefined' && this.searchData.dateRange != '' && this.searchData.dateRange[1] ? `${u.dateToString(this.searchData.dateRange[1])}` : '';
      
      let url = `/api/lms/exports/teacher-payroll?keyword=${this.searchData.keyword}&start_date=${startDate}&end_date=${endDate}&token=${localStorage.getItem('accessToken')}`;
      window.open(url, '_blank');
    }
  },
  filters: {
    formatMoney(val) {
      if (!val) return '0';
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
  }
};
</script>

<style scoped>
th .vs-table-text {
  display: contents;
}
</style>
