
export default {
  router: {
    path: '/crm/parents',
    component: () => import('@/layouts/main/Main.vue'),
    children: [
      {
        path: '/crm/imports',
        name: 'crm-imports',
        component: () => import('@/views/crm/imports/index.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách Import', active: true }
          ],
          pageTitle: 'Danh sách Import',
          rule: 'editor'
        }
      },
      {
        path: '/crm/imports/add',
        name: 'crm-imports-add',
        component: () => import('@/views/crm/imports/add.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách Import', url: '/crm/imports' },
            { title: 'Import', active: true }
          ],
          pageTitle: 'Import',
          rule: 'editor'
        }
      },
    ]
  },
}
