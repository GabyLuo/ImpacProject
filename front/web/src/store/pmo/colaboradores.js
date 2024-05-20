import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    colaboradores: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.colaboradores = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/colaboradores/getAll')
        .then(response => {
          commit('refresh', response.data.colaboradores)
          return response
        })
        .catch(error => error)
    },
    getByProyecto: ({commit}, payload) => {
      return axios.get(`/colaboradores/getByProyecto/${payload.proyecto_id}`)
        .then(response => {
          commit('refresh', response.data.colaboradores)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/colaboradores/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/colaboradores/update', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    delete: ({commit}, payload) => {
      return axios.post('/colaboradores/delete', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            commit('refresh', response.data.colaboradores)
          }
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    colaboradores: state => state.colaboradores
  }
}
export default module
