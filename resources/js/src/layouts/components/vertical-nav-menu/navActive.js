// Xác định mục menu đang active theo URL hiện tại.
// So khớp tiền tố dài nhất → trang con (vd /settings/rooms/edit/3, /lms/evaluations/ucrea/report/5)
// vẫn sáng đúng mục cha và tự mở nhóm chứa nó.
import navMenuItems from './navMenuItems.js'

function collectUrls(items, out) {
  (items || []).forEach(it => {
    if (it.url && it.slug !== 'external') out.push(it.url)
    if (it.items) collectUrls(it.items, out)
    if (it.submenu) collectUrls(it.submenu, out)
  })
  return out
}

const ALL_URLS = Array.from(new Set(collectUrls(navMenuItems, []))).sort((a, b) => b.length - a.length)

export function activeMenuUrl(path) {
  if (!path) return null
  const p = path.length > 1 ? path.replace(/\/+$/, '') : path
  for (const url of ALL_URLS) {
    if (p === url) return url
    if (url !== '/' && p.indexOf(url + '/') === 0) return url
  }
  return null
}

export function isItemActive(route, url, slug) {
  if (!url) return false
  if (route.meta && route.meta.parent && route.meta.parent === slug) return true
  return activeMenuUrl(route.path) === url
}

export function groupHasActive(route, group) {
  let found = false
  const walk = items => (items || []).forEach(it => {
    if (found) return
    if (it.submenu) walk(it.submenu)
    else if (isItemActive(route, it.url, it.slug)) found = true
  })
  walk(group.submenu)
  return found
}
