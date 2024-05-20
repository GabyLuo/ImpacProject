import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    titulos: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.titulos = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/titulos/getAll')
        .then(response => {
          commit('refresh', response.data.titulos)
          return response
        })
        .catch(error => error)
    },
    getByPerfil: ({commit}, payload) => {
      return axios.get(`/titulos/getByPerfil/${payload.perfil_id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    deleteFormato: ({dispatch}, payload) => {
      return axios.post('/titulos/deleteFormato', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    titulos: state => state.titulos
  }
}

export default module
