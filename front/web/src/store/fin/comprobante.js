import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    comprobantes: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.comprobantes = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/comprobantes_gastos/getAll')
        .then(response => {
          commit('refresh', response.data.comprobantes)
          return response
        })
        .catch(error => error)
    },
    deleteFormato: ({commit}, payload) => {
      return axios.post('/comprobantes_gastos/deleteFormato', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    comprobantes: state => state.comprobantes
  }
}

export default module
