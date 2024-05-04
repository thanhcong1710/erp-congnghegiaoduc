
export default {
  router: {
    path: '/settings',
    component: () => import('@/layouts/main/Main.vue'),
    children: [
      {
        path: '/settings/roles',
        name: 'roles',
        component: () => import('@/views/settings/roles/index.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Chức vụ', active: true }
          ],
          pageTitle: 'Chức vụ',
          rule: 'editor'
        }
      },
      {
        path: '/settings/permissions/:role_id',
        name: 'permissions',
        component: () => import('@/views/settings/roles/permissions.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Phân quyền', active: true }
          ],
          pageTitle: 'Phân quyền',
          rule: 'editor'
        }
      },
      {
        path: '/settings/users',
        name: 'setting-users',
        component: () => import('@/views/settings/users/index.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Người dùng', active: true }
          ],
          pageTitle: 'Người dùng',
          rule: 'editor'
        }
      },
      {
        path: '/settings/users/add',
        name: 'setting-user-add',
        component: () => import('@/views/settings/users/add.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Người dùng', url: '/settings/users' },
            { title: 'Thêm mới', active: true }
          ],
          pageTitle: 'Thêm mới người dùng',
          rule: 'editor'
        }
      },
      {
        path: '/settings/users/edit/:user_id',
        name: 'setting-user-edit',
        component: () => import('@/views/settings/users/edit.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Người dùng', url: '/settings/users' },
            { title: 'Cập nhật', active: true }
          ],
          pageTitle: 'Cập nhật người dùng',
          rule: 'editor'
        }
      },
      {
        path: '/settings/classes',
        name: 'setting-classes',
        component: () => import('@/views/settings/classes/index.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách lớp học', active: true }
          ],
          pageTitle: 'Danh sách lớp học',
          rule: 'editor'
        }
      },
      {
        path: '/settings/tuition-fees',
        name: 'setting-tuition-fees',
        component: () => import('@/views/settings/tuition_fees/index.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách gói phí', active: true }
          ],
          pageTitle: 'Danh sách gói phí',
          rule: 'editor'
        }
      },
      {
        path: '/settings/tuition-fees/add',
        name: 'setting-tuition-fees-add',
        component: () => import('@/views/settings/tuition_fees/add.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách gói phí', url: '/settings/tuition-fees' },
            { title: 'Thêm mới', active: true }
          ],
          pageTitle: 'Thêm mới gói phí',
          rule: 'editor'
        }
      },
      {
        path: '/settings/tuition-fees/edit/:id',
        name: 'setting-tuition-fees-edit',
        component: () => import('@/views/settings/tuition_fees/edit.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách gói phí', url: '/settings/tuition-fee' },
            { title: 'Cập nhật', active: true }
          ],
          pageTitle: 'Cập nhật gói phí',
          rule: 'editor'
        }
      },
      {
        path: '/settings/branches',
        name: 'setting-branches',
        component: () => import('@/views/settings/branches/index.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách trung tâm', active: true }
          ],
          pageTitle: 'Danh sách trung tâm',
          rule: 'editor'
        }
      },
      {
        path: '/settings/branches/add',
        name: 'setting-branches-add',
        component: () => import('@/views/settings/branches/add.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách trung tâm', url: '/settings/branches' },
            { title: 'Thêm mới', active: true }
          ],
          pageTitle: 'Thêm mới trung tâm',
          rule: 'editor'
        }
      },
      {
        path: '/settings/branches/edit/:id',
        name: 'setting-branches-edit',
        component: () => import('@/views/settings/branches/edit.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách trung tâm', url: '/settings/branches' },
            { title: 'Cập nhật', active: true }
          ],
          pageTitle: 'Cập nhật trung tâm',
          rule: 'editor'
        }
      },
      {
        path: '/settings/products',
        name: 'setting-products',
        component: () => import('@/views/settings/products/index.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách khóa học', active: true }
          ],
          pageTitle: 'Danh sách khóa học',
          rule: 'editor'
        }
      },
      {
        path: '/settings/products/add',
        name: 'setting-products-add',
        component: () => import('@/views/settings/products/add.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách khóa học', url: '/settings/products' },
            { title: 'Thêm mới', active: true }
          ],
          pageTitle: 'Thêm mới khóa học',
          rule: 'editor'
        }
      },
      {
        path: '/settings/products/edit/:id',
        name: 'setting-products-edit',
        component: () => import('@/views/settings/products/edit.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách khóa học', url: '/settings/products' },
            { title: 'Cập nhật', active: true }
          ],
          pageTitle: 'Cập nhật khóa học',
          rule: 'editor'
        }
      },
      {
        path: '/settings/programs',
        name: 'setting-programs',
        component: () => import('@/views/settings/programs/index.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách chương trình học', active: true }
          ],
          pageTitle: 'Danh sách chương trình học',
          rule: 'editor'
        }
      },
      {
        path: '/settings/programs/add',
        name: 'setting-programs-add',
        component: () => import('@/views/settings/programs/add.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách chương trình học', url: '/settings/programs' },
            { title: 'Thêm mới', active: true }
          ],
          pageTitle: 'Thêm mới chương trình học',
          rule: 'editor'
        }
      },
      {
        path: '/settings/programs/edit/:id',
        name: 'setting-programs-edit',
        component: () => import('@/views/settings/programs/edit.vue'),
        meta: {
          breadcrumb: [
            { title: 'Home', url: '/' },
            { title: 'Danh sách chương trình học', url: '/settings/programs' },
            { title: 'Cập nhật', active: true }
          ],
          pageTitle: 'Cập nhật chương trình học',
          rule: 'editor'
        }
      },
    ]
  },
}
