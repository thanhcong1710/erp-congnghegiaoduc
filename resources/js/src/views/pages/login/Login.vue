<template>
  <div id="page-login" style="
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #00133a 0%, #002369 45%, #072a6b 100%);
    position: relative;
    overflow: hidden;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  ">
    <!-- Background grid decoration -->
    <div style="
      position: absolute; inset: 0; pointer-events: none;
      background-image:
        radial-gradient(circle, rgba(36, 104, 214, 0.14) 1px, transparent 1px);
      background-size: 32px 32px;
    "></div>

    <!-- Ambient glow orbs in STEMKey brand colors (Blue & Green) -->
    <div style="position: absolute; top: -140px; left: -100px; width: 520px; height: 520px; border-radius: 50%; background: radial-gradient(circle, rgba(36, 104, 214, 0.25), transparent 70%); pointer-events: none;"></div>
    <div style="position: absolute; bottom: -120px; right: -80px; width: 480px; height: 480px; border-radius: 50%; background: radial-gradient(circle, rgba(53, 182, 63, 0.2), transparent 70%); pointer-events: none;"></div>
    <div style="position: absolute; top: 40%; right: 10%; width: 260px; height: 260px; border-radius: 50%; background: radial-gradient(circle, rgba(36, 104, 214, 0.12), transparent 70%); pointer-events: none;"></div>

    <!-- Login card -->
    <div style="
      position: relative; z-index: 1;
      background: rgba(255, 255, 255, 0.98);
      backdrop-filter: blur(20px);
      border-radius: 24px;
      padding: 42px 40px;
      width: 100%; max-width: 440px;
      margin: 20px;
      box-shadow: 0 30px 90px rgba(0, 19, 58, 0.45), 0 0 0 1px rgba(255, 255, 255, 0.2);
      overflow: hidden;
    ">
      <!-- Top brand accent stripe (STEMKey Blue -> Green) -->
      <div style="
        position: absolute; top: 0; left: 0; right: 0; height: 4px;
        background: linear-gradient(90deg, #2468d6 0%, #35b63f 100%);
      "></div>

      <!-- Logo & Brand Header -->
      <div style="text-align: center; margin-bottom: 26px;">
        <div style="
          display: inline-flex; align-items: center; justify-content: center;
          padding: 8px 16px; margin-bottom: 12px;
          background: #f8fafc; border-radius: 14px;
          border: 1px solid #e2e8f0;
        ">
          <img
            src="@assets/images/logo/stemkey-logo.png"
            alt="STEMKey Education"
            style="height: 54px; width: auto; max-width: 220px; object-fit: contain; display: block;"
          />
        </div>
        <div style="font-size: 20px; font-weight: 800; color: #002369; letter-spacing: -0.3px;">
          STEMKey Education
        </div>
        <div style="
          font-size: 11.5px; font-weight: 700; color: #35b63f;
          letter-spacing: 0.08em; text-transform: uppercase; margin-top: 3px;
        ">
          Tư Duy – Ngôn Ngữ – Công Nghệ
        </div>
      </div>

      <!-- Title & Subtitle -->
      <div style="margin-bottom: 22px; padding-bottom: 14px; border-bottom: 1px solid #f1f5f9;">
        <h4 style="font-size: 17px; font-weight: 700; color: #0f172a; margin: 0 0 4px;">Đăng nhập hệ thống</h4>
        <p style="font-size: 13px; color: #64748b; margin: 0;">Cổng thông tin quản lý & đào tạo giáo dục STEMKey</p>
      </div>

      <!-- Form -->
      <login-jwt></login-jwt>

      <!-- Footer Info -->
      <div style="text-align: center; margin-top: 24px; padding-top: 14px; border-top: 1px solid #f1f5f9;">
        <p style="font-size: 11.5px; color: #94a3b8; margin: 0;">
          © 2026 <a href="https://stemkey.edu.vn" target="_blank" rel="noopener noreferrer" style="color: #2468d6; text-decoration: none; font-weight: 600;">STEMKey Education</a>. All rights reserved.
        </p>
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
#page-login {
  .vs-input--input {
    border-radius: 10px !important;
    border: 1.5px solid #e2e8f0 !important;
    padding: 12px 14px !important;
    font-size: 14px !important;
    transition: border-color .2s, box-shadow .2s !important;
    &:focus {
      border-color: #2468d6 !important;
      box-shadow: 0 0 0 3px rgba(36, 104, 214, 0.14) !important;
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
    transition: all .2s ease !important;
  }
  .vs-button-primary.vs-button-filled,
  .vs-button:not(.vs-button-border):not(.vs-button--danger) {
    background: linear-gradient(135deg, #002369 0%, #2468d6 100%) !important;
    border: none !important;
    box-shadow: 0 4px 14px rgba(36, 104, 214, 0.28) !important;
    &:hover:not(:disabled) {
      box-shadow: 0 6px 20px rgba(36, 104, 214, 0.42) !important;
      transform: translateY(-1px);
    }
  }
  .vs-button-border {
    border-color: #2468d6 !important;
    color: #2468d6 !important;
    &:hover {
      background: rgba(36, 104, 214, 0.08) !important;
    }
  }
  .vs-checkbox--check {
    background: #2468d6 !important;
  }
  a {
    color: #2468d6;
    transition: color .2s;
    &:hover {
      color: #002369;
    }
  }
}
</style>