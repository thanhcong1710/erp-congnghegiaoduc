<template>
  <div>
    <vs-input
        v-validate="'required|email|min:3'"
        data-vv-validate-on="blur"
        name="email"
        icon-no-border
        icon="icon icon-user"
        icon-pack="feather"
        label-placeholder="Email"
        v-model="email"
        class="w-full"/>
    <span class="text-danger text-sm">{{ errors.first('email') }}</span>

    <vs-input
        @keyup.enter="loginJWT"
        data-vv-validate-on="blur"
        v-validate="'required|min:6|max:10'"
        type="password"
        name="password"
        icon-no-border
        icon="icon icon-lock"
        icon-pack="feather"
        label-placeholder="Mật khẩu"
        v-model="password"
        class="w-full mt-6" />
    <span class="text-danger text-sm">{{ errors.first('password') }}</span>

    <div class="flex flex-wrap justify-between my-5">
        <vs-checkbox v-model="checkbox_remember_me" class="mb-3">Lưu mật khẩu</vs-checkbox>
        <router-link to="/pages/forgot-password">Quên mật khẩu?</router-link>
    </div>
    <div class="flex flex-wrap justify-between mb-3">
      <vs-button  type="border" @click="registerUser">Đăng ký</vs-button>
      <vs-button :disabled="!validateForm" @click="loginJWT">Đăng nhập</vs-button>
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
      email: '',
      password: '',
      checkbox_remember_me: false,
      resendActive: false
    }
  },
  computed: {
    validateForm () {
      return !this.errors.any() && this.phone !== '' && this.password !== ''
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
      if (!this.checkLogin()) return

      // Loading
      this.$vs.loading()
      this.resendActive = false

      const payload = {
        checkbox_remember_me: this.checkbox_remember_me,
        userDetails: {
          email: this.email,
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

