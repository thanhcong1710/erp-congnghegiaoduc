
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
        path: '/lms/exchange',
        name: 'lms-exchange',
        component: () => import('@/views/lms/exchange/index.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách quy đổi', active: true}
          ],
          pageTitle: 'Danh sách quy đổi',
          rule: 'editor'
        }
      },
      {
        path: '/lms/exchange/add',
        name: 'lms-exchange-add',
        component: () => import('@/views/lms/exchange/add.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách quy đổi', url: '/lms/exchange' },
            { title: 'Thêm mới quy đổi', active: true }
          ],
          pageTitle: 'Thêm mới quy đổi',
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
      {
        path: '/lms/assessments',
        name: 'lms-assessments',
        component: () => import('@/views/lms/assessments/index.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách nhận xét & đánh giá', active: true}
          ],
          pageTitle: 'Danh sách nhận xét & đánh giá',
          rule: 'editor'
        }
      },
      {
        path: '/lms/assessments/add',
        name: 'lms-assessments-add',
        component: () => import('@/views/lms/assessments/add.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách nhận xét & đánh giá', url: '/lms/assessments' },
            { title: 'Thêm mới nhận xét & đánh giá', active: true }
          ],
          pageTitle: 'Thêm mới nhận xét & đánh giá',
          rule: 'editor'
        }
      },
      {
        path: '/lms/assessments/edit/:id',
        name: 'lms-assessments-edit',
        component: () => import('@/views/lms/assessments/edit.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách nhận xét & đánh giá', url: '/lms/assessments'},
            { title: 'Cập nhật nhận xét & đánh giá', active: true}
          ],
          pageTitle: 'Cập nhật nhận xét & đánh giá',
          rule: 'editor'
        }
      },
      {
        path: '/lms/assessments/detail/:id',
        name: 'lms-assessments-detail',
        component: () => import('@/views/lms/assessments/detail.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách nhận xét & đánh giá', url: '/lms/assessments'},
            { title: 'Thông tin nhận xét & đánh giá', active: true}
          ],
          pageTitle: 'Thông tin nhận xét & đánh giá',
          rule: 'editor'
        }
      },
      {
        path: '/lms/teachers',
        name: 'lms-teachers',
        component: () => import('@/views/lms/teachers/index.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách buổi dạy', active: true}
          ],
          pageTitle: 'Danh sách buổi dạy',
          rule: 'editor'
        }
      },
      {
        path: '/lms/teachers/edit/:id',
        name: 'lms-teachers-edit',
        component: () => import('@/views/lms/teachers/edit.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách buổi dạy', url: '/lms/teachers'},
            { title: 'Cập nhật buổi dạy', active: true}
          ],
          pageTitle: 'Cập nhật buổi dạy',
          rule: 'editor'
        }
      },
      {
        path: '/lms/reports/operate',
        name: 'lms-reports-operate',
        component: () => import('@/views/lms/report/operate/index.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách báo cáo vận hành', active: true},
          ],
          pageTitle: 'Báo cáo',
          rule: 'editor'
        }
      },
      {
        path: '/lms/reports/operate/report-01',
        name: 'lms-report-operate-01',
        component: () => import('@/views/lms/report/operate/report01.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách báo cáo vận hành', url: '/lms/reports/operate'},
            { title: 'Báo cáo full fee active', active: true}
          ],
          pageTitle: 'Báo cáo full fee active',
          rule: 'editor'
        }
      },
      {
        path: '/lms/reports/operate/report-02a',
        name: 'lms-report-02a',
        component: () => import('@/views/lms/report/operate/report02a.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách báo cáo vận hành', url: '/lms/reports/operate'},
            { title: 'Báo cáo tái phí - chi tiết', active: true}
          ],
          pageTitle: 'Báo cáo tái phí - chi tiết',
          rule: 'editor'
        }
      },
      {
        path: '/lms/reports/operate/report-02b',
        name: 'lms-report-operate-02b',
        component: () => import('@/views/lms/report/operate/report02b.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách báo cáo vận hành', url: '/lms/reports/operate'},
            { title: 'Báo cáo tái phí - tổng hợp', active: true}
          ],
          pageTitle: 'Báo cáo tái phí - tổng hợp',
          rule: 'editor'
        }
      },
      {
        path: '/lms/reports/operate/report-02c',
        name: 'lms-report-operate-02c',
        component: () => import('@/views/lms/report/operate/report02c.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách báo cáo vận hành', url: '/lms/reports/operate'},
            { title: 'Báo cáo tái phí - EC', active: true}
          ],
          pageTitle: 'Báo cáo tái phí - EC',
          rule: 'editor'
        }
      },
      {
        path: '/lms/reports/operate/report-05',
        name: 'lms-report-operate-05',
        component: () => import('@/views/lms/report/operate/report05.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách báo cáo vận hành', url: '/lms/reports/operate'},
            { title: 'Báo cáo học sinh pending', active: true}
          ],
          pageTitle: 'Báo cáo học sinh pending',
          rule: 'editor'
        }
      },
      {
        path: '/lms/reports/operate/report-06',
        name: 'lms-report-operate-06',
        component: () => import('@/views/lms/report/operate/report05.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách báo cáo vận hành', url: '/lms/reports/operate'},
            { title: 'Báo cáo học sinh bảo lưu', active: true}
          ],
          pageTitle: 'Báo cáo học sinh bảo lưu',
          rule: 'editor'
        }
      },
      {
        path: '/lms/reports/operate/report-07',
        name: 'lms-report-operate-07',
        component: () => import('@/views/lms/report/operate/report07.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách báo cáo vận hành', url: '/lms/reports/operate'},
            { title: 'Báo cáo lớp học', active: true}
          ],
          pageTitle: 'Báo cáo lớp học',
          rule: 'editor'
        }
      },
      {
        path: '/lms/reports/operate/report-08',
        name: 'lms-report-operate-08',
        component: () => import('@/views/lms/report/operate/report08.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách báo cáo vận hành', url: '/lms/reports/operate'},
            { title: 'Chỉ số vận hành ACS', active: true}
          ],
          pageTitle: 'Chỉ số vận hành ACS',
          rule: 'editor'
        }
      },
      {
        path: '/lms/reports/operate/report-09',
        name: 'lms-report-operate-09',
        component: () => import('@/views/lms/report/operate/report09.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách báo cáo vận hành', url: '/lms/reports/operate'},
            { title: 'Báo cáo tỷ lệ ACS của từng AF', active: true}
          ],
          pageTitle: 'Báo cáo tỷ lệ ACS của từng AF',
          rule: 'editor'
        }
      },
      {
        path: '/lms/reports/operate/report-10',
        name: 'lms-report-operate-10',
        component: () => import('@/views/lms/report/operate/report10.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách báo cáo vận hành', url: '/lms/reports/operate'},
            { title: 'Báo cáo tỷ lệ ACS của từng trung tâm', active: true}
          ],
          pageTitle: 'Báo cáo tỷ lệ ACS của từng trung tâm',
          rule: 'editor'
        }
      },
      {
        path: '/lms/reports/operate/report-11',
        name: 'lms-report-operate-11',
        component: () => import('@/views/lms/report/operate/report11.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách báo cáo vận hành', url: '/lms/reports/operate'},
            { title: 'Báo cáo học viên trial', active: true}
          ],
          pageTitle: 'Báo cáo học viên trial',
          rule: 'editor'
        }
      },
      {
        path: '/lms/reports/operate/report-12',
        name: 'lms-report-operate-12',
        component: () => import('@/views/lms/report/operate/report12.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách báo cáo vận hành', url: '/lms/reports/operate'},
            { title: 'Báo cáo học viên đặt cọc', active: true}
          ],
          pageTitle: 'Báo cáo học viên đặt cọc',
          rule: 'editor'
        }
      },
      {
        path: '/lms/reports/academic',
        name: 'lms-reports-academic',
        component: () => import('@/views/lms/report/academic/index.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách báo cáo học thuật', active: true},
          ],
          pageTitle: 'Báo cáo',
          rule: 'editor'
        }
      },
      {
        path: '/lms/reports/teacher',
        name: 'lms-reports-teacher',
        component: () => import('@/views/lms/report/teacher/index.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách báo cáo giáo viên', active: true},
          ],
          pageTitle: 'Báo cáo',
          rule: 'editor'
        }
      },
      {
        path: '/lms/reports/business',
        name: 'lms-reports-business',
        component: () => import('@/views/lms/report/business/index.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách báo cáo kinh doanh', active: true},
          ],
          pageTitle: 'Báo cáo',
          rule: 'editor'
        }
      },
      {
        path: '/lms/reports/finance',
        name: 'lms-reports-finance',
        component: () => import('@/views/lms/report/finance/index.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách báo cáo tài chính kế toán', active: true},
          ],
          pageTitle: 'Báo cáo',
          rule: 'editor'
        }
      },
    ]
  },
}
