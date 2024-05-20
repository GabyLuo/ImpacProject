import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    conceptos: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.conceptos = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/conceptos/getAll')
        .then(response => {
          commit('refresh', response.data.conceptos)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/conceptos/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/conceptos/update', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/conceptos/delete', qs.stringify(payload))
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
    conceptos: state => state.conceptos,
    selectOptions: state => state.conceptos.reduce((opt, val) => {
      opt.push({label: val.nombre, value: val.id})
      return opt
    }, [])
  }
}

export default module
