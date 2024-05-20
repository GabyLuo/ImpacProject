import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    propuestas: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.propuestas = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/licitaciones_propuestas/getAll')
        .then(response => {
          commit('refresh', response.data.propuestas)
          return response
        })
        .catch(error => error)
    },
    getPropuestasByLicitacion: ({commit}, payload) => {
      return axios.get(`/licitaciones_propuestas/getPropuestasByLicitacion/${payload.licitacion_id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/licitaciones_propuestas/delete', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/licitaciones_propuestas/save', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/licitaciones_propuestas/update', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    propuestas: state => state.propuestas
  }
}

export default module
