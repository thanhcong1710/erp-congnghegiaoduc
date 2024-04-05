<template>
  <vx-card no-shadow>

    <!-- Bio -->
    

    <!-- DOB -->
    <div class="mt-5">
      <label class="text-sm">Ngày sinh</label>
      <flat-pickr v-model="birthday" :config="{ dateFormat: 'd/m/Y' }" class="w-full mt-2" />
    </div>
    <!-- Gender -->
    <div class="mt-5">
      <label class="text-sm">Giới tính</label>
      <div class="mt-2">
        <vs-radio v-model="gender" vs-value="male" class="mr-4">Nam</vs-radio>
        <vs-radio v-model="gender" vs-value="female" class="mr-4">Nữ</vs-radio>
        <vs-radio v-model="gender" vs-value="other">Khác</vs-radio>
      </div>
    </div>
    <div class="mt-5">
      <label class="text-sm">Địa chỉ</label>
      <vs-input class="w-full mt-2" label-placeholder="" v-model="address" />
    </div>
    <div class="mt-5">
      <label class="text-sm">Giới thiệu</label>
      <vs-textarea class="w-full mt-2" v-model="note" placeholder="" />
    </div>
     <vs-alert :active.sync="alert.show" :color="alert.color" class="mt-4 mb-4"  closable icon-pack="feather" close-icon="icon-x">
      {{alert.message}}
    </vs-alert>
    <!-- Save & Reset Button -->
    <div class="flex flex-wrap items-center justify-end mt-4">
      <vs-button class="ml-auto mt-2" @click="updateUser()">Cập nhật</vs-button>
      <vs-button class="ml-4 mt-2" type="border" @click="reset()" color="warning">Hủy</vs-button>
    </div>
  </vx-card>
</template>

<script>
import axios from './../../http/axios.js'
import flatPickr from 'vue-flatpickr-component'
import 'flatpickr/dist/flatpickr.css'
import vSelect from 'vue-select'

export default {
  components: {
    flatPickr,
    vSelect
  },
  data () {
    return {
      note: this.$store.state.AppActiveUser.note,
      birthday: this.$store.state.AppActiveUser.birthday,
      address: this.$store.state.AppActiveUser.address,
      gender: this.$store.state.AppActiveUser.gender,
      alert: {
        status:'',
        show: false,
        message: ''
      }
    }
  },
  computed: {
    activeUserInfo () {
      return this.$store.state.AppActiveUser
    }
  },
  methods: {
    updateUser(){
      this.$vs.loading()
      axios.p('/api/user/update-info', {
        data: { 
          'note' : this.note,
          'birthday' : this.birthday,
          'address': this.address,
          'gender': this.gender
        }
      })
      .then((response) => {  
        this.$store.dispatch('updateUserInfo', response.data.userData)
        this.$vs.loading.close()
        this.alert.show=true
        this.alert.status=response.data.status
        this.alert.message=response.data.message  
        this.alert.color = response.data.status ? 'success' : 'danger'
      })
      .catch((error)   => { console.log(error); this.$vs.loading.close(); })
    },
    reset(){
      this.note = this.$store.state.AppActiveUser.note
      this.birthday = this.$store.state.AppActiveUser.birthday
      this.address = this.$store.state.AppActiveUser.address
      this.gender = this.$store.state.AppActiveUser.gender
    }
  }
}
</script>
