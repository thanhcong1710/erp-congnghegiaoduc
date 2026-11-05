<template>
  <div id="igbh-class-summative-list">
    <vx-card no-shadow class="mt-5">
      <div class="flex flex-wrap justify-between items-center mb-5">
        <div>
          <h4 class="font-bold text-xl">Quản lý Đánh Giá Tổng Kết Lớp (IG.BH Summative)</h4>
          <p class="text-sm text-gray-500">Khởi tạo và quản lý đợt đánh giá tổng kết kỳ cho từng lớp học</p>
        </div>
        <div class="flex items-center gap-3">
          <router-link to="/lms/evaluations/igbh/summative/students">
            <vs-button color="dark" type="border" icon="people">Danh sách theo Học sinh</vs-button>
          </router-link>
          <vs-button color="primary" type="filled" icon="add" @click="openCreateModal">
            Khởi tạo đợt đánh giá tổng kết lớp
          </vs-button>
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
            placeholder="Nhập tên lớp, giáo viên, bài test..."
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
                  <th>Lớp học</th>
                  <th>Bài kiểm tra</th>
                  <th>Giáo viên phụ trách</th>
                  <th class="text-center">Ngày đánh giá</th>
                  <th class="text-center">Trạng thái</th>
                  <th class="text-center">Thao tác</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="loading">
                  <td colspan="7" class="text-center py-6 text-gray-500">Đang tải dữ liệu...</td>
                </tr>
                <tr v-else-if="results.length === 0">
                  <td colspan="7" class="text-center py-6 text-gray-500">Chưa có đợt đánh giá tổng kết nào</td>
                </tr>
                <tr v-for="(item, index) in results" :key="item.id" class="tr-values vs-table--tr">
                  <td class="text-center">{{ (pagination.current_page - 1) * pagination.per_page + index + 1 }}</td>
                  <td><strong>{{ item.erp_class_name || item.class_nm }}</strong></td>
                  <td>{{ item.test_nm }}</td>
                  <td>{{ item.teacher_nm || '-' }}</td>
                  <td class="text-center">{{ item.eval_ymd ? item.eval_ymd.substring(0, 10) : '-' }}</td>
                  <td class="text-center">
                    <vs-chip v-if="item.status === 'completed'" color="success">Đã hoàn thành</vs-chip>
                    <vs-chip v-else color="warning">Bản nháp (Draft)</vs-chip>
                  </td>
                  <td class="text-center">
                    <div class="flex justify-center items-center gap-2">
                      <vs-button
                        size="small"
                        color="primary"
                        icon="list"
                        @click="viewClassStudents(item)"
                      >
                        DS Học sinh
                      </vs-button>
                      <button
                        @click="confirmDelete(item)"
                        class="btn-action text-danger ml-2"
                        title="Xóa đợt đánh giá"
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
          Tổng số: <strong>{{ pagination.total }}</strong> lớp
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

    <!-- Create Modal -->
    <vs-popup title="Khởi tạo đánh giá tổng kết cho Lớp học" :active.sync="showCreateModal">
      <div>
        <div class="mb-4">
          <label class="vs-input--label">1. Chọn Bài kiểm tra Summative (*)</label>
          <multiselect
            placeholder="Chọn bài kiểm tra"
            v-model="createForm.test"
            :options="tests"
            label="test_nm"
            track-by="test_seq"
          />
        </div>

        <div class="mb-4">
          <label class="vs-input--label">2. Chọn Lớp học (*)</label>
          <multiselect
            placeholder="Chọn lớp học"
            v-model="createForm.class"
            :options="classes"
            label="cls_name"
            track-by="id"
          />
        </div>

        <div class="mb-4">
          <label class="vs-input--label">3. Ngày đánh giá</label>
          <vs-input type="date" class="w-full" v-model="createForm.eval_ymd" />
        </div>

        <div class="flex justify-end gap-3 mt-6">
          <vs-button color="dark" type="border" @click="showCreateModal = false">Hủy</vs-button>
          <vs-button color="primary" @click="submitCreate">Khởi tạo</vs-button>
        </div>
      </div>
    </vs-popup>

    <!-- Class Students Detail Modal -->
    <vs-popup title="Danh sách học sinh đánh giá tổng kết của lớp" :active.sync="showClassStudentsModal">
      <div v-if="selectedClassEval">
        <div class="mb-4 p-3 bg-gray-50 rounded flex justify-between items-center">
          <div>
            <strong>Lớp:</strong> {{ selectedClassEval.class_nm }} |
            <strong>Bài kiểm tra:</strong> {{ selectedClassEval.test_nm }}
          </div>
          <div>
            <vs-button
              size="small"
              :color="selectedClassEval.status === 'completed' ? 'warning' : 'success'"
              @click="toggleClassStatus"
            >
              {{ selectedClassEval.status === 'completed' ? 'Đổi về Bản nháp' : 'Chốt hoàn thành' }}
            </vs-button>
          </div>
        </div>

        <table class="w-full text-left border-collapse text-sm mb-4">
          <thead>
            <tr class="bg-gray-100 border-b">
              <th class="p-2 text-center w-12">STT</th>
              <th class="p-2">Học sinh</th>
              <th class="p-2 text-center">Điểm tổng kết</th>
              <th class="p-2 text-center">Trạng thái</th>
              <th class="p-2 text-center">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(stu, idx) in classStudents" :key="stu.id" class="border-b">
              <td class="p-2 text-center">{{ idx + 1 }}</td>
              <td class="p-2 font-semibold">{{ stu.stu_nm }}</td>
              <td class="p-2 text-center font-bold text-primary">{{ stu.total_score || 0 }}</td>
              <td class="p-2 text-center">
                <vs-chip v-if="stu.status === 'completed'" color="success">Đã hoàn thành</vs-chip>
                <vs-chip v-else color="warning">Bản nháp</vs-chip>
              </td>
              <td class="p-2 text-center">
                <router-link :to="`/lms/evaluations/igbh/summative/form/${stu.id}`" class="text-primary mr-2" title="Chấm điểm">
                  <i class="feather icon-edit"></i>
                </router-link>
                <router-link :to="`/lms/evaluations/igbh/summative/report/${stu.id}`" class="text-success" title="Xem báo cáo">
                  <i class="feather icon-bar-chart-2"></i>
                </router-link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </vs-popup>
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
      tests: [],
      selectedClass: null,
      pagination: {
        current_page: 1,
        per_page: 20,
        total: 0
      },
      showCreateModal: false,
      createForm: {
        test: null,
        class: null,
        eval_ymd: new Date().toISOString().substring(0, 10)
      },
      showClassStudentsModal: false,
      selectedClassEval: null,
      classStudents: []
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
          this.tests = res.data.tests || []
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
      axios.g('/api/lms/evaluations/summative/classes', params).then(res => {
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
    openCreateModal() {
      this.createForm = {
        test: this.tests[0] || null,
        class: this.classes[0] || null,
        eval_ymd: new Date().toISOString().substring(0, 10)
      }
      this.showCreateModal = true
    },
    submitCreate() {
      if (!this.createForm.test || !this.createForm.class) {
        this.$vs.notify({ title: 'Thiếu thông tin', text: 'Vui lòng chọn bài test và lớp học', color: 'danger' })
        return
      }

      const payload = {
        test_seq: this.createForm.test.test_seq,
        class_id: this.createForm.class.id,
        eval_ymd: this.createForm.eval_ymd
      }

      axios.p('/api/lms/evaluations/summative/classes', payload).then(res => {
        if (res && res.data && res.data.id) {
          this.$vs.notify({ title: 'Thành công', text: 'Khởi tạo đánh giá lớp thành công', color: 'success' })
          this.showCreateModal = false
          this.fetchData()
        }
      }).catch(err => {
        this.$vs.notify({ title: 'Lỗi', text: err.message || 'Không thể khởi tạo', color: 'danger' })
      })
    },
    viewClassStudents(item) {
      this.selectedClassEval = item
      axios.g(`/api/lms/evaluations/summative/classes/${item.id}`).then(res => {
        if (res && res.data) {
          this.selectedClassEval = res.data.evaluation
          this.classStudents = res.data.results || []
          this.showClassStudentsModal = true
        }
      })
    },
    toggleClassStatus() {
      const newStatus = this.selectedClassEval.status === 'completed' ? 'draft' : 'completed'
      axios.p(`/api/lms/evaluations/summative/classes/${this.selectedClassEval.id}/status`, {
        status: newStatus
      }).then(() => {
        this.selectedClassEval.status = newStatus
        this.classStudents.forEach(s => s.status = newStatus)
        this.$vs.notify({ title: 'Thành công', text: 'Cập nhật trạng thái thành công', color: 'success' })
        this.fetchData()
      })
    },
    confirmDelete(item) {
      this.$vs.dialog({
        type: 'confirm',
        color: 'danger',
        title: 'Xác nhận xóa',
        text: `Bạn có chắc muốn xóa đợt đánh giá tổng kết của lớp ${item.class_nm}?`,
        acceptText: 'Xóa',
        cancelText: 'Hủy',
        accept: () => {
          axios.del(`/api/lms/evaluations/summative/classes/${item.id}`).then(() => {
            this.$vs.notify({ title: 'Đã xóa', text: 'Xóa đợt đánh giá thành công', color: 'success' })
            this.fetchData()
          })
        }
      })
    }
  }
}
</script>

