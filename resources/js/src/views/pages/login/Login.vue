<template>
  <div id="page-login">
    <!-- Real STEMKey 2026 Homepage Hero Background -->
    <div class="login-bg-image"></div>
    <div class="login-bg-overlay"></div>

    <!-- Soft glow orb on bottom right -->
    <div class="glow-orb glow-orb-stem"></div>

    <!-- Login card container - Aligned to left on PC -->
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
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow-x: hidden;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  padding: 20px;

  /* On desktop/PC: align login form towards the left side */
  @media (min-width: 992px) {
    justify-content: flex-start;
    padding-left: clamp(40px, 8vw, 130px);
  }

  .login-bg-image {
    position: absolute;
    inset: 0;
    background-image: url('/images/pages/stemkey-homepage-hero-2026.webp');
    background-size: cover;
    background-position: center right;
    background-repeat: no-repeat;
    filter: brightness(0.98);
  }

  /* Lớp phủ mờ nhẹ để thấy rõ ảnh nền */
  .login-bg-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(
      90deg,
      rgba(0, 20, 60, 0.28) 0%,
      rgba(0, 35, 105, 0.12) 45%,
      rgba(0, 0, 0, 0.04) 100%
    );
    pointer-events: none;
  }

  .glow-orb-stem {
    position: absolute;
    bottom: -100px;
    right: -80px;
    width: 480px;
    height: 480px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(53, 182, 63, 0.15), transparent 70%);
    pointer-events: none;
    filter: blur(50px);
  }

  .login-card-container {
    position: relative;
    z-index: 10;
    background: rgba(255, 255, 255, 0.96);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border-radius: 24px;
    padding: 38px 40px;
    width: 100%;
    max-width: 440px;
    box-shadow: 0 25px 70px rgba(0, 20, 60, 0.22), 0 0 0 1px rgba(255, 255, 255, 0.85);
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
    padding: 12px 14px 12px 34px !important;
    font-size: 14px !important;
    transition: border-color .2s, box-shadow .2s !important;
    background: #ffffff !important;
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
    background: transparent !important;
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