import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    cultivos: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.cultivos = payload
    }
  },
  actions: {
    getById: ({commit}, payload) => {
      return axios.get(`/cultivos/getById/${payload.id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/cultivos/save', qs.stringify(payload))
        .then(response => {
          // if (response.data.result === 'success') {
          // dispatch('refresh')
          // }
          return response
        })
        .catch(error => error)
    }
  }
}
export default module
