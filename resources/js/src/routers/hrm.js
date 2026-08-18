export default {
  router: {
    path: '',
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
            { title: 'Phòng ban', active: true }
          ],
          pageTitle: 'Quản lý phòng ban',
          rule: 'editor'
        }
      },
      {
        path: '/hrm/employees',
        name: 'hrm-employees',
        component: () => import('@/views/hrm/employees/List.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'HRM', active: true },
            { title: 'Nhân viên', active: true }
          ],
          pageTitle: 'Quản lý nhân viên',
          rule: 'editor'
        }
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
      }
    ]
  }
}
