import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    requisitos: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.requisitos = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/licitaciones_requisitos/getAll')
        .then(response => {
          commit('refresh', response.data.requisitos)
          return response
        })
        .catch(error => error)
    },
    getRequisitosByLicitacion: ({commit}, payload) => {
      return axios.get(`/licitaciones_requisitos/getRequisitosByLicitacion/${payload.licitacion_id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/licitaciones_requisitos/delete', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/licitaciones_requisitos/save', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/licitaciones_requisitos/update', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    requisitos: state => state.requisitos
  }
}

export default module
