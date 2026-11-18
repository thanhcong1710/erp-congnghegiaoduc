// Dùng chung cho các dashboard theo vai trò: định dạng số & cấu hình biểu đồ (ApexCharts)
import VueApexCharts from 'vue-apexcharts'

export const SERIES = '#2a78d6'      // 1 chuỗi số liệu (xanh)
export const SERIES_SOFT = '#86b6ef' // chuỗi so sánh (chỉ tiêu)
const INK = '#52514e'
const GRID = '#eceae6'

export function money(v) {
  const n = Number(v) || 0
  const a = Math.abs(n)
  if (a >= 1e9) return (n / 1e9).toFixed(a >= 1e10 ? 1 : 2).replace(/\.?0+$/, '') + ' tỷ'
  if (a >= 1e6) return (n / 1e6).toFixed(a >= 1e8 ? 0 : 1).replace(/\.0$/, '') + ' tr'
  if (a >= 1e3) return Math.round(n / 1e3) + 'k'
  return String(Math.round(n))
}
export function fullMoney(v) { return (Number(v) || 0).toLocaleString('vi-VN') + ' đ' }

function base(type, height) {
  return {
    chart: { type, height, toolbar: { show: false }, zoom: { enabled: false }, fontFamily: 'inherit', animations: { speed: 300 } },
    grid: { borderColor: GRID, strokeDashArray: 3, padding: { left: 6, right: 10 } },
    dataLabels: { enabled: false },
    legend: { show: false, fontSize: '13px', labels: { colors: INK } },
    tooltip: { theme: 'light', style: { fontSize: '13px' } },
    xaxis: { labels: { style: { colors: INK, fontSize: '13px' } }, axisBorder: { color: GRID }, axisTicks: { show: false } },
    yaxis: { labels: { style: { colors: INK, fontSize: '13px' } } }
  }
}

export default {
  components: { VueApexCharts },
  methods: {
    money,
    fullMoney,
    pctText(v) { return v === null || v === undefined ? '—' : `${v}%` },
    diffPct(cur, prev) {
      if (!prev) return null
      return Math.round(((cur - prev) / prev) * 1000) / 10
    },
    // Cột dọc 1 chuỗi (doanh thu theo tháng…)
    colOptions(labels, isMoney = true, height = 260) {
      const o = base('bar', height)
      o.colors = [SERIES]
      o.plotOptions = { bar: { columnWidth: '42%', borderRadius: 4 } }
      o.xaxis.categories = labels
      o.yaxis.labels.formatter = v => (isMoney ? money(v) : Math.round(v))
      o.tooltip.y = { formatter: v => (isMoney ? fullMoney(v) : v) }
      return o
    },
    // Thanh ngang 1 chuỗi (phễu, nguồn, sản phẩm…) – nhãn giá trị ở đầu thanh
    barOptions(labels, isMoney = false, height = 260) {
      const o = base('bar', height)
      o.colors = [SERIES]
      o.plotOptions = { bar: { horizontal: true, barHeight: '58%', borderRadius: 4, dataLabels: { position: 'top' } } }
      o.dataLabels = { enabled: true, offsetX: 26, style: { fontSize: '13px', colors: [INK], fontWeight: 600 }, formatter: v => (isMoney ? money(v) : v) }
      o.xaxis.categories = labels
      o.xaxis.labels.formatter = v => (isMoney ? money(v) : Math.round(v))
      o.tooltip.y = { formatter: v => (isMoney ? fullMoney(v) : v) }
      return o
    },
    // Luỹ kế thực thu trong tháng + đường chỉ tiêu (cùng đơn vị, cùng 1 trục)
    cumulativeOptions(labels, height = 260) {
      const o = base('area', height)
      o.colors = [SERIES, SERIES_SOFT]
      o.stroke = { width: [2, 2], curve: 'straight', dashArray: [0, 6] }
      o.fill = { type: ['gradient', 'solid'], opacity: [0.25, 0], gradient: { shadeIntensity: 0, opacityFrom: 0.3, opacityTo: 0.02 } }
      o.legend.show = true
      o.legend.position = 'top'
      o.legend.horizontalAlign = 'left'
      o.xaxis.categories = labels
      o.xaxis.tickAmount = 10
      o.yaxis.labels.formatter = v => money(v)
      o.tooltip.shared = true
      o.tooltip.y = { formatter: v => fullMoney(v) }
      return o
    },
    fmtDate(d) {
      if (!d) return ''
      const s = String(d).slice(0, 10).split('-')
      return s.length === 3 ? `${s[2]}/${s[1]}/${s[0]}` : d
    },
    go(path) { this.$router.push(path) },
    chartH(labels) { return Math.max(180, (labels || []).length * 38 + 40) },
    pctCls(p) { return p === null || p === undefined ? '' : p >= 100 ? 'good' : p >= 60 ? 'mid' : 'low' },
    scrollTo(id) { const el = document.getElementById(id); if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' }) }
  }
}
