import axios from 'axios'
import jwt from '../commons/jwt'

function fix () {
  setTimeout(() => {
    let thead = document.querySelector('#maintable thead tr')
    let tbody = document.querySelector('#maintable tbody tr')
    if (thead !== null && tbody !== null) {
      thead = thead.querySelectorAll('th')
      tbody = tbody.querySelectorAll('td')
      for (let i = 0; i < tbody.length; i++) {
        thead[i].style.width = tbody[i].offsetWidth + 'px'
      }
    }
  }, 100)
}

export default ({ Vue }) => {
  axios.defaults.baseURL = process.env.API
  // axios.defaults.timeout = 50000

  let jwtToken = jwt.checkToken()
  if (jwtToken && jwtToken !== null) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${jwtToken}`
  }

  axios.interceptors.response.use(response => {
    fix()
    return response
  }, error => {
    if (error.response) {
      // The request was made and the server responded with a status code
      // that falls out of the range of 2xx
      console.error('Data: ', error.response.data)
      console.error('Status: ', error.response.status)
      console.error('Headers: ', error.response.headers)
    } else if (error.request) {
      // The request was made but no response was received
      // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
      // http.ClientRequest in node.js
      console.error('Request: ', error.request)
    } else {
      // Something happened in setting up the request that triggered an Error
      console.error('Message: ', error.message)
    }
    console.error('Config: ', error.config)
    return Promise.reject(error)
  })

  Vue.prototype.$axios = axios
}
