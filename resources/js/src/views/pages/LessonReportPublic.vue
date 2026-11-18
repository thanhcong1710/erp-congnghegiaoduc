<template>
  <div class="lrp-page">
    <div v-if="loading" class="lrp-state"><i class="fa-solid fa-spinner fa-spin"></i> Đang tải báo cáo...</div>
    <div v-else-if="error" class="lrp-state">
      <img src="@assets/images/logo/stemkey-logo.png" alt="STEMKEY" class="lrp-state__logo" />
      <div>{{ error }}</div>
    </div>
    <template v-else>
      <report-sheet :session="data.session" :report="data.report" :students="data.students" :view="data.view || 'class'" />
      <div class="lrp-actions">
        <button type="button" class="lrp-btn" @click="print"><i class="fa-solid fa-print"></i> In / Lưu PDF</button>
      </div>
    </template>
  </div>
</template>

<script>
import axios from 'axios'
import moment from 'moment'
import ReportSheet from '@/views/lms/lesson-reports/ReportSheet.vue'

export default {
  components: { ReportSheet },
  data() {
    return { loading: true, error: '', data: null }
  },
  created() {
    axios.get(`/api/public/lesson-report/${encodeURIComponent(this.$route.params.token)}`)
      .then(res => {
        const d = res.data || {}
        if (d.status !== 1) { this.error = d.message || 'Không tìm thấy báo cáo'; return }
        this.data = d.data
        const s = d.data.session
        const who = d.data.view === 'student' && d.data.students.length ? ` - ${d.data.students[0].name}` : ''
        document.title = `Báo cáo tuần ${s.week_no || ''} lớp ${s.cls_name}${who} (${moment(s.week_start || s.class_date).format('DD/MM')} - ${moment(s.week_end || s.class_date).format('DD/MM/YYYY')})`
      })
      .catch(() => { this.error = 'Không tải được báo cáo, vui lòng thử lại sau.' })
      .then(() => { this.loading = false })
  },
  methods: {
    print() { window.print() }
  }
}
</script>

<style>
.lrp-page { min-height: 100vh; background: #f6f1ee; padding: 24px 12px 40px; font-size: 14px; }
.lrp-state { max-width: 520px; margin: 80px auto; background: #fff; border-radius: 14px; padding: 36px 24px; text-align: center; color: #475569; font-size: 15px; box-shadow: 0 6px 24px rgba(15, 23, 42, 0.08); }
.lrp-state__logo { height: 52px; margin-bottom: 16px; }
.lrp-actions { text-align: center; margin-top: 18px; }
.lrp-btn { display: inline-flex; align-items: center; gap: 8px; border: 1.5px solid #f05a28; background: #fff; color: #f05a28; font-weight: 600; font-size: 14px; border-radius: 99px; padding: 8px 18px; cursor: pointer; }
.lrp-btn:hover { background: #f05a28; color: #fff; }
@media (max-width: 720px) { .lrp-page { padding: 0 0 24px; background: #fff; } }
@media print { .lrp-page { background: #fff; padding: 0; } .lrp-actions { display: none; } }
</style>
