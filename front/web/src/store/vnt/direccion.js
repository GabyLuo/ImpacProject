import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    direcciones: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.direcciones = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/direcciones_cliente/getAll')
        .then(response => {
          commit('refresh', response.data.direcciones)
          return response
        })
        .catch(error => error)
    },
    getByCliente: ({commit}, payload) => {
      return axios.get(`/direcciones_cliente/getByCliente/${payload.cliente_id}`)
        .then(response => {
          commit('refresh', response.data.direcciones)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/direcciones_cliente/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/direcciones_cliente/update', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/direcciones_cliente/delete', qs.stringify(payload))
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
    direcciones: state => state.direcciones
  }
}

export default module
