import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    saldos: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.saldos = payload
    }
  },
  actions: {
    refresh: ({commit}, payload) => {
      return axios.get('/saldos/getAll')
        .then(response => {
          commit('refresh', response.data.saldos)
          return response
        })
        .catch(error => error)
    },
    getByCuenta: ({commit}, payload) => {
      return axios.get(`/saldos/getByCuenta/${payload.cuenta_id}`)
        .then(response => {
          // commit('refresh', response.data.saldos)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/saldos/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/saldos/update', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/saldos/delete', qs.stringify(payload))
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
    cuentas: state => state.cuentas,
    selectOtherOptions: state => state.cuentas.reduce((opt, val) => {
      opt.push({label: val.nombre, value: val.id})
      return opt
    }, [])
  }
}

export default module
