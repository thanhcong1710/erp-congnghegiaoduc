<template>
  <div class="student-detail-logs mt-3">
    <!-- Table Container -->
    <div class="table-container-card">
      <div class="table-responsive">
        <table class="detail-data-table">
          <thead>
            <tr>
              <th class="text-center" style="width: 60px">STT</th>
              <th style="width: 170px">Thời gian thực hiện</th>
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
            <tr v-for="(item, index) in logs" :key="index" class="table-row-hover">
              <td class="text-center font-semibold text-gray-500">
                {{ index + 1 + (pagination.cpage - 1) * pagination.limit }}
              </td>
              <td>
                <span class="cell-time-badge">
                  <i class="fa-regular fa-clock text-gray-400 mr-1.5"></i>
                  {{ item.created_at }}
                </span>
              </td>
              <td>
                <span class="cell-user-badge">
                  <i class="fa-regular fa-user text-primary mr-1.5"></i>
                  <span class="font-semibold text-gray-800">{{ item.creator_name || 'Hệ thống' }}</span>
                </span>
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
.table-container-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

.table-responsive {
  width: 100%;
  overflow-x: auto;
}

.detail-data-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  font-size: 14px;
}

.detail-data-table thead th {
  background: #f8fafc;
  color: #475569;
  font-weight: 700;
  font-size: 13.5px;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  padding: 12px 16px;
  border-bottom: 2px solid #e2e8f0;
  white-space: nowrap;
}

.detail-data-table tbody td {
  padding: 12px 16px;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
  font-size: 14px;
  color: #334155;
}

.table-row-hover:hover {
  background-color: #f8fafc;
}

.detail-data-table tbody tr:last-child td {
  border-bottom: none;
}

.cell-time-badge {
  display: inline-flex;
  align-items: center;
  color: #475569;
  font-size: 13.5px;
  background: #f1f5f9;
  padding: 3px 8px;
  border-radius: 4px;
  white-space: nowrap;
}

.cell-user-badge {
  display: inline-flex;
  align-items: center;
  font-size: 13.5px;
}

.cell-content-text {
  font-size: 14px;
  color: #1e293b;
  line-height: 1.5;
}
</style>
