import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    compras: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.compras = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/compras/getAll')
        .then(response => {
          commit('refresh', response.data.compras)
          return response
        })
        .catch(error => error)
    },
    getFolioConsecutivo: ({commit}, payload) => {
      return axios.get('/compras/getFolioConsecutivo')
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/compras/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/compras/update', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/compras/delete', qs.stringify(payload))
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
    compras: state => state.compras,
    selectOptions: state => state.compras.reduce((opt, val) => {
      opt.push({label: (val.apellido_paterno + ' ' + val.apellido_materno + ' ' + val.nombre + ' - ' + val.rfc_label), value: val.id})
      return opt
    }, [])
  }
}

export default module
