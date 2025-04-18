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
    permission:'Dashboard',
  },
  {
    header: 'CRM',
    icon: 'PackageIcon',
    permission:'CRM',
    items: [
      {
        url: '/crm/parent',
        name: 'Khách hàng',
        icon: 'UsersIcon',
        slug: 'crm-parent',
        permission:'crm_parent',
      },
      {
        url: '/crm/imports',
        name: 'Import',
        icon: 'ArrowUpCircleIcon',
        slug: 'crm-imports',
        permission:'crm_imports',
      }
    ]
  },
  {
    header: 'LMS',
    icon: 'PackageIcon',
    permission:'LMS',
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
            permission:'lms_students',
          },
          {
            url: '/lms/checkin',
            name: 'Checkin',
            slug: 'lms-checkin',
            permission:'lms_checkin',
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
            permission:'lms_contracts',
          },
          {
            url: '/lms/enrolments',
            name: 'Xếp lớp',
            slug: 'lms-enrolments',
            permission:'lms_enrolments',
          },
          {
            url: '/lms/reserves',
            name: 'Bảo lưu',
            slug: 'lms-reserves',
            permission:'lms_reserves',
          },
          {
            url: '/lms/class_transfers',
            name: 'Chuyển lớp',
            slug: 'lms-class_transfers',
            permission:'lms_class_transfers',
          },
          {
            url: '/lms/branch_transfers',
            name: 'Chuyển trung tâm',
            slug: 'lms-branch_transfers',
            permission:'lms_branch_transfers',
          },
          {
            url: '/lms/tuition_transfers',
            name: 'Chuyển phí',
            slug: 'lms-tuition_transfers',
            permission:'lms_tuition_transfers',
          },
          {
            url: '/lms/exchange',
            name: 'Quy đổi',
            slug: 'lms-exchange',
            permission:'lms_exchange',
          },
          {
            url: '/lms/attendances',
            name: 'Điểm danh',
            slug: 'lms-attendances',
            permission:'lms_attendances',
          },
          {
            url: '/lms/assessments',
            name: 'Nhận xét & đánh giá',
            slug: 'lms-attendances',
            permission:'lms_assessments',
          },
          {
            url: '/lms/teachers',
            name: 'Giáo viên & trợ giảng',
            slug: 'lms-teachers',
            permission:'lms_teachers',
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
            permission:'lms_charges',
          },
          {
            url: '/lms/waitcharges',
            name: 'Chờ thu phí',
            slug: 'lms-waitcharges',
            permission:'lms_waitcharges',
          },
          {
            url: '/lms/waitcharge-approve',
            name: 'Duyệt đóng phí',
            slug: 'lms-waitcharge-approve',
            permission:'lms_waitcharge_approve',
          }
        ],
      },
      {
        url: '/lms/reports',
        name: 'Báo cáo',
        icon: 'TrendingUpIcon',
        submenu: [
          {
            url: '/lms/reports/business',
            name: 'Kinh doanh',
            slug: 'lms-reports-business',
            permission:'lms_reports_business',
          },
          {
            url: '/lms/reports/finance',
            name: 'Tài chính kế toán',
            slug: 'lms-reports-finance',
            permission:'lms_reports_finance',
          },
          {
            url: '/lms/reports/operate',
            name: 'Vận hành',
            slug: 'lms-reports-operate',
            permission:'lms_reports_operate',
          },
          {
            url: '/lms/reports/academic',
            name: 'Học thuật',
            slug: 'lms-reports-academic',
            permission:'lms_reports_academic',
          },
          {
            url: '/lms/reports/teacher',
            name: 'Giáo viên',
            slug: 'lms-reports-teacher',
            permission:'lms_reports_teacher',
          }
        ],
      },
    ]
  },
  {
    header: 'MARKETING',
    icon: 'PackageIcon',
    items: [
      {
        url: '/marketing/campaigns',
        name: 'Chiến dịch',
        icon: 'Volume2Icon',
        slug: 'marketing-campaigns',
        permission:'marketing_campaigns',
      },
      {
        url: '/marketing/b2b',
        name: 'B2B',
        icon: 'CommandIcon',
        slug: 'marketing-b2b',
        submenu: [
          {
            url: '/marketing/b2b/sources',
            name: 'Nguồn đối tác',
            slug: 'marketing-b2b-sources',
            permission:'marketing_b2b_sources',
          },
          {
            url: '/marketing/b2b/campaigns',
            name: 'Chính sách B2B',
            slug: 'marketing-b2b-campaigns',
            permission:'marketing_b2b_campaigns',
          }
        ],
      },
      {
        url: '/marketing/c2c/campaigns',
        name: 'C2C',
        icon: 'UsersIcon',
        slug: 'marketing-c2c',
        permission:'marketing_c2c_campaigns',
      },
      {
        url: '/marketing/coupons',
        name: 'Voucher',
        icon: 'GiftIcon',
        slug: 'marketing-coupons',
        permission:'marketing_coupons',
      },
    ]
  },
  {
    header: 'Cấu hình hệ thống',
    icon: 'PackageIcon',
    permission:'Config',
    items: [
      {
        url: '/settings/branches',
        name: 'Trung tâm',
        icon: 'HomeIcon',
        slug: 'settings-branches',
        permission:'settings_branches',
      },
      {
        url: '/settings/products',
        name: 'Khóa học',
        icon: 'LayersIcon',
        slug: 'settings-products',
        permission:'settings_products',
      },
      {
        url: '/settings/programs',
        name: 'Chương trình học',
        icon: 'ServerIcon',
        slug: 'settings-programs',
        permission:'settings_programs',
      },
      // {
      //   url: '/settings/subjects',
      //   name: 'Môn học',
      //   icon: 'ChromeIcon',
      //   slug: 'settings-subjects',
      //   permission:'settings_subjects',
      // },
      {
        url: '/settings/classes',
        name: 'Lớp học',
        icon: 'FileIcon',
        slug: 'settings-classes',
        permission:'settings_classes',
      },
      {
        url: '/settings/rooms',
        name: 'Phòng học',
        icon: 'CastIcon',
        slug: 'settings-rooms',
        permission:'settings_rooms',
      },
      {
        url: '/settings/shifts',
        name: 'Ca học',
        icon: 'WatchIcon',
        slug: 'settings-shifts',
        permission:'settings_shifts',
      },
      {
        url: '/settings/holidays',
        name: 'Ngày nghỉ lễ',
        icon: 'CloudOffIcon',
        slug: 'settings-holidays',
        permission:'settings_holidays',
      },
      {
        url: '/settings/tuition-fees',
        name: 'Gói phí',
        icon: 'SlackIcon',
        slug: 'settings-tuition-fee',
        permission:'settings_tuition_fees',
      },
      {
        url: '/settings/discount-codes',
        name: 'Mã chiết khấu',
        icon: 'DivideSquareIcon',
        slug: 'settings-discount-codes',
        permission:'settings_discount_codes',
      },
      {
        url: '/settings/users',
        name: 'Người dùng',
        icon: 'UserIcon',
        slug: 'settings-users',
        permission:'settings_users',
      },
      {
        url: '/settings/roles',
        name: 'Chức vụ, phân quyền',
        icon: 'PocketIcon',
        slug: 'settings-roles',
        permission:'settings_roles',
      },
      
    ]
  },
]

