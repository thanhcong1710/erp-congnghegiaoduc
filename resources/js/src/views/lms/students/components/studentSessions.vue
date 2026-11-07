<template>
  <div class="student-detail-sessions mt-2" v-if="contract_info && contract_info.id">
    <!-- Session Stats Cards -->
    <div class="vx-row mb-4">
      <div class="vx-col sm:w-1/3 w-full mb-3">
        <div class="session-stat-card border-l-4 border-primary">
          <div class="text-xs text-gray-500 font-medium">Tổng số buổi trong gói</div>
          <div class="text-xl font-bold text-gray-800 mt-1">
            {{ contract_info.summary_sessions || 0 }} <span class="text-xs font-normal text-gray-400">buổi</span>
          </div>
        </div>
      </div>
      <div class="vx-col sm:w-1/3 w-full mb-3">
        <div class="session-stat-card border-l-4 border-emerald-500">
          <div class="text-xs text-gray-500 font-medium">Số buổi đã học / ghi nhận</div>
          <div class="text-xl font-bold text-emerald-600 mt-1">
            {{ contract_info.done_sessions || 0 }} <span class="text-xs font-normal text-gray-400">buổi</span>
          </div>
        </div>
      </div>
      <div class="vx-col sm:w-1/3 w-full mb-3">
        <div class="session-stat-card border-l-4 border-amber-500">
          <div class="text-xs text-gray-500 font-medium">Số buổi còn lại</div>
          <div class="text-xl font-bold text-amber-600 mt-1">
            {{ (contract_info.summary_sessions || 0) - (contract_info.done_sessions || 0) }} <span class="text-xs font-normal text-gray-400">buổi</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Legend -->
    <div class="flex flex-wrap items-center gap-3 p-3 bg-slate-50 rounded-lg border border-slate-200 mb-4 text-xs text-gray-600">
      <span class="font-bold text-gray-700 mr-2"><i class="fa-solid fa-circle-info mr-1 text-primary"></i> Chú thích:</span>
      <span class="inline-flex items-center"><span class="w-3 h-3 rounded-full bg-emerald-500 mr-1.5 inline-block"></span> Đã học</span>
      <span class="inline-flex items-center"><span class="w-3 h-3 rounded-full bg-purple-500 mr-1.5 inline-block"></span> Đã học bù</span>
      <span class="inline-flex items-center"><span class="w-3 h-3 rounded-full bg-rose-500 mr-1.5 inline-block"></span> Đã chạy phí</span>
      <span class="inline-flex items-center"><span class="w-3 h-3 rounded-full bg-amber-500 mr-1.5 inline-block"></span> Bảo lưu / Nghỉ lễ</span>
      <span class="inline-flex items-center"><span class="w-3 h-3 rounded-full bg-slate-300 mr-1.5 inline-block"></span> Sắp học</span>
    </div>

    <!-- Sessions Grid -->
    <div>
      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
        <!-- Done Sessions -->
        <div
          v-for="(item, index) in done_sessions"
          :key="'done-' + index"
          class="session-chip-card"
          :class="getSessionCardClass(item)"
        >
          <div class="session-order">Buổi {{ index + 1 }}</div>
          <div class="session-class-name font-semibold text-gray-800 truncate" :title="item.cls_name">{{ item.cls_name }}</div>
          <div class="session-date text-gray-500">
            <i class="fa-regular fa-calendar-days text-2xs mr-1"></i>
            {{ item.class_date | formatDateViewDay }}
          </div>
          <div class="mt-2">
            <span class="session-status-pill" :class="getSessionStatusPillClass(item)">
              {{ getSessionStatusText(item) }}
            </span>
          </div>
        </div>

        <!-- Next Sessions -->
        <div
          v-for="(item, index) in next_sessions"
          :key="'next-' + index"
          class="session-chip-card session-upcoming"
        >
          <div class="session-order text-gray-400">Buổi {{ index + 1 + (done_sessions ? done_sessions.length : 0) }}</div>
          <div class="session-class-name font-semibold text-gray-800 truncate" :title="item.cls_name">{{ item.cls_name }}</div>
          <div class="session-date text-gray-500">
            <i class="fa-regular fa-calendar-days text-2xs mr-1"></i>
            {{ item.class_date | formatDateViewDay }}
          </div>
          <div class="mt-2">
            <span class="session-status-pill bg-slate-100 text-slate-600 border border-slate-200">
              <i class="fa-regular fa-clock text-2xs mr-1"></i> Sắp học
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div v-else class="p-8 text-center bg-slate-50 rounded-lg border border-slate-200 mt-2">
    <i class="fa-regular fa-calendar-xmark text-4xl text-gray-300 mb-2"></i>
    <h5 class="text-gray-600 font-semibold mb-1">Chưa có thông tin buổi học</h5>
    <p class="text-xs text-gray-400">Học sinh chưa được xếp lớp hoặc chưa phát sinh lịch học.</p>
  </div>
</template>

<script>
  import axios from '../../../../http/axios.js'
  import moment from 'moment';
  import u from '../../../../until/helper.js'
  import select from 'vue-select'

  export default {
    components: {
      "vue-select": select,
      moment
    },
    props: {
      student_info: {
        type: Object,
        default: () => {}
      },
    },
    data() {
      return {
        done_sessions:[],
        next_sessions:[],
        contract_info:{}
      }
    },
    async created() {
      this.getSessions();
    },
    methods: {
      getSessionCardClass(item) {
        if (item.status == 2 || item.status == 3) return 'session-card-paused';
        if (item.attendance_status == 1) return 'session-card-attended';
        if (item.attendance_status == 3) return 'session-card-makeup';
        return 'session-card-charged';
      },
      getSessionStatusPillClass(item) {
        if (item.status == 2 || item.status == 3) return 'bg-amber-100 text-amber-800 border border-amber-300';
        if (item.attendance_status == 1) return 'bg-emerald-100 text-emerald-800 border border-emerald-300';
        if (item.attendance_status == 3) return 'bg-purple-100 text-purple-800 border border-purple-300';
        return 'bg-rose-100 text-rose-800 border border-rose-300';
      },
      getSessionStatusText(item) {
        if (item.status == 2) return 'Bảo lưu';
        if (item.status == 3) return 'Nghỉ lễ';
        if (item.attendance_status == 1) return 'Đã học';
        if (item.attendance_status == 3) return 'Đã học bù';
        return 'Đã chạy phí';
      },
      getSessions(){
        const data = {
          student_id: this.student_info.id,
        }
        this.$vs.loading()
        axios.p('/api/lms/students/sessions', data)
          .then((response) => {
            this.$vs.loading.close()
            this.contract_info = response.data.contract_info || {}
            this.done_sessions = response.data.done_sessions || []
            this.next_sessions = response.data.next_sessions || []
          })
          .catch((error) => {
            console.log(error);
            this.$vs.loading.close();
          })
      },
    }
  }
</script>

<style scoped>
.session-stat-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 14px 16px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

.session-chip-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 12px;
  text-align: center;
  transition: all 0.2s ease;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
}

.session-chip-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
}

.session-card-attended {
  border-color: #a7f3d0;
  background: #f0fdf4;
}

.session-card-makeup {
  border-color: #e9d5ff;
  background: #faf5ff;
}

.session-card-paused {
  border-color: #fde68a;
  background: #fffbeb;
}

.session-card-charged {
  border-color: #fecdd3;
  background: #fff1f2;
}

.session-upcoming {
  border-style: dashed;
  background: #f8fafc;
}

.session-order {
  font-size: 11px;
  font-weight: 700;
  color: #64748b;
  margin-bottom: 4px;
}

.session-class-name {
  font-size: 12px;
  margin-bottom: 2px;
}

.session-date {
  font-size: 11px;
}

.session-status-pill {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 10px;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 9999px;
}
</style>
