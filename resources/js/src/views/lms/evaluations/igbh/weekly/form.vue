<template>
  <div id="igbh-weekly-eval-form">
    <vx-card no-shadow class="mt-5">
      <div class="flex flex-wrap justify-between items-center mb-5">
        <div>
          <h4 class="font-bold text-xl">
            Đánh Giá Tuần IG.BH - {{ evaluation ? evaluation.each_cd_nm : '' }}
          </h4>
          <p class="text-sm text-gray-500" v-if="evaluation">
            Lớp: <strong class="text-primary">{{ evaluation.class_nm }}</strong> |
            Bài kiểm tra: <strong class="text-gray-800">{{ evaluation.test_nm }}</strong> |
            Giáo viên: <strong>{{ evaluation.teacher_nm }}</strong>
          </p>
        </div>
        <div class="flex items-center gap-2">
          <vs-input type="date" v-model="selectedDate" class="w-40" />
          <vs-select v-model="selectedStatus" class="w-36">
            <vs-select-item value="Draft" text="Đang nhập (Draft)" />
            <vs-select-item value="Completed" text="Đã hoàn thành" />
          </vs-select>
          <vs-button color="success" icon="save" @click="saveGrades" :disabled="saving">
            {{ saving ? 'Đang lưu...' : 'Lưu đánh giá' }}
          </vs-button>
          <router-link to="/lms/evaluations/igbh/weekly">
            <vs-button color="dark" type="border" icon="arrow_back">Quay lại</vs-button>
          </router-link>
        </div>
      </div>

      <div v-if="loading" class="text-center py-12 text-gray-500">
        <i class="fa fa-spinner fa-spin text-2xl mb-2"></i>
        <p>Đang tải danh sách học sinh của lớp...</p>
      </div>

      <div v-else>
        <!-- Evaluation Matrix Table -->
        <div class="vs-component vs-con-table stripe vs-table-primary">
          <div class="con-tablex vs-table--content">
            <div class="vs-con-tbody vs-table--tbody">
              <table class="vs-table vs-table--tbody-table">
                <thead class="vs-table--thead">
                  <tr>
                    <th rowspan="2" class="text-center w-12">STT</th>
                    <th rowspan="2" class="w-44">Tên học sinh</th>
                    <th rowspan="2" class="text-center w-28">Bài tập<br><small>(1~20)</small></th>
                    <th colspan="4" class="text-center border-b">Thái độ học tập (0~5)</th>
                    <th colspan="4" class="text-center border-b">Đánh giá lĩnh vực (0~5)</th>
                  </tr>
                  <tr>
                    <th class="text-center w-20">Lắng nghe</th>
                    <th class="text-center w-20">Tham gia</th>
                    <th class="text-center w-20">Thể hiện</th>
                    <th class="text-center w-20">Hợp tác</th>
                    <th class="text-center w-20">Cơ bản</th>
                    <th class="text-center w-20">Lãnh đạo</th>
                    <th class="text-center w-20">Toán học</th>
                    <th class="text-center w-20">Sáng tạo</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="students.length === 0">
                    <td colspan="11" class="text-center py-6 text-gray-500">
                      Lớp này hiện chưa có học sinh nào được xếp lớp / có hợp đồng hợp lệ.
                    </td>
                  </tr>
                  <tr v-for="(s, index) in students" :key="index" class="tr-values vs-table--tr">
                    <td class="text-center text-gray-500">{{ index + 1 }}</td>
                    <td>
                      <strong>{{ s.stu_nm }}</strong>
                      <div class="text-xs text-gray-400" v-if="s.stu_seq">{{ s.stu_seq }}</div>
                    </td>
                    <td class="text-center p-1">
                      <input
                        type="number"
                        min="0"
                        max="20"
                        v-model.number="s.workbook"
                        class="w-16 h-8 text-center rounded border border-gray-300 font-bold text-sm"
                      />
                    </td>
                    <td class="text-center p-1">
                      <input type="number" min="0" max="5" v-model.number="s.attd_listen" class="w-12 h-8 text-center rounded border border-gray-300 text-sm" />
                    </td>
                    <td class="text-center p-1">
                      <input type="number" min="0" max="5" v-model.number="s.attd_join" class="w-12 h-8 text-center rounded border border-gray-300 text-sm" />
                    </td>
                    <td class="text-center p-1">
                      <input type="number" min="0" max="5" v-model.number="s.attd_express" class="w-12 h-8 text-center rounded border border-gray-300 text-sm" />
                    </td>
                    <td class="text-center p-1">
                      <input type="number" min="0" max="5" v-model.number="s.attd_coop" class="w-12 h-8 text-center rounded border border-gray-300 text-sm" />
                    </td>
                    <td class="text-center p-1">
                      <input type="number" min="0" max="5" v-model.number="s.detect_normal" class="w-12 h-8 text-center rounded border border-gray-300 text-sm" />
                    </td>
                    <td class="text-center p-1">
                      <input type="number" min="0" max="5" v-model.number="s.detect_leadersh" class="w-12 h-8 text-center rounded border border-gray-300 text-sm" />
                    </td>
                    <td class="text-center p-1">
                      <input type="number" min="0" max="5" v-model.number="s.detect_math" class="w-12 h-8 text-center rounded border border-gray-300 text-sm" />
                    </td>
                    <td class="text-center p-1">
                      <input type="number" min="0" max="5" v-model.number="s.detect_creative" class="w-12 h-8 text-center rounded border border-gray-300 text-sm" />
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="flex justify-between items-center mt-6">
          <div class="text-sm text-gray-500">
            Tổng số học sinh được đánh giá: <strong>{{ students.length }}</strong> em
          </div>
          <div class="flex gap-3">
            <router-link to="/lms/evaluations/igbh/weekly">
              <vs-button color="dark" type="border">Quay lại</vs-button>
            </router-link>
            <vs-button color="success" size="large" icon="save" @click="saveGrades" :disabled="saving">
              {{ saving ? 'Đang lưu...' : 'Lưu kết quả tuần' }}
            </vs-button>
          </div>
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
      evaluation: null,
      students: [],
      loading: true,
      saving: false,
      selectedDate: new Date().toISOString().substring(0, 10),
      selectedStatus: 'Draft'
    }
  },
  created() {
    this.fetchData()
  },
  methods: {
    fetchData() {
      this.loading = true
      const id = this.$route.params.id
      axios.g(`/api/lms/evaluations/weekly/results/${id}`).then(res => {
        this.loading = false
        if (res && res.data) {
          this.evaluation = res.data.evaluation
          this.students = res.data.students || []
          if (this.evaluation.eval_ymd) {
            this.selectedDate = this.evaluation.eval_ymd.substring(0, 10)
          }
          this.selectedStatus = this.evaluation.status || 'Draft'
        }
      }).catch(err => {
        this.loading = false
      })
    },
    saveGrades() {
      this.saving = true
      const id = this.$route.params.id
      const payload = {
        students: this.students,
        eval_ymd: this.selectedDate,
        status: this.selectedStatus
      }

      axios.p(`/api/lms/evaluations/weekly/results/${id}/grade`, payload).then(res => {
        this.saving = false
        this.$vs.notify({ title: 'Thành công', text: 'Đã lưu đánh giá tuần thành công', color: 'success' })
      }).catch(err => {
        this.saving = false
        this.$vs.notify({ title: 'Lỗi', text: 'Không thể lưu đánh giá', color: 'danger' })
      })
    }
  }
}
</script>

