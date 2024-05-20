import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    compras_detalles: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.compras_detalles = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/compras_detalles/getAll')
        .then(response => {
          commit('refresh', response.data.compras_detalles)
          return response
        })
        .catch(error => error)
    },
    getByCompra: ({commit}, payload) => {
      return axios.get(`/compras_detalles/getByCompra/${payload.compra_id}`)
        .then(response => {
          // commit('refresh', response.data.compras_detalles)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/compras_detalles/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/compras_detalles/update', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    delete: ({commit}, payload) => {
      return axios.post('/compras_detalles/delete', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // commit('refresh', response.data.compras_detalles)
          }
          return response
        })
        .catch(error => error)
    },
    deleteByCompra: ({commit}, payload) => {
      return axios.post('/compras_detalles/deleteByCompra', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // commit('refresh', response.data.compras_detalles)
          }
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    compras_detalles: state => state.compras_detalles
  }
}
export default module
