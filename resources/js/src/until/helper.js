function checkPermission(user, permission) {
  console.log(user)
  if (typeof user.permissions !== 'undefined') {
    return user.permissions.indexOf(permission) != -1
  } else {
    return false
  }
}

export default {
  checkPermission,
}