import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    sucursales: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.sucursales = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/pmosucursales/getAll')
        .then(response => {
          commit('refresh', response.data.sucursales)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/pmosucursales/save', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/pmosucursales/update', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/pmosucursales/delete', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    sucursales: state => state.sucursales,
    selectOptions: state => state.sucursales.reduce((opt, val) => {
      opt.push({label: val.nombre, value: val.id})
      return opt
    }, [])
  }
}

export default module
