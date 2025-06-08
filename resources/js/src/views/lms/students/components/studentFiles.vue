<template>
  <div class="student-detail-sessions mt-5">
    <div class="vx-row">
      <div class="vx-col md:w-1/3 w-full mb-4">
        <label>File hồ sơ <span class="text-danger"> (*)</span></label>
        <input class="vs-inputx vs-input--input normal" type="file" @change="handleFileUpload" />
      </div>
      <div class="vx-col md:w-1/3 w-full mb-4">
        <label>Ghi chú <span class="text-danger"> (*)</span></label>
        <input
          class="vs-inputx vs-input--input normal"
          type="text"
          name="title"
          v-model="upload.note"
        />
      </div>
      <div class="mt-5">
        <vs-button class="mb-2" color="success" @click="uploadFile" >Lưu</vs-button>
      </div>
      <vs-alert :active.sync="alert.active" class="mb-5" :color="alert.color" closable icon-pack="feather" close-icon="icon-x">
        <div v-html="alert.body"></div>
      </vs-alert>
    </div>
    <div class="vs-component vs-con-table stripe vs-table-primary">
      <div class="con-tablex vs-table--content">
        <div class="vs-con-tbody vs-table--tbody ">
          <table class="vs-table vs-table--tbody-table">
            <thead class="vs-table--thead">
              <tr>
                <!---->
                <th colspan="1" rowspan="1">File</th>
                <th colspan="1" rowspan="1">Ghi chú</th>
                <th colspan="1" rowspan="1">Thao tác</th>
              </tr>
            </thead>
            <tr class="tr-values vs-table--tr tr-table-state-null" v-for="(item, index) in files" :key="index">
              <!---->
              
              <td class="td vs-table--td">
                <a :href="item.file_path" download>{{ item.file_name }}</a>
              </td>
              <td class="td vs-table--td">{{item.note}}</td>
              <td class="td vs-table--td list-action">
                <a :href="item.file_path" download><vs-button size="small"><i class="fas fa-download"></i></vs-button></a>
                <vs-button size="small" color="danger" @click="confirmDelete(item)"><i class="fa-solid fa-trash"></i></vs-button>
              </td>
            </tr>
          </table>
          
        </div>
      </div>
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
      handleFileUpload(event){
        const selectedFile = event.target.files[0];
        if (selectedFile) {
          this.upload.file = selectedFile;
          this.upload.fileName = selectedFile.name;
        }else{
          this.upload.file = "";
          this.upload.fileName = "";
        }
        console.log(this.upload.file)
      },
      uploadFile(){
        let mess = "";
        let resp = true;
        if (this.upload.file == "") {
          mess += " - File hồ sơ không được để trống<br/>";
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
        const formData = new FormData();
        formData.append('file', this.upload.file);
        formData.append('student_id', this.student_info.id);
        formData.append('note', this.upload.note);
        axios.p('/api/lms/students/upload-file', formData, 
          {
            headers: {
              'Content-Type': 'multipart/form-data'
            },
          }).then((response) => {  
            this.getStudentFiles();
          })
        .catch((error)   => { console.log(error); this.$vs.loading.close(); })
      },
      getStudentFiles(){
        const data = {
          student_id: this.student_info.id,
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
      confirmDelete (item) {
        this.delete_id = item.id
        this.$vs.dialog({
          type: 'confirm',
          color: 'danger',
          title: 'Thông báo',
          text: `Bạn chắc chắn muốn xoá file này`,
          accept: this.deleteFile,
          acceptText: 'Xóa',
          cancelText: 'Hủy'
        })
      },
      deleteFile(){
        const data = {
          id: this.delete_id,
        };
        this.$vs.loading();
        axios.p(`/api/lms/students/delete-file`,data)
        .then((response) => {
          this.$vs.loading.close();
          this.getData();
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
        })
      },
    }
  }
</script>