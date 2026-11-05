<template>
  <div id="igbh-summative-list">
    <vx-card no-shadow class="mt-5">
      <div class="flex flex-wrap justify-between items-center mb-5">
        <div>
          <h4 class="font-bold text-xl">Đánh Giá Tổng Kết Học Sinh (IG.BH Summative)</h4>
          <p class="text-sm text-gray-500">Danh sách kết quả đánh giá tổng kết kỳ của từng học sinh</p>
        </div>
        <div>
          <router-link to="/lms/evaluations/igbh/summative">
            <vs-button color="primary" type="border" icon="arrow_back">Quản lý theo Lớp học</vs-button>
          </router-link>
        </div>
      </div>

      <!-- Filters -->
      <div class="vx-row mb-4">
        <div class="vx-col sm:w-1/4 w-full mb-2">
          <label class="vs-input--label">Lớp học</label>
          <multiselect
            placeholder="Chọn lớp học"
            v-model="selectedClass"
            :options="classes"
            label="cls_name"
            track-by="id"
            :searchable="true"
            @input="fetchData"
          />
        </div>
        <div class="vx-col sm:w-1/3 w-full mb-2">
          <label class="vs-input--label">Tìm kiếm</label>
          <vs-input
            class="w-full"
            placeholder="Nhập tên học sinh, lớp, bài test..."
            v-model="search"
            @keyup.enter="fetchData"
            icon="search"
          />
        </div>
        <div class="vx-col sm:w-1/4 w-full mb-2 flex items-end">
          <vs-button class="mr-2" @click="fetchData">Tìm kiếm</vs-button>
          <vs-button color="dark" type="border" @click="resetFilters">Đặt lại</vs-button>
        </div>
      </div>

      <!-- Table -->
      <div class="vs-component vs-con-table stripe vs-table-primary">
        <div class="con-tablex vs-table--content">
          <div class="vs-con-tbody vs-table--tbody">
            <table class="vs-table vs-table--tbody-table">
              <thead class="vs-table--thead">
                <tr>
                  <th class="text-center">STT</th>
                  <th>Học sinh</th>
                  <th>Lớp học</th>
                  <th>Bài kiểm tra</th>
                  <th>Giáo viên</th>
                  <th class="text-center">Tổng điểm</th>
                  <th class="text-center">Ngày đánh giá</th>
                  <th class="text-center">Trạng thái</th>
                  <th class="text-center">Thao tác</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="loading">
                  <td colspan="9" class="text-center py-6 text-gray-500">Đang tải dữ liệu...</td>
                </tr>
                <tr v-else-if="results.length === 0">
                  <td colspan="9" class="text-center py-6 text-gray-500">Không có dữ liệu đánh giá học sinh</td>
                </tr>
                <tr v-for="(item, index) in results" :key="item.id" class="tr-values vs-table--tr">
                  <td class="text-center">{{ (pagination.current_page - 1) * pagination.per_page + index + 1 }}</td>
                  <td>
                    <strong>{{ item.stu_nm }}</strong>
                    <div class="text-xs text-gray-400" v-if="item.erp_student_code">{{ item.erp_student_code }}</div>
                  </td>
                  <td>{{ item.erp_class_name || item.class_nm || '-' }}</td>
                  <td>{{ item.test_nm }}</td>
                  <td>{{ item.teacher_nm || '-' }}</td>
                  <td class="text-center font-bold text-primary">{{ item.total_score || 0 }}</td>
                  <td class="text-center">{{ item.eval_dt ? item.eval_dt.substring(0, 10) : '-' }}</td>
                  <td class="text-center">
                    <vs-chip v-if="item.status === 'completed'" color="success">Đã hoàn thành</vs-chip>
                    <vs-chip v-else color="warning">Bản nháp</vs-chip>
                  </td>
                  <td class="text-center">
                    <div class="flex justify-center items-center gap-2">
                      <router-link
                        :to="`/lms/evaluations/igbh/summative/form/${item.id}`"
                        class="btn-action text-primary mr-2"
                        title="Chấm điểm bài thi"
                      >
                        <i class="feather icon-edit text-lg"></i>
                      </router-link>
                      <router-link
                        :to="`/lms/evaluations/igbh/summative/report/${item.id}`"
                        class="btn-action text-success mr-2"
                        title="Báo cáo 12 tuần & Radar chart"
                      >
                        <i class="feather icon-bar-chart-2 text-lg"></i>
                      </router-link>
                      <button
                        @click="confirmDelete(item)"
                        class="btn-action text-danger"
                        title="Xóa đánh giá"
                      >
                        <i class="feather icon-trash-2 text-lg"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div class="flex flex-wrap items-center justify-between mt-5">
        <div class="text-sm text-gray-500">
          Tổng số: <strong>{{ pagination.total }}</strong> kết quả
        </div>
        <vs-pagination
          v-if="Math.ceil(pagination.total / pagination.per_page) > 1"
          :total="Math.ceil(pagination.total / pagination.per_page)"
          :max="7"
          v-model="pagination.current_page"
          @change="fetchData"
        />
      </div>
    </vx-card>
  </div>
</template>

<script>
import axios from '@/http/axios.js'
import multiselect from 'vue-multiselect'

export default {
  components: {
    multiselect
  },
  data() {
    return {
      search: '',
      loading: false,
      results: [],
      classes: [],
      selectedClass: null,
      pagination: {
        current_page: 1,
        per_page: 20,
        total: 0
      }
    }
  },
  created() {
    this.loadInitData()
    this.fetchData()
  },
  methods: {
    loadInitData() {
      axios.g('/api/lms/evaluations/summative/classes/init-data').then(res => {
        if (res && res.data) {
          this.classes = res.data.classes || []
        }
      })
    },
    fetchData() {
      this.loading = true
      const params = {
        search: this.search,
        page: this.pagination.current_page,
        per_page: this.pagination.per_page,
        class_id: this.selectedClass ? this.selectedClass.id : null
      }
      axios.g('/api/lms/evaluations/summative/results', params).then(res => {
        this.loading = false
        if (res && res.data) {
          this.results = res.data.data || []
          this.pagination.total = res.data.total || 0
          this.pagination.current_page = res.data.current_page || 1
        }
      }).catch(() => {
        this.loading = false
      })
    },
    resetFilters() {
      this.search = ''
      this.selectedClass = null
      this.pagination.current_page = 1
      this.fetchData()
    },
    confirmDelete(item) {
      this.$vs.dialog({
        type: 'confirm',
        color: 'danger',
        title: 'Xác nhận xóa',
        text: `Bạn có chắc muốn xóa đánh giá tổng kết của ${item.stu_nm}?`,
        acceptText: 'Xóa',
        cancelText: 'Hủy',
        accept: () => {
          axios.del(`/api/lms/evaluations/summative/results/${item.id}`).then(() => {
            this.$vs.notify({ title: 'Đã xóa', text: 'Xóa thành công', color: 'success' })
            this.fetchData()
          })
        }
      })
    }
  }
}
</script>

