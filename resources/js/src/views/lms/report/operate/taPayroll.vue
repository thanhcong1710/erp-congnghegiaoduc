<template>
  <div id="page-ta-payroll">
    <vx-card no-shadow class="mt-5">
      <div class="mb-5" style="position: relative; z-index: 99;">
        <div class="vx-row">
          <div :class="is_ta ? 'vx-col sm:w-1/2 w-full mb-4' : 'vx-col sm:w-1/3 w-full mb-4'">
            <label class="vs-input--label">Lớp</label>
            <vs-input class="w-full" placeholder="Nhập tên lớp..." v-model="searchData.class_name" @keyup.enter="getData()"></vs-input>
          </div>
          <div class="vx-col sm:w-1/3 w-full mb-4" v-if="!is_ta">
            <label class="vs-input--label">Trợ giảng</label>
            <multiselect
              v-model="searchData.ta"
              :options="ta_list"
              label="display_name"
              track-by="id"
              :searchable="true"
              :close-on-select="true"
              placeholder="Chọn hoặc tìm trợ giảng..."
              selectedLabel=""
              selectLabel=""
              deselectLabel=""
            >
              <span slot="noResult">Không tìm thấy trợ giảng</span>
            </multiselect>
          </div>
          <div :class="is_ta ? 'vx-col sm:w-1/2 w-full mb-4' : 'vx-col sm:w-1/3 w-full mb-4'">
            <label class="vs-input--label">Thời gian tính lương</label>
            <date-picker name="item-date" v-model="searchData.month" type="month" format="YYYY-MM" style="width: 100%"
              :clearable="true" :lang="datepickerOptions.lang" placeholder="Chọn tháng"></date-picker>
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

      <!-- KPI Summary Cards -->
      <div class="vx-row mb-6" v-if="summary">
        <div class="vx-col sm:w-1/4 w-full mb-2">
          <div class="summary-box bg-primary-light">
            <div class="summary-title">Tổng trợ giảng</div>
            <div class="summary-value text-primary">{{ summary.total_tas || 0 }}</div>
          </div>
        </div>
        <div class="vx-col sm:w-1/4 w-full mb-2">
          <div class="summary-box bg-info-light">
            <div class="summary-title">Buổi BT nhóm / 1:1</div>
            <div class="summary-value text-info">{{ summary.total_group_sessions || 0 }} / {{ summary.total_one_on_one_sessions || 0 }}</div>
          </div>
        </div>
        <div class="vx-col sm:w-1/4 w-full mb-2">
          <div class="summary-box bg-warning-light">
            <div class="summary-title">Tổng số buổi</div>
            <div class="summary-value text-warning">{{ summary.total_sessions || 0 }}</div>
          </div>
        </div>
        <div class="vx-col sm:w-1/4 w-full mb-2">
          <div class="summary-box bg-success-light">
            <div class="summary-title">Tổng thù lao</div>
            <div class="summary-value text-success">{{ summary.total_salary | formatMoney }} VNĐ</div>
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
                  <th>Tên trợ giảng</th>
                  <th>Mã nhân viên</th>
                  <th>Lớp</th>
                  <th>Khóa học</th>
                  <th class="text-center">Số buổi BT nhóm</th>
                  <th class="text-center">Số buổi BT 1:1</th>
                  <th class="text-center">Tổng số buổi</th>
                  <th class="text-right">Đơn giá / buổi</th>
                  <th class="text-right">Lương theo lớp</th>
                  <th class="text-right">Lương Tổng</th>
                </tr>
              </thead>
              <tr class="tr-values vs-table--tr" v-for="(item, index) in listData" :key="index">
                <td class="td vs-table--td text-center">{{ index + 1 + (pagination.cpage - 1) * pagination.limit }}</td>
                <td class="td vs-table--td font-semibold">{{ item.ta_name }}</td>
                <td class="td vs-table--td">{{ item.ta_code }}</td>
                <td class="td vs-table--td font-medium">{{ item.class_name }}</td>
                <td class="td vs-table--td">{{ item.product_name }}</td>
                <td class="td vs-table--td text-center font-medium">{{ item.group_sessions }}</td>
                <td class="td vs-table--td text-center font-medium">{{ item.one_on_one_sessions }}</td>
                <td class="td vs-table--td text-center font-bold text-primary">{{ item.total_sessions }}</td>
                <td class="td vs-table--td text-right font-medium">{{ item.unit_price | formatMoney }} VNĐ</td>
                <td class="td vs-table--td text-right font-medium text-success">{{ item.salary | formatMoney }} VNĐ</td>
                <td class="td vs-table--td text-right font-bold text-success">{{ item.ta_total_salary | formatMoney }} VNĐ</td>
              </tr>
              <tr v-if="listData.length === 0">
                <td colspan="11" class="text-center p-5">Không có dữ liệu</td>
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
            <vs-dropdown-item v-for="(item, index) in limitSource" :key="index" @click="changePageLimit(item)">
              <span>{{ item }}</span>
            </vs-dropdown-item>
          </vs-dropdown-menu>
        </vs-dropdown>
        <vs-pagination
          style="width: calc(100% - 160px);"
          v-if="Math.ceil(pagination.total / pagination.limit) > 1"
          :total="Math.ceil(pagination.total / pagination.limit)"
          :max="7"
          v-model="pagination.cpage"
          @change="changePage()"/>
      </div>
    </vx-card>
  </div>
</template>

<script>
import DatePicker from "vue2-datepicker";
import Multiselect from 'vue-multiselect';
import 'vue-multiselect/dist/vue-multiselect.min.css';
import axios from '../../../../http/axios.js';
import u from '../../../../until/helper.js';

export default {
  components: {
    DatePicker,
    Multiselect
  },
  data() {
    return {
      searchData: {
        class_name: "",
        ta: null,
        month: ""
      },
      ta_list: [],
      is_ta: false,
      listData: [],
      summary: null,
      limitSource: [20, 50, 100, 500],
      pagination: {
        cpage: 1,
        total: 0,
        limit: 50,
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
      }
    };
  },
  created() {
    axios.g('/api/lms/teaching-assistants/all-active').then(r => {
      this.ta_list = (r.data || []).map(item => ({
        id: item.id,
        full_name: item.full_name,
        hrm_id: item.hrm_id,
        display_name: item.hrm_id ? `${item.full_name} (${item.hrm_id})` : item.full_name
      }));
    });
    this.resetDate();
    this.getData();
  },
  methods: {
    resetDate() {
      this.searchData.month = new Date();
    },
    reset() {
      this.searchData.class_name = "";
      this.searchData.ta = null;
      this.resetDate();
      this.pagination.cpage = 1;
      this.getData();
    },
    changePageLimit(limit) {
      this.pagination.cpage = 1;
      this.pagination.limit = limit;
      this.getData();
    },
    getData() {
      let startDate = '';
      let endDate = '';
      if (this.searchData.month) {
        const d = new Date(this.searchData.month);
        const y = d.getFullYear();
        const m = d.getMonth();
        const firstDay = new Date(y, m, 1);
        const lastDay = new Date(y, m + 1, 0);
        startDate = u.dateToString(firstDay);
        endDate = u.dateToString(lastDay);
      }

      const ta_id = this.searchData.ta ? this.searchData.ta.id : '';

      const data = {
        class_name: this.searchData.class_name,
        ta_id: ta_id,
        start_date: startDate,
        end_date: endDate,
        pagination: this.pagination
      };

      this.$vs.loading();
      axios.p('/api/lms/reports/ta-payroll', data)
        .then((response) => {
          this.$vs.loading.close();
          this.listData = response.data.list;
          this.summary = response.data.summary;
          this.pagination = response.data.paging;
          this.is_ta = response.data.is_ta || false;
          setTimeout(() => {
            this.pagination.init = 1;
          }, 500);
        })
        .catch((error) => {
          console.error(error);
          this.$vs.loading.close();
        });
    },
    changePage() {
      if (this.pagination.init) {
        this.getData();
      }
    },
    exportExcel() {
      let startDate = '';
      let endDate = '';
      if (this.searchData.month) {
        const d = new Date(this.searchData.month);
        const y = d.getFullYear();
        const m = d.getMonth();
        const firstDay = new Date(y, m, 1);
        const lastDay = new Date(y, m + 1, 0);
        startDate = u.dateToString(firstDay);
        endDate = u.dateToString(lastDay);
      }

      const ta_id = this.searchData.ta ? this.searchData.ta.id : '';
      let url = `/api/lms/exports/ta-payroll?class_name=${encodeURIComponent(this.searchData.class_name)}&ta_id=${ta_id}&start_date=${startDate}&end_date=${endDate}&token=${localStorage.getItem('accessToken')}`;
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
.summary-box {
  padding: 16px;
  border-radius: 10px;
  text-align: center;
}
.summary-title {
  font-size: 0.85rem;
  font-weight: 600;
  color: #6b7280;
  text-transform: uppercase;
  margin-bottom: 6px;
}
.summary-value {
  font-size: 1.35rem;
  font-weight: 700;
}
.bg-primary-light {
  background: rgba(115, 103, 240, 0.1);
}
.bg-info-light {
  background: rgba(0, 207, 232, 0.1);
}
.bg-warning-light {
  background: rgba(255, 159, 67, 0.1);
}
.bg-success-light {
  background: rgba(40, 199, 111, 0.1);
}
</style>
