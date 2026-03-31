<template>
  <div>
    <vs-input
        v-validate="'required'"
        data-vv-validate-on="blur"
        name="hrm_id"
        icon-no-border
        icon="icon icon-user"
        icon-pack="feather"
        label-placeholder="Mã nhân viên"
        v-model="hrm_id"
        class="w-full"/>
    <span class="text-danger text-sm">{{ errors.first('hrm_id') }}</span>

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

    <!-- QUICK LOGIN TEST ACCOUNTS -->
    <div style="margin-top: 20px; border-top: 1.5px dashed #e2e8f0; padding-top: 16px;">
      <p style="font-size: 11px; color: #94a3b8; text-align: center; margin-bottom: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em;">
        🔑 Tài khoản test nhanh
      </p>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 7px;">
        <button
          v-for="acc in testAccounts"
          :key="acc.role"
          @click="quickLogin(acc)"
          :style="{
            display: 'flex', alignItems: 'center', justifyContent: 'center', gap: '5px',
            padding: '9px 10px', borderRadius: '10px', border: '1.5px solid',
            borderColor: acc.border, background: acc.bg,
            color: acc.text, fontWeight: '600', fontSize: '12px',
            cursor: 'pointer', transition: 'all .15s'
          }"
        >
          <span>{{ acc.icon }}</span> {{ acc.label }}
        </button>
      </div>
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
      testAccounts: [
        { role: 'admin', label: 'Admin',       icon: '👑', color: 'danger',  hrm_id: 'Admin', password: 'abcd1234',  bg: '#fef2f2', border: '#fca5a5', text: '#dc2626' },
        { role: 'cskh',  label: 'CSKH',        icon: '💬', color: 'primary', hrm_id: 'TH004',  password: '@12345678', bg: '#eff6ff', border: '#93c5fd', text: '#2563eb' },
        { role: 'sale',  label: 'Sale',        icon: '🎯', color: 'success', hrm_id: 'HD002',  password: '@12345678', bg: '#f0fdf4', border: '#86efac', text: '#16a34a' },
        { role: 'gdtt',  label: 'Giám đốc TT', icon: '🏫', color: 'warning', hrm_id: 'HD001',  password: '@12345678', bg: '#fffbeb', border: '#fcd34d', text: '#b45309' }
      ]
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
    },
    quickLogin (acc) {
      this.hrm_id = acc.hrm_id
      this.password = acc.password
      this.$nextTick(() => { this.loginJWT() })
    }
  }
}

</script>

