export default {
  router: {
    path: '/hrm-module',
    component: () => import('@/layouts/main/Main.vue'),
    children: [
      {
        path: '/hrm/departments',
        name: 'hrm-departments',
        component: () => import('@/views/hrm/departments/List.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'HRM', active: true },
            { title: 'Cơ cấu tổ chức', active: true }
          ],
          pageTitle: 'Cơ cấu tổ chức',
          rule: 'editor'
        }
      },
      {
        path: '/hrm/job-levels',
        name: 'hrm-job-levels',
        component: () => import('@/views/hrm/job_levels/List.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'HRM', url: '/hrm/departments' },
            { title: 'Cấp bậc', active: true },
          ],
          pageTitle: 'Quản lý Cấp bậc',
          rule: 'editor'
        },
      },
      {
        path: '/hrm/job-titles',
        name: 'hrm-job-titles',
        component: () => import('@/views/hrm/job_titles/List.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'HRM', url: '/hrm/departments' },
            { title: 'Chức danh', active: true },
          ],
          pageTitle: 'Quản lý Chức danh',
          rule: 'editor'
        },
      },
      {
        path: '/hrm/employees',
        name: 'hrm-employees',
        component: () => import('@/views/hrm/employees/List.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'HRM', url: '/hrm/departments' },
            { title: 'Nhân viên', active: true },
          ],
          pageTitle: 'Danh sách Nhân viên',
          rule: 'editor'
        },
      },
      {
        path: '/hrm/employees/add',
        name: 'hrm-employees-add',
        component: () => import('@/views/hrm/employees/Edit.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'HRM', url: '/hrm/departments' },
            { title: 'Nhân viên', url: '/hrm/employees' },
            { title: 'Thêm mới', active: true },
          ],
          pageTitle: 'Thêm mới Hồ sơ Nhân viên',
          rule: 'editor'
        },
      },
      {
        path: '/hrm/employees/edit/:id',
        name: 'hrm-employees-edit',
        component: () => import('@/views/hrm/employees/Edit.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'HRM', url: '/hrm/departments' },
            { title: 'Nhân viên', url: '/hrm/employees' },
            { title: 'Cập nhật', active: true },
          ],
          pageTitle: 'Hồ sơ Nhân viên',
          rule: 'editor'
        },
      },
      {
        path: '/hrm/leaves',
        name: 'hrm-leaves',
        component: () => import('@/views/hrm/leaves/List.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'HRM', active: true },
            { title: 'Nghỉ phép', active: true }
          ],
          pageTitle: 'Quản lý nghỉ phép',
          rule: 'editor'
        }
      },
      {
        path: '/hrm/leave-types',
        name: 'hrm-leave-types',
        component: () => import('@/views/hrm/leave-types/List.vue'),
        meta: { pageTitle: 'Loại nghỉ phép', rule: 'editor' }
      },
      {
        path: '/hrm/attendances',
        name: 'hrm-attendances',
        component: () => import('@/views/hrm/attendances/List.vue'),
        meta: { pageTitle: 'Bảng chấm công', rule: 'editor' }
      },
      {
        path: '/hrm/shifts',
        name: 'hrm-shifts',
        component: () => import('@/views/hrm/shifts/List.vue'),
        meta: { pageTitle: 'Ca làm việc', rule: 'editor' }
      },
      {
        path: '/hrm/shift-assignments',
        name: 'hrm-shift-assignments',
        component: () => import('@/views/hrm/shift-assignments/List.vue'),
        meta: { pageTitle: 'Xếp ca', rule: 'editor' }
      },
      {
        path: '/hrm/overtime',
        name: 'hrm-overtime',
        component: () => import('@/views/hrm/overtime/List.vue'),
        meta: { pageTitle: 'Làm thêm giờ', rule: 'editor' }
      }
    ]
  }
}
