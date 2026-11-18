---
description: Bản đồ kiến trúc repo ERP (nhánh stemkey) - đọc trước khi trace code để đi thẳng vào đúng file
globs: ["*"]
alwaysApply: true
---

# 🗺️ ERP CÔNG NGHỆ GIÁO DỤC – repo `erp` (bản STEMKEY)

> Không thuộc 9Pay → **KHÔNG** áp dụng quy tắc nhánh `prod-tiktok` / quy trình Jira PAYGATEPRO.
> Repo anh em: `../erp_cham` (bản LAPO) – **chung remote GitHub** `thanhcong1710/erp-congnghegiaoduc`, khác nhánh. Code gần như giống nhau (fork theo khách hàng) → sửa bên này **không tự động** áp sang bên kia; chỉ port khi user yêu cầu.

## 0. ⚠️ An toàn dữ liệu (BẮT BUỘC)
- `.env` local đang trỏ DB **remote** `34.87.127.70`, database `stemkey`, `APP_ENV=product` → coi như **DB thật của khách hàng**.
- **KHÔNG** tự chạy: `php artisan migrate*`, `db:seed`, các command import/process/update dữ liệu, `tinker` có ghi dữ liệu, câu SQL `UPDATE/DELETE/INSERT/ALTER`. Chỉ được chạy khi user xác nhận rõ ràng. Query `SELECT` để điều tra thì được.
- Nhánh: đang ở `stemkey`. Có nhiều nhánh khách hàng khác (`lapo`, `antenglish`, `scots`, `pisa`, `xgenius`, `logiclab`, `octopool`...) → **không merge chéo** nếu user không yêu cầu.

## 1. Tech stack
- Backend: **Laravel 7**, PHP ^7.2.5 (local nginx `local-erp.congnghegiaoduc.com` → container `php`). Auth **JWT** (`tymon/jwt-auth`, guard `api`, model `App\User`), `spatie/laravel-permission`, PhpSpreadsheet, BigBlueButton.
- Frontend: **Vue 2 + Vuex + vue-router + Vuesax** (template Vuexy), Tailwind 1, build bằng laravel-mix (`npm run dev|watch|prod`). Code tại `resources/js/src/`.
- Mobile: `erp-mobile/` (Expo/React Native, Redux slices; `src/components/{common,student,teacher}`).
- AI: Gemini/OpenAI – `config/ai.php`, `app/Services/AI/`.

## 2. Quy ước code BACKEND (theo code hiện có – giữ đồng nhất)
- **Gần như không dùng Eloquent.** Controller truy vấn bằng **raw SQL** qua helper:
  `use App\Providers\UtilityServiceProvider as u;`
  - `u::query($sql)` (trả mảng), `u::first($sql)` (1 dòng), `u::getOne`, `u::getObject`, `u::getMultiObject`
  - `u::insertSimpleRow($data, 'table')`, `u::updateSimpleRow($data, ['id'=>..], 'table')`
  - `u::makingPagination(...)`, `u::calculatorSessions*`, `u::getPublicHolidays`, `u::updateScheduleHasStudent`, `u::updateEnrolmentLastDate`, `u::getPermissions`, `u::getBranchIdByUserID`, `u::addLogContracts`, `u::formatCurrency`...
  - Gọi HTTP ngoài: `use App\Providers\CurlServiceProvider as curl;` (`curl::get|post|put|patch|delete`).
- **Response chuẩn:** `return response()->json((object)['status' => 1|0, 'message' => '...', 'data' => ...]);` (message tiếng Việt).
- User hiện tại: `Auth::user()->id`; role so sánh với `App\Enums\SystemCode::ROLE_*` (`ROLE_ADMIN=99999999`, `ROLE_CEO_BRANCH=686868`, `ROLE_EC=68`, `ROLE_EC_LEADER=69`, `ROLE_CM=55`, `ROLE_CM_LEADER=56`, `ROLE_TEACHER=36`, `ROLE_TEACHER_LEADER=37`, `ROLE_TA=54`). Phân quyền chi nhánh qua bảng `branch_has_user`, `role_has_user`.
- Log thay đổi: `App\Models\LogStudents`, `LogParents` (gọi khi cập nhật học sinh/phụ huynh).
- **Code mới:** vẫn dùng helper `u::` cho đồng nhất, nhưng **KHÔNG nội suy trực tiếp input user vào SQL** (code cũ đang dính SQL injection kiểu `WHERE id= $request->id`) → ép kiểu `(int)`, hoặc dùng `DB::select($sql, $bindings)` / `addslashes` cho chuỗi.
- Thay đổi schema: tạo migration mới trong `database/migrations/` (chỉ có vài migration gần đây; schema gốc không nằm trong migration) – không tự chạy (xem mục 0).

## 3. Bản đồ module (API `routes/api.php` → Controller → View Vue)
Tất cả API nằm trong `Route::group(['middleware'=>'api'])`, phần cần đăng nhập nằm trong `['middleware'=>'jwt.auth']`.

| Prefix API | Controller chính (`app/Http/Controllers/`) | View FE (`resources/js/src/views/`) | Router FE |
|---|---|---|---|
| `auth/*` | `AuthController` (login/logout/register/forgot/active) | `pages/login`, `pages/register` | `routers/user.js` |
| `system/*` | `SystemController`, `MenuController` | – | – |
| `user`, `users`, `roles`, `permissions`, `permission-groups` | `UserController`, `RolesController`, `PermissionsController`, `PermissionGroupsController` | `settings/{users,roles,permissions,permission_groups}`, `users/` | `setting.js`, `user.js` |
| `crm/*` | `ParentsController`, `StudentsController`, `ParentCareController`, `ImportsController`, `CheckinController` | `crm/{parents,care,imports}` | `crm.js` |
| `lms/students` | `StudentsController`, `ContractsController`, `EnrolmentsController`, `ChargesController` | `lms/{students,contracts,contracts-renew,enrolments,accounting}` | `lms.js` |
| `lms/evaluations/{ucrea,igbh,weekly,summative}` | `Lms/UcreaEvaluationController`, `Lms/IgbhEvaluationController`, `Lms/IgbhWeeklyEvaluationController`, `Lms/IgbhSummativeEvaluationController`, `Lms/IgbhClassSummativeEvaluationController`, `Lms/LmsTestConfigController` | `lms/evaluations` | `lms.js` |
| `lms/{exchanges,attendances,assessments,teachers}` | `ExchangesController`, `AttendancesController`, `AssessmentsController`, `TeachersController` | `lms/{exchange,attendances,assessments,teachers}` | `lms.js` |
| `lms/lesson-reports/*`, public `public/lesson-report/{token}` | `LessonReportsController` (nhận xét **theo tuần**: 1 phiếu/lớp/tuần T2–CN gắn vào **buổi cuối tuần** `lesson_reports.schedule_id`; `lesson_report_students.share_token` = link riêng từng HS, token lớp chỉ hiện nội dung bài; lộ trình `lesson_plans` (product × week_no) tự điền phiếu; điểm danh đọc/ghi `schedule_has_student`) | `lms/lesson-reports/{index,detail,ReportSheet,plans}`, trang PH `pages/LessonReportPublic.vue` (`/pages/lesson-report/:token`) | `lms.js` (`/lms/lesson-plans`), `router.js` |
| bảo lưu / chuyển lớp / chuyển CN / chuyển phí | `ReservesController`, `ClassTransfersController`, `BranchTransfersController`, `TuitionTransfersController` | `lms/{reserves,class_transfers,branch_transfers,tuition_transfers}` | `lms.js` |
| `lms/reports`, `lms/exports`, `export/*` | `ReportsController` (1.4k dòng), `ExportsController` (1.7k dòng) | `lms/report` | `lms.js` |
| `settings/*` | `ClassesController`, `TuitionFeesController`, `BranchesController`, `ProductsController`, `ProgramsController`, `RoomsController`, `ShiftsController`, `HolidaysController`, `SubjectsController`, `DiscountCodesController` | `settings/<tên>` | `setting.js` |
| `settings/sources/*` | `SourcesController` (bảng `sources` + `source_detail`; nguồn 1/2/3 do module Chiến dịch tự sinh → chỉ xem) | `settings/sources` | `setting.js` |
| `marketing/*` | `CampaignsController`, `CouponsController`, `B2BSourcesController`, `B2BCampaignsController`, `C2CCampaignsController` | `marketing/{campaigns,coupons,b2b,c2c}` | `marketing.js` |
| `dashboard/role/{init,data}`, `dashboard/*` (cũ) | `RoleDashboardController` (dashboard theo vai trò, dữ liệu thật: tab system/ceo/sale/cm/teacher/accountant; admin xem tất cả + chọn tài khoản, leader chọn thành viên team, role khác chỉ tab của mình; thực thu = `payments` theo `contracts.ec_id`), GV dùng `LessonReportsController@teacherTimetable`; `DashboardController` cũ (dashboard01-20) không còn dùng ở trang chủ | `DashboardAnalytics.vue` + `dashboard/{Overview,Sale,Cm,Accountant}Dashboard.vue`, `TeacherTimetable.vue`, `dashMixin.js` | – |
| `ai/*`, `ai/admin/*` | `AI/ChatController`, `AI/AdminController` → `Services/AI/{AiAgentService,HybridAiService,GeminiService,TextToSqlService,SkillRouter,FunctionExecutor}`, `Skills/*Skill.php` | – | – |

**Thêm 1 màn hình mới** thường cần sửa 4 chỗ: `routes/api.php` → Controller → `resources/js/src/views/<module>/<tên>/{index,add,edit}.vue` → `resources/js/src/routers/<module>.js` (meta: `breadcrumb`, `pageTitle`, `rule: 'editor'`).

## 4. Frontend
- Gọi API: helper trong `resources/js/src/http/axios.js` (`g` = GET, `p` = POST; tự gắn `Bearer localStorage.accessToken`, lỗi 401 → `processAuthen`).
- ACL: `resources/js/src/acl/acl.js` (vue-acl, rule `admin` / `editor`, role từ `localStorage.userInfo.userRole`).
- Store: `resources/js/src/store/` (+ `store/auth`), i18n: `resources/js/src/i18n/`, layout: `resources/js/src/layouts/main/Main.vue`.

## 5. Dữ liệu – bảng hay dùng
`contracts` (gói/hợp đồng học – bảng trung tâm), `students`, `crm_parents`, `crm_students`, `crm_customer_care`, `crm_import_parents`, `crm_student_checkin`, `users`, `roles`, `role_has_user`, `branch_has_user`, `branches`, `classes`, `schedules`, `schedule_has_student`, `sessions`, `term_student_user`, `products`, `programs`, `subjects`, `tuition_fee`, `tuition_fee_relation`, `payments`, `reserves`, `class_transfer`, `tuition_transfer`, `coupons`, `discount_codes`, `sources`, `report_*` (bảng tổng hợp báo cáo: `report_renews`, `report_full_fee_active`, `report_classes`, `report_reserve`, `report_pending`, `report_users`), `log_jobs`. Riêng STEMKEY: `lms_tests`, `ucrea_*`, `igbh_*` (đánh giá học sinh).
- Ngày sinh học sinh (`crm_students.birthday`, `students.date_of_birth`): chỉ biết năm → lưu `YYYY-01-01` + cờ `birthday_year_only=1`. Parse bằng `u::parseBirthday()` (BE) / `u.parseManualBirthday()` (FE), nhập bằng component `components/BirthdayInput.vue`, hiển thị bằng `u.formatBirthday(date, yearOnly)`.
- **Data kho CRM:** `crm_parents.is_lock=0` + quá `ParentsController::WAREHOUSE_DAYS` (10) ngày từ `last_assign_date` + chưa có hợp đồng `type>0` + không phải DS đen (`warehouseCond()`); cron `jobsProcessLockParent` 00:00 nhả kho; tab "Data kho" (`type_search=5`) xem toàn trung tâm, nhận về qua `parents/overwrite` (`parent_id`|`phone`).
- Import khách hàng (`ImportsController::assign`) tự đưa học sinh lên chính thức (`promoteImportedStudents`); xử lý bù: `php artisan crm:promoteImportedStudents [--run]`.
- Chuyển EC hợp đồng: Admin/Kế toán sửa EC/EC leader tại `/lms/contracts/:id/edit` → `ContractsController::syncContractEc()` cập nhật `payments.ec_id`, `report_{pending,reserve,renews}` theo `contract_id`; tuỳ chọn chuyển `term_student_user`. Doanh số báo cáo KD lấy `contracts.ec_id`.
- Đặt cọc vs Chờ xếp lớp: duyệt phiếu thu thiếu → `u::contractStatusByCharged()` (thu ≥ `config('app.deposit_min_rate')`=30% `must_charge` → status 3, dưới → 2); xử lý bù: `php artisan contracts:recalcDepositStatus [--run]`.

## 6. Cron (`app/Console/Kernel.php`)
`jobsSendEmail:command` (mỗi phút) · `scheduleHasStudent:process` 00:05 · `scheduleHasStudent:add` 01:00 · `updateEnrolmentLastDate:command` 01:00 · `processdata:command` 02:00 · `jobsProcessLockParent:command` 00:00 · `autoWithdraw:command` 03:00. Command nằm ở `app/Console/Commands/`.

## 7. Khác
- `agent_test/`, `.agents/{rules,skills,workflows}/demo*` là thử nghiệm, không phải code sản phẩm.
- Tài liệu: `docs/thuyet-trinh-san-pham.md`.
- Khi thêm module/bảng/cron mới → cập nhật file này 1–2 dòng.

