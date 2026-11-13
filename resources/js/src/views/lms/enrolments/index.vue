<template>
  <div id="page-enrolments-list" class="enrol-page mt-5">
    <div class="enrol-layout">
      <!-- ============ SIDEBAR: BỘ LỌC + CÂY LỚP ============ -->
      <aside class="enrol-sidebar">
        <div class="enrol-card">
          <div class="enrol-card__title"><i class="fa-solid fa-filter mr-2"></i>Chọn lớp học</div>

          <div class="enrol-field">
            <label>Trung tâm <span class="text-danger">(*)</span></label>
            <vue-select
              label="name"
              placeholder="Chọn trung tâm"
              :options="html.branches.list"
              v-model="html.branches.item"
              :searchable="true"
              language="tv-VN"
              @input="saveBranch"
            ></vue-select>
          </div>

          <div class="enrol-field">
            <label>Khóa học <span class="text-danger">(*)</span></label>
            <vue-select
              label="name"
              placeholder="Chọn khóa học"
              :options="html.products.list"
              v-model="html.products.item"
              :searchable="true"
              language="tv-VN"
              @input="saveProduct"
            ></vue-select>
          </div>

          <div class="enrol-field">
            <label>Trạng thái lớp</label>
            <div class="enrol-segment">
              <button
                v-for="opt in typeOptions"
                :key="opt.value"
                type="button"
                :class="{ active: select_type == opt.value }"
                @click="changeType(opt.value)"
              >{{ opt.label }}</button>
            </div>
          </div>

          <div class="enrol-field mb-0">
            <div class="enrol-search">
              <i class="fa-solid fa-magnifying-glass"></i>
              <input
                type="text"
                v-model="classKeyword"
                placeholder="Tìm nhanh tên lớp..."
                :disabled="!classes.length"
              />
            </div>
          </div>
        </div>

        <div class="enrol-card enrol-tree-card">
          <div class="enrol-tree-head">
            <span>Danh sách lớp</span>
            <span class="enrol-pill">{{ totalClasses }} lớp</span>
          </div>

          <div v-if="!enrol.branch_id || !enrol.product_id" class="enrol-empty-sm">
            <i class="fa-solid fa-hand-pointer"></i>
            Chọn trung tâm và khóa học để xem danh sách lớp
          </div>
          <div v-else-if="!filteredClasses.length" class="enrol-empty-sm">
            <i class="fa-regular fa-folder-open"></i>
            Không có lớp phù hợp
          </div>
          <div v-else class="enrol-tree-body">
            <tree
              :data="filteredClasses"
              text-field-name="text"
              allow-batch
              @item-click="selectClass"
            ></tree>
          </div>

          <div class="enrol-legend">
            <span><i class="fa-solid fa-file-lines"></i> Lớp hoạt động</span>
            <span><i class="fa-solid fa-user-xmark"></i> Chưa có CM</span>
          </div>
        </div>
      </aside>

      <!-- ============ MAIN ============ -->
      <section class="enrol-main">
        <!-- Empty state -->
        <div v-if="!class_info.class_id" class="enrol-card enrol-empty">
          <div class="enrol-empty__icon"><i class="fa-solid fa-chalkboard-user"></i></div>
          <div class="enrol-empty__title">Chưa chọn lớp học</div>
          <div class="enrol-empty__desc">Chọn trung tâm, khóa học rồi bấm vào một lớp ở cột bên trái để xem thông tin và xếp học sinh vào lớp.</div>
        </div>

        <template v-else>
          <!-- Banner thông tin lớp -->
          <div class="enrol-card enrol-hero">
            <div class="enrol-hero__top">
              <div class="enrol-hero__avatar"><i class="fa-solid fa-people-group"></i></div>
              <div class="enrol-hero__info">
                <div class="enrol-hero__name">{{ class_info.cls_name }}</div>
                <div class="enrol-hero__meta">
                  <span><i class="fa-regular fa-calendar"></i>{{ fmtDate(class_info.cls_startdate) }} – {{ fmtDate(class_info.cls_enddate) }}</span>
                  <span v-if="class_info.class_day_text"><i class="fa-regular fa-clock"></i>{{ class_info.class_day_text }}</span>
                  <span v-if="class_info.shift_text || class_info.room_text"><i class="fa-solid fa-door-open"></i>{{ [class_info.shift_text, class_info.room_text].filter(Boolean).join(' · ') }}</span>
                </div>
              </div>
              <div class="enrol-hero__capacity">
                <div class="enrol-capacity__label">Sĩ số</div>
                <div class="enrol-capacity__value" :class="{ full: isFull }">
                  {{ class_info.num_students }}<small>/{{ class_info.max_students }}</small>
                </div>
                <div class="enrol-progress">
                  <div class="enrol-progress__bar" :class="capacityLevel" :style="{ width: capacityPercent + '%' }"></div>
                </div>
                <div class="enrol-capacity__hint">
                  <template v-if="isFull">Lớp đã đủ sĩ số</template>
                  <template v-else>Còn <strong>{{ remainSlots }}</strong> chỗ trống</template>
                </div>
              </div>
            </div>

            <div class="enrol-staff">
              <div class="enrol-staff__item">
                <div class="enrol-staff__icon t"><i class="fa-solid fa-chalkboard-user"></i></div>
                <div><div class="enrol-staff__role">Giáo viên</div><div class="enrol-staff__name">{{ class_info.teacher_name || '—' }}</div></div>
              </div>
              <div class="enrol-staff__item" :class="{ missing: !class_info.cm_name }">
                <div class="enrol-staff__icon c"><i class="fa-solid fa-user-tie"></i></div>
                <div><div class="enrol-staff__role">CM - Quản lý lớp</div><div class="enrol-staff__name">{{ class_info.cm_name || 'Chưa cập nhật' }}</div></div>
              </div>
              <div class="enrol-staff__item">
                <div class="enrol-staff__icon a"><i class="fa-solid fa-user-graduate"></i></div>
                <div><div class="enrol-staff__role">TA - Trợ giảng</div><div class="enrol-staff__name">{{ class_info.ta_name || '—' }}</div></div>
              </div>
            </div>

            <div v-if="pre_schedules.length || next_schedules.length" class="enrol-timeline">
              <div class="enrol-timeline__label">Lịch học gần đây</div>
              <div class="enrol-timeline__list">
                <div class="enrol-session done" v-for="(item, index) in pre_schedules" :key="'P'+index">
                  <div class="enrol-session__code">{{ item.code }} · Buổi {{ item.subject_stt }}</div>
                  <div class="enrol-session__date">{{ item.class_date | formatDateViewDay }}</div>
                  <span class="enrol-badge success">Đã học</span>
                </div>
                <div class="enrol-session__sep" v-if="pre_schedules.length && next_schedules.length"><i class="fa-solid fa-chevron-right"></i></div>
                <div class="enrol-session next" :class="{ first: index === 0 }" v-for="(item, index) in next_schedules" :key="'N'+index">
                  <div class="enrol-session__code">{{ item.code }} · Buổi {{ item.subject_stt }}</div>
                  <div class="enrol-session__date">{{ item.class_date | formatDateViewDay }}</div>
                  <span class="enrol-badge primary">{{ index === 0 ? 'Buổi tới' : 'Sắp học' }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Danh sách học sinh -->
          <div class="enrol-card">
            <div class="enrol-toolbar">
              <div class="enrol-toolbar__title">
                <i class="fa-solid fa-users mr-2"></i>Học sinh trong lớp
                <span class="enrol-pill">{{ students.length }}</span>
                <span class="enrol-pill success" v-if="studentStats.active">{{ studentStats.active }} đang học</span>
                <span class="enrol-pill danger" v-if="studentStats.ended">{{ studentStats.ended }} hết buổi</span>
              </div>
              <div class="enrol-toolbar__actions">
                <div class="enrol-search sm">
                  <i class="fa-solid fa-magnifying-glass"></i>
                  <input type="text" v-model="studentKeyword" placeholder="Lọc theo tên, mã HS, mã HĐ..." />
                </div>
                <vs-button
                  color="success"
                  icon-pack="fa"
                  icon="fa-plus"
                  :disabled="isFull || !class_info.cm_name"
                  @click="showModalEnrol"
                >Thêm học sinh</vs-button>
              </div>
            </div>

            <div v-if="!class_info.cm_name" class="enrol-warning">
              <i class="fa-solid fa-triangle-exclamation"></i> Lớp chưa có CM quản lý – vui lòng cập nhật CM trước khi xếp lớp.
            </div>
            <div v-else-if="isFull" class="enrol-warning">
              <i class="fa-solid fa-circle-info"></i> Lớp đã đủ sĩ số, không thể thêm học sinh.
            </div>

            <div class="table-responsive">
              <table class="enrol-table">
                <thead>
                  <tr>
                    <th class="text-center" style="width:56px">STT</th>
                    <th>Học sinh</th>
                    <th>Hợp đồng / Thời gian</th>
                    <th>Gói phí</th>
                    <th class="text-right">Học phí</th>
                    <th style="min-width:170px">Tiến độ buổi học</th>
                    <th class="text-center">Trạng thái</th>
                    <th class="text-center">Thao tác</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in filteredStudents" :key="item.contract_id">
                    <td class="text-center text-muted">{{ index + 1 }}</td>
                    <td>
                      <div class="enrol-student">
                        <div class="enrol-student__avatar">{{ initials(item.name) }}</div>
                        <div>
                          <div class="enrol-strong">{{ item.name }}</div>
                          <div class="enrol-sub">{{ item.lms_code }}</div>
                        </div>
                      </div>
                    </td>
                    <td>
                      <div class="enrol-strong">{{ item.contract_code }}</div>
                      <div class="enrol-sub">{{ fmtDate(item.enrolment_start_date) || '…' }} → {{ fmtDate(item.enrolment_last_date) || '…' }}</div>
                    </td>
                    <td>
                      <div>{{ item.tuition_fee_name }}</div>
                      <span v-if="item.contract_type == 0" class="enrol-badge warning mt-1">Học thử</span>
                    </td>
                    <td class="text-right">
                      <div class="enrol-strong">{{ item.total_charged | formatCurrency }}</div>
                      <div class="enrol-sub">/ {{ item.must_charge | formatCurrency }}</div>
                      <div v-if="Number(item.must_charge) > Number(item.total_charged)" class="enrol-debt">
                        Còn nợ {{ (item.must_charge - item.total_charged) | formatCurrency }}
                      </div>
                    </td>
                    <td>
                      <div class="enrol-sessions">
                        <span><strong>{{ item.done_sessions || 0 }}</strong>/{{ item.summary_sessions || 0 }} buổi</span>
                        <span class="enrol-sub">còn {{ item.left_sessions || 0 }}</span>
                      </div>
                      <div class="enrol-progress">
                        <div class="enrol-progress__bar" :class="item.left_sessions > 0 ? 'ok' : 'full'" :style="{ width: sessionPercent(item) + '%' }"></div>
                      </div>
                    </td>
                    <td class="text-center">
                      <span v-if="item.left_sessions > 0" class="enrol-badge success">Đang học</span>
                      <span v-else class="enrol-badge danger">Hết số buổi</span>
                    </td>
                    <td class="text-center">
                      <div class="enrol-actions">
                        <button
                          v-if="item.left_sessions <= 0"
                          type="button" class="enrol-btn success"
                          title="Nối phí sang gói tiếp theo"
                          @click="showJoinContract(item)"
                        ><i class="fa-solid fa-link"></i> Nối phí</button>
                        <button
                          v-if="item.left_sessions <= 0 || item.contract_type == 0"
                          type="button" class="enrol-btn danger"
                          title="Rút học sinh ra khỏi lớp"
                          @click="withdrawContract(item)"
                        ><i class="fa-solid fa-right-from-bracket"></i> Rút lớp</button>
                        <span v-if="!(item.left_sessions <= 0 || item.contract_type == 0)" class="text-muted">—</span>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="!filteredStudents.length">
                    <td colspan="8" class="enrol-table__empty">
                      <i class="fa-regular fa-face-meh-blank"></i>
                      {{ students.length ? 'Không có học sinh khớp từ khóa lọc' : 'Lớp chưa có học sinh nào' }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </template>
      </section>
    </div>

    <!-- ============ MODAL THÊM HỌC SINH ============ -->
    <vs-popup :class="'view-enrolments modal_'+ modal_enrol.color" :title="modal_enrol.title" :active.sync="modal_enrol.show" v-if="class_info.class_id">
      <div class="enrol-modal-head">
        <div class="enrol-search grow">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" v-model="searchData.keyword" placeholder="Nhập tên, mã học sinh, mã hợp đồng rồi nhấn Enter" @keyup.enter="searchStudents" />
        </div>
        <vs-button type="border" icon-pack="fa" icon="fa-magnifying-glass" @click="searchStudents">Tìm kiếm</vs-button>
        <div class="enrol-modal-stats">
          <span class="enrol-pill primary">Đã chọn: {{ checked_list.length }}</span>
          <span class="enrol-pill" :class="modalRemain < 0 ? 'danger' : 'success'">Chỗ trống còn lại: {{ modalRemain }}</span>
        </div>
      </div>

      <div class="enrol-bulk" v-if="checked_list.length">
        <span><i class="fa-regular fa-calendar-check mr-1"></i>Áp dụng ngày bắt đầu cho <strong>{{ checked_list.length }}</strong> học sinh đã chọn:</span>
        <select v-model="bulkDate" class="enrol-select">
          <option value="">-- Chọn ngày --</option>
          <option v-for="(d, i) in class_dates" :key="i" :value="d.class_date">{{ d.class_date | formatDateViewDay }}</option>
        </select>
        <vs-button size="small" :disabled="!bulkDate" @click="applyBulkDate">Áp dụng</vs-button>
        <span class="enrol-sub">(chỉ áp dụng cho học sinh được phép chọn ngày này)</span>
      </div>

      <vs-alert :active.sync="alert.active" class="mb-4" :color="alert.color" closable icon-pack="feather" close-icon="icon-x">
        <div v-html="alert.body"></div>
      </vs-alert>

      <div class="table-responsive">
        <table class="enrol-table selectable">
          <thead>
            <tr>
              <th class="text-center" style="width:48px">
                <input type="checkbox" class="enrol-check" :checked="allPageChecked" :disabled="!studentSearch.length" @change="toggleAllPage($event.target.checked)" title="Chọn tất cả trang này" />
              </th>
              <th class="text-center" style="width:56px">STT</th>
              <th>Học sinh</th>
              <th>Mã hợp đồng</th>
              <th>Ngày học dự kiến</th>
              <th style="min-width:200px">Ngày bắt đầu học <span class="text-danger">*</span></th>
              <th>Gói phí</th>
              <th class="text-center">Buổi còn lại</th>
              <th>EC</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(item, index) in studentSearch"
              :key="item.contract_id"
              :class="{ checked: checked_list.indexOf(item.contract_id) > -1 }"
              @click="toggleRow(item, $event)"
            >
              <td class="text-center">
                <input type="checkbox" class="enrol-check" v-model="checked_list" :value="item.contract_id" />
              </td>
              <td class="text-center text-muted">{{ index + 1 + (pagination.cpage - 1) * pagination.limit }}</td>
              <td>
                <div class="enrol-strong">{{ item.name }}</div>
                <div class="enrol-sub">{{ item.lms_code }}</div>
              </td>
              <td>
                {{ item.code }}
                <div v-if="item.contract_type == 0"><span class="enrol-badge warning">Học thử</span></div>
              </td>
              <td>{{ fmtDate(item.start_date) }}</td>
              <td>
                <select
                  v-model="item.class_date"
                  class="enrol-select"
                  :class="{ invalid: checked_list.indexOf(item.contract_id) > -1 && !item.class_date }"
                  @change="onPickDate(item)"
                >
                  <option value="">Chọn ngày bắt đầu</option>
                  <option :value="`${class_date.class_date}`" v-for="(class_date, ind) in filterStartDate(item, class_dates)" :key="ind">{{ class_date.class_date | formatDateViewDay }}</option>
                </select>
              </td>
              <td>{{ item.tuition_fee_name }}</td>
              <td class="text-center">{{ item.left_session }}</td>
              <td>{{ item.ec_name }}</td>
            </tr>
            <tr v-if="!studentSearch.length">
              <td colspan="9" class="enrol-table__empty">
                <i class="fa-regular fa-face-meh-blank"></i> Không tìm thấy học sinh phù hợp để xếp lớp
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="enrol-modal-foot">
        <div class="flex items-center flex-wrap">
          <vs-dropdown vs-trigger-click class="cursor-pointer mr-4 items-per-page-handler">
            <div class="enrol-pager-size">
              <span class="mr-2">{{ pagination.total ? pagination.cpage * pagination.limit - (pagination.limit - 1) : 0 }} - {{ pagination.total - pagination.cpage * pagination.limit > 0 ? pagination.cpage * pagination.limit : pagination.total }} / {{ pagination.total }}</span>
              <feather-icon icon="ChevronDownIcon" svgClasses="h-4 w-4" />
            </div>
            <vs-dropdown-menu>
              <vs-dropdown-item v-for="(item, index) in limitSource" :key="index" @click="changePageLimit(item)">
                <span>{{ item }}</span>
              </vs-dropdown-item>
            </vs-dropdown-menu>
          </vs-dropdown>
          <vs-pagination
            v-if="Math.ceil(pagination.total / pagination.limit) > 1"
            :total="Math.ceil(pagination.total / pagination.limit)"
            :max="7"
            v-model="pagination.cpage" @change="changePage()"/>
        </div>
        <div class="enrol-modal-foot__btns">
          <vs-button color="dark" type="border" @click="modal_enrol.show = false">Đóng</vs-button>
          <vs-button color="success" icon-pack="fa" icon="fa-check" :disabled="!checked_list.length" @click="addStudent">
            Xếp {{ checked_list.length || '' }} học sinh vào lớp
          </vs-button>
        </div>
      </div>
    </vs-popup>

    <!-- ============ MODAL NỐI PHÍ ============ -->
    <vs-popup :class="'modal_'+ modal_join.color" :title="modal_join.title" :active.sync="modal_join.show">
      <div class="enrol-join">
        <div class="enrol-join__fee">
          <div class="enrol-sub">Gói phí sẽ nối</div>
          <div class="enrol-join__name">{{ modal_join.join_contract.tuition_fee_name }}</div>
        </div>
        <div class="enrol-join__kpis">
          <div><div class="enrol-sub">Buổi chính thức</div><strong>{{ modal_join.join_contract.real_sessions || 0 }}</strong></div>
          <div><div class="enrol-sub">Buổi học bổng</div><strong>{{ modal_join.join_contract.bonus_sessions || 0 }}</strong></div>
          <div class="total"><div class="enrol-sub">Tổng số buổi</div><strong>{{ modal_join.join_contract.summary_sessions || 0 }}</strong></div>
        </div>
        <div class="enrol-modal-foot__btns mt-5">
          <vs-button color="dark" type="border" @click="modal_join.show = false">Hủy</vs-button>
          <vs-button color="success" icon-pack="fa" icon="fa-link" @click="joinContract()">Nối phí</vs-button>
        </div>
      </div>
    </vs-popup>
  </div>
</template>

<script>
  import select from 'vue-select'
  import axios from '../../../http/axios.js'
  import u from '../../../until/helper.js'
  import moment from 'moment';
  import tree from 'vue-jstree'

  const LS_KEY = 'enrolments_filter'

  export default {
    components: {
      "vue-select": select,
      tree,
    },
    data() {
      return {
        select_type: 1,
        typeOptions: [
          { value: 1, label: 'Đang học' },
          { value: 2, label: 'Đã kết thúc' },
          { value: 0, label: 'Tất cả' },
        ],
        classKeyword: '',
        studentKeyword: '',
        bulkDate: '',
        modal_enrol: {
          title: "THÊM HỌC SINH VÀO LỚP",
          show: false,
          color: "info",
          closeOnBackdrop: true,
          error_message: ""
        },
        alert: {
          active: false,
          body: '',
          color: '',
        },
        classes: [],
        html: {
          branches: { item: '', list: [] },
          products: { item: '', list: [] },
        },
        enrol: {
          branch_id: '',
          product_id: ''
        },
        class_info: {
          class_id: '',
        },
        students: [],
        class_dates: [],
        searchData: {
          keyword: ''
        },
        studentSearch: [],
        limitSource: [20, 50, 100, 500],
        pagination: {
          url: "/api/roles/list",
          id: "",
          style: "line",
          class: "",
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
        checked_list: [],
        next_schedules: [],
        pre_schedules: [],
        withdraw_id: '',
        modal_join: {
          title: "NỐI PHÍ CHO HỌC SINH",
          show: false,
          color: "info",
          closeOnBackdrop: true,
          error_message: "",
          join_contract: {},
          student_id: ""
        },
      }
    },
    computed: {
      totalClasses() {
        return this.classes.reduce((sum, p) => sum + (p.children ? p.children.length : 0), 0)
      },
      filteredClasses() {
        const kw = this.normalize(this.classKeyword)
        if (!kw) return this.classes
        return this.classes
          .map(p => Object.assign({}, p, {
            opened: true,
            children: (p.children || []).filter(c => this.normalize(c.text).indexOf(kw) > -1)
          }))
          .filter(p => p.children.length)
      },
      isFull() {
        return Number(this.class_info.num_students) >= Number(this.class_info.max_students)
      },
      remainSlots() {
        return Math.max(0, (this.class_info.max_students || 0) - (this.class_info.num_students || 0))
      },
      capacityPercent() {
        const max = Number(this.class_info.max_students) || 0
        if (!max) return 0
        return Math.min(100, Math.round(Number(this.class_info.num_students) * 100 / max))
      },
      capacityLevel() {
        if (this.capacityPercent >= 100) return 'full'
        if (this.capacityPercent >= 80) return 'warn'
        return 'ok'
      },
      modalRemain() {
        return (this.class_info.max_students || 0) - (this.class_info.num_students || 0) - this.checked_list.length
      },
      studentStats() {
        const active = this.students.filter(s => s.left_sessions > 0).length
        return { active, ended: this.students.length - active }
      },
      filteredStudents() {
        const kw = this.normalize(this.studentKeyword)
        if (!kw) return this.students
        return this.students.filter(s =>
          [s.name, s.lms_code, s.contract_code].some(v => this.normalize(v).indexOf(kw) > -1)
        )
      },
      allPageChecked() {
        return this.studentSearch.length > 0 &&
          this.studentSearch.every(s => this.checked_list.indexOf(s.contract_id) > -1)
      },
    },
    created() {
      const saved = this.readSaved()
      if (saved.select_type !== undefined) this.select_type = saved.select_type
      Promise.all([
        axios.g(`/api/system/branches-has-user`),
        axios.g(`/api/system/products`)
      ]).then(([branches, products]) => {
        this.html.branches.list = branches.data
        this.html.products.list = products.data
        // Khôi phục lựa chọn lần trước (hoặc tự chọn nếu chỉ có 1 trung tâm)
        let branch = (branches.data || []).find(b => b.id == saved.branch_id)
        if (!branch && branches.data && branches.data.length === 1) branch = branches.data[0]
        const product = (products.data || []).find(p => p.id == saved.product_id)
        if (branch) { this.html.branches.item = branch; this.enrol.branch_id = branch.id }
        if (product) { this.html.products.item = product; this.enrol.product_id = product.id }
        this.loadClasses()
      })
    },
    methods: {
      normalize(v) {
        return (v || '').toString().toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '').replace(/đ/g, 'd')
      },
      fmtDate(v) {
        if (!v || v === '0000-00-00') return ''
        const m = moment(v)
        return m.isValid() ? m.format('DD/MM/YYYY') : v
      },
      initials(name) {
        const parts = (name || '').trim().split(/\s+/)
        return parts.length ? parts[parts.length - 1].charAt(0).toUpperCase() : '?'
      },
      sessionPercent(item) {
        const total = Number(item.summary_sessions) || 0
        if (!total) return 0
        return Math.min(100, Math.round(Number(item.done_sessions || 0) * 100 / total))
      },
      readSaved() {
        try { return JSON.parse(localStorage.getItem(LS_KEY)) || {} } catch (e) { return {} }
      },
      persistFilter() {
        try {
          localStorage.setItem(LS_KEY, JSON.stringify({
            branch_id: this.enrol.branch_id,
            product_id: this.enrol.product_id,
            select_type: this.select_type
          }))
        } catch (e) { /* ignore */ }
      },
      changeType(value) {
        this.select_type = value
        this.loadClasses()
      },
      resetClass() {
        this.class_info = { class_id: '' }
        this.students = []
        this.class_dates = []
        this.next_schedules = []
        this.pre_schedules = []
        this.studentKeyword = ''
      },
      saveBranch(data = null) {
        this.enrol.branch_id = data && typeof data === 'object' ? data.id : ''
        this.resetClass()
        this.loadClasses()
      },
      saveProduct(data = null) {
        this.enrol.product_id = data && typeof data === 'object' ? data.id : ''
        this.resetClass()
        this.loadClasses()
      },
      loadClasses() {
        this.persistFilter()
        if (this.enrol.branch_id && this.enrol.product_id) {
          this.$vs.loading();
          axios.p(`/api/lms/enrolments/load-classes`, {
            branch_id: this.enrol.branch_id,
            product_id: this.enrol.product_id,
            select_type: this.select_type
          })
            .then(response => {
              this.$vs.loading.close();
              const data = response.data || []
              data.forEach(p => {
                if (p.children && p.children.length) p.text = `${p.text} (${p.children.length})`
              })
              this.classes = data
            })
            .catch(() => this.$vs.loading.close())
        } else {
          this.classes = []
        }
      },
      selectClass(selected_class) {
        if (selected_class.model.item_type === 'class') {
          this.studentKeyword = ''
          this.loadDataClassSelected(selected_class.model.item_id)
        }
      },
      loadDataClassSelected(class_id) {
        this.$vs.loading();
        axios.g(`/api/lms/enrolments/info-class/${class_id}`)
          .then(response => {
            this.$vs.loading.close();
            this.class_info = response.data.class_info
            this.class_dates = response.data.class_dates
            this.students = response.data.students
            this.next_schedules = response.data.next_schedules
            this.pre_schedules = response.data.pre_schedules
          })
          .catch(() => this.$vs.loading.close())
      },
      showModalEnrol() {
        this.modal_enrol.show = true
        this.checked_list = []
        this.studentSearch = []
        this.bulkDate = ''
        this.alert.active = false
        this.pagination.cpage = 1
        this.getData()
      },
      searchStudents() {
        this.pagination.cpage = 1
        this.getData()
      },
      toggleRow(item, event) {
        const tag = (event.target.tagName || '').toLowerCase()
        if (['input', 'select', 'option', 'label'].indexOf(tag) > -1) return
        const i = this.checked_list.indexOf(item.contract_id)
        if (i > -1) this.checked_list.splice(i, 1)
        else this.checked_list.push(item.contract_id)
      },
      toggleAllPage(checked) {
        this.studentSearch.forEach(s => {
          const i = this.checked_list.indexOf(s.contract_id)
          if (checked && i === -1) this.checked_list.push(s.contract_id)
          if (!checked && i > -1) this.checked_list.splice(i, 1)
        })
      },
      onPickDate(item) {
        // Chọn ngày bắt đầu → tự tick học sinh
        if (item.class_date && this.checked_list.indexOf(item.contract_id) === -1) {
          this.checked_list.push(item.contract_id)
        }
      },
      applyBulkDate() {
        let skipped = 0
        this.studentSearch.forEach(s => {
          if (this.checked_list.indexOf(s.contract_id) === -1) return
          const allowed = this.filterStartDate(s, this.class_dates).some(d => d.class_date === this.bulkDate)
          if (allowed) s.class_date = this.bulkDate
          else skipped++
        })
        if (skipped) {
          this.$vs.notify({
            title: 'Lưu ý',
            text: `${skipped} học sinh không được phép bắt đầu từ ngày này, vui lòng chọn riêng.`,
            color: 'warning'
          })
        }
      },
      changePage() {
        if (this.pagination.init) {
          this.getData();
        }
      },
      changePageLimit(limit) {
        this.pagination.cpage = 1
        this.pagination.limit = limit
        this.getData();
      },
      getData() {
        if (this.class_info.class_id) {
          const data = {
            class_id: this.class_info.class_id,
            keyword: this.searchData.keyword,
            pagination: this.pagination
          }
          this.$vs.loading()
          axios.p('/api/lms/enrolments/get-students-add', data)
            .then((response) => {
              this.$vs.loading.close()
              this.studentSearch = response.data.list
              this.pagination = response.data.paging;
              setTimeout(() => {
                this.pagination.init = 1;
              }, 500)
            })
            .catch((error) => {
              console.log(error);
              this.$vs.loading.close();
            })
        } else {
          this.studentSearch = []
        }
      },
      filterStartDate(student, class_dates) {
        const currDate = moment().format('YYYY-MM-DD')
        const lastMonthDate = moment().subtract(1, 'months').format('YYYY-MM-DD')
        const isAdmin = u.checkPermission(this.$store.state.AppActiveUser, 'role_999999')
        return (class_dates || []).filter(item => {
          if (isAdmin) return true
          if (!moment(item.class_date).isSameOrAfter(student.start_date)) return false
          if (student.contract_type == 0) return moment(item.class_date).isSameOrAfter(currDate)
          return moment(item.class_date).isSameOrAfter(lastMonthDate)
        })
      },
      addStudent() {
        let mess = "";
        let resp = true;
        if (this.checked_list.length == 0) {
          mess += " - Chưa chọn học sinh<br/>";
          resp = false;
        }
        if (this.modalRemain < 0) {
          mess += " - Số học sinh đã chọn lớn hơn số chỗ trống còn lại trong lớp<br/>";
          resp = false;
        }
        const selected_contracts = this.studentSearch.filter(item => this.checked_list.indexOf(item.contract_id) > -1)
        selected_contracts.forEach(selected_contract => {
          if (!selected_contract.class_date || selected_contract.class_date.toString() == '') {
            mess += ` - Học sinh "${selected_contract.name} - ${selected_contract.lms_code}" chưa được chỉ định ngày bắt đầu học<br/>`;
            resp = false;
          }
        })
        if (!resp) {
          this.alert.color = 'danger'
          this.alert.body = mess;
          this.alert.active = true;
          return false;
        }
        this.alert.active = false
        this.$vs.loading()
        axios.p("/api/lms/enrolments/add-student", {
          class_id: this.class_info.class_id,
          contracts: selected_contracts
        })
          .then((response) => {
            this.$vs.loading.close();
            this.$vs.notify({
              title: 'Thành Công',
              text: response.data.message,
              color: 'success',
              iconPack: 'feather',
              icon: 'icon-check'
            })
            this.loadDataClassSelected(this.class_info.class_id);
            this.modal_enrol.show = false
          })
          .catch((e) => {
            console.log(e);
            this.$vs.loading.close();
          });
      },
      withdrawContract(data) {
        this.withdraw_id = data.contract_id
        this.$vs.dialog({
          type: 'confirm',
          color: 'danger',
          title: 'Rút học sinh khỏi lớp',
          text: `Bạn chắc chắn muốn rút học sinh ${data.name} (${data.lms_code}) ra khỏi lớp?`,
          accept: this.processWithdaw,
          acceptText: 'Rút lớp',
          cancelText: 'Hủy'
        })
      },
      processWithdaw() {
        this.$vs.loading();
        axios.p(`/api/lms/enrolments/withdraw`, { contract_id: this.withdraw_id })
          .then((response) => {
            this.$vs.loading.close();
            this.loadDataClassSelected(this.class_info.class_id);
            const ok = response.data.status != 0
            this.$vs.notify({
              title: ok ? 'Thành Công' : 'Thông báo',
              text: response.data.message,
              color: ok ? 'success' : 'warning',
              iconPack: 'feather',
              icon: ok ? 'icon-check' : 'icon-alert-triangle'
            })
          })
          .catch(() => this.$vs.loading.close())
      },
      showJoinContract(data) {
        this.$vs.loading();
        axios.p(`/api/lms/enrolments/get-contract-join`, {
          student_id: data.student_id,
          product_id: this.class_info.product_id
        })
          .then((response) => {
            this.$vs.loading.close();
            if (response.data.status == 1) {
              this.modal_join.title = "Nối phí học sinh: " + data.name
              this.modal_join.show = true
              this.modal_join.join_contract = response.data.join_contract
              this.modal_join.student_id = data.student_id
            } else {
              this.$vs.notify({
                title: 'THÔNG BÁO',
                text: response.data.message,
                color: 'warning',
              })
            }
          })
          .catch(() => this.$vs.loading.close())
      },
      joinContract() {
        this.$vs.loading();
        axios.p(`/api/lms/enrolments/contract-join`, {
          student_id: this.modal_join.student_id,
          contract_id: this.modal_join.join_contract.id,
          class_id: this.class_info.class_id
        })
          .then((response) => {
            this.$vs.loading.close();
            this.modal_join.show = false
            this.loadDataClassSelected(this.class_info.class_id);
            this.$vs.notify({
              title: 'Thành Công',
              text: response.data.message,
              color: 'success',
              iconPack: 'feather',
              icon: 'icon-check'
            })
          })
          .catch(() => this.$vs.loading.close())
      }
    },
  }
</script>

<style>
/* ---------- Layout ---------- */
.enrol-page { font-size: 14px; color: #334155; }
.enrol-layout { display: flex; gap: 20px; align-items: flex-start; }
.enrol-sidebar { width: 320px; flex-shrink: 0; position: sticky; top: 90px; display: flex; flex-direction: column; gap: 16px; }
.enrol-main { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 16px; }
@media (max-width: 1024px) {
  .enrol-layout { flex-direction: column; }
  .enrol-sidebar { width: 100%; position: static; }
}
.enrol-card { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(15, 23, 42, 0.06); padding: 18px 20px; }
.enrol-card__title { font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 14px; }
.enrol-field { margin-bottom: 14px; }
.enrol-field > label { display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; }

/* ---------- Inputs (active) ---------- */
.enrol-page .v-select .vs__dropdown-toggle,
.enrol-select,
.enrol-search {
  background: #fff; border: 1.5px solid #cbd5e1; border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); transition: border-color .15s, box-shadow .15s;
}
.enrol-page .v-select .vs__dropdown-toggle { min-height: 40px; cursor: pointer; }
.enrol-page .v-select .vs__dropdown-toggle:hover,
.enrol-page .v-select.vs--open .vs__dropdown-toggle,
.enrol-select:hover, .enrol-select:focus,
.enrol-search:hover, .enrol-search:focus-within {
  border-color: #7367f0; box-shadow: 0 0 0 3px rgba(115, 103, 240, 0.2); outline: none;
}
.enrol-page .v-select .vs__search,
.enrol-page .v-select .vs__search:focus { border: none !important; background: transparent !important; box-shadow: none !important; font-size: 14px; }
.enrol-page .v-select .vs__selected { font-size: 14px; }

.enrol-select { height: 38px; padding: 0 10px; font-size: 14px; color: #334155; cursor: pointer; width: 100%; }
.enrol-select.invalid { border-color: #ea5455; background: #fff5f5; }

.enrol-search { display: flex; align-items: center; gap: 8px; padding: 0 12px; height: 40px; cursor: text; }
.enrol-search i { color: #94a3b8; font-size: 14px; }
.enrol-search input { flex: 1; border: none; outline: none; background: transparent; font-size: 14px; color: #334155; min-width: 0; }
.enrol-search.sm { height: 38px; width: 280px; }
.enrol-search.grow { flex: 1; min-width: 240px; }
.enrol-search:has(input:disabled) { background: #f1f5f9; border: 1px dashed #cbd5e1; box-shadow: none; cursor: not-allowed; }
.enrol-search input:disabled { cursor: not-allowed; color: #94a3b8; }

/* Segment buttons */
.enrol-segment { display: flex; background: #f1f5f9; border-radius: 8px; padding: 3px; gap: 3px; }
.enrol-segment button {
  flex: 1; border: none; background: transparent; padding: 7px 6px; border-radius: 6px;
  font-size: 13px; font-weight: 600; color: #64748b; cursor: pointer; white-space: nowrap; transition: all .15s;
}
.enrol-segment button:hover { color: #7367f0; }
.enrol-segment button.active { background: #fff; color: #7367f0; box-shadow: 0 1px 4px rgba(115, 103, 240, 0.25); }

/* ---------- Tree ---------- */
.enrol-tree-card { padding: 14px 12px; }
.enrol-tree-head { display: flex; justify-content: space-between; align-items: center; padding: 0 8px 10px; font-size: 15px; font-weight: 700; color: #1e293b; border-bottom: 1px solid #eef2f7; }
.enrol-tree-body { max-height: calc(100vh - 470px); min-height: 200px; overflow: auto; padding-top: 6px; }
.enrol-tree-body .tree-anchor { font-size: 14px; padding: 2px 6px; border-radius: 6px; height: auto; line-height: 26px; }
.enrol-tree-body .tree-anchor:hover, .enrol-tree-body .tree-hovered { background: rgba(115, 103, 240, 0.08) !important; box-shadow: none !important; }
.enrol-tree-body .tree-selected { background: rgba(115, 103, 240, 0.16) !important; box-shadow: none !important; color: #5e50ee; font-weight: 600; }
.enrol-tree-body .fa-user-xmark { color: #ea5455; }
.enrol-tree-body .fa-file-lines { color: #7367f0; }
.enrol-tree-body .fa-folder { color: #f59e0b; }
.enrol-legend { display: flex; gap: 14px; flex-wrap: wrap; border-top: 1px solid #eef2f7; margin-top: 8px; padding: 10px 8px 0; font-size: 13px; color: #64748b; }
.enrol-legend .fa-file-lines { color: #7367f0; }
.enrol-legend .fa-user-xmark { color: #ea5455; }
.enrol-empty-sm { text-align: center; color: #94a3b8; font-size: 13px; padding: 28px 12px; }
.enrol-empty-sm i { display: block; font-size: 26px; margin-bottom: 8px; color: #cbd5e1; }

/* ---------- Empty state ---------- */
.enrol-empty { text-align: center; padding: 70px 20px; }
.enrol-empty__icon { width: 84px; height: 84px; margin: 0 auto 16px; border-radius: 50%; background: rgba(115, 103, 240, 0.1); color: #7367f0; display: flex; align-items: center; justify-content: center; font-size: 36px; }
.enrol-empty__title { font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 6px; }
.enrol-empty__desc { font-size: 14px; color: #64748b; max-width: 460px; margin: 0 auto; }

/* ---------- Hero ---------- */
.enrol-hero { padding: 20px 22px; }
.enrol-hero__top { display: flex; align-items: center; gap: 18px; flex-wrap: wrap; }
.enrol-hero__avatar { width: 58px; height: 58px; border-radius: 14px; background: linear-gradient(135deg, #7367f0, #9e95f5); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 26px; flex-shrink: 0; }
.enrol-hero__info { flex: 1; min-width: 240px; }
.enrol-hero__name { font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 6px; }
.enrol-hero__meta { display: flex; flex-wrap: wrap; gap: 8px 18px; font-size: 14px; color: #475569; }
.enrol-hero__meta i { color: #7367f0; margin-right: 6px; }
.enrol-hero__capacity { width: 210px; background: #f8fafc; border: 1px solid #eef2f7; border-radius: 10px; padding: 10px 14px; }
.enrol-capacity__label { font-size: 13px; color: #64748b; font-weight: 600; }
.enrol-capacity__value { font-size: 26px; font-weight: 800; color: #1e293b; line-height: 1.2; }
.enrol-capacity__value small { font-size: 16px; color: #94a3b8; font-weight: 600; }
.enrol-capacity__value.full { color: #ea5455; }
.enrol-capacity__hint { font-size: 13px; color: #64748b; margin-top: 4px; }

.enrol-progress { height: 7px; background: #e2e8f0; border-radius: 99px; overflow: hidden; margin-top: 4px; }
.enrol-progress__bar { height: 100%; border-radius: 99px; transition: width .3s; }
.enrol-progress__bar.ok { background: #28c76f; }
.enrol-progress__bar.warn { background: #ff9f43; }
.enrol-progress__bar.full { background: #ea5455; }

.enrol-staff { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; margin-top: 18px; }
@media (max-width: 900px) { .enrol-staff { grid-template-columns: 1fr; } }
.enrol-staff__item { display: flex; align-items: center; gap: 12px; padding: 10px 12px; border: 1px solid #eef2f7; border-radius: 10px; }
.enrol-staff__item.missing { border: 1px dashed #ea5455; background: #fff5f5; }
.enrol-staff__item.missing .enrol-staff__name { color: #ea5455; }
.enrol-staff__icon { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }
.enrol-staff__icon.t { background: rgba(115, 103, 240, 0.12); color: #7367f0; }
.enrol-staff__icon.c { background: rgba(0, 207, 232, 0.12); color: #00b8cf; }
.enrol-staff__icon.a { background: rgba(255, 159, 67, 0.14); color: #ff9f43; }
.enrol-staff__role { font-size: 13px; color: #64748b; }
.enrol-staff__name { font-size: 14px; font-weight: 600; color: #1e293b; }

.enrol-timeline { margin-top: 18px; padding-top: 14px; border-top: 1px solid #eef2f7; }
.enrol-timeline__label { font-size: 13px; font-weight: 600; color: #64748b; margin-bottom: 10px; }
.enrol-timeline__list { display: flex; gap: 10px; overflow-x: auto; padding-bottom: 4px; align-items: center; }
.enrol-session { min-width: 140px; border-radius: 10px; padding: 10px 12px; text-align: center; border: 1px solid #e2e8f0; background: #fff; flex-shrink: 0; }
.enrol-session.done { background: #f0fdf4; border-color: #bbf7d0; }
.enrol-session.next.first { border: 2px solid #7367f0; box-shadow: 0 2px 8px rgba(115, 103, 240, 0.2); }
.enrol-session__code { font-size: 13px; font-weight: 700; color: #1e293b; white-space: nowrap; }
.enrol-session__date { font-size: 13px; color: #475569; margin: 4px 0 6px; white-space: nowrap; }
.enrol-session__sep { color: #cbd5e1; font-size: 14px; }

/* ---------- Badges / pills ---------- */
.enrol-badge, .enrol-pill { display: inline-flex !important; align-items: center; white-space: nowrap !important; font-size: 13px; font-weight: 600; border-radius: 99px; line-height: 1; }
.enrol-badge { padding: 5px 10px; }
.enrol-badge.success { background: rgba(40, 199, 111, 0.14); color: #1f9d57; }
.enrol-badge.danger { background: rgba(234, 84, 85, 0.12); color: #d63b3c; }
.enrol-badge.primary { background: rgba(115, 103, 240, 0.12); color: #5e50ee; }
.enrol-badge.warning { background: rgba(255, 159, 67, 0.16); color: #d97706; }
.enrol-pill { padding: 4px 10px; background: #f1f5f9; color: #475569; margin-left: 6px; }
.enrol-pill.success { background: rgba(40, 199, 111, 0.14); color: #1f9d57; }
.enrol-pill.danger { background: rgba(234, 84, 85, 0.12); color: #d63b3c; }
.enrol-pill.primary { background: rgba(115, 103, 240, 0.12); color: #5e50ee; }

/* ---------- Toolbar ---------- */
.enrol-toolbar { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 14px; }
.enrol-toolbar__title { font-size: 16px; font-weight: 700; color: #1e293b; display: flex; align-items: center; flex-wrap: wrap; gap: 4px; }
.enrol-toolbar__actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.enrol-warning { background: #fff7ed; border: 1px solid #fed7aa; color: #c2410c; border-radius: 8px; padding: 10px 14px; font-size: 14px; margin-bottom: 14px; }
.enrol-warning i { margin-right: 6px; }

/* ---------- Table ---------- */
.enrol-page .table-responsive, .view-enrolments .table-responsive { overflow-x: auto; border: 1px solid #eef2f7; border-radius: 10px; }
.enrol-table { width: 100%; border-collapse: collapse; font-size: 14px; }
.enrol-table thead th { background: #f8fafc; color: #475569; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .02em; padding: 12px 12px; border-bottom: 1px solid #e2e8f0; white-space: nowrap; text-align: left; }
.enrol-table thead th.text-center { text-align: center; }
.enrol-table thead th.text-right { text-align: right; }
.enrol-table tbody td { padding: 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; white-space: nowrap; }
.enrol-table tbody tr:last-child td { border-bottom: none; }
.enrol-table tbody tr:hover { background: #fafaff; }
.enrol-table.selectable tbody tr { cursor: pointer; }
.enrol-table.selectable tbody tr.checked { background: rgba(115, 103, 240, 0.07); }
.enrol-table.selectable tbody tr.checked td:first-child { box-shadow: inset 3px 0 0 #7367f0; }
.enrol-table__empty { text-align: center; color: #94a3b8; padding: 36px 12px !important; font-size: 14px; }
.enrol-table__empty i { margin-right: 6px; }
.enrol-strong { font-weight: 600; color: #1e293b; }
.enrol-sub { font-size: 13px; color: #64748b; }
.enrol-debt { font-size: 13px; color: #ea5455; font-weight: 600; margin-top: 2px; }
.enrol-student { display: flex; align-items: center; gap: 10px; }
.enrol-student__avatar { width: 36px; height: 36px; border-radius: 50%; background: rgba(115, 103, 240, 0.12); color: #7367f0; font-weight: 700; font-size: 15px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.enrol-sessions { display: flex; justify-content: space-between; gap: 8px; font-size: 14px; }
.enrol-actions { display: inline-flex; gap: 6px; }
.enrol-btn { display: inline-flex; align-items: center; gap: 6px; border-radius: 6px; padding: 6px 10px; font-size: 13px; font-weight: 600; cursor: pointer; white-space: nowrap; background: #fff; transition: all .15s; }
.enrol-btn.success { border: 1.5px solid #28c76f; color: #1f9d57; }
.enrol-btn.success:hover { background: #28c76f; color: #fff; }
.enrol-btn.danger { border: 1.5px solid #ea5455; color: #d63b3c; }
.enrol-btn.danger:hover { background: #ea5455; color: #fff; }
.enrol-check { width: 17px; height: 17px; cursor: pointer; accent-color: #7367f0; vertical-align: middle; }
.enrol-check:disabled { cursor: not-allowed; }

/* ---------- Modals ---------- */
.view-enrolments.con-vs-popup .vs-popup { width: 92%; max-width: 1400px; }
.view-enrolments .vs-popup--content { font-size: 14px; color: #334155; }
.enrol-modal-head { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 14px; }
.enrol-modal-stats { display: flex; gap: 6px; margin-left: auto; }
.enrol-bulk { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; background: rgba(115, 103, 240, 0.06); border: 1px solid rgba(115, 103, 240, 0.25); border-radius: 8px; padding: 10px 14px; margin-bottom: 14px; font-size: 14px; }
.enrol-bulk .enrol-select { width: 200px; }
.enrol-modal-foot { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; margin-top: 16px; }
.enrol-modal-foot__btns { display: flex; gap: 10px; justify-content: flex-end; }
.enrol-pager-size { padding: 8px 14px; border: 1.5px solid #cbd5e1; border-radius: 99px; display: flex; align-items: center; font-size: 14px; font-weight: 500; cursor: pointer; }

.enrol-join__fee { background: #f8fafc; border-radius: 10px; padding: 14px 16px; margin-bottom: 14px; }
.enrol-join__name { font-size: 17px; font-weight: 700; color: #1e293b; margin-top: 2px; }
.enrol-join__kpis { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
.enrol-join__kpis > div { border: 1px solid #eef2f7; border-radius: 10px; padding: 12px; text-align: center; }
.enrol-join__kpis strong { font-size: 22px; color: #1e293b; }
.enrol-join__kpis .total { background: rgba(40, 199, 111, 0.08); border-color: #bbf7d0; }
.enrol-join__kpis .total strong { color: #1f9d57; }
</style>
