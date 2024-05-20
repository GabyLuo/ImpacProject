import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    comentarios: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.comentarios = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/comentarios/getAll')
        .then(response => {
          commit('refresh', response.data.comentarios)
          return response
        })
        .catch(error => error)
    },
    getByTarea: ({commit}, payload) => {
      return axios.get(`/comentarios/getByTarea/${payload.tarea_id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    getByOportunidad: ({commit}, payload) => {
      return axios.get(`/comentarios/getByOportunidad/${payload.oportunidad_id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/comentarios/save', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/comentarios/update', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/comentarios/delete', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    deleteFormato: ({commit}, payload) => {
      return axios.post('/comentarios/deleteFormato', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    comentarios: state => state.comentarios,
    selectOptions: state => state.comentarios.reduce((opt, val) => {
      opt.push({label: val.nombre, value: val.id})
      return opt
    }, [])
  }
}

export default module
