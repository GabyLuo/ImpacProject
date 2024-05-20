import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    solicitantes: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.solicitantes = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/solicitantes/getAll')
        .then(response => {
          commit('refresh', response.data.solicitantes)
          return response
        })
        .catch(error => error)
    },
    getByProyecto: ({commit}, payload) => {
      return axios.get(`/solicitantes/getByProyecto/${payload.proyecto_id}`)
        .then(response => {
          commit('refresh', response.data.solicitantes)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/solicitantes/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/solicitantes/update', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    delete: ({commit}, payload) => {
      return axios.post('/solicitantes/delete', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            commit('refresh', response.data.solicitantes)
          }
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    solicitantes: state => state.solicitantes
  }
}
export default module
