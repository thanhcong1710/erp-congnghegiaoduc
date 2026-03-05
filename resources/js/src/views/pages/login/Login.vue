<template>
  <div id="page-login" style="
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #0d1424 0%, #1a3a7a 50%, #0d1424 100%);
    position: relative;
    overflow: hidden;
    font-family: 'Inter', 'Helvetica Neue', sans-serif;
  ">
    <!-- Background grid -->
    <div style="
      position: absolute; inset: 0; pointer-events: none;
      background-image: linear-gradient(rgba(37,99,235,.06) 1px, transparent 1px),
        linear-gradient(90deg, rgba(37,99,235,.06) 1px, transparent 1px);
      background-size: 60px 60px;
    "></div>

    <!-- Glow orbs -->
    <div style="position:absolute;top:-150px;left:-100px;width:500px;height:500px;border-radius:50%;background:radial-gradient(circle,rgba(37,99,235,.15),transparent 70%);pointer-events:none"></div>
    <div style="position:absolute;bottom:-150px;right:-100px;width:400px;height:400px;border-radius:50%;background:radial-gradient(circle,rgba(6,182,212,.12),transparent 70%);pointer-events:none"></div>

    <!-- Login card -->
    <div style="
      position: relative; z-index: 1;
      background: rgba(255,255,255,.97);
      border-radius: 24px;
      padding: 48px 44px;
      width: 100%; max-width: 440px;
      margin: 20px;
      box-shadow: 0 40px 120px rgba(0,0,0,.4), 0 0 0 1px rgba(37,99,235,.1);
    ">
      <!-- Logo -->
      <div style="text-align: center; margin-bottom: 32px;">
        <div style="
          width: 64px; height: 64px; border-radius: 18px;
          background: linear-gradient(135deg, #2563eb, #06b6d4);
          display: flex; align-items: center; justify-content: center;
          margin: 0 auto 12px;
          box-shadow: 0 8px 24px rgba(37,99,235,.35);
          font-size: 30px; font-weight: 900; color: white;
          font-family: 'Arial Black', sans-serif; letter-spacing: -1px;
        ">E</div>
        <div style="font-size: 22px; font-weight: 800; color: #1e293b; letter-spacing: -0.5px;">Edu-ERP</div>
        <div style="font-size: 13px; color: #64748b; margin-top: 2px;">Công Nghệ Giáo Dục</div>
      </div>

      <!-- Title -->
      <div style="margin-bottom: 24px;">
        <h4 style="font-size: 18px; font-weight: 700; color: #1e293b; margin: 0 0 4px;">Đăng nhập</h4>
        <p style="font-size: 13px; color: #94a3b8; margin: 0;">Chào mừng trở lại! Vui lòng đăng nhập vào tài khoản.</p>
      </div>

      <!-- Form -->
      <login-jwt></login-jwt>
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
#page-login {
  .vs-input--input {
    border-radius: 10px !important;
    border: 1.5px solid #e2e8f0 !important;
    padding: 12px 14px !important;
    font-size: 14px !important;
    transition: border-color .2s, box-shadow .2s !important;
    &:focus {
      border-color: #2563eb !important;
      box-shadow: 0 0 0 3px rgba(37,99,235,.12) !important;
    }
  }
  .vs-input--label {
    font-size: 13px !important;
    font-weight: 600 !important;
    color: #475569 !important;
  }
  .vs-button {
    border-radius: 10px !important;
    font-weight: 600 !important;
  }
  .vs-button--danger {
    background: linear-gradient(135deg, #ef4444, #dc2626) !important;
  }
}
</style>
