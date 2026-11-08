<template>
  <div class="student-detail-files mt-3">
    <!-- Compact Upload Form -->
    <div class="upload-bar-card mb-4">
      <div class="vx-row items-end">
        <div class="vx-col md:w-1/3 w-full mb-2 md:mb-0">
          <label class="upload-label">
            <i class="fa-solid fa-paperclip mr-1 text-primary"></i> Chọn tệp đính kèm <span class="text-danger">*</span>
          </label>
          <input class="upload-input-file" type="file" @change="handleFileUpload" />
        </div>
        <div class="vx-col md:w-1/2 w-full mb-2 md:mb-0">
          <label class="upload-label">
            <i class="fa-solid fa-note-sticky mr-1 text-gray-400"></i> Ghi chú tệp <span class="text-danger">*</span>
          </label>
          <input
            class="upload-input-text"
            type="text"
            placeholder="Nhập ghi chú hoặc mô tả tệp..."
            v-model="upload.note"
          />
        </div>
        <div class="vx-col md:w-1/6 w-full">
          <button class="btn-upload-submit" @click="uploadFile">
            <i class="fa-solid fa-cloud-arrow-up mr-1.5"></i> Tải lên
          </button>
        </div>
      </div>
    </div>

    <vs-alert :active.sync="alert.active" class="mb-3" :color="alert.color" closable icon-pack="feather" close-icon="icon-x">
      <div v-html="alert.body"></div>
    </vs-alert>

    <!-- Files Table with Grid Borders -->
    <div class="bordered-table-container">
      <table class="grid-border-table">
        <thead>
          <tr>
            <th class="text-center" style="width: 60px">STT</th>
            <th style="width: 340px">Tên tệp hồ sơ</th>
            <th>Ghi chú</th>
            <th class="text-center" style="width: 120px">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="!files || files.length === 0">
            <td colspan="4" class="text-center py-6 text-gray-400 italic">
              <i class="fa-regular fa-folder-open text-2xl text-gray-300 block mb-2"></i>
              Chưa có tài liệu hồ sơ nào được lưu trữ
            </td>
          </tr>
          <tr v-for="(item, index) in files" :key="index" class="grid-row-hover">
            <td class="text-center font-bold text-gray-500">{{ index + 1 }}</td>
            <td>
              <a :href="item.file_path" target="_blank" class="file-name-link">
                <i class="fa-solid fa-file-pdf text-rose-500 mr-2 text-base"></i>
                <span class="font-bold">{{ item.file_name }}</span>
              </a>
            </td>
            <td>
              <span class="text-gray-800">{{ item.note || '---' }}</span>
            </td>
            <td class="text-center">
              <div class="flex items-center justify-center gap-2">
                <a :href="item.file_path" target="_blank" class="action-btn-file btn-view" title="Xem tệp">
                  <i class="fa-solid fa-eye"></i>
                </a>
                <button class="action-btn-file btn-delete" @click="confirmDelete(item)" title="Xóa tệp">
                  <i class="fa-solid fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
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
        upload:{
          file:"",
          note:"",
          fileName:""
        },
        alert: {
          active: false,
          body: '',
          color:'',
        },
        files:[]
      }
    },
    async created() {
      this.getStudentFiles();
    },
    methods: {
      handleFileUpload(e){
        this.upload.file = e.target.files[0];
        this.upload.fileName = e.target.files[0].name;
      },
      confirmDelete (item) {
        this.$vs.dialog({
          type: 'confirm',
          color: 'danger',
          title: 'Thông báo',
          text: `Bạn chắc chắn xóa file ${item.file_name}?`,
          accept: () => {
            this.deleteFile(item)
          }
        })
      },
      deleteFile(item){
        const data = {
          file_id: item.id
        }
        this.$vs.loading()
        axios.p('/api/lms/students/delete-file', data)
          .then((response) => {
            this.$vs.loading.close()
            this.getStudentFiles();
            this.$vs.notify({
              title: 'Thành Công',
              text: response.data.message,
              color: 'success',
              iconPack: 'feather',
              icon: 'icon-check'
            })
          })
          .catch((error) => {
            console.log(error);
            this.$vs.loading.close();
          })
      },
      getStudentFiles(){
        const data = {
          student_id: this.student_info.id
        }
        this.$vs.loading()
        axios.p('/api/lms/students/get-files', data)
          .then((response) => {
            this.$vs.loading.close()
            this.files = response.data
          })
          .catch((error) => {
            console.log(error);
            this.$vs.loading.close();
          })
      },
      uploadFile(){
        let mess = "";
        let resp = true;
        if (this.upload.file == "") {
          mess += " - File không được để trống<br/>";
          resp = false;
        }
        if (this.upload.note == "") {
          mess += " - Ghi chú không được để trống<br/>";
          resp = false;
        }
        if (!resp) {
          this.alert.color = 'danger'
          this.alert.body = mess;
          this.alert.active = true;
          return false;
        }
        let formData = new FormData();
        formData.append("file", this.upload.file);
        formData.append("note", this.upload.note);
        formData.append("student_id", this.student_info.id);
        this.$vs.loading()
        axios.p("/api/lms/students/upload-file", formData, {
          headers: {
            "Content-Type": "multipart/form-data"
          }
        })
        .then((response) => {
          this.$vs.loading.close()
          this.getStudentFiles();
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          this.upload.file = ""
          this.upload.note = ""
          this.upload.fileName = ""
        })
        .catch((error) => {
          console.log(error);
          this.$vs.loading.close();
        })
      }
    }
  }
</script>

<style scoped>
.upload-bar-card {
  background: #f8fafc;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  padding: 14px 18px;
}

.upload-label {
  display: block;
  font-size: 13.5px;
  font-weight: 600;
  color: #475569;
  margin-bottom: 6px;
}

.upload-input-file {
  width: 100%;
  padding: 6px;
  background: #ffffff;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  font-size: 13.5px;
}

.upload-input-text {
  width: 100%;
  padding: 7px 12px;
  background: #ffffff;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  font-size: 14px;
  color: #1e293b;
  outline: none;
  transition: border-color 0.15s;
}

.upload-input-text:focus {
  border-color: #7367f0;
}

.btn-upload-submit {
  width: 100%;
  height: 38px;
  background: linear-gradient(135deg, #7367f0, #5e50ee);
  color: #ffffff;
  font-size: 14px;
  font-weight: 600;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.btn-upload-submit:hover {
  background: linear-gradient(135deg, #5e50ee, #4839eb);
  box-shadow: 0 2px 6px rgba(115, 103, 240, 0.4);
}

.bordered-table-container {
  width: 100%;
  overflow-x: auto;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  background: #ffffff;
}

.grid-border-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
}

.grid-border-table thead th {
  background: #f1f5f9;
  color: #1e293b;
  font-weight: 700;
  font-size: 13.5px;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  padding: 11px 14px;
  border: 1px solid #cbd5e1;
  white-space: nowrap;
}

.grid-border-table tbody td {
  padding: 12px 14px;
  border: 1px solid #cbd5e1;
  vertical-align: middle;
  font-size: 14px;
  color: #334155;
}

.grid-row-hover:hover td {
  background-color: #f8fafc;
}

.file-name-link {
  display: inline-flex;
  align-items: center;
  color: #7367f0;
  font-size: 14px;
  text-decoration: none;
}

.file-name-link:hover {
  text-decoration: underline;
}

.action-btn-file {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: 6px;
  font-size: 14px;
  border: none;
  cursor: pointer;
  transition: all 0.15s;
}

.btn-view {
  background: #eff6ff;
  color: #2563eb;
  border: 1px solid #bfdbfe;
}

.btn-view:hover {
  background: #dbeafe;
}

.btn-delete {
  background: #fef2f2;
  color: #dc2626;
  border: 1px solid #fecaca;
}

.btn-delete:hover {
  background: #fee2e2;
}
</style>
