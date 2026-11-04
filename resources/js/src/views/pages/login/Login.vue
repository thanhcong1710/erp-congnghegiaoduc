<template>
  <div id="page-login">
    <!-- Real STEMKey Classroom Background Image -->
    <div class="login-bg-image"></div>
    <div class="login-bg-overlay"></div>

    <!-- Background tech grid pattern -->
    <div class="login-grid-pattern"></div>

    <!-- Ambient glow orbs in STEMKey brand colors -->
    <div class="glow-orb glow-orb-1"></div>
    <div class="glow-orb glow-orb-2"></div>

    <!-- Login card -->
    <div class="login-card-container">
      <!-- Top brand accent stripe (STEMKey Blue -> Green) -->
      <div class="card-accent-stripe"></div>

      <!-- Logo & Brand Header -->
      <div style="text-align: center; margin-bottom: 24px;">
        <div style="
          display: inline-flex; align-items: center; justify-content: center;
          padding: 8px 18px; margin-bottom: 12px;
          background: #ffffff; border-radius: 14px;
          border: 1px solid #e2e8f0;
          box-shadow: 0 4px 14px rgba(0, 35, 105, 0.08);
        ">
          <img
            src="@assets/images/logo/stemkey-logo.png"
            alt="STEMKey Education"
            style="height: 56px; width: auto; max-width: 220px; object-fit: contain; display: block;"
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
      <div style="margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid #f1f5f9;">
        <h4 style="font-size: 17px; font-weight: 700; color: #0f172a; margin: 0 0 4px;">Đăng nhập hệ thống</h4>
        <p style="font-size: 13px; color: #64748b; margin: 0;">Cổng thông tin quản lý & đào tạo giáo dục STEMKey</p>
      </div>

      <!-- Form -->
      <login-jwt></login-jwt>

      <!-- Footer Info -->
      <div style="text-align: center; margin-top: 22px; padding-top: 14px; border-top: 1px solid #f1f5f9;">
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
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  background-color: #00133a;

  .login-bg-image {
    position: absolute;
    inset: 0;
    background-image: url('/images/pages/stemkey-login-bg.jpg');
    background-size: cover;
    background-position: center center;
    background-repeat: no-repeat;
    transform: scale(1.03);
    filter: brightness(0.9);
  }

  .login-bg-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(
      135deg,
      rgba(0, 19, 58, 0.78) 0%,
      rgba(0, 35, 105, 0.65) 50%,
      rgba(4, 45, 115, 0.75) 100%
    );
    backdrop-filter: blur(2px);
  }

  .login-grid-pattern {
    position: absolute;
    inset: 0;
    pointer-events: none;
    background-image: radial-gradient(circle, rgba(255, 255, 255, 0.12) 1px, transparent 1px);
    background-size: 32px 32px;
  }

  .glow-orb {
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
    filter: blur(50px);
  }

  .glow-orb-1 {
    top: -80px;
    left: -80px;
    width: 480px;
    height: 480px;
    background: radial-gradient(circle, rgba(36, 104, 214, 0.35), transparent 70%);
  }

  .glow-orb-2 {
    bottom: -80px;
    right: -60px;
    width: 460px;
    height: 460px;
    background: radial-gradient(circle, rgba(53, 182, 63, 0.28), transparent 70%);
  }

  .login-card-container {
    position: relative;
    z-index: 10;
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(20px);
    border-radius: 24px;
    padding: 38px 40px;
    width: 100%;
    max-width: 440px;
    margin: 20px;
    box-shadow: 0 30px 90px rgba(0, 15, 45, 0.55), 0 0 0 1px rgba(255, 255, 255, 0.35);
    overflow: hidden;
  }

  .card-accent-stripe {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #2468d6 0%, #35b63f 100%);
  }

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