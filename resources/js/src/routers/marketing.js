
export default {
  router: {
    path: '/marketing/campaigns',
    component: () => import('@/layouts/main/Main.vue'),
    children: [
      {
        path: '/marketing/campaigns',
        name: 'marketing-campaigns',
        component: () => import('@/views/marketing/campaigns/index.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách chiến dịch', active: true }
          ],
          pageTitle: 'Danh sách chiến dịch',
          rule: 'editor'
        }
      },
      {
        path: '/marketing/campaigns/add',
        name: 'marketing-campaigns-add',
        component: () => import('@/views/marketing/campaigns/add.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách chiến dịch', url: '/marketing/campaigns' },
            { title: 'Thêm mới', active: true }
          ],
          pageTitle: 'Thêm mới chiến dịch',
          rule: 'editor'
        }
      },
      {
        path: '/marketing/campaigns/edit/:id',
        name: 'marketing-campaigns-edit',
        component: () => import('@/views/marketing/campaigns/edit.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách chiến dịch', url: '/marketing/campaigns' },
            { title: 'Cập nhật', active: true }
          ],
          pageTitle: 'Cập nhật chiến dịch',
          rule: 'editor'
        }
      },
      {
        path: '/marketing/campaigns/detail/:id',
        name: 'marketing-campaigns-detail',
        component: () => import('@/views/marketing/campaigns/detail.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách chiến dịch', url: '/marketing/campaigns' },
            { title: 'Thông tin chi tiết', active: true }
          ],
          pageTitle: 'Chi tiết chiến dịch',
          rule: 'editor'
        }
      },
      {
        path: '/marketing/coupons',
        name: 'marketing-coupons',
        component: () => import('@/views/marketing/coupons/index.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách voucher', active: true }
          ],
          pageTitle: 'Danh sách voucher',
          rule: 'editor'
        }
      },
      {
        path: '/marketing/b2b/sources',
        name: 'marketing-b2b-sources',
        component: () => import('@/views/marketing/b2b/sources/index.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách đối tác B2B', active: true }
          ],
          pageTitle: 'Danh sách đối tác B2B',
          rule: 'editor'
        }
      },
      {
        path: '/marketing/b2b/sources/add',
        name: 'marketing-b2b-sources-add',
        component: () => import('@/views/marketing/b2b/sources/add.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách đối tác B2B', url: '/marketing/b2b/sources' },
            { title: 'Thêm mới', active: true }
          ],
          pageTitle: 'Thêm mới đối tác B2B',
          rule: 'editor'
        }
      },
      {
        path: '/marketing/b2b/sources/edit/:id',
        name: 'marketing-b2b-sources-edit',
        component: () => import('@/views/marketing/b2b/sources/edit.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách đối tác B2B', url: '/marketing/b2b/sources' },
            { title: 'Cập nhật', active: true }
          ],
          pageTitle: 'Cập nhật đối tác B2B',
          rule: 'editor'
        }
      }
    ]
  },
}
