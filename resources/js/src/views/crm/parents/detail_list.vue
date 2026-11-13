<template>
  <div id="page-customer-detail-list">
    <!-- KHUNG BỘ LỌC TÌM KIẾM HIỆN ĐẠI & TỐI ƯU -->
    <vx-card no-shadow class="mt-5 filter-box-card">
      <div class="filter-header-bar flex items-center justify-between pb-3 mb-4 border-b border-gray-100">
        <div class="flex items-center">
          <div class="filter-icon-badge mr-2">
            <i class="feather icon-filter"></i>
          </div>
          <div>
            <h5 class="font-bold text-gray-800 text-base mb-0">Bộ lọc danh sách chi tiết khách hàng</h5>
            <p class="text-xs text-gray-400 mb-0">Tìm kiếm nhanh theo từ khóa, trạng thái, người phụ trách và sản phẩm</p>
          </div>
        </div>
        <div class="flex items-center">
          <span class="active-filter-badge mr-3" v-if="activeFilterCount > 0">
            <i class="feather icon-check-circle mr-1"></i> Đang lọc {{ activeFilterCount }} tiêu chí
          </span>
          <button 
            type="button" 
            class="btn-toggle-filter" 
            @click="isFilterCollapsed = !isFilterCollapsed"
            :title="isFilterCollapsed ? 'Mở rộng bộ lọc' : 'Thu gọn bộ lọc'"
          >
            <i :class="isFilterCollapsed ? 'feather icon-chevron-down' : 'feather icon-chevron-up'"></i>
            {{ isFilterCollapsed ? 'Mở rộng' : 'Thu gọn' }}
          </button>
        </div>
      </div>

      <!-- NỘI DUNG CÁC TRƯỜNG LỌC -->
      <div v-show="!isFilterCollapsed" class="filter-body-content">
        <div class="vx-row">
          <!-- 1. Từ khóa -->
          <div class="vx-col sm:w-1/2 md:w-1/3 lg:w-1/4 w-full mb-4">
            <label class="custom-filter-label">
              <i class="feather icon-search text-primary mr-1"></i> Từ khóa
            </label>
            <vs-input 
              class="w-full custom-input" 
              placeholder="Tên khách hàng, số điện thoại..." 
              v-model="searchData.keyword"
              @keyup.enter="getData"
            ></vs-input>
          </div>

          <!-- 2. Lịch chăm sóc tiếp theo -->
          <div class="vx-col sm:w-1/2 md:w-1/3 lg:w-1/4 w-full mb-4">
            <label class="custom-filter-label">
              <i class="feather icon-calendar text-primary mr-1"></i> Lịch chăm sóc tiếp theo
            </label>
            <date-picker 
              name="item-date" 
              v-model="searchData.dateRange" 
              range 
              format="YYYY-MM-DD" 
              style="width: 100%;"
              :clearable="true" 
              :lang="datepickerOptions.lang" 
              placeholder="Chọn khoảng ngày tìm kiếm"
            ></date-picker>
          </div>

          <!-- 3. Trạng thái khách hàng -->
          <div class="vx-col sm:w-1/2 md:w-1/3 lg:w-1/4 w-full mb-4">
            <label class="custom-filter-label">
              <i class="feather icon-tag text-primary mr-1"></i> Trạng thái khách hàng
            </label>
            <multiselect
              name="search_status"
              placeholder="Tất cả trạng thái"
              v-model="searchData.arr_status"
              :options="statusOptions"
              label="label"
              :close-on-select="false"
              :hide-selected="true"
              :multiple="true"
              :searchable="true"
              track-by="id"
              selectedLabel="" selectLabel="" deselectLabel=""
            >
              <span slot="noResult">Không tìm thấy dữ liệu</span>
            </multiselect>
          </div>

          <!-- 4. Người phụ trách -->
          <div class="vx-col sm:w-1/2 md:w-1/3 lg:w-1/4 w-full mb-4">
            <label class="custom-filter-label">
              <i class="feather icon-user text-primary mr-1"></i> Người phụ trách
            </label>
            <multiselect
              name="search_owner"
              placeholder="Chọn người phụ trách"
              v-model="searchData.arr_owner"
              :options="users_manager_list"
              label="label_name"
              :close-on-select="false"
              :hide-selected="true"
              :multiple="true"
              :searchable="true"
              track-by="id"
              selectedLabel="" selectLabel="" deselectLabel=""
            >
              <span slot="noResult">Không tìm thấy dữ liệu</span>
            </multiselect>
          </div>

          <!-- 5. Nguồn -->
          <div class="vx-col sm:w-1/2 md:w-1/3 lg:w-1/4 w-full mb-4">
            <label class="custom-filter-label">
              <i class="feather icon-share-2 text-primary mr-1"></i> Nguồn khách hàng
            </label>
            <multiselect
              name="search_source"
              placeholder="Chọn nguồn"
              select-label="Chọn nguồn"
              v-model="searchData.arr_source"
              :options="source_list"
              label="name"
              :close-on-select="false"
              :hide-selected="true"
              :multiple="true"
              :searchable="true"
              track-by="id"
              selectedLabel="" selectLabel="" deselectLabel=""
            >
              <span slot="noResult">Không tìm thấy dữ liệu</span>
            </multiselect>
          </div>

          <!-- 6. Sản phẩm -->
          <div class="vx-col sm:w-1/2 md:w-1/3 lg:w-1/4 w-full mb-4">
            <label class="custom-filter-label">
              <i class="feather icon-package text-primary mr-1"></i> Sản phẩm
            </label>
            <multiselect
              name="search_product"
              placeholder="Chọn sản phẩm"
              v-model="searchData.arr_product"
              :options="product_list"
              label="name"
              :close-on-select="false"
              :hide-selected="true"
              :multiple="true"
              :searchable="true"
              track-by="id"
              selectedLabel="" selectLabel="" deselectLabel=""
            >
              <span slot="noResult">Không tìm thấy dữ liệu</span>
            </multiselect>
          </div>
        </div>

        <!-- ACTION TOOLBAR VÀ NÚT CHUYỂN CHẾ ĐỘ XEM -->
        <div class="vx-row mt-1 pt-3 border-t border-gray-100 items-center justify-between">
          <div class="vx-col sm:w-auto w-full mb-2 flex flex-wrap items-center">
            <router-link class="btn btn-success" :to="'/crm/parent/add'">
              <vs-button class="mr-3 mb-2" color="success">
                <i class="fa fa-plus mr-1"></i> Thêm mới
              </vs-button>
            </router-link>  
            <vs-button class="mr-3 mb-2 shadow-btn" color="primary" @click="getData">
              <i class="fa fa-search mr-1"></i> Tìm kiếm
            </vs-button>
            <vs-button color="dark" type="border" class="mb-2 mr-3" @click="reset">
              <i class="fas fa-undo-alt mr-1"></i> Đặt lại
            </vs-button>
          </div>

          <!-- BỘ CHUYỂN CHẾ ĐỘ XEM (TỔNG QUAN / CHI TIẾT) -->
          <div class="vx-col sm:w-auto w-full mb-2 flex items-center justify-end">
            <div class="view-mode-pill-container">
              <span class="mode-label mr-2"><i class="feather icon-eye text-primary mr-1"></i>Chế độ xem:</span>
              <div class="pill-group">
                <button 
                  type="button"
                  class="pill-btn" 
                  @click="goToOverview"
                  title="Chuyển sang chế độ xem tổng quan"
                >
                  <i class="feather icon-list mr-1"></i> Tổng quan
                </button>
                <button 
                  type="button"
                  class="pill-btn active" 
                  title="Đang ở chế độ xem chi tiết"
                >
                  <i class="feather icon-grid mr-1"></i> Chi tiết
                  <span class="active-badge-dot"></span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- THANH CHỌN & BÀN GIAO -->
      <div class="action-bar-selection py-2 px-3 mb-3 bg-gray-50 border rounded flex items-center justify-between">
        <div class="text-sm font-medium">
          <i class="feather icon-check-square text-primary mr-1"></i>
          Đã chọn: <b class="text-primary">{{ checked_list.length }}</b> / {{ parents.length }} khách hàng
        </div>
        <div>
          <vs-button 
            :disabled="checked_list.length === 0" 
            @click="showModalAssign" 
            color="danger" 
            size="small"
          >
            <i class="fa-solid fa-paper-plane mr-1"></i> Bàn giao khách hàng
          </vs-button>
        </div>
      </div>

      <!-- TABS PHÂN LOẠI -->
      <vs-tabs v-model="activeItem">
        <vs-tab :label="'Tất cả ('+ total.total_0 +')'" @click.prevent="setActive(0)"></vs-tab>
        <vs-tab :label="'Chưa chăm sóc ('+ total.total_1 +')'" @click.prevent="setActive(1)"></vs-tab>
        <vs-tab :label="'Lịch chăm sóc trong ngày ('+ total.total_2 +')'" @click.prevent="setActive(2)"></vs-tab>
        <vs-tab :label="'KH quá hạn xử lý ('+ total.total_3+')'" @click.prevent="setActive(3)"></vs-tab>
        <vs-tab :label="'Ticket chưa xử lý ('+ total.total_4 +')'" @click.prevent="setActive(4)"></vs-tab>
      </vs-tabs>

      <!-- BẢNG CHI TIẾT 16 CỘT -->
      <div class="detail-table-wrapper mt-3">
        <div class="detail-table-scroll-container">
          <table class="detail-custom-table" style="min-width: 2050px;">
            <thead>
              <tr>
                <!-- Checkbox -->
                <th class="th-center" style="width: 48px;">
                  <div class="vs-component con-vs-checkbox vs-checkbox-primary vs-checkbox-default">
                    <input type="checkbox" v-model="selectAll" class="vs-checkbox--input" >
                    <span class="checkbox_x vs-checkbox" style="border: 2px solid rgb(180, 180, 180);">
                      <span class="vs-checkbox--check">
                        <i class="vs-icon notranslate icon-scale vs-checkbox--icon material-icons null">check</i>
                      </span>
                    </span>
                  </div>
                </th>
                <!-- Cột 1: Công cụ -->
                <th class="th-center sticky-col" style="width: 140px;">Công cụ</th>
                <!-- Cột 2: Ngày quản lý -->
                <th class="th-center" style="width: 130px;">
                  Ngày quản lý <i class="fa fa-caret-down ml-1 text-primary"></i>
                </th>
                <!-- Cột 3: Điện thoại -->
                <th class="th-center" style="width: 130px;">Điện thoại</th>
                <!-- Cột 4: Họ và tên -->
                <th style="width: 200px;">Họ và tên</th>
                <!-- Cột 5: Lịch sử chăm sóc -->
                <th style="width: 480px;">Lịch sử chăm sóc</th>
                <!-- Cột 6: Ghi chú -->
                <th style="width: 320px;">Ghi chú</th>
                <!-- Cột 7: Người quản lý -->
                <th style="width: 160px;">Người quản lý</th>
                <!-- Cột 8: Quản lý đầu tiên -->
                <th style="width: 160px;">Quản lý đầu tiên</th>
                <!-- Cột 9: Người tạo -->
                <th style="width: 160px;">Người tạo</th>
                <!-- Cột 10: Hợp đồng -->
                <th class="th-center" style="width: 90px;">Hợp đồng</th>
                <!-- Cột 11: Tỉnh thành -->
                <th style="width: 130px;">Tỉnh thành</th>
                <!-- Cột 12: Địa chỉ -->
                <th style="width: 250px;">Địa chỉ</th>
                <!-- Cột 13: Phân loại -->
                <th style="width: 180px;">Phân loại</th>
                <!-- Cột 14: Nguồn data -->
                <th style="width: 140px;">Nguồn data</th>
                <!-- Cột 15: Kênh nguồn -->
                <th style="width: 150px;">Kênh nguồn</th>
                <!-- Cột 16: Link đăng ký -->
                <th style="width: 140px;">Link đăng ký</th>
              </tr>
            </thead>
            <tbody>
              <tr 
                v-for="(item, index) in parents" 
                :key="item.id || index"
                :class="{ 'row-selected': checked_list.includes(item.id) }"
              >
                <!-- Checkbox -->
                <td class="td-center">
                  <div class="vs-component con-vs-checkbox vs-checkbox-primary vs-checkbox-default">
                    <input type="checkbox" v-model="checked_list" :value="item.id" class="vs-checkbox--input" >
                    <span class="checkbox_x vs-checkbox" style="border: 2px solid rgb(180, 180, 180);">
                      <span class="vs-checkbox--check">
                        <i class="vs-icon notranslate icon-scale vs-checkbox--icon material-icons null">check</i>
                      </span>
                    </span>
                  </div>
                </td>

                <!-- Cột 1: CÔNG CỤ -->
                <td class="td-center sticky-col">
                  <div class="action-btn-group">
                    <!-- Nút tím: Thêm chăm sóc -->
                    <button 
                      class="btn-tool btn-tool-purple" 
                      @click="openAddCare(item)" 
                      title="Thêm chăm sóc mới"
                    >
                      <i class="fa fa-phone-volume"></i>
                    </button>

                    <!-- Nút cam: Thêm học sinh -->
                    <button 
                      class="btn-tool btn-tool-orange" 
                      @click="openAddStudent(item)" 
                      title="Thêm học sinh"
                    >
                      <i class="fa fa-user-plus"></i>
                    </button>

                    <!-- Menu ...: Cập nhật thông tin, Chuyển trạng thái, v.v. -->
                    <vs-dropdown vs-trigger-click class="cursor-pointer">
                      <button class="btn-tool btn-tool-navy" title="Tác vụ khác">
                        <i class="fa fa-ellipsis-h"></i>
                      </button>
                      <vs-dropdown-menu class="custom-action-dropdown">
                        <vs-dropdown-item @click="openQuickEdit(item)">
                          <span class="dropdown-item-content text-primary">
                            <i class="feather icon-edit mr-2"></i> Cập nhật thông tin
                          </span>
                        </vs-dropdown-item>
                        <vs-dropdown-item @click="openChangeStatus(item)">
                          <span class="dropdown-item-content text-warning">
                            <i class="feather icon-refresh-cw mr-2"></i> Chuyển trạng thái
                          </span>
                        </vs-dropdown-item>
                        <vs-dropdown-item @click="openAssignSingle(item)">
                          <span class="dropdown-item-content text-info">
                            <i class="feather icon-user-check mr-2"></i> Bàn giao nhân viên
                          </span>
                        </vs-dropdown-item>
                        <vs-dropdown-item divider>
                          <router-link :to="`/crm/parent/${item.id}/detail`" class="dropdown-item-content text-dark">
                            <i class="feather icon-external-link mr-2"></i> Xem chi tiết hồ sơ
                          </router-link>
                        </vs-dropdown-item>
                      </vs-dropdown-menu>
                    </vs-dropdown>
                  </div>
                </td>

                <!-- Cột 2: Ngày quản lý -->
                <td class="td-center">
                  <div class="font-medium text-dark">{{ formatDateOnly(item.last_assign_date || item.created_at) }}</div>
                  <div class="text-xs text-muted">{{ formatTimeOnly(item.last_assign_date || item.created_at) }}</div>
                </td>

                <!-- Cột 3: Điện thoại -->
                <td class="td-center">
                  <a 
                    href="javascript:void(0)" 
                    class="font-semibold text-blue-600 hover:underline"
                    @click="openAddCare(item)"
                    title="Bấm để gọi/thêm chăm sóc"
                  >
                    {{ item.mobile_1 }}
                  </a>
                  <div v-if="item.mobile_2" class="text-xs text-gray-500 mt-0.5">
                    {{ item.mobile_2 }}
                  </div>
                </td>

                <!-- Cột 4: Họ và tên -->
                <td>
                  <router-link :to="`/crm/parent/${item.id}/detail`" class="font-semibold text-blue-600 hover:underline">
                    {{ item.name }}
                  </router-link>
                  <div v-if="item.students && item.students.length" class="text-xs text-gray-500 mt-1">
                    <span class="inline-block bg-blue-50 text-blue-700 px-1.5 py-0.5 rounded text-2xs">
                      HS: {{ item.students.map(s => s.name).join(', ') }}
                    </span>
                  </div>
                </td>

                <!-- Cột 5: Lịch sử chăm sóc -->
                <td>
                  <div class="care-history-box" v-if="item.care_history && item.care_history.length">
                    <div 
                      class="care-log-row" 
                      v-for="(care, cIdx) in item.care_history" 
                      :key="cIdx"
                    >
                      <span class="care-date font-semibold text-gray-800">
                        {{ formatCareDateTime(care.care_date) }}
                      </span>
                      <span class="care-method-badge" v-if="care.method_name">
                        [{{ care.method_name }}]
                      </span>
                      <span class="care-status-badge text-danger" v-if="care.call_status_label">
                        ({{ care.call_status_label }}):
                      </span>
                      <span class="care-text text-gray-700">
                        {{ care.note }}
                      </span>
                    </div>
                  </div>
                  <div v-else class="text-xs text-gray-400 italic">
                    {{ item.last_care || 'Chưa có lịch sử chăm sóc' }}
                  </div>
                </td>

                <!-- Cột 8: Ghi chú -->
                <td>
                  <div class="notes-bullet-box">
                    <div class="note-bullet-line" v-if="item.address">
                      <span class="bullet-dot">•</span> 
                      <strong class="text-dark">address:</strong> {{ item.address }}
                    </div>
                    <div class="note-bullet-line" v-if="item.facebook">
                      <span class="bullet-dot">•</span> 
                      <strong class="text-dark">facebook:</strong> 
                      <a :href="item.facebook" target="_blank" class="text-blue-500 underline">{{ item.facebook }}</a>
                    </div>
                    <div class="note-bullet-line">
                      <span class="bullet-dot">•</span> 
                      <strong class="text-dark">Ghi chú:</strong> {{ item.note || 'Không có ghi chú' }}
                    </div>
                  </div>
                </td>

                <!-- Cột 7: Người quản lý -->
                <td class="font-medium text-dark text-sm" style="min-width: 180px;">
                  <div v-if="item.owner_name">
                    <span class="font-semibold text-dark">{{ item.owner_name }}</span>
                    <span v-if="item.owner_team_name" class="text-primary font-medium"> / {{ item.owner_team_name }}</span>
                  </div>
                  <span v-else>-</span>
                </td>

                <!-- Cột 8: Quản lý đầu tiên -->
                <td class="text-gray-700 text-sm">
                  {{ item.first_owner_name || item.creator_name || '-' }}
                </td>

                <!-- Cột 9: Người tạo -->
                <td class="text-gray-700 text-sm">
                  {{ item.creator_name || '-' }}
                </td>

                <!-- Cột 10: Hợp đồng -->
                <td class="td-center">
                  <span 
                    class="contract-badge" 
                    :class="item.contract_count > 0 ? 'contract-badge-green' : 'contract-badge-zero'"
                  >
                    {{ item.contract_count || 0 }}
                  </span>
                </td>

                <!-- Cột 11: Tỉnh thành -->
                <td class="text-sm text-gray-700">
                  {{ item.province_name || '-' }}
                </td>

                <!-- Cột 12: Địa chỉ -->
                <td class="text-sm text-gray-700" style="max-width: 250px;">
                  {{ item.address || '-' }}
                </td>

                <!-- Cột 13: Phân loại -->
                <td>
                  <div class="classification-box">
                    <span class="status-name font-medium text-dark">
                      {{ item.status | getStatusName }}
                    </span>
                    <span 
                      v-if="item.status === 2 || item.status === 3" 
                      class="sub-badge sub-badge-danger"
                    >
                      Khó
                    </span>
                    <span 
                      v-else-if="item.status === 8 || item.status === 9" 
                      class="sub-badge sub-badge-success"
                    >
                      Hot
                    </span>
                    <span 
                      v-else-if="item.status === 1" 
                      class="sub-badge sub-badge-info"
                    >
                      Mới
                    </span>
                  </div>
                </td>

                <!-- Cột 18: Nguồn data -->
                <td class="text-sm text-gray-700">
                  <span class="badge-source">{{ item.source_name || '-' }}</span>
                </td>

                <!-- Cột 19: Kênh nguồn -->
                <td class="text-sm text-gray-700">
                  {{ item.source_detail_name || '-' }}
                </td>

                <!-- Cột 20: Link đăng ký -->
                <td class="text-sm">
                  <a 
                    v-if="item.reg_link" 
                    :href="item.reg_link" 
                    target="_blank" 
                    class="text-blue-500 underline truncate block"
                  >
                    Link
                  </a>
                  <span v-else class="text-gray-400">-</span>
                </td>
              </tr>

              <!-- Trống dữ liệu -->
              <tr v-if="!parents || parents.length === 0">
                <td colspan="21" class="text-center py-6 text-gray-500">
                  <i class="feather icon-inbox text-2xl mb-1 block"></i>
                  Không có dữ liệu khách hàng phù hợp
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- PHÂN TRANG -->
      <div class="flex flex-wrap items-center justify-between mt-5">
        <div class="flex items-center">
          <vs-dropdown vs-trigger-click class="cursor-pointer mr-4 items-per-page-handler">
            <div class="p-3 border border-solid d-theme-border-grey-light rounded-full d-theme-dark-bg cursor-pointer flex items-center justify-between font-medium text-sm">
              <span class="mr-2">
                {{ pagination.cpage * pagination.limit - (pagination.limit - 1) }} - 
                {{ pagination.total - pagination.cpage * pagination.limit > 0 ? pagination.cpage * pagination.limit : pagination.total }} 
                của {{ pagination.total }}
              </span>
              <feather-icon icon="ChevronDownIcon" svgClasses="h-4 w-4" />
            </div>
            <vs-dropdown-menu>
              <vs-dropdown-item v-for="(limitVal, lIdx) in limitSource" :key="lIdx" @click="changePageLimit(limitVal)">
                <span>{{ limitVal }} dòng / trang</span>
              </vs-dropdown-item>
            </vs-dropdown-menu>
          </vs-dropdown>
          <span class="text-sm text-gray-500">Hiển thị dữ liệu trang {{ pagination.cpage }}</span>
        </div>

        <vs-pagination
          v-if="Math.ceil(pagination.total / pagination.limit) > 1"
          :total="Math.ceil(pagination.total / pagination.limit)"
          :max="7"
          v-model="pagination.cpage"
          @change="changePage"
        />
      </div>
    </vx-card>

    <!-- ========================================================================= -->
    <!-- MODAL 1: CẬP NHẬT THÔNG TIN KHÁCH HÀNG (QUICK EDIT)                     -->
    <!-- ========================================================================= -->
    <vs-popup 
      class="popup-parent-custom modal_primary" 
      title="CẬP NHẬT THÔNG TIN KHÁCH HÀNG" 
      :active.sync="modal_edit.show"
    >
      <div class="vx-row">
        <!-- Tên khách hàng -->
        <div class="vx-col md:w-1/2 w-full mb-3">
          <label class="font-medium text-sm">Họ và tên <span class="text-danger">*</span></label>
          <input class="vs-inputx vs-input--input normal" type="text" v-model="edit_parent.name" placeholder="Nhập họ tên khách hàng">
        </div>

        <!-- Giới tính -->
        <div class="vx-col md:w-1/4 w-full mb-3">
          <label class="font-medium text-sm">Danh xưng</label>
          <select class="vs-inputx vs-input--input normal" v-model="edit_parent.gender">
            <option value="M">Ông</option>
            <option value="F">Bà</option>
          </select>
        </div>

        <!-- Ngày sinh -->
        <div class="vx-col md:w-1/4 w-full mb-3">
          <label class="font-medium text-sm">Ngày sinh</label>
          <input class="vs-inputx vs-input--input normal" type="date" v-model="edit_parent.birthday">
        </div>

        <!-- Số điện thoại 1 -->
        <div class="vx-col md:w-1/2 w-full mb-3">
          <label class="font-medium text-sm">Số điện thoại chính <span class="text-danger">*</span></label>
          <input class="vs-inputx vs-input--input normal" type="text" v-model="edit_parent.mobile_1" placeholder="Số điện thoại chính">
        </div>

        <!-- Số điện thoại 2 -->
        <div class="vx-col md:w-1/2 w-full mb-3">
          <label class="font-medium text-sm">Số điện thoại phụ</label>
          <input class="vs-inputx vs-input--input normal" type="text" v-model="edit_parent.mobile_2" placeholder="Số điện thoại phụ">
        </div>

        <!-- Email -->
        <div class="vx-col md:w-1/2 w-full mb-3">
          <label class="font-medium text-sm">Email</label>
          <input class="vs-inputx vs-input--input normal" type="email" v-model="edit_parent.email" placeholder="Địa chỉ email">
        </div>

        <!-- Mục tiêu band điểm IELTS / Level -->
        <div class="vx-col md:w-1/2 w-full mb-3">
          <label class="font-medium text-sm">Mục tiêu band điểm IELTS</label>
          <input class="vs-inputx vs-input--input normal" type="text" v-model="edit_parent.level" placeholder="Ví dụ: 5.0, 6.5, C1">
        </div>

        <!-- Tỉnh / Thành phố -->
        <div class="vx-col md:w-1/2 w-full mb-3">
          <label class="font-medium text-sm">Tỉnh / Thành phố</label>
          <select class="vs-inputx vs-input--input normal" v-model="edit_parent.province_id" @change="onProvinceChange">
            <option value="">Chọn Tỉnh / Thành</option>
            <option v-for="prov in province_list" :key="prov.id" :value="prov.id">{{ prov.name }}</option>
          </select>
        </div>

        <!-- Quận / Huyện -->
        <div class="vx-col md:w-1/2 w-full mb-3">
          <label class="font-medium text-sm">Quận / Huyện</label>
          <select class="vs-inputx vs-input--input normal" v-model="edit_parent.district_id">
            <option value="">Chọn Quận / Huyện</option>
            <option v-for="dist in district_list" :key="dist.id" :value="dist.id">{{ dist.name }}</option>
          </select>
        </div>

        <!-- Địa chỉ cụ thể -->
        <div class="vx-col w-full mb-3">
          <label class="font-medium text-sm">Địa chỉ chi tiết</label>
          <input class="vs-inputx vs-input--input normal" type="text" v-model="edit_parent.address" placeholder="Số nhà, ngõ, đường, thôn/xóm...">
        </div>

        <!-- Nguồn -->
        <div class="vx-col md:w-1/2 w-full mb-3">
          <label class="font-medium text-sm">Nguồn khách hàng</label>
          <select class="vs-inputx vs-input--input normal" v-model="edit_parent.source_id">
            <option value="">Chọn nguồn</option>
            <option v-for="src in source_list" :key="src.id" :value="src.id">{{ src.name }}</option>
          </select>
        </div>

        <!-- Nguồn chi tiết -->
        <div class="vx-col md:w-1/2 w-full mb-3">
          <label class="font-medium text-sm">Kênh nguồn chi tiết</label>
          <select class="vs-inputx vs-input--input normal" v-model="edit_parent.source_detail_id">
            <option value="">Chọn kênh nguồn</option>
            <option v-for="sdet in source_detail_list" :key="sdet.id" :value="sdet.id">{{ sdet.name }}</option>
          </select>
        </div>

        <!-- Ghi chú -->
        <div class="vx-col w-full mb-3">
          <label class="font-medium text-sm">Ghi chú</label>
          <textarea rows="3" class="vs-inputx vs-input--input normal" v-model="edit_parent.note" placeholder="Thêm ghi chú đặc điểm, nhu cầu của khách hàng"></textarea>
        </div>

        <vs-alert :active.sync="modal_edit.alert.active" class="mb-4 w-full" :color="modal_edit.alert.color" closable>
          <div v-html="modal_edit.alert.body"></div>
        </vs-alert>

        <div class="vx-col w-full text-right mt-2">
          <vs-button color="dark" type="border" class="mr-3" @click="modal_edit.show = false">Hủy</vs-button>
          <vs-button color="primary" @click="saveQuickEdit">
            <i class="fa fa-save mr-1"></i> Lưu thay đổi
          </vs-button>
        </div>
      </div>
    </vs-popup>

    <!-- ========================================================================= -->
    <!-- MODAL 2: CHUYỂN TRẠNG THÁI KHÁCH HÀNG                                   -->
    <!-- ========================================================================= -->
    <vs-popup 
      class="popup-parent-custom modal_warning" 
      title="CHUYỂN TRẠNG THÁI KHÁCH HÀNG" 
      :active.sync="modal_status.show"
    >
      <div class="vx-row">
        <div class="vx-col w-full mb-4">
          <p class="text-sm text-gray-700 mb-2">
            Khách hàng: <strong>{{ status_form.parent_name }}</strong>
          </p>
          <label class="font-medium text-sm">Chọn trạng thái mới <span class="text-danger">*</span></label>
          <select class="vs-inputx vs-input--input normal mt-1" v-model="status_form.status">
            <option v-for="opt in statusOptions" :key="opt.id" :value="opt.id">{{ opt.label }}</option>
          </select>
        </div>

        <div class="vx-col w-full text-right mt-3">
          <vs-button color="dark" type="border" class="mr-3" @click="modal_status.show = false">Hủy</vs-button>
          <vs-button color="warning" @click="saveChangeStatus">
            <i class="feather icon-check mr-1"></i> Cập nhật trạng thái
          </vs-button>
        </div>
      </div>
    </vs-popup>

    <!-- ========================================================================= -->
    <!-- MODAL 3: THÊM CHĂM SÓC (CARE LOG)                                        -->
    <!-- ========================================================================= -->
    <vs-popup 
      class="popup-care-lg modal_info" 
      title="THÊM LỊCH SỬ CHĂM SÓC" 
      :active.sync="modal_care.show"
    >
      <div class="vx-row">
        <div class="vx-col w-full mb-3 bg-blue-50 p-2.5 rounded border border-blue-100">
          <span class="text-sm text-blue-800">
            Khách hàng: <strong>{{ care_form.parent_name }}</strong> - SĐT: <strong>{{ care_form.parent_phone }}</strong>
          </span>
        </div>

        <!-- Phương thức -->
        <div class="vx-col md:w-1/2 w-full mb-3">
          <label class="font-medium text-sm">Phương thức liên hệ <span class="text-danger">*</span></label>
          <select class="vs-inputx vs-input--input normal" v-model="care_form.method_id">
            <option v-for="m in method_list" :key="m.id" :value="m.id">{{ m.name }}</option>
          </select>
        </div>

        <!-- Trạng thái cuộc gọi (khi phương thức là Điện thoại) -->
        <div class="vx-col md:w-1/2 w-full mb-3" v-if="Number(care_form.method_id) === 1">
          <label class="font-medium text-sm">Trạng thái cuộc gọi</label>
          <select class="vs-inputx vs-input--input normal" v-model="care_form.call_status">
            <option value="">Chọn trạng thái</option>
            <option v-for="cst in statusOptionsCare" :key="cst.id" :value="cst.id">{{ cst.label }}</option>
          </select>
        </div>

        <!-- Lịch chăm sóc tiếp theo -->
        <div class="vx-col md:w-1/2 w-full mb-3">
          <label class="font-medium text-sm">Lịch chăm sóc tiếp theo</label>
          <input type="datetime-local" class="vs-inputx vs-input--input normal" v-model="care_form.next_care_date">
        </div>

        <!-- File đính kèm -->
        <div class="vx-col md:w-1/2 w-full mb-3">
          <label class="font-medium text-sm">File đính kèm (Ảnh JPG, PNG)</label>
          <input type="file" class="vs-inputx vs-input--input normal" id="careAttachedFile" @change="onCareFileChange" accept="image/*">
        </div>

        <!-- Nội dung chăm sóc -->
        <div class="vx-col w-full mb-3">
          <label class="font-medium text-sm">Nội dung chăm sóc cuộc gọi <span class="text-danger">*</span></label>
          <textarea rows="4" class="vs-inputx vs-input--input normal" v-model="care_form.note" placeholder="Nhập chi tiết trao đổi với khách hàng, phản hồi, tiến độ tư vấn..."></textarea>
        </div>

        <vs-alert :active.sync="modal_care.alert.active" class="mb-3 w-full" :color="modal_care.alert.color" closable>
          <div v-html="modal_care.alert.body"></div>
        </vs-alert>

        <div class="vx-col w-full text-right mt-2">
          <vs-button color="dark" type="border" class="mr-3" @click="modal_care.show = false">Hủy</vs-button>
          <vs-button color="success" @click="saveAddCare">
            <i class="fa fa-save mr-1"></i> Lưu chăm sóc
          </vs-button>
        </div>
      </div>
    </vs-popup>

    <!-- ========================================================================= -->
    <!-- MODAL 4: THÊM HỌC SINH                                                    -->
    <!-- ========================================================================= -->
    <vs-popup 
      class="popup-parent-custom modal_success" 
      title="THÊM MỚI HỌC SINH" 
      :active.sync="modal_student.show"
    >
      <div class="vx-row">
        <div class="vx-col w-full mb-3 bg-green-50 p-2.5 rounded border border-green-100">
          <span class="text-sm text-green-800">
            Phụ huynh: <strong>{{ student_form.parent_name }}</strong>
          </span>
        </div>

        <!-- Tên học sinh -->
        <div class="vx-col md:w-1/2 w-full mb-3">
          <label class="font-medium text-sm">Họ và tên học sinh <span class="text-danger">*</span></label>
          <input class="vs-inputx vs-input--input normal" type="text" v-model="student_form.name" placeholder="Nhập tên học sinh">
        </div>

        <!-- Ngày sinh -->
        <div class="vx-col md:w-1/2 w-full mb-3">
          <label class="font-medium text-sm">Ngày sinh / Năm sinh <span class="text-danger">*</span></label>
          <birthday-input v-model="student_form.birthday" />
        </div>

        <!-- Giới tính -->
        <div class="vx-col md:w-1/2 w-full mb-3">
          <label class="font-medium text-sm">Giới tính <span class="text-danger">*</span></label>
          <select class="vs-inputx vs-input--input normal" v-model="student_form.gender">
            <option value="M">Nam</option>
            <option value="F">Nữ</option>
          </select>
        </div>

        <!-- Khối lớp / Trường -->
        <div class="vx-col md:w-1/2 w-full mb-3">
          <label class="font-medium text-sm">Trường học / Khối lớp</label>
          <input class="vs-inputx vs-input--input normal" type="text" v-model="student_form.school" placeholder="Ví dụ: Tiểu học Dịch Vọng - Lớp 3">
        </div>

        <!-- Ghi chú học sinh -->
        <div class="vx-col w-full mb-3">
          <label class="font-medium text-sm">Ghi chú</label>
          <textarea rows="3" class="vs-inputx vs-input--input normal" v-model="student_form.note" placeholder="Thói quen, sở thích, lực học, lưu ý đặc biệt..."></textarea>
        </div>

        <vs-alert :active.sync="modal_student.alert.active" class="mb-3 w-full" :color="modal_student.alert.color" closable>
          <div v-html="modal_student.alert.body"></div>
        </vs-alert>

        <div class="vx-col w-full text-right mt-2">
          <vs-button color="dark" type="border" class="mr-3" @click="modal_student.show = false">Hủy</vs-button>
          <vs-button color="success" @click="saveAddStudent">
            <i class="fa fa-save mr-1"></i> Lưu học sinh
          </vs-button>
        </div>
      </div>
    </vs-popup>

    <!-- ========================================================================= -->
    <!-- MODAL 5: BÀN GIAO KHÁCH HÀNG                                             -->
    <!-- ========================================================================= -->
    <vs-popup 
      class="popup-parent-custom modal_info" 
      :title="modal_assign.title" 
      :active.sync="modal_assign.show"
    >
      <div class="vx-row" style="min-height: 250px;">
        <div class="vx-col w-full mb-4">
          <label class="font-medium text-sm">Chọn nhân viên tiếp nhận bàn giao</label>
          <multiselect
            placeholder="Chọn người phụ trách"
            v-model="owners"
            :options="users_manager_list"
            label="label_name"
            :close-on-select="false"
            :hide-selected="true"
            :multiple="true"
            :searchable="true"
            track-by="id"
            selectedLabel="" selectLabel="" deselectLabel=""
          >
            <span slot="noResult">Không tìm thấy nhân viên</span>
          </multiselect>
        </div>
        
        <div class="vx-col w-full text-right mt-auto">
          <vs-button color="dark" type="border" class="mr-3" @click="modal_assign.show = false">Hủy</vs-button>
          <vs-button color="success" @click="assignCustomer">
            <i class="feather icon-check mr-1"></i> Bàn giao
          </vs-button>
        </div>
      </div>
    </vs-popup>

    <!-- ========================================================================= -->
    <!-- MODAL 6: CHI TIẾT ĐIỂM TEST                                              -->
    <!-- ========================================================================= -->
    <vs-popup 
      class="popup-parent-custom modal_info" 
      title="CHI TIẾT KẾT QUẢ TEST & ĐÁNH GIÁ" 
      :active.sync="modal_test.show"
    >
      <div v-if="modal_test.parent">
        <div class="bg-gray-50 p-3 rounded mb-3 border">
          <h6 class="font-bold text-dark">{{ modal_test.parent.name }} - SĐT: {{ modal_test.parent.mobile_1 }}</h6>
          <p class="text-xs text-gray-600 mt-1">Mục tiêu band điểm IELTS: <span class="font-bold text-primary">{{ modal_test.parent.level || '5.0' }}</span></p>
        </div>

        <div v-if="modal_test.parent.test_score" class="p-3 border rounded bg-white mb-3">
          <h6 class="font-semibold text-sm mb-2 text-primary">Kết quả đánh giá LMS</h6>
          <div class="vx-row">
            <div class="vx-col w-1/3">
              <span class="text-xs text-gray-500">Listening / Trắc nghiệm:</span>
              <p class="font-bold text-base text-dark">{{ modal_test.parent.test_score.subject_total || 0 }}</p>
            </div>
            <div class="vx-col w-1/3">
              <span class="text-xs text-gray-500">Reading / Tư duy:</span>
              <p class="font-bold text-base text-dark">{{ modal_test.parent.test_score.thinking_total || 0 }}</p>
            </div>
            <div class="vx-col w-1/3">
              <span class="text-xs text-gray-500">Tổng điểm:</span>
              <p class="font-bold text-base text-success">{{ modal_test.parent.test_score.total_score || 0 }}</p>
            </div>
          </div>
          <p class="text-xs text-gray-400 mt-2">Ngày đánh giá: {{ modal_test.parent.test_score.eval_dt || '-' }}</p>
        </div>
        <div v-else class="p-4 text-center border rounded bg-gray-50 text-gray-500 text-sm">
          <i class="feather icon-clipboard text-2xl mb-1 block"></i>
          Chưa có bài test nào được ghi nhận cho học sinh của khách hàng này.
        </div>

        <div class="text-right mt-4">
          <vs-button color="dark" type="border" @click="modal_test.show = false">Đóng</vs-button>
          <router-link :to="`/crm/parent/${modal_test.parent.id}/detail`" class="ml-2">
            <vs-button color="primary">Vào trang chi tiết</vs-button>
          </router-link>
        </div>
      </div>
    </vs-popup>

  </div>
</template>

<script>
import axios from '../../../http/axios.js'
import Multiselect from 'vue-multiselect'
import DatePicker from 'vue2-datepicker'
import BirthdayInput from '../../../components/BirthdayInput.vue'
import u from '../../../until/helper.js'
import select from 'vue-select'
import moment from 'moment'

export default {
  name: 'CrmParentDetailList',
  components: {
    'vue-select': select,
    DatePicker,
    BirthdayInput,
    Multiselect,
  },
  data() {
    return {
      isFilterCollapsed: false,
      checked_list: [],
      activeItem: 0,
      total: {
        total_0: 0,
        total_1: 0,
        total_2: 0,
        total_3: 0,
        total_4: 0,
      },
      searchData: {
        keyword: '',
        arr_status: '',
        arr_product: '',
        arr_level: '',
        arr_owner: '',
        arr_source: '',
        arr_source_detail: '',
        level: '',
        status: '',
        product_id: '',
        owner_id: '',
        source_id: '',
        source_detail_id: '',
        pagination: this.pagination,
        dateRange: '',
        type_search: 0,
      },
      statusOptions: u.levelOptionsParent,
      statusOptionsCare: u.statusOptionsCare,
      users_manager_list: [],
      source_list: [],
      source_detail_list: [],
      product_list: [],
      province_list: [],
      district_list: [],
      method_list: [
        { id: 1, name: 'Điện thoại' },
        { id: 2, name: 'Email' },
        { id: 3, name: 'Gặp mặt trực tiếp' },
        { id: 4, name: 'Sms' },
        { id: 5, name: 'Zalo' }
      ],
      datepickerOptions: {
        closed: true,
        value: '',
        minDate: '',
        lang: {
          days: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
          months: [
            'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6',
            'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'
          ]
        }
      },
      parents: [],
      limitSource: [20, 50, 100, 500],
      pagination: {
        url: '',
        id: '',
        style: 'line',
        class: '',
        spage: 1,
        ppage: 1,
        npage: 0,
        lpage: 1,
        cpage: 1,
        total: 0,
        limit: 20,
        pages: [],
        init: 0
      },

      // Modal Bàn giao
      modal_assign: {
        title: 'BÀN GIAO KHÁCH HÀNG',
        show: false,
        color: 'info',
        error_message: ''
      },
      owner_id: '',
      owners: [],

      // Modal Cập nhật nhanh thông tin khách hàng
      modal_edit: {
        show: false,
        alert: { active: false, color: 'danger', body: '' }
      },
      edit_parent: {},

      // Modal Chuyển trạng thái
      modal_status: {
        show: false
      },
      status_form: {
        parent_id: '',
        parent_name: '',
        status: ''
      },

      // Modal Thêm chăm sóc
      modal_care: {
        show: false,
        alert: { active: false, color: 'danger', body: '' }
      },
      care_form: {
        parent_id: '',
        parent_name: '',
        parent_phone: '',
        method_id: 1,
        call_status: '',
        next_care_date: '',
        note: '',
        attached_file: ''
      },

      // Modal Thêm học sinh
      modal_student: {
        show: false,
        alert: { active: false, color: 'danger', body: '' }
      },
      student_form: {
        parent_id: '',
        parent_name: '',
        name: '',
        gender: 'M',
        birthday: '',
        school: '',
        note: ''
      },

      // Modal Điểm test
      modal_test: {
        show: false,
        parent: null
      }
    }
  },
  computed: {
    activeFilterCount() {
      let count = 0
      if (this.searchData.keyword && this.searchData.keyword.trim()) count++
      if (this.searchData.dateRange && this.searchData.dateRange.length && this.searchData.dateRange[0]) count++
      if (this.searchData.arr_status && this.searchData.arr_status.length) count++
      if (this.searchData.arr_owner && this.searchData.arr_owner.length) count++
      if (this.searchData.arr_source && this.searchData.arr_source.length) count++
      if (this.searchData.arr_product && this.searchData.arr_product.length) count++
      return count
    },
    selectAll: {
      get: function() {
        return (
          this.parents.length > 0 &&
          parseInt(this.checked_list.length) === parseInt(this.parents.length)
        )
      },
      set: function(val) {
        const selected = []
        if (val) {
          this.parents.forEach(p => selected.push(p.id))
        }
        this.checked_list = selected
      }
    }
  },
  methods: {
    // Chuyển sang chế độ xem tổng quan
    goToOverview() {
      this.$router.push('/crm/parent?view=overview')
    },

    // Lấy dữ liệu danh sách khách hàng chi tiết (API độc lập)
    getData() {
      const startDate = this.searchData.dateRange && this.searchData.dateRange[0]
        ? `${u.dateToString(this.searchData.dateRange[0])}`
        : ''
      const endDate = this.searchData.dateRange && this.searchData.dateRange[1]
        ? `${u.dateToString(this.searchData.dateRange[1])}`
        : ''

      const ids = []
      if (this.searchData.arr_status && this.searchData.arr_status.length) {
        this.searchData.arr_status.forEach(item => ids.push(item.id))
      }
      this.searchData.status = ids

      const ids_product = []
      if (this.searchData.arr_product && this.searchData.arr_product.length) {
        this.searchData.arr_product.forEach(item => ids_product.push(item.id))
      }
      this.searchData.product_id = ids_product

      const ids_owner = []
      if (this.searchData.arr_owner && this.searchData.arr_owner.length) {
        this.searchData.arr_owner.forEach(item => ids_owner.push(item.id))
      }
      this.searchData.owner_id = ids_owner

      const ids_source = []
      if (this.searchData.arr_source && this.searchData.arr_source.length) {
        this.searchData.arr_source.forEach(item => ids_source.push(item.id))
      }
      this.searchData.source_id = ids_source

      const ids_source_detail = []
      if (this.searchData.arr_source_detail && this.searchData.arr_source_detail.length) {
        this.searchData.arr_source_detail.forEach(item => ids_source_detail.push(item.id))
      }
      this.searchData.source_detail_id = ids_source_detail

      const data = {
        keyword: this.searchData.keyword,
        status: this.searchData.status,
        product_id: this.searchData.product_id,
        owner_id: this.searchData.owner_id,
        source_id: this.searchData.source_id,
        source_detail_id: this.searchData.source_detail_id,
        level: this.searchData.ids_level,
        start_date: startDate,
        end_date: endDate,
        pagination: this.pagination,
        type_search: this.searchData.type_search
      }

      localStorage.setItem('parents_detail_searchData', JSON.stringify(this.searchData))

      this.$vs.loading()
      axios.p('/api/crm/parents/detail-list', data)
        .then(response => {
          this.$vs.loading.close()
          this.parents = response.data.list || []
          this.total = response.data.detail_total || this.total
          this.pagination = response.data.paging || this.pagination
          setTimeout(() => {
            this.pagination.init = 1
          }, 300)
        })
        .catch(err => {
          console.error(err)
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Lỗi tải dữ liệu',
            text: 'Không thể tải danh sách khách hàng chi tiết. Vui lòng thử lại.',
            color: 'danger',
            iconPack: 'feather',
            icon: 'icon-alert-triangle'
          })
        })
    },

    reset() {
      this.searchData.keyword = ''
      this.searchData.arr_status = ''
      this.searchData.arr_product = ''
      this.searchData.arr_level = ''
      this.searchData.arr_owner = ''
      this.searchData.arr_source = ''
      this.searchData.arr_source_detail = ''
      this.searchData.status = ''
      this.searchData.product_id = ''
      this.searchData.level = ''
      this.searchData.owner_id = ''
      this.searchData.source_id = ''
      this.searchData.source_detail_id = ''
      this.searchData.pagination = this.pagination
      this.searchData.dateRange = ''
      this.searchData.type_search = 0
      this.activeItem = 0
      this.getData()
    },

    changePage() {
      if (this.pagination.init) {
        this.getData()
      }
    },

    changePageLimit(limit) {
      this.pagination.cpage = 1
      this.pagination.limit = limit
      this.getData()
    },

    setActive(menuItem) {
      this.searchData.type_search = menuItem
      this.activeItem = menuItem
      this.getData()
    },

    // Bàn giao nhiều khách hàng
    showModalAssign() {
      this.owners = []
      this.modal_assign.title = 'BÀN GIAO ' + this.checked_list.length + ' KHÁCH HÀNG'
      this.modal_assign.show = true
    },

    // Bàn giao đơn lẻ
    openAssignSingle(item) {
      this.checked_list = [item.id]
      this.owners = []
      this.modal_assign.title = 'BÀN GIAO KHÁCH HÀNG: ' + item.name
      this.modal_assign.show = true
    },

    assignCustomer() {
      if (!this.owners || !this.owners.length) {
        this.$vs.notify({
          title: 'Thông báo',
          text: 'Vui lòng chọn nhân viên tiếp nhận bàn giao',
          color: 'warning'
        })
        return
      }
      const ids = []
      const ownerArr = Array.isArray(this.owners) ? this.owners : [this.owners]
      ownerArr.forEach(o => ids.push(o.id))

      const postData = {
        parents: this.checked_list,
        owners: ids
      }

      this.modal_assign.show = false
      this.$vs.loading()
      axios.p('/api/crm/parents/assign_list', postData)
        .then(res => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Thành Công',
            text: res.data.message || 'Bàn giao khách hàng thành công',
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          this.checked_list = []
          this.getData()
        })
        .catch(e => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Lỗi',
            text: 'Không thể bàn giao khách hàng',
            color: 'danger'
          })
        })
    },

    // ================== CẬP NHẬT THÔNG TIN (QUICK EDIT) ==================
    openQuickEdit(item) {
      this.edit_parent = Object.assign({}, item)
      this.modal_edit.alert.active = false
      this.modal_edit.alert.body = ''
      this.modal_edit.show = true
      if (item.province_id) {
        this.loadDistricts(item.province_id)
      } else {
        this.district_list = []
      }
    },

    onProvinceChange() {
      if (this.edit_parent.province_id) {
        this.loadDistricts(this.edit_parent.province_id)
      } else {
        this.district_list = []
        this.edit_parent.district_id = ''
      }
    },

    loadDistricts(provinceId) {
      axios.g(`/api/system/provinces/${provinceId}/districts`)
        .then(res => {
          this.district_list = res.data || []
        })
        .catch(() => {
          this.district_list = []
        })
    },

    saveQuickEdit() {
      if (!this.edit_parent.name || !this.edit_parent.name.trim()) {
        this.modal_edit.alert.active = true
        this.modal_edit.alert.body = 'Họ tên khách hàng không được để trống'
        return
      }
      if (!this.edit_parent.mobile_1 || !this.edit_parent.mobile_1.trim()) {
        this.modal_edit.alert.active = true
        this.modal_edit.alert.body = 'Số điện thoại chính không được để trống'
        return
      }

      this.$vs.loading()
      axios.p('/api/crm/parents/update', this.edit_parent)
        .then(res => {
          this.$vs.loading.close()
          this.modal_edit.show = false
          this.$vs.notify({
            title: 'Thành Công',
            text: res.data.message || 'Cập nhật khách hàng thành công',
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          // Update item in local list directly for fast response
          const target = this.parents.find(p => p.id === this.edit_parent.id)
          if (target) {
            Object.assign(target, this.edit_parent)
            // Update province name
            const prov = this.province_list.find(p => p.id === Number(this.edit_parent.province_id))
            if (prov) target.province_name = prov.name
            // Update district name
            const dist = this.district_list.find(d => d.id === Number(this.edit_parent.district_id))
            if (dist) target.district_name = dist.name
            // Update source name
            const src = this.source_list.find(s => s.id === Number(this.edit_parent.source_id))
            if (src) target.source_name = src.name
          }
        })
        .catch(err => {
          this.$vs.loading.close()
          this.modal_edit.alert.active = true
          this.modal_edit.alert.body = 'Có lỗi xảy ra khi lưu thông tin khách hàng'
        })
    },

    // ================== CHUYỂN TRẠNG THÁI ==================
    openChangeStatus(item) {
      this.status_form.parent_id = item.id
      this.status_form.parent_name = item.name
      this.status_form.status = item.status
      this.modal_status.show = true
    },

    saveChangeStatus() {
      const data = {
        parent_id: this.status_form.parent_id,
        status: this.status_form.status
      }
      this.$vs.loading()
      axios.p('/api/crm/parents/change_status', data)
        .then(res => {
          this.$vs.loading.close()
          this.modal_status.show = false
          this.$vs.notify({
            title: 'Thành Công',
            text: res.data.message || 'Cập nhật trạng thái thành công',
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          const target = this.parents.find(p => p.id === this.status_form.parent_id)
          if (target) {
            target.status = this.status_form.status
          }
        })
        .catch(() => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Lỗi',
            text: 'Không thể cập nhật trạng thái',
            color: 'danger'
          })
        })
    },

    // ================== THÊM CHĂM SÓC ==================
    openAddCare(item) {
      this.care_form = {
        parent_id: item.id,
        parent_name: item.name,
        parent_phone: item.mobile_1,
        method_id: 1,
        call_status: '',
        next_care_date: '',
        note: '',
        attached_file: ''
      }
      this.modal_care.alert.active = false
      this.modal_care.alert.body = ''
      this.modal_care.show = true
      const fileEl = document.getElementById('careAttachedFile')
      if (fileEl) fileEl.value = ''
    },

    onCareFileChange(e) {
      const files = e.target.files || e.dataTransfer.files
      if (!files.length) return
      const file = files[0]
      const reader = new FileReader()
      reader.onload = (uploadEvent) => {
        this.care_form.attached_file = uploadEvent.target.result
      }
      reader.readAsDataURL(file)
    },

    saveAddCare() {
      if (!this.care_form.method_id) {
        this.modal_care.alert.active = true
        this.modal_care.alert.body = 'Phương thức liên hệ không được để trống'
        return
      }
      if (!this.care_form.note || !this.care_form.note.trim()) {
        this.modal_care.alert.active = true
        this.modal_care.alert.body = 'Nội dung chăm sóc không được để trống'
        return
      }

      this.$vs.loading()
      axios.p('/api/crm/care/add', this.care_form)
        .then(res => {
          this.$vs.loading.close()
          this.modal_care.show = false
          this.$vs.notify({
            title: 'Thành Công',
            text: res.data.message || 'Thêm chăm sóc thành công',
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })

          // Add care record to local parent care_history
          const target = this.parents.find(p => p.id === this.care_form.parent_id)
          if (target) {
            if (!target.care_history) target.care_history = []
            const newCareLog = {
              care_date: moment().format('YYYY-MM-DD HH:mm:ss'),
              note: this.care_form.note,
              call_status: this.care_form.call_status,
              method_name: this.method_list.find(m => m.id === Number(this.care_form.method_id))?.name || '',
              call_status_label: this.statusOptionsCare.find(s => s.id === Number(this.care_form.call_status))?.label || ''
            }
            target.care_history.unshift(newCareLog)
            target.last_care = this.care_form.note
            target.last_time_care = newCareLog.care_date
            if (this.care_form.next_care_date) {
              target.next_care_date = this.care_form.next_care_date
            }
          }
        })
        .catch(() => {
          this.$vs.loading.close()
          this.modal_care.alert.active = true
          this.modal_care.alert.body = 'Có lỗi xảy ra khi lưu lịch sử chăm sóc'
        })
    },

    // ================== THÊM HỌC SINH ==================
    openAddStudent(item) {
      this.student_form = {
        parent_id: item.id,
        parent_name: item.name,
        branch_id: item.branch_id || '',
        owner_id: item.owner_id || '',
        name: '',
        birthday: '',
        gender: 'M',
        school: '',
        note: ''
      }
      this.modal_student.alert.active = false
      this.modal_student.alert.body = ''
      this.modal_student.show = true
    },

    saveAddStudent() {
      if (!this.student_form.name || !this.student_form.name.trim()) {
        this.modal_student.alert.active = true
        this.modal_student.alert.body = 'Tên học sinh không được để trống'
        return
      }
      const bd = u.parseManualBirthday(this.student_form.birthday)
      if (bd && bd.date === '') {
        this.modal_student.alert.active = true
        this.modal_student.alert.body = 'Ngày sinh học sinh không được để trống'
        return
      }
      if (bd === null) {
        this.modal_student.alert.active = true
        this.modal_student.alert.body = 'Ngày sinh không đúng định dạng (yyyy hoặc dd/mm/yyyy)'
        return
      }

      this.$vs.loading()
      axios.p('/api/crm/students/add', { ...this.student_form, birthday: bd.date, birthday_year_only: bd.yearOnly })
        .then(res => {
          this.$vs.loading.close()
          if (res.data.status) {
            this.modal_student.show = false
            this.$vs.notify({
              title: 'Thành Công',
              text: res.data.message || 'Thêm học sinh thành công',
              color: 'success',
              iconPack: 'feather',
              icon: 'icon-check'
            })
            const target = this.parents.find(p => p.id === this.student_form.parent_id)
            if (target) {
              if (!target.students) target.students = []
              target.students.push({
                name: this.student_form.name,
                gender: this.student_form.gender,
                birthday: bd.date,
                birthday_year_only: bd.yearOnly
              })
              if (!target.hs1_name) {
                target.hs1_name = this.student_form.name
              } else if (!target.hs2_name) {
                target.hs2_name = this.student_form.name
              }
            }
          } else {
            this.modal_student.alert.active = true
            this.modal_student.alert.body = res.data.message || 'Không thể thêm học sinh'
          }
        })
        .catch(() => {
          this.$vs.loading.close()
          this.modal_student.alert.active = true
          this.modal_student.alert.body = 'Có lỗi xảy ra khi thêm học sinh'
        })
    },

    // ================== CHI TIẾT ĐIỂM TEST ==================
    openTestDetail(item) {
      this.modal_test.parent = item
      this.modal_test.show = true
    },

    // ================== FORMAT DATE HELPERS ==================
    formatDateOnly(dateStr) {
      if (!dateStr) return '-'
      return moment(dateStr).format('DD/MM/YYYY')
    },

    formatTimeOnly(dateStr) {
      if (!dateStr) return ''
      return moment(dateStr).format('HH:mm')
    },

    formatCareDateTime(dateStr) {
      if (!dateStr) return ''
      return moment(dateStr).format('DD/MM/YYYY HH:mm')
    }
  },

  created() {
    // Load metadata
    axios.g('/api/system/products').then(res => { this.product_list = res.data || [] })
    axios.g('/api/system/sources').then(res => { this.source_list = res.data || [] })
    axios.g('/api/system/source_detail').then(res => { this.source_detail_list = res.data || [] })
    axios.g('/api/users/get-data/users-manager').then(res => { this.users_manager_list = res.data || [] })
    axios.g('/api/system/provinces').then(res => { this.province_list = res.data || [] })

    // Restore cached filter if any
    if (localStorage.getItem('parents_detail_searchData')) {
      try {
        this.searchData = Object.assign({}, this.searchData, JSON.parse(localStorage.getItem('parents_detail_searchData')))
        this.activeItem = this.searchData.type_search || 0
      } catch (e) {
        console.error(e)
      }
    }

    this.getData()
    if (this.$route.query.reset == 1) {
      this.reset()
    }
  },

  filters: {
    getStatusName(value) {
      const item = u.levelOptionsParent.find(option => option.id === Number(value))
      return item ? item.label : 'Không xác định'
    }
  }
}
</script>

<style scoped>
/* KHUNG BỘ LỌC HIỆN ĐẠI */
.filter-box-card {
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
}
.filter-icon-badge {
  width: 34px;
  height: 34px;
  background: #ede9fe;
  color: #7367f0;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
}
.active-filter-badge {
  font-size: 12px;
  background: #e0f2fe;
  color: #0369a1;
  padding: 4px 10px;
  border-radius: 20px;
  font-weight: 600;
}
.btn-toggle-filter {
  background: #f1f5f9;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  padding: 5px 12px;
  font-size: 12.5px;
  color: #475569;
  font-weight: 500;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  transition: all 0.2s;
}
.btn-toggle-filter:hover {
  background: #e2e8f0;
  color: #1e293b;
}

/* NHÃN FILTER VÀ INPUTS */
.custom-filter-label {
  display: flex;
  align-items: center;
  font-size: 12.5px;
  font-weight: 600;
  color: #475569;
  margin-bottom: 6px;
}
.custom-input input {
  height: 40px !important;
  border-radius: 6px !important;
  border: 1px solid #cbd5e1 !important;
  background: #f8fafc !important;
  transition: all 0.2s;
}
.custom-input input:focus {
  border-color: #7367f0 !important;
  background: #fff !important;
  box-shadow: 0 0 0 3px rgba(115, 103, 240, 0.12) !important;
}

/* NÚT BẤM SHADOW */
.shadow-btn {
  box-shadow: 0 3px 10px rgba(115, 103, 240, 0.3) !important;
}

/* Nút chuyển chế độ xem (View Mode Pill Group) */
.view-mode-pill-container {
  display: flex;
  align-items: center;
}
.mode-label {
  font-size: 13px;
  font-weight: 600;
  color: #4a5568;
}
.pill-group {
  display: inline-flex;
  background: #f1f5f9;
  padding: 4px;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
}
.pill-btn {
  border: none;
  background: transparent;
  padding: 6px 14px;
  font-size: 13px;
  font-weight: 500;
  color: #64748b;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
  transition: all 0.2s ease;
  position: relative;
}
.pill-btn:hover {
  color: #1e293b;
  background: rgba(255, 255, 255, 0.6);
}
.pill-btn.active {
  background: #7367f0;
  color: #ffffff;
  font-weight: 600;
  box-shadow: 0 2px 6px rgba(115, 103, 240, 0.35);
}
.active-badge-dot {
  width: 6px;
  height: 6px;
  background: #28c76f;
  border-radius: 50%;
  margin-left: 6px;
  display: inline-block;
  box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.5);
}

/* Thanh hành động chọn */
.action-bar-selection {
  background: #f8fafc;
  border: 1px solid #edf2f7;
}

/* TABLE SCROLL & STYLING */
.detail-table-wrapper {
  background: #fff;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
}
.detail-table-scroll-container {
  overflow-x: auto;
  max-width: 100%;
}
.detail-custom-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  font-size: 13px;
}

/* TABLE HEADER (DARK NAVY THEME CHUẨN ERP) */
.detail-custom-table thead tr {
  background-color: #2b3346;
  color: #ffffff;
}
.detail-custom-table th {
  padding: 12px 14px;
  font-size: 12.5px;
  font-weight: 600;
  text-transform: none;
  border-right: 1px solid rgba(255, 255, 255, 0.12);
  white-space: nowrap;
  vertical-align: middle;
}
.detail-custom-table th:last-child {
  border-right: none;
}
.th-center, .td-center {
  text-align: center !important;
}

/* TABLE BODY */
.detail-custom-table tbody tr {
  background-color: #ffffff;
  transition: background-color 0.15s ease;
  border-bottom: 1px solid #edf2f7;
}
.detail-custom-table tbody tr:nth-child(even) {
  background-color: #fbfcfe;
}
.detail-custom-table tbody tr:hover {
  background-color: #f1f5f9;
}
.detail-custom-table tbody tr.row-selected {
  background-color: #ede9fe !important;
}
.detail-custom-table td {
  padding: 10px 14px;
  border-right: 1px solid #f1f5f9;
  border-bottom: 1px solid #e2e8f0;
  vertical-align: top;
  line-height: 1.45;
}
.detail-custom-table td:last-child {
  border-right: none;
}

/* STICKY COLUMN CÔNG CỤ */
.sticky-col {
  position: sticky;
  left: 0;
  background-color: inherit;
  z-index: 2;
}

/* TOOL BUTTONS (CÔNG CỤ) */
.action-btn-group {
  display: inline-flex;
  align-items: center;
  gap: 6px;
}
.btn-tool {
  width: 28px;
  height: 28px;
  border-radius: 5px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border: none;
  color: #ffffff;
  cursor: pointer;
  font-size: 11.5px;
  transition: transform 0.15s ease, opacity 0.15s ease;
}
.btn-tool:hover {
  transform: translateY(-1px);
  opacity: 0.9;
}
.btn-tool-purple {
  background: linear-gradient(135deg, #7367f0, #9055fd);
}
.btn-tool-orange {
  background: linear-gradient(135deg, #ff9f43, #ff7043);
}
.btn-tool-navy {
  background: #2b3346;
}

/* CHI TIẾT ĐIỂM TEST BUTTON */
.btn-test-detail {
  background: #28c76f;
  color: #fff;
  border: none;
  font-size: 11px;
  font-weight: 600;
  padding: 3px 10px;
  border-radius: 4px;
  cursor: pointer;
  transition: opacity 0.15s;
}
.btn-test-detail:hover {
  opacity: 0.9;
}

/* CARE HISTORY BOX */
.care-history-box {
  max-height: 120px;
  overflow-y: auto;
  font-size: 12px;
  line-height: 1.5;
  padding-right: 4px;
}
.care-log-row {
  margin-bottom: 6px;
  padding-bottom: 4px;
  border-bottom: 1px dashed #e2e8f0;
}
.care-log-row:last-child {
  margin-bottom: 0;
  padding-bottom: 0;
  border-bottom: none;
}
.care-method-badge {
  font-size: 10.5px;
  color: #7367f0;
  font-weight: 600;
  margin-right: 2px;
}
.care-status-badge {
  font-size: 11px;
  font-weight: 600;
  margin-right: 2px;
}

/* NOTES BULLET BOX */
.notes-bullet-box {
  font-size: 12px;
  line-height: 1.5;
}
.note-bullet-line {
  margin-bottom: 3px;
  word-break: break-word;
}
.bullet-dot {
  font-size: 14px;
  font-weight: bold;
  color: #3b82f6;
  margin-right: 4px;
}

/* CONTRACT BADGE */
.contract-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 22px;
  height: 22px;
  border-radius: 50%;
  font-size: 11px;
  font-weight: bold;
}
.contract-badge-zero {
  border: 2px solid #2b3346;
  color: #2b3346;
  background: #fff;
}
.contract-badge-green {
  background: #28c76f;
  color: #fff;
  border: 2px solid #28c76f;
}

/* PHÂN LOẠI BADGES */
.classification-box {
  display: flex;
  align-items: center;
  gap: 6px;
  flex-wrap: wrap;
}
.sub-badge {
  font-size: 10px;
  font-weight: 700;
  padding: 1px 6px;
  border-radius: 4px;
  text-transform: uppercase;
}
.sub-badge-danger {
  background: #ea5455;
  color: #fff;
}
.sub-badge-success {
  background: #28c76f;
  color: #fff;
}
.sub-badge-info {
  background: #00cfe8;
  color: #fff;
}
.badge-source {
  background: #f1f5f9;
  border: 1px solid #cbd5e1;
  padding: 2px 8px;
  border-radius: 4px;
  font-size: 11.5px;
  color: #334155;
  font-weight: 500;
}

/* DROPDOWN MENU */
.custom-action-dropdown {
  min-width: 200px;
  border-radius: 6px;
  box-shadow: 0 5px 25px rgba(0,0,0,0.1);
}
.dropdown-item-content {
  display: flex;
  align-items: center;
  font-size: 13px;
  padding: 4px 0;
}

/* POPUP CUSTOM */
.popup-parent-custom >>> .vs-popup {
  width: 720px !important;
  max-width: 95vw !important;
  border-radius: 8px;
}
.popup-care-lg >>> .vs-popup {
  width: 750px !important;
  max-width: 95vw !important;
  border-radius: 8px;
}

/* Đồng bộ input và multiselect */
#page-customer-detail-list label.vs-input--label {
  display: inline-block;
  margin-bottom: 0.35rem;
}
#page-customer-detail-list .vs-input .vs-input--input,
#page-customer-detail-list input.vs-input--input.normal,
#page-customer-detail-list select.vs-input--input.normal {
  height: 40px !important;
  font-size: 14px !important;
  border-radius: 5px !important;
  border: 1px solid rgba(0, 0, 0, 0.2) !important;
  box-sizing: border-box !important;
  padding: 0.55rem 0.7rem !important;
}
#page-customer-detail-list .multiselect {
  min-height: 40px;
}
#page-customer-detail-list .multiselect .multiselect__tags {
  min-height: 40px !important;
  padding: 8px 40px 0 10px !important;
  border-radius: 5px !important;
  border: 1px solid rgba(0, 0, 0, 0.2) !important;
  box-sizing: border-box !important;
  background: #fff !important;
}
</style>

