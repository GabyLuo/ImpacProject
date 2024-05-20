import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    solicitudeslibres: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.solicitudeslibres = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/autorizaciones/getAll')
        .then(response => {
          commit('refresh', response.data.solicitudeslibres)
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/autorizaciones/delete', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/autorizaciones/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    solicitudes: state => state.archivos_solicitudes,
    solicitudeslibres: state => state.solicitudeslibres
  }
}

export default module
