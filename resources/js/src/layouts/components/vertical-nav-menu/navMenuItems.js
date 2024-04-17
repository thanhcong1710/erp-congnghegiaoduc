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
    tagColor: 'warning',
    icon: 'HomeIcon',
    i18n: 'Dashboard',
    permission:'dashboard'
  },
  {
    header: 'CRM',
    icon: 'PackageIcon',
    items: [
      {
        url: '/crm/parent',
        name: 'Khách hàng',
        icon: 'UsersIcon',
      },
      {
        url: '/crm/imports',
        name: 'Import',
        icon: 'ArrowUpCircleIcon',
      }
    ]
  },
  {
    header: 'LMS',
    icon: 'PackageIcon',
    items: [
      {
        url: '/lms/students',
        name: 'Học sinh',
        icon: 'UserIcon',
        submenu: [
          {
            url: '/lms/students',
            name: 'Danh sách',
          },
          {
            url: '/lms/checkin',
            name: 'Checkin',
          }
        ],
      },
      {
        url: '/lms/contracts',
        name: 'Vận hành',
        icon: 'AirplayIcon',
        submenu: [
          {
            url: '/lms/contracts',
            name: 'Nhập học',
          },
          {
            url: '/lms/enrolments',
            name: 'Xếp lớp',
          },
          {
            url: '/lms/reserves',
            name: 'Bảo lưu',
          },
          {
            url: '/lms/class_transfers',
            name: 'Chuyển lớp',
          },
          {
            url: '/lms/branch_transfers',
            name: 'Chuyển trung tâm',
          },
          {
            url: '/lms/tution_transfers',
            name: 'Chuyển phí',
          },
          {
            url: '/lms/exchange',
            name: 'Quy đổi',
          },
          {
            url: '/lms/attendances',
            name: 'Điểm danh',
          }

        ]
      },
      {
        url: '/lms/accounting',
        name: 'Kế toán',
        icon: 'DollarSignIcon'
      },
      {
        url: '/lms/reports',
        name: 'Báo cáo',
        icon: 'TrendingUpIcon'
      },
    ]
  },
  {
    header: 'Cấu hình hệ thống',
    icon: 'PackageIcon',
    items: [
      {
        url: '/settings/users',
        name: 'Người dùng',
        icon: 'UserIcon',
      },
      {
        url: '/settings/roles',
        name: 'Chức vụ, phân quyền',
        icon: 'PocketIcon',
      }
    ]
  },
]

