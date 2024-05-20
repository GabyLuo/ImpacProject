import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    requisitos: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.requisitos = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/requisitos/getAll')
        .then(response => {
          commit('refresh', response.data.requisitos)
          return response
        })
        .catch(error => error)
    },
    getByTramite: ({commit}, payload) => {
      return axios.get(`/requisitos/getByTramite/${payload.cliente_id}/${payload.tramite}`)
        .then(response => {
          commit('refresh', response.data.requisitos)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/requisitos/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/requisitos/update', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/requisitos/delete', qs.stringify(payload))
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
    requisitos: state => state.requisitos
  }
}

export default module
