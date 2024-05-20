import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    estados: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.estados = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/estados/getAll')
        .then(response => {
          commit('refresh', response.data.estados)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/estados/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/estados/update', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/estados/delete', qs.stringify(payload))
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
    estados: state => state.estados,
    selectOptions: state => state.estados.reduce((opt, val) => {
      opt.push({label: val.nombre, value: val.id})
      return opt
    }, [])
  }
}

export default module
