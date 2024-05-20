import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    programas: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.programas = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/programas/getAll')
        .then(response => {
          commit('refresh', response.data.programas)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/programas/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/programas/update', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/programas/delete', qs.stringify(payload))
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
    programas: state => state.programas,
    selectOptions: state => state.programas.reduce((opt, val) => {
      opt.push({label: val.nombre, value: val.id})
      return opt
    }, [])
  }
}

export default module
