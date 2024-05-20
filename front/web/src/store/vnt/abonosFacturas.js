import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    abonos: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.abonos = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/abonosFacturas/getAll')
        .then(response => {
          commit('refresh', response.data.abonos)
          return response
        })
        .catch(error => error)
    },
    historialAbonos: ({commit}, payload) => {
      return axios.get(`/abonosFacturas/historialAbonos/${payload.factura_id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    filtrar: ({commit}, payload) => {
      return axios.post('/abonosFacturas/filtrar', qs.stringify(payload))
        .then(response => {
          commit('refresh', response.data.abonos)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/abonosFacturas/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/abonosFacturas/update', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/abonosFacturas/delete', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    abonos: state => state.abonos
  }
}

export default module
