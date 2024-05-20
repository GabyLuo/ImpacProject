import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    movimientos: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.movimientos = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/movimientos/getAll')
        .then(response => {
          commit('refresh', response.data.movimientos)
          return response
        })
        .catch(error => error)
    },
    getFolioConsecutivo: ({commit}, payload) => {
      return axios.get(`/movimientos/getFolioConsecutivo/${payload.tipo_movimiento}`)
        .then(response => {
          // commit('refresh', response.data.movimientos_detalles)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/movimientos/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/movimientos/update', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    delete: ({commit}, payload) => {
      return axios.post('/movimientos/delete', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            commit('refresh', response.data.movimientos)
          }
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    movimientos: state => state.movimientos
  }
}
export default module
