<template>
  <div class="student-detail-sessions mt-3" v-if="contract_info && contract_info.id">
    <!-- Session Stats Cards -->
    <div class="vx-row mb-4">
      <div class="vx-col sm:w-1/3 w-full mb-2">
        <div class="session-stat-card border-l-primary">
          <div class="session-stat-title">Tổng số buổi trong gói</div>
          <div class="session-stat-number text-gray-800">
            {{ contract_info.summary_sessions || 0 }} <span class="session-stat-unit">buổi</span>
          </div>
        </div>
      </div>
      <div class="vx-col sm:w-1/3 w-full mb-2">
        <div class="session-stat-card border-l-emerald">
          <div class="session-stat-title">Số buổi đã học / ghi nhận</div>
          <div class="session-stat-number text-emerald-600">
            {{ contract_info.done_sessions || 0 }} <span class="session-stat-unit">buổi</span>
          </div>
          <div class="session-stat-breakdown">
            <span class="c-present"><i class="fa-solid fa-circle-check"></i> Đi học {{ attendanceStats.present }}</span>
            <span class="c-absent"><i class="fa-solid fa-circle-xmark"></i> Nghỉ học {{ attendanceStats.absent }}</span>
            <span class="c-pending" v-if="attendanceStats.pending"><i class="fa-regular fa-circle"></i> Chưa điểm danh {{ attendanceStats.pending }}</span>
          </div>
        </div>
      </div>
      <div class="vx-col sm:w-1/3 w-full mb-2">
        <div class="session-stat-card border-l-amber">
          <div class="session-stat-title">Số buổi còn lại</div>
          <div class="session-stat-number text-amber-600">
            {{ (contract_info.summary_sessions || 0) - (contract_info.done_sessions || 0) }} <span class="session-stat-unit">buổi</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Legend -->
    <div class="session-legend-bar">
      <span class="font-bold text-gray-700 mr-2"><i class="fa-solid fa-circle-info mr-1 text-primary"></i> Chú thích:</span>
      <span class="legend-item"><span class="legend-dot bg-emerald"></span> Đi học</span>
      <span class="legend-item"><span class="legend-dot bg-rose"></span> Nghỉ học</span>
      <span class="legend-item"><span class="legend-dot bg-pending"></span> Chưa điểm danh</span>
      <span class="legend-item"><span class="legend-dot bg-amber"></span> Bảo lưu / Nghỉ lễ</span>
      <span class="legend-item"><span class="legend-dot bg-upcoming"></span> Sắp học</span>
    </div>

    <!-- Sessions Grid -->
    <div>
      <div class="sessions-chips-grid">
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
            <i class="fa-regular fa-calendar-days mr-1"></i>
            {{ item.class_date | formatDateViewDay }}
          </div>
          <div class="mt-2">
            <span class="session-status-pill" :class="getSessionStatusPillClass(item)">
              <i :class="getSessionStatusIcon(item)" class="mr-1"></i>{{ getSessionStatusText(item) }}
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
            <i class="fa-regular fa-calendar-days mr-1"></i>
            {{ item.class_date | formatDateViewDay }}
          </div>
          <div class="mt-2">
            <span class="session-status-pill pill-upcoming">
              <i class="fa-regular fa-clock mr-1"></i> Sắp học
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div v-else class="empty-state-box mt-3">
    <i class="fa-regular fa-calendar-xmark empty-state-icon"></i>
    <h5 class="empty-state-title">Chưa có thông tin buổi học</h5>
    <p class="empty-state-desc">Học sinh chưa được xếp lớp hoặc chưa phát sinh lịch học.</p>
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
        contract_info: {},
        done_sessions:[],
        next_sessions:[]
      }
    },
    filters: {
      formatDateViewDay(date) {
        if (!date) return '---';
        const d = moment(date);
        const dayOfWeek = ["CN", "T2", "T3", "T4", "T5", "T6", "T7"][d.day()];
        return `${dayOfWeek}, ${d.format('DD/MM/YYYY')}`;
      }
    },
    computed: {
      // Thống kê điểm danh trên các buổi đã trừ (status 0/1), bỏ qua bảo lưu / nghỉ lễ
      attendanceStats() {
        const res = { present: 0, absent: 0, pending: 0 }
        ;(this.done_sessions || []).forEach(item => {
          const t = this.sessionType(item)
          if (t === 'present') res.present++
          else if (t === 'absent') res.absent++
          else if (t === 'pending') res.pending++
        })
        return res
      }
    },
    async created() {
      this.getSessions();
    },
    methods: {
      /**
       * schedule_has_student.status : 0/1 = buổi tính phí, 2 = Bảo lưu, 3 = Nghỉ lễ
       * schedule_has_student.attendance_status : 0 = Chưa điểm danh, 1 = Đi học, 2 = Nghỉ học (3 = dữ liệu cũ "Đã học bù" → coi như Đi học)
       */
      sessionType(item) {
        if (item.status == 2) return 'reserve'
        if (item.status == 3) return 'holiday'
        if (item.attendance_status == 1 || item.attendance_status == 3) return 'present'
        if (item.attendance_status == 2) return 'absent'
        return 'pending'
      },
      getSessionCardClass(item) {
        return {
          present: 'session-card-attended',
          absent: 'session-card-absent',
          pending: 'session-card-pending',
          reserve: 'session-card-paused',
          holiday: 'session-card-paused',
        }[this.sessionType(item)]
      },
      getSessionStatusPillClass(item) {
        return {
          present: 'pill-attended',
          absent: 'pill-absent',
          pending: 'pill-pending',
          reserve: 'pill-paused',
          holiday: 'pill-paused',
        }[this.sessionType(item)]
      },
      getSessionStatusIcon(item) {
        return {
          present: 'fa-solid fa-circle-check',
          absent: 'fa-solid fa-circle-xmark',
          pending: 'fa-regular fa-circle',
          reserve: 'fa-solid fa-pause',
          holiday: 'fa-solid fa-umbrella-beach',
        }[this.sessionType(item)]
      },
      getSessionStatusText(item) {
        return {
          present: 'Đi học',
          absent: 'Nghỉ học',
          pending: 'Chưa điểm danh',
          reserve: 'Bảo lưu',
          holiday: 'Nghỉ lễ',
        }[this.sessionType(item)]
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
.empty-state-box {
  padding: 40px 20px;
  text-align: center;
  background: #f8fafc;
  border-radius: 8px;
  border: 1px dashed #cbd5e1;
}

.empty-state-icon {
  font-size: 36px;
  color: #94a3b8;
  margin-bottom: 8px;
}

.empty-state-title {
  color: #334155;
  font-weight: 700;
  margin-bottom: 4px;
}

.empty-state-desc {
  color: #64748b;
  font-size: 14px;
}

.session-stat-card {
  background: #ffffff;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  padding: 14px 18px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

.border-l-primary { border-left: 5px solid #7367f0 !important; }
.border-l-emerald { border-left: 5px solid #10b981 !important; }
.border-l-amber { border-left: 5px solid #f59e0b !important; }

.session-stat-title {
  font-size: 13.5px;
  font-weight: 600;
  color: #64748b;
}

.session-stat-number {
  font-size: 24px;
  font-weight: 700;
  margin-top: 4px;
  line-height: 1.2;
}

.session-stat-unit {
  font-size: 14px;
  font-weight: normal;
  color: #94a3b8;
}

.session-legend-bar {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 14px;
  padding: 10px 14px;
  background: #f8fafc;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  margin-bottom: 14px;
  font-size: 13.5px;
  color: #475569;
}

.legend-item {
  display: inline-flex;
  align-items: center;
}

.legend-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  margin-right: 6px;
  display: inline-block;
}

.bg-emerald { background-color: #10b981; }
.bg-pending { background-color: #fff; border: 2px solid #94a3b8; }
.bg-rose { background-color: #f43f5e; }
.bg-amber { background-color: #f59e0b; }
.bg-upcoming { background-color: #cbd5e1; }

.sessions-chips-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 12px;
}

.session-chip-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 10px 12px;
  transition: all 0.2s;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
}

.session-chip-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
}

.session-card-attended {
  border-color: #bbf7d0;
  background: #f0fdf4;
}

.session-card-absent {
  border-color: #fecaca;
  background: #fef2f2;
}

.session-card-pending {
  border-color: #cbd5e1;
  background: #ffffff;
}

.session-card-paused {
  border-color: #fef08a;
  background: #fffbeb;
}

.session-upcoming {
  border: 1px dashed #cbd5e1;
  background: #f8fafc;
}

.session-order {
  font-size: 13px;
  font-weight: 700;
  color: #64748b;
  margin-bottom: 3px;
}

.session-class-name {
  font-size: 14px;
  margin-bottom: 3px;
}

.session-date {
  font-size: 13px;
}

.session-status-pill {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  font-weight: 600;
  padding: 3px 10px;
  border-radius: 9999px;
  white-space: nowrap;
}

.pill-attended { background: #dcfce7; color: #15803d; }
.pill-absent { background: #fee2e2; color: #b91c1c; }
.pill-pending { background: #fff; color: #475569; border: 1px dashed #94a3b8; }
.pill-paused { background: #fef9c3; color: #a16207; }
.pill-upcoming { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }
.session-stat-breakdown {
  display: flex;
  flex-wrap: wrap;
  gap: 4px 12px;
  margin-top: 6px;
  font-size: 13px;
  font-weight: 600;
}
.session-stat-breakdown span { white-space: nowrap; }
.session-stat-breakdown i { margin-right: 3px; }
.c-present { color: #15803d; }
.c-absent { color: #dc2626; }
.c-pending { color: #64748b; }
</style>
