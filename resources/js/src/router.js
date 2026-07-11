/*=========================================================================================
  File Name: router.js
  Description: Routes for vue-router. Lazy loading is enabled.
  Object Strucutre:
                    path => router path
                    name => router name
                    component(lazy loading) => component to load
                    meta : {
                      rule => which user can have access (ACL)
                      breadcrumb => Add breadcrumb to specific page
                      pageTitle => Display title besides breadcrumb
                    }
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/


import Vue from 'vue'
import Router from 'vue-router'
import auth from '@/auth/authService'
import User from './routers/user'
import Setting from './routers/setting'
import CRM from './routers/crm'
import LMS from './routers/lms'
import Marketing from './routers/marketing'
Vue.use(Router)

const router = new Router({
  mode: 'history',
  base: '/',
  scrollBehavior () {
    return { x: 0, y: 0 }
  },
  routes: [
    User.router,
    Setting.router,
    CRM.router,
    LMS.router,
    Marketing.router,
    {
    // =============================================================================
    // MAIN LAYOUT ROUTES
    // =============================================================================
      path: '',
      component: () => import('./layouts/main/Main.vue'),
      children: [
        // =============================================================================
        // Theme Routes
        // =============================================================================
        {
          path: '/',
          redirect: '/admin/dashboard'
        },
        {
          path: '/admin/index',
          redirect: '/admin/dashboard'
        },
        {
          path: '/admin/dashboard',
          name: 'admin-dashboard',
          component: () => import('./views/DashboardAnalytics.vue'),
          meta: { rule: 'editor' }
        },
        {
          path: '/admin/dashboard/report',
          name: 'dashboard-detail-report',
          component: () => import('./views/DashboardDetailReport.vue'),
          meta: { rule: 'editor', pageTitle: 'Chi tiết Dashboard' }
        },
        {
          path: '/loyalty/dashboard',
          name: 'loyalty-dashboard',
          component: () => import('./views/loyalty/LoyaltyDashboard.vue'),
          meta: { rule: 'editor', pageTitle: 'Dashboard Thống kê' }
        },
        {
          path: '/loyalty/history',
          name: 'loyalty-history',
          component: () => import('./views/loyalty/History.vue'),
          meta: { rule: 'editor', pageTitle: 'Lịch sử tích/tiêu điểm' }
        },
        {
          path: '/loyalty/member-ranks',
          name: 'loyalty-member-ranks',
          component: () => import('./views/loyalty/MemberRanks.vue'),
          meta: { rule: 'editor', pageTitle: 'Quản lý hạng thành viên' }
        },
        {
          path: '/loyalty/rules',
          name: 'loyalty-rules',
          component: () => import('./views/loyalty/Rules.vue'),
          meta: { rule: 'editor', pageTitle: 'Quy tắc tích điểm' }
        },
        {
          path: '/loyalty/point-conversion',
          name: 'loyalty-point-conversion',
          component: () => import('./views/loyalty/PointConversion.vue'),
          meta: { rule: 'editor', pageTitle: 'Cấu hình quy đổi điểm' }
        },
        {
          path: '/loyalty/vouchers',
          name: 'loyalty-vouchers',
          component: () => import('./views/loyalty/Vouchers.vue'),
          meta: { rule: 'editor', pageTitle: 'Danh sách Voucher' }
        },
        {
          path: '/hr/profiles',
          name: 'hr-profiles',
          component: () => import('./views/hr/Profiles.vue'),
          meta: { rule: 'editor', pageTitle: 'Quản lý Hồ sơ Nhân sự' }
        },
        {
          path: '/hr/recruitment',
          name: 'hr-recruitment',
          component: () => import('./views/hr/Recruitment.vue'),
          meta: { rule: 'editor', pageTitle: 'Tuyển dụng & Phỏng vấn' }
        },
        {
          path: '/hr/kpi',
          name: 'hr-kpi',
          component: () => import('./views/hr/KPI.vue'),
          meta: { rule: 'editor', pageTitle: 'Đánh giá KPI & OKR' }
        },
        {
          path: '/hr/approvals',
          name: 'hr-approvals',
          component: () => import('./views/hr/Approvals.vue'),
          meta: { rule: 'editor', pageTitle: 'Quy trình phê duyệt' }
        },
        {
          path: '/hr/talent',
          name: 'hr-talent',
          component: () => import('./views/hr/Talent.vue'),
          meta: { rule: 'editor', pageTitle: 'Vinh danh Talent' }
        },
        {
          path: '/rate/ratings',
          name: 'rate-ratings',
          component: () => import('./views/rate/RatingsList.vue'),
          meta: { rule: 'editor', pageTitle: 'Danh sách Đánh giá' }
        },
        {
          path: '/rate/csat-report',
          name: 'rate-csat',
          component: () => import('./views/rate/CSATReport.vue'),
          meta: { rule: 'editor', pageTitle: 'Báo cáo CSAT' }
        },
        
        // =============================================================================
        // LAW MAIN PAGE LAYOUTS
        // =============================================================================
        
      ]
    },
    // =============================================================================
    // FULL PAGE LAYOUTS
    // =============================================================================
    {
      path: '',
      component: () => import('@/layouts/full-page/FullPage.vue'),
      children: [
        {
          path: '/pages/print/receipt/:id',
          name: 'print-receipt',
          component: () => import('@/views/pages/print/receipt.vue'),
          meta: {
            rule: 'editor'
          }
        },
        {
          path: '/pages/print/contract/:id',
          name: 'print-contract',
          component: () => import('@/views/pages/print/contract.vue'),
          meta: {
            rule: 'editor'
          }
        },
        {
          path: '/pages/print/contract-renew/:id',
          name: 'print-contract',
          component: () => import('@/views/pages/print/contract_renew.vue'),
          meta: {
            rule: 'editor'
          }
        },
        {
          path: '/pages/print/tuition_transfer/:id',
          name: 'print-tuition-transfer',
          component: () => import('@/views/pages/print/tuition_transfer.vue'),
          meta: {
            rule: 'editor'
          }
        },
        {
          path: '/pages/print/reserve/:id',
          name: 'print-reserve',
          component: () => import('@/views/pages/print/reserve.vue'),
          meta: {
            rule: 'editor'
          }
        },
        // =============================================================================
        // PAGES
        // =============================================================================
        {
          path: '/pages/login',
          name: 'page-login',
          component: () => import('@/views/pages/login/Login.vue'),
          meta: {
            rule: 'editor'
          }
        },
        {
          path: '/pages/register',
          name: 'page-register',
          component: () => import('@/views/pages/register/Register.vue'),
          meta: {
            rule: 'editor'
          }
        },
        {
          path: '/pages/active-account',
          name: 'page-active-account',
          component: () => import('@/views/pages/ActiveAccount.vue'),
          meta: {
            rule: 'editor'
          }
        },
        {
          path: '/pages/forgot-password',
          name: 'page-forgot-password',
          component: () => import('@/views/pages/ForgotPassword.vue'),
          meta: {
            rule: 'editor'
          }
        },
        {
          path: '/pages/reset-password',
          name: 'page-reset-password',
          component: () => import('@/views/pages/ResetPassword.vue'),
          meta: {
            rule: 'editor'
          }
        },

        {
          path: '/callback',
          name: 'auth-callback',
          component: () => import('@/views/Callback.vue'),
          meta: {
            rule: 'editor'
          }
        },
        {
          path: '/pages/lock-screen',
          name: 'page-lock-screen',
          component: () => import('@/views/pages/LockScreen.vue'),
          meta: {
            rule: 'editor'
          }
        },
        {
          path: '/pages/comingsoon',
          name: 'page-coming-soon',
          component: () => import('@/views/pages/ComingSoon.vue'),
          meta: {
            rule: 'editor'
          }
        },
        {
          path: '/pages/error-404',
          name: 'page-error-404',
          component: () => import('@/views/pages/Error404.vue'),
          meta: {
            rule: 'editor'
          }
        },
        {
          path: '/pages/error-500',
          name: 'page-error-500',
          component: () => import('@/views/pages/Error500.vue'),
          meta: {
            rule: 'editor'
          }
        },
        {
          path: '/pages/not-authorized',
          name: 'page-not-authorized',
          component: () => import('@/views/pages/NotAuthorized.vue'),
          meta: {
            rule: 'editor'
          }
        },
        {
          path: '/pages/maintenance',
          name: 'page-maintenance',
          component: () => import('@/views/pages/Maintenance.vue'),
          meta: {
            rule: 'editor'
          }
        },
        // =============================================================================
        // LAW FULL PAGE LAYOUTS
        // =============================================================================
      ]
    },
    // Redirect to 404 page, if no match found
    {
      path: '*',
      redirect: '/pages/error-404'
    },
  ]
})

router.afterEach(() => {
  // Remove initial loading
  const appLoading = document.getElementById('loading-bg')
  if (appLoading) {
    appLoading.style.display = 'none'
  }
})

router.beforeEach((to, from, next) => {
  if (to.meta.authRequired) {
    if (!(auth.isAuthenticated() || firebaseCurrentUser)) {
      router.push({ path: '/pages/login', query: { to: to.path } })
    }
  }

  return next()
})

export default router
