<!-- =========================================================================================
    File Name: Login.vue
    Description: Login Page matching crm.congnghegiaoduc.com
========================================================================================== -->

<template>
  <div class="h-screen flex w-full items-center justify-center crm-login-bg relative overflow-hidden" id="page-login">
    <!-- Animated particles similar to congnghegiaoduc.com hero -->
    <div class="hero-particles">
      <!-- Các chấm sáng màu sắc (Bokeh) tĩnh hoặc di chuyển chậm -->
      <div class="particle-glow" style="left: 15%; top: 20%; width: 25px; height: 25px; background: #60a5fa; filter: blur(3px);"></div>
      <div class="particle-glow" style="left: 25%; top: 60%; width: 35px; height: 35px; background: #f59e0b; filter: blur(5px);"></div>
      <div class="particle-glow" style="left: 75%; top: 30%; width: 28px; height: 28px; background: #10b981; filter: blur(4px);"></div>
      <div class="particle-glow" style="left: 85%; top: 70%; width: 18px; height: 18px; background: #c084fc; filter: blur(2px);"></div>
      <div class="particle-glow" style="left: 45%; top: 80%; width: 40px; height: 40px; background: #3b82f6; filter: blur(6px);"></div>
      <div class="particle-glow" style="left: 55%; top: 10%; width: 22px; height: 22px; background: #f59e0b; filter: blur(3px);"></div>
      <div class="particle-glow" style="left: 5%; top: 40%; width: 16px; height: 16px; background: #e879f9; filter: blur(2px);"></div>
      <div class="particle-glow" style="left: 95%; top: 50%; width: 30px; height: 30px; background: #34d399; filter: blur(4px);"></div>
      
      <!-- Mảng giả lập các particle bụi lơ lửng, nhiều hơn và to hơn -->
      <div class="particle" style="left: 10%; animation-duration: 15s; width: 16px; height: 16px; background: rgba(255,255,255,0.7);"></div>
      <div class="particle" style="left: 25%; animation-duration: 25s; width: 28px; height: 28px; background: rgba(255,255,255,0.4);"></div>
      <div class="particle" style="left: 40%; animation-duration: 18s; width: 20px; height: 20px; background: rgba(255,255,255,0.6);"></div>
      <div class="particle" style="left: 55%; animation-duration: 35s; width: 40px; height: 40px; background: rgba(255,255,255,0.2);"></div>
      <div class="particle" style="left: 70%; animation-duration: 20s; width: 22px; height: 22px; background: rgba(255,255,255,0.5);"></div>
      <div class="particle" style="left: 85%; animation-duration: 30s; width: 36px; height: 36px; background: rgba(255,255,255,0.3);"></div>
      <div class="particle" style="left: 95%; animation-duration: 22s; width: 14px; height: 14px; background: rgba(255,255,255,0.8);"></div>
    </div>

    <!-- Grid lines -->
    <div class="hero-grid"></div>

    <div class="crm-login-card relative z-10">
      
      <!-- Cột bên trái (Branding) -->
      <div class="crm-login-left">
        <div class="relative z-10 h-full flex flex-col justify-between">
          <div class="brand-header flex items-center gap-3">
            <div class="logo-box">
              <img src="@assets/images/logo/logo.png" alt="Logo" class="max-w-full max-h-full">
            </div>
            <span class="brand-name">Scots English</span>
          </div>

          <div class="brand-content mt-12 mb-12">
            <h2 class="slogan">Nền tảng quản trị<br>tiên phong.</h2>
            <p class="sub-slogan" style="font-size: 16px;">Hệ thống thông minh giúp tối ưu hóa vận hành và nâng cao hiệu quả công việc.</p>
          </div>

          <div class="brand-footer">
            <div class="system-version">Hệ thống quản lý nội bộ</div>
          </div>
        </div>
      </div>

      <!-- Cột bên phải (Form) -->
      <div class="crm-login-right">
        
        <!-- Logo mobile -->
        <div class="mobile-logo-wrap">
          <div class="logo-box-mobile">
            <img src="@assets/images/logo/logo.png" alt="Logo" class="max-w-full max-h-full">
          </div>
          <span class="brand-name-mobile">Scots English</span>
        </div>

        <div class="form-header">
          <h1 class="title">Đăng nhập tài khoản 👋</h1>
          <p class="subtitle">Truy cập vào hệ thống để bắt đầu ngày làm việc hiệu quả.</p>
        </div>

        <div class="form-body">
          <login-jwt></login-jwt>
        </div>

      </div>
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
/* Utility colors */
$indigo-600: #4f46e5;
$indigo-700: #4338ca;
$slate-800: #1e293b;
$slate-900: #0f172a;
$slate-700: #334155;
$slate-500: #64748b;
$slate-200: #e2e8f0;

.crm-login-bg {
  font-family: 'Inter', sans-serif;
  background: radial-gradient(ellipse 150% 100% at 50% 0%, #17326b 0%, #0d1b3e 40%, #050a1a 80%);
}

/* Animated particles */
.hero-particles {
  position: absolute;
  inset: 0;
  pointer-events: none;
}
.particle-glow {
  position: absolute;
  border-radius: 50%;
  animation: pulse-glow 8s ease-in-out infinite alternate;
}
@keyframes pulse-glow {
  0% { transform: scale(1) translateY(0); opacity: 0.6; }
  100% { transform: scale(1.2) translateY(-20px); opacity: 1; }
}

.particle {
  position: absolute;
  border-radius: 50%;
  animation: float-particle linear infinite;
  opacity: 0;
  filter: blur(1.5px);
}
@keyframes float-particle {
  0% { transform: translateY(100vh) scale(0.5); opacity: 0; }
  10% { opacity: 0.8; }
  90% { opacity: 0.4; }
  100% { transform: translateY(-20vh) scale(1.5); opacity: 0; }
}

/* Grid lines overlay */
.hero-grid {
  position: absolute;
  inset: 0;
  background-image: 
    linear-gradient(to right, rgba(255,255,255,0.05) 1px, transparent 1px),
    linear-gradient(to bottom, rgba(255,255,255,0.05) 1px, transparent 1px);
  background-size: 40px 40px;
  mask-image: radial-gradient(circle at center, black, transparent 80%);
  -webkit-mask-image: radial-gradient(circle at center, black, transparent 80%);
  pointer-events: none;
}

.crm-login-card {
  display: flex;
  width: 100%;
  max-width: 1024px;
  min-height: 600px;
  background: #ffffff;
  border-radius: 32px;
  box-shadow: 0 25px 50px -12px rgba(79, 70, 229, 0.15);
  margin: 1rem;
  overflow: hidden;
  
  @media (max-width: 768px) {
    flex-direction: column;
  }
}

.crm-login-left {
  flex: 5;
  background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
  padding: 3rem;
  color: #ffffff;
  position: relative;
  
  @media (max-width: 768px) {
    display: none;
  }
}

.crm-login-right {
  flex: 7;
  padding: 3.5rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
  background: #ffffff;
  
  @media (max-width: 768px) {
    padding: 2rem;
  }
}

.logo-box {
  width: 72px;
  height: 72px;
  background: #ffffff;
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 8px;
  box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
}

.brand-name {
  font-size: 2rem;
  font-weight: 700;
  letter-spacing: -0.025em;
}

.slogan {
  font-size: 2.25rem;
  font-weight: 800;
  line-height: 1.2;
  margin-bottom: 1.5rem;
}

.sub-slogan {
  color: rgba(255,255,255,0.9);
  line-height: 1.625;
  font-size: 1rem;
  max-width: 320px;
}

.system-version {
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: rgba(199, 210, 254, 1);
  padding-left: 0.75rem;
  border-left: 2px solid rgba(129, 140, 248, 1);
}

.mobile-logo-wrap {
  display: none;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 2rem;
  
  @media (max-width: 768px) {
    display: flex;
  }
}

.logo-box-mobile {
  width: 56px;
  height: 56px;
  background: #ffffff;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 6px;
  border: 1px solid #e2e8f0;
}

.brand-name-mobile {
  font-size: 1.75rem;
  font-weight: 700;
  color: $slate-800;
  letter-spacing: -0.025em;
}

.form-header {
  margin-bottom: 2rem;
}

.form-header .title {
  font-size: 2.6rem;
  font-weight: 800;
  color: $slate-900;
  margin-bottom: 0.75rem;
  letter-spacing: -0.025em;
}

.form-header .subtitle {
  color: $slate-500;
  font-size: 1.15rem;
}

/* Custom styling for Vuexy vs-input to match Tailwind */
.vs-input--input.normal {
  border-radius: 12px !important;
  border: 1px solid $slate-200 !important;
  padding: 18px 20px !important;
  background: #ffffff !important;
  color: $slate-700 !important;
  font-weight: 600 !important;
  font-size: 1.25rem !important;
  transition: all 0.2s ease;
  width: 100% !important;
  text-align: left !important;
  
  &:focus {
    border-color: $indigo-600 !important;
    box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1) !important;
  }
  
  &::placeholder {
    color: #94a3b8 !important;
  }
}

.vs-input--icon {
  display: none !important;
}

.vs-input {
  margin-bottom: 1.25rem !important;
}

.vs-input--label {
  display: block;
  font-size: 1.15rem !important;
  font-weight: 700 !important;
  color: $slate-700 !important;
  margin-bottom: 0.75rem !important;
  padding-left: 0 !important;
}

.vs-button {
  border-radius: 12px !important;
  font-weight: 800 !important;
  font-size: 1.35rem !important;
  padding: 20px 24px !important;
  background: $indigo-600 !important;
  color: white !important;
  width: 100%;
  transition: all 0.2s ease !important;
  box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3) !important;
  
  &:hover {
    background: $indigo-700 !important;
    transform: scale(0.98);
  }
}

.vs-button-border, .vs-button--border {
  background: transparent !important;
  color: $indigo-600 !important;
  border: 1.5px solid $indigo-600 !important;
  box-shadow: none !important;
  
  &:hover {
    background: rgba(79, 70, 229, 0.05) !important;
    color: $indigo-700 !important;
  }
}

.text-danger {
  display: block;
  margin-top: -10px;
  margin-bottom: 15px;
  font-size: 0.85rem;
  color: #ef4444;
}
</style>
