import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    fianzas: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.fianzas = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/fianzas/getAll')
        .then(response => {
          commit('refresh', response.data.fianzas)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/fianzas/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/fianzas/update', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    deleteFileFinal: ({dispatch}, payload) => {
      return axios.put('/fianzas/deleteFileFinal', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/fianzas/delete', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    deleteFormato: ({dispatch}, payload) => {
      return axios.post('/fianzas/deleteFormato', qs.stringify(payload))
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
    fianzas: state => state.fianzas,
    selectOptions: state => state.fianzas.reduce((opt, val) => {
      opt.push({label: val.fianza, value: val.id})
      return opt
    }, [])
  }
}

export default module
