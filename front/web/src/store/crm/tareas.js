import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    tareas: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.tareas = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/tareas/getAll')
        .then(response => {
          commit('refresh', response.data.tareas)
          return response
        })
        .catch(error => error)
    },
    getBy: ({commit}, payload) => {
      return axios.get(`/tareas/getBy/${payload.id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    getByEjecutivo: ({commit}) => {
      return axios.get('/tareas/getByEjecutivo')
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    getByOportunidad: ({commit}, payload) => {
      return axios.get(`/tareas/getByOportunidad/${payload.oportunidad_id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/tareas/save', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/tareas/update', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    updateComplete: ({dispatch}, payload) => {
      return axios.put('/tareas/updateComplete', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/tareas/delete', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    tareas: state => state.tareas,
    selectOptions: state => state.tareas.reduce((opt, val) => {
      opt.push({label: val.nombre, value: val.id})
      return opt
    }, [])
  }
}

export default module
