<template>
  <div id="igbh-summative-form">
    <vx-card no-shadow class="mt-5">
      <div class="flex flex-wrap justify-between items-center mb-5">
        <div>
          <h4 class="font-bold text-xl">Nhập Điểm Đánh Giá Cuối Kỳ (Summative)</h4>
          <p class="text-sm text-gray-500" v-if="formData && formData.student_info">
            Học sinh: <strong class="text-primary">{{ formData.student_info.stu_nm }}</strong> |
            Lớp: <strong>{{ formData.student_info.class_nm }}</strong> |
            Bài kiểm tra: <strong>{{ formData.student_info.test_nm }}</strong>
          </p>
        </div>
        <div class="flex items-center gap-2">
          <vs-button color="success" icon="save" @click="save" :disabled="saving">
            {{ saving ? 'Đang lưu...' : 'Lưu kết quả' }}
          </vs-button>
          <router-link to="/lms/evaluations/igbh/summative">
            <vs-button color="dark" type="border" icon="arrow_back">Quay lại</vs-button>
          </router-link>
        </div>
      </div>

      <div v-if="loading" class="text-center py-12 text-gray-500">
        <i class="fa fa-spinner fa-spin text-2xl mb-2"></i>
        <p>Đang tải thông tin bài thi cuối kỳ...</p>
      </div>

      <div v-else-if="formData">
        <!-- Header Info -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
          <div class="vx-row">
            <div class="vx-col sm:w-1/3 w-full mb-2">
              <label class="vs-input--label">Học sinh</label>
              <div class="font-bold text-primary text-base">{{ formData.student_info.stu_nm }}</div>
            </div>
            <div class="vx-col sm:w-1/3 w-full mb-2">
              <label class="vs-input--label">Lớp học</label>
              <div class="font-semibold text-gray-800">{{ formData.student_info.class_nm || '-' }}</div>
            </div>
            <div class="vx-col sm:w-1/3 w-full mb-2">
              <label class="vs-input--label">Ngày kiểm tra</label>
              <vs-input type="date" class="w-full" v-model="formData.student_info.eval_dt" />
            </div>
          </div>
        </div>

        <!-- Part 1: Subjective Questions (No 1 - 5) -->
        <div class="border rounded-lg mb-6 overflow-hidden">
          <div class="bg-gray-100 p-3 font-bold text-base text-gray-800 border-b">
            Phần 1: Điểm câu hỏi tự luận (Câu 1 - 5: Khái niệm, Chiến lược, Tính toán, Diễn đạt)
          </div>
          <div class="p-4 overflow-x-auto">
            <table class="w-full text-center border-collapse text-sm">
              <thead>
                <tr class="bg-gray-50">
                  <th rowspan="2" class="border p-2 w-16">Câu số</th>
                  <th colspan="4" class="border p-2">Tiêu chí đánh giá (0 ~ 6 điểm mỗi tiêu chí)</th>
                  <th rowspan="2" class="border p-2 w-24">Tổng điểm</th>
                </tr>
                <tr class="bg-gray-50 text-xs">
                  <th class="border p-1 w-28">Khái niệm</th>
                  <th class="border p-1 w-28">Chiến lược</th>
                  <th class="border p-1 w-28">Tính toán</th>
                  <th class="border p-1 w-28">Diễn đạt</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(sub, idx) in formData.subjective_data" :key="idx">
                  <td class="border p-2 font-bold text-gray-700">No. {{ idx + 1 }}</td>
                  <td class="border p-1">
                    <input type="number" min="0" max="6" v-model.number="sub.concept" class="w-16 h-8 text-center rounded border border-gray-300 font-bold" />
                  </td>
                  <td class="border p-1">
                    <input type="number" min="0" max="6" v-model.number="sub.strategy" class="w-16 h-8 text-center rounded border border-gray-300 font-bold" />
                  </td>
                  <td class="border p-1">
                    <input type="number" min="0" max="6" v-model.number="sub.calculation" class="w-16 h-8 text-center rounded border border-gray-300 font-bold" />
                  </td>
                  <td class="border p-1">
                    <input type="number" min="0" max="6" v-model.number="sub.expression" class="w-16 h-8 text-center rounded border border-gray-300 font-bold" />
                  </td>
                  <td class="border p-1 font-bold text-primary">
                    {{ (parseFloat(sub.concept)||0) + (parseFloat(sub.strategy)||0) + (parseFloat(sub.calculation)||0) + (parseFloat(sub.expression)||0) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Part 2: Workbook Scores per Lesson (12 lessons) -->
        <div class="border rounded-lg mb-6 overflow-hidden">
          <div class="bg-gray-100 p-3 font-bold text-base text-gray-800 border-b">
            Phần 2: Điểm sách bài tập theo 12 bài học (No. 6 - 17)
          </div>
          <div class="p-4 overflow-x-auto">
            <table class="w-full text-center border-collapse text-sm">
              <thead>
                <tr class="bg-gray-50">
                  <th v-for="(wd, idx) in formData.weekly_data" :key="idx" class="border p-2 text-xs font-bold w-20">
                    No. {{ idx + 6 }}<br>
                    <small class="text-gray-500 font-normal">({{ wd.max_score }}đ)</small>
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td v-for="(wd, idx) in formData.weekly_data" :key="idx" class="border p-2">
                    <input
                      type="number"
                      min="0"
                      :max="wd.max_score"
                      v-model.number="wd.workbook"
                      class="w-14 h-8 text-center rounded border border-gray-300 font-bold"
                    />
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Part 3: Teacher Comment -->
        <div class="border rounded-lg mb-6 overflow-hidden">
          <div class="bg-gray-100 p-3 font-bold text-base text-gray-800 border-b">
            Nhận xét của giáo viên
          </div>
          <div class="p-4">
            <vs-textarea
              label="Nhận xét tổng kết tiến bộ của học sinh"
              v-model="formData.teacher_comment"
              rows="4"
              maxlength="480"
              placeholder="Nhập nhận xét chi tiết..."
            />
          </div>
        </div>

        <div class="flex justify-end gap-3 mt-6">
          <router-link to="/lms/evaluations/igbh/summative">
            <vs-button color="dark" type="border">Hủy</vs-button>
          </router-link>
          <vs-button color="success" size="large" icon="save" @click="save" :disabled="saving">
            {{ saving ? 'Đang lưu...' : 'Lưu kết quả tổng kết' }}
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
      formData: null,
      loading: true,
      saving: false
    }
  },
  created() {
    this.fetchData()
  },
  methods: {
    fetchData() {
      this.loading = true
      const id = this.$route.params.id
      axios.g(`/api/lms/evaluations/summative/results/${id}/form`).then(res => {
        this.loading = false
        if (res && res.data) {
          const r = res.data.result
          const details = res.data.details || []
          const themes = res.data.themes || []
          const weeklyEvals = res.data.weekly_evals || {}

          // 1. Subjective data (5 questions)
          const subjectiveData = []
          for (let i = 1; i <= 5; i++) {
            const found = details.find(d => d.sort_no === i)
            subjectiveData.push({
              sort_no: i,
              max_score: 5,
              concept: found ? found.concept : 0,
              strategy: found ? found.strategy : 0,
              calculation: found ? found.calculation : 0,
              expression: found ? found.expression : 0
            })
          }

          // 2. Weekly workbook data (12 lessons)
          const weeklyData = []
          for (let i = 1; i <= 12; i++) {
            const sortNo = i + 5
            const theme = themes.find(t => t.sort_no === i)
            const maxScore = theme ? theme.theme_point : 3
            const found = details.find(d => d.sort_no === sortNo)

            let wbScore = found ? found.score : null
            if (wbScore === null) {
              const eachCd = 'SE' + String(i).padStart(3, '0')
              if (weeklyEvals[eachCd]) {
                wbScore = weeklyEvals[eachCd].workbook
              }
            }

            weeklyData.push({
              sort_no: sortNo,
              max_score: maxScore,
              workbook: wbScore !== null ? wbScore : 0
            })
          }

          let teacherComment = ''
          if (r.subjective_analysis) {
            try {
              const parsed = typeof r.subjective_analysis === 'string' ? JSON.parse(r.subjective_analysis) : r.subjective_analysis
              teacherComment = parsed.teacher_comment || ''
            } catch (e) {}
          }

          this.formData = {
            student_info: {
              stu_nm: r.stu_nm,
              class_nm: r.class_nm,
              test_nm: r.test_nm,
              eval_dt: r.eval_dt ? r.eval_dt.substring(0, 10) : new Date().toISOString().substring(0, 10)
            },
            subjective_data: subjectiveData,
            weekly_data: weeklyData,
            teacher_comment: teacherComment
          }
        }
      }).catch(err => {
        this.loading = false
      })
    },
    save() {
      this.saving = true
      const id = this.$route.params.id

      const subjectiveQuestions = this.formData.subjective_data.map(q => {
        const score = (parseFloat(q.concept) || 0) + (parseFloat(q.strategy) || 0) + (parseFloat(q.calculation) || 0) + (parseFloat(q.expression) || 0)
        return {
          sort_no: q.sort_no,
          max_score: 5,
          score: score,
          concept: q.concept,
          strategy: q.strategy,
          calculation: q.calculation,
          expression: q.expression
        }
      })

      const workbookScores = this.formData.weekly_data.map(wb => ({
        sort_no: wb.sort_no,
        max_score: wb.max_score,
        score: wb.workbook
      }))

      const payload = {
        eval_dt: this.formData.student_info.eval_dt,
        status: 'completed',
        subjective_questions: subjectiveQuestions,
        workbook_scores: workbookScores,
        subjective_analysis: {
          teacher_comment: this.formData.teacher_comment
        }
      }

      axios.p(`/api/lms/evaluations/summative/results/${id}/grade`, payload).then(res => {
        this.saving = false
        this.$vs.notify({ title: 'Thành công', text: 'Đã lưu điểm tổng kết thành công', color: 'success' })
        this.$router.push('/lms/evaluations/igbh/summative')
      }).catch(err => {
        this.saving = false
        this.$vs.notify({ title: 'Lỗi', text: 'Không thể lưu điểm', color: 'danger' })
      })
    }
  }
}
</script>

