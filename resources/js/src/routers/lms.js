
export default {
  router: {
    path: '/lms/students',
    component: () => import('@/layouts/main/Main.vue'),
    children: [
      {
        path: '/lms/checkin',
        name: 'lms-checkin',
        component: () => import('@/views/lms/checkin/index.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách checkin', active: true }
          ],
          pageTitle: 'Danh sách checkin',
          rule: 'editor'
        }
      },
      {
        path: '/lms/students',
        name: 'lms-students',
        component: () => import('@/views/lms/students/index.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách học sinh', active: true }
          ],
          pageTitle: 'Danh sách học sinh',
          rule: 'editor'
        }
      },
      {
        path: '/lms/students/:id/detail',
        name: 'lms-students-detail',
        component: () => import('@/views/lms/students/detail.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách học sinh', url: '/lms/students'},
            { title: 'Thông tin học sinh', active: true}
          ],
          pageTitle: 'Thông tin học sinh',
          rule: 'editor'
        }
      },

      {
        path: '/lms/contracts',
        name: 'lms-contracts',
        component: () => import('@/views/lms/contracts/index.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách nhập học', active: true }
          ],
          pageTitle: 'Danh sách nhập học',
          rule: 'editor'
        }
      },
      {
        path: '/lms/contracts/add',
        name: 'lms-contracts-add',
        component: () => import('@/views/lms/contracts/add.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách nhập học', url: '/lms/contracts' },
            { title: 'Thêm mới nhập học', active: true }
          ],
          pageTitle: 'Thêm mới nhập học',
          rule: 'editor'
        }
      },
      {
        path: '/lms/contracts/:id/detail',
        name: 'lms-contracts-detail',
        component: () => import('@/views/lms/contracts/detail.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách nhập học', url: '/lms/contracts'},
            { title: 'Thông tin nhập học', active: true}
          ],
          pageTitle: 'Thông tin nhập học',
          rule: 'editor'
        }
      },
      {
        path: '/lms/contracts/:id/edit',
        name: 'lms-contracts-edit',
        component: () => import('@/views/lms/contracts/edit.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách nhập học', url: '/lms/contracts'},
            { title: 'Cập nhật nhập học', active: true}
          ],
          pageTitle: 'Cập nhật nhập học',
          rule: 'editor'
        }
      },
      {
        path: '/lms/charges',
        name: 'lms-charges',
        component: () => import('@/views/lms/accounting/charges.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách đã thu phí', active: true}
          ],
          pageTitle: 'Danh sách đã thu phí',
          rule: 'editor'
        }
      },
      {
        path: '/lms/waitcharges',
        name: 'lms-waitcharges',
        component: () => import('@/views/lms/accounting/waitcharges.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách chờ đóng phí', active: true}
          ],
          pageTitle: 'Danh sách chờ đóng phí',
          rule: 'editor'
        }
      },
      {
        path: '/lms/waitcharge/:id/detail',
        name: 'lms-waitcharges-detail',
        component: () => import('@/views/lms/accounting/waitchargeDetail.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách chờ đóng phí', url: '/lms/waitcharges'},
            { title: 'Thêm mới phiếu thu', active: true}
          ],
          pageTitle: 'Thêm mới phiếu thu',
          rule: 'editor'
        }
      },
    ]
  },
}
