import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    notas: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.notas = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/notas/getAll')
        .then(response => {
          commit('refresh', response.data.notas)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/notas/save', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    getNotasBySolicitud: ({commit}, payload) => {
      return axios.get(`/notas/getNotasBySolicitud/${payload.solicitud_id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    notas: state => state.notas
  }
}

export default module
