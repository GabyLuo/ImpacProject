import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    eventos: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.eventos = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/eventos/getAll')
        .then(response => {
          commit('refresh', response.data.eventos)
          return response
        })
        .catch(error => error)
    },
    getEventosByLicitacion: ({commit}, payload) => {
      return axios.get(`/eventos/getEventosByLicitacion/${payload.licitacion_id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/eventos/delete', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/eventos/save', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/eventos/update', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    deleteFormato: ({dispatch}, payload) => {
      return axios.post('/eventos/deleteFormato', qs.stringify(payload))
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
    eventos: state => state.eventos
  }
}

export default module
