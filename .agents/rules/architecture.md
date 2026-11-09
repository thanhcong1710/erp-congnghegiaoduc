---
description: Bản đồ kiến trúc repo ERP_CHAM (nhánh lapo) - đọc trước khi trace code để đi thẳng vào đúng file
globs: ["*"]
alwaysApply: true
---

# 🗺️ ERP CÔNG NGHỆ GIÁO DỤC – repo `erp_cham` (bản LAPO / Chăm)

> Không thuộc 9Pay → **KHÔNG** áp dụng quy tắc nhánh `prod-tiktok` / quy trình Jira PAYGATEPRO.
> Repo anh em: `../erp` (bản STEMKEY) – **chung remote GitHub** `thanhcong1710/erp-congnghegiaoduc`, khác nhánh. Bản `erp_cham` mở rộng mạnh về **HRM (Nhân sự)**, **Agreements (Thỏa thuận/Hợp đồng doanh thu)**, **Trợ giảng (TA Tutoring)** và **Zoom Rooms**. Sửa bên này **không tự động** áp sang bên kia; chỉ port khi user yêu cầu.

## 0. ⚠️ An toàn dữ liệu (BẮT BUỘC)
- `.env` local đang trỏ DB **remote** `34.87.127.70`, database `lapo`, `APP_ENV=product` → coi như **DB thật của khách hàng**.
- **KHÔNG** tự chạy: `php artisan migrate*`, `db:seed`, các command import/process/update dữ liệu, `tinker` có ghi dữ liệu, câu SQL `UPDATE/DELETE/INSERT/ALTER`. Chỉ được chạy khi user xác nhận rõ ràng. Query `SELECT` để điều tra thì được.
- Nhánh: đang ở `lapo`. Có nhiều nhánh khách hàng khác (`stemkey`, `antenglish`, `scots`, `pisa`, `xgenius`...) → **không merge chéo** nếu user không yêu cầu.

## 1. Tech stack
- Backend: **Laravel 7**, PHP ^7.2.5. Auth **JWT** (`tymon/jwt-auth`, guard `api`, model `App\User`), `spatie/laravel-permission`, PhpSpreadsheet, BigBlueButton.
- Frontend: **Vue 2 + Vuex + vue-router + Vuesax** (template Vuexy), Tailwind 1, build bằng laravel-mix (`npm run dev|watch|prod`). Code tại `resources/js/src/`.
- Mobile: `erp-mobile/` (Expo/React Native, Redux slices; `src/components/{common,student,teacher}`).
- AI: Gemini/OpenAI – `config/ai.php`, `app/Services/AI/`.

## 2. Quy ước code BACKEND (theo code hiện có – giữ đồng nhất)
- **Đa số module core (CRM/LMS) truy vấn bằng raw SQL** qua helper:
  `use App\Providers\UtilityServiceProvider as u;`
  - `u::query($sql)` (trả mảng), `u::first($sql)` (1 dòng), `u::getOne`, `u::getObject`, `u::getMultiObject`
  - `u::insertSimpleRow($data, 'table')`, `u::updateSimpleRow($data, ['id'=>..], 'table')`
  - `u::makingPagination(...)`, `u::calculatorSessions*`, `u::addLogContracts`, `u::addLogAgreements`, `u::genLearningStatusByContracts`, `u::updateScheduleHasStudent`, `u::getPermissions`...
  - **Riêng module HRM và AI** có dùng Eloquent Models trong `app/Models/Hrm*.php` và `app/Models/Ai*.php`.
  - Gọi HTTP ngoài: `use App\Providers\CurlServiceProvider as curl;` (`curl::get|post|put|patch|delete`).
- **Response chuẩn:** `return response()->json((object)['status' => 1|0, 'message' => '...', 'data' => ...]);` (message tiếng Việt).
- User hiện tại: `Auth::user()->id`; role so sánh với `App\Enums\SystemCode::ROLE_*` (`ROLE_ADMIN=999999`, `ROLE_CEO_BRANCH=686868`, `ROLE_EC=68`, `ROLE_EC_LEADER=69`, `ROLE_CM=55`, `ROLE_CM_LEADER=56`, `ROLE_TEACHER=36`, `ROLE_TEACHER_LEADER=37`, `ROLE_TA=54`, `ROLE_OPS=16`). Phân quyền HRM theo `app/Services/Hrm/HrmScopeService.php`.
- Log thay đổi: `App\Models\LogStudents`, `LogParents`, `LogClassStudent`.
- **Code mới:** dùng helper `u::` (hoặc Eloquent `Hrm*` khi ở module HRM) cho đồng nhất, nhưng **KHÔNG nội suy trực tiếp input user vào SQL** → ép kiểu `(int)`, hoặc dùng `DB::select($sql, $bindings)` / `addslashes`.

## 3. Bản đồ module (API `routes/api.php` → Controller → View Vue)
Tất cả API nằm trong `Route::group(['middleware'=>'api'])`, phần cần đăng nhập nằm trong `['middleware'=>'jwt.auth']`.

| Prefix API | Controller chính (`app/Http/Controllers/`) | View FE (`resources/js/src/views/`) | Router FE |
|---|---|---|---|
| `auth/*` | `AuthController` (login/logout/register/forgot/active) | `pages/login`, `pages/register` | `routers/user.js` |
| `system/*` | `SystemController` | – | – |
| `user`, `users`, `roles`, `permissions`, `permission-groups` | `UserController`, `RolesController`, `PermissionsController`, `PermissionGroupsController` | `settings/{users,roles,permissions,permission_groups}`, `users/` | `setting.js`, `user.js` |
| `crm/*`, `crm/staff/*` | `ParentsController`, `StudentsController`, `ParentCareController`, `ImportsController`, `CheckinController`, `CrmStaffController` | `crm/{parents,imports,staff}` | `crm.js` |
| `lms/students` | `StudentsController`, `ContractsController` (1.7k dòng), `EnrolmentsController`, `ChargesController` (1.1k dòng) | `lms/{students,agreements,enrolments,accounting}` | `lms.js` |
| `lms/{exchanges,attendances,assessments,teachers}` | `ExchangesController`, `AttendancesController`, `AssessmentsController`, `TeachersController`, `ClassInfoController` | `lms/{exchange,attendances,assessments,teachers}` | `lms.js` |
| `lms/teaching-assistants/*` | `TeachingAssistantsController` → `Services/TATutoringService.php` | `lms/teaching_assistants` | `lms.js` |
| bảo lưu / chuyển lớp / chuyển CN / chuyển phí | `ReservesController`, `ClassTransfersController`, `BranchTransfersController`, `TuitionTransfersController` | `lms/{reserves,class_transfers,branch_transfers,tuition_transfers}` | `lms.js` |
| `lms/reports`, `lms/exports`, `export/*` | `ReportsController` (3.6k dòng), `ExportsController` (5k dòng) | `lms/report` | `lms.js` |
| **`hrm/*`** *(Đặc thù `erp_cham`)* | `Hrm/{DepartmentController, EmployeeProfileController, JobTitleController, JobLevelController, ShiftController, ShiftAssignmentController, OvertimeRequestController, LeaveTypeController, LeaveRequestController, AttendanceController, OnlineRegistrationController}` | `hrm/{departments,employees,contracts,documents,job_titles,job_levels,shifts,shift-assignments,overtime,leave-types,leaves,attendances,online-registrations}` | `hrm.js` |
| `settings/*` | `ClassesController`, `TuitionFeesController`, `BranchesController`, `ProductsController`, `ProgramsController`, `RoomsController`, `ShiftsController`, `HolidaysController`, `SubjectsController`, `DiscountCodesController`, `ZoomRoomsController` | `settings/<tên>`, `settings/zoom_rooms` | `setting.js` |
| `marketing/*` | `CampaignsController`, `CouponsController`, `B2BSourcesController`, `B2BCampaignsController`, `C2CCampaignsController` | `marketing/{campaigns,coupons,b2b,c2c}` | `marketing.js` |
| `dashboard/*` | `DashboardController` | `DashboardAnalytics.vue` | – |
| `ai/*` | `AI/ChatController`, `AI/AdminController` → `Services/AI/{AiAgentService,HybridAiService,GeminiService,TextToSqlService,FunctionExecutor}` | – | – |

**Thêm 1 màn hình mới** thường cần sửa 4 chỗ: `routes/api.php` → Controller → `resources/js/src/views/<module>/<tên>/{index,add,edit}.vue` → `resources/js/src/routers/<module>.js`.

## 4. Frontend
- Gọi API: helper trong `resources/js/src/http/axios.js` (`g` = GET, `p` = POST; tự gắn `Bearer localStorage.accessToken`, lỗi 401 → `processAuthen`).
- ACL: `resources/js/src/acl/acl.js` (vue-acl, rule `admin` / `editor`, role từ `localStorage.userInfo.userRole`).
- Store: `resources/js/src/store/` (+ `store/auth`), i18n: `resources/js/src/i18n/`, layout: `resources/js/src/layouts/main/Main.vue`.

## 5. Dữ liệu – bảng hay dùng
- **Core & Doanh thu:** `contracts`, `agreements` (thỏa thuận học phí/gói học), `log_agreements`, `agreement_transfers`, `agreements_revenue_histories`, `payments`, `tmp_payments`, `tuition_fee`, `tuition_fee_relation`, `reserves`, `class_transfer`, `branch_transfer`, `tuition_transfer`.
- **Học sinh & Lớp học:** `students`, `crm_parents`, `crm_students`, `crm_customer_care`, `crm_import_parents`, `classes`, `schedules`, `schedule_has_student`, `sessions`, `make_up_sessions`, `term_student_user`, `log_class_students`, `teaching_assistants`, `ta_tutorings*`, `teacher_salaries`, `zoom_rooms`.
- **HRM (`app/Models/Hrm*.php`):** `hrm_departments`, `hrm_employee_profiles`, `hrm_contracts`, `hrm_employee_documents`, `hrm_employment_histories`, `hrm_job_levels`, `hrm_job_titles`, `hrm_positions`, `hrm_shifts`, `hrm_shift_assignments`, `hrm_attendances`, `hrm_leave_types`, `hrm_leave_requests`, `hrm_leave_balances`, `hrm_overtime_requests`, `hrm_online_registrations`.

## 6. Cron (`app/Console/Kernel.php`)
`hrm:promote-probation` 00:00 · `jobsSendEmail:command` (mỗi phút) · `autoWithdrawEnrollmentDeposit:process` 23:55 · `scheduleHasStudent:process` 00:05 · `scheduleHasStudent:add` 01:00 · `processdata:command` 02:00 · `autoWithdraw:process` 02:30 · `jobsProcessLockParent:command` 03:00 · `snapshot:agreement-revenue` 23:45 · `erp:update-agreements-data` 23:50. Command nằm ở `app/Console/Commands/` (44 commands).

## 7. Khác
- Tài liệu: `docs/gcp_server_migration_guide.md`.
- Khi thêm module/bảng/cron mới → cập nhật file này 1–2 dòng.

