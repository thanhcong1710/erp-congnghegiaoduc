<template>
  <div class="student-detail-files mt-2">
    <!-- Compact Upload Form -->
    <div class="compact-upload-bar mb-3">
      <div class="upload-item" style="max-width: 250px;">
        <label class="form-label">Chọn tệp đính kèm <span class="text-danger">*</span></label>
        <input class="form-control-compact" type="file" @change="handleFileUpload" />
      </div>
      <div class="upload-item flex-1">
        <label class="form-label">Ghi chú tệp <span class="text-danger">*</span></label>
        <input
          class="form-control-compact"
          type="text"
          placeholder="Nhập ghi chú tệp hồ sơ..."
          v-model="upload.note"
        />
      </div>
      <div class="upload-btn-item">
        <button class="btn-compact-save" @click="uploadFile">
          <i class="fa-solid fa-cloud-arrow-up mr-1"></i> Tải lên
        </button>
      </div>
    </div>

    <vs-alert :active.sync="alert.active" class="mb-3" :color="alert.color" closable icon-pack="feather" close-icon="icon-x">
      <div v-html="alert.body"></div>
    </vs-alert>

    <!-- Files Table -->
    <div class="table-responsive rounded border border-slate-200">
      <table class="w-full text-xs bg-white">
        <thead class="bg-slate-50 text-gray-600 font-bold border-b border-slate-200 text-2xs uppercase">
          <tr>
            <th class="py-2.5 px-3" style="width: 50px">STT</th>
            <th class="py-2.5 px-3" style="width: 280px">Tên tệp</th>
            <th class="py-2.5 px-3">Ghi chú</th>
            <th class="py-2.5 px-3 text-center" style="width: 100px">Thao tác</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-if="!files || files.length === 0">
            <td colspan="4" class="text-center py-4 text-gray-400 italic">
              Chưa có tài liệu hồ sơ nào được lưu trữ
            </td>
          </tr>
          <tr v-for="(item, index) in files" :key="index" class="hover:bg-slate-50">
            <td class="py-2 px-3 text-center text-gray-500 font-semibold">{{ index + 1 }}</td>
            <td class="py-2 px-3">
              <a :href="item.file_path" target="_blank" class="text-primary font-semibold hover:underline flex items-center">
                <i class="fa-solid fa-file-pdf text-rose-500 mr-1.5" style="font-size: 14px;"></i>
                {{ item.file_name }}
              </a>
            </td>
            <td class="py-2 px-3 text-gray-700">{{ item.note }}</td>
            <td class="py-2 px-3 text-center">
              <a :href="item.file_path" target="_blank" class="mr-2 text-primary" title="Xem tệp">
                <i class="fa fa-eye"></i>
              </a>
              <button class="text-rose-500 hover:text-rose-700" @click="confirmDelete(item)" title="Xóa tệp">
                <i class="fa-solid fa-trash"></i>
              </button>
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
        upload: {
          note: "",
          file: "",
          fileName:"",
        },
        alert:{
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
          accept: () => this.deleteFile(item),
          acceptText: 'Xóa',
          cancelText: 'Hủy'
        })
      },
      deleteFile(item){
        const data = {
          file_id: item.id
        }
        this.$vs.loading()
        axios.p('/api/lms/students/delete_files', data)
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
        axios.p('/api/lms/students/files', data)
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
        axios.p("/api/lms/students/upload_files",formData,{
          headers: {
            "Content-Type": "multipart/form-data"
          }
        })
        .then((response) => {
          this.$vs.loading.close();
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          this.upload.file = ""
          this.upload.note = ""
          this.getStudentFiles();
        })
        .catch((e) => {
          console.log(e);
          this.$vs.loading.close();
        });
      }
    }
  }
</script>

<style scoped>
.compact-upload-bar {
  display: flex;
  align-items: flex-end;
  gap: 12px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  padding: 8px 12px;
}

.upload-item {
  display: flex;
  flex-direction: column;
}

.form-label {
  font-size: 11px;
  font-weight: 600;
  color: #475569;
  margin-bottom: 3px;
}

.form-control-compact {
  height: 32px;
  border: 1px solid #cbd5e1;
  border-radius: 5px;
  padding: 4px 8px;
  font-size: 12.5px;
  background: #ffffff;
  outline: none;
  width: 100%;
}

.upload-btn-item {
  height: 32px;
  display: flex;
  align-items: flex-end;
}

.btn-compact-save {
  height: 32px;
  padding: 0 14px;
  font-size: 12px;
  font-weight: 600;
  background: #28c76f;
  color: #ffffff;
  border: 1px solid #28c76f;
  border-radius: 5px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  transition: all 0.2s ease;
  white-space: nowrap;
}

.btn-compact-save:hover {
  background: #20a159;
}

.table-responsive {
  width: 100%;
  overflow-x: auto;
}
</style>
