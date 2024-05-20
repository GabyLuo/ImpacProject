import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    empleos: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.empleos = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/empleos/getAll')
        .then(response => {
          commit('refresh', response.data.empleos)
          return response
        })
        .catch(error => error)
    },
    getByPerfil: ({commit}, payload) => {
      return axios.get(`/empleos/getByPerfil/${payload.perfil_id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/empleos/save', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/empleos/update', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/empleos/delete', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    empleos: state => state.empleos,
    selectOptions: state => state.empleos.reduce((opt, val) => {
      opt.push({label: val.puesto, value: val.id})
      return opt
    }, [])
  }
}

export default module
