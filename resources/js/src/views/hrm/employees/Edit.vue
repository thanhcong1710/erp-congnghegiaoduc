<template>
  <div>
    <div class="flex items-center mb-4">
      <vs-button color="dark" type="border"  @click="$router.push('/hrm/employees')" class="mr-4"><i class="fa fa-arrow-left"></i> Quay lại</vs-button>
      <h4>{{ isEdit ? 'Cập nhật Hồ sơ Nhân viên' : 'Thêm mới Nhân viên' }}</h4>
    </div>

    <vs-card>
      <vs-tabs>
        <!-- Tab 1: Thông tin chung -->
        <vs-tab label="Thông tin chung" icon="person">
          <div class="mt-4">
            <div class="vx-row mb-4">
              <div class="vx-col w-1/2">
                <vs-input class="w-full" label="User ID (Tài khoản HT)" v-model="form.user_id" :disabled="isEdit" />
              </div>
              <div class="vx-col w-1/2">
                <vs-input class="w-full" label="Mã nhân viên" v-model="form.employee_code" />
              </div>
            </div>
            <div class="vx-row mb-4">
              <div class="vx-col w-1/2">
                <label class="text-base font-medium">Phòng ban</label>
                <v-select class="w-full mt-1" v-model="form.department_id" :options="departments" :reduce="item => item.id" label="name" />
              </div>
              <div class="vx-col w-1/2">
                <label class="text-base font-medium">Chức danh</label>
                <v-select class="w-full mt-1" v-model="form.job_title_id" :options="jobTitles" :reduce="item => item.id" label="name" />
              </div>
            </div>
            <div class="vx-row mb-4">
              <div class="vx-col w-1/2">
                <label class="text-base font-medium">Cấp bậc</label>
                <v-select class="w-full mt-1" v-model="form.job_level_id" :options="jobLevels" :reduce="item => item.id" label="name" />
              </div>
              <div class="vx-col w-1/2">
                <vs-input class="w-full" label="CCCD/Passport" v-model="form.id_card_number" />
              </div>
            </div>
            <div class="vx-row mb-4">
              <div class="vx-col w-1/2">
                <vs-input class="w-full" label="Số điện thoại" v-model="form.phone" />
              </div>
              <div class="vx-col w-1/2">
                <vs-input class="w-full" label="Địa chỉ" v-model="form.address" />
              </div>
            </div>
            <h5 class="mb-2 mt-6">Liên hệ khẩn cấp</h5>
            <div class="vx-row mb-4">
              <div class="vx-col w-1/3">
                <vs-input class="w-full" label="Họ tên" v-model="form.emergency_contact_name" />
              </div>
              <div class="vx-col w-1/3">
                <vs-input class="w-full" label="Số điện thoại" v-model="form.emergency_contact_phone" />
              </div>
              <div class="vx-col w-1/3">
                <vs-input class="w-full" label="Quan hệ" v-model="form.emergency_contact_relation" />
              </div>
            </div>
            <div class="vx-row mt-6">
              <div class="vx-col w-full flex justify-end">
                <vs-button @click="saveData()" color="primary">Lưu thay đổi</vs-button>
              </div>
            </div>
          </div>
        </vs-tab>

        <!-- Tab 2: Hợp đồng -->
        <vs-tab label="Hợp đồng" icon="description" :disabled="!isEdit">
          <div class="mt-4">
            <div class="flex justify-end mb-4">
              <vs-button color="success" @click="showContractModal = true"><i class="fa fa-plus"></i> Thêm Hợp đồng</vs-button>
            </div>
            <vs-table :data="contracts">
              <template slot="thead">
                <vs-th>Số hợp đồng</vs-th>
                <vs-th>Loại hợp đồng</vs-th>
                <vs-th>Từ ngày</vs-th>
                <vs-th>Đến ngày</vs-th>
                <vs-th>Lương cơ bản</vs-th>
              </template>
              <template slot-scope="{data}">
                <vs-tr :key="indextr" v-for="(tr, indextr) in data">
                  <vs-td>{{ tr.contract_number }}</vs-td>
                  <vs-td>{{ tr.contract_type }}</vs-td>
                  <vs-td>{{ tr.start_date }}</vs-td>
                  <vs-td>{{ tr.end_date }}</vs-td>
                  <vs-td>{{ tr.basic_salary | formatCurrency }}</vs-td>
                </vs-tr>
              </template>
            </vs-table>
          </div>
        </vs-tab>

        <!-- Tab 3: Lịch sử công tác -->
        <vs-tab label="Lịch sử công tác" icon="history" :disabled="!isEdit">
          <div class="mt-4">
             <vs-table :data="history">
              <template slot="thead">
                <vs-th>Ngày hiệu lực</vs-th>
                <vs-th>Loại</vs-th>
                <vs-th>Ghi chú</vs-th>
              </template>
              <template slot-scope="{data}">
                <vs-tr :key="indextr" v-for="(tr, indextr) in data">
                  <vs-td>{{ tr.effective_date }}</vs-td>
                  <vs-td>{{ tr.type }}</vs-td>
                  <vs-td>{{ tr.note }}</vs-td>
                </vs-tr>
              </template>
            </vs-table>
          </div>
        </vs-tab>

        <!-- Tab 4: Tài liệu -->
        <vs-tab label="Tài liệu" icon="folder" :disabled="!isEdit">
          <div class="mt-4">
            <div class="flex justify-end mb-4">
              <vs-button color="success" @click="showDocumentModal = true"><i class="fa fa-upload"></i> Tải lên Tài liệu</vs-button>
            </div>
             <vs-table :data="documents">
              <template slot="thead">
                <vs-th>Tên tài liệu</vs-th>
                <vs-th>Loại</vs-th>
                <vs-th>Ngày hết hạn</vs-th>
                <vs-th>Thao tác</vs-th>
              </template>
              <template slot-scope="{data}">
                <vs-tr :key="indextr" v-for="(tr, indextr) in data">
                  <vs-td>{{ tr.title }}</vs-td>
                  <vs-td>{{ tr.document_type }}</vs-td>
                  <vs-td>{{ tr.expiry_date }}</vs-td>
                  <vs-td>
                    <vs-button v-if="tr.file_path" size="small" :href="tr.file_path" target="_blank" color="primary"><i class="fa fa-download"></i> Tải xuống</vs-button>
                    <span v-else class="text-gray-500">Chưa có link</span>
                  </vs-td>
                </vs-tr>
              </template>
            </vs-table>
          </div>
        </vs-tab>
      </vs-tabs>
    </vs-card>

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
        employee_code: '',
        department_id: null,
        job_title_id: null,
        job_level_id: null,
        id_card_number: '',
        phone: '',
        address: '',
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
      axios.p('/api/hrm/departments/list').then(res => this.departments = res.data);
      axios.p('/api/hrm/job-titles/list').then(res => this.jobTitles = res.data);
      axios.p('/api/hrm/job-levels/list').then(res => this.jobLevels = res.data);
    },
    fetchEmployeeData() {
      axios.g(`/api/hrm/employees/show/${this.id}`).then(res => {
        if (res.data.profile) {
          this.form = { ...res.data.profile };
          this.userId = this.form.user_id;
        }
        this.contracts = res.data.contracts || [];
        this.history = res.data.history || [];
        this.documents = res.data.documents || [];
      }).catch(err => {
        console.error(err);
      });
    },
    saveData() {
      let url = this.isEdit ? `/api/hrm/employees/update/${this.form.id}` : '/api/hrm/employees/add';
      
      axios.p(url, this.form).then(res => {
        if (res.data.status === 1) {
          this.$vs.notify({title: 'Thành công', text: res.data.message, color: 'success'});
          if (!this.isEdit) {
            this.$router.push('/hrm/employees');
          }
        } else {
          this.$vs.notify({title: 'Lỗi', text: res.data.message || 'Lưu thất bại', color: 'danger'});
        }
      }).catch(err => {
        this.$vs.notify({title: 'Lỗi', text: 'Có lỗi xảy ra', color: 'danger'});
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
