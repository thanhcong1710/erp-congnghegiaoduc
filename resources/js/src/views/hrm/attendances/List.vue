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
                              <i class="fa-solid fa-laptop mr-1.5"></i> Làm Online
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
                            <option value="leave_approved">📅 Nghỉ phép (1 ngày)</option>
                            <option value="leave_approved_half">📅 Nghỉ phép (0.5 ngày)</option>
                            <option value="unexcused_absent">✗ Vắng k phép (1 ngày)</option>
                            <option value="unexcused_absent_half">✗ Vắng k phép (0.5 ngày)</option>
                            <option value="late_fined">⏱ Đi muộn (đã nộp phạt)</option>
                            <option value="late_submitted">⚠ Gửi đơn muộn</option>
                            <option value="online">🌐 Làm Online</option>
                          </select>

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
              <div class="mr-3 mb-2">
                <input
                  type="text"
                  class="vs-inputx vs-input--input normal"
                  style="padding: 8px 14px; font-size: 13px; min-width: 200px;"
                  placeholder="Lọc tên, mã NS..."
                  v-model="monthlySearchKeyword"
                />
              </div>
            </div>
            <div class="text-sm font-semibold text-primary mb-2">
              <i class="fa-solid fa-circle-info mr-1"></i> Công chuẩn tháng:
              <strong class="text-base text-gray-900">{{
                standardDays
              }}</strong>
              công
            </div>
          </div>

          <!-- Bảng tổng hợp công tháng đồng bộ -->
          <div class="vs-component vs-con-table stripe vs-table-primary">
            <div class="con-tablex vs-table--content">
              <div class="vs-con-tbody vs-table--tbody">
                <table class="vs-table vs-table--tbody-table">
                  <thead class="vs-table--thead">
                    <tr>
                      <th width="4%" class="text-center">STT</th>
                      <th width="9%">Mã NS</th>
                      <th width="16%">Họ và tên</th>
                      <th width="14%">Team</th>
                      <th width="9%" class="text-center">Công chuẩn</th>
                      <th width="9%" class="text-center">Đã làm</th>
                      <th width="10%" class="text-center">Làm Online</th>
                      <th width="9%" class="text-center">Đi muộn</th>
                      <th width="9%" class="text-center">Nghỉ phép</th>
                      <th width="11%" class="text-center">Vắng k phép</th>
                      <th width="10%" class="text-center">Gửi muộn</th>
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
                    </tr>
                    <tr v-if="!filteredMonthlySummary.length">
                      <td colspan="11" class="text-center p-4 text-gray-500">
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
      monthlySummary: [],
      monthlySearchKeyword: '',
      monthlyPage: 1,
      monthlyPerPage: 20,
      standardDays: 26,
      quickSearchKeyword: '',
      quickSearchModalActive: false,
      quickSearchResult: null,
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
        (r.team_name && r.team_name.toLowerCase().includes(kw))
      );
    },
    paginatedMonthlySummary() {
      const start = (this.monthlyPage - 1) * this.monthlyPerPage;
      return this.filteredMonthlySummary.slice(start, start + this.monthlyPerPage);
    },
    totalMonthlyPages() {
      return Math.ceil(this.filteredMonthlySummary.length / this.monthlyPerPage) || 1;
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
      axios.p('/api/hrm/attendances/monthly-summary', {
        month: this.selectedMonth,
        year: this.selectedYear
      }).then(res => {
        this.monthlySummary = res.data.data || [];
        this.standardDays = res.data.standard_days || 26;
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
          return 'color: #7c3aed; border-color: #a78bfa; background-color: #f5f3ff;';
        default:
          return 'color: #6e6b7b; border-color: #d8d6de; background-color: #f8f9fa;';
      }
    }
  }
};
</script>
