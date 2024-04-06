
export default {
  router: {
    path: '/user/profile',
    component: () => import('@/layouts/main/Main.vue'),
    children: [
      {
        path: '/user/profile',
        name: 'user-profile',
        component: () => import('@/views/users/UserSettings.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Tài khoản', active: true }
          ],
          pageTitle: 'Tài khoản',
          rule: 'editor'
        }
      },
    ]
  },
}
