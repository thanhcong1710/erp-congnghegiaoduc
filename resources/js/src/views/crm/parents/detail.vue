

<template>

  <div id="page-parent-detail" class="crm-detail-page">
    <!-- 1. MODERN CRM HERO CARD -->
    <vx-card no-shadow class="mb-base crm-hero-card">
      <div class="crm-hero-grid">
        <!-- 1.1 Profile / Avatar & Basic Contact -->
        <div class="crm-hero-profile">
          <div class="crm-avatar-box">
            <img class="crm-avatar-img" :src="parent.avatar_url ? parent.avatar_url : '/images/common/avatar-default.jpg'" :alt="parent.name">
            <label for="account-upload" class="crm-avatar-upload-btn" title="Đổi ảnh đại diện">
              <i class="fa fa-camera"></i>
            </label>
            <input type="file" ref="file" multiple="multiple" id="account-upload" hidden accept="image/*" @change="submitFiles"/>
          </div>

          <div class="crm-profile-info">
            <div class="flex items-center flex-wrap gap-2 mb-1">
              <h4 class="font-bold text-gray-900 m-0 text-lg">{{ parent.name || 'Khách hàng' }}</h4>
              <span class="badge-parent-gender" :class="parent.gender === 'F' ? 'badge-female' : 'badge-male'">
                <i :class="parent.gender === 'F' ? 'fa fa-venus' : 'fa fa-mars'" class="mr-1"></i>
                {{ parent.gender === 'F' ? 'Bà' : 'Ông' }}
              </span>
              <span class="badge-parent-id">ID: #{{ parent.id }}</span>
            </div>

            <div class="crm-phone-chips mt-2">
              <div class="crm-phone-pill" v-if="parent.mobile_1">
                <i class="fa fa-phone text-emerald-500 mr-1.5"></i>
                <strong class="text-gray-800">{{ parent.mobile_1 }}</strong>
                <div class="crm-phone-actions">
                  <button type="button" class="btn-circle-action btn-call" @click="callPhone(parent.mobile_1)" title="Gọi điện">
                    <i class="fa fa-phone"></i>
                  </button>
                  <button type="button" class="btn-circle-action btn-sms" @click="showSendSms(parent.mobile_1)" title="Gửi tin nhắn SMS">
                    <i class="fa fa-comment-dots"></i>
                  </button>
                </div>
              </div>

              <div class="crm-phone-pill" v-if="parent.mobile_2">
                <i class="fa fa-phone text-blue-500 mr-1.5"></i>
                <strong class="text-gray-800">{{ parent.mobile_2 }}</strong>
                <div class="crm-phone-actions">
                  <button type="button" class="btn-circle-action btn-call" @click="callPhone(parent.mobile_2)" title="Gọi điện số 2">
                    <i class="fa fa-phone"></i>
                  </button>
                  <button type="button" class="btn-circle-action btn-sms" @click="showSendSms(parent.mobile_2)" title="Gửi SMS số 2">
                    <i class="fa fa-comment-dots"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 1.2 Middle: Interaction & Care KPIs -->
        <div class="crm-hero-kpis">
          <div class="crm-kpi-item">
            <div class="crm-kpi-icon bg-blue-50 text-blue-600">
              <i class="fa fa-headset"></i>
            </div>
            <div class="crm-kpi-text">
              <span class="crm-kpi-label">Số lần chăm sóc</span>
              <strong class="crm-kpi-val text-blue-700">{{ parent.num_care || 0 }} <span class="text-xs font-normal">lượt</span></strong>
            </div>
          </div>

          <div class="crm-kpi-item">
            <div class="crm-kpi-icon bg-emerald-50 text-emerald-600">
              <i class="fa fa-history"></i>
            </div>
            <div class="crm-kpi-text">
              <span class="crm-kpi-label">Liên hệ lần cuối</span>
              <strong class="crm-kpi-val text-gray-800 text-xs">{{ parent.last_care || 'Chưa liên hệ' }}</strong>
            </div>
          </div>

          <div class="crm-kpi-next-care">
            <label class="crm-next-care-label">
              <i class="fa fa-calendar-alt text-amber-500 mr-1"></i> Lịch chăm sóc tiếp theo:
            </label>
            <div class="crm-next-care-input-box">
              <input
                class="crm-next-care-input"
                type="datetime-local"
                :value="parent.next_care_date ? parent.next_care_date.replace(' ', 'T').slice(0, 16) : ''"
                id="next_care_date"
                @change="updateNextCareDate"
                :disabled="disabled_action"
              />
            </div>
          </div>
        </div>

        <!-- 1.3 Right: Status & Owner Quick Controls -->
        <div class="crm-hero-controls">
          <div class="crm-control-box">
            <label class="crm-control-label"><i class="fa fa-tag text-primary mr-1"></i> Trạng thái khách hàng</label>
            <select class="crm-control-select" @change="openConfirmChangeStatus" v-model="tmp_status">
              <option 
                v-for="option in levelOptionsParent" 
                :key="option.id" 
                :value="option.id">
                {{ option.label }}
              </option>
            </select>
          </div>

          <div class="crm-control-box mt-2">
            <label class="crm-control-label"><i class="fa fa-user-tie text-primary mr-1"></i> Người phụ trách (EC)</label>
            <select class="crm-control-select" @change="openConfirmAssgin" v-model="tmp_owner_id">
              <option :value="item.id" v-for="(item, index) in users_manager" :key="index">{{ item.label_name }}</option>
            </select>
          </div>
        </div>
      </div>
    </vx-card>

    <!-- Calling card modal-inline -->
    <vx-card no-shadow class="mb-base crm-inline-card" v-if="phone.show">
      <div class="crm-action-panel">
        <h5 class="alert-heading flex items-center font-bold text-gray-800 m-0">
          <i class="fa fa-phone text-emerald-500 mr-2"></i> {{ phone.title }}
        </h5>
        <vs-divider class="my-3"/>
        <div v-html="phone.description" class="text-sm text-gray-700"></div>
        <div class="mt-3">
          <label class="font-medium text-sm text-gray-700">Trạng thái cuộc gọi <span class="text-danger">*</span></label>
          <select class="vs-inputx vs-input--input normal mt-1" v-model="phone.select_note_status">
            <option value="">Chọn trạng thái</option>
            <option 
              v-for="option in statusOptionsCare" 
              :key="option.id" 
              :value="option.id">
              {{ option.label }}
            </option>
          </select>
        </div>
        <div class="mt-3">
          <label class="font-medium text-sm text-gray-700">Ghi chú cuộc gọi <span class="text-danger">*</span></label>
          <textarea rows="3" class="vs-inputx vs-input--input normal mt-1" v-model="phone.note" placeholder="Thêm ghi chú cuộc gọi..."></textarea>
        </div>
        <vs-alert :active.sync="phone.alert.active" class="my-3" :color="phone.alert.color" closable icon-pack="feather" close-icon="icon-x">
          <div v-html="phone.alert.body"></div>
        </vs-alert>
        <div class="mt-3 flex justify-end gap-2">
          <vs-button color="dark" type="border" @click="phone.show = false"><i class="fa fa-times mr-1"></i> Hủy</vs-button>
          <vs-button color="success" class="shadow-sm" @click="updateNotePhone"><i class="fa fa-paper-plane mr-1"></i> Lưu ghi chú</vs-button>
        </div>
      </div>
    </vx-card>
      
    <!-- SMS card modal-inline -->
    <vx-card no-shadow class="mb-base crm-inline-card" v-if="sms.show">
      <div class="crm-action-panel">
        <h5 class="alert-heading flex items-center font-bold text-gray-800 m-0">
          <i class="fa fa-sms text-blue-500 mr-2"></i> {{ sms.title }}
        </h5>
        <vs-divider class="my-3"/>
        <textarea rows="3" class="vs-inputx vs-input--input normal" v-model="sms.content" placeholder="Nhập nội dung tin nhắn SMS..."></textarea>
        <div class="mt-3 flex justify-end gap-2">
          <vs-button color="dark" type="border" @click="sms.show = false"><i class="fa fa-times mr-1"></i> Hủy</vs-button>
          <vs-button color="success" class="shadow-sm" @click="sendSms"><i class="fa fa-paper-plane mr-1"></i> Gửi SMS</vs-button>
        </div>
      </div>
    </vx-card>

      <!-- 2. MODERN TABS CARD -->
      <vx-card no-shadow class="mt-4 crm-tabs-card">
        <vs-tabs v-model="active_tab" class="crm-modern-tabs">
          <!-- TAB 1: THÔNG TIN CHI TIẾT -->
          <vs-tab label="Thông tin" @click="changeTab()">
            <div class="tab-content-wrapper p-2">
              <div class="vx-row">
                <!-- Cột trái: Thông tin cá nhân -->
                <div class="vx-col md:w-1/2 w-full mb-3">
                  <div class="crm-form-section">
                    <h6 class="crm-section-title"><i class="fa fa-user text-primary mr-1.5"></i> Thông tin cá nhân</h6>
                    
                    <div class="vx-row">
                      <div class="vx-col sm:w-1/3 w-full mb-3">
                        <label class="crm-field-label">Danh xưng <span class="text-danger">*</span></label>
                        <select class="vs-inputx vs-input--input normal" v-model="parent.gender" :disabled="disabled_edit">
                          <option value="M">Ông</option>
                          <option value="F">Bà</option>
                        </select>
                      </div>
                      <div class="vx-col sm:w-2/3 w-full mb-3">
                        <label class="crm-field-label">Họ tên phụ huynh <span class="text-danger">*</span></label>
                        <input
                          class="vs-inputx vs-input--input normal"
                          type="text"
                          v-model="parent.name"
                          :disabled="disabled_edit"
                        />
                      </div>
                    </div>

                    <div class="vx-row">
                      <div class="vx-col sm:w-1/2 w-full mb-3">
                        <label class="crm-field-label">Điện thoại chính <span class="text-danger">*</span></label>
                        <input
                          class="vs-inputx vs-input--input normal"
                          type="text"
                          v-model="parent.mobile_1"
                          @change="validatePhone"
                          :disabled="disabled_edit"
                        />
                      </div>
                      <div class="vx-col sm:w-1/2 w-full mb-3">
                        <label class="crm-field-label">Điện thoại phụ</label>
                        <input
                          class="vs-inputx vs-input--input normal"
                          type="text"
                          v-model="parent.mobile_2"
                          @change="validatePhone2"
                          :disabled="disabled_edit"
                        />
                      </div>
                    </div>

                    <div class="vx-row">
                      <div class="vx-col sm:w-1/2 w-full mb-3">
                        <label class="crm-field-label">Email</label>
                        <input
                          class="vs-inputx vs-input--input normal"
                          type="text"
                          v-model="parent.email"
                          :disabled="disabled_edit"
                        />
                      </div>
                      <div class="vx-col sm:w-1/2 w-full mb-3">
                        <label class="crm-field-label">Ngày sinh</label>
                        <datepicker class="w-full calendar-input"
                          v-model="parent.birthday"
                          placeholder="Chọn ngày sinh nhật"
                          :lang="datepickerOptions.lang"
                          @change="selectDateParent"
                          :disabled="disabled_edit"
                        />
                      </div>
                    </div>

                    <div class="mb-3">
                      <label class="crm-field-label">Nghề nghiệp</label>
                      <vue-select
                        label="title"
                        placeholder="Chọn nghề nghiệp"
                        :options="html.jobs.list"
                        v-model="parent_input.job"
                        :searchable="true"
                        language="tv-VN"
                        @input="saveJob"
                        :disabled="disabled_edit"
                      ></vue-select>
                    </div>

                    <div class="mb-2">
                      <label class="crm-field-label">Ghi chú phụ huynh</label>
                      <textarea rows="3" class="vs-inputx vs-input--input normal" v-model="parent.note" :disabled="disabled_edit" placeholder="Thói quen, mong muốn, lưu ý đặc biệt..."></textarea>
                    </div>
                  </div>
                </div>

                <!-- Cột phải: Địa chỉ & Nguồn tiếp cận -->
                <div class="vx-col md:w-1/2 w-full mb-3">
                  <div class="crm-form-section">
                    <h6 class="crm-section-title"><i class="fa fa-map-marker-alt text-primary mr-1.5"></i> Địa chỉ & Nguồn tiếp cận</h6>

                    <div class="vx-row">
                      <div class="vx-col sm:w-1/2 w-full mb-3">
                        <label class="crm-field-label">Tỉnh / Thành Phố</label>
                        <vue-select
                          label="name"
                          placeholder="Chọn Tỉnh/Thành Phố"
                          :options="html.province.list"
                          v-model="parent_input.province"
                          :searchable="true"
                          language="tv-VN"
                          @input="getDistrict"
                          :disabled="disabled_edit"
                        ></vue-select>
                      </div>
                      <div class="vx-col sm:w-1/2 w-full mb-3">
                        <label class="crm-field-label">Quận / Huyện</label>
                        <vue-select
                          label="name"
                          placeholder="Chọn Quận/Huyện/Thị Xã"
                          :options="html.district.list"
                          v-model="parent_input.district"
                          :searchable="true"
                          language="tv-VN"
                          @input="saveDistrict"
                          :disabled="disabled_edit"
                        ></vue-select>
                      </div>
                    </div>

                    <div class="mb-3">
                      <label class="crm-field-label">Nguồn dữ liệu <span class="text-danger">*</span></label>
                      <vue-select
                        label="name"
                        placeholder="Chọn nguồn"
                        :options="html.source.list"
                        v-model="parent_input.source"
                        :searchable="true"
                        language="tv-VN"
                        @input="localeDataSourceDetail"
                        :disabled="disabled_edit"
                      ></vue-select>
                    </div>

                    <div class="mb-3">
                      <label class="crm-field-label">Nguồn chi tiết</label>
                      <vue-select
                        label="name"
                        placeholder="Chọn nguồn chi tiết"
                        :options="html.source_detail.list"
                        v-model="parent_input.source_detail"
                        :searchable="true"
                        language="tv-VN"
                        @input="saveSourceDetail"
                        :disabled="disabled_edit"
                      ></vue-select>
                    </div>

                    <div class="mb-3" v-if="this.parent.source_id == 3">
                      <label class="crm-field-label">Người giới thiệu (SĐT C2C)</label>
                      <input
                        class="vs-inputx vs-input--input normal"
                        type="text"
                        v-model="parent.c2c_mobile"
                        @change="validatePhoneC2C"
                        :disabled="disabled_edit"
                        placeholder="Nhập số điện thoại người giới thiệu..."
                      />
                      <p v-if="c2c_info" class="text-xs text-primary font-medium mt-1"><i>{{ c2c_info }}</i></p>
                    </div>
                  </div>
                </div>

                <!-- Thanh nút thao tác -->
                <div class="vx-col w-full mt-2">
                  <vs-alert :active.sync="alert.active" class="mb-4" :color="alert.color" closable icon-pack="feather" close-icon="icon-x">
                    <div v-html="alert.body"></div>
                  </vs-alert>
                  <div class="flex items-center gap-2">
                    <vs-button color="primary" class="shadow-sm" @click="disabled_edit = false" v-if="disabled_edit">
                      <i class="fa fa-edit mr-1"></i> Cập nhật thông tin
                    </vs-button>
                    <vs-button color="dark" type="border" @click="reloadPage" v-if="!disabled_edit">
                      Hủy bỏ
                    </vs-button>
                    <vs-button color="success" class="shadow-sm" @click="saveInfo" v-if="!disabled_edit">
                      <i class="fa fa-save mr-1"></i> Lưu thông tin
                    </vs-button>
                  </div>
                </div>
              </div>
            </div>
          </vs-tab>

          <!-- TAB 2: TICKET -->
          <vs-tab :label="'Ticket (' + total_unsuccess + ')'" @click="changeTab()">
            <div class="tab-content-wrapper p-2">
              <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-semibold text-gray-700">Yêu cầu & Khiếu nại từ khách hàng</span>
                <vs-button :disabled="disabled_action" color="success" size="small" class="shadow-sm" @click="showModalTicket">
                  <i class="fa fa-plus mr-1"></i> Thêm mới Ticket
                </vs-button>
              </div>

              <div class="table-responsive crm-table-wrapper">
                <table class="crm-table">
                  <thead>
                    <tr>
                      <th style="min-width: 140px;" class="text-center">Thời gian tạo</th>
                      <th style="min-width: 130px;" class="text-center">Loại</th>
                      <th style="min-width: 250px;">Nội dung yêu cầu</th>
                      <th style="min-width: 140px;" class="text-center">Trạng thái</th>
                      <th style="min-width: 200px;">Ghi chú xử lý</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="!tickets || tickets.length === 0">
                      <td colspan="5" class="text-center py-4 text-gray-500">Chưa có ticket nào được tạo</td>
                    </tr>
                    <tr v-for="(item, index) in tickets" :key="index">
                      <td class="text-center font-mono text-xs">{{ item.created_at }}</td>
                      <td class="text-center">
                        <span class="badge-ticket-type">{{ item.type | genTypeTicket }}</span>
                      </td>
                      <td class="text-sm text-gray-800">{{ item.description }}</td>
                      <td class="text-center">
                        <select class="crm-cell-select" v-model="item.status" @change="updateTicket(item)">
                          <option value="1">Mới tạo</option>
                          <option value="2">Đã tiếp nhận</option>
                          <option value="3">Chờ xử lý</option>
                          <option value="4">Hoàn thành</option>
                          <option value="5">Hủy</option>
                        </select>
                      </td>
                      <td>
                        <textarea rows="2" class="crm-cell-textarea" v-model="item.note" @change="updateTicket(item)" placeholder="Nhập ghi chú xử lý..."></textarea>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </vs-tab>

          <!-- TAB 3: CHĂM SÓC -->
          <vs-tab label="Chăm sóc" @click="changeTab()">
            <div class="tab-content-wrapper p-2">
              <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-semibold text-gray-700">Lịch sử tương tác & Chăm sóc khách hàng</span>
                <vs-button :disabled="disabled_action" color="success" size="small" class="shadow-sm" @click="showModalCare">
                  <i class="fa fa-plus mr-1"></i> Thêm mới chăm sóc
                </vs-button>
              </div>

              <div class="table-responsive crm-table-wrapper">
                <table class="crm-table">
                  <thead>
                    <tr>
                      <th style="min-width: 140px;" class="text-center">Thời gian</th>
                      <th style="min-width: 130px;">Phụ trách</th>
                      <th style="min-width: 140px;" class="text-center">Phương thức</th>
                      <th style="min-width: 140px;" class="text-center">Trạng thái gọi</th>
                      <th style="min-width: 150px;" class="text-center">Lịch hẹn tiếp</th>
                      <th style="min-width: 250px;">Chi tiết nội dung</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="!cares || cares.length === 0">
                      <td colspan="6" class="text-center py-4 text-gray-500">Chưa có lịch sử chăm sóc nào</td>
                    </tr>
                    <tr v-for="(item, index) in cares" :key="index">
                      <td class="text-center font-mono text-xs">{{ item.care_date }}</td>
                      <td class="font-medium text-gray-900">{{ item.creator_name }}</td>
                      <td class="text-center">
                        <span class="badge-care-method">{{ item.method_name }}{{ item.type_call ? ' - ' + item.type_call : '' }}</span>
                      </td>
                      <td class="text-center text-xs font-semibold">{{ item.call_status_label || '---' }}</td>
                      <td class="text-center text-xs text-amber-600 font-medium">{{ item.next_care_date || '---' }}</td>
                      <td>
                        <div v-if="item.link_record" class="mb-1">
                          <audio controls class="crm-audio-player">
                            <source :src="item.link_record" type="audio/x-wav">
                          </audio>
                        </div>
                        <div class="text-sm text-gray-800" v-html="item.note"></div>
                        <div v-if="item.attached_file" class="mt-1">
                          <a :href="item.attached_file" target="_blank" class="text-primary text-xs underline font-medium">
                            <i class="fa fa-paperclip mr-1"></i>File đính kèm
                          </a>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </vs-tab>

          <!-- TAB 4: HỌC SINH -->
          <vs-tab label="Học sinh" @click="changeTab()">
            <div class="tab-content-wrapper p-2">
              <div class="flex items-center justify-between flex-wrap gap-2 mb-4 mt-1">
                <div>
                  <h5 class="font-bold text-gray-800 m-0 flex items-center">
                    <i class="fa fa-user-graduate text-primary mr-2" style="font-size: 20px;"></i>
                    Danh Sách Học Sinh ({{ students.length }})
                  </h5>
                  <span class="text-xs text-gray-500">Học sinh tạo từ CRM đã là chính thức, sẵn sàng tạo hợp đồng nhập học ngay</span>
                </div>
                <vs-button :disabled="disabled_action" color="success" class="flex items-center shadow-md" @click="showModalStudent(0)">
                  <i class="fa fa-plus mr-1.5"></i> Thêm mới học sinh
                </vs-button>
              </div>

              <!-- Empty state -->
              <div v-if="!students || students.length === 0" class="crm-empty-state">
                <i class="fa fa-user-graduate crm-empty-icon"></i>
                <h6 class="font-bold text-gray-700 mt-2">Chưa có thông tin học sinh</h6>
                <p class="text-sm text-gray-500 mb-3">Phụ huynh này chưa được thêm học sinh nào trong hệ thống.</p>
                <vs-button :disabled="disabled_action" color="primary" size="small" @click="showModalStudent(0)">
                  <i class="fa fa-plus mr-1"></i> Thêm học sinh ngay
                </vs-button>
              </div>

              <!-- Student Cards Grid -->
              <div v-else class="vx-row">
                <div class="vx-col lg:w-1/3 md:w-1/2 w-full mb-4" v-for="(item, index) in students" :key="index">
                  <div class="crm-student-card">
                    <!-- Card Header -->
                    <div class="crm-student-card-header">
                      <div class="crm-student-identity">
                        <img
                          class="crm-student-mini-avatar"
                          :src="item.gender === 'F' ? '/images/common/avatar-girl.svg' : '/images/common/avatar-boy.svg'"
                          :alt="item.name"
                        />
                        <div class="min-w-0">
                          <h6 class="crm-student-name truncate" :title="item.name">{{ item.name }}</h6>
                          <span v-if="item.lms_code" class="crm-student-lms-badge" :title="item.lms_code">
                            <i class="fa fa-id-badge mr-1"></i>{{ item.lms_code }}
                          </span>
                        </div>
                      </div>

                      <div class="flex items-center gap-1">
                        <button type="button" class="btn-icon-soft text-gray-600" :disabled="disabled_action" @click="showModalStudent(item)" title="Sửa thông tin học sinh">
                          <i class="fa fa-edit"></i>
                        </button>
                        <router-link v-if="item.lms_id" :to="`/lms/students/${item.lms_id}/detail`" target="_blank">
                          <button type="button" class="btn-icon-soft text-primary" title="Xem hồ sơ LMS chi tiết">
                            <i class="fa fa-eye"></i>
                          </button>
                        </router-link>
                      </div>
                    </div>

                    <!-- Card Body -->
                    <div class="crm-student-card-body">
                      <div class="crm-student-meta-row">
                        <span class="crm-meta-label"><i class="fa fa-calendar-alt text-primary mr-1"></i> Ngày sinh:</span>
                        <span class="crm-meta-val">{{ item.birthday || '---' }}</span>
                      </div>
                      <div class="crm-student-meta-row">
                        <span class="crm-meta-label"><i :class="item.gender === 'F' ? 'fa fa-venus text-pink-500' : 'fa fa-mars text-blue-500'" class="mr-1"></i> Giới tính:</span>
                        <span class="crm-meta-val">{{ item.gender | genTextGender }}</span>
                      </div>
                      <div class="crm-student-meta-row">
                        <span class="crm-meta-label"><i class="fa fa-building text-primary mr-1"></i> Cơ sở:</span>
                        <span class="crm-meta-val font-semibold text-gray-800">{{ item.checkin_branch_name || parent.branch_name || 'Chưa gán' }}</span>
                      </div>
                      <div class="crm-student-meta-row" v-if="item.note">
                        <span class="crm-meta-label"><i class="fa fa-sticky-note text-amber-500 mr-1"></i> Ghi chú:</span>
                        <span class="crm-meta-val text-gray-600 truncate" :title="item.note">{{ item.note }}</span>
                      </div>
                      <div class="crm-student-meta-row">
                        <span class="crm-meta-label"><i class="fa fa-check-circle text-emerald-500 mr-1"></i> Trạng thái:</span>
                        <span class="badge-status-pill badge-status-official">
                          {{ item.status | genStudentStatus }}
                        </span>
                      </div>
                    </div>

                    <!-- Card Footer: Quick Action -->
                    <div class="crm-student-card-footer">
                      <router-link v-if="item.lms_id" :to="`/lms/contracts/add?student_id=${item.lms_id}&branch_id=${item.checkin_branch_id || parent.branch_id || ''}`" target="_blank" class="w-full">
                        <vs-button color="primary" class="w-full flex items-center justify-center shadow-sm">
                          <i class="fa fa-file-signature mr-1.5"></i> Nhập học ngay (Tạo hợp đồng)
                        </vs-button>
                      </router-link>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </vs-tab>

          <!-- TAB 5: LỊCH SỬ CẬP NHẬT -->
          <vs-tab label="Lịch sử cập nhật" @click="changeTab()">
            <div class="tab-content-wrapper p-2">
              <div class="table-responsive crm-table-wrapper">
                <table class="crm-table">
                  <thead>
                    <tr>
                      <th style="min-width: 160px;" class="text-center">Thời gian</th>
                      <th style="min-width: 300px;">Nội dung cập nhật</th>
                      <th style="min-width: 160px;" class="text-center">Người thực hiện</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="!logs || logs.length === 0">
                      <td colspan="3" class="text-center py-4 text-gray-500">Chưa có lịch sử cập nhật nào</td>
                    </tr>
                    <tr v-for="(item, index) in logs" :key="index">
                      <td class="text-center font-mono text-xs">{{ item.created_at }}</td>
                      <td class="text-sm text-gray-800">{{ item.content }}</td>
                      <td class="text-center font-medium">{{ item.creator_name }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </vs-tab>

          <!-- TAB 6: VOUCHERS -->
          <vs-tab label="Vouchers" @click="changeTab()">
            <div class="tab-content-wrapper p-2">
              <div class="table-responsive crm-table-wrapper">
                <table class="crm-table">
                  <thead>
                    <tr>
                      <th style="min-width: 150px;" class="text-center">Mã voucher</th>
                      <th style="min-width: 150px;" class="text-right">Số tiền giảm trừ</th>
                      <th style="min-width: 140px;" class="text-center">Số buổi học bổng</th>
                      <th style="min-width: 140px;" class="text-center">Ngày hiệu lực</th>
                      <th style="min-width: 130px;" class="text-center">Trạng thái</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="!vouchers || vouchers.length === 0">
                      <td colspan="5" class="text-center py-4 text-gray-500">Chưa có voucher nào</td>
                    </tr>
                    <tr v-for="(item, index) in vouchers" :key="index">
                      <td class="text-center font-mono font-bold text-primary">{{ item.code }}</td>
                      <td class="text-right font-semibold text-emerald-600">{{ item.coupon_amount | formatMoney }}</td>
                      <td class="text-center font-medium">{{ item.coupon_session }} buổi</td>
                      <td class="text-center text-xs">{{ item.end_date | formatDateView }}</td>
                      <td class="text-center">
                        <span :class="item.status == 2 ? 'badge-status-pill badge-status-used' : 'badge-status-pill badge-status-official'">
                          {{ item.status == 2 ? 'Đã sử dụng' : 'Chưa sử dụng' }}
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </vs-tab>
        </vs-tabs>
      </vx-card>
      <vs-popup :class="'popup-care-lg modal_'+ modal_care.color" :title="modal_care.title" :active.sync="modal_care.show">
        <div class="vx-row"> 
          <div class="vx-col md:w-1/2 w-full mb-4">
            <label>Phương thức <span class="text-danger">(*)</span></label>
            <select class="vs-inputx vs-input--input normal" v-model="care.method_id" @change="onChangeCareMethod">
              <option value="">Chọn phương thức</option>
              <option
                :value="method.id"
                v-for="(method, index) in methods"
                :key="index"
              >{{method.name}}</option>
            </select>
          </div>
          <div class="vx-col md:w-1/2 w-full mb-4" v-if="Number(care.method_id) === 1">
            <label>Trạng thái cuộc gọi</label>
            <select class="vs-inputx vs-input--input normal" v-model="care.call_status">
              <option value="">Chọn trạng thái</option>
              <option
                v-for="option in statusOptionsCare"
                :key="option.id"
                :value="option.id"
              >{{ option.label }}</option>
            </select>
          </div>
          <div class="vx-col md:w-1/2 w-full mb-4">
            <label>Lịch chăm sóc tiếp theo</label>
            <input
              type="datetime-local"
              class="vs-inputx vs-input--input normal"
              v-model="care.next_care_date"
            >
          </div>
          <div class="vx-col md:w-1/2 w-full mb-4">
            <label>File đính kèm</label>
             <input
                type="file"
                class="vs-inputx vs-input--input normal"
                id="fileUploadExcel"
                @change="fileChanged"
              >
          </div>
          <div class="vx-col w-full mb-4">
            <label>Ghi chú <span class="text-danger">(*)</span></label>
            <textarea rows="4" class="vs-inputx vs-input--input normal" v-model="care.note" placeholder="Nhập nội dung chăm sóc"></textarea>
          </div>
          <vs-alert :active.sync="modal_care.alert.active" class="mb-5  mr-4 ml-4" :color="modal_care.alert.color" closable icon-pack="feather" close-icon="icon-x">
            <div v-html="modal_care.alert.body"></div>
          </vs-alert>
          <div class="vx-col w-full">
            <vs-button color="dark" type="border" class="mr-3" @click="modal_care.show = false">Hủy</vs-button>
            <vs-button color="success" @click="addCare">Lưu</vs-button>
          </div>
        </div>
      </vs-popup>
      <vs-popup :class="'modal_'+ modal_student.color" :title="modal_student.title" :active.sync="modal_student.show">
        <div class="vx-row"> 
          <div class="vx-col md:w-1/2 w-full mb-4">
            <label>Họ tên học sinh</label>
            <input class="vs-inputx vs-input--input normal" type="text" v-model="student.name">
          </div>
          <div class="vx-col md:w-1/2 w-full mb-4">
            <label>Ngày sinh</label>
            <datepicker
              class=" w-full calendar"
              v-model="student.birthday"
              placeholder="Chọn ngày sinh nhật"
              :lang="datepickerOptions.lang"
              @change="selectDate"
            />
          </div>
          <div class="vx-col md:w-1/2 w-full mb-4">
            <label>Giới tính</label>
            <select class="vs-inputx vs-input--input normal" v-model="student.gender">
              <option value="M">Nam</option>
              <option value="F">Nữ</option>
            </select>
          </div>
          <div class="vx-col md:w-1/2 w-full mb-4">
            <label>Cơ sở / Trung tâm</label>
            <select class="vs-inputx vs-input--input normal" v-model="student.branch_id">
              <option value="">-- Mặc định theo phụ huynh --</option>
              <option :value="item.id" v-for="(item, index) in branches" :key="index">{{item.name}}</option>
            </select>
          </div>
          
          <div class="vx-col w-full mb-4">
            <label>Ghi chú</label>
            <textarea class="vs-inputx vs-input--input normal" v-model="student.note"></textarea>
          </div>
          <vs-alert :active.sync="modal_student.alert.active" class="mb-5" :color="modal_student.alert.color" closable icon-pack="feather" close-icon="icon-x">
            <div v-html="modal_student.alert.body"></div>
          </vs-alert>
          <div class="vx-col w-full">
            <vs-button color="dark" type="border" class="mr-3" @click="modal_student.show = false">Hủy</vs-button>
            <vs-button color="success" @click="addStudent">Lưu</vs-button>
          </div>
        </div>
      </vs-popup>

      <vs-popup :class="'modal_'+ modal_checkin.color" :title="modal_checkin.title" :active.sync="modal_checkin.show">
        <div class="vx-row"> 
          <div class="vx-col md:w-1/2 w-full mb-4">
            <label>Trung tâm checkin</label>
            <select class="vs-inputx vs-input--input normal" v-model="modal_checkin.branch_id" :disabled="modal_checkin.disabled">
              <option value="">Chọn trung tâm</option>
              <option :value="item.id" v-for="(item, index) in branches" :key="index">{{item.name}}</option>
            </select>
          </div>
          <div class="vx-col md:w-1/2 w-full mb-4">
            <label>Ngày/Giờ Checkin</label>
            <input class="vs-inputx vs-input--input normal" type="text" v-model="modal_checkin.checkin_at" disabled>
            <!-- <datepicker
                      id="checkin-at"
                      class="form-control calendar"
                      :value="modal_checkin.checkin_at"
                      v-model="modal_checkin.checkin_at"
                      placeholder="Chọn ngày giờ"
                      :lang="datepickerOptions.lang"
                      type="datetime"
                      format="YYYY-MM-DD HH:mm"
                      :disabled="true"
              >
              </datepicker> -->
          </div>
          <div class="vx-col md:w-1/2 w-full mb-4">
            <label>Khóa học</label>
            <select class="vs-inputx vs-input--input normal" v-model="modal_checkin.type_product">
              <option value="">Chọn khóa học</option>
               <option
                :value="product.id"
                v-for="(product, index) in products"
                :key="index"
              >{{product.name}}</option>
            </select>
          </div>
          
          <vs-alert :active.sync="modal_checkin.alert.active" class="mb-5 mr-4 ml-4" :color="modal_checkin.alert.color" closable icon-pack="feather" close-icon="icon-x">
            <div v-html="modal_checkin.alert.body"></div>
          </vs-alert>
          <div class="vx-col w-full">
            <vs-button color="dark" type="border" class="mr-3" @click="modal_checkin.show = false">Hủy</vs-button>
            <vs-button color="success" @click="checkin">Lưu</vs-button>
          </div>
        </div>
      </vs-popup>
      <vs-popup :class="'modal_'+ modal_ticket.color" :title="modal_ticket.title" :active.sync="modal_ticket.show">
        <div class="vx-row"> 
          <div class="vx-col w-full mb-4">
            <label>Loại Ticket</label>
            <select class="vs-inputx vs-input--input normal" v-model="ticket.type">
              <option value="">Chọn loại</option>
              <option value="1">Yêu cầu dịch vụ</option>
              <option value="2">Yêu cầu hỗ trợ</option>
              <option value="3">Khiếu lại</option>
              <option value="4">Báo lỗi</option>
            </select>
          </div>
          <div class="vx-col w-full mb-4">
            <label>Nội dung</label>
            <textarea rows="8" class="vs-inputx vs-input--input normal" v-model="ticket.description"></textarea>
          </div>
          <vs-alert :active.sync="modal_ticket.alert.active" class="mb-5  mr-4 ml-4" :color="modal_ticket.alert.color" closable icon-pack="feather" close-icon="icon-x">
            <div v-html="modal_ticket.alert.body"></div>
          </vs-alert>
          <div class="vx-col w-full">
            <vs-button color="dark" type="border" class="mr-3" @click="modal_ticket.show = false">Hủy</vs-button>
            <vs-button color="success" @click="addTicket">Lưu</vs-button>
          </div>
        </div>
      </vs-popup>
      <vs-popup :class="'modal_'+ modal.color" :title="modal.title" :active.sync="modal.show">
        <div v-html="modal.body"></div>
      </vs-popup>
  </div>

</template>

<script>

  import Multiselect from "vue-multiselect";
  import axios from '../../../http/axios.js';
  import u from '../../../until/helper.js'
  import moment from 'moment';
  import datepicker from "vue2-datepicker";
  import select from 'vue-select'

  export default {
    components: {
      "vue-select": select,
      Multiselect,
      moment,
      datepicker
    },
    data() {
      return {
        datepickerOptions: {
          closed: true,
          value: "",
          minDate: "",
          lang: {
            days: ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
            months: [
              "Tháng 1",
              "Tháng 2",
              "Tháng 3",
              "Tháng 4",
              "Tháng 5",
              "Tháng 6",
              "Tháng 7",
              "Tháng 8",
              "Tháng 9",
              "Tháng 10",
              "Tháng 11",
              "Tháng 12"
            ]
          }
        },
        check_list: [],
        active_tab: 2,
        alert:{
          active: false,
          body: '',
          color:'',
        },
        html:{
          province: {
            item: '',
            list: []
          },
          district: {
            item: '',
            list: []
          },
          jobs: {
            item: '',
            list: []
          },
          source: {
            item: '',
            list: []
          },
          source_detail: {
            item: '',
            list: []
          },
        },
        branches:[],
        modal_ticket: {
          title: "THÊM MỚI TICKET",
          show: false,
          color: "info",
          closeOnBackdrop: false,
          size:"lg",
          error_message:"",
          alert:{
            active: false,
            body: '',
            color:'',
          },
        },
        modal_care: {
          title: "THÊM MỚI CHĂM SÓC",
          show: false,
          color: "info",
          closeOnBackdrop: false,
          size:"lg",
          error_message:"",
          alert:{
            active: false,
            body: '',
            color:'',
          },
        },
        modal_student: {
          title: "THÊM MỚI HỌC SINH",
          show: false,
          color: "info",
          closeOnBackdrop: false,
          size:"lg",
          error_message:"",
          alert:{
            active: false,
            body: '',
            color:'',
          },
        },
        modal_checkin: {
          title: "TẠO CHECKIN HỌC SINH",
          show: false,
          color: "info",
          closeOnBackdrop: false,
          size:"lg",
          error_message:"",
          branch_id:"",
          checkin_at:"",
          student_id:"",
          error_message:"",
          disabled:false,
          type_product:"",
          is_update:false,
          alert:{
            active: false,
            body: '',
            color:'',
          },
        },
        parent_input:{
          job:"",
          source:"",
          source_detail:"",
          province:"",
          district:"",
        },
        parent: {
          id:"",
          gender: "",
          name: "",
          birthday: "",
          mobile_1: "",
          mobile_2:"",
          note: "",
          email: "",
          status: 0,
          province_id:"",
          district_id:"",
          job_id:"",
          source_id:"",
          source_detail_id:"",
          address:"",
          owner_id:"",
          c2c_mobile:"",
        },
        activeItem: 'customer_care',
        methods:[],
        total_unsuccess:0,
        tickets:[],
        ticket:{
          parent_id:"",
          type:"",
          description:"",
          note:"",
          status:"",
        },
        cares:[],
        care:{
          method_id:1,
          call_status:"",
          next_care_date:"",
          care_date:"",
          note:"",
          parent_id:"",
          attached_file:"",
          file_name:"",
        },
        students:[],
        student:{
          id:0,
          parent_id:"",
          branch_id:"",
          name:"",
          gender:"",
          school_level:"",
          birthday:"",
          select_school:"",
          note:"",
        },
        logs:[],
        vouchers:[],
        users_manager:[],
        tmp_owner_id:"",
        tmp_status:"",
        tmp_level:"",
        tmp_source_detail_id:"",
        phone:{
          css_class: 'alert alert-success',
          show: false,
          title:'Đang thực hiện cuộc gọi đi',
          status:0,
          description:'',
          show_input_note:false,
          care_id:'',
          note:'',
          select_note:'',
          select_note_status:'',
          select_note_status_sub:'',
          next_care_date:'',
          alert:{
            active: false,
            body: '',
            color:'',
          },
        },
        sms:{
          content:'',
          show:false,
          title:'Gửi tin nhắn SMS',
          phone:''
        },
        template_note:[],
        disabled_action:false,
        disabled_edit:true,
        c2c_info:"",
        products:[],
        modal: {
          title: "THÔNG BÁO",
          show: false,
          color: "success",
          body: "Thêm mới lớp học thành công",
          action_exit: "exit",
        },
        levelOptionsParent: u.levelOptionsParent,
        statusOptionsCare: u.statusOptionsCare
      };
    },
    async created() {
      axios.g(`/api/system/products`)
        .then(response => {
        this.products = response.data
      })
      axios.g(`/api/system/methods`)
        .then(response => {
        this.methods = response.data
      })
      axios.g(`/api/system/branches-has-user`)
        .then(response => {
        this.branches = response.data
      })
      axios.g(`/api/users/get-data/users-manager`)
      .then(response => {
        this.users_manager = response.data
      })
      axios.g(`/api/system/provinces`)
        .then(response => {
        this.html.province.list = response.data
      })
      axios.g(`/api/system/jobs`)
        .then(response => {
        this.html.jobs.list = response.data
      })
      await axios.g(`/api/system/sources`)
        .then(response => {
        this.html.source.list = response.data
      })
      this.loadDetail();
      this.loadCares();
      this.loadTickets();
      this.loadStudents();
    },
    methods: {
      localeDataSourceDetail(data=null){
        if (data && typeof data === 'object') {
          const source_id = data.id
          this.parent.source = data
          this.parent.source_id = source_id
          this.$vs.loading()
          axios.g(`/api/system/source_detail?source_id=${this.parent.source_id}`).then(response => {
            this.$vs.loading.close();
            this.html.source_detail.list = response.data
            if(this.tmp_source_detail_id){
              this.parent_input.source_detail = this.html.source_detail.list.filter(item => item.id == this.tmp_source_detail_id)[0]
            }else{
              this.parent.source_detail_id = ""
              this.parent_input.source_detail = ""
            } 
          })
        }else{
          this.html.source_detail.list = []
          this.parent_input.source_detail = ""
          this.parent.source_detail_id = ""
        }
      },
      loadDetail(){
        this.$vs.loading();
        axios.g(`/api/crm/parents/show/${this.$route.params.id}`)
          .then(response => {
          this.$vs.loading.close();
          if(response.data.length !== 0){
            this.parent = response.data
            this.parent_input.job = this.html.jobs.list.filter(item => item.id == response.data.job_id)[0]
            this.parent_input.source = this.html.source.list.filter(item => item.id == response.data.source_id)[0]
            this.parent_input.province = this.html.province.list.filter(item => item.id == response.data.province_id)[0]
            this.tmp_district_id = response.data.district_id
            this.tmp_owner_id = response.data.owner_id
            this.tmp_status = response.data.status
            this.tmp_level  = response.data.level
            this.tmp_source_detail_id = response.data.source_detail_id
            this.getDistrict(this.parent_input.province);
            this.localeDataSourceDetail(this.parent_input.source)
          }else{
            this.$router.push({ path: `/crm/parent` });
          }
        })
      },
      updateNextCareDate(){
          const data = {
            parent_id: this.$route.params.id,
            next_care_date: document.getElementById('next_care_date').value
          };
          this.$vs.loading();;
          axios.p(`/api/parents/update_next_care_date`,data)
          .then((response) => {
            this.loadDetail();
          })
          .catch((e) => {
          });
      },
      showModalAssgin(){
        this.modal_assign.show =true
      },
      showModalChangeStatus(){
        this.modal_status.show =true
      },
      validatePhone(){
        if(this.parent.mobile_1){
          const data = {
            phone: this.parent.mobile_1,
            parent_id: this.parent.id,
          };
          this.$vs.loading();
          axios.p(`/api/crm/parents/validate_phone`,data).then(response => {
            this.$vs.loading.close();
            if(response.data.status==0){
              this.parent.mobile_1 ="";
              this.modal.color = "warning";
              this.modal.body = response.data.message;
              this.modal.show = true;
            }
          })
        }
      },
      validatePhone2(){
        if(this.parent.mobile_2){
          const data = {
            phone: this.parent.mobile_2,
            parent_id: this.parent.id,
          };
          this.$vs.loading();
          axios.p(`/api/crm/parents/validate_phone`,data).then(response => {
            this.$vs.loading.close();
            if(response.data.status==0){
              this.parent.mobile_2 ="";
              this.modal.color = "warning";
              this.modal.body = response.data.message;
              this.modal.show = true;
            }else if(response.data.status==2){
              this.modal_overwrite.show = true;
              this.modal_overwrite.message = response.data.message;
            }
          })
        }
      },
      validatePhoneC2C(){
        this.c2c_info=""
        if(this.parent.c2c_mobile){
          const data = {
            phone: this.parent.c2c_mobile,
          };
          this.$vs.loading();
          axios.p(`/api/crm/parents/validate_c2c_phone`,data).then(response => {
            this.$vs.loading.close();
            if(response.data.status==0){
              this.parent.c2c_mobile ="";
              this.modal.color = "warning";
              this.modal.body = response.data.message;
              this.modal.show = true;
            }else{
              this.c2c_info = response.data.message
            }
          })
        }
      },
      selectDateParent(){
        if (date) {
          this.parent.birthday = moment(date).format("YYYY-MM-DD");
        }
      },
      selectDate(date) {
        if (date) {
          this.student.birthday = moment(date).format("YYYY-MM-DD");
        }
      },
      getDistrict(data = null){
        if (data && typeof data === 'object') {
          const province_id = data.id
          this.parent.province = data
          this.parent.province_id = province_id
          this.$vs.loading()
          axios.g(`/api/system/provinces/${province_id}/districts`).then(response => {
            this.$vs.loading.close();
            this.html.district.list = response.data
            if(this.tmp_district_id){
              this.parent_input.district = this.html.district.list.filter(item => item.id == this.tmp_district_id)[0]
            }else{
              this.parent.district_id = ""
              this.parent_input.district = ""
            } 
          })
        }else{
          this.parent.province = ""
          this.parent.province_id = ""
          this.html.district.list = []
          this.parent.district = ""
          this.parent.district_id = ""
        }
      },
      saveDistrict(data = null){
        if (data && typeof data === 'object') {
          const district_id = data.id
          this.parent_input.district = data
          this.parent.district_id = district_id
        }else{
          this.parent_input.district = ""
          this.parent.district_id = ""
        }
      },
      saveJob(data = null){
        if (data && typeof data === 'object') {
          const job_id = data.id
          this.parent_input.job = data
          this.parent.job_id = job_id
        }else{
          this.parent_input.job = ""
          this.parent.job_id = ""
        }
      },
      saveSourceDetail(data = null){
        if (data && typeof data === 'object') {
          const source_id = data.id
          this.parent_input.source_detail = data
          this.parent.source_detail_id = source_id
        }else{
          this.parent_input.source_detail = ""
          this.parent.source_detail_id = ""
        }
      },
      reloadPage(){
        location.reload();
      },
      saveInfo(){
        let mess = "";
        let resp = true;
        if (this.parent.gender == "") {
          mess += " - Danh xưng không được để trống<br/>";
          resp = false;
        }
        if (this.parent.name == "") {
          mess += " - Họ tên không được để trống<br/>";
          resp = false;
        }
        if (this.parent.mobile_1 == "") {
          mess += " - Số điện thoại không được để trống<br/>";
          resp = false;
        }
        if (this.parent.mobile_1 != "" && !u.vld.phone(this.parent.mobile_1)) {
          mess += " - Số điện thoại không đúng định dạng<br/>";
          resp = false;
        }
        if (this.parent.mobile_2 != null && this.parent.mobile_2 != "" && !u.vld.phone(this.parent.mobile_2)) {
          mess += " - Số điện thoại 2 không đúng định dạng<br/>";
          resp = false;
        }
        if (this.parent.source_id == "") {
          mess += " - Nguồn không được để trống<br/>";
          resp = false;
        }
        if (!resp) {
          this.alert.color = 'danger'
          this.alert.body = mess;
          this.alert.active = true;
          return false;
        }
        this.$vs.loading()
        axios.p("/api/crm/parents/update",this.parent)
        .then((response) => {
          this.$vs.loading.close();;
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          this.disabled_edit = true
        })
        .catch((e) => {
          console.log(e);
          this.$vs.loading.close();
        });
      },
      openConfirmChangeLevel () {
        this.$vs.dialog({
          type: 'confirm',
          color: 'danger',
          title: 'Thông báo',
          text: `Bạn chắc chắn muốn cập nhật level của khách hàng?`,
          accept: this.changeLevel,
          cancel: this.cancelChangeLevel,
          acceptText: 'Cập nhật',
          cancelText: 'Hủy'
        })
      },
      cancelChangeLevel(){
        this.tmp_level = this.parent.level
      },
      changeLevel(){
        const data = {
          parent_id: this.parent.id,
          level: this.tmp_level,
        };
        this.$vs.loading();
        axios.p(`/api/crm/parents/change_level`,data)
        .then((response) => {
          this.$vs.loading.close();
          this.loadDetail();
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
        })
        .catch((e) => {
        });
      },
      openConfirmChangeStatus () {
        this.$vs.dialog({
          type: 'confirm',
          color: 'danger',
          title: 'Thông báo',
          text: `Bạn chắc chắn muốn cập nhật trạng thái của khách hàng?`,
          accept: this.changeStatus,
          cancel: this.cancelChangeStaus,
          acceptText: 'Cập nhật',
          cancelText: 'Hủy'
        })
      },
      cancelChangeStaus(){
        this.tmp_status = this.parent.status
      },
      changeStatus(){
        const data = {
          parent_id: this.parent.id,
          status: this.tmp_status,
        };
        this.$vs.loading();
        axios.p(`/api/crm/parents/change_status`,data)
        .then((response) => {
          this.$vs.loading.close();
          this.loadDetail();
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
        })
        .catch((e) => {
        });
      },
      openConfirmAssgin () {
        this.$vs.dialog({
          type: 'confirm',
          color: 'danger',
          title: 'Thông báo',
          text: `Bạn chắc chắn muốn thay đổi người phụ trách của khách hàng?`,
          accept: this.changeAssgin,
          cancel: this.cancelChangeAssgin,
          acceptText: 'Cập nhật',
          cancelText: 'Hủy'
        })
      },
      cancelChangeAssgin(){
        this.tmp_owner_id = this.parent.owner_id
      },
      changeAssgin(){
        const data = {
          parent_id: this.parent.id,
          owner_id: this.tmp_owner_id,
        };
        this.$vs.loading();
        axios.p(`/api/crm/parents/assign`,data)
        .then((response) => {
          this.$vs.loading.close();
          this.loadDetail();
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
        })
      },
      updateNextCareDate(){
        const data = {
          parent_id: this.$route.params.id,
          next_care_date: document.getElementById('next_care_date').value
        };
        this.$vs.loading();
        axios.p(`/api/crm/parents/update_next_care_date`,data)
        .then((response) => {
          this.$vs.loading.close();
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
        })
        .catch((e) => {
        });
      },
      changeTab(){
        if(this.active_tab ==1){
          this.loadTickets();
        }else if(this.active_tab ==2){
          this.loadCares();
        }else if(this.active_tab ==3){
          this.loadStudents();
        }else if(this.active_tab ==4){
          this.loadLogs();
        }else if(this.active_tab ==5){
          this.loadVouchers();
        }else{
          this.loadDetail();
        }
      },
      loadLogs(){
        this.$vs.loading();
        axios.g(`/api/crm/parents/get_logs/${this.parent.id}`)
        .then((response) => {
          this.$vs.loading.close();
          this.logs=response.data;
        })
        .catch((e) => {
        });
      },
      loadVouchers(){
        this.$vs.loading();
        axios.g(`/api/crm/parents/get_vouchers/${this.parent.id}`)
        .then((response) => {
          this.$vs.loading.close();
          this.vouchers=response.data;
        })
        .catch((e) => {
        });
      },
      showModalTicket(){
        this.modal_ticket.show =true
        this.modal_ticket.error_message=""
        this.ticket.parent_id=""
        this.ticket.type=""
        this.ticket.description=""
        this.ticket.note=""
        this.ticket.status=""
      },
      loadTickets(){
        this.$vs.loading();
          axios.g(`/api/crm/ticket/get_all_data/${this.$route.params.id}`)
          .then((response) => {
            this.$vs.loading.close();
            this.tickets=response.data.list;
            this.total_unsuccess = response.data.total_unsuccess
          })
          .catch((e) => {
          });
      },
      addTicket(){
        this.ticket.parent_id = this.parent.id
        let mess = "";
        let resp = true;
        if (this.ticket.type == "") {
          mess += " - Loại ticket không được để trống<br/>";
          resp = false;
        }
        if (this.ticket.description == "") {
          mess += " - Nội dung không được để trống<br/>";
          resp = false;
        }
        
        if (!resp) {
          this.modal_ticket.alert.color = 'danger'
          this.modal_ticket.alert.body = mess;
          this.modal_ticket.alert.active = true;
          return false;
        }
        this.$vs.loading();
        this.modal_ticket.show = false
        axios.p(`/api/crm/ticket/add`,this.ticket)
        .then((response) => {
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          this.$vs.loading.close();
          this.loadTickets();
        })
        .catch((e) => {
        });
      },
      updateTicket(data){
        this.$vs.loading();
        axios.p(`/api/crm/ticket/update`,data)
        .then((response) => {
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          this.$vs.loading.close();
          this.loadTickets();
        })
        .catch((e) => {
        });
      },
      showModalCare(){
        this.modal_care.show = true
        this.modal_care.error_message=""
        this.modal_care.alert.active = false
        this.modal_care.alert.body = ""
        this.care.method_id = 1
        this.care.call_status = ""
        this.care.next_care_date = ""
        this.care.note = ""
        this.care.attached_file = ""
        this.care.file_name = ""
        const fileEl = document.getElementById('fileUploadExcel')
        if (fileEl) {
          fileEl.value = ""
        }
      },
      onChangeCareMethod(){
        if (Number(this.care.method_id) !== 1) {
          this.care.call_status = ""
        }
      },
      loadCares(){
        this.$vs.loading();
          axios.g(`/api/crm/care/get_all_data/${this.$route.params.id}`)
          .then((response) => {
            this.$vs.loading.close();
            this.cares=response.data;
          })
          .catch((e) => {
          });
      },
      addCare(){
        this.care.parent_id = this.parent.id
        if (Number(this.care.method_id) !== 1) {
          this.care.call_status = ""
        }
        let mess = "";
        let resp = true;
        if (this.care.method_id == "") {
          mess += " - Phương thức chăm sóc không được để trống<br/>";
          resp = false;
        }
        if (this.care.note == "") {
          mess += " - Nội dung chăm sóc không được để trống<br/>";
          resp = false;
        }
        if (!resp) {
          this.modal_care.alert.color = 'danger'
          this.modal_care.alert.body = mess;
          this.modal_care.alert.active = true;
          return false;
        }
        this.$vs.loading();
        this.modal_care.show = false
        axios.p(`/api/crm/care/add`,this.care)
        .then((response) => {
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          this.$vs.loading.close();
          this.loadCares();
          this.loadDetail();
        })
        .catch((e) => {
          this.$vs.loading.close();
        });
      },
      fileChanged(e) {
        const fileReader = new FileReader();
        const fileName = e.target.value.split("\\").pop();
        this.care.file_name = fileName
        fileReader.readAsDataURL(e.target.files[0]);
        fileReader.onload = e => {
          this.care.attached_file = e.target.result;
        };
      },
      showSendSms(phone){
        this.sms.show =true
        this.sms.phone =phone
        this.sms.content = ''
        this.sms.title = 'Gửi tin nhắn SMS tới SĐT - '+phone
      },
      sendSms(){
        if(this.sms.content){
          const data = {
            parent_id: this.$route.params.id,
            content: this.sms.content,
            phone: this.sms.phone,
          };
          this.$vs.loading();
          axios.p(`/api/crm/parents/send_sms`,data)
          .then((response) => {
            this.$vs.loading.close();
            this.sms.show = false;
            this.$vs.notify({
              title: 'Thành Công',
              text: response.data.message,
              color: 'success',
              iconPack: 'feather',
              icon: 'icon-check'
            })
            this.loadCares();
          })
          .catch((e) => {
          });
        }
      },
      callPhone(phone){
        this.phone.show = true
        this.phone.status = 0
        this.phone.select_note = ""
        this.phone.show_input_note = false
        this.phone.css_class= 'alert alert-success'
        this.phone.title = "Đang thực hiện cuộc gọi đi đến SĐT - "+phone+" ..."
        this.phone.care_id = ''
        this.phone.note=''
        this.phone.select_note_status='',
        this.phone.select_note_status_sub='',
        this.phone.next_care_date='',
        this.phone.alert.body=''
        this.phone.alert.color=''
      },
      updateNotePhone(){
        let mess = "";
        let resp = true;
        
        this.phone.alert.body=''
        this.phone.alert.color=''
        if (this.phone.select_note_status == "") {
          mess += " - Trạng thái cuộc gọi không được để trống<br/>";
          resp = false;
        }
        
        if (this.phone.note == "") {
          mess += " - Ghi chú cuộc gọi không được để trống<br/>";
          resp = false;
        }
        if (!resp) {
          this.phone.alert.color = 'danger'
          this.phone.alert.body = mess;
          this.phone.alert.active = true;
          return false;
        }

        const data = {
          method_id:1,
          note:this.phone.note,
          parent_id:this.parent.id,
          attached_file:"",
          file_name:"",
          care_date:"",
          call_status_sub:this.phone.select_note_status_sub,
          call_status:this.phone.select_note_status
        };
        this.$vs.loading();
        axios.p(`/api/crm/care/add`,data)
          .then((response) => {
            this.$vs.loading.close();
            this.phone.show = false;
            this.$vs.notify({
              title: 'Thành Công',
              text: response.data.message,
              color: 'success',
              iconPack: 'feather',
              icon: 'icon-check'
            })
            this.loadCares();
          })
          .catch((e) => {
          });
      },
      showModalStudent(data){
        if(data==0){
          this.modal_student.show = true
          this.modal_student.error_message=""
          this.modal_student.title ="THÊM MỚI HỌC SINH"
          this.student.id =0
          this.student.parent_id = this.parent.id || ""
          this.student.branch_id = this.parent.branch_id || (this.branches && this.branches.length ? this.branches[0].id : "")
          this.student.name =""
          this.student.gender ="M"
          this.student.school_level =""
          this.student.birthday =""
          this.student.select_school =""
          this.student.note =""
        }else{
          console.log(data)
          this.modal_student.show = true
          this.modal_student.error_message=""
          this.modal_student.title ="CẬP NHẬT THÔNG TIN HỌC SINH"
          this.student.id =data.id
          this.student.parent_id =data.parent_id
          this.student.branch_id = data.checkin_branch_id || this.parent.branch_id || ""
          this.student.name =data.name
          this.student.gender =data.gender
          this.student.school_level =data.school_level
          this.student.birthday =data.birthday
          this.student.select_school =data.school
          this.student.school = data.school
          this.student.note =data.note
          this.getSchools()
        }
      },
      showModalCheckin(item){
        this.modal_checkin.show =true
        this.modal_checkin.student_id = item.id
        this.modal_checkin.branch_id = this.parent.branch_id
        this.modal_checkin.checkin_at = moment().format('YYYY-MM-DD HH:mm');
        this.modal_checkin.error_message=""
        this.modal_checkin.type_product=""
      },
      checkin(){
        let mess = "";
        let resp = true;
        if (this.modal_checkin.branch_id == "") {
          mess += " - Trung tâm checkin không được để trống<br/>";
          resp = false;
        }
        if (this.modal_checkin.checkin_at == "") {
          mess += " - Thời gian checkin không được để trống<br/>";
          resp = false;
        }
        if (this.modal_checkin.type_product == "") {
          mess += " - Khóa học không được để trống<br/>";
          resp = false;
        }
        if (!resp) {
          this.modal_checkin.alert.color = 'danger'
          this.modal_checkin.alert.body = mess;
          this.modal_checkin.alert.active = true;
          return false;
        }
        const data = {
          owner_id : this.parent.owner_id,
          student_id: this.modal_checkin.student_id,
          branch_id: this.modal_checkin.branch_id,
          checkin_at: moment(this.modal_checkin.checkin_at).format('YYYY-MM-DD HH:mm'),
          type_product: this.modal_checkin.type_product,
          is_update: this.modal_checkin.is_update ? 1 : 0,
        };
        this.$vs.loading();
        axios.p(`/api/crm/students/checkin`,data)
        .then((response) => {
          this.$vs.loading.close();
          this.modal_checkin.show =false
          this.$vs.notify({
            title: 'Thành Công',
            text: response.data.message,
            color: 'success',
            iconPack: 'feather',
            icon: 'icon-check'
          })
          this.loadStudents();
        })
        .catch((e) => {
        });
      },
      showModalUpdateCheckin(item){
        this.modal_checkin.show =true
        this.modal_checkin.is_update =true
        this.modal_checkin.student_id = item.id
        this.modal_checkin.branch_id = item.checkin_branch_id
        this.modal_checkin.checkin_at =item.checkin_at;
        this.modal_checkin.type_product = item.type_product
        this.modal_checkin.error_message=""
      },
      addStudent(){
        this.student.parent_id = this.parent.id
        this.student.owner_id = this.parent.owner_id
        if (!this.student.branch_id) {
          this.student.branch_id = this.parent.branch_id || ""
        }
        let mess = "";
        let resp = true;
        if (this.student.name == "") {
          mess += " - Tên học sinh không được để trống<br/>";
          resp = false;
        }
        if (this.student.birthday == "") {
          mess += " - Ngày sinh không được để trống<br/>";
          resp = false;
        }
        if (this.student.gender == "") {
          mess += " - Giới tính không được để trống<br/>";
          resp = false;
        }
        if (!resp) {
          this.modal_student.alert.color = 'danger'
          this.modal_student.alert.body = mess;
          this.modal_student.alert.active = true;
          return false;
        }
        this.$vs.loading();
        axios.p(`/api/crm/students/add`,this.student)
        .then((response) => {
          this.modal_student.show = false
          this.$vs.loading.close();
          if(response.data.status){
            this.$vs.notify({
              title: 'Thành Công',
              text: response.data.message,
              color: 'success',
              iconPack: 'feather',
              icon: 'icon-check'
            })
          }else{
            this.$vs.notify({
              title: 'Thông báo',
              text: response.data.message,
              color: 'danger',
              iconPack: 'feather',
              icon: 'icon-check'
            })
          }
          
          this.loadStudents();
        })
        .catch((e) => {
        });
      },
      loadStudents(){
        this.$vs.loading();
        axios.g(`/api/crm/students/get_all_data/${this.$route.params.id}`)
        .then((response) => {
          this.students = response.data;
          this.$vs.loading.close();
        })
        .catch((e) => {
        });
      },
      submitFiles() {
        if(this.$refs.file.files.length){
          this.$vs.loading()
          const formData = new FormData();
          for (var i = 0; i < this.$refs.file.files.length; i++) {
            let file = this.$refs.file.files[i];
            formData.append('files[' + i + ']', file);
          }
          formData.append('parent_id', this.$route.params.id);
          axios.p('/api/crm/parents/upload-avatar', formData, {
              headers: {
                'Content-Type': 'multipart/form-data'
              },
            }).then((response) => {  
              this.loadDetail();
            })
          .catch((error)   => { console.log(error); this.$vs.loading.close(); })
        }
      },
    },
    filters: {
      genTextGender(item){
        let resp = ''
        if(item== 'M'){
          resp = 'Nam'
        }else{
          resp = 'Nữ'
        }
        return resp
      },
      genStudentStatus(item){
        let resp = ''
        if(item== 0){
          resp = 'Mới tạo'
        }else if(item==1){
          resp = 'Thêm mới checkin'
        }else if(item==2){
          resp = 'Đã đến checkin'
        }else if(item==3){
          resp = 'Đã lên chính thức'
        }else if(item==4){
          resp = 'Đã kết thúc'
        }
        return resp
      },
      genTypeTicket(item){
        let resp = ''
        if(item== 1){
          resp = 'Yêu cầu dịch vụ'
        }else if(item== 2){
          resp = 'Yêu cầu hỗ trợ'
        }else if(item== 2){
          resp = 'Khiếu lại'
        }else{
          resp = 'Báo lỗi'
        }
        return resp
      },
    },
  }
</script>
<style>
.popup-care-lg.con-vs-popup .vs-popup {
  width: 850px !important;
  max-width: 95vw !important;
}

/* ==========================================================================
   CRM Parent Detail Modern UI Styles
   ========================================================================== */

/* Hero Header Card */
.crm-hero-card {
  background: #ffffff;
  border-radius: 14px;
  box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.05);
  border: 1px solid #edf2f7;
  padding: 1.25rem 1.5rem;
  transition: all 0.3s ease;
}

.crm-hero-grid {
  display: flex;
  flex-wrap: wrap;
  align-items: stretch;
  gap: 1.25rem;
}

/* Hero Profile */
.crm-hero-profile {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  flex: 1 1 360px;
  min-width: 280px;
}

.crm-avatar-box {
  position: relative;
  width: 86px;
  height: 86px;
  border-radius: 50%;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(115, 103, 240, 0.2);
  border: 3px solid #7367f0;
  flex-shrink: 0;
  background: #f8fafc;
}

.crm-avatar-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.crm-avatar-upload-btn {
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  cursor: pointer;
  transition: opacity 0.2s ease;
}

.crm-avatar-box:hover .crm-avatar-upload-btn {
  opacity: 1;
}

/* Badges */
.badge-parent-gender {
  display: inline-flex;
  align-items: center;
  padding: 2px 8px;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
}

.badge-parent-gender.badge-male {
  background: #e0f2fe;
  color: #0284c7;
}

.badge-parent-gender.badge-female {
  background: #fce7f3;
  color: #db2777;
}

.badge-parent-id {
  display: inline-flex;
  align-items: center;
  padding: 2px 8px;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
  background: #f1f5f9;
  color: #64748b;
}

/* Phone Pills */
.crm-phone-chips {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.crm-phone-pill {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  padding: 3px 10px;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 600;
  color: #334155;
}

.crm-phone-actions {
  display: flex;
  align-items: center;
  gap: 4px;
}

.btn-circle-action {
  width: 26px;
  height: 26px;
  border-radius: 50%;
  border: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-call {
  background: #dcfce7;
  color: #16a34a;
}
.btn-call:hover {
  background: #16a34a;
  color: #ffffff;
}

.btn-sms {
  background: #e0f2fe;
  color: #0284c7;
}
.btn-sms:hover {
  background: #0284c7;
  color: #ffffff;
}

/* Hero KPIs */
.crm-hero-kpis {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  flex: 1 1 320px;
  min-width: 260px;
}

.crm-kpi-item {
  flex: 1;
  display: flex;
  align-items: center;
  gap: 0.65rem;
  padding: 0.65rem 0.85rem;
  background: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: 10px;
}

.crm-kpi-icon {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.crm-kpi-text {
  display: flex;
  flex-direction: column;
}

.crm-kpi-next-care {
  flex-direction: column;
  align-items: flex-start;
  gap: 0.35rem;
}

.crm-next-care-input {
  width: 100%;
}
.crm-next-care-input input {
  padding: 4px 8px !important;
  font-size: 0.8rem !important;
  border-radius: 6px !important;
  border: 1px solid #cbd5e1 !important;
}

/* Hero Controls */
.crm-hero-controls {
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 0.65rem;
  flex: 0 1 240px;
  min-width: 200px;
}

.crm-control-box {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.crm-control-select {
  width: 100%;
  border-radius: 8px;
  border: 1px solid #cbd5e1;
  padding: 6px 10px;
  font-size: 0.85rem;
  background-color: #f8fafc;
  color: #334155;
  outline: none;
  cursor: pointer;
  transition: border-color 0.2s ease;
}

.crm-control-select:focus {
  border-color: #7367f0;
  background-color: #ffffff;
}

/* Tabs Card */
.crm-tabs-card {
  border-radius: 14px;
  box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.05);
  border: 1px solid #edf2f7;
  overflow: hidden;
}

.crm-form-section {
  background: #f8fafc;
  border: 1px solid #edf2f7;
  border-radius: 10px;
  padding: 1.25rem;
}

.crm-section-title {
  font-size: 0.95rem;
  font-weight: 700;
  color: #334155;
  display: flex;
  align-items: center;
  margin-bottom: 1rem;
  border-bottom: 1px dashed #e2e8f0;
  padding-bottom: 0.5rem;
}

.crm-field-label {
  display: block;
  font-size: 0.8rem;
  font-weight: 600;
  color: #64748b;
  margin-bottom: 0.35rem;
}

/* Student Cards (Tab 4) */
.crm-student-card {
  background: #ffffff;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  transition: all 0.25s ease;
}

.crm-student-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
  border-color: #cbd5e1;
}

.crm-student-card-header {
  padding: 1rem;
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  border-bottom: 1px solid #f1f5f9;
  background: #fcfcfd;
}

.crm-student-name {
  font-size: 1rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 0.25rem;
}

.crm-student-lms-badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  background: #eef2ff;
  color: #4f46e5;
  font-size: 0.75rem;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 6px;
  border: 1px solid #c7d2fe;
}

.crm-student-card-body {
  padding: 1rem;
  flex: 1;
}

.crm-student-meta-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 0.85rem;
  padding: 0.35rem 0;
  color: #475569;
  border-bottom: 1px dashed #f1f5f9;
}
.crm-student-meta-row:last-child {
  border-bottom: none;
}

.crm-student-card-footer {
  padding: 0.85rem 1rem;
  background: #f8fafc;
  border-top: 1px solid #f1f5f9;
}

/* Table Responsive & Badges */
.table-responsive {
  width: 100%;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

.crm-table-wrapper {
  background: #ffffff;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  overflow: hidden;
}

.crm-table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
}

.crm-table th {
  background: #f8fafc;
  color: #475569;
  font-size: 0.78rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  padding: 10px 14px;
  border-bottom: 1px solid #e2e8f0;
  white-space: nowrap;
}

.crm-table td {
  padding: 12px 14px;
  font-size: 0.85rem;
  color: #334155;
  border-bottom: 1px solid #f1f5f9;
}

.crm-table tr:hover td {
  background: #fbfcfe;
}

.badge-status-pill {
  display: inline-flex;
  align-items: center;
  padding: 3px 10px;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
}

.badge-status-official {
  background: #dcfce7;
  color: #15803d;
}

.crm-empty-state {
  text-align: center;
  padding: 2.5rem 1rem;
  background: #f8fafc;
  border-radius: 10px;
  border: 1px dashed #cbd5e1;
}

/* Responsive adjustments */
@media (max-width: 992px) {
  .crm-hero-grid {
    flex-direction: column;
  }
  .crm-hero-controls {
    flex: 1 1 100%;
  }
}
</style>