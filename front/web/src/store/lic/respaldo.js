import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    licitaciones_respaldo: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.licitaciones_respaldo = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/licitaciones_respaldo/getAll')
        .then(response => {
          commit('refresh', response.data.licitaciones_respaldo)
          return response
        })
        .catch(error => error)
    },
    getByLicitacion: ({commit}, payload) => {
      return axios.get(`/licitaciones_respaldo/getByLicitacion/${payload.licitacion_id}`)
        .then(response => {
          commit('refresh', response.data.licitaciones_respaldo)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/licitaciones_respaldo/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/licitaciones_respaldo/delete', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    licitaciones_respaldo: state => state.licitaciones_respaldo
  }
}

export default module
