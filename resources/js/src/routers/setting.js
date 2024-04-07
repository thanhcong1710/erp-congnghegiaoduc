
export default {
  router: {
    path: '/settings',
    component: () => import('@/layouts/main/Main.vue'),
    children: [
      {
        path: '/settings/roles',
        name: 'roles',
        component: () => import('@/views/settings/roles/index.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Chức vụ', active: true }
          ],
          pageTitle: 'Chức vụ',
          rule: 'editor'
        }
      },
      {
        path: '/settings/permissions/:role_id',
        name: 'permissions',
        component: () => import('@/views/settings/roles/permissions.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Phân quyền', active: true }
          ],
          pageTitle: 'Phân quyền',
          rule: 'editor'
        }
      },
    ]
  },
}
