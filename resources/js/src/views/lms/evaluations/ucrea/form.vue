<template>
  <div id="ucrea-eval-form">
    <vx-card no-shadow class="mt-5">
      <div class="flex flex-wrap justify-between items-center mb-5">
        <div>
          <h4 class="font-bold text-xl">Đánh Giá Năng Lực UCREA</h4>
          <p class="text-sm text-gray-500">Nhập điểm đánh giá chi tiết theo từng tiêu chí Rubric</p>
        </div>
        <div>
          <router-link to="/lms/evaluations/ucrea">
            <vs-button color="dark" type="border" icon="arrow_back">Quay lại danh sách</vs-button>
          </router-link>
        </div>
      </div>

      <div v-if="loading" class="text-center py-10 text-gray-500">
        <i class="fa fa-spinner fa-spin text-2xl mb-2"></i>
        <p>Đang tải thông tin bài đánh giá...</p>
      </div>

      <div v-else-if="result">
        <!-- Student & Test Card -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-5 mb-6 flex flex-wrap justify-between items-center">
          <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
            <div>
              <p class="text-xs text-gray-500 uppercase font-semibold">Học sinh</p>
              <p class="font-bold text-primary text-base mt-1">{{ result.stu_nm }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-500 uppercase font-semibold">Bài đánh giá</p>
              <p class="font-semibold text-gray-800 mt-1">{{ result.test_nm }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-500 uppercase font-semibold">Cấp độ</p>
              <p class="mt-1"><vs-chip color="primary">{{ result.level_cd_nm || result.level_cd }}</vs-chip></p>
            </div>
            <div>
              <p class="text-xs text-gray-500 uppercase font-semibold">Lớp học</p>
              <p class="text-gray-800 mt-1 font-medium">{{ result.class_nm || '-' }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-500 uppercase font-semibold">Giáo viên</p>
              <p class="text-gray-800 mt-1">{{ result.memb_nm || '-' }}</p>
            </div>
          </div>
          <div class="mt-4 sm:mt-0">
            <vs-button color="success" type="filled" icon="check" @click="submitGrades" :disabled="submitting">
              {{ submitting ? 'Đang lưu...' : 'Lưu kết quả chấm' }}
            </vs-button>
          </div>
        </div>

        <!-- Rubric Table -->
        <div class="vs-component vs-con-table stripe vs-table-primary">
          <div class="con-tablex vs-table--content">
            <div class="vs-con-tbody vs-table--tbody">
              <table class="vs-table vs-table--tbody-table">
                <thead class="vs-table--thead">
                  <tr>
                    <th class="text-center w-16">Vấn đề</th>
                    <th class="w-48">Lĩnh vực kiểm tra</th>
                    <th>Lĩnh vực chi tiết</th>
                    <th class="text-center" colspan="5">Điểm thực tế (chọn 1)</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in rubrics" :key="index" class="tr-values vs-table--tr">
                    <td class="text-center font-bold text-gray-600">{{ getQuestionNumber(index) }}</td>
                    <td class="font-semibold text-gray-800">{{ item.main }}</td>
                    <td>
                      <p class="font-medium text-gray-800">{{ item.sub }}</p>
                      <small class="text-primary font-bold">{{ item.name }}</small>
                    </td>
                    <td v-for="(opt, optIdx) in item.options" :key="optIdx" class="text-center border-l px-2">
                      <label class="cursor-pointer flex flex-col items-center justify-center p-1 hover:bg-blue-100 rounded">
                        <input
                          type="radio"
                          :name="'rubric_' + index"
                          :value="opt"
                          v-model="item.score"
                          class="cursor-pointer"
                        />
                        <span class="text-xs font-semibold mt-1" :class="item.score === opt ? 'text-primary font-bold' : 'text-gray-600'">
                          {{ opt }}
                        </span>
                      </label>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="flex justify-end mt-6">
          <vs-button color="success" size="large" icon="check" @click="submitGrades" :disabled="submitting">
            {{ submitting ? 'Đang lưu kết quả...' : 'Hoàn tất & Lưu điểm' }}
          </vs-button>
        </div>
      </div>
    </vx-card>
  </div>
</template>

<script>
import axios from '@/http/axios.js'

export default {
  data() {
    return {
      result: null,
      loading: true,
      submitting: false,
      rubrics: [
        { main: 'Khả năng suy nghĩ', sub: 'Kỹ năng tư duy cơ bản', name: 'Chú ý', skillName: 'Năng lực chú ý', key: 'sk11', options: ['4', '3', '2', '1', '0'], score: null },
        { main: 'Khả năng suy nghĩ', sub: 'Kỹ năng tư duy cơ bản', name: 'Quan sát', skillName: 'Năng lực quan sát', key: 'sk12', options: ['9-10', '7-8', '5-6', '3-4', '0-2'], score: null },
        { main: 'Khả năng suy nghĩ', sub: 'Kỹ năng tư duy cơ bản', name: 'Ghi nhớ', skillName: 'Năng lực ghi nhớ', key: 'sk13', options: ['5-6', '4', '2-3', '1', '0'], score: null },
        { main: 'Khả năng suy nghĩ', sub: 'Kỹ năng tư duy logic', name: 'Hiểu', skillName: 'Hiểu biết', key: 'sk21', options: ['6', '4-5', '2-3', '1', '0'], score: null },
        { main: 'Khả năng suy nghĩ', sub: 'Kỹ năng tư duy logic', name: 'Áp dụng', skillName: 'Ứng dụng', key: 'sk22', options: ['4', '3', '2', '1', '0'], score: null },
        { main: 'Khả năng suy nghĩ', sub: 'Kỹ năng tư duy logic', name: 'Phân tích', skillName: 'Phân tích', key: 'sk23', options: ['4', '3', '2', '1', '0'], score: null },
        { main: 'Khả năng suy nghĩ', sub: 'Kỹ năng tư duy logic', name: 'Tổng hợp', skillName: 'Tổng hợp', key: 'sk24', options: ['4', '3', '2', '1', '0'], score: null },
        { main: 'Kiến thức tích lũy', sub: 'Kỹ năng tư duy toán học', name: 'Con số và hoạt động', skillName: 'Số và tính toán', key: 'kn11', options: ['4', '3', '2', '1', '0'], score: null },
        { main: 'Kiến thức tích lũy', sub: 'Kỹ năng tư duy toán học', name: 'Hình học', skillName: 'Hình học không gian', key: 'kn12', options: ['4', '3', '2', '1', '0'], score: null },
        { main: 'Kiến thức tích lũy', sub: 'Kỹ năng tư duy toán học', name: 'Đo lường', skillName: 'Đo lường', key: 'kn13', options: ['4', '3', '2', '1', '0'], score: null },
        { main: 'Kiến thức tích lũy', sub: 'Kỹ năng tư duy toán học', name: 'Kiểu Mẫu', skillName: 'Kiểu mẫu', key: 'kn14', options: ['4', '3', '2', '1', '0'], score: null },
        { main: 'Kiến thức tích lũy', sub: 'Kỹ năng tư duy toán học', name: 'Dữ liệu', skillName: 'Dữ liệu', key: 'kn15', options: ['order O, 3', 'order O, 2', 'order O, 0-1', 'order X, 2-3', 'order X, 0-1'], score: null },
        { main: 'Khả năng suy nghĩ', sub: 'Kỹ năng tư duy sáng tạo', name: 'Lưu loát', skillName: 'Sự trôi chảy', key: 'sk31', options: ['6', '4-5', '2-3', '1', '0'], score: null },
        { main: 'Khả năng suy nghĩ', sub: 'Kỹ năng tư duy sáng tạo', name: 'Tính linh hoạt', skillName: 'Tính linh hoạt', key: 'sk32', options: ['8', '6-7', '3-5', '1-2', '0'], score: null },
        { main: 'Khả năng suy nghĩ', sub: 'Kỹ năng tư duy sáng tạo', name: 'Độc đáo', skillName: 'Tính độc đáo', key: 'sk33', options: ['4', '3', '2', '1', '0'], score: null },
        { main: 'Khả năng suy nghĩ', sub: 'Kỹ năng tư duy sáng tạo', name: 'Xây dựng', skillName: 'Tính chính xác', key: 'sk34', options: ['4', '3', '2', '1', '0'], score: null }
      ]
    }
  },
  created() {
    this.fetchInfo()
  },
  methods: {
    getQuestionNumber(index) {
      if (!this.result) return index + 1
      const isL1L2 = ['L1', 'L2'].includes(this.result.level_cd)
      if (isL1L2) {
        if (index >= 10) return '11'
        return index + 1
      } else {
        if (index >= 12) return '13,14'
        return index + 1
      }
    },
    fetchInfo() {
      this.loading = true
      const id = this.$route.params.id
      axios.g(`/api/lms/evaluations/ucrea/results/${id}`).then(res => {
        this.loading = false
        if (res && res.data && res.data.status === 'success') {
          this.result = res.data.data.general

          if (['L1', 'L2'].includes(this.result.level_cd)) {
            this.rubrics = this.rubrics.filter((r, idx) => idx !== 6 && idx !== 11)
          }

          const savedRubrics = res.data.data.rubrics || []
          if (savedRubrics.length > 0) {
            this.rubrics.forEach(rub => {
              const found = savedRubrics.find(sr => sr.rubric_name === rub.name)
              if (found) {
                rub.score = found.assigned_score
              }
            })
          }
        }
      }).catch(err => {
        this.loading = false
        this.$vs.notify({ title: 'Lỗi', text: 'Không thể tải thông tin bài test', color: 'danger' })
      })
    },
    submitGrades() {
      const unanswered = this.rubrics.filter(r => !r.score)
      if (unanswered.length > 0) {
        this.$vs.notify({
          title: 'Chưa hoàn thành',
          text: `Vui lòng chấm điểm cho tất cả ${this.rubrics.length} tiêu chí!`,
          color: 'warning'
        })
        return
      }

      this.submitting = true
      const id = this.$route.params.id
      const payload = { rubrics: this.rubrics }

      axios.p(`/api/lms/evaluations/ucrea/results/${id}/grade`, payload).then(res => {
        this.submitting = false
        if (res && res.data && res.data.status === 'success') {
          this.$vs.notify({ title: 'Thành công', text: 'Đã lưu điểm đánh giá thành công', color: 'success' })
          this.$router.push('/lms/evaluations/ucrea')
        }
      }).catch(err => {
        this.submitting = false
        this.$vs.notify({ title: 'Lỗi', text: 'Không thể lưu điểm', color: 'danger' })
      })
    }
  }
}
</script>

