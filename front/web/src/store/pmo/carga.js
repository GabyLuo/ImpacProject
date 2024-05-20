import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    cargas: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.cargas = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/carga_csv/getAll')
        .then(response => {
          commit('refresh', response.data.cargas)
          return response
        })
        .catch(error => error)
    },
    getById: ({commit}, payload) => {
      return axios.get(`/carga_csv/getById/${payload.id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    getByProyecto: ({commit}, payload) => {
      return axios.get(`/carga_csv/getByProyecto/${payload.proyecto_id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/carga_csv/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/carga_csv/update_actividad', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update_avance: ({dispatch}, payload) => {
      return axios.put('/carga_csv/update_avance', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    update_costo: ({dispatch}, payload) => {
      return axios.put('/carga_csv/update_costo', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    updateFinalizado: ({dispatch}, payload) => {
      return axios.put('/carga_csv/updateFinalizado', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    updateVisible: ({dispatch}, payload) => {
      return axios.put('/carga_csv/updateVisible', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/carga_csv/delete', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete_single: ({dispatch}, payload) => {
      return axios.post('/carga_csv/delete_single', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    deleteFormatoActividad: ({dispatch}, payload) => {
      return axios.post('/carga_csv/deleteFormatoActividad', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    cargas: state => state.cargas
  }
}

export default module
