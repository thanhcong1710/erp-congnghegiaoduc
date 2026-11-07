<template>
  <div id="page-roles-list">
    <!-- KHUNG BỘ LỌC TÌM KIẾM HIỆN ĐẠI & TỐI ƯU -->
    <vx-card no-shadow class="mt-5 filter-box-card">
      <div class="filter-header-bar flex items-center justify-between pb-3 mb-4 border-b border-gray-100">
        <div class="flex items-center">
          <div class="filter-icon-badge mr-2">
            <i class="feather icon-filter"></i>
          </div>
          <div>
            <h5 class="font-bold text-gray-800 text-base mb-0">Bộ lọc tìm kiếm khách hàng</h5>
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

        <!-- ACTION BUTTONS VÀ CHUYỂN CHẾ ĐỘ XEM -->
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

          <!-- BỘ CHUYỂN CHẾ ĐỘ XEM SANG TRỌNG -->
          <div class="vx-col sm:w-auto w-full mb-2 flex items-center justify-end">
            <div class="view-mode-pill-container">
              <span class="mode-label mr-2"><i class="feather icon-eye text-primary mr-1"></i>Chế độ xem:</span>
              <div class="pill-group">
                <button 
                  type="button"
                  class="pill-btn active" 
                  title="Đang ở chế độ xem tổng quan"
                >
                  <i class="feather icon-list mr-1"></i> Tổng quan
                  <span class="active-badge-dot"></span>
                </button>
                <button 
                  type="button"
                  class="pill-btn" 
                  @click="goToDetailList"
                  title="Chuyển sang chế độ xem chi tiết"
                >
                  <i class="feather icon-grid mr-1"></i> Chi tiết
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- THANH CHỌN HÀNG LOẠT & BÀN GIAO -->
      <div class="selection-banner-bar py-2 px-3 mt-3 mb-2 rounded-lg flex items-center justify-between border">
        <div class="flex items-center text-sm font-medium">
          <i class="feather icon-check-square text-primary mr-2 text-base"></i>
          <span>Bạn đã lựa chọn: <b class="text-primary text-base">{{ checked_list.length }}</b> / {{ parents.length }} khách hàng</span>
        </div>
        <div>
          <vs-button 
            :disabled="checked_list.length === 0" 
            @click="showModalAssgin" 
            color="danger"
            size="small"
          >
            <i class="fa-solid fa-paper-plane mr-1"></i> Bàn giao khách hàng
          </vs-button>
        </div>
      </div>

      <!-- TABS PHÂN LOẠI VỚI BADGE ĐẾM MÀU SẮC -->
      <div class="custom-crm-tabs mt-4">
        <vs-tabs v-model="activeItem">
          <vs-tab :label="'Tất cả (' + total.total_0 + ')'" @click.prevent="setActive(0)"></vs-tab>
          <vs-tab :label="'Chưa chăm sóc (' + total.total_1 + ')'" @click.prevent="setActive(1)"></vs-tab>
          <vs-tab :label="'Lịch hôm nay (' + total.total_2 + ')'" @click.prevent="setActive(2)"></vs-tab>
          <vs-tab :label="'Quá hạn xử lý (' + total.total_3 + ')'" @click.prevent="setActive(3)"></vs-tab>
          <vs-tab :label="'Ticket tồn (' + total.total_4 + ')'" @click.prevent="setActive(4)"></vs-tab>
        </vs-tabs>
      </div>

      <!-- BẢNG DANH SÁCH KHÁCH HÀNG TỔNG QUAN NÂNG CẤP CHUẨN ERP -->
      <div class="overview-table-wrapper mt-3">
        <div class="overview-table-scroll">
          <table class="overview-custom-table" style="min-width: 1750px">
            <thead>
              <tr>
                <th class="th-center" style="width: 46px;">
                  <div class="vs-component con-vs-checkbox vs-checkbox-primary vs-checkbox-default">
                    <input type="checkbox" v-model="selectAll" class="vs-checkbox--input" >
                    <span class="checkbox_x vs-checkbox" style="border: 2px solid rgb(180, 180, 180);">
                      <span class="vs-checkbox--check">
                        <i class="vs-icon notranslate icon-scale vs-checkbox--icon material-icons null">check</i>
                      </span>
                    </span>
                  </div>
                </th>
                <th class="th-center" style="width: 60px;">STT</th>
                <th style="width: 190px;">Tên khách hàng</th>
                <th class="th-center" style="width: 130px;">Số điện thoại</th>
                <th style="width: 150px;">Học sinh</th>
                <th style="width: 130px;">Sản phẩm</th>
                <th style="width: 140px;">Nguồn</th>
                <th style="width: 160px;">Người phụ trách</th>
                <th class="th-center" style="width: 130px;">Lịch hẹn</th>
                <th style="width: 300px;">Lịch sử chăm sóc</th>
                <th class="th-center" style="width: 140px;">Chăm sóc gần nhất</th>
                <th class="th-center" style="width: 170px;">Trạng thái</th>
                <th class="th-center" style="width: 110px;">Thao tác</th>
              </tr>
            </thead>
            <tbody>
              <tr 
                v-for="(item, index) in parents" 
                :key="item.id || index"
                :class="{ 'row-active-selected': checked_list.includes(item.id) }"
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

                <!-- STT -->
                <td class="td-center font-medium text-gray-500">
                  {{ index + 1 + (pagination.cpage - 1) * pagination.limit }}
                </td>

                <!-- Tên KH -->
                <td>
                  <router-link :to="`/crm/parent/${item.id}/detail`" class="font-semibold text-blue-600 hover:underline">
                    {{ item.name }}
                  </router-link>
                </td>

                <!-- Số điện thoại -->
                <td class="td-center">
                  <a 
                    href="javascript:void(0)" 
                    class="font-medium text-blue-600 hover:underline inline-flex items-center"
                    @click="openQuickCare(item)"
                    title="Bấm để ghi nhận chăm sóc"
                  >
                    <i class="feather icon-phone text-xs mr-1 text-primary"></i>
                    {{ item.mobile_1 }}
                  </a>
                </td>

                <!-- Học sinh -->
                <td>
                  <span v-if="item.hs1_name" class="inline-block bg-blue-50 text-blue-700 font-medium px-2 py-0.5 rounded text-xs">
                    {{ item.hs1_name }}
                    <span v-if="item.hs2_name">, {{ item.hs2_name }}</span>
                  </span>
                  <span v-else class="text-gray-400 italic text-xs">Chưa có</span>
                </td>

                <!-- Sản phẩm -->
                <td>
                  <span v-if="item.product_name" class="badge-tag-product">
                    {{ item.product_name }}
                  </span>
                  <span v-else class="text-gray-400 text-xs">-</span>
                </td>

                <!-- Nguồn -->
                <td>
                  <span v-if="item.source_name" class="badge-tag-source">
                    {{ item.source_name }}
                  </span>
                  <span v-else class="text-gray-400 text-xs">-</span>
                </td>

                <!-- Người phụ trách -->
                <td class="font-medium text-gray-800 text-sm">
                  {{ item.owner_name || '-' }}
                </td>

                <!-- Lịch hẹn -->
                <td class="td-center text-xs">
                  <span v-if="item.next_care_date" class="font-medium text-primary">
                    {{ item.next_care_date }}
                  </span>
                  <span v-else class="text-gray-400">-</span>
                </td>

                <!-- Lịch sử chăm sóc -->
                <td>
                  <div class="care-text-snippet" :title="item.last_care">
                    {{ item.last_care || 'Chưa có lịch sử chăm sóc' }}
                  </div>
                </td>

                <!-- Thời gian chăm sóc gần nhất -->
                <td class="td-center text-xs text-gray-600">
                  {{ item.last_time_care || '-' }}
                </td>

                <!-- Trạng thái -->
                <td class="td-center">
                  <span class="status-pill" :class="getStatusClass(item.status)">
                    {{ item.status | getStatusName }}
                  </span>
                </td>

                <!-- Thao tác -->
                <td class="td-center">
                  <div class="flex items-center justify-center gap-1">
                    <!-- Nút xem chi tiết -->
                    <router-link :to="`/crm/parent/${item.id}/detail`" title="Xem chi tiết hồ sơ">
                      <button class="btn-action-icon btn-action-view">
                        <i class="feather icon-eye"></i>
                      </button>
                    </router-link>
                    <!-- Nút thêm chăm sóc nhanh -->
                    <button class="btn-action-icon btn-action-care" @click="openQuickCare(item)" title="Thêm chăm sóc nhanh">
                      <i class="fa fa-phone-volume"></i>
                    </button>
                  </div>
                </td>
              </tr>

              <!-- Trống dữ liệu -->
              <tr v-if="!parents || parents.length === 0">
                <td colspan="13" class="text-center py-8 text-gray-400">
                  <i class="feather icon-inbox text-3xl mb-2 block"></i>
                  Không tìm thấy khách hàng phù hợp với điều kiện lọc
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- PHÂN TRANG HIỆN ĐẠI -->
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

    <!-- MODAL BÀN GIAO KHÁCH HÀNG -->
    <vs-popup :class="'modal_'+ modal_assign.color" :title="modal_assign.title" :active.sync="modal_assign.show">
      <div class="vx-row" style="min-height: 250px;"> 
        <div class="vx-col w-full mb-4">
          <label class="font-medium text-sm mb-2 block">Chọn người phụ trách tiếp nhận</label>
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
            <span slot="noResult">Không tìm thấy dữ liệu</span>
          </multiselect>
        </div>
        
        <div class="vx-col w-full text-right mt-auto">
          <vs-button color="dark" type="border" class="mr-3" @click="modal_assign.show = false">Hủy</vs-button>
          <vs-button color="success" @click="assignCustomer">
            <i class="feather icon-check mr-1"></i> Xác nhận bàn giao
          </vs-button>
        </div>
      </div>
    </vs-popup>

    <!-- MODAL THÊM CHĂM SÓC NHANH (QUICK CARE NGAY TRÊN MÀN HÌNH TỔNG QUAN) -->
    <vs-popup class="popup-quick-care modal_primary" title="THÊM LỊCH SỬ CHĂM SÓC KHÁCH HÀNG" :active.sync="modal_care.show">
      <div class="vx-row">
        <div class="vx-col w-full mb-3 bg-blue-50 p-2.5 rounded border border-blue-100">
          <span class="text-sm text-blue-800">
            Khách hàng: <strong>{{ care_form.parent_name }}</strong> - SĐT: <strong>{{ care_form.parent_phone }}</strong>
          </span>
        </div>

        <div class="vx-col md:w-1/2 w-full mb-3">
          <label class="font-medium text-sm">Phương thức liên hệ <span class="text-danger">*</span></label>
          <select class="vs-inputx vs-input--input normal" v-model="care_form.method_id">
            <option :value="1">Điện thoại</option>
            <option :value="2">Email</option>
            <option :value="3">Gặp mặt trực tiếp</option>
            <option :value="4">Sms</option>
            <option :value="5">Zalo</option>
          </select>
        </div>

        <div class="vx-col md:w-1/2 w-full mb-3" v-if="Number(care_form.method_id) === 1">
          <label class="font-medium text-sm">Trạng thái cuộc gọi</label>
          <select class="vs-inputx vs-input--input normal" v-model="care_form.call_status">
            <option value="">Chọn trạng thái</option>
            <option v-for="cst in statusOptionsCare" :key="cst.id" :value="cst.id">{{ cst.label }}</option>
          </select>
        </div>

        <div class="vx-col md:w-1/2 w-full mb-3">
          <label class="font-medium text-sm">Lịch chăm sóc tiếp theo</label>
          <input type="datetime-local" class="vs-inputx vs-input--input normal" v-model="care_form.next_care_date">
        </div>

        <div class="vx-col w-full mb-3">
          <label class="font-medium text-sm">Nội dung cuộc gọi / chăm sóc <span class="text-danger">*</span></label>
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

  </div>
</template>

<script>
import axios from '../../../http/axios.js'
import Multiselect from 'vue-multiselect'
import DatePicker from 'vue2-datepicker'
import u from '../../../until/helper.js'
import select from 'vue-select'
import moment from 'moment'

export default {
  name: 'CrmParentOverviewList',
  components: {
    'vue-select': select,
    DatePicker,
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
        url: '/api/roles/list',
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
      modal_assign: {
        title: 'BÀN GIAO KHÁCH HÀNG',
        show: false,
        color: 'info',
        closeOnBackdrop: true,
        error_message: ''
      },
      owner_id: '',
      owners: [],

      // Modal Thêm chăm sóc nhanh ngay trên màn hình tổng quan
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
      set: function(value) {
        const selected_list = []
        if (value) {
          this.parents.forEach(parent => {
            selected_list.push(parent.id)
          })
        }
        this.checked_list = selected_list
      }
    }
  },
  methods: {
    goToDetailList() {
      this.$router.push('/crm/parent/detail-list')
    },
    showModalAssgin() {
      this.owner_id = ''
      this.owners = []
      this.modal_assign.title = 'BÀN GIAO ' + this.checked_list.length + ' KHÁCH HÀNG'
      this.modal_assign.show = true
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
    getData() {
      const startDate = typeof this.searchData.dateRange != 'undefined' && this.searchData.dateRange != '' && this.searchData.dateRange[0] ? `${u.dateToString(this.searchData.dateRange[0])}` : ''
      const endDate = typeof this.searchData.dateRange != 'undefined' && this.searchData.dateRange != '' && this.searchData.dateRange[1] ? `${u.dateToString(this.searchData.dateRange[1])}` : ''
      
      const ids = []
      if (this.searchData.arr_status && this.searchData.arr_status.length) {
        this.searchData.arr_status.map(item => {
          ids.push(item.id)
        })
      }
      this.searchData.status = ids

      const ids_product = []
      if (this.searchData.arr_product && this.searchData.arr_product.length) {
        this.searchData.arr_product.map(item => {
          ids_product.push(item.id)
        })
      }
      this.searchData.product_id = ids_product

      const ids_owner = []
      if (this.searchData.arr_owner && this.searchData.arr_owner.length) {
        this.searchData.arr_owner.map(item => {
          ids_owner.push(item.id)
        })
      }
      this.searchData.owner_id = ids_owner

      const ids_source = []
      if (this.searchData.arr_source && this.searchData.arr_source.length) {
        this.searchData.arr_source.map(item => {
          ids_source.push(item.id)
        })
      }
      this.searchData.source_id = ids_source

      const ids_source_detail = []
      if (this.searchData.arr_source_detail && this.searchData.arr_source_detail.length) {
        this.searchData.arr_source_detail.map(item => {
          ids_source_detail.push(item.id)
        })
      }
      this.searchData.source_detail_id = ids_source_detail

      const ids_level = []
      if (this.searchData.arr_level && this.searchData.arr_level.length) {
        this.searchData.arr_level.map(item => {
          ids_level.push(item.id)
        })
      }
      this.searchData.ids_level = ids_level

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
      localStorage.setItem("parents_searchData", JSON.stringify(this.searchData))

      this.$vs.loading()
      axios.p('/api/crm/parents/list', data)
        .then((response) => {
          this.$vs.loading.close()
          this.parents = response.data.list
          this.total = response.data.detail_total
          this.pagination = response.data.paging
          setTimeout(() => {
            this.pagination.init = 1
          }, 300)
        })
        .catch((error) => {
          console.error(error)
          this.$vs.loading.close()
        })
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
    assignCustomer() {
      if (this.owners.length) {
        const ids = []
        this.owners = u.is.obj(this.owners) ? [this.owners] : this.owners
        if (this.owners.length) {
          this.owners.map(item => {
            ids.push(item.id)
          })
        }
        const data = {
          parents: this.checked_list,
          owners: ids,
        }
        this.modal_assign.show = false
        this.$vs.loading()
        axios.p(`/api/crm/parents/assign_list`, data)
          .then((response) => {
            this.$vs.loading.close()
            this.$vs.notify({
              title: 'Thành Công',
              text: response.data.message,
              color: 'success',
              iconPack: 'feather',
              icon: 'icon-check'
            })
            this.getData()
            this.checked_list = []
          })
          .catch((e) => {
            this.$vs.loading.close()
          })
      }
    },

    // Mở nhanh modal thêm chăm sóc ngay trên bảng tổng quan
    openQuickCare(item) {
      this.care_form = {
        parent_id: item.id,
        parent_name: item.name,
        parent_phone: item.mobile_1,
        method_id: 1,
        call_status: '',
        next_care_date: '',
        note: '',
      }
      this.modal_care.alert.active = false
      this.modal_care.alert.body = ''
      this.modal_care.show = true
    },
    saveAddCare() {
      if (!this.care_form.method_id) {
        this.modal_care.alert.active = true
        this.modal_care.alert.body = 'Vui lòng chọn phương thức liên hệ'
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
          const target = this.parents.find(p => p.id === this.care_form.parent_id)
          if (target) {
            target.last_care = this.care_form.note
            target.last_time_care = moment().format('YYYY-MM-DD HH:mm:ss')
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

    getStatusClass(status) {
      const s = Number(status)
      if (s === 1) return 'status-new'
      if (s === 2 || s === 3) return 'status-danger'
      if (s === 5 || s === 7) return 'status-warning'
      if (s === 8 || s === 9 || s === 10) return 'status-success'
      return 'status-default'
    }
  },
  created() {
    axios.g(`/api/system/products`).then(response => {
      this.product_list = response.data
    })
    axios.g(`/api/system/sources`).then(response => {
      this.source_list = response.data
    })
    axios.g(`/api/system/source_detail`).then(response => {
      this.source_detail_list = response.data
    })
    axios.g(`/api/users/get-data/users-manager`).then(response => {
      this.users_manager_list = response.data
    })
    if (localStorage.getItem("parents_searchData")) {
      this.searchData = Object.assign({}, this.searchData, JSON.parse(localStorage.getItem("parents_searchData")))
      this.activeItem = this.searchData.type_search
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

/* NÚT CHUYỂN CHẾ ĐỘ XEM (VIEW MODE PILL GROUP) */
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

/* BANNER BÀN GIAO */
.selection-banner-bar {
  background: #f8fafc;
  border-color: #e2e8f0;
}

/* BẢNG TỔNG QUAN NÂNG CẤP */
.overview-table-wrapper {
  background: #fff;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
}
.overview-table-scroll {
  overflow-x: auto;
  max-width: 100%;
}
.overview-custom-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  font-size: 13px;
}
.overview-custom-table thead tr {
  background-color: #2b3346;
  color: #ffffff;
}
.overview-custom-table th {
  padding: 12px 14px;
  font-size: 12.5px;
  font-weight: 600;
  border-right: 1px solid rgba(255, 255, 255, 0.12);
  white-space: nowrap;
  vertical-align: middle;
}
.overview-custom-table th:last-child {
  border-right: none;
}
.th-center, .td-center {
  text-align: center !important;
}
.overview-custom-table tbody tr {
  background-color: #ffffff;
  transition: background-color 0.15s ease;
  border-bottom: 1px solid #edf2f7;
}
.overview-custom-table tbody tr:nth-child(even) {
  background-color: #fbfcfe;
}
.overview-custom-table tbody tr:hover {
  background-color: #f1f5f9;
}
.overview-custom-table tbody tr.row-active-selected {
  background-color: #ede9fe !important;
}
.overview-custom-table td {
  padding: 10px 14px;
  border-right: 1px solid #f1f5f9;
  border-bottom: 1px solid #e2e8f0;
  vertical-align: middle;
  line-height: 1.45;
}
.overview-custom-table td:last-child {
  border-right: none;
}

/* TAGS SẢN PHẨM & NGUỒN */
.badge-tag-product {
  background: #eef2ff;
  color: #4f46e5;
  border: 1px solid #c7d2fe;
  font-size: 11.5px;
  font-weight: 500;
  padding: 2px 8px;
  border-radius: 4px;
}
.badge-tag-source {
  background: #f1f5f9;
  color: #334155;
  border: 1px solid #cbd5e1;
  font-size: 11.5px;
  font-weight: 500;
  padding: 2px 8px;
  border-radius: 4px;
}
.care-text-snippet {
  max-width: 300px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  font-size: 12px;
  color: #475569;
}

/* STATUS PILL */
.status-pill {
  display: inline-block;
  padding: 3px 10px;
  border-radius: 20px;
  font-size: 11.5px;
  font-weight: 600;
}
.status-new {
  background: #e0f2fe;
  color: #0284c7;
}
.status-danger {
  background: #fee2e2;
  color: #dc2626;
}
.status-warning {
  background: #fef3c7;
  color: #d97706;
}
.status-success {
  background: #dcfce7;
  color: #16a34a;
}
.status-default {
  background: #f1f5f9;
  color: #64748b;
}

/* ACTION BUTTONS */
.btn-action-icon {
  width: 30px;
  height: 30px;
  border-radius: 6px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border: none;
  color: #fff;
  cursor: pointer;
  font-size: 12px;
  transition: all 0.15s;
}
.btn-action-view {
  background: #7367f0;
}
.btn-action-view:hover {
  background: #5e50ee;
  transform: translateY(-1px);
}
.btn-action-care {
  background: linear-gradient(135deg, #a855f7, #7c3aed);
}
.btn-action-care:hover {
  opacity: 0.9;
  transform: translateY(-1px);
}

/* POPUP */
.popup-quick-care >>> .vs-popup {
  width: 650px !important;
  max-width: 95vw !important;
  border-radius: 8px;
}

/* ĐỒNG BỘ MULTISELECT & DATEPICKER */
>>> .multiselect {
  min-height: 40px;
}
>>> .multiselect .multiselect__tags {
  min-height: 40px !important;
  padding: 8px 40px 0 10px !important;
  border-radius: 6px !important;
  border: 1px solid #cbd5e1 !important;
  background: #f8fafc !important;
  box-sizing: border-box !important;
}
>>> .multiselect--active .multiselect__tags {
  border-color: #7367f0 !important;
  background: #fff !important;
  box-shadow: 0 0 0 3px rgba(115, 103, 240, 0.12) !important;
}
>>> .mx-datepicker {
  width: 100% !important;
}
>>> .mx-datepicker .mx-input {
  height: 40px !important;
  line-height: 40px !important;
  font-size: 13.5px !important;
  border-radius: 6px !important;
  border: 1px solid #cbd5e1 !important;
  background-color: #f8fafc !important;
  box-shadow: none !important;
}
>>> .mx-datepicker .mx-input:focus {
  border-color: #7367f0 !important;
  background-color: #fff !important;
  box-shadow: 0 0 0 3px rgba(115, 103, 240, 0.12) !important;
}
</style>
