<template>
  <div id="page-student-detail">
    <!-- Back Navigation -->
    <div class="mb-3 flex items-center justify-between">
      <router-link to="/lms/students" class="inline-flex items-center text-sm font-semibold text-primary hover:underline">
        <i class="fa-solid fa-arrow-left mr-2"></i> Quay lại danh sách học sinh
      </router-link>
      <div class="text-xs text-gray-500">
        ID học sinh: <strong>#{{ $route.params.id }}</strong>
      </div>
    </div>

    <!-- Student Profile Hero Card -->
    <vx-card no-shadow class="mb-5 shadow-sm border border-solid border-grey-light rounded-lg profile-hero-card">
      <div class="flex flex-col lg:flex-row items-center lg:items-start gap-6">
        <!-- Avatar Column -->
        <div class="avatar-upload-col text-center flex-shrink-0">
          <div class="profile-avatar-container">
            <img
              class="profile-avatar-img"
              alt="avatar"
              :src="student_info.avatar_url || (student_info.gender == 'F' ? '/images/common/avatar-girl.svg' : '/images/common/avatar-boy.svg')"
            />
            <label for="account-upload" class="avatar-camera-btn" title="Thay đổi ảnh đại diện">
              <i class="fa-solid fa-camera"></i>
            </label>
            <input type="file" ref="file" id="account-upload" hidden accept="image/*" @change="submitFiles" />
          </div>
          <div class="text-xs text-gray-400 mt-2">Bấm icon camera để đổi ảnh</div>
        </div>

        <!-- Middle Info Column -->
        <div class="flex-grow-1 text-center lg:text-left">
          <div class="flex flex-wrap items-center justify-center lg:justify-start gap-3 mb-2">
            <h3 class="font-bold text-gray-900 m-0">{{ student_info.name || 'Học sinh' }}</h3>
            <!-- STK Code Badge -->
            <span class="stk-code-hero-badge" @click="copyCode(student_info.lms_code)" title="Bấm để copy mã học sinh">
              <i class="fa-solid fa-id-badge text-primary mr-1"></i>
              <strong>{{ student_info.lms_code || 'Chưa có mã' }}</strong>
              <i class="fa-regular fa-copy ml-1 text-gray-400"></i>
            </span>
            <!-- Status Badge -->
            <span :class="getStatusBadgeClass(student_info.status_label)">
              {{ student_info.status_label || 'Chưa xác định' }}
            </span>
          </div>

          <!-- Quick Meta Info -->
          <div class="flex flex-wrap items-center justify-center lg:justify-start gap-4 text-xs text-gray-600 mb-4 mt-2">
            <span v-if="student_info.gender" class="inline-flex items-center">
              <i :class="student_info.gender == 'F' ? 'fa-solid fa-venus text-pink-500' : 'fa-solid fa-mars text-blue-500'" class="mr-1"></i>
              {{ student_info.gender == 'F' ? 'Nữ' : 'Nam' }}
            </span>
            <span v-if="student_info.date_of_birth" class="inline-flex items-center">
              <i class="fa-regular fa-calendar text-primary mr-1"></i>
              {{ student_info.date_of_birth | formatDateView }}
            </span>
            <span v-if="student_info.branch_name" class="inline-flex items-center">
              <i class="fa-solid fa-building text-primary mr-1"></i>
              {{ student_info.branch_name }}
            </span>
          </div>

          <!-- Contact Details -->
          <div class="flex flex-wrap items-center justify-center lg:justify-start gap-4 text-xs text-gray-600 bg-slate-50 p-2.5 rounded-lg border border-slate-200">
            <div class="inline-flex items-center">
              <i class="fa-solid fa-user-tie text-gray-400 mr-1.5"></i>
              <span>Phụ huynh: <strong>{{ student_info.gud_name1 || 'Chưa cập nhật' }}</strong></span>
            </div>
            <div class="inline-flex items-center" v-if="student_info.gud_mobile1">
              <i class="fa-solid fa-phone text-success mr-1.5"></i>
              <a :href="'tel:' + student_info.gud_mobile1" class="text-primary hover:underline font-semibold">{{ student_info.gud_mobile1 }}</a>
            </div>
            <div class="inline-flex items-center" v-if="student_info.gud_email1">
              <i class="fa-solid fa-envelope text-primary mr-1.5"></i>
              <span>{{ student_info.gud_email1 }}</span>
            </div>
            <div class="inline-flex items-center" v-if="student_info.address">
              <i class="fa-solid fa-location-dot text-rose-500 mr-1.5"></i>
              <span>{{ student_info.address }}</span>
            </div>
          </div>
        </div>

        <!-- Right Stats Column (KPI Cards) -->
        <div class="flex flex-col sm:flex-row lg:flex-col gap-3 w-full lg:w-72 flex-shrink-0">
          <!-- Học phí Card -->
          <div class="kpi-metric-card">
            <div class="flex items-center justify-between mb-1">
              <span class="kpi-label"><i class="fa-solid fa-wallet text-primary mr-1"></i> Số dư học phí</span>
              <span class="text-xs text-gray-400">VNĐ</span>
            </div>
            <div class="kpi-value text-success">
              {{ (student_info.left_amount || 0) | formatNumber }}
            </div>
            <div class="text-xs text-gray-500 mt-1">
              Tổng đã đóng: <strong>{{ (student_info.init_total_charged || 0) | formatNumber }}</strong> đ
            </div>
          </div>

          <!-- Buổi học Card -->
          <div class="kpi-metric-card">
            <div class="flex items-center justify-between mb-1">
              <span class="kpi-label"><i class="fa-solid fa-clock text-amber-500 mr-1"></i> Số buổi còn lại</span>
              <span class="text-xs font-semibold" :class="(student_info.left_sessions || 0) > 5 ? 'text-success' : 'text-danger'">
                {{ (student_info.left_sessions || 0) > 5 ? 'Ổn định' : 'Sắp hết' }}
              </span>
            </div>
            <div class="kpi-value" :class="(student_info.left_sessions || 0) > 5 ? 'text-success' : 'text-danger'">
              {{ student_info.left_sessions || 0 }}
              <span class="text-xs text-gray-400 font-normal">/ {{ student_info.summary_sessions || 0 }} buổi</span>
            </div>
            <!-- Progress Bar -->
            <div class="mt-2" v-if="student_info.summary_sessions">
              <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                <span>Đã học: {{ student_info.done_sessions || 0 }} buổi</span>
                <span class="font-bold">{{ Math.round(((student_info.done_sessions || 0) * 100) / (student_info.summary_sessions || 1)) }}%</span>
              </div>
              <div class="hero-progress-bg">
                <div
                  class="hero-progress-bar"
                  :style="{ width: Math.min(100, Math.round(((student_info.done_sessions || 0) * 100) / (student_info.summary_sessions || 1))) + '%' }"
                ></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </vx-card>

    <!-- Detailed Tabs Card -->
    <vx-card no-shadow class="shadow-sm border border-solid border-grey-light rounded-lg">
      <vs-tabs v-model="active_tab" class="modern-student-tabs">
        <vs-tab label="Hồ sơ">
          <div class="tab-text pt-4">
            <student-info :student_info="student_info" />
          </div>
        </vs-tab>
        <vs-tab label="Gói phí">
          <div class="tab-text pt-4">
            <student-contracts :student_info="student_info" />
          </div>
        </vs-tab>
        <vs-tab label="Buổi học">
          <div class="tab-text pt-4">
            <student-sessions :student_info="student_info" />
          </div>
        </vs-tab>
        <vs-tab label="Vận hành">
          <div class="tab-text pt-4">
            <student-operating :student_info="student_info" />
          </div>
        </vs-tab>
        <vs-tab label="Đánh giá & nhận xét">
          <div class="tab-text pt-4">
            <student-assessments :student_info="student_info" />
          </div>
        </vs-tab>
        <vs-tab label="Bài test (IG.BH & UCREA)">
          <div class="tab-text pt-4">
            <student-evaluations :student_info="student_info" />
          </div>
        </vs-tab>
        <vs-tab label="Lịch sử">
          <div class="tab-text pt-4">
            <student-logs :student_info="student_info" />
          </div>
        </vs-tab>
        <vs-tab label="Hồ sơ lưu trữ">
          <div class="tab-text pt-4">
            <student-files :student_info="student_info" />
          </div>
        </vs-tab>
      </vs-tabs>
    </vx-card>
  </div>
</template>

<script>
import Multiselect from "vue-multiselect";
import axios from '../../../http/axios.js';
import u from '../../../until/helper.js'
import moment from 'moment';
import datepicker from "vue2-datepicker";
import select from 'vue-select'
import studentInfo from './components/studentInfo.vue'
import studentLogs from './components/studentLogs.vue'
import studentContracts from './components/studentContracts.vue'
import studentSessions from './components/studentSessions.vue'
import studentAssessments from './components/studentAssessments.vue'
import studentOperating from './components/studentOperating.vue'
import studentFiles from './components/studentFiles.vue'
import studentEvaluations from './components/studentEvaluations.vue'

export default {
  components: {
    "vue-select": select,
    Multiselect,
    moment,
    datepicker,
    studentInfo,
    studentLogs,
    studentContracts,
    studentSessions,
    studentAssessments,
    studentOperating,
    studentFiles,
    studentEvaluations
  },
  data() {
    return {
      active_tab: 0,
      student_info: {}
    };
  },
  async created() {
    this.loadDetail();
  },
  methods: {
    submitFiles() {
      if (this.$refs.file.files.length) {
        this.$vs.loading()
        const formData = new FormData();
        for (var i = 0; i < this.$refs.file.files.length; i++) {
          let file = this.$refs.file.files[i];
          formData.append('files[' + i + ']', file);
        }
        formData.append('student_id', this.$route.params.id);
        axios.p('/api/lms/students/upload-avatar', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          },
        }).then((response) => {
          this.$vs.notify({
            title: 'Thành công',
            text: 'Đã cập nhật ảnh đại diện học sinh',
            color: 'success',
            icon: 'check',
            position: 'top-right'
          });
          this.loadDetail();
        }).catch((error) => {
          console.log(error);
          this.$vs.loading.close();
        })
      }
    },
    loadDetail() {
      this.$vs.loading();
      axios.g(`/api/lms/students/show/${this.$route.params.id}`)
        .then(response => {
          this.$vs.loading.close();
          if (response.data && response.data.length !== 0) {
            this.student_info = response.data
          } else {
            this.$router.push({ path: `/lms/students` });
          }
        })
        .catch(() => {
          this.$vs.loading.close();
        });
    },
    copyCode(code) {
      if (!code) return;
      navigator.clipboard.writeText(code).then(() => {
        this.$vs.notify({
          title: 'Thành công',
          text: `Đã sao chép mã học sinh: ${code}`,
          color: 'success',
          icon: 'check',
          position: 'top-right'
        });
      });
    },
    getStatusBadgeClass(status) {
      if (!status) return 'status-hero-badge badge-default';
      const s = status.toLowerCase();
      if (s.includes('chính thức')) return 'status-hero-badge badge-success';
      if (s.includes('học thử')) return 'status-hero-badge badge-info';
      if (s.includes('chờ')) return 'status-hero-badge badge-warning';
      if (s.includes('hết') || s.includes('chưa')) return 'status-hero-badge badge-danger';
      return 'status-hero-badge badge-default';
    }
  }
}
</script>

<style scoped>
.profile-hero-card {
  background: #ffffff;
}

.profile-avatar-container {
  position: relative;
  width: 110px;
  height: 110px;
  border-radius: 50%;
  margin: 0 auto;
  border: 3px solid #e2e8f0;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  overflow: visible;
}

.profile-avatar-img {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
  background: #f1f5f9;
}

.avatar-camera-btn {
  position: absolute;
  bottom: 0;
  right: 0;
  width: 32px;
  height: 32px;
  background: #2563eb;
  color: #ffffff;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  border: 2px solid #ffffff;
  box-shadow: 0 2px 4px rgba(0,0,0,0.15);
  transition: all 0.2s;
}

.avatar-camera-btn:hover {
  background: #1d4ed8;
  transform: scale(1.1);
}

.stk-code-hero-badge {
  display: inline-flex;
  align-items: center;
  background: #eff6ff;
  border: 1px dashed #3b82f6;
  color: #1d4ed8;
  padding: 3px 10px;
  border-radius: 6px;
  font-size: 13px;
  cursor: pointer;
  transition: all 0.2s;
}

.stk-code-hero-badge:hover {
  background: #dbeafe;
}

.status-hero-badge {
  display: inline-block;
  padding: 3px 12px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
}

.badge-success {
  background: #dcfce7;
  color: #166534;
}

.badge-info {
  background: #e0f2fe;
  color: #0369a1;
}

.badge-warning {
  background: #fef3c7;
  color: #b45309;
}

.badge-danger {
  background: #ffe4e6;
  color: #be123c;
}

.badge-default {
  background: #f1f5f9;
  color: #475569;
}

.kpi-metric-card {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 12px 14px;
}

.kpi-label {
  font-size: 11.5px;
  font-weight: 600;
  color: #475569;
  text-transform: uppercase;
  letter-spacing: 0.3px;
}

.kpi-value {
  font-size: 20px;
  font-weight: 700;
  line-height: 1.2;
}

.hero-progress-bg {
  width: 100%;
  height: 6px;
  background: #e2e8f0;
  border-radius: 3px;
  overflow: hidden;
}

.hero-progress-bar {
  height: 100%;
  background: linear-gradient(90deg, #10b981, #059669);
  border-radius: 3px;
  transition: width 0.3s ease;
}
</style>
