import api from '../../commons/api.js'

const module = {
  namespaced: true,
  actions: {
    delete: ({dispatch}, payload) => api.post('documents/delete', payload)
  }
}

export default module
