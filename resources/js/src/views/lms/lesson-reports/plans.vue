<template>
  <div id="page-lesson-plans" class="lp-page mt-5">
    <div class="lp-card lp-head">
      <button type="button" class="lp-btn ghost" @click="$router.push('/lms/lesson-reports')"><i class="fa-solid fa-arrow-left"></i>Lịch nhận xét</button>
      <div class="lp-head__field">
        <vue-select label="name" placeholder="Chọn khóa học" :options="products" v-model="product" :clearable="false" @input="loadPlans">
          <template slot="option" slot-scope="o">{{ o.name }} <span class="lp-muted">({{ o.plan_count }} tuần)</span></template>
        </vue-select>
      </div>
      <div class="lp-hint">
        <i class="fa-solid fa-circle-info"></i>Nội dung bài học cố định theo lộ trình. Phiếu nhận xét tuần N của lớp thuộc khóa học sẽ tự điền nội dung tuần N.
        <template v-if="!canEdit"> Bạn chỉ có quyền xem.</template>
      </div>
    </div>

    <div v-if="!product" class="lp-card lp-empty">Chọn khóa học để xem / soạn lộ trình bài học.</div>

    <div v-else class="lp-body">
      <!-- Danh sách tuần -->
      <div class="lp-card lp-weeks">
        <div class="lp-weeks__title">
          <span>Các tuần <span class="lp-pill">{{ plans.length }}</span></span>
          <button v-if="canEdit" type="button" class="lp-btn soft sm" @click="newWeek"><i class="fa-solid fa-plus"></i>Thêm tuần</button>
        </div>
        <div v-if="!plans.length" class="lp-muted lp-pad">Chưa có tuần nào.</div>
        <div
          v-for="p in plans" :key="p.id" class="lp-week" :class="{ active: form.id === p.id }"
          @click="edit(p)"
        >
          <div class="lp-week__no">Tuần {{ p.week_no }}</div>
          <div class="lp-week__title">{{ p.lesson_title || stripHtml(p.content) || '—' }}</div>
        </div>
      </div>

      <!-- Soạn nội dung -->
      <div class="lp-card lp-form">
        <div v-if="!editing" class="lp-muted lp-pad">Chọn 1 tuần bên trái<template v-if="canEdit"> hoặc bấm "Thêm tuần"</template>.</div>
        <template v-else>
          <div class="lp-grid">
            <div class="lp-field">
              <label>Tuần thứ</label>
              <input type="number" min="1" max="200" class="lp-input" v-model.number="form.week_no" :disabled="!canEdit" />
            </div>
            <div class="lp-field">
              <label>Tên bài học</label>
              <input type="text" class="lp-input" v-model="form.lesson_title" :disabled="!canEdit" placeholder="VD: Unit 2 - Lesson 4 - Letters and sounds" />
            </div>
            <div class="lp-field">
              <label>Thời gian nộp bài</label>
              <input type="text" class="lp-input" v-model="form.homework_deadline" :disabled="!canEdit" placeholder="VD: Trước buổi học tiếp theo" />
            </div>
          </div>
          <div class="lp-field">
            <label>Nội dung bài học &amp; bài tập về nhà</label>
            <quill-editor v-if="canEdit" v-model="form.content" :options="editorOptions" class="lp-editor" />
            <div v-else class="lp-readonly ql-editor" v-html="form.content || '<p>—</p>'"></div>
          </div>
          <div class="lp-actions" v-if="canEdit">
            <button v-if="form.id" type="button" class="lp-btn danger" @click="remove"><i class="fa-solid fa-trash"></i>Xóa tuần</button>
            <button type="button" class="lp-btn primary" @click="save"><i class="fa-solid fa-floppy-disk"></i>Lưu</button>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

<script>
import select from 'vue-select'
import { quillEditor } from 'vue-quill-editor'
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import axios from '../../../http/axios.js'

const LS_KEY = 'lesson_plans_product'
const emptyForm = () => ({ id: null, week_no: 1, lesson_title: '', content: '', homework_deadline: '' })

export default {
  components: { 'vue-select': select, quillEditor },
  data() {
    return {
      products: [],
      product: null,
      plans: [],
      canEdit: false,
      editing: false,
      form: emptyForm(),
      editorOptions: {
        placeholder: 'Nội dung bài học, từ vựng, kỹ năng, bài tập về nhà...',
        modules: { toolbar: [[{ header: [2, 3, false] }], ['bold', 'italic', 'underline', 'strike'], [{ color: [] }, { background: [] }], [{ list: 'ordered' }, { list: 'bullet' }], ['clean']] }
      }
    }
  },
  created() { this.loadProducts() },
  methods: {
    notify(text, color = 'success') {
      this.$vs.notify({ title: color === 'danger' ? 'Lỗi' : 'Thông báo', text, color, iconPack: 'feather', icon: color === 'danger' ? 'icon-alert-triangle' : 'icon-check' })
    },
    stripHtml(html) { return (html || '').replace(/<[^>]*>/g, ' ').replace(/&nbsp;/g, ' ').replace(/\s+/g, ' ').trim().slice(0, 80) },
    loadProducts() {
      axios.g('/api/lms/lesson-reports/plans/products').then(res => {
        const d = res.data || {}
        if (d.status !== 1) return this.notify(d.message || 'Không tải được khóa học', 'danger')
        this.products = d.data || []
        this.canEdit = !!d.can_edit
        let saved = null
        try { saved = parseInt(localStorage.getItem(LS_KEY)) } catch (e) { /* ignore */ }
        const p = this.products.find(x => x.id === saved)
        if (p) { this.product = p; this.loadPlans() }
      })
    },
    loadPlans(keepId) {
      if (!this.product) return
      try { localStorage.setItem(LS_KEY, this.product.id) } catch (e) { /* ignore */ }
      axios.p('/api/lms/lesson-reports/plans/list', { product_id: this.product.id }).then(res => {
        this.plans = (res.data && res.data.data) || []
        this.product.plan_count = this.plans.length
        const cur = typeof keepId === 'number' ? this.plans.find(p => p.id === keepId) : null
        if (cur) this.edit(cur)
        else { this.editing = false; this.form = emptyForm() }
      })
    },
    edit(p) {
      this.form = { id: p.id, week_no: p.week_no, lesson_title: p.lesson_title || '', content: p.content || '', homework_deadline: p.homework_deadline || '' }
      this.editing = true
    },
    newWeek() {
      const max = this.plans.reduce((m, p) => Math.max(m, p.week_no), 0)
      this.form = Object.assign(emptyForm(), { week_no: max + 1 })
      this.editing = true
    },
    save() {
      axios.p('/api/lms/lesson-reports/plans/save', Object.assign({ product_id: this.product.id }, this.form)).then(res => {
        const d = res.data || {}
        if (d.status !== 1) return this.notify(d.message || 'Không lưu được', 'danger')
        this.notify(d.message)
        this.loadPlans(d.data.id)
      })
    },
    remove() {
      this.$vs.dialog({
        type: 'confirm', color: 'danger', title: 'Xóa tuần', text: `Xóa nội dung lộ trình tuần ${this.form.week_no}? Các phiếu nhận xét đã tạo không bị ảnh hưởng.`,
        acceptText: 'Xóa', cancelText: 'Hủy',
        accept: () => axios.p('/api/lms/lesson-reports/plans/delete', { id: this.form.id }).then(() => { this.notify('Đã xóa'); this.loadPlans() })
      })
    }
  }
}
</script>

<style>
.lp-page { font-size: 14px; color: #334155; display: flex; flex-direction: column; gap: 16px; }
.lp-card { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(15, 23, 42, 0.06); padding: 16px 20px; }
.lp-empty { text-align: center; color: #64748b; padding: 50px 20px; font-size: 15px; }
.lp-muted { color: #94a3b8; font-size: 14px; }
.lp-pad { padding: 20px 4px; text-align: center; }
.lp-head { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
.lp-head__field { width: 360px; max-width: 100%; }
.lp-hint { flex: 1; min-width: 260px; font-size: 13px; color: #64748b; }
.lp-hint i { color: #7367f0; margin-right: 6px; }
.lp-body { display: grid; grid-template-columns: 320px minmax(0, 1fr); gap: 16px; align-items: start; }
@media (max-width: 992px) { .lp-body { grid-template-columns: 1fr; } }
.lp-weeks { padding: 12px; max-height: 75vh; overflow-y: auto; }
.lp-weeks__title { display: flex; align-items: center; justify-content: space-between; font-size: 15px; font-weight: 700; color: #1e293b; padding: 4px 6px 10px; }
.lp-pill { font-size: 13px; background: #f1f5f9; color: #475569; border-radius: 99px; padding: 1px 8px; margin-left: 4px; }
.lp-week { border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 8px 12px; margin-bottom: 8px; cursor: pointer; transition: all .15s; }
.lp-week:hover { border-color: #7367f0; }
.lp-week.active { border-color: #7367f0; background: rgba(115, 103, 240, 0.06); box-shadow: 0 0 0 3px rgba(115, 103, 240, 0.15); }
.lp-week__no { font-size: 13px; font-weight: 700; color: #f05a28; white-space: nowrap; }
.lp-week__title { font-size: 14px; color: #1e293b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.lp-grid { display: grid; grid-template-columns: 120px 1fr 1fr; gap: 14px; margin-bottom: 14px; }
@media (max-width: 768px) { .lp-grid { grid-template-columns: 1fr; } }
.lp-field > label { display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; }
.lp-page input.lp-input { width: 100%; height: 40px; background: #fff; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 0 12px; font-size: 14px; color: #1e293b; }
.lp-page input.lp-input:not(:disabled):hover, .lp-page input.lp-input:not(:disabled):focus { border-color: #7367f0; box-shadow: 0 0 0 3px rgba(115, 103, 240, 0.2); outline: none; }
.lp-page input.lp-input:disabled { background: #f1f5f9; border: 1px dashed #cbd5e1; color: #94a3b8; cursor: not-allowed; }
.lp-editor .ql-toolbar.ql-snow { border: 1.5px solid #cbd5e1; border-radius: 8px 8px 0 0; background: #f8fafc; }
.lp-editor .ql-container.ql-snow { border: 1.5px solid #cbd5e1; border-top: none; border-radius: 0 0 8px 8px; font-size: 14px; font-family: inherit; }
.lp-editor .ql-editor { min-height: 280px; line-height: 1.7; }
.lp-readonly.ql-editor { background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; min-height: 120px; font-size: 14px; }
.lp-actions { display: flex; justify-content: flex-end; gap: 8px; margin-top: 14px; }
.lp-btn { display: inline-flex; align-items: center; gap: 7px; white-space: nowrap; height: 38px; padding: 0 14px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; border: 1.5px solid transparent; transition: all .15s; }
.lp-btn.sm { height: 32px; padding: 0 10px; font-size: 13px; }
.lp-btn.ghost { background: #fff; border-color: #cbd5e1; color: #475569; }
.lp-btn.ghost:hover { border-color: #7367f0; color: #7367f0; }
.lp-btn.soft { background: rgba(115, 103, 240, 0.1); color: #5e50ee; }
.lp-btn.primary { background: #7367f0; color: #fff; }
.lp-btn.danger { background: #fff; border-color: #fecaca; color: #dc2626; }
.lp-btn.danger:hover { background: #fef2f2; }
.lp-page .v-select .vs__dropdown-toggle { min-height: 40px; background: #fff; border: 1.5px solid #cbd5e1; border-radius: 8px; }
.lp-page .v-select .vs__search, .lp-page .v-select .vs__selected { font-size: 14px; }
</style>
