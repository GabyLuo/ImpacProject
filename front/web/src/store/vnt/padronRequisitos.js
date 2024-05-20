import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    padron_requisitos: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.padron_requisitos = payload
    }
  },
  actions: {
    getByProveedor: ({commit}, payload) => {
      return axios.get(`/padron_requisitos/getByProveedor/${payload.proveedor_id}/${payload.cliente_id}`)
        .then(response => {
          commit('refresh', response.data.padron_requisitos)
          return response
        })
        .catch(error => error)
    },
    deleteFile: ({dispatch}, payload) => {
      return axios.post('/padron_requisitos/deleteFile', qs.stringify(payload))
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
    padron_requisitos: state => state.padron_requisitos
  }
}

export default module
