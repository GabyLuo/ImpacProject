import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    recepciones_detalles: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.recepciones_detalles = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/recepciones_detalles/getAll')
        .then(response => {
          commit('refresh', response.data.recepciones_detalles)
          return response
        })
        .catch(error => error)
    },
    getByRecepcion: ({commit}, payload) => {
      return axios.get(`/recepciones_detalles/getByRecepcion/${payload.recepcion_id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/recepciones_detalles/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/recepciones_detalles/update', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/recepciones_detalles/delete', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    recepciones_detalles: state => state.recepciones_detalles
  }
}

export default module
