<template>
  <div id="page-staff-add">
    <vx-card no-shadow title="Thêm mới Nhân viên Sales cấp dưới">
      <div class="vx-row">
        <div class="vx-col md:w-1/2 w-full">
          <div class="mb-6">
            <label>Họ tên <span class="text-danger"> (*)</span></label>
            <input type="text" v-model="user_info.name" class="vs-inputx vs-input--input normal w-full mt-1">
          </div>
          <div class="mb-6">
            <label>Điện thoại <span class="text-danger"> (*)</span></label>
            <input type="text" v-model="user_info.phone" class="vs-inputx vs-input--input normal w-full mt-1">
          </div>
          <div class="mb-6">
            <label>Email (Tùy chọn)</label>
            <input type="email" v-model="user_info.email" class="vs-inputx vs-input--input normal w-full mt-1">
          </div>
        </div>
        <div class="vx-col md:w-1/2 w-full bg-light rounded p-4" style="background-color: #f7f7f7;">
          <div class="mb-6">
            <label class="text-grey font-bold">Mã nhân viên</label>
            <p class="mt-1 p-3 bg-white border rounded text-grey">Hệ thống sẽ tự sinh mã CTxxx</p>
          </div>
          <div class="mb-6">
            <label class="text-grey font-bold">Quản lý trực tiếp</label>
            <p class="mt-1 p-3 bg-white border rounded text-grey">{{ activeUser.displayName }} ({{ activeUser.hrm_id }})</p>
          </div>
          <div class="mb-6">
            <label class="text-grey font-bold">Trung tâm mặc định</label>
            <p class="mt-1 p-3 bg-white border rounded text-grey">{{ activeUser.branch_name || 'Trung tâm của bạn' }}</p>
          </div>
          <div class="mb-0">
            <label class="text-grey font-bold">Vai trò mặc định</label>
            <p class="mt-1 p-3 bg-white border rounded text-grey text-success">Nhân viên kinh doanh</p>
          </div>
        </div>
      </div>
      <vs-divider />
      <div class="vx-row">
        <div class="vx-col w-full flex justify-end">
          <vs-button color="dark" type="border" class="mr-3" @click="$router.push('/crm/staff')">Hủy</vs-button>
          <vs-button color="success" icon-pack="feather" icon="icon-save" @click="save">Thêm mới</vs-button>
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
      user_info: { name: '', email: '', phone: '' }
    }
  },
  computed: {
    activeUser() { return this.$store.state.AppActiveUser }
  },
  methods: {
    save() {
      if (!this.user_info.name) {
        return this.$vs.notify({ title: 'Lỗi', text: 'Vui lòng nhập Họ tên nhân viên', color: 'danger' })
      }
      if (!this.user_info.phone) {
        return this.$vs.notify({ title: 'Lỗi', text: 'Vui lòng nhập Số điện thoại', color: 'danger' })
      }

      this.$vs.loading()
      axios.p("/api/crm/staff/add", this.user_info)
        .then((response) => {
          this.$vs.loading.close()
          if (response.data.status) {
            this.$vs.notify({ title: 'Thành Công', text: response.data.message, color: 'success', iconPack: 'feather', icon: 'icon-check' })
            this.$router.push('/crm/staff')
          } else {
            this.$vs.notify({ title: 'Lỗi', text: response.data.message, color: 'danger', iconPack: 'feather', icon: 'icon-alert-circle' })
          }
        }).catch((e) => { 
          console.log(e);
          this.$vs.loading.close() 
        })
    }
  }
}
</script>
