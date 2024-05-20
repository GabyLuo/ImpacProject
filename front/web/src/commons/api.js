import axios from 'axios'
import qs from 'qs'

const api = {
  get: (url, request) => axios.get(url, qs.stringify(request)),
  post: (url, request) => axios.post(url, qs.stringify(request)),
  put: (url, request) => axios.put(url, qs.stringify(request)),
  delete: (url, request) => axios.delete(url, qs.stringify(request))
}

export default api
