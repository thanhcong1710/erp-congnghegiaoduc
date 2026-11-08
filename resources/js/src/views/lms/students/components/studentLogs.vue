<template>
  <div class="student-detail-logs mt-3">
    <!-- Bordered Table Container -->
    <div class="bordered-table-container">
      <table class="grid-border-table">
        <thead>
          <tr>
            <th class="text-center" style="width: 60px">STT</th>
            <th style="width: 180px">Thời gian thực hiện</th>
            <th style="width: 220px">Người thực hiện</th>
            <th>Nội dung thay đổi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="!logs || logs.length === 0">
            <td colspan="4" class="text-center py-6 text-gray-400 italic">
              <i class="fa-regular fa-clock text-2xl text-gray-300 block mb-2"></i>
              Chưa có lịch sử thay đổi nào
            </td>
          </tr>
          <tr v-for="(item, index) in logs" :key="index" class="grid-row-hover">
            <td class="text-center font-bold text-gray-500">
              {{ index + 1 + (pagination.cpage - 1) * pagination.limit }}
            </td>
            <td>
              <div class="font-bold text-gray-900 flex items-center">
                <i class="fa-regular fa-clock text-primary mr-1.5"></i>
                {{ item.created_at }}
              </div>
            </td>
            <td>
              <div class="font-bold text-gray-800 flex items-center">
                <i class="fa-regular fa-user text-primary mr-1.5"></i>
                <span>{{ item.creator_name || 'Hệ thống' }}</span>
              </div>
            </td>
            <td>
              <div class="cell-content-text">
                {{ item.content }}
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination (Chuẩn /lms/contracts) -->
    <div class="flex flex-wrap items-center mt-4" v-if="pagination.total > 0">
      <vs-dropdown vs-trigger-click class="cursor-pointer mr-4 items-per-page-handler">
        <div class="p-3 border border-solid d-theme-border-grey-light rounded-full d-theme-dark-bg cursor-pointer flex items-center justify-between font-medium text-sm">
          <span class="mr-2">{{ pagination.total > 0 ? (pagination.cpage * pagination.limit - (pagination.limit - 1)) : 0 }} - {{ pagination.total - pagination.cpage * pagination.limit > 0 ? pagination.cpage * pagination.limit : pagination.total }} of {{ pagination.total }}</span>
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
        v-if="pagination.total && Math.ceil(pagination.total / pagination.limit) > 1"
        :total="Math.ceil(pagination.total / pagination.limit)"
        :max="7"
        v-model="pagination.cpage"
        @change="changePage()"
      />
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
      this.getLogs();
    },
    methods: {
      getLogs(){
        const data = {
            student_id: this.student_info.id,
            pagination:this.pagination,
          }

        this.$vs.loading()
        axios.p('/api/lms/students/logs', data)
          .then((response) => {
            this.$vs.loading.close()
            this.logs = response.data.list
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
          this.getLogs();
        }
      },
      changePageLimit(limit) {
        this.pagination.cpage = 1
        this.pagination.limit = limit
        this.getLogs();
      },
    }
  }
</script>

<style scoped>
.bordered-table-container {
  width: 100%;
  overflow-x: auto;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  background: #ffffff;
}

.grid-border-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
}

.grid-border-table thead th {
  background: #f1f5f9;
  color: #1e293b;
  font-weight: 700;
  font-size: 13.5px;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  padding: 11px 14px;
  border: 1px solid #cbd5e1;
  white-space: nowrap;
}

.grid-border-table tbody td {
  padding: 12px 14px;
  border: 1px solid #cbd5e1;
  vertical-align: middle;
  font-size: 14px;
  color: #334155;
}

.grid-row-hover:hover td {
  background-color: #f8fafc;
}

.cell-content-text {
  font-size: 14px;
  color: #1e293b;
  line-height: 1.5;
}
</style>
