import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    logs: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.logs = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/logs/getAll')
        .then(response => {
          commit('refresh', response.data.logs)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/logs/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/logs/update', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/logs/delete', qs.stringify(payload))
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
    logs: state => state.logs,
    selectOptions: state => state.logs.reduce((opt, val) => {
      opt.push({label: val.name, value: val.id})
      return opt
    }, [])
  }
}

export default module
