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
      return axios.get(`/facturasContratos/getFacturas/${payload.factura_id}`)
        .then(response => {
          commit('refresh', response.data.facturas)
          return response
        })
        .catch(error => error)
    },
    getFacturasByContrato: ({commit}, payload) => {
      return axios.get(`/facturasContratos/getFacturasByContrato/${payload.contrato}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    getFacturasByContratoandId: ({commit}, payload) => {
      return axios.get(`/facturasContratos/getFacturasByContratoandId/${payload.proyecto}/${payload.cliente}/${payload.contrato}/${payload.factura}/${payload.year}/${payload.status}/${payload.empresa_id}`)
        .then(response => {
          commit('refresh', response.data.facturas)
          return response
        })
        .catch(error => error)
    },
    cancelarFactura: ({dispatch}, payload) => {
      return axios.put('/facturasContratos/cancelarFactura', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
          }
          return response
        })
        .catch(error => error)
    },
    actualizarFactura: ({dispatch}, payload) => {
      return axios.put('/facturasContratos/actualizarFactura', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/facturasContratos/delete', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
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
