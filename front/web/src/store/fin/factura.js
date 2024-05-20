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
    refresh: ({commit}) => {
      return axios.get('/facturas_gastos/getAll')
        .then(response => {
          commit('refresh', response.data.facturas)
          return response
        })
        .catch(error => error)
    },
    deleteFormato: ({commit}, payload) => {
      return axios.post('/facturas_gastos/deleteFormato', qs.stringify(payload))
        .then(response => {
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
