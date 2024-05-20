import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    aptitudes_perfiles: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.aptitudes_perfiles = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/aptitudes_perfiles/getAll')
        .then(response => {
          commit('refresh', response.data.aptitudes_perfiles)
          return response
        })
        .catch(error => error)
    },
    getByPerfil: ({commit}, payload) => {
      return axios.get(`/aptitudes_perfiles/getByPerfil/${payload.perfil_id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/aptitudes_perfiles/save', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    delete: ({commit}, payload) => {
      return axios.post('/aptitudes_perfiles/delete', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    aptitudes_perfiles: state => state.aptitudes_perfiles
  }
}
export default module
