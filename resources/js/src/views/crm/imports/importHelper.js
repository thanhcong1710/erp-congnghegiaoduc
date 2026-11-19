// Nhãn kết quả cho từng dòng crm_import_parents
// status: 1 hợp lệ | 2 dữ liệu lỗi | 3 trùng trong file | 4 trùng KH trong hệ thống | 6 đã import
export function importRowBadge(r) {
  const s = Number(r.status)
  if (s === 6) return { label: 'Đã nhập', cls: 'imp-badge--ok', group: 'ok' }
  if (s === 1) return { label: 'Hợp lệ', cls: 'imp-badge--ok', group: 'ok' }
  if (s === 4 && Number(r.is_lock) === 0) return { label: 'Ghi đè được', cls: 'imp-badge--warn', group: 'overwrite' }
  if (s === 4) return { label: 'Đã có chủ', cls: 'imp-badge--err', group: 'error' }
  if (s === 3) return { label: 'Trùng trong file', cls: 'imp-badge--muted', group: 'error' }
  return { label: 'Lỗi dữ liệu', cls: 'imp-badge--err', group: 'error' }
}
