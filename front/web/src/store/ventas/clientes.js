import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    clientes: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.clientes = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('ventas/clientes/getAll')
        .then(response => {
          commit('refresh', response.data.clientes)
          return response
        })
        .catch(error => error)
    },
    refresh_name: ({commit}) => {
      return axios.get('ventas/clientes/getAll_byName')
        .then(response => {
          commit('refresh', response.data.clientes)
          return response
        })
        .catch(error => error)
    },
    get: (context, payload) => {
      return axios.post('ventas/clientes/get', qs.stringify(payload))
        .then(response => response)
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('ventas/clientes/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('ventas/clientes/update', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('ventas/clientes/delete', qs.stringify(payload))
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
    clientes: state => state.clientes,
    selectOptions: state => state.clientes.reduce((opt, val) => {
      opt.push({label: val.razon_social, value: val.id})
      return opt
    }, [])
  }
}

export default module
