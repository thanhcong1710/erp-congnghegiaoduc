<template>
  <div class="student-detail-info">
    <!-- Form Edit / View Status Banner -->
    <div v-if="!disabled_edit" class="edit-mode-alert mb-5 p-4 rounded-lg bg-amber-50 border border-amber-200 flex items-center justify-between">
      <div class="flex items-center text-amber-800">
        <i class="fa-solid fa-triangle-exclamation text-xl mr-3 text-amber-500"></i>
        <div>
          <strong class="text-sm">Chế độ chỉnh sửa thông tin đang bật</strong>
          <p class="text-xs text-amber-700 m-0">Vui lòng cập nhật thông tin và bấm <strong>"Lưu thay đổi"</strong> để hoàn tất.</p>
        </div>
      </div>
      <div class="flex items-center gap-2">
        <vs-button color="dark" type="border" size="small" class="font-semibold" @click="reloadPage">
          <i class="fa-solid fa-xmark mr-1"></i> Hủy
        </vs-button>
        <vs-button color="success" size="small" class="font-semibold shadow" @click="saveInfo">
          <i class="fa-solid fa-floppy-disk mr-1"></i> Lưu thay đổi
        </vs-button>
      </div>
    </div>

    <!-- Alert Box -->
    <vs-alert :active.sync="alert.active" class="mb-5" :color="alert.color" closable icon-pack="feather" close-icon="icon-x">
      <div v-html="alert.body"></div>
    </vs-alert>

    <!-- SECTION 1: THÔNG TIN HỌC SINH -->
    <div class="info-section-card mb-5">
      <div class="section-card-header">
        <div class="section-card-title">
          <span class="section-icon-wrapper bg-blue-50 text-primary">
            <i class="fa-solid fa-graduation-cap"></i>
          </span>
          <div>
            <h5 class="m-0 font-bold text-gray-800">Thông tin học sinh</h5>
            <span class="text-xs text-gray-500">Thông tin cá nhân, trường lớp và địa chỉ liên lạc của học sinh</span>
          </div>
        </div>
        <div v-if="disabled_edit">
          <vs-button size="small" color="primary" type="border" class="font-semibold" @click="disabled_edit=false">
            <i class="fa-solid fa-pen-to-square mr-1"></i> Sửa thông tin
          </vs-button>
        </div>
      </div>

      <div class="section-card-body">
        <div class="vx-row">
          <!-- Mã Học sinh -->
          <div class="vx-col md:w-1/4 w-full mb-4">
            <label class="form-item-label"><i class="fa-solid fa-id-badge text-gray-400 mr-1"></i> Mã học sinh</label>
            <div class="form-readonly-badge">
              <span class="font-bold text-primary">{{ student_info.lms_code || '---' }}</span>
              <span class="text-2xs text-gray-400 uppercase tracking-wider ml-auto">Cố định</span>
            </div>
          </div>

          <!-- Tên học sinh -->
          <div class="vx-col md:w-2/4 w-full mb-4">
            <label class="form-item-label">
              <i class="fa-solid fa-user text-gray-400 mr-1"></i> Họ và tên học sinh <span class="text-danger font-bold">*</span>
            </label>
            <input
              class="vs-inputx vs-input--input normal modern-input"
              type="text"
              name="student_name"
              placeholder="Nhập họ và tên học sinh"
              v-model="student_info.name"
              :disabled="disabled_edit"
            />
          </div>

          <!-- Giới tính -->
          <div class="vx-col md:w-1/4 w-full mb-4">
            <label class="form-item-label">
              <i class="fa-solid fa-venus-mars text-gray-400 mr-1"></i> Giới tính <span class="text-danger font-bold">*</span>
            </label>
            <select class="vs-inputx vs-input--input normal modern-input" v-model="student_info.gender" :disabled="disabled_edit">
              <option value="M">👦 Nam</option>
              <option value="F">👧 Nữ</option>
            </select>
          </div>

          <!-- Ngày sinh -->
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label class="form-item-label">
              <i class="fa-regular fa-calendar-days text-gray-400 mr-1"></i> Ngày sinh <span class="text-danger font-bold">*</span>
            </label>
            <datepicker
              class="w-full modern-datepicker"
              v-model="student_info.date_of_birth"
              placeholder="Chọn ngày sinh"
              :lang="datepickerOptions.lang"
              @change="selectDate"
              :disabled="disabled_edit"
            />
          </div>

          <!-- Trường học -->
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label class="form-item-label"><i class="fa-solid fa-school text-gray-400 mr-1"></i> Trường học</label>
            <input
              class="vs-inputx vs-input--input normal modern-input"
              type="text"
              placeholder="Trường đang theo học"
              v-model="student_info.school"
              :disabled="disabled_edit"
            />
          </div>

          <!-- School Grade -->
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label class="form-item-label"><i class="fa-solid fa-chalkboard-user text-gray-400 mr-1"></i> Khối lớp</label>
            <select class="vs-inputx vs-input--input normal modern-input" v-model="student_info.school_grade" :disabled="disabled_edit">
              <option value="" class="ng-binding">-- Chọn khối lớp --</option>
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
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label class="form-item-label"><i class="fa-solid fa-map-location-dot text-gray-400 mr-1"></i> Tỉnh / Thành phố</label>
            <vue-select
              naem="input_province"
              label="name"
              placeholder="Chọn Tỉnh/Thành Phố"
              :options="html.province.list"
              v-model="html.province.item"
              :searchable="true"
              language="tv-VN"
              @input="getDistrict"
              :disabled="disabled_edit"
              class="modern-vue-select"
            ></vue-select>
          </div>

          <!-- Quận / Huyện -->
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label class="form-item-label"><i class="fa-solid fa-map-pin text-gray-400 mr-1"></i> Quận / Huyện</label>
            <vue-select
              naem="input_district"
              label="name"
              placeholder="Chọn Quận/Huyện/Thị Xã"
              :options="html.district.list"
              v-model="html.district.item"
              :searchable="true"
              language="tv-VN"
              @input="saveDistrict"
              :disabled="disabled_edit"
              class="modern-vue-select"
            ></vue-select>
          </div>

          <!-- Địa chỉ chi tiết -->
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label class="form-item-label"><i class="fa-solid fa-house text-gray-400 mr-1"></i> Địa chỉ chi tiết</label>
            <input
              class="vs-inputx vs-input--input normal modern-input"
              type="text"
              placeholder="Số nhà, đường phố, phường xã"
              v-model="student_info.address"
              :disabled="disabled_edit"
            />
          </div>

          <!-- Nguồn -->
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label class="form-item-label"><i class="fa-solid fa-bullhorn text-gray-400 mr-1"></i> Nguồn khách hàng</label>
            <div class="form-readonly-badge">
              <span class="text-gray-800">{{ student_info.source_name || 'Chưa xác định' }}</span>
            </div>
          </div>

          <!-- Trạng thái -->
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label class="form-item-label"><i class="fa-solid fa-circle-check text-gray-400 mr-1"></i> Trạng thái</label>
            <div class="form-readonly-badge">
              <span class="font-semibold text-primary">{{ student_info.status_label || 'Chưa xác định' }}</span>
            </div>
          </div>

          <!-- Ghi chú -->
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label class="form-item-label"><i class="fa-regular fa-note-sticky text-gray-400 mr-1"></i> Ghi chú học sinh</label>
            <textarea
              class="vs-inputx vs-input--input normal modern-input modern-textarea"
              placeholder="Nhập ghi chú quan trọng về học sinh..."
              rows="1"
              v-model="student_info.note"
              :disabled="disabled_edit"
            ></textarea>
          </div>
        </div>
      </div>
    </div>

    <!-- SECTION 2: THÔNG TIN PHỤ HUYNH -->
    <div class="info-section-card mb-5">
      <div class="section-card-header">
        <div class="section-card-title">
          <span class="section-icon-wrapper bg-emerald-50 text-emerald-600">
            <i class="fa-solid fa-people-roof"></i>
          </span>
          <div>
            <h5 class="m-0 font-bold text-gray-800">Thông tin phụ huynh & Người giám hộ</h5>
            <span class="text-xs text-gray-500">Thông tin liên hệ, nghề nghiệp và nguyện vọng của phụ huynh</span>
          </div>
        </div>
      </div>

      <div class="section-card-body">
        <div class="vx-row">
          <!-- PHỤ HUYNH 1 (CHÍNH) -->
          <div class="vx-col lg:w-1/2 w-full mb-4">
            <div class="parent-subcard parent-primary-subcard">
              <div class="parent-subcard-header">
                <span class="parent-badge-pill bg-emerald-100 text-emerald-800">
                  <i class="fa-solid fa-user-tie mr-1"></i> Phụ huynh 1 (Người liên hệ chính)
                </span>
              </div>
              <div class="vx-row mt-3">
                <div class="vx-col md:w-1/2 w-full mb-3">
                  <label class="form-item-label">Họ và tên <span class="text-danger font-bold">*</span></label>
                  <input
                    class="vs-inputx vs-input--input normal modern-input"
                    type="text"
                    placeholder="Họ tên phụ huynh 1"
                    v-model="student_info.gud_name1"
                    :disabled="disabled_edit"
                  />
                </div>
                <div class="vx-col md:w-1/2 w-full mb-3">
                  <label class="form-item-label">Số điện thoại <span class="text-danger font-bold">*</span></label>
                  <div class="form-readonly-badge" v-if="disabled_edit">
                    <a v-if="student_info.gud_mobile1" :href="'tel:' + student_info.gud_mobile1" class="text-primary font-bold hover:underline">
                      <i class="fa-solid fa-phone mr-1 text-emerald-500"></i> {{ student_info.gud_mobile1 }}
                    </a>
                    <span v-else class="text-gray-400">Chưa có SĐT</span>
                  </div>
                  <input
                    v-else
                    class="vs-inputx vs-input--input normal modern-input"
                    type="text"
                    v-model="student_info.gud_mobile1"
                    disabled="true"
                    title="Số điện thoại chính được quản lý theo hồ sơ khách hàng"
                  />
                </div>
                <div class="vx-col md:w-1/2 w-full mb-3">
                  <label class="form-item-label">Email</label>
                  <input
                    class="vs-inputx vs-input--input normal modern-input"
                    type="email"
                    placeholder="example@gmail.com"
                    v-model="student_info.gud_email1"
                    :disabled="disabled_edit"
                  />
                </div>
                <div class="vx-col md:w-1/2 w-full mb-3">
                  <label class="form-item-label">Ngày sinh</label>
                  <datepicker
                    class="w-full modern-datepicker"
                    v-model="student_info.gud_birth_day1"
                    placeholder="Chọn ngày sinh"
                    :lang="datepickerOptions.lang"
                    @change="selectDateGudBirthDay1"
                    :disabled="disabled_edit"
                  />
                </div>
                <div class="vx-col w-full mb-2">
                  <label class="form-item-label">Nghề nghiệp</label>
                  <vue-select
                    label="title"
                    placeholder="Chọn nghề nghiệp"
                    :options="html.jobs.list"
                    v-model="html.jobs.item"
                    :searchable="true"
                    language="tv-VN"
                    @input="saveJob"
                    :disabled="disabled_edit"
                    class="modern-vue-select"
                  ></vue-select>
                </div>
              </div>
            </div>
          </div>

          <!-- PHỤ HUYNH 2 (PHỤ) -->
          <div class="vx-col lg:w-1/2 w-full mb-4">
            <div class="parent-subcard">
              <div class="parent-subcard-header">
                <span class="parent-badge-pill bg-slate-100 text-slate-700">
                  <i class="fa-solid fa-user-group mr-1"></i> Phụ huynh 2 (Người liên hệ phụ)
                </span>
              </div>
              <div class="vx-row mt-3">
                <div class="vx-col md:w-1/2 w-full mb-3">
                  <label class="form-item-label">Họ và tên</label>
                  <input
                    class="vs-inputx vs-input--input normal modern-input"
                    type="text"
                    placeholder="Họ tên phụ huynh 2"
                    v-model="student_info.gud_name2"
                    :disabled="disabled_edit"
                  />
                </div>
                <div class="vx-col md:w-1/2 w-full mb-3">
                  <label class="form-item-label">Số điện thoại 2</label>
                  <input
                    class="vs-inputx vs-input--input normal modern-input"
                    type="text"
                    placeholder="Số điện thoại phụ"
                    v-model="student_info.gud_mobile2"
                    :disabled="disabled_edit"
                  />
                </div>
                <div class="vx-col md:w-1/2 w-full mb-3">
                  <label class="form-item-label">Email 2</label>
                  <input
                    class="vs-inputx vs-input--input normal modern-input"
                    type="email"
                    placeholder="example@gmail.com"
                    v-model="student_info.gud_email2"
                    :disabled="disabled_edit"
                  />
                </div>
                <div class="vx-col md:w-1/2 w-full mb-3">
                  <label class="form-item-label">Ngày sinh</label>
                  <datepicker
                    class="w-full modern-datepicker"
                    v-model="student_info.gud_birth_day2"
                    placeholder="Chọn ngày sinh"
                    :lang="datepickerOptions.lang"
                    @change="selectDateGudBirthDay2"
                    :disabled="disabled_edit"
                  />
                </div>
                <div class="vx-col w-full mb-2">
                  <label class="form-item-label">Nghề nghiệp</label>
                  <vue-select
                    label="title"
                    placeholder="Chọn nghề nghiệp"
                    :options="html.jobs.list"
                    v-model="html.jobs.item2"
                    :searchable="true"
                    language="tv-VN"
                    @input="saveJob2"
                    :disabled="disabled_edit"
                    class="modern-vue-select"
                  ></vue-select>
                </div>
              </div>
            </div>
          </div>

          <!-- Nguyện vọng của phụ huynh -->
          <div class="vx-col w-full mb-2">
            <label class="form-item-label"><i class="fa-solid fa-heart text-rose-400 mr-1"></i> Nguyện vọng & Mong muốn của phụ huynh</label>
            <textarea
              class="vs-inputx vs-input--input normal modern-input modern-textarea"
              rows="2"
              placeholder="Ghi nhận mong muốn, định hướng học tập từ phía phụ huynh..."
              v-model="student_info.aspiration"
              :disabled="disabled_edit"
            ></textarea>
          </div>
        </div>
      </div>
    </div>

    <!-- SECTION 3: TRUNG TÂM & NHÂN SỰ QUẢN LÝ -->
    <div class="info-section-card mb-5">
      <div class="section-card-header">
        <div class="section-card-title">
          <span class="section-icon-wrapper bg-amber-50 text-amber-600">
            <i class="fa-solid fa-building-user"></i>
          </span>
          <div>
            <h5 class="m-0 font-bold text-gray-800">Trung tâm & Nhân sự phụ trách</h5>
            <span class="text-xs text-gray-500">Cơ sở học tập và nhân sự phụ trách tư vấn, vận hành lớp</span>
          </div>
        </div>
      </div>

      <div class="section-card-body">
        <div class="vx-row">
          <!-- Trung tâm -->
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label class="form-item-label"><i class="fa-solid fa-building text-gray-400 mr-1"></i> Trung tâm phụ trách</label>
            <div class="form-readonly-badge">
              <i class="fa-solid fa-location-dot text-primary mr-2"></i>
              <strong class="text-gray-800">{{ student_info.branch_name || 'Chưa gắn trung tâm' }}</strong>
            </div>
          </div>

          <!-- EC -->
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label class="form-item-label"><i class="fa-solid fa-user-tag text-gray-400 mr-1"></i> Chuyên viên tư vấn (EC)</label>
            <vue-select
              label="label"
              placeholder="Chọn chuyên viên EC"
              :options="html.ecs.list"
              v-model="html.ecs.item"
              :searchable="true"
              language="tv-VN"
              @input="saveEC"
              :disabled="disabled_edit"
              class="modern-vue-select"
            ></vue-select>
          </div>

          <!-- CM -->
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label class="form-item-label"><i class="fa-solid fa-user-gear text-gray-400 mr-1"></i> Vận hành lớp (CM)</label>
            <vue-select
              label="label"
              placeholder="Chọn chuyên viên CM"
              :options="html.cms.list"
              v-model="html.cms.item"
              :searchable="true"
              language="tv-VN"
              @input="saveCM"
              :disabled="disabled_edit"
              class="modern-vue-select"
            ></vue-select>
          </div>
        </div>
      </div>
    </div>

    <!-- BOTTOM ACTION TOOLBAR -->
    <div class="form-action-footer">
      <div v-if="disabled_edit" class="flex items-center justify-between w-full">
        <div class="text-xs text-gray-500 flex items-center">
          <i class="fa-solid fa-lock text-gray-400 mr-2"></i>
          Dữ liệu đang ở chế độ xem an toàn. Bấm <strong>"Cập nhật thông tin"</strong> để kích hoạt chế độ sửa.
        </div>
        <vs-button color="primary" class="font-semibold shadow px-5 py-2.5" @click="disabled_edit=false">
          <i class="fa-solid fa-pen-to-square mr-2"></i> Cập nhật thông tin
        </vs-button>
      </div>

      <div v-else class="flex items-center justify-between w-full">
        <div class="text-xs text-amber-700 flex items-center font-medium">
          <i class="fa-solid fa-circle-info text-amber-500 mr-2"></i>
          Đang sửa hồ sơ. Hãy kiểm tra kỹ thông tin trước khi nhấn Lưu.
        </div>
        <div class="flex items-center gap-3">
          <vs-button color="dark" type="border" class="font-semibold px-4" @click="reloadPage">
            <i class="fa-solid fa-xmark mr-1.5"></i> Hủy thao tác
          </vs-button>
          <vs-button color="success" class="font-semibold shadow-md px-5" @click="saveInfo">
            <i class="fa-solid fa-floppy-disk mr-1.5"></i> Lưu thay đổi
          </vs-button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import axios from '../../../../http/axios.js'
  import moment from 'moment';
  import u from '../../../../until/helper.js'
  import datepicker from "vue2-datepicker";
  import select from 'vue-select'

  export default {
    components: {
      "vue-select": select,
      moment,
      datepicker
    },
    props: {
      student_info: {
        type: Object,
        default: () => {}
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
              "Tháng 1",
              "Tháng 2",
              "Tháng 3",
              "Tháng 4",
              "Tháng 5",
              "Tháng 6",
              "Tháng 7",
              "Tháng 8",
              "Tháng 9",
              "Tháng 10",
              "Tháng 11",
              "Tháng 12"
            ]
          }
        },
        alert:{
          active: false,
          body: '',
          color:'',
        },
        disabled_edit: true,
        html:{
          province: {
            item: '',
            list: []
          },
          district: {
            item: '',
            list: []
          },
          jobs: {
            item2: '',
            item: '',
            list: []
          },
          ecs: {
            item: '',
            list: []
          },
          cms: {
            item: '',
            list: []
          },
        },
        tmp_district_id:'',
        init_lms_id: 0,
      }
    },
    async created() {
      await axios.g(`/api/system/provinces`)
        .then(response => {
        this.html.province.list = response.data
      })
      await axios.g(`/api/system/jobs`)
        .then(response => {
        this.html.jobs.list = response.data
      })
      await axios.g(`/api/system/cms/${this.student_info.branch_id}`)
        .then(response => {
        this.html.cms.list = response.data
      })
      await axios.g(`/api/system/ecs/${this.student_info.branch_id}`)
        .then(response => {
        this.html.ecs.list = response.data
      })
      this.html.jobs.item = this.html.jobs.list.filter(item => item.id == this.student_info.gud_job1)[0]
      this.html.jobs.item2 = this.html.jobs.list.filter(item => item.id == this.student_info.gud_job2)[0]
      this.html.province.item = this.html.province.list.filter(item => item.id == this.student_info.province_id)[0]
      this.tmp_district_id = this.student_info.district_id
      this.getDistrict(this.html.province.item);
      this.html.ecs.item = this.html.ecs.list.filter(item => item.id == this.student_info.ec_id)[0]
      this.html.cms.item = this.html.cms.list.filter(item => item.id == this.student_info.cm_id)[0]
      this.init_lms_id = this.student_info.lms_id;
    },
    methods: {
      saveEC(data = null){
        if (data && typeof data === 'object') {
          const ec_id = data.id
          this.html.ecs.item = data
          this.student_info.ec_id = ec_id
        }else{
          this.html.ecs.item = ""
          this.student_info.ec_id = ""
        }
      },
      saveCM(data = null){
        if (data && typeof data === 'object') {
          const cm_id = data.id
          this.html.cms.item = data
          this.student_info.cm_id = cm_id
        }else{
          this.html.cms.item = ""
          this.student_info.cm_id = ""
        }
      },
      selectDate(date) {
        if (date) {
          this.student_info.date_of_birth = moment(date).format("YYYY-MM-DD");
        }
      },
      selectDateGudBirthDay1(date){
        if (date) {
          this.student_info.gud_birth_day1 = moment(date).format("YYYY-MM-DD");
        }
      },
      selectDateGudBirthDay2(date){
        if (date) {
          this.student_info.gud_birth_day2 = moment(date).format("YYYY-MM-DD");
        }
      },
      getDistrict(data = null){
        if (data && typeof data === 'object') {
          const province_id = data.id
          this.html.province.item = data
          this.student_info.province_id = province_id
          this.$vs.loading()
          axios.g(`/api/system/provinces/${province_id}/districts`).then(response => {
            this.$vs.loading.close();
            this.html.district.list = response.data
            if(this.tmp_district_id){
              this.html.district.item = this.html.district.list.filter(item => item.id == this.tmp_district_id)[0]
            }else{
              this.student_info.district_id = ""
              this.html.district.item = ""
            } 
          })
        }else{
          this.html.province.item = ""
          this.student_info.province_id = ""
          this.html.district.list = []
          this.html.district.item = ""
          this.student_info.district_id = ""
        }
      },
      saveDistrict(data = null){
        if (data && typeof data === 'object') {
          const district_id = data.id
          this.html.district.item = data
          this.student_info.district_id = district_id
        }else{
          this.html.district.item = ""
          this.student_info.district_id = ""
        }
      },
      saveJob(data = null){
        if (data && typeof data === 'object') {
          const job_id = data.id
          this.html.jobs.item = data
          this.student_info.gud_job1 = job_id
        }else{
          this.html.jobs.item = ""
          this.student_info.gud_job1 = ""
        }
      },
      saveJob2(data = null){
        if (data && typeof data === 'object') {
          const job_id = data.id
          this.html.jobs.item2 = data
          this.student_info.gud_job2 = job_id
        }else{
          this.html.jobs.item2 = ""
          this.student_info.gud_job2 = ""
        }
      },
      reloadPage(){
        location.reload();
      },
      saveInfo(){
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
        if (this.student_info.gud_mobile2 != null && this.student_info.gud_mobile2 != "" && !u.vld.phone(this.student_info.gud_mobile2)) {
          mess += " - Số điện thoại 2 không đúng định dạng<br/>";
          resp = false;
        }
        if (!resp) {
          this.alert.color = 'danger'
          this.alert.body = mess;
          this.alert.active = true;
          return false;
        }
        this.$vs.loading()
        axios.p("/api/lms/students/update",this.student_info)
        .then((response) => {
          this.$vs.loading.close();;
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          this.disabled_edit = true
        })
        .catch((e) => {
          console.log(e);
          this.$vs.loading.close();
        });
      },
      validateIdLMS(){
        const data = {
          student_id: this.student_info.id,
          lms_id: this.student_info.lms_id,
        };
        this.$vs.loading()
        axios.p(`/api/lms/students/validate_lms`,data).then(response => {
          this.$vs.loading.close();
          if(response.data.status==0){
            this.$vs.notify({
              title: 'Thông Báo',
              text: response.data.message,
              iconPack: 'feather',
              icon: 'icon-alert-circle',
              color: 'danger'
            })
            this.student_info.lms_id = 0
          }
        })
      },
      disableLMS(disabled_edit, init_lms_id) {
        if (disabled_edit) {
          return true;
        } else {
          return init_lms_id != 0;
        }
      }
    }
  }
</script>

<style scoped>
.info-section-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

.section-card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 20px;
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
}

.section-card-title {
  display: flex;
  align-items: center;
  gap: 12px;
}

.section-icon-wrapper {
  width: 38px;
  height: 38px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  font-size: 16px;
}

.section-card-body {
  padding: 20px;
}

.form-item-label {
  display: block;
  font-size: 12px;
  font-weight: 600;
  color: #475569;
  margin-bottom: 6px;
}

.modern-input {
  border: 1px solid #cbd5e1 !important;
  border-radius: 7px !important;
  padding: 8px 12px !important;
  font-size: 13px !important;
  width: 100% !important;
  transition: all 0.2s ease !important;
}

.modern-input:focus {
  border-color: #7367f0 !important;
  box-shadow: 0 0 0 3px rgba(115, 103, 240, 0.12) !important;
}

.modern-input:disabled {
  background-color: #f8fafc !important;
  color: #334155 !important;
  border-color: #e2e8f0 !important;
  opacity: 1 !important;
  cursor: not-allowed;
}

.modern-textarea {
  min-height: 40px;
  resize: vertical;
}

.form-readonly-badge {
  display: flex;
  align-items: center;
  padding: 8px 12px;
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 7px;
  min-height: 38px;
  font-size: 13px;
}

.parent-subcard {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 16px;
}

.parent-primary-subcard {
  border-color: #cbd5e1;
  background: #fafbfd;
}

.parent-subcard-header {
  margin-bottom: 8px;
}

.parent-badge-pill {
  display: inline-flex;
  align-items: center;
  font-size: 12px;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 6px;
}

.form-action-footer {
  position: sticky;
  bottom: 0;
  z-index: 20;
  display: flex;
  align-items: center;
  padding: 14px 20px;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.05);
  margin-top: 20px;
  margin-bottom: 20px;
}

.modern-datepicker >>> input {
  border: 1px solid #cbd5e1 !important;
  border-radius: 7px !important;
  height: 38px !important;
  font-size: 13px !important;
}

.modern-datepicker >>> input:disabled {
  background-color: #f8fafc !important;
  color: #334155 !important;
  border-color: #e2e8f0 !important;
  opacity: 1 !important;
  cursor: not-allowed;
}

.modern-vue-select >>> .vs__dropdown-toggle {
  border: 1px solid #cbd5e1 !important;
  border-radius: 7px !important;
  min-height: 38px !important;
  font-size: 13px !important;
}

.modern-vue-select.vs--disabled >>> .vs__dropdown-toggle {
  background-color: #f8fafc !important;
  border-color: #e2e8f0 !important;
}
</style>
