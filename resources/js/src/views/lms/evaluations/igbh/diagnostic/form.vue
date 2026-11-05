<template>
  <div id="igbh-diagnostic-form">
    <vx-card no-shadow class="mt-5">
      <div class="flex flex-wrap justify-between items-center mb-5">
        <div>
          <h4 class="font-bold text-xl">Chấm Điểm Khảo Sát Đầu Vào (IG.BH Diagnostic)</h4>
          <p class="text-sm text-gray-500" v-if="general">
            {{ general.test_nm }} | Học sinh: <strong class="text-primary">{{ general.stu_nm }}</strong>
            <span v-if="general.stu_birth_dt"> | Ngày sinh: {{ general.stu_birth_dt }}</span>
            <span v-if="general.reg_name"> | Người đăng ký: {{ general.reg_name }}</span>
          </p>
        </div>
        <div>
          <router-link to="/lms/evaluations/igbh/diagnostic">
            <vs-button color="dark" type="border" icon="arrow_back">Quay lại danh sách</vs-button>
          </router-link>
        </div>
      </div>

      <div v-if="loading" class="text-center py-10 text-gray-500">
        <i class="fa fa-spinner fa-spin text-2xl mb-2"></i>
        <p>Đang tải đề thi và đáp án...</p>
      </div>

      <div v-else-if="general">
        <!-- Configuration and Date Header -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
          <div class="vx-row">
            <div class="vx-col sm:w-1/3 w-full mb-2">
              <label class="vs-input--label">Ngày thi / khảo sát</label>
              <vs-input type="date" class="w-full" v-model="form.eval_dt" />
            </div>
            <div class="vx-col sm:w-1/3 w-full mb-2">
              <label class="vs-input--label">Level gán sau khảo sát</label>
              <vs-input class="w-full" placeholder="VD: L1, L2, L3..." v-model="form.assigned_level" />
            </div>
            <div class="vx-col sm:w-1/3 w-full mb-2 flex items-end">
              <div class="text-sm font-bold text-gray-700">
                Tổng điểm dự kiến: <span class="text-2xl text-primary font-black ml-1">{{ totalScore }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Part 1: Curriculum Answers (20 questions) -->
        <div class="border rounded-lg mb-6 overflow-hidden">
          <div class="bg-gray-100 p-3 font-bold text-base text-gray-800 border-b flex justify-between items-center">
            <span>Phần 1: Kiến thức môn học (20 câu trắc nghiệm)</span>
            <span class="text-sm font-medium text-gray-600">
              Đúng: <strong class="text-green-600">{{ correctCount }}</strong>/{{ curriculum.length }} câu |
              Điểm: <strong class="text-primary">{{ subjectTotal }}</strong>
            </span>
          </div>
          <div class="p-4 overflow-x-auto">
            <table class="w-full text-center border-collapse text-sm">
              <thead>
                <tr class="bg-gray-50">
                  <th v-for="q in curriculum" :key="q.question_no" class="border p-2 font-bold w-12">
                    {{ q.question_no }}
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td v-for="(q, idx) in curriculum" :key="'in_' + q.question_no" class="border p-1">
                    <input
                      type="text"
                      v-model="q.assigned_score"
                      maxlength="1"
                      @input="onCurriculumInput(idx)"
                      class="w-10 h-9 text-center rounded border font-bold text-sm focus:outline-none"
                      :class="q.is_correct === 'O' ? 'border-green-500 bg-green-50' : (q.is_correct === 'X' ? 'border-red-500 bg-red-50' : 'border-gray-300')"
                    />
                  </td>
                </tr>
                <tr>
                  <td v-for="q in curriculum" :key="'res_' + q.question_no" class="border p-1 text-xs">
                    <span v-if="q.is_correct === 'O'" class="text-green-600 font-bold">O</span>
                    <span v-else-if="q.is_correct === 'X'" class="text-red-500 font-bold">X</span>
                    <span v-else class="text-gray-400">-</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Part 2: Thinking Skills (10 questions) -->
        <div class="border rounded-lg mb-6 overflow-hidden">
          <div class="bg-gray-100 p-3 font-bold text-base text-gray-800 border-b flex justify-between items-center">
            <span>Phần 2: Năng lực tư duy (10 câu)</span>
            <span class="text-sm font-medium text-gray-600">
              Điểm tư duy: <strong class="text-primary">{{ thinkingTotal }}</strong>
            </span>
          </div>
          <div class="p-4 overflow-x-auto">
            <table class="w-full text-center border-collapse text-sm">
              <thead>
                <tr class="bg-gray-50">
                  <th v-for="q in thinking" :key="'th_' + q.question_no" class="border p-2 font-bold w-14">
                    {{ q.question_no }}<br><small class="text-gray-500 font-normal">({{ q.max_score }}đ)</small>
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td v-for="q in thinking" :key="'tin_' + q.question_no" class="border p-1">
                    <input
                      type="number"
                      v-model.number="q.assigned_score"
                      :min="0"
                      :max="q.max_score"
                      class="w-12 h-9 text-center rounded border border-gray-300 font-bold text-sm focus:outline-none focus:border-blue-500"
                    />
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="flex justify-end gap-3 mt-6">
          <router-link to="/lms/evaluations/igbh/diagnostic">
            <vs-button color="dark" type="border">Hủy</vs-button>
          </router-link>
          <vs-button color="success" size="large" icon="save" @click="saveGrades" :disabled="saving">
            {{ saving ? 'Đang lưu...' : 'Lưu kết quả khảo sát' }}
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
      general: null,
      loading: true,
      saving: false,
      questions: [],
      curriculum: [],
      thinking: [],
      form: {
        eval_dt: new Date().toISOString().substring(0, 10),
        assigned_level: ''
      }
    }
  },
  computed: {
    correctCount() {
      return this.curriculum.filter(q => q.is_correct === 'O').length
    },
    subjectTotal() {
      let sum = 0
      this.curriculum.forEach(q => {
        if (q.is_correct === 'O') {
          sum += (q.standard_point || 2)
        }
      })
      return sum
    },
    thinkingTotal() {
      return this.thinking.reduce((acc, q) => acc + (parseInt(q.assigned_score) || 0), 0)
    },
    totalScore() {
      return this.subjectTotal + this.thinkingTotal
    }
  },
  created() {
    this.fetchData()
  },
  methods: {
    fetchData() {
      this.loading = true
      const id = this.$route.params.id
      axios.g(`/api/lms/evaluations/igbh/results/${id}`).then(res => {
        this.loading = false
        if (res && res.data && res.data.status === 'success') {
          const d = res.data.data
          this.general = d.general
          this.form.eval_dt = d.general.eval_dt ? d.general.eval_dt.substring(0, 10) : new Date().toISOString().substring(0, 10)
          this.form.assigned_level = d.general.assigned_level || ''

          this.questions = d.questions || []

          // Build Curriculum questions
          const savedCurr = d.curriculum || []
          const currQuestions = this.questions.filter(q => q.question_type === 'curriculum')
          if (currQuestions.length > 0) {
            this.curriculum = currQuestions.map((q, idx) => {
              const saved = savedCurr.find(s => parseInt(s.question_no) === idx + 1)
              const ans = saved ? saved.assigned_score : ''
              const isCorrect = ans ? (ans == q.answer ? 'O' : 'X') : null
              return {
                question_no: idx + 1,
                assigned_score: ans,
                correct_answer: q.answer,
                standard_point: q.standard_point || 2,
                is_correct: isCorrect,
                unit: q.sector,
                seq_id: q.id
              }
            })
          } else {
            // Default 20
            this.curriculum = Array.from({ length: 20 }, (_, idx) => {
              const saved = savedCurr.find(s => parseInt(s.question_no) === idx + 1)
              return {
                question_no: idx + 1,
                assigned_score: saved ? saved.assigned_score : '',
                standard_point: 2,
                is_correct: saved ? saved.is_correct : null
              }
            })
          }

          // Build Thinking questions
          const savedThinking = d.thinking || []
          const thinkingQuestions = this.questions.filter(q => q.question_type === 'thinking')
          if (thinkingQuestions.length > 0) {
            this.thinking = thinkingQuestions.map((q, idx) => {
              const saved = savedThinking.find(s => parseInt(s.question_no) === idx + 1)
              return {
                question_no: idx + 1,
                assigned_score: saved ? parseInt(saved.assigned_score) : 0,
                max_score: q.standard_point || 5,
                seq_id: q.id
              }
            })
          } else {
            // Default 10
            this.thinking = Array.from({ length: 10 }, (_, idx) => {
              const saved = savedThinking.find(s => parseInt(s.question_no) === idx + 1)
              return {
                question_no: idx + 1,
                assigned_score: saved ? parseInt(saved.assigned_score) : 0,
                max_score: 5
              }
            })
          }
        }
      }).catch(err => {
        this.loading = false
      })
    },
    onCurriculumInput(idx) {
      const q = this.curriculum[idx]
      if (q.assigned_score) {
        if (q.correct_answer) {
          q.is_correct = (q.assigned_score == q.correct_answer) ? 'O' : 'X'
        } else {
          q.is_correct = 'O'
        }
      } else {
        q.is_correct = null
      }
    },
    saveGrades() {
      this.saving = true
      const id = this.$route.params.id
      const payload = {
        eval_dt: this.form.eval_dt,
        assigned_level: this.form.assigned_level,
        curriculum: this.curriculum,
        thinking: this.thinking
      }

      axios.p(`/api/lms/evaluations/igbh/results/${id}/grade`, payload).then(res => {
        this.saving = false
        if (res && res.data && res.data.status === 'success') {
          this.$vs.notify({ title: 'Thành công', text: 'Đã lưu điểm khảo sát đầu vào', color: 'success' })
          this.$router.push('/lms/evaluations/igbh/diagnostic')
        }
      }).catch(err => {
        this.saving = false
        this.$vs.notify({ title: 'Lỗi', text: 'Không thể lưu điểm', color: 'danger' })
      })
    }
  }
}
</script>

