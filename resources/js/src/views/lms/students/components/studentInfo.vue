<template>
  <div class="student-compact-info">
    <!-- Top Action Toolbar -->
    <div class="compact-info-toolbar">
      <div class="flex items-center">
        <span v-if="disabled_edit" class="info-mode-indicator text-gray-500">
          <i class="fa-solid fa-eye text-primary mr-1"></i> Chế độ xem thông tin hồ sơ
        </span>
        <span v-else class="info-mode-indicator text-amber-700 bg-amber-50 px-2 py-1 rounded border border-amber-200">
          <i class="fa-solid fa-pen-to-square text-amber-500 mr-1"></i> Đang chỉnh sửa thông tin học sinh
        </span>
      </div>

      <div class="flex items-center gap-2">
        <template v-if="disabled_edit">
          <button class="btn-compact-action btn-edit" @click="disabled_edit=false">
            <i class="fa-solid fa-pen-to-square mr-1"></i> Chỉnh sửa hồ sơ
          </button>
        </template>
        <template v-else>
          <button class="btn-compact-action btn-cancel" @click="reloadPage">
            <i class="fa-solid fa-xmark mr-1"></i> Hủy
          </button>
          <button class="btn-compact-action btn-save" @click="saveInfo">
            <i class="fa-solid fa-floppy-disk mr-1"></i> Lưu thay đổi
          </button>
        </template>
      </div>
    </div>

    <!-- Alert Box -->
    <vs-alert :active.sync="alert.active" class="mb-3 mt-2" :color="alert.color" closable icon-pack="feather" close-icon="icon-x">
      <div v-html="alert.body"></div>
    </vs-alert>

    <!-- FORM SECTIONS -->
    <div class="form-container">
      <!-- 1. THÔNG TIN HỌC SINH -->
      <div class="compact-section-divider">
        <i class="fa-solid fa-graduation-cap text-primary mr-1.5"></i>
        <span>1. THÔNG TIN HỌC SINH</span>
      </div>

      <div class="compact-grid-4">
        <!-- Mã Học sinh -->
        <div class="form-group">
          <label class="form-label">Mã học sinh</label>
          <div class="form-control-readonly font-bold text-primary">
            {{ student_info.lms_code || '---' }}
          </div>
        </div>

        <!-- Họ và tên -->
        <div class="form-group">
          <label class="form-label">Họ và tên <span class="text-danger font-bold">*</span></label>
          <input
            class="form-control-compact"
            type="text"
            placeholder="Nhập họ và tên"
            v-model="student_info.name"
            :disabled="disabled_edit"
          />
        </div>

        <!-- Giới tính -->
        <div class="form-group">
          <label class="form-label">Giới tính <span class="text-danger font-bold">*</span></label>
          <select class="form-control-compact" v-model="student_info.gender" :disabled="disabled_edit">
            <option value="M">👦 Nam</option>
            <option value="F">👧 Nữ</option>
          </select>
        </div>

        <!-- Ngày sinh -->
        <div class="form-group">
          <label class="form-label">Ngày sinh / Năm sinh <span class="text-danger font-bold">*</span></label>
          <input
            v-if="disabled_edit"
            class="vs-inputx vs-input--input normal w-full"
            type="text"
            :value="birthday_text"
            placeholder="Chưa cập nhật"
            disabled
          >
          <birthday-input v-else v-model="birthday_text" />
        </div>

        <!-- Trường học -->
        <div class="form-group">
          <label class="form-label">Trường học</label>
          <input
            class="form-control-compact"
            type="text"
            placeholder="Trường đang học"
            v-model="student_info.school"
            :disabled="disabled_edit"
          />
        </div>

        <!-- Khối lớp -->
        <div class="form-group">
          <label class="form-label">Khối lớp</label>
          <select class="form-control-compact" v-model="student_info.school_grade" :disabled="disabled_edit">
            <option value="">-- Chọn khối lớp --</option>
            <option value="toddler 3 age">Mầm non 3 tuổi</option>
            <option value="toddler 4 age">Mầm non 4 tuổi</option>
            <option value="toddler 5 age">Mầm non 5 tuổi (Tiền TH)</option>
            <option value="toddler 6 age">Lớp 1 chuẩn bị</option>
            <option value="grade 1">Lớp 1</option>
            <option value="grade 2">Lớp 2</option>
            <option value="grade 3">Lớp 3</option>
            <option value="grade 4">Lớp 4</option>
            <option value="grade 5">Lớp 5</option>
            <option value="grade 6">Lớp 6</option>
            <option value="grade 7">Lớp 7</option>
            <option value="grade 8">Lớp 8</option>
            <option value="grade 9">Lớp 9</option>
            <option value="grade 10">Lớp 10</option>
            <option value="grade 11">Lớp 11</option>
            <option value="grade 12">Lớp 12</option>
            <option value="other">Khác</option>
          </select>
        </div>

        <!-- Tỉnh / Thành phố -->
        <div class="form-group">
          <label class="form-label">Tỉnh / Thành phố</label>
          <vue-select
            class="form-vselect-compact"
            label="name"
            placeholder="Chọn Tỉnh/TP"
            :options="html.province.list"
            v-model="html.province.item"
            :searchable="true"
            language="tv-VN"
            @input="getDistrict"
            :disabled="disabled_edit"
          ></vue-select>
        </div>

        <!-- Quận / Huyện -->
        <div class="form-group">
          <label class="form-label">Quận / Huyện</label>
          <vue-select
            class="form-vselect-compact"
            label="name"
            placeholder="Chọn Quận/Huyện"
            :options="html.district.list"
            v-model="html.district.item"
            :searchable="true"
            language="tv-VN"
            @input="saveDistrict"
            :disabled="disabled_edit"
          ></vue-select>
        </div>

        <!-- Địa chỉ chi tiết (span 2) -->
        <div class="form-group span-2">
          <label class="form-label">Địa chỉ cư trú chi tiết</label>
          <input
            class="form-control-compact"
            type="text"
            placeholder="Số nhà, đường phố, phường xã"
            v-model="student_info.address"
            :disabled="disabled_edit"
          />
        </div>

        <!-- Nguồn -->
        <div class="form-group">
          <label class="form-label">Nguồn khách hàng</label>
          <div class="form-control-readonly">{{ student_info.source_name || 'Chưa xác định' }}</div>
        </div>

        <!-- Trạng thái -->
        <div class="form-group">
          <label class="form-label">Trạng thái học sinh</label>
          <div class="form-control-readonly font-semibold text-primary">
            {{ student_info.status_label || 'Chưa xác định' }}
          </div>
        </div>

        <!-- Ghi chú học sinh (span 4) -->
        <div class="form-group span-4">
          <label class="form-label">Ghi chú học sinh</label>
          <input
            class="form-control-compact"
            type="text"
            placeholder="Ghi chú về học sinh..."
            v-model="student_info.note"
            :disabled="disabled_edit"
          />
        </div>
      </div>

      <!-- 2. THÔNG TIN PHỤ HUYNH -->
      <div class="compact-section-divider mt-3">
        <i class="fa-solid fa-people-roof text-emerald-600 mr-1.5"></i>
        <span>2. THÔNG TIN PHỤ HUYNH & NGƯỜI GIÁM HỘ</span>
      </div>

      <div class="compact-grid-2">
        <!-- Phụ huynh 1 (Chính) -->
        <div class="parent-compact-box">
          <div class="parent-compact-header text-emerald-800">
            <i class="fa-solid fa-user-tie mr-1"></i> Phụ huynh 1 (Người liên hệ chính)
          </div>
          <div class="compact-grid-2 mt-2">
            <div class="form-group">
              <label class="form-label">Họ tên <span class="text-danger font-bold">*</span></label>
              <input
                class="form-control-compact"
                type="text"
                placeholder="Họ tên PH 1"
                v-model="student_info.gud_name1"
                :disabled="disabled_edit"
              />
            </div>
            <div class="form-group">
              <label class="form-label">Số điện thoại <span class="text-danger font-bold">*</span></label>
              <div v-if="disabled_edit" class="form-control-readonly">
                <a v-if="student_info.gud_mobile1" :href="'tel:' + student_info.gud_mobile1" class="text-primary font-bold hover:underline">
                  <i class="fa-solid fa-phone text-emerald-500 mr-1"></i> {{ student_info.gud_mobile1 }}
                </a>
                <span v-else class="text-gray-400">Chưa có</span>
              </div>
              <input
                v-else
                class="form-control-compact"
                type="text"
                v-model="student_info.gud_mobile1"
                disabled="true"
                title="SĐT chính quản lý theo khách hàng"
              />
            </div>
            <div class="form-group">
              <label class="form-label">Email</label>
              <input
                class="form-control-compact"
                type="email"
                placeholder="email@example.com"
                v-model="student_info.gud_email1"
                :disabled="disabled_edit"
              />
            </div>
            <div class="form-group">
              <label class="form-label">Ngày sinh</label>
              <datepicker
                class="w-full form-datepicker-compact"
                v-model="student_info.gud_birth_day1"
                placeholder="YYYY-MM-DD"
                :lang="datepickerOptions.lang"
                @change="selectDateGudBirthDay1"
                :disabled="disabled_edit"
              />
            </div>
            <div class="form-group span-2">
              <label class="form-label">Nghề nghiệp</label>
              <vue-select
                class="form-vselect-compact"
                label="title"
                placeholder="Chọn nghề nghiệp"
                :options="html.jobs.list"
                v-model="html.jobs.item"
                :searchable="true"
                language="tv-VN"
                @input="saveJob"
                :disabled="disabled_edit"
              ></vue-select>
            </div>
          </div>
        </div>

        <!-- Phụ huynh 2 (Phụ) -->
        <div class="parent-compact-box">
          <div class="parent-compact-header text-slate-700">
            <i class="fa-solid fa-user-group mr-1"></i> Phụ huynh 2 (Thông tin liên hệ phụ)
          </div>
          <div class="compact-grid-2 mt-2">
            <div class="form-group">
              <label class="form-label">Họ tên PH 2</label>
              <input
                class="form-control-compact"
                type="text"
                placeholder="Họ tên PH 2"
                v-model="student_info.gud_name2"
                :disabled="disabled_edit"
              />
            </div>
            <div class="form-group">
              <label class="form-label">Số điện thoại 2</label>
              <input
                class="form-control-compact"
                type="text"
                placeholder="SĐT phụ"
                v-model="student_info.gud_mobile2"
                :disabled="disabled_edit"
              />
            </div>
            <div class="form-group">
              <label class="form-label">Email 2</label>
              <input
                class="form-control-compact"
                type="email"
                placeholder="email@example.com"
                v-model="student_info.gud_email2"
                :disabled="disabled_edit"
              />
            </div>
            <div class="form-group">
              <label class="form-label">Ngày sinh 2</label>
              <datepicker
                class="w-full form-datepicker-compact"
                v-model="student_info.gud_birth_day2"
                placeholder="YYYY-MM-DD"
                :lang="datepickerOptions.lang"
                @change="selectDateGudBirthDay2"
                :disabled="disabled_edit"
              />
            </div>
            <div class="form-group span-2">
              <label class="form-label">Nghề nghiệp 2</label>
              <vue-select
                class="form-vselect-compact"
                label="title"
                placeholder="Chọn nghề nghiệp"
                :options="html.jobs.list"
                v-model="html.jobs.item2"
                :searchable="true"
                language="tv-VN"
                @input="saveJob2"
                :disabled="disabled_edit"
              ></vue-select>
            </div>
          </div>
        </div>

        <!-- Nguyện vọng phụ huynh (span 2) -->
        <div class="form-group span-2 mt-1">
          <label class="form-label"><i class="fa-solid fa-heart text-rose-500 mr-1"></i> Nguyện vọng phụ huynh</label>
          <input
            class="form-control-compact"
            type="text"
            placeholder="Nguyện vọng, mong muốn học tập của phụ huynh..."
            v-model="student_info.aspiration"
            :disabled="disabled_edit"
          />
        </div>
      </div>

      <!-- 3. TRUNG TÂM & NHÂN SỰ -->
      <div class="compact-section-divider mt-3">
        <i class="fa-solid fa-building-user text-amber-600 mr-1.5"></i>
        <span>3. TRUNG TÂM & NHÂN SỰ QUẢN LÝ</span>
      </div>

      <div class="compact-grid-3">
        <!-- Trung tâm -->
        <div class="form-group">
          <label class="form-label">Trung tâm phụ trách</label>
          <div class="form-control-readonly font-bold text-gray-800">
            <i class="fa-solid fa-building text-primary mr-1"></i> {{ student_info.branch_name || 'Chưa gắn' }}
          </div>
        </div>

        <!-- EC -->
        <div class="form-group">
          <label class="form-label">Chuyên viên tư vấn (EC)</label>
          <vue-select
            class="form-vselect-compact"
            label="label"
            placeholder="Chọn EC"
            :options="html.ecs.list"
            v-model="html.ecs.item"
            :searchable="true"
            language="tv-VN"
            @input="saveEC"
            :disabled="disabled_edit"
          ></vue-select>
        </div>

        <!-- CM -->
        <div class="form-group">
          <label class="form-label">Vận hành lớp (CM)</label>
          <vue-select
            class="form-vselect-compact"
            label="label"
            placeholder="Chọn CM"
            :options="html.cms.list"
            v-model="html.cms.item"
            :searchable="true"
            language="tv-VN"
            @input="saveCM"
            :disabled="disabled_edit"
          ></vue-select>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "../../../../http/axios.js";
import moment from "moment";
import u from "../../../../until/helper.js";
import datepicker from "vue2-datepicker";
import BirthdayInput from "../../../../components/BirthdayInput.vue";
import select from "vue-select";

export default {
  components: {
    "vue-select": select,
    moment,
    datepicker,
    BirthdayInput,
  },
  watch: {
    // Đồng bộ ô nhập tay khi dữ liệu học sinh được nạp/đổi
    "student_info.date_of_birth": {
      immediate: true,
      handler() {
        this.birthday_text = u.formatBirthdayInput(
          this.student_info.date_of_birth,
          this.student_info.birthday_year_only
        );
      },
    },
    "student_info.birthday_year_only"() {
      this.birthday_text = u.formatBirthdayInput(
        this.student_info.date_of_birth,
        this.student_info.birthday_year_only
      );
    },
  },
  props: {
    student_info: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      datepickerOptions: {
        closed: true,
        value: "",
        minDate: "",
        lang: {
          days: ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
          months: [
            "Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6",
            "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"
          ],
        },
      },
      alert: {
        active: false,
        body: "",
        color: "",
      },
      disabled_edit: true,
      birthday_text: "",
      html: {
        province: { item: "", list: [] },
        district: { item: "", list: [] },
        jobs: { item2: "", item: "", list: [] },
        ecs: { item: "", list: [] },
        cms: { item: "", list: [] },
      },
      tmp_district_id: "",
      init_lms_id: 0,
    };
  },
  async created() {
    await axios.g(`/api/system/provinces`).then((res) => {
      this.html.province.list = res.data;
    });
    await axios.g(`/api/system/jobs`).then((res) => {
      this.html.jobs.list = res.data;
    });
    const branch_id = (this.student_info && this.student_info.branch_id) ? this.student_info.branch_id : 0;
    await axios.g(`/api/system/cms/${branch_id}`).then((res) => {
      this.html.cms.list = res.data;
    });
    await axios.g(`/api/system/ecs/${branch_id}`).then((res) => {
      this.html.ecs.list = res.data;
    });
    if (this.student_info) {
      if (this.student_info.gud_job1) {
        this.html.jobs.item = this.html.jobs.list.filter((i) => i.id == this.student_info.gud_job1)[0];
      }
      if (this.student_info.gud_job2) {
        this.html.jobs.item2 = this.html.jobs.list.filter((i) => i.id == this.student_info.gud_job2)[0];
      }
      if (this.student_info.province_id) {
        this.html.province.item = this.html.province.list.filter((i) => i.id == this.student_info.province_id)[0];
        this.tmp_district_id = this.student_info.district_id;
        this.getDistrict(this.html.province.item);
      }
      if (this.student_info.ec_id) {
        this.html.ecs.item = this.html.ecs.list.filter((i) => i.id == this.student_info.ec_id)[0];
      }
      if (this.student_info.cm_id) {
        this.html.cms.item = this.html.cms.list.filter((i) => i.id == this.student_info.cm_id)[0];
      }
      this.init_lms_id = this.student_info.lms_id || 0;
    }
  },
  methods: {
    saveEC(data = null) {
      if (data && typeof data === "object") {
        this.html.ecs.item = data;
        this.student_info.ec_id = data.id;
      } else {
        this.html.ecs.item = "";
        this.student_info.ec_id = "";
      }
    },
    saveCM(data = null) {
      if (data && typeof data === "object") {
        this.html.cms.item = data;
        this.student_info.cm_id = data.id;
      } else {
        this.html.cms.item = "";
        this.student_info.cm_id = "";
      }
    },
    selectDate(date) {
      if (date) {
        this.student_info.date_of_birth = moment(date).format("YYYY-MM-DD");
      }
    },
    selectDateGudBirthDay1(date) {
      if (date) {
        this.student_info.gud_birth_day1 = moment(date).format("YYYY-MM-DD");
      }
    },
    selectDateGudBirthDay2(date) {
      if (date) {
        this.student_info.gud_birth_day2 = moment(date).format("YYYY-MM-DD");
      }
    },
    getDistrict(data = null) {
      if (data && typeof data === "object") {
        const province_id = data.id;
        this.html.province.item = data;
        this.student_info.province_id = province_id;
        this.$vs.loading();
        axios.g(`/api/system/provinces/${province_id}/districts`).then((response) => {
          this.$vs.loading.close();
          this.html.district.list = response.data;
          if (this.tmp_district_id) {
            this.html.district.item = this.html.district.list.filter((item) => item.id == this.tmp_district_id)[0];
          } else {
            this.student_info.district_id = "";
            this.html.district.item = "";
          }
        });
      } else {
        this.html.province.item = "";
        this.student_info.province_id = "";
        this.html.district.list = [];
        this.html.district.item = "";
        this.student_info.district_id = "";
      }
    },
    saveDistrict(data = null) {
      if (data && typeof data === "object") {
        this.html.district.item = data;
        this.student_info.district_id = data.id;
      } else {
        this.html.district.item = "";
        this.student_info.district_id = "";
      }
    },
    saveJob(data = null) {
      if (data && typeof data === "object") {
        this.html.jobs.item = data;
        this.student_info.gud_job1 = data.id;
      } else {
        this.html.jobs.item = "";
        this.student_info.gud_job1 = "";
      }
    },
    saveJob2(data = null) {
      if (data && typeof data === "object") {
        this.html.jobs.item2 = data;
        this.student_info.gud_job2 = data.id;
      } else {
        this.html.jobs.item2 = "";
        this.student_info.gud_job2 = "";
      }
    },
    reloadPage() {
      location.reload();
    },
    saveInfo() {
      let mess = "";
      let resp = true;
      if (this.student_info.gender == "") {
        mess += " - Giới tính không được để trống<br/>";
        resp = false;
      }
      if (this.student_info.name == "") {
        mess += " - Họ tên không được để trống<br/>";
        resp = false;
      }
      if (this.student_info.gud_name1 == "") {
        mess += " - Họ tên phụ huynh 1 không được để trống<br/>";
        resp = false;
      }
      if (
        this.student_info.gud_mobile2 != null &&
        this.student_info.gud_mobile2 != "" &&
        !u.vld.phone(this.student_info.gud_mobile2)
      ) {
        mess += " - Số điện thoại 2 không đúng định dạng<br/>";
        resp = false;
      }
      const bd = u.parseManualBirthday(this.birthday_text);
      if (bd === null) {
        mess += " - Ngày sinh không đúng định dạng (yyyy hoặc dd/mm/yyyy)<br/>";
        resp = false;
      }
      if (!resp) {
        this.alert.color = "danger";
        this.alert.body = mess;
        this.alert.active = true;
        return false;
      }
      this.$vs.loading();
      axios
        .p("/api/lms/students/update", {
          ...this.student_info,
          date_of_birth: bd.date || null,
          birthday_year_only: bd.yearOnly,
        })
        .then((response) => {
          this.$vs.loading.close();
          this.$set(this.student_info, "date_of_birth", bd.date || null);
          this.$set(this.student_info, "birthday_year_only", bd.yearOnly);
          this.$vs.notify({
            title: "Thành Công",
            text: response.data.message,
            color: "success",
            iconPack: "feather",
            icon: "icon-check",
          });
          this.disabled_edit = true;
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
.student-compact-info {
  padding-bottom: 20px;
}

/* Compact Toolbar */
.compact-info-toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 6px 12px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  margin-bottom: 12px;
}

.info-mode-indicator {
  font-size: 13.5px;
  font-weight: 500;
}

.btn-compact-action {
  height: 34px;
  padding: 0 14px;
  font-size: 13.5px;
  font-weight: 600;
  border-radius: 5px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  transition: all 0.2s ease;
  white-space: nowrap;
}

.btn-edit {
  background: #7367f0;
  color: #ffffff;
  border: 1px solid #7367f0;
}

.btn-edit:hover {
  background: #5e50ee;
}

.btn-save {
  background: #28c76f;
  color: #ffffff;
  border: 1px solid #28c76f;
}

.btn-save:hover {
  background: #20a159;
}

.btn-cancel {
  background: #ffffff;
  color: #64748b;
  border: 1px solid #cbd5e1;
}

.btn-cancel:hover {
  background: #f1f5f9;
}

/* Section Dividers */
.compact-section-divider {
  display: flex;
  align-items: center;
  font-size: 14.5px;
  font-weight: 700;
  color: #334155;
  padding: 6px 0;
  margin-bottom: 10px;
  border-bottom: 1px solid #e2e8f0;
  letter-spacing: 0.3px;
}

/* Grids */
.compact-grid-4 {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 10px 14px;
}

.compact-grid-3 {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 10px 14px;
}

.compact-grid-2 {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 10px 14px;
}

@media (max-width: 991px) {
  .compact-grid-4 { grid-template-columns: repeat(2, 1fr); }
  .compact-grid-3 { grid-template-columns: repeat(2, 1fr); }
  .compact-grid-2 { grid-template-columns: 1fr; }
}

@media (max-width: 640px) {
  .compact-grid-4 { grid-template-columns: 1fr; }
  .compact-grid-3 { grid-template-columns: 1fr; }
}

.span-2 { grid-column: span 2; }
.span-3 { grid-column: span 3; }
.span-4 { grid-column: span 4; }

@media (max-width: 991px) {
  .span-2, .span-3, .span-4 { grid-column: span 1; }
}

/* Form Groups */
.form-group {
  display: flex;
  flex-direction: column;
}

.form-label {
  font-size: 14px;
  font-weight: 600;
  color: #334155;
  margin-bottom: 5px;
}

/* Comfortable Inputs - Exactly 38px */
.form-control-compact {
  box-sizing: border-box !important;
  height: 38px;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  padding: 6px 12px;
  font-size: 14px;
  color: #1e293b;
  background: #ffffff;
  outline: none;
  width: 100%;
  transition: all 0.2s ease;
}

.form-control-compact:focus {
  border-color: #7367f0;
  box-shadow: 0 0 0 2px rgba(115, 103, 240, 0.12);
}

.form-control-compact:disabled {
  background: #f8fafc;
  color: #334155;
  border-color: #e2e8f0;
  cursor: default;
}

.form-control-readonly {
  box-sizing: border-box !important;
  height: 38px;
  display: flex;
  align-items: center;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  padding: 6px 12px;
  font-size: 14px;
  color: #334155;
}

.parent-compact-box {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  padding: 12px 14px;
}

.parent-compact-header {
  font-size: 14px;
  font-weight: 700;
  border-bottom: 1px dashed #cbd5e1;
  padding-bottom: 5px;
}

/* Datepicker & VueSelect Overrides for exactly 38px */
.form-datepicker-compact >>> input {
  box-sizing: border-box !important;
  height: 38px !important;
  border: 1px solid #cbd5e1 !important;
  border-radius: 6px !important;
  font-size: 14px !important;
  padding: 6px 12px !important;
}

.form-datepicker-compact >>> input:disabled {
  background: #f8fafc !important;
  border-color: #e2e8f0 !important;
  color: #334155 !important;
}

.form-vselect-compact >>> .vs__dropdown-toggle {
  box-sizing: border-box !important;
  min-height: 38px !important;
  height: 38px !important;
  border: 1px solid #cbd5e1 !important;
  border-radius: 6px !important;
  font-size: 14px !important;
  padding: 0 8px !important;
}

.form-vselect-compact.vs--disabled >>> .vs__dropdown-toggle {
  background: #f8fafc !important;
  border-color: #e2e8f0 !important;
}

.form-vselect-compact >>> .vs__selected {
  margin: 4px 2px 0 !important;
  padding: 0 4px !important;
  font-size: 14px !important;
}

.form-vselect-compact >>> .vs__search {
  margin: 4px 0 0 !important;
  padding: 0 !important;
  font-size: 14px !important;
}
</style>
