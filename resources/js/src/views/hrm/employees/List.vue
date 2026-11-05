<template>
  <div id="page-hrm-employees-list">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between mb-4">
      <div class="mb-2 md:mb-0">
        <h3 class="font-bold text-gray-800 flex items-center">
          <feather-icon icon="UsersIcon" class="mr-2 text-primary" /> Quản Lý Danh Sách Nhân Sự
        </h3>
        <p class="text-sm text-gray-500 mt-1">Phân loại Fulltime, CTV, Thử việc & theo dõi trạng thái hoạt động</p>
      </div>
      <div v-if="isHrmAdmin">
        <router-link :to="'/hrm/employees/add'">
          <vs-button class="p-3" color="success" type="border">
            <i class="fa-solid fa-user-plus mr-2"></i> Thêm Nhân Sự Mới
          </vs-button>
        </router-link>
      </div>
    </div>

    <vx-card no-shadow>
      <!-- Toolbar lọc & tìm kiếm -->
      <div class="flex flex-wrap items-center justify-between mb-4 pb-3 border-b border-gray-200">
        <!-- Bộ lọc phân loại Tab -->
        <div class="flex flex-wrap items-center space-x-2 mb-2 md:mb-0">
          <vs-button 
            :type="filterType === 'all' ? 'filled' : 'border'" 
            color="primary" 
            size="small" 
            class="py-2 px-3"
            @click="setFilterType('all')"
          >
            Tất cả ({{ employees.length }})
          </vs-button>
          <vs-button 
            :type="filterType === 'fulltime' ? 'filled' : 'border'" 
            color="success" 
            size="small" 
            class="py-2 px-3"
            @click="setFilterType('fulltime')"
          >
            Fulltime ({{ countFulltime }})
          </vs-button>
          <vs-button 
            :type="filterType === 'ctv' ? 'filled' : 'border'" 
            color="warning" 
            size="small" 
            class="py-2 px-3"
            @click="setFilterType('ctv')"
          >
            Cộng tác viên ({{ countCtv }})
          </vs-button>
          <vs-button 
            :type="filterType === 'probation' ? 'filled' : 'border'" 
            color="dark" 
            size="small" 
            class="py-2 px-3"
            @click="setFilterType('probation')"
          >
            Thử việc ({{ countProbation }})
          </vs-button>
        </div>

        <!-- Bộ lọc trạng thái & ô tìm kiếm -->
        <div class="flex flex-wrap items-center">
          <div class="mr-3 mb-2 md:mb-0">
            <select v-model="filterWorkStatus" @change="fetchEmployees" class="vs-inputx vs-input--input normal" style="padding: 10px 14px; font-size: 13px; min-width: 170px;">
              <option value="all">-- Tất cả trạng thái --</option>
              <option value="working">Đang làm việc</option>
              <option value="resigned">Đã nghỉ việc</option>
            </select>
          </div>

          <div class="mb-2 md:mb-0">
            <input 
              type="text" 
              class="vs-inputx vs-input--input normal" 
              style="padding: 10px 14px; font-size: 13px; min-width: 250px;" 
              placeholder="Mã, tên, số điện thoại..." 
              v-model="searchKeyword" 
              @input="fetchEmployees"
            >
          </div>
        </div>
      </div>

      <!-- Table chuẩn hệ thống -->
      <div class="vs-component vs-con-table stripe vs-table-primary">
        <div class="con-tablex vs-table--content">
          <div class="vs-con-tbody vs-table--tbody">
            <table class="vs-table vs-table--tbody-table">
              <thead class="vs-table--thead">
                <tr>
                  <th width="4%" class="text-center">STT</th>
                  <th width="9%">Mã NS</th>
                  <th width="16%">Họ và tên</th>
                  <th width="15%">Mã đăng nhập</th>
                  <th width="13%">Team / Bộ phận</th>
                  <th width="11%">Phân loại</th>
                  <th width="13%">Thời gian thử việc</th>
                  <th width="9%">Trạng thái</th>
                  <th width="10%" class="text-center" style="white-space: nowrap;">Thao tác</th>
                </tr>
              </thead>
              <tbody>
                <tr class="tr-values vs-table--tr" v-for="(tr, index) in paginatedEmployees" :key="tr.id || index">
                  <td class="td vs-table--td text-center">{{ (currentPage - 1) * perPage + index + 1 }}</td>
                  <td class="td vs-table--td font-bold text-primary">{{ tr.employee_code || '---' }}</td>
                  <td class="td vs-table--td">
                    <div class="font-semibold text-gray-900">{{ tr.full_name || (tr.user ? tr.user.name : '') }}</div>
                    <div class="text-xs text-gray-500">{{ tr.phone || tr.email || (tr.user ? tr.user.email : '') }}</div>
                  </td>
                  <td class="td vs-table--td">
                    <!-- Đã có tài khoản User -->
                    <div v-if="tr.user" class="flex items-center justify-between">
                      <div>
                        <span class="font-bold text-success text-sm flex items-center" title="Mã đăng nhập hệ thống">
                          <i class="fa-solid fa-key text-xs mr-1 text-success"></i>
                          {{ tr.user.hrm_id || '---' }}
                        </span>
                        <div class="text-xs text-gray-600 truncate" style="max-width: 140px;" :title="tr.user.name + (tr.user.email ? ' (' + tr.user.email + ')' : '')">
                          {{ tr.user.name }}
                        </div>
                      </div>
                      <div class="flex items-center ml-1" v-if="isHrmAdmin">
                        <button 
                          type="button" 
                          class="text-primary hover:text-primary-dark p-1 text-xs" 
                          title="Đổi tài khoản liên kết"
                          @click="openLinkUserModal(tr)"
                        >
                          <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button 
                          type="button" 
                          class="text-danger hover:text-danger-dark p-1 text-xs ml-1" 
                          title="Hủy liên kết tài khoản"
                          @click="confirmUnlinkUser(tr)"
                        >
                          <i class="fa-solid fa-link-slash"></i>
                        </button>
                      </div>
                    </div>

                    <!-- Chưa liên kết tài khoản -->
                    <div v-else class="flex flex-col items-start">
                      <span class="text-xs text-gray-400 mb-1 flex items-center">
                        <i class="fa-regular fa-circle-question mr-1"></i> Chưa liên kết
                      </span>
                      <vs-button 
                        v-if="isHrmAdmin"
                        size="small" 
                        color="primary" 
                        type="border" 
                        class="py-1 px-2 text-xs"
                        @click="openLinkUserModal(tr)"
                      >
                        <i class="fa-solid fa-link mr-1"></i> Link tài khoản
                      </vs-button>
                    </div>
                  </td>
                  <td class="td vs-table--td">{{ tr.department ? tr.department.name : 'Chưa phân bổ' }}</td>
                  <td class="td vs-table--td">
                    <vs-chip :color="getEmployeeTypeColor(tr.employee_type)">
                      {{ getEmployeeTypeText(tr.employee_type) }}
                    </vs-chip>
                    <div v-if="tr.employee_type === 'ctv' && tr.approval_status === 0" class="mt-1">
                      <span class="text-xs text-danger font-semibold"><i class="fa fa-clock"></i> Chờ HR duyệt</span>
                    </div>
                  </td>
                  <td class="td vs-table--td text-xs">
                    <div v-if="tr.employee_type === 'probation'">
                      <div>Từ: <strong>{{ tr.probation_start_date ? (tr.probation_start_date | formatDateView) : '---' }}</strong></div>
                      <div>Đến: <strong>{{ tr.probation_end_date ? (tr.probation_end_date | formatDateView) : '---' }}</strong></div>
                      <vs-button 
                        v-if="isHrmAdmin"
                        size="small" 
                        color="success" 
                        type="border" 
                        class="mt-1 py-1 px-2 text-xs" 
                        @click="convertToFulltime(tr.id)"
                      >
                        <i class="fa fa-arrow-up mr-1"></i> Lên Fulltime
                      </vs-button>
                    </div>
                    <span v-else class="text-gray-400">---</span>
                  </td>
                  <td class="td vs-table--td">
                    <vs-chip :color="tr.work_status === 'working' ? 'success' : 'danger'">
                      {{ tr.work_status === 'working' ? 'Đang làm' : 'Đã nghỉ' }}
                    </vs-chip>
                    <div v-if="tr.work_status === 'resigned' && tr.resigned_reason" class="text-xs text-gray-500 mt-1">
                      {{ tr.resigned_reason }}
                    </div>
                  </td>
                  <td class="td vs-table--td text-center list-action" style="white-space: nowrap;">
                    <!-- Nút HR duyệt CTV -->
                    <vs-button 
                      v-if="isHrmAdmin && tr.employee_type === 'ctv' && tr.approval_status === 0" 
                      size="small" 
                      color="primary" 
                      class="mr-1"
                      @click="approveCtv(tr.id)"
                      title="Duyệt CTV"
                    >
                      <i class="fa-solid fa-check"></i>
                    </vs-button>

                    <!-- Nút đổi trạng thái hoạt động -->
                    <vs-button 
                      v-if="isHrmAdmin"
                      size="small" 
                      color="warning" 
                      type="border" 
                      class="mr-1"
                      @click="openWorkStatusModal(tr)" 
                      title="Đổi trạng thái hoạt động"
                    >
                      <i class="fa-solid fa-user-clock"></i>
                    </vs-button>

                    <!-- Nút sửa hồ sơ (chỉ Admin/HR) -->
                    <router-link v-if="isHrmAdmin" :to="`/hrm/employees/edit/${tr.id}`">
                      <vs-button size="small" color="success" title="Chỉnh sửa"><i class="fa-solid fa-edit"></i></vs-button>
                    </router-link>

                    <!-- Nút xem hồ sơ (Dành cho role khác chỉ view danh sách) -->
                    <vs-button 
                      v-else 
                      size="small" 
                      color="primary" 
                      type="border" 
                      title="Xem chi tiết hồ sơ"
                      @click="openViewModal(tr)"
                    >
                      <i class="fa-solid fa-eye"></i>
                    </vs-button>

                    <!-- Nút xóa nhân sự (chỉ hiển thị cho Admin và Kế toán) -->
                    <vs-button 
                      v-if="canDeleteEmployee"
                      size="small" 
                      color="danger" 
                      type="filled" 
                      class="ml-1"
                      @click="confirmDeleteEmployee(tr)" 
                      title="Xóa nhân sự (chỉ Admin/Kế toán)"
                    >
                      <i class="fa-solid fa-trash"></i>
                    </vs-button>
                  </td>
                </tr>
                <tr v-if="!filteredEmployees.length">
                  <td colspan="9" class="text-center p-4 text-gray-500">Không có dữ liệu nhân sự</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Phân trang -->
      <div class="flex flex-wrap items-center justify-between mt-5" v-if="filteredEmployees.length > 0">
        <div class="text-sm text-gray-600 mb-2 md:mb-0">
          Hiển thị <strong>{{ filteredEmployees.length ? (currentPage - 1) * perPage + 1 : 0 }}</strong> - <strong>{{ Math.min(currentPage * perPage, filteredEmployees.length) }}</strong> trong tổng số <strong>{{ filteredEmployees.length }}</strong> nhân sự
        </div>
        <div class="flex items-center" v-if="filteredEmployees.length > perPage">
          <span class="text-sm text-gray-600 mr-2">Mỗi trang:</span>
          <select v-model="perPage" @change="currentPage = 1" class="vs-inputx vs-input--input normal mr-4" style="padding: 6px 10px; font-size: 13px;">
            <option :value="10">10</option>
            <option :value="20">20</option>
            <option :value="50">50</option>
            <option :value="100">100</option>
          </select>
          <vs-pagination
            :total="totalPages"
            v-model="currentPage"
          />
        </div>
      </div>
    </vx-card>

    <!-- Modal Cập nhật trạng thái hoạt động (Đang làm / Đã nghỉ) -->
    <vs-popup title="CẬP NHẬT TRẠNG THÁI HOẠT ĐỘNG" :active.sync="statusModalActive">
      <div class="p-4" v-if="selectedEmployee">
        <p class="mb-4 text-sm text-gray-700">
          Nhân sự: <strong class="text-primary">{{ selectedEmployee.full_name || (selectedEmployee.user ? selectedEmployee.user.name : '') }}</strong> (Mã NS: {{ selectedEmployee.employee_code }})
        </p>
        
        <div class="vx-row mb-4">
          <div class="vx-col w-full">
            <label class="text-sm font-semibold text-gray-700 block mb-1">Trạng thái hoạt động (*)</label>
            <select v-model="statusForm.work_status" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;">
              <option value="working">Đang làm việc</option>
              <option value="resigned">Đã nghỉ việc</option>
            </select>
          </div>
        </div>

        <div v-if="statusForm.work_status === 'resigned'">
          <div class="vx-row mb-4">
            <div class="vx-col w-full">
              <label class="text-sm font-semibold text-gray-700 block mb-1">Ngày nghỉ việc</label>
              <flat-pickr
                v-model="statusForm.resigned_date"
                :config="flatpickrDateConfig"
                class="vs-inputx vs-input--input normal w-full"
                style="padding: 10px 14px; font-size: 13px;"
                placeholder="dd/mm/yyyy"
              />
            </div>
          </div>
          <div class="vx-row mb-4">
            <div class="vx-col w-full">
              <label class="text-sm font-semibold text-gray-700 block mb-1">Lý do nghỉ việc (*)</label>
              <textarea placeholder="Nhập lý do nghỉ việc..." rows="3" class="vs-inputx vs-input--input normal w-full" style="padding: 10px 14px; font-size: 13px;" v-model="statusForm.resigned_reason"></textarea>
            </div>
          </div>
        </div>

        <div class="vx-row mt-6">
          <div class="vx-col w-full flex justify-end">
            <vs-button @click="saveWorkStatus()" color="success" type="filled" class="p-3 mr-2"><i class="fa-solid fa-floppy-disk mr-1"></i> Cập Nhật</vs-button>
            <vs-button @click="statusModalActive = false" color="dark" type="border" class="p-3"><i class="fa-solid fa-xmark mr-1"></i> Hủy</vs-button>
          </div>
        </div>
      </div>
    </vs-popup>

    <!-- Modal Liên kết tài khoản User (Quan hệ 1 - 1) -->
    <vs-popup title="LIÊN KẾT TÀI KHOẢN ĐĂNG NHẬP (QUAN HỆ 1 - 1)" :active.sync="linkUserModalActive">
      <div class="p-3" v-if="selectedEmployeeForLink">
        <div class="bg-gray-100 p-3 rounded mb-4 border border-gray-200">
          <div class="text-sm text-gray-800">
            Nhân sự: <strong class="text-primary font-bold">{{ selectedEmployeeForLink.full_name || (selectedEmployeeForLink.user ? selectedEmployeeForLink.user.name : '') }}</strong> 
            (Mã NS: <strong class="text-dark">{{ selectedEmployeeForLink.employee_code }}</strong>)
          </div>
          <div class="text-xs text-gray-600 mt-1 flex flex-wrap gap-4">
            <span>Email: <strong>{{ selectedEmployeeForLink.email || 'Chưa có' }}</strong></span>
            <span>SĐT: <strong>{{ selectedEmployeeForLink.phone || 'Chưa có' }}</strong></span>
          </div>
          <div class="text-xs mt-2 pt-2 border-t border-gray-300">
            Trạng thái hiện tại: 
            <span v-if="selectedEmployeeForLink.user" class="text-success font-semibold">
              <i class="fa-solid fa-circle-check mr-1"></i> Đang liên kết với <strong>{{ selectedEmployeeForLink.user.name }}</strong> (Mã ĐN: {{ selectedEmployeeForLink.user.hrm_id }})
            </span>
            <span v-else class="text-warning font-semibold">
              <i class="fa-solid fa-circle-exclamation mr-1"></i> Chưa liên kết với tài khoản User nào
            </span>
          </div>
        </div>

        <div class="mb-4">
          <label class="text-sm font-semibold text-gray-700 block mb-1">
            Chọn tài khoản User hệ thống (*)
          </label>
          <p class="text-xs text-gray-500 mb-2">
            <i class="fa-solid fa-circle-info text-primary mr-1"></i>
            Chỉ hiển thị các tài khoản chưa liên kết với nhân sự nào khác (1 nhân sự = 1 tài khoản).
          </p>
          <div v-if="loadingAvailableUsers" class="p-3 text-center text-gray-500 text-sm">
            <i class="fa-solid fa-spinner fa-spin mr-1"></i> Đang tải danh sách tài khoản khả dụng...
          </div>
          <v-select 
            v-else
            class="w-full"
            v-model="selectedUserId" 
            :options="availableUsers" 
            :reduce="item => item.id" 
            label="displayName" 
            placeholder="Tìm theo tên, mã ĐN (hrm_id), email hoặc SĐT..."
          >
            <template slot="no-options">
              Không tìm thấy tài khoản khả dụng nào phù hợp
            </template>
          </v-select>
        </div>

        <div class="flex justify-end space-x-2 mt-5">
          <vs-button 
            @click="saveLinkUser()" 
            color="success" 
            type="filled" 
            class="p-3 mr-2"
            :disabled="!selectedUserId"
          >
            <i class="fa-solid fa-floppy-disk mr-1"></i> Xác Nhận Liên Kết
          </vs-button>
          <vs-button 
            @click="linkUserModalActive = false" 
            color="dark" 
            type="border" 
            class="p-3"
          >
            Hủy
          </vs-button>
        </div>
      </div>
    </vs-popup>

    <!-- Modal Xem Chi Tiết Nhân Sự (Dành cho Non-Admin chỉ xem) -->
    <vs-popup title="HỒ SƠ CHI TIẾT NHÂN SỰ" :active.sync="viewModalActive" class="modal-employee-view-detail">
      <div v-if="viewingEmployee" class="p-1">
        
        <!-- Header Banner: Avatar + Tên + Badge trạng thái -->
        <div class="p-4 rounded-xl border border-blue-100 flex flex-wrap items-center justify-between mb-4" style="background: linear-gradient(135deg, #f0f7ff 0%, #e0effe 100%);">
          <div class="flex items-center">
            <div class="h-16 w-16 rounded-2xl flex items-center justify-center font-bold text-2xl text-white shadow-md mr-4 shrink-0" style="background: linear-gradient(135deg, #014CCC 0%, #2563eb 100%);">
              {{ (viewingEmployee.full_name || 'NV').trim().charAt(0).toUpperCase() }}
            </div>
            <div>
              <div class="flex items-center flex-wrap gap-2">
                <h3 class="font-extrabold text-gray-900 text-xl tracking-tight mb-0">
                  {{ viewingEmployee.full_name || (viewingEmployee.user ? viewingEmployee.user.name : '---') }}
                </h3>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-primary text-white shadow-xs">
                  #{{ viewingEmployee.employee_code || '---' }}
                </span>
              </div>
              <div class="text-xs text-gray-600 mt-1.5 flex flex-wrap items-center gap-3">
                <span class="flex items-center font-medium">
                  <i class="fa-solid fa-sitemap mr-1.5 text-primary"></i> {{ viewingEmployee.department ? viewingEmployee.department.name : 'Chưa phân bổ phòng ban' }}
                </span>
                <span class="text-gray-300">•</span>
                <span class="flex items-center font-medium">
                  <i class="fa-solid fa-briefcase mr-1.5 text-primary"></i> {{ (viewingEmployee.job_title ? viewingEmployee.job_title.name : 'Nhân viên') }}
                </span>
              </div>
            </div>
          </div>
          <div class="flex items-center gap-2 mt-3 sm:mt-0">
            <vs-chip :color="getEmployeeTypeColor(viewingEmployee.employee_type)">
              {{ getEmployeeTypeText(viewingEmployee.employee_type) }}
            </vs-chip>
            <vs-chip :color="viewingEmployee.work_status === 'working' ? 'success' : 'danger'">
              <i class="fa-solid fa-circle text-xs mr-1"></i>
              {{ viewingEmployee.work_status === 'working' ? 'Đang làm việc' : 'Đã nghỉ việc' }}
            </vs-chip>
          </div>
        </div>

        <!-- Khối 1: Thông tin công việc & Vị trí -->
        <div class="mb-4">
          <h5 class="font-bold text-gray-800 text-xs uppercase tracking-wide mb-2 flex items-center">
            <i class="fa-solid fa-briefcase text-primary mr-1.5"></i> Vị Trí & Công Việc
          </h5>
          <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 p-3.5 rounded-xl bg-gray-50 border border-gray-200">
            <div>
              <span class="text-xs text-gray-400 font-semibold block uppercase mb-1">Phòng ban / Team</span>
              <span class="text-sm font-bold text-gray-800">{{ viewingEmployee.department ? viewingEmployee.department.name : '---' }}</span>
            </div>
            <div>
              <span class="text-xs text-gray-400 font-semibold block uppercase mb-1">Chức danh</span>
              <span class="text-sm font-bold text-gray-800">{{ viewingEmployee.job_title ? viewingEmployee.job_title.name : '---' }}</span>
            </div>
            <div>
              <span class="text-xs text-gray-400 font-semibold block uppercase mb-1">Cấp bậc</span>
              <span class="text-sm font-bold text-gray-800">{{ viewingEmployee.job_level ? viewingEmployee.job_level.name : '---' }}</span>
            </div>
            <div>
              <span class="text-xs text-gray-400 font-semibold block uppercase mb-1">Ca làm việc</span>
              <span class="text-sm font-bold text-primary">{{ viewingEmployee.shift ? viewingEmployee.shift.name : 'Chưa xếp ca' }}</span>
            </div>
            <div>
              <span class="text-xs text-gray-400 font-semibold block uppercase mb-1">Ngày vào làm</span>
              <span class="text-sm font-bold text-gray-800">{{ formatDateDisplay(viewingEmployee.start_date) }}</span>
            </div>
            <div>
              <span class="text-xs text-gray-400 font-semibold block uppercase mb-1">Thời gian thử việc</span>
              <span v-if="viewingEmployee.employee_type === 'probation'" class="text-sm font-bold text-warning">
                {{ formatDateDisplay(viewingEmployee.probation_start_date) }} - {{ formatDateDisplay(viewingEmployee.probation_end_date) }}
              </span>
              <span v-else class="text-sm text-gray-500 font-medium">Không áp dụng</span>
            </div>
          </div>
        </div>

        <!-- Khối 2: Thông tin cá nhân & Liên hệ -->
        <div class="mb-4">
          <h5 class="font-bold text-gray-800 text-xs uppercase tracking-wide mb-2 flex items-center">
            <i class="fa-solid fa-address-card text-success mr-1.5"></i> Thông Tin Cá Nhân & Liên Hệ
          </h5>
          <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 p-3.5 rounded-xl bg-gray-50 border border-gray-200">
            <div>
              <span class="text-xs text-gray-400 font-semibold block uppercase mb-1">Số điện thoại</span>
              <a v-if="viewingEmployee.phone" :href="'tel:' + viewingEmployee.phone" class="text-sm font-bold text-primary hover:underline flex items-center">
                <i class="fa-solid fa-phone text-xs mr-1 text-success"></i>{{ viewingEmployee.phone }}
              </a>
              <span v-else class="text-sm text-gray-400">---</span>
            </div>
            <div>
              <span class="text-xs text-gray-400 font-semibold block uppercase mb-1">Email liên hệ</span>
              <a v-if="viewingEmployee.email || (viewingEmployee.user && viewingEmployee.user.email)" :href="'mailto:' + (viewingEmployee.email || viewingEmployee.user.email)" class="text-sm font-bold text-primary hover:underline flex items-center truncate" :title="viewingEmployee.email || viewingEmployee.user.email">
                <i class="fa-solid fa-envelope text-xs mr-1 text-danger"></i>{{ viewingEmployee.email || viewingEmployee.user.email }}
              </a>
              <span v-else class="text-sm text-gray-400">---</span>
            </div>
            <div>
              <span class="text-xs text-gray-400 font-semibold block uppercase mb-1">Ngày sinh & Giới tính</span>
              <span class="text-sm font-bold text-gray-800">
                {{ formatDateDisplay(viewingEmployee.dob) }} <span class="font-normal text-gray-600">({{ getGenderText(viewingEmployee.gender) }})</span>
              </span>
            </div>
            <div>
              <span class="text-xs text-gray-400 font-semibold block uppercase mb-1">Số CCCD / CMND</span>
              <span class="text-sm font-bold text-gray-800">{{ viewingEmployee.id_card_number || '---' }}</span>
            </div>
            <div class="col-span-2">
              <span class="text-xs text-gray-400 font-semibold block uppercase mb-1">Địa chỉ thường trú / Nơi ở</span>
              <span class="text-sm font-bold text-gray-800 flex items-center" :title="viewingEmployee.address">
                <i class="fa-solid fa-location-dot text-xs mr-1.5 text-danger shrink-0"></i>
                <span>{{ viewingEmployee.address || 'Chưa cập nhật địa chỉ' }}</span>
              </span>
            </div>
          </div>
        </div>

        <!-- Khối 3: 2 Cột cân đối: Tài khoản hệ thống & Người liên hệ khẩn cấp -->
        <div class="vx-row">
          <div class="vx-col w-full sm:w-1/2 mb-3">
            <div class="p-3.5 rounded-xl border border-gray-200 bg-gray-50 h-full">
              <h5 class="font-bold text-gray-800 text-xs uppercase tracking-wide mb-2.5 flex items-center pb-2 border-b border-gray-200">
                <i class="fa-solid fa-key text-warning mr-1.5"></i> Tài Khoản Hệ Thống
              </h5>
              <div v-if="viewingEmployee.user" class="space-y-2 text-xs">
                <div class="flex justify-between items-center py-0.5">
                  <span class="text-gray-500 font-medium">Mã đăng nhập (HRM ID):</span>
                  <span class="font-bold text-success text-sm">{{ viewingEmployee.user.hrm_id || '---' }}</span>
                </div>
                <div class="flex justify-between items-center py-0.5">
                  <span class="text-gray-500 font-medium">Tên người dùng:</span>
                  <span class="font-semibold text-gray-800">{{ viewingEmployee.user.name }}</span>
                </div>
                <div class="flex justify-between items-center py-0.5">
                  <span class="text-gray-500 font-medium">Email đăng nhập:</span>
                  <span class="font-medium text-gray-700">{{ viewingEmployee.user.email }}</span>
                </div>
              </div>
              <div v-else class="text-xs text-gray-400 italic py-2 text-center">
                <i class="fa-regular fa-circle-question mr-1"></i> Chưa liên kết với tài khoản hệ thống
              </div>
            </div>
          </div>

          <div class="vx-col w-full sm:w-1/2 mb-3">
            <div class="p-3.5 rounded-xl border border-gray-200 bg-gray-50 h-full">
              <h5 class="font-bold text-gray-800 text-xs uppercase tracking-wide mb-2.5 flex items-center pb-2 border-b border-gray-200">
                <i class="fa-solid fa-shield-heart text-danger mr-1.5"></i> Liên Hệ Khẩn Cấp
              </h5>
              <div v-if="viewingEmployee.emergency_contact_name || viewingEmployee.emergency_contact_phone" class="space-y-2 text-xs">
                <div class="flex justify-between items-center py-0.5">
                  <span class="text-gray-500 font-medium">Người liên hệ:</span>
                  <span class="font-bold text-gray-800">{{ viewingEmployee.emergency_contact_name || '---' }}</span>
                </div>
                <div class="flex justify-between items-center py-0.5">
                  <span class="text-gray-500 font-medium">Số điện thoại:</span>
                  <a v-if="viewingEmployee.emergency_contact_phone" :href="'tel:' + viewingEmployee.emergency_contact_phone" class="font-bold text-danger hover:underline">
                    {{ viewingEmployee.emergency_contact_phone }}
                  </a>
                  <span v-else>---</span>
                </div>
                <div class="flex justify-between items-center py-0.5">
                  <span class="text-gray-500 font-medium">Mối quan hệ:</span>
                  <span class="font-medium text-gray-700">{{ viewingEmployee.emergency_contact_relation || '---' }}</span>
                </div>
              </div>
              <div v-else class="text-xs text-gray-400 italic py-2 text-center">
                <i class="fa-solid fa-info-circle mr-1"></i> Chưa cập nhật thông tin liên hệ khẩn cấp
              </div>
            </div>
          </div>
        </div>

        <!-- Footer Modal -->
        <div class="flex justify-end mt-3 pt-3 border-t border-gray-200">
          <vs-button @click="viewModalActive = false" color="primary" type="filled" class="p-2.5 px-6 font-bold shadow-sm">
            <i class="fa-solid fa-check mr-1.5"></i> Đóng
          </vs-button>
        </div>
      </div>
    </vs-popup>
  </div>
</template>

<script>
import axios from '../../../http/axios.js';
import vSelect from 'vue-select';
import moment from 'moment';

export default {
  components: {
    'v-select': vSelect
  },
  data() {
    return {
      flatpickrDateConfig: {
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd/m/Y',
        allowInput: true
      },
      employees: [],
      filterType: 'all',
      filterWorkStatus: 'all',
      searchKeyword: '',
      statusModalActive: false,
      selectedEmployee: null,
      canDeleteServer: null,
      isHrmAdminServer: null,
      statusForm: {
        work_status: 'working',
        resigned_date: '',
        resigned_reason: ''
      },
      currentPage: 1,
      perPage: 20,
      linkUserModalActive: false,
      selectedEmployeeForLink: null,
      selectedUserId: null,
      availableUsers: [],
      loadingAvailableUsers: false,
      viewModalActive: false,
      viewingEmployee: null
    };
  },
  watch: {
    filterType() {
      this.currentPage = 1;
    },
    filterWorkStatus() {
      this.currentPage = 1;
    },
    searchKeyword() {
      this.currentPage = 1;
    }
  },
  computed: {
    countFulltime() {
      return this.employees.filter(e => e.employee_type === 'fulltime').length;
    },
    countCtv() {
      return this.employees.filter(e => e.employee_type === 'ctv').length;
    },
    countProbation() {
      return this.employees.filter(e => e.employee_type === 'probation').length;
    },
    filteredEmployees() {
      let list = this.employees;
      if (this.filterType !== 'all') {
        list = list.filter(emp => emp.employee_type === this.filterType);
      }
      return list;
    },
    paginatedEmployees() {
      const start = (this.currentPage - 1) * this.perPage;
      return this.filteredEmployees.slice(start, start + this.perPage);
    },
    totalPages() {
      return Math.ceil(this.filteredEmployees.length / this.perPage) || 1;
    },
    canDeleteEmployee() {
      if (!this.isHrmAdmin) return false;
      if (this.canDeleteServer !== null) {
        return this.canDeleteServer;
      }
      return this.checkCanDeleteLocal();
    },
    isHrmAdmin() {
      if (this.isHrmAdminServer !== null) {
        return this.isHrmAdminServer;
      }
      const user = this.$store.state.AppActiveUser || JSON.parse(localStorage.getItem('userInfo')) || {};
      if (user.uid === 1 || user.id === 1 || user.is_hrm_admin === true) return true;
      const adminRoleNames = ['admin', 'ceo', 'account', 'ops', 'hrm_manager'];
      const roleName = (user.roleName || user.role_name || '').toLowerCase();
      if (adminRoleNames.includes(roleName)) return true;
      return false;
    }
  },
  created() {
    this.fetchEmployees();
  },
  methods: {
    formatDateDisplay(val) {
      if (!val || val === '0' || val === '0000-00-00' || val === '0000-00-00 00:00:00') return '---';
      const m = moment(val);
      return m.isValid() ? m.format('DD/MM/YYYY') : '---';
    },
    getGenderText(g) {
      if (!g) return '---';
      const s = String(g).toLowerCase().trim();
      if (s === 'male' || s === 'nam' || s === '1') return 'Nam';
      if (s === 'female' || s === 'nu' || s === 'nữ' || s === '0') return 'Nữ';
      return 'Khác';
    },
    openViewModal(emp) {
      this.viewingEmployee = emp;
      this.viewModalActive = true;
    },
    setFilterType(type) {
      this.filterType = type;
    },
    fetchEmployees() {
      this.$vs.loading();
      axios.p('/api/hrm/employees/list', {
        work_status: this.filterWorkStatus,
        keyword: this.searchKeyword
      }).then(res => {
        if (res.data && res.data.data) {
          this.employees = res.data.data;
          this.canDeleteServer = typeof res.data.can_delete !== 'undefined' ? !!res.data.can_delete : null;
          this.isHrmAdminServer = typeof res.data.is_hrm_admin !== 'undefined' ? !!res.data.is_hrm_admin : null;
        } else if (Array.isArray(res.data)) {
          this.employees = res.data;
        } else {
          this.employees = [];
        }
        this.$vs.loading.close();
      }).catch(err => {
        this.$vs.loading.close();
        console.error(err);
      });
    },
    checkCanDeleteLocal() {
      const user = this.$store.state.AppActiveUser || JSON.parse(localStorage.getItem('userInfo')) || {};
      if (user.uid === 1 || user.id === 1) return true;
      const roleName = (user.roleName || '').toLowerCase();
      if (
        roleName.includes('quản trị') ||
        roleName.includes('admin') ||
        roleName.includes('kế toán') ||
        roleName.includes('account') ||
        roleName.includes('giám đốc') ||
        roleName.includes('ceo')
      ) {
        return true;
      }
      return false;
    },
    confirmDeleteEmployee(emp) {
      const empName = emp.full_name || (emp.user ? emp.user.name : emp.employee_code);
      this.$vs.dialog({
        type: 'confirm',
        color: 'danger',
        title: 'Xác nhận xóa nhân sự',
        text: `Bạn có chắc chắn muốn xóa nhân sự "${empName}" (Mã NS: ${emp.employee_code || '---'}) không? Dữ liệu chấm công, xếp ca và hồ sơ liên quan sẽ được xóa sạch. Thao tác này không thể hoàn tác.`,
        acceptText: 'Đồng ý xóa',
        cancelText: 'Hủy bỏ',
        accept: () => {
          this.executeDeleteEmployee(emp.id);
        }
      });
    },
    executeDeleteEmployee(id) {
      this.$vs.loading();
      axios.p(`/api/hrm/employees/delete/${id}`).then(res => {
        this.$vs.loading.close();
        if (res.data.status === 1) {
          this.$vs.notify({ title: 'Thành công', text: res.data.message, color: 'success' });
          this.fetchEmployees();
        } else {
          this.$vs.notify({ title: 'Lỗi', text: res.data.message || 'Không thể xóa nhân sự', color: 'danger' });
        }
      }).catch(err => {
        this.$vs.loading.close();
        const msg = err.response && err.response.data && err.response.data.message 
          ? err.response.data.message 
          : 'Lỗi khi xóa nhân sự';
        this.$vs.notify({ title: 'Lỗi', text: msg, color: 'danger' });
      });
    },
    getEmployeeTypeText(type) {
      const map = {
        fulltime: 'Fulltime',
        ctv: 'Cộng tác viên (CTV)',
        probation: 'Thử việc'
      };
      return map[type] || 'Fulltime';
    },
    getEmployeeTypeColor(type) {
      const map = {
        fulltime: 'success',
        ctv: 'warning',
        probation: 'dark'
      };
      return map[type] || 'primary';
    },
    approveCtv(id) {
      this.$vs.loading();
      axios.p(`/api/hrm/employees/approve-ctv/${id}`).then(res => {
        this.$vs.loading.close();
        this.$vs.notify({ title: 'Thành công', text: res.data.message, color: 'success' });
        this.fetchEmployees();
      }).catch(err => {
        this.$vs.loading.close();
        this.$vs.notify({ title: 'Lỗi', text: 'Không thể duyệt CTV', color: 'danger' });
      });
    },
    convertToFulltime(id) {
      this.$vs.loading();
      axios.p(`/api/hrm/employees/convert-fulltime/${id}`).then(res => {
        this.$vs.loading.close();
        this.$vs.notify({ title: 'Thành công', text: res.data.message, color: 'success' });
        this.fetchEmployees();
      }).catch(err => {
        this.$vs.loading.close();
        this.$vs.notify({ title: 'Lỗi', text: 'Chuyển đổi thất bại', color: 'danger' });
      });
    },
    openWorkStatusModal(emp) {
      this.selectedEmployee = emp;
      this.statusForm = {
        work_status: emp.work_status || 'working',
        resigned_date: emp.resigned_date || new Date().toISOString().split('T')[0],
        resigned_reason: emp.resigned_reason || ''
      };
      this.statusModalActive = true;
    },
    saveWorkStatus() {
      if (this.statusForm.work_status === 'resigned' && !this.statusForm.resigned_reason) {
        this.$vs.notify({ title: 'Lỗi', text: 'Vui lòng nhập lý do nghỉ việc', color: 'danger' });
        return;
      }
      this.$vs.loading();
      axios.p(`/api/hrm/employees/update-work-status/${this.selectedEmployee.id}`, this.statusForm).then(res => {
        this.$vs.loading.close();
        this.$vs.notify({ title: 'Thành công', text: res.data.message, color: 'success' });
        this.statusModalActive = false;
        this.fetchEmployees();
      }).catch(err => {
        this.$vs.loading.close();
        this.$vs.notify({ title: 'Lỗi', text: 'Cập nhật thất bại', color: 'danger' });
      });
    },
    openLinkUserModal(emp) {
      this.selectedEmployeeForLink = emp;
      this.selectedUserId = emp.user_id || (emp.user ? emp.user.id : null);
      this.availableUsers = [];
      this.linkUserModalActive = true;
      this.loadAvailableUsers(emp.id);
    },
    loadAvailableUsers(employeeId) {
      this.loadingAvailableUsers = true;
      axios.p('/api/hrm/employees/available-users', { employee_id: employeeId })
        .then(res => {
          this.loadingAvailableUsers = false;
          if (res.data && res.data.data) {
            this.availableUsers = res.data.data;
            // Nếu chưa có user_id và có gợi ý trùng khớp, tự động chọn gợi ý đầu tiên
            if (!this.selectedUserId) {
              const suggested = this.availableUsers.find(u => u.is_suggested);
              if (suggested) {
                this.selectedUserId = suggested.id;
              }
            }
          }
        })
        .catch(err => {
          this.loadingAvailableUsers = false;
          console.error(err);
          this.$vs.notify({ title: 'Lỗi', text: 'Không thể tải danh sách tài khoản khả dụng', color: 'danger' });
        });
    },
    saveLinkUser() {
      if (!this.selectedUserId) {
        this.$vs.notify({ title: 'Cảnh báo', text: 'Vui lòng chọn tài khoản User cần liên kết', color: 'warning' });
        return;
      }
      this.$vs.loading();
      axios.p(`/api/hrm/employees/link-user/${this.selectedEmployeeForLink.id}`, {
        user_id: this.selectedUserId
      }).then(res => {
        this.$vs.loading.close();
        if (res.data && res.data.status === 1) {
          this.$vs.notify({ title: 'Thành công', text: res.data.message, color: 'success' });
          this.linkUserModalActive = false;
          this.fetchEmployees();
        } else {
          this.$vs.notify({ title: 'Lỗi', text: res.data.message || 'Liên kết thất bại', color: 'danger' });
        }
      }).catch(err => {
        this.$vs.loading.close();
        const msg = err.response && err.response.data && err.response.data.message 
          ? err.response.data.message 
          : 'Lỗi khi liên kết tài khoản';
        this.$vs.notify({ title: 'Lỗi', text: msg, color: 'danger' });
      });
    },
    confirmUnlinkUser(emp) {
      const empName = emp.full_name || (emp.user ? emp.user.name : emp.employee_code);
      const userName = emp.user ? `${emp.user.name} (${emp.user.hrm_id})` : 'tài khoản hiện tại';
      this.$vs.dialog({
        type: 'confirm',
        color: 'warning',
        title: 'Xác nhận hủy liên kết tài khoản',
        text: `Bạn có chắc muốn hủy liên kết tài khoản "${userName}" khỏi nhân sự "${empName}" không? Sau khi hủy, nhân sự này sẽ không thể đăng nhập bằng mã tài khoản cũ nữa cho đến khi được liên kết lại.`,
        acceptText: 'Đồng ý hủy',
        cancelText: 'Đóng',
        accept: () => {
          this.executeUnlinkUser(emp.id);
        }
      });
    },
    executeUnlinkUser(employeeId) {
      this.$vs.loading();
      axios.p(`/api/hrm/employees/unlink-user/${employeeId}`).then(res => {
        this.$vs.loading.close();
        if (res.data && res.data.status === 1) {
          this.$vs.notify({ title: 'Thành công', text: res.data.message, color: 'success' });
          this.fetchEmployees();
        } else {
          this.$vs.notify({ title: 'Lỗi', text: res.data.message || 'Hủy liên kết thất bại', color: 'danger' });
        }
      }).catch(err => {
        this.$vs.loading.close();
        const msg = err.response && err.response.data && err.response.data.message 
          ? err.response.data.message 
          : 'Lỗi khi hủy liên kết tài khoản';
        this.$vs.notify({ title: 'Lỗi', text: msg, color: 'danger' });
      });
    }
  }
};
</script>

<style>
.modal-employee-view-detail.con-vs-popup .vs-popup {
  width: 820px !important;
  max-width: 95vw !important;
  border-radius: 16px !important;
  overflow: hidden !important;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05) !important;
}
.modal-employee-view-detail .vs-popup--header {
  background: #ffffff !important;
  border-bottom: 1px solid #f1f5f9 !important;
  padding: 18px 24px !important;
}
.modal-employee-view-detail .vs-popup--title h3 {
  font-size: 16px !important;
  font-weight: 700 !important;
  color: #0f172a !important;
  text-transform: uppercase !important;
  letter-spacing: 0.5px !important;
}
.modal-employee-view-detail .vs-popup--content {
  padding: 20px 24px !important;
  background: #ffffff !important;
}
.modal-employee-view-detail .vs-popup--close {
  top: 16px !important;
  right: 16px !important;
}
</style>
