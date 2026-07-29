<template>
  <div id="page-refund-deposit-list">
    <vx-card no-shadow class="mt-5">
      <h5>BÁO CÁO HOÀN TIỀN CỌC</h5>
      <hr class="mt-2 mb-4" style="border: 0.5px solid #ccc;">
      <div class="mb-5">
        <div class="vx-row">
          <div class="vx-col sm:w-1/4 w-full mb-4">
            <label for="" class="vs-input--label">Từ khóa</label>
            <vs-input class="w-full" placeholder="Mã HS, tên HS, SĐT" v-model="searchData.keyword"></vs-input>
          </div>
          <div class="vx-col sm:w-1/4 w-full mb-4">
            <label for="" class="vs-input--label">Tháng hoàn tiền</label>
            <date-picker name="item-date" v-model="searchData.month" format="YYYY-MM" value-type="format" style="width: 100%" type="month"
              :clearable="true" :lang="datepickerOptions.lang" placeholder="Chọn tháng"></date-picker>
          </div>
        </div>
        <div class="vx-row mt-3">
          <div class="vx-col w-full">
            <vs-button class="mr-3 mb-2" @click="getData"><i class="fa fa-search"></i> Tìm kiếm</vs-button>
            <vs-button color="dark" type="border" class="mr-3 mb-2" @click="resetData"><i class="fas fa-undo-alt"></i> Hủy</vs-button>
            <vs-button color="success" class="mb-2" @click="exportExcel" ><i class="fa fa-file-excel"></i> Export</vs-button>
          </div>
        </div>
      </div>

      <div class="vs-component vs-con-table stripe vs-table-primary">
        <div class="con-tablex vs-table--content">
          <div class="vs-con-tbody vs-table--tbody ">
            <table class="vs-table vs-table--tbody-table">
              <thead class="vs-table--thead">
                <tr>
                  <th class="td-check">
                    <div class="vs-table-text">STT</div>
                  </th>
                  <th><div class="vs-table-text">Mã HS</div></th>
                  <th><div class="vs-table-text">Tên học sinh</div></th>
                  <th><div class="vs-table-text">Số điện thoại</div></th>
                  <th><div class="vs-table-text">Leader Sale</div></th>
                  <th><div class="vs-table-text">Thành viên Sale</div></th>
                  <th><div class="vs-table-text">Số tiền hoàn</div></th>
                  <th><div class="vs-table-text">Ngày hoàn</div></th>
                </tr>
              </thead>
              <tbody>
                <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in listData" :key="index">
                  <td class="td vs-table--td">{{ index + 1 + (pagination.cpage - 1) * pagination.limit }}</td>
                  <td class="td vs-table--td">{{ item.lms_code }}</td>
                  <td class="td vs-table--td">{{ item.student_name }}</td>
                  <td class="td vs-table--td">{{ item.student_phone }}</td>
                  <td class="td vs-table--td">{{ item.ec_leader_name }}</td>
                  <td class="td vs-table--td">{{ item.ec_name }}</td>
                  <td class="td vs-table--td">{{ formatMoneySigned(item.amount) }}</td>
                  <td class="td vs-table--td">{{ item.refund_date | formatDateViewDay }}</td>
                </tr>
              </tbody>
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
          :total="Math.ceil(pagination.total/pagination.limit)"
          v-model="pagination.cpage">
        </vs-pagination>
      </div>
    </vx-card>
  </div>
</template>

<script>
import DatePicker from "vue2-datepicker";
import axios from "@/http/axios.js";

export default {
  components: {
    DatePicker
  },
  data() {
    return {
      searchData: {
        keyword: "",
        month: this.getCurrentMonth()
      },
      listData: [],
      limitSource: [20, 50, 100, 500],
      pagination: {
        url: "/api/lms/reports/refund-deposit",
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
        pages: []
      },
      datepickerOptions: {
        closed: true,
        value: "",
        minDate: "",
        lang: {
          days: ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
          months: ["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"],
          pickers: ["7 ngày tới", "30 ngày tới", "7 ngày trước", "30 ngày trước"]
        }
      }
    };
  },
  watch: {
    "pagination.cpage": function() {
      this.getData();
    },
    "pagination.limit": function() {
      this.getData();
    }
  },
  created() {
    this.getData();
  },
  methods: {
    formatMoneySigned(v, c = 'đ') {
      if (isNaN(v)) return `0${c}`;
      const sign = v < 0 ? '-' : '';
      const absValue = Math.abs(v);
      return `${sign}${parseInt(absValue).toFixed(1).replace(/(\d)(?=(\d{3})+\.)/g, '$1.').slice(0, -2)}${c}`;
    },
    exportExcel() {
      let url = `/api/lms/exports/refund-deposit?`;
      if (this.searchData.keyword) {
        url += `keyword=${encodeURIComponent(this.searchData.keyword)}&`;
      }
      if (this.searchData.month) {
        url += `month=${encodeURIComponent(this.searchData.month)}&`;
      }
      const token = localStorage.getItem("accessToken");
      window.open(url + `token=${token}`, "_blank");
    },
    getCurrentMonth() {
      const now = new Date();
      return now.getFullYear() + "-" + String(now.getMonth() + 1).padStart(2, "0");
    },
    resetData() {
      this.searchData.keyword = "";
      this.searchData.month = this.getCurrentMonth();
      this.pagination.cpage = 1;
      this.getData();
    },
    getData() {
      this.$vs.loading();
      axios.p("/api/lms/reports/refund-deposit", {
          keyword: this.searchData.keyword,
          month: this.searchData.month,
          pagination: this.pagination
        })
        .then(response => {
          this.$vs.loading.close();
          this.listData = response.data.list;
          this.pagination = response.data.paging;
          this.pagination.limit = response.data.paging.limit || 20;
        })
        .catch(error => {
          this.$vs.loading.close();
          console.error(error);
        });
    }
  }
};
</script>
<style>
</style>
