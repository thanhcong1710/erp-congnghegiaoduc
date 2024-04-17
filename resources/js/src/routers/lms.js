
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
    ]
  },
}
