import axios from '../../../axios/index.js'
import store from '../../../../store/store.js'

// Token Refresh
let isAlreadyFetchingAccessToken = false
let subscribers = []

function onAccessTokenFetched (access_token) {
  subscribers = subscribers.filter(callback => callback(access_token))
}

function addSubscriber (callback) {
  subscribers.push(callback)
}

export default {
  init () {
    axios.interceptors.response.use(function (response) {
      return response
    }, function (error) {
      // const { config, response: { status } } = error
      const { config, response } = error
      const originalRequest = config
      // if (status === 401) {
      if (response && response.status === 401) {
        return [200, {error}];
        if (!isAlreadyFetchingAccessToken) {
          isAlreadyFetchingAccessToken = true
          store.dispatch('auth/fetchAccessToken')
            .then((access_token) => {
              isAlreadyFetchingAccessToken = false
              onAccessTokenFetched(access_token)
            }) 
        }

        const retryOriginalRequest = new Promise((resolve) => {
          addSubscriber(access_token => {
            originalRequest.headers.Authorization = `Bearer ${access_token}`
            resolve(axios(originalRequest))
          })
        })
        return retryOriginalRequest
      }
      return Promise.reject(error)
    })
  },
  login (hrm_id, pwd) {
    return axios.post('/api/auth/login', {
      hrm_id,
      password: pwd
    })
  },
  registerUser (name, email, pwd, pwd_cf) {
    return axios.post('/api/auth/register', {
      name: name,
      email,
      password: pwd,
      password_confirmation: pwd_cf
    })
  },
  refreshToken () {
    return axios.post('/api/auth/refresh-token', {accessToken: localStorage.getItem('accessToKen')})
  }
}
