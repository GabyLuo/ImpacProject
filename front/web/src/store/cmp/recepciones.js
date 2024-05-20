import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    recepciones: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.recepciones = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/recepciones/getAll')
        .then(response => {
          commit('refresh', response.data.recepciones)
          return response
        })
        .catch(error => error)
    },
    getByCompra: ({commit}, payload) => {
      return axios.get(`/recepciones/getByCompra/${payload.compra_id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    getFolioConsecutivo: ({commit}, payload) => {
      return axios.get('/recepciones/getFolioConsecutivo')
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/recepciones/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/recepciones/update', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/recepciones/delete', qs.stringify(payload))
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
    recepciones: state => state.recepciones
  }
}

export default module
