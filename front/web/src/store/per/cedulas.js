import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    cedulas: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.cedulas = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/cedulas/getAll')
        .then(response => {
          commit('refresh', response.data.cedulas)
          return response
        })
        .catch(error => error)
    },
    getByPerfil: ({commit}, payload) => {
      return axios.get(`/cedulas/getByPerfil/${payload.perfil_id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    deleteFormato: ({dispatch}, payload) => {
      return axios.post('/cedulas/deleteFormato', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    cedulas: state => state.cedulas
  }
}

export default module
