<template>
  <div class="student-detail-logs mt-2">
    <div class="table-responsive rounded border border-slate-200">
      <table class="w-full bg-white modern-logs-table">
        <thead class="bg-slate-50 text-gray-700 font-bold border-b border-slate-200 uppercase modern-logs-thead">
          <tr>
            <th class="py-2.5 px-3 text-center" style="width: 50px">STT</th>
            <th class="py-2.5 px-3" style="width: 140px">Thời gian thực hiện</th>
            <th class="py-2.5 px-3" style="width: 160px">Người thực hiện</th>
            <th class="py-2.5 px-3">Nội dung thay đổi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-if="!logs || logs.length === 0">
            <td colspan="4" class="text-center py-4 text-gray-400 italic">
              Chưa có lịch sử thay đổi nào
            </td>
          </tr>
          <tr v-for="(item, index) in logs" :key="index" class="hover:bg-slate-50">
            <td class="py-2 px-3 text-center text-gray-500 font-semibold">
              {{ index + 1 + (pagination.cpage - 1) * pagination.limit }}
            </td>
            <td class="py-2 px-3 text-gray-600">
              <i class="fa-regular fa-clock text-gray-400 mr-1"></i>
              {{ item.created_at }}
            </td>
            <td class="py-2 px-3 font-semibold text-gray-800">
              <i class="fa-regular fa-user text-primary mr-1"></i>
              {{ item.creator_name }}
            </td>
            <td class="py-2 px-3 text-gray-700">
              {{ item.content }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Compact Pagination -->
    <div class="compact-pagination-bar" v-if="logs && logs.length > 0">
      <div class="compact-pagination-info">
        <span class="text-gray-700 whitespace-nowrap">Hiển thị:</span>
        <select class="compact-page-select" v-model="pagination.limit" @change="changePageLimit(pagination.limit)">
          <option v-for="(limit, idx) in limitSource" :key="idx" :value="limit">{{ limit }}</option>
        </select>
        <span class="text-gray-500 whitespace-nowrap">
          ({{ pagination.total > 0 ? (pagination.cpage * pagination.limit - (pagination.limit - 1)) : 0 }} - {{ Math.min(pagination.total || 0, pagination.cpage * pagination.limit) }} / {{ pagination.total || 0 }})
        </span>
      </div>

      <vs-pagination
        v-if="Math.ceil(pagination.total / pagination.limit) > 1"
        :total="Math.ceil(pagination.total / pagination.limit)"
        :max="7"
        v-model="pagination.cpage"
        @change="changePage"
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
.modern-logs-table {
  font-size: 14px;
}

.modern-logs-thead {
  font-size: 13.5px;
}

.table-responsive {
  width: 100%;
  overflow-x: auto;
}

.compact-pagination-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
  margin-top: 10px;
  padding-top: 8px;
  border-top: 1px solid #e2e8f0;
  font-size: 14px;
}

.compact-pagination-info {
  display: inline-flex;
  align-items: center;
  white-space: nowrap;
  flex-shrink: 0;
  gap: 8px;
}

.compact-page-select {
  height: 32px;
  padding: 2px 8px;
  border: 1px solid #cbd5e1;
  border-radius: 4px;
  font-size: 14px;
  background: #ffffff;
  color: #334155;
  outline: none;
}
</style>
