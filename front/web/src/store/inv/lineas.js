import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    lineas: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.lineas = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/lineas/getAll')
        .then(response => {
          commit('refresh', response.data.lineas)
          return response
        })
        .catch(error => error)
    },
    getByCategoria: ({commit}, payload) => {
      return axios.get(`/lineas/getByCategoria/${payload.categoria_id}`)
        .then(response => {
          commit('refresh', response.data.lineas)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/lineas/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/lineas/update', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    delete: ({commit}, payload) => {
      return axios.post('/lineas/delete', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            commit('refresh', response.data.lineas)
          }
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    lineas: state => state.lineas
  }
}
export default module
