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
      return axios.get('/direcciones/getAll')
        .then(response => {
          commit('refresh', response.data.direcciones)
          return response
        })
        .catch(error => error)
    },
    getByEmpresa: ({commit}, payload) => {
      return axios.get(`/direcciones/getByEmpresa/${payload.empresa_id}`)
        .then(response => {
          commit('refresh', response.data.direcciones)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/direcciones/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/direcciones/update', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/direcciones/delete', qs.stringify(payload))
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
