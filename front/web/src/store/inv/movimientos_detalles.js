import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    movimientos_detalles: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.movimientos_detalles = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/movimientos_detalles/getAll')
        .then(response => {
          commit('refresh', response.data.movimientos_detalles)
          return response
        })
        .catch(error => error)
    },
    getByMovimiento: ({commit}, payload) => {
      return axios.get(`/movimientos_detalles/getByMovimiento/${payload.movimiento_id}`)
        .then(response => {
          // commit('refresh', response.data.movimientos_detalles)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/movimientos_detalles/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/movimientos_detalles/update', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    delete: ({commit}, payload) => {
      return axios.post('/movimientos_detalles/delete', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // commit('refresh', response.data.movimientos_detalles)
          }
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    movimientos_detalles: state => state.movimientos_detalles
  }
}
export default module
