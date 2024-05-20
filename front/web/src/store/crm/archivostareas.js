import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    archivostareas: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.archivostareas = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/archivostareas/getAll')
        .then(response => {
          commit('refresh', response.data.archivostareas)
          return response
        })
        .catch(error => error)
    },
    getByTarea: ({commit}, payload) => {
      return axios.get(`/archivostareas/getByTarea/${payload.id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    deleteFormato: ({commit}, payload) => {
      return axios.post('/archivostareas/deleteFormato', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    archivostareas: state => state.archivostareas
  }
}

export default module
