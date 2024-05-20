import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    tipos_presentaciones: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.tipos_presentaciones = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/tipos_presentaciones/getAll')
        .then(response => {
          commit('refresh', response.data.tipos_presentaciones)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/tipos_presentaciones/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/tipos_presentaciones/update', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    delete: ({commit}, payload) => {
      return axios.post('/tipos_presentaciones/delete', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            commit('refresh', response.data.tipos_presentaciones)
          }
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    tipos_presentaciones: state => state.tipos_presentaciones,
    selectOptions: state => state.tipos_presentaciones.reduce((opt, val) => {
      opt.push({label: val.nombre, value: val.id})
      return opt
    }, [])
  }
}
export default module
