// Helper dùng chung cho các màn hình TEST & Đánh giá

export function initials(name) {
  if (!name) return '?'
  const parts = String(name).replace(/\(.*?\)/g, '').trim().split(/\s+/).filter(Boolean)
  if (parts.length === 0) return '?'
  const last = parts[parts.length - 1]
  const first = parts.length > 1 ? parts[parts.length - 2] : ''
  return ((first ? first[0] : '') + last[0]).toUpperCase()
}

const AVATAR_TONES = [
  ['#ede9fe', '#6d28d9'], ['#e0f2fe', '#0369a1'], ['#dcfce7', '#15803d'],
  ['#fef3c7', '#b45309'], ['#fce7f3', '#be185d'], ['#e0e7ff', '#4338ca']
]
export function avatarStyle(name) {
  const s = String(name || '')
  let h = 0
  for (let i = 0; i < s.length; i++) h = (h * 31 + s.charCodeAt(i)) >>> 0
  const [bg, fg] = AVATAR_TONES[h % AVATAR_TONES.length]
  return { background: bg, color: fg }
}

// yyyy-mm-dd -> dd/mm/yyyy
export function fmtDate(v) {
  if (!v) return '—'
  const s = String(v).substring(0, 10)
  const m = s.match(/^(\d{4})-(\d{2})-(\d{2})$/)
  return m ? `${m[3]}/${m[2]}/${m[1]}` : s
}

export function todayISO() {
  const d = new Date()
  d.setMinutes(d.getMinutes() - d.getTimezoneOffset())
  return d.toISOString().substring(0, 10)
}

// Mở báo cáo với ?print=1 → tự bật hộp thoại in sau khi biểu đồ vẽ xong
export function autoPrintIfRequested(vm, delay = 800) {
  if (vm.$route && vm.$route.query && vm.$route.query.print) {
    setTimeout(() => {
      window.print()
      vm.$router.replace({ query: {} }).catch(() => {})
    }, delay)
  }
}

// Mỗi .ev-sheet = đúng 1 trang A4 khi in.
// Trước khi in: đo chiều cao thật của từng tờ, tờ nào dài hơn vùng in (A4 trừ lề 10mm trên/dưới)
// thì thu nhỏ (zoom) vừa 1 trang → không bị đẩy khối sang trang trắng, không mất header.
const MM = 96 / 25.4
const PRINT_H = 276 * MM

export function installPrintFit(vm) {
  const sheets = () => (vm.$el && vm.$el.querySelectorAll ? Array.from(vm.$el.querySelectorAll('.ev-sheet')) : [])
  const before = () => {
    sheets().forEach(el => {
      el.style.zoom = ''
      const prevMin = el.style.minHeight
      el.style.minHeight = '0'
      const h = el.scrollHeight
      el.style.minHeight = prevMin
      if (h > PRINT_H) el.style.zoom = Math.max(0.8, PRINT_H / h).toFixed(3)
    })
  }
  const after = () => sheets().forEach(el => { el.style.zoom = '' })
  window.addEventListener('beforeprint', before)
  window.addEventListener('afterprint', after)
  vm.$once('hook:beforeDestroy', () => {
    window.removeEventListener('beforeprint', before)
    window.removeEventListener('afterprint', after)
  })
}

// Vẽ canvas sắc nét (HiDPI + in ấn): trả về ctx đã scale, kích thước logic giữ nguyên
export function hiDpiCanvas(canvas, w, h, scale = 3) {
  if (!canvas) return null
  canvas.width = w * scale
  canvas.height = h * scale
  canvas.style.width = '100%'
  canvas.style.maxWidth = w + 'px'
  canvas.style.height = 'auto'
  const ctx = canvas.getContext('2d')
  if (!ctx) return null
  ctx.setTransform(scale, 0, 0, scale, 0, 0)
  return ctx
}
