import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    cuentas: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.cuentas = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/cuentas/getAll')
        .then(response => {
          commit('refresh', response.data.cuentas)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/cuentas/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/cuentas/update', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/cuentas/delete', qs.stringify(payload))
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
    cuentas: state => state.cuentas,
    selectOtherOptions: state => state.cuentas.reduce((opt, val) => {
      opt.push({label: val.nombre, value: val.id})
      return opt
    }, [])
  }
}

export default module
