

<template>

  <div id="page-roles-list">
    <vx-card no-shadow class="mt-5">
      <div class="mb-5">
        <div class="vx-row">
          <div class="vx-col w-full mb-4">
            <label for="" class="vs-input--label">Hệ thống báo cáo sẽ được xây dưng theo yêu cầu của công ty, sao cho phù hợp nhất với nghiệp vụ vận hành</label>
            
          </div>
        </div>
      </div>
      
    </vx-card>
  </div>

</template>

<script>

  import vSelect from 'vue-select'
  import axios from '../../../http/axios.js'
  import Multiselect from "vue-multiselect";
  import u from '../../../until/helper.js';
  import DatePicker from "vue2-datepicker";

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
          dateRange: [],
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
        teachers: [],
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
      let from_date =  new Date();
      this.searchData.dateRange[0] = new Date(from_date.getFullYear(), from_date.getMonth() , 1);
      this.searchData.dateRange[1] = new Date(from_date.getFullYear(), from_date.getMonth() + 1, 1);
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
        let from_date =  new Date();
        this.searchData.dateRange[0] = new Date(from_date.getFullYear(), from_date.getMonth() , 1);
        this.searchData.dateRange[1] = new Date(from_date.getFullYear(), from_date.getMonth() + 1, 1);
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
        const startDate = typeof this.searchData.dateRange != 'undefined' && this.searchData.dateRange!='' && this.searchData.dateRange[0] ?`${u.dateToString(this.searchData.dateRange[0])}`:''
        const endDate = typeof this.searchData.dateRange != 'undefined' && this.searchData.dateRange!='' && this.searchData.dateRange[1] ?`${u.dateToString(this.searchData.dateRange[1])}`:''
        
        const data = {
            keyword: this.searchData.keyword,
            branch_id: this.searchData.branch_id,
            start_date:startDate,
            end_date:endDate,
            pagination:this.pagination,
          }

        this.$vs.loading()
        axios.p('/api/lms/teachers/list', data)
          .then((response) => {
            this.$vs.loading.close()
            this.teachers = response.data.list
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