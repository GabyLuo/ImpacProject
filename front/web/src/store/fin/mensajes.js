import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    mensajes: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.mensajes = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/mensajes/getAll')
        .then(response => {
          commit('refresh', response.data.mensajes)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/mensajes/save', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    getMensajesBySolicitud: ({commit}, payload) => {
      return axios.get(`/mensajes/getMensajesBySolicitud/${payload.solicitud_id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    mensajes: state => state.mensajes
  }
}

export default module
