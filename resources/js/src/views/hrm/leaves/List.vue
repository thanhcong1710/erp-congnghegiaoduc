<template>
  <div id="page-hrm-leaves">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between mb-4">
      <div class="mb-2 md:mb-0">
        <h3 class="font-bold text-gray-800 flex items-center">
          <feather-icon icon="CalendarIcon" class="mr-2 text-primary" /> Quản Lý Nghỉ Phép & Quỹ Phép Toàn Bộ Nhân Sự
        </h3>
        <p class="text-sm text-gray-500 mt-1">Theo dõi quỹ phép năm của toàn thể cán bộ nhân viên, phê duyệt đơn nghỉ và đối soát công lương</p>
      </div>
      <div class="flex flex-wrap items-center">
        <vs-button class="p-3 mr-2" color="warning" type="filled" @click="openImportPopup">
          <i class="fa-solid fa-file-import mr-2"></i> Import Bảng Nghỉ Phép
        </vs-button>
        <vs-button class="p-3" color="success" type="border" @click="openPopup">
          <i class="fa-solid fa-plus mr-2"></i> Tạo Đơn Xin Phép
        </vs-button>
      </div>
    </div>

    <!-- Tabs chuyển đổi giữa Quản lý quỹ phép toàn nhân sự và Danh sách đơn -->
    <vs-tabs alignment="left" class="mt-4">
      
      <!-- ========================================== -->
      <!-- TAB 1: BẢNG TỔNG HỢP QUỸ PHÉP TOÀN NHÂN SỰ -->
      <!-- ========================================== -->
      <vs-tab label="Quản lý quỹ phép năm" icon-pack="feather" icon="icon-users">
        
        <!-- 4 Thẻ KPI thống kê chuẩn hệ thống -->
        <div class="vx-row mt-4 mb-4">
          <div class="vx-col w-full sm:w-1/2 md:w-1/4 mb-3 md:mb-0">
            <vx-card class="border-t-4 border-solid border-primary" no-shadow>
              <div class="flex items-center justify-between">
                <div>
                  <span class="text-xs font-semibold text-gray-600 uppercase block mb-1">Tổng số nhân sự</span>
                  <h2 class="font-bold text-primary">{{ stats.total_employees || 0 }} <span class="text-xs text-gray-500 font-normal">nhân sự</span></h2>
                </div>
                <div class="p-3 bg-primary-light rounded-full text-primary">
                  <feather-icon icon="UsersIcon" class="h-6 w-6" />
                </div>
              </div>
            </vx-card>
          </div>

          <div class="vx-col w-full sm:w-1/2 md:w-1/4 mb-3 md:mb-0">
            <vx-card class="border-t-4 border-solid border-warning" no-shadow>
              <div class="flex items-center justify-between">
                <div>
                  <span class="text-xs font-semibold text-gray-600 uppercase block mb-1">Tổng ngày đã nghỉ</span>
                  <h2 class="font-bold text-warning">{{ stats.total_company_used_days || 0 }} <span class="text-xs text-gray-500 font-normal">ngày</span></h2>
                </div>
                <div class="p-3 bg-warning-light rounded-full text-warning">
                  <feather-icon icon="CalendarIcon" class="h-6 w-6" />
                </div>
              </div>
            </vx-card>
          </div>

          <div class="vx-col w-full sm:w-1/2 md:w-1/4 mb-3 sm:mb-0">
            <vx-card class="border-t-4 border-solid border-danger" no-shadow>
              <div class="flex items-center justify-between">
                <div>
                  <span class="text-xs font-semibold text-gray-600 uppercase block mb-1">Nhân sự vượt hạn mức</span>
                  <h2 class="font-bold text-danger">{{ stats.over_limit_count || 0 }} <span class="text-xs text-gray-500 font-normal">nhân sự (>12d)</span></h2>
                </div>
                <div class="p-3 bg-danger-light rounded-full text-danger">
                  <feather-icon icon="AlertTriangleIcon" class="h-6 w-6" />
                </div>
              </div>
            </vx-card>
          </div>

          <div class="vx-col w-full sm:w-1/2 md:w-1/4">
            <vx-card class="border-t-4 border-solid border-gray-500" no-shadow>
              <div class="flex items-center justify-between">
                <div>
                  <span class="text-xs font-semibold text-gray-600 uppercase block mb-1">Đã hết số phép năm</span>
                  <h2 class="font-bold text-gray-700">{{ stats.out_of_leave_count || 0 }} <span class="text-xs text-gray-500 font-normal">nhân sự</span></h2>
                </div>
                <div class="p-3 bg-gray-100 rounded-full text-gray-600">
                  <feather-icon icon="ClockIcon" class="h-6 w-6" />
                </div>
              </div>
            </vx-card>
          </div>
        </div>

        <!-- Bảng danh sách & Bộ lọc -->
        <vx-card no-shadow>
          <!-- Toolbar Lọc chuẩn form control -->
          <div class="flex flex-wrap items-center justify-between mb-4 gap-2">
            <div class="flex flex-wrap items-center gap-3">
              <!-- Chọn năm -->
              <div class="flex items-center">
                <span class="text-sm font-semibold mr-2 text-gray-700 whitespace-no-wrap">Năm:</span>
                <select v-model="filterYear" @change="fetchAllBalances" class="filter-select" style="width: 100px;">
                  <option value="2026">2026</option>
                  <option value="2025">2025</option>
                  <option value="2024">2024</option>
                </select>
              </div>

              <!-- Chọn phòng ban -->
              <div class="flex items-center">
                <span class="text-sm font-semibold mr-2 text-gray-700 whitespace-no-wrap">Phòng ban:</span>
                <select v-model="filterDepartment" @change="fetchAllBalances" class="filter-select" style="min-width: 180px;">
                  <option value="all">-- Tất cả phòng ban --</option>
                  <option :value="dept.id" v-for="dept in departments" :key="dept.id">{{ dept.name }}</option>
                </select>
              </div>
            </div>

            <!-- Tìm kiếm & Nút xuất file -->
            <div class="flex items-center flex-wrap gap-2">
              <div class="relative">
                <input 
                  type="text" 
                  class="filter-input" 
                  style="min-width: 250px; padding-left: 32px;" 
                  placeholder="Tìm họ tên, mã nhân sự..." 
                  v-model="searchStaffKeyword" 
                  @input="onSearchStaffInput"
                >
                <i class="fa-solid fa-magnifying-glass absolute text-gray-400" style="left: 10px; top: 12px; font-size: 12px;"></i>
              </div>

              <button class="btn-tool-action" @click="fetchAllBalances" title="Tải lại dữ liệu">
                <feather-icon icon="RefreshCwIcon" class="h-4 w-4 text-gray-600" />
              </button>

              <button class="btn-excel-export" @click="exportToExcel" title="Xuất dữ liệu Excel">
                <i class="fa-solid fa-file-excel mr-1 text-success"></i> Xuất Excel
              </button>
            </div>
          </div>

          <!-- Bảng Danh Sách Quỹ Phép Toàn Bộ Nhân Sự (Khớp Bảng 2) -->
          <div class="vs-component vs-con-table stripe vs-table-primary overflow-x-auto">
            <div class="con-tablex vs-table--content">
              <div class="vs-con-tbody vs-table--tbody">
                <table class="vs-table vs-table--tbody-table w-full">
                  <thead class="vs-table--thead">
                    <tr>
                      <th style="width: 50px;" class="text-center">STT</th>
                      <th style="width: 100px;">Mã NS</th>
                      <th style="min-width: 180px;">Họ và tên</th>
                      <th style="min-width: 160px;">Phòng ban</th>
                      <th style="width: 110px;" class="text-center">Phép năm</th>
                      <th style="width: 110px;" class="text-center">Đã sử dụng</th>
                      <th style="width: 100px;" class="text-center">Còn lại</th>
                      <th style="width: 130px;" class="text-center">Tình trạng</th>
                      <th style="width: 120px;" class="text-center">Thao tác</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="tr-values vs-table--tr" v-for="(emp, index) in staffBalances.data" :key="emp.id">
                      <td class="td vs-table--td text-center text-gray-500 font-medium">
                        {{ index + 1 + ((staffBalances.pagination.current_page || 1) - 1) * (staffBalances.pagination.per_page || 20) }}
                      </td>
                      <td class="td vs-table--td">
                        <span class="emp-code-badge">{{ emp.employee_code }}</span>
                      </td>
                      <td class="td vs-table--td">
                        <div class="font-semibold text-gray-900 cursor-pointer hover:text-primary transition flex items-center flex-wrap" @click="viewEmployeeHistory(emp)">
                          <span>{{ emp.full_name }}</span>
                          <span
                            class="ml-1.5 text-xs px-1.5 py-0.5 rounded font-bold"
                            :class="emp.is_fulltime ? 'bg-blue-50 text-primary border border-blue-200' : 'bg-gray-100 text-gray-600'"
                          >
                            {{ emp.is_fulltime ? 'Fulltime' : (emp.contract_type || 'CTV') }}
                          </span>
                          <span v-if="emp.work_status === 'resigned'" class="ml-1 text-xs text-danger font-normal bg-red-50 px-1 py-0.5 rounded border border-red-200">Đã thôi việc</span>
                        </div>
                      </td>
                      <td class="td vs-table--td text-sm text-gray-600">
                        {{ emp.department_name }}
                      </td>
                      <td class="td vs-table--td text-center font-semibold text-gray-700">
                        <div class="flex items-center justify-center">
                          <span class="font-bold text-gray-800" style="font-size: 14px;">
                            {{ emp.total_days }} ngày
                          </span>
                          <button
                            v-if="canAdjustBalance"
                            class="ml-1 text-primary hover:text-blue-700 p-1 rounded hover:bg-blue-50 transition"
                            @click.stop="openAdjustBalancePopup(emp)"
                            title="Điều chỉnh số ngày phép"
                          >
                            <i class="fa-solid fa-pen-to-square" style="font-size: 12px;"></i>
                          </button>
                        </div>
                        <div v-if="emp.balance_note" class="text-xs text-blue-600 font-medium truncate max-w-xs mx-auto" :title="emp.balance_note">
                          <i class="fa-solid fa-note-sticky text-xs mr-0.5"></i> {{ emp.balance_note }}
                        </div>
                        <div v-else-if="!emp.is_fulltime && emp.total_days == 0" class="text-xs text-gray-400 italic">
                          (CTV mặc định 0d)
                        </div>
                      </td>
                      <td class="td vs-table--td text-center font-bold text-warning" style="font-size: 14px;">
                        {{ emp.used_days }} ngày
                      </td>
                      <td class="td vs-table--td text-center font-bold" :class="emp.total_days > 0 ? (emp.remaining_days > 0 ? 'text-success' : 'text-danger') : 'text-gray-400'" style="font-size: 14px;">
                        <span v-if="emp.total_days > 0 || emp.is_fulltime">{{ emp.remaining_days }} ngày</span>
                        <span v-else class="text-xs text-gray-400 font-normal">---</span>
                      </td>
                      <td class="td vs-table--td text-center">
                        <template v-if="emp.total_days > 0 || emp.is_fulltime">
                          <span v-if="emp.is_over_limit" class="status-tag status-danger">
                            Vượt +{{ emp.over_days }} ngày
                          </span>
                          <span v-else-if="emp.is_out_of_leave" class="status-tag status-warning">
                            Đã hết phép
                          </span>
                          <span v-else-if="emp.used_days > 0" class="status-tag status-success">
                            Bình thường
                          </span>
                          <span v-else class="status-tag status-gray">
                            Chưa nghỉ
                          </span>
                        </template>
                        <template v-else>
                          <span class="status-tag status-gray" title="Chưa có định mức phép năm">
                            Không áp dụng
                          </span>
                        </template>
                      </td>
                      <td class="td vs-table--td text-center whitespace-no-wrap">
                        <button 
                          class="btn-detail-history"
                          @click="viewEmployeeHistory(emp)"
                          title="Xem chi tiết các đợt nghỉ phép"
                        >
                          <i class="fa-solid fa-clock-rotate-left mr-1"></i> Chi tiết
                        </button>
                        <button 
                          v-if="canAdjustBalance"
                          class="btn-adjust-action ml-1.5"
                          @click="openAdjustBalancePopup(emp)"
                          title="Điều chỉnh quỹ phép năm nhân sự"
                        >
                          <i class="fa-solid fa-sliders mr-1 text-primary"></i> Sửa phép
                        </button>
                      </td>
                    </tr>
                    <tr v-if="!staffBalances.data || !staffBalances.data.length">
                      <td colspan="9" class="text-center p-4 text-gray-500">Không tìm thấy dữ liệu nhân sự</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Phân trang nhân sự -->
          <div class="flex flex-wrap items-center justify-between mt-5 pt-3 border-t border-gray-100" v-if="staffBalances.pagination.total > staffBalances.pagination.per_page">
            <div class="text-sm text-gray-500">
              Hiển thị {{ staffBalances.data.length }} / {{ staffBalances.pagination.total }} nhân sự
            </div>
            <vs-pagination
              :total="staffBalances.pagination.last_page"
              v-model="staffBalances.pagination.current_page"
              @change="changeStaffPage"
            />
          </div>
        </vx-card>
      </vs-tab>

      <!-- ========================================== -->
      <!-- TAB 2: DANH SÁCH ĐƠN XIN NGHỈ & PHÊ DUYỆT -->
      <!-- ========================================== -->
      <vs-tab label="Danh sách đơn xin nghỉ" icon-pack="feather" icon="icon-file-text">
        <vx-card no-shadow class="mt-4">
          <!-- Bộ lọc và tìm kiếm -->
          <div class="flex flex-wrap items-center justify-between mb-4">
            <div class="flex items-center mb-2 md:mb-0">
              <span class="text-sm font-medium mr-2 text-gray-700">Trạng thái:</span>
              <select v-model="filterStatus" @change="fetchLeaves" class="filter-select" style="min-width: 170px;">
                <option value="all">-- Tất cả trạng thái --</option>
                <option value="0">Chờ duyệt</option>
                <option value="1">Đã phê duyệt</option>
                <option value="2">Bị từ chối</option>
              </select>
            </div>

            <div class="mb-2 md:mb-0">
              <input 
                type="text" 
                class="filter-input" 
                style="min-width: 250px;" 
                placeholder="Tìm theo tên nhân viên..." 
                v-model="searchKeyword" 
                @input="fetchLeaves"
              >
            </div>
          </div>

          <!-- Table danh sách đơn -->
          <div class="vs-component vs-con-table stripe vs-table-primary overflow-x-auto">
            <div class="con-tablex vs-table--content">
              <div class="vs-con-tbody vs-table--tbody">
                <table class="vs-table vs-table--tbody-table w-full">
                  <thead class="vs-table--thead">
                    <tr>
                      <th width="3%" class="text-center">STT</th>
                      <th width="8%">Mã NS</th>
                      <th width="12%">Người làm đơn</th>
                      <th width="9%">Loại yêu cầu</th>
                      <th width="13%">Thời gian xin phép</th>
                      <th width="5%" class="text-center">Số ngày</th>
                      <th width="10%" class="text-center">Thời gian tạo</th>
                      <th width="8%" class="text-center">Trạng thái</th>
                      <th width="11%">Chế độ duyệt</th>
                      <th width="11%" class="text-center">Thời gian duyệt</th>
                      <th class="text-center" style="min-width: 150px; width: 150px; white-space: nowrap;">Thao tác</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="tr-values vs-table--tr" v-for="(tr, index) in leaves.data" :key="index">
                      <td class="td vs-table--td text-center">{{ index + 1 + ((leaves.current_page || 1) - 1) * (leaves.per_page || 20) }}</td>
                      <td class="td vs-table--td font-bold text-primary">{{ tr.employee ? tr.employee.employee_code : (tr.user ? (tr.user.hrm_id || '---') : '') }}</td>
                      <td class="td vs-table--td font-semibold text-gray-900">{{ tr.employee ? tr.employee.full_name : (tr.user ? tr.user.name : 'Unknown') }}</td>
                      <td class="td vs-table--td">
                        <vs-chip :color="getRequestCategoryColor(tr.request_category)">
                          {{ getRequestCategoryText(tr.request_category) }}
                        </vs-chip>
                      </td>
                      <td class="td vs-table--td text-sm">
                        <div>{{ tr.start_date | formatDateView }} <i class="fa fa-arrow-right text-xs text-gray-400"></i> {{ tr.end_date | formatDateView }}</div>
                        <div class="text-xs text-gray-500 truncate" :title="tr.reason">Lý do: {{ tr.reason }}</div>
                      </td>
                      <td class="td vs-table--td text-center font-bold text-gray-800">{{ tr.duration_days }}</td>

                      <!-- Thời gian tạo -->
                      <td class="td vs-table--td text-center text-xs whitespace-no-wrap">
                        <div class="font-semibold text-gray-800">{{ formatDateTime(tr.created_at) }}</div>
                      </td>

                      <td class="td vs-table--td text-center">
                        <vs-chip :color="tr.status === 1 ? 'success' : (tr.status === 2 ? 'danger' : 'warning')">
                          {{ tr.status === 1 ? 'Đã duyệt' : (tr.status === 2 ? 'Từ chối' : 'Chờ duyệt') }}
                        </vs-chip>
                      </td>
                      <td class="td vs-table--td">
                        <span v-if="tr.status === 1" class="font-semibold text-xs" :class="getApprovalTypeClass(tr.approval_type)">
                          {{ getApprovalTypeText(tr.approval_type) }}
                        </span>
                        <div v-else-if="tr.status === 2" class="text-danger text-xs">
                          <div class="font-semibold flex items-center">
                            <i class="fa-solid fa-circle-xmark mr-1"></i> Từ chối
                          </div>
                          <div v-if="tr.rejection_reason" class="text-gray-500 italic mt-0.5 truncate max-w-xs" :title="tr.rejection_reason">
                            Ghi chú: {{ tr.rejection_reason }}
                          </div>
                        </div>
                        <span v-else class="text-gray-400 text-xs italic">Chờ duyệt</span>
                      </td>

                      <!-- Thời gian duyệt -->
                      <td class="td vs-table--td text-center text-xs whitespace-no-wrap">
                        <div v-if="tr.status !== 0">
                          <div class="font-semibold text-gray-800">{{ formatDateTime(tr.approved_at || tr.updated_at) }}</div>
                          <div v-if="tr.manager_approver || tr.hr_approver" class="text-gray-500 text-xs mt-0.5 truncate max-w-xs" :title="'Người duyệt: ' + ((tr.manager_approver ? tr.manager_approver.name : '') || (tr.hr_approver ? tr.hr_approver.name : ''))">
                            Bởi: {{ (tr.manager_approver ? tr.manager_approver.name : '') || (tr.hr_approver ? tr.hr_approver.name : '') }}
                          </div>
                        </div>
                        <span v-else class="text-gray-400 text-xs italic">---</span>
                      </td>

                      <td class="td vs-table--td text-center list-action" style="min-width: 150px; width: 150px; white-space: nowrap;">
                        <div class="flex items-center justify-center space-x-2" v-if="canApprove && tr.status === 0" style="white-space: nowrap;">
                          <vs-button 
                            size="small" 
                            color="success" 
                            type="filled"
                            class="px-3 py-1.5 whitespace-nowrap text-xs font-semibold inline-flex items-center"
                            style="white-space: nowrap;"
                            @click="openApprovePopup(tr)"
                            title="Phê duyệt đơn"
                          >
                            <i class="fa-solid fa-check mr-1"></i> Duyệt
                          </vs-button>
                          <vs-button 
                            size="small" 
                            color="danger" 
                            type="border"
                            class="px-3 py-1.5 whitespace-nowrap text-xs font-semibold inline-flex items-center"
                            style="white-space: nowrap;"
                            @click="openRejectPopup(tr)"
                            title="Từ chối đơn"
                          >
                            <i class="fa-solid fa-ban mr-1"></i> Từ chối
                          </vs-button>
                        </div>
                        <span v-else class="text-xs text-gray-400">---</span>
                      </td>
                    </tr>
                    <tr v-if="!leaves.data || !leaves.data.length">
                      <td colspan="11" class="text-center p-4 text-gray-500">Không có dữ liệu đơn nghỉ phép</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Phân trang đơn nghỉ -->
          <div class="flex flex-wrap items-center mt-5" v-if="leaves.total > leaves.per_page">
            <vs-pagination
              :total="Math.ceil(leaves.total / leaves.per_page)"
              v-model="leaves.current_page"
              @change="changePage"
            />
          </div>
        </vx-card>
      </vs-tab>
    </vs-tabs>

    <!-- ========================================== -->
    <!-- POPUP 1: CHI TIẾT LỊCH SỬ NGHỈ PHÉP NHÂN SỰ -->
    <!-- ========================================== -->
    <vs-popup 
      :title="popupHistoryTitle" 
      :active.sync="popupHistoryActive" 
      class="popup-leave-history modal-custom-leave"
    >
      <div class="popup-history-container" v-if="selectedStaffHistory">
        <!-- Thông tin nhân sự -->
        <div class="emp-info-banner mb-4">
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <div class="avatar-box mr-3">
                <i class="fa-solid fa-user text-primary text-xl"></i>
              </div>
              <div>
                <h4 class="font-bold text-gray-900 leading-tight">{{ selectedStaffHistory.employee.full_name }}</h4>
                <div class="text-xs text-gray-500 mt-0.5">
                  Mã NS: <span class="font-semibold text-primary">{{ selectedStaffHistory.employee.employee_code }}</span>
                  <span class="mx-2">•</span>
                  Phòng ban: <span class="font-semibold text-gray-700">{{ selectedStaffHistory.employee.department_name }}</span>
                </div>
              </div>
            </div>
            <div>
              <span v-if="selectedStaffHistory.employee.work_status === 'resigned'" class="status-tag status-danger">
                Đã thôi việc
              </span>
              <span v-else class="status-tag status-success">
                Đang làm việc
              </span>
            </div>
          </div>
        </div>

        <!-- Banner lưu ý cho nhân sự CTV / không phải fulltime -->
        <div v-if="!selectedStaffHistory.summary.is_fulltime && !selectedStaffHistory.summary.total_days" class="p-3 bg-amber-50 border border-amber-200 rounded text-xs text-amber-800 mb-4 flex items-center">
          <i class="fa-solid fa-circle-info mr-2 text-base text-warning"></i>
          <span>Nhân sự thuộc diện <strong>Cộng tác viên (CTV) / Bán thời gian</strong>, không có định mức nghỉ phép năm 12 ngày. Các ngày nghỉ phép không tính vào quỹ phép năm.</span>
        </div>

        <!-- 4 Card Tóm tắt số ngày phép của nhân viên -->
        <div class="grid grid-cols-4 gap-3 mb-5">
          <div class="summary-card card-blue">
            <span class="summary-label">Quỹ phép {{ selectedStaffHistory.summary.year }}</span>
            <span class="summary-value text-primary">
              <span v-if="selectedStaffHistory.summary.is_fulltime || selectedStaffHistory.summary.total_days > 0">{{ selectedStaffHistory.summary.total_days }} <small class="text-xs font-normal">ngày</small></span>
              <span v-else class="text-sm font-semibold text-gray-500">0 ngày (CTV)</span>
            </span>
          </div>

          <div class="summary-card card-orange">
            <span class="summary-label">Đã sử dụng</span>
            <span class="summary-value text-warning">{{ selectedStaffHistory.summary.used_days }} <small class="text-xs font-normal">ngày</small></span>
          </div>

          <div class="summary-card card-green">
            <span class="summary-label">Số ngày còn lại</span>
            <span class="summary-value text-success">
              <span v-if="selectedStaffHistory.summary.is_fulltime || selectedStaffHistory.summary.total_days > 0">{{ selectedStaffHistory.summary.remaining_days }} <small class="text-xs font-normal">ngày</small></span>
              <span v-else class="text-gray-400 font-normal">---</span>
            </span>
          </div>

          <div class="summary-card" :class="(selectedStaffHistory.summary.is_fulltime || selectedStaffHistory.summary.total_days > 0) && selectedStaffHistory.summary.over_days > 0 ? 'card-red' : 'card-gray'">
            <span class="summary-label">Vượt hạn mức</span>
            <span class="summary-value" :class="(selectedStaffHistory.summary.is_fulltime || selectedStaffHistory.summary.total_days > 0) && selectedStaffHistory.summary.over_days > 0 ? 'text-danger' : 'text-gray-400'">
              <span v-if="selectedStaffHistory.summary.is_fulltime || selectedStaffHistory.summary.total_days > 0">{{ selectedStaffHistory.summary.over_days }} <small class="text-xs font-normal">ngày</small></span>
              <span v-else class="text-gray-400 font-normal">---</span>
            </span>
          </div>
        </div>

        <!-- Tiêu đề danh sách -->
        <div class="flex items-center justify-between mb-2">
          <h5 class="font-bold text-gray-800 text-sm flex items-center">
            <i class="fa-solid fa-calendar-days mr-2 text-primary"></i> 
            Chi tiết các đợt nghỉ phép ({{ selectedStaffHistory.leaves.length }} đợt)
          </h5>
        </div>

        <!-- Table chi tiết các đợt nghỉ -->
        <div class="history-table-wrapper">
          <table class="history-table">
            <thead>
              <tr>
                <th style="width: 45px;" class="text-center">STT</th>
                <th style="width: 130px;">Thời gian nghỉ</th>
                <th style="width: 80px;" class="text-center">Số ngày</th>
                <th style="min-width: 150px;">Lý do / Ghi chú</th>
                <th style="width: 130px;" class="text-center">Thời gian tạo</th>
                <th style="width: 150px;">Chế độ phê duyệt</th>
                <th style="width: 130px;" class="text-center">Thời gian duyệt</th>
                <th style="width: 95px;" class="text-center">Trạng thái</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(lv, idx) in selectedStaffHistory.leaves" :key="lv.id">
                <td class="text-center text-gray-500 font-medium">{{ idx + 1 }}</td>
                <td>
                  <div class="font-semibold text-gray-900 text-sm">{{ lv.start_date }}</div>
                  <div class="text-xs text-gray-400" v-if="lv.start_date !== lv.end_date">đến {{ lv.end_date }}</div>
                </td>
                <td class="text-center">
                  <span class="day-badge">{{ lv.duration_days }}</span>
                </td>
                <td class="text-sm text-gray-700">
                  {{ lv.reason || '---' }}
                </td>
                <td class="text-center text-xs whitespace-no-wrap">
                  <span class="text-gray-700 font-medium">{{ formatDateTime(lv.created_at) }}</span>
                </td>
                <td>
                  <span v-if="lv.approval_type === 'approved'" class="mode-badge mode-approved">
                    <i class="fa-solid fa-circle-check mr-1"></i> Hưởng 100% lương
                  </span>
                  <span v-else-if="lv.approval_type === 'late_unpaid'" class="mode-badge mode-late">
                    <i class="fa-solid fa-triangle-exclamation mr-1"></i> Gửi muộn (trừ lương)
                  </span>
                  <span v-else-if="lv.approval_type === 'half_salary'" class="mode-badge mode-half">
                    <i class="fa-solid fa-circle-half-stroke mr-1"></i> Hưởng 50% lương
                  </span>
                  <span v-else-if="lv.approval_type === 'rejected'" class="mode-badge mode-rejected">
                    <i class="fa-solid fa-circle-xmark mr-1"></i> Từ chối
                  </span>
                  <span v-else class="text-gray-400 text-xs">---</span>
                </td>
                <td class="text-center text-xs whitespace-no-wrap">
                  <span v-if="lv.status !== 0" class="text-gray-700 font-medium">{{ formatDateTime(lv.approved_at || lv.updated_at) }}</span>
                  <span v-else class="text-gray-400 italic">---</span>
                </td>
                <td class="text-center">
                  <span v-if="lv.status === 1" class="status-tag status-success">Đã duyệt</span>
                  <span v-else-if="lv.status === 2" class="status-tag status-danger">Từ chối</span>
                  <span v-else class="status-tag status-warning">Chờ duyệt</span>
                </td>
              </tr>
              <tr v-if="!selectedStaffHistory.leaves.length">
                <td colspan="8" class="text-center p-4 text-gray-400 text-sm">
                  Chưa có lịch sử nghỉ phép nào trong năm này
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Footer button -->
        <div class="flex justify-end mt-4 pt-3 border-t border-gray-100">
          <button class="btn-close-modal" @click="popupHistoryActive = false">
            <i class="fa-solid fa-xmark mr-1"></i> Đóng
          </button>
        </div>
      </div>
    </vs-popup>

    <!-- ========================================== -->
    <!-- POPUP 2: IMPORT BẢNG NGHỈ PHÉP TỰ ĐỘNG -->
    <!-- ========================================== -->
    <vs-popup title="IMPORT BẢNG KẾ HOẠCH NGHỈ PHÉP TỪ EXCEL" :active.sync="popupImportActive" class="modal-custom-leave">
      <div class="p-3">
        <p class="text-sm text-gray-600 mb-2">
          Dán các dòng copy trực tiếp từ Excel / Google Sheet vào khung dưới đây theo thứ tự cột:
          <br>
          <code class="text-xs bg-gray-100 p-1.5 rounded font-mono text-primary font-bold inline-block mt-1">
            [Họ và tên] [Tab] [Số ngày] [Tab] [Ngày] [Tab] [Chức vụ] [Tab] [Phòng ban] [Tab] [Ghi chú]
          </code>
        </p>

        <textarea 
          rows="7" 
          class="filter-input w-full font-mono text-xs" 
          placeholder="Dán dữ liệu các dòng copy từ Excel tại đây..."
          v-model="rawImportText"
          @input="parseImportText"
        ></textarea>

        <div class="mt-3" v-if="parsedRows.length > 0">
          <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-semibold text-success">
              <i class="fa-solid fa-check-circle mr-1"></i> Đã nhận diện {{ parsedRows.length }} dòng dữ liệu hợp lệ:
            </span>
          </div>

          <div style="max-height: 200px; overflow-y: auto;" class="border rounded-lg border-gray-200">
            <table class="w-full text-xs">
              <thead class="bg-gray-100 text-gray-600">
                <tr>
                  <th class="p-2 text-center" style="width: 40px;">#</th>
                  <th class="p-2 text-left">Họ tên</th>
                  <th class="p-2 text-center">Số ngày</th>
                  <th class="p-2 text-left">Ngày</th>
                  <th class="p-2 text-left">Phòng ban</th>
                  <th class="p-2 text-left">Ghi chú</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(row, rIdx) in parsedRows.slice(0, 50)" :key="rIdx" class="border-t border-gray-100">
                  <td class="p-2 text-center text-gray-400">{{ rIdx + 1 }}</td>
                  <td class="p-2 font-semibold text-gray-800">{{ row.name }}</td>
                  <td class="p-2 text-center font-bold text-primary">{{ row.days }}</td>
                  <td class="p-2 text-gray-600">{{ row.date }}</td>
                  <td class="p-2 text-gray-600">{{ row.dept }}</td>
                  <td class="p-2 text-gray-500">{{ row.note }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <p class="text-xs text-gray-400 mt-1" v-if="parsedRows.length > 50">
            ... và {{ parsedRows.length - 50 }} dòng khác.
          </p>
        </div>

        <div class="flex justify-end mt-4 pt-3 border-t border-gray-100">
          <vs-button 
            color="success" 
            type="filled" 
            class="mr-2 p-2.5 px-4"
            :disabled="!parsedRows.length || isImporting"
            @click="submitImport"
          >
            <i class="fa-solid fa-cloud-arrow-up mr-1"></i> {{ isImporting ? 'Đang Import...' : 'Xác Nhận Import' }}
          </vs-button>
          <button class="btn-close-modal" @click="popupImportActive = false">Hủy</button>
        </div>
      </div>
    </vs-popup>

    <!-- ========================================== -->
    <!-- POPUP 3: TẠO ĐƠN XIN NGHỈ (HIỆN CÓ) -->
    <!-- ========================================== -->
    <vs-popup title="TẠO ĐƠN XIN NGHỈ / ĐỔI CA" :active.sync="popupActive">
      <div class="p-4">
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Loại yêu cầu (*)</label>
            <select class="filter-select w-full" v-model="form.request_category">
              <option value="leave">Xin nghỉ phép thường</option>
              <option value="shift_change">Xin đổi ca làm việc</option>
              <option value="off_to_onl">Xin chuyển làm Off sang Onl (Hưởng 50% lương)</option>
            </select>
          </div>
        </div>

        <div class="vx-row mb-4" v-if="form.request_category === 'leave'">
          <div class="vx-col w-full">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Loại nghỉ phép</label>
            <select class="filter-select w-full" v-model="form.leave_type_id">
              <option :value="item.id" v-for="item in leaveTypes" :key="item.id">{{ item.name }}</option>
            </select>
          </div>
        </div>

        <div class="vx-row mb-4">
          <div class="vx-col w-1/3">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Từ ngày (*)</label>
            <flat-pickr v-model="form.start_date" :config="flatpickrDateConfig" @on-change="calculateDays" class="filter-input w-full" placeholder="dd/mm/yyyy" />
          </div>
          <div class="vx-col w-1/3">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Đến ngày (*)</label>
            <flat-pickr v-model="form.end_date" :config="flatpickrDateConfig" @on-change="calculateDays" class="filter-input w-full" placeholder="dd/mm/yyyy" />
          </div>
          <div class="vx-col w-1/3">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Số ngày (*)</label>
            <input type="number" step="0.5" class="filter-input w-full" v-model="form.duration_days" />
          </div>
        </div>

        <div class="vx-row mb-4" v-if="form.request_category === 'shift_change'">
          <div class="vx-col w-full">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Thông tin ca xin đổi</label>
            <input type="text" placeholder="Ví dụ: Xin đổi từ ca Sáng sang ca Chiều ngày 15/10" class="filter-input w-full" v-model="form.target_shift_info" />
          </div>
        </div>

        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Lý do chi tiết (*)</label>
            <textarea placeholder="Nhập lý do xin nghỉ hoặc đổi ca..." rows="3" class="filter-input w-full" v-model="form.reason"></textarea>
          </div>
        </div>

        <div class="vx-row mt-6">
          <div class="vx-col w-full flex justify-end">
            <vs-button @click="saveData()" color="success" type="filled" class="p-2.5 px-4 mr-2"><i class="fa-solid fa-paper-plane mr-1"></i> Gửi Đơn</vs-button>
            <button class="btn-close-modal" @click="popupActive = false">Hủy</button>
          </div>
        </div>
      </div>
    </vs-popup>

    <!-- ========================================== -->
    <!-- POPUP 4: PHÊ DUYỆT ĐƠN NGHỈ PHÉP (HIỆN CÓ) -->
    <!-- ========================================== -->
    <vs-popup title="PHÊ DUYỆT ĐƠN NGHỈ PHÉP" :active.sync="approvePopupActive">
      <div class="p-4" v-if="selectedLeave">
        <div class="mb-4 text-sm text-gray-700 bg-gray-50 p-3 rounded border border-gray-200">
          <div>Phê duyệt đơn của nhân sự: <strong class="text-primary">{{ selectedLeave.employee ? selectedLeave.employee.full_name : (selectedLeave.user ? selectedLeave.user.name : '') }}</strong> 
          ({{ selectedLeave.duration_days }} ngày: {{ selectedLeave.start_date }} đến {{ selectedLeave.end_date }})</div>
          <div class="text-xs text-gray-500 mt-1">Thời gian tạo đơn: <strong class="text-gray-700">{{ formatDateTime(selectedLeave.created_at) }}</strong></div>
        </div>

        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <label class="text-sm font-semibold text-gray-800 mb-2 block">Lựa chọn phân loại phê duyệt của HR (*)</label>
            <div class="space-y-2">
              <label class="flex items-center p-3 border rounded-lg cursor-pointer transition hover:bg-gray-50" :class="{'border-success bg-green-50': approveForm.approval_type === 'approved'}">
                <input type="radio" value="approved" v-model="approveForm.approval_type" class="mr-3" />
                <div>
                  <span class="font-bold text-success block">Đã duyệt</span>
                  <span class="text-xs text-gray-500">Hưởng 100% lương / Tự động trừ vào số ngày phép năm</span>
                </div>
              </label>

              <label class="flex items-center p-3 border rounded-lg cursor-pointer transition hover:bg-gray-50" :class="{'border-warning bg-orange-50': approveForm.approval_type === 'half_salary'}">
                <input type="radio" value="half_salary" v-model="approveForm.approval_type" class="mr-3" />
                <div>
                  <span class="font-bold text-warning block">50% lương</span>
                  <span class="text-xs text-gray-500">Áp dụng cho nhân sự xin chuyển làm Off sang Onl</span>
                </div>
              </label>

              <label class="flex items-center p-3 border rounded-lg cursor-pointer transition hover:bg-gray-50" :class="{'border-gray-500 bg-gray-50': approveForm.approval_type === 'late_unpaid'}">
                <input type="radio" value="late_unpaid" v-model="approveForm.approval_type" class="mr-3" />
                <div>
                  <span class="font-bold text-gray-700 block">Gửi đơn muộn (không lương)</span>
                  <span class="text-xs text-gray-500">Nhân viên gửi đơn muộn sau hạn quy định, không tính lương</span>
                </div>
              </label>

              <label class="flex items-center p-3 border rounded-lg cursor-pointer transition hover:bg-gray-50" :class="{'border-danger bg-red-50': approveForm.approval_type === 'rejected'}">
                <input type="radio" value="rejected" v-model="approveForm.approval_type" class="mr-3" />
                <div>
                  <span class="font-bold text-danger block">Từ chối đơn</span>
                  <span class="text-xs text-gray-500">Không đồng ý phê duyệt đơn</span>
                </div>
              </label>
            </div>
          </div>
        </div>

        <div class="vx-row mb-4" v-if="approveForm.approval_type === 'rejected'">
          <div class="vx-col w-full">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Ghi chú / Lý do từ chối (*)</label>
            <textarea placeholder="Nhập ghi chú lý do từ chối đơn..." rows="3" class="filter-input w-full" v-model="approveForm.rejection_reason"></textarea>
          </div>
        </div>

        <div class="vx-row mt-6">
          <div class="vx-col w-full flex justify-end">
            <vs-button
              @click="submitApproval()"
              :color="approveForm.approval_type === 'rejected' ? 'danger' : 'success'"
              type="filled"
              class="p-2.5 px-4 mr-2"
            >
              <i :class="approveForm.approval_type === 'rejected' ? 'fa-solid fa-ban mr-1' : 'fa-solid fa-check mr-1'"></i>
              {{ approveForm.approval_type === 'rejected' ? 'Xác Nhận Từ Chối' : 'Xác Nhận Duyệt' }}
            </vs-button>
            <button class="btn-close-modal" @click="approvePopupActive = false">Đóng</button>
          </div>
        </div>
      </div>
    </vs-popup>

    <!-- ========================================== -->
    <!-- POPUP TỪ CHỐI ĐƠN NGHỈ PHÉP TRỰC TIẾP -->
    <!-- ========================================== -->
    <vs-popup title="TỪ CHỐI ĐƠN NGHỈ PHÉP" :active.sync="rejectPopupActive">
      <div class="p-4" v-if="targetRejectLeave">
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-xs text-red-800">
          Từ chối đơn của nhân sự: <strong>{{ targetRejectLeave.employee ? targetRejectLeave.employee.full_name : (targetRejectLeave.user ? targetRejectLeave.user.name : '') }}</strong>
          ({{ targetRejectLeave.duration_days }} ngày: {{ targetRejectLeave.start_date }} đến {{ targetRejectLeave.end_date }}).
        </div>
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Ghi chú / Lý do từ chối (*)</label>
            <textarea
              placeholder="Nhập ghi chú lý do từ chối đơn nghỉ phép..."
              rows="3"
              class="filter-input w-full"
              v-model="rejectReason"
            ></textarea>
          </div>
        </div>
        <div class="vx-row mt-6">
          <div class="vx-col w-full flex justify-end">
            <vs-button @click="submitDirectReject()" color="danger" type="filled" class="p-2.5 px-4 mr-2">
              <i class="fa-solid fa-ban mr-1"></i> Xác Nhận Từ Chối
            </vs-button>
            <button class="btn-close-modal" @click="rejectPopupActive = false">Hủy</button>
          </div>
        </div>
      </div>
    </vs-popup>

    <!-- ========================================== -->
    <!-- POPUP ĐIỀU CHỈNH QUỸ PHÉP NĂM NHÂN SỰ -->
    <!-- ========================================== -->
    <vs-popup 
      class="modal-custom-leave modal-adjust-balance" 
      :title="adjustPopupTitle" 
      :active.sync="adjustPopupActive"
    >
      <div class="p-2" v-if="adjustingEmployee">
        <!-- Card thông tin nhân viên -->
        <div class="p-3 mb-4 rounded-lg bg-gray-50 border border-gray-200">
          <div class="flex flex-wrap items-center justify-between">
            <div class="flex items-center space-x-2 flex-wrap">
              <span class="emp-code-badge">{{ adjustingEmployee.employee_code }}</span>
              <strong class="text-base text-gray-900 ml-1">{{ adjustingEmployee.full_name }}</strong>
              <span class="text-xs px-2 py-0.5 rounded font-bold bg-blue-50 text-primary border border-blue-200 ml-1">
                {{ adjustingEmployee.department_name }}
              </span>
              <span class="text-xs px-2 py-0.5 rounded font-semibold bg-gray-200 text-gray-700 ml-1">
                {{ adjustingEmployee.is_fulltime ? 'Fulltime' : (adjustingEmployee.contract_type || 'CTV') }}
              </span>
            </div>
            <div class="text-xs text-gray-600 mt-2 sm:mt-0">
              <span v-if="adjustingEmployee.start_date">
                <i class="fa-solid fa-calendar-check text-primary mr-1"></i>
                Vào làm: <strong>{{ formatDate(adjustingEmployee.start_date) }}</strong>
                <span class="text-primary font-semibold ml-1">({{ getWorkingMonthsNote(adjustingEmployee.start_date) }})</span>
              </span>
              <span v-else class="text-gray-400 italic">Chưa có ngày bắt đầu làm việc</span>
            </div>
          </div>
        </div>

        <!-- Form điều chỉnh -->
        <div class="vx-row mb-4">
          <!-- Năm -->
          <div class="vx-col w-full sm:w-1/3 mb-3 sm:mb-0">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Năm áp dụng (*)</label>
            <input type="number" class="filter-input w-full bg-gray-100 font-semibold" v-model="adjustForm.year" readonly />
          </div>

          <!-- Số ngày phép được hưởng -->
          <div class="vx-col w-full sm:w-2/3">
            <label class="text-sm font-semibold text-gray-700 block mb-1">
              Số ngày phép năm được hưởng (*)
              <span class="text-xs font-normal text-gray-500 ml-1">(Hỗ trợ số lẻ như 2.5, 3.5...)</span>
            </label>
            <div class="flex items-center">
              <input 
                type="number" 
                step="0.5" 
                min="0" 
                max="365" 
                class="filter-input w-full font-bold text-primary text-base" 
                v-model.number="adjustForm.total_days" 
                placeholder="Nhập số ngày phép..."
              />
              <span class="ml-2 font-semibold text-gray-600 whitespace-no-wrap">ngày</span>
            </div>
          </div>
        </div>

        <!-- Các phím tắt chọn nhanh -->
        <div class="mb-4 p-3 bg-blue-50 border border-blue-100 rounded-lg">
          <label class="text-xs font-bold text-blue-900 uppercase block mb-2">
            <i class="fa-solid fa-wand-magic-sparkles mr-1"></i> Gợi ý chọn nhanh định mức phép:
          </label>
          <div class="flex flex-wrap gap-2">
            <button 
              type="button" 
              class="quick-preset-btn"
              @click="setQuickDays(2, 'Nhân viên mới làm được 2 tháng (2 ngày phép)')"
            >
              2 ngày (2 tháng)
            </button>
            <button 
              type="button" 
              class="quick-preset-btn"
              @click="setQuickDays(3, 'Nhân viên mới làm được 3 tháng (3 ngày phép)')"
            >
              3 ngày (3 tháng)
            </button>
            <button 
              type="button" 
              class="quick-preset-btn"
              @click="setQuickDays(6, 'Làm việc 6 tháng (6 ngày phép)')"
            >
              6 ngày (Nửa năm)
            </button>
            <button 
              type="button" 
              class="quick-preset-btn"
              @click="setQuickDays(12, 'Định mức chuẩn 1 năm (12 ngày phép)')"
            >
              12 ngày (Chuẩn 1 năm)
            </button>
            <button 
              type="button" 
              class="quick-preset-btn preset-gold"
              @click="setQuickDays(15, 'Thâm niên công tác (15 ngày phép)')"
            >
              15 ngày (Thâm niên)
            </button>
            <button 
              v-if="adjustingEmployee.start_date"
              type="button" 
              class="quick-preset-btn preset-primary"
              @click="autoCalculateFromStartDate()"
              title="Tự động tính số tháng làm việc từ ngày bắt đầu đến hết năm"
            >
              <i class="fa-solid fa-bolt mr-1"></i> Tự tính theo ngày vào làm
            </button>
          </div>
        </div>

        <!-- Xem / Chỉnh số ngày đã sử dụng (tùy chọn đối soát) -->
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <div class="flex items-center justify-between mb-1">
              <label class="text-sm font-semibold text-gray-700">
                Số ngày đã nghỉ / sử dụng
              </label>
              <label class="text-xs text-primary font-medium cursor-pointer flex items-center">
                <input type="checkbox" v-model="adjustForm.enable_custom_used" class="mr-1" />
                Cho phép điều chỉnh số ngày đã nghỉ
              </label>
            </div>
            <div class="flex items-center">
              <input 
                type="number" 
                step="0.5" 
                min="0" 
                class="filter-input w-full font-bold text-warning text-base" 
                :class="{'bg-gray-100 cursor-not-allowed': !adjustForm.enable_custom_used}"
                :readonly="!adjustForm.enable_custom_used"
                v-model.number="adjustForm.used_days" 
              />
              <span class="ml-2 font-semibold text-gray-600 whitespace-no-wrap">ngày</span>
            </div>
            <p class="text-xs text-gray-500 mt-1" v-if="!adjustForm.enable_custom_used">
              * Mặc định số ngày đã dùng được hệ thống cộng dồn tự động từ các đơn xin phép đã được duyệt.
            </p>
          </div>
        </div>

        <!-- Xem trước còn lại sau điều chỉnh -->
        <div class="p-3 mb-4 rounded-lg bg-green-50 border border-green-200 flex items-center justify-between">
          <span class="text-sm text-green-900 font-semibold">
            Số ngày phép còn lại dự kiến sau điều chỉnh:
          </span>
          <span class="text-lg font-bold" :class="previewRemainingDays >= 0 ? 'text-success' : 'text-danger'">
            {{ previewRemainingDays }} ngày
          </span>
        </div>

        <!-- Ghi chú / Lý do điều chỉnh -->
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <label class="text-sm font-semibold text-gray-700 block mb-1">
              Lý do / Ghi chú điều chỉnh (*)
            </label>
            <textarea
              placeholder="VD: Mới làm được 2 tháng, Thâm niên trên 5 năm được 15 ngày, Thỏa thuận hợp đồng..."
              rows="2"
              class="filter-input w-full"
              v-model="adjustForm.note"
            ></textarea>
          </div>
        </div>

        <!-- Footer nút hành động -->
        <div class="vx-row mt-6">
          <div class="vx-col w-full flex justify-end items-center">
            <button class="btn-close-modal mr-2" @click="adjustPopupActive = false" :disabled="isSavingBalance">Hủy</button>
            <vs-button
              @click="submitAdjustBalance()"
              color="primary"
              type="filled"
              class="p-2.5 px-5"
              :disabled="isSavingBalance"
            >
              <i class="fa-solid fa-floppy-disk mr-1.5"></i>
              {{ isSavingBalance ? 'Đang lưu...' : 'Lưu Thay Đổi' }}
            </vs-button>
          </div>
        </div>
      </div>
    </vs-popup>

  </div>
</template>

<script>
import axios from '../../../http/axios.js';
import moment from 'moment';

export default {
  data() {
    return {
      flatpickrDateConfig: {
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd/m/Y',
        allowInput: true
      },
      // Tab 1: Quản lý Quỹ phép Toàn Nhân Sự
      filterYear: '2026',
      filterDepartment: 'all',
      searchStaffKeyword: '',
      searchDebounce: null,
      departments: [],
      canAdjustBalance: false,
      stats: {
        total_employees: 0,
        total_company_used_days: 0,
        over_limit_count: 0,
        out_of_leave_count: 0,
      },
      staffBalances: {
        data: [],
        pagination: { total: 0, per_page: 20, current_page: 1, last_page: 1 }
      },

      // Tab 2: Danh sách đơn nghỉ phép
      filterStatus: 'all',
      searchKeyword: '',
      leaves: { data: [], total: 0, per_page: 20, current_page: 1 },
      leaveTypes: [],
      canApprove: false,

      // Popup Điều Chỉnh Quỹ Phép Năm
      adjustPopupActive: false,
      adjustingEmployee: null,
      isSavingBalance: false,
      adjustForm: {
        employee_id: null,
        year: 2026,
        total_days: 12.0,
        used_days: 0.0,
        enable_custom_used: false,
        note: ''
      },

      // Popup Chi tiết Lịch sử Nghỉ phép
      popupHistoryActive: false,
      selectedStaffHistory: null,

      // Popup Import Dữ liệu
      popupImportActive: false,
      rawImportText: '',
      parsedRows: [],
      isImporting: false,

      // Popup Tạo đơn
      popupActive: false,
      form: {
        user_id: null,
        request_category: 'leave',
        leave_type_id: null,
        start_date: '',
        end_date: '',
        duration_days: 1.0,
        target_shift_info: '',
        reason: ''
      },

      // Popup Phê duyệt
      approvePopupActive: false,
      selectedLeave: null,
      approveForm: {
        approval_type: 'approved',
        rejection_reason: ''
      },

      // Popup Từ chối đơn
      rejectPopupActive: false,
      targetRejectLeave: null,
      rejectReason: ''
    };
  },
  computed: {
    popupHistoryTitle() {
      if (!this.selectedStaffHistory) return 'LỊCH SỬ NGHỈ PHÉP NHÂN SỰ';
      return `LỊCH SỬ NGHỈ PHÉP - ${this.selectedStaffHistory.employee.full_name} (${this.selectedStaffHistory.employee.employee_code})`;
    },
    adjustPopupTitle() {
      if (!this.adjustingEmployee) return 'ĐIỀU CHỈNH QUỸ PHÉP NĂM';
      return `ĐIỀU CHỈNH QUỸ PHÉP NĂM - ${this.adjustingEmployee.full_name} (${this.adjustingEmployee.employee_code})`;
    },
    previewRemainingDays() {
      const total = parseFloat(this.adjustForm.total_days) || 0;
      const used = parseFloat(this.adjustForm.used_days) || 0;
      return Math.max(0, Math.round((total - used) * 100) / 100);
    }
  },
  created() {
    this.fetchLeaveTypes();
    this.fetchAllBalances();
    this.fetchLeaves();
  },
  methods: {
    // ----------------------------------------------------
    // TAB 1: ALL STAFF LEAVE BALANCES
    // ----------------------------------------------------
    fetchAllBalances() {
      axios.p('/api/hrm/leaves/all-balances', {
        year: this.filterYear,
        department_id: this.filterDepartment,
        keyword: this.searchStaffKeyword,
        page: this.staffBalances.pagination.current_page || 1,
        limit: 20
      }).then(res => {
        if (res.data.status === 1) {
          this.stats = res.data.stats || this.stats;
          this.departments = res.data.departments || [];
          this.staffBalances.data = res.data.data || [];
          this.staffBalances.pagination = res.data.pagination || this.staffBalances.pagination;
          this.canAdjustBalance = res.data.can_adjust_balance || false;
        }
      }).catch(err => {
        console.error(err);
      });
    },

    onSearchStaffInput() {
      clearTimeout(this.searchDebounce);
      this.searchDebounce = setTimeout(() => {
        this.staffBalances.pagination.current_page = 1;
        this.fetchAllBalances();
      }, 300);
    },

    changeStaffPage(p) {
      this.staffBalances.pagination.current_page = p;
      this.fetchAllBalances();
    },

    viewEmployeeHistory(emp) {
      axios.p(`/api/hrm/leaves/employee-history/${emp.id}`, {
        year: this.filterYear
      }).then(res => {
        if (res.data.status === 1) {
          this.selectedStaffHistory = res.data;
          this.popupHistoryActive = true;
        } else {
          this.$vs.notify({ title: 'Lỗi', text: res.data.message || 'Không thể tải lịch sử', color: 'danger' });
        }
      }).catch(err => {
        this.$vs.notify({ title: 'Lỗi', text: err.message, color: 'danger' });
      });
    },

    exportToExcel() {
      let csvContent = 'data:text/csv;charset=utf-8,\uFEFF';
      csvContent += 'STT,Mã NS,Họ và tên,Phòng ban,Phép năm được hưởng,Đã sử dụng,Còn lại,Vượt mức,Tình trạng\n';
      
      this.staffBalances.data.forEach((row, i) => {
        let status = row.is_over_limit ? `Vượt ${row.over_days} ngày` : (row.is_out_of_leave ? 'Hết phép' : 'Bình thường');
        csvContent += `"${i + 1}","${row.employee_code}","${row.full_name}","${row.department_name}","${row.total_days}","${row.used_days}","${row.remaining_days}","${row.over_days}","${status}"\n`;
      });

      const encodedUri = encodeURI(csvContent);
      const link = document.createElement('a');
      link.setAttribute('href', encodedUri);
      link.setAttribute('download', `Bang_Tong_Hop_Nghi_Phep_${this.filterYear}.csv`);
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    },

    // ----------------------------------------------------
    // ADJUST LEAVE BALANCE (ADMIN & QUẢN LÝ NHÂN SỰ)
    // ----------------------------------------------------
    openAdjustBalancePopup(emp) {
      this.adjustingEmployee = emp;
      this.adjustForm = {
        employee_id: emp.id,
        year: parseInt(this.filterYear) || 2026,
        total_days: emp.total_days !== undefined ? emp.total_days : (emp.is_fulltime ? 12 : 0),
        used_days: emp.used_days !== undefined ? emp.used_days : 0,
        enable_custom_used: false,
        note: emp.balance_note || ''
      };
      this.adjustPopupActive = true;
    },

    setQuickDays(days, defaultNote) {
      this.adjustForm.total_days = days;
      if (!this.adjustForm.note) {
        this.adjustForm.note = defaultNote;
      }
    },

    autoCalculateFromStartDate() {
      if (!this.adjustingEmployee || !this.adjustingEmployee.start_date) {
        this.$vs.notify({
          title: 'Thông báo',
          text: 'Nhân sự này chưa cập nhật ngày bắt đầu làm việc trong hồ sơ!',
          color: 'warning'
        });
        return;
      }

      const startDate = new Date(this.adjustingEmployee.start_date);
      if (isNaN(startDate.getTime())) {
        this.$vs.notify({ title: 'Lỗi', text: 'Ngày bắt đầu làm việc không hợp lệ!', color: 'danger' });
        return;
      }

      const filterYear = parseInt(this.filterYear) || 2026;
      const startYear = startDate.getFullYear();
      const startMonth = startDate.getMonth() + 1; // 1 - 12
      const startDay = startDate.getDate();

      let calculatedDays = 12.0;
      let note = '';

      if (startYear > filterYear) {
        // Vào làm sau năm xét duyệt
        calculatedDays = 0.0;
        note = `Vào làm năm ${startYear} (sau năm ${filterYear})`;
      } else if (startYear === filterYear) {
        // Vào làm trong năm xét duyệt
        // Tính số tháng làm việc trong năm:
        let activeMonths = 12 - startMonth + 1;
        if (startDay > 15) {
          activeMonths = Math.max(0, activeMonths - 0.5);
        }
        calculatedDays = Math.max(0, Math.min(12, activeMonths));
        note = `Vào làm ${startMonth}/${startYear} (~${activeMonths} tháng làm việc trong năm ${filterYear})`;
      } else {
        // Làm từ các năm trước
        const yearsOfService = filterYear - startYear;
        // Cứ 5 năm làm việc thì được cộng thêm 1 ngày phép
        const extraSeniorityDays = Math.floor(yearsOfService / 5);
        calculatedDays = 12.0 + extraSeniorityDays;
        if (extraSeniorityDays > 0) {
          note = `Thâm niên ${yearsOfService} năm (+${extraSeniorityDays} ngày theo luật LĐ)`;
        } else {
          note = `Làm việc trọn năm ${filterYear} (định mức chuẩn 12 ngày)`;
        }
      }

      this.adjustForm.total_days = calculatedDays;
      this.adjustForm.note = note;

      this.$vs.notify({
        title: 'Đã tính toán gợi ý',
        text: `Định mức đề xuất: ${calculatedDays} ngày (${note})`,
        color: 'success'
      });
    },

    getWorkingMonthsNote(startDateStr) {
      if (!startDateStr) return '';
      const startDate = new Date(startDateStr);
      if (isNaN(startDate.getTime())) return '';
      const filterYear = parseInt(this.filterYear) || 2026;
      const startYear = startDate.getFullYear();
      const startMonth = startDate.getMonth() + 1;

      if (startYear === filterYear) {
        const months = 12 - startMonth + 1;
        return `mới làm được ${months} tháng trong năm ${filterYear}`;
      } else if (startYear < filterYear) {
        const years = filterYear - startYear;
        return `thâm niên ~${years} năm`;
      } else {
        return `vào làm từ năm ${startYear}`;
      }
    },

    formatDate(dateStr) {
      if (!dateStr) return '';
      const parts = dateStr.split('-');
      if (parts.length === 3) {
        return `${parts[2]}/${parts[1]}/${parts[0]}`;
      }
      return dateStr;
    },

    formatDateTime(dateTimeStr) {
      if (!dateTimeStr) return '---';
      try {
        const m = moment(dateTimeStr);
        if (m.isValid()) {
          return m.format('HH:mm DD/MM/YYYY');
        }
      } catch (e) {
        // fallback
      }
      return dateTimeStr;
    },

    submitAdjustBalance() {
      if (this.adjustForm.total_days === '' || isNaN(this.adjustForm.total_days) || this.adjustForm.total_days < 0) {
        this.$vs.notify({
          title: 'Cảnh báo',
          text: 'Vui lòng nhập số ngày phép hợp lệ (>= 0)!',
          color: 'warning'
        });
        return;
      }

      this.isSavingBalance = true;
      const payload = {
        employee_id: this.adjustForm.employee_id,
        year: this.adjustForm.year,
        total_days: parseFloat(this.adjustForm.total_days),
        note: this.adjustForm.note
      };

      if (this.adjustForm.enable_custom_used) {
        payload.used_days = parseFloat(this.adjustForm.used_days) || 0;
      }

      axios.p('/api/hrm/leaves/update-balance', payload)
        .then(res => {
          this.isSavingBalance = false;
          if (res.data.status === 1) {
            this.$vs.notify({
              title: 'Thành công',
              text: res.data.message || 'Cập nhật số ngày phép thành công!',
              color: 'success'
            });
            this.adjustPopupActive = false;
            this.fetchAllBalances();
          } else {
            this.$vs.notify({
              title: 'Lỗi',
              text: res.data.message || 'Không thể điều chỉnh số ngày phép',
              color: 'danger'
            });
          }
        })
        .catch(err => {
          this.isSavingBalance = false;
          const msg = (err.response && err.response.data && err.response.data.message) ? err.response.data.message : err.message;
          this.$vs.notify({
            title: 'Lỗi',
            text: msg,
            color: 'danger'
          });
        });
    },

    // ----------------------------------------------------
    // IMPORT POPUP
    // ----------------------------------------------------
    openImportPopup() {
      this.rawImportText = '';
      this.parsedRows = [];
      this.popupImportActive = true;
    },

    parseImportText() {
      const lines = this.rawImportText.trim().split('\n');
      const rows = [];

      lines.forEach(line => {
        const parts = line.split('\t').map(s => s.trim());
        if (parts.length >= 3) {
          let name = parts[0] || parts[1];
          let days = parts[1] || parts[2];
          let date = parts[2] || parts[3];
          let pos = parts[3] || parts[4] || '';
          let dept = parts[4] || parts[5] || '';
          let note = parts[5] || parts[6] || '';

          if (isNaN(parseFloat(days))) {
            name = parts[1];
            days = parts[2];
            date = parts[3];
            pos = parts[4] || '';
            dept = parts[5] || '';
            note = parts[6] || '';
          }

          if (name && days) {
            rows.push({
              name: name,
              days: days,
              date: date,
              position: pos,
              dept: dept,
              note: note
            });
          }
        }
      });

      this.parsedRows = rows;
    },

    submitImport() {
      if (!this.parsedRows.length) return;
      this.isImporting = true;

      axios.p('/api/hrm/leaves/import-raw', {
        rows: this.parsedRows
      }).then(res => {
        this.isImporting = false;
        if (res.data.status === 1) {
          this.$vs.notify({ title: 'Thành công', text: res.data.message, color: 'success' });
          this.popupImportActive = false;
          this.fetchAllBalances();
          this.fetchLeaves();
        } else {
          this.$vs.notify({ title: 'Lỗi', text: res.data.message, color: 'danger' });
        }
      }).catch(err => {
        this.isImporting = false;
        this.$vs.notify({ title: 'Lỗi', text: err.message, color: 'danger' });
      });
    },

    // ----------------------------------------------------
    // TAB 2: DANH SÁCH ĐƠN & PHÊ DUYỆT (HIỆN CÓ)
    // ----------------------------------------------------
    fetchLeaveTypes() {
      axios.p('/api/hrm/leave-types/list', { limit: 100 }).then(res => {
        if (res.data) {
          this.leaveTypes = res.data.data || res.data;
          if (this.leaveTypes.length > 0) {
            this.form.leave_type_id = this.leaveTypes[0].id;
          }
        }
      }).catch(err => console.error(err));
    },

    fetchLeaves() {
      axios.p('/api/hrm/leaves/list', { 
        status: this.filterStatus,
        keyword: this.searchKeyword,
        page: this.leaves.current_page || 1,
        limit: 20 
      }).then(res => {
        this.leaves = res.data;
        if (typeof res.data.can_approve !== 'undefined') {
          this.canApprove = !!res.data.can_approve;
        }
      }).catch(err => console.error(err));
    },

    changePage(p) {
      this.leaves.current_page = p;
      this.fetchLeaves();
    },

    openPopup() {
      this.form = {
        user_id: null,
        request_category: 'leave',
        leave_type_id: this.leaveTypes.length > 0 ? this.leaveTypes[0].id : null,
        start_date: new Date().toISOString().substr(0, 10),
        end_date: new Date().toISOString().substr(0, 10),
        duration_days: 1.0,
        target_shift_info: '',
        reason: ''
      };
      this.popupActive = true;
    },

    calculateDays() {
      if (this.form.start_date && this.form.end_date) {
        let start = new Date(this.form.start_date);
        let end = new Date(this.form.end_date);
        let diffTime = end - start;
        let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        this.form.duration_days = diffDays > 0 ? diffDays : 1;
      }
    },

    saveData() {
      if (!this.form.start_date || !this.form.end_date) {
        this.$vs.notify({ title: 'Cảnh báo', text: 'Vui lòng chọn ngày bắt đầu và kết thúc', color: 'warning' });
        return;
      }
      axios.p('/api/hrm/leaves/add', this.form).then(res => {
        if (res.data.status === 1) {
          this.$vs.notify({ title: 'Thành công', text: res.data.message, color: 'success' });
          this.popupActive = false;
          this.fetchLeaves();
          this.fetchAllBalances();
        } else {
          this.$vs.notify({ title: 'Lỗi', text: res.data.message, color: 'danger' });
        }
      }).catch(err => {
        this.$vs.notify({ title: 'Lỗi', text: err.message, color: 'danger' });
      });
    },

    openApprovePopup(leave) {
      if (leave.status !== 0) {
        this.$vs.notify({
          title: 'Cảnh báo',
          text: 'Đơn này đã ở trạng thái cuối (' + (leave.status === 1 ? 'Đã duyệt' : 'Đã từ chối') + '), không thể cập nhật thêm!',
          color: 'warning'
        });
        return;
      }
      this.selectedLeave = leave;
      this.approveForm = {
        approval_type: leave.request_category === 'off_to_onl' ? 'half_salary' : 'approved',
        rejection_reason: ''
      };
      this.approvePopupActive = true;
    },

    openRejectPopup(leave) {
      if (leave.status !== 0) {
        this.$vs.notify({
          title: 'Cảnh báo',
          text: 'Đơn này đã ở trạng thái cuối (' + (leave.status === 1 ? 'Đã duyệt' : 'Đã từ chối') + '), không thể cập nhật thêm!',
          color: 'warning'
        });
        return;
      }
      this.targetRejectLeave = leave;
      this.rejectReason = '';
      this.rejectPopupActive = true;
    },

    submitDirectReject() {
      if (!this.rejectReason || !this.rejectReason.trim()) {
        this.$vs.notify({ title: 'Cảnh báo', text: 'Vui lòng nhập ghi chú / lý do từ chối', color: 'warning' });
        return;
      }
      this.$vs.loading();
      axios.p(`/api/hrm/leaves/reject/${this.targetRejectLeave.id}`, {
        rejection_reason: this.rejectReason.trim()
      }).then(res => {
        this.$vs.loading.close();
        if (res.data.status === 1) {
          this.$vs.notify({ title: 'Thành công', text: res.data.message || 'Đã từ chối đơn thành công', color: 'success' });
          this.rejectPopupActive = false;
          this.fetchLeaves();
          this.fetchAllBalances();
        } else {
          this.$vs.notify({ title: 'Lỗi', text: res.data.message, color: 'danger' });
        }
      }).catch(err => {
        this.$vs.loading.close();
        const msg = err.response && err.response.data && err.response.data.message ? err.response.data.message : (err.message || 'Lỗi khi từ chối đơn');
        this.$vs.notify({ title: 'Lỗi', text: msg, color: 'danger' });
      });
    },

    submitApproval() {
      if (this.approveForm.approval_type === 'rejected' && !this.approveForm.rejection_reason) {
        this.$vs.notify({ title: 'Cảnh báo', text: 'Vui lòng nhập lý do từ chối', color: 'warning' });
        return;
      }
      axios.p(`/api/hrm/leaves/approve/${this.selectedLeave.id}`, this.approveForm).then(res => {
        if (res.data.status === 1) {
          this.$vs.notify({ title: 'Thành công', text: res.data.message, color: 'success' });
          this.approvePopupActive = false;
          this.fetchLeaves();
          this.fetchAllBalances();
        } else {
          this.$vs.notify({ title: 'Lỗi', text: res.data.message, color: 'danger' });
        }
      }).catch(err => {
        this.$vs.notify({ title: 'Lỗi', text: err.message, color: 'danger' });
      });
    },

    getRequestCategoryText(cat) {
      const map = {
        leave: 'Nghỉ phép',
        shift_change: 'Đổi ca làm',
        off_to_onl: 'Off sang Onl'
      };
      return map[cat] || cat;
    },

    getRequestCategoryColor(cat) {
      const map = {
        leave: 'primary',
        shift_change: 'warning',
        off_to_onl: 'success'
      };
      return map[cat] || 'primary';
    },

    getApprovalTypeText(type) {
      const map = {
        approved: 'Hưởng 100% lương',
        half_salary: 'Hưởng 50% lương',
        late_unpaid: 'Gửi muộn (không lương)',
        rejected: 'Từ chối'
      };
      return map[type] || '---';
    },

    getApprovalTypeClass(type) {
      const map = {
        approved: 'text-success',
        half_salary: 'text-warning',
        late_unpaid: 'text-gray-600',
        rejected: 'text-danger'
      };
      return map[type] || 'text-gray-400';
    }
  }
};
</script>

<style scoped>
/* Controls lọc */
.filter-select, .filter-input {
  height: 38px;
  padding: 6px 12px;
  font-size: 13px;
  color: #4b5563;
  background-color: #fff;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  outline: none;
  transition: border-color 0.15s ease-in-out;
}
.filter-select:focus, .filter-input:focus {
  border-color: #7367f0;
  box-shadow: 0 0 0 2px rgba(115, 103, 240, 0.15);
}

.btn-tool-action {
  height: 38px;
  width: 38px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #d1d5db;
  background: #fff;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s;
}
.btn-tool-action:hover {
  background: #f3f4f6;
  border-color: #9ca3af;
}

.btn-excel-export {
  height: 38px;
  padding: 0 14px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  font-weight: 600;
  color: #374151;
  border: 1px solid #d1d5db;
  background: #fff;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s;
}
.btn-excel-export:hover {
  background: #f9fafb;
  border-color: #10b981;
  color: #059669;
}

/* Badge Mã nhân viên */
.emp-code-badge {
  font-weight: 700;
  color: #7367f0;
  background-color: #f3f2fe;
  padding: 3px 8px;
  border-radius: 4px;
  font-size: 12px;
  letter-spacing: 0.3px;
}

/* Nút Chi tiết không bao giờ bị vỡ dòng */
.btn-detail-history {
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  white-space: nowrap !important;
  padding: 6px 14px !important;
  font-size: 12px !important;
  font-weight: 600 !important;
  color: #7367f0 !important;
  background-color: #f5f4ff !important;
  border: 1px solid #d8d4fc !important;
  border-radius: 6px !important;
  cursor: pointer !important;
  transition: all 0.2s ease !important;
  line-height: 1.4 !important;
}
.btn-detail-history:hover {
  background-color: #7367f0 !important;
  color: #ffffff !important;
  border-color: #7367f0 !important;
}

/* Tags trạng thái đẹp mắt */
.status-tag {
  display: inline-block;
  padding: 3px 10px;
  font-size: 11px;
  font-weight: 700;
  border-radius: 20px;
  white-space: nowrap;
}
.status-success {
  color: #15803d;
  background-color: #dcfce7;
  border: 1px solid #bbf7d0;
}
.status-danger {
  color: #b91c1c;
  background-color: #fee2e2;
  border: 1px solid #fecaca;
}
.status-warning {
  color: #b45309;
  background-color: #fef3c7;
  border: 1px solid #fde68a;
}
.status-gray {
  color: #4b5563;
  background-color: #f3f4f6;
  border: 1px solid #e5e7eb;
}

/* Thao tác nút Duyệt & Từ chối không bị vỡ giao diện */
.list-action {
  white-space: nowrap !important;
}
.list-action .vs-button {
  margin: 0 4px !important;
  white-space: nowrap !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  padding: 6px 12px !important;
}
.list-action .vs-button .vs-button--text {
  display: inline-flex !important;
  align-items: center !important;
  white-space: nowrap !important;
  font-size: 12px !important;
}

/* POPUP CHI TIẾT LỊCH SỬ NGHỈ */
.popup-history-container {
  padding: 10px;
}
.emp-info-banner {
  padding: 12px 16px;
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
}
.avatar-box {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  background-color: #eeedfd;
  display: flex;
  align-items: center;
  justify-content: center;
}

.summary-card {
  padding: 12px;
  border-radius: 8px;
  text-align: center;
  border: 1px solid #e2e8f0;
  display: flex;
  flex-direction: column;
  justify-content: center;
}
.summary-label {
  font-size: 11px;
  font-weight: 600;
  color: #64748b;
  text-transform: uppercase;
  margin-bottom: 4px;
  display: block;
}
.summary-value {
  font-size: 18px;
  font-weight: 800;
  line-height: 1.2;
}
.card-blue {
  background-color: #f0f7ff;
  border-color: #cfe2ff;
}
.card-orange {
  background-color: #fffbf0;
  border-color: #fde8b3;
}
.card-green {
  background-color: #f0fdf4;
  border-color: #bbf7d0;
}
.card-red {
  background-color: #fef2f2;
  border-color: #fecaca;
}
.card-gray {
  background-color: #f8fafc;
  border-color: #e2e8f0;
}

/* Bảng chi tiết trong popup */
.history-table-wrapper {
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  overflow: hidden;
  max-height: 380px;
  overflow-y: auto;
}
.history-table {
  width: 100%;
  border-collapse: collapse;
}
.history-table th {
  background-color: #f8fafc;
  color: #475569;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  padding: 10px 12px;
  border-bottom: 1px solid #e2e8f0;
  text-align: left;
}
.history-table td {
  padding: 10px 12px;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
}
.history-table tr:last-child td {
  border-bottom: none;
}
.history-table tr:hover {
  background-color: #fbfcfe;
}

.day-badge {
  display: inline-block;
  padding: 3px 10px;
  font-size: 12px;
  font-weight: 700;
  color: #4338ca;
  background-color: #e0e7ff;
  border-radius: 4px;
}

.mode-badge {
  display: inline-flex;
  align-items: center;
  padding: 3px 8px;
  font-size: 11px;
  font-weight: 600;
  border-radius: 4px;
  white-space: nowrap;
}
.mode-approved {
  color: #15803d;
  background-color: #f0fdf4;
}
.mode-late {
  color: #c2410c;
  background-color: #fff7ed;
}
.mode-half {
  color: #b45309;
  background-color: #fffbeb;
}
.mode-rejected {
  color: #b91c1c;
  background-color: #fef2f2;
}

.btn-close-modal {
  padding: 8px 20px;
  font-size: 13px;
  font-weight: 600;
  color: #4b5563;
  background-color: #f3f4f6;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s;
}
.btn-close-modal:hover {
  background-color: #e5e7eb;
  color: #1f2937;
}

/* Nút Sửa Phép */
.btn-adjust-action {
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  white-space: nowrap !important;
  padding: 6px 12px !important;
  font-size: 12px !important;
  font-weight: 600 !important;
  color: #2563eb !important;
  background-color: #eff6ff !important;
  border: 1px solid #bfdbfe !important;
  border-radius: 6px !important;
  cursor: pointer !important;
  transition: all 0.2s ease !important;
  line-height: 1.4 !important;
}
.btn-adjust-action:hover {
  background-color: #2563eb !important;
  color: #ffffff !important;
  border-color: #2563eb !important;
}
.btn-adjust-action:hover i {
  color: #ffffff !important;
}

/* Phím tắt chọn nhanh định mức phép */
.quick-preset-btn {
  padding: 6px 12px;
  font-size: 12px;
  font-weight: 600;
  color: #1e40af;
  background-color: #ffffff;
  border: 1px solid #bfdbfe;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.15s ease-in-out;
}
.quick-preset-btn:hover {
  background-color: #eff6ff;
  border-color: #3b82f6;
  color: #1d4ed8;
  transform: translateY(-1px);
}
.preset-gold {
  color: #b45309;
  border-color: #fde68a;
  background-color: #fffbeb;
}
.preset-gold:hover {
  background-color: #fef3c7;
  border-color: #f59e0b;
  color: #92400e;
}
.preset-primary {
  color: #ffffff;
  background-color: #2563eb;
  border-color: #2563eb;
}
.preset-primary:hover {
  background-color: #1d4ed8;
  border-color: #1d4ed8;
  color: #ffffff;
}
</style>

<!-- CSS Toàn cục cho Modal Vuesax không bị scoped chặn -->
<style>
.modal-custom-leave.con-vs-popup .vs-popup {
  width: 860px !important;
  max-width: 95vw !important;
  border-radius: 12px !important;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
}
.modal-adjust-balance.con-vs-popup .vs-popup {
  width: 720px !important;
}
.modal-custom-leave .vs-popup--header {
  padding: 16px 20px !important;
  background-color: #f8fafc !important;
  border-bottom: 1px solid #e2e8f0 !important;
}
.modal-custom-leave .vs-popup--title h3 {
  font-size: 15px !important;
  font-weight: 700 !important;
  color: #1e293b !important;
  letter-spacing: 0.2px !important;
}
.modal-custom-leave .vs-popup--content {
  padding: 16px 20px !important;
}
</style>
