
export default {
  router: {
    path: '/crm/parent',
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
      {
        path: '/crm/care',
        name: 'crm-care',
        component: () => import('@/views/crm/care/index.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'QL chăm sóc', active: true }
          ],
          pageTitle: 'QL chăm sóc',
          rule: 'editor'
        }
      },
      {
        path: '/crm/parent',
        name: 'crm-parent',
        component: () => import('@/views/crm/parents/index.vue'),
        beforeEnter: (to, from, next) => {
          if (to.query.view !== 'overview') {
            next({ path: '/crm/parent/detail-list' });
          } else {
            next();
          }
        },
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách khách hàng', active: true }
          ],
          pageTitle: 'Danh sách khách hàng',
          rule: 'editor'
        }
      },
      {
        path: '/crm/parent/detail-list',
        name: 'crm-parent-detail-list',
        component: () => import('@/views/crm/parents/detail_list.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách khách hàng (Chi tiết)', active: true }
          ],
          pageTitle: 'Danh sách khách hàng (Chi tiết)',
          rule: 'editor'
        }
      },
      {
        path: '/crm/parent/add',
        name: 'crm-parent-add',
        component: () => import('@/views/crm/parents/add.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách khách hàng',  url: '/crm/parent' },
            { title: 'Thêm mới', active: true }
          ],
          pageTitle: 'Thêm mới khách hàng',
          rule: 'editor'
        }
      },
      {
        path: '/crm/parent/:id/detail',
        name: 'crm-parent-edit',
        component: () => import('@/views/crm/parents/detail.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách khách hàng',  url: '/crm/parent' },
            { title: 'Chi tiết', active: true }
          ],
          pageTitle: 'Chi tiết khách hàng',
          rule: 'editor'
        }
      },
    ]
  },
}
