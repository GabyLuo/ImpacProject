import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    crm: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.crm = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/crm/getAll')
        .then(response => {
          commit('refresh', response.data.crm)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/crm/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/crm/update', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/crm/delete', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    crm: state => state.crm,
    selectOptions: state => state.crm.reduce((opt, val) => {
      opt.push({label: val.nombre, value: val.id})
      return opt
    }, [])
  }
}

export default module
