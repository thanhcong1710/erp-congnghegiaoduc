/*=========================================================================================
  File Name: sidebarItems.js
  Description: Sidebar Items list. Add / Remove menu items from here.
  Strucutre:
          url     => router path
          name    => name to display in sidebar
          slug    => router path name
          icon    => Feather Icon component/icon name
          tag     => text to display on badge
          tagColor  => class to apply on badge element
          i18n    => Internationalization
          submenu   => submenu of current item (current item will become dropdown )
                NOTE: Submenu don't have any icon(you can add icon if u want to display)
          isDisabled  => disable sidebar item/group
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/


export default [
  {
    url: '/',
    name: 'Dashboard',
    icon: 'HomeIcon',
    slug: 'dashboard',
    permission:'dashboard',
  },
  {
    header: 'CRM',
    icon: 'PackageIcon',
    items: [
      {
        url: '/crm/parent',
        name: 'Khách hàng',
        icon: 'UsersIcon',
        slug: 'crm-parent',
      },
      {
        url: '/crm/imports',
        name: 'Import',
        icon: 'ArrowUpCircleIcon',
        slug: 'crm-imports',
      }
    ]
  },
  {
    header: 'LMS',
    icon: 'PackageIcon',
    items: [
      {
        url: null,
        name: 'Học sinh',
        icon: 'UserIcon',
        submenu: [
          {
            url: '/lms/students',
            name: 'Danh sách',
            slug: 'lms-students',
          },
          {
            url: '/lms/checkin',
            name: 'Checkin',
            slug: 'lms-checkin',
          }
        ],
      },
      {
        url: null,
        name: 'Vận hành',
        icon: 'AirplayIcon',
        submenu: [
          {
            url: '/lms/contracts',
            name: 'Nhập học',
            slug: 'lms-contracts',
          },
          {
            url: '/lms/enrolments',
            name: 'Xếp lớp',
            slug: 'lms-enrolments',
          },
          {
            url: '/lms/reserves',
            name: 'Bảo lưu',
            slug: 'lms-reserves',
          },
          {
            url: '/lms/class_transfers',
            name: 'Chuyển lớp',
            slug: 'lms-class_transfers',
          },
          {
            url: '/lms/branch_transfers',
            name: 'Chuyển trung tâm',
            slug: 'lms-branch_transfers',
          },
          {
            url: '/lms/tuition_transfers',
            name: 'Chuyển phí',
            slug: 'lms-tuition_transfers',
          },
          // {
          //   url: '/lms/exchange',
          //   name: 'Quy đổi',
          // },
          {
            url: '/lms/attendances',
            name: 'Điểm danh',
            slug: 'lms-attendances',
          }

        ]
      },
      {
        url: '/lms/charges',
        name: 'Kế toán',
        icon: 'DollarSignIcon',
        submenu: [
          {
            url: '/lms/charges',
            name: 'Đã thu phí',
            slug: 'lms-charges',
          },
          {
            url: '/lms/waitcharges',
            name: 'Chờ thu phí',
            slug: 'lms-waitcharges',
          }
        ],
      },
      {
        url: '/lms/reports',
        name: 'Báo cáo',
        icon: 'TrendingUpIcon',
        slug: 'lms-reports',
      },
    ]
  },
  {
    header: 'Cấu hình hệ thống',
    icon: 'PackageIcon',
    items: [
      {
        url: '/settings/branches',
        name: 'Trung tâm',
        icon: 'HomeIcon',
        slug: 'settings-branches',
      },
      {
        url: '/settings/products',
        name: 'Khóa học',
        icon: 'LayersIcon',
        slug: 'settings-products',
      },
      {
        url: '/settings/programs',
        name: 'Chương trình học',
        icon: 'ServerIcon',
        slug: 'settings-programs',
      },
      {
        url: '/settings/classes',
        name: 'Lớp học',
        icon: 'FileIcon',
        slug: 'settings-classes',
      },
      {
        url: '/settings/rooms',
        name: 'Phòng học',
        icon: 'CastIcon',
        slug: 'settings-rooms',
      },
      {
        url: '/settings/shifts',
        name: 'Ca học',
        icon: 'WatchIcon',
        slug: 'settings-shifts',
      },
      {
        url: '/settings/holidays',
        name: 'Ngày nghỉ lễ',
        icon: 'CloudOffIcon',
        slug: 'settings-holidays',
      },
      {
        url: '/settings/tuition-fees',
        name: 'Gói phí',
        icon: 'SlackIcon',
        slug: 'settings-tuition-fee',
      },
      {
        url: '/settings/users',
        name: 'Người dùng',
        icon: 'UserIcon',
        slug: 'settings-users',
      },
      {
        url: '/settings/roles',
        name: 'Chức vụ, phân quyền',
        icon: 'PocketIcon',
        slug: 'settings-roles',
      },
    ]
  },
]

