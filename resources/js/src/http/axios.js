// axios
import axios from 'axios'
const g = (link, attributes = null) => new Promise((resolve, reject) => {
  if (typeof link === 'string') {
    axios.get(link,{
      headers: {
        'Authorization': `Bearer ${localStorage.getItem("accessToken")}`
      },
      params: attributes
    }).then(response => {
      resolve(response)
    }).catch(e => {
      processAuthen(e);
    })
  } else {
    reject('Request url is not valid')
  }
})
const p = (link, params = null) => new Promise((resolve, reject) => {
  if (typeof link === 'string') {
    axios.post(link,params,
      {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem("accessToken")}`
        }
      }
    )
    .then(response => {
      resolve(response)
    }).catch(e => {
      processAuthen(e);
    })
  } else {
    reject('Request url is not valid')
  }
})
function processAuthen(error) {
  // console.log(error.response.headers);
  try {
      if (error.response.status == 401) {
          localStorage.removeItem('accessToken')
          localStorage.removeItem('userInfo')
          window.location.href = '/pages/login';
      }
  } catch (err) {}

}
export default {
  g,
  p
}