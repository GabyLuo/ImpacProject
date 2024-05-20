import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    aliados: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.aliados = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/aliados/getAll')
        .then(response => {
          commit('refresh', response.data.aliados)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/aliados/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/aliados/update', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/aliados/delete', qs.stringify(payload))
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
    aliados: state => state.aliados,
    selectOptions: state => state.aliados.reduce((opt, val) => {
      opt.push({label: (val.nombre + ' - ' + val.rfc_label), value: val.id})
      return opt
    }, [])
  }
}

export default module
