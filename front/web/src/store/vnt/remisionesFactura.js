import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    facturas: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.facturas = payload
    }
  },
  actions: {
    getFacturas: ({commit}, payload) => {
      return axios.get(`/remisionesFacturas/getFacturas/${payload.remision}`)
        .then(response => {
          commit('refresh', response.data.facturas)
          return response
        })
        .catch(error => error)
    },
    getFacturasByContrato: ({commit}, payload) => {
      return axios.get(`/remisionesFacturas/getFacturasByContrato/${payload.contrato}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    getFacturasByContratoandId: ({commit}, payload) => {
      return axios.get(`/remisionesFacturas/getFacturasByContratoandId/${payload.cliente}/${payload.empresa}/${payload.year}/${payload.status}`)
        .then(response => {
          commit('refresh', response.data.facturas)
          return response
        })
        .catch(error => error)
    },
    getFacturasDuplicadas: ({commit}, payload) => {
      return axios.get(`/remisionesFacturas/getFacturasDuplicadas/${payload.cliente}/${payload.empresa}/${payload.year}/${payload.status}`)
        .then(response => {
          commit('refresh', response.data.facturas)
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/remisionesFacturas/delete', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
          }
          return response
        })
        .catch(error => error)
    },
    agregarNombre: ({dispatch}, payload) => {
      return axios.put('/remisionesFacturas/agregarNombre', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    agregarNombreFacturas: ({dispatch}, payload) => {
      return axios.put('/remisionesFacturas/agregarNombreFacturas', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    remisionesRepetidas: ({dispatch}, payload) => {
      return axios.put('/remisionesFacturas/getRemisionesRepetidas', qs.stringify(payload))
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
    facturas: state => state.facturas
  }
}

export default module
