<template>
  <div>
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center">
        <vs-button color="dark" type="border" @click="$router.push('/hrm/employees')" class="p-3 mr-4">
          <i class="fa-solid fa-arrow-left mr-2"></i> Quay lại
        </vs-button>
        <h3 class="font-bold text-gray-800">{{ isEdit ? 'Cập Nhật Hồ Sơ Nhân Viên' : 'Thêm Mới Hồ Sơ Nhân Viên' }}</h3>
      </div>
    </div>

    <vx-card no-shadow>
      <vs-tabs>
        <!-- Tab 1: Thông tin chung -->
        <vs-tab label="Thông tin chung" icon-pack="feather" icon="icon-user">
          <div class="mt-4">
            <h5 class="mb-3 font-bold text-gray-800 border-b pb-2 flex items-center">
              <feather-icon icon="UserIcon" class="w-4 h-4 mr-2 text-primary" /> Thông tin cơ bản & Tài khoản
            </h5>

            <div class="vx-row mb-4">
              <div class="vx-col w-1/2">
                <label class="text-sm font-semibold text-gray-700 block mb-1">Họ và tên nhân sự (*)</label>
                <input type="text" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.user_name" placeholder="Nguyễn Văn A" />
              </div>
              <div class="vx-col w-1/2">
                <label class="text-sm font-semibold text-gray-700 block mb-1">Email đăng nhập / liên hệ</label>
                <input type="email" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.email" placeholder="Không bắt buộc (để trống tự sinh theo mã NV)" />
              </div>
            </div>

            <div class="vx-row mb-4">
              <div class="vx-col w-1/2">
                <label class="text-sm font-semibold text-gray-700 block mb-1">Mã nhân viên (Để trống tự sinh CHAMxx)</label>
                <input type="text" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.employee_code" placeholder="CHAM01" :disabled="isEdit" />
              </div>
              <div class="vx-col w-1/2">
                <label class="text-sm font-semibold text-gray-700 block mb-1">Ngày vào làm việc chính thức</label>
                <input type="date" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.start_date" />
              </div>
            </div>

            <div class="vx-row mb-4">
              <div class="vx-col w-1/2">
                <label class="text-sm font-semibold text-gray-700 block mb-1">Ngày tháng năm sinh</label>
                <input type="date" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.dob" />
              </div>
              <div class="vx-col w-1/2">
                <label class="text-sm font-semibold text-gray-700 block mb-1">Giới tính</label>
                <select class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.gender">
                  <option :value="1">Nam</option>
                  <option :value="2">Nữ</option>
                  <option :value="3">Khác</option>
                </select>
              </div>
            </div>

            <div class="vx-row mb-4">
              <div class="vx-col w-1/2">
                <label class="text-sm font-semibold text-gray-700 block mb-1">CCCD / CMND / Passport</label>
                <input type="text" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.id_card_number" placeholder="Số CCCD/CMND..." />
              </div>
              <div class="vx-col w-1/2">
                <label class="text-sm font-semibold text-gray-700 block mb-1">Ngày cấp CCCD / CMND</label>
                <input type="date" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.id_card_date" />
              </div>
            </div>

            <div class="vx-row mb-4">
              <div class="vx-col w-1/2">
                <label class="text-sm font-semibold text-gray-700 block mb-1">Số điện thoại</label>
                <input type="text" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.phone" placeholder="0987xxxxxx" />
              </div>
              <div class="vx-col w-1/2">
                <label class="text-sm font-semibold text-gray-700 block mb-1">Địa chỉ thường trú</label>
                <input type="text" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.address" placeholder="Địa chỉ nơi ở hiện tại..." />
              </div>
            </div>

            <h5 class="mb-3 mt-6 font-bold text-gray-800 border-b pb-2 flex items-center">
              <feather-icon icon="BriefcaseIcon" class="w-4 h-4 mr-2 text-primary" /> Phân loại & Vị trí công việc
            </h5>

            <div class="vx-row mb-4">
              <div class="vx-col w-1/2">
                <label class="text-sm font-semibold text-gray-700 block mb-1">Phân loại nhân sự (*)</label>
                <select class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.employee_type">
                  <option value="fulltime">Nhân sự Fulltime</option>
                  <option value="ctv">Cộng tác viên (CTV)</option>
                  <option value="probation">Thử việc</option>
                </select>
              </div>
              <div class="vx-col w-1/2">
                <label class="text-sm font-semibold text-gray-700 block mb-1">Trạng thái hoạt động</label>
                <select class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.work_status">
                  <option value="working">Đang làm việc</option>
                  <option value="resigned">Đã nghỉ việc</option>
                </select>
              </div>
            </div>

            <!-- Nếu là nhân sự thử việc -->
            <div class="vx-row mb-4 p-3 bg-gray-50 border border-gray-200 rounded" v-if="form.employee_type === 'probation'">
              <div class="vx-col w-1/2">
                <label class="text-sm font-semibold text-gray-700 block mb-1">Ngày bắt đầu thử việc (*)</label>
                <input type="date" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.probation_start_date" />
              </div>
              <div class="vx-col w-1/2">
                <label class="text-sm font-semibold text-gray-700 block mb-1">Ngày kết thúc thử việc (*)</label>
                <input type="date" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.probation_end_date" />
              </div>
            </div>

            <!-- Nếu đã nghỉ việc -->
            <div class="vx-row mb-4 p-3 bg-red-50 border border-red-200 rounded" v-if="form.work_status === 'resigned'">
              <div class="vx-col w-1/2">
                <label class="text-sm font-semibold text-gray-700 block mb-1">Ngày nghỉ việc</label>
                <input type="date" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.resigned_date" />
              </div>
              <div class="vx-col w-1/2">
                <label class="text-sm font-semibold text-gray-700 block mb-1">Lý do nghỉ việc</label>
                <input type="text" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.resigned_reason" placeholder="Lý do nghỉ việc..." />
              </div>
            </div>

            <div class="vx-row mb-4">
              <div class="vx-col w-1/2">
                <label class="text-sm font-semibold text-gray-700 block mb-1">Phòng ban / Team</label>
                <v-select class="w-full" v-model="form.department_id" :options="departments" :reduce="item => item.id" label="displayName" placeholder="Chọn phòng ban / team..." />
              </div>
              <div class="vx-col w-1/2">
                <label class="text-sm font-semibold text-gray-700 block mb-1">Chức danh</label>
                <v-select class="w-full" v-model="form.job_title_id" :options="jobTitles" :reduce="item => item.id" label="name" placeholder="Chọn chức danh..." />
              </div>
            </div>

            <div class="vx-row mb-4">
              <div class="vx-col w-1/2">
                <label class="text-sm font-semibold text-gray-700 block mb-1">Cấp bậc năng lực</label>
                <v-select class="w-full" v-model="form.job_level_id" :options="jobLevels" :reduce="item => item.id" label="name" placeholder="Chọn cấp bậc..." />
              </div>
              <div class="vx-col w-1/2">
                <label class="text-sm font-semibold text-gray-700 block mb-1">Lương cơ bản (VNĐ)</label>
                <input type="number" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.base_salary" placeholder="0" />
              </div>
            </div>

            <h5 class="mb-3 mt-6 font-bold text-gray-800 border-b pb-2 flex items-center">
              <feather-icon icon="CreditCardIcon" class="w-4 h-4 mr-2 text-primary" /> Thông tin tài khoản ngân hàng
            </h5>
            <div class="vx-row mb-4">
              <div class="vx-col w-1/2">
                <label class="text-sm font-semibold text-gray-700 block mb-1">Số tài khoản ngân hàng</label>
                <input type="text" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.bank_account" placeholder="Số tài khoản..." />
              </div>
              <div class="vx-col w-1/2">
                <label class="text-sm font-semibold text-gray-700 block mb-1">Ngân hàng (Chi nhánh)</label>
                <input type="text" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.bank_name" placeholder="Ví dụ: Vietcombank, Techcombank..." />
              </div>
            </div>

            <h5 class="mb-3 mt-6 font-bold text-gray-800 border-b pb-2 flex items-center">
              <feather-icon icon="PhoneCallIcon" class="w-4 h-4 mr-2 text-primary" /> Liên hệ khẩn cấp
            </h5>
            <div class="vx-row mb-4">
              <div class="vx-col w-1/3">
                <label class="text-sm font-semibold text-gray-700 block mb-1">Họ tên người liên hệ</label>
                <input type="text" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.emergency_contact_name" />
              </div>
              <div class="vx-col w-1/3">
                <label class="text-sm font-semibold text-gray-700 block mb-1">Số điện thoại</label>
                <input type="text" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.emergency_contact_phone" />
              </div>
              <div class="vx-col w-1/3">
                <label class="text-sm font-semibold text-gray-700 block mb-1">Quan hệ</label>
                <input type="text" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.emergency_contact_relation" />
              </div>
            </div>

            <div class="vx-row mt-6 pt-3 border-t border-gray-200">
              <div class="vx-col w-full flex justify-end">
                <vs-button @click="$router.push('/hrm/employees')" color="dark" type="border" class="mr-2 p-3">
                  <i class="fa fa-times mr-1"></i> Hủy bỏ
                </vs-button>
                <vs-button @click="saveData()" color="primary" type="filled" class="p-3">
                  <i class="fa-solid fa-floppy-disk mr-2"></i> {{ isEdit ? 'Lưu Thay Đổi' : 'Tạo Mới Nhân Sự' }}
                </vs-button>
              </div>
            </div>
          </div>
        </vs-tab>

        <!-- Tab 2: Hợp đồng -->
        <vs-tab label="Hợp đồng" icon-pack="feather" icon="icon-file-text" :disabled="!isEdit">
          <div class="mt-4">
            <div class="flex justify-end mb-4">
              <vs-button color="success" type="border" class="p-3" @click="showContractModal = true"><i class="fa-solid fa-plus mr-2"></i> Thêm Hợp Đồng</vs-button>
            </div>
            <div class="vs-component vs-con-table stripe vs-table-primary">
              <div class="con-tablex vs-table--content">
                <div class="vs-con-tbody vs-table--tbody">
                  <table class="vs-table vs-table--tbody-table">
                    <thead class="vs-table--thead">
                      <tr>
                        <th>Số hợp đồng</th>
                        <th>Loại hợp đồng</th>
                        <th>Từ ngày</th>
                        <th>Đến ngày</th>
                        <th>Lương cơ bản</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr class="tr-values vs-table--tr" v-for="(tr, index) in contracts" :key="index">
                        <td class="td vs-table--td font-bold text-primary">{{ tr.contract_number }}</td>
                        <td class="td vs-table--td">{{ tr.contract_type }}</td>
                        <td class="td vs-table--td">{{ tr.start_date }}</td>
                        <td class="td vs-table--td">{{ tr.end_date || 'Không thời hạn' }}</td>
                        <td class="td vs-table--td font-semibold">{{ tr.basic_salary | formatCurrency }}</td>
                      </tr>
                      <tr v-if="!contracts.length">
                        <td colspan="5" class="text-center p-4 text-gray-500">Chưa có hợp đồng nào</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </vs-tab>

        <!-- Tab 3: Lịch sử công tác -->
        <vs-tab label="Lịch sử công tác" icon-pack="feather" icon="icon-activity" :disabled="!isEdit">
          <div class="mt-4">
            <div class="vs-component vs-con-table stripe vs-table-primary">
              <div class="con-tablex vs-table--content">
                <div class="vs-con-tbody vs-table--tbody">
                  <table class="vs-table vs-table--tbody-table">
                    <thead class="vs-table--thead">
                      <tr>
                        <th>Ngày hiệu lực</th>
                        <th>Loại điều chuyển</th>
                        <th>Ghi chú</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr class="tr-values vs-table--tr" v-for="(tr, index) in history" :key="index">
                        <td class="td vs-table--td">{{ tr.effective_date }}</td>
                        <td class="td vs-table--td font-semibold">{{ tr.type }}</td>
                        <td class="td vs-table--td">{{ tr.note }}</td>
                      </tr>
                      <tr v-if="!history.length">
                        <td colspan="3" class="text-center p-4 text-gray-500">Chưa có lịch sử công tác</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </vs-tab>

        <!-- Tab 4: Tài liệu -->
        <vs-tab label="Tài liệu" icon-pack="feather" icon="icon-folder" :disabled="!isEdit">
          <div class="mt-4">
            <div class="flex justify-end mb-4">
              <vs-button color="success" type="border" class="p-3" @click="showDocumentModal = true"><i class="fa-solid fa-cloud-arrow-up mr-2"></i> Tải Lên Tài Liệu</vs-button>
            </div>
            <div class="vs-component vs-con-table stripe vs-table-primary">
              <div class="con-tablex vs-table--content">
                <div class="vs-con-tbody vs-table--tbody">
                  <table class="vs-table vs-table--tbody-table">
                    <thead class="vs-table--thead">
                      <tr>
                        <th>Tên tài liệu</th>
                        <th>Loại tài liệu</th>
                        <th>Ngày hết hạn</th>
                        <th class="text-center">Thao tác</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr class="tr-values vs-table--tr" v-for="(tr, index) in documents" :key="index">
                        <td class="td vs-table--td font-semibold">{{ tr.title }}</td>
                        <td class="td vs-table--td">{{ tr.document_type }}</td>
                        <td class="td vs-table--td">{{ tr.expiry_date || 'Không thời hạn' }}</td>
                        <td class="td vs-table--td text-center">
                          <vs-button v-if="tr.file_path" size="small" :href="tr.file_path" target="_blank" color="primary" type="border"><i class="fa-solid fa-download mr-1"></i> Tải về</vs-button>
                          <span v-else class="text-gray-400 text-xs">Chưa có file</span>
                        </td>
                      </tr>
                      <tr v-if="!documents.length">
                        <td colspan="4" class="text-center p-4 text-gray-500">Chưa có tài liệu đính kèm</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </vs-tab>
      </vs-tabs>
    </vx-card>

    <!-- Modal Hợp đồng -->
    <vs-popup title="Thêm Hợp đồng mới" :active.sync="showContractModal">
      <div class="mb-4">
        <vs-input class="w-full mb-4" label="Số hợp đồng" v-model="contractForm.contract_number" />
        <vs-input class="w-full mb-4" label="Loại hợp đồng (Ví dụ: Thử việc, 1 năm)" v-model="contractForm.contract_type" />
        <vs-input type="date" class="w-full mb-4" label="Từ ngày" v-model="contractForm.start_date" />
        <vs-input type="date" class="w-full mb-4" label="Đến ngày" v-model="contractForm.end_date" />
        <vs-input type="number" class="w-full mb-4" label="Lương cơ bản" v-model="contractForm.basic_salary" />
      </div>
      <div class="flex justify-end">
        <vs-button color="primary" @click="saveContract()">Lưu Hợp đồng</vs-button>
      </div>
    </vs-popup>

    <!-- Modal Tài liệu -->
    <vs-popup title="Tải lên Tài liệu" :active.sync="showDocumentModal">
      <div class="mb-4">
        <vs-input class="w-full mb-4" label="Tên tài liệu" v-model="documentForm.title" />
        <vs-input class="w-full mb-4" label="Loại tài liệu (Ví dụ: CCCD, Hợp đồng, Bằng cấp)" v-model="documentForm.document_type" />
        <vs-input type="date" class="w-full mb-4" label="Ngày hết hạn (Nếu có)" v-model="documentForm.expiry_date" />
        <div class="vx-col w-full mb-4">
          <label class="text-base font-medium">Đính kèm tài liệu (Có thể chọn nhiều file)</label>
          <input 
            type="file" 
            ref="fileInput"
            @change="handleFileUpload" 
            multiple 
            class="vs-inputx vs-input--input normal w-full mt-1"
          />
          <div v-if="selectedFiles.length > 0" class="mt-3">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div v-for="(file, index) in selectedFiles" :key="index" class="relative">
                <img v-if="file.preview" :src="file.preview" class="w-full h-24 object-cover rounded border border-gray-300" />
                <div v-else class="w-full h-24 flex items-center justify-center bg-gray-200 rounded border border-gray-300">
                   <i class="fa fa-file text-3xl text-gray-400"></i>
                </div>
                <vs-button size="small" color="danger" type="filled" icon-pack="feather" icon="icon-trash" class="absolute top-1 right-1" style="padding: 4px;" @click="removeFile(index)"></vs-button>
                <div class="text-xs mt-1 truncate" :title="file.name">{{ file.name }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="flex justify-end">
        <vs-button color="primary" @click="saveDocument()">Tải lên</vs-button>
      </div>
    </vs-popup>
  </div>
</template>

<script>
import axios from '../../../http/axios.js';
import vSelect from 'vue-select';

export default {
  components: {
    'v-select': vSelect,
  },
  data() {
    return {
      isEdit: false,
      id: null,
      userId: null,
      departments: [],
      jobTitles: [],
      jobLevels: [],
      contracts: [],
      history: [],
      documents: [],
      form: {
        user_id: null,
        user_name: '',
        email: '',
        employee_code: '',
        employee_type: 'fulltime',
        start_date: '',
        dob: '',
        gender: 1,
        id_card_number: '',
        id_card_date: '',
        probation_start_date: '',
        probation_end_date: '',
        work_status: 'working',
        resigned_date: '',
        resigned_reason: '',
        department_id: null,
        job_title_id: null,
        job_level_id: null,
        phone: '',
        address: '',
        base_salary: 0,
        bank_account: '',
        bank_name: '',
        emergency_contact_name: '',
        emergency_contact_phone: '',
        emergency_contact_relation: '',
      },
      showContractModal: false,
      contractForm: {
        contract_number: '',
        contract_type: '',
        start_date: '',
        end_date: '',
        basic_salary: 0
      },
      showDocumentModal: false,
      selectedFiles: [],
      documentForm: {
        title: '',
        document_type: '',
        expiry_date: '',
        file_path: ''
      }
    }
  },
  created() {
    this.fetchOptions();
    if (this.$route.params.id) {
      this.isEdit = true;
      this.id = this.$route.params.id;
      this.fetchEmployeeData();
    }
  },
  filters: {
    formatCurrency(val) {
      if (!val) return '0 VNĐ';
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val);
    }
  },
  methods: {
    fetchOptions() {
      axios.p('/api/hrm/departments/list').then(res => {
        this.departments = this.flattenDepartments(res.data || []);
      });
      axios.p('/api/hrm/job-titles/list').then(res => this.jobTitles = res.data || []);
      axios.p('/api/hrm/job-levels/list').then(res => this.jobLevels = res.data || []);
    },
    flattenDepartments(deps, level = 0) {
      let flat = [];
      if (!Array.isArray(deps)) return flat;
      deps.forEach(d => {
        const prefix = level > 0 ? '— '.repeat(level) : '';
        flat.push({
          id: d.id,
          name: d.name,
          displayName: `${prefix}${d.name}`
        });
        if (d.children && d.children.length > 0) {
          flat = flat.concat(this.flattenDepartments(d.children, level + 1));
        }
      });
      return flat;
    },
    fetchEmployeeData() {
      this.$vs.loading();
      axios.g(`/api/hrm/employees/show/${this.id}`).then(res => {
        this.$vs.loading.close();
        if (res.data.profile) {
          this.form = { ...this.form, ...res.data.profile };
          if (res.data.profile.user) {
            this.form.user_name = res.data.profile.user.name || '';
            this.form.email = res.data.profile.user.email || '';
          }
          this.userId = this.form.user_id;
        }
        this.contracts = res.data.contracts || [];
        this.history = res.data.history || [];
        this.documents = res.data.documents || [];
      }).catch(err => {
        this.$vs.loading.close();
        console.error(err);
      });
    },
    saveData() {
      if (!this.form.user_name || this.form.user_name.trim() === '') {
        this.$vs.notify({
          title: 'Cảnh báo',
          text: 'Vui lòng nhập Họ và tên nhân sự',
          color: 'warning',
          iconPack: 'feather',
          icon: 'icon-alert-triangle'
        });
        return;
      }

      this.$vs.loading();
      let url = this.isEdit ? `/api/hrm/employees/update/${this.form.id}` : '/api/hrm/employees/add';
      
      axios.p(url, this.form).then(res => {
        this.$vs.loading.close();
        if (res.data.status === 1) {
          this.$vs.notify({
            title: 'Thành công',
            text: res.data.message || 'Lưu thông tin nhân viên thành công!',
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          });
          if (!this.isEdit) {
            this.$router.push('/hrm/employees');
          }
        } else {
          this.$vs.notify({
            title: 'Lỗi',
            text: res.data.message || 'Lưu thất bại',
            color: 'danger',
            iconPack: 'feather',
            icon: 'icon-alert-circle'
          });
        }
      }).catch(err => {
        this.$vs.loading.close();
        console.error(err);
        this.$vs.notify({
          title: 'Lỗi',
          text: 'Có lỗi xảy ra khi lưu thông tin nhân viên',
          color: 'danger',
          iconPack: 'feather',
          icon: 'icon-alert-circle'
        });
      });
    },
    saveContract() {
      if(!this.userId) {
        this.$vs.notify({title: 'Lỗi', text: 'Không xác định được User ID', color: 'danger'});
        return;
      }
      this.contractForm.user_id = this.userId;
      axios.p('/api/hrm/employees/add-contract', this.contractForm).then(res => {
        if (res.data.status === 1) {
          this.$vs.notify({title: 'Thành công', text: res.data.message, color: 'success'});
          this.contracts.unshift(res.data.data);
          this.showContractModal = false;
          // reset form
          this.contractForm = { contract_number: '', contract_type: '', start_date: '', end_date: '', basic_salary: 0 };
        }
      });
    },
    handleFileUpload(event) {
      const files = Array.from(event.target.files);
      files.forEach(file => {
        if (file.type.startsWith('image/')) {
          const reader = new FileReader();
          reader.onload = (e) => {
            this.selectedFiles.push({ file: file, name: file.name, preview: e.target.result });
          };
          reader.readAsDataURL(file);
        } else {
           this.selectedFiles.push({ file: file, name: file.name, preview: null });
        }
      });
    },
    removeFile(index) {
      this.selectedFiles.splice(index, 1);
      if (this.selectedFiles.length === 0 && this.$refs.fileInput) {
        this.$refs.fileInput.value = '';
      }
    },
    saveDocument() {
      if(!this.userId) {
        this.$vs.notify({title: 'Lỗi', text: 'Không xác định được User ID', color: 'danger'});
        return;
      }
      
      const formData = new FormData();
      formData.append('user_id', this.userId);
      formData.append('title', this.documentForm.title);
      formData.append('document_type', this.documentForm.document_type);
      if (this.documentForm.expiry_date) {
        formData.append('expiry_date', this.documentForm.expiry_date);
      }
      
      this.selectedFiles.forEach((fileObj, index) => {
        formData.append(`attachments[${index}]`, fileObj.file);
      });

      this.$vs.loading();
      axios.p('/api/hrm/employees/add-document', formData).then(res => {
        this.$vs.loading.close();
        if (res.data.status === 1) {
          this.$vs.notify({title: 'Thành công', text: res.data.message, color: 'success'});
          if (Array.isArray(res.data.data)) {
            res.data.data.forEach(d => this.documents.unshift(d));
          } else {
            this.documents.unshift(res.data.data);
          }
          this.showDocumentModal = false;
          // reset form
          this.documentForm = { title: '', document_type: '', expiry_date: '', file_path: '' };
          this.selectedFiles = [];
          if (this.$refs.fileInput) this.$refs.fileInput.value = '';
        } else {
          this.$vs.notify({title: 'Lỗi', text: res.data.message || 'Lỗi', color: 'danger'});
        }
      }).catch(err => {
        this.$vs.loading.close();
        this.$vs.notify({title: 'Lỗi', text: 'Có lỗi xảy ra', color: 'danger'});
      });
    }
  }
}
</script>
