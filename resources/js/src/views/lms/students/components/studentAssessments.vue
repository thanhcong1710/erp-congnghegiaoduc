<template>
  <div class="student-detail-assessments mt-5">
    <div class="vs-component vs-con-table stripe vs-table-primary">
      <div class="con-tablex vs-table--content">
        <div class="vs-con-tbody vs-table--tbody ">
          <table class="vs-table vs-table--tbody-table">
            <thead class="vs-table--thead">
              <tr>
                <!---->
                <th colspan="1" rowspan="1" class="text-center">STT</th>
                <th colspan="1" rowspan="1" class="text-center">Thời gian thực hiện</th>
                <th colspan="1" rowspan="1">Người thực hiện</th>
                <th colspan="1" rowspan="1">Đánh giá & nhận xét</th>
              </tr>
            </thead>
            <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in assessments" :key="index">
              <!---->
              
              <td class="td vs-table--td text-center">{{ index + 1 + (pagination.cpage - 1) * pagination.limit }}</td>
              <td class="td vs-table--td text-center" style="min-width: 150px">{{item.created_at}}</td>
              <td class="td vs-table--td" style="min-width: 200px">{{item.creator_name}}</td>
              <td class="td vs-table--td">
                <p><strong>{{item.title}}</strong></p>
                <div v-html="item.description"></div>
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
  </div>
</template>

<script>
  import axios from '../../../../http/axios.js'
  import moment from 'moment';
  import u from '../../../../until/helper.js'
  import datepicker from "vue2-datepicker";
  import select from 'vue-select'

  export default {
    components: {
      "vue-select": select,
      moment,
      datepicker
    },
    props: {
      student_info: {
        type: Object,
        default: () => {}
      },
    },
    data() {
      return {
        logs:[],
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
    async created() {
      this.getAsssessments();
    },
    methods: {
      getAsssessments(){
        const data = {
            student_id: this.student_info.id,
            pagination:this.pagination,
          }

        this.$vs.loading()
        axios.p('/api/lms/students/assessments', data)
          .then((response) => {
            this.$vs.loading.close()
            this.assessments = response.data.list
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
          this.getAsssessments();
        }
      },
      changePageLimit(limit) {
        this.pagination.cpage = 1
        this.pagination.limit = limit
        this.getAsssessments();
      },
    }
  }
</script>
<style>
.td.vs-table--td{
  vertical-align: top;
}
</style>