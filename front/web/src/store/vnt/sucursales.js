import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    sucursales: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.sucursales = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/sucursales/getAll')
        .then(response => {
          commit('refresh', response.data.sucursales)
          return response
        })
        .catch(error => error)
    },
    getByEmpresa: ({commit}, payload) => {
      return axios.get(`/sucursales/getByEmpresa/${payload.empresa_id}`)
        .then(response => {
          // commit('refresh', response.data.sucursales)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/sucursales/save', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/sucursales/update', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/sucursales/delete', qs.stringify(payload))
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
    sucursales: state => state.sucursales
  }
}

export default module
