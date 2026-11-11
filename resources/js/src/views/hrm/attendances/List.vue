<template>
  <div id="page-hrm-attendances">
    <!-- Header & Tra cứu nhanh -->
    <div class="flex flex-wrap items-center justify-between mb-4">
      <div class="mb-2 md:mb-0">
        <h3 class="font-bold text-gray-800 flex items-center">
          <feather-icon icon="ClockIcon" class="mr-2 text-primary" /> Bảng Chấm
          Công & Điểm Danh
        </h3>
        <p class="text-sm text-gray-500 mt-1">
          Theo dõi nhân sự đi làm muộn, nghỉ hàng ngày và tổng hợp công tự động
        </p>
      </div>
      <div class="flex items-center flex-wrap">
        <div class="mr-3 mb-2 md:mb-0">
          <input
            type="text"
            class="vs-inputx vs-input--input normal"
            style="padding: 10px 14px; font-size: 13px; min-width: 260px;"
            placeholder="Tìm theo Tên, SĐT, Mã NS..."
            v-model="quickSearchKeyword"
            @keyup.enter="handleQuickSearch"
          />
        </div>
        <vs-button
          color="primary"
          type="filled"
          class="p-3"
          @click="handleQuickSearch"
        >
          <i class="fa-solid fa-magnifying-glass mr-1"></i> Tra cứu
        </vs-button>
      </div>
    </div>

    <!-- Modal kết quả tra cứu nhanh -->
    <vs-popup
      title="KẾT QUẢ TRA CỨU CÔNG NHÂN SỰ"
      :active.sync="quickSearchModalActive"
    >
      <div v-if="quickSearchResult" class="p-4">
        <div
          class="p-4 rounded-lg border border-primary bg-primary-light text-gray-800"
        >
          <div
            class="flex items-center justify-between border-b pb-3 mb-3 border-gray-200"
          >
            <div>
              <h4 class="text-primary font-bold">
                {{ quickSearchResult.name }}
              </h4>
              <span class="text-xs text-gray-600 font-semibold"
                >Mã NS: {{ quickSearchResult.employee_code }} | SĐT:
                {{ quickSearchResult.phone || "Chưa có" }}</span
              >
            </div>
            <vs-chip color="primary">{{ quickSearchResult.team_name }}</vs-chip>
          </div>
          <div class="grid grid-cols-2 gap-3 text-sm">
            <div
              class="p-3 bg-white rounded shadow-sm flex justify-between items-center"
            >
              <span class="text-gray-600">Công chuẩn tháng:</span>
              <span class="font-bold text-gray-900 text-base">{{
                quickSearchResult.standard_days
              }}</span>
            </div>
            <div
              class="p-3 bg-white rounded shadow-sm flex justify-between items-center"
            >
              <span class="text-gray-600">Đã làm:</span>
              <span class="font-bold text-success text-base"
                >{{ quickSearchResult.worked_days }} ngày</span
              >
            </div>
            <div
              class="p-3 bg-white rounded shadow-sm flex justify-between items-center"
            >
              <span class="text-gray-600">Đi muộn:</span>
              <span class="font-bold text-warning text-base"
                >{{ quickSearchResult.late_fined_count }} lần</span
              >
            </div>
            <div
              class="p-3 bg-white rounded shadow-sm flex justify-between items-center"
            >
              <span class="text-gray-600">Nghỉ phép:</span>
              <span class="font-bold text-primary text-base"
                >{{ quickSearchResult.leave_approved_days }} ngày</span
              >
            </div>
            <div
              class="p-3 bg-white rounded shadow-sm flex justify-between items-center"
            >
              <span class="text-gray-600">Nghỉ không phép:</span>
              <span class="font-bold text-danger text-base"
                >{{ quickSearchResult.unexcused_absent_days }} ngày</span
              >
            </div>
            <div
              class="p-3 bg-white rounded shadow-sm flex justify-between items-center"
            >
              <span class="text-gray-600">Làm Online:</span>
              <span class="font-bold text-purple-600 text-base"
                >{{ quickSearchResult.online_days || 0 }} ngày</span
              >
            </div>
            <div
              class="p-3 bg-white rounded shadow-sm flex justify-between items-center"
            >
              <span class="text-gray-600">Gửi đơn muộn:</span>
              <span class="font-bold text-dark text-base"
                >{{ quickSearchResult.late_submitted_count }} lần</span
              >
            </div>
          </div>
        </div>
      </div>
    </vs-popup>

    <!-- Modal chi tiết chấm công tháng của 1 nhân sự -->
    <vs-popup
      :title="detailPopupTitle"
      :active.sync="employeeDetailModalActive"
      class="modal-attendance-month-detail"
    >
      <div v-if="employeeDetailLoading" class="att-modal-loading">
        <i class="fa-solid fa-spinner fa-spin att-loading-spinner"></i>
        <p class="att-loading-text">Đang tải dữ liệu chi tiết chấm công...</p>
      </div>
      <div v-else-if="employeeDetailData" class="att-modal-body">
        <!-- Card thông tin nhân sự -->
        <div class="att-profile-banner">
          <div class="att-profile-top">
            <div class="att-profile-info">
              <div class="att-profile-avatar">
                <i class="fa-solid fa-user-tie"></i>
              </div>
              <div>
                <h4 class="att-profile-name">
                  {{ employeeDetailData.profile.full_name }}
                </h4>
                <div class="att-profile-tags">
                  <span class="att-tag">
                    <i class="fa-solid fa-id-badge mr-1"></i> Mã: <strong>{{ employeeDetailData.profile.employee_code || '---' }}</strong>
                  </span>
                  <span class="att-tag">
                    <i class="fa-solid fa-users mr-1"></i> <strong>{{ employeeDetailData.profile.team_name || 'N/A' }}</strong>
                  </span>
                  <span class="att-tag">
                    <i class="fa-solid fa-clock mr-1"></i> Ca: <strong>{{ employeeDetailData.profile.shift_name || 'Chưa xếp ca' }}</strong>
                  </span>
                  <span v-if="employeeDetailData.profile.phone" class="att-tag">
                    <i class="fa-solid fa-phone mr-1"></i> {{ employeeDetailData.profile.phone }}
                  </span>
                </div>
              </div>
            </div>
            <div class="att-profile-actions">
              <span class="att-chip-month">
                <i class="fa-solid fa-calendar-check mr-1.5"></i> Tháng {{ employeeDetailData.month }}/{{ employeeDetailData.year }}
              </span>
              <button
                type="button"
                class="att-btn-export"
                @click="exportEmployeeDetailExcel"
              >
                <i class="fa-solid fa-file-excel mr-1.5"></i> Xuất Excel Chi Tiết
              </button>
            </div>
          </div>

          <!-- 7 Thẻ KPI Thống Kê Tổng Hợp -->
          <div class="att-kpi-row">
            <div class="att-kpi-item kpi-standard">
              <div class="kpi-icon-wrap"><i class="fa-solid fa-calendar-days"></i></div>
              <div class="kpi-body">
                <div class="kpi-title">Công chuẩn</div>
                <div class="kpi-num">{{ employeeDetailData.summary.standard_days }}</div>
                <div class="kpi-hint">T2 - T6</div>
              </div>
            </div>
            <div class="att-kpi-item kpi-worked">
              <div class="kpi-icon-wrap"><i class="fa-solid fa-briefcase"></i></div>
              <div class="kpi-body">
                <div class="kpi-title">Đã làm tại VP</div>
                <div class="kpi-num text-success">{{ employeeDetailData.summary.worked_days }}</div>
                <div class="kpi-hint">công</div>
              </div>
            </div>
            <div class="att-kpi-item kpi-online">
              <div class="kpi-icon-wrap"><i class="fa-solid fa-laptop-house"></i></div>
              <div class="kpi-body">
                <div class="kpi-title">Làm Online</div>
                <div class="kpi-num text-purple">{{ employeeDetailData.summary.online_days }}</div>
                <div class="kpi-hint">ngày</div>
              </div>
            </div>
            <div class="att-kpi-item kpi-late">
              <div class="kpi-icon-wrap"><i class="fa-solid fa-clock"></i></div>
              <div class="kpi-body">
                <div class="kpi-title">Đi muộn</div>
                <div class="kpi-num text-warning">{{ employeeDetailData.summary.late_fined_count }}</div>
                <div class="kpi-hint">lần phạt</div>
              </div>
            </div>
            <div class="att-kpi-item kpi-leave">
              <div class="kpi-icon-wrap"><i class="fa-solid fa-plane-departure"></i></div>
              <div class="kpi-body">
                <div class="kpi-title">Nghỉ phép</div>
                <div class="kpi-num text-blue">{{ employeeDetailData.summary.leave_approved_days }}</div>
                <div class="kpi-hint">ngày phép</div>
              </div>
            </div>
            <div class="att-kpi-item kpi-absent">
              <div class="kpi-icon-wrap"><i class="fa-solid fa-user-xmark"></i></div>
              <div class="kpi-body">
                <div class="kpi-title">Vắng k phép</div>
                <div class="kpi-num text-danger">{{ employeeDetailData.summary.unexcused_absent_days }}</div>
                <div class="kpi-hint">ngày</div>
              </div>
            </div>
            <div class="att-kpi-item kpi-latesub">
              <div class="kpi-icon-wrap"><i class="fa-solid fa-file-circle-exclamation"></i></div>
              <div class="kpi-body">
                <div class="kpi-title">Gửi muộn</div>
                <div class="kpi-num text-slate">{{ employeeDetailData.summary.late_submitted_count }}</div>
                <div class="kpi-hint">đơn</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Bảng danh sách chi tiết ngày trong tháng -->
        <div class="att-table-box">
          <table class="att-custom-table">
            <thead>
              <tr>
                <th class="th-center" style="width: 58px;">Ngày</th>
                <th style="width: 110px;">Thứ</th>
                <th style="width: 140px;">Ca làm việc</th>
                <th style="width: 210px;">Trạng thái điểm danh</th>
                <th class="th-center" style="width: 90px;">Check-in</th>
                <th class="th-center" style="width: 90px;">Check-out</th>
                <th class="th-center" style="width: 100px;">Công tính</th>
                <th>Ghi chú</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="d in employeeDetailData.days"
                :key="d.date"
                :class="{ 'is-weekend-row': d.is_weekend }"
              >
                <td class="td-center">
                  <span class="att-day-num" :class="{ 'is-weekend-num': d.is_weekend }">
                    {{ d.day_of_month }}
                  </span>
                </td>
                <td>
                  <span class="att-day-label" :class="{ 'is-weekend-text': d.is_weekend }">
                    {{ d.day_name }}
                  </span>
                  <span v-if="d.is_weekend" class="att-weekend-badge">Nghỉ cuối tuần</span>
                </td>
                <td>
                  <span class="att-shift-pill" :class="{ 'is-weekend-pill': d.is_weekend }">
                    {{ d.shift_name || (d.is_weekend ? 'Nghỉ' : 'Chưa xếp ca') }}
                  </span>
                </td>
                <td>
                  <span
                    class="att-status-badge"
                    :class="getStatusBadgeClass(d.attendance_status)"
                  >
                    {{ getStatusText(d.attendance_status) }}
                  </span>
                </td>
                <td class="td-center">
                  <span v-if="d.check_in" class="att-time-text">{{ d.check_in }}</span>
                  <span v-else class="att-time-dash">- - : - -</span>
                </td>
                <td class="td-center">
                  <span v-if="d.check_out" class="att-time-text">{{ d.check_out }}</span>
                  <span v-else class="att-time-dash">- - : - -</span>
                </td>
                <td class="td-center">
                  <span
                    class="att-work-badge"
                    :class="d.work_units > 0 ? 'work-positive' : 'work-zero'"
                  >
                    {{ d.work_units > 0 ? d.work_units + ' công' : '0' }}
                  </span>
                </td>
                <td class="att-note-cell" :title="d.note">
                  {{ d.note || '---' }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </vs-popup>

    <!-- Tabs chuyển đổi -->
    <vs-tabs alignment="left" class="mt-4">
      <!-- TAB 1: Điểm danh hàng ngày -->
      <vs-tab
        label="Điểm danh theo ngày"
        icon-pack="feather"
        icon="icon-calendar"
      >
        <vx-card no-shadow class="mt-4">
          <!-- Toolbar Lọc & Lưu -->
          <div class="flex flex-wrap items-center justify-between mb-4">
            <div class="flex flex-wrap items-center">
              <div class="mr-3 mb-2 flex items-center">
                <span class="text-sm font-medium mr-2 text-gray-700"
                  >Ngày:</span
                >
                <flat-pickr
                  v-model="selectedDate"
                  :config="flatpickrDateConfig"
                  @on-change="fetchDailySheet"
                  class="vs-inputx vs-input--input normal"
                  style="padding: 8px 12px; font-size: 13px; width: 160px"
                  placeholder="dd/mm/yyyy"
                />
              </div>
              <div class="mr-3 mb-2 flex items-center">
                <span class="text-sm font-medium mr-2 text-gray-700"
                  >Team:</span
                >
                <select
                  v-model="selectedDepartment"
                  @change="fetchDailySheet"
                  class="vs-inputx vs-input--input normal"
                  style="padding: 8px 12px; font-size: 13px; min-width: 180px"
                >
                  <option value="">-- Tất cả team --</option>
                  <option
                    :value="dept.id"
                    v-for="dept in departments"
                    :key="dept.id"
                  >
                    {{ dept.name }}
                  </option>
                </select>
              </div>
              <div class="mr-3 mb-2 flex items-center">
                <span class="text-sm font-medium mr-2 text-gray-700"
                  >Ca làm việc:</span
                >
                <select
                  v-model="selectedShift"
                  @change="fetchDailySheet"
                  class="vs-inputx vs-input--input normal"
                  style="padding: 8px 12px; font-size: 13px; min-width: 170px"
                >
                  <option value="">-- Tất cả ca --</option>
                  <option
                    :value="shift.id"
                    v-for="shift in shifts"
                    :key="shift.id"
                  >
                    {{ shift.name }}
                  </option>
                  <option value="unassigned">-- Chưa có ca --</option>
                </select>
              </div>
              <div class="relative mr-3 mb-2" style="min-width: 220px;">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                  <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </div>
                <input
                  type="text"
                  class="vs-inputx vs-input--input normal w-full pl-8 pr-3"
                  style="
                    padding: 8px 12px 8px 30px;
                    font-size: 13px;
                  "
                  placeholder="Lọc tên, mã NS..."
                  v-model="dailySearchKeyword"
                />
              </div>
            </div>
            <div class="mb-2 flex items-center">
              <!-- Trạng thái tự động lưu -->
              <div v-if="isHrmAdmin" class="flex items-center mr-2 px-3 py-2 bg-gray-100 rounded text-xs font-semibold shadow-sm">
                <span v-if="lastSaveStatus === 'saving'" class="text-warning flex items-center">
                  <i class="fa-solid fa-spinner fa-spin mr-1.5"></i> Đang tự động lưu...
                </span>
                <span v-else-if="lastSaveStatus === 'saved'" class="text-success flex items-center">
                  <i class="fa-solid fa-circle-check mr-1.5"></i> Đã tự động lưu ({{ lastSavedTime }})
                </span>
                <span v-else-if="lastSaveStatus === 'error'" class="text-danger flex items-center">
                  <i class="fa-solid fa-triangle-exclamation mr-1.5"></i> Lỗi tự động lưu
                </span>
                <span v-else class="text-gray-600 flex items-center">
                  <i class="fa-solid fa-bolt mr-1.5 text-primary"></i> Tự động lưu: Bật
                </span>
              </div>
              <vs-button
                v-if="isHrmAdmin"
                class="p-3"
                color="success"
                type="filled"
                @click="saveDailyAttendance"
              >
                <i class="fa-solid fa-floppy-disk mr-2"></i> Lưu Bảng Điểm Danh
              </vs-button>
            </div>
          </div>

          <!-- Bảng điểm danh đồng bộ table của hệ thống -->
          <div class="vs-component vs-con-table stripe vs-table-primary">
            <div class="con-tablex vs-table--content">
              <div class="vs-con-tbody vs-table--tbody">
                <table class="vs-table vs-table--tbody-table">
                  <thead class="vs-table--thead">
                    <tr>
                      <th width="5%" class="text-center">STT</th>
                      <th width="11%">Mã nhân sự</th>
                      <th width="18%">Họ và tên</th>
                      <th width="15%">Team / Phòng ban</th>
                      <th width="16%">Ca làm việc</th>
                      <th width="20%">Trạng thái điểm danh</th>
                      <th width="15%">Ghi chú</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      class="tr-values vs-table--tr"
                      v-for="(tr, index) in paginatedDailyRecords"
                      :key="tr.user_id || tr.employee_id || index"
                    >
                      <td class="td vs-table--td text-center">
                        {{ (dailyPage - 1) * dailyPerPage + index + 1 }}
                        <span v-if="tr._saving" class="ml-1 text-warning" title="Đang lưu...">
                          <i class="fa-solid fa-spinner fa-spin text-xs"></i>
                        </span>
                        <span v-else-if="tr._saved" class="ml-1 text-success" title="Đã lưu">
                          <i class="fa-solid fa-check text-xs font-bold"></i>
                        </span>
                      </td>
                      <td class="td vs-table--td font-bold text-primary">
                        {{ tr.employee_code || "---" }}
                      </td>
                      <td class="td vs-table--td font-semibold text-gray-900">
                        {{ tr.name }}
                      </td>
                      <td class="td vs-table--td">{{ tr.team_name }}</td>
                      <td class="td vs-table--td">
                        <select
                          v-model="tr.shift_id"
                          :disabled="!isHrmAdmin"
                          @change="autoSaveRecord(tr)"
                          class="vs-inputx vs-input--input normal w-full font-medium"
                          style="padding: 8px 10px; font-size: 13px"
                        >
                          <option :value="null">-- Chưa xếp ca --</option>
                          <option
                            :value="sh.id"
                            v-for="sh in shifts"
                            :key="sh.id"
                          >
                            {{ sh.name }}
                          </option>
                        </select>
                      </td>
                      <td class="td vs-table--td">
                        <!-- Trường hợp 1: Nhân viên có đơn nghỉ phép ĐÃ ĐƯỢC DUYỆT (khóa mặc định để tránh điểm danh nhầm) -->
                        <div v-if="tr.is_leave_approved && !tr.override_edit" class="flex flex-col">
                          <div class="flex items-center">
                            <span class="inline-flex items-center px-3 py-1 rounded text-xs font-bold bg-blue-100 text-blue-700 border border-blue-300 w-max">
                              <i class="fa-solid fa-calendar-check mr-1.5"></i>
                              Nghỉ phép {{ Number(tr.leave_duration_days) === 0.5 ? '0.5 ngày' : '1 ngày' }} (Đã duyệt)
                            </span>
                            <button
                              v-if="isHrmAdmin"
                              type="button"
                              @click="$set(tr, 'override_edit', true)"
                              class="ml-2 text-gray-400 hover:text-primary text-xs"
                              title="Mở khóa nếu muốn chỉnh sửa điểm danh thủ công"
                            >
                              <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                          </div>
                          <span class="text-xs text-gray-500 mt-1 italic font-normal truncate max-w-xs" :title="tr.leave_reason">
                            {{ tr.leave_reason ? 'Lý do: ' + tr.leave_reason : 'Đã duyệt đơn nghỉ phép' }}
                          </span>
                        </div>

                        <!-- Trường hợp 2: Nhân viên làm Online (ca online hoặc có đơn online đã duyệt) -->
                        <div v-else-if="tr.is_online && !tr.override_edit" class="flex flex-col">
                          <div class="flex items-center">
                            <span class="inline-flex items-center px-3 py-1 rounded text-xs font-bold bg-purple-100 text-purple-700 border border-purple-300 w-max">
                              <i class="fa-solid fa-laptop mr-1.5"></i> {{ tr.attendance_status === 'online_half' ? 'Làm Online (0.5 ngày)' : 'Làm Online (1 ngày)' }}
                            </span>
                            <button
                              v-if="isHrmAdmin"
                              type="button"
                              @click="$set(tr, 'override_edit', true)"
                              class="ml-2 text-gray-400 hover:text-primary text-xs"
                              title="Mở khóa nếu muốn chỉnh sửa điểm danh thủ công"
                            >
                              <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                          </div>
                          <span class="text-xs text-gray-500 mt-1 italic font-normal" :title="tr.online_reason">
                            {{ tr.online_reason || 'Không cần điểm danh' }}
                          </span>
                        </div>

                        <!-- Trường hợp 3: Điểm danh bình thường hoặc khi mở khóa chỉnh sửa -->
                        <div v-else>
                          <select
                            v-model="tr.attendance_status"
                            :disabled="!isHrmAdmin"
                            @change="autoSaveRecord(tr)"
                            class="vs-inputx vs-input--input normal w-full font-semibold"
                            style="padding: 8px 12px; font-size: 13px"
                            :style="getStatusStyle(tr.attendance_status)"
                          >
                            <option value="not_checked">⚪ Chưa điểm danh</option>
                            <option value="present">✓ Có mặt / Đúng giờ (1 công)</option>
                            <option value="online">🌐 Làm Online (1 ngày)</option>
                            <option value="online_half">🌐 Làm Online (0.5 ngày)</option>
                            <option value="leave_approved">📅 Nghỉ phép (1 ngày)</option>
                            <option value="leave_approved_half">📅 Nghỉ phép (0.5 ngày)</option>
                            <option value="unexcused_absent">✗ Vắng k phép (1 ngày)</option>
                            <option value="unexcused_absent_half">✗ Vắng k phép (0.5 ngày)</option>
                            <option value="late_fined">⏱ Đi muộn (đã nộp phạt)</option>
                            <option value="late_submitted">⚠ Gửi đơn muộn</option>
                          </select>

                          <!-- Nút chọn nhanh thời lượng 0.5 ngày / 1 ngày khi Làm Online -->
                          <div
                            v-if="isHrmAdmin && (tr.attendance_status === 'online' || tr.attendance_status === 'online_half')"
                            class="flex items-center mt-1.5 space-x-1"
                          >
                            <span class="text-xs text-gray-500 mr-1 font-medium">Online:</span>
                            <button
                              type="button"
                              @click="setOnlineDuration(tr, 0.5)"
                              class="px-2 py-0.5 rounded text-xs font-bold transition border"
                              :class="tr.attendance_status === 'online_half' ? 'bg-purple-600 text-white border-purple-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'"
                              title="Làm Online 0.5 ngày"
                            >
                              0.5 ngày
                            </button>
                            <button
                              type="button"
                              @click="setOnlineDuration(tr, 1)"
                              class="px-2 py-0.5 rounded text-xs font-bold transition border"
                              :class="tr.attendance_status === 'online' ? 'bg-purple-600 text-white border-purple-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'"
                              title="Làm Online 1 ngày"
                            >
                              1 ngày
                            </button>
                          </div>

                          <!-- Nút chọn nhanh thời lượng 0.5 ngày / 1 ngày khi Nghỉ phép -->
                          <div
                            v-if="isHrmAdmin && (tr.attendance_status === 'leave_approved' || tr.attendance_status === 'leave_approved_half')"
                            class="flex items-center mt-1.5 space-x-1"
                          >
                            <span class="text-xs text-gray-500 mr-1 font-medium">Nghỉ:</span>
                            <button
                              type="button"
                              @click="setLeaveDuration(tr, 0.5)"
                              class="px-2 py-0.5 rounded text-xs font-bold transition border"
                              :class="tr.attendance_status === 'leave_approved_half' ? 'bg-primary text-white border-primary' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'"
                              title="Nghỉ phép 0.5 ngày (làm nửa ngày)"
                            >
                              0.5 ngày
                            </button>
                            <button
                              type="button"
                              @click="setLeaveDuration(tr, 1)"
                              class="px-2 py-0.5 rounded text-xs font-bold transition border"
                              :class="tr.attendance_status === 'leave_approved' ? 'bg-primary text-white border-primary' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'"
                              title="Nghỉ phép 1 ngày"
                            >
                              1 ngày
                            </button>
                          </div>

                          <!-- Nút chọn nhanh thời lượng 0.5 ngày / 1 ngày khi Vắng không phép -->
                          <div
                            v-if="isHrmAdmin && (tr.attendance_status === 'unexcused_absent' || tr.attendance_status === 'unexcused_absent_half')"
                            class="flex items-center mt-1.5 space-x-1"
                          >
                            <span class="text-xs text-gray-500 mr-1 font-medium">Vắng:</span>
                            <button
                              type="button"
                              @click="setAbsentDuration(tr, 0.5)"
                              class="px-2 py-0.5 rounded text-xs font-bold transition border"
                              :class="tr.attendance_status === 'unexcused_absent_half' ? 'bg-danger text-white border-danger' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'"
                              title="Vắng không phép 0.5 ngày"
                            >
                              0.5 ngày
                            </button>
                            <button
                              type="button"
                              @click="setAbsentDuration(tr, 1)"
                              class="px-2 py-0.5 rounded text-xs font-bold transition border"
                              :class="tr.attendance_status === 'unexcused_absent' ? 'bg-danger text-white border-danger' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'"
                              title="Vắng không phép 1 ngày"
                            >
                              1 ngày
                            </button>
                          </div>

                          <div v-if="tr.override_edit && isHrmAdmin" class="text-right mt-1">
                            <button
                              type="button"
                              @click="onRelock(tr)"
                              class="text-xs text-primary hover:underline"
                            >
                              Khóa lại
                            </button>
                          </div>
                        </div>
                      </td>
                      <td class="td vs-table--td">
                        <input
                          type="text"
                          v-model="tr.note"
                          :disabled="!isHrmAdmin"
                          placeholder="Nhập ghi chú..."
                          class="vs-inputx vs-input--input normal w-full"
                          style="padding: 8px 12px; font-size: 13px"
                          @input="onNoteInput(tr)"
                          @blur="onNoteBlur(tr)"
                          @keyup.enter="onNoteBlur(tr)"
                        />
                      </td>
                    </tr>
                    <tr v-if="!dailyRecords.length">
                      <td colspan="7" class="text-center p-4 text-gray-500">
                        Không có dữ liệu nhân viên cần điểm danh
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Phân trang Tab 1 -->
          <div
            class="flex flex-wrap items-center justify-between mt-5"
            v-if="filteredDailyRecords.length > 0"
          >
            <div class="text-sm text-gray-600 mb-2 md:mb-0">
              Hiển thị
              <strong>{{
                filteredDailyRecords.length
                  ? (dailyPage - 1) * dailyPerPage + 1
                  : 0
              }}</strong>
              -
              <strong>{{
                Math.min(dailyPage * dailyPerPage, filteredDailyRecords.length)
              }}</strong>
              trong tổng số
              <strong>{{ filteredDailyRecords.length }}</strong> nhân sự
            </div>
            <div
              class="flex items-center"
              v-if="filteredDailyRecords.length > dailyPerPage"
            >
              <span class="text-sm text-gray-600 mr-2">Mỗi trang:</span>
              <select
                v-model="dailyPerPage"
                @change="dailyPage = 1"
                class="vs-inputx vs-input--input normal mr-4"
                style="padding: 6px 10px; font-size: 13px"
              >
                <option :value="20">20</option>
                <option :value="50">50</option>
                <option :value="100">100</option>
              </select>
              <vs-pagination :total="totalDailyPages" v-model="dailyPage" />
            </div>
          </div>
        </vx-card>
      </vs-tab>

      <!-- TAB 2: Tổng hợp công tháng -->
      <vs-tab
        label="Tổng hợp công tháng"
        icon-pack="feather"
        icon="icon-bar-chart-2"
      >
        <vx-card no-shadow class="mt-4">
          <div class="flex flex-wrap items-center justify-between mb-4">
            <div class="flex flex-wrap items-center">
              <div class="mr-3 mb-2 flex items-center">
                <span class="text-sm font-medium mr-2 text-gray-700"
                  >Tháng:</span
                >
                <select
                  v-model="selectedMonth"
                  @change="fetchMonthlySummary"
                  class="vs-inputx vs-input--input normal"
                  style="padding: 8px 12px; font-size: 13px"
                >
                  <option v-for="m in 12" :key="m" :value="m">
                    Tháng {{ m }}
                  </option>
                </select>
              </div>
              <div class="mr-3 mb-2 flex items-center">
                <span class="text-sm font-medium mr-2 text-gray-700">Năm:</span>
                <select
                  v-model="selectedYear"
                  @change="fetchMonthlySummary"
                  class="vs-inputx vs-input--input normal"
                  style="padding: 8px 12px; font-size: 13px"
                >
                  <option :value="2025">2025</option>
                  <option :value="2026">2026</option>
                  <option :value="2027">2027</option>
                </select>
              </div>
              <div class="mr-3 mb-2 flex items-center">
                <span class="text-sm font-medium mr-2 text-gray-700">Team:</span>
                <select
                  v-model="selectedMonthlyDepartment"
                  @change="fetchMonthlySummary"
                  class="vs-inputx vs-input--input normal"
                  style="padding: 8px 12px; font-size: 13px; min-width: 170px"
                >
                  <option value="">-- Tất cả team --</option>
                  <option
                    :value="dept.id"
                    v-for="dept in departments"
                    :key="dept.id"
                  >
                    {{ dept.name }}
                  </option>
                </select>
              </div>
              <div class="mr-3 mb-2 flex items-center">
                <span class="text-sm font-medium mr-2 text-gray-700">Ca làm việc:</span>
                <select
                  v-model="selectedMonthlyShift"
                  @change="fetchMonthlySummary"
                  class="vs-inputx vs-input--input normal"
                  style="padding: 8px 12px; font-size: 13px; min-width: 160px"
                >
                  <option value="">-- Tất cả ca --</option>
                  <option
                    :value="shift.id"
                    v-for="shift in shifts"
                    :key="shift.id"
                  >
                    {{ shift.name }}
                  </option>
                  <option value="unassigned">-- Chưa có ca --</option>
                </select>
              </div>
              <div class="mr-3 mb-2">
                <input
                  type="text"
                  class="vs-inputx vs-input--input normal"
                  style="padding: 8px 14px; font-size: 13px; min-width: 180px;"
                  placeholder="Lọc tên, mã NS..."
                  v-model="monthlySearchKeyword"
                />
              </div>
            </div>
            <div class="flex items-center flex-wrap mb-2">
              <div class="text-sm font-semibold text-primary mr-3 mb-1 sm:mb-0">
                <i class="fa-solid fa-circle-info mr-1"></i> Công chuẩn:
                <strong class="text-base text-gray-900">{{
                  standardDays
                }}</strong>
                công (T2 - T6)
              </div>
              <vs-button
                color="success"
                type="filled"
                class="p-2.5 sm:p-3 font-semibold shadow-md flex items-center"
                @click="exportMonthlyExcel"
              >
                <i class="fa-solid fa-file-excel mr-1.5 text-base"></i> Xuất Excel
              </vs-button>
            </div>
          </div>

          <!-- Bảng tổng hợp công tháng đồng bộ -->
          <div class="vs-component vs-con-table stripe vs-table-primary">
            <div class="con-tablex vs-table--content">
              <div class="vs-con-tbody vs-table--tbody">
                <table class="vs-table vs-table--tbody-table">
                  <thead class="vs-table--thead">
                    <tr>
                      <th width="3%" class="text-center">STT</th>
                      <th width="8%">Mã NS</th>
                      <th width="14%">Họ và tên</th>
                      <th width="11%">Team</th>
                      <th width="10%">Ca làm việc</th>
                      <th width="6%" class="text-center">Công chuẩn</th>
                      <th width="6%" class="text-center">Đã làm</th>
                      <th width="7%" class="text-center">Làm Online</th>
                      <th width="6%" class="text-center">Đi muộn</th>
                      <th width="6%" class="text-center">Nghỉ phép</th>
                      <th width="7%" class="text-center">Vắng k phép</th>
                      <th width="6%" class="text-center">Gửi muộn</th>
                      <th width="10%" class="text-center">Hành động</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      class="tr-values vs-table--tr"
                      v-for="(tr, index) in paginatedMonthlySummary"
                      :key="tr.user_id || tr.employee_id || index"
                    >
                      <td class="td vs-table--td text-center">
                        {{ (monthlyPage - 1) * monthlyPerPage + index + 1 }}
                      </td>
                      <td class="td vs-table--td font-bold text-primary">
                        {{ tr.employee_code || "---" }}
                      </td>
                      <td class="td vs-table--td font-semibold text-gray-900">
                        {{ tr.name }}
                      </td>
                      <td class="td vs-table--td">{{ tr.team_name }}</td>
                      <td class="td vs-table--td">
                        <span class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-700 font-medium">
                          {{ tr.shift_name || "Chưa xếp ca" }}
                        </span>
                      </td>
                      <td class="td vs-table--td text-center font-semibold">
                        {{ tr.standard_days }}
                      </td>
                      <td
                        class="td vs-table--td text-center font-bold text-success"
                      >
                        {{ tr.worked_days }}
                      </td>
                      <td
                        class="td vs-table--td text-center font-bold"
                        style="color: #7c3aed;"
                      >
                        {{ tr.online_days || 0 }} ngày
                      </td>
                      <td
                        class="td vs-table--td text-center font-semibold text-warning"
                      >
                        {{ tr.late_fined_count }} lần
                      </td>
                      <td
                        class="td vs-table--td text-center font-semibold text-primary"
                      >
                        {{ tr.leave_approved_days }} ngày
                      </td>
                      <td
                        class="td vs-table--td text-center font-semibold text-danger"
                      >
                        {{ tr.unexcused_absent_days }} ngày
                      </td>
                      <td
                        class="td vs-table--td text-center font-semibold text-dark"
                      >
                        {{ tr.late_submitted_count }} lần
                      </td>
                      <td class="td vs-table--td text-center">
                        <vs-button
                          size="small"
                          color="primary"
                          type="border"
                          class="text-xs py-1 px-2 font-semibold"
                          @click="openEmployeeMonthDetail(tr)"
                          title="Xem chi tiết chấm công cả tháng của nhân sự này"
                        >
                          <i class="fa-solid fa-eye mr-1"></i> Chi tiết
                        </vs-button>
                      </td>
                    </tr>
                    <tr v-if="!filteredMonthlySummary.length">
                      <td colspan="13" class="text-center p-4 text-gray-500">
                        Chưa có dữ liệu tổng hợp
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Phân trang Tab 2 -->
          <div
            class="flex flex-wrap items-center justify-between mt-5"
            v-if="filteredMonthlySummary.length > 0"
          >
            <div class="text-sm text-gray-600 mb-2 md:mb-0">
              Hiển thị
              <strong>{{
                filteredMonthlySummary.length
                  ? (monthlyPage - 1) * monthlyPerPage + 1
                  : 0
              }}</strong>
              -
              <strong>{{
                Math.min(
                  monthlyPage * monthlyPerPage,
                  filteredMonthlySummary.length,
                )
              }}</strong>
              trong tổng số
              <strong>{{ filteredMonthlySummary.length }}</strong> nhân sự
            </div>
            <div
              class="flex items-center"
              v-if="filteredMonthlySummary.length > monthlyPerPage"
            >
              <span class="text-sm text-gray-600 mr-2">Mỗi trang:</span>
              <select
                v-model="monthlyPerPage"
                @change="monthlyPage = 1"
                class="vs-inputx vs-input--input normal mr-4"
                style="padding: 6px 10px; font-size: 13px"
              >
                <option :value="20">20</option>
                <option :value="50">50</option>
                <option :value="100">100</option>
              </select>
              <vs-pagination :total="totalMonthlyPages" v-model="monthlyPage" />
            </div>
          </div>
        </vx-card>
      </vs-tab>
    </vs-tabs>
  </div>
</template>

<script>
import axios from '../../../http/axios.js';

export default {
  data() {
    return {
      flatpickrDateConfig: {
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd/m/Y',
        allowInput: true
      },
      selectedDate: new Date().toISOString().split('T')[0],
      selectedDepartment: '',
      selectedShift: '',
      shifts: [],
      departments: [],
      dailyRecords: [],
      dailySearchKeyword: '',
      dailyPage: 1,
      dailyPerPage: 20,
      selectedMonth: new Date().getMonth() + 1,
      selectedYear: new Date().getFullYear(),
      selectedMonthlyDepartment: '',
      selectedMonthlyShift: '',
      monthlySummary: [],
      monthlySearchKeyword: '',
      monthlyPage: 1,
      monthlyPerPage: 20,
      standardDays: 22,
      quickSearchKeyword: '',
      quickSearchModalActive: false,
      quickSearchResult: null,
      employeeDetailModalActive: false,
      employeeDetailLoading: false,
      employeeDetailData: null,
      selectedEmployeeDetail: null,
      isHrmAdminServer: null,
      lastSaveStatus: 'idle', // 'idle' | 'saving' | 'saved' | 'error'
      lastSavedTime: '',
      isAutoSaving: false,
    };
  },
  watch: {
    dailySearchKeyword() {
      this.dailyPage = 1;
    },
    monthlySearchKeyword() {
      this.monthlyPage = 1;
    },
    selectedMonthlyDepartment() {
      this.monthlyPage = 1;
      this.fetchMonthlySummary();
    },
    selectedMonthlyShift() {
      this.monthlyPage = 1;
      this.fetchMonthlySummary();
    }
  },
  computed: {
    filteredDailyRecords() {
      if (!this.dailySearchKeyword) return this.dailyRecords;
      const kw = this.dailySearchKeyword.toLowerCase().trim();
      return this.dailyRecords.filter(r =>
        (r.name && r.name.toLowerCase().includes(kw)) ||
        (r.employee_code && r.employee_code.toLowerCase().includes(kw)) ||
        (r.team_name && r.team_name.toLowerCase().includes(kw)) ||
        (r.shift_name && r.shift_name.toLowerCase().includes(kw))
      );
    },
    paginatedDailyRecords() {
      const start = (this.dailyPage - 1) * this.dailyPerPage;
      return this.filteredDailyRecords.slice(start, start + this.dailyPerPage);
    },
    totalDailyPages() {
      return Math.ceil(this.filteredDailyRecords.length / this.dailyPerPage) || 1;
    },
    filteredMonthlySummary() {
      if (!this.monthlySearchKeyword) return this.monthlySummary;
      const kw = this.monthlySearchKeyword.toLowerCase().trim();
      return this.monthlySummary.filter(r =>
        (r.name && r.name.toLowerCase().includes(kw)) ||
        (r.employee_code && r.employee_code.toLowerCase().includes(kw)) ||
        (r.team_name && r.team_name.toLowerCase().includes(kw)) ||
        (r.shift_name && r.shift_name.toLowerCase().includes(kw))
      );
    },
    paginatedMonthlySummary() {
      const start = (this.monthlyPage - 1) * this.monthlyPerPage;
      return this.filteredMonthlySummary.slice(start, start + this.monthlyPerPage);
    },
    totalMonthlyPages() {
      return Math.ceil(this.filteredMonthlySummary.length / this.monthlyPerPage) || 1;
    },
    detailPopupTitle() {
      if (this.selectedEmployeeDetail) {
        return `CHI TIẾT CHẤM CÔNG THÁNG ${this.selectedMonth}/${this.selectedYear} - ${this.selectedEmployeeDetail.name}`;
      }
      return 'CHI TIẾT CHẤM CÔNG';
    },
    isHrmAdmin() {
      if (this.isHrmAdminServer !== null) {
        return this.isHrmAdminServer;
      }
      const user = this.$store.state.AppActiveUser || JSON.parse(localStorage.getItem('userInfo')) || {};
      if (user.uid === 1 || user.id === 1 || user.is_hrm_admin === true) return true;
      const roleStr = ((user.roleName || user.role_name || '') + '').toLowerCase().replace(/[\s\-_]+/g, '_');
      return (Array.isArray(user.roles) && user.roles.includes('hrm_manager')) ||
        roleStr.includes('admin') ||
        roleStr.includes('hrm_manager');
    }
  },
  created() {
    this.fetchDepartments();
    this.fetchShifts();
    this.fetchDailySheet();
    this.fetchMonthlySummary();
  },
  methods: {
    fetchDepartments() {
      axios.p('/api/hrm/departments/list', { limit: 100 }).then(res => {
        if (res.data) {
          this.departments = res.data;
        }
      });
    },
    fetchShifts() {
      axios.p('/api/hrm/shifts/list').then(res => {
        if (res.data) {
          this.shifts = res.data;
        }
      });
    },
    fetchDailySheet() {
      this.$vs.loading();
      this.lastSaveStatus = 'idle';
      axios.p('/api/hrm/attendances/daily-sheet', {
        date: this.selectedDate,
        department_id: this.selectedDepartment || null,
        shift_id: this.selectedShift || null
      }).then(res => {
        this.dailyRecords = res.data.data || [];
        this.isHrmAdminServer = typeof res.data.is_hrm_admin !== 'undefined' ? !!res.data.is_hrm_admin : null;
        this.$vs.loading.close();
      }).catch(err => {
        this.$vs.loading.close();
        this.$vs.notify({ title: 'Lỗi', text: 'Không thể tải bảng điểm danh', color: 'danger' });
      });
    },
    autoSaveRecord(record) {
      if (!this.isHrmAdmin) return;
      this.$set(record, '_saving', true);
      this.lastSaveStatus = 'saving';

      axios.p('/api/hrm/attendances/update-status', {
        date: this.selectedDate,
        records: [record]
      }).then(res => {
        this.$set(record, '_saving', false);
        this.$set(record, '_saved', true);
        this.lastSaveStatus = 'saved';
        this.lastSavedTime = new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

        setTimeout(() => {
          this.$set(record, '_saved', false);
        }, 2000);

        this.debouncedFetchMonthlySummary();
      }).catch(err => {
        this.$set(record, '_saving', false);
        this.lastSaveStatus = 'error';
        const msg = (err.response && err.response.data && err.response.data.message)
          ? err.response.data.message
          : 'Không thể tự động lưu điểm danh cho ' + (record.name || 'nhân sự');
        this.$vs.notify({
          title: 'Lỗi tự động lưu',
          text: msg,
          color: 'danger',
          time: 4000
        });
      });
    },
    onNoteInput(tr) {
      if (!this.isHrmAdmin) return;
      const key = tr.user_id || tr.employee_id || tr.employee_profile_id;
      if (!this._noteTimers) this._noteTimers = {};
      clearTimeout(this._noteTimers[key]);
      this._noteTimers[key] = setTimeout(() => {
        this.autoSaveRecord(tr);
      }, 700);
    },
    onNoteBlur(tr) {
      if (!this.isHrmAdmin) return;
      const key = tr.user_id || tr.employee_id || tr.employee_profile_id;
      if (this._noteTimers && this._noteTimers[key]) {
        clearTimeout(this._noteTimers[key]);
      }
      this.autoSaveRecord(tr);
    },
    setLeaveDuration(tr, days) {
      if (!this.isHrmAdmin) return;
      tr.attendance_status = days === 0.5 ? 'leave_approved_half' : 'leave_approved';
      this.autoSaveRecord(tr);
    },
    setOnlineDuration(tr, days) {
      if (!this.isHrmAdmin) return;
      tr.attendance_status = days === 0.5 ? 'online_half' : 'online';
      this.autoSaveRecord(tr);
    },
    setAbsentDuration(tr, days) {
      if (!this.isHrmAdmin) return;
      tr.attendance_status = days === 0.5 ? 'unexcused_absent_half' : 'unexcused_absent';
      this.autoSaveRecord(tr);
    },
    onRelock(tr) {
      this.$set(tr, 'override_edit', false);
      if (tr.is_leave_approved) {
        if (tr.leave_approval_type === 'late_unpaid') {
          tr.attendance_status = 'late_submitted';
        } else if (tr.leave_duration_days && Number(tr.leave_duration_days) === 0.5) {
          tr.attendance_status = 'leave_approved_half';
        } else {
          tr.attendance_status = 'leave_approved';
        }
      } else if (tr.is_online) {
        tr.attendance_status = 'online';
      }
      this.autoSaveRecord(tr);
    },
    debouncedFetchMonthlySummary() {
      clearTimeout(this._monthlySummaryTimer);
      this._monthlySummaryTimer = setTimeout(() => {
        this.fetchMonthlySummary();
      }, 1500);
    },
    saveDailyAttendance() {
      this.$vs.loading();
      axios.p('/api/hrm/attendances/update-status', {
        date: this.selectedDate,
        records: this.dailyRecords
      }).then(res => {
        this.$vs.loading.close();
        this.lastSaveStatus = 'saved';
        this.lastSavedTime = new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        this.$vs.notify({ title: 'Thành công', text: res.data.message || 'Lưu bảng điểm danh thành công', color: 'success' });
        this.fetchMonthlySummary();
      }).catch(err => {
        this.$vs.loading.close();
        this.$vs.notify({ title: 'Lỗi', text: 'Lưu điểm danh thất bại', color: 'danger' });
      });
    },
    fetchMonthlySummary() {
      this.$vs.loading();
      axios.p('/api/hrm/attendances/monthly-summary', {
        month: this.selectedMonth,
        year: this.selectedYear,
        department_id: this.selectedMonthlyDepartment || null,
        shift_id: this.selectedMonthlyShift || null
      }).then(res => {
        this.monthlySummary = res.data.data || [];
        this.standardDays = res.data.standard_days || 22;
        this.$vs.loading.close();
      }).catch(err => {
        this.$vs.loading.close();
        this.$vs.notify({ title: 'Lỗi', text: 'Không thể tải tổng hợp công tháng', color: 'danger' });
      });
    },
    openEmployeeMonthDetail(tr) {
      this.selectedEmployeeDetail = tr;
      this.employeeDetailModalActive = true;
      this.employeeDetailLoading = true;
      this.employeeDetailData = null;

      axios.p('/api/hrm/attendances/monthly-detail', {
        employee_profile_id: tr.employee_profile_id || tr.id,
        month: this.selectedMonth,
        year: this.selectedYear
      }).then(res => {
        this.employeeDetailLoading = false;
        if (res.data.status === 1) {
          this.employeeDetailData = res.data;
        } else {
          this.$vs.notify({ title: 'Lỗi', text: res.data.message || 'Không thể tải chi tiết', color: 'danger' });
        }
      }).catch(err => {
        this.employeeDetailLoading = false;
        const msg = (err.response && err.response.data && err.response.data.message) || 'Lỗi khi tải chi tiết chấm công';
        this.$vs.notify({ title: 'Lỗi', text: msg, color: 'danger' });
      });
    },
    exportMonthlyExcel() {
      if (!this.filteredMonthlySummary || !this.filteredMonthlySummary.length) {
        this.$vs.notify({ title: 'Thông báo', text: 'Không có dữ liệu để xuất Excel', color: 'warning' });
        return;
      }
      this.$vs.loading();
      import('@/vendor/Export2Excel').then(excel => {
        const tHeader = [
          'STT',
          'Mã nhân sự',
          'Họ và tên',
          'Team / Phòng ban',
          'Ca làm việc',
          'Công chuẩn (T2-T6)',
          'Đã làm (công)',
          'Làm Online (ngày)',
          'Đi muộn (lần)',
          'Nghỉ phép (ngày)',
          'Vắng k phép (ngày)',
          'Gửi muộn (lần)'
        ];
        const data = this.filteredMonthlySummary.map((item, index) => [
          index + 1,
          item.employee_code || '',
          item.name || '',
          item.team_name || '',
          item.shift_name || 'Chưa xếp ca',
          item.standard_days || 0,
          item.worked_days || 0,
          item.online_days || 0,
          item.late_fined_count || 0,
          item.leave_approved_days || 0,
          item.unexcused_absent_days || 0,
          item.late_submitted_count || 0
        ]);
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: `Tong_Hop_Cham_Cong_Thang_${this.selectedMonth}_${this.selectedYear}`,
          autoWidth: true,
          bookType: 'xlsx'
        });
        this.$vs.loading.close();
        this.$vs.notify({ title: 'Thành công', text: 'Xuất file Excel thành công', color: 'success' });
      }).catch(err => {
        this.$vs.loading.close();
        this.$vs.notify({ title: 'Lỗi', text: 'Không thể xuất file: ' + (err.message || err), color: 'danger' });
      });
    },
    exportEmployeeDetailExcel() {
      if (!this.employeeDetailData || !this.employeeDetailData.days || !this.employeeDetailData.days.length) {
        return;
      }
      this.$vs.loading();
      import('@/vendor/Export2Excel').then(excel => {
        const emp = this.employeeDetailData.profile;
        const tHeader = [
          'STT',
          'Ngày',
          'Thứ',
          'Ca làm việc',
          'Trạng thái điểm danh',
          'Check-in',
          'Check-out',
          'Số công',
          'Ghi chú'
        ];
        const data = this.employeeDetailData.days.map((d, idx) => [
          idx + 1,
          this.formatDateDMY(d.date),
          d.day_name,
          d.shift_name || '',
          this.getStatusText(d.attendance_status),
          d.check_in || '',
          d.check_out || '',
          d.work_units,
          d.note || ''
        ]);
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: `Chi_Tiet_Cham_Cong_${emp.employee_code || emp.id}_Thang_${this.selectedMonth}_${this.selectedYear}`,
          autoWidth: true,
          bookType: 'xlsx'
        });
        this.$vs.loading.close();
        this.$vs.notify({ title: 'Thành công', text: 'Xuất file Excel chi tiết thành công', color: 'success' });
      }).catch(err => {
        this.$vs.loading.close();
        this.$vs.notify({ title: 'Lỗi', text: 'Không thể xuất file: ' + (err.message || err), color: 'danger' });
      });
    },
    handleQuickSearch() {
      if (!this.quickSearchKeyword) {
        this.$vs.notify({ title: 'Thông báo', text: 'Vui lòng nhập tên, sđt hoặc mã nhân sự', color: 'warning' });
        return;
      }
      this.$vs.loading();
      axios.p('/api/hrm/attendances/quick-search', {
        keyword: this.quickSearchKeyword,
        month: this.selectedMonth,
        year: this.selectedYear
      }).then(res => {
        this.$vs.loading.close();
        if (res.data.status === 1) {
          this.quickSearchResult = res.data.data;
          this.quickSearchModalActive = true;
        } else {
          this.$vs.notify({ title: 'Thông báo', text: res.data.message, color: 'warning' });
        }
      }).catch(err => {
        this.$vs.loading.close();
        this.$vs.notify({ title: 'Lỗi', text: 'Lỗi khi tra cứu', color: 'danger' });
      });
    },
    getStatusStyle(status) {
      switch (status) {
        case 'not_checked':
          return 'color: #6e6b7b; border-color: #d8d6de; background-color: #f8f9fa;';
        case 'present':
          return 'color: #28c76f; border-color: #28c76f; background-color: #f0fdf4;';
        case 'leave_approved':
        case 'leave_approved_half':
          return 'color: #7367f0; border-color: #7367f0; background-color: #f5f3ff;';
        case 'unexcused_absent':
        case 'unexcused_absent_half':
          return 'color: #ea5455; border-color: #ea5455; background-color: #fef2f2;';
        case 'late_fined':
          return 'color: #ff9f43; border-color: #ff9f43; background-color: #fffbeb;';
        case 'late_submitted':
          return 'color: #82868b; border-color: #82868b; background-color: #f9fafb;';
        case 'online':
        case 'online_half':
          return 'color: #7c3aed; border-color: #a78bfa; background-color: #f5f3ff;';
        default:
          return 'color: #6e6b7b; border-color: #d8d6de; background-color: #f8f9fa;';
      }
    },
    getStatusBadgeClass(status) {
      return 'status-' + (status || 'not_checked');
    },
    getStatusText(status) {
      switch (status) {
        case 'present':
          return '✓ Có mặt (1 công)';
        case 'online':
          return '🌐 Làm Online (1 ngày)';
        case 'online_half':
          return '🌐 Làm Online (0.5 ngày)';
        case 'leave_approved':
          return '📅 Nghỉ phép (1 ngày)';
        case 'leave_approved_half':
          return '📅 Nghỉ phép (0.5 ngày)';
        case 'unexcused_absent':
          return '✗ Vắng k phép (1 ngày)';
        case 'unexcused_absent_half':
          return '✗ Vắng k phép (0.5 ngày)';
        case 'late_fined':
          return '⏱ Đi muộn (đã nộp phạt)';
        case 'late_submitted':
          return '⚠ Gửi đơn muộn';
        case 'weekend':
          return '☕ Nghỉ cuối tuần (T7/CN)';
        case 'not_checked':
        default:
          return '⚪ Chưa điểm danh';
      }
    },
    formatDateDMY(dateStr) {
      if (!dateStr) return '';
      const parts = dateStr.split('-');
      if (parts.length === 3) {
        return `${parts[2]}/${parts[1]}/${parts[0]}`;
      }
      return dateStr;
    }
  }
};
</script>

<style>
/* Custom Popup Styling */
.modal-attendance-month-detail.con-vs-popup .vs-popup {
  width: 1040px !important;
  max-width: 96vw !important;
  border-radius: 14px !important;
  overflow: hidden !important;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
}
.modal-attendance-month-detail .vs-popup--header {
  background: #f8fafc !important;
  border-bottom: 1px solid #e2e8f0 !important;
  padding: 16px 24px !important;
}
.modal-attendance-month-detail .vs-popup--title h3 {
  font-size: 16px !important;
  font-weight: 700 !important;
  color: #1e293b !important;
  text-transform: uppercase !important;
  letter-spacing: 0.5px !important;
}
.modal-attendance-month-detail .vs-popup--content {
  padding: 20px 24px !important;
  background: #f8fafc !important;
}
.modal-attendance-month-detail .vs-popup--close {
  top: 14px !important;
  right: 18px !important;
  background: rgba(0, 0, 0, 0.05) !important;
  border-radius: 50% !important;
}

/* Loading */
.att-modal-loading {
  padding: 60px 20px;
  text-align: center;
  color: #64748b;
}
.att-loading-spinner {
  font-size: 32px;
  color: #2563eb;
  margin-bottom: 12px;
}
.att-loading-text {
  font-size: 14px;
  font-weight: 500;
}

/* Profile Banner */
.att-profile-banner {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 16px 20px;
  margin-bottom: 18px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}
.att-profile-top {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid #f1f5f9;
  padding-bottom: 14px;
  margin-bottom: 16px;
  gap: 12px;
}
.att-profile-info {
  display: flex;
  align-items: center;
  gap: 14px;
}
.att-profile-avatar {
  width: 46px;
  height: 46px;
  border-radius: 12px;
  background: #eff6ff;
  color: #2563eb;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 22px;
  border: 1px solid #dbeafe;
  flex-shrink: 0;
}
.att-profile-name {
  font-size: 18px;
  font-weight: 700;
  color: #0f172a;
  margin: 0 0 6px 0;
  line-height: 1.2;
}
.att-profile-tags {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 6px;
}
.att-tag {
  display: inline-flex;
  align-items: center;
  background: #f1f5f9;
  color: #334155;
  padding: 3px 8px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 500;
  border: 1px solid #e2e8f0;
}
.att-profile-actions {
  display: flex;
  align-items: center;
  gap: 10px;
}
.att-chip-month {
  display: inline-flex;
  align-items: center;
  background: #1e40af;
  color: #ffffff;
  padding: 6px 14px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 700;
  box-shadow: 0 2px 4px rgba(30, 64, 175, 0.2);
}
.att-btn-export {
  display: inline-flex;
  align-items: center;
  background: #10b981;
  color: #ffffff;
  border: none;
  padding: 6px 14px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
}
.att-btn-export:hover {
  background: #059669;
}

/* KPI Row */
.att-kpi-row {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 10px;
}
@media (max-width: 900px) {
  .att-kpi-row {
    grid-template-columns: repeat(4, 1fr);
  }
}
@media (max-width: 600px) {
  .att-kpi-row {
    grid-template-columns: repeat(2, 1fr);
  }
}
.att-kpi-item {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  padding: 10px 10px;
  display: flex;
  align-items: center;
  gap: 8px;
  position: relative;
  overflow: hidden;
  transition: transform 0.15s, box-shadow 0.15s;
}
.att-kpi-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}
.att-kpi-item::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
}
.kpi-standard::before { background: #3b82f6; }
.kpi-worked::before { background: #10b981; }
.kpi-online::before { background: #8b5cf6; }
.kpi-late::before { background: #f59e0b; }
.kpi-leave::before { background: #06b6d4; }
.kpi-absent::before { background: #ef4444; }
.kpi-latesub::before { background: #64748b; }

.kpi-icon-wrap {
  width: 30px;
  height: 30px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  flex-shrink: 0;
  background: #ffffff;
  border: 1px solid #e2e8f0;
}
.kpi-standard .kpi-icon-wrap { color: #3b82f6; }
.kpi-worked .kpi-icon-wrap { color: #10b981; }
.kpi-online .kpi-icon-wrap { color: #8b5cf6; }
.kpi-late .kpi-icon-wrap { color: #f59e0b; }
.kpi-leave .kpi-icon-wrap { color: #06b6d4; }
.kpi-absent .kpi-icon-wrap { color: #ef4444; }
.kpi-latesub .kpi-icon-wrap { color: #64748b; }

.kpi-body {
  flex: 1;
  min-width: 0;
}
.kpi-title {
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  color: #64748b;
  letter-spacing: 0.3px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.kpi-num {
  font-size: 17px;
  font-weight: 800;
  color: #0f172a;
  line-height: 1.2;
  margin-top: 2px;
}
.kpi-hint {
  font-size: 10px;
  color: #94a3b8;
  font-weight: 500;
}
.text-success { color: #10b981 !important; }
.text-purple { color: #8b5cf6 !important; }
.text-warning { color: #f59e0b !important; }
.text-blue { color: #0284c7 !important; }
.text-danger { color: #ef4444 !important; }
.text-slate { color: #475569 !important; }

/* Table Container */
.att-table-box {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  max-height: 520px;
  overflow-y: auto;
}
.att-custom-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 13px;
}
.att-custom-table thead th {
  background: #f1f5f9;
  color: #334155;
  font-weight: 700;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 0.4px;
  padding: 12px 14px;
  border-bottom: 2px solid #cbd5e1;
  position: sticky;
  top: 0;
  z-index: 10;
  white-space: nowrap;
}
.att-custom-table tbody td {
  padding: 11px 14px;
  border-bottom: 1px solid #e2e8f0;
  vertical-align: middle;
}
.att-custom-table tbody tr:hover {
  background-color: #f8fafc;
}

/* Weekend Rows */
.att-custom-table tbody tr.is-weekend-row {
  background-color: #fafbfc;
}
.att-custom-table tbody tr.is-weekend-row:hover {
  background-color: #f1f5f9;
}
.att-custom-table tbody tr:last-child td {
  border-bottom: none;
}

.th-center, .td-center {
  text-align: center;
}

/* Day Number Badge */
.att-day-num {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 28px;
  border-radius: 6px;
  background: #f1f5f9;
  color: #0f172a;
  font-weight: 700;
  font-size: 13px;
  border: 1px solid #e2e8f0;
}
.is-weekend-num {
  background: #f1f5f9;
  color: #94a3b8;
  border-color: #e2e8f0;
}

/* Day Label */
.att-day-label {
  font-weight: 600;
  color: #1e293b;
}
.is-weekend-text {
  color: #ef4444;
  font-weight: 700;
}
.att-weekend-badge {
  display: block;
  font-size: 10px;
  color: #94a3b8;
  font-weight: normal;
  margin-top: 1px;
}

/* Shift Pill */
.att-shift-pill {
  display: inline-block;
  padding: 3px 8px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 500;
  background: #f8fafc;
  color: #475569;
  border: 1px solid #e2e8f0;
}
.is-weekend-pill {
  color: #94a3b8;
  border-style: dashed;
  background: transparent;
}

/* Status Badges */
.att-status-badge {
  display: inline-flex;
  align-items: center;
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  line-height: 1.3;
}
.status-present {
  background: #ecfdf5;
  color: #047857;
  border: 1px solid #a7f3d0;
}
.status-online, .status-online_half {
  background: #f5f3ff;
  color: #6d28d9;
  border: 1px solid #ddd6fe;
}
.status-leave_approved, .status-leave_approved_half {
  background: #eff6ff;
  color: #1d4ed8;
  border: 1px solid #bfdbfe;
}
.status-unexcused_absent, .status-unexcused_absent_half {
  background: #fef2f2;
  color: #b91c1c;
  border: 1px solid #fecaca;
}
.status-late_fined {
  background: #fffbeb;
  color: #b45309;
  border: 1px solid #fde68a;
}
.status-late_submitted {
  background: #f1f5f9;
  color: #475569;
  border: 1px solid #cbd5e1;
}
.status-weekend {
  background: #f8fafc;
  color: #64748b;
  border: 1px solid #e2e8f0;
}
.status-not_checked {
  background: #ffffff;
  color: #9ca3af;
  border: 1px dashed #d1d5db;
}

/* Time check-in / check-out */
.att-time-text {
  font-family: monospace;
  font-size: 13px;
  font-weight: 600;
  color: #0f172a;
  background: #f8fafc;
  padding: 2px 6px;
  border-radius: 4px;
  border: 1px solid #e2e8f0;
}
.att-time-dash {
  font-family: monospace;
  font-size: 12px;
  color: #cbd5e1;
}

/* Work Units */
.att-work-badge {
  display: inline-block;
  padding: 3px 8px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 700;
}
.work-positive {
  background: #dcfce7;
  color: #15803d;
  border: 1px solid #bbf7d0;
}
.work-zero {
  color: #94a3b8;
  font-weight: 500;
}

.att-note-cell {
  color: #64748b;
  font-size: 12px;
  max-width: 180px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>
