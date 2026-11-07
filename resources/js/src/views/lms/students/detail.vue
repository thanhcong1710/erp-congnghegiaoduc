<template>
  <div id="page-student-detail">
    <!-- Back Navigation -->
    <div class="mb-2 flex items-center justify-between">
      <router-link to="/lms/students" class="inline-flex items-center text-xs font-semibold text-primary hover:underline">
        <i class="fa-solid fa-arrow-left mr-1.5"></i> Quay lại danh sách học sinh
      </router-link>
      <div class="text-xs text-gray-500">
        ID học sinh: <strong>#{{ $route.params.id }}</strong>
      </div>
    </div>

    <!-- Student Profile Hero Banner - Compact & Bulletproof -->
    <div class="profile-hero-bar mb-3">
      <!-- 1. Left Avatar Box -->
      <div class="hero-avatar-wrapper">
        <img
          class="hero-avatar-image"
          alt="avatar"
          :src="student_info.avatar_url || (student_info.gender == 'F' ? '/images/common/avatar-girl.svg' : '/images/common/avatar-boy.svg')"
        />
        <label for="account-upload" class="hero-camera-btn" title="Đổi ảnh đại diện">
          <i class="fa-solid fa-camera"></i>
        </label>
        <input type="file" ref="file" id="account-upload" hidden accept="image/*" @change="submitFiles" />
      </div>

      <!-- 2. Middle Main Info Block -->
      <div class="hero-info-wrapper">
        <!-- Line 1: Student Name + Code + Status -->
        <div class="hero-title-line">
          <h3 class="hero-name">{{ student_info.name || 'Học sinh' }}</h3>
          <span class="hero-stk-badge" @click="copyCode(student_info.lms_code)" title="Bấm để sao chép mã">
            <i class="fa-solid fa-id-badge text-primary mr-1"></i>
            <strong>{{ student_info.lms_code || 'Chưa có mã' }}</strong>
            <i class="fa-regular fa-copy ml-1 opacity-70"></i>
          </span>
          <span :class="getStatusBadgeClass(student_info.status_label)">
            {{ student_info.status_label || 'Chưa xác định' }}
          </span>
        </div>

        <!-- Line 2: Demographic & Branch Tags -->
        <div class="hero-meta-line">
          <span v-if="student_info.gender" class="hero-tag">
            <i :class="student_info.gender == 'F' ? 'fa-solid fa-venus text-pink-500' : 'fa-solid fa-mars text-blue-500'" class="mr-1"></i>
            {{ student_info.gender == 'F' ? 'Nữ' : 'Nam' }}
          </span>
          <span v-if="student_info.date_of_birth" class="hero-tag">
            <i class="fa-regular fa-calendar text-primary mr-1"></i>
            {{ student_info.date_of_birth | formatDateView }}
          </span>
          <span v-if="student_info.branch_name" class="hero-tag">
            <i class="fa-solid fa-building text-primary mr-1"></i>
            {{ student_info.branch_name }}
          </span>
        </div>

        <!-- Line 3: Compact Contact Line -->
        <div class="hero-contact-line">
          <span class="hero-contact-chip">
            <i class="fa-solid fa-user-tie text-gray-400 mr-1"></i>
            Phụ huynh: <strong>{{ student_info.gud_name1 || 'Chưa cập nhật' }}</strong>
          </span>
          <span class="hero-contact-chip" v-if="student_info.gud_mobile1">
            <i class="fa-solid fa-phone text-emerald-500 mr-1"></i>
            <a :href="'tel:' + student_info.gud_mobile1" class="text-primary font-bold hover:underline">{{ student_info.gud_mobile1 }}</a>
          </span>
          <span class="hero-contact-chip" v-if="student_info.gud_email1">
            <i class="fa-solid fa-envelope text-gray-400 mr-1"></i>
            {{ student_info.gud_email1 }}
          </span>
          <span class="hero-contact-chip" v-if="student_info.address" :title="student_info.address">
            <i class="fa-solid fa-location-dot text-rose-500 mr-1"></i>
            {{ student_info.address }}
          </span>
        </div>
      </div>

      <!-- 3. Right Stats KPI Boxes (Compact) -->
      <div class="hero-kpi-wrapper">
        <div class="hero-kpi-card">
          <div class="hero-kpi-header">
            <span><i class="fa-solid fa-wallet text-primary mr-1"></i> Số dư học phí</span>
            <span class="text-2xs text-gray-400">VNĐ</span>
          </div>
          <div class="hero-kpi-number text-emerald-600">
            {{ (student_info.left_amount || 0) | formatNumber }}
          </div>
          <div class="hero-kpi-sub">
            Tổng đã đóng: <strong>{{ (student_info.init_total_charged || 0) | formatNumber }}</strong> đ
          </div>
        </div>

        <div class="hero-kpi-card">
          <div class="hero-kpi-header">
            <span><i class="fa-solid fa-clock text-amber-500 mr-1"></i> Buổi còn lại</span>
            <span class="text-2xs font-semibold" :class="(student_info.left_sessions || 0) > 5 ? 'text-emerald-600' : 'text-rose-500'">
              {{ (student_info.left_sessions || 0) > 5 ? 'Ổn định' : 'Sắp hết' }}
            </span>
          </div>
          <div class="hero-kpi-number" :class="(student_info.left_sessions || 0) > 5 ? 'text-emerald-600' : 'text-rose-500'">
            {{ student_info.left_sessions || 0 }}
            <span class="hero-kpi-max">/ {{ student_info.summary_sessions || 0 }}</span>
          </div>
          <div class="hero-kpi-sub">
            Đã học: <strong>{{ student_info.done_sessions || 0 }}</strong> buổi
          </div>
        </div>
      </div>
    </div>

    <!-- Detailed Tabs Card -->
    <vx-card no-shadow class="shadow-sm border border-solid border-grey-light rounded-lg compact-tabs-card">
      <vs-tabs v-model="active_tab" class="modern-student-tabs">
        <vs-tab label="Hồ sơ">
          <div class="tab-content-area" v-if="student_info && student_info.id">
            <student-info :student_info="student_info" />
          </div>
        </vs-tab>
        <vs-tab label="Gói phí">
          <div class="tab-content-area" v-if="student_info && student_info.id">
            <student-contracts :student_info="student_info" />
          </div>
        </vs-tab>
        <vs-tab label="Buổi học">
          <div class="tab-content-area" v-if="student_info && student_info.id">
            <student-sessions :student_info="student_info" />
          </div>
        </vs-tab>
        <vs-tab label="Vận hành">
          <div class="tab-content-area" v-if="student_info && student_info.id">
            <student-operating :student_info="student_info" />
          </div>
        </vs-tab>
        <vs-tab label="Đánh giá & nhận xét">
          <div class="tab-content-area" v-if="student_info && student_info.id">
            <student-assessments :student_info="student_info" />
          </div>
        </vs-tab>
        <vs-tab label="Bài test (IG.BH & UCREA)">
          <div class="tab-content-area" v-if="student_info && student_info.id">
            <student-evaluations :student_info="student_info" />
          </div>
        </vs-tab>
        <vs-tab label="Lịch sử">
          <div class="tab-content-area" v-if="student_info && student_info.id">
            <student-logs :student_info="student_info" />
          </div>
        </vs-tab>
        <vs-tab label="Hồ sơ lưu trữ">
          <div class="tab-content-area" v-if="student_info && student_info.id">
            <student-files :student_info="student_info" />
          </div>
        </vs-tab>
      </vs-tabs>
    </vx-card>
  </div>
</template>

<script>
import Multiselect from "vue-multiselect";
import axios from "../../../http/axios.js";
import StudentInfo from "./components/studentInfo.vue";
import StudentContracts from "./components/studentContracts.vue";
import StudentSessions from "./components/studentSessions.vue";
import StudentOperating from "./components/studentOperating.vue";
import StudentAssessments from "./components/studentAssessments.vue";
import StudentEvaluations from "./components/studentEvaluations.vue";
import StudentLogs from "./components/studentLogs.vue";
import StudentFiles from "./components/studentFiles.vue";

export default {
  components: {
    Multiselect,
    StudentInfo,
    StudentContracts,
    StudentSessions,
    StudentOperating,
    StudentAssessments,
    StudentEvaluations,
    StudentLogs,
    StudentFiles
  },
  data() {
    return {
      active_tab: 0,
      student_info: {},
      file: "",
    };
  },
  async created() {
    this.getData();
  },
  methods: {
    copyCode(code) {
      if (!code) return;
      navigator.clipboard.writeText(code).then(() => {
        this.$vs.notify({
          title: "Đã sao chép",
          text: `Mã học sinh "${code}" đã được lưu vào clipboard`,
          color: "success",
          iconPack: "feather",
          icon: "icon-check",
          time: 2500
        });
      });
    },
    getStatusBadgeClass(status) {
      if (!status) return "status-pill badge-default";
      const s = status.toLowerCase();
      if (s.includes("chính thức")) return "status-pill badge-success";
      if (s.includes("học thử")) return "status-pill badge-info";
      if (s.includes("chờ xếp")) return "status-pill badge-warning";
      if (s.includes("bảo lưu") || s.includes("đình chỉ") || s.includes("bỏ học")) return "status-pill badge-danger";
      return "status-pill badge-default";
    },
    getData() {
      this.$vs.loading();
      axios.g(`/api/lms/students/${this.$route.params.id}/detail`)
        .then((response) => {
          this.$vs.loading.close();
          this.student_info = response.data;
        })
        .catch((error) => {
          console.log(error);
          this.$vs.loading.close();
        });
    },
    submitFiles() {
      let formData = new FormData();
      formData.append("avatar", this.$refs.file.files[0]);
      formData.append("student_id", this.student_info.id);
      this.$vs.loading();
      axios
        .p("/api/lms/students/update_avatar", formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        })
        .then((response) => {
          this.$vs.loading.close();
          this.student_info.avatar_url = response.data.avatar_url;
          this.$vs.notify({
            title: "Thành Công",
            text: response.data.message,
            color: "success",
            iconPack: "feather",
            icon: "icon-check",
          });
        })
        .catch((e) => {
          console.log(e);
          this.$vs.loading.close();
        });
    },
  },
};
</script>

<style scoped>
/* Bulletproof Compact Profile Hero Bar */
.profile-hero-bar {
  display: flex;
  align-items: center;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 12px 18px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
  gap: 16px;
}

@media (max-width: 991px) {
  .profile-hero-bar {
    flex-direction: column;
    align-items: stretch;
  }
}

/* Avatar Box */
.hero-avatar-wrapper {
  position: relative;
  width: 68px;
  height: 68px;
  flex-shrink: 0;
}

.hero-avatar-image {
  width: 68px;
  height: 68px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #7367f0;
  background: #f8fafc;
}

.hero-camera-btn {
  position: absolute;
  bottom: 0;
  right: 0;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: #7367f0;
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  cursor: pointer;
  border: 2px solid #ffffff;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
  transition: all 0.2s ease;
}

.hero-camera-btn:hover {
  background: #5e50ee;
  transform: scale(1.1);
}

/* Main Info Wrapper */
.hero-info-wrapper {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.hero-title-line {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 8px;
}

.hero-name {
  font-size: 17px;
  font-weight: 700;
  color: #1e293b;
  margin: 0;
  line-height: 1.2;
}

.hero-stk-badge {
  display: inline-flex;
  align-items: center;
  font-size: 11.5px;
  font-weight: 700;
  color: #7367f0;
  background: #f5f3ff;
  border: 1px solid #ddd6fe;
  padding: 2px 7px;
  border-radius: 5px;
  cursor: pointer;
}

.status-pill {
  display: inline-flex;
  align-items: center;
  padding: 2px 8px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 600;
}

.badge-success { background: #dcfce7; color: #15803d; }
.badge-info { background: #e0f2fe; color: #0369a1; }
.badge-warning { background: #fef9c3; color: #a16207; }
.badge-danger { background: #fee2e2; color: #b91c1c; }
.badge-default { background: #f1f5f9; color: #475569; }

/* Demographic Line */
.hero-meta-line {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
  font-size: 12px;
  color: #475569;
}

.hero-tag {
  display: inline-flex;
  align-items: center;
}

/* Contact Line */
.hero-contact-line {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
  font-size: 11.5px;
  color: #64748b;
  background: #f8fafc;
  padding: 4px 10px;
  border-radius: 6px;
  border: 1px solid #f1f5f9;
}

.hero-contact-chip {
  display: inline-flex;
  align-items: center;
  white-space: nowrap;
}

/* Right KPI Wrapper */
.hero-kpi-wrapper {
  display: flex;
  gap: 10px;
  flex-shrink: 0;
}

@media (max-width: 640px) {
  .hero-kpi-wrapper {
    flex-direction: column;
  }
}

.hero-kpi-card {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  padding: 8px 12px;
  min-width: 140px;
}

.hero-kpi-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 11px;
  font-weight: 600;
  color: #64748b;
  margin-bottom: 2px;
}

.hero-kpi-number {
  font-size: 16px;
  font-weight: 700;
  line-height: 1.2;
}

.hero-kpi-max {
  font-size: 11px;
  font-weight: normal;
  color: #94a3b8;
}

.hero-kpi-sub {
  font-size: 10.5px;
  color: #64748b;
  margin-top: 2px;
}

/* Compact Tabs Card */
.compact-tabs-card >>> .vx-card__collapsible-content .vx-card__body {
  padding: 10px 16px !important;
}

.tab-content-area {
  padding-top: 10px;
}
</style>
