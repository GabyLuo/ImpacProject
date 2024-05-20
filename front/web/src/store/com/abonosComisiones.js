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
      return axios.get('/abonosComisiones/getAll')
        .then(response => {
          commit('refresh', response.data.abonos)
          return response
        })
        .catch(error => error)
    },
    historialAbonos: ({commit}, payload) => {
      return axios.get(`/abonosComisiones/historialAbonos/${payload.comision_id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    filtrar: ({commit}, payload) => {
      return axios.get(`/abonosComisiones/filtrar/${payload.proyecto_id}/${payload.aliado_id}/${payload.fecha_inicio}/${payload.fecha_fin}/${payload.vendedor_id}`)
        .then(response => {
          // commit('refresh', response.data.abonos)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/abonosComisiones/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/abonosComisiones/update', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/abonosComisiones/delete', qs.stringify(payload))
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
