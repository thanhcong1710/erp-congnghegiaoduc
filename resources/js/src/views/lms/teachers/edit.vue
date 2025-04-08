<template>

  <div id="page-users-list">
    <vx-card no-shadow class="mt-5">
        <h5 class="w-full mb-3"><i class="fa-solid fa-user-graduate mr-1"></i> Cập nhật thông tin buổi dạy</h5>
        <div class="vx-row">
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label>Trung tâm</label>
            <input
              class="vs-inputx vs-input--input normal"
              type="text"
              name="title"
              v-model="schedule_info.branch_name"
              disabled="true"
            />
          </div>
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label>Lớp</label>
            <input
              class="vs-inputx vs-input--input normal"
              type="text"
              name="title"
              v-model="schedule_info.class_name"
              disabled="true"
            />
          </div>
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label>Ngày</label>
            <input
              class="vs-inputx vs-input--input normal"
              type="text"
              name="title"
              v-model="schedule_info.class_date"
              disabled="true"
            />
          </div>
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label>Giáo viên</label>
            <vue-select
              label="label"
              placeholder="Chọn giáo viên"
              :options="html.teachers.list"
              v-model="html.teachers.item"
              :searchable="true"
              language="tv-VN"
              @input="saveTeacher"
            ></vue-select>
          </div>
          <div class="vx-col md:w-1/3 w-full mb-4">
            <label>Quản lý lớp học</label>
            <vue-select
                label="label"
                placeholder="Chọn quản lý lớp học"
                :options="html.cms.list"
                v-model="html.cms.item"
                :searchable="true"
                language="tv-VN"
                @input="saveCM"
            ></vue-select>
          </div>
        </div>
        <vs-alert :active.sync="alert.active" class="mb-5" :color="alert.color" closable icon-pack="feather" close-icon="icon-x">
          <div v-html="alert.body"></div>
        </vs-alert>
        <div class="vx-col w-full">
          <router-link class="btn btn-danger" :to="`/lms/teachers`">
            <vs-button color="dark" type="border" class="mb-2 mr-3" >Hủy</vs-button>
          </router-link>
          <vs-button class="mb-2" color="success" @click="save">Lưu</vs-button>
        </div>
    </vx-card>
  </div>
</template>

<script>

  import select from 'vue-select'
  import axios from '../../../http/axios.js'
  import u from '../../../until/helper.js'
  
  export default {
    components: {
      "vue-select": select,
    },
    data() {
      return {
        html:{
          teachers:{
            item: '',
            list: []
          },
          cms:{
            item: '',
            list: []
          },
        },
        schedule_info:{
        },
        alert:{
          active: false,
          body: '',
          color:'',
        },
      }
    },
    async created() {
      axios.g(`/api/system/teachers/0`)
        .then(response => {
        this.html.teachers.list = response.data
      })
      await axios.g(`/api/system/cms/0`)
        .then(response => {
        this.html.cms.list = response.data
      })
      this.loadDetail();
    },
    methods: {
      loadDetail(){
        this.$vs.loading();
        axios.g(`/api/lms/teachers/show/${this.$route.params.id}`)
          .then(response => {
          this.$vs.loading.close();
          this.schedule_info = response.data
          this.html.teachers.item = this.html.teachers.list.filter(item => item.id == response.data.teacher_id)[0]
          this.html.cms.item = this.html.cms.list.filter(item => item.id == response.data.cm_id)[0]
        })
      },
      saveCM(data = null){
        if (data && typeof data === 'object') {
          const cm_id = data.id
          this.schedule_info.cm_id = cm_id
        }else{
          this.schedule_info.cm_id = ""
        }
      },
      saveTeacher(data = null){
        if (data && typeof data === 'object') {
          const teacher_id = data.id
          this.schedule_info.teacher_id = teacher_id
        }else{
          this.schedule_info.teacher_id = ""
        }
      },
      save() {
        let mess = "";
        let resp = true;
        if (this.schedule_info.teacher_id == "") {
          mess += " - Giáo viên không được để trống<br/>";
          resp = false;
        }
        if (this.schedule_info.cm_id == "") {
          mess += " - Trợ giảng không được để trống<br/>";
          resp = false;
        }
        if (!resp) {
          this.alert.color = 'danger'
          this.alert.body = mess;
          this.alert.active = true;
          return false;
        }
        this.$vs.loading()
        axios.p("/api/lms/teachers/update",this.schedule_info)
          .then((response) => {
            this.$vs.loading.close();
            if (response.data.status) {
              this.$vs.notify({
                title: 'Thành Công',
                text: response.data.message,
                color: 'success',
                iconPack: 'feather',
                icon: 'icon-check'
              })
              this.$router.push('/lms/teachers')
            }else{
              this.$vs.notify({
                title: 'Lỗi',
                text: response.data.message,
                iconPack: 'feather',
                icon: 'icon-alert-circle',
                color: 'danger'
              })
            }
          })
          .catch((e) => {
            console.log(error);
            this.$vs.loading.close();
          });
      },
    },
  }
</script>