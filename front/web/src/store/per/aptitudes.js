import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    aptitudes: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.aptitudes = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/aptitudes/getAll')
        .then(response => {
          commit('refresh', response.data.aptitudes)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/aptitudes/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/aptitudes/update', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    delete: ({commit}, payload) => {
      return axios.post('/aptitudes/delete', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            commit('refresh', response.data.aptitudes)
          }
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    aptitudes: state => state.aptitudes
  }
}
export default module
