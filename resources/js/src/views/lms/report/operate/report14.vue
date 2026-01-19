
<template>

  <div id="page-report-14">
    <vx-card no-shadow class="mt-5">
      <h5>BÁO CÁO TỔNG QUAN DOANH THU HỌC SINH ĐI HỌC THEO TRUNG TÂM</h5>
      <hr class="mt-2 mb-4" style="border: 0.5px solid #ccc;">
      <div class="mb-5">
        <div class="vx-row">
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
            <vs-button color="success" class="mb-2" @click="exportExcel" ><i class="fa fa-file-excel"></i> Export</vs-button>
          </div>
        </div>
      </div>

      <div class="mb-4 text-right" style="font-size: 1.1rem; font-weight: bold;">
        <span>Tổng doanh thu: {{ totalRevenue | formatMoney }} VNĐ</span>
      </div>

      <div class="vs-component vs-con-table stripe vs-table-primary">
        <div class="con-tablex vs-table--content">
          <div class="vs-con-tbody vs-table--tbody ">
            <table class="vs-table vs-table--tbody-table" style="width: 100%">
              <thead class="vs-table--thead">
                <tr>
                  <th class="text-center">STT</th>
                  <th>Trung tâm</th>
                  <th class="text-center">Tổng số HS</th>
                  <th class="text-center">Số buổi học</th>
                  <th class="text-right">Doanh thu (VNĐ)</th>
                </tr>
              </thead>
              <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in datas" :key="index">
                <td class="td vs-table--td text-center">{{ index + 1 }}</td>
                <td class="td vs-table--td">{{item.branch_name}}</td>
                <td class="td vs-table--td text-center">{{item.total_students}}</td>
                <td class="td vs-table--td text-center">{{item.total_sessions}}</td>
                <td class="td vs-table--td text-right">{{item.total_revenue | formatMoney}}</td>
              </tr>
            </table>
            
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
        searchData: {
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
        totalRevenue: 0,
      }
    },
    created() {
      this.getData();
    },
    methods: {
      reset() {
        this.searchData.dateRange= ""
        this.getData();
      },
      getData() {
        let start_date = ''
        let end_date = ''
        if(this.searchData.dateRange && this.searchData.dateRange.length == 2){
            start_date = u.dateToString(this.searchData.dateRange[0])
            end_date = u.dateToString(this.searchData.dateRange[1])
        }

        const data = {
            start_date: start_date,
            end_date: end_date,
          }

        this.$vs.loading()
        axios.p('/api/lms/reports/14', data)
          .then((response) => {
            this.$vs.loading.close()
            this.datas = response.data.list
            this.totalRevenue = response.data.total_revenue || 0;
          })
          .catch((error) => {
            console.log(error);
            this.$vs.loading.close();
          })
      },
      exportExcel() {
        let keys = []
        let values = []
        
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

        const link = `/api/lms/exports/report14/${keys.join(',')}/${values.join(',')}?token=${localStorage.getItem("accessToken")}`
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
  #page-report-14 .vs-table--search {
    max-width: 360px;
  }
  #page-report-14 .vs-table--search-input{
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
