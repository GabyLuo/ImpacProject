import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
  },
  mutations: {
  },
  actions: {
    save: ({dispatch}, payload) => {
      return axios.post('/clientes_requisitos/save', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    }
  }
}

export default module
