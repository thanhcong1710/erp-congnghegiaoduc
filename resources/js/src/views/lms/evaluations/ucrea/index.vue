<template>
  <div id="ucrea-eval-list">
    <vx-card no-shadow class="mt-5">
      <div class="flex flex-wrap justify-between items-center mb-5">
        <div>
          <h4 class="font-bold text-xl">Quản lý Đánh giá Năng lực UCREA</h4>
          <p class="text-sm text-gray-500">Đánh giá tư duy sáng tạo, toán học và logic</p>
        </div>
        <div class="flex items-center gap-3">
          <vs-button color="primary" type="filled" icon="add" @click="openCreateModal">
            Khởi tạo bài đánh giá
          </vs-button>
          <div class="flex border rounded overflow-hidden">
            <button
              class="px-4 py-2 text-sm font-medium"
              :class="activeTab === 'completed' ? 'bg-primary text-white' : 'bg-white text-gray-700'"
              @click="changeTab('completed')"
            >
              Đã hoàn thành
            </button>
            <button
              class="px-4 py-2 text-sm font-medium"
              :class="activeTab === 'pending' ? 'bg-warning text-white' : 'bg-white text-gray-700'"
              @click="changeTab('pending')"
            >
              Chưa chấm
            </button>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="vx-row mb-4 filter-row" style="position: relative; z-index: 500;">
        <div class="vx-col sm:w-1/4 w-full mb-2">
          <label class="vs-input--label">Trung tâm</label>
          <multiselect
            placeholder="Chọn trung tâm"
            v-model="selectedBranch"
            :options="branches"
            label="name"
            track-by="id"
            :searchable="true"
            @input="fetchData"
          />
        </div>
        <div class="vx-col sm:w-1/4 w-full mb-2">
          <label class="vs-input--label">Lớp học</label>
          <multiselect
            placeholder="Chọn lớp học"
            v-model="selectedClass"
            :options="filteredClasses"
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
            placeholder="Nhập tên học sinh, giáo viên, bài test..."
            v-model="search"
            @keyup.enter="fetchData"
            icon="search"
          />
        </div>
        <div class="vx-col sm:w-1/6 w-full mb-2 flex items-end">
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
                  <th>Bài kiểm tra</th>
                  <th>Level</th>
                  <th>Học sinh</th>
                  <th>Lớp học</th>
                  <th>Giáo viên</th>
                  <th class="text-center">Ngày kiểm tra</th>
                  <th class="text-center">Trạng thái</th>
                  <th class="text-center">Thao tác</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="loading">
                  <td colspan="9" class="text-center py-6 text-gray-500">Đang tải dữ liệu...</td>
                </tr>
                <tr v-else-if="results.length === 0">
                  <td colspan="9" class="text-center py-6 text-gray-500">Không có dữ liệu bài test nào</td>
                </tr>
                <tr v-for="(item, index) in results" :key="item.id" class="tr-values vs-table--tr">
                  <td class="text-center">{{ (pagination.current_page - 1) * pagination.per_page + index + 1 }}</td>
                  <td><strong>{{ item.test_nm }}</strong></td>
                  <td><vs-chip color="primary">{{ item.level_cd }}</vs-chip></td>
                  <td>
                    <div><strong>{{ item.stu_nm }}</strong></div>
                    <small class="text-gray-500" v-if="item.erp_student_code">{{ item.erp_student_code }}</small>
                  </td>
                  <td>{{ item.erp_class_name || item.class_nm || '-' }}</td>
                  <td>{{ item.memb_nm || '-' }}</td>
                  <td class="text-center">{{ item.eval_dt ? item.eval_dt.substring(0, 10) : '-' }}</td>
                  <td class="text-center">
                    <vs-chip v-if="item.result_cd === 'IS002'" color="success">Đã chấm</vs-chip>
                    <vs-chip v-else color="warning">{{ item.result_cd_nm || 'Chưa nhập' }}</vs-chip>
                  </td>
                  <td class="text-center">
                    <div class="flex justify-center items-center gap-2">
                      <router-link
                        v-if="activeTab === 'pending'"
                        :to="`/lms/evaluations/ucrea/form/${item.id}`"
                        class="btn-action text-primary mr-2"
                        title="Chấm điểm Rubric"
                      >
                        <i class="feather icon-edit text-lg"></i>
                      </router-link>
                      <router-link
                        v-else
                        :to="`/lms/evaluations/ucrea/report/${item.id}`"
                        class="btn-action text-success mr-2"
                        title="Xem báo cáo & Radar chart"
                      >
                        <i class="feather icon-bar-chart-2 text-lg"></i>
                      </router-link>
                      <router-link
                        v-if="activeTab === 'completed'"
                        :to="`/lms/evaluations/ucrea/form/${item.id}`"
                        class="btn-action text-gray-600 mr-2"
                        title="Chấm lại"
                      >
                        <i class="feather icon-edit-2 text-lg"></i>
                      </router-link>
                      <button
                        @click="confirmDelete(item)"
                        class="btn-action text-danger"
                        title="Xóa bài test"
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
          Tổng số: <strong>{{ pagination.total }}</strong> bản ghi
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

    <!-- Create Evaluation Modal -->
    <vs-popup title="Khởi tạo bài đánh giá UCREA mới" :active.sync="showCreateModal">
      <div>
        <div class="mb-4">
          <label class="vs-input--label">1. Chọn Trung tâm (*)</label>
          <multiselect
            placeholder="Chọn trung tâm"
            v-model="createForm.branch"
            :options="branches"
            label="name"
            track-by="id"
          />
        </div>

        <div class="mb-4">
          <label class="vs-input--label">2. Chọn Lớp học</label>
          <multiselect
            placeholder="Chọn lớp học (tùy chọn)"
            v-model="createForm.class"
            :options="modalFilteredClasses"
            label="cls_name"
            track-by="id"
            @input="onModalClassChange"
          />
        </div>

        <div class="mb-4">
          <label class="vs-input--label">3. Chọn Học sinh (*)</label>
          <multiselect
            placeholder="Chọn học sinh"
            v-model="createForm.student"
            :options="modalFilteredStudents"
            label="display_name"
            track-by="student_id"
          />
        </div>

        <div class="mb-4">
          <label class="vs-input--label">4. Chọn Bài kiểm tra (*)</label>
          <multiselect
            placeholder="Chọn bài kiểm tra"
            v-model="createForm.test"
            :options="tests"
            label="test_nm"
            track-by="id"
          />
        </div>

        <div class="mb-4">
          <label class="vs-input--label">5. Giáo viên chấm</label>
          <multiselect
            placeholder="Chọn giáo viên"
            v-model="createForm.teacher"
            :options="teachers"
            label="name"
            track-by="id"
          />
        </div>

        <div class="mb-4">
          <label class="vs-input--label">6. Ngày kiểm tra</label>
          <vs-input type="date" class="w-full" v-model="createForm.eval_dt" />
        </div>

        <div class="flex justify-end gap-3 mt-6">
          <vs-button color="dark" type="border" @click="showCreateModal = false">Hủy</vs-button>
          <vs-button color="primary" @click="submitCreate">Tạo bài đánh giá</vs-button>
        </div>
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
      activeTab: 'completed',
      search: '',
      loading: false,
      results: [],
      branches: [],
      classes: [],
      teachers: [],
      tests: [],
      contracts: [],
      selectedBranch: null,
      selectedClass: null,
      pagination: {
        current_page: 1,
        per_page: 20,
        total: 0
      },
      showCreateModal: false,
      createForm: {
        branch: null,
        class: null,
        student: null,
        test: null,
        teacher: null,
        eval_dt: new Date().toISOString().substring(0, 10)
      }
    }
  },
  computed: {
    filteredClasses() {
      if (!this.selectedBranch) return this.classes
      return this.classes.filter(c => c.branch_id === this.selectedBranch.id)
    },
    modalFilteredClasses() {
      if (!this.createForm.branch) return this.classes
      return this.classes.filter(c => c.branch_id === this.createForm.branch.id)
    },
    modalFilteredStudents() {
      let list = this.contracts
      if (this.createForm.class) {
        list = list.filter(c => c.class_id === this.createForm.class.id)
      }
      return list.map(c => ({
        student_id: c.student_id,
        contract_id: c.contract_id,
        class_id: c.class_id,
        display_name: `${c.student_name} (${c.student_lms_id || c.student_id})`
      }))
    }
  },
  created() {
    this.loadInitData()
    this.fetchData()
  },
  methods: {
    loadInitData() {
      axios.g('/api/lms/evaluations/ucrea/init-data').then(res => {
        if (res && res.data && res.data.data) {
          const d = res.data.data
          this.branches = d.branches || []
          this.classes = d.classes || []
          this.teachers = d.teachers || []
          this.tests = d.tests || []
          this.contracts = d.contracts || []
        }
      })
    },
    fetchData() {
      this.loading = true
      const params = {
        status: this.activeTab,
        search: this.search,
        page: this.pagination.current_page,
        per_page: this.pagination.per_page,
        branch_id: this.selectedBranch ? this.selectedBranch.id : null,
        class_id: this.selectedClass ? this.selectedClass.id : null
      }
      axios.g('/api/lms/evaluations/ucrea/results', params).then(res => {
        this.loading = false
        if (res && res.data && res.data.data) {
          this.results = res.data.data
          if (res.data.pagination) {
            this.pagination.total = res.data.pagination.total
            this.pagination.current_page = res.data.pagination.current_page
          }
        }
      }).catch(() => {
        this.loading = false
      })
    },
    changeTab(tab) {
      this.activeTab = tab
      this.pagination.current_page = 1
      this.fetchData()
    },
    resetFilters() {
      this.search = ''
      this.selectedBranch = null
      this.selectedClass = null
      this.pagination.current_page = 1
      this.fetchData()
    },
    openCreateModal() {
      this.createForm = {
        branch: this.branches[0] || null,
        class: null,
        student: null,
        test: this.tests[0] || null,
        teacher: null,
        eval_dt: new Date().toISOString().substring(0, 10)
      }
      this.showCreateModal = true
    },
    onModalClassChange(cls) {
      if (cls && cls.teacher_id) {
        const found = this.teachers.find(t => t.id === cls.teacher_id)
        if (found) this.createForm.teacher = found
      }
    },
    submitCreate() {
      if (!this.createForm.test) {
        this.$vs.notify({ title: 'Thiếu thông tin', text: 'Vui lòng chọn bài test', color: 'danger' })
        return
      }
      if (!this.createForm.student) {
        this.$vs.notify({ title: 'Thiếu thông tin', text: 'Vui lòng chọn học sinh', color: 'danger' })
        return
      }

      const payload = {
        test_id: this.createForm.test.id,
        student_id: this.createForm.student.student_id,
        contract_id: this.createForm.student.contract_id,
        class_id: this.createForm.class ? this.createForm.class.id : null,
        branch_id: this.createForm.branch ? this.createForm.branch.id : null,
        teacher_id: this.createForm.teacher ? this.createForm.teacher.id : null,
        eval_dt: this.createForm.eval_dt
      }

      axios.p('/api/lms/evaluations/ucrea/results', payload).then(res => {
        if (res && res.data && res.data.status === 'success') {
          this.$vs.notify({ title: 'Thành công', text: 'Khởi tạo bài test thành công', color: 'success' })
          this.showCreateModal = false
          this.activeTab = 'pending'
          this.fetchData()
        }
      }).catch(err => {
        this.$vs.notify({ title: 'Lỗi', text: err.message || 'Không thể tạo bài test', color: 'danger' })
      })
    },
    confirmDelete(item) {
      this.$vs.dialog({
        type: 'confirm',
        color: 'danger',
        title: 'Xác nhận xóa',
        text: `Bạn có chắc muốn xóa bài đánh giá của học sinh ${item.stu_nm}?`,
        acceptText: 'Xóa',
        cancelText: 'Hủy',
        accept: () => {
          axios.del(`/api/lms/evaluations/ucrea/results/${item.id}`).then(() => {
            this.$vs.notify({ title: 'Đã xóa', text: 'Xóa bài test thành công', color: 'success' })
            this.fetchData()
          })
        }
      })
    }
  }
}
</script>

<style>
#ucrea-eval-list .multiselect--active {
  z-index: 10000 !important;
}
#ucrea-eval-list .multiselect__content-wrapper {
  z-index: 10000 !important;
}
</style>

