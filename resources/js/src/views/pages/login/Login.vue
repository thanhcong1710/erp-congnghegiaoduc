<!-- =========================================================================================
    File Name: Login.vue
    Description: Login Page
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
      Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
  <div class="h-screen flex w-full vx-row no-gutter items-center justify-center login-page-bg" id="page-login">
    <div class="vx-col sm:w-1/2 md:w-1/2 lg:w-3/4 xl:w-3/5 sm:m-0 m-4">
      <vx-card class="glass-card">
        <div slot="no-body" class="full-page-bg-color">

          <div class="vx-row no-gutter justify-center items-center">

            <div class="vx-col hidden lg:block lg:w-1/2 text-center p-8">
              <img src="@assets/images/pages/login.png" alt="login" class="mx-auto modern-login-img">
            </div>

            <div class="vx-col sm:w-full md:w-full lg:w-1/2 d-theme-dark-bg login-form-container">
              <div class="px-8 pt-10 pb-8 login-tabs-container">
                <div class="text-center mb-8">
                  <img src="@assets/images/logo/logo.png" alt="Logo" class="modern-logo">
                </div>

                <div class="vx-card__title mt-6 mb-8 text-center">
                  <h4 class="mb-2 font-bold text-2xl welcome-text">ĐĂNG NHẬP HỆ THỐNG</h4>
                  <p class="text-gray-500">Chào mừng trở lại! Vui lòng đăng nhập để tiếp tục.</p>
                </div>
                <login-jwt></login-jwt>
              </div>
            </div>
          </div>
        </div>
      </vx-card>
    </div>
  </div>
</template>


<script>
import LoginJwt from './LoginJWT.vue'

export default {
  components: {
    LoginJwt
  }
}
</script>

<style lang="scss">
.login-page-bg {
  background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
  background-size: cover;
}

.glass-card {
  background: rgba(255, 255, 255, 0.9) !important;
  backdrop-filter: blur(10px);
  border-radius: 20px;
  box-shadow: 0 20px 40px rgba(0,0,0,0.25) !important;
  overflow: hidden;
  border: 1px solid rgba(255,255,255,0.3);
}

.d-theme-dark-bg.login-form-container {
  background: #ffffff !important;
  border-left: 1px solid rgba(0,0,0,0.05);
}

.theme-dark .glass-card {
  background: rgba(30, 30, 30, 0.8) !important;
  border: 1px solid rgba(255,255,255,0.1);
}

.theme-dark .d-theme-dark-bg.login-form-container {
  background: rgba(40, 40, 40, 0.9) !important;
  border-left: 1px solid rgba(255,255,255,0.05);
}

.modern-logo {
  height: 85px;
  margin: auto;
  transition: transform 0.3s ease;
  &:hover {
    transform: scale(1.05);
  }
}

.modern-login-img {
  max-width: 90%;
  animation: float 6s ease-in-out infinite;
}

@keyframes float {
  0% { transform: translateY(0px); }
  50% { transform: translateY(-15px); }
  100% { transform: translateY(0px); }
}

.welcome-text {
  background: linear-gradient(to right, #1e3c72, #2a5298);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.login-tabs-container {
  min-height: 505px;
}
</style>
