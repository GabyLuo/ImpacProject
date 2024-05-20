import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    proveedores: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.proveedores = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/proveedores/getAll')
        .then(response => {
          commit('refresh', response.data.proveedores)
          return response
        })
        .catch(error => error)
    },
    getByEmpresa: ({commit}, payload) => {
      return axios.get(`/proveedores/getByEmpresa/${payload.empresa_id}`)
        .then(response => {
          commit('refresh', response.data.proveedores)
          return response
        })
        .catch(error => error)
    },
    getByCliente: ({commit}, payload) => {
      return axios.get(`/proveedores/getByCliente/${payload.cliente_id}`)
        .then(response => {
          commit('refresh', response.data.proveedores)
          return response
        })
        .catch(error => error)
    },
    getFormato: ({commit}, payload) => {
      return axios.get(`/proveedores/getFormato/${payload.proveedor_id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/proveedores/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    save_requisito: ({dispatch}, payload) => {
      return axios.post('/proveedores/save_requisito', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/proveedores/update', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update_requisito: ({dispatch}, payload) => {
      return axios.put('/proveedores/update_requisito', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/proveedores/delete', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete_requisito: ({dispatch}, payload) => {
      return axios.post('/proveedores/delete_requisito', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    deleteFormato: ({dispatch}, payload) => {
      return axios.post('/proveedores/deleteFormato', qs.stringify(payload))
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
    proveedores: state => state.proveedores
  }
}

export default module
