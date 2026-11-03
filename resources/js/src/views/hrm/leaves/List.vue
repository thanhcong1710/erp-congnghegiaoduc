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
      <vs-tab label="Quản lý quỹ phép năm (Toàn nhân sự)" icon-pack="feather" icon="icon-users">
        
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
                  <span class="text-xs font-semibold text-gray-600 uppercase block mb-1">Tổng ngày nghỉ toàn cty</span>
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
          <!-- Toolbar Lọc -->
          <div class="flex flex-wrap items-center justify-between mb-4">
            <div class="flex flex-wrap items-center mb-2 md:mb-0">
              <!-- Chọn năm -->
              <div class="mr-3 mb-2 flex items-center">
                <span class="text-sm font-medium mr-2 text-gray-700">Năm:</span>
                <select v-model="filterYear" @change="fetchAllBalances" class="vs-inputx vs-input--input normal" style="padding: 9px 12px; font-size: 13px; width: 100px;">
                  <option value="2026">2026</option>
                  <option value="2025">2025</option>
                  <option value="2024">2024</option>
                </select>
              </div>

              <!-- Chọn phòng ban -->
              <div class="mr-3 mb-2 flex items-center">
                <span class="text-sm font-medium mr-2 text-gray-700">Phòng ban:</span>
                <select v-model="filterDepartment" @change="fetchAllBalances" class="vs-inputx vs-input--input normal" style="padding: 9px 12px; font-size: 13px; min-width: 170px;">
                  <option value="all">-- Tất cả phòng ban --</option>
                  <option :value="dept.id" v-for="dept in departments" :key="dept.id">{{ dept.name }}</option>
                </select>
              </div>
            </div>

            <!-- Tìm kiếm & Nút xuất file -->
            <div class="flex items-center mb-2 md:mb-0">
              <input 
                type="text" 
                class="vs-inputx vs-input--input normal mr-2" 
                style="padding: 9px 12px; font-size: 13px; min-width: 240px;" 
                placeholder="Tìm họ tên, mã nhân sự..." 
                v-model="searchStaffKeyword" 
                @input="onSearchStaffInput"
              >
              <vs-button color="primary" type="border" class="p-2" @click="fetchAllBalances" title="Làm mới">
                <feather-icon icon="RefreshCwIcon" class="h-4 w-4" />
              </vs-button>
              <vs-button color="dark" type="border" class="p-2 ml-2" @click="exportToExcel" title="Xuất Excel bảng này">
                <i class="fa-solid fa-file-excel mr-1 text-success"></i> Xuất Excel
              </vs-button>
            </div>
          </div>

          <!-- Bảng Danh Sách Quỹ Phép Toàn Bộ Nhân Sự (Khớp Bảng 2) -->
          <div class="vs-component vs-con-table stripe vs-table-primary">
            <div class="con-tablex vs-table--content">
              <div class="vs-con-tbody vs-table--tbody">
                <table class="vs-table vs-table--tbody-table">
                  <thead class="vs-table--thead">
                    <tr>
                      <th width="4%" class="text-center">STT</th>
                      <th width="10%">Mã NS</th>
                      <th width="18%">Họ và tên</th>
                      <th width="15%">Phòng ban</th>
                      <th width="10%" class="text-center">Phép năm</th>
                      <th width="10%" class="text-center">Đã sử dụng</th>
                      <th width="10%" class="text-center">Còn lại</th>
                      <th width="13%" class="text-center">Tình trạng</th>
                      <th width="10%" class="text-center" style="white-space: nowrap;">Thao tác</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="tr-values vs-table--tr" v-for="(emp, index) in staffBalances.data" :key="emp.id">
                      <td class="td vs-table--td text-center">
                        {{ index + 1 + ((staffBalances.pagination.current_page || 1) - 1) * (staffBalances.pagination.per_page || 20) }}
                      </td>
                      <td class="td vs-table--td">
                        <span class="font-bold text-primary">{{ emp.employee_code }}</span>
                      </td>
                      <td class="td vs-table--td font-semibold text-gray-900 cursor-pointer hover:text-primary" @click="viewEmployeeHistory(emp)">
                        {{ emp.full_name }}
                        <span v-if="emp.work_status === 'resigned'" class="ml-1 text-xs text-danger font-normal">(Đã nghỉ)</span>
                      </td>
                      <td class="td vs-table--td text-sm text-gray-700">
                        {{ emp.department_name }}
                      </td>
                      <td class="td vs-table--td text-center font-semibold text-gray-600">
                        {{ emp.total_days }} ngày
                      </td>
                      <td class="td vs-table--td text-center font-bold text-warning">
                        {{ emp.used_days }} ngày
                      </td>
                      <td class="td vs-table--td text-center font-bold" :class="emp.remaining_days > 0 ? 'text-success' : 'text-danger'">
                        {{ emp.remaining_days }} ngày
                      </td>
                      <td class="td vs-table--td text-center">
                        <vs-chip v-if="emp.is_over_limit" color="danger">
                          Vượt +{{ emp.over_days }} ngày
                        </vs-chip>
                        <vs-chip v-else-if="emp.is_out_of_leave" color="warning">
                          Đã hết phép
                        </vs-chip>
                        <vs-chip v-else-if="emp.used_days > 0" color="success">
                          Bình thường
                        </vs-chip>
                        <vs-chip v-else color="dark">
                          Chưa nghỉ
                        </vs-chip>
                      </td>
                      <td class="td vs-table--td text-center list-action">
                        <vs-button 
                          size="small" 
                          color="primary" 
                          type="border"
                          @click="viewEmployeeHistory(emp)"
                          title="Xem chi tiết các đợt nghỉ phép"
                        >
                          <i class="fa-solid fa-clock-rotate-left mr-1"></i> Chi tiết
                        </vs-button>
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
          <div class="flex flex-wrap items-center justify-between mt-5" v-if="staffBalances.pagination.total > staffBalances.pagination.per_page">
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
              <select v-model="filterStatus" @change="fetchLeaves" class="vs-inputx vs-input--input normal" style="padding: 9px 12px; font-size: 13px; min-width: 170px;">
                <option value="all">-- Tất cả trạng thái --</option>
                <option value="0">Chờ duyệt</option>
                <option value="1">Đã phê duyệt</option>
                <option value="2">Bị từ chối</option>
              </select>
            </div>

            <div class="mb-2 md:mb-0">
              <input 
                type="text" 
                class="vs-inputx vs-input--input normal" 
                style="padding: 9px 12px; font-size: 13px; min-width: 250px;" 
                placeholder="Tìm theo tên nhân viên..." 
                v-model="searchKeyword" 
                @input="fetchLeaves"
              >
            </div>
          </div>

          <!-- Table danh sách đơn -->
          <div class="vs-component vs-con-table stripe vs-table-primary">
            <div class="con-tablex vs-table--content">
              <div class="vs-con-tbody vs-table--tbody">
                <table class="vs-table vs-table--tbody-table">
                  <thead class="vs-table--thead">
                    <tr>
                      <th width="5%" class="text-center">STT</th>
                      <th width="12%">Mã NS</th>
                      <th width="18%">Người làm đơn</th>
                      <th width="15%">Loại yêu cầu</th>
                      <th width="18%">Thời gian xin phép</th>
                      <th width="8%" class="text-center">Số ngày</th>
                      <th width="12%">Trạng thái</th>
                      <th width="15%">Phê duyệt</th>
                      <th width="80" class="text-center" style="white-space: nowrap;">Thao tác</th>
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
                        <div>{{ tr.start_date }} <i class="fa fa-arrow-right text-xs text-gray-400"></i> {{ tr.end_date }}</div>
                        <div class="text-xs text-gray-500 truncate" :title="tr.reason">Lý do: {{ tr.reason }}</div>
                      </td>
                      <td class="td vs-table--td text-center font-bold text-gray-800">{{ tr.duration_days }}</td>
                      <td class="td vs-table--td">
                        <vs-chip :color="tr.status === 1 ? 'success' : (tr.status === 2 ? 'danger' : 'warning')">
                          {{ tr.status === 1 ? 'Đã duyệt' : (tr.status === 2 ? 'Từ chối' : 'Chờ duyệt') }}
                        </vs-chip>
                      </td>
                      <td class="td vs-table--td">
                        <span v-if="tr.status === 1" class="font-semibold text-xs" :class="getApprovalTypeClass(tr.approval_type)">
                          {{ getApprovalTypeText(tr.approval_type) }}
                        </span>
                        <span v-else-if="tr.status === 2" class="text-danger text-xs">
                          {{ tr.rejection_reason || 'Từ chối' }}
                        </span>
                        <span v-else class="text-gray-400 text-xs">---</span>
                      </td>
                      <td class="td vs-table--td text-center list-action">
                        <vs-button 
                          v-if="tr.status === 0" 
                          size="small" 
                          color="primary" 
                          @click="openApprovePopup(tr)"
                          title="Phê duyệt đơn"
                        >
                          <i class="fa-solid fa-check"></i>
                        </vs-button>
                      </td>
                    </tr>
                    <tr v-if="!leaves.data || !leaves.data.length">
                      <td colspan="9" class="text-center p-4 text-gray-500">Không có dữ liệu đơn nghỉ phép</td>
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
    <vs-popup :title="popupHistoryTitle" :active.sync="popupHistoryActive" class="popup-leave-history">
      <div class="p-4" v-if="selectedStaffHistory">
        <!-- Tóm tắt số ngày phép của nhân viên này -->
        <div class="vx-row mb-4">
          <div class="vx-col w-1/4">
            <div class="p-3 bg-blue-50 border border-blue-200 rounded text-center">
              <span class="text-xs text-gray-600 block">Quỹ phép {{ selectedStaffHistory.summary.year }}</span>
              <span class="text-xl font-bold text-primary">{{ selectedStaffHistory.summary.total_days }} ngày</span>
            </div>
          </div>
          <div class="vx-col w-1/4">
            <div class="p-3 bg-yellow-50 border border-yellow-200 rounded text-center">
              <span class="text-xs text-gray-600 block">Đã sử dụng</span>
              <span class="text-xl font-bold text-warning">{{ selectedStaffHistory.summary.used_days }} ngày</span>
            </div>
          </div>
          <div class="vx-col w-1/4">
            <div class="p-3 bg-green-50 border border-green-200 rounded text-center">
              <span class="text-xs text-gray-600 block">Còn lại</span>
              <span class="text-xl font-bold text-success">{{ selectedStaffHistory.summary.remaining_days }} ngày</span>
            </div>
          </div>
          <div class="vx-col w-1/4">
            <div class="p-3 border rounded text-center" :class="selectedStaffHistory.summary.over_days > 0 ? 'bg-red-50 border-red-200' : 'bg-gray-50 border-gray-200'">
              <span class="text-xs text-gray-600 block">Vượt phép</span>
              <span class="text-xl font-bold" :class="selectedStaffHistory.summary.over_days > 0 ? 'text-danger' : 'text-gray-400'">
                {{ selectedStaffHistory.summary.over_days }} ngày
              </span>
            </div>
          </div>
        </div>

        <h5 class="font-bold text-gray-800 mb-2 flex items-center">
          <i class="fa-solid fa-list-check mr-2 text-primary"></i> Lịch Sử Chi Tiết Từng Đợt Nghỉ ({{ selectedStaffHistory.leaves.length }} đợt)
        </h5>

        <!-- Table chi tiết các đợt nghỉ -->
        <div class="vs-component vs-con-table stripe vs-table-primary">
          <div class="con-tablex vs-table--content">
            <div class="vs-con-tbody vs-table--tbody" style="max-height: 400px; overflow-y: auto;">
              <table class="vs-table vs-table--tbody-table">
                <thead class="vs-table--thead">
                  <tr>
                    <th width="6%" class="text-center">STT</th>
                    <th width="24%">Thời gian nghỉ</th>
                    <th width="12%" class="text-center">Số ngày</th>
                    <th width="28%">Lý do / Ghi chú</th>
                    <th width="18%">Chế độ phê duyệt</th>
                    <th width="12%" class="text-center">Trạng thái</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(lv, idx) in selectedStaffHistory.leaves" :key="lv.id">
                    <td class="text-center">{{ idx + 1 }}</td>
                    <td>
                      <div class="font-semibold text-gray-900">{{ lv.start_date }}</div>
                      <div class="text-xs text-gray-400" v-if="lv.start_date !== lv.end_date">đến {{ lv.end_date }}</div>
                    </td>
                    <td class="text-center font-bold text-primary">{{ lv.duration_days }}</td>
                    <td class="text-sm text-gray-700">{{ lv.reason || '---' }}</td>
                    <td>
                      <span class="text-xs font-semibold" :class="getApprovalTypeClass(lv.approval_type)">
                        {{ getApprovalTypeText(lv.approval_type) }}
                      </span>
                    </td>
                    <td class="text-center">
                      <vs-chip :color="lv.status === 1 ? 'success' : (lv.status === 2 ? 'danger' : 'warning')">
                        {{ lv.status === 1 ? 'Đã duyệt' : (lv.status === 2 ? 'Từ chối' : 'Chờ duyệt') }}
                      </vs-chip>
                    </td>
                  </tr>
                  <tr v-if="!selectedStaffHistory.leaves.length">
                    <td colspan="6" class="text-center p-3 text-gray-400">Chưa có lịch sử nghỉ phép nào trong năm này</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="flex justify-end mt-4">
          <vs-button color="dark" type="border" @click="popupHistoryActive = false">Đóng</vs-button>
        </div>
      </div>
    </vs-popup>

    <!-- ========================================== -->
    <!-- POPUP 2: IMPORT BẢNG NGHỈ PHÉP TỰ ĐỘNG -->
    <!-- ========================================== -->
    <vs-popup title="IMPORT BẢNG KẾ HOẠCH NGHỈ PHÉP TỪ EXCEL" :active.sync="popupImportActive">
      <div class="p-3">
        <p class="text-sm text-gray-600 mb-2">
          Dán các dòng copy trực tiếp từ Excel / Google Sheet vào khung dưới đây theo thứ tự cột:
          <br>
          <code class="text-xs bg-gray-100 p-1 rounded font-mono text-primary font-bold">
            [Họ và tên] [Tab] [Số ngày] [Tab] [Ngày] [Tab] [Chức vụ] [Tab] [Phòng ban] [Tab] [Ghi chú]
          </code>
        </p>

        <textarea 
          rows="7" 
          class="vs-inputx vs-input--input normal w-full font-mono text-xs" 
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

          <div style="max-height: 200px; overflow-y: auto;" class="border rounded">
            <table class="w-full text-xs">
              <thead class="bg-gray-100 text-gray-600">
                <tr>
                  <th class="p-1">#</th>
                  <th class="p-1 text-left">Họ tên</th>
                  <th class="p-1 text-center">Số ngày</th>
                  <th class="p-1 text-left">Ngày</th>
                  <th class="p-1 text-left">Phòng ban</th>
                  <th class="p-1 text-left">Ghi chú</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(row, rIdx) in parsedRows.slice(0, 50)" :key="rIdx" class="border-t">
                  <td class="p-1 text-center">{{ rIdx + 1 }}</td>
                  <td class="p-1 font-semibold">{{ row.name }}</td>
                  <td class="p-1 text-center font-bold text-primary">{{ row.days }}</td>
                  <td class="p-1">{{ row.date }}</td>
                  <td class="p-1">{{ row.dept }}</td>
                  <td class="p-1 text-gray-500">{{ row.note }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <p class="text-xs text-gray-400 mt-1" v-if="parsedRows.length > 50">
            ... và {{ parsedRows.length - 50 }} dòng khác.
          </p>
        </div>

        <div class="flex justify-end mt-4">
          <vs-button 
            color="success" 
            type="filled" 
            class="mr-2"
            :disabled="!parsedRows.length || isImporting"
            @click="submitImport"
          >
            <i class="fa-solid fa-cloud-arrow-up mr-1"></i> {{ isImporting ? 'Đang Import...' : 'Xác Nhận Import' }}
          </vs-button>
          <vs-button color="dark" type="border" @click="popupImportActive = false">Hủy</vs-button>
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
            <select class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.request_category">
              <option value="leave">Xin nghỉ phép thường</option>
              <option value="shift_change">Xin đổi ca làm việc</option>
              <option value="off_to_onl">Xin chuyển làm Off sang Onl (Hưởng 50% lương)</option>
            </select>
          </div>
        </div>

        <div class="vx-row mb-4" v-if="form.request_category === 'leave'">
          <div class="vx-col w-full">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Loại nghỉ phép</label>
            <select class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.leave_type_id">
              <option :value="item.id" v-for="item in leaveTypes" :key="item.id">{{ item.name }}</option>
            </select>
          </div>
        </div>

        <div class="vx-row mb-4">
          <div class="vx-col w-1/3">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Từ ngày (*)</label>
            <input type="date" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.start_date" @change="calculateDays" />
          </div>
          <div class="vx-col w-1/3">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Đến ngày (*)</label>
            <input type="date" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.end_date" @change="calculateDays" />
          </div>
          <div class="vx-col w-1/3">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Số ngày (*)</label>
            <input type="number" step="0.5" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.duration_days" />
          </div>
        </div>

        <div class="vx-row mb-4" v-if="form.request_category === 'shift_change'">
          <div class="vx-col w-full">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Thông tin ca xin đổi</label>
            <input type="text" placeholder="Ví dụ: Xin đổi từ ca Sáng sang ca Chiều ngày 15/10" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.target_shift_info" />
          </div>
        </div>

        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Lý do chi tiết (*)</label>
            <textarea placeholder="Nhập lý do xin nghỉ hoặc đổi ca..." rows="3" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="form.reason"></textarea>
          </div>
        </div>

        <div class="vx-row mt-6">
          <div class="vx-col w-full flex justify-end">
            <vs-button @click="saveData()" color="success" type="filled" class="p-3 mr-2"><i class="fa-solid fa-paper-plane mr-1"></i> Gửi Đơn</vs-button>
            <vs-button @click="popupActive = false" color="dark" type="border" class="p-3"><i class="fa-solid fa-xmark mr-1"></i> Hủy</vs-button>
          </div>
        </div>
      </div>
    </vs-popup>

    <!-- ========================================== -->
    <!-- POPUP 4: PHÊ DUYỆT ĐƠN NGHỈ PHÉP (HIỆN CÓ) -->
    <!-- ========================================== -->
    <vs-popup title="PHÊ DUYỆT ĐƠN NGHỈ PHÉP" :active.sync="approvePopupActive">
      <div class="p-4" v-if="selectedLeave">
        <p class="mb-4 text-sm text-gray-700">
          Phê duyệt đơn của nhân sự: <strong class="text-primary">{{ selectedLeave.employee ? selectedLeave.employee.full_name : (selectedLeave.user ? selectedLeave.user.name : '') }}</strong> 
          ({{ selectedLeave.duration_days }} ngày: {{ selectedLeave.start_date }} đến {{ selectedLeave.end_date }})
        </p>

        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <label class="text-sm font-semibold text-gray-800 mb-2 block">Lựa chọn phân loại phê duyệt của HR (*)</label>
            <div class="space-y-2">
              <label class="flex items-center p-3 border rounded cursor-pointer transition hover:bg-gray-50" :class="{'border-success bg-green-50': approveForm.approval_type === 'approved'}">
                <input type="radio" value="approved" v-model="approveForm.approval_type" class="mr-3" />
                <div>
                  <span class="font-bold text-success block">Đã duyệt</span>
                  <span class="text-xs text-gray-500">Hưởng 100% lương / Tự động trừ vào số ngày phép năm</span>
                </div>
              </label>

              <label class="flex items-center p-3 border rounded cursor-pointer transition hover:bg-gray-50" :class="{'border-warning bg-orange-50': approveForm.approval_type === 'half_salary'}">
                <input type="radio" value="half_salary" v-model="approveForm.approval_type" class="mr-3" />
                <div>
                  <span class="font-bold text-warning block">50% lương</span>
                  <span class="text-xs text-gray-500">Áp dụng cho nhân sự xin chuyển làm Off sang Onl</span>
                </div>
              </label>

              <label class="flex items-center p-3 border rounded cursor-pointer transition hover:bg-gray-50" :class="{'border-gray-500 bg-gray-50': approveForm.approval_type === 'late_unpaid'}">
                <input type="radio" value="late_unpaid" v-model="approveForm.approval_type" class="mr-3" />
                <div>
                  <span class="font-bold text-gray-700 block">Gửi đơn muộn (không lương)</span>
                  <span class="text-xs text-gray-500">Nhân viên gửi đơn muộn sau hạn quy định, không tính lương</span>
                </div>
              </label>

              <label class="flex items-center p-3 border rounded cursor-pointer transition hover:bg-gray-50" :class="{'border-danger bg-red-50': approveForm.approval_type === 'rejected'}">
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
            <label class="text-sm font-semibold text-gray-700 block mb-1">Lý do từ chối (*)</label>
            <textarea placeholder="Nhập lý do từ chối..." rows="3" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="approveForm.rejection_reason"></textarea>
          </div>
        </div>

        <div class="vx-row mt-6">
          <div class="vx-col w-full flex justify-end">
            <vs-button @click="submitApproval()" color="success" type="filled" class="p-3 mr-2"><i class="fa-solid fa-check mr-1"></i> Xác Nhận Duyệt</vs-button>
            <vs-button @click="approvePopupActive = false" color="dark" type="border" class="p-3"><i class="fa-solid fa-xmark mr-1"></i> Đóng</vs-button>
          </div>
        </div>
      </div>
    </vs-popup>

  </div>
</template>

<script>
import axios from '../../../http/axios.js';

export default {
  data() {
    return {
      // Tab 1: Quản lý Quỹ phép Toàn Nhân Sự
      filterYear: '2026',
      filterDepartment: 'all',
      searchStaffKeyword: '',
      searchDebounce: null,
      departments: [],
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
      }
    };
  },
  computed: {
    popupHistoryTitle() {
      if (!this.selectedStaffHistory) return 'CHI TIẾT LỊCH SỬ NGHỈ PHÉP';
      return `LỊCH SỬ NGHỈ PHÉP - ${this.selectedStaffHistory.employee.full_name} (${this.selectedStaffHistory.employee.employee_code})`;
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
          // Find parts
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
      this.selectedLeave = leave;
      this.approveForm = {
        approval_type: leave.request_category === 'off_to_onl' ? 'half_salary' : 'approved',
        rejection_reason: ''
      };
      this.approvePopupActive = true;
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
.list-action button {
  padding: 6px 10px !important;
  font-size: 12px;
}
.popup-leave-history .vs-popup {
  width: 850px !important;
}
</style>
