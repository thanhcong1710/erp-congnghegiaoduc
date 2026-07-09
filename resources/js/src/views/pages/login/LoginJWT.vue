<template>
  <div>
    <div class="mb-5">
      <label class="block text-lg text-slate-700 mb-2">Mã nhân viên</label>
      <vs-input
          name="hrm_id"
          icon-no-border
          icon="icon icon-user"
          icon-pack="feather"
          placeholder="Mã nhân viên"
          v-model="hrm_id"
          class="w-full custom-input-wrapper"/>
    </div>

    <div class="mb-6">
      <div class="flex items-center justify-between mb-2">
        <label class="block text-lg text-slate-700">Mật khẩu</label>
        <router-link to="/pages/forgot-password" class="text-base font-bold text-indigo-600 hover:text-indigo-800 transition-colors">Quên mật khẩu?</router-link>
      </div>
      <vs-input
          @keyup.enter="loginJWT"
          type="password"
          name="password"
          icon-no-border
          icon="icon icon-lock"
          icon-pack="feather"
          placeholder="••••••••"
          v-model="password"
          class="w-full custom-input-wrapper" />
    </div>

    <div v-if="submitError" class="mb-2 text-danger text-lg font-medium text-center">
      {{ submitError }}
    </div>
    <div class="flex flex-wrap justify-between mb-3 mt-4">
      <vs-button @click="loginJWT" class="w-full btn-login-custom">
        Đăng Nhập <i class="feather icon-arrow-right ml-2"></i>
      </vs-button>
    </div>

    <div class="flex flex-wrap justify-between mt-10 text-center" v-if="resendActive">
      Tài khoản chưa được kích hoạt, <router-link to="/pages/forgot-password">gửi lại mã kích hoạt</router-link>
    </div>
  </div>
</template>

<script>
export default {
  data () {
    return {
      hrm_id: '',
      password: '',
      checkbox_remember_me: false,
      resendActive: false,
      submitError: ''
    }
  },
  methods: {
    checkLogin () {
      // If user is already logged in notify
      if (this.$store.state.auth.isUserLoggedIn()) {

        // Close animation if passed as payload
        // this.$vs.loading.close()

        // this.$vs.notify({
        //   title: 'Login Attempt',
        //   text: 'You are already logged in!',
        //   iconPack: 'feather',
        //   icon: 'icon-alert-circle',
        //   color: 'warning'
        // })
        this.$router.push('/admin/dashboard')

        return false
      }
      return true
    },
    loginJWT () {
      this.submitError = '';
      if (!this.hrm_id) {
        this.submitError = 'Vui lòng nhập mã nhân viên';
        return;
      }
      if (!this.password) {
        this.submitError = 'Vui lòng nhập mật khẩu';
        return;
      }

      if (!this.checkLogin()) return

      // Loading
      this.$vs.loading()
      this.resendActive = false

      const payload = {
        checkbox_remember_me: this.checkbox_remember_me,
        userDetails: {
          hrm_id: this.hrm_id,
          password: this.password
        },
        redirect_url :  '/admin/dashboard'
      }

      this.$store.dispatch('auth/loginJWT', payload)
        .then(() => { this.$vs.loading.close() })
        .catch(error => {
          if(error.type == 'inactive'){
            this.resendActive = true
          }
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Lỗi',
            text: error.message,
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'danger'
          })
        })
    },
    registerUser () {
      if (!this.checkLogin()) return
      this.$router.push('/pages/register').catch(() => {})
    }
  }
}

</script>

