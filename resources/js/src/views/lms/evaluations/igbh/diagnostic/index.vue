<template>
  <div id="igbh-diagnostic-list">
    <vx-card no-shadow class="mt-5">
      <div class="flex flex-wrap justify-between items-center mb-5">
        <div>
          <h4 class="font-bold text-xl">Quản lý Khảo Sát Đầu Vào (IG.BH Diagnostic)</h4>
          <p class="text-sm text-gray-500">Khảo sát năng lực chẩn đoán, xếp lớp và phân bậc học sinh</p>
        </div>
        <div>
          <vs-button color="primary" type="filled" icon="add" @click="openCreateModal">
            Khởi tạo khảo sát đầu vào
          </vs-button>
        </div>
      </div>

      <!-- Filters -->
      <div class="vx-row mb-4">
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
        <div class="vx-col sm:w-1/3 w-full mb-2">
          <label class="vs-input--label">Tìm kiếm</label>
          <vs-input
            class="w-full"
            placeholder="Nhập tên học sinh, người đăng ký, bài test..."
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
                  <th>Bài kiểm tra</th>
                  <th>Học sinh</th>
                  <th>Người chấm / Đăng ký</th>
                  <th class="text-center">Điểm môn học</th>
                  <th class="text-center">Điểm tư duy</th>
                  <th class="text-center">Tổng điểm</th>
                  <th class="text-center">Level gán</th>
                  <th class="text-center">Ngày thi</th>
                  <th class="text-center">Thao tác</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="loading">
                  <td colspan="10" class="text-center py-6 text-gray-500">Đang tải dữ liệu...</td>
                </tr>
                <tr v-else-if="results.length === 0">
                  <td colspan="10" class="text-center py-6 text-gray-500">Không có kết quả khảo sát nào</td>
                </tr>
                <tr v-for="(item, index) in results" :key="item.id" class="tr-values vs-table--tr">
                  <td class="text-center">{{ (pagination.current_page - 1) * pagination.per_page + index + 1 }}</td>
                  <td><strong>{{ item.test_nm }}</strong></td>
                  <td>
                    <div><strong>{{ item.stu_nm }}</strong></div>
                    <small class="text-gray-500" v-if="item.erp_student_code">{{ item.erp_student_code }}</small>
                  </td>
                  <td>{{ item.reg_name || '-' }}</td>
                  <td class="text-center font-semibold text-gray-800">{{ item.subject_total || 0 }}</td>
                  <td class="text-center font-semibold text-gray-800">{{ item.thinking_total || 0 }}</td>
                  <td class="text-center font-bold text-primary">{{ item.total_score || 0 }}</td>
                  <td class="text-center">
                    <vs-chip v-if="item.assigned_level" color="primary">{{ item.assigned_level }}</vs-chip>
                    <span v-else class="text-gray-400">-</span>
                  </td>
                  <td class="text-center">{{ item.eval_dt ? item.eval_dt.substring(0, 10) : '-' }}</td>
                  <td class="text-center">
                    <div class="flex justify-center items-center gap-2">
                      <router-link
                        :to="`/lms/evaluations/igbh/diagnostic/form/${item.id}`"
                        class="btn-action text-primary mr-2"
                        title="Chấm điểm / Cập nhật"
                      >
                        <i class="feather icon-edit text-lg"></i>
                      </router-link>
                      <button
                        @click="confirmDelete(item)"
                        class="btn-action text-danger"
                        title="Xóa kết quả"
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
          Tổng số: <strong>{{ pagination.total }}</strong> bài khảo sát
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
    <vs-popup title="Khởi tạo khảo sát đầu vào IG.BH" :active.sync="showCreateModal">
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
          <label class="vs-input--label">2. Chọn Học sinh (*)</label>
          <multiselect
            placeholder="Chọn học sinh"
            v-model="createForm.student"
            :options="modalFilteredStudents"
            label="display_name"
            track-by="student_id"
          />
        </div>

        <div class="mb-4">
          <label class="vs-input--label">3. Chọn Bài kiểm tra (*)</label>
          <multiselect
            placeholder="Chọn bài kiểm tra"
            v-model="createForm.test"
            :options="tests"
            label="test_nm"
            track-by="test_seq"
          />
        </div>

        <div class="mb-4">
          <label class="vs-input--label">4. Giáo viên / Người chấm</label>
          <multiselect
            placeholder="Chọn giáo viên"
            v-model="createForm.teacher"
            :options="teachers"
            label="name"
            track-by="id"
          />
        </div>

        <div class="mb-4">
          <label class="vs-input--label">5. Ngày khảo sát</label>
          <vs-input type="date" class="w-full" v-model="createForm.eval_dt" />
        </div>

        <div class="flex justify-end gap-3 mt-6">
          <vs-button color="dark" type="border" @click="showCreateModal = false">Hủy</vs-button>
          <vs-button color="primary" @click="submitCreate">Tạo khảo sát</vs-button>
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
      search: '',
      loading: false,
      results: [],
      branches: [],
      teachers: [],
      tests: [],
      contracts: [],
      selectedBranch: null,
      pagination: {
        current_page: 1,
        per_page: 20,
        total: 0
      },
      showCreateModal: false,
      createForm: {
        branch: null,
        student: null,
        test: null,
        teacher: null,
        eval_dt: new Date().toISOString().substring(0, 10)
      }
    }
  },
  computed: {
    modalFilteredStudents() {
      let list = this.contracts
      return list.map(c => ({
        student_id: c.student_id,
        contract_id: c.contract_id,
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
      axios.g('/api/lms/evaluations/igbh/init-data').then(res => {
        if (res && res.data && res.data.data) {
          const d = res.data.data
          this.branches = d.branches || []
          this.teachers = d.teachers || []
          this.tests = d.tests || []
          this.contracts = d.contracts || []
        }
      })
    },
    fetchData() {
      this.loading = true
      const params = {
        search: this.search,
        page: this.pagination.current_page,
        per_page: this.pagination.per_page,
        branch_id: this.selectedBranch ? this.selectedBranch.id : null
      }
      axios.g('/api/lms/evaluations/igbh/results', params).then(res => {
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
    resetFilters() {
      this.search = ''
      this.selectedBranch = null
      this.pagination.current_page = 1
      this.fetchData()
    },
    openCreateModal() {
      this.createForm = {
        branch: this.branches[0] || null,
        student: null,
        test: this.tests[0] || null,
        teacher: null,
        eval_dt: new Date().toISOString().substring(0, 10)
      }
      this.showCreateModal = true
    },
    submitCreate() {
      if (!this.createForm.test) {
        this.$vs.notify({ title: 'Thiếu thông tin', text: 'Vui lòng chọn bài kiểm tra', color: 'danger' })
        return
      }
      if (!this.createForm.student) {
        this.$vs.notify({ title: 'Thiếu thông tin', text: 'Vui lòng chọn học sinh', color: 'danger' })
        return
      }

      const payload = {
        test_seq: this.createForm.test.test_seq,
        student_id: this.createForm.student.student_id,
        contract_id: this.createForm.student.contract_id,
        branch_id: this.createForm.branch ? this.createForm.branch.id : null,
        teacher_id: this.createForm.teacher ? this.createForm.teacher.id : null,
        eval_dt: this.createForm.eval_dt
      }

      axios.p('/api/lms/evaluations/igbh/results', payload).then(res => {
        if (res && res.data && res.data.status === 'success') {
          this.$vs.notify({ title: 'Thành công', text: 'Khởi tạo bài khảo sát thành công', color: 'success' })
          this.showCreateModal = false
          this.fetchData()
        }
      }).catch(err => {
        this.$vs.notify({ title: 'Lỗi', text: err.message || 'Không thể khởi tạo', color: 'danger' })
      })
    },
    confirmDelete(item) {
      this.$vs.dialog({
        type: 'confirm',
        color: 'danger',
        title: 'Xác nhận xóa',
        text: `Bạn có chắc muốn xóa khảo sát của học sinh ${item.stu_nm}?`,
        acceptText: 'Xóa',
        cancelText: 'Hủy',
        accept: () => {
          axios.del(`/api/lms/evaluations/igbh/results/${item.id}`).then(() => {
            this.$vs.notify({ title: 'Đã xóa', text: 'Xóa khảo sát thành công', color: 'success' })
            this.fetchData()
          })
        }
      })
    }
  }
}
</script>

