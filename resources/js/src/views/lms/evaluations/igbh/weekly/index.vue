<template>
  <div id="igbh-weekly-eval-list">
    <vx-card no-shadow class="mt-5">
      <div class="flex flex-wrap justify-between items-center mb-5">
        <div>
          <h4 class="font-bold text-xl">Quản lý Đánh Giá Tuần (IG.BH Weekly)</h4>
          <p class="text-sm text-gray-500">Đánh giá quá trình học tập theo từng lớp và từng tuần (Tuần 1 - 12)</p>
        </div>
        <div>
          <vs-button color="primary" type="filled" icon="add" @click="openCreateModal">
            Khởi tạo đánh giá tuần cho lớp
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
                  <th>Tuần</th>
                  <th>Bài kiểm tra</th>
                  <th>Giáo viên</th>
                  <th class="text-center">Ngày đánh giá</th>
                  <th class="text-center">Trạng thái</th>
                  <th class="text-center">Thao tác</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="loading">
                  <td colspan="8" class="text-center py-6 text-gray-500">Đang tải dữ liệu...</td>
                </tr>
                <tr v-else-if="results.length === 0">
                  <td colspan="8" class="text-center py-6 text-gray-500">Chưa có đợt đánh giá tuần nào</td>
                </tr>
                <tr v-for="(item, index) in results" :key="item.id" class="tr-values vs-table--tr">
                  <td class="text-center">{{ (pagination.current_page - 1) * pagination.per_page + index + 1 }}</td>
                  <td><strong>{{ item.erp_class_name || item.class_nm }}</strong></td>
                  <td><vs-chip color="primary">{{ item.each_cd_nm || item.each_cd }}</vs-chip></td>
                  <td>{{ item.test_nm }}</td>
                  <td>{{ item.teacher_nm || '-' }}</td>
                  <td class="text-center">{{ item.eval_ymd ? item.eval_ymd.substring(0, 10) : '-' }}</td>
                  <td class="text-center">
                    <vs-chip v-if="item.status === 'Completed'" color="success">Đã hoàn thành</vs-chip>
                    <vs-chip v-else color="warning">Đang nhập (Draft)</vs-chip>
                  </td>
                  <td class="text-center">
                    <div class="flex justify-center items-center gap-2">
                      <router-link
                        :to="`/lms/evaluations/igbh/weekly/form/${item.id}`"
                        class="btn-action text-primary mr-2"
                        title="Vào chấm điểm lớp"
                      >
                        <i class="feather icon-edit text-lg"></i>
                      </router-link>
                      <button
                        @click="confirmDelete(item)"
                        class="btn-action text-danger"
                        title="Xóa đánh giá tuần"
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
          Tổng số: <strong>{{ pagination.total }}</strong> đợt đánh giá
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
    <vs-popup title="Khởi tạo đánh giá tuần mới cho Lớp học" :active.sync="showCreateModal">
      <div>
        <div class="mb-4">
          <label class="vs-input--label">1. Chọn Bài kiểm tra (*)</label>
          <multiselect
            placeholder="Chọn bài kiểm tra Summative"
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
            @input="onModalClassChange"
          />
        </div>

        <div class="mb-4">
          <label class="vs-input--label">3. Chọn Tuần đánh giá (*)</label>
          <multiselect
            placeholder="Chọn tuần"
            v-model="createForm.week"
            :options="availableWeeks"
            label="each_cd_nm"
            track-by="each_cd"
          />
        </div>

        <div class="mb-4">
          <label class="vs-input--label">4. Ngày đánh giá</label>
          <vs-input type="date" class="w-full" v-model="createForm.eval_ymd" />
        </div>

        <div class="flex justify-end gap-3 mt-6">
          <vs-button color="dark" type="border" @click="showCreateModal = false">Hủy</vs-button>
          <vs-button color="primary" @click="submitCreate">Bắt đầu chấm điểm</vs-button>
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
      classes: [],
      tests: [],
      weeks: [],
      existingWeeks: [],
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
        week: null,
        eval_ymd: new Date().toISOString().substring(0, 10)
      }
    }
  },
  computed: {
    availableWeeks() {
      return this.weeks.map(w => ({
        ...w,
        each_cd_nm: this.existingWeeks.includes(w.each_cd) ? `${w.each_cd_nm} (Đã có)` : w.each_cd_nm
      }))
    }
  },
  created() {
    this.loadInitData()
    this.fetchData()
  },
  methods: {
    loadInitData() {
      axios.g('/api/lms/evaluations/weekly/init-data').then(res => {
        if (res && res.data) {
          this.classes = res.data.classes || []
          this.tests = res.data.tests || []
          this.weeks = res.data.weeks || []
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
      axios.g('/api/lms/evaluations/weekly/results', params).then(res => {
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
        week: this.weeks[0] || null,
        eval_ymd: new Date().toISOString().substring(0, 10)
      }
      this.showCreateModal = true
      this.fetchExistingWeeks()
    },
    onModalClassChange() {
      this.fetchExistingWeeks()
    },
    fetchExistingWeeks() {
      if (this.createForm.test && this.createForm.class) {
        axios.p('/api/lms/evaluations/weekly/existing-weeks', {
          test_seq: this.createForm.test.test_seq,
          class_id: this.createForm.class.id
        }).then(res => {
          this.existingWeeks = res.data || []
        })
      }
    },
    submitCreate() {
      if (!this.createForm.test || !this.createForm.class || !this.createForm.week) {
        this.$vs.notify({ title: 'Thiếu thông tin', text: 'Vui lòng chọn bài test, lớp học và tuần', color: 'danger' })
        return
      }

      const payload = {
        test_seq: this.createForm.test.test_seq,
        class_id: this.createForm.class.id,
        each_cd: this.createForm.week.each_cd,
        eval_ymd: this.createForm.eval_ymd
      }

      axios.p('/api/lms/evaluations/weekly/results', payload).then(res => {
        if (res && res.data && res.data.id) {
          this.showCreateModal = false
          this.$router.push(`/lms/evaluations/igbh/weekly/form/${res.data.id}`)
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
        text: `Bạn có chắc muốn xóa đánh giá ${item.each_cd_nm} của lớp ${item.class_nm}?`,
        acceptText: 'Xóa',
        cancelText: 'Hủy',
        accept: () => {
          axios.del(`/api/lms/evaluations/weekly/results/${item.id}`).then(() => {
            this.$vs.notify({ title: 'Đã xóa', text: 'Xóa đánh giá tuần thành công', color: 'success' })
            this.fetchData()
          })
        }
      })
    }
  }
}
</script>

