<template>
  <div id="settings-sources" class="ev-page">
    <div class="src-layout">
      <!-- ============ CỘT TRÁI: NGUỒN ============ -->
      <div class="ev-card src-side">
        <div class="src-side__head">
          <div>
            <h4 class="ev-hero__title" style="font-size: 17px;">Nguồn khách hàng</h4>
            <p class="ev-hero__sub">{{ sources.length }} nguồn · chọn để xem nguồn chi tiết</p>
          </div>
          <button class="ev-btn ev-btn--primary ev-btn--sm" @click="openSourceModal()">
            <i class="feather icon-plus"></i> Thêm nguồn
          </button>
        </div>

        <div v-if="loadingSources" class="ev-state"><div class="ev-spinner"></div>Đang tải...</div>
        <div v-else class="src-list">
          <div
            v-for="s in sources"
            :key="s.id"
            class="src-item"
            :class="{ 'is-active': selected && selected.id == s.id, 'is-off': s.status != 1 }"
            @click="selectSource(s)"
          >
            <div class="src-item__main">
              <div class="src-item__name">
                {{ s.name }}
                <span v-if="s.is_campaign_source" class="ev-badge ev-badge--info no-dot src-mini">Chiến dịch</span>
                <span v-if="s.status != 1" class="ev-badge ev-badge--gray no-dot src-mini">Ngừng</span>
              </div>
              <div class="src-item__meta">
                <span><i class="feather icon-git-branch"></i> {{ s.detail_active }}/{{ s.detail_total }} chi tiết</span>
                <span><i class="feather icon-users"></i> {{ s.parent_total }} KH</span>
              </div>
            </div>
            <div class="src-item__acts" @click.stop>
              <button class="ev-act ev-act--primary" title="Sửa nguồn" @click="openSourceModal(s)"><i class="feather icon-edit-2"></i></button>
              <button v-if="!s.is_campaign_source" class="ev-act ev-act--danger" title="Xóa nguồn" @click="confirmDeleteSource(s)"><i class="feather icon-trash-2"></i></button>
            </div>
          </div>
        </div>
      </div>

      <!-- ============ CỘT PHẢI: NGUỒN CHI TIẾT ============ -->
      <div class="ev-card src-main">
        <div class="ev-hero">
          <div class="ev-hero__main">
            <span class="ev-hero__icon"><i class="feather icon-share-2"></i></span>
            <div>
              <h4 class="ev-hero__title">Nguồn chi tiết<span v-if="selected"> · {{ selected.name }}</span></h4>
              <p class="ev-hero__sub">Kênh cụ thể của từng nguồn (trường học, tờ rơi, sự kiện…) – hiển thị khi chọn nguồn ở form khách hàng</p>
            </div>
          </div>
          <div class="ev-hero__actions">
            <button class="ev-btn ev-btn--primary" :disabled="!selected || isCampaign" @click="openDetailModal()">
              <i class="feather icon-plus"></i> Thêm nguồn chi tiết
            </button>
          </div>
        </div>

        <div v-if="selected && isCampaign" class="src-notice">
          <i class="feather icon-info"></i>
          <div>
            Nguồn chi tiết của <strong>{{ selected.name }}</strong> được tạo tự động khi tạo chiến dịch.
            Vui lòng thêm/sửa tại
            <router-link :to="campaignLink">module Marketing</router-link>.
          </div>
        </div>

        <div class="ev-filter">
          <div>
            <label class="ev-field__label">Tìm kiếm</label>
            <div class="ev-search">
              <i class="feather icon-search"></i>
              <input v-model="filters.keyword" class="ev-input" placeholder="Tên nguồn chi tiết..." @keyup.enter="reloadDetails" />
            </div>
          </div>
          <div>
            <label class="ev-field__label">Trung tâm áp dụng</label>
            <multiselect v-model="filters.branch" :options="branchFilterOptions" label="name" track-by="id"
              placeholder="Tất cả" selectLabel="" deselectLabel="Bỏ chọn" :searchable="false" @input="reloadDetails" />
          </div>
          <div>
            <label class="ev-field__label">Trạng thái</label>
            <multiselect v-model="filters.status" :options="statusOptions" label="label" track-by="value"
              placeholder="Tất cả" selectLabel="" deselectLabel="Bỏ chọn" :searchable="false" @input="reloadDetails" />
          </div>
          <div class="ev-filter__actions">
            <button class="ev-btn ev-btn--dark" @click="reloadDetails"><i class="feather icon-search"></i> Tìm</button>
            <button class="ev-btn ev-btn--ghost" @click="resetFilters"><i class="feather icon-rotate-ccw"></i></button>
          </div>
        </div>

        <div class="ev-table-wrap">
          <table class="ev-table">
            <thead>
              <tr>
                <th class="col-stt">#</th>
                <th>Nguồn chi tiết</th>
                <th class="col-wide">Trung tâm áp dụng</th>
                <th class="is-center">Khách hàng / HS</th>
                <th class="is-center">Trạng thái</th>
                <th>Ngày tạo</th>
                <th class="is-center">Thao tác</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loadingDetails">
                <td colspan="7" class="ev-state"><div class="ev-spinner"></div>Đang tải dữ liệu...</td>
              </tr>
              <tr v-else-if="!selected">
                <td colspan="7" class="ev-state"><i class="feather icon-arrow-left"></i>Chọn một nguồn ở cột bên trái</td>
              </tr>
              <tr v-else-if="details.length === 0">
                <td colspan="7" class="ev-state">
                  <i class="feather icon-inbox"></i>
                  <div class="ev-state__title">Chưa có nguồn chi tiết</div>
                  <div v-if="!isCampaign" class="ev-state__sub">Bấm “Thêm nguồn chi tiết” – có thể nhập nhiều dòng cùng lúc.</div>
                </td>
              </tr>
              <tr v-for="(d, i) in details" v-else :key="d.id" :class="{ 'src-row-off': d.status != 1 }">
                <td class="col-stt">{{ (paging.cpage - 1) * paging.limit + i + 1 }}</td>
                <td>
                  <div class="ev-strong">{{ d.name }}</div>
                  <div v-if="d.campaign_id" class="ev-person__meta">Chiến dịch #{{ d.campaign_id }}</div>
                </td>
                <td>
                  <span v-if="d.branch_name" class="src-branch">{{ d.branch_name }}</span>
                  <span v-else class="ev-badge ev-badge--primary no-dot">Tất cả trung tâm</span>
                </td>
                <td class="is-center">
                  <span class="ev-score" :class="{ 'ev-muted': !(d.parent_total + d.student_total) }">{{ Number(d.parent_total) + Number(d.student_total) }}</span>
                </td>
                <td class="is-center">
                  <button
                    class="src-switch"
                    :class="{ 'is-on': d.status == 1 }"
                    :disabled="!!d.is_campaign"
                    :title="d.is_campaign ? 'Quản lý trong module Marketing' : (d.status == 1 ? 'Bấm để ngừng sử dụng' : 'Bấm để kích hoạt')"
                    @click="toggleDetail(d)"
                  >
                    <span></span>{{ d.status == 1 ? 'Đang dùng' : 'Ngừng' }}
                  </button>
                </td>
                <td>
                  <div>{{ fmtDate(d.created_at) }}</div>
                  <div v-if="d.creator_name" class="ev-person__meta">{{ d.creator_name }}</div>
                </td>
                <td class="is-center">
                  <div v-if="!d.is_campaign" class="ev-actions">
                    <button class="ev-act ev-act--primary" title="Sửa" @click="openDetailModal(d)"><i class="feather icon-edit-2"></i></button>
                    <button class="ev-act ev-act--danger" title="Xóa" @click="confirmDeleteDetail(d)"><i class="feather icon-trash-2"></i></button>
                  </div>
                  <span v-else class="ev-muted">—</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <eval-table-foot :page="paging.cpage" :per-page="paging.limit" :total="paging.total" unit="nguồn chi tiết"
          @page="p => { paging.cpage = p; loadDetails() }" @per-page="n => { paging.limit = n; reloadDetails() }" />
      </div>
    </div>

    <!-- Modal nguồn -->
    <vs-popup class="ev-modal ev-page" :title="sourceForm.id ? 'Cập nhật nguồn' : 'Thêm nguồn khách hàng'" :active.sync="showSourceModal">
      <div class="ev-form-grid">
        <div class="is-full">
          <label class="ev-field__label">Tên nguồn <span class="ev-req">*</span></label>
          <input v-model.trim="sourceForm.name" class="ev-input" placeholder="VD: Đi thị trường" />
        </div>
        <div class="is-full">
          <label class="ev-field__label">Mô tả</label>
          <input v-model.trim="sourceForm.description" class="ev-input" placeholder="Ghi chú (không bắt buộc)" />
        </div>
        <div class="is-full">
          <label class="src-check">
            <input v-model="sourceForm.status" type="checkbox" :true-value="1" :false-value="0" />
            Đang sử dụng (hiển thị khi chọn nguồn ở form khách hàng)
          </label>
        </div>
      </div>
      <div class="ev-modal__foot">
        <button class="ev-btn ev-btn--ghost" @click="showSourceModal = false">Hủy</button>
        <button class="ev-btn ev-btn--primary" :disabled="saving" @click="saveSource"><i class="feather icon-check"></i> Lưu</button>
      </div>
    </vs-popup>

    <!-- Modal nguồn chi tiết -->
    <vs-popup class="ev-modal ev-page" :title="detailForm.id ? 'Cập nhật nguồn chi tiết' : 'Thêm nguồn chi tiết'" :active.sync="showDetailModal">
      <div class="ev-form-grid">
        <div>
          <label class="ev-field__label">Nguồn <span class="ev-req">*</span></label>
          <multiselect v-model="detailForm.source" :options="editableSources" label="name" track-by="id"
            placeholder="Chọn nguồn" selectLabel="" deselectLabel="" :allow-empty="false" />
        </div>
        <div>
          <label class="ev-field__label">Trung tâm áp dụng</label>
          <multiselect v-model="detailForm.branch" :options="branchOptions" label="name" track-by="id"
            placeholder="Tất cả trung tâm" selectLabel="" deselectLabel="" :allow-empty="false" :searchable="false" />
        </div>
        <div class="is-full">
          <label class="ev-field__label">
            Tên nguồn chi tiết <span class="ev-req">*</span>
          </label>
          <input v-if="detailForm.id" v-model.trim="detailForm.name" class="ev-input" />
          <template v-else>
            <textarea v-model="detailForm.names" class="ev-input src-textarea" rows="6"
              placeholder="Mỗi dòng một nguồn chi tiết, ví dụ:&#10;Trường MN Trạng Nguyên&#10;Tờ rơi chung cư"></textarea>
            <div class="ev-hint">Sẽ tạo <strong>{{ parsedNames.length }}</strong> nguồn chi tiết. Dòng trùng hoặc đã tồn tại sẽ được bỏ qua.</div>
          </template>
        </div>
        <div class="is-full">
          <label class="src-check">
            <input v-model="detailForm.status" type="checkbox" :true-value="1" :false-value="0" />
            Đang sử dụng
          </label>
        </div>
      </div>
      <div class="ev-modal__foot">
        <button class="ev-btn ev-btn--ghost" @click="showDetailModal = false">Hủy</button>
        <button class="ev-btn ev-btn--primary" :disabled="saving" @click="saveDetail"><i class="feather icon-check"></i> Lưu</button>
      </div>
    </vs-popup>
  </div>
</template>

<script>
import axios from '@/http/axios.js'
import multiselect from 'vue-multiselect'
import '@/views/lms/evaluations/_shared/eval-ui.css'
import EvalTableFoot from '@/views/lms/evaluations/_shared/EvalTableFoot.vue'
import { fmtDate } from '@/views/lms/evaluations/_shared/helpers'

const CAMPAIGN_LINKS = { 1: '/marketing/campaigns', 2: '/marketing/b2b/sources', 3: '/marketing/c2c/campaigns' }

export default {
  components: { multiselect, EvalTableFoot },
  data() {
    return {
      sources: [],
      branches: [],
      selected: null,
      details: [],
      loadingSources: false,
      loadingDetails: false,
      saving: false,
      filters: { keyword: '', branch: null, status: null },
      statusOptions: [{ value: 1, label: 'Đang dùng' }, { value: 0, label: 'Ngừng sử dụng' }],
      paging: { cpage: 1, limit: 20, total: 0 },
      showSourceModal: false,
      sourceForm: { id: 0, name: '', description: '', status: 1 },
      showDetailModal: false,
      detailForm: { id: 0, source: null, branch: null, name: '', names: '', status: 1 }
    }
  },
  computed: {
    isCampaign() {
      return !!(this.selected && this.selected.is_campaign_source)
    },
    campaignLink() {
      return (this.selected && CAMPAIGN_LINKS[this.selected.id]) || '/marketing/campaigns'
    },
    branchOptions() {
      return [{ id: 0, name: 'Tất cả trung tâm' }, ...this.branches]
    },
    branchFilterOptions() {
      return [{ id: 0, name: 'Chỉ nguồn dùng chung' }, ...this.branches]
    },
    editableSources() {
      return this.sources.filter(s => !s.is_campaign_source)
    },
    parsedNames() {
      const seen = {}
      return String(this.detailForm.names || '')
        .split(/\r?\n/)
        .map(s => s.replace(/^[\s,;•\-–]+|[\s,;]+$/g, ''))
        .filter(s => s && !seen[s.toLowerCase()] && (seen[s.toLowerCase()] = true))
    }
  },
  created() {
    this.loadSources(true)
    axios.g('/api/system/branches').then(res => {
      this.branches = (res && res.data ? res.data : []).map(b => ({ id: b.id, name: b.name }))
    })
  },
  methods: {
    fmtDate,
    notify(res, okColor = 'success') {
      const d = res && res.data ? res.data : {}
      this.$vs.notify({ title: d.status == 1 ? 'Thành công' : 'Không thực hiện được', text: d.message || '', color: d.status == 1 ? okColor : 'danger' })
      return d.status == 1
    },
    loadSources(selectFirst = false) {
      this.loadingSources = true
      return axios.g('/api/settings/sources/list').then(res => {
        this.loadingSources = false
        this.sources = (res && res.data && res.data.data) || []
        if (this.selected) {
          this.selected = this.sources.find(s => s.id == this.selected.id) || null
        }
        if (!this.selected && selectFirst && this.sources.length) {
          const pref = this.$route.query.source_id ? Number(this.$route.query.source_id) : 6
          this.selectSource(this.sources.find(s => s.id == pref) || this.sources[0])
        }
      }).catch(() => { this.loadingSources = false })
    },
    selectSource(s) {
      this.selected = s
      this.reloadDetails()
    },
    reloadDetails() {
      this.paging.cpage = 1
      this.loadDetails()
    },
    resetFilters() {
      this.filters = { keyword: '', branch: null, status: null }
      this.reloadDetails()
    },
    loadDetails() {
      if (!this.selected) return
      this.loadingDetails = true
      axios.p('/api/settings/sources/details', {
        source_id: this.selected.id,
        keyword: this.filters.keyword,
        branch_id: this.filters.branch ? this.filters.branch.id : '',
        status: this.filters.status ? this.filters.status.value : '',
        page: this.paging.cpage,
        limit: this.paging.limit
      }).then(res => {
        this.loadingDetails = false
        const d = res && res.data && res.data.data
        this.details = (d && d.list) || []
        this.paging.total = (d && d.paging && d.paging.total) || 0
      }).catch(() => { this.loadingDetails = false })
    },

    /* ---------- Nguồn ---------- */
    openSourceModal(s) {
      this.sourceForm = s
        ? { id: s.id, name: s.name, description: s.description || '', status: Number(s.status) }
        : { id: 0, name: '', description: '', status: 1 }
      this.showSourceModal = true
    },
    saveSource() {
      if (!this.sourceForm.name) {
        this.$vs.notify({ title: 'Thiếu thông tin', text: 'Vui lòng nhập tên nguồn', color: 'danger' })
        return
      }
      this.saving = true
      axios.p('/api/settings/sources/save', this.sourceForm).then(res => {
        this.saving = false
        if (this.notify(res)) {
          this.showSourceModal = false
          const newId = res.data.data && res.data.data.id
          this.loadSources().then(() => {
            if (!this.sourceForm.id && newId) this.selectSource(this.sources.find(s => s.id == newId))
          })
        }
      }).catch(() => { this.saving = false })
    },
    confirmDeleteSource(s) {
      this.$vs.dialog({
        type: 'confirm',
        color: 'danger',
        title: 'Xóa nguồn',
        text: `Xóa nguồn "${s.name}"? Chỉ xóa được khi nguồn chưa có nguồn chi tiết và chưa gắn với khách hàng nào.`,
        acceptText: 'Xóa',
        cancelText: 'Hủy',
        accept: () => {
          axios.p('/api/settings/sources/delete', { id: s.id }).then(res => {
            if (this.notify(res)) {
              if (this.selected && this.selected.id == s.id) this.selected = null
              this.loadSources(true)
            }
          })
        }
      })
    },

    /* ---------- Nguồn chi tiết ---------- */
    openDetailModal(d) {
      const findBranch = id => this.branchOptions.find(b => b.id == Number(id || 0)) || this.branchOptions[0]
      this.detailForm = d
        ? { id: d.id, source: this.sources.find(s => s.id == d.source_id) || this.selected, branch: findBranch(d.branch_id), name: d.name, names: '', status: Number(d.status) }
        : { id: 0, source: this.selected, branch: this.branchOptions[0], name: '', names: '', status: 1 }
      this.showDetailModal = true
    },
    async saveDetail() {
      const f = this.detailForm
      const names = f.id ? [f.name] : this.parsedNames
      if (!f.source || !names.length || !names[0]) {
        this.$vs.notify({ title: 'Thiếu thông tin', text: 'Vui lòng chọn nguồn và nhập tên nguồn chi tiết', color: 'danger' })
        return
      }
      this.saving = true
      let ok = 0
      const errors = []
      for (const name of names) {
        try {
          const res = await axios.p('/api/settings/sources/details/save', {
            id: f.id, source_id: f.source.id, branch_id: f.branch ? f.branch.id : 0, name, status: f.status
          })
          if (res && res.data && res.data.status == 1) ok++
          else errors.push(`${name}: ${(res && res.data && res.data.message) || 'lỗi'}`)
        } catch (e) {
          errors.push(`${name}: lỗi kết nối`)
        }
      }
      this.saving = false
      if (ok) {
        this.$vs.notify({ title: 'Thành công', text: f.id ? 'Đã cập nhật nguồn chi tiết' : `Đã thêm ${ok} nguồn chi tiết`, color: 'success' })
        this.showDetailModal = false
      }
      if (errors.length) {
        this.$vs.notify({ title: 'Bỏ qua ' + errors.length + ' dòng', text: errors.slice(0, 3).join(' · '), color: 'warning', time: 6000 })
      }
      if (f.source && this.selected && f.source.id != this.selected.id) {
        this.selected = this.sources.find(s => s.id == f.source.id) || this.selected
      }
      this.loadSources()
      this.loadDetails()
    },
    toggleDetail(d) {
      if (d.is_campaign) return
      axios.p('/api/settings/sources/details/toggle', { id: d.id }).then(res => {
        if (this.notify(res)) {
          d.status = res.data.data.status
          this.loadSources()
        }
      })
    },
    confirmDeleteDetail(d) {
      const used = Number(d.parent_total) + Number(d.student_total)
      this.$vs.dialog({
        type: 'confirm',
        color: 'danger',
        title: 'Xóa nguồn chi tiết',
        text: used
          ? `"${d.name}" đang gắn với ${used} khách hàng/học sinh nên không thể xóa. Bạn có muốn chuyển sang "Ngừng sử dụng"?`
          : `Xóa nguồn chi tiết "${d.name}"?`,
        acceptText: used ? 'Ngừng sử dụng' : 'Xóa',
        cancelText: 'Hủy',
        accept: () => {
          if (used) {
            if (d.status == 1) this.toggleDetail(d)
            return
          }
          axios.p('/api/settings/sources/details/delete', { id: d.id }).then(res => {
            if (this.notify(res)) {
              this.loadDetails()
              this.loadSources()
            }
          })
        }
      })
    }
  }
}
</script>

<style>
#settings-sources .src-layout { display: grid; grid-template-columns: 340px 1fr; gap: 16px; align-items: start; }
@media (max-width: 1100px) { #settings-sources .src-layout { grid-template-columns: 1fr; } }
#settings-sources .src-side { position: sticky; top: 90px; }
#settings-sources .src-side__head { display: flex; justify-content: space-between; align-items: center; gap: 10px; padding: 16px 16px 12px; border-bottom: 1px solid #e2e8f0; }
#settings-sources .src-list { padding: 8px; max-height: calc(100vh - 220px); overflow-y: auto; }
#settings-sources .src-item {
  display: flex; align-items: center; gap: 8px;
  padding: 10px 12px; border-radius: 10px; cursor: pointer;
  border: 1.5px solid transparent; transition: background 0.15s, border-color 0.15s;
}
#settings-sources .src-item + .src-item { margin-top: 4px; }
#settings-sources .src-item:hover { background: #f8fafc; }
#settings-sources .src-item.is-active { background: rgba(115, 103, 240, 0.08); border-color: #7367f0; }
#settings-sources .src-item.is-off .src-item__name { color: #94a3b8; }
#settings-sources .src-item__main { flex: 1; min-width: 0; }
#settings-sources .src-item__name { font-size: 14.5px; font-weight: 700; color: #0f172a; display: flex; flex-wrap: wrap; align-items: center; gap: 6px; }
#settings-sources .src-item__meta { display: flex; gap: 12px; font-size: 13px; color: #64748b; margin-top: 3px; white-space: nowrap; }
#settings-sources .src-item__meta i { font-size: 13px; }
#settings-sources .src-item__acts { display: flex; gap: 4px; opacity: 0; transition: opacity 0.15s; }
#settings-sources .src-item:hover .src-item__acts, #settings-sources .src-item.is-active .src-item__acts { opacity: 1; }
#settings-sources .src-mini { padding: 3px 8px; }
#settings-sources .src-main { min-width: 0; }
#settings-sources .src-notice {
  display: flex; gap: 10px; align-items: flex-start;
  margin: 14px 22px 0; padding: 12px 14px; border-radius: 10px;
  background: #f0f9ff; border: 1px solid #bae6fd; color: #075985; font-size: 13.5px; line-height: 1.5;
}
#settings-sources .src-notice i { font-size: 18px; margin-top: 1px; }
#settings-sources .src-notice a { font-weight: 700; text-decoration: underline; }
#settings-sources .src-branch { white-space: nowrap; font-weight: 600; color: #334155; }
#settings-sources .src-row-off td { color: #94a3b8; }
#settings-sources .src-row-off .ev-strong { color: #94a3b8; text-decoration: line-through; }
#settings-sources .src-switch {
  display: inline-flex; align-items: center; gap: 8px; white-space: nowrap;
  height: 30px; padding: 0 12px 0 6px; border-radius: 999px; cursor: pointer;
  font-size: 13px; font-weight: 700; border: 1.5px solid #cbd5e1; background: #ffffff; color: #64748b;
}
#settings-sources .src-switch span { width: 18px; height: 18px; border-radius: 50%; background: #cbd5e1; transition: background 0.15s; }
#settings-sources .src-switch.is-on { border-color: #86efac; background: #f0fdf4; color: #15803d; }
#settings-sources .src-switch.is-on span { background: #16a34a; }
#settings-sources .src-switch:disabled { cursor: not-allowed; background: #f1f5f9; border: 1px dashed #cbd5e1; color: #94a3b8; }
#settings-sources .src-switch:disabled span { background: #cbd5e1; }
.src-textarea { height: auto !important; padding: 10px 12px !important; line-height: 1.6; resize: vertical; }
.src-check { display: inline-flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 600; color: #334155; cursor: pointer; }
.src-check input { width: 18px; height: 18px; accent-color: #7367f0; cursor: pointer; }
</style>
