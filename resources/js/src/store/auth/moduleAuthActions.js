/*=========================================================================================
  File Name: moduleAuthActions.js
  Description: Auth Module Actions
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

import jwt from '../../http/requests/auth/jwt/index.js'
import router from '@/router'

export default {
  updateUsername ({ commit }, payload) {
    payload.user.updateProfile({
      displayName: payload.displayName
    }).then(() => {

      // If username update is success
      // update in localstorage
      const newUserData = Object.assign({}, payload.user.providerData[0])
      newUserData.displayName = payload.displayName
      commit('UPDATE_USER_INFO', newUserData, {root: true})

      // If reload is required to get fresh data after update
      // Reload current page
      if (payload.isReloadRequired) {
        router.push(router.currentRoute.query.to || '/')
      }
    }).catch((err) => {
      payload.notify({
        time: 8800,
        title: 'Error',
        text: err.message,
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'danger'
      })
    })
  },


  // JWT
  loginJWT ({ commit }, payload) {
    return new Promise((resolve, reject) => {
      jwt.login(payload.userDetails.email, payload.userDetails.password)
        .then(response => {
          // If there's user data in response
          if (response.data && response.data.status && response.data.userData) {

            // Navigate User to homepage
            router.push(payload.redirect_url || '/admin/dashboard')

            // Set accessToken
            localStorage.setItem('accessToken', response.data.accessToken)

            // Update user details
            commit('UPDATE_USER_INFO', response.data.userData, {root: true})

            resolve(response)
          } else if (response.data && !response.data.status) {
            reject({message: response.data.message, type: response.data.type})
          } else {
            reject({message: 'Lỗi hệ thống vui lòng thử lại'})
          }

        })
        .catch(error => { reject(error) })
    })
  },
  registerUserJWT ({ commit }, payload) {

    const { displayName, email, password, confirmPassword } = payload.userDetails

    return new Promise((resolve, reject) => {

      // Check confirm password
      if (password !== confirmPassword) {
        reject({message: 'Mật khẩu không khớp. Vui lòng thử lại.'})
      }

      jwt.registerUser(displayName, email, password, confirmPassword)
        .then(response => {
          if (response.data && response.data.status) {
            // Redirect User
            router.push('/pages/notify-active')

            resolve(response)
          } else{
            reject({message: response.data.message})
          }
        })
        .catch(error => { reject(error) })
    })
  },
  fetchAccessToken () {
    return new Promise((resolve) => {
      jwt.refreshToken().then(response => { resolve(response) })
    })
  }
}
