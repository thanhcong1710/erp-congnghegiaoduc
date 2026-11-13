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
        url: '/crm/parent/detail-list',
        name: 'Khách hàng',
        icon: 'UsersIcon',
        slug: 'crm-parent',
        permission:'crm_parent',
      },
      {
        url: '/crm/care',
        name: 'QL chăm sóc',
        icon: 'PhoneCallIcon',
        slug: 'crm-care',
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
        url: '/lms/students',
        name: 'Học sinh',
        icon: 'UserIcon',
        slug: 'lms-students',
        permission:'lms_students',
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
            url: '/lms/attendances',
            name: 'Điểm danh',
            slug: 'lms-attendances',
            permission:'lms_attendances',
          },
          {
            url: '/lms/assessments',
            name: 'Nhận xét & đánh giá',
            slug: 'lms-assessments',
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
        url: null,
        name: 'Bảo lưu & chuyển đổi',
        icon: 'RepeatIcon',
        submenu: [
          {
            url: '/lms/reserves',
            name: 'Bảo lưu',
            slug: 'lms-reserves',
            permission:'lms_reserves',
          },
          {
            url: '/lms/reserves-multi',
            name: 'Bảo lưu cả lớp',
            slug: 'lms-reserves-multi',
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
          }
        ]
      },
      {
        url: null,
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
            name: 'Duyệt phí',
            slug: 'lms-waitcharge-approve',
            permission:'lms_waitcharge_approve',
          }
        ],
      },
      {
        url: '/lms/reports',
        name: 'Báo cáo',
        slug: 'lms-reports',
        icon: 'TrendingUpIcon',
        permission: 'lms_reports',
      },
    ]
  },
  {
    header: 'Test & Đánh giá',
    icon: 'CheckSquareIcon',
    items: [
      {
        url: null,
        name: 'UCREA',
        icon: 'StarIcon',
        slug: 'lms-evaluations-ucrea-group',
        submenu: [
          {
            url: '/lms/evaluations/ucrea',
            name: 'Đánh giá năng lực',
            slug: 'lms-evaluations-ucrea',
          },
          {
            url: '/lms/evaluations/ucrea/tests',
            name: 'Đề kiểm tra UCREA',
            slug: 'lms-evaluations-ucrea-tests',
          }
        ]
      },
      {
        url: null,
        name: 'IG.BH',
        icon: 'BarChart2Icon',
        slug: 'lms-evaluations-igbh-group',
        submenu: [
          {
            url: '/lms/evaluations/igbh/diagnostic',
            name: 'Khảo sát đầu vào',
            slug: 'lms-evaluations-igbh-diagnostic',
          },
          {
            url: '/lms/evaluations/igbh/weekly',
            name: 'Đánh giá tuần',
            slug: 'lms-evaluations-igbh-weekly',
          },
          {
            url: '/lms/evaluations/igbh/summative',
            name: 'Tổng kết lớp',
            slug: 'lms-evaluations-igbh-summative-classes',
          },
          {
            url: '/lms/evaluations/igbh/summative/students',
            name: 'Tổng kết học sinh',
            slug: 'lms-evaluations-igbh-summative-students',
          },
          {
            url: '/lms/evaluations/igbh/tests',
            name: 'Đề kiểm tra IG.BH',
            slug: 'lms-evaluations-igbh-tests',
          }
        ]
      }
    ]
  },
  {
    header: 'MARKETING',
    icon: 'PackageIcon',
    permission:'Marketing',
    items: [
      {
        url: '/marketing/campaigns',
        name: 'Chiến dịch',
        icon: 'Volume2Icon',
        slug: 'marketing-campaigns',
        permission:'marketing_campaigns',
      },
      {
        url: null,
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
        url: null,
        name: 'Đào tạo',
        icon: 'BookOpenIcon',
        submenu: [
          { url: '/settings/products', name: 'Khóa học', slug: 'settings-products', permission:'settings_products' },
          { url: '/settings/programs', name: 'Chương trình học', slug: 'settings-programs', permission:'settings_programs' },
          { url: '/settings/classes', name: 'Lớp học', slug: 'settings-classes', permission:'settings_classes' },
          { url: '/settings/rooms', name: 'Phòng học', slug: 'settings-rooms', permission:'settings_rooms' },
          { url: '/settings/shifts', name: 'Ca học', slug: 'settings-shifts', permission:'settings_shifts' },
          { url: '/settings/holidays', name: 'Ngày nghỉ lễ', slug: 'settings-holidays', permission:'settings_holidays' },
          // { url: '/settings/subjects', name: 'Môn học', slug: 'settings-subjects', permission:'settings_subjects' },
        ]
      },
      {
        url: null,
        name: 'Học phí & khách hàng',
        icon: 'CreditCardIcon',
        submenu: [
          { url: '/settings/tuition-fees', name: 'Gói phí', slug: 'settings-tuition-fee', permission:'settings_tuition_fees' },
          { url: '/settings/discount-codes', name: 'Mã chiết khấu', slug: 'settings-discount-codes', permission:'settings_discount_codes' },
          { url: '/settings/sources', name: 'Nguồn khách hàng', slug: 'settings-sources', permission:'settings_sources' },
        ]
      },
      {
        url: null,
        name: 'Tài khoản & phân quyền',
        icon: 'ShieldIcon',
        submenu: [
          { url: '/settings/users', name: 'Người dùng', slug: 'settings-users', permission:'settings_users' },
          { url: '/settings/roles', name: 'Chức vụ, phân quyền', slug: 'settings-roles', permission:'settings_roles' },
          { url: '/settings/permissions', name: 'Permissions', slug: 'settings-permissions', permission: 'settings_permissions' },
          { url: '/settings/permission-groups', name: 'Nhóm Permissions', slug: 'settings-permission-groups', permission: 'settings_permission_groups' },
        ]
      },
    ]
  },
  // {
  //   url: '/ai-admin',
  //   name: 'Quản lý AI',
  //   icon: 'CpuIcon',
  //   slug: 'ai-admin',
  //   permission: 'ai_admin',
  // },
  {
    url: '/guide/index.html',
    name: 'Hướng dẫn sử dụng',
    slug: 'external',
    icon: 'BookOpenIcon',
    target: '_blank'
  }
]

