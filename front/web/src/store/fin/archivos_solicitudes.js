import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    archivos_solicitudes: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.archivos_solicitudes = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/archivos_solicitudes/getAll')
        .then(response => {
          commit('refresh', response.data.archivos_solicitudes)
          return response
        })
        .catch(error => error)
    },
    getArchivosBySolicitud: ({commit}, payload) => {
      return axios.get(`/archivos_solicitudes/getArchivosBySolicitud/${payload.solicitud_id}`)
        .then(response => {
          commit('refresh', response.data.archivos_solicitudes)
          return response
        })
        .catch(error => error)
    },
    deleteFormato: ({dispatch}, payload) => {
      return axios.post('/archivos_solicitudes/deleteFormato', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    guardarArchivo: ({dispatch}, payload) => {
      return axios.post('/archivos_solicitudes/guardarArchivo', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    } /*,
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
    } */
  },
  getters: {
    archivos_solicitud: state => state.archivos_solicitudes
  }
}

export default module
