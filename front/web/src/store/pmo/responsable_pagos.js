import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    responsable_pagos: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.responsable_pagos = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/responsable_pagos/getAll')
        .then(response => {
          commit('refresh', response.data.responsable_pagos)
          return response
        })
        .catch(error => error)
    },
    getByProyecto: ({commit}, payload) => {
      return axios.get(`/responsable_pagos/getByProyecto/${payload.proyecto_id}`)
        .then(response => {
          commit('refresh', response.data.responsable_pagos)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/responsable_pagos/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/responsable_pagos/update', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    delete: ({commit}, payload) => {
      return axios.post('/responsable_pagos/delete', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            commit('refresh', response.data.responsable_pagos)
          }
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    responsable_pagos: state => state.responsable_pagos
  }
}
export default module
