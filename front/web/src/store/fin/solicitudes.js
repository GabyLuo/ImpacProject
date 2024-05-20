import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    solicitudes: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.solicitudes = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/solicitudes/getAll')
        .then(response => {
          commit('refresh', response.data.solicitudes)
          return response
        })
        .catch(error => error)
    },
    getByProyecto: ({commit}, payload) => {
      return axios.get(`/solicitudes/getByProyecto/${payload.proyecto_id}`)
        .then(response => {
          commit('refresh', response.data.solicitudes)
          return response
        })
        .catch(error => error)
    },
    getById: ({commit}, payload) => {
      return axios.get(`/solicitudes/getById/${payload.id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    obtenerSigSolicitud: ({commit}, payload) => {
      return axios.get('/solicitudes/obtenerSigSolicitud')
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    filtrar: ({commit}, payload) => {
      return axios.post('/solicitudes/filtrar', qs.stringify(payload))
        .then(response => {
          commit('refresh', response.data.solicitudes)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/solicitudes/save', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    rechazar: ({dispatch}, payload) => {
      return axios.post('/solicitudes/rechazar', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    cancelar: ({dispatch}, payload) => {
      return axios.post('/solicitudes/cancelar', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/solicitudes/update', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    update_fechas: ({dispatch}, payload) => {
      return axios.put('/solicitudes/update_fechas', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    updateEmpresa: ({dispatch}, payload) => {
      return axios.put('/solicitudes/updateEmpresa', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    updateStatus: ({dispatch}, payload) => {
      return axios.put('/solicitudes/updateStatus', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    updateClasificacion: ({dispatch}, payload) => {
      return axios.put('/solicitudes/updateClasificacion', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    delete: ({commit}, payload) => {
      return axios.put('/solicitudes/delete', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // commit('refresh', response.data.solicitudes)
          }
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    solicitudes: state => state.solicitudes
  }
}
export default module
