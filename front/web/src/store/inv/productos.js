import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    productos: [],
    claveProductos: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.productos = payload
    },
    refreshCP: (state, payload) => {
      state.claveProductos = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/productos/getAll')
        .then(response => {
          commit('refresh', response.data.productos)
          return response
        })
        .catch(error => error)
    },
    getClaveProductos: ({commit}, payload) => {
      return axios.get(`clave_productos/get-select-options/${payload.search}`)
        .then(response => {
          commit('refreshCP', response.data.claveProductos)
          return response
        })
        .catch(error => error)
    },
    getByCategoria: ({commit}, payload) => {
      return axios.get(`/productos/getByCategoria/${payload.categoria}`)
        .then(response => {
          // commit('refresh', response.data.almacenes)
          return response
        })
        .catch(error => error)
    },
    getProductoByLinea: ({commit}, payload) => {
      return axios.get(`/productos/getProductoByLinea/${payload.linea}`)
        .then(response => {
          // commit('refresh', response.data.almacenes)
          return response
        })
        .catch(error => error)
    },
    getByCompra: ({commit}, payload) => {
      return axios.get(`/productos/getByCompra/${payload.compra_id}`)
        .then(response => {
          // commit('refresh', response.data.almacenes)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/productos/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/productos/update', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    delete: ({commit}, payload) => {
      return axios.post('/productos/delete', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            commit('refresh', response.data.productos)
          }
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    productos: state => state.productos,
    selectOptions: state => state.productos.reduce((opt, val) => {
      opt.push({label: val.nombre, value: val.id})
      return opt
    }, []),
    selectClaveProductosOptions: state => state.claveProductos.reduce((opt, val) => {
      opt.push({label: val.label, value: val.clave})
      return opt
    }, [])
  }
}
export default module
