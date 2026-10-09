<template>
  <div id="page-group-tutor">
    <vx-card no-shadow class="rpt-card">
      <div class="mb-6 flex items-center">
        <div class="mr-3 flex items-center justify-center p-3 rounded-lg" style="background:rgba(79, 70, 229, 0.1); color:#4f46e5;">
          <i class="fa fa-users text-xl"></i>
        </div>
        <div>
          <h3 class="text-lg font-bold uppercase" style="color:#4f46e5; margin:0;">QUẢN LÝ BỔ TRỢ NHÓM</h3>
        </div>
      </div>
      <div class="rpt-filter-grid mb-5">
        <div v-show="false">
          <label class="rpt-label">Trung tâm</label>
          <multiselect name="search_branch" placeholder="Chọn trung tâm" v-model="searchData.arr_branch" :options="branch_list" label="name" :close-on-select="false" :hide-selected="true" :multiple="true" :searchable="true" track-by="id" selectedLabel="" selectLabel="" deselectLabel=""><span slot="noResult">Không tìm thấy</span></multiselect>
        </div>
        <div>
          <label class="rpt-label">Tên lớp</label>
          <vs-input class="w-full" placeholder="Nhập tên lớp..." v-model="searchData.class_name" @keyup.enter="getData()"></vs-input>
        </div>
        <div>
          <label class="rpt-label">Trợ giảng</label>
          <multiselect
            placeholder="Chọn trợ giảng"
            v-model="searchData.ta"
            :options="ta_list"
            label="display_name"
            track-by="id"
            :searchable="true"
            :close-on-select="true"
            :clear-on-select="false"
            selectedLabel="" selectLabel="" deselectLabel="">
            <span slot="noResult">Không tìm thấy</span>
          </multiselect>
        </div>
        <div>
          <label class="rpt-label">Thanh tra</label>
          <multiselect
            placeholder="Tất cả"
            v-model="searchData.is_inspected"
            :options="inspected_options"
            label="label"
            track-by="value"
            :searchable="false"
            :close-on-select="true"
            selectedLabel="" selectLabel="" deselectLabel="">
          </multiselect>
        </div>
        <div>
          <label class="rpt-label">Đã đọc NX</label>
          <multiselect
            placeholder="Tất cả"
            v-model="searchData.is_note_read"
            :options="note_read_options"
            label="label"
            track-by="value"
            :searchable="false"
            :close-on-select="true"
            selectedLabel="" selectLabel="" deselectLabel="">
          </multiselect>
        </div>
        <div>
          <label class="rpt-label">Ngày dự kiến</label>
          <date-picker style="width:100%" v-model="searchData.dateRange" type="date" range :clearable="true" format="YYYY-MM-DD" :lang="datepickerOptions.lang" placeholder="Từ ngày — Đến ngày"></date-picker>
        </div>
      </div>
      <div class="rpt-actions mb-5">
        <vs-button class="rpt-btn" @click="getData"><i class="fa fa-search"></i> Tìm kiếm</vs-button>
        <vs-button color="dark" type="border" class="rpt-btn" @click="reset"><i class="fas fa-undo-alt"></i> Hủy</vs-button>
        <span class="rpt-badge-count">{{ pagination.total }} bản ghi</span>
      </div>

      <div class="rpt-table-wrap">
        <table class="rpt-table">
          <thead>
            <tr>
              <th class="text-center">STT</th>
              <th>Họ và tên trợ giảng</th>
              <th>Level</th>
              <th>Lớp</th>
              <th class="text-center">Nhóm kèm</th>
              <th class="text-center">Đợt kèm</th>
              <th>Ngày dự kiến</th>
              <th>Thời gian dự kiến</th>
              <th>Mong muốn hỗ trợ</th>
              <th>Tình hình check LP</th>
              <th>Link record</th>
              <th>Nhận xét</th>
              <th class="text-center">Thanh tra</th>
              <th class="text-center">Đã đọc NX</th>
              <th class="text-center" v-if="!is_ta">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr class="rpt-row" v-for="(item, index) in datas" :key="index">
              <td class="text-center">{{ index + 1 + (pagination.cpage - 1) * pagination.limit }}</td>
              <td>
                <div class="font-semibold">{{ item.ta_name }}</div>
                <div class="text-xs text-gray-500">{{ item.ta_hrm_id }}</div>
              </td>
              <td>{{ item.product_name }}</td>
              <td><span class="badge-code">{{ item.cls_name }}</span></td>
              <td class="text-center">{{ item.group_name }}</td>
              <td class="text-center font-bold">Đợt {{ item.session_index }}</td>
              <td>{{ item.expected_date }}</td>
              <td>{{ item.expected_time }}</td>
              <td class="small">{{ item.support_request }}</td>
              <td class="small">{{ item.lp_check_status || 'Chưa check' }}</td>
              <td>
                <a v-if="item.record_link" :href="item.record_link" target="_blank" class="text-primary hover:underline">Xem Link</a>
              </td>
              <td class="small">{{ item.record_note }}</td>
              <td class="text-center">
                <vs-checkbox disabled v-model="item.is_inspected" :val="1"></vs-checkbox>
              </td>
              <td class="text-center">
                <vs-checkbox disabled v-model="item.is_note_read" :val="1"></vs-checkbox>
              </td>
              <td class="text-center" v-if="!is_ta">
                <vs-button size="small" color="primary" @click="openEditModal(item)" title="Cập nhật">
                  <i class="fa fa-edit"></i>
                </vs-button>
              </td>
            </tr>
            <tr v-if="!datas.length"><td :colspan="is_ta ? 14 : 15" class="text-center p-4">Không có dữ liệu</td></tr>
          </tbody>
        </table>
      </div>

      <div class="rpt-paging" v-if="pagination.total > 0">
        <vs-dropdown vs-trigger-click class="cursor-pointer mr-4">
          <div class="paging-limit-btn">
            <span>{{ pagination.cpage * pagination.limit - (pagination.limit - 1) }} – {{ Math.min(pagination.cpage * pagination.limit, pagination.total) }} / {{ pagination.total }}</span>
            <feather-icon icon="ChevronDownIcon" svgClasses="h-4 w-4" />
          </div>
          <vs-dropdown-menu>
            <vs-dropdown-item v-for="item in limitSource" :key="item" @click="changePageLimit(item)">{{ item }}</vs-dropdown-item>
          </vs-dropdown-menu>
        </vs-dropdown>
        <vs-pagination style="width:calc(100% - 180px);" v-if="Math.ceil(pagination.total / pagination.limit) > 1" :total="Math.ceil(pagination.total / pagination.limit)" :max="7" v-model="pagination.cpage" @change="changePage()"/>
      </div>
    </vx-card>

    <vs-popup title="Cập nhật thông tin Bổ trợ Nhóm" :active.sync="isEditModalActive" width="600px">
      <div v-if="selectedItem" class="p-2">
        <div class="mb-4 text-primary font-semibold">
          Lớp: {{ selectedItem.cls_name }} - {{ selectedItem.group_name }} - Đợt {{ selectedItem.session_index }}
        </div>
        <div class="vx-row mb-4">
          <div class="vx-col sm:w-1/2 w-full mb-2">
            <label class="vs-input--label">Ngày dự kiến</label>
            <vs-input type="date" v-model="editForm.expected_date" class="w-full" />
          </div>
          <div class="vx-col sm:w-1/2 w-full mb-2">
            <label class="vs-input--label">Thời gian dự kiến (Giờ:Phút)</label>
            <vs-input type="time" v-model="editForm.expected_time" class="w-full" />
          </div>
        </div>
        <div class="mb-4">
          <label class="vs-input--label">Mong muốn hỗ trợ</label>
          <vs-textarea v-model="editForm.support_request" />
        </div>
        <div class="mb-4">
          <label class="vs-input--label">Tình hình check LP</label>
          <div>
            <select class="vs-inputx vs-input--input normal select-lp" v-model="editForm.lp_check_status">
              <option value="Chưa check">Chưa check</option>
              <option value="Đã check">Đã check</option>
            </select>
          </div>
        </div>
        <div class="mb-4">
          <label class="vs-input--label">Link record</label>
          <vs-input v-model="editForm.record_link" class="w-full" />
        </div>
        <div class="mb-4">
          <label class="vs-input--label">Nhận xét record kèm nhóm</label>
          <vs-textarea v-model="editForm.record_note" />
        </div>
        <div class="mb-4 flex gap-4">
          <vs-checkbox v-model="editForm.is_inspected">THANH TRA</vs-checkbox>
          <vs-checkbox v-model="editForm.is_note_read">ĐÃ ĐỌC NX</vs-checkbox>
        </div>
        <div class="flex justify-end mt-6">
          <vs-button color="primary" @click="saveUpdate()">Lưu thông tin</vs-button>
        </div>
      </div>
    </vs-popup>

  </div>
</template>

<script>
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.min.css'
import DatePicker from 'vue2-datepicker'
import axios from '../../../http/axios.js'

export default {
  components: { Multiselect, DatePicker },
  data() {
    return {
      branch_list: [],
      ta_list: [],
      inspected_options: [
        { label: 'Tất cả', value: '' },
        { label: 'Đã thanh tra', value: 1 },
        { label: 'Chưa thanh tra', value: 0 }
      ],
      note_read_options: [
        { label: 'Tất cả', value: '' },
        { label: 'Đã đọc NX', value: 1 },
        { label: 'Chưa đọc NX', value: 0 }
      ],
      searchData: { arr_branch:'', class_name:'', ta:null, is_inspected:null, is_note_read:null, dateRange:'' },
      datepickerOptions: { lang: { days:['CN','T2','T3','T4','T5','T6','T7'], months:['Tháng 1','Tháng 2','Tháng 3','Tháng 4','Tháng 5','Tháng 6','Tháng 7','Tháng 8','Tháng 9','Tháng 10','Tháng 11','Tháng 12'] } },
      datas: [],
      pagination: { cpage: 1, limit: 20, total: 0, init: 0 },
      limitSource: [20, 50, 100, 500],
      isEditModalActive: false,
      selectedItem: null,
      is_ta: false,
      editForm: {
        expected_date: '', expected_time: '', support_request: '', lp_check_status: 'Chưa check', record_link: '', record_note: '', is_inspected: false, is_note_read: false
      }
    }
  },
  created() {
    axios.g('/api/system/branches-has-user').then(r => { this.branch_list = r.data })
    axios.g('/api/lms/teaching-assistants/all-active').then(r => {
      this.ta_list = (r.data || []).map(item => ({
        id: item.id,
        full_name: item.full_name,
        hrm_id: item.hrm_id,
        display_name: item.hrm_id ? `${item.full_name} (${item.hrm_id})` : item.full_name
      }))
    })
    this.getData()
  },
  methods: {
    reset() {
      this.searchData = { arr_branch:'', class_name:'', ta:null, is_inspected:null, is_note_read:null, dateRange:'' }
      this.getData()
    },
    openEditModal(item) {
      this.selectedItem = item;
      this.editForm = {
        expected_date: item.expected_date || '',
        expected_time: item.expected_time || '',
        support_request: item.support_request || '',
        lp_check_status: item.lp_check_status || 'Chưa check',
        record_link: item.record_link || '',
        record_note: item.record_note || '',
        is_inspected: item.is_inspected == 1,
        is_note_read: item.is_note_read == 1
      };
      this.isEditModalActive = true;
    },
    saveUpdate() {
      this.$vs.loading();
      axios.p(`/api/lms/teaching-assistants/update-group-tutoring/${this.selectedItem.id}`, this.editForm)
        .then(res => {
          this.$vs.loading.close();
          if (res.data.status === 1) {
            this.$vs.notify({ title: 'Thành công', text: res.data.message, color: 'success', iconPack: 'feather', icon: 'icon-check' });
            this.isEditModalActive = false;
            this.getData();
          } else {
            this.$vs.notify({ title: 'Lỗi', text: res.data.message, color: 'danger', iconPack: 'feather', icon: 'icon-alert-circle' });
          }
        }).catch(e => {
          this.$vs.loading.close();
        });
    },
    getData() {
      const ids = []
      if (this.searchData.arr_branch && this.searchData.arr_branch.length) this.searchData.arr_branch.forEach(i => ids.push(i.id))
      const start_date = this.searchData.dateRange && this.searchData.dateRange[0] ? this.searchData.dateRange[0] : ''
      const end_date = this.searchData.dateRange && this.searchData.dateRange[1] ? this.searchData.dateRange[1] : ''
      const ta_id = this.searchData.ta ? this.searchData.ta.id : ''
      const is_inspected = (this.searchData.is_inspected && this.searchData.is_inspected.value !== '') ? this.searchData.is_inspected.value : ''
      const is_note_read = (this.searchData.is_note_read && this.searchData.is_note_read.value !== '') ? this.searchData.is_note_read.value : ''

      const data = {
        class_name: this.searchData.class_name,
        ta_id: ta_id,
        is_inspected: is_inspected,
        is_note_read: is_note_read,
        branch_id: ids,
        start_date: start_date,
        end_date: end_date,
        pagination: this.pagination
      }
      this.$vs.loading()
      axios.p('/api/lms/teaching-assistants/group-tutorings', data).then(res => { 
        this.$vs.loading.close(); 
        this.datas = res.data.list; 
        this.pagination = res.data.paging; 
        this.is_ta = res.data.is_ta || false;
        setTimeout(() => { this.pagination.init = 1 }, 500) 
      }).catch(e => { this.$vs.loading.close() })
    },
    changePage() { if (this.pagination.init) this.getData() },
    changePageLimit(limit) { this.pagination.cpage = 1; this.pagination.limit = limit; this.getData() }
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
#page-group-tutor { font-family: 'Inter', sans-serif; }
.rpt-header { display:flex; align-items:center; gap:16px; background:linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%); color:white; padding:20px 24px; border-radius:12px; box-shadow:0 4px 20px rgba(79,70,229,.3); margin-bottom:20px; }
.rpt-header__icon { font-size:26px; width:50px; height:50px; background:rgba(255,255,255,.2); border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.rpt-header__title { font-size:1.05rem; font-weight:700; margin:0; }
.rpt-header__sub { font-size:.82rem; opacity:.8; margin:3px 0 0; }
.rpt-card { border-radius:12px !important; box-shadow:0 2px 16px rgba(0,0,0,.06) !important; }
.rpt-filter-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:16px; }
.rpt-label { display:block; font-size:.75rem; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.05em; margin-bottom:6px; }
.rpt-actions { display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
.rpt-btn { border-radius:8px !important; }
.rpt-badge-count { margin-left:auto; background:#eef2ff; color:#4338ca; padding:6px 14px; border-radius:20px; font-weight:600; font-size:.85rem; }
.rpt-table-wrap { overflow-x:auto; max-height:75vh; overflow-y:auto; border-radius:10px; border:1px solid #e5e7eb; }
.rpt-table { width:100%; border-collapse:collapse; font-size:14px; }
.rpt-table thead tr { background:linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%); }
.rpt-table thead th { color:white; font-weight:600; padding:11px 10px; white-space:nowrap; border:1px solid rgba(255,255,255,0.2); position:sticky; top:0; z-index:2; background:linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%); }
.rpt-row { border-bottom:1px solid #f3f4f6; transition:background .15s; }
.rpt-row:hover { background:#f8f7ff; }
.rpt-row td { padding:9px 10px; vertical-align:middle; border:1px solid #e5e7eb; }
.badge-code { background:#eef2ff; color:#4338ca; border-radius:6px; padding:2px 8px; font-weight:600; }
.small { font-size:13px; }
.rpt-paging { display:flex; align-items:center; flex-wrap:wrap; margin-top:16px; }
.paging-limit-btn { display:flex; align-items:center; gap:8px; padding:8px 14px; border:1px solid #e5e7eb; border-radius:8px; cursor:pointer; background:white; font-size:.85rem; font-weight:500; }
.select-lp { width:180px; max-width:100%; height:38px; padding:0 10px; border:1px solid rgba(0,0,0,0.2); border-radius:5px; background-color:#fff; cursor:pointer; }
.select-lp:focus { border-color:#4f46e5; outline:none; }
</style>
