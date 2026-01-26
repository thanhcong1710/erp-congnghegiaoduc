<template>
  <div id="page-zoom-room-edit">
    <vx-card no-shadow class="mt-5">
      <h4 class="mb-5">Chỉnh sửa phòng Zoom</h4>
      
      <div class="vx-row">
        <div class="vx-col md:w-1/2 w-full mb-4">
          <label class="vs-input--label">Lớp học</label>
          <vs-input class="w-full" v-model="classInfo" disabled></vs-input>
        </div>

        <div class="vx-col md:w-1/2 w-full mb-4">
          <label class="vs-input--label">Chủ đề phòng học <span class="text-danger">*</span></label>
          <vs-input class="w-full" v-model="formData.topic" placeholder="Nhập chủ đề phòng học"></vs-input>
        </div>
      </div>

      <div class="vx-row">
        <div class="vx-col md:w-1/2 w-full mb-4">
          <label class="vs-input--label">Meeting ID <span class="text-danger">*</span></label>
          <vs-input class="w-full" v-model="formData.meeting_id" placeholder="Nhập Meeting ID"></vs-input>
        </div>

        <div class="vx-col md:w-1/2 w-full mb-4">
          <label class="vs-input--label">Mật khẩu phòng</label>
          <vs-input class="w-full" v-model="formData.meeting_password" placeholder="Nhập mật khẩu (nếu có)"></vs-input>
        </div>
      </div>

      <div class="vx-row">
        <div class="vx-col w-full mb-4">
          <label class="vs-input--label">Link tham gia <span class="text-danger">*</span></label>
          <vs-input class="w-full" v-model="formData.join_url" placeholder="https://zoom.us/j/..."></vs-input>
        </div>
      </div>

      <div class="vx-row">
        <div class="vx-col w-full mb-4">
          <label class="vs-input--label">Link bắt đầu (cho giáo viên)</label>
          <vs-input class="w-full" v-model="formData.start_url" placeholder="https://zoom.us/s/..."></vs-input>
        </div>
      </div>

      <div class="vx-row">
        <div class="vx-col md:w-1/2 w-full mb-4">
          <label class="vs-input--label">Email host</label>
          <vs-input class="w-full" v-model="formData.host_email" placeholder="email@example.com"></vs-input>
        </div>

        <div class="vx-col md:w-1/2 w-full mb-4">
          <label class="vs-input--label">Thời lượng (phút)</label>
          <vs-input class="w-full" type="number" v-model="formData.duration" placeholder="60"></vs-input>
        </div>
      </div>

      <div class="vx-row mt-5">
        <div class="vx-col w-full">
          <vs-button class="mr-3" @click="save">Cập nhật</vs-button>
          <vs-button color="dark" type="border" @click="$router.push('/settings/zoom-rooms')">Hủy</vs-button>
        </div>
      </div>
    </vx-card>
  </div>
</template>

<script>
import axios from '../../../http/axios.js'

export default {
  data() {
    return {
      formData: {
        id: null,
        class_id: null,
        topic: '',
        meeting_id: '',
        meeting_password: '',
        join_url: '',
        start_url: '',
        host_email: '',
        duration: 60,
        timezone: 'Asia/Ho_Chi_Minh'
      },
      classInfo: ''
    }
  },
  created() {
    this.loadData();
  },
  methods: {
    loadData() {
      const id = this.$route.params.id
      this.$vs.loading()
      axios.g(`/api/settings/zoom-rooms/show/${id}`)
        .then((response) => {
          this.$vs.loading.close()
          const data = response.data
          this.formData = {
            id: data.id,
            class_id: data.class_id,
            topic: data.topic,
            meeting_id: data.meeting_id,
            meeting_password: data.meeting_password,
            join_url: data.join_url,
            start_url: data.start_url,
            host_email: data.host_email,
            duration: data.duration,
            timezone: data.timezone
          }
          this.classInfo = `${data.cls_name} - ${data.branch_name}`
        })
        .catch((error) => {
          console.log(error);
          this.$vs.loading.close();
          this.$router.push('/settings/zoom-rooms')
        })
    },
    validate() {
      if (!this.formData.topic) {
        this.$vs.notify({
          title: 'Lỗi',
          text: 'Vui lòng nhập chủ đề phòng học',
          color: 'danger',
          iconPack: 'feather',
          icon: 'icon-alert-circle'
        })
        return false
      }
      if (!this.formData.meeting_id) {
        this.$vs.notify({
          title: 'Lỗi',
          text: 'Vui lòng nhập Meeting ID',
          color: 'danger',
          iconPack: 'feather',
          icon: 'icon-alert-circle'
        })
        return false
      }
      if (!this.formData.join_url) {
        this.$vs.notify({
          title: 'Lỗi',
          text: 'Vui lòng nhập link tham gia',
          color: 'danger',
          iconPack: 'feather',
          icon: 'icon-alert-circle'
        })
        return false
      }
      return true
    },
    save() {
      if (!this.validate()) return

      this.$vs.loading()
      axios.p('/api/settings/zoom-rooms/save', this.formData)
        .then((response) => {
          this.$vs.loading.close()
          if (response.data.status === 1) {
            this.$vs.notify({
              title: 'Thành Công',
              text: response.data.message,
              color: 'success',
              iconPack: 'feather',
              icon: 'icon-check'
            })
            this.$router.push('/settings/zoom-rooms')
          } else {
            this.$vs.notify({
              title: 'Lỗi',
              text: response.data.message,
              color: 'danger',
              iconPack: 'feather',
              icon: 'icon-alert-circle'
            })
          }
        })
        .catch((error) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Lỗi',
            text: 'Có lỗi xảy ra khi cập nhật phòng Zoom',
            color: 'danger',
            iconPack: 'feather',
            icon: 'icon-alert-circle'
          })
        })
    }
  }
}
</script>

<style scoped>
.text-danger {
  color: #e74c3c;
}
</style>
