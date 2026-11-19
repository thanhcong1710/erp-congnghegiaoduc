<template>
  <div id="crm-import-add" class="imp">
    <vx-card no-shadow class="mt-5">
      <!-- Stepper -->
      <div class="imp-steps">
        <div v-for="(s, i) in steps" :key="i" :class="['imp-step', curr_step === i + 1 && 'is-active', curr_step > i + 1 && 'is-done']">
          <div class="imp-step__dot">
            <i v-if="curr_step > i + 1" class="fas fa-check"></i><span v-else>{{ i + 1 }}</span>
          </div>
          <div class="imp-step__txt">
            <div class="imp-step__title">{{ s.title }}</div>
            <div class="imp-step__sub">{{ s.sub }}</div>
          </div>
        </div>
      </div>

      <!-- STEP 1: chọn file -->
      <div v-if="curr_step == 1" class="imp-body">
        <div class="imp-grid">
          <div>
            <div class="imp-field mb-4" style="max-width:420px">
              <label>Trung tâm nhận dữ liệu <span class="req">*</span></label>
              <div v-if="branch_list.length === 1" class="imp-fixed"><i class="fas fa-school"></i> {{ branch_item && branch_item.name }}</div>
              <v-select v-else label="name" :options="branch_list" v-model="branch_item" placeholder="Chọn trung tâm"
                :searchable="true" @input="saveBranch" />
            </div>

            <div :class="['imp-drop', dragOver && 'is-over', file_name && 'has-file']"
              @click="$refs.fileInput.click()"
              @dragover.prevent="dragOver = true" @dragleave.prevent="dragOver = false" @drop.prevent="onDrop">
              <input ref="fileInput" type="file" accept=".xlsx" style="display:none" @change="fileChanged" />
              <template v-if="!file_name">
                <i class="fas fa-cloud-upload-alt imp-drop__icon"></i>
                <div class="imp-drop__title">Kéo thả file Excel vào đây hoặc <u>bấm để chọn file</u></div>
                <div class="imp-drop__sub">Chỉ chấp nhận file .xlsx theo mẫu, tối đa 10.000 khách hàng / lần</div>
              </template>
              <template v-else>
                <i class="fas fa-file-excel imp-drop__icon" style="color:#16a34a"></i>
                <div class="imp-drop__title">{{ file_name }}</div>
                <div class="imp-drop__sub">{{ fileSize }} · <a href="javascript:void(0)" @click.stop="clearFile">Chọn file khác</a></div>
              </template>
            </div>
            <p v-if="error_message_upload" class="imp-error"><i class="fas fa-exclamation-circle"></i> {{ error_message_upload }}</p>

            <div class="imp-actions">
              <vs-button color="dark" type="border" @click="$router.push('/crm/imports')">Quay lại</vs-button>
              <vs-button color="success" :disabled="!attached_file || !branch_id" @click="btnUpload">
                <i class="fas fa-search"></i> Kiểm tra dữ liệu
              </vs-button>
            </div>
          </div>

          <div class="imp-guide">
            <div class="imp-guide__title"><i class="fas fa-info-circle"></i> Hướng dẫn</div>
            <a class="imp-guide__dl" href="/static/template/import_khach_hang.xlsx" target="_blank"><i class="fas fa-download"></i> Tải file mẫu</a>
            <ol>
              <li>Giữ nguyên dòng tiêu đề và thứ tự cột như file mẫu.</li>
              <li><b>Bắt buộc:</b> Tên phụ huynh, Số điện thoại (10 số).</li>
              <li>Ngày sinh học sinh nhập <b>năm</b> (2019) hoặc <b>dd/mm/yyyy</b>.</li>
              <li>Cột <b>Người phụ trách</b> ghi mã nhân viên – nếu nhân viên đó được chọn ở bước phân chia sẽ được ưu tiên giao đúng người.</li>
              <li>SĐT trùng trong file chỉ lấy dòng đầu tiên; SĐT đã có trong hệ thống và chưa khoá có thể <b>ghi đè</b> người phụ trách.</li>
            </ol>
          </div>
        </div>
      </div>

      <!-- STEP 2: kiểm tra -->
      <div v-if="curr_step == 2" class="imp-body">
        <div class="imp-head">
          <div>
            <div class="imp-head__title">Kết quả kiểm tra dữ liệu</div>
            <div class="imp-head__sub"><i class="fas fa-file-excel" style="color:#16a34a"></i> {{ file_name }}<span v-if="branch_item"> · {{ branch_item.name }}</span></div>
          </div>
          <span class="imp-act" @click="exportResult"><i class="fas fa-download"></i> Tải file kết quả kiểm tra</span>
        </div>

        <div class="imp-stats">
          <div :class="['imp-stat', 'imp-stat--all', rowFilter === 'all' && 'is-active']" @click="rowFilter = 'all'">
            <div class="imp-stat__val">{{ fmt(total) }}</div><div class="imp-stat__lbl">Tổng số dòng</div>
          </div>
          <div :class="['imp-stat', 'imp-stat--ok', rowFilter === 'ok' && 'is-active']" @click="rowFilter = 'ok'">
            <div class="imp-stat__val">{{ fmt(total_validate) }}</div><div class="imp-stat__lbl">Hợp lệ – thêm mới</div>
          </div>
          <div :class="['imp-stat', 'imp-stat--warn', rowFilter === 'overwrite' && 'is-active']" @click="rowFilter = 'overwrite'">
            <div class="imp-stat__val">{{ fmt(total_open_lock) }}</div><div class="imp-stat__lbl">Đã có – có thể ghi đè</div>
          </div>
          <div :class="['imp-stat', 'imp-stat--err', rowFilter === 'error' && 'is-active']" @click="rowFilter = 'error'">
            <div class="imp-stat__val">{{ fmt(total_error) }}</div><div class="imp-stat__lbl">Lỗi / bị bỏ qua</div>
          </div>
        </div>

        <div class="imp-toolbar">
          <input class="imp-input" style="max-width:320px" v-model.trim="rowKeyword" placeholder="Tìm theo tên hoặc SĐT..." />
          <span class="imp-sub">Hiển thị {{ fmt(filteredRows.length) }} dòng</span>
        </div>
        <div class="imp-table-wrap" style="max-height:480px">
          <table class="imp-table">
            <thead>
              <tr><th style="width:60px">STT</th><th>Tên phụ huynh</th><th>Số điện thoại</th><th>Học sinh</th><th>NV phụ trách (file)</th><th class="tc">Kết quả</th><th>Chi tiết</th></tr>
            </thead>
            <tbody>
              <tr v-for="r in pagedRows" :key="r.id">
                <td>{{ r._no }}</td>
                <td class="nowrap">{{ r.name || '—' }}</td>
                <td class="nowrap">{{ r.gud_mobile1 || '—' }}<span v-if="r.gud_mobile2" class="imp-sub"><br>{{ r.gud_mobile2 }}</span></td>
                <td>{{ [r.student_name_1, r.student_name_2].filter(Boolean).join(', ') || '—' }}</td>
                <td class="nowrap">{{ r.owner_hrm || '—' }}</td>
                <td class="tc"><span :class="['imp-badge', rowBadge(r).cls]">{{ rowBadge(r).label }}</span></td>
                <td class="imp-msg">{{ r.error_message }}</td>
              </tr>
              <tr v-if="!pagedRows.length"><td colspan="7" class="imp-empty">Không có dòng nào</td></tr>
            </tbody>
          </table>
        </div>
        <div class="flex justify-end mt-3" v-if="filteredRows.length > rowLimit">
          <vs-pagination :total="Math.ceil(filteredRows.length / rowLimit)" :max="7" v-model="rowPage" />
        </div>

        <div class="imp-confirm" v-if="total_error > 0">
          <vs-checkbox v-model="error_checked">Tôi đã xem và đồng ý <b>bỏ qua {{ fmt(total_error) }} dòng lỗi</b> (không nhập vào hệ thống)</vs-checkbox>
        </div>
        <div v-if="!total_validate && !total_open_lock" class="imp-error mt-3">
          <i class="fas fa-exclamation-circle"></i> File không có dòng nào hợp lệ để nhập. Vui lòng sửa file theo cột “Chi tiết” rồi import lại.
        </div>
        <div class="imp-actions">
          <vs-button color="dark" type="border" @click="restart">Import file khác</vs-button>
          <vs-button color="success" :disabled="!canNextStep2" @click="curr_step = 3">Tiếp theo <i class="fas fa-arrow-right"></i></vs-button>
        </div>
      </div>

      <!-- STEP 3: phân chia -->
      <div v-if="curr_step == 3" class="imp-body">
        <div class="imp-grid">
          <div>
            <div class="imp-field mb-5">
              <label>Người phụ trách <span class="req">*</span>
                <a v-if="list_owner.length" href="javascript:void(0)" class="imp-link" @click="selectAllOwners">Chọn tất cả ({{ list_owner.length }})</a>
                <a v-if="data_assign.owners.length" href="javascript:void(0)" class="imp-link" @click="data_assign.owners = []">Bỏ chọn</a>
              </label>
              <multiselect placeholder="Chọn một hoặc nhiều người phụ trách" v-model="data_assign.owners" :options="list_owner"
                label="label_name" track-by="id" :close-on-select="false" :hide-selected="true" :multiple="true" :searchable="true"
                select-label="" deselect-label="Bấm để bỏ">
                <span slot="noResult">Không tìm thấy nhân viên</span>
                <span slot="noOptions">Trung tâm chưa có nhân viên phụ trách</span>
              </multiselect>
            </div>
            <div class="vx-row">
              <div class="vx-col md:w-1/2 w-full mb-5">
                <div class="imp-field">
                  <label>Nguồn <span class="req">*</span></label>
                  <v-select label="name" placeholder="Chọn nguồn" :options="list_source" v-model="data_assign.source" :searchable="true" @input="selectSource" />
                </div>
              </div>
              <div class="vx-col md:w-1/2 w-full mb-5">
                <div class="imp-field">
                  <label>Nguồn chi tiết</label>
                  <v-select label="name" :placeholder="data_assign.source ? 'Chọn nguồn chi tiết' : 'Chọn nguồn trước'" :options="list_source_detail"
                    v-model="data_assign.source_detail" :searchable="true" :disabled="!data_assign.source" />
                </div>
              </div>
            </div>
            <div class="imp-opts">
              <vs-checkbox v-model="data_assign.prefer_file_owner">Ưu tiên giao cho <b>NV phụ trách ghi trong file</b> (nếu NV đó có trong danh sách đã chọn)</vs-checkbox>
              <vs-checkbox v-if="total_open_lock > 0" v-model="data_assign.allow_overwrite">Ghi đè người phụ trách cho <b>{{ fmt(total_open_lock) }}</b> khách hàng đã có trong hệ thống</vs-checkbox>
            </div>
            <p v-if="data_assign.error_message" class="imp-error" v-html="data_assign.error_message"></p>
            <div class="imp-actions">
              <vs-button color="dark" type="border" @click="curr_step = 2"><i class="fas fa-arrow-left"></i> Quay lại</vs-button>
              <vs-button color="success" @click="assginContact"><i class="fas fa-check"></i> Xác nhận nhập {{ fmt(totalToImport) }} khách hàng</vs-button>
            </div>
          </div>

          <div class="imp-guide">
            <div class="imp-guide__title"><i class="fas fa-chart-pie"></i> Dự kiến phân chia</div>
            <div class="imp-sum-row"><span>Khách hàng thêm mới</span><b>{{ fmt(total_validate) }}</b></div>
            <div class="imp-sum-row"><span>Ghi đè người phụ trách</span><b>{{ fmt(data_assign.allow_overwrite ? total_open_lock : 0) }}</b></div>
            <div class="imp-sum-row"><span>Bỏ qua</span><b style="color:#dc2626">{{ fmt(total_error + (data_assign.allow_overwrite ? 0 : total_open_lock)) }}</b></div>
            <div class="imp-sum-row imp-sum-row--total"><span>Tổng sẽ nhập</span><b>{{ fmt(totalToImport) }}</b></div>
            <template v-if="data_assign.owners.length">
              <div class="imp-guide__title mt-4" style="font-size:14px">Mỗi người nhận khoảng</div>
              <div v-for="o in previewOwners" :key="o.id" class="imp-sum-row"><span class="nowrap">{{ o.label_name }}</span><b>~{{ fmt(o.count) }}</b></div>
              <div v-if="data_assign.owners.length > previewOwners.length" class="imp-sub">… và {{ data_assign.owners.length - previewOwners.length }} người khác</div>
            </template>
            <div v-else class="imp-sub mt-3">Chọn người phụ trách để xem dự kiến. Dữ liệu được chia đều lần lượt (vòng tròn).</div>
          </div>
        </div>
      </div>

      <!-- STEP 4: hoàn thành -->
      <div v-if="curr_step == 4" class="imp-body tc">
        <div class="imp-done__icon"><i class="fas fa-check"></i></div>
        <div class="imp-head__title" style="font-size:20px">Import hoàn tất!</div>
        <div class="imp-sub mb-5">{{ file_name }}</div>
        <div class="imp-stats" style="max-width:760px;margin:0 auto">
          <div class="imp-stat imp-stat--ok"><div class="imp-stat__val">{{ fmt(result.total_success - result.total_overwrite) }}</div><div class="imp-stat__lbl">Khách hàng mới</div></div>
          <div class="imp-stat imp-stat--warn"><div class="imp-stat__val">{{ fmt(result.total_overwrite) }}</div><div class="imp-stat__lbl">Ghi đè người phụ trách</div></div>
          <div class="imp-stat imp-stat--err"><div class="imp-stat__val">{{ fmt(result.total_error) }}</div><div class="imp-stat__lbl">Bị bỏ qua</div></div>
        </div>
        <div v-if="result.owners && result.owners.length" class="imp-table-wrap" style="max-width:560px;margin:20px auto 0;text-align:left">
          <table class="imp-table">
            <thead><tr><th>Người phụ trách</th><th class="tc">KH mới nhận</th></tr></thead>
            <tbody><tr v-for="o in result.owners" :key="o.id"><td class="nowrap">{{ o.hrm_id }} - {{ o.name }}</td><td class="tc"><b>{{ fmt(o.total) }}</b></td></tr></tbody>
          </table>
        </div>
        <div class="imp-actions" style="justify-content:center">
          <vs-button color="dark" type="border" @click="exportResult"><i class="fas fa-download"></i> Tải file kết quả</vs-button>
          <vs-button type="border" @click="$router.push('/crm/imports')"><i class="fas fa-list"></i> Danh sách import</vs-button>
          <vs-button color="success" @click="restart"><i class="fas fa-upload"></i> Import file khác</vs-button>
        </div>
      </div>
    </vx-card>
  </div>
</template>

<script>
  import vSelect from 'vue-select'
  import Multiselect from 'vue-multiselect'
  import axios from '../../../http/axios.js'
  import { importRowBadge } from './importHelper.js'

  const emptyAssign = () => ({
    owners: [], source: null, source_detail: null, error_message: '',
    prefer_file_owner: true, allow_overwrite: true,
  })

  export default {
    components: { vSelect, Multiselect },
    data() {
      return {
        steps: [
          { title: 'Chọn tập tin', sub: 'Trung tâm & file Excel' },
          { title: 'Kiểm tra dữ liệu', sub: 'Hợp lệ, trùng, lỗi' },
          { title: 'Phân chia dữ liệu', sub: 'Người phụ trách & nguồn' },
          { title: 'Hoàn thành', sub: 'Kết quả import' },
        ],
        curr_step: 1,
        attached_file: '',
        file_name: '',
        file_size: 0,
        dragOver: false,
        import_id: 0,
        list_data_check: [],
        total: 0,
        total_error: 0,
        total_validate: 0,
        total_open_lock: 0,
        error_checked: false,
        rowFilter: 'all',
        rowKeyword: '',
        rowPage: 1,
        rowLimit: 100,
        list_owner: [],
        list_source: [],
        list_source_detail: [],
        data_assign: emptyAssign(),
        result: {},
        branch_list: [],
        branch_item: null,
        branch_id: '',
        error_message_upload: '',
      }
    },
    computed: {
      fileSize() {
        const kb = this.file_size / 1024
        return kb > 1024 ? (kb / 1024).toFixed(1) + ' MB' : Math.max(1, Math.round(kb)) + ' KB'
      },
      filteredRows() {
        const kw = this.rowKeyword.toLowerCase()
        return this.list_data_check.filter(r => {
          if (this.rowFilter !== 'all' && importRowBadge(r).group !== this.rowFilter) return false
          if (!kw) return true
          return [r.name, r.gud_mobile1, r.gud_mobile2].some(v => v && String(v).toLowerCase().includes(kw))
        })
      },
      pagedRows() {
        const start = (this.rowPage - 1) * this.rowLimit
        return this.filteredRows.slice(start, start + this.rowLimit)
      },
      canNextStep2() {
        return (this.total_validate + this.total_open_lock) > 0 && (this.total_error == 0 || this.error_checked)
      },
      totalToImport() {
        return this.total_validate + (this.data_assign.allow_overwrite ? this.total_open_lock : 0)
      },
      previewOwners() {
        const owners = this.data_assign.owners || []
        const n = owners.length
        const base = Math.floor(this.totalToImport / n)
        const rest = this.totalToImport % n
        return owners.slice(0, 8).map((o, i) => ({ ...o, count: base + (i < rest ? 1 : 0) }))
      },
    },
    watch: {
      rowFilter() { this.rowPage = 1 },
      rowKeyword() { this.rowPage = 1 },
    },
    methods: {
      rowBadge: importRowBadge,
      fmt(n) { return Number(n || 0).toLocaleString('vi-VN') },
      saveBranch(data = null) {
        this.list_owner = []
        this.data_assign.owners = []
        if (data && typeof data === 'object') {
          this.branch_id = data.id
          this.error_message_upload = ''
          this.loadOwners()
        } else {
          this.branch_id = ''
        }
      },
      loadOwners() {
        return axios.g(`/api/users/get-data/users-manager?branch_id=${this.branch_id}`)
          .then(response => { this.list_owner = response.data || [] })
      },
      onDrop(e) {
        this.dragOver = false
        const f = e.dataTransfer.files && e.dataTransfer.files[0]
        if (f) this.readFile(f)
      },
      fileChanged(e) {
        const f = e.target.files && e.target.files[0]
        if (f) this.readFile(f)
        e.target.value = ''
      },
      readFile(file) {
        this.error_message_upload = ''
        if (!/\.xlsx$/i.test(file.name)) {
          this.clearFile()
          this.error_message_upload = 'Chỉ chấp nhận file Excel định dạng .xlsx (có thể tải file mẫu bên phải)'
          return
        }
        if (file.size > 15 * 1024 * 1024) {
          this.clearFile()
          this.error_message_upload = 'File quá lớn (tối đa 15MB)'
          return
        }
        this.file_name = file.name
        this.file_size = file.size
        const reader = new FileReader()
        reader.onload = ev => { this.attached_file = ev.target.result }
        reader.readAsDataURL(file)
      },
      clearFile() {
        this.attached_file = ''
        this.file_name = ''
        this.file_size = 0
      },
      applyCheckResult(d) {
        this.import_id = d.import_id
        this.list_data_check = (d.data || []).map((r, i) => ({ ...r, _no: i + 1 }))
        this.total = d.total || this.list_data_check.length
        this.total_error = d.total_error
        this.total_validate = d.total_validate
        this.total_open_lock = d.total_open_lock
        this.error_checked = false
        this.rowFilter = d.total_error > 0 ? 'error' : 'all'
        this.rowKeyword = ''
        this.rowPage = 1
        this.data_assign.allow_overwrite = true
      },
      btnUpload() {
        if (!this.branch_id) {
          this.error_message_upload = 'Vui lòng chọn trung tâm nhận dữ liệu'
          return
        }
        if (!this.attached_file) {
          this.error_message_upload = 'Vui lòng chọn file Excel'
          return
        }
        this.$vs.loading()
        axios.p('/api/crm/imports/upload', { files: this.attached_file, file_name: this.file_name, branch_id: this.branch_id })
          .then(response => {
            if (response.data.error) {
              this.error_message_upload = response.data.message
              return
            }
            this.applyCheckResult(response.data)
            this.curr_step = 2
          })
          .catch(() => { this.error_message_upload = 'Có lỗi khi tải file lên, vui lòng thử lại' })
          .finally(() => this.$vs.loading.close())
      },
      // Mở lại lượt import đang "Chờ phân chia" từ trang danh sách
      resume(import_id) {
        this.$vs.loading()
        axios.p('/api/crm/imports/detail', { import_id })
          .then(res => {
            const d = res.data
            if (d.error) {
              this.$vs.notify({ color: 'danger', title: 'Lỗi', text: d.message })
              this.$router.replace('/crm/imports')
              return
            }
            if (d.import.status == 1) {
              this.$vs.notify({ color: 'warning', title: 'Thông báo', text: 'Lượt import này đã phân chia xong' })
              this.$router.replace('/crm/imports')
              return
            }
            this.file_name = d.import.file_name
            this.branch_id = d.branch_id
            this.branch_item = { id: d.branch_id, name: d.branch_name }
            this.loadOwners()
            this.applyCheckResult(d)
            this.curr_step = 2
          })
          .finally(() => this.$vs.loading.close())
      },
      restart() {
        this.curr_step = 1
        this.clearFile()
        this.import_id = 0
        this.list_data_check = []
        this.data_assign = emptyAssign()
        this.result = {}
        this.error_message_upload = ''
        if (this.$route.query.import_id) this.$router.replace('/crm/imports/add')
        if (this.branch_list.length !== 1) {
          this.branch_item = null
          this.branch_id = ''
          this.list_owner = []
        }
      },
      exportResult() {
        window.open(`/api/export/import/${this.import_id}?token=${localStorage.getItem('accessToken')}`, '_blank')
      },
      selectAllOwners() {
        this.data_assign.owners = this.list_owner.slice()
      },
      selectSource(data = null) {
        this.data_assign.source_detail = null
        this.list_source_detail = []
        if (data && typeof data === 'object') {
          axios.g(`/api/system/source_detail?source_id=${data.id}`)
            .then(response => { this.list_source_detail = response.data || [] })
        }
      },
      assginContact() {
        const a = this.data_assign
        const errs = []
        if (!a.owners || !a.owners.length) errs.push('Người phụ trách không được để trống')
        if (!a.source) errs.push('Nguồn dữ liệu không được để trống')
        if (errs.length) {
          a.error_message = errs.map(e => ' - ' + e).join('<br/>')
          return
        }
        a.error_message = ''
        this.$vs.loading()
        axios.p('/api/crm/imports/assign', {
          import_id: this.import_id,
          owners_id: a.owners.map(o => o.id),
          source_id: a.source.id,
          source_detail_id: a.source_detail ? a.source_detail.id : 0,
          prefer_file_owner: a.prefer_file_owner,
          allow_overwrite: a.allow_overwrite,
        })
          .then(response => {
            if (response.data.error) {
              a.error_message = response.data.message
              return
            }
            this.result = response.data
            this.curr_step = 4
          })
          .catch(() => { a.error_message = 'Có lỗi xảy ra, vui lòng thử lại' })
          .finally(() => this.$vs.loading.close())
      },
    },
    created() {
      axios.g('/api/system/sources').then(response => { this.list_source = response.data || [] })
      const resumeId = parseInt(this.$route.query.import_id, 10)
      axios.g('/api/system/branches-has-user').then(response => {
        this.branch_list = response.data || []
        // User chỉ thuộc 1 trung tâm → chọn sẵn, không bắt chọn lại
        if (!resumeId && this.branch_list.length === 1) {
          this.branch_item = this.branch_list[0]
          this.saveBranch(this.branch_item)
        }
      })
      if (resumeId) this.resume(resumeId)
    },
  }
</script>

<style>
/* Dùng chung class .imp-* với trang danh sách (index.vue) */
.imp { font-size: 14px; color: #1e293b; }
.imp .nowrap { white-space: nowrap !important; }
.imp .tc { text-align: center; }
.imp .req { color: #dc2626; }
.imp-sub { color: #64748b; font-size: 13px; }
.imp-steps { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 8px; padding-bottom: 20px; border-bottom: 1px solid #eef2f7; }
@media (max-width: 800px) { .imp-steps { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
.imp-step { display: flex; align-items: center; gap: 12px; position: relative; }
.imp-step__dot { width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: #f1f5f9; color: #94a3b8; font-weight: 700; font-size: 15px; border: 2px solid #e2e8f0; }
.imp-step.is-active .imp-step__dot { background: #7367f0; color: #fff; border-color: #7367f0; box-shadow: 0 0 0 4px rgba(115, 103, 240, .18); }
.imp-step.is-done .imp-step__dot { background: #16a34a; color: #fff; border-color: #16a34a; }
.imp-step__title { font-weight: 700; font-size: 15px; color: #94a3b8; }
.imp-step.is-active .imp-step__title, .imp-step.is-done .imp-step__title { color: #1e293b; }
.imp-step__sub { font-size: 13px; color: #94a3b8; }
.imp-body { padding-top: 22px; }
.imp-grid { display: grid; grid-template-columns: minmax(0, 1fr) 360px; gap: 24px; }
@media (max-width: 1000px) { .imp-grid { grid-template-columns: 1fr; } }
.imp-field { display: flex; flex-direction: column; }
.imp-field > label { font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; }
.imp-link { margin-left: 12px; font-weight: 600; color: #7367f0; font-size: 13px; }
.imp-fixed { height: 40px; display: flex; align-items: center; gap: 8px; padding: 0 12px; font-size: 14px; font-weight: 600; color: #94a3b8; background: #f1f5f9; border: 1px dashed #cbd5e1; border-radius: 6px; white-space: nowrap; cursor: not-allowed; }
.imp-input { height: 40px; padding: 0 12px; font-size: 14px; background: #fff; border: 1.5px solid #cbd5e1; border-radius: 6px; box-shadow: 0 1px 3px rgba(0, 0, 0, .05); outline: none; color: #1e293b; width: 100%; }
.imp-input:hover, .imp-input:focus { border-color: #7367f0; box-shadow: 0 0 0 3px rgba(115, 103, 240, .2); }
.imp .v-select .vs__dropdown-toggle { min-height: 40px; background: #fff; border: 1.5px solid #cbd5e1; cursor: pointer; }
.imp .v-select.vs--disabled .vs__dropdown-toggle { background: #f1f5f9 !important; border: 1px dashed #cbd5e1 !important; cursor: not-allowed !important; }
.imp .v-select .vs__search, .imp .multiselect .multiselect__input { border: none !important; background: transparent !important; box-shadow: none !important; }
.imp .multiselect__tags { min-height: 42px; border: 1.5px solid #cbd5e1; font-size: 14px; }
.imp .multiselect__tag { font-size: 13px; background: #7367f0; }
.imp .multiselect__option { font-size: 14px; }
.imp .multiselect--above .multiselect__content-wrapper { z-index: 20; }
.imp-drop { border: 2px dashed #c7d2fe; border-radius: 12px; background: #fafaff; padding: 36px 20px; text-align: center; cursor: pointer; transition: all .15s; }
.imp-drop:hover, .imp-drop.is-over { border-color: #7367f0; background: #f5f3ff; }
.imp-drop.has-file { border-style: solid; border-color: #86efac; background: #f0fdf4; }
.imp-drop__icon { font-size: 42px; color: #7367f0; margin-bottom: 10px; }
.imp-drop__title { font-size: 15px; font-weight: 600; word-break: break-all; }
.imp-drop__sub { font-size: 13px; color: #64748b; margin-top: 4px; }
.imp-drop__sub a { color: #7367f0; font-weight: 600; }
.imp-error { color: #dc2626; font-size: 13.5px; margin-top: 10px; }
.imp-actions { display: flex; flex-wrap: wrap; gap: 10px; justify-content: flex-end; margin-top: 22px; }
.imp-guide { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px; font-size: 13.5px; align-self: start; }
.imp-guide__title { font-size: 15px; font-weight: 700; margin-bottom: 10px; }
.imp-guide__title i { color: #7367f0; margin-right: 4px; }
.imp-guide__dl { display: inline-flex; align-items: center; gap: 6px; background: #16a34a; color: #fff !important; padding: 7px 14px; border-radius: 6px; font-weight: 600; font-size: 13px; margin-bottom: 10px; }
.imp-guide ol { padding-left: 18px; margin: 0; }
.imp-guide li { margin-bottom: 6px; line-height: 1.5; list-style: decimal; }
.imp-sum-row { display: flex; justify-content: space-between; gap: 10px; padding: 7px 0; border-bottom: 1px dashed #e2e8f0; }
.imp-sum-row--total { border-bottom: 0; font-size: 15px; }
.imp-sum-row--total b { color: #7367f0; font-size: 17px; }
.imp-head { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
.imp-head__title { font-size: 17px; font-weight: 700; }
.imp-head__sub { font-size: 13px; color: #64748b; margin-top: 2px; }
.imp-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 12px; margin-top: 16px; }
.imp-stat { border-radius: 10px; padding: 14px 16px; cursor: pointer; border: 2px solid transparent; transition: all .15s; text-align: left; }
.imp-stat__val { font-size: 24px; font-weight: 700; line-height: 1.2; }
.imp-stat__lbl { font-size: 13px; font-weight: 600; }
.imp-stat--all { background: #f1f5f9; color: #334155; }
.imp-stat--ok { background: #dcfce7; color: #15803d; }
.imp-stat--warn { background: #e0e7ff; color: #4338ca; }
.imp-stat--err { background: #fee2e2; color: #b91c1c; }
.imp-stat.is-active { border-color: currentColor; box-shadow: 0 4px 12px rgba(15, 23, 42, .08); }
.imp-toolbar { display: flex; align-items: center; gap: 12px; margin-top: 16px; }
.imp-table-wrap { overflow: auto; margin-top: 12px; border: 1px solid #e2e8f0; border-radius: 8px; }
.imp-table { width: 100%; border-collapse: collapse; font-size: 14px; }
.imp-table th { position: sticky; top: 0; z-index: 1; background: #f8fafc; color: #475569; font-weight: 600; font-size: 13px; text-align: left; padding: 11px 12px; border-bottom: 1px solid #e2e8f0; white-space: nowrap; }
.imp-table th.tc { text-align: center; }
.imp-table td { padding: 9px 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.imp-table tbody tr:hover td { background: #faf9ff; }
.imp-msg { font-size: 13px; color: #b45309; min-width: 240px; }
.imp-badge { display: inline-flex !important; align-items: center; gap: 5px; white-space: nowrap !important; padding: 3px 10px; border-radius: 999px; font-size: 13px; font-weight: 600; }
.imp-badge--ok { background: #dcfce7; color: #15803d; }
.imp-badge--warn { background: #e0e7ff; color: #4338ca; }
.imp-badge--err { background: #fee2e2; color: #b91c1c; }
.imp-badge--muted { background: #f1f5f9; color: #475569; }
.imp-act { display: inline-flex; align-items: center; gap: 6px; height: 34px; padding: 0 12px; border-radius: 6px; color: #7367f0; background: #f5f3ff; cursor: pointer; font-size: 13px; font-weight: 600; }
.imp-act:hover { background: #ede9fe; }
.imp-empty { text-align: center; color: #94a3b8; padding: 30px 0 !important; }
.imp-confirm { margin-top: 16px; padding: 12px 14px; background: #fff7ed; border: 1px solid #fed7aa; border-radius: 8px; font-size: 14px; }
.imp-opts { display: flex; flex-direction: column; gap: 10px; font-size: 14px; }
.imp-opts .vs-checkbox--input, .imp-confirm .vs-checkbox--input { cursor: pointer; }
.imp-done__icon { width: 72px; height: 72px; margin: 6px auto 14px; border-radius: 50%; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 32px; }
</style>
