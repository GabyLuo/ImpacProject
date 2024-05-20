import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    bases: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.bases = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/requerimientos/getAll')
        .then(response => {
          commit('refresh', response.data.bases)
          return response
        })
        .catch(error => error)
    },
    getBasesByLicitacion: ({commit}, payload) => {
      return axios.get(`/requerimientos/getBasesByLicitacion/${payload.licitacion_id}`)
        .then(response => {
          commit('refresh', response.data.bases)
          return response
        })
        .catch(error => error)
    },
    deleteFormato: ({dispatch}, payload) => {
      return axios.post('/requerimientos/deleteFormato', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    guardarArchivo: ({dispatch}, payload) => {
      return axios.post('/requerimientos/guardarArchivo', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/requerimientos/update', qs.stringify(payload))
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
    bases: state => state.bases
  }
}

export default module
