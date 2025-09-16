
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
        path: '/lms/checkin/add',
        name: 'lms-checkin-add',
        component: () => import('@/views/lms/checkin/add.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách checkin', url: '/lms/checkin'},
            { title: 'Thêm mới checkin', active: true}
          ],
          pageTitle: 'Thêm mới checkin',
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
        path: '/lms/contracts-renew',
        name: 'lms-contracts-renew',
        component: () => import('@/views/lms/contracts-renew/index.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách tái phí', active: true }
          ],
          pageTitle: 'Danh sách tái phí',
          rule: 'editor'
        }
      },
      {
        path: '/lms/contracts-renew/add',
        name: 'lms-contracts-renew-add',
        component: () => import('@/views/lms/contracts-renew/add.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách tái phí', url: '/lms/contracts-renew' },
            { title: 'Thêm mới tái phí', active: true }
          ],
          pageTitle: 'Thêm mới tái phí',
          rule: 'editor'
        }
      },
      {
        path: '/lms/contracts-renew/:id/detail',
        name: 'lms-contracts-renew-detail',
        component: () => import('@/views/lms/contracts-renew/detail.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách tái phí', url: '/lms/contracts-renew'},
            { title: 'Thông tin tái phí', active: true}
          ],
          pageTitle: 'Thông tin tái phí',
          rule: 'editor'
        }
      },
      {
        path: '/lms/contracts-renew/:id/edit',
        name: 'lms-contracts-renew-edit',
        component: () => import('@/views/lms/contracts-renew/edit.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách tái phí', url: '/lms/contracts-renew'},
            { title: 'Cập nhật tái phí', active: true}
          ],
          pageTitle: 'Cập nhật tái phí',
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
        path: '/lms/waitcharge-approve',
        name: 'lms-waitcharge-approve',
        component: () => import('@/views/lms/accounting/waitchargeApprove.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách chờ duyệt phí', active: true}
          ],
          pageTitle: 'Danh sách chờ duyệt phí',
          rule: 'editor'
        }
      },
      {
        path: '/lms/waitcharge-approve/:id/detail',
        name: 'lms-waitcharge-approve-detail',
        component: () => import('@/views/lms/accounting/waitchargeApproveDetail.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách chờ duyệt phí', url: '/lms/waitcharge-approve'},
            { title: 'Thông tin phiếu thu chờ duyệt', active: true}
          ],
          pageTitle: 'Thông tin phiếu thu chờ duyệt',
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
        path: '/lms/reserves-multi',
        name: 'lms-reserves-multi',
        component: () => import('@/views/lms/reserves/multiIndex.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách bảo lưu cả lớp', active: true}
          ],
          pageTitle: 'Danh sách bảo lưu cả lớp',
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
        path: '/lms/reserves/multi-add',
        name: 'lms-reserves-multi-add',
        component: () => import('@/views/lms/reserves/multiAdd.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách bảo lưu cả lớp', url: '/lms/reserves-multi' },
            { title: 'Bảo lưu cả lớp', active: true }
          ],
          pageTitle: 'Bảo lưu cả lớp',
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
        path: '/lms/reserves/:id/detail-multi',
        name: 'lms-reserves-detail-multi',
        component: () => import('@/views/lms/reserves/multiDetail.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách bảo lưu cả lớp', url: '/lms/reserves-multi'},
            { title: 'Thông tin bảo lưu cả lớp', active: true}
          ],
          pageTitle: 'Thông tin bảo lưu cả lớp',
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
        path: '/lms/class_transfers/add-semester',
        name: 'lms-class_transfers-add-semester',
        component: () => import('@/views/lms/class_transfers/addSemester.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách chuyển lớp', url: '/lms/class_transfers' },
            { title: 'Thêm mới chuyển kỳ', active: true }
          ],
          pageTitle: 'Thêm mới chuyển kỳ',
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
            { title: 'Báo cáo tái phí - CM', active: true}
          ],
          pageTitle: 'Báo cáo tái phí - CM',
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
            { title: 'Báo cáo tổng hợp theo thời gian', active: true}
          ],
          pageTitle: 'Báo cáo tổng hợp theo thời gian',
          rule: 'editor'
        }
      },
      {
        path: '/lms/reports/operate/report-06',
        name: 'lms-report-operate-06',
        component: () => import('@/views/lms/report/operate/report06.vue'),
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
            { title: 'Báo cáo tỷ lệ ACS của từng CM', active: true}
          ],
          pageTitle: 'Báo cáo tỷ lệ ACS của từng CM',
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
        path: '/lms/reports/business/report-101',
        name: 'lms-report-business-101',
        component: () => import('@/views/lms/report/business/report101.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách báo cáo kinh doanh', url: '/lms/reports/business'},
            { title: 'Kết quả kinh doanh theo trung tâm', active: true}
          ],
          pageTitle: 'Kết quả kinh doanh theo trung tâm',
          rule: 'editor'
        }
      },
      {
        path: '/lms/reports/business/report-102',
        name: 'lms-report-business-102',
        component: () => import('@/views/lms/report/business/report102.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách báo cáo kinh doanh', url: '/lms/reports/business'},
            { title: 'Kết quả kinh doanh theo sale', active: true}
          ],
          pageTitle: 'Kết quả kinh doanh theo sale',
          rule: 'editor'
        }
      },
      {
        path: '/lms/reports/business/report-103',
        name: 'lms-report-business-103',
        component: () => import('@/views/lms/report/business/report103.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách báo cáo kinh doanh', url: '/lms/reports/business'},
            { title: 'Báo cáo checkin', active: true}
          ],
          pageTitle: 'Báo cáo checkin',
          rule: 'editor'
        }
      },
      {
        path: '/lms/reports/business/report-104',
        name: 'lms-report-business-104',
        component: () => import('@/views/lms/report/business/report104.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách báo cáo kinh doanh', url: '/lms/reports/business'},
            { title: 'Báo cáo học thử', active: true}
          ],
          pageTitle: 'Báo cáo học thử',
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
      {
        path: '/lms/reports/finance/report-201',
        name: 'lms-report-finance-201',
        component: () => import('@/views/lms/report/finance/report201.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách báo cáo tài chính kế toán', url: '/lms/reports/finance'},
            { title: 'Báo cáo doanh thu phân bổ theo tháng', active: true}
          ],
          pageTitle: 'Báo cáo doanh thu phân bổ theo tháng',
          rule: 'editor'
        },
      },
      {
        path: '/lms/reports/finance/report-202',
        name: 'lms-report-finance-202',
        component: () => import('@/views/lms/report/finance/report202.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách báo cáo tài chính kế toán', url: '/lms/reports/finance'},
            { title: 'Báo cáo đối soát CREVERSE', active: true}
          ],
          pageTitle: 'Báo cáo đối soát CREVERSE',
          rule: 'editor'
        }
      },
      {
        path: '/lms/reports/finance/report-203',
        name: 'lms-report-finance-203',
        component: () => import('@/views/lms/report/finance/report203.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách báo cáo tài chính kế toán', url: '/lms/reports/finance'},
            { title: 'Báo cáo doanh số', active: true}
          ],
          pageTitle: 'Báo cáo doanh số',
          rule: 'editor'
        }
      },
    ]
  },
}
