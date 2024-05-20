import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    tiposgastos: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.tiposgastos = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/tipos/getAll')
        .then(response => {
          commit('refresh', response.data.tiposgastos)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/tipos/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/tipos/update', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/tipos/delete', qs.stringify(payload))
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
    tiposgastos: state => state.tiposgastos,
    selectOptions: state => state.tiposgastos.reduce((opt, val) => {
      opt.push({label: val.nombre, value: val.id})
      return opt
    }, [])
  }
}

export default module
