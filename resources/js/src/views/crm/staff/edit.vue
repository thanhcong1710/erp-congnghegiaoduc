<template>
  <div id="page-staff-edit">
    <vx-card no-shadow title="Chỉnh sửa Nhân viên cấp dưới">
      <div class="vx-row" v-if="user_info.id">
        <div class="vx-col md:w-1/2 w-full">
          <div class="mb-6">
            <label>Mã nhân viên (HrmId)</label>
            <input type="text" v-model="user_info.hrm_id" disabled class="vs-inputx vs-input--input normal w-full mt-1 bg-grey-light cursor-not-allowed">
          </div>
          <div class="mb-6">
            <label>Họ tên <span class="text-danger"> (*)</span></label>
            <input type="text" v-model="user_info.name" class="vs-inputx vs-input--input normal w-full mt-1">
          </div>
          <div class="mb-6">
            <label>Điện thoại <span class="text-danger"> (*)</span></label>
            <input type="text" v-model="user_info.phone" class="vs-inputx vs-input--input normal w-full mt-1">
          </div>
          <div class="mb-6">
            <label>Email</label>
            <input type="email" v-model="user_info.email" class="vs-inputx vs-input--input normal w-full mt-1">
          </div>
          <div class="mb-6">
            <label>Mật khẩu mới (Để trống nếu không muốn đổi)</label>
            <input type="password" v-model="user_info.password" class="vs-inputx vs-input--input normal w-full mt-1" placeholder="Nhập mật khẩu mới cho nhân viên...">
          </div>
        </div>
        <div class="vx-col md:w-1/2 w-full bg-light rounded p-4" style="background-color: #f7f7f7;">
          <div class="mb-6">
            <label class="text-grey font-bold">Người quản lý</label>
            <p class="mt-1 p-3 bg-white border rounded text-grey cursor-not-allowed">{{ user_info.manager_hrm_id }}</p>
          </div>
          <div class="mb-6">
            <label class="text-grey font-bold">Trung tâm</label>
            <p class="mt-1 p-3 bg-white border rounded text-grey cursor-not-allowed">{{ user_info.branch_name }}</p>
          </div>
          <div class="mb-0">
            <label class="text-grey font-bold">Vai trò</label>
            <p class="mt-1 p-3 bg-white border rounded text-grey cursor-not-allowed">{{ user_info.role_name }}</p>
          </div>
        </div>
      </div>
      <vs-divider />
      <div class="vx-row">
        <div class="vx-col w-full flex justify-end">
          <vs-button color="dark" type="border" class="mr-3" @click="$router.push('/crm/staff')">Hủy</vs-button>
          <vs-button color="success" icon-pack="feather" icon="icon-save" @click="save">Cập nhật thông tin</vs-button>
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
      user_info: { id: null, name: '', email: '', phone: '', hrm_id: '', role_name: '', branch_name: '', manager_hrm_id: '', password: '' }
    }
  },
  methods: {
    getData() {
      const staffId = this.$route.params.id
      this.$vs.loading()
      axios.g(`/api/crm/staff/show/${staffId}`)
        .then((response) => {
          this.$vs.loading.close()
          if (response.data.status) {
            this.user_info = response.data.data
            this.$set(this.user_info, 'password', '') // Reset password field for input
          } else {
            this.$vs.notify({ title: 'Lỗi', text: response.data.message, color: 'danger' })
            this.$router.push('/crm/staff')
          }
        }).catch((e) => { 
          console.log(e);
          this.$vs.loading.close() 
        })
    },
    save() {
      if (!this.user_info.name || !this.user_info.phone) {
        return this.$vs.notify({ title: 'Lỗi', text: 'Vui lòng nhập đầy đủ các trường bắt buộc (*)', color: 'danger' })
      }
      this.$vs.loading()
      axios.p(`/api/crm/staff/update/${this.user_info.id}`, this.user_info)
        .then((response) => {
          this.$vs.loading.close()
          if (response.data.status) {
            this.$vs.notify({ title: 'Thành Công', text: response.data.message, color: 'success' })
            this.$router.push('/crm/staff')
          } else {
            this.$vs.notify({ title: 'Lỗi', text: response.data.message, color: 'danger' })
          }
        }).catch((e) => { 
          console.log(e);
          this.$vs.loading.close() 
        })
    }
  },
  created() { this.getData() }
}
</script>
