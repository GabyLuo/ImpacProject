import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    cotizaciones: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.cotizaciones = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/cotizaciones_gastos/getAll')
        .then(response => {
          commit('refresh', response.data.cotizaciones)
          return response
        })
        .catch(error => error)
    },
    deleteFormato: ({commit}, payload) => {
      return axios.post('/cotizaciones_gastos/deleteFormato', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    cotizaciones: state => state.cotizaciones
  }
}

export default module
