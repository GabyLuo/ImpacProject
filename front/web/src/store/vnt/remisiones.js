import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    remisiones: [],
    detalles: [],
    paymentsForms: [],
    usoCFDIs: [],
    pagos: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.remisiones = payload
    },
    refreshDetails: (state, payload) => {
      state.detalles = payload
    },
    refreshPF: (state, payload) => {
      state.paymentsForms = payload
    },
    refreshCFDI: (state, payload) => {
      state.usoCFDIs = payload
    },
    refreshP: (state, payload) => {
      state.pagos = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/remisiones/getAll')
        .then(response => {
          commit('refresh', response.data.remisiones)
          return response
        })
        .catch(error => error)
    },
    get: ({commit}, payload) => {
      return axios.get(`/remisiones/get/${payload.id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    getBy: ({commit}, payload) => {
      return axios.get(`/remisiones/getBy/${payload.empresa_id}/${payload.cliente_id}/${payload.status}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    refreshDetails: ({commit}, payload) => {
      return axios.get(`/remisiones/getDetalles/${payload.remision_id}`)
        .then(response => {
          commit('refreshDetails', response.data.detalles)
          return response
        })
        .catch(error => error)
    },
    getPagos: ({commit}, payload) => {
      return axios.get(`/remisiones/getPagos/${payload.remision_id}`)
        .then(response => {
          commit('refreshP', response.data.pagos)
          return response
        })
        .catch(error => error)
    },
    keepChecking: ({commit}, payload) => {
      return axios.get(`/remisiones/keepChecking/${payload.remision_id}`)
        .then(response => {
          // commit('refreshP', response.data.pagos)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/remisiones/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    saveWithFactura: ({dispatch}, payload) => {
      return axios.post('/remisiones/saveWithFactura', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/remisiones/update', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/remisiones/delete', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    saveProduct: ({dispatch}, payload) => {
      return axios.post('/remisiones/saveProduct', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    updateProduct: ({dispatch}, payload) => {
      return axios.put('/remisiones/updateProduct', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    deleteProduct: ({dispatch}, payload) => {
      return axios.post('/remisiones/deleteProduct', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    updateFiscal: ({dispatch}, payload) => {
      return axios.put('/remisiones/updateFiscal', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    changeStatus: ({dispatch}, payload) => {
      return axios.put('/remisiones/changeStatus', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    timbrar: ({dispatch}, payload) => {
      return axios.put('/remisiones/timbrar', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    timbrarPayment: ({dispatch}, payload) => {
      return axios.put('/remisiones/timbrarPayment', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    cancelInvoice: ({dispatch}, payload) => {
      return axios.put('/remisiones/cancelInvoice', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    cancelPago: ({dispatch}, payload) => {
      return axios.put('/remisiones/cancelPago', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    checkInvoice: ({dispatch}, payload) => {
      return axios.put('/remisiones/checkInvoice', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    checkPagos: ({dispatch}, payload) => {
      return axios.put('/remisiones/checkPagos', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    getFormasDePago: ({commit}, payload) => {
      return axios.get('/payment-forms/get-select-options/')
        .then(response => {
          commit('refreshPF', response.data.paymentsForms)
          return response
        })
        .catch(error => error)
    },
    getUsoCFDIs: ({commit}, payload) => {
      return axios.get('/uso_cfdi/get-select-options/')
        .then(response => {
          commit('refreshCFDI', response.data.usoCFDIs)
          return response
        })
        .catch(error => error)
    },
    getDatosFiscales: ({dispatch}, payload) => {
      return axios.get(`/remisiones/getDatosFiscales/${payload.remision_id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    savePago: ({dispatch}, payload) => {
      return axios.post('/remisiones/savePago', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refreshP')
          }
          return response
        })
        .catch(error => error)
    },
    deletePayment: ({dispatch}, payload) => {
      return axios.post('/remisiones/deletePayment', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    getNextFolio: ({dispatch}, payload) => {
      return axios.get(`/remisiones/getNextFolio/${payload.remision_id}/${payload.serie}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    getFolioConsecutivo: ({commit}, payload) => {
      return axios.get(`/remisiones/getFolioConsecutivo/${payload.tipo}`)
        .then(response => {
          // commit('refresh', response.data.movimientos_detalles)
          return response
        })
        .catch(error => error)
    },
    cancelar: ({dispatch}, payload) => {
      return axios.put('/remisiones/cancelar', qs.stringify(payload))
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
    remisiones: state => state.remisiones,
    detalles: state => state.detalles,
    pagos: state => state.pagos,
    selectPaymentFormsOptions: state => state.paymentsForms.reduce((opt, val) => {
      opt.push({label: val.label, value: val.value})
      return opt
    }, []),
    selectUsoCFDIOptions: state => state.usoCFDIs.reduce((opt, val) => {
      opt.push({label: val.label, value: val.value})
      return opt
    }, [])
  }
}

export default module
