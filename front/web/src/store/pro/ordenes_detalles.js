import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    ordenes: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.ordenes = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/ordenes_detalles/getAll')
        .then(response => {
          commit('refresh', response.data.ordenes)
          return response
        })
        .catch(error => error)
    },
    getByOrden: ({commit}, payload) => {
      return axios.get(`/ordenes_detalles/getByOrden/${payload.orden_id}/${payload.almacen_origen}`)
        .then(response => {
          // commit('refresh', response.data.ordenes)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/ordenes_detalles/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/ordenes_detalles/update', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/ordenes_detalles/delete', qs.stringify(payload))
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
    ordenes: state => state.ordenes,
    selectOptions: state => state.ordenes.reduce((opt, val) => {
      opt.push({label: (val.folio + ' - ' + val.titulo), value: val.id})
      return opt
    }, [])
  }
}

export default module
