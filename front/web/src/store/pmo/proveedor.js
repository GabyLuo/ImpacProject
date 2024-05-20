import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    proveedor: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.proveedor = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/proveedor/getAll')
        .then(response => {
          commit('refresh', response.data.proveedor)
          return response
        })
        .catch(error => error)
    },
    getOptions: ({commit}) => {
      return axios.get('/proveedor/getOptions')
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    getById: ({commit}, payload) => {
      return axios.get(`/proveedor/getById/${payload.id}`)
        .then(response => {
          // commit('refresh', response.data.proveedor)
          return response
        })
        .catch(error => error)
    },
    getCuentas: ({commit}, payload) => {
      return axios.get(`/proveedor/getCuentas/${payload.id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/proveedor/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/proveedor/update', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/proveedor/delete', qs.stringify(payload))
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
    proveedor: state => state.proveedor,
    selectOptions: state => state.proveedor.reduce((opt, val) => {
      opt.push({label: val.nombre_comercial + ', ' + val.rfc, value: val.id})
      return opt
    }, [])
  }
}

export default module
