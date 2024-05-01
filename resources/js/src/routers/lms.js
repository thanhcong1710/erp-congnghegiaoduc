
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
      {
        path: '/lms/enrolments',
        name: 'lms-enrolments',
        component: () => import('@/views/lms/enrolments/index.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Xếp lớp', active: true}
          ],
          pageTitle: 'Xếp lớp',
          rule: 'editor'
        }
      },
      {
        path: '/lms/reserves',
        name: 'lms-reserves',
        component: () => import('@/views/lms/reserves/index.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách bảo lưu', active: true}
          ],
          pageTitle: 'Danh sách bảo lưu',
          rule: 'editor'
        }
      },
      {
        path: '/lms/reserves/add',
        name: 'lms-reserves-add',
        component: () => import('@/views/lms/reserves/add.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách bảo lưu', url: '/lms/reserves' },
            { title: 'Thêm mới bảo lưu', active: true }
          ],
          pageTitle: 'Thêm mới bảo lưu',
          rule: 'editor'
        }
      },
      {
        path: '/lms/reserves/:id/detail',
        name: 'lms-reserves-detail',
        component: () => import('@/views/lms/reserves/detail.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách bảo lưu', url: '/lms/reserves'},
            { title: 'Thông tin bảo lưu', active: true}
          ],
          pageTitle: 'Thông tin bảo lưu',
          rule: 'editor'
        }
      },
      {
        path: '/lms/class_transfers',
        name: 'lms-class_transfers',
        component: () => import('@/views/lms/class_transfers/index.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách chuyển lớp', active: true}
          ],
          pageTitle: 'Danh sách chuyển lớp',
          rule: 'editor'
        }
      },
      {
        path: '/lms/class_transfers/add',
        name: 'lms-class_transfers-add',
        component: () => import('@/views/lms/class_transfers/add.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách chuyển lớp', url: '/lms/class_transfers' },
            { title: 'Thêm mới chuyển lớp', active: true }
          ],
          pageTitle: 'Thêm mới chuyển lớp',
          rule: 'editor'
        }
      },
      {
        path: '/lms/class_transfers/:id/detail',
        name: 'lms-class_transfers-detail',
        component: () => import('@/views/lms/class_transfers/detail.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách chuyển lớp', url: '/lms/class_transfers'},
            { title: 'Thông tin chuyển lớp', active: true}
          ],
          pageTitle: 'Thông tin chuyển lớp',
          rule: 'editor'
        }
      },
      {
        path: '/lms/branch_transfers',
        name: 'lms-branch_transfers',
        component: () => import('@/views/lms/branch_transfers/index.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách chuyển trung tâm', active: true}
          ],
          pageTitle: 'Danh sách chuyển trung tâm',
          rule: 'editor'
        }
      },
      {
        path: '/lms/branch_transfers/add',
        name: 'lms-branch_transfers-add',
        component: () => import('@/views/lms/branch_transfers/add.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách chuyển trung tâm', url: '/lms/branch_transfers' },
            { title: 'Thêm mới chuyển trung tâm', active: true }
          ],
          pageTitle: 'Thêm mới chuyển trung tâm',
          rule: 'editor'
        }
      },
      {
        path: '/lms/branch_transfers/:id/detail',
        name: 'lms-branch_transfers-detail',
        component: () => import('@/views/lms/branch_transfers/detail.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách chuyển trung tâm', url: '/lms/branch_transfers'},
            { title: 'Thông tin chuyển trung tâm', active: true}
          ],
          pageTitle: 'Thông tin chuyển trung tâm',
          rule: 'editor'
        }
      },
      {
        path: '/lms/tuition_transfers',
        name: 'lms-tuition_transfers',
        component: () => import('@/views/lms/tuition_transfers/index.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách chuyển phí', active: true}
          ],
          pageTitle: 'Danh sách chuyển phí',
          rule: 'editor'
        }
      },
      {
        path: '/lms/tuition_transfers/add',
        name: 'lms-tuition_transfers-add',
        component: () => import('@/views/lms/tuition_transfers/add.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách chuyển phí', url: '/lms/tuition_transfers' },
            { title: 'Thêm mới chuyển phí', active: true }
          ],
          pageTitle: 'Thêm mới chuyển phí',
          rule: 'editor'
        }
      },
      {
        path: '/lms/tuition_transfers/:id/detail',
        name: 'lms-tuition_transfers-detail',
        component: () => import('@/views/lms/tuition_transfers/detail.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách chuyển phí', url: '/lms/tuition_transfers'},
            { title: 'Thông tin chuyển phí', active: true}
          ],
          pageTitle: 'Thông tin chuyển phí',
          rule: 'editor'
        }
      },
      {
        path: '/lms/attendances',
        name: 'lms-attendances',
        component: () => import('@/views/lms/attendances/index.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Điểm danh', active: true }
          ],
          pageTitle: 'Điểm danh',
          rule: 'editor'
        }
      },
    ]
  },
}
