<template>
  <vx-card no-shadow>

    <!-- Img Row -->
    <div class="flex flex-wrap items-center mb-base">
      <vs-avatar :src="activeUserInfo.photoURL" size="70px" class="mr-4 mb-4" />
      <div>
        <label for="account-upload" class="vs-component vs-button vs-button-primary vs-button-filled mb-2">Upload Avatar</label>
        <input type="file" ref="file" multiple="multiple" id="account-upload" hidden accept="image/*" @change="submitFiles"/>
        <p class="text-sm mt-4">Allowed JPG, GIF or PNG. Max size of 800kB</p>
      </div>
    </div>

    <!-- Info -->
    <vs-input class="w-full mb-base" label-placeholder="Số điện thoại" v-model="phone"></vs-input>
    <vs-input class="w-full mb-base" label-placeholder="Họ tên" v-model="name"></vs-input>
    <vs-input class="w-full" label-placeholder="Email" v-model="email"></vs-input>
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
export default {
  data () {
    return {
      phone: this.$store.state.AppActiveUser.phone,
      name: this.$store.state.AppActiveUser.name,
      email: this.$store.state.AppActiveUser.email,
      alert: {
        status:'',
        show: false,
        message: ''
      }
    }
  },
  computed: {
    activeUserInfo () {
      console.log()
      return this.$store.state.AppActiveUser
    }
  },
  methods: {
    submitFiles() {
      if(this.$refs.file.files.length){
        this.$vs.loading()
        const formData = new FormData();
        for (var i = 0; i < this.$refs.file.files.length; i++) {
          let file = this.$refs.file.files[i];
          formData.append('files[' + i + ']', file);
        }
        axios.p('/api/user/upload-avatar', formData, {
            headers: {
              'Content-Type': 'multipart/form-data'
            },
          }).then((response) => {  
            if(response.data.status){
              this.$store.dispatch('updateUserInfo', response.data.userData)
            }
            this.$vs.loading.close()
            this.alert.show=true
            this.alert.status=response.data.status
            this.alert.message=response.data.message  
            this.alert.color = response.data.status ? 'success' : 'danger'
          })
        .catch((error)   => { console.log(error); this.$vs.loading.close(); })
      }
    },
    updateUser(){
      this.$vs.loading()
      axios.p('/api/user/update-info', {
        data: { 
          'name' : this.name,
          'email' : this.email
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
      this.phone = this.$store.state.AppActiveUser.phone
      this.name = this.$store.state.AppActiveUser.name
      this.email = this.$store.state.AppActiveUser.email
    }
  },
}
</script>
