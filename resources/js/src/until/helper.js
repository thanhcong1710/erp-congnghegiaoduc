function checkPermission(user, permission) {
  if (typeof user.permissions !== 'undefined') {
    return user.permissions.indexOf(permission) != -1
  } else {
    return false
  }
}

export default {
  checkPermission,
}